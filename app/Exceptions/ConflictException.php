<?php

namespace App\Exceptions;

use Exception;

class ConflictException extends ApiException
{
    public function __construct(
        string $message = 'Conflict',
        mixed $data = null,
    ) {
        parent::__construct(
            message: $message,
            httpCode: 409,
            status: 'error',
            data: $data,
        );
    }
}
