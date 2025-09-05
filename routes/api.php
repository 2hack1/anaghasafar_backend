<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HoltelBookingController;
use App\Http\Controllers\HotelRoomsController;
use App\Http\Controllers\HotelVender;
use App\Http\Controllers\orderController;
use App\Http\Controllers\UserController;

require base_path("/routes/api/destinaions.php");
require base_path("/routes/api/subdestination.php");
require base_path("/routes/api/itineries.php");
require base_path("/routes/api/transports.php");
require base_path("/routes/api/month.php");
require base_path("/routes/api/dates.php");
require base_path("/routes/api/package_main_image.php");
require base_path("/routes/api/topSlider.php");
require base_path("/routes/api/packages.php");
// user***************************************

require base_path("/routes/api/userlogin.php");
require base_path("/routes/api/four_card.php");
// make your own trip
require base_path("/routes/api/make_my_trip.php");
require base_path("/routes/api/gallery.php");
Route::post('/send-mail', [EmailController::class, 'sendMail']);
Route::post('/order-send-mail', [EmailController::class, 'orderEmail' ]);
Route::post('/order', [orderController:: class, 'set']);
Route::get('/order', [orderController :: class , 'get']);
Route::get('/orderbyid/{id}', [orderController :: class , 'getByuserId']);
Route::delete('/delete_order/{id}',[orderController :: class,'deleteOrderById']);


// hotel vendor
Route::post('vendor/register', [HotelVender::class, 'register']);
Route::post('vendor/login', [HotelVender::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('vendors', [HotelVender::class, 'index']);
    Route::get('vendor/{id}', [HotelVender::class, 'show']);
    Route::post('vendor', [HotelVender::class, 'store']);
    Route::put('vendor/{id}', [HotelVender::class, 'update']);
    Route::delete('vendor/{id}', [HotelVender::class, 'destroy']);
});

Route::get('/hotel-rooms', [HotelRoomsController::class, 'index']);    // on used 
Route::get('/hotels/{hotelId}/rooms/{roomId}', [HotelRoomsController::class, 'show']);  //  used 

Route::post('/hotel-rooms', [HotelRoomsController::class, 'store']);  //  used
Route::post('/hotel-rooms/{id}', [HotelRoomsController::class, 'update']);  //  used 

Route::delete('/hotel-rooms/{id}', [HotelRoomsController::class, 'destroy']);  //  used
Route::get('/hotel-available-rooms-exact', [HotelRoomsController::class, 'exectFindingRooms']);   // used  
Route::get('/hotel-available-rooms-price', [HotelRoomsController::class, 'combo']);     //  used




// Booking CRUD + Extra Functions
Route::prefix('bookings')->group(function () {

    Route::get('/', [HoltelBookingController::class, 'index']);      //currently not  used
    Route::post('/', [HoltelBookingController::class, 'store']);   //currently not  used
    Route::get('/vendor/{vendorId}', [HoltelBookingController::class, 'bookingsByVendor']); //currently not  used
    Route::get('/user/{userId}', [HoltelBookingController::class, 'bookingsByUser']); //currently not  used
    Route::post('/check-availability', [HoltelBookingController::class, 'checkAvailability']);
    Route::put('/{id}', [HoltelBookingController::class, 'update']); // not  used
    Route::patch('/{id}/cancel', [HoltelBookingController::class, 'cancel']);  //currently not  used
    Route::delete('/{id}', [HoltelBookingController::class, 'destroy']);  //currently not  used

});

Route::get('/users/{id}', [UserController::class, 'show']);