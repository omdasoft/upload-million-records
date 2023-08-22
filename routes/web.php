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
