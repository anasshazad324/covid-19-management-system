<?php
// reset_admin_password.php  (temporary) - DELETE after use


$user = 'root';
$pass = '';        // agar aapka DB password hai to yahan daal den
$db   = 'covid_system';

// New admin password (change if you want)
$new_password_plain = 'Admin@123';   // <- aap yahan apna naya password rakh sakte ho

// Admin identifier (email) - change if your admin email different
$admin_email = 'admin@covid.com';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connect error: " . $conn->connect_error);
}

// Hash the password using bcrypt
$hash = password_hash($new_password_plain, PASSWORD_BCRYPT);

$sql = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('ss', $hash, $admin_email);
if ($stmt->execute()) {
    echo "✅ Admin password updated for $admin_email\n";
    echo "Use this to login: $admin_email / $new_password_plain\n";
} else {
    echo "❌ Update failed: " . $stmt->error;
}
$stmt->close();
$conn->close();

// IMPORTANT: After confirming login works, DELETE this file from server.
?>
