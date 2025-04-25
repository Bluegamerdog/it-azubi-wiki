<?php
session_start();

require_once 'functions/database.php';
require_once 'functions/utils.php';

// Prüfen ob Post-ID übergeben wurde
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = (int) $_GET['id'];

// Beitrag abrufen
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: admin_dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;

// Reaktion verarbeiten (nur wenn User eingeloggt ist)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    if (isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        set_reaction($pdo, $post_id, $user_id, $_POST['reaction']);
    }
    header("Location: read_post.php?id=$post_id"); // Reload, um doppeltes Absenden zu vermeiden
    exit();
}

// Aktuelle Reaktionen zählen
$reactions = get_reaction_counts($pdo, $post_id);
$userReaction = $user_id ? get_user_reaction($pdo, $post_id, $user_id) : null;

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1><?= htmlspecialchars($post["title"]) ?></h1>
    <p><strong>Veröffentlicht am:</strong> <?= htmlspecialchars($post["created_at"]) ?></p>

    <div class="mt-3">
        <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>
    </div>

    <?php if ($user_id): ?>
        <form method="post" class="mt-4">
            <button type="submit" name="reaction" value="upvote"
                class="btn <?= $userReaction === 'upvote' ? 'btn-success' : 'btn-outline-success' ?>">
                👍 <?= $reactions['upvote'] ?>
            </button>
            <button type="submit" name="reaction" value="downvote"
                class="btn <?= $userReaction === 'downvote' ? 'btn-danger' : 'btn-outline-danger' ?>">
                👎 <?= $reactions['downvote'] ?>
            </button>
        </form>
    <?php else: ?>
        <p class="text-muted mt-3">🔐 Bitte <a href="login.php">einloggen</a>, um abzustimmen.</p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-4">Zurück zur Übersicht</a>
</div>

<?php include 'includes/footer.php'; ?>