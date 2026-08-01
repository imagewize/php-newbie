<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cms_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'My CMS');
define('SITE_URL', 'http://localhost/php-newbie');

// Error Reporting (development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/includes/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
