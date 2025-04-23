<?php
session_start();
require_once 'functions/database.php';   // PDO-Verbindung

// Zugriff prüfen (nur Administratoren)
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Beitrag löschen
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Weiterleitung zur Admin-Übersicht
    header('Location: admin_dashboard.php');
    exit();
} else {
    header('Location: admin_dashboard.php');
    exit();
}
