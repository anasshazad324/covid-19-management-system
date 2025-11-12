<?php
include 'connection.php';

// Message variable
$msg = "";

if (isset($_POST['register'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // ✅ Secure hash

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "⚠️ This email is already registered.";
    } else {
        // Insert new patient
        $sql = "INSERT INTO users (name, email, phone, address, password, role, status) 
                VALUES ('$name', '$email', '$phone', '$address', '$password', 'patient', 'approved')";

        if (mysqli_query($conn, $sql)) {
            $msg = "✅ Registration successful! You can now login.";
        } else {
            $msg = "❌ Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Registration | HealthConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --primary-light: #60a5fa;
      --secondary: #64748b;
      --light: #f8fafc;
      --dark: #1e293b;
      --success: #10b981;
      --warning: #f59e0b;
      --error: #ef4444;
      --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light);
      overflow-x: hidden;
    }

    .split-screen {
      display: flex;
      min-height: 100vh;
    }

    /* Left Side Styling */
    .left {
      flex: 1;
      background: var(--gradient);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      padding: 3rem;
      position: relative;
      overflow: hidden;
    }

    .left::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    }

    .left-content {
      max-width: 600px;
      z-index: 1;
      animation: fadeInLeft 1s ease-out;
    }

    .left h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }

    .left p {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
      opacity: 0.9;
    }

    .benefits {
      margin-top: 2rem;
    }

    .benefit-item {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }

    .benefit-item i {
      font-size: 1.2rem;
      margin-right: 15px;
      background: rgba(255,255,255,0.2);
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }

    /* Right Side Styling */
    .right {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: var(--light);
      padding: 2rem;
      overflow-y: auto;
    }

    .form-container {
      width: 100%;
      max-width: 500px;
      background: #fff;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.08);
      animation: fadeInRight 1s ease-out;
      position: relative;
    }

    .form-container::before {
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
      font-size: 2.5rem;
      background: var(--gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 10px;
    }

    .form-container h3 {
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
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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

    .btn-custom {
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
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .btn-custom:active {
      transform: translateY(0);
    }

    .alert {
      text-align: center;
      font-size: 0.9rem;
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 20px;
    }

    .login-link {
      text-align: center;
      margin-top: 25px;
      font-size: 0.9rem;
      color: var(--secondary);
    }

    .login-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
    }

    .login-link a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    footer {
      text-align: center;
      padding: 1.5rem;
      background: var(--dark);
      color: white;
      width: 100%;
    }

    footer a {
      color: var(--primary-light);
      text-decoration: none;
      transition: all 0.2s;
    }

    footer a:hover {
      color: white;
      text-decoration: underline;
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
      .split-screen {
        flex-direction: column;
        min-height: auto;
      }
      
      .left {
        padding: 2rem;
        min-height: 50vh;
      }
      
      .left h1 {
        font-size: 2.2rem;
      }
      
      .form-container {
        padding: 2rem;
      }
    }

    @media (max-width: 576px) {
      .left, .right {
        padding: 1.5rem;
      }
      
      .left h1 {
        font-size: 1.8rem;
      }
      
      .form-container {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<div class="split-screen">
  <!-- LEFT SIDE -->
  <div class="left">
    <div class="left-content">
      <h1>Your Health Journey Starts Here</h1>
      <p>Join thousands of patients who are managing their healthcare seamlessly with our platform.</p>
      
      <div class="benefits">
        <div class="benefit-item">
          <i class="fas fa-check-circle"></i>
          <span>Quick access to hospitals and specialists</span>
        </div>
        <div class="benefit-item">
          <i class="fas fa-check-circle"></i>
          <span>Easy online appointment booking</span>
        </div>
        <div class="benefit-item">
          <i class="fas fa-check-circle"></i>
          <span>Secure medical records storage</span>
        </div>
        <div class="benefit-item">
          <i class="fas fa-check-circle"></i>
          <span>Fast COVID-19 testing and updates</span>
        </div>
      </div>
      
      <p class="mt-3">Join today and take control of your healthcare journey!</p>
    </div>
  </div>

  <!-- RIGHT SIDE -->
  <div class="right">
    <div class="form-container">
      <div class="logo">
        <i class="fas fa-user-injured"></i>
      </div>
      <h3>Patient Registration</h3>
      <p class="subtitle">Create your account in less than a minute</p>

      <?php if ($msg != ""): ?>
        <div class="alert alert-info"><?php echo $msg; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="Enter your phone number" required>
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" placeholder="Enter your complete address" rows="3" required></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Create a secure password" required>
          <button type="button" class="password-toggle" id="togglePassword">
            <i class="far fa-eye"></i>
          </button>
        </div>
        <button type="submit" name="register" class="btn-custom">Create Account</button>
      </form>

      <div class="login-link">
        Already have an account? <a href="patient_login.php">Sign in here</a>
      </div>
    </div>
  </div>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> HealthConnect System | 
  <a href="index.php">Home</a> | 
  <a href="hospital_login.php">Hospital Portal</a> | 
  <a href="admin_login.php">Admin Portal</a>
</footer>

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

  // Form validation enhancement
  document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    if (password.length < 6) {
      e.preventDefault();
      alert('Password should be at least 6 characters long.');
    }
  });
</script>

</body>
</html>