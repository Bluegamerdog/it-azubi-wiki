<?php
session_start();

require_once 'functions/database.php';
require_once 'functions/utils.php';

// CSRF-Token setzen (für Formular-Sicherheit)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = (int) $_GET['id'];

// Beitrag + Autor abrufen
$stmt = $pdo->prepare("
    SELECT p.*, u.username AS author_name
    FROM posts p
    JOIN users u ON p.author_id = u.id
    WHERE p.id = :id
");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;

// Verarbeitung von Reaktionen und Kommentaren (nur wenn eingeloggt)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {

    // CSRF-Token validieren
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        exit("Ungültiger CSRF-Token.");
    }

    // Reaktion verarbeiten
    if (isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        set_reaction($pdo, $post_id, $user_id, $_POST['reaction']);
    }

    // Kommentar verarbeiten
    if (!empty(trim($_POST['comment']))) {
        $content = trim($_POST['comment']);
        create_comment($pdo, $post_id, $user_id, $content);
    }

    // Redirect um doppeltes Absenden zu vermeiden
    header("Location: read_post.php?id=$post_id");
    exit();
}

// Reaktionen abrufen
$reactions = get_reaction_counts($pdo, $post_id);
$userReaction = $user_id ? get_user_reaction($pdo, $post_id, $user_id) : null;

// Kommentare laden
$stmt = $pdo->prepare("
    SELECT c.content, c.created_at, u.username 
    FROM post_comments c 
    JOIN users u ON c.author_id = u.id 
    WHERE c.post_id = :post_id 
    ORDER BY c.created_at DESC
");
$stmt->execute([':post_id' => $post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1><?= htmlspecialchars($post["title"]) ?></h1>
    <p><strong>Veröffentlicht am:</strong> <?= date('d.m.Y H:i', strtotime($post["created_at"])) ?></p>
    <p><strong>Verfasst von:</strong> <?= htmlspecialchars($post["author_name"]) ?></p>

    <div class="mt-3">
        <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>
    </div>

    <!-- Reaktionen -->
    <?php if ($user_id): ?>
        <form method="post" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
        <p class="text-muted mt-3">🔐 Bitte <a href="login.php">einloggen</a>, um abzustimmen oder zu kommentieren.</p>
    <?php endif; ?>

    <!-- Kommentar schreiben -->
    <?php if ($user_id): ?>
        <div class="mt-5">
            <h4>Kommentar schreiben</h4>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="mb-3">
                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Absenden</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Kommentare anzeigen -->
    <div class="mt-5">
        <h4>Kommentare (<?= count($comments) ?>)</h4>
        <?php if (empty($comments)): ?>
            <p class="text-muted">Noch keine Kommentare vorhanden.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="border rounded p-2 mb-3">
                    <strong><?= htmlspecialchars($comment['username']) ?></strong>
                    <small class="text-muted">– <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></small>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="index.php" class="btn btn-secondary mt-4">Zurück zur Übersicht</a>
</div>

<?php include 'includes/footer.php'; ?>