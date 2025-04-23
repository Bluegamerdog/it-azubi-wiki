<?php
session_start();
require_once 'functions/database.php';   // PDO-Verbindung
require_once 'functions/utils.php';      // check_admin

// Zugriff prüfen (nur Administratoren)
// if (!isset($_SESSION["username"])) {
//     header("Location: login.php");
//     exit();
// }

// Beiträge abrufen (PDO)
$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1>Beiträge verwalten</h1>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Es sind keine Beiträge vorhanden.</div>
    <?php endif; ?>

    <ul class="list-group">
        <?php foreach ($posts as $post): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <a href="post_read.php?id=<?= htmlspecialchars($post['id']) ?>">
                        <?= htmlspecialchars($post["title"]) ?>
                    </a>
                </div>
                <div>
                    <!-- Bearbeiten -->
                    <a href="update_post.php?id=<?= $post['id'] ?>" class="btn btn-warning btn-sm">Bearbeiten</a>

                    <!-- Löschen -->
                    <form method="post" action="delete_post.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Löschen</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="create_post.php" class="btn btn-success mt-3">Neuen Beitrag erstellen</a>
</div>

<?php include 'includes/footer.php'; ?>