<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$dept_id = (int)($_GET['id'] ?? 0);

if ($dept_id <= 0) {
    header("Location: manage_departments.php?msg=error");
    exit();
}

// Optional: Check if department has courses (prevent delete if needed)
$sql_check = "SELECT COUNT(*) as count FROM courses WHERE dept_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $dept_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();

if ($row_check['count'] > 0) {
    // If you want to prevent delete when courses exist:
    header("Location: manage_departments.php?msg=cannot_delete");
    exit();

    // Or allow delete and let foreign key set dept_id to NULL (current DB behavior)
    // Just proceed below
}

// Delete the department
$sql = "DELETE FROM departments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dept_id);

if ($stmt->execute()) {
    header("Location: manage_departments.php?msg=deleted");
} else {
    header("Location: manage_departments.php?msg=error");
}

exit();
?>