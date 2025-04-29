<?php
require_once __DIR__  . '/functions/database.php';   // PDO-Verbindung
require_once __DIR__  . '/functions/utils.php';      // check_admin
start_session();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialisierung
$title = '';
$content = '';
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

    // Wenn keine Fehler aufgetreten sind, den Beitrag speichern
    if (empty($errors)) {
        create_post($pdo, $_SESSION['user_id'], $title, $content);

        $success = true;
        header('Location: index.php');
        exit();
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="container mt-4">
    <h1>Neuen Beitrag erstellen</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Der Beitrag wurde erfolgreich erstellt.
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
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title) ?>"
                required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Inhalt</label>
            <textarea class="form-control" id="content" name="content" rows="5"
                required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Speichern</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>