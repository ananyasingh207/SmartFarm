<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturer Dashboard</title>
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
            margin-left: 2rem;
        }

        .dashboard-title {
            padding-left: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #4ade80;
        }

        .user-name {
            padding-left: 0.5rem;
            color: #cbd5e1;
            font-size: 1rem;
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
            margin: 0 auto;
        }

        .dashboard-content::-webkit-scrollbar {
            display: none;
        }

        /* Main Content Shift with Sidebar */
        .dashboard-main {
            transition: margin-left 0.4s ease-in-out;
        }

        .sidebar-container.open + .dashboard-main {
            margin-left: 16rem;
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
            margin: 0.6rem;
            background: rgba(31, 41, 55, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(74, 222, 128, 0.3);
            padding: 2rem;
            border-radius: 0.75rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .widget-table {
            background: rgba(31, 41, 55, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(74, 222, 128, 0.3);
            padding: 2rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
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
            border: 2px solid #4ade80;
            border-radius: 50%;
            padding: 0.25rem;
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
            border-bottom: 1px solid rgba(74, 222, 128, 0.2);
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

        /* Chatbot Toggle */
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

        /* Chatbot Overlay */
        #chatbot-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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

        /* Chatbot Frame */
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
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
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

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar-container.open + .dashboard-main {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .widget-grid {
                flex-direction: column;
            }

            .widget-column {
                width: 100%;
            }

            .widget-card {
                margin-left: 0;
                margin-right: 0;
            }

            .widget-table {
                margin-left: 0;
                margin-right: 0;
                overflow-x: auto;
            }

            .service-table {
                min-width: 600px; /* Ensures table scrolls horizontally */
            }

            #chatbot-frame {
                width: 90%;
                height: 80vh;
                bottom: 80px;
                right: 5%;
            }
        }

        @media (max-width: 480px) {
            .dashboard-content {
                padding: 1rem;
            }

            .dashboard-header {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .user-greeting {
                margin-left: 0;
            }

            .header-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .widget-title {
                font-size: 1rem;
            }

            .widget-value {
                font-size: 1.5rem;
            }
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
                <li class="menu-item"><a href="#" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
                <li class="menu-item"><a href="manuProfile.php" class="sidebar-link"><i class="fas fa-user icon-green"></i> Profile</a></li>
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
                        <p class="widget-value">15 Devices</p>
                        <button class="widget-action">View All</button>
                    </div>
                    <!-- Pending Requests -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-tools"></i> Pending Requests</h2>
                        <p class="widget-value">5 Pending</p>
                        <button class="widget-action">Manage Requests</button>
                    </div>
                </div>
                <!-- Column with Revenue Summary and Device Usage -->
                <div class="widget-column">
                    <!-- Revenue Summary -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-dollar-sign"></i> Revenue Summary</h2>
                        <p class="widget-value">$10,500</p>
                        <button class="widget-action">View Details</button>
                    </div>
                    <!-- Device Usage -->
                    <div class="widget-card">
                        <h2 class="widget-title"><i class="fas fa-chart-line"></i> Device Usage</h2>
                        <p class="widget-value">80% Usage</p>
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
    <iframe id="chatbot-frame" src="chatbot.html"></iframe>

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
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
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