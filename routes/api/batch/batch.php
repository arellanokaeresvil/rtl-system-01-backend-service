<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Batch\BatchController;

Route::controller(BatchController::class)->prefix('batches')->group(function () {
    Route::get('options', 'getOptions');
    Route::get('restore/{id}', 'restore');

});

Route::apiResource('batches', BatchController::class);
