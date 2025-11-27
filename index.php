<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\ParentController;
use App\Controllers\TeenagerController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
} catch (Exception $e) {
}

$userRepo = new UserRepository();
$walletRepo = new WalletRepository();
$transactionRepo = new TransactionRepository();

$authController = new AuthController($userRepo, $walletRepo);
$parentController = new ParentController($userRepo, $walletRepo, $transactionRepo);
$teenagerController = new TeenagerController($walletRepo, $transactionRepo);

$router = new Router();

// --- ROUTES ---

$router->register('GET', '/', function () {
    $user = AuthMiddleware::authenticate();
    if ($user) {
        if ($user->role === 'parent') {
            header('Location: /parent/dashboard');
        } else {
            header('Location: /teenager/dashboard');
        }
        exit;
    }
    $home = new HomeController();
    $home->showLanding();
});

// Auth routes
$router->register('GET', '/auth/login-parent', function () use ($authController) {
    $authController->showLoginParent();
});

$router->register('GET', '/auth/login-teenager', function () use ($authController) {
    $authController->showLoginTeenager();
});

$router->register('GET', '/auth/register', function () use ($authController) {
    $authController->showRegister();
});

$router->register('POST', '/auth/login-parent', function () use ($authController) {
    $authController->loginParent();
});

$router->register('POST', '/auth/login-teenager', function () use ($authController) {
    $authController->loginTeenager();
});

$router->register('POST', '/auth/register', function () use ($authController) {
    $authController->register();
});

$router->register('GET', '/auth/logout', function () use ($authController) {
    $authController->logout();
});

// Parent routes
$router->register('GET', '/parent/dashboard', function () use ($parentController) {
    $parentController->dashboard();
});

$router->register('GET', '/parent/add-teenager', function () use ($parentController) {
    $parentController->showAddTeenager();
});

$router->register('POST', '/parent/add-teenager', function () use ($parentController) {
    $parentController->addTeenager();
});

$router->register('GET', '/parent/teenager', function () use ($parentController) {
    $parentController->showTeenager();
});

$router->register('POST', '/parent/set-allowance', function () use ($parentController) {
    $parentController->setAllowance();
});

$router->register('POST', '/parent/transfer-money', function () use ($parentController) {
    $parentController->transferMoney();
});

$router->register('POST', '/parent/delete-teenager', function () use ($parentController) {
    $parentController->deleteTeenager();
});

$router->register('GET', '/parent/deposit', function () use ($parentController) {
    $parentController->showDeposit();
});

$router->register('POST', '/parent/deposit', function () use ($parentController) {
    $parentController->processDeposit();
});

// Teenager routes
$router->register('GET', '/teenager/dashboard', function () use ($teenagerController) {
    $teenagerController->dashboard();
});

$router->register('POST', '/teenager/expense', function () use ($teenagerController) {
    $teenagerController->expense();
});

$router->register('GET', '/teenager/history', function () use ($teenagerController) {
    $teenagerController->history();
});

try {
    $router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    http_response_code(404);
    echo "404 Not Found: " . htmlspecialchars($e->getMessage());
}
