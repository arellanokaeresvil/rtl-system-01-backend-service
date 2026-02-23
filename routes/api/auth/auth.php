<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Container\Attributes\Auth;   
use App\Http\Controllers\Auth\AuthController;

Route::controller(AuthController::class)->group(function (){
    Route::post('/login', 'login');

    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/auth-user', 'authUser')->middleware('auth:sanctum');
});