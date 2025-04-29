<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "irrigation";

// Create a single connection to the database
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch system status - sorting by last maintenance date
$systemQuery = "SELECT * FROM system_status ORDER BY last_maintenance DESC LIMIT 1";
$systemResult = $conn->query($systemQuery);
if ($systemResult && $systemResult->num_rows > 0) {
  $system = $systemResult->fetch_assoc();
  $systemHealth = $system['system_health'];
  $lastMaintenance = date("F d, Y", strtotime($system['last_maintenance']));
  $sensorsOnline = $system['sensors_online'];
  $sensorsTotal = $system['sensors_total'];
} else {
  $systemHealth = 'Unknown';
  $lastMaintenance = 'N/A';
  $sensorsOnline = 0;
  $sensorsTotal = 0;
}

// Fetch water status - sorting by recorded_at
$waterQuery = "SELECT * FROM water_status ORDER BY recorded_at DESC LIMIT 1";
$waterResult = $conn->query($waterQuery);
if ($waterResult && $waterResult->num_rows > 0) {
  $water = $waterResult->fetch_assoc();
  $waterPressure = $water['water_pressure'];
  $tankLevel = $water['tank_level'];
} else {
  $waterPressure = 0;
  $tankLevel = 0;
}

// Fetch water quality analysis - sorting by recorded_at
$waterQualityQuery = "SELECT * FROM water_quality ORDER BY recorded_at DESC LIMIT 1";
$waterQualityResult = $conn->query($waterQualityQuery);
if ($waterQualityResult && $waterQualityResult->num_rows > 0) {
  $waterQuality = $waterQualityResult->fetch_assoc();
  $phLevel = $waterQuality['ph_level'];
  $ecValue = $waterQuality['ec_value'];
  $tds = $waterQuality['tds'];
  $temperature = $waterQuality['temperature'];
  $status = $waterQuality['status'];
} else {
  $phLevel = 0;
  $ecValue = 0;
  $tds = 0;
  $temperature = 0;
  $status = 'Unknown';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Irrigation Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0B1120 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
        }

        
        .glass-card {
            background: rgba(31, 41, 55, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(74, 222, 128, 0.3);
            border-radius: 0.75rem;
        }

          
  .dashboard-header {
    background-color: #111827;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  }

  .user-greeting {
    margin-left: 5rem;
  }

  .dashboard-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #4ade80;
  }

  .user-name {
    color: #cbd5e1;
    font-size: 1rem;
  }

  .user-role {
    color: #cbd5e1;
    font-size: 0.875rem;
    opacity: 0.8;
  }

  .header-controls {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .current-date {
    color: #cbd5e1;
    font-weight: 500;
    font-size: 0.875rem;
  }

  .user-avatar {
    width: 3rem;  
    height: 3rem; 
    border-radius: 50%;
    border: 2px solid #4ade80; 
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #1f2937; 
  }
  
  .user-avatar i {
    font-size: 2.8rem; 
    color: #4ade80; 
  }
   
  /* Dashboard Content */
  .dashboard-content {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
  }

  .dashboard-content::-webkit-scrollbar {
    width: 8px;
  }

  .dashboard-content::-webkit-scrollbar-track {
    background: #1e293b;
  }

  .dashboard-content::-webkit-scrollbar-thumb {
    background: #4ade80;
    border-radius: 4px;
  }

    /* Hamburger Button */
  .hamburger-toggle {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 50;
    padding: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .hamburger-toggle:hover {
    background-color: #1e293b;
  }

  .hamburger-toggle span {
    display: block;
    width: 24px;
    height: 3px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 2px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
  }

  .hamburger-toggle span:last-child {
    margin-bottom: 0;
  }

  .hamburger-toggle:hover span {
    background: #86efac;
    transform: scaleX(1.1);
  }

  .hamburger-toggle.active span:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
    background: #4ade80;
  }

  .hamburger-toggle.active span:nth-child(2) {
    opacity: 0;
  }

  .hamburger-toggle.active span:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
    background: #4ade80;
  }

  /* Sidebar */
  .sidebar-container {
    width: 16rem;
    background-color: #111827;
    color: #ffffff;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    z-index: 40;
    transition: transform 0.4s ease-in-out;
    transform: translateX(-100%);
  }

  .sidebar-container.open {
    transform: translateX(0);
  }

  .sidebar-brand {
    padding: 3rem 1.5rem 1.5rem;
    display: flex;
    align-items: center;
  }

  .sidebar-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #4ade80;
  }

  .sidebar-nav {
    flex: 1;
    padding: 1rem;
  }

  .sidebar-nav-item {
    margin-bottom: 0.75rem;
    opacity: 0;
    transform: translateX(-20px);
    transition: opacity 0.3s ease, transform 0.3s ease;
  }

  .sidebar-container.open .sidebar-nav-item:nth-child(1) { transition-delay: 0.1s; opacity: 1; transform: translateX(0); }
  .sidebar-container.open .sidebar-nav-item:nth-child(2) { transition-delay: 0.2s; opacity: 1; transform: translateX(0); }
  .sidebar-container.open .sidebar-nav-item:nth-child(3) { transition-delay: 0.3s; opacity: 1; transform: translateX(0); }
  .sidebar-container.open .sidebar-nav-item:nth-child(4) { transition-delay: 0.4s; opacity: 1; transform: translateX(0); }
  .sidebar-container.open .sidebar-nav-item:nth-child(5) { transition-delay: 0.5s; opacity: 1; transform: translateX(0); }
  .sidebar-container.open .sidebar-nav-item:nth-child(6) { transition-delay: 0.6s; opacity: 1; transform: translateX(0); }

  .sidebar-link {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    color: #ffffff;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }

  .sidebar-link:hover {
    background-color: #1e293b;
  }

  .icon-green {
    margin-right: 0.75rem;
    color: #4ade80;
  }

  .sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #1e293b;
  }

        /* Main Content & Header */
        main {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background: #111827;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .content {
            padding: 1.5rem;
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Water analysis chart styling */
        .chart-container {
            width: 100%;
            height: 200px;
            position: relative;
        }

        #chatbot-toggle {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 45px; 
        height: 45px; 
        background-color: transparent;
        color: #ccc;
        border-radius: 50%;
        border: 2px solid #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 18px; 
        cursor: pointer;
        z-index: 1002;
        box-shadow: none;
        transition: all 0.3s ease;
        }

        #chatbot-toggle.active {
        background-color: #22c55e;
        color: white;
        border-color: #22c55e;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }


        #chatbot-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        backdrop-filter: blur(6px);
        background-color: rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
        }

        #chatbot-overlay.show {
        display: block;
        opacity: 1;
        }

        #chatbot-frame {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 400px;
        height: 600px;
        border: none;
        border-radius: 20px;
        z-index: 1001;
        overflow: hidden;
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
        opacity: 0;
        transform: translateY(20px);
        pointer-events: none;
        transition: all 0.3s ease;
        }

        #chatbot-frame.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        }

        .dashboard-main {
            transition: margin-left 0.4s ease-in-out;
        }
        .sidebar-container.open + .dashboard-main {
            margin-left: 16rem;
        } 
        @media (max-width: 1024px) {
            .sidebar-container.open + .dashboard-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Hamburger Button -->
  <div id="hamburger" class="hamburger-toggle">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <!-- Sidebar (Hidden by Default) -->
  <aside id="sidebar" class="sidebar-container">
    <div class="sidebar-brand">
      <span class="sidebar-title">SmartFarm</span>
    </div>
    <nav class="sidebar-nav">
      <ul type="none">
        <li class="sidebar-nav-item"><a href="farmer.php" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
        <li class="sidebar-nav-item"><a href="farmerProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
        <li class="sidebar-nav-item"><a href="#" class="sidebar-link"><i class="fas fa-tint icon-green"></i> Irrigation</a></li>
        <li class="sidebar-nav-item"><a href="farmerWater.php" class="sidebar-link"><i class="fas fa-chart-bar icon-green"></i> Water Usage</a></li>
        <li class="sidebar-nav-item"><a href="index.php" class="sidebar-link"><i class="fas fa-arrow-left icon-green"></i> Back to Home</a></li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <a href="#" class="sidebar-link"><i class="fas fa-sign-out-alt icon-green"></i> Logout</a>
    </div>
  </aside>


    <!-- Main Content -->
    <main class="dashboard-main">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="user-greeting">
          <h1 class="dashboard-title">Irrigation Management</h1>
          <p class="user-name">Welcome, Farmer</p>
          <!-- <p class="user-role">(Farmer)</p> -->
        </div>
        <div class="header-controls">
            <span class="current-date"><?php echo date("F j, Y"); ?></span>
            <div class="user-avatar">
             <a href="farmerProfile.php" style="color: #4ade80; text-decoration: none;">
                <i class="fas fa-user"></i>
             </a>
            </div>
          </div>
            <!-- <i class="fas fa-user"></i> -->
          </div>
        </div>
      </header>
  

        <!-- Quick Action Bar -->
        <div class="bg-gray-800 py-3 px-4 flex justify-around">
            <button id="startAllBtn" class="text-gray-300 hover:text-green-400 hover:bg-opacity-20 hover:bg-green-400 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-play mr-2"></i> Start All
            </button>
            <button id="stopAllBtn" class="text-gray-300 hover:text-green-400 hover:bg-opacity-20 hover:bg-green-400 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-stop mr-2"></i> Stop All
            </button>
            <button id="systemCheckBtn" class="text-gray-300 hover:text-green-400 hover:bg-opacity-20 hover:bg-green-400 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-sync-alt mr-2"></i> System Check
            </button>
            <button id="savePresetBtn" class="text-gray-300 hover:text-green-400 hover:bg-opacity-20 hover:bg-green-400 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i> Save Preset
            </button>
        </div>

        <!-- Content Area -->
        <div class="content">
            <!-- Irrigation Zones -->
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-green-400 mb-4 flex items-center">
                    <i class="fas fa-map-marked-alt mr-2"></i> Irrigation Zones
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Zone 1 -->
                    <div class="glass-card p-4 transition-transform hover:scale-105">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium text-green-400">Zone 1: North Field</h3>
                            <span class="px-2 py-1 bg-green-900 text-green-300 text-xs rounded-full">Active</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Moisture Level:</span>
                                <span>65%</span>
                            </div>
                            <div class="w-full bg-gray-900 rounded-full h-2">
                                <div class="bg-green-400 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="irrigation-zone-btn bg-green-400 text-gray-900 px-3 py-1 text-sm rounded-lg transition-colors flex-1 font-medium">Irrigate</button>
                            <button class="zone-schedule-btn bg-transparent border border-green-400 text-green-400 px-3 py-1 text-sm rounded-lg transition-colors hover:bg-green-400 hover:text-gray-900" data-zone="Zone 1: North Field">Schedule</button>
                        </div>
                    </div>
                    
                    <!-- Zone 2 -->
                    <div class="glass-card p-4 transition-transform hover:scale-105">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium text-green-400">Zone 2: Orchard</h3>
                            <span class="px-2 py-1 bg-yellow-900 text-yellow-300 text-xs rounded-full">Low Moisture</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Moisture Level:</span>
                                <span>32%</span>
                            </div>
                            <div class="w-full bg-gray-900 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 32%"></div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="irrigation-zone-btn bg-green-400 text-gray-900 px-3 py-1 text-sm rounded-lg transition-colors flex-1 font-medium">Irrigate</button>
                            <button class="zone-schedule-btn bg-transparent border border-green-400 text-green-400 px-3 py-1 text-sm rounded-lg transition-colors hover:bg-green-400 hover:text-gray-900" data-zone="Zone 2: Orchard">Schedule</button>
                        </div>
                    </div>
                    
                    <!-- Zone 3 -->
                    <div class="glass-card p-4 transition-transform hover:scale-105">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium text-green-400">Zone 3: South Field</h3>
                            <span class="px-2 py-1 bg-blue-900 text-blue-300 text-xs rounded-full">Irrigating</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Moisture Level:</span>
                                <span>78%</span>
                            </div>
                            <div class="w-full bg-gray-900 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 78%"></div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="irrigation-zone-btn bg-red-500 text-white px-3 py-1 text-sm rounded-lg transition-colors flex-1 font-medium">Stop</button>
                            <button class="zone-schedule-btn bg-transparent border border-green-400 text-green-400 px-3 py-1 text-sm rounded-lg transition-colors hover:bg-green-400 hover:text-gray-900" data-zone="Zone 3: South Field">Schedule</button>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Irrigation Schedule -->
            <section class="glass-card p-6 mb-8">
                <h2 class="text-xl font-semibold text-green-400 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i> Irrigation Schedule
                </h2>
                <div class="bg-gray-800 rounded-lg overflow-hidden mb-4">
                    <table class="w-full text-left text-gray-300 text-sm">
                        <thead>
                            <tr class="border-b border-gray-900">
                                <th class="p-3 font-semibold text-green-400">Zone</th>
                                <th class="p-3 font-semibold text-green-400">Start Time</th>
                                <th class="p-3 font-semibold text-green-400">Duration</th>
                                <th class="p-3 font-semibold text-green-400">Status</th>
                                <th class="p-3 font-semibold text-green-400">Action</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTable">
                            <tr class="border-b border-gray-900 hover:bg-gray-700" data-id="1">
                                <td class="p-3">Zone 1</td>
                                <td class="p-3">06:00 AM</td>
                                <td class="p-3">30 min</td>
                                <td class="p-3"><span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded-full">Pending</span></td>
                                <td class="p-3"><button class="delete-schedule-btn text-red-400 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button></td>
                            </tr>
                            <tr class="border-b border-gray-900 hover:bg-gray-700" data-id="2">
                                <td class="p-3">Zone 2</td>
                                <td class="p-3">07:00 AM</td>
                                <td class="p-3">45 min</td>
                                <td class="p-3"><span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded-full">Pending</span></td>
                                <td class="p-3"><button class="delete-schedule-btn text-red-400 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button></td>
                            </tr>
                            <tr class="hover:bg-gray-700" data-id="3">
                                <td class="p-3">Zone 3</td>
                                <td class="p-3">05:30 PM</td>
                                <td class="p-3">20 min</td>
                                <td class="p-3"><span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded-full">Pending</span></td>
                                <td class="p-3"><button class="delete-schedule-btn text-red-400 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button id="addScheduleBtn" class="bg-transparent border border-green-400 text-green-400 px-4 py-2 rounded-lg hover:bg-green-400 hover:text-gray-900 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add Schedule
                </button>
            </section>
            
            <!-- System Stats & Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- System Status -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-medium text-green-400 mb-4 flex items-center">
                        <i class="fas fa-heartbeat mr-2"></i> System Status
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Water Pressure</span>
                            <span class="text-green-400"><?php echo $waterPressure; ?> PSI</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Tank Level</span>
                            <div class="flex items-center">
                                <span class="text-green-400 mr-2"><?php echo $tankLevel; ?>%</span>
                                <div class="w-16 bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-400 h-2 rounded-full" style="width: <?php echo $tankLevel; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">System Health</span>
                            <span class="text-green-400 flex items-center">
                                <i class="fas fa-check-circle mr-1"></i> <?php echo $systemHealth; ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Last Maintenance</span>
                            <span class="text-gray-300"><?php echo $lastMaintenance; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Sensors Online</span>
                            <span class="text-green-400"><?php echo "$sensorsOnline/$sensorsTotal"; ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Water Quality Analysis (Replacing Weather) -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-medium text-green-400 mb-4 flex items-center">
                        <i class="fas fa-flask mr-2"></i> Water Quality Analysis
                    </h3>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">pH Level</span>
                            <span class="text-green-400 flex items-center">
                                <?php echo $phLevel; ?> <i class="fas fa-check-circle ml-1 text-green-400"></i>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">EC Value</span>
                            <span class="text-yellow-400 flex items-center">
                            <?php echo $ecValue; ?> mS/cm <i class="fas fa-exclamation-circle ml-1 text-yellow-400"></i>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">TDS</span>
                            <span class="text-green-400 flex items-center">
                                <?php echo $tds; ?> ppm <i class="fas fa-check-circle ml-1 text-green-400"></i>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Temperature</span>
                            <span class="text-green-400"><?php echo $temperature; ?>°C</span>
                        </div>
                    </div>
                    <div class="w-full rounded-lg bg-gray-800 p-2">
                        <div class="chart-container">
                            <canvas id="waterQualityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Schedule Modal -->
    <div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="glass-card p-6 rounded-lg max-w-md w-full">
            <h3 class="text-lg font-semibold text-green-400 mb-4">Add Irrigation Schedule</h3>
            <form id="scheduleForm">
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Zone</label>
                    <select id="scheduleZone" class="w-full p-2 bg-gray-800 border border-green-400 text-white rounded-lg focus:outline-none">
                        <option value="Zone 1">Zone 1: North Field</option>
                        <option value="Zone 2">Zone 2: Orchard</option>
                        <option value="Zone 3">Zone 3: South Field</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Start Time</label>
                    <input type="time" id="scheduleTime" class="w-full p-2 bg-gray-800 border border-green-400 text-white rounded-lg focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Duration (minutes)</label>
                    <input type="number" id="scheduleDuration" min="5" max="120" value="30" class="w-full p-2 bg-gray-800 border border-green-400 text-white rounded-lg focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Repeat</label>
                    <select id="scheduleRepeat" class="w-full p-2 bg-gray-800 border border-green-400 text-white rounded-lg focus:outline-none">
                        <option value="once">Once</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelScheduleBtn" class="bg-transparent border border-green-400 text-green-400 px-4 py-2 rounded-lg hover:bg-green-400 hover:text-gray-900 transition-colors">Cancel</button>
                    <button type="submit" class="bg-green-400 text-gray-900 px-4 py-2 rounded-lg hover:bg-green-500 transition-colors font-medium">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Chatbot Button -->
    <div id="chatbot-toggle">
        <i class="fas fa-comment-dots"></i>
      </div>
  
      <!-- Background Overlay Blur -->
      <div id="chatbot-overlay"></div>
  
      <!-- Chatbot iframe popup -->
      <iframe id="chatbot-frame" src="chatbot.html"></iframe>

    <script>

        document.addEventListener("DOMContentLoaded", () => {
        const chatbotBtn = document.getElementById("chatbot-toggle");
        const chatbotFrame = document.getElementById("chatbot-frame");
        const chatbotOverlay = document.getElementById("chatbot-overlay");

        chatbotBtn.addEventListener("click", () => {
            const isOpen = chatbotFrame.classList.contains("show");
            chatbotFrame.classList.toggle("show");
            chatbotOverlay.classList.toggle("show");
            chatbotBtn.classList.toggle("active", !isOpen);
        });

        chatbotOverlay.addEventListener("click", () => {
            chatbotFrame.classList.remove("show");
            chatbotOverlay.classList.remove("show");
            chatbotBtn.classList.remove("active");
        });
        });

        window.addEventListener("message", function (event) {
        if (event.data.action === "closeChatbot") {
            document.getElementById("chatbot-frame").classList.remove("show");
            document.getElementById("chatbot-overlay").classList.remove("show");
            document.getElementById("chatbot-toggle").classList.remove("active");
        }
        });
       // Hamburger Menu Toggle
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      hamburger.classList.toggle('active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 768 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.classList.remove('open');
        hamburger.classList.remove('active');
      }
    });

            // ===== IRRIGATION ZONE BUTTONS =====
            document.querySelectorAll('.irrigation-zone-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.textContent === 'Irrigate') {
                        this.textContent = 'Stop';
                        this.classList.remove('bg-green-400', 'text-gray-900');
                        this.classList.add('bg-red-500', 'text-white');
                        // Find parent zone div and update status
                        const zoneDiv = this.closest('div.glass-card');
                        const statusSpan = zoneDiv.querySelector('span.rounded-full');
                        statusSpan.className = 'px-2 py-1 bg-blue-900 text-blue-300 text-xs rounded-full';
                        statusSpan.textContent = 'Irrigating';
                    } else {
                        this.textContent = 'Irrigate';
                        this.classList.remove('bg-red-500', 'text-white');
                        this.classList.add('bg-green-400', 'text-gray-900');
                        // Find parent zone div and update status
                        const zoneDiv = this.closest('div.glass-card');
                        const statusSpan = zoneDiv.querySelector('span.rounded-full');
                        statusSpan.className = 'px-2 py-1 bg-green-900 text-green-300 text-xs rounded-full';
                        statusSpan.textContent = 'Active';
                    }
                });
            });

            // ===== SCHEDULE MANAGEMENT =====
            // Variables
            const scheduleTable = document.getElementById('scheduleTable');
            const scheduleModal = document.getElementById('scheduleModal');
            const scheduleForm = document.getElementById('scheduleForm');
            const addScheduleBtn = document.getElementById('addScheduleBtn');
            const cancelScheduleBtn = document.getElementById('cancelScheduleBtn');
            
            let nextScheduleId = 4; // Starting ID (after existing 3 items)
            
            // Generate unique IDs for schedules
            function generateScheduleId() {
                return nextScheduleId++;
            }
            
            // Zone schedule buttons
            document.querySelectorAll('.zone-schedule-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const zoneName = this.getAttribute('data-zone');
                    const zoneSelect = document.getElementById('scheduleZone');
                    
                    // Select the corresponding zone in the form
                    for (let i = 0; i < zoneSelect.options.length; i++) {
                        if (zoneSelect.options[i].text.includes(zoneName.split(':')[0])) {
                            zoneSelect.selectedIndex = i;
                            break;
                        }
                    }
                    
                    scheduleModal.classList.remove('hidden');
                });
            });

            // Delete schedule buttons
            function setupDeleteScheduleButtons() {
                document.querySelectorAll('.delete-schedule-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        row.remove();
                    });
                });
            }
            
            // Initialize delete buttons
            setupDeleteScheduleButtons();

            // Open schedule modal
            addScheduleBtn.addEventListener('click', () => {
                scheduleModal.classList.remove('hidden');
            });

            // Close schedule modal
            cancelScheduleBtn.addEventListener('click', () => {
                scheduleModal.classList.add('hidden');
            });

            // Format time helper function
            function formatTime(timeString) {
                if (!timeString) return "12:00 AM";
                
                const [hours, minutes] = timeString.split(':');
                let h = parseInt(hours);
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12; // Convert 0 to 12
                return `${h}:${minutes} ${ampm}`;
            }

           // Handle schedule form submission
    scheduleForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const zone = document.getElementById('scheduleZone').value;
    const time = document.getElementById('scheduleTime').value;
    const duration = document.getElementById('scheduleDuration').value;
    const repeat = document.getElementById('scheduleRepeat').value;
    
    // Create new schedule row
    const newRow = document.createElement('tr');
    newRow.className = 'border-b border-gray-900 hover:bg-gray-700';
    newRow.dataset.id = generateScheduleId();
    
    // Format zone name to show just the zone number
    const zoneNum = zone.split(':')[0];
    
    // Create row content
    newRow.innerHTML = `
        <td class="p-3">${zoneNum}</td>
        <td class="p-3">${formatTime(time)}</td>
        <td class="p-3">${duration} min</td>
        <td class="p-3"><span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded-full">Pending</span></td>
        <td class="p-3"><button class="delete-schedule-btn text-red-400 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button></td>
    `;
    
    // Add to table
    scheduleTable.appendChild(newRow);
    
    // Setup delete button for the new row
    setupDeleteScheduleButtons();
    
    // Close modal
    scheduleModal.classList.add('hidden');
    
    // Reset form
    scheduleForm.reset();
});

