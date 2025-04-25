<?php
require_once "functions/database.php";
require_once "functions/utils.php";
?>

<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>IT Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" defer></script>
    <script src="assets/js/darkmode.js" defer></script>

    <style>
        /* Custom sidebar styles */
        .sidebar {
            min-width: 220px;
            height: 100vh;
            position: sticky;
            top: 0;
            padding-top: 20px;
            background-color: #f8f9fa;
        }

        [data-bs-theme="dark"] .sidebar {
            background-color: #343a40;
        }

        .sidebar .nav-link {
            font-size: 16px;
        }

        .sidebar h4 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .sidebar .nav-item+.nav-item {
            margin-top: 15px;
        }

        .sidebar .btn {
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }

        #darkModeToggle {
            border-radius: 20px;
        }

        .sidebar .auth-buttons a,
        .sidebar .auth-buttons form {
            margin-top: 10px;
        }

        .sidebar .auth-buttons {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .top-nav {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }

        [data-bs-theme="dark"] .top-nav {
            background-color: #343a40;
            border-bottom: 2px solid #444;
            border-color: rgb(71, 80, 88);
        }

        .top-nav .navbar-brand {
            font-weight: bold;
        }

        .top-nav .navbar-nav .nav-link {
            font-size: 16px;
        }

        .search-bar {
            flex-grow: 1;
            display: flex;
            justify-content: center;
        }

        .search-bar input {
            width: 60%;
        }

        .top-nav .ms-auto {
            display: flex;
            gap: 15px;
        }

        [data-bs-theme="dark"] .navbar-text,
        [data-bs-theme="dark"] .nav-link {
            color: #ccc;
        }
    </style>

</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Top Nav Bar -->
    <nav class="navbar navbar-expand-sm navbar-light top-nav">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">IT Forum</a>
            <form class="d-flex search-bar">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            </form>
            <div class="d-flex ms-auto align-items-center">
                <?php if (!isset($_SESSION['username'])): ?>
                    <span class="navbar-text">Gast</span>
                    <a href="login.php" class="btn btn-outline-primary">Log in</a>
                    <a href="register.php" class="btn btn-primary">Sign up</a>
                <?php else: ?>
                    <div class="dropdown d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-flex align-items-center gap-2" type="button"
                            id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                            <img src="<?= $_SESSION['profilbild'] ?? 'uploads/user_avatars/default.png'; ?>"
                                alt="Profilbild" class="rounded-circle"
                                style="width: 32px; height: 32px; object-fit: cover;">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="profile.php">Zum Profil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar p-3 d-flex flex-column border-end border-body-subtle" style="min-width: 250px;">
            <!-- Main Nav Links -->
            <div class="mb-3">
                <ul class="nav flex-column">
                    <li class="nav-item border-bottom border-body-subtle">
                        <a href="index.php" class="nav-link text-body py-2">Beiträge</a>
                    </li>
                    <li class="nav-item border-bottom border-body-subtle">
                        <a href="profiles.php" class="nav-link text-body py-2">Benutzer</a>
                    </li>
                    <!-- <li class="nav-item border-bottom border-body-subtle">
                        <a href="delete_post.php" class="nav-link text-body py-2">Verwalten</a>
                    </li> -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item border-bottom border-body-subtle">
                            <a href="admin.php" class="nav-link text-body py-2">Admin Panel</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Bookmark Header -->
                <div class="mb-2 border-top border-body-subtle pt-3">
                    <h5 class="text-body mb-2">Lesezeichen</h5>
                </div>

                <!-- Bookmark List -->
                <div class="bookmark-list flex-grow-1 overflow-auto mb-3">
                    <ul class="nav flex-column">
                        <?php foreach (fetch_user_bookmarks($pdo, $_SESSION['user_id']) as $post): ?>
                            <li class="nav-item border-bottom border-body-subtle">
                                <a href=<?= "read_post.php?id=" . $post['id'] ?> class="nav-link text-body py-1">Post
                                    <?= nl2br(htmlspecialchars(substr($post['content'], 0, 15))) . (strlen($post['content']) > 15 ? '...' : '') ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif ?>

            <!-- Dark Mode Toggle -->
            <hr class="border-body-subtle my-3">
            <button id="darkModeToggle" class="btn btn-sm btn-secondary mt-auto">🌙</button>
        </nav>



        <main class="main-content flex-grow-1">
            <div class="container mt-4">