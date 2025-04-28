<?php
require_once "functions/database.php";
require_once 'functions/utils.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action to determine what to do
    $action = $_POST['action'] ?? null;
    if (!$action) {
        echo 'NO ACTION';
        exit;
    }

    if ($action === 'promote_moderator' && isset($_POST['user_id'])) {
        echo 'PROMOTED';
        update_user($pdo, $_POST['user_id'], ['role' => 'moderator']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'demote_moderator' && isset($_POST['user_id'])) {
        echo 'DEMOTED';
        update_user($pdo, $_POST['user_id'], ['role' => 'user']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'delete_user' && isset($_POST['user_id'])) {
        echo 'DELETED';
        delete_user($pdo, $_POST['user_id']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'unflag_comment' && isset($_POST['comment_id'])) {
        unflag_comment($pdo, $_POST['comment_id']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    // Handle case where no POST request is made (e.g., direct access)
    echo 'Invalid request method.';
    exit;
}


