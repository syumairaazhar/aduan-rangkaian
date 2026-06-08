<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Start session only if it hasn't been started already
}

// Check if user is logged in
if (!isset($_SESSION['id_number'])) {
    header("Location: login.php");
    exit();
}

// Include centralized database connection
require_once('db.php');

// Fetch user data from session
$user_id = $_SESSION['id_number']; 

// Verify if user exists in the database. Bind as "s" because id_number is alphanumeric.
$query = $conn->prepare("SELECT * FROM users WHERE id_number = ?");
$query->bind_param("s", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: login.php?error=invalid_session");
    exit();
}

// Fetch user details
$user = $result->fetch_assoc();

// Check if the user is authorized
if ($user['role'] === 'staff' || $user['role'] === 'admin' || $user['role'] === 'support') {
    // Admin/Staff/Support actions
} elseif ($user['role'] === 'user') {
    // Regular user actions
} else {
    session_destroy();
    header("Location: login.php?error=invalid_role");
    exit();
}
?>
