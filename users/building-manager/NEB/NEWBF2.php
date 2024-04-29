<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';


if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
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
$stmtLatestLogs->bind_param("si",  $pattern, $loggedInAccountId);

// Execute the query
$stmtLatestLogs->execute();
$resultLatestLogs = $stmtLatestLogs->get_result(); 

$unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs 
WHERE p_seen = '0' AND accountID != ? AND action LIKE 'Assigned maintenance personnel%' AND action LIKE ?";
$pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

$stmt = $conn->prepare($unseenCountQuery);
$stmt->bind_param("is", $loggedInAccountId, $pattern );
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
       $mail->Host       = 'smtp.gmail.com';               // Set the SMTP server to send through
       $mail->SMTPAuth   = true;                             // Enable SMTP authentication
       $mail->Username   = 'qcu.upkeep@gmail.com';         // SMTP username
       $mail->Password   = 'qvpx bbcm bgmy hcvf';                  // SMTP password
       $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
       $mail->Port       = 587;                              // TCP port to connect to
   
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
                   $mail->Body    = "The status of asset with ID $assetId has been changed to $status.";
   
                   $mail->send();
                   echo 'Message has been sent';
                   break; // Stop the loop after sending the email
               }
           }
       }
   } catch (Exception $e) {
       echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
   }

    //KURT 1 DITO
   $assetIds = [16795, 16796, 16797, 16798, 16799, 16800, 16801, 16802, 16803, 16804, 16805, 16806, 16807, 16808, 16809, 16810, 16811, 16812, 16813, 16814, 16815, 16816, 16817, 16818, 16819, 16820, 16821, 16822, 16823, 16824, 16825, 16826, 16827, 16828, 16829, 16830, 16831, 16832, 16833, 16834, 16835, 16836, 16837, 16838, 16839, 16840, 16841, 16842, 16843, 16844, 16845, 16846, 16847, 16848, 16849, 16850, 16851, 16852, 16853, 16854, 16855, 16856, 16857, 16858, 16859, 16860, 16861, 16862, 16863, 16864, 16865, 16866, 16867, 16868, 16869, 16870, 16871, 16872, 16873, 16874, 16875, 16876, 16877, 16878, 16879, 16880, 16881, 16882, 16883, 16884, 16885, 16886, 16887, 16888, 16889, 16890, 16891, 16892, 16893, 16894, 16895, 16896, 16897, 16898, 16899, 16900, 16901, 16902, 16903, 16904, 16905, 16906, 16907, 16908, 16909, 16910, 16911, 16912, 16913, 16914, 16915, 16916, 16917, 16918, 16919, 16920, 16921, 16922, 16923, 16924, 16925, 16926, 16927, 16928, 16929, 16930, 16931, 16932, 16933, 16934, 16935, 16936, 16937, 16938, 16939, 16940, 16941, 16942, 16943, 16944, 16945, 16946, 16947, 16948, 16949, 16950, 16951, 16952, 16953, 16954, 16955, 16956, 16957, 16958, 16959, 16960, 16961, 16962, 16963, 16964, 16965, 16966, 16967, 16968, 16969, 16970, 16971, 16972, 16973, 16974, 16975, 16976, 16977, 16978, 16979, 16980, 16981, 16982, 16983, 16984, 16985, 16986, 16987, 16988, 16989, 16990, 16991, 16992, 16993, 16994, 16995, 16996, 16997, 16998, 16999, 17000, 17001, 17002, 17003, 17004, 17005, 17006, 17007, 17008, 17009, 17010, 17011, 17012, 17013, 17014, 17015, 17016, 17017, 17018, 17019, 17020, 17021, 17022, 17023, 17024, 17025, 17026, 17027, 17028, 17029, 17030, 17031, 17032, 17033, 17034, 17035, 17036, 17037, 17038, 17039, 17040, 17041, 17042, 17043, 17044, 17045, 17046, 17047, 17048, 17049, 17050, 17051, 17052, 17053, 17054, 17055, 17056, 17057, 17058, 17059, 17060, 17061, 17062, 17063, 17064, 17065, 17066, 17067, 17068, 17069, 17070, 17071, 17072, 17073, 17074, 17075, 17076, 17077, 17078, 17079, 17080, 17081, 17082, 17083, 17084, 17085, 17086, 17087, 17088, 17089, 17090, 17091, 17092, 17093, 17094, 17095, 17096, 17097, 17098, 17099, 17100, 17101, 17102, 17103, 17104, 17105, 17106, 17107, 17108, 17109, 17110, 17111, 17112, 17113, 17114, 17115, 17116, 17117, 17118, 17119, 17120, 17121, 17122, 17123, 17124, 17125, 17126, 17127, 17128, 17129, 17130, 17131, 17132, 17133, 17134, 17135, 17136, 17137, 17138, 17139, 17140, 17141, 17142, 17143, 17144, 17145, 17146, 17147, 17148, 17149, 17150, 17151, 17152, 17153, 17154, 17155, 17156, 17157, 17158, 17159, 17160, 17161, 17162, 17163, 17164, 17165, 17166, 17167, 17168, 17169, 17170, 17171, 17172, 17173, 17174, 17175, 17176, 17177, 17178, 17179, 17180, 17181, 17182, 17183, 17184, 17185, 17186, 17187, 17188, 17189, 17190, 17191, 17192, 17193, 17194, 17195, 17196, 17197, 17198, 17199, 17200, 17201, 17202, 17203, 17204, 17205, 17206, 17207, 17208, 17209, 17210, 17211, 17212, 17213, 17214, 17215, 17216, 17217, 17218, 17219, 17220, 17221, 17222, 17223, 17224, 17225, 17226, 17227, 17228, 17229, 17230, 17231, 17232, 17233, 17234, 17235, 17236, 17237, 17238, 17239, 17240, 17241, 17242, 17243, 17244, 17245, 17246, 17247, 17248, 17249, 17250, 17251, 17252, 17253, 17254, 17255, 17256, 17257, 17258, 17259, 17260, 17261, 17262, 17263, 17264, 17265, 17266, 17267, 17268, 17269, 17270, 17271, 17272, 17273, 17274, 17275, 17276, 17277, 17278, 18188, 18189, 18190, 18191, 18192, 18193, 18194, 18195, 18196];

   // Loop through each asset ID
   foreach ($assetIds as $id) {
       // Prepare and execute the SQL query
       $sql = "SELECT assetId, category, building, floor, room, images, assignedName, assignedBy, status, date, upload_img, description FROM asset WHERE assetId = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param('i', $id);
       $stmt->execute();
       $result = $stmt->get_result();

       // Fetch the data
       $row = $result->fetch_assoc();
       // Create variables dynamically using variable variables
       foreach ($row as $key => $value) {
           ${$key . $id} = $value;
       }
       $stmt->close();
   }

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
               header("Location: NEWBF2.php");
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
               header("Location: NEWBF2.php");
           } else {
               echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
           }
       }
   }
   //KURT 2 DITO
   // Call updateAsset function for each asset ID you want to handle
   $assetIds = [16795, 16796, 16797, 16798, 16799, 16800, 16801, 16802, 16803, 16804, 16805, 16806, 16807, 16808, 16809, 16810, 16811, 16812, 16813, 16814, 16815, 16816, 16817, 16818, 16819, 16820, 16821, 16822, 16823, 16824, 16825, 16826, 16827, 16828, 16829, 16830, 16831, 16832, 16833, 16834, 16835, 16836, 16837, 16838, 16839, 16840, 16841, 16842, 16843, 16844, 16845, 16846, 16847, 16848, 16849, 16850, 16851, 16852, 16853, 16854, 16855, 16856, 16857, 16858, 16859, 16860, 16861, 16862, 16863, 16864, 16865, 16866, 16867, 16868, 16869, 16870, 16871, 16872, 16873, 16874, 16875, 16876, 16877, 16878, 16879, 16880, 16881, 16882, 16883, 16884, 16885, 16886, 16887, 16888, 16889, 16890, 16891, 16892, 16893, 16894, 16895, 16896, 16897, 16898, 16899, 16900, 16901, 16902, 16903, 16904, 16905, 16906, 16907, 16908, 16909, 16910, 16911, 16912, 16913, 16914, 16915, 16916, 16917, 16918, 16919, 16920, 16921, 16922, 16923, 16924, 16925, 16926, 16927, 16928, 16929, 16930, 16931, 16932, 16933, 16934, 16935, 16936, 16937, 16938, 16939, 16940, 16941, 16942, 16943, 16944, 16945, 16946, 16947, 16948, 16949, 16950, 16951, 16952, 16953, 16954, 16955, 16956, 16957, 16958, 16959, 16960, 16961, 16962, 16963, 16964, 16965, 16966, 16967, 16968, 16969, 16970, 16971, 16972, 16973, 16974, 16975, 16976, 16977, 16978, 16979, 16980, 16981, 16982, 16983, 16984, 16985, 16986, 16987, 16988, 16989, 16990, 16991, 16992, 16993, 16994, 16995, 16996, 16997, 16998, 16999, 17000, 17001, 17002, 17003, 17004, 17005, 17006, 17007, 17008, 17009, 17010, 17011, 17012, 17013, 17014, 17015, 17016, 17017, 17018, 17019, 17020, 17021, 17022, 17023, 17024, 17025, 17026, 17027, 17028, 17029, 17030, 17031, 17032, 17033, 17034, 17035, 17036, 17037, 17038, 17039, 17040, 17041, 17042, 17043, 17044, 17045, 17046, 17047, 17048, 17049, 17050, 17051, 17052, 17053, 17054, 17055, 17056, 17057, 17058, 17059, 17060, 17061, 17062, 17063, 17064, 17065, 17066, 17067, 17068, 17069, 17070, 17071, 17072, 17073, 17074, 17075, 17076, 17077, 17078, 17079, 17080, 17081, 17082, 17083, 17084, 17085, 17086, 17087, 17088, 17089, 17090, 17091, 17092, 17093, 17094, 17095, 17096, 17097, 17098, 17099, 17100, 17101, 17102, 17103, 17104, 17105, 17106, 17107, 17108, 17109, 17110, 17111, 17112, 17113, 17114, 17115, 17116, 17117, 17118, 17119, 17120, 17121, 17122, 17123, 17124, 17125, 17126, 17127, 17128, 17129, 17130, 17131, 17132, 17133, 17134, 17135, 17136, 17137, 17138, 17139, 17140, 17141, 17142, 17143, 17144, 17145, 17146, 17147, 17148, 17149, 17150, 17151, 17152, 17153, 17154, 17155, 17156, 17157, 17158, 17159, 17160, 17161, 17162, 17163, 17164, 17165, 17166, 17167, 17168, 17169, 17170, 17171, 17172, 17173, 17174, 17175, 17176, 17177, 17178, 17179, 17180, 17181, 17182, 17183, 17184, 17185, 17186, 17187, 17188, 17189, 17190, 17191, 17192, 17193, 17194, 17195, 17196, 17197, 17198, 17199, 17200, 17201, 17202, 17203, 17204, 17205, 17206, 17207, 17208, 17209, 17210, 17211, 17212, 17213, 17214, 17215, 17216, 17217, 17218, 17219, 17220, 17221, 17222, 17223, 17224, 17225, 17226, 17227, 17228, 17229, 17230, 17231, 17232, 17233, 17234, 17235, 17236, 17237, 17238, 17239, 17240, 17241, 17242, 17243, 17244, 17245, 17246, 17247, 17248, 17249, 17250, 17251, 17252, 17253, 17254, 17255, 17256, 17257, 17258, 17259, 17260, 17261, 17262, 17263, 17264, 17265, 17266, 17267, 17268, 17269, 17270, 17271, 17272, 17273, 17274, 17275, 17276, 17277, 17278, 18188, 18189, 18190, 18191, 18192, 18193, 18194, 18195, 18196]; 
   
   // Add more asset IDs here
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
              WHERE al.tab='Report' AND al.seen = '0' AND al.accountID != ?
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/NEB/NEWBF1.css" />
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <script src="../../src/js/locationTracker.js"></script>
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
        <!-- SIDEBAR -->
        <section id="sidebar">
            <a href="./dashboard.php" class="brand" title="logo">
                <i><img src="../../../src/img/UpKeep.png" alt="" class="logo" /></i>
                <div class="mobile-sidebar-close">
                    <i class="bi bi-arrow-left-circle"></i>
                </div>
            </a>
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
                        <a href="../../manager/gps_history.php">
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
                <div class="Map-cont" onclick="toggleMAP()">
                    <li class="Map-dropdown">
                        <div class="Map-drondown-content">
                            <div class="Map-side-cont">
                                <i class="bi bi-receipt"></i>
                                <span class="text">Request</span>
                            </div>
                            <div class="Map-ind">
                                <i id="map-chevron-icon" class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                    </li>
                </div>
                <div class="Map-container">
                    <li class="Map-Batasan">
                        <a href="../../manager/batasan.php">
                            <i class="bi bi-building"></i>
                            <span class="text">Batasan</span>
                        </a>
                    </li>
                    <li class="Map-SanBartolome">
                        <a href="../../manager/sanBartolome.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Bartolome</span>
                        </a>
                    </li>
                    <li class="Map-SanFrancisco">
                        <a href="../../manager/sanFrancisco.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Francisco</span>
                        </a>
                    </li>
                </div>
                <li>
                    <a href="../../manager/activity-logs.php">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="text">Activity Logs</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- SIDEBAR -->
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
                    <img class="Floor-container-1" src="../../../src/floors/newAcademicB/NAB2F.png" alt="">

                    <div class="legend-body" id="legendBody">
                        <!-- Your legend body content goes here -->
                        <div class="legend-item"><img src="../../../src/legend/BED.jpg" alt="" class="legend-img">
                            <p>BED</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                            <p>BULB</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/CHAIR.jpg" alt="" class="legend-img">
                            <p>CHAIR</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/DESK.jpg" alt="" class="legend-img">
                            <p>DESK</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/SOFA.jpg" alt="" class="legend-img">
                            <p>SOFA</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/TABLE.jpg" alt="" class="legend-img">
                            <p>TABLE</p>
                        </div>
                        <div class="legend-item"><img src="../../../src/legend/TOILET-SEAT.jpg" alt=""
                                class="legend-img">
                            <p>TOILET SEAT</p>
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

                    <!-- START OF ASSETS -->
                    <!-- DITO START KURT THEN WALA KANA GAGALAWIN SA TAAS NG PART NA CODE MORE ON ASSETS NALANG -->

                    <!-- ASSET 16795 -->
                    <img src='../image.php?id=16795'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:190px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16795'
                        onclick='fetchAssetData(16795);' class='asset-image' data-id='<?php echo $assetId16795; ?>'
                        data-room='<?php echo htmlspecialchars($room16795); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16795); ?>'
                        data-image='<?php echo base64_encode($upload_img16795); ?>'
                        data-status='<?php echo htmlspecialchars($status16795); ?>'
                        data-category='<?php echo htmlspecialchars($category16795); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16795); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16795); ?>;
                        position:absolute; top:455px; left:200px;'>
                    </div>

                    <!-- ASSET 16796 -->
                    <img src='../image.php?id=16796'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:260px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16796'
                        onclick='fetchAssetData(16796);' class='asset-image' data-id='<?php echo $assetId16796; ?>'
                        data-room='<?php echo htmlspecialchars($room16796); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16796); ?>'
                        data-image='<?php echo base64_encode($upload_img16796); ?>'
                        data-status='<?php echo htmlspecialchars($status16796); ?>'
                        data-category='<?php echo htmlspecialchars($category16796); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16796); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16796); ?>;
                        position:absolute; top:455px; left:270px;'>
                    </div>

                    <!-- ASSET 16797 -->
                    <img src='../image.php?id=16797'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:190px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16797'
                        onclick='fetchAssetData(16797);' class='asset-image' data-id='<?php echo $assetId16797; ?>'
                        data-room='<?php echo htmlspecialchars($room16797); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16797); ?>'
                        data-image='<?php echo base64_encode($upload_img16797); ?>'
                        data-status='<?php echo htmlspecialchars($status16797); ?>'
                        data-category='<?php echo htmlspecialchars($category16797); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16797); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16797); ?>;
                        position:absolute; top:405px; left:200px;'>
                    </div>

                    <!-- ASSET 16798 -->
                    <img src='../image.php?id=16798'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:260px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16798'
                        onclick='fetchAssetData(16798);' class='asset-image' data-id='<?php echo $assetId16798; ?>'
                        data-room='<?php echo htmlspecialchars($room16798); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16798); ?>'
                        data-image='<?php echo base64_encode($upload_img16798); ?>'
                        data-status='<?php echo htmlspecialchars($status16798); ?>'
                        data-category='<?php echo htmlspecialchars($category16798); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16798); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16798); ?>;
                        position:absolute; top:405px; left:270px;'>
                    </div>

                    <!-- ASSET 16799 -->
                    <img src='../image.php?id=16799'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:360px; left:190px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16799'
                        onclick='fetchAssetData(16799);' class='asset-image' data-id='<?php echo $assetId16799; ?>'
                        data-room='<?php echo htmlspecialchars($room16799); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16799); ?>'
                        data-image='<?php echo base64_encode($upload_img16799); ?>'
                        data-status='<?php echo htmlspecialchars($status16799); ?>'
                        data-category='<?php echo htmlspecialchars($category16799); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16799); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16799); ?>;
                        position:absolute; top:355px; left:200px;'>
                    </div>

                    ASSET 16800
                    <img src='../image.php?id=16800'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16800'
                        onclick='fetchAssetData(16800);' class='asset-image' data-id='<?php echo $assetId16800; ?>'
                        data-room='<?php echo htmlspecialchars($room16800); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16800); ?>'
                        data-image='<?php echo base64_encode($upload_img16800); ?>'
                        data-status='<?php echo htmlspecialchars($status16800); ?>'
                        data-category='<?php echo htmlspecialchars($category16800); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16800); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16800); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16801 -->
                    <img src='../image.php?id=16801'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16801'
                        onclick='fetchAssetData(16801);' class='asset-image' data-id='<?php echo $assetId16801; ?>'
                        data-room='<?php echo htmlspecialchars($room16801); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16801); ?>'
                        data-image='<?php echo base64_encode($upload_img16801); ?>'
                        data-status='<?php echo htmlspecialchars($status16801); ?>'
                        data-category='<?php echo htmlspecialchars($category16801); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16801); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16801); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16802 -->
                    <img src='../image.php?id=16802'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16802'
                        onclick='fetchAssetData(16802);' class='asset-image' data-id='<?php echo $assetId16802; ?>'
                        data-room='<?php echo htmlspecialchars($room16802); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16802); ?>'
                        data-image='<?php echo base64_encode($upload_img16802); ?>'
                        data-status='<?php echo htmlspecialchars($status16802); ?>'
                        data-category='<?php echo htmlspecialchars($category16802); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16802); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16802); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16803 -->
                    <img src='../image.php?id=16803'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16803'
                        onclick='fetchAssetData(16803);' class='asset-image' data-id='<?php echo $assetId16803; ?>'
                        data-room='<?php echo htmlspecialchars($room16803); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16803); ?>'
                        data-image='<?php echo base64_encode($upload_img16803); ?>'
                        data-status='<?php echo htmlspecialchars($status16803); ?>'
                        data-category='<?php echo htmlspecialchars($category16803); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16803); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16803); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16804 -->
                    <img src='../image.php?id=16804'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16804'
                        onclick='fetchAssetData(16804);' class='asset-image' data-id='<?php echo $assetId16804; ?>'
                        data-room='<?php echo htmlspecialchars($room16804); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16804); ?>'
                        data-image='<?php echo base64_encode($upload_img16804); ?>'
                        data-status='<?php echo htmlspecialchars($status16804); ?>'
                        data-category='<?php echo htmlspecialchars($category16804); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16804); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16804); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16805 -->
                    <img src='../image.php?id=16805'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16805'
                        onclick='fetchAssetData(16805);' class='asset-image' data-id='<?php echo $assetId16805; ?>'
                        data-room='<?php echo htmlspecialchars($room16805); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16805); ?>'
                        data-image='<?php echo base64_encode($upload_img16805); ?>'
                        data-status='<?php echo htmlspecialchars($status16805); ?>'
                        data-category='<?php echo htmlspecialchars($category16805); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16805); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16805); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16806 -->
                    <img src='../image.php?id=16806'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16806'
                        onclick='fetchAssetData(16806);' class='asset-image' data-id='<?php echo $assetId16806; ?>'
                        data-room='<?php echo htmlspecialchars($room16806); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16806); ?>'
                        data-image='<?php echo base64_encode($upload_img16806); ?>'
                        data-status='<?php echo htmlspecialchars($status16806); ?>'
                        data-category='<?php echo htmlspecialchars($category16806); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16806); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16806); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16807 -->
                    <img src='../image.php?id=16807'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16807'
                        onclick='fetchAssetData(16807);' class='asset-image' data-id='<?php echo $assetId16807; ?>'
                        data-room='<?php echo htmlspecialchars($room16807); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16807); ?>'
                        data-image='<?php echo base64_encode($upload_img16807); ?>'
                        data-status='<?php echo htmlspecialchars($status16807); ?>'
                        data-category='<?php echo htmlspecialchars($category16807); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16807); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16807); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16808 -->
                    <img src='../image.php?id=16808'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16808'
                        onclick='fetchAssetData(16808);' class='asset-image' data-id='<?php echo $assetId16808; ?>'
                        data-room='<?php echo htmlspecialchars($room16808); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16808); ?>'
                        data-image='<?php echo base64_encode($upload_img16808); ?>'
                        data-status='<?php echo htmlspecialchars($status16808); ?>'
                        data-category='<?php echo htmlspecialchars($category16808); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16808); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16808); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16809 -->
                    <img src='../image.php?id=16809'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16809'
                        onclick='fetchAssetData(16809);' class='asset-image' data-id='<?php echo $assetId16809; ?>'
                        data-room='<?php echo htmlspecialchars($room16809); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16809); ?>'
                        data-image='<?php echo base64_encode($upload_img16809); ?>'
                        data-status='<?php echo htmlspecialchars($status16809); ?>'
                        data-category='<?php echo htmlspecialchars($category16809); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16809); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16809); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16810 -->
                    <img src='../image.php?id=16810'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16810'
                        onclick='fetchAssetData(16810);' class='asset-image' data-id='<?php echo $assetId16810; ?>'
                        data-room='<?php echo htmlspecialchars($room16810); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16810); ?>'
                        data-image='<?php echo base64_encode($upload_img16810); ?>'
                        data-status='<?php echo htmlspecialchars($status16810); ?>'
                        data-category='<?php echo htmlspecialchars($category16810); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16810); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16810); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16811 -->
                    <img src='../image.php?id=16811'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16811'
                        onclick='fetchAssetData(16811);' class='asset-image' data-id='<?php echo $assetId16811; ?>'
                        data-room='<?php echo htmlspecialchars($room16811); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16811); ?>'
                        data-image='<?php echo base64_encode($upload_img16811); ?>'
                        data-status='<?php echo htmlspecialchars($status16811); ?>'
                        data-category='<?php echo htmlspecialchars($category16811); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16811); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16811); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16812 -->
                    <img src='../image.php?id=16812'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16812'
                        onclick='fetchAssetData(16812);' class='asset-image' data-id='<?php echo $assetId16812; ?>'
                        data-room='<?php echo htmlspecialchars($room16812); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16812); ?>'
                        data-image='<?php echo base64_encode($upload_img16812); ?>'
                        data-status='<?php echo htmlspecialchars($status16812); ?>'
                        data-category='<?php echo htmlspecialchars($category16812); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16812); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16812); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16813 -->
                    <img src='../image.php?id=16813'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16813'
                        onclick='fetchAssetData(16813);' class='asset-image' data-id='<?php echo $assetId16813; ?>'
                        data-room='<?php echo htmlspecialchars($room16813); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16813); ?>'
                        data-image='<?php echo base64_encode($upload_img16813); ?>'
                        data-status='<?php echo htmlspecialchars($status16813); ?>'
                        data-category='<?php echo htmlspecialchars($category16813); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16813); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16813); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16814 -->
                    <img src='../image.php?id=16814'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16814'
                        onclick='fetchAssetData(16814);' class='asset-image' data-id='<?php echo $assetId16814; ?>'
                        data-room='<?php echo htmlspecialchars($room16814); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16814); ?>'
                        data-image='<?php echo base64_encode($upload_img16814); ?>'
                        data-status='<?php echo htmlspecialchars($status16814); ?>'
                        data-category='<?php echo htmlspecialchars($category16814); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16814); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16814); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16815 -->
                    <img src='../image.php?id=16815'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16815'
                        onclick='fetchAssetData(16815);' class='asset-image' data-id='<?php echo $assetId16815; ?>'
                        data-room='<?php echo htmlspecialchars($room16815); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16815); ?>'
                        data-image='<?php echo base64_encode($upload_img16815); ?>'
                        data-status='<?php echo htmlspecialchars($status16815); ?>'
                        data-category='<?php echo htmlspecialchars($category16815); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16815); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16815); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16816 -->
                    <img src='../image.php?id=16816'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16816'
                        onclick='fetchAssetData(16816);' class='asset-image' data-id='<?php echo $assetId16816; ?>'
                        data-room='<?php echo htmlspecialchars($room16816); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16816); ?>'
                        data-image='<?php echo base64_encode($upload_img16816); ?>'
                        data-status='<?php echo htmlspecialchars($status16816); ?>'
                        data-category='<?php echo htmlspecialchars($category16816); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16816); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16816); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16817 -->
                    <img src='../image.php?id=16817'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16817'
                        onclick='fetchAssetData(16817);' class='asset-image' data-id='<?php echo $assetId16817; ?>'
                        data-room='<?php echo htmlspecialchars($room16817); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16817); ?>'
                        data-image='<?php echo base64_encode($upload_img16817); ?>'
                        data-status='<?php echo htmlspecialchars($status16817); ?>'
                        data-category='<?php echo htmlspecialchars($category16817); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16817); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16817); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16818 -->
                    <img src='../image.php?id=16818'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16818'
                        onclick='fetchAssetData(16818);' class='asset-image' data-id='<?php echo $assetId16818; ?>'
                        data-room='<?php echo htmlspecialchars($room16818); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16818); ?>'
                        data-image='<?php echo base64_encode($upload_img16818); ?>'
                        data-status='<?php echo htmlspecialchars($status16818); ?>'
                        data-category='<?php echo htmlspecialchars($category16818); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16818); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16818); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16819 -->
                    <img src='../image.php?id=16819'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16819'
                        onclick='fetchAssetData(16819);' class='asset-image' data-id='<?php echo $assetId16819; ?>'
                        data-room='<?php echo htmlspecialchars($room16819); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16819); ?>'
                        data-image='<?php echo base64_encode($upload_img16819); ?>'
                        data-status='<?php echo htmlspecialchars($status16819); ?>'
                        data-category='<?php echo htmlspecialchars($category16819); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16819); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16819); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16820 -->
                    <img src='../image.php?id=16820'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16820'
                        onclick='fetchAssetData(16820);' class='asset-image' data-id='<?php echo $assetId16820; ?>'
                        data-room='<?php echo htmlspecialchars($room16820); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16820); ?>'
                        data-image='<?php echo base64_encode($upload_img16820); ?>'
                        data-status='<?php echo htmlspecialchars($status16820); ?>'
                        data-category='<?php echo htmlspecialchars($category16820); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16820); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16820); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16821 -->
                    <img src='../image.php?id=16821'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16821'
                        onclick='fetchAssetData(16821);' class='asset-image' data-id='<?php echo $assetId16821; ?>'
                        data-room='<?php echo htmlspecialchars($room16821); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16821); ?>'
                        data-image='<?php echo base64_encode($upload_img16821); ?>'
                        data-status='<?php echo htmlspecialchars($status16821); ?>'
                        data-category='<?php echo htmlspecialchars($category16821); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16821); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16821); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16822 -->
                    <img src='../image.php?id=16822'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16822'
                        onclick='fetchAssetData(16822);' class='asset-image' data-id='<?php echo $assetId16822; ?>'
                        data-room='<?php echo htmlspecialchars($room16822); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16822); ?>'
                        data-image='<?php echo base64_encode($upload_img16822); ?>'
                        data-status='<?php echo htmlspecialchars($status16822); ?>'
                        data-category='<?php echo htmlspecialchars($category16822); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16822); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16822); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16823 -->
                    <img src='../image.php?id=16823'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16823'
                        onclick='fetchAssetData(16823);' class='asset-image' data-id='<?php echo $assetId16823; ?>'
                        data-room='<?php echo htmlspecialchars($room16823); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16823); ?>'
                        data-image='<?php echo base64_encode($upload_img16823); ?>'
                        data-status='<?php echo htmlspecialchars($status16823); ?>'
                        data-category='<?php echo htmlspecialchars($category16823); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16823); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16823); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16824 -->
                    <img src='../image.php?id=16824'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:335px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16824'
                        onclick='fetchAssetData(16824);' class='asset-image' data-id='<?php echo $assetId16824; ?>'
                        data-room='<?php echo htmlspecialchars($room16824); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16824); ?>'
                        data-image='<?php echo base64_encode($upload_img16824); ?>'
                        data-status='<?php echo htmlspecialchars($status16824); ?>'
                        data-category='<?php echo htmlspecialchars($category16824); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16824); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16824); ?>;
                        position:absolute; top:455px; left:345px;'>
                    </div>

                    <!-- ASSET 16825 -->
                    <img src='../image.php?id=16825'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:385px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16825'
                        onclick='fetchAssetData(16825);' class='asset-image' data-id='<?php echo $assetId16825; ?>'
                        data-room='<?php echo htmlspecialchars($room16825); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16825); ?>'
                        data-image='<?php echo base64_encode($upload_img16825); ?>'
                        data-status='<?php echo htmlspecialchars($status16825); ?>'
                        data-category='<?php echo htmlspecialchars($category16825); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16825); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16825); ?>;
                        position:absolute; top:455px; left:395px;'>
                    </div>

                    <!-- ASSET 16826 -->
                    <img src='../image.php?id=16826'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:435px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16826'
                        onclick='fetchAssetData(16826);' class='asset-image' data-id='<?php echo $assetId16826; ?>'
                        data-room='<?php echo htmlspecialchars($room16826); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16826); ?>'
                        data-image='<?php echo base64_encode($upload_img16826); ?>'
                        data-status='<?php echo htmlspecialchars($status16826); ?>'
                        data-category='<?php echo htmlspecialchars($category16826); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16826); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16826); ?>;
                        position:absolute; top:455px; left:445px;'>
                    </div>

                    <!-- ASSET 16827 -->
                    <img src='../image.php?id=16827'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:485px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16827'
                        onclick='fetchAssetData(16827);' class='asset-image' data-id='<?php echo $assetId16827; ?>'
                        data-room='<?php echo htmlspecialchars($room16827); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16827); ?>'
                        data-image='<?php echo base64_encode($upload_img16827); ?>'
                        data-status='<?php echo htmlspecialchars($status16827); ?>'
                        data-category='<?php echo htmlspecialchars($category16827); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16827); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16827); ?>;
                        position:absolute; top:455px; left:495px;'>
                    </div>

                    <!-- ASSET 16828 -->
                    <img src='../image.php?id=16828'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:535px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16828'
                        onclick='fetchAssetData(16828);' class='asset-image' data-id='<?php echo $assetId16828; ?>'
                        data-room='<?php echo htmlspecialchars($room16828); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16828); ?>'
                        data-image='<?php echo base64_encode($upload_img16828); ?>'
                        data-status='<?php echo htmlspecialchars($status16828); ?>'
                        data-category='<?php echo htmlspecialchars($category16828); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16828); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16828); ?>;
                        position:absolute; top:455px; left:545px;'>
                    </div>

                    <!-- ASSET 16829 -->
                    <img src='../image.php?id=16829'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:460px; left:585px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16829'
                        onclick='fetchAssetData(16829);' class='asset-image' data-id='<?php echo $assetId16829; ?>'
                        data-room='<?php echo htmlspecialchars($room16829); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16829); ?>'
                        data-image='<?php echo base64_encode($upload_img16829); ?>'
                        data-status='<?php echo htmlspecialchars($status16829); ?>'
                        data-category='<?php echo htmlspecialchars($category16829); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16829); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16829); ?>;
                        position:absolute; top:455px; left:595px;'>
                    </div>

                    <!-- ASSET 16830 -->
                    <img src='../image.php?id=16830'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:335px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16830'
                        onclick='fetchAssetData(16830);' class='asset-image' data-id='<?php echo $assetId16830; ?>'
                        data-room='<?php echo htmlspecialchars($room16830); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16830); ?>'
                        data-image='<?php echo base64_encode($upload_img16830); ?>'
                        data-status='<?php echo htmlspecialchars($status16830); ?>'
                        data-category='<?php echo htmlspecialchars($category16830); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16830); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16830); ?>;
                        position:absolute; top:410px; left:345px;'>
                    </div>

                    <!-- ASSET 16831 -->
                    <img src='../image.php?id=16831'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:385px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16831'
                        onclick='fetchAssetData(16831);' class='asset-image' data-id='<?php echo $assetId16831; ?>'
                        data-room='<?php echo htmlspecialchars($room16831); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16831); ?>'
                        data-image='<?php echo base64_encode($upload_img16831); ?>'
                        data-status='<?php echo htmlspecialchars($status16831); ?>'
                        data-category='<?php echo htmlspecialchars($category16831); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16831); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16831); ?>;
                        position:absolute; top:410px; left:395px;'>
                    </div>

                    <!-- ASSET 16832 -->
                    <img src='../image.php?id=16832'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:435px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16832'
                        onclick='fetchAssetData(16832);' class='asset-image' data-id='<?php echo $assetId16832; ?>'
                        data-room='<?php echo htmlspecialchars($room16832); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16832); ?>'
                        data-image='<?php echo base64_encode($upload_img16832); ?>'
                        data-status='<?php echo htmlspecialchars($status16832); ?>'
                        data-category='<?php echo htmlspecialchars($category16832); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16832); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16832); ?>;
                        position:absolute; top:410px; left:445px;'>
                    </div>

                    <!-- ASSET 16833 -->
                    <img src='../image.php?id=16833'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:485px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16833'
                        onclick='fetchAssetData(16833);' class='asset-image' data-id='<?php echo $assetId16833; ?>'
                        data-room='<?php echo htmlspecialchars($room16833); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16833); ?>'
                        data-image='<?php echo base64_encode($upload_img16833); ?>'
                        data-status='<?php echo htmlspecialchars($status16833); ?>'
                        data-category='<?php echo htmlspecialchars($category16833); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16833); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16833); ?>;
                        position:absolute; top:410px; left:495px;'>
                    </div>

                    <!-- ASSET 16834 -->
                    <img src='../image.php?id=16834'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:535px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16834'
                        onclick='fetchAssetData(16834);' class='asset-image' data-id='<?php echo $assetId16834; ?>'
                        data-room='<?php echo htmlspecialchars($room16834); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16834); ?>'
                        data-image='<?php echo base64_encode($upload_img16834); ?>'
                        data-status='<?php echo htmlspecialchars($status16834); ?>'
                        data-category='<?php echo htmlspecialchars($category16834); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16834); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16834); ?>;
                        position:absolute; top:410px; left:545px;'>
                    </div>

                    <!-- ASSET 16835 -->
                    <img src='../image.php?id=16835'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:585px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16835'
                        onclick='fetchAssetData(16835);' class='asset-image' data-id='<?php echo $assetId16835; ?>'
                        data-room='<?php echo htmlspecialchars($room16835); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16835); ?>'
                        data-image='<?php echo base64_encode($upload_img16835); ?>'
                        data-status='<?php echo htmlspecialchars($status16835); ?>'
                        data-category='<?php echo htmlspecialchars($category16835); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16835); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16835); ?>;
                        position:absolute; top:410px; left:595px;'>
                    </div>

                    <!-- ASSET 16836 -->
                    <img src='../image.php?id=16836'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:335px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16836'
                        onclick='fetchAssetData(16836);' class='asset-image' data-id='<?php echo $assetId16836; ?>'
                        data-room='<?php echo htmlspecialchars($room16836); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16836); ?>'
                        data-image='<?php echo base64_encode($upload_img16836); ?>'
                        data-status='<?php echo htmlspecialchars($status16836); ?>'
                        data-category='<?php echo htmlspecialchars($category16836); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16836); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16836); ?>;
                        position:absolute; top:365px; left:345px;'>
                    </div>

                    <!-- ASSET 16837 -->
                    <img src='../image.php?id=16837'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:385px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16837'
                        onclick='fetchAssetData(16837);' class='asset-image' data-id='<?php echo $assetId16837; ?>'
                        data-room='<?php echo htmlspecialchars($room16837); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16837); ?>'
                        data-image='<?php echo base64_encode($upload_img16837); ?>'
                        data-status='<?php echo htmlspecialchars($status16837); ?>'
                        data-category='<?php echo htmlspecialchars($category16837); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16837); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16837); ?>;
                        position:absolute; top:365px; left:395px;'>
                    </div>

                    <!-- ASSET 16838 -->
                    <img src='../image.php?id=16838'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:435px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16838'
                        onclick='fetchAssetData(16838);' class='asset-image' data-id='<?php echo $assetId16838; ?>'
                        data-room='<?php echo htmlspecialchars($room16838); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16838); ?>'
                        data-image='<?php echo base64_encode($upload_img16838); ?>'
                        data-status='<?php echo htmlspecialchars($status16838); ?>'
                        data-category='<?php echo htmlspecialchars($category16838); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16838); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16838); ?>;
                        position:absolute; top:365px; left:445px;'>
                    </div>

                    <!-- ASSET 16839 -->
                    <img src='../image.php?id=16839'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:485px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16839'
                        onclick='fetchAssetData(16839);' class='asset-image' data-id='<?php echo $assetId16839; ?>'
                        data-room='<?php echo htmlspecialchars($room16839); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16839); ?>'
                        data-image='<?php echo base64_encode($upload_img16839); ?>'
                        data-status='<?php echo htmlspecialchars($status16839); ?>'
                        data-category='<?php echo htmlspecialchars($category16839); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16839); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16839); ?>;
                        position:absolute; top:365px; left:495px;'>
                    </div>

                    <!-- ASSET 16840 -->
                    <img src='../image.php?id=16840'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:535px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16840'
                        onclick='fetchAssetData(16840);' class='asset-image' data-id='<?php echo $assetId16840; ?>'
                        data-room='<?php echo htmlspecialchars($room16840); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16840); ?>'
                        data-image='<?php echo base64_encode($upload_img16840); ?>'
                        data-status='<?php echo htmlspecialchars($status16840); ?>'
                        data-category='<?php echo htmlspecialchars($category16840); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16840); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16840); ?>;
                        position:absolute; top:365px; left:545px;'>
                    </div>

                    <!-- ASSET 16841 -->
                    <img src='../image.php?id=16841'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:585px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16841'
                        onclick='fetchAssetData(16841);' class='asset-image' data-id='<?php echo $assetId16841; ?>'
                        data-room='<?php echo htmlspecialchars($room16841); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16841); ?>'
                        data-image='<?php echo base64_encode($upload_img16841); ?>'
                        data-status='<?php echo htmlspecialchars($status16841); ?>'
                        data-category='<?php echo htmlspecialchars($category16841); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16841); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16841); ?>;
                        position:absolute; top:365px; left:595px;'>
                    </div>

                    <!-- ASSET 16842 -->
                    <img src='../image.php?id=16842'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16842'
                        onclick='fetchAssetData(16842);' class='asset-image' data-id='<?php echo $assetId16842; ?>'
                        data-room='<?php echo htmlspecialchars($room16842); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16842); ?>'
                        data-image='<?php echo base64_encode($upload_img16842); ?>'
                        data-status='<?php echo htmlspecialchars($status16842); ?>'
                        data-category='<?php echo htmlspecialchars($category16842); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16842); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16842); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16843 -->
                    <img src='../image.php?id=16843'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16843'
                        onclick='fetchAssetData(16843);' class='asset-image' data-id='<?php echo $assetId16843; ?>'
                        data-room='<?php echo htmlspecialchars($room16843); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16843); ?>'
                        data-image='<?php echo base64_encode($upload_img16843); ?>'
                        data-status='<?php echo htmlspecialchars($status16843); ?>'
                        data-category='<?php echo htmlspecialchars($category16843); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16843); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16843); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16844 -->
                    <img src='../image.php?id=16844'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16844'
                        onclick='fetchAssetData(16844);' class='asset-image' data-id='<?php echo $assetId16844; ?>'
                        data-room='<?php echo htmlspecialchars($room16844); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16844); ?>'
                        data-image='<?php echo base64_encode($upload_img16844); ?>'
                        data-status='<?php echo htmlspecialchars($status16844); ?>'
                        data-category='<?php echo htmlspecialchars($category16844); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16844); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16844); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16845 -->
                    <img src='../image.php?id=16845'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16845'
                        onclick='fetchAssetData(16845);' class='asset-image' data-id='<?php echo $assetId16845; ?>'
                        data-room='<?php echo htmlspecialchars($room16845); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16845); ?>'
                        data-image='<?php echo base64_encode($upload_img16845); ?>'
                        data-status='<?php echo htmlspecialchars($status16845); ?>'
                        data-category='<?php echo htmlspecialchars($category16845); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16845); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16845); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16846 -->
                    <img src='../image.php?id=16846'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16846'
                        onclick='fetchAssetData(16846);' class='asset-image' data-id='<?php echo $assetId16846; ?>'
                        data-room='<?php echo htmlspecialchars($room16846); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16846); ?>'
                        data-image='<?php echo base64_encode($upload_img16846); ?>'
                        data-status='<?php echo htmlspecialchars($status16846); ?>'
                        data-category='<?php echo htmlspecialchars($category16846); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16846); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16846); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16847 -->
                    <img src='../image.php?id=16847'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16847'
                        onclick='fetchAssetData(16847);' class='asset-image' data-id='<?php echo $assetId16847; ?>'
                        data-room='<?php echo htmlspecialchars($room16847); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16847); ?>'
                        data-image='<?php echo base64_encode($upload_img16847); ?>'
                        data-status='<?php echo htmlspecialchars($status16847); ?>'
                        data-category='<?php echo htmlspecialchars($category16847); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16847); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16847); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16848 -->
                    <img src='../image.php?id=16848'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16848'
                        onclick='fetchAssetData(16848);' class='asset-image' data-id='<?php echo $assetId16848; ?>'
                        data-room='<?php echo htmlspecialchars($room16848); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16848); ?>'
                        data-image='<?php echo base64_encode($upload_img16848); ?>'
                        data-status='<?php echo htmlspecialchars($status16848); ?>'
                        data-category='<?php echo htmlspecialchars($category16848); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16848); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16848); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16849 -->
                    <img src='../image.php?id=16849'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16849'
                        onclick='fetchAssetData(16849);' class='asset-image' data-id='<?php echo $assetId16849; ?>'
                        data-room='<?php echo htmlspecialchars($room16849); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16849); ?>'
                        data-image='<?php echo base64_encode($upload_img16849); ?>'
                        data-status='<?php echo htmlspecialchars($status16849); ?>'
                        data-category='<?php echo htmlspecialchars($category16849); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16849); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16849); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16850 -->
                    <img src='../image.php?id=16850'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16850'
                        onclick='fetchAssetData(16850);' class='asset-image' data-id='<?php echo $assetId16850; ?>'
                        data-room='<?php echo htmlspecialchars($room16850); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16850); ?>'
                        data-image='<?php echo base64_encode($upload_img16850); ?>'
                        data-status='<?php echo htmlspecialchars($status16850); ?>'
                        data-category='<?php echo htmlspecialchars($category16850); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16850); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16850); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16851 -->
                    <img src='../image.php?id=16851'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16851'
                        onclick='fetchAssetData(16851);' class='asset-image' data-id='<?php echo $assetId16851; ?>'
                        data-room='<?php echo htmlspecialchars($room16851); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16851); ?>'
                        data-image='<?php echo base64_encode($upload_img16851); ?>'
                        data-status='<?php echo htmlspecialchars($status16851); ?>'
                        data-category='<?php echo htmlspecialchars($category16851); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16851); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16851); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16852 -->
                    <img src='../image.php?id=16852'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16852'
                        onclick='fetchAssetData(16852);' class='asset-image' data-id='<?php echo $assetId16852; ?>'
                        data-room='<?php echo htmlspecialchars($room16852); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16852); ?>'
                        data-image='<?php echo base64_encode($upload_img16852); ?>'
                        data-status='<?php echo htmlspecialchars($status16852); ?>'
                        data-category='<?php echo htmlspecialchars($category16852); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16852); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16852); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16853 -->
                    <img src='../image.php?id=16853'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16853'
                        onclick='fetchAssetData(16853);' class='asset-image' data-id='<?php echo $assetId16853; ?>'
                        data-room='<?php echo htmlspecialchars($room16853); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16853); ?>'
                        data-image='<?php echo base64_encode($upload_img16853); ?>'
                        data-status='<?php echo htmlspecialchars($status16853); ?>'
                        data-category='<?php echo htmlspecialchars($category16853); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16853); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16853); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16854 -->
                    <img src='../image.php?id=16854'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16854'
                        onclick='fetchAssetData(16854);' class='asset-image' data-id='<?php echo $assetId16854; ?>'
                        data-room='<?php echo htmlspecialchars($room16854); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16854); ?>'
                        data-image='<?php echo base64_encode($upload_img16854); ?>'
                        data-status='<?php echo htmlspecialchars($status16854); ?>'
                        data-category='<?php echo htmlspecialchars($category16854); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16854); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16854); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16855 -->
                    <img src='../image.php?id=16855'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16855'
                        onclick='fetchAssetData(16855);' class='asset-image' data-id='<?php echo $assetId16855; ?>'
                        data-room='<?php echo htmlspecialchars($room16855); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16855); ?>'
                        data-image='<?php echo base64_encode($upload_img16855); ?>'
                        data-status='<?php echo htmlspecialchars($status16855); ?>'
                        data-category='<?php echo htmlspecialchars($category16855); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16855); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16855); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16856 -->
                    <img src='../image.php?id=16856'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16856'
                        onclick='fetchAssetData(16856);' class='asset-image' data-id='<?php echo $assetId16856; ?>'
                        data-room='<?php echo htmlspecialchars($room16856); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16856); ?>'
                        data-image='<?php echo base64_encode($upload_img16856); ?>'
                        data-status='<?php echo htmlspecialchars($status16856); ?>'
                        data-category='<?php echo htmlspecialchars($category16856); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16856); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16856); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16857 -->
                    <img src='../image.php?id=16857'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16857'
                        onclick='fetchAssetData(16857);' class='asset-image' data-id='<?php echo $assetId16857; ?>'
                        data-room='<?php echo htmlspecialchars($room16857); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16857); ?>'
                        data-image='<?php echo base64_encode($upload_img16857); ?>'
                        data-status='<?php echo htmlspecialchars($status16857); ?>'
                        data-category='<?php echo htmlspecialchars($category16857); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16857); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16857); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16858 -->
                    <img src='../image.php?id=16858'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16858'
                        onclick='fetchAssetData(16858);' class='asset-image' data-id='<?php echo $assetId16858; ?>'
                        data-room='<?php echo htmlspecialchars($room16858); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16858); ?>'
                        data-image='<?php echo base64_encode($upload_img16858); ?>'
                        data-status='<?php echo htmlspecialchars($status16858); ?>'
                        data-category='<?php echo htmlspecialchars($category16858); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16858); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16858); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16859 -->
                    <img src='../image.php?id=16859'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16859'
                        onclick='fetchAssetData(16859);' class='asset-image' data-id='<?php echo $assetId16859; ?>'
                        data-room='<?php echo htmlspecialchars($room16859); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16859); ?>'
                        data-image='<?php echo base64_encode($upload_img16859); ?>'
                        data-status='<?php echo htmlspecialchars($status16859); ?>'
                        data-category='<?php echo htmlspecialchars($category16859); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16859); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16859); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16860 -->
                    <img src='../image.php?id=16860'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16860'
                        onclick='fetchAssetData(16860);' class='asset-image' data-id='<?php echo $assetId16860; ?>'
                        data-room='<?php echo htmlspecialchars($room16860); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16860); ?>'
                        data-image='<?php echo base64_encode($upload_img16860); ?>'
                        data-status='<?php echo htmlspecialchars($status16860); ?>'
                        data-category='<?php echo htmlspecialchars($category16860); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16860); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16860); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16861 -->
                    <img src='../image.php?id=16861'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16861'
                        onclick='fetchAssetData(16861);' class='asset-image' data-id='<?php echo $assetId16861; ?>'
                        data-room='<?php echo htmlspecialchars($room16861); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16861); ?>'
                        data-image='<?php echo base64_encode($upload_img16861); ?>'
                        data-status='<?php echo htmlspecialchars($status16861); ?>'
                        data-category='<?php echo htmlspecialchars($category16861); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16861); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16861); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16862 -->
                    <img src='../image.php?id=16862'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16862'
                        onclick='fetchAssetData(16862);' class='asset-image' data-id='<?php echo $assetId16862; ?>'
                        data-room='<?php echo htmlspecialchars($room16862); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16862); ?>'
                        data-image='<?php echo base64_encode($upload_img16862); ?>'
                        data-status='<?php echo htmlspecialchars($status16862); ?>'
                        data-category='<?php echo htmlspecialchars($category16862); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16862); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16862); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16863 -->
                    <img src='../image.php?id=16863'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16863'
                        onclick='fetchAssetData(16863);' class='asset-image' data-id='<?php echo $assetId16863; ?>'
                        data-room='<?php echo htmlspecialchars($room16863); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16863); ?>'
                        data-image='<?php echo base64_encode($upload_img16863); ?>'
                        data-status='<?php echo htmlspecialchars($status16863); ?>'
                        data-category='<?php echo htmlspecialchars($category16863); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16863); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16863); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16864 -->
                    <img src='../image.php?id=16864'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16864'
                        onclick='fetchAssetData(16864);' class='asset-image' data-id='<?php echo $assetId16864; ?>'
                        data-room='<?php echo htmlspecialchars($room16864); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16864); ?>'
                        data-image='<?php echo base64_encode($upload_img16864); ?>'
                        data-status='<?php echo htmlspecialchars($status16864); ?>'
                        data-category='<?php echo htmlspecialchars($category16864); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16864); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16864); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16865 -->
                    <img src='../image.php?id=16865'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16865'
                        onclick='fetchAssetData(16865);' class='asset-image' data-id='<?php echo $assetId16865; ?>'
                        data-room='<?php echo htmlspecialchars($room16865); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16865); ?>'
                        data-image='<?php echo base64_encode($upload_img16865); ?>'
                        data-status='<?php echo htmlspecialchars($status16865); ?>'
                        data-category='<?php echo htmlspecialchars($category16865); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16865); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16865); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16866 -->
                    <img src='../image.php?id=16866'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16866'
                        onclick='fetchAssetData(16866);' class='asset-image' data-id='<?php echo $assetId16866; ?>'
                        data-room='<?php echo htmlspecialchars($room16866); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16866); ?>'
                        data-image='<?php echo base64_encode($upload_img16866); ?>'
                        data-status='<?php echo htmlspecialchars($status16866); ?>'
                        data-category='<?php echo htmlspecialchars($category16866); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16866); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16866); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16867 -->
                    <img src='../image.php?id=16867'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16867'
                        onclick='fetchAssetData(16867);' class='asset-image' data-id='<?php echo $assetId16867; ?>'
                        data-room='<?php echo htmlspecialchars($room16867); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16867); ?>'
                        data-image='<?php echo base64_encode($upload_img16867); ?>'
                        data-status='<?php echo htmlspecialchars($status16867); ?>'
                        data-category='<?php echo htmlspecialchars($category16867); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16867); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16867); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16868 -->
                    <img src='../image.php?id=16868'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16868'
                        onclick='fetchAssetData(16868);' class='asset-image' data-id='<?php echo $assetId16868; ?>'
                        data-room='<?php echo htmlspecialchars($room16868); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16868); ?>'
                        data-image='<?php echo base64_encode($upload_img16868); ?>'
                        data-status='<?php echo htmlspecialchars($status16868); ?>'
                        data-category='<?php echo htmlspecialchars($category16868); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16868); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16868); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16869 -->
                    <img src='../image.php?id=16869'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16869'
                        onclick='fetchAssetData(16869);' class='asset-image' data-id='<?php echo $assetId16869; ?>'
                        data-room='<?php echo htmlspecialchars($room16869); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16869); ?>'
                        data-image='<?php echo base64_encode($upload_img16869); ?>'
                        data-status='<?php echo htmlspecialchars($status16869); ?>'
                        data-category='<?php echo htmlspecialchars($category16869); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16869); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16869); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16870 -->
                    <img src='../image.php?id=16870'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16870'
                        onclick='fetchAssetData(16870);' class='asset-image' data-id='<?php echo $assetId16870; ?>'
                        data-room='<?php echo htmlspecialchars($room16870); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16870); ?>'
                        data-image='<?php echo base64_encode($upload_img16870); ?>'
                        data-status='<?php echo htmlspecialchars($status16870); ?>'
                        data-category='<?php echo htmlspecialchars($category16870); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16870); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16870); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16871 -->
                    <img src='../image.php?id=16871'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16871'
                        onclick='fetchAssetData(16871);' class='asset-image' data-id='<?php echo $assetId16871; ?>'
                        data-room='<?php echo htmlspecialchars($room16871); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16871); ?>'
                        data-image='<?php echo base64_encode($upload_img16871); ?>'
                        data-status='<?php echo htmlspecialchars($status16871); ?>'
                        data-category='<?php echo htmlspecialchars($category16871); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16871); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16871); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16872 -->
                    <img src='../image.php?id=16872'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16872'
                        onclick='fetchAssetData(16872);' class='asset-image' data-id='<?php echo $assetId16872; ?>'
                        data-room='<?php echo htmlspecialchars($room16872); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16872); ?>'
                        data-image='<?php echo base64_encode($upload_img16872); ?>'
                        data-status='<?php echo htmlspecialchars($status16872); ?>'
                        data-category='<?php echo htmlspecialchars($category16872); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16872); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16872); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16873 -->
                    <img src='../image.php?id=16873'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16873'
                        onclick='fetchAssetData(16873);' class='asset-image' data-id='<?php echo $assetId16873; ?>'
                        data-room='<?php echo htmlspecialchars($room16873); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16873); ?>'
                        data-image='<?php echo base64_encode($upload_img16873); ?>'
                        data-status='<?php echo htmlspecialchars($status16873); ?>'
                        data-category='<?php echo htmlspecialchars($category16873); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16873); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16873); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16874 -->
                    <img src='../image.php?id=16874'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16874'
                        onclick='fetchAssetData(16874);' class='asset-image' data-id='<?php echo $assetId16874; ?>'
                        data-room='<?php echo htmlspecialchars($room16874); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16874); ?>'
                        data-image='<?php echo base64_encode($upload_img16874); ?>'
                        data-status='<?php echo htmlspecialchars($status16874); ?>'
                        data-category='<?php echo htmlspecialchars($category16874); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16874); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16874); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16875 -->
                    <img src='../image.php?id=16875'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16875'
                        onclick='fetchAssetData(16875);' class='asset-image' data-id='<?php echo $assetId16875; ?>'
                        data-room='<?php echo htmlspecialchars($room16875); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16875); ?>'
                        data-image='<?php echo base64_encode($upload_img16875); ?>'
                        data-status='<?php echo htmlspecialchars($status16875); ?>'
                        data-category='<?php echo htmlspecialchars($category16875); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16875); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16875); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16876 -->
                    <img src='../image.php?id=16876'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16876'
                        onclick='fetchAssetData(16876);' class='asset-image' data-id='<?php echo $assetId16876; ?>'
                        data-room='<?php echo htmlspecialchars($room16876); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16876); ?>'
                        data-image='<?php echo base64_encode($upload_img16876); ?>'
                        data-status='<?php echo htmlspecialchars($status16876); ?>'
                        data-category='<?php echo htmlspecialchars($category16876); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16876); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16876); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16877 -->
                    <img src='../image.php?id=16877'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16877'
                        onclick='fetchAssetData(16877);' class='asset-image' data-id='<?php echo $assetId16877; ?>'
                        data-room='<?php echo htmlspecialchars($room16877); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16877); ?>'
                        data-image='<?php echo base64_encode($upload_img16877); ?>'
                        data-status='<?php echo htmlspecialchars($status16877); ?>'
                        data-category='<?php echo htmlspecialchars($category16877); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16877); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16877); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16878 -->
                    <img src='../image.php?id=16878'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16878'
                        onclick='fetchAssetData(16878);' class='asset-image' data-id='<?php echo $assetId16878; ?>'
                        data-room='<?php echo htmlspecialchars($room16878); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16878); ?>'
                        data-image='<?php echo base64_encode($upload_img16878); ?>'
                        data-status='<?php echo htmlspecialchars($status16878); ?>'
                        data-category='<?php echo htmlspecialchars($category16878); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16878); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16878); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16879 -->
                    <img src='../image.php?id=16879'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16879'
                        onclick='fetchAssetData(16879);' class='asset-image' data-id='<?php echo $assetId16879; ?>'
                        data-room='<?php echo htmlspecialchars($room16879); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16879); ?>'
                        data-image='<?php echo base64_encode($upload_img16879); ?>'
                        data-status='<?php echo htmlspecialchars($status16879); ?>'
                        data-category='<?php echo htmlspecialchars($category16879); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16879); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16879); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16880 -->
                    <img src='../image.php?id=16880'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16880'
                        onclick='fetchAssetData(16880);' class='asset-image' data-id='<?php echo $assetId16880; ?>'
                        data-room='<?php echo htmlspecialchars($room16880); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16880); ?>'
                        data-image='<?php echo base64_encode($upload_img16880); ?>'
                        data-status='<?php echo htmlspecialchars($status16880); ?>'
                        data-category='<?php echo htmlspecialchars($category16880); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16880); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16880); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16881 -->
                    <img src='../image.php?id=16881'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16881'
                        onclick='fetchAssetData(16881);' class='asset-image' data-id='<?php echo $assetId16881; ?>'
                        data-room='<?php echo htmlspecialchars($room16881); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16881); ?>'
                        data-image='<?php echo base64_encode($upload_img16881); ?>'
                        data-status='<?php echo htmlspecialchars($status16881); ?>'
                        data-category='<?php echo htmlspecialchars($category16881); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16881); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16881); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16882 -->
                    <img src='../image.php?id=16882'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16882'
                        onclick='fetchAssetData(16882);' class='asset-image' data-id='<?php echo $assetId16882; ?>'
                        data-room='<?php echo htmlspecialchars($room16882); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16882); ?>'
                        data-image='<?php echo base64_encode($upload_img16882); ?>'
                        data-status='<?php echo htmlspecialchars($status16882); ?>'
                        data-category='<?php echo htmlspecialchars($category16882); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16882); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16882); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16883 -->
                    <img src='../image.php?id=16883'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16883'
                        onclick='fetchAssetData(16883);' class='asset-image' data-id='<?php echo $assetId16883; ?>'
                        data-room='<?php echo htmlspecialchars($room16883); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16883); ?>'
                        data-image='<?php echo base64_encode($upload_img16883); ?>'
                        data-status='<?php echo htmlspecialchars($status16883); ?>'
                        data-category='<?php echo htmlspecialchars($category16883); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16883); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16883); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16884 -->
                    <img src='../image.php?id=16884'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16884'
                        onclick='fetchAssetData(16884);' class='asset-image' data-id='<?php echo $assetId16884; ?>'
                        data-room='<?php echo htmlspecialchars($room16884); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16884); ?>'
                        data-image='<?php echo base64_encode($upload_img16884); ?>'
                        data-status='<?php echo htmlspecialchars($status16884); ?>'
                        data-category='<?php echo htmlspecialchars($category16884); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16884); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16884); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16885 -->
                    <img src='../image.php?id=16885'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16885'
                        onclick='fetchAssetData(16885);' class='asset-image' data-id='<?php echo $assetId16885; ?>'
                        data-room='<?php echo htmlspecialchars($room16885); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16885); ?>'
                        data-image='<?php echo base64_encode($upload_img16885); ?>'
                        data-status='<?php echo htmlspecialchars($status16885); ?>'
                        data-category='<?php echo htmlspecialchars($category16885); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16885); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16885); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16886 -->
                    <img src='../image.php?id=16886'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16886'
                        onclick='fetchAssetData(16886);' class='asset-image' data-id='<?php echo $assetId16886; ?>'
                        data-room='<?php echo htmlspecialchars($room16886); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16886); ?>'
                        data-image='<?php echo base64_encode($upload_img16886); ?>'
                        data-status='<?php echo htmlspecialchars($status16886); ?>'
                        data-category='<?php echo htmlspecialchars($category16886); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16886); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16886); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16887 -->
                    <img src='../image.php?id=16887'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16887'
                        onclick='fetchAssetData(16887);' class='asset-image' data-id='<?php echo $assetId16887; ?>'
                        data-room='<?php echo htmlspecialchars($room16887); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16887); ?>'
                        data-image='<?php echo base64_encode($upload_img16887); ?>'
                        data-status='<?php echo htmlspecialchars($status16887); ?>'
                        data-category='<?php echo htmlspecialchars($category16887); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16887); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16887); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16888 -->
                    <img src='../image.php?id=16888'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16888'
                        onclick='fetchAssetData(16888);' class='asset-image' data-id='<?php echo $assetId16888; ?>'
                        data-room='<?php echo htmlspecialchars($room16888); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16888); ?>'
                        data-image='<?php echo base64_encode($upload_img16888); ?>'
                        data-status='<?php echo htmlspecialchars($status16888); ?>'
                        data-category='<?php echo htmlspecialchars($category16888); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16888); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16888); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16889 -->
                    <img src='../image.php?id=16889'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16889'
                        onclick='fetchAssetData(16889);' class='asset-image' data-id='<?php echo $assetId16889; ?>'
                        data-room='<?php echo htmlspecialchars($room16889); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16889); ?>'
                        data-image='<?php echo base64_encode($upload_img16889); ?>'
                        data-status='<?php echo htmlspecialchars($status16889); ?>'
                        data-category='<?php echo htmlspecialchars($category16889); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16889); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16889); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16890 -->
                    <img src='../image.php?id=16890'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16890'
                        onclick='fetchAssetData(16890);' class='asset-image' data-id='<?php echo $assetId16890; ?>'
                        data-room='<?php echo htmlspecialchars($room16890); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16890); ?>'
                        data-image='<?php echo base64_encode($upload_img16890); ?>'
                        data-status='<?php echo htmlspecialchars($status16890); ?>'
                        data-category='<?php echo htmlspecialchars($category16890); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16890); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16890); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16891 -->
                    <img src='../image.php?id=16891'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16891'
                        onclick='fetchAssetData(16891);' class='asset-image' data-id='<?php echo $assetId16891; ?>'
                        data-room='<?php echo htmlspecialchars($room16891); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16891); ?>'
                        data-image='<?php echo base64_encode($upload_img16891); ?>'
                        data-status='<?php echo htmlspecialchars($status16891); ?>'
                        data-category='<?php echo htmlspecialchars($category16891); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16891); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16891); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16892 -->
                    <img src='../image.php?id=16892'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16892'
                        onclick='fetchAssetData(16892);' class='asset-image' data-id='<?php echo $assetId16892; ?>'
                        data-room='<?php echo htmlspecialchars($room16892); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16892); ?>'
                        data-image='<?php echo base64_encode($upload_img16892); ?>'
                        data-status='<?php echo htmlspecialchars($status16892); ?>'
                        data-category='<?php echo htmlspecialchars($category16892); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16892); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16892); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16893 -->
                    <img src='../image.php?id=16893'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16893'
                        onclick='fetchAssetData(16893);' class='asset-image' data-id='<?php echo $assetId16893; ?>'
                        data-room='<?php echo htmlspecialchars($room16893); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16893); ?>'
                        data-image='<?php echo base64_encode($upload_img16893); ?>'
                        data-status='<?php echo htmlspecialchars($status16893); ?>'
                        data-category='<?php echo htmlspecialchars($category16893); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16893); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16893); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16894 -->
                    <img src='../image.php?id=16894'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16894'
                        onclick='fetchAssetData(16894);' class='asset-image' data-id='<?php echo $assetId16894; ?>'
                        data-room='<?php echo htmlspecialchars($room16894); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16894); ?>'
                        data-image='<?php echo base64_encode($upload_img16894); ?>'
                        data-status='<?php echo htmlspecialchars($status16894); ?>'
                        data-category='<?php echo htmlspecialchars($category16894); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16894); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16894); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16895 -->
                    <img src='../image.php?id=16895'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16895'
                        onclick='fetchAssetData(16895);' class='asset-image' data-id='<?php echo $assetId16895; ?>'
                        data-room='<?php echo htmlspecialchars($room16895); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16895); ?>'
                        data-image='<?php echo base64_encode($upload_img16895); ?>'
                        data-status='<?php echo htmlspecialchars($status16895); ?>'
                        data-category='<?php echo htmlspecialchars($category16895); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16895); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16895); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16896 -->
                    <img src='../image.php?id=16896'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16896'
                        onclick='fetchAssetData(16896);' class='asset-image' data-id='<?php echo $assetId16896; ?>'
                        data-room='<?php echo htmlspecialchars($room16896); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16896); ?>'
                        data-image='<?php echo base64_encode($upload_img16896); ?>'
                        data-status='<?php echo htmlspecialchars($status16896); ?>'
                        data-category='<?php echo htmlspecialchars($category16896); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16896); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16896); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16897 -->
                    <img src='../image.php?id=16897'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16897'
                        onclick='fetchAssetData(16897);' class='asset-image' data-id='<?php echo $assetId16897; ?>'
                        data-room='<?php echo htmlspecialchars($room16897); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16897); ?>'
                        data-image='<?php echo base64_encode($upload_img16897); ?>'
                        data-status='<?php echo htmlspecialchars($status16897); ?>'
                        data-category='<?php echo htmlspecialchars($category16897); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16897); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16897); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16898 -->
                    <img src='../image.php?id=16898'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16898'
                        onclick='fetchAssetData(16898);' class='asset-image' data-id='<?php echo $assetId16898; ?>'
                        data-room='<?php echo htmlspecialchars($room16898); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16898); ?>'
                        data-image='<?php echo base64_encode($upload_img16898); ?>'
                        data-status='<?php echo htmlspecialchars($status16898); ?>'
                        data-category='<?php echo htmlspecialchars($category16898); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16898); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16898); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16899 -->
                    <img src='../image.php?id=16899'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16899'
                        onclick='fetchAssetData(16899);' class='asset-image' data-id='<?php echo $assetId16899; ?>'
                        data-room='<?php echo htmlspecialchars($room16899); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16899); ?>'
                        data-image='<?php echo base64_encode($upload_img16899); ?>'
                        data-status='<?php echo htmlspecialchars($status16899); ?>'
                        data-category='<?php echo htmlspecialchars($category16899); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16899); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16899); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16900 -->
                    <img src='../image.php?id=16900'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16900'
                        onclick='fetchAssetData(16900);' class='asset-image' data-id='<?php echo $assetId16900; ?>'
                        data-room='<?php echo htmlspecialchars($room16900); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16900); ?>'
                        data-image='<?php echo base64_encode($upload_img16900); ?>'
                        data-status='<?php echo htmlspecialchars($status16900); ?>'
                        data-category='<?php echo htmlspecialchars($category16900); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16900); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16900); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16901 -->
                    <img src='../image.php?id=16901'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16901'
                        onclick='fetchAssetData(16901);' class='asset-image' data-id='<?php echo $assetId16901; ?>'
                        data-room='<?php echo htmlspecialchars($room16901); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16901); ?>'
                        data-image='<?php echo base64_encode($upload_img16901); ?>'
                        data-status='<?php echo htmlspecialchars($status16901); ?>'
                        data-category='<?php echo htmlspecialchars($category16901); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16901); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16901); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16902 -->
                    <img src='../image.php?id=16902'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16902'
                        onclick='fetchAssetData(16902);' class='asset-image' data-id='<?php echo $assetId16902; ?>'
                        data-room='<?php echo htmlspecialchars($room16902); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16902); ?>'
                        data-image='<?php echo base64_encode($upload_img16902); ?>'
                        data-status='<?php echo htmlspecialchars($status16902); ?>'
                        data-category='<?php echo htmlspecialchars($category16902); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16902); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16902); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16903 -->
                    <img src='../image.php?id=16903'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16903'
                        onclick='fetchAssetData(16903);' class='asset-image' data-id='<?php echo $assetId16903; ?>'
                        data-room='<?php echo htmlspecialchars($room16903); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16903); ?>'
                        data-image='<?php echo base64_encode($upload_img16903); ?>'
                        data-status='<?php echo htmlspecialchars($status16903); ?>'
                        data-category='<?php echo htmlspecialchars($category16903); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16903); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16903); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16904 -->
                    <img src='../image.php?id=16904'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16904'
                        onclick='fetchAssetData(16904);' class='asset-image' data-id='<?php echo $assetId16904; ?>'
                        data-room='<?php echo htmlspecialchars($room16904); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16904); ?>'
                        data-image='<?php echo base64_encode($upload_img16904); ?>'
                        data-status='<?php echo htmlspecialchars($status16904); ?>'
                        data-category='<?php echo htmlspecialchars($category16904); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16904); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16904); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16905 -->
                    <img src='../image.php?id=16905'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16905'
                        onclick='fetchAssetData(16905);' class='asset-image' data-id='<?php echo $assetId16905; ?>'
                        data-room='<?php echo htmlspecialchars($room16905); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16905); ?>'
                        data-image='<?php echo base64_encode($upload_img16905); ?>'
                        data-status='<?php echo htmlspecialchars($status16905); ?>'
                        data-category='<?php echo htmlspecialchars($category16905); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16905); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16905); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16906 -->
                    <img src='../image.php?id=16906'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16906'
                        onclick='fetchAssetData(16906);' class='asset-image' data-id='<?php echo $assetId16906; ?>'
                        data-room='<?php echo htmlspecialchars($room16906); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16906); ?>'
                        data-image='<?php echo base64_encode($upload_img16906); ?>'
                        data-status='<?php echo htmlspecialchars($status16906); ?>'
                        data-category='<?php echo htmlspecialchars($category16906); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16906); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16906); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16907 -->
                    <img src='../image.php?id=16907'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16907'
                        onclick='fetchAssetData(16907);' class='asset-image' data-id='<?php echo $assetId16907; ?>'
                        data-room='<?php echo htmlspecialchars($room16907); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16907); ?>'
                        data-image='<?php echo base64_encode($upload_img16907); ?>'
                        data-status='<?php echo htmlspecialchars($status16907); ?>'
                        data-category='<?php echo htmlspecialchars($category16907); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16907); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16907); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16908 -->
                    <img src='../image.php?id=16908'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16908'
                        onclick='fetchAssetData(16908);' class='asset-image' data-id='<?php echo $assetId16908; ?>'
                        data-room='<?php echo htmlspecialchars($room16908); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16908); ?>'
                        data-image='<?php echo base64_encode($upload_img16908); ?>'
                        data-status='<?php echo htmlspecialchars($status16908); ?>'
                        data-category='<?php echo htmlspecialchars($category16908); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16908); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16908); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16909 -->
                    <img src='../image.php?id=16909'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16909'
                        onclick='fetchAssetData(16909);' class='asset-image' data-id='<?php echo $assetId16909; ?>'
                        data-room='<?php echo htmlspecialchars($room16909); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16909); ?>'
                        data-image='<?php echo base64_encode($upload_img16909); ?>'
                        data-status='<?php echo htmlspecialchars($status16909); ?>'
                        data-category='<?php echo htmlspecialchars($category16909); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16909); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16909); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16910 -->
                    <img src='../image.php?id=16910'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16910'
                        onclick='fetchAssetData(16910);' class='asset-image' data-id='<?php echo $assetId16910; ?>'
                        data-room='<?php echo htmlspecialchars($room16910); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16910); ?>'
                        data-image='<?php echo base64_encode($upload_img16910); ?>'
                        data-status='<?php echo htmlspecialchars($status16910); ?>'
                        data-category='<?php echo htmlspecialchars($category16910); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16910); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16910); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16911 -->
                    <img src='../image.php?id=16911'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16911'
                        onclick='fetchAssetData(16911);' class='asset-image' data-id='<?php echo $assetId16911; ?>'
                        data-room='<?php echo htmlspecialchars($room16911); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16911); ?>'
                        data-image='<?php echo base64_encode($upload_img16911); ?>'
                        data-status='<?php echo htmlspecialchars($status16911); ?>'
                        data-category='<?php echo htmlspecialchars($category16911); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16911); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16911); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16912 -->
                    <img src='../image.php?id=16912'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16912'
                        onclick='fetchAssetData(16912);' class='asset-image' data-id='<?php echo $assetId16912; ?>'
                        data-room='<?php echo htmlspecialchars($room16912); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16912); ?>'
                        data-image='<?php echo base64_encode($upload_img16912); ?>'
                        data-status='<?php echo htmlspecialchars($status16912); ?>'
                        data-category='<?php echo htmlspecialchars($category16912); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16912); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16912); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16913 -->
                    <img src='../image.php?id=16913'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16913'
                        onclick='fetchAssetData(16913);' class='asset-image' data-id='<?php echo $assetId16913; ?>'
                        data-room='<?php echo htmlspecialchars($room16913); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16913); ?>'
                        data-image='<?php echo base64_encode($upload_img16913); ?>'
                        data-status='<?php echo htmlspecialchars($status16913); ?>'
                        data-category='<?php echo htmlspecialchars($category16913); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16913); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16913); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16914 -->
                    <img src='../image.php?id=16914'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16914'
                        onclick='fetchAssetData(16914);' class='asset-image' data-id='<?php echo $assetId16914; ?>'
                        data-room='<?php echo htmlspecialchars($room16914); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16914); ?>'
                        data-image='<?php echo base64_encode($upload_img16914); ?>'
                        data-status='<?php echo htmlspecialchars($status16914); ?>'
                        data-category='<?php echo htmlspecialchars($category16914); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16914); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16914); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16915 -->
                    <img src='../image.php?id=16915'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16915'
                        onclick='fetchAssetData(16915);' class='asset-image' data-id='<?php echo $assetId16915; ?>'
                        data-room='<?php echo htmlspecialchars($room16915); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16915); ?>'
                        data-image='<?php echo base64_encode($upload_img16915); ?>'
                        data-status='<?php echo htmlspecialchars($status16915); ?>'
                        data-category='<?php echo htmlspecialchars($category16915); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16915); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16915); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16916 -->
                    <img src='../image.php?id=16916'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16916'
                        onclick='fetchAssetData(16916);' class='asset-image' data-id='<?php echo $assetId16916; ?>'
                        data-room='<?php echo htmlspecialchars($room16916); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16916); ?>'
                        data-image='<?php echo base64_encode($upload_img16916); ?>'
                        data-status='<?php echo htmlspecialchars($status16916); ?>'
                        data-category='<?php echo htmlspecialchars($category16916); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16916); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16916); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16917 -->
                    <img src='../image.php?id=16917'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16917'
                        onclick='fetchAssetData(16917);' class='asset-image' data-id='<?php echo $assetId16917; ?>'
                        data-room='<?php echo htmlspecialchars($room16917); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16917); ?>'
                        data-image='<?php echo base64_encode($upload_img16917); ?>'
                        data-status='<?php echo htmlspecialchars($status16917); ?>'
                        data-category='<?php echo htmlspecialchars($category16917); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16917); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16917); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16918 -->
                    <img src='../image.php?id=16918'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16918'
                        onclick='fetchAssetData(16918);' class='asset-image' data-id='<?php echo $assetId16918; ?>'
                        data-room='<?php echo htmlspecialchars($room16918); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16918); ?>'
                        data-image='<?php echo base64_encode($upload_img16918); ?>'
                        data-status='<?php echo htmlspecialchars($status16918); ?>'
                        data-category='<?php echo htmlspecialchars($category16918); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16918); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16918); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16919 -->
                    <img src='../image.php?id=16919'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16919'
                        onclick='fetchAssetData(16919);' class='asset-image' data-id='<?php echo $assetId16919; ?>'
                        data-room='<?php echo htmlspecialchars($room16919); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16919); ?>'
                        data-image='<?php echo base64_encode($upload_img16919); ?>'
                        data-status='<?php echo htmlspecialchars($status16919); ?>'
                        data-category='<?php echo htmlspecialchars($category16919); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16919); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16919); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16920 -->
                    <img src='../image.php?id=16920'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16920'
                        onclick='fetchAssetData(16920);' class='asset-image' data-id='<?php echo $assetId16920; ?>'
                        data-room='<?php echo htmlspecialchars($room16920); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16920); ?>'
                        data-image='<?php echo base64_encode($upload_img16920); ?>'
                        data-status='<?php echo htmlspecialchars($status16920); ?>'
                        data-category='<?php echo htmlspecialchars($category16920); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16920); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16920); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16921 -->
                    <img src='../image.php?id=16921'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16921'
                        onclick='fetchAssetData(16921);' class='asset-image' data-id='<?php echo $assetId16921; ?>'
                        data-room='<?php echo htmlspecialchars($room16921); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16921); ?>'
                        data-image='<?php echo base64_encode($upload_img16921); ?>'
                        data-status='<?php echo htmlspecialchars($status16921); ?>'
                        data-category='<?php echo htmlspecialchars($category16921); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16921); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16921); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16922 -->
                    <img src='../image.php?id=16922'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16922'
                        onclick='fetchAssetData(16922);' class='asset-image' data-id='<?php echo $assetId16922; ?>'
                        data-room='<?php echo htmlspecialchars($room16922); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16922); ?>'
                        data-image='<?php echo base64_encode($upload_img16922); ?>'
                        data-status='<?php echo htmlspecialchars($status16922); ?>'
                        data-category='<?php echo htmlspecialchars($category16922); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16922); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16922); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16923 -->
                    <img src='../image.php?id=16923'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16923'
                        onclick='fetchAssetData(16923);' class='asset-image' data-id='<?php echo $assetId16923; ?>'
                        data-room='<?php echo htmlspecialchars($room16923); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16923); ?>'
                        data-image='<?php echo base64_encode($upload_img16923); ?>'
                        data-status='<?php echo htmlspecialchars($status16923); ?>'
                        data-category='<?php echo htmlspecialchars($category16923); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16923); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16923); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16924 -->
                    <img src='../image.php?id=16924'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16924'
                        onclick='fetchAssetData(16924);' class='asset-image' data-id='<?php echo $assetId16924; ?>'
                        data-room='<?php echo htmlspecialchars($room16924); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16924); ?>'
                        data-image='<?php echo base64_encode($upload_img16924); ?>'
                        data-status='<?php echo htmlspecialchars($status16924); ?>'
                        data-category='<?php echo htmlspecialchars($category16924); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16924); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16924); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16925 -->
                    <img src='../image.php?id=16925'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16925'
                        onclick='fetchAssetData(16925);' class='asset-image' data-id='<?php echo $assetId16925; ?>'
                        data-room='<?php echo htmlspecialchars($room16925); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16925); ?>'
                        data-image='<?php echo base64_encode($upload_img16925); ?>'
                        data-status='<?php echo htmlspecialchars($status16925); ?>'
                        data-category='<?php echo htmlspecialchars($category16925); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16925); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16925); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16926 -->
                    <img src='../image.php?id=16926'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16926'
                        onclick='fetchAssetData(16926);' class='asset-image' data-id='<?php echo $assetId16926; ?>'
                        data-room='<?php echo htmlspecialchars($room16926); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16926); ?>'
                        data-image='<?php echo base64_encode($upload_img16926); ?>'
                        data-status='<?php echo htmlspecialchars($status16926); ?>'
                        data-category='<?php echo htmlspecialchars($category16926); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16926); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16926); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16927 -->
                    <img src='../image.php?id=16927'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16927'
                        onclick='fetchAssetData(16927);' class='asset-image' data-id='<?php echo $assetId16927; ?>'
                        data-room='<?php echo htmlspecialchars($room16927); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16927); ?>'
                        data-image='<?php echo base64_encode($upload_img16927); ?>'
                        data-status='<?php echo htmlspecialchars($status16927); ?>'
                        data-category='<?php echo htmlspecialchars($category16927); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16927); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16927); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16928 -->
                    <img src='../image.php?id=16928'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16928'
                        onclick='fetchAssetData(16928);' class='asset-image' data-id='<?php echo $assetId16928; ?>'
                        data-room='<?php echo htmlspecialchars($room16928); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16928); ?>'
                        data-image='<?php echo base64_encode($upload_img16928); ?>'
                        data-status='<?php echo htmlspecialchars($status16928); ?>'
                        data-category='<?php echo htmlspecialchars($category16928); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16928); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16928); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16929 -->
                    <img src='../image.php?id=16929'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16929'
                        onclick='fetchAssetData(16929);' class='asset-image' data-id='<?php echo $assetId16929; ?>'
                        data-room='<?php echo htmlspecialchars($room16929); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16929); ?>'
                        data-image='<?php echo base64_encode($upload_img16929); ?>'
                        data-status='<?php echo htmlspecialchars($status16929); ?>'
                        data-category='<?php echo htmlspecialchars($category16929); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16929); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16929); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16930 -->
                    <img src='../image.php?id=16930'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16930'
                        onclick='fetchAssetData(16930);' class='asset-image' data-id='<?php echo $assetId16930; ?>'
                        data-room='<?php echo htmlspecialchars($room16930); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16930); ?>'
                        data-image='<?php echo base64_encode($upload_img16930); ?>'
                        data-status='<?php echo htmlspecialchars($status16930); ?>'
                        data-category='<?php echo htmlspecialchars($category16930); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16930); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16930); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16931 -->
                    <img src='../image.php?id=16931'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16931'
                        onclick='fetchAssetData(16931);' class='asset-image' data-id='<?php echo $assetId16931; ?>'
                        data-room='<?php echo htmlspecialchars($room16931); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16931); ?>'
                        data-image='<?php echo base64_encode($upload_img16931); ?>'
                        data-status='<?php echo htmlspecialchars($status16931); ?>'
                        data-category='<?php echo htmlspecialchars($category16931); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16931); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16931); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16932 -->
                    <img src='../image.php?id=16932'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16932'
                        onclick='fetchAssetData(16932);' class='asset-image' data-id='<?php echo $assetId16932; ?>'
                        data-room='<?php echo htmlspecialchars($room16932); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16932); ?>'
                        data-image='<?php echo base64_encode($upload_img16932); ?>'
                        data-status='<?php echo htmlspecialchars($status16932); ?>'
                        data-category='<?php echo htmlspecialchars($category16932); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16932); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16932); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16933 -->
                    <img src='../image.php?id=16933'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16933'
                        onclick='fetchAssetData(16933);' class='asset-image' data-id='<?php echo $assetId16933; ?>'
                        data-room='<?php echo htmlspecialchars($room16933); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16933); ?>'
                        data-image='<?php echo base64_encode($upload_img16933); ?>'
                        data-status='<?php echo htmlspecialchars($status16933); ?>'
                        data-category='<?php echo htmlspecialchars($category16933); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16933); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16933); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16934 -->
                    <img src='../image.php?id=16934'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16934'
                        onclick='fetchAssetData(16934);' class='asset-image' data-id='<?php echo $assetId16934; ?>'
                        data-room='<?php echo htmlspecialchars($room16934); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16934); ?>'
                        data-image='<?php echo base64_encode($upload_img16934); ?>'
                        data-status='<?php echo htmlspecialchars($status16934); ?>'
                        data-category='<?php echo htmlspecialchars($category16934); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16934); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16934); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16935 -->
                    <img src='../image.php?id=16935'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16935'
                        onclick='fetchAssetData(16935);' class='asset-image' data-id='<?php echo $assetId16935; ?>'
                        data-room='<?php echo htmlspecialchars($room16935); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16935); ?>'
                        data-image='<?php echo base64_encode($upload_img16935); ?>'
                        data-status='<?php echo htmlspecialchars($status16935); ?>'
                        data-category='<?php echo htmlspecialchars($category16935); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16935); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16935); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16936 -->
                    <img src='../image.php?id=16936'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16936'
                        onclick='fetchAssetData(16936);' class='asset-image' data-id='<?php echo $assetId16936; ?>'
                        data-room='<?php echo htmlspecialchars($room16936); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16936); ?>'
                        data-image='<?php echo base64_encode($upload_img16936); ?>'
                        data-status='<?php echo htmlspecialchars($status16936); ?>'
                        data-category='<?php echo htmlspecialchars($category16936); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16936); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16936); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16937 -->
                    <img src='../image.php?id=16937'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16937'
                        onclick='fetchAssetData(16937);' class='asset-image' data-id='<?php echo $assetId16937; ?>'
                        data-room='<?php echo htmlspecialchars($room16937); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16937); ?>'
                        data-image='<?php echo base64_encode($upload_img16937); ?>'
                        data-status='<?php echo htmlspecialchars($status16937); ?>'
                        data-category='<?php echo htmlspecialchars($category16937); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16937); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16937); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16938 -->
                    <img src='../image.php?id=16938'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16938'
                        onclick='fetchAssetData(16938);' class='asset-image' data-id='<?php echo $assetId16938; ?>'
                        data-room='<?php echo htmlspecialchars($room16938); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16938); ?>'
                        data-image='<?php echo base64_encode($upload_img16938); ?>'
                        data-status='<?php echo htmlspecialchars($status16938); ?>'
                        data-category='<?php echo htmlspecialchars($category16938); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16938); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16938); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16939 -->
                    <img src='../image.php?id=16939'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16939'
                        onclick='fetchAssetData(16939);' class='asset-image' data-id='<?php echo $assetId16939; ?>'
                        data-room='<?php echo htmlspecialchars($room16939); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16939); ?>'
                        data-image='<?php echo base64_encode($upload_img16939); ?>'
                        data-status='<?php echo htmlspecialchars($status16939); ?>'
                        data-category='<?php echo htmlspecialchars($category16939); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16939); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16939); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16940 -->
                    <img src='../image.php?id=16940'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16940'
                        onclick='fetchAssetData(16940);' class='asset-image' data-id='<?php echo $assetId16940; ?>'
                        data-room='<?php echo htmlspecialchars($room16940); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16940); ?>'
                        data-image='<?php echo base64_encode($upload_img16940); ?>'
                        data-status='<?php echo htmlspecialchars($status16940); ?>'
                        data-category='<?php echo htmlspecialchars($category16940); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16940); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16940); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16941 -->
                    <img src='../image.php?id=16941'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16941'
                        onclick='fetchAssetData(16941);' class='asset-image' data-id='<?php echo $assetId16941; ?>'
                        data-room='<?php echo htmlspecialchars($room16941); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16941); ?>'
                        data-image='<?php echo base64_encode($upload_img16941); ?>'
                        data-status='<?php echo htmlspecialchars($status16941); ?>'
                        data-category='<?php echo htmlspecialchars($category16941); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16941); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16941); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16942 -->
                    <img src='../image.php?id=16942'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16942'
                        onclick='fetchAssetData(16942);' class='asset-image' data-id='<?php echo $assetId16942; ?>'
                        data-room='<?php echo htmlspecialchars($room16942); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16942); ?>'
                        data-image='<?php echo base64_encode($upload_img16942); ?>'
                        data-status='<?php echo htmlspecialchars($status16942); ?>'
                        data-category='<?php echo htmlspecialchars($category16942); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16942); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16942); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16943 -->
                    <img src='../image.php?id=16943'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16943'
                        onclick='fetchAssetData(16943);' class='asset-image' data-id='<?php echo $assetId16943; ?>'
                        data-room='<?php echo htmlspecialchars($room16943); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16943); ?>'
                        data-image='<?php echo base64_encode($upload_img16943); ?>'
                        data-status='<?php echo htmlspecialchars($status16943); ?>'
                        data-category='<?php echo htmlspecialchars($category16943); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16943); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16943); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16944 -->
                    <img src='../image.php?id=16944'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16944'
                        onclick='fetchAssetData(16944);' class='asset-image' data-id='<?php echo $assetId16944; ?>'
                        data-room='<?php echo htmlspecialchars($room16944); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16944); ?>'
                        data-image='<?php echo base64_encode($upload_img16944); ?>'
                        data-status='<?php echo htmlspecialchars($status16944); ?>'
                        data-category='<?php echo htmlspecialchars($category16944); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16944); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16944); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16945 -->
                    <img src='../image.php?id=16945'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16945'
                        onclick='fetchAssetData(16945);' class='asset-image' data-id='<?php echo $assetId16945; ?>'
                        data-room='<?php echo htmlspecialchars($room16945); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16945); ?>'
                        data-image='<?php echo base64_encode($upload_img16945); ?>'
                        data-status='<?php echo htmlspecialchars($status16945); ?>'
                        data-category='<?php echo htmlspecialchars($category16945); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16945); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16945); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16946 -->
                    <img src='../image.php?id=16946'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16946'
                        onclick='fetchAssetData(16946);' class='asset-image' data-id='<?php echo $assetId16946; ?>'
                        data-room='<?php echo htmlspecialchars($room16946); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16946); ?>'
                        data-image='<?php echo base64_encode($upload_img16946); ?>'
                        data-status='<?php echo htmlspecialchars($status16946); ?>'
                        data-category='<?php echo htmlspecialchars($category16946); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16946); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16946); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16947 -->
                    <img src='../image.php?id=16947'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16947'
                        onclick='fetchAssetData(16947);' class='asset-image' data-id='<?php echo $assetId16947; ?>'
                        data-room='<?php echo htmlspecialchars($room16947); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16947); ?>'
                        data-image='<?php echo base64_encode($upload_img16947); ?>'
                        data-status='<?php echo htmlspecialchars($status16947); ?>'
                        data-category='<?php echo htmlspecialchars($category16947); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16947); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16947); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16948 -->
                    <img src='../image.php?id=16948'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16948'
                        onclick='fetchAssetData(16948);' class='asset-image' data-id='<?php echo $assetId16948; ?>'
                        data-room='<?php echo htmlspecialchars($room16948); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16948); ?>'
                        data-image='<?php echo base64_encode($upload_img16948); ?>'
                        data-status='<?php echo htmlspecialchars($status16948); ?>'
                        data-category='<?php echo htmlspecialchars($category16948); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16948); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16948); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16949 -->
                    <img src='../image.php?id=16949'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16949'
                        onclick='fetchAssetData(16949);' class='asset-image' data-id='<?php echo $assetId16949; ?>'
                        data-room='<?php echo htmlspecialchars($room16949); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16949); ?>'
                        data-image='<?php echo base64_encode($upload_img16949); ?>'
                        data-status='<?php echo htmlspecialchars($status16949); ?>'
                        data-category='<?php echo htmlspecialchars($category16949); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16949); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16949); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16950 -->
                    <img src='../image.php?id=16950'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16950'
                        onclick='fetchAssetData(16950);' class='asset-image' data-id='<?php echo $assetId16950; ?>'
                        data-room='<?php echo htmlspecialchars($room16950); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16950); ?>'
                        data-image='<?php echo base64_encode($upload_img16950); ?>'
                        data-status='<?php echo htmlspecialchars($status16950); ?>'
                        data-category='<?php echo htmlspecialchars($category16950); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16950); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16950); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16951 -->
                    <img src='../image.php?id=16951'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16951'
                        onclick='fetchAssetData(16951);' class='asset-image' data-id='<?php echo $assetId16951; ?>'
                        data-room='<?php echo htmlspecialchars($room16951); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16951); ?>'
                        data-image='<?php echo base64_encode($upload_img16951); ?>'
                        data-status='<?php echo htmlspecialchars($status16951); ?>'
                        data-category='<?php echo htmlspecialchars($category16951); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16951); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16951); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16952 -->
                    <img src='../image.php?id=16952'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16952'
                        onclick='fetchAssetData(16952);' class='asset-image' data-id='<?php echo $assetId16952; ?>'
                        data-room='<?php echo htmlspecialchars($room16952); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16952); ?>'
                        data-image='<?php echo base64_encode($upload_img16952); ?>'
                        data-status='<?php echo htmlspecialchars($status16952); ?>'
                        data-category='<?php echo htmlspecialchars($category16952); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16952); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16952); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16953 -->
                    <img src='../image.php?id=16953'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16953'
                        onclick='fetchAssetData(16953);' class='asset-image' data-id='<?php echo $assetId16953; ?>'
                        data-room='<?php echo htmlspecialchars($room16953); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16953); ?>'
                        data-image='<?php echo base64_encode($upload_img16953); ?>'
                        data-status='<?php echo htmlspecialchars($status16953); ?>'
                        data-category='<?php echo htmlspecialchars($category16953); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16953); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16953); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16954 -->
                    <img src='../image.php?id=16954'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16954'
                        onclick='fetchAssetData(16954);' class='asset-image' data-id='<?php echo $assetId16954; ?>'
                        data-room='<?php echo htmlspecialchars($room16954); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16954); ?>'
                        data-image='<?php echo base64_encode($upload_img16954); ?>'
                        data-status='<?php echo htmlspecialchars($status16954); ?>'
                        data-category='<?php echo htmlspecialchars($category16954); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16954); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16954); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16955 -->
                    <img src='../image.php?id=16955'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16955'
                        onclick='fetchAssetData(16955);' class='asset-image' data-id='<?php echo $assetId16955; ?>'
                        data-room='<?php echo htmlspecialchars($room16955); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16955); ?>'
                        data-image='<?php echo base64_encode($upload_img16955); ?>'
                        data-status='<?php echo htmlspecialchars($status16955); ?>'
                        data-category='<?php echo htmlspecialchars($category16955); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16955); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16955); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16956 -->
                    <img src='../image.php?id=16956'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16956'
                        onclick='fetchAssetData(16956);' class='asset-image' data-id='<?php echo $assetId16956; ?>'
                        data-room='<?php echo htmlspecialchars($room16956); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16956); ?>'
                        data-image='<?php echo base64_encode($upload_img16956); ?>'
                        data-status='<?php echo htmlspecialchars($status16956); ?>'
                        data-category='<?php echo htmlspecialchars($category16956); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16956); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16956); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16957 -->
                    <img src='../image.php?id=16957'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16957'
                        onclick='fetchAssetData(16957);' class='asset-image' data-id='<?php echo $assetId16957; ?>'
                        data-room='<?php echo htmlspecialchars($room16957); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16957); ?>'
                        data-image='<?php echo base64_encode($upload_img16957); ?>'
                        data-status='<?php echo htmlspecialchars($status16957); ?>'
                        data-category='<?php echo htmlspecialchars($category16957); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16957); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16957); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16958 -->
                    <img src='../image.php?id=16958'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16958'
                        onclick='fetchAssetData(16958);' class='asset-image' data-id='<?php echo $assetId16958; ?>'
                        data-room='<?php echo htmlspecialchars($room16958); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16958); ?>'
                        data-image='<?php echo base64_encode($upload_img16958); ?>'
                        data-status='<?php echo htmlspecialchars($status16958); ?>'
                        data-category='<?php echo htmlspecialchars($category16958); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16958); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16958); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16959 -->
                    <img src='../image.php?id=16959'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16959'
                        onclick='fetchAssetData(16959);' class='asset-image' data-id='<?php echo $assetId16959; ?>'
                        data-room='<?php echo htmlspecialchars($room16959); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16959); ?>'
                        data-image='<?php echo base64_encode($upload_img16959); ?>'
                        data-status='<?php echo htmlspecialchars($status16959); ?>'
                        data-category='<?php echo htmlspecialchars($category16959); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16959); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16959); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16960 -->
                    <img src='../image.php?id=16960'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16960'
                        onclick='fetchAssetData(16960);' class='asset-image' data-id='<?php echo $assetId16960; ?>'
                        data-room='<?php echo htmlspecialchars($room16960); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16960); ?>'
                        data-image='<?php echo base64_encode($upload_img16960); ?>'
                        data-status='<?php echo htmlspecialchars($status16960); ?>'
                        data-category='<?php echo htmlspecialchars($category16960); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16960); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16960); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16961 -->
                    <img src='../image.php?id=16961'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16961'
                        onclick='fetchAssetData(16961);' class='asset-image' data-id='<?php echo $assetId16961; ?>'
                        data-room='<?php echo htmlspecialchars($room16961); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16961); ?>'
                        data-image='<?php echo base64_encode($upload_img16961); ?>'
                        data-status='<?php echo htmlspecialchars($status16961); ?>'
                        data-category='<?php echo htmlspecialchars($category16961); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16961); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16961); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16962 -->
                    <img src='../image.php?id=16962'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16962'
                        onclick='fetchAssetData(16962);' class='asset-image' data-id='<?php echo $assetId16962; ?>'
                        data-room='<?php echo htmlspecialchars($room16962); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16962); ?>'
                        data-image='<?php echo base64_encode($upload_img16962); ?>'
                        data-status='<?php echo htmlspecialchars($status16962); ?>'
                        data-category='<?php echo htmlspecialchars($category16962); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16962); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16962); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16963 -->
                    <img src='../image.php?id=16963'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16963'
                        onclick='fetchAssetData(16963);' class='asset-image' data-id='<?php echo $assetId16963; ?>'
                        data-room='<?php echo htmlspecialchars($room16963); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16963); ?>'
                        data-image='<?php echo base64_encode($upload_img16963); ?>'
                        data-status='<?php echo htmlspecialchars($status16963); ?>'
                        data-category='<?php echo htmlspecialchars($category16963); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16963); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16963); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16964 -->
                    <img src='../image.php?id=16964'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16964'
                        onclick='fetchAssetData(16964);' class='asset-image' data-id='<?php echo $assetId16964; ?>'
                        data-room='<?php echo htmlspecialchars($room16964); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16964); ?>'
                        data-image='<?php echo base64_encode($upload_img16964); ?>'
                        data-status='<?php echo htmlspecialchars($status16964); ?>'
                        data-category='<?php echo htmlspecialchars($category16964); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16964); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16964); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16965 -->
                    <img src='../image.php?id=16965'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16965'
                        onclick='fetchAssetData(16965);' class='asset-image' data-id='<?php echo $assetId16965; ?>'
                        data-room='<?php echo htmlspecialchars($room16965); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16965); ?>'
                        data-image='<?php echo base64_encode($upload_img16965); ?>'
                        data-status='<?php echo htmlspecialchars($status16965); ?>'
                        data-category='<?php echo htmlspecialchars($category16965); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16965); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16965); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16966 -->
                    <img src='../image.php?id=16966'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16966'
                        onclick='fetchAssetData(16966);' class='asset-image' data-id='<?php echo $assetId16966; ?>'
                        data-room='<?php echo htmlspecialchars($room16966); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16966); ?>'
                        data-image='<?php echo base64_encode($upload_img16966); ?>'
                        data-status='<?php echo htmlspecialchars($status16966); ?>'
                        data-category='<?php echo htmlspecialchars($category16966); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16966); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16966); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16967 -->
                    <img src='../image.php?id=16967'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16967'
                        onclick='fetchAssetData(16967);' class='asset-image' data-id='<?php echo $assetId16967; ?>'
                        data-room='<?php echo htmlspecialchars($room16967); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16967); ?>'
                        data-image='<?php echo base64_encode($upload_img16967); ?>'
                        data-status='<?php echo htmlspecialchars($status16967); ?>'
                        data-category='<?php echo htmlspecialchars($category16967); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16967); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16967); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16968 -->
                    <img src='../image.php?id=16968'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16968'
                        onclick='fetchAssetData(16968);' class='asset-image' data-id='<?php echo $assetId16968; ?>'
                        data-room='<?php echo htmlspecialchars($room16968); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16968); ?>'
                        data-image='<?php echo base64_encode($upload_img16968); ?>'
                        data-status='<?php echo htmlspecialchars($status16968); ?>'
                        data-category='<?php echo htmlspecialchars($category16968); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16968); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16968); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16969 -->
                    <img src='../image.php?id=16969'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16969'
                        onclick='fetchAssetData(16969);' class='asset-image' data-id='<?php echo $assetId16969; ?>'
                        data-room='<?php echo htmlspecialchars($room16969); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16969); ?>'
                        data-image='<?php echo base64_encode($upload_img16969); ?>'
                        data-status='<?php echo htmlspecialchars($status16969); ?>'
                        data-category='<?php echo htmlspecialchars($category16969); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16969); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16969); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16970 -->
                    <img src='../image.php?id=16970'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16970'
                        onclick='fetchAssetData(16970);' class='asset-image' data-id='<?php echo $assetId16970; ?>'
                        data-room='<?php echo htmlspecialchars($room16970); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16970); ?>'
                        data-image='<?php echo base64_encode($upload_img16970); ?>'
                        data-status='<?php echo htmlspecialchars($status16970); ?>'
                        data-category='<?php echo htmlspecialchars($category16970); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16970); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16970); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16971 -->
                    <img src='../image.php?id=16971'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16971'
                        onclick='fetchAssetData(16971);' class='asset-image' data-id='<?php echo $assetId16971; ?>'
                        data-room='<?php echo htmlspecialchars($room16971); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16971); ?>'
                        data-image='<?php echo base64_encode($upload_img16971); ?>'
                        data-status='<?php echo htmlspecialchars($status16971); ?>'
                        data-category='<?php echo htmlspecialchars($category16971); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16971); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16971); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16972 -->
                    <img src='../image.php?id=16972'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16972'
                        onclick='fetchAssetData(16972);' class='asset-image' data-id='<?php echo $assetId16972; ?>'
                        data-room='<?php echo htmlspecialchars($room16972); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16972); ?>'
                        data-image='<?php echo base64_encode($upload_img16972); ?>'
                        data-status='<?php echo htmlspecialchars($status16972); ?>'
                        data-category='<?php echo htmlspecialchars($category16972); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16972); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16972); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16973 -->
                    <img src='../image.php?id=16973'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16973'
                        onclick='fetchAssetData(16973);' class='asset-image' data-id='<?php echo $assetId16973; ?>'
                        data-room='<?php echo htmlspecialchars($room16973); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16973); ?>'
                        data-image='<?php echo base64_encode($upload_img16973); ?>'
                        data-status='<?php echo htmlspecialchars($status16973); ?>'
                        data-category='<?php echo htmlspecialchars($category16973); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16973); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16973); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16974 -->
                    <img src='../image.php?id=16974'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16974'
                        onclick='fetchAssetData(16974);' class='asset-image' data-id='<?php echo $assetId16974; ?>'
                        data-room='<?php echo htmlspecialchars($room16974); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16974); ?>'
                        data-image='<?php echo base64_encode($upload_img16974); ?>'
                        data-status='<?php echo htmlspecialchars($status16974); ?>'
                        data-category='<?php echo htmlspecialchars($category16974); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16974); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16974); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16975 -->
                    <img src='../image.php?id=16975'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16975'
                        onclick='fetchAssetData(16975);' class='asset-image' data-id='<?php echo $assetId16975; ?>'
                        data-room='<?php echo htmlspecialchars($room16975); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16975); ?>'
                        data-image='<?php echo base64_encode($upload_img16975); ?>'
                        data-status='<?php echo htmlspecialchars($status16975); ?>'
                        data-category='<?php echo htmlspecialchars($category16975); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16975); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16975); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16976 -->
                    <img src='../image.php?id=16976'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16976'
                        onclick='fetchAssetData(16976);' class='asset-image' data-id='<?php echo $assetId16976; ?>'
                        data-room='<?php echo htmlspecialchars($room16976); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16976); ?>'
                        data-image='<?php echo base64_encode($upload_img16976); ?>'
                        data-status='<?php echo htmlspecialchars($status16976); ?>'
                        data-category='<?php echo htmlspecialchars($category16976); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16976); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16976); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16977 -->
                    <img src='../image.php?id=16977'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16977'
                        onclick='fetchAssetData(16977);' class='asset-image' data-id='<?php echo $assetId16977; ?>'
                        data-room='<?php echo htmlspecialchars($room16977); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16977); ?>'
                        data-image='<?php echo base64_encode($upload_img16977); ?>'
                        data-status='<?php echo htmlspecialchars($status16977); ?>'
                        data-category='<?php echo htmlspecialchars($category16977); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16977); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16977); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16978 -->
                    <img src='../image.php?id=16978'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16978'
                        onclick='fetchAssetData(16978);' class='asset-image' data-id='<?php echo $assetId16978; ?>'
                        data-room='<?php echo htmlspecialchars($room16978); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16978); ?>'
                        data-image='<?php echo base64_encode($upload_img16978); ?>'
                        data-status='<?php echo htmlspecialchars($status16978); ?>'
                        data-category='<?php echo htmlspecialchars($category16978); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16978); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16978); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16979 -->
                    <img src='../image.php?id=16979'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16979'
                        onclick='fetchAssetData(16979);' class='asset-image' data-id='<?php echo $assetId16979; ?>'
                        data-room='<?php echo htmlspecialchars($room16979); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16979); ?>'
                        data-image='<?php echo base64_encode($upload_img16979); ?>'
                        data-status='<?php echo htmlspecialchars($status16979); ?>'
                        data-category='<?php echo htmlspecialchars($category16979); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16979); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16979); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16980 -->
                    <img src='../image.php?id=16980'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16980'
                        onclick='fetchAssetData(16980);' class='asset-image' data-id='<?php echo $assetId16980; ?>'
                        data-room='<?php echo htmlspecialchars($room16980); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16980); ?>'
                        data-image='<?php echo base64_encode($upload_img16980); ?>'
                        data-status='<?php echo htmlspecialchars($status16980); ?>'
                        data-category='<?php echo htmlspecialchars($category16980); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16980); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16980); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16981 -->
                    <img src='../image.php?id=16981'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16981'
                        onclick='fetchAssetData(16981);' class='asset-image' data-id='<?php echo $assetId16981; ?>'
                        data-room='<?php echo htmlspecialchars($room16981); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16981); ?>'
                        data-image='<?php echo base64_encode($upload_img16981); ?>'
                        data-status='<?php echo htmlspecialchars($status16981); ?>'
                        data-category='<?php echo htmlspecialchars($category16981); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16981); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16981); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16982 -->
                    <img src='../image.php?id=16982'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16982'
                        onclick='fetchAssetData(16982);' class='asset-image' data-id='<?php echo $assetId16982; ?>'
                        data-room='<?php echo htmlspecialchars($room16982); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16982); ?>'
                        data-image='<?php echo base64_encode($upload_img16982); ?>'
                        data-status='<?php echo htmlspecialchars($status16982); ?>'
                        data-category='<?php echo htmlspecialchars($category16982); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16982); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16982); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16983 -->
                    <img src='../image.php?id=16983'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16983'
                        onclick='fetchAssetData(16983);' class='asset-image' data-id='<?php echo $assetId16983; ?>'
                        data-room='<?php echo htmlspecialchars($room16983); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16983); ?>'
                        data-image='<?php echo base64_encode($upload_img16983); ?>'
                        data-status='<?php echo htmlspecialchars($status16983); ?>'
                        data-category='<?php echo htmlspecialchars($category16983); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16983); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16983); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16984 -->
                    <img src='../image.php?id=16984'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16984'
                        onclick='fetchAssetData(16984);' class='asset-image' data-id='<?php echo $assetId16984; ?>'
                        data-room='<?php echo htmlspecialchars($room16984); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16984); ?>'
                        data-image='<?php echo base64_encode($upload_img16984); ?>'
                        data-status='<?php echo htmlspecialchars($status16984); ?>'
                        data-category='<?php echo htmlspecialchars($category16984); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16984); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16984); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16985 -->
                    <img src='../image.php?id=16985'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16985'
                        onclick='fetchAssetData(16985);' class='asset-image' data-id='<?php echo $assetId16985; ?>'
                        data-room='<?php echo htmlspecialchars($room16985); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16985); ?>'
                        data-image='<?php echo base64_encode($upload_img16985); ?>'
                        data-status='<?php echo htmlspecialchars($status16985); ?>'
                        data-category='<?php echo htmlspecialchars($category16985); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16985); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16985); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16986 -->
                    <img src='../image.php?id=16986'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16986'
                        onclick='fetchAssetData(16986);' class='asset-image' data-id='<?php echo $assetId16986; ?>'
                        data-room='<?php echo htmlspecialchars($room16986); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16986); ?>'
                        data-image='<?php echo base64_encode($upload_img16986); ?>'
                        data-status='<?php echo htmlspecialchars($status16986); ?>'
                        data-category='<?php echo htmlspecialchars($category16986); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16986); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16986); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16987 -->
                    <img src='../image.php?id=16987'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16987'
                        onclick='fetchAssetData(16987);' class='asset-image' data-id='<?php echo $assetId16987; ?>'
                        data-room='<?php echo htmlspecialchars($room16987); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16987); ?>'
                        data-image='<?php echo base64_encode($upload_img16987); ?>'
                        data-status='<?php echo htmlspecialchars($status16987); ?>'
                        data-category='<?php echo htmlspecialchars($category16987); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16987); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16987); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16988 -->
                    <img src='../image.php?id=16988'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16988'
                        onclick='fetchAssetData(16988);' class='asset-image' data-id='<?php echo $assetId16988; ?>'
                        data-room='<?php echo htmlspecialchars($room16988); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16988); ?>'
                        data-image='<?php echo base64_encode($upload_img16988); ?>'
                        data-status='<?php echo htmlspecialchars($status16988); ?>'
                        data-category='<?php echo htmlspecialchars($category16988); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16988); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16988); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16989 -->
                    <img src='../image.php?id=16989'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16989'
                        onclick='fetchAssetData(16989);' class='asset-image' data-id='<?php echo $assetId16989; ?>'
                        data-room='<?php echo htmlspecialchars($room16989); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16989); ?>'
                        data-image='<?php echo base64_encode($upload_img16989); ?>'
                        data-status='<?php echo htmlspecialchars($status16989); ?>'
                        data-category='<?php echo htmlspecialchars($category16989); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16989); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16989); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16990 -->
                    <img src='../image.php?id=16990'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16990'
                        onclick='fetchAssetData(16990);' class='asset-image' data-id='<?php echo $assetId16990; ?>'
                        data-room='<?php echo htmlspecialchars($room16990); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16990); ?>'
                        data-image='<?php echo base64_encode($upload_img16990); ?>'
                        data-status='<?php echo htmlspecialchars($status16990); ?>'
                        data-category='<?php echo htmlspecialchars($category16990); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16990); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16990); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16991 -->
                    <img src='../image.php?id=16991'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16991'
                        onclick='fetchAssetData(16991);' class='asset-image' data-id='<?php echo $assetId16991; ?>'
                        data-room='<?php echo htmlspecialchars($room16991); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16991); ?>'
                        data-image='<?php echo base64_encode($upload_img16991); ?>'
                        data-status='<?php echo htmlspecialchars($status16991); ?>'
                        data-category='<?php echo htmlspecialchars($category16991); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16991); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16991); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16992 -->
                    <img src='../image.php?id=16992'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16992'
                        onclick='fetchAssetData(16992);' class='asset-image' data-id='<?php echo $assetId16992; ?>'
                        data-room='<?php echo htmlspecialchars($room16992); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16992); ?>'
                        data-image='<?php echo base64_encode($upload_img16992); ?>'
                        data-status='<?php echo htmlspecialchars($status16992); ?>'
                        data-category='<?php echo htmlspecialchars($category16992); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16992); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16992); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16993 -->
                    <img src='../image.php?id=16993'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16993'
                        onclick='fetchAssetData(16993);' class='asset-image' data-id='<?php echo $assetId16993; ?>'
                        data-room='<?php echo htmlspecialchars($room16993); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16993); ?>'
                        data-image='<?php echo base64_encode($upload_img16993); ?>'
                        data-status='<?php echo htmlspecialchars($status16993); ?>'
                        data-category='<?php echo htmlspecialchars($category16993); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16993); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16993); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16994 -->
                    <img src='../image.php?id=16994'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16994'
                        onclick='fetchAssetData(16994);' class='asset-image' data-id='<?php echo $assetId16994; ?>'
                        data-room='<?php echo htmlspecialchars($room16994); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16994); ?>'
                        data-image='<?php echo base64_encode($upload_img16994); ?>'
                        data-status='<?php echo htmlspecialchars($status16994); ?>'
                        data-category='<?php echo htmlspecialchars($category16994); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16994); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16994); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16995 -->
                    <img src='../image.php?id=16995'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16995'
                        onclick='fetchAssetData(16995);' class='asset-image' data-id='<?php echo $assetId16995; ?>'
                        data-room='<?php echo htmlspecialchars($room16995); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16995); ?>'
                        data-image='<?php echo base64_encode($upload_img16995); ?>'
                        data-status='<?php echo htmlspecialchars($status16995); ?>'
                        data-category='<?php echo htmlspecialchars($category16995); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16995); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16995); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16996 -->
                    <img src='../image.php?id=16996'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16996'
                        onclick='fetchAssetData(16996);' class='asset-image' data-id='<?php echo $assetId16996; ?>'
                        data-room='<?php echo htmlspecialchars($room16996); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16996); ?>'
                        data-image='<?php echo base64_encode($upload_img16996); ?>'
                        data-status='<?php echo htmlspecialchars($status16996); ?>'
                        data-category='<?php echo htmlspecialchars($category16996); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16996); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16996); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16997 -->
                    <img src='../image.php?id=16997'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16997'
                        onclick='fetchAssetData(16997);' class='asset-image' data-id='<?php echo $assetId16997; ?>'
                        data-room='<?php echo htmlspecialchars($room16997); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16997); ?>'
                        data-image='<?php echo base64_encode($upload_img16997); ?>'
                        data-status='<?php echo htmlspecialchars($status16997); ?>'
                        data-category='<?php echo htmlspecialchars($category16997); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16997); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16997); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16998 -->
                    <img src='../image.php?id=16998'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16998'
                        onclick='fetchAssetData(16998);' class='asset-image' data-id='<?php echo $assetId16998; ?>'
                        data-room='<?php echo htmlspecialchars($room16998); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16998); ?>'
                        data-image='<?php echo base64_encode($upload_img16998); ?>'
                        data-status='<?php echo htmlspecialchars($status16998); ?>'
                        data-category='<?php echo htmlspecialchars($category16998); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16998); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16998); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 16999 -->
                    <img src='../image.php?id=16999'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16999'
                        onclick='fetchAssetData(16999);' class='asset-image' data-id='<?php echo $assetId16999; ?>'
                        data-room='<?php echo htmlspecialchars($room16999); ?>'
                        data-floor='<?php echo htmlspecialchars($floor16999); ?>'
                        data-image='<?php echo base64_encode($upload_img16999); ?>'
                        data-status='<?php echo htmlspecialchars($status16999); ?>'
                        data-category='<?php echo htmlspecialchars($category16999); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName16999); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16999); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17000 -->
                    <img src='../image.php?id=17000'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17000'
                        onclick='fetchAssetData(17000);' class='asset-image' data-id='<?php echo $assetId17000; ?>'
                        data-room='<?php echo htmlspecialchars($room17000); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17000); ?>'
                        data-image='<?php echo base64_encode($upload_img17000); ?>'
                        data-status='<?php echo htmlspecialchars($status17000); ?>'
                        data-category='<?php echo htmlspecialchars($category17000); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17000); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17000); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17001 -->
                    <img src='../image.php?id=17001'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17001'
                        onclick='fetchAssetData(17001);' class='asset-image' data-id='<?php echo $assetId17001; ?>'
                        data-room='<?php echo htmlspecialchars($room17001); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17001); ?>'
                        data-image='<?php echo base64_encode($upload_img17001); ?>'
                        data-status='<?php echo htmlspecialchars($status17001); ?>'
                        data-category='<?php echo htmlspecialchars($category17001); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17001); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17001); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17002 -->
                    <img src='../image.php?id=17002'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17002'
                        onclick='fetchAssetData(17002);' class='asset-image' data-id='<?php echo $assetId17002; ?>'
                        data-room='<?php echo htmlspecialchars($room17002); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17002); ?>'
                        data-image='<?php echo base64_encode($upload_img17002); ?>'
                        data-status='<?php echo htmlspecialchars($status17002); ?>'
                        data-category='<?php echo htmlspecialchars($category17002); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17002); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17002); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17003 -->
                    <img src='../image.php?id=17003'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17003'
                        onclick='fetchAssetData(17003);' class='asset-image' data-id='<?php echo $assetId17003; ?>'
                        data-room='<?php echo htmlspecialchars($room17003); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17003); ?>'
                        data-image='<?php echo base64_encode($upload_img17003); ?>'
                        data-status='<?php echo htmlspecialchars($status17003); ?>'
                        data-category='<?php echo htmlspecialchars($category17003); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17003); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17003); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17004 -->
                    <img src='../image.php?id=17004'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17004'
                        onclick='fetchAssetData(17004);' class='asset-image' data-id='<?php echo $assetId17004; ?>'
                        data-room='<?php echo htmlspecialchars($room17004); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17004); ?>'
                        data-image='<?php echo base64_encode($upload_img17004); ?>'
                        data-status='<?php echo htmlspecialchars($status17004); ?>'
                        data-category='<?php echo htmlspecialchars($category17004); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17004); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17004); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17005 -->
                    <img src='../image.php?id=17005'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17005'
                        onclick='fetchAssetData(17005);' class='asset-image' data-id='<?php echo $assetId17005; ?>'
                        data-room='<?php echo htmlspecialchars($room17005); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17005); ?>'
                        data-image='<?php echo base64_encode($upload_img17005); ?>'
                        data-status='<?php echo htmlspecialchars($status17005); ?>'
                        data-category='<?php echo htmlspecialchars($category17005); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17005); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17005); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17006 -->
                    <img src='../image.php?id=17006'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17006'
                        onclick='fetchAssetData(17006);' class='asset-image' data-id='<?php echo $assetId17006; ?>'
                        data-room='<?php echo htmlspecialchars($room17006); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17006); ?>'
                        data-image='<?php echo base64_encode($upload_img17006); ?>'
                        data-status='<?php echo htmlspecialchars($status17006); ?>'
                        data-category='<?php echo htmlspecialchars($category17006); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17006); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17006); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17007 -->
                    <img src='../image.php?id=17007'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17007'
                        onclick='fetchAssetData(17007);' class='asset-image' data-id='<?php echo $assetId17007; ?>'
                        data-room='<?php echo htmlspecialchars($room17007); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17007); ?>'
                        data-image='<?php echo base64_encode($upload_img17007); ?>'
                        data-status='<?php echo htmlspecialchars($status17007); ?>'
                        data-category='<?php echo htmlspecialchars($category17007); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17007); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17007); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17008 -->
                    <img src='../image.php?id=17008'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17008'
                        onclick='fetchAssetData(17008);' class='asset-image' data-id='<?php echo $assetId17008; ?>'
                        data-room='<?php echo htmlspecialchars($room17008); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17008); ?>'
                        data-image='<?php echo base64_encode($upload_img17008); ?>'
                        data-status='<?php echo htmlspecialchars($status17008); ?>'
                        data-category='<?php echo htmlspecialchars($category17008); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17008); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17008); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17009 -->
                    <img src='../image.php?id=17009'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17009'
                        onclick='fetchAssetData(17009);' class='asset-image' data-id='<?php echo $assetId17009; ?>'
                        data-room='<?php echo htmlspecialchars($room17009); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17009); ?>'
                        data-image='<?php echo base64_encode($upload_img17009); ?>'
                        data-status='<?php echo htmlspecialchars($status17009); ?>'
                        data-category='<?php echo htmlspecialchars($category17009); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17009); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17009); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17010 -->
                    <img src='../image.php?id=17010'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17010'
                        onclick='fetchAssetData(17010);' class='asset-image' data-id='<?php echo $assetId17010; ?>'
                        data-room='<?php echo htmlspecialchars($room17010); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17010); ?>'
                        data-image='<?php echo base64_encode($upload_img17010); ?>'
                        data-status='<?php echo htmlspecialchars($status17010); ?>'
                        data-category='<?php echo htmlspecialchars($category17010); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17010); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17010); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17011 -->
                    <img src='../image.php?id=17011'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17011'
                        onclick='fetchAssetData(17011);' class='asset-image' data-id='<?php echo $assetId17011; ?>'
                        data-room='<?php echo htmlspecialchars($room17011); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17011); ?>'
                        data-image='<?php echo base64_encode($upload_img17011); ?>'
                        data-status='<?php echo htmlspecialchars($status17011); ?>'
                        data-category='<?php echo htmlspecialchars($category17011); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17011); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17011); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17012 -->
                    <img src='../image.php?id=17012'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17012'
                        onclick='fetchAssetData(17012);' class='asset-image' data-id='<?php echo $assetId17012; ?>'
                        data-room='<?php echo htmlspecialchars($room17012); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17012); ?>'
                        data-image='<?php echo base64_encode($upload_img17012); ?>'
                        data-status='<?php echo htmlspecialchars($status17012); ?>'
                        data-category='<?php echo htmlspecialchars($category17012); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17012); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17012); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17013 -->
                    <img src='../image.php?id=17013'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17013'
                        onclick='fetchAssetData(17013);' class='asset-image' data-id='<?php echo $assetId17013; ?>'
                        data-room='<?php echo htmlspecialchars($room17013); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17013); ?>'
                        data-image='<?php echo base64_encode($upload_img17013); ?>'
                        data-status='<?php echo htmlspecialchars($status17013); ?>'
                        data-category='<?php echo htmlspecialchars($category17013); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17013); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17013); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17014 -->
                    <img src='../image.php?id=17014'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17014'
                        onclick='fetchAssetData(17014);' class='asset-image' data-id='<?php echo $assetId17014; ?>'
                        data-room='<?php echo htmlspecialchars($room17014); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17014); ?>'
                        data-image='<?php echo base64_encode($upload_img17014); ?>'
                        data-status='<?php echo htmlspecialchars($status17014); ?>'
                        data-category='<?php echo htmlspecialchars($category17014); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17014); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17014); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17015 -->
                    <img src='../image.php?id=17015'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17015'
                        onclick='fetchAssetData(17015);' class='asset-image' data-id='<?php echo $assetId17015; ?>'
                        data-room='<?php echo htmlspecialchars($room17015); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17015); ?>'
                        data-image='<?php echo base64_encode($upload_img17015); ?>'
                        data-status='<?php echo htmlspecialchars($status17015); ?>'
                        data-category='<?php echo htmlspecialchars($category17015); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17015); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17015); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17016 -->
                    <img src='../image.php?id=17016'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17016'
                        onclick='fetchAssetData(17016);' class='asset-image' data-id='<?php echo $assetId17016; ?>'
                        data-room='<?php echo htmlspecialchars($room17016); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17016); ?>'
                        data-image='<?php echo base64_encode($upload_img17016); ?>'
                        data-status='<?php echo htmlspecialchars($status17016); ?>'
                        data-category='<?php echo htmlspecialchars($category17016); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17016); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17016); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17017 -->
                    <img src='../image.php?id=17017'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17017'
                        onclick='fetchAssetData(17017);' class='asset-image' data-id='<?php echo $assetId17017; ?>'
                        data-room='<?php echo htmlspecialchars($room17017); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17017); ?>'
                        data-image='<?php echo base64_encode($upload_img17017); ?>'
                        data-status='<?php echo htmlspecialchars($status17017); ?>'
                        data-category='<?php echo htmlspecialchars($category17017); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17017); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17017); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17018 -->
                    <img src='../image.php?id=17018'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17018'
                        onclick='fetchAssetData(17018);' class='asset-image' data-id='<?php echo $assetId17018; ?>'
                        data-room='<?php echo htmlspecialchars($room17018); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17018); ?>'
                        data-image='<?php echo base64_encode($upload_img17018); ?>'
                        data-status='<?php echo htmlspecialchars($status17018); ?>'
                        data-category='<?php echo htmlspecialchars($category17018); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17018); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17018); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17019 -->
                    <img src='../image.php?id=17019'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17019'
                        onclick='fetchAssetData(17019);' class='asset-image' data-id='<?php echo $assetId17019; ?>'
                        data-room='<?php echo htmlspecialchars($room17019); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17019); ?>'
                        data-image='<?php echo base64_encode($upload_img17019); ?>'
                        data-status='<?php echo htmlspecialchars($status17019); ?>'
                        data-category='<?php echo htmlspecialchars($category17019); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17019); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17019); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17020 -->
                    <img src='../image.php?id=17020'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17020'
                        onclick='fetchAssetData(17020);' class='asset-image' data-id='<?php echo $assetId17020; ?>'
                        data-room='<?php echo htmlspecialchars($room17020); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17020); ?>'
                        data-image='<?php echo base64_encode($upload_img17020); ?>'
                        data-status='<?php echo htmlspecialchars($status17020); ?>'
                        data-category='<?php echo htmlspecialchars($category17020); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17020); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17020); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17021 -->
                    <img src='../image.php?id=17021'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17021'
                        onclick='fetchAssetData(17021);' class='asset-image' data-id='<?php echo $assetId17021; ?>'
                        data-room='<?php echo htmlspecialchars($room17021); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17021); ?>'
                        data-image='<?php echo base64_encode($upload_img17021); ?>'
                        data-status='<?php echo htmlspecialchars($status17021); ?>'
                        data-category='<?php echo htmlspecialchars($category17021); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17021); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17021); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17022 -->
                    <img src='../image.php?id=17022'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17022'
                        onclick='fetchAssetData(17022);' class='asset-image' data-id='<?php echo $assetId17022; ?>'
                        data-room='<?php echo htmlspecialchars($room17022); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17022); ?>'
                        data-image='<?php echo base64_encode($upload_img17022); ?>'
                        data-status='<?php echo htmlspecialchars($status17022); ?>'
                        data-category='<?php echo htmlspecialchars($category17022); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17022); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17022); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17023 -->
                    <img src='../image.php?id=17023'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17023'
                        onclick='fetchAssetData(17023);' class='asset-image' data-id='<?php echo $assetId17023; ?>'
                        data-room='<?php echo htmlspecialchars($room17023); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17023); ?>'
                        data-image='<?php echo base64_encode($upload_img17023); ?>'
                        data-status='<?php echo htmlspecialchars($status17023); ?>'
                        data-category='<?php echo htmlspecialchars($category17023); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17023); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17023); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17024 -->
                    <img src='../image.php?id=17024'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17024'
                        onclick='fetchAssetData(17024);' class='asset-image' data-id='<?php echo $assetId17024; ?>'
                        data-room='<?php echo htmlspecialchars($room17024); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17024); ?>'
                        data-image='<?php echo base64_encode($upload_img17024); ?>'
                        data-status='<?php echo htmlspecialchars($status17024); ?>'
                        data-category='<?php echo htmlspecialchars($category17024); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17024); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17024); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17025 -->
                    <img src='../image.php?id=17025'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17025'
                        onclick='fetchAssetData(17025);' class='asset-image' data-id='<?php echo $assetId17025; ?>'
                        data-room='<?php echo htmlspecialchars($room17025); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17025); ?>'
                        data-image='<?php echo base64_encode($upload_img17025); ?>'
                        data-status='<?php echo htmlspecialchars($status17025); ?>'
                        data-category='<?php echo htmlspecialchars($category17025); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17025); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17025); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17026 -->
                    <img src='../image.php?id=17026'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17026'
                        onclick='fetchAssetData(17026);' class='asset-image' data-id='<?php echo $assetId17026; ?>'
                        data-room='<?php echo htmlspecialchars($room17026); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17026); ?>'
                        data-image='<?php echo base64_encode($upload_img17026); ?>'
                        data-status='<?php echo htmlspecialchars($status17026); ?>'
                        data-category='<?php echo htmlspecialchars($category17026); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17026); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17026); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17027 -->
                    <img src='../image.php?id=17027'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17027'
                        onclick='fetchAssetData(17027);' class='asset-image' data-id='<?php echo $assetId17027; ?>'
                        data-room='<?php echo htmlspecialchars($room17027); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17027); ?>'
                        data-image='<?php echo base64_encode($upload_img17027); ?>'
                        data-status='<?php echo htmlspecialchars($status17027); ?>'
                        data-category='<?php echo htmlspecialchars($category17027); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17027); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17027); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17028 -->
                    <img src='../image.php?id=17028'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17028'
                        onclick='fetchAssetData(17028);' class='asset-image' data-id='<?php echo $assetId17028; ?>'
                        data-room='<?php echo htmlspecialchars($room17028); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17028); ?>'
                        data-image='<?php echo base64_encode($upload_img17028); ?>'
                        data-status='<?php echo htmlspecialchars($status17028); ?>'
                        data-category='<?php echo htmlspecialchars($category17028); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17028); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17028); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17029 -->
                    <img src='../image.php?id=17029'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17029'
                        onclick='fetchAssetData(17029);' class='asset-image' data-id='<?php echo $assetId17029; ?>'
                        data-room='<?php echo htmlspecialchars($room17029); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17029); ?>'
                        data-image='<?php echo base64_encode($upload_img17029); ?>'
                        data-status='<?php echo htmlspecialchars($status17029); ?>'
                        data-category='<?php echo htmlspecialchars($category17029); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17029); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17029); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17030 -->
                    <img src='../image.php?id=17030'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17030'
                        onclick='fetchAssetData(17030);' class='asset-image' data-id='<?php echo $assetId17030; ?>'
                        data-room='<?php echo htmlspecialchars($room17030); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17030); ?>'
                        data-image='<?php echo base64_encode($upload_img17030); ?>'
                        data-status='<?php echo htmlspecialchars($status17030); ?>'
                        data-category='<?php echo htmlspecialchars($category17030); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17030); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17030); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17031 -->
                    <img src='../image.php?id=17031'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17031'
                        onclick='fetchAssetData(17031);' class='asset-image' data-id='<?php echo $assetId17031; ?>'
                        data-room='<?php echo htmlspecialchars($room17031); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17031); ?>'
                        data-image='<?php echo base64_encode($upload_img17031); ?>'
                        data-status='<?php echo htmlspecialchars($status17031); ?>'
                        data-category='<?php echo htmlspecialchars($category17031); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17031); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17031); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17032 -->
                    <img src='../image.php?id=17032'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17032'
                        onclick='fetchAssetData(17032);' class='asset-image' data-id='<?php echo $assetId17032; ?>'
                        data-room='<?php echo htmlspecialchars($room17032); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17032); ?>'
                        data-image='<?php echo base64_encode($upload_img17032); ?>'
                        data-status='<?php echo htmlspecialchars($status17032); ?>'
                        data-category='<?php echo htmlspecialchars($category17032); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17032); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17032); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17033 -->
                    <img src='../image.php?id=17033'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17033'
                        onclick='fetchAssetData(17033);' class='asset-image' data-id='<?php echo $assetId17033; ?>'
                        data-room='<?php echo htmlspecialchars($room17033); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17033); ?>'
                        data-image='<?php echo base64_encode($upload_img17033); ?>'
                        data-status='<?php echo htmlspecialchars($status17033); ?>'
                        data-category='<?php echo htmlspecialchars($category17033); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17033); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17033); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17034 -->
                    <img src='../image.php?id=17034'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17034'
                        onclick='fetchAssetData(17034);' class='asset-image' data-id='<?php echo $assetId17034; ?>'
                        data-room='<?php echo htmlspecialchars($room17034); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17034); ?>'
                        data-image='<?php echo base64_encode($upload_img17034); ?>'
                        data-status='<?php echo htmlspecialchars($status17034); ?>'
                        data-category='<?php echo htmlspecialchars($category17034); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17034); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17034); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17035 -->
                    <img src='../image.php?id=17035'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17035'
                        onclick='fetchAssetData(17035);' class='asset-image' data-id='<?php echo $assetId17035; ?>'
                        data-room='<?php echo htmlspecialchars($room17035); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17035); ?>'
                        data-image='<?php echo base64_encode($upload_img17035); ?>'
                        data-status='<?php echo htmlspecialchars($status17035); ?>'
                        data-category='<?php echo htmlspecialchars($category17035); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17035); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17035); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17036 -->
                    <img src='../image.php?id=17036'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17036'
                        onclick='fetchAssetData(17036);' class='asset-image' data-id='<?php echo $assetId17036; ?>'
                        data-room='<?php echo htmlspecialchars($room17036); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17036); ?>'
                        data-image='<?php echo base64_encode($upload_img17036); ?>'
                        data-status='<?php echo htmlspecialchars($status17036); ?>'
                        data-category='<?php echo htmlspecialchars($category17036); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17036); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17036); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17037 -->
                    <img src='../image.php?id=17037'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17037'
                        onclick='fetchAssetData(17037);' class='asset-image' data-id='<?php echo $assetId17037; ?>'
                        data-room='<?php echo htmlspecialchars($room17037); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17037); ?>'
                        data-image='<?php echo base64_encode($upload_img17037); ?>'
                        data-status='<?php echo htmlspecialchars($status17037); ?>'
                        data-category='<?php echo htmlspecialchars($category17037); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17037); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17037); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17038 -->
                    <img src='../image.php?id=17038'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17038'
                        onclick='fetchAssetData(17038);' class='asset-image' data-id='<?php echo $assetId17038; ?>'
                        data-room='<?php echo htmlspecialchars($room17038); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17038); ?>'
                        data-image='<?php echo base64_encode($upload_img17038); ?>'
                        data-status='<?php echo htmlspecialchars($status17038); ?>'
                        data-category='<?php echo htmlspecialchars($category17038); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17038); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17038); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17039 -->
                    <img src='../image.php?id=17039'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17039'
                        onclick='fetchAssetData(17039);' class='asset-image' data-id='<?php echo $assetId17039; ?>'
                        data-room='<?php echo htmlspecialchars($room17039); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17039); ?>'
                        data-image='<?php echo base64_encode($upload_img17039); ?>'
                        data-status='<?php echo htmlspecialchars($status17039); ?>'
                        data-category='<?php echo htmlspecialchars($category17039); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17039); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17039); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17040 -->
                    <img src='../image.php?id=17040'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17040'
                        onclick='fetchAssetData(17040);' class='asset-image' data-id='<?php echo $assetId17040; ?>'
                        data-room='<?php echo htmlspecialchars($room17040); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17040); ?>'
                        data-image='<?php echo base64_encode($upload_img17040); ?>'
                        data-status='<?php echo htmlspecialchars($status17040); ?>'
                        data-category='<?php echo htmlspecialchars($category17040); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17040); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17040); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17041 -->
                    <img src='../image.php?id=17041'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17041'
                        onclick='fetchAssetData(17041);' class='asset-image' data-id='<?php echo $assetId17041; ?>'
                        data-room='<?php echo htmlspecialchars($room17041); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17041); ?>'
                        data-image='<?php echo base64_encode($upload_img17041); ?>'
                        data-status='<?php echo htmlspecialchars($status17041); ?>'
                        data-category='<?php echo htmlspecialchars($category17041); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17041); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17041); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17042 -->
                    <img src='../image.php?id=17042'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17042'
                        onclick='fetchAssetData(17042);' class='asset-image' data-id='<?php echo $assetId17042; ?>'
                        data-room='<?php echo htmlspecialchars($room17042); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17042); ?>'
                        data-image='<?php echo base64_encode($upload_img17042); ?>'
                        data-status='<?php echo htmlspecialchars($status17042); ?>'
                        data-category='<?php echo htmlspecialchars($category17042); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17042); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17042); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17043 -->
                    <img src='../image.php?id=17043'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17043'
                        onclick='fetchAssetData(17043);' class='asset-image' data-id='<?php echo $assetId17043; ?>'
                        data-room='<?php echo htmlspecialchars($room17043); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17043); ?>'
                        data-image='<?php echo base64_encode($upload_img17043); ?>'
                        data-status='<?php echo htmlspecialchars($status17043); ?>'
                        data-category='<?php echo htmlspecialchars($category17043); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17043); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17043); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17044 -->
                    <img src='../image.php?id=17044'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17044'
                        onclick='fetchAssetData(17044);' class='asset-image' data-id='<?php echo $assetId17044; ?>'
                        data-room='<?php echo htmlspecialchars($room17044); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17044); ?>'
                        data-image='<?php echo base64_encode($upload_img17044); ?>'
                        data-status='<?php echo htmlspecialchars($status17044); ?>'
                        data-category='<?php echo htmlspecialchars($category17044); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17044); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17044); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17045 -->
                    <img src='../image.php?id=17045'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17045'
                        onclick='fetchAssetData(17045);' class='asset-image' data-id='<?php echo $assetId17045; ?>'
                        data-room='<?php echo htmlspecialchars($room17045); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17045); ?>'
                        data-image='<?php echo base64_encode($upload_img17045); ?>'
                        data-status='<?php echo htmlspecialchars($status17045); ?>'
                        data-category='<?php echo htmlspecialchars($category17045); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17045); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17045); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17046 -->
                    <img src='../image.php?id=17046'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17046'
                        onclick='fetchAssetData(17046);' class='asset-image' data-id='<?php echo $assetId17046; ?>'
                        data-room='<?php echo htmlspecialchars($room17046); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17046); ?>'
                        data-image='<?php echo base64_encode($upload_img17046); ?>'
                        data-status='<?php echo htmlspecialchars($status17046); ?>'
                        data-category='<?php echo htmlspecialchars($category17046); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17046); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17046); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17047 -->
                    <img src='../image.php?id=17047'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17047'
                        onclick='fetchAssetData(17047);' class='asset-image' data-id='<?php echo $assetId17047; ?>'
                        data-room='<?php echo htmlspecialchars($room17047); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17047); ?>'
                        data-image='<?php echo base64_encode($upload_img17047); ?>'
                        data-status='<?php echo htmlspecialchars($status17047); ?>'
                        data-category='<?php echo htmlspecialchars($category17047); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17047); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17047); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17048 -->
                    <img src='../image.php?id=17048'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17048'
                        onclick='fetchAssetData(17048);' class='asset-image' data-id='<?php echo $assetId17048; ?>'
                        data-room='<?php echo htmlspecialchars($room17048); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17048); ?>'
                        data-image='<?php echo base64_encode($upload_img17048); ?>'
                        data-status='<?php echo htmlspecialchars($status17048); ?>'
                        data-category='<?php echo htmlspecialchars($category17048); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17048); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17048); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17049 -->
                    <img src='../image.php?id=17049'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17049'
                        onclick='fetchAssetData(17049);' class='asset-image' data-id='<?php echo $assetId17049; ?>'
                        data-room='<?php echo htmlspecialchars($room17049); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17049); ?>'
                        data-image='<?php echo base64_encode($upload_img17049); ?>'
                        data-status='<?php echo htmlspecialchars($status17049); ?>'
                        data-category='<?php echo htmlspecialchars($category17049); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17049); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17049); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17050 -->
                    <img src='../image.php?id=17050'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17050'
                        onclick='fetchAssetData(17050);' class='asset-image' data-id='<?php echo $assetId17050; ?>'
                        data-room='<?php echo htmlspecialchars($room17050); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17050); ?>'
                        data-image='<?php echo base64_encode($upload_img17050); ?>'
                        data-status='<?php echo htmlspecialchars($status17050); ?>'
                        data-category='<?php echo htmlspecialchars($category17050); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17050); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17050); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17051 -->
                    <img src='../image.php?id=17051'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17051'
                        onclick='fetchAssetData(17051);' class='asset-image' data-id='<?php echo $assetId17051; ?>'
                        data-room='<?php echo htmlspecialchars($room17051); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17051); ?>'
                        data-image='<?php echo base64_encode($upload_img17051); ?>'
                        data-status='<?php echo htmlspecialchars($status17051); ?>'
                        data-category='<?php echo htmlspecialchars($category17051); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17051); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17051); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17052 -->
                    <img src='../image.php?id=17052'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17052'
                        onclick='fetchAssetData(17052);' class='asset-image' data-id='<?php echo $assetId17052; ?>'
                        data-room='<?php echo htmlspecialchars($room17052); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17052); ?>'
                        data-image='<?php echo base64_encode($upload_img17052); ?>'
                        data-status='<?php echo htmlspecialchars($status17052); ?>'
                        data-category='<?php echo htmlspecialchars($category17052); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17052); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17052); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17053 -->
                    <img src='../image.php?id=17053'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17053'
                        onclick='fetchAssetData(17053);' class='asset-image' data-id='<?php echo $assetId17053; ?>'
                        data-room='<?php echo htmlspecialchars($room17053); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17053); ?>'
                        data-image='<?php echo base64_encode($upload_img17053); ?>'
                        data-status='<?php echo htmlspecialchars($status17053); ?>'
                        data-category='<?php echo htmlspecialchars($category17053); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17053); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17053); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17054 -->
                    <img src='../image.php?id=17054'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17054'
                        onclick='fetchAssetData(17054);' class='asset-image' data-id='<?php echo $assetId17054; ?>'
                        data-room='<?php echo htmlspecialchars($room17054); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17054); ?>'
                        data-image='<?php echo base64_encode($upload_img17054); ?>'
                        data-status='<?php echo htmlspecialchars($status17054); ?>'
                        data-category='<?php echo htmlspecialchars($category17054); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17054); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17054); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17055 -->
                    <img src='../image.php?id=17055'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17055'
                        onclick='fetchAssetData(17055);' class='asset-image' data-id='<?php echo $assetId17055; ?>'
                        data-room='<?php echo htmlspecialchars($room17055); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17055); ?>'
                        data-image='<?php echo base64_encode($upload_img17055); ?>'
                        data-status='<?php echo htmlspecialchars($status17055); ?>'
                        data-category='<?php echo htmlspecialchars($category17055); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17055); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17055); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17056 -->
                    <img src='../image.php?id=17056'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17056'
                        onclick='fetchAssetData(17056);' class='asset-image' data-id='<?php echo $assetId17056; ?>'
                        data-room='<?php echo htmlspecialchars($room17056); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17056); ?>'
                        data-image='<?php echo base64_encode($upload_img17056); ?>'
                        data-status='<?php echo htmlspecialchars($status17056); ?>'
                        data-category='<?php echo htmlspecialchars($category17056); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17056); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17056); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17057 -->
                    <img src='../image.php?id=17057'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17057'
                        onclick='fetchAssetData(17057);' class='asset-image' data-id='<?php echo $assetId17057; ?>'
                        data-room='<?php echo htmlspecialchars($room17057); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17057); ?>'
                        data-image='<?php echo base64_encode($upload_img17057); ?>'
                        data-status='<?php echo htmlspecialchars($status17057); ?>'
                        data-category='<?php echo htmlspecialchars($category17057); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17057); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17057); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17058 -->
                    <img src='../image.php?id=17058'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17058'
                        onclick='fetchAssetData(17058);' class='asset-image' data-id='<?php echo $assetId17058; ?>'
                        data-room='<?php echo htmlspecialchars($room17058); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17058); ?>'
                        data-image='<?php echo base64_encode($upload_img17058); ?>'
                        data-status='<?php echo htmlspecialchars($status17058); ?>'
                        data-category='<?php echo htmlspecialchars($category17058); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17058); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17058); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17059 -->
                    <img src='../image.php?id=17059'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17059'
                        onclick='fetchAssetData(17059);' class='asset-image' data-id='<?php echo $assetId17059; ?>'
                        data-room='<?php echo htmlspecialchars($room17059); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17059); ?>'
                        data-image='<?php echo base64_encode($upload_img17059); ?>'
                        data-status='<?php echo htmlspecialchars($status17059); ?>'
                        data-category='<?php echo htmlspecialchars($category17059); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17059); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17059); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17060 -->
                    <img src='../image.php?id=17060'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17060'
                        onclick='fetchAssetData(17060);' class='asset-image' data-id='<?php echo $assetId17060; ?>'
                        data-room='<?php echo htmlspecialchars($room17060); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17060); ?>'
                        data-image='<?php echo base64_encode($upload_img17060); ?>'
                        data-status='<?php echo htmlspecialchars($status17060); ?>'
                        data-category='<?php echo htmlspecialchars($category17060); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17060); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17060); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17061 -->
                    <img src='../image.php?id=17061'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17061'
                        onclick='fetchAssetData(17061);' class='asset-image' data-id='<?php echo $assetId17061; ?>'
                        data-room='<?php echo htmlspecialchars($room17061); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17061); ?>'
                        data-image='<?php echo base64_encode($upload_img17061); ?>'
                        data-status='<?php echo htmlspecialchars($status17061); ?>'
                        data-category='<?php echo htmlspecialchars($category17061); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17061); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17061); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17062 -->
                    <img src='../image.php?id=17062'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17062'
                        onclick='fetchAssetData(17062);' class='asset-image' data-id='<?php echo $assetId17062; ?>'
                        data-room='<?php echo htmlspecialchars($room17062); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17062); ?>'
                        data-image='<?php echo base64_encode($upload_img17062); ?>'
                        data-status='<?php echo htmlspecialchars($status17062); ?>'
                        data-category='<?php echo htmlspecialchars($category17062); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17062); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17062); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17063 -->
                    <img src='../image.php?id=17063'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17063'
                        onclick='fetchAssetData(17063);' class='asset-image' data-id='<?php echo $assetId17063; ?>'
                        data-room='<?php echo htmlspecialchars($room17063); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17063); ?>'
                        data-image='<?php echo base64_encode($upload_img17063); ?>'
                        data-status='<?php echo htmlspecialchars($status17063); ?>'
                        data-category='<?php echo htmlspecialchars($category17063); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17063); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17063); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17064 -->
                    <img src='../image.php?id=17064'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17064'
                        onclick='fetchAssetData(17064);' class='asset-image' data-id='<?php echo $assetId17064; ?>'
                        data-room='<?php echo htmlspecialchars($room17064); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17064); ?>'
                        data-image='<?php echo base64_encode($upload_img17064); ?>'
                        data-status='<?php echo htmlspecialchars($status17064); ?>'
                        data-category='<?php echo htmlspecialchars($category17064); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17064); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17064); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17065 -->
                    <img src='../image.php?id=17065'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17065'
                        onclick='fetchAssetData(17065);' class='asset-image' data-id='<?php echo $assetId17065; ?>'
                        data-room='<?php echo htmlspecialchars($room17065); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17065); ?>'
                        data-image='<?php echo base64_encode($upload_img17065); ?>'
                        data-status='<?php echo htmlspecialchars($status17065); ?>'
                        data-category='<?php echo htmlspecialchars($category17065); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17065); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17065); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17066 -->
                    <img src='../image.php?id=17066'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17066'
                        onclick='fetchAssetData(17066);' class='asset-image' data-id='<?php echo $assetId17066; ?>'
                        data-room='<?php echo htmlspecialchars($room17066); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17066); ?>'
                        data-image='<?php echo base64_encode($upload_img17066); ?>'
                        data-status='<?php echo htmlspecialchars($status17066); ?>'
                        data-category='<?php echo htmlspecialchars($category17066); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17066); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17066); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17067 -->
                    <img src='../image.php?id=17067'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17067'
                        onclick='fetchAssetData(17067);' class='asset-image' data-id='<?php echo $assetId17067; ?>'
                        data-room='<?php echo htmlspecialchars($room17067); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17067); ?>'
                        data-image='<?php echo base64_encode($upload_img17067); ?>'
                        data-status='<?php echo htmlspecialchars($status17067); ?>'
                        data-category='<?php echo htmlspecialchars($category17067); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17067); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17067); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17068 -->
                    <img src='../image.php?id=17068'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17068'
                        onclick='fetchAssetData(17068);' class='asset-image' data-id='<?php echo $assetId17068; ?>'
                        data-room='<?php echo htmlspecialchars($room17068); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17068); ?>'
                        data-image='<?php echo base64_encode($upload_img17068); ?>'
                        data-status='<?php echo htmlspecialchars($status17068); ?>'
                        data-category='<?php echo htmlspecialchars($category17068); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17068); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17068); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17069 -->
                    <img src='../image.php?id=17069'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17069'
                        onclick='fetchAssetData(17069);' class='asset-image' data-id='<?php echo $assetId17069; ?>'
                        data-room='<?php echo htmlspecialchars($room17069); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17069); ?>'
                        data-image='<?php echo base64_encode($upload_img17069); ?>'
                        data-status='<?php echo htmlspecialchars($status17069); ?>'
                        data-category='<?php echo htmlspecialchars($category17069); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17069); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17069); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17070 -->
                    <img src='../image.php?id=17070'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17070'
                        onclick='fetchAssetData(17070);' class='asset-image' data-id='<?php echo $assetId17070; ?>'
                        data-room='<?php echo htmlspecialchars($room17070); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17070); ?>'
                        data-image='<?php echo base64_encode($upload_img17070); ?>'
                        data-status='<?php echo htmlspecialchars($status17070); ?>'
                        data-category='<?php echo htmlspecialchars($category17070); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17070); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17070); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17071 -->
                    <img src='../image.php?id=17071'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17071'
                        onclick='fetchAssetData(17071);' class='asset-image' data-id='<?php echo $assetId17071; ?>'
                        data-room='<?php echo htmlspecialchars($room17071); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17071); ?>'
                        data-image='<?php echo base64_encode($upload_img17071); ?>'
                        data-status='<?php echo htmlspecialchars($status17071); ?>'
                        data-category='<?php echo htmlspecialchars($category17071); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17071); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17071); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17072 -->
                    <img src='../image.php?id=17072'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17072'
                        onclick='fetchAssetData(17072);' class='asset-image' data-id='<?php echo $assetId17072; ?>'
                        data-room='<?php echo htmlspecialchars($room17072); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17072); ?>'
                        data-image='<?php echo base64_encode($upload_img17072); ?>'
                        data-status='<?php echo htmlspecialchars($status17072); ?>'
                        data-category='<?php echo htmlspecialchars($category17072); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17072); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17072); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17073 -->
                    <img src='../image.php?id=17073'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17073'
                        onclick='fetchAssetData(17073);' class='asset-image' data-id='<?php echo $assetId17073; ?>'
                        data-room='<?php echo htmlspecialchars($room17073); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17073); ?>'
                        data-image='<?php echo base64_encode($upload_img17073); ?>'
                        data-status='<?php echo htmlspecialchars($status17073); ?>'
                        data-category='<?php echo htmlspecialchars($category17073); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17073); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17073); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17074 -->
                    <img src='../image.php?id=17074'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17074'
                        onclick='fetchAssetData(17074);' class='asset-image' data-id='<?php echo $assetId17074; ?>'
                        data-room='<?php echo htmlspecialchars($room17074); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17074); ?>'
                        data-image='<?php echo base64_encode($upload_img17074); ?>'
                        data-status='<?php echo htmlspecialchars($status17074); ?>'
                        data-category='<?php echo htmlspecialchars($category17074); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17074); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17074); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17075 -->
                    <img src='../image.php?id=17075'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17075'
                        onclick='fetchAssetData(17075);' class='asset-image' data-id='<?php echo $assetId17075; ?>'
                        data-room='<?php echo htmlspecialchars($room17075); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17075); ?>'
                        data-image='<?php echo base64_encode($upload_img17075); ?>'
                        data-status='<?php echo htmlspecialchars($status17075); ?>'
                        data-category='<?php echo htmlspecialchars($category17075); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17075); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17075); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17076 -->
                    <img src='../image.php?id=17076'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17076'
                        onclick='fetchAssetData(17076);' class='asset-image' data-id='<?php echo $assetId17076; ?>'
                        data-room='<?php echo htmlspecialchars($room17076); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17076); ?>'
                        data-image='<?php echo base64_encode($upload_img17076); ?>'
                        data-status='<?php echo htmlspecialchars($status17076); ?>'
                        data-category='<?php echo htmlspecialchars($category17076); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17076); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17076); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17077 -->
                    <img src='../image.php?id=17077'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17077'
                        onclick='fetchAssetData(17077);' class='asset-image' data-id='<?php echo $assetId17077; ?>'
                        data-room='<?php echo htmlspecialchars($room17077); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17077); ?>'
                        data-image='<?php echo base64_encode($upload_img17077); ?>'
                        data-status='<?php echo htmlspecialchars($status17077); ?>'
                        data-category='<?php echo htmlspecialchars($category17077); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17077); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17077); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17078 -->
                    <img src='../image.php?id=17078'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17078'
                        onclick='fetchAssetData(17078);' class='asset-image' data-id='<?php echo $assetId17078; ?>'
                        data-room='<?php echo htmlspecialchars($room17078); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17078); ?>'
                        data-image='<?php echo base64_encode($upload_img17078); ?>'
                        data-status='<?php echo htmlspecialchars($status17078); ?>'
                        data-category='<?php echo htmlspecialchars($category17078); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17078); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17078); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17079 -->
                    <img src='../image.php?id=17079'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17079'
                        onclick='fetchAssetData(17079);' class='asset-image' data-id='<?php echo $assetId17079; ?>'
                        data-room='<?php echo htmlspecialchars($room17079); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17079); ?>'
                        data-image='<?php echo base64_encode($upload_img17079); ?>'
                        data-status='<?php echo htmlspecialchars($status17079); ?>'
                        data-category='<?php echo htmlspecialchars($category17079); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17079); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17079); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17080 -->
                    <img src='../image.php?id=17080'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17080'
                        onclick='fetchAssetData(17080);' class='asset-image' data-id='<?php echo $assetId17080; ?>'
                        data-room='<?php echo htmlspecialchars($room17080); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17080); ?>'
                        data-image='<?php echo base64_encode($upload_img17080); ?>'
                        data-status='<?php echo htmlspecialchars($status17080); ?>'
                        data-category='<?php echo htmlspecialchars($category17080); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17080); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17080); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17081 -->
                    <img src='../image.php?id=17081'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17081'
                        onclick='fetchAssetData(17081);' class='asset-image' data-id='<?php echo $assetId17081; ?>'
                        data-room='<?php echo htmlspecialchars($room17081); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17081); ?>'
                        data-image='<?php echo base64_encode($upload_img17081); ?>'
                        data-status='<?php echo htmlspecialchars($status17081); ?>'
                        data-category='<?php echo htmlspecialchars($category17081); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17081); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17081); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17082 -->
                    <img src='../image.php?id=17082'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17082'
                        onclick='fetchAssetData(17082);' class='asset-image' data-id='<?php echo $assetId17082; ?>'
                        data-room='<?php echo htmlspecialchars($room17082); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17082); ?>'
                        data-image='<?php echo base64_encode($upload_img17082); ?>'
                        data-status='<?php echo htmlspecialchars($status17082); ?>'
                        data-category='<?php echo htmlspecialchars($category17082); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17082); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17082); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17083 -->
                    <img src='../image.php?id=17083'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17083'
                        onclick='fetchAssetData(17083);' class='asset-image' data-id='<?php echo $assetId17083; ?>'
                        data-room='<?php echo htmlspecialchars($room17083); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17083); ?>'
                        data-image='<?php echo base64_encode($upload_img17083); ?>'
                        data-status='<?php echo htmlspecialchars($status17083); ?>'
                        data-category='<?php echo htmlspecialchars($category17083); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17083); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17083); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17084 -->
                    <img src='../image.php?id=17084'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17084'
                        onclick='fetchAssetData(17084);' class='asset-image' data-id='<?php echo $assetId17084; ?>'
                        data-room='<?php echo htmlspecialchars($room17084); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17084); ?>'
                        data-image='<?php echo base64_encode($upload_img17084); ?>'
                        data-status='<?php echo htmlspecialchars($status17084); ?>'
                        data-category='<?php echo htmlspecialchars($category17084); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17084); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17084); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17085 -->
                    <img src='../image.php?id=17085'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17085'
                        onclick='fetchAssetData(17085);' class='asset-image' data-id='<?php echo $assetId17085; ?>'
                        data-room='<?php echo htmlspecialchars($room17085); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17085); ?>'
                        data-image='<?php echo base64_encode($upload_img17085); ?>'
                        data-status='<?php echo htmlspecialchars($status17085); ?>'
                        data-category='<?php echo htmlspecialchars($category17085); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17085); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17085); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17086 -->
                    <img src='../image.php?id=17086'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17086'
                        onclick='fetchAssetData(17086);' class='asset-image' data-id='<?php echo $assetId17086; ?>'
                        data-room='<?php echo htmlspecialchars($room17086); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17086); ?>'
                        data-image='<?php echo base64_encode($upload_img17086); ?>'
                        data-status='<?php echo htmlspecialchars($status17086); ?>'
                        data-category='<?php echo htmlspecialchars($category17086); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17086); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17086); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17087 -->
                    <img src='../image.php?id=17087'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17087'
                        onclick='fetchAssetData(17087);' class='asset-image' data-id='<?php echo $assetId17087; ?>'
                        data-room='<?php echo htmlspecialchars($room17087); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17087); ?>'
                        data-image='<?php echo base64_encode($upload_img17087); ?>'
                        data-status='<?php echo htmlspecialchars($status17087); ?>'
                        data-category='<?php echo htmlspecialchars($category17087); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17087); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17087); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17088 -->
                    <img src='../image.php?id=17088'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17088'
                        onclick='fetchAssetData(17088);' class='asset-image' data-id='<?php echo $assetId17088; ?>'
                        data-room='<?php echo htmlspecialchars($room17088); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17088); ?>'
                        data-image='<?php echo base64_encode($upload_img17088); ?>'
                        data-status='<?php echo htmlspecialchars($status17088); ?>'
                        data-category='<?php echo htmlspecialchars($category17088); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17088); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17088); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17089 -->
                    <img src='../image.php?id=17089'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17089'
                        onclick='fetchAssetData(17089);' class='asset-image' data-id='<?php echo $assetId17089; ?>'
                        data-room='<?php echo htmlspecialchars($room17089); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17089); ?>'
                        data-image='<?php echo base64_encode($upload_img17089); ?>'
                        data-status='<?php echo htmlspecialchars($status17089); ?>'
                        data-category='<?php echo htmlspecialchars($category17089); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17089); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17089); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17090 -->
                    <img src='../image.php?id=17090'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17090'
                        onclick='fetchAssetData(17090);' class='asset-image' data-id='<?php echo $assetId17090; ?>'
                        data-room='<?php echo htmlspecialchars($room17090); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17090); ?>'
                        data-image='<?php echo base64_encode($upload_img17090); ?>'
                        data-status='<?php echo htmlspecialchars($status17090); ?>'
                        data-category='<?php echo htmlspecialchars($category17090); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17090); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17090); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17091 -->
                    <img src='../image.php?id=17091'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17091'
                        onclick='fetchAssetData(17091);' class='asset-image' data-id='<?php echo $assetId17091; ?>'
                        data-room='<?php echo htmlspecialchars($room17091); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17091); ?>'
                        data-image='<?php echo base64_encode($upload_img17091); ?>'
                        data-status='<?php echo htmlspecialchars($status17091); ?>'
                        data-category='<?php echo htmlspecialchars($category17091); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17091); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17091); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17092 -->
                    <img src='../image.php?id=17092'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17092'
                        onclick='fetchAssetData(17092);' class='asset-image' data-id='<?php echo $assetId17092; ?>'
                        data-room='<?php echo htmlspecialchars($room17092); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17092); ?>'
                        data-image='<?php echo base64_encode($upload_img17092); ?>'
                        data-status='<?php echo htmlspecialchars($status17092); ?>'
                        data-category='<?php echo htmlspecialchars($category17092); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17092); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17092); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17093 -->
                    <img src='../image.php?id=17093'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17093'
                        onclick='fetchAssetData(17093);' class='asset-image' data-id='<?php echo $assetId17093; ?>'
                        data-room='<?php echo htmlspecialchars($room17093); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17093); ?>'
                        data-image='<?php echo base64_encode($upload_img17093); ?>'
                        data-status='<?php echo htmlspecialchars($status17093); ?>'
                        data-category='<?php echo htmlspecialchars($category17093); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17093); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17093); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17094 -->
                    <img src='../image.php?id=17094'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17094'
                        onclick='fetchAssetData(17094);' class='asset-image' data-id='<?php echo $assetId17094; ?>'
                        data-room='<?php echo htmlspecialchars($room17094); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17094); ?>'
                        data-image='<?php echo base64_encode($upload_img17094); ?>'
                        data-status='<?php echo htmlspecialchars($status17094); ?>'
                        data-category='<?php echo htmlspecialchars($category17094); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17094); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17094); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17095 -->
                    <img src='../image.php?id=17095'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17095'
                        onclick='fetchAssetData(17095);' class='asset-image' data-id='<?php echo $assetId17095; ?>'
                        data-room='<?php echo htmlspecialchars($room17095); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17095); ?>'
                        data-image='<?php echo base64_encode($upload_img17095); ?>'
                        data-status='<?php echo htmlspecialchars($status17095); ?>'
                        data-category='<?php echo htmlspecialchars($category17095); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17095); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17095); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17096 -->
                    <img src='../image.php?id=17096'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17096'
                        onclick='fetchAssetData(17096);' class='asset-image' data-id='<?php echo $assetId17096; ?>'
                        data-room='<?php echo htmlspecialchars($room17096); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17096); ?>'
                        data-image='<?php echo base64_encode($upload_img17096); ?>'
                        data-status='<?php echo htmlspecialchars($status17096); ?>'
                        data-category='<?php echo htmlspecialchars($category17096); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17096); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17096); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17097 -->
                    <img src='../image.php?id=17097'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17097'
                        onclick='fetchAssetData(17097);' class='asset-image' data-id='<?php echo $assetId17097; ?>'
                        data-room='<?php echo htmlspecialchars($room17097); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17097); ?>'
                        data-image='<?php echo base64_encode($upload_img17097); ?>'
                        data-status='<?php echo htmlspecialchars($status17097); ?>'
                        data-category='<?php echo htmlspecialchars($category17097); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17097); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17097); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17098 -->
                    <img src='../image.php?id=17098'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17098'
                        onclick='fetchAssetData(17098);' class='asset-image' data-id='<?php echo $assetId17098; ?>'
                        data-room='<?php echo htmlspecialchars($room17098); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17098); ?>'
                        data-image='<?php echo base64_encode($upload_img17098); ?>'
                        data-status='<?php echo htmlspecialchars($status17098); ?>'
                        data-category='<?php echo htmlspecialchars($category17098); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17098); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17098); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17099 -->
                    <img src='../image.php?id=17099'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17099'
                        onclick='fetchAssetData(17099);' class='asset-image' data-id='<?php echo $assetId17099; ?>'
                        data-room='<?php echo htmlspecialchars($room17099); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17099); ?>'
                        data-image='<?php echo base64_encode($upload_img17099); ?>'
                        data-status='<?php echo htmlspecialchars($status17099); ?>'
                        data-category='<?php echo htmlspecialchars($category17099); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17099); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17099); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17100 -->
                    <img src='../image.php?id=17100'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17100'
                        onclick='fetchAssetData(17100);' class='asset-image' data-id='<?php echo $assetId17100; ?>'
                        data-room='<?php echo htmlspecialchars($room17100); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17100); ?>'
                        data-image='<?php echo base64_encode($upload_img17100); ?>'
                        data-status='<?php echo htmlspecialchars($status17100); ?>'
                        data-category='<?php echo htmlspecialchars($category17100); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17100); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17100); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17101 -->
                    <img src='../image.php?id=17101'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17101'
                        onclick='fetchAssetData(17101);' class='asset-image' data-id='<?php echo $assetId17101; ?>'
                        data-room='<?php echo htmlspecialchars($room17101); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17101); ?>'
                        data-image='<?php echo base64_encode($upload_img17101); ?>'
                        data-status='<?php echo htmlspecialchars($status17101); ?>'
                        data-category='<?php echo htmlspecialchars($category17101); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17101); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17101); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17102 -->
                    <img src='../image.php?id=17102'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17102'
                        onclick='fetchAssetData(17102);' class='asset-image' data-id='<?php echo $assetId17102; ?>'
                        data-room='<?php echo htmlspecialchars($room17102); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17102); ?>'
                        data-image='<?php echo base64_encode($upload_img17102); ?>'
                        data-status='<?php echo htmlspecialchars($status17102); ?>'
                        data-category='<?php echo htmlspecialchars($category17102); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17102); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17102); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17103 -->
                    <img src='../image.php?id=17103'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17103'
                        onclick='fetchAssetData(17103);' class='asset-image' data-id='<?php echo $assetId17103; ?>'
                        data-room='<?php echo htmlspecialchars($room17103); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17103); ?>'
                        data-image='<?php echo base64_encode($upload_img17103); ?>'
                        data-status='<?php echo htmlspecialchars($status17103); ?>'
                        data-category='<?php echo htmlspecialchars($category17103); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17103); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17103); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17104 -->
                    <img src='../image.php?id=17104'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17104'
                        onclick='fetchAssetData(17104);' class='asset-image' data-id='<?php echo $assetId17104; ?>'
                        data-room='<?php echo htmlspecialchars($room17104); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17104); ?>'
                        data-image='<?php echo base64_encode($upload_img17104); ?>'
                        data-status='<?php echo htmlspecialchars($status17104); ?>'
                        data-category='<?php echo htmlspecialchars($category17104); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17104); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17104); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17105 -->
                    <img src='../image.php?id=17105'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17105'
                        onclick='fetchAssetData(17105);' class='asset-image' data-id='<?php echo $assetId17105; ?>'
                        data-room='<?php echo htmlspecialchars($room17105); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17105); ?>'
                        data-image='<?php echo base64_encode($upload_img17105); ?>'
                        data-status='<?php echo htmlspecialchars($status17105); ?>'
                        data-category='<?php echo htmlspecialchars($category17105); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17105); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17105); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17106 -->
                    <img src='../image.php?id=17106'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17106'
                        onclick='fetchAssetData(17106);' class='asset-image' data-id='<?php echo $assetId17106; ?>'
                        data-room='<?php echo htmlspecialchars($room17106); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17106); ?>'
                        data-image='<?php echo base64_encode($upload_img17106); ?>'
                        data-status='<?php echo htmlspecialchars($status17106); ?>'
                        data-category='<?php echo htmlspecialchars($category17106); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17106); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17106); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17107 -->
                    <img src='../image.php?id=17107'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17107'
                        onclick='fetchAssetData(17107);' class='asset-image' data-id='<?php echo $assetId17107; ?>'
                        data-room='<?php echo htmlspecialchars($room17107); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17107); ?>'
                        data-image='<?php echo base64_encode($upload_img17107); ?>'
                        data-status='<?php echo htmlspecialchars($status17107); ?>'
                        data-category='<?php echo htmlspecialchars($category17107); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17107); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17107); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17108 -->
                    <img src='../image.php?id=17108'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17108'
                        onclick='fetchAssetData(17108);' class='asset-image' data-id='<?php echo $assetId17108; ?>'
                        data-room='<?php echo htmlspecialchars($room17108); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17108); ?>'
                        data-image='<?php echo base64_encode($upload_img17108); ?>'
                        data-status='<?php echo htmlspecialchars($status17108); ?>'
                        data-category='<?php echo htmlspecialchars($category17108); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17108); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17108); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17109 -->
                    <img src='../image.php?id=17109'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17109'
                        onclick='fetchAssetData(17109);' class='asset-image' data-id='<?php echo $assetId17109; ?>'
                        data-room='<?php echo htmlspecialchars($room17109); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17109); ?>'
                        data-image='<?php echo base64_encode($upload_img17109); ?>'
                        data-status='<?php echo htmlspecialchars($status17109); ?>'
                        data-category='<?php echo htmlspecialchars($category17109); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17109); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17109); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17110 -->
                    <img src='../image.php?id=17110'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17110'
                        onclick='fetchAssetData(17110);' class='asset-image' data-id='<?php echo $assetId17110; ?>'
                        data-room='<?php echo htmlspecialchars($room17110); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17110); ?>'
                        data-image='<?php echo base64_encode($upload_img17110); ?>'
                        data-status='<?php echo htmlspecialchars($status17110); ?>'
                        data-category='<?php echo htmlspecialchars($category17110); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17110); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17110); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17111 -->
                    <img src='../image.php?id=17111'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17111'
                        onclick='fetchAssetData(17111);' class='asset-image' data-id='<?php echo $assetId17111; ?>'
                        data-room='<?php echo htmlspecialchars($room17111); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17111); ?>'
                        data-image='<?php echo base64_encode($upload_img17111); ?>'
                        data-status='<?php echo htmlspecialchars($status17111); ?>'
                        data-category='<?php echo htmlspecialchars($category17111); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17111); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17111); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17112 -->
                    <img src='../image.php?id=17112'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17112'
                        onclick='fetchAssetData(17112);' class='asset-image' data-id='<?php echo $assetId17112; ?>'
                        data-room='<?php echo htmlspecialchars($room17112); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17112); ?>'
                        data-image='<?php echo base64_encode($upload_img17112); ?>'
                        data-status='<?php echo htmlspecialchars($status17112); ?>'
                        data-category='<?php echo htmlspecialchars($category17112); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17112); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17112); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17113 -->
                    <img src='../image.php?id=17113'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17113'
                        onclick='fetchAssetData(17113);' class='asset-image' data-id='<?php echo $assetId17113; ?>'
                        data-room='<?php echo htmlspecialchars($room17113); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17113); ?>'
                        data-image='<?php echo base64_encode($upload_img17113); ?>'
                        data-status='<?php echo htmlspecialchars($status17113); ?>'
                        data-category='<?php echo htmlspecialchars($category17113); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17113); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17113); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17114 -->
                    <img src='../image.php?id=17114'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17114'
                        onclick='fetchAssetData(17114);' class='asset-image' data-id='<?php echo $assetId17114; ?>'
                        data-room='<?php echo htmlspecialchars($room17114); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17114); ?>'
                        data-image='<?php echo base64_encode($upload_img17114); ?>'
                        data-status='<?php echo htmlspecialchars($status17114); ?>'
                        data-category='<?php echo htmlspecialchars($category17114); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17114); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17114); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17115 -->
                    <img src='../image.php?id=17115'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17115'
                        onclick='fetchAssetData(17115);' class='asset-image' data-id='<?php echo $assetId17115; ?>'
                        data-room='<?php echo htmlspecialchars($room17115); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17115); ?>'
                        data-image='<?php echo base64_encode($upload_img17115); ?>'
                        data-status='<?php echo htmlspecialchars($status17115); ?>'
                        data-category='<?php echo htmlspecialchars($category17115); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17115); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17115); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17116 -->
                    <img src='../image.php?id=17116'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17116'
                        onclick='fetchAssetData(17116);' class='asset-image' data-id='<?php echo $assetId17116; ?>'
                        data-room='<?php echo htmlspecialchars($room17116); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17116); ?>'
                        data-image='<?php echo base64_encode($upload_img17116); ?>'
                        data-status='<?php echo htmlspecialchars($status17116); ?>'
                        data-category='<?php echo htmlspecialchars($category17116); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17116); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17116); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17117 -->
                    <img src='../image.php?id=17117'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17117'
                        onclick='fetchAssetData(17117);' class='asset-image' data-id='<?php echo $assetId17117; ?>'
                        data-room='<?php echo htmlspecialchars($room17117); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17117); ?>'
                        data-image='<?php echo base64_encode($upload_img17117); ?>'
                        data-status='<?php echo htmlspecialchars($status17117); ?>'
                        data-category='<?php echo htmlspecialchars($category17117); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17117); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17117); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17118 -->
                    <img src='../image.php?id=17118'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17118'
                        onclick='fetchAssetData(17118);' class='asset-image' data-id='<?php echo $assetId17118; ?>'
                        data-room='<?php echo htmlspecialchars($room17118); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17118); ?>'
                        data-image='<?php echo base64_encode($upload_img17118); ?>'
                        data-status='<?php echo htmlspecialchars($status17118); ?>'
                        data-category='<?php echo htmlspecialchars($category17118); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17118); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17118); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17119 -->
                    <img src='../image.php?id=17119'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17119'
                        onclick='fetchAssetData(17119);' class='asset-image' data-id='<?php echo $assetId17119; ?>'
                        data-room='<?php echo htmlspecialchars($room17119); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17119); ?>'
                        data-image='<?php echo base64_encode($upload_img17119); ?>'
                        data-status='<?php echo htmlspecialchars($status17119); ?>'
                        data-category='<?php echo htmlspecialchars($category17119); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17119); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17119); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17120 -->
                    <img src='../image.php?id=17120'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17120'
                        onclick='fetchAssetData(17120);' class='asset-image' data-id='<?php echo $assetId17120; ?>'
                        data-room='<?php echo htmlspecialchars($room17120); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17120); ?>'
                        data-image='<?php echo base64_encode($upload_img17120); ?>'
                        data-status='<?php echo htmlspecialchars($status17120); ?>'
                        data-category='<?php echo htmlspecialchars($category17120); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17120); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17120); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17121 -->
                    <img src='../image.php?id=17121'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17121'
                        onclick='fetchAssetData(17121);' class='asset-image' data-id='<?php echo $assetId17121; ?>'
                        data-room='<?php echo htmlspecialchars($room17121); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17121); ?>'
                        data-image='<?php echo base64_encode($upload_img17121); ?>'
                        data-status='<?php echo htmlspecialchars($status17121); ?>'
                        data-category='<?php echo htmlspecialchars($category17121); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17121); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17121); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17122 -->
                    <img src='../image.php?id=17122'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17122'
                        onclick='fetchAssetData(17122);' class='asset-image' data-id='<?php echo $assetId17122; ?>'
                        data-room='<?php echo htmlspecialchars($room17122); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17122); ?>'
                        data-image='<?php echo base64_encode($upload_img17122); ?>'
                        data-status='<?php echo htmlspecialchars($status17122); ?>'
                        data-category='<?php echo htmlspecialchars($category17122); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17122); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17122); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17123 -->
                    <img src='../image.php?id=17123'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17123'
                        onclick='fetchAssetData(17123);' class='asset-image' data-id='<?php echo $assetId17123; ?>'
                        data-room='<?php echo htmlspecialchars($room17123); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17123); ?>'
                        data-image='<?php echo base64_encode($upload_img17123); ?>'
                        data-status='<?php echo htmlspecialchars($status17123); ?>'
                        data-category='<?php echo htmlspecialchars($category17123); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17123); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17123); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17124 -->
                    <img src='../image.php?id=17124'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17124'
                        onclick='fetchAssetData(17124);' class='asset-image' data-id='<?php echo $assetId17124; ?>'
                        data-room='<?php echo htmlspecialchars($room17124); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17124); ?>'
                        data-image='<?php echo base64_encode($upload_img17124); ?>'
                        data-status='<?php echo htmlspecialchars($status17124); ?>'
                        data-category='<?php echo htmlspecialchars($category17124); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17124); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17124); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17125 -->
                    <img src='../image.php?id=17125'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17125'
                        onclick='fetchAssetData(17125);' class='asset-image' data-id='<?php echo $assetId17125; ?>'
                        data-room='<?php echo htmlspecialchars($room17125); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17125); ?>'
                        data-image='<?php echo base64_encode($upload_img17125); ?>'
                        data-status='<?php echo htmlspecialchars($status17125); ?>'
                        data-category='<?php echo htmlspecialchars($category17125); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17125); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17125); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17126 -->
                    <img src='../image.php?id=17126'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17126'
                        onclick='fetchAssetData(17126);' class='asset-image' data-id='<?php echo $assetId17126; ?>'
                        data-room='<?php echo htmlspecialchars($room17126); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17126); ?>'
                        data-image='<?php echo base64_encode($upload_img17126); ?>'
                        data-status='<?php echo htmlspecialchars($status17126); ?>'
                        data-category='<?php echo htmlspecialchars($category17126); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17126); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17126); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17127 -->
                    <img src='../image.php?id=17127'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17127'
                        onclick='fetchAssetData(17127);' class='asset-image' data-id='<?php echo $assetId17127; ?>'
                        data-room='<?php echo htmlspecialchars($room17127); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17127); ?>'
                        data-image='<?php echo base64_encode($upload_img17127); ?>'
                        data-status='<?php echo htmlspecialchars($status17127); ?>'
                        data-category='<?php echo htmlspecialchars($category17127); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17127); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17127); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17128 -->
                    <img src='../image.php?id=17128'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17128'
                        onclick='fetchAssetData(17128);' class='asset-image' data-id='<?php echo $assetId17128; ?>'
                        data-room='<?php echo htmlspecialchars($room17128); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17128); ?>'
                        data-image='<?php echo base64_encode($upload_img17128); ?>'
                        data-status='<?php echo htmlspecialchars($status17128); ?>'
                        data-category='<?php echo htmlspecialchars($category17128); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17128); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17128); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17129 -->
                    <img src='../image.php?id=17129'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17129'
                        onclick='fetchAssetData(17129);' class='asset-image' data-id='<?php echo $assetId17129; ?>'
                        data-room='<?php echo htmlspecialchars($room17129); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17129); ?>'
                        data-image='<?php echo base64_encode($upload_img17129); ?>'
                        data-status='<?php echo htmlspecialchars($status17129); ?>'
                        data-category='<?php echo htmlspecialchars($category17129); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17129); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17129); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17130 -->
                    <img src='../image.php?id=17130'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17130'
                        onclick='fetchAssetData(17130);' class='asset-image' data-id='<?php echo $assetId17130; ?>'
                        data-room='<?php echo htmlspecialchars($room17130); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17130); ?>'
                        data-image='<?php echo base64_encode($upload_img17130); ?>'
                        data-status='<?php echo htmlspecialchars($status17130); ?>'
                        data-category='<?php echo htmlspecialchars($category17130); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17130); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17130); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17131 -->
                    <img src='../image.php?id=17131'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17131'
                        onclick='fetchAssetData(17131);' class='asset-image' data-id='<?php echo $assetId17131; ?>'
                        data-room='<?php echo htmlspecialchars($room17131); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17131); ?>'
                        data-image='<?php echo base64_encode($upload_img17131); ?>'
                        data-status='<?php echo htmlspecialchars($status17131); ?>'
                        data-category='<?php echo htmlspecialchars($category17131); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17131); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17131); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17132 -->
                    <img src='../image.php?id=17132'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17132'
                        onclick='fetchAssetData(17132);' class='asset-image' data-id='<?php echo $assetId17132; ?>'
                        data-room='<?php echo htmlspecialchars($room17132); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17132); ?>'
                        data-image='<?php echo base64_encode($upload_img17132); ?>'
                        data-status='<?php echo htmlspecialchars($status17132); ?>'
                        data-category='<?php echo htmlspecialchars($category17132); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17132); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17132); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17133 -->
                    <img src='../image.php?id=17133'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17133'
                        onclick='fetchAssetData(17133);' class='asset-image' data-id='<?php echo $assetId17133; ?>'
                        data-room='<?php echo htmlspecialchars($room17133); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17133); ?>'
                        data-image='<?php echo base64_encode($upload_img17133); ?>'
                        data-status='<?php echo htmlspecialchars($status17133); ?>'
                        data-category='<?php echo htmlspecialchars($category17133); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17133); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17133); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17134 -->
                    <img src='../image.php?id=17134'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17134'
                        onclick='fetchAssetData(17134);' class='asset-image' data-id='<?php echo $assetId17134; ?>'
                        data-room='<?php echo htmlspecialchars($room17134); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17134); ?>'
                        data-image='<?php echo base64_encode($upload_img17134); ?>'
                        data-status='<?php echo htmlspecialchars($status17134); ?>'
                        data-category='<?php echo htmlspecialchars($category17134); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17134); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17134); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17135 -->
                    <img src='../image.php?id=17135'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17135'
                        onclick='fetchAssetData(17135);' class='asset-image' data-id='<?php echo $assetId17135; ?>'
                        data-room='<?php echo htmlspecialchars($room17135); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17135); ?>'
                        data-image='<?php echo base64_encode($upload_img17135); ?>'
                        data-status='<?php echo htmlspecialchars($status17135); ?>'
                        data-category='<?php echo htmlspecialchars($category17135); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17135); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17135); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17136 -->
                    <img src='../image.php?id=17136'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17136'
                        onclick='fetchAssetData(17136);' class='asset-image' data-id='<?php echo $assetId17136; ?>'
                        data-room='<?php echo htmlspecialchars($room17136); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17136); ?>'
                        data-image='<?php echo base64_encode($upload_img17136); ?>'
                        data-status='<?php echo htmlspecialchars($status17136); ?>'
                        data-category='<?php echo htmlspecialchars($category17136); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17136); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17136); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17137 -->
                    <img src='../image.php?id=17137'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17137'
                        onclick='fetchAssetData(17137);' class='asset-image' data-id='<?php echo $assetId17137; ?>'
                        data-room='<?php echo htmlspecialchars($room17137); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17137); ?>'
                        data-image='<?php echo base64_encode($upload_img17137); ?>'
                        data-status='<?php echo htmlspecialchars($status17137); ?>'
                        data-category='<?php echo htmlspecialchars($category17137); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17137); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17137); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17138 -->
                    <img src='../image.php?id=17138'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17138'
                        onclick='fetchAssetData(17138);' class='asset-image' data-id='<?php echo $assetId17138; ?>'
                        data-room='<?php echo htmlspecialchars($room17138); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17138); ?>'
                        data-image='<?php echo base64_encode($upload_img17138); ?>'
                        data-status='<?php echo htmlspecialchars($status17138); ?>'
                        data-category='<?php echo htmlspecialchars($category17138); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17138); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17138); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17139 -->
                    <img src='../image.php?id=17139'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17139'
                        onclick='fetchAssetData(17139);' class='asset-image' data-id='<?php echo $assetId17139; ?>'
                        data-room='<?php echo htmlspecialchars($room17139); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17139); ?>'
                        data-image='<?php echo base64_encode($upload_img17139); ?>'
                        data-status='<?php echo htmlspecialchars($status17139); ?>'
                        data-category='<?php echo htmlspecialchars($category17139); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17139); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17139); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17140 -->
                    <img src='../image.php?id=17140'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17140'
                        onclick='fetchAssetData(17140);' class='asset-image' data-id='<?php echo $assetId17140; ?>'
                        data-room='<?php echo htmlspecialchars($room17140); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17140); ?>'
                        data-image='<?php echo base64_encode($upload_img17140); ?>'
                        data-status='<?php echo htmlspecialchars($status17140); ?>'
                        data-category='<?php echo htmlspecialchars($category17140); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17140); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17140); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17141 -->
                    <img src='../image.php?id=17141'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17141'
                        onclick='fetchAssetData(17141);' class='asset-image' data-id='<?php echo $assetId17141; ?>'
                        data-room='<?php echo htmlspecialchars($room17141); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17141); ?>'
                        data-image='<?php echo base64_encode($upload_img17141); ?>'
                        data-status='<?php echo htmlspecialchars($status17141); ?>'
                        data-category='<?php echo htmlspecialchars($category17141); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17141); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17141); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17142 -->
                    <img src='../image.php?id=17142'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17142'
                        onclick='fetchAssetData(17142);' class='asset-image' data-id='<?php echo $assetId17142; ?>'
                        data-room='<?php echo htmlspecialchars($room17142); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17142); ?>'
                        data-image='<?php echo base64_encode($upload_img17142); ?>'
                        data-status='<?php echo htmlspecialchars($status17142); ?>'
                        data-category='<?php echo htmlspecialchars($category17142); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17142); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17142); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17143 -->
                    <img src='../image.php?id=17143'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17143'
                        onclick='fetchAssetData(17143);' class='asset-image' data-id='<?php echo $assetId17143; ?>'
                        data-room='<?php echo htmlspecialchars($room17143); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17143); ?>'
                        data-image='<?php echo base64_encode($upload_img17143); ?>'
                        data-status='<?php echo htmlspecialchars($status17143); ?>'
                        data-category='<?php echo htmlspecialchars($category17143); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17143); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17143); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17144 -->
                    <img src='../image.php?id=17144'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17144'
                        onclick='fetchAssetData(17144);' class='asset-image' data-id='<?php echo $assetId17144; ?>'
                        data-room='<?php echo htmlspecialchars($room17144); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17144); ?>'
                        data-image='<?php echo base64_encode($upload_img17144); ?>'
                        data-status='<?php echo htmlspecialchars($status17144); ?>'
                        data-category='<?php echo htmlspecialchars($category17144); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17144); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17144); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17145 -->
                    <img src='../image.php?id=17145'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17145'
                        onclick='fetchAssetData(17145);' class='asset-image' data-id='<?php echo $assetId17145; ?>'
                        data-room='<?php echo htmlspecialchars($room17145); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17145); ?>'
                        data-image='<?php echo base64_encode($upload_img17145); ?>'
                        data-status='<?php echo htmlspecialchars($status17145); ?>'
                        data-category='<?php echo htmlspecialchars($category17145); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17145); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17145); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17146 -->
                    <img src='../image.php?id=17146'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17146'
                        onclick='fetchAssetData(17146);' class='asset-image' data-id='<?php echo $assetId17146; ?>'
                        data-room='<?php echo htmlspecialchars($room17146); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17146); ?>'
                        data-image='<?php echo base64_encode($upload_img17146); ?>'
                        data-status='<?php echo htmlspecialchars($status17146); ?>'
                        data-category='<?php echo htmlspecialchars($category17146); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17146); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17146); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17147 -->
                    <img src='../image.php?id=17147'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17147'
                        onclick='fetchAssetData(17147);' class='asset-image' data-id='<?php echo $assetId17147; ?>'
                        data-room='<?php echo htmlspecialchars($room17147); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17147); ?>'
                        data-image='<?php echo base64_encode($upload_img17147); ?>'
                        data-status='<?php echo htmlspecialchars($status17147); ?>'
                        data-category='<?php echo htmlspecialchars($category17147); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17147); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17147); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17148 -->
                    <img src='../image.php?id=17148'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17148'
                        onclick='fetchAssetData(17148);' class='asset-image' data-id='<?php echo $assetId17148; ?>'
                        data-room='<?php echo htmlspecialchars($room17148); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17148); ?>'
                        data-image='<?php echo base64_encode($upload_img17148); ?>'
                        data-status='<?php echo htmlspecialchars($status17148); ?>'
                        data-category='<?php echo htmlspecialchars($category17148); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17148); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17148); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17149 -->
                    <img src='../image.php?id=17149'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17149'
                        onclick='fetchAssetData(17149);' class='asset-image' data-id='<?php echo $assetId17149; ?>'
                        data-room='<?php echo htmlspecialchars($room17149); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17149); ?>'
                        data-image='<?php echo base64_encode($upload_img17149); ?>'
                        data-status='<?php echo htmlspecialchars($status17149); ?>'
                        data-category='<?php echo htmlspecialchars($category17149); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17149); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17149); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17150 -->
                    <img src='../image.php?id=17150'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17150'
                        onclick='fetchAssetData(17150);' class='asset-image' data-id='<?php echo $assetId17150; ?>'
                        data-room='<?php echo htmlspecialchars($room17150); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17150); ?>'
                        data-image='<?php echo base64_encode($upload_img17150); ?>'
                        data-status='<?php echo htmlspecialchars($status17150); ?>'
                        data-category='<?php echo htmlspecialchars($category17150); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17150); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17150); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17151 -->
                    <img src='../image.php?id=17151'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17151'
                        onclick='fetchAssetData(17151);' class='asset-image' data-id='<?php echo $assetId17151; ?>'
                        data-room='<?php echo htmlspecialchars($room17151); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17151); ?>'
                        data-image='<?php echo base64_encode($upload_img17151); ?>'
                        data-status='<?php echo htmlspecialchars($status17151); ?>'
                        data-category='<?php echo htmlspecialchars($category17151); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17151); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17151); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17152 -->
                    <img src='../image.php?id=17152'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17152'
                        onclick='fetchAssetData(17152);' class='asset-image' data-id='<?php echo $assetId17152; ?>'
                        data-room='<?php echo htmlspecialchars($room17152); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17152); ?>'
                        data-image='<?php echo base64_encode($upload_img17152); ?>'
                        data-status='<?php echo htmlspecialchars($status17152); ?>'
                        data-category='<?php echo htmlspecialchars($category17152); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17152); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17152); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17153 -->
                    <img src='../image.php?id=17153'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17153'
                        onclick='fetchAssetData(17153);' class='asset-image' data-id='<?php echo $assetId17153; ?>'
                        data-room='<?php echo htmlspecialchars($room17153); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17153); ?>'
                        data-image='<?php echo base64_encode($upload_img17153); ?>'
                        data-status='<?php echo htmlspecialchars($status17153); ?>'
                        data-category='<?php echo htmlspecialchars($category17153); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17153); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17153); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17154 -->
                    <img src='../image.php?id=17154'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17154'
                        onclick='fetchAssetData(17154);' class='asset-image' data-id='<?php echo $assetId17154; ?>'
                        data-room='<?php echo htmlspecialchars($room17154); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17154); ?>'
                        data-image='<?php echo base64_encode($upload_img17154); ?>'
                        data-status='<?php echo htmlspecialchars($status17154); ?>'
                        data-category='<?php echo htmlspecialchars($category17154); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17154); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17154); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17155 -->
                    <img src='../image.php?id=17155'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17155'
                        onclick='fetchAssetData(17155);' class='asset-image' data-id='<?php echo $assetId17155; ?>'
                        data-room='<?php echo htmlspecialchars($room17155); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17155); ?>'
                        data-image='<?php echo base64_encode($upload_img17155); ?>'
                        data-status='<?php echo htmlspecialchars($status17155); ?>'
                        data-category='<?php echo htmlspecialchars($category17155); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17155); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17155); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17156 -->
                    <img src='../image.php?id=17156'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17156'
                        onclick='fetchAssetData(17156);' class='asset-image' data-id='<?php echo $assetId17156; ?>'
                        data-room='<?php echo htmlspecialchars($room17156); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17156); ?>'
                        data-image='<?php echo base64_encode($upload_img17156); ?>'
                        data-status='<?php echo htmlspecialchars($status17156); ?>'
                        data-category='<?php echo htmlspecialchars($category17156); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17156); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17156); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17157 -->
                    <img src='../image.php?id=17157'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17157'
                        onclick='fetchAssetData(17157);' class='asset-image' data-id='<?php echo $assetId17157; ?>'
                        data-room='<?php echo htmlspecialchars($room17157); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17157); ?>'
                        data-image='<?php echo base64_encode($upload_img17157); ?>'
                        data-status='<?php echo htmlspecialchars($status17157); ?>'
                        data-category='<?php echo htmlspecialchars($category17157); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17157); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17157); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17158 -->
                    <img src='../image.php?id=17158'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17158'
                        onclick='fetchAssetData(17158);' class='asset-image' data-id='<?php echo $assetId17158; ?>'
                        data-room='<?php echo htmlspecialchars($room17158); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17158); ?>'
                        data-image='<?php echo base64_encode($upload_img17158); ?>'
                        data-status='<?php echo htmlspecialchars($status17158); ?>'
                        data-category='<?php echo htmlspecialchars($category17158); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17158); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17158); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17159 -->
                    <img src='../image.php?id=17159'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17159'
                        onclick='fetchAssetData(17159);' class='asset-image' data-id='<?php echo $assetId17159; ?>'
                        data-room='<?php echo htmlspecialchars($room17159); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17159); ?>'
                        data-image='<?php echo base64_encode($upload_img17159); ?>'
                        data-status='<?php echo htmlspecialchars($status17159); ?>'
                        data-category='<?php echo htmlspecialchars($category17159); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17159); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17159); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17160 -->
                    <img src='../image.php?id=17160'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17160'
                        onclick='fetchAssetData(17160);' class='asset-image' data-id='<?php echo $assetId17160; ?>'
                        data-room='<?php echo htmlspecialchars($room17160); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17160); ?>'
                        data-image='<?php echo base64_encode($upload_img17160); ?>'
                        data-status='<?php echo htmlspecialchars($status17160); ?>'
                        data-category='<?php echo htmlspecialchars($category17160); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17160); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17160); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17161 -->
                    <img src='../image.php?id=17161'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17161'
                        onclick='fetchAssetData(17161);' class='asset-image' data-id='<?php echo $assetId17161; ?>'
                        data-room='<?php echo htmlspecialchars($room17161); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17161); ?>'
                        data-image='<?php echo base64_encode($upload_img17161); ?>'
                        data-status='<?php echo htmlspecialchars($status17161); ?>'
                        data-category='<?php echo htmlspecialchars($category17161); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17161); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17161); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17162 -->
                    <img src='../image.php?id=17162'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:630px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17162'
                        onclick='fetchAssetData(17162);' class='asset-image' data-id='<?php echo $assetId17162; ?>'
                        data-room='<?php echo htmlspecialchars($room17162); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17162); ?>'
                        data-image='<?php echo base64_encode($upload_img17162); ?>'
                        data-status='<?php echo htmlspecialchars($status17162); ?>'
                        data-category='<?php echo htmlspecialchars($category17162); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17162); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17162); ?>;
                        position:absolute; top:465px; left:640px;'>
                    </div>

                    <!-- ASSET 17163 -->
                    <img src='../image.php?id=17163'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:695px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17163'
                        onclick='fetchAssetData(17163);' class='asset-image' data-id='<?php echo $assetId17163; ?>'
                        data-room='<?php echo htmlspecialchars($room17163); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17163); ?>'
                        data-image='<?php echo base64_encode($upload_img17163); ?>'
                        data-status='<?php echo htmlspecialchars($status17163); ?>'
                        data-category='<?php echo htmlspecialchars($category17163); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17163); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17163); ?>;
                        position:absolute; top:465px; left:705px;'>
                    </div>

                    <!-- ASSET 17164 -->
                    <img src='../image.php?id=17164'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:630px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17164'
                        onclick='fetchAssetData(17164);' class='asset-image' data-id='<?php echo $assetId17164; ?>'
                        data-room='<?php echo htmlspecialchars($room17164); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17164); ?>'
                        data-image='<?php echo base64_encode($upload_img17164); ?>'
                        data-status='<?php echo htmlspecialchars($status17164); ?>'
                        data-category='<?php echo htmlspecialchars($category17164); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17164); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17164); ?>;
                        position:absolute; top:430px; left:640px;'>
                    </div>

                    <!-- ASSET 17165 -->
                    <img src='../image.php?id=17165'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:695px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17165'
                        onclick='fetchAssetData(17165);' class='asset-image' data-id='<?php echo $assetId17165; ?>'
                        data-room='<?php echo htmlspecialchars($room17165); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17165); ?>'
                        data-image='<?php echo base64_encode($upload_img17165); ?>'
                        data-status='<?php echo htmlspecialchars($status17165); ?>'
                        data-category='<?php echo htmlspecialchars($category17165); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17165); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17165); ?>;
                        position:absolute; top:430px; left:705px;'>
                    </div>

                    <!-- ASSET 17166 -->
                    <img src='../image.php?id=17166'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17166'
                        onclick='fetchAssetData(17166);' class='asset-image' data-id='<?php echo $assetId17166; ?>'
                        data-room='<?php echo htmlspecialchars($room17166); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17166); ?>'
                        data-image='<?php echo base64_encode($upload_img17166); ?>'
                        data-status='<?php echo htmlspecialchars($status17166); ?>'
                        data-category='<?php echo htmlspecialchars($category17166); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17166); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17166); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17167 -->
                    <img src='../image.php?id=17167'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17167'
                        onclick='fetchAssetData(17167);' class='asset-image' data-id='<?php echo $assetId17167; ?>'
                        data-room='<?php echo htmlspecialchars($room17167); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17167); ?>'
                        data-image='<?php echo base64_encode($upload_img17167); ?>'
                        data-status='<?php echo htmlspecialchars($status17167); ?>'
                        data-category='<?php echo htmlspecialchars($category17167); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17167); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17167); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17168 -->
                    <img src='../image.php?id=17168'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17168'
                        onclick='fetchAssetData(17168);' class='asset-image' data-id='<?php echo $assetId17168; ?>'
                        data-room='<?php echo htmlspecialchars($room17168); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17168); ?>'
                        data-image='<?php echo base64_encode($upload_img17168); ?>'
                        data-status='<?php echo htmlspecialchars($status17168); ?>'
                        data-category='<?php echo htmlspecialchars($category17168); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17168); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17168); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17169 -->
                    <img src='../image.php?id=17169'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17169'
                        onclick='fetchAssetData(17169);' class='asset-image' data-id='<?php echo $assetId17169; ?>'
                        data-room='<?php echo htmlspecialchars($room17169); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17169); ?>'
                        data-image='<?php echo base64_encode($upload_img17169); ?>'
                        data-status='<?php echo htmlspecialchars($status17169); ?>'
                        data-category='<?php echo htmlspecialchars($category17169); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17169); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17169); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17170 -->
                    <img src='../image.php?id=17170'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17170'
                        onclick='fetchAssetData(17170);' class='asset-image' data-id='<?php echo $assetId17170; ?>'
                        data-room='<?php echo htmlspecialchars($room17170); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17170); ?>'
                        data-image='<?php echo base64_encode($upload_img17170); ?>'
                        data-status='<?php echo htmlspecialchars($status17170); ?>'
                        data-category='<?php echo htmlspecialchars($category17170); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17170); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17170); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17171 -->
                    <img src='../image.php?id=17171'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17171'
                        onclick='fetchAssetData(17171);' class='asset-image' data-id='<?php echo $assetId17171; ?>'
                        data-room='<?php echo htmlspecialchars($room17171); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17171); ?>'
                        data-image='<?php echo base64_encode($upload_img17171); ?>'
                        data-status='<?php echo htmlspecialchars($status17171); ?>'
                        data-category='<?php echo htmlspecialchars($category17171); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17171); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17171); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17172 -->
                    <img src='../image.php?id=17172'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17172'
                        onclick='fetchAssetData(17172);' class='asset-image' data-id='<?php echo $assetId17172; ?>'
                        data-room='<?php echo htmlspecialchars($room17172); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17172); ?>'
                        data-image='<?php echo base64_encode($upload_img17172); ?>'
                        data-status='<?php echo htmlspecialchars($status17172); ?>'
                        data-category='<?php echo htmlspecialchars($category17172); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17172); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17172); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17173 -->
                    <img src='../image.php?id=17173'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17173'
                        onclick='fetchAssetData(17173);' class='asset-image' data-id='<?php echo $assetId17173; ?>'
                        data-room='<?php echo htmlspecialchars($room17173); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17173); ?>'
                        data-image='<?php echo base64_encode($upload_img17173); ?>'
                        data-status='<?php echo htmlspecialchars($status17173); ?>'
                        data-category='<?php echo htmlspecialchars($category17173); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17173); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17173); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17174 -->
                    <img src='../image.php?id=17174'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17174'
                        onclick='fetchAssetData(17174);' class='asset-image' data-id='<?php echo $assetId17174; ?>'
                        data-room='<?php echo htmlspecialchars($room17174); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17174); ?>'
                        data-image='<?php echo base64_encode($upload_img17174); ?>'
                        data-status='<?php echo htmlspecialchars($status17174); ?>'
                        data-category='<?php echo htmlspecialchars($category17174); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17174); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17174); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17175 -->
                    <img src='../image.php?id=17175'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17175'
                        onclick='fetchAssetData(17175);' class='asset-image' data-id='<?php echo $assetId17175; ?>'
                        data-room='<?php echo htmlspecialchars($room17175); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17175); ?>'
                        data-image='<?php echo base64_encode($upload_img17175); ?>'
                        data-status='<?php echo htmlspecialchars($status17175); ?>'
                        data-category='<?php echo htmlspecialchars($category17175); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17175); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17175); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17176 -->
                    <img src='../image.php?id=17176'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17176'
                        onclick='fetchAssetData(17176);' class='asset-image' data-id='<?php echo $assetId17176; ?>'
                        data-room='<?php echo htmlspecialchars($room17176); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17176); ?>'
                        data-image='<?php echo base64_encode($upload_img17176); ?>'
                        data-status='<?php echo htmlspecialchars($status17176); ?>'
                        data-category='<?php echo htmlspecialchars($category17176); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17176); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17176); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17177 -->
                    <img src='../image.php?id=17177'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17177'
                        onclick='fetchAssetData(17177);' class='asset-image' data-id='<?php echo $assetId17177; ?>'
                        data-room='<?php echo htmlspecialchars($room17177); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17177); ?>'
                        data-image='<?php echo base64_encode($upload_img17177); ?>'
                        data-status='<?php echo htmlspecialchars($status17177); ?>'
                        data-category='<?php echo htmlspecialchars($category17177); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17177); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17177); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17178 -->
                    <img src='../image.php?id=17178'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17178'
                        onclick='fetchAssetData(17178);' class='asset-image' data-id='<?php echo $assetId17178; ?>'
                        data-room='<?php echo htmlspecialchars($room17178); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17178); ?>'
                        data-image='<?php echo base64_encode($upload_img17178); ?>'
                        data-status='<?php echo htmlspecialchars($status17178); ?>'
                        data-category='<?php echo htmlspecialchars($category17178); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17178); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17178); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17179 -->
                    <img src='../image.php?id=17179'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17179'
                        onclick='fetchAssetData(17179);' class='asset-image' data-id='<?php echo $assetId17179; ?>'
                        data-room='<?php echo htmlspecialchars($room17179); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17179); ?>'
                        data-image='<?php echo base64_encode($upload_img17179); ?>'
                        data-status='<?php echo htmlspecialchars($status17179); ?>'
                        data-category='<?php echo htmlspecialchars($category17179); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17179); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17179); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17180 -->
                    <img src='../image.php?id=17180'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17180'
                        onclick='fetchAssetData(17180);' class='asset-image' data-id='<?php echo $assetId17180; ?>'
                        data-room='<?php echo htmlspecialchars($room17180); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17180); ?>'
                        data-image='<?php echo base64_encode($upload_img17180); ?>'
                        data-status='<?php echo htmlspecialchars($status17180); ?>'
                        data-category='<?php echo htmlspecialchars($category17180); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17180); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17180); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17181 -->
                    <img src='../image.php?id=17181'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17181'
                        onclick='fetchAssetData(17181);' class='asset-image' data-id='<?php echo $assetId17181; ?>'
                        data-room='<?php echo htmlspecialchars($room17181); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17181); ?>'
                        data-image='<?php echo base64_encode($upload_img17181); ?>'
                        data-status='<?php echo htmlspecialchars($status17181); ?>'
                        data-category='<?php echo htmlspecialchars($category17181); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17181); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17181); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17182 -->
                    <img src='../image.php?id=17182'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17182'
                        onclick='fetchAssetData(17182);' class='asset-image' data-id='<?php echo $assetId17182; ?>'
                        data-room='<?php echo htmlspecialchars($room17182); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17182); ?>'
                        data-image='<?php echo base64_encode($upload_img17182); ?>'
                        data-status='<?php echo htmlspecialchars($status17182); ?>'
                        data-category='<?php echo htmlspecialchars($category17182); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17182); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17182); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17183 -->
                    <img src='../image.php?id=17183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17183'
                        onclick='fetchAssetData(17183);' class='asset-image' data-id='<?php echo $assetId17183; ?>'
                        data-room='<?php echo htmlspecialchars($room17183); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17183); ?>'
                        data-image='<?php echo base64_encode($upload_img17183); ?>'
                        data-status='<?php echo htmlspecialchars($status17183); ?>'
                        data-category='<?php echo htmlspecialchars($category17183); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17183); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17183); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17184 -->
                    <img src='../image.php?id=17184'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17184'
                        onclick='fetchAssetData(17184);' class='asset-image' data-id='<?php echo $assetId17184; ?>'
                        data-room='<?php echo htmlspecialchars($room17184); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17184); ?>'
                        data-image='<?php echo base64_encode($upload_img17184); ?>'
                        data-status='<?php echo htmlspecialchars($status17184); ?>'
                        data-category='<?php echo htmlspecialchars($category17184); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17184); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17184); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17185 -->
                    <img src='../image.php?id=17185'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17185'
                        onclick='fetchAssetData(17185);' class='asset-image' data-id='<?php echo $assetId17185; ?>'
                        data-room='<?php echo htmlspecialchars($room17185); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17185); ?>'
                        data-image='<?php echo base64_encode($upload_img17185); ?>'
                        data-status='<?php echo htmlspecialchars($status17185); ?>'
                        data-category='<?php echo htmlspecialchars($category17185); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17185); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17185); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17186 -->
                    <img src='../image.php?id=17186'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:740px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17186'
                        onclick='fetchAssetData(17186);' class='asset-image' data-id='<?php echo $assetId17186; ?>'
                        data-room='<?php echo htmlspecialchars($room17186); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17186); ?>'
                        data-image='<?php echo base64_encode($upload_img17186); ?>'
                        data-status='<?php echo htmlspecialchars($status17186); ?>'
                        data-category='<?php echo htmlspecialchars($category17186); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17186); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17186); ?>;
                        position:absolute; top:465px; left:750px;'>
                    </div>

                    <!-- ASSET 17187 -->
                    <img src='../image.php?id=17187'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:805px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17187'
                        onclick='fetchAssetData(17187);' class='asset-image' data-id='<?php echo $assetId17187; ?>'
                        data-room='<?php echo htmlspecialchars($room17187); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17187); ?>'
                        data-image='<?php echo base64_encode($upload_img17187); ?>'
                        data-status='<?php echo htmlspecialchars($status17187); ?>'
                        data-category='<?php echo htmlspecialchars($category17187); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17187); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17187); ?>;
                        position:absolute; top:465px; left:815px;'>
                    </div>

                    <!-- ASSET 17188 -->
                    <img src='../image.php?id=17188'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:740px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17188'
                        onclick='fetchAssetData(17188);' class='asset-image' data-id='<?php echo $assetId17188; ?>'
                        data-room='<?php echo htmlspecialchars($room17188); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17188); ?>'
                        data-image='<?php echo base64_encode($upload_img17188); ?>'
                        data-status='<?php echo htmlspecialchars($status17188); ?>'
                        data-category='<?php echo htmlspecialchars($category17188); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17188); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17188); ?>;
                        position:absolute; top:430px; left:750px;'>
                    </div>

                    <!-- ASSET 17189 -->
                    <img src='../image.php?id=17189'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:805px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17189'
                        onclick='fetchAssetData(17189);' class='asset-image' data-id='<?php echo $assetId17189; ?>'
                        data-room='<?php echo htmlspecialchars($room17189); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17189); ?>'
                        data-image='<?php echo base64_encode($upload_img17189); ?>'
                        data-status='<?php echo htmlspecialchars($status17189); ?>'
                        data-category='<?php echo htmlspecialchars($category17189); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17189); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17189); ?>;
                        position:absolute; top:430px; left:815px;'>
                    </div>

                    <!-- ASSET 17190 -->
                    <img src='../image.php?id=17190'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:850px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17190'
                        onclick='fetchAssetData(17190);' class='asset-image' data-id='<?php echo $assetId17190; ?>'
                        data-room='<?php echo htmlspecialchars($room17190); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17190); ?>'
                        data-image='<?php echo base64_encode($upload_img17190); ?>'
                        data-status='<?php echo htmlspecialchars($status17190); ?>'
                        data-category='<?php echo htmlspecialchars($category17190); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17190); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17190); ?>;
                        position:absolute; top:465px; left:860px;'>
                    </div>

                    <!-- ASSET 17191 -->
                    <img src='../image.php?id=17191'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:915px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17191'
                        onclick='fetchAssetData(17191);' class='asset-image' data-id='<?php echo $assetId17191; ?>'
                        data-room='<?php echo htmlspecialchars($room17191); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17191); ?>'
                        data-image='<?php echo base64_encode($upload_img17191); ?>'
                        data-status='<?php echo htmlspecialchars($status17191); ?>'
                        data-category='<?php echo htmlspecialchars($category17191); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17191); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17191); ?>;
                        position:absolute; top:465px; left:925px;'>
                    </div>

                    <!-- ASSET 17192 -->
                    <img src='../image.php?id=17192'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:850px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17192'
                        onclick='fetchAssetData(17192);' class='asset-image' data-id='<?php echo $assetId17192; ?>'
                        data-room='<?php echo htmlspecialchars($room17192); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17192); ?>'
                        data-image='<?php echo base64_encode($upload_img17192); ?>'
                        data-status='<?php echo htmlspecialchars($status17192); ?>'
                        data-category='<?php echo htmlspecialchars($category17192); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17192); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17192); ?>;
                        position:absolute; top:430px; left:860px;'>
                    </div>

                    <!-- ASSET 17193 -->
                    <img src='../image.php?id=17193'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:915px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17193'
                        onclick='fetchAssetData(17193);' class='asset-image' data-id='<?php echo $assetId17193; ?>'
                        data-room='<?php echo htmlspecialchars($room17193); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17193); ?>'
                        data-image='<?php echo base64_encode($upload_img17193); ?>'
                        data-status='<?php echo htmlspecialchars($status17193); ?>'
                        data-category='<?php echo htmlspecialchars($category17193); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17193); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17193); ?>;
                        position:absolute; top:430px; left:925px;'>
                    </div>

                    <!-- ASSET 17194 -->
                    <img src='../image.php?id=17194'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17194'
                        onclick='fetchAssetData(17194);' class='asset-image' data-id='<?php echo $assetId17194; ?>'
                        data-room='<?php echo htmlspecialchars($room17194); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17194); ?>'
                        data-image='<?php echo base64_encode($upload_img17194); ?>'
                        data-status='<?php echo htmlspecialchars($status17194); ?>'
                        data-category='<?php echo htmlspecialchars($category17194); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17194); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17194); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17195 -->
                    <img src='../image.php?id=17195'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17195'
                        onclick='fetchAssetData(17195);' class='asset-image' data-id='<?php echo $assetId17195; ?>'
                        data-room='<?php echo htmlspecialchars($room17195); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17195); ?>'
                        data-image='<?php echo base64_encode($upload_img17195); ?>'
                        data-status='<?php echo htmlspecialchars($status17195); ?>'
                        data-category='<?php echo htmlspecialchars($category17195); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17195); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17195); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17196 -->
                    <img src='../image.php?id=17196'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17196'
                        onclick='fetchAssetData(17196);' class='asset-image' data-id='<?php echo $assetId17196; ?>'
                        data-room='<?php echo htmlspecialchars($room17196); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17196); ?>'
                        data-image='<?php echo base64_encode($upload_img17196); ?>'
                        data-status='<?php echo htmlspecialchars($status17196); ?>'
                        data-category='<?php echo htmlspecialchars($category17196); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17196); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17196); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17197 -->
                    <img src='../image.php?id=17197'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17197'
                        onclick='fetchAssetData(17197);' class='asset-image' data-id='<?php echo $assetId17197; ?>'
                        data-room='<?php echo htmlspecialchars($room17197); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17197); ?>'
                        data-image='<?php echo base64_encode($upload_img17197); ?>'
                        data-status='<?php echo htmlspecialchars($status17197); ?>'
                        data-category='<?php echo htmlspecialchars($category17197); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17197); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17197); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17198 -->
                    <img src='../image.php?id=17198'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17198'
                        onclick='fetchAssetData(17198);' class='asset-image' data-id='<?php echo $assetId17198; ?>'
                        data-room='<?php echo htmlspecialchars($room17198); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17198); ?>'
                        data-image='<?php echo base64_encode($upload_img17198); ?>'
                        data-status='<?php echo htmlspecialchars($status17198); ?>'
                        data-category='<?php echo htmlspecialchars($category17198); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17198); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17198); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17199 -->
                    <img src='../image.php?id=17199'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17199'
                        onclick='fetchAssetData(17199);' class='asset-image' data-id='<?php echo $assetId17199; ?>'
                        data-room='<?php echo htmlspecialchars($room17199); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17199); ?>'
                        data-image='<?php echo base64_encode($upload_img17199); ?>'
                        data-status='<?php echo htmlspecialchars($status17199); ?>'
                        data-category='<?php echo htmlspecialchars($category17199); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17199); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17199); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17200 -->
                    <img src='../image.php?id=17200'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17200'
                        onclick='fetchAssetData(17200);' class='asset-image' data-id='<?php echo $assetId17200; ?>'
                        data-room='<?php echo htmlspecialchars($room17200); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17200); ?>'
                        data-image='<?php echo base64_encode($upload_img17200); ?>'
                        data-status='<?php echo htmlspecialchars($status17200); ?>'
                        data-category='<?php echo htmlspecialchars($category17200); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17200); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17200); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17201 -->
                    <img src='../image.php?id=17201'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17201'
                        onclick='fetchAssetData(17201);' class='asset-image' data-id='<?php echo $assetId17201; ?>'
                        data-room='<?php echo htmlspecialchars($room17201); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17201); ?>'
                        data-image='<?php echo base64_encode($upload_img17201); ?>'
                        data-status='<?php echo htmlspecialchars($status17201); ?>'
                        data-category='<?php echo htmlspecialchars($category17201); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17201); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17201); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17202 -->
                    <img src='../image.php?id=17202'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17202'
                        onclick='fetchAssetData(17202);' class='asset-image' data-id='<?php echo $assetId17202; ?>'
                        data-room='<?php echo htmlspecialchars($room17202); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17202); ?>'
                        data-image='<?php echo base64_encode($upload_img17202); ?>'
                        data-status='<?php echo htmlspecialchars($status17202); ?>'
                        data-category='<?php echo htmlspecialchars($category17202); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17202); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17202); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17203 -->
                    <img src='../image.php?id=17203'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17203'
                        onclick='fetchAssetData(17203);' class='asset-image' data-id='<?php echo $assetId17203; ?>'
                        data-room='<?php echo htmlspecialchars($room17203); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17203); ?>'
                        data-image='<?php echo base64_encode($upload_img17203); ?>'
                        data-status='<?php echo htmlspecialchars($status17203); ?>'
                        data-category='<?php echo htmlspecialchars($category17203); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17203); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17203); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17204 -->
                    <img src='../image.php?id=17204'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:960px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17204'
                        onclick='fetchAssetData(17204);' class='asset-image' data-id='<?php echo $assetId17204; ?>'
                        data-room='<?php echo htmlspecialchars($room17204); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17204); ?>'
                        data-image='<?php echo base64_encode($upload_img17204); ?>'
                        data-status='<?php echo htmlspecialchars($status17204); ?>'
                        data-category='<?php echo htmlspecialchars($category17204); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17204); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17204); ?>;
                        position:absolute; top:465px; left:970px;'>
                    </div>

                    <!-- ASSET 17205 -->
                    <img src='../image.php?id=17205'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:1020px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17205'
                        onclick='fetchAssetData(17205);' class='asset-image' data-id='<?php echo $assetId17205; ?>'
                        data-room='<?php echo htmlspecialchars($room17205); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17205); ?>'
                        data-image='<?php echo base64_encode($upload_img17205); ?>'
                        data-status='<?php echo htmlspecialchars($status17205); ?>'
                        data-category='<?php echo htmlspecialchars($category17205); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17205); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17205); ?>;
                        position:absolute; top:465px; left:1030px;'>
                    </div>

                    <!-- ASSET 17206 -->
                    <img src='../image.php?id=17206'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:960px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17206'
                        onclick='fetchAssetData(17206);' class='asset-image' data-id='<?php echo $assetId17206; ?>'
                        data-room='<?php echo htmlspecialchars($room17206); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17206); ?>'
                        data-image='<?php echo base64_encode($upload_img17206); ?>'
                        data-status='<?php echo htmlspecialchars($status17206); ?>'
                        data-category='<?php echo htmlspecialchars($category17206); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17206); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17206); ?>;
                        position:absolute; top:430px; left:970px;'>
                    </div>

                    <!-- ASSET 17207 -->
                    <img src='../image.php?id=17207'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:1020px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17207'
                        onclick='fetchAssetData(17207);' class='asset-image' data-id='<?php echo $assetId17207; ?>'
                        data-room='<?php echo htmlspecialchars($room17207); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17207); ?>'
                        data-image='<?php echo base64_encode($upload_img17207); ?>'
                        data-status='<?php echo htmlspecialchars($status17207); ?>'
                        data-category='<?php echo htmlspecialchars($category17207); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17207); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17207); ?>;
                        position:absolute; top:430px; left:1030px;'>
                    </div>

                    <!-- ASSET 17208 -->
                    <img src='../image.php?id=17208'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17208'
                        onclick='fetchAssetData(17208);' class='asset-image' data-id='<?php echo $assetId17208; ?>'
                        data-room='<?php echo htmlspecialchars($room17208); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17208); ?>'
                        data-image='<?php echo base64_encode($upload_img17208); ?>'
                        data-status='<?php echo htmlspecialchars($status17208); ?>'
                        data-category='<?php echo htmlspecialchars($category17208); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17208); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17208); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17209 -->
                    <img src='../image.php?id=17209'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17209'
                        onclick='fetchAssetData(17209);' class='asset-image' data-id='<?php echo $assetId17209; ?>'
                        data-room='<?php echo htmlspecialchars($room17209); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17209); ?>'
                        data-image='<?php echo base64_encode($upload_img17209); ?>'
                        data-status='<?php echo htmlspecialchars($status17209); ?>'
                        data-category='<?php echo htmlspecialchars($category17209); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17209); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17209); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17210 -->
                    <img src='../image.php?id=17210'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17210'
                        onclick='fetchAssetData(17210);' class='asset-image' data-id='<?php echo $assetId17210; ?>'
                        data-room='<?php echo htmlspecialchars($room17210); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17210); ?>'
                        data-image='<?php echo base64_encode($upload_img17210); ?>'
                        data-status='<?php echo htmlspecialchars($status17210); ?>'
                        data-category='<?php echo htmlspecialchars($category17210); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17210); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17210); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17211 -->
                    <img src='../image.php?id=17211'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17211'
                        onclick='fetchAssetData(17211);' class='asset-image' data-id='<?php echo $assetId17211; ?>'
                        data-room='<?php echo htmlspecialchars($room17211); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17211); ?>'
                        data-image='<?php echo base64_encode($upload_img17211); ?>'
                        data-status='<?php echo htmlspecialchars($status17211); ?>'
                        data-category='<?php echo htmlspecialchars($category17211); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17211); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17211); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17212 -->
                    <img src='../image.php?id=17212'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17212'
                        onclick='fetchAssetData(17212);' class='asset-image' data-id='<?php echo $assetId17212; ?>'
                        data-room='<?php echo htmlspecialchars($room17212); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17212); ?>'
                        data-image='<?php echo base64_encode($upload_img17212); ?>'
                        data-status='<?php echo htmlspecialchars($status17212); ?>'
                        data-category='<?php echo htmlspecialchars($category17212); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17212); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17212); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17213 -->
                    <img src='../image.php?id=17213'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17213'
                        onclick='fetchAssetData(17213);' class='asset-image' data-id='<?php echo $assetId17213; ?>'
                        data-room='<?php echo htmlspecialchars($room17213); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17213); ?>'
                        data-image='<?php echo base64_encode($upload_img17213); ?>'
                        data-status='<?php echo htmlspecialchars($status17213); ?>'
                        data-category='<?php echo htmlspecialchars($category17213); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17213); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17213); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17214 -->
                    <img src='../image.php?id=17214'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17214'
                        onclick='fetchAssetData(17214);' class='asset-image' data-id='<?php echo $assetId17214; ?>'
                        data-room='<?php echo htmlspecialchars($room17214); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17214); ?>'
                        data-image='<?php echo base64_encode($upload_img17214); ?>'
                        data-status='<?php echo htmlspecialchars($status17214); ?>'
                        data-category='<?php echo htmlspecialchars($category17214); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17214); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17214); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17215 -->
                    <img src='../image.php?id=17215'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17215'
                        onclick='fetchAssetData(17215);' class='asset-image' data-id='<?php echo $assetId17215; ?>'
                        data-room='<?php echo htmlspecialchars($room17215); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17215); ?>'
                        data-image='<?php echo base64_encode($upload_img17215); ?>'
                        data-status='<?php echo htmlspecialchars($status17215); ?>'
                        data-category='<?php echo htmlspecialchars($category17215); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17215); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17215); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17216 -->
                    <img src='../image.php?id=17216'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17216'
                        onclick='fetchAssetData(17216);' class='asset-image' data-id='<?php echo $assetId17216; ?>'
                        data-room='<?php echo htmlspecialchars($room17216); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17216); ?>'
                        data-image='<?php echo base64_encode($upload_img17216); ?>'
                        data-status='<?php echo htmlspecialchars($status17216); ?>'
                        data-category='<?php echo htmlspecialchars($category17216); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17216); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17216); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17217 -->
                    <img src='../image.php?id=17217'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17217'
                        onclick='fetchAssetData(17217);' class='asset-image' data-id='<?php echo $assetId17217; ?>'
                        data-room='<?php echo htmlspecialchars($room17217); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17217); ?>'
                        data-image='<?php echo base64_encode($upload_img17217); ?>'
                        data-status='<?php echo htmlspecialchars($status17217); ?>'
                        data-category='<?php echo htmlspecialchars($category17217); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17217); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17217); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17218 -->
                    <img src='../image.php?id=17218'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:145px; left:950px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17218'
                        onclick='fetchAssetData(17218);' class='asset-image' data-id='<?php echo $assetId17218; ?>'
                        data-room='<?php echo htmlspecialchars($room17218); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17218); ?>'
                        data-image='<?php echo base64_encode($upload_img17218); ?>'
                        data-status='<?php echo htmlspecialchars($status17218); ?>'
                        data-category='<?php echo htmlspecialchars($category17218); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17218); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17218); ?>;
                        position:absolute; top:140px; left:960px;'>
                    </div>

                    <!-- ASSET 17219 -->
                    <img src='../image.php?id=17219'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:175px; left:1025px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17219'
                        onclick='fetchAssetData(17219);' class='asset-image' data-id='<?php echo $assetId17219; ?>'
                        data-room='<?php echo htmlspecialchars($room17219); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17219); ?>'
                        data-image='<?php echo base64_encode($upload_img17219); ?>'
                        data-status='<?php echo htmlspecialchars($status17219); ?>'
                        data-category='<?php echo htmlspecialchars($category17219); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17219); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17219); ?>;
                        position:absolute; top:170px; left:1035px;'>
                    </div>

                    <!-- ASSET 17220 -->
                    <img src='../image.php?id=17220'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:175px; left:950px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17220'
                        onclick='fetchAssetData(17220);' class='asset-image' data-id='<?php echo $assetId17220; ?>'
                        data-room='<?php echo htmlspecialchars($room17220); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17220); ?>'
                        data-image='<?php echo base64_encode($upload_img17220); ?>'
                        data-status='<?php echo htmlspecialchars($status17220); ?>'
                        data-category='<?php echo htmlspecialchars($category17220); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17220); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17220); ?>;
                        position:absolute; top:170px; left:960px;'>
                    </div>

                    <!-- ASSET 17221 -->
                    <img src='../image.php?id=17221'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:145px; left:1025px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17221'
                        onclick='fetchAssetData(17221);' class='asset-image' data-id='<?php echo $assetId17221; ?>'
                        data-room='<?php echo htmlspecialchars($room17221); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17221); ?>'
                        data-image='<?php echo base64_encode($upload_img17221); ?>'
                        data-status='<?php echo htmlspecialchars($status17221); ?>'
                        data-category='<?php echo htmlspecialchars($category17221); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17221); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17221); ?>;
                        position:absolute; top:140px; left:1035px;'>
                    </div>

                    <!-- ASSET 17222 -->
                    <img src='../image.php?id=17222'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17222'
                        onclick='fetchAssetData(17222);' class='asset-image' data-id='<?php echo $assetId17222; ?>'
                        data-room='<?php echo htmlspecialchars($room17222); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17222); ?>'
                        data-image='<?php echo base64_encode($upload_img17222); ?>'
                        data-status='<?php echo htmlspecialchars($status17222); ?>'
                        data-category='<?php echo htmlspecialchars($category17222); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17222); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17222); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17223 -->
                    <img src='../image.php?id=17223'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17223'
                        onclick='fetchAssetData(17223);' class='asset-image' data-id='<?php echo $assetId17223; ?>'
                        data-room='<?php echo htmlspecialchars($room17223); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17223); ?>'
                        data-image='<?php echo base64_encode($upload_img17223); ?>'
                        data-status='<?php echo htmlspecialchars($status17223); ?>'
                        data-category='<?php echo htmlspecialchars($category17223); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17223); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17223); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17224 -->
                    <img src='../image.php?id=17224'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17224'
                        onclick='fetchAssetData(17224);' class='asset-image' data-id='<?php echo $assetId17224; ?>'
                        data-room='<?php echo htmlspecialchars($room17224); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17224); ?>'
                        data-image='<?php echo base64_encode($upload_img17224); ?>'
                        data-status='<?php echo htmlspecialchars($status17224); ?>'
                        data-category='<?php echo htmlspecialchars($category17224); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17224); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17224); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17225 -->
                    <img src='../image.php?id=17225'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17225'
                        onclick='fetchAssetData(17225);' class='asset-image' data-id='<?php echo $assetId17225; ?>'
                        data-room='<?php echo htmlspecialchars($room17225); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17225); ?>'
                        data-image='<?php echo base64_encode($upload_img17225); ?>'
                        data-status='<?php echo htmlspecialchars($status17225); ?>'
                        data-category='<?php echo htmlspecialchars($category17225); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17225); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17225); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17226 -->
                    <img src='../image.php?id=17226'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17226'
                        onclick='fetchAssetData(17226);' class='asset-image' data-id='<?php echo $assetId17226; ?>'
                        data-room='<?php echo htmlspecialchars($room17226); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17226); ?>'
                        data-image='<?php echo base64_encode($upload_img17226); ?>'
                        data-status='<?php echo htmlspecialchars($status17226); ?>'
                        data-category='<?php echo htmlspecialchars($category17226); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17226); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17226); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17227 -->
                    <img src='../image.php?id=17227'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17227'
                        onclick='fetchAssetData(17227);' class='asset-image' data-id='<?php echo $assetId17227; ?>'
                        data-room='<?php echo htmlspecialchars($room17227); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17227); ?>'
                        data-image='<?php echo base64_encode($upload_img17227); ?>'
                        data-status='<?php echo htmlspecialchars($status17227); ?>'
                        data-category='<?php echo htmlspecialchars($category17227); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17227); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17227); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17228 -->
                    <img src='../image.php?id=17228'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17228'
                        onclick='fetchAssetData(17228);' class='asset-image' data-id='<?php echo $assetId17228; ?>'
                        data-room='<?php echo htmlspecialchars($room17228); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17228); ?>'
                        data-image='<?php echo base64_encode($upload_img17228); ?>'
                        data-status='<?php echo htmlspecialchars($status17228); ?>'
                        data-category='<?php echo htmlspecialchars($category17228); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17228); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17228); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17229 -->
                    <img src='../image.php?id=17229'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17229'
                        onclick='fetchAssetData(17229);' class='asset-image' data-id='<?php echo $assetId17229; ?>'
                        data-room='<?php echo htmlspecialchars($room17229); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17229); ?>'
                        data-image='<?php echo base64_encode($upload_img17229); ?>'
                        data-status='<?php echo htmlspecialchars($status17229); ?>'
                        data-category='<?php echo htmlspecialchars($category17229); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17229); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17229); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17230 -->
                    <img src='../image.php?id=17230'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17230'
                        onclick='fetchAssetData(17230);' class='asset-image' data-id='<?php echo $assetId17230; ?>'
                        data-room='<?php echo htmlspecialchars($room17230); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17230); ?>'
                        data-image='<?php echo base64_encode($upload_img17230); ?>'
                        data-status='<?php echo htmlspecialchars($status17230); ?>'
                        data-category='<?php echo htmlspecialchars($category17230); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17230); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17230); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17231 -->
                    <img src='../image.php?id=17231'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17231'
                        onclick='fetchAssetData(17231);' class='asset-image' data-id='<?php echo $assetId17231; ?>'
                        data-room='<?php echo htmlspecialchars($room17231); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17231); ?>'
                        data-image='<?php echo base64_encode($upload_img17231); ?>'
                        data-status='<?php echo htmlspecialchars($status17231); ?>'
                        data-category='<?php echo htmlspecialchars($category17231); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17231); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17231); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17232
                    <img src='../image.php?id=17232'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:145px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17232'
                        onclick='fetchAssetData(17232);' class='asset-image' data-id='<?php echo $assetId17232; ?>'
                        data-room='<?php echo htmlspecialchars($room17232); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17232); ?>'
                        data-image='<?php echo base64_encode($upload_img17232); ?>'
                        data-status='<?php echo htmlspecialchars($status17232); ?>'
                        data-category='<?php echo htmlspecialchars($category17232); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17232); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17232); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div> -->

                    <!-- ASSET 17233
                    <img src='../image.php?id=17233'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:175px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17233'
                        onclick='fetchAssetData(17233);' class='asset-image' data-id='<?php echo $assetId17233; ?>'
                        data-room='<?php echo htmlspecialchars($room17233); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17233); ?>'
                        data-image='<?php echo base64_encode($upload_img17233); ?>'
                        data-status='<?php echo htmlspecialchars($status17233); ?>'
                        data-category='<?php echo htmlspecialchars($category17233); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17233); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17233); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div> -->

                    <!-- ASSET 17234
                    <img src='../image.php?id=17234'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:145px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17234'
                        onclick='fetchAssetData(17234);' class='asset-image' data-id='<?php echo $assetId17234; ?>'
                        data-room='<?php echo htmlspecialchars($room17234); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17234); ?>'
                        data-image='<?php echo base64_encode($upload_img17234); ?>'
                        data-status='<?php echo htmlspecialchars($status17234); ?>'
                        data-category='<?php echo htmlspecialchars($category17234); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17234); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17234); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div> -->

                    <!-- ASSET 17235 -->
                    <!-- <img src='../image.php?id=17235'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:175px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17235'
                        onclick='fetchAssetData(17235);' class='asset-image' data-id='<?php echo $assetId17235; ?>'
                        data-room='<?php echo htmlspecialchars($room17235); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17235); ?>'
                        data-image='<?php echo base64_encode($upload_img17235); ?>'
                        data-status='<?php echo htmlspecialchars($status17235); ?>'
                        data-category='<?php echo htmlspecialchars($category17235); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17235); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17235); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div> -->

                    <!-- ASSET 17236 -->
                    <img src='../image.php?id=17236'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:250px; left:1060px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17236'
                        onclick='fetchAssetData(17236);' class='asset-image' data-id='<?php echo $assetId17236; ?>'
                        data-room='<?php echo htmlspecialchars($room17236); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17236); ?>'
                        data-image='<?php echo base64_encode($upload_img17236); ?>'
                        data-status='<?php echo htmlspecialchars($status17236); ?>'
                        data-category='<?php echo htmlspecialchars($category17236); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17236); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17236); ?>;
                        position:absolute; top:245px; left:1070px;'>
                    </div>

                    <!-- ASSET 17237 -->
                    <img src='../image.php?id=17237'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:250px; left:1125px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17237'
                        onclick='fetchAssetData(17237);' class='asset-image' data-id='<?php echo $assetId17237; ?>'
                        data-room='<?php echo htmlspecialchars($room17237); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17237); ?>'
                        data-image='<?php echo base64_encode($upload_img17237); ?>'
                        data-status='<?php echo htmlspecialchars($status17237); ?>'
                        data-category='<?php echo htmlspecialchars($category17237); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17237); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17237); ?>;
                        position:absolute; top:245px; left:1135px;'>
                    </div>

                    <!-- ASSET 17238 -->
                    <img src='../image.php?id=17238'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:150px; left:1060px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17238'
                        onclick='fetchAssetData(17238);' class='asset-image' data-id='<?php echo $assetId17238; ?>'
                        data-room='<?php echo htmlspecialchars($room17238); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17238); ?>'
                        data-image='<?php echo base64_encode($upload_img17238); ?>'
                        data-status='<?php echo htmlspecialchars($status17238); ?>'
                        data-category='<?php echo htmlspecialchars($category17238); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17238); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17238); ?>;
                        position:absolute; top:145px; left:1070px;'>
                    </div>

                    <!-- ASSET 17239 -->
                    <img src='../image.php?id=17239'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:150px; left:1125px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17239'
                        onclick='fetchAssetData(17239);' class='asset-image' data-id='<?php echo $assetId17239; ?>'
                        data-room='<?php echo htmlspecialchars($room17239); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17239); ?>'
                        data-image='<?php echo base64_encode($upload_img17239); ?>'
                        data-status='<?php echo htmlspecialchars($status17239); ?>'
                        data-category='<?php echo htmlspecialchars($category17239); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17239); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17239); ?>;
                        position:absolute; top:145px; left:1135px;'>
                    </div>

                    <!-- ASSET 17240 -->
                    <img src='../image.php?id=17240'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:1070px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17240'
                        onclick='fetchAssetData(17240);' class='asset-image' data-id='<?php echo $assetId17240; ?>'
                        data-room='<?php echo htmlspecialchars($room17240); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17240); ?>'
                        data-image='<?php echo base64_encode($upload_img17240); ?>'
                        data-status='<?php echo htmlspecialchars($status17240); ?>'
                        data-category='<?php echo htmlspecialchars($category17240); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17240); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17240); ?>;
                        position:absolute; top:405px; left:1080px;'>
                    </div>

                    <!-- ASSET 17241 -->
                    <img src='../image.php?id=17241'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:360px; left:1070px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17241'
                        onclick='fetchAssetData(17241);' class='asset-image' data-id='<?php echo $assetId17241; ?>'
                        data-room='<?php echo htmlspecialchars($room17241); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17241); ?>'
                        data-image='<?php echo base64_encode($upload_img17241); ?>'
                        data-status='<?php echo htmlspecialchars($status17241); ?>'
                        data-category='<?php echo htmlspecialchars($category17241); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17241); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17241); ?>;
                        position:absolute; top:355px; left:1080px;'>
                    </div>

                    <!-- ASSET 17242 -->
                    <img src='../image.php?id=17242'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:1065px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17242'
                        onclick='fetchAssetData(17242);' class='asset-image' data-id='<?php echo $assetId17242; ?>'
                        data-room='<?php echo htmlspecialchars($room17242); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17242); ?>'
                        data-image='<?php echo base64_encode($upload_img17242); ?>'
                        data-status='<?php echo htmlspecialchars($status17242); ?>'
                        data-category='<?php echo htmlspecialchars($category17242); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17242); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17242); ?>;
                        position:absolute; top:465px; left:1075px;'>
                    </div>

                    <!-- ASSET 17243 -->
                    <img src='../image.php?id=17243'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:470px; left:1120px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17243'
                        onclick='fetchAssetData(17243);' class='asset-image' data-id='<?php echo $assetId17243; ?>'
                        data-room='<?php echo htmlspecialchars($room17243); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17243); ?>'
                        data-image='<?php echo base64_encode($upload_img17243); ?>'
                        data-status='<?php echo htmlspecialchars($status17243); ?>'
                        data-category='<?php echo htmlspecialchars($category17243); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17243); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17243); ?>;
                        position:absolute; top:465px; left:1130px;'>
                    </div>

                    <!-- ASSET 17244 -->
                    <img src='../image.php?id=17244'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:1065px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17244'
                        onclick='fetchAssetData(17244);' class='asset-image' data-id='<?php echo $assetId17244; ?>'
                        data-room='<?php echo htmlspecialchars($room17244); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17244); ?>'
                        data-image='<?php echo base64_encode($upload_img17244); ?>'
                        data-status='<?php echo htmlspecialchars($status17244); ?>'
                        data-category='<?php echo htmlspecialchars($category17244); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17244); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17244); ?>;
                        position:absolute; top:430px; left:1075px;'>
                    </div>

                    <!-- ASSET 17245 -->
                    <img src='../image.php?id=17245'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:435px; left:1120px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17245'
                        onclick='fetchAssetData(17245);' class='asset-image' data-id='<?php echo $assetId17245; ?>'
                        data-room='<?php echo htmlspecialchars($room17245); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17245); ?>'
                        data-image='<?php echo base64_encode($upload_img17245); ?>'
                        data-status='<?php echo htmlspecialchars($status17245); ?>'
                        data-category='<?php echo htmlspecialchars($category17245); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17245); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17245); ?>;
                        position:absolute; top:430px; left:1130px;'>
                    </div>

                    <!-- ASSET 17246 -->
                    <img src='../image.php?id=17246'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17246'
                        onclick='fetchAssetData(17246);' class='asset-image' data-id='<?php echo $assetId17246; ?>'
                        data-room='<?php echo htmlspecialchars($room17246); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17246); ?>'
                        data-image='<?php echo base64_encode($upload_img17246); ?>'
                        data-status='<?php echo htmlspecialchars($status17246); ?>'
                        data-category='<?php echo htmlspecialchars($category17246); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17246); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17246); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17247 -->
                    <img src='../image.php?id=17247'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17247'
                        onclick='fetchAssetData(17247);' class='asset-image' data-id='<?php echo $assetId17247; ?>'
                        data-room='<?php echo htmlspecialchars($room17247); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17247); ?>'
                        data-image='<?php echo base64_encode($upload_img17247); ?>'
                        data-status='<?php echo htmlspecialchars($status17247); ?>'
                        data-category='<?php echo htmlspecialchars($category17247); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17247); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17247); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17248 -->
                    <img src='../image.php?id=17248'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17248'
                        onclick='fetchAssetData(17248);' class='asset-image' data-id='<?php echo $assetId17248; ?>'
                        data-room='<?php echo htmlspecialchars($room17248); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17248); ?>'
                        data-image='<?php echo base64_encode($upload_img17248); ?>'
                        data-status='<?php echo htmlspecialchars($status17248); ?>'
                        data-category='<?php echo htmlspecialchars($category17248); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17248); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17248); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17249 -->
                    <img src='../image.php?id=17249'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17249'
                        onclick='fetchAssetData(17249);' class='asset-image' data-id='<?php echo $assetId17249; ?>'
                        data-room='<?php echo htmlspecialchars($room17249); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17249); ?>'
                        data-image='<?php echo base64_encode($upload_img17249); ?>'
                        data-status='<?php echo htmlspecialchars($status17249); ?>'
                        data-category='<?php echo htmlspecialchars($category17249); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17249); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17249); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17250 -->
                    <img src='../image.php?id=17250'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17250'
                        onclick='fetchAssetData(17250);' class='asset-image' data-id='<?php echo $assetId17250; ?>'
                        data-room='<?php echo htmlspecialchars($room17250); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17250); ?>'
                        data-image='<?php echo base64_encode($upload_img17250); ?>'
                        data-status='<?php echo htmlspecialchars($status17250); ?>'
                        data-category='<?php echo htmlspecialchars($category17250); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17250); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17250); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17251 -->
                    <img src='../image.php?id=17251'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17251'
                        onclick='fetchAssetData(17251);' class='asset-image' data-id='<?php echo $assetId17251; ?>'
                        data-room='<?php echo htmlspecialchars($room17251); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17251); ?>'
                        data-image='<?php echo base64_encode($upload_img17251); ?>'
                        data-status='<?php echo htmlspecialchars($status17251); ?>'
                        data-category='<?php echo htmlspecialchars($category17251); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17251); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17251); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17252 -->
                    <img src='../image.php?id=17252'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17252'
                        onclick='fetchAssetData(17252);' class='asset-image' data-id='<?php echo $assetId17252; ?>'
                        data-room='<?php echo htmlspecialchars($room17252); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17252); ?>'
                        data-image='<?php echo base64_encode($upload_img17252); ?>'
                        data-status='<?php echo htmlspecialchars($status17252); ?>'
                        data-category='<?php echo htmlspecialchars($category17252); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17252); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17252); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17253 -->
                    <img src='../image.php?id=17253'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17253'
                        onclick='fetchAssetData(17253);' class='asset-image' data-id='<?php echo $assetId17253; ?>'
                        data-room='<?php echo htmlspecialchars($room17253); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17253); ?>'
                        data-image='<?php echo base64_encode($upload_img17253); ?>'
                        data-status='<?php echo htmlspecialchars($status17253); ?>'
                        data-category='<?php echo htmlspecialchars($category17253); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17253); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17253); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17254 -->
                    <img src='../image.php?id=17254'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17254'
                        onclick='fetchAssetData(17254);' class='asset-image' data-id='<?php echo $assetId17254; ?>'
                        data-room='<?php echo htmlspecialchars($room17254); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17254); ?>'
                        data-image='<?php echo base64_encode($upload_img17254); ?>'
                        data-status='<?php echo htmlspecialchars($status17254); ?>'
                        data-category='<?php echo htmlspecialchars($category17254); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17254); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17254); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17255 -->
                    <img src='../image.php?id=17255'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17255'
                        onclick='fetchAssetData(17255);' class='asset-image' data-id='<?php echo $assetId17255; ?>'
                        data-room='<?php echo htmlspecialchars($room17255); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17255); ?>'
                        data-image='<?php echo base64_encode($upload_img17255); ?>'
                        data-status='<?php echo htmlspecialchars($status17255); ?>'
                        data-category='<?php echo htmlspecialchars($category17255); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17255); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17255); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17256 -->
                    <img src='../image.php?id=17256'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17256'
                        onclick='fetchAssetData(17256);' class='asset-image' data-id='<?php echo $assetId17256; ?>'
                        data-room='<?php echo htmlspecialchars($room17256); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17256); ?>'
                        data-image='<?php echo base64_encode($upload_img17256); ?>'
                        data-status='<?php echo htmlspecialchars($status17256); ?>'
                        data-category='<?php echo htmlspecialchars($category17256); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17256); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17256); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17257 -->
                    <img src='../image.php?id=17257'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17257'
                        onclick='fetchAssetData(17257);' class='asset-image' data-id='<?php echo $assetId17257; ?>'
                        data-room='<?php echo htmlspecialchars($room17257); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17257); ?>'
                        data-image='<?php echo base64_encode($upload_img17257); ?>'
                        data-status='<?php echo htmlspecialchars($status17257); ?>'
                        data-category='<?php echo htmlspecialchars($category17257); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17257); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17257); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17258 -->
                    <img src='../image.php?id=17258'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17258'
                        onclick='fetchAssetData(17258);' class='asset-image' data-id='<?php echo $assetId17258; ?>'
                        data-room='<?php echo htmlspecialchars($room17258); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17258); ?>'
                        data-image='<?php echo base64_encode($upload_img17258); ?>'
                        data-status='<?php echo htmlspecialchars($status17258); ?>'
                        data-category='<?php echo htmlspecialchars($category17258); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17258); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17258); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17259 -->
                    <img src='../image.php?id=17259'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17259'
                        onclick='fetchAssetData(17259);' class='asset-image' data-id='<?php echo $assetId17259; ?>'
                        data-room='<?php echo htmlspecialchars($room17259); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17259); ?>'
                        data-image='<?php echo base64_encode($upload_img17259); ?>'
                        data-status='<?php echo htmlspecialchars($status17259); ?>'
                        data-category='<?php echo htmlspecialchars($category17259); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17259); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17259); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17260 -->
                    <img src='../image.php?id=17260'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17260'
                        onclick='fetchAssetData(17260);' class='asset-image' data-id='<?php echo $assetId17260; ?>'
                        data-room='<?php echo htmlspecialchars($room17260); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17260); ?>'
                        data-image='<?php echo base64_encode($upload_img17260); ?>'
                        data-status='<?php echo htmlspecialchars($status17260); ?>'
                        data-category='<?php echo htmlspecialchars($category17260); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17260); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17260); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17261 -->
                    <img src='../image.php?id=17261'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17261'
                        onclick='fetchAssetData(17261);' class='asset-image' data-id='<?php echo $assetId17261; ?>'
                        data-room='<?php echo htmlspecialchars($room17261); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17261); ?>'
                        data-image='<?php echo base64_encode($upload_img17261); ?>'
                        data-status='<?php echo htmlspecialchars($status17261); ?>'
                        data-category='<?php echo htmlspecialchars($category17261); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17261); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17261); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17262 -->
                    <img src='../image.php?id=17262'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17262'
                        onclick='fetchAssetData(17262);' class='asset-image' data-id='<?php echo $assetId17262; ?>'
                        data-room='<?php echo htmlspecialchars($room17262); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17262); ?>'
                        data-image='<?php echo base64_encode($upload_img17262); ?>'
                        data-status='<?php echo htmlspecialchars($status17262); ?>'
                        data-category='<?php echo htmlspecialchars($category17262); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17262); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17262); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17263 -->
                    <img src='../image.php?id=17263'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17263'
                        onclick='fetchAssetData(17263);' class='asset-image' data-id='<?php echo $assetId17263; ?>'
                        data-room='<?php echo htmlspecialchars($room17263); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17263); ?>'
                        data-image='<?php echo base64_encode($upload_img17263); ?>'
                        data-status='<?php echo htmlspecialchars($status17263); ?>'
                        data-category='<?php echo htmlspecialchars($category17263); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17263); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17263); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17264 -->
                    <img src='../image.php?id=17264'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17264'
                        onclick='fetchAssetData(17264);' class='asset-image' data-id='<?php echo $assetId17264; ?>'
                        data-room='<?php echo htmlspecialchars($room17264); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17264); ?>'
                        data-image='<?php echo base64_encode($upload_img17264); ?>'
                        data-status='<?php echo htmlspecialchars($status17264); ?>'
                        data-category='<?php echo htmlspecialchars($category17264); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17264); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17264); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17265 -->
                    <img src='../image.php?id=17265'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17265'
                        onclick='fetchAssetData(17265);' class='asset-image' data-id='<?php echo $assetId17265; ?>'
                        data-room='<?php echo htmlspecialchars($room17265); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17265); ?>'
                        data-image='<?php echo base64_encode($upload_img17265); ?>'
                        data-status='<?php echo htmlspecialchars($status17265); ?>'
                        data-category='<?php echo htmlspecialchars($category17265); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17265); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17265); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17266 -->
                    <img src='../image.php?id=17266'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17266'
                        onclick='fetchAssetData(17266);' class='asset-image' data-id='<?php echo $assetId17266; ?>'
                        data-room='<?php echo htmlspecialchars($room17266); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17266); ?>'
                        data-image='<?php echo base64_encode($upload_img17266); ?>'
                        data-status='<?php echo htmlspecialchars($status17266); ?>'
                        data-category='<?php echo htmlspecialchars($category17266); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17266); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17266); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17267 -->
                    <img src='../image.php?id=17267'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17267'
                        onclick='fetchAssetData(17267);' class='asset-image' data-id='<?php echo $assetId17267; ?>'
                        data-room='<?php echo htmlspecialchars($room17267); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17267); ?>'
                        data-image='<?php echo base64_encode($upload_img17267); ?>'
                        data-status='<?php echo htmlspecialchars($status17267); ?>'
                        data-category='<?php echo htmlspecialchars($category17267); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17267); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17267); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17268 -->
                    <img src='../image.php?id=17268'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17268'
                        onclick='fetchAssetData(17268);' class='asset-image' data-id='<?php echo $assetId17268; ?>'
                        data-room='<?php echo htmlspecialchars($room17268); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17268); ?>'
                        data-image='<?php echo base64_encode($upload_img17268); ?>'
                        data-status='<?php echo htmlspecialchars($status17268); ?>'
                        data-category='<?php echo htmlspecialchars($category17268); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17268); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17268); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17269 -->
                    <img src='../image.php?id=17269'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17269'
                        onclick='fetchAssetData(17269);' class='asset-image' data-id='<?php echo $assetId17269; ?>'
                        data-room='<?php echo htmlspecialchars($room17269); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17269); ?>'
                        data-image='<?php echo base64_encode($upload_img17269); ?>'
                        data-status='<?php echo htmlspecialchars($status17269); ?>'
                        data-category='<?php echo htmlspecialchars($category17269); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17269); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17269); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17270 -->
                    <img src='../image.php?id=17270'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17270'
                        onclick='fetchAssetData(17270);' class='asset-image' data-id='<?php echo $assetId17270; ?>'
                        data-room='<?php echo htmlspecialchars($room17270); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17270); ?>'
                        data-image='<?php echo base64_encode($upload_img17270); ?>'
                        data-status='<?php echo htmlspecialchars($status17270); ?>'
                        data-category='<?php echo htmlspecialchars($category17270); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17270); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17270); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17271 -->
                    <img src='../image.php?id=17271'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17271'
                        onclick='fetchAssetData(17271);' class='asset-image' data-id='<?php echo $assetId17271; ?>'
                        data-room='<?php echo htmlspecialchars($room17271); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17271); ?>'
                        data-image='<?php echo base64_encode($upload_img17271); ?>'
                        data-status='<?php echo htmlspecialchars($status17271); ?>'
                        data-category='<?php echo htmlspecialchars($category17271); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17271); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17271); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17272 -->
                    <img src='../image.php?id=17272'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17272'
                        onclick='fetchAssetData(17272);' class='asset-image' data-id='<?php echo $assetId17272; ?>'
                        data-room='<?php echo htmlspecialchars($room17272); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17272); ?>'
                        data-image='<?php echo base64_encode($upload_img17272); ?>'
                        data-status='<?php echo htmlspecialchars($status17272); ?>'
                        data-category='<?php echo htmlspecialchars($category17272); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17272); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17272); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17273 -->
                    <img src='../image.php?id=17273'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17273'
                        onclick='fetchAssetData(17273);' class='asset-image' data-id='<?php echo $assetId17273; ?>'
                        data-room='<?php echo htmlspecialchars($room17273); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17273); ?>'
                        data-image='<?php echo base64_encode($upload_img17273); ?>'
                        data-status='<?php echo htmlspecialchars($status17273); ?>'
                        data-category='<?php echo htmlspecialchars($category17273); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17273); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17273); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17274 -->
                    <img src='../image.php?id=17274'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17274'
                        onclick='fetchAssetData(17274);' class='asset-image' data-id='<?php echo $assetId17274; ?>'
                        data-room='<?php echo htmlspecialchars($room17274); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17274); ?>'
                        data-image='<?php echo base64_encode($upload_img17274); ?>'
                        data-status='<?php echo htmlspecialchars($status17274); ?>'
                        data-category='<?php echo htmlspecialchars($category17274); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17274); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17274); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17275 -->
                    <img src='../image.php?id=17275'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17275'
                        onclick='fetchAssetData(17275);' class='asset-image' data-id='<?php echo $assetId17275; ?>'
                        data-room='<?php echo htmlspecialchars($room17275); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17275); ?>'
                        data-image='<?php echo base64_encode($upload_img17275); ?>'
                        data-status='<?php echo htmlspecialchars($status17275); ?>'
                        data-category='<?php echo htmlspecialchars($category17275); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17275); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17275); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17276 -->
                    <img src='../image.php?id=17276'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17276'
                        onclick='fetchAssetData(17276);' class='asset-image' data-id='<?php echo $assetId17276; ?>'
                        data-room='<?php echo htmlspecialchars($room17276); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17276); ?>'
                        data-image='<?php echo base64_encode($upload_img17276); ?>'
                        data-status='<?php echo htmlspecialchars($status17276); ?>'
                        data-category='<?php echo htmlspecialchars($category17276); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17276); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17276); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17277 -->
                    <img src='../image.php?id=17277'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17277'
                        onclick='fetchAssetData(17277);' class='asset-image' data-id='<?php echo $assetId17277; ?>'
                        data-room='<?php echo htmlspecialchars($room17277); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17277); ?>'
                        data-image='<?php echo base64_encode($upload_img17277); ?>'
                        data-status='<?php echo htmlspecialchars($status17277); ?>'
                        data-category='<?php echo htmlspecialchars($category17277); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17277); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17277); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 17278 -->
                    <img src='../image.php?id=17278'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal17278'
                        onclick='fetchAssetData(17278);' class='asset-image' data-id='<?php echo $assetId17278; ?>'
                        data-room='<?php echo htmlspecialchars($room17278); ?>'
                        data-floor='<?php echo htmlspecialchars($floor17278); ?>'
                        data-image='<?php echo base64_encode($upload_img17278); ?>'
                        data-status='<?php echo htmlspecialchars($status17278); ?>'
                        data-category='<?php echo htmlspecialchars($category17278); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName17278); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status17278); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18188 -->
                    <img src='../image.php?id=18188'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18188'
                        onclick='fetchAssetData(18188);' class='asset-image' data-id='<?php echo $assetId18188; ?>'
                        data-room='<?php echo htmlspecialchars($room18188); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18188); ?>'
                        data-image='<?php echo base64_encode($upload_img18188); ?>'
                        data-status='<?php echo htmlspecialchars($status18188); ?>'
                        data-category='<?php echo htmlspecialchars($category18188); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18188); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18188); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18189 -->
                    <img src='../image.php?id=18189'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18189'
                        onclick='fetchAssetData(18189);' class='asset-image' data-id='<?php echo $assetId18189; ?>'
                        data-room='<?php echo htmlspecialchars($room18189); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18189); ?>'
                        data-image='<?php echo base64_encode($upload_img18189); ?>'
                        data-status='<?php echo htmlspecialchars($status18189); ?>'
                        data-category='<?php echo htmlspecialchars($category18189); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18189); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18189); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18190 -->
                    <img src='../image.php?id=18190'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18190'
                        onclick='fetchAssetData(18190);' class='asset-image' data-id='<?php echo $assetId18190; ?>'
                        data-room='<?php echo htmlspecialchars($room18190); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18190); ?>'
                        data-image='<?php echo base64_encode($upload_img18190); ?>'
                        data-status='<?php echo htmlspecialchars($status18190); ?>'
                        data-category='<?php echo htmlspecialchars($category18190); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18190); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18190); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18191 -->
                    <img src='../image.php?id=18191'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18191'
                        onclick='fetchAssetData(18191);' class='asset-image' data-id='<?php echo $assetId18191; ?>'
                        data-room='<?php echo htmlspecialchars($room18191); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18191); ?>'
                        data-image='<?php echo base64_encode($upload_img18191); ?>'
                        data-status='<?php echo htmlspecialchars($status18191); ?>'
                        data-category='<?php echo htmlspecialchars($category18191); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18191); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18191); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18192 -->
                    <img src='../image.php?id=18192'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18192'
                        onclick='fetchAssetData(18192);' class='asset-image' data-id='<?php echo $assetId18192; ?>'
                        data-room='<?php echo htmlspecialchars($room18192); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18192); ?>'
                        data-image='<?php echo base64_encode($upload_img18192); ?>'
                        data-status='<?php echo htmlspecialchars($status18192); ?>'
                        data-category='<?php echo htmlspecialchars($category18192); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18192); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18192); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18193 -->
                    <img src='../image.php?id=18193'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18193'
                        onclick='fetchAssetData(18193);' class='asset-image' data-id='<?php echo $assetId18193; ?>'
                        data-room='<?php echo htmlspecialchars($room18193); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18193); ?>'
                        data-image='<?php echo base64_encode($upload_img18193); ?>'
                        data-status='<?php echo htmlspecialchars($status18193); ?>'
                        data-category='<?php echo htmlspecialchars($category18193); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18193); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18193); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18194 -->
                    <img src='../image.php?id=18194'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18194'
                        onclick='fetchAssetData(18194);' class='asset-image' data-id='<?php echo $assetId18194; ?>'
                        data-room='<?php echo htmlspecialchars($room18194); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18194); ?>'
                        data-image='<?php echo base64_encode($upload_img18194); ?>'
                        data-status='<?php echo htmlspecialchars($status18194); ?>'
                        data-category='<?php echo htmlspecialchars($category18194); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18194); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18194); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18195 -->
                    <img src='../image.php?id=18195'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18195'
                        onclick='fetchAssetData(18195);' class='asset-image' data-id='<?php echo $assetId18195; ?>'
                        data-room='<?php echo htmlspecialchars($room18195); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18195); ?>'
                        data-image='<?php echo base64_encode($upload_img18195); ?>'
                        data-status='<?php echo htmlspecialchars($status18195); ?>'
                        data-category='<?php echo htmlspecialchars($category18195); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18195); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18195); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 18196 -->
                    <img src='../image.php?id=18196'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18196'
                        onclick='fetchAssetData(18196);' class='asset-image' data-id='<?php echo $assetId18196; ?>'
                        data-room='<?php echo htmlspecialchars($room18196); ?>'
                        data-floor='<?php echo htmlspecialchars($floor18196); ?>'
                        data-image='<?php echo base64_encode($upload_img18196); ?>'
                        data-status='<?php echo htmlspecialchars($status18196); ?>'
                        data-category='<?php echo htmlspecialchars($category18196); ?>'
                        data-assignedname='<?php echo htmlspecialchars($assignedName18196); ?>'>
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status18196); ?>;
                        position:absolute; top:395px; left:622px;'>
                    </div>













                    

                    <!--Start of hover-->
                    <div id="hover-asset" class="hover-asset" style="display: none;">
                        <!-- Content will be added dynamically -->
                    </div>

                    <!--End of hover-->

                </div>

                <?php

