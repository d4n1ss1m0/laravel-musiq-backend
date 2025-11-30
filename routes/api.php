<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

include __DIR__ . '/Auth/api.php';
include __DIR__ . '/Player/api.php';
include __DIR__ . '/ImageService/api.php';
include __DIR__ . '/MainPage/api.php';
include __DIR__ . '/Playlist/api.php';
include __DIR__ . '/Artist/api.php';
include __DIR__ . '/Search/api.php';
include __DIR__ . '/AddFile/api.php';

