<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseCategory\ExpenseCategoryController;

Route::controller(ExpenseCategoryController::class)->prefix('expense-categories')->group(function () {
     Route::get('restore/{id}', 'restore');
     Route::get('options', 'getOptions');
});

Route::apiResource('expense-categories', ExpenseCategoryController::class);