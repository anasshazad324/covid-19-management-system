<?php
include 'connection.php';
$msg = "";

if (isset($_POST['register'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);    
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password']; // no hashing

    // check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "⚠️ This email is already registered.";
    } else {
        $sql = "INSERT INTO users (name, email, phone, address, password, role, status)
                VALUES ('$name','$email','$phone','$address','$password','hospital','pending')";

        if (mysqli_query($conn, $sql)) {
            $msg = "✅ Registration successful! Please wait for admin approval.";
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
  <title>Hospital Registration | MediConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --secondary: #64748b;
      --light: #f8fafc;
      --dark: #1e293b;
      --success: #10b981;
      --warning: #f59e0b;
      --error: #ef4444;
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

    .split-container {
      display: flex;
      height: 100vh;
      position: relative;
    }

    .left-side {
      flex: 1;
      background: linear-gradient(rgba(37, 99, 235, 0.85), rgba(29, 78, 216, 0.9)), 
                  url('https://images.unsplash.com/photo-1588776814546-ec938b09b82c?auto=format&fit=crop&w=1400&q=80') no-repeat center center;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      padding: 40px;
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
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    }

    .left-content {
      max-width: 600px;
      text-align: center;
      z-index: 1;
      animation: fadeInUp 1s ease-out;
    }

    .left-side h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .left-side p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      opacity: 0.9;
    }

    .features {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-top: 40px;
    }

    .feature {
      display: flex;
      flex-direction: column;
      align-items: center;
      max-width: 150px;
    }

    .feature i {
      font-size: 2rem;
      margin-bottom: 10px;
      background: rgba(255,255,255,0.2);
      width: 70px;
      height: 70px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      backdrop-filter: blur(10px);
    }

    .feature span {
      font-size: 0.9rem;
      font-weight: 500;
    }

    .right-side {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: var(--light);
      padding: 40px;
      overflow-y: auto;
    }

    .register-box {
      width: 100%;
      max-width: 500px;
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
      animation: fadeIn 0.8s ease-out;
      position: relative;
    }

    .register-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), var(--success));
      border-radius: 16px 16px 0 0;
    }

    .logo {
      text-align: center;
      margin-bottom: 10px;
    }

    .logo i {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 10px;
    }

    .register-box h2 {
      text-align: center;
      margin-bottom: 5px;
      color: var(--dark);
      font-weight: 700;
      font-size: 1.8rem;
    }

    .subtitle {
      text-align: center;
      color: var(--secondary);
      margin-bottom: 30px;
      font-size: 0.95rem;
    }

    .form-group {
      margin-bottom: 20px;
      position: relative;
    }

    .form-control {
      border-radius: 10px;
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

    .form-label {
      font-weight: 500;
      margin-bottom: 8px;
      color: var(--dark);
      font-size: 0.9rem;
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
      background: var(--primary);
      border: none;
      padding: 14px;
      border-radius: 10px;
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s;
      margin-top: 10px;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-custom:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
    }

    .btn-custom:active {
      transform: translateY(0);
    }

    .alert {
      text-align: center;
      font-size: 0.9rem;
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 20px;
    }

    .small-text {
      text-align: center;
      margin-top: 25px;
      font-size: 0.9rem;
      color: var(--secondary);
    }

    .small-text a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
    }

    .small-text a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 25px 0;
    }

    .divider::before, .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #e2e8f0;
    }

    .divider span {
      padding: 0 15px;
      color: var(--secondary);
      font-size: 0.85rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive design */
    @media (max-width: 992px) {
      .split-container {
        flex-direction: column;
        height: auto;
      }
      
      .left-side {
        min-height: 40vh;
        padding: 30px 20px;
      }
      
      .left-side h1 {
        font-size: 2rem;
      }
      
      .features {
        gap: 15px;
      }
      
      .feature {
        max-width: 120px;
      }
      
      .feature i {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
      }
    }

    @media (max-width: 576px) {
      .register-box {
        padding: 25px;
      }
      
      .left-side h1 {
        font-size: 1.8rem;
      }
      
      .features {
        flex-direction: column;
        gap: 20px;
        align-items: center;
      }
    }
  </style>
</head>
<body>

<div class="split-container">
  <!-- Left side with hospital info / image -->
  <div class="left-side">
    <div class="left-content">
      <h1>Join Our Healthcare Network</h1>
      <p>Register your hospital to streamline patient management, appointments, and medical records in one secure platform.</p>
      
      <div class="features">
        <div class="feature">
          <i class="fas fa-user-injured"></i>
          <span>Patient Management</span>
        </div>
        <div class="feature">
          <i class="fas fa-calendar-check"></i>
          <span>Appointment Scheduling</span>
        </div>
        <div class="feature">
          <i class="fas fa-shield-alt"></i>
          <span>Secure & Compliant</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right side with registration form -->
  <div class="right-side">
    <div class="register-box">
      <div class="logo">
        <i class="fas fa-hospital"></i>
      </div>
      <h2>Hospital Registration</h2>
      <p class="subtitle">Create your account to get started</p>
      
      <?php if($msg!=""): ?>
        <div class="alert alert-info"><?php echo $msg; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Hospital Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter hospital name" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Hospital Address</label>
          <textarea name="address" class="form-control" placeholder="Enter full hospital address" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
          <button type="button" class="password-toggle" id="togglePassword">
            <i class="far fa-eye"></i>
          </button>
        </div>
        
        <button type="submit" name="register" class="btn-custom">Register Hospital</button>
      </form>

      <div class="small-text">
        Already have an account? <a href="hospital_login.php">Login here</a>
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
</script>

</body>
</html>