<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Maintenance\Http\Controllers\MaintenanceController;

Route::get('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
Route::post('/vehicles/{vehicle}/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');