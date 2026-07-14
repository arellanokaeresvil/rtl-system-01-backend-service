<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\ReportController;

Route::controller(ReportController::class)->prefix('reports')->group(function () {
     Route::get('summary', 'summary');
     Route::get('generated', 'generated');
     Route::get('generated/{id}', 'generatedDetails');
});