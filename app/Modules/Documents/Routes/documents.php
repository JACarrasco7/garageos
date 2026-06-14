<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Documents\Http\Controllers\DocumentController;

Route::get('/vehicles/{vehicle}/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/vehicles/{vehicle}/documents/upload', [DocumentController::class, 'create'])->name('documents.create');
Route::post('/vehicles/{vehicle}/documents', [DocumentController::class, 'store'])->name('documents.store');