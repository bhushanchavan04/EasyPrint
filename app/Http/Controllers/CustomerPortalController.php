<?php

namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Env;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\DigitalDocument;
use App\Models\Order;
use App\Models\OrderSpecification;
use App\Models\Payment;
use App\Models\PrintExecution;
use App\Models\ResourcePricingRule;
use App\Models\ResourcePrinter;
use App\Models\Shop;
use App\Models\ShopSubscription;
use App\Services\DocumentParser;
use App\Services\UpiService;

class CustomerPortalController
{
    /**
     * Show 4-Step Customer Print Portal
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $shopSlug = $params['shopSlug'];
        $shop = Shop::findBySlug($shopSlug);

        if (!$shop) {
            $response->setStatusCode(404);
            echo "Shop not found.";
            return;
        }

        // Active Subscription check
        $activeSub = ShopSubscription::getActiveSubscription($shop['id']);
        if (!$activeSub || !$shop['is_active']) {
            View::render('customer.paused', ['shop' => $shop], null);
            return;
        }

        $rates = ResourcePricingRule::getShopMatrix($shop['id']);
        $defaultPrinter = ResourcePrinter::getDefaultPrinter($shop['id']);

        View::render('customer.portal', [
            'shop' => $shop,
            'rates' => $rates,
            'defaultPrinter' => $defaultPrinter
        ], 'layouts.customer');
    }

    /**
     * Step 1: Handle Document Upload & Auto Page Parsing
     */
    public function upload(Request $request, Response $response, array $params): void
    {
        $shopSlug = $params['shopSlug'];
        $shop = Shop::findBySlug($shopSlug);

        if (!$shop) {
            $response->json(['error' => 'Shop not found'], 404);
        }

        if (!$request->hasFile('document')) {
            $response->json(['error' => 'No file uploaded or file exceeds upload limit.'], 400);
        }

        $file = $request->file('document');
        $originalName = basename($file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowed = ['pdf', 'png', 'jpg', 'jpeg', 'docx'];
        if (!in_array($extension, $allowed)) {
            $response->json(['error' => 'Invalid file format. Only PDF, DOCX, PNG, JPG allowed.'], 400);
        }

        // Ensure upload directory exists
        $uploadDir = __DIR__ . '/../../../public/uploads/documents/' . date('Y/m/d');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $response->json(['error' => 'Failed to store uploaded file.'], 500);
        }

        // Detect MIME and parse page count
        $mimeType = $file['type'] ?: mime_content_type($targetPath);
        $pageCount = DocumentParser::getPageCount($targetPath, $mimeType);
        $fileSize = filesize($targetPath);

        $docId = DigitalDocument::create([
            'shop_id' => $shop['id'],
            'original_name' => $originalName,
            'storage_path' => $targetPath,
            'mime_type' => $mimeType,
            'size_bytes' => $fileSize,
            'total_pages' => $pageCount
        ]);

        $publicUrl = '/uploads/documents/' . date('Y/m/d') . '/' . $uniqueName;

        $response->json([
            'success' => true,
            'document_id' => $docId,
            'file_name' => $originalName,
            'file_size' => $fileSize,
            'file_size_formatted' => round($fileSize / 1024, 1) . ' KB',
            'mime_type' => $mimeType,
            'total_pages' => $pageCount,
            'preview_url' => $publicUrl,
            'is_image' => str_starts_with($mimeType, 'image/'),
            'is_pdf' => $mimeType === 'application/pdf' || $extension === 'pdf',
        ]);
    }

    /**
     * Generate dynamic sliced PDF preview for selected pages
     */
    public function previewSlice(Request $request, Response $response, array $params): void
    {
        $shopSlug = $params['shopSlug'];
        $shop = Shop::findBySlug($shopSlug);
        if (!$shop) {
            $response->json(['error' => 'Shop not found'], 404);
            return;
        }

        $documentId = $request->get('document_id');
        $pageRange = trim((string)$request->get('page_range', 'all'));

        $doc = DigitalDocument::findById($documentId);
        if (!$doc) {
            $response->json(['error' => 'Document not found'], 404);
            return;
        }

        $totalPages = max(1, (int)$doc['total_pages']);
        $selectedPages = DocumentParser::parsePageRange($pageRange, $totalPages);

        if ($pageRange === '' || strtolower($pageRange) === 'all' || count($selectedPages) === $totalPages) {
            $response->json([
                'success' => true,
                'is_sliced' => false,
                'page_count' => $totalPages,
                'selected_pages' => $selectedPages
            ]);
            return;
        }

        $slicedPath = DocumentParser::slicePdf($doc['storage_path'], $pageRange, $totalPages);
        if (file_exists($slicedPath) && filesize($slicedPath) > 0) {
            $filename = basename($slicedPath);
            $previewUrl = '/uploads/documents/sliced/' . $filename;
            $response->json([
                'success' => true,
                'preview_url' => $previewUrl,
                'is_sliced' => true,
                'page_count' => count($selectedPages),
                'selected_pages' => $selectedPages
            ]);
            return;
        }

        $response->json([
            'success' => false,
            'error' => 'Could not slice PDF'
        ], 500);
    }

