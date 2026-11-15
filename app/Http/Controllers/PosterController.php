<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Env;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Shop;
use App\Services\UpiService;

class PosterController
{
    public function generate(Request $request, Response $response, array $params = []): void
    {
        $shopSlug = $params['shopSlug'] ?? null;
        $shop = null;

        if ($shopSlug) {
            $shop = Shop::findBySlug($shopSlug);
        }

        if (!$shop) {
            $shop = Auth::shop();
        }

        if (!$shop) {
            $response->redirect('/login');
            return;
        }

        $appUrl = rtrim(Env::get('APP_URL', 'http://localhost:8000'), '/');
        $customerPrintUrl = "{$appUrl}/print/{$shop['slug']}";
        $qrCodeUrl = UpiService::getQrCodeUrl($customerPrintUrl, 450);

        View::render('dashboard.poster', [
            'shop' => $shop,
            'customerPrintUrl' => $customerPrintUrl,
            'qrCodeUrl' => $qrCodeUrl
        ], null); // Render standalone printable view without default layout
    }
}

