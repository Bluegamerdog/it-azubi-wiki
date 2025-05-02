<?php
require_once __DIR__  . "/functions/database.php";
require_once __DIR__  . "/functions/utils.php";
start_session();

if (get_logged_in_user()) {
    header("Location: index.php");
    exit(); // Already logged in
}

$message = "";


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sent"])) {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password_eingabe"] ?? "");
    $password_wiederholen = trim($_POST["password_wiederholung"] ?? "");

    if (empty($username) || empty($email) || empty($password) || empty($password_wiederholen)) {
        $message = "Bitte fülle alle Felder aus.";
        $message_class = "alert-warning";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Bitte gib eine gültige E-Mail-Adresse ein.";
        $message_class = "alert-warning";
    } elseif (strlen($password) < 6) {
        $message = "Das Passwort muss mindestens 6 Zeichen lang sein.";
        $message_class = "alert-warning";
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $message = "Das Passwort muss mindestens einen Buchstaben enthalten.";
        $message_class = "alert-warning";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $message = "Das Passwort muss mindestens eine Ziffer enthalten.";
        $message_class = "alert-warning";
    } elseif ($password !== $password_wiederholen) {
        $message = "Die Passwörter stimmen nicht überein.";
        $message_class = "alert-warning";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Benutzername oder E-Mail ist bereits vergeben.";
            $message_class = "alert-warning";
        } else {
            if (create_user($pdo, $username, $email, $password)) {
                $message = "Registrierung erfolgreich! Sie können sich jetzt einloggen.";
                $message_class = "alert-success";
            } else {
                $message = "Fehler beim Erstellen des Accounts.";
                $message_class = "alert-danger";
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>


<script>
    function togglePassword(inputId, btn) {
        const field = document.getElementById(inputId);
        if (field.type === "password") {
            field.type = "text";
            btn.textContent = "Hide";
        } else {
            field.type = "password";
            btn.textContent = "Show";
        }
    }
</script>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <h1 class="text-center mb-4">Registrierung</h1>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="username_eingeben" class="form-label">Benutzername</label>
                    <input type="text" class="form-control" id="username_eingeben" name="username"
                        value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password_eingabe" class="form-label">Passwort</label>
                    <div class="input-group">
                        <input type="password" id="password_eingabe" name="password_eingabe" class="form-control"
                            value="<?php echo htmlspecialchars($password ?? ''); ?>" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_eingabe', this)">Show</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_wiederholung" class="form-label">Passwort wiederholen</label>
                    <div class="input-group">
                        <input type="password" id="password_wiederholung" name="password_wiederholung" class="form-control"
                            value="<?php echo htmlspecialchars($password_wiederholen ?? ''); ?>" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_wiederholung', this)">Show</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3" name="sent">Registrieren</button>
            </form>

            <div class="text-center">
                <p>Bereits ein Konto? <a href="login.php" class="btn btn-secondary w-100">Zum Login</a></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>