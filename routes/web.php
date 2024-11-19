<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewHomeController;

Route::get('/', ViewHomeController::class);

