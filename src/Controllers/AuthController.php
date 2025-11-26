<?php

namespace App\Controllers;

use App\Helpers\JWTHelper;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;

class AuthController
{
    private UserRepository $userRepo;
    private WalletRepository $walletRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->walletRepo = new WalletRepository();
    }

    public function showLoginParent(): void
    {
        require_once __DIR__ . '/../../views/auth/login-parent.php';
    }

    public function showLoginTeenager(): void
    {
        require_once __DIR__ . '/../../views/auth/login-teenager.php';
    }

    public function showRegister(): void
    {
        require_once __DIR__ . '/../../views/auth/register.php';
    }

    public function loginParent(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userRepo->findParentByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $token = JWTHelper::generateToken($user['id'], 'parent');
            setcookie('auth_token', $token, time() + 86400, '/', '', false, true);

            header('Location: /parent/dashboard');
            exit;
        }

        header('Location: /auth/login-parent?error=1');
        exit;
    }

    public function loginTeenager(): void
    {
        $parentEmail = $_POST['parent_email'] ?? '';
        $password = $_POST['password'] ?? '';

        $parent = $this->userRepo->findParentByEmail($parentEmail);

        if (!$parent) {
            header('Location: /auth/login-teenager?error=1');
            exit;
        }

        $teenagers = $this->userRepo->findTeenagersByParentId($parent['id']);

        foreach ($teenagers as $teenager) {
            if (password_verify($password, $teenager['password'])) {
                $token = JWTHelper::generateToken($teenager['id'], 'teenager');
                setcookie('auth_token', $token, time() + 86400, '/', '', false, true);
                header('Location: /teenager/dashboard');
                exit;
            }
        }

        header('Location: /auth/login-teenager?error=1');
        exit;
    }

    public function register(): void
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'parent';

        if ($role === 'parent') {
            // Vérifier si l'email existe déjà
            $existingUser = $this->userRepo->findParentByEmail($email);
            if ($existingUser) {
                header('Location: /auth/login-parent?error=email_exists');
                exit;
            }

            try {
                $userId = $this->userRepo->createParent($name, $email, $password);
                $this->walletRepo->create($userId);

                $token = JWTHelper::generateToken($userId, 'parent');
                setcookie('auth_token', $token, time() + 86400, '/', '', false, true);

                header('Location: /parent/dashboard?registered=1');
            } catch (\Exception $e) {
                error_log($e->getMessage());
                header('Location: /auth/register?error=db');
            }
        }

        exit;
    }

    public function logout(): void
    {
        setcookie('auth_token', '', time() - 3600, '/');
        header('Location: /auth/login-parent');
        exit;
    }
}
