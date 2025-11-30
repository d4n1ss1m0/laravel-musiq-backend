<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('artist')->middleware('auth:sanctum')->group(function () {
    Route::get('{id}', [\App\Http\Controllers\Artist\ArtistController::class, 'getArtist']);
});

















// // api/v1
// Route::group(['prefex' => 'v2', 'namespace' => 'App\Http\Controllers\Api\V2'], function() {
//     Route::apiResource('/customers', CustomerController::class);
//     Route::apiResource('/invoices', InvoiceController::class);
// });

