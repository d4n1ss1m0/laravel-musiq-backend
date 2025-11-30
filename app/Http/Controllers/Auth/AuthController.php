<?php

namespace App\Http\Controllers\Auth;

use App\Application\UseCase\Auth\LoginUser\LoginUserInterface;
use App\Application\UseCase\Auth\RefreshToken\RefreshTokenInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, LoginUserInterface $loginUseCase)
    {
        $data = $loginUseCase->handle($request->login, $request->password);
        return response()->json($data);
    }

    public function refresh(Request $request, RefreshTokenInterface $refreshUseCase)
    {
        $data = $refreshUseCase->handle($request->refresh_token);
        return response()->json($data);
    }
}
