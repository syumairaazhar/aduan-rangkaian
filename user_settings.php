<?php
session_start();
include('auth_check.php');  // Ensure the user is authenticated

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';

// Check if user is logged in
if (!isset($_SESSION['id_number'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$id_number = $_SESSION['id_number']; // Get user ID from session

// Update password functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Fetch current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];

        // Verify the current password using password_verify()
        if (password_verify($current_password, $stored_password)) {

            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in the database with the hashed password
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id_number = ?");
                $update_stmt->bind_param("ss", $hashed_new_password, $id_number);

                // Check if the query executed successfully
                if ($update_stmt->execute()) {
                    echo "<script>alert('Password updated successfully. Please log in again.'); window.location='login.php';</script>";
                    session_destroy(); // Log the user out
                    exit();
                } else {
                    echo "<script>alert('Error updating password in database: " . $update_stmt->error . "');</script>";
                }
            } else {
                echo "<script>alert('New password and confirmation do not match.');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }
    $stmt->close();
}

$page_title = "Settings";
include('templates/header.php');
?>
             <div class="form-box">
                <form method="POST" action="user_settings.php">
                    <h3>Change Password</h3>
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>

                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                </form>
            </div>

<?php include('templates/footer.php'); ?>
