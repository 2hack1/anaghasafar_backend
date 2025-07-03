<?php

use App\Http\Controllers\ItinariesController;
use Illuminate\Support\Facades\Route;

Route::prefix('itineraries')->group(function () {

    Route::get('/{packageId}', [ItinariesController::class, 'getItineraries']);
    Route::post('/{packageId}', [ItinariesController::class, 'setItinerary']);
    Route::delete('/{packageId}', [ItinariesController::class, 'deleteItinerary']);
    Route::post('update/{packageId}', [ItinariesController::class, 'updateItineraries']);
});
