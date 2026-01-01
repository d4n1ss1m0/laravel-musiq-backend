<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Service\AuthService\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, AuthServiceInterface $authService)
    {
        $data = $authService->auth($request->login, $request->password);
        return response()->json($data);
    }

    public function refresh(Request $request, AuthServiceInterface $authService)
    {
        $data = $authService->refresh($request->refresh_token);
        return response()->json($data);
    }
}
