<?php

use App\Modules\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/stripe-keys', [AdminController::class, 'stripeKeys'])->name('stripe-keys');
    Route::put('/stripe-keys', [AdminController::class, 'updateStripeKeys'])->name('stripe-keys.update');
});
