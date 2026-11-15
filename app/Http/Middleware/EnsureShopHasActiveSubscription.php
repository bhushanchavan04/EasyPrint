<?php

namespace App\Http\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Shop;
use App\Models\ShopSubscription;

class EnsureShopHasActiveSubscription
{
    public function handle(Request $request, Response $response, array $params = []): bool
    {
        $uri = $request->getUri();

        // 1. DASHBOARD ROUTES CHECK
        if (str_starts_with($uri, '/dashboard')) {
            // Allow billing subscription routes so merchant can pay
            if (str_starts_with($uri, '/dashboard/billing')) {
                return true;
            }

            $shop = Auth::shop();
            if (!$shop) {
                $response->redirect('/login');
                return false;
            }

            $activeSub = ShopSubscription::getActiveSubscription($shop['id']);
            if (!$activeSub) {
                // Hard Paywall Redirect
                $response->redirect('/dashboard/billing/subscribe');
                return false;
            }

            return true;
        }

        // 2. CUSTOMER PRINT PORTAL CHECK (/print/{shopSlug})
        if (str_starts_with($uri, '/print/')) {
            $shopSlug = $params['shopSlug'] ?? null;
            if (!$shopSlug && preg_match('#^/print/([^/]+)#', $uri, $m)) {
                $shopSlug = $m[1];
            }

            if ($shopSlug) {
                $shop = Shop::findBySlug($shopSlug);
                if (!$shop) {
                    $response->setStatusCode(404);
                    echo "Shop not found.";
                    return false;
                }

                $activeSub = ShopSubscription::getActiveSubscription($shop['id']);
                if (!$activeSub || !$shop['is_active']) {
                    // Render friendly blocked screen
                    View::render('customer.paused', ['shop' => $shop], null);
                    return false;
                }
            }

            return true;
        }

        // 3. DESKTOP CONNECTOR API ROUTE (GET /api/connector/jobs)
        if (str_starts_with($uri, '/api/connector/')) {
            $token = $request->getBearerToken();
            if ($token) {
                $tokenHash = hash('sha256', $token);
                $device = \App\Core\Database::fetch(
                    "SELECT d.*, s.id as shop_id FROM device_agents d JOIN shops s ON d.shop_id = s.id WHERE d.auth_token_hash = ?",
                    [$tokenHash]
                );

                if ($device) {
                    $activeSub = ShopSubscription::getActiveSubscription($device['shop_id']);
                    if (!$activeSub) {
                        $response->json([
                            'error' => 'Payment Required: Subscription expired',
                            'message' => "This shop's EasyPrint subscription has expired. Please renew on your merchant dashboard."
                        ], 402);
                        return false;
                    }
                }
            }
        }

        return true;
    }
}

