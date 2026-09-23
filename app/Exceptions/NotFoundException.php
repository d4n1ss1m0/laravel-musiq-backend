<?php

namespace App\Exceptions;

class NotFoundException extends ApiException
{
    public function __construct(
        string $message = 'Not Found',
        mixed $data = null,
    ) {
        parent::__construct(
            message: $message,
            httpCode: 404,
            status: 'error',
            data: $data,
        );
    }
}
