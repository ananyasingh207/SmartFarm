<?php
require_once __DIR__ . '/../config/db.php';

// Get form data from POST
$name = $_POST["name"];
$email = $_POST["email"];
$role = $_POST["role"];
$message = $_POST["message"];

// Get connection
$conn = Database::getInstance()->getConnection();

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
