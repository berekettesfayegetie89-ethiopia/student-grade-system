<?php
session_start();


// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Allow only admin or instructor for admin/instructor pages
$allowed_roles = ['admin', 'instructor'];
if (in_array($_SESSION['role'], $allowed_roles) === false && strpos($_SERVER['SCRIPT_NAME'], 'student') === false) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

// Get current user details for later use
$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($current_role); ?> - Woldia University SGMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <!-- Hamburger for mobile -->
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand ms-3" href="<?php echo ($current_role == 'admin' || $current_role == 'instructor') ? ($current_role == 'admin' ? 'admin/dashboard.php' : 'instructor/dashboard.php') : 'student/dashboard.php'; ?>">
            <i class="bi bi-mortarboard-fill"></i> Woldia University SGMS
        </a>

        <div class="text-white d-flex align-items-center">
            <span class="me-3">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                (<?php echo ucfirst($current_role); ?>)
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar only for admin and instructor -->
                        <!-- Sidebar only for admin and instructor -->
            <?php if ($current_role == 'admin' || $current_role == 'instructor'): ?>
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
                <?php 
                if ($current_role == 'admin') {
                    include '../includes/sidebar.php';
                } elseif ($current_role == 'instructor') {
                    include '../instructor/sidebar.php';
                }
                ?>
            </div>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="<?php echo ($current_role == 'admin' || $current_role == 'instructor') ? 'col-md-9 ms-sm-auto col-lg-10' : 'col-12'; ?> px-md-4 mt-5 pt-3">