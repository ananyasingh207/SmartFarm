<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $conn = Database::getInstance()->getConnection();
    $range = isset($_GET['range']) ? $_GET['range'] : 'week';

    $labels = [];
    $data = [];

    // 2. Fetch Data with Correct Aggregation (dates are now current via seed.php)
    if ($range === 'week') {
        // Last 7 days relative to today
        $query = "SELECT DATE_FORMAT(date, '%a') as label, SUM(usage_liters) as total_usage 
                  FROM daily_water_usage 
                  WHERE date > DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND date <= CURDATE()
                  GROUP BY DATE(date) 
                  ORDER BY date ASC";
                  
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['label'];
                $data[] = (float)$row['total_usage'];
            }
        }

    } elseif ($range === 'month') {
        // Last 30 days
        $query = "SELECT DATE_FORMAT(date, '%d %b') as label, SUM(usage_liters) as total_usage 
                  FROM daily_water_usage 
                  WHERE date > DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND date <= CURDATE()
                  GROUP BY DATE(date) 
                  ORDER BY date ASC";
                  
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['label'];
                $data[] = (float)$row['total_usage'];
            }
        }

    } elseif ($range === 'year') {
        // Current year, grouped by month
        $query = "SELECT DATE_FORMAT(date, '%b') as label, SUM(usage_liters) as total_usage 
                  FROM daily_water_usage 
                  WHERE YEAR(date) = YEAR(CURDATE())
                  GROUP BY MONTH(date), DATE_FORMAT(date, '%b') 
                  ORDER BY MIN(date) ASC";
                  
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['label'];
                $data[] = (float)$row['total_usage'];
            }
        }
    }

    // 3. Return JSON
    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
