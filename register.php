<?php
session_start();

// Datenbankverbindung herstellen
require_once "functions/database.php";

// Variable für Fehlermeldungen initialisieren
$message = "";

// Überprüfen, ob das Formular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sent"])) {
    $benutzername = isset($_POST["benutzername"]) ? trim($_POST["benutzername"]) : "";
    $passwort = isset($_POST["passwort_eingabe"]) ? trim($_POST["passwort_eingabe"]) : "";
    $passwort_wiederholen = isset($_POST["passwort_wiederholung"]) ? trim($_POST["passwort_wiederholung"]) : "";

    // Validierung der Eingaben
    if (empty($benutzername) || empty($passwort) || empty($passwort_wiederholen)) {
        $message = "Bitte fülle alle Felder aus.";
    } elseif (strlen($passwort) < 6) {
        $message = "Das Passwort muss mindestens 6 Zeichen lang sein.";
    } elseif (!preg_match('/[a-zA-Z]/', $passwort)) {
        $message = "Das Passwort muss mindestens einen Buchstaben enthalten.";
    } elseif (!preg_match('/[0-9]/', $passwort)) {
        $message = "Das Passwort muss mindestens eine Ziffer enthalten.";
    } elseif ($passwort !== $passwort_wiederholen) {
        $message = "Die Passwörter stimmen nicht überein. Bitte geben Sie beide Passwörter erneut ein.";
    } else {
        // Überprüfen, ob der Benutzername bereits existiert
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE benutzername = :benutzername");
        $stmt->execute([':benutzername' => $benutzername]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Der Benutzername ist bereits vergeben. Bitte wählen Sie einen anderen.";
        } else {
            // Daten in die Datenbank einfügen
            $stmt = $pdo->prepare("INSERT INTO users (benutzername, passwort) VALUES (:benutzername, :passwort)");
            $stmt->execute([
                ":benutzername" => $benutzername,
                ":passwort" => password_hash($passwort, PASSWORD_DEFAULT)
            ]);
            $message = "Registrierung erfolgreich! Sie können sich jetzt einloggen.";
        }
    }
}
?>

<h1>Registrierung</h1>
<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="mb-3">
            <label for="benutzername_eingeben" class="form-label">Benutzername</label>
            <input type="text" class="form-control" id="benutzername_eingeben" name="benutzername" placeholder="Benutzernamen eingeben" required>
        </div>
        <div class="mb-3">
            <label for="passwort_eingabe" class="form-label">Passwort</label>
            <input type="password" class="form-control" id="passwort_eingabe" name="passwort_eingabe" placeholder="Neues Passwort eingeben" required>
        </div>
        <div class="mb-3">
            <label for="passwort_wiederholung" class="form-label">Passwort wiederholen</label>
            <input type="password" class="form-control" id="passwort_wiederholung" name="passwort_wiederholung" placeholder="Passwort wiederholen" required>
        </div>
        <button type="submit" class="btn btn-primary mb-3" name="sent">Registrieren</button>
    </form>
    <!-- Button zum Weiterleiten zur Einloggen-Seite -->
    <form action="login.php" method="get">
        <button type="submit" class="btn btn-secondary mb-3">Zum Login</button>
    </form>
    <?php if (!empty($message)): ?>
        <div class="alert alert-warning">
            <strong>Hinweis!</strong> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
</div>