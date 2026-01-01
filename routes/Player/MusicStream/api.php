<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/stream/{id}', [\App\Http\Controllers\Player\MusicStreamController::class, 'stream'])->middleware(\App\Http\Middleware\OptJwtMiddleware::class);
