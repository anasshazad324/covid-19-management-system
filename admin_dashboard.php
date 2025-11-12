<?php
session_start();
include 'connection.php';

// Check if admin is logged in - FIXED SESSION CHECK
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

// Initialize message variable
$msg = "";

// Approve/Reject Hospital
if (isset($_POST['update_hospital_status'])) {
    $hospital_id = (int)$_POST['hospital_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if (mysqli_query($conn, "UPDATE users SET status='$status' WHERE id=$hospital_id AND role='hospital'")) {
        $msg = "Hospital status updated successfully!";
    } else {
        $msg = "Error updating hospital status: " . mysqli_error($conn);
    }
}

// Vaccine stock update
if (isset($_POST['update_vaccine'])) {
    $vaccine_id = (int)$_POST['vaccine_id'];
    $stock = (int)$_POST['stock'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if (mysqli_query($conn, "UPDATE vaccines SET stock=$stock, status='$status' WHERE id=$vaccine_id")) {
        $msg = "Vaccine updated successfully!";
    } else {
        $msg = "Error updating vaccine: " . mysqli_error($conn);
    }
}

// Fetch data with error handling
$hospitals = mysqli_query($conn, "SELECT * FROM users WHERE role='hospital'") or die(mysqli_error($conn));
$patients = mysqli_query($conn, "SELECT * FROM users WHERE role='patient'") or die(mysqli_error($conn));
$appointments = mysqli_query($conn, "SELECT a.*, u.name as patient_name, h.name as hospital_name 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        JOIN users h ON a.hospital_id = h.id 
        ORDER BY a.appointment_date DESC") or die(mysqli_error($conn));

// Check if vaccines table exists
$vaccines = mysqli_query($conn, "SHOW TABLES LIKE 'vaccines'");
if (mysqli_num_rows($vaccines) > 0) {
    $vaccines = mysqli_query($conn, "SELECT * FROM vaccines ORDER BY name ASC") or die(mysqli_error($conn));
} else {
    $vaccines = false;
}

// Reports filter
$where = "";
$report_type = "";
if (isset($_GET['report_type']) && !empty($_GET['report_type'])) {
    $report_type = $_GET['report_type'];
    if ($report_type == "daily") {
        $where = "WHERE DATE(a.appointment_date) = CURDATE()";
    } elseif ($report_type == "weekly") {
        $where = "WHERE YEARWEEK(a.appointment_date, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($report_type == "monthly") {
        $where = "WHERE YEAR(a.appointment_date) = YEAR(CURDATE()) AND MONTH(a.appointment_date) = MONTH(CURDATE())";
    }
}

$reports = mysqli_query($conn, "SELECT a.*, u.name as patient_name, h.name as hospital_name 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        JOIN users h ON a.hospital_id = h.id 
        $where ORDER BY a.appointment_date DESC") or die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | COVID System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --danger: #ef4444;
            --dark: #1f2937;
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%) !important;
        }
        
        .section-card {
            margin-bottom: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .section-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .badge-status {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .badge-approved { background: var(--success); color: #fff; }
        .badge-rejected { background: var(--danger); color: #fff; }
        .badge-pending { background: var(--warning); color: #000; }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 500;
        }
        
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #374151;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">
        <i class="fas fa-shield-alt me-2"></i>Admin Dashboard
    </span>
    <div class="d-flex align-items-center">
        <span class="text-white me-3">
            <i class="fas fa-user me-1"></i> Welcome, <?php echo $_SESSION['admin_email']; ?>
        </span>
        <a href="admin_logout.php" class="btn btn-outline-light btn-sm">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>
  </div>
</nav>

<div class="container mt-4">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">Welcome, Administrator!</h2>
                <p class="mb-0">Manage hospitals, patients, appointments, and system reports from this dashboard.</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="bg-white text-dark rounded p-3 d-inline-block">
                    <small class="text-muted">Current Time</small>
                    <div class="fw-bold" id="currentTime"><?php echo date('Y-m-d H:i:s'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card text-primary">
                <div class="stats-number"><?php echo mysqli_num_rows($hospitals); ?></div>
                <div class="stats-label">
                    <i class="fas fa-hospital me-1"></i> Total Hospitals
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-success">
                <div class="stats-number"><?php echo mysqli_num_rows($patients); ?></div>
                <div class="stats-label">
                    <i class="fas fa-user-injured me-1"></i> Total Patients
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-warning">
                <div class="stats-number"><?php echo mysqli_num_rows($appointments); ?></div>
                <div class="stats-label">
                    <i class="fas fa-calendar-check me-1"></i> Total Appointments
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-info">
                <div class="stats-number">
                    <?php 
                        $pending_hospitals = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='hospital' AND status='pending'");
                        $pending_count = mysqli_fetch_assoc($pending_hospitals)['count'];
                        echo $pending_count;
                    ?>
                </div>
                <div class="stats-label">
                    <i class="fas fa-clock me-1"></i> Pending Approvals
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($msg)): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <i class="fas fa-info-circle me-2"></i> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hospitals Section -->
    <div class="card section-card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-hospital me-2"></i> Hospital Management
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                mysqli_data_seek($hospitals, 0); // Reset pointer
                while($row = mysqli_fetch_assoc($hospitals)): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <span class="badge-status 
                                <?php echo $row['status']=='approved'?'badge-approved':
                                       ($row['status']=='rejected'?'badge-rejected':'badge-pending'); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="hospital_id" value="<?php echo $row['id']; ?>">
                                <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                    <option value="approved" <?php echo $row['status']=='approved'?'selected':''; ?>>Approve</option>
                                    <option value="rejected" <?php echo $row['status']=='rejected'?'selected':''; ?>>Reject</option>
                                    <option value="pending" <?php echo $row['status']=='pending'?'selected':''; ?>>Pending</option>
                                </select>
                                <button type="submit" name="update_hospital_status" class="btn btn-sm btn-success">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Patients Section -->
    <div class="card section-card">
        <div class="card-header bg-success text-white">
            <i class="fas fa-user-injured me-2"></i> Patient Management
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                mysqli_data_seek($patients, 0); // Reset pointer
                while($row = mysqli_fetch_assoc($patients)): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td>
                            <span class="badge badge-status 
                                <?php echo $row['status']=='approved'?'badge-approved':'badge-pending'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Appointments Section -->
    <div class="card section-card">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-calendar-check me-2"></i> Appointments
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Hospital</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                mysqli_data_seek($appointments, 0); // Reset pointer
                while($row = mysqli_fetch_assoc($appointments)): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                        <td><?php echo ucfirst($row['type']); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($row['appointment_date'])); ?></td>
                        <td>
                            <span class="badge badge-status 
                                <?php echo $row['status']=='completed'?'badge-approved':
                                       ($row['status']=='cancelled'?'badge-rejected':'badge-pending'); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="card section-card">
        <div class="card-header bg-info text-dark">
            <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
        </div>
        <div class="card-body">
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-4">
                    <select name="report_type" class="form-select">
                        <option value="">All Appointments</option>
                        <option value="daily" <?php echo $report_type=='daily'?'selected':''; ?>>Today's Appointments</option>
                        <option value="weekly" <?php echo $report_type=='weekly'?'selected':''; ?>>This Week's Appointments</option>
                        <option value="monthly" <?php echo $report_type=='monthly'?'selected':''; ?>>This Month's Appointments</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Showing <?php echo mysqli_num_rows($reports); ?> appointment(s)
                        <?php echo $report_type ? "for " . ucfirst($report_type) : ""; ?>
                    </small>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    mysqli_data_seek($reports, 0); // Reset pointer
                    while($row = mysqli_fetch_assoc($reports)): 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                            <td><?php echo ucfirst($row['type']); ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($row['appointment_date'])); ?></td>
                            <td>
                                <span class="badge badge-status 
                                    <?php echo $row['status']=='completed'?'badge-approved':
                                           ($row['status']=='cancelled'?'badge-rejected':'badge-pending'); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Vaccines Section (Conditional) -->
    <?php if($vaccines && mysqli_num_rows($vaccines) > 0): ?>
    <div class="card section-card">
        <div class="card-header bg-danger text-white">
            <i class="fas fa-syringe me-2"></i> Vaccine Management
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                mysqli_data_seek($vaccines, 0); // Reset pointer
                while($row = mysqli_fetch_assoc($vaccines)): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td>
                            <span class="badge badge-status 
                                <?php echo $row['status']=='available'?'badge-approved':'badge-rejected'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="vaccine_id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="stock" value="<?php echo $row['stock']; ?>" 
                                       class="form-control form-control-sm" style="width: 80px;" min="0">
                                <select name="status" class="form-select form-select-sm" style="width: 130px;">
                                    <option value="available" <?php echo $row['status']=='available'?'selected':''; ?>>Available</option>
                                    <option value="unavailable" <?php echo $row['status']=='unavailable'?'selected':''; ?>>Unavailable</option>
                                </select>
                                <button type="submit" name="update_vaccine" class="btn btn-sm btn-primary">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = now.toLocaleString();
    }
    setInterval(updateTime, 1000);
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>

</body>
</html>