<?php
// Session starten (für Login-Verwaltung)
session_start();
require_once 'functions/database.php';   // korrigierter Pfad
require_once 'functions/utils.php';      // falls check_admin dort ist

// Login-Versuch auswerten
$username = (isset($_POST["username"]) && is_string($_POST["username"])) ? trim($_POST["username"]) : "";
$password = (isset($_POST["password"]) && is_string($_POST["password"])) ? $_POST["password"] : "";

// Wenn Login-Daten eingegeben wurden, prüfen
if (!empty($username) && !empty($password) && check_admin($username, $password)) {
    $_SESSION["username"] = $username;
}

// Wenn nicht eingeloggt → Meldung und Abbruch
if (!isset($_SESSION["username"])) {
    include 'includes/header.php';
?>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <strong>Zugriff verweigert:</strong> Bitte melde dich an.
        </div>
        <a href="login.php" class="btn btn-success">Zum Login</a>
    </div>
<?php
    include 'includes/footer.php';
    exit();
}

// Beiträge abrufen
$posts = fetch_posts($conn);  // angepasst auf mysqli
include 'includes/header.php';
?>

<div class="container mt-4">
    <h1>Adminbereich – Beiträge verwalten</h1>

    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Noch keine Beiträge vorhanden.</div>
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
                        <input type="hidden" name="title" value="<?= htmlspecialchars($post['title']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Löschen</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Neuen Beitrag erstellen -->
    <a href="create_post.php" class="btn btn-success mt-3">Neuen Beitrag erstellen</a>
</div>

<?php include 'includes/footer.php'; ?>