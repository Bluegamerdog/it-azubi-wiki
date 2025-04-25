<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';

$responseData = $_SESSION['responseData'] ?? [];
unset($_SESSION['responseData']);

$avatar = $user['profile_image_path'] ?? 'default.png';

$logged_in_user_id = $_SESSION['user_id'] ?? '';
$profile_user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : $logged_in_user_id;

$user = fetch_user($pdo, $profile_user_id);
if (!$user) { // Not logged in and no ?id= set
    header('Location: index.php');
    exit();
}

$avatar = $user['profile_image_path'] ?? 'default.png';


include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-15">
            <div class="card shadow-lg">
                <div class="card-body text-center">
                    <!-- Display current avatar -->
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="rounded-circle border mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">
                    <h3 class="mb-1"><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>

                    <!-- Action buttons for changing avatar, username, and email (only if the user is viewing their own profile) -->
                    <?php if ($profile_user_id == $logged_in_user_id): ?>
                        <div class="d-grid gap-2 d-md-block">
                            <form action="actions_user_edit.php" method="POST">
                                <input type="text" name="username" class="form-control mb-2"
                                    placeholder="Neuer Benutzername" value="<?= htmlspecialchars($user['username']) ?>">
                                <button type="submit" name="action" value="edit_username"
                                    class="btn btn-outline-secondary m-1">
                                    <i class="fas fa-user-edit me-1"></i> Benutzername ändern
                                </button>
                            </form>
                            <form action="actions_user_edit.php" method="POST">
                                <input type="email" name="email" class="form-control mb-2" placeholder="Neue E-Mail"
                                    value="<?= htmlspecialchars($user['email']) ?>">
                                <button type="submit" name="action" value="edit_email"
                                    class="btn btn-outline-secondary m-1">
                                    <i class="fas fa-envelope me-1"></i> E-Mail ändern
                                </button>
                            </form>
                        </div>

                        <!-- Avatar upload form -->
                        <form action="actions_user_edit.php" method="POST" enctype="multipart/form-data" class="mt-3">
                            <input type="file" name="avatar" accept="image/*">
                            <button type="submit" name="action" value="upload_avatar"
                                class="btn btn-outline-success mt-2">Avatar Hochladen</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($responseData)): ?>
                        <div class="alert alert-<?php echo $responseData['response_type']; ?>">
                            <?php echo htmlspecialchars($responseData['response_message']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>