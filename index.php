<?php
session_start();
require_once 'functions/database.php';
require_once 'functions/utils.php';
include 'includes/header.php';

// Get the filters from the GET request
$sorted_by = isset($_GET['sorted_by']) ? $_GET['sorted_by'] : 'newest';
$days_old = isset($_GET['days_old']) ? $_GET['days_old'] : 'all';

// Fetch the posts based on filters
$posts = fetch_all_posts($pdo, $sorted_by, $days_old);
$postCount = count($posts);


switch ($sorted_by) {
    case 'oldest':
        $title = "Älteste Beiträge";
        break;
    case 'most_activity':
        $title = "Beiträge mit den meisten Aktivitäten";
        break;
    case 'trending':
        $title = "Trendige Beiträge";
        break;
    case 'no_comments':
        $title = "Beiträge ohne Kommentare";
        break;
    default:
        $title = "Neuste Beiträge";
        break;
}

if ($days_old != 'all') {
    $title .= " - Letzte $days_old Tage";
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><class="fw-bold"><?= $title ?></h1>
        <a href="create_post.php" class="btn btn-primary">Create Post</a>
    </div>

    <!-- Filters Section -->
    <form method="GET" class="mb-5">
        <div class=" justify-content-between  ">
            <p class="mb-2 text-muted ms-auto">
                <?= $postCount ?> <?= $postCount == 1 ? "Beitrag" : "Beiträge" ?>
            </p>
        </div>
        <div class="row g-2">
            <div class="col-md-2  ms-auto">
                <label for="sorted_by" class="form-label">Sorted by</label>
                <select name="sorted_by" class="form-select form-select-sm">
                    <option value="newest" <?= $sorted_by == 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest" <?= $sorted_by == 'oldest' ? 'selected' : '' ?>>Oldest</option>
                    <option value="most_activity" <?= $sorted_by == 'most_activity' ? 'selected' : '' ?>>Most activity
                    </option>
                    <option value="trending" <?= $sorted_by == 'trending' ? 'selected' : '' ?>>Trending</option>
                    <option value="no_comments" <?= $sorted_by == 'no_comments' ? 'selected' : '' ?>>No Comments</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="days_old" class="form-label">Days old</label>
                <select name="days_old" class="form-select form-select-sm">
                    <option value="7" <?= $days_old == '7' ? 'selected' : '' ?>>Last 7 days</option>
                    <option value="30" <?= $days_old == '30' ? 'selected' : '' ?>>Last 30 days</option>
                    <option value="90" <?= $days_old == '90' ? 'selected' : '' ?>>Last 90 days</option>
                    <option value="all" <?= $days_old == 'all' ? 'selected' : '' ?>>All time</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-sm">Apply Filters</button>
            </div>
        </div>
    </form>

    <!-- Posts Section -->
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Es sind noch keine Beiträge vorhanden.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($posts as $post):
                $comments = fetch_comments_by_post($pdo, $post['id']);
                $commentCount = is_array($comments) ? count($comments) : 0;
                $votes = fetch_reaction_counts($pdo, $post['id']);
                $voteCount = ($votes['upvote'] ?? 0) + ($votes['downvote'] ?? 0);
                $author = isset($post['author_id']) && is_numeric($post['author_id']) ? fetch_user($pdo, $post['author_id']) : null;
                ?>
                <div class="post-card list-group-item list-group-item-action mb-3 rounded shadow-sm border-2 p-3">

                    <!-- Post Title and Content -->
                    <h5 class="fw-semibold">
                        <a href="read_post.php?id=<?= htmlspecialchars($post['id']) ?>"
                            class="text-decoration-none link-primary">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h5>
                    <p class="mb-2 text-muted small">
                        <?= nl2br(htmlspecialchars(substr($post['content'], 0, 180))) . (strlen($post['content']) > 180 ? '...' : '') ?>
                    </p>


                    <!-- Votes, Comments, Author and Date Section -->
                    <div class="d-flex justify-content-between text-muted small">
                        <div class="d-flex">
                            <p class="mb-0 me-2"><?= $voteCount . ($voteCount == 1 ? ' Vote' : ' Votes') ?>
                            <p class="mb-0"><?= $commentCount . ($commentCount == 1 ? ' Kommentar' : ' Kommentare') ?>
                        </div>

                        <div>
                            <?= 'Von <strong class="ms-1 me-1">' . htmlspecialchars($author['username'] ?? 'deleted_user') . '</strong>〢 ' . time_ago($post['created_at']) ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>