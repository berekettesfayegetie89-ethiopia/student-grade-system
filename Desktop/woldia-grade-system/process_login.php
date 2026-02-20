<?php
session_start();
require_once 'config/db_connect.php';

// Use prepared statement for security
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: index.php?error=empty");
        exit();
    }

    // Fetch user with is_active check
    $stmt = $conn->prepare("
        SELECT id, username, password, role, is_active 
        FROM users 
        WHERE username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // First check if account is active
        if ($user['is_active'] == 0) {
            header("Location: index.php?error=deactivated");
            exit();
        }

        // Then verify password
        if (password_verify($password, $user['password'])) {
            // Login success!
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['initiated'] = true; // for extra session security

            header("Location: dashboard.php");
            exit();
        }
    }

    // Wrong credentials or deactivated
    header("Location: index.php?error=invalid");
    exit();
}
?>