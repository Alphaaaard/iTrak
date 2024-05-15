<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();

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


    $assetIds = [15815, 15816, 15817, 15818, 15819, 15820, 15821, 15822, 15823, 15824, 15825, 15826, 15827, 15828, 15829, 15830, 15831, 15832, 15833, 15834, 15835, 15836, 15837, 15838, 15839, 15840, 15841, 15842, 15843, 15844, 15845, 15846, 15847, 15848, 15849, 15850, 15851, 15852, 15853, 15854, 15855, 15856, 15857, 15858, 15859, 15860, 15861, 15862, 15863, 15864, 15865, 15866, 15867, 15868, 15869, 15870, 15871, 15872, 15873, 15874, 15875, 15876, 15877, 15878, 15879, 15880, 15881, 15882, 15883, 15884, 15885, 15886, 15887, 15888, 15889, 15890, 15891, 15892, 15893, 15894, 15895, 15896, 15897, 15898, 15899, 15900, 15901, 15902, 15903, 15904, 15905, 15906, 15907, 15908, 15909, 15910, 15911, 15912, 15913, 15914, 15915, 15916, 15917, 15918, 15919, 15920, 15921, 15922, 15923, 15924, 15925, 15926, 15927, 15928, 15929, 15930, 15931, 15932, 15933, 15934, 15935, 15936, 15937, 15938, 15939, 15940, 15941, 15942, 15943, 15944, 15945, 15946, 15947, 15948, 15949, 15950, 15951, 15952, 15953, 15954, 15955, 15956, 15957, 15958, 15959, 15960, 15961, 15962, 15963, 15964, 15965, 15966, 15967, 15968, 15969, 15970, 15971, 15972, 15973, 15974, 15975, 15976, 15977, 15978, 15979, 15980, 15981, 15982, 15983, 15984, 15985, 15986, 15987, 15988, 15989, 15990, 15991, 15992, 15993, 15994, 15995, 15996, 15997, 15998, 15999, 16000, 16001, 16002, 16003, 16004, 16005, 16006, 16007, 16008, 16009, 16010, 16011, 16012, 16013, 16014, 16015, 16016, 16017, 16018, 16019, 16020, 16021, 16022, 16023, 16024, 16025, 16026, 16027, 16028, 16029, 16030, 16031, 16032, 16033, 16034, 16035, 16036, 16037, 16038, 16039, 16040, 16041, 16042, 16043, 16044, 16045, 16046, 16047, 16048, 16049, 16050, 16051, 16052, 16053, 16054, 16055, 16056, 16057, 16058, 16059, 16060, 16061, 16062, 16063, 16064, 16065, 16066, 16067, 16068, 16069, 16070, 16071, 16072, 16073, 16074, 16075, 16076, 16077, 16078, 16079, 16080, 16081, 16082, 16083, 16084, 16085, 16086, 16087, 16088, 16089, 16090, 16091, 16092, 16093, 16094, 16095, 16096, 16097, 16098, 16099, 16100, 16101, 16102, 16103, 16104, 16105, 16106, 16107, 16108, 16109, 16110, 16111, 16112, 16113, 16114, 16115, 16116, 16117, 16118, 16119, 16120, 16121, 16122, 16123, 16124, 16125, 16126, 16127, 16128, 16129, 16130, 16131, 16132, 16133, 16134, 16135, 16136, 16137, 16138, 16139, 16140, 16141, 16142, 16143, 16144, 16145, 16146, 16147, 16148, 16149, 16150, 16151, 16152, 16153, 16154, 16155, 16156, 16157, 16158, 16159, 16160, 16161, 16162, 16163, 16164, 16165, 16166, 16167, 16168, 16169, 16170, 16171, 16172, 16173, 16174, 16175, 16176, 16177, 16178, 16179, 16180, 16181, 16182, 16183, 16184, 16185, 16186, 16187, 16188, 16189, 16190, 16191, 16192, 16193, 16194, 16195, 16196, 16197, 16198, 16199, 16200, 16201, 16202, 16203, 16204, 16205, 16206, 16207, 16208, 16209, 16210, 16211, 16212, 16213, 16214, 16215, 16216, 16217, 16218, 16219, 16220, 16221, 16222, 16223, 16224, 16225, 16226, 16227];


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
                header("Location: BEBF3.php");
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
    $assetIds = [15815, 15816, 15817, 15818, 15819, 15820, 15821, 15822, 15823, 15824, 15825, 15826, 15827, 15828, 15829, 15830, 15831, 15832, 15833, 15834, 15835, 15836, 15837, 15838, 15839, 15840, 15841, 15842, 15843, 15844, 15845, 15846, 15847, 15848, 15849, 15850, 15851, 15852, 15853, 15854, 15855, 15856, 15857, 15858, 15859, 15860, 15861, 15862, 15863, 15864, 15865, 15866, 15867, 15868, 15869, 15870, 15871, 15872, 15873, 15874, 15875, 15876, 15877, 15878, 15879, 15880, 15881, 15882, 15883, 15884, 15885, 15886, 15887, 15888, 15889, 15890, 15891, 15892, 15893, 15894, 15895, 15896, 15897, 15898, 15899, 15900, 15901, 15902, 15903, 15904, 15905, 15906, 15907, 15908, 15909, 15910, 15911, 15912, 15913, 15914, 15915, 15916, 15917, 15918, 15919, 15920, 15921, 15922, 15923, 15924, 15925, 15926, 15927, 15928, 15929, 15930, 15931, 15932, 15933, 15934, 15935, 15936, 15937, 15938, 15939, 15940, 15941, 15942, 15943, 15944, 15945, 15946, 15947, 15948, 15949, 15950, 15951, 15952, 15953, 15954, 15955, 15956, 15957, 15958, 15959, 15960, 15961, 15962, 15963, 15964, 15965, 15966, 15967, 15968, 15969, 15970, 15971, 15972, 15973, 15974, 15975, 15976, 15977, 15978, 15979, 15980, 15981, 15982, 15983, 15984, 15985, 15986, 15987, 15988, 15989, 15990, 15991, 15992, 15993, 15994, 15995, 15996, 15997, 15998, 15999, 16000, 16001, 16002, 16003, 16004, 16005, 16006, 16007, 16008, 16009, 16010, 16011, 16012, 16013, 16014, 16015, 16016, 16017, 16018, 16019, 16020, 16021, 16022, 16023, 16024, 16025, 16026, 16027, 16028, 16029, 16030, 16031, 16032, 16033, 16034, 16035, 16036, 16037, 16038, 16039, 16040, 16041, 16042, 16043, 16044, 16045, 16046, 16047, 16048, 16049, 16050, 16051, 16052, 16053, 16054, 16055, 16056, 16057, 16058, 16059, 16060, 16061, 16062, 16063, 16064, 16065, 16066, 16067, 16068, 16069, 16070, 16071, 16072, 16073, 16074, 16075, 16076, 16077, 16078, 16079, 16080, 16081, 16082, 16083, 16084, 16085, 16086, 16087, 16088, 16089, 16090, 16091, 16092, 16093, 16094, 16095, 16096, 16097, 16098, 16099, 16100, 16101, 16102, 16103, 16104, 16105, 16106, 16107, 16108, 16109, 16110, 16111, 16112, 16113, 16114, 16115, 16116, 16117, 16118, 16119, 16120, 16121, 16122, 16123, 16124, 16125, 16126, 16127, 16128, 16129, 16130, 16131, 16132, 16133, 16134, 16135, 16136, 16137, 16138, 16139, 16140, 16141, 16142, 16143, 16144, 16145, 16146, 16147, 16148, 16149, 16150, 16151, 16152, 16153, 16154, 16155, 16156, 16157, 16158, 16159, 16160, 16161, 16162, 16163, 16164, 16165, 16166, 16167, 16168, 16169, 16170, 16171, 16172, 16173, 16174, 16175, 16176, 16177, 16178, 16179, 16180, 16181, 16182, 16183, 16184, 16185, 16186, 16187, 16188, 16189, 16190, 16191, 16192, 16193, 16194, 16195, 16196, 16197, 16198, 16199, 16200, 16201, 16202, 16203, 16204, 16205, 16206, 16207, 16208, 16209, 16210, 16211, 16212, 16213, 16214, 16215, 16216, 16217, 16218, 16219, 16220, 16221, 16222, 16223, 16224, 16225, 16226, 16227];
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
                header("Location: BEBF3.php");
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
        <link rel="stylesheet" href="../../../src/css/map-container.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="../../src/js/locationTracker.js"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
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
            <a href="#" class="brand" title="logo">
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
                        <img class="Floor-container-1 .NEWBF1" src="../../../src/floors/belmonteB/BB4F.png" alt="">


                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/I-CHAIR.jpg" alt="" class="legend-img">
                                <p>CHAIR</p>
                            </div>

                            <div class="legend-item"><img src="../../../src/legend/I-TABLE.jpg" alt="" class="legend-img">
                                <p>TABLE</p>
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

                        <!-- Start of Hallway -->

                        <!-- ASSET 15850 -->
                        <img src='../image.php?id=15850' style='width:20px; cursor:pointer; position:absolute; top:170px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15850' onclick='fetchAssetData(15850);' class="asset-image" data-id="<?php echo $assetId15850; ?>" data-room="<?php echo htmlspecialchars($room15850); ?>" data-floor="<?php echo htmlspecialchars($floor15850); ?>" data-image="<?php echo base64_encode($upload_img15850); ?>" data-status="<?php echo htmlspecialchars($status15850); ?>" data-category="<?php echo htmlspecialchars($category15850); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15850); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15850); ?>; 
    position:absolute; top:170px; left:135px;'>
                        </div>

                        <!-- ASSET 15851 -->
                        <img src='../image.php?id=15851' style='width:20px; cursor:pointer; position:absolute; top:190px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15851' onclick='fetchAssetData(15851);' class="asset-image" data-id="<?php echo $assetId15851; ?>" data-room="<?php echo htmlspecialchars($room15851); ?>" data-floor="<?php echo htmlspecialchars($floor15851); ?>" data-image="<?php echo base64_encode($upload_img15851); ?>" data-status="<?php echo htmlspecialchars($status15851); ?>" data-category="<?php echo htmlspecialchars($category15851); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15851); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15851); ?>; 
    position:absolute; top:190px; left:135px;'>
                        </div>

                        <!-- ASSET 15852 -->
                        <img src='../image.php?id=15852' style='width:20px; cursor:pointer; position:absolute; top:210px; left:120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15852' onclick='fetchAssetData(15852);' class="asset-image" data-id="<?php echo $assetId15852; ?>" data-room="<?php echo htmlspecialchars($room15852); ?>" data-floor="<?php echo htmlspecialchars($floor15852); ?>" data-image="<?php echo base64_encode($upload_img15852); ?>" data-status="<?php echo htmlspecialchars($status15852); ?>" data-category="<?php echo htmlspecialchars($category15852); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15852); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15852); ?>; 
    position:absolute; top:210px; left:135px;'>
                        </div>

                        <!-- ASSET 15853 -->
                        <img src='../image.php?id=15853' style='width:20px; cursor:pointer; position:absolute; top:170px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15853' onclick='fetchAssetData(15853);' class="asset-image" data-id="<?php echo $assetId15853; ?>" data-room="<?php echo htmlspecialchars($room15853); ?>" data-floor="<?php echo htmlspecialchars($floor15853); ?>" data-image="<?php echo base64_encode($upload_img15853); ?>" data-category="<?php echo htmlspecialchars($category15853); ?>" data-status="<?php echo htmlspecialchars($status15853); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15853); ?>">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15853); ?>; 
    position:absolute; top:170px; left:190px;'>
                        </div>

                        <!-- ASSET 15855 -->
                        <img src='../image.php?id=15855' style='width:20px; cursor:pointer; position:absolute; top:207px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15855' onclick='fetchAssetData(15855);' class="asset-image" data-id="<?php echo $assetId15855; ?>" data-room="<?php echo htmlspecialchars($room15855); ?>" data-floor="<?php echo htmlspecialchars($floor15855); ?>" data-image="<?php echo base64_encode($upload_img15855); ?>" data-status="<?php echo htmlspecialchars($status15855); ?>" data-category="<?php echo htmlspecialchars($category15855); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15855); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15855); ?>; 
    position:absolute; top:207px; left:190px;'>
                        </div>

                        <!-- ASSET 15854 -->
                        <img src='../image.php?id=15854' style='width:20px; cursor:pointer; position:absolute; top:390px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15854' onclick='fetchAssetData(15854);' class="asset-image" data-id="<?php echo $assetId15854; ?>" data-room="<?php echo htmlspecialchars($room15854); ?>" data-floor="<?php echo htmlspecialchars($floor15854); ?>" data-image="<?php echo base64_encode($upload_img15854); ?>" data-status="<?php echo htmlspecialchars($status15854); ?>" data-category="<?php echo htmlspecialchars($category15854); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15854); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15854); ?>; 
    position:absolute; top:390px; left:185px;'>
                        </div>

                        <!-- ASSET 15826 -->
                        <img src='../image.php?id=15826' style='width:20px; cursor:pointer; position:absolute; top:390px; left:95px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15826' onclick='fetchAssetData(15826);' class="asset-image" data-id="<?php echo $assetId15826; ?>" data-room="<?php echo htmlspecialchars($room15826); ?>" data-floor="<?php echo htmlspecialchars($floor15826); ?>" data-image="<?php echo base64_encode($upload_img15826); ?>" data-status="<?php echo htmlspecialchars($status15826); ?>" data-category="<?php echo htmlspecialchars($category15826); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15826); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15826); ?>; 
    position:absolute; top:390px; left:105px;'>
                        </div>

                        <!-- ASSET 15827 -->
                        <img src='../image.php?id=15827' style='width:20px; cursor:pointer; position:absolute; top:490px; left:175px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15827' onclick='fetchAssetData(15827);' class="asset-image" data-id="<?php echo $assetId15827; ?>" data-room="<?php echo htmlspecialchars($room15827); ?>" data-floor="<?php echo htmlspecialchars($floor15827); ?>" data-image="<?php echo base64_encode($upload_img15827); ?>" data-status="<?php echo htmlspecialchars($status15827); ?>" data-category="<?php echo htmlspecialchars($category15827); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15827); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15827); ?>; 
    position:absolute; top:490px; left:185px;'>
                        </div>

                        <!-- ASSET 15817 -->
                        <img src='../image.php?id=15817' style='width:20px; cursor:pointer; position:absolute; top:490px; left:95px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15817' onclick='fetchAssetData(15817);' class="asset-image" data-id="<?php echo $assetId15817; ?>" data-room="<?php echo htmlspecialchars($room15817); ?>" data-floor="<?php echo htmlspecialchars($floor15817); ?>" data-image="<?php echo base64_encode($upload_img15817); ?>" data-status="<?php echo htmlspecialchars($status15817); ?>" data-category="<?php echo htmlspecialchars($category15817); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15817); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15817); ?>; 
    position:absolute; top:490px; left:105px;'>
                        </div>

                        <!-- ////// -->

                        <!-- ASSET 15822 -->
                        <img src='../image.php?id=15822' style='width:20px; cursor:pointer; position:absolute; top:390px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15822' onclick='fetchAssetData(15822);' class="asset-image" data-id="<?php echo $assetId15822; ?>" data-room="<?php echo htmlspecialchars($room15822); ?>" data-floor="<?php echo htmlspecialchars($floor15822); ?>" data-image="<?php echo base64_encode($upload_img15822); ?>" data-status="<?php echo htmlspecialchars($status15822); ?>" data-category="<?php echo htmlspecialchars($category15822); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15822); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15822); ?>; 
    position:absolute; top:390px; left:1050px;'>
                        </div>

                        <!-- ASSET 15823 -->
                        <img src='../image.php?id=15823' style='width:20px; cursor:pointer; position:absolute; top:390px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15823' onclick='fetchAssetData(15823);' class="asset-image" data-id="<?php echo $assetId15823; ?>" data-room="<?php echo htmlspecialchars($room15823); ?>" data-floor="<?php echo htmlspecialchars($floor15823); ?>" data-image="<?php echo base64_encode($upload_img15823); ?>" data-status="<?php echo htmlspecialchars($status15823); ?>" data-category="<?php echo htmlspecialchars($category15823); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15823); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15819); ?>; 
    position:absolute; top:390px; left:1130px;'>
                        </div>

                        <!-- ASSET 15824 -->
                        <img src='../image.php?id=15824' style='width:20px; cursor:pointer; position:absolute; top:490px; left:1040px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15824' onclick='fetchAssetData(15824);' class="asset-image" data-id="<?php echo $assetId15824; ?>" data-room="<?php echo htmlspecialchars($room15824); ?>" data-floor="<?php echo htmlspecialchars($floor15824); ?>" data-image="<?php echo base64_encode($upload_img15824); ?>" data-status="<?php echo htmlspecialchars($status15824); ?>" data-category="<?php echo htmlspecialchars($category15824); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15824); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15824); ?>; 
    position:absolute; top:490px; left:1050px;'>
                        </div>

                        <!-- ASSET 15206 -->
                        <img src='../image.php?id=15206' style='width:20px; cursor:pointer; position:absolute; top:490px; left:1120px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15206' onclick='fetchAssetData(15206);' class="asset-image" data-id="<?php echo $assetId15206; ?>" data-room="<?php echo htmlspecialchars($room15206); ?>" data-floor="<?php echo htmlspecialchars($floor15206); ?>" data-image="<?php echo base64_encode($upload_img15206); ?>" data-status="<?php echo htmlspecialchars($status15206); ?>" data-category="<?php echo htmlspecialchars($category15206); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15206); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15206); ?>; 
    position:absolute; top:490px; left:1130px;'>
                        </div>

                        <!-- ASSET 15823 -->
                        <img src='../image.php?id=15823' style='width:20px; cursor:pointer; position:absolute; top:252px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15823' onclick='fetchAssetData(15823);' class="asset-image" data-id="<?php echo $assetId15823; ?>" data-room="<?php echo htmlspecialchars($room15823); ?>" data-floor="<?php echo htmlspecialchars($floor15823); ?>" data-image="<?php echo base64_encode($upload_img15823); ?>" data-status="<?php echo htmlspecialchars($status15823); ?>" data-category="<?php echo htmlspecialchars($category15823); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15823); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15823); ?>; 
    position:absolute; top:252px; left:235px;'>
                        </div>

                        <!-- ASSET 15824 -->
                        <img src='../image.php?id=15824' style='width:20px; cursor:pointer; position:absolute; top:320px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15824' onclick='fetchAssetData(15824);' class="asset-image" data-id="<?php echo $assetId15824; ?>" data-room="<?php echo htmlspecialchars($room15824); ?>" data-floor="<?php echo htmlspecialchars($floor15824); ?>" data-status="<?php echo htmlspecialchars($status15824); ?>" data-image="<?php echo base64_encode($upload_img15824); ?>" data-category="<?php echo htmlspecialchars($category15824); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15824); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15824); ?>; 
    position:absolute; top:320px; left:235px;'>
                        </div>

                        <!-- ASSET 15825 -->
                        <img src='../image.php?id=15825' style='width:20px; cursor:pointer; position:absolute; top:320px; left:305px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15825' onclick='fetchAssetData(15825);' class="asset-image" data-id="<?php echo $assetId15825; ?>" data-room="<?php echo htmlspecialchars($room15825); ?>" data-floor="<?php echo htmlspecialchars($floor15825); ?>" data-image="<?php echo base64_encode($upload_img15825); ?>" data-category="<?php echo htmlspecialchars($category15825); ?>" data-status="<?php echo htmlspecialchars($status15825); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15825); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15825); ?>; 
    position:absolute; top:320px; left:320px;'>
                        </div>

                        <!-- ASSET 15831 -->
                        <img src='../image.php?id=15831' style='width:20px; cursor:pointer; position:absolute; top:252px; left:305px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15831' onclick='fetchAssetData(15831);' class="asset-image" data-id="<?php echo $assetId15831; ?>" data-room="<?php echo htmlspecialchars($room15831); ?>" data-floor="<?php echo htmlspecialchars($floor15831); ?>" data-image="<?php echo base64_encode($upload_img15831); ?>" data-status="<?php echo htmlspecialchars($status15831); ?>" data-category="<?php echo htmlspecialchars($category15831); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15831); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15831); ?>; 
    position:absolute; top:252px; left:320px;'>
                        </div>

                        <!-- ASSET 15828 -->
                        <img src='../image.php?id=15828' style='width:20px; cursor:pointer; position:absolute; top:252px; left:380px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15828' onclick='fetchAssetData(15828);' class="asset-image" data-id="<?php echo $assetId15828; ?>" data-room="<?php echo htmlspecialchars($room15828); ?>" data-floor="<?php echo htmlspecialchars($floor15828); ?>" data-image="<?php echo base64_encode($upload_img15828); ?>" data-status="<?php echo htmlspecialchars($status15828); ?>" data-category="<?php echo htmlspecialchars($category15828); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15828); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15828); ?>; 
    position:absolute; top:252px; left:395px;'>
                        </div>

                        <!-- ASSET 15829 -->
                        <img src='../image.php?id=15829' style='width:20px; cursor:pointer; position:absolute; top:320px; left:380px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15829' onclick='fetchAssetData(15829);' class="asset-image" data-id="<?php echo $assetId15829; ?>" data-room="<?php echo htmlspecialchars($room15829); ?>" data-floor="<?php echo htmlspecialchars($floor15829); ?>" data-image="<?php echo base64_encode($upload_img15829); ?>" data-category="<?php echo htmlspecialchars($category15829); ?>" data-status="<?php echo htmlspecialchars($status15829); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15829); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15829); ?>; 
    position:absolute; top:320px; left:395px;'>
                        </div>

                        <!-- ASSET 15830 -->
                        <img src='../image.php?id=15830' style='width:20px; cursor:pointer; position:absolute; top:320px; left:480px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15830' onclick='fetchAssetData(15830);' class="asset-image" data-id="<?php echo $assetId15830; ?>" data-room="<?php echo htmlspecialchars($room15830); ?>" data-floor="<?php echo htmlspecialchars($floor15830); ?>" data-image="<?php echo base64_encode($upload_img15830); ?>" data-category="<?php echo htmlspecialchars($category15830); ?>" data-status="<?php echo htmlspecialchars($status15830); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15830); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15830); ?>; 
    position:absolute; top:320px; left:495px;'>
                        </div>

                        <!-- ASSET 15832 -->
                        <img src='../image.php?id=15832' style='width:20px; cursor:pointer; position:absolute; top:252px; left:480px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15832' onclick='fetchAssetData(15832);' class="asset-image" data-id="<?php echo $assetId15832; ?>" data-room="<?php echo htmlspecialchars($room15832); ?>" data-floor="<?php echo htmlspecialchars($floor15832); ?>" data-image="<?php echo base64_encode($upload_img15832); ?>" data-category="<?php echo htmlspecialchars($category15832); ?>" data-status="<?php echo htmlspecialchars($status15832); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15832); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15832); ?>; 
    position:absolute; top:252px; left:495px;'>
                        </div>

                        <!-- ASSET 15833 -->
                        <img src='../image.php?id=15833' style='width:20px; cursor:pointer; position:absolute; top:252px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15833' onclick='fetchAssetData(15833);' class="asset-image" data-id="<?php echo $assetId15833; ?>" data-room="<?php echo htmlspecialchars($room15833); ?>" data-floor="<?php echo htmlspecialchars($floor15833); ?>" data-image="<?php echo base64_encode($upload_img15833); ?>" data-category="<?php echo htmlspecialchars($category15833); ?>" data-status="<?php echo htmlspecialchars($status15833); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15833); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15833); ?>; 
    position:absolute; top:252px; left:575px;'>
                        </div>

                        <!-- ASSET 15834 -->
                        <img src='../image.php?id=15834' style='width:20px; cursor:pointer; position:absolute; top:320px; left:560px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15834' onclick='fetchAssetData(15834);' class="asset-image" data-id="<?php echo $assetId15834; ?>" data-room="<?php echo htmlspecialchars($room15834); ?>" data-floor="<?php echo htmlspecialchars($floor15834); ?>" data-image="<?php echo base64_encode($upload_img15834); ?>" data-category="<?php echo htmlspecialchars($category15834); ?>" data-status="<?php echo htmlspecialchars($status15834); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15834); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15834); ?>; 
    position:absolute; top:320px; left:575px;'>
                        </div>

                        <!-- ASSET 15826 -->
                        <img src='../image.php?id=15826' style='width:20px; cursor:pointer; position:absolute; top:320px; left:640px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15826' onclick='fetchAssetData(15826);' class="asset-image" data-id="<?php echo $assetId15826; ?>" data-room="<?php echo htmlspecialchars($room15826); ?>" data-floor="<?php echo htmlspecialchars($floor15826); ?>" data-image="<?php echo base64_encode($upload_img15826); ?>" data-status="<?php echo htmlspecialchars($status15826); ?>" data-category="<?php echo htmlspecialchars($category15826); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15826); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15826); ?>; 
    position:absolute; top:320px; left:655px;'>
                        </div>

                        <!-- ASSET 15827 -->
                        <img src='../image.php?id=15827' style='width:20px; cursor:pointer; position:absolute; top:252px; left:640px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15827' onclick='fetchAssetData(15827);' class="asset-image" data-id="<?php echo $assetId15827; ?>" data-room="<?php echo htmlspecialchars($room15827); ?>" data-floor="<?php echo htmlspecialchars($floor15827); ?>" data-image="<?php echo base64_encode($upload_img15827); ?>" data-category="<?php echo htmlspecialchars($category15827); ?>" data-status="<?php echo htmlspecialchars($status15827); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15827); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15827); ?>; 
    position:absolute; top:252px; left:655px;'>
                        </div>

                        <!-- ASSET 15831 -->
                        <img src='../image.php?id=15831' style='width:20px; cursor:pointer; position:absolute; top:252px; left:725px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15831' onclick='fetchAssetData(15831);' class="asset-image" data-id="<?php echo $assetId15831; ?>" data-room="<?php echo htmlspecialchars($room15831); ?>" data-floor="<?php echo htmlspecialchars($floor15831); ?>" data-image="<?php echo base64_encode($upload_img15831); ?>" data-status="<?php echo htmlspecialchars($status15831); ?>" data-category="<?php echo htmlspecialchars($category15831); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15831); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15831); ?>; 
    position:absolute; top:252px; left:740px;'>
                        </div>

                        <!-- ASSET 15833 -->
                        <img src='../image.php?id=15833' style='width:20px; cursor:pointer; position:absolute; top:320px; left:725px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15833' onclick='fetchAssetData(15833);' class="asset-image" data-id="<?php echo $assetId15833; ?>" data-room="<?php echo htmlspecialchars($room15833); ?>" data-floor="<?php echo htmlspecialchars($floor15833); ?>" data-image="<?php echo base64_encode($upload_img15833); ?>" data-status="<?php echo htmlspecialchars($status15833); ?>" data-category="<?php echo htmlspecialchars($category15833); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15833); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15833); ?>; 
    position:absolute; top:320px; left:740px;'>
                        </div>

                        <!-- ASSET 15834 -->
                        <img src='../image.php?id=15834' style='width:20px; cursor:pointer; position:absolute; top:320px; left:815px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15834' onclick='fetchAssetData(15834);' class="asset-image" data-id="<?php echo $assetId15834; ?>" data-room="<?php echo htmlspecialchars($room15834); ?>" data-floor="<?php echo htmlspecialchars($floor15834); ?>" data-image="<?php echo base64_encode($upload_img15834); ?>" data-status="<?php echo htmlspecialchars($status15834); ?>" data-category="<?php echo htmlspecialchars($category15834); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15834); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15834); ?>; 
    position:absolute; top:320px; left:830px;'>
                        </div>

                        <!-- ASSET 15835 -->
                        <img src='../image.php?id=15835' style='width:20px; cursor:pointer; position:absolute; top:252px; left:815px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15835' onclick='fetchAssetData(15835);' class="asset-image" data-id="<?php echo $assetId15835; ?>" data-room="<?php echo htmlspecialchars($room15835); ?>" data-floor="<?php echo htmlspecialchars($floor15835); ?>" data-image="<?php echo base64_encode($upload_img15835); ?>" data-category="<?php echo htmlspecialchars($category15835); ?>" data-status="<?php echo htmlspecialchars($status15835); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15835); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15835); ?>; 
    position:absolute; top:252px; left:830px;'>
                        </div>

                        <!-- ASSET 15836 -->
                        <img src='../image.php?id=15836' style='width:20px; cursor:pointer; position:absolute; top:252px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15836' onclick='fetchAssetData(15836);' class="asset-image" data-id="<?php echo $assetId15836; ?>" data-room="<?php echo htmlspecialchars($room15836); ?>" data-floor="<?php echo htmlspecialchars($floor15836); ?>" data-image="<?php echo base64_encode($upload_img15836); ?>" data-category="<?php echo htmlspecialchars($category15836); ?>" data-status="<?php echo htmlspecialchars($status15836); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15836); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15836); ?>; 
    position:absolute; top:252px; left:905px;'>
                        </div>

                        <!-- ASSET 15837 -->
                        <img src='../image.php?id=15837' style='width:20px; cursor:pointer; position:absolute; top:320px; left:890px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15837' onclick='fetchAssetData(15837);' class="asset-image" data-id="<?php echo $assetId15837; ?>" data-room="<?php echo htmlspecialchars($room15837); ?>" data-floor="<?php echo htmlspecialchars($floor15837); ?>" data-image="<?php echo base64_encode($upload_img15837); ?>" data-category="<?php echo htmlspecialchars($category15837); ?>" data-status="<?php echo htmlspecialchars($status15837); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15837); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15837); ?>; 
    position:absolute; top:320px; left:905px;'>
                        </div>

                        <!-- ASSET 15838 -->
                        <img src='../image.php?id=15838' style='width:20px; cursor:pointer; position:absolute; top:320px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15838' onclick='fetchAssetData(15838);' class="asset-image" data-id="<?php echo $assetId15838; ?>" data-room="<?php echo htmlspecialchars($room15838); ?>" data-floor="<?php echo htmlspecialchars($floor15838); ?>" data-image="<?php echo base64_encode($upload_img15838); ?>" data-category="<?php echo htmlspecialchars($category15838); ?>" data-status="<?php echo htmlspecialchars($status15838); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15838); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15838); ?>; 
    position:absolute; top:320px; left:985px;'>
                        </div>

                        <!-- ASSET 15839 -->
                        <img src='../image.php?id=15839' style='width:20px; cursor:pointer; position:absolute; top:252px; left:970px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15839' onclick='fetchAssetData(15839);' class="asset-image" data-id="<?php echo $assetId15839; ?>" data-room="<?php echo htmlspecialchars($room15839); ?>" data-floor="<?php echo htmlspecialchars($floor15839); ?>" data-image="<?php echo base64_encode($upload_img15839); ?>" data-category="<?php echo htmlspecialchars($category15839); ?>" data-status="<?php echo htmlspecialchars($status15839); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15839); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15839); ?>; 
    position:absolute; top:252px; left:985px;'>
                        </div>

                        <!-- ASSET 15840 -->
                        <img src='../image.php?id=15840' style='width:20px; cursor:pointer; position:absolute; top:252px; left:1050px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15840' onclick='fetchAssetData(15840);' class="asset-image" data-id="<?php echo $assetId15840; ?>" data-room="<?php echo htmlspecialchars($room15840); ?>" data-floor="<?php echo htmlspecialchars($floor15840); ?>" data-image="<?php echo base64_encode($upload_img15840); ?>" data-category="<?php echo htmlspecialchars($category15840); ?>" data-status="<?php echo htmlspecialchars($status15840); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15840); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15840); ?>; 
    position:absolute; top:252px; left:1065px;'>
                        </div>

                        <!-- ASSET 15845 -->
                        <img src='../image.php?id=15845' style='width:20px; cursor:pointer; position:absolute; top:320px; left:1050px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15845' onclick='fetchAssetData(15845);' class="asset-image" data-id="<?php echo $assetId15845; ?>" data-room="<?php echo htmlspecialchars($room15845); ?>" data-floor="<?php echo htmlspecialchars($floor15845); ?>" data-image="<?php echo base64_encode($upload_img15845); ?>" data-category="<?php echo htmlspecialchars($category15845); ?>" data-status="<?php echo htmlspecialchars($status15845); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15845); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15845); ?>; 
    position:absolute; top:320px; left:1065px;'>
                        </div>



                        <!-- End of Hallway -->

                        <!-- Start IC101a -->

                        <!-- ASSET 15856 -->
                        <img src='../image.php?id=15856' style='width:15px; cursor:pointer; position:absolute; top:70px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15856' onclick='fetchAssetData(15856);' class="asset-image" data-id="<?php echo $assetId15856; ?>" data-room="<?php echo htmlspecialchars($room15856); ?>" data-floor="<?php echo htmlspecialchars($floor15856); ?>" data-image="<?php echo base64_encode($upload_img15856); ?>" data-category="<?php echo htmlspecialchars($category15856); ?>" data-status="<?php echo htmlspecialchars($status15856); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15856); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15856); ?>; 
    position:absolute; top:70px; left:230px;'>
                        </div>

                        <!-- ASSET 15857 -->
                        <img src='../image.php?id=15857' style='width:15px; cursor:pointer; position:absolute; top:145px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15857' onclick='fetchAssetData(15857);' class="asset-image" data-id="<?php echo $assetId15857; ?>" data-room="<?php echo htmlspecialchars($room15857); ?>" data-floor="<?php echo htmlspecialchars($floor15857); ?>" data-image="<?php echo base64_encode($upload_img15857); ?>" data-status="<?php echo htmlspecialchars($status15857); ?>" data-category="<?php echo htmlspecialchars($category15857); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15857); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15857); ?>; 
    position:absolute; top:145px; left:230px;'>
                        </div>

                        <!-- ASSET 15858 -->
                        <img src='../image.php?id=15858' style='width:15px; cursor:pointer; position:absolute; top:70px; left:347px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15858' onclick='fetchAssetData(15858);' class="asset-image" data-id="<?php echo $assetId15858; ?>" data-room="<?php echo htmlspecialchars($room15858); ?>" data-floor="<?php echo htmlspecialchars($floor15858); ?>" data-image="<?php echo base64_encode($upload_img15858); ?>" data-status="<?php echo htmlspecialchars($status15858); ?>" data-category="<?php echo htmlspecialchars($category15858); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15858); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15858); ?>; 
    position:absolute; top:70px; left:357px;'>
                        </div>

                        <!-- ASSET 15859 -->
                        <img src='../image.php?id=15859' style='width:15px; cursor:pointer; position:absolute; top:145px; left:347px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15859' onclick='fetchAssetData(15859);' class="asset-image" data-id="<?php echo $assetId15859; ?>" data-room="<?php echo htmlspecialchars($room15859); ?>" data-floor="<?php echo htmlspecialchars($floor15859); ?>" data-image="<?php echo base64_encode($upload_img15859); ?>" data-category="<?php echo htmlspecialchars($category15859); ?>" data-status="<?php echo htmlspecialchars($status15859); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15859); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15859); ?>; 
    position:absolute; top:145px; left:357px;'>
                        </div>


                        <!-- ASSET 15860 -->
                        <img src='../image.php?id=15860' style='width:15px; cursor:pointer; position:absolute; top:145px; left:460px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15860' onclick='fetchAssetData(15860);' class="asset-image" data-id="<?php echo $assetId15860; ?>" data-room="<?php echo htmlspecialchars($room15860); ?>" data-floor="<?php echo htmlspecialchars($floor15860); ?>" data-image="<?php echo base64_encode($upload_img15860); ?>" data-status="<?php echo htmlspecialchars($status15860); ?>" data-category="<?php echo htmlspecialchars($category15860); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15860); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15860); ?>; 
    position:absolute; top:145px; left:470px;'>
                        </div>

                        <!-- ASSET 15861 -->
                        <img src='../image.php?id=15861' style='width:15px; cursor:pointer; position:absolute; top:70px; left:460px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15861' onclick='fetchAssetData(15861);' class="asset-image" data-id="<?php echo $assetId15861; ?>" data-room="<?php echo htmlspecialchars($room15861); ?>" data-floor="<?php echo htmlspecialchars($floor15861); ?>" data-image="<?php echo base64_encode($upload_img15861); ?>" data-status="<?php echo htmlspecialchars($status15861); ?>" data-category="<?php echo htmlspecialchars($category15861); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15861); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15861); ?>; 
    position:absolute; top:70px; left:470px;'>
                        </div>

                        <!-- ASSET 15350 -->
                        <img src='../image.php?id=15350' style='width:15px; cursor:pointer; position:absolute; top:143px; left:430px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15350' onclick='fetchAssetData(15350);' class="asset-image" data-id="<?php echo $assetId15350; ?>" data-room="<?php echo htmlspecialchars($room15350); ?>" data-floor="<?php echo htmlspecialchars($floor15350); ?>" data-image="<?php echo base64_encode($upload_img15350); ?>" data-status="<?php echo htmlspecialchars($status15350); ?>" data-category="<?php echo htmlspecialchars($category15350); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15350); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15350); ?>; 
    position:absolute; top:135px; left:433.5px;'>
                        </div>

                        <!-- ASSET 15300 -->
                        <img src='../image.php?id=15300' style='width:18px; cursor:pointer; position:absolute; top:144px; left:446px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15300' onclick='fetchAssetData(15300);' class="asset-image" data-id="<?php echo $assetId15300; ?>" data-room="<?php echo htmlspecialchars($room15300); ?>" data-floor="<?php echo htmlspecialchars($floor15300); ?>" data-image="<?php echo base64_encode($upload_img15300); ?>" data-category="<?php echo htmlspecialchars($category15300); ?>" data-status="<?php echo htmlspecialchars($status15300); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15300); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15300); ?>; 
    position:absolute; top:142px; left:452px;'>
                        </div>


                        <!-- ASSET 15862 -->
                        <img src='../image.php?id=15862' style='width:15px; cursor:pointer; position:absolute; top:225px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15862' onclick='fetchAssetData(15862);' class="asset-image" data-id="<?php echo $assetId15862; ?>" data-room="<?php echo htmlspecialchars($room15862); ?>" data-floor="<?php echo htmlspecialchars($floor15862); ?>" data-image="<?php echo base64_encode($upload_img15862); ?>" data-category="<?php echo htmlspecialchars($category15862); ?>" data-status="<?php echo htmlspecialchars($status15862); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15862); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15862); ?>; 
    position:absolute; top:225px; left:230px;'>
                        </div>

                        <!-- ASSET 15863 -->
                        <img src='../image.php?id=15863' style='width:15px; cursor:pointer; position:absolute; top:225px; left:347px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15863' onclick='fetchAssetData(15863);' class="asset-image" data-id="<?php echo $assetId15863; ?>" data-room="<?php echo htmlspecialchars($room15863); ?>" data-floor="<?php echo htmlspecialchars($floor15863); ?>" data-image="<?php echo base64_encode($upload_img15863); ?>" data-status="<?php echo htmlspecialchars($status15863); ?>" data-category="<?php echo htmlspecialchars($category15863); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15863); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15863); ?>; 
    position:absolute; top:225px; left:357px;'>
                        </div>

                        <!-- ASSET 15864 -->
                        <img src='../image.php?id=15864' style='width:15px; cursor:pointer; position:absolute; top:225px; left:460px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15864' onclick='fetchAssetData(15864);' class="asset-image" data-id="<?php echo $assetId15864; ?>" data-room="<?php echo htmlspecialchars($room15864); ?>" data-floor="<?php echo htmlspecialchars($floor15864); ?>" data-image="<?php echo base64_encode($upload_img15864); ?>" data-status="<?php echo htmlspecialchars($status15864); ?>" data-category="<?php echo htmlspecialchars($category15864); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15864); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15864); ?>; 
    position:absolute; top:225px; left:470px;'>
                        </div>

                        <!-- ASSET 15865 -->
                        <img src='../image.php?id=15865' style='width:18px; cursor:pointer; position:absolute; top:76px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15865' onclick='fetchAssetData(15865);' class="asset-image" data-id="<?php echo $assetId15865; ?>" data-room="<?php echo htmlspecialchars($room15865); ?>" data-floor="<?php echo htmlspecialchars($floor15865); ?>" data-status="<?php echo htmlspecialchars($status15865); ?>" data-image="<?php echo base64_encode($upload_img15865); ?>" data-category="<?php echo htmlspecialchars($category15865); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15865); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15865); ?>; 
    position:absolute; top:79px; left:246px;'>
                        </div>

                        <!-- ASSET 15866 -->
                        <img src='../image.php?id=15866' style='width:18px; cursor:pointer; position:absolute; top:89px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15866' onclick='fetchAssetData(15866);' class="asset-image" data-id="<?php echo $assetId15866; ?>" data-room="<?php echo htmlspecialchars($room15866); ?>" data-floor="<?php echo htmlspecialchars($floor15866); ?>" data-image="<?php echo base64_encode($upload_img15866); ?>" data-status="<?php echo htmlspecialchars($status15866); ?>" data-category="<?php echo htmlspecialchars($category15866); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15866); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15866); ?>; 
    position:absolute; top:91px; left:246px;'>
                        </div>

                        <!-- ASSET 15867 -->
                        <img src='../image.php?id=15867' style='width:18px; cursor:pointer; position:absolute; top:102px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15867' onclick='fetchAssetData(15867);' class="asset-image" data-id="<?php echo $assetId15867; ?>" data-room="<?php echo htmlspecialchars($room15867); ?>" data-floor="<?php echo htmlspecialchars($floor15867); ?>" data-image="<?php echo base64_encode($upload_img15867); ?>" data-status="<?php echo htmlspecialchars($status15867); ?>" data-category="<?php echo htmlspecialchars($category15867); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15867); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15867); ?>; 
    position:absolute; top:105px; left:246px;'>
                        </div>

                        <!-- ASSET 15868 -->
                        <img src='../image.php?id=15868' style='width:18px; cursor:pointer; position:absolute; top:115px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15868' onclick='fetchAssetData(15868);' class="asset-image" data-id="<?php echo $assetId15868; ?>" data-room="<?php echo htmlspecialchars($room15868); ?>" data-floor="<?php echo htmlspecialchars($floor15868); ?>" data-image="<?php echo base64_encode($upload_img15868); ?>" data-status="<?php echo htmlspecialchars($status15868); ?>" data-category="<?php echo htmlspecialchars($category15868); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15868); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15868); ?>; 
    position:absolute; top:118px; left:246px;'>
                        </div>

                        <!-- ASSET 15869 -->
                        <img src='../image.php?id=15869' style='width:18px; cursor:pointer; position:absolute; top:129px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15869' onclick='fetchAssetData(15869);' class="asset-image" data-id="<?php echo $assetId15869; ?>" data-room="<?php echo htmlspecialchars($room15869); ?>" data-floor="<?php echo htmlspecialchars($floor15869); ?>" data-image="<?php echo base64_encode($upload_img15869); ?>" data-status="<?php echo htmlspecialchars($status15869); ?>" data-category="<?php echo htmlspecialchars($category15869); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15869); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15869); ?>; 
    position:absolute; top:132px; left:246px;'>
                        </div>


                        <!-- ASSET 15870 -->
                        <img src='../image.php?id=15870' style='width:18px; cursor:pointer; position:absolute; top:75px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15870' onclick='fetchAssetData(15870);' class="asset-image" data-id="<?php echo $assetId15870; ?>" data-room="<?php echo htmlspecialchars($room15870); ?>" data-floor="<?php echo htmlspecialchars($floor15870); ?>" data-image="<?php echo base64_encode($upload_img15870); ?>" data-status="<?php echo htmlspecialchars($status15870); ?>" data-category="<?php echo htmlspecialchars($category15870); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15870); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15870); ?>; 
    position:absolute; top:78px; left:270px;'>
                        </div>

                        <!-- ASSET 15871 -->
                        <img src='../image.php?id=15871' style='width:18px; cursor:pointer; position:absolute; top:87px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15871' onclick='fetchAssetData(15871);' class="asset-image" data-id="<?php echo $assetId15871; ?>" data-room="<?php echo htmlspecialchars($room15871); ?>" data-floor="<?php echo htmlspecialchars($floor15871); ?>" data-image="<?php echo base64_encode($upload_img15871); ?>" data-status="<?php echo htmlspecialchars($status15871); ?>" data-category="<?php echo htmlspecialchars($category15871); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15871); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15871); ?>; 
    position:absolute; top:91px; left:270px;'>
                        </div>

                        <!-- ASSET 15872 -->
                        <img src='../image.php?id=15872' style='width:18px; cursor:pointer; position:absolute; top:100px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15872' onclick='fetchAssetData(15872);' class="asset-image" data-id="<?php echo $assetId15872; ?>" data-room="<?php echo htmlspecialchars($room15872); ?>" data-floor="<?php echo htmlspecialchars($floor15872); ?>" data-image="<?php echo base64_encode($upload_img15872); ?>" data-status="<?php echo htmlspecialchars($status15872); ?>" data-category="<?php echo htmlspecialchars($category15872); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15872); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15872); ?>; 
    position:absolute; top:104px; left:270px;'>
                        </div>

                        <!-- ASSET 15873 -->
                        <img src='../image.php?id=15873' style='width:18px; cursor:pointer; position:absolute; top:113px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15873' onclick='fetchAssetData(15873);' class="asset-image" data-id="<?php echo $assetId15873; ?>" data-room="<?php echo htmlspecialchars($room15873); ?>" data-floor="<?php echo htmlspecialchars($floor15873); ?>" data-status="<?php echo htmlspecialchars($status15873); ?>" data-image="<?php echo base64_encode($upload_img15873); ?>" data-category="<?php echo htmlspecialchars($category15873); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15873); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15873); ?>; 
    position:absolute; top:117px; left:270px;'>
                        </div>


                        <!-- ASSET 15874 -->
                        <img src='../image.php?id=15874' style='width:18px; cursor:pointer; position:absolute; top:126px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15874' onclick='fetchAssetData(15874);' class="asset-image" data-id="<?php echo $assetId15874; ?>" data-room="<?php echo htmlspecialchars($room15874); ?>" data-status="<?php echo htmlspecialchars($status15874); ?>" data-floor="<?php echo htmlspecialchars($floor15874); ?>" data-image="<?php echo base64_encode($upload_img15874); ?>" data-category="<?php echo htmlspecialchars($category15874); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15874); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15874); ?>; 
    position:absolute; top:130px; left:270px;'>
                        </div>

                        <!-- ASSET 15875 -->
                        <img src='../image.php?id=15875' style='width:18px; cursor:pointer; position:absolute; top:74px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15875' onclick='fetchAssetData(15875);' class="asset-image" data-id="<?php echo $assetId15875; ?>" data-room="<?php echo htmlspecialchars($room15875); ?>" data-floor="<?php echo htmlspecialchars($floor15875); ?>" data-image="<?php echo base64_encode($upload_img15875); ?>" data-status="<?php echo htmlspecialchars($status15875); ?>" data-category="<?php echo htmlspecialchars($category15875); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15875); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15875); ?>; 
    position:absolute; top:79px; left:293px;'>
                        </div>

                        <!-- ASSET 15876 -->
                        <img src='../image.php?id=15876' style='width:18px; cursor:pointer; position:absolute; top:87px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15876' onclick='fetchAssetData(15876);' class="asset-image" data-id="<?php echo $assetId15876; ?>" data-room="<?php echo htmlspecialchars($room15876); ?>" data-floor="<?php echo htmlspecialchars($floor15876); ?>" data-image="<?php echo base64_encode($upload_img15876); ?>" data-status="<?php echo htmlspecialchars($status15876); ?>" data-category="<?php echo htmlspecialchars($category15876); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15876); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15876); ?>; 
    position:absolute; top:91px; left:293px;'>
                        </div>

                        <!-- ASSET 15877 -->
                        <img src='../image.php?id=15877' style='width:18px; cursor:pointer; position:absolute; top:100px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15877' onclick='fetchAssetData(15877);' class="asset-image" data-id="<?php echo $assetId15877; ?>" data-room="<?php echo htmlspecialchars($room15877); ?>" data-floor="<?php echo htmlspecialchars($floor15877); ?>" data-status="<?php echo htmlspecialchars($status15877); ?>" data-image="<?php echo base64_encode($upload_img15877); ?>" data-category="<?php echo htmlspecialchars($category15877); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15877); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15877); ?>; 
    position:absolute; top:104px; left:293px;'>
                        </div>

                        <!-- ASSET 15878 -->
                        <img src='../image.php?id=15878' style='width:18px; cursor:pointer; position:absolute; top:113px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15878' onclick='fetchAssetData(15878);' class="asset-image" data-id="<?php echo $assetId15878; ?>" data-room="<?php echo htmlspecialchars($room15878); ?>" data-floor="<?php echo htmlspecialchars($floor15878); ?>" data-image="<?php echo base64_encode($upload_img15878); ?>" data-status="<?php echo htmlspecialchars($status15878); ?>" data-category="<?php echo htmlspecialchars($category15878); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15878); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15878); ?>; 
    position:absolute; top:117px; left:293px;'>
                        </div>

                        <!-- ASSET 15879 -->
                        <img src='../image.php?id=15879' style='width:18px; cursor:pointer; position:absolute; top:126px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15879' onclick='fetchAssetData(15879);' class="asset-image" data-id="<?php echo $assetId15879; ?>" data-room="<?php echo htmlspecialchars($room15879); ?>" data-floor="<?php echo htmlspecialchars($floor15879); ?>" data-image="<?php echo base64_encode($upload_img15879); ?>" data-status="<?php echo htmlspecialchars($status15879); ?>" data-category="<?php echo htmlspecialchars($category15879); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15879); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15879); ?>; 
    position:absolute; top:130px; left:293px;'>
                        </div>

                        <!-- ASSET 15880 -->
                        <img src='../image.php?id=15880' style='width:18px; cursor:pointer; position:absolute; top:74px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15880' onclick='fetchAssetData(15880);' class="asset-image" data-id="<?php echo $assetId15880; ?>" data-room="<?php echo htmlspecialchars($room15880); ?>" data-floor="<?php echo htmlspecialchars($floor15880); ?>" data-status="<?php echo htmlspecialchars($status15880); ?>" data-image="<?php echo base64_encode($upload_img15880); ?>" data-category="<?php echo htmlspecialchars($category15880); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15880); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15880); ?>; 
    position:absolute; top:79px; left:315px;'>
                        </div>

                        <!-- ASSET 15881 -->
                        <img src='../image.php?id=15881' style='width:18px; cursor:pointer; position:absolute; top:87px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15881' onclick='fetchAssetData(15881);' class="asset-image" data-id="<?php echo $assetId15881; ?>" data-room="<?php echo htmlspecialchars($room15881); ?>" data-floor="<?php echo htmlspecialchars($floor15881); ?>" data-image="<?php echo base64_encode($upload_img15881); ?>" data-status="<?php echo htmlspecialchars($status15881); ?>" data-category="<?php echo htmlspecialchars($category15881); ?>">
                        data-assignedname="<?php echo htmlspecialchars($assignedName15881); ?>"
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15881); ?>; 
    position:absolute; top:91px; left:315px;'>
                        </div>

                        <!-- ASSET 15882 -->
                        <img src='../image.php?id=15882' style='width:18px; cursor:pointer; position:absolute; top:100px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15882' onclick='fetchAssetData(15882);' class="asset-image" data-id="<?php echo $assetId15882; ?>" data-room="<?php echo htmlspecialchars($room15882); ?>" data-floor="<?php echo htmlspecialchars($floor15882); ?>" data-status="<?php echo htmlspecialchars($status15882); ?>" data-image="<?php echo base64_encode($upload_img15882); ?>" data-category="<?php echo htmlspecialchars($category15882); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15882); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15882); ?>; 
    position:absolute; top:104px; left:315px;'>
                        </div>

                        <!-- ASSET 15883 -->
                        <img src='../image.php?id=15883' style='width:18px; cursor:pointer; position:absolute; top:113px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15883' onclick='fetchAssetData(15883);' class="asset-image" data-id="<?php echo $assetId15883; ?>" data-room="<?php echo htmlspecialchars($room15883); ?>" data-floor="<?php echo htmlspecialchars($floor15883); ?>" data-image="<?php echo base64_encode($upload_img15883); ?>" data-status="<?php echo htmlspecialchars($status15883); ?>" data-category="<?php echo htmlspecialchars($category15883); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15883); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15883); ?>; 
    position:absolute; top:117px; left:315px;'>
                        </div>

                        <!-- ASSET 15884 -->
                        <img src='../image.php?id=15884' style='width:18px; cursor:pointer; position:absolute; top:126px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15884' onclick='fetchAssetData(15884);' class="asset-image" data-id="<?php echo $assetId15884; ?>" data-room="<?php echo htmlspecialchars($room15884); ?>" data-floor="<?php echo htmlspecialchars($floor15884); ?>" data-status="<?php echo htmlspecialchars($status15884); ?>" data-image="<?php echo base64_encode($upload_img15884); ?>" data-category="<?php echo htmlspecialchars($category15884); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15884); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15884); ?>; 
    position:absolute; top:130px; left:315px;'>
                        </div>

                        <!-- ASSET 15885 -->
                        <img src='../image.php?id=15885' style='width:18px; cursor:pointer; position:absolute; top:74px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15885' onclick='fetchAssetData(15885);' class="asset-image" data-id="<?php echo $assetId15885; ?>" data-room="<?php echo htmlspecialchars($room15885); ?>" data-floor="<?php echo htmlspecialchars($floor15885); ?>" data-image="<?php echo base64_encode($upload_img15885); ?>" data-status="<?php echo htmlspecialchars($status15885); ?>" data-category="<?php echo htmlspecialchars($category15885); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15885); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15885); ?>; 
    position:absolute; top:79px; left:338px;'>
                        </div>


                        <!-- ASSET 15886 -->
                        <img src='../image.php?id=15886' style='width:18px; cursor:pointer; position:absolute; top:87px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15886' onclick='fetchAssetData(15886);' class="asset-image" data-id="<?php echo $assetId15886; ?>" data-room="<?php echo htmlspecialchars($room15886); ?>" data-floor="<?php echo htmlspecialchars($floor15886); ?>" data-image="<?php echo base64_encode($upload_img15886); ?>" data-status="<?php echo htmlspecialchars($status15886); ?>" data-category="<?php echo htmlspecialchars($category15886); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15886); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15886); ?>; 
    position:absolute; top:91px; left:338px;'>
                        </div>

                        <!-- ASSET 15887 -->
                        <img src='../image.php?id=15887' style='width:18px; cursor:pointer; position:absolute; top:100px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15887' onclick='fetchAssetData(15887);' class="asset-image" data-id="<?php echo $assetId15887; ?>" data-room="<?php echo htmlspecialchars($room15887); ?>" data-floor="<?php echo htmlspecialchars($floor15887); ?>" data-image="<?php echo base64_encode($upload_img15887); ?>" data-status="<?php echo htmlspecialchars($status15887); ?>" data-category="<?php echo htmlspecialchars($category15887); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15887); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15887); ?>; 
    position:absolute; top:104px; left:338px;'>
                        </div>

                        <!-- ASSET 15888 -->
                        <img src='../image.php?id=15888' style='width:18px; cursor:pointer; position:absolute; top:113px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15888' onclick='fetchAssetData(15888);' class="asset-image" data-id="<?php echo $assetId15888; ?>" data-room="<?php echo htmlspecialchars($room15888); ?>" data-floor="<?php echo htmlspecialchars($floor15888); ?>" data-image="<?php echo base64_encode($upload_img15888); ?>" data-status="<?php echo htmlspecialchars($status15888); ?>" data-category="<?php echo htmlspecialchars($category15888); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15888); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15888); ?>; 
    position:absolute; top:117px; left:338px;'>
                        </div>

                        <!-- ASSET 15889 -->
                        <img src='../image.php?id=15889' style='width:18px; cursor:pointer; position:absolute; top:126px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15889' onclick='fetchAssetData(15889);' class="asset-image" data-id="<?php echo $assetId15889; ?>" data-room="<?php echo htmlspecialchars($room15889); ?>" data-floor="<?php echo htmlspecialchars($floor15889); ?>" data-image="<?php echo base64_encode($upload_img15889); ?>" data-status="<?php echo htmlspecialchars($status15889); ?>" data-category="<?php echo htmlspecialchars($category15889); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15889); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15889); ?>; 
    position:absolute; top:130px; left:338px;'>
                        </div>


                        <!-- ASSET 15890 -->
                        <img src='../image.php?id=15890' style='width:18px; cursor:pointer; position:absolute; top:155px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15890' onclick='fetchAssetData(15890);' class="asset-image" data-id="<?php echo $assetId15890; ?>" data-room="<?php echo htmlspecialchars($room15890); ?>" data-floor="<?php echo htmlspecialchars($floor15890); ?>" data-image="<?php echo base64_encode($upload_img15890); ?>" data-status="<?php echo htmlspecialchars($status15890); ?>" data-category="<?php echo htmlspecialchars($category15890); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15890); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15890); ?>; 
    position:absolute; top:159px; left:246px;'>
                        </div>

                        <!-- ASSET 15891 -->
                        <img src='../image.php?id=15891' style='width:18px; cursor:pointer; position:absolute; top:167px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15891' onclick='fetchAssetData(15891);' class="asset-image" data-id="<?php echo $assetId15891; ?>" data-room="<?php echo htmlspecialchars($room15891); ?>" data-floor="<?php echo htmlspecialchars($floor15891); ?>" data-image="<?php echo base64_encode($upload_img15891); ?>" data-status="<?php echo htmlspecialchars($status15891); ?>" data-category="<?php echo htmlspecialchars($category15891); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15891); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15891); ?>; 
    position:absolute; top:171px; left:246px;'>
                        </div>

                        <!-- ASSET 15892 -->
                        <img src='../image.php?id=15892' style='width:18px; cursor:pointer; position:absolute; top:179px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15892' onclick='fetchAssetData(15892);' class="asset-image" data-id="<?php echo $assetId15892; ?>" data-room="<?php echo htmlspecialchars($room15892); ?>" data-floor="<?php echo htmlspecialchars($floor15892); ?>" data-image="<?php echo base64_encode($upload_img15892); ?>" data-status="<?php echo htmlspecialchars($status15892); ?>" data-category="<?php echo htmlspecialchars($category15892); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15892); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15892); ?>; 
    position:absolute; top:183px; left:246px;'>
                        </div>

                        <!-- ASSET 15893 -->
                        <img src='../image.php?id=15893' style='width:18px; cursor:pointer; position:absolute; top:191px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15893' onclick='fetchAssetData(15893);' class="asset-image" data-id="<?php echo $assetId15893; ?>" data-room="<?php echo htmlspecialchars($room15893); ?>" data-floor="<?php echo htmlspecialchars($floor15893); ?>" data-image="<?php echo base64_encode($upload_img15893); ?>" data-category="<?php echo htmlspecialchars($category15893); ?>" data-status="<?php echo htmlspecialchars($status15893); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15893); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15893); ?>; 
    position:absolute; top:194px; left:246px;'>
                        </div>

                        <!-- ASSET 15894 -->
                        <img src='../image.php?id=15894' style='width:18px; cursor:pointer; position:absolute; top:203px; left:233px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15894' onclick='fetchAssetData(15894);' class="asset-image" data-id="<?php echo $assetId15894; ?>" data-room="<?php echo htmlspecialchars($room15894); ?>" data-floor="<?php echo htmlspecialchars($floor15894); ?>" data-image="<?php echo base64_encode($upload_img15894); ?>" data-status="<?php echo htmlspecialchars($status15894); ?>" data-category="<?php echo htmlspecialchars($category15894); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15894); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15894); ?>; 
    position:absolute; top:207px; left:246px;'>
                        </div>

                        <!-- ASSET 15895 -->
                        <img src='../image.php?id=15895' style='width:18px; cursor:pointer; position:absolute; top:155px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15895' onclick='fetchAssetData(15895);' class="asset-image" data-id="<?php echo $assetId15895; ?>" data-room="<?php echo htmlspecialchars($room15895); ?>" data-floor="<?php echo htmlspecialchars($floor15895); ?>" data-status="<?php echo htmlspecialchars($status15895); ?>" data-image="<?php echo base64_encode($upload_img15895); ?>" data-category="<?php echo htmlspecialchars($category15895); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15895); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15895); ?>; 
    position:absolute; top:159px; left:269px;'>
                        </div>

                        <!-- ASSET 15896 -->
                        <img src='../image.php?id=15896' style='width:18px; cursor:pointer; position:absolute; top:167px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15896' onclick='fetchAssetData(15896);' class="asset-image" data-id="<?php echo $assetId15896; ?>" data-room="<?php echo htmlspecialchars($room15896); ?>" data-floor="<?php echo htmlspecialchars($floor15896); ?>" data-image="<?php echo base64_encode($upload_img15896); ?>" data-status="<?php echo htmlspecialchars($status15896); ?>" data-category="<?php echo htmlspecialchars($category15896); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15896); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15896); ?>; 
    position:absolute; top:171px; left:269px;'>
                        </div>

                        <!-- ASSET 15897 -->
                        <img src='../image.php?id=15897' style='width:18px; cursor:pointer; position:absolute; top:179px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15897' onclick='fetchAssetData(15897);' class="asset-image" data-id="<?php echo $assetId15897; ?>" data-room="<?php echo htmlspecialchars($room15897); ?>" data-floor="<?php echo htmlspecialchars($floor15897); ?>" data-image="<?php echo base64_encode($upload_img15897); ?>" data-status="<?php echo htmlspecialchars($status15897); ?>" data-category="<?php echo htmlspecialchars($category15897); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15897); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15897); ?>; 
    position:absolute; top:183px; left:269px;'>
                        </div>


                        <!-- ASSET 15898 -->
                        <img src='../image.php?id=15898' style='width:18px; cursor:pointer; position:absolute; top:191px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15898' onclick='fetchAssetData(15898);' class="asset-image" data-id="<?php echo $assetId15898; ?>" data-room="<?php echo htmlspecialchars($room15898); ?>" data-floor="<?php echo htmlspecialchars($floor15898); ?>" data-status="<?php echo htmlspecialchars($status15898); ?>" data-image="<?php echo base64_encode($upload_img15898); ?>" data-category="<?php echo htmlspecialchars($category15898); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15898); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15898); ?>; 
    position:absolute; top:194px; left:269px;'>
                        </div>

                        <!-- ASSET 15899 -->
                        <img src='../image.php?id=15899' style='width:18px; cursor:pointer; position:absolute; top:203px; left:256px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15899' onclick='fetchAssetData(15899);' class="asset-image" data-id="<?php echo $assetId15899; ?>" data-room="<?php echo htmlspecialchars($room15899); ?>" data-floor="<?php echo htmlspecialchars($floor15899); ?>" data-image="<?php echo base64_encode($upload_img15899); ?>" data-category="<?php echo htmlspecialchars($category15899); ?>" data-status="<?php echo htmlspecialchars($status15899); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15899); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15899); ?>; 
    position:absolute; top:207px; left:269px;'>
                        </div>

                        <!-- ASSET 15900 -->
                        <img src='../image.php?id=15900' style='width:18px; cursor:pointer; position:absolute; top:155px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15900' onclick='fetchAssetData(15900);' class="asset-image" data-id="<?php echo $assetId15900; ?>" data-room="<?php echo htmlspecialchars($room15900); ?>" data-floor="<?php echo htmlspecialchars($floor15900); ?>" data-image="<?php echo base64_encode($upload_img15900); ?>" data-category="<?php echo htmlspecialchars($category15900); ?>" data-status="<?php echo htmlspecialchars($status15900); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15900); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15900); ?>; 
    position:absolute; top:159px; left:292px;'>
                        </div>

                        <!-- ASSET 15901 -->
                        <img src='../image.php?id=15901' style='width:18px; cursor:pointer; position:absolute; top:167px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15901' onclick='fetchAssetData(15901);' class="asset-image" data-id="<?php echo $assetId15901; ?>" data-room="<?php echo htmlspecialchars($room15901); ?>" data-floor="<?php echo htmlspecialchars($floor15901); ?>" data-image="<?php echo base64_encode($upload_img15901); ?>" data-status="<?php echo htmlspecialchars($status15901); ?>" data-category="<?php echo htmlspecialchars($category15901); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15901); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15901); ?>; 
    position:absolute; top:171px; left:292px;'>
                        </div>

                        <!-- ASSET 15902 -->
                        <img src='../image.php?id=15902' style='width:18px; cursor:pointer; position:absolute; top:179px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15902' onclick='fetchAssetData(15902);' class="asset-image" data-id="<?php echo $assetId15902; ?>" data-room="<?php echo htmlspecialchars($room15902); ?>" data-floor="<?php echo htmlspecialchars($floor15902); ?>" data-image="<?php echo base64_encode($upload_img15902); ?>" data-category="<?php echo htmlspecialchars($category15902); ?>" data-status="<?php echo htmlspecialchars($status15902); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15902); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15902); ?>; 
    position:absolute; top:183px; left:292px;'>
                        </div>

                        <!-- ASSET 15903 -->
                        <img src='../image.php?id=15903' style='width:18px; cursor:pointer; position:absolute; top:191px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15903' onclick='fetchAssetData(15903);' class="asset-image" data-id="<?php echo $assetId15903; ?>" data-room="<?php echo htmlspecialchars($room15903); ?>" data-floor="<?php echo htmlspecialchars($floor15903); ?>" data-status="<?php echo htmlspecialchars($status15903); ?>" data-image="<?php echo base64_encode($upload_img15903); ?>" data-category="<?php echo htmlspecialchars($category15903); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15903); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15903); ?>; 
    position:absolute; top:194px; left:292px;'>
                        </div>

                        <!-- ASSET 15904 -->
                        <img src='../image.php?id=15904' style='width:18px; cursor:pointer; position:absolute; top:203px; left:279px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15904' onclick='fetchAssetData(15904);' class="asset-image" data-id="<?php echo $assetId15904; ?>" data-room="<?php echo htmlspecialchars($room15904); ?>" data-floor="<?php echo htmlspecialchars($floor15904); ?>" data-image="<?php echo base64_encode($upload_img15904); ?>" data-status="<?php echo htmlspecialchars($status15904); ?>" data-category="<?php echo htmlspecialchars($category15904); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15904); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15904); ?>; 
    position:absolute; top:207px; left:292px;'>
                        </div>

                        <!-- ASSET 15905 -->
                        <img src='../image.php?id=15905' style='width:18px; cursor:pointer; position:absolute; top:155px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15905' onclick='fetchAssetData(15905);' class="asset-image" data-id="<?php echo $assetId15905; ?>" data-room="<?php echo htmlspecialchars($room15905); ?>" data-floor="<?php echo htmlspecialchars($floor15905); ?>" data-image="<?php echo base64_encode($upload_img15905); ?>" data-status="<?php echo htmlspecialchars($status15905); ?>" data-category="<?php echo htmlspecialchars($category15905); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15905); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15905); ?>; 
    position:absolute; top:159px; left:315px;'>
                        </div>


                        <!-- ASSET 15906 -->
                        <img src='../image.php?id=15906' style='width:18px; cursor:pointer; position:absolute; top:167px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15906' onclick='fetchAssetData(15906);' class="asset-image" data-id="<?php echo $assetId15906; ?>" data-room="<?php echo htmlspecialchars($room15906); ?>" data-floor="<?php echo htmlspecialchars($floor15906); ?>" data-image="<?php echo base64_encode($upload_img15906); ?>" data-status="<?php echo htmlspecialchars($status15906); ?>" data-category="<?php echo htmlspecialchars($category15906); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15906); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15906); ?>; 
    position:absolute; top:171px; left:315px;'>
                        </div>

                        <!-- ASSET 15907 -->
                        <img src='../image.php?id=15907' style='width:18px; cursor:pointer; position:absolute; top:179px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15907' onclick='fetchAssetData(15907);' class="asset-image" data-id="<?php echo $assetId15907; ?>" data-room="<?php echo htmlspecialchars($room15907); ?>" data-floor="<?php echo htmlspecialchars($floor15907); ?>" data-image="<?php echo base64_encode($upload_img15907); ?>" data-category="<?php echo htmlspecialchars($category15907); ?>" data-status="<?php echo htmlspecialchars($status15907); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15907); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15907); ?>; 
    position:absolute; top:183px; left:315px;'>
                        </div>

                        <!-- ASSET 15909 -->
                        <img src='../image.php?id=15909' style='width:18px; cursor:pointer; position:absolute; top:191px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15909' onclick='fetchAssetData(15909);' class="asset-image" data-id="<?php echo $assetId15909; ?>" data-room="<?php echo htmlspecialchars($room15909); ?>" data-floor="<?php echo htmlspecialchars($floor15909); ?>" data-image="<?php echo base64_encode($upload_img15909); ?>" data-status="<?php echo htmlspecialchars($status15909); ?>" data-category="<?php echo htmlspecialchars($category15909); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15909); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15909); ?>; 
    position:absolute; top:194px; left:315px;'>
                        </div>

                        <!-- ASSET 15910 -->
                        <img src='../image.php?id=15910' style='width:18px; cursor:pointer; position:absolute; top:203px; left:302px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15910' onclick='fetchAssetData(15910);' class="asset-image" data-id="<?php echo $assetId15910; ?>" data-room="<?php echo htmlspecialchars($room15910); ?>" data-floor="<?php echo htmlspecialchars($floor15910); ?>" data-image="<?php echo base64_encode($upload_img15910); ?>" data-status="<?php echo htmlspecialchars($status15910); ?>" data-category="<?php echo htmlspecialchars($category15910); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15910); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15910); ?>; 
    position:absolute; top:207px; left:315px;'>
                        </div>


                        <!-- ASSET 15911 -->
                        <img src='../image.php?id=15911' style='width:18px; cursor:pointer; position:absolute; top:155px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15911' onclick='fetchAssetData(15911);' class="asset-image" data-id="<?php echo $assetId15911; ?>" data-room="<?php echo htmlspecialchars($room15911); ?>" data-floor="<?php echo htmlspecialchars($floor15911); ?>" data-image="<?php echo base64_encode($upload_img15911); ?>" data-category="<?php echo htmlspecialchars($category15911); ?>" data-status="<?php echo htmlspecialchars($status15911); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15911); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15911); ?>; 
    position:absolute; top:159px; left:338px;'>
                        </div>

                        <!-- ASSET 15912 -->
                        <img src='../image.php?id=15912' style='width:18px; cursor:pointer; position:absolute; top:167px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15912' onclick='fetchAssetData(15912);' class="asset-image" data-id="<?php echo $assetId15912; ?>" data-room="<?php echo htmlspecialchars($room15912); ?>" data-floor="<?php echo htmlspecialchars($floor15912); ?>" data-image="<?php echo base64_encode($upload_img15912); ?>" data-status="<?php echo htmlspecialchars($status15912); ?>" data-category="<?php echo htmlspecialchars($category15912); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15912); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15912); ?>; 
    position:absolute; top:171px; left:338px;'>
                        </div>

                        <!-- ASSET 15913 -->
                        <img src='../image.php?id=15913' style='width:18px; cursor:pointer; position:absolute; top:179px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15913' onclick='fetchAssetData(15913);' class="asset-image" data-id="<?php echo $assetId15913; ?>" data-room="<?php echo htmlspecialchars($room15913); ?>" data-floor="<?php echo htmlspecialchars($floor15913); ?>" data-image="<?php echo base64_encode($upload_img15913); ?>" data-category="<?php echo htmlspecialchars($category15913); ?>" data-status="<?php echo htmlspecialchars($status15913); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15913); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15913); ?>; 
    position:absolute; top:183px; left:338px;'>
                        </div>

                        <!-- ASSET 15914 -->
                        <img src='../image.php?id=15914' style='width:18px; cursor:pointer; position:absolute; top:191px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15914' onclick='fetchAssetData(15914);' class="asset-image" data-id="<?php echo $assetId15914; ?>" data-room="<?php echo htmlspecialchars($room15914); ?>" data-floor="<?php echo htmlspecialchars($floor15914); ?>" data-image="<?php echo base64_encode($upload_img15914); ?>" data-status="<?php echo htmlspecialchars($status15914); ?>" data-category="<?php echo htmlspecialchars($category15914); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15914); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15914); ?>; 
    position:absolute; top:194px; left:338px;'>
                        </div>

                        <!-- ASSET 15915 -->
                        <img src='../image.php?id=15915' style='width:18px; cursor:pointer; position:absolute; top:203px; left:325px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15915' onclick='fetchAssetData(15915);' class="asset-image" data-id="<?php echo $assetId15915; ?>" data-room="<?php echo htmlspecialchars($room15915); ?>" data-floor="<?php echo htmlspecialchars($floor15915); ?>" data-status="<?php echo htmlspecialchars($status15915); ?>" data-image="<?php echo base64_encode($upload_img15915); ?>" data-category="<?php echo htmlspecialchars($category15915); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15915); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15915); ?>; 
    position:absolute; top:207px; left:338px;'>
                        </div>

                        <!-- ASSET 15351 -->
                        <img src='../image.php?id=15351' style='width:15px; cursor:pointer; position:absolute; top:70px; left:490px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15351' onclick='fetchAssetData(15351);' class="asset-image" data-id="<?php echo $assetId15351; ?>" data-room="<?php echo htmlspecialchars($room15351); ?>" data-floor="<?php echo htmlspecialchars($floor15351); ?>" data-image="<?php echo base64_encode($upload_img15351); ?>" data-category="<?php echo htmlspecialchars($category15351); ?>" data-status="<?php echo htmlspecialchars($status15351); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15351); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15351); ?>; 
    position:absolute; top:70px; left:500px;'>
                        </div>

                        <!-- ASSET 15352 -->
                        <img src='../image.php?id=15352' style='width:15px; cursor:pointer; position:absolute; top:145px; left:490px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15352' onclick='fetchAssetData(15352);' class="asset-image" data-id="<?php echo $assetId15352; ?>" data-room="<?php echo htmlspecialchars($room15352); ?>" data-floor="<?php echo htmlspecialchars($floor15352); ?>" data-image="<?php echo base64_encode($upload_img15352); ?>" data-category="<?php echo htmlspecialchars($category15352); ?>" data-status="<?php echo htmlspecialchars($status15352); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15352); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15352); ?>; 
    position:absolute; top:145px; left:500px;'>
                        </div>

                        <!-- ASSET 15353 -->
                        <img src='../image.php?id=15353' style='width:15px; cursor:pointer; position:absolute; top:70px; left:615px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15353' onclick='fetchAssetData(15353);' class="asset-image" data-id="<?php echo $assetId15353; ?>" data-room="<?php echo htmlspecialchars($room15353); ?>" data-floor="<?php echo htmlspecialchars($floor15353); ?>" data-image="<?php echo base64_encode($upload_img15353); ?>" data-category="<?php echo htmlspecialchars($category15353); ?>" data-status="<?php echo htmlspecialchars($status15353); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15353); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15353); ?>; 
    position:absolute; top:70px; left:625px;'>
                        </div>

                        <!-- ASSET 15354 -->
                        <img src='../image.php?id=15354' style='width:15px; cursor:pointer; position:absolute; top:145px; left:615px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15354' onclick='fetchAssetData(15354);' class="asset-image" data-id="<?php echo $assetId15354; ?>" data-room="<?php echo htmlspecialchars($room15354); ?>" data-floor="<?php echo htmlspecialchars($floor15354); ?>" data-image="<?php echo base64_encode($upload_img15354); ?>" data-category="<?php echo htmlspecialchars($category15354); ?>" data-status="<?php echo htmlspecialchars($status15354); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15354); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15354); ?>; 
    position:absolute; top:145px; left:625px;'>
                        </div>

                        <!-- ASSET 15358 -->
                        <img src='../image.php?id=15358' style='width:15px; cursor:pointer; position:absolute; top:145px; left:729px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15358' onclick='fetchAssetData(15358);' class="asset-image" data-id="<?php echo $assetId15358; ?>" data-room="<?php echo htmlspecialchars($room15358); ?>" data-floor="<?php echo htmlspecialchars($floor15358); ?>" data-image="<?php echo base64_encode($upload_img15358); ?>" data-category="<?php echo htmlspecialchars($category15358); ?>" data-status="<?php echo htmlspecialchars($status15358); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15358); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15358); ?>; 
    position:absolute; top:145px; left:739px;'>
                        </div>

                        <!-- ASSET 15359 -->
                        <img src='../image.php?id=15359' style='width:15px; cursor:pointer; position:absolute; top:70px; left:729px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15359' onclick='fetchAssetData(15359);' class="asset-image" data-id="<?php echo $assetId15359; ?>" data-room="<?php echo htmlspecialchars($room15359); ?>" data-floor="<?php echo htmlspecialchars($floor15359); ?>" data-image="<?php echo base64_encode($upload_img15359); ?>" data-category="<?php echo htmlspecialchars($category15359); ?>" data-status="<?php echo htmlspecialchars($status15359); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15359); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15359); ?>; 
    position:absolute; top:70px; left:739px;'>
                        </div>

                        <!-- ASSET 15355 -->
                        <img src='../image.php?id=15355' style='width:15px; cursor:pointer; position:absolute; top:225px; left:490px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15355' onclick='fetchAssetData(15355);' class="asset-image" data-id="<?php echo $assetId15355; ?>" data-room="<?php echo htmlspecialchars($room15355); ?>" data-floor="<?php echo htmlspecialchars($floor15355); ?>" data-image="<?php echo base64_encode($upload_img15355); ?>" data-status="<?php echo htmlspecialchars($status15355); ?>" data-category="<?php echo htmlspecialchars($category15355); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15355); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15355); ?>; 
    position:absolute; top:225px; left:500px;'>
                        </div>


                        <!-- ASSET 15356 -->
                        <img src='../image.php?id=15356' style='width:15px; cursor:pointer; position:absolute; top:225px; left:615px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15356' onclick='fetchAssetData(15356);' class="asset-image" data-id="<?php echo $assetId15356; ?>" data-room="<?php echo htmlspecialchars($room15356); ?>" data-floor="<?php echo htmlspecialchars($floor15356); ?>" data-image="<?php echo base64_encode($upload_img15356); ?>" data-status="<?php echo htmlspecialchars($status15356); ?>" data-category="<?php echo htmlspecialchars($category15356); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15356); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15356); ?>; 
    position:absolute; top:225px; left:625px;'>
                        </div>

                        <!-- ASSET 15357 -->
                        <img src='../image.php?id=15357' style='width:15px; cursor:pointer; position:absolute; top:225px; left:729px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15357' onclick='fetchAssetData(15357);' class="asset-image" data-id="<?php echo $assetId15357; ?>" data-room="<?php echo htmlspecialchars($room15357); ?>" data-floor="<?php echo htmlspecialchars($floor15357); ?>" data-image="<?php echo base64_encode($upload_img15357); ?>" data-status="<?php echo htmlspecialchars($status15357); ?>" data-category="<?php echo htmlspecialchars($category15357); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15357); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15357); ?>; 
    position:absolute; top:225px; left:739px;'>
                        </div>

                        <!-- ASSET 15926 -->
                        <img src='../image.php?id=15926' style='width:18px; cursor:pointer; position:absolute; top:74px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15926' onclick='fetchAssetData(15926);' class="asset-image" data-id="<?php echo $assetId15926; ?>" data-room="<?php echo htmlspecialchars($room15926); ?>" data-floor="<?php echo htmlspecialchars($floor15926); ?>" data-image="<?php echo base64_encode($upload_img15926); ?>" data-status="<?php echo htmlspecialchars($status15926); ?>" data-category="<?php echo htmlspecialchars($category15926); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15926); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15926); ?>; 
    position:absolute; top:79px; left:518px;'>
                        </div>

                        <!-- ASSET 15927 -->
                        <img src='../image.php?id=15927' style='width:18px; cursor:pointer; position:absolute; top:87px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15927' onclick='fetchAssetData(15927);' class="asset-image" data-id="<?php echo $assetId15927; ?>" data-room="<?php echo htmlspecialchars($room15927); ?>" data-floor="<?php echo htmlspecialchars($floor15927); ?>" data-image="<?php echo base64_encode($upload_img15927); ?>" data-status="<?php echo htmlspecialchars($status15927); ?>" data-category="<?php echo htmlspecialchars($category15927); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15927); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15927); ?>; 
    position:absolute; top:92px; left:518px;'>
                        </div>

                        <!-- ASSET 15928 -->
                        <img src='../image.php?id=15928' style='width:18px; cursor:pointer; position:absolute; top:100px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15928' onclick='fetchAssetData(15928);' class="asset-image" data-id="<?php echo $assetId15928; ?>" data-room="<?php echo htmlspecialchars($room15928); ?>" data-floor="<?php echo htmlspecialchars($floor15928); ?>" data-image="<?php echo base64_encode($upload_img15928); ?>" data-status="<?php echo htmlspecialchars($status15928); ?>" data-category="<?php echo htmlspecialchars($category15928); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15928); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15928); ?>; 
    position:absolute; top:104px; left:518px;'>
                        </div>

                        <!-- ASSET 15928 -->
                        <img src='../image.php?id=15928' style='width:18px; cursor:pointer; position:absolute; top:113px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15928' onclick='fetchAssetData(15928);' class="asset-image" data-id="<?php echo $assetId15928; ?>" data-room="<?php echo htmlspecialchars($room15928); ?>" data-floor="<?php echo htmlspecialchars($floor15928); ?>" data-image="<?php echo base64_encode($upload_img15928); ?>" data-status="<?php echo htmlspecialchars($status15928); ?>" data-category="<?php echo htmlspecialchars($category15928); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15928); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15928); ?>; 
    position:absolute; top:118px; left:518px;'>
                        </div>

                        <!-- ASSET 15921 -->
                        <img src='../image.php?id=15921' style='width:18px; cursor:pointer; position:absolute; top:126px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15921' onclick='fetchAssetData(15921);' class="asset-image" data-id="<?php echo $assetId15921; ?>" data-room="<?php echo htmlspecialchars($room15921); ?>" data-floor="<?php echo htmlspecialchars($floor15921); ?>" data-image="<?php echo base64_encode($upload_img15921); ?>" data-status="<?php echo htmlspecialchars($status15921); ?>" data-category="<?php echo htmlspecialchars($category15921); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15921); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15921); ?>; 
    position:absolute; top:131px; left:518px;'>
                        </div>

                        <!-- ASSET 15922 -->
                        <img src='../image.php?id=15922' style='width:18px; cursor:pointer; position:absolute; top:74px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15922' onclick='fetchAssetData(15922);' class="asset-image" data-id="<?php echo $assetId15922; ?>" data-room="<?php echo htmlspecialchars($room15922); ?>" data-floor="<?php echo htmlspecialchars($floor15922); ?>" data-image="<?php echo base64_encode($upload_img15922); ?>" data-status="<?php echo htmlspecialchars($status15922); ?>" data-category="<?php echo htmlspecialchars($category15922); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15922); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15922); ?>; 
    position:absolute; top:79px; left:541px;'>
                        </div>

                        <!-- ASSET 15923 -->
                        <img src='../image.php?id=15923' style='width:18px; cursor:pointer; position:absolute; top:87px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15923' onclick='fetchAssetData(15923);' class="asset-image" data-id="<?php echo $assetId15923; ?>" data-room="<?php echo htmlspecialchars($room15923); ?>" data-floor="<?php echo htmlspecialchars($floor15923); ?>" data-image="<?php echo base64_encode($upload_img15923); ?>" data-category="<?php echo htmlspecialchars($category15923); ?>" data-status="<?php echo htmlspecialchars($status15923); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15923); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15923); ?>; 
    position:absolute; top:91px; left:541px;'>
                        </div>

                        <!-- ASSET 15924 -->
                        <img src='../image.php?id=15924' style='width:18px; cursor:pointer; position:absolute; top:100px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15924' onclick='fetchAssetData(15924);' class="asset-image" data-id="<?php echo $assetId15924; ?>" data-room="<?php echo htmlspecialchars($room15924); ?>" data-floor="<?php echo htmlspecialchars($floor15924); ?>" data-image="<?php echo base64_encode($upload_img15924); ?>" data-status="<?php echo htmlspecialchars($status15924); ?>" data-category="<?php echo htmlspecialchars($category15924); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15924); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15924); ?>; 
    position:absolute; top:104px; left:541px;'>
                        </div>

                        <!-- ASSET 15925 -->
                        <img src='../image.php?id=15925' style='width:18px; cursor:pointer; position:absolute; top:113px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15925' onclick='fetchAssetData(15925);' class="asset-image" data-id="<?php echo $assetId15925; ?>" data-room="<?php echo htmlspecialchars($room15925); ?>" data-floor="<?php echo htmlspecialchars($floor15925); ?>" data-image="<?php echo base64_encode($upload_img15925); ?>" data-status="<?php echo htmlspecialchars($status15925); ?>" data-category="<?php echo htmlspecialchars($category15925); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15925); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15925); ?>; 
    position:absolute; top:117px; left:541px;'>
                        </div>

                        <!-- ASSET 15926 -->
                        <img src='../image.php?id=15926' style='width:18px; cursor:pointer; position:absolute; top:126px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15926' onclick='fetchAssetData(15926);' class="asset-image" data-id="<?php echo $assetId15926; ?>" data-room="<?php echo htmlspecialchars($room15926); ?>" data-floor="<?php echo htmlspecialchars($floor15926); ?>" data-image="<?php echo base64_encode($upload_img15926); ?>" data-status="<?php echo htmlspecialchars($status15926); ?>" data-category="<?php echo htmlspecialchars($category15926); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15926); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15926); ?>; 
    position:absolute; top:130px; left:541px;'>
                        </div>

                        <!-- ASSET 15927 -->
                        <img src='../image.php?id=15927' style='width:18px; cursor:pointer; position:absolute; top:74px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15927' onclick='fetchAssetData(15927);' class="asset-image" data-id="<?php echo $assetId15927; ?>" data-room="<?php echo htmlspecialchars($room15927); ?>" data-floor="<?php echo htmlspecialchars($floor15927); ?>" data-image="<?php echo base64_encode($upload_img15927); ?>" data-status="<?php echo htmlspecialchars($status15927); ?>" data-category="<?php echo htmlspecialchars($category15927); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15927); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15927); ?>; 
    position:absolute; top:79px; left:564px;'>
                        </div>

                        <!-- ASSET 15371 -->
                        <img src='../image.php?id=15371' style='width:18px; cursor:pointer; position:absolute; top:87px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15371' onclick='fetchAssetData(15371);' class="asset-image" data-id="<?php echo $assetId15371; ?>" data-room="<?php echo htmlspecialchars($room15371); ?>" data-floor="<?php echo htmlspecialchars($floor15371); ?>" data-image="<?php echo base64_encode($upload_img15371); ?>" data-status="<?php echo htmlspecialchars($status15371); ?>" data-category="<?php echo htmlspecialchars($category15371); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15371); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15371); ?>; 
    position:absolute; top:91px; left:564px;'>
                        </div>

                        <!-- ASSET 15372 -->
                        <img src='../image.php?id=15372' style='width:18px; cursor:pointer; position:absolute; top:100px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15372' onclick='fetchAssetData(15372);' class="asset-image" data-id="<?php echo $assetId15372; ?>" data-room="<?php echo htmlspecialchars($room15372); ?>" data-floor="<?php echo htmlspecialchars($floor15372); ?>" data-image="<?php echo base64_encode($upload_img15372); ?>" data-status="<?php echo htmlspecialchars($status15372); ?>" data-category="<?php echo htmlspecialchars($category15372); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15372); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15372); ?>; 
    position:absolute; top:104px; left:564px;'>
                        </div>

                        <!-- ASSET 15373 -->
                        <img src='../image.php?id=15373' style='width:18px; cursor:pointer; position:absolute; top:113px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15373' onclick='fetchAssetData(15373);' class="asset-image" data-id="<?php echo $assetId15373; ?>" data-room="<?php echo htmlspecialchars($room15373); ?>" data-floor="<?php echo htmlspecialchars($floor15373); ?>" data-image="<?php echo base64_encode($upload_img15373); ?>" data-status="<?php echo htmlspecialchars($status15373); ?>" data-category="<?php echo htmlspecialchars($category15373); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15373); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15373); ?>; 
    position:absolute; top:117px; left:564px;'>
                        </div>


                        <!-- ASSET 15374 -->
                        <img src='../image.php?id=15374' style='width:18px; cursor:pointer; position:absolute; top:126px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15374' onclick='fetchAssetData(15374);' class="asset-image" data-id="<?php echo $assetId15374; ?>" data-room="<?php echo htmlspecialchars($room15374); ?>" data-floor="<?php echo htmlspecialchars($floor15374); ?>" data-image="<?php echo base64_encode($upload_img15374); ?>" data-status="<?php echo htmlspecialchars($status15374); ?>" data-category="<?php echo htmlspecialchars($category15374); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15374); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15374); ?>; 
    position:absolute; top:130px; left:564px;'>
                        </div>

                        <!-- ASSET 15375 -->
                        <img src='../image.php?id=15375' style='width:18px; cursor:pointer; position:absolute; top:74px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15375' onclick='fetchAssetData(15375);' class="asset-image" data-id="<?php echo $assetId15375; ?>" data-room="<?php echo htmlspecialchars($room15375); ?>" data-floor="<?php echo htmlspecialchars($floor15375); ?>" data-image="<?php echo base64_encode($upload_img15375); ?>" data-status="<?php echo htmlspecialchars($status15375); ?>" data-category="<?php echo htmlspecialchars($category15375); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15375); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15375); ?>; 
    position:absolute; top:79px; left:587px;'>
                        </div>

                        <!-- ASSET 15376 -->
                        <img src='../image.php?id=15376' style='width:18px; cursor:pointer; position:absolute; top:87px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15376' onclick='fetchAssetData(15376);' class="asset-image" data-id="<?php echo $assetId15376; ?>" data-room="<?php echo htmlspecialchars($room15376); ?>" data-floor="<?php echo htmlspecialchars($floor15376); ?>" data-status="<?php echo htmlspecialchars($status15376); ?>" data-image="<?php echo base64_encode($upload_img15376); ?>" data-category="<?php echo htmlspecialchars($category15376); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15376); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15376); ?>; 
    position:absolute; top:91px; left:587px;'>
                        </div>

                        <!-- ASSET 15377 -->
                        <img src='../image.php?id=15377' style='width:18px; cursor:pointer; position:absolute; top:100px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15377' onclick='fetchAssetData(15377);' class="asset-image" data-id="<?php echo $assetId15377; ?>" data-room="<?php echo htmlspecialchars($room15377); ?>" data-floor="<?php echo htmlspecialchars($floor15377); ?>" data-image="<?php echo base64_encode($upload_img15377); ?>" data-status="<?php echo htmlspecialchars($status15377); ?>" data-category="<?php echo htmlspecialchars($category15377); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15377); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15377); ?>; 
    position:absolute; top:104px; left:587px;'>
                        </div>

                        <!-- ASSET 15378 -->
                        <img src='../image.php?id=15378' style='width:18px; cursor:pointer; position:absolute; top:113px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15378' onclick='fetchAssetData(15378);' class="asset-image" data-id="<?php echo $assetId15378; ?>" data-room="<?php echo htmlspecialchars($room15378); ?>" data-floor="<?php echo htmlspecialchars($floor15378); ?>" data-image="<?php echo base64_encode($upload_img15378); ?>" data-status="<?php echo htmlspecialchars($status15378); ?>" data-category="<?php echo htmlspecialchars($category15378); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15378); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15378); ?>; 
    position:absolute; top:117px; left:587px;'>
                        </div>

                        <!-- ASSET 15379 -->
                        <img src='../image.php?id=15379' style='width:18px; cursor:pointer; position:absolute; top:127px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15379' onclick='fetchAssetData(15379);' class="asset-image" data-id="<?php echo $assetId15379; ?>" data-room="<?php echo htmlspecialchars($room15379); ?>" data-floor="<?php echo htmlspecialchars($floor15379); ?>" data-image="<?php echo base64_encode($upload_img15379); ?>" data-status="<?php echo htmlspecialchars($status15379); ?>" data-category="<?php echo htmlspecialchars($category15379); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15379); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15379); ?>; 
    position:absolute; top:130px; left:587px;'>
                        </div>

                        <!-- ASSET 15380 -->
                        <img src='../image.php?id=15380' style='width:18px; cursor:pointer; position:absolute; top:74px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15380' onclick='fetchAssetData(15380);' class="asset-image" data-id="<?php echo $assetId15380; ?>" data-room="<?php echo htmlspecialchars($room15380); ?>" data-floor="<?php echo htmlspecialchars($floor15380); ?>" data-image="<?php echo base64_encode($upload_img15380); ?>" data-status="<?php echo htmlspecialchars($status15380); ?>" data-category="<?php echo htmlspecialchars($category15380); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15380); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15380); ?>; 
    position:absolute; top:79px; left:610px;'>
                        </div>

                        <!-- ASSET 15381 -->
                        <img src='../image.php?id=15381' style='width:18px; cursor:pointer; position:absolute; top:87px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15381' onclick='fetchAssetData(15381);' class="asset-image" data-id="<?php echo $assetId15381; ?>" data-room="<?php echo htmlspecialchars($room15381); ?>" data-floor="<?php echo htmlspecialchars($floor15381); ?>" data-image="<?php echo base64_encode($upload_img15381); ?>" data-status="<?php echo htmlspecialchars($status15381); ?>" data-category="<?php echo htmlspecialchars($category15381); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15381); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15381); ?>; 
    position:absolute; top:91px; left:610px;'>
                        </div>

                        <!-- ASSET 15382 -->
                        <img src='../image.php?id=15382' style='width:18px; cursor:pointer; position:absolute; top:100px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15382' onclick='fetchAssetData(15382);' class="asset-image" data-id="<?php echo $assetId15382; ?>" data-room="<?php echo htmlspecialchars($room15382); ?>" data-floor="<?php echo htmlspecialchars($floor15382); ?>" data-image="<?php echo base64_encode($upload_img15382); ?>" data-status="<?php echo htmlspecialchars($status15382); ?>" data-category="<?php echo htmlspecialchars($category15382); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15382); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15382); ?>; 
    position:absolute; top:104px; left:610px;'>
                        </div>

                        <!-- ASSET 15383 -->
                        <img src='../image.php?id=15383' style='width:18px; cursor:pointer; position:absolute; top:113px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15383' onclick='fetchAssetData(15383);' class="asset-image" data-id="<?php echo $assetId15383; ?>" data-room="<?php echo htmlspecialchars($room15383); ?>" data-floor="<?php echo htmlspecialchars($floor15383); ?>" data-image="<?php echo base64_encode($upload_img15383); ?>" data-status="<?php echo htmlspecialchars($status15383); ?>" data-category="<?php echo htmlspecialchars($category15383); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15383); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15383); ?>; 
    position:absolute; top:117px; left:610px;'>
                        </div>

                        <!-- ASSET 15384 -->
                        <img src='../image.php?id=15384' style='width:18px; cursor:pointer; position:absolute; top:126px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15384' onclick='fetchAssetData(15384);' class="asset-image" data-id="<?php echo $assetId15384; ?>" data-room="<?php echo htmlspecialchars($room15384); ?>" data-floor="<?php echo htmlspecialchars($floor15384); ?>" data-image="<?php echo base64_encode($upload_img15384); ?>" data-status="<?php echo htmlspecialchars($status15384); ?>" data-category="<?php echo htmlspecialchars($category15384); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15384); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15384); ?>; 
    position:absolute; top:130px; left:610px;'>
                        </div>

                        <!-- ASSET 15385 -->
                        <img src='../image.php?id=15385' style='width:18px; cursor:pointer; position:absolute; top:155px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15385' onclick='fetchAssetData(15385);' class="asset-image" data-id="<?php echo $assetId15385; ?>" data-room="<?php echo htmlspecialchars($room15385); ?>" data-floor="<?php echo htmlspecialchars($floor15385); ?>" data-image="<?php echo base64_encode($upload_img15385); ?>" data-status="<?php echo htmlspecialchars($status15385); ?>" data-category="<?php echo htmlspecialchars($category15385); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15385); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15385); ?>; 
    position:absolute; top:159px; left:518px;'>
                        </div>

                        <!-- ASSET 15386 -->
                        <img src='../image.php?id=15386' style='width:18px; cursor:pointer; position:absolute; top:167px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15386' onclick='fetchAssetData(15386);' class="asset-image" data-id="<?php echo $assetId15386; ?>" data-room="<?php echo htmlspecialchars($room15386); ?>" data-floor="<?php echo htmlspecialchars($floor15386); ?>" data-image="<?php echo base64_encode($upload_img15386); ?>" data-status="<?php echo htmlspecialchars($status15386); ?>" data-category="<?php echo htmlspecialchars($category15386); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15386); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15386); ?>; 
    position:absolute; top:171px; left:518px;'>
                        </div>

                        <!-- ASSET 15387 -->
                        <img src='../image.php?id=15387' style='width:18px; cursor:pointer; position:absolute; top:179px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15387' onclick='fetchAssetData(15387);' class="asset-image" data-id="<?php echo $assetId15387; ?>" data-room="<?php echo htmlspecialchars($room15387); ?>" data-floor="<?php echo htmlspecialchars($floor15387); ?>" data-image="<?php echo base64_encode($upload_img15387); ?>" data-status="<?php echo htmlspecialchars($status15387); ?>" data-category="<?php echo htmlspecialchars($category15387); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15387); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15387); ?>; 
    position:absolute; top:183px; left:518px;'>
                        </div>

                        <!-- ASSET 15388 -->
                        <img src='../image.php?id=15388' style='width:18px; cursor:pointer; position:absolute; top:191px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15388' onclick='fetchAssetData(15388);' class="asset-image" data-id="<?php echo $assetId15388; ?>" data-room="<?php echo htmlspecialchars($room15388); ?>" data-floor="<?php echo htmlspecialchars($floor15388); ?>" data-image="<?php echo base64_encode($upload_img15388); ?>" data-category="<?php echo htmlspecialchars($category15388); ?>" data-status="<?php echo htmlspecialchars($status15388); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15388); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15388); ?>; 
    position:absolute; top:194px; left:518px;'>
                        </div>

                        <!-- ASSET 15389 -->
                        <img src='../image.php?id=15389' style='width:18px; cursor:pointer; position:absolute; top:203px; left:505px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15389' onclick='fetchAssetData(15389);' class="asset-image" data-id="<?php echo $assetId15389; ?>" data-room="<?php echo htmlspecialchars($room15389); ?>" data-floor="<?php echo htmlspecialchars($floor15389); ?>" data-image="<?php echo base64_encode($upload_img15389); ?>" data-status="<?php echo htmlspecialchars($status15389); ?>" data-category="<?php echo htmlspecialchars($category15389); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15389); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15389); ?>; 
    position:absolute; top:207px; left:518px;'>
                        </div>

                        <!-- ASSET 15390 -->
                        <img src='../image.php?id=15390' style='width:18px; cursor:pointer; position:absolute; top:155px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15390' onclick='fetchAssetData(15390);' class="asset-image" data-id="<?php echo $assetId15390; ?>" data-room="<?php echo htmlspecialchars($room15390); ?>" data-floor="<?php echo htmlspecialchars($floor15390); ?>" data-image="<?php echo base64_encode($upload_img15390); ?>" data-status="<?php echo htmlspecialchars($status15390); ?>" data-category="<?php echo htmlspecialchars($category15390); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15390); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15390); ?>; 
    position:absolute; top:159px; left:541px;'>
                        </div>

                        <!-- ASSET 15391 -->
                        <img src='../image.php?id=15391' style='width:18px; cursor:pointer; position:absolute; top:167px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15391' onclick='fetchAssetData(15391);' class="asset-image" data-id="<?php echo $assetId15391; ?>" data-room="<?php echo htmlspecialchars($room15391); ?>" data-floor="<?php echo htmlspecialchars($floor15391); ?>" data-image="<?php echo base64_encode($upload_img15391); ?>" data-category="<?php echo htmlspecialchars($category15391); ?>" data-status="<?php echo htmlspecialchars($status15391); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15391); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15391); ?>; 
    position:absolute; top:171px; left:541px;'>
                        </div>

                        <!-- ASSET 15392 -->
                        <img src='../image.php?id=15392' style='width:18px; cursor:pointer; position:absolute; top:179px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15392' onclick='fetchAssetData(15392);' class="asset-image" data-id="<?php echo $assetId15392; ?>" data-room="<?php echo htmlspecialchars($room15392); ?>" data-floor="<?php echo htmlspecialchars($floor15392); ?>" data-image="<?php echo base64_encode($upload_img15392); ?>" data-status="<?php echo htmlspecialchars($status15392); ?>" data-category="<?php echo htmlspecialchars($category15392); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15392); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15392); ?>; 
    position:absolute; top:183px; left:541px;'>
                        </div>

                        <!-- ASSET 15393 -->
                        <img src='../image.php?id=15393' style='width:18px; cursor:pointer; position:absolute; top:191px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15393' onclick='fetchAssetData(15393);' class="asset-image" data-id="<?php echo $assetId15393; ?>" data-room="<?php echo htmlspecialchars($room15393); ?>" data-floor="<?php echo htmlspecialchars($floor15393); ?>" data-image="<?php echo base64_encode($upload_img15393); ?>" data-category="<?php echo htmlspecialchars($category15393); ?>" data-status="<?php echo htmlspecialchars($status15393); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15393); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15393); ?>; 
    position:absolute; top:195px; left:541px;'>
                        </div>

                        <!-- ASSET 15394 -->
                        <img src='../image.php?id=15394' style='width:18px; cursor:pointer; position:absolute; top:203px; left:528px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15394' onclick='fetchAssetData(15394);' class="asset-image" data-id="<?php echo $assetId15394; ?>" data-room="<?php echo htmlspecialchars($room15394); ?>" data-floor="<?php echo htmlspecialchars($floor15394); ?>" data-image="<?php echo base64_encode($upload_img15394); ?>" data-category="<?php echo htmlspecialchars($category15394); ?>" data-status="<?php echo htmlspecialchars($status15394); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15394); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15394); ?>; 
    position:absolute; top:207px; left:541px;'>
                        </div>

                        <!-- ASSET 15395 -->
                        <img src='../image.php?id=15395' style='width:18px; cursor:pointer; position:absolute; top:155px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15395' onclick='fetchAssetData(15395);' class="asset-image" data-id="<?php echo $assetId15395; ?>" data-room="<?php echo htmlspecialchars($room15395); ?>" data-floor="<?php echo htmlspecialchars($floor15395); ?>" data-image="<?php echo base64_encode($upload_img15395); ?>" data-category="<?php echo htmlspecialchars($category15395); ?>" data-status="<?php echo htmlspecialchars($status15395); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15395); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15395); ?>; 
    position:absolute; top:159px; left:564px;'>
                        </div>

                        <!-- ASSET 15396 -->
                        <img src='../image.php?id=15396' style='width:18px; cursor:pointer; position:absolute; top:167px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15396' onclick='fetchAssetData(15396);' class="asset-image" data-id="<?php echo $assetId15396; ?>" data-room="<?php echo htmlspecialchars($room15396); ?>" data-floor="<?php echo htmlspecialchars($floor15396); ?>" data-image="<?php echo base64_encode($upload_img15396); ?>" data-category="<?php echo htmlspecialchars($category15396); ?>" data-status="<?php echo htmlspecialchars($status15396); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15396); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15396); ?>; 
    position:absolute; top:171px; left:564px;'>
                        </div>


                        <!-- ASSET 15397 -->
                        <img src='../image.php?id=15397' style='width:18px; cursor:pointer; position:absolute; top:179px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15397' onclick='fetchAssetData(15397);' class="asset-image" data-id="<?php echo $assetId15397; ?>" data-room="<?php echo htmlspecialchars($room15397); ?>" data-floor="<?php echo htmlspecialchars($floor15397); ?>" data-image="<?php echo base64_encode($upload_img15397); ?>" data-category="<?php echo htmlspecialchars($category15397); ?>" data-status="<?php echo htmlspecialchars($status15397); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15397); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15397); ?>; 
    position:absolute; top:183px; left:564px;'>
                        </div>

                        <!-- ASSET 15398 -->
                        <img src='../image.php?id=15398' style='width:18px; cursor:pointer; position:absolute; top:191px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15398' onclick='fetchAssetData(15398);' class="asset-image" data-id="<?php echo $assetId15398; ?>" data-room="<?php echo htmlspecialchars($room15398); ?>" data-floor="<?php echo htmlspecialchars($floor15398); ?>" data-image="<?php echo base64_encode($upload_img15398); ?>" data-category="<?php echo htmlspecialchars($category15398); ?>" data-status="<?php echo htmlspecialchars($status15398); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15398); ?>">

                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15398); ?>; 
    position:absolute; top:194px; left:564px;'>
                        </div>

                        <!-- ASSET 15399 -->
                        <img src='../image.php?id=15399' style='width:18px; cursor:pointer; position:absolute; top:203px; left:551px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15399' onclick='fetchAssetData(15399);' class="asset-image" data-id="<?php echo $assetId15399; ?>" data-room="<?php echo htmlspecialchars($room15399); ?>" data-floor="<?php echo htmlspecialchars($floor15399); ?>" data-image="<?php echo base64_encode($upload_img15399); ?>" data-status="<?php echo htmlspecialchars($status15399); ?>" data-category="<?php echo htmlspecialchars($category15399); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15399); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15399); ?>; 
    position:absolute; top:207px; left: 564px;'>
                        </div>

                        <!-- ASSET 15400 -->
                        <img src='../image.php?id=15400' style='width:18px; cursor:pointer; position:absolute; top:155px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15400' onclick='fetchAssetData(15400);' class="asset-image" data-id="<?php echo $assetId15400; ?>" data-room="<?php echo htmlspecialchars($room15400); ?>" data-floor="<?php echo htmlspecialchars($floor15400); ?>" data-image="<?php echo base64_encode($upload_img15400); ?>" data-status="<?php echo htmlspecialchars($status15400); ?>" data-category="<?php echo htmlspecialchars($category15400); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15400); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15400); ?>; 
    position:absolute; top:159px; left: 587px;'>
                        </div>

                        <!-- ASSET 15401 -->
                        <img src='../image.php?id=15401' style='width:18px; cursor:pointer; position:absolute; top:167px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15401' onclick='fetchAssetData(15401);' class="asset-image" data-id="<?php echo $assetId15401; ?>" data-room="<?php echo htmlspecialchars($room15401); ?>" data-floor="<?php echo htmlspecialchars($floor15401); ?>" data-image="<?php echo base64_encode($upload_img15401); ?>" data-category="<?php echo htmlspecialchars($category15401); ?>" data-status="<?php echo htmlspecialchars($status15401); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15401); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15401); ?>; 
    position:absolute; top:171px; left:587px;'>
                        </div>

                        <!-- ASSET 15402 -->
                        <img src='../image.php?id=15402' style='width:18px; cursor:pointer; position:absolute; top:179px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15402' onclick='fetchAssetData(15402);' class="asset-image" data-id="<?php echo $assetId15402; ?>" data-room="<?php echo htmlspecialchars($room15402); ?>" data-floor="<?php echo htmlspecialchars($floor15402); ?>" data-image="<?php echo base64_encode($upload_img15402); ?>" data-status="<?php echo htmlspecialchars($status15402); ?>" data-category="<?php echo htmlspecialchars($category15402); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15402); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15402); ?>; 
    position:absolute; top:183px; left:587px;'>
                        </div>

                        <!-- ASSET 15403 -->
                        <img src='../image.php?id=15403' style='width:18px; cursor:pointer; position:absolute; top:191px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15403' onclick='fetchAssetData(15403);' class="asset-image" data-id="<?php echo $assetId15403; ?>" data-room="<?php echo htmlspecialchars($room15403); ?>" data-floor="<?php echo htmlspecialchars($floor15403); ?>" data-image="<?php echo base64_encode($upload_img15403); ?>" data-status="<?php echo htmlspecialchars($status15403); ?>" data-category="<?php echo htmlspecialchars($category15403); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15403); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15403); ?>; 
    position:absolute; top:194px; left: 587px;'>
                        </div>

                        <!-- ASSET 15404 -->
                        <img src='../image.php?id=15404' style='width:18px; cursor:pointer; position:absolute; top:203px; left:574px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15404' onclick='fetchAssetData(15404);' class="asset-image" data-id="<?php echo $assetId15404; ?>" data-room="<?php echo htmlspecialchars($room15404); ?>" data-floor="<?php echo htmlspecialchars($floor15404); ?>" data-status="<?php echo htmlspecialchars($status15404); ?>" data-image="<?php echo base64_encode($upload_img15404); ?>" data-category="<?php echo htmlspecialchars($category15404); ?>">
                        data-assignedname="<?php echo htmlspecialchars($assignedName15404); ?>"
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15404); ?>; 
    position:absolute; top:207px; left:587px;'>
                        </div>


                        <!-- ASSET 15405 -->
                        <img src='../image.php?id=15405' style='width:18px; cursor:pointer; position:absolute; top:155px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15405' onclick='fetchAssetData(15405);' class="asset-image" data-id="<?php echo $assetId15405; ?>" data-room="<?php echo htmlspecialchars($room15405); ?>" data-floor="<?php echo htmlspecialchars($floor15405); ?>" data-image="<?php echo base64_encode($upload_img15405); ?>" data-status="<?php echo htmlspecialchars($status15405); ?>" data-category="<?php echo htmlspecialchars($category15405); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15405); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15405); ?>; 
