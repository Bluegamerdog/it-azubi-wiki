<?php
session_start();
require_once 'functions/database.php';
require_once 'functions/utils.php';
include 'includes/header.php';

// Alle Beiträge abrufen
$posts = fetch_all_posts($pdo);

?>

<div class="container mt-4">
    <h1>Willkommen beim IT Forum Wiki</h1>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Es sind noch keine Beiträge vorhanden.</div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">
                        <a href="read_post.php?id=<?= htmlspecialchars($post['id']) ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h4>
                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
                    <a href="read_post.php?id=<?= htmlspecialchars($post['id']) ?>" class="btn btn-primary btn-sm">Weiterlesen</a>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
