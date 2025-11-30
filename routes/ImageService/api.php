<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/image/{name}', [\App\Http\Controllers\ImageService\ImageServiceController::class, 'getImage'])->where('name', '.*');


