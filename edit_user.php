<?php
include('auth_check.php');  // Ensure the user is authorized to access this page (Admin only)

if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'support') {
    die("Access denied. Admins only.");
}

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';

// Fetch the user details to edit based on the passed user ID
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("User not found.");
}

// Handle Update User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    // Sanitize form data
    $name = htmlspecialchars($_POST['name']);
    $department = htmlspecialchars($_POST['department']);
    $phone = htmlspecialchars($_POST['phone']);
    $role = htmlspecialchars($_POST['role']);
    $email = htmlspecialchars($_POST['email']);
    $status = htmlspecialchars($_POST['status']);
    $new_password = $_POST['new_password'];  // New password from the form

    // If a new password is provided, hash it
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);  // Hash the new password
    } else {
        // If no new password, use the existing password
        $hashed_password = $user['password'];
    }

    // Update user details
    $edit_query = "UPDATE users SET name = ?, department = ?, phone = ?, role = ?, email = ?, status = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($edit_query);

    $stmt->bind_param("sssssssi", $name, $department, $phone, $role, $email, $status, $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User details updated successfully.'); window.location='manage_user.php';</script>";
    } else {
        echo "<script>alert('Failed to update user details: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

$page_title = "Edit User";
include('templates/header.php');
?>

            <div class="form-box">
                <form method="POST">
                    <h3>Edit User</h3>

                    <label for="id">ID Number</label>
                    <input type="text" name="user_id" value="<?php echo $user['id_number']; ?>" readonly>

                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $user['name']; ?>" required>

                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" value="<?php echo $user['department']; ?>" required>

                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $user['phone']; ?>" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>

                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="staff" <?php echo ($user['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                    </select>

                    <label for="status">Status</label>
                    <select name="status" id="status" required>
                        <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>

                    <label for="password">New Password</label>
                    <input type="password" name="new_password" placeholder="New Password (Leave blank to keep the current password)">

                    <div class="button-container">
                        <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                        <a href="manage_user.php" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>

<?php include('templates/footer.php'); ?>
