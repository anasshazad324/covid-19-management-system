<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['hospital_id'])) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = $_SESSION['hospital_id'];

// Approve / Reject action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $action         = $_GET['action'];

    if ($action == "approve") {
        mysqli_query($conn, "UPDATE appointments SET status='approved' WHERE id=$appointment_id");
    } elseif ($action == "reject") {
        mysqli_query($conn, "UPDATE appointments SET status='rejected' WHERE id=$appointment_id");
    }
    header("Location: hospital_appointments.php");
    exit();
}

// Fetch requests for this hospital
$appointments = mysqli_query($conn,
    "SELECT a.*, u.name as patient_name 
     FROM appointments a
     JOIN users u ON a.patient_id = u.id
     WHERE a.hospital_id='$hospital_id'
     ORDER BY a.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hospital Appointments</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h4>Appointment Requests</h4>
        <a href="hospital_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($appointments)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo ucfirst($row['type']); ?></td>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td>
                            <?php
                                if ($row['status'] == 'pending')   echo '<span class="badge bg-warning">Pending</span>';
                                elseif ($row['status'] == 'approved') echo '<span class="badge bg-success">Approved</span>';
                                elseif ($row['status'] == 'rejected') echo '<span class="badge bg-danger">Rejected</span>';
                                else echo '<span class="badge bg-info">Completed</span>';
                            ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending') { ?>
                            <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                            <a href="?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Reject</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
