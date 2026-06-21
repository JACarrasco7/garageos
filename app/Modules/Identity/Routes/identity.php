<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Identity\Http\Controllers\GarageController;

Route::get('/garages', [GarageController::class, 'index'])->name('garages.index');
Route::get('/garages/{garage}', [GarageController::class, 'show'])->name('garages.show');
