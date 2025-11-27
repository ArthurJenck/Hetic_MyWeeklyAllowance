<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Database;
use PDO;

class DatabaseTest extends TestCase
{
    public function test_get_instance_returns_singleton(): void
    {
        // Act
        $instance1 = Database::getInstance();
        $instance2 = Database::getInstance();

        // Assert
        $this->assertSame($instance1, $instance2);
    }

    public function test_get_connection_returns_pdo(): void
    {
        // Arrange
        $db = Database::getInstance();

        // Act
        $connection = $db->getConnection();

        // Assert
        $this->assertInstanceOf(PDO::class, $connection);
    }

    public function test_wakeup_throws_exception(): void
    {
        // Arrange
        $db = Database::getInstance();

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot unserialize singleton');

        // Act
        $db->__wakeup();
    }

    public function test_cannot_clone_singleton(): void
    {
        // Arrange
        $db = Database::getInstance();

        // Assert
        $this->expectException(\Error::class);

        // Act
        $clone = clone $db;
    }

    public function test_connection_failure_throws_exception(): void
    {
        // Arrange
        $_ENV['DB_HOST'] = 'invalid_host_that_does_not_exist';
        $_ENV['DB_NAME'] = 'invalid_db';

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection failed');

        // Act
        $reflection = new \ReflectionClass(Database::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);

        Database::getInstance();
    }
}

