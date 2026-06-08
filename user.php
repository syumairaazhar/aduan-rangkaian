<?php
//session_start();

include('auth_check.php');

$id_number = $_SESSION['id_number'];  // Fetch user ID from session

if (!$id_number) {
    die("User not logged in.");
}

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';  // Default to 'Guest' if not logged in

// Initialize filter variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build the query with filters
$query = "SELECT * FROM tickets WHERE id_number = ?";
$params = [$id_number];

if ($search) {
    $query .= " AND category LIKE ?";
    $params[] = '%' . $search . '%';
}

if ($status_filter) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if ($date_filter) {
    $query .= " AND DATE(last_updated) = ?";
    $params[] = $date_filter;
}

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);  // Ensure correct data type for parameters
$stmt->execute();
$result = $stmt->get_result();

// Fetch distinct statuses for filter dropdown
$status_query = "SELECT DISTINCT status FROM tickets WHERE id_number = ?";
$status_stmt = $conn->prepare($status_query);
$status_stmt->bind_param("s", $id_number);  // Use "s" for string since id_number is alphanumeric
$status_stmt->execute();
$status_result = $status_stmt->get_result();
?>

<?php
$page_title = "My Tickets";
include('templates/header.php');
?>

            <!-- Filter & Search Section -->
            <div class="filter-section">
                <form method="GET" action="">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Search by issue..." value="<?php echo htmlspecialchars($search); ?>">
                        <select name="status">
                            <option value="">📌 Filter by Status</option>
                            <?php while ($row = $status_result->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($row['status']); ?>" <?php echo ($status_filter == $row['status']) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn apply-btn">Apply Filters</button>
                        <a href="user.php" class="btn reset-filters">Reset</a>
                        <a href="create_ticket.php" class="btn add-ticket-btn"><i class="bi bi-plus-lg"></i> Add Ticket</a>
                    </div>
                </form>
            </div>

            <!-- Tickets Table -->
            <div class="recent-tickets">
                <h3>My Tickets</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Issue Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th> <!-- New column for created_at -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;  // Initialize counter for auto-numbering
                        if ($result->num_rows > 0) {
                            while ($tickets = $result->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td> <!-- Auto number column -->
                                    <td><?php echo htmlspecialchars($tickets['category']); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($tickets['description'])); ?></td>
                                    <td>
                                        <?php 
                                        $status_class = 'status-' . str_replace(' ', '-', strtolower($tickets['status']));
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($tickets['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($tickets['last_updated']); ?></td> <!-- Display the creation date -->
                                    <td>
                                        <div class="button-container">
                                            <!-- Modified View link to look like a button -->
                                            <a href="ticket_details.php?id=<?php echo htmlspecialchars($tickets['ticket_id']); ?>" class="view-btn"><i class="bi bi-eye"></i> View</a>

                                            <?php if ($tickets['status'] == 'closed') { ?>
                                                <a href="reopen_ticket.php?id=<?php echo htmlspecialchars($tickets['ticket_id']); ?>" class="reopen-btn"><i class="bi bi-arrow-repeat"></i> Reopen Ticket</a>
                                            <?php } ?>

                                            <!-- Delete Ticket Button -->
                                            <a href="delete_ticket.php?id=<?php echo htmlspecialchars($tickets['ticket_id']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this ticket?')"><i class="bi bi-trash"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                        <?php }
                        } else {
                            // Display message when no records are found
                            echo "<tr><td colspan='6' style='text-align:center;'>No records found</td></tr>";
                        } ?>
                    </tbody>

                </table>
            </div>
<?php
include('templates/footer.php');
?>