    /**
     * Step 2 & 3: Create Order & Generate Direct 0% Commission UPI Payment Link
     */
    public function createOrder(Request $request, Response $response, array $params): void
    {
        $shopSlug = $params['shopSlug'];
        $shop = Shop::findBySlug($shopSlug);

        if (!$shop) {
            $response->json(['error' => 'Shop not found'], 404);
        }

        $documentId = $request->get('document_id');
        $copies = max(1, (int)$request->get('copies', 1));
        $colorMode = $request->get('color_mode', 'BLACK_AND_WHITE');
        $sides = $request->get('sides', 'SINGLE');
        $paperSize = $request->get('paper_size', 'A4');
        $pageRange = trim((string)$request->get('page_range', 'all'));
        $customerIdentifier = trim($request->get('customer_identifier', ''));

        $doc = DigitalDocument::findById($documentId);
        if (!$doc) {
            $response->json(['error' => 'Document not found or expired.'], 404);
        }

        $totalDocPages = max(1, (int)$doc['total_pages']);
        // Parse page range to know exact number of pages to be printed
        $selectedPages = DocumentParser::parsePageRange($pageRange, $totalDocPages);
        $selectedPagesCount = max(1, count($selectedPages));

        // Normalize range string for consistency
        if ($selectedPagesCount === $totalDocPages && empty($pageRange)) {
            $pageRange = 'all';
        }

        // PHYSICAL PDF SLICING:
        // If a specific page range is requested (e.g. page 3, or pages 2, 4, 6),
        // physically slice the source document into a dedicated PDF containing ONLY the selected pages.
        // This guarantees that the printer CANNOT physically print any unselected pages.
        $targetDocId = $doc['id'];
        if ($pageRange !== '' && strtolower($pageRange) !== 'all' && $selectedPagesCount < $totalDocPages) {
            $slicedPath = DocumentParser::slicePdf($doc['storage_path'], $pageRange, $totalDocPages);
            if (file_exists($slicedPath) && filesize($slicedPath) > 0 && $slicedPath !== $doc['storage_path']) {
                $cleanRange = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $pageRange);
                $slicedDocName = 'p_' . $cleanRange . '_' . $doc['original_name'];
                $slicedDocId = DigitalDocument::create([
                    'shop_id' => $shop['id'],
                    'original_name' => $slicedDocName,
                    'storage_path' => $slicedPath,
                    'mime_type' => 'application/pdf',
                    'size_bytes' => filesize($slicedPath),
                    'total_pages' => $selectedPagesCount
                ]);
                if ($slicedDocId) {
                    $targetDocId = $slicedDocId;
                }
            }
        }

        // Rate Calculation based on selected pages
        $ratePerPage = ResourcePricingRule::getRate($shop['id'], $paperSize, $colorMode, $sides);

        // If double sided, physical sheets = ceil(selected pages / 2)
        $sheetsOrPages = ($sides === 'DOUBLE') ? (int)ceil($selectedPagesCount / 2) : $selectedPagesCount;
        $orderTotalAmount = round($sheetsOrPages * $ratePerPage * $copies, 2);
        if ($orderTotalAmount < 1.00) {
            $orderTotalAmount = 1.00; // Minimum ₹1.00 for UPI
        }

        $totalPrintPages = $selectedPagesCount * $copies;
        $orderNumber = Order::generateOrderNumber();

