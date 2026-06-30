<?php

namespace App\Modules\VehicleImport\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class VehicleImportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/imports', [\App\Modules\VehicleImport\Http\Controllers\VehicleImportController::class, 'index'])->name('imports.index');
            Route::get('/imports/create', [\App\Modules\VehicleImport\Http\Controllers\VehicleImportController::class, 'create'])->name('imports.create');
            Route::post('/imports', [\App\Modules\VehicleImport\Http\Controllers\VehicleImportController::class, 'store'])->name('imports.store');
            Route::get('/imports/{import}', [\App\Modules\VehicleImport\Http\Controllers\VehicleImportController::class, 'show'])->name('imports.show');
            Route::post('/imports/{import}/process', [\App\Modules\VehicleImport\Http\Controllers\VehicleImportController::class, 'process'])->name('imports.process');
        });
    }
}