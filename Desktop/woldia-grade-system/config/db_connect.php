<?php
// What it is: Code to connect to MySQL database
// Why: Every page that needs data (almost all) will use this

$host = 'localhost';
$dbname = 'woldia_sgms';
$username = 'root';          // Default XAMPP MySQL username
$password = '';              // Default XAMPP MySQL password (empty)

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// If you see no error, connection is good!
?>