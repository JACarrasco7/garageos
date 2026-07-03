<?php

use App\Modules\Maintenance\Http\Controllers\PublicWorkshopController;
use Illuminate\Support\Facades\Route;

// Public workshop marketplace
Route::get('/workshops', [PublicWorkshopController::class, 'index'])->name('workshops.public');
Route::get('/workshops/nearby', [PublicWorkshopController::class, 'nearby'])->name('workshops.nearby');
Route::get('/workshops/{workshop}', [PublicWorkshopController::class, 'show'])->name('workshops.public.show');
Route::post('/workshops/{workshop}/review', [PublicWorkshopController::class, 'review'])->name('workshops.review')->middleware('auth');
