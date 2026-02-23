<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $conn = Database::getInstance()->getConnection();
    
    $scheduleId = $_POST['schedule_id'] ?? null;
    
    if (!$scheduleId) {
        throw new Exception("Missing schedule_id");
    }
    
    $stmt = $conn->prepare("DELETE FROM irrigation_schedule WHERE id = ?");
    $stmt->bind_param("i", $scheduleId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Failed to delete schedule");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
