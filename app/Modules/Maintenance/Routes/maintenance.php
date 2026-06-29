<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Maintenance\Http\Controllers\MaintenanceController;
use App\Modules\Maintenance\Http\Controllers\WorkshopController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/vehicles/{vehicle}/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
    Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
});
