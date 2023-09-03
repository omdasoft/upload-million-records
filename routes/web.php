<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', function() {
    return view('upload-file');
});

Route::get('/upload', [SalesController::class, 'create']);
Route::post('/upload', [SalesController::class, 'upload']);
Route::get('/batch', [SalesController::class, 'batch']);
Route::get('/batch/all', [SalesController::class, 'inProgressBatch']);