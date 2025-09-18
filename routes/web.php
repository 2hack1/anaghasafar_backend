<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// Catch-all route for Angular frontend
Route::get('/{any}', function () {
    return File::get(public_path('index.html')); // Angular build file
})->where('any', '^(?!api).*'); // Exclude API routes


