<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Water Usage Dashboard - SmartFarm</title>
  <!-- Chart.js for visualizations -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
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
    .icon-padding {
      padding-right: 10px;
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

    .breadcrumb {
      color: #cbd5e1;
      font-size: 0.9rem;
    }

    .breadcrumb a {
      color: #4ade80;
      text-decoration: none;
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

    .hamburger-toggle span {
      display: block;
      width: 24px;
      height: 3px;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 2px;
      margin-bottom: 5px;
    }

    .hamburger-toggle span:last-child {
      margin-bottom: 0;
    }

    .dashboard-content {
      padding: 1.5rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .panel {
      background: rgba(31, 41, 55, 0.85);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(74, 222, 128, 0.3);
      padding: 1.5rem;
      border-radius: 0.75rem;
      margin-bottom: 1.5rem;
    }

    .panel-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #4ade80;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
    }

    .panel-title i {
      margin-right: 0.5rem;
    }

    .summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .summary-card {
      background: rgba(31, 41, 55, 0.5);
      border: 1px solid rgba(74, 222, 128, 0.2);
      padding: 1rem;
      border-radius: 0.5rem;
      text-align: center;
    }

    .summary-label {
      font-size: 0.9rem;
      color: #cbd5e1;
      margin-bottom: 0.5rem;
    }

    .summary-value {
      font-size: 1.5rem;
      font-weight: bold;
      color: #ffffff;
    }

    .saving {
      color: #4ade80;
    }

    .warning {
      color: #fcd34d;
    }

    .time-control {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .time-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .time-btn {
      padding: 0.5rem 1rem;
      background: transparent;
      border: 1px solid #4ade80;
      color: #cbd5e1;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .time-btn.active {
      background-color: #4ade80;
      color: #0B1120;
    }

    .export-btn {
      padding: 0.5rem 1rem;
      background-color: #4ade80;
      color: #0B1120;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .export-btn:hover {
      background-color: #22c55e;
    }

    .chart-container {
      position: relative;
      height: 300px;
      margin-bottom: 1.5rem;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    .usage-table {
      width: 100%;
      border-collapse: collapse;
      color: #cbd5e1;
    }

    .usage-table th,
    .usage-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #374151;
    }

    .usage-table th {
      font-weight: 600;
      color: #4ade80;
    }

    .tips-section {
      padding: 1rem;
      background: rgba(74, 222, 128, 0.1);
      border-left: 4px solid #4ade80;
      border-radius: 0.25rem;
      margin-top: 1.5rem;
    }

    .tips-title {
      font-weight: 600;
      color: #4ade80;
      margin-bottom: 0.5rem;
    }

    .tips-list {
      padding-left: 1.5rem;
      color: #cbd5e1;
    }

    .tips-list li {
      margin-bottom: 0.5rem;
    }

    .footer {
      background-color: #1a2332;
      color: white;
      padding: 20px 15px;
      text-align: center;
      margin-top: 2rem;
    }

    .footer-bottom {
      font-size: 13px;
      color: #94a3b8;
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

.hamburger-toggle span {
  transition: all 0.3s ease;
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
  <!-- Hamburger Button -->x
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
        <li class="menu-item"><a href="farmer.php" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
        <li class="menu-item"><a href="farmerProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
        <li class="menu-item"><a href="farmerIrrigation.php" class="sidebar-link"><i class="fas fa-tint icon-green"></i> Irrigation</a></li>
        <li class="menu-item"><a href="#" class="sidebar-link"><i class="fas fa-chart-bar icon-green"></i> Water Usage</a></li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <a href="#" class="sidebar-link"><i class="fas fa-sign-out-alt icon-green"></i> Logout</a>
    </div>
  </aside>

  <!-- Header -->
  <div class="dashboard-main">
    <header class="dashboard-header">
      <div class="user-greeting">
        <h1 class="dashboard-title">Water Usage</h1>
        <p class="breadcrumb"><a href="dboard.html">Dashboard</a> / Water Usage</p>
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
      <!-- Summary Cards -->
      <div class="summary-grid">
        <div class="summary-card">
          <p class="summary-label">Total Usage This Month</p>
          <p class="summary-value">3,450 L</p>
        </div>
        <div class="summary-card">
          <p class="summary-label">Daily Average</p>
          <p class="summary-value">115 L</p>
        </div>
        <div class="summary-card">
          <p class="summary-label">Compared to Last Month</p>
          <p class="summary-value saving">-12%</p>
        </div>
        <div class="summary-card">
          <p class="summary-label">Water Efficiency</p>
          <p class="summary-value">85%</p>
        </div>
      </div>

      <!-- Usage Chart -->
      <div class="panel">
        <h2 class="panel-title"><i class="fas fa-chart-line icon-padding"></i> Water Consumption Trends</h2>
        <div class="time-control">
          <div class="time-buttons">
            <button class="time-btn" data-period="week">Week</button>
            <button class="time-btn active" data-period="month">Month</button>
            <button class="time-btn" data-period="year">Year</button>
          </div>
          <button class="export-btn"><i class="fas fa-download"></i> Export Data</button>
        </div>
        <div class="chart-container">
          <canvas id="usageChart"></canvas>
        </div>
      </div>

      <!-- Zone Usage -->
      <div class="panel">
        <h2 class="panel-title"><i class="fas fa-map-marker-alt icon-padding"></i> Zone Usage Breakdown</h2>
        <div class="table-wrapper">
          <table class="usage-table">
            <thead>
              <tr>
                <th>Zone</th>
                <th>Crop</th>
                <th>Usage (L)</th>
                <th>Target (L)</th>
                <th>Efficiency</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>North Field</td>
                <td>Wheat</td>
                <td>1,250</td>
                <td>1,400</td>
                <td>89%</td>
              </tr>
              <tr>
                <td>South Field</td>
                <td>Rice</td>
                <td>1,800</td>
                <td>1,600</td>
                <td class="warning">112%</td>
              </tr>
              <tr>
                <td>East Garden</td>
                <td>Corn</td>
                <td>400</td>
                <td>500</td>
                <td>80%</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="tips-section">
          <h3 class="tips-title">Water Saving Tips</h3>
          <ul class="tips-list">
            <li>Consider adjusting irrigation schedule for South Field to reduce water usage</li>
            <li>Install soil moisture sensors in East Garden to optimize irrigation timing</li>
            <li>Check for leaks in the irrigation system to improve overall efficiency</li>
          </ul>
        </div>
      </div>
    </div>

    <footer class="footer">
      <div class="footer-bottom">
        <p>&copy; 2025 Smart Irrigation System. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <!-- Floating Chatbot Button -->
  <div id="chatbot-toggle">
    <i class="fas fa-comment-dots"></i>
  </div>

  <!-- Background Overlay Blur -->
  <div id="chatbot-overlay"></div>

  <!-- Chatbot iframe popup -->
  <iframe id="chatbot-frame" src="chatbot.html"></iframe>

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

    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Chart
      const ctx = document.getElementById('usageChart').getContext('2d');
      const usageChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['1', '5', '10', '15', '20', '25', '30'],
          datasets: [
            {
              label: 'Actual Usage',
              data: [120, 145, 110, 125, 130, 115, 105],
              borderColor: '#4ade80',
              backgroundColor: 'rgba(74, 222, 128, 0.1)',
              tension: 0.4,
              fill: true
            },
            {
              label: 'Target Usage',
              data: [130, 130, 130, 130, 130, 130, 130],
              borderColor: '#94a3b8',
              borderDash: [5, 5],
              pointRadius: 0,
              tension: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(55, 65, 81, 0.3)'
              },
              ticks: {
                color: '#cbd5e1'
              },
              title: {
                display: true,
                text: 'Water Usage (Liters)',
                color: '#cbd5e1'
              }
            },
            x: {
              grid: {
                color: 'rgba(55, 65, 81, 0.3)'
              },
              ticks: {
                color: '#cbd5e1'
              },
              title: {
                display: true,
                text: 'Day of Month',
                color: '#cbd5e1'
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

      // Time period buttons
      const timeButtons = document.querySelectorAll('.time-btn');
      timeButtons.forEach(button => {
        button.addEventListener('click', () => {
          // Remove active class from all buttons
          timeButtons.forEach(b => b.classList.remove('active'));
          // Add active class to clicked button
          button.classList.add('active');
          
          // Update chart based on selected period
          const period = button.dataset.period;
          updateChart(usageChart, period);
        });
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

      // Export button
      document.querySelector('.export-btn').addEventListener('click', () => {
        alert('Water usage data would be exported here');
      });
    });

    // Function to update chart data based on time period
    function updateChart(chart, period) {
      let labels, data, targetData;
      
      switch(period) {
        case 'week':
          labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          data = [110, 130, 120, 140, 100, 90, 105];
          targetData = [120, 120, 120, 120, 120, 120, 120];
          break;
        case 'month':
          labels = ['1', '5', '10', '15', '20', '25', '30'];
          data = [120, 145, 110, 125, 130, 115, 105];
          targetData = [130, 130, 130, 130, 130, 130, 130];
          break;
        case 'year':
          labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          data = [3200, 2800, 3000, 3450, 4200, 4800, 5100, 4800, 4200, 3800, 3500, 3200];
          targetData = [3500, 3500, 3500, 3500, 4000, 5000, 5000, 5000, 4000, 3500, 3500, 3500];
          break;
      }
      
      chart.data.labels = labels;
      chart.data.datasets[0].data = data;
      chart.data.datasets[1].data = targetData;
      chart.update();
    }
  </script>
</body>
</html>