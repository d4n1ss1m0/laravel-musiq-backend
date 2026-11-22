<?php

use App\Http\Controllers\History\HistoryController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('history')
    ->middleware(JwtAuthMiddleware::class)
    ->group(function () {
        Route::post('/', [HistoryController::class, 'store']); // deprecated
        Route::post('/listen', [HistoryController::class, 'listen']);

        Route::get('/', [HistoryController::class, 'get']);
    });
