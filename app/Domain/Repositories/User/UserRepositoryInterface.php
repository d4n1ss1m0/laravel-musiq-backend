<?php

namespace App\Domain\Repositories\User;



use App\Domain\Models\Auth\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
