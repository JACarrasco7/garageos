<?php

use App\Modules\Listings\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('listings')->group(function () {
    Route::get('/', [ListingController::class, 'index'])->name('listings.index');
    // More routes will be added here
});
