<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$course_id = (int)($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    header("Location: my_courses.php");
    exit();
}

// Security: only allow instructor who is assigned to this course
$sql_check = "SELECT ca.id 
              FROM course_assignments ca
              JOIN instructors i ON ca.instructor_id = i.id
              WHERE ca.course_id = ? AND i.user_id = ? AND ca.status = 'open'";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $course_id, $_SESSION['user_id']);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    header("Location: my_courses.php");
    exit();
}

// Mark course as submitted (locked)
$sql = "UPDATE course_assignments SET status = 'submitted' WHERE course_id = ? AND status = 'open'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);

if ($stmt->execute()) {
    header("Location: my_courses.php?msg=submitted");
} else {
    header("Location: my_courses.php?msg=error");
}
exit();
?>