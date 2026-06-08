<?php

session_start();  // Start the session at the beginning of the file
include('auth_check.php');  // Ensure the user is authenticated

// Fetch user ID and name from session
$id_number = isset($_SESSION['id_number']) ? $_SESSION['id_number'] : null;  // Fetch user ID from session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : null;  // Fetch user name from session

// Check if user ID and name are available
if (!$id_number || !$user_name) {
    echo "<script>alert('User not logged in.'); window.location='login.php';</script>";
    exit();
}



// Fetch user details from the database
$query = "SELECT * FROM users WHERE id_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id_number); // Bind user ID from session
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_details = $result->fetch_assoc();
    $phone = $user_details['phone'];  // Fetch phone from database
    $department = $user_details['department'];  // Fetch department from database
    $user_email = $user_details['email'];  // Fetch user email from the database
} else {
    $phone = '';
    $department = '';
    $user_email = '';  // Default empty email if not found
}
$stmt->close();

// Fetch categories from the database to populate the dropdown
$categories_query = "SELECT * FROM ticket_category";
$categories_result = $conn->query($categories_query);

// Handle form submission for creating a new ticket
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ticket'])) {
    // Get form data
    $category = $_POST['category'];  // Get selected category
    $description = $_POST['description'];
    $location = $_POST['location'];
    $phone = $_POST['phone']; // User's phone (in case of edit)
    $department = $_POST['department']; // User's department (in case of edit)

    // File Upload Handling
    $attachment = NULL;  // Initialize attachment as NULL by default
    if (!empty($_FILES['attachment']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }

        $attachment = $target_dir . basename($_FILES["attachment"]["name"]);

        // Optionally, validate file type and size
        $allowed_types = ['image/jpg', 'image/png', 'application/pdf']; // Example allowed types
        if (in_array($_FILES['attachment']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment)) {
                echo "File uploaded successfully.";
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, PNG, and PDF are allowed.');</script>";
        }
    }

    // Insert the new ticket into the database
    $stmt = $conn->prepare("INSERT INTO tickets (id_number, name, department, phone, category, description, attachment, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $id_number, $user_name, $department, $phone, $category, $description, $attachment, $location);

    if ($stmt->execute()) {
        $ticket_id = $conn->insert_id;  // Get the last inserted ticket ID
        echo "<script>alert('Ticket created successfully! Ticket ID: " . $ticket_id . "');</script>";

        // Admin's Email
        $admin_email = 'ictsppt@gmail.com';  // IT Admin's email address

        // Display notification for both user and admin
        echo "<script type='text/javascript'>
                alert('Ticket ID: " . $ticket_id . " has been submitted successfully!');
                alert('Admin (IT Support) has been notified.');
              </script>";

        // Redirect user back to their ticket list or dashboard
        echo "<script type='text/javascript'>window.location = 'user.php';</script>"; // Redirect to the user page

    } else {
        echo "<script>alert('Error creating ticket. Please try again.');</script>";
    }

    // Close the prepared statement
    $stmt->close();
}
?>
<?php
$page_title = "Create Ticket";
include('templates/header.php');
?>
            <div class="form-box">
                <form method="POST" enctype="multipart/form-data">
                    <h3>Create Ticket</h3>

                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>

                    <label>Department</label>
                    <input type="text" name="department" value="<?php echo htmlspecialchars($department); ?>" readonly>

                    <label>Phone (Ext)</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

                    <label>Issue Category <span style="color: var(--color-danger);">*</span></label>
                    <select name="category" required>
                        <?php
                        while ($row = $categories_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['category_name']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                        }
                        ?>
                    </select>

                    <label>Description (Optional)</label>
                    <textarea name="description" placeholder="Provide more details about the issue..."></textarea>

                    <label>Attach File (Optional)</label>
                    <input type="file" name="attachment">

                    <label>Location <span style="color: var(--color-danger);">*</span></label>
                    <input type="text" name="location" required placeholder="e.g. Block A, Level 3">

                    <p><span style="color: var(--color-danger);">*</span> Required field</p>

                    <div class="button-container">
                        <button type="submit" name="submit_ticket" class="btn btn-primary">Submit Ticket</button>
                        <a href="user.php" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>
<?php
include('templates/footer.php');
?>
