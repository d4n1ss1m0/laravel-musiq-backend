<?php

namespace App\Service\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Domain\Entities\User;
use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService implements AuthServiceInterface
{
    public function login(LoginRequest $request) {
        $user = User::where('email', $request['email'])->first();
        if(isset($user)) {
            $verify = password_verify($request['password'], $user->password);
            if($verify) {
                $token = $user->createToken('AuthToken')->plainTextToken;
                return [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                    'token' => $token
                ];
            }
        }
        throw new AuthenticationException();
    }
}
