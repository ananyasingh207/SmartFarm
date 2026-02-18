<?php
session_start();
require_once 'db.php';

// 1. Security & Authentication check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    // Redirect to login if not logged in or not a farmer
    header("Location: auth/login.php");
    exit();
}

// 2. Get parameters
$range = isset($_GET['range']) ? $_GET['range'] : 'month'; // 'week', 'month', 'year'

// 3. Prepare CSV Headers
$filename = "water_report_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV Column Headers
fputcsv($output, ['Day/Period', 'Water Usage (Liters)', 'Target (Liters)']);

try {
    $conn = Database::getInstance()->getConnection();
    $data = [];

    // 4. Fetch Data based on range
    if ($range === 'week') {
        // Last 7 days (consistent with dashboard graph)
        $query = "SELECT DATE_FORMAT(date, '%a %d %b') as label, usage_liters, target_liters 
                  FROM daily_water_usage 
                  WHERE date > DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND date <= CURDATE()
                  ORDER BY date ASC";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    } elseif ($range === 'year') {
        // Monthly aggregation for current year
        $query = "SELECT DATE_FORMAT(date, '%b') as label, SUM(usage_liters) as usage_liters, SUM(target_liters) as target_liters 
                  FROM daily_water_usage 
                  WHERE YEAR(date) = YEAR(CURDATE())
                  GROUP BY MONTH(date), DATE_FORMAT(date, '%b') 
                  ORDER BY MIN(date) ASC";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    } else {
        // Default to month (Last 30 days)
        $query = "SELECT DATE_FORMAT(date, '%d %b') as label, usage_liters, target_liters 
                  FROM daily_water_usage 
                  WHERE date > DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND date <= CURDATE()
                  ORDER BY date ASC";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    }

    // 5. Write Data to CSV
    foreach ($data as $row) {
        fputcsv($output, [
            $row['label'], 
            $row['usage_liters'], 
            $row['target_liters']
        ]);
    }

} catch (Exception $e) {
    // In case of error, putting it in the CSV or just stopping. 
    // Since headers are sent, we can't redirect.
    fputcsv($output, ['Error', 'Could not fetch data']);
}

fclose($output);
exit();
?>
