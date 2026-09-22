<?php

namespace App\Shared\Traits;

use App\Shared\Support\ApiResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HttpResponse
{
    public function success($value, $status = 'success', $code = 200) {
        return ApiResponse::success($value, $status, $code);
    }

    public function error($value, $status = 'error', $code = 500) {
        return ApiResponse::error($value, $status, $code);
    }

    public function musicStream(string $filePath, callable $callback, int $fileSize, ?int $start, ?int $end):StreamedResponse
    {
        return ApiResponse::musicStream($filePath, $callback, $fileSize, $start, $end);
    }

    public function paginator(array $keyValueArray, int $total, int $perPage, int $currentPage) : array
    {
        return ApiResponse::paginator($keyValueArray, $total, $perPage, $currentPage);
    }
}
