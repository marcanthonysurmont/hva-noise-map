<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewHomeController;
use App\Http\Controllers\NoiseLevelStoreController;

Route::get('/', ViewHomeController::class);

Route::post('api/store-noise-level', NoiseLevelStoreController::class);
