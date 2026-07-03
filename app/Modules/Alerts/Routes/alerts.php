<?php

use App\Modules\Alerts\Http\Controllers\AlertsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/alerts', [AlertsController::class, 'index'])->name('alerts.index');
});
