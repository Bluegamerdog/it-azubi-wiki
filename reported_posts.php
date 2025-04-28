<?php
session_start();

require_once "functions/database.php";


// Filterung per GET
$selected_post_id = $_GET['post_id'] ?? null;

// Alle einzigartigen post_ids für Dropdown
$posts_stmt = $pdo->query("SELECT DISTINCT post_id FROM reports ORDER BY post_id ASC");
$post_ids = $posts_stmt->fetchAll(PDO::FETCH_COLUMN);

// Reports holen (alle oder gefiltert)
if ($selected_post_id) {
    $stmt = $pdo->prepare("SELECT id AS reports_id, user_id, post_id, reported_at FROM reports WHERE post_id = ? ORDER BY reported_at DESC");
    $stmt->execute([$selected_post_id]);
} else {
    $stmt = $pdo->query("SELECT id AS reports_id, user_id, post_id, reported_at FROM reports ORDER BY reported_at DESC");
}
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Löschen eines Reports, falls der Lösch-Button geklickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_report_id'])) {
    $delete_report_id = $_POST['delete_report_id'];

    // Report aus der Datenbank löschen
    $delete_stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    if ($delete_stmt->execute([$delete_report_id])) {
        echo "Der Report wurde erfolgreich gelöscht.";
    } else {
        echo "Fehler beim Löschen des Reports.";
    }
    // Umleiten zur gleichen Seite, um den gelöschten Report nicht mehr anzuzeigen
    header("Location: reported_posts.php");
    exit();
}
include "includes/header.php";

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Gemeldete Beiträge</title>
</head>

<body>
    <h1>Gemeldete Beiträge</h1>

    <!-- Filter: Dropdown -->
    <form method="GET" action="">
        <label for="post_id">Berichte filtern nach Post-ID:</label>
        <select name="post_id" id="post_id">
            <option value="">-- Alle anzeigen --</option>
            <?php foreach ($post_ids as $pid): ?>
                <option value="<?= $pid ?>" <?= ($pid == $selected_post_id) ? 'selected' : '' ?>>
                    Beitrag #<?= htmlspecialchars($pid) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtern</button>
    </form>

    <hr>

    <?php if (empty($reports)): ?>
        <p>Keine Reports gefunden.</p>
    <?php else: ?>
        <?php foreach ($reports as $report): ?>
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <p><strong>Report-ID:</strong> <?= htmlspecialchars($report['reports_id']) ?></p>

                <?php
                // Benutzername anhand der user_id aus der users-Tabelle holen
                $user_stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $user_stmt->execute([$report['user_id']]);
                $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <p><strong>Benutzername:</strong> <?= htmlspecialchars($user['username']) ?></p>

                <p><strong>Post-ID:</strong> <?= htmlspecialchars($report['post_id']) ?></p>
                <p><strong>Gemeldet am:</strong> <?= htmlspecialchars($report['reported_at']) ?></p>

                <?php
                // Den Titel des Beitrags abrufen, der gemeldet wurde
                $post_stmt = $pdo->prepare("SELECT title FROM posts WHERE id = ?");
                $post_stmt->execute([$report['post_id']]);
                $post = $post_stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <p>
                    <?php if ($post): ?>
                        <a href="read_post.php?id=<?= urlencode($report['post_id']) ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    <?php else: ?>
                        Beitrag nicht gefunden
                    <?php endif; ?>
                </p>

                <!-- Button zum Löschen des Reports -->
                <form method="POST" action="" onsubmit="return confirm('Bist du sicher, dass du diesen Report löschen möchtest?');">
                    <input type="hidden" name="delete_report_id" value="<?= $report['reports_id'] ?>">
                    <button type="submit" class="btn btn-danger">Report löschen</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>