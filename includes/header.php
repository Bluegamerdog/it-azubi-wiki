<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>IT Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <style>
        body.dark-mode {
            background-color: #121212;
            color: #f0f0f0;
        }

        .dark-mode .navbar {
            background-color: #1f1f1f !important;
        }

        .dark-mode .nav-link {
            color: #f0f0f0 !important;
        }

        .dark-mode .btn-outline-dark {
            color: #f0f0f0;
            border-color: #f0f0f0;
        }

        .dark-mode .btn-outline-primary {
            color: #f0f0f0;
            border-color: #007bff;
        }

        .post-card {
            transition: all 0.2s ease;
        }

        .post-card:hover {
            background-color: #f8f9fa;
        }

        .dark-mode .post-card:hover {
            background-color: #1e1e1e;
        }

        .dark-mode .post-card {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        .votes {
            width: 40px;
            flex-shrink: 0;
        }
    </style>


</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">IT Forum</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link active" aria-current="page">Beiträge</a>
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

                <div class="d-flex ms-auto align-items-center">
                    <span class="navbar-text me-3">
                        <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Gast' ?>
                    </span>
                    <?php if (isset($_SESSION['username'])): ?>
                        <form action="logout.php" method="post" class="d-inline">
                            <button type="submit" name="logout" class="btn btn-outline-dark btn-sm">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary btn-sm">Log in</a>
                        <a href="register.php" class="btn btn-outline-primary btn-sm ms-1">Sign up</a>
                    <?php endif; ?>
                    <button id="darkModeToggle" class="btn btn-sm btn-secondary ms-2">🌙</button>
                </div>
            </div>
        </div>
    </nav>



    <div class="container">