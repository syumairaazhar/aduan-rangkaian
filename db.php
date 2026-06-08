<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "network_careline";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
