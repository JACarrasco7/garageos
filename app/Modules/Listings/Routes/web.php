<?php

use App\Modules\Listings\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('listings')->group(function () {
    Route::get('/', [ListingController::class, 'index'])->name('listings.index');
    Route::get('/create', [ListingController::class, 'create'])->name('listings.create')->middleware('subscription:marketplace.create');
    Route::post('/', [ListingController::class, 'store'])->name('listings.store')->middleware('subscription:marketplace.create');
    Route::get('/search', [ListingController::class, 'search'])->name('listings.search');
    Route::get('/nearby', [ListingController::class, 'nearby'])->name('listings.nearby');
    // More routes will be added here
});
