<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\ZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DesiredWash API Routes  —  prefix: /api/v1  (set in bootstrap/app.php)
|--------------------------------------------------------------------------
*/

// ── Auth (public) ─────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('social', [AuthController::class, 'social']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

// ── Public ────────────────────────────────────────────────────────────────
Route::get('zones', [ZoneController::class, 'index']);
Route::get('services', [ServicesController::class, 'index']);

// ── Flutterwave webhook (public — verified by verif-hash header) ──────────
Route::post('webhooks/flutterwave', [WebhookController::class, 'handle']);

// ── Authenticated routes ──────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);

    // Orders
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Payments
    Route::post('payments/initiate', [PaymentController::class, 'initiate']);
    Route::post('payments/verify', [PaymentController::class, 'verify']);

    // Wallet
    Route::get('wallet/balance', [WalletController::class, 'balance']);
    Route::post('wallet/topup', [WalletController::class, 'topup']);
    Route::post('wallet/topup/verify', [WalletController::class, 'verifyTopup']);
    Route::get('wallet/transactions', [WalletController::class, 'transactions']);
});
