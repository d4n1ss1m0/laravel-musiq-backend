<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('playlist')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::get('{id}', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getPlaylist']);
    Route::get('{id}/tracks', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getTracks']);
    Route::post('create', [\App\Http\Controllers\Playlist\PlaylistController::class, 'create']);
});

