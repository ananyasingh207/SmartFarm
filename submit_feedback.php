<?php
$host = "localhost";
$port = 3307;
$user = "root";
$password = "";
$database = "smart_irrigation";

// Get form data from POST
$name = $_POST["name"];
$email = $_POST["email"];
$role = $_POST["role"];
$message = $_POST["message"];

// Connect to MySQL with port
$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Insert feedback
$stmt = $conn->prepare("INSERT INTO feedbacks (name, email, role, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $role, $message);

if ($stmt->execute()) {
    echo "Feedback submitted successfully";
} else {
    echo "Failed to submit feedback: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>