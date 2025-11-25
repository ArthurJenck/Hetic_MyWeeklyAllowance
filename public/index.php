<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$router = new Router();

$router->register('GET', '/', function () {
    echo "Hello World";
});

try {
    $router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    http_response_code(404);
    echo "404 Not Found";
}
