<?php

namespace App\Http\Middleware;

use App\Models\Auth\User;
use App\Service\JwtService\JwtServiceInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token');
        $jwtService = app()->make(JwtServiceInterface::class);
        $claims = $jwtService->parseToken($token);

        if ($claims['type'] !== 'access' || Carbon::parse($claims['exp']) < Carbon::now()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($claims['uid']);
        $request->attributes->add(['userId' => $user->id]);
        return $next($request);
    }
}
