<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Vehicle\Http\Controllers\VehicleController;
use App\Modules\Vehicle\Http\Controllers\VehicleImportController;

Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/import', [VehicleImportController::class, 'create'])->name('vehicles.import.create');
Route::post('/vehicles/import', [VehicleImportController::class, 'store'])->name('vehicles.import.store');
Route::get('/vehicles/export', [VehicleImportController::class, 'export'])->name('vehicles.export');
Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
