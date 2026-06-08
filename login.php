<?php
session_start();

// Centralized database connection details
require_once('db.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Network Careline</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css"> <!-- External CSS file -->
</head>

<body>
    <div class="container-login">
        <div class="login-left">
            <div class="login-brand-panel">
                <img src="logo_BTD.png" alt="BTD Logo" class="brand-illustration">
                <div class="brand-overlay-text">
                    <h2>IT Support Portal</h2>
                    <p>Report issues, track progress, and get network care support quickly and easily.</p>
                </div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-card">
                <div class="login-header">
                    <img src="PKNS.png" alt="PKNS Logo" class="company-logo">
                    <h1>Network Careline</h1>
                    <p>Please enter your credentials to access your account</p>
                </div>

                <div class="login-box">
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="id_number">ID Number</label>
                            <div class="input-wrapper">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" id="id_number" name="id_number" placeholder="Enter your ID" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper">
                                <i class="bi bi-lock input-icon"></i>
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                <span class="eye-icon" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="passwordEyeIcon"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login">Login</button>
                    </form>
                </div>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Get the ID number and password from the form
                    $id_number = $_POST['id_number'];
                    $password = $_POST['password'];

                    // Prepare the SQL query to check the credentials
                    $sql = "SELECT * FROM users WHERE id_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $id_number);  // "s" means the id_number is a string

                    // Execute the query
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Verify password using password_verify
                        if (password_verify($password, $row['password'])) {
                            if ($row['status'] === 'active') {
                                $_SESSION["id_number"] = $row['id_number'];  // Store the user's id_number in the session
                                $_SESSION["name"] = $row['name'];  // Store the user's name in the session
                                $_SESSION["role"] = $row["role"];  // Store the user's role in the session

                                if ($row['role'] == 'staff' || $row['role'] == 'admin' || $row['role'] == 'support') {
                                    header('Location: staff.php');
                                    exit;
                                } elseif ($row['role'] == 'user') {
                                    header('Location: user.php');
                                    exit;
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Your account is inactive. Please contact your administrator.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Incorrect password. Please try again.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Incorrect ID Number. Please try again.</div>";
                    }
                    // Close the statement and the connection
                    $stmt->close();
                    $conn->close();
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // JavaScript function to toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');

            if (passwordField.type === "password") {
                passwordField.type = "text"; // Change password field type to text (visible)
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = "password"; // Change password field type to password (hidden)
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>

</html>