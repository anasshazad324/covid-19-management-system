<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$appointments = mysqli_query($conn,
    "SELECT a.*, u.name as hospital_name 
     FROM appointments a 
     JOIN users u ON a.hospital_id = u.id 
     WHERE a.patient_id='$patient_id'
     ORDER BY a.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Appointments</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .hero {
      background: linear-gradient(135deg, #0d6efd, #20c997);
      color: white;
      padding: 60px 20px;
      text-align: center;
      border-radius: 0 0 20px 20px;
      margin-bottom: 40px;
    }
    .appointment-card {
      border-radius: 15px;
      transition: 0.3s;
      border: none;
    }
    .appointment-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .status-badge {
      font-size: 0.85rem;
      padding: 6px 12px;
      border-radius: 20px;
    }
  </style>
</head>
<body>

<!-- Hero Banner -->
<div class="hero">
  <h1>🗓️ My Appointments</h1>
  <p class="lead">Track all your Covid Test & Vaccination appointments here</p>
  <a href="patient_dashboard.php" class="btn btn-light mt-2">⬅ Back to Dashboard</a>
</div>

<div class="container">
  <div class="row g-4">
    <?php if (mysqli_num_rows($appointments) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($appointments)) { ?>
        <div class="col-md-6 col-lg-4">
          <div class="card appointment-card shadow-sm p-3 h-100">
            <div class="card-body">
              <h5 class="card-title mb-3"><?php echo htmlspecialchars($row['hospital_name']); ?></h5>
              <p class="mb-1"><strong>Type:</strong> <?php echo ucfirst($row['type']); ?></p>
              <p class="mb-1"><strong>Date:</strong> <?php echo $row['appointment_date']; ?></p>
              <p class="mb-2">
                <strong>Status:</strong> 
                <?php
                  if ($row['status'] == 'pending')   
                    echo '<span class="status-badge bg-warning text-dark">⏳ Pending</span>';
                  elseif ($row['status'] == 'approved') 
                    echo '<span class="status-badge bg-success">✅ Approved</span>';
                  elseif ($row['status'] == 'rejected') 
                    echo '<span class="status-badge bg-danger">❌ Rejected</span>';
                  else 
                    echo '<span class="status-badge bg-info">🎉 Completed</span>';
                ?>
              </p>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php else: ?>
      <div class="col-12 text-center">
        <p class="lead text-muted">No appointments found yet. Book one from your dashboard!</p>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
