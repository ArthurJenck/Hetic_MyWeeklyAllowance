<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Router;

class RouterTest extends TestCase
{
    public function test_register_adds_route(): void
    {
        // Arrange
        $router = new Router();
        $action = fn() => 'test';

        // Act
        $router->register('GET', '/test', $action);

        // Assert
        $this->assertTrue(true);
    }

    public function test_resolve_calls_registered_action(): void
    {
        // Arrange
        $router = new Router();
        $called = false;
        $action = function() use (&$called) {
            $called = true;
            return 'response';
        };

        $router->register('GET', '/test', $action);

        // Act
        $result = $router->resolve('/test', 'GET');

        // Assert
        $this->assertTrue($called);
        $this->assertEquals('response', $result);
    }

    public function test_resolve_throws_exception_for_unknown_route(): void
    {
        // Arrange
        $router = new Router();

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route not found');

        // Act
        $router->resolve('/unknown', 'GET');
    }
}

