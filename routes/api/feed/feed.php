<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feed\FeedController;
use App\Http\Controllers\FeedUsage\FeedUsageController;

Route::controller(FeedController::class)->prefix('feeds')->group(function () {
     Route::get('restore/{id}', 'restore');
     Route::get('options', 'getOptions');
});

Route::controller(FeedUsageController::class)->prefix('feed-usages')->group(function () {
     Route::post('', 'store');
     Route::get('', 'index');
});

Route::apiResource('feeds', FeedController::class);