<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea"></i> Voting System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
                    </li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'vote.php' ? 'active' : '' ?>" href="vote.php">Vote</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'results.php' ? 'active' : '' ?>" href="results.php">Results</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout (<?= $_SESSION['username'] ?>)</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'login.php' ? 'active' : '' ?>" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'register.php' ? 'active' : '' ?>" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container-fluid p-0">