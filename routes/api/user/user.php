<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->prefix('users')->group(function(){
    Route::get('restore/{id}', 'restore');
    Route::get('/options', 'getOptions');
});

Route::apiResource('users', UserController::class);