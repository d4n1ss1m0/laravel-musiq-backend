<?php

use App\Http\Controllers\Personal\PersonalController;

Route::prefix('personal')->middleware(\App\Http\Middleware\JwtAuthMiddleware::class)->group(function () {
    Route::get('/added', [PersonalController::class, 'getAdded']);

    include __DIR__ . '/YearReview/api.php';
});
