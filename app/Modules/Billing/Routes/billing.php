<?php

use App\Modules\Billing\Http\Controllers\DashboardController;
use App\Modules\Billing\Http\Controllers\InvoiceOcrController;
use App\Modules\Billing\Http\Controllers\PaymentController;
use App\Modules\Billing\Http\Controllers\StripeConnectController;
use App\Modules\Billing\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Stripe Connect
    Route::prefix('stripe/connect')->name('stripe.connect.')->group(function () {
        Route::get('/', [StripeConnectController::class, 'index'])->name('index');
        Route::post('/', [StripeConnectController::class, 'create'])->name('create');
        Route::get('/onboard', [StripeConnectController::class, 'onboard'])->name('onboard');
        Route::get('/refresh', [StripeConnectController::class, 'refresh'])->name('refresh');
        Route::get('/complete', [StripeConnectController::class, 'complete'])->name('complete');
        Route::get('/dashboard', [StripeConnectController::class, 'dashboard'])->name('dashboard');
    });

    // Payment Intents
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::post('/', [PaymentController::class, 'create'])->name('create');
    });

    // Billing Dashboard
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/invoices', [DashboardController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/scan', [InvoiceOcrController::class, 'create'])->name('invoices.scan.create');
        Route::post('/invoices/scan', [InvoiceOcrController::class, 'store'])->name('invoices.scan.store');
    });
});

// Stripe Webhooks (no auth)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware(['web', 'auth']);
