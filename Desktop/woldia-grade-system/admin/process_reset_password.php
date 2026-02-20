<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_users_full.php?msg=error");
    exit();
}

$user_id = (int)$_POST['user_id'];
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($user_id <= 0 || empty($new_password) || empty($confirm_password)) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}

// Basic validation
if ($new_password !== $confirm_password) {
    // You could redirect back with error, but for simplicity we'll use a generic error
    header("Location: manage_users_full.php?msg=error");
    exit();
}

if (strlen($new_password) < 8) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    header("Location: manage_users_full.php?msg=reset_custom");
} else {
    header("Location: manage_users_full.php?msg=error");
}

exit();
?>