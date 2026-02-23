<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

// 1. Validate Session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in or session expired. Please re-login.']);
    exit();
}

$userId = $_SESSION['user_id'];
$conn = Database::getInstance()->getConnection();

// 2. Get POST Data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// 3. Validate Inputs
if (empty($name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Name and Email are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

// Check if email belongs to another user
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already taken by another user.']);
    exit();
}
$stmt->close();

// 4. Update Database
try {
    if (!empty($password)) {
        // Validation for password
        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            exit();
        }
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $updateStmt->bind_param("sssi", $name, $email, $hashedPassword, $userId);
    } else {
        // Update without password
        $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $name, $email, $userId);
    }

    if ($updateStmt->execute()) {
        // Update session
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
    $updateStmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>
