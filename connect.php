<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "irrigation";

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Set charset to UTF-8 to support special characters
$conn->set_charset('utf8mb4');

// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) === 'connect.php') {
    die('Direct access not allowed');
}
?>