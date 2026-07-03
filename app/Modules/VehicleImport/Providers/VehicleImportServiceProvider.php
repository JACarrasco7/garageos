<?php

namespace App\Modules\VehicleImport\Providers;

use App\Modules\VehicleImport\Http\Controllers\ImportDocumentController;
use App\Modules\VehicleImport\Http\Controllers\VehicleImportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VehicleImportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/imports', [VehicleImportController::class, 'index'])->name('imports.index');
            Route::get('/imports/create', [VehicleImportController::class, 'create'])->name('imports.create');
            Route::post('/imports', [VehicleImportController::class, 'store'])->name('imports.store');
            Route::get('/imports/{import}', [VehicleImportController::class, 'show'])->name('imports.show');
            Route::get('/imports/{import}/wizard', [VehicleImportController::class, 'wizard'])->name('import.wizard');
            Route::post('/imports/{import}/upload', [VehicleImportController::class, 'uploadDocument'])->name('import.upload');
            Route::patch('/imports/{import}/step', [VehicleImportController::class, 'updateStep'])->name('import.update-step');
            Route::post('/imports/{import}/process', [VehicleImportController::class, 'process'])->name('imports.process');
            Route::delete('/imports/documents/{document}', [ImportDocumentController::class, 'destroy'])->name('import.documents.destroy');
            Route::get('/imports/{import}/valuation', [VehicleImportController::class, 'valuation'])->name('import.valuation');
            Route::post('/imports/{import}/certificate', [VehicleImportController::class, 'generateCertificate'])->name('import.certificate');
        });
    }
}
