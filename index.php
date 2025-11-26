<?php
// Afficher toutes les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Relais vers le dossier public
require_once __DIR__ . '/public/index.php';
