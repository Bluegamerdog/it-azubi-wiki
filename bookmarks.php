<?php
session_start();
require_once "functions/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Nicht eingeloggt.");
}

$user_id = (int) $_SESSION['user_id'];
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Löschen per POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    header("Location: bookmarks.php");
    exit;
}

//Bookmarks abrufen
$stmt = $pdo->prepare("
    SELECT b.id AS bookmark_id, p.id AS post_id, p.title
    FROM bookmarks b
    JOIN posts p ON b.post_id = p.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<h1>Deine Lesezeichen: </h1>
<?php if (empty($bookmarks)): ?>
    <p>Du hast noch keine Lesezeichen gespeichert.</p>
<?php else: ?>
    <ul class="list-group">
        <?php foreach ($bookmarks as $bm): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <!-- Button zum Öffnen des Bookmarks mit dem Titel als Text -->
                <form method="get" action="read_post.php" class="d-inline">
                    <input type="hidden" name="id" value="<?= $bm['post_id'] ?>">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <?= htmlspecialchars($bm['title']) ?> <!-- Titel als Button-Text -->
                    </button>
                </form>

                <!-- Button zum Löschen des Bookmarks -->
                <form method="post" onsubmit="return confirm('Wirklich löschen?');" class="d-inline">
                    <input type="hidden" name="delete_id" value="<?= $bm['bookmark_id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Löschen</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>