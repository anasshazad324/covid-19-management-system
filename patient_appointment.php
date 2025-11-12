<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}

$msg = "";

// Appointment submit
if (isset($_POST['book'])) {
    $hospital_id      = intval($_POST['hospital_id']);
    $type             = mysqli_real_escape_string($conn, $_POST['type']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $patient_id       = $_SESSION['patient_id'];

    $sql = "INSERT INTO appointments (patient_id, hospital_id, type, appointment_date, status)
            VALUES ('$patient_id', '$hospital_id', '$type', '$appointment_date', 'pending')";
    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Appointment request sent successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}

// Fetch approved hospitals
$hospitals = mysqli_query($conn, "SELECT id, name FROM users WHERE role='hospital' AND status='approved'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h4>Book Appointment</h4>
        <a href="patient_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($msg != ""): ?>
        <div class="alert alert-info"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label>Select Hospital</label>
                    <select name="hospital_id" class="form-control" required>
                        <option value="">-- Select Hospital --</option>
                        <?php while ($h = mysqli_fetch_assoc($hospitals)) { ?>
                            <option value="<?php echo $h['id']; ?>">
                                <?php echo htmlspecialchars($h['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Appointment Type</label>
                    <select name="type" class="form-control" required>
                        <option value="">-- Select Type --</option>
                        <option value="test">COVID Test</option>
                        <option value="vaccination">Vaccination</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Appointment Date</label>
                    <input type="date" name="appointment_date" class="form-control" required>
                </div>

                <button type="submit" name="book" class="btn btn-primary w-100">Book Appointment</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
