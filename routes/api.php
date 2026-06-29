<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Modules\Vehicle\Http\Controllers\PublicVehicleController;

// Rutas públicas para QR
Route::get('/vehicles/qr/{token}', [PublicVehicleController::class, 'showByQr'])->name('vehicles.qr');

// API pública para integraciones OBD
Route::get('/v1/vehicles/{qr_token}', [VehicleController::class, 'show'])->name('api.vehicles.show');
Route::get('/v1/vehicles/{qr_token}/service-pack/{type}', [VehicleController::class, 'servicePack'])->name('api.vehicles.service-pack');
