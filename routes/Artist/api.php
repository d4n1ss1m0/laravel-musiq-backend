<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('artist')->middleware(\App\Http\Middleware\OptJwtMiddleware::class)->group(function () {
    Route::get('{id}', [\App\Http\Controllers\Artist\ArtistController::class, 'getArtist']);
});