position:absolute; top:159px; left:610px;'>
                        </div>

                        <!-- ASSET 15406 -->
                        <img src='../image.php?id=15406' style='width:18px; cursor:pointer; position:absolute; top:167px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15406' onclick='fetchAssetData(15406);' class="asset-image" data-id="<?php echo $assetId15406; ?>" data-room="<?php echo htmlspecialchars($room15406); ?>" data-floor="<?php echo htmlspecialchars($floor15406); ?>" data-image="<?php echo base64_encode($upload_img15406); ?>" data-status="<?php echo htmlspecialchars($status15406); ?>" data-category="<?php echo htmlspecialchars($category15406); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15406); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15406); ?>; 
    position:absolute; top:171px; left:610px;'>
                        </div>

                        <!-- ASSET 15407 -->
                        <img src='../image.php?id=15407' style='width:18px; cursor:pointer; position:absolute; top:179px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15407' onclick='fetchAssetData(15407);' class="asset-image" data-id="<?php echo $assetId15407; ?>" data-room="<?php echo htmlspecialchars($room15407); ?>" data-floor="<?php echo htmlspecialchars($floor15407); ?>" data-status="<?php echo htmlspecialchars($status15407); ?>" data-image="<?php echo base64_encode($upload_img15407); ?>" data-category="<?php echo htmlspecialchars($category15407); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15407); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15407); ?>; 
    position:absolute; top:183px; left:610px;'>
                        </div>

                        <!-- ASSET 15408 -->
                        <img src='../image.php?id=15408' style='width:18px; cursor:pointer; position:absolute; top:191px; left:597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15408' onclick='fetchAssetData(15408);' class="asset-image" data-id="<?php echo $assetId15408; ?>" data-room="<?php echo htmlspecialchars($room15408); ?>" data-floor="<?php echo htmlspecialchars($floor15408); ?>" data-image="<?php echo base64_encode($upload_img15408); ?>" data-category="<?php echo htmlspecialchars($category15408); ?>" data-status="<?php echo htmlspecialchars($status15408); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15408); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15408); ?>; 
    position:absolute; top:194px; left:610px;'>
                        </div>


                        <!-- ASSET 15409 -->
                        <img src='../image.php?id=15409' style='width:18px; cursor:pointer; position:absolute; top:203px; left: 597px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15409' onclick='fetchAssetData(15409);' class="asset-image" data-id="<?php echo $assetId15409; ?>" data-room="<?php echo htmlspecialchars($room15409); ?>" data-floor="<?php echo htmlspecialchars($floor15409); ?>" data-image="<?php echo base64_encode($upload_img15409); ?>" data-category="<?php echo htmlspecialchars($category15409); ?>" data-status="<?php echo htmlspecialchars($status15409); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15409); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15409); ?>; 
    position:absolute; top:207px; left:610px;'>
                        </div>

                        <!-- ASSET 6261
