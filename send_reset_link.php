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
    $email = $_POST['email'];  // Direct use in prepared statement

    // Prepare a statement for getting user details
    $stmt = $conn->prepare("SELECT firstName, lastName FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $token = bin2hex(random_bytes(50)); // generate unique token
        $resetLink = "https://itrak.site/reset_password.php?token=$token";

        // Prepare a statement for updating the token
        $updateStmt = $conn->prepare("UPDATE account SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 60 SECOND) WHERE email = ?");
        $updateStmt->bind_param("ss", $token, $email);
        $updateStmt->execute();

        // Check if the update was successful
        if ($updateStmt->affected_rows > 0) {
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
                $mail->setFrom('qcu.upkeep@gmail.com', 'iTrak');
                $mail->addAddress($email); // Add a recipient

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Link';
                $imagePath = '../../src/img/NewItrakLogo.png';
                $imageData = base64_encode(file_get_contents($imagePath));
                $base64ImageSrc = 'data:image/png;base64,' . $imageData;

                $mail->Body = 'Dear ' . $lastName . ',<br><br>'
                . 'We have received a request to reset the password associated with your account. Please click the following link to proceed with resetting your password:<br><br>'
                . '<a href="' . $resetLink . '">Password Reset Link</a><br><br>'
                . 'If you did not request this, please ignore this email.<br><br>'
                . 'Thank you,<br>iTrak<br>'
                . '<img src="' . $base64ImageSrc . '" alt="iTrak Logo">';
                
                $mail->AltBody = 'Dear ' . $lastName . ",\n\n"
                . "We have received a request to reset your password. Please click the link below to proceed:\n\n"
                . $resetLink . "\n\n"
                . "If you did not request this, please ignore this email.\n\n"
                . "Thank you,\niTrak";

                $mail->send();
                $response = ['success' => true, 'message' => 'Password reset link sent! Please check your email.'];
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
            }
        } else {
            $response = ['success' => false, 'message' => "Could not update the user token."];
        }

        $updateStmt->close();
    } else {
        $response = ['success' => false, 'message' => "No account found with that email."];
    }

    $stmt->close();
}
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
