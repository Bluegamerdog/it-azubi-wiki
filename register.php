<?php
session_start();
require_once "functions/database.php"; // oder functions/user.php, falls du dort die Funktion hast
include 'includes/header.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sent"])) {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password_eingabe"] ?? "");
    $password_wiederholen = trim($_POST["password_wiederholung"] ?? "");





    if (empty($username) || empty($email) || empty($password) || empty($password_wiederholen)) {
    $message = "Bitte fülle alle Felder aus.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "Bitte gib eine gültige E-Mail-Adresse ein.";
    } elseif (strlen($password) < 6) {
        $message = "Das Passwort muss mindestens 6 Zeichen lang sein.";
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $message = "Das Passwort muss mindestens einen Buchstaben enthalten.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $message = "Das Passwort muss mindestens eine Ziffer enthalten.";
    } elseif ($password !== $password_wiederholen) {
        $message = "Die Passwörter stimmen nicht überein.";
    } else {
       $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
        $message = "Benutzername oder E-Mail ist bereits vergeben.";
        } else {
        if (create_user($pdo, $username, $email, $password)) {
        $message = "Registrierung erfolgreich! Sie können sich jetzt einloggen.";
        } else {
        $message = "Fehler beim Erstellen des Accounts.";
        }
    }
  }
}

?>

<h1>Registrierung</h1>
<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="mb-3">
            <label for="username_eingeben" class="form-label">Benutzername</label>
            <input type="text" class="form-control" id="username_eingeben" name="username" placeholder="username eingeben" required>
        </div>
        <div class="mb-3">
            <label for="password_eingabe" class="form-label">Passwort</label>
            <input type="password" class="form-control" id="password_eingabe" name="password_eingabe" placeholder="Neues Password eingeben" required>
        </div>
        <div class="mb-3">
            <label for="password_wiederholung" class="form-label">Passwort wiederholen</label>
            <input type="password" class="form-control" id="password_wiederholung" name="password_wiederholung" placeholder="Password wiederholen" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="email eingabe" required>
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
<?php
include 'includes/footer.php';
?>
