<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $conn = Database::getInstance()->getConnection();
    
    $query = "SELECT * FROM irrigation_zones ORDER BY id ASC";
    $result = $conn->query($query);
    
    $zones = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $zones[] = $row;
        }
    }
    
    echo json_encode($zones);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
