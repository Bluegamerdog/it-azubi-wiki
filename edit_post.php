<?php
session_start();
require_once 'functions/database.php';   // PDO-Verbindung
require_once 'functions/utils.php';      // check_admin

// Zugriff prüfen (nur Administratoren)
$user_id = $_SESSION["user_id"] ?? NULL;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

$id = isset($_POST['post_id']) ? $_POST['post_id'] : NULL;
$post = $id ? fetch_post($pdo, $id) : NULL;

// Wenn der Beitrag nicht existiert, zurück zur Übersicht
if (!$post) {
    exit("Post not found.");
}
if ($post['author_id'] !== $user_id) {
    exit("No authorized to edit this post.");
}

// Initialisierung
$title = $post['title'];
$content = $post['content'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    // Validierung
    if (empty($title)) {
        $errors[] = "Der Titel darf nicht leer sein.";
    }
    if (empty($content)) {
        $errors[] = "Der Inhalt darf nicht leer sein.";
    }

    // Wenn keine Fehler aufgetreten sind, den Beitrag aktualisieren
    if (empty($errors)) {
        // Jetzt wird die Anfrage zu actions_post.php gesendet
        $_POST['action'] = 'edit_post';
        // Weiterhin validiere die Daten auf der actions_post.php-Seite
        include 'actions_post.php'; // Dies führt das Update auf actions_post.php aus.
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1>Beitrag bearbeiten</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_post.php" method="post">
        <input type="hidden" name="submit" value=<?= true ?>>
        <input type="hidden" name="post_id" value=<?= $_POST['post_id'] ?>>
        <div class="mb-3">
            <label for="title" class="form-label">Titel</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title) ?>"
                required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Inhalt</label>
            <textarea class="form-control" id="content" name="content" rows="5"
                required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Aktualisieren</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>