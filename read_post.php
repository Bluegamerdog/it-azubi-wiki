<?php
session_start();
require_once 'functions/database.php';  // PDO-Verbindung
require_once 'functions/utils.php';     // check_admin (falls benötigt)

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Wenn keine gültige ID übergeben wurde, zur Admin-Übersicht zurückkehren
    header('Location: admin_dashboard.php');
    exit();
}

$id = (int) $_GET['id'];

// Beitrag aus der Datenbank abrufen
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Wenn der Beitrag nicht existiert, zur Admin-Übersicht weiterleiten
if (!$post) {
    header('Location: admin_dashboard.php');
    exit();
}

include 'includes/header.php';

// // Like button
// if ($hasLiked) {
//     $hasLiked = false;
// } elseif ($hasDisliked) {
//     $hasDisliked = false;
//     $hasLiked = true;
// } else {
//     $hasLiked = true;
// }
// ;

// // Dislike button
// if ($hasDisliked) {
//     $hasDisliked = false;
// } elseif ($hasLiked) {
//     $hasLiked = false;
//     $hasDisliked = true;
// } else {
//     $hasDisliked = true;
// }
;
?>


<div class="container mt-4">
    <h1><?= htmlspecialchars($post["title"]) ?></h1>
    <p><strong>Veröffentlicht am:</strong> <?= htmlspecialchars($post["created_at"]) ?></p>

    <div class="mt-3">
        <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>
    </div>

    <a href="edit_post.php" class="btn btn-secondary mt-3">Zurück zur Übersicht</a>
</div>

<?php include 'includes/footer.php'; ?>