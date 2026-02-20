<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();

require_once '../config/db_connect.php';

$id = (int)$_GET['id'];

// Delete grades first (if any), then enrollment
mysqli_query($conn, "DELETE FROM grades WHERE enrollment_id = $id");
mysqli_query($conn, "DELETE FROM enrollments WHERE id = $id");

header("Location: manage_enrollments.php");
exit();
?>