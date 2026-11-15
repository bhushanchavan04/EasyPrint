<?php

namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Env;
use App\Core\Request;
use App\Core\Response;
use App\Models\DeviceAgent;
use App\Models\DigitalDocument;
use App\Models\Order;
use App\Models\PrintExecution;
use App\Models\ResourcePrinter;
use App\Models\ShopSubscription;

class ConnectorApiController
{
    /**
     * 1. POST /api/connector/pair
     */
    public function pair(Request $request, Response $response): void
    {
        $code = trim($request->get('pairing_code', ''));
        $deviceName = trim($request->get('device_name', 'Windows PC'));

        if (empty($code)) {
            $response->json(['error' => 'Pairing code is required.'], 400);
        }

        $agent = DeviceAgent::verifyPairingCode($code, $deviceName);
        if (!$agent) {
            $response->json(['error' => 'Invalid or expired 6-digit pairing code.'], 401);
        }

        $response->json([
            'success' => true,
            'auth_token' => $agent['raw_token'],
            'shop_name' => $agent['shop_name'],
            'shop_slug' => $agent['shop_slug'],
            'device_id' => $agent['id']
        ]);
    }

    /**
     * 2. POST /api/connector/register-printers
     */
    public function registerPrinters(Request $request, Response $response): void
    {
        $device = $GLOBALS['current_device'] ?? null;
        if (!$device) {
            $response->json(['error' => 'Unauthorized device'], 401);
        }

        $printers = $request->get('printers', []);
        if (!is_array($printers)) {
            $response->json(['error' => 'Invalid printers payload format.'], 400);
        }

        $synced = ResourcePrinter::syncPrinters($device['shop_id'], $device['id'], $printers);

        $response->json([
            'success' => true,
            'synced_printers' => $synced,
            'count' => count($synced)
        ]);
    }

    /**
     * 3. GET /api/connector/jobs
     */
    public function getPendingJobs(Request $request, Response $response): void
    {
        $device = $GLOBALS['current_device'] ?? null;
        if (!$device) {
            $response->json(['error' => 'Unauthorized device'], 401);
        }

        // Hard Paywall Check for Connector
        $activeSub = ShopSubscription::getActiveSubscription($device['shop_id']);
        if (!$activeSub) {
            $response->json([
                'error' => 'Payment Required: Subscription expired',
                'message' => 'Shop subscription has expired. Disallowing print queuing.'
            ], 402);
            return;
        }

        $job = PrintExecution::getNextPendingForShop($device['shop_id']);
        if (!$job) {
            $response->json(['has_job' => false, 'message' => 'No pending jobs']);
            return;
        }

        // Generate temporary secure download token (HMAC valid for 30 minutes)
        // If a custom page_range is specified, bind it into the token so the downloaded file is already sliced
        $expiry = time() + 1800;
        $secret = Env::get('APP_SECRET', 'secret_key');
        $pageRange = trim($job['page_range'] ?? 'all');
        $tokenRaw = "{$job['document_id']}|{$expiry}|{$pageRange}";
        $signature = hash_hmac('sha256', $tokenRaw, $secret);
        $downloadToken = base64_encode("{$tokenRaw}|{$signature}");

        $appUrl = rtrim(Env::get('APP_URL', 'http://localhost:8000'), '/');
        $fileDownloadUrl = "{$appUrl}/api/files/download?token=" . urlencode($downloadToken);

        $response->json([
            'has_job' => true,
            'job_id' => $job['execution_id'],
            'order_id' => $job['order_id'],
            'order_number' => $job['order_number'],
            'target_printer' => $job['target_printer'] ?: 'HP LaserJet Pro MFP M126nw',
            'system_spool_name' => $job['system_spool_name'] ?: 'HP_LaserJet_Pro_MFP_M126nw',
            'file_name' => $job['file_name'],
            'file_download_url' => $fileDownloadUrl,
            'settings' => [
                'copies' => (int)$job['copies'],
                'color' => ($job['color_mode'] === 'COLOR'),
                'duplex' => ($job['sides'] === 'DOUBLE'),
                'paper_size' => $job['paper_size'] ?? 'A4',
                'page_range' => $job['page_range'] ?? 'all',
                'layout' => $job['layout'] ?? 'PORTRAIT'
            ]
        ]);
    }

    /**
     * 4. POST /api/connector/jobs/{job_id}/status
     */
    public function updateJobStatus(Request $request, Response $response, array $params): void
    {
        $jobId = $params['job_id'];
        $status = strtoupper(trim($request->get('status', '')));

        $execution = PrintExecution::findById($jobId);
        if (!$execution) {
            $response->json(['error' => 'Job execution not found'], 404);
        }

        $validStatuses = ['DOWNLOADING', 'PROCESSING', 'SUBMITTED_TO_QUEUE', 'COMPLETED', 'FAILED'];
        if (!in_array($status, $validStatuses)) {
            $response->json(['error' => 'Invalid status transition'], 400);
        }

        PrintExecution::updateStatus($jobId, $status);

        // When submitted or completed, update the main customer order
        if ($status === 'COMPLETED' || $status === 'SUBMITTED_TO_QUEUE') {
            Order::updateStatus($execution['order_id'], 'COMPLETED');
        } elseif ($status === 'FAILED') {
            Order::updateStatus($execution['order_id'], 'FAILED');
        }

        $response->json([
            'success' => true,
            'job_id' => $jobId,
            'status' => $status
        ]);
    }

    /**
     * Secure Document Download for Desktop Connector
     */
    public function downloadFile(Request $request, Response $response): void
    {
        $token = $request->get('token', '');
        if (empty($token)) {
            $response->json(['error' => 'Missing download token'], 403);
        }

        $decoded = base64_decode($token);
        $parts = explode('|', $decoded);
        if (count($parts) < 3) {
            $response->json(['error' => 'Invalid download token structure'], 403);
        }

        // Support both 3-part (legacy) and 4-part (with page_range) tokens
        $secret = Env::get('APP_SECRET', 'secret_key');
        if (count($parts) === 4) {
            [$docId, $expiry, $pageRange, $signature] = $parts;
            $tokenRaw = "{$docId}|{$expiry}|{$pageRange}";
        } else {
            [$docId, $expiry, $signature] = $parts;
            $pageRange = 'all';
            $tokenRaw = "{$docId}|{$expiry}";
        }

        if (time() > (int)$expiry) {
            $response->json(['error' => 'Download token expired'], 403);
        }

        $expectedSignature = hash_hmac('sha256', $tokenRaw, $secret);
        if (!hash_equals($expectedSignature, $signature)) {
            $response->json(['error' => 'Invalid signature'], 403);
        }

        $doc = DigitalDocument::findById($docId);
        if (!$doc || !file_exists($doc['storage_path'])) {
            $response->json(['error' => 'Document file not found on disk'], 404);
        }

        $filePathToSend = $doc['storage_path'];
        $fileNameToSend = $doc['original_name'];

        // If page range is not 'all', physically slice the PDF!
        if (!empty($pageRange) && strtolower($pageRange) !== 'all') {
            $slicedPath = \App\Services\DocumentParser::slicePdf(
                $doc['storage_path'],
                $pageRange,
                (int)($doc['total_pages'] ?? 1)
            );
            if (file_exists($slicedPath) && filesize($slicedPath) > 0) {
                $filePathToSend = $slicedPath;
                $fileNameToSend = 'sliced_' . $fileNameToSend;
            }
        }

        $response->download($filePathToSend, $fileNameToSend);
    }
}

