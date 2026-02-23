<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'manufacturer') {
    header("Location: ../auth/login.php");
    exit();
}



require_once __DIR__ . '/../config/db.php';
$conn = Database::getInstance()->getConnection();

$query = "SELECT * FROM active_device WHERE id = 1"; 
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    $activeDevices = $row['active_devices'];
    $pendingRequests = $row['pending_requests'];
    $revenue = $row['revenue'];
    $deviceUsage = $row['device_usage'];
} else {
    $activeDevices = 0;
    $pendingRequests = 0;
    $revenue = 0;
    $deviceUsage = 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/hamburger.css">
  <link rel="stylesheet" href="../css/manufacturer.css">
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
                <li class="menu-item"><a href="manufacturer.php" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
                <li class="menu-item"><a href="manuProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
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
                <p class="user-name">Welcome, Manufacturer</p>
            </div>
            <div class="header-controls">
                <span class="current-date"><?php echo date("F j, Y"); ?></span>
                <div class="user-avatar">
                    <a href="manuProfile.php" style="color: #4ade80; text-decoration: none;">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Widget Grid -->
            <div class="widget-grid">
                <!-- Column with Active Devices and Pending Requests -->
                <div class="widget-column">
                    <!-- Active Devices -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-cogs"></i> Active Devices</h2>
                        <p class="widget-value"><?php echo $activeDevices; ?> Devices</p>
                        <button class="widget-action">View All</button>
                    </div>
                    <!-- Pending Requests -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-tools"></i> Pending Requests</h2>
                        <p class="widget-value"><?php echo $pendingRequests; ?> Pending</p>
                        <button class="widget-action">Manage Requests</button>
                    </div>
                </div>
                <!-- Column with Revenue Summary and Device Usage -->
                <div class="widget-column">
                    <!-- Revenue Summary -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-dollar-sign"></i> Revenue Summary</h2>
                        <p class="widget-value"><?php echo "$" . number_format($revenue, 2); ?></p>
                        <button class="widget-action">View Details</button>
                    </div>
                    <!-- Device Usage -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-chart-line"></i> Device Usage</h2>
                        <p class="widget-value"><?php echo $deviceUsage; ?>% Usage</p>
                        <button class="widget-action">View All</button>
                    </div>
                </div>
            </div>

            <!-- Service Requests Table -->
            <div class="widget-table">
                <h2 class="widget-title"><i class="fas fa-tools"></i> Pending Service Requests</h2>
                <div style="overflow-x: auto;">
                    <table class="service-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Farmer</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>001</td>
                                <td>Ravi Singh</td>
                                <td>Punjab</td>
                                <td><span class="text-yellow-500">Pending</span></td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Priya Sharma</td>
                                <td>Uttarakhand</td>
                                <td><span class="text-green-500">Completed</span></td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Rajesh Kumar</td>
                                <td>Rajasthan</td>
                                <td><span class="text-yellow-500">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Chatbot -->
    <div id="chatbot-toggle">
        <i class="fas fa-comment-dots"></i>
    </div>
    <div id="chatbot-overlay"></div>
    <iframe id="chatbot-frame" src="../chatbot/chatbot.html"></iframe>

    <script>
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

        // Chatbot Toggle
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