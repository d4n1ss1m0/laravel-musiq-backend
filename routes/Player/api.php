<?php

use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/MusicStream/api.php';

Route::get('/tracks', [\App\Http\Controllers\Track\TrackController::class, 'getTracks']);















// // api/v1
// Route::group(['prefex' => 'v2', 'namespace' => 'App\Http\Controllers\Api\V2'], function() {
//     Route::apiResource('/customers', CustomerController::class);
//     Route::apiResource('/invoices', InvoiceController::class);
// });

