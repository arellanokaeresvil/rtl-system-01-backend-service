<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Egg\EggController;

Route::controller(EggController::class)->prefix('eggs')->group(function () {
     Route::get('restore/{id}', 'restore');
});

Route::apiResource('eggs', EggController::class);