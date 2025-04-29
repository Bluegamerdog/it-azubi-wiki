<?php
// Wenn Formular abgesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verbindungsdaten


    // SQL-Datei festlegen
    $sql_file = 'itforumwiki.sql';

    // Verbindung zur Datenbank aufbauen
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('❌ Verbindung fehlgeschlagen: ' . $e->getMessage());
    }

    // Fremdschlüsselprüfung deaktivieren und Tabellen löschen
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    } catch (PDOException $e) {
        die("❌ Fehler beim Löschen der Tabellen: " . $e->getMessage());
    }

    // Datei prüfen
    if (!file_exists($sql_file)) {
        die("❌ Datei '$sql_file' nicht gefunden!");
    }

    // Datei einlesen
    $sql = file_get_contents($sql_file);

    // Ausführen
    try {
        $pdo->exec($sql);
        $meldung = "✅ Die Datenbank wurde erfolgreich importiert und überschrieben!";
    } catch (PDOException $e) {
        $meldung = "❌ Fehler beim Import: " . $e->getMessage();
    }

    // Verbindung schließen
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Datenbank-Import (Azupedia)</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 2rem;
        }
        form {
            margin-top: 2rem;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
        }
        button:hover {
            background-color: #45a049;
        }
        .meldung {
            margin-top: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Wiki-Datenbank importieren & überschreiben</h1>

    <form method="POST">
        <button type="submit">wiki.sql jetzt importieren</button>
    </form>

    <?php if (!empty($meldung)): ?>
        <div class="meldung"><?= htmlspecialchars($meldung) ?></div>
    <?php endif; ?>

</body>
</html>
