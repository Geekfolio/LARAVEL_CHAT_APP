<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('chat');
});

// Add this test route
Route::get('/test', function () {
    return 'Test route works!';
});