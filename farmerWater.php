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
        <li class="menu-item"><a href="farmer.php" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
        <li class="menu-item"><a href="farmerProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
        <li class="menu-item"><a href="farmerIrrigation.php" class="sidebar-link"><i class="fas fa-tint icon-green"></i> Irrigation</a></li>
        <li class="menu-item"><a href="#" class="sidebar-link"><i class="fas fa-chart-bar icon-green"></i> Water Usage</a></li>
        <li class="menu-item"><a href="index.php" class="sidebar-link"><i class="fas fa-arrow-left icon-green"></i> Back to Home</a></li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <a href="auth/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt icon-green"></i> Logout</a>
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
          <button id="exportBtn" class="export-btn"><i class="fas fa-download"></i> Export Data</button>
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
            <tbody id="zoneTableBody">
              <!-- Data will be populated via JS -->
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
  <iframe id="chatbot-frame" src="chatbot/chatbot.html"></iframe>

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
      // Initialize Chart instance
      const ctx = document.getElementById('usageChart').getContext('2d');
      let usageChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [
            {
              label: 'Actual Usage',
              data: [],
              borderColor: '#4ade80',
              backgroundColor: 'rgba(74, 222, 128, 0.1)',
              tension: 0.4,
              fill: true
            },
            {
              label: 'Target Usage',
              data: [],
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
              grid: { color: 'rgba(55, 65, 81, 0.3)' },
              ticks: { color: '#cbd5e1' },
              title: { display: true, text: 'Water Usage (Liters)', color: '#cbd5e1' }
            },
            x: {
              grid: { color: 'rgba(55, 65, 81, 0.3)' },
              ticks: { color: '#cbd5e1' },
              title: { display: true, text: 'Date', color: '#cbd5e1' }
            }
          },
          plugins: {
            legend: { labels: { color: '#cbd5e1' } }
          }
        }
      });

      // Function to fetch data
      function fetchData(period = 'month') {
        fetch(`getWaterData.php?period=${period}`)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              console.error(data.error);
              return;
            }

            // Update Chart
            usageChart.data.labels = data.chart.labels;
            usageChart.data.datasets[0].data = data.chart.usage;
            usageChart.data.datasets[1].data = data.chart.target;
            usageChart.update();

            // Update Summary (Only needs to happen once or if api returns different summaries per period)
            if (document.querySelector('.summary-value')) {
                const summaryCards = document.querySelectorAll('.summary-value');
                if(summaryCards.length >= 4 && data.summary) {
                    summaryCards[0].textContent = data.summary.total;
                    summaryCards[1].textContent = data.summary.daily_average;
                    summaryCards[2].textContent = data.summary.comparison;
                    summaryCards[3].textContent = data.summary.efficiency;
                }
            }

            // Update Zone Table
            const tableBody = document.getElementById('zoneTableBody');
            tableBody.innerHTML = '';
            
            if (data.zones) {
                data.zones.forEach(zone => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="padding: 1rem; border-bottom: 1px solid rgba(74, 222, 128, 0.2);">${zone.zone}</td>
                        <td style="padding: 1rem; border-bottom: 1px solid rgba(74, 222, 128, 0.2);">${zone.crop}</td>
                        <td style="padding: 1rem; border-bottom: 1px solid rgba(74, 222, 128, 0.2);">${zone.usage}</td>
                        <td style="padding: 1rem; border-bottom: 1px solid rgba(74, 222, 128, 0.2);">${zone.target}</td>
                        <td style="padding: 1rem; border-bottom: 1px solid rgba(74, 222, 128, 0.2); color: #4ade80;">${zone.efficiency}</td>
                    `;
                    tableBody.appendChild(row);
                });
            }
          })
          .catch(error => console.error('Error fetching data:', error));
      }

      // Initial Load
      fetchData('month');

      // Time Filter Buttons
      const timeBtns = document.querySelectorAll('.time-btn');
      timeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          // Remove active class from all
          timeBtns.forEach(b => b.classList.remove('active'));
          // Add active class to clicked
          btn.classList.add('active');
          
          // Fetch new data
          const period = btn.getAttribute('data-period');
          fetchData(period);
        });
      });

      // Export Button Logic
      const exportBtn = document.getElementById('exportBtn');
      if(exportBtn) {
        exportBtn.addEventListener('click', () => {
            const activeBtn = document.querySelector('.time-btn.active');
            const period = activeBtn ? activeBtn.getAttribute('data-period') : 'month';
            window.location.href = `download_report.php?range=${period}`;
        });
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


  </script>
</body>
</html>