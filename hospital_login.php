<?php
session_start();
include 'connection.php';
$msg = "";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email' AND role='hospital'");

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        if ($row['status'] != 'approved') {
            $msg = "⚠️ Admin approval ka wait karein.";
        } elseif ($password === $row['password']) { // Simple password comparison
            $_SESSION['hospital_id']   = $row['id'];
            $_SESSION['hospital_name'] = $row['name'];
            header("Location: hospital_dashboard.php");
            exit();
        } else {
            $msg = "❌ Galat password.";
        }
    } else {
        $msg = "⚠️ Email ya account invalid hai.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hospital Login | HealthConnect</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --success: #198754;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            overflow-x: hidden;
        }
        
        .split-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Left Side Styling */
        .left-side {
            flex: 1;
            background: linear-gradient(rgba(13, 110, 253, 0.85), rgba(25, 135, 84, 0.85)), 
                        url('assets/hos.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 3rem;
            color: white;
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
            margin-bottom: 1rem;
        }
        
        .feature-item i {
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
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
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
            font-size: 2.5rem;
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
            color: var(--dark);
            margin-bottom: 2rem;
            font-size: 0.95rem;
            opacity: 0.7;
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
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            background-color: white;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 42px;
            background: none;
            border: none;
            color: #6c757d;
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
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.6);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            text-align: center;
            font-size: 0.9rem;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 20px;
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .register-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-links a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.85rem;
            margin: 0 10px;
            transition: all 0.2s;
        }
        
        .footer-links a:hover {
            color: var(--primary);
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
            <h1>Hospital Management System</h1>
            <p>Access your hospital dashboard to manage patients, appointments, and medical records efficiently.</p>
            
            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-user-injured"></i>
                    <span>Comprehensive patient management</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Easy appointment scheduling</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-file-medical"></i>
                    <span>Secure medical records storage</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Real-time analytics and reports</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side -->
    <div class="right-side">
        <div class="login-container">
            <div class="logo">
                <i class="fas fa-hospital"></i>
            </div>
            <h2>Hospital Login</h2>
            <p class="subtitle">Access your hospital dashboard</p>

            <?php if ($msg != ""): ?>
                <div class="alert alert-danger"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your hospital email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <button type="submit" name="login" class="btn-login">Sign In</button>
            </form>

            <div class="register-link">
                New hospital? <a href="hospital_register.php">Register here</a>
            </div>
            
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="patient_login.php">Patient Login</a>
                <a href="admin_login.php">Admin Login</a>
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
    
    // Form focus effects
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
    });
</script>

</body>
</html>