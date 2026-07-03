<?php

use App\Http\Controllers\Api\VehicleController;
use App\Modules\Vehicle\Http\Controllers\PublicVehicleController;
use App\Modules\Vehicle\Http\Controllers\VehicleApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vehicles/brands', [VehicleApiController::class, 'getBrands']);
    Route::get('/vehicles/models/{brand}/{year}', [VehicleApiController::class, 'getModels']);
    Route::get('/vehicles/decode-vin/{vin}', [VehicleApiController::class, 'decodeVin']);
    Route::get('/vehicles/spanish-specs/{brand}/{model}/{year}', [VehicleApiController::class, 'getSpanishSpecs']);
});

// Rutas públicas para QR
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/vehicles/qr/{token}', [PublicVehicleController::class, 'showByQr'])->name('vehicles.qr');
    Route::get('/v1/vehicles/{qr_token}', [VehicleController::class, 'show'])->name('api.vehicles.show');
    Route::get('/v1/vehicles/{qr_token}/service-pack/{type}', [VehicleController::class, 'servicePack'])->name('api.vehicles.service-pack');
});
