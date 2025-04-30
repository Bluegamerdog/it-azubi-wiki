<?php

require_once __DIR__  . '/functions/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wiki_category_id = $_POST['wiki_category_id'] ?? NULL; // Taghiri be estefade az sotune wiki_category_id
    $is_wiki_entry = $_POST['is_wiki_entry'] ?? 1;
    $post_id = $_POST['post_id'];

    // Update kar shodan categoriyah
    $stmt = $pdo->prepare("UPDATE posts SET wiki_category_id = :wiki_category_id, is_wiki_entry =:is_wiki_entry WHERE id = :post_id");
    $stmt->execute([
        'wiki_category_id' => $wiki_category_id,
        'is_wiki_entry' => $is_wiki_entry,
        'post_id' => $post_id
    ]);

    // Redirection or confirmation message
    header('Location: read_forum_post.php?id=' . $post_id);
    exit();
}
?>

<form action="submit_wiki.php" method="POST">
    <div class="mb-3">
        <label for="wiki_category" class="form-label">Kategori</label>
        <select class="form-control" id="wiki_category" name="wiki_category_id" required>
            <option value="">Kategori ra entekhab konid...</option>
            <?php
            // Gireftane hame categories
            $categories = fetch_all_wiki_categories($pdo);
            foreach ($categories as $category) {
                echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Mojzaye Wiki</label>
        <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
    </div>
    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
    <button type="submit" class="btn btn-primary">Ejra kon</button>
</form>