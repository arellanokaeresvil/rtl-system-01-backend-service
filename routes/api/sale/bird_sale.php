<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\BirdSaleController;

Route::apiResource('bird-sales', BirdSaleController::class);