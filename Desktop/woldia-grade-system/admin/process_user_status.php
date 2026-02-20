<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$id = (int)$_GET['id'];
$action = $_GET['action'] ?? '';

if ($id <= 0 || !in_array($action, ['activate', 'deactivate'])) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}

$new_status = ($action === 'activate') ? 1 : 0;

$stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
$stmt->bind_param("ii", $new_status, $id);

if ($stmt->execute()) {
    $msg = ($action === 'activate') ? 'activated' : 'deactivated';
    header("Location: manage_users_full.php?msg=$msg");
} else {
    header("Location: manage_users_full.php?msg=error");
}
exit();