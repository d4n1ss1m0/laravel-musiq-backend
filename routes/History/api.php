<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\History\HistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('history')->group(function () {
    Route::post('/', [HistoryController::class, 'store']);
});
