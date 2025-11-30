<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/stream/{id}', [\App\Http\Controllers\Player\MusicStreamController::class, 'stream']);
















// // api/v1
// Route::group(['prefex' => 'v2', 'namespace' => 'App\Http\Controllers\Api\V2'], function() {
//     Route::apiResource('/customers', CustomerController::class);
//     Route::apiResource('/invoices', InvoiceController::class);
// });

