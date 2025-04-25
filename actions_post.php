<?php
require_once "functions/database.php";
require_once 'functions/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action to determine what to do
    $action = $_POST['action'] ?? null;
    if (!$action) {
        echo 'NO ACTION';
        exit;
    }

    if ($action === 'bookmark_post' && isset($_POST['post_id'], $_POST['user_id'])) {
        $post_id = $_POST['post_id'];
        $user_id = $_POST['user_id'];
        $isBookmarked = (bool) $_POST['isBookmarked'];

        var_dump($_POST);
        var_dump($isBookmarked);

        if ($isBookmarked) {
            echo 'ubnbookmarked';
            unbookmark_post($pdo, $user_id, $post_id);
        } else {
            echo 'bookmarked';
            bookmark_post($pdo, $user_id, $post_id);
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    // Handle case where no POST request is made (e.g., direct access)
    echo 'Invalid request method.';
    exit;
}


