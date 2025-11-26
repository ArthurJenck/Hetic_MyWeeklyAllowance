<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHelper
{
    private static function getSecret(): string
    {
        return $_ENV['JWT_SECRET'] ?? 'default_secret_key_change_in_production';
    }

    private static function getExpiration(): int
    {
        return (int) ($_ENV['JWT_EXPIRATION'] ?? 86400);
    }

    public static function generateToken(int $userId, string $role): string
    {
        $issuedAt = time();
        $expire = $issuedAt + self::getExpiration();

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'userId' => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function validateToken(string $token): bool
    {
        try {
            JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::getSecret(), 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }
}

