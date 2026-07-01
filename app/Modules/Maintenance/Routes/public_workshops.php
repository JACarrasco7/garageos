<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Maintenance\Http\Controllers\PublicWorkshopController;

// Public workshop marketplace
Route::get('/workshops', [PublicWorkshopController::class, 'index'])->name('workshops.public');
Route::get('/workshops/{workshop}', [PublicWorkshopController::class, 'show'])->name('workshops.public.show');
Route::post('/workshops/{workshop}/review', [PublicWorkshopController::class, 'review'])->name('workshops.review')->middleware('auth');