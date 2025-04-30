<?php
require_once __DIR__  . "/functions/database.php";
require_once __DIR__  . "/functions/utils.php";
start_session();

if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit(); // Already logged in
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user inputs from the form
    $username = trim(htmlspecialchars($_POST["username"]));
    $password = trim(htmlspecialchars($_POST["password"]));

    // Fetch user by username from the database
    $user = fetch_user_by_username($pdo, $username);

    // Check if the user exists and if the password is correct
    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
        // User successfully authenticated
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // 'user', 'moderator', 'admin'
        $_SESSION['profilbild'] = $user['profile_image_path'];
        session_write_close();
        header("Location: index.php");
        exit();
    } else {
        // Invalid login attempt
        $error_message = "Falscher Benutzername oder Passwort!";
    }
}

include __DIR__ . '/includes/header.php';
?>

<script>
    function togglePassword() {
        const passwordField = document.getElementById("password");
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    }
</script>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <h1 class="text-center mb-4">Login</h1>

            <?php
            // Display error message if any
            if (isset($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Benutzername</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Passwort</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">⓿</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <p>Hast du noch keinen Account?</p>
                <a href="register.php" class="btn btn-secondary w-100">Klicke hier um dich zu Registrieren!</a>
            </div>
        </div>
    </div>
</div>