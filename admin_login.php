<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_email'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$admin_email = "admin@covid.com";
$admin_password = "admin123";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password!";
    } elseif ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin_email'] = $admin_email;
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Ensure no output before header
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | COVID System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --primary-light: #8b5cf6;
      --secondary: #64748b;
      --light: #f8fafc;
      --dark: #1e293b;
      --success: #10b981;
      --warning: #f59e0b;
      --error: #ef4444;
      --gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      --dark-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    
    body {
      background-color: #f1f5f9;
      overflow-x: hidden;
    }
    
    .split-container {
      display: flex;
      min-height: 100vh;
    }
    
    /* Left Side Styling */
    .left-side {
      flex: 1;
      background: var(--dark-gradient);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      padding: 3rem;
      position: relative;
      overflow: hidden;
    }
    
    .left-side::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
    }
    
    .left-content {
      max-width: 600px;
      z-index: 1;
      animation: fadeInLeft 1s ease-out;
    }
    
    .left-side h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }
    
    .left-side p {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
      opacity: 0.9;
    }
    
    .features {
      margin-top: 2rem;
    }
    
    .feature-item {
      display: flex;
      align-items: center;
      margin-bottom: 1.2rem;
    }
    
    .feature-item i {
      font-size: 1.2rem;
      margin-right: 15px;
      background: rgba(99, 102, 241, 0.2);
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    
    .security-badge {
      display: inline-flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.1);
      padding: 8px 16px;
      border-radius: 20px;
      margin-top: 20px;
      font-size: 0.9rem;
    }
    
    .security-badge i {
      margin-right: 8px;
      color: #10b981;
    }
    
    /* Right Side Styling */
    .right-side {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: var(--light);
      padding: 2rem;
      overflow-y: auto;
    }
    
    .login-container {
      width: 100%;
      max-width: 450px;
      background: #fff;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
      animation: fadeInRight 1s ease-out;
      position: relative;
    }
    
    .login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: var(--gradient);
      border-radius: 20px 20px 0 0;
    }
    
    .logo {
      text-align: center;
      margin-bottom: 10px;
    }
    
    .logo i {
      font-size: 2.8rem;
      background: var(--gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 10px;
    }
    
    .login-container h2 {
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-align: center;
      color: var(--dark);
      font-size: 1.8rem;
    }
    
    .subtitle {
      text-align: center;
      color: var(--secondary);
      margin-bottom: 2rem;
      font-size: 0.95rem;
    }
    
    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }
    
    .form-label {
      font-weight: 500;
      margin-bottom: 8px;
      color: var(--dark);
      font-size: 0.9rem;
    }
    
    .form-control {
      border-radius: 12px;
      padding: 14px 16px;
      font-size: 1rem;
      border: 1.5px solid #e2e8f0;
      transition: all 0.3s;
      background-color: #f8fafc;
    }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
      background-color: white;
    }
    
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 42px;
      background: none;
      border: none;
      color: var(--secondary);
      cursor: pointer;
    }
    
    .btn-login {
      width: 100%;
      background: var(--gradient);
      border: none;
      padding: 14px;
      border-radius: 12px;
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s;
      margin-top: 10px;
      box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
    }
    
    .btn-login:active {
      transform: translateY(0);
    }
    
    .error-message {
      text-align: center;
      font-size: 0.9rem;
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 20px;
      background-color: rgba(239, 68, 68, 0.1);
      color: var(--error);
      border: 1px solid rgba(239, 68, 68, 0.2);
    }
    
    .demo-credentials {
      background-color: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 12px;
      padding: 15px;
      margin-top: 25px;
      font-size: 0.85rem;
    }
    
    .demo-credentials h6 {
      color: var(--primary);
      margin-bottom: 8px;
      font-weight: 600;
    }
    
    .demo-credentials p {
      margin-bottom: 5px;
      color: var(--secondary);
    }
    
    .footer-links {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e9ecef;
    }
    
    .footer-links a {
      color: var(--secondary);
      text-decoration: none;
      font-size: 0.85rem;
      margin: 0 10px;
      transition: all 0.2s;
    }
    
    .footer-links a:hover {
      color: var(--primary);
    }
    
    /* Loading animation */
    .btn-loading {
      position: relative;
      pointer-events: none;
    }
    
    .btn-loading::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      top: 50%;
      left: 50%;
      margin-left: -10px;
      margin-top: -10px;
      border: 2px solid #ffffff;
      border-radius: 50%;
      border-right-color: transparent;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    /* Animations */
    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes fadeInRight {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }
    
    /* Responsive Design */
    @media (max-width: 992px) {
      .split-container {
        flex-direction: column;
        min-height: auto;
      }
      
      .left-side {
        padding: 2rem;
        min-height: 50vh;
      }
      
      .left-side h1 {
        font-size: 2.2rem;
      }
      
      .login-container {
        padding: 2rem;
      }
    }
    
    @media (max-width: 576px) {
      .left-side, .right-side {
        padding: 1.5rem;
      }
      
      .left-side h1 {
        font-size: 1.8rem;
      }
      
      .login-container {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<div class="split-container">
  <!-- Left Side -->
  <div class="left-side">
    <div class="left-content">
      <h1>Administrator Portal</h1>
      <p>Access the complete COVID System management dashboard with full administrative control, analytics, and system oversight.</p>
      
      <div class="features">
        <div class="feature-item">
          <i class="fas fa-shield-alt"></i>
          <span>Full system administration and control</span>
        </div>
        <div class="feature-item">
          <i class="fas fa-chart-bar"></i>
          <span>Comprehensive analytics and reporting</span>
        </div>
        <div class="feature-item">
          <i class="fas fa-hospital-user"></i>
          <span>Hospital and user management</span>
        </div>
        <div class="feature-item">
          <i class="fas fa-cogs"></i>
          <span>System configuration and settings</span>
        </div>
      </div>
      
      <div class="security-badge">
        <i class="fas fa-lock"></i>
        <span>Secure admin authentication required</span>
      </div>
    </div>
  </div>

  <!-- Right Side -->
  <div class="right-side">
    <div class="login-container">
      <div class="logo">
        <i class="fas fa-user-shield"></i>
      </div>
      <h2>Admin Login</h2>
      <p class="subtitle">Enter your credentials to continue</p>

      <?php if ($error != ""): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" action="" id="loginForm">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="admin@covid.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
          <button type="button" class="password-toggle" id="togglePassword">
            <i class="far fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn-login" id="loginBtn">Access Dashboard</button>
      </form>

      <div class="demo-credentials">
        <h6>Demo Credentials</h6>
        <p><strong>Email:</strong> admin@covid.com</p>
        <p><strong>Password:</strong> admin123</p>
      </div>
      
      <div class="footer-links">
        <a href="index.php">Home</a>
        <a href="hospital_login.php">Hospital Login</a>
        <a href="patient_login.php">Patient Login</a>
      </div>
    </div>
  </div>
</div>

<script>
  // Password toggle functionality
  document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });

  // Form submission loading state
  document.getElementById('loginForm').addEventListener('submit', function() {
    const loginBtn = document.getElementById('loginBtn');
    loginBtn.classList.add('btn-loading');
    loginBtn.disabled = true;
    loginBtn.innerHTML = 'Authenticating...';
  });
</script>

</body>
</html>