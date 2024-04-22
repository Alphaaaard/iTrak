<?php
session_start();
include_once ("../../../config/connection.php");
$conn = connection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 2) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }
    function logActivity($conn, $accountId, $actionDescription, $tabValue)
    {
        $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("iss", $accountId, $actionDescription, $tabValue);
        if (!$stmt->execute()) {
            echo "Error logging activity: " . $stmt->error;
        }
        $stmt->close();
    }


    // for notif below
    // Update the SQL to join with the account and asset tables to get the admin's name and asset information
    $loggedInUserFirstName = $_SESSION['firstName'];
    $loggedInUserMiddleName = $_SESSION['middleName']; // Get the middle name from the session
    $loggedInUserLastName = $_SESSION['lastName'];

    // Assuming $loggedInUserFirstName, $loggedInUserMiddleName, $loggedInUserLastName are set

    $loggedInFullName = $loggedInUserFirstName . ' ' . $loggedInUserMiddleName . ' ' . $loggedInUserLastName;
    $loggedInAccountId = $_SESSION['accountId'];
    // SQL query to fetch notifications related to report activities
    $sqlLatestLogs = "SELECT al.*, acc.firstName AS adminFirstName, acc.middleName AS adminMiddleName, acc.lastName AS adminLastName, acc.role AS adminRole
    FROM activitylogs AS al
   JOIN account AS acc ON al.accountID = acc.accountID
   WHERE al.tab = 'General' AND al.p_seen = '0' AND al.action LIKE 'Assigned maintenance personnel%' AND al.action LIKE ? AND al.accountID != ?
   ORDER BY al.date DESC 
   LIMIT 5"; // Set limit to 5

    // Prepare the SQL statement
    $stmtLatestLogs = $conn->prepare($sqlLatestLogs);
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

    // Bind the parameter to exclude the current user's account ID
    $stmtLatestLogs->bind_param("si", $pattern, $loggedInAccountId);

    // Execute the query
    $stmtLatestLogs->execute();
    $resultLatestLogs = $stmtLatestLogs->get_result();

    $unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs 
WHERE p_seen = '0' AND accountID != ? AND action LIKE 'Assigned maintenance personnel%' AND action LIKE ?";
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

    $stmt = $conn->prepare($unseenCountQuery);
    $stmt->bind_param("is", $loggedInAccountId, $pattern);
    $stmt->execute();
    $stmt->bind_result($unseenCount);
    $stmt->fetch();
    $stmt->close();

    // Your PHPMailer settings and email credentials
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;              // Enable verbose debug output
        $mail->isSMTP();                                      // Send using SMTP
        $mail->Host = 'smtp.gmail.com';               // Set the SMTP server to send through
        $mail->SMTPAuth = true;                             // Enable SMTP authentication
        $mail->Username = 'qcu.upkeep@gmail.com';         // SMTP username
        $mail->Password = 'qvpx bbcm bgmy hcvf';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port = 587;                              // TCP port to connect to

        //Recipients
        $mail->setFrom('qcu.upkeep@gmail.com', 'iTrak');
        $mail->addAddress('qcu.upkeep@gmail.com', 'Admin');     // Baguhin niyo email to test

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Asset Status Changed';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'edit') === 0) {
                    $assetId = str_replace('edit', '', $key);
                    $status = $_POST['status']; // Ensure you have a field with name 'status' in your form
                    // Add your mail body content
                    $mail->Body = "The status of asset with ID $assetId has been changed to $status.";

                    $mail->send();
                    echo 'Message has been sent';
                    break; // Stop the loop after sending the email
                }
            }
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    //FOR ID 7275
    $sql7275 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7275";
    $stmt7275 = $conn->prepare($sql7275);
    $stmt7275->execute();
    $result7275 = $stmt7275->get_result();
    $row7275 = $result7275->fetch_assoc();
    $assetId7275 = $row7275['assetId'];
    $category7275 = $row7275['category'];
    $date7275 = $row7275['date'];
    $building7275 = $row7275['building'];
    $floor7275 = $row7275['floor'];
    $room7275 = $row7275['room'];
    $status7275 = $row7275['status'];
    $assignedName7275 = $row7275['assignedName'];
    $assignedBy7275 = $row7275['assignedBy'];
    $upload_img7275 = $row7275['upload_img'];
    $description7275 = $row7275['description'];
    //FOR ID 7274
    $sql7274 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7274";
    $stmt7274 = $conn->prepare($sql7274);
    $stmt7274->execute();
    $result7274 = $stmt7274->get_result();
    $row7274 = $result7274->fetch_assoc();
    $assetId7274 = $row7274['assetId'];
    $category7274 = $row7274['category'];
    $date7274 = $row7274['date'];
    $building7274 = $row7274['building'];
    $floor7274 = $row7274['floor'];
    $room7274 = $row7274['room'];
    $status7274 = $row7274['status'];
    $assignedName7274 = $row7274['assignedName'];
    $assignedBy7274 = $row7274['assignedBy'];
    $upload_img7274 = $row7274['upload_img'];
    $description7274 = $row7274['description'];
    //FOR ID 7273
    $sql7273 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7273";
    $stmt7273 = $conn->prepare($sql7273);
    $stmt7273->execute();
    $result7273 = $stmt7273->get_result();
    $row7273 = $result7273->fetch_assoc();
    $assetId7273 = $row7273['assetId'];
    $category7273 = $row7273['category'];
    $date7273 = $row7273['date'];
    $building7273 = $row7273['building'];
    $floor7273 = $row7273['floor'];
    $room7273 = $row7273['room'];
    $status7273 = $row7273['status'];
    $assignedName7273 = $row7273['assignedName'];
    $assignedBy7273 = $row7273['assignedBy'];
    $upload_img7273 = $row7273['upload_img'];
    $description7273 = $row7273['description'];
    //FOR ID 7272
    $sql7272 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7272";
    $stmt7272 = $conn->prepare($sql7272);
    $stmt7272->execute();
    $result7272 = $stmt7272->get_result();
    $row7272 = $result7272->fetch_assoc();
    $assetId7272 = $row7272['assetId'];
    $category7272 = $row7272['category'];
    $date7272 = $row7272['date'];
    $building7272 = $row7272['building'];
    $floor7272 = $row7272['floor'];
    $room7272 = $row7272['room'];
    $status7272 = $row7272['status'];
    $assignedName7272 = $row7272['assignedName'];
    $assignedBy7272 = $row7272['assignedBy'];
    $upload_img7272 = $row7272['upload_img'];
    $description7272 = $row7272['description'];
    //FOR ID 6947
    $sql6947 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6947";
    $stmt6947 = $conn->prepare($sql6947);
    $stmt6947->execute();
    $result6947 = $stmt6947->get_result();
    $row6947 = $result6947->fetch_assoc();
    $assetId6947 = $row6947['assetId'];
    $category6947 = $row6947['category'];
    $date6947 = $row6947['date'];
    $building6947 = $row6947['building'];
    $floor6947 = $row6947['floor'];
    $room6947 = $row6947['room'];
    $status6947 = $row6947['status'];
    $assignedName6947 = $row6947['assignedName'];
    $assignedBy6947 = $row6947['assignedBy'];
    $upload_img6947 = $row6947['upload_img'];
    $description6947 = $row6947['description'];
    //FOR ID 6946
    $sql6946 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6946";
    $stmt6946 = $conn->prepare($sql6946);
    $stmt6946->execute();
    $result6946 = $stmt6946->get_result();
    $row6946 = $result6946->fetch_assoc();
    $assetId6946 = $row6946['assetId'];
    $category6946 = $row6946['category'];
    $date6946 = $row6946['date'];
    $building6946 = $row6946['building'];
    $floor6946 = $row6946['floor'];
    $room6946 = $row6946['room'];
    $status6946 = $row6946['status'];
    $assignedName6946 = $row6946['assignedName'];
    $assignedBy6946 = $row6946['assignedBy'];
    $upload_img6946 = $row6946['upload_img'];
    $description6946 = $row6946['description'];
    //FOR ID 6945
    $sql6945 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6945";
    $stmt6945 = $conn->prepare($sql6945);
    $stmt6945->execute();
    $result6945 = $stmt6945->get_result();
    $row6945 = $result6945->fetch_assoc();
    $assetId6945 = $row6945['assetId'];
    $category6945 = $row6945['category'];
    $date6945 = $row6945['date'];
    $building6945 = $row6945['building'];
    $floor6945 = $row6945['floor'];
    $room6945 = $row6945['room'];
    $status6945 = $row6945['status'];
    $assignedName6945 = $row6945['assignedName'];
    $assignedBy6945 = $row6945['assignedBy'];
    $upload_img6945 = $row6945['upload_img'];
    $description6945 = $row6945['description'];
    //FOR ID 6944
    $sql6944 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6944";
    $stmt6944 = $conn->prepare($sql6944);
    $stmt6944->execute();
    $result6944 = $stmt6944->get_result();
    $row6944 = $result6944->fetch_assoc();
    $assetId6944 = $row6944['assetId'];
    $category6944 = $row6944['category'];
    $date6944 = $row6944['date'];
    $building6944 = $row6944['building'];
    $floor6944 = $row6944['floor'];
    $room6944 = $row6944['room'];
    $status6944 = $row6944['status'];
    $assignedName6944 = $row6944['assignedName'];
    $assignedBy6944 = $row6944['assignedBy'];
    $upload_img6944 = $row6944['upload_img'];
    $description6944 = $row6944['description'];
    //FOR ID 6943
    $sql6943 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6943";
    $stmt6943 = $conn->prepare($sql6943);
    $stmt6943->execute();
    $result6943 = $stmt6943->get_result();
    $row6943 = $result6943->fetch_assoc();
    $assetId6943 = $row6943['assetId'];
    $category6943 = $row6943['category'];
    $date6943 = $row6943['date'];
    $building6943 = $row6943['building'];
    $floor6943 = $row6943['floor'];
    $room6943 = $row6943['room'];
    $status6943 = $row6943['status'];
    $assignedName6943 = $row6943['assignedName'];
    $assignedBy6943 = $row6943['assignedBy'];
    $upload_img6943 = $row6943['upload_img'];
    $description6943 = $row6943['description'];
    //FOR ID 6942
    $sql6942 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6942";
    $stmt6942 = $conn->prepare($sql6942);
    $stmt6942->execute();
    $result6942 = $stmt6942->get_result();
    $row6942 = $result6942->fetch_assoc();
    $assetId6942 = $row6942['assetId'];
    $category6942 = $row6942['category'];
    $date6942 = $row6942['date'];
    $building6942 = $row6942['building'];
    $floor6942 = $row6942['floor'];
    $room6942 = $row6942['room'];
    $status6942 = $row6942['status'];
    $assignedName6942 = $row6942['assignedName'];
    $assignedBy6942 = $row6942['assignedBy'];
    $upload_img6942 = $row6942['upload_img'];
    $description6942 = $row6942['description'];
    //FOR ID 6941
    $sql6941 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6941";
    $stmt6941 = $conn->prepare($sql6941);
    $stmt6941->execute();
    $result6941 = $stmt6941->get_result();
    $row6941 = $result6941->fetch_assoc();
    $assetId6941 = $row6941['assetId'];
    $category6941 = $row6941['category'];
    $date6941 = $row6941['date'];
    $building6941 = $row6941['building'];
    $floor6941 = $row6941['floor'];
    $room6941 = $row6941['room'];
    $status6941 = $row6941['status'];
    $assignedName6941 = $row6941['assignedName'];
    $assignedBy6941 = $row6941['assignedBy'];
    $upload_img6941 = $row6941['upload_img'];
    $description6941 = $row6941['description'];
    //FOR ID 6940
    $sql6940 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6940";
    $stmt6940 = $conn->prepare($sql6940);
    $stmt6940->execute();
    $result6940 = $stmt6940->get_result();
    $row6940 = $result6940->fetch_assoc();
    $assetId6940 = $row6940['assetId'];
    $category6940 = $row6940['category'];
    $date6940 = $row6940['date'];
    $building6940 = $row6940['building'];
    $floor6940 = $row6940['floor'];
    $room6940 = $row6940['room'];
    $status6940 = $row6940['status'];
    $assignedName6940 = $row6940['assignedName'];
    $assignedBy6940 = $row6940['assignedBy'];
    $upload_img6940 = $row6940['upload_img'];
    $description6940 = $row6940['description'];
    //FOR ID 6939
    $sql6939 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6939";
    $stmt6939 = $conn->prepare($sql6939);
    $stmt6939->execute();
    $result6939 = $stmt6939->get_result();
    $row6939 = $result6939->fetch_assoc();
    $assetId6939 = $row6939['assetId'];
    $category6939 = $row6939['category'];
    $date6939 = $row6939['date'];
    $building6939 = $row6939['building'];
    $floor6939 = $row6939['floor'];
    $room6939 = $row6939['room'];
    $status6939 = $row6939['status'];
    $assignedName6939 = $row6939['assignedName'];
    $assignedBy6939 = $row6939['assignedBy'];
    $upload_img6939 = $row6939['upload_img'];
    $description6939 = $row6939['description'];
    //FOR ID 6938
    $sql6938 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6938";
    $stmt6938 = $conn->prepare($sql6938);
    $stmt6938->execute();
    $result6938 = $stmt6938->get_result();
    $row6938 = $result6938->fetch_assoc();
    $assetId6938 = $row6938['assetId'];
    $category6938 = $row6938['category'];
    $date6938 = $row6938['date'];
    $building6938 = $row6938['building'];
    $floor6938 = $row6938['floor'];
    $room6938 = $row6938['room'];
    $status6938 = $row6938['status'];
    $assignedName6938 = $row6938['assignedName'];
    $assignedBy6938 = $row6938['assignedBy'];
    $upload_img6938 = $row6938['upload_img'];
    $description6938 = $row6938['description'];
    //FOR ID 6937
    $sql6937 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6937";
    $stmt6937 = $conn->prepare($sql6937);
    $stmt6937->execute();
    $result6937 = $stmt6937->get_result();
    $row6937 = $result6937->fetch_assoc();
    $assetId6937 = $row6937['assetId'];
    $category6937 = $row6937['category'];
    $date6937 = $row6937['date'];
    $building6937 = $row6937['building'];
    $floor6937 = $row6937['floor'];
    $room6937 = $row6937['room'];
    $status6937 = $row6937['status'];
    $assignedName6937 = $row6937['assignedName'];
    $assignedBy6937 = $row6937['assignedBy'];
    $upload_img6937 = $row6937['upload_img'];
    $description6937 = $row6937['description'];
    //FOR ID 6936
    $sql6936 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6936";
    $stmt6936 = $conn->prepare($sql6936);
    $stmt6936->execute();
    $result6936 = $stmt6936->get_result();
    $row6936 = $result6936->fetch_assoc();
    $assetId6936 = $row6936['assetId'];
    $category6936 = $row6936['category'];
    $date6936 = $row6936['date'];
    $building6936 = $row6936['building'];
    $floor6936 = $row6936['floor'];
    $room6936 = $row6936['room'];
    $status6936 = $row6936['status'];
    $assignedName6936 = $row6936['assignedName'];
    $assignedBy6936 = $row6936['assignedBy'];
    $upload_img6936 = $row6936['upload_img'];
    $description6936 = $row6936['description'];
    //FOR ID 6935
    $sql6935 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6935";
    $stmt6935 = $conn->prepare($sql6935);
    $stmt6935->execute();
    $result6935 = $stmt6935->get_result();
    $row6935 = $result6935->fetch_assoc();
    $assetId6935 = $row6935['assetId'];
    $category6935 = $row6935['category'];
    $date6935 = $row6935['date'];
    $building6935 = $row6935['building'];
    $floor6935 = $row6935['floor'];
    $room6935 = $row6935['room'];
    $status6935 = $row6935['status'];
    $assignedName6935 = $row6935['assignedName'];
    $assignedBy6935 = $row6935['assignedBy'];
    $upload_img6935 = $row6935['upload_img'];
    $description6935 = $row6935['description'];
    //FOR ID 6934
    $sql6934 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6934";
    $stmt6934 = $conn->prepare($sql6934);
    $stmt6934->execute();
    $result6934 = $stmt6934->get_result();
    $row6934 = $result6934->fetch_assoc();
    $assetId6934 = $row6934['assetId'];
    $category6934 = $row6934['category'];
    $date6934 = $row6934['date'];
    $building6934 = $row6934['building'];
    $floor6934 = $row6934['floor'];
    $room6934 = $row6934['room'];
    $status6934 = $row6934['status'];
    $assignedName6934 = $row6934['assignedName'];
    $assignedBy6934 = $row6934['assignedBy'];
    $upload_img6934 = $row6934['upload_img'];
    $description6934 = $row6934['description'];
    //FOR ID 6933
    $sql6933 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6933";
    $stmt6933 = $conn->prepare($sql6933);
    $stmt6933->execute();
    $result6933 = $stmt6933->get_result();
    $row6933 = $result6933->fetch_assoc();
    $assetId6933 = $row6933['assetId'];
    $category6933 = $row6933['category'];
    $date6933 = $row6933['date'];
    $building6933 = $row6933['building'];
    $floor6933 = $row6933['floor'];
    $room6933 = $row6933['room'];
    $status6933 = $row6933['status'];
    $assignedName6933 = $row6933['assignedName'];
    $assignedBy6933 = $row6933['assignedBy'];
    $upload_img6933 = $row6933['upload_img'];
    $description6933 = $row6933['description'];
    //FOR ID 6932
    $sql6932 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6932";
    $stmt6932 = $conn->prepare($sql6932);
    $stmt6932->execute();
    $result6932 = $stmt6932->get_result();
    $row6932 = $result6932->fetch_assoc();
    $assetId6932 = $row6932['assetId'];
    $category6932 = $row6932['category'];
    $date6932 = $row6932['date'];
    $building6932 = $row6932['building'];
    $floor6932 = $row6932['floor'];
    $room6932 = $row6932['room'];
    $status6932 = $row6932['status'];
    $assignedName6932 = $row6932['assignedName'];
    $assignedBy6932 = $row6932['assignedBy'];
    $upload_img6932 = $row6932['upload_img'];
    $description6932 = $row6932['description'];
    //FOR ID 6931
    $sql6931 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6931";
    $stmt6931 = $conn->prepare($sql6931);
    $stmt6931->execute();
    $result6931 = $stmt6931->get_result();
    $row6931 = $result6931->fetch_assoc();
    $assetId6931 = $row6931['assetId'];
    $category6931 = $row6931['category'];
    $date6931 = $row6931['date'];
    $building6931 = $row6931['building'];
    $floor6931 = $row6931['floor'];
    $room6931 = $row6931['room'];
    $status6931 = $row6931['status'];
    $assignedName6931 = $row6931['assignedName'];
    $assignedBy6931 = $row6931['assignedBy'];
    $upload_img6931 = $row6931['upload_img'];
    $description6931 = $row6931['description'];
    //FOR ID 6930
    $sql6930 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6930";
    $stmt6930 = $conn->prepare($sql6930);
    $stmt6930->execute();
    $result6930 = $stmt6930->get_result();
    $row6930 = $result6930->fetch_assoc();
    $assetId6930 = $row6930['assetId'];
    $category6930 = $row6930['category'];
    $date6930 = $row6930['date'];
    $building6930 = $row6930['building'];
    $floor6930 = $row6930['floor'];
    $room6930 = $row6930['room'];
    $status6930 = $row6930['status'];
    $assignedName6930 = $row6930['assignedName'];
    $assignedBy6930 = $row6930['assignedBy'];
    $upload_img6930 = $row6930['upload_img'];
    $description6930 = $row6930['description'];
    //FOR ID 6929
    $sql6929 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6929";
    $stmt6929 = $conn->prepare($sql6929);
    $stmt6929->execute();
    $result6929 = $stmt6929->get_result();
    $row6929 = $result6929->fetch_assoc();
    $assetId6929 = $row6929['assetId'];
    $category6929 = $row6929['category'];
    $date6929 = $row6929['date'];
    $building6929 = $row6929['building'];
    $floor6929 = $row6929['floor'];
    $room6929 = $row6929['room'];
    $status6929 = $row6929['status'];
    $assignedName6929 = $row6929['assignedName'];
    $assignedBy6929 = $row6929['assignedBy'];
    $upload_img6929 = $row6929['upload_img'];
    $description6929 = $row6929['description'];
    //FOR ID 6928
    $sql6928 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6928";
    $stmt6928 = $conn->prepare($sql6928);
    $stmt6928->execute();
    $result6928 = $stmt6928->get_result();
    $row6928 = $result6928->fetch_assoc();
    $assetId6928 = $row6928['assetId'];
    $category6928 = $row6928['category'];
    $date6928 = $row6928['date'];
    $building6928 = $row6928['building'];
    $floor6928 = $row6928['floor'];
    $room6928 = $row6928['room'];
    $status6928 = $row6928['status'];
    $assignedName6928 = $row6928['assignedName'];
    $assignedBy6928 = $row6928['assignedBy'];
    $upload_img6928 = $row6928['upload_img'];
    $description6928 = $row6928['description'];
    //FOR ID 6927
    $sql6927 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6927";
    $stmt6927 = $conn->prepare($sql6927);
    $stmt6927->execute();
    $result6927 = $stmt6927->get_result();
    $row6927 = $result6927->fetch_assoc();
    $assetId6927 = $row6927['assetId'];
    $category6927 = $row6927['category'];
    $date6927 = $row6927['date'];
    $building6927 = $row6927['building'];
    $floor6927 = $row6927['floor'];
    $room6927 = $row6927['room'];
    $status6927 = $row6927['status'];
    $assignedName6927 = $row6927['assignedName'];
    $assignedBy6927 = $row6927['assignedBy'];
    $upload_img6927 = $row6927['upload_img'];
    $description6927 = $row6927['description'];
    //FOR ID 6926
    $sql6926 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6926";
    $stmt6926 = $conn->prepare($sql6926);
    $stmt6926->execute();
    $result6926 = $stmt6926->get_result();
    $row6926 = $result6926->fetch_assoc();
    $assetId6926 = $row6926['assetId'];
    $category6926 = $row6926['category'];
    $date6926 = $row6926['date'];
    $building6926 = $row6926['building'];
    $floor6926 = $row6926['floor'];
    $room6926 = $row6926['room'];
    $status6926 = $row6926['status'];
    $assignedName6926 = $row6926['assignedName'];
    $assignedBy6926 = $row6926['assignedBy'];
    $upload_img6926 = $row6926['upload_img'];
    $description6926 = $row6926['description'];
    //FOR ID 6925
    $sql6925 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6925";
    $stmt6925 = $conn->prepare($sql6925);
    $stmt6925->execute();
    $result6925 = $stmt6925->get_result();
    $row6925 = $result6925->fetch_assoc();
    $assetId6925 = $row6925['assetId'];
    $category6925 = $row6925['category'];
    $date6925 = $row6925['date'];
    $building6925 = $row6925['building'];
    $floor6925 = $row6925['floor'];
    $room6925 = $row6925['room'];
    $status6925 = $row6925['status'];
    $assignedName6925 = $row6925['assignedName'];
    $assignedBy6925 = $row6925['assignedBy'];
    $upload_img6925 = $row6925['upload_img'];
    $description6925 = $row6925['description'];
    //FOR ID 6924
    $sql6924 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6924";
    $stmt6924 = $conn->prepare($sql6924);
    $stmt6924->execute();
    $result6924 = $stmt6924->get_result();
    $row6924 = $result6924->fetch_assoc();
    $assetId6924 = $row6924['assetId'];
    $category6924 = $row6924['category'];
    $date6924 = $row6924['date'];
    $building6924 = $row6924['building'];
    $floor6924 = $row6924['floor'];
    $room6924 = $row6924['room'];
    $status6924 = $row6924['status'];
    $assignedName6924 = $row6924['assignedName'];
    $assignedBy6924 = $row6924['assignedBy'];
    $upload_img6924 = $row6924['upload_img'];
    $description6924 = $row6924['description'];
    //FOR ID 6923
    $sql6923 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6923";
    $stmt6923 = $conn->prepare($sql6923);
    $stmt6923->execute();
    $result6923 = $stmt6923->get_result();
    $row6923 = $result6923->fetch_assoc();
    $assetId6923 = $row6923['assetId'];
    $category6923 = $row6923['category'];
    $date6923 = $row6923['date'];
    $building6923 = $row6923['building'];
    $floor6923 = $row6923['floor'];
    $room6923 = $row6923['room'];
    $status6923 = $row6923['status'];
    $assignedName6923 = $row6923['assignedName'];
    $assignedBy6923 = $row6923['assignedBy'];
    $upload_img6923 = $row6923['upload_img'];
    $description6923 = $row6923['description'];
    //FOR ID 6922
    $sql6922 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6922";
    $stmt6922 = $conn->prepare($sql6922);
    $stmt6922->execute();
    $result6922 = $stmt6922->get_result();
    $row6922 = $result6922->fetch_assoc();
    $assetId6922 = $row6922['assetId'];
    $category6922 = $row6922['category'];
    $date6922 = $row6922['date'];
    $building6922 = $row6922['building'];
    $floor6922 = $row6922['floor'];
    $room6922 = $row6922['room'];
    $status6922 = $row6922['status'];
    $assignedName6922 = $row6922['assignedName'];
    $assignedBy6922 = $row6922['assignedBy'];
    $upload_img6922 = $row6922['upload_img'];
    $description6922 = $row6922['description'];
    //FOR ID 6921
    $sql6921 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6921";
    $stmt6921 = $conn->prepare($sql6921);
    $stmt6921->execute();
    $result6921 = $stmt6921->get_result();
    $row6921 = $result6921->fetch_assoc();
    $assetId6921 = $row6921['assetId'];
    $category6921 = $row6921['category'];
    $date6921 = $row6921['date'];
    $building6921 = $row6921['building'];
    $floor6921 = $row6921['floor'];
    $room6921 = $row6921['room'];
    $status6921 = $row6921['status'];
    $assignedName6921 = $row6921['assignedName'];
    $assignedBy6921 = $row6921['assignedBy'];
    $upload_img6921 = $row6921['upload_img'];
    $description6921 = $row6921['description'];
    //FOR ID 6920
    $sql6920 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6920";
    $stmt6920 = $conn->prepare($sql6920);
    $stmt6920->execute();
    $result6920 = $stmt6920->get_result();
    $row6920 = $result6920->fetch_assoc();
    $assetId6920 = $row6920['assetId'];
    $category6920 = $row6920['category'];
    $date6920 = $row6920['date'];
    $building6920 = $row6920['building'];
    $floor6920 = $row6920['floor'];
    $room6920 = $row6920['room'];
    $status6920 = $row6920['status'];
    $assignedName6920 = $row6920['assignedName'];
    $assignedBy6920 = $row6920['assignedBy'];
    $upload_img6920 = $row6920['upload_img'];
    $description6920 = $row6920['description'];
    //FOR ID 6919
    $sql6919 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6919";
    $stmt6919 = $conn->prepare($sql6919);
    $stmt6919->execute();
    $result6919 = $stmt6919->get_result();
    $row6919 = $result6919->fetch_assoc();
    $assetId6919 = $row6919['assetId'];
    $category6919 = $row6919['category'];
    $date6919 = $row6919['date'];
    $building6919 = $row6919['building'];
    $floor6919 = $row6919['floor'];
    $room6919 = $row6919['room'];
    $status6919 = $row6919['status'];
    $assignedName6919 = $row6919['assignedName'];
    $assignedBy6919 = $row6919['assignedBy'];
    $upload_img6919 = $row6919['upload_img'];
    $description6919 = $row6919['description'];
    //FOR ID 6918
    $sql6918 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6918";
    $stmt6918 = $conn->prepare($sql6918);
    $stmt6918->execute();
    $result6918 = $stmt6918->get_result();
    $row6918 = $result6918->fetch_assoc();
    $assetId6918 = $row6918['assetId'];
    $category6918 = $row6918['category'];
    $date6918 = $row6918['date'];
    $building6918 = $row6918['building'];
    $floor6918 = $row6918['floor'];
    $room6918 = $row6918['room'];
    $status6918 = $row6918['status'];
    $assignedName6918 = $row6918['assignedName'];
    $assignedBy6918 = $row6918['assignedBy'];
    $upload_img6918 = $row6918['upload_img'];
    $description6918 = $row6918['description'];
    //FOR ID 6917
    $sql6917 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6917";
    $stmt6917 = $conn->prepare($sql6917);
    $stmt6917->execute();
    $result6917 = $stmt6917->get_result();
    $row6917 = $result6917->fetch_assoc();
    $assetId6917 = $row6917['assetId'];
    $category6917 = $row6917['category'];
    $date6917 = $row6917['date'];
    $building6917 = $row6917['building'];
    $floor6917 = $row6917['floor'];
    $room6917 = $row6917['room'];
    $status6917 = $row6917['status'];
    $assignedName6917 = $row6917['assignedName'];
    $assignedBy6917 = $row6917['assignedBy'];
    $upload_img6917 = $row6917['upload_img'];
    $description6917 = $row6917['description'];
    //FOR ID 6916
    $sql6916 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6916";
    $stmt6916 = $conn->prepare($sql6916);
    $stmt6916->execute();
    $result6916 = $stmt6916->get_result();
    $row6916 = $result6916->fetch_assoc();
    $assetId6916 = $row6916['assetId'];
    $category6916 = $row6916['category'];
    $date6916 = $row6916['date'];
    $building6916 = $row6916['building'];
    $floor6916 = $row6916['floor'];
    $room6916 = $row6916['room'];
    $status6916 = $row6916['status'];
    $assignedName6916 = $row6916['assignedName'];
    $assignedBy6916 = $row6916['assignedBy'];
    $upload_img6916 = $row6916['upload_img'];
    $description6916 = $row6916['description'];
    //FOR ID 6915
    $sql6915 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6915";
    $stmt6915 = $conn->prepare($sql6915);
    $stmt6915->execute();
    $result6915 = $stmt6915->get_result();
    $row6915 = $result6915->fetch_assoc();
    $assetId6915 = $row6915['assetId'];
    $category6915 = $row6915['category'];
    $date6915 = $row6915['date'];
    $building6915 = $row6915['building'];
    $floor6915 = $row6915['floor'];
    $room6915 = $row6915['room'];
    $status6915 = $row6915['status'];
    $assignedName6915 = $row6915['assignedName'];
    $assignedBy6915 = $row6915['assignedBy'];
    $upload_img6915 = $row6915['upload_img'];
    $description6915 = $row6915['description'];
    //FOR ID 6914
    $sql6914 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6914";
    $stmt6914 = $conn->prepare($sql6914);
    $stmt6914->execute();
    $result6914 = $stmt6914->get_result();
    $row6914 = $result6914->fetch_assoc();
    $assetId6914 = $row6914['assetId'];
    $category6914 = $row6914['category'];
    $date6914 = $row6914['date'];
    $building6914 = $row6914['building'];
    $floor6914 = $row6914['floor'];
    $room6914 = $row6914['room'];
    $status6914 = $row6914['status'];
    $assignedName6914 = $row6914['assignedName'];
    $assignedBy6914 = $row6914['assignedBy'];
    $upload_img6914 = $row6914['upload_img'];
    $description6914 = $row6914['description'];
    //FOR ID 6913
    $sql6913 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6913";
    $stmt6913 = $conn->prepare($sql6913);
    $stmt6913->execute();
    $result6913 = $stmt6913->get_result();
    $row6913 = $result6913->fetch_assoc();
    $assetId6913 = $row6913['assetId'];
    $category6913 = $row6913['category'];
    $date6913 = $row6913['date'];
    $building6913 = $row6913['building'];
    $floor6913 = $row6913['floor'];
    $room6913 = $row6913['room'];
    $status6913 = $row6913['status'];
    $assignedName6913 = $row6913['assignedName'];
    $assignedBy6913 = $row6913['assignedBy'];
    $upload_img6913 = $row6913['upload_img'];
    $description6913 = $row6913['description'];
    //FOR ID 6912
    $sql6912 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6912";
    $stmt6912 = $conn->prepare($sql6912);
    $stmt6912->execute();
    $result6912 = $stmt6912->get_result();
    $row6912 = $result6912->fetch_assoc();
    $assetId6912 = $row6912['assetId'];
    $category6912 = $row6912['category'];
    $date6912 = $row6912['date'];
    $building6912 = $row6912['building'];
    $floor6912 = $row6912['floor'];
    $room6912 = $row6912['room'];
    $status6912 = $row6912['status'];
    $assignedName6912 = $row6912['assignedName'];
    $assignedBy6912 = $row6912['assignedBy'];
    $upload_img6912 = $row6912['upload_img'];
    $description6912 = $row6912['description'];
    //FOR ID 6911
    $sql6911 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6911";
    $stmt6911 = $conn->prepare($sql6911);
    $stmt6911->execute();
    $result6911 = $stmt6911->get_result();
    $row6911 = $result6911->fetch_assoc();
    $assetId6911 = $row6911['assetId'];
    $category6911 = $row6911['category'];
    $date6911 = $row6911['date'];
    $building6911 = $row6911['building'];
    $floor6911 = $row6911['floor'];
    $room6911 = $row6911['room'];
    $status6911 = $row6911['status'];
    $assignedName6911 = $row6911['assignedName'];
    $assignedBy6911 = $row6911['assignedBy'];
    $upload_img6911 = $row6911['upload_img'];
    $description6911 = $row6911['description'];
    //FOR ID 6910
    $sql6910 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6910";
    $stmt6910 = $conn->prepare($sql6910);
    $stmt6910->execute();
    $result6910 = $stmt6910->get_result();
    $row6910 = $result6910->fetch_assoc();
    $assetId6910 = $row6910['assetId'];
    $category6910 = $row6910['category'];
    $date6910 = $row6910['date'];
    $building6910 = $row6910['building'];
    $floor6910 = $row6910['floor'];
    $room6910 = $row6910['room'];
    $status6910 = $row6910['status'];
    $assignedName6910 = $row6910['assignedName'];
    $assignedBy6910 = $row6910['assignedBy'];
    $upload_img6910 = $row6910['upload_img'];
    $description6910 = $row6910['description'];
    //FOR ID 6909
    $sql6909 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6909";
    $stmt6909 = $conn->prepare($sql6909);
    $stmt6909->execute();
    $result6909 = $stmt6909->get_result();
    $row6909 = $result6909->fetch_assoc();
    $assetId6909 = $row6909['assetId'];
    $category6909 = $row6909['category'];
    $date6909 = $row6909['date'];
    $building6909 = $row6909['building'];
    $floor6909 = $row6909['floor'];
    $room6909 = $row6909['room'];
    $status6909 = $row6909['status'];
    $assignedName6909 = $row6909['assignedName'];
    $assignedBy6909 = $row6909['assignedBy'];
    $upload_img6909 = $row6909['upload_img'];
    $description6909 = $row6909['description'];
    //FOR ID 6908
    $sql6908 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6908";
    $stmt6908 = $conn->prepare($sql6908);
    $stmt6908->execute();
    $result6908 = $stmt6908->get_result();
    $row6908 = $result6908->fetch_assoc();
    $assetId6908 = $row6908['assetId'];
    $category6908 = $row6908['category'];
    $date6908 = $row6908['date'];
    $building6908 = $row6908['building'];
    $floor6908 = $row6908['floor'];
    $room6908 = $row6908['room'];
    $status6908 = $row6908['status'];
    $assignedName6908 = $row6908['assignedName'];
    $assignedBy6908 = $row6908['assignedBy'];
    $upload_img6908 = $row6908['upload_img'];
    $description6908 = $row6908['description'];
    //FOR ID 6907
    $sql6907 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6907";
    $stmt6907 = $conn->prepare($sql6907);
    $stmt6907->execute();
    $result6907 = $stmt6907->get_result();
    $row6907 = $result6907->fetch_assoc();
    $assetId6907 = $row6907['assetId'];
    $category6907 = $row6907['category'];
    $date6907 = $row6907['date'];
    $building6907 = $row6907['building'];
    $floor6907 = $row6907['floor'];
    $room6907 = $row6907['room'];
    $status6907 = $row6907['status'];
    $assignedName6907 = $row6907['assignedName'];
    $assignedBy6907 = $row6907['assignedBy'];
    $upload_img6907 = $row6907['upload_img'];
    $description6907 = $row6907['description'];
    //FOR ID 6906
    $sql6906 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6906";
    $stmt6906 = $conn->prepare($sql6906);
    $stmt6906->execute();
    $result6906 = $stmt6906->get_result();
    $row6906 = $result6906->fetch_assoc();
    $assetId6906 = $row6906['assetId'];
    $category6906 = $row6906['category'];
    $date6906 = $row6906['date'];
    $building6906 = $row6906['building'];
    $floor6906 = $row6906['floor'];
    $room6906 = $row6906['room'];
    $status6906 = $row6906['status'];
    $assignedName6906 = $row6906['assignedName'];
    $assignedBy6906 = $row6906['assignedBy'];
    $upload_img6906 = $row6906['upload_img'];
    $description6906 = $row6906['description'];
    //FOR ID 6905
    $sql6905 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6905";
    $stmt6905 = $conn->prepare($sql6905);
    $stmt6905->execute();
    $result6905 = $stmt6905->get_result();
    $row6905 = $result6905->fetch_assoc();
    $assetId6905 = $row6905['assetId'];
    $category6905 = $row6905['category'];
    $date6905 = $row6905['date'];
    $building6905 = $row6905['building'];
    $floor6905 = $row6905['floor'];
    $room6905 = $row6905['room'];
    $status6905 = $row6905['status'];
    $assignedName6905 = $row6905['assignedName'];
    $assignedBy6905 = $row6905['assignedBy'];
    $upload_img6905 = $row6905['upload_img'];
    $description6905 = $row6905['description'];
    //FOR ID 6904
    $sql6904 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6904";
    $stmt6904 = $conn->prepare($sql6904);
    $stmt6904->execute();
    $result6904 = $stmt6904->get_result();
    $row6904 = $result6904->fetch_assoc();
    $assetId6904 = $row6904['assetId'];
    $category6904 = $row6904['category'];
    $date6904 = $row6904['date'];
    $building6904 = $row6904['building'];
    $floor6904 = $row6904['floor'];
    $room6904 = $row6904['room'];
    $status6904 = $row6904['status'];
    $assignedName6904 = $row6904['assignedName'];
    $assignedBy6904 = $row6904['assignedBy'];
    $upload_img6904 = $row6904['upload_img'];
    $description6904 = $row6904['description'];
    //FOR ID 6903
    $sql6903 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6903";
    $stmt6903 = $conn->prepare($sql6903);
    $stmt6903->execute();
    $result6903 = $stmt6903->get_result();
    $row6903 = $result6903->fetch_assoc();
    $assetId6903 = $row6903['assetId'];
    $category6903 = $row6903['category'];
    $date6903 = $row6903['date'];
    $building6903 = $row6903['building'];
    $floor6903 = $row6903['floor'];
    $room6903 = $row6903['room'];
    $status6903 = $row6903['status'];
    $assignedName6903 = $row6903['assignedName'];
    $assignedBy6903 = $row6903['assignedBy'];
    $upload_img6903 = $row6903['upload_img'];
    $description6903 = $row6903['description'];
    //FOR ID 6902
    $sql6902 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6902";
    $stmt6902 = $conn->prepare($sql6902);
    $stmt6902->execute();
    $result6902 = $stmt6902->get_result();
    $row6902 = $result6902->fetch_assoc();
    $assetId6902 = $row6902['assetId'];
    $category6902 = $row6902['category'];
    $date6902 = $row6902['date'];
    $building6902 = $row6902['building'];
    $floor6902 = $row6902['floor'];
    $room6902 = $row6902['room'];
    $status6902 = $row6902['status'];
    $assignedName6902 = $row6902['assignedName'];
    $assignedBy6902 = $row6902['assignedBy'];
    $upload_img6902 = $row6902['upload_img'];
    $description6902 = $row6902['description'];
    //FOR ID 6901
    $sql6901 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6901";
    $stmt6901 = $conn->prepare($sql6901);
    $stmt6901->execute();
    $result6901 = $stmt6901->get_result();
    $row6901 = $result6901->fetch_assoc();
    $assetId6901 = $row6901['assetId'];
    $category6901 = $row6901['category'];
    $date6901 = $row6901['date'];
    $building6901 = $row6901['building'];
    $floor6901 = $row6901['floor'];
    $room6901 = $row6901['room'];
    $status6901 = $row6901['status'];
    $assignedName6901 = $row6901['assignedName'];
    $assignedBy6901 = $row6901['assignedBy'];
    $upload_img6901 = $row6901['upload_img'];
    $description6901 = $row6901['description'];
    //FOR ID 6900
    $sql6900 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6900";
    $stmt6900 = $conn->prepare($sql6900);
    $stmt6900->execute();
    $result6900 = $stmt6900->get_result();
    $row6900 = $result6900->fetch_assoc();
    $assetId6900 = $row6900['assetId'];
    $category6900 = $row6900['category'];
    $date6900 = $row6900['date'];
    $building6900 = $row6900['building'];
    $floor6900 = $row6900['floor'];
    $room6900 = $row6900['room'];
    $status6900 = $row6900['status'];
    $assignedName6900 = $row6900['assignedName'];
    $assignedBy6900 = $row6900['assignedBy'];
    $upload_img6900 = $row6900['upload_img'];
    $description6900 = $row6900['description'];
    //FOR ID 6899
    $sql6899 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6899";
    $stmt6899 = $conn->prepare($sql6899);
    $stmt6899->execute();
    $result6899 = $stmt6899->get_result();
    $row6899 = $result6899->fetch_assoc();
    $assetId6899 = $row6899['assetId'];
    $category6899 = $row6899['category'];
    $date6899 = $row6899['date'];
    $building6899 = $row6899['building'];
    $floor6899 = $row6899['floor'];
    $room6899 = $row6899['room'];
    $status6899 = $row6899['status'];
    $assignedName6899 = $row6899['assignedName'];
    $assignedBy6899 = $row6899['assignedBy'];
    $upload_img6899 = $row6899['upload_img'];
    $description6899 = $row6899['description'];

    //FOR ID 6898
    $sql6898 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6898";
    $stmt6898 = $conn->prepare($sql6898);
    $stmt6898->execute();
    $result6898 = $stmt6898->get_result();
    $row6898 = $result6898->fetch_assoc();
    $assetId6898 = $row6898['assetId'];
    $category6898 = $row6898['category'];
    $date6898 = $row6898['date'];
    $building6898 = $row6898['building'];
    $floor6898 = $row6898['floor'];
    $room6898 = $row6898['room'];
    $status6898 = $row6898['status'];
    $assignedName6898 = $row6898['assignedName'];
    $assignedBy6898 = $row6898['assignedBy'];
    $upload_img6898 = $row6898['upload_img'];
    $description6898 = $row6898['description'];

    //FOR ID 6897
    $sql6897 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6897";
    $stmt6897 = $conn->prepare($sql6897);
    $stmt6897->execute();
    $result6897 = $stmt6897->get_result();
    $row6897 = $result6897->fetch_assoc();
    $assetId6897 = $row6897['assetId'];
    $category6897 = $row6897['category'];
    $date6897 = $row6897['date'];
    $building6897 = $row6897['building'];
    $floor6897 = $row6897['floor'];
    $room6897 = $row6897['room'];
    $status6897 = $row6897['status'];
    $assignedName6897 = $row6897['assignedName'];
    $assignedBy6897 = $row6897['assignedBy'];
    $upload_img6897 = $row6897['upload_img'];
    $description6897 = $row6897['description'];

    //FOR ID 6896
    $sql6896 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6896";
    $stmt6896 = $conn->prepare($sql6896);
    $stmt6896->execute();
    $result6896 = $stmt6896->get_result();
    $row6896 = $result6896->fetch_assoc();
    $assetId6896 = $row6896['assetId'];
    $category6896 = $row6896['category'];
    $date6896 = $row6896['date'];
    $building6896 = $row6896['building'];
    $floor6896 = $row6896['floor'];
    $room6896 = $row6896['room'];
    $status6896 = $row6896['status'];
    $assignedName6896 = $row6896['assignedName'];
    $assignedBy6896 = $row6896['assignedBy'];
    $upload_img6896 = $row6896['upload_img'];
    $description6896 = $row6896['description'];

    //FOR ID 6895
    $sql6895 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6895";
    $stmt6895 = $conn->prepare($sql6895);
    $stmt6895->execute();
    $result6895 = $stmt6895->get_result();
    $row6895 = $result6895->fetch_assoc();
    $assetId6895 = $row6895['assetId'];
    $category6895 = $row6895['category'];
    $date6895 = $row6895['date'];
    $building6895 = $row6895['building'];
    $floor6895 = $row6895['floor'];
    $room6895 = $row6895['room'];
    $status6895 = $row6895['status'];
    $assignedName6895 = $row6895['assignedName'];
    $assignedBy6895 = $row6895['assignedBy'];
    $upload_img6895 = $row6895['upload_img'];
    $description6895 = $row6895['description'];

    //FOR ID 6894
    $sql6894 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6894";
    $stmt6894 = $conn->prepare($sql6894);
    $stmt6894->execute();
    $result6894 = $stmt6894->get_result();
    $row6894 = $result6894->fetch_assoc();
    $assetId6894 = $row6894['assetId'];
    $category6894 = $row6894['category'];
    $date6894 = $row6894['date'];
    $building6894 = $row6894['building'];
    $floor6894 = $row6894['floor'];
    $room6894 = $row6894['room'];
    $status6894 = $row6894['status'];
    $assignedName6894 = $row6894['assignedName'];
    $assignedBy6894 = $row6894['assignedBy'];
    $upload_img6894 = $row6894['upload_img'];
    $description6894 = $row6894['description'];

    //FOR ID 6893
    $sql6893 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6893";
    $stmt6893 = $conn->prepare($sql6893);
    $stmt6893->execute();
    $result6893 = $stmt6893->get_result();
    $row6893 = $result6893->fetch_assoc();
    $assetId6893 = $row6893['assetId'];
    $category6893 = $row6893['category'];
    $date6893 = $row6893['date'];
    $building6893 = $row6893['building'];
    $floor6893 = $row6893['floor'];
    $room6893 = $row6893['room'];
    $status6893 = $row6893['status'];
    $assignedName6893 = $row6893['assignedName'];
    $assignedBy6893 = $row6893['assignedBy'];
    $upload_img6893 = $row6893['upload_img'];
    $description6893 = $row6893['description'];

    //FOR ID 6892
    $sql6892 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6892";
    $stmt6892 = $conn->prepare($sql6892);
    $stmt6892->execute();
    $result6892 = $stmt6892->get_result();
    $row6892 = $result6892->fetch_assoc();
    $assetId6892 = $row6892['assetId'];
    $category6892 = $row6892['category'];
    $date6892 = $row6892['date'];
    $building6892 = $row6892['building'];
    $floor6892 = $row6892['floor'];
    $room6892 = $row6892['room'];
    $status6892 = $row6892['status'];
    $assignedName6892 = $row6892['assignedName'];
    $assignedBy6892 = $row6892['assignedBy'];
    $upload_img6892 = $row6892['upload_img'];
    $description6892 = $row6892['description'];

    //FOR ID 6891
    $sql6891 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6891";
    $stmt6891 = $conn->prepare($sql6891);
    $stmt6891->execute();
    $result6891 = $stmt6891->get_result();
    $row6891 = $result6891->fetch_assoc();
    $assetId6891 = $row6891['assetId'];
    $category6891 = $row6891['category'];
    $date6891 = $row6891['date'];
    $building6891 = $row6891['building'];
    $floor6891 = $row6891['floor'];
    $room6891 = $row6891['room'];
    $status6891 = $row6891['status'];
    $assignedName6891 = $row6891['assignedName'];
    $assignedBy6891 = $row6891['assignedBy'];
    $upload_img6891 = $row6891['upload_img'];
    $description6891 = $row6891['description'];

    //FOR ID 6890
    $sql6890 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6890";
    $stmt6890 = $conn->prepare($sql6890);
    $stmt6890->execute();
    $result6890 = $stmt6890->get_result();
    $row6890 = $result6890->fetch_assoc();
    $assetId6890 = $row6890['assetId'];
    $category6890 = $row6890['category'];
    $date6890 = $row6890['date'];
    $building6890 = $row6890['building'];
    $floor6890 = $row6890['floor'];
    $room6890 = $row6890['room'];
    $status6890 = $row6890['status'];
    $assignedName6890 = $row6890['assignedName'];
    $assignedBy6890 = $row6890['assignedBy'];
    $upload_img6890 = $row6890['upload_img'];
    $description6890 = $row6890['description'];

    //FOR ID 6889
    $sql6889 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6889";
    $stmt6889 = $conn->prepare($sql6889);
    $stmt6889->execute();
    $result6889 = $stmt6889->get_result();
    $row6889 = $result6889->fetch_assoc();
    $assetId6889 = $row6889['assetId'];
    $category6889 = $row6889['category'];
    $date6889 = $row6889['date'];
    $building6889 = $row6889['building'];
    $floor6889 = $row6889['floor'];
    $room6889 = $row6889['room'];
    $status6889 = $row6889['status'];
    $assignedName6889 = $row6889['assignedName'];
    $assignedBy6889 = $row6889['assignedBy'];
    $upload_img6889 = $row6889['upload_img'];
    $description6889 = $row6889['description'];

    //FOR ID 6888
    $sql6888 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6888";
    $stmt6888 = $conn->prepare($sql6888);
    $stmt6888->execute();
    $result6888 = $stmt6888->get_result();
    $row6888 = $result6888->fetch_assoc();
    $assetId6888 = $row6888['assetId'];
    $category6888 = $row6888['category'];
    $date6888 = $row6888['date'];
    $building6888 = $row6888['building'];
    $floor6888 = $row6888['floor'];
    $room6888 = $row6888['room'];
    $status6888 = $row6888['status'];
    $assignedName6888 = $row6888['assignedName'];
    $assignedBy6888 = $row6888['assignedBy'];
    $upload_img6888 = $row6888['upload_img'];
    $description6888 = $row6888['description'];

    //FOR ID 6887
    $sql6887 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6887";
    $stmt6887 = $conn->prepare($sql6887);
    $stmt6887->execute();
    $result6887 = $stmt6887->get_result();
    $row6887 = $result6887->fetch_assoc();
    $assetId6887 = $row6887['assetId'];
    $category6887 = $row6887['category'];
    $date6887 = $row6887['date'];
    $building6887 = $row6887['building'];
    $floor6887 = $row6887['floor'];
    $room6887 = $row6887['room'];
    $status6887 = $row6887['status'];
    $assignedName6887 = $row6887['assignedName'];
    $assignedBy6887 = $row6887['assignedBy'];
    $upload_img6887 = $row6887['upload_img'];
    $description6887 = $row6887['description'];
    //FOR ID 6886
    $sql6886 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6886";
    $stmt6886 = $conn->prepare($sql6886);
    $stmt6886->execute();
    $result6886 = $stmt6886->get_result();
    $row6886 = $result6886->fetch_assoc();
    $assetId6886 = $row6886['assetId'];
    $category6886 = $row6886['category'];
    $date6886 = $row6886['date'];
    $building6886 = $row6886['building'];
    $floor6886 = $row6886['floor'];
    $room6886 = $row6886['room'];
    $status6886 = $row6886['status'];
    $assignedName6886 = $row6886['assignedName'];
    $assignedBy6886 = $row6886['assignedBy'];
    $upload_img6886 = $row6886['upload_img'];
    $description6886 = $row6886['description'];
    //FOR ID 6885
    $sql6885 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6885";
    $stmt6885 = $conn->prepare($sql6885);
    $stmt6885->execute();
    $result6885 = $stmt6885->get_result();
    $row6885 = $result6885->fetch_assoc();
    $assetId6885 = $row6885['assetId'];
    $category6885 = $row6885['category'];
    $date6885 = $row6885['date'];
    $building6885 = $row6885['building'];
    $floor6885 = $row6885['floor'];
    $room6885 = $row6885['room'];
    $status6885 = $row6885['status'];
    $assignedName6885 = $row6885['assignedName'];
    $assignedBy6885 = $row6885['assignedBy'];
    $upload_img6885 = $row6885['upload_img'];
    $description6885 = $row6885['description'];
    //FOR ID 6884
    $sql6884 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6884";
    $stmt6884 = $conn->prepare($sql6884);
    $stmt6884->execute();
    $result6884 = $stmt6884->get_result();
    $row6884 = $result6884->fetch_assoc();
    $assetId6884 = $row6884['assetId'];
    $category6884 = $row6884['category'];
    $date6884 = $row6884['date'];
    $building6884 = $row6884['building'];
    $floor6884 = $row6884['floor'];
    $room6884 = $row6884['room'];
    $status6884 = $row6884['status'];
    $assignedName6884 = $row6884['assignedName'];
    $assignedBy6884 = $row6884['assignedBy'];
    $upload_img6884 = $row6884['upload_img'];
    $description6884 = $row6884['description'];

    //FOR ID 6883
    $sql6883 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6883";
    $stmt6883 = $conn->prepare($sql6883);
    $stmt6883->execute();
    $result6883 = $stmt6883->get_result();
    $row6883 = $result6883->fetch_assoc();
    $assetId6883 = $row6883['assetId'];
    $category6883 = $row6883['category'];
    $date6883 = $row6883['date'];
    $building6883 = $row6883['building'];
    $floor6883 = $row6883['floor'];
    $room6883 = $row6883['room'];
    $status6883 = $row6883['status'];
    $assignedName6883 = $row6883['assignedName'];
    $assignedBy6883 = $row6883['assignedBy'];
    $upload_img6883 = $row6883['upload_img'];
    $description6883 = $row6883['description'];

    //FOR ID 6882
    $sql6882 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6882";
    $stmt6882 = $conn->prepare($sql6882);
    $stmt6882->execute();
    $result6882 = $stmt6882->get_result();
    $row6882 = $result6882->fetch_assoc();
    $assetId6882 = $row6882['assetId'];
    $category6882 = $row6882['category'];
    $date6882 = $row6882['date'];
    $building6882 = $row6882['building'];
    $floor6882 = $row6882['floor'];
    $room6882 = $row6882['room'];
    $status6882 = $row6882['status'];
    $assignedName6882 = $row6882['assignedName'];
    $assignedBy6882 = $row6882['assignedBy'];
    $upload_img6882 = $row6882['upload_img'];
    $description6882 = $row6882['description'];

    //FOR ID 6881
    $sql6881 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6881";
    $stmt6881 = $conn->prepare($sql6881);
    $stmt6881->execute();
    $result6881 = $stmt6881->get_result();
    $row6881 = $result6881->fetch_assoc();
    $assetId6881 = $row6881['assetId'];
    $category6881 = $row6881['category'];
    $date6881 = $row6881['date'];
    $building6881 = $row6881['building'];
    $floor6881 = $row6881['floor'];
    $room6881 = $row6881['room'];
    $status6881 = $row6881['status'];
    $assignedName6881 = $row6881['assignedName'];
    $assignedBy6881 = $row6881['assignedBy'];
    $upload_img6881 = $row6881['upload_img'];
    $description6881 = $row6881['description'];
    //FOR ID 6880
    $sql6880 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6880";
    $stmt6880 = $conn->prepare($sql6880);
    $stmt6880->execute();
    $result6880 = $stmt6880->get_result();
    $row6880 = $result6880->fetch_assoc();
    $assetId6880 = $row6880['assetId'];
    $category6880 = $row6880['category'];
    $date6880 = $row6880['date'];
    $building6880 = $row6880['building'];
    $floor6880 = $row6880['floor'];
    $room6880 = $row6880['room'];
    $status6880 = $row6880['status'];
    $assignedName6880 = $row6880['assignedName'];
    $assignedBy6880 = $row6880['assignedBy'];
    $upload_img6880 = $row6880['upload_img'];
    $description6880 = $row6880['description'];

    //FOR ID 6879
    $sql6879 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6879";
    $stmt6879 = $conn->prepare($sql6879);
    $stmt6879->execute();
    $result6879 = $stmt6879->get_result();
    $row6879 = $result6879->fetch_assoc();
    $assetId6879 = $row6879['assetId'];
    $category6879 = $row6879['category'];
    $date6879 = $row6879['date'];
    $building6879 = $row6879['building'];
    $floor6879 = $row6879['floor'];
    $room6879 = $row6879['room'];
    $status6879 = $row6879['status'];
    $assignedName6879 = $row6879['assignedName'];
    $assignedBy6879 = $row6879['assignedBy'];
    $upload_img6879 = $row6879['upload_img'];
    $description6879 = $row6879['description'];

    //FOR ID 6878
    $sql6878 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6878";
    $stmt6878 = $conn->prepare($sql6878);
    $stmt6878->execute();
    $result6878 = $stmt6878->get_result();
    $row6878 = $result6878->fetch_assoc();
    $assetId6878 = $row6878['assetId'];
    $category6878 = $row6878['category'];
    $date6878 = $row6878['date'];
    $building6878 = $row6878['building'];
    $floor6878 = $row6878['floor'];
    $room6878 = $row6878['room'];
    $status6878 = $row6878['status'];
    $assignedName6878 = $row6878['assignedName'];
    $assignedBy6878 = $row6878['assignedBy'];
    $upload_img6878 = $row6878['upload_img'];
    $description6878 = $row6878['description'];

    //FOR ID 6877
    $sql6877 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6877";
    $stmt6877 = $conn->prepare($sql6877);
    $stmt6877->execute();
    $result6877 = $stmt6877->get_result();
    $row6877 = $result6877->fetch_assoc();
    $assetId6877 = $row6877['assetId'];
    $category6877 = $row6877['category'];
    $date6877 = $row6877['date'];
    $building6877 = $row6877['building'];
    $floor6877 = $row6877['floor'];
    $room6877 = $row6877['room'];
    $status6877 = $row6877['status'];
    $assignedName6877 = $row6877['assignedName'];
    $assignedBy6877 = $row6877['assignedBy'];
    $upload_img6877 = $row6877['upload_img'];
    $description6877 = $row6877['description'];

    //FOR ID 6876
    $sql6876 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6876";
    $stmt6876 = $conn->prepare($sql6876);
    $stmt6876->execute();
    $result6876 = $stmt6876->get_result();
    $row6876 = $result6876->fetch_assoc();
    $assetId6876 = $row6876['assetId'];
    $category6876 = $row6876['category'];
    $date6876 = $row6876['date'];
    $building6876 = $row6876['building'];
    $floor6876 = $row6876['floor'];
    $room6876 = $row6876['room'];
    $status6876 = $row6876['status'];
    $assignedName6876 = $row6876['assignedName'];
    $assignedBy6876 = $row6876['assignedBy'];
    $upload_img6876 = $row6876['upload_img'];
    $description6876 = $row6876['description'];

    //FOR ID 6875
    $sql6875 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6875";
    $stmt6875 = $conn->prepare($sql6875);
    $stmt6875->execute();
    $result6875 = $stmt6875->get_result();
    $row6875 = $result6875->fetch_assoc();
    $assetId6875 = $row6875['assetId'];
    $category6875 = $row6875['category'];
    $date6875 = $row6875['date'];
    $building6875 = $row6875['building'];
    $floor6875 = $row6875['floor'];
    $room6875 = $row6875['room'];
    $status6875 = $row6875['status'];
    $assignedName6875 = $row6875['assignedName'];
    $assignedBy6875 = $row6875['assignedBy'];
    $upload_img6875 = $row6875['upload_img'];
    $description6875 = $row6875['description'];

    //FOR ID 6874
    $sql6874 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6874";
    $stmt6874 = $conn->prepare($sql6874);
    $stmt6874->execute();
    $result6874 = $stmt6874->get_result();
    $row6874 = $result6874->fetch_assoc();
    $assetId6874 = $row6874['assetId'];
    $category6874 = $row6874['category'];
    $date6874 = $row6874['date'];
    $building6874 = $row6874['building'];
    $floor6874 = $row6874['floor'];
    $room6874 = $row6874['room'];
    $status6874 = $row6874['status'];
    $assignedName6874 = $row6874['assignedName'];
    $assignedBy6874 = $row6874['assignedBy'];
    $upload_img6874 = $row6874['upload_img'];
    $description6874 = $row6874['description'];

    //FOR ID 6873
    $sql6873 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6873";
    $stmt6873 = $conn->prepare($sql6873);
    $stmt6873->execute();
    $result6873 = $stmt6873->get_result();
    $row6873 = $result6873->fetch_assoc();
    $assetId6873 = $row6873['assetId'];
    $category6873 = $row6873['category'];
    $date6873 = $row6873['date'];
    $building6873 = $row6873['building'];
    $floor6873 = $row6873['floor'];
    $room6873 = $row6873['room'];
    $status6873 = $row6873['status'];
    $assignedName6873 = $row6873['assignedName'];
    $assignedBy6873 = $row6873['assignedBy'];
    $upload_img6873 = $row6873['upload_img'];
    $description6873 = $row6873['description'];

    //FOR ID 6872
    $sql6872 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6872";
    $stmt6872 = $conn->prepare($sql6872);
    $stmt6872->execute();
    $result6872 = $stmt6872->get_result();
    $row6872 = $result6872->fetch_assoc();
    $assetId6872 = $row6872['assetId'];
    $category6872 = $row6872['category'];
    $date6872 = $row6872['date'];
    $building6872 = $row6872['building'];
    $floor6872 = $row6872['floor'];
    $room6872 = $row6872['room'];
    $status6872 = $row6872['status'];
    $assignedName6872 = $row6872['assignedName'];
    $assignedBy6872 = $row6872['assignedBy'];
    $upload_img6872 = $row6872['upload_img'];
    $description6872 = $row6872['description'];

    //FOR ID 6871
    $sql6871 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6871";
    $stmt6871 = $conn->prepare($sql6871);
    $stmt6871->execute();
    $result6871 = $stmt6871->get_result();
    $row6871 = $result6871->fetch_assoc();
    $assetId6871 = $row6871['assetId'];
    $category6871 = $row6871['category'];
    $date6871 = $row6871['date'];
    $building6871 = $row6871['building'];
    $floor6871 = $row6871['floor'];
    $room6871 = $row6871['room'];
    $status6871 = $row6871['status'];
    $assignedName6871 = $row6871['assignedName'];
    $assignedBy6871 = $row6871['assignedBy'];
    $upload_img6871 = $row6871['upload_img'];
    $description6871 = $row6871['description'];

    //FOR ID 6870
    $sql6870 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6870";
    $stmt6870 = $conn->prepare($sql6870);
    $stmt6870->execute();
    $result6870 = $stmt6870->get_result();
    $row6870 = $result6870->fetch_assoc();
    $assetId6870 = $row6870['assetId'];
    $category6870 = $row6870['category'];
    $date6870 = $row6870['date'];
    $building6870 = $row6870['building'];
    $floor6870 = $row6870['floor'];
    $room6870 = $row6870['room'];
    $status6870 = $row6870['status'];
    $assignedName6870 = $row6870['assignedName'];
    $assignedBy6870 = $row6870['assignedBy'];
    $upload_img6870 = $row6870['upload_img'];
    $description6870 = $row6870['description'];

    //FOR ID 6869
    $sql6869 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6869";
    $stmt6869 = $conn->prepare($sql6869);
    $stmt6869->execute();
    $result6869 = $stmt6869->get_result();
    $row6869 = $result6869->fetch_assoc();
    $assetId6869 = $row6869['assetId'];
    $category6869 = $row6869['category'];
    $date6869 = $row6869['date'];
    $building6869 = $row6869['building'];
    $floor6869 = $row6869['floor'];
    $room6869 = $row6869['room'];
    $status6869 = $row6869['status'];
    $assignedName6869 = $row6869['assignedName'];
    $assignedBy6869 = $row6869['assignedBy'];
    $upload_img6869 = $row6869['upload_img'];
    $description6869 = $row6869['description'];

    //FOR ID 6868
    $sql6868 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6868";
    $stmt6868 = $conn->prepare($sql6868);
    $stmt6868->execute();
    $result6868 = $stmt6868->get_result();
    $row6868 = $result6868->fetch_assoc();
    $assetId6868 = $row6868['assetId'];
    $category6868 = $row6868['category'];
    $date6868 = $row6868['date'];
    $building6868 = $row6868['building'];
    $floor6868 = $row6868['floor'];
    $room6868 = $row6868['room'];
    $status6868 = $row6868['status'];
    $assignedName6868 = $row6868['assignedName'];
    $assignedBy6868 = $row6868['assignedBy'];
    $upload_img6868 = $row6868['upload_img'];
    $description6868 = $row6868['description'];

    //FOR ID 6867
    $sql6867 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6867";
    $stmt6867 = $conn->prepare($sql6867);
    $stmt6867->execute();
    $result6867 = $stmt6867->get_result();
    $row6867 = $result6867->fetch_assoc();
    $assetId6867 = $row6867['assetId'];
    $category6867 = $row6867['category'];
    $date6867 = $row6867['date'];
    $building6867 = $row6867['building'];
    $floor6867 = $row6867['floor'];
    $room6867 = $row6867['room'];
    $status6867 = $row6867['status'];
    $assignedName6867 = $row6867['assignedName'];
    $assignedBy6867 = $row6867['assignedBy'];
    $upload_img6867 = $row6867['upload_img'];
    $description6867 = $row6867['description'];

    //FOR ID 7269
    $sql7269 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7269";
    $stmt7269 = $conn->prepare($sql7269);
    $stmt7269->execute();
    $result7269 = $stmt7269->get_result();
    $row7269 = $result7269->fetch_assoc();
    $assetId7269 = $row7269['assetId'];
    $category7269 = $row7269['category'];
    $date7269 = $row7269['date'];
    $building7269 = $row7269['building'];
    $floor7269 = $row7269['floor'];
    $room7269 = $row7269['room'];
    $status7269 = $row7269['status'];
    $assignedName7269 = $row7269['assignedName'];
    $assignedBy7269 = $row7269['assignedBy'];
    $upload_img7269 = $row7269['upload_img'];
    $description7269 = $row7269['description'];

    //FOR ID 7268
    $sql7268 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7268";
    $stmt7268 = $conn->prepare($sql7268);
    $stmt7268->execute();
    $result7268 = $stmt7268->get_result();
    $row7268 = $result7268->fetch_assoc();
    $assetId7268 = $row7268['assetId'];
    $category7268 = $row7268['category'];
    $date7268 = $row7268['date'];
    $building7268 = $row7268['building'];
    $floor7268 = $row7268['floor'];
    $room7268 = $row7268['room'];
    $status7268 = $row7268['status'];
    $assignedName7268 = $row7268['assignedName'];
    $assignedBy7268 = $row7268['assignedBy'];
    $upload_img7268 = $row7268['upload_img'];
    $description7268 = $row7268['description'];

    //FOR ID 7267
    $sql7267 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7267";
    $stmt7267 = $conn->prepare($sql7267);
    $stmt7267->execute();
    $result7267 = $stmt7267->get_result();
    $row7267 = $result7267->fetch_assoc();
    $assetId7267 = $row7267['assetId'];
    $category7267 = $row7267['category'];
    $date7267 = $row7267['date'];
    $building7267 = $row7267['building'];
    $floor7267 = $row7267['floor'];
    $room7267 = $row7267['room'];
    $status7267 = $row7267['status'];
    $assignedName7267 = $row7267['assignedName'];
    $assignedBy7267 = $row7267['assignedBy'];
    $upload_img7267 = $row7267['upload_img'];
    $description7267 = $row7267['description'];

    //FOR ID 6948
    $sql6948 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6948";
    $stmt6948 = $conn->prepare($sql6948);
    $stmt6948->execute();
    $result6948 = $stmt6948->get_result();
    $row6948 = $result6948->fetch_assoc();
    $assetId6948 = $row6948['assetId'];
    $category6948 = $row6948['category'];
    $date6948 = $row6948['date'];
    $building6948 = $row6948['building'];
    $floor6948 = $row6948['floor'];
    $room6948 = $row6948['room'];
    $status6948 = $row6948['status'];
    $assignedName6948 = $row6948['assignedName'];
    $assignedBy6948 = $row6948['assignedBy'];
    $upload_img6948 = $row6948['upload_img'];
    $description6948 = $row6948['description'];

    //FOR ID 6949
    $sql6949 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6949";
    $stmt6949 = $conn->prepare($sql6949);
    $stmt6949->execute();
    $result6949 = $stmt6949->get_result();
    $row6949 = $result6949->fetch_assoc();
    $assetId6949 = $row6949['assetId'];
    $category6949 = $row6949['category'];
    $date6949 = $row6949['date'];
    $building6949 = $row6949['building'];
    $floor6949 = $row6949['floor'];
    $room6949 = $row6949['room'];
    $status6949 = $row6949['status'];
    $assignedName6949 = $row6949['assignedName'];
    $assignedBy6949 = $row6949['assignedBy'];
    $upload_img6949 = $row6949['upload_img'];
    $description6949 = $row6949['description'];

    //FOR ID 6950
    $sql6950 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6950";
    $stmt6950 = $conn->prepare($sql6950);
    $stmt6950->execute();
    $result6950 = $stmt6950->get_result();
    $row6950 = $result6950->fetch_assoc();
    $assetId6950 = $row6950['assetId'];
    $category6950 = $row6950['category'];
    $date6950 = $row6950['date'];
    $building6950 = $row6950['building'];
    $floor6950 = $row6950['floor'];
    $room6950 = $row6950['room'];
    $status6950 = $row6950['status'];
    $assignedName6950 = $row6950['assignedName'];
    $assignedBy6950 = $row6950['assignedBy'];
    $upload_img6950 = $row6950['upload_img'];
    $description6950 = $row6950['description'];

    //FOR ID 6951
    $sql6951 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6951";
    $stmt6951 = $conn->prepare($sql6951);
    $stmt6951->execute();
    $result6951 = $stmt6951->get_result();
    $row6951 = $result6951->fetch_assoc();
    $assetId6951 = $row6951['assetId'];
    $category6951 = $row6951['category'];
    $date6951 = $row6951['date'];
    $building6951 = $row6951['building'];
    $floor6951 = $row6951['floor'];
    $room6951 = $row6951['room'];
    $status6951 = $row6951['status'];
    $assignedName6951 = $row6951['assignedName'];
    $assignedBy6951 = $row6951['assignedBy'];
    $upload_img6951 = $row6951['upload_img'];
    $description6951 = $row6951['description'];

    //FOR ID 6952
    $sql6952 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6952";
    $stmt6952 = $conn->prepare($sql6952);
    $stmt6952->execute();
    $result6952 = $stmt6952->get_result();
    $row6952 = $result6952->fetch_assoc();
    $assetId6952 = $row6952['assetId'];
    $category6952 = $row6952['category'];
    $date6952 = $row6952['date'];
    $building6952 = $row6952['building'];
    $floor6952 = $row6952['floor'];
    $room6952 = $row6952['room'];
    $status6952 = $row6952['status'];
    $assignedName6952 = $row6952['assignedName'];
    $assignedBy6952 = $row6952['assignedBy'];
    $upload_img6952 = $row6952['upload_img'];
    $description6952 = $row6952['description'];

    //FOR ID 6953
    $sql6953 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6953";
    $stmt6953 = $conn->prepare($sql6953);
    $stmt6953->execute();
    $result6953 = $stmt6953->get_result();
    $row6953 = $result6953->fetch_assoc();
    $assetId6953 = $row6953['assetId'];
    $category6953 = $row6953['category'];
    $date6953 = $row6953['date'];
    $building6953 = $row6953['building'];
    $floor6953 = $row6953['floor'];
    $room6953 = $row6953['room'];
    $status6953 = $row6953['status'];
    $assignedName6953 = $row6953['assignedName'];
    $assignedBy6953 = $row6953['assignedBy'];
    $upload_img6953 = $row6953['upload_img'];
    $description6953 = $row6953['description'];

    //FOR ID 6954
    $sql6954 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6954";
    $stmt6954 = $conn->prepare($sql6954);
    $stmt6954->execute();
    $result6954 = $stmt6954->get_result();
    $row6954 = $result6954->fetch_assoc();
    $assetId6954 = $row6954['assetId'];
    $category6954 = $row6954['category'];
    $date6954 = $row6954['date'];
    $building6954 = $row6954['building'];
    $floor6954 = $row6954['floor'];
    $room6954 = $row6954['room'];
    $status6954 = $row6954['status'];
    $assignedName6954 = $row6954['assignedName'];
    $assignedBy6954 = $row6954['assignedBy'];
    $upload_img6954 = $row6954['upload_img'];
    $description6954 = $row6954['description'];

    //FOR ID 6955
    $sql6955 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6955";
    $stmt6955 = $conn->prepare($sql6955);
    $stmt6955->execute();
    $result6955 = $stmt6955->get_result();
    $row6955 = $result6955->fetch_assoc();
    $assetId6955 = $row6955['assetId'];
    $category6955 = $row6955['category'];
    $date6955 = $row6955['date'];
    $building6955 = $row6955['building'];
    $floor6955 = $row6955['floor'];
    $room6955 = $row6955['room'];
    $status6955 = $row6955['status'];
    $assignedName6955 = $row6955['assignedName'];
    $assignedBy6955 = $row6955['assignedBy'];
    $upload_img6955 = $row6955['upload_img'];
    $description6955 = $row6955['description'];

    //FOR ID 6956
    $sql6956 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6956";
    $stmt6956 = $conn->prepare($sql6956);
    $stmt6956->execute();
    $result6956 = $stmt6956->get_result();
    $row6956 = $result6956->fetch_assoc();
    $assetId6956 = $row6956['assetId'];
    $category6956 = $row6956['category'];
    $date6956 = $row6956['date'];
    $building6956 = $row6956['building'];
    $floor6956 = $row6956['floor'];
    $room6956 = $row6956['room'];
    $status6956 = $row6956['status'];
    $assignedName6956 = $row6956['assignedName'];
    $assignedBy6956 = $row6956['assignedBy'];
    $upload_img6956 = $row6956['upload_img'];
    $description6956 = $row6956['description'];

    //FOR ID 6957
    $sql6957 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6957";
    $stmt6957 = $conn->prepare($sql6957);
    $stmt6957->execute();
    $result6957 = $stmt6957->get_result();
    $row6957 = $result6957->fetch_assoc();
    $assetId6957 = $row6957['assetId'];
    $category6957 = $row6957['category'];
    $date6957 = $row6957['date'];
    $building6957 = $row6957['building'];
    $floor6957 = $row6957['floor'];
    $room6957 = $row6957['room'];
    $status6957 = $row6957['status'];
    $assignedName6957 = $row6957['assignedName'];
    $assignedBy6957 = $row6957['assignedBy'];
    $upload_img6957 = $row6957['upload_img'];
    $description6957 = $row6957['description'];

    //FOR ID 6958
    $sql6958 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6958";
    $stmt6958 = $conn->prepare($sql6958);
    $stmt6958->execute();
    $result6958 = $stmt6958->get_result();
    $row6958 = $result6958->fetch_assoc();
    $assetId6958 = $row6958['assetId'];
    $category6958 = $row6958['category'];
    $date6958 = $row6958['date'];
    $building6958 = $row6958['building'];
    $floor6958 = $row6958['floor'];
    $room6958 = $row6958['room'];
    $status6958 = $row6958['status'];
    $assignedName6958 = $row6958['assignedName'];
    $assignedBy6958 = $row6958['assignedBy'];
    $upload_img6958 = $row6958['upload_img'];
    $description6958 = $row6958['description'];

    //FOR ID 6959
    $sql6959 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6959";
    $stmt6959 = $conn->prepare($sql6959);
    $stmt6959->execute();
    $result6959 = $stmt6959->get_result();
    $row6959 = $result6959->fetch_assoc();
    $assetId6959 = $row6959['assetId'];
    $category6959 = $row6959['category'];
    $date6959 = $row6959['date'];
    $building6959 = $row6959['building'];
    $floor6959 = $row6959['floor'];
    $room6959 = $row6959['room'];
    $status6959 = $row6959['status'];
    $assignedName6959 = $row6959['assignedName'];
    $assignedBy6959 = $row6959['assignedBy'];
    $upload_img6959 = $row6959['upload_img'];
    $description6959 = $row6959['description'];

    //FOR ID 6960
    $sql6960 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6960";
    $stmt6960 = $conn->prepare($sql6960);
    $stmt6960->execute();
    $result6960 = $stmt6960->get_result();
    $row6960 = $result6960->fetch_assoc();
    $assetId6960 = $row6960['assetId'];
    $category6960 = $row6960['category'];
    $date6960 = $row6960['date'];
    $building6960 = $row6960['building'];
    $floor6960 = $row6960['floor'];
    $room6960 = $row6960['room'];
    $status6960 = $row6960['status'];
    $assignedName6960 = $row6960['assignedName'];
    $assignedBy6960 = $row6960['assignedBy'];
    $upload_img6960 = $row6960['upload_img'];
    $description6960 = $row6960['description'];

    //FOR ID 6961
    $sql6961 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6961";
    $stmt6961 = $conn->prepare($sql6961);
    $stmt6961->execute();
    $result6961 = $stmt6961->get_result();
    $row6961 = $result6961->fetch_assoc();
    $assetId6961 = $row6961['assetId'];
    $category6961 = $row6961['category'];
    $date6961 = $row6961['date'];
    $building6961 = $row6961['building'];
    $floor6961 = $row6961['floor'];
    $room6961 = $row6961['room'];
    $status6961 = $row6961['status'];
    $assignedName6961 = $row6961['assignedName'];
    $assignedBy6961 = $row6961['assignedBy'];
    $upload_img6961 = $row6961['upload_img'];
    $description6961 = $row6961['description'];


    //FOR ID 6962
    $sql6962 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6962";
    $stmt6962 = $conn->prepare($sql6962);
    $stmt6962->execute();
    $result6962 = $stmt6962->get_result();
    $row6962 = $result6962->fetch_assoc();
    $assetId6962 = $row6962['assetId'];
    $category6962 = $row6962['category'];
    $date6962 = $row6962['date'];
    $building6962 = $row6962['building'];
    $floor6962 = $row6962['floor'];
    $room6962 = $row6962['room'];
    $status6962 = $row6962['status'];
    $assignedName6962 = $row6962['assignedName'];
    $assignedBy6962 = $row6962['assignedBy'];
    $upload_img6962 = $row6962['upload_img'];
    $description6962 = $row6962['description'];

    //FOR ID 6963
    $sql6963 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6963";
    $stmt6963 = $conn->prepare($sql6963);
    $stmt6963->execute();
    $result6963 = $stmt6963->get_result();
    $row6963 = $result6963->fetch_assoc();
    $assetId6963 = $row6963['assetId'];
    $category6963 = $row6963['category'];
    $date6963 = $row6963['date'];
    $building6963 = $row6963['building'];
    $floor6963 = $row6963['floor'];
    $room6963 = $row6963['room'];
    $status6963 = $row6963['status'];
    $assignedName6963 = $row6963['assignedName'];
    $assignedBy6963 = $row6963['assignedBy'];
    $upload_img6963 = $row6963['upload_img'];
    $description6963 = $row6963['description'];

    //FOR ID 6964
    $sql6964 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6964";
    $stmt6964 = $conn->prepare($sql6964);
    $stmt6964->execute();
    $result6964 = $stmt6964->get_result();
    $row6964 = $result6964->fetch_assoc();
    $assetId6964 = $row6964['assetId'];
    $category6964 = $row6964['category'];
    $date6964 = $row6964['date'];
    $building6964 = $row6964['building'];
    $floor6964 = $row6964['floor'];
    $room6964 = $row6964['room'];
    $status6964 = $row6964['status'];
    $assignedName6964 = $row6964['assignedName'];
    $assignedBy6964 = $row6964['assignedBy'];
    $upload_img6964 = $row6964['upload_img'];
    $description6964 = $row6964['description'];

    //FOR ID 6965
    $sql6965 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6965";
    $stmt6965 = $conn->prepare($sql6965);
    $stmt6965->execute();
    $result6965 = $stmt6965->get_result();
    $row6965 = $result6965->fetch_assoc();
    $assetId6965 = $row6965['assetId'];
    $category6965 = $row6965['category'];
    $date6965 = $row6965['date'];
    $building6965 = $row6965['building'];
    $floor6965 = $row6965['floor'];
    $room6965 = $row6965['room'];
    $status6965 = $row6965['status'];
    $assignedName6965 = $row6965['assignedName'];
    $assignedBy6965 = $row6965['assignedBy'];
    $upload_img6965 = $row6965['upload_img'];
    $description6965 = $row6965['description'];

    //FOR ID 6966
    $sql6966 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6966";
    $stmt6966 = $conn->prepare($sql6966);
    $stmt6966->execute();
    $result6966 = $stmt6966->get_result();
    $row6966 = $result6966->fetch_assoc();
    $assetId6966 = $row6966['assetId'];
    $category6966 = $row6966['category'];
    $date6966 = $row6966['date'];
    $building6966 = $row6966['building'];
    $floor6966 = $row6966['floor'];
    $room6966 = $row6966['room'];
    $status6966 = $row6966['status'];
    $assignedName6966 = $row6966['assignedName'];
    $assignedBy6966 = $row6966['assignedBy'];
    $upload_img6966 = $row6966['upload_img'];
    $description6966 = $row6966['description'];

    //FOR ID 6967
    $sql6967 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6967";
    $stmt6967 = $conn->prepare($sql6967);
    $stmt6967->execute();
    $result6967 = $stmt6967->get_result();
    $row6967 = $result6967->fetch_assoc();
    $assetId6967 = $row6967['assetId'];
    $category6967 = $row6967['category'];
    $date6967 = $row6967['date'];
    $building6967 = $row6967['building'];
    $floor6967 = $row6967['floor'];
    $room6967 = $row6967['room'];
    $status6967 = $row6967['status'];
    $assignedName6967 = $row6967['assignedName'];
    $assignedBy6967 = $row6967['assignedBy'];
    $upload_img6967 = $row6967['upload_img'];
    $description6967 = $row6967['description'];

    //FOR ID 6968
    $sql6968 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6968";
    $stmt6968 = $conn->prepare($sql6968);
    $stmt6968->execute();
    $result6968 = $stmt6968->get_result();
    $row6968 = $result6968->fetch_assoc();
    $assetId6968 = $row6968['assetId'];
    $category6968 = $row6968['category'];
    $date6968 = $row6968['date'];
    $building6968 = $row6968['building'];
    $floor6968 = $row6968['floor'];
    $room6968 = $row6968['room'];
    $status6968 = $row6968['status'];
    $assignedName6968 = $row6968['assignedName'];
    $assignedBy6968 = $row6968['assignedBy'];
    $upload_img6968 = $row6968['upload_img'];
    $description6968 = $row6968['description'];

    //FOR ID 6969
    $sql6969 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6969";
    $stmt6969 = $conn->prepare($sql6969);
    $stmt6969->execute();
    $result6969 = $stmt6969->get_result();
    $row6969 = $result6969->fetch_assoc();
    $assetId6969 = $row6969['assetId'];
    $category6969 = $row6969['category'];
    $date6969 = $row6969['date'];
    $building6969 = $row6969['building'];
    $floor6969 = $row6969['floor'];
    $room6969 = $row6969['room'];
    $status6969 = $row6969['status'];
    $assignedName6969 = $row6969['assignedName'];
    $assignedBy6969 = $row6969['assignedBy'];
    $upload_img6969 = $row6969['upload_img'];
    $description6969 = $row6969['description'];

    //FOR ID 6970
    $sql6970 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6970";
    $stmt6970 = $conn->prepare($sql6970);
    $stmt6970->execute();
    $result6970 = $stmt6970->get_result();
    $row6970 = $result6970->fetch_assoc();
    $assetId6970 = $row6970['assetId'];
    $category6970 = $row6970['category'];
    $date6970 = $row6970['date'];
    $building6970 = $row6970['building'];
    $floor6970 = $row6970['floor'];
    $room6970 = $row6970['room'];
    $status6970 = $row6970['status'];
    $assignedName6970 = $row6970['assignedName'];
    $assignedBy6970 = $row6970['assignedBy'];
    $upload_img6970 = $row6970['upload_img'];
    $description6970 = $row6970['description'];

    //FOR ID 6971
    $sql6971 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6971";
    $stmt6971 = $conn->prepare($sql6971);
    $stmt6971->execute();
    $result6971 = $stmt6971->get_result();
    $row6971 = $result6971->fetch_assoc();
    $assetId6971 = $row6971['assetId'];
    $category6971 = $row6971['category'];
    $date6971 = $row6971['date'];
    $building6971 = $row6971['building'];
    $floor6971 = $row6971['floor'];
    $room6971 = $row6971['room'];
    $status6971 = $row6971['status'];
    $assignedName6971 = $row6971['assignedName'];
    $assignedBy6971 = $row6971['assignedBy'];
    $upload_img6971 = $row6971['upload_img'];
    $description6971 = $row6971['description'];

    //FOR ID 6972
    $sql6972 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6972";
    $stmt6972 = $conn->prepare($sql6972);
    $stmt6972->execute();
    $result6972 = $stmt6972->get_result();
    $row6972 = $result6972->fetch_assoc();
    $assetId6972 = $row6972['assetId'];
    $category6972 = $row6972['category'];
    $date6972 = $row6972['date'];
    $building6972 = $row6972['building'];
    $floor6972 = $row6972['floor'];
    $room6972 = $row6972['room'];
    $status6972 = $row6972['status'];
    $assignedName6972 = $row6972['assignedName'];
    $assignedBy6972 = $row6972['assignedBy'];
    $upload_img6972 = $row6972['upload_img'];
    $description6972 = $row6972['description'];

    //FOR ID 6973
    $sql6973 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6973";
    $stmt6973 = $conn->prepare($sql6973);
    $stmt6973->execute();
    $result6973 = $stmt6973->get_result();
    $row6973 = $result6973->fetch_assoc();
    $assetId6973 = $row6973['assetId'];
    $category6973 = $row6973['category'];
    $date6973 = $row6973['date'];
    $building6973 = $row6973['building'];
    $floor6973 = $row6973['floor'];
    $room6973 = $row6973['room'];
    $status6973 = $row6973['status'];
    $assignedName6973 = $row6973['assignedName'];
    $assignedBy6973 = $row6973['assignedBy'];
    $upload_img6973 = $row6973['upload_img'];
    $description6973 = $row6973['description'];

    //FOR ID 6974
    $sql6974 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6974";
    $stmt6974 = $conn->prepare($sql6974);
    $stmt6974->execute();
    $result6974 = $stmt6974->get_result();
    $row6974 = $result6974->fetch_assoc();
    $assetId6974 = $row6974['assetId'];
    $category6974 = $row6974['category'];
    $date6974 = $row6974['date'];
    $building6974 = $row6974['building'];
    $floor6974 = $row6974['floor'];
    $room6974 = $row6974['room'];
    $status6974 = $row6974['status'];
    $assignedName6974 = $row6974['assignedName'];
    $assignedBy6974 = $row6974['assignedBy'];
    $upload_img6974 = $row6974['upload_img'];
    $description6974 = $row6974['description'];

    //FOR ID 6975
    $sql6975 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6975";
    $stmt6975 = $conn->prepare($sql6975);
    $stmt6975->execute();
    $result6975 = $stmt6975->get_result();
    $row6975 = $result6975->fetch_assoc();
    $assetId6975 = $row6975['assetId'];
    $category6975 = $row6975['category'];
    $date6975 = $row6975['date'];
    $building6975 = $row6975['building'];
    $floor6975 = $row6975['floor'];
    $room6975 = $row6975['room'];
    $status6975 = $row6975['status'];
    $assignedName6975 = $row6975['assignedName'];
    $assignedBy6975 = $row6975['assignedBy'];
    $upload_img6975 = $row6975['upload_img'];
    $description6975 = $row6975['description'];

    //FOR ID 6976
    $sql6976 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6976";
    $stmt6976 = $conn->prepare($sql6976);
    $stmt6976->execute();
    $result6976 = $stmt6976->get_result();
    $row6976 = $result6976->fetch_assoc();
    $assetId6976 = $row6976['assetId'];
    $category6976 = $row6976['category'];
    $date6976 = $row6976['date'];
    $building6976 = $row6976['building'];
    $floor6976 = $row6976['floor'];
    $room6976 = $row6976['room'];
    $status6976 = $row6976['status'];
    $assignedName6976 = $row6976['assignedName'];
    $assignedBy6976 = $row6976['assignedBy'];
    $upload_img6976 = $row6976['upload_img'];
    $description6976 = $row6976['description'];

    //FOR ID 6977
    $sql6977 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6977";
    $stmt6977 = $conn->prepare($sql6977);
    $stmt6977->execute();
    $result6977 = $stmt6977->get_result();
    $row6977 = $result6977->fetch_assoc();
    $assetId6977 = $row6977['assetId'];
    $category6977 = $row6977['category'];
    $date6977 = $row6977['date'];
    $building6977 = $row6977['building'];
    $floor6977 = $row6977['floor'];
    $room6977 = $row6977['room'];
    $status6977 = $row6977['status'];
    $assignedName6977 = $row6977['assignedName'];
    $assignedBy6977 = $row6977['assignedBy'];
    $upload_img6977 = $row6977['upload_img'];
    $description6977 = $row6977['description'];

    //FOR ID 6978
    $sql6978 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6978";
    $stmt6978 = $conn->prepare($sql6978);
    $stmt6978->execute();
    $result6978 = $stmt6978->get_result();
    $row6978 = $result6978->fetch_assoc();
    $assetId6978 = $row6978['assetId'];
    $category6978 = $row6978['category'];
    $date6978 = $row6978['date'];
    $building6978 = $row6978['building'];
    $floor6978 = $row6978['floor'];
    $room6978 = $row6978['room'];
    $status6978 = $row6978['status'];
    $assignedName6978 = $row6978['assignedName'];
    $assignedBy6978 = $row6978['assignedBy'];
    $upload_img6978 = $row6978['upload_img'];
    $description6978 = $row6978['description'];

    //FOR ID 6979
    $sql6979 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6979";
    $stmt6979 = $conn->prepare($sql6979);
    $stmt6979->execute();
    $result6979 = $stmt6979->get_result();
    $row6979 = $result6979->fetch_assoc();
    $assetId6979 = $row6979['assetId'];
    $category6979 = $row6979['category'];
    $date6979 = $row6979['date'];
    $building6979 = $row6979['building'];
    $floor6979 = $row6979['floor'];
    $room6979 = $row6979['room'];
    $status6979 = $row6979['status'];
    $assignedName6979 = $row6979['assignedName'];
    $assignedBy6979 = $row6979['assignedBy'];
    $upload_img6979 = $row6979['upload_img'];
    $description6979 = $row6979['description'];

    //FOR ID 6980
    $sql6980 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6980";
    $stmt6980 = $conn->prepare($sql6980);
    $stmt6980->execute();
    $result6980 = $stmt6980->get_result();
    $row6980 = $result6980->fetch_assoc();
    $assetId6980 = $row6980['assetId'];
    $category6980 = $row6980['category'];
    $date6980 = $row6980['date'];
    $building6980 = $row6980['building'];
    $floor6980 = $row6980['floor'];
    $room6980 = $row6980['room'];
    $status6980 = $row6980['status'];
    $assignedName6980 = $row6980['assignedName'];
    $assignedBy6980 = $row6980['assignedBy'];
    $upload_img6980 = $row6980['upload_img'];
    $description6980 = $row6980['description'];

    //FOR ID 6981
    $sql6981 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6981";
    $stmt6981 = $conn->prepare($sql6981);
    $stmt6981->execute();
    $result6981 = $stmt6981->get_result();
    $row6981 = $result6981->fetch_assoc();
    $assetId6981 = $row6981['assetId'];
    $category6981 = $row6981['category'];
    $date6981 = $row6981['date'];
    $building6981 = $row6981['building'];
    $floor6981 = $row6981['floor'];
    $room6981 = $row6981['room'];
    $status6981 = $row6981['status'];
    $assignedName6981 = $row6981['assignedName'];
    $assignedBy6981 = $row6981['assignedBy'];
    $upload_img6981 = $row6981['upload_img'];
    $description6981 = $row6981['description'];

    //FOR ID 6982
    $sql6982 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6982";
    $stmt6982 = $conn->prepare($sql6982);
    $stmt6982->execute();
    $result6982 = $stmt6982->get_result();
    $row6982 = $result6982->fetch_assoc();
    $assetId6982 = $row6982['assetId'];
    $category6982 = $row6982['category'];
    $date6982 = $row6982['date'];
    $building6982 = $row6982['building'];
    $floor6982 = $row6982['floor'];
    $room6982 = $row6982['room'];
    $status6982 = $row6982['status'];
    $assignedName6982 = $row6982['assignedName'];
    $assignedBy6982 = $row6982['assignedBy'];
    $upload_img6982 = $row6982['upload_img'];
    $description6982 = $row6982['description'];

    //FOR ID 6983
    $sql6983 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6983";
    $stmt6983 = $conn->prepare($sql6983);
    $stmt6983->execute();
    $result6983 = $stmt6983->get_result();
    $row6983 = $result6983->fetch_assoc();
    $assetId6983 = $row6983['assetId'];
    $category6983 = $row6983['category'];
    $date6983 = $row6983['date'];
    $building6983 = $row6983['building'];
    $floor6983 = $row6983['floor'];
    $room6983 = $row6983['room'];
    $status6983 = $row6983['status'];
    $assignedName6983 = $row6983['assignedName'];
    $assignedBy6983 = $row6983['assignedBy'];
    $upload_img6983 = $row6983['upload_img'];
    $description6983 = $row6983['description'];

    //FOR ID 6984
    $sql6984 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6984";
    $stmt6984 = $conn->prepare($sql6984);
    $stmt6984->execute();
    $result6984 = $stmt6984->get_result();
    $row6984 = $result6984->fetch_assoc();
    $assetId6984 = $row6984['assetId'];
    $category6984 = $row6984['category'];
    $date6984 = $row6984['date'];
    $building6984 = $row6984['building'];
    $floor6984 = $row6984['floor'];
    $room6984 = $row6984['room'];
    $status6984 = $row6984['status'];
    $assignedName6984 = $row6984['assignedName'];
    $assignedBy6984 = $row6984['assignedBy'];
    $upload_img6984 = $row6984['upload_img'];
    $description6984 = $row6984['description'];

    //FOR ID 6985
    $sql6985 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6985";
    $stmt6985 = $conn->prepare($sql6985);
    $stmt6985->execute();
    $result6985 = $stmt6985->get_result();
    $row6985 = $result6985->fetch_assoc();
    $assetId6985 = $row6985['assetId'];
    $category6985 = $row6985['category'];
    $date6985 = $row6985['date'];
    $building6985 = $row6985['building'];
    $floor6985 = $row6985['floor'];
    $room6985 = $row6985['room'];
    $status6985 = $row6985['status'];
    $assignedName6985 = $row6985['assignedName'];
    $assignedBy6985 = $row6985['assignedBy'];
    $upload_img6985 = $row6985['upload_img'];
    $description6985 = $row6985['description'];

    //FOR ID 6986
    $sql6986 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6986";
    $stmt6986 = $conn->prepare($sql6986);
    $stmt6986->execute();
    $result6986 = $stmt6986->get_result();
    $row6986 = $result6986->fetch_assoc();
    $assetId6986 = $row6986['assetId'];
    $category6986 = $row6986['category'];
    $date6986 = $row6986['date'];
    $building6986 = $row6986['building'];
    $floor6986 = $row6986['floor'];
    $room6986 = $row6986['room'];
    $status6986 = $row6986['status'];
    $assignedName6986 = $row6986['assignedName'];
    $assignedBy6986 = $row6986['assignedBy'];
    $upload_img6986 = $row6986['upload_img'];
    $description6986 = $row6986['description'];

    //FOR ID 6987
    $sql6987 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6987";
    $stmt6987 = $conn->prepare($sql6987);
    $stmt6987->execute();
    $result6987 = $stmt6987->get_result();
    $row6987 = $result6987->fetch_assoc();
    $assetId6987 = $row6987['assetId'];
    $category6987 = $row6987['category'];
    $date6987 = $row6987['date'];
    $building6987 = $row6987['building'];
    $floor6987 = $row6987['floor'];
    $room6987 = $row6987['room'];
    $status6987 = $row6987['status'];
    $assignedName6987 = $row6987['assignedName'];
    $assignedBy6987 = $row6987['assignedBy'];
    $upload_img6987 = $row6987['upload_img'];
    $description6987 = $row6987['description'];

    //FOR ID 6988
    $sql6988 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6988";
    $stmt6988 = $conn->prepare($sql6988);
    $stmt6988->execute();
    $result6988 = $stmt6988->get_result();
    $row6988 = $result6988->fetch_assoc();
    $assetId6988 = $row6988['assetId'];
    $category6988 = $row6988['category'];
    $date6988 = $row6988['date'];
    $building6988 = $row6988['building'];
    $floor6988 = $row6988['floor'];
    $room6988 = $row6988['room'];
    $status6988 = $row6988['status'];
    $assignedName6988 = $row6988['assignedName'];
    $assignedBy6988 = $row6988['assignedBy'];
    $upload_img6988 = $row6988['upload_img'];
    $description6988 = $row6988['description'];

    //FOR ID 6989
    $sql6989 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6989";
    $stmt6989 = $conn->prepare($sql6989);
    $stmt6989->execute();
    $result6989 = $stmt6989->get_result();
    $row6989 = $result6989->fetch_assoc();
    $assetId6989 = $row6989['assetId'];
    $category6989 = $row6989['category'];
    $date6989 = $row6989['date'];
    $building6989 = $row6989['building'];
    $floor6989 = $row6989['floor'];
    $room6989 = $row6989['room'];
    $status6989 = $row6989['status'];
    $assignedName6989 = $row6989['assignedName'];
    $assignedBy6989 = $row6989['assignedBy'];
    $upload_img6989 = $row6989['upload_img'];
    $description6989 = $row6989['description'];

    //FOR ID 6990
    $sql6990 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6990";
    $stmt6990 = $conn->prepare($sql6990);
    $stmt6990->execute();
    $result6990 = $stmt6990->get_result();
    $row6990 = $result6990->fetch_assoc();
    $assetId6990 = $row6990['assetId'];
    $category6990 = $row6990['category'];
    $date6990 = $row6990['date'];
    $building6990 = $row6990['building'];
    $floor6990 = $row6990['floor'];
    $room6990 = $row6990['room'];
    $status6990 = $row6990['status'];
    $assignedName6990 = $row6990['assignedName'];
    $assignedBy6990 = $row6990['assignedBy'];
    $upload_img6990 = $row6990['upload_img'];
    $description6990 = $row6990['description'];

    //FOR ID 6991
    $sql6991 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6991";
    $stmt6991 = $conn->prepare($sql6991);
    $stmt6991->execute();
    $result6991 = $stmt6991->get_result();
    $row6991 = $result6991->fetch_assoc();
    $assetId6991 = $row6991['assetId'];
    $category6991 = $row6991['category'];
    $date6991 = $row6991['date'];
    $building6991 = $row6991['building'];
    $floor6991 = $row6991['floor'];
    $room6991 = $row6991['room'];
    $status6991 = $row6991['status'];
    $assignedName6991 = $row6991['assignedName'];
    $assignedBy6991 = $row6991['assignedBy'];
    $upload_img6991 = $row6991['upload_img'];
    $description6991 = $row6991['description'];

    //FOR ID 6992
    $sql6992 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6992";
    $stmt6992 = $conn->prepare($sql6992);
    $stmt6992->execute();
    $result6992 = $stmt6992->get_result();
    $row6992 = $result6992->fetch_assoc();
    $assetId6992 = $row6992['assetId'];
    $category6992 = $row6992['category'];
    $date6992 = $row6992['date'];
    $building6992 = $row6992['building'];
    $floor6992 = $row6992['floor'];
    $room6992 = $row6992['room'];
    $status6992 = $row6992['status'];
    $assignedName6992 = $row6992['assignedName'];
    $assignedBy6992 = $row6992['assignedBy'];
    $upload_img6992 = $row6992['upload_img'];
    $description6992 = $row6992['description'];

    //FOR ID 6993
    $sql6993 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6993";
    $stmt6993 = $conn->prepare($sql6993);
    $stmt6993->execute();
    $result6993 = $stmt6993->get_result();
    $row6993 = $result6993->fetch_assoc();
    $assetId6993 = $row6993['assetId'];
    $category6993 = $row6993['category'];
    $date6993 = $row6993['date'];
    $building6993 = $row6993['building'];
    $floor6993 = $row6993['floor'];
    $room6993 = $row6993['room'];
    $status6993 = $row6993['status'];
    $assignedName6993 = $row6993['assignedName'];
    $assignedBy6993 = $row6993['assignedBy'];
    $upload_img6993 = $row6993['upload_img'];
    $description6993 = $row6993['description'];

    //FOR ID 6994
    $sql6994 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6994";
    $stmt6994 = $conn->prepare($sql6994);
    $stmt6994->execute();
    $result6994 = $stmt6994->get_result();
    $row6994 = $result6994->fetch_assoc();
    $assetId6994 = $row6994['assetId'];
    $category6994 = $row6994['category'];
    $date6994 = $row6994['date'];
    $building6994 = $row6994['building'];
    $floor6994 = $row6994['floor'];
    $room6994 = $row6994['room'];
    $status6994 = $row6994['status'];
    $assignedName6994 = $row6994['assignedName'];
    $assignedBy6994 = $row6994['assignedBy'];
    $upload_img6994 = $row6994['upload_img'];
    $description6994 = $row6994['description'];

    //FOR ID 6995
    $sql6995 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6995";
    $stmt6995 = $conn->prepare($sql6995);
    $stmt6995->execute();
    $result6995 = $stmt6995->get_result();
    $row6995 = $result6995->fetch_assoc();
    $assetId6995 = $row6995['assetId'];
    $category6995 = $row6995['category'];
    $date6995 = $row6995['date'];
    $building6995 = $row6995['building'];
    $floor6995 = $row6995['floor'];
    $room6995 = $row6995['room'];
    $status6995 = $row6995['status'];
    $assignedName6995 = $row6995['assignedName'];
    $assignedBy6995 = $row6995['assignedBy'];
    $upload_img6995 = $row6995['upload_img'];
    $description6995 = $row6995['description'];

    //FOR ID 6996
    $sql6996 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6996";
    $stmt6996 = $conn->prepare($sql6996);
    $stmt6996->execute();
    $result6996 = $stmt6996->get_result();
    $row6996 = $result6996->fetch_assoc();
    $assetId6996 = $row6996['assetId'];
    $category6996 = $row6996['category'];
    $date6996 = $row6996['date'];
    $building6996 = $row6996['building'];
    $floor6996 = $row6996['floor'];
    $room6996 = $row6996['room'];
    $status6996 = $row6996['status'];
    $assignedName6996 = $row6996['assignedName'];
    $assignedBy6996 = $row6996['assignedBy'];
    $upload_img6996 = $row6996['upload_img'];
    $description6996 = $row6996['description'];

    //FOR ID 6997
    $sql6997 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6997";
    $stmt6997 = $conn->prepare($sql6997);
    $stmt6997->execute();
    $result6997 = $stmt6997->get_result();
    $row6997 = $result6997->fetch_assoc();
    $assetId6997 = $row6997['assetId'];
    $category6997 = $row6997['category'];
    $date6997 = $row6997['date'];
    $building6997 = $row6997['building'];
    $floor6997 = $row6997['floor'];
    $room6997 = $row6997['room'];
    $status6997 = $row6997['status'];
    $assignedName6997 = $row6997['assignedName'];
    $assignedBy6997 = $row6997['assignedBy'];
    $upload_img6997 = $row6997['upload_img'];
    $description6997 = $row6997['description'];

    //FOR ID 6998
    $sql6998 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6998";
    $stmt6998 = $conn->prepare($sql6998);
    $stmt6998->execute();
    $result6998 = $stmt6998->get_result();
    $row6998 = $result6998->fetch_assoc();
    $assetId6998 = $row6998['assetId'];
    $category6998 = $row6998['category'];
    $date6998 = $row6998['date'];
    $building6998 = $row6998['building'];
    $floor6998 = $row6998['floor'];
    $room6998 = $row6998['room'];
    $status6998 = $row6998['status'];
    $assignedName6998 = $row6998['assignedName'];
    $assignedBy6998 = $row6998['assignedBy'];
    $upload_img6998 = $row6998['upload_img'];
    $description6998 = $row6998['description'];

    //FOR ID 6999
    $sql6999 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 6999";
    $stmt6999 = $conn->prepare($sql6999);
    $stmt6999->execute();
    $result6999 = $stmt6999->get_result();
    $row6999 = $result6999->fetch_assoc();
    $assetId6999 = $row6999['assetId'];
    $category6999 = $row6999['category'];
    $date6999 = $row6999['date'];
    $building6999 = $row6999['building'];
    $floor6999 = $row6999['floor'];
    $room6999 = $row6999['room'];
    $status6999 = $row6999['status'];
    $assignedName6999 = $row6999['assignedName'];
    $assignedBy6999 = $row6999['assignedBy'];
    $upload_img6999 = $row6999['upload_img'];
    $description6999 = $row6999['description'];

    //FOR ID 7000
    $sql7000 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7000";
    $stmt7000 = $conn->prepare($sql7000);
    $stmt7000->execute();
    $result7000 = $stmt7000->get_result();
    $row7000 = $result7000->fetch_assoc();
    $assetId7000 = $row7000['assetId'];
    $category7000 = $row7000['category'];
    $date7000 = $row7000['date'];
    $building7000 = $row7000['building'];
    $floor7000 = $row7000['floor'];
    $room7000 = $row7000['room'];
    $status7000 = $row7000['status'];
    $assignedName7000 = $row7000['assignedName'];
    $assignedBy7000 = $row7000['assignedBy'];
    $upload_img7000 = $row7000['upload_img'];
    $description7000 = $row7000['description'];

    //FOR ID 7001
    $sql7001 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7001";
    $stmt7001 = $conn->prepare($sql7001);
    $stmt7001->execute();
    $result7001 = $stmt7001->get_result();
    $row7001 = $result7001->fetch_assoc();
    $assetId7001 = $row7001['assetId'];
    $category7001 = $row7001['category'];
    $date7001 = $row7001['date'];
    $building7001 = $row7001['building'];
    $floor7001 = $row7001['floor'];
    $room7001 = $row7001['room'];
    $status7001 = $row7001['status'];
    $assignedName7001 = $row7001['assignedName'];
    $assignedBy7001 = $row7001['assignedBy'];
    $upload_img7001 = $row7001['upload_img'];
    $description7001 = $row7001['description'];

    //FOR ID 7002
    $sql7002 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7002";
    $stmt7002 = $conn->prepare($sql7002);
    $stmt7002->execute();
    $result7002 = $stmt7002->get_result();
    $row7002 = $result7002->fetch_assoc();
    $assetId7002 = $row7002['assetId'];
    $category7002 = $row7002['category'];
    $date7002 = $row7002['date'];
    $building7002 = $row7002['building'];
    $floor7002 = $row7002['floor'];
    $room7002 = $row7002['room'];
    $status7002 = $row7002['status'];
    $assignedName7002 = $row7002['assignedName'];
    $assignedBy7002 = $row7002['assignedBy'];
    $upload_img7002 = $row7002['upload_img'];
    $description7002 = $row7002['description'];

    //FOR ID 7003
    $sql7003 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7003";
    $stmt7003 = $conn->prepare($sql7003);
    $stmt7003->execute();
    $result7003 = $stmt7003->get_result();
    $row7003 = $result7003->fetch_assoc();
    $assetId7003 = $row7003['assetId'];
    $category7003 = $row7003['category'];
    $date7003 = $row7003['date'];
    $building7003 = $row7003['building'];
    $floor7003 = $row7003['floor'];
    $room7003 = $row7003['room'];
    $status7003 = $row7003['status'];
    $assignedName7003 = $row7003['assignedName'];
    $assignedBy7003 = $row7003['assignedBy'];
    $upload_img7003 = $row7003['upload_img'];
    $description7003 = $row7003['description'];

    //FOR ID 7004
    $sql7004 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7004";
    $stmt7004 = $conn->prepare($sql7004);
    $stmt7004->execute();
    $result7004 = $stmt7004->get_result();
    $row7004 = $result7004->fetch_assoc();
    $assetId7004 = $row7004['assetId'];
    $category7004 = $row7004['category'];
    $date7004 = $row7004['date'];
    $building7004 = $row7004['building'];
    $floor7004 = $row7004['floor'];
    $room7004 = $row7004['room'];
    $status7004 = $row7004['status'];
    $assignedName7004 = $row7004['assignedName'];
    $assignedBy7004 = $row7004['assignedBy'];
    $upload_img7004 = $row7004['upload_img'];
    $description7004 = $row7004['description'];

    //FOR ID 7005
    $sql7005 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7005";
    $stmt7005 = $conn->prepare($sql7005);
    $stmt7005->execute();
    $result7005 = $stmt7005->get_result();
    $row7005 = $result7005->fetch_assoc();
    $assetId7005 = $row7005['assetId'];
    $category7005 = $row7005['category'];
    $date7005 = $row7005['date'];
    $building7005 = $row7005['building'];
    $floor7005 = $row7005['floor'];
    $room7005 = $row7005['room'];
    $status7005 = $row7005['status'];
    $assignedName7005 = $row7005['assignedName'];
    $assignedBy7005 = $row7005['assignedBy'];
    $upload_img7005 = $row7005['upload_img'];
    $description7005 = $row7005['description'];

    //FOR ID 7006
    $sql7006 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7006";
    $stmt7006 = $conn->prepare($sql7006);
    $stmt7006->execute();
    $result7006 = $stmt7006->get_result();
    $row7006 = $result7006->fetch_assoc();
    $assetId7006 = $row7006['assetId'];
    $category7006 = $row7006['category'];
    $date7006 = $row7006['date'];
    $building7006 = $row7006['building'];
    $floor7006 = $row7006['floor'];
    $room7006 = $row7006['room'];
    $status7006 = $row7006['status'];
    $assignedName7006 = $row7006['assignedName'];
    $assignedBy7006 = $row7006['assignedBy'];
    $upload_img7006 = $row7006['upload_img'];
    $description7006 = $row7006['description'];

    //FOR ID 7007
    $sql7007 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7007";
    $stmt7007 = $conn->prepare($sql7007);
    $stmt7007->execute();
    $result7007 = $stmt7007->get_result();
    $row7007 = $result7007->fetch_assoc();
    $assetId7007 = $row7007['assetId'];
    $category7007 = $row7007['category'];
    $date7007 = $row7007['date'];
    $building7007 = $row7007['building'];
    $floor7007 = $row7007['floor'];
    $room7007 = $row7007['room'];
    $status7007 = $row7007['status'];
    $assignedName7007 = $row7007['assignedName'];
    $assignedBy7007 = $row7007['assignedBy'];
    $upload_img7007 = $row7007['upload_img'];
    $description7007 = $row7007['description'];

    //FOR ID 7008
    $sql7008 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7008";
    $stmt7008 = $conn->prepare($sql7008);
    $stmt7008->execute();
    $result7008 = $stmt7008->get_result();
    $row7008 = $result7008->fetch_assoc();
    $assetId7008 = $row7008['assetId'];
    $category7008 = $row7008['category'];
    $date7008 = $row7008['date'];
    $building7008 = $row7008['building'];
    $floor7008 = $row7008['floor'];
    $room7008 = $row7008['room'];
    $status7008 = $row7008['status'];
    $assignedName7008 = $row7008['assignedName'];
    $assignedBy7008 = $row7008['assignedBy'];
    $upload_img7008 = $row7008['upload_img'];
    $description7008 = $row7008['description'];

    //FOR ID 7009
    $sql7009 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7009";
    $stmt7009 = $conn->prepare($sql7009);
    $stmt7009->execute();
    $result7009 = $stmt7009->get_result();
    $row7009 = $result7009->fetch_assoc();
    $assetId7009 = $row7009['assetId'];
    $category7009 = $row7009['category'];
    $date7009 = $row7009['date'];
    $building7009 = $row7009['building'];
    $floor7009 = $row7009['floor'];
    $room7009 = $row7009['room'];
    $status7009 = $row7009['status'];
    $assignedName7009 = $row7009['assignedName'];
    $assignedBy7009 = $row7009['assignedBy'];
    $upload_img7009 = $row7009['upload_img'];
    $description7009 = $row7009['description'];

    //FOR ID 7010
    $sql7010 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7010";
    $stmt7010 = $conn->prepare($sql7010);
    $stmt7010->execute();
    $result7010 = $stmt7010->get_result();
    $row7010 = $result7010->fetch_assoc();
    $assetId7010 = $row7010['assetId'];
    $category7010 = $row7010['category'];
    $date7010 = $row7010['date'];
    $building7010 = $row7010['building'];
    $floor7010 = $row7010['floor'];
    $room7010 = $row7010['room'];
    $status7010 = $row7010['status'];
    $assignedName7010 = $row7010['assignedName'];
    $assignedBy7010 = $row7010['assignedBy'];
    $upload_img7010 = $row7010['upload_img'];
    $description7010 = $row7010['description'];
    //FOR ID 7011
    $sql7011 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7011";
    $stmt7011 = $conn->prepare($sql7011);
    $stmt7011->execute();
    $result7011 = $stmt7011->get_result();
    $row7011 = $result7011->fetch_assoc();
    $assetId7011 = $row7011['assetId'];
    $category7011 = $row7011['category'];
    $date7011 = $row7011['date'];
    $building7011 = $row7011['building'];
    $floor7011 = $row7011['floor'];
    $room7011 = $row7011['room'];
    $status7011 = $row7011['status'];
    $assignedName7011 = $row7011['assignedName'];
    $assignedBy7011 = $row7011['assignedBy'];
    $upload_img7011 = $row7011['upload_img'];
    $description7011 = $row7011['description'];

    //FOR ID 7012
    $sql7012 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7012";
    $stmt7012 = $conn->prepare($sql7012);
    $stmt7012->execute();
    $result7012 = $stmt7012->get_result();
    $row7012 = $result7012->fetch_assoc();
    $assetId7012 = $row7012['assetId'];
    $category7012 = $row7012['category'];
    $date7012 = $row7012['date'];
    $building7012 = $row7012['building'];
    $floor7012 = $row7012['floor'];
    $room7012 = $row7012['room'];
    $status7012 = $row7012['status'];
    $assignedName7012 = $row7012['assignedName'];
    $assignedBy7012 = $row7012['assignedBy'];
    $upload_img7012 = $row7012['upload_img'];
    $description7012 = $row7012['description'];

    //FOR ID 7013
    $sql7013 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7013";
    $stmt7013 = $conn->prepare($sql7013);
    $stmt7013->execute();
    $result7013 = $stmt7013->get_result();
    $row7013 = $result7013->fetch_assoc();
    $assetId7013 = $row7013['assetId'];
    $category7013 = $row7013['category'];
    $date7013 = $row7013['date'];
    $building7013 = $row7013['building'];
    $floor7013 = $row7013['floor'];
    $room7013 = $row7013['room'];
    $status7013 = $row7013['status'];
    $assignedName7013 = $row7013['assignedName'];
    $assignedBy7013 = $row7013['assignedBy'];
    $upload_img7013 = $row7013['upload_img'];
    $description7013 = $row7013['description'];

    //FOR ID 7014
    $sql7014 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7014";
    $stmt7014 = $conn->prepare($sql7014);
    $stmt7014->execute();
    $result7014 = $stmt7014->get_result();
    $row7014 = $result7014->fetch_assoc();
    $assetId7014 = $row7014['assetId'];
    $category7014 = $row7014['category'];
    $date7014 = $row7014['date'];
    $building7014 = $row7014['building'];
    $floor7014 = $row7014['floor'];
    $room7014 = $row7014['room'];
    $status7014 = $row7014['status'];
    $assignedName7014 = $row7014['assignedName'];
    $assignedBy7014 = $row7014['assignedBy'];
    $upload_img7014 = $row7014['upload_img'];
    $description7014 = $row7014['description'];

    //FOR ID 7015
    $sql7015 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7015";
    $stmt7015 = $conn->prepare($sql7015);
    $stmt7015->execute();
    $result7015 = $stmt7015->get_result();
    $row7015 = $result7015->fetch_assoc();
    $assetId7015 = $row7015['assetId'];
    $category7015 = $row7015['category'];
    $date7015 = $row7015['date'];
    $building7015 = $row7015['building'];
    $floor7015 = $row7015['floor'];
    $room7015 = $row7015['room'];
    $status7015 = $row7015['status'];
    $assignedName7015 = $row7015['assignedName'];
    $assignedBy7015 = $row7015['assignedBy'];
    $upload_img7015 = $row7015['upload_img'];
    $description7015 = $row7015['description'];

    //FOR ID 7016
    $sql7016 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7016";
    $stmt7016 = $conn->prepare($sql7016);
    $stmt7016->execute();
    $result7016 = $stmt7016->get_result();
    $row7016 = $result7016->fetch_assoc();
    $assetId7016 = $row7016['assetId'];
    $category7016 = $row7016['category'];
    $date7016 = $row7016['date'];
    $building7016 = $row7016['building'];
    $floor7016 = $row7016['floor'];
    $room7016 = $row7016['room'];
    $status7016 = $row7016['status'];
    $assignedName7016 = $row7016['assignedName'];
    $assignedBy7016 = $row7016['assignedBy'];
    $upload_img7016 = $row7016['upload_img'];
    $description7016 = $row7016['description'];

    //FOR ID 7016
    $sql7016 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7016";
    $stmt7016 = $conn->prepare($sql7016);
    $stmt7016->execute();
    $result7016 = $stmt7016->get_result();
    $row7016 = $result7016->fetch_assoc();
    $assetId7016 = $row7016['assetId'];
    $category7016 = $row7016['category'];
    $date7016 = $row7016['date'];
    $building7016 = $row7016['building'];
    $floor7016 = $row7016['floor'];
    $room7016 = $row7016['room'];
    $status7016 = $row7016['status'];
    $assignedName7016 = $row7016['assignedName'];
    $assignedBy7016 = $row7016['assignedBy'];
    $upload_img7016 = $row7016['upload_img'];
    $description7016 = $row7016['description'];

    //FOR ID 7017
    $sql7017 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7017";
    $stmt7017 = $conn->prepare($sql7017);
    $stmt7017->execute();
    $result7017 = $stmt7017->get_result();
    $row7017 = $result7017->fetch_assoc();
    $assetId7017 = $row7017['assetId'];
    $category7017 = $row7017['category'];
    $date7017 = $row7017['date'];
    $building7017 = $row7017['building'];
    $floor7017 = $row7017['floor'];
    $room7017 = $row7017['room'];
    $status7017 = $row7017['status'];
    $assignedName7017 = $row7017['assignedName'];
    $assignedBy7017 = $row7017['assignedBy'];
    $upload_img7017 = $row7017['upload_img'];
    $description7017 = $row7017['description'];

    //FOR ID 7018
    $sql7018 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7018";
    $stmt7018 = $conn->prepare($sql7018);
    $stmt7018->execute();
    $result7018 = $stmt7018->get_result();
    $row7018 = $result7018->fetch_assoc();
    $assetId7018 = $row7018['assetId'];
    $category7018 = $row7018['category'];
    $date7018 = $row7018['date'];
    $building7018 = $row7018['building'];
    $floor7018 = $row7018['floor'];
    $room7018 = $row7018['room'];
    $status7018 = $row7018['status'];
    $assignedName7018 = $row7018['assignedName'];
    $assignedBy7018 = $row7018['assignedBy'];
    $upload_img7018 = $row7018['upload_img'];
    $description7018 = $row7018['description'];

    //FOR ID 7019
    $sql7019 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7019";
    $stmt7019 = $conn->prepare($sql7019);
    $stmt7019->execute();
    $result7019 = $stmt7019->get_result();
    $row7019 = $result7019->fetch_assoc();
    $assetId7019 = $row7019['assetId'];
    $category7019 = $row7019['category'];
    $date7019 = $row7019['date'];
    $building7019 = $row7019['building'];
    $floor7019 = $row7019['floor'];
    $room7019 = $row7019['room'];
    $status7019 = $row7019['status'];
    $assignedName7019 = $row7019['assignedName'];
    $assignedBy7019 = $row7019['assignedBy'];
    $upload_img7019 = $row7019['upload_img'];
    $description7019 = $row7019['description'];

    //FOR ID 7020
    $sql7020 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7020";
    $stmt7020 = $conn->prepare($sql7020);
    $stmt7020->execute();
    $result7020 = $stmt7020->get_result();
    $row7020 = $result7020->fetch_assoc();
    $assetId7020 = $row7020['assetId'];
    $category7020 = $row7020['category'];
    $date7020 = $row7020['date'];
    $building7020 = $row7020['building'];
    $floor7020 = $row7020['floor'];
    $room7020 = $row7020['room'];
    $status7020 = $row7020['status'];
    $assignedName7020 = $row7020['assignedName'];
    $assignedBy7020 = $row7020['assignedBy'];
    $upload_img7020 = $row7020['upload_img'];
    $description7020 = $row7020['description'];

    //FOR ID 7021
    $sql7021 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7021";
    $stmt7021 = $conn->prepare($sql7021);
    $stmt7021->execute();
    $result7021 = $stmt7021->get_result();
    $row7021 = $result7021->fetch_assoc();
    $assetId7021 = $row7021['assetId'];
    $category7021 = $row7021['category'];
    $date7021 = $row7021['date'];
    $building7021 = $row7021['building'];
    $floor7021 = $row7021['floor'];
    $room7021 = $row7021['room'];
    $status7021 = $row7021['status'];
    $assignedName7021 = $row7021['assignedName'];
    $assignedBy7021 = $row7021['assignedBy'];
    $upload_img7021 = $row7021['upload_img'];
    $description7021 = $row7021['description'];

    //FOR ID 7022
    $sql7022 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7022";
    $stmt7022 = $conn->prepare($sql7022);
    $stmt7022->execute();
    $result7022 = $stmt7022->get_result();
    $row7022 = $result7022->fetch_assoc();
    $assetId7022 = $row7022['assetId'];
    $category7022 = $row7022['category'];
    $date7022 = $row7022['date'];
    $building7022 = $row7022['building'];
    $floor7022 = $row7022['floor'];
    $room7022 = $row7022['room'];
    $status7022 = $row7022['status'];
    $assignedName7022 = $row7022['assignedName'];
    $assignedBy7022 = $row7022['assignedBy'];
    $upload_img7022 = $row7022['upload_img'];
    $description7022 = $row7022['description'];

    //FOR ID 7023
    $sql7023 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7023";
    $stmt7023 = $conn->prepare($sql7023);
    $stmt7023->execute();
    $result7023 = $stmt7023->get_result();
    $row7023 = $result7023->fetch_assoc();
    $assetId7023 = $row7023['assetId'];
    $category7023 = $row7023['category'];
    $date7023 = $row7023['date'];
    $building7023 = $row7023['building'];
    $floor7023 = $row7023['floor'];
    $room7023 = $row7023['room'];
    $status7023 = $row7023['status'];
    $assignedName7023 = $row7023['assignedName'];
    $assignedBy7023 = $row7023['assignedBy'];
    $upload_img7023 = $row7023['upload_img'];
    $description7023 = $row7023['description'];

    //FOR ID 7024
    $sql7024 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7024";
    $stmt7024 = $conn->prepare($sql7024);
    $stmt7024->execute();
    $result7024 = $stmt7024->get_result();
    $row7024 = $result7024->fetch_assoc();
    $assetId7024 = $row7024['assetId'];
    $category7024 = $row7024['category'];
    $date7024 = $row7024['date'];
    $building7024 = $row7024['building'];
    $floor7024 = $row7024['floor'];
    $room7024 = $row7024['room'];
    $status7024 = $row7024['status'];
    $assignedName7024 = $row7024['assignedName'];
    $assignedBy7024 = $row7024['assignedBy'];
    $upload_img7024 = $row7024['upload_img'];
    $description7024 = $row7024['description'];

    //FOR ID 7025
    $sql7025 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7025";
    $stmt7025 = $conn->prepare($sql7025);
    $stmt7025->execute();
    $result7025 = $stmt7025->get_result();
    $row7025 = $result7025->fetch_assoc();
    $assetId7025 = $row7025['assetId'];
    $category7025 = $row7025['category'];
    $date7025 = $row7025['date'];
    $building7025 = $row7025['building'];
    $floor7025 = $row7025['floor'];
    $room7025 = $row7025['room'];
    $status7025 = $row7025['status'];
    $assignedName7025 = $row7025['assignedName'];
    $assignedBy7025 = $row7025['assignedBy'];
    $upload_img7025 = $row7025['upload_img'];
    $description7025 = $row7025['description'];

    //FOR ID 7026
    $sql7026 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7026";
    $stmt7026 = $conn->prepare($sql7026);
    $stmt7026->execute();
    $result7026 = $stmt7026->get_result();
    $row7026 = $result7026->fetch_assoc();
    $assetId7026 = $row7026['assetId'];
    $category7026 = $row7026['category'];
    $date7026 = $row7026['date'];
    $building7026 = $row7026['building'];
    $floor7026 = $row7026['floor'];
    $room7026 = $row7026['room'];
    $status7026 = $row7026['status'];
    $assignedName7026 = $row7026['assignedName'];
    $assignedBy7026 = $row7026['assignedBy'];
    $upload_img7026 = $row7026['upload_img'];
    $description7026 = $row7026['description'];

    //FOR ID 7027
    $sql7027 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7027";
    $stmt7027 = $conn->prepare($sql7027);
    $stmt7027->execute();
    $result7027 = $stmt7027->get_result();
    $row7027 = $result7027->fetch_assoc();
    $assetId7027 = $row7027['assetId'];
    $category7027 = $row7027['category'];
    $date7027 = $row7027['date'];
    $building7027 = $row7027['building'];
    $floor7027 = $row7027['floor'];
    $room7027 = $row7027['room'];
    $status7027 = $row7027['status'];
    $assignedName7027 = $row7027['assignedName'];
    $assignedBy7027 = $row7027['assignedBy'];
    $upload_img7027 = $row7027['upload_img'];
    $description7027 = $row7027['description'];

    //FOR ID 7028
    $sql7028 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7028";
    $stmt7028 = $conn->prepare($sql7028);
    $stmt7028->execute();
    $result7028 = $stmt7028->get_result();
    $row7028 = $result7028->fetch_assoc();
    $assetId7028 = $row7028['assetId'];
    $category7028 = $row7028['category'];
    $date7028 = $row7028['date'];
    $building7028 = $row7028['building'];
    $floor7028 = $row7028['floor'];
    $room7028 = $row7028['room'];
    $status7028 = $row7028['status'];
    $assignedName7028 = $row7028['assignedName'];
    $assignedBy7028 = $row7028['assignedBy'];
    $upload_img7028 = $row7028['upload_img'];
    $description7028 = $row7028['description'];

    //FOR ID 7029
    $sql7029 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7029";
    $stmt7029 = $conn->prepare($sql7029);
    $stmt7029->execute();
    $result7029 = $stmt7029->get_result();
    $row7029 = $result7029->fetch_assoc();
    $assetId7029 = $row7029['assetId'];
    $category7029 = $row7029['category'];
    $date7029 = $row7029['date'];
    $building7029 = $row7029['building'];
    $floor7029 = $row7029['floor'];
    $room7029 = $row7029['room'];
    $status7029 = $row7029['status'];
    $assignedName7029 = $row7029['assignedName'];
    $assignedBy7029 = $row7029['assignedBy'];
    $upload_img7029 = $row7029['upload_img'];
    $description7029 = $row7029['description'];

    //FOR ID 7030
    $sql7030 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7030";
    $stmt7030 = $conn->prepare($sql7030);
    $stmt7030->execute();
    $result7030 = $stmt7030->get_result();
    $row7030 = $result7030->fetch_assoc();
    $assetId7030 = $row7030['assetId'];
    $category7030 = $row7030['category'];
    $date7030 = $row7030['date'];
    $building7030 = $row7030['building'];
    $floor7030 = $row7030['floor'];
    $room7030 = $row7030['room'];
    $status7030 = $row7030['status'];
    $assignedName7030 = $row7030['assignedName'];
    $assignedBy7030 = $row7030['assignedBy'];
    $upload_img7030 = $row7030['upload_img'];
    $description7030 = $row7030['description'];

    //FOR ID 7031
    $sql7031 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7031";
    $stmt7031 = $conn->prepare($sql7031);
    $stmt7031->execute();
    $result7031 = $stmt7031->get_result();
    $row7031 = $result7031->fetch_assoc();
    $assetId7031 = $row7031['assetId'];
    $category7031 = $row7031['category'];
    $date7031 = $row7031['date'];
    $building7031 = $row7031['building'];
    $floor7031 = $row7031['floor'];
    $room7031 = $row7031['room'];
    $status7031 = $row7031['status'];
    $assignedName7031 = $row7031['assignedName'];
    $assignedBy7031 = $row7031['assignedBy'];
    $upload_img7031 = $row7031['upload_img'];
    $description7031 = $row7031['description'];

    //FOR ID 7032
    $sql7032 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7032";
    $stmt7032 = $conn->prepare($sql7032);
    $stmt7032->execute();
    $result7032 = $stmt7032->get_result();
    $row7032 = $result7032->fetch_assoc();
    $assetId7032 = $row7032['assetId'];
    $category7032 = $row7032['category'];
    $date7032 = $row7032['date'];
    $building7032 = $row7032['building'];
    $floor7032 = $row7032['floor'];
    $room7032 = $row7032['room'];
    $status7032 = $row7032['status'];
    $assignedName7032 = $row7032['assignedName'];
    $assignedBy7032 = $row7032['assignedBy'];
    $upload_img7032 = $row7032['upload_img'];
    $description7032 = $row7032['description'];

    //FOR ID 7033
    $sql7033 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7033";
    $stmt7033 = $conn->prepare($sql7033);
    $stmt7033->execute();
    $result7033 = $stmt7033->get_result();
    $row7033 = $result7033->fetch_assoc();
    $assetId7033 = $row7033['assetId'];
    $category7033 = $row7033['category'];
    $date7033 = $row7033['date'];
    $building7033 = $row7033['building'];
    $floor7033 = $row7033['floor'];
    $room7033 = $row7033['room'];
    $status7033 = $row7033['status'];
    $assignedName7033 = $row7033['assignedName'];
    $assignedBy7033 = $row7033['assignedBy'];
    $upload_img7033 = $row7033['upload_img'];
    $description7033 = $row7033['description'];

    //FOR ID 7034
    $sql7034 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7034";
    $stmt7034 = $conn->prepare($sql7034);
    $stmt7034->execute();
    $result7034 = $stmt7034->get_result();
    $row7034 = $result7034->fetch_assoc();
    $assetId7034 = $row7034['assetId'];
    $category7034 = $row7034['category'];
    $date7034 = $row7034['date'];
    $building7034 = $row7034['building'];
    $floor7034 = $row7034['floor'];
    $room7034 = $row7034['room'];
    $status7034 = $row7034['status'];
    $assignedName7034 = $row7034['assignedName'];
    $assignedBy7034 = $row7034['assignedBy'];
    $upload_img7034 = $row7034['upload_img'];
    $description7034 = $row7034['description'];

    //FOR ID 7035
    $sql7035 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7035";
    $stmt7035 = $conn->prepare($sql7035);
    $stmt7035->execute();
    $result7035 = $stmt7035->get_result();
    $row7035 = $result7035->fetch_assoc();
    $assetId7035 = $row7035['assetId'];
    $category7035 = $row7035['category'];
    $date7035 = $row7035['date'];
    $building7035 = $row7035['building'];
    $floor7035 = $row7035['floor'];
    $room7035 = $row7035['room'];
    $status7035 = $row7035['status'];
    $assignedName7035 = $row7035['assignedName'];
    $assignedBy7035 = $row7035['assignedBy'];
    $upload_img7035 = $row7035['upload_img'];
    $description7035 = $row7035['description'];

    //FOR ID 7036
    $sql7036 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7036";
    $stmt7036 = $conn->prepare($sql7036);
    $stmt7036->execute();
    $result7036 = $stmt7036->get_result();
    $row7036 = $result7036->fetch_assoc();
    $assetId7036 = $row7036['assetId'];
    $category7036 = $row7036['category'];
    $date7036 = $row7036['date'];
    $building7036 = $row7036['building'];
    $floor7036 = $row7036['floor'];
    $room7036 = $row7036['room'];
    $status7036 = $row7036['status'];
    $assignedName7036 = $row7036['assignedName'];
    $assignedBy7036 = $row7036['assignedBy'];
    $upload_img7036 = $row7036['upload_img'];
    $description7036 = $row7036['description'];

    //FOR ID 7037
    $sql7037 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7037";
    $stmt7037 = $conn->prepare($sql7037);
    $stmt7037->execute();
    $result7037 = $stmt7037->get_result();
    $row7037 = $result7037->fetch_assoc();
    $assetId7037 = $row7037['assetId'];
    $category7037 = $row7037['category'];
    $date7037 = $row7037['date'];
    $building7037 = $row7037['building'];
    $floor7037 = $row7037['floor'];
    $room7037 = $row7037['room'];
    $status7037 = $row7037['status'];
    $assignedName7037 = $row7037['assignedName'];
    $assignedBy7037 = $row7037['assignedBy'];
    $upload_img7037 = $row7037['upload_img'];
    $description7037 = $row7037['description'];

    //FOR ID 7038
    $sql7038 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7038";
    $stmt7038 = $conn->prepare($sql7038);
    $stmt7038->execute();
    $result7038 = $stmt7038->get_result();
    $row7038 = $result7038->fetch_assoc();
    $assetId7038 = $row7038['assetId'];
    $category7038 = $row7038['category'];
    $date7038 = $row7038['date'];
    $building7038 = $row7038['building'];
    $floor7038 = $row7038['floor'];
    $room7038 = $row7038['room'];
    $status7038 = $row7038['status'];
    $assignedName7038 = $row7038['assignedName'];
    $assignedBy7038 = $row7038['assignedBy'];
    $upload_img7038 = $row7038['upload_img'];
    $description7038 = $row7038['description'];

    //FOR ID 7039
    $sql7039 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7039";
    $stmt7039 = $conn->prepare($sql7039);
    $stmt7039->execute();
    $result7039 = $stmt7039->get_result();
    $row7039 = $result7039->fetch_assoc();
    $assetId7039 = $row7039['assetId'];
    $category7039 = $row7039['category'];
    $date7039 = $row7039['date'];
    $building7039 = $row7039['building'];
    $floor7039 = $row7039['floor'];
    $room7039 = $row7039['room'];
    $status7039 = $row7039['status'];
    $assignedName7039 = $row7039['assignedName'];
    $assignedBy7039 = $row7039['assignedBy'];
    $upload_img7039 = $row7039['upload_img'];
    $description7039 = $row7039['description'];

    //FOR ID 7040
    $sql7040 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7040";
    $stmt7040 = $conn->prepare($sql7040);
    $stmt7040->execute();
    $result7040 = $stmt7040->get_result();
    $row7040 = $result7040->fetch_assoc();
    $assetId7040 = $row7040['assetId'];
    $category7040 = $row7040['category'];
    $date7040 = $row7040['date'];
    $building7040 = $row7040['building'];
    $floor7040 = $row7040['floor'];
    $room7040 = $row7040['room'];
    $status7040 = $row7040['status'];
    $assignedName7040 = $row7040['assignedName'];
    $assignedBy7040 = $row7040['assignedBy'];
    $upload_img7040 = $row7040['upload_img'];
    $description7040 = $row7040['description'];

    //FOR ID 7041
    $sql7041 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7041";
    $stmt7041 = $conn->prepare($sql7041);
    $stmt7041->execute();
    $result7041 = $stmt7041->get_result();
    $row7041 = $result7041->fetch_assoc();
    $assetId7041 = $row7041['assetId'];
    $category7041 = $row7041['category'];
    $date7041 = $row7041['date'];
    $building7041 = $row7041['building'];
    $floor7041 = $row7041['floor'];
    $room7041 = $row7041['room'];
    $status7041 = $row7041['status'];
    $assignedName7041 = $row7041['assignedName'];
    $assignedBy7041 = $row7041['assignedBy'];
    $upload_img7041 = $row7041['upload_img'];
    $description7041 = $row7041['description'];

    //FOR ID 7042
    $sql7042 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7042";
    $stmt7042 = $conn->prepare($sql7042);
    $stmt7042->execute();
    $result7042 = $stmt7042->get_result();
    $row7042 = $result7042->fetch_assoc();
    $assetId7042 = $row7042['assetId'];
    $category7042 = $row7042['category'];
    $date7042 = $row7042['date'];
    $building7042 = $row7042['building'];
    $floor7042 = $row7042['floor'];
    $room7042 = $row7042['room'];
    $status7042 = $row7042['status'];
    $assignedName7042 = $row7042['assignedName'];
    $assignedBy7042 = $row7042['assignedBy'];
    $upload_img7042 = $row7042['upload_img'];
    $description7042 = $row7042['description'];

    //FOR ID 7043
    $sql7043 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7043";
    $stmt7043 = $conn->prepare($sql7043);
    $stmt7043->execute();
    $result7043 = $stmt7043->get_result();
    $row7043 = $result7043->fetch_assoc();
    $assetId7043 = $row7043['assetId'];
    $category7043 = $row7043['category'];
    $date7043 = $row7043['date'];
    $building7043 = $row7043['building'];
    $floor7043 = $row7043['floor'];
    $room7043 = $row7043['room'];
    $status7043 = $row7043['status'];
    $assignedName7043 = $row7043['assignedName'];
    $assignedBy7043 = $row7043['assignedBy'];
    $upload_img7043 = $row7043['upload_img'];
    $description7043 = $row7043['description'];

    //FOR ID 7044
    $sql7044 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7044";
    $stmt7044 = $conn->prepare($sql7044);
    $stmt7044->execute();
    $result7044 = $stmt7044->get_result();
    $row7044 = $result7044->fetch_assoc();
    $assetId7044 = $row7044['assetId'];
    $category7044 = $row7044['category'];
    $date7044 = $row7044['date'];
    $building7044 = $row7044['building'];
    $floor7044 = $row7044['floor'];
    $room7044 = $row7044['room'];
    $status7044 = $row7044['status'];
    $assignedName7044 = $row7044['assignedName'];
    $assignedBy7044 = $row7044['assignedBy'];
    $upload_img7044 = $row7044['upload_img'];
    $description7044 = $row7044['description'];

    //FOR ID 7045
    $sql7045 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7045";
    $stmt7045 = $conn->prepare($sql7045);
    $stmt7045->execute();
    $result7045 = $stmt7045->get_result();
    $row7045 = $result7045->fetch_assoc();
    $assetId7045 = $row7045['assetId'];
    $category7045 = $row7045['category'];
    $date7045 = $row7045['date'];
    $building7045 = $row7045['building'];
    $floor7045 = $row7045['floor'];
    $room7045 = $row7045['room'];
    $status7045 = $row7045['status'];
    $assignedName7045 = $row7045['assignedName'];
    $assignedBy7045 = $row7045['assignedBy'];
    $upload_img7045 = $row7045['upload_img'];
    $description7045 = $row7045['description'];

    //FOR ID 7046
    $sql7046 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7046";
    $stmt7046 = $conn->prepare($sql7046);
    $stmt7046->execute();
    $result7046 = $stmt7046->get_result();
    $row7046 = $result7046->fetch_assoc();
    $assetId7046 = $row7046['assetId'];
    $category7046 = $row7046['category'];
    $date7046 = $row7046['date'];
    $building7046 = $row7046['building'];
    $floor7046 = $row7046['floor'];
    $room7046 = $row7046['room'];
    $status7046 = $row7046['status'];
    $assignedName7046 = $row7046['assignedName'];
    $assignedBy7046 = $row7046['assignedBy'];
    $upload_img7046 = $row7046['upload_img'];
    $description7046 = $row7046['description'];

    //FOR ID 7047
    $sql7047 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7047";
    $stmt7047 = $conn->prepare($sql7047);
    $stmt7047->execute();
    $result7047 = $stmt7047->get_result();
    $row7047 = $result7047->fetch_assoc();
    $assetId7047 = $row7047['assetId'];
    $category7047 = $row7047['category'];
    $date7047 = $row7047['date'];
    $building7047 = $row7047['building'];
    $floor7047 = $row7047['floor'];
    $room7047 = $row7047['room'];
    $status7047 = $row7047['status'];
    $assignedName7047 = $row7047['assignedName'];
    $assignedBy7047 = $row7047['assignedBy'];
    $upload_img7047 = $row7047['upload_img'];
    $description7047 = $row7047['description'];

    //FOR ID 7048
    $sql7048 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7048";
    $stmt7048 = $conn->prepare($sql7048);
    $stmt7048->execute();
    $result7048 = $stmt7048->get_result();
    $row7048 = $result7048->fetch_assoc();
    $assetId7048 = $row7048['assetId'];
    $category7048 = $row7048['category'];
    $date7048 = $row7048['date'];
    $building7048 = $row7048['building'];
    $floor7048 = $row7048['floor'];
    $room7048 = $row7048['room'];
    $status7048 = $row7048['status'];
    $assignedName7048 = $row7048['assignedName'];
    $assignedBy7048 = $row7048['assignedBy'];
    $upload_img7048 = $row7048['upload_img'];
    $description7048 = $row7048['description'];

    //FOR ID 7049
    $sql7049 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7049";
    $stmt7049 = $conn->prepare($sql7049);
    $stmt7049->execute();
    $result7049 = $stmt7049->get_result();
    $row7049 = $result7049->fetch_assoc();
    $assetId7049 = $row7049['assetId'];
    $category7049 = $row7049['category'];
    $date7049 = $row7049['date'];
    $building7049 = $row7049['building'];
    $floor7049 = $row7049['floor'];
    $room7049 = $row7049['room'];
    $status7049 = $row7049['status'];
    $assignedName7049 = $row7049['assignedName'];
    $assignedBy7049 = $row7049['assignedBy'];
    $upload_img7049 = $row7049['upload_img'];
    $description7049 = $row7049['description'];

    //FOR ID 7050
    $sql7050 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7050";
    $stmt7050 = $conn->prepare($sql7050);
    $stmt7050->execute();
    $result7050 = $stmt7050->get_result();
    $row7050 = $result7050->fetch_assoc();
    $assetId7050 = $row7050['assetId'];
    $category7050 = $row7050['category'];
    $date7050 = $row7050['date'];
    $building7050 = $row7050['building'];
    $floor7050 = $row7050['floor'];
    $room7050 = $row7050['room'];
    $status7050 = $row7050['status'];
    $assignedName7050 = $row7050['assignedName'];
    $assignedBy7050 = $row7050['assignedBy'];
    $upload_img7050 = $row7050['upload_img'];
    $description7050 = $row7050['description'];

    //FOR ID 7051
    $sql7051 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7051";
    $stmt7051 = $conn->prepare($sql7051);
    $stmt7051->execute();
    $result7051 = $stmt7051->get_result();
    $row7051 = $result7051->fetch_assoc();
    $assetId7051 = $row7051['assetId'];
    $category7051 = $row7051['category'];
    $date7051 = $row7051['date'];
    $building7051 = $row7051['building'];
    $floor7051 = $row7051['floor'];
    $room7051 = $row7051['room'];
    $status7051 = $row7051['status'];
    $assignedName7051 = $row7051['assignedName'];
    $assignedBy7051 = $row7051['assignedBy'];
    $upload_img7051 = $row7051['upload_img'];
    $description7051 = $row7051['description'];

    //FOR ID 7052
    $sql7052 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7052";
    $stmt7052 = $conn->prepare($sql7052);
    $stmt7052->execute();
    $result7052 = $stmt7052->get_result();
    $row7052 = $result7052->fetch_assoc();
    $assetId7052 = $row7052['assetId'];
    $category7052 = $row7052['category'];
    $date7052 = $row7052['date'];
    $building7052 = $row7052['building'];
    $floor7052 = $row7052['floor'];
    $room7052 = $row7052['room'];
    $status7052 = $row7052['status'];
    $assignedName7052 = $row7052['assignedName'];
    $assignedBy7052 = $row7052['assignedBy'];
    $upload_img7052 = $row7052['upload_img'];
    $description7052 = $row7052['description'];

    //FOR ID 7053
    $sql7053 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7053";
    $stmt7053 = $conn->prepare($sql7053);
    $stmt7053->execute();
    $result7053 = $stmt7053->get_result();
    $row7053 = $result7053->fetch_assoc();
    $assetId7053 = $row7053['assetId'];
    $category7053 = $row7053['category'];
    $date7053 = $row7053['date'];
    $building7053 = $row7053['building'];
    $floor7053 = $row7053['floor'];
    $room7053 = $row7053['room'];
    $status7053 = $row7053['status'];
    $assignedName7053 = $row7053['assignedName'];
    $assignedBy7053 = $row7053['assignedBy'];
    $upload_img7053 = $row7053['upload_img'];
    $description7053 = $row7053['description'];

    //FOR ID 7054
    $sql7054 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7054";
    $stmt7054 = $conn->prepare($sql7054);
    $stmt7054->execute();
    $result7054 = $stmt7054->get_result();
    $row7054 = $result7054->fetch_assoc();
    $assetId7054 = $row7054['assetId'];
    $category7054 = $row7054['category'];
    $date7054 = $row7054['date'];
    $building7054 = $row7054['building'];
    $floor7054 = $row7054['floor'];
    $room7054 = $row7054['room'];
    $status7054 = $row7054['status'];
    $assignedName7054 = $row7054['assignedName'];
    $assignedBy7054 = $row7054['assignedBy'];
    $upload_img7054 = $row7054['upload_img'];
    $description7054 = $row7054['description'];

    //FOR ID 7055
    $sql7055 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7055";
    $stmt7055 = $conn->prepare($sql7055);
    $stmt7055->execute();
    $result7055 = $stmt7055->get_result();
    $row7055 = $result7055->fetch_assoc();
    $assetId7055 = $row7055['assetId'];
    $category7055 = $row7055['category'];
    $date7055 = $row7055['date'];
    $building7055 = $row7055['building'];
    $floor7055 = $row7055['floor'];
    $room7055 = $row7055['room'];
    $status7055 = $row7055['status'];
    $assignedName7055 = $row7055['assignedName'];
    $assignedBy7055 = $row7055['assignedBy'];
    $upload_img7055 = $row7055['upload_img'];
    $description7055 = $row7055['description'];

    //FOR ID 7056
    $sql7056 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7056";
    $stmt7056 = $conn->prepare($sql7056);
    $stmt7056->execute();
    $result7056 = $stmt7056->get_result();
    $row7056 = $result7056->fetch_assoc();
    $assetId7056 = $row7056['assetId'];
    $category7056 = $row7056['category'];
    $date7056 = $row7056['date'];
    $building7056 = $row7056['building'];
    $floor7056 = $row7056['floor'];
    $room7056 = $row7056['room'];
    $status7056 = $row7056['status'];
    $assignedName7056 = $row7056['assignedName'];
    $assignedBy7056 = $row7056['assignedBy'];
    $upload_img7056 = $row7056['upload_img'];
    $description7056 = $row7056['description'];

    //FOR ID 7057
    $sql7057 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7057";
    $stmt7057 = $conn->prepare($sql7057);
    $stmt7057->execute();
    $result7057 = $stmt7057->get_result();
    $row7057 = $result7057->fetch_assoc();
    $assetId7057 = $row7057['assetId'];
    $category7057 = $row7057['category'];
    $date7057 = $row7057['date'];
    $building7057 = $row7057['building'];
    $floor7057 = $row7057['floor'];
    $room7057 = $row7057['room'];
    $status7057 = $row7057['status'];
    $assignedName7057 = $row7057['assignedName'];
    $assignedBy7057 = $row7057['assignedBy'];
    $upload_img7057 = $row7057['upload_img'];
    $description7057 = $row7057['description'];

    //FOR ID 7058
    $sql7058 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7058";
    $stmt7058 = $conn->prepare($sql7058);
    $stmt7058->execute();
    $result7058 = $stmt7058->get_result();
    $row7058 = $result7058->fetch_assoc();
    $assetId7058 = $row7058['assetId'];
    $category7058 = $row7058['category'];
    $date7058 = $row7058['date'];
    $building7058 = $row7058['building'];
    $floor7058 = $row7058['floor'];
    $room7058 = $row7058['room'];
    $status7058 = $row7058['status'];
    $assignedName7058 = $row7058['assignedName'];
    $assignedBy7058 = $row7058['assignedBy'];
    $upload_img7058 = $row7058['upload_img'];
    $description7058 = $row7058['description'];

    //FOR ID 7059
    $sql7059 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7059";
    $stmt7059 = $conn->prepare($sql7059);
    $stmt7059->execute();
    $result7059 = $stmt7059->get_result();
    $row7059 = $result7059->fetch_assoc();
    $assetId7059 = $row7059['assetId'];
    $category7059 = $row7059['category'];
    $date7059 = $row7059['date'];
    $building7059 = $row7059['building'];
    $floor7059 = $row7059['floor'];
    $room7059 = $row7059['room'];
    $status7059 = $row7059['status'];
    $assignedName7059 = $row7059['assignedName'];
    $assignedBy7059 = $row7059['assignedBy'];
    $upload_img7059 = $row7059['upload_img'];
    $description7059 = $row7059['description'];

    //FOR ID 7060
    $sql7060 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7060";
    $stmt7060 = $conn->prepare($sql7060);
    $stmt7060->execute();
    $result7060 = $stmt7060->get_result();
    $row7060 = $result7060->fetch_assoc();
    $assetId7060 = $row7060['assetId'];
    $category7060 = $row7060['category'];
    $date7060 = $row7060['date'];
    $building7060 = $row7060['building'];
    $floor7060 = $row7060['floor'];
    $room7060 = $row7060['room'];
    $status7060 = $row7060['status'];
    $assignedName7060 = $row7060['assignedName'];
    $assignedBy7060 = $row7060['assignedBy'];
    $upload_img7060 = $row7060['upload_img'];
    $description7060 = $row7060['description'];

    //FOR ID 7061
    $sql7061 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7061";
    $stmt7061 = $conn->prepare($sql7061);
    $stmt7061->execute();
    $result7061 = $stmt7061->get_result();
    $row7061 = $result7061->fetch_assoc();
    $assetId7061 = $row7061['assetId'];
    $category7061 = $row7061['category'];
    $date7061 = $row7061['date'];
    $building7061 = $row7061['building'];
    $floor7061 = $row7061['floor'];
    $room7061 = $row7061['room'];
    $status7061 = $row7061['status'];
    $assignedName7061 = $row7061['assignedName'];
    $assignedBy7061 = $row7061['assignedBy'];
    $upload_img7061 = $row7061['upload_img'];
    $description7061 = $row7061['description'];

    //FOR ID 7062
    $sql7062 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7062";
    $stmt7062 = $conn->prepare($sql7062);
    $stmt7062->execute();
    $result7062 = $stmt7062->get_result();
    $row7062 = $result7062->fetch_assoc();
    $assetId7062 = $row7062['assetId'];
    $category7062 = $row7062['category'];
    $date7062 = $row7062['date'];
    $building7062 = $row7062['building'];
    $floor7062 = $row7062['floor'];
    $room7062 = $row7062['room'];
    $status7062 = $row7062['status'];
    $assignedName7062 = $row7062['assignedName'];
    $assignedBy7062 = $row7062['assignedBy'];
    $upload_img7062 = $row7062['upload_img'];
    $description7062 = $row7062['description'];

    //FOR ID 7063
    $sql7063 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7063";
    $stmt7063 = $conn->prepare($sql7063);
    $stmt7063->execute();
    $result7063 = $stmt7063->get_result();
    $row7063 = $result7063->fetch_assoc();
    $assetId7063 = $row7063['assetId'];
    $category7063 = $row7063['category'];
    $date7063 = $row7063['date'];
    $building7063 = $row7063['building'];
    $floor7063 = $row7063['floor'];
    $room7063 = $row7063['room'];
    $status7063 = $row7063['status'];
    $assignedName7063 = $row7063['assignedName'];
    $assignedBy7063 = $row7063['assignedBy'];
    $upload_img7063 = $row7063['upload_img'];
    $description7063 = $row7063['description'];

    //FOR ID 7064
    $sql7064 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7064";
    $stmt7064 = $conn->prepare($sql7064);
    $stmt7064->execute();
    $result7064 = $stmt7064->get_result();
    $row7064 = $result7064->fetch_assoc();
    $assetId7064 = $row7064['assetId'];
    $category7064 = $row7064['category'];
    $date7064 = $row7064['date'];
    $building7064 = $row7064['building'];
    $floor7064 = $row7064['floor'];
    $room7064 = $row7064['room'];
    $status7064 = $row7064['status'];
    $assignedName7064 = $row7064['assignedName'];
    $assignedBy7064 = $row7064['assignedBy'];
    $upload_img7064 = $row7064['upload_img'];
    $description7064 = $row7064['description'];

    //FOR ID 7065
    $sql7065 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7065";
    $stmt7065 = $conn->prepare($sql7065);
    $stmt7065->execute();
    $result7065 = $stmt7065->get_result();
    $row7065 = $result7065->fetch_assoc();
    $assetId7065 = $row7065['assetId'];
    $category7065 = $row7065['category'];
    $date7065 = $row7065['date'];
    $building7065 = $row7065['building'];
    $floor7065 = $row7065['floor'];
    $room7065 = $row7065['room'];
    $status7065 = $row7065['status'];
    $assignedName7065 = $row7065['assignedName'];
    $assignedBy7065 = $row7065['assignedBy'];
    $upload_img7065 = $row7065['upload_img'];
    $description7065 = $row7065['description'];

    //FOR ID 7066
    $sql7066 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7066";
    $stmt7066 = $conn->prepare($sql7066);
    $stmt7066->execute();
    $result7066 = $stmt7066->get_result();
    $row7066 = $result7066->fetch_assoc();
    $assetId7066 = $row7066['assetId'];
    $category7066 = $row7066['category'];
    $date7066 = $row7066['date'];
    $building7066 = $row7066['building'];
    $floor7066 = $row7066['floor'];
    $room7066 = $row7066['room'];
    $status7066 = $row7066['status'];
    $assignedName7066 = $row7066['assignedName'];
    $assignedBy7066 = $row7066['assignedBy'];
    $upload_img7066 = $row7066['upload_img'];
    $description7066 = $row7066['description'];

    //FOR ID 7067
    $sql7067 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7067";
    $stmt7067 = $conn->prepare($sql7067);
    $stmt7067->execute();
    $result7067 = $stmt7067->get_result();
    $row7067 = $result7067->fetch_assoc();
    $assetId7067 = $row7067['assetId'];
    $category7067 = $row7067['category'];
    $date7067 = $row7067['date'];
    $building7067 = $row7067['building'];
    $floor7067 = $row7067['floor'];
    $room7067 = $row7067['room'];
    $status7067 = $row7067['status'];
    $assignedName7067 = $row7067['assignedName'];
    $assignedBy7067 = $row7067['assignedBy'];
    $upload_img7067 = $row7067['upload_img'];
    $description7067 = $row7067['description'];

    //FOR ID 7068
    $sql7068 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7068";
    $stmt7068 = $conn->prepare($sql7068);
    $stmt7068->execute();
    $result7068 = $stmt7068->get_result();
    $row7068 = $result7068->fetch_assoc();
    $assetId7068 = $row7068['assetId'];
    $category7068 = $row7068['category'];
    $date7068 = $row7068['date'];
    $building7068 = $row7068['building'];
    $floor7068 = $row7068['floor'];
    $room7068 = $row7068['room'];
    $status7068 = $row7068['status'];
    $assignedName7068 = $row7068['assignedName'];
    $assignedBy7068 = $row7068['assignedBy'];
    $upload_img7068 = $row7068['upload_img'];
    $description7068 = $row7068['description'];

    //FOR ID 7069
    $sql7069 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7069";
    $stmt7069 = $conn->prepare($sql7069);
    $stmt7069->execute();
    $result7069 = $stmt7069->get_result();
    $row7069 = $result7069->fetch_assoc();
    $assetId7069 = $row7069['assetId'];
    $category7069 = $row7069['category'];
    $date7069 = $row7069['date'];
    $building7069 = $row7069['building'];
    $floor7069 = $row7069['floor'];
    $room7069 = $row7069['room'];
    $status7069 = $row7069['status'];
    $assignedName7069 = $row7069['assignedName'];
    $assignedBy7069 = $row7069['assignedBy'];
    $upload_img7069 = $row7069['upload_img'];
    $description7069 = $row7069['description'];

    //FOR ID 7070
    $sql7070 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7070";
    $stmt7070 = $conn->prepare($sql7070);
    $stmt7070->execute();
    $result7070 = $stmt7070->get_result();
    $row7070 = $result7070->fetch_assoc();
    $assetId7070 = $row7070['assetId'];
    $category7070 = $row7070['category'];
    $date7070 = $row7070['date'];
    $building7070 = $row7070['building'];
    $floor7070 = $row7070['floor'];
    $room7070 = $row7070['room'];
    $status7070 = $row7070['status'];
    $assignedName7070 = $row7070['assignedName'];
    $assignedBy7070 = $row7070['assignedBy'];
    $upload_img7070 = $row7070['upload_img'];
    $description7070 = $row7070['description'];

    //FOR ID 7071
    $sql7071 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7071";
    $stmt7071 = $conn->prepare($sql7071);
    $stmt7071->execute();
    $result7071 = $stmt7071->get_result();
    $row7071 = $result7071->fetch_assoc();
    $assetId7071 = $row7071['assetId'];
    $category7071 = $row7071['category'];
    $date7071 = $row7071['date'];
    $building7071 = $row7071['building'];
    $floor7071 = $row7071['floor'];
    $room7071 = $row7071['room'];
    $status7071 = $row7071['status'];
    $assignedName7071 = $row7071['assignedName'];
    $assignedBy7071 = $row7071['assignedBy'];
    $upload_img7071 = $row7071['upload_img'];
    $description7071 = $row7071['description'];

    //FOR ID 7072
    $sql7072 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7072";
    $stmt7072 = $conn->prepare($sql7072);
    $stmt7072->execute();
    $result7072 = $stmt7072->get_result();
    $row7072 = $result7072->fetch_assoc();
    $assetId7072 = $row7072['assetId'];
    $category7072 = $row7072['category'];
    $date7072 = $row7072['date'];
    $building7072 = $row7072['building'];
    $floor7072 = $row7072['floor'];
    $room7072 = $row7072['room'];
    $status7072 = $row7072['status'];
    $assignedName7072 = $row7072['assignedName'];
    $assignedBy7072 = $row7072['assignedBy'];
    $upload_img7072 = $row7072['upload_img'];
    $description7072 = $row7072['description'];

    //FOR ID 7073
    $sql7073 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7073";
    $stmt7073 = $conn->prepare($sql7073);
    $stmt7073->execute();
    $result7073 = $stmt7073->get_result();
    $row7073 = $result7073->fetch_assoc();
    $assetId7073 = $row7073['assetId'];
    $category7073 = $row7073['category'];
    $date7073 = $row7073['date'];
    $building7073 = $row7073['building'];
    $floor7073 = $row7073['floor'];
    $room7073 = $row7073['room'];
    $status7073 = $row7073['status'];
    $assignedName7073 = $row7073['assignedName'];
    $assignedBy7073 = $row7073['assignedBy'];
    $upload_img7073 = $row7073['upload_img'];
    $description7073 = $row7073['description'];

    //FOR ID 7074
    $sql7074 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7074";
    $stmt7074 = $conn->prepare($sql7074);
    $stmt7074->execute();
    $result7074 = $stmt7074->get_result();
    $row7074 = $result7074->fetch_assoc();
    $assetId7074 = $row7074['assetId'];
    $category7074 = $row7074['category'];
    $date7074 = $row7074['date'];
    $building7074 = $row7074['building'];
    $floor7074 = $row7074['floor'];
    $room7074 = $row7074['room'];
    $status7074 = $row7074['status'];
    $assignedName7074 = $row7074['assignedName'];
    $assignedBy7074 = $row7074['assignedBy'];
    $upload_img7074 = $row7074['upload_img'];
    $description7074 = $row7074['description'];

    //FOR ID 7075
    $sql7075 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7075";
    $stmt7075 = $conn->prepare($sql7075);
    $stmt7075->execute();
    $result7075 = $stmt7075->get_result();
    $row7075 = $result7075->fetch_assoc();
    $assetId7075 = $row7075['assetId'];
    $category7075 = $row7075['category'];
    $date7075 = $row7075['date'];
    $building7075 = $row7075['building'];
    $floor7075 = $row7075['floor'];
    $room7075 = $row7075['room'];
    $status7075 = $row7075['status'];
    $assignedName7075 = $row7075['assignedName'];
    $assignedBy7075 = $row7075['assignedBy'];
    $upload_img7075 = $row7075['upload_img'];
    $description7075 = $row7075['description'];

    //FOR ID 7076
    $sql7076 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7076";
    $stmt7076 = $conn->prepare($sql7076);
    $stmt7076->execute();
    $result7076 = $stmt7076->get_result();
    $row7076 = $result7076->fetch_assoc();
    $assetId7076 = $row7076['assetId'];
    $category7076 = $row7076['category'];
    $date7076 = $row7076['date'];
    $building7076 = $row7076['building'];
    $floor7076 = $row7076['floor'];
    $room7076 = $row7076['room'];
    $status7076 = $row7076['status'];
    $assignedName7076 = $row7076['assignedName'];
    $assignedBy7076 = $row7076['assignedBy'];
    $upload_img7076 = $row7076['upload_img'];
    $description7076 = $row7076['description'];

    //FOR ID 7077
    $sql7077 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7077";
    $stmt7077 = $conn->prepare($sql7077);
    $stmt7077->execute();
    $result7077 = $stmt7077->get_result();
    $row7077 = $result7077->fetch_assoc();
    $assetId7077 = $row7077['assetId'];
    $category7077 = $row7077['category'];
    $date7077 = $row7077['date'];
    $building7077 = $row7077['building'];
    $floor7077 = $row7077['floor'];
    $room7077 = $row7077['room'];
    $status7077 = $row7077['status'];
    $assignedName7077 = $row7077['assignedName'];
    $assignedBy7077 = $row7077['assignedBy'];
    $upload_img7077 = $row7077['upload_img'];
    $description7077 = $row7077['description'];

    //FOR ID 7078
    $sql7078 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7078";
    $stmt7078 = $conn->prepare($sql7078);
    $stmt7078->execute();
    $result7078 = $stmt7078->get_result();
    $row7078 = $result7078->fetch_assoc();
    $assetId7078 = $row7078['assetId'];
    $category7078 = $row7078['category'];
    $date7078 = $row7078['date'];
    $building7078 = $row7078['building'];
    $floor7078 = $row7078['floor'];
    $room7078 = $row7078['room'];
    $status7078 = $row7078['status'];
    $assignedName7078 = $row7078['assignedName'];
    $assignedBy7078 = $row7078['assignedBy'];
    $upload_img7078 = $row7078['upload_img'];
    $description7078 = $row7078['description'];

    //FOR ID 7079
    $sql7079 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 7079";
    $stmt7079 = $conn->prepare($sql7079);
    $stmt7079->execute();
    $result7079 = $stmt7079->get_result();
    $row7079 = $result7079->fetch_assoc();
    $assetId7079 = $row7079['assetId'];
    $category7079 = $row7079['category'];
    $date7079 = $row7079['date'];
    $building7079 = $row7079['building'];
    $floor7079 = $row7079['floor'];
    $room7079 = $row7079['room'];
    $status7079 = $row7079['status'];
    $assignedName7079 = $row7079['assignedName'];
    $assignedBy7079 = $row7079['assignedBy'];
    $upload_img7079 = $row7079['upload_img'];
    $description7079 = $row7079['description'];

    //FOR ID 7275
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7275'])) {
        // Get form data
        $assetId7275 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7275 = $_POST['status']; // Get the status from the form
        $description7275 = $_POST['description']; // Get the description from the form
        $room7275 = $_POST['room']; // Get the room from the form
        $assignedBy7275 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7275 = $status7275 === 'Need Repair' ? '' : $assignedName7275;

        // Prepare SQL query to update the asset
        $sql7275 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7275 = $conn->prepare($sql7275);
        $stmt7275->bind_param('sssssi', $status7275, $assignedName7275, $assignedBy7275, $description7275, $room7275, $assetId7275);

        if ($stmt7275->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7275 to $status7275.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7275->close();
    }
    //FOR ID 7274
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7274'])) {
        // Get form data
        $assetId7274 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7274 = $_POST['status']; // Get the status from the form
        $description7274 = $_POST['description']; // Get the description from the form
        $room7274 = $_POST['room']; // Get the room from the form
        $assignedBy7274 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7274 = $status7274 === 'Need Repair' ? '' : $assignedName7274;

        // Prepare SQL query to update the asset
        $sql7274 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7274 = $conn->prepare($sql7274);
        $stmt7274->bind_param('sssssi', $status7274, $assignedName7274, $assignedBy7274, $description7274, $room7274, $assetId7274);

        if ($stmt7274->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7274 to $status7274.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7274->close();
    }
    //FOR ID 7273
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7273'])) {
        // Get form data
        $assetId7273 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7273 = $_POST['status']; // Get the status from the form
        $description7273 = $_POST['description']; // Get the description from the form
        $room7273 = $_POST['room']; // Get the room from the form
        $assignedBy7273 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7273 = $status7273 === 'Need Repair' ? '' : $assignedName7273;

        // Prepare SQL query to update the asset
        $sql7273 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7273 = $conn->prepare($sql7273);
        $stmt7273->bind_param('sssssi', $status7273, $assignedName7273, $assignedBy7273, $description7273, $room7273, $assetId7273);

        if ($stmt7273->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7273 to $status7273.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7273->close();
    }
    //FOR ID 7272
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7272'])) {
        // Get form data
        $assetId7272 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7272 = $_POST['status']; // Get the status from the form
        $description7272 = $_POST['description']; // Get the description from the form
        $room7272 = $_POST['room']; // Get the room from the form
        $assignedBy7272 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7272 = $status7272 === 'Need Repair' ? '' : $assignedName7272;

        // Prepare SQL query to update the asset
        $sql7272 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7272 = $conn->prepare($sql7272);
        $stmt7272->bind_param('sssssi', $status7272, $assignedName7272, $assignedBy7272, $description7272, $room7272, $assetId7272);

        if ($stmt7272->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7272 to $status7272.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7272->close();
    }
    //FOR ID 6947
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6947'])) {
        // Get form data
        $assetId6947 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6947 = $_POST['status']; // Get the status from the form
        $description6947 = $_POST['description']; // Get the description from the form
        $room6947 = $_POST['room']; // Get the room from the form
        $assignedBy6947 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6947 = $status6947 === 'Need Repair' ? '' : $assignedName6947;

        // Prepare SQL query to update the asset
        $sql6947 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6947 = $conn->prepare($sql6947);
        $stmt6947->bind_param('sssssi', $status6947, $assignedName6947, $assignedBy6947, $description6947, $room6947, $assetId6947);

        if ($stmt6947->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6947 to $status6947.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6947->close();
    }
    //FOR ID 6946
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6946'])) {
        // Get form data
        $assetId6946 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6946 = $_POST['status']; // Get the status from the form
        $description6946 = $_POST['description']; // Get the description from the form
        $room6946 = $_POST['room']; // Get the room from the form
        $assignedBy6946 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6946 = $status6946 === 'Need Repair' ? '' : $assignedName6946;

        // Prepare SQL query to update the asset
        $sql6946 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6946 = $conn->prepare($sql6946);
        $stmt6946->bind_param('sssssi', $status6946, $assignedName6946, $assignedBy6946, $description6946, $room6946, $assetId6946);

        if ($stmt6946->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6946 to $status6946.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6946->close();
    }
    //FOR ID 6945
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6945'])) {
        // Get form data
        $assetId6945 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6945 = $_POST['status']; // Get the status from the form
        $description6945 = $_POST['description']; // Get the description from the form
        $room6945 = $_POST['room']; // Get the room from the form
        $assignedBy6945 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6945 = $status6945 === 'Need Repair' ? '' : $assignedName6945;

        // Prepare SQL query to update the asset
        $sql6945 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6945 = $conn->prepare($sql6945);
        $stmt6945->bind_param('sssssi', $status6945, $assignedName6945, $assignedBy6945, $description6945, $room6945, $assetId6945);

        if ($stmt6945->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6945 to $status6945.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6945->close();
    }
    //FOR ID 6944
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6944'])) {
        // Get form data
        $assetId6944 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6944 = $_POST['status']; // Get the status from the form
        $description6944 = $_POST['description']; // Get the description from the form
        $room6944 = $_POST['room']; // Get the room from the form
        $assignedBy6944 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6944 = $status6944 === 'Need Repair' ? '' : $assignedName6944;

        // Prepare SQL query to update the asset
        $sql6944 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6944 = $conn->prepare($sql6944);
        $stmt6944->bind_param('sssssi', $status6944, $assignedName6944, $assignedBy6944, $description6944, $room6944, $assetId6944);

        if ($stmt6944->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6944 to $status6944.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6944->close();
    }
    //FOR ID 6943
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6943'])) {
        // Get form data
        $assetId6943 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6943 = $_POST['status']; // Get the status from the form
        $description6943 = $_POST['description']; // Get the description from the form
        $room6943 = $_POST['room']; // Get the room from the form
        $assignedBy6943 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6943 = $status6943 === 'Need Repair' ? '' : $assignedName6943;

        // Prepare SQL query to update the asset
        $sql6943 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6943 = $conn->prepare($sql6943);
        $stmt6943->bind_param('sssssi', $status6943, $assignedName6943, $assignedBy6943, $description6943, $room6943, $assetId6943);

        if ($stmt6943->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6943 to $status6943.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6943->close();
    }
    //FOR ID 6942
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6942'])) {
        // Get form data
        $assetId6942 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6942 = $_POST['status']; // Get the status from the form
        $description6942 = $_POST['description']; // Get the description from the form
        $room6942 = $_POST['room']; // Get the room from the form
        $assignedBy6942 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6942 = $status6942 === 'Need Repair' ? '' : $assignedName6942;

        // Prepare SQL query to update the asset
        $sql6942 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6942 = $conn->prepare($sql6942);
        $stmt6942->bind_param('sssssi', $status6942, $assignedName6942, $assignedBy6942, $description6942, $room6942, $assetId6942);

        if ($stmt6942->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6942 to $status6942.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6942->close();
    }
    //FOR ID 6941
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6941'])) {
        // Get form data
        $assetId6941 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6941 = $_POST['status']; // Get the status from the form
        $description6941 = $_POST['description']; // Get the description from the form
        $room6941 = $_POST['room']; // Get the room from the form
        $assignedBy6941 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6941 = $status6941 === 'Need Repair' ? '' : $assignedName6941;

        // Prepare SQL query to update the asset
        $sql6941 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6941 = $conn->prepare($sql6941);
        $stmt6941->bind_param('sssssi', $status6941, $assignedName6941, $assignedBy6941, $description6941, $room6941, $assetId6941);

        if ($stmt6941->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6941 to $status6941.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6941->close();
    }
    //FOR ID 6940
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6940'])) {
        // Get form data
        $assetId6940 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6940 = $_POST['status']; // Get the status from the form
        $description6940 = $_POST['description']; // Get the description from the form
        $room6940 = $_POST['room']; // Get the room from the form
        $assignedBy6940 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6940 = $status6940 === 'Need Repair' ? '' : $assignedName6940;

        // Prepare SQL query to update the asset
        $sql6940 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6940 = $conn->prepare($sql6940);
        $stmt6940->bind_param('sssssi', $status6940, $assignedName6940, $assignedBy6940, $description6940, $room6940, $assetId6940);

        if ($stmt6940->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6940 to $status6940.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6940->close();
    }
    //FOR ID 6939
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6939'])) {
        // Get form data
        $assetId6939 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6939 = $_POST['status']; // Get the status from the form
        $description6939 = $_POST['description']; // Get the description from the form
        $room6939 = $_POST['room']; // Get the room from the form
        $assignedBy6939 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6939 = $status6939 === 'Need Repair' ? '' : $assignedName6939;

        // Prepare SQL query to update the asset
        $sql6939 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6939 = $conn->prepare($sql6939);
        $stmt6939->bind_param('sssssi', $status6939, $assignedName6939, $assignedBy6939, $description6939, $room6939, $assetId6939);

        if ($stmt6939->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6939 to $status6939.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6939->close();
    }
    //FOR ID 6938
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6938'])) {
        // Get form data
        $assetId6938 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6938 = $_POST['status']; // Get the status from the form
        $description6938 = $_POST['description']; // Get the description from the form
        $room6938 = $_POST['room']; // Get the room from the form
        $assignedBy6938 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6938 = $status6938 === 'Need Repair' ? '' : $assignedName6938;

        // Prepare SQL query to update the asset
        $sql6938 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6938 = $conn->prepare($sql6938);
        $stmt6938->bind_param('sssssi', $status6938, $assignedName6938, $assignedBy6938, $description6938, $room6938, $assetId6938);

        if ($stmt6938->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6938 to $status6938.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6938->close();
    }
    //FOR ID 6937
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6937'])) {
        // Get form data
        $assetId6937 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6937 = $_POST['status']; // Get the status from the form
        $description6937 = $_POST['description']; // Get the description from the form
        $room6937 = $_POST['room']; // Get the room from the form
        $assignedBy6937 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6937 = $status6937 === 'Need Repair' ? '' : $assignedName6937;

        // Prepare SQL query to update the asset
        $sql6937 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6937 = $conn->prepare($sql6937);
        $stmt6937->bind_param('sssssi', $status6937, $assignedName6937, $assignedBy6937, $description6937, $room6937, $assetId6937);

        if ($stmt6937->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6937 to $status6937.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6937->close();
    }
    //FOR ID 6936
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6936'])) {
        // Get form data
        $assetId6936 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6936 = $_POST['status']; // Get the status from the form
        $description6936 = $_POST['description']; // Get the description from the form
        $room6936 = $_POST['room']; // Get the room from the form
        $assignedBy6936 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6936 = $status6936 === 'Need Repair' ? '' : $assignedName6936;

        // Prepare SQL query to update the asset
        $sql6936 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6936 = $conn->prepare($sql6936);
        $stmt6936->bind_param('sssssi', $status6936, $assignedName6936, $assignedBy6936, $description6936, $room6936, $assetId6936);

        if ($stmt6936->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6936 to $status6936.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6936->close();
    }
    //FOR ID 6935
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6935'])) {
        // Get form data
        $assetId6935 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6935 = $_POST['status']; // Get the status from the form
        $description6935 = $_POST['description']; // Get the description from the form
        $room6935 = $_POST['room']; // Get the room from the form
        $assignedBy6935 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6935 = $status6935 === 'Need Repair' ? '' : $assignedName6935;

        // Prepare SQL query to update the asset
        $sql6935 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6935 = $conn->prepare($sql6935);
        $stmt6935->bind_param('sssssi', $status6935, $assignedName6935, $assignedBy6935, $description6935, $room6935, $assetId6935);

        if ($stmt6935->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6935 to $status6935.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6935->close();
    }
    //FOR ID 6934
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6934'])) {
        // Get form data
        $assetId6934 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6934 = $_POST['status']; // Get the status from the form
        $description6934 = $_POST['description']; // Get the description from the form
        $room6934 = $_POST['room']; // Get the room from the form
        $assignedBy6934 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6934 = $status6934 === 'Need Repair' ? '' : $assignedName6934;

        // Prepare SQL query to update the asset
        $sql6934 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6934 = $conn->prepare($sql6934);
        $stmt6934->bind_param('sssssi', $status6934, $assignedName6934, $assignedBy6934, $description6934, $room6934, $assetId6934);

        if ($stmt6934->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6934 to $status6934.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6934->close();
    }
    //FOR ID 6933
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6933'])) {
        // Get form data
        $assetId6933 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6933 = $_POST['status']; // Get the status from the form
        $description6933 = $_POST['description']; // Get the description from the form
        $room6933 = $_POST['room']; // Get the room from the form
        $assignedBy6933 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6933 = $status6933 === 'Need Repair' ? '' : $assignedName6933;

        // Prepare SQL query to update the asset
        $sql6933 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6933 = $conn->prepare($sql6933);
        $stmt6933->bind_param('sssssi', $status6933, $assignedName6933, $assignedBy6933, $description6933, $room6933, $assetId6933);

        if ($stmt6933->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6933 to $status6933.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6933->close();
    }
    //FOR ID 6932
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6932'])) {
        // Get form data
        $assetId6932 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6932 = $_POST['status']; // Get the status from the form
        $description6932 = $_POST['description']; // Get the description from the form
        $room6932 = $_POST['room']; // Get the room from the form
        $assignedBy6932 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6932 = $status6932 === 'Need Repair' ? '' : $assignedName6932;

        // Prepare SQL query to update the asset
        $sql6932 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6932 = $conn->prepare($sql6932);
        $stmt6932->bind_param('sssssi', $status6932, $assignedName6932, $assignedBy6932, $description6932, $room6932, $assetId6932);

        if ($stmt6932->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6932 to $status6932.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6932->close();
    }
    //FOR ID 6931
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6931'])) {
        // Get form data
        $assetId6931 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6931 = $_POST['status']; // Get the status from the form
        $description6931 = $_POST['description']; // Get the description from the form
        $room6931 = $_POST['room']; // Get the room from the form
        $assignedBy6931 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6931 = $status6931 === 'Need Repair' ? '' : $assignedName6931;

        // Prepare SQL query to update the asset
        $sql6931 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6931 = $conn->prepare($sql6931);
        $stmt6931->bind_param('sssssi', $status6931, $assignedName6931, $assignedBy6931, $description6931, $room6931, $assetId6931);

        if ($stmt6931->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6931 to $status6931.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6931->close();
    }
    //FOR ID 6930
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6930'])) {
        // Get form data
        $assetId6930 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6930 = $_POST['status']; // Get the status from the form
        $description6930 = $_POST['description']; // Get the description from the form
        $room6930 = $_POST['room']; // Get the room from the form
        $assignedBy6930 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6930 = $status6930 === 'Need Repair' ? '' : $assignedName6930;

        // Prepare SQL query to update the asset
        $sql6930 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6930 = $conn->prepare($sql6930);
        $stmt6930->bind_param('sssssi', $status6930, $assignedName6930, $assignedBy6930, $description6930, $room6930, $assetId6930);

        if ($stmt6930->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6930 to $status6930.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6930->close();
    }
    //FOR ID 6929
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6929'])) {
        // Get form data
        $assetId6929 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6929 = $_POST['status']; // Get the status from the form
        $description6929 = $_POST['description']; // Get the description from the form
        $room6929 = $_POST['room']; // Get the room from the form
        $assignedBy6929 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6929 = $status6929 === 'Need Repair' ? '' : $assignedName6929;

        // Prepare SQL query to update the asset
        $sql6929 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6929 = $conn->prepare($sql6929);
        $stmt6929->bind_param('sssssi', $status6929, $assignedName6929, $assignedBy6929, $description6929, $room6929, $assetId6929);

        if ($stmt6929->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6929 to $status6929.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6929->close();
    }
    //FOR ID 6928
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6928'])) {
        // Get form data
        $assetId6928 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6928 = $_POST['status']; // Get the status from the form
        $description6928 = $_POST['description']; // Get the description from the form
        $room6928 = $_POST['room']; // Get the room from the form
        $assignedBy6928 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6928 = $status6928 === 'Need Repair' ? '' : $assignedName6928;

        // Prepare SQL query to update the asset
        $sql6928 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6928 = $conn->prepare($sql6928);
        $stmt6928->bind_param('sssssi', $status6928, $assignedName6928, $assignedBy6928, $description6928, $room6928, $assetId6928);

        if ($stmt6928->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6928 to $status6928.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6928->close();
    }
    //FOR ID 6927
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6927'])) {
        // Get form data
        $assetId6927 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6927 = $_POST['status']; // Get the status from the form
        $description6927 = $_POST['description']; // Get the description from the form
        $room6927 = $_POST['room']; // Get the room from the form
        $assignedBy6927 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6927 = $status6927 === 'Need Repair' ? '' : $assignedName6927;

        // Prepare SQL query to update the asset
        $sql6927 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6927 = $conn->prepare($sql6927);
        $stmt6927->bind_param('sssssi', $status6927, $assignedName6927, $assignedBy6927, $description6927, $room6927, $assetId6927);

        if ($stmt6927->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6927 to $status6927.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6927->close();
    }
    //FOR ID 6926
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6926'])) {
        // Get form data
        $assetId6926 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6926 = $_POST['status']; // Get the status from the form
        $description6926 = $_POST['description']; // Get the description from the form
        $room6926 = $_POST['room']; // Get the room from the form
        $assignedBy6926 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6926 = $status6926 === 'Need Repair' ? '' : $assignedName6926;

        // Prepare SQL query to update the asset
        $sql6926 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6926 = $conn->prepare($sql6926);
        $stmt6926->bind_param('sssssi', $status6926, $assignedName6926, $assignedBy6926, $description6926, $room6926, $assetId6926);

        if ($stmt6926->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6926 to $status6926.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6926->close();
    }
    //FOR ID 6925
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6925'])) {
        // Get form data
        $assetId6925 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6925 = $_POST['status']; // Get the status from the form
        $description6925 = $_POST['description']; // Get the description from the form
        $room6925 = $_POST['room']; // Get the room from the form
        $assignedBy6925 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6925 = $status6925 === 'Need Repair' ? '' : $assignedName6925;

        // Prepare SQL query to update the asset
        $sql6925 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6925 = $conn->prepare($sql6925);
        $stmt6925->bind_param('sssssi', $status6925, $assignedName6925, $assignedBy6925, $description6925, $room6925, $assetId6925);

        if ($stmt6925->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6925 to $status6925.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6925->close();
    }
    //FOR ID 6924
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6924'])) {
        // Get form data
        $assetId6924 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6924 = $_POST['status']; // Get the status from the form
        $description6924 = $_POST['description']; // Get the description from the form
        $room6924 = $_POST['room']; // Get the room from the form
        $assignedBy6924 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6924 = $status6924 === 'Need Repair' ? '' : $assignedName6924;

        // Prepare SQL query to update the asset
        $sql6924 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6924 = $conn->prepare($sql6924);
        $stmt6924->bind_param('sssssi', $status6924, $assignedName6924, $assignedBy6924, $description6924, $room6924, $assetId6924);

        if ($stmt6924->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6924 to $status6924.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6924->close();
    }
    //FOR ID 6923
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6923'])) {
        // Get form data
        $assetId6923 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6923 = $_POST['status']; // Get the status from the form
        $description6923 = $_POST['description']; // Get the description from the form
        $room6923 = $_POST['room']; // Get the room from the form
        $assignedBy6923 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6923 = $status6923 === 'Need Repair' ? '' : $assignedName6923;

        // Prepare SQL query to update the asset
        $sql6923 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6923 = $conn->prepare($sql6923);
        $stmt6923->bind_param('sssssi', $status6923, $assignedName6923, $assignedBy6923, $description6923, $room6923, $assetId6923);

        if ($stmt6923->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6923 to $status6923.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6923->close();
    }
    //FOR ID 6922
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6922'])) {
        // Get form data
        $assetId6922 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6922 = $_POST['status']; // Get the status from the form
        $description6922 = $_POST['description']; // Get the description from the form
        $room6922 = $_POST['room']; // Get the room from the form
        $assignedBy6922 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6922 = $status6922 === 'Need Repair' ? '' : $assignedName6922;

        // Prepare SQL query to update the asset
        $sql6922 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6922 = $conn->prepare($sql6922);
        $stmt6922->bind_param('sssssi', $status6922, $assignedName6922, $assignedBy6922, $description6922, $room6922, $assetId6922);

        if ($stmt6922->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6922 to $status6922.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6922->close();
    }
    //FOR ID 6921
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6921'])) {
        // Get form data
        $assetId6921 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6921 = $_POST['status']; // Get the status from the form
        $description6921 = $_POST['description']; // Get the description from the form
        $room6921 = $_POST['room']; // Get the room from the form
        $assignedBy6921 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6921 = $status6921 === 'Need Repair' ? '' : $assignedName6921;

        // Prepare SQL query to update the asset
        $sql6921 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6921 = $conn->prepare($sql6921);
        $stmt6921->bind_param('sssssi', $status6921, $assignedName6921, $assignedBy6921, $description6921, $room6921, $assetId6921);

        if ($stmt6921->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6921 to $status6921.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6921->close();
    }
    //FOR ID 6920
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6920'])) {
        // Get form data
        $assetId6920 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6920 = $_POST['status']; // Get the status from the form
        $description6920 = $_POST['description']; // Get the description from the form
        $room6920 = $_POST['room']; // Get the room from the form
        $assignedBy6920 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6920 = $status6920 === 'Need Repair' ? '' : $assignedName6920;

        // Prepare SQL query to update the asset
        $sql6920 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6920 = $conn->prepare($sql6920);
        $stmt6920->bind_param('sssssi', $status6920, $assignedName6920, $assignedBy6920, $description6920, $room6920, $assetId6920);

        if ($stmt6920->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6920 to $status6920.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6920->close();
    }
    //FOR ID 6919
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6919'])) {
        // Get form data
        $assetId6919 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6919 = $_POST['status']; // Get the status from the form
        $description6919 = $_POST['description']; // Get the description from the form
        $room6919 = $_POST['room']; // Get the room from the form
        $assignedBy6919 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6919 = $status6919 === 'Need Repair' ? '' : $assignedName6919;

        // Prepare SQL query to update the asset
        $sql6919 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6919 = $conn->prepare($sql6919);
        $stmt6919->bind_param('sssssi', $status6919, $assignedName6919, $assignedBy6919, $description6919, $room6919, $assetId6919);

        if ($stmt6919->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6919 to $status6919.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6919->close();
    }
    //FOR ID 6918
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6918'])) {
        // Get form data
        $assetId6918 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6918 = $_POST['status']; // Get the status from the form
        $description6918 = $_POST['description']; // Get the description from the form
        $room6918 = $_POST['room']; // Get the room from the form
        $assignedBy6918 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6918 = $status6918 === 'Need Repair' ? '' : $assignedName6918;

        // Prepare SQL query to update the asset
        $sql6918 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6918 = $conn->prepare($sql6918);
        $stmt6918->bind_param('sssssi', $status6918, $assignedName6918, $assignedBy6918, $description6918, $room6918, $assetId6918);

        if ($stmt6918->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6918 to $status6918.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6918->close();
    }
    //FOR ID 6917
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6917'])) {
        // Get form data
        $assetId6917 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6917 = $_POST['status']; // Get the status from the form
        $description6917 = $_POST['description']; // Get the description from the form
        $room6917 = $_POST['room']; // Get the room from the form
        $assignedBy6917 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6917 = $status6917 === 'Need Repair' ? '' : $assignedName6917;

        // Prepare SQL query to update the asset
        $sql6917 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6917 = $conn->prepare($sql6917);
        $stmt6917->bind_param('sssssi', $status6917, $assignedName6917, $assignedBy6917, $description6917, $room6917, $assetId6917);

        if ($stmt6917->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6917 to $status6917.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6917->close();
    }
    //FOR ID 6916
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6916'])) {
        // Get form data
        $assetId6916 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6916 = $_POST['status']; // Get the status from the form
        $description6916 = $_POST['description']; // Get the description from the form
        $room6916 = $_POST['room']; // Get the room from the form
        $assignedBy6916 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6916 = $status6916 === 'Need Repair' ? '' : $assignedName6916;

        // Prepare SQL query to update the asset
        $sql6916 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6916 = $conn->prepare($sql6916);
        $stmt6916->bind_param('sssssi', $status6916, $assignedName6916, $assignedBy6916, $description6916, $room6916, $assetId6916);

        if ($stmt6916->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6916 to $status6916.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6916->close();
    }
    //FOR ID 6915
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6915'])) {
        // Get form data
        $assetId6915 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6915 = $_POST['status']; // Get the status from the form
        $description6915 = $_POST['description']; // Get the description from the form
        $room6915 = $_POST['room']; // Get the room from the form
        $assignedBy6915 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6915 = $status6915 === 'Need Repair' ? '' : $assignedName6915;

        // Prepare SQL query to update the asset
        $sql6915 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6915 = $conn->prepare($sql6915);
        $stmt6915->bind_param('sssssi', $status6915, $assignedName6915, $assignedBy6915, $description6915, $room6915, $assetId6915);

        if ($stmt6915->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6915 to $status6915.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6915->close();
    }
    //FOR ID 6914
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6914'])) {
        // Get form data
        $assetId6914 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6914 = $_POST['status']; // Get the status from the form
        $description6914 = $_POST['description']; // Get the description from the form
        $room6914 = $_POST['room']; // Get the room from the form
        $assignedBy6914 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6914 = $status6914 === 'Need Repair' ? '' : $assignedName6914;

        // Prepare SQL query to update the asset
        $sql6914 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6914 = $conn->prepare($sql6914);
        $stmt6914->bind_param('sssssi', $status6914, $assignedName6914, $assignedBy6914, $description6914, $room6914, $assetId6914);

        if ($stmt6914->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6914 to $status6914.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6914->close();
    }
    //FOR ID 6913
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6913'])) {
        // Get form data
        $assetId6913 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6913 = $_POST['status']; // Get the status from the form
        $description6913 = $_POST['description']; // Get the description from the form
        $room6913 = $_POST['room']; // Get the room from the form
        $assignedBy6913 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6913 = $status6913 === 'Need Repair' ? '' : $assignedName6913;

        // Prepare SQL query to update the asset
        $sql6913 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6913 = $conn->prepare($sql6913);
        $stmt6913->bind_param('sssssi', $status6913, $assignedName6913, $assignedBy6913, $description6913, $room6913, $assetId6913);

        if ($stmt6913->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6913 to $status6913.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6913->close();
    }
    //FOR ID 6912
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6912'])) {
        // Get form data
        $assetId6912 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6912 = $_POST['status']; // Get the status from the form
        $description6912 = $_POST['description']; // Get the description from the form
        $room6912 = $_POST['room']; // Get the room from the form
        $assignedBy6912 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6912 = $status6912 === 'Need Repair' ? '' : $assignedName6912;

        // Prepare SQL query to update the asset
        $sql6912 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6912 = $conn->prepare($sql6912);
        $stmt6912->bind_param('sssssi', $status6912, $assignedName6912, $assignedBy6912, $description6912, $room6912, $assetId6912);

        if ($stmt6912->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6912 to $status6912.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6912->close();
    }
    //FOR ID 6911
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6911'])) {
        // Get form data
        $assetId6911 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6911 = $_POST['status']; // Get the status from the form
        $description6911 = $_POST['description']; // Get the description from the form
        $room6911 = $_POST['room']; // Get the room from the form
        $assignedBy6911 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6911 = $status6911 === 'Need Repair' ? '' : $assignedName6911;

        // Prepare SQL query to update the asset
        $sql6911 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6911 = $conn->prepare($sql6911);
        $stmt6911->bind_param('sssssi', $status6911, $assignedName6911, $assignedBy6911, $description6911, $room6911, $assetId6911);

        if ($stmt6911->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6911 to $status6911.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6911->close();
    }
    //FOR ID 6910
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6910'])) {
        // Get form data
        $assetId6910 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6910 = $_POST['status']; // Get the status from the form
        $description6910 = $_POST['description']; // Get the description from the form
        $room6910 = $_POST['room']; // Get the room from the form
        $assignedBy6910 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6910 = $status6910 === 'Need Repair' ? '' : $assignedName6910;

        // Prepare SQL query to update the asset
        $sql6910 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6910 = $conn->prepare($sql6910);
        $stmt6910->bind_param('sssssi', $status6910, $assignedName6910, $assignedBy6910, $description6910, $room6910, $assetId6910);

        if ($stmt6910->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6910 to $status6910.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6910->close();
    }
    //FOR ID 6909
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6909'])) {
        // Get form data
        $assetId6909 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6909 = $_POST['status']; // Get the status from the form
        $description6909 = $_POST['description']; // Get the description from the form
        $room6909 = $_POST['room']; // Get the room from the form
        $assignedBy6909 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6909 = $status6909 === 'Need Repair' ? '' : $assignedName6909;

        // Prepare SQL query to update the asset
        $sql6909 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6909 = $conn->prepare($sql6909);
        $stmt6909->bind_param('sssssi', $status6909, $assignedName6909, $assignedBy6909, $description6909, $room6909, $assetId6909);

        if ($stmt6909->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6909 to $status6909.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6909->close();
    }
    //FOR ID 6908
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6908'])) {
        // Get form data
        $assetId6908 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6908 = $_POST['status']; // Get the status from the form
        $description6908 = $_POST['description']; // Get the description from the form
        $room6908 = $_POST['room']; // Get the room from the form
        $assignedBy6908 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6908 = $status6908 === 'Need Repair' ? '' : $assignedName6908;

        // Prepare SQL query to update the asset
        $sql6908 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6908 = $conn->prepare($sql6908);
        $stmt6908->bind_param('sssssi', $status6908, $assignedName6908, $assignedBy6908, $description6908, $room6908, $assetId6908);

        if ($stmt6908->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6908 to $status6908.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6908->close();
    }
    //FOR ID 6907
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6907'])) {
        // Get form data
        $assetId6907 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6907 = $_POST['status']; // Get the status from the form
        $description6907 = $_POST['description']; // Get the description from the form
        $room6907 = $_POST['room']; // Get the room from the form
        $assignedBy6907 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6907 = $status6907 === 'Need Repair' ? '' : $assignedName6907;

        // Prepare SQL query to update the asset
        $sql6907 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6907 = $conn->prepare($sql6907);
        $stmt6907->bind_param('sssssi', $status6907, $assignedName6907, $assignedBy6907, $description6907, $room6907, $assetId6907);

        if ($stmt6907->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6907 to $status6907.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6907->close();
    }
    //FOR ID 6906
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6906'])) {
        // Get form data
        $assetId6906 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6906 = $_POST['status']; // Get the status from the form
        $description6906 = $_POST['description']; // Get the description from the form
        $room6906 = $_POST['room']; // Get the room from the form
        $assignedBy6906 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6906 = $status6906 === 'Need Repair' ? '' : $assignedName6906;

        // Prepare SQL query to update the asset
        $sql6906 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6906 = $conn->prepare($sql6906);
        $stmt6906->bind_param('sssssi', $status6906, $assignedName6906, $assignedBy6906, $description6906, $room6906, $assetId6906);

        if ($stmt6906->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6906 to $status6906.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6906->close();
    }
    //FOR ID 6905
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6905'])) {
        // Get form data
        $assetId6905 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6905 = $_POST['status']; // Get the status from the form
        $description6905 = $_POST['description']; // Get the description from the form
        $room6905 = $_POST['room']; // Get the room from the form
        $assignedBy6905 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6905 = $status6905 === 'Need Repair' ? '' : $assignedName6905;

        // Prepare SQL query to update the asset
        $sql6905 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6905 = $conn->prepare($sql6905);
        $stmt6905->bind_param('sssssi', $status6905, $assignedName6905, $assignedBy6905, $description6905, $room6905, $assetId6905);

        if ($stmt6905->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6905 to $status6905.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6905->close();
    }
    //FOR ID 6904
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6904'])) {
        // Get form data
        $assetId6904 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6904 = $_POST['status']; // Get the status from the form
        $description6904 = $_POST['description']; // Get the description from the form
        $room6904 = $_POST['room']; // Get the room from the form
        $assignedBy6904 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6904 = $status6904 === 'Need Repair' ? '' : $assignedName6904;

        // Prepare SQL query to update the asset
        $sql6904 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6904 = $conn->prepare($sql6904);
        $stmt6904->bind_param('sssssi', $status6904, $assignedName6904, $assignedBy6904, $description6904, $room6904, $assetId6904);

        if ($stmt6904->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6904 to $status6904.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6904->close();
    }
    //FOR ID 6903
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6903'])) {
        // Get form data
        $assetId6903 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6903 = $_POST['status']; // Get the status from the form
        $description6903 = $_POST['description']; // Get the description from the form
        $room6903 = $_POST['room']; // Get the room from the form
        $assignedBy6903 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6903 = $status6903 === 'Need Repair' ? '' : $assignedName6903;

        // Prepare SQL query to update the asset
        $sql6903 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6903 = $conn->prepare($sql6903);
        $stmt6903->bind_param('sssssi', $status6903, $assignedName6903, $assignedBy6903, $description6903, $room6903, $assetId6903);

        if ($stmt6903->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6903 to $status6903.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6903->close();
    }
    //FOR ID 6902
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6902'])) {
        // Get form data
        $assetId6902 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6902 = $_POST['status']; // Get the status from the form
        $description6902 = $_POST['description']; // Get the description from the form
        $room6902 = $_POST['room']; // Get the room from the form
        $assignedBy6902 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6902 = $status6902 === 'Need Repair' ? '' : $assignedName6902;

        // Prepare SQL query to update the asset
        $sql6902 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6902 = $conn->prepare($sql6902);
        $stmt6902->bind_param('sssssi', $status6902, $assignedName6902, $assignedBy6902, $description6902, $room6902, $assetId6902);

        if ($stmt6902->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6902 to $status6902.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6902->close();
    }
    //FOR ID 6903
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6903'])) {
        // Get form data
        $assetId6903 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6903 = $_POST['status']; // Get the status from the form
        $description6903 = $_POST['description']; // Get the description from the form
        $room6903 = $_POST['room']; // Get the room from the form
        $assignedBy6903 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6903 = $status6903 === 'Need Repair' ? '' : $assignedName6903;

        // Prepare SQL query to update the asset
        $sql6903 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6903 = $conn->prepare($sql6903);
        $stmt6903->bind_param('sssssi', $status6903, $assignedName6903, $assignedBy6903, $description6903, $room6903, $assetId6903);

        if ($stmt6903->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6903 to $status6903.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6903->close();
    }
    //FOR ID 6902
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6902'])) {
        // Get form data
        $assetId6902 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6902 = $_POST['status']; // Get the status from the form
        $description6902 = $_POST['description']; // Get the description from the form
        $room6902 = $_POST['room']; // Get the room from the form
        $assignedBy6902 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6902 = $status6902 === 'Need Repair' ? '' : $assignedName6902;

        // Prepare SQL query to update the asset
        $sql6902 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6902 = $conn->prepare($sql6902);
        $stmt6902->bind_param('sssssi', $status6902, $assignedName6902, $assignedBy6902, $description6902, $room6902, $assetId6902);

        if ($stmt6902->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6902 to $status6902.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6902->close();
    }
    //FOR ID 6901
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6901'])) {
        // Get form data
        $assetId6901 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6901 = $_POST['status']; // Get the status from the form
        $description6901 = $_POST['description']; // Get the description from the form
        $room6901 = $_POST['room']; // Get the room from the form
        $assignedBy6901 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6901 = $status6901 === 'Need Repair' ? '' : $assignedName6901;

        // Prepare SQL query to update the asset
        $sql6901 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6901 = $conn->prepare($sql6901);
        $stmt6901->bind_param('sssssi', $status6901, $assignedName6901, $assignedBy6901, $description6901, $room6901, $assetId6901);

        if ($stmt6901->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6901 to $status6901.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6901->close();
    }
    //FOR ID 6900
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6900'])) {
        // Get form data
        $assetId6900 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6900 = $_POST['status']; // Get the status from the form
        $description6900 = $_POST['description']; // Get the description from the form
        $room6900 = $_POST['room']; // Get the room from the form
        $assignedBy6900 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6900 = $status6900 === 'Need Repair' ? '' : $assignedName6900;

        // Prepare SQL query to update the asset
        $sql6900 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6900 = $conn->prepare($sql6900);
        $stmt6900->bind_param('sssssi', $status6900, $assignedName6900, $assignedBy6900, $description6900, $room6900, $assetId6900);

        if ($stmt6900->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6900 to $status6900.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6900->close();
    }

    //FOR ID 6899
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6899'])) {
        // Get form data
        $assetId6899 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6899 = $_POST['status']; // Get the status from the form
        $description6899 = $_POST['description']; // Get the description from the form
        $room6899 = $_POST['room']; // Get the room from the form
        $assignedBy6899 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6899 = $status6899 === 'Need Repair' ? '' : $assignedName6899;

        // Prepare SQL query to update the asset
        $sql6899 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6899 = $conn->prepare($sql6899);
        $stmt6899->bind_param('sssssi', $status6899, $assignedName6899, $assignedBy6899, $description6899, $room6899, $assetId6899);

        if ($stmt6899->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6899 to $status6899.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6899->close();
    }
    //FOR ID 6898
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6898'])) {
        // Get form data
        $assetId6898 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6898 = $_POST['status']; // Get the status from the form
        $description6898 = $_POST['description']; // Get the description from the form
        $room6898 = $_POST['room']; // Get the room from the form
        $assignedBy6898 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6898 = $status6898 === 'Need Repair' ? '' : $assignedName6898;

        // Prepare SQL query to update the asset
        $sql6898 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6898 = $conn->prepare($sql6898);
        $stmt6898->bind_param('sssssi', $status6898, $assignedName6898, $assignedBy6898, $description6898, $room6898, $assetId6898);

        if ($stmt6898->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6898 to $status6898.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6898->close();
    }
    //FOR ID 6897
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6897'])) {
        // Get form data
        $assetId6897 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6897 = $_POST['status']; // Get the status from the form
        $description6897 = $_POST['description']; // Get the description from the form
        $room6897 = $_POST['room']; // Get the room from the form
        $assignedBy6897 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6897 = $status6897 === 'Need Repair' ? '' : $assignedName6897;

        // Prepare SQL query to update the asset
        $sql6897 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6897 = $conn->prepare($sql6897);
        $stmt6897->bind_param('sssssi', $status6897, $assignedName6897, $assignedBy6897, $description6897, $room6897, $assetId6897);

        if ($stmt6897->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6897 to $status6897.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6897->close();
    }
    //FOR ID 6896
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6896'])) {
        // Get form data
        $assetId6896 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6896 = $_POST['status']; // Get the status from the form
        $description6896 = $_POST['description']; // Get the description from the form
        $room6896 = $_POST['room']; // Get the room from the form
        $assignedBy6896 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6896 = $status6896 === 'Need Repair' ? '' : $assignedName6896;

        // Prepare SQL query to update the asset
        $sql6896 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6896 = $conn->prepare($sql6896);
        $stmt6896->bind_param('sssssi', $status6896, $assignedName6896, $assignedBy6896, $description6896, $room6896, $assetId6896);

        if ($stmt6896->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6896 to $status6896.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6896->close();
    }
    //FOR ID 6895
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6895'])) {
        // Get form data
        $assetId6895 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6895 = $_POST['status']; // Get the status from the form
        $description6895 = $_POST['description']; // Get the description from the form
        $room6895 = $_POST['room']; // Get the room from the form
        $assignedBy6895 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6895 = $status6895 === 'Need Repair' ? '' : $assignedName6895;

        // Prepare SQL query to update the asset
        $sql6895 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6895 = $conn->prepare($sql6895);
        $stmt6895->bind_param('sssssi', $status6895, $assignedName6895, $assignedBy6895, $description6895, $room6895, $assetId6895);

        if ($stmt6895->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6895 to $status6895.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6895->close();
    }
    //FOR ID 6894
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6894'])) {
        // Get form data
        $assetId6894 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6894 = $_POST['status']; // Get the status from the form
        $description6894 = $_POST['description']; // Get the description from the form
        $room6894 = $_POST['room']; // Get the room from the form
        $assignedBy6894 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6894 = $status6894 === 'Need Repair' ? '' : $assignedName6894;

        // Prepare SQL query to update the asset
        $sql6894 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6894 = $conn->prepare($sql6894);
        $stmt6894->bind_param('sssssi', $status6894, $assignedName6894, $assignedBy6894, $description6894, $room6894, $assetId6894);

        if ($stmt6894->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6894 to $status6894.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6894->close();
    }
    //FOR ID 6893
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6893'])) {
        // Get form data
        $assetId6893 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6893 = $_POST['status']; // Get the status from the form
        $description6893 = $_POST['description']; // Get the description from the form
        $room6893 = $_POST['room']; // Get the room from the form
        $assignedBy6893 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6893 = $status6893 === 'Need Repair' ? '' : $assignedName6893;

        // Prepare SQL query to update the asset
        $sql6893 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6893 = $conn->prepare($sql6893);
        $stmt6893->bind_param('sssssi', $status6893, $assignedName6893, $assignedBy6893, $description6893, $room6893, $assetId6893);

        if ($stmt6893->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6893 to $status6893.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6893->close();
    }
    //FOR ID 6892
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6892'])) {
        // Get form data
        $assetId6892 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6892 = $_POST['status']; // Get the status from the form
        $description6892 = $_POST['description']; // Get the description from the form
        $room6892 = $_POST['room']; // Get the room from the form
        $assignedBy6892 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6892 = $status6892 === 'Need Repair' ? '' : $assignedName6892;

        // Prepare SQL query to update the asset
        $sql6892 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6892 = $conn->prepare($sql6892);
        $stmt6892->bind_param('sssssi', $status6892, $assignedName6892, $assignedBy6892, $description6892, $room6892, $assetId6892);

        if ($stmt6892->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6892 to $status6892.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6892->close();
    }

    //FOR ID 6891
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6891'])) {
        // Get form data
        $assetId6891 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6891 = $_POST['status']; // Get the status from the form
        $description6891 = $_POST['description']; // Get the description from the form
        $room6891 = $_POST['room']; // Get the room from the form
        $assignedBy6891 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6891 = $status6891 === 'Need Repair' ? '' : $assignedName6891;

        // Prepare SQL query to update the asset
        $sql6891 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6891 = $conn->prepare($sql6891);
        $stmt6891->bind_param('sssssi', $status6891, $assignedName6891, $assignedBy6891, $description6891, $room6891, $assetId6891);

        if ($stmt6891->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6891 to $status6891.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6891->close();
    }
    //FOR ID 6890
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6890'])) {
        // Get form data
        $assetId6890 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6890 = $_POST['status']; // Get the status from the form
        $description6890 = $_POST['description']; // Get the description from the form
        $room6890 = $_POST['room']; // Get the room from the form
        $assignedBy6890 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6890 = $status6890 === 'Need Repair' ? '' : $assignedName6890;

        // Prepare SQL query to update the asset
        $sql6890 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6890 = $conn->prepare($sql6890);
        $stmt6890->bind_param('sssssi', $status6890, $assignedName6890, $assignedBy6890, $description6890, $room6890, $assetId6890);

        if ($stmt6890->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6890 to $status6890.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6890->close();
    }
    //FOR ID 6889
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6889'])) {
        // Get form data
        $assetId6889 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6889 = $_POST['status']; // Get the status from the form
        $description6889 = $_POST['description']; // Get the description from the form
        $room6889 = $_POST['room']; // Get the room from the form
        $assignedBy6889 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6889 = $status6889 === 'Need Repair' ? '' : $assignedName6889;

        // Prepare SQL query to update the asset
        $sql6889 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6889 = $conn->prepare($sql6889);
        $stmt6889->bind_param('sssssi', $status6889, $assignedName6889, $assignedBy6889, $description6889, $room6889, $assetId6889);

        if ($stmt6889->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6889 to $status6889.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6889->close();
    }
    //FOR ID 6888
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6888'])) {
        // Get form data
        $assetId6888 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6888 = $_POST['status']; // Get the status from the form
        $description6888 = $_POST['description']; // Get the description from the form
        $room6888 = $_POST['room']; // Get the room from the form
        $assignedBy6888 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6888 = $status6888 === 'Need Repair' ? '' : $assignedName6888;

        // Prepare SQL query to update the asset
        $sql6888 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6888 = $conn->prepare($sql6888);
        $stmt6888->bind_param('sssssi', $status6888, $assignedName6888, $assignedBy6888, $description6888, $room6888, $assetId6888);

        if ($stmt6888->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6888 to $status6888.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6888->close();
    }
    //FOR ID 6887
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6887'])) {
        // Get form data
        $assetId6887 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6887 = $_POST['status']; // Get the status from the form
        $description6887 = $_POST['description']; // Get the description from the form
        $room6887 = $_POST['room']; // Get the room from the form
        $assignedBy6887 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6887 = $status6887 === 'Need Repair' ? '' : $assignedName6887;

        // Prepare SQL query to update the asset
        $sql6887 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6887 = $conn->prepare($sql6887);
        $stmt6887->bind_param('sssssi', $status6887, $assignedName6887, $assignedBy6887, $description6887, $room6887, $assetId6887);

        if ($stmt6887->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6887 to $status6887.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6887->close();
    }
    //FOR ID 6886
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6886'])) {
        // Get form data
        $assetId6886 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6886 = $_POST['status']; // Get the status from the form
        $description6886 = $_POST['description']; // Get the description from the form
        $room6886 = $_POST['room']; // Get the room from the form
        $assignedBy6886 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6886 = $status6886 === 'Need Repair' ? '' : $assignedName6886;

        // Prepare SQL query to update the asset
        $sql6886 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6886 = $conn->prepare($sql6886);
        $stmt6886->bind_param('sssssi', $status6886, $assignedName6886, $assignedBy6886, $description6886, $room6886, $assetId6886);

        if ($stmt6886->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6886 to $status6886.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6886->close();
    }
    //FOR ID 6885
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6885'])) {
        // Get form data
        $assetId6885 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6885 = $_POST['status']; // Get the status from the form
        $description6885 = $_POST['description']; // Get the description from the form
        $room6885 = $_POST['room']; // Get the room from the form
        $assignedBy6885 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6885 = $status6885 === 'Need Repair' ? '' : $assignedName6885;

        // Prepare SQL query to update the asset
        $sql6885 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6885 = $conn->prepare($sql6885);
        $stmt6885->bind_param('sssssi', $status6885, $assignedName6885, $assignedBy6885, $description6885, $room6885, $assetId6885);

        if ($stmt6885->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6885 to $status6885.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6885->close();
    }
    //FOR ID 6884
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6884'])) {
        // Get form data
        $assetId6884 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6884 = $_POST['status']; // Get the status from the form
        $description6884 = $_POST['description']; // Get the description from the form
        $room6884 = $_POST['room']; // Get the room from the form
        $assignedBy6884 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6884 = $status6884 === 'Need Repair' ? '' : $assignedName6884;

        // Prepare SQL query to update the asset
        $sql6884 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6884 = $conn->prepare($sql6884);
        $stmt6884->bind_param('sssssi', $status6884, $assignedName6884, $assignedBy6884, $description6884, $room6884, $assetId6884);

        if ($stmt6884->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6884 to $status6884.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6884->close();
    }
    //FOR ID 6883
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6883'])) {
        // Get form data
        $assetId6883 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6883 = $_POST['status']; // Get the status from the form
        $description6883 = $_POST['description']; // Get the description from the form
        $room6883 = $_POST['room']; // Get the room from the form
        $assignedBy6883 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6883 = $status6883 === 'Need Repair' ? '' : $assignedName6883;

        // Prepare SQL query to update the asset
        $sql6883 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6883 = $conn->prepare($sql6883);
        $stmt6883->bind_param('sssssi', $status6883, $assignedName6883, $assignedBy6883, $description6883, $room6883, $assetId6883);

        if ($stmt6883->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6883 to $status6883.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6883->close();
    }
    //FOR ID 6882
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6882'])) {
        // Get form data
        $assetId6882 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6882 = $_POST['status']; // Get the status from the form
        $description6882 = $_POST['description']; // Get the description from the form
        $room6882 = $_POST['room']; // Get the room from the form
        $assignedBy6882 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6882 = $status6882 === 'Need Repair' ? '' : $assignedName6882;

        // Prepare SQL query to update the asset
        $sql6882 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6882 = $conn->prepare($sql6882);
        $stmt6882->bind_param('sssssi', $status6882, $assignedName6882, $assignedBy6882, $description6882, $room6882, $assetId6882);

        if ($stmt6882->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6882 to $status6882.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6882->close();
    }
    //FOR ID 6881
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6881'])) {
        // Get form data
        $assetId6881 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6881 = $_POST['status']; // Get the status from the form
        $description6881 = $_POST['description']; // Get the description from the form
        $room6881 = $_POST['room']; // Get the room from the form
        $assignedBy6881 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6881 = $status6881 === 'Need Repair' ? '' : $assignedName6881;

        // Prepare SQL query to update the asset
        $sql6881 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6881 = $conn->prepare($sql6881);
        $stmt6881->bind_param('sssssi', $status6881, $assignedName6881, $assignedBy6881, $description6881, $room6881, $assetId6881);

        if ($stmt6881->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6881 to $status6881.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6881->close();
    }
    //FOR ID 6880
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6880'])) {
        // Get form data
        $assetId6880 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6880 = $_POST['status']; // Get the status from the form
        $description6880 = $_POST['description']; // Get the description from the form
        $room6880 = $_POST['room']; // Get the room from the form
        $assignedBy6880 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6880 = $status6880 === 'Need Repair' ? '' : $assignedName6880;

        // Prepare SQL query to update the asset
        $sql6880 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6880 = $conn->prepare($sql6880);
        $stmt6880->bind_param('sssssi', $status6880, $assignedName6880, $assignedBy6880, $description6880, $room6880, $assetId6880);

        if ($stmt6880->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6880 to $status6880.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6880->close();
    }
    //FOR ID 6879
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6879'])) {
        // Get form data
        $assetId6879 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6879 = $_POST['status']; // Get the status from the form
        $description6879 = $_POST['description']; // Get the description from the form
        $room6879 = $_POST['room']; // Get the room from the form
        $assignedBy6879 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6879 = $status6879 === 'Need Repair' ? '' : $assignedName6879;

        // Prepare SQL query to update the asset
        $sql6879 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6879 = $conn->prepare($sql6879);
        $stmt6879->bind_param('sssssi', $status6879, $assignedName6879, $assignedBy6879, $description6879, $room6879, $assetId6879);

        if ($stmt6879->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6879 to $status6879.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6879->close();
    }
    //FOR ID 6878
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6878'])) {
        // Get form data
        $assetId6878 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6878 = $_POST['status']; // Get the status from the form
        $description6878 = $_POST['description']; // Get the description from the form
        $room6878 = $_POST['room']; // Get the room from the form
        $assignedBy6878 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6878 = $status6878 === 'Need Repair' ? '' : $assignedName6878;

        // Prepare SQL query to update the asset
        $sql6878 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6878 = $conn->prepare($sql6878);
        $stmt6878->bind_param('sssssi', $status6878, $assignedName6878, $assignedBy6878, $description6878, $room6878, $assetId6878);

        if ($stmt6878->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6878 to $status6878.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6878->close();
    }
    //FOR ID 6877
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6877'])) {
        // Get form data
        $assetId6877 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6877 = $_POST['status']; // Get the status from the form
        $description6877 = $_POST['description']; // Get the description from the form
        $room6877 = $_POST['room']; // Get the room from the form
        $assignedBy6877 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6877 = $status6877 === 'Need Repair' ? '' : $assignedName6877;

        // Prepare SQL query to update the asset
        $sql6877 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6877 = $conn->prepare($sql6877);
        $stmt6877->bind_param('sssssi', $status6877, $assignedName6877, $assignedBy6877, $description6877, $room6877, $assetId6877);

        if ($stmt6877->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6877 to $status6877.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6877->close();
    }
    //FOR ID 6876
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6876'])) {
        // Get form data
        $assetId6876 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6876 = $_POST['status']; // Get the status from the form
        $description6876 = $_POST['description']; // Get the description from the form
        $room6876 = $_POST['room']; // Get the room from the form
        $assignedBy6876 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6876 = $status6876 === 'Need Repair' ? '' : $assignedName6876;

        // Prepare SQL query to update the asset
        $sql6876 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6876 = $conn->prepare($sql6876);
        $stmt6876->bind_param('sssssi', $status6876, $assignedName6876, $assignedBy6876, $description6876, $room6876, $assetId6876);

        if ($stmt6876->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6876 to $status6876.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6876->close();
    }
    //FOR ID 6875
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6875'])) {
        // Get form data
        $assetId6875 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6875 = $_POST['status']; // Get the status from the form
        $description6875 = $_POST['description']; // Get the description from the form
        $room6875 = $_POST['room']; // Get the room from the form
        $assignedBy6875 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6875 = $status6875 === 'Need Repair' ? '' : $assignedName6875;

        // Prepare SQL query to update the asset
        $sql6875 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6875 = $conn->prepare($sql6875);
        $stmt6875->bind_param('sssssi', $status6875, $assignedName6875, $assignedBy6875, $description6875, $room6875, $assetId6875);

        if ($stmt6875->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6875 to $status6875.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6875->close();
    }

    //FOR ID 6874
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6874'])) {
        // Get form data
        $assetId6874 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6874 = $_POST['status']; // Get the status from the form
        $description6874 = $_POST['description']; // Get the description from the form
        $room6874 = $_POST['room']; // Get the room from the form
        $assignedBy6874 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6874 = $status6874 === 'Need Repair' ? '' : $assignedName6874;

        // Prepare SQL query to update the asset
        $sql6874 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6874 = $conn->prepare($sql6874);
        $stmt6874->bind_param('sssssi', $status6874, $assignedName6874, $assignedBy6874, $description6874, $room6874, $assetId6874);

        if ($stmt6874->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6874 to $status6874.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6874->close();
    }
    //FOR ID 6873
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6873'])) {
        // Get form data
        $assetId6873 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6873 = $_POST['status']; // Get the status from the form
        $description6873 = $_POST['description']; // Get the description from the form
        $room6873 = $_POST['room']; // Get the room from the form
        $assignedBy6873 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6873 = $status6873 === 'Need Repair' ? '' : $assignedName6873;

        // Prepare SQL query to update the asset
        $sql6873 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6873 = $conn->prepare($sql6873);
        $stmt6873->bind_param('sssssi', $status6873, $assignedName6873, $assignedBy6873, $description6873, $room6873, $assetId6873);

        if ($stmt6873->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6873 to $status6873.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6873->close();
    }
    //FOR ID 6872
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6872'])) {
        // Get form data
        $assetId6872 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6872 = $_POST['status']; // Get the status from the form
        $description6872 = $_POST['description']; // Get the description from the form
        $room6872 = $_POST['room']; // Get the room from the form
        $assignedBy6872 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6872 = $status6872 === 'Need Repair' ? '' : $assignedName6872;

        // Prepare SQL query to update the asset
        $sql6872 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6872 = $conn->prepare($sql6872);
        $stmt6872->bind_param('sssssi', $status6872, $assignedName6872, $assignedBy6872, $description6872, $room6872, $assetId6872);

        if ($stmt6872->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6872 to $status6872.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6872->close();
    }
    //FOR ID 6871
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6871'])) {
        // Get form data
        $assetId6871 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6871 = $_POST['status']; // Get the status from the form
        $description6871 = $_POST['description']; // Get the description from the form
        $room6871 = $_POST['room']; // Get the room from the form
        $assignedBy6871 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6871 = $status6871 === 'Need Repair' ? '' : $assignedName6871;

        // Prepare SQL query to update the asset
        $sql6871 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6871 = $conn->prepare($sql6871);
        $stmt6871->bind_param('sssssi', $status6871, $assignedName6871, $assignedBy6871, $description6871, $room6871, $assetId6871);

        if ($stmt6871->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6871 to $status6871.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6871->close();
    }
    //FOR ID 6870
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6870'])) {
        // Get form data
        $assetId6870 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6870 = $_POST['status']; // Get the status from the form
        $description6870 = $_POST['description']; // Get the description from the form
        $room6870 = $_POST['room']; // Get the room from the form
        $assignedBy6870 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6870 = $status6870 === 'Need Repair' ? '' : $assignedName6870;

        // Prepare SQL query to update the asset
        $sql6870 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6870 = $conn->prepare($sql6870);
        $stmt6870->bind_param('sssssi', $status6870, $assignedName6870, $assignedBy6870, $description6870, $room6870, $assetId6870);

        if ($stmt6870->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6870 to $status6870.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6870->close();
    }

    //FOR ID 6869
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6869'])) {
        // Get form data
        $assetId6869 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6869 = $_POST['status']; // Get the status from the form
        $description6869 = $_POST['description']; // Get the description from the form
        $room6869 = $_POST['room']; // Get the room from the form
        $assignedBy6869 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6869 = $status6869 === 'Need Repair' ? '' : $assignedName6869;

        // Prepare SQL query to update the asset
        $sql6869 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6869 = $conn->prepare($sql6869);
        $stmt6869->bind_param('sssssi', $status6869, $assignedName6869, $assignedBy6869, $description6869, $room6869, $assetId6869);

        if ($stmt6869->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6869 to $status6869.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6869->close();
    }
    //FOR ID 6868
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6868'])) {
        // Get form data
        $assetId6868 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6868 = $_POST['status']; // Get the status from the form
        $description6868 = $_POST['description']; // Get the description from the form
        $room6868 = $_POST['room']; // Get the room from the form
        $assignedBy6868 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6868 = $status6868 === 'Need Repair' ? '' : $assignedName6868;

        // Prepare SQL query to update the asset
        $sql6868 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6868 = $conn->prepare($sql6868);
        $stmt6868->bind_param('sssssi', $status6868, $assignedName6868, $assignedBy6868, $description6868, $room6868, $assetId6868);

        if ($stmt6868->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6868 to $status6868.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6868->close();
    }
    //FOR ID 6867
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6867'])) {
        // Get form data
        $assetId6867 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6867 = $_POST['status']; // Get the status from the form
        $description6867 = $_POST['description']; // Get the description from the form
        $room6867 = $_POST['room']; // Get the room from the form
        $assignedBy6867 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6867 = $status6867 === 'Need Repair' ? '' : $assignedName6867;

        // Prepare SQL query to update the asset
        $sql6867 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6867 = $conn->prepare($sql6867);
        $stmt6867->bind_param('sssssi', $status6867, $assignedName6867, $assignedBy6867, $description6867, $room6867, $assetId6867);

        if ($stmt6867->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6867 to $status6867.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6867->close();
    }
    //FOR ID 7269
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7269'])) {
        // Get form data
        $assetId7269 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7269 = $_POST['status']; // Get the status from the form
        $description7269 = $_POST['description']; // Get the description from the form
        $room7269 = $_POST['room']; // Get the room from the form
        $assignedBy7269 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7269 = $status7269 === 'Need Repair' ? '' : $assignedName7269;

        // Prepare SQL query to update the asset
        $sql7269 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7269 = $conn->prepare($sql7269);
        $stmt7269->bind_param('sssssi', $status7269, $assignedName7269, $assignedBy7269, $description7269, $room7269, $assetId7269);

        if ($stmt7269->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7269 to $status7269.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7269->close();
    }

    //FOR ID 7268
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7268'])) {
        // Get form data
        $assetId7268 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7268 = $_POST['status']; // Get the status from the form
        $description7268 = $_POST['description']; // Get the description from the form
        $room7268 = $_POST['room']; // Get the room from the form
        $assignedBy7268 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7268 = $status7268 === 'Need Repair' ? '' : $assignedName7268;

        // Prepare SQL query to update the asset
        $sql7268 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7268 = $conn->prepare($sql7268);
        $stmt7268->bind_param('sssssi', $status7268, $assignedName7268, $assignedBy7268, $description7268, $room7268, $assetId7268);

        if ($stmt7268->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7268 to $status7268.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7268->close();
    }
    //FOR ID 7267
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7267'])) {
        // Get form data
        $assetId7267 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7267 = $_POST['status']; // Get the status from the form
        $description7267 = $_POST['description']; // Get the description from the form
        $room7267 = $_POST['room']; // Get the room from the form
        $assignedBy7267 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7267 = $status7267 === 'Need Repair' ? '' : $assignedName7267;

        // Prepare SQL query to update the asset
        $sql7267 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7267 = $conn->prepare($sql7267);
        $stmt7267->bind_param('sssssi', $status7267, $assignedName7267, $assignedBy7267, $description7267, $room7267, $assetId7267);

        if ($stmt7267->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7267 to $status7267.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7267->close();
    }
    //FOR ID 6948
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6948'])) {
        // Get form data
        $assetId6948 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6948 = $_POST['status']; // Get the status from the form
        $description6948 = $_POST['description']; // Get the description from the form
        $room6948 = $_POST['room']; // Get the room from the form
        $assignedBy6948 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6948 = $status6948 === 'Need Repair' ? '' : $assignedName6948;

        // Prepare SQL query to update the asset
        $sql6948 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6948 = $conn->prepare($sql6948);
        $stmt6948->bind_param('sssssi', $status6948, $assignedName6948, $assignedBy6948, $description6948, $room6948, $assetId6948);

        if ($stmt6948->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6948 to $status6948.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6948->close();
    }
    //FOR ID 6949
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6949'])) {
        // Get form data
        $assetId6949 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6949 = $_POST['status']; // Get the status from the form
        $description6949 = $_POST['description']; // Get the description from the form
        $room6949 = $_POST['room']; // Get the room from the form
        $assignedBy6949 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6949 = $status6949 === 'Need Repair' ? '' : $assignedName6949;

        // Prepare SQL query to update the asset
        $sql6949 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6949 = $conn->prepare($sql6949);
        $stmt6949->bind_param('sssssi', $status6949, $assignedName6949, $assignedBy6949, $description6949, $room6949, $assetId6949);

        if ($stmt6949->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6949 to $status6949.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6949->close();
    }
    //FOR ID 6950
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6950'])) {
        // Get form data
        $assetId6950 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6950 = $_POST['status']; // Get the status from the form
        $description6950 = $_POST['description']; // Get the description from the form
        $room6950 = $_POST['room']; // Get the room from the form
        $assignedBy6950 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6950 = $status6950 === 'Need Repair' ? '' : $assignedName6950;

        // Prepare SQL query to update the asset
        $sql6950 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6950 = $conn->prepare($sql6950);
        $stmt6950->bind_param('sssssi', $status6950, $assignedName6950, $assignedBy6950, $description6950, $room6950, $assetId6950);

        if ($stmt6950->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6950 to $status6950.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6950->close();
    }

    //FOR ID 6951
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6951'])) {
        // Get form data
        $assetId6951 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6951 = $_POST['status']; // Get the status from the form
        $description6951 = $_POST['description']; // Get the description from the form
        $room6951 = $_POST['room']; // Get the room from the form
        $assignedBy6951 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6951 = $status6951 === 'Need Repair' ? '' : $assignedName6951;

        // Prepare SQL query to update the asset
        $sql6951 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6951 = $conn->prepare($sql6951);
        $stmt6951->bind_param('sssssi', $status6951, $assignedName6951, $assignedBy6951, $description6951, $room6951, $assetId6951);

        if ($stmt6951->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6951 to $status6951.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6951->close();
    }

    //FOR ID 6952
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6952'])) {
        // Get form data
        $assetId6952 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6952 = $_POST['status']; // Get the status from the form
        $description6952 = $_POST['description']; // Get the description from the form
        $room6952 = $_POST['room']; // Get the room from the form
        $assignedBy6952 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6952 = $status6952 === 'Need Repair' ? '' : $assignedName6952;

        // Prepare SQL query to update the asset
        $sql6952 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6952 = $conn->prepare($sql6952);
        $stmt6952->bind_param('sssssi', $status6952, $assignedName6952, $assignedBy6952, $description6952, $room6952, $assetId6952);

        if ($stmt6952->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6952 to $status6952.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6952->close();
    }

    //FOR ID 6953
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6953'])) {
        // Get form data
        $assetId6953 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6953 = $_POST['status']; // Get the status from the form
        $description6953 = $_POST['description']; // Get the description from the form
        $room6953 = $_POST['room']; // Get the room from the form
        $assignedBy6953 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6953 = $status6953 === 'Need Repair' ? '' : $assignedName6953;

        // Prepare SQL query to update the asset
        $sql6953 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6953 = $conn->prepare($sql6953);
        $stmt6953->bind_param('sssssi', $status6953, $assignedName6953, $assignedBy6953, $description6953, $room6953, $assetId6953);

        if ($stmt6953->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6953 to $status6953.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6953->close();
    }

    //FOR ID 6954
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6954'])) {
        // Get form data
        $assetId6954 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6954 = $_POST['status']; // Get the status from the form
        $description6954 = $_POST['description']; // Get the description from the form
        $room6954 = $_POST['room']; // Get the room from the form
        $assignedBy6954 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6954 = $status6954 === 'Need Repair' ? '' : $assignedName6954;

        // Prepare SQL query to update the asset
        $sql6954 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6954 = $conn->prepare($sql6954);
        $stmt6954->bind_param('sssssi', $status6954, $assignedName6954, $assignedBy6954, $description6954, $room6954, $assetId6954);

        if ($stmt6954->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6954 to $status6954.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6954->close();
    }

    //FOR ID 6955
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6955'])) {
        // Get form data
        $assetId6955 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6955 = $_POST['status']; // Get the status from the form
        $description6955 = $_POST['description']; // Get the description from the form
        $room6955 = $_POST['room']; // Get the room from the form
        $assignedBy6955 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6955 = $status6955 === 'Need Repair' ? '' : $assignedName6955;

        // Prepare SQL query to update the asset
        $sql6955 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6955 = $conn->prepare($sql6955);
        $stmt6955->bind_param('sssssi', $status6955, $assignedName6955, $assignedBy6955, $description6955, $room6955, $assetId6955);

        if ($stmt6955->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6955 to $status6955.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6955->close();
    }

    //FOR ID 6956
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6956'])) {
        // Get form data
        $assetId6956 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6956 = $_POST['status']; // Get the status from the form
        $description6956 = $_POST['description']; // Get the description from the form
        $room6956 = $_POST['room']; // Get the room from the form
        $assignedBy6956 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6956 = $status6956 === 'Need Repair' ? '' : $assignedName6956;

        // Prepare SQL query to update the asset
        $sql6956 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6956 = $conn->prepare($sql6956);
        $stmt6956->bind_param('sssssi', $status6956, $assignedName6956, $assignedBy6956, $description6956, $room6956, $assetId6956);

        if ($stmt6956->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6956 to $status6956.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6956->close();
    }

    //FOR ID 6957
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6957'])) {
        // Get form data
        $assetId6957 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6957 = $_POST['status']; // Get the status from the form
        $description6957 = $_POST['description']; // Get the description from the form
        $room6957 = $_POST['room']; // Get the room from the form
        $assignedBy6957 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6957 = $status6957 === 'Need Repair' ? '' : $assignedName6957;

        // Prepare SQL query to update the asset
        $sql6957 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6957 = $conn->prepare($sql6957);
        $stmt6957->bind_param('sssssi', $status6957, $assignedName6957, $assignedBy6957, $description6957, $room6957, $assetId6957);

        if ($stmt6957->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6957 to $status6957.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6957->close();
    }

    //FOR ID 6958
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6958'])) {
        // Get form data
        $assetId6958 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6958 = $_POST['status']; // Get the status from the form
        $description6958 = $_POST['description']; // Get the description from the form
        $room6958 = $_POST['room']; // Get the room from the form
        $assignedBy6958 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6958 = $status6958 === 'Need Repair' ? '' : $assignedName6958;

        // Prepare SQL query to update the asset
        $sql6958 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6958 = $conn->prepare($sql6958);
        $stmt6958->bind_param('sssssi', $status6958, $assignedName6958, $assignedBy6958, $description6958, $room6958, $assetId6958);

        if ($stmt6958->execute()) {
            // Update success
            // logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6958 to $status6958.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6958->close();
    }

    //FOR ID 6959
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6959'])) {
        // Get form data
        $assetId6959 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6959 = $_POST['status']; // Get the status from the form
        $description6959 = $_POST['description']; // Get the description from the form
        $room6959 = $_POST['room']; // Get the room from the form
        $assignedBy6959 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6959 = $status6959 === 'Need Repair' ? '' : $assignedName6959;

        // Prepare SQL query to update the asset
        $sql6959 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6959 = $conn->prepare($sql6959);
        $stmt6959->bind_param('sssssi', $status6959, $assignedName6959, $assignedBy6959, $description6959, $room6959, $assetId6959);

        if ($stmt6959->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6959 to $status6959.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6959->close();
    }

    //FOR ID 6960
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6960'])) {
        // Get form data
        $assetId6960 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6960 = $_POST['status']; // Get the status from the form
        $description6960 = $_POST['description']; // Get the description from the form
        $room6960 = $_POST['room']; // Get the room from the form
        $assignedBy6960 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6960 = $status6960 === 'Need Repair' ? '' : $assignedName6960;

        // Prepare SQL query to update the asset
        $sql6960 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6960 = $conn->prepare($sql6960);
        $stmt6960->bind_param('sssssi', $status6960, $assignedName6960, $assignedBy6960, $description6960, $room6960, $assetId6960);

        if ($stmt6960->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6960 to $status6960.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6960->close();
    }

    //FOR ID 6961
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6961'])) {
        // Get form data
        $assetId6961 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6961 = $_POST['status']; // Get the status from the form
        $description6961 = $_POST['description']; // Get the description from the form
        $room6961 = $_POST['room']; // Get the room from the form
        $assignedBy6961 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6961 = $status6961 === 'Need Repair' ? '' : $assignedName6961;

        // Prepare SQL query to update the asset
        $sql6961 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6961 = $conn->prepare($sql6961);
        $stmt6961->bind_param('sssssi', $status6961, $assignedName6961, $assignedBy6961, $description6961, $room6961, $assetId6961);

        if ($stmt6961->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6961 to $status6961.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6961->close();
    }

    //FOR ID 6962
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6962'])) {
        // Get form data
        $assetId6962 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6962 = $_POST['status']; // Get the status from the form
        $description6962 = $_POST['description']; // Get the description from the form
        $room6962 = $_POST['room']; // Get the room from the form
        $assignedBy6962 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6962 = $status6962 === 'Need Repair' ? '' : $assignedName6962;

        // Prepare SQL query to update the asset
        $sql6962 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6962 = $conn->prepare($sql6962);
        $stmt6962->bind_param('sssssi', $status6962, $assignedName6962, $assignedBy6962, $description6962, $room6962, $assetId6962);

        if ($stmt6962->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6962 to $status6962.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6962->close();
    }

    //FOR ID 6963
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6963'])) {
        // Get form data
        $assetId6963 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6963 = $_POST['status']; // Get the status from the form
        $description6963 = $_POST['description']; // Get the description from the form
        $room6963 = $_POST['room']; // Get the room from the form
        $assignedBy6963 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6963 = $status6963 === 'Need Repair' ? '' : $assignedName6963;

        // Prepare SQL query to update the asset
        $sql6963 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6963 = $conn->prepare($sql6963);
        $stmt6963->bind_param('sssssi', $status6963, $assignedName6963, $assignedBy6963, $description6963, $room6963, $assetId6963);

        if ($stmt6963->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6963 to $status6963.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6963->close();
    }

    //FOR ID 6964
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6964'])) {
        // Get form data
        $assetId6964 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6964 = $_POST['status']; // Get the status from the form
        $description6964 = $_POST['description']; // Get the description from the form
        $room6964 = $_POST['room']; // Get the room from the form
        $assignedBy6964 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6964 = $status6964 === 'Need Repair' ? '' : $assignedName6964;

        // Prepare SQL query to update the asset
        $sql6964 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6964 = $conn->prepare($sql6964);
        $stmt6964->bind_param('sssssi', $status6964, $assignedName6964, $assignedBy6964, $description6964, $room6964, $assetId6964);

        if ($stmt6964->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6964 to $status6964.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6964->close();
    }

    //FOR ID 6965
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6965'])) {
        // Get form data
        $assetId6965 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6965 = $_POST['status']; // Get the status from the form
        $description6965 = $_POST['description']; // Get the description from the form
        $room6965 = $_POST['room']; // Get the room from the form
        $assignedBy6965 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6965 = $status6965 === 'Need Repair' ? '' : $assignedName6965;

        // Prepare SQL query to update the asset
        $sql6965 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6965 = $conn->prepare($sql6965);
        $stmt6965->bind_param('sssssi', $status6965, $assignedName6965, $assignedBy6965, $description6965, $room6965, $assetId6965);

        if ($stmt6965->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6965 to $status6965.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6965->close();
    }

    //FOR ID 6966
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6966'])) {
        // Get form data
        $assetId6966 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6966 = $_POST['status']; // Get the status from the form
        $description6966 = $_POST['description']; // Get the description from the form
        $room6966 = $_POST['room']; // Get the room from the form
        $assignedBy6966 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6966 = $status6966 === 'Need Repair' ? '' : $assignedName6966;

        // Prepare SQL query to update the asset
        $sql6966 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6966 = $conn->prepare($sql6966);
        $stmt6966->bind_param('sssssi', $status6966, $assignedName6966, $assignedBy6966, $description6966, $room6966, $assetId6966);

        if ($stmt6966->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6966 to $status6966.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6966->close();
    }

    //FOR ID 6967
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6967'])) {
        // Get form data
        $assetId6967 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6967 = $_POST['status']; // Get the status from the form
        $description6967 = $_POST['description']; // Get the description from the form
        $room6967 = $_POST['room']; // Get the room from the form
        $assignedBy6967 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6967 = $status6967 === 'Need Repair' ? '' : $assignedName6967;

        // Prepare SQL query to update the asset
        $sql6967 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6967 = $conn->prepare($sql6967);
        $stmt6967->bind_param('sssssi', $status6967, $assignedName6967, $assignedBy6967, $description6967, $room6967, $assetId6967);

        if ($stmt6967->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6967 to $status6967.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6967->close();
    }

    //FOR ID 6968
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6968'])) {
        // Get form data
        $assetId6968 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6968 = $_POST['status']; // Get the status from the form
        $description6968 = $_POST['description']; // Get the description from the form
        $room6968 = $_POST['room']; // Get the room from the form
        $assignedBy6968 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6968 = $status6968 === 'Need Repair' ? '' : $assignedName6968;

        // Prepare SQL query to update the asset
        $sql6968 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6968 = $conn->prepare($sql6968);
        $stmt6968->bind_param('sssssi', $status6968, $assignedName6968, $assignedBy6968, $description6968, $room6968, $assetId6968);

        if ($stmt6968->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6968 to $status6968.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6968->close();
    }

    //FOR ID 6969
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6969'])) {
        // Get form data
        $assetId6969 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6969 = $_POST['status']; // Get the status from the form
        $description6969 = $_POST['description']; // Get the description from the form
        $room6969 = $_POST['room']; // Get the room from the form
        $assignedBy6969 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6969 = $status6969 === 'Need Repair' ? '' : $assignedName6969;

        // Prepare SQL query to update the asset
        $sql6969 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6969 = $conn->prepare($sql6969);
        $stmt6969->bind_param('sssssi', $status6969, $assignedName6969, $assignedBy6969, $description6969, $room6969, $assetId6969);

        if ($stmt6969->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6969 to $status6969.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6969->close();
    }

    //FOR ID 6970
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6970'])) {
        // Get form data
        $assetId6970 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6970 = $_POST['status']; // Get the status from the form
        $description6970 = $_POST['description']; // Get the description from the form
        $room6970 = $_POST['room']; // Get the room from the form
        $assignedBy6970 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6970 = $status6970 === 'Need Repair' ? '' : $assignedName6970;

        // Prepare SQL query to update the asset
        $sql6970 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6970 = $conn->prepare($sql6970);
        $stmt6970->bind_param('sssssi', $status6970, $assignedName6970, $assignedBy6970, $description6970, $room6970, $assetId6970);

        if ($stmt6970->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6970 to $status6970.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6970->close();
    }

    //FOR ID 6971
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6971'])) {
        // Get form data
        $assetId6971 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6971 = $_POST['status']; // Get the status from the form
        $description6971 = $_POST['description']; // Get the description from the form
        $room6971 = $_POST['room']; // Get the room from the form
        $assignedBy6971 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6971 = $status6971 === 'Need Repair' ? '' : $assignedName6971;

        // Prepare SQL query to update the asset
        $sql6971 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6971 = $conn->prepare($sql6971);
        $stmt6971->bind_param('sssssi', $status6971, $assignedName6971, $assignedBy6971, $description6971, $room6971, $assetId6971);

        if ($stmt6971->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6971 to $status6971.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6971->close();
    }

    //FOR ID 6972
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6972'])) {
        // Get form data
        $assetId6972 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6972 = $_POST['status']; // Get the status from the form
        $description6972 = $_POST['description']; // Get the description from the form
        $room6972 = $_POST['room']; // Get the room from the form
        $assignedBy6972 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6972 = $status6972 === 'Need Repair' ? '' : $assignedName6972;

        // Prepare SQL query to update the asset
        $sql6972 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6972 = $conn->prepare($sql6972);
        $stmt6972->bind_param('sssssi', $status6972, $assignedName6972, $assignedBy6972, $description6972, $room6972, $assetId6972);

        if ($stmt6972->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6972 to $status6972.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6972->close();
    }

    //FOR ID 6973
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6973'])) {
        // Get form data
        $assetId6973 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6973 = $_POST['status']; // Get the status from the form
        $description6973 = $_POST['description']; // Get the description from the form
        $room6973 = $_POST['room']; // Get the room from the form
        $assignedBy6973 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6973 = $status6973 === 'Need Repair' ? '' : $assignedName6973;

        // Prepare SQL query to update the asset
        $sql6973 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6973 = $conn->prepare($sql6973);
        $stmt6973->bind_param('sssssi', $status6973, $assignedName6973, $assignedBy6973, $description6973, $room6973, $assetId6973);

        if ($stmt6973->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6973 to $status6973.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6973->close();
    }

    //FOR ID 6974
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6974'])) {
        // Get form data
        $assetId6974 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6974 = $_POST['status']; // Get the status from the form
        $description6974 = $_POST['description']; // Get the description from the form
        $room6974 = $_POST['room']; // Get the room from the form
        $assignedBy6974 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6974 = $status6974 === 'Need Repair' ? '' : $assignedName6974;

        // Prepare SQL query to update the asset
        $sql6974 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6974 = $conn->prepare($sql6974);
        $stmt6974->bind_param('sssssi', $status6974, $assignedName6974, $assignedBy6974, $description6974, $room6974, $assetId6974);

        if ($stmt6974->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6974 to $status6974.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6974->close();
    }

    //FOR ID 6975
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6975'])) {
        // Get form data
        $assetId6975 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6975 = $_POST['status']; // Get the status from the form
        $description6975 = $_POST['description']; // Get the description from the form
        $room6975 = $_POST['room']; // Get the room from the form
        $assignedBy6975 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6975 = $status6975 === 'Need Repair' ? '' : $assignedName6975;

        // Prepare SQL query to update the asset
        $sql6975 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6975 = $conn->prepare($sql6975);
        $stmt6975->bind_param('sssssi', $status6975, $assignedName6975, $assignedBy6975, $description6975, $room6975, $assetId6975);

        if ($stmt6975->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6975 to $status6975.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6975->close();
    }

    //FOR ID 6976
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6976'])) {
        // Get form data
        $assetId6976 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6976 = $_POST['status']; // Get the status from the form
        $description6976 = $_POST['description']; // Get the description from the form
        $room6976 = $_POST['room']; // Get the room from the form
        $assignedBy6976 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6976 = $status6976 === 'Need Repair' ? '' : $assignedName6976;

        // Prepare SQL query to update the asset
        $sql6976 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6976 = $conn->prepare($sql6976);
        $stmt6976->bind_param('sssssi', $status6976, $assignedName6976, $assignedBy6976, $description6976, $room6976, $assetId6976);

        if ($stmt6976->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6976 to $status6976.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6976->close();
    }

    //FOR ID 6977
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6977'])) {
        // Get form data
        $assetId6977 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6977 = $_POST['status']; // Get the status from the form
        $description6977 = $_POST['description']; // Get the description from the form
        $room6977 = $_POST['room']; // Get the room from the form
        $assignedBy6977 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6977 = $status6977 === 'Need Repair' ? '' : $assignedName6977;

        // Prepare SQL query to update the asset
        $sql6977 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6977 = $conn->prepare($sql6977);
        $stmt6977->bind_param('sssssi', $status6977, $assignedName6977, $assignedBy6977, $description6977, $room6977, $assetId6977);

        if ($stmt6977->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6977 to $status6977.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6977->close();
    }

    //FOR ID 6978
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6978'])) {
        // Get form data
        $assetId6978 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6978 = $_POST['status']; // Get the status from the form
        $description6978 = $_POST['description']; // Get the description from the form
        $room6978 = $_POST['room']; // Get the room from the form
        $assignedBy6978 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6978 = $status6978 === 'Need Repair' ? '' : $assignedName6978;

        // Prepare SQL query to update the asset
        $sql6978 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6978 = $conn->prepare($sql6978);
        $stmt6978->bind_param('sssssi', $status6978, $assignedName6978, $assignedBy6978, $description6978, $room6978, $assetId6978);

        if ($stmt6978->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6978 to $status6978.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6978->close();
    }

    //FOR ID 6979
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6979'])) {
        // Get form data
        $assetId6979 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6979 = $_POST['status']; // Get the status from the form
        $description6979 = $_POST['description']; // Get the description from the form
        $room6979 = $_POST['room']; // Get the room from the form
        $assignedBy6979 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6979 = $status6979 === 'Need Repair' ? '' : $assignedName6979;

        // Prepare SQL query to update the asset
        $sql6979 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6979 = $conn->prepare($sql6979);
        $stmt6979->bind_param('sssssi', $status6979, $assignedName6979, $assignedBy6979, $description6979, $room6979, $assetId6979);

        if ($stmt6979->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6979 to $status6979.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6979->close();
    }

    //FOR ID 6980
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6980'])) {
        // Get form data
        $assetId6980 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6980 = $_POST['status']; // Get the status from the form
        $description6980 = $_POST['description']; // Get the description from the form
        $room6980 = $_POST['room']; // Get the room from the form
        $assignedBy6980 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6980 = $status6980 === 'Need Repair' ? '' : $assignedName6980;

        // Prepare SQL query to update the asset
        $sql6980 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6980 = $conn->prepare($sql6980);
        $stmt6980->bind_param('sssssi', $status6980, $assignedName6980, $assignedBy6980, $description6980, $room6980, $assetId6980);

        if ($stmt6980->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6980 to $status6980.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6980->close();
    }

    //FOR ID 6981
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6981'])) {
        // Get form data
        $assetId6981 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6981 = $_POST['status']; // Get the status from the form
        $description6981 = $_POST['description']; // Get the description from the form
        $room6981 = $_POST['room']; // Get the room from the form
        $assignedBy6981 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6981 = $status6981 === 'Need Repair' ? '' : $assignedName6981;

        // Prepare SQL query to update the asset
        $sql6981 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6981 = $conn->prepare($sql6981);
        $stmt6981->bind_param('sssssi', $status6981, $assignedName6981, $assignedBy6981, $description6981, $room6981, $assetId6981);

        if ($stmt6981->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6981 to $status6981.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6981->close();
    }

    //FOR ID 6982
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6982'])) {
        // Get form data
        $assetId6982 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6982 = $_POST['status']; // Get the status from the form
        $description6982 = $_POST['description']; // Get the description from the form
        $room6982 = $_POST['room']; // Get the room from the form
        $assignedBy6982 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6982 = $status6982 === 'Need Repair' ? '' : $assignedName6982;

        // Prepare SQL query to update the asset
        $sql6982 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6982 = $conn->prepare($sql6982);
        $stmt6982->bind_param('sssssi', $status6982, $assignedName6982, $assignedBy6982, $description6982, $room6982, $assetId6982);

        if ($stmt6982->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6982 to $status6982.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6982->close();
    }

    //FOR ID 6983
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6983'])) {
        // Get form data
        $assetId6983 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6983 = $_POST['status']; // Get the status from the form
        $description6983 = $_POST['description']; // Get the description from the form
        $room6983 = $_POST['room']; // Get the room from the form
        $assignedBy6983 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6983 = $status6983 === 'Need Repair' ? '' : $assignedName6983;

        // Prepare SQL query to update the asset
        $sql6983 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6983 = $conn->prepare($sql6983);
        $stmt6983->bind_param('sssssi', $status6983, $assignedName6983, $assignedBy6983, $description6983, $room6983, $assetId6983);

        if ($stmt6983->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6983 to $status6983.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6983->close();
    }

    //FOR ID 6984
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6984'])) {
        // Get form data
        $assetId6984 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6984 = $_POST['status']; // Get the status from the form
        $description6984 = $_POST['description']; // Get the description from the form
        $room6984 = $_POST['room']; // Get the room from the form
        $assignedBy6984 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6984 = $status6984 === 'Need Repair' ? '' : $assignedName6984;

        // Prepare SQL query to update the asset
        $sql6984 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6984 = $conn->prepare($sql6984);
        $stmt6984->bind_param('sssssi', $status6984, $assignedName6984, $assignedBy6984, $description6984, $room6984, $assetId6984);

        if ($stmt6984->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6984 to $status6984.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6984->close();
    }

    //FOR ID 6985
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6985'])) {
        // Get form data
        $assetId6985 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6985 = $_POST['status']; // Get the status from the form
        $description6985 = $_POST['description']; // Get the description from the form
        $room6985 = $_POST['room']; // Get the room from the form
        $assignedBy6985 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6985 = $status6985 === 'Need Repair' ? '' : $assignedName6985;

        // Prepare SQL query to update the asset
        $sql6985 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6985 = $conn->prepare($sql6985);
        $stmt6985->bind_param('sssssi', $status6985, $assignedName6985, $assignedBy6985, $description6985, $room6985, $assetId6985);

        if ($stmt6985->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6985 to $status6985.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6985->close();
    }

    //FOR ID 6986
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6986'])) {
        // Get form data
        $assetId6986 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6986 = $_POST['status']; // Get the status from the form
        $description6986 = $_POST['description']; // Get the description from the form
        $room6986 = $_POST['room']; // Get the room from the form
        $assignedBy6986 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6986 = $status6986 === 'Need Repair' ? '' : $assignedName6986;

        // Prepare SQL query to update the asset
        $sql6986 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6986 = $conn->prepare($sql6986);
        $stmt6986->bind_param('sssssi', $status6986, $assignedName6986, $assignedBy6986, $description6986, $room6986, $assetId6986);

        if ($stmt6986->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6986 to $status6986.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6986->close();
    }

    //FOR ID 6987
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6987'])) {
        // Get form data
        $assetId6987 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6987 = $_POST['status']; // Get the status from the form
        $description6987 = $_POST['description']; // Get the description from the form
        $room6987 = $_POST['room']; // Get the room from the form
        $assignedBy6987 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6987 = $status6987 === 'Need Repair' ? '' : $assignedName6987;

        // Prepare SQL query to update the asset
        $sql6987 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6987 = $conn->prepare($sql6987);
        $stmt6987->bind_param('sssssi', $status6987, $assignedName6987, $assignedBy6987, $description6987, $room6987, $assetId6987);

        if ($stmt6987->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6987 to $status6987.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6987->close();
    }

    //FOR ID 6988
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6988'])) {
        // Get form data
        $assetId6988 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6988 = $_POST['status']; // Get the status from the form
        $description6988 = $_POST['description']; // Get the description from the form
        $room6988 = $_POST['room']; // Get the room from the form
        $assignedBy6988 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6988 = $status6988 === 'Need Repair' ? '' : $assignedName6988;

        // Prepare SQL query to update the asset
        $sql6988 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6988 = $conn->prepare($sql6988);
        $stmt6988->bind_param('sssssi', $status6988, $assignedName6988, $assignedBy6988, $description6988, $room6988, $assetId6988);

        if ($stmt6988->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6988 to $status6988.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6988->close();
    }

    //FOR ID 6989
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6989'])) {
        // Get form data
        $assetId6989 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6989 = $_POST['status']; // Get the status from the form
        $description6989 = $_POST['description']; // Get the description from the form
        $room6989 = $_POST['room']; // Get the room from the form
        $assignedBy6989 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6989 = $status6989 === 'Need Repair' ? '' : $assignedName6989;

        // Prepare SQL query to update the asset
        $sql6989 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6989 = $conn->prepare($sql6989);
        $stmt6989->bind_param('sssssi', $status6989, $assignedName6989, $assignedBy6989, $description6989, $room6989, $assetId6989);

        if ($stmt6989->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6989 to $status6989.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6989->close();
    }

    //FOR ID 6990
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6990'])) {
        // Get form data
        $assetId6990 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6990 = $_POST['status']; // Get the status from the form
        $description6990 = $_POST['description']; // Get the description from the form
        $room6990 = $_POST['room']; // Get the room from the form
        $assignedBy6990 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6990 = $status6990 === 'Need Repair' ? '' : $assignedName6990;

        // Prepare SQL query to update the asset
        $sql6990 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6990 = $conn->prepare($sql6990);
        $stmt6990->bind_param('sssssi', $status6990, $assignedName6990, $assignedBy6990, $description6990, $room6990, $assetId6990);

        if ($stmt6990->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6990 to $status6990.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6990->close();
    }

    //FOR ID 6991
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6991'])) {
        // Get form data
        $assetId6991 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6991 = $_POST['status']; // Get the status from the form
        $description6991 = $_POST['description']; // Get the description from the form
        $room6991 = $_POST['room']; // Get the room from the form
        $assignedBy6991 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6991 = $status6991 === 'Need Repair' ? '' : $assignedName6991;

        // Prepare SQL query to update the asset
        $sql6991 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6991 = $conn->prepare($sql6991);
        $stmt6991->bind_param('sssssi', $status6991, $assignedName6991, $assignedBy6991, $description6991, $room6991, $assetId6991);

        if ($stmt6991->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6991 to $status6991.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6991->close();
    }

    //FOR ID 6992
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6992'])) {
        // Get form data
        $assetId6992 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6992 = $_POST['status']; // Get the status from the form
        $description6992 = $_POST['description']; // Get the description from the form
        $room6992 = $_POST['room']; // Get the room from the form
        $assignedBy6992 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6992 = $status6992 === 'Need Repair' ? '' : $assignedName6992;

        // Prepare SQL query to update the asset
        $sql6992 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6992 = $conn->prepare($sql6992);
        $stmt6992->bind_param('sssssi', $status6992, $assignedName6992, $assignedBy6992, $description6992, $room6992, $assetId6992);

        if ($stmt6992->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6992 to $status6992.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6992->close();
    }

    //FOR ID 6993
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6993'])) {
        // Get form data
        $assetId6993 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6993 = $_POST['status']; // Get the status from the form
        $description6993 = $_POST['description']; // Get the description from the form
        $room6993 = $_POST['room']; // Get the room from the form
        $assignedBy6993 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6993 = $status6993 === 'Need Repair' ? '' : $assignedName6993;

        // Prepare SQL query to update the asset
        $sql6993 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6993 = $conn->prepare($sql6993);
        $stmt6993->bind_param('sssssi', $status6993, $assignedName6993, $assignedBy6993, $description6993, $room6993, $assetId6993);

        if ($stmt6993->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6993 to $status6993.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6993->close();
    }

    //FOR ID 6994
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6994'])) {
        // Get form data
        $assetId6994 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6994 = $_POST['status']; // Get the status from the form
        $description6994 = $_POST['description']; // Get the description from the form
        $room6994 = $_POST['room']; // Get the room from the form
        $assignedBy6994 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6994 = $status6994 === 'Need Repair' ? '' : $assignedName6994;

        // Prepare SQL query to update the asset
        $sql6994 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6994 = $conn->prepare($sql6994);
        $stmt6994->bind_param('sssssi', $status6994, $assignedName6994, $assignedBy6994, $description6994, $room6994, $assetId6994);

        if ($stmt6994->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6994 to $status6994.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6994->close();
    }

    //FOR ID 6995
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6995'])) {
        // Get form data
        $assetId6995 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6995 = $_POST['status']; // Get the status from the form
        $description6995 = $_POST['description']; // Get the description from the form
        $room6995 = $_POST['room']; // Get the room from the form
        $assignedBy6995 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6995 = $status6995 === 'Need Repair' ? '' : $assignedName6995;

        // Prepare SQL query to update the asset
        $sql6995 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6995 = $conn->prepare($sql6995);
        $stmt6995->bind_param('sssssi', $status6995, $assignedName6995, $assignedBy6995, $description6995, $room6995, $assetId6995);

        if ($stmt6995->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6995 to $status6995.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6995->close();
    }
    //FOR ID 6996
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6996'])) {
        // Get form data
        $assetId6996 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6996 = $_POST['status']; // Get the status from the form
        $description6996 = $_POST['description']; // Get the description from the form
        $room6996 = $_POST['room']; // Get the room from the form
        $assignedBy6996 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6996 = $status6996 === 'Need Repair' ? '' : $assignedName6996;

        // Prepare SQL query to update the asset
        $sql6996 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6996 = $conn->prepare($sql6996);
        $stmt6996->bind_param('sssssi', $status6996, $assignedName6996, $assignedBy6996, $description6996, $room6996, $assetId6996);

        if ($stmt6996->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6996 to $status6996.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6996->close();
    }

    //FOR ID 6997
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6997'])) {
        // Get form data
        $assetId6997 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6997 = $_POST['status']; // Get the status from the form
        $description6997 = $_POST['description']; // Get the description from the form
        $room6997 = $_POST['room']; // Get the room from the form
        $assignedBy6997 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6997 = $status6997 === 'Need Repair' ? '' : $assignedName6997;

        // Prepare SQL query to update the asset
        $sql6997 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6997 = $conn->prepare($sql6997);
        $stmt6997->bind_param('sssssi', $status6997, $assignedName6997, $assignedBy6997, $description6997, $room6997, $assetId6997);

        if ($stmt6997->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6997 to $status6997.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6997->close();
    }

    //FOR ID 6998
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6998'])) {
        // Get form data
        $assetId6998 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6998 = $_POST['status']; // Get the status from the form
        $description6998 = $_POST['description']; // Get the description from the form
        $room6998 = $_POST['room']; // Get the room from the form
        $assignedBy6998 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6998 = $status6998 === 'Need Repair' ? '' : $assignedName6998;

        // Prepare SQL query to update the asset
        $sql6998 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6998 = $conn->prepare($sql6998);
        $stmt6998->bind_param('sssssi', $status6998, $assignedName6998, $assignedBy6998, $description6998, $room6998, $assetId6998);

        if ($stmt6998->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6998 to $status6998.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6998->close();
    }

    //FOR ID 6999
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit6999'])) {
        // Get form data
        $assetId6999 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status6999 = $_POST['status']; // Get the status from the form
        $description6999 = $_POST['description']; // Get the description from the form
        $room6999 = $_POST['room']; // Get the room from the form
        $assignedBy6999 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName6999 = $status6999 === 'Need Repair' ? '' : $assignedName6999;

        // Prepare SQL query to update the asset
        $sql6999 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt6999 = $conn->prepare($sql6999);
        $stmt6999->bind_param('sssssi', $status6999, $assignedName6999, $assignedBy6999, $description6999, $room6999, $assetId6999);

        if ($stmt6999->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId6999 to $status6999.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt6999->close();
    }

    //FOR ID 7000
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7000'])) {
        // Get form data
        $assetId7000 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7000 = $_POST['status']; // Get the status from the form
        $description7000 = $_POST['description']; // Get the description from the form
        $room7000 = $_POST['room']; // Get the room from the form
        $assignedBy7000 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7000 = $status7000 === 'Need Repair' ? '' : $assignedName7000;

        // Prepare SQL query to update the asset
        $sql7000 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7000 = $conn->prepare($sql7000);
        $stmt7000->bind_param('sssssi', $status7000, $assignedName7000, $assignedBy7000, $description7000, $room7000, $assetId7000);

        if ($stmt7000->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7000 to $status7000.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7000->close();
    }

    //FOR ID 7001
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7001'])) {
        // Get form data
        $assetId7001 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7001 = $_POST['status']; // Get the status from the form
        $description7001 = $_POST['description']; // Get the description from the form
        $room7001 = $_POST['room']; // Get the room from the form
        $assignedBy7001 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7001 = $status7001 === 'Need Repair' ? '' : $assignedName7001;

        // Prepare SQL query to update the asset
        $sql7001 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7001 = $conn->prepare($sql7001);
        $stmt7001->bind_param('sssssi', $status7001, $assignedName7001, $assignedBy7001, $description7001, $room7001, $assetId7001);

        if ($stmt7001->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7001 to $status7001.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7001->close();
    }

    //FOR ID 7002
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7002'])) {
        // Get form data
        $assetId7002 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7002 = $_POST['status']; // Get the status from the form
        $description7002 = $_POST['description']; // Get the description from the form
        $room7002 = $_POST['room']; // Get the room from the form
        $assignedBy7002 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7002 = $status7002 === 'Need Repair' ? '' : $assignedName7002;

        // Prepare SQL query to update the asset
        $sql7002 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7002 = $conn->prepare($sql7002);
        $stmt7002->bind_param('sssssi', $status7002, $assignedName7002, $assignedBy7002, $description7002, $room7002, $assetId7002);

        if ($stmt7002->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7002 to $status7002.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7002->close();
    }

    //FOR ID 7003
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7003'])) {
        // Get form data
        $assetId7003 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7003 = $_POST['status']; // Get the status from the form
        $description7003 = $_POST['description']; // Get the description from the form
        $room7003 = $_POST['room']; // Get the room from the form
        $assignedBy7003 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7003 = $status7003 === 'Need Repair' ? '' : $assignedName7003;

        // Prepare SQL query to update the asset
        $sql7003 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7003 = $conn->prepare($sql7003);
        $stmt7003->bind_param('sssssi', $status7003, $assignedName7003, $assignedBy7003, $description7003, $room7003, $assetId7003);

        if ($stmt7003->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7003 to $status7003.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7003->close();
    }

    //FOR ID 7004
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7004'])) {
        // Get form data
        $assetId7004 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7004 = $_POST['status']; // Get the status from the form
        $description7004 = $_POST['description']; // Get the description from the form
        $room7004 = $_POST['room']; // Get the room from the form
        $assignedBy7004 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7004 = $status7004 === 'Need Repair' ? '' : $assignedName7004;

        // Prepare SQL query to update the asset
        $sql7004 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7004 = $conn->prepare($sql7004);
        $stmt7004->bind_param('sssssi', $status7004, $assignedName7004, $assignedBy7004, $description7004, $room7004, $assetId7004);

        if ($stmt7004->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7004 to $status7004.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7004->close();
    }

    //FOR ID 7005
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7005'])) {
        // Get form data
        $assetId7005 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7005 = $_POST['status']; // Get the status from the form
        $description7005 = $_POST['description']; // Get the description from the form
        $room7005 = $_POST['room']; // Get the room from the form
        $assignedBy7005 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7005 = $status7005 === 'Need Repair' ? '' : $assignedName7005;

        // Prepare SQL query to update the asset
        $sql7005 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7005 = $conn->prepare($sql7005);
        $stmt7005->bind_param('sssssi', $status7005, $assignedName7005, $assignedBy7005, $description7005, $room7005, $assetId7005);

        if ($stmt7005->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7005 to $status7005.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7005->close();
    }

    //FOR ID 7006
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7006'])) {
        // Get form data
        $assetId7006 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7006 = $_POST['status']; // Get the status from the form
        $description7006 = $_POST['description']; // Get the description from the form
        $room7006 = $_POST['room']; // Get the room from the form
        $assignedBy7006 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7006 = $status7006 === 'Need Repair' ? '' : $assignedName7006;

        // Prepare SQL query to update the asset
        $sql7006 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7006 = $conn->prepare($sql7006);
        $stmt7006->bind_param('sssssi', $status7006, $assignedName7006, $assignedBy7006, $description7006, $room7006, $assetId7006);

        if ($stmt7006->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7006 to $status7006.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7006->close();
    }

    //FOR ID 7007
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7007'])) {
        // Get form data
        $assetId7007 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7007 = $_POST['status']; // Get the status from the form
        $description7007 = $_POST['description']; // Get the description from the form
        $room7007 = $_POST['room']; // Get the room from the form
        $assignedBy7007 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7007 = $status7007 === 'Need Repair' ? '' : $assignedName7007;

        // Prepare SQL query to update the asset
        $sql7007 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7007 = $conn->prepare($sql7007);
        $stmt7007->bind_param('sssssi', $status7007, $assignedName7007, $assignedBy7007, $description7007, $room7007, $assetId7007);

        if ($stmt7007->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7007 to $status7007.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7007->close();
    }

    //FOR ID 7008
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7008'])) {
        // Get form data
        $assetId7008 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7008 = $_POST['status']; // Get the status from the form
        $description7008 = $_POST['description']; // Get the description from the form
        $room7008 = $_POST['room']; // Get the room from the form
        $assignedBy7008 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7008 = $status7008 === 'Need Repair' ? '' : $assignedName7008;

        // Prepare SQL query to update the asset
        $sql7008 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7008 = $conn->prepare($sql7008);
        $stmt7008->bind_param('sssssi', $status7008, $assignedName7008, $assignedBy7008, $description7008, $room7008, $assetId7008);

        if ($stmt7008->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7008 to $status7008.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7008->close();
    }

    //FOR ID 7009
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7009'])) {
        // Get form data
        $assetId7009 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7009 = $_POST['status']; // Get the status from the form
        $description7009 = $_POST['description']; // Get the description from the form
        $room7009 = $_POST['room']; // Get the room from the form
        $assignedBy7009 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7009 = $status7009 === 'Need Repair' ? '' : $assignedName7009;

        // Prepare SQL query to update the asset
        $sql7009 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7009 = $conn->prepare($sql7009);
        $stmt7009->bind_param('sssssi', $status7009, $assignedName7009, $assignedBy7009, $description7009, $room7009, $assetId7009);

        if ($stmt7009->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7009 to $status7009.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7009->close();
    }
    //FOR ID 7010
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7010'])) {
        // Get form data
        $assetId7010 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7010 = $_POST['status']; // Get the status from the form
        $description7010 = $_POST['description']; // Get the description from the form
        $room7010 = $_POST['room']; // Get the room from the form
        $assignedBy7010 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7010 = $status7010 === 'Need Repair' ? '' : $assignedName7010;

        // Prepare SQL query to update the asset
        $sql7010 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7010 = $conn->prepare($sql7010);
        $stmt7010->bind_param('sssssi', $status7010, $assignedName7010, $assignedBy7010, $description7010, $room7010, $assetId7010);

        if ($stmt7010->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7010 to $status7010.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7010->close();
    }

    //FOR ID 7011
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7011'])) {
        // Get form data
        $assetId7011 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7011 = $_POST['status']; // Get the status from the form
        $description7011 = $_POST['description']; // Get the description from the form
        $room7011 = $_POST['room']; // Get the room from the form
        $assignedBy7011 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7011 = $status7011 === 'Need Repair' ? '' : $assignedName7011;

        // Prepare SQL query to update the asset
        $sql7011 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7011 = $conn->prepare($sql7011);
        $stmt7011->bind_param('sssssi', $status7011, $assignedName7011, $assignedBy7011, $description7011, $room7011, $assetId7011);

        if ($stmt7011->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7011 to $status7011.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7011->close();
    }

    //FOR ID 7012
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7012'])) {
        // Get form data
        $assetId7012 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7012 = $_POST['status']; // Get the status from the form
        $description7012 = $_POST['description']; // Get the description from the form
        $room7012 = $_POST['room']; // Get the room from the form
        $assignedBy7012 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7012 = $status7012 === 'Need Repair' ? '' : $assignedName7012;

        // Prepare SQL query to update the asset
        $sql7012 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7012 = $conn->prepare($sql7012);
        $stmt7012->bind_param('sssssi', $status7012, $assignedName7012, $assignedBy7012, $description7012, $room7012, $assetId7012);

        if ($stmt7012->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7012 to $status7012.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7012->close();
    }

    //FOR ID 7013
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7013'])) {
        // Get form data
        $assetId7013 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7013 = $_POST['status']; // Get the status from the form
        $description7013 = $_POST['description']; // Get the description from the form
        $room7013 = $_POST['room']; // Get the room from the form
        $assignedBy7013 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7013 = $status7013 === 'Need Repair' ? '' : $assignedName7013;

        // Prepare SQL query to update the asset
        $sql7013 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7013 = $conn->prepare($sql7013);
        $stmt7013->bind_param('sssssi', $status7013, $assignedName7013, $assignedBy7013, $description7013, $room7013, $assetId7013);

        if ($stmt7013->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7013 to $status7013.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7013->close();
    }

    //FOR ID 7014
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7014'])) {
        // Get form data
        $assetId7014 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7014 = $_POST['status']; // Get the status from the form
        $description7014 = $_POST['description']; // Get the description from the form
        $room7014 = $_POST['room']; // Get the room from the form
        $assignedBy7014 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7014 = $status7014 === 'Need Repair' ? '' : $assignedName7014;

        // Prepare SQL query to update the asset
        $sql7014 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7014 = $conn->prepare($sql7014);
        $stmt7014->bind_param('sssssi', $status7014, $assignedName7014, $assignedBy7014, $description7014, $room7014, $assetId7014);

        if ($stmt7014->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7014 to $status7014.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7014->close();
    }

    //FOR ID 7015
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7015'])) {
        // Get form data
        $assetId7015 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7015 = $_POST['status']; // Get the status from the form
        $description7015 = $_POST['description']; // Get the description from the form
        $room7015 = $_POST['room']; // Get the room from the form
        $assignedBy7015 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7015 = $status7015 === 'Need Repair' ? '' : $assignedName7015;

        // Prepare SQL query to update the asset
        $sql7015 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7015 = $conn->prepare($sql7015);
        $stmt7015->bind_param('sssssi', $status7015, $assignedName7015, $assignedBy7015, $description7015, $room7015, $assetId7015);

        if ($stmt7015->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7015 to $status7015.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7015->close();
    }

    //FOR ID 7016
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7016'])) {
        // Get form data
        $assetId7016 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7016 = $_POST['status']; // Get the status from the form
        $description7016 = $_POST['description']; // Get the description from the form
        $room7016 = $_POST['room']; // Get the room from the form
        $assignedBy7016 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7016 = $status7016 === 'Need Repair' ? '' : $assignedName7016;

        // Prepare SQL query to update the asset
        $sql7016 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7016 = $conn->prepare($sql7016);
        $stmt7016->bind_param('sssssi', $status7016, $assignedName7016, $assignedBy7016, $description7016, $room7016, $assetId7016);

        if ($stmt7016->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7016 to $status7016.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7016->close();
    }

    //FOR ID 7016
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7016'])) {
        // Get form data
        $assetId7016 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7016 = $_POST['status']; // Get the status from the form
        $description7016 = $_POST['description']; // Get the description from the form
        $room7016 = $_POST['room']; // Get the room from the form
        $assignedBy7016 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7016 = $status7016 === 'Need Repair' ? '' : $assignedName7016;

        // Prepare SQL query to update the asset
        $sql7016 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7016 = $conn->prepare($sql7016);
        $stmt7016->bind_param('sssssi', $status7016, $assignedName7016, $assignedBy7016, $description7016, $room7016, $assetId7016);

        if ($stmt7016->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7016 to $status7016.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7016->close();
    }

    //FOR ID 7017
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7017'])) {
        // Get form data
        $assetId7017 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7017 = $_POST['status']; // Get the status from the form
        $description7017 = $_POST['description']; // Get the description from the form
        $room7017 = $_POST['room']; // Get the room from the form
        $assignedBy7017 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7017 = $status7017 === 'Need Repair' ? '' : $assignedName7017;

        // Prepare SQL query to update the asset
        $sql7017 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7017 = $conn->prepare($sql7017);
        $stmt7017->bind_param('sssssi', $status7017, $assignedName7017, $assignedBy7017, $description7017, $room7017, $assetId7017);

        if ($stmt7017->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7017 to $status7017.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7017->close();
    }

    //FOR ID 7018
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7018'])) {
        // Get form data
        $assetId7018 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7018 = $_POST['status']; // Get the status from the form
        $description7018 = $_POST['description']; // Get the description from the form
        $room7018 = $_POST['room']; // Get the room from the form
        $assignedBy7018 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7018 = $status7018 === 'Need Repair' ? '' : $assignedName7018;

        // Prepare SQL query to update the asset
        $sql7018 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7018 = $conn->prepare($sql7018);
        $stmt7018->bind_param('sssssi', $status7018, $assignedName7018, $assignedBy7018, $description7018, $room7018, $assetId7018);

        if ($stmt7018->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7018 to $status7018.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7018->close();
    }

    //FOR ID 7019
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7019'])) {
        // Get form data
        $assetId7019 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7019 = $_POST['status']; // Get the status from the form
        $description7019 = $_POST['description']; // Get the description from the form
        $room7019 = $_POST['room']; // Get the room from the form
        $assignedBy7019 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7019 = $status7019 === 'Need Repair' ? '' : $assignedName7019;

        // Prepare SQL query to update the asset
        $sql7019 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7019 = $conn->prepare($sql7019);
        $stmt7019->bind_param('sssssi', $status7019, $assignedName7019, $assignedBy7019, $description7019, $room7019, $assetId7019);

        if ($stmt7019->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7019 to $status7019.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7019->close();
    }

    //FOR ID 7020
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7020'])) {
        // Get form data
        $assetId7020 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7020 = $_POST['status']; // Get the status from the form
        $description7020 = $_POST['description']; // Get the description from the form
        $room7020 = $_POST['room']; // Get the room from the form
        $assignedBy7020 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7020 = $status7020 === 'Need Repair' ? '' : $assignedName7020;

        // Prepare SQL query to update the asset
        $sql7020 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7020 = $conn->prepare($sql7020);
        $stmt7020->bind_param('sssssi', $status7020, $assignedName7020, $assignedBy7020, $description7020, $room7020, $assetId7020);

        if ($stmt7020->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7020 to $status7020.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7020->close();
    }

    //FOR ID 7021
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7021'])) {
        // Get form data
        $assetId7021 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7021 = $_POST['status']; // Get the status from the form
        $description7021 = $_POST['description']; // Get the description from the form
        $room7021 = $_POST['room']; // Get the room from the form
        $assignedBy7021 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7021 = $status7021 === 'Need Repair' ? '' : $assignedName7021;

        // Prepare SQL query to update the asset
        $sql7021 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7021 = $conn->prepare($sql7021);
        $stmt7021->bind_param('sssssi', $status7021, $assignedName7021, $assignedBy7021, $description7021, $room7021, $assetId7021);

        if ($stmt7021->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7021 to $status7021.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7021->close();
    }

    //FOR ID 7022
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7022'])) {
        // Get form data
        $assetId7022 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7022 = $_POST['status']; // Get the status from the form
        $description7022 = $_POST['description']; // Get the description from the form
        $room7022 = $_POST['room']; // Get the room from the form
        $assignedBy7022 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7022 = $status7022 === 'Need Repair' ? '' : $assignedName7022;

        // Prepare SQL query to update the asset
        $sql7022 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7022 = $conn->prepare($sql7022);
        $stmt7022->bind_param('sssssi', $status7022, $assignedName7022, $assignedBy7022, $description7022, $room7022, $assetId7022);

        if ($stmt7022->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7022 to $status7022.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7022->close();
    }

    //FOR ID 7023
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7023'])) {
        // Get form data
        $assetId7023 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7023 = $_POST['status']; // Get the status from the form
        $description7023 = $_POST['description']; // Get the description from the form
        $room7023 = $_POST['room']; // Get the room from the form
        $assignedBy7023 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7023 = $status7023 === 'Need Repair' ? '' : $assignedName7023;

        // Prepare SQL query to update the asset
        $sql7023 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7023 = $conn->prepare($sql7023);
        $stmt7023->bind_param('sssssi', $status7023, $assignedName7023, $assignedBy7023, $description7023, $room7023, $assetId7023);

        if ($stmt7023->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7023 to $status7023.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7023->close();
    }

    //FOR ID 7024
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7024'])) {
        // Get form data
        $assetId7024 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7024 = $_POST['status']; // Get the status from the form
        $description7024 = $_POST['description']; // Get the description from the form
        $room7024 = $_POST['room']; // Get the room from the form
        $assignedBy7024 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7024 = $status7024 === 'Need Repair' ? '' : $assignedName7024;

        // Prepare SQL query to update the asset
        $sql7024 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7024 = $conn->prepare($sql7024);
        $stmt7024->bind_param('sssssi', $status7024, $assignedName7024, $assignedBy7024, $description7024, $room7024, $assetId7024);

        if ($stmt7024->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7024 to $status7024.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7024->close();
    }

    //FOR ID 7025
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7025'])) {
        // Get form data
        $assetId7025 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7025 = $_POST['status']; // Get the status from the form
        $description7025 = $_POST['description']; // Get the description from the form
        $room7025 = $_POST['room']; // Get the room from the form
        $assignedBy7025 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7025 = $status7025 === 'Need Repair' ? '' : $assignedName7025;

        // Prepare SQL query to update the asset
        $sql7025 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7025 = $conn->prepare($sql7025);
        $stmt7025->bind_param('sssssi', $status7025, $assignedName7025, $assignedBy7025, $description7025, $room7025, $assetId7025);

        if ($stmt7025->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7025 to $status7025.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7025->close();
    }

    //FOR ID 7026
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7026'])) {
        // Get form data
        $assetId7026 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7026 = $_POST['status']; // Get the status from the form
        $description7026 = $_POST['description']; // Get the description from the form
        $room7026 = $_POST['room']; // Get the room from the form
        $assignedBy7026 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7026 = $status7026 === 'Need Repair' ? '' : $assignedName7026;

        // Prepare SQL query to update the asset
        $sql7026 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7026 = $conn->prepare($sql7026);
        $stmt7026->bind_param('sssssi', $status7026, $assignedName7026, $assignedBy7026, $description7026, $room7026, $assetId7026);

        if ($stmt7026->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7026 to $status7026.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7026->close();
    }

    //FOR ID 7027
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7027'])) {
        // Get form data
        $assetId7027 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7027 = $_POST['status']; // Get the status from the form
        $description7027 = $_POST['description']; // Get the description from the form
        $room7027 = $_POST['room']; // Get the room from the form
        $assignedBy7027 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7027 = $status7027 === 'Need Repair' ? '' : $assignedName7027;

        // Prepare SQL query to update the asset
        $sql7027 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7027 = $conn->prepare($sql7027);
        $stmt7027->bind_param('sssssi', $status7027, $assignedName7027, $assignedBy7027, $description7027, $room7027, $assetId7027);

        if ($stmt7027->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7027 to $status7027.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7027->close();
    }
    //FOR ID 7028
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7028'])) {
        // Get form data
        $assetId7028 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7028 = $_POST['status']; // Get the status from the form
        $description7028 = $_POST['description']; // Get the description from the form
        $room7028 = $_POST['room']; // Get the room from the form
        $assignedBy7028 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7028 = $status7028 === 'Need Repair' ? '' : $assignedName7028;

        // Prepare SQL query to update the asset
        $sql7028 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7028 = $conn->prepare($sql7028);
        $stmt7028->bind_param('sssssi', $status7028, $assignedName7028, $assignedBy7028, $description7028, $room7028, $assetId7028);

        if ($stmt7028->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7028 to $status7028.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7028->close();
    }

    //FOR ID 7029
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7029'])) {
        // Get form data
        $assetId7029 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7029 = $_POST['status']; // Get the status from the form
        $description7029 = $_POST['description']; // Get the description from the form
        $room7029 = $_POST['room']; // Get the room from the form
        $assignedBy7029 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7029 = $status7029 === 'Need Repair' ? '' : $assignedName7029;

        // Prepare SQL query to update the asset
        $sql7029 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7029 = $conn->prepare($sql7029);
        $stmt7029->bind_param('sssssi', $status7029, $assignedName7029, $assignedBy7029, $description7029, $room7029, $assetId7029);

        if ($stmt7029->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7029 to $status7029.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7029->close();
    }

    //FOR ID 7030
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7030'])) {
        // Get form data
        $assetId7030 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7030 = $_POST['status']; // Get the status from the form
        $description7030 = $_POST['description']; // Get the description from the form
        $room7030 = $_POST['room']; // Get the room from the form
        $assignedBy7030 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7030 = $status7030 === 'Need Repair' ? '' : $assignedName7030;

        // Prepare SQL query to update the asset
        $sql7030 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7030 = $conn->prepare($sql7030);
        $stmt7030->bind_param('sssssi', $status7030, $assignedName7030, $assignedBy7030, $description7030, $room7030, $assetId7030);

        if ($stmt7030->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7030 to $status7030.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7030->close();
    }

    //FOR ID 7031
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7031'])) {
        // Get form data
        $assetId7031 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7031 = $_POST['status']; // Get the status from the form
        $description7031 = $_POST['description']; // Get the description from the form
        $room7031 = $_POST['room']; // Get the room from the form
        $assignedBy7031 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7031 = $status7031 === 'Need Repair' ? '' : $assignedName7031;

        // Prepare SQL query to update the asset
        $sql7031 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7031 = $conn->prepare($sql7031);
        $stmt7031->bind_param('sssssi', $status7031, $assignedName7031, $assignedBy7031, $description7031, $room7031, $assetId7031);

        if ($stmt7031->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7031 to $status7031.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7031->close();
    }

    //FOR ID 7032
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7032'])) {
        // Get form data
        $assetId7032 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7032 = $_POST['status']; // Get the status from the form
        $description7032 = $_POST['description']; // Get the description from the form
        $room7032 = $_POST['room']; // Get the room from the form
        $assignedBy7032 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7032 = $status7032 === 'Need Repair' ? '' : $assignedName7032;

        // Prepare SQL query to update the asset
        $sql7032 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7032 = $conn->prepare($sql7032);
        $stmt7032->bind_param('sssssi', $status7032, $assignedName7032, $assignedBy7032, $description7032, $room7032, $assetId7032);

        if ($stmt7032->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7032 to $status7032.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7032->close();
    }

    //FOR ID 7033
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7033'])) {
        // Get form data
        $assetId7033 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7033 = $_POST['status']; // Get the status from the form
        $description7033 = $_POST['description']; // Get the description from the form
        $room7033 = $_POST['room']; // Get the room from the form
        $assignedBy7033 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7033 = $status7033 === 'Need Repair' ? '' : $assignedName7033;

        // Prepare SQL query to update the asset
        $sql7033 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7033 = $conn->prepare($sql7033);
        $stmt7033->bind_param('sssssi', $status7033, $assignedName7033, $assignedBy7033, $description7033, $room7033, $assetId7033);

        if ($stmt7033->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7033 to $status7033.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7033->close();
    }

    //FOR ID 7034
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7034'])) {
        // Get form data
        $assetId7034 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7034 = $_POST['status']; // Get the status from the form
        $description7034 = $_POST['description']; // Get the description from the form
        $room7034 = $_POST['room']; // Get the room from the form
        $assignedBy7034 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7034 = $status7034 === 'Need Repair' ? '' : $assignedName7034;

        // Prepare SQL query to update the asset
        $sql7034 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7034 = $conn->prepare($sql7034);
        $stmt7034->bind_param('sssssi', $status7034, $assignedName7034, $assignedBy7034, $description7034, $room7034, $assetId7034);

        if ($stmt7034->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7034 to $status7034.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7034->close();
    }

    //FOR ID 7035
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7035'])) {
        // Get form data
        $assetId7035 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7035 = $_POST['status']; // Get the status from the form
        $description7035 = $_POST['description']; // Get the description from the form
        $room7035 = $_POST['room']; // Get the room from the form
        $assignedBy7035 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7035 = $status7035 === 'Need Repair' ? '' : $assignedName7035;

        // Prepare SQL query to update the asset
        $sql7035 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7035 = $conn->prepare($sql7035);
        $stmt7035->bind_param('sssssi', $status7035, $assignedName7035, $assignedBy7035, $description7035, $room7035, $assetId7035);

        if ($stmt7035->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7035 to $status7035.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7035->close();
    }

    //FOR ID 7036
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7036'])) {
        // Get form data
        $assetId7036 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7036 = $_POST['status']; // Get the status from the form
        $description7036 = $_POST['description']; // Get the description from the form
        $room7036 = $_POST['room']; // Get the room from the form
        $assignedBy7036 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7036 = $status7036 === 'Need Repair' ? '' : $assignedName7036;

        // Prepare SQL query to update the asset
        $sql7036 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7036 = $conn->prepare($sql7036);
        $stmt7036->bind_param('sssssi', $status7036, $assignedName7036, $assignedBy7036, $description7036, $room7036, $assetId7036);

        if ($stmt7036->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7036 to $status7036.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7036->close();
    }

    //FOR ID 7037
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7037'])) {
        // Get form data
        $assetId7037 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7037 = $_POST['status']; // Get the status from the form
        $description7037 = $_POST['description']; // Get the description from the form
        $room7037 = $_POST['room']; // Get the room from the form
        $assignedBy7037 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7037 = $status7037 === 'Need Repair' ? '' : $assignedName7037;

        // Prepare SQL query to update the asset
        $sql7037 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7037 = $conn->prepare($sql7037);
        $stmt7037->bind_param('sssssi', $status7037, $assignedName7037, $assignedBy7037, $description7037, $room7037, $assetId7037);

        if ($stmt7037->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7037 to $status7037.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7037->close();
    }

    //FOR ID 7038
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7038'])) {
        // Get form data
        $assetId7038 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7038 = $_POST['status']; // Get the status from the form
        $description7038 = $_POST['description']; // Get the description from the form
        $room7038 = $_POST['room']; // Get the room from the form
        $assignedBy7038 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7038 = $status7038 === 'Need Repair' ? '' : $assignedName7038;

        // Prepare SQL query to update the asset
        $sql7038 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7038 = $conn->prepare($sql7038);
        $stmt7038->bind_param('sssssi', $status7038, $assignedName7038, $assignedBy7038, $description7038, $room7038, $assetId7038);

        if ($stmt7038->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7038 to $status7038.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7038->close();
    }

    //FOR ID 7039
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7039'])) {
        // Get form data
        $assetId7039 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7039 = $_POST['status']; // Get the status from the form
        $description7039 = $_POST['description']; // Get the description from the form
        $room7039 = $_POST['room']; // Get the room from the form
        $assignedBy7039 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7039 = $status7039 === 'Need Repair' ? '' : $assignedName7039;

        // Prepare SQL query to update the asset
        $sql7039 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7039 = $conn->prepare($sql7039);
        $stmt7039->bind_param('sssssi', $status7039, $assignedName7039, $assignedBy7039, $description7039, $room7039, $assetId7039);

        if ($stmt7039->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7039 to $status7039.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7039->close();
    }

    //FOR ID 7040
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7040'])) {
        // Get form data
        $assetId7040 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7040 = $_POST['status']; // Get the status from the form
        $description7040 = $_POST['description']; // Get the description from the form
        $room7040 = $_POST['room']; // Get the room from the form
        $assignedBy7040 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7040 = $status7040 === 'Need Repair' ? '' : $assignedName7040;

        // Prepare SQL query to update the asset
        $sql7040 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7040 = $conn->prepare($sql7040);
        $stmt7040->bind_param('sssssi', $status7040, $assignedName7040, $assignedBy7040, $description7040, $room7040, $assetId7040);

        if ($stmt7040->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7040 to $status7040.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7040->close();
    }

    //FOR ID 7041
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7041'])) {
        // Get form data
        $assetId7041 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7041 = $_POST['status']; // Get the status from the form
        $description7041 = $_POST['description']; // Get the description from the form
        $room7041 = $_POST['room']; // Get the room from the form
        $assignedBy7041 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7041 = $status7041 === 'Need Repair' ? '' : $assignedName7041;

        // Prepare SQL query to update the asset
        $sql7041 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7041 = $conn->prepare($sql7041);
        $stmt7041->bind_param('sssssi', $status7041, $assignedName7041, $assignedBy7041, $description7041, $room7041, $assetId7041);

        if ($stmt7041->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7041 to $status7041.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7041->close();
    }

    //FOR ID 7042
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7042'])) {
        // Get form data
        $assetId7042 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7042 = $_POST['status']; // Get the status from the form
        $description7042 = $_POST['description']; // Get the description from the form
        $room7042 = $_POST['room']; // Get the room from the form
        $assignedBy7042 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7042 = $status7042 === 'Need Repair' ? '' : $assignedName7042;

        // Prepare SQL query to update the asset
        $sql7042 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7042 = $conn->prepare($sql7042);
        $stmt7042->bind_param('sssssi', $status7042, $assignedName7042, $assignedBy7042, $description7042, $room7042, $assetId7042);

        if ($stmt7042->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7042 to $status7042.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7042->close();
    }

    //FOR ID 7043
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7043'])) {
        // Get form data
        $assetId7043 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7043 = $_POST['status']; // Get the status from the form
        $description7043 = $_POST['description']; // Get the description from the form
        $room7043 = $_POST['room']; // Get the room from the form
        $assignedBy7043 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7043 = $status7043 === 'Need Repair' ? '' : $assignedName7043;

        // Prepare SQL query to update the asset
        $sql7043 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7043 = $conn->prepare($sql7043);
        $stmt7043->bind_param('sssssi', $status7043, $assignedName7043, $assignedBy7043, $description7043, $room7043, $assetId7043);

        if ($stmt7043->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7043 to $status7043.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7043->close();
    }

    //FOR ID 7044
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7044'])) {
        // Get form data
        $assetId7044 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7044 = $_POST['status']; // Get the status from the form
        $description7044 = $_POST['description']; // Get the description from the form
        $room7044 = $_POST['room']; // Get the room from the form
        $assignedBy7044 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7044 = $status7044 === 'Need Repair' ? '' : $assignedName7044;

        // Prepare SQL query to update the asset
        $sql7044 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7044 = $conn->prepare($sql7044);
        $stmt7044->bind_param('sssssi', $status7044, $assignedName7044, $assignedBy7044, $description7044, $room7044, $assetId7044);

        if ($stmt7044->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7044 to $status7044.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7044->close();
    }

    //FOR ID 7045
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7045'])) {
        // Get form data
        $assetId7045 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7045 = $_POST['status']; // Get the status from the form
        $description7045 = $_POST['description']; // Get the description from the form
        $room7045 = $_POST['room']; // Get the room from the form
        $assignedBy7045 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7045 = $status7045 === 'Need Repair' ? '' : $assignedName7045;

        // Prepare SQL query to update the asset
        $sql7045 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7045 = $conn->prepare($sql7045);
        $stmt7045->bind_param('sssssi', $status7045, $assignedName7045, $assignedBy7045, $description7045, $room7045, $assetId7045);

        if ($stmt7045->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7045 to $status7045.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7045->close();
    }
    //FOR ID 7046
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7046'])) {
        // Get form data
        $assetId7046 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7046 = $_POST['status']; // Get the status from the form
        $description7046 = $_POST['description']; // Get the description from the form
        $room7046 = $_POST['room']; // Get the room from the form
        $assignedBy7046 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7046 = $status7046 === 'Need Repair' ? '' : $assignedName7046;

        // Prepare SQL query to update the asset
        $sql7046 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7046 = $conn->prepare($sql7046);
        $stmt7046->bind_param('sssssi', $status7046, $assignedName7046, $assignedBy7046, $description7046, $room7046, $assetId7046);

        if ($stmt7046->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7046 to $status7046.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7046->close();
    }

    //FOR ID 7047
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7047'])) {
        // Get form data
        $assetId7047 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7047 = $_POST['status']; // Get the status from the form
        $description7047 = $_POST['description']; // Get the description from the form
        $room7047 = $_POST['room']; // Get the room from the form
        $assignedBy7047 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7047 = $status7047 === 'Need Repair' ? '' : $assignedName7047;

        // Prepare SQL query to update the asset
        $sql7047 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7047 = $conn->prepare($sql7047);
        $stmt7047->bind_param('sssssi', $status7047, $assignedName7047, $assignedBy7047, $description7047, $room7047, $assetId7047);

        if ($stmt7047->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7047 to $status7047.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7047->close();
    }

    //FOR ID 7048
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7048'])) {
        // Get form data
        $assetId7048 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7048 = $_POST['status']; // Get the status from the form
        $description7048 = $_POST['description']; // Get the description from the form
        $room7048 = $_POST['room']; // Get the room from the form
        $assignedBy7048 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7048 = $status7048 === 'Need Repair' ? '' : $assignedName7048;

        // Prepare SQL query to update the asset
        $sql7048 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7048 = $conn->prepare($sql7048);
        $stmt7048->bind_param('sssssi', $status7048, $assignedName7048, $assignedBy7048, $description7048, $room7048, $assetId7048);

        if ($stmt7048->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7048 to $status7048.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7048->close();
    }

    //FOR ID 7049
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7049'])) {
        // Get form data
        $assetId7049 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7049 = $_POST['status']; // Get the status from the form
        $description7049 = $_POST['description']; // Get the description from the form
        $room7049 = $_POST['room']; // Get the room from the form
        $assignedBy7049 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7049 = $status7049 === 'Need Repair' ? '' : $assignedName7049;

        // Prepare SQL query to update the asset
        $sql7049 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7049 = $conn->prepare($sql7049);
        $stmt7049->bind_param('sssssi', $status7049, $assignedName7049, $assignedBy7049, $description7049, $room7049, $assetId7049);

        if ($stmt7049->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7049 to $status7049.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7049->close();
    }

    //FOR ID 7050
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7050'])) {
        // Get form data
        $assetId7050 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7050 = $_POST['status']; // Get the status from the form
        $description7050 = $_POST['description']; // Get the description from the form
        $room7050 = $_POST['room']; // Get the room from the form
        $assignedBy7050 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7050 = $status7050 === 'Need Repair' ? '' : $assignedName7050;

        // Prepare SQL query to update the asset
        $sql7050 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7050 = $conn->prepare($sql7050);
        $stmt7050->bind_param('sssssi', $status7050, $assignedName7050, $assignedBy7050, $description7050, $room7050, $assetId7050);

        if ($stmt7050->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7050 to $status7050.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7050->close();
    }

    //FOR ID 7051
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7051'])) {
        // Get form data
        $assetId7051 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7051 = $_POST['status']; // Get the status from the form
        $description7051 = $_POST['description']; // Get the description from the form
        $room7051 = $_POST['room']; // Get the room from the form
        $assignedBy7051 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7051 = $status7051 === 'Need Repair' ? '' : $assignedName7051;

        // Prepare SQL query to update the asset
        $sql7051 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7051 = $conn->prepare($sql7051);
        $stmt7051->bind_param('sssssi', $status7051, $assignedName7051, $assignedBy7051, $description7051, $room7051, $assetId7051);

        if ($stmt7051->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7051 to $status7051.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7051->close();
    }

    //FOR ID 7052
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7052'])) {
        // Get form data
        $assetId7052 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7052 = $_POST['status']; // Get the status from the form
        $description7052 = $_POST['description']; // Get the description from the form
        $room7052 = $_POST['room']; // Get the room from the form
        $assignedBy7052 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7052 = $status7052 === 'Need Repair' ? '' : $assignedName7052;

        // Prepare SQL query to update the asset
        $sql7052 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7052 = $conn->prepare($sql7052);
        $stmt7052->bind_param('sssssi', $status7052, $assignedName7052, $assignedBy7052, $description7052, $room7052, $assetId7052);

        if ($stmt7052->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7052 to $status7052.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7052->close();
    }


    //FOR ID 7054
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7054'])) {
        // Get form data
        $assetId7054 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7054 = $_POST['status']; // Get the status from the form
        $description7054 = $_POST['description']; // Get the description from the form
        $room7054 = $_POST['room']; // Get the room from the form
        $assignedBy7054 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7054 = $status7054 === 'Need Repair' ? '' : $assignedName7054;

        // Prepare SQL query to update the asset
        $sql7054 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7054 = $conn->prepare($sql7054);
        $stmt7054->bind_param('sssssi', $status7054, $assignedName7054, $assignedBy7054, $description7054, $room7054, $assetId7054);

        if ($stmt7054->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7054 to $status7054.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7054->close();
    }

    //FOR ID 7053
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7053'])) {
        // Get form data
        $assetId7053 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7053 = $_POST['status']; // Get the status from the form
        $description7053 = $_POST['description']; // Get the description from the form
        $room7053 = $_POST['room']; // Get the room from the form
        $assignedBy7053 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7053 = $status7053 === 'Need Repair' ? '' : $assignedName7053;

        // Prepare SQL query to update the asset
        $sql7053 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7053 = $conn->prepare($sql7053);
        $stmt7053->bind_param('sssssi', $status7053, $assignedName7053, $assignedBy7053, $description7053, $room7053, $assetId7053);

        if ($stmt7053->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7053 to $status7053.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7053->close();
    }

    //FOR ID 7055
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7055'])) {
        // Get form data
        $assetId7055 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7055 = $_POST['status']; // Get the status from the form
        $description7055 = $_POST['description']; // Get the description from the form
        $room7055 = $_POST['room']; // Get the room from the form
        $assignedBy7055 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7055 = $status7055 === 'Need Repair' ? '' : $assignedName7055;

        // Prepare SQL query to update the asset
        $sql7055 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7055 = $conn->prepare($sql7055);
        $stmt7055->bind_param('sssssi', $status7055, $assignedName7055, $assignedBy7055, $description7055, $room7055, $assetId7055);

        if ($stmt7055->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7055 to $status7055.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7055->close();
    }

    //FOR ID 7056
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7056'])) {
        // Get form data
        $assetId7056 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7056 = $_POST['status']; // Get the status from the form
        $description7056 = $_POST['description']; // Get the description from the form
        $room7056 = $_POST['room']; // Get the room from the form
        $assignedBy7056 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7056 = $status7056 === 'Need Repair' ? '' : $assignedName7056;

        // Prepare SQL query to update the asset
        $sql7056 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7056 = $conn->prepare($sql7056);
        $stmt7056->bind_param('sssssi', $status7056, $assignedName7056, $assignedBy7056, $description7056, $room7056, $assetId7056);

        if ($stmt7056->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7056 to $status7056.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7056->close();
    }

    //FOR ID 7057
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7057'])) {
        // Get form data
        $assetId7057 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7057 = $_POST['status']; // Get the status from the form
        $description7057 = $_POST['description']; // Get the description from the form
        $room7057 = $_POST['room']; // Get the room from the form
        $assignedBy7057 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7057 = $status7057 === 'Need Repair' ? '' : $assignedName7057;

        // Prepare SQL query to update the asset
        $sql7057 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7057 = $conn->prepare($sql7057);
        $stmt7057->bind_param('sssssi', $status7057, $assignedName7057, $assignedBy7057, $description7057, $room7057, $assetId7057);

        if ($stmt7057->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7057 to $status7057.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7057->close();
    }

    //FOR ID 7058
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7058'])) {
        // Get form data
        $assetId7058 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7058 = $_POST['status']; // Get the status from the form
        $description7058 = $_POST['description']; // Get the description from the form
        $room7058 = $_POST['room']; // Get the room from the form
        $assignedBy7058 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7058 = $status7058 === 'Need Repair' ? '' : $assignedName7058;

        // Prepare SQL query to update the asset
        $sql7058 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7058 = $conn->prepare($sql7058);
        $stmt7058->bind_param('sssssi', $status7058, $assignedName7058, $assignedBy7058, $description7058, $room7058, $assetId7058);

        if ($stmt7058->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7058 to $status7058.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7058->close();
    }

    //FOR ID 7059
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7059'])) {
        // Get form data
        $assetId7059 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7059 = $_POST['status']; // Get the status from the form
        $description7059 = $_POST['description']; // Get the description from the form
        $room7059 = $_POST['room']; // Get the room from the form
        $assignedBy7059 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7059 = $status7059 === 'Need Repair' ? '' : $assignedName7059;

        // Prepare SQL query to update the asset
        $sql7059 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7059 = $conn->prepare($sql7059);
        $stmt7059->bind_param('sssssi', $status7059, $assignedName7059, $assignedBy7059, $description7059, $room7059, $assetId7059);

        if ($stmt7059->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7059 to $status7059.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7059->close();
    }
    //FOR ID 7060
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7060'])) {
        // Get form data
        $assetId7060 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7060 = $_POST['status']; // Get the status from the form
        $description7060 = $_POST['description']; // Get the description from the form
        $room7060 = $_POST['room']; // Get the room from the form
        $assignedBy7060 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7060 = $status7060 === 'Need Repair' ? '' : $assignedName7060;

        // Prepare SQL query to update the asset
        $sql7060 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7060 = $conn->prepare($sql7060);
        $stmt7060->bind_param('sssssi', $status7060, $assignedName7060, $assignedBy7060, $description7060, $room7060, $assetId7060);

        if ($stmt7060->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7060 to $status7060.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7060->close();
    }

    //FOR ID 7061
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7061'])) {
        // Get form data
        $assetId7061 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7061 = $_POST['status']; // Get the status from the form
        $description7061 = $_POST['description']; // Get the description from the form
        $room7061 = $_POST['room']; // Get the room from the form
        $assignedBy7061 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7061 = $status7061 === 'Need Repair' ? '' : $assignedName7061;

        // Prepare SQL query to update the asset
        $sql7061 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7061 = $conn->prepare($sql7061);
        $stmt7061->bind_param('sssssi', $status7061, $assignedName7061, $assignedBy7061, $description7061, $room7061, $assetId7061);

        if ($stmt7061->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7061 to $status7061.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7061->close();
    }

    //FOR ID 7062
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7062'])) {
        // Get form data
        $assetId7062 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7062 = $_POST['status']; // Get the status from the form
        $description7062 = $_POST['description']; // Get the description from the form
        $room7062 = $_POST['room']; // Get the room from the form
        $assignedBy7062 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7062 = $status7062 === 'Need Repair' ? '' : $assignedName7062;

        // Prepare SQL query to update the asset
        $sql7062 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7062 = $conn->prepare($sql7062);
        $stmt7062->bind_param('sssssi', $status7062, $assignedName7062, $assignedBy7062, $description7062, $room7062, $assetId7062);

        if ($stmt7062->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7062 to $status7062.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7062->close();
    }

    //FOR ID 7063
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7063'])) {
        // Get form data
        $assetId7063 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7063 = $_POST['status']; // Get the status from the form
        $description7063 = $_POST['description']; // Get the description from the form
        $room7063 = $_POST['room']; // Get the room from the form
        $assignedBy7063 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7063 = $status7063 === 'Need Repair' ? '' : $assignedName7063;

        // Prepare SQL query to update the asset
        $sql7063 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7063 = $conn->prepare($sql7063);
        $stmt7063->bind_param('sssssi', $status7063, $assignedName7063, $assignedBy7063, $description7063, $room7063, $assetId7063);

        if ($stmt7063->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7063 to $status7063.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7063->close();
    }

    //FOR ID 7065
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7065'])) {
        // Get form data
        $assetId7065 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7065 = $_POST['status']; // Get the status from the form
        $description7065 = $_POST['description']; // Get the description from the form
        $room7065 = $_POST['room']; // Get the room from the form
        $assignedBy7065 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7065 = $status7065 === 'Need Repair' ? '' : $assignedName7065;

        // Prepare SQL query to update the asset
        $sql7065 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7065 = $conn->prepare($sql7065);
        $stmt7065->bind_param('sssssi', $status7065, $assignedName7065, $assignedBy7065, $description7065, $room7065, $assetId7065);

        if ($stmt7065->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7065 to $status7065.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7065->close();
    }

    //FOR ID 7064
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7064'])) {
        // Get form data
        $assetId7064 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7064 = $_POST['status']; // Get the status from the form
        $description7064 = $_POST['description']; // Get the description from the form
        $room7064 = $_POST['room']; // Get the room from the form
        $assignedBy7064 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7064 = $status7064 === 'Need Repair' ? '' : $assignedName7064;

        // Prepare SQL query to update the asset
        $sql7064 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7064 = $conn->prepare($sql7064);
        $stmt7064->bind_param('sssssi', $status7064, $assignedName7064, $assignedBy7064, $description7064, $room7064, $assetId7064);

        if ($stmt7064->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7064 to $status7064.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7064->close();
    }

    //FOR ID 7066
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7066'])) {
        // Get form data
        $assetId7066 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7066 = $_POST['status']; // Get the status from the form
        $description7066 = $_POST['description']; // Get the description from the form
        $room7066 = $_POST['room']; // Get the room from the form
        $assignedBy7066 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7066 = $status7066 === 'Need Repair' ? '' : $assignedName7066;

        // Prepare SQL query to update the asset
        $sql7066 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7066 = $conn->prepare($sql7066);
        $stmt7066->bind_param('sssssi', $status7066, $assignedName7066, $assignedBy7066, $description7066, $room7066, $assetId7066);

        if ($stmt7066->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7066 to $status7066.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7066->close();
    }

    //FOR ID 7067
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7067'])) {
        // Get form data
        $assetId7067 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7067 = $_POST['status']; // Get the status from the form
        $description7067 = $_POST['description']; // Get the description from the form
        $room7067 = $_POST['room']; // Get the room from the form
        $assignedBy7067 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7067 = $status7067 === 'Need Repair' ? '' : $assignedName7067;

        // Prepare SQL query to update the asset
        $sql7067 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7067 = $conn->prepare($sql7067);
        $stmt7067->bind_param('sssssi', $status7067, $assignedName7067, $assignedBy7067, $description7067, $room7067, $assetId7067);

        if ($stmt7067->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7067 to $status7067.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7067->close();
    }

    //FOR ID 7068
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7068'])) {
        // Get form data
        $assetId7068 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7068 = $_POST['status']; // Get the status from the form
        $description7068 = $_POST['description']; // Get the description from the form
        $room7068 = $_POST['room']; // Get the room from the form
        $assignedBy7068 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7068 = $status7068 === 'Need Repair' ? '' : $assignedName7068;

        // Prepare SQL query to update the asset
        $sql7068 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7068 = $conn->prepare($sql7068);
        $stmt7068->bind_param('sssssi', $status7068, $assignedName7068, $assignedBy7068, $description7068, $room7068, $assetId7068);

        if ($stmt7068->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7068 to $status7068.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7068->close();
    }

    //FOR ID 7069
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7069'])) {
        // Get form data
        $assetId7069 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7069 = $_POST['status']; // Get the status from the form
        $description7069 = $_POST['description']; // Get the description from the form
        $room7069 = $_POST['room']; // Get the room from the form
        $assignedBy7069 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7069 = $status7069 === 'Need Repair' ? '' : $assignedName7069;

        // Prepare SQL query to update the asset
        $sql7069 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7069 = $conn->prepare($sql7069);
        $stmt7069->bind_param('sssssi', $status7069, $assignedName7069, $assignedBy7069, $description7069, $room7069, $assetId7069);

        if ($stmt7069->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7069 to $status7069.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7069->close();
    }

    //FOR ID 7070
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7070'])) {
        // Get form data
        $assetId7070 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7070 = $_POST['status']; // Get the status from the form
        $description7070 = $_POST['description']; // Get the description from the form
        $room7070 = $_POST['room']; // Get the room from the form
        $assignedBy7070 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7070 = $status7070 === 'Need Repair' ? '' : $assignedName7070;

        // Prepare SQL query to update the asset
        $sql7070 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7070 = $conn->prepare($sql7070);
        $stmt7070->bind_param('sssssi', $status7070, $assignedName7070, $assignedBy7070, $description7070, $room7070, $assetId7070);

        if ($stmt7070->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7070 to $status7070.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7070->close();
    }

    //FOR ID 7071
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7071'])) {
        // Get form data
        $assetId7071 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7071 = $_POST['status']; // Get the status from the form
        $description7071 = $_POST['description']; // Get the description from the form
        $room7071 = $_POST['room']; // Get the room from the form
        $assignedBy7071 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7071 = $status7071 === 'Need Repair' ? '' : $assignedName7071;

        // Prepare SQL query to update the asset
        $sql7071 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7071 = $conn->prepare($sql7071);
        $stmt7071->bind_param('sssssi', $status7071, $assignedName7071, $assignedBy7071, $description7071, $room7071, $assetId7071);

        if ($stmt7071->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7071 to $status7071.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7071->close();
    }

    //FOR ID 7072
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7072'])) {
        // Get form data
        $assetId7072 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7072 = $_POST['status']; // Get the status from the form
        $description7072 = $_POST['description']; // Get the description from the form
        $room7072 = $_POST['room']; // Get the room from the form
        $assignedBy7072 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7072 = $status7072 === 'Need Repair' ? '' : $assignedName7072;

        // Prepare SQL query to update the asset
        $sql7072 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7072 = $conn->prepare($sql7072);
        $stmt7072->bind_param('sssssi', $status7072, $assignedName7072, $assignedBy7072, $description7072, $room7072, $assetId7072);

        if ($stmt7072->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7072 to $status7072.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7072->close();
    }
    //FOR ID 7073
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7073'])) {
        // Get form data
        $assetId7073 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7073 = $_POST['status']; // Get the status from the form
        $description7073 = $_POST['description']; // Get the description from the form
        $room7073 = $_POST['room']; // Get the room from the form
        $assignedBy7073 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7073 = $status7073 === 'Need Repair' ? '' : $assignedName7073;

        // Prepare SQL query to update the asset
        $sql7073 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7073 = $conn->prepare($sql7073);
        $stmt7073->bind_param('sssssi', $status7073, $assignedName7073, $assignedBy7073, $description7073, $room7073, $assetId7073);

        if ($stmt7073->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7073 to $status7073.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7073->close();
    }

    //FOR ID 7074
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7074'])) {
        // Get form data
        $assetId7074 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7074 = $_POST['status']; // Get the status from the form
        $description7074 = $_POST['description']; // Get the description from the form
        $room7074 = $_POST['room']; // Get the room from the form
        $assignedBy7074 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7074 = $status7074 === 'Need Repair' ? '' : $assignedName7074;

        // Prepare SQL query to update the asset
        $sql7074 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7074 = $conn->prepare($sql7074);
        $stmt7074->bind_param('sssssi', $status7074, $assignedName7074, $assignedBy7074, $description7074, $room7074, $assetId7074);

        if ($stmt7074->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7074 to $status7074.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7074->close();
    }

    //FOR ID 7075
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7075'])) {
        // Get form data
        $assetId7075 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7075 = $_POST['status']; // Get the status from the form
        $description7075 = $_POST['description']; // Get the description from the form
        $room7075 = $_POST['room']; // Get the room from the form
        $assignedBy7075 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7075 = $status7075 === 'Need Repair' ? '' : $assignedName7075;

        // Prepare SQL query to update the asset
        $sql7075 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7075 = $conn->prepare($sql7075);
        $stmt7075->bind_param('sssssi', $status7075, $assignedName7075, $assignedBy7075, $description7075, $room7075, $assetId7075);

        if ($stmt7075->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7075 to $status7075.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: KOBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7075->close();
    }

    //FOR ID 7076
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7076'])) {
        // Get form data
        $assetId7076 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7076 = $_POST['status']; // Get the status from the form
        $description7076 = $_POST['description']; // Get the description from the form
        $room7076 = $_POST['room']; // Get the room from the form
        $assignedBy7076 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7076 = $status7076 === 'Need Repair' ? '' : $assignedName7076;

        // Prepare SQL query to update the asset
        $sql7076 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7076 = $conn->prepare($sql7076);
        $stmt7076->bind_param('sssssi', $status7076, $assignedName7076, $assignedBy7076, $description7076, $room7076, $assetId7076);

        if ($stmt7076->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7076 to $status7076.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7076->close();
    }

    //FOR ID 7077
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7077'])) {
        // Get form data
        $assetId7077 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7077 = $_POST['status']; // Get the status from the form
        $description7077 = $_POST['description']; // Get the description from the form
        $room7077 = $_POST['room']; // Get the room from the form
        $assignedBy7077 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7077 = $status7077 === 'Need Repair' ? '' : $assignedName7077;

        // Prepare SQL query to update the asset
        $sql7077 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7077 = $conn->prepare($sql7077);
        $stmt7077->bind_param('sssssi', $status7077, $assignedName7077, $assignedBy7077, $description7077, $room7077, $assetId7077);

        if ($stmt7077->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7077 to $status7077.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7077->close();
    }

    //FOR ID 7078
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7078'])) {
        // Get form data
        $assetId7078 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7078 = $_POST['status']; // Get the status from the form
        $description7078 = $_POST['description']; // Get the description from the form
        $room7078 = $_POST['room']; // Get the room from the form
        $assignedBy7078 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7078 = $status7078 === 'Need Repair' ? '' : $assignedName7078;

        // Prepare SQL query to update the asset
        $sql7078 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7078 = $conn->prepare($sql7078);
        $stmt7078->bind_param('sssssi', $status7078, $assignedName7078, $assignedBy7078, $description7078, $room7078, $assetId7078);

        if ($stmt7078->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7078 to $status7078.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7078->close();
    }

    //FOR ID 7079
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit7079'])) {
        // Get form data
        $assetId7079 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status7079 = $_POST['status']; // Get the status from the form
        $description7079 = $_POST['description']; // Get the description from the form
        $room7079 = $_POST['room']; // Get the room from the form
        $assignedBy7079 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName7079 = $status7079 === 'Need Repair' ? '' : $assignedName7079;

        // Prepare SQL query to update the asset
        $sql7079 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt7079 = $conn->prepare($sql7079);
        $stmt7079->bind_param('sssssi', $status7079, $assignedName7079, $assignedBy7079, $description7079, $room7079, $assetId7079);

        if ($stmt7079->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId7079 to $status7079.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: BABFB.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt7079->close();
    }


    function getStatusColor($status)
    {
        switch ($status) {
            case 'Working':
                return 'green';
            case 'Under Maintenance':
                return 'yellow';
            case 'Need Repair':
                return 'red';
            case 'For Replacement':
                return 'blue';
            default:
                return 'grey'; // Default color
        }
    }

    //FOR IMAGE UPLOAD BASED ON ASSET ID
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_img']) && isset($_POST['assetId'])) {
        // Check for upload errors
        if ($_FILES['upload_img']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['upload_img']['tmp_name'])) {
            $image = $_FILES['upload_img']['tmp_name'];
            $imgContent = file_get_contents($image); // Get the content of the file

            // Get the asset ID from the form
            $assetId = $_POST['assetId'];

            // Prepare SQL query to update the asset with the image based on asset ID
            $sql = "UPDATE asset SET upload_img = ? WHERE assetId = ?";
            $stmt = $conn->prepare($sql);

            // Null for blob data
            $null = NULL;
            $stmt->bind_param('bi', $null, $assetId);
            // Send blob data in packets
            $stmt->send_long_data(0, $imgContent);

            if ($stmt->execute()) {
                echo "<script>alert('Asset and image updated successfully!');</script>";
                header("Location: BABFB.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload image. Error: " . $_FILES['upload_img']['error'] . "');</script>";
        }
    }
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Map</title>
        <link rel="icon" type="image/x-icon" href="../../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../../src/js/locationTracker.js"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/BEB/BEBF1.css" />
        <link rel="stylesheet" href="../../../src/css/map.css" />
        <link rel="stylesheet" href="../../../src/css/map-container.css" />
    </head>
    <style>
        .notification-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: red;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>

    <body>
        <div id="navbar" class="">
            <nav>
                <div class="hamburger">
                    <i class="bi bi-list"></i>
                    <a href="#" class="brand" title="logo">
                    </a>
                </div>
                <div class="content-nav">
                    <div class="notification-dropdown">

                        <a href="#" class="notification" id="notification-button">
                            <i class="fa fa-bell" aria-hidden="true"></i>
                            <!-- Notification Indicator Dot -->
                            <?php if ($unseenCount > 0): ?>
                                <span class="notification-indicator"></span>
                            <?php endif; ?>
                        </a>

                        <div class="dropdown-content" id="notification-dropdown-content">
                            <h6 class="dropdown-header">Alerts Center</h6>
                            <!-- PHP code to display notifications will go here -->
                            <?php
                            if ($resultLatestLogs && $resultLatestLogs->num_rows > 0) {
                                while ($row = $resultLatestLogs->fetch_assoc()) {
                                    $adminName = $row["adminFirstName"] . ' ' . $row["adminLastName"];
                                    $adminRole = $row["adminRole"]; // This should be the role such as 'Manager' or 'Personnel'
                                    $actionText = $row["action"];

                                    // Initialize the notification text as empty
                                    $notificationText = "";
                                    if (strpos($actionText, $adminRole) === false) {
                                        // Role is not in the action text, so prepend it to the admin name
                                        $adminName = "$adminRole $adminName";
                                    }
                                    // Check for 'Assigned maintenance personnel' action
                                    if (preg_match('/Assigned maintenance personnel (.*?) to asset ID (\d+)/', $actionText, $matches)) {
                                        $assignedName = $matches[1];
                                        $assetId = $matches[2];
                                        $notificationText = "assigned $assignedName to asset ID $assetId";
                                    }
                                    // Check for 'Changed status of asset ID' action
                                    elseif (preg_match('/Changed status of asset ID (\d+) to (.+)/', $actionText, $matches)) {
                                        $assetId = $matches[1];
                                        $newStatus = $matches[2];
                                        $notificationText = "changed status of asset ID $assetId to $newStatus";
                                    }

                                    // If notification text is set, echo the notification
                                    if (!empty($notificationText)) {
                                        // HTML for notification item
                                        echo '<a href="#" class="notification-item" data-activity-id="' . $row["activityId"] . '">' . htmlspecialchars("$adminName $notificationText") . '</a>';
                                    }
                                }
                            } else {
                                // No notifications found
                                echo '<a href="#">No new notifications</a>';
                            }
                            ?>
                            <a href="activity-logs.php" class="view-all">View All</a>

                        </div>
                    </div>

                    <a href="#" class="settings profile">
                        <div class="profile-container" title="settings">
                            <div class="profile-img">
                                <?php
                                if ($conn->connect_error) {
                                    die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
                                }

                                $userId = $_SESSION['accountId'];
                                $query = "SELECT picture FROM account WHERE accountId = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('i', $userId);
                                $stmt->execute();
                                $stmt->store_result();

                                if ($stmt->num_rows > 0) {
                                    $stmt->bind_result($userPicture);
                                    $stmt->fetch();

                                    echo "<img src='data:image/jpeg;base64," . base64_encode($userPicture) . "' title='profile-picture' />";
                                } else {
                                    echo $_SESSION['firstName'];
                                }

                                $stmt->close();
                                ?>
                            </div>
                            <div class="profile-name-container " id="desktop">
                                <div><a class="profile-name">
                                        <?php echo $_SESSION['firstName']; ?>
                                    </a></div>
                                <div><a class="profile-role">
                                        <?php echo $_SESSION['role']; ?>
                                    </a></div>
                            </div>
                        </div>
                    </a>

                    <div id="settings-dropdown" class="dropdown-content1">
                        <div class="profile-name-container" id="mobile">
                            <div><a class="profile-name">
                                    <?php echo $_SESSION['firstName']; ?>
                                </a></div>
                            <div><a class="profile-role">
                                    <?php echo $_SESSION['role']; ?>
                                </a></div>
                            <hr>
                        </div>
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><i
                                class="bi bi-person profile-icons"></i>Profile</a>
                        <a class="profile-hover" href="#" id="logoutBtn"><i class="bi bi-box-arrow-left "></i>Logout</a>
                    </div>
                    <?php
} else {
    header("Location:../../index.php");
    exit();
}
?>
            </div>
        </nav>
    </div>
        <section id="sidebar">
            <div href="#" class="brand" title="logo">
                <i><img src="../../../src/img/UpKeep.png" alt="" class="logo" /></i>
                <div class="mobile-sidebar-close">
                    <i class="bi bi-arrow-left-circle"></i>
                </div>
            </div>
            <ul class="side-menu top">
                <li>
                    <a href="../../manager/dashboard.php">
                        <i class="bi bi-grid"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../../manager/attendance-logs.php">
                        <i class="bi bi-calendar-week"></i>
                        <span class="text">Attendance Logs</span>
                    </a>
                </li>
                <div class="GPS-cont" onclick="toggleGPS()">
                    <li class="GPS-dropdown">
                        <div class="GPS-drondown-content">
                            <div class="GPS-side-cont">
                                <i class="bi bi-geo-alt"></i>
                                <span class="text">GPS</span>
                            </div>
                            <div class="GPS-ind">
                                <i id="chevron-icon" class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                    </li>
                </div>
                <div class="GPS-container">
                    <li class="GPS-Tracker">
                        <a href="../../manager/gps.php">
                            <i class="bi bi-crosshair"></i>
                            <span class="text">GPS Tracker</span>
                        </a>
                    </li>
                    <li class="GPS-History">
                        <a href="../../manager/gps-history.php">
                            <i class="bi bi-radar"></i>
                            <span class="text">GPS History</span>
                        </a>
                    </li>
                </div>
                <li class="active">
                    <a href="../../manager/map.php">
                        <i class="bi bi-map"></i>
                        <span class="text">Map</span>
                    </a>
                </li>
                <li>
                    <a href="../../manager/reports.php">
                        <i class="bi bi-clipboard"></i>
                        <span class="text">Reports</span>
                    </a>
                </li>
                <li>
                    <a href="../../manager/activity-logs.php">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="text">Activity Logs</span>
                    </a>
                </li>
            </ul>
        </section>
    <div id="map-top-nav">
        <a href="../../manager/map.php" class="closeFloor"><i class="bi bi-box-arrow-left"></i></i></a>

        <div class="legend-button" id="legendButton">
            <i class="bi bi-info-circle"></i>
        </div>
    </div>
    <section id="content">
        <main>
            <div class="content-container" id="content-container">
                <div id="belmonte-F1" class="content">
                        <!-- FLOOR PLAN -->
                        <img src="../../../src/floors/bautistaB/BB3F.png" alt="" class="Floor-container">

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/AC.jpg" alt="" class="legend-img">
                                <p>AIRCON</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/CHAIR.jpg" alt="" class="legend-img">
                                <p>CHAIR</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/B-TABLE.jpg" alt="" class="legend-img">
                                <p>TABLE</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/TOILET-SEAT.jpg" alt="" class="legend-img">
                                <p>TOILET-SEAT</p>
                            </div>
                        </div>

                        <div class="map-nav">
                            <div class="map-legend">
                                <div class="legend-item" data-status="Working">
                                    <div class="legend-color-green"></div>
                                    <button class="legend-toggle">Working</button>
                                </div>
                                <div class="legend-item" data-status="Under Maintenance">
                                    <div class="legend-color-under-maintenance"></div>
                                    <button class="legend-toggle">Under maintenance</button>
                                </div>
                                <div class="legend-item" data-status="Need Repair">
                                    <div class="legend-color-need-repair"></div>
                                    <button class="legend-toggle">Need repair</button>
                                </div>
                                <div class="legend-item" data-status="For Replacement">
                                    <div class="legend-color-for-replacement"></div>
                                    <button class="legend-toggle">For replacement</button>
                                </div>
                            </div>
                        </div>
                        <!-- ASSETS -->

                        <!-- ASSET 12107 -->
                        <img src='../image.php?id=12107' style='width:15px; cursor:pointer; position:absolute; top:180px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12107' onclick='fetchAssetData(12107);' class="asset-image" data-id="<?php echo $assetId12107; ?>" data-room="<?php echo htmlspecialchars($room12107); ?>" data-floor="<?php echo htmlspecialchars($floor12107); ?>" data-image="<?php echo base64_encode($upload_img12107); ?>" data-status="<?php echo htmlspecialchars($status12107); ?>" data-category="<?php echo htmlspecialchars($category12107); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12107); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12107); ?>; position:absolute; top:175px; left:675px;'>
                        </div>

                        <!-- ASSET 12108 -->
                        <img src='../image.php?id=12108' style='width:15px; cursor:pointer; position:absolute; top:180px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12108' onclick='fetchAssetData(12108);' class="asset-image" data-id="<?php echo $assetId12108; ?>" data-room="<?php echo htmlspecialchars($room12108); ?>" data-floor="<?php echo htmlspecialchars($floor12108); ?>" data-image="<?php echo base64_encode($upload_img12108); ?>" data-status="<?php echo htmlspecialchars($status12108); ?>" data-category="<?php echo htmlspecialchars($category12108); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12108); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12108); ?>; position:absolute; top:175px; left:760px;'>
                        </div>

                        <!-- ASSET 12109 -->
                        <img src='../image.php?id=12109' style='width:15px; cursor:pointer; position:absolute; top:230px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12109' onclick='fetchAssetData(12109);' class="asset-image" data-id="<?php echo $assetId12109; ?>" data-room="<?php echo htmlspecialchars($room12109); ?>" data-floor="<?php echo htmlspecialchars($floor12109); ?>" data-image="<?php echo base64_encode($upload_img12109); ?>" data-status="<?php echo htmlspecialchars($status12109); ?>" data-category="<?php echo htmlspecialchars($category12109); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12109); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12109); ?>; position:absolute; top:225px; left:675px;'>
                        </div>

                        <!-- ASSET 12110 -->
                        <img src='../image.php?id=12110' style='width:15px; cursor:pointer; position:absolute; top:230px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12110' onclick='fetchAssetData(12110);' class="asset-image" data-id="<?php echo $assetId12110; ?>" data-room="<?php echo htmlspecialchars($room12110); ?>" data-floor="<?php echo htmlspecialchars($floor12110); ?>" data-image="<?php echo base64_encode($upload_img12110); ?>" data-status="<?php echo htmlspecialchars($status12110); ?>" data-category="<?php echo htmlspecialchars($category12110); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12110); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12110); ?>; position:absolute; top:225px; left:760px;'>
                        </div>

                        <!-- ASSET 12111 -->
                        <img src='../image.php?id=12111' style='width:15px; cursor:pointer; position:absolute; top:200px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12111' onclick='fetchAssetData(12111);' class="asset-image" data-id="<?php echo $assetId12111; ?>" data-room="<?php echo htmlspecialchars($room12111); ?>" data-floor="<?php echo htmlspecialchars($floor12111); ?>" data-image="<?php echo base64_encode($upload_img12111); ?>" data-status="<?php echo htmlspecialchars($status12111); ?>" data-category="<?php echo htmlspecialchars($category12111); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12111); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12111); ?>; position:absolute; top:195px; left:760px;'>
                        </div>

                        <!-- ASSET 11858 -->
                        <img src='../image.php?id=11858' style='width:15px; cursor:pointer; position:absolute; top:415px; left:510px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11858' onclick='fetchAssetData(11858);' class="asset-image" data-id="<?php echo $assetId11858; ?>" data-room="<?php echo htmlspecialchars($room11858); ?>" data-floor="<?php echo htmlspecialchars($floor11858); ?>" data-image="<?php echo base64_encode($upload_img11858); ?>" data-status="<?php echo htmlspecialchars($status11858); ?>" data-category="<?php echo htmlspecialchars($category11858); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11858); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11858); ?>; position:absolute; top:410px; left:520px;'>
                        </div>

                        <!-- ASSET 11859 -->
                        <img src='../image.php?id=11859' style='width:15px; cursor:pointer; position:absolute; top:415px; left:565px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11859' onclick='fetchAssetData(11859);' class="asset-image" data-id="<?php echo $assetId11859; ?>" data-room="<?php echo htmlspecialchars($room11859); ?>" data-floor="<?php echo htmlspecialchars($floor11859); ?>" data-image="<?php echo base64_encode($upload_img11859); ?>" data-status="<?php echo htmlspecialchars($status11859); ?>" data-category="<?php echo htmlspecialchars($category11859); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11859); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11859); ?>; position:absolute; top:410px; left:575px;'>
                        </div>

                        <!-- ASSET 11860 -->
                        <img src='../image.php?id=11860' style='width:15px; cursor:pointer; position:absolute; top:415px; left:625px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11860' onclick='fetchAssetData(11860);' class="asset-image" data-id="<?php echo $assetId11860; ?>" data-room="<?php echo htmlspecialchars($room11860); ?>" data-floor="<?php echo htmlspecialchars($floor11860); ?>" data-image="<?php echo base64_encode($upload_img11860); ?>" data-status="<?php echo htmlspecialchars($status11860); ?>" data-category="<?php echo htmlspecialchars($category11860); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11860); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11860); ?>; position:absolute; top:410px; left:635px;'>
                        </div>

                        <!-- ASSET 11861 -->
                        <img src='../image.php?id=11861' style='width:15px; cursor:pointer; position:absolute; top:490px; left:510px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11861' onclick='fetchAssetData(11861);' class="asset-image" data-id="<?php echo $assetId11861; ?>" data-room="<?php echo htmlspecialchars($room11861); ?>" data-floor="<?php echo htmlspecialchars($floor11861); ?>" data-image="<?php echo base64_encode($upload_img11861); ?>" data-status="<?php echo htmlspecialchars($status11861); ?>" data-category="<?php echo htmlspecialchars($category11861); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11861); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11861); ?>; position:absolute; top:485px; left:520px;'>
                        </div>

                        <!-- ASSET 11862 -->
                        <img src='../image.php?id=11862' style='width:15px; cursor:pointer; position:absolute; top:490px; left:565px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11862' onclick='fetchAssetData(11862);' class="asset-image" data-id="<?php echo $assetId11862; ?>" data-room="<?php echo htmlspecialchars($room11862); ?>" data-floor="<?php echo htmlspecialchars($floor11862); ?>" data-image="<?php echo base64_encode($upload_img11862); ?>" data-status="<?php echo htmlspecialchars($status11862); ?>" data-category="<?php echo htmlspecialchars($category11862); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11862); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11862); ?>; position:absolute; top:485px; left:575px;'>
                        </div>

                        <!-- ASSET 11863 -->
                        <img src='../image.php?id=11863' style='width:15px; cursor:pointer; position:absolute; top:490px; left:625px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11863' onclick='fetchAssetData(11863);' class="asset-image" data-id="<?php echo $assetId11863; ?>" data-room="<?php echo htmlspecialchars($room11863); ?>" data-floor="<?php echo htmlspecialchars($floor11863); ?>" data-image="<?php echo base64_encode($upload_img11863); ?>" data-status="<?php echo htmlspecialchars($status11863); ?>" data-category="<?php echo htmlspecialchars($category11863); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11863); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11863); ?>; position:absolute; top:485px; left:635px;'>
                        </div>

                        <!-- ASSET 11864 -->
                        <img src='../image.php?id=11864' style='width:15px; cursor:pointer; position:absolute; top:180px; left:645px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11864' onclick='fetchAssetData(11864);' class="asset-image" data-id="<?php echo $assetId11864; ?>" data-room="<?php echo htmlspecialchars($room11864); ?>" data-floor="<?php echo htmlspecialchars($floor11864); ?>" data-image="<?php echo base64_encode($upload_img11864); ?>" data-status="<?php echo htmlspecialchars($status11864); ?>" data-category="<?php echo htmlspecialchars($category11864); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11864); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11864); ?>; position:absolute; top:175px; left:655px;'>
                        </div>

                        <!-- ASSET 11865 -->
                        <img src='../image.php?id=11865' style='width:15px; cursor:pointer; position:absolute; top:400px; left:510px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11865' onclick='fetchAssetData(11865);' class="asset-image" data-id="<?php echo $assetId11865; ?>" data-room="<?php echo htmlspecialchars($room11865); ?>" data-floor="<?php echo htmlspecialchars($floor11865); ?>" data-image="<?php echo base64_encode($upload_img11865); ?>" data-status="<?php echo htmlspecialchars($status11865); ?>" data-category="<?php echo htmlspecialchars($category11865); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11865); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11865); ?>; position:absolute; top:390px; left:520px;'>
                        </div>

                        <!-- ASSET 11866 -->
                        <img src='../image.php?id=11866' style='width:15px; cursor:pointer; position:absolute; top:450px; left:625px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11866' onclick='fetchAssetData(11866);' class="asset-image" data-id="<?php echo $assetId11866; ?>" data-room="<?php echo htmlspecialchars($room11866); ?>" data-floor="<?php echo htmlspecialchars($floor11866); ?>" data-image="<?php echo base64_encode($upload_img11866); ?>" data-status="<?php echo htmlspecialchars($status11866); ?>" data-category="<?php echo htmlspecialchars($category11866); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11866); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11866); ?>; position:absolute; top:440px; left:635px;'>
                        </div>

                        <!-- ASSET 11867 -->
                        <img src='../image.php?id=11867' style='width:15px; cursor:pointer; position:absolute; top:415px; left:165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11867' onclick='fetchAssetData(11867);' class="asset-image" data-id="<?php echo $assetId11867; ?>" data-room="<?php echo htmlspecialchars($room11867); ?>" data-floor="<?php echo htmlspecialchars($floor11867); ?>" data-image="<?php echo base64_encode($upload_img11867); ?>" data-status="<?php echo htmlspecialchars($status11867); ?>" data-category="<?php echo htmlspecialchars($category11867); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11867); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11867); ?>; position:absolute; top:410px; left:175px;'>
                        </div>

                        <!-- ASSET 11868 -->
                        <img src='../image.php?id=11868' style='width:15px; cursor:pointer; position:absolute; top:415px; left:265px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11868' onclick='fetchAssetData(11868);' class="asset-image" data-id="<?php echo $assetId11868; ?>" data-room="<?php echo htmlspecialchars($room11868); ?>" data-floor="<?php echo htmlspecialchars($floor11868); ?>" data-image="<?php echo base64_encode($upload_img11868); ?>" data-status="<?php echo htmlspecialchars($status11868); ?>" data-category="<?php echo htmlspecialchars($category11868); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11868); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11868); ?>; position:absolute; top:410px; left:275px;'>
                        </div>

                        <!-- ASSET 11869 -->
                        <img src='../image.php?id=11869' style='width:15px; cursor:pointer; position:absolute; top:485px; left:165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11869' onclick='fetchAssetData(11869);' class="asset-image" data-id="<?php echo $assetId11869; ?>" data-room="<?php echo htmlspecialchars($room11869); ?>" data-floor="<?php echo htmlspecialchars($floor11869); ?>" data-image="<?php echo base64_encode($upload_img11869); ?>" data-status="<?php echo htmlspecialchars($status11869); ?>" data-category="<?php echo htmlspecialchars($category11869); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11869); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11869); ?>; position:absolute; top:480px; left:175px;'>
                        </div>

                        <!-- ASSET 11870 -->
                        <img src='../image.php?id=11870' style='width:15px; cursor:pointer; position:absolute; top:485px; left:265px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11870' onclick='fetchAssetData(11870);' class="asset-image" data-id="<?php echo $assetId11870; ?>" data-room="<?php echo htmlspecialchars($room11870); ?>" data-floor="<?php echo htmlspecialchars($floor11870); ?>" data-image="<?php echo base64_encode($upload_img11870); ?>" data-status="<?php echo htmlspecialchars($status11870); ?>" data-category="<?php echo htmlspecialchars($category11870); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11870); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11870); ?>; position:absolute; top:480px; left:275px;'>
                        </div>

                        <!-- ASSET 11871 -->
                        <img src='../image.php?id=11871' style='width:15px; cursor:pointer; position:absolute; top:445px; left:165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11871' onclick='fetchAssetData(11871);' class="asset-image" data-id="<?php echo $assetId11871; ?>" data-room="<?php echo htmlspecialchars($room11871); ?>" data-floor="<?php echo htmlspecialchars($floor11871); ?>" data-image="<?php echo base64_encode($upload_img11871); ?>" data-status="<?php echo htmlspecialchars($status11871); ?>" data-category="<?php echo htmlspecialchars($category11871); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11871); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11871); ?>; position:absolute; top:440px; left:175px;'>
                        </div>

                        <!-- ASSET 11872 -->
                        <img src='../image.php?id=11872' style='width:15px; cursor:pointer; position:absolute; top:400px; left:265px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11872' onclick='fetchAssetData(11872);' class="asset-image" data-id="<?php echo $assetId11872; ?>" data-room="<?php echo htmlspecialchars($room11872); ?>" data-floor="<?php echo htmlspecialchars($floor11872); ?>" data-image="<?php echo base64_encode($upload_img11872); ?>" data-status="<?php echo htmlspecialchars($status11872); ?>" data-category="<?php echo htmlspecialchars($category11872); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11872); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11872); ?>; position:absolute; top:395px; left:275px;'>
                        </div>

                        <!-- ASSET 11873 -->
                        <img src='../image.php?id=11873' style='width:15px; cursor:pointer; position:absolute; top:110px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11873' onclick='fetchAssetData(11873);' class="asset-image" data-id="<?php echo $assetId11873; ?>" data-room="<?php echo htmlspecialchars($room11873); ?>" data-floor="<?php echo htmlspecialchars($floor11873); ?>" data-image="<?php echo base64_encode($upload_img11873); ?>" data-status="<?php echo htmlspecialchars($status11873); ?>" data-category="<?php echo htmlspecialchars($category11873); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11873); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11873); ?>; position:absolute; top:105px; left:800px;'>
                        </div>

                        <!-- ASSET 11874 -->
                        <img src='../image.php?id=11874' style='width:15px; cursor:pointer; position:absolute; top:150px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11874' onclick='fetchAssetData(11874);' class="asset-image" data-id="<?php echo $assetId11874; ?>" data-room="<?php echo htmlspecialchars($room11874); ?>" data-floor="<?php echo htmlspecialchars($floor11874); ?>" data-image="<?php echo base64_encode($upload_img11874); ?>" data-status="<?php echo htmlspecialchars($status11874); ?>" data-category="<?php echo htmlspecialchars($category11874); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11874); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11874); ?>; position:absolute; top:145px; left:800px;'>
                        </div>

                        <!-- ASSET 11875 -->
                        <img src='../image.php?id=11875' style='width:15px; cursor:pointer; position:absolute; top:415px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11875' onclick='fetchAssetData(11875);' class="asset-image" data-id="<?php echo $assetId11875; ?>" data-room="<?php echo htmlspecialchars($room11875); ?>" data-floor="<?php echo htmlspecialchars($floor11875); ?>" data-image="<?php echo base64_encode($upload_img11875); ?>" data-status="<?php echo htmlspecialchars($status11875); ?>" data-category="<?php echo htmlspecialchars($category11875); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11875); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11875); ?>; position:absolute; top:410px; left:675px;'>
                        </div>

                        <!-- ASSET 11876 -->
                        <img src='../image.php?id=11876' style='width:15px; cursor:pointer; position:absolute; top:455px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11876' onclick='fetchAssetData(11876);' class="asset-image" data-id="<?php echo $assetId11876; ?>" data-room="<?php echo htmlspecialchars($room11876); ?>" data-floor="<?php echo htmlspecialchars($floor11876); ?>" data-image="<?php echo base64_encode($upload_img11876); ?>" data-status="<?php echo htmlspecialchars($status11876); ?>" data-category="<?php echo htmlspecialchars($category11876); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11876); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11876); ?>; position:absolute; top:450px; left:675px;'>
                        </div>

                        <!-- ASSET 11877 -->
                        <img src='../image.php?id=11877' style='width:15px; cursor:pointer; position:absolute; top:110px; left:820px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11877' onclick='fetchAssetData(11877);' class="asset-image" data-id="<?php echo $assetId11877; ?>" data-room="<?php echo htmlspecialchars($room11877); ?>" data-floor="<?php echo htmlspecialchars($floor11877); ?>" data-image="<?php echo base64_encode($upload_img11877); ?>" data-status="<?php echo htmlspecialchars($status11877); ?>" data-category="<?php echo htmlspecialchars($category11877); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11877); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11877); ?>; position:absolute; top:105px; left:830px;'>
                        </div>

                        <!-- ASSET 11878 -->
                        <img src='../image.php?id=11878' style='width:15px; cursor:pointer; position:absolute; top:180px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11878' onclick='fetchAssetData(11878);' class="asset-image" data-id="<?php echo $assetId11878; ?>" data-room="<?php echo htmlspecialchars($room11878); ?>" data-floor="<?php echo htmlspecialchars($floor11878); ?>" data-image="<?php echo base64_encode($upload_img11878); ?>" data-status="<?php echo htmlspecialchars($status11878); ?>" data-category="<?php echo htmlspecialchars($category11878); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11878); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11878); ?>; position:absolute; top:175px; left:800px;'>
                        </div>

                        <!-- ASSET 11879 -->
                        <img src='../image.php?id=11879' style='width:15px; cursor:pointer; position:absolute; top:230px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11879' onclick='fetchAssetData(11879);' class="asset-image" data-id="<?php echo $assetId11879; ?>" data-room="<?php echo htmlspecialchars($room11879); ?>" data-floor="<?php echo htmlspecialchars($floor11879); ?>" data-image="<?php echo base64_encode($upload_img11879); ?>" data-status="<?php echo htmlspecialchars($status11879); ?>" data-category="<?php echo htmlspecialchars($category11879); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11879); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11879); ?>; position:absolute; top:225px; left:800px;'>
                        </div>

                        <!-- ASSET 11880 -->
                        <img src='../image.php?id=11880' style='width:15px; cursor:pointer; position:absolute; top:335px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11880' onclick='fetchAssetData(11880);' class="asset-image" data-id="<?php echo $assetId11880; ?>" data-room="<?php echo htmlspecialchars($room11880); ?>" data-floor="<?php echo htmlspecialchars($floor11880); ?>" data-image="<?php echo base64_encode($upload_img11880); ?>" data-status="<?php echo htmlspecialchars($status11880); ?>" data-category="<?php echo htmlspecialchars($category11880); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11880); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11880); ?>; position:absolute; top:330px; left:675px;'>
                        </div>

                        <!-- ASSET 11881 -->
                        <img src='../image.php?id=11881' style='width:15px; cursor:pointer; position:absolute; top:380px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11881' onclick='fetchAssetData(11881);' class="asset-image" data-id="<?php echo $assetId11881; ?>" data-room="<?php echo htmlspecialchars($room11881); ?>" data-floor="<?php echo htmlspecialchars($floor11881); ?>" data-image="<?php echo base64_encode($upload_img11881); ?>" data-status="<?php echo htmlspecialchars($status11881); ?>" data-category="<?php echo htmlspecialchars($category11881); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11881); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11881); ?>; position:absolute; top:375px; left:675px;'>
                        </div>

                        <!-- ASSET 11882 -->
                        <img src='../image.php?id=11882' style='width:15px; cursor:pointer; position:absolute; top:335px; left:765px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11882' onclick='fetchAssetData(11882);' class="asset-image" data-id="<?php echo $assetId11882; ?>" data-room="<?php echo htmlspecialchars($room11882); ?>" data-floor="<?php echo htmlspecialchars($floor11882); ?>" data-image="<?php echo base64_encode($upload_img11882); ?>" data-status="<?php echo htmlspecialchars($status11882); ?>" data-category="<?php echo htmlspecialchars($category11882); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11882); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11882); ?>; position:absolute; top:330px; left:775px;'>
                        </div>

                        <!-- ASSET 11883 -->
                        <img src='../image.php?id=11883' style='width:15px; cursor:pointer; position:absolute; top:110px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11883' onclick='fetchAssetData(11883);' class="asset-image" data-id="<?php echo $assetId11883; ?>" data-room="<?php echo htmlspecialchars($room11883); ?>" data-floor="<?php echo htmlspecialchars($floor11883); ?>" data-image="<?php echo base64_encode($upload_img11883); ?>" data-status="<?php echo htmlspecialchars($status11883); ?>" data-category="<?php echo htmlspecialchars($category11883); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11883); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11883); ?>; position:absolute; top:105px; left:910px;'>
                        </div>

                        <!-- ASSET 11884 -->
                        <img src='../image.php?id=11884' style='width:15px; cursor:pointer; position:absolute; top:110px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11884' onclick='fetchAssetData(11884);' class="asset-image" data-id="<?php echo $assetId11884; ?>" data-room="<?php echo htmlspecialchars($room11884); ?>" data-floor="<?php echo htmlspecialchars($floor11884); ?>" data-image="<?php echo base64_encode($upload_img11884); ?>" data-status="<?php echo htmlspecialchars($status11884); ?>" data-category="<?php echo htmlspecialchars($category11884); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11884); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11884); ?>; position:absolute; top:105px; left:950px;'>
                        </div>

                        <!-- ASSET 11885 -->
                        <img src='../image.php?id=11885' style='width:15px; cursor:pointer; position:absolute; top:110px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11885' onclick='fetchAssetData(11885);' class="asset-image" data-id="<?php echo $assetId11885; ?>" data-room="<?php echo htmlspecialchars($room11885); ?>" data-floor="<?php echo htmlspecialchars($floor11885); ?>" data-image="<?php echo base64_encode($upload_img11885); ?>" data-status="<?php echo htmlspecialchars($status11885); ?>" data-category="<?php echo htmlspecialchars($category11885); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11885); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11885); ?>; position:absolute; top:105px; left:990px;'>
                        </div>

                        <!-- ASSET 11886 -->
                        <img src='../image.php?id=11886' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11886' onclick='fetchAssetData(11886);' class="asset-image" data-id="<?php echo $assetId11886; ?>" data-room="<?php echo htmlspecialchars($room11886); ?>" data-floor="<?php echo htmlspecialchars($floor11886); ?>" data-image="<?php echo base64_encode($upload_img11886); ?>" data-status="<?php echo htmlspecialchars($status11886); ?>" data-category="<?php echo htmlspecialchars($category11886); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11886); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11886); ?>; position:absolute; top:105px; left:1050px;'>
                        </div>

                        <!-- ASSET 11887 -->
                        <img src='../image.php?id=11887' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11887' onclick='fetchAssetData(11887);' class="asset-image" data-id="<?php echo $assetId11887; ?>" data-room="<?php echo htmlspecialchars($room11887); ?>" data-floor="<?php echo htmlspecialchars($floor11887); ?>" data-image="<?php echo base64_encode($upload_img11887); ?>" data-status="<?php echo htmlspecialchars($status11887); ?>" data-category="<?php echo htmlspecialchars($category11887); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11887); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11887); ?>; position:absolute; top:105px; left:1090px;'>
                        </div>

                        <!-- ASSET 11888 -->
                        <img src='../image.php?id=11888' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11888' onclick='fetchAssetData(11888);' class="asset-image" data-id="<?php echo $assetId11888; ?>" data-room="<?php echo htmlspecialchars($room11888); ?>" data-floor="<?php echo htmlspecialchars($floor11888); ?>" data-image="<?php echo base64_encode($upload_img11888); ?>" data-status="<?php echo htmlspecialchars($status11888); ?>" data-category="<?php echo htmlspecialchars($category11888); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11888); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11888); ?>; position:absolute; top:105px; left:1130px;'>
                        </div>

                        <!-- ASSET 11889 -->
                        <img src='../image.php?id=11889' style='width:15px; cursor:pointer; position:absolute; top:150px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11889' onclick='fetchAssetData(11889);' class="asset-image" data-id="<?php echo $assetId11889; ?>" data-room="<?php echo htmlspecialchars($room11889); ?>" data-floor="<?php echo htmlspecialchars($floor11889); ?>" data-image="<?php echo base64_encode($upload_img11889); ?>" data-status="<?php echo htmlspecialchars($status11889); ?>" data-category="<?php echo htmlspecialchars($category11889); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11889); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11889); ?>; position:absolute; top:145px; left:910px;'>
                        </div>

                        <!-- ASSET 11890 -->
                        <img src='../image.php?id=11890' style='width:15px; cursor:pointer; position:absolute; top:150px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11890' onclick='fetchAssetData(11890);' class="asset-image" data-id="<?php echo $assetId11890; ?>" data-room="<?php echo htmlspecialchars($room11890); ?>" data-floor="<?php echo htmlspecialchars($floor11890); ?>" data-image="<?php echo base64_encode($upload_img11890); ?>" data-status="<?php echo htmlspecialchars($status11890); ?>" data-category="<?php echo htmlspecialchars($category11890); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11890); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11890); ?>; position:absolute; top:145px; left:950px;'>
                        </div>

                        <!-- ASSET 11891 -->
                        <img src='../image.php?id=11891' style='width:15px; cursor:pointer; position:absolute; top:150px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11891' onclick='fetchAssetData(11891);' class="asset-image" data-id="<?php echo $assetId11891; ?>" data-room="<?php echo htmlspecialchars($room11891); ?>" data-floor="<?php echo htmlspecialchars($floor11891); ?>" data-image="<?php echo base64_encode($upload_img11891); ?>" data-status="<?php echo htmlspecialchars($status11891); ?>" data-category="<?php echo htmlspecialchars($category11891); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11891); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11891); ?>; position:absolute; top:145px; left:990px;'>
                        </div>

                        <!-- ASSET 11892 -->
                        <img src='../image.php?id=11892' style='width:15px; cursor:pointer; position:absolute; top:150px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11892' onclick='fetchAssetData(11892);' class="asset-image" data-id="<?php echo $assetId11892; ?>" data-room="<?php echo htmlspecialchars($room11892); ?>" data-floor="<?php echo htmlspecialchars($floor11892); ?>" data-image="<?php echo base64_encode($upload_img11892); ?>" data-status="<?php echo htmlspecialchars($status11892); ?>" data-category="<?php echo htmlspecialchars($category11892); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11892); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11892); ?>; position:absolute; top:145px; left:1050px;'>
                        </div>

                        <!-- ASSET 11893 -->
                        <img src='../image.php?id=11893' style='width:15px; cursor:pointer; position:absolute; top:150px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11893' onclick='fetchAssetData(11893);' class="asset-image" data-id="<?php echo $assetId11893; ?>" data-room="<?php echo htmlspecialchars($room11893); ?>" data-floor="<?php echo htmlspecialchars($floor11893); ?>" data-image="<?php echo base64_encode($upload_img11893); ?>" data-status="<?php echo htmlspecialchars($status11893); ?>" data-category="<?php echo htmlspecialchars($category11893); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11893); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11893); ?>; position:absolute; top:145px; left:1090px;'>
                        </div>

                        <!-- ASSET 11894 -->
                        <img src='../image.php?id=11894' style='width:15px; cursor:pointer; position:absolute; top:150px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11894' onclick='fetchAssetData(11894);' class="asset-image" data-id="<?php echo $assetId11894; ?>" data-room="<?php echo htmlspecialchars($room11894); ?>" data-floor="<?php echo htmlspecialchars($floor11894); ?>" data-image="<?php echo base64_encode($upload_img11894); ?>" data-status="<?php echo htmlspecialchars($status11894); ?>" data-category="<?php echo htmlspecialchars($category11894); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11894); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11894); ?>; position:absolute; top:145px; left:1130px;'>
                        </div>

                        <!-- ASSET 11895 -->
                        <img src='../image.php?id=11895' style='width:15px; cursor:pointer; position:absolute; top:190px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11895' onclick='fetchAssetData(11895);' class="asset-image" data-id="<?php echo $assetId11895; ?>" data-room="<?php echo htmlspecialchars($room11895); ?>" data-floor="<?php echo htmlspecialchars($floor11895); ?>" data-image="<?php echo base64_encode($upload_img11895); ?>" data-status="<?php echo htmlspecialchars($status11895); ?>" data-category="<?php echo htmlspecialchars($category11895); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11895); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11895); ?>; position:absolute; top:185px; left:910px;'>
                        </div>

                        <!-- ASSET 11896 -->
                        <img src='../image.php?id=11896' style='width:15px; cursor:pointer; position:absolute; top:190px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11896' onclick='fetchAssetData(11896);' class="asset-image" data-id="<?php echo $assetId11896; ?>" data-room="<?php echo htmlspecialchars($room11896); ?>" data-floor="<?php echo htmlspecialchars($floor11896); ?>" data-image="<?php echo base64_encode($upload_img11896); ?>" data-status="<?php echo htmlspecialchars($status11896); ?>" data-category="<?php echo htmlspecialchars($category11896); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11896); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11896); ?>; position:absolute; top:185px; left:950px;'>
                        </div>

                        <!-- ASSET 11897 -->
                        <img src='../image.php?id=11897' style='width:15px; cursor:pointer; position:absolute; top:190px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11897' onclick='fetchAssetData(11897);' class="asset-image" data-id="<?php echo $assetId11897; ?>" data-room="<?php echo htmlspecialchars($room11897); ?>" data-floor="<?php echo htmlspecialchars($floor11897); ?>" data-image="<?php echo base64_encode($upload_img11897); ?>" data-status="<?php echo htmlspecialchars($status11897); ?>" data-category="<?php echo htmlspecialchars($category11897); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11897); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11897); ?>; position:absolute; top:185px; left:990px;'>
                        </div>

                        <!-- ASSET 11898 -->
                        <img src='../image.php?id=11898' style='width:15px; cursor:pointer; position:absolute; top:190px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11898' onclick='fetchAssetData(11898);' class="asset-image" data-id="<?php echo $assetId11898; ?>" data-room="<?php echo htmlspecialchars($room11898); ?>" data-floor="<?php echo htmlspecialchars($floor11898); ?>" data-image="<?php echo base64_encode($upload_img11898); ?>" data-status="<?php echo htmlspecialchars($status11898); ?>" data-category="<?php echo htmlspecialchars($category11898); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11898); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11898); ?>; position:absolute; top:185px; left:1050px;'>
                        </div>

                        <!-- ASSET 11899 -->
                        <img src='../image.php?id=11899' style='width:15px; cursor:pointer; position:absolute; top:190px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11899' onclick='fetchAssetData(11899);' class="asset-image" data-id="<?php echo $assetId11899; ?>" data-room="<?php echo htmlspecialchars($room11899); ?>" data-floor="<?php echo htmlspecialchars($floor11899); ?>" data-image="<?php echo base64_encode($upload_img11899); ?>" data-status="<?php echo htmlspecialchars($status11899); ?>" data-category="<?php echo htmlspecialchars($category11899); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11899); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11899); ?>; position:absolute; top:185px; left:1090px;'>
                        </div>

                        <!-- ASSET 11900 -->
                        <img src='../image.php?id=11900' style='width:15px; cursor:pointer; position:absolute; top:190px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11900' onclick='fetchAssetData(11900);' class="asset-image" data-id="<?php echo $assetId11900; ?>" data-room="<?php echo htmlspecialchars($room11900); ?>" data-floor="<?php echo htmlspecialchars($floor11900); ?>" data-image="<?php echo base64_encode($upload_img11900); ?>" data-status="<?php echo htmlspecialchars($status11900); ?>" data-category="<?php echo htmlspecialchars($category11900); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11900); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11900); ?>; position:absolute; top:185px; left:1130px;'>
                        </div>

                        <!-- ASSET 11901 -->
                        <img src='../image.php?id=11901' style='width:15px; cursor:pointer; position:absolute; top:230px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11901' onclick='fetchAssetData(11901);' class="asset-image" data-id="<?php echo $assetId11901; ?>" data-room="<?php echo htmlspecialchars($room11901); ?>" data-floor="<?php echo htmlspecialchars($floor11901); ?>" data-image="<?php echo base64_encode($upload_img11901); ?>" data-status="<?php echo htmlspecialchars($status11901); ?>" data-category="<?php echo htmlspecialchars($category11901); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11901); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11901); ?>; position:absolute; top:225px; left:910px;'>
                        </div>

                        <!-- ASSET 11902 -->
                        <img src='../image.php?id=11902' style='width:15px; cursor:pointer; position:absolute; top:230px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11902' onclick='fetchAssetData(11902);' class="asset-image" data-id="<?php echo $assetId11902; ?>" data-room="<?php echo htmlspecialchars($room11902); ?>" data-floor="<?php echo htmlspecialchars($floor11902); ?>" data-image="<?php echo base64_encode($upload_img11902); ?>" data-status="<?php echo htmlspecialchars($status11902); ?>" data-category="<?php echo htmlspecialchars($category11902); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11902); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11902); ?>; position:absolute; top:225px; left:950px;'>
                        </div>

                        <!-- ASSET 11903 -->
                        <img src='../image.php?id=11903' style='width:15px; cursor:pointer; position:absolute; top:230px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11903' onclick='fetchAssetData(11903);' class="asset-image" data-id="<?php echo $assetId11903; ?>" data-room="<?php echo htmlspecialchars($room11903); ?>" data-floor="<?php echo htmlspecialchars($floor11903); ?>" data-image="<?php echo base64_encode($upload_img11903); ?>" data-status="<?php echo htmlspecialchars($status11903); ?>" data-category="<?php echo htmlspecialchars($category11903); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11903); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11903); ?>; position:absolute; top:225px; left:990px;'>
                        </div>

                        <!-- ASSET 11904 -->
                        <img src='../image.php?id=11904' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11904' onclick='fetchAssetData(11904);' class="asset-image" data-id="<?php echo $assetId11904; ?>" data-room="<?php echo htmlspecialchars($room11904); ?>" data-floor="<?php echo htmlspecialchars($floor11904); ?>" data-image="<?php echo base64_encode($upload_img11904); ?>" data-status="<?php echo htmlspecialchars($status11904); ?>" data-category="<?php echo htmlspecialchars($category11904); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11904); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11904); ?>; position:absolute; top:225px; left:1050px;'>
                        </div>

                        <!-- ASSET 11905 -->
                        <img src='../image.php?id=11905' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11905' onclick='fetchAssetData(11905);' class="asset-image" data-id="<?php echo $assetId11905; ?>" data-room="<?php echo htmlspecialchars($room11905); ?>" data-floor="<?php echo htmlspecialchars($floor11905); ?>" data-image="<?php echo base64_encode($upload_img11905); ?>" data-status="<?php echo htmlspecialchars($status11905); ?>" data-category="<?php echo htmlspecialchars($category11905); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11905); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11905); ?>; position:absolute; top:225px; left:1090px;'>
                        </div>

                        <!-- ASSET 11906 -->
                        <img src='../image.php?id=11906' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11906' onclick='fetchAssetData(11906);' class="asset-image" data-id="<?php echo $assetId11906; ?>" data-room="<?php echo htmlspecialchars($room11906); ?>" data-floor="<?php echo htmlspecialchars($floor11906); ?>" data-image="<?php echo base64_encode($upload_img11906); ?>" data-status="<?php echo htmlspecialchars($status11906); ?>" data-category="<?php echo htmlspecialchars($category11906); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11906); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11906); ?>; position:absolute; top:225px; left:1130px;'>
                        </div>

                        <!-- ASSET 11907 -->
                        <img src='../image.php?id=11907' style='width:20px; cursor:pointer; position:absolute; top:125px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11907' onclick='fetchAssetData(11907);' class="asset-image" data-id="<?php echo $assetId11907; ?>" data-room="<?php echo htmlspecialchars($room11907); ?>" data-floor="<?php echo htmlspecialchars($floor11907); ?>" data-image="<?php echo base64_encode($upload_img11907); ?>" data-status="<?php echo htmlspecialchars($status11907); ?>" data-category="<?php echo htmlspecialchars($category11907); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11907); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11907); ?>; position:absolute; top:120px; left:915px;'>
                        </div>


                        <!-- ASSET 11908 -->
                        <img src='../image.php?id=11908' style='width:20px; cursor:pointer; position:absolute; top:125px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11908' onclick='fetchAssetData(11908);' class="asset-image" data-id="<?php echo $assetId11908; ?>" data-room="<?php echo htmlspecialchars($room11908); ?>" data-floor="<?php echo htmlspecialchars($floor11908); ?>" data-image="<?php echo base64_encode($upload_img11908); ?>" data-status="<?php echo htmlspecialchars($status11908); ?>" data-category="<?php echo htmlspecialchars($category11908); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11908); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11908); ?>; position:absolute; top:120px; left:935px;'>
                        </div>

                        <!-- ASSET 11909 -->
                        <img src='../image.php?id=11909' style='width:20px; cursor:pointer; position:absolute; top:125px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11909' onclick='fetchAssetData(11909);' class="asset-image" data-id="<?php echo $assetId11909; ?>" data-room="<?php echo htmlspecialchars($room11909); ?>" data-floor="<?php echo htmlspecialchars($floor11909); ?>" data-image="<?php echo base64_encode($upload_img11909); ?>" data-status="<?php echo htmlspecialchars($status11909); ?>" data-category="<?php echo htmlspecialchars($category11909); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11909); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11909); ?>; position:absolute; top:120px; left:955px;'>
                        </div>

                        <!-- ASSET 11910 -->
                        <img src='../image.php?id=11910' style='width:20px; cursor:pointer; position:absolute; top:125px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11910' onclick='fetchAssetData(11910);' class="asset-image" data-id="<?php echo $assetId11910; ?>" data-room="<?php echo htmlspecialchars($room11910); ?>" data-floor="<?php echo htmlspecialchars($floor11910); ?>" data-image="<?php echo base64_encode($upload_img11910); ?>" data-status="<?php echo htmlspecialchars($status11910); ?>" data-category="<?php echo htmlspecialchars($category11910); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11910); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11910); ?>; position:absolute; top:120px; left:975px;'>
                        </div>

                        <!-- ASSET 11911 -->
                        <img src='../image.php?id=11911' style='width:20px; cursor:pointer; position:absolute; top:125px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11911' onclick='fetchAssetData(11911);' class="asset-image" data-id="<?php echo $assetId11911; ?>" data-room="<?php echo htmlspecialchars($room11911); ?>" data-floor="<?php echo htmlspecialchars($floor11911); ?>" data-image="<?php echo base64_encode($upload_img11911); ?>" data-status="<?php echo htmlspecialchars($status11911); ?>" data-category="<?php echo htmlspecialchars($category11911); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11911); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11911); ?>; position:absolute; top:120px; left:995px;'>
                        </div>

                        <!-- ASSET 11912 -->
                        <img src='../image.php?id=11912' style='width:20px; cursor:pointer; position:absolute; top:125px; left:1000px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11912' onclick='fetchAssetData(11912);' class="asset-image" data-id="<?php echo $assetId11912; ?>" data-room="<?php echo htmlspecialchars($room11912); ?>" data-floor="<?php echo htmlspecialchars($floor11912); ?>" data-image="<?php echo base64_encode($upload_img11912); ?>" data-status="<?php echo htmlspecialchars($status11912); ?>" data-category="<?php echo htmlspecialchars($category11912); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11912); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11912); ?>; position:absolute; top:120px; left:1015px;'>
                        </div>

                        <!-- ASSET 11913 -->
                        <img src='../image.php?id=11913' style='width:20px; cursor:pointer; position:absolute; top:125px; left:1020px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11913' onclick='fetchAssetData(11913);' class="asset-image" data-id="<?php echo $assetId11913; ?>" data-room="<?php echo htmlspecialchars($room11913); ?>" data-floor="<?php echo htmlspecialchars($floor11913); ?>" data-image="<?php echo base64_encode($upload_img11913); ?>" data-status="<?php echo htmlspecialchars($status11913); ?>" data-category="<?php echo htmlspecialchars($category11913); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11913); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11913); ?>; position:absolute; top:120px; left:1035px;'>
                        </div>

                        <!-- ASSET 11914 -->
                        <img src='../image.php?id=11914' style='width:20px; cursor:pointer; position:absolute; top:125px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11914' onclick='fetchAssetData(11914);' class="asset-image" data-id="<?php echo $assetId11914; ?>" data-room="<?php echo htmlspecialchars($room11914); ?>" data-floor="<?php echo htmlspecialchars($floor11914); ?>" data-image="<?php echo base64_encode($upload_img11914); ?>" data-status="<?php echo htmlspecialchars($status11914); ?>" data-category="<?php echo htmlspecialchars($category11914); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11914); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11914); ?>; position:absolute; top:120px; left:1055px;'>
                        </div>

                        <!-- ASSET 11915 -->
                        <img src='../image.php?id=11915' style='width:20px; cursor:pointer; position:absolute; top:125px; left:1060px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11915' onclick='fetchAssetData(11915);' class="asset-image" data-id="<?php echo $assetId11915; ?>" data-room="<?php echo htmlspecialchars($room11915); ?>" data-floor="<?php echo htmlspecialchars($floor11915); ?>" data-image="<?php echo base64_encode($upload_img11915); ?>" data-status="<?php echo htmlspecialchars($status11915); ?>" data-category="<?php echo htmlspecialchars($category11915); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11915); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11915); ?>; position:absolute; top:120px; left:1075px;'>
                        </div>

                        <!-- ASSET 11916 -->
                        <img src='../image.php?id=11916' style='width:20px; cursor:pointer; position:absolute; top:140px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11916' onclick='fetchAssetData(11916);' class="asset-image" data-id="<?php echo $assetId11916; ?>" data-room="<?php echo htmlspecialchars($room11916); ?>" data-floor="<?php echo htmlspecialchars($floor11916); ?>" data-image="<?php echo base64_encode($upload_img11916); ?>" data-status="<?php echo htmlspecialchars($status11916); ?>" data-category="<?php echo htmlspecialchars($category11916); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11916); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11916); ?>; position:absolute; top:135px; left:915px;'>
                        </div>

                        <!-- ASSET 11917 -->
                        <img src='../image.php?id=11917' style='width:20px; cursor:pointer; position:absolute; top:140px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11917' onclick='fetchAssetData(11917);' class="asset-image" data-id="<?php echo $assetId11917; ?>" data-room="<?php echo htmlspecialchars($room11917); ?>" data-floor="<?php echo htmlspecialchars($floor11917); ?>" data-image="<?php echo base64_encode($upload_img11917); ?>" data-status="<?php echo htmlspecialchars($status11917); ?>" data-category="<?php echo htmlspecialchars($category11917); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11917); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11917); ?>; position:absolute; top:135px; left:935px;'>
                        </div>

                        <!-- ASSET 11918 -->
                        <img src='../image.php?id=11918' style='width:20px; cursor:pointer; position:absolute; top:140px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11918' onclick='fetchAssetData(11918);' class="asset-image" data-id="<?php echo $assetId11918; ?>" data-room="<?php echo htmlspecialchars($room11918); ?>" data-floor="<?php echo htmlspecialchars($floor11918); ?>" data-image="<?php echo base64_encode($upload_img11918); ?>" data-status="<?php echo htmlspecialchars($status11918); ?>" data-category="<?php echo htmlspecialchars($category11918); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11918); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11918); ?>; position:absolute; top:135px; left:955px;'>
                        </div>

                        <!-- ASSET 11919 -->
                        <img src='../image.php?id=11919' style='width:20px; cursor:pointer; position:absolute; top:140px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11919' onclick='fetchAssetData(11919);' class="asset-image" data-id="<?php echo $assetId11919; ?>" data-room="<?php echo htmlspecialchars($room11919); ?>" data-floor="<?php echo htmlspecialchars($floor11919); ?>" data-image="<?php echo base64_encode($upload_img11919); ?>" data-status="<?php echo htmlspecialchars($status11919); ?>" data-category="<?php echo htmlspecialchars($category11919); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11919); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11919); ?>; position:absolute; top:135px; left:975px;'>
                        </div>

                        <!-- ASSET 11920 -->
                        <img src='../image.php?id=11920' style='width:20px; cursor:pointer; position:absolute; top:140px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11920' onclick='fetchAssetData(11920);' class="asset-image" data-id="<?php echo $assetId11920; ?>" data-room="<?php echo htmlspecialchars($room11920); ?>" data-floor="<?php echo htmlspecialchars($floor11920); ?>" data-image="<?php echo base64_encode($upload_img11920); ?>" data-status="<?php echo htmlspecialchars($status11920); ?>" data-category="<?php echo htmlspecialchars($category11920); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11920); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11920); ?>; position:absolute; top:135px; left:995px;'>
                        </div>

                        <!-- ASSET 11921 -->
                        <img src='../image.php?id=11921' style='width:20px; cursor:pointer; position:absolute; top:140px; left:1000px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11921' onclick='fetchAssetData(11921);' class="asset-image" data-id="<?php echo $assetId11921; ?>" data-room="<?php echo htmlspecialchars($room11921); ?>" data-floor="<?php echo htmlspecialchars($floor11921); ?>" data-image="<?php echo base64_encode($upload_img11921); ?>" data-status="<?php echo htmlspecialchars($status11921); ?>" data-category="<?php echo htmlspecialchars($category11921); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11921); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11921); ?>; position:absolute; top:135px; left:1015px;'>
                        </div>

                        <!-- ASSET 11922 -->
                        <img src='../image.php?id=11922' style='width:20px; cursor:pointer; position:absolute; top:140px; left:1020px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11922' onclick='fetchAssetData(11922);' class="asset-image" data-id="<?php echo $assetId11922; ?>" data-room="<?php echo htmlspecialchars($room11922); ?>" data-floor="<?php echo htmlspecialchars($floor11922); ?>" data-image="<?php echo base64_encode($upload_img11922); ?>" data-status="<?php echo htmlspecialchars($status11922); ?>" data-category="<?php echo htmlspecialchars($category11922); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11922); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11922); ?>; position:absolute; top:135px; left:1035px;'>
                        </div>

                        <!-- ASSET 11923 -->
                        <img src='../image.php?id=11923' style='width:20px; cursor:pointer; position:absolute; top:140px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11923' onclick='fetchAssetData(11923);' class="asset-image" data-id="<?php echo $assetId11923; ?>" data-room="<?php echo htmlspecialchars($room11923); ?>" data-floor="<?php echo htmlspecialchars($floor11923); ?>" data-image="<?php echo base64_encode($upload_img11923); ?>" data-status="<?php echo htmlspecialchars($status11923); ?>" data-category="<?php echo htmlspecialchars($category11923); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11923); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11923); ?>; position:absolute; top:135px; left:1055px;'>
                        </div>

                        <!-- ASSET 11924 -->
                        <img src='../image.php?id=11924' style='width:20px; cursor:pointer; position:absolute; top:140px; left:1060px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11924' onclick='fetchAssetData(11924);' class="asset-image" data-id="<?php echo $assetId11924; ?>" data-room="<?php echo htmlspecialchars($room11924); ?>" data-floor="<?php echo htmlspecialchars($floor11924); ?>" data-image="<?php echo base64_encode($upload_img11924); ?>" data-status="<?php echo htmlspecialchars($status11924); ?>" data-category="<?php echo htmlspecialchars($category11924); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11924); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11924); ?>; position:absolute; top:135px; left:1075px;'>
                        </div>

                        <!-- ASSET 11925 -->
                        <img src='../image.php?id=11925' style='width:20px; cursor:pointer; position:absolute; top:200px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11925' onclick='fetchAssetData(11925);' class="asset-image" data-id="<?php echo $assetId11925; ?>" data-room="<?php echo htmlspecialchars($room11925); ?>" data-floor="<?php echo htmlspecialchars($floor11925); ?>" data-image="<?php echo base64_encode($upload_img11925); ?>" data-status="<?php echo htmlspecialchars($status11925); ?>" data-category="<?php echo htmlspecialchars($category11925); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11925); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11925); ?>; position:absolute; top:195px; left:915px;'>
                        </div>

                        <!-- ASSET 11926 -->
                        <img src='../image.php?id=11926' style='width:20px; cursor:pointer; position:absolute; top:200px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11926' onclick='fetchAssetData(11926);' class="asset-image" data-id="<?php echo $assetId11926; ?>" data-room="<?php echo htmlspecialchars($room11926); ?>" data-floor="<?php echo htmlspecialchars($floor11926); ?>" data-image="<?php echo base64_encode($upload_img11926); ?>" data-status="<?php echo htmlspecialchars($status11926); ?>" data-category="<?php echo htmlspecialchars($category11926); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11926); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11926); ?>; position:absolute; top:195px; left:935px;'>
                        </div>

                        <!-- ASSET 11927 -->
                        <img src='../image.php?id=11927' style='width:20px; cursor:pointer; position:absolute; top:200px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11927' onclick='fetchAssetData(11927);' class="asset-image" data-id="<?php echo $assetId11927; ?>" data-room="<?php echo htmlspecialchars($room11927); ?>" data-floor="<?php echo htmlspecialchars($floor11927); ?>" data-image="<?php echo base64_encode($upload_img11927); ?>" data-status="<?php echo htmlspecialchars($status11927); ?>" data-category="<?php echo htmlspecialchars($category11927); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11927); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11927); ?>; position:absolute; top:195px; left:955px;'>
                        </div>

                        <!-- ASSET 11928 -->
                        <img src='../image.php?id=11928' style='width:20px; cursor:pointer; position:absolute; top:200px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11928' onclick='fetchAssetData(11928);' class="asset-image" data-id="<?php echo $assetId11928; ?>" data-room="<?php echo htmlspecialchars($room11928); ?>" data-floor="<?php echo htmlspecialchars($floor11928); ?>" data-image="<?php echo base64_encode($upload_img11928); ?>" data-status="<?php echo htmlspecialchars($status11928); ?>" data-category="<?php echo htmlspecialchars($category11928); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11928); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11928); ?>; position:absolute; top:195px; left:975px;'>
                        </div>

                        <!-- ASSET 11929 -->
                        <img src='../image.php?id=11929' style='width:20px; cursor:pointer; position:absolute; top:200px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11929' onclick='fetchAssetData(11929);' class="asset-image" data-id="<?php echo $assetId11929; ?>" data-room="<?php echo htmlspecialchars($room11929); ?>" data-floor="<?php echo htmlspecialchars($floor11929); ?>" data-image="<?php echo base64_encode($upload_img11929); ?>" data-status="<?php echo htmlspecialchars($status11929); ?>" data-category="<?php echo htmlspecialchars($category11929); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11929); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11929); ?>; position:absolute; top:195px; left:995px;'>
                        </div>

                        <!-- ASSET 11930 -->
                        <img src='../image.php?id=11930' style='width:20px; cursor:pointer; position:absolute; top:200px; left:1000px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11930' onclick='fetchAssetData(11930);' class="asset-image" data-id="<?php echo $assetId11930; ?>" data-room="<?php echo htmlspecialchars($room11930); ?>" data-floor="<?php echo htmlspecialchars($floor11930); ?>" data-image="<?php echo base64_encode($upload_img11930); ?>" data-status="<?php echo htmlspecialchars($status11930); ?>" data-category="<?php echo htmlspecialchars($category11930); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11930); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11930); ?>; position:absolute; top:195px; left:1015px;'>
                        </div>

                        <!-- ASSET 11931 -->
                        <img src='../image.php?id=11931' style='width:20px; cursor:pointer; position:absolute; top:200px; left:1020px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11931' onclick='fetchAssetData(11931);' class="asset-image" data-id="<?php echo $assetId11931; ?>" data-room="<?php echo htmlspecialchars($room11931); ?>" data-floor="<?php echo htmlspecialchars($floor11931); ?>" data-image="<?php echo base64_encode($upload_img11931); ?>" data-status="<?php echo htmlspecialchars($status11931); ?>" data-category="<?php echo htmlspecialchars($category11931); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11931); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11931); ?>; position:absolute; top:195px; left:1035px;'>
                        </div>

                        <!-- ASSET 11932 -->
                        <img src='../image.php?id=11932' style='width:20px; cursor:pointer; position:absolute; top:200px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11932' onclick='fetchAssetData(11932);' class="asset-image" data-id="<?php echo $assetId11932; ?>" data-room="<?php echo htmlspecialchars($room11932); ?>" data-floor="<?php echo htmlspecialchars($floor11932); ?>" data-image="<?php echo base64_encode($upload_img11932); ?>" data-status="<?php echo htmlspecialchars($status11932); ?>" data-category="<?php echo htmlspecialchars($category11932); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11932); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11932); ?>; position:absolute; top:195px; left:1055px;'>
                        </div>

                        <!-- ASSET 11933 -->
                        <img src='../image.php?id=11933' style='width:20px; cursor:pointer; position:absolute; top:200px; left:1060px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11933' onclick='fetchAssetData(11933);' class="asset-image" data-id="<?php echo $assetId11933; ?>" data-room="<?php echo htmlspecialchars($room11933); ?>" data-floor="<?php echo htmlspecialchars($floor11933); ?>" data-image="<?php echo base64_encode($upload_img11933); ?>" data-status="<?php echo htmlspecialchars($status11933); ?>" data-category="<?php echo htmlspecialchars($category11933); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11933); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11933); ?>; position:absolute; top:195px; left:1075px;'>
                        </div>

                        <!-- ASSET 11934 -->
                        <img src='../image.php?id=11934' style='width:20px; cursor:pointer; position:absolute; top:215px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11934' onclick='fetchAssetData(11934);' class="asset-image" data-id="<?php echo $assetId11934; ?>" data-room="<?php echo htmlspecialchars($room11934); ?>" data-floor="<?php echo htmlspecialchars($floor11934); ?>" data-image="<?php echo base64_encode($upload_img11934); ?>" data-status="<?php echo htmlspecialchars($status11934); ?>" data-category="<?php echo htmlspecialchars($category11934); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11934); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11934); ?>; position:absolute; top:210px; left:915px;'>
                        </div>

                        <!-- ASSET 11935 -->
                        <img src='../image.php?id=11935' style='width:20px; cursor:pointer; position:absolute; top:215px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11935' onclick='fetchAssetData(11935);' class="asset-image" data-id="<?php echo $assetId11935; ?>" data-room="<?php echo htmlspecialchars($room11935); ?>" data-floor="<?php echo htmlspecialchars($floor11935); ?>" data-image="<?php echo base64_encode($upload_img11935); ?>" data-status="<?php echo htmlspecialchars($status11935); ?>" data-category="<?php echo htmlspecialchars($category11935); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11935); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11935); ?>; position:absolute; top:210px; left:935px;'>
                        </div>

                        <!-- ASSET 11936 -->
                        <img src='../image.php?id=11936' style='width:20px; cursor:pointer; position:absolute; top:215px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11936' onclick='fetchAssetData(11936);' class="asset-image" data-id="<?php echo $assetId11936; ?>" data-room="<?php echo htmlspecialchars($room11936); ?>" data-floor="<?php echo htmlspecialchars($floor11936); ?>" data-image="<?php echo base64_encode($upload_img11936); ?>" data-status="<?php echo htmlspecialchars($status11936); ?>" data-category="<?php echo htmlspecialchars($category11936); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11936); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11936); ?>; position:absolute; top:210px; left:955px;'>
                        </div>

                        <!-- ASSET 11937 -->
                        <img src='../image.php?id=11937' style='width:20px; cursor:pointer; position:absolute; top:215px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11937' onclick='fetchAssetData(11937);' class="asset-image" data-id="<?php echo $assetId11937; ?>" data-room="<?php echo htmlspecialchars($room11937); ?>" data-floor="<?php echo htmlspecialchars($floor11937); ?>" data-image="<?php echo base64_encode($upload_img11937); ?>" data-status="<?php echo htmlspecialchars($status11937); ?>" data-category="<?php echo htmlspecialchars($category11937); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11937); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11937); ?>; position:absolute; top:210px; left:975px;'>
                        </div>

                        <!-- ASSET 11938 -->
                        <img src='../image.php?id=11938' style='width:20px; cursor:pointer; position:absolute; top:215px; left:980px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11938' onclick='fetchAssetData(11938);' class="asset-image" data-id="<?php echo $assetId11938; ?>" data-room="<?php echo htmlspecialchars($room11938); ?>" data-floor="<?php echo htmlspecialchars($floor11938); ?>" data-image="<?php echo base64_encode($upload_img11938); ?>" data-status="<?php echo htmlspecialchars($status11938); ?>" data-category="<?php echo htmlspecialchars($category11938); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11938); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11938); ?>; position:absolute; top:210px; left:995px;'>
                        </div>

                        <!-- ASSET 11939 -->
                        <img src='../image.php?id=11939' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1000px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11939' onclick='fetchAssetData(11939);' class="asset-image" data-id="<?php echo $assetId11939; ?>" data-room="<?php echo htmlspecialchars($room11939); ?>" data-floor="<?php echo htmlspecialchars($floor11939); ?>" data-image="<?php echo base64_encode($upload_img11939); ?>" data-status="<?php echo htmlspecialchars($status11939); ?>" data-category="<?php echo htmlspecialchars($category11939); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11939); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11939); ?>; position:absolute; top:210px; left:1015px;'>
                        </div>

                        <!-- ASSET 11940 -->
                        <img src='../image.php?id=11940' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1020px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11940' onclick='fetchAssetData(11940);' class="asset-image" data-id="<?php echo $assetId11940; ?>" data-room="<?php echo htmlspecialchars($room11940); ?>" data-floor="<?php echo htmlspecialchars($floor11940); ?>" data-image="<?php echo base64_encode($upload_img11940); ?>" data-status="<?php echo htmlspecialchars($status11940); ?>" data-category="<?php echo htmlspecialchars($category11940); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11940); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11940); ?>; position:absolute; top:210px; left:1035px;'>
                        </div>

                        <!-- ASSET 11941 -->
                        <img src='../image.php?id=11941' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11941' onclick='fetchAssetData(11941);' class="asset-image" data-id="<?php echo $assetId11941; ?>" data-room="<?php echo htmlspecialchars($room11941); ?>" data-floor="<?php echo htmlspecialchars($floor11941); ?>" data-image="<?php echo base64_encode($upload_img11941); ?>" data-status="<?php echo htmlspecialchars($status11941); ?>" data-category="<?php echo htmlspecialchars($category11941); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11941); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11941); ?>; position:absolute; top:210px; left:1055px;'>
                        </div>

                        <!-- ASSET 11942 -->
                        <img src='../image.php?id=11942' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1060px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11942' onclick='fetchAssetData(11942);' class="asset-image" data-id="<?php echo $assetId11942; ?>" data-room="<?php echo htmlspecialchars($room11942); ?>" data-floor="<?php echo htmlspecialchars($floor11942); ?>" data-image="<?php echo base64_encode($upload_img11942); ?>" data-status="<?php echo htmlspecialchars($status11942); ?>" data-category="<?php echo htmlspecialchars($category11942); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11942); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11942); ?>; position:absolute; top:210px; left:1075px;'>
                        </div>

                        <!-- ASSET 11943 -->
                        <img src='../image.php?id=11943' style='width:15px; cursor:pointer; position:absolute; top:110px; left:900px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11943' onclick='fetchAssetData(11943);' class="asset-image" data-id="<?php echo $assetId11943; ?>" data-room="<?php echo htmlspecialchars($room11943); ?>" data-floor="<?php echo htmlspecialchars($floor11943); ?>" data-image="<?php echo base64_encode($upload_img11943); ?>" data-status="<?php echo htmlspecialchars($status11943); ?>" data-category="<?php echo htmlspecialchars($category11943); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11943); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11943); ?>; position:absolute; top:105px; left:915px;'>
                        </div>

                        <!-- ASSET 11944 -->
                        <img src='../image.php?id=11944' style='width:15px; cursor:pointer; position:absolute; top:110px; left:920px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11944' onclick='fetchAssetData(11944);' class="asset-image" data-id="<?php echo $assetId11944; ?>" data-room="<?php echo htmlspecialchars($room11944); ?>" data-floor="<?php echo htmlspecialchars($floor11944); ?>" data-image="<?php echo base64_encode($upload_img11944); ?>" data-status="<?php echo htmlspecialchars($status11944); ?>" data-category="<?php echo htmlspecialchars($category11944); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11944); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11944); ?>; position:absolute; top:105px; left:935px;'>
                        </div>

                        <!-- ASSET 11945 -->
                        <img src='../image.php?id=11945' style='width:15px; cursor:pointer; position:absolute; top:110px; left:940px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11945' onclick='fetchAssetData(11945);' class="asset-image" data-id="<?php echo $assetId11945; ?>" data-room="<?php echo htmlspecialchars($room11945); ?>" data-floor="<?php echo htmlspecialchars($floor11945); ?>" data-image="<?php echo base64_encode($upload_img11945); ?>" data-status="<?php echo htmlspecialchars($status11945); ?>" data-category="<?php echo htmlspecialchars($category11945); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11945); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11945); ?>; position:absolute; top:105px; left:955px;'>
                        </div>

                        <!-- ASSET 11946 -->
                        <img src='../image.php?id=11946' style='width:15px; cursor:pointer; position:absolute; top:110px; left:960px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11946' onclick='fetchAssetData(11946);' class="asset-image" data-id="<?php echo $assetId11946; ?>" data-room="<?php echo htmlspecialchars($room11946); ?>" data-floor="<?php echo htmlspecialchars($floor11946); ?>" data-image="<?php echo base64_encode($upload_img11946); ?>" data-status="<?php echo htmlspecialchars($status11946); ?>" data-category="<?php echo htmlspecialchars($category11946); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11946); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11946); ?>; position:absolute; top:105px; left:975px;'>
                        </div>

                        <!-- ASSET 11947 -->
                        <img src='../image.php?id=11947' style='width:15px; cursor:pointer; position:absolute; top:110px; left:980px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11947' onclick='fetchAssetData(11947);' class="asset-image" data-id="<?php echo $assetId11947; ?>" data-room="<?php echo htmlspecialchars($room11947); ?>" data-floor="<?php echo htmlspecialchars($floor11947); ?>" data-image="<?php echo base64_encode($upload_img11947); ?>" data-status="<?php echo htmlspecialchars($status11947); ?>" data-category="<?php echo htmlspecialchars($category11947); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11947); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11947); ?>; position:absolute; top:105px; left:995px;'>
                        </div>

                        <!-- ASSET 11948 -->
                        <img src='../image.php?id=11948' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1000px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11948' onclick='fetchAssetData(11948);' class="asset-image" data-id="<?php echo $assetId11948; ?>" data-room="<?php echo htmlspecialchars($room11948); ?>" data-floor="<?php echo htmlspecialchars($floor11948); ?>" data-image="<?php echo base64_encode($upload_img11948); ?>" data-status="<?php echo htmlspecialchars($status11948); ?>" data-category="<?php echo htmlspecialchars($category11948); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11948); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11948); ?>; position:absolute; top:105px; left:1015px;'>
                        </div>

                        <!-- ASSET 11949 -->
                        <img src='../image.php?id=11949' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11949' onclick='fetchAssetData(11949);' class="asset-image" data-id="<?php echo $assetId11949; ?>" data-room="<?php echo htmlspecialchars($room11949); ?>" data-floor="<?php echo htmlspecialchars($floor11949); ?>" data-image="<?php echo base64_encode($upload_img11949); ?>" data-status="<?php echo htmlspecialchars($status11949); ?>" data-category="<?php echo htmlspecialchars($category11949); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11949); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11949); ?>; position:absolute; top:105px; left:1035px;'>
                        </div>

                        <!-- ASSET 11950 -->
                        <img src='../image.php?id=11950' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1040px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11950' onclick='fetchAssetData(11950);' class="asset-image" data-id="<?php echo $assetId11950; ?>" data-room="<?php echo htmlspecialchars($room11950); ?>" data-floor="<?php echo htmlspecialchars($floor11950); ?>" data-image="<?php echo base64_encode($upload_img11950); ?>" data-status="<?php echo htmlspecialchars($status11950); ?>" data-category="<?php echo htmlspecialchars($category11950); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11950); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11950); ?>; position:absolute; top:105px; left:1055px;'>
                        </div>

                        <!-- ASSET 11951 -->
                        <img src='../image.php?id=11951' style='width:15px; cursor:pointer; position:absolute; top:110px; left:1060px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11951' onclick='fetchAssetData(11951);' class="asset-image" data-id="<?php echo $assetId11951; ?>" data-room="<?php echo htmlspecialchars($room11951); ?>" data-floor="<?php echo htmlspecialchars($floor11951); ?>" data-image="<?php echo base64_encode($upload_img11951); ?>" data-status="<?php echo htmlspecialchars($status11951); ?>" data-category="<?php echo htmlspecialchars($category11951); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11951); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11951); ?>; position:absolute; top:105px; left:1075px;'>
                        </div>

                        <!-- ASSET 11952 -->
                        <img src='../image.php?id=11952' style='width:15px; cursor:pointer; position:absolute; top:155px; left:900px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11952' onclick='fetchAssetData(11952);' class="asset-image" data-id="<?php echo $assetId11952; ?>" data-room="<?php echo htmlspecialchars($room11952); ?>" data-floor="<?php echo htmlspecialchars($floor11952); ?>" data-image="<?php echo base64_encode($upload_img11952); ?>" data-status="<?php echo htmlspecialchars($status11952); ?>" data-category="<?php echo htmlspecialchars($category11952); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11952); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11952); ?>; position:absolute; top:150px; left:915px;'>
                        </div>

                        <!-- ASSET 11953 -->
                        <img src='../image.php?id=11953' style='width:15px; cursor:pointer; position:absolute; top:155px; left:920px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11953' onclick='fetchAssetData(11953);' class="asset-image" data-id="<?php echo $assetId11953; ?>" data-room="<?php echo htmlspecialchars($room11953); ?>" data-floor="<?php echo htmlspecialchars($floor11953); ?>" data-image="<?php echo base64_encode($upload_img11953); ?>" data-status="<?php echo htmlspecialchars($status11953); ?>" data-category="<?php echo htmlspecialchars($category11953); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11953); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11953); ?>; position:absolute; top:150px; left:935px;'>
                        </div>

                        <!-- ASSET 11954 -->
                        <img src='../image.php?id=11954' style='width:15px; cursor:pointer; position:absolute; top:155px; left:940px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11954' onclick='fetchAssetData(11954);' class="asset-image" data-id="<?php echo $assetId11954; ?>" data-room="<?php echo htmlspecialchars($room11954); ?>" data-floor="<?php echo htmlspecialchars($floor11954); ?>" data-image="<?php echo base64_encode($upload_img11954); ?>" data-status="<?php echo htmlspecialchars($status11954); ?>" data-category="<?php echo htmlspecialchars($category11954); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11954); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11954); ?>; position:absolute; top:150px; left:955px;'>
                        </div>

                        <!-- ASSET 11955 -->
                        <img src='../image.php?id=11955' style='width:15px; cursor:pointer; position:absolute; top:155px; left:960px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11955' onclick='fetchAssetData(11955);' class="asset-image" data-id="<?php echo $assetId11955; ?>" data-room="<?php echo htmlspecialchars($room11955); ?>" data-floor="<?php echo htmlspecialchars($floor11955); ?>" data-image="<?php echo base64_encode($upload_img11955); ?>" data-status="<?php echo htmlspecialchars($status11955); ?>" data-category="<?php echo htmlspecialchars($category11955); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11955); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11955); ?>; position:absolute; top:150px; left:975px;'>
                        </div>

                        <!-- ASSET 11956 -->
                        <img src='../image.php?id=11956' style='width:15px; cursor:pointer; position:absolute; top:155px; left:980px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11956' onclick='fetchAssetData(11956);' class="asset-image" data-id="<?php echo $assetId11956; ?>" data-room="<?php echo htmlspecialchars($room11956); ?>" data-floor="<?php echo htmlspecialchars($floor11956); ?>" data-image="<?php echo base64_encode($upload_img11956); ?>" data-status="<?php echo htmlspecialchars($status11956); ?>" data-category="<?php echo htmlspecialchars($category11956); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11956); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11956); ?>; position:absolute; top:150px; left:995px;'>
                        </div>

                        <!-- ASSET 11957 -->
                        <img src='../image.php?id=11957' style='width:15px; cursor:pointer; position:absolute; top:155px; left:1000px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11957' onclick='fetchAssetData(11957);' class="asset-image" data-id="<?php echo $assetId11957; ?>" data-room="<?php echo htmlspecialchars($room11957); ?>" data-floor="<?php echo htmlspecialchars($floor11957); ?>" data-image="<?php echo base64_encode($upload_img11957); ?>" data-status="<?php echo htmlspecialchars($status11957); ?>" data-category="<?php echo htmlspecialchars($category11957); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11957); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11957); ?>; position:absolute; top:150px; left:1015px;'>
                        </div>

                        <!-- ASSET 11958 -->
                        <img src='../image.php?id=11958' style='width:15px; cursor:pointer; position:absolute; top:155px; left:1020px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11958' onclick='fetchAssetData(11958);' class="asset-image" data-id="<?php echo $assetId11958; ?>" data-room="<?php echo htmlspecialchars($room11958); ?>" data-floor="<?php echo htmlspecialchars($floor11958); ?>" data-image="<?php echo base64_encode($upload_img11958); ?>" data-status="<?php echo htmlspecialchars($status11958); ?>" data-category="<?php echo htmlspecialchars($category11958); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11958); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11958); ?>; position:absolute; top:150px; left:1035px;'>
                        </div>

                        <!-- ASSET 11959 -->
                        <img src='../image.php?id=11959' style='width:15px; cursor:pointer; position:absolute; top:155px; left:1040px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11959' onclick='fetchAssetData(11959);' class="asset-image" data-id="<?php echo $assetId11959; ?>" data-room="<?php echo htmlspecialchars($room11959); ?>" data-floor="<?php echo htmlspecialchars($floor11959); ?>" data-image="<?php echo base64_encode($upload_img11959); ?>" data-status="<?php echo htmlspecialchars($status11959); ?>" data-category="<?php echo htmlspecialchars($category11959); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11959); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11959); ?>; position:absolute; top:150px; left:1055px;'>
                        </div>

                        <!-- ASSET 11960 -->
                        <img src='../image.php?id=11960' style='width:15px; cursor:pointer; position:absolute; top:155px; left:1060px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11960' onclick='fetchAssetData(11960);' class="asset-image" data-id="<?php echo $assetId11960; ?>" data-room="<?php echo htmlspecialchars($room11960); ?>" data-floor="<?php echo htmlspecialchars($floor11960); ?>" data-image="<?php echo base64_encode($upload_img11960); ?>" data-status="<?php echo htmlspecialchars($status11960); ?>" data-category="<?php echo htmlspecialchars($category11960); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11960); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11960); ?>; position:absolute; top:150px; left:1075px;'>
                        </div>

                        <!-- ASSET 11961 -->
                        <img src='../image.php?id=11961' style='width:15px; cursor:pointer; position:absolute; top:185px; left:900px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11961' onclick='fetchAssetData(11961);' class="asset-image" data-id="<?php echo $assetId11961; ?>" data-room="<?php echo htmlspecialchars($room11961); ?>" data-floor="<?php echo htmlspecialchars($floor11961); ?>" data-image="<?php echo base64_encode($upload_img11961); ?>" data-status="<?php echo htmlspecialchars($status11961); ?>" data-category="<?php echo htmlspecialchars($category11961); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11961); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11961); ?>; position:absolute; top:180px; left:915px;'>
                        </div>

                        <!-- ASSET 11962 -->
                        <img src='../image.php?id=11962' style='width:15px; cursor:pointer; position:absolute; top:185px; left:920px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11962' onclick='fetchAssetData(11962);' class="asset-image" data-id="<?php echo $assetId11962; ?>" data-room="<?php echo htmlspecialchars($room11962); ?>" data-floor="<?php echo htmlspecialchars($floor11962); ?>" data-image="<?php echo base64_encode($upload_img11962); ?>" data-status="<?php echo htmlspecialchars($status11962); ?>" data-category="<?php echo htmlspecialchars($category11962); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11962); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11962); ?>; position:absolute; top:180px; left:935px;'>
                        </div>

                        <!-- ASSET 11963 -->
                        <img src='../image.php?id=11963' style='width:15px; cursor:pointer; position:absolute; top:185px; left:940px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11963' onclick='fetchAssetData(11963);' class="asset-image" data-id="<?php echo $assetId11963; ?>" data-room="<?php echo htmlspecialchars($room11963); ?>" data-floor="<?php echo htmlspecialchars($floor11963); ?>" data-image="<?php echo base64_encode($upload_img11963); ?>" data-status="<?php echo htmlspecialchars($status11963); ?>" data-category="<?php echo htmlspecialchars($category11963); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11963); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11963); ?>; position:absolute; top:180px; left:955px;'>
                        </div>

                        <!-- ASSET 11964 -->
                        <img src='../image.php?id=11964' style='width:15px; cursor:pointer; position:absolute; top:185px; left:960px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11964' onclick='fetchAssetData(11964);' class="asset-image" data-id="<?php echo $assetId11964; ?>" data-room="<?php echo htmlspecialchars($room11964); ?>" data-floor="<?php echo htmlspecialchars($floor11964); ?>" data-image="<?php echo base64_encode($upload_img11964); ?>" data-status="<?php echo htmlspecialchars($status11964); ?>" data-category="<?php echo htmlspecialchars($category11964); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11964); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11964); ?>; position:absolute; top:180px; left:975px;'>
                        </div>

                        <!-- ASSET 11965 -->
                        <img src='../image.php?id=11965' style='width:15px; cursor:pointer; position:absolute; top:185px; left:980px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11965' onclick='fetchAssetData(11965);' class="asset-image" data-id="<?php echo $assetId11965; ?>" data-room="<?php echo htmlspecialchars($room11965); ?>" data-floor="<?php echo htmlspecialchars($floor11965); ?>" data-image="<?php echo base64_encode($upload_img11965); ?>" data-status="<?php echo htmlspecialchars($status11965); ?>" data-category="<?php echo htmlspecialchars($category11965); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11965); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11965); ?>; position:absolute; top:180px; left:995px;'>
                        </div>

                        <!-- ASSET 11966 -->
                        <img src='../image.php?id=11966' style='width:15px; cursor:pointer; position:absolute; top:185px; left:1000px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11966' onclick='fetchAssetData(11966);' class="asset-image" data-id="<?php echo $assetId11966; ?>" data-room="<?php echo htmlspecialchars($room11966); ?>" data-floor="<?php echo htmlspecialchars($floor11966); ?>" data-image="<?php echo base64_encode($upload_img11966); ?>" data-status="<?php echo htmlspecialchars($status11966); ?>" data-category="<?php echo htmlspecialchars($category11966); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11966); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11966); ?>; position:absolute; top:180px; left:1015px;'>
                        </div>

                        <!-- ASSET 11967 -->
                        <img src='../image.php?id=11967' style='width:15px; cursor:pointer; position:absolute; top:185px; left:1020px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11967' onclick='fetchAssetData(11967);' class="asset-image" data-id="<?php echo $assetId11967; ?>" data-room="<?php echo htmlspecialchars($room11967); ?>" data-floor="<?php echo htmlspecialchars($floor11967); ?>" data-image="<?php echo base64_encode($upload_img11967); ?>" data-status="<?php echo htmlspecialchars($status11967); ?>" data-category="<?php echo htmlspecialchars($category11967); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11967); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11967); ?>; position:absolute; top:180px; left:1035px;'>
                        </div>

                        <!-- ASSET 11968 -->
                        <img src='../image.php?id=11968' style='width:15px; cursor:pointer; position:absolute; top:185px; left:1040px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11968' onclick='fetchAssetData(11968);' class="asset-image" data-id="<?php echo $assetId11968; ?>" data-room="<?php echo htmlspecialchars($room11968); ?>" data-floor="<?php echo htmlspecialchars($floor11968); ?>" data-image="<?php echo base64_encode($upload_img11968); ?>" data-status="<?php echo htmlspecialchars($status11968); ?>" data-category="<?php echo htmlspecialchars($category11968); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11968); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11968); ?>; position:absolute; top:180px; left:1055px;'>
                        </div>

                        <!-- ASSET 11969 -->
                        <img src='../image.php?id=11969' style='width:15px; cursor:pointer; position:absolute; top:185px; left:1060px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11969' onclick='fetchAssetData(11969);' class="asset-image" data-id="<?php echo $assetId11969; ?>" data-room="<?php echo htmlspecialchars($room11969); ?>" data-floor="<?php echo htmlspecialchars($floor11969); ?>" data-image="<?php echo base64_encode($upload_img11969); ?>" data-status="<?php echo htmlspecialchars($status11969); ?>" data-category="<?php echo htmlspecialchars($category11969); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11969); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11969); ?>; position:absolute; top:180px; left:1075px;'>
                        </div>


                        <!-- ASSET 11970 -->
                        <img src='../image.php?id=11970' style='width:15px; cursor:pointer; position:absolute; top:230px; left:900px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11970' onclick='fetchAssetData(11970);' class="asset-image" data-id="<?php echo $assetId11970; ?>" data-room="<?php echo htmlspecialchars($room11970); ?>" data-floor="<?php echo htmlspecialchars($floor11970); ?>" data-image="<?php echo base64_encode($upload_img11970); ?>" data-status="<?php echo htmlspecialchars($status11970); ?>" data-category="<?php echo htmlspecialchars($category11970); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11970); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11970); ?>; position:absolute; top:225px; left:915px;'>
                        </div>

                        <!-- ASSET 11971 -->
                        <img src='../image.php?id=11971' style='width:15px; cursor:pointer; position:absolute; top:230px; left:920px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11971' onclick='fetchAssetData(11971);' class="asset-image" data-id="<?php echo $assetId11971; ?>" data-room="<?php echo htmlspecialchars($room11971); ?>" data-floor="<?php echo htmlspecialchars($floor11971); ?>" data-image="<?php echo base64_encode($upload_img11971); ?>" data-status="<?php echo htmlspecialchars($status11971); ?>" data-category="<?php echo htmlspecialchars($category11971); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11971); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11971); ?>; position:absolute; top:225px; left:935px;'>
                        </div>

                        <!-- ASSET 11972 -->
                        <img src='../image.php?id=11972' style='width:15px; cursor:pointer; position:absolute; top:230px; left:940px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11972' onclick='fetchAssetData(11972);' class="asset-image" data-id="<?php echo $assetId11972; ?>" data-room="<?php echo htmlspecialchars($room11972); ?>" data-floor="<?php echo htmlspecialchars($floor11972); ?>" data-image="<?php echo base64_encode($upload_img11972); ?>" data-status="<?php echo htmlspecialchars($status11972); ?>" data-category="<?php echo htmlspecialchars($category11972); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11972); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11972); ?>; position:absolute; top:225px; left:955px;'>
                        </div>

                        <!-- ASSET 11973 -->
                        <img src='../image.php?id=11973' style='width:15px; cursor:pointer; position:absolute; top:230px; left:960px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11973' onclick='fetchAssetData(11973);' class="asset-image" data-id="<?php echo $assetId11973; ?>" data-room="<?php echo htmlspecialchars($room11973); ?>" data-floor="<?php echo htmlspecialchars($floor11973); ?>" data-image="<?php echo base64_encode($upload_img11973); ?>" data-status="<?php echo htmlspecialchars($status11973); ?>" data-category="<?php echo htmlspecialchars($category11973); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11973); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11973); ?>; position:absolute; top:225px; left:975px;'>
                        </div>

                        <!-- ASSET 11974 -->
                        <img src='../image.php?id=11974' style='width:15px; cursor:pointer; position:absolute; top:230px; left:980px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11974' onclick='fetchAssetData(11974);' class="asset-image" data-id="<?php echo $assetId11974; ?>" data-room="<?php echo htmlspecialchars($room11974); ?>" data-floor="<?php echo htmlspecialchars($floor11974); ?>" data-image="<?php echo base64_encode($upload_img11974); ?>" data-status="<?php echo htmlspecialchars($status11974); ?>" data-category="<?php echo htmlspecialchars($category11974); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11974); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11974); ?>; position:absolute; top:225px; left:995px;'>
                        </div>

                        <!-- ASSET 11975 -->
                        <img src='../image.php?id=11975' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1000px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11975' onclick='fetchAssetData(11975);' class="asset-image" data-id="<?php echo $assetId11975; ?>" data-room="<?php echo htmlspecialchars($room11975); ?>" data-floor="<?php echo htmlspecialchars($floor11975); ?>" data-image="<?php echo base64_encode($upload_img11975); ?>" data-status="<?php echo htmlspecialchars($status11975); ?>" data-category="<?php echo htmlspecialchars($category11975); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11975); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11975); ?>; position:absolute; top:225px; left:1015px;'>
                        </div>

                        <!-- ASSET 11976 -->
                        <img src='../image.php?id=11976' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1020px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11976' onclick='fetchAssetData(11976);' class="asset-image" data-id="<?php echo $assetId11976; ?>" data-room="<?php echo htmlspecialchars($room11976); ?>" data-floor="<?php echo htmlspecialchars($floor11976); ?>" data-image="<?php echo base64_encode($upload_img11976); ?>" data-status="<?php echo htmlspecialchars($status11976); ?>" data-category="<?php echo htmlspecialchars($category11976); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11976); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11976); ?>; position:absolute; top:225px; left:1035px;'>
                        </div>

                        <!-- ASSET 11977 -->
                        <img src='../image.php?id=11977' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1040px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11977' onclick='fetchAssetData(11977);' class="asset-image" data-id="<?php echo $assetId11977; ?>" data-room="<?php echo htmlspecialchars($room11977); ?>" data-floor="<?php echo htmlspecialchars($floor11977); ?>" data-image="<?php echo base64_encode($upload_img11977); ?>" data-status="<?php echo htmlspecialchars($status11977); ?>" data-category="<?php echo htmlspecialchars($category11977); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11977); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11977); ?>; position:absolute; top:225px; left:1055px;'>
                        </div>

                        <!-- ASSET 11978 -->
                        <img src='../image.php?id=11978' style='width:15px; cursor:pointer; position:absolute; top:230px; left:1060px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11978' onclick='fetchAssetData(11978);' class="asset-image" data-id="<?php echo $assetId11978; ?>" data-room="<?php echo htmlspecialchars($room11978); ?>" data-floor="<?php echo htmlspecialchars($floor11978); ?>" data-image="<?php echo base64_encode($upload_img11978); ?>" data-status="<?php echo htmlspecialchars($status11978); ?>" data-category="<?php echo htmlspecialchars($category11978); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11978); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11978); ?>; position:absolute; top:225px; left:1075px;'>
                        </div>

                        <!-- ASSET 11979 -->
                        <img src='../image.php?id=11979' style='width:15px; cursor:pointer; position:absolute; top:170px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11979' onclick='fetchAssetData(11979);' class="asset-image" data-id="<?php echo $assetId11979; ?>" data-room="<?php echo htmlspecialchars($room11979); ?>" data-floor="<?php echo htmlspecialchars($floor11979); ?>" data-image="<?php echo base64_encode($upload_img11979); ?>" data-status="<?php echo htmlspecialchars($status11979); ?>" data-category="<?php echo htmlspecialchars($category11979); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11979); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11979); ?>; position:absolute; top:165px; left:975px;'>
                        </div>

                        <!-- ASSET 11980 -->
                        <img src='../image.php?id=11980' style='width:15px; cursor:pointer; position:absolute; top:170px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11980' onclick='fetchAssetData(11980);' class="asset-image" data-id="<?php echo $assetId11980; ?>" data-room="<?php echo htmlspecialchars($room11980); ?>" data-floor="<?php echo htmlspecialchars($floor11980); ?>" data-image="<?php echo base64_encode($upload_img11980); ?>" data-status="<?php echo htmlspecialchars($status11980); ?>" data-category="<?php echo htmlspecialchars($category11980); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11980); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11980); ?>; position:absolute; top:165px; left:1055px;'>
                        </div>

                        <!-- ASSET 11981 -->
                        <img src='../image.php?id=11981' style='width:40px; cursor:pointer; position:absolute; top:90px; left:845px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11981' onclick='fetchAssetData(11981);' class="asset-image" data-id="<?php echo $assetId11981; ?>" data-room="<?php echo htmlspecialchars($room11981); ?>" data-floor="<?php echo htmlspecialchars($floor11981); ?>" data-image="<?php echo base64_encode($upload_img11981); ?>" data-status="<?php echo htmlspecialchars($status11981); ?>" data-category="<?php echo htmlspecialchars($category11981); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11981); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11981); ?>; position:absolute; top:85px; left:880px;'>
                        </div>

                        <!-- ASSET 11982 -->
                        <img src='../image.php?id=11982' style='width:50px; cursor:pointer; position:absolute; top:90px; left:915px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11982' onclick='fetchAssetData(11982);' class="asset-image" data-id="<?php echo $assetId11982; ?>" data-room="<?php echo htmlspecialchars($room11982); ?>" data-floor="<?php echo htmlspecialchars($floor11982); ?>" data-image="<?php echo base64_encode($upload_img11982); ?>" data-status="<?php echo htmlspecialchars($status11982); ?>" data-category="<?php echo htmlspecialchars($category11982); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11982); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11982); ?>; position:absolute; top:85px; left:950px;'>
                        </div>

                        <!-- ASSET 11983 -->
                        <img src='../image.php?id=11983' style='width:50px; cursor:pointer; position:absolute; top:90px; left:1000px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11983' onclick='fetchAssetData(11983);' class="asset-image" data-id="<?php echo $assetId11983; ?>" data-room="<?php echo htmlspecialchars($room11983); ?>" data-floor="<?php echo htmlspecialchars($floor11983); ?>" data-image="<?php echo base64_encode($upload_img11983); ?>" data-status="<?php echo htmlspecialchars($status11983); ?>" data-category="<?php echo htmlspecialchars($category11983); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11983); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11983); ?>; position:absolute; top:85px; left:1050px;'>
                        </div>

                        <!-- ASSET 11984 -->
                        <img src='../image.php?id=11984' style='width:50px; cursor:pointer; position:absolute; top:90px; left:1090px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11984' onclick='fetchAssetData(11984);' class="asset-image" data-id="<?php echo $assetId11984; ?>" data-room="<?php echo htmlspecialchars($room11984); ?>" data-floor="<?php echo htmlspecialchars($floor11984); ?>" data-image="<?php echo base64_encode($upload_img11984); ?>" data-status="<?php echo htmlspecialchars($status11984); ?>" data-category="<?php echo htmlspecialchars($category11984); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11984); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11984); ?>; position:absolute; top:85px; left:1130px;'>
                        </div>

                        <!-- ASSET 11985 -->
                        <img src='../image.php?id=11985' style='width:15px; cursor:pointer; position:absolute; top:340px; left:1060px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11985' onclick='fetchAssetData(11985);' class="asset-image" data-id="<?php echo $assetId11985; ?>" data-room="<?php echo htmlspecialchars($room11985); ?>" data-floor="<?php echo htmlspecialchars($floor11985); ?>" data-image="<?php echo base64_encode($upload_img11985); ?>" data-status="<?php echo htmlspecialchars($status11985); ?>" data-category="<?php echo htmlspecialchars($category11985); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11985); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11985); ?>; position:absolute; top:335px; left:1070px;'>
                        </div>

                        <!-- ASSET 11986 -->
                        <img src='../image.php?id=11986' style='width:15px; cursor:pointer; position:absolute; top:450px; left:1060px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11986' onclick='fetchAssetData(11986);' class="asset-image" data-id="<?php echo $assetId11986; ?>" data-room="<?php echo htmlspecialchars($room11986); ?>" data-floor="<?php echo htmlspecialchars($floor11986); ?>" data-image="<?php echo base64_encode($upload_img11986); ?>" data-status="<?php echo htmlspecialchars($status11986); ?>" data-category="<?php echo htmlspecialchars($category11986); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11986); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11986); ?>; position:absolute; top:445px; left:1070px;'>
                        </div>

                        <!-- ASSET 11987 -->
                        <img src='../image.php?id=11987' style='width:15px; cursor:pointer; position:absolute; top:300px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11987' onclick='fetchAssetData(11987);' class="asset-image" data-id="<?php echo $assetId11987; ?>" data-room="<?php echo htmlspecialchars($room11987); ?>" data-floor="<?php echo htmlspecialchars($floor11987); ?>" data-image="<?php echo base64_encode($upload_img11987); ?>" data-status="<?php echo htmlspecialchars($status11987); ?>" data-category="<?php echo htmlspecialchars($category11987); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11987); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11987); ?>; position:absolute; top:295px; left:95px;'>
                        </div>

                        <!-- ASSET 11988 -->
                        <img src='../image.php?id=11988' style='width:15px; cursor:pointer; position:absolute; top:190px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11988' onclick='fetchAssetData(11988);' class="asset-image" data-id="<?php echo $assetId11988; ?>" data-room="<?php echo htmlspecialchars($room11988); ?>" data-floor="<?php echo htmlspecialchars($floor11988); ?>" data-image="<?php echo base64_encode($upload_img11988); ?>" data-status="<?php echo htmlspecialchars($status11988); ?>" data-category="<?php echo htmlspecialchars($category11988); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11988); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11988); ?>; position:absolute; top:185px; left:95px;'>
                        </div>

                        <!-- ASSET 11989 -->
                        <img src='../image.php?id=11989' style='width:15px; cursor:pointer; position:absolute; top:335px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11989' onclick='fetchAssetData(11989);' class="asset-image" data-id="<?php echo $assetId11989; ?>" data-room="<?php echo htmlspecialchars($room11989); ?>" data-floor="<?php echo htmlspecialchars($floor11989); ?>" data-image="<?php echo base64_encode($upload_img11989); ?>" data-status="<?php echo htmlspecialchars($status11989); ?>" data-category="<?php echo htmlspecialchars($category11989); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11989); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11989); ?>; position:absolute; top:330px; left:800px;'>
                        </div>

                        <!-- ASSET 11990 -->
                        <img src='../image.php?id=11990' style='width:15px; cursor:pointer; position:absolute; top:335px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11990' onclick='fetchAssetData(11990);' class="asset-image" data-id="<?php echo $assetId11990; ?>" data-room="<?php echo htmlspecialchars($room11990); ?>" data-floor="<?php echo htmlspecialchars($floor11990); ?>" data-image="<?php echo base64_encode($upload_img11990); ?>" data-status="<?php echo htmlspecialchars($status11990); ?>" data-category="<?php echo htmlspecialchars($category11990); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11990); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11990); ?>; position:absolute; top:330px; left:840px;'>
                        </div>

                        <!-- ASSET 11991 -->
                        <img src='../image.php?id=11991' style='width:15px; cursor:pointer; position:absolute; top:335px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11991' onclick='fetchAssetData(11991);' class="asset-image" data-id="<?php echo $assetId11991; ?>" data-room="<?php echo htmlspecialchars($room11991); ?>" data-floor="<?php echo htmlspecialchars($floor11991); ?>" data-image="<?php echo base64_encode($upload_img11991); ?>" data-status="<?php echo htmlspecialchars($status11991); ?>" data-category="<?php echo htmlspecialchars($category11991); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11991); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11991); ?>; position:absolute; top:330px; left:880px;'>
                        </div>

                        <!-- ASSET 11992 -->
                        <img src='../image.php?id=11992' style='width:15px; cursor:pointer; position:absolute; top:335px; left:930px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11992' onclick='fetchAssetData(11992);' class="asset-image" data-id="<?php echo $assetId11992; ?>" data-room="<?php echo htmlspecialchars($room11992); ?>" data-floor="<?php echo htmlspecialchars($floor11992); ?>" data-image="<?php echo base64_encode($upload_img11992); ?>" data-status="<?php echo htmlspecialchars($status11992); ?>" data-category="<?php echo htmlspecialchars($category11992); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11992); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11992); ?>; position:absolute; top:330px; left:940px;'>
                        </div>

                        <!-- ASSET 11993 -->
                        <img src='../image.php?id=11993' style='width:15px; cursor:pointer; position:absolute; top:335px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11993' onclick='fetchAssetData(11993);' class="asset-image" data-id="<?php echo $assetId11993; ?>" data-room="<?php echo htmlspecialchars($room11993); ?>" data-floor="<?php echo htmlspecialchars($floor11993); ?>" data-image="<?php echo base64_encode($upload_img11993); ?>" data-status="<?php echo htmlspecialchars($status11993); ?>" data-category="<?php echo htmlspecialchars($category11993); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11993); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11993); ?>; position:absolute; top:330px; left:980px;'>
                        </div>

                        <!-- ASSET 11994 -->
                        <img src='../image.php?id=11994' style='width:15px; cursor:pointer; position:absolute; top:335px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11994' onclick='fetchAssetData(11994);' class="asset-image" data-id="<?php echo $assetId11994; ?>" data-room="<?php echo htmlspecialchars($room11994); ?>" data-floor="<?php echo htmlspecialchars($floor11994); ?>" data-image="<?php echo base64_encode($upload_img11994); ?>" data-status="<?php echo htmlspecialchars($status11994); ?>" data-category="<?php echo htmlspecialchars($category11994); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11994); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11994); ?>; position:absolute; top:330px; left:1020px;'>
                        </div>

                        <!-- ASSET 11995 -->
                        <img src='../image.php?id=11995' style='width:15px; cursor:pointer; position:absolute; top:375px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11995' onclick='fetchAssetData(11995);' class="asset-image" data-id="<?php echo $assetId11995; ?>" data-room="<?php echo htmlspecialchars($room11995); ?>" data-floor="<?php echo htmlspecialchars($floor11995); ?>" data-image="<?php echo base64_encode($upload_img11995); ?>" data-status="<?php echo htmlspecialchars($status11995); ?>" data-category="<?php echo htmlspecialchars($category11995); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11995); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11995); ?>; position:absolute; top:370px; left:800px;'>
                        </div>

                        <!-- ASSET 11996 -->
                        <img src='../image.php?id=11996' style='width:15px; cursor:pointer; position:absolute; top:375px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11996' onclick='fetchAssetData(11996);' class="asset-image" data-id="<?php echo $assetId11996; ?>" data-room="<?php echo htmlspecialchars($room11996); ?>" data-floor="<?php echo htmlspecialchars($floor11996); ?>" data-image="<?php echo base64_encode($upload_img11996); ?>" data-status="<?php echo htmlspecialchars($status11996); ?>" data-category="<?php echo htmlspecialchars($category11996); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11996); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11996); ?>; position:absolute; top:370px; left:840px;'>
                        </div>

                        <!-- ASSET 11997 -->
                        <img src='../image.php?id=11997' style='width:15px; cursor:pointer; position:absolute; top:375px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11997' onclick='fetchAssetData(11997);' class="asset-image" data-id="<?php echo $assetId11997; ?>" data-room="<?php echo htmlspecialchars($room11997); ?>" data-floor="<?php echo htmlspecialchars($floor11997); ?>" data-image="<?php echo base64_encode($upload_img11997); ?>" data-status="<?php echo htmlspecialchars($status11997); ?>" data-category="<?php echo htmlspecialchars($category11997); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11997); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11997); ?>; position:absolute; top:370px; left:880px;'>
                        </div>

                        <!-- ASSET 11998 -->
                        <img src='../image.php?id=11998' style='width:15px; cursor:pointer; position:absolute; top:375px; left:930px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11998' onclick='fetchAssetData(11998);' class="asset-image" data-id="<?php echo $assetId11998; ?>" data-room="<?php echo htmlspecialchars($room11998); ?>" data-floor="<?php echo htmlspecialchars($floor11998); ?>" data-image="<?php echo base64_encode($upload_img11998); ?>" data-status="<?php echo htmlspecialchars($status11998); ?>" data-category="<?php echo htmlspecialchars($category11998); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11998); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11998); ?>; position:absolute; top:370px; left:940px;'>
                        </div>

                        <!-- ASSET 11999 -->
                        <img src='../image.php?id=11999' style='width:15px; cursor:pointer; position:absolute; top:375px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11999' onclick='fetchAssetData(11999);' class="asset-image" data-id="<?php echo $assetId11999; ?>" data-room="<?php echo htmlspecialchars($room11999); ?>" data-floor="<?php echo htmlspecialchars($floor11999); ?>" data-image="<?php echo base64_encode($upload_img11999); ?>" data-status="<?php echo htmlspecialchars($status11999); ?>" data-category="<?php echo htmlspecialchars($category11999); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11999); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11999); ?>; position:absolute; top:370px; left:980px;'>
                        </div>

                        <!-- ASSET 12000 -->
                        <img src='../image.php?id=12000' style='width:15px; cursor:pointer; position:absolute; top:375px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12000' onclick='fetchAssetData(12000);' class="asset-image" data-id="<?php echo $assetId12000; ?>" data-room="<?php echo htmlspecialchars($room12000); ?>" data-floor="<?php echo htmlspecialchars($floor12000); ?>" data-image="<?php echo base64_encode($upload_img12000); ?>" data-status="<?php echo htmlspecialchars($status12000); ?>" data-category="<?php echo htmlspecialchars($category12000); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12000); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12000); ?>; position:absolute; top:370px; left:1020px;'>
                        </div>

                        <!-- ASSET 12001 -->
                        <img src='../image.php?id=12001' style='width:15px; cursor:pointer; position:absolute; top:415px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12001' onclick='fetchAssetData(12001);' class="asset-image" data-id="<?php echo $assetId12001; ?>" data-room="<?php echo htmlspecialchars($room12001); ?>" data-floor="<?php echo htmlspecialchars($floor12001); ?>" data-image="<?php echo base64_encode($upload_img12001); ?>" data-status="<?php echo htmlspecialchars($status12001); ?>" data-category="<?php echo htmlspecialchars($category12001); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12001); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12001); ?>; position:absolute; top:410px; left:800px;'>
                        </div>

                        <!-- ASSET 12002 -->
                        <img src='../image.php?id=12002' style='width:15px; cursor:pointer; position:absolute; top:415px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12002' onclick='fetchAssetData(12002);' class="asset-image" data-id="<?php echo $assetId12002; ?>" data-room="<?php echo htmlspecialchars($room12002); ?>" data-floor="<?php echo htmlspecialchars($floor12002); ?>" data-image="<?php echo base64_encode($upload_img12002); ?>" data-status="<?php echo htmlspecialchars($status12002); ?>" data-category="<?php echo htmlspecialchars($category12002); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12002); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12002); ?>; position:absolute; top:410px; left:840px;'>
                        </div>

                        <!-- ASSET 12003 -->
                        <img src='../image.php?id=12003' style='width:15px; cursor:pointer; position:absolute; top:415px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12003' onclick='fetchAssetData(12003);' class="asset-image" data-id="<?php echo $assetId12003; ?>" data-room="<?php echo htmlspecialchars($room12003); ?>" data-floor="<?php echo htmlspecialchars($floor12003); ?>" data-image="<?php echo base64_encode($upload_img12003); ?>" data-status="<?php echo htmlspecialchars($status12003); ?>" data-category="<?php echo htmlspecialchars($category12003); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12003); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12003); ?>; position:absolute; top:410px; left:880px;'>
                        </div>

                        <!-- ASSET 12004 -->
                        <img src='../image.php?id=12004' style='width:15px; cursor:pointer; position:absolute; top:415px; left:930px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12004' onclick='fetchAssetData(12004);' class="asset-image" data-id="<?php echo $assetId12004; ?>" data-room="<?php echo htmlspecialchars($room12004); ?>" data-floor="<?php echo htmlspecialchars($floor12004); ?>" data-image="<?php echo base64_encode($upload_img12004); ?>" data-status="<?php echo htmlspecialchars($status12004); ?>" data-category="<?php echo htmlspecialchars($category12004); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12004); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12004); ?>; position:absolute; top:410px; left:940px;'>
                        </div>

                        <!-- ASSET 12005 -->
                        <img src='../image.php?id=12005' style='width:15px; cursor:pointer; position:absolute; top:415px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12005' onclick='fetchAssetData(12005);' class="asset-image" data-id="<?php echo $assetId12005; ?>" data-room="<?php echo htmlspecialchars($room12005); ?>" data-floor="<?php echo htmlspecialchars($floor12005); ?>" data-image="<?php echo base64_encode($upload_img12005); ?>" data-status="<?php echo htmlspecialchars($status12005); ?>" data-category="<?php echo htmlspecialchars($category12005); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12005); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12005); ?>; position:absolute; top:410px; left:980px;'>
                        </div>

                        <!-- ASSET 12006 -->
                        <img src='../image.php?id=12006' style='width:15px; cursor:pointer; position:absolute; top:415px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12006' onclick='fetchAssetData(12006);' class="asset-image" data-id="<?php echo $assetId12006; ?>" data-room="<?php echo htmlspecialchars($room12006); ?>" data-floor="<?php echo htmlspecialchars($floor12006); ?>" data-image="<?php echo base64_encode($upload_img12006); ?>" data-status="<?php echo htmlspecialchars($status12006); ?>" data-category="<?php echo htmlspecialchars($category12006); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12006); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12006); ?>; position:absolute; top:410px; left:1020px;'>
                        </div>


                        <!-- ASSET 12007 -->
                        <img src='../image.php?id=12007' style='width:15px; cursor:pointer; position:absolute; top:455px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12007' onclick='fetchAssetData(12007);' class="asset-image" data-id="<?php echo $assetId12007; ?>" data-room="<?php echo htmlspecialchars($room12007); ?>" data-floor="<?php echo htmlspecialchars($floor12007); ?>" data-image="<?php echo base64_encode($upload_img12007); ?>" data-status="<?php echo htmlspecialchars($status12007); ?>" data-category="<?php echo htmlspecialchars($category12007); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12007); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12007); ?>; position:absolute; top:450px; left:800px;'>
                        </div>

                        <!-- ASSET 12008 -->
                        <img src='../image.php?id=12008' style='width:15px; cursor:pointer; position:absolute; top:455px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12008' onclick='fetchAssetData(12008);' class="asset-image" data-id="<?php echo $assetId12008; ?>" data-room="<?php echo htmlspecialchars($room12008); ?>" data-floor="<?php echo htmlspecialchars($floor12008); ?>" data-image="<?php echo base64_encode($upload_img12008); ?>" data-status="<?php echo htmlspecialchars($status12008); ?>" data-category="<?php echo htmlspecialchars($category12008); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12008); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12008); ?>; position:absolute; top:450px; left:840px;'>
                        </div>

                        <!-- ASSET 12009 -->
                        <img src='../image.php?id=12009' style='width:15px; cursor:pointer; position:absolute; top:455px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12009' onclick='fetchAssetData(12009);' class="asset-image" data-id="<?php echo $assetId12009; ?>" data-room="<?php echo htmlspecialchars($room12009); ?>" data-floor="<?php echo htmlspecialchars($floor12009); ?>" data-image="<?php echo base64_encode($upload_img12009); ?>" data-status="<?php echo htmlspecialchars($status12009); ?>" data-category="<?php echo htmlspecialchars($category12009); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12009); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12009); ?>; position:absolute; top:450px; left:880px;'>
                        </div>

                        <!-- ASSET 12010 -->
                        <img src='../image.php?id=12010' style='width:15px; cursor:pointer; position:absolute; top:455px; left:930px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12010' onclick='fetchAssetData(12010);' class="asset-image" data-id="<?php echo $assetId12010; ?>" data-room="<?php echo htmlspecialchars($room12010); ?>" data-floor="<?php echo htmlspecialchars($floor12010); ?>" data-image="<?php echo base64_encode($upload_img12010); ?>" data-status="<?php echo htmlspecialchars($status12010); ?>" data-category="<?php echo htmlspecialchars($category12010); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12010); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12010); ?>; position:absolute; top:450px; left:940px;'>
                        </div>

                        <!-- ASSET 12011 -->
                        <img src='../image.php?id=12011' style='width:15px; cursor:pointer; position:absolute; top:455px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12011' onclick='fetchAssetData(12011);' class="asset-image" data-id="<?php echo $assetId12011; ?>" data-room="<?php echo htmlspecialchars($room12011); ?>" data-floor="<?php echo htmlspecialchars($floor12011); ?>" data-image="<?php echo base64_encode($upload_img12011); ?>" data-status="<?php echo htmlspecialchars($status12011); ?>" data-category="<?php echo htmlspecialchars($category12011); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12011); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12011); ?>; position:absolute; top:450px; left:980px;'>
                        </div>

                        <!-- ASSET 12012 -->
                        <img src='../image.php?id=12012' style='width:15px; cursor:pointer; position:absolute; top:455px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12012' onclick='fetchAssetData(12012);' class="asset-image" data-id="<?php echo $assetId12012; ?>" data-room="<?php echo htmlspecialchars($room12012); ?>" data-floor="<?php echo htmlspecialchars($floor12012); ?>" data-image="<?php echo base64_encode($upload_img12012); ?>" data-status="<?php echo htmlspecialchars($status12012); ?>" data-category="<?php echo htmlspecialchars($category12012); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12012); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12012); ?>; position:absolute; top:450px; left:1020px;'>
                        </div>

                        <!-- ASSET 12013 -->
                        <img src='../image.php?id=12013' style='width:20px; cursor:pointer; position:absolute; top:350px; left:800px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12013' onclick='fetchAssetData(12013);' class="asset-image" data-id="<?php echo $assetId12013; ?>" data-room="<?php echo htmlspecialchars($room12013); ?>" data-floor="<?php echo htmlspecialchars($floor12013); ?>" data-image="<?php echo base64_encode($upload_img12013); ?>" data-status="<?php echo htmlspecialchars($status12013); ?>" data-category="<?php echo htmlspecialchars($category12013); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12013); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12013); ?>; position:absolute; top:345px; left:815px;'>
                        </div>

                        <!-- ASSET 12014 -->
                        <img src='../image.php?id=12014' style='width:20px; cursor:pointer; position:absolute; top:350px; left:820px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12014' onclick='fetchAssetData(12014);' class="asset-image" data-id="<?php echo $assetId12014; ?>" data-room="<?php echo htmlspecialchars($room12014); ?>" data-floor="<?php echo htmlspecialchars($floor12014); ?>" data-image="<?php echo base64_encode($upload_img12014); ?>" data-status="<?php echo htmlspecialchars($status12014); ?>" data-category="<?php echo htmlspecialchars($category12014); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12014); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12014); ?>; position:absolute; top:345px; left:835px;'>
                        </div>

                        <!-- ASSET 12015 -->
                        <img src='../image.php?id=12015' style='width:20px; cursor:pointer; position:absolute; top:350px; left:840px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12015' onclick='fetchAssetData(12015);' class="asset-image" data-id="<?php echo $assetId12015; ?>" data-room="<?php echo htmlspecialchars($room12015); ?>" data-floor="<?php echo htmlspecialchars($floor12015); ?>" data-image="<?php echo base64_encode($upload_img12015); ?>" data-status="<?php echo htmlspecialchars($status12015); ?>" data-category="<?php echo htmlspecialchars($category12015); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12015); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12015); ?>; position:absolute; top:345px; left:855px;'>
                        </div>

                        <!-- ASSET 12016 -->
                        <img src='../image.php?id=12016' style='width:20px; cursor:pointer; position:absolute; top:350px; left:860px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12016' onclick='fetchAssetData(12016);' class="asset-image" data-id="<?php echo $assetId12016; ?>" data-room="<?php echo htmlspecialchars($room12016); ?>" data-floor="<?php echo htmlspecialchars($floor12016); ?>" data-image="<?php echo base64_encode($upload_img12016); ?>" data-status="<?php echo htmlspecialchars($status12016); ?>" data-category="<?php echo htmlspecialchars($category12016); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12016); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12016); ?>; position:absolute; top:345px; left:875px;'>
                        </div>

                        <!-- ASSET 12017 -->
                        <img src='../image.php?id=12017' style='width:20px; cursor:pointer; position:absolute; top:350px; left:880px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12017' onclick='fetchAssetData(12017);' class="asset-image" data-id="<?php echo $assetId12017; ?>" data-room="<?php echo htmlspecialchars($room12017); ?>" data-floor="<?php echo htmlspecialchars($floor12017); ?>" data-image="<?php echo base64_encode($upload_img12017); ?>" data-status="<?php echo htmlspecialchars($status12017); ?>" data-category="<?php echo htmlspecialchars($category12017); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12017); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12017); ?>; position:absolute; top:345px; left:895px;'>
                        </div>

                        <!-- ASSET 12018 -->
                        <img src='../image.php?id=12018' style='width:20px; cursor:pointer; position:absolute; top:350px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12018' onclick='fetchAssetData(12018);' class="asset-image" data-id="<?php echo $assetId12018; ?>" data-room="<?php echo htmlspecialchars($room12018); ?>" data-floor="<?php echo htmlspecialchars($floor12018); ?>" data-image="<?php echo base64_encode($upload_img12018); ?>" data-status="<?php echo htmlspecialchars($status12018); ?>" data-category="<?php echo htmlspecialchars($category12018); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12018); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12018); ?>; position:absolute; top:345px; left:915px;'>
                        </div>

                        <!-- ASSET 12019 -->
                        <img src='../image.php?id=12019' style='width:20px; cursor:pointer; position:absolute; top:350px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12019' onclick='fetchAssetData(12019);' class="asset-image" data-id="<?php echo $assetId12019; ?>" data-room="<?php echo htmlspecialchars($room12019); ?>" data-floor="<?php echo htmlspecialchars($floor12019); ?>" data-image="<?php echo base64_encode($upload_img12019); ?>" data-status="<?php echo htmlspecialchars($status12019); ?>" data-category="<?php echo htmlspecialchars($category12019); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12019); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12019); ?>; position:absolute; top:345px; left:935px;'>
                        </div>

                        <!-- ASSET 12020 -->
                        <img src='../image.php?id=12020' style='width:20px; cursor:pointer; position:absolute; top:350px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12020' onclick='fetchAssetData(12020);' class="asset-image" data-id="<?php echo $assetId12020; ?>" data-room="<?php echo htmlspecialchars($room12020); ?>" data-floor="<?php echo htmlspecialchars($floor12020); ?>" data-image="<?php echo base64_encode($upload_img12020); ?>" data-status="<?php echo htmlspecialchars($status12020); ?>" data-category="<?php echo htmlspecialchars($category12020); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12020); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12020); ?>; position:absolute; top:345px; left:955px;'>
                        </div>

                        <!-- ASSET 12021 -->
                        <img src='../image.php?id=12021' style='width:20px; cursor:pointer; position:absolute; top:350px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12021' onclick='fetchAssetData(12021);' class="asset-image" data-id="<?php echo $assetId12021; ?>" data-room="<?php echo htmlspecialchars($room12021); ?>" data-floor="<?php echo htmlspecialchars($floor12021); ?>" data-image="<?php echo base64_encode($upload_img12021); ?>" data-status="<?php echo htmlspecialchars($status12021); ?>" data-category="<?php echo htmlspecialchars($category12021); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12021); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12021); ?>; position:absolute; top:345px; left:975px;'>
                        </div>

                        <!-- ASSET 12022 -->
                        <img src='../image.php?id=12022' style='width:20px; cursor:pointer; position:absolute; top:365px; left:800px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12022' onclick='fetchAssetData(12022);' class="asset-image" data-id="<?php echo $assetId12022; ?>" data-room="<?php echo htmlspecialchars($room12022); ?>" data-floor="<?php echo htmlspecialchars($floor12022); ?>" data-image="<?php echo base64_encode($upload_img12022); ?>" data-status="<?php echo htmlspecialchars($status12022); ?>" data-category="<?php echo htmlspecialchars($category12022); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12022); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12022); ?>; position:absolute; top:360px; left:815px;'>
                        </div>

                        <!-- ASSET 12023 -->
                        <img src='../image.php?id=12023' style='width:20px; cursor:pointer; position:absolute; top:365px; left:820px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12023' onclick='fetchAssetData(12023);' class="asset-image" data-id="<?php echo $assetId12023; ?>" data-room="<?php echo htmlspecialchars($room12023); ?>" data-floor="<?php echo htmlspecialchars($floor12023); ?>" data-image="<?php echo base64_encode($upload_img12023); ?>" data-status="<?php echo htmlspecialchars($status12023); ?>" data-category="<?php echo htmlspecialchars($category12023); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12023); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12023); ?>; position:absolute; top:360px; left:835px;'>
                        </div>

                        <!-- ASSET 12024 -->
                        <img src='../image.php?id=12024' style='width:20px; cursor:pointer; position:absolute; top:365px; left:840px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12024' onclick='fetchAssetData(12024);' class="asset-image" data-id="<?php echo $assetId12024; ?>" data-room="<?php echo htmlspecialchars($room12024); ?>" data-floor="<?php echo htmlspecialchars($floor12024); ?>" data-image="<?php echo base64_encode($upload_img12024); ?>" data-status="<?php echo htmlspecialchars($status12024); ?>" data-category="<?php echo htmlspecialchars($category12024); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12024); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12024); ?>; position:absolute; top:360px; left:855px;'>
                        </div>

                        <!-- ASSET 12025 -->
                        <img src='../image.php?id=12025' style='width:20px; cursor:pointer; position:absolute; top:365px; left:860px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12025' onclick='fetchAssetData(12025);' class="asset-image" data-id="<?php echo $assetId12025; ?>" data-room="<?php echo htmlspecialchars($room12025); ?>" data-floor="<?php echo htmlspecialchars($floor12025); ?>" data-image="<?php echo base64_encode($upload_img12025); ?>" data-status="<?php echo htmlspecialchars($status12025); ?>" data-category="<?php echo htmlspecialchars($category12025); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12025); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12025); ?>; position:absolute; top:360px; left:875px;'>
                        </div>

                        <!-- ASSET 12026 -->
                        <img src='../image.php?id=12026' style='width:20px; cursor:pointer; position:absolute; top:365px; left:880px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12026' onclick='fetchAssetData(12026);' class="asset-image" data-id="<?php echo $assetId12026; ?>" data-room="<?php echo htmlspecialchars($room12026); ?>" data-floor="<?php echo htmlspecialchars($floor12026); ?>" data-image="<?php echo base64_encode($upload_img12026); ?>" data-status="<?php echo htmlspecialchars($status12026); ?>" data-category="<?php echo htmlspecialchars($category12026); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12026); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12026); ?>; position:absolute; top:360px; left:895px;'>
                        </div>

                        <!-- ASSET 12027 -->
                        <img src='../image.php?id=12027' style='width:20px; cursor:pointer; position:absolute; top:365px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12027' onclick='fetchAssetData(12027);' class="asset-image" data-id="<?php echo $assetId12027; ?>" data-room="<?php echo htmlspecialchars($room12027); ?>" data-floor="<?php echo htmlspecialchars($floor12027); ?>" data-image="<?php echo base64_encode($upload_img12027); ?>" data-status="<?php echo htmlspecialchars($status12027); ?>" data-category="<?php echo htmlspecialchars($category12027); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12027); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12027); ?>; position:absolute; top:360px; left:915px;'>
                        </div>

                        <!-- ASSET 12028 -->
                        <img src='../image.php?id=12028' style='width:20px; cursor:pointer; position:absolute; top:365px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12028' onclick='fetchAssetData(12028);' class="asset-image" data-id="<?php echo $assetId12028; ?>" data-room="<?php echo htmlspecialchars($room12028); ?>" data-floor="<?php echo htmlspecialchars($floor12028); ?>" data-image="<?php echo base64_encode($upload_img12028); ?>" data-status="<?php echo htmlspecialchars($status12028); ?>" data-category="<?php echo htmlspecialchars($category12028); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12028); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12028); ?>; position:absolute; top:360px; left:935px;'>
                        </div>

                        <!-- ASSET 12029 -->
                        <img src='../image.php?id=12029' style='width:20px; cursor:pointer; position:absolute; top:365px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12029' onclick='fetchAssetData(12029);' class="asset-image" data-id="<?php echo $assetId12029; ?>" data-room="<?php echo htmlspecialchars($room12029); ?>" data-floor="<?php echo htmlspecialchars($floor12029); ?>" data-image="<?php echo base64_encode($upload_img12029); ?>" data-status="<?php echo htmlspecialchars($status12029); ?>" data-category="<?php echo htmlspecialchars($category12029); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12029); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12029); ?>; position:absolute; top:360px; left:955px;'>
                        </div>

                        <!-- ASSET 12030 -->
                        <img src='../image.php?id=12030' style='width:20px; cursor:pointer; position:absolute; top:365px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12030' onclick='fetchAssetData(12030);' class="asset-image" data-id="<?php echo $assetId12030; ?>" data-room="<?php echo htmlspecialchars($room12030); ?>" data-floor="<?php echo htmlspecialchars($floor12030); ?>" data-image="<?php echo base64_encode($upload_img12030); ?>" data-status="<?php echo htmlspecialchars($status12030); ?>" data-category="<?php echo htmlspecialchars($category12030); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12030); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12030); ?>; position:absolute; top:360px; left:975px;'>
                        </div>

                        <!-- ASSET 12031 -->
                        <img src='../image.php?id=12031' style='width:20px; cursor:pointer; position:absolute; top:425px; left:800px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12031' onclick='fetchAssetData(12031);' class="asset-image" data-id="<?php echo $assetId12031; ?>" data-room="<?php echo htmlspecialchars($room12031); ?>" data-floor="<?php echo htmlspecialchars($floor12031); ?>" data-image="<?php echo base64_encode($upload_img12031); ?>" data-status="<?php echo htmlspecialchars($status12031); ?>" data-category="<?php echo htmlspecialchars($category12031); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12031); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12031); ?>; position:absolute; top:420px; left:815px;'>
                        </div>

                        <!-- ASSET 12032 -->
                        <img src='../image.php?id=12032' style='width:20px; cursor:pointer; position:absolute; top:425px; left:820px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12032' onclick='fetchAssetData(12032);' class="asset-image" data-id="<?php echo $assetId12032; ?>" data-room="<?php echo htmlspecialchars($room12032); ?>" data-floor="<?php echo htmlspecialchars($floor12032); ?>" data-image="<?php echo base64_encode($upload_img12032); ?>" data-status="<?php echo htmlspecialchars($status12032); ?>" data-category="<?php echo htmlspecialchars($category12032); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12032); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12032); ?>; position:absolute; top:420px; left:835px;'>
                        </div>

                        <!-- ASSET 12033 -->
                        <img src='../image.php?id=12033' style='width:20px; cursor:pointer; position:absolute; top:425px; left:840px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12033' onclick='fetchAssetData(12033);' class="asset-image" data-id="<?php echo $assetId12033; ?>" data-room="<?php echo htmlspecialchars($room12033); ?>" data-floor="<?php echo htmlspecialchars($floor12033); ?>" data-image="<?php echo base64_encode($upload_img12033); ?>" data-status="<?php echo htmlspecialchars($status12033); ?>" data-category="<?php echo htmlspecialchars($category12033); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12033); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12033); ?>; position:absolute; top:420px; left:855px;'>
                        </div>

                        <!-- ASSET 12034 -->
                        <img src='../image.php?id=12034' style='width:20px; cursor:pointer; position:absolute; top:425px; left:860px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12034' onclick='fetchAssetData(12034);' class="asset-image" data-id="<?php echo $assetId12034; ?>" data-room="<?php echo htmlspecialchars($room12034); ?>" data-floor="<?php echo htmlspecialchars($floor12034); ?>" data-image="<?php echo base64_encode($upload_img12034); ?>" data-status="<?php echo htmlspecialchars($status12034); ?>" data-category="<?php echo htmlspecialchars($category12034); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12034); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12034); ?>; position:absolute; top:420px; left:875px;'>
                        </div>

                        <!-- ASSET 12035 -->
                        <img src='../image.php?id=12035' style='width:20px; cursor:pointer; position:absolute; top:425px; left:880px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12035' onclick='fetchAssetData(12035);' class="asset-image" data-id="<?php echo $assetId12035; ?>" data-room="<?php echo htmlspecialchars($room12035); ?>" data-floor="<?php echo htmlspecialchars($floor12035); ?>" data-image="<?php echo base64_encode($upload_img12035); ?>" data-status="<?php echo htmlspecialchars($status12035); ?>" data-category="<?php echo htmlspecialchars($category12035); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12035); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12035); ?>; position:absolute; top:420px; left:895px;'>
                        </div>

                        <!-- ASSET 12036 -->
                        <img src='../image.php?id=12036' style='width:20px; cursor:pointer; position:absolute; top:425px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12036' onclick='fetchAssetData(12036);' class="asset-image" data-id="<?php echo $assetId12036; ?>" data-room="<?php echo htmlspecialchars($room12036); ?>" data-floor="<?php echo htmlspecialchars($floor12036); ?>" data-image="<?php echo base64_encode($upload_img12036); ?>" data-status="<?php echo htmlspecialchars($status12036); ?>" data-category="<?php echo htmlspecialchars($category12036); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12036); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12036); ?>; position:absolute; top:420px; left:915px;'>
                        </div>

                        <!-- ASSET 12037 -->
                        <img src='../image.php?id=12037' style='width:20px; cursor:pointer; position:absolute; top:425px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12037' onclick='fetchAssetData(12037);' class="asset-image" data-id="<?php echo $assetId12037; ?>" data-room="<?php echo htmlspecialchars($room12037); ?>" data-floor="<?php echo htmlspecialchars($floor12037); ?>" data-image="<?php echo base64_encode($upload_img12037); ?>" data-status="<?php echo htmlspecialchars($status12037); ?>" data-category="<?php echo htmlspecialchars($category12037); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12037); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12037); ?>; position:absolute; top:420px; left:935px;'>
                        </div>

                        <!-- ASSET 12038 -->
                        <img src='../image.php?id=12038' style='width:20px; cursor:pointer; position:absolute; top:425px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12038' onclick='fetchAssetData(12038);' class="asset-image" data-id="<?php echo $assetId12038; ?>" data-room="<?php echo htmlspecialchars($room12038); ?>" data-floor="<?php echo htmlspecialchars($floor12038); ?>" data-image="<?php echo base64_encode($upload_img12038); ?>" data-status="<?php echo htmlspecialchars($status12038); ?>" data-category="<?php echo htmlspecialchars($category12038); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12038); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12038); ?>; position:absolute; top:420px; left:955px;'>
                        </div>

                        <!-- ASSET 12039 -->
                        <img src='../image.php?id=12039' style='width:20px; cursor:pointer; position:absolute; top:425px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12039' onclick='fetchAssetData(12039);' class="asset-image" data-id="<?php echo $assetId12039; ?>" data-room="<?php echo htmlspecialchars($room12039); ?>" data-floor="<?php echo htmlspecialchars($floor12039); ?>" data-image="<?php echo base64_encode($upload_img12039); ?>" data-status="<?php echo htmlspecialchars($status12039); ?>" data-category="<?php echo htmlspecialchars($category12039); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12039); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12039); ?>; position:absolute; top:420px; left:975px;'>
                        </div>

                        <!-- ASSET 12040 -->
                        <img src='../image.php?id=12040' style='width:20px; cursor:pointer; position:absolute; top:440px; left:800px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12040' onclick='fetchAssetData(12040);' class="asset-image" data-id="<?php echo $assetId12040; ?>" data-room="<?php echo htmlspecialchars($room12040); ?>" data-floor="<?php echo htmlspecialchars($floor12040); ?>" data-image="<?php echo base64_encode($upload_img12040); ?>" data-status="<?php echo htmlspecialchars($status12040); ?>" data-category="<?php echo htmlspecialchars($category12040); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12040); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12040); ?>; position:absolute; top:435px; left:815px;'>
                        </div>

                        <!-- ASSET 12041 -->
                        <img src='../image.php?id=12041' style='width:20px; cursor:pointer; position:absolute; top:440px; left:820px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12041' onclick='fetchAssetData(12041);' class="asset-image" data-id="<?php echo $assetId12041; ?>" data-room="<?php echo htmlspecialchars($room12041); ?>" data-floor="<?php echo htmlspecialchars($floor12041); ?>" data-image="<?php echo base64_encode($upload_img12041); ?>" data-status="<?php echo htmlspecialchars($status12041); ?>" data-category="<?php echo htmlspecialchars($category12041); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12041); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12041); ?>; position:absolute; top:435px; left:835px;'>
                        </div>

                        <!-- ASSET 12042 -->
                        <img src='../image.php?id=12042' style='width:20px; cursor:pointer; position:absolute; top:440px; left:840px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12042' onclick='fetchAssetData(12042);' class="asset-image" data-id="<?php echo $assetId12042; ?>" data-room="<?php echo htmlspecialchars($room12042); ?>" data-floor="<?php echo htmlspecialchars($floor12042); ?>" data-image="<?php echo base64_encode($upload_img12042); ?>" data-status="<?php echo htmlspecialchars($status12042); ?>" data-category="<?php echo htmlspecialchars($category12042); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12042); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12042); ?>; position:absolute; top:435px; left:855px;'>
                        </div>

                        <!-- ASSET 12043 -->
                        <img src='../image.php?id=12043' style='width:20px; cursor:pointer; position:absolute; top:440px; left:860px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12043' onclick='fetchAssetData(12043);' class="asset-image" data-id="<?php echo $assetId12043; ?>" data-room="<?php echo htmlspecialchars($room12043); ?>" data-floor="<?php echo htmlspecialchars($floor12043); ?>" data-image="<?php echo base64_encode($upload_img12043); ?>" data-status="<?php echo htmlspecialchars($status12043); ?>" data-category="<?php echo htmlspecialchars($category12043); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12043); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12043); ?>; position:absolute; top:435px; left:875px;'>
                        </div>

                        <!-- ASSET 12044 -->
                        <img src='../image.php?id=12044' style='width:20px; cursor:pointer; position:absolute; top:440px; left:880px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12044' onclick='fetchAssetData(12044);' class="asset-image" data-id="<?php echo $assetId12044; ?>" data-room="<?php echo htmlspecialchars($room12044); ?>" data-floor="<?php echo htmlspecialchars($floor12044); ?>" data-image="<?php echo base64_encode($upload_img12044); ?>" data-status="<?php echo htmlspecialchars($status12044); ?>" data-category="<?php echo htmlspecialchars($category12044); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12044); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12044); ?>; position:absolute; top:435px; left:895px;'>
                        </div>

                        <!-- ASSET 12045 -->
                        <img src='../image.php?id=12045' style='width:20px; cursor:pointer; position:absolute; top:440px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12045' onclick='fetchAssetData(12045);' class="asset-image" data-id="<?php echo $assetId12045; ?>" data-room="<?php echo htmlspecialchars($room12045); ?>" data-floor="<?php echo htmlspecialchars($floor12045); ?>" data-image="<?php echo base64_encode($upload_img12045); ?>" data-status="<?php echo htmlspecialchars($status12045); ?>" data-category="<?php echo htmlspecialchars($category12045); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12045); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12045); ?>; position:absolute; top:435px; left:915px;'>
                        </div>

                        <!-- ASSET 12046 -->
                        <img src='../image.php?id=12046' style='width:20px; cursor:pointer; position:absolute; top:440px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12046' onclick='fetchAssetData(12046);' class="asset-image" data-id="<?php echo $assetId12046; ?>" data-room="<?php echo htmlspecialchars($room12046); ?>" data-floor="<?php echo htmlspecialchars($floor12046); ?>" data-image="<?php echo base64_encode($upload_img12046); ?>" data-status="<?php echo htmlspecialchars($status12046); ?>" data-category="<?php echo htmlspecialchars($category12046); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12046); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12046); ?>; position:absolute; top:435px; left:935px;'>
                        </div>

                        <!-- ASSET 12047 -->
                        <img src='../image.php?id=12047' style='width:20px; cursor:pointer; position:absolute; top:440px; left:940px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12047' onclick='fetchAssetData(12047);' class="asset-image" data-id="<?php echo $assetId12047; ?>" data-room="<?php echo htmlspecialchars($room12047); ?>" data-floor="<?php echo htmlspecialchars($floor12047); ?>" data-image="<?php echo base64_encode($upload_img12047); ?>" data-status="<?php echo htmlspecialchars($status12047); ?>" data-category="<?php echo htmlspecialchars($category12047); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12047); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12047); ?>; position:absolute; top:435px; left:955px;'>
                        </div>

                        <!-- ASSET 12048 -->
                        <img src='../image.php?id=12048' style='width:20px; cursor:pointer; position:absolute; top:440px; left:960px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12048' onclick='fetchAssetData(12048);' class="asset-image" data-id="<?php echo $assetId12048; ?>" data-room="<?php echo htmlspecialchars($room12048); ?>" data-floor="<?php echo htmlspecialchars($floor12048); ?>" data-image="<?php echo base64_encode($upload_img12048); ?>" data-status="<?php echo htmlspecialchars($status12048); ?>" data-category="<?php echo htmlspecialchars($category12048); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12048); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12048); ?>; position:absolute; top:435px; left:975px;'>
                        </div>

                        <!-- ASSET 12049 -->
                        <img src='../image.php?id=12049' style='width:15px; cursor:pointer; position:absolute; top:335px; left:800px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12049' onclick='fetchAssetData(12049);' class="asset-image" data-id="<?php echo $assetId12049; ?>" data-room="<?php echo htmlspecialchars($room12049); ?>" data-floor="<?php echo htmlspecialchars($floor12049); ?>" data-image="<?php echo base64_encode($upload_img12049); ?>" data-status="<?php echo htmlspecialchars($status12049); ?>" data-category="<?php echo htmlspecialchars($category12049); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12049); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12049); ?>; position:absolute; top:330px; left:815px;'>
                        </div>

                        <!-- ASSET 12050 -->
                        <img src='../image.php?id=12050' style='width:15px; cursor:pointer; position:absolute; top:335px; left:820px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12050' onclick='fetchAssetData(12050);' class="asset-image" data-id="<?php echo $assetId12050; ?>" data-room="<?php echo htmlspecialchars($room12050); ?>" data-floor="<?php echo htmlspecialchars($floor12050); ?>" data-image="<?php echo base64_encode($upload_img12050); ?>" data-status="<?php echo htmlspecialchars($status12050); ?>" data-category="<?php echo htmlspecialchars($category12050); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12050); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12050); ?>; position:absolute; top:330px; left:835px;'>
                        </div>

                        <!-- ASSET 12051 -->
                        <img src='../image.php?id=12051' style='width:15px; cursor:pointer; position:absolute; top:335px; left:840px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12051' onclick='fetchAssetData(12051);' class="asset-image" data-id="<?php echo $assetId12051; ?>" data-room="<?php echo htmlspecialchars($room12051); ?>" data-floor="<?php echo htmlspecialchars($floor12051); ?>" data-image="<?php echo base64_encode($upload_img12051); ?>" data-status="<?php echo htmlspecialchars($status12051); ?>" data-category="<?php echo htmlspecialchars($category12051); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12051); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12051); ?>; position:absolute; top:330px; left:855px;'>
                        </div>

                        <!-- ASSET 12052 -->
                        <img src='../image.php?id=12052' style='width:15px; cursor:pointer; position:absolute; top:335px; left:860px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12052' onclick='fetchAssetData(12052);' class="asset-image" data-id="<?php echo $assetId12052; ?>" data-room="<?php echo htmlspecialchars($room12052); ?>" data-floor="<?php echo htmlspecialchars($floor12052); ?>" data-image="<?php echo base64_encode($upload_img12052); ?>" data-status="<?php echo htmlspecialchars($status12052); ?>" data-category="<?php echo htmlspecialchars($category12052); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12052); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12052); ?>; position:absolute; top:330px; left:875px;'>
                        </div>

                        <!-- ASSET 12053 -->
                        <img src='../image.php?id=12053' style='width:15px; cursor:pointer; position:absolute; top:335px; left:880px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12053' onclick='fetchAssetData(12053);' class="asset-image" data-id="<?php echo $assetId12053; ?>" data-room="<?php echo htmlspecialchars($room12053); ?>" data-floor="<?php echo htmlspecialchars($floor12053); ?>" data-image="<?php echo base64_encode($upload_img12053); ?>" data-status="<?php echo htmlspecialchars($status12053); ?>" data-category="<?php echo htmlspecialchars($category12053); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12053); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12053); ?>; position:absolute; top:330px; left:895px;'>
                        </div>

                        <!-- ASSET 12054 -->
                        <img src='../image.php?id=12054' style='width:15px; cursor:pointer; position:absolute; top:335px; left:900px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12054' onclick='fetchAssetData(12054);' class="asset-image" data-id="<?php echo $assetId12054; ?>" data-room="<?php echo htmlspecialchars($room12054); ?>" data-floor="<?php echo htmlspecialchars($floor12054); ?>" data-image="<?php echo base64_encode($upload_img12054); ?>" data-status="<?php echo htmlspecialchars($status12054); ?>" data-category="<?php echo htmlspecialchars($category12054); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12054); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12054); ?>; position:absolute; top:330px; left:915px;'>
                        </div>

                        <!-- ASSET 12055 -->
                        <img src='../image.php?id=12055' style='width:15px; cursor:pointer; position:absolute; top:335px; left:920px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12055' onclick='fetchAssetData(12055);' class="asset-image" data-id="<?php echo $assetId12055; ?>" data-room="<?php echo htmlspecialchars($room12055); ?>" data-floor="<?php echo htmlspecialchars($floor12055); ?>" data-image="<?php echo base64_encode($upload_img12055); ?>" data-status="<?php echo htmlspecialchars($status12055); ?>" data-category="<?php echo htmlspecialchars($category12055); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12055); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12055); ?>; position:absolute; top:330px; left:935px;'>
                        </div>

                        <!-- ASSET 12056 -->
                        <img src='../image.php?id=12056' style='width:15px; cursor:pointer; position:absolute; top:335px; left:940px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12056' onclick='fetchAssetData(12056);' class="asset-image" data-id="<?php echo $assetId12056; ?>" data-room="<?php echo htmlspecialchars($room12056); ?>" data-floor="<?php echo htmlspecialchars($floor12056); ?>" data-image="<?php echo base64_encode($upload_img12056); ?>" data-status="<?php echo htmlspecialchars($status12056); ?>" data-category="<?php echo htmlspecialchars($category12056); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12056); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12056); ?>; position:absolute; top:330px; left:955px;'>
                        </div>

                        <!-- ASSET 12057 -->
                        <img src='../image.php?id=12057' style='width:15px; cursor:pointer; position:absolute; top:335px; left:960px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12057' onclick='fetchAssetData(12057);' class="asset-image" data-id="<?php echo $assetId12057; ?>" data-room="<?php echo htmlspecialchars($room12057); ?>" data-floor="<?php echo htmlspecialchars($floor12057); ?>" data-image="<?php echo base64_encode($upload_img12057); ?>" data-status="<?php echo htmlspecialchars($status12057); ?>" data-category="<?php echo htmlspecialchars($category12057); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12057); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12057); ?>; position:absolute; top:330px; left:975px;'>
                        </div>





                        <!-- ASSET 12058 -->
                        <img src='../image.php?id=12058' style='width:15px; cursor:pointer; position:absolute; top:380px; left:800px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12058' onclick='fetchAssetData(12058);' class="asset-image" data-id="<?php echo $assetId12058; ?>" data-room="<?php echo htmlspecialchars($room12058); ?>" data-floor="<?php echo htmlspecialchars($floor12058); ?>" data-image="<?php echo base64_encode($upload_img12058); ?>" data-status="<?php echo htmlspecialchars($status12058); ?>" data-category="<?php echo htmlspecialchars($category12058); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12058); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12058); ?>; position:absolute; top:375px; left:815px;'>
                        </div>

                        <!-- ASSET 12059 -->
                        <img src='../image.php?id=12059' style='width:15px; cursor:pointer; position:absolute; top:380px; left:820px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12059' onclick='fetchAssetData(12059);' class="asset-image" data-id="<?php echo $assetId12059; ?>" data-room="<?php echo htmlspecialchars($room12059); ?>" data-floor="<?php echo htmlspecialchars($floor12059); ?>" data-image="<?php echo base64_encode($upload_img12059); ?>" data-status="<?php echo htmlspecialchars($status12059); ?>" data-category="<?php echo htmlspecialchars($category12059); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12059); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12059); ?>; position:absolute; top:375px; left:835px;'>
                        </div>

                        <!-- ASSET 12060 -->
                        <img src='../image.php?id=12060' style='width:15px; cursor:pointer; position:absolute; top:380px; left:840px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12060' onclick='fetchAssetData(12060);' class="asset-image" data-id="<?php echo $assetId12060; ?>" data-room="<?php echo htmlspecialchars($room12060); ?>" data-floor="<?php echo htmlspecialchars($floor12060); ?>" data-image="<?php echo base64_encode($upload_img12060); ?>" data-status="<?php echo htmlspecialchars($status12060); ?>" data-category="<?php echo htmlspecialchars($category12060); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12060); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12060); ?>; position:absolute; top:375px; left:855px;'>
                        </div>

                        <!-- ASSET 12061 -->
                        <img src='../image.php?id=12061' style='width:15px; cursor:pointer; position:absolute; top:380px; left:860px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12061' onclick='fetchAssetData(12061);' class="asset-image" data-id="<?php echo $assetId12061; ?>" data-room="<?php echo htmlspecialchars($room12061); ?>" data-floor="<?php echo htmlspecialchars($floor12061); ?>" data-image="<?php echo base64_encode($upload_img12061); ?>" data-status="<?php echo htmlspecialchars($status12061); ?>" data-category="<?php echo htmlspecialchars($category12061); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12061); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12061); ?>; position:absolute; top:375px; left:875px;'>
                        </div>

                        <!-- ASSET 12062 -->
                        <img src='../image.php?id=12062' style='width:15px; cursor:pointer; position:absolute; top:380px; left:880px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12062' onclick='fetchAssetData(12062);' class="asset-image" data-id="<?php echo $assetId12062; ?>" data-room="<?php echo htmlspecialchars($room12062); ?>" data-floor="<?php echo htmlspecialchars($floor12062); ?>" data-image="<?php echo base64_encode($upload_img12062); ?>" data-status="<?php echo htmlspecialchars($status12062); ?>" data-category="<?php echo htmlspecialchars($category12062); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12062); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12062); ?>; position:absolute; top:375px; left:895px;'>
                        </div>

                        <!-- ASSET 12063 -->
                        <img src='../image.php?id=12063' style='width:15px; cursor:pointer; position:absolute; top:380px; left:900px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12063' onclick='fetchAssetData(12063);' class="asset-image" data-id="<?php echo $assetId12063; ?>" data-room="<?php echo htmlspecialchars($room12063); ?>" data-floor="<?php echo htmlspecialchars($floor12063); ?>" data-image="<?php echo base64_encode($upload_img12063); ?>" data-status="<?php echo htmlspecialchars($status12063); ?>" data-category="<?php echo htmlspecialchars($category12063); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12063); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12063); ?>; position:absolute; top:375px; left:915px;'>
                        </div>

                        <!-- ASSET 12064 -->
                        <img src='../image.php?id=12064' style='width:15px; cursor:pointer; position:absolute; top:380px; left:920px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12064' onclick='fetchAssetData(12064);' class="asset-image" data-id="<?php echo $assetId12064; ?>" data-room="<?php echo htmlspecialchars($room12064); ?>" data-floor="<?php echo htmlspecialchars($floor12064); ?>" data-image="<?php echo base64_encode($upload_img12064); ?>" data-status="<?php echo htmlspecialchars($status12064); ?>" data-category="<?php echo htmlspecialchars($category12064); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12064); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12064); ?>; position:absolute; top:375px; left:935px;'>
                        </div>

                        <!-- ASSET 12065 -->
                        <img src='../image.php?id=12065' style='width:15px; cursor:pointer; position:absolute; top:380px; left:940px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12065' onclick='fetchAssetData(12065);' class="asset-image" data-id="<?php echo $assetId12065; ?>" data-room="<?php echo htmlspecialchars($room12065); ?>" data-floor="<?php echo htmlspecialchars($floor12065); ?>" data-image="<?php echo base64_encode($upload_img12065); ?>" data-status="<?php echo htmlspecialchars($status12065); ?>" data-category="<?php echo htmlspecialchars($category12065); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12065); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12065); ?>; position:absolute; top:375px; left:955px;'>
                        </div>

                        <!-- ASSET 12066 -->
                        <img src='../image.php?id=12066' style='width:15px; cursor:pointer; position:absolute; top:380px; left:960px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12066' onclick='fetchAssetData(12066);' class="asset-image" data-id="<?php echo $assetId12066; ?>" data-room="<?php echo htmlspecialchars($room12066); ?>" data-floor="<?php echo htmlspecialchars($floor12066); ?>" data-image="<?php echo base64_encode($upload_img12066); ?>" data-status="<?php echo htmlspecialchars($status12066); ?>" data-category="<?php echo htmlspecialchars($category12066); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12066); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12066); ?>; position:absolute; top:375px; left:975px;'>
                        </div>



                        <!-- ASSET 12067 -->
                        <img src='../image.php?id=12067' style='width:15px; cursor:pointer; position:absolute; top:410px; left:800px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12067' onclick='fetchAssetData(12067);' class="asset-image" data-id="<?php echo $assetId12067; ?>" data-room="<?php echo htmlspecialchars($room12067); ?>" data-floor="<?php echo htmlspecialchars($floor12067); ?>" data-image="<?php echo base64_encode($upload_img12067); ?>" data-status="<?php echo htmlspecialchars($status12067); ?>" data-category="<?php echo htmlspecialchars($category12067); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12067); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12067); ?>; position:absolute; top:405px; left:815px;'>
                        </div>

                        <!-- ASSET 12068 -->
                        <img src='../image.php?id=12068' style='width:15px; cursor:pointer; position:absolute; top:410px; left:820px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12068' onclick='fetchAssetData(12068);' class="asset-image" data-id="<?php echo $assetId12068; ?>" data-room="<?php echo htmlspecialchars($room12068); ?>" data-floor="<?php echo htmlspecialchars($floor12068); ?>" data-image="<?php echo base64_encode($upload_img12068); ?>" data-status="<?php echo htmlspecialchars($status12068); ?>" data-category="<?php echo htmlspecialchars($category12068); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12068); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12068); ?>; position:absolute; top:405px; left:835px;'>
                        </div>

                        <!-- ASSET 12069 -->
                        <img src='../image.php?id=12069' style='width:15px; cursor:pointer; position:absolute; top:410px; left:840px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12069' onclick='fetchAssetData(12069);' class="asset-image" data-id="<?php echo $assetId12069; ?>" data-room="<?php echo htmlspecialchars($room12069); ?>" data-floor="<?php echo htmlspecialchars($floor12069); ?>" data-image="<?php echo base64_encode($upload_img12069); ?>" data-status="<?php echo htmlspecialchars($status12069); ?>" data-category="<?php echo htmlspecialchars($category12069); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12069); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12069); ?>; position:absolute; top:405px; left:855px;'>
                        </div>

                        <!-- ASSET 12070 -->
                        <img src='../image.php?id=12070' style='width:15px; cursor:pointer; position:absolute; top:410px; left:860px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12070' onclick='fetchAssetData(12070);' class="asset-image" data-id="<?php echo $assetId12070; ?>" data-room="<?php echo htmlspecialchars($room12070); ?>" data-floor="<?php echo htmlspecialchars($floor12070); ?>" data-image="<?php echo base64_encode($upload_img12070); ?>" data-status="<?php echo htmlspecialchars($status12070); ?>" data-category="<?php echo htmlspecialchars($category12070); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12070); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12070); ?>; position:absolute; top:405px; left:875px;'>
                        </div>

                        <!-- ASSET 12071 -->
                        <img src='../image.php?id=12071' style='width:15px; cursor:pointer; position:absolute; top:410px; left:880px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12071' onclick='fetchAssetData(12071);' class="asset-image" data-id="<?php echo $assetId12071; ?>" data-room="<?php echo htmlspecialchars($room12071); ?>" data-floor="<?php echo htmlspecialchars($floor12071); ?>" data-image="<?php echo base64_encode($upload_img12071); ?>" data-status="<?php echo htmlspecialchars($status12071); ?>" data-category="<?php echo htmlspecialchars($category12071); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12071); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12071); ?>; position:absolute; top:405px; left:895px;'>
                        </div>

                        <!-- ASSET 12072 -->
                        <img src='../image.php?id=12072' style='width:15px; cursor:pointer; position:absolute; top:410px; left:900px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12072' onclick='fetchAssetData(12072);' class="asset-image" data-id="<?php echo $assetId12072; ?>" data-room="<?php echo htmlspecialchars($room12072); ?>" data-floor="<?php echo htmlspecialchars($floor12072); ?>" data-image="<?php echo base64_encode($upload_img12072); ?>" data-status="<?php echo htmlspecialchars($status12072); ?>" data-category="<?php echo htmlspecialchars($category12072); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12072); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12072); ?>; position:absolute; top:405px; left:915px;'>
                        </div>

                        <!-- ASSET 12073 -->
                        <img src='../image.php?id=12073' style='width:15px; cursor:pointer; position:absolute; top:410px; left:920px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12073' onclick='fetchAssetData(12073);' class="asset-image" data-id="<?php echo $assetId12073; ?>" data-room="<?php echo htmlspecialchars($room12073); ?>" data-floor="<?php echo htmlspecialchars($floor12073); ?>" data-image="<?php echo base64_encode($upload_img12073); ?>" data-status="<?php echo htmlspecialchars($status12073); ?>" data-category="<?php echo htmlspecialchars($category12073); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12073); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12073); ?>; position:absolute; top:405px; left:935px;'>
                        </div>

                        <!-- ASSET 12074 -->
                        <img src='../image.php?id=12074' style='width:15px; cursor:pointer; position:absolute; top:410px; left:940px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12074' onclick='fetchAssetData(12074);' class="asset-image" data-id="<?php echo $assetId12074; ?>" data-room="<?php echo htmlspecialchars($room12074); ?>" data-floor="<?php echo htmlspecialchars($floor12074); ?>" data-image="<?php echo base64_encode($upload_img12074); ?>" data-status="<?php echo htmlspecialchars($status12074); ?>" data-category="<?php echo htmlspecialchars($category12074); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12074); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12074); ?>; position:absolute; top:405px; left:955px;'>
                        </div>

                        <!-- ASSET 12075 -->
                        <img src='../image.php?id=12075' style='width:15px; cursor:pointer; position:absolute; top:410px; left:960px;transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12075' onclick='fetchAssetData(12075);' class="asset-image" data-id="<?php echo $assetId12075; ?>" data-room="<?php echo htmlspecialchars($room12075); ?>" data-floor="<?php echo htmlspecialchars($floor12075); ?>" data-image="<?php echo base64_encode($upload_img12075); ?>" data-status="<?php echo htmlspecialchars($status12075); ?>" data-category="<?php echo htmlspecialchars($category12075); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12075); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12075); ?>; position:absolute; top:405px; left:975px;'>
                        </div>





                        <!-- ASSET 12076 -->
                        <img src='../image.php?id=12076' style='width:15px; cursor:pointer; position:absolute; top:455px; left:800px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12076' onclick='fetchAssetData(12076);' class="asset-image" data-id="<?php echo $assetId12076; ?>" data-room="<?php echo htmlspecialchars($room12076); ?>" data-floor="<?php echo htmlspecialchars($floor12076); ?>" data-image="<?php echo base64_encode($upload_img12076); ?>" data-status="<?php echo htmlspecialchars($status12076); ?>" data-category="<?php echo htmlspecialchars($category12076); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12076); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12076); ?>; position:absolute; top:450px; left:815px;'>
                        </div>

                        <!-- ASSET 12077 -->
                        <img src='../image.php?id=12077' style='width:15px; cursor:pointer; position:absolute; top:455px; left:820px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12077' onclick='fetchAssetData(12077);' class="asset-image" data-id="<?php echo $assetId12077; ?>" data-room="<?php echo htmlspecialchars($room12077); ?>" data-floor="<?php echo htmlspecialchars($floor12077); ?>" data-image="<?php echo base64_encode($upload_img12077); ?>" data-status="<?php echo htmlspecialchars($status12077); ?>" data-category="<?php echo htmlspecialchars($category12077); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12077); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12077); ?>; position:absolute; top:450px; left:835px;'>
                        </div>

                        <!-- ASSET 12078 -->
                        <img src='../image.php?id=12078' style='width:15px; cursor:pointer; position:absolute; top:455px; left:840px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12078' onclick='fetchAssetData(12078);' class="asset-image" data-id="<?php echo $assetId12078; ?>" data-room="<?php echo htmlspecialchars($room12078); ?>" data-floor="<?php echo htmlspecialchars($floor12078); ?>" data-image="<?php echo base64_encode($upload_img12078); ?>" data-status="<?php echo htmlspecialchars($status12078); ?>" data-category="<?php echo htmlspecialchars($category12078); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12078); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12078); ?>; position:absolute; top:450px; left:855px;'>
                        </div>

                        <!-- ASSET 12079 -->
                        <img src='../image.php?id=12079' style='width:15px; cursor:pointer; position:absolute; top:455px; left:860px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12079' onclick='fetchAssetData(12079);' class="asset-image" data-id="<?php echo $assetId12079; ?>" data-room="<?php echo htmlspecialchars($room12079); ?>" data-floor="<?php echo htmlspecialchars($floor12079); ?>" data-image="<?php echo base64_encode($upload_img12079); ?>" data-status="<?php echo htmlspecialchars($status12079); ?>" data-category="<?php echo htmlspecialchars($category12079); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12079); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12079); ?>; position:absolute; top:450px; left:875px;'>
                        </div>

                        <!-- ASSET 12080 -->
                        <img src='../image.php?id=12080' style='width:15px; cursor:pointer; position:absolute; top:455px; left:880px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12080' onclick='fetchAssetData(12080);' class="asset-image" data-id="<?php echo $assetId12080; ?>" data-room="<?php echo htmlspecialchars($room12080); ?>" data-floor="<?php echo htmlspecialchars($floor12080); ?>" data-image="<?php echo base64_encode($upload_img12080); ?>" data-status="<?php echo htmlspecialchars($status12080); ?>" data-category="<?php echo htmlspecialchars($category12080); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12080); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12080); ?>; position:absolute; top:450px; left:895px;'>
                        </div>

                        <!-- ASSET 12081 -->
                        <img src='../image.php?id=12081' style='width:15px; cursor:pointer; position:absolute; top:455px; left:900px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12081' onclick='fetchAssetData(12081);' class="asset-image" data-id="<?php echo $assetId12081; ?>" data-room="<?php echo htmlspecialchars($room12081); ?>" data-floor="<?php echo htmlspecialchars($floor12081); ?>" data-image="<?php echo base64_encode($upload_img12081); ?>" data-status="<?php echo htmlspecialchars($status12081); ?>" data-category="<?php echo htmlspecialchars($category12081); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12081); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12081); ?>; position:absolute; top:450px; left:915px;'>
                        </div>

                        <!-- ASSET 12082 -->
                        <img src='../image.php?id=12082' style='width:15px; cursor:pointer; position:absolute; top:455px; left:920px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12082' onclick='fetchAssetData(12082);' class="asset-image" data-id="<?php echo $assetId12082; ?>" data-room="<?php echo htmlspecialchars($room12082); ?>" data-floor="<?php echo htmlspecialchars($floor12082); ?>" data-image="<?php echo base64_encode($upload_img12082); ?>" data-status="<?php echo htmlspecialchars($status12082); ?>" data-category="<?php echo htmlspecialchars($category12082); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12082); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12082); ?>; position:absolute; top:450px; left:935px;'>
                        </div>

                        <!-- ASSET 12083 -->
                        <img src='../image.php?id=12083' style='width:15px; cursor:pointer; position:absolute; top:455px; left:940px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12083' onclick='fetchAssetData(12083);' class="asset-image" data-id="<?php echo $assetId12083; ?>" data-room="<?php echo htmlspecialchars($room12083); ?>" data-floor="<?php echo htmlspecialchars($floor12083); ?>" data-image="<?php echo base64_encode($upload_img12083); ?>" data-status="<?php echo htmlspecialchars($status12083); ?>" data-category="<?php echo htmlspecialchars($category12083); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12083); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12083); ?>; position:absolute; top:450px; left:955px;'>
                        </div>

                        <!-- ASSET 12084 -->
                        <img src='../image.php?id=12084' style='width:15px; cursor:pointer; position:absolute; top:455px; left:960px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12084' onclick='fetchAssetData(12084);' class="asset-image" data-id="<?php echo $assetId12084; ?>" data-room="<?php echo htmlspecialchars($room12084); ?>" data-floor="<?php echo htmlspecialchars($floor12084); ?>" data-image="<?php echo base64_encode($upload_img12084); ?>" data-status="<?php echo htmlspecialchars($status12084); ?>" data-category="<?php echo htmlspecialchars($category12084); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12084); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12084); ?>; position:absolute; top:450px; left:975px;'>
                        </div>

                        <!-- ASSET 12085 -->
                        <img src='../image.php?id=12085' style='width:15px; cursor:pointer; position:absolute; top:320px; left:1120px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12085' onclick='fetchAssetData(12085);' class="asset-image" data-id="<?php echo $assetId12085; ?>" data-room="<?php echo htmlspecialchars($room12085); ?>" data-floor="<?php echo htmlspecialchars($floor12085); ?>" data-image="<?php echo base64_encode($upload_img12085); ?>" data-status="<?php echo htmlspecialchars($status12085); ?>" data-category="<?php echo htmlspecialchars($category12085); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12085); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12085); ?>; position:absolute; top:315px; left:1130px;'>
                        </div>

                        <!-- ASSET 12086 -->
                        <img src='../image.php?id=12086' style='width:15px; cursor:pointer; position:absolute; top:250px; left:1120px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12086' onclick='fetchAssetData(12086);' class="asset-image" data-id="<?php echo $assetId12086; ?>" data-room="<?php echo htmlspecialchars($room12086); ?>" data-floor="<?php echo htmlspecialchars($floor12086); ?>" data-image="<?php echo base64_encode($upload_img12086); ?>" data-status="<?php echo htmlspecialchars($status12086); ?>" data-category="<?php echo htmlspecialchars($category12086); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12086); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12086); ?>; position:absolute; top:245px; left:1130px;'>
                        </div>

                        <!-- ASSET 12087 -->
                        <img src='../image.php?id=12087' style='width:15px; cursor:pointer; position:absolute; top:250px; left:910px;transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12087' onclick='fetchAssetData(12087);' class="asset-image" data-id="<?php echo $assetId12087; ?>" data-room="<?php echo htmlspecialchars($room12087); ?>" data-floor="<?php echo htmlspecialchars($floor12087); ?>" data-image="<?php echo base64_encode($upload_img12087); ?>" data-status="<?php echo htmlspecialchars($status12087); ?>" data-category="<?php echo htmlspecialchars($category12087); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12087); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12087); ?>; position:absolute; top:245px; left:920px;'>
                        </div>

                        <!-- ASSET 12088 -->
                        <img src='../image.php?id=12088' style='width:40px; cursor:pointer; position:absolute; top:470px; left:745px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12088' onclick='fetchAssetData(12088);' class="asset-image" data-id="<?php echo $assetId12088; ?>" data-room="<?php echo htmlspecialchars($room12088); ?>" data-floor="<?php echo htmlspecialchars($floor12088); ?>" data-image="<?php echo base64_encode($upload_img12088); ?>" data-status="<?php echo htmlspecialchars($status12088); ?>" data-category="<?php echo htmlspecialchars($category12088); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12088); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12088); ?>; position:absolute; top:470px; left:775px;'>
                        </div>

                        <!-- ASSET 12089 -->
                        <img src='../image.php?id=12089' style='width:60px; cursor:pointer; position:absolute; top:465px; left:800px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12089' onclick='fetchAssetData(12089);' class="asset-image" data-id="<?php echo $assetId12089; ?>" data-room="<?php echo htmlspecialchars($room12089); ?>" data-floor="<?php echo htmlspecialchars($floor12089); ?>" data-image="<?php echo base64_encode($upload_img12089); ?>" data-status="<?php echo htmlspecialchars($status12089); ?>" data-category="<?php echo htmlspecialchars($category12089); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12089); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12089); ?>; position:absolute; top:470px; left:830px;'>
                        </div>

                        <!-- ASSET 12090 -->
                        <img src='../image.php?id=12090' style='width:70px; cursor:pointer; position:absolute; top:460px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12090' onclick='fetchAssetData(12090);' class="asset-image" data-id="<?php echo $assetId12090; ?>" data-room="<?php echo htmlspecialchars($room12090); ?>" data-floor="<?php echo htmlspecialchars($floor12090); ?>" data-image="<?php echo base64_encode($upload_img12090); ?>" data-status="<?php echo htmlspecialchars($status12090); ?>" data-category="<?php echo htmlspecialchars($category12090); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12090); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12090); ?>; position:absolute; top:470px; left:900px;'>
                        </div>

                        <!-- ASSET 12091 -->
                        <img src='../image.php?id=12091' style='width:50px; cursor:pointer; position:absolute; top:465px; left:955px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12091' onclick='fetchAssetData(12091);' class="asset-image" data-id="<?php echo $assetId12091; ?>" data-room="<?php echo htmlspecialchars($room12091); ?>" data-floor="<?php echo htmlspecialchars($floor12091); ?>" data-image="<?php echo base64_encode($upload_img12091); ?>" data-status="<?php echo htmlspecialchars($status12091); ?>" data-category="<?php echo htmlspecialchars($category12091); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12091); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12091); ?>; position:absolute; top:470px; left:985px;'>
                        </div>

                        <!-- ASSET 12092 -->
                        <img src='../image.php?id=12092' style='width:15px; cursor:pointer; position:absolute; top:150px; left:845px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12092' onclick='fetchAssetData(12092);' class="asset-image" data-id="<?php echo $assetId12092; ?>" data-room="<?php echo htmlspecialchars($room12092); ?>" data-floor="<?php echo htmlspecialchars($floor12092); ?>" data-image="<?php echo base64_encode($upload_img12092); ?>" data-status="<?php echo htmlspecialchars($status12092); ?>" data-category="<?php echo htmlspecialchars($category12092); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12092); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12092); ?>; position:absolute; top:145px; left:855px;'>
                        </div>


                        <!-- ASSET 12093 -->
                        <img src='../image.php?id=12093' style='width:15px; cursor:pointer; position:absolute; top:415px; left:745px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12093' onclick='fetchAssetData(12093);' class="asset-image" data-id="<?php echo $assetId12093; ?>" data-room="<?php echo htmlspecialchars($room12093); ?>" data-floor="<?php echo htmlspecialchars($floor12093); ?>" data-image="<?php echo base64_encode($upload_img12093); ?>" data-status="<?php echo htmlspecialchars($status12093); ?>" data-category="<?php echo htmlspecialchars($category12093); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12093); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12093); ?>; position:absolute; top:410px; left:755px;'>
                        </div>



                        <!-- ASSET 12095 -->
                        <img src='../image.php?id=12095' style='width:15px; cursor:pointer; position:absolute; top:180px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12095' onclick='fetchAssetData(12095);' class="asset-image" data-id="<?php echo $assetId12095; ?>" data-room="<?php echo htmlspecialchars($room12095); ?>" data-floor="<?php echo htmlspecialchars($floor12095); ?>" data-image="<?php echo base64_encode($upload_img12095); ?>" data-status="<?php echo htmlspecialchars($status12095); ?>" data-category="<?php echo htmlspecialchars($category12095); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12095); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12095); ?>; position:absolute; top:175px; left:860px;'>
                        </div>

                        <!-- ASSET 12096 -->
                        <img src='../image.php?id=12096' style='width:15px; cursor:pointer; position:absolute; top:230px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12096' onclick='fetchAssetData(12096);' class="asset-image" data-id="<?php echo $assetId12096; ?>" data-room="<?php echo htmlspecialchars($room12096); ?>" data-floor="<?php echo htmlspecialchars($floor12096); ?>" data-image="<?php echo base64_encode($upload_img12096); ?>" data-status="<?php echo htmlspecialchars($status12096); ?>" data-category="<?php echo htmlspecialchars($category12096); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12096); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12096); ?>; position:absolute; top:225px; left:860px;'>
                        </div>

                        <!-- ASSET 12097 -->
                        <img src='../image.php?id=12097' style='width:15px; cursor:pointer; position:absolute; top:335px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12097' onclick='fetchAssetData(12097);' class="asset-image" data-id="<?php echo $assetId12097; ?>" data-room="<?php echo htmlspecialchars($room12097); ?>" data-floor="<?php echo htmlspecialchars($floor12097); ?>" data-image="<?php echo base64_encode($upload_img12097); ?>" data-status="<?php echo htmlspecialchars($status12097); ?>" data-category="<?php echo htmlspecialchars($category12097); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12097); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12097); ?>; position:absolute; top:330px; left:750px;'>
                        </div>

                        <!-- ASSET 12098 -->
                        <img src='../image.php?id=12098' style='width:15px; cursor:pointer; position:absolute; top:380px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12098' onclick='fetchAssetData(12098);' class="asset-image" data-id="<?php echo $assetId12098; ?>" data-room="<?php echo htmlspecialchars($room12098); ?>" data-floor="<?php echo htmlspecialchars($floor12098); ?>" data-image="<?php echo base64_encode($upload_img12098); ?>" data-status="<?php echo htmlspecialchars($status12098); ?>" data-category="<?php echo htmlspecialchars($category12098); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12098); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12098); ?>; position:absolute; top:375px; left:750px;'>
                        </div>

                        <!-- ASSET 12099 -->
                        <img src='../image.php?id=12099' style='width:15px; cursor:pointer; position:absolute; top:460px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12099' onclick='fetchAssetData(12099);' class="asset-image" data-id="<?php echo $assetId12099; ?>" data-room="<?php echo htmlspecialchars($room12099); ?>" data-floor="<?php echo htmlspecialchars($floor12099); ?>" data-image="<?php echo base64_encode($upload_img12099); ?>" data-status="<?php echo htmlspecialchars($status12099); ?>" data-category="<?php echo htmlspecialchars($category12099); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12099); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12099); ?>; position:absolute; top:455px; left:730px;'>
                        </div>

                        <!-- ASSET 12100 -->
                        <img src='../image.php?id=12100' style='width:15px; cursor:pointer; position:absolute; top:230px; left:860px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12100' onclick='fetchAssetData(12100);' class="asset-image" data-id="<?php echo $assetId12100; ?>" data-room="<?php echo htmlspecialchars($room12100); ?>" data-floor="<?php echo htmlspecialchars($floor12100); ?>" data-image="<?php echo base64_encode($upload_img12100); ?>" data-status="<?php echo htmlspecialchars($status12100); ?>" data-category="<?php echo htmlspecialchars($category12100); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12100); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12100); ?>; position:absolute; top:225px; left:870px;'>
                        </div>

                        <!-- ASSET 12101 -->
                        <img src='../image.php?id=12101' style='width:15px; cursor:pointer; position:absolute; top:110px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12101' onclick='fetchAssetData(12101);' class="asset-image" data-id="<?php echo $assetId12101; ?>" data-room="<?php echo htmlspecialchars($room12101); ?>" data-floor="<?php echo htmlspecialchars($floor12101); ?>" data-image="<?php echo base64_encode($upload_img12101); ?>" data-status="<?php echo htmlspecialchars($status12101); ?>" data-category="<?php echo htmlspecialchars($category12101); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12101); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12101); ?>; position:absolute; top:105px; left:760px;'>
                        </div>

                        <!-- ASSET 12102 -->
                        <img src='../image.php?id=12102' style='width:15px; cursor:pointer; position:absolute; top:110px; left:670px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12102' onclick='fetchAssetData(12102);' class="asset-image" data-id="<?php echo $assetId12102; ?>" data-room="<?php echo htmlspecialchars($room12102); ?>" data-floor="<?php echo htmlspecialchars($floor12102); ?>" data-image="<?php echo base64_encode($upload_img12102); ?>" data-status="<?php echo htmlspecialchars($status12102); ?>" data-category="<?php echo htmlspecialchars($category12102); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12102); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12102); ?>; position:absolute; top:105px; left:680px;'>
                        </div>

                        <!-- ASSET 12103 -->
                        <img src='../image.php?id=12103' style='width:15px; cursor:pointer; position:absolute; top:120px; left:650px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12103' onclick='fetchAssetData(12103);' class="asset-image" data-id="<?php echo $assetId12103; ?>" data-room="<?php echo htmlspecialchars($room12103); ?>" data-floor="<?php echo htmlspecialchars($floor12103); ?>" data-image="<?php echo base64_encode($upload_img12103); ?>" data-status="<?php echo htmlspecialchars($status12103); ?>" data-category="<?php echo htmlspecialchars($category12103); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12103); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12103); ?>; position:absolute; top:115px; left:660px;'>
                        </div>

                        <!-- ASSET 12104 -->
                        <img src='../image.php?id=12104' style='width:15px; cursor:pointer; position:absolute; top:150px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12104' onclick='fetchAssetData(12104);' class="asset-image" data-id="<?php echo $assetId12104; ?>" data-room="<?php echo htmlspecialchars($room12104); ?>" data-floor="<?php echo htmlspecialchars($floor12104); ?>" data-image="<?php echo base64_encode($upload_img12104); ?>" data-status="<?php echo htmlspecialchars($status12104); ?>" data-category="<?php echo htmlspecialchars($category12104); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12104); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12104); ?>; position:absolute; top:145px; left:760px;'>
                        </div>

                        <!-- ASSET 12105 -->
                        <img src='../image.php?id=12105' style='width:15px; cursor:pointer; position:absolute; top:150px; left:670px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12105' onclick='fetchAssetData(12105);' class="asset-image" data-id="<?php echo $assetId12105; ?>" data-room="<?php echo htmlspecialchars($room12105); ?>" data-floor="<?php echo htmlspecialchars($floor12105); ?>" data-image="<?php echo base64_encode($upload_img12105); ?>" data-status="<?php echo htmlspecialchars($status12105); ?>" data-category="<?php echo htmlspecialchars($category12105); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12105); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12105); ?>; position:absolute; top:145px; left:680px;'>
                        </div>

                        <!-- ASSET 12106 -->
                        <img src='../image.php?id=12106' style='width:15px; cursor:pointer; position:absolute; top:150px; left:650px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12106' onclick='fetchAssetData(12106);' class="asset-image" data-id="<?php echo $assetId12106; ?>" data-room="<?php echo htmlspecialchars($room12106); ?>" data-floor="<?php echo htmlspecialchars($floor12106); ?>" data-image="<?php echo base64_encode($upload_img12106); ?>" data-status="<?php echo htmlspecialchars($status12106); ?>" data-category="<?php echo htmlspecialchars($category12106); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12106); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12106); ?>; position:absolute; top:145px; left:660px;'>
                        </div>



                        <!-- ASSET 12112 -->
                        <img src='../image.php?id=12112' style='width:15px; cursor:pointer; position:absolute; top:115px; left:490px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12112' onclick='fetchAssetData(12112);' class="asset-image" data-id="<?php echo $assetId12112; ?>" data-room="<?php echo htmlspecialchars($room12112); ?>" data-floor="<?php echo htmlspecialchars($floor12112); ?>" data-image="<?php echo base64_encode($upload_img12112); ?>" data-status="<?php echo htmlspecialchars($status12112); ?>" data-category="<?php echo htmlspecialchars($category12112); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12112); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12112); ?>; position:absolute; top:110px; left:500px;'>
                        </div>

                        <!-- ASSET 12113 -->
                        <img src='../image.php?id=12113' style='width:15px; cursor:pointer; position:absolute; top:115px; left:520px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12113' onclick='fetchAssetData(12113);' class="asset-image" data-id="<?php echo $assetId12113; ?>" data-room="<?php echo htmlspecialchars($room12113); ?>" data-floor="<?php echo htmlspecialchars($floor12113); ?>" data-image="<?php echo base64_encode($upload_img12113); ?>" data-status="<?php echo htmlspecialchars($status12113); ?>" data-category="<?php echo htmlspecialchars($category12113); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12113); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12113); ?>; position:absolute; top:110px; left:530px;'>
                        </div>

                        <!-- ASSET 12114 -->
                        <img src='../image.php?id=12114' style='width:15px; cursor:pointer; position:absolute; top:115px; left:550px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12114' onclick='fetchAssetData(12114);' class="asset-image" data-id="<?php echo $assetId12114; ?>" data-room="<?php echo htmlspecialchars($room12114); ?>" data-floor="<?php echo htmlspecialchars($floor12114); ?>" data-image="<?php echo base64_encode($upload_img12114); ?>" data-status="<?php echo htmlspecialchars($status12114); ?>" data-category="<?php echo htmlspecialchars($category12114); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12114); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12114); ?>; position:absolute; top:110px; left:560px;'>
                        </div>

                        <!-- ASSET 12115 -->
                        <img src='../image.php?id=12115' style='width:15px; cursor:pointer; position:absolute; top:115px; left:580px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12115' onclick='fetchAssetData(12115);' class="asset-image" data-id="<?php echo $assetId12115; ?>" data-room="<?php echo htmlspecialchars($room12115); ?>" data-floor="<?php echo htmlspecialchars($floor12115); ?>" data-image="<?php echo base64_encode($upload_img12115); ?>" data-status="<?php echo htmlspecialchars($status12115); ?>" data-category="<?php echo htmlspecialchars($category12115); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12115); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12115); ?>; position:absolute; top:110px; left:590px;'>
                        </div>

                        <!-- ASSET 12116 -->
                        <img src='../image.php?id=12116' style='width:15px; cursor:pointer; position:absolute; top:115px; left:610px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12116' onclick='fetchAssetData(12116);' class="asset-image" data-id="<?php echo $assetId12116; ?>" data-room="<?php echo htmlspecialchars($room12116); ?>" data-floor="<?php echo htmlspecialchars($floor12116); ?>" data-image="<?php echo base64_encode($upload_img12116); ?>" data-status="<?php echo htmlspecialchars($status12116); ?>" data-category="<?php echo htmlspecialchars($category12116); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12116); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12116); ?>; position:absolute; top:110px; left:620px;'>
                        </div>




                        <!-- ASSET 12117 -->
                        <img src='../image.php?id=12117' style='width:15px; cursor:pointer; position:absolute; top:170px; left:490px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12117' onclick='fetchAssetData(12117);' class="asset-image" data-id="<?php echo $assetId12117; ?>" data-room="<?php echo htmlspecialchars($room12117); ?>" data-floor="<?php echo htmlspecialchars($floor12117); ?>" data-image="<?php echo base64_encode($upload_img12117); ?>" data-status="<?php echo htmlspecialchars($status12117); ?>" data-category="<?php echo htmlspecialchars($category12117); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12117); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12117); ?>; position:absolute; top:165px; left:500px;'>
                        </div>

                        <!-- ASSET 12118 -->
                        <img src='../image.php?id=12118' style='width:15px; cursor:pointer; position:absolute; top:170px; left:520px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12118' onclick='fetchAssetData(12118);' class="asset-image" data-id="<?php echo $assetId12118; ?>" data-room="<?php echo htmlspecialchars($room12118); ?>" data-floor="<?php echo htmlspecialchars($floor12118); ?>" data-image="<?php echo base64_encode($upload_img12118); ?>" data-status="<?php echo htmlspecialchars($status12118); ?>" data-category="<?php echo htmlspecialchars($category12118); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12118); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12118); ?>; position:absolute; top:165px; left:530px;'>
                        </div>

                        <!-- ASSET 12119 -->
                        <img src='../image.php?id=12119' style='width:15px; cursor:pointer; position:absolute; top:170px; left:550px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12119' onclick='fetchAssetData(12119);' class="asset-image" data-id="<?php echo $assetId12119; ?>" data-room="<?php echo htmlspecialchars($room12119); ?>" data-floor="<?php echo htmlspecialchars($floor12119); ?>" data-image="<?php echo base64_encode($upload_img12119); ?>" data-status="<?php echo htmlspecialchars($status12119); ?>" data-category="<?php echo htmlspecialchars($category12119); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12119); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12119); ?>; position:absolute; top:165px; left:560px;'>
                        </div>

                        <!-- ASSET 12120 -->
                        <img src='../image.php?id=12120' style='width:15px; cursor:pointer; position:absolute; top:170px; left:580px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12120' onclick='fetchAssetData(12120);' class="asset-image" data-id="<?php echo $assetId12120; ?>" data-room="<?php echo htmlspecialchars($room12120); ?>" data-floor="<?php echo htmlspecialchars($floor12120); ?>" data-image="<?php echo base64_encode($upload_img12120); ?>" data-status="<?php echo htmlspecialchars($status12120); ?>" data-category="<?php echo htmlspecialchars($category12120); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12120); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12120); ?>; position:absolute; top:165px; left:590px;'>
                        </div>

                        <!-- ASSET 12121 -->
                        <img src='../image.php?id=12121' style='width:15px; cursor:pointer; position:absolute; top:170px; left:610px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12121' onclick='fetchAssetData(12121);' class="asset-image" data-id="<?php echo $assetId12121; ?>" data-room="<?php echo htmlspecialchars($room12121); ?>" data-floor="<?php echo htmlspecialchars($floor12121); ?>" data-image="<?php echo base64_encode($upload_img12121); ?>" data-status="<?php echo htmlspecialchars($status12121); ?>" data-category="<?php echo htmlspecialchars($category12121); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12121); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12121); ?>; position:absolute; top:165px; left:620px;'>
                        </div>

                        <!-- ASSET 12122 -->
                        <img src='../image.php?id=12122' style='width:40px; cursor:pointer; position:absolute; top:106px; left:409px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12122' onclick='fetchAssetData(12122);' class="asset-image" data-id="<?php echo $assetId12122; ?>" data-room="<?php echo htmlspecialchars($room12122); ?>" data-floor="<?php echo htmlspecialchars($floor12122); ?>" data-image="<?php echo base64_encode($upload_img12122); ?>" data-status="<?php echo htmlspecialchars($status12122); ?>" data-category="<?php echo htmlspecialchars($category12122); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12122); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12122); ?>; position:absolute; top:100px; left:450px;'>
                        </div>

                        <!-- ASSET 12123 -->
                        <img src='../image.php?id=12123' style='width:40px; cursor:pointer; position:absolute; top:156px; left:409px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12123' onclick='fetchAssetData(12123);' class="asset-image" data-id="<?php echo $assetId12123; ?>" data-room="<?php echo htmlspecialchars($room12123); ?>" data-floor="<?php echo htmlspecialchars($floor12123); ?>" data-image="<?php echo base64_encode($upload_img12123); ?>" data-status="<?php echo htmlspecialchars($status12123); ?>" data-category="<?php echo htmlspecialchars($category12123); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12123); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12123); ?>; position:absolute; top:150px; left:450px;'>
                        </div>

                        <!-- ASSET 12124 -->
                        <img src='../image.php?id=12124' style='width:15px; cursor:pointer; position:absolute; top:220px; left:409px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12124' onclick='fetchAssetData(12124);' class="asset-image" data-id="<?php echo $assetId12124; ?>" data-room="<?php echo htmlspecialchars($room12124); ?>" data-floor="<?php echo htmlspecialchars($floor12124); ?>" data-image="<?php echo base64_encode($upload_img12124); ?>" data-status="<?php echo htmlspecialchars($status12124); ?>" data-category="<?php echo htmlspecialchars($category12124); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12124); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12124); ?>; position:absolute; top:215px; left:419px;'>
                        </div>

                        <!-- ASSET 12125 -->
                        <img src='../image.php?id=12125' style='width:15px; cursor:pointer; position:absolute; top:220px; left:465px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12125' onclick='fetchAssetData(12125);' class="asset-image" data-id="<?php echo $assetId12125; ?>" data-room="<?php echo htmlspecialchars($room12125); ?>" data-floor="<?php echo htmlspecialchars($floor12125); ?>" data-image="<?php echo base64_encode($upload_img12125); ?>" data-status="<?php echo htmlspecialchars($status12125); ?>" data-category="<?php echo htmlspecialchars($category12125); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12125); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12125); ?>; position:absolute; top:215px; left:475px;'>
                        </div>

                        <!-- ASSET 12126 -->
                        <img src='../image.php?id=12126' style='width:15px; cursor:pointer; position:absolute; top:295px; left:290px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12126' onclick='fetchAssetData(12126);' class="asset-image" data-id="<?php echo $assetId12126; ?>" data-room="<?php echo htmlspecialchars($room12126); ?>" data-floor="<?php echo htmlspecialchars($floor12126); ?>" data-image="<?php echo base64_encode($upload_img12126); ?>" data-status="<?php echo htmlspecialchars($status12126); ?>" data-category="<?php echo htmlspecialchars($category12126); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12126); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12126); ?>; position:absolute; top:290px; left:300px;'>
                        </div>

                        <!-- ASSET 12127 -->
                        <img src='../image.php?id=12127' style='width:15px; cursor:pointer; position:absolute; top:290px; left:320px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12127' onclick='fetchAssetData(12127);' class="asset-image" data-id="<?php echo $assetId12127; ?>" data-room="<?php echo htmlspecialchars($room12127); ?>" data-floor="<?php echo htmlspecialchars($floor12127); ?>" data-image="<?php echo base64_encode($upload_img12127); ?>" data-status="<?php echo htmlspecialchars($status12127); ?>" data-category="<?php echo htmlspecialchars($category12127); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12127); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12127); ?>; position:absolute; top:285px; left:330px;'>
                        </div>

                        <!-- ASSET 12128 -->
                        <img src='../image.php?id=12128' style='width:15px; cursor:pointer; position:absolute; top:290px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12128' onclick='fetchAssetData(12128);' class="asset-image" data-id="<?php echo $assetId12128; ?>" data-room="<?php echo htmlspecialchars($room12128); ?>" data-floor="<?php echo htmlspecialchars($floor12128); ?>" data-image="<?php echo base64_encode($upload_img12128); ?>" data-status="<?php echo htmlspecialchars($status12128); ?>" data-category="<?php echo htmlspecialchars($category12128); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12128); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12128); ?>; position:absolute; top:285px; left:250px;'>
                        </div>

                        <!-- ASSET 12129 -->
                        <img src='../image.php?id=12129' style='width:15px; cursor:pointer; position:absolute; top:320px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12129' onclick='fetchAssetData(12129);' class="asset-image" data-id="<?php echo $assetId12129; ?>" data-room="<?php echo htmlspecialchars($room12129); ?>" data-floor="<?php echo htmlspecialchars($floor12129); ?>" data-image="<?php echo base64_encode($upload_img12129); ?>" data-status="<?php echo htmlspecialchars($status12129); ?>" data-category="<?php echo htmlspecialchars($category12129); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12129); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12129); ?>; position:absolute; top:315px; left:245px;'>
                        </div>

                        <!-- ASSET 12130 -->
                        <img src='../image.php?id=12130' style='width:15px; cursor:pointer; position:absolute; top:190px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12130' onclick='fetchAssetData(12130);' class="asset-image" data-id="<?php echo $assetId12130; ?>" data-room="<?php echo htmlspecialchars($room12130); ?>" data-floor="<?php echo htmlspecialchars($floor12130); ?>" data-image="<?php echo base64_encode($upload_img12130); ?>" data-status="<?php echo htmlspecialchars($status12130); ?>" data-category="<?php echo htmlspecialchars($category12130); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12130); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12130); ?>; position:absolute; top:185px; left:185px;'>
                        </div>

                        <!-- ASSET 12131 -->
                        <img src='../image.php?id=12131' style='width:15px; cursor:pointer; position:absolute; top:220px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12131' onclick='fetchAssetData(12131);' class="asset-image" data-id="<?php echo $assetId12131; ?>" data-room="<?php echo htmlspecialchars($room12131); ?>" data-floor="<?php echo htmlspecialchars($floor12131); ?>" data-image="<?php echo base64_encode($upload_img12131); ?>" data-status="<?php echo htmlspecialchars($status12131); ?>" data-category="<?php echo htmlspecialchars($category12131); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12131); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12131); ?>; position:absolute; top:215px; left:185px;'>
                        </div>

                        <!-- ASSET 12132 -->
                        <img src='../image.php?id=12132' style='width:15px; cursor:pointer; position:absolute; top:250px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12132' onclick='fetchAssetData(12132);' class="asset-image" data-id="<?php echo $assetId12132; ?>" data-room="<?php echo htmlspecialchars($room12132); ?>" data-floor="<?php echo htmlspecialchars($floor12132); ?>" data-image="<?php echo base64_encode($upload_img12132); ?>" data-status="<?php echo htmlspecialchars($status12132); ?>" data-category="<?php echo htmlspecialchars($category12132); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12132); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12132); ?>; position:absolute; top:245px; left:185px;'>
                        </div>

                        <!-- ASSET 12133 -->
                        <img src='../image.php?id=12133' style='width:15px; cursor:pointer; position:absolute; top:190px; left:355px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12133' onclick='fetchAssetData(12133);' class="asset-image" data-id="<?php echo $assetId12133; ?>" data-room="<?php echo htmlspecialchars($room12133); ?>" data-floor="<?php echo htmlspecialchars($floor12133); ?>" data-image="<?php echo base64_encode($upload_img12133); ?>" data-status="<?php echo htmlspecialchars($status12133); ?>" data-category="<?php echo htmlspecialchars($category12133); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12133); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12133); ?>; position:absolute; top:185px; left:365px;'>
                        </div>

                        <!-- ASSET 12134 -->
                        <img src='../image.php?id=12134' style='width:15px; cursor:pointer; position:absolute; top:220px; left:355px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12134' onclick='fetchAssetData(12134);' class="asset-image" data-id="<?php echo $assetId12134; ?>" data-room="<?php echo htmlspecialchars($room12134); ?>" data-floor="<?php echo htmlspecialchars($floor12134); ?>" data-image="<?php echo base64_encode($upload_img12134); ?>" data-status="<?php echo htmlspecialchars($status12134); ?>" data-category="<?php echo htmlspecialchars($category12134); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12134); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12134); ?>; position:absolute; top:215px; left:365px;'>
                        </div>

                        <!-- ASSET 12135 -->
                        <img src='../image.php?id=12135' style='width:15px; cursor:pointer; position:absolute; top:250px; left:355px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12135' onclick='fetchAssetData(12135);' class="asset-image" data-id="<?php echo $assetId12135; ?>" data-room="<?php echo htmlspecialchars($room12135); ?>" data-floor="<?php echo htmlspecialchars($floor12135); ?>" data-image="<?php echo base64_encode($upload_img12135); ?>" data-status="<?php echo htmlspecialchars($status12135); ?>" data-category="<?php echo htmlspecialchars($category12135); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12135); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12135); ?>; position:absolute; top:245px; left:365px;'>
                        </div>

                        <!-- ASSET 12136 -->
                        <img src='../image.php?id=12136' style='width:15px; cursor:pointer; position:absolute; top:190px; left:310px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12136' onclick='fetchAssetData(12136);' class="asset-image" data-id="<?php echo $assetId12136; ?>" data-room="<?php echo htmlspecialchars($room12136); ?>" data-floor="<?php echo htmlspecialchars($floor12136); ?>" data-image="<?php echo base64_encode($upload_img12136); ?>" data-status="<?php echo htmlspecialchars($status12136); ?>" data-category="<?php echo htmlspecialchars($category12136); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12136); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12136); ?>; position:absolute; top:185px; left:320px;'>
                        </div>

                        <!-- ASSET 12137 -->
                        <img src='../image.php?id=12137' style='width:15px; cursor:pointer; position:absolute; top:220px; left:310px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12137' onclick='fetchAssetData(12137);' class="asset-image" data-id="<?php echo $assetId12137; ?>" data-room="<?php echo htmlspecialchars($room12137); ?>" data-floor="<?php echo htmlspecialchars($floor12137); ?>" data-image="<?php echo base64_encode($upload_img12137); ?>" data-status="<?php echo htmlspecialchars($status12137); ?>" data-category="<?php echo htmlspecialchars($category12137); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12137); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12137); ?>; position:absolute; top:215px; left:320px;'>
                        </div>

                        <!-- ASSET 12138 -->
                        <img src='../image.php?id=12138' style='width:15px; cursor:pointer; position:absolute; top:250px; left:310px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12138' onclick='fetchAssetData(12138);' class="asset-image" data-id="<?php echo $assetId12138; ?>" data-room="<?php echo htmlspecialchars($room12138); ?>" data-floor="<?php echo htmlspecialchars($floor12138); ?>" data-image="<?php echo base64_encode($upload_img12138); ?>" data-status="<?php echo htmlspecialchars($status12138); ?>" data-category="<?php echo htmlspecialchars($category12138); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12138); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12138); ?>; position:absolute; top:245px; left:320px;'>
                        </div>

                        <!-- ASSET 12139 -->
                        <img src='../image.php?id=12139' style='width:15px; cursor:pointer; position:absolute; top:190px; left:245px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12139' onclick='fetchAssetData(12139);' class="asset-image" data-id="<?php echo $assetId12139; ?>" data-room="<?php echo htmlspecialchars($room12139); ?>" data-floor="<?php echo htmlspecialchars($floor12139); ?>" data-image="<?php echo base64_encode($upload_img12139); ?>" data-status="<?php echo htmlspecialchars($status12139); ?>" data-category="<?php echo htmlspecialchars($category12139); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12139); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12139); ?>; position:absolute; top:185px; left:255px;'>
                        </div>

                        <!-- ASSET 12140 -->
                        <img src='../image.php?id=12140' style='width:15px; cursor:pointer; position:absolute; top:220px; left:245px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12140' onclick='fetchAssetData(12140);' class="asset-image" data-id="<?php echo $assetId12140; ?>" data-room="<?php echo htmlspecialchars($room12140); ?>" data-floor="<?php echo htmlspecialchars($floor12140); ?>" data-image="<?php echo base64_encode($upload_img12140); ?>" data-status="<?php echo htmlspecialchars($status12140); ?>" data-category="<?php echo htmlspecialchars($category12140); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12140); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12140); ?>; position:absolute; top:215px; left:255px;'>
                        </div>

                        <!-- ASSET 12141 -->
                        <img src='../image.php?id=12141' style='width:15px; cursor:pointer; position:absolute; top:250px; left:245px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12141' onclick='fetchAssetData(12141);' class="asset-image" data-id="<?php echo $assetId12141; ?>" data-room="<?php echo htmlspecialchars($room12141); ?>" data-floor="<?php echo htmlspecialchars($floor12141); ?>" data-image="<?php echo base64_encode($upload_img12141); ?>" data-status="<?php echo htmlspecialchars($status12141); ?>" data-category="<?php echo htmlspecialchars($category12141); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12141); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12141); ?>; position:absolute; top:245px; left:255px;'>
                        </div>

                        <!-- ASSET 12142 -->
                        <img src='../image.php?id=12142' style='width:15px; cursor:pointer; position:absolute; top:270px; left:280px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12142' onclick='fetchAssetData(12142);' class="asset-image" data-id="<?php echo $assetId12142; ?>" data-room="<?php echo htmlspecialchars($room12142); ?>" data-floor="<?php echo htmlspecialchars($floor12142); ?>" data-image="<?php echo base64_encode($upload_img12142); ?>" data-status="<?php echo htmlspecialchars($status12142); ?>" data-category="<?php echo htmlspecialchars($category12142); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12142); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12142); ?>; position:absolute; top:265px; left:290px;'>
                        </div>

                        <!-- ASSET 12143 -->
                        <img src='../image.php?id=12143' style='width:15px; cursor:pointer; position:absolute; top:270px; left:170px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12143' onclick='fetchAssetData(12143);' class="asset-image" data-id="<?php echo $assetId12143; ?>" data-room="<?php echo htmlspecialchars($room12143); ?>" data-floor="<?php echo htmlspecialchars($floor12143); ?>" data-image="<?php echo base64_encode($upload_img12143); ?>" data-status="<?php echo htmlspecialchars($status12143); ?>" data-category="<?php echo htmlspecialchars($category12143); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12143); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12143); ?>; position:absolute; top:265px; left:180px;'>
                        </div>

                        <!-- ASSET 12144 -->
                        <img src='../image.php?id=12144' style='width:15px; cursor:pointer; position:absolute; top:270px; left:370px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12144' onclick='fetchAssetData(12144);' class="asset-image" data-id="<?php echo $assetId12144; ?>" data-room="<?php echo htmlspecialchars($room12144); ?>" data-floor="<?php echo htmlspecialchars($floor12144); ?>" data-image="<?php echo base64_encode($upload_img12144); ?>" data-status="<?php echo htmlspecialchars($status12144); ?>" data-category="<?php echo htmlspecialchars($category12144); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12144); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12144); ?>; position:absolute; top:265px; left:380px;'>
                        </div>

                        <!-- ASSET 12145 -->
                        <img src='../image.php?id=12145' style='width:15px; cursor:pointer; position:absolute; top:190px; left:130px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12145' onclick='fetchAssetData(12145);' class="asset-image" data-id="<?php echo $assetId12145; ?>" data-room="<?php echo htmlspecialchars($room12145); ?>" data-floor="<?php echo htmlspecialchars($floor12145); ?>" data-image="<?php echo base64_encode($upload_img12145); ?>" data-status="<?php echo htmlspecialchars($status12145); ?>" data-category="<?php echo htmlspecialchars($category12145); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12145); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12145); ?>; position:absolute; top:185px; left:140px;'>
                        </div>

                        <!-- ASSET 12146 -->
                        <img src='../image.php?id=12146' style='width:15px; cursor:pointer; position:absolute; top:300px; left:130px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12146' onclick='fetchAssetData(12146);' class="asset-image" data-id="<?php echo $assetId12146; ?>" data-room="<?php echo htmlspecialchars($room12146); ?>" data-floor="<?php echo htmlspecialchars($floor12146); ?>" data-image="<?php echo base64_encode($upload_img12146); ?>" data-status="<?php echo htmlspecialchars($status12146); ?>" data-category="<?php echo htmlspecialchars($category12146); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12146); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12146); ?>; position:absolute; top:295px; left:140px;'>
                        </div>


                        <!-- ASSET 12147 -->
                        <img src='../image.php?id=12147' style='width:15px; cursor:pointer; position:absolute; top:340px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12147' onclick='fetchAssetData(12147);' class="asset-image" data-id="<?php echo $assetId12147; ?>" data-room="<?php echo htmlspecialchars($room12147); ?>" data-floor="<?php echo htmlspecialchars($floor12147); ?>" data-image="<?php echo base64_encode($upload_img12147); ?>" data-status="<?php echo htmlspecialchars($status12147); ?>" data-category="<?php echo htmlspecialchars($category12147); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12147); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12147); ?>; position:absolute; top:335px; left:1130px;'>
                        </div>

                        <!-- ASSET 12148 -->
                        <img src='../image.php?id=12148' style='width:15px; cursor:pointer; position:absolute; top:450px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12148' onclick='fetchAssetData(12148);' class="asset-image" data-id="<?php echo $assetId12148; ?>" data-room="<?php echo htmlspecialchars($room12148); ?>" data-floor="<?php echo htmlspecialchars($floor12148); ?>" data-image="<?php echo base64_encode($upload_img12148); ?>" data-status="<?php echo htmlspecialchars($status12148); ?>" data-category="<?php echo htmlspecialchars($category12148); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12148); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12148); ?>; position:absolute; top:445px; left:1130px;'>
                        </div>

                        <!-- ASSET 12149 -->
                        <img src='../image.php?id=12149' style='width:15px; cursor:pointer; position:absolute; top:320px; left:130px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12149' onclick='fetchAssetData(12149);' class="asset-image" data-id="<?php echo $assetId12149; ?>" data-room="<?php echo htmlspecialchars($room12149); ?>" data-floor="<?php echo htmlspecialchars($floor12149); ?>" data-image="<?php echo base64_encode($upload_img12149); ?>" data-status="<?php echo htmlspecialchars($status12149); ?>" data-category="<?php echo htmlspecialchars($category12149); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12149); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12149); ?>; position:absolute; top:315px; left:140px;'>
                        </div>

                        <!-- ASSET 12150 -->
                        <img src='../image.php?id=12150' style='width:15px; cursor:pointer; position:absolute; top:335px; left:80px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12150' onclick='fetchAssetData(12150);' class="asset-image" data-id="<?php echo $assetId12150; ?>" data-room="<?php echo htmlspecialchars($room12150); ?>" data-floor="<?php echo htmlspecialchars($floor12150); ?>" data-image="<?php echo base64_encode($upload_img12150); ?>" data-status="<?php echo htmlspecialchars($status12150); ?>" data-category="<?php echo htmlspecialchars($category12150); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12150); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12150); ?>; position:absolute; top:330px; left:90px;'>
                        </div>

                        <!-- ASSET 12151 -->
                        <img src='../image.php?id=12151' style='width:15px; cursor:pointer; position:absolute; top:335px; left:140px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12151' onclick='fetchAssetData(12151);' class="asset-image" data-id="<?php echo $assetId12151; ?>" data-room="<?php echo htmlspecialchars($room12151); ?>" data-floor="<?php echo htmlspecialchars($floor12151); ?>" data-image="<?php echo base64_encode($upload_img12151); ?>" data-status="<?php echo htmlspecialchars($status12151); ?>" data-category="<?php echo htmlspecialchars($category12151); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12151); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12151); ?>; position:absolute; top:330px; left:150px;'>
                        </div>

                        <!-- ASSET 12152 -->
                        <img src='../image.php?id=12152' style='width:15px; cursor:pointer; position:absolute; top:335px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12152' onclick='fetchAssetData(12152);' class="asset-image" data-id="<?php echo $assetId12152; ?>" data-room="<?php echo htmlspecialchars($room12152); ?>" data-floor="<?php echo htmlspecialchars($floor12152); ?>" data-image="<?php echo base64_encode($upload_img12152); ?>" data-status="<?php echo htmlspecialchars($status12152); ?>" data-category="<?php echo htmlspecialchars($category12152); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12152); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12152); ?>; position:absolute; top:330px; left:210px;'>
                        </div>

                        <!-- ASSET 12153 -->
                        <img src='../image.php?id=12153' style='width:15px; cursor:pointer; position:absolute; top:335px; left:260px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12153' onclick='fetchAssetData(12153);' class="asset-image" data-id="<?php echo $assetId12153; ?>" data-room="<?php echo htmlspecialchars($room12153); ?>" data-floor="<?php echo htmlspecialchars($floor12153); ?>" data-image="<?php echo base64_encode($upload_img12153); ?>" data-status="<?php echo htmlspecialchars($status12153); ?>" data-category="<?php echo htmlspecialchars($category12153); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12153); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12153); ?>; position:absolute; top:330px; left:270px;'>
                        </div>

                        <!-- ASSET 12154 -->
                        <img src='../image.php?id=12154' style='width:15px; cursor:pointer; position:absolute; top:335px; left:320px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12154' onclick='fetchAssetData(12154);' class="asset-image" data-id="<?php echo $assetId12154; ?>" data-room="<?php echo htmlspecialchars($room12154); ?>" data-floor="<?php echo htmlspecialchars($floor12154); ?>" data-image="<?php echo base64_encode($upload_img12154); ?>" data-status="<?php echo htmlspecialchars($status12154); ?>" data-category="<?php echo htmlspecialchars($category12154); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12154); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12154); ?>; position:absolute; top:330px; left:330px;'>
                        </div>

                        <!-- ASSET 12155 -->
                        <img src='../image.php?id=12155' style='width:15px; cursor:pointer; position:absolute; top:335px; left:380px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12155' onclick='fetchAssetData(12155);' class="asset-image" data-id="<?php echo $assetId12155; ?>" data-room="<?php echo htmlspecialchars($room12155); ?>" data-floor="<?php echo htmlspecialchars($floor12155); ?>" data-image="<?php echo base64_encode($upload_img12155); ?>" data-status="<?php echo htmlspecialchars($status12155); ?>" data-category="<?php echo htmlspecialchars($category12155); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12155); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12155); ?>; position:absolute; top:330px; left:390px;'>
                        </div>

                        <!-- ASSET 12156 -->
                        <img src='../image.php?id=12156' style='width:15px; cursor:pointer; position:absolute; top:335px; left:440px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12156' onclick='fetchAssetData(12156);' class="asset-image" data-id="<?php echo $assetId12156; ?>" data-room="<?php echo htmlspecialchars($room12156); ?>" data-floor="<?php echo htmlspecialchars($floor12156); ?>" data-image="<?php echo base64_encode($upload_img12156); ?>" data-status="<?php echo htmlspecialchars($status12156); ?>" data-category="<?php echo htmlspecialchars($category12156); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12156); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12156); ?>; position:absolute; top:330px; left:450px;'>
                        </div>

                        <!-- ASSET 12157 -->
                        <img src='../image.php?id=12157' style='width:15px; cursor:pointer; position:absolute; top:335px; left:500px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12157' onclick='fetchAssetData(12157);' class="asset-image" data-id="<?php echo $assetId12157; ?>" data-room="<?php echo htmlspecialchars($room12157); ?>" data-floor="<?php echo htmlspecialchars($floor12157); ?>" data-image="<?php echo base64_encode($upload_img12157); ?>" data-status="<?php echo htmlspecialchars($status12157); ?>" data-category="<?php echo htmlspecialchars($category12157); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12157); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12157); ?>; position:absolute; top:330px; left:510px;'>
                        </div>

                        <!-- ASSET 12158 -->
                        <img src='../image.php?id=12158' style='width:15px; cursor:pointer; position:absolute; top:335px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12158' onclick='fetchAssetData(12158);' class="asset-image" data-id="<?php echo $assetId12158; ?>" data-room="<?php echo htmlspecialchars($room12158); ?>" data-floor="<?php echo htmlspecialchars($floor12158); ?>" data-image="<?php echo base64_encode($upload_img12158); ?>" data-status="<?php echo htmlspecialchars($status12158); ?>" data-category="<?php echo htmlspecialchars($category12158); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12158); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12158); ?>; position:absolute; top:330px; left:570px;'>
                        </div>

                        <!-- ASSET 12159 -->
                        <img src='../image.php?id=12159' style='width:15px; cursor:pointer; position:absolute; top:335px; left:620px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12159' onclick='fetchAssetData(12159);' class="asset-image" data-id="<?php echo $assetId12159; ?>" data-room="<?php echo htmlspecialchars($room12159); ?>" data-floor="<?php echo htmlspecialchars($floor12159); ?>" data-image="<?php echo base64_encode($upload_img12159); ?>" data-status="<?php echo htmlspecialchars($status12159); ?>" data-category="<?php echo htmlspecialchars($category12159); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12159); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12159); ?>; position:absolute; top:330px; left:630px;'>
                        </div>




                        <!-- ASSET 12160 -->
                        <img src='../image.php?id=12160' style='width:15px; cursor:pointer; position:absolute; top:375px; left:80px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12160' onclick='fetchAssetData(12160);' class="asset-image" data-id="<?php echo $assetId12160; ?>" data-room="<?php echo htmlspecialchars($room12160); ?>" data-floor="<?php echo htmlspecialchars($floor12160); ?>" data-image="<?php echo base64_encode($upload_img12160); ?>" data-status="<?php echo htmlspecialchars($status12160); ?>" data-category="<?php echo htmlspecialchars($category12160); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12160); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12160); ?>; position:absolute; top:370px; left:90px;'>
                        </div>

                        <!-- ASSET 12161 -->
                        <img src='../image.php?id=12161' style='width:15px; cursor:pointer; position:absolute; top:375px; left:140px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12161' onclick='fetchAssetData(12161);' class="asset-image" data-id="<?php echo $assetId12161; ?>" data-room="<?php echo htmlspecialchars($room12161); ?>" data-floor="<?php echo htmlspecialchars($floor12161); ?>" data-image="<?php echo base64_encode($upload_img12161); ?>" data-status="<?php echo htmlspecialchars($status12161); ?>" data-category="<?php echo htmlspecialchars($category12161); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12161); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12161); ?>; position:absolute; top:370px; left:150px;'>
                        </div>

                        <!-- ASSET 12162 -->
                        <img src='../image.php?id=12162' style='width:15px; cursor:pointer; position:absolute; top:375px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12162' onclick='fetchAssetData(12162);' class="asset-image" data-id="<?php echo $assetId12162; ?>" data-room="<?php echo htmlspecialchars($room12162); ?>" data-floor="<?php echo htmlspecialchars($floor12162); ?>" data-image="<?php echo base64_encode($upload_img12162); ?>" data-status="<?php echo htmlspecialchars($status12162); ?>" data-category="<?php echo htmlspecialchars($category12162); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12162); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12162); ?>; position:absolute; top:370px; left:210px;'>
                        </div>

                        <!-- ASSET 12163 -->
                        <img src='../image.php?id=12163' style='width:15px; cursor:pointer; position:absolute; top:375px; left:260px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12163' onclick='fetchAssetData(12163);' class="asset-image" data-id="<?php echo $assetId12163; ?>" data-room="<?php echo htmlspecialchars($room12163); ?>" data-floor="<?php echo htmlspecialchars($floor12163); ?>" data-image="<?php echo base64_encode($upload_img12163); ?>" data-status="<?php echo htmlspecialchars($status12163); ?>" data-category="<?php echo htmlspecialchars($category12163); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12163); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12163); ?>; position:absolute; top:370px; left:270px;'>
                        </div>

                        <!-- ASSET 12164 -->
                        <img src='../image.php?id=12164' style='width:15px; cursor:pointer; position:absolute; top:375px; left:320px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12164' onclick='fetchAssetData(12164);' class="asset-image" data-id="<?php echo $assetId12164; ?>" data-room="<?php echo htmlspecialchars($room12164); ?>" data-floor="<?php echo htmlspecialchars($floor12164); ?>" data-image="<?php echo base64_encode($upload_img12164); ?>" data-status="<?php echo htmlspecialchars($status12164); ?>" data-category="<?php echo htmlspecialchars($category12164); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12164); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12164); ?>; position:absolute; top:370px; left:330px;'>
                        </div>

                        <!-- ASSET 12165 -->
                        <img src='../image.php?id=12165' style='width:15px; cursor:pointer; position:absolute; top:375px; left:380px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12165' onclick='fetchAssetData(12165);' class="asset-image" data-id="<?php echo $assetId12165; ?>" data-room="<?php echo htmlspecialchars($room12165); ?>" data-floor="<?php echo htmlspecialchars($floor12165); ?>" data-image="<?php echo base64_encode($upload_img12165); ?>" data-status="<?php echo htmlspecialchars($status12165); ?>" data-category="<?php echo htmlspecialchars($category12165); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12165); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12165); ?>; position:absolute; top:370px; left:390px;'>
                        </div>

                        <!-- ASSET 12166 -->
                        <img src='../image.php?id=12166' style='width:15px; cursor:pointer; position:absolute; top:375px; left:440px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12166' onclick='fetchAssetData(12166);' class="asset-image" data-id="<?php echo $assetId12166; ?>" data-room="<?php echo htmlspecialchars($room12166); ?>" data-floor="<?php echo htmlspecialchars($floor12166); ?>" data-image="<?php echo base64_encode($upload_img12166); ?>" data-status="<?php echo htmlspecialchars($status12166); ?>" data-category="<?php echo htmlspecialchars($category12166); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12166); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12166); ?>; position:absolute; top:370px; left:450px;'>
                        </div>

                        <!-- ASSET 12167 -->
                        <img src='../image.php?id=12167' style='width:15px; cursor:pointer; position:absolute; top:375px; left:500px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12167' onclick='fetchAssetData(12167);' class="asset-image" data-id="<?php echo $assetId12167; ?>" data-room="<?php echo htmlspecialchars($room12167); ?>" data-floor="<?php echo htmlspecialchars($floor12167); ?>" data-image="<?php echo base64_encode($upload_img12167); ?>" data-status="<?php echo htmlspecialchars($status12167); ?>" data-category="<?php echo htmlspecialchars($category12167); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12167); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12167); ?>; position:absolute; top:370px; left:510px;'>
                        </div>

                        <!-- ASSET 12168 -->
                        <img src='../image.php?id=12168' style='width:15px; cursor:pointer; position:absolute; top:375px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12168' onclick='fetchAssetData(12168);' class="asset-image" data-id="<?php echo $assetId12168; ?>" data-room="<?php echo htmlspecialchars($room12168); ?>" data-floor="<?php echo htmlspecialchars($floor12168); ?>" data-image="<?php echo base64_encode($upload_img12168); ?>" data-status="<?php echo htmlspecialchars($status12168); ?>" data-category="<?php echo htmlspecialchars($category12168); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12168); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12168); ?>; position:absolute; top:370px; left:570px;'>
                        </div>

                        <!-- ASSET 12169 -->
                        <img src='../image.php?id=12169' style='width:15px; cursor:pointer; position:absolute; top:375px; left:620px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12169' onclick='fetchAssetData(12169);' class="asset-image" data-id="<?php echo $assetId12169; ?>" data-room="<?php echo htmlspecialchars($room12169); ?>" data-floor="<?php echo htmlspecialchars($floor12169); ?>" data-image="<?php echo base64_encode($upload_img12169); ?>" data-status="<?php echo htmlspecialchars($status12169); ?>" data-category="<?php echo htmlspecialchars($category12169); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12169); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12169); ?>; position:absolute; top:370px; left:630px;'>
                        </div>



                        <!-- ASSET 12170 -->
                        <img src='../image.php?id=12170' style='width:15px; cursor:pointer; position:absolute; top:300px; left:600px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12170' onclick='fetchAssetData(12170);' class="asset-image" data-id="<?php echo $assetId12170; ?>" data-room="<?php echo htmlspecialchars($room12170); ?>" data-floor="<?php echo htmlspecialchars($floor12170); ?>" data-image="<?php echo base64_encode($upload_img12170); ?>" data-status="<?php echo htmlspecialchars($status12170); ?>" data-category="<?php echo htmlspecialchars($category12170); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12170); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12170); ?>; position:absolute; top:295px; left:610px;'>
                        </div>


                        <!-- ASSET 12171 -->
                        <img src='../image.php?id=12171' style='width:15px; cursor:pointer; position:absolute; top:300px; left:540px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12171' onclick='fetchAssetData(12171);' class="asset-image" data-id="<?php echo $assetId12171; ?>" data-room="<?php echo htmlspecialchars($room12171); ?>" data-floor="<?php echo htmlspecialchars($floor12171); ?>" data-image="<?php echo base64_encode($upload_img12171); ?>" data-status="<?php echo htmlspecialchars($status12171); ?>" data-category="<?php echo htmlspecialchars($category12171); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12171); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12171); ?>; position:absolute; top:295px; left:550px;'>
                        </div>

                        <!-- ASSET 12172 -->
                        <img src='../image.php?id=12172' style='width:15px; cursor:pointer; position:absolute; top:270px; left:600px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12172' onclick='fetchAssetData(12172);' class="asset-image" data-id="<?php echo $assetId12172; ?>" data-room="<?php echo htmlspecialchars($room12172); ?>" data-floor="<?php echo htmlspecialchars($floor12172); ?>" data-image="<?php echo base64_encode($upload_img12172); ?>" data-status="<?php echo htmlspecialchars($status12172); ?>" data-category="<?php echo htmlspecialchars($category12172); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12172); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12172); ?>; position:absolute; top:265px; left:610px;'>
                        </div>


                        <!-- ASSET 12173 -->
                        <img src='../image.php?id=12173' style='width:15px; cursor:pointer; position:absolute; top:270px; left:540px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12173' onclick='fetchAssetData(12173);' class="asset-image" data-id="<?php echo $assetId12173; ?>" data-room="<?php echo htmlspecialchars($room12173); ?>" data-floor="<?php echo htmlspecialchars($floor12173); ?>" data-image="<?php echo base64_encode($upload_img12173); ?>" data-status="<?php echo htmlspecialchars($status12173); ?>" data-category="<?php echo htmlspecialchars($category12173); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12173); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12173); ?>; position:absolute; top:265px; left:550px;'>
                        </div>

                        <!-- ASSET 12174 -->
                        <img src='../image.php?id=12174' style='width:15px; cursor:pointer; position:absolute; top:240px; left:600px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12174' onclick='fetchAssetData(12174);' class="asset-image" data-id="<?php echo $assetId12174; ?>" data-room="<?php echo htmlspecialchars($room12174); ?>" data-floor="<?php echo htmlspecialchars($floor12174); ?>" data-image="<?php echo base64_encode($upload_img12174); ?>" data-status="<?php echo htmlspecialchars($status12174); ?>" data-category="<?php echo htmlspecialchars($category12174); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12174); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12174); ?>; position:absolute; top:235px; left:610px;'>
                        </div>


                        <!-- ASSET 12175 -->
                        <img src='../image.php?id=12175' style='width:15px; cursor:pointer; position:absolute; top:240px; left:540px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12175' onclick='fetchAssetData(12175);' class="asset-image" data-id="<?php echo $assetId12175; ?>" data-room="<?php echo htmlspecialchars($room12175); ?>" data-floor="<?php echo htmlspecialchars($floor12175); ?>" data-image="<?php echo base64_encode($upload_img12175); ?>" data-status="<?php echo htmlspecialchars($status12175); ?>" data-category="<?php echo htmlspecialchars($category12175); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12175); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12175); ?>; position:absolute; top:235px; left:550px;'>
                        </div>

                        <!-- ASSET 12176 -->
                        <img src='../image.php?id=12176' style='width:15px; cursor:pointer; position:absolute; top:210px; left:600px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12176' onclick='fetchAssetData(12176);' class="asset-image" data-id="<?php echo $assetId12176; ?>" data-room="<?php echo htmlspecialchars($room12176); ?>" data-floor="<?php echo htmlspecialchars($floor12176); ?>" data-image="<?php echo base64_encode($upload_img12176); ?>" data-status="<?php echo htmlspecialchars($status12176); ?>" data-category="<?php echo htmlspecialchars($category12176); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12176); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12176); ?>; position:absolute; top:205px; left:610px;'>
                        </div>


                        <!-- ASSET 12177 -->
                        <img src='../image.php?id=12177' style='width:15px; cursor:pointer; position:absolute; top:210px; left:540px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12177' onclick='fetchAssetData(12177);' class="asset-image" data-id="<?php echo $assetId12177; ?>" data-room="<?php echo htmlspecialchars($room12177); ?>" data-floor="<?php echo htmlspecialchars($floor12177); ?>" data-image="<?php echo base64_encode($upload_img12177); ?>" data-status="<?php echo htmlspecialchars($status12177); ?>" data-category="<?php echo htmlspecialchars($category12177); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12177); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12177); ?>; position:absolute; top:205px; left:550px;'>
                        </div>

                        <!-- ASSET 12178 -->
                        <img src='../image.php?id=12178' style='width:15px; cursor:pointer; position:absolute; top:280px; left:650px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12178' onclick='fetchAssetData(12178);' class="asset-image" data-id="<?php echo $assetId12178; ?>" data-room="<?php echo htmlspecialchars($room12178); ?>" data-floor="<?php echo htmlspecialchars($floor12178); ?>" data-image="<?php echo base64_encode($upload_img12178); ?>" data-status="<?php echo htmlspecialchars($status12178); ?>" data-category="<?php echo htmlspecialchars($category12178); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12178); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12178); ?>; position:absolute; top:275px; left:660px;'>
                        </div>

                        <!-- ASSET 12179 -->
                        <img src='../image.php?id=12179' style='width:15px; cursor:pointer; position:absolute; top:280px; left:710px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12179' onclick='fetchAssetData(12179);' class="asset-image" data-id="<?php echo $assetId12179; ?>" data-room="<?php echo htmlspecialchars($room12179); ?>" data-floor="<?php echo htmlspecialchars($floor12179); ?>" data-image="<?php echo base64_encode($upload_img12179); ?>" data-status="<?php echo htmlspecialchars($status12179); ?>" data-category="<?php echo htmlspecialchars($category12179); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12179); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12179); ?>; position:absolute; top:275px; left:720px;'>
                        </div>

                        <!-- ASSET 12180 -->
                        <img src='../image.php?id=12180' style='width:15px; cursor:pointer; position:absolute; top:280px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12180' onclick='fetchAssetData(12180);' class="asset-image" data-id="<?php echo $assetId12180; ?>" data-room="<?php echo htmlspecialchars($room12180); ?>" data-floor="<?php echo htmlspecialchars($floor12180); ?>" data-image="<?php echo base64_encode($upload_img12180); ?>" data-status="<?php echo htmlspecialchars($status12180); ?>" data-category="<?php echo htmlspecialchars($category12180); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12180); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12180); ?>; position:absolute; top:275px; left:780px;'>
                        </div>

                        <!-- ASSET 12181 -->
                        <img src='../image.php?id=12181' style='width:15px; cursor:pointer; position:absolute; top:280px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12181' onclick='fetchAssetData(12181);' class="asset-image" data-id="<?php echo $assetId12181; ?>" data-room="<?php echo htmlspecialchars($room12181); ?>" data-floor="<?php echo htmlspecialchars($floor12181); ?>" data-image="<?php echo base64_encode($upload_img12181); ?>" data-status="<?php echo htmlspecialchars($status12181); ?>" data-category="<?php echo htmlspecialchars($category12181); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12181); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12181); ?>; position:absolute; top:275px; left:840px;'>
                        </div>

                        <!-- ASSET 12182 -->
                        <img src='../image.php?id=12182' style='width:15px; cursor:pointer; position:absolute; top:280px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12182' onclick='fetchAssetData(12182);' class="asset-image" data-id="<?php echo $assetId12182; ?>" data-room="<?php echo htmlspecialchars($room12182); ?>" data-floor="<?php echo htmlspecialchars($floor12182); ?>" data-image="<?php echo base64_encode($upload_img12182); ?>" data-status="<?php echo htmlspecialchars($status12182); ?>" data-category="<?php echo htmlspecialchars($category12182); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12182); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12182); ?>; position:absolute; top:275px; left:900px;'>
                        </div>

                        <!-- ASSET 12183 -->
                        <img src='../image.php?id=12183' style='width:15px; cursor:pointer; position:absolute; top:280px; left:950px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12183' onclick='fetchAssetData(12183);' class="asset-image" data-id="<?php echo $assetId12183; ?>" data-room="<?php echo htmlspecialchars($room12183); ?>" data-floor="<?php echo htmlspecialchars($floor12183); ?>" data-image="<?php echo base64_encode($upload_img12183); ?>" data-status="<?php echo htmlspecialchars($status12183); ?>" data-category="<?php echo htmlspecialchars($category12183); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12183); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12183); ?>; position:absolute; top:275px; left:960px;'>
                        </div>

                        <!-- ASSET 12184 -->
                        <img src='../image.php?id=12184' style='width:15px; cursor:pointer; position:absolute; top:280px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12184' onclick='fetchAssetData(12184);' class="asset-image" data-id="<?php echo $assetId12184; ?>" data-room="<?php echo htmlspecialchars($room12184); ?>" data-floor="<?php echo htmlspecialchars($floor12184); ?>" data-image="<?php echo base64_encode($upload_img12184); ?>" data-status="<?php echo htmlspecialchars($status12184); ?>" data-category="<?php echo htmlspecialchars($category12184); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12184); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12184); ?>; position:absolute; top:275px; left:1020px;'>
                        </div>

                        <!-- ASSET 12185 -->
                        <img src='../image.php?id=12185' style='width:15px; cursor:pointer; position:absolute; top:280px; left:1070px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12185' onclick='fetchAssetData(12185);' class="asset-image" data-id="<?php echo $assetId12185; ?>" data-room="<?php echo htmlspecialchars($room12185); ?>" data-floor="<?php echo htmlspecialchars($floor12185); ?>" data-image="<?php echo base64_encode($upload_img12185); ?>" data-status="<?php echo htmlspecialchars($status12185); ?>" data-category="<?php echo htmlspecialchars($category12185); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12185); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12185); ?>; position:absolute; top:275px; left:1080px;'>
                        </div>

                        <!-- ASSET 12186 -->
                        <img src='../image.php?id=12186' style='width:15px; cursor:pointer; position:absolute; top:280px; left:1130px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal12186' onclick='fetchAssetData(12186);' class="asset-image" data-id="<?php echo $assetId12186; ?>" data-room="<?php echo htmlspecialchars($room12186); ?>" data-floor="<?php echo htmlspecialchars($floor12186); ?>" data-image="<?php echo base64_encode($upload_img12186); ?>" data-status="<?php echo htmlspecialchars($status12186); ?>" data-category="<?php echo htmlspecialchars($category12186); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName12186); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status12186); ?>; position:absolute; top:275px; left:1140px;'>
                        </div>


                        <!-- ASSET 11854 -->
                        <img src='../image.php?id=11854' style='width:15px; cursor:pointer; position:absolute; top:415px; left:80px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11854' onclick='fetchAssetData(11854);' class="asset-image" data-id="<?php echo $assetId11854; ?>" data-room="<?php echo htmlspecialchars($room11854); ?>" data-floor="<?php echo htmlspecialchars($floor11854); ?>" data-image="<?php echo base64_encode($upload_img11854); ?>" data-status="<?php echo htmlspecialchars($status11854); ?>" data-category="<?php echo htmlspecialchars($category11854); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11854); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11854); ?>; position:absolute; top:410px; left:90px;'>
                        </div>

                        <!-- ASSET 11855 -->
                        <img src='../image.php?id=11855' style='width:15px; cursor:pointer; position:absolute; top:415px; left:135px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11855' onclick='fetchAssetData(11855);' class="asset-image" data-id="<?php echo $assetId11855; ?>" data-room="<?php echo htmlspecialchars($room11855); ?>" data-floor="<?php echo htmlspecialchars($floor11855); ?>" data-image="<?php echo base64_encode($upload_img11855); ?>" data-status="<?php echo htmlspecialchars($status11855); ?>" data-category="<?php echo htmlspecialchars($category11855); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11855); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11855); ?>; position:absolute; top:410px; left:145px;'>
                        </div>

                        <!-- ASSET 11856 -->
                        <img src='../image.php?id=11856' style='width:15px; cursor:pointer; position:absolute; top:460px; left:80px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11856' onclick='fetchAssetData(11856);' class="asset-image" data-id="<?php echo $assetId11856; ?>" data-room="<?php echo htmlspecialchars($room11856); ?>" data-floor="<?php echo htmlspecialchars($floor11856); ?>" data-image="<?php echo base64_encode($upload_img11856); ?>" data-status="<?php echo htmlspecialchars($status11856); ?>" data-category="<?php echo htmlspecialchars($category11856); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11856); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11856); ?>; position:absolute; top:455px; left:90px;'>
                        </div>

                        <!-- ASSET 11857 -->
                        <img src='../image.php?id=11857' style='width:15px; cursor:pointer; position:absolute; top:460px; left:135px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11857' onclick='fetchAssetData(11857);' class="asset-image" data-id="<?php echo $assetId11857; ?>" data-room="<?php echo htmlspecialchars($room11857); ?>" data-floor="<?php echo htmlspecialchars($floor11857); ?>" data-image="<?php echo base64_encode($upload_img11857); ?>" data-status="<?php echo htmlspecialchars($status11857); ?>" data-category="<?php echo htmlspecialchars($category11857); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11857); ?>">
                        <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11857); ?>; position:absolute; top:455px; left:145px;'>
                        </div>






                        <!--Start of hover-->
                        <div id="hover-asset" class="hover-asset" style="display: none;">
                            <!-- Content will be added dynamically -->
                        </div>
                    </div>
                    <?php

                    // Function to generate modal structure for a given asset
                    function generateModal($assetId, $room, $floor, $upload_img, $status, $category, $assignedName, $assignedBy, $description)
                    {
                    ?>
                        <!-- Modal structure for asset with ID <?php echo $assetId; ?> -->
                        <div class='modal fade' id='imageModal<?php echo $assetId; ?>' tabindex=' -1' aria-labelledby='imageModalLabel<?php echo $assetId; ?>' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value=" <?php echo htmlspecialchars($assetId); ?>">
                                            <!--START DIV FOR IMAGE -->
                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src=" data:image/jpeg;base64,<?php echo base64_encode($upload_img); ?>
                            " alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->
                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId); ?>" readonly />
                                            </div>

                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room); ?>" readonly />
                                            </div>

                                            <!--End of Second Row-->
                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor); ?>" readonly />
                                            </div>
                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" readonly />
                                            </div>
                                            <div class=" col-4" style="display:none">
                                                <label for=" images" class="form-label">Images:</label>
                                                <input type=" text" class="form-control" id="" name="images" readonly />
                                            </div>
                                            <!--End of Third Row-->
                                            <!--Fourth Row-->
                                            <div class="col-2 ">
                                                <label for="status" class="form-label">Status:</label>
                                            </div>
                                            <div class="col-6">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Working" <?php echo ($status == 'Working') ? 'selected="selected"' : ''; ?>>Working</option>
                                                    <option value="Under Maintenance" <?php echo ($status == 'Under Maintenance') ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                    <option value="For Replacement" <?php echo ($status == 'For Replacement') ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                    <option value="Need Repair" <?php echo ($status == 'Need Repair') ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                                </select>
                                            </div>
                                            <div class="col-4" style="display:none">
                                                <label for="assignedName" class="form-label">Assigned Name:</label>
                                                <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName); ?>" readonly />
                                            </div>
                                            <div class="col-4" style="display:none">
                                                <label for="assignedBy" class="form-label">Assigned By:</label>
                                                <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy); ?>" readonly />
                                            </div>
                                            <!--End of Fourth Row-->
                                            <!--Fifth Row-->
                                            <div class="col-12">
                                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description); ?>" />
                                            </div>
                                            <!--End of Fifth Row-->
                                            <!--Sixth Row-->
                                            <div class=" col-2 Upload">
                                                <label for="upload_img" class="form-label">Upload:</label>
                                            </div>
                                            <div class="col-9">
                                                <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                            </div>
                                            <!--End of Sixth Row-->
                                            <!-- Modal footer -->
                                            <div class="button-submit-container">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop<?php echo $assetId; ?>">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Edit for table <?php echo $assetId; ?>-->
                        <div class="map-alert">
                            <div class="modal fade" id="staticBackdrop<?php echo $assetId; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            <p>Are you sure you want to save changes?</p>
                                            <div class="modal-popups">
                                                <button type="submit" class="btn add-modal-btn" name="edit<?php echo $assetId; ?>">Yes</button>
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    <?php
                    }

                    // Call the generateModal function for each asset
                    foreach ($assetIds as $id) {
                        generateModal($id, ${'room' . $id}, ${'floor' . $id}, ${'upload_img' . $id}, ${'status' . $id}, ${'category' . $id}, ${'assignedName' . $id}, ${'assignedBy' . $id}, ${'description' . $id});
                    }
                    ?>
                </div>
            </main>
        </section>

        <script>
            $(document).ready(function() {
                $('.notification-item').on('click', function(e) {
                    e.preventDefault();
                    var activityId = $(this).data('activity-id');
                    var notificationItem = $(this); // Store the clicked element

                    $.ajax({
                        type: "POST",
                        url: "../../administrator/update_single_notification.php", // The URL to the PHP file
                        data: {
                            activityId: activityId
                        },
                        success: function(response) {
                            if (response.trim() === "Notification updated successfully") {
                                // If the notification is updated successfully, remove the clicked element
                                notificationItem.remove();

                                // Update the notification count
                                var countElement = $('#noti_number');
                                var count = parseInt(countElement.text()) || 0;
                                countElement.text(count > 1 ? count - 1 : '');
                            } else {
                                // Handle error
                                console.error("Failed to update notification:", response);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle AJAX error
                            console.error("AJAX error:", status, error);
                        }
                    });
                });
            });
        </script>

        <!--Start of JS Hover-->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const assetImages = document.querySelectorAll('.asset-image');
                const hoverElement = document.getElementById('hover-asset');

                assetImages.forEach(image => {
                    image.addEventListener('mouseenter', function() {
                        const id = this.dataset.id;
                        const room = this.dataset.room;
                        const floor = this.dataset.floor;
                        const base64Data = this.dataset.image;
                        const category = this.dataset.category; // Get the category from the data attribute
                        const assignedName = this.dataset.assignedname; // Add this line to get the assignedName from the data attribute

                        let imageHTML = '';
                        if (base64Data && base64Data.trim() !== '') {
                            const imageSrc = "data:image/jpeg;base64," + base64Data;
                            imageHTML = `<img src="${imageSrc}" alt="Asset Image">`;
                        } else {
                            imageHTML = '<p class="NoImage">No Image uploaded</p>';
                        }

                        // Update hover element's content
                        hoverElement.innerHTML = `
                    <div class="top-side-hover">
                        <div class="center-content-hover">
                            ${imageHTML}
                        </div>
                        <input type="text" class="form-control input-hover" id="category-hover" value="${category}" readonly />
                    </div>

                    <div class="hover-location">

                        <div class ="hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover">Tracking #:</label>
                            <input type="text" class="form-control input-hover1 hover-input" id="assetId" value="${id}" readonly />
                        </div>

                        <div class = "hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover1">Room:</label>
                            <input type="text" class="form-control input-hover1 room-hover" id="room" value="${room}" readonly />
                        </div>

                        <div class = "hover-label">
                            <label for="assetIdHover${id}" class="form-label TrackingHover1">Floor:</label>
                            <input type="text" class="form-control input-hover1" id="floor" value="${floor}" readonly />
                        </div>

                    ${assignedName && assignedName.trim() !== '' ? `
                        <div>
                            <label for="assignedNameHover${id}" class="form-label TrackingHover">Assigned To:</label>
                            <input type="text" class="form-control input-hover1" id="assignedName" value="${assignedName}" readonly />
                        </div>
                     ` : ''
                        }
                    </div>
            `;

                        // Show hover element
                        hoverElement.style.display = 'block';
                    });

                    image.addEventListener('mouseleave', function() {
                        // Hide hover element
                        hoverElement.style.display = 'none';
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                var urlParams = new URLSearchParams(window.location.search);
                var assetId = urlParams.get('assetId'); // Get the assetId from the URL

                if (assetId) {
                    var modalId = '#imageModal' + assetId;
                    $(modalId).modal('show'); // Open the modal with the corresponding ID
                }
            });
        </script>

        <script>
            // Find all input elements with ID 'description'
            var inputElements = document.querySelectorAll('input#description');

            // Iterate through each input element
            inputElements.forEach(function(inputElement) {
                // Create a new textarea element
                var textareaElement = document.createElement('textarea');

                // Copy attributes from the input element
                textareaElement.className = inputElement.className;
                textareaElement.id = inputElement.id;
                textareaElement.name = inputElement.name;
                textareaElement.value = inputElement.value;

                // Replace the input element with the textarea element
                inputElement.parentNode.replaceChild(textareaElement, inputElement);
            });
        </script>

        <!--FOR LEGEND FILTER-->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const legendItems = document.querySelectorAll('.legend-item button');
                let activeStatuses = []; // Keep track of active statuses

                legendItems.forEach(item => {
                    item.addEventListener('click', function() {
                        const legendItem = this.closest('.legend-item');
                        const status = legendItem.getAttribute('data-status');
                        // Toggle the active status in the array
                        const isActive = activeStatuses.includes(status);
                        if (isActive) {
                            // Remove the status if it's already active
                            activeStatuses = activeStatuses.filter(s => s !== status);
                        } else {
                            // Add the status if it's not already active
                            activeStatuses.push(status);
                        }
                        // Toggle visibility of assets
                        toggleAssetVisibility(status);
                        // Update the opacity of legend items
                        updateLegendItems();
                    });
                });

                function toggleAssetVisibility(status) {
                    const assets = document.querySelectorAll(`.asset-image[data-status="${status}"]`);
                    assets.forEach(asset => {
                        const isHidden = asset.classList.contains('hidden-asset');
                        const statusIndicator = asset.nextElementSibling;

                        if (isHidden) {
                            asset.classList.remove('hidden-asset');
                            if (statusIndicator) {
                                statusIndicator.classList.remove('hidden-asset');
                            }
                        } else {
                            asset.classList.add('hidden-asset');
                            if (statusIndicator) {
                                statusIndicator.classList.add('hidden-asset');
                            }
                        }
                    });
                }

                function updateLegendItems() {
                    // Update the opacity of all legend items based on activeStatuses
                    const allLegendItems = document.querySelectorAll('.legend-item');
                    allLegendItems.forEach(legendItem => {
                        const status = legendItem.getAttribute('data-status');
                        if (activeStatuses.includes(status)) {
                            // If the status is active, change opacity to 50%
                            legendItem.style.opacity = '0.2';
                        } else {
                            // If the status is not active, revert opacity to 100%
                            legendItem.style.opacity = '1';
                        }
                    });
                }
            });
        </script>

        <script src="../../../src/js/main.js"></script>
        <script src="../../../src/js/logoutMap.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>