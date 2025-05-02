<?php
require_once __DIR__  . "/../functions/database.php";
require_once __DIR__  . '/../functions/utils.php';
start_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action to determine what to do
    $action = $_POST['action'] ?? null;
    if (!$action) {
        exit('NO ACTION');
    }

    $user_id = (int) $_SESSION['user_id'] ?? null;

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
    } elseif ($action === 'edit_post' && isset($_POST['post_id']) && isset($_POST["title"]) && isset($_POST["content"])) {
        $post = fetch_post($pdo, $_POST['post_id']);
        if (!$post) {
            exit('Post not found.');
        }
        if ($post['author_id'] !== $user_id) {
            // You do not have permission to edit this post.
            exit('Unauthorized');
        }
        update_post($pdo, $_POST['post_id'], $_POST["title"], $_POST["content"]);

        header("Location: read_forum_post.php?id=" . $_POST['post_id']);
        exit;
    } elseif ($action === 'reaction' && isset($_POST['post_id']) && isset($_POST['reaction']) && in_array($_POST['reaction'], ['upvote', 'downvote'])) {
        $post = fetch_post($pdo, $_POST['post_id']);
        if (!$post) {
            exit('Post not found.');
        }
        set_reaction($pdo, $_POST['post_id'], $user_id, $_POST['reaction']);
    } elseif ($action === 'mark_answer' && isset($_POST['comment_id'], $_POST['post_id'])) {
        $comment_id = (int) $_POST['comment_id'];
        $post_id = (int) $_POST['post_id'];

        $comment = fetch_comment($pdo, $comment_id);
        $post = fetch_post($pdo, $post_id);

        if (!$comment || !$post) {
            exit('Kommentar oder Beitrag nicht gefunden.');
        }

        // Nur Autor des Beitrags oder Admin/Moderator darf markieren
        if ($post['author_id'] === $user_id || in_array($_SESSION['role'], ['admin', 'moderator'])) {
            mark_comment_as_answer($pdo, $comment_id);
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            // 'Sie dürfen diesen Kommentar nicht als richtige Antwort markieren.'
            exit('Unauthorized');
        }
    }
} else {
    // Handle case where no POST request is made (e.g., direct access)
    exit('Invalid request method.');
}
