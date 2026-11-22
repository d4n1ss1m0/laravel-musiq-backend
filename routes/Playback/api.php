<?php

use App\Http\Controllers\Playback\PlaybackController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;


Route::prefix('playback')
    ->middleware([JwtAuthMiddleware::class])
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\Playback\PlaybackController::class, 'queue']);
        Route::post('snapshot', [\App\Http\Controllers\Playback\PlaybackController::class, 'snapshot']);

        //queue modes
        Route::patch('shuffle', [\App\Http\Controllers\Playback\PlaybackController::class, 'shuffle']);
        Route::patch('repeat', [\App\Http\Controllers\Playback\PlaybackController::class, 'repeat']);

        //queue actions
        Route::post('next', [\App\Http\Controllers\Playback\PlaybackController::class, 'next']);
        Route::post('prev', [\App\Http\Controllers\Playback\PlaybackController::class, 'prev']);
        Route::post('play', [\App\Http\Controllers\Playback\PlaybackController::class, 'play']);
        Route::post('pause', [\App\Http\Controllers\Playback\PlaybackController::class, 'pause']);

        //queue tracks
        Route::post('add', [PlaybackController::class, 'add']);
        Route::post('move', [PlaybackController::class, 'move']);
    });

