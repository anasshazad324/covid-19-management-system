<?php
// Simple Database Connection File

$servername = "localhost";   // Default XAMPP host
$username   = "root";        // Default MySQL user
$password   = "";            // Default password (XAMPP me khaali hoti hai)
$dbname     = "covid_system"; // Aapka database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional success message (test ke liye)
// echo "Database connected successfully!";
?>
