<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group([], function () {
    Route::post('chat', [ChatController::class, 'chat']);
    Route::post('clear-chat', [ChatController::class, 'clearHistory']);
});