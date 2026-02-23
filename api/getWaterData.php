<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

try {
    $conn = Database::getInstance()->getConnection();
    
    // Get period from request (week, month, year)
    $period = isset($_GET['period']) ? $_GET['period'] : 'month';
    
    // --- 1. Fetch Chart Data ---
    $chartData = [
        'labels' => [],
        'usage' => [],
        'target' => []
    ];
    
    $query = "";
    if ($period === 'week') {
        // Last 7 days
        $query = "SELECT DATE_FORMAT(date, '%a') as label, usage_liters, target_liters FROM daily_water_usage ORDER BY date DESC LIMIT 7";
    } elseif ($period === 'month') {
        // Daily data for this month (simulated by LIMIT 30 for simplicity in this demo)
        $query = "SELECT DAY(date) as label, usage_liters, target_liters FROM daily_water_usage ORDER BY date ASC LIMIT 30";
    } elseif ($period === 'year') {
        // Monthly aggregation (simulated data or aggregated from real)
        // Since we only seeded 1 month, we will just return mock-like aggregation 
        // OR we can generate it on the fly in SQL. Let's use the seeded table pattern if exists, 
        // or just aggregate what we have.
        // For robustness, let's just return the same month data but labeled as months for this demo to not break UI
        // In a real app, this would be: SELECT MONTHNAME(date) ... GROUP BY MONTH(date)
        $query = "SELECT DATE_FORMAT(date, '%b') as label, SUM(usage_liters) as usage_liters, SUM(target_liters) as target_liters FROM daily_water_usage GROUP BY MONTH(date) ORDER BY date ASC";
    }
    
    // Fallback query if variable is empty
    if(empty($query)) {
         $query = "SELECT DAY(date) as label, usage_liters, target_liters FROM daily_water_usage ORDER BY date ASC LIMIT 30";
    }

    $result = $conn->query($query);
    
    // If 'week', we need to reverse the order because we fetched DESC
    $tempData = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $tempData[] = $row;
        }
    }
    
    if ($period === 'week') {
        $tempData = array_reverse($tempData);
    }
    
    foreach ($tempData as $row) {
        $chartData['labels'][] = $row['label'];
        $chartData['usage'][] = (float)$row['usage_liters'];
        $chartData['target'][] = (float)$row['target_liters'];
    }
    
    // --- 2. Fetch Zone Data ---
    $zones = [];
    $zoneQuery = "SELECT * FROM zone_performance";
    $zoneResult = $conn->query($zoneQuery);
    if ($zoneResult) {
        while ($row = $zoneResult->fetch_assoc()) {
            $usage = (float)$row['usage_liters'];
            $target = (float)$row['target_liters'];
            $efficiency = $target > 0 ? round(($usage / $target) * 100) : 0;
            
            $zones[] = [
                'zone' => $row['zone_name'],
                'crop' => $row['crop'],
                'usage' => number_format($usage),
                'target' => number_format($target),
                'efficiency' => $efficiency . '%'
            ];
        }
    }
    
    // --- 3. Calculate Summary Stats ---
    // Total Usage This Month (Sum of all records since we only seeded a month)
    $sumQuery = "SELECT SUM(usage_liters) as total, AVG(usage_liters) as daily_avg FROM daily_water_usage";
    $sumResult = $conn->query($sumQuery);
    $sumRow = $sumResult->fetch_assoc();
    
    $totalUsage = $sumRow['total'] ?? 0;
    $dailyAvg = $sumRow['daily_avg'] ?? 0;
    
    $summary = [
        'total' => number_format($totalUsage) . ' L',
        'daily_average' => number_format($dailyAvg, 0) . ' L',
        'comparison' => '-12%', // This would typically compare with previous month's data
        'efficiency' => '85%'
    ];
    
    // Final JSON Response
    echo json_encode([
        'chart' => $chartData,
        'zones' => $zones,
        'summary' => $summary
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
