<?php
include('auth_check.php');  // Ensure the user is authorized to access this page (Admin only)

// Fetch user role from session (assuming it is stored in the session)
$role = $_SESSION['role'];  // 'user' or 'staff'
$id_number = $_SESSION['id_number'];  // Fetch user ID from session

if (!$id_number) {
    die("User not logged in.");
}

// Check if the logged-in user is an Admin/Staff
if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'support') {
    die("Access denied. Admins only.");
}

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';  // Default to 'Guest' if not logged in

// Initialize filter variable for search term
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query with filters
$query = "SELECT * FROM users WHERE 1";  // 'WHERE 1' is used to make the query more flexible for filters
$params = [];

if ($search) {
    $query .= " AND (id_number LIKE ? OR name LIKE ? OR department LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$query .= " ORDER BY name ASC";  // Optional: Adjust sorting based on your preference

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);  // Bind all parameters
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch all users based on the query
// This query has already been executed above, no need to re-fetch
?>

<?php
$page_title = "User Management";
include('templates/header.php');
?>
            <!-- Filter & Search Section -->
            <div class="filter-section">
                <form method="GET" action="">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Search by ID, name or department..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn apply-btn">Apply Filters</button>
                        <a href="manage_user.php" class="btn reset-filters">Reset</a>
                        <div class="generate-report-btn-container">
                            <a href="generate_report.php" class="btn generate-report-btn">Export Report to CSV</a>
                        </div>
                        <a href="add_user.php" class="btn add-user-btn"><i class="bi bi-person-plus"></i> Add User</a>
                    </div>
                </form>
            </div>

            <!-- User List Table -->
            <div class="recent-tickets">
                <h3>All Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Phone (Ext)</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if any users were found
                        if ($result->num_rows > 0) {
                            while ($user = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo strtoupper($user['id_number']); ?></td>
                                    <td><?php echo ucfirst($user['name']); ?></td>
                                    <td><?php echo strtoupper($user['department']); ?></td>
                                    <td><?php echo $user['phone']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                    <td>
                                        <?php if ($user['status'] == 'active'): ?>
                                            <span class="status-badge status-closed">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-in-progress">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="button-container">
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn edit-user-btn">Update User</a>
                                            <a href="delete_user.php?id_number=<?php echo htmlspecialchars($user['id_number']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')"><i class="bi bi-trash"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                        <?php }
                        } else {
                            // Display message when no records are found
                            echo "<tr><td colspan='8' style='text-align:center;'>No users found</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
<?php include('templates/footer.php'); ?>
