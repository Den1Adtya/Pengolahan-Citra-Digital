<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/studio', function () {
    return view('studio');
});

Route::get('/about', function () {
    return view('about');
});