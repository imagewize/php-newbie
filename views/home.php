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

<?php if ($user->isLoggedIn()): ?>
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
