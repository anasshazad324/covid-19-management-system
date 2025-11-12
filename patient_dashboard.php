<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$msg = "";

// ✅ Book appointment action
if (isset($_POST['book'])) {
    $hospital_id = intval($_POST['hospital_id']);
    $type        = mysqli_real_escape_string($conn, $_POST['type']);
    $date        = mysqli_real_escape_string($conn, $_POST['appointment_date']);

    $sql = "INSERT INTO appointments (patient_id, hospital_id, type, appointment_date, status)
            VALUES ('$patient_id', '$hospital_id', '$type', '$date', 'pending')";
    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Appointment booked successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}

// ✅ Fetch approved hospitals for dropdown
$hospitals = mysqli_query($conn, "SELECT id, name FROM users WHERE role='hospital' AND status='approved'");

// ✅ Fetch patient appointments
$appointments = mysqli_query($conn,
    "SELECT a.appointment_date, a.type, a.status, h.name AS hospital_name
     FROM appointments a
     JOIN users h ON a.hospital_id = h.id
     WHERE a.patient_id = '$patient_id'
     ORDER BY a.appointment_date DESC");

// ✅ Fetch patient results
$results = mysqli_query($conn, "
    SELECT a.appointment_date, a.type, h.name AS hospital_name, r.test_result, r.vaccination_status
    FROM results r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN users h ON a.hospital_id = h.id
    WHERE a.patient_id = '$patient_id'
    ORDER BY a.appointment_date DESC
");

// Get stats for dashboard
$total_appointments = mysqli_num_rows($appointments);
$completed_appointments = mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE patient_id = '$patient_id' AND status='completed'");
$completed_count = mysqli_fetch_assoc($completed_appointments)['count'];
mysqli_data_seek($appointments, 0); // Reset pointer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard | HealthConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
            --gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #fdf2ff 100%);
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
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
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
        
        .stat-card.appointments {
            border-left-color: var(--info);
        }
        
        .stat-card.completed {
            border-left-color: var(--success);
        }
        
        .stat-card.pending {
            border-left-color: var(--warning);
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
        
        .stat-card.appointments .stat-icon {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }
        
        .stat-card.completed .stat-icon {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .stat-card.pending .stat-icon {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
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
        
        /* Form Styles */
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .btn-custom {
            background: var(--gradient);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
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
        
        .badge-custom {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        
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
                    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['patient_name']); ?>! 👋</h1>
                    <p>Manage your health appointments and results in one place</p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="patient_logout.php" class="btn btn-light btn-lg">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card appointments">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number"><?php echo $total_appointments; ?></div>
            <div class="stat-label">Total Appointments</div>
        </div>
        
        <div class="stat-card completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo $completed_count; ?></div>
            <div class="stat-label">Completed Appointments</div>
        </div>
        
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number"><?php echo $total_appointments - $completed_count; ?></div>
            <div class="stat-label">Pending Appointments</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="#book-appointment" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-text">Book Appointment</div>
        </a>
        
        <a href="#my-appointments" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <div class="action-text">View Appointments</div>
        </a>
        
        <a href="#my-results" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-file-medical"></i>
            </div>
            <div class="action-text">Check Results</div>
        </a>
        
        <a href="#" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-download"></i>
            </div>
            <div class="action-text">Download Reports</div>
        </a>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-info alert-custom">
            <i class="fas fa-info-circle me-2"></i><?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <!-- Book Appointment Section -->
    <div class="section-card" id="book-appointment">
        <div class="card-header-custom">
            <h3><i class="fas fa-calendar-plus"></i> Book New Appointment</h3>
        </div>
        <div class="card-body-custom">
            <form method="POST" class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Select Hospital</label>
                    <select name="hospital_id" class="form-select" required>
                        <option value="">-- Choose Hospital --</option>
                        <?php while($h = mysqli_fetch_assoc($hospitals)) { ?>
                            <option value="<?php echo $h['id']; ?>">
                                <?php echo htmlspecialchars($h['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Appointment Type</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Choose Type --</option>
                        <option value="test">🦠 COVID Test</option>
                        <option value="vaccination">💉 Vaccination</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Appointment Date</label>
                    <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" name="book" class="btn btn-custom">
                        <i class="fas fa-calendar-check me-2"></i>Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- My Appointments Section -->
    <div class="section-card" id="my-appointments">
        <div class="card-header-custom">
            <h3><i class="fas fa-list-ul"></i> My Appointments</h3>
        </div>
        <div class="card-body-custom">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Hospital</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($appointments) > 0) {
                            while($a = mysqli_fetch_assoc($appointments)) { ?>
                        <tr>
                            <td>
                                <i class="fas fa-hospital me-2 text-muted"></i>
                                <?php echo htmlspecialchars($a['hospital_name']); ?>
                            </td>
                            <td>
                                <?php if($a['type'] == 'test'): ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-vial me-1"></i>COVID Test
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-syringe me-1"></i>Vaccination
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($a['appointment_date'])); ?></td>
                            <td>
                                <span class="badge-custom 
                                    <?php echo ($a['status']=='completed') ? 'badge-completed' :
                                             (($a['status']=='approved') ? 'badge-approved' : 'badge-pending'); ?>">
                                    <i class="fas 
                                        <?php echo ($a['status']=='completed') ? 'fa-check' :
                                                 (($a['status']=='approved') ? 'fa-thumbs-up' : 'fa-clock'); ?> me-1"></i>
                                    <?php echo ucfirst($a['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php } } else { ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No appointments booked yet.</p>
                                <a href="#book-appointment" class="btn btn-custom btn-sm">Book Your First Appointment</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- My Results Section -->
    <div class="section-card" id="my-results">
        <div class="card-header-custom">
            <h3><i class="fas fa-file-medical-alt"></i> My Test Results & Vaccination Status</h3>
        </div>
        <div class="card-body-custom">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th>Hospital</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Test Result</th>
                            <th>Vaccination Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(mysqli_num_rows($results) > 0) {
                            while($r = mysqli_fetch_assoc($results)) { ?>
                        <tr>
                            <td>
                                <i class="fas fa-hospital me-2 text-muted"></i>
                                <?php echo htmlspecialchars($r['hospital_name']); ?>
                            </td>
                            <td>
                                <?php if($r['type'] == 'test'): ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-vial me-1"></i>COVID Test
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-syringe me-1"></i>Vaccination
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($r['appointment_date'])); ?></td>
                            <td>
                                <?php 
                                if ($r['type'] == 'test') {
                                    $result = $r['test_result'] ?: 'Pending';
                                    $badge_class = $result == 'Positive' ? 'bg-danger' : ($result == 'Negative' ? 'bg-success' : 'bg-secondary');
                                    echo '<span class="badge ' . $badge_class . '">' . $result . '</span>';
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if ($r['type'] == 'vaccination') {
                                    $status = $r['vaccination_status'] ?: 'Pending';
                                    $badge_class = $status == 'Completed' ? 'bg-success' : ($status == 'Scheduled' ? 'bg-info' : 'bg-secondary');
                                    echo '<span class="badge ' . $badge_class . '">' . $status . '</span>';
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } } else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-file-medical fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No results available yet.</p>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
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
    
    // Set minimum date for appointment booking to today
    document.querySelector('input[name="appointment_date"]').min = new Date().toISOString().split('T')[0];
</script>

</body>
</html>