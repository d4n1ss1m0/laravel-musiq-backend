<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $httpCode = 500,
        public readonly string $status = 'error',
        public readonly mixed $data = null,
    ) {
        parent::__construct($message);
    }
}
