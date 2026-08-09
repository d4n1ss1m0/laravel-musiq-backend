<?php

use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('playback')
    ->middleware([JwtAuthMiddleware::class])
    ->group(function () {
        Route::post('snapshot', [\App\Http\Controllers\Playback\PlaybackController::class, 'snapshot']);
        Route::patch('shuffle', [\App\Http\Controllers\Playback\PlaybackController::class, 'shuffle']);
        Route::patch('next', [\App\Http\Controllers\Playback\PlaybackController::class, 'next']);
        Route::patch('prev', [\App\Http\Controllers\Playback\PlaybackController::class, 'prev']);
        Route::patch('repeat', [\App\Http\Controllers\Playback\PlaybackController::class, 'repeat']);
        Route::patch('play', [\App\Http\Controllers\Playback\PlaybackController::class, 'play']);
        Route::patch('pause', [\App\Http\Controllers\Playback\PlaybackController::class, 'pause']);
    });

