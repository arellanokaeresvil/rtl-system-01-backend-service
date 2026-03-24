<?php

use App\Http\Controllers\Sales\SalesController;
use Illuminate\Support\Facades\Route;

Route::controller(SalesController::class)->prefix('sales')->group(function () {
     Route::get('summary', 'summary');
     Route::get('records', 'records');
     Route::post('egg', 'store_egg_sale');
     Route::post('bird', 'store_bird_sale');
});
