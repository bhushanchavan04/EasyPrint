<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Agent;
use App\Models\ResourcePricingRule;
use App\Models\Shop;
use App\Models\ShopSubscription;

class AuthController
{
    public function showLogin(Request $request, Response $response): void
    {
        if (Auth::check()) {
            $response->redirect('/dashboard');
        }
        View::render('auth.login', ['error' => Session::flash('error')], 'layouts.app');
    }

    public function login(Request $request, Response $response): void
    {
        $email = trim($request->get('email', ''));
        $password = $request->get('password', '');

        $agent = Agent::findByEmail($email);
        if (!$agent || !Agent::verifyPassword($agent, $password)) {
            Session::flash('error', 'Invalid email address or password.');
            $response->redirect('/login');
            return;
        }

        Auth::login($agent);

        // Check subscription
        $shop = Shop::findByOwnerId($agent['id']);
        if ($shop) {
            $activeSub = ShopSubscription::getActiveSubscription($shop['id']);
            if (!$activeSub) {
                $response->redirect('/dashboard/billing/subscribe');
                return;
            }
        }

        $response->redirect('/dashboard');
    }

    public function showRegister(Request $request, Response $response): void
    {
        if (Auth::check()) {
            $response->redirect('/dashboard');
        }
        View::render('auth.register', ['error' => Session::flash('error')], 'layouts.app');
    }

    public function register(Request $request, Response $response): void
    {
        $name = trim($request->get('name', ''));
        $email = trim(strtolower($request->get('email', '')));
        $password = $request->get('password', '');
        $phone = trim($request->get('phone', ''));
        $shopName = trim($request->get('shop_name', ''));
        $upiId = trim($request->get('upi_id', ''));
        $upiName = trim($request->get('upi_name', $shopName));

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($shopName) || empty($upiId)) {
            Session::flash('error', 'Please fill all required fields.');
            $response->redirect('/register');
            return;
        }

        if (Agent::findByEmail($email)) {
            Session::flash('error', 'An account with this email already exists.');
            $response->redirect('/register');
            return;
        }

        // Generate clean unique shop slug
        $baseSlug = preg_replace('/[^a-z0-9]+/', '-', strtolower($shopName));
        $baseSlug = trim($baseSlug, '-');
        if (empty($baseSlug)) {
            $baseSlug = 'shop-' . substr(bin2hex(random_bytes(3)), 0, 6);
        }

        $slug = $baseSlug;
        $counter = 1;
        while (Shop::findBySlug($slug)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        Database::beginTransaction();
        try {
            // 1. Create Agent
            $agentId = Agent::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'agent_type' => 'SHOP_OWNER'
            ]);

            // 2. Create Shop
            $shopId = Shop::create([
                'owner_agent_id' => $agentId,
                'name' => $shopName,
                'slug' => $slug,
                'upi_id' => $upiId,
                'upi_name' => $upiName,
                'accept_cash' => 1,
                'autoprint' => 1
            ]);

            // 3. Seed Default Rate Card
            $defaultRates = [
                ['paper_size' => 'A4', 'color_mode' => 'BLACK_AND_WHITE', 'sides' => 'SINGLE', 'price_per_page' => 2.00],
                ['paper_size' => 'A4', 'color_mode' => 'BLACK_AND_WHITE', 'sides' => 'DOUBLE', 'price_per_page' => 3.00],
                ['paper_size' => 'A4', 'color_mode' => 'COLOR', 'sides' => 'SINGLE', 'price_per_page' => 10.00],
                ['paper_size' => 'A4', 'color_mode' => 'COLOR', 'sides' => 'DOUBLE', 'price_per_page' => 18.00],
                ['paper_size' => 'A3', 'color_mode' => 'BLACK_AND_WHITE', 'sides' => 'SINGLE', 'price_per_page' => 5.00],
                ['paper_size' => 'A3', 'color_mode' => 'COLOR', 'sides' => 'SINGLE', 'price_per_page' => 20.00],
                ['paper_size' => 'LEGAL', 'color_mode' => 'BLACK_AND_WHITE', 'sides' => 'SINGLE', 'price_per_page' => 3.00],
                ['paper_size' => 'LEGAL', 'color_mode' => 'BLACK_AND_WHITE', 'sides' => 'DOUBLE', 'price_per_page' => 5.00],
            ];
            ResourcePricingRule::saveRules($shopId, $defaultRates);

            Database::commit();

            $agent = Agent::findById($agentId);
            Auth::login($agent);

            // Redirect directly to subscription paywall page
            $response->redirect('/dashboard/billing/subscribe');
        } catch (\Throwable $e) {
            Database::rollBack();
            Session::flash('error', 'Registration failed: ' . $e->getMessage());
            $response->redirect('/register');
        }
    }

    public function logout(Request $request, Response $response): void
    {
        Auth::logout();
        $response->redirect('/login');
    }
}

