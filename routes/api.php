<?php

use App\Http\Controllers\DateOfTourController;

use App\Http\Controllers\SubDestinationController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $name = "AYush";
    dd($name);
});

Route::get('/greeting', function () {
    return response()->json(['message' => 'Hello from Laravel 12!']);
});

// Route::post('/dest', function (Request $request) {
//     // Validate input
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'type' => 'required|in:inbound,outbound',
//     ]);

//     // Create destination
//     DestinationModel::create($validated);
//     // Fetch all destinations
//     $data = DestinationModel::all();
//     // Debug or return JSON response
//     // dd($data); // for debugging
//     return response()->json($data);
// });
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ItirariesController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\TopBarImageController;
use App\Http\Controllers\TransportsController;

Route::post('/dest', [DestinationController::class, 'UploadDes']);

// sub-destinations for any countries
Route::post('/upload', [SubDestinationController::class, 'upload']);

Route::post('/topimg', [TopBarImageController::class, 'top']);
Route::get('/topimagess', [TopBarImageController::class, 'getImages']);
// packages currently not run 
Route::post('/packages', [PackagesController::class, 'store']);
Route::get('/packages', [PackagesController::class, 'index']);
// ************************************ *********************************************************
// itinariries
Route::post('/itineraries', [ItirariesController::class, 'store']);
Route::get('/itineraries', [ItirariesController::class, 'index']);
// Route::get('/itineraries/{id}', [ItirariesController::class, 'show']);

// Datetours
// Route::post('/tour-dates', [DateTourController::class, 'store']);

Route::post('/monthtour', [MonthController::class, 'store']); // Create
Route::get('/monthtour', [MonthController::class, 'index']);



// DateOfTour
// Route::post("/dateOfTour",[DateOfTourController::class,'store']);
// Route::get('/dateOfTour',[DateOfTourController::class,'index']);

Route::post('/dateOfTour', [DateOfTourController::class, 'store']); // Create date
Route::get('/dateOfTour', [DateOfTourController::class, 'index']);  // Get all dates

// transport
Route::get('/transports', [TransportsController::class, 'index']);
Route::post('/transports', [TransportsController::class, 'store']);
