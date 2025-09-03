<?php

use App\Http\Controllers\PackagesController;
use Illuminate\Support\Facades\Route;

Route::prefix('packages')->group(function () {
    Route::get('/{sub_des_id}', [PackagesController::class, 'getPackage']);
    Route::get('/{packageId}/details', [PackagesController::class, 'getPackageDetails']);
    // **********************  with filter **********************
    Route::post('/{packageId}/details/filter', [PackagesController::class, 'filterPackages']);



    Route::post('/{sub_des_id}', [PackagesController::class, 'setPackage']);
    Route::delete('/delete/{package_id}', [PackagesController::class, 'deleteByPackageId']);
    Route::post('/update/{package_id}', [PackagesController::class, 'updatePackage']);
    Route::post('/limit/{subdesid}', [PackagesController::class, 'getPackageHomeLimit']);
});
// packages finding
Route::post('/filter/homepage', [PackagesController::class, 'check']);  // current it is no used in future  i am not use it

Route::get('/top/filter', [PackagesController::class, 'searchPackages']);
Route::get('/pac/places', [PackagesController::class, 'getAllPlaces']);
