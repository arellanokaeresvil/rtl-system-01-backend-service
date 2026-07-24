<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Egg\EggController;

Route::controller(EggController::class)->prefix('eggs')->group(function () {
     Route::get('restore/{id}', 'restore');
     Route::post('per-tray', 'storePerTray');
     Route::post('customize', 'storeCustomize');
     Route::get('profitability', 'loadProfitabilityEggs');
     Route::get('weekly-production', 'weeklyEggProduction');
});

Route::apiResource('eggs', EggController::class);