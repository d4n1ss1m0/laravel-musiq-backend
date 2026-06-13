<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('playlist')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::prefix('{id}')->group(function () {
        Route::get('/', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getPlaylist']);
        Route::get('/tracks', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getTracks']);
        Route::post('/add', [\App\Http\Controllers\Playlist\PlaylistController::class, 'addTrack']);
        Route::delete('/remove', [\App\Http\Controllers\Playlist\PlaylistController::class, 'removeTrack']);
        Route::put('/order', [\App\Http\Controllers\Playlist\PlaylistController::class, 'order']);
    });
    Route::post('create', [\App\Http\Controllers\Playlist\PlaylistController::class, 'create']);
});

