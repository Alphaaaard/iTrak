<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 3) {
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


    $assetIds = [8999, 9000, 9001, 9002, 9003, 9004, 9005, 9006, 9007, 9008, 9009, 9010, 9011, 9012, 9013, 9014, 9015, 9016, 9017, 9018, 9019, 9020, 9021, 9022, 9023, 9024, 9025, 9026, 9027, 9028, 9029, 9030, 9031, 9032, 9033, 9034, 9035, 9036, 9037, 9038, 9039, 9040, 9041, 9042, 9043, 9044, 9045, 9046, 9047, 9048, 9049, 9050, 9051, 9052, 9053, 9054, 9055, 9056, 9057, 9058, 9059, 9060, 9061, 9062, 9063, 9064, 9065, 9066, 9067, 9068, 9069, 9070, 9071, 9072, 9073, 9074, 9075, 9076, 9077, 9078, 9079, 9080, 9081, 9082, 9083, 9084, 9085, 9086, 9087, 9088, 9089, 9090, 9091, 9092, 9093, 9094, 9095, 9096, 9097, 9098, 9099, 9100, 9101, 9102, 9103, 9104, 9105, 9106, 9107, 9108, 9109, 9110, 9111, 9112, 9113, 9114, 9115, 9116, 9117, 9118, 9119, 9120, 9121, 9122, 9123, 9124, 9125, 9126, 9127, 9128, 9129, 9130, 9131, 9132, 9133, 9134, 9135, 9136, 9137, 9138, 9139, 9140, 9141, 9142, 9143, 9144, 9145, 9146, 9147, 9148, 9149, 9150, 9151, 9152, 9153, 9154, 9155, 9156, 9157, 9158, 9159, 9160, 9161, 9162, 9163, 9164, 9165, 9166, 9167, 9168, 9169, 9170, 9171, 9172, 9173, 9174, 9175, 9176, 9177, 9178, 9179, 9180, 9181, 9182, 9183, 9184, 9185, 9186, 9187, 9188, 9189, 9190, 9191, 9192, 9193, 9194, 9195, 9196, 9197, 9198, 9199, 9200, 9201, 9202, 9203, 9204, 9205, 9206, 9207, 9208, 9209, 9210, 9211, 9212, 9213, 9214, 9215, 9216, 9217, 9218, 9219, 9220, 9221, 9222, 9223, 9224, 9225, 9226, 9227, 9228, 9229, 9230, 9231, 9232, 9233, 9234, 9235, 9236, 9237, 9244, 9245, 9246, 9247, 9248, 9249, 9250, 9251, 9252, 9253, 9254, 9255, 10399, 10502, 10503, 10504, 10505, 10506, 10507, 10508, 10509, 16657, 16658, 16659, 16660, 16661, 16662, 16663, 16664, 16665, 16666, 9160, 9161, 9162, 9163, 9164, 9165, 9166, 9167, 9168, 9169, 9170, 9171, 9172, 9173, 9174, 9175, 9176, 9177, 9178, 9179, 9180, 9181, 9182, 9183, 9184, 9185, 9186, 9187, 9188, 9189, 9190, 9191, 9192, 9193, 9194, 9195, 9196, 9197, 9198, 9199, 9200, 9201, 9202, 9203, 9204, 9205, 9206, 9207, 9208, 9209, 9210, 9211, 9212, 9213, 9214, 9215, 9216, 9217, 9218, 9219, 9220, 9221, 9222, 9223, 9224, 9225, 9226, 9227, 9228, 9229, 9230, 9231, 9232, 9233, 9234, 9235, 9236, 9237, 9244, 9245, 9246, 9247, 9248, 9249, 9250, 9251, 9252, 9253, 9254, 9255, 16780, 16753, 16754, 16755, 16756, 16757, 16758, 16759, 16760, 16761, 16762, 16763, 16764, 16765, 16766, 16767, 16768, 16769, 16770, 16771, 16772, 16773, 16774, 16775, 16776, 16777, 16778, 16779, 16780, 16781, 16682, 16683, 16684, 16685, 16686, 16687, 16688, 16761, 16689, 16690, 16691, 16692, 16693, 16694, 16695, 16696, 16697, 16698, 16699, 16700, 16701, 16702, 16703, 16704, 16705, 16706, 16707, 16708, 16709, 16710, 16711, 16712, 16713, 16714, 16715, 16716, 16717, 16718, 18202, 18203, 18204, 18205, 18206, 18207, 18208, 18209, 18210, 18211, 18212, 18213, 18214, 18215, 18216, 18217, 18218, 18219, 18220, 18221, 18222, 18223, 18224, 18225, 18226, 18227, 18228, 18229, 18230, 18231, 18232, 18233, 18201, 16789];
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
                header("Location: OLBF2.php");
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
                header("Location: OLBF2.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
        }
    }

    // Call updateAsset function for each asset ID you want to handle
    $assetIds = [8999, 9000, 9001, 9002, 9003, 9004, 9005, 9006, 9007, 9008, 9009, 9010, 9011, 9012, 9013, 9014, 9015, 9016, 9017, 9018, 9019, 9020, 9021, 9022, 9023, 9024, 9025, 9026, 9027, 9028, 9029, 9030, 9031, 9032, 9033, 9034, 9035, 9036, 9037, 9038, 9039, 9040, 9041, 9042, 9043, 9044, 9045, 9046, 9047, 9048, 9049, 9050, 9051, 9052, 9053, 9054, 9055, 9056, 9057, 9058, 9059, 9060, 9061, 9062, 9063, 9064, 9065, 9066, 9067, 9068, 9069, 9070, 9071, 9072, 9073, 9074, 9075, 9076, 9077, 9078, 9079, 9080, 9081, 9082, 9083, 9084, 9085, 9086, 9087, 9088, 9089, 9090, 9091, 9092, 9093, 9094, 9095, 9096, 9097, 9098, 9099, 9100, 9101, 9102, 9103, 9104, 9105, 9106, 9107, 9108, 9109, 9110, 9111, 9112, 9113, 9114, 9115, 9116, 9117, 9118, 9119, 9120, 9121, 9122, 9123, 9124, 9125, 9126, 9127, 9128, 9129, 9130, 9131, 9132, 9133, 9134, 9135, 9136, 9137, 9138, 9139, 9140, 9141, 9142, 9143, 9144, 9145, 9146, 9147, 9148, 9149, 9150, 9151, 9152, 9153, 9154, 9155, 9156, 9157, 9158, 9159, 9160, 9161, 9162, 9163, 9164, 9165, 9166, 9167, 9168, 9169, 9170, 9171, 9172, 9173, 9174, 9175, 9176, 9177, 9178, 9179, 9180, 9181, 9182, 9183, 9184, 9185, 9186, 9187, 9188, 9189, 9190, 9191, 9192, 9193, 9194, 9195, 9196, 9197, 9198, 9199, 9200, 9201, 9202, 9203, 9204, 9205, 9206, 9207, 9208, 9209, 9210, 9211, 9212, 9213, 9214, 9215, 9216, 9217, 9218, 9219, 9220, 9221, 9222, 9223, 9224, 9225, 9226, 9227, 9228, 9229, 9230, 9231, 9232, 9233, 9234, 9235, 9236, 9237, 9244, 9245, 9246, 9247, 9248, 9249, 9250, 9251, 9252, 9253, 9254, 9255, 10399, 10502, 10503, 10504, 10505, 10506, 10507, 10508, 10509, 16657, 16658, 16659, 16660, 16661, 16662, 16663, 16664, 16665, 16666, 9160, 9161, 9162, 9163, 9164, 9165, 9166, 9167, 9168, 9169, 9170, 9171, 9172, 9173, 9174, 9175, 9176, 9177, 9178, 9179, 9180, 9181, 9182, 9183, 9184, 9185, 9186, 9187, 9188, 9189, 9190, 9191, 9192, 9193, 9194, 9195, 9196, 9197, 9198, 9199, 9200, 9201, 9202, 9203, 9204, 9205, 9206, 9207, 9208, 9209, 9210, 9211, 9212, 9213, 9214, 9215, 9216, 9217, 9218, 9219, 9220, 9221, 9222, 9223, 9224, 9225, 9226, 9227, 9228, 9229, 9230, 9231, 9232, 9233, 9234, 9235, 9236, 9237, 9244, 9245, 9246, 9247, 9248, 9249, 9250, 9251, 9252, 9253, 9254, 9255, 16781, 16754, 16755, 16756, 16757, 16758, 16759, 16760, 16761, 16762, 16763, 16764, 16765, 16766, 16767, 16768, 16769, 16770, 16771, 16772, 16773, 16774, 16775, 16776, 16777, 16778, 16779, 16781, 16682, 16683, 16684, 16685, 16686, 16687, 16688, 16761, 16689, 16690, 16691, 16692, 16693, 16694, 16695, 16696, 16697, 16698, 16699, 16700, 16701, 16702, 16703, 16704, 16705, 16706, 16707, 16708, 16709, 16710, 16711, 16712, 16713, 16714, 16715, 16716, 16717, 16718, 18202, 18203, 18204, 18205, 18206, 18207, 18208, 18209, 18210, 18211, 18212, 18213, 18214, 18215, 18216, 18217, 18218, 18219, 18220, 18221, 18222, 18223, 18224, 18225, 18226, 18227, 18228, 18229, 18230, 18231, 18232, 18233, 18201, 16789];

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
                header("Location: OLBF2.php");
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
        <title>Map</title>
        <link rel="icon" type="image/x-icon" href="../../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/KOB/KOBF1.css" />
        <link rel="stylesheet" href="../../../src/css/map.css" />
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                                <div><a class="profile-name"><?php echo $_SESSION['firstName']; ?></a></div>
                                <div><a class="profile-role"><?php echo $_SESSION['role']; ?></a></div>
                            </div>
                        </div>
                    </a>

                    <div id="settings-dropdown" class="dropdown-content1">
                        <div class="profile-name-container" id="mobile">
                            <div><a class="profile-name"><?php echo $_SESSION['firstName']; ?></a></div>
                            <div><a class="profile-role"><?php echo $_SESSION['role']; ?></a></div>
                            <hr>
                        </div>
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><img src="../../../src/icons/Profile.svg" alt="" class="profile-icons">Profile</a>
                        <a class="profile-hover" href="#"><img src="../../../src/icons/Logout.svg" alt="" class="profile-icons">Settings</a>
                        <a class="profile-hover" href="#" id="logoutBtn"><img src="../../../src/icons/Settings.svg" alt="" class="profile-icons">Logout</a>
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
                    <a href="../../personnel/dashboard.php">
                        <i class="bi bi-grid"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../../personnel/attendance-logs.php">
                        <i class="bi bi-calendar-week"></i>
                        <span class="text">Attendance Logs</span>
                    </a>
                </li>

                <li class="active">
                    <a href="../../personnel/map.php">
                        <i class="bi bi-map"></i>
                        <span class="text">Map</span>
                    </a>
                </li>
                <li>
                    <a href="../../personnel/assigned-tasks.php">
                        <i class="bi bi-geo-alt"></i>
                        <span class="text">Assigned Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="../../personnel/reports.php">
                        <i class="bi bi-clipboard"></i>
                        <span class="text">Reports</span>
                    </a>
                </li>
                <li>
                    <a href="../../personnel/activity-logs.php">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="text">Activity Logs</span>
                    </a>
                </li>
            </ul>
        </section>

        <div id="map-top-nav">
            <a href="../../personnel/map.php" class="closeFloor"><i class="bi bi-box-arrow-left"></i></i></a>

            <div class="legend-button" id="legendButton">
                <i class="bi bi-info-circle"></i>
            </div>
        </div>
        <section id="content">
            <main>
                <div class="content-container" id="content-container">
                    <div id="belmonte-F1" class="content">
                        <a href="../../administrator/map.php" class="closeFloor"><i class="bi bi-arrow-left"></i></a>

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/I-CHAIR.jpg" alt="" class="legend-img">
                                <p>CHAIR</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/TOILET-SEAT.jpg" alt="" class="legend-img">
                                <p>TOILET SEAT</p>
                            </div>
                        </div>


                        <!-- FLOOR PLAN -->
                        <img class="Floor-container-1" src="../../../src/floors/oldAcademicB/OAB2F.png" alt="">
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

                        <!-- ASSET 41 -->
                        <img src='../image.php?id=41' style='width:18px; cursor:pointer; position:absolute; top:405px; left: 1150px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal41' onclick='fetchAssetData(41);' data-id="<?php echo $assetId41; ?>" data-room="<?php echo htmlspecialchars($room41); ?>" data-floor="<?php echo htmlspecialchars($floor41); ?>" data-image="<?php echo base64_encode($upload_img41); ?>" data-category="<?php echo htmlspecialchars($category41); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName41); ?>" data-status="<?php echo htmlspecialchars($status41); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status41); ?>; 
    position:absolute; top:418px; left:1162px;'>
                        </div>

                        <!-- ASSET 42 -->
                        <img src='../image.php?id=42' style='width:18px; cursor:pointer; position:absolute; top: 425px; left:1150px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal42' onclick='fetchAssetData(42);' data-id="<?php echo $assetId42; ?>" data-room="<?php echo htmlspecialchars($room42); ?>" data-floor="<?php echo htmlspecialchars($floor42); ?>" data-image="<?php echo base64_encode($upload_img42); ?>" data-category="<?php echo htmlspecialchars($category42); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName42); ?>" data-status="<?php echo htmlspecialchars($status42); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status42); ?>; 
    position:absolute; top:438px; left:1162px;'>
                        </div>

                        <!-- ASSET 43 -->
                        <img src='../image.php?id=43' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1150px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal43' onclick='fetchAssetData(43);' data-id="<?php echo $assetId43; ?>" data-room="<?php echo htmlspecialchars($room43); ?>" data-floor="<?php echo htmlspecialchars($floor43); ?>" data-image="<?php echo base64_encode($upload_img43); ?>" data-category="<?php echo htmlspecialchars($category43); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName43); ?>" data-status="<?php echo htmlspecialchars($status43); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status43); ?>; 
    position:absolute; top:457px; left:1162px;'>
                        </div>

                        <!-- ASSET 44 -->
                        <img src='../image.php?id=44' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1150px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal44' onclick='fetchAssetData(44);' data-id="<?php echo $assetId44; ?>" data-room="<?php echo htmlspecialchars($room44); ?>" data-floor="<?php echo htmlspecialchars($floor44); ?>" data-image="<?php echo base64_encode($upload_img44); ?>" data-category="<?php echo htmlspecialchars($category44); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName44); ?>" data-status="<?php echo htmlspecialchars($status44); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status44); ?>; 
    position:absolute; top:477px; left:1162px;'>
                        </div>


                        <!-- ASSET 45 -->
                        <img src='../image.php?id=45' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1150px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal45' onclick='fetchAssetData(45);' data-id="<?php echo $assetId45; ?>" data-room="<?php echo htmlspecialchars($room45); ?>" data-floor="<?php echo htmlspecialchars($floor45); ?>" data-image="<?php echo base64_encode($upload_img45); ?>" data-category="<?php echo htmlspecialchars($category45); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName45); ?>" data-status="<?php echo htmlspecialchars($status45); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status45); ?>; 
    position:absolute; top:497px; left:1162px;'>
                        </div>

                        <!-- ASSET 46 -->
                        <img src='../image.php?id=46' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal46' onclick='fetchAssetData(46);' data-id="<?php echo $assetId46; ?>" data-room="<?php echo htmlspecialchars($room46); ?>" data-floor="<?php echo htmlspecialchars($floor46); ?>" data-image="<?php echo base64_encode($upload_img46); ?>" data-category="<?php echo htmlspecialchars($category46); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName46); ?>" data-status="<?php echo htmlspecialchars($status46); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status46); ?>; 
    position:absolute; top:418px; left:1139px;'>
                        </div>

                        <!-- ASSET 47 -->
                        <img src='../image.php?id=47' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal47' onclick='fetchAssetData(47);' data-id="<?php echo $assetId47; ?>" data-room="<?php echo htmlspecialchars($room47); ?>" data-floor="<?php echo htmlspecialchars($floor47); ?>" data-image="<?php echo base64_encode($upload_img47); ?>" data-category="<?php echo htmlspecialchars($category47); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName47); ?>" data-status="<?php echo htmlspecialchars($status47); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status47); ?>; 
    position:absolute; top:438px; left:1139px;'>
                        </div>

                        <!-- ASSET 48 -->
                        <img src='../image.php?id=48' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal48' onclick='fetchAssetData(48);' data-id="<?php echo $assetId48; ?>" data-room="<?php echo htmlspecialchars($room48); ?>" data-floor="<?php echo htmlspecialchars($floor48); ?>" data-image="<?php echo base64_encode($upload_img48); ?>" data-category="<?php echo htmlspecialchars($category48); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName48); ?>" data-status="<?php echo htmlspecialchars($status48); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status48); ?>; 
    position:absolute; top:457px; left:1139px;'>
                        </div>


                        <!-- ASSET 49 -->
                        <img src='../image.php?id=49' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal49' onclick='fetchAssetData(49);' data-id="<?php echo $assetId49; ?>" data-room="<?php echo htmlspecialchars($room49); ?>" data-floor="<?php echo htmlspecialchars($floor49); ?>" data-image="<?php echo base64_encode($upload_img49); ?>" data-category="<?php echo htmlspecialchars($category49); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName49); ?>" data-status="<?php echo htmlspecialchars($status49); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status49); ?>; 
    position:absolute; top:477px; left:1139px;'>
                        </div>

                        <!-- ASSET 50 -->
                        <img src='../image.php?id=50' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal50' onclick='fetchAssetData(50);' data-id="<?php echo $assetId50; ?>" data-room="<?php echo htmlspecialchars($room50); ?>" data-floor="<?php echo htmlspecialchars($floor50); ?>" data-image="<?php echo base64_encode($upload_img50); ?>" data-category="<?php echo htmlspecialchars($category50); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName50); ?>" data-status="<?php echo htmlspecialchars($status50); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status50); ?>; 
    position:absolute; top:497px; left:1139px;'>
                        </div>

                        <!-- ASSET 51 -->
                        <img src='../image.php?id=51' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal51' onclick='fetchAssetData(51);' data-id="<?php echo $assetId51; ?>" data-room="<?php echo htmlspecialchars($room51); ?>" data-floor="<?php echo htmlspecialchars($floor51); ?>" data-image="<?php echo base64_encode($upload_img51); ?>" data-category="<?php echo htmlspecialchars($category51); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName51); ?>" data-status="<?php echo htmlspecialchars($status51); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status51); ?>; 
    position:absolute; top:418px; left:1118px;'>
                        </div>

                        <!-- ASSET 52 -->
                        <img src='../image.php?id=52' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal52' onclick='fetchAssetData(52);' data-id="<?php echo $assetId52; ?>" data-room="<?php echo htmlspecialchars($room52); ?>" data-floor="<?php echo htmlspecialchars($floor52); ?>" data-image="<?php echo base64_encode($upload_img52); ?>" data-category="<?php echo htmlspecialchars($category52); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName52); ?>" data-status="<?php echo htmlspecialchars($status52); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status52); ?>; 
    position:absolute; top:438px; left:1118px;'>
                        </div>


                        <!-- ASSET 53 -->
                        <img src='../image.php?id=53' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal53' onclick='fetchAssetData(53);' data-id="<?php echo $assetId53; ?>" data-room="<?php echo htmlspecialchars($room53); ?>" data-floor="<?php echo htmlspecialchars($floor53); ?>" data-image="<?php echo base64_encode($upload_img53); ?>" data-category="<?php echo htmlspecialchars($category53); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName53); ?>" data-status="<?php echo htmlspecialchars($status53); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status53); ?>; 
    position:absolute; top:457px; left:1118px;'>
                        </div>

                        <!-- ASSET 54 -->
                        <img src='../image.php?id=54' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal54' onclick='fetchAssetData(54);' data-id="<?php echo $assetId54; ?>" data-room="<?php echo htmlspecialchars($room54); ?>" data-floor="<?php echo htmlspecialchars($floor54); ?>" data-image="<?php echo base64_encode($upload_img54); ?>" data-category="<?php echo htmlspecialchars($category54); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName54); ?>" data-status="<?php echo htmlspecialchars($status54); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status54); ?>; 
    position:absolute; top:477px; left:1118px;'>
                        </div>

                        <!-- ASSET 55 -->
                        <img src='../image.php?id=55' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal55' onclick='fetchAssetData(55);' data-id="<?php echo $assetId55; ?>" data-room="<?php echo htmlspecialchars($room55); ?>" data-floor="<?php echo htmlspecialchars($floor55); ?>" data-image="<?php echo base64_encode($upload_img55); ?>" data-category="<?php echo htmlspecialchars($category55); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName55); ?>" data-status="<?php echo htmlspecialchars($status55); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status55); ?>; 
    position:absolute; top:497px; left:1118px;'>
                        </div>

                        <!-- ASSET 56 -->
                        <img src='../image.php?id=56' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal56' onclick='fetchAssetData(56);' data-id="<?php echo $assetId56; ?>" data-room="<?php echo htmlspecialchars($room56); ?>" data-floor="<?php echo htmlspecialchars($floor56); ?>" data-image="<?php echo base64_encode($upload_img56); ?>" data-category="<?php echo htmlspecialchars($category56); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName56); ?>" data-status="<?php echo htmlspecialchars($status56); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status56); ?>; 
    position:absolute; top:418px; left:1098px;'>
                        </div>


                        <!-- ASSET 57 -->
                        <img src='../image.php?id=57' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal57' onclick='fetchAssetData(57);' data-id="<?php echo $assetId57; ?>" data-room="<?php echo htmlspecialchars($room57); ?>" data-floor="<?php echo htmlspecialchars($floor57); ?>" data-image="<?php echo base64_encode($upload_img57); ?>" data-category="<?php echo htmlspecialchars($category57); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName57); ?>" data-status="<?php echo htmlspecialchars($status57); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status57); ?>; 
    position:absolute; top:438px; left:1098px;'>
                        </div>

                        <!-- ASSET 58 -->
                        <img src='../image.php?id=58' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal58' onclick='fetchAssetData(58);' data-id="<?php echo $assetId58; ?>" data-room="<?php echo htmlspecialchars($room58); ?>" data-floor="<?php echo htmlspecialchars($floor58); ?>" data-image="<?php echo base64_encode($upload_img58); ?>" data-category="<?php echo htmlspecialchars($category58); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName58); ?>" data-status="<?php echo htmlspecialchars($status58); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status58); ?>; 
    position:absolute; top:457px; left:1098px;'>
                        </div>

                        <!-- ASSET 59 -->
                        <img src='../image.php?id=59' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal59' onclick='fetchAssetData(59);' data-id="<?php echo $assetId59; ?>" data-room="<?php echo htmlspecialchars($room59); ?>" data-floor="<?php echo htmlspecialchars($floor59); ?>" data-image="<?php echo base64_encode($upload_img59); ?>" data-category="<?php echo htmlspecialchars($category59); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName59); ?>" data-status="<?php echo htmlspecialchars($status59); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status59); ?>; 
    position:absolute; top:477px; left:1098px;'>
                        </div>

                        <!-- ASSET 60 -->
                        <img src='../image.php?id=60' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal60' onclick='fetchAssetData(60);' data-id="<?php echo $assetId60; ?>" data-room="<?php echo htmlspecialchars($room60); ?>" data-floor="<?php echo htmlspecialchars($floor60); ?>" data-image="<?php echo base64_encode($upload_img60); ?>" data-category="<?php echo htmlspecialchars($category60); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName60); ?>" data-status="<?php echo htmlspecialchars($status60); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status60); ?>; 
    position:absolute; top:497px; left:1098px;'>
                        </div>


                        <!-- END OF CHAIR 51 TO 60 -->

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 61 -->
                        <img src='../image.php?id=61' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal61' onclick='fetchAssetData(61);' data-id="<?php echo $assetId61; ?>" data-room="<?php echo htmlspecialchars($room61); ?>" data-floor="<?php echo htmlspecialchars($floor61); ?>" data-image="<?php echo base64_encode($upload_img61); ?>" data-category="<?php echo htmlspecialchars($category61); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName61); ?>" data-status="<?php echo htmlspecialchars($status61); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status61); ?>; 
    position:absolute; top:418px; left:1078px;'>
                        </div>

                        <!-- ASSET 62 -->
                        <img src='../image.php?id=62' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal62' onclick='fetchAssetData(62);' data-id="<?php echo $assetId62; ?>" data-room="<?php echo htmlspecialchars($room62); ?>" data-floor="<?php echo htmlspecialchars($floor62); ?>" data-image="<?php echo base64_encode($upload_img62); ?>" data-category="<?php echo htmlspecialchars($category62); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName62); ?>" data-status="<?php echo htmlspecialchars($status62); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status62); ?>; 
    position:absolute; top:438px; left:1078px;'>
                        </div>

                        <!-- ASSET 63 -->
                        <img src='../image.php?id=63' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal63' onclick='fetchAssetData(63);' data-id="<?php echo $assetId63; ?>" data-room="<?php echo htmlspecialchars($room63); ?>" data-floor="<?php echo htmlspecialchars($floor63); ?>" data-image="<?php echo base64_encode($upload_img63); ?>" data-category="<?php echo htmlspecialchars($category63); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName63); ?>" data-status="<?php echo htmlspecialchars($status63); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status63); ?>; 
    position:absolute; top:457px; left:1078px;'>
                        </div>

                        <!-- ASSET 64 -->
                        <img src='../image.php?id=64' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal64' onclick='fetchAssetData(64);' data-id="<?php echo $assetId64; ?>" data-room="<?php echo htmlspecialchars($room64); ?>" data-floor="<?php echo htmlspecialchars($floor64); ?>" data-image="<?php echo base64_encode($upload_img64); ?>" data-category="<?php echo htmlspecialchars($category64); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName64); ?>" data-status="<?php echo htmlspecialchars($status64); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status64); ?>; 
    position:absolute; top:477px; left:1078px;'>
                        </div>


                        <!-- ASSET 65 -->
                        <img src='../image.php?id=65' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal65' onclick='fetchAssetData(65);' data-id="<?php echo $assetId65; ?>" data-room="<?php echo htmlspecialchars($room65); ?>" data-floor="<?php echo htmlspecialchars($floor65); ?>" data-image="<?php echo base64_encode($upload_img65); ?>" data-category="<?php echo htmlspecialchars($category65); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName65); ?>" data-status="<?php echo htmlspecialchars($status65); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status65); ?>; 
    position:absolute; top:497px; left:1078px;'>
                        </div>

                        <!-- ASSET 67 -->
                        <img src='../image.php?id=67' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal67' onclick='fetchAssetData(67);' class="asset-image" data-id="<?php echo $assetId67; ?>" data-room="<?php echo htmlspecialchars($room67); ?>" data-floor="<?php echo htmlspecialchars($floor67); ?>" data-image="<?php echo base64_encode($upload_img67); ?>" data-category="<?php echo htmlspecialchars($category67); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName67); ?>" data-status="<?php echo htmlspecialchars($status67); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status67); ?>; 
    position:absolute; top:100px; left:1085px;'>
                        </div>

                        <!-- ASSET 68 -->
                        <img src='../image.php?id=68' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal68' onclick='fetchAssetData(68);' class="asset-image" data-id="<?php echo $assetId68; ?>" data-room="<?php echo htmlspecialchars($room68); ?>" data-floor="<?php echo htmlspecialchars($floor68); ?>" data-image="<?php echo base64_encode($upload_img68); ?>" data-category="<?php echo htmlspecialchars($category68); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName68); ?>" data-status="<?php echo htmlspecialchars($status68); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status68); ?>; 
    position:absolute; top:100px; left:1103px;'>
                        </div>

                        <!-- ASSET 69 -->
                        <img src='../image.php?id=69' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal69' onclick='fetchAssetData(69);' class="asset-image" data-id="<?php echo $assetId69; ?>" data-room="<?php echo htmlspecialchars($room69); ?>" data-floor="<?php echo htmlspecialchars($floor69); ?>" data-image="<?php echo base64_encode($upload_img69); ?>" data-category="<?php echo htmlspecialchars($category69); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName69); ?>" data-status="<?php echo htmlspecialchars($status69); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status69); ?>; 
    position:absolute; top:100px; left:1120px;'>
                        </div>


                        <!-- ASSET 70 -->
                        <img src='../image.php?id=70' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal70' onclick='fetchAssetData(70);' data-id="<?php echo $assetId70; ?>" class="asset-image" data-room="<?php echo htmlspecialchars($room70); ?>" data-floor="<?php echo htmlspecialchars($floor70); ?>" data-image="<?php echo base64_encode($upload_img70); ?>" data-category="<?php echo htmlspecialchars($category70); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName70); ?>" data-status="<?php echo htmlspecialchars($status70); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status70); ?>; 
    position:absolute; top:100px; left:1139px;'>
                        </div>

                        <!-- ASSET 71 -->
                        <img src='../image.php?id=71' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal71' onclick='fetchAssetData(71);' data-id="<?php echo $assetId71; ?>" class="asset-image" data-room=" <?php echo htmlspecialchars($room71); ?>" data-floor="<?php echo htmlspecialchars($floor71); ?>" data-image=" <?php echo base64_encode($upload_img71); ?>" data-category="<?php echo htmlspecialchars($category71); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName71); ?>" data-status="<?php echo htmlspecialchars($status71); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status71); ?>; 
    position:absolute; top:100px; left:1158px;'>
                        </div>

                        <!-- ASSET 72 -->
                        <img src='../image.php?id=72' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal72' onclick='fetchAssetData(72);' class="asset-image" data-id="<?php echo $assetId72; ?>" data-room=" <?php echo htmlspecialchars($room72); ?>" data-floor="<?php echo htmlspecialchars($floor72); ?>" data-image=" <?php echo base64_encode($upload_img72); ?>" data-category="<?php echo htmlspecialchars($category72); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName72); ?>" data-status="<?php echo htmlspecialchars($status72); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status72); ?>; 
    position:absolute; top:120px; left:1085px;'>
                        </div>

                        <!-- ASSET 73 -->
                        <img src='../image.php?id=73' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal73' onclick='fetchAssetData(73);' class="asset-image" data-id="<?php echo $assetId73; ?>" data-room=" <?php echo htmlspecialchars($room73); ?>" data-floor="<?php echo htmlspecialchars($floor73); ?>" data-image=" <?php echo base64_encode($upload_img73); ?>" data-category="<?php echo htmlspecialchars($category73); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName73); ?>" data-status="<?php echo htmlspecialchars($status73); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status73); ?>; 
    position:absolute; top:120px; left:1103px;'>
                        </div>


                        <!-- ASSET 74 -->
                        <img src='../image.php?id=74' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal74' onclick='fetchAssetData(74);' class="asset-image" data-id="<?php echo $assetId74; ?>" data-room=" <?php echo htmlspecialchars($room74); ?>" data-floor="<?php echo htmlspecialchars($floor74); ?>" data-image=" <?php echo base64_encode($upload_img74); ?>" data-category="<?php echo htmlspecialchars($category74); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName74); ?>" data-status="<?php echo htmlspecialchars($status74); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status74); ?>; 
    position:absolute; top:120px; left:1120px;'>
                        </div>

                        <!-- ASSET 75 -->
                        <img src='../image.php?id=75' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal75' onclick='fetchAssetData(75);' class="asset-image" data-id="<?php echo $assetId75; ?>" data-room=" <?php echo htmlspecialchars($room75); ?>" data-floor="<?php echo htmlspecialchars($floor75); ?>" data-image=" <?php echo base64_encode($upload_img75); ?>" data-category="<?php echo htmlspecialchars($category75); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName75); ?>" data-status="<?php echo htmlspecialchars($status75); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status75); ?>; 
    position:absolute; top:120px; left:1139px;'>
                        </div>

                        <!-- ASSET 76 -->
                        <img src='../image.php?id=76' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal76' class="asset-image" onclick='fetchAssetData(76);' data-id="<?php echo $assetId76; ?>" data-room=" <?php echo htmlspecialchars($room76); ?>" data-floor="<?php echo htmlspecialchars($floor76); ?>" data-image=" <?php echo base64_encode($upload_img76); ?>" data-category="<?php echo htmlspecialchars($category76); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName76); ?>" data-status="<?php echo htmlspecialchars($status76); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status76); ?>; 
    position:absolute; top:120px; left:1158px;'>
                        </div>

                        <!-- ASSET 77 -->
                        <img src='../image.php?id=77' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal77' class="asset-image" onclick='fetchAssetData(77);' data-id="<?php echo $assetId77; ?>" data-room=" <?php echo htmlspecialchars($room77); ?>" data-floor="<?php echo htmlspecialchars($floor77); ?>" data-image=" <?php echo base64_encode($upload_img77); ?>" data-category="<?php echo htmlspecialchars($category77); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName77); ?>" data-status="<?php echo htmlspecialchars($status77); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status77); ?>; 
    position:absolute; top:145px; left:1085px;'>
                        </div>


                        <!-- ASSET 78 -->
                        <img src='../image.php?id=78' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal78' class="asset-image" onclick='fetchAssetData(78);' data-id="<?php echo $assetId78; ?>" data-room=" <?php echo htmlspecialchars($room78); ?>" data-floor="<?php echo htmlspecialchars($floor78); ?>" data-image=" <?php echo base64_encode($upload_img78); ?>" data-category="<?php echo htmlspecialchars($category78); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName78); ?>" data-status="<?php echo htmlspecialchars($status78); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status78); ?>; 
    position:absolute; top:145px; left:1103px;'>
                        </div>

                        <!-- ASSET 79 -->
                        <img src='../image.php?id=79' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal79' class="asset-image" onclick='fetchAssetData(79);' data-id="<?php echo $assetId79; ?>" data-room=" <?php echo htmlspecialchars($room79); ?>" data-floor="<?php echo htmlspecialchars($floor79); ?>" data-image=" <?php echo base64_encode($upload_img79); ?>" data-category="<?php echo htmlspecialchars($category79); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName79); ?>" data-status="<?php echo htmlspecialchars($status79); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status79); ?>; 
    position:absolute; top:145px; left:1120px;'>
                        </div>

                        <!-- ASSET 80 -->
                        <img src='../image.php?id=80' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal80' class="asset-image" onclick='fetchAssetData(80);' data-id="<?php echo $assetId80; ?>" data-room=" <?php echo htmlspecialchars($room80); ?>" data-floor="<?php echo htmlspecialchars($floor80); ?>" data-image=" <?php echo base64_encode($upload_img80); ?>" data-category="<?php echo htmlspecialchars($category80); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName80); ?>" data-status="<?php echo htmlspecialchars($status80); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status80); ?>; 
    position:absolute; top:145px; left:1139px;'>
                        </div>

                        <!-- ASSET 81 -->
                        <img src='../image.php?id=81' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal81' class="asset-image" onclick='fetchAssetData(81);' data-id="<?php echo $assetId81; ?>" data-room=" <?php echo htmlspecialchars($room81); ?>" data-floor="<?php echo htmlspecialchars($floor81); ?>" data-image=" <?php echo base64_encode($upload_img81); ?>" data-category="<?php echo htmlspecialchars($category81); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName81); ?>" data-status="<?php echo htmlspecialchars($status81); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status81); ?>; 
    position:absolute; top:145px; left:1158px;'>
                        </div>


                        <!-- END OF ROW 3 -->

                        <!-- ASSET 82 -->
                        <img src='../image.php?id=82' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal82' class="asset-image" onclick='fetchAssetData(82);' data-id="<?php echo $assetId82; ?>" data-room=" <?php echo htmlspecialchars($room82); ?>" data-floor="<?php echo htmlspecialchars($floor82); ?>" data-image=" <?php echo base64_encode($upload_img82); ?>" data-category="<?php echo htmlspecialchars($category82); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName82); ?>" data-status="<?php echo htmlspecialchars($status82); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status82); ?>; 
    position:absolute; top:170px; left:1085px;'>
                        </div>

                        <!-- ASSET 83 -->
                        <img src='../image.php?id=83' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal83' class="asset-image" onclick='fetchAssetData(83);' data-id="<?php echo $assetId83; ?>" data-room=" <?php echo htmlspecialchars($room83); ?>" data-floor="<?php echo htmlspecialchars($floor83); ?>" data-image=" <?php echo base64_encode($upload_img83); ?>" data-category="<?php echo htmlspecialchars($category83); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName83); ?>" data-status="<?php echo htmlspecialchars($status83); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status83); ?>; 
    position:absolute; top:170px; left:1103px;'>
                        </div>

                        <!-- ASSET 84 -->
                        <img src='../image.php?id=84' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal84' class="asset-image" onclick='fetchAssetData(84);' data-id="<?php echo $assetId84; ?>" data-room=" <?php echo htmlspecialchars($room84); ?>" data-floor="<?php echo htmlspecialchars($floor84); ?>" data-image=" <?php echo base64_encode($upload_img84); ?>" data-category="<?php echo htmlspecialchars($category84); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName84); ?>" data-status="<?php echo htmlspecialchars($status84); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status84); ?>; 
    position:absolute; top:170px; left:1120px;'>
                        </div>

                        <!-- ASSET 85 -->
                        <img src='../image.php?id=85' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal85' class="asset-image" onclick='fetchAssetData(85);' data-id="<?php echo $assetId85; ?>" data-room=" <?php echo htmlspecialchars($room85); ?>" data-floor="<?php echo htmlspecialchars($floor85); ?>" data-image=" <?php echo base64_encode($upload_img85); ?>" data-category="<?php echo htmlspecialchars($category85); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName85); ?>" data-status="<?php echo htmlspecialchars($status85); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status85); ?>; 
    position:absolute; top:170px; left:1139px;'>
                        </div>

                        <!-- ASSET 86 -->
                        <img src='../image.php?id=86' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal86' class="asset-image" onclick='fetchAssetData(86);' data-id="<?php echo $assetId86; ?>" data-room=" <?php echo htmlspecialchars($room86); ?>" data-floor="<?php echo htmlspecialchars($floor86); ?>" data-image=" <?php echo base64_encode($upload_img86); ?>" data-category="<?php echo htmlspecialchars($category86); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName86); ?>" data-status="<?php echo htmlspecialchars($status86); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status86); ?>; 
    position:absolute; top:170px; left:1158px;'>
                        </div>

                        <!-- ASSET 66 -->
                        <img src='../image.php?id=66' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal66' class="asset-image" onclick='fetchAssetData(66);' data-id="<?php echo $assetId66; ?>" data-room=" <?php echo htmlspecialchars($room66); ?>" data-floor="<?php echo htmlspecialchars($floor66); ?>" data-image=" <?php echo base64_encode($upload_img66); ?>" data-category="<?php echo htmlspecialchars($category66); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName66); ?>" data-status="<?php echo htmlspecialchars($status66); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status66); ?>; 
    position:absolute; top:190px; left:1085px;'>
                        </div>

                        <!-- ASSET 87 -->
                        <img src='../image.php?id=87' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal87' class="asset-image" onclick='fetchAssetData(87);' data-id="<?php echo $assetId87; ?>" data-room=" <?php echo htmlspecialchars($room87); ?>" data-floor="<?php echo htmlspecialchars($floor87); ?>" data-image=" <?php echo base64_encode($upload_img87); ?>" data-category="<?php echo htmlspecialchars($category87); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName87); ?>" data-status="<?php echo htmlspecialchars($status87); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status87); ?>; 
    position:absolute; top:190px; left:1103px;'>
                        </div>

                        <!-- ASSET 88 -->
                        <img src='../image.php?id=88' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal88' class="asset-image" onclick='fetchAssetData(88);' data-id="<?php echo $assetId88; ?>" data-room=" <?php echo htmlspecialchars($room88); ?>" data-floor="<?php echo htmlspecialchars($floor88); ?>" data-image=" <?php echo base64_encode($upload_img88); ?>" data-category="<?php echo htmlspecialchars($category88); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName88); ?>" data-status="<?php echo htmlspecialchars($status88); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status88); ?>; 
    position:absolute; top:190px; left:1120px;'>
                        </div>


                        <!-- ASSET 89 -->
                        <img src='../image.php?id=89' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal89' class="asset-image" onclick='fetchAssetData(89);' data-id="<?php echo $assetId89; ?>" data-room=" <?php echo htmlspecialchars($room89); ?>" data-floor="<?php echo htmlspecialchars($floor89); ?>" data-image=" <?php echo base64_encode($upload_img89); ?>" data-category="<?php echo htmlspecialchars($category89); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName89); ?>" data-status="<?php echo htmlspecialchars($status89); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status89); ?>; 
    position:absolute; top:190px; left:1139px;'>
                        </div>

                        <!-- ASSET 90 -->
                        <img src='../image.php?id=90' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal90' class="asset-image" onclick='fetchAssetData(90);' data-id="<?php echo $assetId90; ?>" data-room=" <?php echo htmlspecialchars($room90); ?>" data-floor="<?php echo htmlspecialchars($floor90); ?>" data-image=" <?php echo base64_encode($upload_img90); ?>" data-category="<?php echo htmlspecialchars($category90); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName90); ?>" data-status="<?php echo htmlspecialchars($status90); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status90); ?>; 
    position:absolute; top:190px; left:1158px;'>
                        </div>

                        <!-- ASSET 91 -->
                        <img src='../image.php?id=91' style='width:17px; cursor:pointer; position:absolute; top:80px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal91' class="asset-image" onclick='fetchAssetData(91);' data-id="<?php echo $assetId91; ?>" data-room=" <?php echo htmlspecialchars($room91); ?>" data-floor="<?php echo htmlspecialchars($floor91); ?>" data-image=" <?php echo base64_encode($upload_img91); ?>" data-category="<?php echo htmlspecialchars($category91); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName91); ?>" data-status="<?php echo htmlspecialchars($status91); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status91); ?>; 
    position:absolute; top:80px; left:950px;'>
                        </div>

                        <!-- ASSET 92 -->
                        <img src='../image.php?id=92' style='width:17px; cursor:pointer; position:absolute; top:80px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal92' class="asset-image" onclick='fetchAssetData(92);' data-id="<?php echo $assetId92; ?>" data-room=" <?php echo htmlspecialchars($room92); ?>" data-floor="<?php echo htmlspecialchars($floor92); ?>" data-image=" <?php echo base64_encode($upload_img92); ?>" data-category="<?php echo htmlspecialchars($category92); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName92); ?>" data-status="<?php echo htmlspecialchars($status92); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status92); ?>; 
    position:absolute; top:80px; left:970px;'>
                        </div>

                        <!-- ASSET 93 -->
                        <img src='../image.php?id=93' style='width:17px; cursor:pointer; position:absolute; top:80px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal93' class="asset-image" onclick='fetchAssetData(93);' data-id="<?php echo $assetId93; ?>" data-room=" <?php echo htmlspecialchars($room93); ?>" data-floor="<?php echo htmlspecialchars($floor93); ?>" data-image=" <?php echo base64_encode($upload_img93); ?>" data-category="<?php echo htmlspecialchars($category93); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName93); ?>" data-status="<?php echo htmlspecialchars($status93); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status93); ?>; 
    position:absolute; top:80px; left:990px;'>
                        </div>

                        <!-- ASSET 94 -->
                        <img src='../image.php?id=94' style='width:17px; cursor:pointer; position:absolute; top:80px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal94' class="asset-image" onclick='fetchAssetData(94);' data-id="<?php echo $assetId94; ?>" data-room=" <?php echo htmlspecialchars($room94); ?>" data-floor="<?php echo htmlspecialchars($floor94); ?>" data-image=" <?php echo base64_encode($upload_img94); ?>" data-category="<?php echo htmlspecialchars($category94); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName94); ?>" data-status="<?php echo htmlspecialchars($status94); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status94); ?>; 
    position:absolute; top:80px; left:1010px;'>
                        </div>

                        <!-- ASSET 95 -->
                        <img src='../image.php?id=95' style='width:17px; cursor:pointer; position:absolute; top:80px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal95' class="asset-image" onclick='fetchAssetData(95);' data-id="<?php echo $assetId95; ?>" data-room=" <?php echo htmlspecialchars($room95); ?>" data-floor="<?php echo htmlspecialchars($floor95); ?>" data-image=" <?php echo base64_encode($upload_img95); ?>" data-category="<?php echo htmlspecialchars($category95); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName95); ?>" data-status="<?php echo htmlspecialchars($status95); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status95); ?>; 
    position:absolute; top:80px; left:1030px;'>
                        </div>

                        <!-- ASSET 96 -->
                        <img src='../image.php?id=96' style='width:17px; cursor:pointer; position:absolute; top:100px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal96' class="asset-image" onclick='fetchAssetData(96);' data-id="<?php echo $assetId96; ?>" data-room=" <?php echo htmlspecialchars($room96); ?>" data-floor="<?php echo htmlspecialchars($floor96); ?>" data-image=" <?php echo base64_encode($upload_img96); ?>" data-category="<?php echo htmlspecialchars($category96); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName96); ?>" data-status="<?php echo htmlspecialchars($status96); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status96); ?>; 
    position:absolute; top:100px; left:950px;'>
                        </div>


                        <!-- ASSET 97 -->
                        <img src='../image.php?id=97' style='width:17px; cursor:pointer; position:absolute; top:100px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal97' class="asset-image" onclick='fetchAssetData(97);' data-id="<?php echo $assetId97; ?>" data-room=" <?php echo htmlspecialchars($room97); ?>" data-floor="<?php echo htmlspecialchars($floor97); ?>" data-image=" <?php echo base64_encode($upload_img97); ?>" data-category="<?php echo htmlspecialchars($category97); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName97); ?>" data-status="<?php echo htmlspecialchars($status97); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status97); ?>; 
    position:absolute; top:100px; left:970px;'>
                        </div>

                        <!-- ASSET 98 -->
                        <img src='../image.php?id=98' style='width:17px; cursor:pointer; position:absolute; top:100px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal98' class="asset-image" onclick='fetchAssetData(98);' data-id="<?php echo $assetId98; ?>" data-room=" <?php echo htmlspecialchars($room98); ?>" data-floor="<?php echo htmlspecialchars($floor98); ?>" data-image=" <?php echo base64_encode($upload_img98); ?>" data-category="<?php echo htmlspecialchars($category98); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName98); ?>" data-status="<?php echo htmlspecialchars($status98); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status98); ?>; 
    position:absolute; top:100px; left:990px;'>
                        </div>

                        <!-- ASSET 99 -->
                        <img src='../image.php?id=99' style='width:17px; cursor:pointer; position:absolute; top:100px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal99' class="asset-image" onclick='fetchAssetData(99);' data-id="<?php echo $assetId99; ?>" data-room=" <?php echo htmlspecialchars($room99); ?>" data-floor="<?php echo htmlspecialchars($floor99); ?>" data-image=" <?php echo base64_encode($upload_img99); ?>" data-category="<?php echo htmlspecialchars($category99); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName99); ?>" data-status="<?php echo htmlspecialchars($status99); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status99); ?>; 
    position:absolute; top:100px; left:1010px;'>
                        </div>

                        <!-- ASSET 100 -->
                        <img src='../image.php?id=100' style='width:17px; cursor:pointer; position:absolute; top:100px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal100' class="asset-image" onclick='fetchAssetData(100);' data-id="<?php echo $assetId100; ?>" data-room=" <?php echo htmlspecialchars($room100); ?>" data-floor="<?php echo htmlspecialchars($floor100); ?>" data-image=" <?php echo base64_encode($upload_img100); ?>" data-category="<?php echo htmlspecialchars($category100); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName100); ?>" data-status="<?php echo htmlspecialchars($status100); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status100); ?>; 
    position:absolute; top:100px; left:1030px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 101 -->
                        <img src='../image.php?id=101' style='width:17px; cursor:pointer; position:absolute; top:120px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal101' class="asset-image" onclick='fetchAssetData(101);' data-id="<?php echo $assetId101; ?>" data-room=" <?php echo htmlspecialchars($room101); ?>" data-floor="<?php echo htmlspecialchars($floor101); ?>" data-image=" <?php echo base64_encode($upload_img101); ?>" data-category="<?php echo htmlspecialchars($category101); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName101); ?>" data-status="<?php echo htmlspecialchars($status101); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status101); ?>; 
    position:absolute; top:120px; left:950px;'>
                        </div>

                        <!-- ASSET 102 -->
                        <img src='../image.php?id=102' style='width:17px; cursor:pointer; position:absolute; top:120px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal102' class="asset-image" onclick='fetchAssetData(102);' data-id="<?php echo $assetId102; ?>" data-room=" <?php echo htmlspecialchars($room102); ?>" data-floor="<?php echo htmlspecialchars($floor102); ?>" data-image=" <?php echo base64_encode($upload_img102); ?>" data-category="<?php echo htmlspecialchars($category102); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName102); ?>" data-status="<?php echo htmlspecialchars($status102); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status102); ?>; 
    position:absolute; top:120px; left:970px;'>
                        </div>

                        <!-- ASSET 103 -->
                        <img src='../image.php?id=103' style='width:17px; cursor:pointer; position:absolute; top:120px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal103' class="asset-image" onclick='fetchAssetData(103);' data-id="<?php echo $assetId103; ?>" data-room=" <?php echo htmlspecialchars($room103); ?>" data-floor="<?php echo htmlspecialchars($floor103); ?>" data-image=" <?php echo base64_encode($upload_img103); ?>" data-category="<?php echo htmlspecialchars($category103); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName103); ?>" data-status="<?php echo htmlspecialchars($status103); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status103); ?>; 
    position:absolute; top:120px; left:990px;'>
                        </div>

                        <!-- ASSET 104 -->
                        <img src='../image.php?id=104' style='width:17px; cursor:pointer; position:absolute; top:120px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal104' class="asset-image" onclick='fetchAssetData(104);' data-id="<?php echo $assetId104; ?>" data-room=" <?php echo htmlspecialchars($room104); ?>" data-floor="<?php echo htmlspecialchars($floor104); ?>" data-image=" <?php echo base64_encode($upload_img104); ?>" data-category="<?php echo htmlspecialchars($category104); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName104); ?>" data-status="<?php echo htmlspecialchars($status104); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status104); ?>; 
    position:absolute; top:120px; left:1010px;'>
                        </div>

                        <!-- ASSET 105 -->
                        <img src='../image.php?id=105' style='width:17px; cursor:pointer; position:absolute; top:120px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal105' class="asset-image" onclick='fetchAssetData(105);' data-id="<?php echo $assetId105; ?>" data-room=" <?php echo htmlspecialchars($room105); ?>" data-floor="<?php echo htmlspecialchars($floor105); ?>" data-image=" <?php echo base64_encode($upload_img105); ?>" data-category="<?php echo htmlspecialchars($category105); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName105); ?>" data-status="<?php echo htmlspecialchars($status105); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status105); ?>; 
    position:absolute; top:120px; left:1030px;'>
                        </div>

                        <!-- ASSET 106 -->
                        <img src='../image.php?id=106' style='width:17px; cursor:pointer; position:absolute; top:140px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal106' class="asset-image" onclick='fetchAssetData(106);' data-id="<?php echo $assetId106; ?>" data-room=" <?php echo htmlspecialchars($room106); ?>" data-floor="<?php echo htmlspecialchars($floor106); ?>" data-image=" <?php echo base64_encode($upload_img106); ?>" data-category="<?php echo htmlspecialchars($category106); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName106); ?>" data-status="<?php echo htmlspecialchars($status106); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status106); ?>; 
    position:absolute; top:140px; left:950px;'>
                        </div>

                        <!-- ASSET 107 -->
                        <img src='../image.php?id=107' style='width:17px; cursor:pointer; position:absolute; top:140px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal107' class="asset-image" onclick='fetchAssetData(107);' data-id="<?php echo $assetId107; ?>" data-room=" <?php echo htmlspecialchars($room107); ?>" data-floor="<?php echo htmlspecialchars($floor107); ?>" data-image=" <?php echo base64_encode($upload_img107); ?>" data-category="<?php echo htmlspecialchars($category107); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName107); ?>" data-status="<?php echo htmlspecialchars($status107); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status107); ?>; 
    position:absolute; top:140px; left:970px;'>
                        </div>

                        <!-- ASSET 108 -->
                        <img src='../image.php?id=108' style='width:17px; cursor:pointer; position:absolute; top:140px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal108' class="asset-image" onclick='fetchAssetData(108);' data-id="<?php echo $assetId108; ?>" data-room=" <?php echo htmlspecialchars($room108); ?>" data-floor="<?php echo htmlspecialchars($floor108); ?>" data-image=" <?php echo base64_encode($upload_img108); ?>" data-category="<?php echo htmlspecialchars($category108); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName108); ?>" data-status="<?php echo htmlspecialchars($status108); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status108); ?>; 
    position:absolute; top:140px; left:990px;'>
                        </div>


                        <!-- ASSET 109 -->
                        <img src='../image.php?id=109' style='width:17px; cursor:pointer; position:absolute; top:140px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal109' class="asset-image" onclick='fetchAssetData(109);' data-id="<?php echo $assetId109; ?>" data-room=" <?php echo htmlspecialchars($room109); ?>" data-floor="<?php echo htmlspecialchars($floor109); ?>" data-image=" <?php echo base64_encode($upload_img109); ?>" data-category="<?php echo htmlspecialchars($category109); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName109); ?>" data-status="<?php echo htmlspecialchars($status109); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status109); ?>; 
    position:absolute; top:140px; left:1010px;'>
                        </div>

                        <!-- ASSET 110 -->
                        <img src='../image.php?id=110' style='width:17px; cursor:pointer; position:absolute; top:140px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal110' class="asset-image" onclick='fetchAssetData(110);' data-id="<?php echo $assetId110; ?>" data-room=" <?php echo htmlspecialchars($room110); ?>" data-floor="<?php echo htmlspecialchars($floor110); ?>" data-image=" <?php echo base64_encode($upload_img110); ?>" data-category="<?php echo htmlspecialchars($category110); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName110); ?>" data-status="<?php echo htmlspecialchars($status110); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status110); ?>; 
    position:absolute; top:140px; left:1030px;'>
                        </div>

                        <!-- ASSET 111 -->
                        <img src='../image.php?id=111' style='width:17px; cursor:pointer; position:absolute; top:160px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal111' class="asset-image" onclick='fetchAssetData(111);' data-id="<?php echo $assetId111; ?>" data-room=" <?php echo htmlspecialchars($room111); ?>" data-floor="<?php echo htmlspecialchars($floor111); ?>" data-image=" <?php echo base64_encode($upload_img111); ?>" data-category="<?php echo htmlspecialchars($category111); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName111); ?>" data-status="<?php echo htmlspecialchars($status111); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status111); ?>; 
    position:absolute; top:160px; left:950px;'>
                        </div>

                        <!-- ASSET 112 -->
                        <img src='../image.php?id=112' style='width:17px; cursor:pointer; position:absolute; top:160px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal112' class="asset-image" onclick='fetchAssetData(112);' data-id="<?php echo $assetId112; ?>" data-room=" <?php echo htmlspecialchars($room112); ?>" data-floor="<?php echo htmlspecialchars($floor112); ?>" data-image=" <?php echo base64_encode($upload_img112); ?>" data-category="<?php echo htmlspecialchars($category112); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName112); ?>" data-status="<?php echo htmlspecialchars($status112); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status112); ?>; 
    position:absolute; top:160px; left:970px;'>
                        </div>


                        <!-- ASSET 113 -->
                        <img src='../image.php?id=113' style='width:17px; cursor:pointer; position:absolute; top:160px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal113' class="asset-image" onclick='fetchAssetData(113);' data-id="<?php echo $assetId113; ?>" data-room=" <?php echo htmlspecialchars($room113); ?>" data-floor="<?php echo htmlspecialchars($floor113); ?>" data-image=" <?php echo base64_encode($upload_img113); ?>" data-category="<?php echo htmlspecialchars($category113); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName113); ?>" data-status="<?php echo htmlspecialchars($status113); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status113); ?>; 
    position:absolute; top:160px; left:990px;'>
                        </div>

                        <!-- ASSET 114 -->
                        <img src='../image.php?id=114' style='width:17px; cursor:pointer; position:absolute; top:160px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal114' class="asset-image" onclick='fetchAssetData(114);' data-id="<?php echo $assetId114; ?>" data-room=" <?php echo htmlspecialchars($room114); ?>" data-floor="<?php echo htmlspecialchars($floor114); ?>" data-image=" <?php echo base64_encode($upload_img114); ?>" data-category="<?php echo htmlspecialchars($category114); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName114); ?>" data-status="<?php echo htmlspecialchars($status114); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status114); ?>; 
    position:absolute; top:160px; left:1010px;'>
                        </div>

                        <!-- ASSET 115 -->
                        <img src='../image.php?id=115' style='width:17px; cursor:pointer; position:absolute; top:160px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal115' class="asset-image" onclick='fetchAssetData(115);' data-id="<?php echo $assetId115; ?>" data-room=" <?php echo htmlspecialchars($room115); ?>" data-floor="<?php echo htmlspecialchars($floor115); ?>" data-image=" <?php echo base64_encode($upload_img115); ?>" data-category="<?php echo htmlspecialchars($category115); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName115); ?>" data-status="<?php echo htmlspecialchars($status115); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status115); ?>; 
    position:absolute; top:160px; left:1030px;'>
                        </div>

                        <!-- ASSET 1441 -->
                        <img src='../image.php?id=1441' style='width:17px; cursor:pointer; position:absolute; top:210px; left:979px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' class="asset-image" data-bs-target='#imageModal1441' onclick='fetchAssetData(1441);' data-id="<?php echo $assetId1441; ?>" data-room=" <?php echo htmlspecialchars($room1441); ?>" data-floor="<?php echo htmlspecialchars($floor1441); ?>" data-image=" <?php echo base64_encode($upload_img1441); ?>" data-category="<?php echo htmlspecialchars($category1441); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName1441); ?>" data-status="<?php echo htmlspecialchars($status1441); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status1441); ?>; 
    position:absolute; top:220px; left:980px;'>
                        </div>


                        <!-- START OF IB104A -->

                        <!-- START OF ROW 1-->

                        <!-- ASSET 116 -->
                        <img src='../image.php?id=116' style='width:17px; cursor:pointer; position:absolute; top:80px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal116' class="asset-image" onclick='fetchAssetData(116);' data-id="<?php echo $assetId116; ?>" data-room=" <?php echo htmlspecialchars($room116); ?>" data-floor="<?php echo htmlspecialchars($floor116); ?>" data-image=" <?php echo base64_encode($upload_img116); ?>" data-category="<?php echo htmlspecialchars($category116); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName116); ?>" data-status="<?php echo htmlspecialchars($status116); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status116); ?>; 
    position:absolute; top:80px; left:820px;'>
                        </div>

                        <!-- ASSET 117 -->
                        <img src='../image.php?id=117' style='width:17px; cursor:pointer; position:absolute; top:80px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal117' class="asset-image" onclick='fetchAssetData(117);' data-id="<?php echo $assetId117; ?>" data-room=" <?php echo htmlspecialchars($room117); ?>" data-floor="<?php echo htmlspecialchars($floor117); ?>" data-image=" <?php echo base64_encode($upload_img117); ?>" data-category="<?php echo htmlspecialchars($category117); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName117); ?>" data-status="<?php echo htmlspecialchars($status117); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status117); ?>; 
    position:absolute; top:80px; left:840px;'>
                        </div>

                        <!-- ASSET 118 -->
                        <img src='../image.php?id=118' style='width:17px; cursor:pointer; position:absolute; top:80px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal118' class="asset-image" onclick='fetchAssetData(118);' data-id="<?php echo $assetId118; ?>" data-room=" <?php echo htmlspecialchars($room118); ?>" data-floor="<?php echo htmlspecialchars($floor118); ?>" data-image=" <?php echo base64_encode($upload_img118); ?>" data-category="<?php echo htmlspecialchars($category118); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName118); ?>" data-status="<?php echo htmlspecialchars($status118); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status118); ?>; 
    position:absolute; top:80px; left:860px;'>
                        </div>

                        <!-- ASSET 119 -->
                        <img src='../image.php?id=119' style='width:17px; cursor:pointer; position:absolute; top:80px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal119' class="asset-image" onclick='fetchAssetData(119);' data-id="<?php echo $assetId119; ?>" data-room=" <?php echo htmlspecialchars($room119); ?>" data-floor="<?php echo htmlspecialchars($floor119); ?>" data-image=" <?php echo base64_encode($upload_img119); ?>" data-category="<?php echo htmlspecialchars($category119); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName119); ?>" data-status="<?php echo htmlspecialchars($status119); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status119); ?>; 
    position:absolute; top:80px; left:880px;'>
                        </div>


                        <!-- ASSET 120 -->
                        <img src='../image.php?id=120' style='width:17px; cursor:pointer; position:absolute; top:80px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal120' class="asset-image" onclick='fetchAssetData(120);' data-id="<?php echo $assetId120; ?>" data-room=" <?php echo htmlspecialchars($room120); ?>" data-floor="<?php echo htmlspecialchars($floor120); ?>" data-image=" <?php echo base64_encode($upload_img120); ?>" data-category="<?php echo htmlspecialchars($category120); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName120); ?>" data-status="<?php echo htmlspecialchars($status120); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status120); ?>; 
    position:absolute; top:80px; left:900px;'>
                        </div>

                        <!-- ASSET 121 -->
                        <img src='../image.php?id=121' style='width:17px; cursor:pointer; position:absolute; top:100px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal121' class="asset-image" onclick='fetchAssetData(121);' data-id="<?php echo $assetId121; ?>" data-room=" <?php echo htmlspecialchars($room121); ?>" data-floor="<?php echo htmlspecialchars($floor121); ?>" data-image=" <?php echo base64_encode($upload_img121); ?>" data-category="<?php echo htmlspecialchars($category121); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName121); ?>" data-status="<?php echo htmlspecialchars($status121); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status121); ?>; 
    position:absolute; top:100px; left:820px;'>
                        </div>

                        <!-- ASSET 122 -->
                        <img src='../image.php?id=122' style='width:17px; cursor:pointer; position:absolute; top:100px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal122' class="asset-image" onclick='fetchAssetData(122);' data-id="<?php echo $assetId122; ?>" data-room=" <?php echo htmlspecialchars($room122); ?>" data-floor="<?php echo htmlspecialchars($floor122); ?>" data-image=" <?php echo base64_encode($upload_img122); ?>" data-category="<?php echo htmlspecialchars($category122); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName122); ?>" data-status="<?php echo htmlspecialchars($status122); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status122); ?>; 
    position:absolute; top:100px; left:840px;'>
                        </div>

                        <!-- ASSET 123 -->
                        <img src='../image.php?id=123' style='width:17px; cursor:pointer; position:absolute; top:100px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal123' class="asset-image" onclick='fetchAssetData(123);' data-id="<?php echo $assetId123; ?>" data-room=" <?php echo htmlspecialchars($room123); ?>" data-floor="<?php echo htmlspecialchars($floor123); ?>" data-image=" <?php echo base64_encode($upload_img123); ?>" data-category="<?php echo htmlspecialchars($category123); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName123); ?>" data-status="<?php echo htmlspecialchars($status123); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status123); ?>; 
    position:absolute; top:100px; left:860px;'>
                        </div>


                        <!-- ASSET 124 -->
                        <img src='../image.php?id=124' style='width:17px; cursor:pointer; position:absolute; top:100px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal124' class="asset-image" onclick='fetchAssetData(124);' data-id="<?php echo $assetId124; ?>" data-room=" <?php echo htmlspecialchars($room124); ?>" data-floor="<?php echo htmlspecialchars($floor124); ?>" data-image=" <?php echo base64_encode($upload_img124); ?>" data-category="<?php echo htmlspecialchars($category124); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName124); ?>" data-status="<?php echo htmlspecialchars($status124); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status124); ?>; 
    position:absolute; top:100px; left:880px;'>
                        </div>

                        <!-- ASSET 125 -->
                        <img src='../image.php?id=125' style='width:17px; cursor:pointer; position:absolute; top:100px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal125' class="asset-image" onclick='fetchAssetData(125);' data-id="<?php echo $assetId125; ?>" data-room=" <?php echo htmlspecialchars($room125); ?>" data-floor="<?php echo htmlspecialchars($floor125); ?>" data-image=" <?php echo base64_encode($upload_img125); ?>" data-category="<?php echo htmlspecialchars($category125); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName125); ?>" data-status="<?php echo htmlspecialchars($status125); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status125); ?>; 
    position:absolute; top:100px; left:900px;'>
                        </div>

                        <!-- ASSET 126 -->
                        <img src='../image.php?id=126' style='width:17px; cursor:pointer; position:absolute; top:120px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal126' class="asset-image" onclick='fetchAssetData(126);' data-id="<?php echo $assetId126; ?>" data-room=" <?php echo htmlspecialchars($room126); ?>" data-floor="<?php echo htmlspecialchars($floor126); ?>" data-image=" <?php echo base64_encode($upload_img126); ?>" data-category="<?php echo htmlspecialchars($category126); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName126); ?>" data-status="<?php echo htmlspecialchars($status126); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status126); ?>; 
    position:absolute; top:120px; left:820px;'>
                        </div>

                        <!-- ASSET 127 -->
                        <img src='../image.php?id=127' style='width:17px; cursor:pointer; position:absolute; top:120px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal127' class="asset-image" onclick='fetchAssetData(127);' data-id="<?php echo $assetId127; ?>" data-room=" <?php echo htmlspecialchars($room127); ?>" data-floor="<?php echo htmlspecialchars($floor127); ?>" data-image=" <?php echo base64_encode($upload_img127); ?>" data-category="<?php echo htmlspecialchars($category127); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName127); ?>" data-status="<?php echo htmlspecialchars($status127); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status127); ?>; 
    position:absolute; top:120px; left:840px;'>
                        </div>


                        <!-- ASSET 128 -->
                        <img src='../image.php?id=128' style='width:17px; cursor:pointer; position:absolute; top:120px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal128' class="asset-image" onclick='fetchAssetData(128);' data-id="<?php echo $assetId128; ?>" data-room=" <?php echo htmlspecialchars($room128); ?>" data-floor="<?php echo htmlspecialchars($floor128); ?>" data-image=" <?php echo base64_encode($upload_img128); ?>" data-category="<?php echo htmlspecialchars($category128); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName128); ?>" data-status="<?php echo htmlspecialchars($status128); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status128); ?>; 
    position:absolute; top:120px; left:860px;'>
                        </div>

                        <!-- ASSET 129 -->
                        <img src='../image.php?id=129' style='width:17px; cursor:pointer; position:absolute; top:120px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal129' class="asset-image" onclick='fetchAssetData(129);' data-id="<?php echo $assetId129; ?>" data-room=" <?php echo htmlspecialchars($room129); ?>" data-floor="<?php echo htmlspecialchars($floor129); ?>" data-image=" <?php echo base64_encode($upload_img129); ?>" data-category="<?php echo htmlspecialchars($category129); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName129); ?>" data-status="<?php echo htmlspecialchars($status129); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status129); ?>; 
    position:absolute; top:120px; left:880px;'>
                        </div>

                        <!-- ASSET 130 -->
                        <img src='../image.php?id=130' style='width:17px; cursor:pointer; position:absolute; top:120px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal130' class="asset-image" onclick='fetchAssetData(130);' data-id="<?php echo $assetId130; ?>" data-room=" <?php echo htmlspecialchars($room130); ?>" data-floor="<?php echo htmlspecialchars($floor130); ?>" data-image=" <?php echo base64_encode($upload_img130); ?>" data-category="<?php echo htmlspecialchars($category130); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName130); ?>" data-status="<?php echo htmlspecialchars($status130); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status130); ?>; 
    position:absolute; top:120px; left:900px;'>
                        </div>

                        <!-- ASSET 131 -->
                        <img src='../image.php?id=131' style='width:17px; cursor:pointer; position:absolute; top:140px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal131' class="asset-image" onclick='fetchAssetData(131);' data-id="<?php echo $assetId131; ?>" data-room=" <?php echo htmlspecialchars($room131); ?>" data-floor="<?php echo htmlspecialchars($floor131); ?>" data-image=" <?php echo base64_encode($upload_img131); ?>" data-category="<?php echo htmlspecialchars($category131); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName131); ?>" data-status="<?php echo htmlspecialchars($status131); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status131); ?>; 
    position:absolute; top:140px; left:820px;'>
                        </div>


                        <!-- ASSET 132 -->
                        <img src='../image.php?id=132' style='width:17px; cursor:pointer; position:absolute; top:140px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal132' class="asset-image" onclick='fetchAssetData(132);' data-id="<?php echo $assetId132; ?>" data-room=" <?php echo htmlspecialchars($room132); ?>" data-floor="<?php echo htmlspecialchars($floor132); ?>" data-image=" <?php echo base64_encode($upload_img132); ?>" data-category="<?php echo htmlspecialchars($category132); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName132); ?>" data-status="<?php echo htmlspecialchars($status132); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status132); ?>; 
    position:absolute; top:140px; left:840px;'>
                        </div>

                        <!-- ASSET 133 -->
                        <img src='../image.php?id=133' style='width:17px; cursor:pointer; position:absolute; top:140px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal133' class="asset-image" onclick='fetchAssetData(133);' data-id="<?php echo $assetId133; ?>" data-room=" <?php echo htmlspecialchars($room133); ?>" data-floor="<?php echo htmlspecialchars($floor133); ?>" data-image=" <?php echo base64_encode($upload_img133); ?>" data-category="<?php echo htmlspecialchars($category133); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName133); ?>" data-status="<?php echo htmlspecialchars($status133); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status133); ?>; 
    position:absolute; top:140px; left:860px;'>
                        </div>

                        <!-- ASSET 134 -->
                        <img src='../image.php?id=134' style='width:17px; cursor:pointer; position:absolute; top:140px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal134' class="asset-image" onclick='fetchAssetData(134);' data-id="<?php echo $assetId134; ?>" data-room=" <?php echo htmlspecialchars($room134); ?>" data-floor="<?php echo htmlspecialchars($floor134); ?>" data-image=" <?php echo base64_encode($upload_img134); ?>" data-category="<?php echo htmlspecialchars($category134); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName134); ?>" data-status="<?php echo htmlspecialchars($status134); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status134); ?>; 
    position:absolute; top:140px; left:880px;'>
                        </div>

                        <!-- ASSET 135 -->
                        <img src='../image.php?id=135' style='width:17px; cursor:pointer; position:absolute; top:140px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal135' class="asset-image" onclick='fetchAssetData(135);' data-id="<?php echo $assetId135; ?>" data-room=" <?php echo htmlspecialchars($room135); ?>" data-floor="<?php echo htmlspecialchars($floor135); ?>" data-image=" <?php echo base64_encode($upload_img135); ?>" data-category="<?php echo htmlspecialchars($category135); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName135); ?>" data-status="<?php echo htmlspecialchars($status135); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status135); ?>; 
    position:absolute; top:140px; left:900px;'>
                        </div>

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 136 -->
                        <img src='../image.php?id=136' style='width:17px; cursor:pointer; position:absolute; top:160px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal136' class="asset-image" onclick='fetchAssetData(136);' data-id="<?php echo $assetId136; ?>" data-room=" <?php echo htmlspecialchars($room136); ?>" data-floor="<?php echo htmlspecialchars($floor136); ?>" data-image=" <?php echo base64_encode($upload_img136); ?>" data-category="<?php echo htmlspecialchars($category136); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName136); ?>" data-status="<?php echo htmlspecialchars($status136); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status136); ?>; 
    position:absolute; top:160px; left:820px;'>
                        </div>

                        <!-- ASSET 137 -->
                        <img src='../image.php?id=137' style='width:17px; cursor:pointer; position:absolute; top:160px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal137' class="asset-image" onclick='fetchAssetData(137);' data-id="<?php echo $assetId137; ?>" data-room=" <?php echo htmlspecialchars($room137); ?>" data-floor="<?php echo htmlspecialchars($floor137); ?>" data-image=" <?php echo base64_encode($upload_img137); ?>" data-category="<?php echo htmlspecialchars($category137); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName137); ?>" data-status="<?php echo htmlspecialchars($status137); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status137); ?>; 
    position:absolute; top:160px; left:840px;'>
                        </div>

                        <!-- ASSET 138 -->
                        <img src='../image.php?id=138' style='width:17px; cursor:pointer; position:absolute; top:160px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal138' class="asset-image" onclick='fetchAssetData(138);' data-id="<?php echo $assetId138; ?>" data-room=" <?php echo htmlspecialchars($room138); ?>" data-floor="<?php echo htmlspecialchars($floor138); ?>" data-image=" <?php echo base64_encode($upload_img138); ?>" data-category="<?php echo htmlspecialchars($category138); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName138); ?>" data-status="<?php echo htmlspecialchars($status138); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status138); ?>; 
    position:absolute; top:160px; left:860px;'>
                        </div>

                        <!-- ASSET 139 -->
                        <img src='../image.php?id=139' style='width:17px; cursor:pointer; position:absolute; top:160px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal139' class="asset-image" onclick='fetchAssetData(139);' data-id="<?php echo $assetId139; ?>" data-room=" <?php echo htmlspecialchars($room139); ?>" data-floor="<?php echo htmlspecialchars($floor139); ?>" data-image=" <?php echo base64_encode($upload_img139); ?>" data-category="<?php echo htmlspecialchars($category139); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName139); ?>" data-status="<?php echo htmlspecialchars($status139); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status139); ?>; 
    position:absolute; top:160px; left:880px;'>
                        </div>

                        <!-- ASSET 140 -->
                        <img src='../image.php?id=140' style='width:17px; cursor:pointer; position:absolute; top:160px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal140' class="asset-image" onclick='fetchAssetData(140);' data-id="<?php echo $assetId140; ?>" data-room=" <?php echo htmlspecialchars($room140); ?>" data-floor="<?php echo htmlspecialchars($floor140); ?>" data-image=" <?php echo base64_encode($upload_img140); ?>" data-category="<?php echo htmlspecialchars($category140); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName140); ?>" data-status="<?php echo htmlspecialchars($status140); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status140); ?>; 
    position:absolute; top:160px; left:900px;'>
                        </div>

                        <!-- ASSET 141 -->
                        <img src='../image.php?id=141' style='width:17px; cursor:pointer; position:absolute; top:210px; left:850px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' class="asset-image" data-bs-target='#imageModal141' onclick='fetchAssetData(141);' data-id="<?php echo $assetId141; ?>" data-room=" <?php echo htmlspecialchars($room141); ?>" data-floor="<?php echo htmlspecialchars($floor141); ?>" data-image=" <?php echo base64_encode($upload_img141); ?>" data-category="<?php echo htmlspecialchars($category141); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName141); ?>" data-status="<?php echo htmlspecialchars($status141); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status141); ?>; 
    position:absolute; top:220px; left:850px;'>
                        </div>

                        <!-- ASSET 142 -->
                        <img src='../image.php?id=142' style='width:17px; cursor:pointer; position:absolute; top:80px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal142' class="asset-image" onclick='fetchAssetData(142);' data-id="<?php echo $assetId142; ?>" data-room=" <?php echo htmlspecialchars($room142); ?>" data-floor="<?php echo htmlspecialchars($floor142); ?>" data-image=" <?php echo base64_encode($upload_img142); ?>" data-category="<?php echo htmlspecialchars($category142); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName142); ?>" data-status="<?php echo htmlspecialchars($status142); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status142); ?>; 
    position:absolute; top:80px; left:690px;'>
                        </div>

                        <!-- ASSET 143 -->
                        <img src='../image.php?id=143' style='width:17px; cursor:pointer; position:absolute; top:80px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal143' class="asset-image" onclick='fetchAssetData(143);' data-id="<?php echo $assetId143; ?>" data-room=" <?php echo htmlspecialchars($room143); ?>" data-floor="<?php echo htmlspecialchars($floor143); ?>" data-image=" <?php echo base64_encode($upload_img143); ?>" data-category="<?php echo htmlspecialchars($category143); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName143); ?>" data-status="<?php echo htmlspecialchars($status143); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status143); ?>; 
    position:absolute; top:80px; left:710px;'>
                        </div>


                        <!-- ASSET 144 -->
                        <img src='../image.php?id=144' style='width:17px; cursor:pointer; position:absolute; top:80px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal144' class="asset-image" onclick='fetchAssetData(144);' data-id="<?php echo $assetId144; ?>" data-room=" <?php echo htmlspecialchars($room144); ?>" data-floor="<?php echo htmlspecialchars($floor144); ?>" data-image=" <?php echo base64_encode($upload_img144); ?>" data-category="<?php echo htmlspecialchars($category144); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName144); ?>" data-status="<?php echo htmlspecialchars($status144); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status144); ?>; 
    position:absolute; top:80px; left:730px;'>
                        </div>

                        <!-- ASSET 145 -->
                        <img src='../image.php?id=145' style='width:17px; cursor:pointer; position:absolute; top:80px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal145' class="asset-image" onclick='fetchAssetData(145);' data-id="<?php echo $assetId145; ?>" data-room=" <?php echo htmlspecialchars($room145); ?>" data-floor="<?php echo htmlspecialchars($floor145); ?>" data-image=" <?php echo base64_encode($upload_img145); ?>" data-category="<?php echo htmlspecialchars($category145); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName145); ?>" data-status="<?php echo htmlspecialchars($status145); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status145); ?>; 
    position:absolute; top:80px; left:750px;'>
                        </div>

                        <!-- ASSET 146 -->
                        <img src='../image.php?id=146' style='width:17px; cursor:pointer; position:absolute; top:80px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal146' class="asset-image" onclick='fetchAssetData(146);' data-id="<?php echo $assetId146; ?>" data-room=" <?php echo htmlspecialchars($room146); ?>" data-floor="<?php echo htmlspecialchars($floor146); ?>" data-image=" <?php echo base64_encode($upload_img146); ?>" data-category="<?php echo htmlspecialchars($category146); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName146); ?>" data-status="<?php echo htmlspecialchars($status146); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status146); ?>; 
    position:absolute; top:80px; left:770px;'>
                        </div>

                        <!-- ASSET 147 -->
                        <img src='../image.php?id=147' style='width:17px; cursor:pointer; position:absolute; top:100px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal147' class="asset-image" onclick='fetchAssetData(147);' data-id="<?php echo $assetId147; ?>" data-room=" <?php echo htmlspecialchars($room147); ?>" data-floor="<?php echo htmlspecialchars($floor147); ?>" data-image=" <?php echo base64_encode($upload_img147); ?>" data-category="<?php echo htmlspecialchars($category147); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName147); ?>" data-status="<?php echo htmlspecialchars($status147); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status147); ?>; 
    position:absolute; top:100px; left:690px;'>
                        </div>

                        <!-- ASSET 148 -->
                        <img src='../image.php?id=148' style='width:17px; cursor:pointer; position:absolute; top:100px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal148' class="asset-image" onclick='fetchAssetData(148);' data-id="<?php echo $assetId148; ?>" data-room=" <?php echo htmlspecialchars($room148); ?>" data-floor="<?php echo htmlspecialchars($floor148); ?>" data-image=" <?php echo base64_encode($upload_img148); ?>" data-category="<?php echo htmlspecialchars($category148); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName148); ?>" data-status="<?php echo htmlspecialchars($status148); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status148); ?>; 
    position:absolute; top:100px; left:710px;'>
                        </div>

                        <!-- ASSET 149 -->
                        <img src='../image.php?id=149' style='width:17px; cursor:pointer; position:absolute; top:100px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal149' class="asset-image" onclick='fetchAssetData(149);' data-id="<?php echo $assetId149; ?>" data-room=" <?php echo htmlspecialchars($room149); ?>" data-floor="<?php echo htmlspecialchars($floor149); ?>" data-image=" <?php echo base64_encode($upload_img149); ?>" data-category="<?php echo htmlspecialchars($category149); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName149); ?>" data-status="<?php echo htmlspecialchars($status149); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status149); ?>; 
    position:absolute; top:100px; left:730px;'>
                        </div>

                        <!-- ASSET 150 -->
                        <img src='../image.php?id=150' style='width:17px; cursor:pointer; position:absolute; top:100px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal150' class="asset-image" onclick='fetchAssetData(150);' data-id="<?php echo $assetId150; ?>" data-room=" <?php echo htmlspecialchars($room150); ?>" data-floor="<?php echo htmlspecialchars($floor150); ?>" data-image=" <?php echo base64_encode($upload_img150); ?>" data-category="<?php echo htmlspecialchars($category150); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName150); ?>" data-status="<?php echo htmlspecialchars($status150); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status150); ?>; 
    position:absolute; top:100px; left:750px;'>
                        </div>

                        <!-- ASSET 151 -->
                        <img src='../image.php?id=151' style='width:17px; cursor:pointer; position:absolute; top:100px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal151' class="asset-image" onclick='fetchAssetData(151);' data-id="<?php echo $assetId151; ?>" data-room=" <?php echo htmlspecialchars($room151); ?>" data-floor="<?php echo htmlspecialchars($floor151); ?>" data-image=" <?php echo base64_encode($upload_img151); ?>" data-category="<?php echo htmlspecialchars($category151); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName151); ?>" data-status="<?php echo htmlspecialchars($status151); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status151); ?>; 
    position:absolute; top:100px; left:770px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 152 -->
                        <img src='../image.php?id=152' style='width:17px; cursor:pointer; position:absolute; top:120px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal152' class="asset-image" onclick='fetchAssetData(152);' data-id="<?php echo $assetId152; ?>" data-room=" <?php echo htmlspecialchars($room152); ?>" data-floor="<?php echo htmlspecialchars($floor152); ?>" data-image=" <?php echo base64_encode($upload_img152); ?>" data-category="<?php echo htmlspecialchars($category152); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName152); ?>" data-status="<?php echo htmlspecialchars($status152); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status152); ?>; 
    position:absolute; top:120px; left:690px;'>
                        </div>

                        <!-- ASSET 153 -->
                        <img src='../image.php?id=153' style='width:17px; cursor:pointer; position:absolute; top:120px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal153' class="asset-image" onclick='fetchAssetData(153);' data-id="<?php echo $assetId153; ?>" data-room=" <?php echo htmlspecialchars($room153); ?>" data-floor="<?php echo htmlspecialchars($floor153); ?>" data-image=" <?php echo base64_encode($upload_img153); ?>" data-category="<?php echo htmlspecialchars($category153); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName153); ?>" data-status="<?php echo htmlspecialchars($status153); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status153); ?>; 
    position:absolute; top:120px; left:710px;'>
                        </div>

                        <!-- ASSET 154 -->
                        <img src='../image.php?id=154' style='width:17px; cursor:pointer; position:absolute; top:120px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal154' class="asset-image" onclick='fetchAssetData(154);' data-id="<?php echo $assetId154; ?>" data-room=" <?php echo htmlspecialchars($room154); ?>" data-floor="<?php echo htmlspecialchars($floor154); ?>" data-image=" <?php echo base64_encode($upload_img154); ?>" data-category="<?php echo htmlspecialchars($category154); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName154); ?>" data-status="<?php echo htmlspecialchars($status154); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status154); ?>; 
    position:absolute; top:120px; left:730px;'>
                        </div>

                        <!-- ASSET 155 -->
                        <img src='../image.php?id=155' style='width:17px; cursor:pointer; position:absolute; top:120px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal155' class="asset-image" onclick='fetchAssetData(155);' data-id="<?php echo $assetId155; ?>" data-room=" <?php echo htmlspecialchars($room155); ?>" data-floor="<?php echo htmlspecialchars($floor155); ?>" data-image=" <?php echo base64_encode($upload_img155); ?>" data-category="<?php echo htmlspecialchars($category155); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName155); ?>" data-status="<?php echo htmlspecialchars($status155); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status155); ?>; 
    position:absolute; top:120px; left:750px;'>
                        </div>


                        <!-- ASSET 156 -->
                        <img src='../image.php?id=156' style='width:17px; cursor:pointer; position:absolute; top:120px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal156' class="asset-image" onclick='fetchAssetData(156);' data-id="<?php echo $assetId156; ?>" data-room=" <?php echo htmlspecialchars($room156); ?>" data-floor="<?php echo htmlspecialchars($floor156); ?>" data-image=" <?php echo base64_encode($upload_img156); ?>" data-category="<?php echo htmlspecialchars($category156); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName156); ?>" data-status="<?php echo htmlspecialchars($status156); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status156); ?>; 
    position:absolute; top:120px; left:770px;'>
                        </div>

                        <!-- ASSET 157 -->
                        <img src='../image.php?id=157' style='width:17px; cursor:pointer; position:absolute; top:140px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal157' class="asset-image" onclick='fetchAssetData(157);' data-id="<?php echo $assetId157; ?>" data-room=" <?php echo htmlspecialchars($room157); ?>" data-floor="<?php echo htmlspecialchars($floor157); ?>" data-image=" <?php echo base64_encode($upload_img157); ?>" data-category="<?php echo htmlspecialchars($category157); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName157); ?>" data-status="<?php echo htmlspecialchars($status157); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status157); ?>; 
    position:absolute; top:140px; left:690px;'>
                        </div>

                        <!-- ASSET 158 -->
                        <img src='../image.php?id=158' style='width:17px; cursor:pointer; position:absolute; top:140px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal158' class="asset-image" onclick='fetchAssetData(158);' data-id="<?php echo $assetId158; ?>" data-room=" <?php echo htmlspecialchars($room158); ?>" data-floor="<?php echo htmlspecialchars($floor158); ?>" data-image=" <?php echo base64_encode($upload_img158); ?>" data-category="<?php echo htmlspecialchars($category158); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName158); ?>" data-status="<?php echo htmlspecialchars($status158); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status158); ?>; 
    position:absolute; top:140px; left:710px;'>
                        </div>

                        <!-- ASSET 159 -->
                        <img src='../image.php?id=159' style='width:17px; cursor:pointer; position:absolute; top:140px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal159' class="asset-image" onclick='fetchAssetData(159);' data-id="<?php echo $assetId159; ?>" data-room=" <?php echo htmlspecialchars($room159); ?>" data-floor="<?php echo htmlspecialchars($floor159); ?>" data-image=" <?php echo base64_encode($upload_img159); ?>" data-category="<?php echo htmlspecialchars($category159); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName159); ?>" data-status="<?php echo htmlspecialchars($status159); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status159); ?>; 
    position:absolute; top:140px; left:730px;'>
                        </div>

                        <!-- ASSET 160 -->
                        <img src='../image.php?id=160' style='width:17px; cursor:pointer; position:absolute; top:140px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal160' class="asset-image" onclick='fetchAssetData(160);' data-id="<?php echo $assetId160; ?>" data-room=" <?php echo htmlspecialchars($room160); ?>" data-floor="<?php echo htmlspecialchars($floor160); ?>" data-image=" <?php echo base64_encode($upload_img160); ?>" data-category="<?php echo htmlspecialchars($category160); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName160); ?>" data-status="<?php echo htmlspecialchars($status160); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status160); ?>; 
    position:absolute; top:140px; left:750px;'>
                        </div>

                        <!-- ASSET 161 -->
                        <img src='../image.php?id=161' style='width:17px; cursor:pointer; position:absolute; top:140px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal161' class="asset-image" onclick='fetchAssetData(161);' data-id="<?php echo $assetId161; ?>" data-room=" <?php echo htmlspecialchars($room161); ?>" data-floor="<?php echo htmlspecialchars($floor161); ?>" data-image=" <?php echo base64_encode($upload_img161); ?>" data-category="<?php echo htmlspecialchars($category161); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName161); ?>" data-status="<?php echo htmlspecialchars($status161); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status161); ?>; 
    position:absolute; top:140px; left:770px;'>
                        </div>

                        <!-- ASSET 162 -->
                        <img src='../image.php?id=162' style='width:17px; cursor:pointer; position:absolute; top:160px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal162' class="asset-image" onclick='fetchAssetData(162);' data-id="<?php echo $assetId162; ?>" data-room=" <?php echo htmlspecialchars($room162); ?>" data-floor="<?php echo htmlspecialchars($floor162); ?>" data-image=" <?php echo base64_encode($upload_img162); ?>" data-category="<?php echo htmlspecialchars($category162); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName162); ?>" data-status="<?php echo htmlspecialchars($status162); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status162); ?>; 
    position:absolute; top:160px; left:690px;'>
                        </div>

                        <!-- ASSET 163 -->
                        <img src='../image.php?id=163' style='width:17px; cursor:pointer; position:absolute; top:160px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal163' class="asset-image" onclick='fetchAssetData(163);' data-id="<?php echo $assetId163; ?>" data-room=" <?php echo htmlspecialchars($room163); ?>" data-floor="<?php echo htmlspecialchars($floor163); ?>" data-image=" <?php echo base64_encode($upload_img163); ?>" data-category="<?php echo htmlspecialchars($category163); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName163); ?>" data-status="<?php echo htmlspecialchars($status163); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status163); ?>; 
    position:absolute; top:160px; left:710px;'>
                        </div>

                        <!-- ASSET 164 -->
                        <img src='../image.php?id=164' style='width:17px; cursor:pointer; position:absolute; top:160px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal164' class="asset-image" onclick='fetchAssetData(164);' data-id="<?php echo $assetId164; ?>" data-room=" <?php echo htmlspecialchars($room164); ?>" data-floor="<?php echo htmlspecialchars($floor164); ?>" data-image=" <?php echo base64_encode($upload_img164); ?>" data-category="<?php echo htmlspecialchars($category164); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName164); ?>" data-status="<?php echo htmlspecialchars($status164); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status164); ?>; 
    position:absolute; top:160px; left:730px;'>
                        </div>

                        <!-- ASSET 165 -->
                        <img src='../image.php?id=165' style='width:17px; cursor:pointer; position:absolute; top:160px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal165' class="asset-image" onclick='fetchAssetData(165);' data-id="<?php echo $assetId165; ?>" data-room=" <?php echo htmlspecialchars($room165); ?>" data-floor="<?php echo htmlspecialchars($floor165); ?>" data-image=" <?php echo base64_encode($upload_img165); ?>" data-category="<?php echo htmlspecialchars($category165); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName165); ?>" data-status="<?php echo htmlspecialchars($status165); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status165); ?>; 
    position:absolute; top:160px; left:750px;'>
                        </div>

                        <!-- ASSET 166 -->
                        <img src='../image.php?id=166' style='width:17px; cursor:pointer; position:absolute; top:160px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal166' class="asset-image" onclick='fetchAssetData(166);' data-id="<?php echo $assetId166; ?>" data-room=" <?php echo htmlspecialchars($room166); ?>" data-floor="<?php echo htmlspecialchars($floor166); ?>" data-image=" <?php echo base64_encode($upload_img166); ?>" data-category="<?php echo htmlspecialchars($category166); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName166); ?>" data-status="<?php echo htmlspecialchars($status166); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status166); ?>; 
    position:absolute; top:160px; left:770px;'>
                        </div>

                        <!-- ASSET 167 -->
                        <img src='../image.php?id=167' style='width:17px; cursor:pointer; position:absolute; top:210px; left:720px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal167' class="asset-image" onclick='fetchAssetData(167);' data-id="<?php echo $assetId167; ?>" data-room=" <?php echo htmlspecialchars($room167); ?>" data-floor="<?php echo htmlspecialchars($floor167); ?>" data-image=" <?php echo base64_encode($upload_img167); ?>" data-category="<?php echo htmlspecialchars($category167); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName167); ?>" data-status="<?php echo htmlspecialchars($status167); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status167); ?>; 
    position:absolute; top:220px; left:720px;'>
                        </div>


                        <!-- END OF ROW 5 -->

                        <!-- END OF IB105A -->
                        <!-- START OF ROW IB106A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 168 -->
                        <img src='../image.php?id=168' style='width:17px; cursor:pointer; position:absolute; top:80px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal168' class="asset-image" onclick='fetchAssetData(168);' data-id="<?php echo $assetId168; ?>" data-room=" <?php echo htmlspecialchars($room168); ?>" data-floor="<?php echo htmlspecialchars($floor168); ?>" data-image=" <?php echo base64_encode($upload_img168); ?>" data-category="<?php echo htmlspecialchars($category168); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName168); ?>" data-status="<?php echo htmlspecialchars($status168); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status168); ?>; 
    position:absolute; top:80px; left:465px;'>
                        </div>

                        <!-- ASSET 169 -->
                        <img src='../image.php?id=169' style='width:17px; cursor:pointer; position:absolute; top:80px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal169' class="asset-image" onclick='fetchAssetData(169);' data-id="<?php echo $assetId169; ?>" data-room=" <?php echo htmlspecialchars($room169); ?>" data-floor="<?php echo htmlspecialchars($floor169); ?>" data-image=" <?php echo base64_encode($upload_img169); ?>" data-category="<?php echo htmlspecialchars($category169); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName169); ?>" data-status="<?php echo htmlspecialchars($status169); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status169); ?>; 
    position:absolute; top:80px; left:485px;'>
                        </div>

                        <!-- ASSET 170 -->
                        <img src='../image.php?id=170' style='width:17px; cursor:pointer; position:absolute; top:80px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal170' class="asset-image" onclick='fetchAssetData(170);' data-id="<?php echo $assetId170; ?>" data-room=" <?php echo htmlspecialchars($room170); ?>" data-floor="<?php echo htmlspecialchars($floor170); ?>" data-image=" <?php echo base64_encode($upload_img170); ?>" data-category="<?php echo htmlspecialchars($category170); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName170); ?>" data-status="<?php echo htmlspecialchars($status170); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status170); ?>; 
    position:absolute; top:80px; left:505px;'>
                        </div>

                        <!-- ASSET 171 -->
                        <img src='../image.php?id=171' style='width:17px; cursor:pointer; position:absolute; top:80px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal171' class="asset-image" onclick='fetchAssetData(171);' data-id="<?php echo $assetId171; ?>" data-room=" <?php echo htmlspecialchars($room171); ?>" data-floor="<?php echo htmlspecialchars($floor171); ?>" data-image=" <?php echo base64_encode($upload_img171); ?>" data-category="<?php echo htmlspecialchars($category171); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName171); ?>" data-status="<?php echo htmlspecialchars($status171); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status171); ?>; 
    position:absolute; top:80px; left:525px;'>
                        </div>

                        <!-- ASSET 172 -->
                        <img src='../image.php?id=172' style='width:17px; cursor:pointer; position:absolute; top:80px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal172' class="asset-image" onclick='fetchAssetData(172);' data-id="<?php echo $assetId172; ?>" data-room=" <?php echo htmlspecialchars($room172); ?>" data-floor="<?php echo htmlspecialchars($floor172); ?>" data-image=" <?php echo base64_encode($upload_img172); ?>" data-category="<?php echo htmlspecialchars($category172); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName172); ?>" data-status="<?php echo htmlspecialchars($status172); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status172); ?>; 
    position:absolute; top:80px; left:545px;'>
                        </div>

                        <!-- ASSET 173 -->
                        <img src='../image.php?id=173' style='width:17px; cursor:pointer; position:absolute; top:100px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal173' class="asset-image" onclick='fetchAssetData(173);' data-id="<?php echo $assetId173; ?>" data-room=" <?php echo htmlspecialchars($room173); ?>" data-floor="<?php echo htmlspecialchars($floor173); ?>" data-image=" <?php echo base64_encode($upload_img173); ?>" data-category="<?php echo htmlspecialchars($category173); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName173); ?>" data-status="<?php echo htmlspecialchars($status173); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status173); ?>; 
    position:absolute; top:100px; left:465px;'>
                        </div>

                        <!-- ASSET 174 -->
                        <img src='../image.php?id=174' style='width:17px; cursor:pointer; position:absolute; top:100px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal174' class="asset-image" onclick='fetchAssetData(174);' data-id="<?php echo $assetId174; ?>" data-room=" <?php echo htmlspecialchars($room174); ?>" data-floor="<?php echo htmlspecialchars($floor174); ?>" data-image=" <?php echo base64_encode($upload_img174); ?>" data-category="<?php echo htmlspecialchars($category174); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName174); ?>" data-status="<?php echo htmlspecialchars($status174); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status174); ?>; 
    position:absolute; top:100px; left:485px;'>
                        </div>

                        <!-- ASSET 175 -->
                        <img src='../image.php?id=175' style='width:17px; cursor:pointer; position:absolute; top:100px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal175' class="asset-image" onclick='fetchAssetData(175);' data-id="<?php echo $assetId175; ?>" data-room=" <?php echo htmlspecialchars($room175); ?>" data-floor="<?php echo htmlspecialchars($floor175); ?>" data-image=" <?php echo base64_encode($upload_img175); ?>" data-category="<?php echo htmlspecialchars($category175); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName175); ?>" data-status="<?php echo htmlspecialchars($status175); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status175); ?>; 
    position:absolute; top:100px; left:505px;'>
                        </div>


                        <!-- ASSET 176 -->
                        <img src='../image.php?id=176' style='width:17px; cursor:pointer; position:absolute; top:100px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal176' class="asset-image" onclick='fetchAssetData(176);' data-id="<?php echo $assetId176; ?>" data-room=" <?php echo htmlspecialchars($room176); ?>" data-floor="<?php echo htmlspecialchars($floor176); ?>" data-image=" <?php echo base64_encode($upload_img176); ?>" data-category="<?php echo htmlspecialchars($category176); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName176); ?>" data-status="<?php echo htmlspecialchars($status176); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status176); ?>; 
    position:absolute; top:100px; left:525px;'>
                        </div>

                        <!-- ASSET 177 -->
                        <img src='../image.php?id=177' style='width:17px; cursor:pointer; position:absolute; top:100px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal177' class="asset-image" onclick='fetchAssetData(177);' data-id="<?php echo $assetId177; ?>" data-room=" <?php echo htmlspecialchars($room177); ?>" data-floor="<?php echo htmlspecialchars($floor177); ?>" data-image=" <?php echo base64_encode($upload_img177); ?>" data-category="<?php echo htmlspecialchars($category177); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName177); ?>" data-status="<?php echo htmlspecialchars($status177); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status177); ?>; 
    position:absolute; top:100px; left:545px;'>
                        </div>

                        <!-- ASSET 178 -->
                        <img src='../image.php?id=178' style='width:17px; cursor:pointer; position:absolute; top:120px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal178' class="asset-image" onclick='fetchAssetData(178);' data-id="<?php echo $assetId178; ?>" data-room=" <?php echo htmlspecialchars($room178); ?>" data-floor="<?php echo htmlspecialchars($floor178); ?>" data-image=" <?php echo base64_encode($upload_img178); ?>" data-category="<?php echo htmlspecialchars($category178); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName178); ?>" data-status="<?php echo htmlspecialchars($status178); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status178); ?>; 
    position:absolute; top:120px; left:465px;'>
                        </div>

                        <!-- ASSET 179 -->
                        <img src='../image.php?id=179' style='width:17px; cursor:pointer; position:absolute; top:120px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal179' class="asset-image" onclick='fetchAssetData(179);' data-id="<?php echo $assetId179; ?>" data-room=" <?php echo htmlspecialchars($room179); ?>" data-floor="<?php echo htmlspecialchars($floor179); ?>" data-image=" <?php echo base64_encode($upload_img179); ?>" data-category="<?php echo htmlspecialchars($category179); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName179); ?>" data-status="<?php echo htmlspecialchars($status179); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status179); ?>; 
    position:absolute; top:120px; left:485px;'>
                        </div>


                        <!-- ASSET 180 -->
                        <img src='../image.php?id=180' style='width:17px; cursor:pointer; position:absolute; top:120px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal180' class="asset-image" onclick='fetchAssetData(180);' data-id="<?php echo $assetId180; ?>" data-room=" <?php echo htmlspecialchars($room180); ?>" data-floor="<?php echo htmlspecialchars($floor180); ?>" data-image=" <?php echo base64_encode($upload_img180); ?>" data-category="<?php echo htmlspecialchars($category180); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName180); ?>" data-status="<?php echo htmlspecialchars($status180); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status180); ?>; 
    position:absolute; top:120px; left:505px;'>
                        </div>

                        <!-- ASSET 181 -->
                        <img src='../image.php?id=181' style='width:17px; cursor:pointer; position:absolute; top:120px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal181' class="asset-image" onclick='fetchAssetData(181);' data-id="<?php echo $assetId181; ?>" data-room=" <?php echo htmlspecialchars($room181); ?>" data-floor="<?php echo htmlspecialchars($floor181); ?>" data-image=" <?php echo base64_encode($upload_img181); ?>" data-category="<?php echo htmlspecialchars($category181); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName181); ?>" data-status="<?php echo htmlspecialchars($status181); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status181); ?>; 
    position:absolute; top:120px; left:525px;'>
                        </div>

                        <!-- ASSET 182 -->
                        <img src='../image.php?id=182' style='width:17px; cursor:pointer; position:absolute; top:120px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal182' class="asset-image" onclick='fetchAssetData(182);' data-id="<?php echo $assetId182; ?>" data-room=" <?php echo htmlspecialchars($room182); ?>" data-floor="<?php echo htmlspecialchars($floor182); ?>" data-image=" <?php echo base64_encode($upload_img182); ?>" data-category="<?php echo htmlspecialchars($category182); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName182); ?>" data-status="<?php echo htmlspecialchars($status182); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status182); ?>; 
    position:absolute; top:120px; left:545px;'>
                        </div>

                        <!-- ASSET 183 -->
                        <img src='../image.php?id=183' style='width:17px; cursor:pointer; position:absolute; top:140px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal183' class="asset-image" onclick='fetchAssetData(183);' data-id="<?php echo $assetId183; ?>" data-room=" <?php echo htmlspecialchars($room183); ?>" data-floor="<?php echo htmlspecialchars($floor183); ?>" data-image=" <?php echo base64_encode($upload_img183); ?>" data-category="<?php echo htmlspecialchars($category183); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName183); ?>" data-status="<?php echo htmlspecialchars($status183); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status183); ?>; 
    position:absolute; top:140px; left:465px;'>
                        </div>


                        <!-- ASSET 184 -->
                        <img src='../image.php?id=184' style='width:17px; cursor:pointer; position:absolute; top:140px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal184' class="asset-image" onclick='fetchAssetData(184);' data-id="<?php echo $assetId184; ?>" data-room=" <?php echo htmlspecialchars($room184); ?>" data-floor="<?php echo htmlspecialchars($floor184); ?>" data-image=" <?php echo base64_encode($upload_img184); ?>" data-category="<?php echo htmlspecialchars($category184); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName184); ?>" data-status="<?php echo htmlspecialchars($status184); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status184); ?>; 
    position:absolute; top:140px; left:485px;'>
                        </div>

                        <!-- ASSET 185 -->
                        <img src='../image.php?id=185' style='width:17px; cursor:pointer; position:absolute; top:140px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal185' class="asset-image" onclick='fetchAssetData(185);' data-id="<?php echo $assetId185; ?>" data-room=" <?php echo htmlspecialchars($room185); ?>" data-floor="<?php echo htmlspecialchars($floor185); ?>" data-image=" <?php echo base64_encode($upload_img185); ?>" data-category="<?php echo htmlspecialchars($category185); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName185); ?>" data-status="<?php echo htmlspecialchars($status185); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status185); ?>; 
    position:absolute; top:140px; left:505px;'>
                        </div>

                        <!-- ASSET 186 -->
                        <img src='../image.php?id=186' style='width:17px; cursor:pointer; position:absolute; top:140px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal186' class="asset-image" onclick='fetchAssetData(186);' data-id="<?php echo $assetId186; ?>" data-room=" <?php echo htmlspecialchars($room186); ?>" data-floor="<?php echo htmlspecialchars($floor186); ?>" data-image=" <?php echo base64_encode($upload_img186); ?>" data-category="<?php echo htmlspecialchars($category186); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName186); ?>" data-status="<?php echo htmlspecialchars($status186); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status186); ?>; 
    position:absolute; top:140px; left:525px;'>
                        </div>

                        <!-- ASSET 187 -->
                        <img src='../image.php?id=187' style='width:17px; cursor:pointer; position:absolute; top:140px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal187' class="asset-image" onclick='fetchAssetData(187);' data-id="<?php echo $assetId187; ?>" data-room=" <?php echo htmlspecialchars($room187); ?>" data-floor="<?php echo htmlspecialchars($floor187); ?>" data-image=" <?php echo base64_encode($upload_img187); ?>" data-category="<?php echo htmlspecialchars($category187); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName187); ?>" data-status="<?php echo htmlspecialchars($status187); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status187); ?>; 
    position:absolute; top:140px; left:545px;'>
                        </div>


                        <!-- END OF ROW 4 -->

                        <!-- ASSET 188 -->
                        <img src='../image.php?id=188' style='width:17px; cursor:pointer; position:absolute; top:160px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal188' class="asset-image" onclick='fetchAssetData(188);' data-id="<?php echo $assetId188; ?>" data-room=" <?php echo htmlspecialchars($room188); ?>" data-floor="<?php echo htmlspecialchars($floor188); ?>" data-image=" <?php echo base64_encode($upload_img188); ?>" data-category="<?php echo htmlspecialchars($category188); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName188); ?>" data-status="<?php echo htmlspecialchars($status188); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status188); ?>; 
    position:absolute; top:160px; left:465px;'>
                        </div>

                        <!-- ASSET 189 -->
                        <img src='../image.php?id=189' style='width:17px; cursor:pointer; position:absolute; top:160px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal189' class="asset-image" onclick='fetchAssetData(189);' data-id="<?php echo $assetId189; ?>" data-room=" <?php echo htmlspecialchars($room189); ?>" data-floor="<?php echo htmlspecialchars($floor189); ?>" data-image=" <?php echo base64_encode($upload_img189); ?>" data-category="<?php echo htmlspecialchars($category189); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName189); ?>" data-status="<?php echo htmlspecialchars($status189); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status189); ?>; 
    position:absolute; top:160px; left:485px;'>
                        </div>

                        <!-- ASSET 190 -->
                        <img src='../image.php?id=190' style='width:17px; cursor:pointer; position:absolute; top:160px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal190' class="asset-image" onclick='fetchAssetData(190);' data-id="<?php echo $assetId190; ?>" data-room=" <?php echo htmlspecialchars($room190); ?>" data-floor="<?php echo htmlspecialchars($floor190); ?>" data-image=" <?php echo base64_encode($upload_img190); ?>" data-category="<?php echo htmlspecialchars($category190); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName190); ?>" data-status="<?php echo htmlspecialchars($status190); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status190); ?>; 
    position:absolute; top:160px; left:505px;'>
                        </div>

                        <!-- ASSET 191 -->
                        <img src='../image.php?id=191' style='width:17px; cursor:pointer; position:absolute; top:160px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal191' class="asset-image" onclick='fetchAssetData(191);' data-id="<?php echo $assetId191; ?>" data-room=" <?php echo htmlspecialchars($room191); ?>" data-floor="<?php echo htmlspecialchars($floor191); ?>" data-image=" <?php echo base64_encode($upload_img191); ?>" data-category="<?php echo htmlspecialchars($category191); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName191); ?>" data-status="<?php echo htmlspecialchars($status191); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status191); ?>; 
    position:absolute; top:160px; left:525px;'>
                        </div>


                        <!-- ASSET 192 -->
                        <img src='../image.php?id=192' style='width:17px; cursor:pointer; position:absolute; top:160px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal192' class="asset-image" onclick='fetchAssetData(192);' data-id="<?php echo $assetId192; ?>" data-room=" <?php echo htmlspecialchars($room192); ?>" data-floor="<?php echo htmlspecialchars($floor192); ?>" data-image=" <?php echo base64_encode($upload_img192); ?>" data-category="<?php echo htmlspecialchars($category192); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName192); ?>" data-status="<?php echo htmlspecialchars($status192); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status192); ?>; 
    position:absolute; top:160px; left:545px;'>
                        </div>

                        <!-- ASSET 193 -->
                        <img src='../image.php?id=193' style='width:17px; cursor:pointer; position:absolute; top:210px; left:495px; transform: rotate(180deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal193' onclick='fetchAssetData(193);' data-id="<?php echo $assetId193; ?>" data-room="<?php echo htmlspecialchars($room193); ?>" data-floor="<?php echo htmlspecialchars($floor193); ?>" data-image="<?php echo base64_encode($upload_img193); ?>" data-category="<?php echo htmlspecialchars($category193); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName193); ?>" data-status="<?php echo htmlspecialchars($status193); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status193); ?>;
                    position:absolute; top:220px; left:495px;'>
                        </div>

                        <!-- END OF ROW 5 -->

                        <!-- END OF IB106A -->
                        <!-- START OF ROW IB107A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 194 -->
                        <img src='../image.php?id=194' style='width:17px; cursor:pointer; position:absolute; top:80px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal194' class="asset-image" onclick='fetchAssetData(194);' data-id="<?php echo $assetId194; ?>" data-room=" <?php echo htmlspecialchars($room194); ?>" data-floor="<?php echo htmlspecialchars($floor194); ?>" data-image=" <?php echo base64_encode($upload_img194); ?>" data-category="<?php echo htmlspecialchars($category194); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName194); ?>" data-status="<?php echo htmlspecialchars($status194); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status194); ?>; 
    position:absolute; top:80px; left:335px;'>
                        </div>

                        <!-- ASSET 195 -->
                        <img src='../image.php?id=195' style='width:17px; cursor:pointer; position:absolute; top:80px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal195' class="asset-image" onclick='fetchAssetData(195);' data-id="<?php echo $assetId195; ?>" data-room=" <?php echo htmlspecialchars($room195); ?>" data-floor="<?php echo htmlspecialchars($floor195); ?>" data-image=" <?php echo base64_encode($upload_img195); ?>" data-category="<?php echo htmlspecialchars($category195); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName195); ?>" data-status="<?php echo htmlspecialchars($status195); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status195); ?>; 
    position:absolute; top:80px; left:355px;'>
                        </div>

                        <!-- ASSET 196 -->
                        <img src='../image.php?id=196' style='width:17px; cursor:pointer; position:absolute; top:80px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal196' class="asset-image" onclick='fetchAssetData(196);' data-id="<?php echo $assetId196; ?>" data-room=" <?php echo htmlspecialchars($room196); ?>" data-floor="<?php echo htmlspecialchars($floor196); ?>" data-image=" <?php echo base64_encode($upload_img196); ?>" data-category="<?php echo htmlspecialchars($category196); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName196); ?>" data-status="<?php echo htmlspecialchars($status196); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status196); ?>; 
    position:absolute; top:80px; left:375px;'>
                        </div>

                        <!-- ASSET 197 -->
                        <img src='../image.php?id=197' style='width:17px; cursor:pointer; position:absolute; top:80px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal197' class="asset-image" onclick='fetchAssetData(197);' data-id="<?php echo $assetId197; ?>" data-room=" <?php echo htmlspecialchars($room197); ?>" data-floor="<?php echo htmlspecialchars($floor197); ?>" data-image=" <?php echo base64_encode($upload_img197); ?>" data-category="<?php echo htmlspecialchars($category197); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName197); ?>" data-status="<?php echo htmlspecialchars($status197); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status197); ?>; 
    position:absolute; top:80px; left:395px;'>
                        </div>

                        <!-- ASSET 198 -->
                        <img src='../image.php?id=198' style='width:17px; cursor:pointer; position:absolute; top:80px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal198' class="asset-image" onclick='fetchAssetData(198);' data-id="<?php echo $assetId198; ?>" data-room=" <?php echo htmlspecialchars($room198); ?>" data-floor="<?php echo htmlspecialchars($floor198); ?>" data-image=" <?php echo base64_encode($upload_img198); ?>" data-category="<?php echo htmlspecialchars($category198); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName198); ?>" data-status="<?php echo htmlspecialchars($status198); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status198); ?>; 
    position:absolute; top:80px; left:415px;'>
                        </div>

                        <!-- END OF ROW 1 -->

                        <!-- ASSET 199 -->
                        <img src='../image.php?id=199' style='width:17px; cursor:pointer; position:absolute; top:100px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal199' class="asset-image" onclick='fetchAssetData(199);' data-id="<?php echo $assetId199; ?>" data-room=" <?php echo htmlspecialchars($room199); ?>" data-floor="<?php echo htmlspecialchars($floor199); ?>" data-image=" <?php echo base64_encode($upload_img199); ?>" data-category="<?php echo htmlspecialchars($category199); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName199); ?>" data-status="<?php echo htmlspecialchars($status199); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status199); ?>; 
    position:absolute; top:100px; left:335px;'>
                        </div>

                        <!-- ASSET 200 -->
                        <img src='../image.php?id=200' style='width:17px; cursor:pointer; position:absolute; top:100px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal200' class="asset-image" onclick='fetchAssetData(200);' data-id="<?php echo $assetId200; ?>" data-room=" <?php echo htmlspecialchars($room200); ?>" data-floor="<?php echo htmlspecialchars($floor200); ?>" data-image=" <?php echo base64_encode($upload_img200); ?>" data-category="<?php echo htmlspecialchars($category200); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName200); ?>" data-status="<?php echo htmlspecialchars($status200); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status200); ?>; 
    position:absolute; top:100px; left:355px;'>
                        </div>

                        <!-- ASSET 201 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:100px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal201' class="asset-image" onclick='fetchAssetData(201);' data-id="<?php echo $assetId201; ?>" data-room=" <?php echo htmlspecialchars($room201); ?>" data-floor="<?php echo htmlspecialchars($floor201); ?>" data-image=" <?php echo base64_encode($upload_img201); ?>" data-category="<?php echo htmlspecialchars($category201); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName201); ?>" data-status="<?php echo htmlspecialchars($status201); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status201); ?>; 
    position:absolute; top:100px; left:375px;'>
                        </div>


                        <!-- ASSET 7276 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:100px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7276' class="asset-image" onclick='fetchAssetData(7276);' data-id="<?php echo $assetId7276; ?>" data-room=" <?php echo htmlspecialchars($room7276); ?>" data-floor="<?php echo htmlspecialchars($floor7276); ?>" data-image=" <?php echo base64_encode($upload_img7276); ?>" data-category="<?php echo htmlspecialchars($category7276); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7276); ?>" data-status="<?php echo htmlspecialchars($status7276); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7276); ?>; 
    position:absolute; top:100px; left:395px;'>
                        </div>

                        <!-- ASSET 7277 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:100px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7277' class="asset-image" onclick='fetchAssetData(7277);' data-id="<?php echo $assetId7277; ?>" data-room=" <?php echo htmlspecialchars($room7277); ?>" data-floor="<?php echo htmlspecialchars($floor7277); ?>" data-image=" <?php echo base64_encode($upload_img7277); ?>" data-category="<?php echo htmlspecialchars($category7277); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7277); ?>" data-status="<?php echo htmlspecialchars($status7277); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7277); ?>; 
    position:absolute; top:100px; left:415px;'>
                        </div>

                        <!-- END OF ROW 2 -->

                        <!-- ASSET 7278 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7278' class="asset-image" onclick='fetchAssetData(7278);' data-id="<?php echo $assetId7278; ?>" data-room=" <?php echo htmlspecialchars($room7278); ?>" data-floor="<?php echo htmlspecialchars($floor7278); ?>" data-image=" <?php echo base64_encode($upload_img7278); ?>" data-category="<?php echo htmlspecialchars($category7278); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7278); ?>" data-status="<?php echo htmlspecialchars($status7278); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7278); ?>; 
    position:absolute; top:120px; left:335px;'>
                        </div>

                        <!-- ASSET 7279 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7279' class="asset-image" onclick='fetchAssetData(7279);' data-id="<?php echo $assetId7279; ?>" data-room=" <?php echo htmlspecialchars($room7279); ?>" data-floor="<?php echo htmlspecialchars($floor7279); ?>" data-image=" <?php echo base64_encode($upload_img7279); ?>" data-category="<?php echo htmlspecialchars($category7279); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7279); ?>" data-status="<?php echo htmlspecialchars($status7279); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7279); ?>; 
    position:absolute; top:120px; left:365px;'>
                        </div>


                        <!-- ASSET 7280 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7280' class="asset-image" onclick='fetchAssetData(7280);' data-id="<?php echo $assetId7280; ?>" data-room=" <?php echo htmlspecialchars($room7280); ?>" data-floor="<?php echo htmlspecialchars($floor7280); ?>" data-image=" <?php echo base64_encode($upload_img7280); ?>" data-category="<?php echo htmlspecialchars($category7280); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7280); ?>" data-status="<?php echo htmlspecialchars($status7280); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7280); ?>; 
    position:absolute; top:120px; left:385px;'>
                        </div>

                        <!-- ASSET 7281 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7281' class="asset-image" onclick='fetchAssetData(7281);' data-id="<?php echo $assetId7281; ?>" data-room=" <?php echo htmlspecialchars($room7281); ?>" data-floor="<?php echo htmlspecialchars($floor7281); ?>" data-image=" <?php echo base64_encode($upload_img7281); ?>" data-category="<?php echo htmlspecialchars($category7281); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7281); ?>" data-status="<?php echo htmlspecialchars($status7281); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7281); ?>; 
    position:absolute; top:120px; left:395px;'>
                        </div>

                        <!-- ASSET 7282 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7282' class="asset-image" onclick='fetchAssetData(7282);' data-id="<?php echo $assetId7282; ?>" data-room=" <?php echo htmlspecialchars($room7282); ?>" data-floor="<?php echo htmlspecialchars($floor7282); ?>" data-image=" <?php echo base64_encode($upload_img7282); ?>" data-category="<?php echo htmlspecialchars($category7282); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7282); ?>" data-status="<?php echo htmlspecialchars($status7282); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7282); ?>; 
    position:absolute; top:120px; left:415px;'>
                        </div>





                        <!-- START OF IB108A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 202 -->
                        <img src='../image.php?id=202' style='width:17px; cursor:pointer; position:absolute; top:80px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal202' class="asset-image" onclick='fetchAssetData(202);' data-id="<?php echo $assetId202; ?>" data-room=" <?php echo htmlspecialchars($room202); ?>" data-floor="<?php echo htmlspecialchars($floor202); ?>" data-image=" <?php echo base64_encode($upload_img202); ?>" data-category="<?php echo htmlspecialchars($category202); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName202); ?>" data-status="<?php echo htmlspecialchars($status202); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status202); ?>; 
    position:absolute; top:80px; left:205px;'>
                        </div>

                        <!-- ASSET 203 -->
                        <img src='../image.php?id=203' style='width:17px; cursor:pointer; position:absolute; top:80px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal203' class="asset-image" onclick='fetchAssetData(203);' data-id="<?php echo $assetId203; ?>" data-room=" <?php echo htmlspecialchars($room203); ?>" data-floor="<?php echo htmlspecialchars($floor203); ?>" data-image=" <?php echo base64_encode($upload_img203); ?>" data-category="<?php echo htmlspecialchars($category203); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName203); ?>" data-status="<?php echo htmlspecialchars($status203); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status203); ?>; 
    position:absolute; top:80px; left:225px;'>
                        </div>

                        <!-- ASSET 204 -->
                        <img src='../image.php?id=204' style='width:17px; cursor:pointer; position:absolute; top:80px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal204' class="asset-image" onclick='fetchAssetData(204);' data-id="<?php echo $assetId204; ?>" data-room=" <?php echo htmlspecialchars($room204); ?>" data-floor="<?php echo htmlspecialchars($floor204); ?>" data-image=" <?php echo base64_encode($upload_img204); ?>" data-category="<?php echo htmlspecialchars($category204); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName204); ?>" data-status="<?php echo htmlspecialchars($status204); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status204); ?>; 
    position:absolute; top:80px; left:245px;'>
                        </div>

                        <!-- ASSET 205 -->
                        <img src='../image.php?id=205' style='width:17px; cursor:pointer; position:absolute; top:80px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal205' class="asset-image" onclick='fetchAssetData(205);' data-id="<?php echo $assetId205; ?>" data-room=" <?php echo htmlspecialchars($room205); ?>" data-floor="<?php echo htmlspecialchars($floor205); ?>" data-image=" <?php echo base64_encode($upload_img205); ?>" data-category="<?php echo htmlspecialchars($category205); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName205); ?>" data-status="<?php echo htmlspecialchars($status205); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status205); ?>; 
    position:absolute; top:80px; left:265px;'>
                        </div>


                        <!-- ASSET 206 -->
                        <img src='../image.php?id=206' style='width:17px; cursor:pointer; position:absolute; top:80px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal206' class="asset-image" onclick='fetchAssetData(206);' data-id="<?php echo $assetId206; ?>" data-room="<?php echo htmlspecialchars($room206); ?>" data-floor="<?php echo htmlspecialchars($floor206); ?>" data-image="<?php echo base64_encode($upload_img206); ?>" data-category="<?php echo htmlspecialchars($category206); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName206); ?>" data-status="<?php echo htmlspecialchars($status206); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status206); ?>; 
position:absolute; top:80px; left:285px;'>
                        </div>

                        <!-- ASSET 207 -->
                        <img src='../image.php?id=207' style='width:17px; cursor:pointer; position:absolute; top:100px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal207' class="asset-image" onclick='fetchAssetData(207);' data-id="<?php echo $assetId207; ?>" data-room="<?php echo htmlspecialchars($room207); ?>" data-floor="<?php echo htmlspecialchars($floor207); ?>" data-image="<?php echo base64_encode($upload_img207); ?>" data-category="<?php echo htmlspecialchars($category207); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName207); ?>" data-status="<?php echo htmlspecialchars($status207); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status207); ?>; 
position:absolute; top:100px; left:205px;'>
                        </div>

                        <!-- ASSET 208 -->
                        <img src='../image.php?id=208' style='width:17px; cursor:pointer; position:absolute; top:100px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal208' class="asset-image" onclick='fetchAssetData(208);' data-id="<?php echo $assetId208; ?>" data-room="<?php echo htmlspecialchars($room208); ?>" data-floor="<?php echo htmlspecialchars($floor208); ?>" data-image="<?php echo base64_encode($upload_img208); ?>" data-category="<?php echo htmlspecialchars($category208); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName208); ?>" data-status="<?php echo htmlspecialchars($status208); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status208); ?>; 
position:absolute; top:100px; left:225px;'>
                        </div>

                        <!-- ASSET 209 -->
                        <img src='../image.php?id=209' style='width:17px; cursor:pointer; position:absolute; top:100px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal209' class="asset-image" onclick='fetchAssetData(209);' data-id="<?php echo $assetId209; ?>" data-room="<?php echo htmlspecialchars($room209); ?>" data-floor="<?php echo htmlspecialchars($floor209); ?>" data-image="<?php echo base64_encode($upload_img209); ?>" data-category="<?php echo htmlspecialchars($category209); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName209); ?>" data-status="<?php echo htmlspecialchars($status209); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status209); ?>; 
position:absolute; top:100px; left:245px;'>
                        </div>


                        <!-- ASSET 210 -->
                        <img src='../image.php?id=210' style='width:17px; cursor:pointer; position:absolute; top:100px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal210' class="asset-image" onclick='fetchAssetData(210);' data-id="<?php echo $assetId210; ?>" data-room="<?php echo htmlspecialchars($room210); ?>" data-floor="<?php echo htmlspecialchars($floor210); ?>" data-image="<?php echo base64_encode($upload_img210); ?>" data-category="<?php echo htmlspecialchars($category210); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName210); ?>" data-status="<?php echo htmlspecialchars($status210); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status210); ?>; 
position:absolute; top:100px; left:265px;'>
                        </div>

                        <!-- ASSET 211 -->
                        <img src='../image.php?id=211' style='width:17px; cursor:pointer; position:absolute; top:100px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal211' class="asset-image" onclick='fetchAssetData(211);' data-id="<?php echo $assetId211; ?>" data-room="<?php echo htmlspecialchars($room211); ?>" data-floor="<?php echo htmlspecialchars($floor211); ?>" data-image="<?php echo base64_encode($upload_img211); ?>" data-category="<?php echo htmlspecialchars($category211); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName211); ?>" data-status="<?php echo htmlspecialchars($status211); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status211); ?>; 
position:absolute; top:100px; left:285px;'>
                        </div>

                        <!-- END OF ROW 2 -->

                        <!-- ASSET 212 -->
                        <img src='../image.php?id=212' style='width:17px; cursor:pointer; position:absolute; top:120px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal212' class="asset-image" onclick='fetchAssetData(212);' data-id="<?php echo $assetId212; ?>" data-room="<?php echo htmlspecialchars($room212); ?>" data-floor="<?php echo htmlspecialchars($floor212); ?>" data-image="<?php echo base64_encode($upload_img212); ?>" data-category="<?php echo htmlspecialchars($category212); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName212); ?>" data-status="<?php echo htmlspecialchars($status212); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status212); ?>; 
position:absolute; top:120px; left:205px;'>
                        </div>

                        <!-- ASSET 213 -->
                        <img src='../image.php?id=213' style='width:17px; cursor:pointer; position:absolute; top:120px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal213' class="asset-image" onclick='fetchAssetData(213);' data-id="<?php echo $assetId213; ?>" data-room="<?php echo htmlspecialchars($room213); ?>" data-floor="<?php echo htmlspecialchars($floor213); ?>" data-image="<?php echo base64_encode($upload_img213); ?>" data-category="<?php echo htmlspecialchars($category213); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName213); ?>" data-status="<?php echo htmlspecialchars($status213); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status213); ?>; 
position:absolute; top:120px; left:225px;'>
                        </div>


                        <!-- ASSET 214 -->
                        <img src='../image.php?id=214' style='width:17px; cursor:pointer; position:absolute; top:120px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal214' class="asset-image" onclick='fetchAssetData(214);' data-id="<?php echo $assetId214; ?>" data-room="<?php echo htmlspecialchars($room214); ?>" data-floor="<?php echo htmlspecialchars($floor214); ?>" data-image="<?php echo base64_encode($upload_img214); ?>" data-category="<?php echo htmlspecialchars($category214); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName214); ?>" data-status="<?php echo htmlspecialchars($status214); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status214); ?>; 
position:absolute; top:120px; left:245px;'>
                        </div>

                        <!-- ASSET 215 -->
                        <img src='../image.php?id=215' style='width:17px; cursor:pointer; position:absolute; top:120px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal215' class="asset-image" onclick='fetchAssetData(215);' data-id="<?php echo $assetId215; ?>" data-room="<?php echo htmlspecialchars($room215); ?>" data-floor="<?php echo htmlspecialchars($floor215); ?>" data-image="<?php echo base64_encode($upload_img215); ?>" data-category="<?php echo htmlspecialchars($category215); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName215); ?>" data-status="<?php echo htmlspecialchars($status215); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status215); ?>; 
position:absolute; top:120px; left:265px;'>
                        </div>

                        <!-- ASSET 216 -->
                        <img src='../image.php?id=216' style='width:17px; cursor:pointer; position:absolute; top:120px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal216' class="asset-image" onclick='fetchAssetData(216);' data-id="<?php echo $assetId216; ?>" data-room="<?php echo htmlspecialchars($room216); ?>" data-floor="<?php echo htmlspecialchars($floor216); ?>" data-image="<?php echo base64_encode($upload_img216); ?>" data-category="<?php echo htmlspecialchars($category216); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName216); ?>" data-status="<?php echo htmlspecialchars($status216); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status216); ?>; 
position:absolute; top:120px; left:285px;'>
                        </div>

                        <!-- END OF ROW 3 -->

                        <!-- ASSET 217 -->
                        <img src='../image.php?id=217' style='width:17px; cursor:pointer; position:absolute; top:140px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal217' class="asset-image" onclick='fetchAssetData(217);' data-id="<?php echo $assetId217; ?>" data-room="<?php echo htmlspecialchars($room217); ?>" data-floor="<?php echo htmlspecialchars($floor217); ?>" data-image="<?php echo base64_encode($upload_img217); ?>" data-category="<?php echo htmlspecialchars($category217); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName217); ?>" data-status="<?php echo htmlspecialchars($status217); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status217); ?>; 
position:absolute; top:140px; left:205px;'>
                        </div>


                        <!-- ASSET 218 -->
                        <img src='../image.php?id=218' style='width:17px; cursor:pointer; position:absolute; top:140px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal218' class="asset-image" onclick='fetchAssetData(218);' data-id="<?php echo $assetId218; ?>" data-room="<?php echo htmlspecialchars($room218); ?>" data-floor="<?php echo htmlspecialchars($floor218); ?>" data-image="<?php echo base64_encode($upload_img218); ?>" data-category="<?php echo htmlspecialchars($category218); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName218); ?>" data-status="<?php echo htmlspecialchars($status218); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status218); ?>; 
position:absolute; top:140px; left:225px;'>
                        </div>

                        <!-- ASSET 219 -->
                        <img src='../image.php?id=219' style='width:17px; cursor:pointer; position:absolute; top:140px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal219' class="asset-image" onclick='fetchAssetData(219);' data-id="<?php echo $assetId219; ?>" data-room="<?php echo htmlspecialchars($room219); ?>" data-floor="<?php echo htmlspecialchars($floor219); ?>" data-image="<?php echo base64_encode($upload_img219); ?>" data-category="<?php echo htmlspecialchars($category219); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName219); ?>" data-status="<?php echo htmlspecialchars($status219); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status219); ?>; 
position:absolute; top:140px; left:245px;'>
                        </div>

                        <!-- ASSET 220 -->
                        <img src='../image.php?id=220' style='width:17px; cursor:pointer; position:absolute; top:140px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal220' class="asset-image" onclick='fetchAssetData(220);' data-id="<?php echo $assetId220; ?>" data-room="<?php echo htmlspecialchars($room220); ?>" data-floor="<?php echo htmlspecialchars($floor220); ?>" data-image="<?php echo base64_encode($upload_img220); ?>" data-category="<?php echo htmlspecialchars($category220); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName220); ?>" data-status="<?php echo htmlspecialchars($status220); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status220); ?>; 
position:absolute; top:140px; left:265px;'>
                        </div>

                        <!-- ASSET 221 -->
                        <img src='../image.php?id=221' style='width:17px; cursor:pointer; position:absolute; top:140px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal221' class="asset-image" onclick='fetchAssetData(221);' data-id="<?php echo $assetId221; ?>" data-room="<?php echo htmlspecialchars($room221); ?>" data-floor="<?php echo htmlspecialchars($floor221); ?>" data-image="<?php echo base64_encode($upload_img221); ?>" data-category="<?php echo htmlspecialchars($category221); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName221); ?>" data-status="<?php echo htmlspecialchars($status221); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status221); ?>; 
position:absolute; top:140px; left:285px;'>
                        </div>

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 222 -->
                        <img src='../image.php?id=222' style='width:17px; cursor:pointer; position:absolute; top:160px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal222' class="asset-image" onclick='fetchAssetData(222);' data-id="<?php echo $assetId222; ?>" data-room="<?php echo htmlspecialchars($room222); ?>" data-floor="<?php echo htmlspecialchars($floor222); ?>" data-image="<?php echo base64_encode($upload_img222); ?>" data-category="<?php echo htmlspecialchars($category222); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName222); ?>" data-status="<?php echo htmlspecialchars($status222); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status222); ?>; 
position:absolute; top:160px; left:205px;'>
                        </div>

                        <!-- ASSET 223 -->
                        <img src='../image.php?id=223' style='width:17px; cursor:pointer; position:absolute; top:160px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal223' class="asset-image" onclick='fetchAssetData(223);' data-id="<?php echo $assetId223; ?>" data-room="<?php echo htmlspecialchars($room223); ?>" data-floor="<?php echo htmlspecialchars($floor223); ?>" data-image="<?php echo base64_encode($upload_img223); ?>" data-category="<?php echo htmlspecialchars($category223); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName223); ?>" data-status="<?php echo htmlspecialchars($status223); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status223); ?>; 
position:absolute; top:160px; left:225px;'>
                        </div>

                        <!-- ASSET 224 -->
                        <img src='../image.php?id=224' style='width:17px; cursor:pointer; position:absolute; top:160px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal224' class="asset-image" onclick='fetchAssetData(224);' data-id="<?php echo $assetId224; ?>" data-room="<?php echo htmlspecialchars($room224); ?>" data-floor="<?php echo htmlspecialchars($floor224); ?>" data-image="<?php echo base64_encode($upload_img224); ?>" data-category="<?php echo htmlspecialchars($category224); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName224); ?>" data-status="<?php echo htmlspecialchars($status224); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status224); ?>; 
position:absolute; top:160px; left:245px;'>
                        </div>

                        <!-- ASSET 225 -->
                        <img src='../image.php?id=225' style='width:17px; cursor:pointer; position:absolute; top:160px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal225' class="asset-image" onclick='fetchAssetData(225);' data-id="<?php echo $assetId225; ?>" data-room="<?php echo htmlspecialchars($room225); ?>" data-floor="<?php echo htmlspecialchars($floor225); ?>" data-image="<?php echo base64_encode($upload_img225); ?>" data-category="<?php echo htmlspecialchars($category225); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName225); ?>" data-status="<?php echo htmlspecialchars($status225); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status225); ?>; 
position:absolute; top:160px; left:265px;'>
                        </div>


                        <!-- ASSET 226 -->
                        <img src='../image.php?id=226' style='width:17px; cursor:pointer; position:absolute; top:160px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal226' class="asset-image" onclick='fetchAssetData(226);' data-id="<?php echo $assetId226; ?>" data-room="<?php echo htmlspecialchars($room226); ?>" data-floor="<?php echo htmlspecialchars($floor226); ?>" data-image="<?php echo base64_encode($upload_img226); ?>" data-category="<?php echo htmlspecialchars($category226); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName226); ?>" data-status="<?php echo htmlspecialchars($status226); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status226); ?>; 
position:absolute; top:160px; left:285px;'>
                        </div>

                        <!-- END OF ROW 5 -->

                        <!-- ASSET 227 -->
                        <img src='../image.php?id=227' style='width:17px; cursor:pointer; position:absolute; top:210px; left:235px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal227' class="asset-image" onclick='fetchAssetData(227);' data-id="<?php echo $assetId227; ?>" data-room="<?php echo htmlspecialchars($room227); ?>" data-floor="<?php echo htmlspecialchars($floor227); ?>" data-image="<?php echo base64_encode($upload_img227); ?>" data-category="<?php echo htmlspecialchars($category227); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName227); ?>" data-status="<?php echo htmlspecialchars($status227); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status227); ?>; 
position:absolute; top:220px; left:235px;'>
                        </div>

                        <!-- END OF IB108A -->

                        <!-- START OF IB109A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 228 -->
                        <img src='../image.php?id=228' style='width:17px; cursor:pointer; position:absolute; top:100px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal228' class="asset-image" onclick='fetchAssetData(228);' data-id="<?php echo $assetId228; ?>" data-room="<?php echo htmlspecialchars($room228); ?>" data-floor="<?php echo htmlspecialchars($floor228); ?>" data-image="<?php echo base64_encode($upload_img228); ?>" data-category="<?php echo htmlspecialchars($category228); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName228); ?>" data-status="<?php echo htmlspecialchars($status228); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status228); ?>; 
position:absolute; top:100px; left:75px;'>
                        </div>

                        <!-- ASSET 229 -->
                        <img src='../image.php?id=229' style='width:17px; cursor:pointer; position:absolute; top:100px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal229' class="asset-image" onclick='fetchAssetData(229);' data-id="<?php echo $assetId229; ?>" data-room="<?php echo htmlspecialchars($room229); ?>" data-floor="<?php echo htmlspecialchars($floor229); ?>" data-image="<?php echo base64_encode($upload_img229); ?>" data-category="<?php echo htmlspecialchars($category229); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName229); ?>" data-status="<?php echo htmlspecialchars($status229); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status229); ?>; 
position:absolute; top:100px; left:95px;'>
                        </div>


                        <!-- ASSET 230 -->
                        <img src='../image.php?id=230' style='width:17px; cursor:pointer; position:absolute; top:100px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal230' class="asset-image" onclick='fetchAssetData(230);' data-id="<?php echo $assetId230; ?>" data-room="<?php echo htmlspecialchars($room230); ?>" data-floor="<?php echo htmlspecialchars($floor230); ?>" data-image="<?php echo base64_encode($upload_img230); ?>" data-category="<?php echo htmlspecialchars($category230); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName230); ?>" data-status="<?php echo htmlspecialchars($status230); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status230); ?>; 
position:absolute; top:100px; left:115px;'>
                        </div>

                        <!-- ASSET 231 -->
                        <img src='../image.php?id=231' style='width:17px; cursor:pointer; position:absolute; top:100px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal231' class="asset-image" onclick='fetchAssetData(231);' data-id="<?php echo $assetId231; ?>" data-room="<?php echo htmlspecialchars($room231); ?>" data-floor="<?php echo htmlspecialchars($floor231); ?>" data-image="<?php echo base64_encode($upload_img231); ?>" data-category="<?php echo htmlspecialchars($category231); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName231); ?>" data-status="<?php echo htmlspecialchars($status231); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status231); ?>; 
position:absolute; top:100px; left:135px;'>
                        </div>

                        <!-- ASSET 232 -->
                        <img src='../image.php?id=232' style='width:17px; cursor:pointer; position:absolute; top:100px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal232' class="asset-image" onclick='fetchAssetData(232);' data-id="<?php echo $assetId232; ?>" data-room="<?php echo htmlspecialchars($room232); ?>" data-floor="<?php echo htmlspecialchars($floor232); ?>" data-image="<?php echo base64_encode($upload_img232); ?>" data-category="<?php echo htmlspecialchars($category232); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName232); ?>" data-status="<?php echo htmlspecialchars($status232); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status232); ?>; 
position:absolute; top:100px; left:155px;'>
                        </div>

                        <!-- END OF ROW 1 -->

                        <!-- ASSET 233 -->
                        <img src='../image.php?id=233' style='width:17px; cursor:pointer; position:absolute; top:120px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal233' class="asset-image" onclick='fetchAssetData(233);' data-id="<?php echo $assetId233; ?>" data-room="<?php echo htmlspecialchars($room233); ?>" data-floor="<?php echo htmlspecialchars($floor233); ?>" data-image="<?php echo base64_encode($upload_img233); ?>" data-category="<?php echo htmlspecialchars($category233); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName233); ?>" data-status="<?php echo htmlspecialchars($status233); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status233); ?>; 
position:absolute; top:120px; left:75px;'>
                        </div>


                        <!-- ASSET 234 -->
                        <img src='../image.php?id=234' style='width:17px; cursor:pointer; position:absolute; top:120px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal234' class="asset-image" onclick='fetchAssetData(234);' data-id="<?php echo $assetId234; ?>" data-room="<?php echo htmlspecialchars($room234); ?>" data-floor="<?php echo htmlspecialchars($floor234); ?>" data-image="<?php echo base64_encode($upload_img234); ?>" data-category="<?php echo htmlspecialchars($category234); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName234); ?>" data-status="<?php echo htmlspecialchars($status234); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status234); ?>; 
position:absolute; top:120px; left:95px;'>
                        </div>

                        <!-- ASSET 235 -->
                        <img src='../image.php?id=235' style='width:17px; cursor:pointer; position:absolute; top:120px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal235' class="asset-image" onclick='fetchAssetData(235);' data-id="<?php echo $assetId235; ?>" data-room="<?php echo htmlspecialchars($room235); ?>" data-floor="<?php echo htmlspecialchars($floor235); ?>" data-image="<?php echo base64_encode($upload_img235); ?>" data-category="<?php echo htmlspecialchars($category235); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName235); ?>" data-status="<?php echo htmlspecialchars($status235); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status235); ?>; 
position:absolute; top:120px; left:115px;'>
                        </div>

                        <!-- ASSET 236 -->
                        <img src='../image.php?id=236' style='width:17px; cursor:pointer; position:absolute; top:120px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal236' class="asset-image" onclick='fetchAssetData(236);' data-id="<?php echo $assetId236; ?>" data-room="<?php echo htmlspecialchars($room236); ?>" data-floor="<?php echo htmlspecialchars($floor236); ?>" data-image="<?php echo base64_encode($upload_img236); ?>" data-category="<?php echo htmlspecialchars($category236); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName236); ?>" data-status="<?php echo htmlspecialchars($status236); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status236); ?>; 
position:absolute; top:120px; left:135px;'>
                        </div>

                        <!-- ASSET 237 -->
                        <img src='../image.php?id=237' style='width:17px; cursor:pointer; position:absolute; top:120px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal237' class="asset-image" onclick='fetchAssetData(237);' data-id="<?php echo $assetId237; ?>" data-room="<?php echo htmlspecialchars($room237); ?>" data-floor="<?php echo htmlspecialchars($floor237); ?>" data-image="<?php echo base64_encode($upload_img237); ?>" data-category="<?php echo htmlspecialchars($category237); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName237); ?>" data-status="<?php echo htmlspecialchars($status237); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status237); ?>; 
position:absolute; top:120px; left:155px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 238 -->
                        <img src='../image.php?id=238' style='width:17px; cursor:pointer; position:absolute; top:140px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal238' class="asset-image" onclick='fetchAssetData(238);' data-id="<?php echo $assetId238; ?>" data-room="<?php echo htmlspecialchars($room238); ?>" data-floor="<?php echo htmlspecialchars($floor238); ?>" data-image="<?php echo base64_encode($upload_img238); ?>" data-category="<?php echo htmlspecialchars($category238); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName238); ?>" data-status="<?php echo htmlspecialchars($status238); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status238); ?>; 
position:absolute; top:140px; left:75px;'>
                        </div>

                        <!-- ASSET 239 -->
                        <img src='../image.php?id=239' style='width:17px; cursor:pointer; position:absolute; top:140px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal239' class="asset-image" onclick='fetchAssetData(239);' data-id="<?php echo $assetId239; ?>" data-room="<?php echo htmlspecialchars($room239); ?>" data-floor="<?php echo htmlspecialchars($floor239); ?>" data-image="<?php echo base64_encode($upload_img239); ?>" data-category="<?php echo htmlspecialchars($category239); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName239); ?>" data-status="<?php echo htmlspecialchars($status239); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status239); ?>; 
position:absolute; top:140px; left:95px;'>
                        </div>

                        <!-- ASSET 240 -->
                        <img src='../image.php?id=240' style='width:17px; cursor:pointer; position:absolute; top:140px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal240' class="asset-image" onclick='fetchAssetData(240);' data-id="<?php echo $assetId240; ?>" data-room="<?php echo htmlspecialchars($room240); ?>" data-floor="<?php echo htmlspecialchars($floor240); ?>" data-image="<?php echo base64_encode($upload_img240); ?>" data-category="<?php echo htmlspecialchars($category240); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName240); ?>" data-status="<?php echo htmlspecialchars($status240); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status240); ?>; 
position:absolute; top:140px; left:115px;'>
                        </div>

                        <!-- ASSET 241 -->
                        <img src='../image.php?id=241' style='width:17px; cursor:pointer; position:absolute; top:140px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal241' class="asset-image" onclick='fetchAssetData(241);' data-id="<?php echo $assetId241; ?>" data-room="<?php echo htmlspecialchars($room241); ?>" data-floor="<?php echo htmlspecialchars($floor241); ?>" data-image="<?php echo base64_encode($upload_img241); ?>" data-category="<?php echo htmlspecialchars($category241); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName241); ?>" data-status="<?php echo htmlspecialchars($status241); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status241); ?>; 
position:absolute; top:140px; left:135px;'>
                        </div>


                        <!-- ASSET 242 -->
                        <img src='../image.php?id=242' style='width:17px; cursor:pointer; position:absolute; top:140px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal242' class="asset-image" onclick='fetchAssetData(242);' data-id="<?php echo $assetId242; ?>" data-room="<?php echo htmlspecialchars($room242); ?>" data-floor="<?php echo htmlspecialchars($floor242); ?>" data-image="<?php echo base64_encode($upload_img242); ?>" data-category="<?php echo htmlspecialchars($category242); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName242); ?>" data-status="<?php echo htmlspecialchars($status242); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status242); ?>; 
position:absolute; top:140px; left:155px;'>
                        </div>

                        <!-- ASSET 243 -->
                        <img src='../image.php?id=243' style='width:17px; cursor:pointer; position:absolute; top:160px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal243' class="asset-image" onclick='fetchAssetData(243);' data-id="<?php echo $assetId243; ?>" data-room="<?php echo htmlspecialchars($room243); ?>" data-floor="<?php echo htmlspecialchars($floor243); ?>" data-image="<?php echo base64_encode($upload_img243); ?>" data-category="<?php echo htmlspecialchars($category243); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName243); ?>" data-status="<?php echo htmlspecialchars($status243); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status243); ?>; 
position:absolute; top:160px; left:75px;'>
                        </div>

                        <!-- ASSET 244 -->
                        <img src='../image.php?id=244' style='width:17px; cursor:pointer; position:absolute; top:160px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal244' class="asset-image" onclick='fetchAssetData(244);' data-id="<?php echo $assetId244; ?>" data-room="<?php echo htmlspecialchars($room244); ?>" data-floor="<?php echo htmlspecialchars($floor244); ?>" data-image="<?php echo base64_encode($upload_img244); ?>" data-category="<?php echo htmlspecialchars($category244); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName244); ?>" data-status="<?php echo htmlspecialchars($status244); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status244); ?>; 
position:absolute; top:160px; left:95px;'>
                        </div>

                        <!-- ASSET 245 -->
                        <img src='../image.php?id=245' style='width:17px; cursor:pointer; position:absolute; top:160px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal245' class="asset-image" onclick='fetchAssetData(245);' data-id="<?php echo $assetId245; ?>" data-room="<?php echo htmlspecialchars($room245); ?>" data-floor="<?php echo htmlspecialchars($floor245); ?>" data-image="<?php echo base64_encode($upload_img245); ?>" data-category="<?php echo htmlspecialchars($category245); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName245); ?>" data-status="<?php echo htmlspecialchars($status245); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status245); ?>; 
position:absolute; top:160px; left:115px;'>
                        </div>


                        <!-- ASSET 246 -->
                        <img src='../image.php?id=246' style='width:17px; cursor:pointer; position:absolute; top:160px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal246' class="asset-image" onclick='fetchAssetData(246);' data-id="<?php echo $assetId246; ?>" data-room="<?php echo htmlspecialchars($room246); ?>" data-floor="<?php echo htmlspecialchars($floor246); ?>" data-image="<?php echo base64_encode($upload_img246); ?>" data-category="<?php echo htmlspecialchars($category246); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName246); ?>" data-status="<?php echo htmlspecialchars($status246); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status246); ?>; 
position:absolute; top:160px; left:135px;'>
                        </div>

                        <!-- ASSET 247 -->
                        <img src='../image.php?id=247' style='width:17px; cursor:pointer; position:absolute; top:160px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal247' class="asset-image" onclick='fetchAssetData(247);' data-id="<?php echo $assetId247; ?>" data-room="<?php echo htmlspecialchars($room247); ?>" data-floor="<?php echo htmlspecialchars($floor247); ?>" data-image="<?php echo base64_encode($upload_img247); ?>" data-category="<?php echo htmlspecialchars($category247); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName247); ?>" data-status="<?php echo htmlspecialchars($status247); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status247); ?>; 
position:absolute; top:160px; left:155px;'>
                        </div>

                        <!-- ASSET 248 -->
                        <img src='../image.php?id=248' style='width:17px; cursor:pointer; position:absolute; top:180px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal248' class="asset-image" onclick='fetchAssetData(248);' data-id="<?php echo $assetId248; ?>" data-room="<?php echo htmlspecialchars($room248); ?>" data-floor="<?php echo htmlspecialchars($floor248); ?>" data-image="<?php echo base64_encode($upload_img248); ?>" data-category="<?php echo htmlspecialchars($category248); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName248); ?>" data-status="<?php echo htmlspecialchars($status248); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status248); ?>; 
position:absolute; top:180px; left:75px;'>
                        </div>

                        <!-- ASSET 249 -->
                        <img src='../image.php?id=249' style='width:17px; cursor:pointer; position:absolute; top:180px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal249' class="asset-image" onclick='fetchAssetData(249);' data-id="<?php echo $assetId249; ?>" data-room="<?php echo htmlspecialchars($room249); ?>" data-floor="<?php echo htmlspecialchars($floor249); ?>" data-image="<?php echo base64_encode($upload_img249); ?>" data-category="<?php echo htmlspecialchars($category249); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName249); ?>" data-status="<?php echo htmlspecialchars($status249); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status249); ?>; 
position:absolute; top:180px; left:95px;'>
                        </div>


                        <!-- ASSET 250 -->
                        <img src='../image.php?id=250' style='width:17px; cursor:pointer; position:absolute; top:180px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal250' class="asset-image" onclick='fetchAssetData(250);' data-id="<?php echo $assetId250; ?>" data-room="<?php echo htmlspecialchars($room250); ?>" data-floor="<?php echo htmlspecialchars($floor250); ?>" data-image="<?php echo base64_encode($upload_img250); ?>" data-category="<?php echo htmlspecialchars($category250); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName250); ?>" data-status="<?php echo htmlspecialchars($status250); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status250); ?>; 
position:absolute; top:180px; left:115px;'>
                        </div>

                        <!-- ASSET 251 -->
                        <img src='../image.php?id=251' style='width:17px; cursor:pointer; position:absolute; top:180px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal251' class="asset-image" onclick='fetchAssetData(251);' data-id="<?php echo $assetId251; ?>" data-room="<?php echo htmlspecialchars($room251); ?>" data-floor="<?php echo htmlspecialchars($floor251); ?>" data-image="<?php echo base64_encode($upload_img251); ?>" data-category="<?php echo htmlspecialchars($category251); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName251); ?>" data-status="<?php echo htmlspecialchars($status251); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status251); ?>; 
position:absolute; top:180px; left:135px;'>
                        </div>

                        <!-- ASSET 252 -->
                        <img src='../image.php?id=252' style='width:17px; cursor:pointer; position:absolute; top:180px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal252' class="asset-image" onclick='fetchAssetData(252);' data-id="<?php echo $assetId252; ?>" data-room="<?php echo htmlspecialchars($room252); ?>" data-floor="<?php echo htmlspecialchars($floor252); ?>" data-image="<?php echo base64_encode($upload_img252); ?>" data-category="<?php echo htmlspecialchars($category252); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName252); ?>" data-status="<?php echo htmlspecialchars($status252); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status252); ?>; 
position:absolute; top:180px; left:155px;'>
                        </div>

                        <!-- ASSET 253 -->
                        <img src='../image.php?id=253' style='width:17px; cursor:pointer; position:absolute; top:210px; left:105px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal253' class="asset-image" onclick='fetchAssetData(253);' data-id="<?php echo $assetId253; ?>" data-room="<?php echo htmlspecialchars($room253); ?>" data-floor="<?php echo htmlspecialchars($floor253); ?>" data-image="<?php echo base64_encode($upload_img253); ?>" data-category="<?php echo htmlspecialchars($category253); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName253); ?>" data-status="<?php echo htmlspecialchars($status253); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status253); ?>; 
position:absolute; top:220px; left:105px;'>
                        </div>


                        <!-- END OF IB109A -->

                        <!-- START OF IB110A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 254 -->
                        <img src='../image.php?id=254' style='width:17px; cursor:pointer; position:absolute; top:405px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal254' class="asset-image" onclick='fetchAssetData(254);' data-id="<?php echo $assetId254; ?>" data-room="<?php echo htmlspecialchars($room254); ?>" data-floor="<?php echo htmlspecialchars($floor254); ?>" data-image="<?php echo base64_encode($upload_img254); ?>" data-category="<?php echo htmlspecialchars($category254); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName254); ?>" data-status="<?php echo htmlspecialchars($status254); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status254); ?>; 
            position:absolute; top:405px; left:65px;'>
                        </div>

                        <!-- ASSET 255 -->
                        <img src='../image.php?id=255' style='width:17px; cursor:pointer; position:absolute; top:425px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal255' class="asset-image" onclick='fetchAssetData(255);' data-id="<?php echo $assetId255; ?>" data-room="<?php echo htmlspecialchars($room255); ?>" data-floor="<?php echo htmlspecialchars($floor255); ?>" data-image="<?php echo base64_encode($upload_img255); ?>" data-category="<?php echo htmlspecialchars($category255); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName255); ?>" data-status="<?php echo htmlspecialchars($status255); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status255); ?>; 
            position:absolute; top:425px; left:65px;'>
                        </div>

                        <!-- ASSET 256 -->
                        <img src='../image.php?id=256' style='width:17px; cursor:pointer; position:absolute; top:445px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal256' class="asset-image" onclick='fetchAssetData(256);' data-id="<?php echo $assetId256; ?>" data-room="<?php echo htmlspecialchars($room256); ?>" data-floor="<?php echo htmlspecialchars($floor256); ?>" data-image="<?php echo base64_encode($upload_img256); ?>" data-category="<?php echo htmlspecialchars($category256); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName256); ?>" data-status="<?php echo htmlspecialchars($status256); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status256); ?>; 
            position:absolute; top:445px; left:65px;'>
                        </div>

                        <!-- ASSET 257 -->
                        <img src='../image.php?id=257' style='width:17px; cursor:pointer; position:absolute; top:465px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal257' class="asset-image" onclick='fetchAssetData(257);' data-id="<?php echo $assetId257; ?>" data-room="<?php echo htmlspecialchars($room257); ?>" data-floor="<?php echo htmlspecialchars($floor257); ?>" data-image="<?php echo base64_encode($upload_img257); ?>" data-category="<?php echo htmlspecialchars($category257); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName257); ?>" data-status="<?php echo htmlspecialchars($status257); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status257); ?>; 
            position:absolute; top:465px; left:65px;'>
                        </div>

                        <!-- ASSET 258 -->
                        <img src='../image.php?id=258' style='width:17px; cursor:pointer; position:absolute; top:485px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal258' class="asset-image" onclick='fetchAssetData(258);' data-id="<?php echo $assetId258; ?>" data-room="<?php echo htmlspecialchars($room258); ?>" data-floor="<?php echo htmlspecialchars($floor258); ?>" data-image="<?php echo base64_encode($upload_img258); ?>" data-category="<?php echo htmlspecialchars($category258); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName258); ?>" data-status="<?php echo htmlspecialchars($status258); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status258); ?>; 
            position:absolute; top:485px; left:65px;'>
                        </div>


                        <!-- END OF ROW 1 -->

                        <!-- ASSET 259 -->
                        <img src='../image.php?id=259' style='width:17px; cursor:pointer; position:absolute; top:405px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal259' class="asset-image" onclick='fetchAssetData(259);' data-id="<?php echo $assetId259; ?>" data-room="<?php echo htmlspecialchars($room259); ?>" data-floor="<?php echo htmlspecialchars($floor259); ?>" data-image="<?php echo base64_encode($upload_img259); ?>" data-category="<?php echo htmlspecialchars($category259); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName259); ?>" data-status="<?php echo htmlspecialchars($status259); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status259); ?>; 
            position:absolute; top:405px; left:85px;'>
                        </div>

                        <!-- ASSET 260 -->
                        <img src='../image.php?id=260' style='width:17px; cursor:pointer; position:absolute; top:425px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal260' class="asset-image" onclick='fetchAssetData(260);' data-id="<?php echo $assetId260; ?>" data-room="<?php echo htmlspecialchars($room260); ?>" data-floor="<?php echo htmlspecialchars($floor260); ?>" data-image="<?php echo base64_encode($upload_img260); ?>" data-category="<?php echo htmlspecialchars($category260); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName260); ?>" data-status="<?php echo htmlspecialchars($status260); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status260); ?>; 
            position:absolute; top:425px; left:85px;'>
                        </div>

                        <!-- ASSET 261 -->
                        <img src='../image.php?id=261' style='width:17px; cursor:pointer; position:absolute; top:445px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal261' class="asset-image" onclick='fetchAssetData(261);' data-id="<?php echo $assetId261; ?>" data-room="<?php echo htmlspecialchars($room261); ?>" data-floor="<?php echo htmlspecialchars($floor261); ?>" data-image="<?php echo base64_encode($upload_img261); ?>" data-category="<?php echo htmlspecialchars($category261); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName261); ?>" data-status="<?php echo htmlspecialchars($status261); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status261); ?>; 
            position:absolute; top:445px; left:85px;'>
                        </div>

                        <!-- ASSET 262 -->
                        <img src='../image.php?id=262' style='width:17px; cursor:pointer; position:absolute; top:465px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal262' class="asset-image" onclick='fetchAssetData(262);' data-id="<?php echo $assetId262; ?>" data-room="<?php echo htmlspecialchars($room262); ?>" data-floor="<?php echo htmlspecialchars($floor262); ?>" data-image="<?php echo base64_encode($upload_img262); ?>" data-category="<?php echo htmlspecialchars($category262); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName262); ?>" data-status="<?php echo htmlspecialchars($status262); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status262); ?>; 
            position:absolute; top:465px; left:85px;'>
                        </div>

                        <!-- ASSET 263 -->
                        <img src='../image.php?id=263' style='width:17px; cursor:pointer; position:absolute; top:485px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal263' class="asset-image" onclick='fetchAssetData(263);' data-id="<?php echo $assetId263; ?>" data-room="<?php echo htmlspecialchars($room263); ?>" data-floor="<?php echo htmlspecialchars($floor263); ?>" data-image="<?php echo base64_encode($upload_img263); ?>" data-category="<?php echo htmlspecialchars($category263); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName263); ?>" data-status="<?php echo htmlspecialchars($status263); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status263); ?>; 
            position:absolute; top:485px; left:85px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 264 -->
                        <img src='../image.php?id=264' style='width:17px; cursor:pointer; position:absolute; top:405px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal264' class="asset-image" onclick='fetchAssetData(264);' data-id="<?php echo $assetId264; ?>" data-room="<?php echo htmlspecialchars($room264); ?>" data-floor="<?php echo htmlspecialchars($floor264); ?>" data-image="<?php echo base64_encode($upload_img264); ?>" data-category="<?php echo htmlspecialchars($category264); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName264); ?>" data-status="<?php echo htmlspecialchars($status264); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status264); ?>; 
            position:absolute; top:405px; left:105px;'>
                        </div>

                        <!-- ASSET 265 -->
                        <img src='../image.php?id=265' style='width:17px; cursor:pointer; position:absolute; top:425px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal265' class="asset-image" onclick='fetchAssetData(265);' data-id="<?php echo $assetId265; ?>" data-room="<?php echo htmlspecialchars($room265); ?>" data-floor="<?php echo htmlspecialchars($floor265); ?>" data-image="<?php echo base64_encode($upload_img265); ?>" data-category="<?php echo htmlspecialchars($category265); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName265); ?>" data-status="<?php echo htmlspecialchars($status265); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status265); ?>; 
            position:absolute; top:425px; left:105px;'>
                        </div>

                        <!-- ASSET 266 -->
                        <img src='../image.php?id=266' style='width:17px; cursor:pointer; position:absolute; top:445px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal266' class="asset-image" onclick='fetchAssetData(266);' data-id="<?php echo $assetId266; ?>" data-room="<?php echo htmlspecialchars($room266); ?>" data-floor="<?php echo htmlspecialchars($floor266); ?>" data-image="<?php echo base64_encode($upload_img266); ?>" data-category="<?php echo htmlspecialchars($category266); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName266); ?>" data-status="<?php echo htmlspecialchars($status266); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status266); ?>; 
            position:absolute; top:445px; left:105px;'>
                        </div>

                        <!-- ASSET 267 -->
                        <img src='../image.php?id=267' style='width:17px; cursor:pointer; position:absolute; top:465px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal267' class="asset-image" onclick='fetchAssetData(267);' data-id="<?php echo $assetId267; ?>" data-room="<?php echo htmlspecialchars($room267); ?>" data-floor="<?php echo htmlspecialchars($floor267); ?>" data-image="<?php echo base64_encode($upload_img267); ?>" data-category="<?php echo htmlspecialchars($category267); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName267); ?>" data-status="<?php echo htmlspecialchars($status267); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status267); ?>; 
            position:absolute; top:465px; left:105px;'>
                        </div>

                        <!-- ASSET 268 -->
                        <img src='../image.php?id=268' style='width:17px; cursor:pointer; position:absolute; top:485px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal268' class="asset-image" onclick='fetchAssetData(268);' data-id="<?php echo $assetId268; ?>" data-room="<?php echo htmlspecialchars($room268); ?>" data-floor="<?php echo htmlspecialchars($floor268); ?>" data-image="<?php echo base64_encode($upload_img268); ?>" data-category="<?php echo htmlspecialchars($category268); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName268); ?>" data-status="<?php echo htmlspecialchars($status268); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status268); ?>; 
            position:absolute; top:485px; left:105px;'>
                        </div>


                        <!-- END OF ROW 3 -->

                        <!-- ASSET 269 -->
                        <img src='../image.php?id=269' style='width:17px; cursor:pointer; position:absolute; top:405px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal269' class="asset-image" onclick='fetchAssetData(269);' data-id="<?php echo $assetId269; ?>" data-room="<?php echo htmlspecialchars($room269); ?>" data-floor="<?php echo htmlspecialchars($floor269); ?>" data-image="<?php echo base64_encode($upload_img269); ?>" data-category="<?php echo htmlspecialchars($category269); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName269); ?>" data-status="<?php echo htmlspecialchars($status269); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status269); ?>; 
            position:absolute; top:405px; left:125px;'>
                        </div>

                        <!-- ASSET 270 -->
                        <img src='../image.php?id=270' style='width:17px; cursor:pointer; position:absolute; top:425px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal270' class="asset-image" onclick='fetchAssetData(270);' data-id="<?php echo $assetId270; ?>" data-room="<?php echo htmlspecialchars($room270); ?>" data-floor="<?php echo htmlspecialchars($floor270); ?>" data-image="<?php echo base64_encode($upload_img270); ?>" data-category="<?php echo htmlspecialchars($category270); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName270); ?>" data-status="<?php echo htmlspecialchars($status270); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status270); ?>; 
            position:absolute; top:425px; left:125px;'>
                        </div>

                        <!-- ASSET 271 -->
                        <img src='../image.php?id=271' style='width:17px; cursor:pointer; position:absolute; top:445px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal271' class="asset-image" onclick='fetchAssetData(271);' data-id="<?php echo $assetId271; ?>" data-room="<?php echo htmlspecialchars($room271); ?>" data-floor="<?php echo htmlspecialchars($floor271); ?>" data-image="<?php echo base64_encode($upload_img271); ?>" data-category="<?php echo htmlspecialchars($category271); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName271); ?>" data-status="<?php echo htmlspecialchars($status271); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status271); ?>; 
            position:absolute; top:445px; left:125px;'>
                        </div>

                        <!-- ASSET 272 -->
                        <img src='../image.php?id=272' style='width:17px; cursor:pointer; position:absolute; top:465px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal272' class="asset-image" onclick='fetchAssetData(272);' data-id="<?php echo $assetId272; ?>" data-room="<?php echo htmlspecialchars($room272); ?>" data-floor="<?php echo htmlspecialchars($floor272); ?>" data-image="<?php echo base64_encode($upload_img272); ?>" data-category="<?php echo htmlspecialchars($category272); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName272); ?>" data-status="<?php echo htmlspecialchars($status272); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status272); ?>; 
            position:absolute; top:465px; left:125px;'>
                        </div>

                        <!-- ASSET 273 -->
                        <img src='../image.php?id=273' style='width:17px; cursor:pointer; position:absolute; top:485px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal273' class="asset-image" onclick='fetchAssetData(273);' data-id="<?php echo $assetId273; ?>" data-room="<?php echo htmlspecialchars($room273); ?>" data-floor="<?php echo htmlspecialchars($floor273); ?>" data-image="<?php echo base64_encode($upload_img273); ?>" data-category="<?php echo htmlspecialchars($category273); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName273); ?>" data-status="<?php echo htmlspecialchars($status273); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status273); ?>; 
            position:absolute; top:485px; left:125px;'>
                        </div>


                        <!-- END OF ROW 4 -->

                        <!-- ASSET 274 -->
                        <img src='../image.php?id=274' style='width:17px; cursor:pointer; position:absolute; top:405px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal274' class="asset-image" onclick='fetchAssetData(274);' data-id="<?php echo $assetId274; ?>" data-room="<?php echo htmlspecialchars($room274); ?>" data-floor="<?php echo htmlspecialchars($floor274); ?>" data-image="<?php echo base64_encode($upload_img274); ?>" data-category="<?php echo htmlspecialchars($category274); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName274); ?>" data-status="<?php echo htmlspecialchars($status274); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status274); ?>; 
            position:absolute; top:405px; left:145px;'>
                        </div>

                        <!-- ASSET 275 -->
                        <img src='../image.php?id=275' style='width:17px; cursor:pointer; position:absolute; top:425px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal275' class="asset-image" onclick='fetchAssetData(275);' data-id="<?php echo $assetId275; ?>" data-room="<?php echo htmlspecialchars($room275); ?>" data-floor="<?php echo htmlspecialchars($floor275); ?>" data-image="<?php echo base64_encode($upload_img275); ?>" data-category="<?php echo htmlspecialchars($category275); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName275); ?>" data-status="<?php echo htmlspecialchars($status275); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status275); ?>; 
            position:absolute; top:425px; left:145px;'>
                        </div>

                        <!-- ASSET 276 -->
                        <img src='../image.php?id=276' style='width:17px; cursor:pointer; position:absolute; top:445px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal276' class="asset-image" onclick='fetchAssetData(276);' data-id="<?php echo $assetId276; ?>" data-room="<?php echo htmlspecialchars($room276); ?>" data-floor="<?php echo htmlspecialchars($floor276); ?>" data-image="<?php echo base64_encode($upload_img276); ?>" data-category="<?php echo htmlspecialchars($category276); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName276); ?>" data-status="<?php echo htmlspecialchars($status276); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status276); ?>; 
            position:absolute; top:445px; left:145px;'>
                        </div>

                        <!-- ASSET 277 -->
                        <img src='../image.php?id=277' style='width:17px; cursor:pointer; position:absolute; top:465px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal277' class="asset-image" onclick='fetchAssetData(277);' data-id="<?php echo $assetId277; ?>" data-room="<?php echo htmlspecialchars($room277); ?>" data-floor="<?php echo htmlspecialchars($floor277); ?>" data-image="<?php echo base64_encode($upload_img277); ?>" data-category="<?php echo htmlspecialchars($category277); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName277); ?>" data-status="<?php echo htmlspecialchars($status277); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status277); ?>; 
            position:absolute; top:465px; left:145px;'>
                        </div>


                        <!-- ASSET 278 -->
                        <img src='../image.php?id=278' style='width:17px; cursor:pointer; position:absolute; top:485px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal278' class="asset-image" onclick='fetchAssetData(278);' data-id="<?php echo $assetId278; ?>" data-room="<?php echo htmlspecialchars($room278); ?>" data-floor="<?php echo htmlspecialchars($floor278); ?>" data-image="<?php echo base64_encode($upload_img278); ?>" data-category="<?php echo htmlspecialchars($category278); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName278); ?>" data-status="<?php echo htmlspecialchars($status278); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status278); ?>; 
            position:absolute; top:485px; left:145px;'>
                        </div>

                        <!-- ASSET 279 -->
                        <img src='../image.php?id=279' style='width:17px; cursor:pointer; position:absolute; top:445px; left:198px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal279' class="asset-image" onclick='fetchAssetData(279);' data-id="<?php echo $assetId279; ?>" data-room="<?php echo htmlspecialchars($room279); ?>" data-floor="<?php echo htmlspecialchars($floor279); ?>" data-image="<?php echo base64_encode($upload_img279); ?>" data-category="<?php echo htmlspecialchars($category279); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName279); ?>" data-status="<?php echo htmlspecialchars($status279); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status279); ?>; 
            position:absolute; top:455px; left:208px;'>
                        </div>

                        <!-- ASSET 286 -->
                        <img src='../image.php?id=286' style='width:15px; cursor:pointer; position:absolute; top:314px; left:58px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal286' class="asset-image" onclick='fetchAssetData(286);' data-id="<?php echo $assetId286; ?>" data-room="<?php echo htmlspecialchars($room286); ?>" data-floor="<?php echo htmlspecialchars($floor286); ?>" data-image="<?php echo base64_encode($upload_img286); ?>" data-category="<?php echo htmlspecialchars($category286); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName286); ?>" data-status="<?php echo htmlspecialchars($status286); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status286); ?>;
    position:absolute; top:330px; left:66px;'>
                        </div>

                        <!-- ASSET 287 -->
                        <img src='../image.php?id=287' style='width:15px; cursor:pointer; position:absolute; top:314px; left:87px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal287' class="asset-image" onclick='fetchAssetData(287);' data-id="<?php echo $assetId287; ?>" data-room="<?php echo htmlspecialchars($room287); ?>" data-floor="<?php echo htmlspecialchars($floor287); ?>" data-image="<?php echo base64_encode($upload_img287); ?>" data-category="<?php echo htmlspecialchars($category287); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName287); ?>" data-status="<?php echo htmlspecialchars($status287); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status287); ?>;
    position:absolute; top:330px; left:94px;'>
                        </div>

                        <!-- ASSET 288 -->
                        <img src='../image.php?id=288' style='width:15px; cursor:pointer; position:absolute; top:314px; left:115px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal288' class="asset-image" onclick='fetchAssetData(288);' data-id="<?php echo $assetId288; ?>" data-room="<?php echo htmlspecialchars($room288); ?>" data-floor="<?php echo htmlspecialchars($floor288); ?>" data-image="<?php echo base64_encode($upload_img288); ?>" data-category="<?php echo htmlspecialchars($category288); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName288); ?>" data-status="<?php echo htmlspecialchars($status288); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status288); ?>;
    position:absolute; top:330px; left:123px;'>
                        </div>

                        <!-- ASSET 290 -->
                        <img src='../image.php?id=290' style='width:15px; cursor:pointer; position:absolute; top:314px; left:148px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal290' class="asset-image" onclick='fetchAssetData(290);' data-id="<?php echo $assetId290; ?>" data-room="<?php echo htmlspecialchars($room290); ?>" data-floor="<?php echo htmlspecialchars($floor290); ?>" data-image="<?php echo base64_encode($upload_img290); ?>" data-category="<?php echo htmlspecialchars($category290); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName290); ?>" data-status="<?php echo htmlspecialchars($status290); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status290); ?>;
    position:absolute; top:330px; left:156px;'>
                        </div>

                        <!-- ASSET 291 -->
                        <img src='../image.php?id=291' style='width:15px; cursor:pointer; position:absolute; top:314px; left:179px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal291' class="asset-image" onclick='fetchAssetData(291);' data-id="<?php echo $assetId291; ?>" data-room="<?php echo htmlspecialchars($room291); ?>" data-floor="<?php echo htmlspecialchars($floor291); ?>" data-image="<?php echo base64_encode($upload_img291); ?>" data-category="<?php echo htmlspecialchars($category291); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName291); ?>" data-status="<?php echo htmlspecialchars($status291); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status291); ?>;
    position:absolute; top:330px; left:186px;'>
                        </div>

                        <!-- ASSET 292 -->
                        <img src='../image.php?id=292' style='width:15px; cursor:pointer; position:absolute; top:314px; left:207px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal292' class="asset-image" onclick='fetchAssetData(292);' data-id="<?php echo $assetId292; ?>" data-room="<?php echo htmlspecialchars($room292); ?>" data-floor="<?php echo htmlspecialchars($floor292); ?>" data-image="<?php echo base64_encode($upload_img292); ?>" data-category="<?php echo htmlspecialchars($category292); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName292); ?>" data-status="<?php echo htmlspecialchars($status292); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status292); ?>;
    position:absolute; top:330px; left:215px;'>
                        </div>


                        <!-- END OF CR RIGHT SIDE WOMENS -->

                        <!-- START OF CR LEFT SIDE WOMENS -->

                        <!-- ASSET 293 -->
                        <img src='../image.php?id=293' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal293' class="asset-image" onclick='fetchAssetData(293);' data-id="<?php echo $assetId293; ?>" data-room="<?php echo htmlspecialchars($room293); ?>" data-floor="<?php echo htmlspecialchars($floor293); ?>" data-image="<?php echo base64_encode($upload_img293); ?>" data-category="<?php echo htmlspecialchars($category293); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName293); ?>" data-status="<?php echo htmlspecialchars($status293); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status293); ?>;
    position:absolute; top:330px; left:1018px;'>
                        </div>

                        <!-- ASSET 294 -->
                        <img src='../image.php?id=294' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1041px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal294' class="asset-image" onclick='fetchAssetData(294);' data-id="<?php echo $assetId294; ?>" data-room="<?php echo htmlspecialchars($room294); ?>" data-floor="<?php echo htmlspecialchars($floor294); ?>" data-image="<?php echo base64_encode($upload_img294); ?>" data-category="<?php echo htmlspecialchars($category294); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName294); ?>" data-status="<?php echo htmlspecialchars($status294); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status294); ?>;
    position:absolute; top:330px; left:1049px;'>
                        </div>

                        <!-- ASSET 295 -->
                        <img src='../image.php?id=295' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1070px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal295' class="asset-image" onclick='fetchAssetData(295);' data-id="<?php echo $assetId295; ?>" data-room="<?php echo htmlspecialchars($room295); ?>" data-floor="<?php echo htmlspecialchars($floor295); ?>" data-image="<?php echo base64_encode($upload_img295); ?>" data-category="<?php echo htmlspecialchars($category295); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName295); ?>" data-status="<?php echo htmlspecialchars($status295); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status295); ?>;
    position:absolute; top:330px; left:1078px;'>
                        </div>

                        <!-- ASSET 296 -->
                        <img src='../image.php?id=296' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1100px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal296' class="asset-image" onclick='fetchAssetData(296);' data-id="<?php echo $assetId296; ?>" data-room="<?php echo htmlspecialchars($room296); ?>" data-floor="<?php echo htmlspecialchars($floor296); ?>" data-image="<?php echo base64_encode($upload_img296); ?>" data-category="<?php echo htmlspecialchars($category296); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName296); ?>" data-status="<?php echo htmlspecialchars($status296); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status296); ?>;
    position:absolute; top:330px; left:1108px;'>
                        </div>


                        <!-- ASSET 297 -->
                        <img src='../image.php?id=297' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1132px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal297' class="asset-image" onclick='fetchAssetData(297);' data-id="<?php echo $assetId297; ?>" data-room="<?php echo htmlspecialchars($room297); ?>" data-floor="<?php echo htmlspecialchars($floor297); ?>" data-image="<?php echo base64_encode($upload_img297); ?>" data-category="<?php echo htmlspecialchars($category297); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName297); ?>" data-status="<?php echo htmlspecialchars($status297); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status297); ?>;
    position:absolute; top:330px; left:1140px;'>
                        </div>

                        <!-- ASSET 1636 -->
                        <img src='../image.php?id=1636' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1162px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal1636' class="asset-image" onclick='fetchAssetData(1636);' data-id="<?php echo $assetId1636; ?>" data-room="<?php echo htmlspecialchars($room1636); ?>" data-floor="<?php echo htmlspecialchars($floor1636); ?>" data-image="<?php echo base64_encode($upload_img1636); ?>" data-category="<?php echo htmlspecialchars($category1636); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName1636); ?>" data-status="<?php echo htmlspecialchars($status1636); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status1636); ?>;
    position:absolute; top:330px; left:1170px;'>
                        </div>

                        <!-- LIGHTS HALLWAY -->

                        <!-- ASSET 7425 -->
                        <img src='../image.php?id=7425' style='width:20px; cursor:pointer; position:absolute; top:250px; left:110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7425' class="asset-image" onclick='fetchAssetData(7425);' data-id="<?php echo $assetId7425; ?>" data-room="<?php echo htmlspecialchars($room7425); ?>" data-floor="<?php echo htmlspecialchars($floor7425); ?>" data-image="<?php echo base64_encode($upload_img7425); ?>" data-category="<?php echo htmlspecialchars($category7425); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7425); ?>" data-status="<?php echo htmlspecialchars($status7425); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7425); ?>;
    position:absolute; top:250px; left:120px;'>
                        </div>

                        <!-- ASSET 7426 -->
                        <img src='../image.php?id=7426' style='width:20px; cursor:pointer; position:absolute; top:250px; left:170px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7426' class="asset-image" onclick='fetchAssetData(7426);' data-id="<?php echo $assetId7426; ?>" data-room="<?php echo htmlspecialchars($room7426); ?>" data-floor="<?php echo htmlspecialchars($floor7426); ?>" data-image="<?php echo base64_encode($upload_img7426); ?>" data-category="<?php echo htmlspecialchars($category7426); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7426); ?>" data-status="<?php echo htmlspecialchars($status7426); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7426); ?>;
    position:absolute; top:250px; left:180px;'>
                        </div>


                        <!-- ASSET 7427 -->
                        <img src='../image.php?id=7427' style='width:20px; cursor:pointer; position:absolute; top:250px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7427' class="asset-image" onclick='fetchAssetData(7427);' data-id="<?php echo $assetId7427; ?>" data-room="<?php echo htmlspecialchars($room7427); ?>" data-floor="<?php echo htmlspecialchars($floor7427); ?>" data-image="<?php echo base64_encode($upload_img7427); ?>" data-category="<?php echo htmlspecialchars($category7427); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7427); ?>" data-status="<?php echo htmlspecialchars($status7427); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7427); ?>;
    position:absolute; top:250px; left:250px;'>
                        </div>

                        <!-- ASSET 7428 -->
                        <img src='../image.php?id=7428' style='width:20px; cursor:pointer; position:absolute; top:250px; left:300px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7428' class="asset-image" onclick='fetchAssetData(7428);' data-id="<?php echo $assetId7428; ?>" data-room="<?php echo htmlspecialchars($room7428); ?>" data-floor="<?php echo htmlspecialchars($floor7428); ?>" data-image="<?php echo base64_encode($upload_img7428); ?>" data-category="<?php echo htmlspecialchars($category7428); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7428); ?>" data-status="<?php echo htmlspecialchars($status7428); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7428); ?>;
    position:absolute; top:250px; left:310px;'>
                        </div>

                        <!-- ASSET 7429 -->
                        <img src='../image.php?id=7429' style='width:20px; cursor:pointer; position:absolute; top:250px; left:380px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7429' class="asset-image" onclick='fetchAssetData(7429);' data-id="<?php echo $assetId7429; ?>" data-room="<?php echo htmlspecialchars($room7429); ?>" data-floor="<?php echo htmlspecialchars($floor7429); ?>" data-image="<?php echo base64_encode($upload_img7429); ?>" data-category="<?php echo htmlspecialchars($category7429); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7429); ?>" data-status="<?php echo htmlspecialchars($status7429); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7429); ?>;
    position:absolute; top:250px; left:390px;'>
                        </div>

                        <!-- ASSET 7430 -->
                        <img src='../image.php?id=7430' style='width:20px; cursor:pointer; position:absolute; top:250px; left:480px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7430' class="asset-image" onclick='fetchAssetData(7430);' data-id="<?php echo $assetId7430; ?>" data-room="<?php echo htmlspecialchars($room7430); ?>" data-floor="<?php echo htmlspecialchars($floor7430); ?>" data-image="<?php echo base64_encode($upload_img7430); ?>" data-category="<?php echo htmlspecialchars($category7430); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7430); ?>" data-status="<?php echo htmlspecialchars($status7430); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7430); ?>;
    position:absolute; top:250px; left:490px;'>
                        </div>


                        <!-- ASSET 7431 -->
                        <img src='../image.php?id=7431' style='width:20px; cursor:pointer; position:absolute; top:250px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7431' class="asset-image" onclick='fetchAssetData(7431);' data-id="<?php echo $assetId7431; ?>" data-room="<?php echo htmlspecialchars($room7431); ?>" data-floor="<?php echo htmlspecialchars($floor7431); ?>" data-image="<?php echo base64_encode($upload_img7431); ?>" data-category="<?php echo htmlspecialchars($category7431); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7431); ?>" data-status="<?php echo htmlspecialchars($status7431); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7431); ?>;
    position:absolute; top:250px; left:570px;'>
                        </div>

                        <!-- ASSET 7432 -->
                        <img src='../image.php?id=7432' style='width:20px; cursor:pointer; position:absolute; top:250px; left:650px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7432' class="asset-image" onclick='fetchAssetData(7432);' data-id="<?php echo $assetId7432; ?>" data-room="<?php echo htmlspecialchars($room7432); ?>" data-floor="<?php echo htmlspecialchars($floor7432); ?>" data-image="<?php echo base64_encode($upload_img7432); ?>" data-category="<?php echo htmlspecialchars($category7432); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7432); ?>" data-status="<?php echo htmlspecialchars($status7432); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7432); ?>;
    position:absolute; top:250px; left:660px;'>
                        </div>

                        <!-- ASSET 7433 -->
                        <img src='../image.php?id=7433' style='width:20px; cursor:pointer; position:absolute; top:250px; left:730px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7433' class="asset-image" onclick='fetchAssetData(7433);' data-id="<?php echo $assetId7433; ?>" data-room="<?php echo htmlspecialchars($room7433); ?>" data-floor="<?php echo htmlspecialchars($floor7433); ?>" data-image="<?php echo base64_encode($upload_img7433); ?>" data-category="<?php echo htmlspecialchars($category7433); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7433); ?>" data-status="<?php echo htmlspecialchars($status7433); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7433); ?>;
    position:absolute; top:250px; left:740px;'>
                        </div>

                        <!-- ASSET 7434 -->
                        <img src='../image.php?id=7434' style='width:20px; cursor:pointer; position:absolute; top:250px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7434' class="asset-image" onclick='fetchAssetData(7434);' data-id="<?php echo $assetId7434; ?>" data-room="<?php echo htmlspecialchars($room7434); ?>" data-floor="<?php echo htmlspecialchars($floor7434); ?>" data-image="<?php echo base64_encode($upload_img7434); ?>" data-category="<?php echo htmlspecialchars($category7434); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7434); ?>" data-status="<?php echo htmlspecialchars($status7434); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7434); ?>;
    position:absolute; top:250px; left:840px;'>
                        </div>


                        <!-- ASSET 7435 -->
                        <img src='../image.php?id=7435' style='width:20px; cursor:pointer; position:absolute; top:250px; left:915px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7435' class="asset-image" onclick='fetchAssetData(7435);' data-id="<?php echo $assetId7435; ?>" data-room="<?php echo htmlspecialchars($room7435); ?>" data-floor="<?php echo htmlspecialchars($floor7435); ?>" data-image="<?php echo base64_encode($upload_img7435); ?>" data-category="<?php echo htmlspecialchars($category7435); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7435); ?>" data-status="<?php echo htmlspecialchars($status7435); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7435); ?>;
    position:absolute; top:250px; left:925px;'>
                        </div>

                        <!-- ASSET 7436 -->
                        <img src='../image.php?id=7436' style='width:20px; cursor:pointer; position:absolute; top:250px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7436' class="asset-image" onclick='fetchAssetData(7436);' data-id="<?php echo $assetId7436; ?>" data-room="<?php echo htmlspecialchars($room7436); ?>" data-floor="<?php echo htmlspecialchars($floor7436); ?>" data-image="<?php echo base64_encode($upload_img7436); ?>" data-category="<?php echo htmlspecialchars($category7436); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7436); ?>" data-status="<?php echo htmlspecialchars($status7436); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7436); ?>;
    position:absolute; top:250px; left:980px;'>
                        </div>

                        <!-- ASSET 7437 -->
                        <img src='../image.php?id=7437' style='width:20px; cursor:pointer; position:absolute; top:250px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7437' class="asset-image" onclick='fetchAssetData(7437);' data-id="<?php echo $assetId7437; ?>" data-room="<?php echo htmlspecialchars($room7437); ?>" data-floor="<?php echo htmlspecialchars($floor7437); ?>" data-image="<?php echo base64_encode($upload_img7437); ?>" data-category="<?php echo htmlspecialchars($category7437); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7437); ?>" data-status="<?php echo htmlspecialchars($status7437); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7437); ?>;
    position:absolute; top:250px; left:1050px;'>
                        </div>

                        <!-- ASSET 7438 -->
                        <img src='../image.php?id=7438' style='width:20px; cursor:pointer; position:absolute; top:250px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7438' class="asset-image" onclick='fetchAssetData(7438);' data-id="<?php echo $assetId7438; ?>" data-room="<?php echo htmlspecialchars($room7438); ?>" data-floor="<?php echo htmlspecialchars($floor7438); ?>" data-image="<?php echo base64_encode($upload_img7438); ?>" data-category="<?php echo htmlspecialchars($category7438); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7438); ?>" data-status="<?php echo htmlspecialchars($status7438); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7438); ?>;
    position:absolute; top:250px; left:1130px;'>
                        </div>


                        <!-- VERTICAL -->

                        <!-- ASSET 7439 -->
                        <img src='../image.php?id=7439' style='width:20px; cursor:pointer; position:absolute; top:290px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7439' class="asset-image" onclick='fetchAssetData(7439);' data-id="<?php echo $assetId7439; ?>" data-room="<?php echo htmlspecialchars($room7439); ?>" data-floor="<?php echo htmlspecialchars($floor7439); ?>" data-image="<?php echo base64_encode($upload_img7439); ?>" data-category="<?php echo htmlspecialchars($category7439); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7439); ?>" data-status="<?php echo htmlspecialchars($status7439); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7439); ?>;
    position:absolute; top:290px; left:250px;'>
                        </div>

                        <!-- ASSET 7440 -->
                        <img src='../image.php?id=7440' style='width:20px; cursor:pointer; position:absolute; top:330px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7440' class="asset-image" onclick='fetchAssetData(7440);' data-id="<?php echo $assetId7440; ?>" data-room="<?php echo htmlspecialchars($room7440); ?>" data-floor="<?php echo htmlspecialchars($floor7440); ?>" data-image="<?php echo base64_encode($upload_img7440); ?>" data-category="<?php echo htmlspecialchars($category7440); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7440); ?>" data-status="<?php echo htmlspecialchars($status7440); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7440); ?>;
    position:absolute; top:330px; left:250px;'>
                        </div>

                        <!-- ASSET 7441 -->
                        <img src='../image.php?id=7441' style='width:20px; cursor:pointer; position:absolute; top:370px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7441' class="asset-image" onclick='fetchAssetData(7441);' data-id="<?php echo $assetId7441; ?>" data-room="<?php echo htmlspecialchars($room7441); ?>" data-floor="<?php echo htmlspecialchars($floor7441); ?>" data-image="<?php echo base64_encode($upload_img7441); ?>" data-category="<?php echo htmlspecialchars($category7441); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7441); ?>" data-status="<?php echo htmlspecialchars($status7441); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7441); ?>;
    position:absolute; top:370px; left:250px;'>
                        </div>

                        <!-- ASSET 7442 -->
                        <img src='../image.php?id=7442' style='width:20px; cursor:pointer; position:absolute; top:420px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7442' class="asset-image" onclick='fetchAssetData(7442);' data-id="<?php echo $assetId7442; ?>" data-room="<?php echo htmlspecialchars($room7442); ?>" data-floor="<?php echo htmlspecialchars($floor7442); ?>" data-image="<?php echo base64_encode($upload_img7442); ?>" data-category="<?php echo htmlspecialchars($category7442); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7442); ?>" data-status="<?php echo htmlspecialchars($status7442); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7442); ?>;
    position:absolute; top:420px; left:250px;'>
                        </div>

                        <!-- ASSET 7443 -->
                        <img src='../image.php?id=7443' style='width:20px; cursor:pointer; position:absolute; top:460px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7443' class="asset-image" onclick='fetchAssetData(7443);' data-id="<?php echo $assetId7443; ?>" data-room="<?php echo htmlspecialchars($room7443); ?>" data-floor="<?php echo htmlspecialchars($floor7443); ?>" data-image="<?php echo base64_encode($upload_img7443); ?>" data-category="<?php echo htmlspecialchars($category7443); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7443); ?>" data-status="<?php echo htmlspecialchars($status7443); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7443); ?>;
    position:absolute; top:460px; left:250px;'>
                        </div>

                        <!-- ASSET 7444 -->
                        <img src='../image.php?id=7444' style='width:20px; cursor:pointer; position:absolute; top:500px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7444' class="asset-image" onclick='fetchAssetData(7444);' data-id="<?php echo $assetId7444; ?>" data-room="<?php echo htmlspecialchars($room7444); ?>" data-floor="<?php echo htmlspecialchars($floor7444); ?>" data-image="<?php echo base64_encode($upload_img7444); ?>" data-category="<?php echo htmlspecialchars($category7444); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7444); ?>" data-status="<?php echo htmlspecialchars($status7444); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7444); ?>;
    position:absolute; top:500px; left:250px;'>
                        </div>

                        <!-- ASSET 7445 -->
                        <img src='../image.php?id=7445' style='width:20px; cursor:pointer; position:absolute; top:360px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7445' class="asset-image" onclick='fetchAssetData(7445);' data-id="<?php echo $assetId7445; ?>" data-room="<?php echo htmlspecialchars($room7445); ?>" data-floor="<?php echo htmlspecialchars($floor7445); ?>" data-image="<?php echo base64_encode($upload_img7445); ?>" data-category="<?php echo htmlspecialchars($category7445); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7445); ?>" data-status="<?php echo htmlspecialchars($status7445); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7445); ?>;
    position:absolute; top:360px; left:210px;'>
                        </div>

                        <!-- ASSET 7446 -->
                        <img src='../image.php?id=7446' style='width:20px; cursor:pointer; position:absolute; top:360px; left:150px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7446' class="asset-image" onclick='fetchAssetData(7446);' data-id="<?php echo $assetId7446; ?>" data-room="<?php echo htmlspecialchars($room7446); ?>" data-floor="<?php echo htmlspecialchars($floor7446); ?>" data-image="<?php echo base64_encode($upload_img7446); ?>" data-category="<?php echo htmlspecialchars($category7446); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7446); ?>" data-status="<?php echo htmlspecialchars($status7446); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7446); ?>;
    position:absolute; top:360px; left:160px;'>
                        </div>

                        <!-- VERTICAL RIGHT SIDE -->

                        <!-- ASSET 7447 -->
                        <img src='../image.php?id=7447' style='width:20px; cursor:pointer; position:absolute; top:290px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7447' class="asset-image" onclick='fetchAssetData(7447);' data-id="<?php echo $assetId7447; ?>" data-room="<?php echo htmlspecialchars($room7447); ?>" data-floor="<?php echo htmlspecialchars($floor7447); ?>" data-image="<?php echo base64_encode($upload_img7447); ?>" data-category="<?php echo htmlspecialchars($category7447); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7447); ?>" data-status="<?php echo htmlspecialchars($status7447); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7447); ?>;
    position:absolute; top:290px; left:980px;'>
                        </div>

                        <!-- ASSET 7448 -->
                        <img src='../image.php?id=7448' style='width:20px; cursor:pointer; position:absolute; top:330px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7448' class="asset-image" onclick='fetchAssetData(7448);' data-id="<?php echo $assetId7448; ?>" data-room="<?php echo htmlspecialchars($room7448); ?>" data-floor="<?php echo htmlspecialchars($floor7448); ?>" data-image="<?php echo base64_encode($upload_img7448); ?>" data-category="<?php echo htmlspecialchars($category7448); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7448); ?>" data-status="<?php echo htmlspecialchars($status7448); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7448); ?>;
    position:absolute; top:330px; left:980px;'>
                        </div>

                        <!-- ASSET 7449 -->
                        <img src='../image.php?id=7449' style='width:20px; cursor:pointer; position:absolute; top:370px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7449' class="asset-image" onclick='fetchAssetData(7449);' data-id="<?php echo $assetId7449; ?>" data-room="<?php echo htmlspecialchars($room7449); ?>" data-floor="<?php echo htmlspecialchars($floor7449); ?>" data-image="<?php echo base64_encode($upload_img7449); ?>" data-category="<?php echo htmlspecialchars($category7449); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7449); ?>" data-status="<?php echo htmlspecialchars($status7449); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7449); ?>;
    position:absolute; top:370px; left:980px;'>
                        </div>

                        <!-- ASSET 7450 -->
                        <img src='../image.php?id=7450' style='width:20px; cursor:pointer; position:absolute; top:420px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7450' class="asset-image" onclick='fetchAssetData(7450);' data-id="<?php echo $assetId7450; ?>" data-room="<?php echo htmlspecialchars($room7450); ?>" data-floor="<?php echo htmlspecialchars($floor7450); ?>" data-image="<?php echo base64_encode($upload_img7450); ?>" data-category="<?php echo htmlspecialchars($category7450); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7450); ?>" data-status="<?php echo htmlspecialchars($status7450); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7450); ?>;
    position:absolute; top:420px; left:980px;'>
                        </div>


                        <!-- ASSET 7451 -->
                        <img src='../image.php?id=7451' style='width:20px; cursor:pointer; position:absolute; top:460px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7451' class="asset-image" onclick='fetchAssetData(7451);' data-id="<?php echo $assetId7451; ?>" data-room="<?php echo htmlspecialchars($room7451); ?>" data-floor="<?php echo htmlspecialchars($floor7451); ?>" data-image="<?php echo base64_encode($upload_img7451); ?>" data-category="<?php echo htmlspecialchars($category7451); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7451); ?>" data-status="<?php echo htmlspecialchars($status7451); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7451); ?>;
    position:absolute; top:460px; left:980px;'>
                        </div>

                        <!-- ASSET 7452 -->
                        <img src='../image.php?id=7452' style='width:20px; cursor:pointer; position:absolute; top:500px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7452' class="asset-image" onclick='fetchAssetData(7452);' data-id="<?php echo $assetId7452; ?>" data-room="<?php echo htmlspecialchars($room7452); ?>" data-floor="<?php echo htmlspecialchars($floor7452); ?>" data-image="<?php echo base64_encode($upload_img7452); ?>" data-category="<?php echo htmlspecialchars($category7452); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7452); ?>" data-status="<?php echo htmlspecialchars($status7452); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7452); ?>;
    position:absolute; top:500px; left:980px;'>
                        </div>

                        <!-- ASSET 7453 -->
                        <img src='../image.php?id=7453' style='width:20px; cursor:pointer; position:absolute; top:355px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7453' class="asset-image" onclick='fetchAssetData(7453);' data-id="<?php echo $assetId7453; ?>" data-room="<?php echo htmlspecialchars($room7453); ?>" data-floor="<?php echo htmlspecialchars($floor7453); ?>" data-image="<?php echo base64_encode($upload_img7453); ?>" data-category="<?php echo htmlspecialchars($category7453); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7453); ?>" data-status="<?php echo htmlspecialchars($status7453); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7453); ?>;
    position:absolute; top:355px; left:1020px;'>
                        </div>

                        <!-- ASSET 7454 -->
                        <img src='../image.php?id=7454' style='width:20px; cursor:pointer; position:absolute; top:355px; left:1070px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7454' class="asset-image" onclick='fetchAssetData(7454);' data-id="<?php echo $assetId7454; ?>" data-room="<?php echo htmlspecialchars($room7454); ?>" data-floor="<?php echo htmlspecialchars($floor7454); ?>" data-image="<?php echo base64_encode($upload_img7454); ?>" data-category="<?php echo htmlspecialchars($category7454); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7454); ?>" data-status="<?php echo htmlspecialchars($status7454); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7454); ?>;
    position:absolute; top:355px; left:1080px;'>
                        </div>


                        <!-- END OF LIGHTS HALLWAY -->

                        <!-- START OF LIGHTS IB101A -->

                        <!-- ROW 1 LIGHTS -->

                        <!-- ASSET 7319 -->
                        <img src='../image.php?id=7319' style='width:20px; cursor:pointer; position:absolute; top:390px; left:1165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7319' class="asset-image" onclick='fetchAssetData(7319);' data-id="<?php echo $assetId7319; ?>" data-room="<?php echo htmlspecialchars($room7319); ?>" data-floor="<?php echo htmlspecialchars($floor7319); ?>" data-image="<?php echo base64_encode($upload_img7319); ?>" data-category="<?php echo htmlspecialchars($category7319); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7319); ?>" data-status="<?php echo htmlspecialchars($status7319); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7319); ?>;
    position:absolute; top:390px; left:1175px;'>
                        </div>

                        <!-- ASSET 7320 -->
                        <img src='../image.php?id=7320' style='width:20px; cursor:pointer; position:absolute; top:500px; left:1165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7320' class="asset-image" onclick='fetchAssetData(7320);' data-id="<?php echo $assetId7320; ?>" data-room="<?php echo htmlspecialchars($room7320); ?>" data-floor="<?php echo htmlspecialchars($floor7320); ?>" data-image="<?php echo base64_encode($upload_img7320); ?>" data-category="<?php echo htmlspecialchars($category7320); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7320); ?>" data-status="<?php echo htmlspecialchars($status7320); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7320); ?>;
    position:absolute; top:510px; left:1175px;'>
                        </div>

                        <!-- ASSET 7321 -->
                        <img src='../image.php?id=7321' style='width:20px; cursor:pointer; position:absolute; top:448px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7321' class="asset-image" onclick='fetchAssetData(7321);' data-id="<?php echo $assetId7321; ?>" data-room="<?php echo htmlspecialchars($room7321); ?>" data-floor="<?php echo htmlspecialchars($floor7321); ?>" data-image="<?php echo base64_encode($upload_img7321); ?>" data-category="<?php echo htmlspecialchars($category7321); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7321); ?>" data-status="<?php echo htmlspecialchars($status7321); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7321); ?>;
    position:absolute; top:448px; left:1130px;'>
                        </div>

                        <!-- ASSET 7322 -->
                        <img src='../image.php?id=7322' style='width:20px; cursor:pointer; position:absolute; top:390px; left:1090px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7322' class="asset-image" onclick='fetchAssetData(7322);' data-id="<?php echo $assetId7322; ?>" data-room="<?php echo htmlspecialchars($room7322); ?>" data-floor="<?php echo htmlspecialchars($floor7322); ?>" data-image="<?php echo base64_encode($upload_img7322); ?>" data-category="<?php echo htmlspecialchars($category7322); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7322); ?>" data-status="<?php echo htmlspecialchars($status7322); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7322); ?>;
    position:absolute; top:390px; left:1100px;'>
                        </div>


                        <!-- ASSET 7323 -->
                        <img src='../image.php?id=7323' style='width:20px; cursor:pointer; position:absolute; top:500px; left:1090px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7323' class="asset-image" onclick='fetchAssetData(7323);' data-id="<?php echo $assetId7323; ?>" data-room="<?php echo htmlspecialchars($room7323); ?>" data-floor="<?php echo htmlspecialchars($floor7323); ?>" data-image="<?php echo base64_encode($upload_img7323); ?>" data-category="<?php echo htmlspecialchars($category7323); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7323); ?>" data-status="<?php echo htmlspecialchars($status7323); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7323); ?>;
    position:absolute; top:510px; left:1100px;'>
                        </div>

                        <!-- ASSET 7324 -->
                        <img src='../image.php?id=7324' style='width:20px; cursor:pointer; position:absolute; top:448px; left:1079px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7324' class="asset-image" onclick='fetchAssetData(7324);' data-id="<?php echo $assetId7324; ?>" data-room="<?php echo htmlspecialchars($room7324); ?>" data-floor="<?php echo htmlspecialchars($floor7324); ?>" data-image="<?php echo base64_encode($upload_img7324); ?>" data-category="<?php echo htmlspecialchars($category7324); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7324); ?>" data-status="<?php echo htmlspecialchars($status7324); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7324); ?>;
    position:absolute; top:448px; left:1088px;'>
                        </div>

                        <!-- ASSET 7325 -->
                        <img src='../image.php?id=7325' style='width:20px; cursor:pointer; position:absolute; top:420px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7325' class="asset-image" onclick='fetchAssetData(7325);' data-id="<?php echo $assetId7325; ?>" data-room="<?php echo htmlspecialchars($room7325); ?>" data-floor="<?php echo htmlspecialchars($floor7325); ?>" data-image="<?php echo base64_encode($upload_img7325); ?>" data-category="<?php echo htmlspecialchars($category7325); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7325); ?>" data-status="<?php echo htmlspecialchars($status7325); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7325); ?>;
    position:absolute; top:420px; left:1020px;'>
                        </div>

                        <!-- ASSET 7326 -->
                        <img src='../image.php?id=7326' style='width:20px; cursor:pointer; position:absolute; top:480px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7326' class="asset-image" onclick='fetchAssetData(7326);' data-id="<?php echo $assetId7326; ?>" data-room="<?php echo htmlspecialchars($room7326); ?>" data-floor="<?php echo htmlspecialchars($floor7326); ?>" data-image="<?php echo base64_encode($upload_img7326); ?>" data-category="<?php echo htmlspecialchars($category7326); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7326); ?>" data-status="<?php echo htmlspecialchars($status7326); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7326); ?>;
    position:absolute; top:480px; left:1020px;'>
                        </div>


                        <!-- END OF IB101A -->

                        <!-- START OF IB102A LIGHTS -->

                        <!-- ASSET 7328 -->
                        <img src='../image.php?id=7328' style='width:20px; cursor:pointer; position:absolute; top:90px; left:1163px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7328' class="asset-image" onclick='fetchAssetData(7328);' data-id="<?php echo $assetId7328; ?>" data-room="<?php echo htmlspecialchars($room7328); ?>" data-floor="<?php echo htmlspecialchars($floor7328); ?>" data-image="<?php echo base64_encode($upload_img7328); ?>" data-category="<?php echo htmlspecialchars($category7328); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7328); ?>" data-status="<?php echo htmlspecialchars($status7328); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7328); ?>;
    position:absolute; top:90px; left:1173px;'>
                        </div>

                        <!-- ASSET 7329 -->
                        <img src='../image.php?id=7329' style='width:20px; cursor:pointer; position:absolute; top:90px; left:1055px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7329' class="asset-image" onclick='fetchAssetData(7329);' data-id="<?php echo $assetId7329; ?>" data-room="<?php echo htmlspecialchars($room7329); ?>" data-floor="<?php echo htmlspecialchars($floor7329); ?>" data-image="<?php echo base64_encode($upload_img7329); ?>" data-category="<?php echo htmlspecialchars($category7329); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7329); ?>" data-status="<?php echo htmlspecialchars($status7329); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7329); ?>;
    position:absolute; top:90px; left:1065px;'>
                        </div>

                        <!-- ASSET 7330 -->
                        <img src='../image.php?id=7330' style='width:20px; cursor:pointer; position:absolute; top:135px; left:1110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7330' class="asset-image" onclick='fetchAssetData(7330);' data-id="<?php echo $assetId7330; ?>" data-room="<?php echo htmlspecialchars($room7330); ?>" data-floor="<?php echo htmlspecialchars($floor7330); ?>" data-image="<?php echo base64_encode($upload_img7330); ?>" data-category="<?php echo htmlspecialchars($category7330); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7330); ?>" data-status="<?php echo htmlspecialchars($status7330); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7330); ?>;
    position:absolute; top:135px; left:1120px;'>
                        </div>

                        <!-- ASSET 7332 -->
                        <img src='../image.php?id=7332' style='width:20px; cursor:pointer; position:absolute; top:180px; left:1163px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7332' class="asset-image" onclick='fetchAssetData(7332);' data-id="<?php echo $assetId7332; ?>" data-room="<?php echo htmlspecialchars($room7332); ?>" data-floor="<?php echo htmlspecialchars($floor7332); ?>" data-image="<?php echo base64_encode($upload_img7332); ?>" data-category="<?php echo htmlspecialchars($category7332); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7332); ?>" data-status="<?php echo htmlspecialchars($status7332); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7332); ?>;
    position:absolute; top:180px; left:1173px;'>
                        </div>


                        <!-- ASSET 7333 -->
                        <img src='../image.php?id=7333' style='width:20px; cursor:pointer; position:absolute; top:180px; left:1055px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7333' class="asset-image" onclick='fetchAssetData(7333);' data-id="<?php echo $assetId7333; ?>" data-room="<?php echo htmlspecialchars($room7333); ?>" data-floor="<?php echo htmlspecialchars($floor7333); ?>" data-image="<?php echo base64_encode($upload_img7333); ?>" data-category="<?php echo htmlspecialchars($category7333); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7333); ?>" data-status="<?php echo htmlspecialchars($status7333); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7333); ?>;
    position:absolute; top:180px; left:1065px;'>
                        </div>

                        <!-- ASSET 7334 -->
                        <img src='../image.php?id=7334' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7334' class="asset-image" onclick='fetchAssetData(7334);' data-id="<?php echo $assetId7334; ?>" data-room="<?php echo htmlspecialchars($room7334); ?>" data-floor="<?php echo htmlspecialchars($floor7334); ?>" data-image="<?php echo base64_encode($upload_img7334); ?>" data-category="<?php echo htmlspecialchars($category7334); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7334); ?>" data-status="<?php echo htmlspecialchars($status7334); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7334); ?>;
    position:absolute; top:215px; left:1090px;'>
                        </div>

                        <!-- ASSET 7335 -->
                        <img src='../image.php?id=7335' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1135px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7335' class="asset-image" onclick='fetchAssetData(7335);' data-id="<?php echo $assetId7335; ?>" data-room="<?php echo htmlspecialchars($room7335); ?>" data-floor="<?php echo htmlspecialchars($floor7335); ?>" data-image="<?php echo base64_encode($upload_img7335); ?>" data-category="<?php echo htmlspecialchars($category7335); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7335); ?>" data-status="<?php echo htmlspecialchars($status7335); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7335); ?>;
    position:absolute; top:215px; left:1145px;'>
                        </div>

                        <!-- ASSET 7337 -->
                        <img src='../image.php?id=7337' style='width:20px; cursor:pointer; position:absolute; top:65px; left:925px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7337' class="asset-image" onclick='fetchAssetData(7337);' data-id="<?php echo $assetId7337; ?>" data-room="<?php echo htmlspecialchars($room7337); ?>" data-floor="<?php echo htmlspecialchars($floor7337); ?>" data-image="<?php echo base64_encode($upload_img7337); ?>" data-category="<?php echo htmlspecialchars($category7337); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7337); ?>" data-status="<?php echo htmlspecialchars($status7337); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7337); ?>;
    position:absolute; top:65px; left:935px;'>
                        </div>


                        <!-- ASSET 7338 -->
                        <img src='../image.php?id=7338' style='width:20px; cursor:pointer; position:absolute; top:65px; left:1030px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7338' class="asset-image" onclick='fetchAssetData(7338);' data-id="<?php echo $assetId7338; ?>" data-room="<?php echo htmlspecialchars($room7338); ?>" data-floor="<?php echo htmlspecialchars($floor7338); ?>" data-image="<?php echo base64_encode($upload_img7338); ?>" data-category="<?php echo htmlspecialchars($category7338); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7338); ?>" data-status="<?php echo htmlspecialchars($status7338); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7338); ?>;
    position:absolute; top:65px; left:1040px;'>
                        </div>

                        <!-- ASSET 7339 -->
                        <img src='../image.php?id=7339' style='width:20px; cursor:pointer; position:absolute; top:110px; left:978px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7339' class="asset-image" onclick='fetchAssetData(7339);' data-id="<?php echo $assetId7339; ?>" data-room="<?php echo htmlspecialchars($room7339); ?>" data-floor="<?php echo htmlspecialchars($floor7339); ?>" data-image="<?php echo base64_encode($upload_img7339); ?>" data-category="<?php echo htmlspecialchars($category7339); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7339); ?>" data-status="<?php echo htmlspecialchars($status7339); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7339); ?>;
    position:absolute; top:110px; left:988px;'>
                        </div>

                        <!-- ASSET 7340 -->
                        <img src='../image.php?id=7340' style='width:20px; cursor:pointer; position:absolute; top:140px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7340' class="asset-image" onclick='fetchAssetData(7340);' data-id="<?php echo $assetId7340; ?>" data-room="<?php echo htmlspecialchars($room7340); ?>" data-floor="<?php echo htmlspecialchars($floor7340); ?>" data-image="<?php echo base64_encode($upload_img7340); ?>" data-category="<?php echo htmlspecialchars($category7340); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7340); ?>" data-status="<?php echo htmlspecialchars($status7340); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7340); ?>;
    position:absolute; top:140px; left:930px;'>
                        </div>

                        <!-- ASSET 7341 -->
                        <img src='../image.php?id=7341' style='width:20px; cursor:pointer; position:absolute; top:140px; left:1035px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7341' class="asset-image" onclick='fetchAssetData(7341);' data-id="<?php echo $assetId7341; ?>" data-room="<?php echo htmlspecialchars($room7341); ?>" data-floor="<?php echo htmlspecialchars($floor7341); ?>" data-image="<?php echo base64_encode($upload_img7341); ?>" data-category="<?php echo htmlspecialchars($category7341); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7341); ?>" data-status="<?php echo htmlspecialchars($status7341); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7341); ?>;
    position:absolute; top:140px; left:1045px;'>
                        </div>


                        <!-- ASSET 7342 -->
                        <img src='../image.php?id=7342' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7342' class="asset-image" onclick='fetchAssetData(7342);' data-id="<?php echo $assetId7342; ?>" data-room="<?php echo htmlspecialchars($room7342); ?>" data-floor="<?php echo htmlspecialchars($floor7342); ?>" data-image="<?php echo base64_encode($upload_img7342); ?>" data-category="<?php echo htmlspecialchars($category7342); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7342); ?>" data-status="<?php echo htmlspecialchars($status7342); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7342); ?>;
    position:absolute; top:215px; left:1020px;'>
                        </div>

                        <!-- ASSET 7343 -->
                        <img src='../image.php?id=7343' style='width:20px; cursor:pointer; position:absolute; top:215px; left:945px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7343' class="asset-image" onclick='fetchAssetData(7343);' data-id="<?php echo $assetId7343; ?>" data-room="<?php echo htmlspecialchars($room7343); ?>" data-floor="<?php echo htmlspecialchars($floor7343); ?>" data-image="<?php echo base64_encode($upload_img7343); ?>" data-category="<?php echo htmlspecialchars($category7343); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7343); ?>" data-status="<?php echo htmlspecialchars($status7343); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7343); ?>;
    position:absolute; top:215px; left:955px;'>
                        </div>

                        <!-- ASSET 7346 -->
                        <img src='../image.php?id=7346' style='width:20px; cursor:pointer; position:absolute; top:65px; left:795px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7346' class="asset-image" onclick='fetchAssetData(7346);' data-id="<?php echo $assetId7346; ?>" data-room="<?php echo htmlspecialchars($room7346); ?>" data-floor="<?php echo htmlspecialchars($floor7346); ?>" data-image="<?php echo base64_encode($upload_img7346); ?>" data-category="<?php echo htmlspecialchars($category7346); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7346); ?>" data-status="<?php echo htmlspecialchars($status7346); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7346); ?>;
    position:absolute; top:65px; left:805px;'>
                        </div>

                        <!-- ASSET 7347 -->
                        <img src='../image.php?id=7347' style='width:20px; cursor:pointer; position:absolute; top:65px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7347' class="asset-image" onclick='fetchAssetData(7347);' data-id="<?php echo $assetId7347; ?>" data-room="<?php echo htmlspecialchars($room7347); ?>" data-floor="<?php echo htmlspecialchars($floor7347); ?>" data-image="<?php echo base64_encode($upload_img7347); ?>" data-category="<?php echo htmlspecialchars($category7347); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7347); ?>" data-status="<?php echo htmlspecialchars($status7347); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7347); ?>;
    position:absolute; top:65px; left:910px;'>
                        </div>

                        <!-- ASSET 7348 -->
                        <img src='../image.php?id=7348' style='width:20px; cursor:pointer; position:absolute; top:110px; left:848px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7348' class="asset-image" onclick='fetchAssetData(7348);' data-id="<?php echo $assetId7348; ?>" data-room="<?php echo htmlspecialchars($room7348); ?>" data-floor="<?php echo htmlspecialchars($floor7348); ?>" data-image="<?php echo base64_encode($upload_img7348); ?>" data-category="<?php echo htmlspecialchars($category7348); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7348); ?>" data-status="<?php echo htmlspecialchars($status7348); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7348); ?>;
    position:absolute; top:110px; left:858px;'>
                        </div>

                        <!-- ASSET 7349 -->
                        <img src='../image.php?id=7349' style='width:20px; cursor:pointer; position:absolute; top:140px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7349' class="asset-image" onclick='fetchAssetData(7349);' data-id="<?php echo $assetId7349; ?>" data-room="<?php echo htmlspecialchars($room7349); ?>" data-floor="<?php echo htmlspecialchars($floor7349); ?>" data-image="<?php echo base64_encode($upload_img7349); ?>" data-category="<?php echo htmlspecialchars($category7349); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7349); ?>" data-status="<?php echo htmlspecialchars($status7349); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7349); ?>;
    position:absolute; top:140px; left:800px;'>
                        </div>

                        <!-- ASSET 7350 -->
                        <img src='../image.php?id=7350' style='width:20px; cursor:pointer; position:absolute; top:140px; left:905px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7350' class="asset-image" onclick='fetchAssetData(7350);' data-id="<?php echo $assetId7350; ?>" data-room="<?php echo htmlspecialchars($room7350); ?>" data-floor="<?php echo htmlspecialchars($floor7350); ?>" data-image="<?php echo base64_encode($upload_img7350); ?>" data-category="<?php echo htmlspecialchars($category7350); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7350); ?>" data-status="<?php echo htmlspecialchars($status7350); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7350); ?>;
    position:absolute; top:140px; left:915px;'>
                        </div>

                        <!-- ASSET 7351 -->
                        <img src='../image.php?id=7351' style='width:20px; cursor:pointer; position:absolute; top:215px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7351' class="asset-image" onclick='fetchAssetData(7351);' data-id="<?php echo $assetId7351; ?>" data-room="<?php echo htmlspecialchars($room7351); ?>" data-floor="<?php echo htmlspecialchars($floor7351); ?>" data-image="<?php echo base64_encode($upload_img7351); ?>" data-category="<?php echo htmlspecialchars($category7351); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7351); ?>" data-status="<?php echo htmlspecialchars($status7351); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7351); ?>;
    position:absolute; top:215px; left:820px;'>
                        </div>

                        <!-- ASSET 7352 -->
                        <img src='../image.php?id=7352' style='width:20px; cursor:pointer; position:absolute; top:215px; left:880px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7352' class="asset-image" onclick='fetchAssetData(7352);' data-id="<?php echo $assetId7352; ?>" data-room="<?php echo htmlspecialchars($room7352); ?>" data-floor="<?php echo htmlspecialchars($floor7352); ?>" data-image="<?php echo base64_encode($upload_img7352); ?>" data-category="<?php echo htmlspecialchars($category7352); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7352); ?>" data-status="<?php echo htmlspecialchars($status7352); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7352); ?>;
    position:absolute; top:215px; left:890px;'>
                        </div>

                        <!-- END OF IB104A -->
                        <!-- START OF IB105A -->

                        <!-- ASSET 7358 -->
                        <img src='../image.php?id=7358' style='width:20px; cursor:pointer; position:absolute; top:65px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7358' class="asset-image" onclick='fetchAssetData(7358);' data-id="<?php echo $assetId7358; ?>" data-room="<?php echo htmlspecialchars($room7358); ?>" data-floor="<?php echo htmlspecialchars($floor7358); ?>" data-image="<?php echo base64_encode($upload_img7358); ?>" data-category="<?php echo htmlspecialchars($category7358); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7358); ?>" data-status="<?php echo htmlspecialchars($status7358); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7358); ?>;
    position:absolute; top:65px; left:675px;'>
                        </div>

                        <!-- ASSET 7359 -->
                        <img src='../image.php?id=7359' style='width:20px; cursor:pointer; position:absolute; top:65px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7359' class="asset-image" onclick='fetchAssetData(7359);' data-id="<?php echo $assetId7359; ?>" data-room="<?php echo htmlspecialchars($room7359); ?>" data-floor="<?php echo htmlspecialchars($floor7359); ?>" data-image="<?php echo base64_encode($upload_img7359); ?>" data-category="<?php echo htmlspecialchars($category7359); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7359); ?>" data-status="<?php echo htmlspecialchars($status7359); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7359); ?>;
    position:absolute; top:65px; left:780px;'>
                        </div>

                        <!-- ASSET 7355 -->
                        <img src='../image.php?id=7355' style='width:20px; cursor:pointer; position:absolute; top:110px; left:718px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7355' class="asset-image" onclick='fetchAssetData(7355);' data-id="<?php echo $assetId7355; ?>" data-room="<?php echo htmlspecialchars($room7355); ?>" data-floor="<?php echo htmlspecialchars($floor7355); ?>" data-image="<?php echo base64_encode($upload_img7355); ?>" data-category="<?php echo htmlspecialchars($category7355); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7355); ?>" data-status="<?php echo htmlspecialchars($status7355); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7355); ?>;
    position:absolute; top:110px; left:728px;'>
                        </div>


                        <!-- ASSET 7356 -->
                        <img src='../image.php?id=7356' style='width:20px; cursor:pointer; position:absolute; top:140px; left:660px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7356' class="asset-image" onclick='fetchAssetData(7356);' data-id="<?php echo $assetId7356; ?>" data-room="<?php echo htmlspecialchars($room7356); ?>" data-floor="<?php echo htmlspecialchars($floor7356); ?>" data-image="<?php echo base64_encode($upload_img7356); ?>" data-category="<?php echo htmlspecialchars($category7356); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7356); ?>" data-status="<?php echo htmlspecialchars($status7356); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7356); ?>;
    position:absolute; top:140px; left:670px;'>
                        </div>

                        <!-- ASSET 7357 -->
                        <img src='../image.php?id=7357' style='width:20px; cursor:pointer; position:absolute; top:140px; left:775px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7357' class="asset-image" onclick='fetchAssetData(7357);' data-id="<?php echo $assetId7357; ?>" data-room="<?php echo htmlspecialchars($room7357); ?>" data-floor="<?php echo htmlspecialchars($floor7357); ?>" data-image="<?php echo base64_encode($upload_img7357); ?>" data-category="<?php echo htmlspecialchars($category7357); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7357); ?>" data-status="<?php echo htmlspecialchars($status7357); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7357); ?>;
    position:absolute; top:140px; left:785px;'>
                        </div>

                        <!-- ASSET 7360 -->
                        <img src='../image.php?id=7360' style='width:20px; cursor:pointer; position:absolute; top:215px; left:685px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7360' class="asset-image" onclick='fetchAssetData(7360);' data-id="<?php echo $assetId7360; ?>" data-room="<?php echo htmlspecialchars($room7360); ?>" data-floor="<?php echo htmlspecialchars($floor7360); ?>" data-image="<?php echo base64_encode($upload_img7360); ?>" data-category="<?php echo htmlspecialchars($category7360); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7360); ?>" data-status="<?php echo htmlspecialchars($status7360); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7360); ?>;
    position:absolute; top:215px; left:695px;'>
                        </div>

                        <!-- ASSET 7361 -->
                        <img src='../image.php?id=7361' style='width:20px; cursor:pointer; position:absolute; top:215px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7361' class="asset-image" onclick='fetchAssetData(7361);' data-id="<?php echo $assetId7361; ?>" data-room="<?php echo htmlspecialchars($room7361); ?>" data-floor="<?php echo htmlspecialchars($floor7361); ?>" data-image="<?php echo base64_encode($upload_img7361); ?>" data-category="<?php echo htmlspecialchars($category7361); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7361); ?>" data-status="<?php echo htmlspecialchars($status7361); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7361); ?>;
    position:absolute; top:215px; left:760px;'>
                        </div>


                        <!-- END OF IB105A -->

                        <!-- START OF IB106A -->

                        <!-- ASSET 7364 -->
                        <img src='../image.php?id=7364' style='width:20px; cursor:pointer; position:absolute; top:65px; left:440px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7364' class="asset-image" onclick='fetchAssetData(7364);' data-id="<?php echo $assetId7364; ?>" data-room="<?php echo htmlspecialchars($room7364); ?>" data-floor="<?php echo htmlspecialchars($floor7364); ?>" data-image="<?php echo base64_encode($upload_img7364); ?>" data-category="<?php echo htmlspecialchars($category7364); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7364); ?>" data-status="<?php echo htmlspecialchars($status7364); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7364); ?>;
    position:absolute; top:65px; left:450px;'>
                        </div>

                        <!-- ASSET 7365 -->
                        <img src='../image.php?id=7365' style='width:20px; cursor:pointer; position:absolute; top:65px; left:545px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7365' class="asset-image" onclick='fetchAssetData(7365);' data-id="<?php echo $assetId7365; ?>" data-room="<?php echo htmlspecialchars($room7365); ?>" data-floor="<?php echo htmlspecialchars($floor7365); ?>" data-image="<?php echo base64_encode($upload_img7365); ?>" data-category="<?php echo htmlspecialchars($category7365); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7365); ?>" data-status="<?php echo htmlspecialchars($status7365); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7365); ?>;
    position:absolute; top:65px; left:555px;'>
                        </div>

                        <!-- ASSET 7366 -->
                        <img src='../image.php?id=7366' style='width:20px; cursor:pointer; position:absolute; top:110px; left:493px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7366' class="asset-image" onclick='fetchAssetData(7366);' data-id="<?php echo $assetId7366; ?>" data-room="<?php echo htmlspecialchars($room7366); ?>" data-floor="<?php echo htmlspecialchars($floor7366); ?>" data-image="<?php echo base64_encode($upload_img7366); ?>" data-category="<?php echo htmlspecialchars($category7366); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7366); ?>" data-status="<?php echo htmlspecialchars($status7366); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7366); ?>;
    position:absolute; top:110px; left:503px;'>
                        </div>

                        <!-- ASSET 7367 -->
                        <img src='../image.php?id=7367' style='width:20px; cursor:pointer; position:absolute; top:140px; left:435px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7367' class="asset-image" onclick='fetchAssetData(7367);' data-id="<?php echo $assetId7367; ?>" data-room="<?php echo htmlspecialchars($room7367); ?>" data-floor="<?php echo htmlspecialchars($floor7367); ?>" data-image="<?php echo base64_encode($upload_img7367); ?>" data-category="<?php echo htmlspecialchars($category7367); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7367); ?>" data-status="<?php echo htmlspecialchars($status7367); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7367); ?>;
    position:absolute; top:140px; left:445px;'>
                        </div>

                        <!-- ASSET 7368 -->
                        <img src='../image.php?id=7368' style='width:20px; cursor:pointer; position:absolute; top:140px; left:550px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7368' class="asset-image" onclick='fetchAssetData(7368);' data-id="<?php echo $assetId7368; ?>" data-room="<?php echo htmlspecialchars($room7368); ?>" data-floor="<?php echo htmlspecialchars($floor7368); ?>" data-image="<?php echo base64_encode($upload_img7368); ?>" data-category="<?php echo htmlspecialchars($category7368); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7368); ?>" data-status="<?php echo htmlspecialchars($status7368); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7368); ?>;
    position:absolute; top:140px; left:560px;'>
                        </div>

                        <!-- ASSET 7369 -->
                        <img src='../image.php?id=7369' style='width:20px; cursor:pointer; position:absolute; top:215px; left:460px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7369' class="asset-image" onclick='fetchAssetData(7369);' data-id="<?php echo $assetId7369; ?>" data-room="<?php echo htmlspecialchars($room7369); ?>" data-floor="<?php echo htmlspecialchars($floor7369); ?>" data-image="<?php echo base64_encode($upload_img7369); ?>" data-category="<?php echo htmlspecialchars($category7369); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7369); ?>" data-status="<?php echo htmlspecialchars($status7369); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7369); ?>;
    position:absolute; top:215px; left:470px;'>
                        </div>

                        <!-- ASSET 7370 -->
                        <img src='../image.php?id=7370' style='width:20px; cursor:pointer; position:absolute; top:215px; left:525px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7370' class="asset-image" onclick='fetchAssetData(7370);' data-id="<?php echo $assetId7370; ?>" data-room="<?php echo htmlspecialchars($room7370); ?>" data-floor="<?php echo htmlspecialchars($floor7370); ?>" data-image="<?php echo base64_encode($upload_img7370); ?>" data-category="<?php echo htmlspecialchars($category7370); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7370); ?>" data-status="<?php echo htmlspecialchars($status7370); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7370); ?>;
    position:absolute; top:215px; left:535px;'>
                        </div>

                        <!-- ASSET 7373 -->
                        <img src='../image.php?id=7373' style='width:20px; cursor:pointer; position:absolute; top:65px; left:310px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7373' class="asset-image" onclick='fetchAssetData(7373);' data-id="<?php echo $assetId7373; ?>" data-room="<?php echo htmlspecialchars($room7373); ?>" data-floor="<?php echo htmlspecialchars($floor7373); ?>" data-image="<?php echo base64_encode($upload_img7373); ?>" data-category="<?php echo htmlspecialchars($category7373); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7373); ?>" data-status="<?php echo htmlspecialchars($status7373); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7373); ?>;
    position:absolute; top:65px; left:320px;'>
                        </div>


                        <!-- ASSET 7374 -->
                        <img src='../image.php?id=7374' style='width:20px; cursor:pointer; position:absolute; top:65px; left:415px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7374' class="asset-image" onclick='fetchAssetData(7374);' data-id="<?php echo $assetId7374; ?>" data-room="<?php echo htmlspecialchars($room7374); ?>" data-floor="<?php echo htmlspecialchars($floor7374); ?>" data-image="<?php echo base64_encode($upload_img7374); ?>" data-category="<?php echo htmlspecialchars($category7374); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7374); ?>" data-status="<?php echo htmlspecialchars($status7374); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7374); ?>;
    position:absolute; top:65px; left:425px;'>
                        </div>

                        <!-- ASSET 7375 -->
                        <img src='../image.php?id=7375' style='width:20px; cursor:pointer; position:absolute; top:110px; left:363px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7375' class="asset-image" onclick='fetchAssetData(7375);' data-id="<?php echo $assetId7375; ?>" data-room="<?php echo htmlspecialchars($room7375); ?>" data-floor="<?php echo htmlspecialchars($floor7375); ?>" data-image="<?php echo base64_encode($upload_img7375); ?>" data-category="<?php echo htmlspecialchars($category7375); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7375); ?>" data-status="<?php echo htmlspecialchars($status7375); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7375); ?>;
    position:absolute; top:110px; left:373px;'>
                        </div>

                        <!-- ASSET 7376 -->
                        <img src='../image.php?id=7376' style='width:20px; cursor:pointer; position:absolute; top:140px; left:305px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7376' class="asset-image" onclick='fetchAssetData(7376);' data-id="<?php echo $assetId7376; ?>" data-room="<?php echo htmlspecialchars($room7376); ?>" data-floor="<?php echo htmlspecialchars($floor7376); ?>" data-image="<?php echo base64_encode($upload_img7376); ?>" data-category="<?php echo htmlspecialchars($category7376); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7376); ?>" data-status="<?php echo htmlspecialchars($status7376); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7376); ?>;
    position:absolute; top:140px; left:315px;'>
                        </div>

                        <!-- ASSET 7377 -->
                        <img src='../image.php?id=7377' style='width:20px; cursor:pointer; position:absolute; top:140px; left:420px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7377' class="asset-image" onclick='fetchAssetData(7377);' data-id="<?php echo $assetId7377; ?>" data-room="<?php echo htmlspecialchars($room7377); ?>" data-floor="<?php echo htmlspecialchars($floor7377); ?>" data-image="<?php echo base64_encode($upload_img7377); ?>" data-category="<?php echo htmlspecialchars($category7377); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7377); ?>" data-status="<?php echo htmlspecialchars($status7377); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7377); ?>;
    position:absolute; top:140px; left:430px;'>
                        </div>


                        <!-- ASSET 7378 -->
                        <img src='../image.php?id=7378' style='width:20px; cursor:pointer; position:absolute; top:210px; left:330px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7378' class="asset-image" onclick='fetchAssetData(7378);' data-id="<?php echo $assetId7378; ?>" data-room="<?php echo htmlspecialchars($room7378); ?>" data-floor="<?php echo htmlspecialchars($floor7378); ?>" data-image="<?php echo base64_encode($upload_img7378); ?>" data-category="<?php echo htmlspecialchars($category7378); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7378); ?>" data-status="<?php echo htmlspecialchars($status7378); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7378); ?>;
    position:absolute; top:210px; left:340px;'>
                        </div>

                        <!-- ASSET 7379 -->
                        <img src='../image.php?id=7379' style='width:20px; cursor:pointer; position:absolute; top:210px; left:400px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7379' class="asset-image" onclick='fetchAssetData(7379);' data-id="<?php echo $assetId7379; ?>" data-room="<?php echo htmlspecialchars($room7379); ?>" data-floor="<?php echo htmlspecialchars($floor7379); ?>" data-image="<?php echo base64_encode($upload_img7379); ?>" data-category="<?php echo htmlspecialchars($category7379); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7379); ?>" data-status="<?php echo htmlspecialchars($status7379); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7379); ?>;
    position:absolute; top:210px; left:410px;'>
                        </div>

                        <!-- ASSET 7382 -->
                        <img src='../image.php?id=7382' style='width:20px; cursor:pointer; position:absolute; top:65px; left:180px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7382' class="asset-image" onclick='fetchAssetData(7382);' data-id="<?php echo $assetId7382; ?>" data-room="<?php echo htmlspecialchars($room7382); ?>" data-floor="<?php echo htmlspecialchars($floor7382); ?>" data-image="<?php echo base64_encode($upload_img7382); ?>" data-category="<?php echo htmlspecialchars($category7382); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7382); ?>" data-status="<?php echo htmlspecialchars($status7382); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7382); ?>;
    position:absolute; top:65px; left:190px;'>
                        </div>

                        <!-- ASSET 7383 -->
                        <img src='../image.php?id=7383' style='width:20px; cursor:pointer; position:absolute; top:65px; left:285px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7383' class="asset-image" onclick='fetchAssetData(7383);' data-id="<?php echo $assetId7383; ?>" data-room="<?php echo htmlspecialchars($room7383); ?>" data-floor="<?php echo htmlspecialchars($floor7383); ?>" data-image="<?php echo base64_encode($upload_img7383); ?>" data-category="<?php echo htmlspecialchars($category7383); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7383); ?>" data-status="<?php echo htmlspecialchars($status7383); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7383); ?>;
    position:absolute; top:65px; left:295px;'>
                        </div>

                        <!-- ASSET 7384 -->
                        <img src='../image.php?id=7384' style='width:20px; cursor:pointer; position:absolute; top:110px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7384' class="asset-image" onclick='fetchAssetData(7384);' data-id="<?php echo $assetId7384; ?>" data-room="<?php echo htmlspecialchars($room7384); ?>" data-floor="<?php echo htmlspecialchars($floor7384); ?>" data-image=" <?php echo base64_encode($upload_img7384); ?>" data-category="
                    <?php echo htmlspecialchars($category7384); ?>" data-assignedname="
                    <?php echo htmlspecialchars($assignedName7384); ?>" data-status="
                    <?php echo htmlspecialchars($status7384); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7384); ?>;
    position:absolute; top:110px; left:243px;'>
                        </div>

                        <!-- ASSET 7385 -->
                        <img src='../image.php?id=7385' style='width:20px; cursor:pointer; position:absolute; top:140px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7385' class="asset-image" onclick='fetchAssetData(7385);' data-id="<?php echo $assetId7385; ?>" data-room="<?php echo htmlspecialchars($room7385); ?>" data-floor="<?php echo htmlspecialchars($floor7385); ?>" data-image="<?php echo base64_encode($upload_img7385); ?>" data-category="<?php echo htmlspecialchars($category7385); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7385); ?>" data-status="<?php echo htmlspecialchars($status7385); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7385); ?>;
    position:absolute; top:140px; left:185px;'>
                        </div>

                        <!-- ASSET 7386 -->
                        <img src='../image.php?id=7386' style='width:20px; cursor:pointer; position:absolute; top:140px; left:290px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7386' class="asset-image" onclick='fetchAssetData(7386);' data-id="<?php echo $assetId7386; ?>" data-room="<?php echo htmlspecialchars($room7386); ?>" data-floor="<?php echo htmlspecialchars($floor7386); ?>" data-image="<?php echo base64_encode($upload_img7386); ?>" data-category="<?php echo htmlspecialchars($category7386); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7386); ?>" data-status="<?php echo htmlspecialchars($status7386); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7386); ?>;
                    position:absolute; top:140px; left:300px;'>
                        </div>

                        <!-- ASSET 7387 -->
                        <img src='../image.php?id=7387' style='width:20px; cursor:pointer; position:absolute; top:210px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7387' class="asset-image" onclick='fetchAssetData(7387);' data-id="<?php echo $assetId7387; ?>" data-room="<?php echo htmlspecialchars($room7387); ?>" data-floor="<?php echo htmlspecialchars($floor7387); ?>" data-image="<?php echo base64_encode($upload_img7387); ?>" data-category="<?php echo htmlspecialchars($category7387); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7387); ?>" data-status="<?php echo htmlspecialchars($status7387); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7387); ?>;
    position:absolute; top:210px; left:210px;'>
                        </div>


                        <!-- ASSET 7388 -->
                        <img src='../image.php?id=7388' style='width:20px; cursor:pointer; position:absolute; top:210px; left:265px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7388' class="asset-image" onclick='fetchAssetData(7388);' data-id="<?php echo $assetId7388; ?>" data-room="<?php echo htmlspecialchars($room7388); ?>" data-floor="<?php echo htmlspecialchars($floor7388); ?>" data-image="<?php echo base64_encode($upload_img7388); ?>" data-category="<?php echo htmlspecialchars($category7388); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7388); ?>" data-status="<?php echo htmlspecialchars($status7388); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7388); ?>;
    position:absolute; top:210px; left:275px;'>
                        </div>

                        <!-- ASSET 7391 -->
                        <img src='../image.php?id=7391' style='width:20px; cursor:pointer; position:absolute; top:90px; left:50px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7391' class="asset-image" onclick='fetchAssetData(7391);' data-id="<?php echo $assetId7391; ?>" data-room="<?php echo htmlspecialchars($room7391); ?>" data-floor="<?php echo htmlspecialchars($floor7391); ?>" data-image="<?php echo base64_encode($upload_img7391); ?>" data-category="<?php echo htmlspecialchars($category7391); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7391); ?>" data-status="<?php echo htmlspecialchars($status7391); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7391); ?>;
    position:absolute; top:90px; left:60px;'>
                        </div>

                        <!-- ASSET 7392 -->
                        <img src='../image.php?id=7392' style='width:20px; cursor:pointer; position:absolute; top:90px; left:158px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7392' class="asset-image" onclick='fetchAssetData(7392);' data-id="<?php echo $assetId7392; ?>" data-room="<?php echo htmlspecialchars($room7392); ?>" data-floor="<?php echo htmlspecialchars($floor7392); ?>" data-image="<?php echo base64_encode($upload_img7392); ?>" data-category="<?php echo htmlspecialchars($category7392); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7392); ?>" data-status="<?php echo htmlspecialchars($status7392); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7392); ?>;
    position:absolute; top:90px; left:168px;'>
                        </div>

                        <!-- ASSET 7393 -->
                        <img src='../image.php?id=7393' style='width:20px; cursor:pointer; position:absolute; top:130px; left:103px;' alt='Asset Image' data-bs-toggle=' modal' data-bs-target='#imageModal7393' class="asset-image" onclick='fetchAssetData(7393);' data-id="<?php echo $assetId7393; ?>" data-room="<?php echo htmlspecialchars($room7393); ?>" data-floor="<?php echo htmlspecialchars($floor7393); ?>" data-image="<?php echo base64_encode($upload_img7393); ?>" data-category="<?php echo htmlspecialchars($category7393); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7393); ?>" data-status="<?php echo htmlspecialchars($status7393); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7393); ?>;
    position:absolute; top:130px; left:113px;'>
                        </div>


                        <!-- ASSET 7394 -->
                        <img src='../image.php?id=7394' style='width:20px; cursor:pointer; position:absolute; top:160px; left:45px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7394' class="asset-image" onclick='fetchAssetData(7394);' data-id="<?php echo $assetId7394; ?>" data-room="<?php echo htmlspecialchars($room7394); ?>" data-floor="<?php echo htmlspecialchars($floor7394); ?>" data-image="<?php echo base64_encode($upload_img7394); ?>" data-category="<?php echo htmlspecialchars($category7394); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7394); ?>" data-status="<?php echo htmlspecialchars($status7394); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7394); ?>;
    position:absolute; top:160px; left:55px;'>
                        </div>

                        <!-- ASSET 7395 -->
                        <img src='../image.php?id=7395' style='width:20px; cursor:pointer; position:absolute; top:160px; left:158px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7395' class="asset-image" onclick='fetchAssetData(7395);' data-id="<?php echo $assetId7395; ?>" data-room="<?php echo htmlspecialchars($room7395); ?>" data-floor="<?php echo htmlspecialchars($floor7395); ?>" data-image="<?php echo base64_encode($upload_img7395); ?>" data-category="<?php echo htmlspecialchars($category7395); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7395); ?>" data-status="<?php echo htmlspecialchars($status7395); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7395); ?>;
    position:absolute; top:160px; left:168px;'>
                        </div>

                        <!-- ASSET 7396 -->
                        <img src='../image.php?id=7396' style='width:20px; cursor:pointer; position:absolute; top:210px; left:70px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7396' class="asset-image" onclick='fetchAssetData(7396);' data-id="<?php echo $assetId7396; ?>" data-room="<?php echo htmlspecialchars($room7396); ?>" data-floor="<?php echo htmlspecialchars($floor7396); ?>" data-image="<?php echo base64_encode($upload_img7396); ?>" data-category="<?php echo htmlspecialchars($category7396); ?>" data-assignedname="
                <?php echo htmlspecialchars($assignedName7396); ?>" data-status="
                <?php echo htmlspecialchars($status7396); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7396); ?>;
    position:absolute; top:210px; left:80px;'>
                        </div>

                        <!-- ASSET 7397 -->
                        <img src='../image.php?id=7397' style='width:20px; cursor:pointer; position:absolute; top:210px; left:140px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal7397' class="asset-image" onclick='fetchAssetData(7397);' data-id="<?php echo $assetId7397; ?>" data-room="<?php echo htmlspecialchars($room7397); ?>" data-floor="<?php echo htmlspecialchars($floor7397); ?>" data-image="<?php echo base64_encode($upload_img7397); ?>" data-category="<?php echo htmlspecialchars($category7397); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName7397); ?>" data-status="<?php echo htmlspecialchars($status7397); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status7397); ?>;
    position:absolute; top:210px; left:150px;'>
                        </div>


                        <!-- ASSET 9003 -->
                        <img src='../image.php?id=9003' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1150px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9003' onclick='fetchAssetData(9003);' data-id="<?php echo $assetId9003; ?>" data-room="<?php echo htmlspecialchars($room9003); ?>" data-floor="<?php echo htmlspecialchars($floor9003); ?>" data-image="<?php echo base64_encode($upload_img9003); ?>" data-category="<?php echo htmlspecialchars($category9003); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9003); ?>" data-status="<?php echo htmlspecialchars($status9003); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9003); ?>; 
position:absolute; top:497px; left:1162px;'>
                        </div>

                        <!-- ASSET 9004 -->
                        <img src='../image.php?id=9004' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9004' onclick='fetchAssetData(9004);' data-id="<?php echo $assetId9004; ?>" data-room="<?php echo htmlspecialchars($room9004); ?>" data-floor="<?php echo htmlspecialchars($floor9004); ?>" data-image="<?php echo base64_encode($upload_img9004); ?>" data-category="<?php echo htmlspecialchars($category9004); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9004); ?>" data-status="<?php echo htmlspecialchars($status9004); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9004); ?>; 
position:absolute; top:418px; left:1139px;'>
                        </div>

                        <!-- ASSET 9005 -->
                        <img src='../image.php?id=9005' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9005' onclick='fetchAssetData(9005);' data-id="<?php echo $assetId9005; ?>" data-room="<?php echo htmlspecialchars($room9005); ?>" data-floor="<?php echo htmlspecialchars($floor9005); ?>" data-image="<?php echo base64_encode($upload_img9005); ?>" data-category="<?php echo htmlspecialchars($category9005); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9005); ?>" data-status="<?php echo htmlspecialchars($status9005); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9005); ?>; 
position:absolute; top:438px; left:1139px;'>
                        </div>

                        <!-- ASSET 9006 -->
                        <img src='../image.php?id=9006' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9006' onclick='fetchAssetData(9006);' data-id="<?php echo $assetId9006; ?>" data-room="<?php echo htmlspecialchars($room9006); ?>" data-floor="<?php echo htmlspecialchars($floor9006); ?>" data-image="<?php echo base64_encode($upload_img9006); ?>" data-category="<?php echo htmlspecialchars($category9006); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9006); ?>" data-status="<?php echo htmlspecialchars($status9006); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9006); ?>; 
position:absolute; top:457px; left:1139px;'>
                        </div>


                        <!-- ASSET 9007 -->
                        <img src='../image.php?id=9007' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9007' onclick='fetchAssetData(9007);' data-id="<?php echo $assetId9007; ?>" data-room="<?php echo htmlspecialchars($room9007); ?>" data-floor="<?php echo htmlspecialchars($floor9007); ?>" data-image="<?php echo base64_encode($upload_img9007); ?>" data-category="<?php echo htmlspecialchars($category9007); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9007); ?>" data-status="<?php echo htmlspecialchars($status9007); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9007); ?>; 
position:absolute; top:477px; left:1139px;'>
                        </div>

                        <!-- ASSET 9008 -->
                        <img src='../image.php?id=9008' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1129px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9008' onclick='fetchAssetData(9008);' data-id="<?php echo $assetId9008; ?>" data-room="<?php echo htmlspecialchars($room9008); ?>" data-floor="<?php echo htmlspecialchars($floor9008); ?>" data-image="<?php echo base64_encode($upload_img9008); ?>" data-category="<?php echo htmlspecialchars($category9008); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9008); ?>" data-status="<?php echo htmlspecialchars($status9008); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9008); ?>; 
position:absolute; top:497px; left:1139px;'>
                        </div>

                        <!-- ASSET 9009 -->
                        <img src='../image.php?id=9009' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9009' onclick='fetchAssetData(9009);' data-id="<?php echo $assetId9009; ?>" data-room="<?php echo htmlspecialchars($room9009); ?>" data-floor="<?php echo htmlspecialchars($floor9009); ?>" data-image="<?php echo base64_encode($upload_img9009); ?>" data-category="<?php echo htmlspecialchars($category9009); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9009); ?>" data-status="<?php echo htmlspecialchars($status9009); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9009); ?>; 
position:absolute; top:418px; left:1118px;'>
                        </div>

                        <!-- ASSET 9010 -->
                        <img src='../image.php?id=9010' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9010' onclick='fetchAssetData(9010);' data-id="<?php echo $assetId9010; ?>" data-room="<?php echo htmlspecialchars($room9010); ?>" data-floor="<?php echo htmlspecialchars($floor9010); ?>" data-image="<?php echo base64_encode($upload_img9010); ?>" data-category="<?php echo htmlspecialchars($category9010); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9010); ?>" data-status="<?php echo htmlspecialchars($status9010); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9010); ?>; 
position:absolute; top:438px; left:1118px;'>
                        </div>


                        <!-- ASSET 9011 -->
                        <img src='../image.php?id=9011' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9011' onclick='fetchAssetData(9011);' data-id="<?php echo $assetId9011; ?>" data-room="<?php echo htmlspecialchars($room9011); ?>" data-floor="<?php echo htmlspecialchars($floor9011); ?>" data-image="<?php echo base64_encode($upload_img9011); ?>" data-category="<?php echo htmlspecialchars($category9011); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9011); ?>" data-status="<?php echo htmlspecialchars($status9011); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9011); ?>; 
position:absolute; top:457px; left:1118px;'>
                        </div>

                        <!-- ASSET 9012 -->
                        <img src='../image.php?id=9012' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9012' onclick='fetchAssetData(9012);' data-id="<?php echo $assetId9012; ?>" data-room="<?php echo htmlspecialchars($room9012); ?>" data-floor="<?php echo htmlspecialchars($floor9012); ?>" data-image="<?php echo base64_encode($upload_img9012); ?>" data-category="<?php echo htmlspecialchars($category9012); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9012); ?>" data-status="<?php echo htmlspecialchars($status9012); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9012); ?>; 
position:absolute; top:477px; left:1118px;'>
                        </div>

                        <!-- ASSET 9013 -->
                        <img src='../image.php?id=9013' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1109px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9013' onclick='fetchAssetData(9013);' data-id="<?php echo $assetId9013; ?>" data-room="<?php echo htmlspecialchars($room9013); ?>" data-floor="<?php echo htmlspecialchars($floor9013); ?>" data-image="<?php echo base64_encode($upload_img9013); ?>" data-category="<?php echo htmlspecialchars($category9013); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9013); ?>" data-status="<?php echo htmlspecialchars($status9013); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9013); ?>; 
position:absolute; top:497px; left:1118px;'>
                        </div>

                        <!-- ASSET 9014 -->
                        <img src='../image.php?id=9014' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9014' onclick='fetchAssetData(9014);' data-id="<?php echo $assetId9014; ?>" data-room="<?php echo htmlspecialchars($room9014); ?>" data-floor="<?php echo htmlspecialchars($floor9014); ?>" data-image="<?php echo base64_encode($upload_img9014); ?>" data-category="<?php echo htmlspecialchars($category9014); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9014); ?>" data-status="<?php echo htmlspecialchars($status9014); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9014); ?>; 
position:absolute; top:418px; left:1098px;'>
                        </div>


                        <!-- ASSET 9015 -->
                        <img src='../image.php?id=9015' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9015' onclick='fetchAssetData(9015);' data-id="<?php echo $assetId9015; ?>" data-room="<?php echo htmlspecialchars($room9015); ?>" data-floor="<?php echo htmlspecialchars($floor9015); ?>" data-image="<?php echo base64_encode($upload_img9015); ?>" data-category="<?php echo htmlspecialchars($category9015); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9015); ?>" data-status="<?php echo htmlspecialchars($status9015); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9015); ?>; 
position:absolute; top:438px; left:1098px;'>
                        </div>

                        <!-- ASSET 9016 -->
                        <img src='../image.php?id=9016' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9016' onclick='fetchAssetData(9016);' data-id="<?php echo $assetId9016; ?>" data-room="<?php echo htmlspecialchars($room9016); ?>" data-floor="<?php echo htmlspecialchars($floor9016); ?>" data-image="<?php echo base64_encode($upload_img9016); ?>" data-category="<?php echo htmlspecialchars($category9016); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9016); ?>" data-status="<?php echo htmlspecialchars($status9016); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9016); ?>; 
position:absolute; top:457px; left:1098px;'>
                        </div>

                        <!-- ASSET 9017 -->
                        <img src='../image.php?id=9017' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9017' onclick='fetchAssetData(9017);' data-id="<?php echo $assetId9017; ?>" data-room="<?php echo htmlspecialchars($room9017); ?>" data-floor="<?php echo htmlspecialchars($floor9017); ?>" data-image="<?php echo base64_encode($upload_img9017); ?>" data-category="<?php echo htmlspecialchars($category9017); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9017); ?>" data-status="<?php echo htmlspecialchars($status9017); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9017); ?>; 
position:absolute; top:477px; left:1098px;'>
                        </div>

                        <!-- ASSET 9018 -->
                        <img src='../image.php?id=9018' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1088px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9018' onclick='fetchAssetData(9018);' data-id="<?php echo $assetId9018; ?>" data-room="<?php echo htmlspecialchars($room9018); ?>" data-floor="<?php echo htmlspecialchars($floor9018); ?>" data-image="<?php echo base64_encode($upload_img9018); ?>" data-category="<?php echo htmlspecialchars($category9018); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9018); ?>" data-status="<?php echo htmlspecialchars($status9018); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9018); ?>; 
position:absolute; top:497px; left:1098px;'>
                        </div>


                        <!-- END OF CHAIR 51 TO 60 -->

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 9019 -->
                        <img src='../image.php?id=9019' style='width:18px; cursor:pointer; position:absolute; top:405px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9019' onclick='fetchAssetData(9019);' data-id="<?php echo $assetId9019; ?>" data-room="<?php echo htmlspecialchars($room9019); ?>" data-floor="<?php echo htmlspecialchars($floor9019); ?>" data-image="<?php echo base64_encode($upload_img9019); ?>" data-category="<?php echo htmlspecialchars($category9019); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9019); ?>" data-status="<?php echo htmlspecialchars($status9019); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9019); ?>; 
position:absolute; top:418px; left:1078px;'>
                        </div>

                        <!-- ASSET 9020 -->
                        <img src='../image.php?id=9020' style='width:18px; cursor:pointer; position:absolute; top:425px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9020' onclick='fetchAssetData(9020);' data-id="<?php echo $assetId9020; ?>" data-room="<?php echo htmlspecialchars($room9020); ?>" data-floor="<?php echo htmlspecialchars($floor9020); ?>" data-image="<?php echo base64_encode($upload_img9020); ?>" data-category="<?php echo htmlspecialchars($category9020); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9020); ?>" data-status="<?php echo htmlspecialchars($status9020); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9020); ?>; 
position:absolute; top:438px; left:1078px;'>
                        </div>

                        <!-- ASSET 9021 -->
                        <img src='../image.php?id=9021' style='width:18px; cursor:pointer; position:absolute; top:445px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9021' onclick='fetchAssetData(9021);' data-id="<?php echo $assetId9021; ?>" data-room="<?php echo htmlspecialchars($room9021); ?>" data-floor="<?php echo htmlspecialchars($floor9021); ?>" data-image="<?php echo base64_encode($upload_img9021); ?>" data-category="<?php echo htmlspecialchars($category9021); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9021); ?>" data-status="<?php echo htmlspecialchars($status9021); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9021); ?>; 
position:absolute; top:457px; left:1078px;'>
                        </div>

                        <!-- ASSET 9022 -->
                        <img src='../image.php?id=9022' style='width:18px; cursor:pointer; position:absolute; top:465px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9022' onclick='fetchAssetData(9022);' data-id="<?php echo $assetId9022; ?>" data-room="<?php echo htmlspecialchars($room9022); ?>" data-floor="<?php echo htmlspecialchars($floor9022); ?>" data-image="<?php echo base64_encode($upload_img9022); ?>" data-category="<?php echo htmlspecialchars($category9022); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9022); ?>" data-status="<?php echo htmlspecialchars($status9022); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9022); ?>; 
position:absolute; top:477px; left:1078px;'>
                        </div>


                        <!-- ASSET 9023 -->
                        <img src='../image.php?id=9023' style='width:18px; cursor:pointer; position:absolute; top:485px; left:1067px; transform: rotate(91deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9023' onclick='fetchAssetData(9023);' data-id="<?php echo $assetId9023; ?>" data-room="<?php echo htmlspecialchars($room9023); ?>" data-floor="<?php echo htmlspecialchars($floor9023); ?>" data-image="<?php echo base64_encode($upload_img9023); ?>" data-category="<?php echo htmlspecialchars($category9023); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9023); ?>" data-status="<?php echo htmlspecialchars($status9023); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9023); ?>; 
position:absolute; top:497px; left:1078px;'>
                        </div>

                        <!-- ASSET 9024 -->
                        <img src='../image.php?id=9024' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9024' onclick='fetchAssetData(9024);' class="asset-image" data-id="<?php echo $assetId9024; ?>" data-room="<?php echo htmlspecialchars($room9024); ?>" data-floor="<?php echo htmlspecialchars($floor9024); ?>" data-image="<?php echo base64_encode($upload_img9024); ?>" data-category="<?php echo htmlspecialchars($category9024); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9024); ?>" data-status="<?php echo htmlspecialchars($status9024); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9024); ?>; 
position:absolute; top:100px; left:1085px;'>
                        </div>

                        <!-- ASSET 9025 -->
                        <img src='../image.php?id=9025' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9025' onclick='fetchAssetData(9025);' class="asset-image" data-id="<?php echo $assetId9025; ?>" data-room="<?php echo htmlspecialchars($room9025); ?>" data-floor="<?php echo htmlspecialchars($floor9025); ?>" data-image="<?php echo base64_encode($upload_img9025); ?>" data-category="<?php echo htmlspecialchars($category9025); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9025); ?>" data-status="<?php echo htmlspecialchars($status9025); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9025); ?>; 
position:absolute; top:100px; left:1103px;'>
                        </div>

                        <!-- ASSET 9026 -->
                        <img src='../image.php?id=9026' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9026' onclick='fetchAssetData(9026);' class="asset-image" data-id="<?php echo $assetId9026; ?>" data-room="<?php echo htmlspecialchars($room9026); ?>" data-floor="<?php echo htmlspecialchars($floor9026); ?>" data-image="<?php echo base64_encode($upload_img9026); ?>" data-category="<?php echo htmlspecialchars($category9026); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9026); ?>" data-status="<?php echo htmlspecialchars($status9026); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9026); ?>; 
position:absolute; top:100px; left:1120px;'>
                        </div>


                        <!-- ASSET 9027 -->
                        <img src='../image.php?id=9027' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9027' onclick='fetchAssetData(9027);' data-id="<?php echo $assetId9027; ?>" class="asset-image" data-room="<?php echo htmlspecialchars($room9027); ?>" data-floor="<?php echo htmlspecialchars($floor9027); ?>" data-image="<?php echo base64_encode($upload_img9027); ?>" data-category="<?php echo htmlspecialchars($category9027); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9027); ?>" data-status="<?php echo htmlspecialchars($status9027); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9027); ?>; 
position:absolute; top:100px; left:1139px;'>
                        </div>

                        <!-- ASSET 9028 -->
                        <img src='../image.php?id=9028' style='width:18px; cursor:pointer; position:absolute; top:100px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9028' onclick='fetchAssetData(9028);' data-id="<?php echo $assetId9028; ?>" class="asset-image" data-room=" <?php echo htmlspecialchars($room9028); ?>" data-floor="<?php echo htmlspecialchars($floor9028); ?>" data-image=" <?php echo base64_encode($upload_img9028); ?>" data-category="<?php echo htmlspecialchars($category9028); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9028); ?>" data-status="<?php echo htmlspecialchars($status9028); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9028); ?>; 
position:absolute; top:100px; left:1158px;'>
                        </div>

                        <!-- ASSET 9029 -->
                        <img src='../image.php?id=9029' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9029' onclick='fetchAssetData(9029);' class="asset-image" data-id="<?php echo $assetId9029; ?>" data-room=" <?php echo htmlspecialchars($room9029); ?>" data-floor="<?php echo htmlspecialchars($floor9029); ?>" data-image=" <?php echo base64_encode($upload_img9029); ?>" data-category="<?php echo htmlspecialchars($category9029); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9029); ?>" data-status="<?php echo htmlspecialchars($status9029); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9029); ?>; 
position:absolute; top:120px; left:1085px;'>
                        </div>

                        <!-- ASSET 9030 -->
                        <img src='../image.php?id=9030' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9030' onclick='fetchAssetData(9030);' class="asset-image" data-id="<?php echo $assetId9030; ?>" data-room=" <?php echo htmlspecialchars($room9030); ?>" data-floor="<?php echo htmlspecialchars($floor9030); ?>" data-image=" <?php echo base64_encode($upload_img9030); ?>" data-category="<?php echo htmlspecialchars($category9030); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9030); ?>" data-status="<?php echo htmlspecialchars($status9030); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9030); ?>; 
position:absolute; top:120px; left:1103px;'>
                        </div>


                        <!-- ASSET 9031 -->
                        <img src='../image.php?id=9031' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9031' onclick='fetchAssetData(9031);' class="asset-image" data-id="<?php echo $assetId9031; ?>" data-room=" <?php echo htmlspecialchars($room9031); ?>" data-floor="<?php echo htmlspecialchars($floor9031); ?>" data-image=" <?php echo base64_encode($upload_img9031); ?>" data-category="<?php echo htmlspecialchars($category9031); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9031); ?>" data-status="<?php echo htmlspecialchars($status9031); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9031); ?>; 
position:absolute; top:120px; left:1120px;'>
                        </div>

                        <!-- ASSET 9032 -->
                        <img src='../image.php?id=9032' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9032' onclick='fetchAssetData(9032);' class="asset-image" data-id="<?php echo $assetId9032; ?>" data-room=" <?php echo htmlspecialchars($room9032); ?>" data-floor="<?php echo htmlspecialchars($floor9032); ?>" data-image=" <?php echo base64_encode($upload_img9032); ?>" data-category="<?php echo htmlspecialchars($category9032); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9032); ?>" data-status="<?php echo htmlspecialchars($status9032); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9032); ?>; 
position:absolute; top:120px; left:1139px;'>
                        </div>

                        <!-- ASSET 9033 -->
                        <img src='../image.php?id=9033' style='width:18px; cursor:pointer; position:absolute; top:120px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9033' class="asset-image" onclick='fetchAssetData(9033);' data-id="<?php echo $assetId9033; ?>" data-room=" <?php echo htmlspecialchars($room9033); ?>" data-floor="<?php echo htmlspecialchars($floor9033); ?>" data-image=" <?php echo base64_encode($upload_img9033); ?>" data-category="<?php echo htmlspecialchars($category9033); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9033); ?>" data-status="<?php echo htmlspecialchars($status9033); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9033); ?>; 
position:absolute; top:120px; left:1158px;'>
                        </div>

                        <!-- ASSET 9034 -->
                        <img src='../image.php?id=9034' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9034' class="asset-image" onclick='fetchAssetData(9034);' data-id="<?php echo $assetId9034; ?>" data-room=" <?php echo htmlspecialchars($room9034); ?>" data-floor="<?php echo htmlspecialchars($floor9034); ?>" data-image=" <?php echo base64_encode($upload_img9034); ?>" data-category="<?php echo htmlspecialchars($category9034); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9034); ?>" data-status="<?php echo htmlspecialchars($status9034); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9034); ?>; 
position:absolute; top:145px; left:1085px;'>
                        </div>


                        <!-- ASSET 9035 -->
                        <img src='../image.php?id=9035' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9035' class="asset-image" onclick='fetchAssetData(9035);' data-id="<?php echo $assetId9035; ?>" data-room=" <?php echo htmlspecialchars($room9035); ?>" data-floor="<?php echo htmlspecialchars($floor9035); ?>" data-image=" <?php echo base64_encode($upload_img9035); ?>" data-category="<?php echo htmlspecialchars($category9035); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9035); ?>" data-status="<?php echo htmlspecialchars($status9035); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9035); ?>; 
position:absolute; top:145px; left:1103px;'>
                        </div>

                        <!-- ASSET 9036 -->
                        <img src='../image.php?id=9036' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9036' class="asset-image" onclick='fetchAssetData(9036);' data-id="<?php echo $assetId9036; ?>" data-room=" <?php echo htmlspecialchars($room9036); ?>" data-floor="<?php echo htmlspecialchars($floor9036); ?>" data-image=" <?php echo base64_encode($upload_img9036); ?>" data-category="<?php echo htmlspecialchars($category9036); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9036); ?>" data-status="<?php echo htmlspecialchars($status9036); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9036); ?>; 
position:absolute; top:145px; left:1120px;'>
                        </div>

                        <!-- ASSET 9037 -->
                        <img src='../image.php?id=9037' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9037' class="asset-image" onclick='fetchAssetData(9037);' data-id="<?php echo $assetId9037; ?>" data-room=" <?php echo htmlspecialchars($room9037); ?>" data-floor="<?php echo htmlspecialchars($floor9037); ?>" data-image=" <?php echo base64_encode($upload_img9037); ?>" data-category="<?php echo htmlspecialchars($category9037); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9037); ?>" data-status="<?php echo htmlspecialchars($status9037); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9037); ?>; 
position:absolute; top:145px; left:1139px;'>
                        </div>

                        <!-- ASSET 9038 -->
                        <img src='../image.php?id=9038' style='width:18px; cursor:pointer; position:absolute; top:145px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9038' class="asset-image" onclick='fetchAssetData(9038);' data-id="<?php echo $assetId9038; ?>" data-room=" <?php echo htmlspecialchars($room9038); ?>" data-floor="<?php echo htmlspecialchars($floor9038); ?>" data-image=" <?php echo base64_encode($upload_img9038); ?>" data-category="<?php echo htmlspecialchars($category9038); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9038); ?>" data-status="<?php echo htmlspecialchars($status9038); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9038); ?>; 
position:absolute; top:145px; left:1158px;'>
                        </div>


                        <!-- END OF ROW 3 -->

                        <!-- ASSET 9039 -->
                        <img src='../image.php?id=9039' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9039' class="asset-image" onclick='fetchAssetData(9039);' data-id="<?php echo $assetId9039; ?>" data-room=" <?php echo htmlspecialchars($room9039); ?>" data-floor="<?php echo htmlspecialchars($floor9039); ?>" data-image=" <?php echo base64_encode($upload_img9039); ?>" data-category="<?php echo htmlspecialchars($category9039); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9039); ?>" data-status="<?php echo htmlspecialchars($status9039); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9039); ?>; 
position:absolute; top:170px; left:1085px;'>
                        </div>

                        <!-- ASSET 9040 -->
                        <img src='../image.php?id=9040' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9040' class="asset-image" onclick='fetchAssetData(9040);' data-id="<?php echo $assetId9040; ?>" data-room=" <?php echo htmlspecialchars($room9040); ?>" data-floor="<?php echo htmlspecialchars($floor9040); ?>" data-image=" <?php echo base64_encode($upload_img9040); ?>" data-category="<?php echo htmlspecialchars($category9040); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9040); ?>" data-status="<?php echo htmlspecialchars($status9040); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9040); ?>; 
position:absolute; top:170px; left:1103px;'>
                        </div>

                        <!-- ASSET 9041 -->
                        <img src='../image.php?id=9041' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9041' class="asset-image" onclick='fetchAssetData(9041);' data-id="<?php echo $assetId9041; ?>" data-room=" <?php echo htmlspecialchars($room9041); ?>" data-floor="<?php echo htmlspecialchars($floor9041); ?>" data-image=" <?php echo base64_encode($upload_img9041); ?>" data-category="<?php echo htmlspecialchars($category9041); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9041); ?>" data-status="<?php echo htmlspecialchars($status9041); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9041); ?>; 
position:absolute; top:170px; left:1120px;'>
                        </div>

                        <!-- ASSET 9042 -->
                        <img src='../image.php?id=9042' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9042' class="asset-image" onclick='fetchAssetData(9042);' data-id="<?php echo $assetId9042; ?>" data-room=" <?php echo htmlspecialchars($room9042); ?>" data-floor="<?php echo htmlspecialchars($floor9042); ?>" data-image=" <?php echo base64_encode($upload_img9042); ?>" data-category="<?php echo htmlspecialchars($category9042); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9042); ?>" data-status="<?php echo htmlspecialchars($status9042); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9042); ?>; 
position:absolute; top:170px; left:1139px;'>
                        </div>

                        <!-- ASSET 9043 -->
                        <img src='../image.php?id=9043' style='width:17px; cursor:pointer; position:absolute; top:170px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9043' class="asset-image" onclick='fetchAssetData(9043);' data-id="<?php echo $assetId9043; ?>" data-room=" <?php echo htmlspecialchars($room9043); ?>" data-floor="<?php echo htmlspecialchars($floor9043); ?>" data-image=" <?php echo base64_encode($upload_img9043); ?>" data-category="<?php echo htmlspecialchars($category9043); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9043); ?>" data-status="<?php echo htmlspecialchars($status9043); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9043); ?>; 
position:absolute; top:170px; left:1158px;'>
                        </div>

                        <!-- ASSET 9048 ETO YON -->
                        <img src='../image.php?id=9048' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1075px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9048' class="asset-image" onclick='fetchAssetData(9048);' data-id="<?php echo $assetId9048; ?>" data-room=" <?php echo htmlspecialchars($room9048); ?>" data-floor="<?php echo htmlspecialchars($floor9048); ?>" data-image=" <?php echo base64_encode($upload_img9048); ?>" data-category="<?php echo htmlspecialchars($category9048); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9048); ?>" data-status="<?php echo htmlspecialchars($status9048); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9048); ?>; 
position:absolute; top:190px; left:1085px;'>
                        </div>

                        <!-- ASSET 9044 -->
                        <img src='../image.php?id=9044' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1093px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9044' class="asset-image" onclick='fetchAssetData(9044);' data-id="<?php echo $assetId9044; ?>" data-room=" <?php echo htmlspecialchars($room9044); ?>" data-floor="<?php echo htmlspecialchars($floor9044); ?>" data-image=" <?php echo base64_encode($upload_img9044); ?>" data-category="<?php echo htmlspecialchars($category9044); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9044); ?>" data-status="<?php echo htmlspecialchars($status9044); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9044); ?>; 
position:absolute; top:190px; left:1103px;'>
                        </div>

                        <!-- ASSET 9045 -->
                        <img src='../image.php?id=9045' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1111px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9045' class="asset-image" onclick='fetchAssetData(9045);' data-id="<?php echo $assetId9045; ?>" data-room=" <?php echo htmlspecialchars($room9045); ?>" data-floor="<?php echo htmlspecialchars($floor9045); ?>" data-image=" <?php echo base64_encode($upload_img9045); ?>" data-category="<?php echo htmlspecialchars($category9045); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9045); ?>" data-status="<?php echo htmlspecialchars($status9045); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9045); ?>; 
position:absolute; top:190px; left:1120px;'>
                        </div>


                        <!-- ASSET 9046 -->
                        <img src='../image.php?id=9046' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1129px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9046' class="asset-image" onclick='fetchAssetData(9046);' data-id="<?php echo $assetId9046; ?>" data-room=" <?php echo htmlspecialchars($room9046); ?>" data-floor="<?php echo htmlspecialchars($floor9046); ?>" data-image=" <?php echo base64_encode($upload_img9046); ?>" data-category="<?php echo htmlspecialchars($category9046); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9046); ?>" data-status="<?php echo htmlspecialchars($status9046); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9046); ?>; 
position:absolute; top:190px; left:1139px;'>
                        </div>

                        <!-- ASSET 9047 -->
                        <img src='../image.php?id=9047' style='width:17px; cursor:pointer; position:absolute; top:190px; left:1147px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9047' class="asset-image" onclick='fetchAssetData(9047);' data-id="<?php echo $assetId9047; ?>" data-room=" <?php echo htmlspecialchars($room9047); ?>" data-floor="<?php echo htmlspecialchars($floor9047); ?>" data-image=" <?php echo base64_encode($upload_img9047); ?>" data-category="<?php echo htmlspecialchars($category9047); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9047); ?>" data-status="<?php echo htmlspecialchars($status9047); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9047); ?>; 
position:absolute; top:190px; left:1158px;'>
                        </div>

                        <!-- ASSET 10399 -->
                        <img src='../image.php?id=10399' style='width:17px; cursor:pointer; position:absolute; top:80px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10399' class="asset-image" onclick='fetchAssetData(10399);' data-id="<?php echo $assetId10399; ?>" data-room=" <?php echo htmlspecialchars($room10399); ?>" data-floor="<?php echo htmlspecialchars($floor10399); ?>" data-image=" <?php echo base64_encode($upload_img10399); ?>" data-category="<?php echo htmlspecialchars($category10399); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10399); ?>" data-status="<?php echo htmlspecialchars($status10399); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10399); ?>; 
position:absolute; top:80px; left:950px;'>
                        </div>

                        <!-- ASSET 9049 -->
                        <img src='../image.php?id=9049' style='width:17px; cursor:pointer; position:absolute; top:80px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9049' class="asset-image" onclick='fetchAssetData(9049);' data-id="<?php echo $assetId9049; ?>" data-room=" <?php echo htmlspecialchars($room9049); ?>" data-floor="<?php echo htmlspecialchars($floor9049); ?>" data-image=" <?php echo base64_encode($upload_img9049); ?>" data-category="<?php echo htmlspecialchars($category9049); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9049); ?>" data-status="<?php echo htmlspecialchars($status9049); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9049); ?>; 
position:absolute; top:80px; left:970px;'>
                        </div>

                        <!-- ASSET 9050 -->
                        <img src='../image.php?id=9050' style='width:17px; cursor:pointer; position:absolute; top:80px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9050' class="asset-image" onclick='fetchAssetData(9050);' data-id="<?php echo $assetId9050; ?>" data-room=" <?php echo htmlspecialchars($room9050); ?>" data-floor="<?php echo htmlspecialchars($floor9050); ?>" data-image=" <?php echo base64_encode($upload_img9050); ?>" data-category="<?php echo htmlspecialchars($category9050); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9050); ?>" data-status="<?php echo htmlspecialchars($status9050); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9050); ?>; 
position:absolute; top:80px; left:990px;'>
                        </div>

                        <!-- ASSET 9051 -->
                        <img src='../image.php?id=9051' style='width:17px; cursor:pointer; position:absolute; top:80px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9051' class="asset-image" onclick='fetchAssetData(9051);' data-id="<?php echo $assetId9051; ?>" data-room=" <?php echo htmlspecialchars($room9051); ?>" data-floor="<?php echo htmlspecialchars($floor9051); ?>" data-image=" <?php echo base64_encode($upload_img9051); ?>" data-category="<?php echo htmlspecialchars($category9051); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9051); ?>" data-status="<?php echo htmlspecialchars($status9051); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9051); ?>; 
position:absolute; top:80px; left:1010px;'>
                        </div>

                        <!-- ASSET 9052 -->
                        <img src='../image.php?id=9052' style='width:17px; cursor:pointer; position:absolute; top:80px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9052' class="asset-image" onclick='fetchAssetData(9052);' data-id="<?php echo $assetId9052; ?>" data-room=" <?php echo htmlspecialchars($room9052); ?>" data-floor="<?php echo htmlspecialchars($floor9052); ?>" data-image=" <?php echo base64_encode($upload_img9052); ?>" data-category="<?php echo htmlspecialchars($category9052); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9052); ?>" data-status="<?php echo htmlspecialchars($status9052); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9052); ?>; 
position:absolute; top:80px; left:1030px;'>
                        </div>

                        <!-- ASSET 9053 -->
                        <img src='../image.php?id=9053' style='width:17px; cursor:pointer; position:absolute; top:100px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9053' class="asset-image" onclick='fetchAssetData(9053);' data-id="<?php echo $assetId9053; ?>" data-room=" <?php echo htmlspecialchars($room9053); ?>" data-floor="<?php echo htmlspecialchars($floor9053); ?>" data-image=" <?php echo base64_encode($upload_img9053); ?>" data-category="<?php echo htmlspecialchars($category9053); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9053); ?>" data-status="<?php echo htmlspecialchars($status9053); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9053); ?>; 
position:absolute; top:100px; left:950px;'>
                        </div>


                        <!-- ASSET 9054 -->
                        <img src='../image.php?id=9054' style='width:17px; cursor:pointer; position:absolute; top:100px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9054' class="asset-image" onclick='fetchAssetData(9054);' data-id="<?php echo $assetId9054; ?>" data-room=" <?php echo htmlspecialchars($room9054); ?>" data-floor="<?php echo htmlspecialchars($floor9054); ?>" data-image=" <?php echo base64_encode($upload_img9054); ?>" data-category="<?php echo htmlspecialchars($category9054); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9054); ?>" data-status="<?php echo htmlspecialchars($status9054); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9054); ?>; 
position:absolute; top:100px; left:970px;'>
                        </div>

                        <!-- ASSET 9055 -->
                        <img src='../image.php?id=9055' style='width:17px; cursor:pointer; position:absolute; top:100px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9055' class="asset-image" onclick='fetchAssetData(9055);' data-id="<?php echo $assetId9055; ?>" data-room=" <?php echo htmlspecialchars($room9055); ?>" data-floor="<?php echo htmlspecialchars($floor9055); ?>" data-image=" <?php echo base64_encode($upload_img9055); ?>" data-category="<?php echo htmlspecialchars($category9055); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9055); ?>" data-status="<?php echo htmlspecialchars($status9055); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9055); ?>; 
position:absolute; top:100px; left:990px;'>
                        </div>

                        <!-- ASSET 9056 -->
                        <img src='../image.php?id=9056' style='width:17px; cursor:pointer; position:absolute; top:100px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9056' class="asset-image" onclick='fetchAssetData(9056);' data-id="<?php echo $assetId9056; ?>" data-room=" <?php echo htmlspecialchars($room9056); ?>" data-floor="<?php echo htmlspecialchars($floor9056); ?>" data-image=" <?php echo base64_encode($upload_img9056); ?>" data-category="<?php echo htmlspecialchars($category9056); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9056); ?>" data-status="<?php echo htmlspecialchars($status9056); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9056); ?>; 
position:absolute; top:100px; left:1010px;'>
                        </div>

                        <!-- ASSET 9057 -->
                        <img src='../image.php?id=9057' style='width:17px; cursor:pointer; position:absolute; top:100px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9057' class="asset-image" onclick='fetchAssetData(9057);' data-id="<?php echo $assetId9057; ?>" data-room=" <?php echo htmlspecialchars($room9057); ?>" data-floor="<?php echo htmlspecialchars($floor9057); ?>" data-image=" <?php echo base64_encode($upload_img9057); ?>" data-category="<?php echo htmlspecialchars($category9057); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9057); ?>" data-status="<?php echo htmlspecialchars($status9057); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9057); ?>; 
position:absolute; top:100px; left:1030px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 9058 -->
                        <img src='../image.php?id=9058' style='width:17px; cursor:pointer; position:absolute; top:120px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9058' class="asset-image" onclick='fetchAssetData(9058);' data-id="<?php echo $assetId9058; ?>" data-room=" <?php echo htmlspecialchars($room9058); ?>" data-floor="<?php echo htmlspecialchars($floor9058); ?>" data-image=" <?php echo base64_encode($upload_img9058); ?>" data-category="<?php echo htmlspecialchars($category9058); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9058); ?>" data-status="<?php echo htmlspecialchars($status9058); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9058); ?>; 
position:absolute; top:120px; left:950px;'>
                        </div>

                        <!-- ASSET 9059 -->
                        <img src='../image.php?id=9059' style='width:17px; cursor:pointer; position:absolute; top:120px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9059' class="asset-image" onclick='fetchAssetData(9059);' data-id="<?php echo $assetId9059; ?>" data-room=" <?php echo htmlspecialchars($room9059); ?>" data-floor="<?php echo htmlspecialchars($floor9059); ?>" data-image=" <?php echo base64_encode($upload_img9059); ?>" data-category="<?php echo htmlspecialchars($category9059); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9059); ?>" data-status="<?php echo htmlspecialchars($status9059); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9059); ?>; 
position:absolute; top:120px; left:970px;'>
                        </div>

                        <!-- ASSET 9060 -->
                        <img src='../image.php?id=9060' style='width:17px; cursor:pointer; position:absolute; top:120px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9060' class="asset-image" onclick='fetchAssetData(9060);' data-id="<?php echo $assetId9060; ?>" data-room=" <?php echo htmlspecialchars($room9060); ?>" data-floor="<?php echo htmlspecialchars($floor9060); ?>" data-image=" <?php echo base64_encode($upload_img9060); ?>" data-category="<?php echo htmlspecialchars($category9060); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9060); ?>" data-status="<?php echo htmlspecialchars($status9060); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9060); ?>; 
position:absolute; top:120px; left:990px;'>
                        </div>

                        <!-- ASSET 9061 -->
                        <img src='../image.php?id=9061' style='width:17px; cursor:pointer; position:absolute; top:120px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9061' class="asset-image" onclick='fetchAssetData(9061);' data-id="<?php echo $assetId9061; ?>" data-room=" <?php echo htmlspecialchars($room9061); ?>" data-floor="<?php echo htmlspecialchars($floor9061); ?>" data-image=" <?php echo base64_encode($upload_img9061); ?>" data-category="<?php echo htmlspecialchars($category9061); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9061); ?>" data-status="<?php echo htmlspecialchars($status9061); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9061); ?>; 
position:absolute; top:120px; left:1010px;'>
                        </div>

                        <!-- ASSET 9062 -->
                        <img src='../image.php?id=9062' style='width:17px; cursor:pointer; position:absolute; top:120px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9062' class="asset-image" onclick='fetchAssetData(9062);' data-id="<?php echo $assetId9062; ?>" data-room=" <?php echo htmlspecialchars($room9062); ?>" data-floor="<?php echo htmlspecialchars($floor9062); ?>" data-image=" <?php echo base64_encode($upload_img9062); ?>" data-category="<?php echo htmlspecialchars($category9062); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9062); ?>" data-status="<?php echo htmlspecialchars($status9062); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9062); ?>; 
position:absolute; top:120px; left:1030px;'>
                        </div>

                        <!-- ASSET 9063 -->
                        <img src='../image.php?id=9063' style='width:17px; cursor:pointer; position:absolute; top:140px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9063' class="asset-image" onclick='fetchAssetData(9063);' data-id="<?php echo $assetId9063; ?>" data-room=" <?php echo htmlspecialchars($room9063); ?>" data-floor="<?php echo htmlspecialchars($floor9063); ?>" data-image=" <?php echo base64_encode($upload_img9063); ?>" data-category="<?php echo htmlspecialchars($category9063); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9063); ?>" data-status="<?php echo htmlspecialchars($status9063); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9063); ?>; 
position:absolute; top:140px; left:950px;'>
                        </div>

                        <!-- ASSET 9064 -->
                        <img src='../image.php?id=9064' style='width:17px; cursor:pointer; position:absolute; top:140px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9064' class="asset-image" onclick='fetchAssetData(9064);' data-id="<?php echo $assetId9064; ?>" data-room=" <?php echo htmlspecialchars($room9064); ?>" data-floor="<?php echo htmlspecialchars($floor9064); ?>" data-image=" <?php echo base64_encode($upload_img9064); ?>" data-category="<?php echo htmlspecialchars($category9064); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9064); ?>" data-status="<?php echo htmlspecialchars($status9064); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9064); ?>; 
position:absolute; top:140px; left:970px;'>
                        </div>

                        <!-- ASSET 9065 -->
                        <img src='../image.php?id=9065' style='width:17px; cursor:pointer; position:absolute; top:140px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9065' class="asset-image" onclick='fetchAssetData(9065);' data-id="<?php echo $assetId9065; ?>" data-room=" <?php echo htmlspecialchars($room9065); ?>" data-floor="<?php echo htmlspecialchars($floor9065); ?>" data-image=" <?php echo base64_encode($upload_img9065); ?>" data-category="<?php echo htmlspecialchars($category9065); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9065); ?>" data-status="<?php echo htmlspecialchars($status9065); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9065); ?>; 
position:absolute; top:140px; left:990px;'>
                        </div>


                        <!-- ASSET 9066 -->
                        <img src='../image.php?id=9066' style='width:17px; cursor:pointer; position:absolute; top:140px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9066' class="asset-image" onclick='fetchAssetData(9066);' data-id="<?php echo $assetId9066; ?>" data-room=" <?php echo htmlspecialchars($room9066); ?>" data-floor="<?php echo htmlspecialchars($floor9066); ?>" data-image=" <?php echo base64_encode($upload_img9066); ?>" data-category="<?php echo htmlspecialchars($category9066); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9066); ?>" data-status="<?php echo htmlspecialchars($status9066); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9066); ?>; 
position:absolute; top:140px; left:1010px;'>
                        </div>

                        <!-- ASSET 9067 -->
                        <img src='../image.php?id=9067' style='width:17px; cursor:pointer; position:absolute; top:140px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9067' class="asset-image" onclick='fetchAssetData(9067);' data-id="<?php echo $assetId9067; ?>" data-room=" <?php echo htmlspecialchars($room9067); ?>" data-floor="<?php echo htmlspecialchars($floor9067); ?>" data-image=" <?php echo base64_encode($upload_img9067); ?>" data-category="<?php echo htmlspecialchars($category9067); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9067); ?>" data-status="<?php echo htmlspecialchars($status9067); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9067); ?>; 
position:absolute; top:140px; left:1030px;'>
                        </div>

                        <!-- ASSET 9068 -->
                        <img src='../image.php?id=9068' style='width:17px; cursor:pointer; position:absolute; top:160px; left:939px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9068' class="asset-image" onclick='fetchAssetData(9068);' data-id="<?php echo $assetId9068; ?>" data-room=" <?php echo htmlspecialchars($room9068); ?>" data-floor="<?php echo htmlspecialchars($floor9068); ?>" data-image=" <?php echo base64_encode($upload_img9068); ?>" data-category="<?php echo htmlspecialchars($category9068); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9068); ?>" data-status="<?php echo htmlspecialchars($status9068); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9068); ?>; 
position:absolute; top:160px; left:950px;'>
                        </div>

                        <!-- ASSET 9069 -->
                        <img src='../image.php?id=9069' style='width:17px; cursor:pointer; position:absolute; top:160px; left:959px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9069' class="asset-image" onclick='fetchAssetData(9069);' data-id="<?php echo $assetId9069; ?>" data-room=" <?php echo htmlspecialchars($room9069); ?>" data-floor="<?php echo htmlspecialchars($floor9069); ?>" data-image=" <?php echo base64_encode($upload_img9069); ?>" data-category="<?php echo htmlspecialchars($category9069); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9069); ?>" data-status="<?php echo htmlspecialchars($status9069); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9069); ?>; 
position:absolute; top:160px; left:970px;'>
                        </div>


                        <!-- ASSET 9070 -->
                        <img src='../image.php?id=9070' style='width:17px; cursor:pointer; position:absolute; top:160px; left:979px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9070' class="asset-image" onclick='fetchAssetData(9070);' data-id="<?php echo $assetId9070; ?>" data-room=" <?php echo htmlspecialchars($room9070); ?>" data-floor="<?php echo htmlspecialchars($floor9070); ?>" data-image=" <?php echo base64_encode($upload_img9070); ?>" data-category="<?php echo htmlspecialchars($category9070); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9070); ?>" data-status="<?php echo htmlspecialchars($status9070); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9070); ?>; 
position:absolute; top:160px; left:990px;'>
                        </div>

                        <!-- ASSET 9071 -->
                        <img src='../image.php?id=9071' style='width:17px; cursor:pointer; position:absolute; top:160px; left:999px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9071' class="asset-image" onclick='fetchAssetData(9071);' data-id="<?php echo $assetId9071; ?>" data-room=" <?php echo htmlspecialchars($room9071); ?>" data-floor="<?php echo htmlspecialchars($floor9071); ?>" data-image=" <?php echo base64_encode($upload_img9071); ?>" data-category="<?php echo htmlspecialchars($category9071); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9071); ?>" data-status="<?php echo htmlspecialchars($status9071); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9071); ?>; 
position:absolute; top:160px; left:1010px;'>
                        </div>

                        <!-- ASSET 9072 -->
                        <img src='../image.php?id=9072' style='width:17px; cursor:pointer; position:absolute; top:160px; left:1019px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9072' class="asset-image" onclick='fetchAssetData(9072);' data-id="<?php echo $assetId9072; ?>" data-room=" <?php echo htmlspecialchars($room9072); ?>" data-floor="<?php echo htmlspecialchars($floor9072); ?>" data-image=" <?php echo base64_encode($upload_img9072); ?>" data-category="<?php echo htmlspecialchars($category9072); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9072); ?>" data-status="<?php echo htmlspecialchars($status9072); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9072); ?>; 
position:absolute; top:160px; left:1030px;'>
                        </div>

                        <!-- ASSET 9073 -->
                        <img src='../image.php?id=9073' style='width:17px; cursor:pointer; position:absolute; top:210px; left:979px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' class="asset-image" data-bs-target='#imageModal9073' onclick='fetchAssetData(9073);' data-id="<?php echo $assetId9073; ?>" data-room=" <?php echo htmlspecialchars($room9073); ?>" data-floor="<?php echo htmlspecialchars($floor9073); ?>" data-image=" <?php echo base64_encode($upload_img9073); ?>" data-category="<?php echo htmlspecialchars($category9073); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9073); ?>" data-status="<?php echo htmlspecialchars($status9073); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9073); ?>; 
position:absolute; top:220px; left:980px;'>
                        </div>


                        <!-- START OF IB104A -->

                        <!-- START OF ROW 1-->

                        <!-- ASSET 9099 -->
                        <img src='../image.php?id=9099' style='width:17px; cursor:pointer; position:absolute; top:80px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9099' class="asset-image" onclick='fetchAssetData(9099);' data-id="<?php echo $assetId9099; ?>" data-room=" <?php echo htmlspecialchars($room9099); ?>" data-floor="<?php echo htmlspecialchars($floor9099); ?>" data-image=" <?php echo base64_encode($upload_img9099); ?>" data-category="<?php echo htmlspecialchars($category9099); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9099); ?>" data-status="<?php echo htmlspecialchars($status9099); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9099); ?>; 
position:absolute; top:80px; left:820px;'>
                        </div>

                        <!-- ASSET 9074 -->
                        <img src='../image.php?id=9074' style='width:17px; cursor:pointer; position:absolute; top:80px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9074' class="asset-image" onclick='fetchAssetData(9074);' data-id="<?php echo $assetId9074; ?>" data-room=" <?php echo htmlspecialchars($room9074); ?>" data-floor="<?php echo htmlspecialchars($floor9074); ?>" data-image=" <?php echo base64_encode($upload_img9074); ?>" data-category="<?php echo htmlspecialchars($category9074); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9074); ?>" data-status="<?php echo htmlspecialchars($status9074); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9074); ?>; 
position:absolute; top:80px; left:840px;'>
                        </div>

                        <!-- ASSET 9075 -->
                        <img src='../image.php?id=9075' style='width:17px; cursor:pointer; position:absolute; top:80px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9075' class="asset-image" onclick='fetchAssetData(9075);' data-id="<?php echo $assetId9075; ?>" data-room=" <?php echo htmlspecialchars($room9075); ?>" data-floor="<?php echo htmlspecialchars($floor9075); ?>" data-image=" <?php echo base64_encode($upload_img9075); ?>" data-category="<?php echo htmlspecialchars($category9075); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9075); ?>" data-status="<?php echo htmlspecialchars($status9075); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9075); ?>; 
position:absolute; top:80px; left:860px;'>
                        </div>

                        <!-- ASSET 9076 -->
                        <img src='../image.php?id=9076' style='width:17px; cursor:pointer; position:absolute; top:80px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9076' class="asset-image" onclick='fetchAssetData(9076);' data-id="<?php echo $assetId9076; ?>" data-room=" <?php echo htmlspecialchars($room9076); ?>" data-floor="<?php echo htmlspecialchars($floor9076); ?>" data-image=" <?php echo base64_encode($upload_img9076); ?>" data-category="<?php echo htmlspecialchars($category9076); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9076); ?>" data-status="<?php echo htmlspecialchars($status9076); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9076); ?>; 
position:absolute; top:80px; left:880px;'>
                        </div>


                        <!-- ASSET 9077 -->
                        <img src='../image.php?id=9077' style='width:17px; cursor:pointer; position:absolute; top:80px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9077' class="asset-image" onclick='fetchAssetData(9077);' data-id="<?php echo $assetId9077; ?>" data-room=" <?php echo htmlspecialchars($room9077); ?>" data-floor="<?php echo htmlspecialchars($floor9077); ?>" data-image=" <?php echo base64_encode($upload_img9077); ?>" data-category="<?php echo htmlspecialchars($category9077); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9077); ?>" data-status="<?php echo htmlspecialchars($status9077); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9077); ?>; 
position:absolute; top:80px; left:900px;'>
                        </div>

                        <!-- ASSET 9078 -->
                        <img src='../image.php?id=9078' style='width:17px; cursor:pointer; position:absolute; top:100px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9078' class="asset-image" onclick='fetchAssetData(9078);' data-id="<?php echo $assetId9078; ?>" data-room=" <?php echo htmlspecialchars($room9078); ?>" data-floor="<?php echo htmlspecialchars($floor9078); ?>" data-image=" <?php echo base64_encode($upload_img9078); ?>" data-category="<?php echo htmlspecialchars($category9078); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9078); ?>" data-status="<?php echo htmlspecialchars($status9078); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9078); ?>; 
position:absolute; top:100px; left:820px;'>
                        </div>

                        <!-- ASSET 9079 -->
                        <img src='../image.php?id=9079' style='width:17px; cursor:pointer; position:absolute; top:100px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9079' class="asset-image" onclick='fetchAssetData(9079);' data-id="<?php echo $assetId9079; ?>" data-room=" <?php echo htmlspecialchars($room9079); ?>" data-floor="<?php echo htmlspecialchars($floor9079); ?>" data-image=" <?php echo base64_encode($upload_img9079); ?>" data-category="<?php echo htmlspecialchars($category9079); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9079); ?>" data-status="<?php echo htmlspecialchars($status9079); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9079); ?>; 
position:absolute; top:100px; left:840px;'>
                        </div>

                        <!-- ASSET 9080 -->
                        <img src='../image.php?id=9080' style='width:17px; cursor:pointer; position:absolute; top:100px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9080' class="asset-image" onclick='fetchAssetData(9080);' data-id="<?php echo $assetId9080; ?>" data-room=" <?php echo htmlspecialchars($room9080); ?>" data-floor="<?php echo htmlspecialchars($floor9080); ?>" data-image=" <?php echo base64_encode($upload_img9080); ?>" data-category="<?php echo htmlspecialchars($category9080); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9080); ?>" data-status="<?php echo htmlspecialchars($status9080); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9080); ?>; 
position:absolute; top:100px; left:860px;'>
                        </div>


                        <!-- ASSET 9081 -->
                        <img src='../image.php?id=9081' style='width:17px; cursor:pointer; position:absolute; top:100px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9081' class="asset-image" onclick='fetchAssetData(9081);' data-id="<?php echo $assetId9081; ?>" data-room=" <?php echo htmlspecialchars($room9081); ?>" data-floor="<?php echo htmlspecialchars($floor9081); ?>" data-image=" <?php echo base64_encode($upload_img9081); ?>" data-category="<?php echo htmlspecialchars($category9081); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9081); ?>" data-status="<?php echo htmlspecialchars($status9081); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9081); ?>; 
position:absolute; top:100px; left:880px;'>
                        </div>

                        <!-- ASSET 9082 -->
                        <img src='../image.php?id=9082' style='width:17px; cursor:pointer; position:absolute; top:100px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9082' class="asset-image" onclick='fetchAssetData(9082);' data-id="<?php echo $assetId9082; ?>" data-room=" <?php echo htmlspecialchars($room9082); ?>" data-floor="<?php echo htmlspecialchars($floor9082); ?>" data-image=" <?php echo base64_encode($upload_img9082); ?>" data-category="<?php echo htmlspecialchars($category9082); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9082); ?>" data-status="<?php echo htmlspecialchars($status9082); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9082); ?>; 
position:absolute; top:100px; left:900px;'>
                        </div>

                        <!-- ASSET 9083 -->
                        <img src='../image.php?id=9083' style='width:17px; cursor:pointer; position:absolute; top:120px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9083' class="asset-image" onclick='fetchAssetData(9083);' data-id="<?php echo $assetId9083; ?>" data-room=" <?php echo htmlspecialchars($room9083); ?>" data-floor="<?php echo htmlspecialchars($floor9083); ?>" data-image=" <?php echo base64_encode($upload_img9083); ?>" data-category="<?php echo htmlspecialchars($category9083); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9083); ?>" data-status="<?php echo htmlspecialchars($status9083); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9083); ?>; 
position:absolute; top:120px; left:820px;'>
                        </div>

                        <!-- ASSET 9084 -->
                        <img src='../image.php?id=9084' style='width:17px; cursor:pointer; position:absolute; top:120px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9084' class="asset-image" onclick='fetchAssetData(9084);' data-id="<?php echo $assetId9084; ?>" data-room=" <?php echo htmlspecialchars($room9084); ?>" data-floor="<?php echo htmlspecialchars($floor9084); ?>" data-image=" <?php echo base64_encode($upload_img9084); ?>" data-category="<?php echo htmlspecialchars($category9084); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9084); ?>" data-status="<?php echo htmlspecialchars($status9084); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9084); ?>; 
position:absolute; top:120px; left:840px;'>
                        </div>


                        <!-- ASSET 9085 -->
                        <img src='../image.php?id=9085' style='width:17px; cursor:pointer; position:absolute; top:120px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9085' class="asset-image" onclick='fetchAssetData(9085);' data-id="<?php echo $assetId9085; ?>" data-room=" <?php echo htmlspecialchars($room9085); ?>" data-floor="<?php echo htmlspecialchars($floor9085); ?>" data-image=" <?php echo base64_encode($upload_img9085); ?>" data-category="<?php echo htmlspecialchars($category9085); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9085); ?>" data-status="<?php echo htmlspecialchars($status9085); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9085); ?>; 
position:absolute; top:120px; left:860px;'>
                        </div>

                        <!-- ASSET 9086 -->
                        <img src='../image.php?id=9086' style='width:17px; cursor:pointer; position:absolute; top:120px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9086' class="asset-image" onclick='fetchAssetData(9086);' data-id="<?php echo $assetId9086; ?>" data-room=" <?php echo htmlspecialchars($room9086); ?>" data-floor="<?php echo htmlspecialchars($floor9086); ?>" data-image=" <?php echo base64_encode($upload_img9086); ?>" data-category="<?php echo htmlspecialchars($category9086); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9086); ?>" data-status="<?php echo htmlspecialchars($status9086); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9086); ?>; 
position:absolute; top:120px; left:880px;'>
                        </div>

                        <!-- ASSET 9087 -->
                        <img src='../image.php?id=9087' style='width:17px; cursor:pointer; position:absolute; top:120px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9087' class="asset-image" onclick='fetchAssetData(9087);' data-id="<?php echo $assetId9087; ?>" data-room=" <?php echo htmlspecialchars($room9087); ?>" data-floor="<?php echo htmlspecialchars($floor9087); ?>" data-image=" <?php echo base64_encode($upload_img9087); ?>" data-category="<?php echo htmlspecialchars($category9087); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9087); ?>" data-status="<?php echo htmlspecialchars($status9087); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9087); ?>; 
position:absolute; top:120px; left:900px;'>
                        </div>

                        <!-- ASSET 9088 -->
                        <img src='../image.php?id=9088' style='width:17px; cursor:pointer; position:absolute; top:140px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9088' class="asset-image" onclick='fetchAssetData(9088);' data-id="<?php echo $assetId9088; ?>" data-room=" <?php echo htmlspecialchars($room9088); ?>" data-floor="<?php echo htmlspecialchars($floor9088); ?>" data-image=" <?php echo base64_encode($upload_img9088); ?>" data-category="<?php echo htmlspecialchars($category9088); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9088); ?>" data-status="<?php echo htmlspecialchars($status9088); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9088); ?>; 
position:absolute; top:140px; left:820px;'>
                        </div>


                        <!-- ASSET 9089 -->
                        <img src='../image.php?id=9089' style='width:17px; cursor:pointer; position:absolute; top:140px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9089' class="asset-image" onclick='fetchAssetData(9089);' data-id="<?php echo $assetId9089; ?>" data-room=" <?php echo htmlspecialchars($room9089); ?>" data-floor="<?php echo htmlspecialchars($floor9089); ?>" data-image=" <?php echo base64_encode($upload_img9089); ?>" data-category="<?php echo htmlspecialchars($category9089); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9089); ?>" data-status="<?php echo htmlspecialchars($status9089); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9089); ?>; 
position:absolute; top:140px; left:840px;'>
                        </div>

                        <!-- ASSET 9090 -->
                        <img src='../image.php?id=9090' style='width:17px; cursor:pointer; position:absolute; top:140px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9090' class="asset-image" onclick='fetchAssetData(9090);' data-id="<?php echo $assetId9090; ?>" data-room=" <?php echo htmlspecialchars($room9090); ?>" data-floor="<?php echo htmlspecialchars($floor9090); ?>" data-image=" <?php echo base64_encode($upload_img9090); ?>" data-category="<?php echo htmlspecialchars($category9090); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9090); ?>" data-status="<?php echo htmlspecialchars($status9090); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9090); ?>; 
position:absolute; top:140px; left:860px;'>
                        </div>

                        <!-- ASSET 9091 -->
                        <img src='../image.php?id=9091' style='width:17px; cursor:pointer; position:absolute; top:140px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9091' class="asset-image" onclick='fetchAssetData(9091);' data-id="<?php echo $assetId9091; ?>" data-room=" <?php echo htmlspecialchars($room9091); ?>" data-floor="<?php echo htmlspecialchars($floor9091); ?>" data-image=" <?php echo base64_encode($upload_img9091); ?>" data-category="<?php echo htmlspecialchars($category9091); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9091); ?>" data-status="<?php echo htmlspecialchars($status9091); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9091); ?>; 
position:absolute; top:140px; left:880px;'>
                        </div>

                        <!-- ASSET 9092 -->
                        <img src='../image.php?id=9092' style='width:17px; cursor:pointer; position:absolute; top:140px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9092' class="asset-image" onclick='fetchAssetData(9092);' data-id="<?php echo $assetId9092; ?>" data-room=" <?php echo htmlspecialchars($room9092); ?>" data-floor="<?php echo htmlspecialchars($floor9092); ?>" data-image=" <?php echo base64_encode($upload_img9092); ?>" data-category="<?php echo htmlspecialchars($category9092); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9092); ?>" data-status="<?php echo htmlspecialchars($status9092); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9092); ?>; 
position:absolute; top:140px; left:900px;'>
                        </div>

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 9093 -->
                        <img src='../image.php?id=9093' style='width:17px; cursor:pointer; position:absolute; top:160px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9093' class="asset-image" onclick='fetchAssetData(9093);' data-id="<?php echo $assetId9093; ?>" data-room=" <?php echo htmlspecialchars($room9093); ?>" data-floor="<?php echo htmlspecialchars($floor9093); ?>" data-image=" <?php echo base64_encode($upload_img9093); ?>" data-category="<?php echo htmlspecialchars($category9093); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9093); ?>" data-status="<?php echo htmlspecialchars($status9093); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9093); ?>; 
position:absolute; top:160px; left:820px;'>
                        </div>

                        <!-- ASSET 9094 -->
                        <img src='../image.php?id=9094' style='width:17px; cursor:pointer; position:absolute; top:160px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9094' class="asset-image" onclick='fetchAssetData(9094);' data-id="<?php echo $assetId9094; ?>" data-room=" <?php echo htmlspecialchars($room9094); ?>" data-floor="<?php echo htmlspecialchars($floor9094); ?>" data-image=" <?php echo base64_encode($upload_img9094); ?>" data-category="<?php echo htmlspecialchars($category9094); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9094); ?>" data-status="<?php echo htmlspecialchars($status9094); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9094); ?>; 
position:absolute; top:160px; left:840px;'>
                        </div>

                        <!-- ASSET 9095 -->
                        <img src='../image.php?id=9095' style='width:17px; cursor:pointer; position:absolute; top:160px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9095' class="asset-image" onclick='fetchAssetData(9095);' data-id="<?php echo $assetId9095; ?>" data-room=" <?php echo htmlspecialchars($room9095); ?>" data-floor="<?php echo htmlspecialchars($floor9095); ?>" data-image=" <?php echo base64_encode($upload_img9095); ?>" data-category="<?php echo htmlspecialchars($category9095); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9095); ?>" data-status="<?php echo htmlspecialchars($status9095); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9095); ?>; 
position:absolute; top:160px; left:860px;'>
                        </div>

                        <!-- ASSET 9096 -->
                        <img src='../image.php?id=9096' style='width:17px; cursor:pointer; position:absolute; top:160px; left:870px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9096' class="asset-image" onclick='fetchAssetData(9096);' data-id="<?php echo $assetId9096; ?>" data-room=" <?php echo htmlspecialchars($room9096); ?>" data-floor="<?php echo htmlspecialchars($floor9096); ?>" data-image=" <?php echo base64_encode($upload_img9096); ?>" data-category="<?php echo htmlspecialchars($category9096); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9096); ?>" data-status="<?php echo htmlspecialchars($status9096); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9096); ?>; 
position:absolute; top:160px; left:880px;'>
                        </div>

                        <!-- ASSET 9097 -->
                        <img src='../image.php?id=9097' style='width:17px; cursor:pointer; position:absolute; top:160px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9097' class="asset-image" onclick='fetchAssetData(9097);' data-id="<?php echo $assetId9097; ?>" data-room=" <?php echo htmlspecialchars($room9097); ?>" data-floor="<?php echo htmlspecialchars($floor9097); ?>" data-image=" <?php echo base64_encode($upload_img9097); ?>" data-category="<?php echo htmlspecialchars($category9097); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9097); ?>" data-status="<?php echo htmlspecialchars($status9097); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9097); ?>; 
position:absolute; top:160px; left:900px;'>
                        </div>

                        <!-- ASSET 9098 -->
                        <img src='../image.php?id=9098' style='width:17px; cursor:pointer; position:absolute; top:210px; left:850px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' class="asset-image" data-bs-target='#imageModal9098' onclick='fetchAssetData(9098);' data-id="<?php echo $assetId9098; ?>" data-room=" <?php echo htmlspecialchars($room9098); ?>" data-floor="<?php echo htmlspecialchars($floor9098); ?>" data-image=" <?php echo base64_encode($upload_img9098); ?>" data-category="<?php echo htmlspecialchars($category9098); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9098); ?>" data-status="<?php echo htmlspecialchars($status9098); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9098); ?>; 
position:absolute; top:220px; left:850px;'>
                        </div>

                        <!-- ASSET 9125 -->
                        <img src='../image.php?id=9125' style='width:17px; cursor:pointer; position:absolute; top:80px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9125' class="asset-image" onclick='fetchAssetData(9125);' data-id="<?php echo $assetId9125; ?>" data-room=" <?php echo htmlspecialchars($room9125); ?>" data-floor="<?php echo htmlspecialchars($floor9125); ?>" data-image=" <?php echo base64_encode($upload_img9125); ?>" data-category="<?php echo htmlspecialchars($category9125); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9125); ?>" data-status="<?php echo htmlspecialchars($status9125); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9125); ?>; 
position:absolute; top:80px; left:690px;'>
                        </div>

                        <!-- ASSET 9100 -->
                        <img src='../image.php?id=9100' style='width:17px; cursor:pointer; position:absolute; top:80px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9100' class="asset-image" onclick='fetchAssetData(9100);' data-id="<?php echo $assetId9100; ?>" data-room=" <?php echo htmlspecialchars($room9100); ?>" data-floor="<?php echo htmlspecialchars($floor9100); ?>" data-image=" <?php echo base64_encode($upload_img9100); ?>" data-category="<?php echo htmlspecialchars($category9100); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9100); ?>" data-status="<?php echo htmlspecialchars($status9100); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9100); ?>; 
position:absolute; top:80px; left:710px;'>
                        </div>


                        <!-- ASSET 9101 -->
                        <img src='../image.php?id=9101' style='width:17px; cursor:pointer; position:absolute; top:80px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9101' class="asset-image" onclick='fetchAssetData(9101);' data-id="<?php echo $assetId9101; ?>" data-room=" <?php echo htmlspecialchars($room9101); ?>" data-floor="<?php echo htmlspecialchars($floor9101); ?>" data-image=" <?php echo base64_encode($upload_img9101); ?>" data-category="<?php echo htmlspecialchars($category9101); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9101); ?>" data-status="<?php echo htmlspecialchars($status9101); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9101); ?>; 
position:absolute; top:80px; left:730px;'>
                        </div>

                        <!-- ASSET 9102 -->
                        <img src='../image.php?id=9102' style='width:17px; cursor:pointer; position:absolute; top:80px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9102' class="asset-image" onclick='fetchAssetData(9102);' data-id="<?php echo $assetId9102; ?>" data-room=" <?php echo htmlspecialchars($room9102); ?>" data-floor="<?php echo htmlspecialchars($floor9102); ?>" data-image=" <?php echo base64_encode($upload_img9102); ?>" data-category="<?php echo htmlspecialchars($category9102); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9102); ?>" data-status="<?php echo htmlspecialchars($status9102); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9102); ?>; 
position:absolute; top:80px; left:750px;'>
                        </div>

                        <!-- ASSET 9103 -->
                        <img src='../image.php?id=9103' style='width:17px; cursor:pointer; position:absolute; top:80px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9103' class="asset-image" onclick='fetchAssetData(9103);' data-id="<?php echo $assetId9103; ?>" data-room=" <?php echo htmlspecialchars($room9103); ?>" data-floor="<?php echo htmlspecialchars($floor9103); ?>" data-image=" <?php echo base64_encode($upload_img9103); ?>" data-category="<?php echo htmlspecialchars($category9103); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9103); ?>" data-status="<?php echo htmlspecialchars($status9103); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9103); ?>; 
position:absolute; top:80px; left:770px;'>
                        </div>

                        <!-- ASSET 9104 -->
                        <img src='../image.php?id=9104' style='width:17px; cursor:pointer; position:absolute; top:100px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9104' class="asset-image" onclick='fetchAssetData(9104);' data-id="<?php echo $assetId9104; ?>" data-room=" <?php echo htmlspecialchars($room9104); ?>" data-floor="<?php echo htmlspecialchars($floor9104); ?>" data-image=" <?php echo base64_encode($upload_img9104); ?>" data-category="<?php echo htmlspecialchars($category9104); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9104); ?>" data-status="<?php echo htmlspecialchars($status9104); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9104); ?>; 
position:absolute; top:100px; left:690px;'>
                        </div>

                        <!-- ASSET 9105 -->
                        <img src='../image.php?id=9105' style='width:17px; cursor:pointer; position:absolute; top:100px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9105' class="asset-image" onclick='fetchAssetData(9105);' data-id="<?php echo $assetId9105; ?>" data-room=" <?php echo htmlspecialchars($room9105); ?>" data-floor="<?php echo htmlspecialchars($floor9105); ?>" data-image=" <?php echo base64_encode($upload_img9105); ?>" data-category="<?php echo htmlspecialchars($category9105); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9105); ?>" data-status="<?php echo htmlspecialchars($status9105); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9105); ?>; 
position:absolute; top:100px; left:710px;'>
                        </div>

                        <!-- ASSET 9106 -->
                        <img src='../image.php?id=9106' style='width:17px; cursor:pointer; position:absolute; top:100px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9106' class="asset-image" onclick='fetchAssetData(9106);' data-id="<?php echo $assetId9106; ?>" data-room=" <?php echo htmlspecialchars($room9106); ?>" data-floor="<?php echo htmlspecialchars($floor9106); ?>" data-image=" <?php echo base64_encode($upload_img9106); ?>" data-category="<?php echo htmlspecialchars($category9106); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9106); ?>" data-status="<?php echo htmlspecialchars($status9106); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9106); ?>; 
position:absolute; top:100px; left:730px;'>
                        </div>

                        <!-- ASSET 9107 -->
                        <img src='../image.php?id=9107' style='width:17px; cursor:pointer; position:absolute; top:100px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9107' class="asset-image" onclick='fetchAssetData(9107);' data-id="<?php echo $assetId9107; ?>" data-room=" <?php echo htmlspecialchars($room9107); ?>" data-floor="<?php echo htmlspecialchars($floor9107); ?>" data-image=" <?php echo base64_encode($upload_img9107); ?>" data-category="<?php echo htmlspecialchars($category9107); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9107); ?>" data-status="<?php echo htmlspecialchars($status9107); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9107); ?>; 
position:absolute; top:100px; left:750px;'>
                        </div>

                        <!-- ASSET 9108 -->
                        <img src='../image.php?id=9108' style='width:17px; cursor:pointer; position:absolute; top:100px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9108' class="asset-image" onclick='fetchAssetData(9108);' data-id="<?php echo $assetId9108; ?>" data-room=" <?php echo htmlspecialchars($room9108); ?>" data-floor="<?php echo htmlspecialchars($floor9108); ?>" data-image=" <?php echo base64_encode($upload_img9108); ?>" data-category="<?php echo htmlspecialchars($category9108); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9108); ?>" data-status="<?php echo htmlspecialchars($status9108); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9108); ?>; 
position:absolute; top:100px; left:770px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 9109 -->
                        <img src='../image.php?id=9109' style='width:17px; cursor:pointer; position:absolute; top:120px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9109' class="asset-image" onclick='fetchAssetData(9109);' data-id="<?php echo $assetId9109; ?>" data-room=" <?php echo htmlspecialchars($room9109); ?>" data-floor="<?php echo htmlspecialchars($floor9109); ?>" data-image=" <?php echo base64_encode($upload_img9109); ?>" data-category="<?php echo htmlspecialchars($category9109); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9109); ?>" data-status="<?php echo htmlspecialchars($status9109); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9109); ?>; 
position:absolute; top:120px; left:690px;'>
                        </div>

                        <!-- ASSET 9110 -->
                        <img src='../image.php?id=9110' style='width:17px; cursor:pointer; position:absolute; top:120px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9110' class="asset-image" onclick='fetchAssetData(9110);' data-id="<?php echo $assetId9110; ?>" data-room=" <?php echo htmlspecialchars($room9110); ?>" data-floor="<?php echo htmlspecialchars($floor9110); ?>" data-image=" <?php echo base64_encode($upload_img9110); ?>" data-category="<?php echo htmlspecialchars($category9110); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9110); ?>" data-status="<?php echo htmlspecialchars($status9110); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9110); ?>; 
position:absolute; top:120px; left:710px;'>
                        </div>

                        <!-- ASSET 9111 -->
                        <img src='../image.php?id=9111' style='width:17px; cursor:pointer; position:absolute; top:120px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9111' class="asset-image" onclick='fetchAssetData(9111);' data-id="<?php echo $assetId9111; ?>" data-room=" <?php echo htmlspecialchars($room9111); ?>" data-floor="<?php echo htmlspecialchars($floor9111); ?>" data-image=" <?php echo base64_encode($upload_img9111); ?>" data-category="<?php echo htmlspecialchars($category9111); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9111); ?>" data-status="<?php echo htmlspecialchars($status9111); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9111); ?>; 
position:absolute; top:120px; left:730px;'>
                        </div>

                        <!-- ASSET 9112 -->
                        <img src='../image.php?id=9112' style='width:17px; cursor:pointer; position:absolute; top:120px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9112' class="asset-image" onclick='fetchAssetData(9112);' data-id="<?php echo $assetId9112; ?>" data-room=" <?php echo htmlspecialchars($room9112); ?>" data-floor="<?php echo htmlspecialchars($floor9112); ?>" data-image=" <?php echo base64_encode($upload_img9112); ?>" data-category="<?php echo htmlspecialchars($category9112); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9112); ?>" data-status="<?php echo htmlspecialchars($status9112); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9112); ?>; 
position:absolute; top:120px; left:750px;'>
                        </div>


                        <!-- ASSET 9113 -->
                        <img src='../image.php?id=9113' style='width:17px; cursor:pointer; position:absolute; top:120px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9113' class="asset-image" onclick='fetchAssetData(9113);' data-id="<?php echo $assetId9113; ?>" data-room=" <?php echo htmlspecialchars($room9113); ?>" data-floor="<?php echo htmlspecialchars($floor9113); ?>" data-image=" <?php echo base64_encode($upload_img9113); ?>" data-category="<?php echo htmlspecialchars($category9113); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9113); ?>" data-status="<?php echo htmlspecialchars($status9113); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9113); ?>; 
position:absolute; top:120px; left:770px;'>
                        </div>

                        <!-- ASSET 9114 -->
                        <img src='../image.php?id=9114' style='width:17px; cursor:pointer; position:absolute; top:140px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9114' class="asset-image" onclick='fetchAssetData(9114);' data-id="<?php echo $assetId9114; ?>" data-room=" <?php echo htmlspecialchars($room9114); ?>" data-floor="<?php echo htmlspecialchars($floor9114); ?>" data-image=" <?php echo base64_encode($upload_img9114); ?>" data-category="<?php echo htmlspecialchars($category9114); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9114); ?>" data-status="<?php echo htmlspecialchars($status9114); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9114); ?>; 
position:absolute; top:140px; left:690px;'>
                        </div>

                        <!-- ASSET 9115 -->
                        <img src='../image.php?id=9115' style='width:17px; cursor:pointer; position:absolute; top:140px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9115' class="asset-image" onclick='fetchAssetData(9115);' data-id="<?php echo $assetId9115; ?>" data-room=" <?php echo htmlspecialchars($room9115); ?>" data-floor="<?php echo htmlspecialchars($floor9115); ?>" data-image=" <?php echo base64_encode($upload_img9115); ?>" data-category="<?php echo htmlspecialchars($category9115); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9115); ?>" data-status="<?php echo htmlspecialchars($status9115); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9115); ?>; 
position:absolute; top:140px; left:710px;'>
                        </div>

                        <!-- ASSET 9116 -->
                        <img src='../image.php?id=9116' style='width:17px; cursor:pointer; position:absolute; top:140px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9116' class="asset-image" onclick='fetchAssetData(9116);' data-id="<?php echo $assetId9116; ?>" data-room=" <?php echo htmlspecialchars($room9116); ?>" data-floor="<?php echo htmlspecialchars($floor9116); ?>" data-image=" <?php echo base64_encode($upload_img9116); ?>" data-category="<?php echo htmlspecialchars($category9116); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9116); ?>" data-status="<?php echo htmlspecialchars($status9116); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9116); ?>; 
position:absolute; top:140px; left:730px;'>
                        </div>

                        <!-- ASSET 9117 -->
                        <img src='../image.php?id=9117' style='width:17px; cursor:pointer; position:absolute; top:140px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9117' class="asset-image" onclick='fetchAssetData(9117);' data-id="<?php echo $assetId9117; ?>" data-room=" <?php echo htmlspecialchars($room9117); ?>" data-floor="<?php echo htmlspecialchars($floor9117); ?>" data-image=" <?php echo base64_encode($upload_img9117); ?>" data-category="<?php echo htmlspecialchars($category9117); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9117); ?>" data-status="<?php echo htmlspecialchars($status9117); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9117); ?>; 
position:absolute; top:140px; left:750px;'>
                        </div>

                        <!-- ASSET 9118 -->
                        <img src='../image.php?id=9118' style='width:17px; cursor:pointer; position:absolute; top:140px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9118' class="asset-image" onclick='fetchAssetData(9118);' data-id="<?php echo $assetId9118; ?>" data-room=" <?php echo htmlspecialchars($room9118); ?>" data-floor="<?php echo htmlspecialchars($floor9118); ?>" data-image=" <?php echo base64_encode($upload_img9118); ?>" data-category="<?php echo htmlspecialchars($category9118); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9118); ?>" data-status="<?php echo htmlspecialchars($status9118); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9118); ?>; 
position:absolute; top:140px; left:770px;'>
                        </div>

                        <!-- ASSET 9119 -->
                        <img src='../image.php?id=9119' style='width:17px; cursor:pointer; position:absolute; top:160px; left:680px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9119' class="asset-image" onclick='fetchAssetData(9119);' data-id="<?php echo $assetId9119; ?>" data-room=" <?php echo htmlspecialchars($room9119); ?>" data-floor="<?php echo htmlspecialchars($floor9119); ?>" data-image=" <?php echo base64_encode($upload_img9119); ?>" data-category="<?php echo htmlspecialchars($category9119); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9119); ?>" data-status="<?php echo htmlspecialchars($status9119); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9119); ?>; 
position:absolute; top:160px; left:690px;'>
                        </div>

                        <!-- ASSET 9120 -->
                        <img src='../image.php?id=9120' style='width:17px; cursor:pointer; position:absolute; top:160px; left:700px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9120' class="asset-image" onclick='fetchAssetData(9120);' data-id="<?php echo $assetId9120; ?>" data-room=" <?php echo htmlspecialchars($room9120); ?>" data-floor="<?php echo htmlspecialchars($floor9120); ?>" data-image=" <?php echo base64_encode($upload_img9120); ?>" data-category="<?php echo htmlspecialchars($category9120); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9120); ?>" data-status="<?php echo htmlspecialchars($status9120); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9120); ?>; 
position:absolute; top:160px; left:710px;'>
                        </div>

                        <!-- ASSET 9121 -->
                        <img src='../image.php?id=9121' style='width:17px; cursor:pointer; position:absolute; top:160px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9121' class="asset-image" onclick='fetchAssetData(9121);' data-id="<?php echo $assetId9121; ?>" data-room=" <?php echo htmlspecialchars($room9121); ?>" data-floor="<?php echo htmlspecialchars($floor9121); ?>" data-image=" <?php echo base64_encode($upload_img9121); ?>" data-category="<?php echo htmlspecialchars($category9121); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9121); ?>" data-status="<?php echo htmlspecialchars($status9121); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9121); ?>; 
position:absolute; top:160px; left:730px;'>
                        </div>

                        <!-- ASSET 9122 -->
                        <img src='../image.php?id=9122' style='width:17px; cursor:pointer; position:absolute; top:160px; left:740px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9122' class="asset-image" onclick='fetchAssetData(9122);' data-id="<?php echo $assetId9122; ?>" data-room=" <?php echo htmlspecialchars($room9122); ?>" data-floor="<?php echo htmlspecialchars($floor9122); ?>" data-image=" <?php echo base64_encode($upload_img9122); ?>" data-category="<?php echo htmlspecialchars($category9122); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9122); ?>" data-status="<?php echo htmlspecialchars($status9122); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9122); ?>; 
position:absolute; top:160px; left:750px;'>
                        </div>

                        <!-- ASSET 9123 -->
                        <img src='../image.php?id=9123' style='width:17px; cursor:pointer; position:absolute; top:160px; left:760px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9123' class="asset-image" onclick='fetchAssetData(9123);' data-id="<?php echo $assetId9123; ?>" data-room=" <?php echo htmlspecialchars($room9123); ?>" data-floor="<?php echo htmlspecialchars($floor9123); ?>" data-image=" <?php echo base64_encode($upload_img9123); ?>" data-category="<?php echo htmlspecialchars($category9123); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9123); ?>" data-status="<?php echo htmlspecialchars($status9123); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9123); ?>; 
position:absolute; top:160px; left:770px;'>
                        </div>

                        <!-- ASSET 9124 -->
                        <img src='../image.php?id=9124' style='width:17px; cursor:pointer; position:absolute; top:210px; left:720px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9124' class="asset-image" onclick='fetchAssetData(9124);' data-id="<?php echo $assetId9124; ?>" data-room=" <?php echo htmlspecialchars($room9124); ?>" data-floor="<?php echo htmlspecialchars($floor9124); ?>" data-image=" <?php echo base64_encode($upload_img9124); ?>" data-category="<?php echo htmlspecialchars($category9124); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9124); ?>" data-status="<?php echo htmlspecialchars($status9124); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9124); ?>; 
position:absolute; top:220px; left:720px;'>
                        </div>


                        <!-- END OF ROW 5 -->

                        <!-- END OF IB105A -->
                        <!-- START OF ROW IB106A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 9151 -->
                        <img src='../image.php?id=9151' style='width:17px; cursor:pointer; position:absolute; top:80px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9151' class="asset-image" onclick='fetchAssetData(9151);' data-id="<?php echo $assetId9151; ?>" data-room=" <?php echo htmlspecialchars($room9151); ?>" data-floor="<?php echo htmlspecialchars($floor9151); ?>" data-image=" <?php echo base64_encode($upload_img9151); ?>" data-category="<?php echo htmlspecialchars($category9151); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9151); ?>" data-status="<?php echo htmlspecialchars($status9151); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9151); ?>; 
position:absolute; top:80px; left:465px;'>
                        </div>

                        <!-- ASSET 9126 -->
                        <img src='../image.php?id=9126' style='width:17px; cursor:pointer; position:absolute; top:80px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9126' class="asset-image" onclick='fetchAssetData(9126);' data-id="<?php echo $assetId9126; ?>" data-room=" <?php echo htmlspecialchars($room9126); ?>" data-floor="<?php echo htmlspecialchars($floor9126); ?>" data-image=" <?php echo base64_encode($upload_img9126); ?>" data-category="<?php echo htmlspecialchars($category9126); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9126); ?>" data-status="<?php echo htmlspecialchars($status9126); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9126); ?>; 
position:absolute; top:80px; left:485px;'>
                        </div>

                        <!-- ASSET 9127 -->
                        <img src='../image.php?id=9127' style='width:17px; cursor:pointer; position:absolute; top:80px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9127' class="asset-image" onclick='fetchAssetData(9127);' data-id="<?php echo $assetId9127; ?>" data-room=" <?php echo htmlspecialchars($room9127); ?>" data-floor="<?php echo htmlspecialchars($floor9127); ?>" data-image=" <?php echo base64_encode($upload_img9127); ?>" data-category="<?php echo htmlspecialchars($category9127); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9127); ?>" data-status="<?php echo htmlspecialchars($status9127); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9127); ?>; 
position:absolute; top:80px; left:505px;'>
                        </div>

                        <!-- ASSET 9128 -->
                        <img src='../image.php?id=9128' style='width:17px; cursor:pointer; position:absolute; top:80px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9128' class="asset-image" onclick='fetchAssetData(9128);' data-id="<?php echo $assetId9128; ?>" data-room=" <?php echo htmlspecialchars($room9128); ?>" data-floor="<?php echo htmlspecialchars($floor9128); ?>" data-image=" <?php echo base64_encode($upload_img9128); ?>" data-category="<?php echo htmlspecialchars($category9128); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9128); ?>" data-status="<?php echo htmlspecialchars($status9128); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9128); ?>; 
position:absolute; top:80px; left:525px;'>
                        </div>

                        <!-- ASSET 9129 -->
                        <img src='../image.php?id=9129' style='width:17px; cursor:pointer; position:absolute; top:80px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9129' class="asset-image" onclick='fetchAssetData(9129);' data-id="<?php echo $assetId9129; ?>" data-room=" <?php echo htmlspecialchars($room9129); ?>" data-floor="<?php echo htmlspecialchars($floor9129); ?>" data-image=" <?php echo base64_encode($upload_img9129); ?>" data-category="<?php echo htmlspecialchars($category9129); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9129); ?>" data-status="<?php echo htmlspecialchars($status9129); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9129); ?>; 
position:absolute; top:80px; left:545px;'>
                        </div>

                        <!-- ASSET 9130 -->
                        <img src='../image.php?id=9130' style='width:17px; cursor:pointer; position:absolute; top:100px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9130' class="asset-image" onclick='fetchAssetData(9130);' data-id="<?php echo $assetId9130; ?>" data-room=" <?php echo htmlspecialchars($room9130); ?>" data-floor="<?php echo htmlspecialchars($floor9130); ?>" data-image=" <?php echo base64_encode($upload_img9130); ?>" data-category="<?php echo htmlspecialchars($category9130); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9130); ?>" data-status="<?php echo htmlspecialchars($status9130); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9130); ?>; 
position:absolute; top:100px; left:465px;'>
                        </div>

                        <!-- ASSET 9131 -->
                        <img src='../image.php?id=9131' style='width:17px; cursor:pointer; position:absolute; top:100px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9131' class="asset-image" onclick='fetchAssetData(9131);' data-id="<?php echo $assetId9131; ?>" data-room=" <?php echo htmlspecialchars($room9131); ?>" data-floor="<?php echo htmlspecialchars($floor9131); ?>" data-image=" <?php echo base64_encode($upload_img9131); ?>" data-category="<?php echo htmlspecialchars($category9131); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9131); ?>" data-status="<?php echo htmlspecialchars($status9131); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9131); ?>; 
position:absolute; top:100px; left:485px;'>
                        </div>

                        <!-- ASSET 9132 -->
                        <img src='../image.php?id=9132' style='width:17px; cursor:pointer; position:absolute; top:100px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9132' class="asset-image" onclick='fetchAssetData(9132);' data-id="<?php echo $assetId9132; ?>" data-room=" <?php echo htmlspecialchars($room9132); ?>" data-floor="<?php echo htmlspecialchars($floor9132); ?>" data-image=" <?php echo base64_encode($upload_img9132); ?>" data-category="<?php echo htmlspecialchars($category9132); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9132); ?>" data-status="<?php echo htmlspecialchars($status9132); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9132); ?>; 
position:absolute; top:100px; left:505px;'>
                        </div>


                        <!-- ASSET 9133 -->
                        <img src='../image.php?id=9133' style='width:17px; cursor:pointer; position:absolute; top:100px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9133' class="asset-image" onclick='fetchAssetData(9133);' data-id="<?php echo $assetId9133; ?>" data-room=" <?php echo htmlspecialchars($room9133); ?>" data-floor="<?php echo htmlspecialchars($floor9133); ?>" data-image=" <?php echo base64_encode($upload_img9133); ?>" data-category="<?php echo htmlspecialchars($category9133); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9133); ?>" data-status="<?php echo htmlspecialchars($status9133); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9133); ?>; 
position:absolute; top:100px; left:525px;'>
                        </div>

                        <!-- ASSET 9134 -->
                        <img src='../image.php?id=9134' style='width:17px; cursor:pointer; position:absolute; top:100px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9134' class="asset-image" onclick='fetchAssetData(9134);' data-id="<?php echo $assetId9134; ?>" data-room=" <?php echo htmlspecialchars($room9134); ?>" data-floor="<?php echo htmlspecialchars($floor9134); ?>" data-image=" <?php echo base64_encode($upload_img9134); ?>" data-category="<?php echo htmlspecialchars($category9134); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9134); ?>" data-status="<?php echo htmlspecialchars($status9134); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9134); ?>; 
position:absolute; top:100px; left:545px;'>
                        </div>

                        <!-- ASSET 9135 -->
                        <img src='../image.php?id=9135' style='width:17px; cursor:pointer; position:absolute; top:120px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9135' class="asset-image" onclick='fetchAssetData(9135);' data-id="<?php echo $assetId9135; ?>" data-room=" <?php echo htmlspecialchars($room9135); ?>" data-floor="<?php echo htmlspecialchars($floor9135); ?>" data-image=" <?php echo base64_encode($upload_img9135); ?>" data-category="<?php echo htmlspecialchars($category9135); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9135); ?>" data-status="<?php echo htmlspecialchars($status9135); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9135); ?>; 
position:absolute; top:120px; left:465px;'>
                        </div>

                        <!-- ASSET 9136 -->
                        <img src='../image.php?id=9136' style='width:17px; cursor:pointer; position:absolute; top:120px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9136' class="asset-image" onclick='fetchAssetData(9136);' data-id="<?php echo $assetId9136; ?>" data-room=" <?php echo htmlspecialchars($room9136); ?>" data-floor="<?php echo htmlspecialchars($floor9136); ?>" data-image=" <?php echo base64_encode($upload_img9136); ?>" data-category="<?php echo htmlspecialchars($category9136); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9136); ?>" data-status="<?php echo htmlspecialchars($status9136); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9136); ?>; 
position:absolute; top:120px; left:485px;'>
                        </div>


                        <!-- ASSET 9137 -->
                        <img src='../image.php?id=9137' style='width:17px; cursor:pointer; position:absolute; top:120px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9137' class="asset-image" onclick='fetchAssetData(9137);' data-id="<?php echo $assetId9137; ?>" data-room=" <?php echo htmlspecialchars($room9137); ?>" data-floor="<?php echo htmlspecialchars($floor9137); ?>" data-image=" <?php echo base64_encode($upload_img9137); ?>" data-category="<?php echo htmlspecialchars($category9137); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9137); ?>" data-status="<?php echo htmlspecialchars($status9137); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9137); ?>; 
position:absolute; top:120px; left:505px;'>
                        </div>

                        <!-- ASSET 9138 -->
                        <img src='../image.php?id=9138' style='width:17px; cursor:pointer; position:absolute; top:120px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9138' class="asset-image" onclick='fetchAssetData(9138);' data-id="<?php echo $assetId9138; ?>" data-room=" <?php echo htmlspecialchars($room9138); ?>" data-floor="<?php echo htmlspecialchars($floor9138); ?>" data-image=" <?php echo base64_encode($upload_img9138); ?>" data-category="<?php echo htmlspecialchars($category9138); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9138); ?>" data-status="<?php echo htmlspecialchars($status9138); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9138); ?>; 
position:absolute; top:120px; left:525px;'>
                        </div>

                        <!-- ASSET 9139 -->
                        <img src='../image.php?id=9139' style='width:17px; cursor:pointer; position:absolute; top:120px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9139' class="asset-image" onclick='fetchAssetData(9139);' data-id="<?php echo $assetId9139; ?>" data-room=" <?php echo htmlspecialchars($room9139); ?>" data-floor="<?php echo htmlspecialchars($floor9139); ?>" data-image=" <?php echo base64_encode($upload_img9139); ?>" data-category="<?php echo htmlspecialchars($category9139); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9139); ?>" data-status="<?php echo htmlspecialchars($status9139); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9139); ?>; 
position:absolute; top:120px; left:545px;'>
                        </div>

                        <!-- ASSET 9140 -->
                        <img src='../image.php?id=9140' style='width:17px; cursor:pointer; position:absolute; top:140px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9140' class="asset-image" onclick='fetchAssetData(9140);' data-id="<?php echo $assetId9140; ?>" data-room=" <?php echo htmlspecialchars($room9140); ?>" data-floor="<?php echo htmlspecialchars($floor9140); ?>" data-image=" <?php echo base64_encode($upload_img9140); ?>" data-category="<?php echo htmlspecialchars($category9140); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9140); ?>" data-status="<?php echo htmlspecialchars($status9140); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9140); ?>; 
position:absolute; top:140px; left:465px;'>
                        </div>


                        <!-- ASSET 9141 -->
                        <img src='../image.php?id=9141' style='width:17px; cursor:pointer; position:absolute; top:140px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9141' class="asset-image" onclick='fetchAssetData(9141);' data-id="<?php echo $assetId9141; ?>" data-room=" <?php echo htmlspecialchars($room9141); ?>" data-floor="<?php echo htmlspecialchars($floor9141); ?>" data-image=" <?php echo base64_encode($upload_img9141); ?>" data-category="<?php echo htmlspecialchars($category9141); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9141); ?>" data-status="<?php echo htmlspecialchars($status9141); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9141); ?>; 
position:absolute; top:140px; left:485px;'>
                        </div>

                        <!-- ASSET 9142 -->
                        <img src='../image.php?id=9142' style='width:17px; cursor:pointer; position:absolute; top:140px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9142' class="asset-image" onclick='fetchAssetData(9142);' data-id="<?php echo $assetId9142; ?>" data-room=" <?php echo htmlspecialchars($room9142); ?>" data-floor="<?php echo htmlspecialchars($floor9142); ?>" data-image=" <?php echo base64_encode($upload_img9142); ?>" data-category="<?php echo htmlspecialchars($category9142); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9142); ?>" data-status="<?php echo htmlspecialchars($status9142); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9142); ?>; 
position:absolute; top:140px; left:505px;'>
                        </div>

                        <!-- ASSET 9143 -->
                        <img src='../image.php?id=9143' style='width:17px; cursor:pointer; position:absolute; top:140px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9143' class="asset-image" onclick='fetchAssetData(9143);' data-id="<?php echo $assetId9143; ?>" data-room=" <?php echo htmlspecialchars($room9143); ?>" data-floor="<?php echo htmlspecialchars($floor9143); ?>" data-image=" <?php echo base64_encode($upload_img9143); ?>" data-category="<?php echo htmlspecialchars($category9143); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9143); ?>" data-status="<?php echo htmlspecialchars($status9143); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9143); ?>; 
position:absolute; top:140px; left:525px;'>
                        </div>

                        <!-- ASSET 9144 -->
                        <img src='../image.php?id=9144' style='width:17px; cursor:pointer; position:absolute; top:140px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9144' class="asset-image" onclick='fetchAssetData(9144);' data-id="<?php echo $assetId9144; ?>" data-room=" <?php echo htmlspecialchars($room9144); ?>" data-floor="<?php echo htmlspecialchars($floor9144); ?>" data-image=" <?php echo base64_encode($upload_img9144); ?>" data-category="<?php echo htmlspecialchars($category9144); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9144); ?>" data-status="<?php echo htmlspecialchars($status9144); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9144); ?>; 
position:absolute; top:140px; left:545px;'>
                        </div>


                        <!-- END OF ROW 4 -->

                        <!-- ASSET 9145 -->
                        <img src='../image.php?id=9145' style='width:17px; cursor:pointer; position:absolute; top:160px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9145' class="asset-image" onclick='fetchAssetData(9145);' data-id="<?php echo $assetId9145; ?>" data-room=" <?php echo htmlspecialchars($room9145); ?>" data-floor="<?php echo htmlspecialchars($floor9145); ?>" data-image=" <?php echo base64_encode($upload_img9145); ?>" data-category="<?php echo htmlspecialchars($category9145); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9145); ?>" data-status="<?php echo htmlspecialchars($status9145); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9145); ?>; 
position:absolute; top:160px; left:465px;'>
                        </div>

                        <!-- ASSET 9146 -->
                        <img src='../image.php?id=9146' style='width:17px; cursor:pointer; position:absolute; top:160px; left:475px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9146' class="asset-image" onclick='fetchAssetData(9146);' data-id="<?php echo $assetId9146; ?>" data-room=" <?php echo htmlspecialchars($room9146); ?>" data-floor="<?php echo htmlspecialchars($floor9146); ?>" data-image=" <?php echo base64_encode($upload_img9146); ?>" data-category="<?php echo htmlspecialchars($category9146); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9146); ?>" data-status="<?php echo htmlspecialchars($status9146); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9146); ?>; 
position:absolute; top:160px; left:485px;'>
                        </div>

                        <!-- ASSET 9147 -->
                        <img src='../image.php?id=9147' style='width:17px; cursor:pointer; position:absolute; top:160px; left:495px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9147' class="asset-image" onclick='fetchAssetData(9147);' data-id="<?php echo $assetId9147; ?>" data-room=" <?php echo htmlspecialchars($room9147); ?>" data-floor="<?php echo htmlspecialchars($floor9147); ?>" data-image=" <?php echo base64_encode($upload_img9147); ?>" data-category="<?php echo htmlspecialchars($category9147); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9147); ?>" data-status="<?php echo htmlspecialchars($status9147); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9147); ?>; 
position:absolute; top:160px; left:505px;'>
                        </div>

                        <!-- ASSET 9148 -->
                        <img src='../image.php?id=9148' style='width:17px; cursor:pointer; position:absolute; top:160px; left:515px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9148' class="asset-image" onclick='fetchAssetData(9148);' data-id="<?php echo $assetId9148; ?>" data-room=" <?php echo htmlspecialchars($room9148); ?>" data-floor="<?php echo htmlspecialchars($floor9148); ?>" data-image=" <?php echo base64_encode($upload_img9148); ?>" data-category="<?php echo htmlspecialchars($category9148); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9148); ?>" data-status="<?php echo htmlspecialchars($status9148); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9148); ?>; 
position:absolute; top:160px; left:525px;'>
                        </div>


                        <!-- ASSET 9149 -->
                        <img src='../image.php?id=9149' style='width:17px; cursor:pointer; position:absolute; top:160px; left:535px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9149' class="asset-image" onclick='fetchAssetData(9149);' data-id="<?php echo $assetId9149; ?>" data-room=" <?php echo htmlspecialchars($room9149); ?>" data-floor="<?php echo htmlspecialchars($floor9149); ?>" data-image=" <?php echo base64_encode($upload_img9149); ?>" data-category="<?php echo htmlspecialchars($category9149); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9149); ?>" data-status="<?php echo htmlspecialchars($status9149); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9149); ?>; 
position:absolute; top:160px; left:545px;'>
                        </div>

                        <!-- ASSET 9150 -->
                        <img src='../image.php?id=9150' style='width:17px; cursor:pointer; position:absolute; top:210px; left:495px; transform: rotate(180deg);' alt='Asset Image' class="asset-image" data-bs-toggle='modal' data-bs-target='#imageModal9150' onclick='fetchAssetData(9150);' data-id="<?php echo $assetId9150; ?>" data-room="<?php echo htmlspecialchars($room9150); ?>" data-floor="<?php echo htmlspecialchars($floor9150); ?>" data-image="<?php echo base64_encode($upload_img9150); ?>" data-category="<?php echo htmlspecialchars($category9150); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9150); ?>" data-status="<?php echo htmlspecialchars($status9150); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9150); ?>;
position:absolute; top:220px; left:495px;'>
                        </div>

                        <!-- END OF ROW 5 -->

                        <!-- END OF IB106A -->
                        <!-- START OF ROW IB107A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 9152 -->
                        <img src='../image.php?id=9152' style='width:17px; cursor:pointer; position:absolute; top:80px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9152' class="asset-image" onclick='fetchAssetData(9152);' data-id="<?php echo $assetId9152; ?>" data-room=" <?php echo htmlspecialchars($room9152); ?>" data-floor="<?php echo htmlspecialchars($floor9152); ?>" data-image=" <?php echo base64_encode($upload_img9152); ?>" data-category="<?php echo htmlspecialchars($category9152); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9152); ?>" data-status="<?php echo htmlspecialchars($status9152); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9152); ?>; 
position:absolute; top:80px; left:335px;'>
                        </div>

                        <!-- ASSET 9153 -->
                        <img src='../image.php?id=9153' style='width:17px; cursor:pointer; position:absolute; top:80px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9153' class="asset-image" onclick='fetchAssetData(9153);' data-id="<?php echo $assetId9153; ?>" data-room=" <?php echo htmlspecialchars($room9153); ?>" data-floor="<?php echo htmlspecialchars($floor9153); ?>" data-image=" <?php echo base64_encode($upload_img9153); ?>" data-category="<?php echo htmlspecialchars($category9153); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9153); ?>" data-status="<?php echo htmlspecialchars($status9153); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9153); ?>; 
position:absolute; top:80px; left:355px;'>
                        </div>

                        <!-- ASSET 9154 -->
                        <img src='../image.php?id=9154' style='width:17px; cursor:pointer; position:absolute; top:80px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9154' class="asset-image" onclick='fetchAssetData(9154);' data-id="<?php echo $assetId9154; ?>" data-room=" <?php echo htmlspecialchars($room9154); ?>" data-floor="<?php echo htmlspecialchars($floor9154); ?>" data-image=" <?php echo base64_encode($upload_img9154); ?>" data-category="<?php echo htmlspecialchars($category9154); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9154); ?>" data-status="<?php echo htmlspecialchars($status9154); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9154); ?>; 
position:absolute; top:80px; left:375px;'>
                        </div>

                        <!-- ASSET 9155 -->
                        <img src='../image.php?id=9155' style='width:17px; cursor:pointer; position:absolute; top:80px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9155' class="asset-image" onclick='fetchAssetData(9155);' data-id="<?php echo $assetId9155; ?>" data-room=" <?php echo htmlspecialchars($room9155); ?>" data-floor="<?php echo htmlspecialchars($floor9155); ?>" data-image=" <?php echo base64_encode($upload_img9155); ?>" data-category="<?php echo htmlspecialchars($category9155); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9155); ?>" data-status="<?php echo htmlspecialchars($status9155); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9155); ?>; 
position:absolute; top:80px; left:395px;'>
                        </div>

                        <!-- ASSET 9156 -->
                        <img src='../image.php?id=9156' style='width:17px; cursor:pointer; position:absolute; top:80px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9156' class="asset-image" onclick='fetchAssetData(9156);' data-id="<?php echo $assetId9156; ?>" data-room=" <?php echo htmlspecialchars($room9156); ?>" data-floor="<?php echo htmlspecialchars($floor9156); ?>" data-image=" <?php echo base64_encode($upload_img9156); ?>" data-category="<?php echo htmlspecialchars($category9156); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9156); ?>" data-status="<?php echo htmlspecialchars($status9156); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9156); ?>; 
position:absolute; top:80px; left:415px;'>
                        </div>

                        <!-- END OF ROW 1 -->

                        <!-- ASSET 9157 -->
                        <img src='../image.php?id=9157' style='width:17px; cursor:pointer; position:absolute; top:100px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9157' class="asset-image" onclick='fetchAssetData(9157);' data-id="<?php echo $assetId9157; ?>" data-room=" <?php echo htmlspecialchars($room9157); ?>" data-floor="<?php echo htmlspecialchars($floor9157); ?>" data-image=" <?php echo base64_encode($upload_img9157); ?>" data-category="<?php echo htmlspecialchars($category9157); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9157); ?>" data-status="<?php echo htmlspecialchars($status9157); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9157); ?>; 
position:absolute; top:100px; left:335px;'>
                        </div>

                        <!-- ASSET 9158 -->
                        <img src='../image.php?id=9158' style='width:17px; cursor:pointer; position:absolute; top:100px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9158' class="asset-image" onclick='fetchAssetData(9158);' data-id="<?php echo $assetId9158; ?>" data-room=" <?php echo htmlspecialchars($room9158); ?>" data-floor="<?php echo htmlspecialchars($floor9158); ?>" data-image=" <?php echo base64_encode($upload_img9158); ?>" data-category="<?php echo htmlspecialchars($category9158); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9158); ?>" data-status="<?php echo htmlspecialchars($status9158); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9158); ?>; 
position:absolute; top:100px; left:355px;'>
                        </div>

                        <!-- ASSET 9159 -->
                        <img src='../image.php?id=9159' style='width:17px; cursor:pointer; position:absolute; top:100px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9159' class="asset-image" onclick='fetchAssetData(9159);' data-id="<?php echo $assetId9159; ?>" data-room=" <?php echo htmlspecialchars($room9159); ?>" data-floor="<?php echo htmlspecialchars($floor9159); ?>" data-image=" <?php echo base64_encode($upload_img9159); ?>" data-category="<?php echo htmlspecialchars($category9159); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9159); ?>" data-status="<?php echo htmlspecialchars($status9159); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9159); ?>; 
position:absolute; top:100px; left:375px;'>
                        </div>


                        <!-- ASSET 10502 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:100px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10502' class="asset-image" onclick='fetchAssetData(10502);' data-id="<?php echo $assetId10502; ?>" data-room=" <?php echo htmlspecialchars($room10502); ?>" data-floor="<?php echo htmlspecialchars($floor10502); ?>" data-image=" <?php echo base64_encode($upload_img10502); ?>" data-category="<?php echo htmlspecialchars($category10502); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10502); ?>" data-status="<?php echo htmlspecialchars($status10502); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10502); ?>; 
position:absolute; top:100px; left:395px;'>
                        </div>

                        <!-- ASSET 10503 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:100px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10503' class="asset-image" onclick='fetchAssetData(10503);' data-id="<?php echo $assetId10503; ?>" data-room=" <?php echo htmlspecialchars($room10503); ?>" data-floor="<?php echo htmlspecialchars($floor10503); ?>" data-image=" <?php echo base64_encode($upload_img10503); ?>" data-category="<?php echo htmlspecialchars($category10503); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10503); ?>" data-status="<?php echo htmlspecialchars($status10503); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10503); ?>; 
position:absolute; top:100px; left:415px;'>
                        </div>

                        <!-- END OF ROW 2 -->

                        <!-- ASSET 10504 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10504' class="asset-image" onclick='fetchAssetData(10504);' data-id="<?php echo $assetId10504; ?>" data-room=" <?php echo htmlspecialchars($room10504); ?>" data-floor="<?php echo htmlspecialchars($floor10504); ?>" data-image=" <?php echo base64_encode($upload_img10504); ?>" data-category="<?php echo htmlspecialchars($category10504); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10504); ?>" data-status="<?php echo htmlspecialchars($status10504); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10504); ?>; 
position:absolute; top:120px; left:335px;'>
                        </div>

                        <!-- ASSET 10505 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10505' class="asset-image" onclick='fetchAssetData(10505);' data-id="<?php echo $assetId10505; ?>" data-room=" <?php echo htmlspecialchars($room10505); ?>" data-floor="<?php echo htmlspecialchars($floor10505); ?>" data-image=" <?php echo base64_encode($upload_img10505); ?>" data-category="<?php echo htmlspecialchars($category10505); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10505); ?>" data-status="<?php echo htmlspecialchars($status10505); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10505); ?>; 
position:absolute; top:120px; left:355px;'>
                        </div>


                        <!-- ASSET 10506 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10506' class="asset-image" onclick='fetchAssetData(10506);' data-id="<?php echo $assetId10506; ?>" data-room=" <?php echo htmlspecialchars($room10506); ?>" data-floor="<?php echo htmlspecialchars($floor10506); ?>" data-image=" <?php echo base64_encode($upload_img10506); ?>" data-category="<?php echo htmlspecialchars($category10506); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10506); ?>" data-status="<?php echo htmlspecialchars($status10506); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10506); ?>; 
position:absolute; top:120px; left:375px;'>
                        </div>

                        <!-- ASSET 10507 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10507' class="asset-image" onclick='fetchAssetData(10507);' data-id="<?php echo $assetId10507; ?>" data-room=" <?php echo htmlspecialchars($room10507); ?>" data-floor="<?php echo htmlspecialchars($floor10507); ?>" data-image=" <?php echo base64_encode($upload_img10507); ?>" data-category="<?php echo htmlspecialchars($category10507); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10507); ?>" data-status="<?php echo htmlspecialchars($status10507); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10507); ?>; 
position:absolute; top:120px; left:395px;'>
                        </div>

                        <!-- ASSET 10508 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:120px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10508' class="asset-image" onclick='fetchAssetData(10508);' data-id="<?php echo $assetId10508; ?>" data-room=" <?php echo htmlspecialchars($room10508); ?>" data-floor="<?php echo htmlspecialchars($floor10508); ?>" data-image=" <?php echo base64_encode($upload_img10508); ?>" data-category="<?php echo htmlspecialchars($category10508); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10508); ?>" data-status="<?php echo htmlspecialchars($status10508); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10508); ?>; 
position:absolute; top:120px; left:415px;'>
                        </div>
                        <!-- END OF ROW 3 -->

                        <!-- START OF ROW 4 -->

                        <!-- ASSET 10509 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:140px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal10509' class="asset-image" onclick='fetchAssetData(10509);' data-id="<?php echo $assetId10509; ?>" data-room=" <?php echo htmlspecialchars($room10509); ?>" data-floor="<?php echo htmlspecialchars($floor10509); ?>" data-image=" <?php echo base64_encode($upload_img10509); ?>" data-category="<?php echo htmlspecialchars($category10509); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName10509); ?>" data-status="<?php echo htmlspecialchars($status10509); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status10509); ?>; 
position:absolute; top:140px; left:335px;'>
                        </div>

                        <!-- ASSET 16657 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:140px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16657' class="asset-image" onclick='fetchAssetData(16657);' data-id="<?php echo $assetId16657; ?>" data-room=" <?php echo htmlspecialchars($room16657); ?>" data-floor="<?php echo htmlspecialchars($floor16657); ?>" data-image=" <?php echo base64_encode($upload_img16657); ?>" data-category="<?php echo htmlspecialchars($category16657); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16657); ?>" data-status="<?php echo htmlspecialchars($status16657); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16657); ?>; 
position:absolute; top:140px; left:355px;'>
                        </div>

                        <!-- ASSET 16658 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:140px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16658' class="asset-image" onclick='fetchAssetData(16658);' data-id="<?php echo $assetId16658; ?>" data-room=" <?php echo htmlspecialchars($room16658); ?>" data-floor="<?php echo htmlspecialchars($floor16658); ?>" data-image=" <?php echo base64_encode($upload_img16658); ?>" data-category="<?php echo htmlspecialchars($category16658); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16658); ?>" data-status="<?php echo htmlspecialchars($status16658); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16658); ?>; 
position:absolute; top:140px; left:375px;'>
                        </div>

                        <!-- ASSET 16659 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:140px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16659' class="asset-image" onclick='fetchAssetData(16659);' data-id="<?php echo $assetId16659; ?>" data-room=" <?php echo htmlspecialchars($room16659); ?>" data-floor="<?php echo htmlspecialchars($floor16659); ?>" data-image=" <?php echo base64_encode($upload_img16659); ?>" data-category="<?php echo htmlspecialchars($category16659); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16659); ?>" data-status="<?php echo htmlspecialchars($status16659); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16659); ?>; 
position:absolute; top:140px; left:395px;'>
                        </div>

                        <!-- ASSET 16660 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:140px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16660' class="asset-image" onclick='fetchAssetData(16660);' data-id="<?php echo $assetId16660; ?>" data-room=" <?php echo htmlspecialchars($room16660); ?>" data-floor="<?php echo htmlspecialchars($floor16660); ?>" data-image=" <?php echo base64_encode($upload_img16660); ?>" data-category="<?php echo htmlspecialchars($category16660); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16660); ?>" data-status="<?php echo htmlspecialchars($status16660); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16660); ?>; 
position:absolute; top:140px; left:415px;'>
                        </div>

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 16661 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:160px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16661' class="asset-image" onclick='fetchAssetData(16661);' data-id="<?php echo $assetId16661; ?>" data-room=" <?php echo htmlspecialchars($room16661); ?>" data-floor="<?php echo htmlspecialchars($floor16661); ?>" data-image=" <?php echo base64_encode($upload_img16661); ?>" data-category="<?php echo htmlspecialchars($category16661); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16661); ?>" data-status="<?php echo htmlspecialchars($status16661); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16661); ?>; 
position:absolute; top:160px; left:335px;'>
                        </div>

                        <!-- ASSET 16662 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:160px; left:345px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16662' class="asset-image" onclick='fetchAssetData(16662);' data-id="<?php echo $assetId16662; ?>" data-room=" <?php echo htmlspecialchars($room16662); ?>" data-floor="<?php echo htmlspecialchars($floor16662); ?>" data-image=" <?php echo base64_encode($upload_img16662); ?>" data-category="<?php echo htmlspecialchars($category16662); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16662); ?>" data-status="<?php echo htmlspecialchars($status16662); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16662); ?>; 
position:absolute; top:160px; left:355px;'>
                        </div>

                        <!-- ASSET 16663 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:160px; left:365px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16663' class="asset-image" onclick='fetchAssetData(16663);' data-id="<?php echo $assetId16663; ?>" data-room=" <?php echo htmlspecialchars($room16663); ?>" data-floor="<?php echo htmlspecialchars($floor16663); ?>" data-image=" <?php echo base64_encode($upload_img16663); ?>" data-category="<?php echo htmlspecialchars($category16663); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16663); ?>" data-status="<?php echo htmlspecialchars($status16663); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16663); ?>; 
position:absolute; top:160px; left:375px;'>
                        </div>

                        <!-- ASSET 16664 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:160px; left:385px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16664' class="asset-image" onclick='fetchAssetData(16664);' data-id="<?php echo $assetId16664; ?>" data-room=" <?php echo htmlspecialchars($room16664); ?>" data-floor="<?php echo htmlspecialchars($floor16664); ?>" data-image=" <?php echo base64_encode($upload_img16664); ?>" data-category="<?php echo htmlspecialchars($category16664); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16664); ?>" data-status="<?php echo htmlspecialchars($status16664); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16664); ?>; 
position:absolute; top:160px; left:395px;'>
                        </div>

                        <!-- ASSET 16665 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:160px; left:405px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16665' class="asset-image" onclick='fetchAssetData(16665);' data-id="<?php echo $assetId16665; ?>" data-room=" <?php echo htmlspecialchars($room16665); ?>" data-floor="<?php echo htmlspecialchars($floor16665); ?>" data-image=" <?php echo base64_encode($upload_img16665); ?>" data-category="<?php echo htmlspecialchars($category16665); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16665); ?>" data-status="<?php echo htmlspecialchars($status16665); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16665); ?>; 
position:absolute; top:160px; left:415px;'>
                        </div>

                        <!-- ASSET 16666 -->
                        <img src='../image.php?id=201' style='width:17px; cursor:pointer; position:absolute; top:210px; left:365px; transform: rotate(180deg); alt=' Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16666' class="asset-image" onclick='fetchAssetData(16666);' data-id="<?php echo $assetId16666; ?>" data-room=" <?php echo htmlspecialchars($room16666); ?>" data-floor="<?php echo htmlspecialchars($floor16666); ?>" data-image=" <?php echo base64_encode($upload_img16666); ?>" data-category="<?php echo htmlspecialchars($category16666); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16666); ?>" data-status="<?php echo htmlspecialchars($status16666); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16666); ?>; 
position:absolute; top:220px; left:365px;'>
                        </div>





                        <!-- START OF IB108A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 9160 -->
                        <img src='../image.php?id=9160' style='width:17px; cursor:pointer; position:absolute; top:80px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9160' class="asset-image" onclick='fetchAssetData(9160);' data-id="<?php echo $assetId9160; ?>" data-room=" <?php echo htmlspecialchars($room9160); ?>" data-floor="<?php echo htmlspecialchars($floor9160); ?>" data-image=" <?php echo base64_encode($upload_img9160); ?>" data-category="<?php echo htmlspecialchars($category9160); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9160); ?>" data-status="<?php echo htmlspecialchars($status9160); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9160); ?>; 
position:absolute; top:80px; left:205px;'>
                        </div>

                        <!-- ASSET 9161 -->
                        <img src='../image.php?id=9161' style='width:17px; cursor:pointer; position:absolute; top:80px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9161' class="asset-image" onclick='fetchAssetData(9161);' data-id="<?php echo $assetId9161; ?>" data-room=" <?php echo htmlspecialchars($room9161); ?>" data-floor="<?php echo htmlspecialchars($floor9161); ?>" data-image=" <?php echo base64_encode($upload_img9161); ?>" data-category="<?php echo htmlspecialchars($category9161); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9161); ?>" data-status="<?php echo htmlspecialchars($status9161); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9161); ?>; 
position:absolute; top:80px; left:225px;'>
                        </div>

                        <!-- ASSET 9162 -->
                        <img src='../image.php?id=9162' style='width:17px; cursor:pointer; position:absolute; top:80px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9162' class="asset-image" onclick='fetchAssetData(9162);' data-id="<?php echo $assetId9162; ?>" data-room=" <?php echo htmlspecialchars($room9162); ?>" data-floor="<?php echo htmlspecialchars($floor9162); ?>" data-image=" <?php echo base64_encode($upload_img9162); ?>" data-category="<?php echo htmlspecialchars($category9162); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9162); ?>" data-status="<?php echo htmlspecialchars($status9162); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9162); ?>; 
position:absolute; top:80px; left:245px;'>
                        </div>

                        <!-- ASSET 9163 -->
                        <img src='../image.php?id=9163' style='width:17px; cursor:pointer; position:absolute; top:80px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9163' class="asset-image" onclick='fetchAssetData(9163);' data-id="<?php echo $assetId9163; ?>" data-room=" <?php echo htmlspecialchars($room9163); ?>" data-floor="<?php echo htmlspecialchars($floor9163); ?>" data-image=" <?php echo base64_encode($upload_img9163); ?>" data-category="<?php echo htmlspecialchars($category9163); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9163); ?>" data-status="<?php echo htmlspecialchars($status9163); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9163); ?>; 
position:absolute; top:80px; left:265px;'>
                        </div>


                        <!-- ASSET 9164 -->
                        <img src='../image.php?id=9164' style='width:17px; cursor:pointer; position:absolute; top:80px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9164' class="asset-image" onclick='fetchAssetData(9164);' data-id="<?php echo $assetId9164; ?>" data-room="<?php echo htmlspecialchars($room9164); ?>" data-floor="<?php echo htmlspecialchars($floor9164); ?>" data-image="<?php echo base64_encode($upload_img9164); ?>" data-category="<?php echo htmlspecialchars($category9164); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9164); ?>" data-status="<?php echo htmlspecialchars($status9164); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9164); ?>; 
position:absolute; top:80px; left:285px;'>
                        </div>

                        <!-- ASSET 9165 -->
                        <img src='../image.php?id=9165' style='width:17px; cursor:pointer; position:absolute; top:100px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9165' class="asset-image" onclick='fetchAssetData(9165);' data-id="<?php echo $assetId9165; ?>" data-room="<?php echo htmlspecialchars($room9165); ?>" data-floor="<?php echo htmlspecialchars($floor9165); ?>" data-image="<?php echo base64_encode($upload_img9165); ?>" data-category="<?php echo htmlspecialchars($category9165); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9165); ?>" data-status="<?php echo htmlspecialchars($status9165); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9165); ?>; 
position:absolute; top:100px; left:205px;'>
                        </div>

                        <!-- ASSET 9166 -->
                        <img src='../image.php?id=9166' style='width:17px; cursor:pointer; position:absolute; top:100px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9166' class="asset-image" onclick='fetchAssetData(9166);' data-id="<?php echo $assetId9166; ?>" data-room="<?php echo htmlspecialchars($room9166); ?>" data-floor="<?php echo htmlspecialchars($floor9166); ?>" data-image="<?php echo base64_encode($upload_img9166); ?>" data-category="<?php echo htmlspecialchars($category9166); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9166); ?>" data-status="<?php echo htmlspecialchars($status9166); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9166); ?>; 
position:absolute; top:100px; left:225px;'>
                        </div>

                        <!-- ASSET 9167 -->
                        <img src='../image.php?id=9167' style='width:17px; cursor:pointer; position:absolute; top:100px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9167' class="asset-image" onclick='fetchAssetData(9167);' data-id="<?php echo $assetId9167; ?>" data-room="<?php echo htmlspecialchars($room9167); ?>" data-floor="<?php echo htmlspecialchars($floor9167); ?>" data-image="<?php echo base64_encode($upload_img9167); ?>" data-category="<?php echo htmlspecialchars($category9167); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9167); ?>" data-status="<?php echo htmlspecialchars($status9167); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9167); ?>; 
position:absolute; top:100px; left:245px;'>
                        </div>


                        <!-- ASSET 9168 -->
                        <img src='../image.php?id=9168' style='width:17px; cursor:pointer; position:absolute; top:100px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9168' class="asset-image" onclick='fetchAssetData(9168);' data-id="<?php echo $assetId9168; ?>" data-room="<?php echo htmlspecialchars($room9168); ?>" data-floor="<?php echo htmlspecialchars($floor9168); ?>" data-image="<?php echo base64_encode($upload_img9168); ?>" data-category="<?php echo htmlspecialchars($category9168); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9168); ?>" data-status="<?php echo htmlspecialchars($status9168); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9168); ?>; 
position:absolute; top:100px; left:265px;'>
                        </div>

                        <!-- ASSET 9169 -->
                        <img src='../image.php?id=9169' style='width:17px; cursor:pointer; position:absolute; top:100px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9169' class="asset-image" onclick='fetchAssetData(9169);' data-id="<?php echo $assetId9169; ?>" data-room="<?php echo htmlspecialchars($room9169); ?>" data-floor="<?php echo htmlspecialchars($floor9169); ?>" data-image="<?php echo base64_encode($upload_img9169); ?>" data-category="<?php echo htmlspecialchars($category9169); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9169); ?>" data-status="<?php echo htmlspecialchars($status9169); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9169); ?>; 
position:absolute; top:100px; left:285px;'>
                        </div>

                        <!-- END OF ROW 2 -->

                        <!-- ASSET 9170 -->
                        <img src='../image.php?id=9170' style='width:17px; cursor:pointer; position:absolute; top:120px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9170' class="asset-image" onclick='fetchAssetData(9170);' data-id="<?php echo $assetId9170; ?>" data-room="<?php echo htmlspecialchars($room9170); ?>" data-floor="<?php echo htmlspecialchars($floor9170); ?>" data-image="<?php echo base64_encode($upload_img9170); ?>" data-category="<?php echo htmlspecialchars($category9170); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9170); ?>" data-status="<?php echo htmlspecialchars($status9170); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9170); ?>; 
position:absolute; top:120px; left:205px;'>
                        </div>

                        <!-- ASSET 9171 -->
                        <img src='../image.php?id=9171' style='width:17px; cursor:pointer; position:absolute; top:120px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9171' class="asset-image" onclick='fetchAssetData(9171);' data-id="<?php echo $assetId9171; ?>" data-room="<?php echo htmlspecialchars($room9171); ?>" data-floor="<?php echo htmlspecialchars($floor9171); ?>" data-image="<?php echo base64_encode($upload_img9171); ?>" data-category="<?php echo htmlspecialchars($category9171); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9171); ?>" data-status="<?php echo htmlspecialchars($status9171); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9171); ?>; 
position:absolute; top:120px; left:225px;'>
                        </div>


                        <!-- ASSET 9172 -->
                        <img src='../image.php?id=9172' style='width:17px; cursor:pointer; position:absolute; top:120px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9172' class="asset-image" onclick='fetchAssetData(9172);' data-id="<?php echo $assetId9172; ?>" data-room="<?php echo htmlspecialchars($room9172); ?>" data-floor="<?php echo htmlspecialchars($floor9172); ?>" data-image="<?php echo base64_encode($upload_img9172); ?>" data-category="<?php echo htmlspecialchars($category9172); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9172); ?>" data-status="<?php echo htmlspecialchars($status9172); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9172); ?>; 
position:absolute; top:120px; left:245px;'>
                        </div>

                        <!-- ASSET 9173 -->
                        <img src='../image.php?id=9173' style='width:17px; cursor:pointer; position:absolute; top:120px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9173' class="asset-image" onclick='fetchAssetData(9173);' data-id="<?php echo $assetId9173; ?>" data-room="<?php echo htmlspecialchars($room9173); ?>" data-floor="<?php echo htmlspecialchars($floor9173); ?>" data-image="<?php echo base64_encode($upload_img9173); ?>" data-category="<?php echo htmlspecialchars($category9173); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9173); ?>" data-status="<?php echo htmlspecialchars($status9173); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9173); ?>; 
position:absolute; top:120px; left:265px;'>
                        </div>

                        <!-- ASSET 9174 -->
                        <img src='../image.php?id=9174' style='width:17px; cursor:pointer; position:absolute; top:120px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9174' class="asset-image" onclick='fetchAssetData(9174);' data-id="<?php echo $assetId9174; ?>" data-room="<?php echo htmlspecialchars($room9174); ?>" data-floor="<?php echo htmlspecialchars($floor9174); ?>" data-image="<?php echo base64_encode($upload_img9174); ?>" data-category="<?php echo htmlspecialchars($category9174); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9174); ?>" data-status="<?php echo htmlspecialchars($status9174); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9174); ?>; 
position:absolute; top:120px; left:285px;'>
                        </div>

                        <!-- END OF ROW 3 -->

                        <!-- ASSET 9175 -->
                        <img src='../image.php?id=9175' style='width:17px; cursor:pointer; position:absolute; top:140px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9175' class="asset-image" onclick='fetchAssetData(9175);' data-id="<?php echo $assetId9175; ?>" data-room="<?php echo htmlspecialchars($room9175); ?>" data-floor="<?php echo htmlspecialchars($floor9175); ?>" data-image="<?php echo base64_encode($upload_img9175); ?>" data-category="<?php echo htmlspecialchars($category9175); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9175); ?>" data-status="<?php echo htmlspecialchars($status9175); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9175); ?>; 
position:absolute; top:140px; left:205px;'>
                        </div>


                        <!-- ASSET 9176 -->
                        <img src='../image.php?id=9176' style='width:17px; cursor:pointer; position:absolute; top:140px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9176' class="asset-image" onclick='fetchAssetData(9176);' data-id="<?php echo $assetId9176; ?>" data-room="<?php echo htmlspecialchars($room9176); ?>" data-floor="<?php echo htmlspecialchars($floor9176); ?>" data-image="<?php echo base64_encode($upload_img9176); ?>" data-category="<?php echo htmlspecialchars($category9176); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9176); ?>" data-status="<?php echo htmlspecialchars($status9176); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9176); ?>; 
position:absolute; top:140px; left:225px;'>
                        </div>

                        <!-- ASSET 9177 -->
                        <img src='../image.php?id=9177' style='width:17px; cursor:pointer; position:absolute; top:140px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9177' class="asset-image" onclick='fetchAssetData(9177);' data-id="<?php echo $assetId9177; ?>" data-room="<?php echo htmlspecialchars($room9177); ?>" data-floor="<?php echo htmlspecialchars($floor9177); ?>" data-image="<?php echo base64_encode($upload_img9177); ?>" data-category="<?php echo htmlspecialchars($category9177); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9177); ?>" data-status="<?php echo htmlspecialchars($status9177); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9177); ?>; 
position:absolute; top:140px; left:245px;'>
                        </div>

                        <!-- ASSET 9178 -->
                        <img src='../image.php?id=9178' style='width:17px; cursor:pointer; position:absolute; top:140px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9178' class="asset-image" onclick='fetchAssetData(9178);' data-id="<?php echo $assetId9178; ?>" data-room="<?php echo htmlspecialchars($room9178); ?>" data-floor="<?php echo htmlspecialchars($floor9178); ?>" data-image="<?php echo base64_encode($upload_img9178); ?>" data-category="<?php echo htmlspecialchars($category9178); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9178); ?>" data-status="<?php echo htmlspecialchars($status9178); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9178); ?>; 
position:absolute; top:140px; left:265px;'>
                        </div>

                        <!-- ASSET 9179 -->
                        <img src='../image.php?id=9179' style='width:17px; cursor:pointer; position:absolute; top:140px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9179' class="asset-image" onclick='fetchAssetData(9179);' data-id="<?php echo $assetId9179; ?>" data-room="<?php echo htmlspecialchars($room9179); ?>" data-floor="<?php echo htmlspecialchars($floor9179); ?>" data-image="<?php echo base64_encode($upload_img9179); ?>" data-category="<?php echo htmlspecialchars($category9179); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9179); ?>" data-status="<?php echo htmlspecialchars($status9179); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9179); ?>; 
position:absolute; top:140px; left:285px;'>
                        </div>

                        <!-- END OF ROW 4 -->

                        <!-- ASSET 9180 -->
                        <img src='../image.php?id=9180' style='width:17px; cursor:pointer; position:absolute; top:160px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9180' class="asset-image" onclick='fetchAssetData(9180);' data-id="<?php echo $assetId9180; ?>" data-room="<?php echo htmlspecialchars($room9180); ?>" data-floor="<?php echo htmlspecialchars($floor9180); ?>" data-image="<?php echo base64_encode($upload_img9180); ?>" data-category="<?php echo htmlspecialchars($category9180); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9180); ?>" data-status="<?php echo htmlspecialchars($status9180); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9180); ?>; 
position:absolute; top:160px; left:205px;'>
                        </div>

                        <!-- ASSET 9181 -->
                        <img src='../image.php?id=9181' style='width:17px; cursor:pointer; position:absolute; top:160px; left:215px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9181' class="asset-image" onclick='fetchAssetData(9181);' data-id="<?php echo $assetId9181; ?>" data-room="<?php echo htmlspecialchars($room9181); ?>" data-floor="<?php echo htmlspecialchars($floor9181); ?>" data-image="<?php echo base64_encode($upload_img9181); ?>" data-category="<?php echo htmlspecialchars($category9181); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9181); ?>" data-status="<?php echo htmlspecialchars($status9181); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9181); ?>; 
position:absolute; top:160px; left:225px;'>
                        </div>

                        <!-- ASSET 9182 -->
                        <img src='../image.php?id=9182' style='width:17px; cursor:pointer; position:absolute; top:160px; left:235px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9182' class="asset-image" onclick='fetchAssetData(9182);' data-id="<?php echo $assetId9182; ?>" data-room="<?php echo htmlspecialchars($room9182); ?>" data-floor="<?php echo htmlspecialchars($floor9182); ?>" data-image="<?php echo base64_encode($upload_img9182); ?>" data-category="<?php echo htmlspecialchars($category9182); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9182); ?>" data-status="<?php echo htmlspecialchars($status9182); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9182); ?>; 
position:absolute; top:160px; left:245px;'>
                        </div>

                        <!-- ASSET 9183 -->
                        <img src='../image.php?id=9183' style='width:17px; cursor:pointer; position:absolute; top:160px; left:255px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9183' class="asset-image" onclick='fetchAssetData(9183);' data-id="<?php echo $assetId9183; ?>" data-room="<?php echo htmlspecialchars($room9183); ?>" data-floor="<?php echo htmlspecialchars($floor9183); ?>" data-image="<?php echo base64_encode($upload_img9183); ?>" data-category="<?php echo htmlspecialchars($category9183); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9183); ?>" data-status="<?php echo htmlspecialchars($status9183); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9183); ?>; 
position:absolute; top:160px; left:265px;'>
                        </div>


                        <!-- ASSET 9184 -->
                        <img src='../image.php?id=9184' style='width:17px; cursor:pointer; position:absolute; top:160px; left:275px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9184' class="asset-image" onclick='fetchAssetData(9184);' data-id="<?php echo $assetId9184; ?>" data-room="<?php echo htmlspecialchars($room9184); ?>" data-floor="<?php echo htmlspecialchars($floor9184); ?>" data-image="<?php echo base64_encode($upload_img9184); ?>" data-category="<?php echo htmlspecialchars($category9184); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9184); ?>" data-status="<?php echo htmlspecialchars($status9184); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9184); ?>; 
position:absolute; top:160px; left:285px;'>
                        </div>

                        <!-- END OF ROW 5 -->

                        <!-- ASSET 9185 -->
                        <img src='../image.php?id=9185' style='width:17px; cursor:pointer; position:absolute; top:210px; left:235px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9185' class="asset-image" onclick='fetchAssetData(9185);' data-id="<?php echo $assetId9185; ?>" data-room="<?php echo htmlspecialchars($room9185); ?>" data-floor="<?php echo htmlspecialchars($floor9185); ?>" data-image="<?php echo base64_encode($upload_img9185); ?>" data-category="<?php echo htmlspecialchars($category9185); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9185); ?>" data-status="<?php echo htmlspecialchars($status9185); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9185); ?>; 
position:absolute; top:220px; left:235px;'>
                        </div>

                        <!-- END OF IB108A -->

                        <!-- START OF IB109A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 9186 -->
                        <img src='../image.php?id=9186' style='width:17px; cursor:pointer; position:absolute; top:100px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9186' class="asset-image" onclick='fetchAssetData(9186);' data-id="<?php echo $assetId9186; ?>" data-room="<?php echo htmlspecialchars($room9186); ?>" data-floor="<?php echo htmlspecialchars($floor9186); ?>" data-image="<?php echo base64_encode($upload_img9186); ?>" data-category="<?php echo htmlspecialchars($category9186); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9186); ?>" data-status="<?php echo htmlspecialchars($status9186); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9186); ?>; 
position:absolute; top:100px; left:75px;'>
                        </div>

                        <!-- ASSET 9187 -->
                        <img src='../image.php?id=9187' style='width:17px; cursor:pointer; position:absolute; top:100px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9187' class="asset-image" onclick='fetchAssetData(9187);' data-id="<?php echo $assetId9187; ?>" data-room="<?php echo htmlspecialchars($room9187); ?>" data-floor="<?php echo htmlspecialchars($floor9187); ?>" data-image="<?php echo base64_encode($upload_img9187); ?>" data-category="<?php echo htmlspecialchars($category9187); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9187); ?>" data-status="<?php echo htmlspecialchars($status9187); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9187); ?>; 
position:absolute; top:100px; left:95px;'>
                        </div>


                        <!-- ASSET 9188 -->
                        <img src='../image.php?id=9188' style='width:17px; cursor:pointer; position:absolute; top:100px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9188' class="asset-image" onclick='fetchAssetData(9188);' data-id="<?php echo $assetId9188; ?>" data-room="<?php echo htmlspecialchars($room9188); ?>" data-floor="<?php echo htmlspecialchars($floor9188); ?>" data-image="<?php echo base64_encode($upload_img9188); ?>" data-category="<?php echo htmlspecialchars($category9188); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9188); ?>" data-status="<?php echo htmlspecialchars($status9188); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9188); ?>; 
position:absolute; top:100px; left:115px;'>
                        </div>

                        <!-- ASSET 9189 -->
                        <img src='../image.php?id=9189' style='width:17px; cursor:pointer; position:absolute; top:100px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9189' class="asset-image" onclick='fetchAssetData(9189);' data-id="<?php echo $assetId9189; ?>" data-room="<?php echo htmlspecialchars($room9189); ?>" data-floor="<?php echo htmlspecialchars($floor9189); ?>" data-image="<?php echo base64_encode($upload_img9189); ?>" data-category="<?php echo htmlspecialchars($category9189); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9189); ?>" data-status="<?php echo htmlspecialchars($status9189); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9189); ?>; 
position:absolute; top:100px; left:135px;'>
                        </div>

                        <!-- ASSET 9190 -->
                        <img src='../image.php?id=9190' style='width:17px; cursor:pointer; position:absolute; top:100px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9190' class="asset-image" onclick='fetchAssetData(9190);' data-id="<?php echo $assetId9190; ?>" data-room="<?php echo htmlspecialchars($room9190); ?>" data-floor="<?php echo htmlspecialchars($floor9190); ?>" data-image="<?php echo base64_encode($upload_img9190); ?>" data-category="<?php echo htmlspecialchars($category9190); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9190); ?>" data-status="<?php echo htmlspecialchars($status9190); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9190); ?>; 
position:absolute; top:100px; left:155px;'>
                        </div>

                        <!-- END OF ROW 1 -->

                        <!-- ASSET 9191 -->
                        <img src='../image.php?id=9191' style='width:17px; cursor:pointer; position:absolute; top:120px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9191' class="asset-image" onclick='fetchAssetData(9191);' data-id="<?php echo $assetId9191; ?>" data-room="<?php echo htmlspecialchars($room9191); ?>" data-floor="<?php echo htmlspecialchars($floor9191); ?>" data-image="<?php echo base64_encode($upload_img9191); ?>" data-category="<?php echo htmlspecialchars($category9191); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9191); ?>" data-status="<?php echo htmlspecialchars($status9191); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9191); ?>; 
position:absolute; top:120px; left:75px;'>
                        </div>


                        <!-- ASSET 9192 -->
                        <img src='../image.php?id=9192' style='width:17px; cursor:pointer; position:absolute; top:120px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9192' class="asset-image" onclick='fetchAssetData(9192);' data-id="<?php echo $assetId9192; ?>" data-room="<?php echo htmlspecialchars($room9192); ?>" data-floor="<?php echo htmlspecialchars($floor9192); ?>" data-image="<?php echo base64_encode($upload_img9192); ?>" data-category="<?php echo htmlspecialchars($category9192); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9192); ?>" data-status="<?php echo htmlspecialchars($status9192); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9192); ?>; 
position:absolute; top:120px; left:95px;'>
                        </div>

                        <!-- ASSET 9193 -->
                        <img src='../image.php?id=9193' style='width:17px; cursor:pointer; position:absolute; top:120px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9193' class="asset-image" onclick='fetchAssetData(9193);' data-id="<?php echo $assetId9193; ?>" data-room="<?php echo htmlspecialchars($room9193); ?>" data-floor="<?php echo htmlspecialchars($floor9193); ?>" data-image="<?php echo base64_encode($upload_img9193); ?>" data-category="<?php echo htmlspecialchars($category9193); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9193); ?>" data-status="<?php echo htmlspecialchars($status9193); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9193); ?>; 
position:absolute; top:120px; left:115px;'>
                        </div>

                        <!-- ASSET 9194 -->
                        <img src='../image.php?id=9194' style='width:17px; cursor:pointer; position:absolute; top:120px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9194' class="asset-image" onclick='fetchAssetData(9194);' data-id="<?php echo $assetId9194; ?>" data-room="<?php echo htmlspecialchars($room9194); ?>" data-floor="<?php echo htmlspecialchars($floor9194); ?>" data-image="<?php echo base64_encode($upload_img9194); ?>" data-category="<?php echo htmlspecialchars($category9194); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9194); ?>" data-status="<?php echo htmlspecialchars($status9194); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9194); ?>; 
position:absolute; top:120px; left:135px;'>
                        </div>

                        <!-- ASSET 9195 -->
                        <img src='../image.php?id=9195' style='width:17px; cursor:pointer; position:absolute; top:120px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9195' class="asset-image" onclick='fetchAssetData(9195);' data-id="<?php echo $assetId9195; ?>" data-room="<?php echo htmlspecialchars($room9195); ?>" data-floor="<?php echo htmlspecialchars($floor9195); ?>" data-image="<?php echo base64_encode($upload_img9195); ?>" data-category="<?php echo htmlspecialchars($category9195); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9195); ?>" data-status="<?php echo htmlspecialchars($status9195); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9195); ?>; 
position:absolute; top:120px; left:155px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 9196 -->
                        <img src='../image.php?id=9196' style='width:17px; cursor:pointer; position:absolute; top:140px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9196' class="asset-image" onclick='fetchAssetData(9196);' data-id="<?php echo $assetId9196; ?>" data-room="<?php echo htmlspecialchars($room9196); ?>" data-floor="<?php echo htmlspecialchars($floor9196); ?>" data-image="<?php echo base64_encode($upload_img9196); ?>" data-category="<?php echo htmlspecialchars($category9196); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9196); ?>" data-status="<?php echo htmlspecialchars($status9196); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9196); ?>; 
position:absolute; top:140px; left:75px;'>
                        </div>

                        <!-- ASSET 9197 -->
                        <img src='../image.php?id=9197' style='width:17px; cursor:pointer; position:absolute; top:140px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9197' class="asset-image" onclick='fetchAssetData(9197);' data-id="<?php echo $assetId9197; ?>" data-room="<?php echo htmlspecialchars($room9197); ?>" data-floor="<?php echo htmlspecialchars($floor9197); ?>" data-image="<?php echo base64_encode($upload_img9197); ?>" data-category="<?php echo htmlspecialchars($category9197); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9197); ?>" data-status="<?php echo htmlspecialchars($status9197); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9197); ?>; 
position:absolute; top:140px; left:95px;'>
                        </div>

                        <!-- ASSET 9198 -->
                        <img src='../image.php?id=9198' style='width:17px; cursor:pointer; position:absolute; top:140px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9198' class="asset-image" onclick='fetchAssetData(9198);' data-id="<?php echo $assetId9198; ?>" data-room="<?php echo htmlspecialchars($room9198); ?>" data-floor="<?php echo htmlspecialchars($floor9198); ?>" data-image="<?php echo base64_encode($upload_img9198); ?>" data-category="<?php echo htmlspecialchars($category9198); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9198); ?>" data-status="<?php echo htmlspecialchars($status9198); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9198); ?>; 
position:absolute; top:140px; left:115px;'>
                        </div>

                        <!-- ASSET 9199 -->
                        <img src='../image.php?id=9199' style='width:17px; cursor:pointer; position:absolute; top:140px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9199' class="asset-image" onclick='fetchAssetData(9199);' data-id="<?php echo $assetId9199; ?>" data-room="<?php echo htmlspecialchars($room9199); ?>" data-floor="<?php echo htmlspecialchars($floor9199); ?>" data-image="<?php echo base64_encode($upload_img9199); ?>" data-category="<?php echo htmlspecialchars($category9199); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9199); ?>" data-status="<?php echo htmlspecialchars($status9199); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9199); ?>; 
position:absolute; top:140px; left:135px;'>
                        </div>


                        <!-- ASSET 9200 -->
                        <img src='../image.php?id=9200' style='width:17px; cursor:pointer; position:absolute; top:140px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9200' class="asset-image" onclick='fetchAssetData(9200);' data-id="<?php echo $assetId9200; ?>" data-room="<?php echo htmlspecialchars($room9200); ?>" data-floor="<?php echo htmlspecialchars($floor9200); ?>" data-image="<?php echo base64_encode($upload_img9200); ?>" data-category="<?php echo htmlspecialchars($category9200); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9200); ?>" data-status="<?php echo htmlspecialchars($status9200); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9200); ?>; 
position:absolute; top:140px; left:155px;'>
                        </div>

                        <!-- ASSET 9201 -->
                        <img src='../image.php?id=9201' style='width:17px; cursor:pointer; position:absolute; top:160px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9201' class="asset-image" onclick='fetchAssetData(9201);' data-id="<?php echo $assetId9201; ?>" data-room="<?php echo htmlspecialchars($room9201); ?>" data-floor="<?php echo htmlspecialchars($floor9201); ?>" data-image="<?php echo base64_encode($upload_img9201); ?>" data-category="<?php echo htmlspecialchars($category9201); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9201); ?>" data-status="<?php echo htmlspecialchars($status9201); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9201); ?>; 
position:absolute; top:160px; left:75px;'>
                        </div>

                        <!-- ASSET 9202 -->
                        <img src='../image.php?id=9202' style='width:17px; cursor:pointer; position:absolute; top:160px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9202' class="asset-image" onclick='fetchAssetData(9202);' data-id="<?php echo $assetId9202; ?>" data-room="<?php echo htmlspecialchars($room9202); ?>" data-floor="<?php echo htmlspecialchars($floor9202); ?>" data-image="<?php echo base64_encode($upload_img9202); ?>" data-category="<?php echo htmlspecialchars($category9202); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9202); ?>" data-status="<?php echo htmlspecialchars($status9202); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9202); ?>; 
position:absolute; top:160px; left:95px;'>
                        </div>

                        <!-- ASSET 9203 -->
                        <img src='../image.php?id=9203' style='width:17px; cursor:pointer; position:absolute; top:160px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9203' class="asset-image" onclick='fetchAssetData(9203);' data-id="<?php echo $assetId9203; ?>" data-room="<?php echo htmlspecialchars($room9203); ?>" data-floor="<?php echo htmlspecialchars($floor9203); ?>" data-image="<?php echo base64_encode($upload_img9203); ?>" data-category="<?php echo htmlspecialchars($category9203); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9203); ?>" data-status="<?php echo htmlspecialchars($status9203); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9203); ?>; 
position:absolute; top:160px; left:115px;'>
                        </div>


                        <!-- ASSET 9204 -->
                        <img src='../image.php?id=9204' style='width:17px; cursor:pointer; position:absolute; top:160px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9204' class="asset-image" onclick='fetchAssetData(9204);' data-id="<?php echo $assetId9204; ?>" data-room="<?php echo htmlspecialchars($room9204); ?>" data-floor="<?php echo htmlspecialchars($floor9204); ?>" data-image="<?php echo base64_encode($upload_img9204); ?>" data-category="<?php echo htmlspecialchars($category9204); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9204); ?>" data-status="<?php echo htmlspecialchars($status9204); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9204); ?>; 
position:absolute; top:160px; left:135px;'>
                        </div>

                        <!-- ASSET 9205 -->
                        <img src='../image.php?id=9205' style='width:17px; cursor:pointer; position:absolute; top:160px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9205' class="asset-image" onclick='fetchAssetData(9205);' data-id="<?php echo $assetId9205; ?>" data-room="<?php echo htmlspecialchars($room9205); ?>" data-floor="<?php echo htmlspecialchars($floor9205); ?>" data-image="<?php echo base64_encode($upload_img9205); ?>" data-category="<?php echo htmlspecialchars($category9205); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9205); ?>" data-status="<?php echo htmlspecialchars($status9205); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9205); ?>; 
position:absolute; top:160px; left:155px;'>
                        </div>

                        <!-- ASSET 9206 -->
                        <img src='../image.php?id=9206' style='width:17px; cursor:pointer; position:absolute; top:180px; left:65px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9206' class="asset-image" onclick='fetchAssetData(9206);' data-id="<?php echo $assetId9206; ?>" data-room="<?php echo htmlspecialchars($room9206); ?>" data-floor="<?php echo htmlspecialchars($floor9206); ?>" data-image="<?php echo base64_encode($upload_img9206); ?>" data-category="<?php echo htmlspecialchars($category9206); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9206); ?>" data-status="<?php echo htmlspecialchars($status9206); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9206); ?>; 
position:absolute; top:180px; left:75px;'>
                        </div>

                        <!-- ASSET 9207 -->
                        <img src='../image.php?id=9207' style='width:17px; cursor:pointer; position:absolute; top:180px; left:85px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9207' class="asset-image" onclick='fetchAssetData(9207);' data-id="<?php echo $assetId9207; ?>" data-room="<?php echo htmlspecialchars($room9207); ?>" data-floor="<?php echo htmlspecialchars($floor9207); ?>" data-image="<?php echo base64_encode($upload_img9207); ?>" data-category="<?php echo htmlspecialchars($category9207); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9207); ?>" data-status="<?php echo htmlspecialchars($status9207); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9207); ?>; 
position:absolute; top:180px; left:95px;'>
                        </div>


                        <!-- ASSET 9208 -->
                        <img src='../image.php?id=9208' style='width:17px; cursor:pointer; position:absolute; top:180px; left:105px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9208' class="asset-image" onclick='fetchAssetData(9208);' data-id="<?php echo $assetId9208; ?>" data-room="<?php echo htmlspecialchars($room9208); ?>" data-floor="<?php echo htmlspecialchars($floor9208); ?>" data-image="<?php echo base64_encode($upload_img9208); ?>" data-category="<?php echo htmlspecialchars($category9208); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9208); ?>" data-status="<?php echo htmlspecialchars($status9208); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9208); ?>; 
position:absolute; top:180px; left:115px;'>
                        </div>

                        <!-- ASSET 9209 -->
                        <img src='../image.php?id=9209' style='width:17px; cursor:pointer; position:absolute; top:180px; left:125px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9209' class="asset-image" onclick='fetchAssetData(9209);' data-id="<?php echo $assetId9209; ?>" data-room="<?php echo htmlspecialchars($room9209); ?>" data-floor="<?php echo htmlspecialchars($floor9209); ?>" data-image="<?php echo base64_encode($upload_img9209); ?>" data-category="<?php echo htmlspecialchars($category9209); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9209); ?>" data-status="<?php echo htmlspecialchars($status9209); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9209); ?>; 
position:absolute; top:180px; left:135px;'>
                        </div>

                        <!-- ASSET 9210 -->
                        <img src='../image.php?id=9210' style='width:17px; cursor:pointer; position:absolute; top:180px; left:145px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9210' class="asset-image" onclick='fetchAssetData(9210);' data-id="<?php echo $assetId9210; ?>" data-room="<?php echo htmlspecialchars($room9210); ?>" data-floor="<?php echo htmlspecialchars($floor9210); ?>" data-image="<?php echo base64_encode($upload_img9210); ?>" data-category="<?php echo htmlspecialchars($category9210); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9210); ?>" data-status="<?php echo htmlspecialchars($status9210); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9210); ?>; 
position:absolute; top:180px; left:155px;'>
                        </div>

                        <!-- ASSET 9211 -->
                        <img src='../image.php?id=9211' style='width:17px; cursor:pointer; position:absolute; top:210px; left:105px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9211' class="asset-image" onclick='fetchAssetData(9211);' data-id="<?php echo $assetId9211; ?>" data-room="<?php echo htmlspecialchars($room9211); ?>" data-floor="<?php echo htmlspecialchars($floor9211); ?>" data-image="<?php echo base64_encode($upload_img9211); ?>" data-category="<?php echo htmlspecialchars($category9211); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9211); ?>" data-status="<?php echo htmlspecialchars($status9211); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9211); ?>; 
position:absolute; top:220px; left:105px;'>
                        </div>


                        <!-- END OF IB109A -->

                        <!-- START OF IB110A -->

                        <!-- START OF ROW 1 -->

                        <!-- ASSET 9212 -->
                        <img src='../image.php?id=9212' style='width:17px; cursor:pointer; position:absolute; top:405px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9212' class="asset-image" onclick='fetchAssetData(9212);' data-id="<?php echo $assetId9212; ?>" data-room="<?php echo htmlspecialchars($room9212); ?>" data-floor="<?php echo htmlspecialchars($floor9212); ?>" data-image="<?php echo base64_encode($upload_img9212); ?>" data-category="<?php echo htmlspecialchars($category9212); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9212); ?>" data-status="<?php echo htmlspecialchars($status9212); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9212); ?>; 
position:absolute; top:405px; left:65px;'>
                        </div>

                        <!-- ASSET 9213 -->
                        <img src='../image.php?id=9213' style='width:17px; cursor:pointer; position:absolute; top:425px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9213' class="asset-image" onclick='fetchAssetData(9213);' data-id="<?php echo $assetId9213; ?>" data-room="<?php echo htmlspecialchars($room9213); ?>" data-floor="<?php echo htmlspecialchars($floor9213); ?>" data-image="<?php echo base64_encode($upload_img9213); ?>" data-category="<?php echo htmlspecialchars($category9213); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9213); ?>" data-status="<?php echo htmlspecialchars($status9213); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9213); ?>; 
position:absolute; top:425px; left:65px;'>
                        </div>

                        <!-- ASSET 9214 -->
                        <img src='../image.php?id=9214' style='width:17px; cursor:pointer; position:absolute; top:445px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9214' class="asset-image" onclick='fetchAssetData(9214);' data-id="<?php echo $assetId9214; ?>" data-room="<?php echo htmlspecialchars($room9214); ?>" data-floor="<?php echo htmlspecialchars($floor9214); ?>" data-image="<?php echo base64_encode($upload_img9214); ?>" data-category="<?php echo htmlspecialchars($category9214); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9214); ?>" data-status="<?php echo htmlspecialchars($status9214); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9214); ?>; 
position:absolute; top:445px; left:65px;'>
                        </div>

                        <!-- ASSET 9215 -->
                        <img src='../image.php?id=9215' style='width:17px; cursor:pointer; position:absolute; top:465px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9215' class="asset-image" onclick='fetchAssetData(9215);' data-id="<?php echo $assetId9215; ?>" data-room="<?php echo htmlspecialchars($room9215); ?>" data-floor="<?php echo htmlspecialchars($floor9215); ?>" data-image="<?php echo base64_encode($upload_img9215); ?>" data-category="<?php echo htmlspecialchars($category9215); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9215); ?>" data-status="<?php echo htmlspecialchars($status9215); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9215); ?>; 
position:absolute; top:465px; left:65px;'>
                        </div>

                        <!-- ASSET 9216 -->
                        <img src='../image.php?id=9216' style='width:17px; cursor:pointer; position:absolute; top:485px; left:65px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9216' class="asset-image" onclick='fetchAssetData(9216);' data-id="<?php echo $assetId9216; ?>" data-room="<?php echo htmlspecialchars($room9216); ?>" data-floor="<?php echo htmlspecialchars($floor9216); ?>" data-image="<?php echo base64_encode($upload_img9216); ?>" data-category="<?php echo htmlspecialchars($category9216); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9216); ?>" data-status="<?php echo htmlspecialchars($status9216); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9216); ?>; 
position:absolute; top:485px; left:65px;'>
                        </div>


                        <!-- END OF ROW 1 -->

                        <!-- ASSET 9217 -->
                        <img src='../image.php?id=9217' style='width:17px; cursor:pointer; position:absolute; top:405px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9217' class="asset-image" onclick='fetchAssetData(9217);' data-id="<?php echo $assetId9217; ?>" data-room="<?php echo htmlspecialchars($room9217); ?>" data-floor="<?php echo htmlspecialchars($floor9217); ?>" data-image="<?php echo base64_encode($upload_img9217); ?>" data-category="<?php echo htmlspecialchars($category9217); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9217); ?>" data-status="<?php echo htmlspecialchars($status9217); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9217); ?>; 
position:absolute; top:405px; left:85px;'>
                        </div>

                        <!-- ASSET 9218 -->
                        <img src='../image.php?id=9218' style='width:17px; cursor:pointer; position:absolute; top:425px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9218' class="asset-image" onclick='fetchAssetData(9218);' data-id="<?php echo $assetId9218; ?>" data-room="<?php echo htmlspecialchars($room9218); ?>" data-floor="<?php echo htmlspecialchars($floor9218); ?>" data-image="<?php echo base64_encode($upload_img9218); ?>" data-category="<?php echo htmlspecialchars($category9218); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9218); ?>" data-status="<?php echo htmlspecialchars($status9218); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9218); ?>; 
position:absolute; top:425px; left:85px;'>
                        </div>

                        <!-- ASSET 9219 -->
                        <img src='../image.php?id=9219' style='width:17px; cursor:pointer; position:absolute; top:445px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9219' class="asset-image" onclick='fetchAssetData(9219);' data-id="<?php echo $assetId9219; ?>" data-room="<?php echo htmlspecialchars($room9219); ?>" data-floor="<?php echo htmlspecialchars($floor9219); ?>" data-image="<?php echo base64_encode($upload_img9219); ?>" data-category="<?php echo htmlspecialchars($category9219); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9219); ?>" data-status="<?php echo htmlspecialchars($status9219); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9219); ?>; 
position:absolute; top:445px; left:85px;'>
                        </div>

                        <!-- ASSET 9220 -->
                        <img src='../image.php?id=9220' style='width:17px; cursor:pointer; position:absolute; top:465px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9220' class="asset-image" onclick='fetchAssetData(9220);' data-id="<?php echo $assetId9220; ?>" data-room="<?php echo htmlspecialchars($room9220); ?>" data-floor="<?php echo htmlspecialchars($floor9220); ?>" data-image="<?php echo base64_encode($upload_img9220); ?>" data-category="<?php echo htmlspecialchars($category9220); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9220); ?>" data-status="<?php echo htmlspecialchars($status9220); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9220); ?>; 
position:absolute; top:465px; left:85px;'>
                        </div>

                        <!-- ASSET 9221 -->
                        <img src='../image.php?id=9221' style='width:17px; cursor:pointer; position:absolute; top:485px; left:85px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9221' class="asset-image" onclick='fetchAssetData(9221);' data-id="<?php echo $assetId9221; ?>" data-room="<?php echo htmlspecialchars($room9221); ?>" data-floor="<?php echo htmlspecialchars($floor9221); ?>" data-image="<?php echo base64_encode($upload_img9221); ?>" data-category="<?php echo htmlspecialchars($category9221); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9221); ?>" data-status="<?php echo htmlspecialchars($status9221); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9221); ?>; 
position:absolute; top:485px; left:85px;'>
                        </div>


                        <!-- END OF ROW 2 -->

                        <!-- ASSET 9222 -->
                        <img src='../image.php?id=9222' style='width:17px; cursor:pointer; position:absolute; top:405px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9222' class="asset-image" onclick='fetchAssetData(9222);' data-id="<?php echo $assetId9222; ?>" data-room="<?php echo htmlspecialchars($room9222); ?>" data-floor="<?php echo htmlspecialchars($floor9222); ?>" data-image="<?php echo base64_encode($upload_img9222); ?>" data-category="<?php echo htmlspecialchars($category9222); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9222); ?>" data-status="<?php echo htmlspecialchars($status9222); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9222); ?>; 
position:absolute; top:405px; left:105px;'>
                        </div>

                        <!-- ASSET 9223 -->
                        <img src='../image.php?id=9223' style='width:17px; cursor:pointer; position:absolute; top:425px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9223' class="asset-image" onclick='fetchAssetData(9223);' data-id="<?php echo $assetId9223; ?>" data-room="<?php echo htmlspecialchars($room9223); ?>" data-floor="<?php echo htmlspecialchars($floor9223); ?>" data-image="<?php echo base64_encode($upload_img9223); ?>" data-category="<?php echo htmlspecialchars($category9223); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9223); ?>" data-status="<?php echo htmlspecialchars($status9223); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9223); ?>; 
position:absolute; top:425px; left:105px;'>
                        </div>

                        <!-- ASSET 9224 -->
                        <img src='../image.php?id=9224' style='width:17px; cursor:pointer; position:absolute; top:445px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9224' class="asset-image" onclick='fetchAssetData(9224);' data-id="<?php echo $assetId9224; ?>" data-room="<?php echo htmlspecialchars($room9224); ?>" data-floor="<?php echo htmlspecialchars($floor9224); ?>" data-image="<?php echo base64_encode($upload_img9224); ?>" data-category="<?php echo htmlspecialchars($category9224); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9224); ?>" data-status="<?php echo htmlspecialchars($status9224); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9224); ?>; 
position:absolute; top:445px; left:105px;'>
                        </div>

                        <!-- ASSET 9225 -->
                        <img src='../image.php?id=9225' style='width:17px; cursor:pointer; position:absolute; top:465px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9225' class="asset-image" onclick='fetchAssetData(9225);' data-id="<?php echo $assetId9225; ?>" data-room="<?php echo htmlspecialchars($room9225); ?>" data-floor="<?php echo htmlspecialchars($floor9225); ?>" data-image="<?php echo base64_encode($upload_img9225); ?>" data-category="<?php echo htmlspecialchars($category9225); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9225); ?>" data-status="<?php echo htmlspecialchars($status9225); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9225); ?>; 
position:absolute; top:465px; left:105px;'>
                        </div>

                        <!-- ASSET 9226 -->
                        <img src='../image.php?id=9226' style='width:17px; cursor:pointer; position:absolute; top:485px; left:105px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9226' class="asset-image" onclick='fetchAssetData(9226);' data-id="<?php echo $assetId9226; ?>" data-room="<?php echo htmlspecialchars($room9226); ?>" data-floor="<?php echo htmlspecialchars($floor9226); ?>" data-image="<?php echo base64_encode($upload_img9226); ?>" data-category="<?php echo htmlspecialchars($category9226); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9226); ?>" data-status="<?php echo htmlspecialchars($status9226); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9226); ?>; 
position:absolute; top:485px; left:105px;'>
                        </div>


                        <!-- END OF ROW 3 -->

                        <!-- ASSET 9227 -->
                        <img src='../image.php?id=9227' style='width:17px; cursor:pointer; position:absolute; top:405px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9227' class="asset-image" onclick='fetchAssetData(9227);' data-id="<?php echo $assetId9227; ?>" data-room="<?php echo htmlspecialchars($room9227); ?>" data-floor="<?php echo htmlspecialchars($floor9227); ?>" data-image="<?php echo base64_encode($upload_img9227); ?>" data-category="<?php echo htmlspecialchars($category9227); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9227); ?>" data-status="<?php echo htmlspecialchars($status9227); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9227); ?>; 
position:absolute; top:405px; left:125px;'>
                        </div>

                        <!-- ASSET 9228 -->
                        <img src='../image.php?id=9228' style='width:17px; cursor:pointer; position:absolute; top:425px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9228' class="asset-image" onclick='fetchAssetData(9228);' data-id="<?php echo $assetId9228; ?>" data-room="<?php echo htmlspecialchars($room9228); ?>" data-floor="<?php echo htmlspecialchars($floor9228); ?>" data-image="<?php echo base64_encode($upload_img9228); ?>" data-category="<?php echo htmlspecialchars($category9228); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9228); ?>" data-status="<?php echo htmlspecialchars($status9228); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9228); ?>; 
position:absolute; top:425px; left:125px;'>
                        </div>

                        <!-- ASSET 9229 -->
                        <img src='../image.php?id=9229' style='width:17px; cursor:pointer; position:absolute; top:445px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9229' class="asset-image" onclick='fetchAssetData(9229);' data-id="<?php echo $assetId9229; ?>" data-room="<?php echo htmlspecialchars($room9229); ?>" data-floor="<?php echo htmlspecialchars($floor9229); ?>" data-image="<?php echo base64_encode($upload_img9229); ?>" data-category="<?php echo htmlspecialchars($category9229); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9229); ?>" data-status="<?php echo htmlspecialchars($status9229); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9229); ?>; 
position:absolute; top:445px; left:125px;'>
                        </div>

                        <!-- ASSET 9230 -->
                        <img src='../image.php?id=9230' style='width:17px; cursor:pointer; position:absolute; top:465px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9230' class="asset-image" onclick='fetchAssetData(9230);' data-id="<?php echo $assetId9230; ?>" data-room="<?php echo htmlspecialchars($room9230); ?>" data-floor="<?php echo htmlspecialchars($floor9230); ?>" data-image="<?php echo base64_encode($upload_img9230); ?>" data-category="<?php echo htmlspecialchars($category9230); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9230); ?>" data-status="<?php echo htmlspecialchars($status9230); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9230); ?>; 
position:absolute; top:465px; left:125px;'>
                        </div>

                        <!-- ASSET 9231 -->
                        <img src='../image.php?id=9231' style='width:17px; cursor:pointer; position:absolute; top:485px; left:125px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9231' class="asset-image" onclick='fetchAssetData(9231);' data-id="<?php echo $assetId9231; ?>" data-room="<?php echo htmlspecialchars($room9231); ?>" data-floor="<?php echo htmlspecialchars($floor9231); ?>" data-image="<?php echo base64_encode($upload_img9231); ?>" data-category="<?php echo htmlspecialchars($category9231); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9231); ?>" data-status="<?php echo htmlspecialchars($status9231); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9231); ?>; 
position:absolute; top:485px; left:125px;'>
                        </div>


                        <!-- END OF ROW 4 -->

                        <!-- ASSET 9232 -->
                        <img src='../image.php?id=9232' style='width:17px; cursor:pointer; position:absolute; top:405px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9232' class="asset-image" onclick='fetchAssetData(9232);' data-id="<?php echo $assetId9232; ?>" data-room="<?php echo htmlspecialchars($room9232); ?>" data-floor="<?php echo htmlspecialchars($floor9232); ?>" data-image="<?php echo base64_encode($upload_img9232); ?>" data-category="<?php echo htmlspecialchars($category9232); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9232); ?>" data-status="<?php echo htmlspecialchars($status9232); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9232); ?>; 
position:absolute; top:405px; left:145px;'>
                        </div>

                        <!-- ASSET 9233 -->
                        <img src='../image.php?id=9233' style='width:17px; cursor:pointer; position:absolute; top:425px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9233' class="asset-image" onclick='fetchAssetData(9233);' data-id="<?php echo $assetId9233; ?>" data-room="<?php echo htmlspecialchars($room9233); ?>" data-floor="<?php echo htmlspecialchars($floor9233); ?>" data-image="<?php echo base64_encode($upload_img9233); ?>" data-category="<?php echo htmlspecialchars($category9233); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9233); ?>" data-status="<?php echo htmlspecialchars($status9233); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9233); ?>; 
position:absolute; top:425px; left:145px;'>
                        </div>

                        <!-- ASSET 9234 -->
                        <img src='../image.php?id=9234' style='width:17px; cursor:pointer; position:absolute; top:445px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9234' class="asset-image" onclick='fetchAssetData(9234);' data-id="<?php echo $assetId9234; ?>" data-room="<?php echo htmlspecialchars($room9234); ?>" data-floor="<?php echo htmlspecialchars($floor9234); ?>" data-image="<?php echo base64_encode($upload_img9234); ?>" data-category="<?php echo htmlspecialchars($category9234); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9234); ?>" data-status="<?php echo htmlspecialchars($status9234); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9234); ?>; 
position:absolute; top:445px; left:145px;'>
                        </div>

                        <!-- ASSET 9235 -->
                        <img src='../image.php?id=9235' style='width:17px; cursor:pointer; position:absolute; top:465px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9235' class="asset-image" onclick='fetchAssetData(9235);' data-id="<?php echo $assetId9235; ?>" data-room="<?php echo htmlspecialchars($room9235); ?>" data-floor="<?php echo htmlspecialchars($floor9235); ?>" data-image="<?php echo base64_encode($upload_img9235); ?>" data-category="<?php echo htmlspecialchars($category9235); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9235); ?>" data-status="<?php echo htmlspecialchars($status9235); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9235); ?>; 
position:absolute; top:465px; left:145px;'>
                        </div>


                        <!-- ASSET 9236 -->
                        <img src='../image.php?id=9236' style='width:17px; cursor:pointer; position:absolute; top:485px; left:145px; transform: rotate(-90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9236' class="asset-image" onclick='fetchAssetData(9236);' data-id="<?php echo $assetId9236; ?>" data-room="<?php echo htmlspecialchars($room9236); ?>" data-floor="<?php echo htmlspecialchars($floor9236); ?>" data-image="<?php echo base64_encode($upload_img9236); ?>" data-category="<?php echo htmlspecialchars($category9236); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9236); ?>" data-status="<?php echo htmlspecialchars($status9236); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9236); ?>; 
position:absolute; top:485px; left:145px;'>
                        </div>

                        <!-- ASSET 9237 -->
                        <img src='../image.php?id=9237' style='width:17px; cursor:pointer; position:absolute; top:445px; left:198px; transform: rotate(90deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9237' class="asset-image" onclick='fetchAssetData(9237);' data-id="<?php echo $assetId9237; ?>" data-room="<?php echo htmlspecialchars($room9237); ?>" data-floor="<?php echo htmlspecialchars($floor9237); ?>" data-image="<?php echo base64_encode($upload_img9237); ?>" data-category="<?php echo htmlspecialchars($category9237); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9237); ?>" data-status="<?php echo htmlspecialchars($status9237); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9237); ?>; 
position:absolute; top:455px; left:208px;'>
                        </div>

                        <!-- ASSET 9244 -->
                        <img src='../image.php?id=9244' style='width:15px; cursor:pointer; position:absolute; top:314px; left:58px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9244' class="asset-image" onclick='fetchAssetData(9244);' data-id="<?php echo $assetId9244; ?>" data-room="<?php echo htmlspecialchars($room9244); ?>" data-floor="<?php echo htmlspecialchars($floor9244); ?>" data-image="<?php echo base64_encode($upload_img9244); ?>" data-category="<?php echo htmlspecialchars($category9244); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9244); ?>" data-status="<?php echo htmlspecialchars($status9244); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9244); ?>;
position:absolute; top:330px; left:66px;'>
                        </div>

                        <!-- ASSET 9245 -->
                        <img src='../image.php?id=9245' style='width:15px; cursor:pointer; position:absolute; top:314px; left:87px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9245' class="asset-image" onclick='fetchAssetData(9245);' data-id="<?php echo $assetId9245; ?>" data-room="<?php echo htmlspecialchars($room9245); ?>" data-floor="<?php echo htmlspecialchars($floor9245); ?>" data-image="<?php echo base64_encode($upload_img9245); ?>" data-category="<?php echo htmlspecialchars($category9245); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9245); ?>" data-status="<?php echo htmlspecialchars($status9245); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9245); ?>;
position:absolute; top:330px; left:94px;'>
                        </div>

                        <!-- ASSET 9246 -->
                        <img src='../image.php?id=9246' style='width:15px; cursor:pointer; position:absolute; top:314px; left:115px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9246' class="asset-image" onclick='fetchAssetData(9246);' data-id="<?php echo $assetId9246; ?>" data-room="<?php echo htmlspecialchars($room9246); ?>" data-floor="<?php echo htmlspecialchars($floor9246); ?>" data-image="<?php echo base64_encode($upload_img9246); ?>" data-category="<?php echo htmlspecialchars($category9246); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9246); ?>" data-status="<?php echo htmlspecialchars($status9246); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9246); ?>;
position:absolute; top:330px; left:123px;'>
                        </div>

                        <!-- ASSET 9247 -->
                        <img src='../image.php?id=9246' style='width:15px; cursor:pointer; position:absolute; top:314px; left:148px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9247' class="asset-image" onclick='fetchAssetData(9247);' data-id="<?php echo $assetId9247; ?>" data-room="<?php echo htmlspecialchars($room9247); ?>" data-floor="<?php echo htmlspecialchars($floor9247); ?>" data-image="<?php echo base64_encode($upload_img9247); ?>" data-category="<?php echo htmlspecialchars($category9247); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9247); ?>" data-status="<?php echo htmlspecialchars($status9247); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9247); ?>;
position:absolute; top:330px; left:156px;'>
                        </div>

                        <!-- ASSET 9248 -->
                        <img src='../image.php?id=9248' style='width:15px; cursor:pointer; position:absolute; top:314px; left:179px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9248' class="asset-image" onclick='fetchAssetData(9248);' data-id="<?php echo $assetId9248; ?>" data-room="<?php echo htmlspecialchars($room9248); ?>" data-floor="<?php echo htmlspecialchars($floor9248); ?>" data-image="<?php echo base64_encode($upload_img9248); ?>" data-category="<?php echo htmlspecialchars($category9248); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9248); ?>" data-status="<?php echo htmlspecialchars($status9248); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9248); ?>;
position:absolute; top:330px; left:186px;'>
                        </div>

                        <!-- ASSET 9249 -->
                        <img src='../image.php?id=9249' style='width:15px; cursor:pointer; position:absolute; top:314px; left:207px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9249' class="asset-image" onclick='fetchAssetData(9249);' data-id="<?php echo $assetId9249; ?>" data-room="<?php echo htmlspecialchars($room9249); ?>" data-floor="<?php echo htmlspecialchars($floor9249); ?>" data-image="<?php echo base64_encode($upload_img9249); ?>" data-category="<?php echo htmlspecialchars($category9249); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9249); ?>" data-status="<?php echo htmlspecialchars($status9249); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9249); ?>;
position:absolute; top:330px; left:215px;'>
                        </div>


                        <!-- END OF CR RIGHT SIDE WOMENS -->

                        <!-- START OF CR LEFT SIDE WOMENS -->

                        <!-- ASSET 9250 -->
                        <img src='../image.php?id=9250' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1020px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9250' class="asset-image" onclick='fetchAssetData(9250);' data-id="<?php echo $assetId9250; ?>" data-room="<?php echo htmlspecialchars($room9250); ?>" data-floor="<?php echo htmlspecialchars($floor9250); ?>" data-image="<?php echo base64_encode($upload_img9250); ?>" data-category="<?php echo htmlspecialchars($category9250); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9250); ?>" data-status="<?php echo htmlspecialchars($status9250); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9250); ?>;
position:absolute; top:330px; left:1030px;'>
                        </div>

                        <!-- ASSET 9251 -->
                        <img src='../image.php?id=9251' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1050px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9251' class="asset-image" onclick='fetchAssetData(9251);' data-id="<?php echo $assetId9251; ?>" data-room="<?php echo htmlspecialchars($room9251); ?>" data-floor="<?php echo htmlspecialchars($floor9251); ?>" data-image="<?php echo base64_encode($upload_img9251); ?>" data-category="<?php echo htmlspecialchars($category9251); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9251); ?>" data-status="<?php echo htmlspecialchars($status9251); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9251); ?>;
position:absolute; top:330px; left:1060px;'>
                        </div>

                        <!-- ASSET 9252 -->
                        <img src='../image.php?id=9252' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1077px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9252' class="asset-image" onclick='fetchAssetData(9252);' data-id="<?php echo $assetId9252; ?>" data-room="<?php echo htmlspecialchars($room9252); ?>" data-floor="<?php echo htmlspecialchars($floor9252); ?>" data-image="<?php echo base64_encode($upload_img9252); ?>" data-category="<?php echo htmlspecialchars($category9252); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9252); ?>" data-status="<?php echo htmlspecialchars($status9252); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9252); ?>;
position:absolute; top:330px; left:1087px;'>
                        </div>

                        <!-- ASSET 9253 -->
                        <img src='../image.php?id=9253' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9253' class="asset-image" onclick='fetchAssetData(9253);' data-id="<?php echo $assetId9253; ?>" data-room="<?php echo htmlspecialchars($room9253); ?>" data-floor="<?php echo htmlspecialchars($floor9253); ?>" data-image="<?php echo base64_encode($upload_img9253); ?>" data-category="<?php echo htmlspecialchars($category9253); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9253); ?>" data-status="<?php echo htmlspecialchars($status9253); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9253); ?>;
position:absolute; top:330px; left:1118px;'>
                        </div>


                        <!-- ASSET 9254 -->
                        <img src='../image.php?id=9254' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1137px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9254' class="asset-image" onclick='fetchAssetData(9254);' data-id="<?php echo $assetId9254; ?>" data-room="<?php echo htmlspecialchars($room9254); ?>" data-floor="<?php echo htmlspecialchars($floor9254); ?>" data-image="<?php echo base64_encode($upload_img9254); ?>" data-category="<?php echo htmlspecialchars($category9254); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9254); ?>" data-status="<?php echo htmlspecialchars($status9254); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9254); ?>;
position:absolute; top:330px; left:1145px;'>
                        </div>

                        <!-- ASSET 9255 -->
                        <img src='../image.php?id=9255' style='width:15px; cursor:pointer; position:absolute; top:314px; left:1167px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal9255' class="asset-image" onclick='fetchAssetData(9255);' data-id="<?php echo $assetId9255; ?>" data-room="<?php echo htmlspecialchars($room9255); ?>" data-floor="<?php echo htmlspecialchars($floor9255); ?>" data-image="<?php echo base64_encode($upload_img9255); ?>" data-category="<?php echo htmlspecialchars($category9255); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9255); ?>" data-status="<?php echo htmlspecialchars($status9255); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9255); ?>;
position:absolute; top:330px; left:1175px;'>
                        </div>

                        <!-- LIGHTS HALLWAY -->

                        <!-- ASSET 16789 -->
                        <img src='../image.php?id=16789' style='width:20px; cursor:pointer; position:absolute; top:250px; left:110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16789' class="asset-image" onclick='fetchAssetData(16789);' data-id="<?php echo $assetId16789; ?>" data-room="<?php echo htmlspecialchars($room16789); ?>" data-floor="<?php echo htmlspecialchars($floor16789); ?>" data-image="<?php echo base64_encode($upload_img16789); ?>" data-category="<?php echo htmlspecialchars($category16789); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16789); ?>" data-status="<?php echo htmlspecialchars($status16789); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16789); ?>;
position:absolute; top:250px; left:120px;'>
                        </div>

                        <!-- ASSET 16753 -->
                        <img src='../image.php?id=16753' style='width:20px; cursor:pointer; position:absolute; top:250px; left:170px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16753' class="asset-image" onclick='fetchAssetData(16753);' data-id="<?php echo $assetId16753; ?>" data-room="<?php echo htmlspecialchars($room16753); ?>" data-floor="<?php echo htmlspecialchars($floor16753); ?>" data-image="<?php echo base64_encode($upload_img16753); ?>" data-category="<?php echo htmlspecialchars($category16753); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16753); ?>" data-status="<?php echo htmlspecialchars($status16753); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16753); ?>;
position:absolute; top:250px; left:180px;'>
                        </div>


                        <!-- ASSET 16754 -->
                        <img src='../image.php?id=16754' style='width:20px; cursor:pointer; position:absolute; top:250px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16754' class="asset-image" onclick='fetchAssetData(16754);' data-id="<?php echo $assetId16754; ?>" data-room="<?php echo htmlspecialchars($room16754); ?>" data-floor="<?php echo htmlspecialchars($floor16754); ?>" data-image="<?php echo base64_encode($upload_img16754); ?>" data-category="<?php echo htmlspecialchars($category16754); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16754); ?>" data-status="<?php echo htmlspecialchars($status16754); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16754); ?>;
position:absolute; top:250px; left:250px;'>
                        </div>

                        <!-- ASSET 16755 -->
                        <img src='../image.php?id=16755' style='width:20px; cursor:pointer; position:absolute; top:250px; left:300px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16755' class="asset-image" onclick='fetchAssetData(16755);' data-id="<?php echo $assetId16755; ?>" data-room="<?php echo htmlspecialchars($room16755); ?>" data-floor="<?php echo htmlspecialchars($floor16755); ?>" data-image="<?php echo base64_encode($upload_img16755); ?>" data-category="<?php echo htmlspecialchars($category16755); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16755); ?>" data-status="<?php echo htmlspecialchars($status16755); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16755); ?>;
position:absolute; top:250px; left:310px;'>
                        </div>

                        <!-- ASSET 16756 -->
                        <img src='../image.php?id=16756' style='width:20px; cursor:pointer; position:absolute; top:250px; left:380px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16756' class="asset-image" onclick='fetchAssetData(16756);' data-id="<?php echo $assetId16756; ?>" data-room="<?php echo htmlspecialchars($room16756); ?>" data-floor="<?php echo htmlspecialchars($floor16756); ?>" data-image="<?php echo base64_encode($upload_img16756); ?>" data-category="<?php echo htmlspecialchars($category16756); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16756); ?>" data-status="<?php echo htmlspecialchars($status16756); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16756); ?>;
position:absolute; top:250px; left:390px;'>
                        </div>

                        <!-- ASSET 16757 -->
                        <img src='../image.php?id=16757' style='width:20px; cursor:pointer; position:absolute; top:250px; left:480px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16757' class="asset-image" onclick='fetchAssetData(16757);' data-id="<?php echo $assetId16757; ?>" data-room="<?php echo htmlspecialchars($room16757); ?>" data-floor="<?php echo htmlspecialchars($floor16757); ?>" data-image="<?php echo base64_encode($upload_img16757); ?>" data-category="<?php echo htmlspecialchars($category16757); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16757); ?>" data-status="<?php echo htmlspecialchars($status16757); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16757); ?>;
position:absolute; top:250px; left:490px;'>
                        </div>


                        <!-- ASSET 16758 -->
                        <img src='../image.php?id=16758' style='width:20px; cursor:pointer; position:absolute; top:250px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16758' class="asset-image" onclick='fetchAssetData(16758);' data-id="<?php echo $assetId16758; ?>" data-room="<?php echo htmlspecialchars($room16758); ?>" data-floor="<?php echo htmlspecialchars($floor16758); ?>" data-image="<?php echo base64_encode($upload_img16758); ?>" data-category="<?php echo htmlspecialchars($category16758); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16758); ?>" data-status="<?php echo htmlspecialchars($status16758); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16758); ?>;
position:absolute; top:250px; left:570px;'>
                        </div>

                        <!-- ASSET 16759 -->
                        <img src='../image.php?id=16759' style='width:20px; cursor:pointer; position:absolute; top:250px; left:650px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16759' class="asset-image" onclick='fetchAssetData(16759);' data-id="<?php echo $assetId16759; ?>" data-room="<?php echo htmlspecialchars($room16759); ?>" data-floor="<?php echo htmlspecialchars($floor16759); ?>" data-image="<?php echo base64_encode($upload_img16759); ?>" data-category="<?php echo htmlspecialchars($category16759); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16759); ?>" data-status="<?php echo htmlspecialchars($status16759); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16759); ?>;
position:absolute; top:250px; left:660px;'>
                        </div>

                        <!-- ASSET 16760 -->
                        <img src='../image.php?id=16760' style='width:20px; cursor:pointer; position:absolute; top:250px; left:730px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16760' class="asset-image" onclick='fetchAssetData(16760);' data-id="<?php echo $assetId16760; ?>" data-room="<?php echo htmlspecialchars($room16760); ?>" data-floor="<?php echo htmlspecialchars($floor16760); ?>" data-image="<?php echo base64_encode($upload_img16760); ?>" data-category="<?php echo htmlspecialchars($category16760); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16760); ?>" data-status="<?php echo htmlspecialchars($status16760); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16760); ?>;
position:absolute; top:250px; left:740px;'>
                        </div>

                        <!-- ASSET 16761 -->
                        <img src='../image.php?id=16761' style='width:20px; cursor:pointer; position:absolute; top:250px; left:830px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16761' class="asset-image" onclick='fetchAssetData(16761);' data-id="<?php echo $assetId16761; ?>" data-room="<?php echo htmlspecialchars($room16761); ?>" data-floor="<?php echo htmlspecialchars($floor16761); ?>" data-image="<?php echo base64_encode($upload_img16761); ?>" data-category="<?php echo htmlspecialchars($category16761); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16761); ?>" data-status="<?php echo htmlspecialchars($status16761); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16761); ?>;
position:absolute; top:250px; left:840px;'>
                        </div>


                        <!-- ASSET 16762 -->
                        <img src='../image.php?id=16762' style='width:20px; cursor:pointer; position:absolute; top:250px; left:915px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16762' class="asset-image" onclick='fetchAssetData(16762);' data-id="<?php echo $assetId16762; ?>" data-room="<?php echo htmlspecialchars($room16762); ?>" data-floor="<?php echo htmlspecialchars($floor16762); ?>" data-image="<?php echo base64_encode($upload_img16762); ?>" data-category="<?php echo htmlspecialchars($category16762); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16762); ?>" data-status="<?php echo htmlspecialchars($status16762); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16762); ?>;
position:absolute; top:250px; left:925px;'>
                        </div>

                        <!-- ASSET 16763 -->
                        <img src='../image.php?id=16763' style='width:20px; cursor:pointer; position:absolute; top:250px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16763' class="asset-image" onclick='fetchAssetData(16763);' data-id="<?php echo $assetId16763; ?>" data-room="<?php echo htmlspecialchars($room16763); ?>" data-floor="<?php echo htmlspecialchars($floor16763); ?>" data-image="<?php echo base64_encode($upload_img16763); ?>" data-category="<?php echo htmlspecialchars($category16763); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16763); ?>" data-status="<?php echo htmlspecialchars($status16763); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16763); ?>;
position:absolute; top:250px; left:980px;'>
                        </div>

                        <!-- ASSET 16764 -->
                        <img src='../image.php?id=16764' style='width:20px; cursor:pointer; position:absolute; top:250px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16764' class="asset-image" onclick='fetchAssetData(16764);' data-id="<?php echo $assetId16764; ?>" data-room="<?php echo htmlspecialchars($room16764); ?>" data-floor="<?php echo htmlspecialchars($floor16764); ?>" data-image="<?php echo base64_encode($upload_img16764); ?>" data-category="<?php echo htmlspecialchars($category16764); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16764); ?>" data-status="<?php echo htmlspecialchars($status16764); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16764); ?>;
position:absolute; top:250px; left:1050px;'>
                        </div>

                        <!-- ASSET 16765 -->
                        <img src='../image.php?id=16765' style='width:20px; cursor:pointer; position:absolute; top:250px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16765' class="asset-image" onclick='fetchAssetData(16765);' data-id="<?php echo $assetId16765; ?>" data-room="<?php echo htmlspecialchars($room16765); ?>" data-floor="<?php echo htmlspecialchars($floor16765); ?>" data-image="<?php echo base64_encode($upload_img16765); ?>" data-category="<?php echo htmlspecialchars($category16765); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16765); ?>" data-status="<?php echo htmlspecialchars($status16765); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16765); ?>;
position:absolute; top:250px; left:1130px;'>
                        </div>


                        <!-- VERTICAL -->

                        <!-- ASSET 16766 -->
                        <img src='../image.php?id=16766' style='width:20px; cursor:pointer; position:absolute; top:290px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16766' class="asset-image" onclick='fetchAssetData(16766);' data-id="<?php echo $assetId16766; ?>" data-room="<?php echo htmlspecialchars($room16766); ?>" data-floor="<?php echo htmlspecialchars($floor16766); ?>" data-image="<?php echo base64_encode($upload_img16766); ?>" data-category="<?php echo htmlspecialchars($category16766); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16766); ?>" data-status="<?php echo htmlspecialchars($status16766); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16766); ?>;
position:absolute; top:290px; left:250px;'>
                        </div>

                        <!-- ASSET 16767 -->
                        <img src='../image.php?id=16767' style='width:20px; cursor:pointer; position:absolute; top:330px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16767' class="asset-image" onclick='fetchAssetData(16767);' data-id="<?php echo $assetId16767; ?>" data-room="<?php echo htmlspecialchars($room16767); ?>" data-floor="<?php echo htmlspecialchars($floor16767); ?>" data-image="<?php echo base64_encode($upload_img16767); ?>" data-category="<?php echo htmlspecialchars($category16767); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16767); ?>" data-status="<?php echo htmlspecialchars($status16767); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16767); ?>;
position:absolute; top:330px; left:250px;'>
                        </div>

                        <!-- ASSET 16768 -->
                        <img src='../image.php?id=16768' style='width:20px; cursor:pointer; position:absolute; top:370px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16768' class="asset-image" onclick='fetchAssetData(16768);' data-id="<?php echo $assetId16768; ?>" data-room="<?php echo htmlspecialchars($room16768); ?>" data-floor="<?php echo htmlspecialchars($floor16768); ?>" data-image="<?php echo base64_encode($upload_img16768); ?>" data-category="<?php echo htmlspecialchars($category16768); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16768); ?>" data-status="<?php echo htmlspecialchars($status16768); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16768); ?>;
position:absolute; top:370px; left:250px;'>
                        </div>

                        <!-- ASSET 16769 -->
                        <img src='../image.php?id=16769' style='width:20px; cursor:pointer; position:absolute; top:420px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16769' class="asset-image" onclick='fetchAssetData(16769);' data-id="<?php echo $assetId16769; ?>" data-room="<?php echo htmlspecialchars($room16769); ?>" data-floor="<?php echo htmlspecialchars($floor16769); ?>" data-image="<?php echo base64_encode($upload_img16769); ?>" data-category="<?php echo htmlspecialchars($category16769); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16769); ?>" data-status="<?php echo htmlspecialchars($status16769); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16769); ?>;
position:absolute; top:420px; left:250px;'>
                        </div>

                        <!-- ASSET 16770 -->
                        <img src='../image.php?id=16770' style='width:20px; cursor:pointer; position:absolute; top:460px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16770' class="asset-image" onclick='fetchAssetData(16770);' data-id="<?php echo $assetId16770; ?>" data-room="<?php echo htmlspecialchars($room16770); ?>" data-floor="<?php echo htmlspecialchars($floor16770); ?>" data-image="<?php echo base64_encode($upload_img16770); ?>" data-category="<?php echo htmlspecialchars($category16770); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16770); ?>" data-status="<?php echo htmlspecialchars($status16770); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16770); ?>;
position:absolute; top:460px; left:250px;'>
                        </div>

                        <!-- ASSET 16771 -->
                        <img src='../image.php?id=16771' style='width:20px; cursor:pointer; position:absolute; top:500px; left:240px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16771' class="asset-image" onclick='fetchAssetData(16771);' data-id="<?php echo $assetId16771; ?>" data-room="<?php echo htmlspecialchars($room16771); ?>" data-floor="<?php echo htmlspecialchars($floor16771); ?>" data-image="<?php echo base64_encode($upload_img16771); ?>" data-category="<?php echo htmlspecialchars($category16771); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16771); ?>" data-status="<?php echo htmlspecialchars($status16771); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16771); ?>;
position:absolute; top:500px; left:250px;'>
                        </div>

                        <!-- ASSET 16772 -->
                        <img src='../image.php?id=16772' style='width:20px; cursor:pointer; position:absolute; top:360px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16772' class="asset-image" onclick='fetchAssetData(16772);' data-id="<?php echo $assetId16772; ?>" data-room="<?php echo htmlspecialchars($room16772); ?>" data-floor="<?php echo htmlspecialchars($floor16772); ?>" data-image="<?php echo base64_encode($upload_img16772); ?>" data-category="<?php echo htmlspecialchars($category16772); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16772); ?>" data-status="<?php echo htmlspecialchars($status16772); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16772); ?>;
position:absolute; top:360px; left:210px;'>
                        </div>

                        <!-- ASSET 16773 -->
                        <img src='../image.php?id=16773' style='width:20px; cursor:pointer; position:absolute; top:360px; left:150px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16773' class="asset-image" onclick='fetchAssetData(16773);' data-id="<?php echo $assetId16773; ?>" data-room="<?php echo htmlspecialchars($room16773); ?>" data-floor="<?php echo htmlspecialchars($floor16773); ?>" data-image="<?php echo base64_encode($upload_img16773); ?>" data-category="<?php echo htmlspecialchars($category16773); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16773); ?>" data-status="<?php echo htmlspecialchars($status16773); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16773); ?>;
position:absolute; top:360px; left:160px;'>
                        </div>

                        <!-- VERTICAL RIGHT SIDE -->

                        <!-- ASSET 16774 -->
                        <img src='../image.php?id=16774' style='width:20px; cursor:pointer; position:absolute; top:290px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16774' class="asset-image" onclick='fetchAssetData(16774);' data-id="<?php echo $assetId16774; ?>" data-room="<?php echo htmlspecialchars($room16774); ?>" data-floor="<?php echo htmlspecialchars($floor16774); ?>" data-image="<?php echo base64_encode($upload_img16774); ?>" data-category="<?php echo htmlspecialchars($category16774); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16774); ?>" data-status="<?php echo htmlspecialchars($status16774); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16774); ?>;
position:absolute; top:290px; left:980px;'>
                        </div>

                        <!-- ASSET 16775 -->
                        <img src='../image.php?id=16775' style='width:20px; cursor:pointer; position:absolute; top:330px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16775' class="asset-image" onclick='fetchAssetData(16775);' data-id="<?php echo $assetId16775; ?>" data-room="<?php echo htmlspecialchars($room16775); ?>" data-floor="<?php echo htmlspecialchars($floor16775); ?>" data-image="<?php echo base64_encode($upload_img16775); ?>" data-category="<?php echo htmlspecialchars($category16775); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16775); ?>" data-status="<?php echo htmlspecialchars($status16775); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16775); ?>;
position:absolute; top:330px; left:980px;'>
                        </div>

                        <!-- ASSET 16776 -->
                        <img src='../image.php?id=16776' style='width:20px; cursor:pointer; position:absolute; top:370px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16776' class="asset-image" onclick='fetchAssetData(16776);' data-id="<?php echo $assetId16776; ?>" data-room="<?php echo htmlspecialchars($room16776); ?>" data-floor="<?php echo htmlspecialchars($floor16776); ?>" data-image="<?php echo base64_encode($upload_img16776); ?>" data-category="<?php echo htmlspecialchars($category16776); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16776); ?>" data-status="<?php echo htmlspecialchars($status16776); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16776); ?>;
position:absolute; top:370px; left:980px;'>
                        </div>

                        <!-- ASSET 16777 -->
                        <img src='../image.php?id=16777' style='width:20px; cursor:pointer; position:absolute; top:420px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16777' class="asset-image" onclick='fetchAssetData(16777);' data-id="<?php echo $assetId16777; ?>" data-room="<?php echo htmlspecialchars($room16777); ?>" data-floor="<?php echo htmlspecialchars($floor16777); ?>" data-image="<?php echo base64_encode($upload_img16777); ?>" data-category="<?php echo htmlspecialchars($category16777); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16777); ?>" data-status="<?php echo htmlspecialchars($status16777); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16777); ?>;
position:absolute; top:420px; left:980px;'>
                        </div>


                        <!-- ASSET 16778 -->
                        <img src='../image.php?id=16778' style='width:20px; cursor:pointer; position:absolute; top:460px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16778' class="asset-image" onclick='fetchAssetData(16778);' data-id="<?php echo $assetId16778; ?>" data-room="<?php echo htmlspecialchars($room16778); ?>" data-floor="<?php echo htmlspecialchars($floor16778); ?>" data-image="<?php echo base64_encode($upload_img16778); ?>" data-category="<?php echo htmlspecialchars($category16778); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16778); ?>" data-status="<?php echo htmlspecialchars($status16778); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16778); ?>;
position:absolute; top:460px; left:980px;'>
                        </div>

                        <!-- ASSET 16779 -->
                        <img src='../image.php?id=16779' style='width:20px; cursor:pointer; position:absolute; top:500px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16779' class="asset-image" onclick='fetchAssetData(16779);' data-id="<?php echo $assetId16779; ?>" data-room="<?php echo htmlspecialchars($room16779); ?>" data-floor="<?php echo htmlspecialchars($floor16779); ?>" data-image="<?php echo base64_encode($upload_img16779); ?>" data-category="<?php echo htmlspecialchars($category16779); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16779); ?>" data-status="<?php echo htmlspecialchars($status16779); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16779); ?>;
position:absolute; top:500px; left:980px;'>
                        </div>

                        <!-- ASSET 16780 -->
                        <img src='../image.php?id=16780' style='width:20px; cursor:pointer; position:absolute; top:355px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16780' class="asset-image" onclick='fetchAssetData(16780);' data-id="<?php echo $assetId16780; ?>" data-room="<?php echo htmlspecialchars($room16780); ?>" data-floor="<?php echo htmlspecialchars($floor16780); ?>" data-image="<?php echo base64_encode($upload_img16780); ?>" data-category="<?php echo htmlspecialchars($category16780); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16780); ?>" data-status="<?php echo htmlspecialchars($status16780); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16780); ?>;
position:absolute; top:355px; left:1020px;'>
                        </div>

                        <!-- ASSET 16781 -->
                        <img src='../image.php?id=16781' style='width:20px; cursor:pointer; position:absolute; top:355px; left:1070px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16781' class="asset-image" onclick='fetchAssetData(16781);' data-id="<?php echo $assetId16781; ?>" data-room="<?php echo htmlspecialchars($room16781); ?>" data-floor="<?php echo htmlspecialchars($floor16781); ?>" data-image="<?php echo base64_encode($upload_img16781); ?>" data-category="<?php echo htmlspecialchars($category16781); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16781); ?>" data-status="<?php echo htmlspecialchars($status16781); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16781); ?>;
position:absolute; top:355px; left:1080px;'>
                        </div>


                        <!-- END OF LIGHTS HALLWAY -->

                        <!-- START OF LIGHTS IB101A -->

                        <!-- ROW 1 LIGHTS -->

                        <!-- ASSET 16682 -->
                        <img src='../image.php?id=16682' style='width:20px; cursor:pointer; position:absolute; top:390px; left:1165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16682' class="asset-image" onclick='fetchAssetData(16682);' data-id="<?php echo $assetId16682; ?>" data-room="<?php echo htmlspecialchars($room16682); ?>" data-floor="<?php echo htmlspecialchars($floor16682); ?>" data-image="<?php echo base64_encode($upload_img16682); ?>" data-category="<?php echo htmlspecialchars($category16682); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16682); ?>" data-status="<?php echo htmlspecialchars($status16682); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16682); ?>;
position:absolute; top:390px; left:1175px;'>
                        </div>

                        <!-- ASSET 16683 -->
                        <img src='../image.php?id=16683' style='width:20px; cursor:pointer; position:absolute; top:500px; left:1165px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16683' class="asset-image" onclick='fetchAssetData(16683);' data-id="<?php echo $assetId16683; ?>" data-room="<?php echo htmlspecialchars($room16683); ?>" data-floor="<?php echo htmlspecialchars($floor16683); ?>" data-image="<?php echo base64_encode($upload_img16683); ?>" data-category="<?php echo htmlspecialchars($category16683); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16683); ?>" data-status="<?php echo htmlspecialchars($status16683); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16683); ?>;
position:absolute; top:510px; left:1175px;'>
                        </div>

                        <!-- ASSET 16684 -->
                        <img src='../image.php?id=16684' style='width:20px; cursor:pointer; position:absolute; top:448px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16684' class="asset-image" onclick='fetchAssetData(16684);' data-id="<?php echo $assetId16684; ?>" data-room="<?php echo htmlspecialchars($room16684); ?>" data-floor="<?php echo htmlspecialchars($floor16684); ?>" data-image="<?php echo base64_encode($upload_img16684); ?>" data-category="<?php echo htmlspecialchars($category16684); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16684); ?>" data-status="<?php echo htmlspecialchars($status16684); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16684); ?>;
position:absolute; top:448px; left:1130px;'>
                        </div>

                        <!-- ASSET 16685 -->
                        <img src='../image.php?id=16685' style='width:20px; cursor:pointer; position:absolute; top:390px; left:1090px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16685' class="asset-image" onclick='fetchAssetData(16685);' data-id="<?php echo $assetId16685; ?>" data-room="<?php echo htmlspecialchars($room16685); ?>" data-floor="<?php echo htmlspecialchars($floor16685); ?>" data-image="<?php echo base64_encode($upload_img16685); ?>" data-category="<?php echo htmlspecialchars($category16685); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16685); ?>" data-status="<?php echo htmlspecialchars($status16685); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16685); ?>;
position:absolute; top:390px; left:1100px;'>
                        </div>


                        <!-- ASSET 16686 -->
                        <img src='../image.php?id=16686' style='width:20px; cursor:pointer; position:absolute; top:500px; left:1090px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16686' class="asset-image" onclick='fetchAssetData(16686);' data-id="<?php echo $assetId16686; ?>" data-room="<?php echo htmlspecialchars($room16686); ?>" data-floor="<?php echo htmlspecialchars($floor16686); ?>" data-image="<?php echo base64_encode($upload_img16686); ?>" data-category="<?php echo htmlspecialchars($category16686); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16686); ?>" data-status="<?php echo htmlspecialchars($status16686); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16686); ?>;
position:absolute; top:510px; left:1100px;'>
                        </div>

                        <!-- ASSET 16687 -->
                        <img src='../image.php?id=16687' style='width:20px; cursor:pointer; position:absolute; top:448px; left:1079px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16687' class="asset-image" onclick='fetchAssetData(16687);' data-id="<?php echo $assetId16687; ?>" data-room="<?php echo htmlspecialchars($room16687); ?>" data-floor="<?php echo htmlspecialchars($floor16687); ?>" data-image="<?php echo base64_encode($upload_img16687); ?>" data-category="<?php echo htmlspecialchars($category16687); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16687); ?>" data-status="<?php echo htmlspecialchars($status16687); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16687); ?>;
position:absolute; top:448px; left:1088px;'>
                        </div>

                        <!-- ASSET 16688 -->
                        <img src='../image.php?id=16688' style='width:20px; cursor:pointer; position:absolute; top:420px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16688' class="asset-image" onclick='fetchAssetData(16688);' data-id="<?php echo $assetId16688; ?>" data-room="<?php echo htmlspecialchars($room16688); ?>" data-floor="<?php echo htmlspecialchars($floor16688); ?>" data-image="<?php echo base64_encode($upload_img16688); ?>" data-category="<?php echo htmlspecialchars($category16688); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16688); ?>" data-status="<?php echo htmlspecialchars($status16688); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16688); ?>;
position:absolute; top:420px; left:1020px;'>
                        </div>

                        <!-- ASSET 16761 -->
                        <img src='../image.php?id=16761' style='width:20px; cursor:pointer; position:absolute; top:480px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16761' class="asset-image" onclick='fetchAssetData(16761);' data-id="<?php echo $assetId16761; ?>" data-room="<?php echo htmlspecialchars($room16761); ?>" data-floor="<?php echo htmlspecialchars($floor16761); ?>" data-image="<?php echo base64_encode($upload_img16761); ?>" data-category="<?php echo htmlspecialchars($category16761); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16761); ?>" data-status="<?php echo htmlspecialchars($status16761); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16761); ?>;
position:absolute; top:480px; left:1020px;'>
                        </div>


                        <!-- END OF IB101A -->

                        <!-- START OF IB102A LIGHTS -->

                        <!-- ASSET 16689 -->
                        <img src='../image.php?id=16689' style='width:20px; cursor:pointer; position:absolute; top:90px; left:1163px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16689' class="asset-image" onclick='fetchAssetData(16689);' data-id="<?php echo $assetId16689; ?>" data-room="<?php echo htmlspecialchars($room16689); ?>" data-floor="<?php echo htmlspecialchars($floor16689); ?>" data-image="<?php echo base64_encode($upload_img16689); ?>" data-category="<?php echo htmlspecialchars($category16689); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16689); ?>" data-status="<?php echo htmlspecialchars($status16689); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16689); ?>;
position:absolute; top:90px; left:1173px;'>
                        </div>

                        <!-- ASSET 16690 -->
                        <img src='../image.php?id=16690' style='width:20px; cursor:pointer; position:absolute; top:90px; left:1055px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16690' class="asset-image" onclick='fetchAssetData(16690);' data-id="<?php echo $assetId16690; ?>" data-room="<?php echo htmlspecialchars($room16690); ?>" data-floor="<?php echo htmlspecialchars($floor16690); ?>" data-image="<?php echo base64_encode($upload_img16690); ?>" data-category="<?php echo htmlspecialchars($category16690); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16690); ?>" data-status="<?php echo htmlspecialchars($status16690); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16690); ?>;
position:absolute; top:90px; left:1065px;'>
                        </div>

                        <!-- ASSET 16691 -->
                        <img src='../image.php?id=16691' style='width:20px; cursor:pointer; position:absolute; top:135px; left:1110px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16691' class="asset-image" onclick='fetchAssetData(16691);' data-id="<?php echo $assetId16691; ?>" data-room="<?php echo htmlspecialchars($room16691); ?>" data-floor="<?php echo htmlspecialchars($floor16691); ?>" data-image="<?php echo base64_encode($upload_img16691); ?>" data-category="<?php echo htmlspecialchars($category16691); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16691); ?>" data-status="<?php echo htmlspecialchars($status16691); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16691); ?>;
position:absolute; top:135px; left:1120px;'>
                        </div>

                        <!-- ASSET 16692 -->
                        <img src='../image.php?id=16692' style='width:20px; cursor:pointer; position:absolute; top:180px; left:1163px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16692' class="asset-image" onclick='fetchAssetData(16692);' data-id="<?php echo $assetId16692; ?>" data-room="<?php echo htmlspecialchars($room16692); ?>" data-floor="<?php echo htmlspecialchars($floor16692); ?>" data-image="<?php echo base64_encode($upload_img16692); ?>" data-category="<?php echo htmlspecialchars($category16692); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16692); ?>" data-status="<?php echo htmlspecialchars($status16692); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16692); ?>;
position:absolute; top:180px; left:1173px;'>
                        </div>


                        <!-- ASSET 16693 -->
                        <img src='../image.php?id=16693' style='width:20px; cursor:pointer; position:absolute; top:180px; left:1055px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16693' class="asset-image" onclick='fetchAssetData(16693);' data-id="<?php echo $assetId16693; ?>" data-room="<?php echo htmlspecialchars($room16693); ?>" data-floor="<?php echo htmlspecialchars($floor16693); ?>" data-image="<?php echo base64_encode($upload_img16693); ?>" data-category="<?php echo htmlspecialchars($category16693); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16693); ?>" data-status="<?php echo htmlspecialchars($status16693); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16693); ?>;
position:absolute; top:180px; left:1065px;'>
                        </div>

                        <!-- ASSET 16694 -->
                        <img src='../image.php?id=16694' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1080px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16694' class="asset-image" onclick='fetchAssetData(16694);' data-id="<?php echo $assetId16694; ?>" data-room="<?php echo htmlspecialchars($room16694); ?>" data-floor="<?php echo htmlspecialchars($floor16694); ?>" data-image="<?php echo base64_encode($upload_img16694); ?>" data-category="<?php echo htmlspecialchars($category16694); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16694); ?>" data-status="<?php echo htmlspecialchars($status16694); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16694); ?>;
position:absolute; top:215px; left:1090px;'>
                        </div>

                        <!-- ASSET 16695 -->
                        <img src='../image.php?id=16695' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1135px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16695' class="asset-image" onclick='fetchAssetData(16695);' data-id="<?php echo $assetId16695; ?>" data-room="<?php echo htmlspecialchars($room16695); ?>" data-floor="<?php echo htmlspecialchars($floor16695); ?>" data-image="<?php echo base64_encode($upload_img16695); ?>" data-category="<?php echo htmlspecialchars($category16695); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16695); ?>" data-status="<?php echo htmlspecialchars($status16695); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16695); ?>;
position:absolute; top:215px; left:1145px;'>
                        </div>

                        <!-- ASSET 16696 -->
                        <img src='../image.php?id=16696' style='width:20px; cursor:pointer; position:absolute; top:65px; left:925px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16696' class="asset-image" onclick='fetchAssetData(16696);' data-id="<?php echo $assetId16696; ?>" data-room="<?php echo htmlspecialchars($room16696); ?>" data-floor="<?php echo htmlspecialchars($floor16696); ?>" data-image="<?php echo base64_encode($upload_img16696); ?>" data-category="<?php echo htmlspecialchars($category16696); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16696); ?>" data-status="<?php echo htmlspecialchars($status16696); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16696); ?>;
position:absolute; top:65px; left:935px;'>
                        </div>


                        <!-- ASSET 16697 -->
                        <img src='../image.php?id=16697' style='width:20px; cursor:pointer; position:absolute; top:65px; left:1030px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16697' class="asset-image" onclick='fetchAssetData(16697);' data-id="<?php echo $assetId16697; ?>" data-room="<?php echo htmlspecialchars($room16697); ?>" data-floor="<?php echo htmlspecialchars($floor16697); ?>" data-image="<?php echo base64_encode($upload_img16697); ?>" data-category="<?php echo htmlspecialchars($category16697); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16697); ?>" data-status="<?php echo htmlspecialchars($status16697); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16697); ?>;
position:absolute; top:65px; left:1040px;'>
                        </div>

                        <!-- ASSET 16698 -->
                        <img src='../image.php?id=16698' style='width:20px; cursor:pointer; position:absolute; top:110px; left:978px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16698' class="asset-image" onclick='fetchAssetData(16698);' data-id="<?php echo $assetId16698; ?>" data-room="<?php echo htmlspecialchars($room16698); ?>" data-floor="<?php echo htmlspecialchars($floor16698); ?>" data-image="<?php echo base64_encode($upload_img16698); ?>" data-category="<?php echo htmlspecialchars($category16698); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16698); ?>" data-status="<?php echo htmlspecialchars($status16698); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16698); ?>;
position:absolute; top:110px; left:988px;'>
                        </div>

                        <!-- ASSET 16699 -->
                        <img src='../image.php?id=16699' style='width:20px; cursor:pointer; position:absolute; top:140px; left:920px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16699' class="asset-image" onclick='fetchAssetData(16699);' data-id="<?php echo $assetId16699; ?>" data-room="<?php echo htmlspecialchars($room16699); ?>" data-floor="<?php echo htmlspecialchars($floor16699); ?>" data-image="<?php echo base64_encode($upload_img16699); ?>" data-category="<?php echo htmlspecialchars($category16699); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16699); ?>" data-status="<?php echo htmlspecialchars($status16699); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16699); ?>;
position:absolute; top:140px; left:930px;'>
                        </div>

                        <!-- ASSET 16700 -->
                        <img src='../image.php?id=16700' style='width:20px; cursor:pointer; position:absolute; top:140px; left:1035px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16700' class="asset-image" onclick='fetchAssetData(16700);' data-id="<?php echo $assetId16700; ?>" data-room="<?php echo htmlspecialchars($room16700); ?>" data-floor="<?php echo htmlspecialchars($floor16700); ?>" data-image="<?php echo base64_encode($upload_img16700); ?>" data-category="<?php echo htmlspecialchars($category16700); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16700); ?>" data-status="<?php echo htmlspecialchars($status16700); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16700); ?>;
position:absolute; top:140px; left:1045px;'>
                        </div>


                        <!-- ASSET 16701 -->
                        <img src='../image.php?id=16701' style='width:20px; cursor:pointer; position:absolute; top:215px; left:1010px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16701' class="asset-image" onclick='fetchAssetData(16701);' data-id="<?php echo $assetId16701; ?>" data-room="<?php echo htmlspecialchars($room16701); ?>" data-floor="<?php echo htmlspecialchars($floor16701); ?>" data-image="<?php echo base64_encode($upload_img16701); ?>" data-category="<?php echo htmlspecialchars($category16701); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16701); ?>" data-status="<?php echo htmlspecialchars($status16701); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16701); ?>;
position:absolute; top:215px; left:1020px;'>
                        </div>

                        <!-- ASSET 16702 -->
                        <img src='../image.php?id=16702' style='width:20px; cursor:pointer; position:absolute; top:215px; left:945px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16702' class="asset-image" onclick='fetchAssetData(16702);' data-id="<?php echo $assetId16702; ?>" data-room="<?php echo htmlspecialchars($room16702); ?>" data-floor="<?php echo htmlspecialchars($floor16702); ?>" data-image="<?php echo base64_encode($upload_img16702); ?>" data-category="<?php echo htmlspecialchars($category16702); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16702); ?>" data-status="<?php echo htmlspecialchars($status16702); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16702); ?>;
position:absolute; top:215px; left:955px;'>
                        </div>

                        <!-- ASSET 16703 -->
                        <img src='../image.php?id=16703' style='width:20px; cursor:pointer; position:absolute; top:65px; left:795px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16703' class="asset-image" onclick='fetchAssetData(16703);' data-id="<?php echo $assetId16703; ?>" data-room="<?php echo htmlspecialchars($room16703); ?>" data-floor="<?php echo htmlspecialchars($floor16703); ?>" data-image="<?php echo base64_encode($upload_img16703); ?>" data-category="<?php echo htmlspecialchars($category16703); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16703); ?>" data-status="<?php echo htmlspecialchars($status16703); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16703); ?>;
position:absolute; top:65px; left:805px;'>
                        </div>

                        <!-- ASSET 16704 -->
                        <img src='../image.php?id=16704' style='width:20px; cursor:pointer; position:absolute; top:65px; left:900px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16704' class="asset-image" onclick='fetchAssetData(16704);' data-id="<?php echo $assetId16704; ?>" data-room="<?php echo htmlspecialchars($room16704); ?>" data-floor="<?php echo htmlspecialchars($floor16704); ?>" data-image="<?php echo base64_encode($upload_img16704); ?>" data-category="<?php echo htmlspecialchars($category16704); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16704); ?>" data-status="<?php echo htmlspecialchars($status16704); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16704); ?>;
position:absolute; top:65px; left:910px;'>
                        </div>

                        <!-- ASSET 16705 -->
                        <img src='../image.php?id=16705' style='width:20px; cursor:pointer; position:absolute; top:110px; left:848px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16705' class="asset-image" onclick='fetchAssetData(16705);' data-id="<?php echo $assetId16705; ?>" data-room="<?php echo htmlspecialchars($room16705); ?>" data-floor="<?php echo htmlspecialchars($floor16705); ?>" data-image="<?php echo base64_encode($upload_img16705); ?>" data-category="<?php echo htmlspecialchars($category16705); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16705); ?>" data-status="<?php echo htmlspecialchars($status16705); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16705); ?>;
position:absolute; top:110px; left:858px;'>
                        </div>

                        <!-- ASSET 16706 -->
                        <img src='../image.php?id=16706' style='width:20px; cursor:pointer; position:absolute; top:140px; left:790px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16706' class="asset-image" onclick='fetchAssetData(16706);' data-id="<?php echo $assetId16706; ?>" data-room="<?php echo htmlspecialchars($room16706); ?>" data-floor="<?php echo htmlspecialchars($floor16706); ?>" data-image="<?php echo base64_encode($upload_img16706); ?>" data-category="<?php echo htmlspecialchars($category16706); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16706); ?>" data-status="<?php echo htmlspecialchars($status16706); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16706); ?>;
position:absolute; top:140px; left:800px;'>
                        </div>

                        <!-- ASSET 16707 -->
                        <img src='../image.php?id=16707' style='width:20px; cursor:pointer; position:absolute; top:140px; left:905px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16707' class="asset-image" onclick='fetchAssetData(16707);' data-id="<?php echo $assetId16707; ?>" data-room="<?php echo htmlspecialchars($room16707); ?>" data-floor="<?php echo htmlspecialchars($floor16707); ?>" data-image="<?php echo base64_encode($upload_img16707); ?>" data-category="<?php echo htmlspecialchars($category16707); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16707); ?>" data-status="<?php echo htmlspecialchars($status16707); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16707); ?>;
position:absolute; top:140px; left:915px;'>
                        </div>

                        <!-- ASSET 16708 -->
                        <img src='../image.php?id=16708' style='width:20px; cursor:pointer; position:absolute; top:215px; left:810px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16708' class="asset-image" onclick='fetchAssetData(16708);' data-id="<?php echo $assetId16708; ?>" data-room="<?php echo htmlspecialchars($room16708); ?>" data-floor="<?php echo htmlspecialchars($floor16708); ?>" data-image="<?php echo base64_encode($upload_img16708); ?>" data-category="<?php echo htmlspecialchars($category16708); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16708); ?>" data-status="<?php echo htmlspecialchars($status16708); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16708); ?>;
position:absolute; top:215px; left:820px;'>
                        </div>

                        <!-- ASSET 16709 -->
                        <img src='../image.php?id=16709' style='width:20px; cursor:pointer; position:absolute; top:215px; left:880px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16709' class="asset-image" onclick='fetchAssetData(16709);' data-id="<?php echo $assetId16709; ?>" data-room="<?php echo htmlspecialchars($room16709); ?>" data-floor="<?php echo htmlspecialchars($floor16709); ?>" data-image="<?php echo base64_encode($upload_img16709); ?>" data-category="<?php echo htmlspecialchars($category16709); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16709); ?>" data-status="<?php echo htmlspecialchars($status16709); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16709); ?>;
position:absolute; top:215px; left:890px;'>
                        </div>

                        <!-- END OF IB104A -->
                        <!-- START OF IB105A -->

                        <!-- ASSET 16710 -->
                        <img src='../image.php?id=16710' style='width:20px; cursor:pointer; position:absolute; top:65px; left:665px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16710' class="asset-image" onclick='fetchAssetData(16710);' data-id="<?php echo $assetId16710; ?>" data-room="<?php echo htmlspecialchars($room16710); ?>" data-floor="<?php echo htmlspecialchars($floor16710); ?>" data-image="<?php echo base64_encode($upload_img16710); ?>" data-category="<?php echo htmlspecialchars($category16710); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16710); ?>" data-status="<?php echo htmlspecialchars($status16710); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16710); ?>;
position:absolute; top:65px; left:675px;'>
                        </div>

                        <!-- ASSET 16711 -->
                        <img src='../image.php?id=16711' style='width:20px; cursor:pointer; position:absolute; top:65px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16711' class="asset-image" onclick='fetchAssetData(16711);' data-id="<?php echo $assetId16711; ?>" data-room="<?php echo htmlspecialchars($room16711); ?>" data-floor="<?php echo htmlspecialchars($floor16711); ?>" data-image="<?php echo base64_encode($upload_img16711); ?>" data-category="<?php echo htmlspecialchars($category16711); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16711); ?>" data-status="<?php echo htmlspecialchars($status16711); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16711); ?>;
position:absolute; top:65px; left:780px;'>
                        </div>

                        <!-- ASSET 16712 -->
                        <img src='../image.php?id=16712' style='width:20px; cursor:pointer; position:absolute; top:110px; left:718px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16712' class="asset-image" onclick='fetchAssetData(16712);' data-id="<?php echo $assetId16712; ?>" data-room="<?php echo htmlspecialchars($room16712); ?>" data-floor="<?php echo htmlspecialchars($floor16712); ?>" data-image="<?php echo base64_encode($upload_img16712); ?>" data-category="<?php echo htmlspecialchars($category16712); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16712); ?>" data-status="<?php echo htmlspecialchars($status16712); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16712); ?>;
position:absolute; top:110px; left:728px;'>
                        </div>


                        <!-- ASSET 16713 -->
                        <img src='../image.php?id=16713' style='width:20px; cursor:pointer; position:absolute; top:140px; left:660px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16713' class="asset-image" onclick='fetchAssetData(16713);' data-id="<?php echo $assetId16713; ?>" data-room="<?php echo htmlspecialchars($room16713); ?>" data-floor="<?php echo htmlspecialchars($floor16713); ?>" data-image="<?php echo base64_encode($upload_img16713); ?>" data-category="<?php echo htmlspecialchars($category16713); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16713); ?>" data-status="<?php echo htmlspecialchars($status16713); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16713); ?>;
position:absolute; top:140px; left:670px;'>
                        </div>

                        <!-- ASSET 16714 -->
                        <img src='../image.php?id=16714' style='width:20px; cursor:pointer; position:absolute; top:140px; left:775px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16714' class="asset-image" onclick='fetchAssetData(16714);' data-id="<?php echo $assetId16714; ?>" data-room="<?php echo htmlspecialchars($room16714); ?>" data-floor="<?php echo htmlspecialchars($floor16714); ?>" data-image="<?php echo base64_encode($upload_img16714); ?>" data-category="<?php echo htmlspecialchars($category16714); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16714); ?>" data-status="<?php echo htmlspecialchars($status16714); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16714); ?>;
position:absolute; top:140px; left:785px;'>
                        </div>

                        <!-- ASSET 16715 -->
                        <img src='../image.php?id=16715' style='width:20px; cursor:pointer; position:absolute; top:215px; left:685px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16715' class="asset-image" onclick='fetchAssetData(16715);' data-id="<?php echo $assetId16715; ?>" data-room="<?php echo htmlspecialchars($room16715); ?>" data-floor="<?php echo htmlspecialchars($floor16715); ?>" data-image="<?php echo base64_encode($upload_img16715); ?>" data-category="<?php echo htmlspecialchars($category16715); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16715); ?>" data-status="<?php echo htmlspecialchars($status16715); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16715); ?>;
position:absolute; top:215px; left:695px;'>
                        </div>

                        <!-- ASSET 16716 -->
                        <img src='../image.php?id=16716' style='width:20px; cursor:pointer; position:absolute; top:215px; left:750px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16716' class="asset-image" onclick='fetchAssetData(16716);' data-id="<?php echo $assetId16716; ?>" data-room="<?php echo htmlspecialchars($room16716); ?>" data-floor="<?php echo htmlspecialchars($floor16716); ?>" data-image="<?php echo base64_encode($upload_img16716); ?>" data-category="<?php echo htmlspecialchars($category16716); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16716); ?>" data-status="<?php echo htmlspecialchars($status16716); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16716); ?>;
position:absolute; top:215px; left:760px;'>
                        </div>


                        <!-- END OF IB105A -->

                        <!-- START OF IB106A -->

                        <!-- ASSET 16717 -->
                        <img src='../image.php?id=16717' style='width:20px; cursor:pointer; position:absolute; top:65px; left:440px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16717' class="asset-image" onclick='fetchAssetData(16717);' data-id="<?php echo $assetId16717; ?>" data-room="<?php echo htmlspecialchars($room16717); ?>" data-floor="<?php echo htmlspecialchars($floor16717); ?>" data-image="<?php echo base64_encode($upload_img16717); ?>" data-category="<?php echo htmlspecialchars($category16717); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16717); ?>" data-status="<?php echo htmlspecialchars($status16717); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16717); ?>;
position:absolute; top:65px; left:450px;'>
                        </div>

                        <!-- ASSET 16718 -->
                        <img src='../image.php?id=16718' style='width:20px; cursor:pointer; position:absolute; top:65px; left:545px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16718' class="asset-image" onclick='fetchAssetData(16718);' data-id="<?php echo $assetId16718; ?>" data-room="<?php echo htmlspecialchars($room16718); ?>" data-floor="<?php echo htmlspecialchars($floor16718); ?>" data-image="<?php echo base64_encode($upload_img16718); ?>" data-category="<?php echo htmlspecialchars($category16718); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName16718); ?>" data-status="<?php echo htmlspecialchars($status16718); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status16718); ?>;
position:absolute; top:65px; left:555px;'>
                        </div>

                        <!-- ASSET 18201 -->
                        <img src='../image.php?id=18201' style='width:20px; cursor:pointer; position:absolute; top:110px; left:493px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18201' class="asset-image" onclick='fetchAssetData(18201);' data-id="<?php echo $assetId18201; ?>" data-room="<?php echo htmlspecialchars($room18201); ?>" data-floor="<?php echo htmlspecialchars($floor18201); ?>" data-image="<?php echo base64_encode($upload_img18201); ?>" data-category="<?php echo htmlspecialchars($category18201); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18201); ?>" data-status="<?php echo htmlspecialchars($status18201); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18201); ?>;
position:absolute; top:110px; left:503px;'>
                        </div>

                        <!-- ASSET 18202 -->
                        <img src='../image.php?id=18202' style='width:20px; cursor:pointer; position:absolute; top:140px; left:435px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18202' class="asset-image" onclick='fetchAssetData(18202);' data-id="<?php echo $assetId18202; ?>" data-room="<?php echo htmlspecialchars($room18202); ?>" data-floor="<?php echo htmlspecialchars($floor18202); ?>" data-image="<?php echo base64_encode($upload_img18202); ?>" data-category="<?php echo htmlspecialchars($category18202); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18202); ?>" data-status="<?php echo htmlspecialchars($status18202); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18202); ?>;
position:absolute; top:140px; left:445px;'>
                        </div>

                        <!-- ASSET 18203 -->
                        <img src='../image.php?id=18203' style='width:20px; cursor:pointer; position:absolute; top:140px; left:550px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18203' class="asset-image" onclick='fetchAssetData(18203);' data-id="<?php echo $assetId18203; ?>" data-room="<?php echo htmlspecialchars($room18203); ?>" data-floor="<?php echo htmlspecialchars($floor18203); ?>" data-image="<?php echo base64_encode($upload_img18203); ?>" data-category="<?php echo htmlspecialchars($category18203); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18203); ?>" data-status="<?php echo htmlspecialchars($status18203); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18203); ?>;
position:absolute; top:140px; left:560px;'>
                        </div>

                        <!-- ASSET 18204 -->
                        <img src='../image.php?id=18204' style='width:20px; cursor:pointer; position:absolute; top:215px; left:460px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18204' class="asset-image" onclick='fetchAssetData(18204);' data-id="<?php echo $assetId18204; ?>" data-room="<?php echo htmlspecialchars($room18204); ?>" data-floor="<?php echo htmlspecialchars($floor18204); ?>" data-image="<?php echo base64_encode($upload_img18204); ?>" data-category="<?php echo htmlspecialchars($category18204); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18204); ?>" data-status="<?php echo htmlspecialchars($status18204); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18204); ?>;
position:absolute; top:215px; left:470px;'>
                        </div>

                        <!-- ASSET 18205 -->
                        <img src='../image.php?id=18205' style='width:20px; cursor:pointer; position:absolute; top:215px; left:525px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18205' class="asset-image" onclick='fetchAssetData(18205);' data-id="<?php echo $assetId18205; ?>" data-room="<?php echo htmlspecialchars($room18205); ?>" data-floor="<?php echo htmlspecialchars($floor18205); ?>" data-image="<?php echo base64_encode($upload_img18205); ?>" data-category="<?php echo htmlspecialchars($category18205); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18205); ?>" data-status="<?php echo htmlspecialchars($status18205); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18205); ?>;
position:absolute; top:215px; left:535px;'>
                        </div>

                        <!-- ASSET 18206 -->
                        <img src='../image.php?id=18206' style='width:20px; cursor:pointer; position:absolute; top:65px; left:310px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18206' class="asset-image" onclick='fetchAssetData(18206);' data-id="<?php echo $assetId18206; ?>" data-room="<?php echo htmlspecialchars($room18206); ?>" data-floor="<?php echo htmlspecialchars($floor18206); ?>" data-image="<?php echo base64_encode($upload_img18206); ?>" data-category="<?php echo htmlspecialchars($category18206); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18206); ?>" data-status="<?php echo htmlspecialchars($status18206); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18206); ?>;
position:absolute; top:65px; left:320px;'>
                        </div>


                        <!-- ASSET 18207 -->
                        <img src='../image.php?id=18207' style='width:20px; cursor:pointer; position:absolute; top:65px; left:415px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18207' class="asset-image" onclick='fetchAssetData(18207);' data-id="<?php echo $assetId18207; ?>" data-room="<?php echo htmlspecialchars($room18207); ?>" data-floor="<?php echo htmlspecialchars($floor18207); ?>" data-image="<?php echo base64_encode($upload_img18207); ?>" data-category="<?php echo htmlspecialchars($category18207); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18207); ?>" data-status="<?php echo htmlspecialchars($status18207); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18207); ?>;
position:absolute; top:65px; left:425px;'>
                        </div>

                        <!-- ASSET 18208 -->
                        <img src='../image.php?id=18208' style='width:20px; cursor:pointer; position:absolute; top:110px; left:363px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18208' class="asset-image" onclick='fetchAssetData(18208);' data-id="<?php echo $assetId18208; ?>" data-room="<?php echo htmlspecialchars($room18208); ?>" data-floor="<?php echo htmlspecialchars($floor18208); ?>" data-image="<?php echo base64_encode($upload_img18208); ?>" data-category="<?php echo htmlspecialchars($category18208); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18208); ?>" data-status="<?php echo htmlspecialchars($status18208); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18208); ?>;
position:absolute; top:110px; left:373px;'>
                        </div>

                        <!-- ASSET 18209 -->
                        <img src='../image.php?id=18209' style='width:20px; cursor:pointer; position:absolute; top:140px; left:305px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18209' class="asset-image" onclick='fetchAssetData(18209);' data-id="<?php echo $assetId18209; ?>" data-room="<?php echo htmlspecialchars($room18209); ?>" data-floor="<?php echo htmlspecialchars($floor18209); ?>" data-image="<?php echo base64_encode($upload_img18209); ?>" data-category="<?php echo htmlspecialchars($category18209); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18209); ?>" data-status="<?php echo htmlspecialchars($status18209); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18209); ?>;
position:absolute; top:140px; left:315px;'>
                        </div>

                        <!-- ASSET 18210 -->
                        <img src='../image.php?id=18210' style='width:20px; cursor:pointer; position:absolute; top:140px; left:420px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18210' class="asset-image" onclick='fetchAssetData(18210);' data-id="<?php echo $assetId18210; ?>" data-room="<?php echo htmlspecialchars($room18210); ?>" data-floor="<?php echo htmlspecialchars($floor18210); ?>" data-image="<?php echo base64_encode($upload_img18210); ?>" data-category="<?php echo htmlspecialchars($category18210); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18210); ?>" data-status="<?php echo htmlspecialchars($status18210); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18210); ?>;
position:absolute; top:140px; left:430px;'>
                        </div>


                        <!-- ASSET 18211 -->
                        <img src='../image.php?id=18211' style='width:20px; cursor:pointer; position:absolute; top:210px; left:330px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18211' class="asset-image" onclick='fetchAssetData(18211);' data-id="<?php echo $assetId18211; ?>" data-room="<?php echo htmlspecialchars($room18211); ?>" data-floor="<?php echo htmlspecialchars($floor18211); ?>" data-image="<?php echo base64_encode($upload_img18211); ?>" data-category="<?php echo htmlspecialchars($category18211); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18211); ?>" data-status="<?php echo htmlspecialchars($status18211); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18211); ?>;
position:absolute; top:210px; left:340px;'>
                        </div>

                        <!-- ASSET 18212 -->
                        <img src='../image.php?id=18212' style='width:20px; cursor:pointer; position:absolute; top:210px; left:400px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18212' class="asset-image" onclick='fetchAssetData(18212);' data-id="<?php echo $assetId18212; ?>" data-room="<?php echo htmlspecialchars($room18212); ?>" data-floor="<?php echo htmlspecialchars($floor18212); ?>" data-image="<?php echo base64_encode($upload_img18212); ?>" data-category="<?php echo htmlspecialchars($category18212); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18212); ?>" data-status="<?php echo htmlspecialchars($status18212); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18212); ?>;
position:absolute; top:210px; left:410px;'>
                        </div>

                        <!-- ASSET 18213 -->
                        <img src='../image.php?id=18213' style='width:20px; cursor:pointer; position:absolute; top:65px; left:180px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18213' class="asset-image" onclick='fetchAssetData(18213);' data-id="<?php echo $assetId18213; ?>" data-room="<?php echo htmlspecialchars($room18213); ?>" data-floor="<?php echo htmlspecialchars($floor18213); ?>" data-image="<?php echo base64_encode($upload_img18213); ?>" data-category="<?php echo htmlspecialchars($category18213); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18213); ?>" data-status="<?php echo htmlspecialchars($status18213); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18213); ?>;
position:absolute; top:65px; left:190px;'>
                        </div>

                        <!-- ASSET 18214 -->
                        <img src='../image.php?id=18214' style='width:20px; cursor:pointer; position:absolute; top:65px; left:285px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18214' class="asset-image" onclick='fetchAssetData(18214);' data-id="<?php echo $assetId18214; ?>" data-room="<?php echo htmlspecialchars($room18214); ?>" data-floor="<?php echo htmlspecialchars($floor18214); ?>" data-image="<?php echo base64_encode($upload_img18214); ?>" data-category="<?php echo htmlspecialchars($category18214); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18214); ?>" data-status="<?php echo htmlspecialchars($status18214); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18214); ?>;
position:absolute; top:65px; left:295px;'>
                        </div>

                        <!-- ASSET 18215 -->
                        <img src='../image.php?id=18215' style='width:20px; cursor:pointer; position:absolute; top:110px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18215' class="asset-image" onclick='fetchAssetData(18215);' data-id="<?php echo $assetId18215; ?>" data-room="<?php echo htmlspecialchars($room18215); ?>" data-floor="<?php echo htmlspecialchars($floor18215); ?>" data-image=" <?php echo base64_encode($upload_img18215); ?>" data-category="<?php echo htmlspecialchars($category18215); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18215); ?>" data-status="<?php echo htmlspecialchars($status18215); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18215); ?>;
position:absolute; top:110px; left:243px;'>
                        </div>

                        <!-- ASSET 18216 -->
                        <img src='../image.php?id=18216' style='width:20px; cursor:pointer; position:absolute; top:140px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18216' class="asset-image" onclick='fetchAssetData(18216);' data-id="<?php echo $assetId18216; ?>" data-room="<?php echo htmlspecialchars($room18216); ?>" data-floor="<?php echo htmlspecialchars($floor18216); ?>" data-image="<?php echo base64_encode($upload_img18216); ?>" data-category="<?php echo htmlspecialchars($category18216); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18216); ?>" data-status="<?php echo htmlspecialchars($status18216); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18216); ?>;
position:absolute; top:140px; left:185px;'>
                        </div>

                        <!-- ASSET 18217 -->
                        <img src='../image.php?id=18217' style='width:20px; cursor:pointer; position:absolute; top:140px; left:290px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18217' class="asset-image" onclick='fetchAssetData(18217);' data-id="<?php echo $assetId18217; ?>" data-room="<?php echo htmlspecialchars($room18217); ?>" data-floor="<?php echo htmlspecialchars($floor18217); ?>" data-image="<?php echo base64_encode($upload_img18217); ?>" data-category="<?php echo htmlspecialchars($category18217); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18217); ?>" data-status="<?php echo htmlspecialchars($status18217); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18217); ?>;
position:absolute; top:140px; left:300px;'>
                        </div>

                        <!-- ASSET 18218 -->
                        <img src='../image.php?id=18218' style='width:20px; cursor:pointer; position:absolute; top:210px; left:200px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18218' class="asset-image" onclick='fetchAssetData(18218);' data-id="<?php echo $assetId18218; ?>" data-room="<?php echo htmlspecialchars($room18218); ?>" data-floor="<?php echo htmlspecialchars($floor18218); ?>" data-image="<?php echo base64_encode($upload_img18218); ?>" data-category="<?php echo htmlspecialchars($category18218); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18218); ?>" data-status="<?php echo htmlspecialchars($status18218); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18218); ?>;
position:absolute; top:210px; left:210px;'>
                        </div>


                        <!-- ASSET 18219 -->
                        <img src='../image.php?id=18219' style='width:20px; cursor:pointer; position:absolute; top:210px; left:265px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18219' class="asset-image" onclick='fetchAssetData(18219);' data-id="<?php echo $assetId18219; ?>" data-room="<?php echo htmlspecialchars($room18219); ?>" data-floor="<?php echo htmlspecialchars($floor18219); ?>" data-image="<?php echo base64_encode($upload_img18219); ?>" data-category="<?php echo htmlspecialchars($category18219); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18219); ?>" data-status="<?php echo htmlspecialchars($status18219); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18219); ?>;
position:absolute; top:210px; left:275px;'>
                        </div>

                        <!-- ASSET 18220 -->
                        <img src='../image.php?id=18220' style='width:20px; cursor:pointer; position:absolute; top:90px; left:50px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18220' class="asset-image" onclick='fetchAssetData(18220);' data-id="<?php echo $assetId18220; ?>" data-room="<?php echo htmlspecialchars($room18220); ?>" data-floor="<?php echo htmlspecialchars($floor18220); ?>" data-image="<?php echo base64_encode($upload_img18220); ?>" data-category="<?php echo htmlspecialchars($category18220); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18220); ?>" data-status="<?php echo htmlspecialchars($status18220); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18220); ?>;
position:absolute; top:90px; left:60px;'>
                        </div>

                        <!-- ASSET 18221 -->
                        <img src='../image.php?id=18221' style='width:20px; cursor:pointer; position:absolute; top:90px; left:158px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18221' class="asset-image" onclick='fetchAssetData(18221);' data-id="<?php echo $assetId18221; ?>" data-room="<?php echo htmlspecialchars($room18221); ?>" data-floor="<?php echo htmlspecialchars($floor18221); ?>" data-image="<?php echo base64_encode($upload_img18221); ?>" data-category="<?php echo htmlspecialchars($category18221); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18221); ?>" data-status="<?php echo htmlspecialchars($status18221); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18221); ?>;
position:absolute; top:90px; left:168px;'>
                        </div>

                        <!-- ASSET 18222 -->
                        <img src='../image.php?id=18222' style='width:20px; cursor:pointer; position:absolute; top:130px; left:103px;' alt='Asset Image' data-bs-toggle=' modal' data-bs-target='#imageModal18222' class="asset-image" onclick='fetchAssetData(18222);' data-id="<?php echo $assetId18222; ?>" data-room="<?php echo htmlspecialchars($room18222); ?>" data-floor="<?php echo htmlspecialchars($floor18222); ?>" data-image="<?php echo base64_encode($upload_img18222); ?>" data-category="<?php echo htmlspecialchars($category18222); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18222); ?>" data-status="<?php echo htmlspecialchars($status18222); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18222); ?>;
position:absolute; top:130px; left:113px;'>
                        </div>


                        <!-- ASSET 18223 -->
                        <img src='../image.php?id=18223' style='width:20px; cursor:pointer; position:absolute; top:160px; left:45px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18223' class="asset-image" onclick='fetchAssetData(18223);' data-id="<?php echo $assetId18223; ?>" data-room="<?php echo htmlspecialchars($room18223); ?>" data-floor="<?php echo htmlspecialchars($floor18223); ?>" data-image="<?php echo base64_encode($upload_img18223); ?>" data-category="<?php echo htmlspecialchars($category18223); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18223); ?>" data-status="<?php echo htmlspecialchars($status18223); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18223); ?>;
position:absolute; top:160px; left:55px;'>
                        </div>

                        <!-- ASSET 18224 -->
                        <img src='../image.php?id=18224' style='width:20px; cursor:pointer; position:absolute; top:160px; left:158px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18224' class="asset-image" onclick='fetchAssetData(18224);' data-id="<?php echo $assetId18224; ?>" data-room="<?php echo htmlspecialchars($room18224); ?>" data-floor="<?php echo htmlspecialchars($floor18224); ?>" data-image="<?php echo base64_encode($upload_img18224); ?>" data-category="<?php echo htmlspecialchars($category18224); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18224); ?>" data-status="<?php echo htmlspecialchars($status18224); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18224); ?>;
position:absolute; top:160px; left:168px;'>
                        </div>

                        <!-- ASSET 18225 -->
                        <img src='../image.php?id=18225' style='width:20px; cursor:pointer; position:absolute; top:210px; left:70px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18225' class="asset-image" onclick='fetchAssetData(18225);' data-id="<?php echo $assetId18225; ?>" data-room="<?php echo htmlspecialchars($room18225); ?>" data-floor="<?php echo htmlspecialchars($floor18225); ?>" data-image="<?php echo base64_encode($upload_img18225); ?>" data-category="<?php echo htmlspecialchars($category18225); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18225); ?>" data-status="<?php echo htmlspecialchars($status18225); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18225); ?>;
position:absolute; top:210px; left:80px;'>
                        </div>

                        <!-- ASSET 18226 -->
                        <img src='../image.php?id=18226' style='width:20px; cursor:pointer; position:absolute; top:210px; left:140px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18226' class="asset-image" onclick='fetchAssetData(18226);' data-id="<?php echo $assetId18226; ?>" data-room="<?php echo htmlspecialchars($room18226); ?>" data-floor="<?php echo htmlspecialchars($floor18226); ?>" data-image="<?php echo base64_encode($upload_img18226); ?>" data-category="<?php echo htmlspecialchars($category18226); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18226); ?>" data-status="<?php echo htmlspecialchars($status18226); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18226); ?>;
position:absolute; top:210px; left:150px;'>
                        </div>

                        <!-- ASSET 18227 -->
                        <img src='../image.php?id=18227' style='width:20px; cursor:pointer; position:absolute; top:390px; left:50px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18227' class="asset-image" onclick='fetchAssetData(18227);' data-id="<?php echo $assetId18227; ?>" data-room="<?php echo htmlspecialchars($room18227); ?>" data-floor="<?php echo htmlspecialchars($floor18227); ?>" data-image="<?php echo base64_encode($upload_img18227); ?>" data-category="<?php echo htmlspecialchars($category18227); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18227); ?>" data-status="<?php echo htmlspecialchars($status18227); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18227); ?>;
position:absolute; top:390px; left:50px;'>
                        </div>

                        <!-- ASSET 18228 -->
                        <img src='../image.php?id=18228' style='width:20px; cursor:pointer; position:absolute; top:500px; left:50px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18228' class="asset-image" onclick='fetchAssetData(18228);' data-id="<?php echo $assetId18228; ?>" data-room="<?php echo htmlspecialchars($room18228); ?>" data-floor="<?php echo htmlspecialchars($floor18228); ?>" data-image="<?php echo base64_encode($upload_img18228); ?>" data-category="<?php echo htmlspecialchars($category18228); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18228); ?>" data-status="<?php echo htmlspecialchars($status18228); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18228); ?>;
position:absolute; top:500px; left:50px;'>
                        </div>

                        <!-- ASSET 18229 -->
                        <img src='../image.php?id=18229' style='width:20px; cursor:pointer; position:absolute; top:390px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18229' class="asset-image" onclick='fetchAssetData(18229);' data-id="<?php echo $assetId18229; ?>" data-room="<?php echo htmlspecialchars($room18229); ?>" data-floor="<?php echo htmlspecialchars($floor18229); ?>" data-image="<?php echo base64_encode($upload_img18229); ?>" data-category="<?php echo htmlspecialchars($category18229); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18229); ?>" data-status="<?php echo htmlspecialchars($status18229); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18229); ?>;
position:absolute; top:390px; left:130px;'>
                        </div>

                        <!-- ASSET 18230 -->
                        <img src='../image.php?id=18230' style='width:20px; cursor:pointer; position:absolute; top:500px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18230' class="asset-image" onclick='fetchAssetData(18230);' data-id="<?php echo $assetId18230; ?>" data-room="<?php echo htmlspecialchars($room18230); ?>" data-floor="<?php echo htmlspecialchars($floor18230); ?>" data-image="<?php echo base64_encode($upload_img18230); ?>" data-category="<?php echo htmlspecialchars($category18230); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18230); ?>" data-status="<?php echo htmlspecialchars($status18230); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18230); ?>;
position:absolute; top:500px; left:130px;'>
                        </div>

                        <!-- ASSET 18231 -->
                        <img src='../image.php?id=18231' style='width:20px; cursor:pointer; position:absolute; top:448px; left:93px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18231' class="asset-image" onclick='fetchAssetData(18231);' data-id="<?php echo $assetId18231; ?>" data-room="<?php echo htmlspecialchars($room18231); ?>" data-floor="<?php echo htmlspecialchars($floor18231); ?>" data-image="<?php echo base64_encode($upload_img18231); ?>" data-category="<?php echo htmlspecialchars($category18231); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18231); ?>" data-status="<?php echo htmlspecialchars($status18231); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18231); ?>;
position:absolute; top:448px; left:95px;'>
                        </div>

                        <!-- ASSET 18232 -->
                        <img src='../image.php?id=18232' style='width:20px; cursor:pointer; position:absolute; top:415px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18232' class="asset-image" onclick='fetchAssetData(18232);' data-id="<?php echo $assetId18232; ?>" data-room="<?php echo htmlspecialchars($room18232); ?>" data-floor="<?php echo htmlspecialchars($floor18232); ?>" data-image="<?php echo base64_encode($upload_img18232); ?>" data-category="<?php echo htmlspecialchars($category18232); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18232); ?>" data-status="<?php echo htmlspecialchars($status18232); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18232); ?>;
position:absolute; top:415px; left:205px;'>
                        </div>

                        <!-- ASSET 18233 -->
                        <img src='../image.php?id=18233' style='width:20px; cursor:pointer; position:absolute; top:485px; left:195px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal18233' class="asset-image" onclick='fetchAssetData(18233);' data-id="<?php echo $assetId18233; ?>" data-room="<?php echo htmlspecialchars($room18233); ?>" data-floor="<?php echo htmlspecialchars($floor18233); ?>" data-image="<?php echo base64_encode($upload_img18233); ?>" data-category="<?php echo htmlspecialchars($category18233); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName18233); ?>" data-status="<?php echo htmlspecialchars($status18233); ?>">
                        <div style=' width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status18233); ?>;
position:absolute; top:485px; left:205px;'>
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
                        <div class='modal fade' id='imageModal<?php echo $assetId; ?>' tabindex='-1' aria-labelledby='imageModalLabel<?php echo $assetId; ?>' aria-hidden='true'>
                            <div class='modal-dialog modal-xl modal-dialog-centered'>
                                <div class='modal-content'>
                                    <!-- Modal header -->
                                    <div class='modal-header'>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class='modal-body'>
                                        <form method="post" class="row g-3" enctype="multipart/form-data">
                                            <input type="hidden" name="assetId" value="<?php echo htmlspecialchars($assetId); ?>">
                                            <!--START DIV FOR IMAGE -->
                                            <!--First Row-->
                                            <!--IMAGE HERE-->
                                            <div class="col-12 center-content">
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img); ?>" alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                            </div>
                                            <!--END DIV FOR IMAGE -->
                                            <div class="col-4" style="display:none">
                                                <label for="assetId" class="form-label">Tracking #:</label>
                                                <input type="text" class="form-control" id="assetId" name="assetId" value="<?php echo htmlspecialchars($assetId); ?>" readonly />
                                            </div>
                                            <div class="col-4" style="display:none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" readonly />
                                            </div>
                                            <!--Second Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($room); ?>" readonly />
                                            </div>
                                            <div class="col-6" style="display:none">
                                                <input type="text" class="form-control  center-content" id="building" name="building" value="<?php echo htmlspecialchars($building); ?>" readonly />
                                            </div>
                                            <!--End of Second Row-->
                                            <!--Third Row-->
                                            <div class="col-6">
                                                <input type="text" class="form-control" id="floor" name="floor" value="<?php echo htmlspecialchars($floor); ?>" readonly />
                                            </div>
                                            <div class="col-12 center-content">
                                                <input type="text" class="form-control  center-content" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" readonly />
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
                                            <div class="col-2 Upload">
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
        <script src="../../../src/js/main.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>