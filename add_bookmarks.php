<?php
session_start();
require_once "functions/database.php";

if (!isset($_SESSION['user_id'], $_POST['post_id'])) {
    die("Nicht angemeldet oder ungültige Daten.");
}

$user_id = (int) $_SESSION['user_id'];
$post_id = (int) $_POST['post_id'];

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("INSERT IGNORE INTO bookmarks (user_id, post_id) VALUES (?, ?)");
$stmt->execute([$user_id, $post_id]);

header("Location: read_post.php?id=$post_id");
exit;
