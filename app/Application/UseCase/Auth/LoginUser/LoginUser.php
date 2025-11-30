<?php

namespace App\Application\UseCase\Auth\LoginUser;

use App\Domain\Entities\User;
use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\User\UserRepository;
use App\Infrastructure\Services\Auth\JwtService\JwtServiceInterface;

class LoginUser implements LoginUserInterface
{

    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly JwtServiceInterface $jwtService)
    {
    }

    public function handle(string $email, string $password): array
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
}
