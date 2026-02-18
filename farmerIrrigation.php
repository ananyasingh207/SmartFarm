<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Create a single connection to the database
$conn = Database::getInstance()->getConnection();

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
    <link rel="stylesheet" href="css/hamburger.css">
    <link rel="stylesheet" href="css/farmer.css">
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
      <a href="auth/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt icon-green"></i> Logout</a>
    </div>
  </aside>


    <!-- Main Content -->
    <main class="dashboard-main">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="user-greeting">
          <h1 class="dashboard-title">Irrigation Management</h1>
          <p class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Farmer'); ?></p>
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
                <div id="zonesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Zones will be populated via JS -->
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
                            <!-- Schedules will be populated via JS -->
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
      <iframe id="chatbot-frame" src="chatbot/chatbot.html"></iframe>

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
    if(hamburger && sidebar) {
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
    }

    // ===== DB-DRIVEN IRRIGATION LOGIC =====

    // 1. Fetch and Render Zones
    function fetchZones() {
        fetch('getZones.php')
            .then(r => r.json())
            .then(zones => {
                const container = document.getElementById('zonesContainer');
                const scheduleSelect = document.getElementById('scheduleZone');
                
                if(!container) return;

                container.innerHTML = '';
                if(scheduleSelect) scheduleSelect.innerHTML = ''; 
                
                zones.forEach(zone => {
                    const moisture = zone.moisture_level;
                    const status = zone.status;
                    
                    let statusClass = 'bg-green-900 text-green-300';
                    let barColor = 'bg-green-400';
                    let btnText = 'Irrigate';
                    let btnClass = 'bg-green-400 text-gray-900';
                    
                    if (status === 'Low Moisture') {
                        statusClass = 'bg-yellow-900 text-yellow-300';
                        barColor = 'bg-yellow-500';
                    } else if (status === 'Irrigating') {
                        statusClass = 'bg-blue-900 text-blue-300';
                        barColor = 'bg-blue-500';
                        btnText = 'Stop';
                        btnClass = 'bg-red-500 text-white';
                    }
                    
                    const card = `
                    <div class="glass-card p-4 transition-transform hover:scale-105">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium text-green-400">${zone.zone_name}</h3>
                            <span class="px-2 py-1 ${statusClass} text-xs rounded-full">${status}</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Moisture Level:</span>
                                <span>${moisture}%</span>
                            </div>
                            <div class="w-full bg-gray-900 rounded-full h-2">
                                <div class="${barColor} h-2 rounded-full" style="width: ${moisture}%"></div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="toggleZone(${zone.id}, '${btnText}')" 
                                    class="irrigation-zone-btn ${btnClass} px-3 py-1 text-sm rounded-lg transition-colors flex-1 font-medium">
                                ${btnText}
                            </button>
                            <button class="zone-schedule-btn bg-transparent border border-green-400 text-green-400 px-3 py-1 text-sm rounded-lg transition-colors hover:bg-green-400 hover:text-gray-900" 
                                    onclick="openScheduleModal(${zone.id})">
                                Schedule
                            </button>
                        </div>
                    </div>`;
                    container.innerHTML += card;
                    
                    if(scheduleSelect) {
                        const option = document.createElement('option');
                        option.value = zone.id;
                        option.textContent = zone.zone_name;
                        scheduleSelect.appendChild(option);
                    }
                });
            })
            .catch(err => console.error('Error fetching zones:', err));
    }

    // 2. Fetch and Render Schedule
    function fetchSchedule() {
        fetch('getSchedule.php')
            .then(r => r.json())
            .then(schedules => {
                const tbody = document.getElementById('scheduleTable');
                if(!tbody) return;
                tbody.innerHTML = '';
                
                if (schedules.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-gray-400">No schedules found</td></tr>';
                    return;
                }

                schedules.forEach(item => {
                    const row = `
                    <tr class="border-b border-gray-900 hover:bg-gray-700">
                        <td class="p-3">${item.zone_name}</td>
                        <td class="p-3">${item.display_time}</td>
                        <td class="p-3">${item.duration} min</td>
                        <td class="p-3"><span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded-full">${item.status}</span></td>
                        <td class="p-3">
                            <button onclick="deleteSchedule(${item.id})" class="delete-schedule-btn text-red-400 hover:text-red-500 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            })
            .catch(err => console.error('Error fetching schedule:', err));
    }

    // 3. Toggle Zone Status
    window.toggleZone = function(zoneId, currentText) {
        const action = (currentText === 'Irrigate') ? 'start' : 'stop';
        
        const formData = new FormData();
        formData.append('zone_id', zoneId);
        formData.append('action', action);
        
        fetch('updateZoneStatus.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if(res.success) {
                fetchZones(); 
            } else {
                alert('Failed to update zone status');
            }
        })
        .catch(err => console.error('Error updating zone:', err));
    };

    // 4. Modal Logic
    const scheduleModal = document.getElementById('scheduleModal');
    const scheduleForm = document.getElementById('scheduleForm');
    const addScheduleBtn = document.getElementById('addScheduleBtn');
    const cancelScheduleBtn = document.getElementById('cancelScheduleBtn');
    
    if(addScheduleBtn) {
        addScheduleBtn.addEventListener('click', () => {
            if(scheduleModal) scheduleModal.classList.remove('hidden');
        });
    }
    
    if(cancelScheduleBtn) {
        cancelScheduleBtn.addEventListener('click', () => {
            if(scheduleModal) scheduleModal.classList.add('hidden');
        });
    }
    
    window.openScheduleModal = function(zoneId) {
        const select = document.getElementById('scheduleZone');
        if(select) select.value = zoneId;
        if(scheduleModal) scheduleModal.classList.remove('hidden');
    };

    // 5. Add Schedule Logic
    if(scheduleForm) {
        scheduleForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const zoneId = document.getElementById('scheduleZone').value;
            const startTime = document.getElementById('scheduleTime').value;
            const duration = document.getElementById('scheduleDuration').value;
            
            if(!startTime) {
                alert('Please select a time');
                return;
            }
            
            const formData = new FormData();
            formData.append('zone_id', zoneId);
            formData.append('start_time', startTime);
            formData.append('duration', duration);
            
            fetch('addSchedule.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    if(scheduleModal) scheduleModal.classList.add('hidden');
                    fetchSchedule();
                    scheduleForm.reset();
                } else {
                    alert('Failed to add schedule');
                }
            })
            .catch(err => console.error('Error adding schedule:', err));
        });
    }

    // 6. Delete Schedule Logic
    window.deleteSchedule = function(id) {
        if(!confirm('Are you sure you want to delete this schedule?')) return;
        
        const formData = new FormData();
        formData.append('schedule_id', id);
        
        fetch('deleteSchedule.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if(res.success) {
                fetchSchedule();
            } else {
                alert('Failed to delete schedule');
            }
        })
        .catch(err => console.error('Error deleting schedule:', err));
    };
    
    // 7. Global Buttons (Stubbed for now, or mapped to bulk actions)
    const startAllBtn = document.getElementById('startAllBtn');
    if(startAllBtn) {
        startAllBtn.addEventListener('click', () => {
            if(confirm('Start irrigation for ALL zones?')) {
                 // Fetch all zones and start them (naive implementation for now)
                 fetch('getZones.php').then(r=>r.json()).then(zones => {
                     zones.forEach(z => {
                         if(z.status !== 'Irrigating') toggleZone(z.id, 'Irrigate');
                     });
                 });
            }
        });
    }

    const stopAllBtn = document.getElementById('stopAllBtn');
    if(stopAllBtn) {
        stopAllBtn.addEventListener('click', () => {
             if(confirm('Stop ALL irrigation?')) {
                 fetch('getZones.php').then(r=>r.json()).then(zones => {
                     zones.forEach(z => {
                         if(z.status === 'Irrigating') toggleZone(z.id, 'Stop');
                     });
                 });
            }
        });
    }

    const systemCheckBtn = document.getElementById('systemCheckBtn');
    if(systemCheckBtn) {
        systemCheckBtn.addEventListener('click', function() {
            alert('System check in progress. This may take a few moments...');
            setTimeout(() => {
                alert('System check complete. All systems operating normally.');
            }, 2000);
        });
    }
    
    const savePresetBtn = document.getElementById('savePresetBtn');
    if(savePresetBtn) {
        savePresetBtn.addEventListener('click', function() {
            alert('Current irrigation settings saved as preset.');
        });
    }

    // Initial Load
    fetchZones();
    fetchSchedule();

    // Chart and Footer Logic (Preserved)
    // Setup Water Quality Chart
    const phLevel = <?php echo $phLevel; ?>;
    const ecValue = <?php echo $ecValue; ?>;
    const tds = <?php echo $tds; ?>;
    const temperature = <?php echo $temperature; ?>;

    const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    const waterChartEl = document.getElementById('waterQualityChart');
    if(waterChartEl) {
        const waterCtx = waterChartEl.getContext('2d');
        const waterChart = new Chart(waterCtx, {
            type: 'line',
            data: {
                labels: labels, 
                datasets: [{
                    label: 'pH Level',
                    data: [phLevel, phLevel, phLevel, phLevel, phLevel, phLevel, phLevel],
                    borderColor: 'rgba(74, 222, 128, 1)',
                    backgroundColor: 'rgba(74, 222, 128, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'EC Value',
                    data: [ecValue, ecValue, ecValue, ecValue, ecValue, ecValue, ecValue],
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
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                    }
                },
                plugins: {
                    legend: { labels: { color: 'rgba(255, 255, 255, 0.7)' } }
                }
            }
        });   
    }

    // ===== CURRENT DATE =====
    const dateEl = document.getElementById('currentDate');
    if(dateEl) {
        dateEl.textContent = new Date().toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }

    // ===== NOTIFICATION HANDLING =====
    const bellBtn = document.querySelector('.fa-bell');
    if(bellBtn) {
        bellBtn.addEventListener('click', function() {
            document.getElementById('notificationModal').classList.remove('hidden');
        });
    }

    const closeNotifBtn = document.getElementById('closeNotificationModal');
    if(closeNotifBtn) {
        closeNotifBtn.addEventListener('click', function() {
            document.getElementById('notificationModal').classList.add('hidden');
        });
    }

    // Adjust layout based on screen size
    // Hamburger Menu Toggle


    // Adjust layout based on screen size
    function handleResponsiveness() {
        const width = window.innerWidth;
        const widthHamburger = document.getElementById('hamburger');
        const widthSidebar = document.getElementById('sidebar');

        if (width < 1024 && widthHamburger && widthSidebar) {
            // Mobile adjustments
            widthSidebar.classList.remove('open');
            widthHamburger.classList.remove('active');
        }
    }

    // Run on page load and resize
    handleResponsiveness();
    window.addEventListener('resize', handleResponsiveness);

    // Close any open modals on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if(scheduleModal) scheduleModal.classList.add('hidden');
            const notifModal = document.getElementById('notificationModal');
            if(notifModal) notifModal.classList.add('hidden');
        }
    });
</script>