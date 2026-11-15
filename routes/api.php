<?php

use App\Core\Application;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConnectorApiController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\EnsureShopHasActiveSubscription;

$router = Application::getInstance()->getRouter();

// Customer Portal API Endpoints
$router->post('/api/print/{shopSlug}/upload', [CustomerPortalController::class, 'upload'], [
    EnsureShopHasActiveSubscription::class
]);
$router->post('/api/print/{shopSlug}/order', [CustomerPortalController::class, 'createOrder'], [
    EnsureShopHasActiveSubscription::class
]);
$router->post('/api/print/{shopSlug}/preview-slice', [CustomerPortalController::class, 'previewSlice']);
$router->post('/api/orders/{orderNumber}/pay', [CustomerPortalController::class, 'submitPayment']);
$router->get('/api/orders/{orderNumber}/status', [CustomerPortalController::class, 'getOrderStatus']);

// Merchant Real-Time Dashboard APIs
$router->get('/api/dashboard/orders/poll', [DashboardController::class, 'pollLiveOrders']);
$router->post('/api/dashboard/orders/status', [DashboardController::class, 'updateOrderStatus']);

// Merchant Billing & Razorpay Integration APIs
$router->post('/api/billing/create-order', [BillingController::class, 'createOrder']);
$router->post('/api/billing/verify-payment', [BillingController::class, 'verifyPayment']);

// Desktop Connector REST APIs
$router->post('/api/connector/pair', [ConnectorApiController::class, 'pair']);

// Protected Connector endpoints requiring Machine Agent Bearer Token
$router->post('/api/connector/register-printers', [ConnectorApiController::class, 'registerPrinters'], [
    ApiAuthMiddleware::class
]);

$router->get('/api/connector/jobs', [ConnectorApiController::class, 'getPendingJobs'], [
    ApiAuthMiddleware::class,
    EnsureShopHasActiveSubscription::class
]);

$router->post('/api/connector/jobs/{job_id}/status', [ConnectorApiController::class, 'updateJobStatus'], [
    ApiAuthMiddleware::class
]);

// Secure File Download Endpoint
$router->get('/api/files/download', [ConnectorApiController::class, 'downloadFile']);

