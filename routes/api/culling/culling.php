<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Culling\CullingController;

Route::apiResource('cullings', CullingController::class);
