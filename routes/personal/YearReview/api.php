<?php

use App\Http\Controllers\YearReview\YearReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('year-reviews')->group(function () {
    Route::get('/', [YearReviewController::class, 'getReviews']);
});

Route::prefix('year-review')->group(function () {
    Route::get('/new', [YearReviewController::class, 'getNewReview']);

    Route::get('/{year}', [YearReviewController::class, 'getReviewByYear'])->where('year', '[0-9]{4}');
});
