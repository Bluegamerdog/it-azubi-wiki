<!DOCTYPE html>
<html lang="de" data-bs-theme="light">

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
                    <span class="navbar-text me-1">Gast</span>
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                <?php else: ?>
                    <div class="dropdown d-flex align-items-center">
                        <img src=<?php echo $_SESSION['profilbild'] ?? 'uploads/user_avatars/default.png'; ?> alt="Profilbild" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($_SESSION['username']) ?>
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
                <button id="darkModeToggle" class="btn btn-sm btn-secondary ms-2">🌙</button>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar text-white p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">Beiträge</a>
                </li>
                <li class="nav-item">
                    <a href="edit_post.php" class="nav-link">Bearbeiten</a>
                </li>
                <li class="nav-item">
                    <a href="delete_post.php" class="nav-link">Verwalten</a>
                </li>
                <li class="nav-item">
                    <a href="bookmarks.php" class="nav-link">Lesezeichen</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-grow-1">
            <div class="container">