<?php
require_once "functions/database.php";

// Falls bereits eine Session besteht, wird sie übernommen
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Beispiel: Nutzer ist eingeloggt und hat eine user_id
$user_id = $_SESSION['user_id'] ?? 1; // Fallback zu 1 für Testzwecke

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_id'])) {
    $post_id = (int)$_POST['post_id'];

    $stmt = $pdo->prepare("INSERT IGNORE INTO reports (user_id, post_id) VALUES (?, ?)");
    if ($stmt->execute([$user_id, $post_id])) {
        $_SESSION['message'] = 'Meldung erfolgreich gemeldet!';
    } else {
        $_SESSION['message'] = 'Fehler beim Melden des Beitrags!';
    }
    header("Location: reported_posts.php");
    // HEADER MUSS ERSETZT WERDEN
    // header("Location: read_post.php");
    exit();
}
