<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();
require_once '../config/db_connect.php';
$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM courses WHERE id = $id");
header("Location: manage_courses.php");
?>