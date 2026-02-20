<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();
require_once '../config/db_connect.php';
$id = (int)$_GET['id'];

// Get user_id first
$res = mysqli_query($conn, "SELECT user_id FROM students WHERE id = $id");
$row = mysqli_fetch_assoc($res);
$user_id = $row['user_id'];

// Delete from related tables
mysqli_query($conn, "DELETE FROM grades WHERE enrollment_id IN (SELECT id FROM enrollments WHERE student_id = $id)");
mysqli_query($conn, "DELETE FROM enrollments WHERE student_id = $id");
mysqli_query($conn, "DELETE FROM students WHERE id = $id");
mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");

header("Location: manage_students.php");
?>