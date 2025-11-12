<?php
session_start();
if (!isset($_SESSION['hospital_id'])) {
    header("Location: hospital_login.php");
    exit();
}
include 'connection.php';

$hospital_id = $_SESSION['hospital_id'];

// ✅ Update Appointment Status
if (isset($_POST['update_status'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "UPDATE appointments SET status='$status' WHERE id=$appointment_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "✅ Appointment #$appointment_id updated to $status";
    } else {
        $_SESSION['msg'] = "❌ Error updating appointment: " . mysqli_error($conn);
    }
    header("Location: hospital_dashboard.php");
    exit();
}

// ✅ Update Covid Test Result / Vaccination
if (isset($_POST['update_result'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $test_result = mysqli_real_escape_string($conn, $_POST['test_result']);
    $vaccination_status = mysqli_real_escape_string($conn, $_POST['vaccination_status']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $check = mysqli_query($conn, "SELECT * FROM results WHERE appointment_id=$appointment_id");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE results 
                SET test_result='$test_result', vaccination_status='$vaccination_status', remarks='$remarks' 
                WHERE appointment_id=$appointment_id";
    } else {
        $sql = "INSERT INTO results (appointment_id, test_result, vaccination_status, remarks) 
                VALUES ($appointment_id, '$test_result', '$vaccination_status', '$remarks')";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "✅ Result updated for appointment #$appointment_id";
    } else {
        $_SESSION['msg'] = "❌ Error updating result: " . mysqli_error($conn);
    }
    header("Location: hospital_dashboard.php");
    exit();
}

// ✅ Fetch Hospital Appointments
$sql = "SELECT a.*, u.name as patient_name, u.phone 
        FROM appointments a
        JOIN users u ON a.patient_id = u.id
        WHERE a.hospital_id = $hospital_id
        ORDER BY a.appointment_date DESC";
$result = mysqli_query($conn, $sql);

// Get stats for dashboard
$total_appointments = mysqli_num_rows($result);
$pending_appointments = mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE hospital_id = $hospital_id AND status='pending'");
$pending_count = mysqli_fetch_assoc($pending_appointments)['count'];
$completed_appointments = mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE hospital_id = $hospital_id AND status='completed'");
$completed_count = mysqli_fetch_assoc($completed_appointments)['count'];
mysqli_data_seek($result, 0); // Reset pointer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Dashboard | HealthConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --success: #198754;
            --warning: #ffc107;
            --info: #0dcaf0;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f0f8ff 0%, #f5f0ff 100%);
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Styles */
        .dashboard-header {
            background: var(--gradient);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }
        
        .welcome-text h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .welcome-text p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .stat-card.total {
            border-left-color: var(--primary);
        }
        
        .stat-card.pending {
            border-left-color: var(--warning);
        }
        
        .stat-card.completed {
            border-left-color: var(--success);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .stat-card.total .stat-icon {
            background: rgba(13, 110, 253, 0.1);
            color: var(--primary);
        }
        
        .stat-card.pending .stat-icon {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .stat-card.completed .stat-icon {
            background: rgba(25, 135, 84, 0.1);
            color: var(--success);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .section-card:hover {
            transform: translateY(-3px);
        }
        
        .card-header-custom {
            background: var(--gradient);
            color: white;
            padding: 20px 25px;
            border-bottom: none;
        }
        
        .card-header-custom h3 {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body-custom {
            padding: 25px;
        }
        
        /* Table Styles */
        .table-custom {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table-custom thead {
            background: #f8fafc;
        }
        
        .table-custom th {
            font-weight: 600;
            color: #374151;
            padding: 15px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .table-custom td {
            padding: 15px;
            vertical-align: middle;
        }
        
        /* Badge Styles */
        .badge-custom {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d1ecf1; color: #0c5460; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .badge-completed { background: #d4edda; color: #155724; }
        
        /* Form Styles */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }
        
        .btn-custom {
            background: var(--gradient);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            font-size: 0.9rem;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
            color: white;
        }
        
        .btn-sm-custom {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
        
        /* Alert Styles */
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .action-btn {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .action-btn:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: inherit;
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        /* Appointment Card View */
        .appointment-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary);
            transition: transform 0.3s ease;
        }
        
        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        
        .appointment-card.pending { border-left-color: var(--warning); }
        .appointment-card.approved { border-left-color: var(--info); }
        .appointment-card.completed { border-left-color: var(--success); }
        .appointment-card.rejected { border-left-color: var(--danger); }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 20px;
            }
            
            .welcome-text h1 {
                font-size: 1.8rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .card-body-custom {
                padding: 15px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .action-btn {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="welcome-text">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['hospital_name']); ?>! 🏥</h1>
                    <p>Manage patient appointments and medical records efficiently</p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="hospital_logout.php" class="btn btn-light btn-lg">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number"><?php echo $total_appointments; ?></div>
            <div class="stat-label">Total Appointments</div>
        </div>
        
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number"><?php echo $pending_count; ?></div>
            <div class="stat-label">Pending Appointments</div>
        </div>
        
        <div class="stat-card completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo $completed_count; ?></div>
            <div class="stat-label">Completed Appointments</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="#appointments" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <div class="action-text">View Appointments</div>
        </a>
        
        <a href="#" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="action-text">Add Patient</div>
        </a>
        
        <a href="#" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="action-text">View Reports</div>
        </a>
        
        <a href="#" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="action-text">Settings</div>
        </a>
    </div>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-info alert-custom">
            <i class="fas fa-info-circle me-2"></i><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <!-- Appointments Section -->
    <div class="section-card" id="appointments">
        <div class="card-header-custom">
            <h3><i class="fas fa-calendar-alt"></i> Patient Appointments</h3>
        </div>
        <div class="card-body-custom">
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <div class="table-responsive">
                    <table class="table table-hover table-custom">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Medical Results</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($row['patient_name']); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <?php echo htmlspecialchars($row['phone']); ?>
                                </td>
                                <td>
                                    <?php if($row['type'] == 'test'): ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-vial me-1"></i>COVID Test
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-syringe me-1"></i>Vaccination
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <?php echo date('M j, Y', strtotime($row['appointment_date'])); ?>
                                </td>
                                <td>
                                    <span class="badge-custom 
                                        <?php echo ($row['status']=='completed') ? 'badge-completed' :
                                                 (($row['status']=='approved') ? 'badge-approved' :
                                                 (($row['status']=='rejected') ? 'badge-rejected' : 'badge-pending')); ?>">
                                        <i class="fas 
                                            <?php echo ($row['status']=='completed') ? 'fa-check' :
                                                     (($row['status']=='approved') ? 'fa-thumbs-up' :
                                                     (($row['status']=='rejected') ? 'fa-times' : 'fa-clock')); ?> me-1"></i>
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>

                                <!-- Status Update Form -->
                                <td>
                                    <form method="post" class="d-flex flex-column gap-2">
                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="pending" <?php if($row['status']=="pending") echo "selected"; ?>>Pending</option>
                                            <option value="approved" <?php if($row['status']=="approved") echo "selected"; ?>>Approved</option>
                                            <option value="rejected" <?php if($row['status']=="rejected") echo "selected"; ?>>Rejected</option>
                                            <option value="completed" <?php if($row['status']=="completed") echo "selected"; ?>>Completed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-custom btn-sm-custom">
                                            <i class="fas fa-sync-alt me-1"></i>Update
                                        </button>
                                    </form>
                                </td>

                                <!-- Medical Results Form -->
                                <td>
                                    <form method="post" class="d-flex flex-column gap-2">
                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                        
                                        <?php if($row['type'] == 'test'): ?>
                                            <select name="test_result" class="form-select form-select-sm">
                                                <option value="not_applicable">N/A</option>
                                                <option value="positive">Positive</option>
                                                <option value="negative">Negative</option>
                                            </select>
                                            <select name="vaccination_status" class="form-select form-select-sm" disabled>
                                                <option value="not_done">Not Applicable</option>
                                            </select>
                                        <?php else: ?>
                                            <select name="test_result" class="form-select form-select-sm" disabled>
                                                <option value="not_applicable">Not Applicable</option>
                                            </select>
                                            <select name="vaccination_status" class="form-select form-select-sm">
                                                <option value="not_done">Not Done</option>
                                                <option value="1st_dose">1st Dose</option>
                                                <option value="2nd_dose">2nd Dose</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        <?php endif; ?>
                                        
                                        <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Doctor remarks...">
                                        <button type="submit" name="update_result" class="btn btn-custom btn-sm-custom">
                                            <i class="fas fa-save me-1"></i>Save Results
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No appointments scheduled yet</h5>
                    <p class="text-muted">Patient appointments will appear here once booked.</p>
                </div>
            <?php } ?>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Smooth scrolling for quick action links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
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