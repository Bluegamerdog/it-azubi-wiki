<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>IT Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/darkmode.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" defer></script>
    <script src="assets/js/darkmode.js" defer></script>
</head>

<body class="d-flex flex-column min-vh-100">
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
                        <a href="index.php" class="nav-link active" aria-current="page">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="create_post.php" class="nav-link active">Neuer Beitrag</a>
                    </li>

                    <li class="nav-item">
                        <a href="edit_post.php" class="nav-link active">Beiträge bearbeiten</a>
                    </li>

                    <li class="nav-item">
                        <a href="delete_post.php" class="nav-link active">Beiträge Verwalten</a>
                    </li>

                </ul>

                <div class="d-flex ms-auto align-items-center">
                    <span class="navbar-text me-3">
                        <?= isset($_SESSION['admin']) ? htmlspecialchars($_SESSION['admin']) : 'Gast' ?>
                    </span>
                    <?php if (isset($_SESSION['admin'])): ?>
                        <form action="logout.php" method="post" class="d-inline">
                            <button type="submit" name="logout" class="btn btn-outline-dark btn-sm">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary btn-sm">Login</a>
                    <?php endif; ?>
                    <button id="darkModeToggle" class="btn btn-sm btn-secondary ms-2">🌙</button>
                </div>

            </div>
        </div>
    </nav>


    <div class="container">