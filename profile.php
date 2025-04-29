<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';

$responseData = $_SESSION['responseData'] ?? [];
unset($_SESSION['responseData']);

$logged_in_user_id = $_SESSION['user_id'] ?? '';
$profile_user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : $logged_in_user_id;

$user = fetch_user($pdo, $profile_user_id);
if (!$user) {
    header('Location: index.php');
    exit();
}

$avatar = $user['profile_image_path'] ?? 'default.png';

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="rounded-circle border shadow-sm mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <!-- Benutzerinfos -->
                    <h3 class="fw-bold"><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>

                    <!-- Feedback -->
                    <?php if (!empty($responseData)): ?>
                        <div class="alert alert-<?= htmlspecialchars($responseData['response_type']) ?>">
                            <?= htmlspecialchars($responseData['response_message']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Nur eigener Account -->
                    <?php if ($profile_user_id == $logged_in_user_id): ?>
                        <!-- Username ändern -->
                        <form action="actions_user_edit.php" method="POST" class="mb-3">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Benutzername">
                                <label for="username">Neuer Benutzername</label>
                            </div>
                            <button type="submit" name="action" value="edit_username" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-edit me-1"></i> Benutzername ändern
                            </button>
                        </form>

                        <!-- E-Mail ändern -->
                        <form action="actions_user_edit.php" method="POST" class="mb-3">
                            <div class="form-floating mb-2">
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="E-Mail">
                                <label for="email">Neue E-Mail</label>
                            </div>
                            <button type="submit" name="action" value="edit_email" class="btn btn-outline-primary w-100">
                                <i class="fas fa-envelope me-1"></i> E-Mail ändern
                            </button>
                        </form>

                        <!-- Avatar hochladen -->
                        <form action="actions_user_edit.php" method="POST" enctype="multipart/form-data" class="mb-3">
                            <input type="file" name="avatar" class="form-control mb-2" accept="image/*">
                            <button type="submit" name="action" value="upload_avatar" class="btn btn-outline-success w-100">
                                <i class="fas fa-upload me-1"></i> Avatar hochladen
                            </button>
                        </form>
                    <?php endif; ?>

                    <!-- Admin-Löschaktion -->
                    <?php if ($user['role'] !== 'admin'): ?>
                        <form method="post" action="actions_admin.php" class="mt-3">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" name="action" value="delete_user" class="btn btn-danger w-100"
                                onclick="return confirm('Benutzer wirklich löschen?')">
                                <i class="fas fa-trash-alt me-1"></i> Benutzer löschen
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
