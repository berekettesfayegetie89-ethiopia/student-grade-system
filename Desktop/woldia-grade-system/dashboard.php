<?php
session_start();

// If not logged in, send to login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];

// Redirect based on role
if ($role == 'admin') {
    header("Location: admin/dashboard.php");
} elseif ($role == 'instructor') {
    header("Location: instructor/dashboard.php");
} elseif ($role == 'student') {
    header("Location: student/dashboard.php");
} else {
    // Unknown role
    header("Location: logout.php");
}
exit();
?>