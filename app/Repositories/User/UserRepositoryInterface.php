<?php

namespace App\Repositories\User;



use App\Models\Auth\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
