<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
// require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

session_start();
include_once ("../../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
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
               WHERE  al.seen = '0' AND al.accountID != ?
               ORDER BY al.date DESC 
               LIMIT 5"; // Set limit to 5


    // Prepare the SQL statement
    $stmtLatestLogs = $conn->prepare($sqlLatestLogs);

    // Bind the parameter to exclude the current user's account ID
    $stmtLatestLogs->bind_param("i", $loggedInAccountId);

    // Execute the query
    $stmtLatestLogs->execute();
    $resultLatestLogs = $stmtLatestLogs->get_result();


    $unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE seen = '0' AND accountID != ?";
    $stmt = $conn->prepare($unseenCountQuery);
    $stmt->bind_param("i", $loggedInAccountId);
    $stmt->execute();
    $stmt->bind_result($unseenCount);
    $stmt->fetch();
    $stmt->close();
    $todayDate = date("Y-m-d"); // Today's date


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

    //FOR ID 11518
    $sql11518 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11518";
    $stmt11518 = $conn->prepare($sql11518);
    $stmt11518->execute();
    $result11518 = $stmt11518->get_result();
    $row11518 = $result11518->fetch_assoc();
    $assetId11518 = $row11518['assetId'];
    $category11518 = $row11518['category'];
    $date11518 = $row11518['date'];
    $building11518 = $row11518['building'];
    $floor11518 = $row11518['floor'];
    $room11518 = $row11518['room'];
    $status11518 = $row11518['status'];
    $assignedName11518 = $row11518['assignedName'];
    $assignedBy11518 = $row11518['assignedBy'];
    $upload_img11518 = $row11518['upload_img'];
    $description11518 = $row11518['description'];

    //FOR ID 11519
    $sql11519 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11519";
    $stmt11519 = $conn->prepare($sql11519);
    $stmt11519->execute();
    $result11519 = $stmt11519->get_result();
    $row11519 = $result11519->fetch_assoc();
    $assetId11519 = $row11519['assetId'];
    $category11519 = $row11519['category'];
    $date11519 = $row11519['date'];
    $building11519 = $row11519['building'];
    $floor11519 = $row11519['floor'];
    $room11519 = $row11519['room'];
    $status11519 = $row11519['status'];
    $assignedName11519 = $row11519['assignedName'];
    $assignedBy11519 = $row11519['assignedBy'];
    $upload_img11519 = $row11519['upload_img'];
    $description11519 = $row11519['description'];

    //FOR ID 11520
    $sql11520 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11520";
    $stmt11520 = $conn->prepare($sql11520);
    $stmt11520->execute();
    $result11520 = $stmt11520->get_result();
    $row11520 = $result11520->fetch_assoc();
    $assetId11520 = $row11520['assetId'];
    $category11520 = $row11520['category'];
    $date11520 = $row11520['date'];
    $building11520 = $row11520['building'];
    $floor11520 = $row11520['floor'];
    $room11520 = $row11520['room'];
    $status11520 = $row11520['status'];
    $assignedName11520 = $row11520['assignedName'];
    $assignedBy11520 = $row11520['assignedBy'];
    $upload_img11520 = $row11520['upload_img'];
    $description11520 = $row11520['description'];

    //FOR ID 11521
    $sql11521 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11521";
    $stmt11521 = $conn->prepare($sql11521);
    $stmt11521->execute();
    $result11521 = $stmt11521->get_result();
    $row11521 = $result11521->fetch_assoc();
    $assetId11521 = $row11521['assetId'];
    $category11521 = $row11521['category'];
    $date11521 = $row11521['date'];
    $building11521 = $row11521['building'];
    $floor11521 = $row11521['floor'];
    $room11521 = $row11521['room'];
    $status11521 = $row11521['status'];
    $assignedName11521 = $row11521['assignedName'];
    $assignedBy11521 = $row11521['assignedBy'];
    $upload_img11521 = $row11521['upload_img'];
    $description11521 = $row11521['description'];

    //FOR ID 11523
    $sql11523 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11523";
    $stmt11523 = $conn->prepare($sql11523);
    $stmt11523->execute();
    $result11523 = $stmt11523->get_result();
    $row11523 = $result11523->fetch_assoc();
    $assetId11523 = $row11523['assetId'];
    $category11523 = $row11523['category'];
    $date11523 = $row11523['date'];
    $building11523 = $row11523['building'];
    $floor11523 = $row11523['floor'];
    $room11523 = $row11523['room'];
    $status11523 = $row11523['status'];
    $assignedName11523 = $row11523['assignedName'];
    $assignedBy11523 = $row11523['assignedBy'];
    $upload_img11523 = $row11523['upload_img'];
    $description11523 = $row11523['description'];

    //FOR ID 11524
    $sql11524 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11524";
    $stmt11524 = $conn->prepare($sql11524);
    $stmt11524->execute();
    $result11524 = $stmt11524->get_result();
    $row11524 = $result11524->fetch_assoc();
    $assetId11524 = $row11524['assetId'];
    $category11524 = $row11524['category'];
    $date11524 = $row11524['date'];
    $building11524 = $row11524['building'];
    $floor11524 = $row11524['floor'];
    $room11524 = $row11524['room'];
    $status11524 = $row11524['status'];
    $assignedName11524 = $row11524['assignedName'];
    $assignedBy11524 = $row11524['assignedBy'];
    $upload_img11524 = $row11524['upload_img'];
    $description11524 = $row11524['description'];

    //FOR ID 11525
    $sql11525 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11525";
    $stmt11525 = $conn->prepare($sql11525);
    $stmt11525->execute();
    $result11525 = $stmt11525->get_result();
    $row11525 = $result11525->fetch_assoc();
    $assetId11525 = $row11525['assetId'];
    $category11525 = $row11525['category'];
    $date11525 = $row11525['date'];
    $building11525 = $row11525['building'];
    $floor11525 = $row11525['floor'];
    $room11525 = $row11525['room'];
    $status11525 = $row11525['status'];
    $assignedName11525 = $row11525['assignedName'];
    $assignedBy11525 = $row11525['assignedBy'];
    $upload_img11525 = $row11525['upload_img'];
    $description11525 = $row11525['description'];

    //FOR ID 11526
    $sql11526 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11526";
    $stmt11526 = $conn->prepare($sql11526);
    $stmt11526->execute();
    $result11526 = $stmt11526->get_result();
    $row11526 = $result11526->fetch_assoc();
    $assetId11526 = $row11526['assetId'];
    $category11526 = $row11526['category'];
    $date11526 = $row11526['date'];
    $building11526 = $row11526['building'];
    $floor11526 = $row11526['floor'];
    $room11526 = $row11526['room'];
    $status11526 = $row11526['status'];
    $assignedName11526 = $row11526['assignedName'];
    $assignedBy11526 = $row11526['assignedBy'];
    $upload_img11526 = $row11526['upload_img'];
    $description11526 = $row11526['description'];

    //FOR ID 11527
    $sql11527 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11527";
    $stmt11527 = $conn->prepare($sql11527);
    $stmt11527->execute();
    $result11527 = $stmt11527->get_result();
    $row11527 = $result11527->fetch_assoc();
    $assetId11527 = $row11527['assetId'];
    $category11527 = $row11527['category'];
    $date11527 = $row11527['date'];
    $building11527 = $row11527['building'];
    $floor11527 = $row11527['floor'];
    $room11527 = $row11527['room'];
    $status11527 = $row11527['status'];
    $assignedName11527 = $row11527['assignedName'];
    $assignedBy11527 = $row11527['assignedBy'];
    $upload_img11527 = $row11527['upload_img'];
    $description11527 = $row11527['description'];

    //FOR ID 11528
    $sql11528 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11528";
    $stmt11528 = $conn->prepare($sql11528);
    $stmt11528->execute();
    $result11528 = $stmt11528->get_result();
    $row11528 = $result11528->fetch_assoc();
    $assetId11528 = $row11528['assetId'];
    $category11528 = $row11528['category'];
    $date11528 = $row11528['date'];
    $building11528 = $row11528['building'];
    $floor11528 = $row11528['floor'];
    $room11528 = $row11528['room'];
    $status11528 = $row11528['status'];
    $assignedName11528 = $row11528['assignedName'];
    $assignedBy11528 = $row11528['assignedBy'];
    $upload_img11528 = $row11528['upload_img'];
    $description11528 = $row11528['description'];
    //FOR ID 11529
    $sql11529 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11529";
    $stmt11529 = $conn->prepare($sql11529);
    $stmt11529->execute();
    $result11529 = $stmt11529->get_result();
    $row11529 = $result11529->fetch_assoc();
    $assetId11529 = $row11529['assetId'];
    $category11529 = $row11529['category'];
    $date11529 = $row11529['date'];
    $building11529 = $row11529['building'];
    $floor11529 = $row11529['floor'];
    $room11529 = $row11529['room'];
    $status11529 = $row11529['status'];
    $assignedName11529 = $row11529['assignedName'];
    $assignedBy11529 = $row11529['assignedBy'];
    $upload_img11529 = $row11529['upload_img'];
    $description11529 = $row11529['description'];

    //FOR ID 11530
    $sql11530 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11530";
    $stmt11530 = $conn->prepare($sql11530);
    $stmt11530->execute();
    $result11530 = $stmt11530->get_result();
    $row11530 = $result11530->fetch_assoc();
    $assetId11530 = $row11530['assetId'];
    $category11530 = $row11530['category'];
    $date11530 = $row11530['date'];
    $building11530 = $row11530['building'];
    $floor11530 = $row11530['floor'];
    $room11530 = $row11530['room'];
    $status11530 = $row11530['status'];
    $assignedName11530 = $row11530['assignedName'];
    $assignedBy11530 = $row11530['assignedBy'];
    $upload_img11530 = $row11530['upload_img'];
    $description11530 = $row11530['description'];
    //FOR ID 11531
    $sql11531 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11531";
    $stmt11531 = $conn->prepare($sql11531);
    $stmt11531->execute();
    $result11531 = $stmt11531->get_result();
    $row11531 = $result11531->fetch_assoc();
    $assetId11531 = $row11531['assetId'];
    $category11531 = $row11531['category'];
    $date11531 = $row11531['date'];
    $building11531 = $row11531['building'];
    $floor11531 = $row11531['floor'];
    $room11531 = $row11531['room'];
    $status11531 = $row11531['status'];
    $assignedName11531 = $row11531['assignedName'];
    $assignedBy11531 = $row11531['assignedBy'];
    $upload_img11531 = $row11531['upload_img'];
    $description11531 = $row11531['description'];



    //FOR ID 11531
    $sql11531 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11531";
    $stmt11531 = $conn->prepare($sql11531);
    $stmt11531->execute();
    $result11531 = $stmt11531->get_result();
    $row11531 = $result11531->fetch_assoc();
    $assetId11531 = $row11531['assetId'];
    $category11531 = $row11531['category'];
    $date11531 = $row11531['date'];
    $building11531 = $row11531['building'];
    $floor11531 = $row11531['floor'];
    $room11531 = $row11531['room'];
    $status11531 = $row11531['status'];
    $assignedName11531 = $row11531['assignedName'];
    $assignedBy11531 = $row11531['assignedBy'];
    $upload_img11531 = $row11531['upload_img'];
    $description11531 = $row11531['description'];

    //FOR ID 11532
    $sql11532 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11532";
    $stmt11532 = $conn->prepare($sql11532);
    $stmt11532->execute();
    $result11532 = $stmt11532->get_result();
    $row11532 = $result11532->fetch_assoc();
    $assetId11532 = $row11532['assetId'];
    $category11532 = $row11532['category'];
    $date11532 = $row11532['date'];
    $building11532 = $row11532['building'];
    $floor11532 = $row11532['floor'];
    $room11532 = $row11532['room'];
    $status11532 = $row11532['status'];
    $assignedName11532 = $row11532['assignedName'];
    $assignedBy11532 = $row11532['assignedBy'];
    $upload_img11532 = $row11532['upload_img'];
    $description11532 = $row11532['description'];

    //FOR ID 11533
    $sql11533 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11533";
    $stmt11533 = $conn->prepare($sql11533);
    $stmt11533->execute();
    $result11533 = $stmt11533->get_result();
    $row11533 = $result11533->fetch_assoc();
    $assetId11533 = $row11533['assetId'];
    $category11533 = $row11533['category'];
    $date11533 = $row11533['date'];
    $building11533 = $row11533['building'];
    $floor11533 = $row11533['floor'];
    $room11533 = $row11533['room'];
    $status11533 = $row11533['status'];
    $assignedName11533 = $row11533['assignedName'];
    $assignedBy11533 = $row11533['assignedBy'];
    $upload_img11533 = $row11533['upload_img'];
    $description11533 = $row11533['description'];

    //FOR ID 11534
    $sql11534 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11534";
    $stmt11534 = $conn->prepare($sql11534);
    $stmt11534->execute();
    $result11534 = $stmt11534->get_result();
    $row11534 = $result11534->fetch_assoc();
    $assetId11534 = $row11534['assetId'];
    $category11534 = $row11534['category'];
    $date11534 = $row11534['date'];
    $building11534 = $row11534['building'];
    $floor11534 = $row11534['floor'];
    $room11534 = $row11534['room'];
    $status11534 = $row11534['status'];
    $assignedName11534 = $row11534['assignedName'];
    $assignedBy11534 = $row11534['assignedBy'];
    $upload_img11534 = $row11534['upload_img'];
    $description11534 = $row11534['description'];

    //FOR ID 11535
    $sql11535 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11535";
    $stmt11535 = $conn->prepare($sql11535);
    $stmt11535->execute();
    $result11535 = $stmt11535->get_result();
    $row11535 = $result11535->fetch_assoc();
    $assetId11535 = $row11535['assetId'];
    $category11535 = $row11535['category'];
    $date11535 = $row11535['date'];
    $building11535 = $row11535['building'];
    $floor11535 = $row11535['floor'];
    $room11535 = $row11535['room'];
    $status11535 = $row11535['status'];
    $assignedName11535 = $row11535['assignedName'];
    $assignedBy11535 = $row11535['assignedBy'];
    $upload_img11535 = $row11535['upload_img'];
    $description11535 = $row11535['description'];

    //FOR ID 11536
    $sql11536 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 11536";
    $stmt11536 = $conn->prepare($sql11536);
    $stmt11536->execute();
    $result11536 = $stmt11536->get_result();
    $row11536 = $result11536->fetch_assoc();
    $assetId11536 = $row11536['assetId'];
    $category11536 = $row11536['category'];
    $date11536 = $row11536['date'];
    $building11536 = $row11536['building'];
    $floor11536 = $row11536['floor'];
    $room11536 = $row11536['room'];
    $status11536 = $row11536['status'];
    $assignedName11536 = $row11536['assignedName'];
    $assignedBy11536 = $row11536['assignedBy'];
    $upload_img11536 = $row11536['upload_img'];
    $description11536 = $row11536['description'];

    // Function to update asset information based on asset ID
    // Function to handle update for a given asset ID
    function updateAsset($conn, $assetId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit' . $assetId])) {
            // Get form data
            $status = $_POST['status'];
            $description = $_POST['description'];
            $room = $_POST['room'];
            $assignedBy = $_POST['assignedBy'];
            // Assuming assignedName is fetched from somewhere
            $assignedName = ''; // Change this according to your logic

            // Check if status is "Need Repair" and set "Assigned Name" to none
            $assignedName = $status === 'Need Repair' ? '' : $assignedName;

            // Prepare SQL query to update the asset
            $sql = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssi', $status, $assignedName, $assignedBy, $description, $room, $assetId);

            if ($stmt->execute()) {
                // Update success
                logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId to $status.", 'Report');
                echo "<script>alert('Asset updated successfully!');</script>";
                header("Location: BABF1.php");
            } else {
                // Update failed
                echo "<script>alert('Failed to update asset.');</script>";
            }
            $stmt->close();
        }
    }

    // Handle form submission for any asset ID
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
                header("Location: BABF1.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
        }
    }

    // Call updateAsset function for each asset ID you want to handle
    $assetIds = [11518, 11519, 11520, 11521, 11523, 11524, 11525, 11526, 11527, 11528, 11529, 11530, 11531, 11532, 11533, 11534, 11535, 11536]; // Add more asset IDs here
    foreach ($assetIds as $id) {
        updateAsset($conn, $id);
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
                <a href="../../administrator/dashboard.php">
                    <i class="bi bi-grid"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/attendance-logs.php">
                    <i class="bi bi-calendar-week"></i>
                    <span class="text">Attendance Logs</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/staff.php">
                    <i class="bi bi-person"></i>
                    <span class="text">Staff</span>
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
                    <a href="../../administrator/gps.php">
                        <i class="bi bi-crosshair"></i>
                        <span class="text">GPS Tracker</span>
                    </a>
                </li>
                <li class="GPS-History">
                    <a href="../../administrator/gps-history.php">
                        <i class="bi bi-radar"></i>
                        <span class="text">GPS History</span>
                    </a>
                </li>
            </div>
            <li class="active">
                <a href="../../administrator/map.php">
                    <i class="bi bi-map"></i>
                    <span class="text">Map</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/reports.php">
                    <i class="bi bi-clipboard"></i>
                    <span class="text">Reports</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/archive.php">
                    <i class="bi bi-archive"></i>
                    <span class="text">Archive</span>
                </a>
            </li>
            <li>
                <a href="../../administrator/activity-logs.php">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span class="text">Activity Logs</span>
                </a>
            </li>
        </ul>
    </section>
    <div id="map-top-nav">
        <a href="../../administrator/map.php" class="closeFloor"><i class="bi bi-box-arrow-left"></i></i></a>

        <div class="legend-button" id="legendButton">
            <i class="bi bi-info-circle"></i>
        </div>
    </div>
    <section id="content">
        <main>
            <div class="content-container" id="content-container">
                <div id="belmonte-F1" class="content">
                    <!-- FLOOR PLAN -->
                    <img src="../../../src/floors/bautistaB/BB1F.png" alt="" class="Floor-container">

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
                        <div class="legend-item"><img src="../../../src/legend/TOILET-SEAT.jpg" alt=""
                                class="legend-img">
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




                    <!-- ASSET 11518 -->
                    <img src='../image.php?id=11518'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:150px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11518' onclick='fetchAssetData(11518);'
                        class="asset-image" data-id="<?php echo $assetId11518; ?>"
                        data-room="<?php echo htmlspecialchars($room11518); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11518); ?>"
                        data-image="<?php echo base64_encode($upload_img11518); ?>"
                        data-status="<?php echo htmlspecialchars($status11518); ?>"
                        data-category="<?php echo htmlspecialchars($category11518); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11518); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11518); ?>; 
    position:absolute; top:365px; left:160px;'>
                    </div>

                    <!-- ASSET 11519 -->
                    <img src='../image.php?id=11519'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:190px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11519' onclick='fetchAssetData(11519);'
                        class="asset-image" data-id="<?php echo $assetId11519; ?>"
                        data-room="<?php echo htmlspecialchars($room11519); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11519); ?>"
                        data-image="<?php echo base64_encode($upload_img11519); ?>"
                        data-status="<?php echo htmlspecialchars($status11519); ?>"
                        data-category="<?php echo htmlspecialchars($category11519); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11519); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11519); ?>; 
    position:absolute; top:365px; left:200px;'>
                    </div>

                    <!-- ASSET 11520 -->
                    <img src='../image.php?id=11520'
                        style='width:15px; cursor:pointer; position:absolute; top:395px; left:150px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11520' onclick='fetchAssetData(11520);'
                        class="asset-image" data-id="<?php echo $assetId11520; ?>"
                        data-room="<?php echo htmlspecialchars($room11520); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11520); ?>"
                        data-image="<?php echo base64_encode($upload_img11520); ?>"
                        data-status="<?php echo htmlspecialchars($status11520); ?>"
                        data-category="<?php echo htmlspecialchars($category11520); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11520); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11520); ?>; 
    position:absolute; top:390px; left:160px;'>
                    </div>

                    <!-- ASSET 11521 -->
                    <img src='../image.php?id=11521'
                        style='width:15px; cursor:pointer; position:absolute; top:395px; left:190px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11521' onclick='fetchAssetData(11521);'
                        class="asset-image" data-id="<?php echo $assetId11521; ?>"
                        data-room="<?php echo htmlspecialchars($room11521); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11521); ?>"
                        data-image="<?php echo base64_encode($upload_img11521); ?>"
                        data-status="<?php echo htmlspecialchars($status11521); ?>"
                        data-category="<?php echo htmlspecialchars($category11521); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11521); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11521); ?>; 
    position:absolute; top:390px; left:200px;'>
                    </div>

                    <!-- ASSET 11523 -->
                    <img src='../image.php?id=11523'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11523' onclick='fetchAssetData(11523);'
                        class="asset-image" data-id="<?php echo $assetId11523; ?>"
                        data-room="<?php echo htmlspecialchars($room11523); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11523); ?>"
                        data-image="<?php echo base64_encode($upload_img11523); ?>"
                        data-status="<?php echo htmlspecialchars($status11523); ?>"
                        data-category="<?php echo htmlspecialchars($category11523); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11523); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11523); ?>; 
    position:absolute; top:365px; left:230px;'>
                    </div>

                    <!-- ASSET 11524 -->
                    <img src='../image.php?id=11524'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:270px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11524' onclick='fetchAssetData(11524);'
                        class="asset-image" data-id="<?php echo $assetId11524; ?>"
                        data-room="<?php echo htmlspecialchars($room11524); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11524); ?>"
                        data-image="<?php echo base64_encode($upload_img11524); ?>"
                        data-status="<?php echo htmlspecialchars($status11524); ?>"
                        data-category="<?php echo htmlspecialchars($category11524); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11524); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11524); ?>; 
    position:absolute; top:365px; left:280px;'>
                    </div>

                    <!-- ASSET 11525 -->
                    <img src='../image.php?id=11525'
                        style='width:15px; cursor:pointer; position:absolute; top:410px; left:270px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11525' onclick='fetchAssetData(11525);'
                        class="asset-image" data-id="<?php echo $assetId11525; ?>"
                        data-room="<?php echo htmlspecialchars($room11525); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11525); ?>"
                        data-image="<?php echo base64_encode($upload_img11525); ?>"
                        data-status="<?php echo htmlspecialchars($status11525); ?>"
                        data-category="<?php echo htmlspecialchars($category11525); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11525); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11525); ?>; 
    position:absolute; top:405px; left:280px;'>
                    </div>

                    <!-- ASSET 11526 -->
                    <img src='../image.php?id=11526'
                        style='width:15px; cursor:pointer; position:absolute; top:410px; left:220px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11526' onclick='fetchAssetData(11526);'
                        class="asset-image" data-id="<?php echo $assetId11526; ?>"
                        data-room="<?php echo htmlspecialchars($room11526); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11526); ?>"
                        data-image="<?php echo base64_encode($upload_img11526); ?>"
                        data-status="<?php echo htmlspecialchars($status11526); ?>"
                        data-category="<?php echo htmlspecialchars($category11526); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11526); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11526); ?>; 
    position:absolute; top:405px; left:230px;'>
                    </div>

                    <!-- ASSET 11527 -->
                    <img src='../image.php?id=11527'
                        style='width:15px; cursor:pointer; position:absolute; top:415px; left:270px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11527'
                        onclick='fetchAssetData(11527);' class="asset-image" data-id="<?php echo $assetId11527; ?>"
                        data-room="<?php echo htmlspecialchars($room11527); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11527); ?>"
                        data-image="<?php echo base64_encode($upload_img11527); ?>"
                        data-status="<?php echo htmlspecialchars($status11527); ?>"
                        data-category="<?php echo htmlspecialchars($category11527); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11527); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11527); ?>; 
    position:absolute; top:415px; left:290px;'>
                    </div>


                    <!--Start of hover-->
                    <div id="hover-asset" class="hover-asset" style="display: none;">
                        <!-- Content will be added dynamically -->
                    </div>

                </div>

                <!-- Modal structure for id 11518-->
                <div class='modal fade' id='imageModal11518' tabindex='-1' aria-labelledby='imageModalLabel11518'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11518); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11518); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11518); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11518); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11518); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11518); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11518); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11518); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11518 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11518 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11518 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11518 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11518); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11518); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11518); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11518">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11518-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11518" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11518">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11519-->
                <div class='modal fade' id='imageModal11519' tabindex='-1' aria-labelledby='imageModalLabel11519'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11519); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11519); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11519); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11519); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11519); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11519); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11519); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11519); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11519 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11519 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11519 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11519 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11519); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11519); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11519); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11519">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11519-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11519" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11519">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11520-->
                <div class='modal fade' id='imageModal11520' tabindex='-1' aria-labelledby='imageModalLabel11520'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11520); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11520); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11520); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11520); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11520); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11520); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11520); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11520); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11520 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11520 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11520 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11520 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11520); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11520); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11520); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11520">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11520-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11520" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11520">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11521-->
                <div class='modal fade' id='imageModal11521' tabindex='-1' aria-labelledby='imageModalLabel11521'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11521); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11521); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11521); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11521); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11521); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11521); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11521); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11521); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11521 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11521 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11521 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11521 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11521); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11521); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11521); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11521">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11521-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11521" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11521">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11523-->
                <div class='modal fade' id='imageModal11523' tabindex='-1' aria-labelledby='imageModalLabel11523'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11523); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11523); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11523); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11523); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11523); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11523); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11523); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11523); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11523 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11523 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11523 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11523 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11523); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11523); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11523); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11523">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11523-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11523" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11523">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11524-->
                <div class='modal fade' id='imageModal11524' tabindex='-1' aria-labelledby='imageModalLabel11524'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11524); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11524); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11524); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11524); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11524); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11524); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11524); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11524); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11524 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11524 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11524 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11524 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11524); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11524); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11524); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11524">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11524-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11524" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11524">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11525-->
                <div class='modal fade' id='imageModal11525' tabindex='-1' aria-labelledby='imageModalLabel11525'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11525); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11525); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11525); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11525); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11525); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11525); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11525); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11525); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11525 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11525 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11525 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11525 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11525); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11525); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11525); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11525">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11525-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11525" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11525">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11526-->
                <div class='modal fade' id='imageModal11526' tabindex='-1' aria-labelledby='imageModalLabel11526'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11526); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11526); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11526); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11526); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11526); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11526); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11526); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11526); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11526 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11526 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11526 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11526 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11526); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11526); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11526); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11526">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11526-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11526" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11526">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11527-->
                <div class='modal fade' id='imageModal11527' tabindex='-1' aria-labelledby='imageModalLabel11527'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11527); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11527); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11527); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11527); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11527); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11527); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11527); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11527); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11527 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11527 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11527 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11527 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11527); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11527); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11527); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11527">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11527-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11527" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11527">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11528-->
                <div class='modal fade' id='imageModal11528' tabindex='-1' aria-labelledby='imageModalLabel11528'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11528); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11528); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11528); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11528); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11528); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11528); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11528); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11528); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11528 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11528 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11528 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11528 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11528); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11528); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11528); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11528">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11528-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11528" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11528">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11529-->
                <div class='modal fade' id='imageModal11529' tabindex='-1' aria-labelledby='imageModalLabel11529'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11529); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11529); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11529); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11529); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11529); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11529); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11529); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11529); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11529 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11529 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11529 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11529 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11529); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11529); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11529); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11529">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11529-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11529" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11529">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11530-->
                <div class='modal fade' id='imageModal11530' tabindex='-1' aria-labelledby='imageModalLabel11530'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11530); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11530); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11530); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11530); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11530); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11530); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11530); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11530); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11530 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11530 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11530 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11530 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11530); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11530); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11530); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11530">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11530-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11530" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11530">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11531-->
                <div class='modal fade' id='imageModal11531' tabindex='-1' aria-labelledby='imageModalLabel11531'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11531); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11531); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11531); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11531); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11531); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11531); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11531); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11531); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11531 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11531 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11531 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11531 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11531); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11531); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11531); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11531">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11531-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11531" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11531">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11532-->
                <div class='modal fade' id='imageModal11532' tabindex='-1' aria-labelledby='imageModalLabel11532'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11532); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11532); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11532); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11532); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11532); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11532); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11532); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11532); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11532 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11532 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11532 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11532 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11532); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11532); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11532); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11532">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11532-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11532" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11532">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11533-->
                <div class='modal fade' id='imageModal11533' tabindex='-1' aria-labelledby='imageModalLabel11533'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11533); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11533); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11533); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11533); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11533); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11533); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11533); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11533); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11533 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11533 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11533 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11533 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11533); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11533); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11533); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11533">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11533-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11533" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11533">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11534-->
                <div class='modal fade' id='imageModal11534' tabindex='-1' aria-labelledby='imageModalLabel11534'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11534); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11534); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11534); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11534); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11534); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11534); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11534); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11534); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11534 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11534 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11534 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11534 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11534); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11534); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11534); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11534">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11534-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11534" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11534">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11535-->
                <div class='modal fade' id='imageModal11535' tabindex='-1' aria-labelledby='imageModalLabel11535'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11535); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11535); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11535); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11535); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11535); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11535); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11535); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11535); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11535 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11535 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11535 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11535 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11535); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11535); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11535); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11535">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11535-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11535" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11535">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 11536-->
                <div class='modal fade' id='imageModal11536' tabindex='-1' aria-labelledby='imageModalLabel11536'
                    aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal'
                                    aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId"
                                        value="<?php echo htmlspecialchars($assetId11536); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img11536); ?>"
                                            alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId"
                                            value="<?php echo htmlspecialchars($assetId11536); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date"
                                            value="<?php echo htmlspecialchars($date11536); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room"
                                            value="<?php echo htmlspecialchars($room11536); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building"
                                            name="building" value="<?php echo htmlspecialchars($building11536); ?>"
                                            readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor"
                                            value="<?php echo htmlspecialchars($floor11536); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category"
                                            name="category" value="<?php echo htmlspecialchars($category11536); ?>"
                                            readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2 ">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status11536 == 'Working')
                                                ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status11536 == 'Under Maintenance')
                                                ? 'selected="selected"' : ''; ?>>Under Maintenance
                                            </option>
                                            <option value="For Replacement" <?php echo ($status11536 == 'For Replacement')
                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status11536 == 'Need Repair')
                                                ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName"
                                            value="<?php echo htmlspecialchars($assignedName11536); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                            value="<?php echo htmlspecialchars($assignedBy11536); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description"
                                            value="<?php echo htmlspecialchars($description11536); ?>" />
                                    </div>
                                    <!--End of Fifth Row-->

                                    <!--Sixth Row-->
                                    <div class="col-2 Upload">
                                        <label for="upload_img" class="form-label">Upload:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" class="form-control" id="upload_img" name="upload_img" />
                                    </div>
                                    <!--End of Sixth Row-->

                                    <!-- Modal footer -->
                                    <div class="button-submit-container">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop11536">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 11536-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop11536" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit11536">Yes</button>
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>



            </div>
        </main>
    </section>
    <script>
        $(document).ready(function () {
            $('.notification-item').on('click', function (e) {
                e.preventDefault();
                var activityId = $(this).data('activity-id');
                var notificationItem = $(this); // Store the clicked element

                $.ajax({
                    type: "POST",
                    url: "../../administrator/update_single_notification.php", // The URL to the PHP file
                    data: {
                        activityId: activityId
                    },
                    success: function (response) {
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
                    error: function (xhr, status, error) {
                        // Handle AJAX error
                        console.error("AJAX error:", status, error);
                    }
                });
            });
        });
    </script>
    <!--Start of JS Hover-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const assetImages = document.querySelectorAll('.asset-image');
            const hoverElement = document.getElementById('hover-asset');

            assetImages.forEach(image => {
                image.addEventListener('mouseenter', function () {
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

                image.addEventListener('mouseleave', function () {
                    // Hide hover element
                    hoverElement.style.display = 'none';
                });
            });
        });


    </script>


    <script>
        $(document).ready(function () {
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
        inputElements.forEach(function (inputElement) {
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
        document.addEventListener("DOMContentLoaded", function () {
            const legendItems = document.querySelectorAll('.legend-item button');
            let activeStatuses = []; // Keep track of active statuses

            legendItems.forEach(item => {
                item.addEventListener('click', function () {
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>