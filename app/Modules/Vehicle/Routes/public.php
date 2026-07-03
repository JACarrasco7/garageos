<?php

use App\Modules\Vehicle\Http\Controllers\PublicVehicleController;
use Illuminate\Support\Facades\Route;

// Public routes - No auth required
Route::get('/public/vehicles/{token}', [PublicVehicleController::class, 'showByQr'])->name('public.vehicles.show');
Route::get('/vehicles/qr/{token}', [PublicVehicleController::class, 'showByQr'])->name('vehicles.qr');
