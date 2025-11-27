<?php

namespace Tests\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Middleware\AuthMiddleware;
use App\Helpers\JWTHelper;

require_once __DIR__ . '/../Controllers/HeaderMock.php';

class AuthMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        global $mockHeaders;
        $mockHeaders = [];
        $_COOKIE = [];
        $_ENV['JWT_SECRET'] = 'test_secret_key';
    }

    public function test_authenticate_returns_null_without_token(): void
    {
        // Act
        $result = AuthMiddleware::authenticate();

        // Assert
        $this->assertNull($result);
    }

    public function test_authenticate_returns_null_with_invalid_token(): void
    {
        // Arrange
        $_COOKIE['auth_token'] = 'invalid.token.string';

        // Act
        $result = AuthMiddleware::authenticate();

        // Assert
        $this->assertNull($result);
    }

    public function test_authenticate_returns_user_with_valid_token(): void
    {
        // Arrange
        $token = JWTHelper::generateToken(1, 'parent');
        $_COOKIE['auth_token'] = $token;

        // Act
        $result = AuthMiddleware::authenticate();

        // Assert
        $this->assertIsObject($result);
        $this->assertEquals(1, $result->userId);
        $this->assertEquals('parent', $result->role);
    }

    public function test_authenticate_with_no_cookie_returns_null(): void
    {
        // Arrange
        $_COOKIE = [];

        // Act
        $result = AuthMiddleware::authenticate();

        // Assert
        $this->assertNull($result);
    }

    public function test_require_auth_returns_user_with_valid_token(): void
    {
        // Arrange
        $token = JWTHelper::generateToken(2, 'teenager');
        $_COOKIE['auth_token'] = $token;

        // Act
        $result = AuthMiddleware::requireAuth();

        // Assert
        $this->assertIsObject($result);
        $this->assertEquals(2, $result->userId);
        $this->assertEquals('teenager', $result->role);
    }

    #[TestWith(['parent', 'parent'])]
    #[TestWith(['teenager', 'teenager'])]
    public function test_require_auth_validates_role(string $userRole, string $requiredRole): void
    {
        // Arrange
        $token = JWTHelper::generateToken(1, $userRole);
        $_COOKIE['auth_token'] = $token;

        // Act
        $result = AuthMiddleware::requireAuth($requiredRole);

        // Assert
        $this->assertEquals($userRole, $result->role);
    }

    public function test_require_parent_returns_parent_user(): void
    {
        // Arrange
        $token = JWTHelper::generateToken(1, 'parent');
        $_COOKIE['auth_token'] = $token;

        // Act
        $result = AuthMiddleware::requireParent();

        // Assert
        $this->assertEquals('parent', $result->role);
        $this->assertEquals(1, $result->userId);
    }

    public function test_require_teenager_returns_teenager_user(): void
    {
        // Arrange
        $token = JWTHelper::generateToken(2, 'teenager');
        $_COOKIE['auth_token'] = $token;

        // Act
        $result = AuthMiddleware::requireTeenager();

        // Assert
        $this->assertEquals('teenager', $result->role);
        $this->assertEquals(2, $result->userId);
    }
}

