<?php
// Include the PHPMailer class
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoload (If you installed via Composer)
//require 'vendor/autoload.php';  // Adjust path if needed

// Or manually include PHPMailer files (if you downloaded PHPMailer manually)
require 'C:/xampp/htdocs/aduan-rangkaian/PHPMailer-master/PHPMailerAutoload.php';  // Replace with the correct path

// Create a new PHPMailer instance
$mail = new PHPMailer(true);  // Set to true to enable exceptions

try {
    // Server settings
    $mail->isSMTP();  // Use SMTP
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
    $mail->SMTPAuth = true;  // Enable SMTP authentication
    $mail->Username = 'wiltedflower2030@gmail.com';  // Your Gmail email address
    $mail->Password = '00Syumaira00_';  // Your Gmail password or App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
    $mail->Port = 587;  // Port for sending email (TLS)

    // Recipients
    $mail->setFrom('syumaira00@gmail.com', 'Support Team');  // Sender's email and name
    $mail->addAddress('syumairaazhar@example.com', 'User');  // Recipient's email and name

    // Content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a <b>test</b> email sent using PHPMailer!';

    // Send email
    if ($mail->send()) {
        echo 'Message has been sent!';
    } else {
        echo 'Message could not be sent.';
    }
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
