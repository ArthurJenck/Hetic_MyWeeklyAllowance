<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Controllers\AuthController;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;

require_once __DIR__ . '/HeaderMock.php';

class AuthControllerTest extends TestCase
{
    private $userRepo;
    private $walletRepo;
    private $authController;

    protected function setUp(): void
    {
        global $mockHeaders;
        $mockHeaders = [];

        $this->userRepo = $this->createMock(UserRepository::class);
        $this->walletRepo = $this->createMock(WalletRepository::class);

        $this->authController = new AuthController($this->userRepo, $this->walletRepo);

        $_POST = [];
        $_COOKIE = [];
    }

    public function test_show_methods(): void
    {
        // Act
        ob_start();
        $this->authController->showLoginParent();
        $this->authController->showLoginTeenager();
        $this->authController->showRegister();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    #[TestWith(['parent@test.com', 'password', true, '/parent/dashboard'])]
    #[TestWith(['parent@test.com', 'wrong', false, '/auth/login-parent?error=1'])]
    #[TestWith(['unknown@test.com', 'password', false, '/auth/login-parent?error=1'])]
    #[TestWith(['', '', false, '/auth/login-parent?error=1'])]
    public function test_login_parent(string $email, string $password, bool $loginSuccess, string $expectedRedirect): void
    {
        // Arrange
        $_POST['email'] = $email;
        $_POST['password'] = $password;

        $user = $loginSuccess ? [
            'id' => 1,
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'parent'
        ] : null;

        if ($email === 'parent@test.com') {
            $this->userRepo->method('findParentByEmail')->willReturn($user);
        } else {
            $this->userRepo->method('findParentByEmail')->willReturn(null);
        }

        // Act
        $this->authController->loginParent();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: ' . $expectedRedirect, $mockHeaders);

        if ($loginSuccess) {
            $this->assertArrayHasKey('auth_token', $_COOKIE);
        }
    }

    #[TestWith(['teen@test.com', 'password', true, '/teenager/dashboard'])]
    #[TestWith(['teen@test.com', 'wrong', false, '/auth/login-teenager?error=1'])]
    #[TestWith(['unknown@test.com', 'password', false, '/auth/login-teenager?error=1'])]
    #[TestWith(['', '', false, '/auth/login-teenager?error=1'])]
    public function test_login_teenager(string $email, string $password, bool $loginSuccess, string $expectedRedirect): void
    {
        // Arrange
        $_POST['email'] = $email;
        $_POST['password'] = $password;

        $user = $loginSuccess ? [
            'id' => 2,
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'teenager'
        ] : null;

        if ($email === 'teen@test.com') {
            $this->userRepo->method('findTeenagerByEmail')->willReturn($user);
        } else {
            $this->userRepo->method('findTeenagerByEmail')->willReturn(null);
        }

        // Act
        $this->authController->loginTeenager();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: ' . $expectedRedirect, $mockHeaders);

        if ($loginSuccess) {
            $this->assertArrayHasKey('auth_token', $_COOKIE);
        }
    }

    #[TestWith(['new@test.com', false, '/parent/dashboard?registered=1'])]
    #[TestWith(['existing@test.com', true, '/auth/login-parent?error=email_exists'])]
    public function test_register_parent(string $email, bool $emailExists, string $expectedRedirect): void
    {
        // Arrange
        $_POST['name'] = 'Parent';
        $_POST['email'] = $email;
        $_POST['password'] = 'pass';
        $_POST['role'] = 'parent';

        if ($emailExists) {
            $this->userRepo->method('findParentByEmail')->willReturn(['id' => 1]);
            $this->userRepo->expects($this->never())->method('createParent');
        } else {
            $this->userRepo->method('findParentByEmail')->willReturn(null);
            $this->userRepo->method('createParent')->willReturn(1);
            $this->walletRepo->expects($this->once())->method('create')->with(1);
        }

        // Act
        $this->authController->register();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: ' . $expectedRedirect, $mockHeaders);
    }

    public function test_logout(): void
    {
        // Act
        $this->authController->logout();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /auth/login-parent', $mockHeaders);
    }

    public function test_register_parent_handles_database_error(): void
    {
        // Arrange
        $_POST['name'] = 'Parent';
        $_POST['email'] = 'new@test.com';
        $_POST['password'] = 'pass';
        $_POST['role'] = 'parent';

        $this->userRepo->method('findParentByEmail')->willReturn(null);
        $this->userRepo->method('createParent')->willThrowException(new \Exception('DB Error'));

        // Act
        $this->authController->register();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /auth/register?error=db', $mockHeaders);
    }
}
