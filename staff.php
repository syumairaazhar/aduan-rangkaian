<?php
include('auth_check.php');  // Ensure the user is authorized to access this page

// Fetch user role from session (assuming it is stored in the session)
$role = $_SESSION['role'];  // 'user' or 'staff'
$id_number = $_SESSION['id_number'];  // Fetch user ID from session

if (!$id_number) {
    die("User not logged in.");
}

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';  // Default to 'Guest' if not logged in

// Initialize filter variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build the query with filters
$query = "SELECT * FROM tickets WHERE 1";  // 'WHERE 1' is used to make the query more flexible for filters
$params = [];

// Filter by user tickets if not staff
if ($role != 'staff') {  // If the user is not a staff, filter by their own tickets
    $query .= " AND id_number = ?";  // Only fetch tickets for the logged-in user
    $params[] = $id_number;  // Bind the id_number for non-staff users
}

// If search is provided, filter by category or id_number
if ($search) {
    // If search term is alphanumeric (ID number), search by id_number
    if (preg_match('/^[a-zA-Z0-9]+$/', $search)) {
        $query .= " AND id_number LIKE ?";
        $params[] = '%' . $search . '%';  // Search by ID number
    } else {
        // If search term is string (category), search by category
        $query .= " AND category LIKE ?";
        $params[] = '%' . $search . '%';  // Search by category
    }
}

// Filter by status
if ($status_filter) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

// Filter by category
if ($category_filter) {
    $query .= " AND category = ?";
    $params[] = $category_filter;
}

// Filter by date
if ($date_filter) {
    $query .= " AND DATE(last_updated) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY last_updated DESC"; // Order by last updated

// Ensure that params are not empty before calling bind_param
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    // Bind all parameters (adjust the number of 's' based on the number of params)
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Handle case when no parameters are set (just fetch all tickets)
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch distinct categories for filter dropdown
$category_query = "SELECT DISTINCT category FROM tickets";
$category_stmt = $conn->prepare($category_query);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

// Fetch ticket statistics
$ticket_stats_query = "SELECT 
            COUNT(*) AS total_tickets,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS pending_tickets,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) AS closed_tickets,
            SUM(CASE WHEN status = 'reopen' THEN 1 ELSE 0 END) AS reopen_tickets,
            SUM(CASE WHEN status = 'in progress' THEN 1 ELSE 0 END) AS inprogress_issues
          FROM tickets";
$ticket_stats_stmt = $conn->prepare($ticket_stats_query);
$ticket_stats_stmt->execute();
$ticket_stats = $ticket_stats_stmt->get_result()->fetch_assoc();

// Handle Update Ticket Status (Form Submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];  // Get ticket ID
    $new_status = $_POST['status'];    // Get new status

    // Update the ticket status in the database
    $update_query = "UPDATE tickets SET status = ? WHERE ticket_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $ticket_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        echo "<script>alert('Ticket status updated successfully.'); window.location='staff.php';</script>"; // Redirect after success
    } else {
        echo "<script>alert('Error updating ticket status.');</script>";
    }
    $update_stmt->close();
}
?>

<?php
$page_title = "Dashboard";
include('templates/header.php');
?>
<div id="notificationArea"></div>

<!-- Quick Summary Cards -->
<div class="summary-cards">
    <div class="card total-tickets" onclick="filterTickets('')">
        <h3>Total Tickets</h3>
        <p><?php echo $ticket_stats['total_tickets']; ?></p>
    </div>
    <div class="card pending-tickets" onclick="filterTickets('new')">
        <h3>Pending Tickets</h3>
        <p><?php echo $ticket_stats['pending_tickets']; ?></p>
    </div>
    <div class="card reopen-tickets" onclick="filterTickets('reopen')">
        <h3>Reopen Tickets</h3>
        <p><?php echo $ticket_stats['reopen_tickets']; ?></p>
    </div>
    <div class="card inprogress-issues" onclick="filterTickets('in progress')">
        <h3>In Progress Tickets</h3>
        <p><?php echo $ticket_stats['inprogress_issues']; ?></p>
    </div>
    <div class="card closed-tickets" onclick="filterTickets('closed')">
        <h3>Closed Tickets</h3>
        <p><?php echo $ticket_stats['closed_tickets']; ?></p>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="filter-section">
    <form method="GET" action="">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Search by ID..." value="<?php echo htmlspecialchars($search); ?>">

            <select name="category" class="long-search-box">
                <option value="">📌 Filter by Category</option>
                <?php while ($row = $category_result->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($row['category']); ?>" <?php echo ($category_filter == $row['category']) ? 'selected' : ''; ?>>
                        <?php echo ucfirst(htmlspecialchars($row['category'])); ?>
                    </option>
                <?php } ?>
            </select>

            <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
        </div>
        <div class="button-group">
            <button type="submit" class="btn apply-btn">Apply Filters</button>
            <a href="staff.php" class="btn reset-filters">Reset</a>
            <!-- Export Report Button -->
            <div class='generate-report-btn-container'>
                <a href="generate_report.php?<?php echo http_build_query($_GET); ?>" class="btn generate-report-btn">Export Report to CSV</a>
            </div>
        </div>
    </form>
