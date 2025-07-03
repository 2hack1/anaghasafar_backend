<?php

use App\Http\Controllers\MonthController;
use Illuminate\Support\Facades\Route;

Route::prefix('months')->group(function () {
    Route::get('/{packageId}', [MonthController::class, 'getMonthTours']);
    Route::post('/{packageId}', [MonthController::class, 'setMonthTour']);
    Route::post('/update/multiple', [MonthController::class, 'updateMultipleMonthTour']);
});
Route::post('/set-multiple-months', [MonthController::class, 'setMultipleMonthTour']);
