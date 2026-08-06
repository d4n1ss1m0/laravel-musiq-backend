<?php

use App\Enum\MediatekaItemType;
use App\Http\Controllers\Mediateka\MediatekaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('mediateka')
    ->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)
    ->group(function () {
        Route::get('/', [MediatekaController::class, 'getMediateka']);

        Route::prefix('{type}')
            ->whereIn('type', array_column(MediatekaItemType::cases(), 'value'))
            ->group(function () {
                Route::patch('/pin', [MediatekaController::class, 'pinItem']);
                Route::patch('/unpin', [MediatekaController::class, 'unpinItem']);
            });

        Route::prefix('{type}')
            ->whereIn('type', [MediatekaItemType::ARTIST->value])
            ->group(function () {
                Route::post('/add', [MediatekaController::class, 'addMedia']);
                Route::delete('/remove', [MediatekaController::class, 'removeMedia']);
            });
    });

