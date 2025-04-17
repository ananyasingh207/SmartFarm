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
    }
    /* Glassmorphism for cards */
    .glass-card {
      background: rgba(31, 41, 55, 0.85);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(74, 222, 128, 0.3);
    }
    /* Hover effects */
    .card-hover:hover {
      transform: scale(1.02);
      box-shadow: 0 8px 24px rgba(74, 222, 128, 0.4);
    }
    /* Form field styling */
    .form-field:focus {
      border-color: #4ade80;
      box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.2);
    }
    /* Tab styling */
    .tab {
      position: relative;
      transition: all 0.3s ease;
    }
    .tab::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background-color: #4ade80;
      transition: width 0.3s ease;
    }
    .tab.active::after {
      width: 100%;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
      animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
  </style>
</head>
<body class="min-h-screen">
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
          <h1 class="text-2xl font-bold text-[#4ade80] pl-2">Your Name</h1>
          <p class="text-[#cbd5e1] text-lg pl-2">Farmer</p>
          
          <div class="flex flex-wrap gap-2 justify-center md:justify-start mt-2">
            <span class="bg-[#1e293b] px-3 py-1 rounded-full text-sm text-[#4ade80] border border-[#4ade80]">Premium Member</span>
            <span class="bg-[#1e293b] px-3 py-1 rounded-full text-sm text-[#4ade80] border border-[#4ade80]">10 Hectares</span>
            <span class="bg-[#1e293b] px-3 py-1 rounded-full text-sm text-[#4ade80] border border-[#4ade80]">4 Active Devices</span>
          </div>
        </div>
      </div>
    </div>
    
      <!-- Personal Info Tab -->
      <div id="personal" class="tab-content active p-6">
        <form id="personalForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label class="block text-[#cbd5e1] mb-2">Full Name</label>
              <input type="text" value="Your name" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none">
            </div>
            <div class="md:col-span-2">
              <label class="block text-[#cbd5e1] mb-2">Email</label>
              <input type="email" value="email@example.com" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none">
            </div>
            <div>
              <label class="block text-[#cbd5e1] mb-2">New Password</label>
              <input type="password" placeholder="Enter new password" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none">
            </div>
            <div>
              <label class="block text-[#cbd5e1] mb-2">Confirm Password</label>
              <input type="password" placeholder="Confirm new password" class="w-full p-3 rounded-lg bg-[#1e293b] border border-[#4ade80] text-white form-field focus:outline-none">
            </div>
          </div>
          <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-[#4ade80] text-[#0B1120] rounded-lg hover:bg-[#22c55e] transition-colors">Save Changes</button>
          </div>
        </form>
      </div>
      
  

  <!-- JavaScript for Profile Page -->
  <script>
    
    // Form submission handlers (prevent default and show save notification)
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create and show notification
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-[#4ade80] text-[#0B1120] p-4 rounded-lg shadow-lg z-50';
        notification.textContent = 'Changes saved successfully!';
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
          notification.remove();
        }, 3000);
      });
    });
  </script>
</body>
</html>