<?php

use App\Http\Controllers\TopBarImageController;
use Illuminate\Support\Facades\Route;

Route::post('/topimg', [TopBarImageController::class, 'top']);
Route::get('/topimagess', [TopBarImageController::class, 'getImages']);
Route::post('/topimg/update/{id}', [TopBarImageController::class, 'updateTop']);
