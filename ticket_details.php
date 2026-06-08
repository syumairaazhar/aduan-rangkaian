<?php
session_start();  // Start the session at the beginning of the file
include('auth_check.php');  // Ensure the user is authenticated

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);



// Check if ticket ID is provided in the URL
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];  // Get ticket ID

    // Sanitize input to prevent SQL injection
    $ticket_id = mysqli_real_escape_string($conn, $ticket_id);

    // Fetch ticket details from the database
    $query = "SELECT * FROM tickets WHERE ticket_id = '$ticket_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $ticket = mysqli_fetch_assoc($result);  // Get ticket data
    } else {
        echo "Ticket not found.";
    }
} else {
    echo "Ticket ID not provided.";
}
?>

<?php
$page_title = "Ticket Details";
include('templates/header.php');
?>

            <div class="form-box">
                <?php if (isset($ticket)): ?>
                    <form>
                        <h3>Ticket Details</h3>

                        <label for="issue">Issue Category</label>
                        <input type="text" id="issue" name="issue" value="<?php echo ucfirst(htmlspecialchars($ticket['category'])); ?>" readonly>

                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" readonly><?php echo ucfirst(htmlspecialchars($ticket['description'])); ?></textarea>

                        <label for="status">Status</label>
                        <input type="text" id="status" name="status" value="<?php echo ucfirst(htmlspecialchars($ticket['status'])); ?>" readonly>
                        
                        <div class="button-container">
                            <a href="user.php" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
<?php
include('templates/footer.php');
?>