<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();
require_once '../config/db_connect.php';
$id = (int)$_GET['id'];

$res = mysqli_query($conn, "SELECT user_id FROM instructors WHERE id = $id");
$row = mysqli_fetch_assoc($res);
$user_id = $row['user_id'];

mysqli_query($conn, "DELETE FROM grades WHERE enrollment_id IN (SELECT id FROM enrollments WHERE course_id IN (SELECT course_id FROM course_assignments WHERE instructor_id = $id))");
mysqli_query($conn, "DELETE FROM course_assignments WHERE instructor_id = $id");
mysqli_query($conn, "DELETE FROM instructors WHERE id = $id");
mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");

header("Location: manage_instructors.php");
?>