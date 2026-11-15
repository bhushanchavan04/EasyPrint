<?php

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\ShopSubscription;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;

class BillingController
{
    public function index(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $subscription = ShopSubscription::getActiveSubscription($shop['id']);
        $latestSub = ShopSubscription::getLatest($shop['id']);
        $plans = SubscriptionPlan::allActive();

        $history = Database::fetchAll(
            "SELECT s.*, p.name as plan_name, p.price 
             FROM shop_subscriptions s 
             JOIN subscription_plans p ON s.plan_id = p.id 
             WHERE s.shop_id = ? 
             ORDER BY s.created_at DESC",
            [$shop['id']]
        );

        View::render('dashboard.billing', [
            'shop' => $shop,
            'subscription' => $subscription,
            'latestSub' => $latestSub,
            'plans' => $plans,
            'history' => $history,
            'razorpayKey' => (new RazorpayService())->getKeyId(),
            'success' => Session::flash('success'),
            'error' => Session::flash('error')
        ], 'layouts.dashboard');
    }

    /**
     * Hard Paywall Dedicated Landing Page
     */
    public function subscribe(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $activeSub = ShopSubscription::getActiveSubscription($shop['id']);

        // If already active, redirect back to main dashboard
        if ($activeSub) {
            $response->redirect('/dashboard');
            return;
        }

        $plans = SubscriptionPlan::allActive();

        View::render('dashboard.billing', [
            'shop' => $shop,
            'subscription' => null,
            'latestSub' => ShopSubscription::getLatest($shop['id']),
            'plans' => $plans,
            'history' => [],
            'razorpayKey' => (new RazorpayService())->getKeyId(),
            'isPaywall' => true,
            'error' => 'Your merchant subscription is currently inactive or expired. Please select a plan to unlock your shop dashboard, customer kiosk, and desktop print spooler.'
        ], 'layouts.dashboard');
    }

    /**
     * Create Razorpay Order for Checkout
     */
    public function createOrder(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $planId = $request->get('plan_id');

        $plan = SubscriptionPlan::findById($planId);
        if (!$plan) {
            $response->json(['error' => 'Plan not found'], 404);
        }

        $receipt = 'rcpt_' . substr($shop['id'], 0, 8) . '_' . time();
        $razorpay = new RazorpayService();

        try {
            $order = $razorpay->createOrder((float)$plan['price'], $receipt, [
                'shop_id' => $shop['id'],
                'plan_id' => $plan['id']
            ]);

            $response->json([
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'plan_name' => $plan['name'],
                'plan_price' => $plan['price'],
                'key_id' => $razorpay->getKeyId(),
                'shop_name' => $shop['name']
            ]);
        } catch (\Throwable $e) {
            $response->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Payment & Activate Hard Paywall Unlock
     */
    public function verifyPayment(Request $request, Response $response): void
    {
        $shop = Auth::shop();
        $planId = $request->get('plan_id');
        $razorpayOrderId = $request->get('razorpay_order_id');
        $razorpayPaymentId = $request->get('razorpay_payment_id');
        $razorpaySignature = $request->get('razorpay_signature');

        $razorpay = new RazorpayService();
        $isValid = $razorpay->verifyPaymentSignature(
            $razorpayOrderId,
            $razorpayPaymentId,
            $razorpaySignature ?? ''
        );

        if (!$isValid) {
            $response->json(['error' => 'Invalid payment signature. Verification failed.'], 400);
        }

        // Activate Subscription
        ShopSubscription::subscribe(
            $shop['id'],
            $planId,
            $razorpayOrderId,
            $razorpayPaymentId
        );

        Session::flash('success', 'Subscription activated successfully! Welcome to EasyPrint Pro.');
        $response->json(['success' => true, 'redirect' => '/dashboard']);
    }
}

