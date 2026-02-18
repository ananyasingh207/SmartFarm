<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $conn = Database::getInstance()->getConnection();
    
    $zoneId = $_POST['zone_id'] ?? null;
    $action = $_POST['action'] ?? null; // 'start' or 'stop'
    
    if (!$zoneId || !$action) {
        throw new Exception("Missing zone_id or action");
    }
    
    $newStatus = ($action === 'start') ? 'Irrigating' : 'Active';
    
    $stmt = $conn->prepare("UPDATE irrigation_zones SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $zoneId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'new_status' => $newStatus]);
    } else {
        throw new Exception("Failed to update status");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
