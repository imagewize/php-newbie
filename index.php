<?php
// Stage 0: everything in one file. No models, no views, no router -
// just a script that connects to the database, handles the request,
// and prints HTML. This is the "before MVC" starting point.

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        h1 { color: #333; }
        .auth-form { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px; max-width: 400px; }
        .auth-form input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 3px; }
        .auth-form button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        .error { color: red; margin: 10px 0; }
        .post { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .post h2 { margin-bottom: 10px; }
        .post .meta { color: #666; font-size: 0.9em; margin-bottom: 10px; }
        .user-list { background: #f5f5f5; padding: 20px; border-radius: 5px; }
        .user-list h3 { margin-bottom: 15px; }
        .user-list ul { list-style: none; }
        .user-list li { padding: 10px; border-bottom: 1px solid #ddd; }
        .container { margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars(SITE_NAME) ?></h1>
        <nav>
            <?php if ($isLoggedIn): ?>
                <span>Welcome, <?= htmlspecialchars($currentUser['name']) ?> (<?= htmlspecialchars($currentUser['role']) ?>)</span>
                <a href="?action=logout" style="margin-left: 20px;">Logout</a>
            <?php else: ?>
                <span>Not logged in</span>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?php if ($showLogin): ?>
            <div class="auth-form">
                <h2>Login</h2>
                <?php if ($loginError): ?>
                    <p class="error"><?= htmlspecialchars($loginError) ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p><small>Demo credentials: admin@example.com / password123</small></p>
            </div>
        <?php else: ?>
            <h2>Posts</h2>
            <?php if (empty($posts)): ?>
                <p>No posts yet.</p>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="post">
                        <h2><?= htmlspecialchars($p['title']) ?></h2>
                        <div class="meta">
                            Posted by <?= htmlspecialchars($p['author_name']) ?>
                            on <?= date('M j, Y g:i a', strtotime($p['created_at'])) ?>
                            <span style="color: #007bff;">(<?= ucfirst($p['status']) ?>)</span>
                        </div>
                        <div><?= nl2br(htmlspecialchars($p['content'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <div class="user-list" style="margin-top: 40px;">
                    <h3>Users</h3>
                    <ul>
                        <?php foreach ($allUsers as $u): ?>
                            <li>
                                <strong><?= htmlspecialchars($u['name']) ?></strong>
                                (<?= htmlspecialchars($u['email']) ?>) -
                                <?= htmlspecialchars($u['role']) ?>
                                <br>
                                <small>Joined: <?= date('M j, Y', strtotime($u['created_at'])) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
