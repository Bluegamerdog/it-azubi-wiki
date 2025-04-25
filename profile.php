<!-- User Id in header "profile?id=" -->
<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';
include 'includes/header.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = fetch_user($pdo, $user_id);
$avatar = $user['profile_image_path'] ?? 'default.png'; // Falls kein Avatar gesetzt ist
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <style>
        .profile-container {
            max-width: 500px;
            margin: 50px auto;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <h1>Willkommen, <?= htmlspecialchars($user['username']) ?></h1>
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="avatar">
        <p><a href="upload_avatar.php">Avatar ändern</a></p>
    </div>
</body>

</html>