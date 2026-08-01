<?php
require_once 'config.php';

$user = new User();
$post = new Post();

// Handle actions from GET parameters
$action = $_GET['action'] ?? 'home';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $loginUser = $user->login($_POST['email'], $_POST['password']);
        if ($loginUser) {
            header('Location: index.php');
            exit;
        }
        $error = 'Invalid email or password';
        $action = 'login';
    }
}

// Handle GET actions
switch ($action) {
    case 'logout':
        $user->logout();
        header('Location: index.php');
        exit;

    case 'login':
        // Show login form
        break;

    default:
        $action = 'home';
        // Get data for home view
        $currentUser = $user->getCurrentUser();
        $posts = $post->getPublished();
        $allUsers = $user->getAll();
        break;
}

// Prepare data for views
$currentUser = $currentUser ?? $user->getCurrentUser();

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
