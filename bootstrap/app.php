<?php

use App\Exceptions\ApiException;
use App\Shared\Support\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            ValidationException $exception
        ) {
            return ApiResponse::error(
                $exception->errors(),
                'error',
                422
            );
        });

        $exceptions->render(function (
            ApiException $exception
        ) {
            return ApiResponse::error(
                $exception->getMessage(),
                'error',
                $exception->httpCode,
            );
        });

        // Любая необработанная ошибка
        $exceptions->render(function (
            Throwable $exception
        ) {
            return ApiResponse::error(
                'Unexpected error',
                'error',
                500
            );
        });
    })->create();
