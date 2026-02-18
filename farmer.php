<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'farmer') {
    header("Location: auth/login.php");
    exit();
}



require_once 'db.php';
$conn = Database::getInstance()->getConnection();

// Fetch active devices
$activeDevicesQuery = "SELECT COUNT(*) as total FROM devices WHERE status = 'online'";
$activeDevicesResult = $conn->query($activeDevicesQuery);
$activeDevices = $activeDevicesResult->fetch_assoc()['total'] ?? 0;

// Fetch average soil moisture
$moistureQuery = "SELECT AVG(value) as avg_moisture FROM sensors WHERE sensor_type = 'soil_moisture'";
$moistureResult = $conn->query($moistureQuery);
$soilMoisture = round($moistureResult->fetch_assoc()['avg_moisture'] ?? 0, 1);

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Irrigation Dashboard</title>
  <!-- Chart.js for visualizations -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Leaflet for maps -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Google Fonts: Poppins -->
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
        <li class="menu-item"><a href="#" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
        <li class="menu-item"><a href="farmerProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
        <li class="menu-item"><a href="farmerIrrigation.php" class="sidebar-link"><i class="fas fa-tint icon-green"></i> Irrigation</a></li>
        <li class="menu-item"><a href="farmerWater.php" class="sidebar-link"><i class="fas fa-chart-bar icon-green"></i> Water Usage</a></li>
        <li class="menu-item"><a href="index.php" class="sidebar-link"><i class="fas fa-arrow-left icon-green"></i> Back to Home</a></li>
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
        <h1 class="dashboard-title">Dashboard</h1>
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
    </header>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
      <!-- Overview Widget -->
      <div class="widget-grid">
        <div class="widget-card">
          <h2 class="widget-title"><i class="fas fa-plug icon-padding"></i> Active Devices</h2>
          <p class="widget-value"><?php echo $activeDevices; ?> Online</p>
          <button id="manageDevices" class="widget-action">Manage Devices</button>
        </div>
        <div class="widget-card">
          <h2 class="widget-title"><i class="fas fa-tint icon-padding"></i> Water Usage</h2>
          <p class="widget-value"><?php echo $soilMoisture; ?>%</p>
          <a href="#water"><button class="widget-action">Track Usage</button></a>
        </div>
        <div class="widget-card">
          <h2 class="widget-title"><i class="fas fa-sun icon-padding"></i> Weather</h2>
            <p id="location">Fetching location...</p>
            <div id="weather" class="data">Loading...</div>
        </div>
      </div>

      <!-- Irrigation Control -->
      <div class="irrigation-panel">
        <h2 class="irrigation-title"><i class="fas fa-spray-can icon-padding"></i> Irrigation Control</h2>
        <div class="irrigation-grid">
          <!-- Device Controls -->
          <div>
            <div class="device-info">
              <p><strong>Device:</strong> Smart Sprinkler X1</p>
              <p><strong>Status:</strong> <span id="deviceStatus" class="device-status">Online</span></p>
              <p><strong>Soil Moisture:</strong> 68%</p>
              <p><strong>Water Flow:</strong> 2.5 L/min</p>
            </div>
            <div class="irrigation-buttons">
              <button id="toggleIrrigation" class="irrigation-btn">Start Irrigation</button>
              <button id="scheduleIrrigation" class="irrigation-btn schedule">Schedule</button>
            </div>
          </div>
          <!-- Farm Plot Map -->
          <div>
            <div id="farm-map"></div>
          </div>
        </div>
      </div>

      <!-- Water Usage Tracking -->
      <div id="water" class="usage-panel">
        <h2 class="usage-title"><i class="fas fa-chart-line icon-padding"></i> Water Usage Tracking</h2>
        <div class="usage-controls">
          <select id="timeRange" class="time-range">
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="year">This Year</option>
          </select>
          <button id="downloadReport" class="download-report">Download Report</button>
        </div>
        <canvas id="usageChart" height="100"></canvas>
      </div>

      <!-- Service Finder -->
      <div class="service-panel">
        <h2 class="service-title"><i class="fas fa-search icon-padding"></i> Service Finder</h2>
        <div class="service-search">
          <input type="text" id="serviceSearch" placeholder="Search by location, service, or crop..." class="service-input">
          <button class="search-btn">Search</button>
        </div>
        <div class="table-wrapper">
          <table class="service-table">
            <thead>
              <tr>
                <th>Provider</th>
                <th>Service</th>
                <th>Location</th>
                <th>Rating</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="serviceTable">
              <tr>
                <td>GreenTech Solutions</td>
                <td>Drip Irrigation Setup</td>
                <td>3 km away</td>
                <td>4.8/5</td>
                <td><button class="contact-btn">Contact</button></td>
              </tr>
              <tr>
                <td>Aqua Innovate</td>
                <td>Sensor Maintenance</td>
                <td>7 km away</td>
                <td>4.5/5</td>
                <td><button class="contact-btn">Contact</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Crop Irrigation Insight -->
      <section class="insight-panel">
        <h2 class="insight-title">Crop Irrigation Insight</h2>
        <div class="insight-form">
          <input id="aiCropInput" type="text" placeholder="Enter your crop (e.g., wheat)" class="insight-input">
          <input id="aiLocationInput" type="text" placeholder="Enter your location (e.g., Rajasthan)" class="insight-input">
        </div>
        <button onclick="getAIRecommendation()" class="insight-btn">View Crop Insight</button>
        <div id="aiResponse" class="insight-response"></div>
      </section>
    </div>
  </main>
  <footer class="footer">
    <div class="footer-container">
        <div class="footer-row">
          <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="FAQs.html">FAQ</a></li>
            </ul>
          </div>
          <div class="footer-socials">
            <h3>Follow Us</h3>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
          </div>
            <div class="contact-details">
                <h3>Contact Us</h3>
                <p>Email: support@smartirrigation.com</p>
                <p>Phone: +1 234 567 890</p>
            </div>
          </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2025 Smart Irrigation System. All rights reserved.</p>
    </div>
