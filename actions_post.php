<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action to determine what to do
    $action = $_POST['action'] ?? null;
    if (!$action) {
        exit('NO ACTION');
    }

    $user_id = $_SESSION['user_id'] ?? null;

    if (!isset($user_id)) {
        exit('Unverified');
    }

    if ($action === 'bookmark_post' && isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];
        $isBookmarked = (bool) $_POST['isBookmarked'];

        if ($isBookmarked) {
            unbookmark_post($pdo, $user_id, $post_id);
        } else {
            bookmark_post($pdo, $user_id, $post_id);
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'post_comment' && isset($_POST['content']) && isset($_POST['post_id'])) {
        create_comment($pdo, $_POST['post_id'], $user_id, $_POST['content']);

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'flag_comment' && isset($_POST['comment_id'])) {
        $comment_id = (int) $_POST['comment_id'];
        flag_comment($pdo, $comment_id, $user_id);

        // Redirect back to the same page after reporting
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'flag_post' && isset($_POST['post_id'])) {
        $post_id = (int) $_POST['post_id'];
        flag_post($pdo, $post_id, $user_id);

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } elseif ($action === 'delete_comment' && isset($_POST['comment_id'])) {
        $comment_id = $_POST['comment_id'];

        // Fetch the comment to check if the current user can delete it
        $comment = fetch_comment($pdo, $comment_id);
        if (!$comment) {
            echo 'Comment not found.';
            exit;
        }
        if ($comment['author_id'] === $user_id || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderator') {
            delete_comment($pdo, $comment_id);

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            // You do not have permission to delete this comment.
            exit('Unauthorized');
        }

    } elseif ($action === 'delete_post' && isset($_POST['post_id'])) {
        $post = fetch_post($pdo, $_POST['post_id']);
        if (!$post) {
            exit('Post not found.');
        }
        if ($post['author_id'] === $user_id || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderator') {
            delete_post($pdo, $_POST['post_id']);

            header("Location: index.php");
            exit;
        } else {
            // You do not have permission to delete this post.
            exit('Unauthorized');
        }
    } elseif ($action === 'reaction' && isset($_POST['post_id']) && isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        $post = fetch_post($pdo, $_POST['post_id']);
        if (!$post) {
            exit('Post not found.');
        }
        set_reaction($pdo, $_POST['post_id'], $user_id, $_POST['reaction']);
    }
} else {
    // Handle case where no POST request is made (e.g., direct access)
    exit('Invalid request method.');
}


