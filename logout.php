<?php
require_once __DIR__  . "/functions/utils.php";
start_session();

// Überprüfen, ob der Logout-Button gedrückt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Alle Session-Daten löschen
    session_unset();

    // Session zerstören
    session_destroy();

    // Weiterleitung zur Login-Seite
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zurück'])) {
    header("Location: login.php");
    exit();
}

include __DIR__ . '/includes/header.php';
?>


<div class="container mt-4 col-md-6 text-center card shadow card-body">
    <div class="justify-content-center">
        <h1 class="mb-4">Möchten Sie sich wirklich ausloggen?</h1>
        <form method="post">
            <button type="submit" name="logout" class="btn btn-danger me-2">Ausloggen</button>
            <button type="submit" name="zurück" class="btn btn-secondary">Zurück</button>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>