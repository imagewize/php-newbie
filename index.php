<?php
require_once 'config.php';

// Initialize models and controllers
$user = new User();
$router = new Router();
$authController = new AuthController();
$homeController = new HomeController();

// Register GET routes
$router->addGetRoute('home', [$homeController, 'index']);
$router->addGetRoute('login', fn($get) => ['action' => 'login']);
$router->addGetRoute('logout', [$authController, 'logout']);

// Register POST routes
$router->addPostRoute('login', [$authController, 'login']);

// Dispatch and get result
$result = $router->dispatch();

// Handle redirects from controllers
if (isset($result['redirect'])) {
    header("Location: {$result['redirect']}");
    exit;
}

$action = $result['action'];
$data = $result['data'];

// Ensure currentUser is available for views
$currentUser = $currentUser ?? $user->getCurrentUser();

// Extract controller data for views (won't overwrite existing vars due to EXTR_SKIP)
extract($data, EXTR_SKIP);

// Set default title
$title = SITE_NAME;

// Render the view
ob_start();

if ($action === 'login' && !$user->isLoggedIn()) {
    include 'views/login.php';
} else {
    include 'views/home.php';
}

$content = ob_get_clean();

// Render layout
include 'views/layout.php';
