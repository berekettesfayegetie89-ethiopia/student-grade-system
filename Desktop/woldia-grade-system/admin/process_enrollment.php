<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db_connect.php';

$student_id = (int)$_POST['student_id'];
$course_id = (int)$_POST['course_id'];
$year = mysqli_real_escape_string($conn, $_POST['year']);
$semester = (int)$_POST['semester'];

// Prevent duplicate enrollment
$sql_check = "SELECT id FROM enrollments 
              WHERE student_id = $student_id 
              AND course_id = $course_id 
              AND academic_year = '$year' 
              AND semester = $semester";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    header("Location: manage_enrollments.php?error=already");
    exit();
}

// Insert new enrollment
$sql = "INSERT IGNORE INTO enrollments 
        (student_id, course_id, academic_year, semester) 
        VALUES ($student_id, $course_id, '$year', $semester)";

if (mysqli_query($conn, $sql)) {
    header("Location: manage_enrollments.php?success=1");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>