<?php

use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('playback')
    ->middleware([JwtAuthMiddleware::class])
    ->group(function () {
        Route::post('snapshot', [\App\Http\Controllers\Playback\PlaybackController::class, 'snapshot']);
    });

