<?php

namespace App;

class Router
{
    private array $routes = [];

    public function register(string $method, string $path, callable $action): void
    {
        $this->routes[$method][$path] = $action;
    }

    public function resolve(string $requestUri, string $requestMethod): mixed
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        $action = $this->routes[$requestMethod][$path] ?? null;

        if (!$action) {
            throw new \Exception("Route not found");
        }

        return call_user_func($action);
    }
}
