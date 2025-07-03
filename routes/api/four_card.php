<?php

use App\Http\Controllers\FourCardsController;
use Illuminate\Support\Facades\Route;

Route::get('/four-cards', [FourCardsController::class, 'get']);         // Get all
Route::post('/four-cards', [FourCardsController::class, 'set']);        // Set/Create
Route::post('/four-cards/{id}', [FourCardsController::class, 'upadate']);   // Update
