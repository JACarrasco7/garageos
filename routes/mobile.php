<?php

use App\Http\Controllers\DashboardController;
use App\Modules\Alerts\Http\Controllers\AlertsController;
use App\Modules\Documents\Http\Controllers\DocumentController;
use App\Modules\Maintenance\Http\Controllers\MaintenanceController;
use App\Modules\Vehicle\Http\Controllers\VehicleController;
use App\Modules\Vehicle\Http\Controllers\VehiclePhotoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('m')->name('mobile.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'mobileIndex'])->name('dashboard');
    Route::get('/vehicles', [VehicleController::class, 'mobileIndex'])->name('vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'mobileCreate'])->name('vehicles.create');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'mobileShow'])->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'mobileIndex'])->name('maintenance.index');
    Route::get('/vehicles/{vehicle}/maintenance/create', [MaintenanceController::class, 'mobileCreate'])->name('maintenance.create');
    Route::get('/alerts', [AlertsController::class, 'mobileIndex'])->name('alerts.index');
    Route::get('/profile', [DashboardController::class, 'mobileProfile'])->name('profile');
    Route::get('/vehicles/{vehicle}/documents', [DocumentController::class, 'mobileIndex'])->name('documents.index');
    Route::get('/vehicles/{vehicle}/documents/upload', [DocumentController::class, 'mobileCreate'])->name('documents.upload');
    Route::post('/vehicles/{vehicle}/documents/upload', [DocumentController::class, 'mobileStore'])->name('documents.store');
    Route::get('/vehicles/{vehicle}/photos', [VehicleController::class, 'mobilePhotos'])->name('vehicles.photos');
    Route::post('/vehicles/{vehicle}/photos', [VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
    Route::delete('/vehicles/{vehicle}/photos/{photo}', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');
});
