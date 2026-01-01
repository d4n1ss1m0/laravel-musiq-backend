<?php

namespace App\Service\AuthService;

use App\Service\JwtService\JwtService;
use App\Repositories\User\UserRepositoryInterface;

class AuthService implements AuthServiceInterface
{

    public function __construct(private readonly JwtService $jwtService, private readonly UserRepositoryInterface $userRepository) {}

    function auth(string $email, string $password): array
    {
        $model = $this->userRepository->findByEmail($email);

        if (!$model || !$model->checkPassword($password)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'refreshToken' => $this->jwtService->generateRefreshToken($model),
            'accessToken' => $this->jwtService->generateAccessToken($model)
        ];
    }

    function refresh(string $token)
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
