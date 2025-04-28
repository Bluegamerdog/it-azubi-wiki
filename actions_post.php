<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action to determine what to do
    $action = $_POST['action'] ?? null;
    if (!$action) {
        echo 'NO ACTION';
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? null;

    if (!isset($user_id)) {
        echo 'Unauthorized.';
        exit;
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
        $comment_id = $_POST['comment_id'];
        flag_comment($pdo, $comment_id, $user_id);

        // Redirect back to the same page after reporting
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
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
            echo 'You do not have permission to delete this comment.';
            exit;
        }

    } elseif ($action === 'delete_post' && isset($_POST['post_id'])) {
        $post = fetch_post($pdo, $_POST['post_id']);
        if (!$post) {
            echo 'Post not found.';
            exit;
        }
        if ($post['author_id'] === $user_id || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderator') {
            delete_post($pdo, $_POST['post_id']);

            header("Location: index.php");
            exit;
        } else {
            echo 'You do not have permission to delete this post.';
            exit;
        }
    } elseif ($action === 'reaction' && isset($_POST['post_id']) && isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        $post = fetch_post($pdo, $_POST['post_id']);
        if (!$post) {
            echo 'Post not found.';
            exit;
        }
        set_reaction($pdo, $_POST['post_id'], $user_id, $_POST['reaction']);
    }
} else {
    // Handle case where no POST request is made (e.g., direct access)
    echo 'Invalid request method.';
    exit;
}