// ===== GLOBAL ACTION BUTTONS =====
// Start All Button
document.getElementById('startAllBtn').addEventListener('click', function() {
    document.querySelectorAll('.irrigation-zone-btn').forEach(btn => {
        if (btn.textContent === 'Irrigate') {
            btn.click(); // Trigger the click event
        }
    });
});

// Stop All Button
document.getElementById('stopAllBtn').addEventListener('click', function() {
    document.querySelectorAll('.irrigation-zone-btn').forEach(btn => {
        if (btn.textContent === 'Stop') {
            btn.click(); // Trigger the click event
        }
    });
});

// System Check Button
document.getElementById('systemCheckBtn').addEventListener('click', function() {
    alert('System check in progress. This may take a few moments...');
    
    // Simulate system check
    setTimeout(() => {
        alert('System check complete. All systems operating normally.');
    }, 2000);
});

// Save Preset Button
document.getElementById('savePresetBtn').addEventListener('click', function() {
    alert('Current irrigation settings saved as preset.');
});


    // Setup Water Quality Chart
    const phLevel = <?php echo $phLevel; ?>;
    const ecValue = <?php echo $ecValue; ?>;
    const tds = <?php echo $tds; ?>;
    const temperature = <?php echo $temperature; ?>;

    const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    const waterCtx = document.getElementById('waterQualityChart').getContext('2d');
    const waterChart = new Chart(waterCtx, {
        type: 'line',
        data: {
            labels: labels, // Use the PHP-generated labels or static values
            datasets: [{
                label: 'pH Level',
                data: [phLevel, phLevel, phLevel, phLevel, phLevel, phLevel, phLevel],  // Using pH Level for simplicity here
                borderColor: 'rgba(74, 222, 128, 1)',
                backgroundColor: 'rgba(74, 222, 128, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'EC Value',
                data: [ecValue, ecValue, ecValue, ecValue, ecValue, ecValue, ecValue],  // Using EC Value for simplicity here
                borderColor: 'rgba(251, 191, 36, 1)',
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            }
        }
    });   



// ===== CURRENT DATE =====
// Set current date
document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
});

// ===== NOTIFICATION HANDLING =====
// Show notification modal
document.querySelector('.fa-bell').addEventListener('click', function() {
    document.getElementById('notificationModal').classList.remove('hidden');
});

// Close notification modal
document.getElementById('closeNotificationModal').addEventListener('click', function() {
    document.getElementById('notificationModal').classList.add('hidden');
});
// Adjust layout based on screen size
function handleResponsiveness() {
    const width = window.innerWidth;
    
    if (width < 768) {
        // Mobile adjustments
        sidebar.classList.remove('sidebar-open');
        hamburger.classList.remove('active');
    } else {
        // Desktop adjustments
        // You might want to auto-open sidebar on desktop
        // sidebar.classList.add('sidebar-open');
    }
}

// Run on page load and resize
handleResponsiveness();
window.addEventListener('resize', handleResponsiveness);

// Close any open modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        scheduleModal.classList.add('hidden');
        document.getElementById('notificationModal').classList.add('hidden');
    }
});
</script>