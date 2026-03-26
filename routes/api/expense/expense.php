<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Expense\ExpenseController;

Route::controller(ExpenseController::class)->prefix('expenses')->group(function () {
     Route::get('restore/{id}', 'restore');
     Route::get('summary', 'summary');
});

Route::apiResource('expenses', ExpenseController::class);