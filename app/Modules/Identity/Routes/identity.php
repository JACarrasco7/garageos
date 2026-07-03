<?php

use App\Modules\Identity\Http\Controllers\DataExportController;
use App\Modules\Identity\Http\Controllers\GarageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/data-export', [DataExportController::class, 'export'])->name('data-export.export');
    Route::get('/data-export/{file}', [DataExportController::class, 'download'])->name('data-export.download');
    Route::delete('/account', [DataExportController::class, 'deleteAccount'])->name('account.delete');
});

Route::get('/garages', [GarageController::class, 'index'])->name('garages.index');
Route::get('/garages/{garage}', [GarageController::class, 'show'])->name('garages.show');
