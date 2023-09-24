<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sales', function() {
    var_dump(\App\Models\Sales::latest()->limit(10000)->get());
});