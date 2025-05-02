<?php
require_once __DIR__ . "/../functions/database.php";
require_once __DIR__ . "/../functions/utils.php";

verifyLoginState($pdo);
?>

<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? "IT Forum") ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" defer></script>
    <script src="assets/js/darkmode.js" defer></script>

    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">

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
            position: sticky;
            top: 0;
            z-index: 1030;
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

        .flex-wrapper {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>

</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Top Nav Bar -->
    <nav class="navbar navbar-expand-lg navbar-light top-nav">
        <div class="container-fluid">
            <!-- Offcanvas toggle button for small screens -->
            <button class="btn btn-outline-secondary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                ☰
            </button>

            <a class="navbar-brand" href="<?= htmlspecialchars($pageHref ?? "index.php") ?>"><?= htmlspecialchars($pageHeader ?? "IT Forum") ?></a>

            <!-- Collapsible search input -->
            <form class="d-none d-md-flex search-bar mx-auto">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            </form>

            <!-- Mobile Search Button -->
            <button class="d-flex d-md-none btn btn-outline-primary search-btn mx-auto" type="button" id="searchButton">
                <i class="bi bi-search"></i>
            </button>

            <!-- Collapsing Search Bar for Mobile -->
            <div class="collapse" id="searchCollapse">
                <form class="d-md-none search-bar mx-auto mt-2">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                </form>
            </div>
            <!-- Right side: auth or user dropdown -->
            <div class="d-flex ms-auto align-items-center">
                <?php if (!isset($_SESSION['username'])): ?>
                    <span class="navbar-text me-2">Gast</span>
                    <a href="login.php" class="btn btn-outline-primary me-2">Log in</a>
                    <a href="register.php" class="btn btn-primary">Sign up</a>
                <?php else: ?>
                    <div class="dropdown d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-flex align-items-center gap-2" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                            <img src="<?= get_profile_image_path() ?>" alt="" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
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
        <!-- Offcanvas Sidebar for small screens -->
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title fw-bold" id="sidebarOffcanvasLabel">Menü</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body px-3 pt-2">
                <div class="sidebar-content">
                    <?php include 'sidebar_content.php'; ?>
                </div>
            </div>
        </div>

        <!-- Static Sidebar for large screens -->
        <nav class="sidebar p-3 d-none d-lg-flex flex-column border-end border-body-subtle" style="min-width: 250px;">
            <?php include 'sidebar_content.php'; ?>
        </nav>

        <main class="main-content flex-grow-1 flex-wrapper">
            <div class="container mt-4">