// Function to generate modal structure for a given asset
function generateModal($assetId, $room, $floor, $upload_img, $status, $category, $assignedName, $assignedBy, $description)
{
    ?>
    <!-- Modal structure for asset with ID <?php echo $assetId; ?> -->
    <div class='modal fade' id='imageModal<?php echo $assetId; ?>' tabindex=' -1'
        aria-labelledby='imageModalLabel<?php echo $assetId; ?>' aria-hidden='true'>
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
                            value=" <?php echo htmlspecialchars($assetId); ?>">
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
                            <input type="text" class="form-control" id="assetId" name="assetId"
                                value=" <?php echo htmlspecialchars($assetId); ?>" readonly />
                        </div>

                        <!--Second Row-->
                        <div class="col-6">
                            <input type=" text" class="form-control" id="room" name="room"
                                value="<?php echo htmlspecialchars($room); ?>" readonly />
                        </div>

                        <!--End of Second Row-->
                        <!--Third Row-->
                        <div class="col-6">
                            <input type="text" class="form-control" id="floor" name="floor"
                                value="<?php echo htmlspecialchars($floor); ?>" readonly />
                        </div>
                        <div class="col-12 center-content">
                            <input type="text" class="form-control  center-content" id="category"
                                name=" category" value="<?php echo htmlspecialchars($category); ?>"
                                readonly />
                        </div>
                        <div class=" col-4" style="display:none">
                            <label for=" images" class="form-label">Images:</label>
                            <input type=" text" class="form-control" id="" name="images" readonly />
                        </div>
                        <!--End of Third Row-->
                        <!--Fourth Row-->
                        <div class="col-2 ">
                            <label for=" status" class="form-label">Status:</label>
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
                            <label for=" assignedName" class="form-label">Assigned Name:</label>
                            <input type="text" class="form-control" id="assignedName" name="assignedName"
                                value="<?php echo htmlspecialchars($assignedName); ?>" readonly />
                        </div>
                        <div class="col-4" style="display:none">
                            <label for="assignedBy" class="form-label">Assigned By:</label>
                            <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="
                    <?php echo htmlspecialchars($assignedBy); ?>" readonly />
                        </div>
                        <!--End of Fourth Row-->
                        <!--Fifth Row-->
                        <div class="col-12">
                            <input type="text" class="form-control" id="description" name=" description"
                                value="<?php echo htmlspecialchars($description); ?>" />
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
                            <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop<?php echo $assetId; ?>">
                                Save
                            </button>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <!--Edit for table <?php echo $assetId; ?>-->
    <div class="map-alert">
        <div class="modal fade" id="staticBackdrop<?php echo $assetId; ?>" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-footer">
                        <p>Are you sure you want to save changes?</p>
                        <div class="modal-popups">
                            <button type="submit" class="btn add-modal-btn"
                                name="edit<?php echo $assetId; ?>">Yes</button>
                            <button type="button" class="btn close-popups"
                                data-bs-dismiss="modal">No</button>


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