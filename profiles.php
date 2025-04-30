
<?php
session_start();
require_once "functions/database.php";
require_once "functions/utils.php";
include 'includes/header.php';

$users = fetch_all_users($pdo);
?>
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa; /* leichtes Grau */
        cursor: pointer;
    }
</style>
<style>
    .profile-img {
        transition: transform 0.15s ease-in-out;
    }

    .profile-img:hover {
        transform: scale(4); /* Vergrößert das Bild */
        z-index: 10;
        position: relative;
    }
</style>


<div class="container py-5">
<?php
$usersPerPage = 10;
$totalUsers = count($users);
$totalPages = ceil($totalUsers / $usersPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startIndex = ($currentPage - 1) * $usersPerPage;
$paginatedUsers = array_slice($users, $startIndex, $usersPerPage);
?>
<div class="container py-5">
    <h1 class="text-center mb-4">Alle Benutzer</h1>
    <form class="mb-4" action="user_list.php" method="get">
</form>
    <?php if (!empty($paginatedUsers)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Benutzername</th>
                        <th>Registriert am</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paginatedUsers as $user): ?>
                        <tr>
<td>
<?= htmlspecialchars($user['username']) ?>
    <a href="profile.php?id=<?= $user['id'] ?>" class="avatar-wrapper">
        <img src="<?= $user['profile_image_path'] ?? 'uploads/user_avatars/default.png'; ?>"
             alt="Profilbild"
             class="rounded-circle profile-img"
             style="width: 32px; height: 32px; object-fit: cover;"
             title="Profil von <?= htmlspecialchars($user['username']); ?>">
    </a>
    </td>
    <td><?= htmlspecialchars($user['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginierung -->
        <nav aria-label="Seitenavigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i === $currentPage) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Keine Benutzer gefunden.
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
