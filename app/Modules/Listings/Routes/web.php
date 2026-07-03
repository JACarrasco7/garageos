<?php

use App\Modules\Listings\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('listings')->group(function () {
    Route::get('/', [ListingController::class, 'index'])->name('listings.index');
    Route::get('/search', [ListingController::class, 'search'])->name('listings.search');
    Route::get('/nearby', [ListingController::class, 'nearby'])->name('listings.nearby');
    // More routes will be added here
});
