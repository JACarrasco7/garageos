<?php

use App\Modules\Documents\Http\Controllers\CameraCaptureController;
use App\Modules\Documents\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/vehicles/{vehicle}/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/vehicles/{vehicle}/documents/upload', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/vehicles/{vehicle}/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

    Route::post('/camera/auth', [CameraCaptureController::class, 'generateAuth'])->name('camera.auth');
    Route::get('/camera/auth/{token}', [CameraCaptureController::class, 'validateAuth'])->name('camera.validate');
    Route::delete('/camera/auth/{token}', [CameraCaptureController::class, 'revokeAuth'])->name('camera.revoke');
});
