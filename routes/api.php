<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Vehicle\Http\Controllers\PublicVehicleController;

// Rutas públicas para QR
Route::get('/vehicles/qr/{token}', [PublicVehicleController::class, 'showByQr'])->name('vehicles.qr');