<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

session_start();
include_once("../../../config/connection.php");
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

    //FOR ID 5762 BULB
    $sql5762 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5762";
    $stmt5762 = $conn->prepare($sql5762);
    $stmt5762->execute();
    $result5762 = $stmt5762->get_result();
    $row5762 = $result5762->fetch_assoc();
    $assetId5762 = $row5762['assetId'];
    $category5762 = $row5762['category'];
    $date5762 = $row5762['date'];
    $building5762 = $row5762['building'];
    $floor5762 = $row5762['floor'];
    $room5762 = $row5762['room'];
    $status5762 = $row5762['status'];
    $assignedName5762 = $row5762['assignedName'];
    $assignedBy5762 = $row5762['assignedBy'];
    $upload_img5762 = $row5762['upload_img'];
    $description5762 = $row5762['description'];

    //FOR ID 5763 BULB
    $sql5763 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5763";
    $stmt5763 = $conn->prepare($sql5763);
    $stmt5763->execute();
    $result5763 = $stmt5763->get_result();
    $row5763 = $result5763->fetch_assoc();
    $assetId5763 = $row5763['assetId'];
    $category5763 = $row5763['category'];
    $date5763 = $row5763['date'];
    $building5763 = $row5763['building'];
    $floor5763 = $row5763['floor'];
    $room5763 = $row5763['room'];
    $status5763 = $row5763['status'];
    $assignedName5763 = $row5763['assignedName'];
    $assignedBy5763 = $row5763['assignedBy'];
    $upload_img5763 = $row5763['upload_img'];
    $description5763 = $row5763['description'];

    //FOR ID 5764 BULB
    $sql5764 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5764";
    $stmt5764 = $conn->prepare($sql5764);
    $stmt5764->execute();
    $result5764 = $stmt5764->get_result();
    $row5764 = $result5764->fetch_assoc();
    $assetId5764 = $row5764['assetId'];
    $category5764 = $row5764['category'];
    $date5764 = $row5764['date'];
    $building5764 = $row5764['building'];
    $floor5764 = $row5764['floor'];
    $room5764 = $row5764['room'];
    $status5764 = $row5764['status'];
    $assignedName5764 = $row5764['assignedName'];
    $assignedBy5764 = $row5764['assignedBy'];
    $upload_img5764 = $row5764['upload_img'];
    $description5764 = $row5764['description'];

    //FOR ID 5765 BULB
    $sql5765 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5765";
    $stmt5765 = $conn->prepare($sql5765);
    $stmt5765->execute();
    $result5765 = $stmt5765->get_result();
    $row5765 = $result5765->fetch_assoc();
    $assetId5765 = $row5765['assetId'];
    $category5765 = $row5765['category'];
    $date5765 = $row5765['date'];
    $building5765 = $row5765['building'];
    $floor5765 = $row5765['floor'];
    $room5765 = $row5765['room'];
    $status5765 = $row5765['status'];
    $assignedName5765 = $row5765['assignedName'];
    $assignedBy5765 = $row5765['assignedBy'];
    $upload_img5765 = $row5765['upload_img'];
    $description5765 = $row5765['description'];

    //FOR ID 5766 BULB
    $sql5766 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5766";
    $stmt5766 = $conn->prepare($sql5766);
    $stmt5766->execute();
    $result5766 = $stmt5766->get_result();
    $row5766 = $result5766->fetch_assoc();
    $assetId5766 = $row5766['assetId'];
    $category5766 = $row5766['category'];
    $date5766 = $row5766['date'];
    $building5766 = $row5766['building'];
    $floor5766 = $row5766['floor'];
    $room5766 = $row5766['room'];
    $status5766 = $row5766['status'];
    $assignedName5766 = $row5766['assignedName'];
    $assignedBy5766 = $row5766['assignedBy'];
    $upload_img5766 = $row5766['upload_img'];
    $description5766 = $row5766['description'];

    //FOR ID 5767 BULB
    $sql5767 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5767";
    $stmt5767 = $conn->prepare($sql5767);
    $stmt5767->execute();
    $result5767 = $stmt5767->get_result();
    $row5767 = $result5767->fetch_assoc();
    $assetId5767 = $row5767['assetId'];
    $category5767 = $row5767['category'];
    $date5767 = $row5767['date'];
    $building5767 = $row5767['building'];
    $floor5767 = $row5767['floor'];
    $room5767 = $row5767['room'];
    $status5767 = $row5767['status'];
    $assignedName5767 = $row5767['assignedName'];
    $assignedBy5767 = $row5767['assignedBy'];
    $upload_img5767 = $row5767['upload_img'];
    $description5767 = $row5767['description'];

    //FOR ID 5768 BULB
    $sql5768 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5768";
    $stmt5768 = $conn->prepare($sql5768);
    $stmt5768->execute();
    $result5768 = $stmt5768->get_result();
    $row5768 = $result5768->fetch_assoc();
    $assetId5768 = $row5768['assetId'];
    $category5768 = $row5768['category'];
    $date5768 = $row5768['date'];
    $building5768 = $row5768['building'];
    $floor5768 = $row5768['floor'];
    $room5768 = $row5768['room'];
    $status5768 = $row5768['status'];
    $assignedName5768 = $row5768['assignedName'];
    $assignedBy5768 = $row5768['assignedBy'];
    $upload_img5768 = $row5768['upload_img'];
    $description5768 = $row5768['description'];

    //FOR ID 5769 BULB
    $sql5769 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5769";
    $stmt5769 = $conn->prepare($sql5769);
    $stmt5769->execute();
    $result5769 = $stmt5769->get_result();
    $row5769 = $result5769->fetch_assoc();
    $assetId5769 = $row5769['assetId'];
    $category5769 = $row5769['category'];
    $date5769 = $row5769['date'];
    $building5769 = $row5769['building'];
    $floor5769 = $row5769['floor'];
    $room5769 = $row5769['room'];
    $status5769 = $row5769['status'];
    $assignedName5769 = $row5769['assignedName'];
    $assignedBy5769 = $row5769['assignedBy'];
    $upload_img5769 = $row5769['upload_img'];
    $description5769 = $row5769['description'];

    //FOR ID 5770 BULB
    $sql5770 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5770";
    $stmt5770 = $conn->prepare($sql5770);
    $stmt5770->execute();
    $result5770 = $stmt5770->get_result();
    $row5770 = $result5770->fetch_assoc();
    $assetId5770 = $row5770['assetId'];
    $category5770 = $row5770['category'];
    $date5770 = $row5770['date'];
    $building5770 = $row5770['building'];
    $floor5770 = $row5770['floor'];
    $room5770 = $row5770['room'];
    $status5770 = $row5770['status'];
    $assignedName5770 = $row5770['assignedName'];
    $assignedBy5770 = $row5770['assignedBy'];
    $upload_img5770 = $row5770['upload_img'];
    $description5770 = $row5770['description'];

    //FOR ID 5771 BULB
    $sql5771 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5771";
    $stmt5771 = $conn->prepare($sql5771);
    $stmt5771->execute();
    $result5771 = $stmt5771->get_result();
    $row5771 = $result5771->fetch_assoc();
    $assetId5771 = $row5771['assetId'];
    $category5771 = $row5771['category'];
    $date5771 = $row5771['date'];
    $building5771 = $row5771['building'];
    $floor5771 = $row5771['floor'];
    $room5771 = $row5771['room'];
    $status5771 = $row5771['status'];
    $assignedName5771 = $row5771['assignedName'];
    $assignedBy5771 = $row5771['assignedBy'];
    $upload_img5771 = $row5771['upload_img'];
    $description5771 = $row5771['description'];

    //FOR ID 5772 BULB
    $sql5772 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5772";
    $stmt5772 = $conn->prepare($sql5772);
    $stmt5772->execute();
    $result5772 = $stmt5772->get_result();
    $row5772 = $result5772->fetch_assoc();
    $assetId5772 = $row5772['assetId'];
    $category5772 = $row5772['category'];
    $date5772 = $row5772['date'];
    $building5772 = $row5772['building'];
    $floor5772 = $row5772['floor'];
    $room5772 = $row5772['room'];
    $status5772 = $row5772['status'];
    $assignedName5772 = $row5772['assignedName'];
    $assignedBy5772 = $row5772['assignedBy'];
    $upload_img5772 = $row5772['upload_img'];
    $description5772 = $row5772['description'];

    //FOR ID 5773 BULB
    $sql5773 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5773";
    $stmt5773 = $conn->prepare($sql5773);
    $stmt5773->execute();
    $result5773 = $stmt5773->get_result();
    $row5773 = $result5773->fetch_assoc();
    $assetId5773 = $row5773['assetId'];
    $category5773 = $row5773['category'];
    $date5773 = $row5773['date'];
    $building5773 = $row5773['building'];
    $floor5773 = $row5773['floor'];
    $room5773 = $row5773['room'];
    $status5773 = $row5773['status'];
    $assignedName5773 = $row5773['assignedName'];
    $assignedBy5773 = $row5773['assignedBy'];
    $upload_img5773 = $row5773['upload_img'];
    $description5773 = $row5773['description'];

    //FOR ID 5774 BULB
    $sql5774 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5774";
    $stmt5774 = $conn->prepare($sql5774);
    $stmt5774->execute();
    $result5774 = $stmt5774->get_result();
    $row5774 = $result5774->fetch_assoc();
    $assetId5774 = $row5774['assetId'];
    $category5774 = $row5774['category'];
    $date5774 = $row5774['date'];
    $building5774 = $row5774['building'];
    $floor5774 = $row5774['floor'];
    $room5774 = $row5774['room'];
    $status5774 = $row5774['status'];
    $assignedName5774 = $row5774['assignedName'];
    $assignedBy5774 = $row5774['assignedBy'];
    $upload_img5774 = $row5774['upload_img'];
    $description5774 = $row5774['description'];

    //FOR ID 5775 BULB
    $sql5775 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5775";
    $stmt5775 = $conn->prepare($sql5775);
    $stmt5775->execute();
    $result5775 = $stmt5775->get_result();
    $row5775 = $result5775->fetch_assoc();
    $assetId5775 = $row5775['assetId'];
    $category5775 = $row5775['category'];
    $date5775 = $row5775['date'];
    $building5775 = $row5775['building'];
    $floor5775 = $row5775['floor'];
    $room5775 = $row5775['room'];
    $status5775 = $row5775['status'];
    $assignedName5775 = $row5775['assignedName'];
    $assignedBy5775 = $row5775['assignedBy'];
    $upload_img5775 = $row5775['upload_img'];
    $description5775 = $row5775['description'];

    //FOR ID 5776 BULB
    $sql5776 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5776";
    $stmt5776 = $conn->prepare($sql5776);
    $stmt5776->execute();
    $result5776 = $stmt5776->get_result();
    $row5776 = $result5776->fetch_assoc();
    $assetId5776 = $row5776['assetId'];
    $category5776 = $row5776['category'];
    $date5776 = $row5776['date'];
    $building5776 = $row5776['building'];
    $floor5776 = $row5776['floor'];
    $room5776 = $row5776['room'];
    $status5776 = $row5776['status'];
    $assignedName5776 = $row5776['assignedName'];
    $assignedBy5776 = $row5776['assignedBy'];
    $upload_img5776 = $row5776['upload_img'];
    $description5776 = $row5776['description'];

    //FOR ID 5777 BULB
    $sql5777 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5777";
    $stmt5777 = $conn->prepare($sql5777);
    $stmt5777->execute();
    $result5777 = $stmt5777->get_result();
    $row5777 = $result5777->fetch_assoc();
    $assetId5777 = $row5777['assetId'];
    $category5777 = $row5777['category'];
    $date5777 = $row5777['date'];
    $building5777 = $row5777['building'];
    $floor5777 = $row5777['floor'];
    $room5777 = $row5777['room'];
    $status5777 = $row5777['status'];
    $assignedName5777 = $row5777['assignedName'];
    $assignedBy5777 = $row5777['assignedBy'];
    $upload_img5777 = $row5777['upload_img'];
    $description5777 = $row5777['description'];

    //FOR ID 5778 BULB
    $sql5778 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5778";
    $stmt5778 = $conn->prepare($sql5778);
    $stmt5778->execute();
    $result5778 = $stmt5778->get_result();
    $row5778 = $result5778->fetch_assoc();
    $assetId5778 = $row5778['assetId'];
    $category5778 = $row5778['category'];
    $date5778 = $row5778['date'];
    $building5778 = $row5778['building'];
    $floor5778 = $row5778['floor'];
    $room5778 = $row5778['room'];
    $status5778 = $row5778['status'];
    $assignedName5778 = $row5778['assignedName'];
    $assignedBy5778 = $row5778['assignedBy'];
    $upload_img5778 = $row5778['upload_img'];
    $description5778 = $row5778['description'];

    //FOR ID 5779 BULB
    $sql5779 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5779";
    $stmt5779 = $conn->prepare($sql5779);
    $stmt5779->execute();
    $result5779 = $stmt5779->get_result();
    $row5779 = $result5779->fetch_assoc();
    $assetId5779 = $row5779['assetId'];
    $category5779 = $row5779['category'];
    $date5779 = $row5779['date'];
    $building5779 = $row5779['building'];
    $floor5779 = $row5779['floor'];
    $room5779 = $row5779['room'];
    $status5779 = $row5779['status'];
    $assignedName5779 = $row5779['assignedName'];
    $assignedBy5779 = $row5779['assignedBy'];
    $upload_img5779 = $row5779['upload_img'];
    $description5779 = $row5779['description'];

    //FOR ID 5780 BULB
    $sql5780 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5780";
    $stmt5780 = $conn->prepare($sql5780);
    $stmt5780->execute();
    $result5780 = $stmt5780->get_result();
    $row5780 = $result5780->fetch_assoc();
    $assetId5780 = $row5780['assetId'];
    $category5780 = $row5780['category'];
    $date5780 = $row5780['date'];
    $building5780 = $row5780['building'];
    $floor5780 = $row5780['floor'];
    $room5780 = $row5780['room'];
    $status5780 = $row5780['status'];
    $assignedName5780 = $row5780['assignedName'];
    $assignedBy5780 = $row5780['assignedBy'];
    $upload_img5780 = $row5780['upload_img'];
    $description5780 = $row5780['description'];

    //FOR ID 5781 BULB
    $sql5781 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5781";
    $stmt5781 = $conn->prepare($sql5781);
    $stmt5781->execute();
    $result5781 = $stmt5781->get_result();
    $row5781 = $result5781->fetch_assoc();
    $assetId5781 = $row5781['assetId'];
    $category5781 = $row5781['category'];
    $date5781 = $row5781['date'];
    $building5781 = $row5781['building'];
    $floor5781 = $row5781['floor'];
    $room5781 = $row5781['room'];
    $status5781 = $row5781['status'];
    $assignedName5781 = $row5781['assignedName'];
    $assignedBy5781 = $row5781['assignedBy'];
    $upload_img5781 = $row5781['upload_img'];
    $description5781 = $row5781['description'];

    //FOR ID 5782 BULB
    $sql5782 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5782";
    $stmt5782 = $conn->prepare($sql5782);
    $stmt5782->execute();
    $result5782 = $stmt5782->get_result();
    $row5782 = $result5782->fetch_assoc();
    $assetId5782 = $row5782['assetId'];
    $category5782 = $row5782['category'];
    $date5782 = $row5782['date'];
    $building5782 = $row5782['building'];
    $floor5782 = $row5782['floor'];
    $room5782 = $row5782['room'];
    $status5782 = $row5782['status'];
    $assignedName5782 = $row5782['assignedName'];
    $assignedBy5782 = $row5782['assignedBy'];
    $upload_img5782 = $row5782['upload_img'];
    $description5782 = $row5782['description'];

    //FOR ID 5783 BULB
    $sql5783 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5783";
    $stmt5783 = $conn->prepare($sql5783);
    $stmt5783->execute();
    $result5783 = $stmt5783->get_result();
    $row5783 = $result5783->fetch_assoc();
    $assetId5783 = $row5783['assetId'];
    $category5783 = $row5783['category'];
    $date5783 = $row5783['date'];
    $building5783 = $row5783['building'];
    $floor5783 = $row5783['floor'];
    $room5783 = $row5783['room'];
    $status5783 = $row5783['status'];
    $assignedName5783 = $row5783['assignedName'];
    $assignedBy5783 = $row5783['assignedBy'];
    $upload_img5783 = $row5783['upload_img'];
    $description5783 = $row5783['description'];

    //FOR ID 5784 BULB
    $sql5784 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5784";
    $stmt5784 = $conn->prepare($sql5784);
    $stmt5784->execute();
    $result5784 = $stmt5784->get_result();
    $row5784 = $result5784->fetch_assoc();
    $assetId5784 = $row5784['assetId'];
    $category5784 = $row5784['category'];
    $date5784 = $row5784['date'];
    $building5784 = $row5784['building'];
    $floor5784 = $row5784['floor'];
    $room5784 = $row5784['room'];
    $status5784 = $row5784['status'];
    $assignedName5784 = $row5784['assignedName'];
    $assignedBy5784 = $row5784['assignedBy'];
    $upload_img5784 = $row5784['upload_img'];
    $description5784 = $row5784['description'];

    //FOR ID 5785 BULB
    $sql5785 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5785";
    $stmt5785 = $conn->prepare($sql5785);
    $stmt5785->execute();
    $result5785 = $stmt5785->get_result();
    $row5785 = $result5785->fetch_assoc();
    $assetId5785 = $row5785['assetId'];
    $category5785 = $row5785['category'];
    $date5785 = $row5785['date'];
    $building5785 = $row5785['building'];
    $floor5785 = $row5785['floor'];
    $room5785 = $row5785['room'];
    $status5785 = $row5785['status'];
    $assignedName5785 = $row5785['assignedName'];
    $assignedBy5785 = $row5785['assignedBy'];
    $upload_img5785 = $row5785['upload_img'];
    $description5785 = $row5785['description'];

    //FOR ID 5786 BULB
    $sql5786 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5786";
    $stmt5786 = $conn->prepare($sql5786);
    $stmt5786->execute();
    $result5786 = $stmt5786->get_result();
    $row5786 = $result5786->fetch_assoc();
    $assetId5786 = $row5786['assetId'];
    $category5786 = $row5786['category'];
    $date5786 = $row5786['date'];
    $building5786 = $row5786['building'];
    $floor5786 = $row5786['floor'];
    $room5786 = $row5786['room'];
    $status5786 = $row5786['status'];
    $assignedName5786 = $row5786['assignedName'];
    $assignedBy5786 = $row5786['assignedBy'];
    $upload_img5786 = $row5786['upload_img'];
    $description5786 = $row5786['description'];

    //FOR ID 5787 BULB
    $sql5787 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5787";
    $stmt5787 = $conn->prepare($sql5787);
    $stmt5787->execute();
    $result5787 = $stmt5787->get_result();
    $row5787 = $result5787->fetch_assoc();
    $assetId5787 = $row5787['assetId'];
    $category5787 = $row5787['category'];
    $date5787 = $row5787['date'];
    $building5787 = $row5787['building'];
    $floor5787 = $row5787['floor'];
    $room5787 = $row5787['room'];
    $status5787 = $row5787['status'];
    $assignedName5787 = $row5787['assignedName'];
    $assignedBy5787 = $row5787['assignedBy'];
    $upload_img5787 = $row5787['upload_img'];
    $description5787 = $row5787['description'];

    //FOR ID 5788 BULB
    $sql5788 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5788";
    $stmt5788 = $conn->prepare($sql5788);
    $stmt5788->execute();
    $result5788 = $stmt5788->get_result();
    $row5788 = $result5788->fetch_assoc();
    $assetId5788 = $row5788['assetId'];
    $category5788 = $row5788['category'];
    $date5788 = $row5788['date'];
    $building5788 = $row5788['building'];
    $floor5788 = $row5788['floor'];
    $room5788 = $row5788['room'];
    $status5788 = $row5788['status'];
    $assignedName5788 = $row5788['assignedName'];
    $assignedBy5788 = $row5788['assignedBy'];
    $upload_img5788 = $row5788['upload_img'];
    $description5788 = $row5788['description'];

    //FOR ID 5789 BULB
    $sql5789 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5789";
    $stmt5789 = $conn->prepare($sql5789);
    $stmt5789->execute();
    $result5789 = $stmt5789->get_result();
    $row5789 = $result5789->fetch_assoc();
    $assetId5789 = $row5789['assetId'];
    $category5789 = $row5789['category'];
    $date5789 = $row5789['date'];
    $building5789 = $row5789['building'];
    $floor5789 = $row5789['floor'];
    $room5789 = $row5789['room'];
    $status5789 = $row5789['status'];
    $assignedName5789 = $row5789['assignedName'];
    $assignedBy5789 = $row5789['assignedBy'];
    $upload_img5789 = $row5789['upload_img'];
    $description5789 = $row5789['description'];

    //FOR ID 5790 BULB
    $sql5790 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5790";
    $stmt5790 = $conn->prepare($sql5790);
    $stmt5790->execute();
    $result5790 = $stmt5790->get_result();
    $row5790 = $result5790->fetch_assoc();
    $assetId5790 = $row5790['assetId'];
    $category5790 = $row5790['category'];
    $date5790 = $row5790['date'];
    $building5790 = $row5790['building'];
    $floor5790 = $row5790['floor'];
    $room5790 = $row5790['room'];
    $status5790 = $row5790['status'];
    $assignedName5790 = $row5790['assignedName'];
    $assignedBy5790 = $row5790['assignedBy'];
    $upload_img5790 = $row5790['upload_img'];
    $description5790 = $row5790['description'];

    //FOR ID 5791 BULB
    $sql5791 = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date,upload_img, description FROM asset WHERE assetId = 5791";
    $stmt5791 = $conn->prepare($sql5791);
    $stmt5791->execute();
    $result5791 = $stmt5791->get_result();
    $row5791 = $result5791->fetch_assoc();
    $assetId5791 = $row5791['assetId'];
    $category5791 = $row5791['category'];
    $date5791 = $row5791['date'];
    $building5791 = $row5791['building'];
    $floor5791 = $row5791['floor'];
    $room5791 = $row5791['room'];
    $status5791 = $row5791['status'];
    $assignedName5791 = $row5791['assignedName'];
    $assignedBy5791 = $row5791['assignedBy'];
    $upload_img5791 = $row5791['upload_img'];
    $description5791 = $row5791['description'];






    //FOR ID 5762
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5762'])) {
        // Get form data
        $assetId5762 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5762 = $_POST['status']; // Get the status from the form
        $description5762 = $_POST['description']; // Get the description from the form
        $room5762 = $_POST['room']; // Get the room from the form
        $assignedBy5762 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5762 = $status5762 === 'Need Repair' ? '' : $assignedName5762;

        // Prepare SQL query to update the asset
        $sql5762 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5762 = $conn->prepare($sql5762);
        $stmt5762->bind_param('sssssi', $status5762, $assignedName5762, $assignedBy5762, $description5762, $room5762, $assetId5762);

        if ($stmt5762->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5762 to $status5762.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5762->close();
    }

    //FOR ID 5763
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5763'])) {
        // Get form data
        $assetId5763 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5763 = $_POST['status']; // Get the status from the form
        $description5763 = $_POST['description']; // Get the description from the form
        $room5763 = $_POST['room']; // Get the room from the form
        $assignedBy5763 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5763 = $status5763 === 'Need Repair' ? '' : $assignedName5763;

        // Prepare SQL query to update the asset
        $sql5763 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5763 = $conn->prepare($sql5763);
        $stmt5763->bind_param('sssssi', $status5763, $assignedName5763, $assignedBy5763, $description5763, $room5763, $assetId5763);

        if ($stmt5763->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5763 to $status5763.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5763->close();
    }

    //FOR ID 5764
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5764'])) {
        // Get form data
        $assetId5764 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5764 = $_POST['status']; // Get the status from the form
        $description5764 = $_POST['description']; // Get the description from the form
        $room5764 = $_POST['room']; // Get the room from the form
        $assignedBy5764 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5764 = $status5764 === 'Need Repair' ? '' : $assignedName5764;

        // Prepare SQL query to update the asset
        $sql5764 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5764 = $conn->prepare($sql5764);
        $stmt5764->bind_param('sssssi', $status5764, $assignedName5764, $assignedBy5764, $description5764, $room5764, $assetId5764);

        if ($stmt5764->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5764 to $status5764.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5764->close();
    }

    //FOR ID 5765
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5765'])) {
        // Get form data
        $assetId5765 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5765 = $_POST['status']; // Get the status from the form
        $description5765 = $_POST['description']; // Get the description from the form
        $room5765 = $_POST['room']; // Get the room from the form
        $assignedBy5765 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5765 = $status5765 === 'Need Repair' ? '' : $assignedName5765;

        // Prepare SQL query to update the asset
        $sql5765 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5765 = $conn->prepare($sql5765);
        $stmt5765->bind_param('sssssi', $status5765, $assignedName5765, $assignedBy5765, $description5765, $room5765, $assetId5765);

        if ($stmt5765->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5765 to $status5765.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5765->close();
    }

    //FOR ID 5766
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5766'])) {
        // Get form data
        $assetId5766 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5766 = $_POST['status']; // Get the status from the form
        $description5766 = $_POST['description']; // Get the description from the form
        $room5766 = $_POST['room']; // Get the room from the form
        $assignedBy5766 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5766 = $status5766 === 'Need Repair' ? '' : $assignedName5766;

        // Prepare SQL query to update the asset
        $sql5766 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5766 = $conn->prepare($sql5766);
        $stmt5766->bind_param('sssssi', $status5766, $assignedName5766, $assignedBy5766, $description5766, $room5766, $assetId5766);

        if ($stmt5766->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5766 to $status5766.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5766->close();
    }

    //FOR ID 5767
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5767'])) {
        // Get form data
        $assetId5767 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5767 = $_POST['status']; // Get the status from the form
        $description5767 = $_POST['description']; // Get the description from the form
        $room5767 = $_POST['room']; // Get the room from the form
        $assignedBy5767 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5767 = $status5767 === 'Need Repair' ? '' : $assignedName5767;

        // Prepare SQL query to update the asset
        $sql5767 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5767 = $conn->prepare($sql5767);
        $stmt5767->bind_param('sssssi', $status5767, $assignedName5767, $assignedBy5767, $description5767, $room5767, $assetId5767);

        if ($stmt5767->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5767 to $status5767.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5767->close();
    }

    //FOR ID 5768
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5768'])) {
        // Get form data
        $assetId5768 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5768 = $_POST['status']; // Get the status from the form
        $description5768 = $_POST['description']; // Get the description from the form
        $room5768 = $_POST['room']; // Get the room from the form
        $assignedBy5768 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5768 = $status5768 === 'Need Repair' ? '' : $assignedName5768;

        // Prepare SQL query to update the asset
        $sql5768 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5768 = $conn->prepare($sql5768);
        $stmt5768->bind_param('sssssi', $status5768, $assignedName5768, $assignedBy5768, $description5768, $room5768, $assetId5768);

        if ($stmt5768->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5768 to $status5768.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5768->close();
    }

    //FOR ID 5769
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5769'])) {
        // Get form data
        $assetId5769 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5769 = $_POST['status']; // Get the status from the form
        $description5769 = $_POST['description']; // Get the description from the form
        $room5769 = $_POST['room']; // Get the room from the form
        $assignedBy5769 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5769 = $status5769 === 'Need Repair' ? '' : $assignedName5769;

        // Prepare SQL query to update the asset
        $sql5769 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5769 = $conn->prepare($sql5769);
        $stmt5769->bind_param('sssssi', $status5769, $assignedName5769, $assignedBy5769, $description5769, $room5769, $assetId5769);

        if ($stmt5769->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5769 to $status5769.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5769->close();
    }

    //FOR ID 5770
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5770'])) {
        // Get form data
        $assetId5770 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5770 = $_POST['status']; // Get the status from the form
        $description5770 = $_POST['description']; // Get the description from the form
        $room5770 = $_POST['room']; // Get the room from the form
        $assignedBy5770 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5770 = $status5770 === 'Need Repair' ? '' : $assignedName5770;

        // Prepare SQL query to update the asset
        $sql5770 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5770 = $conn->prepare($sql5770);
        $stmt5770->bind_param('sssssi', $status5770, $assignedName5770, $assignedBy5770, $description5770, $room5770, $assetId5770);

        if ($stmt5770->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5770 to $status5770.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5770->close();
    }

    //FOR ID 5771
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5771'])) {
        // Get form data
        $assetId5771 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5771 = $_POST['status']; // Get the status from the form
        $description5771 = $_POST['description']; // Get the description from the form
        $room5771 = $_POST['room']; // Get the room from the form
        $assignedBy5771 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5771 = $status5771 === 'Need Repair' ? '' : $assignedName5771;

        // Prepare SQL query to update the asset
        $sql5771 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5771 = $conn->prepare($sql5771);
        $stmt5771->bind_param('sssssi', $status5771, $assignedName5771, $assignedBy5771, $description5771, $room5771, $assetId5771);

        if ($stmt5771->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5771 to $status5771.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5771->close();
    }

    //FOR ID 5772
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5772'])) {
        // Get form data
        $assetId5772 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5772 = $_POST['status']; // Get the status from the form
        $description5772 = $_POST['description']; // Get the description from the form
        $room5772 = $_POST['room']; // Get the room from the form
        $assignedBy5772 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5772 = $status5772 === 'Need Repair' ? '' : $assignedName5772;

        // Prepare SQL query to update the asset
        $sql5772 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5772 = $conn->prepare($sql5772);
        $stmt5772->bind_param('sssssi', $status5772, $assignedName5772, $assignedBy5772, $description5772, $room5772, $assetId5772);

        if ($stmt5772->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5772 to $status5772.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5772->close();
    }

    //FOR ID 5773
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5773'])) {
        // Get form data
        $assetId5773 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5773 = $_POST['status']; // Get the status from the form
        $description5773 = $_POST['description']; // Get the description from the form
        $room5773 = $_POST['room']; // Get the room from the form
        $assignedBy5773 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5773 = $status5773 === 'Need Repair' ? '' : $assignedName5773;

        // Prepare SQL query to update the asset
        $sql5773 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5773 = $conn->prepare($sql5773);
        $stmt5773->bind_param('sssssi', $status5773, $assignedName5773, $assignedBy5773, $description5773, $room5773, $assetId5773);

        if ($stmt5773->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5773 to $status5773.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5773->close();
    }

    //FOR ID 5774
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5774'])) {
        // Get form data
        $assetId5774 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5774 = $_POST['status']; // Get the status from the form
        $description5774 = $_POST['description']; // Get the description from the form
        $room5774 = $_POST['room']; // Get the room from the form
        $assignedBy5774 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5774 = $status5774 === 'Need Repair' ? '' : $assignedName5774;

        // Prepare SQL query to update the asset
        $sql5774 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5774 = $conn->prepare($sql5774);
        $stmt5774->bind_param('sssssi', $status5774, $assignedName5774, $assignedBy5774, $description5774, $room5774, $assetId5774);

        if ($stmt5774->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5774 to $status5774.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5774->close();
    }

    //FOR ID 5775
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5775'])) {
        // Get form data
        $assetId5775 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5775 = $_POST['status']; // Get the status from the form
        $description5775 = $_POST['description']; // Get the description from the form
        $room5775 = $_POST['room']; // Get the room from the form
        $assignedBy5775 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5775 = $status5775 === 'Need Repair' ? '' : $assignedName5775;

        // Prepare SQL query to update the asset
        $sql5775 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5775 = $conn->prepare($sql5775);
        $stmt5775->bind_param('sssssi', $status5775, $assignedName5775, $assignedBy5775, $description5775, $room5775, $assetId5775);

        if ($stmt5775->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5775 to $status5775.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5775->close();
    }

    //FOR ID 5776
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5776'])) {
        // Get form data
        $assetId5776 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5776 = $_POST['status']; // Get the status from the form
        $description5776 = $_POST['description']; // Get the description from the form
        $room5776 = $_POST['room']; // Get the room from the form
        $assignedBy5776 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5776 = $status5776 === 'Need Repair' ? '' : $assignedName5776;

        // Prepare SQL query to update the asset
        $sql5776 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5776 = $conn->prepare($sql5776);
        $stmt5776->bind_param('sssssi', $status5776, $assignedName5776, $assignedBy5776, $description5776, $room5776, $assetId5776);

        if ($stmt5776->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5776 to $status5776.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5776->close();
    }

    //FOR ID 5777
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5777'])) {
        // Get form data
        $assetId5777 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5777 = $_POST['status']; // Get the status from the form
        $description5777 = $_POST['description']; // Get the description from the form
        $room5777 = $_POST['room']; // Get the room from the form
        $assignedBy5777 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5777 = $status5777 === 'Need Repair' ? '' : $assignedName5777;

        // Prepare SQL query to update the asset
        $sql5777 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5777 = $conn->prepare($sql5777);
        $stmt5777->bind_param('sssssi', $status5777, $assignedName5777, $assignedBy5777, $description5777, $room5777, $assetId5777);

        if ($stmt5777->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5777 to $status5777.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5777->close();
    }

    //FOR ID 5778
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5778'])) {
        // Get form data
        $assetId5778 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5778 = $_POST['status']; // Get the status from the form
        $description5778 = $_POST['description']; // Get the description from the form
        $room5778 = $_POST['room']; // Get the room from the form
        $assignedBy5778 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5778 = $status5778 === 'Need Repair' ? '' : $assignedName5778;

        // Prepare SQL query to update the asset
        $sql5778 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5778 = $conn->prepare($sql5778);
        $stmt5778->bind_param('sssssi', $status5778, $assignedName5778, $assignedBy5778, $description5778, $room5778, $assetId5778);

        if ($stmt5778->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5778 to $status5778.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5778->close();
    }

    //FOR ID 5779
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5779'])) {
        // Get form data
        $assetId5779 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5779 = $_POST['status']; // Get the status from the form
        $description5779 = $_POST['description']; // Get the description from the form
        $room5779 = $_POST['room']; // Get the room from the form
        $assignedBy5779 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5779 = $status5779 === 'Need Repair' ? '' : $assignedName5779;

        // Prepare SQL query to update the asset
        $sql5779 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5779 = $conn->prepare($sql5779);
        $stmt5779->bind_param('sssssi', $status5779, $assignedName5779, $assignedBy5779, $description5779, $room5779, $assetId5779);

        if ($stmt5779->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5779 to $status5779.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5779->close();
    }

    //FOR ID 5780
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5780'])) {
        // Get form data
        $assetId5780 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5780 = $_POST['status']; // Get the status from the form
        $description5780 = $_POST['description']; // Get the description from the form
        $room5780 = $_POST['room']; // Get the room from the form
        $assignedBy5780 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5780 = $status5780 === 'Need Repair' ? '' : $assignedName5780;

        // Prepare SQL query to update the asset
        $sql5780 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5780 = $conn->prepare($sql5780);
        $stmt5780->bind_param('sssssi', $status5780, $assignedName5780, $assignedBy5780, $description5780, $room5780, $assetId5780);

        if ($stmt5780->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5780 to $status5780.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5780->close();
    }

    //FOR ID 5781
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5781'])) {
        // Get form data
        $assetId5781 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5781 = $_POST['status']; // Get the status from the form
        $description5781 = $_POST['description']; // Get the description from the form
        $room5781 = $_POST['room']; // Get the room from the form
        $assignedBy5781 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5781 = $status5781 === 'Need Repair' ? '' : $assignedName5781;

        // Prepare SQL query to update the asset
        $sql5781 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5781 = $conn->prepare($sql5781);
        $stmt5781->bind_param('sssssi', $status5781, $assignedName5781, $assignedBy5781, $description5781, $room5781, $assetId5781);

        if ($stmt5781->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5781 to $status5781.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5781->close();
    }

    //FOR ID 5782
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5782'])) {
        // Get form data
        $assetId5782 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5782 = $_POST['status']; // Get the status from the form
        $description5782 = $_POST['description']; // Get the description from the form
        $room5782 = $_POST['room']; // Get the room from the form
        $assignedBy5782 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5782 = $status5782 === 'Need Repair' ? '' : $assignedName5782;

        // Prepare SQL query to update the asset
        $sql5782 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5782 = $conn->prepare($sql5782);
        $stmt5782->bind_param('sssssi', $status5782, $assignedName5782, $assignedBy5782, $description5782, $room5782, $assetId5782);

        if ($stmt5782->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5782 to $status5782.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5782->close();
    }

    //FOR ID 5783
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5783'])) {
        // Get form data
        $assetId5783 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5783 = $_POST['status']; // Get the status from the form
        $description5783 = $_POST['description']; // Get the description from the form
        $room5783 = $_POST['room']; // Get the room from the form
        $assignedBy5783 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5783 = $status5783 === 'Need Repair' ? '' : $assignedName5783;

        // Prepare SQL query to update the asset
        $sql5783 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5783 = $conn->prepare($sql5783);
        $stmt5783->bind_param('sssssi', $status5783, $assignedName5783, $assignedBy5783, $description5783, $room5783, $assetId5783);

        if ($stmt5783->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5783 to $status5783.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5783->close();
    }

    //FOR ID 5784
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5784'])) {
        // Get form data
        $assetId5784 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5784 = $_POST['status']; // Get the status from the form
        $description5784 = $_POST['description']; // Get the description from the form
        $room5784 = $_POST['room']; // Get the room from the form
        $assignedBy5784 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5784 = $status5784 === 'Need Repair' ? '' : $assignedName5784;

        // Prepare SQL query to update the asset
        $sql5784 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5784 = $conn->prepare($sql5784);
        $stmt5784->bind_param('sssssi', $status5784, $assignedName5784, $assignedBy5784, $description5784, $room5784, $assetId5784);

        if ($stmt5784->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5784 to $status5784.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5784->close();
    }

    //FOR ID 5785
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5785'])) {
        // Get form data
        $assetId5785 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5785 = $_POST['status']; // Get the status from the form
        $description5785 = $_POST['description']; // Get the description from the form
        $room5785 = $_POST['room']; // Get the room from the form
        $assignedBy5785 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5785 = $status5785 === 'Need Repair' ? '' : $assignedName5785;

        // Prepare SQL query to update the asset
        $sql5785 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5785 = $conn->prepare($sql5785);
        $stmt5785->bind_param('sssssi', $status5785, $assignedName5785, $assignedBy5785, $description5785, $room5785, $assetId5785);

        if ($stmt5785->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5785 to $status5785.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5785->close();
    }

    //FOR ID 5786
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5786'])) {
        // Get form data
        $assetId5786 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5786 = $_POST['status']; // Get the status from the form
        $description5786 = $_POST['description']; // Get the description from the form
        $room5786 = $_POST['room']; // Get the room from the form
        $assignedBy5786 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5786 = $status5786 === 'Need Repair' ? '' : $assignedName5786;

        // Prepare SQL query to update the asset
        $sql5786 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5786 = $conn->prepare($sql5786);
        $stmt5786->bind_param('sssssi', $status5786, $assignedName5786, $assignedBy5786, $description5786, $room5786, $assetId5786);

        if ($stmt5786->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5786 to $status5786.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5786->close();
    }

    //FOR ID 5787
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5787'])) {
        // Get form data
        $assetId5787 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5787 = $_POST['status']; // Get the status from the form
        $description5787 = $_POST['description']; // Get the description from the form
        $room5787 = $_POST['room']; // Get the room from the form
        $assignedBy5787 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5787 = $status5787 === 'Need Repair' ? '' : $assignedName5787;

        // Prepare SQL query to update the asset
        $sql5787 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5787 = $conn->prepare($sql5787);
        $stmt5787->bind_param('sssssi', $status5787, $assignedName5787, $assignedBy5787, $description5787, $room5787, $assetId5787);

        if ($stmt5787->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5787 to $status5787.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5787->close();
    }

    //FOR ID 5788
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5788'])) {
        // Get form data
        $assetId5788 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5788 = $_POST['status']; // Get the status from the form
        $description5788 = $_POST['description']; // Get the description from the form
        $room5788 = $_POST['room']; // Get the room from the form
        $assignedBy5788 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5788 = $status5788 === 'Need Repair' ? '' : $assignedName5788;

        // Prepare SQL query to update the asset
        $sql5788 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5788 = $conn->prepare($sql5788);
        $stmt5788->bind_param('sssssi', $status5788, $assignedName5788, $assignedBy5788, $description5788, $room5788, $assetId5788);

        if ($stmt5788->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5788 to $status5788.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5788->close();
    }

    //FOR ID 5789
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5789'])) {
        // Get form data
        $assetId5789 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5789 = $_POST['status']; // Get the status from the form
        $description5789 = $_POST['description']; // Get the description from the form
        $room5789 = $_POST['room']; // Get the room from the form
        $assignedBy5789 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5789 = $status5789 === 'Need Repair' ? '' : $assignedName5789;

        // Prepare SQL query to update the asset
        $sql5789 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5789 = $conn->prepare($sql5789);
        $stmt5789->bind_param('sssssi', $status5789, $assignedName5789, $assignedBy5789, $description5789, $room5789, $assetId5789);

        if ($stmt5789->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5789 to $status5789.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5789->close();
    }

    //FOR ID 5790
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5790'])) {
        // Get form data
        $assetId5790 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5790 = $_POST['status']; // Get the status from the form
        $description5790 = $_POST['description']; // Get the description from the form
        $room5790 = $_POST['room']; // Get the room from the form
        $assignedBy5790 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5790 = $status5790 === 'Need Repair' ? '' : $assignedName5790;

        // Prepare SQL query to update the asset
        $sql5790 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5790 = $conn->prepare($sql5790);
        $stmt5790->bind_param('sssssi', $status5790, $assignedName5790, $assignedBy5790, $description5790, $room5790, $assetId5790);

        if ($stmt5790->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5790 to $status5790.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5790->close();
    }

    //FOR ID 5791
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit5791'])) {
        // Get form data
        $assetId5791 = $_POST['assetId']; // Assuming assetId is passed as a hidden input in your form
        $status5791 = $_POST['status']; // Get the status from the form
        $description5791 = $_POST['description']; // Get the description from the form
        $room5791 = $_POST['room']; // Get the room from the form
        $assignedBy5791 = $_POST['assignedBy'];
        // Check if status is "Need Repair" and set "Assigned Name" to none
        $assignedName5791 = $status5791 === 'Need Repair' ? '' : $assignedName5791;

        // Prepare SQL query to update the asset
        $sql5791 = "UPDATE asset SET status = ?, assignedName = ?, assignedBy = ?, description = ?, room = ?, date = NOW() WHERE assetId = ?";
        $stmt5791 = $conn->prepare($sql5791);
        $stmt5791->bind_param('sssssi', $status5791, $assignedName5791, $assignedBy5791, $description5791, $room5791, $assetId5791);

        if ($stmt5791->execute()) {
            // Update success
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId5791 to $status5791.", 'Report');
            echo "<script>alert('Asset updated successfully!');</script>";
            header("Location: ADBF1.php");
        } else {
            // Update failed
            echo "<script>alert('Failed to update asset.');</script>";
        }
        $stmt5791->close();
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
                header("Location: ADBF1.php");
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                        <!--NOTIF NI PABS-->
                        <a href="#" class="notification" id="notification-button">
                            <i class="fa fa-bell" aria-hidden="true"></i>
                            <!-- Notification Indicator Dot -->
                            <?php if ($unseenCount > 0) : ?>
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
                    <!--END NG  NOTIF NI PABS-->

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
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><i class="bi bi-person profile-icons"></i>Profile</a>
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
                        <img src="../../../src/floors/adminB/AB3F.png" alt="" class="Floor-container">

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
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
                    </div>

                    <!-- ASSET 5762 -->
                    <img src='../image.php?id=5762' style='width:25px; cursor:pointer; position:absolute; top:60px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5762' onclick='fetchAssetData(5762);' class="asset-image" data-id="<?php echo $assetId5762; ?>" data-room="<?php echo htmlspecialchars($room5762); ?>" data-floor="<?php echo htmlspecialchars($floor5762); ?>" data-image="<?php echo base64_encode($upload_img5762); ?>" data-status="<?php echo htmlspecialchars($status5762); ?>" data-category="<?php echo htmlspecialchars($category5762); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5762); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5762); ?>; 
    position:absolute; top:60px; left:875px;'>
                    </div>

                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5762); ?>; 
                        position:absolute; top:60px; left:875px;'>
                    </div>

                    <!-- ASSET 5763 -->
                    <img src='../image.php?id=5763' style='width:25px; cursor:pointer; position:absolute; top:155px; left:110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5763' onclick='fetchAssetData(5763);' class="asset-image" data-id="<?php echo $assetId5763; ?>" data-room="<?php echo htmlspecialchars($room5763); ?>" data-floor="<?php echo htmlspecialchars($floor5763); ?>" data-image="<?php echo base64_encode($upload_img5763); ?>" data-status="<?php echo htmlspecialchars($status5763); ?>" data-category="<?php echo htmlspecialchars($category5763); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5763); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5763); ?>; 
    position:absolute; top:155px; left:135px;'>
                    </div>

                    <!-- ASSET 5764 -->
                    <img src='../image.php?id=5764' style='width:25px; cursor:pointer; position:absolute; top:155px; left:280px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5764' onclick='fetchAssetData(5764);' class="asset-image" data-id="<?php echo $assetId5764; ?>" data-room="<?php echo htmlspecialchars($room5764); ?>" data-floor="<?php echo htmlspecialchars($floor5764); ?>" data-image="<?php echo base64_encode($upload_img5764); ?>" data-category="<?php echo htmlspecialchars($category5764); ?>" data-status="<?php echo htmlspecialchars($status5764); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5764); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5764); ?>; 
    position:absolute; top:155px; left:305px;'>
                    </div>

                    <!-- ASSET 5765 -->
                    <img src='../image.php?id=5765' style='width:25px; cursor:pointer; position:absolute; top:155px; left:440px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5765' onclick='fetchAssetData(5765);' class="asset-image" data-id="<?php echo $assetId5765; ?>" data-room="<?php echo htmlspecialchars($room5765); ?>" data-floor="<?php echo htmlspecialchars($floor5765); ?>" data-image="<?php echo base64_encode($upload_img5765); ?>" data-category="<?php echo htmlspecialchars($category5765); ?>" data-status="<?php echo htmlspecialchars($status5765); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5765); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5765); ?>; 
    position:absolute; top:155px; left:465px;'>
                    </div>

                    <!-- ASSET 5766 -->
                    <img src='../image.php?id=5766' style='width:25px; cursor:pointer; position:absolute; top:155px; left:780px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5766' onclick='fetchAssetData(5766);' class="asset-image" data-id="<?php echo $assetId5766; ?>" data-room="<?php echo htmlspecialchars($room5766); ?>" data-floor="<?php echo htmlspecialchars($floor5766); ?>" data-image="<?php echo base64_encode($upload_img5766); ?>" data-category="<?php echo htmlspecialchars($category5766); ?>" data-status="<?php echo htmlspecialchars($status5766); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5766); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5766); ?>; 
    position:absolute; top:155px; left:805px;'>
                    </div>

                    <!-- ASSET 5767 -->
                    <img src='../image.php?id=5767' style='width:25px; cursor:pointer; position:absolute; top:155px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5767' onclick='fetchAssetData(5767);' class="asset-image" data-id="<?php echo $assetId5767; ?>" data-room="<?php echo htmlspecialchars($room5767); ?>" data-floor="<?php echo htmlspecialchars($floor5767); ?>" data-image="<?php echo base64_encode($upload_img5767); ?>" data-category="<?php echo htmlspecialchars($category5767); ?>" data-status="<?php echo htmlspecialchars($status5767); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5767); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5767); ?>; 
    position:absolute; top:155px; left:945px;'>
                    </div>

                    <!-- ASSET 5768 -->
                    <img src='../image.php?id=5768' style='width:25px; cursor:pointer; position:absolute; top:155px; left:1050px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5768' onclick='fetchAssetData(5768);' class="asset-image" data-id="<?php echo $assetId5768; ?>" data-room="<?php echo htmlspecialchars($room5768); ?>" data-floor="<?php echo htmlspecialchars($floor5768); ?>" data-image="<?php echo base64_encode($upload_img5768); ?>" data-category="<?php echo htmlspecialchars($category5768); ?>" data-status="<?php echo htmlspecialchars($status5768); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5768); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5768); ?>; 
    position:absolute; top:155px; left:1075px;'>
                    </div>

                    <!-- ASSET 5769 -->
                    <img src='../image.php?id=5769' style='width:25px; cursor:pointer; position:absolute; top:255px; left:110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5769' onclick='fetchAssetData(5769);' class="asset-image" data-id="<?php echo $assetId5769; ?>" data-room="<?php echo htmlspecialchars($room5769); ?>" data-floor="<?php echo htmlspecialchars($floor5769); ?>" data-image="<?php echo base64_encode($upload_img5769); ?>" data-category="<?php echo htmlspecialchars($category5769); ?>" data-status="<?php echo htmlspecialchars($status5769); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5769); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5769); ?>; 
    position:absolute; top:255px; left:135px;'>
                    </div>

                    <!-- ASSET 5770 -->
                    <img src='../image.php?id=5770' style='width:25px; cursor:pointer; position:absolute; top:255px; left:280px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5770' onclick='fetchAssetData(5770);' class="asset-image" data-id="<?php echo $assetId5770; ?>" data-room="<?php echo htmlspecialchars($room5770); ?>" data-floor="<?php echo htmlspecialchars($floor5770); ?>" data-image="<?php echo base64_encode($upload_img5770); ?>" data-category="<?php echo htmlspecialchars($category5770); ?>" data-status="<?php echo htmlspecialchars($status5770); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5770); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5770); ?>; 
    position:absolute; top:255px; left:305px;'>
                    </div>

                    <!-- ASSET 5771 -->
                    <img src='../image.php?id=5771' style='width:25px; cursor:pointer; position:absolute; top:255px; left:440px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5771' onclick='fetchAssetData(5771);' class="asset-image" data-id="<?php echo $assetId5771; ?>" data-room="<?php echo htmlspecialchars($room5771); ?>" data-floor="<?php echo htmlspecialchars($floor5771); ?>" data-image="<?php echo base64_encode($upload_img5771); ?>" data-category="<?php echo htmlspecialchars($category5771); ?>" data-status="<?php echo htmlspecialchars($status5771); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5771); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5771); ?>; 
    position:absolute; top:255px; left:465px;'>
                    </div>

                    <!-- ASSET 5772 -->
                    <img src='../image.php?id=5772' style='width:25px; cursor:pointer; position:absolute; top:255px; left:600px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5772' onclick='fetchAssetData(5772);' class="asset-image" data-id="<?php echo $assetId5772; ?>" data-room="<?php echo htmlspecialchars($room5772); ?>" data-floor="<?php echo htmlspecialchars($floor5772); ?>" data-image="<?php echo base64_encode($upload_img5772); ?>" data-status="<?php echo htmlspecialchars($status5772); ?>" data-category="<?php echo htmlspecialchars($category5772); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5772); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5772); ?>; 
    position:absolute; top:255px; left:625px;'>
                    </div>

                    <!-- ASSET 5773 -->
                    <img src='../image.php?id=5773' style='width:25px; cursor:pointer; position:absolute; top:255px; left:780px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5773' onclick='fetchAssetData(5773);' class="asset-image" data-id="<?php echo $assetId5773; ?>" data-room="<?php echo htmlspecialchars($room5773); ?>" data-floor="<?php echo htmlspecialchars($floor5773); ?>" data-image="<?php echo base64_encode($upload_img5773); ?>" data-category="<?php echo htmlspecialchars($category5773); ?>" data-status="<?php echo htmlspecialchars($status5773); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5773); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5773); ?>; 
    position:absolute; top:255px; left:805px;'>
                    </div>

                    <!-- ASSET 5774 -->
                    <img src='../image.php?id=5774' style='width:25px; cursor:pointer; position:absolute; top:255px; left:935px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5774' onclick='fetchAssetData(5774);' class="asset-image" data-id="<?php echo $assetId5774; ?>" data-room="<?php echo htmlspecialchars($room5774); ?>" data-floor="<?php echo htmlspecialchars($floor5774); ?>" data-image="<?php echo base64_encode($upload_img5774); ?>" data-category="<?php echo htmlspecialchars($category5774); ?>" data-status="<?php echo htmlspecialchars($status5774); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5774); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5774); ?>; 
    position:absolute; top:255px; left:960px;'>
                    </div>

                    <!-- ASSET 5775 -->
                    <img src='../image.php?id=5775' style='width:25px; cursor:pointer; position:absolute; top:255px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5775' onclick='fetchAssetData(5775);' class="asset-image" data-id="<?php echo $assetId5775; ?>" data-room="<?php echo htmlspecialchars($room5775); ?>" data-floor="<?php echo htmlspecialchars($floor5775); ?>" data-image="<?php echo base64_encode($upload_img5775); ?>" data-category="<?php echo htmlspecialchars($category5775); ?>" data-status="<?php echo htmlspecialchars($status5775); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5775); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5775); ?>; 
    position:absolute; top:255px; left:1105px;'>
                    </div>


                    <!-- ASSET 5776 -->
                    <img src='../image.php?id=5776' style='width:25px; cursor:pointer; position:absolute; top:335px; left:110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5776' onclick='fetchAssetData(5776);' class="asset-image" data-id="<?php echo $assetId5776; ?>" data-room="<?php echo htmlspecialchars($room5776); ?>" data-floor="<?php echo htmlspecialchars($floor5776); ?>" data-image="<?php echo base64_encode($upload_img5776); ?>" data-category="<?php echo htmlspecialchars($category5776); ?>" data-status="<?php echo htmlspecialchars($status5776); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5776); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5776); ?>; 
    position:absolute; top:335px; left:135px;'>
                    </div>

                    <!-- ASSET 5777 -->
                    <img src='../image.php?id=5777' style='width:25px; cursor:pointer; position:absolute; top:335px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5777' onclick='fetchAssetData(5777);' class="asset-image" data-id="<?php echo $assetId5777; ?>" data-room="<?php echo htmlspecialchars($room5777); ?>" data-floor="<?php echo htmlspecialchars($floor5777); ?>" data-image="<?php echo base64_encode($upload_img5777); ?>" data-status="<?php echo htmlspecialchars($status5777); ?>" data-category="<?php echo htmlspecialchars($category5777); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5777); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5777); ?>; 
    position:absolute; top:335px; left:245px;'>
                    </div>

                    <!-- ASSET 5778 -->
                    <img src='../image.php?id=5778' style='width:25px; cursor:pointer; position:absolute; top:370px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5778' onclick='fetchAssetData(5778);' class="asset-image" data-id="<?php echo $assetId5778; ?>" data-room="<?php echo htmlspecialchars($room5778); ?>" data-floor="<?php echo htmlspecialchars($floor5778); ?>" data-image="<?php echo base64_encode($upload_img5778); ?>" data-category="<?php echo htmlspecialchars($category5778); ?>" data-status="<?php echo htmlspecialchars($status5778); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5778); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5778); ?>; 
    position:absolute; top:370px; left:300px;'>
                    </div>

                    <!-- ASSET 5779 -->
                    <img src='../image.php?id=5779' style='width:25px; cursor:pointer; position:absolute; top:370px; left:430px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5779' onclick='fetchAssetData(5779);' class="asset-image" data-id="<?php echo $assetId5779; ?>" data-room="<?php echo htmlspecialchars($room5779); ?>" data-floor="<?php echo htmlspecialchars($floor5779); ?>" data-image="<?php echo base64_encode($upload_img5779); ?>" data-category="<?php echo htmlspecialchars($category5779); ?>" data-status="<?php echo htmlspecialchars($status5779); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5779); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5779); ?>; 
    position:absolute; top:370px; left:455px;'>
                    </div>

                    <!-- ASSET 5780 -->
                    <img src='../image.php?id=5780' style='width:25px; cursor:pointer; position:absolute; top:410px; left:330px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5780' onclick='fetchAssetData(5780);' class="asset-image" data-id="<?php echo $assetId5780; ?>" data-room="<?php echo htmlspecialchars($room5780); ?>" data-floor="<?php echo htmlspecialchars($floor5780); ?>" data-image="<?php echo base64_encode($upload_img5780); ?>" data-status="<?php echo htmlspecialchars($status5780); ?>" data-category="<?php echo htmlspecialchars($category5780); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5780); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5780); ?>; 
    position:absolute; top:410px; left:355px;'>
                    </div>

                    <!-- ASSET 5781 -->
                    <img src='../image.php?id=5781' style='width:25px; cursor:pointer; position:absolute; top:410px; left:370px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5781' onclick='fetchAssetData(5781);' class="asset-image" data-id="<?php echo $assetId5781; ?>" data-room="<?php echo htmlspecialchars($room5781); ?>" data-floor="<?php echo htmlspecialchars($floor5781); ?>" data-image="<?php echo base64_encode($upload_img5781); ?>" data-category="<?php echo htmlspecialchars($category5781); ?>" data-status="<?php echo htmlspecialchars($status5781); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5781); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5781); ?>; 
    position:absolute; top:410px; left:395px;'>
                    </div>

                    <!-- ASSET 5782 -->
                    <img src='../image.php?id=5782' style='width:25px; cursor:pointer; position:absolute; top:450px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5782' onclick='fetchAssetData(5782);' class="asset-image" data-id="<?php echo $assetId5782; ?>" data-room="<?php echo htmlspecialchars($room5782); ?>" data-floor="<?php echo htmlspecialchars($floor5782); ?>" data-image="<?php echo base64_encode($upload_img5782); ?>" data-status="<?php echo htmlspecialchars($status5782); ?>" data-category="<?php echo htmlspecialchars($category5782); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5782); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5782); ?>; 
    position:absolute; top:450px; left:300px;'>
                    </div>

                    <!-- ASSET 5783 -->
                    <img src='../image.php?id=5783' style='width:25px; cursor:pointer; position:absolute; top:450px; left:430px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5783' onclick='fetchAssetData(5783);' class="asset-image" data-id="<?php echo $assetId5783; ?>" data-room="<?php echo htmlspecialchars($room5783); ?>" data-floor="<?php echo htmlspecialchars($floor5783); ?>" data-image="<?php echo base64_encode($upload_img5783); ?>" data-status="<?php echo htmlspecialchars($status5783); ?>" data-category="<?php echo htmlspecialchars($category5783); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5783); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5783); ?>; 
    position:absolute; top:450px; left:455px;'>
                    </div>

                    <!-- ASSET 5784 -->
                    <img src='../image.php?id=5784' style='width:25px; cursor:pointer; position:absolute; top:350px; left:570px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5784' onclick='fetchAssetData(5784);' class="asset-image" data-id="<?php echo $assetId5784; ?>" data-room="<?php echo htmlspecialchars($room5784); ?>" data-floor="<?php echo htmlspecialchars($floor5784); ?>" data-image="<?php echo base64_encode($upload_img5784); ?>" data-status="<?php echo htmlspecialchars($status5784); ?>" data-category="<?php echo htmlspecialchars($category5784); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5784); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5784); ?>; 
    position:absolute; top:350px; left:595px;'>
                    </div>

                    <!-- ASSET 5785 -->
                    <img src='../image.php?id=5785' style='width:25px; cursor:pointer; position:absolute; top:350px; left:640px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5785' onclick='fetchAssetData(5785);' class="asset-image" data-id="<?php echo $assetId5785; ?>" data-room="<?php echo htmlspecialchars($room5785); ?>" data-floor="<?php echo htmlspecialchars($floor5785); ?>" data-image="<?php echo base64_encode($upload_img5785); ?>" data-status="<?php echo htmlspecialchars($status5785); ?>" data-category="<?php echo htmlspecialchars($category5785); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5785); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5785); ?>; 
    position:absolute; top:350px; left:665px;'>
                    </div>

                    <!-- ASSET 5786 -->
                    <img src='../image.php?id=5786' style='width:25px; cursor:pointer; position:absolute; top:370px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5786' onclick='fetchAssetData(5786);' class="asset-image" data-id="<?php echo $assetId5786; ?>" data-room="<?php echo htmlspecialchars($room5786); ?>" data-floor="<?php echo htmlspecialchars($floor5786); ?>" data-image="<?php echo base64_encode($upload_img5786); ?>" data-status="<?php echo htmlspecialchars($status5786); ?>" data-category="<?php echo htmlspecialchars($category5786); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5786); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5786); ?>; 
    position:absolute; top:370px; left:795px;'>
                    </div>

                    <!-- ASSET 5787 -->
                    <img src='../image.php?id=5787' style='width:25px; cursor:pointer; position:absolute; top:370px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5787' onclick='fetchAssetData(5787);' class="asset-image" data-id="<?php echo $assetId5787; ?>" data-room="<?php echo htmlspecialchars($room5787); ?>" data-floor="<?php echo htmlspecialchars($floor5787); ?>" data-image="<?php echo base64_encode($upload_img5787); ?>" data-status="<?php echo htmlspecialchars($status5787); ?>" data-category="<?php echo htmlspecialchars($category5787); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5787); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5787); ?>; 
    position:absolute; top:370px; left:945px;'>
                    </div>

                    <!-- ASSET 5788 -->
                    <img src='../image.php?id=5788' style='width:25px; cursor:pointer; position:absolute; top:410px; left:820px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5788' onclick='fetchAssetData(5788);' class="asset-image" data-id="<?php echo $assetId5788; ?>" data-room="<?php echo htmlspecialchars($room5788); ?>" data-floor="<?php echo htmlspecialchars($floor5788); ?>" data-image="<?php echo base64_encode($upload_img5788); ?>" data-status="<?php echo htmlspecialchars($status5788); ?>" data-category="<?php echo htmlspecialchars($category5788); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5788); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5788); ?>; 
    position:absolute; top:410px; left:845px;'>
                    </div>

                    <!-- ASSET 5789 -->
                    <img src='../image.php?id=5789' style='width:25px; cursor:pointer; position:absolute; top:410px; left:860px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5789' onclick='fetchAssetData(5789);' class="asset-image" data-id="<?php echo $assetId5789; ?>" data-room="<?php echo htmlspecialchars($room5789); ?>" data-floor="<?php echo htmlspecialchars($floor5789); ?>" data-image="<?php echo base64_encode($upload_img5789); ?>" data-category="<?php echo htmlspecialchars($category5789); ?>" data-status="<?php echo htmlspecialchars($status5789); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5789); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5789); ?>; 
    position:absolute; top:410px; left:885px;'>
                    </div>

                    <!-- ASSET 5790 -->
                    <img src='../image.php?id=5790' style='width:25px; cursor:pointer; position:absolute; top:450px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5790' onclick='fetchAssetData(5790);' class="asset-image" data-id="<?php echo $assetId5790; ?>" data-room="<?php echo htmlspecialchars($room5790); ?>" data-floor="<?php echo htmlspecialchars($floor5790); ?>" data-image="<?php echo base64_encode($upload_img5790); ?>" data-category="<?php echo htmlspecialchars($category5790); ?>" data-status="<?php echo htmlspecialchars($status5790); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5790); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5790); ?>; 
    position:absolute; top:450px; left:795px;'>
                    </div>

                    <!-- ASSET 5791 -->
                    <img src='../image.php?id=5791' style='width:25px; cursor:pointer; position:absolute; top:450px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal5791' onclick='fetchAssetData(5791);' class="asset-image" data-id="<?php echo $assetId5791; ?>" data-room="<?php echo htmlspecialchars($room5791); ?>" data-floor="<?php echo htmlspecialchars($floor5791); ?>" data-image="<?php echo base64_encode($upload_img5791); ?>" data-status="<?php echo htmlspecialchars($status5791); ?>" data-category="<?php echo htmlspecialchars($category5791); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName5791); ?>">
                    <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status5791); ?>; 
    position:absolute; top:450px; left:945px;'>
                    </div>

                    <!--Start of hover-->
                    <div id="hover-asset" class="hover-asset" style="display: none;">
                        <!-- Content will be added dynamically -->
                    </div>
                    <!--End of hover-->




                </div>
                <!-- Modal structure for id 5762-->
                <div class='modal fade' id='imageModal5762' tabindex='-1' aria-labelledby='imageModalLabel5762' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5762); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5762); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5762); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5762); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5762); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5762); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5762); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5762); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5762 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5762 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5762 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5762 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5762); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5762); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5762); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5762">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5762-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5762" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5762">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Modal structure for id 5763-->
                <div class='modal fade' id='imageModal5763' tabindex='-1' aria-labelledby='imageModalLabel5763' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5763); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5763); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5763); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5763); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5763); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5763); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5763); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5763); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5763 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5763 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5763 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5763 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5763); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5763); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5763); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5763">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5763-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5763" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5763">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5764-->
                <div class='modal fade' id='imageModal5764' tabindex='-1' aria-labelledby='imageModalLabel5764' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5764); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5764); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5764); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5764); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5764); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5764); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5764); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5764); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5764 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5764 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5764 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5764 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5764); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5764); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5764); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5764">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5764-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5764" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5764">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5765-->
                <div class='modal fade' id='imageModal5765' tabindex='-1' aria-labelledby='imageModalLabel5765' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5765); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5765); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5765); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5765); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5765); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5765); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5765); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5765); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5765 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5765 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5765 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5765 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5765); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5765); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5765); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5765">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5765-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5765" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5765">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5766-->
                <div class='modal fade' id='imageModal5766' tabindex='-1' aria-labelledby='imageModalLabel5766' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5766); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5766); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5766); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5766); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5766); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5766); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5766); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5766); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5766 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5766 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5766 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5766 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5766); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5766); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5766); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5766">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5766-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5766" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5766">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5767-->
                <div class='modal fade' id='imageModal5767' tabindex='-1' aria-labelledby='imageModalLabel5767' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5767); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5767); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5767); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5767); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5767); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5767); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5767); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5767); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5767 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5767 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5767 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5767 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5767); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5767); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5767); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5767">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5767-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5767" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5767">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5768-->
                <div class='modal fade' id='imageModal5768' tabindex='-1' aria-labelledby='imageModalLabel5768' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5768); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5768); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5768); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5768); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5768); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5768); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5768); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5768); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5768 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5768 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5768 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5768 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5768); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5768); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5768); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5768">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5768-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5768" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5768">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5769-->
                <div class='modal fade' id='imageModal5769' tabindex='-1' aria-labelledby='imageModalLabel5769' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5769); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5769); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5769); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5769); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5769); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5769); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5769); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5769); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5769 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5769 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5769 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5769 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5769); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5769); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5769); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5769">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5769-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5769" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5769">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5770-->
                <div class='modal fade' id='imageModal5770' tabindex='-1' aria-labelledby='imageModalLabel5771' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5770); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5770); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5770); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5770); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5770); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5770); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5770); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5770); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5770 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5770 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5770 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5770 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5770); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5770); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5770); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5770">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5770-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5770" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5770">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5771-->
                <div class='modal fade' id='imageModal5771' tabindex='-1' aria-labelledby='imageModalLabel5771' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5771); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5771); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5771); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5771); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5771); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5771); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5771); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5771); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5771 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5771 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5771 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5771 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5771); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5771); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5771); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5771">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5771-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5771" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5771">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5772-->
                <div class='modal fade' id='imageModal5772' tabindex='-1' aria-labelledby='imageModalLabel5772' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5772); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5772); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5772); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5772); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5772); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5772); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5772); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5772); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5772 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5772 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5772 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5772 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5772); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5772); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5772); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5772">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5772-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5772" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5772">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5773-->
                <div class='modal fade' id='imageModal5773' tabindex='-1' aria-labelledby='imageModalLabel5773' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5773); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5773); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5773); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5773); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5773); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5773); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5773); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5773); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5773 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5773 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5773 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5773 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5773); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5773); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5773); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5773">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5773-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5773" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5773">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5774-->
                <div class='modal fade' id='imageModal5774' tabindex='-1' aria-labelledby='imageModalLabel5774' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5774); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5774); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5774); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5774); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5774); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5774); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5774); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5774); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5774 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5774 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5774 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5774 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5774); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5774); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5774); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5774">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5774-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5774" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5774">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5775-->
                <div class='modal fade' id='imageModal5775' tabindex='-1' aria-labelledby='imageModalLabel5775' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5775); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5775); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5775); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5775); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5775); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5775); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5775); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5775); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5775 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5775 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5775 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5775 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5775); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5775); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5775); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5775">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5775-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5775" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5775">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5776-->
                <div class='modal fade' id='imageModal5776' tabindex='-1' aria-labelledby='imageModalLabel5776' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5776); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5776); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5776); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5776); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5776); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5776); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5776); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5776); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5776 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5776 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5776 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5776 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5776); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5776); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5776); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5776">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5776-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5776" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5776">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5777-->
                <div class='modal fade' id='imageModal5777' tabindex='-1' aria-labelledby='imageModalLabel5777' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5777); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5777); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5777); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5777); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5777); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5777); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5777); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5777); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5777 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5777 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5777 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5777 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5777); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5777); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5777); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5777">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5777-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5777" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5777">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5778-->
                <div class='modal fade' id='imageModal5778' tabindex='-1' aria-labelledby='imageModalLabel5778' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5778); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5778); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5778); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5778); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5778); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5778); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5778); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5778); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5778 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5778 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5778 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5778 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5778); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5778); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5778); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5778">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5778-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5778" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5778">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5779-->
                <div class='modal fade' id='imageModal5779' tabindex='-1' aria-labelledby='imageModalLabel5779' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5779); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5779); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5779); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5779); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5779); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5779); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5779); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5779); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5779 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5779 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5779 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5779 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5779); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5779); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5779); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5779">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5779-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5779" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5779">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5780-->
                <div class='modal fade' id='imageModal5780' tabindex='-1' aria-labelledby='imageModalLabel5780' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5780); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5780); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5780); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5780); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5780); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5780); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5780); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5780); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5780 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5780 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5780 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5780 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5780); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5780); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5780); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5780">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5780-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5780" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5780">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5781-->
                <div class='modal fade' id='imageModal5781' tabindex='-1' aria-labelledby='imageModalLabel5781' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5781); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5781); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5781); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5781); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5781); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5781); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5781); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5781); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5781 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5781 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5781 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5781 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5781); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5781); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5781); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5781">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5781-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5781" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5781">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5782-->
                <div class='modal fade' id='imageModal5782' tabindex='-1' aria-labelledby='imageModalLabel5782' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5782); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5782); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5782); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5782); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5782); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5782); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5782); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5782); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5782 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5782 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5782 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5782 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5782); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5782); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5782); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5782">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5782-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5782" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5782">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5783-->
                <div class='modal fade' id='imageModal5783' tabindex='-1' aria-labelledby='imageModalLabel5783' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5783); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5783); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5783); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5783); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5783); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5783); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5783); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5783); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5783 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5783 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5783 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5783 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5783); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5783); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5783); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5783">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5783-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5783" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5783">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5784-->
                <div class='modal fade' id='imageModal5784' tabindex='-1' aria-labelledby='imageModalLabel5784' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5784); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5784); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5784); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5784); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5784); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5784); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5784); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5784); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5784 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5784 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5784 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5784 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5784); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5784); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5784); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5784">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5784-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5784" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5784">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5785-->
                <div class='modal fade' id='imageModal5785' tabindex='-1' aria-labelledby='imageModalLabel5785' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5785); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5785); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5785); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5785); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5785); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5785); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5785); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5785); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5785 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5785 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5785 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5785 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5785); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5785); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5785); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5785">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5785-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5785" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5785">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5786-->
                <div class='modal fade' id='imageModal5786' tabindex='-1' aria-labelledby='imageModalLabel5786' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5786); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5786); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5786); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5786); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5786); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5786); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5786); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5786); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5786 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5786 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5786 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5786 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5786); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5786); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5786); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5786">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5786-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5786" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5786">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5787-->
                <div class='modal fade' id='imageModal5787' tabindex='-1' aria-labelledby='imageModalLabel5787' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5787); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5787); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5787); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5787); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5787); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5787); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5787); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5787); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5787 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5787 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5787 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5787 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5787); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5787); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5787); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5787">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5787-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5787" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5787">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5788-->
                <div class='modal fade' id='imageModal5788' tabindex='-1' aria-labelledby='imageModalLabel5788' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5788); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5788); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5788); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5788); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5788); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5788); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5788); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5788); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5788 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5788 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5788 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5788 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5788); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5788); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5788); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5788">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5788-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5788" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5788">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5789-->
                <div class='modal fade' id='imageModal5789' tabindex='-1' aria-labelledby='imageModalLabel5789' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5789); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5789); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5789); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5789); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5789); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5789); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5789); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5789); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5789 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5789 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5789 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5789 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5789); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5789); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5789); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5789">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5789-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5789" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5789">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5790-->
                <div class='modal fade' id='imageModal5790' tabindex='-1' aria-labelledby='imageModalLabel5790' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5790); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5790); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5790); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5790); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5790); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5790); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5790); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5790); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5790 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5790 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5790 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5790 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5790); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5790); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5790); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5790">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5790-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5790" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5790">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- Modal structure for id 5791-->
                <div class='modal fade' id='imageModal5791' tabindex='-1' aria-labelledby='imageModalLabel5791' aria-hidden='true'>
                    <div class='modal-dialog modal-xl modal-dialog-centered'>
                        <div class='modal-content'>
                            <!-- Modal header -->
                            <div class='modal-header'>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>

                            <!-- Modal body -->
                            <div class='modal-body'>
                                <form method="post" class="row g-3" enctype="multipart/form-data">
                                    <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId5791); ?>">
                                    <!--START DIV FOR IMAGE -->

                                    <!--First Row-->
                                    <!--IMAGE HERE-->
                                    <div class="col-12 center-content">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img5791); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                    </div>
                                    <!--END DIV FOR IMAGE -->

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId5791); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date5791); ?>" readonly />
                                    </div>

                                    <!--Second Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room5791); ?>" readonly />
                                    </div>


                                    <div class="col-6" style="display:none">
                                        <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building5791); ?>" readonly />
                                    </div>

                                    <!--End of Second Row-->

                                    <!--Third Row-->
                                    <div class="col-6">
                                        <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor5791); ?>" readonly />
                                    </div>

                                    <div class="col-12 center-content">
                                        <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category5791); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <!--End of Third Row-->

                                    <!--Fourth Row-->
                                    <div class="col-2">
                                        <label for="status" class="form-label">Status:</label>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working" <?php echo ($status5791 == 'Working')
                                                                        ? 'selected="selected"' : ''; ?>>Working</option>
                                            <option value="Under Maintenance" <?php echo ($status5791 == 'Under Maintenance')
                                                                                    ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                            <option value="For Replacement" <?php echo ($status5791 == 'For Replacement')
                                                                                ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                            <option value="Need Repair" <?php echo ($status5791 == 'Need Repair')
                                                                            ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="<?php echo htmlspecialchars($assignedName5791); ?>" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="<?php echo htmlspecialchars($assignedBy5791); ?>" readonly />
                                    </div>

                                    <!--End of Fourth Row-->

                                    <!--Fifth Row-->
                                    <!-- <div class="col-3">
                                        <label for="description" class="form-label">Description:</label>
                                    </div> -->
                                    <div class="col-12">
                                        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($description5791); ?>" />
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
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop5791">
                                            Save
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Edit for table 5791-->
                <div class="map-alert">
                    <div class="modal fade" id="staticBackdrop5791" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    <p>Are you sure you want to save changes?</p>
                                    <div class="modal-popups">
                                        <button type="submit" class="btn add-modal-btn" name="edit5791">Yes</button>
                                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </main>
        </section>

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