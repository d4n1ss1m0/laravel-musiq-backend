<?php

namespace App\Application\UseCase\Auth\RefreshToken;

interface RefreshTokenInterface
{
    public function handle(string $token);
}
