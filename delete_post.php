<?php
// Datenbankverbindung und Kopfbereich einbinden
require_once 'functions/database.php'; // korrigierter Pfad: 'function/' → 'functions/'
include 'includes/header.php';

// === Schritt 3: Eintrag löschen ===
if (isset($_POST['id'], $_POST['title'], $_POST['ok'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];

    // Vorbeugung gegen SQL-Injection durch Prepared Statement
    $stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // Erfolgreiche Löschung anzeigen
?>
    <div class="container mt-4">
        <div class="alert alert-success">
            Eintrag '<?= htmlspecialchars($title) ?>' erfolgreich gelöscht.
        </div>
        <a href="edit_post.php" class="btn btn-primary">Zurück zur Übersicht</a>
    </div>
<?php
    include 'includes/footer.php';
    exit();
}

// === Schritt 2: Bestätigung anzeigen ===
if (isset($_POST['id'], $_POST['title'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
?>
    <div class="container mt-4">
        <h4>Möchtest du diesen Eintrag wirklich löschen?</h4>
        <p><strong><?= htmlspecialchars($title) ?></strong></p>

        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
            <input type="hidden" name="ok" value="1">
            <button type="submit" class="btn btn-danger">Ja, löschen</button>
        </form>

        <a href="edit_post.php" class="btn btn-secondary mt-3">Zurück zur Übersicht</a>
    </div>
<?php
    include 'includes/footer.php';
    exit();
}

// === Schritt 1: Falls kein gültiger POST vorhanden ist, weiterleiten ===
header('Location: edit_post.php');
exit();
