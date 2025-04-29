<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'service') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "irrigation";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
            top: 1.5rem;
            left: 1.5rem;
            z-index: 50;
            padding: 0.75rem;
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

        .hamburger-toggle {
            overflow: hidden;
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
            margin-left: 1rem;
        }

        .sidebar-container.open {
            transform: translateX(0);
        }

        .sidebar-brand {
            padding: 3.5rem 2rem 2rem;
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
            padding: 1.5rem;
        }

        .sidebar-nav-item {
            margin-bottom: 1rem;
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
            padding: 1.5rem;
            border-top: 1px solid #1e293b;
        }

        /* Header */
        .dashboard-header {
            background-color: #111827;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .user-greeting {
            margin-left: 6rem;
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
            gap: 2rem;
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
            font-size: 1.6rem;
            color: #4ade80;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 2rem;
            flex: 1;
            overflow-y: auto;
        }

        .dashboard-content::-webkit-scrollbar {
            display: none;
        }

        /* Widget Grid */
        .widget-grid {
            display: flex;
            flex-direction: row;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .widget-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            flex: 1;
        }

        /* Widget Cards */
        .widget-card {
            background: rgba(31, 41, 55, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(74, 222, 128, 0.3);
            padding: 2rem;
            border-radius: 0.75rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }

        .widget-table{
            background: rgba(31, 41, 55, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(74, 222, 128, 0.3);
            padding: 2rem;
            border-radius: 0.75rem;
            margin-bottom: 5rem;
        }

        .widget-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 24px rgba(74, 222, 128, 0.4);
        }

        .widget-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #4ade80;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .widget-title i {
            margin-right: 0.75rem;
        }

        .widget-value {
            font-size: 1.875rem;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }

        .widget-action {
            margin-top: 1.5rem;
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

        /* Table Styling */
        .service-table {
            width: 100%;
            color: #cbd5e1;
            margin-top: 1.5rem;
            border-collapse: separate;
            border-spacing: 0;
            background: rgba(17, 24, 39, 0.5);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .service-table th,
        .service-table td {
            padding: 1rem;
            text-align: left;
        }

        .service-table th {
            background: rgba(74, 222, 128, 0.2);
            color: #4ade80;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .service-table tr:nth-child(even) {
            background: rgba(31, 41, 55, 0.3);
        }

        .service-table td {
            border-bottom: 1px solid rgba(74, 222, 128, 0.2);
        }

        .service-table .table-action {
            padding: 0.5rem 1rem;
            background: rgba(74, 222, 128, 0.2);
            border-radius: 0.25rem;
            color: #4ade80;
            font-weight: 500;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .service-table .table-action:hover {
            background: #4ade80;
            color: #111827;
        }

        /* Recent Activities List */
        .recent-activities {
            list-style-type: none;
            padding: 0;
            color: #cbd5e1;
        }

        .recent-activities li {
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .recent-activities strong {
            color: #4ade80;
        }

        .recent-activities em {
            color: #94a3b8;
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
                margin-left: 0; /* Initial state */
            }

            .sidebar-container.open + .dashboard-main {
                margin-left: 16rem; /* Shift content when sidebar is open */
            }

            @media (max-width: 1024px) {
                .sidebar-container.open + .dashboard-main {
                    margin-left: 0; /* Sidebar overlays content on smaller screens */
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
                <li class="menu-item"><a href="#" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
                <li class="menu-item"><a href="serviceProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
                <li class="menu-item"><a href="index.php" class="sidebar-link"><i class="fas fa-arrow-left icon-green"></i> Back to Home</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt icon-green"></i> Logout</a>
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
      <iframe id="chatbot-frame" src="chatbot.html"></iframe>
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
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
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