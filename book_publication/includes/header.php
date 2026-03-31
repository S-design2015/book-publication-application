<?php
// includes/header.php
if (!isset($pageTitle)) {
    $pageTitle = 'Book Publication Management System';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN (or local file later) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/book_publication/index.php">BookPub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/book_publication/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/book_publication/subjects.php">Subjects</a></li>
                <li class="nav-item"><a class="nav-link" href="/book_publication/publishers.php">Publishers</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (!isset($_SESSION)) session_start(); ?>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/book_publication/auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/book_publication/auth/register.php">Register</a></li>
                <?php else: ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="/book_publication/admin/index.php">Admin Panel</a></li>
                    <?php elseif ($_SESSION['role'] === 'publisher'): ?>
                        <li class="nav-item"><a class="nav-link" href="/book_publication/publisher/index.php">Publisher Panel</a></li>
                    <?php elseif ($_SESSION['role'] === 'customer'): ?>
                        <li class="nav-item"><a class="nav-link" href="/book_publication/customer/index.php">Customer Panel</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="/book_publication/auth/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
