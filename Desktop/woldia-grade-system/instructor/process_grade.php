<?php
session_start();
if ($_SESSION['role'] !== 'instructor') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/db_connect.php';

// Get data from POST (safe)
$enrollment_id = (int)$_POST['enrollment_id'];
$course_id = (int)$_POST['course_id'];  // Now comes from hidden input!
$score = floatval($_POST['score']);
$manual_letter = strtoupper(trim($_POST['letter_grade'] ?? ''));

// Validation
if ($score < 0 || $score > 100) {
    header("Location: enter_grades.php?course_id=$course_id&error=invalid");
    exit();
}

// Auto calculate letter grade and grade point
$letter = '';
$gp = 0.00;

if ($score >= 90) { $letter = 'A+'; $gp = 4.00; }
elseif ($score >= 85) { $letter = 'A'; $gp = 4.00; }
elseif ($score >= 80) { $letter = 'A-'; $gp = 3.75; }
elseif ($score >= 75) { $letter = 'B+'; $gp = 3.50; }
elseif ($score >= 70) { $letter = 'B'; $gp = 3.00; }
elseif ($score >= 65) { $letter = 'B-'; $gp = 2.75; }
elseif ($score >= 60) { $letter = 'C+'; $gp = 2.50; }
elseif ($score >= 50) { $letter = 'C'; $gp = 2.00; }
else { $letter = 'F'; $gp = 0.00; }

// Allow instructor to override letter grade if they type it
$valid_letters = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'F'];
if (in_array($manual_letter, $valid_letters)) {
    $letter = $manual_letter;
    // You can add logic here to recalculate $gp if needed
}

// Save or update grade
$sql_check = "SELECT id FROM grades WHERE enrollment_id = $enrollment_id";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Update existing
    $sql = "UPDATE grades SET grade = $score, letter_grade = '$letter', grade_point = $gp 
            WHERE enrollment_id = $enrollment_id";
} else {
    // Insert new
    $sql = "INSERT INTO grades (enrollment_id, grade, letter_grade, grade_point) 
            VALUES ($enrollment_id, $score, '$letter', $gp)";
}

mysqli_query($conn, $sql);

// SUCCESS: Redirect back with correct course_id and success message
header("Location: enter_grades.php?course_id=$course_id&msg=saved");
exit();
?>