<?php

use App\Http\Controllers\DestinationController;
use Illuminate\Support\Facades\Route;

Route::prefix('destination')->group(function () {
    Route::post('/', [DestinationController::class, 'setDestination']);
    Route::get('/{dest_id}', [DestinationController::class, 'getDestinations']); //get_subdestinations
    Route::get('/{dest_id}/limit', [DestinationController::class, 'getwithLimit']); //get_subdestinations
    Route::get('/all/des', [DestinationController::class, 'getsingle']); //get_subdestinations
});
Route::put('/destination/update/{id}', [DestinationController::class, 'update']);
Route::delete('/destination/delete/{id}', [DestinationController::class, 'destroy']);