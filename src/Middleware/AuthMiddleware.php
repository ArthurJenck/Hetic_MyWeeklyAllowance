<?php

namespace App\Middleware;

use App\Helpers\JWTHelper;

class AuthMiddleware
{
    public static function authenticate(): ?object
    {
        if (!isset($_COOKIE['auth_token'])) {
            return null;
        }

        $token = $_COOKIE['auth_token'];
        if (!JWTHelper::validateToken($token)) {
            return null;
        }

        return JWTHelper::decodeToken($token);
    }

    public static function requireAuth(?string $requiredRole = null): object
    {
        $user = self::authenticate();

        if ($user === null) {
            header('Location: /auth/login');
            exit;
        }

        if ($requiredRole !== null && $user->role !== $requiredRole) {
            http_response_code(403);
            die('Access denied');
        }

        return $user;
    }

    public static function requireParent(): object
    {
        return self::requireAuth('parent');
    }

    public static function requireTeenager(): object
    {
        return self::requireAuth('teenager');
    }
}
