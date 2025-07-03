<?php

use App\Http\Controllers\gelleryController;
use Illuminate\Support\Facades\Route;

Route::controller(gelleryController::class)->group(function () {
    Route::get('/gellery/{id}', 'index');               // optional ?package_id=1
    Route::post('/gellery/{id}', 'store');              // multiple upload
    Route::delete('/gellery/{packageId}/{image_id}', 'deleteImageByPackageId');
    Route::post('/gellery/{package_id}/replace', 'replaceGalleryImages');
    // add more to same package
});
