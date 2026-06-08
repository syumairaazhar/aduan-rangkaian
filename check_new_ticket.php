<?php
session_start();
include('auth_check.php');

// Check if there is any new ticket submitted
$query = "SELECT * FROM tickets ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $latest_ticket = $result->fetch_assoc();
    echo "new_ticket";
} else {
    echo "no_new_ticket";
}

$conn->close();
?>
