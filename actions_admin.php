<?php
session_start();
require_once "functions/database.php";
require_once 'functions/utils.php';

$permissionsTable = [
    'promote_moderator' => ['admin'],
    'demote_moderator' => ['admin'],
    'delete_user' => ['admin'],
    'unflag_comment' => ['admin', 'moderator'],
    'unflag_post' => ['admin', 'moderator'],
];

function canPerformAction($action, $userRole, $userId, $targetUserId = null)
{
    global $permissionsTable;

    $allowedRoles = $permissionsTable[$action] ?? [];

    // Special case: allow users to delete themselves
    if ($action === 'delete_user' && $userId === $targetUserId) {
        return true;
    }

    return in_array($userRole, $allowedRoles);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check permissions
    $action = $_POST['action'] ?? null;
    if (!$action) {
        echo 'NO ACTION';
        exit;
    }
    $permissionSet = $permissionsTable[$action] ?? null;
    if (!$permissionSet) {
        echo 'NO PERMISSIONS SET UP FOR ACTION';
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $user_role = $_SESSION['role'] ?? null;
    if (!isset($user_id) || !isset($user_role)) {
        echo 'Unverified.';
        exit;
    }

    $targetUserId = (int) $_POST['user_id'] ?? null;
    if (!canPerformAction($action, $user_role, $user_id, $targetUserId)) {
        echo 'Unauthorized.';
        exit;
    }

    // Handle Action
    if ($action === 'promote_moderator' && isset($_POST['user_id'])) {
        update_user($pdo, $_POST['user_id'], ['role' => 'moderator']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'demote_moderator' && isset($_POST['user_id'])) {
        update_user($pdo, $_POST['user_id'], ['role' => 'user']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'delete_user' && isset($_POST['user_id'])) {
        delete_user($pdo, $_POST['user_id']);
        if ($user_id === $targetUserId) {
            session_unset();
            session_destroy();
            header("Location: login.php");
        } else {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        exit;
    } elseif ($action === 'unflag_comment' && isset($_POST['comment_id'])) {
        unflag_comment($pdo, $_POST['comment_id']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'unflag_post' && isset($_POST['post_id'])) {
        unflag_post($pdo, $_POST['post_id']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    // Handle case where no POST request is made (e.g., direct access)
    echo 'Invalid request method.';
    exit;
}


