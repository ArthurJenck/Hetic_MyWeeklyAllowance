<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\JWTHelper;

class JWTHelperTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV['JWT_SECRET'] = 'test_secret_key';
        $_ENV['JWT_EXPIRATION'] = 3600;
    }

    public function test_generate_token_returns_string(): void
    {
        $token = JWTHelper::generateToken(1, 'parent');
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function test_validate_token_returns_true_for_valid_token(): void
    {
        $token = JWTHelper::generateToken(1, 'parent');
        $isValid = JWTHelper::validateToken($token);
        $this->assertTrue($isValid);
    }

    public function test_validate_token_returns_false_for_invalid_token(): void
    {
        $isValid = JWTHelper::validateToken('invalid.token.string');
        $this->assertFalse($isValid);
    }

    public function test_decode_token_returns_object_for_valid_token(): void
    {
        $userId = 123;
        $role = 'teenager';
        $token = JWTHelper::generateToken($userId, $role);

        $decoded = JWTHelper::decodeToken($token);

        $this->assertIsObject($decoded);
        $this->assertEquals($userId, $decoded->userId);
        $this->assertEquals($role, $decoded->role);
    }

    public function test_decode_token_returns_null_for_invalid_token(): void
    {
        $decoded = JWTHelper::decodeToken('invalid.token.string');
        $this->assertNull($decoded);
    }
}
