<?php
// send_reset_link.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

session_start();
include_once("./config/connection.php");

$conn = connection();

$response = ['success' => false, 'message' => 'An error occurred. Please try again later.'];


if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists in the database
    $query = "SELECT * FROM account WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50)); // generate unique token
        $resetLink = "https://itrak.website/reset_password.php?token=$token";

        // Store the token in your database along with its expiration date
        $conn->query("UPDATE account SET reset_token = '$token', token_expiry = DATE_ADD(NOW(), INTERVAL 60 SECOND) WHERE email = '$email'");

        // PHPMailer setup
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'qcu.upkeep@gmail.com'; // SMTP username
            $mail->Password = 'qvpx bbcm bgmy hcvf'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('qcu.upkeep@gmail.com', 'UpKeep');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Link';
            $mail->Body    = 'Dear ' . $userName . ',<br><br>'
            . 'We have received a request to reset the password associated with your account. To proceed with resetting your password, please click the following link below:<br><br>'
            . '<a href="' . $resetLink . '">Password Reset Link</a><br><br>'
            . 'If you did not request this password reset or believe it to be an error, please ignore this email. Your account security is important to us, and no action is required if you did not initiate this request.<br><br>'
            . 'Thank you,<br>iTrak<br>'
            . '<img src="https://imgur.com/a/hSbNPhC" alt="iTrak Logo">';
            $mail->AltBody = 'Dear ' . $userName . ",\n\n"
            . "We have received a request to reset the password associated with your account. To proceed with resetting your password, please click the following link below:\n\n"
            . $resetLink . "\n\n"
            . "If you did not request this password reset or believe it to be an error, please ignore this email. Your account security is important to us, and no action is required if you did not initiate this request.\n\n"
            . "Thank you,\niTrak". $resetLink;

            $mail->send();
            $response = ['success' => true, 'message' => 'Password reset link sent! Please check your email.'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
        }
    } else {
        $response = ['success' => false, 'message' => "No account found with that email."];
    }
}
$conn->close();
// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
