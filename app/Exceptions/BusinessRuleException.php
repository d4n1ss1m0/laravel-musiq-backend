<?php

namespace App\Exceptions;

class BusinessRuleException extends ApiException
{
    public function __construct(
        string $message = 'Business rule exception',
        mixed $data = null,
    ) {
        parent::__construct(
            message: $message,
            httpCode: 422,
            status: 'error',
            data: $data,
        );
    }
}
