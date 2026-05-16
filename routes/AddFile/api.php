<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('add-track')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::post('/', [\App\Http\Controllers\AddTrack\AddTrackController::class, 'addTrackByFile']);
    Route::prefix('parse')->group(function () {
        Route::post('/', [\App\Http\Controllers\AddTrack\AddTrackController::class, 'parseFromLink']);
        Route::post('/after-parse', [\App\Http\Controllers\AddTrack\AddTrackController::class, 'addAfterParse']);
    });
    //Route::get('{id}/tracks', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getTracks']);
});

















// // api/v1
// Route::group(['prefex' => 'v2', 'namespace' => 'App\Http\Controllers\Api\V2'], function() {
//     Route::apiResource('/customers', CustomerController::class);
//     Route::apiResource('/invoices', InvoiceController::class);
// });

