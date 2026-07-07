<?php

use App\Modules\Maintenance\Http\Controllers\MaintenanceController;
use App\Modules\Maintenance\Http\Controllers\PublicWorkshopController;
use App\Modules\Maintenance\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

// User workshop management (auth required)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'indexForVehicle'])->name('maintenance.vehicle.index');
    Route::get('/vehicles/{vehicle}/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/my-workshops', [WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/my-workshops/map', [WorkshopController::class, 'map'])->name('workshops.map');
    Route::post('/my-workshops', [WorkshopController::class, 'store'])->name('workshops.store');
});

// Public workshop marketplace
Route::get('/workshops', [PublicWorkshopController::class, 'index'])->name('workshops.public');
Route::get('/workshops/{workshop}', [PublicWorkshopController::class, 'show'])->name('workshops.public.show');
Route::post('/workshops/{workshop}/review', [PublicWorkshopController::class, 'review'])->name('workshops.review')->middleware('auth');
