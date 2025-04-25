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
$post = fetch_post($pdo, $post_id);

if (!$post) {
    exit('Post with ID ' . $post_id . ' not found!');
}

$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null; // Get the user's role from session

// Reaktion verarbeiten (nur wenn User eingeloggt ist)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    if (isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        set_reaction($pdo, $post_id, $user_id, $_POST['reaction']);
    }
    if (isset($_POST['delete_post']) && ($user_role === 'admin' || $user_role === 'moderator')) {
        delete_post($pdo, $post_id);
        header('Location: index.php');
        exit();
    }
    header("Location: read_post.php?id=$post_id");
    exit();
}

// Aktuelle Reaktionen zählen
$reactions = fetch_reaction_counts($pdo, $post_id);
$userReaction = $user_id ? fetch_user_reaction($pdo, $post_id, $user_id) : null;

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1><?= htmlspecialchars($post["title"]) ?></h1>
    <p><strong>Veröffentlicht am:</strong> <?= htmlspecialchars($post["created_at"]) ?></p>

    <div class="mt-3">
        <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>
    </div>

    <form action="read_post.php?id=<?= $post_id ?>" method="POST">
        <div class="mt-4">
            <button class="btn <?= $userReaction === 'upvote' ? 'btn-success' : 'btn-outline-success' ?>" type="submit"
                name="reaction" value="upvote" <?= $user_id ? '' : 'disabled' ?>>
                👍 <?= $reactions['upvote'] ?>
            </button>
            <button class="btn <?= $userReaction === 'downvote' ? 'btn-danger' : 'btn-outline-danger' ?>" type="submit"
                name="reaction" value="downvote" <?= $user_id ? '' : 'disabled' ?>>
                👎 <?= $reactions['downvote'] ?>
            </button>
        </div>
    </form>

    <?php if ($user_role === 'admin' || $user_role === 'moderator'): ?>
        <!-- Show delete button for admins and moderators -->
        <form action="read_post.php?id=<?= $post_id ?>" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this post?');">
            <button type="submit" class="btn btn-danger mt-4" name="delete_post" value="1">Delete Post</button>
        </form>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-4">Zurück zur Übersicht</a>
</div>

<?php include 'includes/footer.php'; ?>