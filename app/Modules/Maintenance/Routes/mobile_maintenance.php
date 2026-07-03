<?php

use App\Modules\Maintenance\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('m')->name('mobile.')->group(function () {
    Route::get('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'mobileIndex'])->name('maintenance.index');
    Route::get('/vehicles/{vehicle}/maintenance/create', [MaintenanceController::class, 'mobileCreate'])->name('maintenance.create');
});
