<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

try {
    $conn = Database::getInstance()->getConnection();
    
    $query = "SELECT s.*, z.zone_name 
              FROM irrigation_schedule s 
              JOIN irrigation_zones z ON s.zone_id = z.id 
              ORDER BY s.start_time ASC";
              
    $result = $conn->query($query);
    
    $schedules = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Format time for display (e.g., 06:00:00 -> 06:00 AM)
            $row['display_time'] = date("h:i A", strtotime($row['start_time']));
            $schedules[] = $row;
        }
    }
    
    echo json_encode($schedules);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