<img src='../image.php?id=6261'
    style='width:18px; cursor:pointer; position:absolute; top:144px; left:695px; transform: rotate(180deg);'
    alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6261'
    onclick='fetchAssetData(6261);' class="asset-image" data-id="<?php echo $assetId6261; ?>"
    data-room="<?php echo htmlspecialchars($room6261); ?>"
    data-floor="<?php echo htmlspecialchars($floor6261); ?>"
    data-image="<?php echo base64_encode($upload_img6261); ?>"
    data-category="<?php echo htmlspecialchars($category6261); ?>"
    data-status="<?php echo htmlspecialchars($status6261); ?>"
    data-assignedname="<?php echo htmlspecialchars($assignedName6261); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status6261); ?>; 
position:absolute; top:142px; left:700px;'>
</div> -->

                        <!-- ASSET 15566 -->
                        <img src='../image.php?id=15566' style='width:15px; cursor:pointer; position:absolute; top:143px; left: 698px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15566' onclick='fetchAssetData(15566);' class="asset-image" data-id="<?php echo $assetId15566; ?>" data-room="<?php echo htmlspecialchars($room15566); ?>" data-floor="<?php echo htmlspecialchars($floor15566); ?>" data-image="<?php echo base64_encode($upload_img15566); ?>" data-category="<?php echo htmlspecialchars($category15566); ?>" data-status="<?php echo htmlspecialchars($status15566); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15566); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15566); ?>; 
    position:absolute; top:135px; left:702px;'>
                        </div>

                        <!-- ASSET 15567 -->
                        <img src='../image.php?id=15567' style='width:15px; cursor:pointer; position:absolute; top:70px; left:755px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15567' onclick='fetchAssetData(15567);' class="asset-image" data-id="<?php echo $assetId15567; ?>" data-room="<?php echo htmlspecialchars($room15567); ?>" data-floor="<?php echo htmlspecialchars($floor15567); ?>" data-image="<?php echo base64_encode($upload_img15567); ?>" data-category="<?php echo htmlspecialchars($category15567); ?>" data-status="<?php echo htmlspecialchars($status15567); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15567); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15567); ?>; 
    position:absolute; top:70px; left:765px;'>
                        </div>


                        <!-- ASSET 15568 -->
                        <img src='../image.php?id=15568' style='width:15px; cursor:pointer; position:absolute; top:145px; left:755px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15568' onclick='fetchAssetData(15568);' class="asset-image" data-id="<?php echo $assetId15568; ?>" data-room="<?php echo htmlspecialchars($room15568); ?>" data-floor="<?php echo htmlspecialchars($floor15568); ?>" data-image="<?php echo base64_encode($upload_img15568); ?>" data-category="<?php echo htmlspecialchars($category15568); ?>" data-status="<?php echo htmlspecialchars($status15568); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15568); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15568); ?>; 
    position:absolute; top:145px; left:765px;'>
                        </div>

                        <!-- ASSET 15569 -->
                        <img src='../image.php?id=15569' style='width:15px; cursor:pointer; position:absolute; top:70px; left:885px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15569' onclick='fetchAssetData(15569);' class="asset-image" data-id="<?php echo $assetId15569; ?>" data-room="<?php echo htmlspecialchars($room15569); ?>" data-floor="<?php echo htmlspecialchars($floor15569); ?>" data-image="<?php echo base64_encode($upload_img15569); ?>" data-category="<?php echo htmlspecialchars($category15569); ?>" data-status="<?php echo htmlspecialchars($status15569); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15569); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15569); ?>; 
    position:absolute; top:70px; left:895px;'>
                        </div>

                        <!-- ASSET 15570 -->
                        <img src='../image.php?id=15570' style='width:15px; cursor:pointer; position:absolute; top:145px; left:885px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15570' onclick='fetchAssetData(15570);' class="asset-image" data-id="<?php echo $assetId15570; ?>" data-room="<?php echo htmlspecialchars($room15570); ?>" data-floor="<?php echo htmlspecialchars($floor15570); ?>" data-image="<?php echo base64_encode($upload_img15570); ?>" data-status="<?php echo htmlspecialchars($status15570); ?>" data-category="<?php echo htmlspecialchars($category15570); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15570); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15570); ?>; 
    position:absolute; top:145px; left:895px;'>
                        </div>

                        <!-- ASSET 15571 -->
                        <img src='../image.php?id=15571' style='width:15px; cursor:pointer; position:absolute; top:145px; left:995px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15571' onclick='fetchAssetData(15571);' class="asset-image" data-id="<?php echo $assetId15571; ?>" data-room="<?php echo htmlspecialchars($room15571); ?>" data-floor="<?php echo htmlspecialchars($floor15571); ?>" data-status="<?php echo htmlspecialchars($status15571); ?>" data-image="<?php echo base64_encode($upload_img15571); ?>" data-category="<?php echo htmlspecialchars($category15571); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15571); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15571); ?>; 
    position:absolute; top:145px; left:1005px;'>
                        </div>


                        <!-- ASSET 15572 -->
                        <img src='../image.php?id=15572' style='width:15px; cursor:pointer; position:absolute; top:70px; left:995px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15572' onclick='fetchAssetData(15572);' class="asset-image" data-id="<?php echo $assetId15572; ?>" data-room="<?php echo htmlspecialchars($room15572); ?>" data-floor="<?php echo htmlspecialchars($floor15572); ?>" data-image="<?php echo base64_encode($upload_img15572); ?>" data-category="<?php echo htmlspecialchars($category15572); ?>" data-status="<?php echo htmlspecialchars($status15572); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15572); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15572); ?>; 
    position:absolute; top:70px; left:1005px;'>
                        </div>

                        <!-- ASSET 15573 -->
                        <img src='../image.php?id=15573' style='width:15px; cursor:pointer; position:absolute; top:214px; left:755px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15573' onclick='fetchAssetData(15573);' class="asset-image" data-id="<?php echo $assetId15573; ?>" data-room="<?php echo htmlspecialchars($room15573); ?>" data-floor="<?php echo htmlspecialchars($floor15573); ?>" data-image="<?php echo base64_encode($upload_img15573); ?>" data-category="<?php echo htmlspecialchars($category15573); ?>" data-status="<?php echo htmlspecialchars($status15573); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15573); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15573); ?>; 
    position:absolute; top:214px; left:765px;'>
                        </div>

                        <!-- ASSET 15574 -->
                        <img src='../image.php?id=15574' style='width:15px; cursor:pointer; position:absolute; top:214px; left:885px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15574' onclick='fetchAssetData(15574);' class="asset-image" data-id="<?php echo $assetId15574; ?>" data-room="<?php echo htmlspecialchars($room15574); ?>" data-floor="<?php echo htmlspecialchars($floor15574); ?>" data-image="<?php echo base64_encode($upload_img15574); ?>" data-status="<?php echo htmlspecialchars($status15574); ?>" data-category="<?php echo htmlspecialchars($category15574); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15574); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15574); ?>; 
    position:absolute; top:214px; left:895px;'>
                        </div>

                        <!-- ASSET 15575 -->
                        <img src='../image.php?id=15575' style='width:15px; cursor:pointer; position:absolute; top:214px; left:995px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15575' onclick='fetchAssetData(15575);' class="asset-image" data-id="<?php echo $assetId15575; ?>" data-room="<?php echo htmlspecialchars($room15575); ?>" data-floor="<?php echo htmlspecialchars($floor15575); ?>" data-image="<?php echo base64_encode($upload_img15575); ?>" data-status="<?php echo htmlspecialchars($status15575); ?>" data-category="<?php echo htmlspecialchars($category15575); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15575); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15575); ?>; 
    position:absolute; top:214px; left:1005px;'>
                        </div>


                        <!-- /// -->

                        <!-- ASSET 15576 -->
                        <img src='../image.php?id=15576' style='width:18px; cursor:pointer; position:absolute; top:74px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15576' onclick='fetchAssetData(15576);' class="asset-image" data-id="<?php echo $assetId15576; ?>" data-room="<?php echo htmlspecialchars($room15576); ?>" data-floor="<?php echo htmlspecialchars($floor15576); ?>" data-image="<?php echo base64_encode($upload_img15576); ?>" data-category="<?php echo htmlspecialchars($category15576); ?>" data-status="<?php echo htmlspecialchars($status15576); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15576); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15576); ?>; 
    position:absolute; top:79px; left:783px;'>
                        </div>

                        <!-- ASSET 15577 -->
                        <img src='../image.php?id=15577' style='width:18px; cursor:pointer; position:absolute; top:87px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15577' onclick='fetchAssetData(15577);' class="asset-image" data-id="<?php echo $assetId15577; ?>" data-room="<?php echo htmlspecialchars($room15577); ?>" data-floor="<?php echo htmlspecialchars($floor15577); ?>" data-image="<?php echo base64_encode($upload_img15577); ?>" data-category="<?php echo htmlspecialchars($category15577); ?>" data-status="<?php echo htmlspecialchars($status15577); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15577); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15577); ?>; 
    position:absolute; top:92px; left:783px;'>
                        </div>

                        <!-- ASSET 15578 -->
                        <img src='../image.php?id=15578' style='width:18px; cursor:pointer; position:absolute; top:100px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15578' onclick='fetchAssetData(15578);' class="asset-image" data-id="<?php echo $assetId15578; ?>" data-room="<?php echo htmlspecialchars($room15578); ?>" data-floor="<?php echo htmlspecialchars($floor15578); ?>" data-image="<?php echo base64_encode($upload_img15578); ?>" data-status="<?php echo htmlspecialchars($status15578); ?>" data-category="<?php echo htmlspecialchars($category15578); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15578); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15578); ?>; 
    position:absolute; top:104px; left:783px;'>
                        </div>

                        <!-- ASSET 15579 -->
                        <img src='../image.php?id=15579' style='width:18px; cursor:pointer; position:absolute; top:113px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15579' onclick='fetchAssetData(15579);' class="asset-image" data-id="<?php echo $assetId15579; ?>" data-room="<?php echo htmlspecialchars($room15579); ?>" data-floor="<?php echo htmlspecialchars($floor15579); ?>" data-image="<?php echo base64_encode($upload_img15579); ?>" data-category="<?php echo htmlspecialchars($category15579); ?>" data-status="<?php echo htmlspecialchars($status15579); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15579); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15579); ?>; 
    position:absolute; top:118px; left:783px;'>
                        </div>

                        <!-- ASSET 15580 -->
                        <img src='../image.php?id=15580' style='width:18px; cursor:pointer; position:absolute; top:126px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15580' onclick='fetchAssetData(15580);' class="asset-image" data-id="<?php echo $assetId15580; ?>" data-room="<?php echo htmlspecialchars($room15580); ?>" data-floor="<?php echo htmlspecialchars($floor15580); ?>" data-image="<?php echo base64_encode($upload_img15580); ?>" data-category="<?php echo htmlspecialchars($category15580); ?>" data-status="<?php echo htmlspecialchars($status15580); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15580); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15580); ?>; 
    position:absolute; top:131px; left:783px;'>
                        </div>

                        <!-- ASSET 15581 -->
                        <img src='../image.php?id=15581' style='width:18px; cursor:pointer; position:absolute; top:74px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15581' onclick='fetchAssetData(15581);' class="asset-image" data-id="<?php echo $assetId15581; ?>" data-room="<?php echo htmlspecialchars($room15581); ?>" data-floor="<?php echo htmlspecialchars($floor15581); ?>" data-image="<?php echo base64_encode($upload_img15581); ?>" data-category="<?php echo htmlspecialchars($category15581); ?>" data-status="<?php echo htmlspecialchars($status15581); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15581); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15581); ?>; 
    position:absolute; top:79px; left:806px;'>
                        </div>

                        <!-- ASSET 15582 -->
                        <img src='../image.php?id=15582' style='width:18px; cursor:pointer; position:absolute; top:87px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15582' onclick='fetchAssetData(15582);' class="asset-image" data-id="<?php echo $assetId15582; ?>" data-room="<?php echo htmlspecialchars($room15582); ?>" data-floor="<?php echo htmlspecialchars($floor15582); ?>" data-image="<?php echo base64_encode($upload_img15582); ?>" data-status="<?php echo htmlspecialchars($status15582); ?>" data-category="<?php echo htmlspecialchars($category15582); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15582); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15582); ?>; 
    position:absolute; top:91px; left:806px;'>
                        </div>

                        <!-- ASSET 15583 -->
                        <img src='../image.php?id=15583' style='width:18px; cursor:pointer; position:absolute; top:100px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15583' onclick='fetchAssetData(15583);' class="asset-image" data-id="<?php echo $assetId15583; ?>" data-room="<?php echo htmlspecialchars($room15583); ?>" data-floor="<?php echo htmlspecialchars($floor15583); ?>" data-image="<?php echo base64_encode($upload_img15583); ?>" data-status="<?php echo htmlspecialchars($status15583); ?>" data-category="<?php echo htmlspecialchars($category15583); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15583); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15583); ?>; 
    position:absolute; top:104px; left:806px;'>
                        </div>


                        <!-- ASSET 15584 -->
                        <img src='../image.php?id=15584' style='width:18px; cursor:pointer; position:absolute; top:113px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15584' onclick='fetchAssetData(15584);' class="asset-image" data-id="<?php echo $assetId15584; ?>" data-room="<?php echo htmlspecialchars($room15584); ?>" data-floor="<?php echo htmlspecialchars($floor15584); ?>" data-image="<?php echo base64_encode($upload_img15584); ?>" data-status="<?php echo htmlspecialchars($status15584); ?>" data-category="<?php echo htmlspecialchars($category15584); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15584); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15584); ?>; 
    position:absolute; top:117px; left:806px;'>
                        </div>

                        <!-- ASSET 15585 -->
                        <img src='../image.php?id=15585' style='width:18px; cursor:pointer; position:absolute; top:126px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15585' onclick='fetchAssetData(15585);' class="asset-image" data-id="<?php echo $assetId15585; ?>" data-room="<?php echo htmlspecialchars($room15585); ?>" data-floor="<?php echo htmlspecialchars($floor15585); ?>" data-image="<?php echo base64_encode($upload_img15585); ?>" data-status="<?php echo htmlspecialchars($status15585); ?>" data-category="<?php echo htmlspecialchars($category15585); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15585); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15585); ?>; 
    position:absolute; top:130px; left:806px;'>
                        </div>

                        <!-- ASSET 15586 -->
                        <img src='../image.php?id=15586' style='width:18px; cursor:pointer; position:absolute; top:74px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15586' onclick='fetchAssetData(15586);' class="asset-image" data-id="<?php echo $assetId15586; ?>" data-room="<?php echo htmlspecialchars($room15586); ?>" data-floor="<?php echo htmlspecialchars($floor15586); ?>" data-status="<?php echo htmlspecialchars($status15586); ?>" data-image="<?php echo base64_encode($upload_img15586); ?>" data-category="<?php echo htmlspecialchars($category15586); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15586); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15586); ?>; 
    position:absolute; top:79px; left:829px;'>
                        </div>

                        <!-- ASSET 15587 -->
                        <img src='../image.php?id=15587' style='width:18px; cursor:pointer; position:absolute; top:87px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15587' onclick='fetchAssetData(15587);' class="asset-image" data-id="<?php echo $assetId15587; ?>" data-room="<?php echo htmlspecialchars($room15587); ?>" data-floor="<?php echo htmlspecialchars($floor15587); ?>" data-image="<?php echo base64_encode($upload_img15587); ?>" data-status="<?php echo htmlspecialchars($status15587); ?>" data-category="<?php echo htmlspecialchars($category15587); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15587); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15587); ?>; 
    position:absolute; top:91px; left:829px;'>
                        </div>


                        <!-- ASSET 15588 -->
                        <img src='../image.php?id=15588' style='width:18px; cursor:pointer; position:absolute; top:100px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15588' onclick='fetchAssetData(15588);' class="asset-image" data-id="<?php echo $assetId15588; ?>" data-room="<?php echo htmlspecialchars($room15588); ?>" data-floor="<?php echo htmlspecialchars($floor15588); ?>" data-image="<?php echo base64_encode($upload_img15588); ?>" data-category="<?php echo htmlspecialchars($category15588); ?>" data-status="<?php echo htmlspecialchars($status15588); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15588); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15588); ?>; 
    position:absolute; top:104px; left:829px;'>
                        </div>

                        <!-- ASSET 15589 -->
                        <img src='../image.php?id=15589' style='width:18px; cursor:pointer; position:absolute; top:113px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15589' onclick='fetchAssetData(15589);' class="asset-image" data-id="<?php echo $assetId15589; ?>" data-room="<?php echo htmlspecialchars($room15589); ?>" data-floor="<?php echo htmlspecialchars($floor15589); ?>" data-image="<?php echo base64_encode($upload_img15589); ?>" data-category="<?php echo htmlspecialchars($category15589); ?>" data-status="<?php echo htmlspecialchars($status15589); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15589); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15589); ?>; 
    position:absolute; top:117px; left:829px;'>
                        </div>

                        <!-- ASSET 15590 -->
                        <img src='../image.php?id=15590' style='width:18px; cursor:pointer; position:absolute; top:126px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15590' onclick='fetchAssetData(15590);' class="asset-image" data-id="<?php echo $assetId15590; ?>" data-room="<?php echo htmlspecialchars($room15590); ?>" data-floor="<?php echo htmlspecialchars($floor15590); ?>" data-image="<?php echo base64_encode($upload_img15590); ?>" data-status="<?php echo htmlspecialchars($status15590); ?>" data-category="<?php echo htmlspecialchars($category15590); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15590); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15590); ?>; 
    position:absolute; top:130px; left:829px;'>
                        </div>

                        <!-- ASSET 15591 -->
                        <img src='../image.php?id=15591' style='width:18px; cursor:pointer; position:absolute; top:74px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15591' onclick='fetchAssetData(15591);' class="asset-image" data-id="<?php echo $assetId15591; ?>" data-room="<?php echo htmlspecialchars($room15591); ?>" data-floor="<?php echo htmlspecialchars($floor15591); ?>" data-status="<?php echo htmlspecialchars($status15591); ?>" data-image="<?php echo base64_encode($upload_img15591); ?>" data-category="<?php echo htmlspecialchars($category15591); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15591); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15591); ?>; 
    position:absolute; top:79px; left:852px;'>
                        </div>

                        <!-- ASSET 15592 -->
                        <img src='../image.php?id=15592' style='width:18px; cursor:pointer; position:absolute; top:87px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15592' onclick='fetchAssetData(15592);' class="asset-image" data-id="<?php echo $assetId15592; ?>" data-room="<?php echo htmlspecialchars($room15592); ?>" data-floor="<?php echo htmlspecialchars($floor15592); ?>" data-image="<?php echo base64_encode($upload_img15592); ?>" data-status="<?php echo htmlspecialchars($status15592); ?>" data-category="<?php echo htmlspecialchars($category15592); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15592); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15592); ?>; 
    position:absolute; top:91px; left:852px;'>
                        </div>

                        <!-- ASSET 15593-->
                        <img src='../image.php?id=15593' style='width:18px; cursor:pointer; position:absolute; top:100px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15593' onclick='fetchAssetData(15593);' class="asset-image" data-id="<?php echo $assetId15593; ?>" data-room="<?php echo htmlspecialchars($room15593); ?>" data-floor="<?php echo htmlspecialchars($floor15593); ?>" data-image="<?php echo base64_encode($upload_img15593); ?>" data-status="<?php echo htmlspecialchars($status15593); ?>" data-category="<?php echo htmlspecialchars($category15593); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15593); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15593); ?>; 
    position:absolute; top:104px; left:852px;'>
                        </div>

                        <!-- ASSET 15594 -->
                        <img src='../image.php?id=15594' style='width:18px; cursor:pointer; position:absolute; top:113px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15594' onclick='fetchAssetData(15594);' class="asset-image" data-id="<?php echo $assetId15594; ?>" data-room="<?php echo htmlspecialchars($room15594); ?>" data-floor="<?php echo htmlspecialchars($floor15594); ?>" data-image="<?php echo base64_encode($upload_img15594); ?>" data-status="<?php echo htmlspecialchars($status15594); ?>" data-category="<?php echo htmlspecialchars($category15594); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15594); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15594); ?>; 
    position:absolute; top:117px; left:852px;'>
                        </div>

                        <!-- ASSET 15595 -->
                        <img src='../image.php?id=15595' style='width:18px; cursor:pointer; position:absolute; top:127px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15595' onclick='fetchAssetData(15595);' class="asset-image" data-id="<?php echo $assetId15595; ?>" data-room="<?php echo htmlspecialchars($room15595); ?>" data-floor="<?php echo htmlspecialchars($floor15595); ?>" data-image="<?php echo base64_encode($upload_img15595); ?>" data-status="<?php echo htmlspecialchars($status15595); ?>" data-category="<?php echo htmlspecialchars($category15595); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15595); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15595); ?>; 
    position:absolute; top:130px; left:852px;'>
                        </div>

                        <!-- ASSET 15596 -->
                        <img src='../image.php?id=15596' style='width:18px; cursor:pointer; position:absolute; top:74px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15596' onclick='fetchAssetData(15596);' class="asset-image" data-id="<?php echo $assetId15596; ?>" data-room="<?php echo htmlspecialchars($room15596); ?>" data-floor="<?php echo htmlspecialchars($floor15596); ?>" data-status="<?php echo htmlspecialchars($status15596); ?>" data-image="<?php echo base64_encode($upload_img15596); ?>" data-category="<?php echo htmlspecialchars($category15596); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15596); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15596); ?>; 
    position:absolute; top:79px; left:875px;'>
                        </div>

                        <!-- ASSET 15597 -->
                        <img src='../image.php?id=15597' style='width:18px; cursor:pointer; position:absolute; top:87px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15597' onclick='fetchAssetData(15597);' class="asset-image" data-id="<?php echo $assetId15597; ?>" data-room="<?php echo htmlspecialchars($room15597); ?>" data-floor="<?php echo htmlspecialchars($floor15597); ?>" data-image="<?php echo base64_encode($upload_img15597); ?>" data-category="<?php echo htmlspecialchars($category15597); ?>" data-status="<?php echo htmlspecialchars($status15597); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15597); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15597); ?>; 
    position:absolute; top:91px; left:875px;'>
                        </div>

                        <!-- ASSET 15598 -->
                        <img src='../image.php?id=15598' style='width:18px; cursor:pointer; position:absolute; top:100px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15598' onclick='fetchAssetData(15598);' class="asset-image" data-id="<?php echo $assetId15598; ?>" data-room="<?php echo htmlspecialchars($room15598); ?>" data-floor="<?php echo htmlspecialchars($floor15598); ?>" data-image="<?php echo base64_encode($upload_img15598); ?>" data-category="<?php echo htmlspecialchars($category15598); ?>" data-status="<?php echo htmlspecialchars($status15598); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15598); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15598); ?>; 
    position:absolute; top:104px; left:875px;'>
                        </div>

                        <!-- ASSET 15599 -->
                        <img src='../image.php?id=15599' style='width:18px; cursor:pointer; position:absolute; top:113px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15599' onclick='fetchAssetData(15599);' class="asset-image" data-id="<?php echo $assetId15599; ?>" data-room="<?php echo htmlspecialchars($room15599); ?>" data-floor="<?php echo htmlspecialchars($floor15599); ?>" data-status="<?php echo htmlspecialchars($status15599); ?>" data-image="<?php echo base64_encode($upload_img15599); ?>" data-category="<?php echo htmlspecialchars($category15599); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15599); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15599); ?>; 
    position:absolute; top:117px; left:875px;'>
                        </div>


                        <!-- ASSET 15600 -->
                        <img src='../image.php?id=15600' style='width:18px; cursor:pointer; position:absolute; top:126px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15600' onclick='fetchAssetData(15600);' class="asset-image" data-id="<?php echo $assetId15600; ?>" data-room="<?php echo htmlspecialchars($room15600); ?>" data-status="<?php echo htmlspecialchars($status15600); ?>" data-floor="<?php echo htmlspecialchars($floor15600); ?>" data-image="<?php echo base64_encode($upload_img15600); ?>" data-category="<?php echo htmlspecialchars($category15600); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15600); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15600); ?>; 
    position:absolute; top:130px; left:875px;'>
                        </div>

                        <!-- ASSET 15601 -->
                        <img src='../image.php?id=15601' style='width:18px; cursor:pointer; position:absolute; top:155px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15601' onclick='fetchAssetData(15601);' class="asset-image" data-id="<?php echo $assetId15601; ?>" data-room="<?php echo htmlspecialchars($room15601); ?>" data-floor="<?php echo htmlspecialchars($floor15601); ?>" data-status="<?php echo htmlspecialchars($status15601); ?>" data-image="<?php echo base64_encode($upload_img15601); ?>" data-category="<?php echo htmlspecialchars($category15601); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15601); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15601); ?>; 
    position:absolute; top:159px; left:783px;'>
                        </div>

                        <!-- ASSET 15602 -->
                        <img src='../image.php?id=15602' style='width:18px; cursor:pointer; position:absolute; top:167px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15602' onclick='fetchAssetData(15602);' class="asset-image" data-id="<?php echo $assetId15602; ?>" data-room="<?php echo htmlspecialchars($room15602); ?>" data-floor="<?php echo htmlspecialchars($floor15602); ?>" data-status="<?php echo htmlspecialchars($status15602); ?>" data-image="<?php echo base64_encode($upload_img15602); ?>" data-category="<?php echo htmlspecialchars($category15602); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15602); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15602); ?>; 
    position:absolute; top:171px; left:783px;'>
                        </div>

                        <!-- ASSET 15603 -->
                        <img src='../image.php?id=15603' style='width:18px; cursor:pointer; position:absolute; top:179px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15603' onclick='fetchAssetData(15603);' class="asset-image" data-id="<?php echo $assetId15603; ?>" data-room="<?php echo htmlspecialchars($room15603); ?>" data-floor="<?php echo htmlspecialchars($floor15603); ?>" data-image="<?php echo base64_encode($upload_img15603); ?>" data-status="<?php echo htmlspecialchars($status15603); ?>" data-category="<?php echo htmlspecialchars($category15603); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15603); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15603); ?>; 
    position:absolute; top:183px; left:783px;'>
                        </div>


                        <!-- ASSET 15604 -->
                        <img src='../image.php?id=15604' style='width:18px; cursor:pointer; position:absolute; top:191px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15604' onclick='fetchAssetData(15604);' class="asset-image" data-id="<?php echo $assetId15604; ?>" data-room="<?php echo htmlspecialchars($room15604); ?>" data-floor="<?php echo htmlspecialchars($floor15604); ?>" data-image="<?php echo base64_encode($upload_img15604); ?>" data-status="<?php echo htmlspecialchars($status15604); ?>" data-category="<?php echo htmlspecialchars($category15604); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15604); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15604); ?>; 
    position:absolute; top:194px; left:783px;'>
                        </div>

                        <!-- ASSET 15605 -->
                        <img src='../image.php?id=15605' style='width:18px; cursor:pointer; position:absolute; top:203px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15605' onclick='fetchAssetData(15605);' class="asset-image" data-id="<?php echo $assetId15605; ?>" data-room="<?php echo htmlspecialchars($room15605); ?>" data-floor="<?php echo htmlspecialchars($floor15605); ?>" data-image="<?php echo base64_encode($upload_img15605); ?>" data-category="<?php echo htmlspecialchars($category15605); ?>" data-status="<?php echo htmlspecialchars($status15605); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15605); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15605); ?>; 
    position:absolute; top:207px; left:783px;'>
                        </div>

                        <!-- ASSET 15606 -->
                        <img src='../image.php?id=15606' style='width:18px; cursor:pointer; position:absolute; top:155px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15606' onclick='fetchAssetData(15606);' class="asset-image" data-id="<?php echo $assetId15606; ?>" data-room="<?php echo htmlspecialchars($room15606); ?>" data-floor="<?php echo htmlspecialchars($floor15606); ?>" data-image="<?php echo base64_encode($upload_img15606); ?>" data-category="<?php echo htmlspecialchars($category15606); ?>" data-status="<?php echo htmlspecialchars($status15606); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15606); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15606); ?>; 
    position:absolute; top:159px; left:806px;'>
                        </div>

                        <!-- ASSET 15607 -->
                        <img src='../image.php?id=15607' style='width:18px; cursor:pointer; position:absolute; top:167px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15607' onclick='fetchAssetData(15607);' class="asset-image" data-id="<?php echo $assetId15607; ?>" data-room="<?php echo htmlspecialchars($room15607); ?>" data-floor="<?php echo htmlspecialchars($floor15607); ?>" data-image="<?php echo base64_encode($upload_img15607); ?>" data-category="<?php echo htmlspecialchars($category15607); ?>" data-status="<?php echo htmlspecialchars($status15607); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15607); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15607); ?>; 
    position:absolute; top:171px; left:806px;'>
                        </div>

                        <!-- ASSET 15608 -->
                        <img src='../image.php?id=15608' style='width:18px; cursor:pointer; position:absolute; top:179px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15608' onclick='fetchAssetData(15608);' class="asset-image" data-id="<?php echo $assetId15608; ?>" data-room="<?php echo htmlspecialchars($room15608); ?>" data-floor="<?php echo htmlspecialchars($floor15608); ?>" data-image="<?php echo base64_encode($upload_img15608); ?>" data-category="<?php echo htmlspecialchars($category15608); ?>" data-status="<?php echo htmlspecialchars($status15608); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15608); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15608); ?>; 
    position:absolute; top:183px; left:806px;'>
                        </div>

                        <!-- ASSET 15609 -->
                        <img src='../image.php?id=15609' style='width:18px; cursor:pointer; position:absolute; top:191px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15609' onclick='fetchAssetData(15609);' class="asset-image" data-id="<?php echo $assetId15609; ?>" data-room="<?php echo htmlspecialchars($room15609); ?>" data-floor="<?php echo htmlspecialchars($floor15609); ?>" data-image="<?php echo base64_encode($upload_img15609); ?>" data-status="<?php echo htmlspecialchars($status15609); ?>" data-category="<?php echo htmlspecialchars($category15609); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15609); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15609); ?>; 
    position:absolute; top:195px; left:806px;'>
                        </div>

                        <!-- ASSET 15610 -->
                        <img src='../image.php?id=15610' style='width:18px; cursor:pointer; position:absolute; top:203px; left:793px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15610' onclick='fetchAssetData(15610);' class="asset-image" data-id="<?php echo $assetId15610; ?>" data-room="<?php echo htmlspecialchars($room15610); ?>" data-floor="<?php echo htmlspecialchars($floor15610); ?>" data-image="<?php echo base64_encode($upload_img15610); ?>" data-category="<?php echo htmlspecialchars($category15610); ?>" data-status="<?php echo htmlspecialchars($status15610); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15610); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15610); ?>; 
    position:absolute; top:207px; left:806px;'>
                        </div>

                        <!-- ASSET 15611 -->
                        <img src='../image.php?id=15611' style='width:18px; cursor:pointer; position:absolute; top:155px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15611' onclick='fetchAssetData(15611);' class="asset-image" data-id="<?php echo $assetId15611; ?>" data-room="<?php echo htmlspecialchars($room15611); ?>" data-floor="<?php echo htmlspecialchars($floor15611); ?>" data-image="<?php echo base64_encode($upload_img15611); ?>" data-category="<?php echo htmlspecialchars($category15611); ?>" data-status="<?php echo htmlspecialchars($status15611); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15611); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15611); ?>; 
    position:absolute; top:159px; left:829px;'>
                        </div>


                        <!-- ASSET 15612 -->
                        <img src='../image.php?id=15612' style='width:18px; cursor:pointer; position:absolute; top:167px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15612' onclick='fetchAssetData(15612);' class="asset-image" data-id="<?php echo $assetId15612; ?>" data-room="<?php echo htmlspecialchars($room15612); ?>" data-floor="<?php echo htmlspecialchars($floor15612); ?>" data-image="<?php echo base64_encode($upload_img15612); ?>" data-category="<?php echo htmlspecialchars($category15612); ?>" data-status="<?php echo htmlspecialchars($status15612); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15612); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15612); ?>; 
    position:absolute; top:171px; left:829px;'>
                        </div>

                        <!-- ASSET 15613 -->
                        <img src='../image.php?id=15613' style='width:18px; cursor:pointer; position:absolute; top:179px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15613' onclick='fetchAssetData(15613);' class="asset-image" data-id="<?php echo $assetId15613; ?>" data-room="<?php echo htmlspecialchars($room15613); ?>" data-floor="<?php echo htmlspecialchars($floor15613); ?>" data-image="<?php echo base64_encode($upload_img15613); ?>" data-category="<?php echo htmlspecialchars($category15613); ?>" data-status="<?php echo htmlspecialchars($status15613); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15613); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15613); ?>; 
    position:absolute; top:183px; left:829px;'>
                        </div>

                        <!-- ASSET 15614 -->
                        <img src='../image.php?id=15614' style='width:18px; cursor:pointer; position:absolute; top:191px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15614' onclick='fetchAssetData(15614);' class="asset-image" data-id="<?php echo $assetId15614; ?>" data-room="<?php echo htmlspecialchars($room15614); ?>" data-floor="<?php echo htmlspecialchars($floor15614); ?>" data-image="<?php echo base64_encode($upload_img15614); ?>" data-status="<?php echo htmlspecialchars($status15614); ?>" data-category="<?php echo htmlspecialchars($category15614); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15614); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15614); ?>; 
    position:absolute; top:194px; left:829px;'>
                        </div>

                        <!-- ASSET 15615 -->
                        <img src='../image.php?id=15615' style='width:18px; cursor:pointer; position:absolute; top:203px; left:816px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15615' onclick='fetchAssetData(15615);' class="asset-image" data-id="<?php echo $assetId15615; ?>" data-room="<?php echo htmlspecialchars($room15615); ?>" data-floor="<?php echo htmlspecialchars($floor15615); ?>" data-image="<?php echo base64_encode($upload_img15615); ?>" data-status="<?php echo htmlspecialchars($status15615); ?>" data-category="<?php echo htmlspecialchars($category15615); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15615); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15615); ?>; 
    position:absolute; top:207px; left:829px;'>
                        </div>

                        <!-- ASSET 15616 -->
                        <img src='../image.php?id=15616' style='width:18px; cursor:pointer; position:absolute; top:155px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15616' onclick='fetchAssetData(15616);' class="asset-image" data-id="<?php echo $assetId15616; ?>" data-room="<?php echo htmlspecialchars($room15616); ?>" data-floor="<?php echo htmlspecialchars($floor15616); ?>" data-status="<?php echo htmlspecialchars($status15616); ?>" data-image="<?php echo base64_encode($upload_img15616); ?>" data-category="<?php echo htmlspecialchars($category15616); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15616); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15616); ?>; 
    position:absolute; top:159px; left:852px;'>
                        </div>

                        <!-- ASSET 15617 -->
                        <img src='../image.php?id=15617' style='width:18px; cursor:pointer; position:absolute; top:167px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15617' onclick='fetchAssetData(15617);' class="asset-image" data-id="<?php echo $assetId15617; ?>" data-room="<?php echo htmlspecialchars($room15617); ?>" data-floor="<?php echo htmlspecialchars($floor15617); ?>" data-status="<?php echo htmlspecialchars($status15617); ?>" data-image="<?php echo base64_encode($upload_img15617); ?>" data-category="<?php echo htmlspecialchars($category15617); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15617); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15617); ?>; 
    position:absolute; top:171px; left:852px;'>
                        </div>

                        <!-- ASSET 15618 -->
                        <img src='../image.php?id=15618' style='width:18px; cursor:pointer; position:absolute; top:179px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15618' onclick='fetchAssetData(15618);' class="asset-image" data-id="<?php echo $assetId15618; ?>" data-room="<?php echo htmlspecialchars($room15618); ?>" data-floor="<?php echo htmlspecialchars($floor15618); ?>" data-image="<?php echo base64_encode($upload_img15618); ?>" data-status="<?php echo htmlspecialchars($status15618); ?>" data-category="<?php echo htmlspecialchars($category15618); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15618); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15618); ?>; 
    position:absolute; top:183px; left:852px;'>
                        </div>

                        <!-- ASSET 15619 -->
                        <img src='../image.php?id=15619' style='width:18px; cursor:pointer; position:absolute; top:191px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15619' onclick='fetchAssetData(15619);' class="asset-image" data-id="<?php echo $assetId15619; ?>" data-room="<?php echo htmlspecialchars($room15619); ?>" data-floor="<?php echo htmlspecialchars($floor15619); ?>" data-image="<?php echo base64_encode($upload_img15619); ?>" data-category="<?php echo htmlspecialchars($category15619); ?>" data-status="<?php echo htmlspecialchars($status15619); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15619); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15619); ?>; 
    position:absolute; top:194px; left:852px;'>
                        </div>


                        <!-- ASSET 15620 -->
                        <img src='../image.php?id=15620' style='width:18px; cursor:pointer; position:absolute; top:203px; left:839px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15620' onclick='fetchAssetData(15620);' class="asset-image" data-id="<?php echo $assetId15620; ?>" data-room="<?php echo htmlspecialchars($room15620); ?>" data-floor="<?php echo htmlspecialchars($floor15620); ?>" data-image="<?php echo base64_encode($upload_img15620); ?>" data-category="<?php echo htmlspecialchars($category15620); ?>" data-status="<?php echo htmlspecialchars($status15620); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15620); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15620); ?>; 
    position:absolute; top:207px; left:852px;'>
                        </div>

                        <!-- ASSET 15621 -->
                        <img src='../image.php?id=15621' style='width:18px; cursor:pointer; position:absolute; top:155px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15621' onclick='fetchAssetData(15621);' class="asset-image" data-id="<?php echo $assetId15621; ?>" data-room="<?php echo htmlspecialchars($room15621); ?>" data-floor="<?php echo htmlspecialchars($floor15621); ?>" data-image="<?php echo base64_encode($upload_img15621); ?>" data-category="<?php echo htmlspecialchars($category15621); ?>" data-status="<?php echo htmlspecialchars($status15621); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15621); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15621); ?>; 
    position:absolute; top:159px; left:875px;'>
                        </div>

                        <!-- ASSET 15622 -->
                        <img src='../image.php?id=15622' style='width:18px; cursor:pointer; position:absolute; top:167px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15622' onclick='fetchAssetData(15622);' class="asset-image" data-id="<?php echo $assetId15622; ?>" data-room="<?php echo htmlspecialchars($room15622); ?>" data-floor="<?php echo htmlspecialchars($floor15622); ?>" data-image="<?php echo base64_encode($upload_img15622); ?>" data-status="<?php echo htmlspecialchars($status15622); ?>" data-category="<?php echo htmlspecialchars($category15622); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15622); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15622); ?>; 
    position:absolute; top:171px; left:875px;'>
                        </div>

                        <!-- ASSET 15623 -->
                        <img src='../image.php?id=15623' style='width:18px; cursor:pointer; position:absolute; top:179px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15623' onclick='fetchAssetData(15623);' class="asset-image" data-id="<?php echo $assetId15623; ?>" data-room="<?php echo htmlspecialchars($room15623); ?>" data-floor="<?php echo htmlspecialchars($floor15623); ?>" data-image="<?php echo base64_encode($upload_img15623); ?>" data-status="<?php echo htmlspecialchars($status15623); ?>" data-category="<?php echo htmlspecialchars($category15623); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15623); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15623); ?>; 
    position:absolute; top:183px; left:875px;'>
                        </div>

                        <!-- ASSET 15624 -->
                        <img src='../image.php?id=15624' style='width:18px; cursor:pointer; position:absolute; top:191px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15624' onclick='fetchAssetData(15624);' class="asset-image" data-id="<?php echo $assetId15624; ?>" data-room="<?php echo htmlspecialchars($room15624); ?>" data-floor="<?php echo htmlspecialchars($floor15624); ?>" data-image="<?php echo base64_encode($upload_img15624); ?>" data-status="<?php echo htmlspecialchars($status15624); ?>" data-category="<?php echo htmlspecialchars($category15624); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15624); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15624); ?>; 
    position:absolute; top:194px; left:875px;'>
                        </div>

                        <!-- ASSET 15625 -->
                        <img src='../image.php?id=15625' style='width:18px; cursor:pointer; position:absolute; top:203px; left:862px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15625' onclick='fetchAssetData(15625);' class="asset-image" data-id="<?php echo $assetId15625; ?>" data-room="<?php echo htmlspecialchars($room15625); ?>" data-floor="<?php echo htmlspecialchars($floor15625); ?>" data-image="<?php echo base64_encode($upload_img15625); ?>" data-status="<?php echo htmlspecialchars($status15625); ?>" data-category="<?php echo htmlspecialchars($category15625); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15625); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15625); ?>; 
    position:absolute; top:207px; left:875px;'>
                        </div>

                        <!-- ASSET 15430 -->
                        <img src='../image.php?id=15430' style='width:18px; cursor:pointer; position:absolute; top:144px; left:715px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15430' onclick='fetchAssetData(15430);' class="asset-image" data-id="<?php echo $assetId15430; ?>" data-room="<?php echo htmlspecialchars($room15430); ?>" data-floor="<?php echo htmlspecialchars($floor15430); ?>" data-image="<?php echo base64_encode($upload_img15430); ?>" data-status="<?php echo htmlspecialchars($status15430); ?>" data-category="<?php echo htmlspecialchars($category15430); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15430); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15430); ?>; 
    position:absolute; top:142px; left:719px;'>
                        </div>

                        <!-- ASSET 15628 -->
                        <img src='../image.php?id=15628' style='width:15px; cursor:pointer; position:absolute; top:70px; left:1025px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15628' onclick='fetchAssetData(15628);' class="asset-image" data-id="<?php echo $assetId15628; ?>" data-room="<?php echo htmlspecialchars($room15628); ?>" data-floor="<?php echo htmlspecialchars($floor15628); ?>" data-image="<?php echo base64_encode($upload_img15628); ?>" data-status="<?php echo htmlspecialchars($status15628); ?>" data-category="<?php echo htmlspecialchars($category15628); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15628); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15628); ?>; 
    position:absolute; top:70px; left:1035px;'>
                        </div>


                        <!-- ASSET 15629 -->
                        <img src='../image.php?id=15629' style='width:15px; cursor:pointer; position:absolute; top:195px; left:1025px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15629' onclick='fetchAssetData(15629);' class="asset-image" data-id="<?php echo $assetId15629; ?>" data-room="<?php echo htmlspecialchars($room15629); ?>" data-floor="<?php echo htmlspecialchars($floor15629); ?>" data-image="<?php echo base64_encode($upload_img15629); ?>" data-status="<?php echo htmlspecialchars($status15629); ?>" data-category="<?php echo htmlspecialchars($category15629); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15629); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15629); ?>; 
    position:absolute; top:195px; left:1035px;'>
                        </div>

                        <!-- ASSET 15630 -->
                        <img src='../image.php?id=15630' style='width:15px; cursor:pointer; position:absolute; top:70px; left:1130px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15630' onclick='fetchAssetData(15630);' class="asset-image" data-id="<?php echo $assetId15630; ?>" data-room="<?php echo htmlspecialchars($room15630); ?>" data-floor="<?php echo htmlspecialchars($floor15630); ?>" data-image="<?php echo base64_encode($upload_img15630); ?>" data-status="<?php echo htmlspecialchars($status15630); ?>" data-category="<?php echo htmlspecialchars($category15630); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15630); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15630); ?>; 
    position:absolute; top:70px; left:1140px;'>
                        </div>

                        <!-- ASSET 15631 -->
                        <img src='../image.php?id=15631' style='width:15px; cursor:pointer; position:absolute; top:195px; left:1130px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15631' onclick='fetchAssetData(15631);' class="asset-image" data-id="<?php echo $assetId15631; ?>" data-room="<?php echo htmlspecialchars($room15631); ?>" data-floor="<?php echo htmlspecialchars($floor15631); ?>" data-image="<?php echo base64_encode($upload_img15631); ?>" data-category="<?php echo htmlspecialchars($category15631); ?>" data-status="<?php echo htmlspecialchars($status15631); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15631); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15631); ?>; 
    position:absolute; top:195px; left:1140px;'>
                        </div>

                        <!-- ASSET 15632 -->
                        <img src='../image.php?id=15632' style='width:15px; cursor:pointer; position:absolute; top:355px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15632' onclick='fetchAssetData(15632);' class="asset-image" data-id="<?php echo $assetId15632; ?>" data-room="<?php echo htmlspecialchars($room15632); ?>" data-floor="<?php echo htmlspecialchars($floor15632); ?>" data-image="<?php echo base64_encode($upload_img15632); ?>" data-category="<?php echo htmlspecialchars($category15632); ?>" data-status="<?php echo htmlspecialchars($status15632); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15632); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15632); ?>; 
    position:absolute; top:355px; left:230px;'>
                        </div>


                        <!-- ASSET 15633 -->
                        <img src='../image.php?id=15633' style='width:15px; cursor:pointer; position:absolute; top:430px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15633' onclick='fetchAssetData(15633);' class="asset-image" data-id="<?php echo $assetId15633; ?>" data-room="<?php echo htmlspecialchars($room15633); ?>" data-floor="<?php echo htmlspecialchars($floor15633); ?>" data-image="<?php echo base64_encode($upload_img15694); ?>" data-category="<?php echo htmlspecialchars($category15694); ?>" data-status="<?php echo htmlspecialchars($status15694); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15694); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15694); ?>; 
    position:absolute; top:430px; left:230px;'>
                        </div>

                        <!-- ASSET 15640 -->
                        <img src='../image.php?id=15640' style='width:15px; cursor:pointer; position:absolute; top:355px; left:317px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal1515640' onclick='fetchAssetData(1515640);' class="asset-image" data-id="<?php echo $assetId1515640; ?>" data-room="<?php echo htmlspecialchars($room1515640); ?>" data-floor="<?php echo htmlspecialchars($floor1515640); ?>" data-image="<?php echo base64_encode($upload_img1515640); ?>" data-status="<?php echo htmlspecialchars($status1515640); ?>" data-category="<?php echo htmlspecialchars($category1515640); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName1515640); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status1515640); ?>; 
    position:absolute; top:355px; left:327px;'>
                        </div>

                        <!-- ASSET 15635 -->
                        <img src='../image.php?id=15635' style='width:15px; cursor:pointer; position:absolute; top:430px; left:317px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15635' onclick='fetchAssetData(15635);' class="asset-image" data-id="<?php echo $assetId15635; ?>" data-room="<?php echo htmlspecialchars($room15635); ?>" data-floor="<?php echo htmlspecialchars($floor15635); ?>" data-image="<?php echo base64_encode($upload_img15635); ?>" data-status="<?php echo htmlspecialchars($status15635); ?>" data-category="<?php echo htmlspecialchars($category15635); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15635); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15635); ?>; 
    position:absolute; top:430px; left:327px;'>
                        </div>

                        <!-- ASSET 15636 -->
                        <img src='../image.php?id=15636' style='width:15px; cursor:pointer; position:absolute; top:430px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15636' onclick='fetchAssetData(15636);' class="asset-image" data-id="<?php echo $assetId15636; ?>" data-room="<?php echo htmlspecialchars($room15636); ?>" data-floor="<?php echo htmlspecialchars($floor15636); ?>" data-image="<?php echo base64_encode($upload_img15636); ?>" data-status="<?php echo htmlspecialchars($status15636); ?>" data-category="<?php echo htmlspecialchars($category15636); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15636); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15636); ?>; 
    position:absolute; top:430px; left:465px;'>
                        </div>


                        <!-- ASSET 15639 -->
                        <img src='../image.php?id=15639' style='width:15px; cursor:pointer; position:absolute; top:355px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15639' onclick='fetchAssetData(15639);' class="asset-image" data-id="<?php echo $assetId15639; ?>" data-room="<?php echo htmlspecialchars($room15639); ?>" data-floor="<?php echo htmlspecialchars($floor15639); ?>" data-image="<?php echo base64_encode($upload_img15639); ?>" data-status="<?php echo htmlspecialchars($status15639); ?>" data-category="<?php echo htmlspecialchars($category15639); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15639); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15639); ?>; 
    position:absolute; top:355px; left:465px;'>
                        </div>

                        <!-- ASSET 6992 -->
                        <img src='../image.php?id=6992' style='width:18px; cursor:pointer; position:absolute; top:426px; left:230px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6992' onclick='fetchAssetData(6992);' class="asset-image" data-id="<?php echo $assetId6992; ?>" data-room="<?php echo htmlspecialchars($room6992); ?>" data-floor="<?php echo htmlspecialchars($floor6992); ?>" data-image="<?php echo base64_encode($upload_img6992); ?>" data-status="<?php echo htmlspecialchars($status6992); ?>" data-category="<?php echo htmlspecialchars($category6992); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName6992); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status6992); ?>; 
    position:absolute; top:426px; left:240px;'>
                        </div>

                        <!-- ASSET 15692 -->
                        <img src='../image.php?id=15692' style='width:15px; cursor:pointer; position:absolute; top:426px; left:250px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15692' onclick='fetchAssetData(15692);' class="asset-image" data-id="<?php echo $assetId15692; ?>" data-room="<?php echo htmlspecialchars($room15692); ?>" data-floor="<?php echo htmlspecialchars($floor15692); ?>" data-image="<?php echo base64_encode($upload_img15692); ?>" data-category="<?php echo htmlspecialchars($category15692); ?>" data-status="<?php echo htmlspecialchars($status15692); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15692); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15692); ?>; 
    position:absolute; top:415px; left:254px;'>
                        </div>

                        <!-- ASSET 15634 -->
                        <img src='../image.php?id=15634' style='width:15px; cursor:pointer; position:absolute; top:515px; left:220px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15634' onclick='fetchAssetData(15634);' class="asset-image" data-id="<?php echo $assetId15634; ?>" data-room="<?php echo htmlspecialchars($room15634); ?>" data-floor="<?php echo htmlspecialchars($floor15634); ?>" data-image="<?php echo base64_encode($upload_img15634); ?>" data-status="<?php echo htmlspecialchars($status15634); ?>" data-category="<?php echo htmlspecialchars($category15634); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15634); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15634); ?>; 
    position:absolute; top:515px; left:230px;'>
                        </div>


                        <!-- ASSET 15638 -->
                        <img src='../image.php?id=15638' style='width:15px; cursor:pointer; position:absolute; top:515px; left:317px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15638' onclick='fetchAssetData(15638);' class="asset-image" data-id="<?php echo $assetId15638; ?>" data-room="<?php echo htmlspecialchars($room15638); ?>" data-floor="<?php echo htmlspecialchars($floor15638); ?>" data-image="<?php echo base64_encode($upload_img15638); ?>" data-status="<?php echo htmlspecialchars($status15638); ?>" data-category="<?php echo htmlspecialchars($category15638); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15638); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15638); ?>; 
    position:absolute; top:515px; left:327px;'>
                        </div>

                        <!-- ASSET 15640 -->
                        <img src='../image.php?id=15640' style='width:15px; cursor:pointer; position:absolute; top:515px; left:455px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15640' onclick='fetchAssetData(15640);' class="asset-image" data-id="<?php echo $assetId15640; ?>" data-room="<?php echo htmlspecialchars($room15640); ?>" data-floor="<?php echo htmlspecialchars($floor15640); ?>" data-status="<?php echo htmlspecialchars($status15640); ?>" data-image="<?php echo base64_encode($upload_img15640); ?>" data-category="<?php echo htmlspecialchars($category15640); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15640); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15640); ?>; 
    position:absolute; top:515px; left:465px;'>
                        </div>

                        <!-- ASSET 15641 -->
                        <img src='../image.php?id=15641' style='width:18px; cursor:pointer; position:absolute; top:360px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15641' onclick='fetchAssetData(15641);' class="asset-image" data-id="<?php echo $assetId15641; ?>" data-room="<?php echo htmlspecialchars($room15641); ?>" data-floor="<?php echo htmlspecialchars($floor15641); ?>" data-image="<?php echo base64_encode($upload_img15641); ?>" data-status="<?php echo htmlspecialchars($status15641); ?>" data-category="<?php echo htmlspecialchars($category15641); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15641); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15641); ?>; 
    position:absolute; top:366px; left:331px;'>
                        </div>

                        <!-- ASSET 15642 -->
                        <img src='../image.php?id=15642' style='width:18px; cursor:pointer; position:absolute; top:373px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15642' onclick='fetchAssetData(15642);' class="asset-image" data-id="<?php echo $assetId15642; ?>" data-room="<?php echo htmlspecialchars($room15642); ?>" data-floor="<?php echo htmlspecialchars($floor15642); ?>" data-image="<?php echo base64_encode($upload_img15642); ?>" data-status="<?php echo htmlspecialchars($status15642); ?>" data-category="<?php echo htmlspecialchars($category15642); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15642); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15642); ?>; 
    position:absolute; top:383px; left:331px;'>
                        </div>


                        <!-- ASSET 15643 -->
                        <img src='../image.php?id=15643' style='width:18px; cursor:pointer; position:absolute; top:386px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15643' onclick='fetchAssetData(15643);' class="asset-image" data-id="<?php echo $assetId15643; ?>" data-room="<?php echo htmlspecialchars($room15643); ?>" data-floor="<?php echo htmlspecialchars($floor15643); ?>" data-image="<?php echo base64_encode($upload_img15643); ?>" data-category="<?php echo htmlspecialchars($category15643); ?>" data-status="<?php echo htmlspecialchars($status15643); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15643); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15643); ?>; 
    position:absolute; top:396px; left:331px;'>
                        </div>

                        <!-- ASSET 15644 -->
                        <img src='../image.php?id=15644' style='width:18px; cursor:pointer; position:absolute; top:399px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15644' onclick='fetchAssetData(15644);' class="asset-image" data-id="<?php echo $assetId15644; ?>" data-room="<?php echo htmlspecialchars($room15644); ?>" data-floor="<?php echo htmlspecialchars($floor15644); ?>" data-image="<?php echo base64_encode($upload_img15644); ?>" data-category="<?php echo htmlspecialchars($category15644); ?>" data-status="<?php echo htmlspecialchars($status15644); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15644); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15644); ?>; 
    position:absolute; top:409px; left:331px;'>
                        </div>

                        <!-- ASSET 15645 -->
                        <img src='../image.php?id=15645' style='width:18px; cursor:pointer; position:absolute; top:412px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15645' onclick='fetchAssetData(15645);' class="asset-image" data-id="<?php echo $assetId15645; ?>" data-room="<?php echo htmlspecialchars($room15645); ?>" data-floor="<?php echo htmlspecialchars($floor15645); ?>" data-image="<?php echo base64_encode($upload_img15645); ?>" data-category="<?php echo htmlspecialchars($category15645); ?>" data-status="<?php echo htmlspecialchars($status15645); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15645); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15645); ?>; 
    position:absolute; top:422px; left:331px;'>
                        </div>

                        <!-- ASSET 15646 -->
                        <img src='../image.php?id=15646' style='width:18px; cursor:pointer; position:absolute; top:360px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15646' onclick='fetchAssetData(15646);' class="asset-image" data-id="<?php echo $assetId15646; ?>" data-room="<?php echo htmlspecialchars($room15646); ?>" data-floor="<?php echo htmlspecialchars($floor15646); ?>" data-image="<?php echo base64_encode($upload_img15646); ?>" data-category="<?php echo htmlspecialchars($category15646); ?>" data-status="<?php echo htmlspecialchars($status15646); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15646); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15646); ?>; 
    position:absolute; top:370px; left:354px;'>
                        </div>


                        <!-- ASSET 15647 -->
                        <img src='../image.php?id=15647' style='width:18px; cursor:pointer; position:absolute; top:373px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15647' onclick='fetchAssetData(15647);' class="asset-image" data-id="<?php echo $assetId15647; ?>" data-room="<?php echo htmlspecialchars($room15647); ?>" data-floor="<?php echo htmlspecialchars($floor15647); ?>" data-image="<?php echo base64_encode($upload_img15647); ?>" data-category="<?php echo htmlspecialchars($category15647); ?>" data-status="<?php echo htmlspecialchars($status15647); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15647); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15647); ?>; 
    position:absolute; top:383px; left:354px;'>
                        </div>

                        <!-- ASSET 15648 -->
                        <img src='../image.php?id=15648' style='width:18px; cursor:pointer; position:absolute; top:386px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15648' onclick='fetchAssetData(15648);' class="asset-image" data-id="<?php echo $assetId15648; ?>" data-room="<?php echo htmlspecialchars($room15648); ?>" data-floor="<?php echo htmlspecialchars($floor15648); ?>" data-image="<?php echo base64_encode($upload_img15648); ?>" data-category="<?php echo htmlspecialchars($category15648); ?>" data-status="<?php echo htmlspecialchars($status15648); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15648); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15648); ?>; 
    position:absolute; top:396px; left:354px;'>
                        </div>

                        <!-- ASSET 15649 -->
                        <img src='../image.php?id=15649' style='width:18px; cursor:pointer; position:absolute; top:399px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15649' onclick='fetchAssetData(15649);' class="asset-image" data-id="<?php echo $assetId15649; ?>" data-room="<?php echo htmlspecialchars($room15649); ?>" data-floor="<?php echo htmlspecialchars($floor15649); ?>" data-image="<?php echo base64_encode($upload_img15649); ?>" data-category="<?php echo htmlspecialchars($category15649); ?>" data-status="<?php echo htmlspecialchars($status15649); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15649); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15649); ?>; 
    position:absolute; top:409px; left:354px;'>
                        </div>

                        <!-- ASSET 15650 -->
                        <img src='../image.php?id=15650' style='width:18px; cursor:pointer; position:absolute; top:412px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15650' onclick='fetchAssetData(15650);' class="asset-image" data-id="<?php echo $assetId15650; ?>" data-room="<?php echo htmlspecialchars($room15650); ?>" data-floor="<?php echo htmlspecialchars($floor15650); ?>" data-image="<?php echo base64_encode($upload_img15650); ?>" data-category="<?php echo htmlspecialchars($category15650); ?>" data-status="<?php echo htmlspecialchars($status15650); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15650); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15650); ?>; 
    position:absolute; top:422px; left:354px;'>
                        </div>

                        <!-- ASSET 15651 -->
                        <img src='../image.php?id=15651' style='width:18px; cursor:pointer; position:absolute; top:360px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15651' onclick='fetchAssetData(15651);' class="asset-image" data-id="<?php echo $assetId15651; ?>" data-room="<?php echo htmlspecialchars($room15651); ?>" data-floor="<?php echo htmlspecialchars($floor15651); ?>" data-image="<?php echo base64_encode($upload_img15651); ?>" data-status="<?php echo htmlspecialchars($status15651); ?>" data-category="<?php echo htmlspecialchars($category15651); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15651); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15651); ?>; 
    position:absolute; top:370px; left:377px;'>
                        </div>

                        <!-- ASSET 15652 -->
                        <img src='../image.php?id=15652' style='width:18px; cursor:pointer; position:absolute; top:373px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15652' onclick='fetchAssetData(15652);' class="asset-image" data-id="<?php echo $assetId15652; ?>" data-room="<?php echo htmlspecialchars($room15652); ?>" data-floor="<?php echo htmlspecialchars($floor15652); ?>" data-image="<?php echo base64_encode($upload_img15652); ?>" data-category="<?php echo htmlspecialchars($category15652); ?>" data-status="<?php echo htmlspecialchars($status15652); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15652); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15652); ?>; 
    position:absolute; top:383px; left:377px;'>
                        </div>

                        <!-- ASSET 15653 -->
                        <img src='../image.php?id=15653' style='width:18px; cursor:pointer; position:absolute; top:386px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15653' onclick='fetchAssetData(15653);' class="asset-image" data-id="<?php echo $assetId15653; ?>" data-room="<?php echo htmlspecialchars($room15653); ?>" data-floor="<?php echo htmlspecialchars($floor15653); ?>" data-image="<?php echo base64_encode($upload_img15653); ?>" data-category="<?php echo htmlspecialchars($category15653); ?>" data-status="<?php echo htmlspecialchars($status15653); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15653); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15653); ?>; 
    position:absolute; top:396px; left:377px;'>
                        </div>

                        <!-- ASSET 15654 -->
                        <img src='../image.php?id=15654' style='width:18px; cursor:pointer; position:absolute; top:399px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15654' onclick='fetchAssetData(15654);' class="asset-image" data-id="<?php echo $assetId15654; ?>" data-room="<?php echo htmlspecialchars($room15654); ?>" data-floor="<?php echo htmlspecialchars($floor15654); ?>" data-image="<?php echo base64_encode($upload_img15654); ?>" data-status="<?php echo htmlspecialchars($status15654); ?>" data-category="<?php echo htmlspecialchars($category15654); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName6450); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15654); ?>; 
    position:absolute; top:409px; left:377px;'>
                        </div>


                        <!-- ASSET 15655 -->
                        <img src='../image.php?id=15655' style='width:18px; cursor:pointer; position:absolute; top:412px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15655' onclick='fetchAssetData(15655);' class="asset-image" data-id="<?php echo $assetId15655; ?>" data-room="<?php echo htmlspecialchars($room15655); ?>" data-floor="<?php echo htmlspecialchars($floor15655); ?>" data-image="<?php echo base64_encode($upload_img15655); ?>" data-status="<?php echo htmlspecialchars($status15655); ?>" data-category="<?php echo htmlspecialchars($category15655); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15655); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15655); ?>; 
    position:absolute; top:422px; left:377px;'>
                        </div>

                        <!-- ASSET 15656 -->
                        <img src='../image.php?id=15656' style='width:18px; cursor:pointer; position:absolute; top:360px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15656' onclick='fetchAssetData(15656);' class="asset-image" data-id="<?php echo $assetId15656; ?>" data-room="<?php echo htmlspecialchars($room15656); ?>" data-floor="<?php echo htmlspecialchars($floor15656); ?>" data-image="<?php echo base64_encode($upload_img15656); ?>" data-status="<?php echo htmlspecialchars($status15656); ?>" data-category="<?php echo htmlspecialchars($category15656); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15656); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15656); ?>; 
    position:absolute; top:370px; left:400px;'>
                        </div>

                        <!-- ASSET 15657 -->
                        <img src='../image.php?id=15657' style='width:18px; cursor:pointer; position:absolute; top:373px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15657' onclick='fetchAssetData(15657);' class="asset-image" data-id="<?php echo $assetId15657; ?>" data-room="<?php echo htmlspecialchars($room15657); ?>" data-floor="<?php echo htmlspecialchars($floor15657); ?>" data-status="<?php echo htmlspecialchars($status15657); ?>" data-image="<?php echo base64_encode($upload_img15657); ?>" data-category="<?php echo htmlspecialchars($category15657); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15657); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15657); ?>; 
    position:absolute; top:383px; left:400px;'>
                        </div>

                        <!-- ASSET 15658 -->
                        <img src='../image.php?id=15658' style='width:18px; cursor:pointer; position:absolute; top:386px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15658' onclick='fetchAssetData(15658);' class="asset-image" data-id="<?php echo $assetId15658; ?>" data-room="<?php echo htmlspecialchars($room15658); ?>" data-floor="<?php echo htmlspecialchars($floor15658); ?>" data-status="<?php echo htmlspecialchars($status15658); ?>" data-image="<?php echo base64_encode($upload_img15658); ?>" data-category="<?php echo htmlspecialchars($category15658); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15658); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15658); ?>; 
    position:absolute; top:396px; left:400px;'>
                        </div>

                        <!-- ASSET 15659 -->
                        <img src='../image.php?id=15659' style='width:18px; cursor:pointer; position:absolute; top:399px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15659' onclick='fetchAssetData(15659);' class="asset-image" data-id="<?php echo $assetId15659; ?>" data-room="<?php echo htmlspecialchars($room15659); ?>" data-floor="<?php echo htmlspecialchars($floor15659); ?>" data-image="<?php echo base64_encode($upload_img15659); ?>" data-status="<?php echo htmlspecialchars($status15659); ?>" data-category="<?php echo htmlspecialchars($category15659); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15659); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15659); ?>; 
    position:absolute; top:409px; left:400px;'>
                        </div>

                        <!-- ASSET 15660 -->
                        <img src='../image.php?id=15660' style='width:18px; cursor:pointer; position:absolute; top:412px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15660' onclick='fetchAssetData(15660);' class="asset-image" data-id="<?php echo $assetId15660; ?>" data-room="<?php echo htmlspecialchars($room15660); ?>" data-floor="<?php echo htmlspecialchars($floor15660); ?>" data-image="<?php echo base64_encode($upload_img15660); ?>" data-status="<?php echo htmlspecialchars($status15660); ?>" data-category="<?php echo htmlspecialchars($category15660); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15660); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15660); ?>; 
    position:absolute; top:422px; left:400px;'>
                        </div>

                        <!-- ASSET 15661 -->
                        <img src='../image.php?id=15661' style='width:18px; cursor:pointer; position:absolute; top:360px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15661' onclick='fetchAssetData(15661);' class="asset-image" data-id="<?php echo $assetId15661; ?>" data-room="<?php echo htmlspecialchars($room15661); ?>" data-floor="<?php echo htmlspecialchars($floor15661); ?>" data-image="<?php echo base64_encode($upload_img15661); ?>" data-category="<?php echo htmlspecialchars($category15661); ?>" data-status="<?php echo htmlspecialchars($status15661); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15661); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15661); ?>; 
    position:absolute; top:370px; left:423px;'>
                        </div>

                        <!-- ASSET 15662 -->
                        <img src='../image.php?id=15662' style='width:18px; cursor:pointer; position:absolute; top:373px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15662' onclick='fetchAssetData(15662);' class="asset-image" data-id="<?php echo $assetId15662; ?>" data-room="<?php echo htmlspecialchars($room15662); ?>" data-floor="<?php echo htmlspecialchars($floor15662); ?>" data-image="<?php echo base64_encode($upload_img15662); ?>" data-category="<?php echo htmlspecialchars($category15662); ?>" data-status="<?php echo htmlspecialchars($status15662); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15662); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15662); ?>; 
    position:absolute; top:383px; left:423px;'>
                        </div>

                        <!-- ASSET 15663 -->
                        <img src='../image.php?id=15663' style='width:18px; cursor:pointer; position:absolute; top:386px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15663' onclick='fetchAssetData(15663);' class="asset-image" data-id="<?php echo $assetId15663; ?>" data-room="<?php echo htmlspecialchars($room15663); ?>" data-floor="<?php echo htmlspecialchars($floor15663); ?>" data-image="<?php echo base64_encode($upload_img15663); ?>" data-category="<?php echo htmlspecialchars($category15663); ?>" data-status="<?php echo htmlspecialchars($status15663); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15663); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15663); ?>; 
    position:absolute; top:396px; left:423px;'>
                        </div>

                        <!-- ASSET 15664 -->
                        <img src='../image.php?id=15664' style='width:18px; cursor:pointer; position:absolute; top:399px; left: 427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15664' onclick='fetchAssetData(15664);' class="asset-image" data-id="<?php echo $assetId15664; ?>" data-room="<?php echo htmlspecialchars($room15664); ?>" data-floor="<?php echo htmlspecialchars($floor15664); ?>" data-image="<?php echo base64_encode($upload_img15664); ?>" data-category="<?php echo htmlspecialchars($category15664); ?>" data-status="<?php echo htmlspecialchars($status15664); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15664); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15664); ?>; 
    position:absolute; top:409px; left:423px;'>
                        </div>

                        <!-- ASSET 15665 -->
                        <img src='../image.php?id=15665' style='width:18px; cursor:pointer; position:absolute; top:412px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15665' onclick='fetchAssetData(15665);' class="asset-image" data-id="<?php echo $assetId15665; ?>" data-room="<?php echo htmlspecialchars($room15665); ?>" data-floor="<?php echo htmlspecialchars($floor15665); ?>" data-image="<?php echo base64_encode($upload_img15665); ?>" data-category="<?php echo htmlspecialchars($category15665); ?>" data-status="<?php echo htmlspecialchars($status15665); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15665); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15665); ?>; 
    position:absolute; top:422px; left:423px;'>
                        </div>

                        <!-- ASSET 15666 -->
                        <img src='../image.php?id=15666' style='width:18px; cursor:pointer; position:absolute; top:445px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15666' onclick='fetchAssetData(15666);' class="asset-image" data-id="<?php echo $assetId15666; ?>" data-room="<?php echo htmlspecialchars($room15666); ?>" data-floor="<?php echo htmlspecialchars($floor15666); ?>" data-image="<?php echo base64_encode($upload_img15666); ?>" data-category="<?php echo htmlspecialchars($category15666); ?>" data-status="<?php echo htmlspecialchars($status15666); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15666); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15666); ?>; 
    position:absolute; top:455px; left:331px;'>
                        </div>

                        <!-- ASSET 15667 -->
                        <img src='../image.php?id=15667' style='width:18px; cursor:pointer; position:absolute; top:458px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15667' onclick='fetchAssetData(15667);' class="asset-image" data-id="<?php echo $assetId15667; ?>" data-room="<?php echo htmlspecialchars($room15667); ?>" data-floor="<?php echo htmlspecialchars($floor15667); ?>" data-image="<?php echo base64_encode($upload_img15667); ?>" data-category="<?php echo htmlspecialchars($category15667); ?>" data-status="<?php echo htmlspecialchars($status15667); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15667); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15667); ?>; 
    position:absolute; top:468px; left:331px;'>
                        </div>

                        <!-- ASSET 15668 -->
                        <img src='../image.php?id=15668' style='width:18px; cursor:pointer; position:absolute; top:471px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15668' onclick='fetchAssetData(15668);' class="asset-image" data-id="<?php echo $assetId15668; ?>" data-room="<?php echo htmlspecialchars($room15668); ?>" data-floor="<?php echo htmlspecialchars($floor15668); ?>" data-image="<?php echo base64_encode($upload_img15668); ?>" data-category="<?php echo htmlspecialchars($category15668); ?>" data-status="<?php echo htmlspecialchars($status15668); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15668); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15668); ?>; 
    position:absolute; top:481px; left:331px;'>
                        </div>

                        <!-- ASSET 15669 -->
                        <img src='../image.php?id=15669' style='width:18px; cursor:pointer; position:absolute; top:484px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15669' onclick='fetchAssetData(15669);' class="asset-image" data-id="<?php echo $assetId15669; ?>" data-room="<?php echo htmlspecialchars($room15669); ?>" data-floor="<?php echo htmlspecialchars($floor15669); ?>" data-image="<?php echo base64_encode($upload_img15669); ?>" data-status="<?php echo htmlspecialchars($status15669); ?>" data-category="<?php echo htmlspecialchars($category15669); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15669); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15669); ?>; 
    position:absolute; top:494px; left:331px;'>
                        </div>

                        <!-- ASSET 15670 -->
                        <img src='../image.php?id=15670' style='width:18px; cursor:pointer; position:absolute; top:497px; left:335px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15670' onclick='fetchAssetData(15670);' class="asset-image" data-id="<?php echo $assetId15670; ?>" data-room="<?php echo htmlspecialchars($room15670); ?>" data-floor="<?php echo htmlspecialchars($floor15670); ?>" data-image="<?php echo base64_encode($upload_img15670); ?>" data-status="<?php echo htmlspecialchars($status15670); ?>" data-category="<?php echo htmlspecialchars($category15670); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15670); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15670); ?>; 
    position:absolute; top:507px; left:331px;'>
                        </div>

                        <!-- ASSET 15671 -->
                        <img src='../image.php?id=15671' style='width:18px; cursor:pointer; position:absolute; top:445px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15671' onclick='fetchAssetData(15671);' class="asset-image" data-id="<?php echo $assetId15671; ?>" data-room="<?php echo htmlspecialchars($room15671); ?>" data-floor="<?php echo htmlspecialchars($floor15671); ?>" data-image="<?php echo base64_encode($upload_img15671); ?>" data-status="<?php echo htmlspecialchars($status15671); ?>" data-category="<?php echo htmlspecialchars($category15671); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15671); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15671); ?>; 
    position:absolute; top:455px; left:354px;'>
                        </div>

                        <!-- ASSET 15672 -->
                        <img src='../image.php?id=15672' style='width:18px; cursor:pointer; position:absolute; top:458px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15672' onclick='fetchAssetData(15672);' class="asset-image" data-id="<?php echo $assetId15672; ?>" data-room="<?php echo htmlspecialchars($room15672); ?>" data-floor="<?php echo htmlspecialchars($floor15672); ?>" data-image="<?php echo base64_encode($upload_img15672); ?>" data-category="<?php echo htmlspecialchars($category15672); ?>" data-status="<?php echo htmlspecialchars($status15672); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15672); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15672); ?>; 
    position:absolute; top:468px; left:354px;'>
                        </div>

                        <!-- ASSET 15673 -->
                        <img src='../image.php?id=15673' style='width:18px; cursor:pointer; position:absolute; top:471px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15673' onclick='fetchAssetData(15673);' class="asset-image" data-id="<?php echo $assetId15673; ?>" data-room="<?php echo htmlspecialchars($room15673); ?>" data-floor="<?php echo htmlspecialchars($floor15673); ?>" data-image="<?php echo base64_encode($upload_img15673); ?>" data-category="<?php echo htmlspecialchars($category15673); ?>" data-status="<?php echo htmlspecialchars($status15673); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName6469); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15673); ?>; 
    position:absolute; top:481px; left:354px;'>
                        </div>

                        <!-- ASSET 15674 -->
                        <img src='../image.php?id=15674' style='width:18px; cursor:pointer; position:absolute; top:484px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15674' onclick='fetchAssetData(15674);' class="asset-image" data-id="<?php echo $assetId15674; ?>" data-room="<?php echo htmlspecialchars($room15674); ?>" data-floor="<?php echo htmlspecialchars($floor15674); ?>" data-image="<?php echo base64_encode($upload_img15674); ?>" data-category="<?php echo htmlspecialchars($category15674); ?>" data-status="<?php echo htmlspecialchars($status15674); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15674); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15674); ?>; 
    position:absolute; top:494px; left:354px;'>
                        </div>


                        <!-- ASSET 15675 -->
                        <img src='../image.php?id=15675' style='width:18px; cursor:pointer; position:absolute; top:497px; left:358px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15675' onclick='fetchAssetData(15675);' class="asset-image" data-id="<?php echo $assetId15675; ?>" data-room="<?php echo htmlspecialchars($room15675); ?>" data-floor="<?php echo htmlspecialchars($floor15675); ?>" data-image="<?php echo base64_encode($upload_img15675); ?>" data-category="<?php echo htmlspecialchars($category15675); ?>" data-status="<?php echo htmlspecialchars($status15675); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15675); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15675); ?>; 
    position:absolute; top:507px; left:354px;'>
                        </div>

                        <!-- ASSET 15676 -->
                        <img src='../image.php?id=15676' style='width:18px; cursor:pointer; position:absolute; top:445px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15676' onclick='fetchAssetData(15676);' class="asset-image" data-id="<?php echo $assetId15676; ?>" data-room="<?php echo htmlspecialchars($room15676); ?>" data-floor="<?php echo htmlspecialchars($floor15676); ?>" data-image="<?php echo base64_encode($upload_img15676); ?>" data-category="<?php echo htmlspecialchars($category15676); ?>" data-status="<?php echo htmlspecialchars($status15676); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15676); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15676); ?>; 
    position:absolute; top:455px; left:377px;'>
                        </div>

                        <!-- ASSET 15677 -->
                        <img src='../image.php?id=15677' style='width:18px; cursor:pointer; position:absolute; top:458px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15677' onclick='fetchAssetData(15677);' class="asset-image" data-id="<?php echo $assetId15677; ?>" data-room="<?php echo htmlspecialchars($room15677); ?>" data-floor="<?php echo htmlspecialchars($floor15677); ?>" data-image="<?php echo base64_encode($upload_img15677); ?>" data-category="<?php echo htmlspecialchars($category15677); ?>" data-status="<?php echo htmlspecialchars($status15677); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15677); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15677); ?>; 
    position:absolute; top:468px; left:377px;'>
                        </div>

                        <!-- ASSET 15678 -->
                        <img src='../image.php?id=15678' style='width:18px; cursor:pointer; position:absolute; top:471px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15678' onclick='fetchAssetData(15678);' class="asset-image" data-id="<?php echo $assetId15678; ?>" data-room="<?php echo htmlspecialchars($room15678); ?>" data-floor="<?php echo htmlspecialchars($floor15678); ?>" data-image="<?php echo base64_encode($upload_img15678); ?>" data-category="<?php echo htmlspecialchars($category15678); ?>" data-status="<?php echo htmlspecialchars($status15678); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15678); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15678); ?>; 
    :absolute; top:481px; left:377px;'>
                        </div>

                        <!-- ASSET 15679 -->
                        <img src='../image.php?id=15679' style='width:18px; cursor:pointer; position:absolute; top:484px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15679' onclick='fetchAssetData(15679);' class="asset-image" data-id="<?php echo $assetId15679; ?>" data-room="<?php echo htmlspecialchars($room15679); ?>" data-floor="<?php echo htmlspecialchars($floor15679); ?>" data-image="<?php echo base64_encode($upload_img15679); ?>" data-category="<?php echo htmlspecialchars($category15679); ?>" data-status="<?php echo htmlspecialchars($status15679); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15679); ?>; 
    position:absolute; top:494px; left:377px;'>
                        </div>

                        <!-- ASSET 15680 -->
                        <img src='../image.php?id=15680' style='width:18px; cursor:pointer; position:absolute; top:497px; left:381px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15680' onclick='fetchAssetData(15680);' class="asset-image" data-id="<?php echo $assetId15680; ?>" data-room="<?php echo htmlspecialchars($room15680); ?>" data-floor="<?php echo htmlspecialchars($floor15680); ?>" data-image="<?php echo base64_encode($upload_img15680); ?>" data-category="<?php echo htmlspecialchars($category15680); ?>" data-status="<?php echo htmlspecialchars($status15680); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15680); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15680); ?>; 
    position:absolute; top:507px; left:377px;'>
                        </div>

                        <!-- ASSET 15681 -->
                        <img src='../image.php?id=15681' style='width:18px; cursor:pointer; position:absolute; top:445px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15681' onclick='fetchAssetData(15681);' class="asset-image" data-id="<?php echo $assetId15681; ?>" data-room="<?php echo htmlspecialchars($room15681); ?>" data-floor="<?php echo htmlspecialchars($floor15681); ?>" data-image="<?php echo base64_encode($upload_img15681); ?>" data-status="<?php echo htmlspecialchars($status15681); ?>" data-category="<?php echo htmlspecialchars($category15681); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15681); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15681); ?>; 
    position:absolute; top:455px; left:400px;'>
                        </div>

                        <!-- ASSET 15682 -->
                        <img src='../image.php?id=15682' style='width:18px; cursor:pointer; position:absolute; top:458px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15682' onclick='fetchAssetData(15682);' class="asset-image" data-id="<?php echo $assetId15682; ?>" data-room="<?php echo htmlspecialchars($room15682); ?>" data-floor="<?php echo htmlspecialchars($floor15682); ?>" data-image="<?php echo base64_encode($upload_img15682); ?>" data-category="<?php echo htmlspecialchars($category15682); ?>" data-status="<?php echo htmlspecialchars($status15682); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15682); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15682); ?>; 
    position:absolute; top:468px; left:400px;'>
                        </div>


                        <!-- ASSET 15683 -->
                        <img src='../image.php?id=15683' style='width:18px; cursor:pointer; position:absolute; top:471px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15683' onclick='fetchAssetData(15683);' class="asset-image" data-id="<?php echo $assetId15683; ?>" data-room="<?php echo htmlspecialchars($room15683); ?>" data-floor="<?php echo htmlspecialchars($floor15683); ?>" data-image="<?php echo base64_encode($upload_img15683); ?>" data-status="<?php echo htmlspecialchars($status15683); ?>" data-category="<?php echo htmlspecialchars($category15683); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15683); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15683); ?>; 
    position:absolute; top:481px; left:400px;'>
                        </div>

                        <!-- ASSET 15684 -->
                        <img src='../image.php?id=15684' style='width:18px; cursor:pointer; position:absolute; top:484px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15684' onclick='fetchAssetData(15684);' class="asset-image" data-id="<?php echo $assetId15684; ?>" data-room="<?php echo htmlspecialchars($room15684); ?>" data-floor="<?php echo htmlspecialchars($floor15684); ?>" data-image="<?php echo base64_encode($upload_img15684); ?>" data-category="<?php echo htmlspecialchars($category15684); ?>" data-status="<?php echo htmlspecialchars($status15684); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15684); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15684); ?>; 
    position:absolute; top:494px; left:400px;'>
                        </div>

                        <!-- ASSET 15685 -->
                        <img src='../image.php?id=15685' style='width:18px; cursor:pointer; position:absolute; top:497px; left:404px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15685' onclick='fetchAssetData(15685);' class="asset-image" data-id="<?php echo $assetId15685; ?>" data-room="<?php echo htmlspecialchars($room15685); ?>" data-floor="<?php echo htmlspecialchars($floor15685); ?>" data-image="<?php echo base64_encode($upload_img15685); ?>" data-category="<?php echo htmlspecialchars($category15685); ?>" data-status="<?php echo htmlspecialchars($status15685); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15685); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15685); ?>; 
    position:absolute; top:507px; left:400px;'>
                        </div>

                        <!-- ASSET 15686 -->
                        <img src='../image.php?id=15686' style='width:18px; cursor:pointer; position:absolute; top:445px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15686' onclick='fetchAssetData(15686);' class="asset-image" data-id="<?php echo $assetId15686; ?>" data-room="<?php echo htmlspecialchars($room15686); ?>" data-floor="<?php echo htmlspecialchars($floor15686); ?>" data-image="<?php echo base64_encode($upload_img15686); ?>" data-category="<?php echo htmlspecialchars($category15686); ?>" data-status="<?php echo htmlspecialchars($status15686); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15686); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15686); ?>; 
    position:absolute; top:455px; left:423px;'>
                        </div>

                        <!-- ASSET 15687 -->
                        <img src='../image.php?id=15687' style='width:18px; cursor:pointer; position:absolute; top:458px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15687' onclick='fetchAssetData(15687);' class="asset-image" data-id="<?php echo $assetId15687; ?>" data-room="<?php echo htmlspecialchars($room15687); ?>" data-floor="<?php echo htmlspecialchars($floor15687); ?>" data-image="<?php echo base64_encode($upload_img15687); ?>" data-category="<?php echo htmlspecialchars($category15687); ?>" data-status="<?php echo htmlspecialchars($status15687); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15687); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15687); ?>; 
    position:absolute; top:468px; left:423px;'>
                        </div>

                        <!-- ASSET 15688 -->
                        <img src='../image.php?id=15688' style='width:18px; cursor:pointer; position:absolute; top:471px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15688' onclick='fetchAssetData(15688);' class="asset-image" data-id="<?php echo $assetId15688; ?>" data-room="<?php echo htmlspecialchars($room15688); ?>" data-floor="<?php echo htmlspecialchars($floor15688); ?>" data-image="<?php echo base64_encode($upload_img15688); ?>" data-category="<?php echo htmlspecialchars($category15688); ?>" data-status="<?php echo htmlspecialchars($status15688); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15688); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15688); ?>; 
    position:absolute; top:481px; left:423px;'>
                        </div>

                        <!-- ASSET 15689 -->
                        <img src='../image.php?id=15689' style='width:18px; cursor:pointer; position:absolute; top:484px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15689' onclick='fetchAssetData(15689);' class="asset-image" data-id="<?php echo $assetId15689; ?>" data-room="<?php echo htmlspecialchars($room15689); ?>" data-floor="<?php echo htmlspecialchars($floor15689); ?>" data-image="<?php echo base64_encode($upload_img15689); ?>" data-status="<?php echo htmlspecialchars($status15689); ?>" data-category="<?php echo htmlspecialchars($category15689); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15689); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15689); ?>; 
    position:absolute; top:494px; left:423px;'>
                        </div>

                        <!-- ASSET 15690 -->
                        <img src='../image.php?id=15690' style='width:18px; cursor:pointer; position:absolute; top:497px; left:427px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15690' onclick='fetchAssetData(15690);' class="asset-image" data-id="<?php echo $assetId15690; ?>" data-room="<?php echo htmlspecialchars($room15690); ?>" data-floor="<?php echo htmlspecialchars($floor15690); ?>" data-image="<?php echo base64_encode($upload_img15690); ?>" data-category="<?php echo htmlspecialchars($category15690); ?>" data-status="<?php echo htmlspecialchars($status15690); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15690); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15690); ?>; 
    position:absolute; top:507px; left:423px;'>
                        </div>

                        <!-- Start of IC207 -->

                        <!-- ASSET 15754 -->
                        <img src='../image.php?id=15754' style='width:15px; cursor:pointer; position:absolute; top:355px; left:755px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15754' onclick='fetchAssetData(15754);' class="asset-image" data-id="<?php echo $assetId15754; ?>" data-room="<?php echo htmlspecialchars($room15754); ?>" data-floor="<?php echo htmlspecialchars($floor15754); ?>" data-image="<?php echo base64_encode($upload_img15754); ?>" data-category="<?php echo htmlspecialchars($category15754); ?>" data-status="<?php echo htmlspecialchars($status15754); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15754); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15754); ?>; 
    position:absolute; top:355px; left:765px;'>
                        </div>

                        <!-- ASSET 15755 -->
                        <img src='../image.php?id=15755' style='width:15px; cursor:pointer; position:absolute; top:430px; left:755px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15755' onclick='fetchAssetData(15755);' class="asset-image" data-id="<?php echo $assetId15755; ?>" data-room="<?php echo htmlspecialchars($room15755); ?>" data-floor="<?php echo htmlspecialchars($floor15755); ?>" data-image="<?php echo base64_encode($upload_img15755); ?>" data-category="<?php echo htmlspecialchars($category15755); ?>" data-status="<?php echo htmlspecialchars($status15755); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15755); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15755); ?>; 
    position:absolute; top:430px; left:765px;'>
                        </div>

                        <!-- ASSET 15756 -->
                        <img src='../image.php?id=15756' style='width:15px; cursor:pointer; position:absolute; top:355px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15756' onclick='fetchAssetData(15756);' class="asset-image" data-id="<?php echo $assetId15756; ?>" data-room="<?php echo htmlspecialchars($room15756); ?>" data-floor="<?php echo htmlspecialchars($floor15756); ?>" data-image="<?php echo base64_encode($upload_img15756); ?>" data-category="<?php echo htmlspecialchars($category15756); ?>" data-status="<?php echo htmlspecialchars($status15756); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15756); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15756); ?>; 
    position:absolute; top:355px; left:860px;'>
                        </div>

                        <!-- ASSET 15757 -->
                        <img src='../image.php?id=15757' style='width:15px; cursor:pointer; position:absolute; top:430px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15757' onclick='fetchAssetData(15757);' class="asset-image" data-id="<?php echo $assetId15757; ?>" data-room="<?php echo htmlspecialchars($room15757); ?>" data-floor="<?php echo htmlspecialchars($floor15757); ?>" data-image="<?php echo base64_encode($upload_img15757); ?>" data-category="<?php echo htmlspecialchars($category15757); ?>" data-status="<?php echo htmlspecialchars($status15757); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15757); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15757); ?>; 
    position:absolute; top:430px; left:860px;'>
                        </div>

                        <!-- ASSET 15758 -->
                        <img src='../image.php?id=15758' style='width:15px; cursor:pointer; position:absolute; top:430px; left:990px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15758' onclick='fetchAssetData(15758);' class="asset-image" data-id="<?php echo $assetId15758; ?>" data-room="<?php echo htmlspecialchars($room15758); ?>" data-floor="<?php echo htmlspecialchars($floor15758); ?>" data-image="<?php echo base64_encode($upload_img15758); ?>" data-category="<?php echo htmlspecialchars($category15758); ?>" data-status="<?php echo htmlspecialchars($status15758); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15758); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15758); ?>; 
    position:absolute; top:430px; left:1000px;'>
                        </div>

                        <!-- ASSET 15759 -->
                        <img src='../image.php?id=15759' style='width:15px; cursor:pointer; position:absolute; top:355px; left:990px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15759' onclick='fetchAssetData(15759);' class="asset-image" data-id="<?php echo $assetId15759; ?>" data-room="<?php echo htmlspecialchars($room15759); ?>" data-floor="<?php echo htmlspecialchars($floor15759); ?>" data-image="<?php echo base64_encode($upload_img15759); ?>" data-category="<?php echo htmlspecialchars($category15759); ?>" data-status="<?php echo htmlspecialchars($status15759); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15759); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15759); ?>; 
    position:absolute; top:355px; left:1000px;'>
                        </div>

                        <!-- ASSET 15826 -->
                        <img src='../image.php?id=15826' style='width:15px; cursor:pointer; position:absolute; top:426px; left:788px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15826' onclick='fetchAssetData(15826);' class="asset-image" data-id="<?php echo $assetId15826; ?>" data-room="<?php echo htmlspecialchars($room15826); ?>" data-floor="<?php echo htmlspecialchars($floor15826); ?>" data-image="<?php echo base64_encode($upload_img15826); ?>" data-category="<?php echo htmlspecialchars($category15826); ?>" data-status="<?php echo htmlspecialchars($status15826); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15826); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15826); ?>; 
    position:absolute; top:415px; left:792px;'>
                        </div>

                        <!-- ASSET 6498 -->
                        <img src='../image.php?id=6498' style='width:18px; cursor:pointer; position:absolute; top:426px; left:770px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6498' onclick='fetchAssetData(6498);' class="asset-image" data-id="<?php echo $assetId6498; ?>" data-room="<?php echo htmlspecialchars($room6498); ?>" data-floor="<?php echo htmlspecialchars($floor6498); ?>" data-image="<?php echo base64_encode($upload_img6498); ?>" data-category="<?php echo htmlspecialchars($category6498); ?>" data-status="<?php echo htmlspecialchars($status6498); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName6498); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status6498); ?>; 
    position:absolute; top:405px; left:764px;'>
                        </div>

                        <!-- ASSET 15760 -->
                        <img src='../image.php?id=15760' style='width:15px; cursor:pointer; position:absolute; top:515px; left:755px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15760' onclick='fetchAssetData(15760);' class="asset-image" data-id="<?php echo $assetId15760; ?>" data-room="<?php echo htmlspecialchars($room15760); ?>" data-floor="<?php echo htmlspecialchars($floor15760); ?>" data-image="<?php echo base64_encode($upload_img15760); ?>" data-category="<?php echo htmlspecialchars($category15760); ?>" data-status="<?php echo htmlspecialchars($status15760); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15760); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15760); ?>; 
    position:absolute; top:515px; left:765px;'>
                        </div>

                        <!-- ASSET 15761 -->
                        <img src='../image.php?id=15761' style='width:15px; cursor:pointer; position:absolute; top:515px; left:850px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15761' onclick='fetchAssetData(15761);' class="asset-image" data-id="<?php echo $assetId15761; ?>" data-room="<?php echo htmlspecialchars($room15761); ?>" data-floor="<?php echo htmlspecialchars($floor15761); ?>" data-image="<?php echo base64_encode($upload_img15761); ?>" data-category="<?php echo htmlspecialchars($category15761); ?>" data-status="<?php echo htmlspecialchars($status15761); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15761); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15761); ?>; 
    position:absolute; top:515px; left:860px;'>
                        </div>

                        <!-- ASSET 15762 -->
                        <img src='../image.php?id=15762' style='width:15px; cursor:pointer; position:absolute; top:515px; left:990px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15762' onclick='fetchAssetData(15762);' class="asset-image" data-id="<?php echo $assetId15762; ?>" data-room="<?php echo htmlspecialchars($room15762); ?>" data-floor="<?php echo htmlspecialchars($floor15762); ?>" data-image="<?php echo base64_encode($upload_img15762); ?>" data-category="<?php echo htmlspecialchars($category15762); ?>" data-status="<?php echo htmlspecialchars($status15762); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15762); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15762); ?>; 
    position:absolute; top:515px; left:1000px;'>
                        </div>

                        <!-- ASSET 15763 -->
                        <img src='../image.php?id=15763' style='width:18px; cursor:pointer; position:absolute; top:360px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15763' onclick='fetchAssetData(15763);' class="asset-image" data-id="<?php echo $assetId15763; ?>" data-room="<?php echo htmlspecialchars($room15763); ?>" data-floor="<?php echo htmlspecialchars($floor15763); ?>" data-image="<?php echo base64_encode($upload_img15763); ?>" data-status="<?php echo htmlspecialchars($status15763); ?>" data-category="<?php echo htmlspecialchars($category15763); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15763); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15763); ?>; 
    position:absolute; top:370px; left:869px;'>
                        </div>

                        <!-- ASSET 15764 -->
                        <img src='../image.php?id=15764' style='width:18px; cursor:pointer; position:absolute; top:373px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15764' onclick='fetchAssetData(15764);' class="asset-image" data-id="<?php echo $assetId15764; ?>" data-room="<?php echo htmlspecialchars($room15764); ?>" data-floor="<?php echo htmlspecialchars($floor15764); ?>" data-image="<?php echo base64_encode($upload_img15764); ?>" data-status="<?php echo htmlspecialchars($status15764); ?>" data-category="<?php echo htmlspecialchars($category15764); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15764); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15764); ?>; 
    position:absolute; top:383px; left:869px;'>
                        </div>

                        <!-- ASSET 15765 -->
                        <img src='../image.php?id=15765' style='width:18px; cursor:pointer; position:absolute; top:386px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15765' onclick='fetchAssetData(15765);' class="asset-image" data-id="<?php echo $assetId15765; ?>" data-room="<?php echo htmlspecialchars($room15765); ?>" data-floor="<?php echo htmlspecialchars($floor15765); ?>" data-image="<?php echo base64_encode($upload_img15765); ?>" data-status="<?php echo htmlspecialchars($status15765); ?>" data-category="<?php echo htmlspecialchars($category15765); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15765); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15765); ?>; 
    position:absolute; top:396px; left:869px;'>
                        </div>

                        <!-- ASSET 15766 -->
                        <img src='../image.php?id=15766' style='width:18px; cursor:pointer; position:absolute; top:399px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15766' onclick='fetchAssetData(15766);' class="asset-image" data-id="<?php echo $assetId15766; ?>" data-room="<?php echo htmlspecialchars($room15766); ?>" data-floor="<?php echo htmlspecialchars($floor15766); ?>" data-image="<?php echo base64_encode($upload_img15766); ?>" data-category="<?php echo htmlspecialchars($category15766); ?>" data-status="<?php echo htmlspecialchars($status15766); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15766); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15766); ?>; 
    position:absolute; top:409px; left:869px;'>
                        </div>

                        <!-- ASSET 15767 -->
                        <img src='../image.php?id=15767' style='width:18px; cursor:pointer; position:absolute; top:412px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15767' onclick='fetchAssetData(15767);' class="asset-image" data-id="<?php echo $assetId15767; ?>" data-room="<?php echo htmlspecialchars($room15767); ?>" data-floor="<?php echo htmlspecialchars($floor15767); ?>" data-image="<?php echo base64_encode($upload_img15767); ?>" data-category="<?php echo htmlspecialchars($category15767); ?>" data-status="<?php echo htmlspecialchars($status15767); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15767); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15767); ?>; 
    position:absolute; top:422px; left:869px;'>
                        </div>

                        <!-- ASSET 15768 -->
                        <img src='../image.php?id=15768' style='width:18px; cursor:pointer; position:absolute; top:360px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15768' onclick='fetchAssetData(15768);' class="asset-image" data-id="<?php echo $assetId15768; ?>" data-room="<?php echo htmlspecialchars($room15768); ?>" data-floor="<?php echo htmlspecialchars($floor15768); ?>" data-image="<?php echo base64_encode($upload_img15768); ?>" data-status="<?php echo htmlspecialchars($status15768); ?>" data-category="<?php echo htmlspecialchars($category15768); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15768); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15768); ?>; 
    position:absolute; top:370px; left:892px;'>
                        </div>

                        <!-- ASSET 15769 -->
                        <img src='../image.php?id=15769' style='width:18px; cursor:pointer; position:absolute; top:373px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15769' onclick='fetchAssetData(15769);' class="asset-image" data-id="<?php echo $assetId15769; ?>" data-room="<?php echo htmlspecialchars($room15769); ?>" data-floor="<?php echo htmlspecialchars($floor15769); ?>" data-image="<?php echo base64_encode($upload_img15769); ?>" data-category="<?php echo htmlspecialchars($category15769); ?>" data-status="<?php echo htmlspecialchars($status15769); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15769); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15769); ?>; 
    position:absolute; top:383px; left:892px;'>
                        </div>

                        <!-- ASSET 15770 -->
                        <img src='../image.php?id=15770' style='width:18px; cursor:pointer; position:absolute; top:386px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15770' onclick='fetchAssetData(15770);' class="asset-image" data-id="<?php echo $assetId15770; ?>" data-room="<?php echo htmlspecialchars($room15770); ?>" data-floor="<?php echo htmlspecialchars($floor15770); ?>" data-image="<?php echo base64_encode($upload_img15770); ?>" data-category="<?php echo htmlspecialchars($category15770); ?>" data-status="<?php echo htmlspecialchars($status15770); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15770); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15770); ?>; 
    position:absolute; top:396px; left:892px;'>
                        </div>

                        <!-- ASSET 15771 -->
                        <img src='../image.php?id=15771' style='width:18px; cursor:pointer; position:absolute; top:399px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15771' onclick='fetchAssetData(15771);' class="asset-image" data-id="<?php echo $assetId15771; ?>" data-room="<?php echo htmlspecialchars($room15771); ?>" data-floor="<?php echo htmlspecialchars($floor15771); ?>" data-image="<?php echo base64_encode($upload_img15771); ?>" data-category="<?php echo htmlspecialchars($category15771); ?>" data-status="<?php echo htmlspecialchars($status15771); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15771); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15771); ?>; 
    position:absolute; top:409px; left:892px;'>
                        </div>

                        <!-- ASSET 15772 -->
                        <img src='../image.php?id=15772' style='width:18px; cursor:pointer; position:absolute; top:412px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15772' onclick='fetchAssetData(15772);' class="asset-image" data-id="<?php echo $assetId15772; ?>" data-room="<?php echo htmlspecialchars($room15772); ?>" data-floor="<?php echo htmlspecialchars($floor15772); ?>" data-image="<?php echo base64_encode($upload_img15772); ?>" data-status="<?php echo htmlspecialchars($status15772); ?>" data-category="<?php echo htmlspecialchars($category15772); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15772); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15772); ?>; 
    position:absolute; top:422px; left:892px;'>
                        </div>

                        <!-- ASSET 15773 -->
                        <img src='../image.php?id=15773' style='width:18px; cursor:pointer; position:absolute; top:360px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15773' onclick='fetchAssetData(15773);' class="asset-image" data-id="<?php echo $assetId15773; ?>" data-room="<?php echo htmlspecialchars($room15773); ?>" data-floor="<?php echo htmlspecialchars($floor15773); ?>" data-image="<?php echo base64_encode($upload_img15773); ?>" data-status="<?php echo htmlspecialchars($status15773); ?>" data-category="<?php echo htmlspecialchars($category15773); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15773); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15773); ?>; 
    position:absolute; top:370px; left:915px;'>
                        </div>

                        <!-- ASSET 15774 -->
                        <img src='../image.php?id=15774' style='width:18px; cursor:pointer; position:absolute; top:373px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15774' onclick='fetchAssetData(15774);' class="asset-image" data-id="<?php echo $assetId15774; ?>" data-room="<?php echo htmlspecialchars($room15774); ?>" data-floor="<?php echo htmlspecialchars($floor15774); ?>" data-image="<?php echo base64_encode($upload_img15774); ?>" data-status="<?php echo htmlspecialchars($status15774); ?>" data-category="<?php echo htmlspecialchars($category15774); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15774); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15774); ?>; 
    position:absolute; top:383px; left:915px;'>
                        </div>

                        <!-- ASSET 15775 -->
                        <img src='../image.php?id=15775' style='width:18px; cursor:pointer; position:absolute; top:386px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15775' onclick='fetchAssetData(15775);' class="asset-image" data-id="<?php echo $assetId15775; ?>" data-room="<?php echo htmlspecialchars($room15775); ?>" data-floor="<?php echo htmlspecialchars($floor15775); ?>" data-image="<?php echo base64_encode($upload_img15775); ?>" data-category="<?php echo htmlspecialchars($category15775); ?>" data-status="<?php echo htmlspecialchars($status15775); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15775); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15775); ?>; 
    position:absolute; top:396px; left:915px;'>
                        </div>

                        <!-- ASSET 15776 -->
                        <img src='../image.php?id=15776' style='width:18px; cursor:pointer; position:absolute; top:399px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15776' onclick='fetchAssetData(15776);' class="asset-image" data-id="<?php echo $assetId15776; ?>" data-room="<?php echo htmlspecialchars($room15776); ?>" data-floor="<?php echo htmlspecialchars($floor15776); ?>" data-image="<?php echo base64_encode($upload_img15776); ?>" data-category="<?php echo htmlspecialchars($category15776); ?>" data-status="<?php echo htmlspecialchars($status15776); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15776); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15776); ?>; 
    position:absolute; top:409px; left:915px;'>
                        </div>

                        <!-- ASSET 15777 -->
                        <img src='../image.php?id=15777' style='width:18px; cursor:pointer; position:absolute; top:412px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15777' onclick='fetchAssetData(15777);' class="asset-image" data-id="<?php echo $assetId15777; ?>" data-room="<?php echo htmlspecialchars($room15777); ?>" data-floor="<?php echo htmlspecialchars($floor15777); ?>" data-image="<?php echo base64_encode($upload_img15777); ?>" data-status="<?php echo htmlspecialchars($status15777); ?>" data-category="<?php echo htmlspecialchars($category15777); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15777); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15777); ?>; 
    position:absolute; top:422px; left:915px;'>
                        </div>

                        <!-- ASSET 15778 -->
                        <img src='../image.php?id=15778' style='width:18px; cursor:pointer; position:absolute; top:360px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15778' onclick='fetchAssetData(15778);' class="asset-image" data-id="<?php echo $assetId15778; ?>" data-room="<?php echo htmlspecialchars($room15778); ?>" data-floor="<?php echo htmlspecialchars($floor15778); ?>" data-image="<?php echo base64_encode($upload_img15778); ?>" data-status="<?php echo htmlspecialchars($status15778); ?>" data-category="<?php echo htmlspecialchars($category15778); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15778); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15778); ?>; 
    position:absolute; top:370px; left:938px;'>
                        </div>

                        <!-- ASSET 15779 -->
                        <img src='../image.php?id=15779' style='width:18px; cursor:pointer; position:absolute; top:373px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15779' onclick='fetchAssetData(15779);' class="asset-image" data-id="<?php echo $assetId15779; ?>" data-room="<?php echo htmlspecialchars($room15779); ?>" data-floor="<?php echo htmlspecialchars($floor15779); ?>" data-image="<?php echo base64_encode($upload_img15779); ?>" data-status="<?php echo htmlspecialchars($status15779); ?>" data-category="<?php echo htmlspecialchars($category15779); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15779); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15779); ?>; 
    position:absolute; top:383px; left:938px;'>
                        </div>

                        <!-- ASSET 15780 -->
                        <img src='../image.php?id=15780' style='width:18px; cursor:pointer; position:absolute; top:386px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15780' onclick='fetchAssetData(15780);' class="asset-image" data-id="<?php echo $assetId15780; ?>" data-room="<?php echo htmlspecialchars($room15780); ?>" data-floor="<?php echo htmlspecialchars($floor15780); ?>" data-image="<?php echo base64_encode($upload_img15780); ?>" data-category="<?php echo htmlspecialchars($category15780); ?>" data-status="<?php echo htmlspecialchars($status15780); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15780); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15780); ?>; 
    position:absolute; top:396px; left:938px;'>
                        </div>

                        <!-- ASSET 15781 -->
                        <img src='../image.php?id=15781' style='width:18px; cursor:pointer; position:absolute; top:399px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15781' onclick='fetchAssetData(15781);' class="asset-image" data-id="<?php echo $assetId15781; ?>" data-room="<?php echo htmlspecialchars($room15781); ?>" data-floor="<?php echo htmlspecialchars($floor15781); ?>" data-image="<?php echo base64_encode($upload_img15781); ?>" data-status="<?php echo htmlspecialchars($status15781); ?>" data-category="<?php echo htmlspecialchars($category15781); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15781); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15781); ?>; 
    position:absolute; top:409px; left:938px;'>
                        </div>

                        <!-- ASSET 15782 -->
                        <img src='../image.php?id=15782' style='width:18px; cursor:pointer; position:absolute; top:412px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15782' onclick='fetchAssetData(15782);' class="asset-image" data-id="<?php echo $assetId15782; ?>" data-room="<?php echo htmlspecialchars($room15782); ?>" data-floor="<?php echo htmlspecialchars($floor15782); ?>" data-image="<?php echo base64_encode($upload_img15782); ?>" data-status="<?php echo htmlspecialchars($status15782); ?>" data-category="<?php echo htmlspecialchars($category15782); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15782); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15782); ?>; 
    position:absolute; top:422px; left:938px;'>
                        </div>

                        <!-- ASSET 15783 -->
                        <img src='../image.php?id=15783' style='width:18px; cursor:pointer; position:absolute; top:360px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15783' onclick='fetchAssetData(15783);' class="asset-image" data-id="<?php echo $assetId15783; ?>" data-room="<?php echo htmlspecialchars($room15783); ?>" data-floor="<?php echo htmlspecialchars($floor15783); ?>" data-image="<?php echo base64_encode($upload_img15783); ?>" data-status="<?php echo htmlspecialchars($status15783); ?>" data-category="<?php echo htmlspecialchars($category15783); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15783); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15783); ?>; 
    position:absolute; top:370px; left:961px;'>
                        </div>

                        <!-- ASSET 15784 -->
                        <img src='../image.php?id=15784' style='width:18px; cursor:pointer; position:absolute; top:373px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15784' onclick='fetchAssetData(15784);' class="asset-image" data-id="<?php echo $assetId15784; ?>" data-room="<?php echo htmlspecialchars($room15784); ?>" data-floor="<?php echo htmlspecialchars($floor15784); ?>" data-status="<?php echo htmlspecialchars($status15784); ?>" data-image="<?php echo base64_encode($upload_img15784); ?>" data-category="<?php echo htmlspecialchars($category15784); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15784); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15784); ?>; 
    position:absolute; top:383px; left:961px;'>
                        </div>

                        <!-- ASSET 15785 -->
                        <img src='../image.php?id=15785' style='width:18px; cursor:pointer; position:absolute; top:386px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15785' onclick='fetchAssetData(15785);' class="asset-image" data-id="<?php echo $assetId15785; ?>" data-room="<?php echo htmlspecialchars($room15785); ?>" data-floor="<?php echo htmlspecialchars($floor15785); ?>" data-image="<?php echo base64_encode($upload_img15785); ?>" data-status="<?php echo htmlspecialchars($status15785); ?>" data-category="<?php echo htmlspecialchars($category15785); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15785); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15785); ?>; 
    position:absolute; top:396px; left:961px;'>
                        </div>

                        <!-- ASSET 15786 -->
                        <img src='../image.php?id=15786' style='width:18px; cursor:pointer; position:absolute; top:399px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15786' onclick='fetchAssetData(15786);' class="asset-image" data-id="<?php echo $assetId15786; ?>" data-room="<?php echo htmlspecialchars($room15786); ?>" data-floor="<?php echo htmlspecialchars($floor15786); ?>" data-image="<?php echo base64_encode($upload_img15786); ?>" data-category="<?php echo htmlspecialchars($category15786); ?>" data-status="<?php echo htmlspecialchars($status15786); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15786); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15786); ?>; 
    position:absolute; top:409px; left:961px;'>
                        </div>

                        <!-- ASSET 15787 -->
                        <img src='../image.php?id=15787' style='width:18px; cursor:pointer; position:absolute; top:412px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15787' onclick='fetchAssetData(15787);' class="asset-image" data-id="<?php echo $assetId15787; ?>" data-room="<?php echo htmlspecialchars($room15787); ?>" data-floor="<?php echo htmlspecialchars($floor15787); ?>" data-image="<?php echo base64_encode($upload_img15787); ?>" data-category="<?php echo htmlspecialchars($category15787); ?>" data-status="<?php echo htmlspecialchars($status15787); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15787); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15787); ?>; 
    position:absolute; top:422px; left:961px;'>
                        </div>


                        <!-- ASSET 15788 -->
                        <img src='../image.php?id=15788' style='width:18px; cursor:pointer; position:absolute; top:445px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15788' onclick='fetchAssetData(15788);' class="asset-image" data-id="<?php echo $assetId15788; ?>" data-room="<?php echo htmlspecialchars($room15788); ?>" data-floor="<?php echo htmlspecialchars($floor15788); ?>" data-image="<?php echo base64_encode($upload_img15788); ?>" data-status="<?php echo htmlspecialchars($status15788); ?>" data-category="<?php echo htmlspecialchars($category15788); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15788); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15788); ?>; 
    position:absolute; top:455px; left:869px;'>
                        </div>

                        <!-- ASSET 15789 -->
                        <img src='../image.php?id=15789' style='width:18px; cursor:pointer; position:absolute; top:458px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15789' onclick='fetchAssetData(15789);' class="asset-image" data-id="<?php echo $assetId15789; ?>" data-room="<?php echo htmlspecialchars($room15789); ?>" data-floor="<?php echo htmlspecialchars($floor15789); ?>" data-image="<?php echo base64_encode($upload_img15789); ?>" data-category="<?php echo htmlspecialchars($category15789); ?>" data-status="<?php echo htmlspecialchars($status15789); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15789); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15789); ?>; 
    position:absolute; top:468px; left:869px;'>
                        </div>

                        <!-- ASSET 15790 -->
                        <img src='../image.php?id=15790' style='width:18px; cursor:pointer; position:absolute; top:471px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15790' onclick='fetchAssetData(15790);' class="asset-image" data-id="<?php echo $assetId15790; ?>" data-room="<?php echo htmlspecialchars($room15790); ?>" data-floor="<?php echo htmlspecialchars($floor15790); ?>" data-image="<?php echo base64_encode($upload_img15790); ?>" data-status="<?php echo htmlspecialchars($status15790); ?>" data-category="<?php echo htmlspecialchars($category15790); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15790); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15790); ?>; 
    position:absolute; top:481px; left:869px;'>
                        </div>

                        <!-- ASSET 15791 -->
                        <img src='../image.php?id=15791' style='width:18px; cursor:pointer; position:absolute; top:484px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15791' onclick='fetchAssetData(15791);' class="asset-image" data-id="<?php echo $assetId15791; ?>" data-room="<?php echo htmlspecialchars($room15791); ?>" data-floor="<?php echo htmlspecialchars($floor15791); ?>" data-status="<?php echo htmlspecialchars($status15791); ?>" data-image="<?php echo base64_encode($upload_img15791); ?>" data-category="<?php echo htmlspecialchars($category15791); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15791); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15791); ?>; 
    position:absolute; top:494px; left:869px;'>
                        </div>

                        <!-- ASSET 15792 -->
                        <img src='../image.php?id=15792' style='width:18px; cursor:pointer; position:absolute; top:497px; left:873px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15792' onclick='fetchAssetData(15792);' class="asset-image" data-id="<?php echo $assetId15792; ?>" data-room="<?php echo htmlspecialchars($room15792); ?>" data-floor="<?php echo htmlspecialchars($floor15792); ?>" data-image="<?php echo base64_encode($upload_img15792); ?>" data-status="<?php echo htmlspecialchars($status15792); ?>" data-category="<?php echo htmlspecialchars($category15792); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15792); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15792); ?>; 
    position:absolute; top:507px; left:869px;'>
                        </div>

                        <!-- ASSET 15793 -->
                        <img src='../image.php?id=15793' style='width:18px; cursor:pointer; position:absolute; top:445px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15793' onclick='fetchAssetData(15793);' class="asset-image" data-id="<?php echo $assetId15793; ?>" data-room="<?php echo htmlspecialchars($room15793); ?>" data-floor="<?php echo htmlspecialchars($floor15793); ?>" data-image="<?php echo base64_encode($upload_img15793); ?>" data-status="<?php echo htmlspecialchars($status15793); ?>" data-category="<?php echo htmlspecialchars($category15793); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15793); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15793); ?>; 
    position:absolute; top:455px; left:892px;'>
                        </div>

                        <!-- ASSET 15794 -->
                        <img src='../image.php?id=15794' style='width:18px; cursor:pointer; position:absolute; top:458px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15794' onclick='fetchAssetData(15794);' class="asset-image" data-id="<?php echo $assetId15794; ?>" data-room="<?php echo htmlspecialchars($room15794); ?>" data-floor="<?php echo htmlspecialchars($floor15794); ?>" data-image="<?php echo base64_encode($upload_img15794); ?>" data-status="<?php echo htmlspecialchars($status15794); ?>" data-category="<?php echo htmlspecialchars($category15794); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15794); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15794); ?>; 
    position:absolute; top:468px; left:891px;'>
                        </div>

                        <!-- ASSET 15795 -->
                        <img src='../image.php?id=15795' style='width:18px; cursor:pointer; position:absolute; top:471px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15795' onclick='fetchAssetData(15795);' class="asset-image" data-id="<?php echo $assetId15795; ?>" data-room="<?php echo htmlspecialchars($room15795); ?>" data-floor="<?php echo htmlspecialchars($floor15795); ?>" data-image="<?php echo base64_encode($upload_img15795); ?>" data-status="<?php echo htmlspecialchars($status15795); ?>" data-category="<?php echo htmlspecialchars($category15795); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15795); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15795); ?>; 
    position:absolute; top:481px; left:891px;'>
                        </div>

                        <!-- ASSET 15796 -->
                        <img src='../image.php?id=15796' style='width:18px; cursor:pointer; position:absolute; top:484px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15796' onclick='fetchAssetData(15796);' class="asset-image" data-id="<?php echo $assetId15796; ?>" data-room="<?php echo htmlspecialchars($room15796); ?>" data-floor="<?php echo htmlspecialchars($floor15796); ?>" data-image="<?php echo base64_encode($upload_img15796); ?>" data-category="<?php echo htmlspecialchars($category15796); ?>" data-status="<?php echo htmlspecialchars($status15796); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15796); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15796); ?>; 
    position:absolute; top:494px; left:891px;'>
                        </div>

                        <!-- ASSET 15797 -->
                        <img src='../image.php?id=15797' style='width:18px; cursor:pointer; position:absolute; top:497px; left:896px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15797' onclick='fetchAssetData(15797);' class="asset-image" data-id="<?php echo $assetId15797; ?>" data-room="<?php echo htmlspecialchars($room15797); ?>" data-floor="<?php echo htmlspecialchars($floor15797); ?>" data-image="<?php echo base64_encode($upload_img15797); ?>" data-status="<?php echo htmlspecialchars($status15797); ?>" data-category="<?php echo htmlspecialchars($category15797); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15797); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15797); ?>; 
    position:absolute; top:507px; left:891px;'>
                        </div>

                        <!-- ASSET 15798 -->
                        <img src='../image.php?id=15798' style='width:18px; cursor:pointer; position:absolute; top:445px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15798' onclick='fetchAssetData(15798);' class="asset-image" data-id="<?php echo $assetId15798; ?>" data-room="<?php echo htmlspecialchars($room15798); ?>" data-floor="<?php echo htmlspecialchars($floor15798); ?>" data-image="<?php echo base64_encode($upload_img15798); ?>" data-status="<?php echo htmlspecialchars($status15798); ?>" data-category="<?php echo htmlspecialchars($category15798); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15798); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15798); ?>; 
    position:absolute; top:455px; left:915px;'>
                        </div>

                        <!-- ASSET 15799 -->
                        <img src='../image.php?id=15799' style='width:18px; cursor:pointer; position:absolute; top:458px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15799' onclick='fetchAssetData(15799);' class="asset-image" data-id="<?php echo $assetId15799; ?>" data-room="<?php echo htmlspecialchars($room15799); ?>" data-floor="<?php echo htmlspecialchars($floor15799); ?>" data-status="<?php echo htmlspecialchars($status15799); ?>" data-image="<?php echo base64_encode($upload_img15799); ?>" data-category="<?php echo htmlspecialchars($category15799); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15799); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15799); ?>; 
    position:absolute; top:468px; left:915px;'>
                        </div>

                        <!-- ASSET 15800 -->
                        <img src='../image.php?id=15800' style='width:18px; cursor:pointer; position:absolute; top:471px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15800' onclick='fetchAssetData(15800);' class="asset-image" data-id="<?php echo $assetId15800; ?>" data-room="<?php echo htmlspecialchars($room15800); ?>" data-floor="<?php echo htmlspecialchars($floor15800); ?>" data-image="<?php echo base64_encode($upload_img15800); ?>" data-status="<?php echo htmlspecialchars($status15800); ?>" data-category="<?php echo htmlspecialchars($category15800); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15800); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15800); ?>; 
    position:absolute; top:481px; left:915px;'>
                        </div>

                        <!-- ASSET 15801 -->
                        <img src='../image.php?id=15801' style='width:18px; cursor:pointer; position:absolute; top:484px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15801' onclick='fetchAssetData(15801);' class="asset-image" data-id="<?php echo $assetId15801; ?>" data-room="<?php echo htmlspecialchars($room15801); ?>" data-floor="<?php echo htmlspecialchars($floor15801); ?>" data-status="<?php echo htmlspecialchars($status15801); ?>" data-image="<?php echo base64_encode($upload_img15801); ?>" data-category="<?php echo htmlspecialchars($category15801); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15801); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15801); ?>; 
    position:absolute; top:494px; left:915px;'>
                        </div>

                        <!-- ASSET 15802 -->
                        <img src='../image.php?id=15802' style='width:18px; cursor:pointer; position:absolute; top:497px; left:919px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15802' onclick='fetchAssetData(15802);' class="asset-image" data-id="<?php echo $assetId15802; ?>" data-room="<?php echo htmlspecialchars($room15802); ?>" data-floor="<?php echo htmlspecialchars($floor15802); ?>" data-image="<?php echo base64_encode($upload_img15802); ?>" data-status="<?php echo htmlspecialchars($status15802); ?>" data-category="<?php echo htmlspecialchars($category15802); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15802); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15802); ?>; 
    position:absolute; top:507px; left:915px;'>
                        </div>

                        <!-- ASSET 15803 -->
                        <img src='../image.php?id=15803' style='width:18px; cursor:pointer; position:absolute; top:445px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15803' onclick='fetchAssetData(15803);' class="asset-image" data-id="<?php echo $assetId15803; ?>" data-room="<?php echo htmlspecialchars($room15803); ?>" data-floor="<?php echo htmlspecialchars($floor15803); ?>" data-image="<?php echo base64_encode($upload_img15803); ?>" data-status="<?php echo htmlspecialchars($status15803); ?>" data-category="<?php echo htmlspecialchars($category15803); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15803); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15803); ?>; 
    position:absolute; top:455px; left:938px;'>
                        </div>

                        <!-- ASSET 15804 -->
                        <img src='../image.php?id=15804' style='width:18px; cursor:pointer; position:absolute; top:458px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15804' onclick='fetchAssetData(15804);' class="asset-image" data-id="<?php echo $assetId15804; ?>" data-room="<?php echo htmlspecialchars($room15804); ?>" data-floor="<?php echo htmlspecialchars($floor15804); ?>" data-image="<?php echo base64_encode($upload_img15804); ?>" data-status="<?php echo htmlspecialchars($status15804); ?>" data-category="<?php echo htmlspecialchars($category15804); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15804); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15804); ?>; 
    position:absolute; top:468px; left:938px;'>
                        </div>

                        <!-- ASSET 15805 -->
                        <img src='../image.php?id=15805' style='width:18px; cursor:pointer; position:absolute; top:471px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15805' onclick='fetchAssetData(15805);' class="asset-image" data-id="<?php echo $assetId15805; ?>" data-room="<?php echo htmlspecialchars($room15805); ?>" data-floor="<?php echo htmlspecialchars($floor15805); ?>" data-image="<?php echo base64_encode($upload_img15805); ?>" data-status="<?php echo htmlspecialchars($status15805); ?>" data-category="<?php echo htmlspecialchars($category15805); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15805); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15805); ?>; 
    position:absolute; top:481px; left:938px;'>
                        </div>

                        <!-- ASSET 15806 -->
                        <img src='../image.php?id=15806' style='width:18px; cursor:pointer; position:absolute; top:484px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15806' onclick='fetchAssetData(15806);' class="asset-image" data-id="<?php echo $assetId15806; ?>" data-room="<?php echo htmlspecialchars($room15806); ?>" data-floor="<?php echo htmlspecialchars($floor15806); ?>" data-image="<?php echo base64_encode($upload_img15806); ?>" data-status="<?php echo htmlspecialchars($status15806); ?>" data-category="<?php echo htmlspecialchars($category15806); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15806); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15806); ?>; 
    position:absolute; top:494px; left:938px;'>
                        </div>

                        <!-- ASSET 15807 -->
                        <img src='../image.php?id=15807' style='width:18px; cursor:pointer; position:absolute; top:497px; left:942px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15807' onclick='fetchAssetData(15807);' class="asset-image" data-id="<?php echo $assetId15807; ?>" data-room="<?php echo htmlspecialchars($room15807); ?>" data-floor="<?php echo htmlspecialchars($floor15807); ?>" data-image="<?php echo base64_encode($upload_img15807); ?>" data-status="<?php echo htmlspecialchars($status15807); ?>" data-category="<?php echo htmlspecialchars($category15807); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15807); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15807); ?>; 
    position:absolute; top:507px; left:938px;'>
                        </div>

                        <!-- ASSET 15808 -->
                        <img src='../image.php?id=15808' style='width:18px; cursor:pointer; position:absolute; top:445px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15808' onclick='fetchAssetData(15808);' class="asset-image" data-id="<?php echo $assetId15808; ?>" data-room="<?php echo htmlspecialchars($room15808); ?>" data-floor="<?php echo htmlspecialchars($floor15808); ?>" data-image="<?php echo base64_encode($upload_img15808); ?>" data-status="<?php echo htmlspecialchars($status15808); ?>" data-category="<?php echo htmlspecialchars($category15808); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15808); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15808); ?>; 
    position:absolute; top:455px; left:961px;'>
                        </div>

                        <!-- ASSET 15809 -->
                        <img src='../image.php?id=15809' style='width:18px; cursor:pointer; position:absolute; top:458px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15809' onclick='fetchAssetData(15809);' class="asset-image" data-id="<?php echo $assetId15809; ?>" data-room="<?php echo htmlspecialchars($room15809); ?>" data-floor="<?php echo htmlspecialchars($floor15809); ?>" data-image="<?php echo base64_encode($upload_img15809); ?>" data-status="<?php echo htmlspecialchars($status15809); ?>" data-category="<?php echo htmlspecialchars($category15809); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15809); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15809); ?>; 
    position:absolute; top:468px; left:961px;'>
                        </div>

                        <!-- ASSET 15810 -->
                        <img src='../image.php?id=15810' style='width:18px; cursor:pointer; position:absolute; top:471px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15810' onclick='fetchAssetData(15810);' class="asset-image" data-id="<?php echo $assetId15810; ?>" data-room="<?php echo htmlspecialchars($room15810); ?>" data-floor="<?php echo htmlspecialchars($floor15810); ?>" data-image="<?php echo base64_encode($upload_img15810); ?>" data-status="<?php echo htmlspecialchars($status15810); ?>" data-category="<?php echo htmlspecialchars($category15810); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15810); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15810); ?>; 
    position:absolute; top:481px; left:961px;'>
                        </div>

                        <!-- ASSET 15811 -->
                        <img src='../image.php?id=15811' style='width:18px; cursor:pointer; position:absolute; top:484px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15811' onclick='fetchAssetData(15811);' class="asset-image" data-id="<?php echo $assetId15811; ?>" data-room="<?php echo htmlspecialchars($room15811); ?>" data-floor="<?php echo htmlspecialchars($floor15811); ?>" data-status="<?php echo htmlspecialchars($status15811); ?>" data-image="<?php echo base64_encode($upload_img15811); ?>" data-category="<?php echo htmlspecialchars($category15811); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15811); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15811); ?>; 
    position:absolute; top:494px; left:961px;'>
                        </div>

                        <!-- ASSET 15812 -->
                        <img src='../image.php?id=15812' style='width:18px; cursor:pointer; position:absolute; top:497px; left:965px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15812' onclick='fetchAssetData(15812);' class="asset-image" data-id="<?php echo $assetId15812; ?>" data-room="<?php echo htmlspecialchars($room15812); ?>" data-floor="<?php echo htmlspecialchars($floor15812); ?>" data-image="<?php echo base64_encode($upload_img15812); ?>" data-status="<?php echo htmlspecialchars($status15812); ?>" data-category="<?php echo htmlspecialchars($category15812); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15812); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15812); ?>; 
    position:absolute; top:507px; left:961px;'>
                        </div>

                        <!-- IC206A -->

                        <!-- ASSET 15693 -->
                        <img src='../image.php?id=15693' style='width:15px; cursor:pointer; position:absolute; top:355px; left:485px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15693' onclick='fetchAssetData(15693);' class="asset-image" data-id="<?php echo $assetId15693; ?>" data-room="<?php echo htmlspecialchars($room15693); ?>" data-floor="<?php echo htmlspecialchars($floor15693); ?>" data-image="<?php echo base64_encode($upload_img15693); ?>" data-category="<?php echo htmlspecialchars($category15693); ?>" data-status="<?php echo htmlspecialchars($status15693); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15693); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15693); ?>; 
    position:absolute; top:355px; left:495px;'>
                        </div>

                        <!-- ASSET 15694 -->
                        <img src='../image.php?id=15694' style='width:15px; cursor:pointer; position:absolute; top:430px; left:485px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15694' onclick='fetchAssetData(15694);' class="asset-image" data-id="<?php echo $assetId15694; ?>" data-room="<?php echo htmlspecialchars($room15694); ?>" data-floor="<?php echo htmlspecialchars($floor15694); ?>" data-image="<?php echo base64_encode($upload_img15694); ?>" data-category="<?php echo htmlspecialchars($category15694); ?>" data-status="<?php echo htmlspecialchars($status15694); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15694); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15694); ?>; 
    position:absolute; top:430px; left:495px;'>
                        </div>

                        <!-- ASSET 15696 -->
                        <img src='../image.php?id=15695' style='width:15px; cursor:pointer; position:absolute; top:355px; left:580px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15695' onclick='fetchAssetData(15695);' class="asset-image" data-id="<?php echo $assetId15695; ?>" data-room="<?php echo htmlspecialchars($room15695); ?>" data-floor="<?php echo htmlspecialchars($floor15695); ?>" data-image="<?php echo base64_encode($upload_img15695); ?>" data-category="<?php echo htmlspecialchars($category15695); ?>" data-status="<?php echo htmlspecialchars($status15695); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15695); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15695); ?>; 
    position:absolute; top:355px; left:590px;'>
                        </div>

                        <!-- ASSET 15697 -->
                        <img src='../image.php?id=15697' style='width:15px; cursor:pointer; position:absolute; top:430px; left:580px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15697' onclick='fetchAssetData(15697);' class="asset-image" data-id="<?php echo $assetId15697; ?>" data-room="<?php echo htmlspecialchars($room15697); ?>" data-floor="<?php echo htmlspecialchars($floor15697); ?>" data-image="<?php echo base64_encode($upload_img15697); ?>" data-category="<?php echo htmlspecialchars($category15697); ?>" data-status="<?php echo htmlspecialchars($status15697); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15697); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15697); ?>; 
    position:absolute; top:430px; left:590px;'>
                        </div>

                        <!-- ASSET 15698 -->
                        <img src='../image.php?id=15698' style='width:15px; cursor:pointer; position:absolute; top:430px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15698' onclick='fetchAssetData(15698);' class="asset-image" data-id="<?php echo $assetId15698; ?>" data-room="<?php echo htmlspecialchars($room15698); ?>" data-floor="<?php echo htmlspecialchars($floor15698); ?>" data-image="<?php echo base64_encode($upload_img15698); ?>" data-category="<?php echo htmlspecialchars($category15698); ?>" data-status="<?php echo htmlspecialchars($status15698); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15698); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15698); ?>; 
    position:absolute; top:430px; left:730px;'>
                        </div>

                        <!-- ASSET 15699 -->
                        <img src='../image.php?id=15699' style='width:15px; cursor:pointer; position:absolute; top:355px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15699' onclick='fetchAssetData(15699);' class="asset-image" data-id="<?php echo $assetId15699; ?>" data-room="<?php echo htmlspecialchars($room15699); ?>" data-floor="<?php echo htmlspecialchars($floor15699); ?>" data-image="<?php echo base64_encode($upload_img15699); ?>" data-category="<?php echo htmlspecialchars($category15699); ?>" data-status="<?php echo htmlspecialchars($status15699); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15699); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15699); ?>; 
    position:absolute; top:355px; left:730px;'>
                        </div>

                        <!-- ASSET 15753 -->
                        <img src='../image.php?id=15753' style='width:15px; cursor:pointer; position:absolute; top:426px; left:518px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15753' onclick='fetchAssetData(15753);' class="asset-image" data-id="<?php echo $assetId15753; ?>" data-room="<?php echo htmlspecialchars($room15753); ?>" data-floor="<?php echo htmlspecialchars($floor15753); ?>" data-image="<?php echo base64_encode($upload_img15753); ?>" data-category="<?php echo htmlspecialchars($category15753); ?>" data-status="<?php echo htmlspecialchars($status15753); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15753); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15753); ?>; 
    position:absolute; top:415px; left:523px;'>
                        </div>

                        <!-- ASSET 6498 -->
                        <img src='../image.php?id=6498' style='width:18px; cursor:pointer; position:absolute; top: 426px;6px; left:498px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal6498' onclick='fetchAssetData(6498);' class="asset-image" data-id="<?php echo $assetId6498; ?>" data-room="<?php echo htmlspecialchars($room6498); ?>" data-floor="<?php echo htmlspecialchars($floor6498); ?>" data-image="<?php echo base64_encode($upload_img6498); ?>" data-category="<?php echo htmlspecialchars($category6498); ?>" data-status="<?php echo htmlspecialchars($status6498); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName6498); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status6498); ?>; 
    position:absolute; top:405px; left:764px;'>
                        </div>

                        <!-- ASSET 15696 -->
                        <img src='../image.php?id=15696' style='width:15px; cursor:pointer; position:absolute; top:515px; left:485px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15696' onclick='fetchAssetData(15696);' class="asset-image" data-id="<?php echo $assetId15696; ?>" data-room="<?php echo htmlspecialchars($room15696); ?>" data-floor="<?php echo htmlspecialchars($floor15696); ?>" data-image="<?php echo base64_encode($upload_img15696); ?>" data-category="<?php echo htmlspecialchars($category15696); ?>" data-status="<?php echo htmlspecialchars($status15696); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15696); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15696); ?>; 
    position:absolute; top:515px; left:495px;'>
                        </div>

                        <!-- ASSET 15700 -->
                        <img src='../image.php?id=15700' style='width:15px; cursor:pointer; position:absolute; top:515px; left:580px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15700' onclick='fetchAssetData(15700);' class="asset-image" data-id="<?php echo $assetId15700; ?>" data-room="<?php echo htmlspecialchars($room15700); ?>" data-floor="<?php echo htmlspecialchars($floor15700); ?>" data-image="<?php echo base64_encode($upload_img15700); ?>" data-category="<?php echo htmlspecialchars($category15700); ?>" data-status="<?php echo htmlspecialchars($status15700); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15700); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15700); ?>; 
    position:absolute; top:515px; left:590px;'>
                        </div>

                        <!-- ASSET 15701 -->
                        <img src='../image.php?id=15701' style='width:15px; cursor:pointer; position:absolute; top:515px; left:720px;' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15701' onclick='fetchAssetData(15701);' class="asset-image" data-id="<?php echo $assetId15701; ?>" data-room="<?php echo htmlspecialchars($room15701); ?>" data-floor="<?php echo htmlspecialchars($floor15701); ?>" data-image="<?php echo base64_encode($upload_img15701); ?>" data-category="<?php echo htmlspecialchars($category15701); ?>" data-status="<?php echo htmlspecialchars($status15701); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15701); ?>">
                        <div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status15701); ?>; 
    position:absolute; top:515px; left:730px;'>
                        </div>

                        <!-- ASSET 15702 -->
                        <img src='../image.php?id=15702' style='width:18px; cursor:pointer; position:absolute; top:360px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15702' onclick='fetchAssetData(15702);' class="asset-image" data-id="<?php echo $assetId15702; ?>" data-room="<?php echo htmlspecialchars($room15702); ?>" data-floor="<?php echo htmlspecialchars($floor15702); ?>" data-image="<?php echo base64_encode($upload_img15702); ?>" data-status="<?php echo htmlspecialchars($status15702); ?>" data-category="<?php echo htmlspecialchars($category15702); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15702); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15702); ?>; 
    position:absolute; top:370px; left:599px;'>
                        </div>

                        <!-- ASSET 15703 -->
                        <img src='../image.php?id=15703' style='width:18px; cursor:pointer; position:absolute; top:373px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15703' onclick='fetchAssetData(15703);' class="asset-image" data-id="<?php echo $assetId15703; ?>" data-room="<?php echo htmlspecialchars($room15703); ?>" data-floor="<?php echo htmlspecialchars($floor15703); ?>" data-image="<?php echo base64_encode($upload_img15703); ?>" data-status="<?php echo htmlspecialchars($status15703); ?>" data-category="<?php echo htmlspecialchars($category15703); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15703); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15703); ?>; 
    position:absolute; top:383px; left:599px;'>
                        </div>

                        <!-- ASSET 15704 -->
                        <img src='../image.php?id=15704' style='width:18px; cursor:pointer; position:absolute; top:386px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15704' onclick='fetchAssetData(15704);' class="asset-image" data-id="<?php echo $assetId15704; ?>" data-room="<?php echo htmlspecialchars($room15704); ?>" data-floor="<?php echo htmlspecialchars($floor15704); ?>" data-image="<?php echo base64_encode($upload_img15704); ?>" data-status="<?php echo htmlspecialchars($status15704); ?>" data-category="<?php echo htmlspecialchars($category15704); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15704); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15704); ?>; 
    position:absolute; top:396px; left:599px;'>
                        </div>

                        <!-- ASSET 15705 -->
                        <img src='../image.php?id=15705' style='width:18px; cursor:pointer; position:absolute; top:399px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15705' onclick='fetchAssetData(15705);' class="asset-image" data-id="<?php echo $assetId15705; ?>" data-room="<?php echo htmlspecialchars($room15705); ?>" data-floor="<?php echo htmlspecialchars($floor15705); ?>" data-image="<?php echo base64_encode($upload_img15705); ?>" data-category="<?php echo htmlspecialchars($category15705); ?>" data-status="<?php echo htmlspecialchars($status15705); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15705); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15705); ?>; 
    position:absolute; top:409px; left:599px;'>
                        </div>

                        <!-- ASSET 15706 -->
                        <img src='../image.php?id=15706' style='width:18px; cursor:pointer; position:absolute; top:412px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15706' onclick='fetchAssetData(15706);' class="asset-image" data-id="<?php echo $assetId15706; ?>" data-room="<?php echo htmlspecialchars($room15706); ?>" data-floor="<?php echo htmlspecialchars($floor15706); ?>" data-image="<?php echo base64_encode($upload_img15706); ?>" data-category="<?php echo htmlspecialchars($category15706); ?>" data-status="<?php echo htmlspecialchars($status15706); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15706); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15706); ?>; 
    position:absolute; top:422px; left:599px;'>
                        </div>

                        <!-- ASSET 15707 -->
                        <img src='../image.php?id=15707' style='width:18px; cursor:pointer; position:absolute; top:360px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15707' onclick='fetchAssetData(15707);' class="asset-image" data-id="<?php echo $assetId15707; ?>" data-room="<?php echo htmlspecialchars($room15707); ?>" data-floor="<?php echo htmlspecialchars($floor15707); ?>" data-image="<?php echo base64_encode($upload_img15707); ?>" data-status="<?php echo htmlspecialchars($status15707); ?>" data-category="<?php echo htmlspecialchars($category15707); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15707); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15707); ?>; 
    position:absolute; top:370px; left:625px;'>
                        </div>

                        <!-- ASSET 15708 -->
                        <img src='../image.php?id=15708' style='width:18px; cursor:pointer; position:absolute; top:373px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15708' onclick='fetchAssetData(15708);' class="asset-image" data-id="<?php echo $assetId15708; ?>" data-room="<?php echo htmlspecialchars($room15708); ?>" data-floor="<?php echo htmlspecialchars($floor15708); ?>" data-image="<?php echo base64_encode($upload_img15708); ?>" data-category="<?php echo htmlspecialchars($category15708); ?>" data-status="<?php echo htmlspecialchars($status15708); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15708); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15708); ?>; 
    position:absolute; top:383px; left:625px;'>
                        </div>

                        <!-- ASSET 15709 -->
                        <img src='../image.php?id=15709' style='width:18px; cursor:pointer; position:absolute; top:386px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15709' onclick='fetchAssetData(15709);' class="asset-image" data-id="<?php echo $assetId15709; ?>" data-room="<?php echo htmlspecialchars($room15709); ?>" data-floor="<?php echo htmlspecialchars($floor15709); ?>" data-image="<?php echo base64_encode($upload_img15709); ?>" data-category="<?php echo htmlspecialchars($category15709); ?>" data-status="<?php echo htmlspecialchars($status15709); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15709); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15709); ?>; 
    position:absolute; top:396px; left:625px;'>
                        </div>

                        <!-- ASSET 15710 -->
                        <img src='../image.php?id=15710' style='width:18px; cursor:pointer; position:absolute; top:399px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15710' onclick='fetchAssetData(15710);' class="asset-image" data-id="<?php echo $assetId15710; ?>" data-room="<?php echo htmlspecialchars($room15710); ?>" data-floor="<?php echo htmlspecialchars($floor15710); ?>" data-image="<?php echo base64_encode($upload_img15710); ?>" data-category="<?php echo htmlspecialchars($category15710); ?>" data-status="<?php echo htmlspecialchars($status15710); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15710); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15710); ?>; 
    position:absolute; top:409px; left:625px;'>
                        </div>

                        <!-- ASSET 15711 -->
                        <img src='../image.php?id=15711' style='width:18px; cursor:pointer; position:absolute; top:412px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15711' onclick='fetchAssetData(15711);' class="asset-image" data-id="<?php echo $assetId15711; ?>" data-room="<?php echo htmlspecialchars($room15711); ?>" data-floor="<?php echo htmlspecialchars($floor15711); ?>" data-image="<?php echo base64_encode($upload_img15711); ?>" data-status="<?php echo htmlspecialchars($status15711); ?>" data-category="<?php echo htmlspecialchars($category15711); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15711); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15711); ?>; 
    position:absolute; top:422px; left:625px;'>
                        </div>

                        <!-- ASSET 15712 -->
                        <img src='../image.php?id=15712' style='width:18px; cursor:pointer; position:absolute; top:360px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15712' onclick='fetchAssetData(15712);' class="asset-image" data-id="<?php echo $assetId15712; ?>" data-room="<?php echo htmlspecialchars($room15712); ?>" data-floor="<?php echo htmlspecialchars($floor15712); ?>" data-image="<?php echo base64_encode($upload_img15712); ?>" data-status="<?php echo htmlspecialchars($status15712); ?>" data-category="<?php echo htmlspecialchars($category15712); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15712); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15712); ?>; 
    position:absolute; top:370px; left:648px;'>
                        </div>

                        <!-- ASSET 15713 -->
                        <img src='../image.php?id=15713' style='width:18px; cursor:pointer; position:absolute; top:373px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15713' onclick='fetchAssetData(15713);' class="asset-image" data-id="<?php echo $assetId15713; ?>" data-room="<?php echo htmlspecialchars($room15713); ?>" data-floor="<?php echo htmlspecialchars($floor15713); ?>" data-image="<?php echo base64_encode($upload_img15713); ?>" data-status="<?php echo htmlspecialchars($status15713); ?>" data-category="<?php echo htmlspecialchars($category15713); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15713); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15713); ?>; 
    position:absolute; top:383px; left:648px;'>
                        </div>

                        <!-- ASSET 15714 -->
                        <img src='../image.php?id=15714' style='width:18px; cursor:pointer; position:absolute; top:386px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15714' onclick='fetchAssetData(15714);' class="asset-image" data-id="<?php echo $assetId15714; ?>" data-room="<?php echo htmlspecialchars($room15714); ?>" data-floor="<?php echo htmlspecialchars($floor15714); ?>" data-image="<?php echo base64_encode($upload_img15714); ?>" data-category="<?php echo htmlspecialchars($category15714); ?>" data-status="<?php echo htmlspecialchars($status15714); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15714); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15714); ?>; 
    position:absolute; top:396px; left:648px;'>
                        </div>

                        <!-- ASSET 15715 -->
                        <img src='../image.php?id=15715' style='width:18px; cursor:pointer; position:absolute; top:399px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15715' onclick='fetchAssetData(15715);' class="asset-image" data-id="<?php echo $assetId15715; ?>" data-room="<?php echo htmlspecialchars($room15715); ?>" data-floor="<?php echo htmlspecialchars($floor15715); ?>" data-image="<?php echo base64_encode($upload_img15715); ?>" data-category="<?php echo htmlspecialchars($category15715); ?>" data-status="<?php echo htmlspecialchars($status15715); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15715); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15715); ?>; 
    position:absolute; top:409px; left:648px;'>
                        </div>

                        <!-- ASSET 15716 -->
                        <img src='../image.php?id=15716' style='width:18px; cursor:pointer; position:absolute; top:412px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15716' onclick='fetchAssetData(15716);' class="asset-image" data-id="<?php echo $assetId15716; ?>" data-room="<?php echo htmlspecialchars($room15716); ?>" data-floor="<?php echo htmlspecialchars($floor15716); ?>" data-image="<?php echo base64_encode($upload_img15716); ?>" data-status="<?php echo htmlspecialchars($status15716); ?>" data-category="<?php echo htmlspecialchars($category15716); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15716); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15716); ?>; 
    position:absolute; top:422px; left:648px;'>
                        </div>

                        <!-- ASSET 15717 -->
                        <img src='../image.php?id=15717' style='width:18px; cursor:pointer; position:absolute; top:360px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15717' onclick='fetchAssetData(15717);' class="asset-image" data-id="<?php echo $assetId15717; ?>" data-room="<?php echo htmlspecialchars($room15717); ?>" data-floor="<?php echo htmlspecialchars($floor15717); ?>" data-image="<?php echo base64_encode($upload_img15717); ?>" data-status="<?php echo htmlspecialchars($status15717); ?>" data-category="<?php echo htmlspecialchars($category15717); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15717); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15717); ?>; 
    position:absolute; top:370px; left:671px;'>
                        </div>

                        <!-- ASSET 15718 -->
                        <img src='../image.php?id=15718' style='width:18px; cursor:pointer; position:absolute; top:373px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15718' onclick='fetchAssetData(15718);' class="asset-image" data-id="<?php echo $assetId15718; ?>" data-room="<?php echo htmlspecialchars($room15718); ?>" data-floor="<?php echo htmlspecialchars($floor15718); ?>" data-image="<?php echo base64_encode($upload_img15718); ?>" data-status="<?php echo htmlspecialchars($status15718); ?>" data-category="<?php echo htmlspecialchars($category15718); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15718); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15718); ?>; 
    position:absolute; top:383px; left:671px;'>
                        </div>

                        <!-- ASSET 15719 -->
                        <img src='../image.php?id=15719' style='width:18px; cursor:pointer; position:absolute; top:386px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15719' onclick='fetchAssetData(15719);' class="asset-image" data-id="<?php echo $assetId15719; ?>" data-room="<?php echo htmlspecialchars($room15719); ?>" data-floor="<?php echo htmlspecialchars($floor15719); ?>" data-image="<?php echo base64_encode($upload_img15719); ?>" data-category="<?php echo htmlspecialchars($category15719); ?>" data-status="<?php echo htmlspecialchars($status15719); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15719); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15719); ?>; 
    position:absolute; top:396px; left:671px;'>
                        </div>

                        <!-- ASSET 15720 -->
                        <img src='../image.php?id=15720' style='width:18px; cursor:pointer; position:absolute; top:399px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15720' onclick='fetchAssetData(15720);' class="asset-image" data-id="<?php echo $assetId15720; ?>" data-room="<?php echo htmlspecialchars($room15720); ?>" data-floor="<?php echo htmlspecialchars($floor15720); ?>" data-image="<?php echo base64_encode($upload_img15720); ?>" data-status="<?php echo htmlspecialchars($status15720); ?>" data-category="<?php echo htmlspecialchars($category15720); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15720); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15720); ?>; 
    position:absolute; top:409px; left:671px;'>
                        </div>

                        <!-- ASSET 15721 -->
                        <img src='../image.php?id=15721' style='width:18px; cursor:pointer; position:absolute; top:412px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15721' onclick='fetchAssetData(15721);' class="asset-image" data-id="<?php echo $assetId15721; ?>" data-room="<?php echo htmlspecialchars($room15721); ?>" data-floor="<?php echo htmlspecialchars($floor15721); ?>" data-image="<?php echo base64_encode($upload_img15721); ?>" data-status="<?php echo htmlspecialchars($status15721); ?>" data-category="<?php echo htmlspecialchars($category15721); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15721); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15721); ?>; 
    position:absolute; top:422px; left:671px;'>
                        </div>

                        <!-- ASSET 15722 -->
                        <img src='../image.php?id=15722' style='width:18px; cursor:pointer; position:absolute; top:360px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15722' onclick='fetchAssetData(15722);' class="asset-image" data-id="<?php echo $assetId15722; ?>" data-room="<?php echo htmlspecialchars($room15722); ?>" data-floor="<?php echo htmlspecialchars($floor15722); ?>" data-image="<?php echo base64_encode($upload_img15722); ?>" data-status="<?php echo htmlspecialchars($status15722); ?>" data-category="<?php echo htmlspecialchars($category15722); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15722); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15722); ?>; 
    position:absolute; top:370px; left:694px;'>
                        </div>


                        <!-- ASSET 15723 -->
                        <img src='../image.php?id=15723' style='width:18px; cursor:pointer; position:absolute; top:373px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15723' onclick='fetchAssetData(15723);' class="asset-image" data-id="<?php echo $assetId15723; ?>" data-room="<?php echo htmlspecialchars($room15723); ?>" data-floor="<?php echo htmlspecialchars($floor15723); ?>" data-status="<?php echo htmlspecialchars($status15723); ?>" data-image="<?php echo base64_encode($upload_img15723); ?>" data-category="<?php echo htmlspecialchars($category15723); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15723); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15723); ?>; 
    position:absolute; top:383px; left:694px;'>
                        </div>

                        <!-- ASSET 15724 -->
                        <img src='../image.php?id=15724' style='width:18px; cursor:pointer; position:absolute; top:386px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15724' onclick='fetchAssetData(15724);' class="asset-image" data-id="<?php echo $assetId15724; ?>" data-room="<?php echo htmlspecialchars($room15724); ?>" data-floor="<?php echo htmlspecialchars($floor15724); ?>" data-image="<?php echo base64_encode($upload_img15724); ?>" data-status="<?php echo htmlspecialchars($status15724); ?>" data-category="<?php echo htmlspecialchars($category15724); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15724); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15724); ?>; 
    position:absolute; top:396px; left:694px;'>
                        </div>

                        <!-- ASSET 15725 -->
                        <img src='../image.php?id=15725' style='width:18px; cursor:pointer; position:absolute; top:399px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15725' onclick='fetchAssetData(15725);' class="asset-image" data-id="<?php echo $assetId15725; ?>" data-room="<?php echo htmlspecialchars($room15725); ?>" data-floor="<?php echo htmlspecialchars($floor15725); ?>" data-image="<?php echo base64_encode($upload_img15725); ?>" data-category="<?php echo htmlspecialchars($category15725); ?>" data-status="<?php echo htmlspecialchars($status15725); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15725); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15725); ?>; 
    position:absolute; top:409px; left:694px;'>
                        </div>

                        <!-- ASSET 15726 -->
                        <img src='../image.php?id=15726' style='width:18px; cursor:pointer; position:absolute; top:412px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15726' onclick='fetchAssetData(15726);' class="asset-image" data-id="<?php echo $assetId15726; ?>" data-room="<?php echo htmlspecialchars($room15726); ?>" data-floor="<?php echo htmlspecialchars($floor15726); ?>" data-image="<?php echo base64_encode($upload_img15726); ?>" data-category="<?php echo htmlspecialchars($category15726); ?>" data-status="<?php echo htmlspecialchars($status15726); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15726); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15726); ?>; 
    position:absolute; top:422px; left:694px;'>
                        </div>

                        <!-- ASSET 15727 -->
                        <img src='../image.php?id=15727' style='width:18px; cursor:pointer; position:absolute; top:445px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15727' onclick='fetchAssetData(15727);' class="asset-image" data-id="<?php echo $assetId15727; ?>" data-room="<?php echo htmlspecialchars($room15727); ?>" data-floor="<?php echo htmlspecialchars($floor15727); ?>" data-image="<?php echo base64_encode($upload_img15727); ?>" data-status="<?php echo htmlspecialchars($status15727); ?>" data-category="<?php echo htmlspecialchars($category15727); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15727); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15727); ?>; 
    position:absolute; top:455px; left:599px;'>
                        </div>

                        <!-- ASSET 15728 -->
                        <img src='../image.php?id=15728' style='width:18px; cursor:pointer; position:absolute; top:458px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15728' onclick='fetchAssetData(15728);' class="asset-image" data-id="<?php echo $assetId15728; ?>" data-room="<?php echo htmlspecialchars($room15728); ?>" data-floor="<?php echo htmlspecialchars($floor15728); ?>" data-image="<?php echo base64_encode($upload_img15728); ?>" data-category="<?php echo htmlspecialchars($category15728); ?>" data-status="<?php echo htmlspecialchars($status15728); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15728); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15728); ?>; 
    position:absolute; top:468px; left:599px;'>
                        </div>

                        <!-- ASSET 15729 -->
                        <img src='../image.php?id=15729' style='width:18px; cursor:pointer; position:absolute; top:471px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15729' onclick='fetchAssetData(15729);' class="asset-image" data-id="<?php echo $assetId15729; ?>" data-room="<?php echo htmlspecialchars($room15729); ?>" data-floor="<?php echo htmlspecialchars($floor15729); ?>" data-image="<?php echo base64_encode($upload_img15729); ?>" data-status="<?php echo htmlspecialchars($status15729); ?>" data-category="<?php echo htmlspecialchars($category15729); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15729); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15729); ?>; 
    position:absolute; top:481px; left:599px;'>
                        </div>

                        <!-- ASSET 15730 -->
                        <img src='../image.php?id=15730' style='width:18px; cursor:pointer; position:absolute; top:484px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15730' onclick='fetchAssetData(15730);' class="asset-image" data-id="<?php echo $assetId15730; ?>" data-room="<?php echo htmlspecialchars($room15730); ?>" data-floor="<?php echo htmlspecialchars($floor15730); ?>" data-status="<?php echo htmlspecialchars($status15730); ?>" data-image="<?php echo base64_encode($upload_img15730); ?>" data-category="<?php echo htmlspecialchars($category15730); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15730); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15730); ?>; 
    position:absolute; top:494px; left:599px;'>
                        </div>

                        <!-- ASSET 15731 -->
                        <img src='../image.php?id=15731' style='width:18px; cursor:pointer; position:absolute; top:497px; left:603px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15731' onclick='fetchAssetData(15731);' class="asset-image" data-id="<?php echo $assetId15731; ?>" data-room="<?php echo htmlspecialchars($room15731); ?>" data-floor="<?php echo htmlspecialchars($floor15731); ?>" data-image="<?php echo base64_encode($upload_img15731); ?>" data-status="<?php echo htmlspecialchars($status15731); ?>" data-category="<?php echo htmlspecialchars($category15731); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15731); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15731); ?>; 
    position:absolute; top:507px; left:599px;'>
                        </div>

                        <!-- ASSET 15732 -->
                        <img src='../image.php?id=15732' style='width:18px; cursor:pointer; position:absolute; top:445px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15732' onclick='fetchAssetData(15732);' class="asset-image" data-id="<?php echo $assetId15732; ?>" data-room="<?php echo htmlspecialchars($room15732); ?>" data-floor="<?php echo htmlspecialchars($floor15732); ?>" data-image="<?php echo base64_encode($upload_img15732); ?>" data-status="<?php echo htmlspecialchars($status15732); ?>" data-category="<?php echo htmlspecialchars($category15732); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15732); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15732); ?>; 
    position:absolute; top:455px; left:625px;'>
                        </div>

                        <!-- ASSET 15733 -->
                        <img src='../image.php?id=15733' style='width:18px; cursor:pointer; position:absolute; top:458px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15733' onclick='fetchAssetData(15733);' class="asset-image" data-id="<?php echo $assetId15733; ?>" data-room="<?php echo htmlspecialchars($room15733); ?>" data-floor="<?php echo htmlspecialchars($floor15733); ?>" data-image="<?php echo base64_encode($upload_img15733); ?>" data-status="<?php echo htmlspecialchars($status15733); ?>" data-category="<?php echo htmlspecialchars($category15733); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15733); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15733); ?>; 
    position:absolute; top:468px; left:625px;'>
                        </div>

                        <!-- ASSET 15734 -->
                        <img src='../image.php?id=15734' style='width:18px; cursor:pointer; position:absolute; top:471px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15734' onclick='fetchAssetData(15734);' class="asset-image" data-id="<?php echo $assetId15734; ?>" data-room="<?php echo htmlspecialchars($room15734); ?>" data-floor="<?php echo htmlspecialchars($floor15734); ?>" data-image="<?php echo base64_encode($upload_img15734); ?>" data-status="<?php echo htmlspecialchars($status15734); ?>" data-category="<?php echo htmlspecialchars($category15734); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15734); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15734); ?>; 
    position:absolute; top:481px; left:625px;'>
                        </div>

                        <!-- ASSET 15735 -->
                        <img src='../image.php?id=15735' style='width:18px; cursor:pointer; position:absolute; top:484px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15735' onclick='fetchAssetData(15735);' class="asset-image" data-id="<?php echo $assetId15735; ?>" data-room="<?php echo htmlspecialchars($room15735); ?>" data-floor="<?php echo htmlspecialchars($floor15735); ?>" data-image="<?php echo base64_encode($upload_img15735); ?>" data-category="<?php echo htmlspecialchars($category15735); ?>" data-status="<?php echo htmlspecialchars($status15735); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15735); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15735); ?>; 
    position:absolute; top:494px; left:625px;'>
                        </div>

                        <!-- ASSET 15736 -->
                        <img src='../image.php?id=15736' style='width:18px; cursor:pointer; position:absolute; top:497px; left:629px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15736' onclick='fetchAssetData(15736);' class="asset-image" data-id="<?php echo $assetId15736; ?>" data-room="<?php echo htmlspecialchars($room15736); ?>" data-floor="<?php echo htmlspecialchars($floor15736); ?>" data-image="<?php echo base64_encode($upload_img15736); ?>" data-status="<?php echo htmlspecialchars($status15736); ?>" data-category="<?php echo htmlspecialchars($category15736); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15736); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15736); ?>; 
    position:absolute; top:507px; left:625px;'>
                        </div>

                        <!-- ASSET 15737 -->
                        <img src='../image.php?id=15737' style='width:18px; cursor:pointer; position:absolute; top:445px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15737' onclick='fetchAssetData(15737);' class="asset-image" data-id="<?php echo $assetId15737; ?>" data-room="<?php echo htmlspecialchars($room15737); ?>" data-floor="<?php echo htmlspecialchars($floor15737); ?>" data-image="<?php echo base64_encode($upload_img15737); ?>" data-status="<?php echo htmlspecialchars($status15737); ?>" data-category="<?php echo htmlspecialchars($category15737); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15737); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15737); ?>; 
    position:absolute; top:455px; left:648px;'>
                        </div>

                        <!-- ASSET 15738 -->
                        <img src='../image.php?id=15738' style='width:18px; cursor:pointer; position:absolute; top:458px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15738' onclick='fetchAssetData(15738);' class="asset-image" data-id="<?php echo $assetId15738; ?>" data-room="<?php echo htmlspecialchars($room15738); ?>" data-floor="<?php echo htmlspecialchars($floor15738); ?>" data-status="<?php echo htmlspecialchars($status15738); ?>" data-image="<?php echo base64_encode($upload_img15738); ?>" data-category="<?php echo htmlspecialchars($category15738); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15738); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15738); ?>; 
    position:absolute; top:468px; left:648px;'>
                        </div>


                        <!-- ASSET 15739 -->
                        <img src='../image.php?id=15739' style='width:18px; cursor:pointer; position:absolute; top:471px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15739' onclick='fetchAssetData(15739);' class="asset-image" data-id="<?php echo $assetId15739; ?>" data-room="<?php echo htmlspecialchars($room15739); ?>" data-floor="<?php echo htmlspecialchars($floor15739); ?>" data-image="<?php echo base64_encode($upload_img15739); ?>" data-status="<?php echo htmlspecialchars($status15739); ?>" data-category="<?php echo htmlspecialchars($category15739); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15739); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15739); ?>; 
    position:absolute; top:481px; left:648px;'>
                        </div>

                        <!-- ASSET 15740 -->
                        <img src='../image.php?id=15740' style='width:18px; cursor:pointer; position:absolute; top:484px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15740' onclick='fetchAssetData(15740);' class="asset-image" data-id="<?php echo $assetId15740; ?>" data-room="<?php echo htmlspecialchars($room15740); ?>" data-floor="<?php echo htmlspecialchars($floor15740); ?>" data-status="<?php echo htmlspecialchars($status15740); ?>" data-image="<?php echo base64_encode($upload_img15740); ?>" data-category="<?php echo htmlspecialchars($category15740); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15740); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15740); ?>; 
    position:absolute; top:494px; left:648px;'>
                        </div>

                        <!-- ASSET 15741 -->
                        <img src='../image.php?id=15741' style='width:18px; cursor:pointer; position:absolute; top:497px; left:652px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15741' onclick='fetchAssetData(15741);' class="asset-image" data-id="<?php echo $assetId15741; ?>" data-room="<?php echo htmlspecialchars($room15741); ?>" data-floor="<?php echo htmlspecialchars($floor15741); ?>" data-image="<?php echo base64_encode($upload_img15741); ?>" data-status="<?php echo htmlspecialchars($status15741); ?>" data-category="<?php echo htmlspecialchars($category15741); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15741); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15741); ?>; 
    position:absolute; top:507px; left:648px;'>
                        </div>

                        <!-- ASSET 15742 -->
                        <img src='../image.php?id=15742' style='width:18px; cursor:pointer; position:absolute; top:445px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15742' onclick='fetchAssetData(15742);' class="asset-image" data-id="<?php echo $assetId15742; ?>" data-room="<?php echo htmlspecialchars($room15742); ?>" data-floor="<?php echo htmlspecialchars($floor15742); ?>" data-image="<?php echo base64_encode($upload_img15742); ?>" data-status="<?php echo htmlspecialchars($status15742); ?>" data-category="<?php echo htmlspecialchars($category15742); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15742); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15742); ?>; 
    position:absolute; top:455px; left:671px;'>
                        </div>

                        <!-- ASSET 15743 -->
                        <img src='../image.php?id=15743' style='width:18px; cursor:pointer; position:absolute; top:458px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15743' onclick='fetchAssetData(15743);' class="asset-image" data-id="<?php echo $assetId15743; ?>" data-room="<?php echo htmlspecialchars($room15743); ?>" data-floor="<?php echo htmlspecialchars($floor15743); ?>" data-image="<?php echo base64_encode($upload_img15743); ?>" data-status="<?php echo htmlspecialchars($status15743); ?>" data-category="<?php echo htmlspecialchars($category15743); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15743); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15743); ?>; 
    position:absolute; top:468px; left:671px;'>
                        </div>

                        <!-- ASSET 15744 -->
                        <img src='../image.php?id=15744' style='width:18px; cursor:pointer; position:absolute; top:471px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15744' onclick='fetchAssetData(15744);' class="asset-image" data-id="<?php echo $assetId15744; ?>" data-room="<?php echo htmlspecialchars($room15744); ?>" data-floor="<?php echo htmlspecialchars($floor15744); ?>" data-image="<?php echo base64_encode($upload_img15744); ?>" data-status="<?php echo htmlspecialchars($status15744); ?>" data-category="<?php echo htmlspecialchars($category15744); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15744); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15744); ?>; 
    position:absolute; top:481px; left:671px;'>
                        </div>

                        <!-- ASSET 15745-->
                        <img src='../image.php?id=15745' style='width:18px; cursor:pointer; position:absolute; top:484px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15745' onclick='fetchAssetData(15745);' class="asset-image" data-id="<?php echo $assetId15745; ?>" data-room="<?php echo htmlspecialchars($room15745); ?>" data-floor="<?php echo htmlspecialchars($floor15745); ?>" data-image="<?php echo base64_encode($upload_img15745); ?>" data-status="<?php echo htmlspecialchars($status15745); ?>" data-category="<?php echo htmlspecialchars($category15745); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15745); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15745); ?>; 
    position:absolute; top:494px; left:671px;'>
                        </div>

                        <!-- ASSET 15746 -->
                        <img src='../image.php?id=15746' style='width:18px; cursor:pointer; position:absolute; top:497px; left:675px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15746' onclick='fetchAssetData(15746);' class="asset-image" data-id="<?php echo $assetId15746; ?>" data-room="<?php echo htmlspecialchars($room15746); ?>" data-floor="<?php echo htmlspecialchars($floor15746); ?>" data-image="<?php echo base64_encode($upload_img15746); ?>" data-status="<?php echo htmlspecialchars($status15746); ?>" data-category="<?php echo htmlspecialchars($category15746); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15746); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15746); ?>; 
    position:absolute; top:507px; left:671px;'>
                        </div>

                        <!-- ASSET 15747 -->
                        <img src='../image.php?id=15747' style='width:18px; cursor:pointer; position:absolute; top:445px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15747' onclick='fetchAssetData(15747);' class="asset-image" data-id="<?php echo $assetId15747; ?>" data-room="<?php echo htmlspecialchars($room15747); ?>" data-floor="<?php echo htmlspecialchars($floor15747); ?>" data-image="<?php echo base64_encode($upload_img15747); ?>" data-status="<?php echo htmlspecialchars($status15747); ?>" data-category="<?php echo htmlspecialchars($category15747); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15747); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15747); ?>; 
    position:absolute; top:455px; left:694px;'>
                        </div>

                        <!-- ASSET 15748 -->
                        <img src='../image.php?id=15748' style='width:18px; cursor:pointer; position:absolute; top:458px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15748' onclick='fetchAssetData(15748);' class="asset-image" data-id="<?php echo $assetId15748; ?>" data-room="<?php echo htmlspecialchars($room15748); ?>" data-floor="<?php echo htmlspecialchars($floor15748); ?>" data-image="<?php echo base64_encode($upload_img15748); ?>" data-status="<?php echo htmlspecialchars($status15748); ?>" data-category="<?php echo htmlspecialchars($category15748); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15748); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15748); ?>; 
    position:absolute; top:468px; left:694px;'>
                        </div>

                        <!-- ASSET 15749 -->
                        <img src='../image.php?id=15749' style='width:18px; cursor:pointer; position:absolute; top:471px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15749' onclick='fetchAssetData(15749);' class="asset-image" data-id="<?php echo $assetId15749; ?>" data-room="<?php echo htmlspecialchars($room15749); ?>" data-floor="<?php echo htmlspecialchars($floor15749); ?>" data-image="<?php echo base64_encode($upload_img15749); ?>" data-status="<?php echo htmlspecialchars($status15749); ?>" data-category="<?php echo htmlspecialchars($category15749); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15749); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15749); ?>; 
    position:absolute; top:481px; left:694px;'>
                        </div>

                        <!-- ASSET 15750 -->
                        <img src='../image.php?id=15750' style='width:18px; cursor:pointer; position:absolute; top:484px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15750' onclick='fetchAssetData(15750);' class="asset-image" data-id="<?php echo $assetId15750; ?>" data-room="<?php echo htmlspecialchars($room15750); ?>" data-floor="<?php echo htmlspecialchars($floor15750); ?>" data-status="<?php echo htmlspecialchars($status15750); ?>" data-image="<?php echo base64_encode($upload_img15750); ?>" data-category="<?php echo htmlspecialchars($category15750); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15750); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15750); ?>; 
    position:absolute; top:494px; left:694px;'>
                        </div>

                        <!-- ASSET 15751 -->
                        <img src='../image.php?id=15751' style='width:18px; cursor:pointer; position:absolute; top:497px; left:698px; transform: rotate(180deg);' alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal15751' onclick='fetchAssetData(15751);' class="asset-image" data-id="<?php echo $assetId15751; ?>" data-room="<?php echo htmlspecialchars($room15751); ?>" data-floor="<?php echo htmlspecialchars($floor15751); ?>" data-image="<?php echo base64_encode($upload_img15751); ?>" data-status="<?php echo htmlspecialchars($status15751); ?>" data-category="<?php echo htmlspecialchars($category15751); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName15751); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status15751); ?>; 
    position:absolute; top:507px; left:694px;'>
                        </div>



                        <!--Start of hover-->
                        <div id="hover-asset" class="hover-asset" style="display: none;">
                            <!-- Content will be added dynamically -->
                        </div>
                    </div>

                </div>
                </div>
                </div>
                </div>
                </div>
            </main>
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
                        url: "update_single_notification.php", // The URL to the PHP file
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

        <!-- Start of JS Hover -->
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
        <!-- FOR LEGEND FILTER -->
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