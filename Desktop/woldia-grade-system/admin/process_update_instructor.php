<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_instructors.php");
    exit();
}

$instructor_id = (int)$_POST['instructor_id'];
$staff_id      = trim($_POST['staff_id']);
$full_name     = trim($_POST['full_name']);
$dept_id       = (int)$_POST['dept_id'];

if ($instructor_id <= 0 || empty($staff_id) || empty($full_name) || $dept_id <= 0) {
    header("Location: manage_instructors.php?msg=error");
    exit();
}

// Check for duplicate staff_id (except current instructor)
$sql_check = "SELECT id FROM instructors WHERE staff_id = ? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $staff_id, $instructor_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    header("Location: edit_instructor.php?id=$instructor_id&msg=duplicate_staff");
    exit();
}

// Update instructor
$sql = "UPDATE instructors SET staff_id = ?, full_name = ?, dept_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $staff_id, $full_name, $dept_id, $instructor_id);

if ($stmt->execute()) {
    header("Location: manage_instructors.php?msg=updated");
} else {
    header("Location: edit_instructor.php?id=$instructor_id&msg=error");
}

exit();
?>