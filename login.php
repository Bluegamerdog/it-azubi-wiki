<?php

session_start();

require_once "functions/database.php"; 

// Initialize error message
$error_message = "";

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user inputs from the form
    $username = trim(htmlspecialchars($_POST["username"]));
    $password = trim(htmlspecialchars($_POST["password"]));

    // Fetch username and password from the users table
    $users = fetch_all_users($username); // Function to fetch a user by username

    // Check if username and password are correct
    if ($users && password_verify($password, $users['password'])) {
        // User successfully authenticated
        $_SESSION['username'] = $username; // Store username in session
        header("Location: post_alleausgeben.php"); // Redirect to the next page
        exit;
    } else {
        // Set error message if authentication fails
        $error_message = "Incorrect username or password. Please try again.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <!-- Display error message if available -->
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Log In</button>
    </form>
    <p>Don't have an account?</p>
    <form action="register.php" method="get">
        <button type="submit" class="btn btn-secondary mb-3">Click here to register.</button>
    </form>
</body>
</html>