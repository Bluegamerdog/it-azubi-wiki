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

    <div class="card mb-4">
        <div class="card-header">Flagged Content</div>

        <div class="card-body">
            <h10>Posts</h10>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Post Inhalt</th>
                        <th>Author des Posts</th>
                        <th>Gemeldet Von</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch flagged comments from the database
                    $flagged_posts = fetch_flagged_posts($pdo);
                    foreach ($flagged_posts as $flagged_post):
                        $reportingUser = fetch_user($pdo, $flagged_post['flagged_by']);
                        $reportedPost = fetch_post($pdo, $flagged_post['post_id']);
                        $reportedUser = fetch_user($pdo, $reportedPost['author_id']);
                        ?>
                        <tr>

                            <td><a href=<?= "read_forum_post.php?id=" . $reportedPost['id'] ?>><?= htmlspecialchars($reportedPost['content']) ?></a></td>
                            <td><a href=<?= "profile.php?id=" . $reportedUser['id'] ?>><?= htmlspecialchars($reportedUser['username']) ?></a>
                            <td><a href=<?= "profile.php?id=" . $reportingUser['id'] ?>><?= htmlspecialchars($reportingUser['username']) ?></a>
                            </td>
                            <td>
                                <form method="post" action="actions_admin.php" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?= $reportedPost['id'] ?>">
                                    <button type="submit" name="action" value="unflag_post" class="btn btn-sm btn-success"
                                        onclick="return confirm('Post wirklich entflaggen?')">Entflaggen</button>
                                </form>
                                <form method="post" action="actions_post.php" class="d-inline">
                                    <input type="hidden" name="post_id" value="<?= $reportedPost['id'] ?>">
                                    <button type="submit" name="action" value="delete_post" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Post wirklich lösen?')">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card-body">
            <h10>Comments</h10>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kommentar</th>
                        <th>Author des Kommentars</th>
                        <th>Gemeldet Von</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch flagged comments from the database
                    $flagged_comments = fetch_flagged_comments($pdo);
                    foreach ($flagged_comments as $comment):
                        $reportingUser = fetch_user($pdo, $comment['flagged_by']);
                        $reportedComment = fetch_comment($pdo, $comment['comment_id']);
                        $reportedUser = fetch_user($pdo, $reportedComment['author_id']);
                        ?>
                        <tr>

                            <td><a href=<?= "read_forum_post.php?id=" . $reportedComment['post_id'] . "#comment-" . $reportedComment['id'] ?>><?= htmlspecialchars($reportedComment['content']) ?></a></td>
                            <td><a href=<?= "profile.php?id=" . $reportedUser['id'] ?>><?= htmlspecialchars($reportedUser['username']) ?></a>
                            <td><a href=<?= "profile.php?id=" . $reportingUser['id'] ?>><?= htmlspecialchars($reportingUser['username']) ?></a>
                            </td>
                            <td>
                                <form method="post" action="actions_admin.php" class="d-inline">
                                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                    <button type="submit" name="action" value="unflag_comment"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Kommentar wirklich entflaggen?')">Entflaggen</button>
                                </form>
                                <form method="post" action="actions_post.php" class="d-inline">
                                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                    <button type="submit" name="action" value="delete_comment" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Kommentar wirklich lösen?')">Löschen</button>
                                </form>
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