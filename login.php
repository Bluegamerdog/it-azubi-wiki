<?php

session_start();

require_once "functions/database.php";
include 'includes/header.php';
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user inputs from the form
    $username = trim(htmlspecialchars($_POST["username"]));
    $password = trim(htmlspecialchars($_POST["password"]));

    // Fetch user by username
    $user = fetch_user_by_username($pdo, $username);

    // Check if the user exists and if the password is correct
    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
        // User successfully authenticated
        $_SESSION['username'] = $username; // Store username in session
        header("Location: create_post.php"); //<----------------Nächste Seite eintragen
        exit;
    } else {
        // Invalid login attempt
        $error_message = "Falscher Benutzername oder Passwort!";
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    
    <?php
    // Display error message if any
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>

    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Log In</button>
    </form>

    <p>Hast du noch keinen Account?</p>
    <form action="register.php" method="get">
        <button type="submit" class="btn btn-secondary mb-3">Klicke hier um dich zu Registrieren!</button>
    </form>
    <?php
    include 'includes/footer.php';
    ?>
</body>
</html>