<?php
require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/utils.php';
start_session();

// Get the filters from the GET request
$sorted_by = $_GET['sorted_by'] ?? 'newest';
$days_old = $_GET['days_old'] ?? 'all';
$category_id = $_GET['category_id'] ?? 'all';

// Fetch the posts based on filters
$posts = fetch_all_wiki_posts($pdo, $sorted_by, $days_old, $category_id); // true = nur wiki
$postCount = count($posts);

$wikiCategories = fetch_all_wiki_categories($pdo);

switch ($sorted_by) {
    case 'oldest':
        $title = "Ältesten Wiki Beiträge";
        break;
    default:
        $title = "Neusten Wiki Beiträge";
        break;
}

if ($days_old != 'all') {
    $title .= " - Letzte $days_old Tage";
}
$pageTitle = "IT Wiki";
$pageHeader = "IT Wiki";
$pageHref = "wiki.php";
include __DIR__ . '/includes/header.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><?= $title ?></h1>
    </div>

    <!-- Filters Section -->
    <form method="GET" class="mb-5">
        <div class=" justify-content-between">
            <p class="mb-2 text-muted ms-auto">
                <?= $postCount ?> <?= $postCount == 1 ? "Beitrag" : "Beiträge" ?>
            </p>
        </div>
        <div class="row g-2">
            <div class="col-md-2  ms-auto">
                <label for="category_id" class="form-label">Kategory</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value=<?= 'all' ?> <?= $category_id == 'all' ? 'selected' : '' ?>>Alle</option>
                    <option value=<?= 0 ?> <?= $category_id == 0 ? 'selected' : '' ?>>Keine</option>
                    <?php foreach ($wikiCategories as $wikiCategory): ?>
                        <option value=<?= $wikiCategory['id'] ?>     <?= $category_id == $wikiCategory['id'] ? 'selected' : '' ?>>
                            <?= $wikiCategory['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sorted_by" class="form-label">Sorted by</label>
                <select name="sorted_by" class="form-select form-select-sm">
                    <option value="newest" <?= $sorted_by == 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest" <?= $sorted_by == 'oldest' ? 'selected' : '' ?>>Oldest</option>
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
                $author = isset($post['author_id']) && is_numeric($post['author_id']) ? fetch_user($pdo, $post['author_id']) : null;
                ?>
                <div class="post-card list-group-item list-group-item-action mb-3 rounded shadow-sm border-2 p-3">

                    <!-- Post Title and Content -->
                    <h5 class="fw-semibold">
                        <a href="read_wiki_post.php?id=<?= htmlspecialchars($post['id']) ?>"
                            class="text-decoration-none link-primary">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h5>
                    <p class="mb-2 text-muted small">
                        <?= nl2br(htmlspecialchars(substr($post['content'], 0, 180))) . (strlen($post['content']) > 180 ? '...' : '') ?>
                    </p>


                    <!-- Author and Date Section -->
                    <div class="d-flex justify-content-between text-muted small">
                        <div class="d-flex"></div>
                        <div>
                            <?= 'Von <strong class="ms-1 me-1">' . htmlspecialchars($author['username'] ?? 'deleted_user') . '</strong>〢 ' . time_ago($post['created_at']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>