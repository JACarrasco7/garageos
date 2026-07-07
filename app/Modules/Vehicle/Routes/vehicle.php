<?php

use App\Modules\Vehicle\Http\Controllers\VehicleController;
use App\Modules\Vehicle\Http\Controllers\VehicleImportController;
use App\Modules\Vehicle\Http\Controllers\VehiclePhotoController;
use App\Modules\Vehicle\Http\Controllers\VehicleDocumentOcrController;
use Illuminate\Support\Facades\Route;

Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/import', [VehicleImportController::class, 'create'])->name('vehicles.import.create');
Route::post('/vehicles/import', [VehicleImportController::class, 'store'])->name('vehicles.import.store');
Route::get('/vehicles/export', [VehicleImportController::class, 'export'])->name('vehicles.export');
Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
Route::get('/vehicles/wizard', [VehicleController::class, 'wizard'])->name('vehicles.wizard');
Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store')->middleware('vehicle.limit');
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/vehicles/{vehicle}/photos', [VehiclePhotoController::class, 'index'])->name('vehicles.photos.index');
Route::post('/vehicles/{vehicle}/photos', [VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
Route::put('/vehicles/{vehicle}/photos/{photo}', [VehiclePhotoController::class, 'update'])->name('vehicles.photos.update');
Route::post('/vehicles/{vehicle}/photos/reorder', [VehiclePhotoController::class, 'reorder'])->name('vehicles.photos.reorder');
Route::delete('/vehicles/{vehicle}/photos/{photo}', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');
Route::post('/vehicles/{vehicle}/photo', [VehiclePhotoController::class, 'store'])->name('vehicles.photo.store');
Route::delete('/vehicles/{vehicle}/photo', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photo.destroy');
Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
Route::get('/vehicles/{vehicle}/report', [VehicleController::class, 'generateReport'])->name('vehicles.report');

// Vehicle Document OCR
Route::get('/vehicles/documents/ocr', [VehicleDocumentOcrController::class, 'create'])->name('vehicle.documents.ocr.create');
Route::post('/vehicles/documents/ocr', [VehicleDocumentOcrController::class, 'store'])->name('vehicle.documents.ocr.store');
