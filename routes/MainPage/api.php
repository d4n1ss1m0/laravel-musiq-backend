<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('mainPage')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::get('/recentlyPlayedTracks', [\App\Http\Controllers\MainPage\MainPageController::class, 'getRecentlyPlayedTracks']);
    Route::get('/recentlyPlayedPlaylists', [\App\Http\Controllers\MainPage\MainPageController::class, 'getRecentlyPlayedPlaylists']);
    Route::get('/recentlyAddedTracks', [\App\Http\Controllers\MainPage\MainPageController::class, 'getRecentlyAddedTracks']);
});

Route::prefix('main-page')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::get('/recently-played-tracks', [\App\Http\Controllers\MainPage\MainPageController::class, 'getRecentlyPlayedTracks']);
    Route::get('/recently-played-playlists', [\App\Http\Controllers\MainPage\MainPageController::class, 'getRecentlyPlayedPlaylists']);
    Route::get('/recently-added-tracks', [\App\Http\Controllers\MainPage\MainPageController::class, 'getRecentlyAddedTracks']);
});


