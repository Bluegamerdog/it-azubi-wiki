<?php
require_once __DIR__  . '/functions/database.php';
require_once __DIR__  . '/functions/utils.php';

start_session();

// Prüfen ob Post-ID übergeben wurde
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = (int) $_GET['id'];
$post = fetch_post($pdo, $post_id);

if (!$post) {
    exit('Post with ID ' . $post_id . ' not found!');
}

$author = isset($post['author_id']) && is_numeric($post['author_id']) ? fetch_user($pdo, $post['author_id']) : null;

$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null; // Get the user's role from session

// Reaktion verarbeiten (nur wenn User eingeloggt ist)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    if (isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        set_reaction($pdo, $post_id, $user_id, $_POST['reaction']);
    }
    header("Location: read_forum_post.php?id=$post_id");
    exit();
}

// Aktuelle Reaktionen zählen
$reactions = fetch_reaction_counts($pdo, $post_id);
$userReaction = $user_id ? fetch_user_reaction($pdo, $post_id, $user_id) : null;

$pageTitle = "IT Wiki";
$pageHeader = "IT Wiki";
$pageHref = "wiki.php";
include __DIR__ . '/includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Main Content Column (Center) -->
        <div class="col-md-12">
            <!-- Post Header: Profile picture, username, and post time -->
            <div class="d-flex align-items-center mb-4">
                <img src="<?= htmlspecialchars($author['profile_image_path'] ?? 'uploads/user_avatars/default.png'); ?>"
                    alt="Profilbild" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <strong>
                            <?php if ($author): ?>
                                <a href="profile.php?id=<?= $author['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($author['username']) ?>
                                </a>
                            <?php else: ?>
                                deleted_user
                            <?php endif; ?>
                        </strong>
                        <!-- Created time -->
                        <small class="text-muted">· <?= htmlspecialchars(time_ago($post['created_at'])); ?></small>

                        <!-- Only show updated time if different -->
                        <?php if ($post['updated_at'] !== $post['created_at']): ?>
                            <small class="text-muted">(Zuletzt bearbeitet
                                <?= htmlspecialchars(time_ago($post['updated_at'], true)); ?>)</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Post Title and Content -->
            <h1><?= htmlspecialchars($post["title"]) ?></h1>
            <div class="mt-3">
                <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>
            </div>
            <!-- Show bookmark button for logged in users -->
            <?php if ($user_id && $post_id):
                $isBookmarked = is_post_bookmarked($pdo, $user_id, $post_id); ?>
                <form action="actions/actions_post.php" method="post" class="d-inline">
                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                    <input type="hidden" name="isBookmarked" value="<?= $isBookmarked ? 1 : 0 ?>">
                    <button type="submit" class="btn <?= $isBookmarked ? 'btn-success' : 'btn-info' ?>" name="action"
                        value="bookmark_post">Lesezeichen</button>
                </form>
            <?php endif; ?>

            <!-- Show delete button for admins and moderators -->
            <?php if ($user_role === 'admin' || $user_role === 'moderator'): ?>
                <form action="read_forum_post.php?id=<?= $post_id ?>" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this post?');" class="d-inline ms-2">
                    <button type="submit" class="btn btn-danger" name="delete_post" value=<?= $post_id ?>>Delete
                        Post</button>
                </form>
            <?php endif; ?>
        </div>

    </div>
</div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>