<?php
require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/utils.php';

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

$answerComment = fetch_answer_comment_by_post($pdo, $post['id']);
$author = isset($post['author_id']) && is_numeric($post['author_id']) ? fetch_user($pdo, $post['author_id']) : null;

$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;

// Reaktion verarbeiten (nur wenn User eingeloggt ist)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    if (isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        set_reaction($pdo, $post_id, $user_id, $_POST['reaction']);
    }
    if (isset($_POST['delete_post']) && ($user_role === 'admin' || $user_role === 'moderator')) {
        delete_post($pdo, $post_id);
        header('Location: index.php');
        exit();
    }
    header("Location: read_forum_post.php?id=$post_id");
    exit();
}

// Aktuelle Reaktionen zählen
$reactions = fetch_reaction_counts($pdo, $post_id);
$userReaction = $user_id ? fetch_user_reaction($pdo, $post_id, $user_id) : null;

include __DIR__ . '/includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Main Content Column (Center) -->
        <div class="col-md-12">
            <!-- Post Header: Profile picture, username, and post time -->
            <div class="d-flex align-items-center mb-2">
                <img src="<?= htmlspecialchars($author['profile_image_path'] ?? 'uploads/user_avatars/default.png'); ?>"
                    alt="Profilbild" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
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
            <h2><?= htmlspecialchars($post["title"]) ?></h2>
            <div class="mt-3">
                <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>
            </div>

            <?php if ($answerComment):
                $answerAuthor = fetch_user($pdo, $answerComment['author_id']); ?>
                <hr class="border-body-subtle my-3">
                <div class="mt-4 p-4 bg-success-subtle border border-success rounded">
                    <div class="d-flex align-items-center mb-2">
                        <img src="<?= htmlspecialchars($answerAuthor['profile_image_path'] ?? 'uploads/user_avatars/default.png') ?>"
                            alt="Answer Author" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                        <div>
                            <strong>
                                <a href="profile.php?id=<?= htmlspecialchars($answerAuthor['id']) ?>" class="text-decoration-none text-success-emphasis">
                                    <?= htmlspecialchars($answerAuthor['username']) ?>
                                </a>
                            </strong>
                            <small class="text-muted ms-1">·
                                <a href="<?= "read_forum_post.php?id=" . $answerComment['post_id'] . "#comment-" . $answerComment['id'] ?>" class="text-decoration-none">
                                    Lösung ansehen
                                </a>
                            </small>

                        </div>
                    </div>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($answerComment["content"])) ?></p>
                </div>
            <?php endif ?>
            <hr class="border-body-subtle my-3">

            <div class="d-flex flex-wrap gap-2 mt-4">
                <!-- Reactions -->
                <div class="btn-group" role="group" aria-label="Reaction buttons">
                    <form action="read_forum_post.php?id=<?= $post_id ?>" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="reaction">
                        <input type="hidden" name="post_id" value="<?= $post_id ?>">
                        <button class="btn <?= $userReaction === 'upvote' ? 'btn-success' : 'btn-outline-success' ?>"
                            type="submit" name="reaction" value="upvote" <?= $user_id ? '' : 'disabled' ?>>
                            👍 <?= $reactions['upvote'] ?>
                        </button>
                        <button class="btn <?= $userReaction === 'downvote' ? 'btn-danger' : 'btn-outline-danger' ?>"
                            type="submit" name="reaction" value="downvote" <?= $user_id ? '' : 'disabled' ?>>
                            👎 <?= $reactions['downvote'] ?>
                        </button>
                    </form>
                </div>

                <!-- Bookmark -->
                <?php if ($user_id):
                    $isBookmarked = is_post_bookmarked($pdo, $user_id, $post_id); ?>
                    <form action="actions/actions_post.php" method="post" class="d-inline">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                        <input type="hidden" name="isBookmarked" value="<?= $isBookmarked ? 1 : 0 ?>">
                        <button type="submit" class="btn <?= $isBookmarked ? 'btn-success' : 'btn-outline-info' ?>"
                            name="action" value="bookmark_post">📌 Lesezeichen</button>
                    </form>
                <?php endif; ?>

                <!-- Report -->
                <?php if ($user_id && !is_post_flagged($pdo, $post_id)): ?>
                    <form action="actions/actions_post.php" method="post" class="d-inline"
                        onsubmit="return confirm('Bist du sicher, dass du diesen Beitrag melden möchtest?');">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                        <button type="submit" class="btn btn-outline-danger" name="action" value="flag_post">🚩
                            Melden</button>
                    </form>
                <?php endif; ?>

                <!-- Edit -->
                <?php if ($user_id && $user_id == $post['author_id']): ?>
                    <form action="edit_post.php" method="POST" class="d-inline">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                        <button type="submit" class="btn btn-outline-secondary" name="action" value="edit_post">✏️
                            Bearbeiten</button>
                    </form>
                <?php endif; ?>

                <!-- Delete -->
                <?php if ($user_role === 'admin' || $user_role === 'moderator' || ($user_id && $user_id == $post['author_id'])): ?>
                    <form action="actions/actions_post.php" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this post?');" class="d-inline">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                        <button type="submit" class="btn btn-outline-danger" name="action" value="delete_post">🗑️
                            Löschen</button>
                    </form>
                <?php endif; ?>

                <!-- Wiki Submission -->
                <?php if ($user_role === 'admin' || $user_role === 'moderator'): ?>
                    <form action="submit_wiki.php?id=<?= $post_id ?>" method="POST" class="d-inline">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                        <button class="btn btn-outline-warning wiki-submit-btn">📚 Wiki Submission</button>
                    </form>
                <?php endif; ?>

            </div>


            <!-- Comment Form -->
            <?php if ($user_id): ?>
                <div class="mt-4">
                    <h5>Post a Comment</h5>
                    <form action="actions/actions_post.php" method="POST">
                        <textarea name="content" class="form-control" rows="4" placeholder="Write your comment..."
                            required></textarea>
                        <input type="hidden" name="post_id" value="<?= $post_id ?>">
                        <button type="submit" name="action" value="post_comment" class="btn btn-primary mt-2">Post
                            Comment</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php">Login</a> to post a comment.</p>
            <?php endif; ?>

            <hr class="border-body-subtle my-4">

            <!-- Comment Section -->
            <div class="mt-2">
                <h4>Comments</h4>

                <?php
                $comments = fetch_comments_by_post($pdo, $post_id);
                if ($comments):
                    foreach ($comments as $comment):
                        $commentAuthor = fetch_user($pdo, $comment['author_id']);
                ?>
                        <div id="comment-<?= htmlspecialchars($comment['id']) ?>"
                            class="mt-3 p-3 border rounded position-relative">
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?= htmlspecialchars($commentAuthor['profile_image_path'] ?? 'uploads/user_avatars/default.png') ?>"
                                    alt="Commentor" class="rounded-circle me-2"
                                    style="width: 30px; height: 30px; object-fit: cover;">
                                <strong>
                                    <a href="profile.php?id=<?= htmlspecialchars($commentAuthor['id']) ?>"
                                        class="text-decoration-none">
                                        <?= htmlspecialchars($commentAuthor['username']) ?>
                                    </a>
                                </strong>
                                <small class="text-muted ms-2"><?= htmlspecialchars(time_ago($comment['created_at'])) ?></small>
                            </div>

                            <p class="mt-2"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                            <div class="d-flex text-muted small ms-auto">
                                <div class="d-flex"></div>
                                <!-- Report Button -->
                                <?php if ($user_id && !is_comment_flagged($pdo, $comment['id'])): ?>
                                    <form action="actions/actions_post.php" method="POST" class="d-inline">
                                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                        <button type="submit" name="action" value="flag_comment" class="btn btn-sm btn-warning">
                                            Report
                                        </button>
                                    </form>
                                <?php endif ?>

                                <!-- Delete Button (for author, admin, or moderator) -->
                                <?php if (
                                    ($user_id && $comment['author_id'] == $user_id) ||
                                    ($user_role === 'admin' || $user_role === 'moderator')
                                ): ?>
                                    <form action="actions/actions_post.php" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                        <button type="submit" name="action" value="delete_comment" class="btn btn-sm btn-danger ms-2">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- ✅ Mark as Answer Button -->
                                <?php if ($answerComment && $answerComment['id'] !== $comment['id'] && $user_id && ($post['author_id'] === $user_id || $user_role === 'admin' || $user_role === 'moderator')): ?>
                                    <form action="actions/actions_post.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="mark_answer">
                                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                        <input type="hidden" name="post_id" value="<?= $post_id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success ms-2">
                                            ✅ Mark as Answer
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- ✅ Answer Badge -->
                                <?php if ($answerComment && $answerComment['id'] === $comment['id']): ?>
                                    <button type="submit" class="btn btn-sm btn-success ms-2">✅ Answer</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php else: ?>
                    <p>No comments yet.</p>
                <?php endif; ?>
            </div>



            <hr class="border-body-subtle my-4">
        </div>
    </div>
</div>

<!-- Wiki Form (Hidden by default) -->
<div id="wiki-form" style="display:none; margin-top:20px;">
    <form method="POST" action="submit_wiki.php">
        <input type="hidden" name="post_id" id="wiki-post-id">
        <label for="category">Kategorie auswählen:</label>
        <select name="category" id="category" required>
            <option value="1">Netzwerk</option>
            <option value="3">Programmieren</option>
            <option value="4">Betriebssysteme</option>
        </select>
        <button type="submit">Absenden</button>
    </form>
</div>

<script>
    document.querySelectorAll('.wiki-submit-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('wiki-form').style.display = 'block';
            document.getElementById('wiki-post-id').value = this.getAttribute('data-post-id');
        });
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>