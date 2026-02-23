<?php

use App\Http\Controllers\Sales\EggSaleController;
use Illuminate\Support\Facades\Route;

Route::apiResource('egg-sales', EggSaleController::class);