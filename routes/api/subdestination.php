<?php

use App\Http\Controllers\Sub_DestinationController;
use Illuminate\Support\Facades\Route;

Route::prefix('subdestination')->group(function () {
    Route::get('/', [Sub_DestinationController::class, 'index']); // GET all
    Route::get('/{sub_destinationId}', [Sub_DestinationController::class, 'show']); //getPackages
    Route::post('/{sub_destinationId}', [Sub_DestinationController::class, 'store']);
});
Route::put('/ssubdestination/update/{id}', [Sub_DestinationController::class, 'update']);
Route::delete('/ssubdestination/delete/{sub_destination_id}', [Sub_DestinationController::class, 'destroy']);