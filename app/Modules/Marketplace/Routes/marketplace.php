<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Marketplace\Http\Controllers\SaleReportController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/vehicles/{vehicle}/marketplace/generate', [SaleReportController::class, 'create'])
        ->name('marketplace.generate');
    Route::post('/vehicles/{vehicle}/marketplace/generate', [SaleReportController::class, 'store'])
        ->name('marketplace.store');
    Route::get('/marketplace/{token}', [SaleReportController::class, 'show'])
        ->name('marketplace.show');
    Route::get('/marketplace/{report}/download', [SaleReportController::class, 'download'])
        ->name('marketplace.download');
});

// Ruta pública para informes
Route::get('/r/{token}', [SaleReportController::class, 'showPublic'])
    ->name('marketplace.public');