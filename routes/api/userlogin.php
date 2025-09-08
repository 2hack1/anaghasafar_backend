<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::post('/register-user', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user',    [UserController::class, 'user']);
});

Route::post('/forget-pass', [UserController::class, 'generateAndSendOtp']);  //  check  email have 
Route::post('/verify-pass', [UserController::class, 'verifyOtp']);  // varify otp
Route::post('/pass-update', [UserController::class, 'updatePassword']);  //update password
