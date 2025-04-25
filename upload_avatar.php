<?php
session_start();
require_once "functions/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$uploadMessage = "";

// Wenn das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $uploadDir = 'uploads/user_avatars/';
    $fileTmp = $_FILES['avatar']['tmp_name'];
    $fileName = basename($_FILES['avatar']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExt, $allowedExts)) {
        $uploadMessage = "❌ Ungültiger Dateityp.";
    } else {
        $newFileName = "avatar_user_" . $user_id . "." . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_image_path = :profile_image_path WHERE id = :id");
            $stmt->execute([
                ':profile_image_path' => $uploadPath,
                ':id' => $user_id
            ]);
            $_SESSION['profilbild'] = $uploadPath;
            $uploadMessage = "✅ Avatar erfolgreich hochgeladen!<br><a href='profile.php'>Zurück zum Profil</a>";
        } else {
            $uploadMessage = "❌ Fehler beim Verschieben der Datei.";
        }
    }
}
?>

<h1>Avatar Hochladen</h1>

<?php if ($uploadMessage): ?>
    <p><?= $uploadMessage ?></p>
<?php endif; ?>

<form action="upload_avatar.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar" accept="image/*" required>
    <button type="submit">Hochladen</button>
</form>

<?php include 'includes/footer.php'; ?>