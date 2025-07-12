<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DateOfTourController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FourCardsController;
use App\Http\Controllers\gelleryController;
use App\Http\Controllers\ItinariesController;
use App\Http\Controllers\MakeTripController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\PacImageController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\Sub_DestinationController;
use App\Http\Controllers\TopBarImageController;
use App\Http\Controllers\TransportsController;
use App\Http\Controllers\UserController;

// Route::prefix('destination')->group(function () {
//     Route::post('/', [DestinationController::class, 'setDestination']);
//     Route::get('/{dest_id}', [DestinationController::class, 'getDestinations']); //get_subdestinations
//     Route::get('/{dest_id}/limit', [DestinationController::class, 'getwithLimit']); //get_subdestinations
//     Route::get('/all/des', [DestinationController::class, 'getsingle']); //get_subdestinations

// });


require base_path("/routes/api/destinaions.php");
// require __DIR__ . "/api/destinaions.php";

// Route::put('/destination/update/{id}', [DestinationController::class, 'update']);
// Route::delete('/destination/delete/{id}', [DestinationController::class, 'destroy']);

require base_path("/routes/api/subdestination.php");
// Route::prefix('subdestination')->group(function () {
//     Route::get('/', [Sub_DestinationController::class, 'index']); // GET all
//     Route::get('/{sub_destinationId}', [Sub_DestinationController::class, 'show']); //getPackages
//     Route::post('/{sub_destinationId}', [Sub_DestinationController::class, 'store']);
// });
// Route::put('/ssubdestination/update/{id}', [Sub_DestinationController::class, 'update']);

// Route::delete('/ssubdestination/delete/{sub_destination_id}', [Sub_DestinationController::class, 'destroy']);

require base_path("/routes/api/itineries.php");
// Route::prefix('itineraries')->group(function () {

//     Route::get('/{packageId}', [ItinariesController::class, 'getItineraries']);
//     Route::post('/{packageId}', [ItinariesController::class, 'setItinerary']);
//     Route::delete('/{packageId}', [ItinariesController::class, 'deleteItinerary']);
//     Route::post('update/{packageId}', [ItinariesController::class, 'updateItineraries']);
// });


require base_path("/routes/api/transports.php");
// Route::prefix('transports')->group(function () {
//     Route::get('/{packageId}', [TransportsController::class, 'getTransports']);
//     Route::post('/{packageId}', [TransportsController::class, 'setTransport']);
//     Route::post('/update/{packageId}', [TransportsController::class, 'updateTransport']);
// });

require base_path("/routes/api/month.php");
// Route::prefix('months')->group(function () {
//     Route::get('/{packageId}', [MonthController::class, 'getMonthTours']);
//     Route::post('/{packageId}', [MonthController::class, 'setMonthTour']);
//     Route::post('/update/multiple', [MonthController::class, 'updateMultipleMonthTour']);
// });
// Route::post('/set-multiple-months', [MonthController::class, 'setMultipleMonthTour']);

require base_path("/routes/api/dates.php");

// Route::prefix('dateOfTour')->group(function () {
//     // Route::post('/{monthTourId}', [DateOfTourController::class, 'setDateTour']); // Create date
//     Route::get('/{monthTourId}', [DateOfTourController::class, 'getDateTours']);  // Get all dates
// });
// Route::post('dateOfTour/a', [DateOfTourController::class, 'setDateTour']); // Create date
// Route::post('dateOfTour/multipleupdate', [DateOfTourController::class, 'updateDateTours']); // Create date


require base_path("/routes/api/package_main_image.php");

// Route::prefix('pac_image')->group(function () {
//     Route::get('/{packageId}', [PacImageController::class, 'getPackageImages']);
//     Route::post('/{packageId}', [PacImageController::class, 'setPackageImage']);
//     Route::post('update/{packageId}', [PacImageController::class, 'updatePackageImage']);
// });

require base_path("/routes/api/topSlider.php");
// Route::post('/topimg', [TopBarImageController::class, 'top']);
// Route::get('/topimagess', [TopBarImageController::class, 'getImages']);
// Route::post('/topimg/update/{id}', [TopBarImageController::class, 'updateTop']);


require base_path("/routes/api/packages.php");
// Route::prefix('packages')->group(function () {
//     Route::get('/{sub_des_id}', [PackagesController::class, 'getPackage']);
//     Route::get('/{packageId}/details', [PackagesController::class, 'getPackageDetails']);
//     // **********************  with filter **********************
//     Route::post('/{packageId}/details/filter', [PackagesController::class, 'filterPackages']);
//     Route::post('/{sub_des_id}', [PackagesController::class, 'setPackage']);
//     Route::delete('/delete/{package_id}', [PackagesController::class, 'deleteByPackageId']);
//     Route::post('/update/{package_id}', [PackagesController::class, 'updatePackage']);
//     Route::post('/limit/{subdesid}', [PackagesController::class, 'getPackageHomeLimit']);
// });
// // packages finding
// Route::post('/filter/homepage', [PackagesController::class, 'check']);

// user***************************************


require base_path("/routes/api/userlogin.php");
// Route::post('/users', [UserController::class, 'store']);
// Route::put('/users/{id}', [UserController::class, 'update']);
// Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Route::post('/register-user', [UserController::class, 'register']);
// Route::post('/login',    [UserController::class, 'login']);

// Route::middleware('auth')->group(function () {
//     Route::post('/logout', [UserController::class, 'logout']);
//     Route::get('/user',    [UserController::class, 'user']);
// });

require base_path("/routes/api/four_card.php");
// Route::get('/four-cards', [FourCardsController::class, 'get']);         // Get all
// Route::post('/four-cards', [FourCardsController::class, 'set']);        // Set/Create
// Route::post('/four-cards/{id}', [FourCardsController::class, 'upadate']);   // Update

// make your own trip

require base_path("/routes/api/make_my_trip.php");

// Route::post('/trips', [MakeTripController::class, 'set']);
// Route::get('/trips', [MakeTripController::class, 'get']);
// Route::delete('/trips/{id}', [MakeTripController::class, 'deleted']);

require base_path("/routes/api/gallery.php");

// Route::controller(gelleryController::class)->group(function () {
//     Route::get('/gellery/{id}', 'index');               // optional ?package_id=1
//     Route::post('/gellery/{id}', 'store');              // multiple upload
//     Route::delete('/gellery/{packageId}/{image_id}', 'deleteImageByPackageId');
//     Route::post('/gellery/{package_id}/replace', 'replaceGalleryImages');
//     // add more to same package
// });

Route::post('/send-mail', [EmailController::class, 'sendMail']);
Route:: post('/order', [orderController:: class, 'set']);
Route :: get('/order', [orderController :: class , 'get']);
Route :: get('/orderbyid/{id}', [orderController :: class , 'getByuserId']);