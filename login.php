<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Smart Irrigation</title>
 <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="left-section">
    <img src="img1.png" class="slide" style="opacity: 1;">
    <img src="img2.png" class="slide">
    <img src="img3.png" class="slide">
    <img src="img4.png" class="slide">
    <img src="img5.png" class="slide">
  </div>

  <div class="right-section">
    <div class="login-box">
      <div class="form-container">
        <!-- Login Form -->
        <div id="loginFormContainer" class="active">
          <h2>Login</h2>

          <?php if (isset($_GET['error']) && $_GET['type'] === 'login'): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
          <?php endif; ?>

          <form id="loginForm" action="auth.php" method="POST">
            <input type="hidden" name="type" value="login" />
            <input type="email" name="email" id="email" placeholder="Email" required />
            <input type="password" name="password" id="password" placeholder="Password" required />
            <input type="hidden" name="type" value="login" />
            <button type="submit">Login</button>
          </form>
          
          <div class="register-link">
            Don't have an account? <a onclick="showRegister()">Create one</a>
          </div>
        </div>
        <!-- Register Form -->
        <div id="registerFormContainer">
          <h2>Register</h2>

          <?php if (isset($_GET['error']) && $_GET['type'] === 'register'): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
          <?php endif; ?>

          <form id="registerForm" action="auth.php" method="POST">
            <input type="hidden" name="type" value="register" />
            <input type="text" name="name" id="name" placeholder="Full Name" required />
            <input type="email" name="email" id="regEmail" placeholder="Email" required />
            <input type="password" name="password" id="regPassword" placeholder="Password" required />
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required />
            <select name="role" id="role" required>
              <option value="">Select Role</option>
              <option value="farmer">Farmer</option>
              <option value="manufacturer">Manufacturer</option>
              <option value="service_provider">Service Provider</option>
            </select>
            <button type="submit">Register</button>
          </form>

          
          
          <div class="register-link">
            Already have an account? <a onclick="showLogin()">Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Slideshow
    const slides = document.querySelectorAll('.slide');
    let index = 0;
    setInterval(() => {
      slides.forEach(slide => slide.style.opacity = '0');
      slides[index].style.opacity = '1';
      index = (index + 1) % slides.length;
    }, 1500);
  
    // Toggle Functions
    function showRegister() {
      document.getElementById("loginFormContainer").classList.remove("active");
      document.getElementById("registerFormContainer").classList.add("active");
    }
  
    function showLogin() {
      document.getElementById("registerFormContainer").classList.remove("active");
      document.getElementById("loginFormContainer").classList.add("active");
    }
  
    // Check URL Param to Open Correct Form
    window.onload = () => {
      const params = new URLSearchParams(window.location.search);
      const form = params.get('form');
  
      if (form === 'register') {
        showRegister();
      } else {
        showLogin(); // default
      }
    };
  </script>
  
</body>
</html>

<!-- SmartIrrigation1234 -->