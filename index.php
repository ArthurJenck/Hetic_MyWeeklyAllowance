<?php

// Affichage des erreurs (à désactiver en prod plus tard)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// On cherche l'autoload au même niveau que index.php
require_once __DIR__ . '/vendor/autoload.php';

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\ParentController;
use App\Controllers\TeenagerController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use Dotenv\Dotenv;

// Chargement du .env
try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
} catch (Exception $e) {
    // Si .env manque, on continue
}

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
$router->register('GET', '/auth/login-parent', function () {
    $auth = new AuthController();
    $auth->showLoginParent();
});

$router->register('GET', '/auth/login-teenager', function () {
    $auth = new AuthController();
    $auth->showLoginTeenager();
});

$router->register('GET', '/auth/register', function () {
    $auth = new AuthController();
    $auth->showRegister();
});

$router->register('POST', '/auth/login-parent', function () {
    $auth = new AuthController();
    $auth->loginParent();
});

$router->register('POST', '/auth/login-teenager', function () {
    $auth = new AuthController();
    $auth->loginTeenager();
});

$router->register('POST', '/auth/register', function () {
    $auth = new AuthController();
    $auth->register();
});

$router->register('GET', '/auth/logout', function () {
    $auth = new AuthController();
    $auth->logout();
});

// Parent routes
$router->register('GET', '/parent/dashboard', function () {
    $parent = new ParentController();
    $parent->dashboard();
});

$router->register('GET', '/parent/add-teenager', function () {
    $parent = new ParentController();
    $parent->showAddTeenager();
});

$router->register('POST', '/parent/add-teenager', function () {
    $parent = new ParentController();
    $parent->addTeenager();
});

$router->register('GET', '/parent/teenager', function () {
    $parent = new ParentController();
    $parent->showTeenager();
});

$router->register('POST', '/parent/set-allowance', function () {
    $parent = new ParentController();
    $parent->setAllowance();
});

$router->register('POST', '/parent/transfer-money', function () {
    $parent = new ParentController();
    $parent->transferMoney();
});

$router->register('POST', '/parent/delete-teenager', function () {
    $parent = new ParentController();
    $parent->deleteTeenager();
});

$router->register('GET', '/parent/deposit', function () {
    $parent = new ParentController();
    $parent->showDeposit();
});

$router->register('POST', '/parent/deposit', function () {
    $parent = new ParentController();
    $parent->processDeposit();
});

// Teenager routes
$router->register('GET', '/teenager/dashboard', function () {
    $teenager = new TeenagerController();
    $teenager->dashboard();
});

$router->register('POST', '/teenager/expense', function () {
    $teenager = new TeenagerController();
    $teenager->expense();
});

$router->register('GET', '/teenager/history', function () {
    $teenager = new TeenagerController();
    $teenager->history();
});

// --- DEBUG ---
// On intercepte tout avant le resolve pour voir ce qui se passe
echo "<pre>";
echo "URI Demandée (REQUEST_URI): " . $_SERVER['REQUEST_URI'] . "\n";
echo "Script Name (SCRIPT_NAME): " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Query String: " . ($_SERVER['QUERY_STRING'] ?? 'VIDE') . "\n";
echo "Path Info: " . ($_SERVER['PATH_INFO'] ?? 'NON DÉFINI') . "\n";
echo "</pre>";
// die("FIN DU DEBUG");

// Dispatch
try {
    $router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    http_response_code(404);
    echo "404 Not Found: " . htmlspecialchars($e->getMessage());
}
