<?php

namespace App\Service\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Domain\Entities\User;
use Laravel\Sanctum\PersonalAccessToken;

class AccountService
{
    public function getAccountId() {
        return auth()->id();
    }

    public function getCurrentAccount() {
        return User::find(auth()->id());
    }
}
