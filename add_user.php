<?php
include('auth_check.php');  // Ensure the user is authorized to access this page (Admin only)

// Check if the logged-in user is an Admin/Staff
if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'support') {
    die("Access denied. Admins only.");
}

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    // Collect form data and sanitize inputs
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = 'active'; // Default status is active

    // Check if the email already exists
    $check_email_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email is already taken. Please choose a different email.');</script>";
    } else {
        // Insert the new user into the database
        $add_query = "INSERT INTO users (id_number, name, department, phone, email, password, role, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($add_query);
        $stmt->bind_param("sssissss", $id_number, $name, $department, $phone, $email, $password, $role, $status);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('User added successfully.'); window.location='manage_user.php';</script>";
    }
}

$page_title = "Add User";
include('templates/header.php');
?>

            <div class="form-box">
                <form method="POST">
                    <h3>Add User</h3>
                    <label>ID Number</label>
                    <input type="text" name="id_number" placeholder="ID Number" required>
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Name" required>
                    <label>Department</label>
                    <input type="text" name="department" placeholder="Department" required>
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="Phone" required>
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email" required>
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                    <label>Role</label>
                    <select name="role" required>
                        <option value="user">User</option>
                        <option value="staff">Staff</option>
                    </select>
                    <div class="button-container">
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        <a href="manage_user.php" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>

<?php include('templates/footer.php'); ?>