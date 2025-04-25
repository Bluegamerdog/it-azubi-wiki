<?php
session_start();
require_once "functions/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit("Zugriff verweigert.");
}

$users = fetch_all_users($pdo);
include 'includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Admin Panel</h2>

    <div class="card mb-4">
        <div class="card-header">Benutzerverwaltung</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Benutzername</th>
                        <th>Rolle</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <?php if ($user['role'] == 'moderator'): ?>
                                    <form method="post" action="actions_admin.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="action" value="demote_moderator"
                                            class="btn btn-sm btn-success">Zum
                                            User machen</button>
                                    </form>
                                <?php endif ?>
                                <?php if ($user['role'] == 'user'): ?>
                                    <form method="post" action="actions_admin.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="action" value="promote_moderator"
                                            class="btn btn-sm btn-success">Zum
                                            Moderator machen</button>
                                    </form>
                                <?php endif ?>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <form method="post" action="actions_admin.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="action" value="delete_user" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Benutzer wirklich löschen?')">Löschen</button>
                                    </form>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Placeholder for future admin tools -->
    <div class="card">
        <div class="card-header">Weitere Werkzeuge</div>
        <div class="card-body">
            <p>Demnächst: Statistiken, Post-Moderation, Logs etc.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>