<?php

namespace App\Infrastructure\Services\Auth\JwtService;


use App\Domain\Models\Auth\User;

interface JwtServiceInterface
{
    public function generateAccessToken(User $user): string;
    public function generateRefreshToken(User $user): string;
    public function validateRefreshToken(string $token): ?User;
    public function invalidateRefreshToken(string $token): void;
}
