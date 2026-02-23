<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mortality\MortalityController;

Route::apiResource('mortalities', MortalityController::class);