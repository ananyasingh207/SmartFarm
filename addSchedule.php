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
    $startTime = $_POST['start_time'] ?? null;
    $duration = $_POST['duration'] ?? null;
    $repeat = $_POST['repeat'] ?? 'once'; // Handling repeat logic could be complex, for now we just store the basic schedule
    
    // Validate inputs
    if (!$zoneId || !$startTime || !$duration) {
        throw new Exception("Missing required fields");
    }
    
    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO irrigation_schedule (zone_id, start_time, duration, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("isi", $zoneId, $startTime, $duration);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        throw new Exception("Failed to add schedule");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
