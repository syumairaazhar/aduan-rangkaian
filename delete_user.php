<?php
include('auth_check.php');

// Check if 'id_number' parameter is passed in the URL
if (isset($_GET['id_number'])) {
    $id_number = $_GET['id_number'];

    // Check if the user is authorized (only admins should be able to delete users)
    if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'support') {
        echo "<script>alert('Access denied. You are not authorized to delete users.'); window.location='manage_user.php';</script>";
        exit();
    }

    // Prepare SQL query to delete the user
    $delete_query = "DELETE FROM users WHERE id_number = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $id_number);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location='manage_user.php';</script>";
    } else {
        echo "<script>alert('Error deleting user. Please try again.'); window.location='manage_user.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('No user selected to delete.'); window.location='manage_user.php';</script>";
}
?>
