<?php
include('auth_check.php');

// Check if 'id' parameter is passed in the URL
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Prepare SQL query to delete the ticket
    $delete_query = "DELETE FROM tickets WHERE ticket_id = ? AND id_number = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("is", $ticket_id, $_SESSION['id_number']); // Ensure only the correct user can delete their tickets

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Ticket deleted successfully!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Error deleting ticket. Please try again.'); window.location='user.php';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('No ticket selected to delete.'); window.location='user.php';</script>";
}
?>
