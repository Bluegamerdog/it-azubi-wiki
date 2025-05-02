<!-- Main Nav Links -->
<div class="mb-3">
    <ul class="nav flex-column">
        <li class="nav-item border-bottom border-body-subtle">
            <a href="index.php" class="nav-link text-body py-2 hover-effect">Forum</a>
        </li>
        <li class="nav-item border-bottom border-body-subtle">
            <a href="wiki.php" class="nav-link text-body py-2 hover-effect">Wiki</a>
        </li>
        <li class="nav-item border-bottom border-body-subtle">
            <a href="profiles.php" class="nav-link text-body py-2 hover-effect">Benutzer</a>
        </li>

        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'moderator')): ?>
            <li class="nav-item border-bottom border-body-subtle">
                <a href="admin.php" class="nav-link text-body py-2 hover-effect">Admin Panel</a>
            </li>
        <?php endif; ?>

    </ul>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Bookmark Header -->
    <div class="mb-2 border-top border-body-subtle pt-3">
        <h5 class="text-body mb-2">Lesezeichen</h5>
    </div>

    <!-- Bookmark List -->
    <div class="bookmark-list flex-grow-1 overflow-auto mb-3">
        <ul class="nav flex-column">
            <?php foreach (fetch_user_bookmarks($pdo, $_SESSION['user_id']) as $bookmarkedPost): ?>
                <li class="nav-item border-bottom border-body-subtle">
                    <a href=<?= "read_forum_post.php?id=" . $bookmarkedPost['id'] ?> class="nav-link hover-effect text-body py-1">
                        <?= nl2br(htmlspecialchars(substr($bookmarkedPost['content'], 0, 15))) . (strlen($bookmarkedPost['content']) > 15 ? '...' : '') ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif ?>

<!-- Dark Mode Toggle -->
<hr class="border-body-subtle my-3">
<button id="darkModeToggle" class="btn btn-sm btn-secondary w-100 mt-auto">🌙</button>