</footer>
  <!-- Floating Chatbot Button -->
  <div id="chatbot-toggle">
    <i class="fas fa-comment-dots"></i>
  </div>

  <!-- Background Overlay Blur -->
  <div id="chatbot-overlay"></div>

  <!-- Chatbot iframe popup -->
  <iframe id="chatbot-frame" src="chatbot/chatbot.html"></iframe>

  <!-- JavaScript (Including Font Awesome for Icons) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
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
    function fetchWeather(lat, lon) {
    const url = `weather_api.php?lat=${lat}&lon=${lon}`;
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById("weather").innerHTML = 
                `${data.weather[0].main}, ${data.main.temp}°C`;
        })
        .catch(error => {
            console.error("Weather fetch error:", error);
            document.getElementById("weather").innerHTML = "⚠️ Failed to load weather.";
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async position => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                const data = await response.json();
                const locationName = data.address.city || data.address.town || data.address.village || "Unknown location";
                document.getElementById("location").innerText = locationName;
                fetchWeather(lat, lon);
            } catch (error) {
                document.getElementById("location").innerText = "Location unavailable";
            }
        }, () => {
            document.getElementById("location").innerText = "Permission denied";
            document.getElementById("weather").innerText = "⚠️ Cannot fetch weather without location access.";
    });

    } else {
        document.getElementById("location").innerText = "Geolocation not supported";
        document.getElementById("weather").innerText = "⚠️ Weather unavailable.";
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
      if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.classList.remove('open');
        hamburger.classList.remove('active');
      }
    });

    // Toggle Irrigation
    const toggleButton = document.getElementById('toggleIrrigation');
    const deviceStatus = document.getElementById('deviceStatus');
    toggleButton.addEventListener('click', () => {
      toggleButton.classList.toggle('irrigating');
      if (toggleButton.classList.contains('irrigating')) {
        toggleButton.textContent = 'Stop Irrigation';
        deviceStatus.textContent = 'Irrigating';
      } else {
        toggleButton.textContent = 'Start Irrigation';
        deviceStatus.textContent = 'Online';
      }
    });

    // Schedule Irrigation (Placeholder)
    document.getElementById('scheduleIrrigation').addEventListener('click', () => {
      alert('Please go to the irrigation schedule page to set up your irrigation schedule.');
    });

    // Crop Selector (Placeholder)


    // Farm Plot Map
    // Initialize the map centered on Phagwara, Punjab
    const map = L.map('farm-map').setView([31.2245, 75.7739], 12);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
    }).addTo(map);

    // Water Usage Chart
    const ctx = document.getElementById('usageChart').getContext('2d');
    const usageChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [], // Dynamic
        datasets: [{
          label: 'Water Usage (Liters)',
          data: [], // Dynamic
          backgroundColor: '#1d8043',
          borderColor: '#afb3b0',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: { color: '#cbd5e1' },
            grid: { color: '#374151' }
          },
          x: {
            ticks: { color: '#cbd5e1' },
            grid: { color: '#374151' }
          }
        },
        plugins: {
          legend: {
            labels: { color: '#cbd5e1' }
          }
        }
      }
    });

    // Fetch Data Function
    function fetchDashboardData(range) {
        fetch(`getDashboardWaterData.php?range=${range}`)
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                
                usageChart.data.labels = data.labels;
                usageChart.data.datasets[0].data = data.data;
                usageChart.update();
            })
            .catch(error => console.error('Fetch error:', error));
    }

    // Initial Load
    fetchDashboardData('week');

    // Time Range Filter
    document.getElementById('timeRange').addEventListener('change', (e) => {
      const range = e.target.value;
      fetchDashboardData(range);
    });

    // Download Report Button
    document.getElementById('downloadReport').addEventListener('click', () => {
      const range = document.getElementById('timeRange').value;
      window.location.href = `download_report.php?range=${range}`;
    });

    // Service Finder Search
    const serviceSearch = document.getElementById('serviceSearch');
    serviceSearch.addEventListener('input', () => {
      const query = serviceSearch.value.toLowerCase();
      const rows = document.querySelectorAll('#serviceTable tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });

    // Crop Irrigation Insight (Placeholder AI Recommendation)
    async function getAIRecommendation() {
      const crop = document.getElementById('aiCropInput').value.trim();
      const location = document.getElementById('aiLocationInput').value.trim();
      const aiResponseEl = document.getElementById('aiResponse');

      if (!crop || !location) {
        aiResponseEl.innerHTML = '<span class="error">Please enter both crop and location.</span>';
        return;
      }

      aiResponseEl.innerHTML = 'Generating recommendation...';

      // Placeholder response (since API key and endpoint are invalid)
      try {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Mock response based on crop and location
        const mockResponses = {
          'wheat-rajasthan': 'For growing <strong>wheat</strong> in <strong>Rajasthan</strong>, use <strong>drip irrigation</strong> to conserve water. Irrigate <strong>every 7-10 days</strong>, delivering 30-40 mm water. Monitor <strong>soil moisture</strong> to avoid overwatering. Apply <strong>nitrogen fertilizers</strong> during tillering. Watch for <strong>pests</strong> like aphids.',
          'rice-punjab': 'For <strong>rice</strong> in <strong>Punjab</strong>, maintain <strong>flood irrigation</strong> with 5-10 cm water depth. Irrigate <strong>every 2-3 days</strong>. Use <strong>sensors</strong> to check water levels. Apply <strong>phosphorus</strong> at transplanting. Control <strong>weeds</strong> early to maximize yield.',
          default: `For <strong>${crop}</strong> in <strong>${location}</strong>, ensure <strong>regular irrigation</strong> based on soil type. Use <strong>sensors</strong> to monitor moisture. Apply <strong>balanced fertilizers</strong> and check for <strong>local pests</strong>. Consult <strong>local experts</strong> for specific advice.`
        };

        const key = `${crop.toLowerCase()}-${location.toLowerCase()}`;
        const responseText = mockResponses[key] || mockResponses.default;
        aiResponseEl.innerHTML = responseText;
      } catch (error) {
        console.error("Mock AI Error:", error);
        aiResponseEl.innerHTML = '<span class="error">Failed to get recommendation. Try again later.</span>';
      }
    }
  </script>
</body>
</html>