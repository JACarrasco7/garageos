<?php

use Illuminate\Support\Facades\Route;

Route::get('/ayuda', function () {
    return Inertia\Inertia::render('Help/Index');
})->name('help.index');

Route::get('/ayuda/{topic}', function ($topic) {
    return Inertia\Inertia::render('Help/Show', ['topic' => $topic]);
})->name('help.show');
