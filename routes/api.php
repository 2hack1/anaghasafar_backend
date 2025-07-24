<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HotelVender;
use App\Http\Controllers\orderController;



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

