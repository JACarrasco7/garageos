<?php

use App\Modules\Messaging\Http\Controllers\MessagingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('messaging')->name('messaging.')->group(function () {
        Route::get('/', [MessagingController::class, 'index'])->name('index');
        Route::post('/', [MessagingController::class, 'store'])->name('store');

        Route::prefix('{conversation}')->group(function () {
            Route::get('/', [MessagingController::class, 'show'])->name('show');
            Route::post('/reply', [MessagingController::class, 'reply'])->name('reply');
            Route::post('/read', [MessagingController::class, 'markAsRead'])->name('read');
            Route::post('/archive', [MessagingController::class, 'archive'])->name('archive');
        });
    });
});
