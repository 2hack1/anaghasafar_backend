<?php

use App\Http\Controllers\DateOfTourController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ItinariesController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\PacImageController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\Sub_DestinationController;
use App\Http\Controllers\TopBarImageController;
use App\Http\Controllers\TransportsController;

Route::get('/', function () {
    $name = "AYush";
    dd($name);
});

Route::get('/greeting', function () {
    return response()->json(['message' => 'Hello from Laravel 12!']);
});

Route::prefix('destination')->group(function () {
    Route::get('/{dest_id}', [DestinationController::class, 'getDestinations']);
    Route::post('/', [DestinationController::class, 'setDestination']);
});
Route::prefix('subdestination')->group(function () {
    Route::get('/', [Sub_DestinationController::class, 'index']); // GET all
    Route::get('/{sub_destinationId}', [Sub_DestinationController::class, 'show']);
});

Route::prefix('itineraries')->group(function () {

    Route::get('/{packageId}', [ItinariesController::class, 'getItineraries']);
    Route::post('/{packageId}', [ItinariesController::class, 'setItinerary']);
});


Route::prefix('transports')->group(function () {
    Route::get('/{packageId}', [TransportsController::class, 'getTransports']);
    Route::post('/{packageId}', [TransportsController::class, 'setTransport']);
});


Route::prefix('months')->group(function () {
    Route::get('/{packageId}', [MonthController::class, 'getMonthTours']);
    Route::post('/{packageId}', [MonthController::class, 'setMonthTour']);
});
Route::prefix('dateOfTour')->group(function () {
    Route::post('/{monthTourId}', [DateOfTourController::class, 'setDateTour']); // Create date
    Route::get('/{monthTourId}', [DateOfTourController::class, 'getDateTours']);  // Get all dates
});

Route::prefix('pac_image')->group(function () {
    Route::get('/{packageId}', [PacImageController::class, 'getPackageImages']);
    Route::post('/{packageId}', [PacImageController::class, 'setPackageImage']);
});


Route::post('/topimg', [TopBarImageController::class, 'top']);
Route::get('/topimagess', [TopBarImageController::class, 'getImages']);


// ************************************************ done
Route::prefix('packages')->group(function () {
    Route::get('/{sub_des_id}', [PackagesController::class, 'getPackage']);
    Route::post('/{sub_des_id}', [PackagesController::class, 'setPackage']);
});
