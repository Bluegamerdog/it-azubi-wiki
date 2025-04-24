<?php
session_start();

// Überprüfen, ob der Logout-Button gedrückt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Alle Session-Daten löschen
    session_unset();

    // Session zerstören
    session_destroy();

    // Weiterleitung zur Login-Seite
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zurück'])) {
header("Location: register.php"); //<--------------------------------------------------vorherige seite einfügen
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
</head>
<body>
    <h1>Wollen sie sich wirklich Ausloggen?</h1>
    <form method="post">
        <button type="submit" name="logout">Ausloggen</button>
        <button type="submit" name="zurück">zurück</button>

    </form>
</body>
</html>