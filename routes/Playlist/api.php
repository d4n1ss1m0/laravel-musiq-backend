<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('playlist')->middleware('auth:sanctum')->group(function () {
    Route::get('{id}', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getPlaylist']);
    Route::get('{id}/tracks', [\App\Http\Controllers\Playlist\PlaylistController::class, 'getTracks']);
});

















// // api/v1
// Route::group(['prefex' => 'v2', 'namespace' => 'App\Http\Controllers\Api\V2'], function() {
//     Route::apiResource('/customers', CustomerController::class);
//     Route::apiResource('/invoices', InvoiceController::class);
// });

