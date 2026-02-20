<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_students.php");
    exit();
}

$student_id        = (int)$_POST['student_id'];
$student_id_number = trim($_POST['student_id_number']);
$full_name         = trim($_POST['full_name']);
$dept_id           = (int)$_POST['dept_id'];
$year              = (int)$_POST['year'];

if ($student_id <= 0 || empty($student_id_number) || empty($full_name) || $dept_id <= 0 || $year < 1 || $year > 6) {
    header("Location: manage_students.php?msg=error");
    exit();
}

// Check for duplicate student_id (except current student)
$sql_check = "SELECT id FROM students WHERE student_id = ? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $student_id_number, $student_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    header("Location: edit_student.php?id=$student_id&msg=duplicate_id");
    exit();
}

// Update student
$sql = "UPDATE students SET student_id = ?, full_name = ?, dept_id = ?, year = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiii", $student_id_number, $full_name, $dept_id, $year, $student_id);

if ($stmt->execute()) {
    header("Location: manage_students.php?msg=updated");
} else {
    header("Location: edit_student.php?id=$student_id&msg=error");
}

exit();
?>