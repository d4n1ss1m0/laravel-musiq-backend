<?php

namespace App\Repositories\User;


use App\Models\Auth\User;

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
