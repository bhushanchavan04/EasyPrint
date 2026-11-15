<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Shop;
use App\Models\SubscriptionPlan;

class HomeController
{
    public function index(Request $request, Response $response): void
    {
        $isLoggedIn = Auth::check();
        $user = $isLoggedIn ? Auth::user() : null;
        $shop = $isLoggedIn ? Auth::shop() : null;

        // Fetch active plans for pricing section
        $plans = SubscriptionPlan::allActive();

        // Get demo shop for live interactive preview
        $demoShop = Shop::findBySlug('pixaprint');
        $demoSlug = $demoShop ? $demoShop['slug'] : 'pixaprint';

        View::render('home.index', [
            'isLoggedIn' => $isLoggedIn,
            'user' => $user,
            'shop' => $shop,
            'plans' => $plans,
            'demoSlug' => $demoSlug,
            'title' => 'EasyPrint SaaS - #1 Print Automation & QR Kiosk for Cyber Cafes'
        ], null); // Render as standalone full-page template for complete design control
    }
}
