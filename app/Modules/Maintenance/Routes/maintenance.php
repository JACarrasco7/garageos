<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Maintenance\Http\Controllers\MaintenanceController;

Route::get('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
Route::get('/vehicles/{vehicle}/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
Route::post('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
