<?php

require 'PHPMailer\src\PHPMailer.php';
require 'PHPMailer\src\Exception.php';
require 'PHPMailer\src\SMTP.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoload (if installed via Composer)
require 'vendor/autoload.php';  // If using Composer

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "network_careline";

// Create connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ticket details (from form or URL)
$ticket_id = $_POST['ticket_id'];  // Ticket ID passed through the form or URL
$progress_update = $_POST['progress_update'];  // Progress update from IT support

// Update ticket progress in database
$sql = "UPDATE tickets_testing SET progress_update='$progress_update', status='in progress' WHERE id=$ticket_id";
if ($conn->query($sql) === TRUE) {
    // Fetch user email from the database
    $user_sql = "SELECT users.email FROM tickets INNER JOIN users ON tickets.user_id = users.id WHERE tickets.id = '$ticket_id'";
    $user_result = $conn->query($user_sql);
    $user_email = '';
    if ($user_result->num_rows > 0) {
        $user_email = $user_result->fetch_assoc()['email'];
    }

    // Send progress update email to the user
    sendEmail($user_email, 'Ticket Progress Update', "Your ticket #$ticket_id has been updated: $progress_update.");

    echo "Ticket progress updated and email sent to user.";

} else {
    echo "Error updating ticket: " . $conn->error;
}

$conn->close();

// Function to send email using PHPMailer
function sendEmail($to_email, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration (Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wiltedflower2030@gmail.com';
        $mail->Password = '00Syumaira00_';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('syumairaazhar@gmail.com', 'Support Team');
        $mail->addAddress($to_email);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(true);

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
