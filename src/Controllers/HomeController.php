<?php

namespace App\Controllers;

class HomeController
{
    public function showLanding(): void
    {
        require_once __DIR__ . '/../../views/landing.php';
    }
}
