<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['hospital_id'])) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = $_SESSION['hospital_id'];
$msg = "";

// ✅ Update result action
if (isset($_POST['update_result'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $type           = $_POST['type'];

    if ($type == 'test') {
        $test_result = mysqli_real_escape_string($conn, $_POST['test_result']);
        $sql = "INSERT INTO results (appointment_id, test_result) 
                VALUES ($appointment_id, '$test_result')
                ON DUPLICATE KEY UPDATE test_result='$test_result'";
    } else {
        $vaccination_status = mysqli_real_escape_string($conn, $_POST['vaccination_status']);
        $sql = "INSERT INTO results (appointment_id, vaccination_status) 
                VALUES ($appointment_id, '$vaccination_status')
                ON DUPLICATE KEY UPDATE vaccination_status='$vaccination_status'";
    }

    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Result updated successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}

// ✅ Fetch hospital appointments (approved ones only)
$appointments = mysqli_query($conn, "
    SELECT a.id, a.appointment_date, a.type, a.status, u.name as patient_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE a.hospital_id = $hospital_id AND a.status = 'approved'
    ORDER BY a.appointment_date DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hospital - Update Results</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="alert alert-primary text-center mb-4">
        <h4>Welcome, <?php echo htmlspecialchars($_SESSION['hospital_name']); ?> 🏥</h4>
        <p>Update Covid Test Results or Vaccination Status for your patients.</p>
        <a href="hospital_dashboard.php" class="btn btn-secondary btn-sm">⬅ Back to Dashboard</a>
        <a href="hospital_logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-info text-center"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-success text-white">Approved Appointments</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Update Result</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(mysqli_num_rows($appointments) > 0) {
                        while($a = mysqli_fetch_assoc($appointments)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                        <td><?php echo ucfirst($a['type']); ?></td>
                        <td><?php echo $a['appointment_date']; ?></td>
                        <td>
                            <form method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                <input type="hidden" name="appointment_id" value="<?php echo $a['id']; ?>">
                                <input type="hidden" name="type" value="<?php echo $a['type']; ?>">

                                <?php if ($a['type'] == 'test') { ?>
                                    <select name="test_result" class="form-select form-select-sm w-auto" required>
                                        <option value="positive">Positive</option>
                                        <option value="negative">Negative</option>
                                    </select>
                                <?php } else { ?>
                                    <select name="vaccination_status" class="form-select form-select-sm w-auto" required>
                                        <option value="1st_dose">1st Dose</option>
                                        <option value="2nd_dose">2nd Dose</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                <?php } ?>

                                <button type="submit" name="update_result" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="4">No approved appointments found.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
