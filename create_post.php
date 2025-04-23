<?php
// Session starten (z. B. für spätere Nutzerprüfung)
session_start();

// Notwendige Dateien einbinden
require_once 'functions/database.php';
require_once 'functions/utils.php';
include 'includes/header.php';

// Initialisierung von Variablen
$title = '';
$content = '';
$errors = [];
$success = false;

// Prüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingaben säubern
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validierung: Titel darf nicht leer sein
    if ($title === '') {
        $errors[] = 'Titel darf nicht leer sein.';
    } elseif (strlen($title) > 100) {
        $errors[] = 'Titel darf maximal 100 Zeichen lang sein.';
    }

    // Validierung: Inhalt darf nicht leer sein
    if ($content === '') {
        $errors[] = 'Inhalt darf nicht leer sein.';
    }

    // Wenn keine Fehler vorliegen, speichern wir den Post
    if (empty($errors)) {
        if (create_post($conn, $title, $content)) {
            // Erfolgreich? Zur Startseite weiterleiten
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Fehler beim Speichern des Posts.';
        }
    }
}
?>

<!-- HTML: Formular anzeigen -->
<div class="container mt-5">
    <h2>Neuen Post erstellen</h2>

    <!-- Fehlermeldungen anzeigen -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Eingabeformular -->
    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Titel</label>
            <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($title) ?>">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Inhalt</label>
            <textarea class="form-control" id="content" name="content" rows="5" required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>
</div>

<!-- Footer einbinden -->
<?php include 'includes/footer.php'; ?>