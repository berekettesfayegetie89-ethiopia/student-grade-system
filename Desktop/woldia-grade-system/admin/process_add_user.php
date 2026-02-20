<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit();
require_once '../config/db_connect.php';

$role = $_POST['role'];
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
$dept_id = (int)$_POST['dept_id'];

// First: Insert into users table
$sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
if (!mysqli_query($conn, $sql)) {
    die("Username already exists!");
}
$user_id = mysqli_insert_id($conn);

// Then: Insert into correct table
if ($role == 'student') {
    $sql = "INSERT INTO students (student_id, full_name, dept_id, user_id) 
            VALUES ('$id_number', '$full_name', $dept_id, $user_id)";
} elseif ($role == 'instructor') {
    $sql = "INSERT INTO instructors (staff_id, full_name, dept_id, user_id) 
            VALUES ('$id_number', '$full_name', $dept_id, $user_id)";
} else {
    // Admin: no extra table needed
    header("Location: manage_users_full.php");
    exit();
}

if (mysqli_query($conn, $sql)) {
    header("Location: manage_users_full.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>