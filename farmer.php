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
</head>
<style>
    html {
  scroll-behavior: smooth;
  }


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

  /* Header */
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

  .crop-dropdown {
    padding: 0.5rem;
    background-color: #1e293b;
    border: 1px solid #4ade80;
    color: #ffffff;
    border-radius: 0.5rem;
    font-size: 0.875rem;
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

  /* Widget Cards */
  .widget-card {
    background: rgba(31, 41, 55, 0.85);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(74, 222, 128, 0.3);
    padding: 1.5rem;
    border-radius: 0.75rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 1.5rem;
  }

  .widget-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 24px rgba(74, 222, 128, 0.4);
  }

  .widget-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #4ade80;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
  }

  .widget-title i {
    margin-right: 0.5rem;
  }

  .widget-value {
    font-size: 1.875rem;
    font-weight: bold;
    color: #ffffff;
  }

  .widget-action {
    margin-top: 1rem;
    color: #4ade80;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.3s ease;
  }

  .widget-action:hover {
    color: #22c55e;
  }

  #location{
    font-size: 1.2rem; 
    font-weight: bold;  
    color: #d7d7d7; 
    margin-top: 0.5rem;
    transition: color 0.3s ease;
  }

  #weather {
    font-size: 1.875rem; 
    font-weight: bold;  
    color: #ffffff; 
    margin-top: 0.5rem;
  }

  /* Irrigation Control */
  .irrigation-panel {
    background: rgba(31, 41, 55, 0.85);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(74, 222, 128, 0.3);
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
  }

  .irrigation-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #4ade80;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
  }

  .irrigation-title i {
    margin-right: 0.5rem;
  }

  .device-info {
    color: #cbd5e1;
    margin-bottom: 1rem;
  }

  .device-info p {
    margin-bottom: 0.25rem;
  }

  .device-status {
    color: #4ade80;
    font-weight: 500;
  }

  .irrigation-buttons {
    display: flex;
    gap: 1rem;
  }

  .irrigation-btn {
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    background-color: #4ade80;
    color: #0B1120;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .irrigation-btn:hover {
    background-color: #22c55e;
  }

  .irrigation-btn.schedule {
    background: transparent;
    border: 1px solid #4ade80;
    color: #4ade80;
  }

  .irrigation-btn.schedule:hover {
    background-color: #4ade80;
    color: #0B1120;
  }

  .irrigation-btn.irrigating {
    background-color: #ef4444;
    color: #ffffff;
  }

  .irrigation-btn.irrigating:hover {
    background-color: #dc2626;
  }

  #farm-map {
    height: 16rem;
    border-radius: 0.5rem;
  }

  /* Water Usage Tracking */
  .usage-panel {
    background: rgba(31, 41, 55, 0.85);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(74, 222, 128, 0.3);
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
  }

  .usage-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #4ade80;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
  }

  .usage-title i {
    margin-right: 0.5rem;
  }

  .usage-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .time-range {
    padding: 0.5rem;
    background-color: #1e293b;
    border: 1px solid #4ade80;
    color: #ffffff;
    border-radius: 0.5rem;
  }

  .download-report {
    padding: 0.5rem 1rem;
    background-color: #4ade80;
    color: #0B1120;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .download-report:hover {
    background-color: #22c55e;
  }

  /* Service Finder */
  .service-panel {
    background: rgba(31, 41, 55, 0.85);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(74, 222, 128, 0.3);
    padding: 1.5rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
  }

  .service-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #4ade80;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
  }

  .service-title i {
    margin-right: 0.5rem;
  }

  .service-search {
    display: flex;
    margin-bottom: 1rem;
  }

  .service-input {
    flex: 1;
    padding: 0.75rem;
    background-color: #1e293b;
    border: 1px solid #4ade80;
    color: #ffffff;
    border-radius: 0.5rem 0 0 0.5rem;
    outline: none;
  }

  .service-input:focus {
    box-shadow: 0 0 0 2px #4ade80;
  }

  .search-btn {
    padding: 0.75rem 1.5rem;
    background-color: #4ade80;
    color: #0B1120;
    border: none;
    border-radius: 0 0.5rem 0.5rem 0;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .search-btn:hover {
    background-color: #22c55e;
  }

  .service-table {
    width: 100%;
    border-collapse: collapse;
    color: #cbd5e1;
  }

  .service-table th,
  .service-table td {
    padding: 0.75rem;
    text-align: left;
  }

  .service-table thead {
    background-color: #1e293b;
  }

  .service-table th {
    font-weight: 600;
    color: #4ade80;
  }

  .service-table tbody tr:hover {
    background-color: #1e293b;
  }

  .contact-btn {
    color: #4ade80;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.3s ease;
  }

  .contact-btn:hover {
    color: #22c55e;
  }

  /* Crop Insight */
.insight-panel {
  background: rgba(31, 41, 55, 0.85);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(74, 222, 128, 0.3);
  padding: 2rem;
  border-radius: 0.75rem;
  max-width: 48rem; /* Increased from 32rem to 48rem */
  margin: 1.5rem auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 90%; /* Ensures it uses most of the viewport */
}

/* Insight Form */
.insight-form {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  width: 100%;
}

@media (min-width: 768px) {
  .insight-form {
    flex-direction: row;
    gap: 1.5rem;
  }
}

.insight-input {
  padding: 1rem;
  background-color: #1e293b;
  border: 1px solid #4ade80;
  color: #ffffff;
  border-radius: 0.5rem;
  outline: none;
  flex: 1;
  width: 100%;
  font-size: 1rem;
}

.insight-input::placeholder {
  color: #cccccc;
}

/* Insight Button */
.insight-btn {
  padding: 1rem 2rem;
  background-color: #4ade80;
  color: #0B1120;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  width: 100%;
  text-align: center;
  margin-top: 1.5rem;
  font-size: 1rem;
}

@media (min-width: 768px) {
  .insight-btn {
    width: auto;
  }
}

  .insight-btn:hover {
    transform: scale(1.05);
    background-color: #1fc45c;
  }
  
  /* Insight Response */
  .insight-response {
    margin-top: 1.5rem;
    color: #cbd5e1;
    font-size: 1rem;
    white-space: pre-wrap;
  }
  
  .insight-response .error {
    color: #ef4444;
  }
  
  /* Responsive Grid for Widgets */
  .widget-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }
  
  @media (min-width: 640px) {
    .widget-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (min-width: 1024px) {
    .widget-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  /* Irrigation Panel Grid */
  .irrigation-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  @media (min-width: 1024px) {
    .irrigation-grid {
      grid-template-columns: 1fr 1fr;
    }
  }
  
  .icon-padding {
    padding-right: 6px; 
  }
  .footer {
  background-color: #1a2332;
  color: white;
  padding: 20px 15px;
  text-align: center;
}

.footer-container {
  max-width: 1100px;
  margin: 0 auto;
}

.footer-row {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.footer-row div {
  flex: 1;
  min-width: 160px;
}

.footer h3 {
  color: #22c55e;
  font-size: 16px;
  margin-bottom: 8px;
}

.footer p,
.footer-links ul li a {
  color: #94a3b8;
  font-size: 14px;
}

.footer-links ul {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
}

.footer-links ul li a {
  text-decoration: none;
  transition: color 0.3s ease-in-out;
}

.footer-links ul li a:hover {
  color: #22c55e;
}

.social-icons {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.social-icons a {
  color: #d1d5db;
  font-size: 18px;
  transition: color 0.3s ease-in-out;
}

.social-icons a:hover {
  color: #22c55e;
}

.footer-bottom {
  border-top: 1px solid #374151;
  padding-top: 10px;
  margin-top: 10px;
  font-size: 13px;
  color: #94a3b8;
}

.footer-bottom p {
  margin: 0;
}

@media (max-width: 768px) {
  .footer-row {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 24px;
  }

  .footer-links ul {
    align-items: center;
  }
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

</style>
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
        <h1 class="dashboard-title">Dashboard</h1>
        <p class="user-name">Welcome, Farmer<?php //echo htmlspecialchars($username); ?></p>
        <!-- <p class="user-role">(Farmer)</p> -->
      </div>
      <div class="header-controls">
        <select id="cropSelector" class="crop-dropdown">
          <option value="wheat">Wheat</option>
          <option value="rice">Rice</option>
          <option value="corn">Corn</option>
        </select>
        <span class="current-date"><?php echo date("F j, Y"); ?></span>
        <div class="user-avatar">
            <a href="profile.php" style="color: #4ade80; text-decoration: none;">
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
          <p class="widget-value">4 Online</p>
          <button id="manageDevices" class="widget-action">Manage Devices</button>
        </div>
        <div class="widget-card">
          <h2 class="widget-title"><i class="fas fa-tint icon-padding"></i> Water Usage</h2>
          <p class="widget-value">150 Liters</p>
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
          <button class="download-report">Download Report</button>
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
  <iframe id="chatbot-frame" src="chatbot.html"></iframe>

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
    const apiKey = "846965112d61ecee5abdc3f8255c3893"; 
    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`
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
      if (window.innerWidth < 768 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
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
    document.getElementById('cropSelector').addEventListener('change', (e) => {
      alert(`Selected crop: ${e.target.value}`);
    });

    // Farm Plot Map
    // Initialize the map centered on Phagwara, Punjab
    const map = L.map('farm-map').setView([31.2245, 75.7739], 12);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
    }).addTo(map);

    // Water Usage Chart

    const usageChart = new Chart(document.getElementById('usageChart'), {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Water Usage (Liters)',
          data: [120, 150, 130, 170, 140, 160, 145],
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
            grid: {
              color: '#374151'  
            }
          },
          x: {
            ticks: { color: '#cbd5e1' },
            grid: {
              color: '#374151'  
            }
          }
        },
        plugins: {
          legend: {
            labels: {
              color: '#cbd5e1'
            }
          }
        }
      }
    });

    // Time Range Filter
    document.getElementById('timeRange').addEventListener('change', (e) => {
      const range = e.target.value;
      let newData, newLabels;
      if (range === 'week') {
        newData = [120, 150, 130, 170, 140, 160, 145];
        newLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
      } else if (range === 'month') {
        newData = [500, 600, 550, 620];
        newLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
      } else {
        newData = [5000, 5200, 5100, 5300];
        newLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
      }
      usageChart.data.datasets[0].data = newData;
      usageChart.data.labels = newLabels;
      usageChart.update();
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