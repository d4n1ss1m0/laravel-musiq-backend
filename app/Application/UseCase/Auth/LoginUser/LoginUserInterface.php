<?php

namespace App\Application\UseCase\Auth\LoginUser;

interface LoginUserInterface
{
    public function handle(string $email, string $password): array;
}