</div>

<!-- Recent Tickets Table -->
<div class="recent-tickets">
    <h3>Recent Tickets</h3><br>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Ticket ID</th>
                <th>Issued By</th>
                <th>Issue Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Status</th>
                <th>Last Updated</th>
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
                        <td><?php echo htmlspecialchars($tickets['ticket_id']); ?></td>
                        <td><?php echo strtoupper(htmlspecialchars($tickets['id_number'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($tickets['category'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($tickets['description'])); ?></td>
                        <td><?php echo htmlspecialchars($tickets['location']); ?></td>
                        <td>
                            <?php
                            $status_class = 'status-' . str_replace(' ', '-', strtolower($tickets['status']));
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($tickets['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($tickets['last_updated']); ?></td>
                        <td>
                            <!-- Update Status Form -->
                            <form method="POST" style="display: flex; align-items: center;">
                                <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($tickets['ticket_id']); ?>">
                                <select name="status" required style="margin-right: 1px;">
                                    <option value="new" <?php echo ($tickets['status'] == 'new') ? 'selected' : ''; ?>>New</option>
                                    <option value="reopen" <?php echo ($tickets['status'] == 'reopen') ? 'selected' : ''; ?>>Reopen</option>
                                    <option value="in progress" <?php echo ($tickets['status'] == 'in progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="closed" <?php echo ($tickets['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
                                </select>
                                <button type="submit" name="update_status" style="padding: 5px 20px; background-color: orange; color: white; border: none; border-radius: 5px; cursor: pointer;">Update</button>
                            </form>
                        </td>
                    </tr>
            <?php }
            } else {
                // Display message when no records are found
                echo "<tr><td colspan='9' style='text-align:center;'>No records found</td></tr>";
            } ?>
        </tbody>
    </table>
</div>
<script>
    // Function to filter tickets based on status and category
    function filterTickets(status) {
        const category = document.querySelector('select[name="category"]').value;
        const date = document.querySelector('input[name="date"]').value;
        const search = document.querySelector('input[name="search"]').value;

        let url = window.location.href.split('?')[0]; // Get the base URL

        // Build query string based on selected filters
        let params = [];

        if (search) {
            params.push('search=' + encodeURIComponent(search));
        }

        if (category) {
            params.push('category=' + encodeURIComponent(category));
        }

        if (status) {
            params.push('status=' + encodeURIComponent(status));
        }

        if (date) {
            params.push('date=' + encodeURIComponent(date));
        }

        // Update the URL with the selected filters
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        // Redirect to the new URL with the filters applied
        window.location.href = url;
    }

    // JavaScript to fetch notifications for new tickets
    let lastTicketId = localStorage.getItem("lastTicketId") || 0;

    function fetchNewTickets() {
        $.ajax({
            url: "check_new_ticket.php",
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data.latest_ticket_id > lastTicketId) {
                    if (lastTicketId != 0) {
                        alert("New ticket submitted!");
                    }

                    lastTicketId = data.latest_ticket_id;
                    localStorage.setItem("lastTicketId", lastTicketId);
                }
            }
        });
    }

    fetchNewTickets();
    setInterval(fetchNewTickets, 5000);

    // Start polling for new tickets every 5 seconds
    setInterval(fetchNewTickets, 5000);
</script>
<?php
include('templates/footer.php');
?>