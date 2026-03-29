<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private const ACCESS_TTL = 3600;
    private const REFRESH_TTL = 604800;

    public function __construct(
        private string $jwtSecret,
    ) {
    }

    public function createAccessToken(User $user): string
    {
        $now = time();
        $payload = [
            'sub' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'typ' => 'access',
            'iat' => $now,
            'exp' => $now + self::ACCESS_TTL,
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function createRefreshToken(User $user): string
    {
        $now = time();
        $payload = [
            'sub' => $user->getUserIdentifier(),
            'typ' => 'refresh',
            'iat' => $now,
            'exp' => $now + self::REFRESH_TTL,
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    /**
     * @return \stdClass Decoded JWT payload (sub, roles, typ, …)
     */
    public function decodeAccessToken(string $jwt): object
    {
        $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
        if (($decoded->typ ?? null) !== 'access') {
            throw new \InvalidArgumentException('Not an access token');
        }

        return $decoded;
    }

    /**
     * @return \stdClass Decoded JWT payload
     */
    public function decodeRefreshToken(string $jwt): object
    {
        $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
        if (($decoded->typ ?? null) !== 'refresh') {
            throw new \InvalidArgumentException('Not a refresh token');
        }

        return $decoded;
    }
}
