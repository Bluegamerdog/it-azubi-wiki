<?php
require_once __DIR__ . "/functions/database.php";
require_once __DIR__ . "/functions/utils.php";
start_session();

$users = fetch_all_users($pdo);
include __DIR__ . '/includes/header.php';
?>

<style>
    .profile-img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 50%;
        transition: transform 0.2s ease-in-out;
    }

    .profile-img:hover {
        transform: scale(1.3);
        z-index: 10;
        position: relative;
    }

    .user-card {
        transition: box-shadow 0.2s;
    }

    .user-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container py-5">
    <h1 class="text-center mb-4">Alle Benutzer</h1>

    <?php
    $usersPerPage = 16;
    $totalUsers = count($users);
    $totalPages = ceil($totalUsers / $usersPerPage);
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $startIndex = ($currentPage - 1) * $usersPerPage;
    $paginatedUsers = array_slice($users, $startIndex, $usersPerPage);
    ?>

    <?php if (!empty($paginatedUsers)): ?>
        <div class="row g-4">
            <?php foreach ($paginatedUsers as $user): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card user-card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <a href="profile.php?id=<?= $user['id'] ?>">
                                <img src="<?= $user['profile_image_path'] ?? 'uploads/user_avatars/default.png'; ?>"
                                    alt="Profilbild"
                                    class="me-3 profile-img"
                                    title="Profil von <?= htmlspecialchars($user['username']); ?>">
                            </a>
                            <div>
                                <h5 class="mb-1">
                                    <a href="profile.php?id=<?= $user['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </a>
                                </h5>
                                <small class="text-muted">Registriert am <?= htmlspecialchars($user['created_at']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalUsers > $usersPerPage): ?>
            <nav aria-label="Seitennavigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i === $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Keine Benutzer gefunden.
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>