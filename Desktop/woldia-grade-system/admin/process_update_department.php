<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_departments.php");
    exit();
}

$dept_id   = (int)$_POST['dept_id'];
$dept_code = trim($_POST['dept_code']);
$dept_name = trim($_POST['dept_name']);

if ($dept_id <= 0 || empty($dept_code) || empty($dept_name)) {
    header("Location: manage_departments.php?msg=error");
    exit();
}

// Check for duplicate code (except current department)
$sql_check = "SELECT id FROM departments WHERE dept_code = ? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $dept_code, $dept_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    header("Location: edit_department.php?id=$dept_id&msg=duplicate");
    exit();
}

// Update department
$sql = "UPDATE departments SET dept_code = ?, dept_name = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $dept_code, $dept_name, $dept_id);

if ($stmt->execute()) {
    header("Location: manage_departments.php?msg=updated");
} else {
    header("Location: edit_department.php?id=$dept_id&msg=error");
}

exit();
?>