<?php
include('auth_check.php');  // Ensure only admins have access to this page

// Fetch user name from the session
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    // Check if the category already exists in the database
    $check_query = "SELECT * FROM ticket_category WHERE category_name = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Category already exists. Please choose a different category name.');</script>";
    } else {
        // Insert the new category into the database if it doesn't exist
        $add_query = "INSERT INTO ticket_category (category_name) VALUES (?)";
        $stmt = $conn->prepare($add_query);
        $stmt->bind_param("s", $category_name);
        if ($stmt->execute()) {
            echo "<script>alert('Category added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding category. Please try again.');</script>";
        }
    }

    $stmt->close();
}

$page_title = "Add Category";
include('templates/header.php');
?>

             <div class="form-box">
                <form method="POST">
                    <h3>Add New Issue Category</h3>
                    <label for="category_name">Category Name</label>
                    <input type="text" name="category_name" id="category_name" required placeholder="Enter category name">
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </form>
            </div>

<?php include('templates/footer.php'); ?>
