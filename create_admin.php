<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "network_careline";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin user details
$id_number = "ADMIN001";
$name = "System Admin";
$department = "IT Support";
$phone = "0123456789";
$email = "admin@example.com";
$raw_password = "admin123"; // Default password
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
$role = "admin";
$status = "active";

// Check if user already exists
$check_query = "SELECT * FROM users WHERE email = ? OR id_number = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ss", $email, $id_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "User with this Email or ID Number already exists.";
} else {
    // Insert new admin
    $sql = "INSERT INTO users (id_number, name, department, phone, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $id_number, $name, $department, $phone, $email, $hashed_password, $role, $status);

    if ($stmt->execute()) {
        echo "Admin user created successfully.<br>";
        echo "Email: $email<br>";
        echo "Password: $raw_password<br>";
        echo "ID Number: $id_number<br>"; 
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$stmt->close();
$conn->close();
?>
