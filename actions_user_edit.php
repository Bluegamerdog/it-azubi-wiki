<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action to determine what to do
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Handle username change
        if ($action === 'edit_username' && isset($_POST['username'])) {
            $new_username = trim($_POST['username']);

            // Check if the new username is already taken
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$new_username]);
            $username_count = $stmt->fetchColumn();

            if ($username_count > 0) {
                $_SESSION['responseData'] = [
                    'response_message' => "❌ Dieser Benutzername ist bereits vergeben.",
                    'response_type' => "danger"
                ];
            } elseif (!empty($new_username)) {
                update_user($pdo, $user_id, ['username' => $new_username]);
                $_SESSION['responseData'] = [
                    'response_message' => "✔️ Benutzername erfolgreich geändert!",
                    'response_type' => "success"
                ];
            } else {
                $_SESSION['responseData'] = [
                    'response_message' => "❌ Benutzername darf nicht leer sein.",
                    'response_type' => "danger"
                ];
            }
        }

        // Handle email change
        if ($action === 'edit_email' && isset($_POST['email'])) {
            $new_email = trim($_POST['email']);
            if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                update_user($pdo, $user_id, ['email' => $new_email]);
                $_SESSION['responseData'] = [
                    'response_message' => "✔️ E-Mail erfolgreich geändert!",
                    'response_type' => "success"
                ];
            } else {
                $_SESSION['responseData'] = [
                    'response_message' => "❌ Ungültige E-Mail-Adresse.",
                    'response_type' => "danger"
                ];
            }
        }

        // Handle avatar upload
        if ($action === 'upload_avatar' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $uploadDir = 'uploads/user_avatars/';
            $fileTmp = $_FILES['avatar']['tmp_name'];
            $fileName = basename($_FILES['avatar']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExt, $allowedExts)) {
                $_SESSION['responseData'] = [
                    'response_message' => "❌ Ungültiger Dateityp.",
                    'response_type' => "danger"
                ];
            } else {
                $newFileName = "avatar_user_" . $user_id . "." . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    update_user($pdo, $user_id, ['profile_image_path' => $uploadPath]);
                    $_SESSION['profilbild'] = $uploadPath;
                    $_SESSION['responseData'] = [
                        'response_message' => "✔️ Avatar erfolgreich hochgeladen!",
                        'response_type' => "success"
                    ];
                } else {
                    $_SESSION['responseData'] = [
                        'response_message' => "❌ Fehler beim Verschieben der Datei.",
                        'response_type' => "danger"
                    ];
                }
            }
        }

        // Redirect to the profile page after updating
        header('Location: profile.php?id=' . $user_id);
        exit();
    }
}
?>