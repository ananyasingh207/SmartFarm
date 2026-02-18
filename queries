-- Creating 'devices' table
CREATE TABLE devices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  status ENUM('online', 'offline') NOT NULL DEFAULT 'offline'
);

-- Inserting data into 'devices'
INSERT INTO devices (id, name, status) VALUES
(1, 'Soil Sensor 1', 'online'),
(2, 'Water Pump A', 'online'),
(3, 'Valve Controller B', 'offline'),
(4, 'Soil Sensor 2', 'online'),
(5, 'Main Pump', 'offline'),
(6, 'Sprinkler Unit', 'online');

-- Creating 'sensors' table
CREATE TABLE sensors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sensor_type VARCHAR(50),
  value DECIMAL(5,2),
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Inserting data into 'sensors'
INSERT INTO sensors (id, sensor_type, value, recorded_at) VALUES
(1, 'soil_moisture', 64.50, '2025-04-25 04:47:00'),
(2, 'soil_moisture', 67.20, '2025-04-25 04:47:00'),
(3, 'soil_moisture', 62.90, '2025-04-25 04:47:00'),
(4, 'soil_moisture', 68.10, '2025-04-25 04:47:00'),
(5, 'soil_moisture', 65.00, '2025-04-25 04:47:00');

-- Creating 'system_status' table
CREATE TABLE system_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  system_health VARCHAR(50),
  last_maintenance DATE,
  sensors_online INT,
  sensors_total INT,
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Inserting data into 'system_status'
INSERT INTO system_status (id, system_health, last_maintenance, sensors_online, sensors_total, recorded_at) VALUES
(1, 'Good', '2025-03-28', 12, 12, '2025-04-25 05:04:18'),
(2, 'Moderate', '2025-02-15', 10, 12, '2025-04-25 05:04:18'),
(3, 'Good', '2025-01-20', 11, 12, '2025-04-25 05:04:18'),
(4, 'Critical', '2024-12-10', 7, 12, '2025-04-25 05:04:18'),
(5, 'Good', '2024-11-05', 12, 12, '2025-04-25 05:04:18');

