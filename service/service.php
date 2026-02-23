<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'service') {
    header("Location: ../auth/login.php");
    exit();
}



require_once __DIR__ . '/../config/db.php';
$conn = Database::getInstance()->getConnection();

$query = "SELECT * FROM active_service WHERE id = 1";  
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    $inProgress = $row['in_progress'];
    $newRequests = $row['new_requests'];
    $pendingInvoices = $row['pending_invoices'];
} else {
    $inProgress = 0;
    $newRequests = 0;
    $pendingInvoices = 0;
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/hamburger.css">
  <link rel="stylesheet" href="../css/service.css">
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
                <li class="menu-item"><a href="service.php" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
                <li class="menu-item"><a href="serviceProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
                <li class="menu-item"><a href="../index.php" class="sidebar-link"><i class="fas fa-arrow-left icon-green"></i> Back to Home</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt icon-green"></i> Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="user-greeting">
                <h1 class="dashboard-title">Dashboard</h1>
                <p class="user-name">Welcome, Service Provider</p>
            </div>
            <div class="header-controls">
                <span class="current-date"><?php echo date("F j, Y"); ?></span>
                <div class="user-avatar">
                    <a href="serviceProfile.php" style="color: #4ade80; text-decoration: none;">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Overview Cards -->
            <div class="widget-grid">
                <!-- Column with Active Services and Service Requests -->
                <div class="widget-column">
                    <!-- Active Services -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-cogs icon-padding"></i> Active Services</h2>
                        <p class="widget-value"><?php echo $inProgress; ?> In Progress</p>
                        <button class="widget-action">View All</button>
                    </div>
                    <!-- Service Requests -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-tools icon-padding"></i> New Service Requests</h2>
                        <p class="widget-value"><?php echo $newRequests; ?> Pending</p>
                        <button class="widget-action">Manage Requests</button>
                    </div>
                </div>
                <!-- Column with Pending Invoices and Recent Activities -->
                <div class="widget-column">
                    <!-- Pending Invoices -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-file-invoice-dollar icon-padding"></i> Pending Invoices</h2>
                        <p class="widget-value"><?php echo $pendingInvoices; ?> Unpaid</p>
                        <button class="widget-action">View Invoices</button>
                    </div>
                    <!-- Recent Activities -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-history icon-padding"></i> Recent Activities</h2>
                        <ul class="recent-activities">
                            <li>Service for <strong>Ravi Singh</strong> completed on <em>April 10</em>.</li>
                            <li>New request from <strong>Priya Sharma</strong> for irrigation setup.</li>
                            <li>Invoice for <strong>Amit Verma</strong> pending.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service Request Table -->
            <div class="widget-table">
                <h2 class="widget-title"><i class="fas fa-map-marker-alt icon-padding"></i> Nearby Farmer Requests</h2>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Farmer</th>
                            <th>Location</th>
                            <th>Service Needed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Amit Verma</td>
                            <td>Pune, MH</td>
                            <td>Pump Repair</td>
                            <td><button class="table-action">View</button></td>
                        </tr>
                        <tr>
                            <td>Ravi Singh</td>
                            <td>Indore, MP</td>
                            <td>Irrigation Setup</td>
                            <td><button class="table-action">View</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="chatbot-toggle">
        <i class="fas fa-comment-dots"></i>
      </div>
  
      <!-- Background Overlay Blur -->
      <div id="chatbot-overlay"></div>
  
      <!-- Chatbot iframe popup -->
      <iframe id="chatbot-frame" src="../chatbot/chatbot.html"></iframe>
    <script>
        // Hamburger Menu Toggle
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const dashboardMain = document.querySelector('.dashboard-main');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            hamburger.classList.toggle('active');
            dashboardMain.offsetHeight; // Force reflow to ensure transition
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
                sidebar.classList.remove('open');
                hamburger.classList.remove('active');
            }
        });

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
    </script>
</body>
</html>