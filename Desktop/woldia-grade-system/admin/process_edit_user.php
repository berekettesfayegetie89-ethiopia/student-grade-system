<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();

require_once '../config/db_connect.php';

$user_id = (int)$_POST['user_id'];
$username = trim($_POST['username']);
$full_name = trim($_POST['full_name']);
$id_number = trim($_POST['id_number']);
$dept_id = (int)$_POST['dept_id'];
$is_active = (int)$_POST['is_active'];

if ($user_id <= 0 || empty($username) || empty($full_name)) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}

// Update users table
$stmt = $conn->prepare("UPDATE users SET username = ?, is_active = ? WHERE id = ?");
$stmt->bind_param("sii", $username, $is_active, $user_id);
$stmt->execute();

// Update student or instructor table
$role_query = $conn->query("SELECT role FROM users WHERE id = $user_id");
$role = $role_query->fetch_assoc()['role'];

if ($role === 'student') {
    $stmt = $conn->prepare("UPDATE students SET student_id = ?, full_name = ?, dept_id = ? WHERE user_id = ?");
    $stmt->bind_param("ssii", $id_number, $full_name, $dept_id, $user_id);
} elseif ($role === 'instructor') {
    $stmt = $conn->prepare("UPDATE instructors SET staff_id = ?, full_name = ?, dept_id = ? WHERE user_id = ?");
    $stmt->bind_param("ssii", $id_number, $full_name, $dept_id, $user_id);
}

if ($stmt->execute()) {
    header("Location: manage_users_full.php?msg=updated");
} else {
    header("Location: manage_users_full.php?msg=error");
}
exit();