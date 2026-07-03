<?php

use App\Modules\Marketplace\Http\Controllers\ListingAnalysisController;
use App\Modules\Marketplace\Http\Controllers\MarketplaceController;
use App\Modules\Marketplace\Http\Controllers\SaleReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Sale Report Routes (legacy)
    Route::get('/vehicles/{vehicle}/marketplace/generate', [SaleReportController::class, 'create'])
        ->name('marketplace.generate');
    Route::post('/vehicles/{vehicle}/marketplace/generate', [SaleReportController::class, 'store'])
        ->name('marketplace.store');
    Route::get('/marketplace/{token}', [SaleReportController::class, 'show'])
        ->name('marketplace.show');
    Route::get('/marketplace/{report}/download', [SaleReportController::class, 'download'])
        ->name('marketplace.download');

    // Marketplace Listings Routes
    Route::prefix('marketplace')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('marketplace.index');
        Route::get('/create', [MarketplaceController::class, 'create'])->name('marketplace.create');
        Route::post('/', [MarketplaceController::class, 'store'])->name('marketplace.store-listing');
        Route::get('/my-listings', [MarketplaceController::class, 'myListings'])->name('marketplace.my-listings');

        // URL Analysis
        Route::get('/analyze', [ListingAnalysisController::class, 'create'])->name('marketplace.analyze.create');
        Route::post('/analyze', [ListingAnalysisController::class, 'store'])->name('marketplace.analyze.store');

        Route::prefix('listings/{listing}')->group(function () {
            Route::get('/', [MarketplaceController::class, 'show'])->name('marketplace.show-listing');
            Route::patch('/', [MarketplaceController::class, 'update'])->name('marketplace.update');
            Route::delete('/', [MarketplaceController::class, 'destroy'])->name('marketplace.destroy');
            Route::post('/favorite', [MarketplaceController::class, 'toggleFavorite'])->name('marketplace.favorite');
        });
    });
});

// Ruta pública para informes
Route::get('/r/{token}', [SaleReportController::class, 'showPublic'])
    ->name('marketplace.public');
