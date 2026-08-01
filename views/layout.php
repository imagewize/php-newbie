<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? SITE_NAME) ?></title>
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
            <?php if ($user->isLoggedIn()): ?>
                <span>Welcome, <?= htmlspecialchars($currentUser['name']) ?> (<?= htmlspecialchars($currentUser['role']) ?>)</span>
                <a href="?action=logout" style="margin-left: 20px;">Logout</a>
            <?php else: ?>
                <span>Not logged in</span>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?= $content ?? '' ?>
    </main>
</body>
</html>
