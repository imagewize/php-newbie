<?php
// Stage 1: the same flat script as stage 0, but the HTML now lives in
// views/. Still no classes, no models, no router - the only new idea here
// is separating what the page *does* from what the page *looks like*.

require_once 'config.php';

// Connect to the database right here - no Database class, no Singleton.
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$action = $_GET['action'] ?? 'home';
if ($action !== 'login' && $action !== 'logout') {
    $action = 'home';
}

$loginError = null;

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$_POST['email']]);
    $loginUser = $stmt->fetch();

    if ($loginUser && password_verify($_POST['password'], $loginUser['password'])) {
        $_SESSION['user_id'] = $loginUser['id'];
        $_SESSION['user_role'] = $loginUser['role'];
        $_SESSION['user_name'] = $loginUser['name'];
        header('Location: index.php');
        exit;
    }

    $loginError = 'Invalid email or password';
    $action = 'login';
}

// Handle logout
if ($action === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);

$currentUser = null;
if ($isLoggedIn) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
}

$showLogin = ($action === 'login' && !$isLoggedIn);

// Fetch the data the home page needs
$posts = [];
$allUsers = [];
if (!$showLogin) {
    $posts = $pdo->query(
        "SELECT p.*, u.name AS author_name
         FROM posts p
         JOIN users u ON p.user_id = u.id
         WHERE p.status = 'published'
         ORDER BY p.created_at DESC"
    )->fetchAll();

    if ($isLoggedIn) {
        $allUsers = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    }
}

// Render the page view into a string. Everything the included file prints gets
// captured by the output buffer instead of going straight to the browser. The
// view can read any variable defined above - $posts, $isLoggedIn, and so on.
ob_start();

if ($showLogin) {
    include 'views/login.php';
} else {
    include 'views/home.php';
}

$content = ob_get_clean();

// Now wrap that captured markup in the site-wide layout, which prints $content.
include 'views/layout.php';
