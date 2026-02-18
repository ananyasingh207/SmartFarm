<?php
// seed.php - Standalone script to seed mock data
// Usage: Run manually via browser or CLI (php seed.php)

require_once 'db.php';

// Enable error reporting for debugging this script
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

try {
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "Connected to database successfully.\n\n";
    echo "Starting seeding process...\n";
    echo "--------------------------------------------------\n";

    // Helper function to check if table has data
    function tableHasData($conn, $tableName) {
        $result = $conn->query("SELECT COUNT(*) as count FROM " . $tableName);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }
        return false;
    }

    // --- 1. Users ---
    if (!tableHasData($conn, 'users')) {
        echo "Seeding 'users' table... ";
        
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        
        $users = [
            ['John Farmer', 'farmer@example.com', $password, 'farmer'],
            ['Jane Manufacturer', 'manufacturer@example.com', $password, 'manufacturer'],
            ['Bob Service', 'service@example.com', $password, 'service'],
            ['Alice Farmer', 'alice@example.com', $password, 'farmer']
        ];
        
        foreach ($users as $user) {
            $stmt->bind_param("ssss", $user[0], $user[1], $user[2], $user[3]);
            $stmt->execute();
        }
        $stmt->close();
        echo "Done.\n";
    } else {
        echo "'users' table - Skipped (already contains data).\n";
    }

    // --- 2. Devices ---
    if (!tableHasData($conn, 'devices')) {
        echo "Seeding 'devices' table... ";
        $sql = "INSERT INTO devices (name, status) VALUES 
                ('Soil Moisture Sensor - North', 'online'),
                ('Soil Moisture Sensor - South', 'online'),
                ('Main Water Pump', 'offline'),
                ('Drip Irrigation Valve A', 'online'),
                ('Weather Station', 'online')";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'devices' table - Skipped (already contains data).\n";
    }

    // --- 3. Sensors ---
    if (!tableHasData($conn, 'sensors')) {
        echo "Seeding 'sensors' table... ";
        $sql = "INSERT INTO sensors (sensor_type, value, recorded_at) VALUES 
                ('soil_moisture', 45.5, NOW()),
                ('soil_moisture', 42.1, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
                ('temperature', 28.5, NOW()),
                ('humidity', 60.2, NOW()),
                ('soil_moisture', 46.0, DATE_SUB(NOW(), INTERVAL 2 HOUR))";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'sensors' table - Skipped (already contains data).\n";
    }

    // --- 4. System Status ---
    if (!tableHasData($conn, 'system_status')) {
        echo "Seeding 'system_status' table... ";
        $sql = "INSERT INTO system_status (system_health, last_maintenance, sensors_online, sensors_total, recorded_at) VALUES 
                ('Good', '2023-10-15', 4, 5, NOW())";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'system_status' table - Skipped (already contains data).\n";
    }

    // --- 5. Water Quality ---
    if (!tableHasData($conn, 'water_quality')) {
        echo "Seeding 'water_quality' table... ";
        $sql = "INSERT INTO water_quality (ph_level, ec_value, tds, temperature, status, recorded_at) VALUES 
                (6.8, 1.2, 450, 24.5, 'Good', NOW()),
                (7.0, 1.3, 460, 23.0, 'Good', DATE_SUB(NOW(), INTERVAL 1 DAY))";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'water_quality' table - Skipped (already contains data).\n";
    }

    // --- 6. Water Status ---
    if (!tableHasData($conn, 'water_status')) {
        echo "Seeding 'water_status' table... ";
        $sql = "INSERT INTO water_status (water_pressure, tank_level, recorded_at) VALUES 
                (35.5, 80, NOW()),
                (34.0, 75, DATE_SUB(NOW(), INTERVAL 1 HOUR))";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'water_status' table - Skipped (already contains data).\n";
    }

    // --- 7. Feedbacks ---
    if (!tableHasData($conn, 'feedbacks')) {
        echo "Seeding 'feedbacks' table... ";
        $sql = "INSERT INTO feedbacks (name, email, role, message, submitted_at) VALUES 
                ('John Doe', 'john@example.com', 'farmer', 'Great system, saved a lot of water!', NOW()),
                ('Jane Smith', 'jane@example.com', 'service', 'Easy to maintain.', DATE_SUB(NOW(), INTERVAL 2 DAY))";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'feedbacks' table - Skipped (already contains data).\n";
    }

    // --- 8. Active Service ---
    if (!tableHasData($conn, 'active_service')) {
        echo "Seeding 'active_service' table... ";
        $sql = "INSERT INTO active_service (in_progress, new_requests, pending_invoices) VALUES 
                (5, 2, 1)";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'active_service' table - Skipped (already contains data).\n";
    }

    // --- 9. Active Device ---
    if (!tableHasData($conn, 'active_device')) {
        echo "Seeding 'active_device' table... ";
        $sql = "INSERT INTO active_device (active_devices, pending_requests, revenue, device_usage, updated_at) VALUES 
                (150, 12, 50000.00, 85, NOW())";
        $conn->query($sql);
        echo "Done.\n";
    } else {
        echo "'active_device' table - Skipped (already contains data).\n";
    }

    echo "--------------------------------------------------\n";
    echo "Seeding process completed successfully!\n";

} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}
?>
