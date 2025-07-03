<?php

use App\Http\Controllers\DateOfTourController;
use Illuminate\Support\Facades\Route;

Route::prefix('dateOfTour')->group(function () {
    // Route::post('/{monthTourId}', [DateOfTourController::class, 'setDateTour']); // Create date
    Route::get('/{monthTourId}', [DateOfTourController::class, 'getDateTours']);  // Get all dates
});
Route::post('dateOfTour/a', [DateOfTourController::class, 'setDateTour']); // Create date
Route::post('dateOfTour/multipleupdate', [DateOfTourController::class, 'updateDateTours']); // Create date
