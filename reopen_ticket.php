<?php
session_start();
include('auth_check.php');

// Check if ticket ID is provided in the URL
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    
    // Sanitize input to prevent SQL injection
    $ticket_id = mysqli_real_escape_string($conn, $ticket_id);

    // Update the ticket status to 'reopen'
    $query = "UPDATE tickets SET status = 'reopen' WHERE ticket_id = '$ticket_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Ticket reopened successfully!'); window.location='user.php';</script>";
    } else {
        echo "Error reopening the ticket: " . mysqli_error($conn);
    }
} else {
    echo "Ticket ID not provided.";
}
?>
