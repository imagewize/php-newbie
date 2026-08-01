<?php
require_once 'config.php';

// Initialize models and controllers
$user = new User();
$router = new Router();
$authController = new AuthController();
$homeController = new HomeController();
$view = new View();

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
$data['currentUser'] ??= $user->getCurrentUser();
$data['user'] = $user;
$data['title'] = SITE_NAME;

$template = ($action === 'login' && !$user->isLoggedIn()) ? 'login' : 'home';

$view->renderWithLayout($template, $data);
