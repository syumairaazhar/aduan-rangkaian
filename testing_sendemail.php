<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);  // Enable exceptions
try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Sendinblue SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'ictsppt@gmail.com';  // Your Sendinblue SMTP username
    $mail->Password = 'wjcb wcfd dwrw ijuj';  // Your Sendinblue SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Enable SMTP Debugging (verbose output)
    $mail->SMTPDebug = 2;  // Debug output level (2 = detailed)
    $mail->Debugoutput = 'html';  // Output in HTML format

    // Email content
    $mail->setFrom('syumaira00@gmail.com', 'Support Team');  // From email
    $mail->addAddress('ictsppt@gmail.com');  // Recipient email
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email from IT Support Team sent using PHPMailer!';
    $mail->isHTML(true);  // Make email HTML

    // Send the email
    if ($mail->send()) {
        echo 'Message has been sent!';
    } else {
        echo 'Failed to send email.';
    }
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";  // Display error message
}
?>
