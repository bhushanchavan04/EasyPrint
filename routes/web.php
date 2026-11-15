<?php

use App\Core\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PosterController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\EnsureShopHasActiveSubscription;

$router = Application::getInstance()->getRouter();

// Public SaaS Landing Page
$router->get('/', [HomeController::class, 'index']);

// Authentication
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Customer Zero-Login Print Portal
$router->get('/print/{shopSlug}', [CustomerPortalController::class, 'show'], [
    EnsureShopHasActiveSubscription::class
]);

// Direct Public Counter Poster
$router->get('/poster/{shopSlug}', [PosterController::class, 'generate']);

// Merchant Dashboard Routes (Enforces Auth and Active Subscription Paywall)
$router->group(['middleware' => [AuthMiddleware::class, EnsureShopHasActiveSubscription::class]], function ($router) {
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->get('/dashboard/rates', [DashboardController::class, 'rates']);
    $router->post('/dashboard/rates', [DashboardController::class, 'updateRates']);
    $router->get('/dashboard/shop', [DashboardController::class, 'shop']);
    $router->post('/dashboard/shop', [DashboardController::class, 'updateShop']);
    $router->get('/dashboard/printers', [DashboardController::class, 'printers']);
    $router->post('/dashboard/printers/generate-pin', [DashboardController::class, 'generatePairingPin']);
    $router->post('/dashboard/printers/set-default', [DashboardController::class, 'setDefaultPrinter']);
    $router->post('/dashboard/printers/disconnect', [DashboardController::class, 'disconnectPrinter']);
    $router->post('/dashboard/devices/disconnect', [DashboardController::class, 'disconnectDevice']);
    $router->get('/dashboard/poster', [PosterController::class, 'generate']);
    
    // Billing routes
    $router->get('/dashboard/billing', [BillingController::class, 'index']);
});

// Paywall Subscribe Screen (Only requires Auth, accessible when subscription is expired)
$router->group(['middleware' => [AuthMiddleware::class]], function ($router) {
    $router->get('/dashboard/billing/subscribe', [BillingController::class, 'subscribe']);
});

