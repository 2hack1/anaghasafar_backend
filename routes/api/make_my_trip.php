<?php

use App\Http\Controllers\MakeTripController;
use Illuminate\Support\Facades\Route;

Route::post('/trips', [MakeTripController::class, 'set']);
Route::get('/trips', [MakeTripController::class, 'get']);
Route::delete('/trips/{id}', [MakeTripController::class, 'deleted']);
