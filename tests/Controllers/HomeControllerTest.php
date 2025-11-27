<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;

class HomeControllerTest extends TestCase
{
    public function test_show_landing(): void
    {
        // Arrange
        $controller = new HomeController();

        // Act
        ob_start();
        $controller->showLanding();
        $output = ob_get_clean();

        // Assert
        $this->assertNotEmpty($output);
    }
}
