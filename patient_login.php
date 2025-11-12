<?php
session_start();
include 'connection.php';
$msg = "";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND role='patient' AND status='approved'");
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['password'])) {
            $_SESSION['patient_id']   = $row['id'];
            $_SESSION['patient_name'] = $row['name'];
            header("Location: patient_dashboard.php");
            exit();
        } else {
            $msg = "❌ Incorrect password.";
        }
    } else {
        $msg = "⚠️ Invalid email or account not yet approved by admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .split-screen {
      display: flex;
      height: 100vh;
    }
    .left-panel {
      flex: 1;
      background: url('assets/hospital.webp') no-repeat center center/cover;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
    }
    .left-panel::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
    }
    .left-content {
      position: relative;
      z-index: 2;
      max-width: 500px;
    }
    .left-content h1 {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      font-weight: bold;
    }
    .left-content p {
      font-size: 1.2rem;
    }
    .right-panel {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #f5f8fa;
      padding: 20px;
    }
    .glass-card {
      width: 100%;
      max-width: 420px;
      background: rgba(255,255,255,0.85);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    }
    .glass-card h4 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 1.2rem;
      color: #333;
    }
    .form-control {
      border-radius: 10px;
      padding: 12px;
    }
    .btn-custom {
      background: linear-gradient(45deg, #28a745, #20c997);
      border: none;
      font-weight: bold;
      color: white;
      border-radius: 10px;
      transition: 0.3s;
    }
    .btn-custom:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(40,167,69,0.4);
    }
    a {
      color: #20c997;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="split-screen">
  <!-- Left side info -->
  <div class="left-panel">
    <div class="left-content">
      <h1>🔐 Secure Patient Login</h1>
      <p>Login to access your appointments, check status updates, and manage your healthcare journey with ease.</p>
    </div>
  </div>

  <!-- Right side login form -->
  <div class="right-panel">
    <div class="glass-card">
      <h4>👩‍⚕️ Patient Login</h4>

      <?php if ($msg != ""): ?>
        <div class="alert alert-danger text-center"><?php echo $msg; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
      </form>

      <p class="text-center mt-3">
        New User? <a href="patient_register.php">Register here</a>
      </p>
    </div>
  </div>
</div>

</body>
</html>