        Database::beginTransaction();
        try {
            $orderId = Order::create([
                'shop_id' => $shop['id'],
                'order_number' => $orderNumber,
                'customer_identifier' => $customerIdentifier,
                'total_pages' => $totalPrintPages,
                'total_amount' => $orderTotalAmount,
                'status' => 'AWAITING_PAYMENT'
            ]);

            OrderSpecification::create([
                'order_id' => $orderId,
                'document_id' => $targetDocId,
                'copies' => $copies,
                'color_mode' => $colorMode,
                'sides' => $sides,
                'paper_size' => $paperSize,
                'page_range' => $pageRange,
                'calculated_amount' => $orderTotalAmount
            ]);

            Database::commit();

            // Direct UPI details
            $upiUri = UpiService::generateUpiUri($shop['upi_id'], $shop['upi_name'], $orderTotalAmount, $orderNumber);
            $appLinks = UpiService::getAppDeepLinks($upiUri);
            $qrUrl = UpiService::getQrCodeUrl($upiUri, 320);

            $response->json([
                'success' => true,
                'order_number' => $orderNumber,
                'order_id' => $orderId,
                'total_amount' => $orderTotalAmount,
                'total_pages' => $totalPrintPages,
                'upi_id' => $shop['upi_id'],
                'upi_name' => $shop['upi_name'],
                'upi_uri' => $upiUri,
                'qr_url' => $qrUrl,
                'app_links' => $appLinks,
                'accept_cash' => (bool)$shop['accept_cash']
            ]);
        } catch (\Throwable $e) {
            Database::rollBack();
            $response->json(['error' => 'Failed to initialize order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Submit Payment (Customer verifies UTR / Cash at Counter) -> Moves to PRINTING
     */
    public function submitPayment(Request $request, Response $response, array $params): void
    {
        $orderNumber = $params['orderNumber'];
        $order = Order::findByOrderNumber($orderNumber);

        if (!$order) {
            $response->json(['error' => 'Order not found'], 404);
        }

        $method = $request->get('payment_method', 'DIRECT_UPI');
        $transactionRef = trim($request->get('transaction_reference', ''));

        Database::beginTransaction();
        try {
            // Record payment
            Payment::create([
                'order_id' => $order['id'],
                'payment_method' => $method,
                'amount' => $order['total_amount'],
                'transaction_reference' => $transactionRef,
                'is_verified' => ($method === 'DIRECT_UPI' && !empty($transactionRef)) ? 1 : 0
            ]);

            // Cash at Counter: Keep order in AWAITING_PAYMENT until shopkeeper accepts cash at counter.
            // Do NOT queue print execution automatically.
            if ($method === 'CASH_AT_COUNTER') {
                Order::updateStatus($order['id'], 'AWAITING_PAYMENT');
                Database::commit();

                $response->json([
                    'success' => true,
                    'order_number' => $orderNumber,
                    'status' => 'AWAITING_PAYMENT',
                    'payment_method' => 'CASH_AT_COUNTER',
                    'message' => 'Cash payment selected. Please hand cash to the shopkeeper at the counter to start printing.'
                ]);
                return;
            }

            // Direct UPI: Update Order Status to PRINTING and queue execution
            Order::updateStatus($order['id'], 'PRINTING');

            // Queue print execution for connected machine agent
            $printer = ResourcePrinter::getDefaultPrinter($order['shop_id']);
            $deviceAgentId = $printer ? $printer['device_agent_id'] : null;
            $printerId = $printer ? $printer['id'] : null;

            PrintExecution::create($order['id'], $deviceAgentId, $printerId);

            Database::commit();

            $response->json([
                'success' => true,
                'order_number' => $orderNumber,
                'status' => 'PRINTING',
                'payment_method' => 'DIRECT_UPI',
                'message' => 'Payment submitted! Printing initialized.'
            ]);
        } catch (\Throwable $e) {
            Database::rollBack();
            $response->json(['error' => 'Payment submission failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Step 4: Live Order Status Polling (called every 2.5s)
     */
    public function getOrderStatus(Request $request, Response $response, array $params): void
    {
        $orderNumber = $params['orderNumber'];
        $order = Order::findByOrderNumber($orderNumber);

        if (!$order) {
            $response->json(['error' => 'Order not found'], 404);
        }

        // Check print execution status
        $execution = Database::fetch(
            "SELECT * FROM event_print_executions WHERE order_id = ? ORDER BY created_at DESC LIMIT 1",
            [$order['id']]
        );

        $response->json([
            'order_number' => $order['order_number'],
            'order_status' => $order['status'],
            'print_status' => $execution['status'] ?? 'PENDING',
            'is_completed' => ($order['status'] === 'COMPLETED'),
            'is_printing' => ($order['status'] === 'PRINTING'),
            'total_amount' => $order['total_amount'],
            'total_pages' => $order['total_pages'],
            'confirmed_at' => $order['confirmed_at'],
            'completed_at' => $order['completed_at']
        ]);
    }
}

