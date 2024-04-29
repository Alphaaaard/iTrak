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



    $assetIds = range(11854, 12188);
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
                header("Location: BABF2.php");
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
                header("Location: BABF2.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
        }
    }

    // Call updateAsset function for each asset ID you want to handle

    $assetIds = range(11854, 12188);
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
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
            <a href="./dashboard.php" class="brand" title="logo">
                <i><img src="../../../src/img/UpKeep.png" alt="" class="logo" /></i>
                <div class="mobile-sidebar-close">
                    <i class="bi bi-arrow-left-circle"></i>
                </div>
            </a>
            <ul class="side-menu top">
                <li>
                    <a href="./dashboard.php">
                        <i class="bi bi-grid"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./attendance-logs.php">
                        <i class="bi bi-calendar-week"></i>
                        <span class="text">Attendance Logs</span>
                    </a>
                </li>
                <li>
                    <a href="./staff.php">
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
                        <a href="./gps.php">
                            <i class="bi bi-crosshair"></i>
                            <span class="text">GPS Tracker</span>
                        </a>
                    </li>
                    <li class="GPS-History">
                        <a href="./gps_history.php">
                            <i class="bi bi-radar"></i>
                            <span class="text">GPS History</span>
                        </a>
                    </li>
                </div>
                <li class="active">
                    <a href="./map.php">
                        <i class="bi bi-map"></i>
                        <span class="text">Map</span>
                    </a>
                </li>
                <li>
                    <a href="./reports.php">
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
                        <a href="./batasan.php">
                            <i class="bi bi-building"></i>
                            <span class="text">Batasan</span>
                        </a>
                    </li>
                    <li class="Map-SanBartolome">
                        <a href="./sanBartolome.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Bartolome</span>
                        </a>
                    </li>
                    <li class="Map-SanFrancisco">
                        <a href="./sanFrancisco.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Francisco</span>
                        </a>
                    </li>
                </div>
                <li>
                    <a href="./archive.php">
                        <i class="bi bi-archive"></i>
                        <span class="text">Archive</span>
                    </a>
                </li>
                <li>
                    <a href="./activity-logs.php">
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
                        <img src="../../../src/floors/bautistaB/BB2F.png" alt="" class="Floor-container">

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/AC.jpg" alt="" class="legend-img">
                                <p>AIRCON</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/CASSETTE-AC.jpg" alt="" class="legend-img">
                                <p>CASSETTE AIRCON</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/CHAIR.jpg" alt="" class="legend-img">
                                <p>CHAIR</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/DOOR.jpg" alt="" class="legend-img">
                                <p>DOOR</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/DESK-TABLE.jpg" alt="" class="legend-img">
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