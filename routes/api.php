<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DateOfTourController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FourCardsController;
use App\Http\Controllers\gelleryController;
use App\Http\Controllers\ItinariesController;
use App\Http\Controllers\MakeTripController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\PacImageController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\Sub_DestinationController;
use App\Http\Controllers\TopBarImageController;
use App\Http\Controllers\TransportsController;
use App\Http\Controllers\UserController;
use App\Models\gelleryModel;

Route::prefix('destination')->group(function () {
    Route::post('/', [DestinationController::class, 'setDestination']);
    Route::get('/{dest_id}', [DestinationController::class, 'getDestinations']); //get_subdestinations
    Route::get('/{dest_id}/limit', [DestinationController::class, 'getwithLimit']); //get_subdestinations
    Route::get('/all/des', [DestinationController::class, 'getsingle']); //get_subdestinations

});
Route::put('/destination/update/{id}', [DestinationController::class, 'update']);
Route::delete('/destination/delete/{id}', [DestinationController::class, 'destroy']);


Route::prefix('subdestination')->group(function () {
    Route::get('/', [Sub_DestinationController::class, 'index']); // GET all
    Route::get('/{sub_destinationId}', [Sub_DestinationController::class, 'show']); //getPackages
    Route::post('/{sub_destinationId}', [Sub_DestinationController::class, 'store']);
});
Route::put('/ssubdestination/update/{id}', [Sub_DestinationController::class, 'update']);
Route::delete('/ssubdestination/delete/{sub_destination_id}', [Sub_DestinationController::class, 'destroy']);

Route::prefix('itineraries')->group(function () {

    Route::get('/{packageId}', [ItinariesController::class, 'getItineraries']);
    Route::post('/{packageId}', [ItinariesController::class, 'setItinerary']);
    Route::delete('/{packageId}', [ItinariesController::class, 'deleteItinerary']);
    Route::post('update/{packageId}', [ItinariesController::class, 'updateItineraries']);
});


Route::prefix('transports')->group(function () {
    Route::get('/{packageId}', [TransportsController::class, 'getTransports']);
    Route::post('/{packageId}', [TransportsController::class, 'setTransport']);
    Route::post('/update/{packageId}', [TransportsController::class, 'updateTransport']);
});


Route::prefix('months')->group(function () {
    Route::get('/{packageId}', [MonthController::class, 'getMonthTours']);
    Route::post('/{packageId}', [MonthController::class, 'setMonthTour']);
    Route::post('/update/multiple', [MonthController::class, 'updateMultipleMonthTour']);

    
});
Route::post('/set-multiple-months', [MonthController::class, 'setMultipleMonthTour']);

Route::prefix('dateOfTour')->group(function () {
    // Route::post('/{monthTourId}', [DateOfTourController::class, 'setDateTour']); // Create date
    Route::get('/{monthTourId}', [DateOfTourController::class, 'getDateTours']);  // Get all dates
});
    Route::post('dateOfTour/a', [DateOfTourController::class, 'setDateTour']); // Create date
    Route::post('dateOfTour/multipleupdate', [DateOfTourController::class, 'updateDateTours']); // Create date
  


Route::prefix('pac_image')->group(function () {
    Route::get('/{packageId}', [PacImageController::class, 'getPackageImages']);
    Route::post('/{packageId}', [PacImageController::class, 'setPackageImage']);
    Route::post('update/{packageId}', [PacImageController::class, 'updatePackageImage']);
});
 

Route::post('/topimg', [TopBarImageController::class, 'top']);
Route::get('/topimagess', [TopBarImageController::class, 'getImages']);
Route::post('/topimg/update/{id}', [TopBarImageController::class, 'updateTop']);



Route::prefix('packages')->group(function () {
    Route::get('/{sub_des_id}', [PackagesController::class, 'getPackage']);
    Route::get('/{packageId}/details', [PackagesController::class, 'getPackageDetails']);
    // **********************  with filter **********************
    Route::post('/{packageId}/details/filter', [PackagesController::class, 'filterPackages']);
    Route::post('/{sub_des_id}', [PackagesController::class, 'setPackage']);
    Route::delete('/delete/{package_id}', [PackagesController::class, 'deleteByPackageId']);
    Route::post('/update/{package_id}', [PackagesController::class, 'updatePackage']);


});
// packages finding
Route::post('/filter/homepage', [PackagesController::class, 'check']);

// user***************************************

Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::post('/register-user', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);

Route::middleware('auth')->group(function () {
Route::post('/logout', [UserController::class, 'logout']);
Route::get('/user',    [UserController::class, 'user']);
});

Route::get('/four-cards', [FourCardsController::class, 'get']);         // Get all
Route::post('/four-cards', [FourCardsController::class, 'set']);        // Set/Create
Route::post('/four-cards/{id}', [FourCardsController::class, 'upadate']);   // Update

// make your own trip
Route::post('/trips', [MakeTripController::class, 'set']);
Route::get('/trips', [MakeTripController::class, 'get']);
Route::delete('/trips/{id}', [MakeTripController::class, 'deleted']);


Route::controller(gelleryController::class)->group(function () {
    Route::get('/gellery', 'index');               // optional ?package_id=1
    Route::post('/gellery', 'store');              // multiple upload
    Route::put('/gellery/{packageId}', 'update');  // add more to same package
});