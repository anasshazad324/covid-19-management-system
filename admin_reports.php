<?php
session_start();

// ✅ Match session with login page
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connection.php';

// Export CSV if requested
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=appointments_report.csv');
    $output = fopen('php://output', 'w');

    // CSV header
    fputcsv($output, ['Patient', 'Hospital', 'Type', 'Appointment Date', 'Status', 'Test Result', 'Vaccination Status']);

    $query = "SELECT a.appointment_date, a.type, a.status,
                     p.name AS patient_name,
                     h.name AS hospital_name,
                     r.test_result,
                     r.vaccination_status
              FROM appointments a
              JOIN users p ON a.patient_id = p.id
              JOIN hospitals h ON a.hospital_id = h.id
              LEFT JOIN results r ON a.id = r.appointment_id
              ORDER BY a.appointment_date DESC";

    $rows = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($rows)) {
        fputcsv($output, [
            $row['patient_name'],
            $row['hospital_name'],
            ucfirst($row['type']),
            $row['appointment_date'],
            ucfirst($row['status']),
            ucfirst(str_replace('_',' ', $row['test_result'] ?? 'Pending')),
            ucfirst(str_replace('_',' ', $row['vaccination_status'] ?? 'Pending'))
        ]);
    }
    fclose($output);
    exit();
}

// Fetch appointments for table preview
$appointments = mysqli_query($conn,
    "SELECT a.appointment_date, a.type, a.status,
            p.name AS patient_name,
            h.name AS hospital_name,
            r.test_result,
            r.vaccination_status
     FROM appointments a
     JOIN users p ON a.patient_id = p.id
     JOIN hospitals h ON a.hospital_id = h.id
     LEFT JOIN results r ON a.id = r.appointment_id
     ORDER BY a.appointment_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">COVID Admin Panel</a>
    <div class="d-flex">
      <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm me-2">🏠 Dashboard</a>
      <a href="admin_logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary fw-bold">📊 Appointments Report</h3>
        <a href="?export=csv" class="btn btn-success">⬇ Export CSV</a>
    </div>

    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Patient</th>
                        <th>Hospital</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Test Result</th>
                        <th>Vaccination Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($appointments) > 0) {
                        while ($row = mysqli_fetch_assoc($appointments)) {
                            $status      = ucfirst($row['status']);
                            $test        = ucfirst(str_replace('_',' ',$row['test_result'] ?? 'Pending'));
                            $vaccination = ucfirst(str_replace('_',' ',$row['vaccination_status'] ?? 'Pending'));

                            // ✅ Badge color mapping
                            $badgeClass = match($status) {
                                'Pending'   => 'warning',
                                'Approved'  => 'primary',
                                'Rejected'  => 'danger',
                                'Completed' => 'success',
                                default     => 'secondary'
                            };
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                        <td><?php echo ucfirst($row['type']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($row['appointment_date'])); ?></td>
                        <td><span class="badge bg-<?php echo $badgeClass; ?>"><?php echo $status; ?></span></td>
                        <td><?php echo $test; ?></td>
                        <td><?php echo $vaccination; ?></td>
                    </tr>
                <?php }
                    } else { ?>
                    <tr><td colspan="7">No appointments found.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
