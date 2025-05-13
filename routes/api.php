<?php

use App\Http\Controllers\DateOfTourController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ItinariesController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\PacImageController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\TopBarImageController;
use App\Http\Controllers\TransportsController;

Route::get('/', function () {
    $name = "AYush";
    dd($name);
});

Route::get('/greeting', function () {
    return response()->json(['message' => 'Hello from Laravel 12!']);
});


Route::get('/destination', [DestinationController::class, 'getDestinations']);
Route::post('/destination', [DestinationController::class, 'setDestination']);


Route::get('/subdestination', [DestinationController::class, 'getSubDestinations']);
Route::post('/subdestination', [DestinationController::class, 'setSubDestination']);

Route::get('/package/{packageId}/itineraries', [ItinariesController::class, 'getItineraries']);
Route::post('/package/{packageId}/itinerary', [ItinariesController::class, 'setItinerary']);

Route::get('/package/{packageId}/transports', [TransportsController::class, 'getTransports']);
Route::post('/package/{packageId}/transport', [TransportsController::class, 'setTransport']);

Route::get('/package/{packageId}/images', [PacImageController::class, 'getPackageImages']);
Route::post('/package/{packageId}/image', [PacImageController::class, 'setPackageImage']);

Route::post('/topimg', [TopBarImageController::class, 'top']);
Route::get('/topimagess', [TopBarImageController::class, 'getImages']);
// packages currently not run 

Route::get('/packages', [PackagesController::class, 'getPackages']);
Route::get('/package/{packageId}', [PackagesController::class, 'getPackage']);
Route::post('/package', [PackagesController::class, 'setPackage']);


Route::get('/package/{packageId}/months', [MonthController::class, 'getMonthTours']);
Route::post('/package/{packageId}/month', [MonthController::class, 'setMonthTour']);

// Route::post('/monthtour', [MonthController::class, 'store']); // Create
// Route::get('/monthtour', [MonthController::class, 'index']);

Route::post('/dateOfTour', [DateOfTourController::class, 'store']); // Create date
Route::get('/dateOfTour', [DateOfTourController::class, 'index']);  // Get all dates

