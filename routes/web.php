<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewHomeController;
use App\Http\Controllers\NoiseLevelStoreController;
use App\Http\Controllers\GetNoiseLevelDataController;

Route::get('/', ViewHomeController::class);

Route::post('api/store-noise-level', NoiseLevelStoreController::class);

Route::get('api/noise-levels/data', GetNoiseLevelDataController::class);
