<?php

use App\Http\Controllers\Playlist\PlaylistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('playlist')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::prefix('favourite')
        ->group(function () {
            Route::post('/add', [PlaylistController::class, 'addFavourite']);
            Route::delete('/remove', [PlaylistController::class, 'removeFavourite']);
        });

    Route::prefix('{uuid}')
        ->middleware([\App\Http\Middleware\IsPlaylistExistsMiddleware::class])
        ->group(function () {
            Route::middleware([\App\Http\Middleware\CanViewPlaylistMiddleware::class])->group(function () {
                //playlist
                Route::get('/', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getPlaylist']);

                //tracks
                Route::get('/tracks', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getTracks']);
                Route::get('/queue', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getQueue']);
            });

            Route::middleware([\App\Http\Middleware\CanManagePlaylistMiddleware::class])->group(function () {
                //playlist
                Route::patch('/', [\App\Http\Controllers\Playlist\PlaylistController::class, 'update']);
                Route::delete('/', [\App\Http\Controllers\Playlist\PlaylistController::class, 'delete']);
                Route::post('/import-from-playlist', [\App\Http\Controllers\Playlist\PlaylistController::class, 'importFromPlaylist']);

                //tracks
                Route::post('/add', [\App\Http\Controllers\Playlist\PlaylistController::class, 'addTrack']);
                Route::delete('/remove', [\App\Http\Controllers\Playlist\PlaylistController::class, 'removeTrack']);
                Route::put('/order', [\App\Http\Controllers\Playlist\PlaylistController::class, 'order']);
            });
        });
    Route::post('create', [\App\Http\Controllers\Playlist\PlaylistController::class, 'create']);
});

