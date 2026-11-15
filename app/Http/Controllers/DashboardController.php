<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\DeviceAgent;
use App\Models\Order;
use App\Models\ResourcePricingRule;
use App\Models\ResourcePrinter;
use App\Models\Shop;
use App\Models\ShopSubscription;

class DashboardController
{
    /**
     * Live Orders Screen
     */
    public function index(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $metrics = Order::getShopMetrics($shop['id']);
        $orders = Order::getShopOrders($shop['id'], 30);
        $subscription = ShopSubscription::getActiveSubscription($shop['id']);
        $defaultPrinter = ResourcePrinter::getDefaultPrinter($shop['id']);

        View::render('dashboard.index', [
            'shop' => $shop,
            'metrics' => $metrics,
            'orders' => $orders,
            'subscription' => $subscription,
            'defaultPrinter' => $defaultPrinter
        ], 'layouts.dashboard');
    }

    /**
     * Real-time polling for incoming orders with sound chime
     */
    public function pollLiveOrders(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $since = $request->get('since', date('Y-m-d H:i:s', strtotime('-1 hour')));

        $newOrders = Database::fetchAll(
            "SELECT o.id, o.order_number, o.total_pages, o.total_amount, o.status, o.created_at,
                    p.payment_method, p.transaction_reference,
                    d.original_name as file_name,
                    s.copies, s.color_mode, s.sides
             FROM event_orders o
             LEFT JOIN event_payments p ON o.id = p.order_id
             LEFT JOIN event_order_specifications s ON o.id = s.order_id
             LEFT JOIN resource_digital_documents d ON s.document_id = d.id
             WHERE o.shop_id = ? AND o.created_at > ?
             ORDER BY o.created_at DESC",
            [$shop['id'], $since]
        );

        $metrics = Order::getShopMetrics($shop['id']);

        $response->json([
            'new_orders' => $newOrders,
            'metrics' => $metrics,
            'server_time' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Manual Order Action (e.g. Mark Paid, Complete, Cancel)
     */
    public function updateOrderStatus(Request $request, Response $response): void
    {
        $orderId = $request->get('order_id');
        $status = $request->get('status');

        $shop = Auth::shop();
        $order = Order::findById($orderId);

        if (!$order || $order['shop_id'] !== $shop['id']) {
            $response->json(['error' => 'Order not found'], 404);
        }

        Order::updateStatus($orderId, $status);

        if ($status === 'PRINTING') {
            // Mark payment as verified
            Database::execute("UPDATE event_payments SET is_verified = 1 WHERE order_id = ?", [$orderId]);

            // Ensure execution exists
            $exec = Database::fetch("SELECT id FROM event_print_executions WHERE order_id = ?", [$orderId]);
            if (!$exec) {
                $printer = ResourcePrinter::getDefaultPrinter($shop['id']);
                \App\Models\PrintExecution::create($orderId, $printer['device_agent_id'] ?? null, $printer['id'] ?? null);
            } else {
                Database::execute("UPDATE event_print_executions SET status = 'PENDING', attempts = 0 WHERE id = ?", [$exec['id']]);
            }
        }

        $response->json(['success' => true]);
    }

    /**
     * Rate Card Configuration
     */
    public function rates(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $rules = ResourcePricingRule::getShopMatrix($shop['id']);

        View::render('dashboard.rates', [
            'shop' => $shop,
            'rules' => $rules,
            'success' => Session::flash('success')
        ], 'layouts.dashboard');
    }

    public function updateRates(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $rates = $request->get('rates', []);

        if (is_array($rates)) {
            ResourcePricingRule::saveRules($shop['id'], $rates);
            Session::flash('success', 'Rate card updated successfully!');
        }

        $response->redirect('/dashboard/rates');
    }

    /**
     * Shop Profile & UPI Setup
     */
    public function shop(Request $request, Response $response): void
    {
        $shop = Auth::shop();

        View::render('dashboard.shop', [
            'shop' => $shop,
            'success' => Session::flash('success'),
            'error' => Session::flash('error')
        ], 'layouts.dashboard');
    }

    public function updateShop(Request $request, Response $response): void
    {
        $shop = Auth::shop();

        $name = trim($request->get('name', ''));
        $upiId = trim($request->get('upi_id', ''));
        $upiName = trim($request->get('upi_name', ''));
        $acceptCash = $request->get('accept_cash') ? 1 : 0;
        $autoprint = $request->get('autoprint') ? 1 : 0;

        if (empty($name) || empty($upiId) || empty($upiName)) {
            Session::flash('error', 'Please fill in shop name, UPI ID, and UPI Name.');
            $response->redirect('/dashboard/shop');
            return;
        }

        $updateData = [
            'name' => $name,
            'upi_id' => $upiId,
            'upi_name' => $upiName,
            'accept_cash' => $acceptCash,
            'autoprint' => $autoprint
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg'])) {
                $dir = __DIR__ . '/../../../public/uploads/logos';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $filename = 'logo_' . $shop['id'] . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $dir . '/' . $filename);
                $updateData['logo_url'] = '/uploads/logos/' . $filename;
            }
        }

        Shop::update($shop['id'], $updateData);
        Session::flash('success', 'Shop settings saved successfully.');
        $response->redirect('/dashboard/shop');
    }

    /**
     * Printer Pairing Tab
     */
    public function printers(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $devices = DeviceAgent::findByShopId($shop['id']);
        $printers = ResourcePrinter::findByShopId($shop['id']);

        View::render('dashboard.printers', [
            'shop' => $shop,
            'devices' => $devices,
            'printers' => $printers,
            'pairingCode' => Session::flash('pairing_code'),
            'success' => Session::flash('success')
        ], 'layouts.dashboard');
    }

    public function generatePairingPin(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $pin = DeviceAgent::generatePairingCode($shop['id'], 'Windows Counter PC');

        Session::flash('pairing_code', $pin);
        $response->redirect('/dashboard/printers');
    }

    public function setDefaultPrinter(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $printerId = $request->get('printer_id');

        Database::execute("UPDATE resource_printers SET is_default = 0 WHERE shop_id = ?", [$shop['id']]);
        Database::execute("UPDATE resource_printers SET is_default = 1 WHERE shop_id = ? AND id = ?", [$shop['id'], $printerId]);

        Session::flash('success', 'Default printer updated.');
        $response->redirect('/dashboard/printers');
    }

    public function disconnectPrinter(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $printerId = $request->get('printer_id');

        // Delete printer from shop
        Database::execute("DELETE FROM resource_printers WHERE shop_id = ? AND id = ?", [$shop['id'], $printerId]);

        // If no default printer left, pick first available
        $hasDefault = Database::fetch("SELECT id FROM resource_printers WHERE shop_id = ? AND is_default = 1 LIMIT 1", [$shop['id']]);
        if (!$hasDefault) {
            Database::execute("UPDATE resource_printers SET is_default = 1 WHERE shop_id = ? LIMIT 1", [$shop['id']]);
        }

        Session::flash('success', 'Printer disconnected successfully.');
        $response->redirect('/dashboard/printers');
    }

    public function disconnectDevice(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $deviceId = $request->get('device_id');

        // Disconnect and remove machine agent
        Database::execute("DELETE FROM device_agents WHERE shop_id = ? AND id = ?", [$shop['id'], $deviceId]);
        // Also remove linked printers or mark them offline
        Database::execute("UPDATE resource_printers SET is_online = 0 WHERE shop_id = ? AND device_agent_id = ?", [$shop['id'], $deviceId]);

        Session::flash('success', 'Windows PC agent disconnected.');
        $response->redirect('/dashboard/printers');
    }
}

