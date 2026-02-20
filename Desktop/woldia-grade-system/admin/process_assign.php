<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

// Get form data
$instructor_id = (int)$_POST['instructor_id'];
$course_id     = (int)$_POST['course_id'];
$year          = mysqli_real_escape_string($conn, trim($_POST['year']));
$semester      = (int)$_POST['semester'];

// Basic validation
if ($instructor_id <= 0 || $course_id <= 0 || empty($year) || $semester < 1 || $semester > 2) {
    header("Location: assign_instructor.php?msg=error");
    exit();
}

// Prevent duplicate assignment for same year/semester
$sql_check = "SELECT id FROM course_assignments 
              WHERE course_id = ? AND instructor_id = ? 
              AND academic_year = ? AND semester = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("iiss", $course_id, $instructor_id, $year, $semester);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Already assigned
    header("Location: assign_instructor.php?msg=already");
    exit();
}

// Insert new assignment
$sql = "INSERT INTO course_assignments 
        (course_id, instructor_id, academic_year, semester, status) 
        VALUES (?, ?, ?, ?, 'open')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $course_id, $instructor_id, $year, $semester);

if ($stmt->execute()) {
    // Success - redirect with message
    header("Location: assign_instructor.php?msg=success");
} else {
    // Error
    header("Location: assign_instructor.php?msg=error");
}

exit();
?>