<?php

use App\Http\Controllers\TransportsController;
use Illuminate\Support\Facades\Route;

Route::prefix('transports')->group(function () {
    Route::get('/{packageId}', [TransportsController::class, 'getTransports']);
    Route::post('/{packageId}', [TransportsController::class, 'setTransport']);
    Route::post('/update/{packageId}', [TransportsController::class, 'updateTransport']);
});
