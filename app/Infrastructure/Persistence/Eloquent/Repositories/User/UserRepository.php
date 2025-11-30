<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\User;

use App\Domain\Models\Auth\User;
use App\Domain\Repositories\User\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function findByEmail(string $email): ?User
    {
        $model = User::where('email', $email)->first();

        if(!$model) {
            return null;
        }

        return $model;
    }
}
