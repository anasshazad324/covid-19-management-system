<?php
session_start();
include 'connection.php';
include 'includes/header.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

$results = mysqli_query($conn,
    "SELECT a.appointment_date, a.type, u.name as hospital_name,
            r.test_result, r.vaccination_status, r.remarks
     FROM appointments a
     JOIN users u ON a.hospital_id = u.id
     LEFT JOIN results r ON a.id = r.appointment_id
     WHERE a.patient_id='$patient_id'
     ORDER BY a.appointment_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Test/Vaccination Results</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h4>My Results</h4>
        <a href="patient_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Hospital</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Test Result</th>
                        <th>Vaccination Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($results)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                        <td><?php echo ucfirst($row['type']); ?></td>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td><?php echo ucfirst(str_replace('_',' ',$row['test_result'] ?? 'Pending')); ?></td>
                        <td><?php echo ucfirst(str_replace('_',' ',$row['vaccination_status'] ?? 'Pending')); ?></td>
                        <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


</body>
</html>
