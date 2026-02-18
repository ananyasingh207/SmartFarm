<?php
session_start();
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : 'user@example.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - Smart Irrigation Dashboard</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for Icons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/hamburger.css">
  <link rel="stylesheet" href="css/farmer.css">
</head>

<body class="min-h-screen">

  <!-- Hamburger Button -->
  <div id="hamburger" class="hamburger-toggle">
      <span></span>
      <span></span>
      <span></span>
  </div>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar-container">
    <div class="sidebar-brand">
      <span class="sidebar-title">SmartFarm</span>
    </div>
    <nav class="sidebar-nav">
      <ul type="none">
        <li class="menu-item"><a href="farmer.php" class="sidebar-link"><i class="fas fa-home icon-green"></i> Dashboard</a></li>
        <li class="menu-item"><a href="farmerProfile.php" class="sidebar-link" style="background-color: #1e293b;"><i class="fas fa-user icon-green"></i> Profile</a></li>
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
      <!-- Profile Content Container -->
      <div class="container mx-auto p-6">
        <!-- Profile Header -->
        <div class="glass-card p-6 rounded-xl mb-6">
          <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <!-- Profile Image -->
            <div class="relative">
              <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-[#4ade80] flex items-center justify-center bg-[#1e293b]">
                <i class="fas fa-user text-4xl"></i>
              </div>
            </div>        
            
            <!-- Profile Info -->
            <div class="flex-1 text-center md:text-left">
              <h1 class="text-2xl font-bold text-[#4ade80] pl-2"><?php echo $userName; ?></h1>
              <p class="text-[#cbd5e1] text-lg pl-2">Farmer</p>
              <p class="text-[#cbd5e1] mt-2 pl-2"><?php echo $userEmail; ?></p>
            </div>
          </div>
        </div>
        
        <!-- Personal Info Tab -->
        <div id="personal" class="tab-content active p-6">
          <form id="personalForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <label class="block text-[#cbd5e1] mb-2">Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($userName); ?>" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none" required>
              </div>
              <div class="md:col-span-2">
                <label class="block text-[#cbd5e1] mb-2">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none" required>
              </div>
              <div>
                <label class="block text-[#cbd5e1] mb-2">New Password</label>
                <div class="password-toggle-wrapper">
                  <input type="password" name="password" placeholder="Enter new password" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none">
                  <button type="button" class="password-toggle-btn" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                </div>
              </div>
              <div>
                <label class="block text-[#cbd5e1] mb-2">Confirm Password</label>
                <div class="password-toggle-wrapper">
                  <input type="password" name="confirm_password" placeholder="Confirm new password" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none">
                  <button type="button" class="password-toggle-btn" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                </div>
              </div>
            </div>
            <div class="mt-6 flex justify-end">
              <button type="submit" class="px-6 py-2 bg-[#4ade80] text-[#0B1120] rounded-lg hover:bg-[#22c55e] transition-colors">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
  </main>

  <script>
    // Password Toggle
    function togglePassword(btn) {
      const input = btn.previousElementSibling;
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }

    // Hamburger Menu Toggle
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    const dashboardMain = document.querySelector('.dashboard-main');

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

    document.getElementById('personalForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      
      submitBtn.textContent = 'Saving...';
      submitBtn.disabled = true;

      fetch('update_profile.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        // Create and show notification
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 ${data.success ? 'bg-[#4ade80] text-[#0B1120]' : 'bg-red-500 text-white'} p-4 rounded-lg shadow-lg z-50`;
        notification.textContent = data.message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
          notification.remove();
        }, 3000);

        if (data.success) {
            // Update the display name in the profile header if changed
            const nameDisplay = document.querySelector('h1.text-2xl');
            if (nameDisplay) {
                nameDisplay.textContent = formData.get('name');
            }
            // Clear password fields
            document.querySelector('input[name="password"]').value = '';
            document.querySelector('input[name="confirm_password"]').value = '';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      })
      .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      });
    });
  </script>
</body>
</html>
