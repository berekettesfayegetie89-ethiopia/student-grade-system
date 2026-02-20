<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();
require_once '../config/db_connect.php';

$code = mysqli_real_escape_string($conn, $_POST['course_code']);
$name = mysqli_real_escape_string($conn, $_POST['course_name']);
$credit = (int)$_POST['credit_hours'];
$dept = (int)$_POST['dept_id'];
$sem = (int)$_POST['semester'];

$sql = "INSERT INTO courses (course_code, course_name, credit_hours, dept_id, semester) 
        VALUES ('$code', '$name', $credit, $dept, $sem)";

if (mysqli_query($conn, $sql)) {
    header("Location: manage_courses.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>