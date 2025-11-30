<?php

namespace App\Application\UseCase\Auth\RefreshToken;

use App\Infrastructure\Services\Auth\JwtService\JwtServiceInterface;

class RefreshToken implements RefreshTokenInterface
{

    public function __construct(private readonly JwtServiceInterface $jwtService)
    {
    }

    public function handle(string $token)
    {
        $user = $this->jwtService->validateRefreshToken($token);

        if(!$user) { throw new \Exception('Invalid refresh token'); }

        $this->jwtService->invalidateRefreshToken($token);

        return [
            'refreshToken' => $this->jwtService->generateRefreshToken($user),
            'accessToken' => $this->jwtService->generateAccessToken($user)
        ];
    }
}
