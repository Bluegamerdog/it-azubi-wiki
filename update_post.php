<?php
session_start();
require_once 'functions/database.php';   // PDO-Verbindung
require_once 'functions/utils.php';      // check_admin

// Zugriff prüfen (nur Administratoren)
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Beitrag bearbeiten
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Wenn der Beitrag nicht existiert, zurück zur Übersicht
if (!$post) {
    header('Location: admin_dashboard.php');
    exit();
}

// Initialisierung
$title = $post['title'];
$content = $post['content'];
$errors = [];
$success = false;

// Wenn das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        $stmt = $pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':id' => $id
        ]);

        $success = true;
        header('Location: admin_dashboard.php');
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <h1>Beitrag bearbeiten</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Der Beitrag wurde erfolgreich aktualisiert.
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Titel</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Inhalt</label>
            <textarea class="form-control" id="content" name="content" rows="5" required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Aktualisieren</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>