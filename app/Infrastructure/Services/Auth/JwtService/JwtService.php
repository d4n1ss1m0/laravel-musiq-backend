<?php

namespace App\Infrastructure\Services\Auth\JwtService;

use App\Domain\Models\Auth\RefreshToken;
use App\Domain\Models\Auth\User;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class JwtService implements JwtServiceInterface
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(config('app.key'))
        );
    }

    public function generateAccessToken(User $user): string
    {
        $now = new DateTimeImmutable();
        $token = $this->config->builder()
            ->issuedBy('your-app')
            ->identifiedBy(uniqid(), true)
            ->issuedAt($now)
            ->expiresAt($now->modify('+15 minutes'))
            ->withClaim('uid', $user->id)
            ->withClaim('type', 'access')
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    public function generateRefreshToken(User $user): string
    {
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addDays(30);

        $tokenModel = RefreshToken::create([
            'token' => $token,
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    public function parseToken(string $token): array
    {
        $parsed = $this->config->parser()->parse($token);
        $claims = $parsed->claims();

        return [
            'uid' => $claims->get('uid'),
            'type' => $claims->get('type'),
            'exp' => $claims->get('exp')
        ];
    }

    public function validateRefreshToken(string $token): ?User
    {
        $userId = cache()->get("refresh_token:{$token}");
        return $userId ? User::find($userId) : null;
    }

    public function invalidateRefreshToken(string $token): void
    {
        cache()->forget("refresh_token:{$token}");
    }
}
