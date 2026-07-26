<?php

use App\Http\Controllers\Mediateka\MediatekaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('mediateka')
    ->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)
    ->group(function () {
        Route::get('/', [MediatekaController::class, 'getMediateka']);
    });

