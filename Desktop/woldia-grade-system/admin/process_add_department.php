<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();

require_once '../config/db_connect.php';

$code = mysqli_real_escape_string($conn, $_POST['dept_code']);
$name = mysqli_real_escape_string($conn, $_POST['dept_name']);

$sql = "INSERT INTO departments (dept_code, dept_name) VALUES ('$code', '$name')";
if (mysqli_query($conn, $sql)) {
    header("Location: manage_departments.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>