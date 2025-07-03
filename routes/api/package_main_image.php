<?php

use App\Http\Controllers\PacImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('pac_image')->group(function () {
    Route::get('/{packageId}', [PacImageController::class, 'getPackageImages']);
    Route::post('/{packageId}', [PacImageController::class, 'setPackageImage']);
    Route::post('update/{packageId}', [PacImageController::class, 'updatePackageImage']);
});