-- Creating 'users' table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('farmer', 'manufacturer', 'service') NOT NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Creating 'water_quality' table
CREATE TABLE water_quality (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ph_level DECIMAL(3,1) NOT NULL,
  ec_value DECIMAL(3,1) NOT NULL,
  tds INT NOT NULL,
  temperature DECIMAL(4,1) NOT NULL,
  status ENUM('Good', 'Moderate', 'Critical') DEFAULT 'Good',
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Inserting data into 'water_quality'
INSERT INTO water_quality (id, ph_level, ec_value, tds, temperature, status, recorded_at) VALUES
(1, 6.8, 1.2, 450, 24.0, 'Good', '2025-04-25 05:22:36'),
(2, 7.2, 1.5, 500, 23.5, 'Moderate', '2025-04-25 05:22:36'),
(3, 5.9, 2.0, 600, 25.0, 'Critical', '2025-04-25 05:22:36');

-- Creating 'water_status' table
CREATE TABLE water_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  water_pressure DECIMAL(5,2),
  tank_level INT,
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Inserting data into 'water_status'
INSERT INTO water_status (id, water_pressure, tank_level, recorded_at) VALUES
(1, 72.00, 85, '2025-04-25 05:04:36'),
(2, 68.50, 70, '2025-04-25 05:04:36'),
(3, 74.20, 95, '2025-04-25 05:04:36'),
(4, 60.00, 40, '2025-04-25 05:04:36'),
(5, 76.00, 100, '2025-04-25 05:04:36');

-- Creating 'feedbacks' table
CREATE TABLE feedbacks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  role VARCHAR(50),
  message TEXT,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Creating table 'active_device'
CREATE TABLE active_device (
  id INT AUTO_INCREMENT PRIMARY KEY,
  active_devices INT NOT NULL,
  pending_requests INT NOT NULL,
  revenue DECIMAL(10,2) NOT NULL,
  device_usage INT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserting data into 'active_device'
INSERT INTO active_device (id, active_devices, pending_requests, revenue, device_usage, updated_at) VALUES
(1, 15, 5, 10500.00, 80, '2025-04-27 08:36:23');


-- Creating table 'active_service'
CREATE TABLE active_service (
  id INT AUTO_INCREMENT PRIMARY KEY,
  in_progress INT NOT NULL,
  new_requests INT NOT NULL,
  pending_invoices INT NOT NULL
);

-- Inserting data into 'active_service'
INSERT INTO active_service (id, in_progress, new_requests, pending_invoices) VALUES
(1, 3, 5, 3);

-- Creating 'daily_water_usage' table
CREATE TABLE daily_water_usage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  usage_liters DECIMAL(10,2) NOT NULL,
  target_liters DECIMAL(10,2) NOT NULL
);

-- Inserting data into 'daily_water_usage'
INSERT INTO daily_water_usage (date, usage_liters, target_liters) VALUES
('2025-04-01', 120, 130),
('2025-04-02', 125, 130),
('2025-04-03', 115, 130),
('2025-04-04', 130, 130),
('2025-04-05', 145, 130),
('2025-04-06', 150, 130),
('2025-04-07', 140, 130),
('2025-04-08', 135, 130),
('2025-04-09', 120, 130),
('2025-04-10', 110, 130),
('2025-04-11', 115, 130),
('2025-04-12', 125, 130),
('2025-04-13', 135, 130),
('2025-04-14', 130, 130),
('2025-04-15', 125, 130),
('2025-04-16', 120, 130),
('2025-04-17', 125, 130),
('2025-04-18', 128, 130),
('2025-04-19', 130, 130),
('2025-04-20', 130, 130),
('2025-04-21', 125, 130),
('2025-04-22', 120, 130),
('2025-04-23', 115, 130),
('2025-04-24', 110, 130),
('2025-04-25', 115, 130),
('2025-04-26', 120, 130),
('2025-04-27', 125, 130),
('2025-04-28', 130, 130),
('2025-04-29', 135, 130),
('2025-04-30', 105, 130);


-- Creating 'zone_performance' table
CREATE TABLE zone_performance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  zone_name VARCHAR(100),
  crop VARCHAR(50),
  usage_liters DECIMAL(10,2),
  target_liters DECIMAL(10,2)
);

-- Inserting data into 'zone_performance'
INSERT INTO zone_performance (zone_name, crop, usage_liters, target_liters) VALUES
('North Field', 'Wheat', 1250, 1400),
('South Field', 'Rice', 1800, 1600),
('East Garden', 'Corn', 400, 500);

-- Creating 'irrigation_zones' table
CREATE TABLE irrigation_zones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  zone_name VARCHAR(100) NOT NULL,
  moisture_level INT NOT NULL,
  status ENUM('Active', 'Irrigating', 'Low Moisture', 'Inactive') NOT NULL
);

-- Inserting data into 'irrigation_zones' (Matching hardcoded UI values)
INSERT INTO irrigation_zones (id, zone_name, moisture_level, status) VALUES
(1, 'Zone 1: North Field', 65, 'Active'),
(2, 'Zone 2: Orchard', 32, 'Low Moisture'),
(3, 'Zone 3: South Field', 78, 'Irrigating');

-- Creating 'irrigation_schedule' table
CREATE TABLE irrigation_schedule (
  id INT AUTO_INCREMENT PRIMARY KEY,
  zone_id INT NOT NULL,
  start_time TIME NOT NULL,
  duration INT NOT NULL COMMENT 'Duration in minutes',
  status ENUM('Pending', 'Completed') NOT NULL DEFAULT 'Pending',
  FOREIGN KEY (zone_id) REFERENCES irrigation_zones(id) ON DELETE CASCADE
);

-- Inserting data into 'irrigation_schedule' (Matching hardcoded UI values)
INSERT INTO irrigation_schedule (zone_id, start_time, duration, status) VALUES
(1, '06:00:00', 30, 'Pending'),
(2, '07:00:00', 45, 'Pending'),
(3, '17:30:00', 20, 'Pending');
