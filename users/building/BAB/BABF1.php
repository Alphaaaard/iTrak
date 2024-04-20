<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

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

    $assetIds = [11518, 11519, 11520, 11521, 11523, 11524, 11525, 11526, 11527, 11528, 11529, 11530, 11531, 11532, 11533, 11534, 11535, 11536, 11537, 11538, 11539, 11540, 11541, 11542, 11543, 11544, 11545, 11546, 11547, 11548, 11549, 11550, 11551, 11552, 11553, 11554, 11555, 11556, 11557, 11558, 11559, 11560, 11561, 11562, 11563, 11564, 11565, 11566, 11567, 11568, 11569, 11570, 11571, 11572, 11573, 11574, 11575, 11576, 11577, 11578, 11579, 11580, 11581, 11582, 11583, 11584, 11585, 11586, 11587, 11588, 11589, 11590, 11591, 11592, 11593, 11594, 11595, 11596, 11597, 11598, 11599, 11600, 11601, 11602, 11603, 11604, 11605, 11606, 11607, 11608, 11609, 11610, 11611, 11612, 11613, 11614, 11615, 11616, 11617, 11618, 11619, 11620, 11621, 11622, 11623, 11624, 11625, 11626, 11627, 11628, 11629, 11630, 11631, 11632, 11633, 11634, 11635, 11636, 11637, 11638, 11639, 11640, 11641, 11642, 11643, 11644, 11645, 11646, 11647, 11648, 11649, 11650, 11651, 11652, 11653, 11654, 11655];

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
    $assetIds = [11518, 11519, 11520, 11521, 11523, 11524, 11525, 11526, 11527, 11528, 11529, 11530, 11531, 11532, 11533, 11534, 11535, 11536, 11537, 11538, 11539, 11540, 11541, 11542, 11543, 11544, 11545, 11546, 11547, 11548, 11549, 11550, 11551, 11552, 11553, 11554, 11555, 11556, 11557, 11558, 11559, 11560, 11561, 11562, 11563, 11564, 11565, 11566, 11567, 11568, 11569, 11570, 11571, 11572, 11573, 11574, 11575, 11576, 11577, 11578, 11579, 11580, 11581, 11582, 11583, 11584, 11585, 11586, 11587, 11588, 11589, 11590, 11591, 11592, 11593, 11594, 11595, 11596, 11597, 11598, 11599, 11600, 11601, 11602, 11603, 11604, 11605, 11606, 11607, 11608, 11609, 11610, 11611, 11612, 11613, 11614, 11615, 11616, 11617, 11618, 11619, 11620, 11621, 11622, 11623, 11624, 11625, 11626, 11627, 11628, 11629, 11630, 11631, 11632, 11633, 11634, 11635, 11636, 11637, 11638, 11639, 11640, 11641, 11642, 11643, 11644, 11645, 11646, 11647, 11648, 11649, 11650, 11651, 11652, 11653, 11654, 11655]; // Add more asset IDs here
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


                    <!--ASSET 11528 -->
                    <img src='../image.php?id=11528'
                        style='width:15px; cursor:pointer; position:absolute; top:410px; left:330px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11528' onclick='fetchAssetData(11528);'
                        class="asset-image" data-id="<?php echo $assetId11528; ?>"
                        data-room="<?php echo htmlspecialchars($room11528); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11528); ?>"
                        data-image="<?php echo base64_encode($upload_img11528); ?>"
                        data-status="<?php echo htmlspecialchars($status11528); ?>"
                        data-category="<?php echo htmlspecialchars($category11528); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11528); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11528); ?>; 
    position:absolute; top:405px; left:340px;'>
                    </div>

                    <!-- ASSET 11529 -->
                    <img src='../image.php?id=11529'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:330px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11529' onclick='fetchAssetData(11529);'
                        class="asset-image" data-id="<?php echo $assetId11529; ?>"
                        data-room="<?php echo htmlspecialchars($room11529); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11529); ?>"
                        data-image="<?php echo base64_encode($upload_img11529); ?>"
                        data-status="<?php echo htmlspecialchars($status11529); ?>"
                        data-category="<?php echo htmlspecialchars($category11529); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11529); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11529); ?>; 
    position:absolute; top:365px; left:340px;'>
                    </div>

                    <!--ASSET 11530 -->
                    <img src='../image.php?id=11530'
                        style='width:15px; cursor:pointer; position:absolute; top:410px; left:425px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11530' onclick='fetchAssetData(11530);'
                        class="asset-image" data-id="<?php echo $assetId11530; ?>"
                        data-room="<?php echo htmlspecialchars($room11530); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11530); ?>"
                        data-image="<?php echo base64_encode($upload_img11530); ?>"
                        data-status="<?php echo htmlspecialchars($status11530); ?>"
                        data-category="<?php echo htmlspecialchars($category11530); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11530); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11530); ?>; 
    position:absolute; top:405px; left:435px;'>
                    </div>

                    <!-- ASSET 11531 -->
                    <img src='../image.php?id=11531'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:425px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11531' onclick='fetchAssetData(11531);'
                        class="asset-image" data-id="<?php echo $assetId11531; ?>"
                        data-room="<?php echo htmlspecialchars($room11531); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11531); ?>"
                        data-image="<?php echo base64_encode($upload_img11531); ?>"
                        data-status="<?php echo htmlspecialchars($status11531); ?>"
                        data-category="<?php echo htmlspecialchars($category11531); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11531); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11531); ?>; 
    position:absolute; top:365px; left:435px;'>
                    </div>

                    <!-- ASSET 11532 -->
                    <img src='../image.php?id=11532'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:465px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11532' onclick='fetchAssetData(11532);'
                        class="asset-image" data-id="<?php echo $assetId11532; ?>"
                        data-room="<?php echo htmlspecialchars($room11532); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11532); ?>"
                        data-image="<?php echo base64_encode($upload_img11532); ?>"
                        data-status="<?php echo htmlspecialchars($status11532); ?>"
                        data-category="<?php echo htmlspecialchars($category11532); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11532); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11532); ?>; 
    position:absolute; top:365px; left:475px;'>
                    </div>

                    <!-- ASSET 11533 -->
                    <img src='../image.php?id=11533'
                        style='width:15px; cursor:pointer; position:absolute; top:410px; left:465px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11533' onclick='fetchAssetData(11533);'
                        class="asset-image" data-id="<?php echo $assetId11533; ?>"
                        data-room="<?php echo htmlspecialchars($room11533); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11533); ?>"
                        data-image="<?php echo base64_encode($upload_img11533); ?>"
                        data-status="<?php echo htmlspecialchars($status11533); ?>"
                        data-category="<?php echo htmlspecialchars($category11533); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11533); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11533); ?>; 
    position:absolute; top:405px; left:475px;'>
                    </div>

                    <!-- ASSET 11534 -->
                    <img src='../image.php?id=11534'
                        style='width:15px; cursor:pointer; position:absolute; top:383px; left:469px; transform: rotate(0deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11534'
                        onclick='fetchAssetData(11534);' class="asset-image" data-id="<?php echo $assetId11534; ?>"
                        data-room="<?php echo htmlspecialchars($room11534); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11534); ?>"
                        data-image="<?php echo base64_encode($upload_img11534); ?>"
                        data-status="<?php echo htmlspecialchars($status11534); ?>"
                        data-category="<?php echo htmlspecialchars($category11534); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11534); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11534); ?>; 
    position:absolute; top:380px; left:464px;'>
                    </div>

                    <!-- ASSET 11537 -->
                    <img src='../image.php?id=11537'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11537' onclick='fetchAssetData(11537);'
                        class="asset-image" data-id="<?php echo $assetId11537; ?>"
                        data-room="<?php echo htmlspecialchars($room11537); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11537); ?>"
                        data-image="<?php echo base64_encode($upload_img11537); ?>"
                        data-status="<?php echo htmlspecialchars($status11537); ?>"
                        data-category="<?php echo htmlspecialchars($category11537); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11537); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11537); ?>; 
    position:absolute; top:365px; left:515px;'>
                    </div>

                    <!-- ASSET 11538 -->
                    <img src='../image.php?id=11538'
                        style='width:15px; cursor:pointer; position:absolute; top:370px; left:580px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11538' onclick='fetchAssetData(11538);'
                        class="asset-image" data-id="<?php echo $assetId11538; ?>"
                        data-room="<?php echo htmlspecialchars($room11538); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11538); ?>"
                        data-image="<?php echo base64_encode($upload_img11538); ?>"
                        data-status="<?php echo htmlspecialchars($status11538); ?>"
                        data-category="<?php echo htmlspecialchars($category11538); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11538); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11538); ?>; 
    position:absolute; top:365px; left:590px;'>
                    </div>

                    <!-- ASSET 11539 -->
                    <img src='../image.php?id=11539'
                        style='width:15px; cursor:pointer; position:absolute; top:420px; left:505px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11539' onclick='fetchAssetData(11539);'
                        class="asset-image" data-id="<?php echo $assetId11539; ?>"
                        data-room="<?php echo htmlspecialchars($room11539); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11539); ?>"
                        data-image="<?php echo base64_encode($upload_img11539); ?>"
                        data-status="<?php echo htmlspecialchars($status11539); ?>"
                        data-category="<?php echo htmlspecialchars($category11539); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11539); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11539); ?>; 
    position:absolute; top:415px; left:515px;'>
                    </div>

                    <!-- ASSET 11540 -->
                    <img src='../image.php?id=11540'
                        style='width:15px; cursor:pointer; position:absolute; top:420px; left:580px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11540' onclick='fetchAssetData(11540);'
                        class="asset-image" data-id="<?php echo $assetId11540; ?>"
                        data-room="<?php echo htmlspecialchars($room11540); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11540); ?>"
                        data-image="<?php echo base64_encode($upload_img11540); ?>"
                        data-status="<?php echo htmlspecialchars($status11540); ?>"
                        data-category="<?php echo htmlspecialchars($category11540); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11540); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11540); ?>; 
    position:absolute; top:415px; left:590px;'>
                    </div>

                    <!--ASSET 11541 -->
                    <img src='../image.php?id=11541'
                        style='width:15px; cursor:pointer; position:absolute; top:383px; left:490px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11541'
                        onclick='fetchAssetData(11541);' class="asset-image" data-id="<?php echo $assetId11541; ?>"
                        data-room="<?php echo htmlspecialchars($room11541); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11541); ?>"
                        data-image="<?php echo base64_encode($upload_img11541); ?>"
                        data-status="<?php echo htmlspecialchars($status11541); ?>"
                        data-category="<?php echo htmlspecialchars($category11541); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11541); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11541); ?>; 
    position:absolute; top:378px; left:500px;'>
                    </div>

                    <!--ASSET 11542 -->
                    <img src='../image.php?id=11542'
                        style='width:15px; cursor:pointer; position:absolute; top:383px; left:590px; transform: rotate(0deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11542'
                        onclick='fetchAssetData(11542);' class="asset-image" data-id="<?php echo $assetId11542; ?>"
                        data-room="<?php echo htmlspecialchars($room11542); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11542); ?>"
                        data-image="<?php echo base64_encode($upload_img11542); ?>"
                        data-status="<?php echo htmlspecialchars($status11542); ?>"
                        data-category="<?php echo htmlspecialchars($category11542); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11542); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11542); ?>; 
    position:absolute; top:380px; left:590px;'>
                    </div>

                    <!--ASSET 11543 -->
                    <img src='../image.php?id=11543'
                        style='width:15px; cursor:pointer; position:absolute; top:395px; left:545px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11543'
                        onclick='fetchAssetData(11543);' class="asset-image" data-id="<?php echo $assetId11543; ?>"
                        data-room="<?php echo htmlspecialchars($room11543); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11543); ?>"
                        data-image="<?php echo base64_encode($upload_img11543); ?>"
                        data-status="<?php echo htmlspecialchars($status11543); ?>"
                        data-category="<?php echo htmlspecialchars($category11543); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11543); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11543); ?>; 
    position:absolute; top:390px; left:560px;'>
                    </div>

                    <!--ASSET 11545 -->
                    <img src='../image.php?id=11545'
                        style='width:15px; cursor:pointer; position:absolute; top:367px; left:520px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11545'
                        onclick='fetchAssetData(11545);' class="asset-image" data-id="<?php echo $assetId11545; ?>"
                        data-room="<?php echo htmlspecialchars($room11545); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11545); ?>"
                        data-image="<?php echo base64_encode($upload_img11545); ?>"
                        data-status="<?php echo htmlspecialchars($status11545); ?>"
                        data-category="<?php echo htmlspecialchars($category11545); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11545); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11545); ?>; 
    position:absolute; top:380px; left:530px;'>
                    </div>

                    <!--ASSET 11546 -->
                    <img src='../image.php?id=11546'
                        style='width:10px; cursor:pointer; position:absolute; top:368px; left:532px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11546'
                        onclick='fetchAssetData(11546);' class="asset-image" data-id="<?php echo $assetId11546; ?>"
                        data-room="<?php echo htmlspecialchars($room11546); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11546); ?>"
                        data-image="<?php echo base64_encode($upload_img11546); ?>"
                        data-status="<?php echo htmlspecialchars($status11546); ?>"
                        data-category="<?php echo htmlspecialchars($category11546); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11546); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11546); ?>; 
    position:absolute; top:363px; left:542px;'>
                    </div>

                    <!-- ASSET 11547 -->
                    <img src='../image.php?id=11547'
                        style='width:15px; cursor:pointer; position:absolute; top:362px; left:612px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11547' onclick='fetchAssetData(11547);'
                        class="asset-image" data-id="<?php echo $assetId11547; ?>"
                        data-room="<?php echo htmlspecialchars($room11547); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11547); ?>"
                        data-image="<?php echo base64_encode($upload_img11547); ?>"
                        data-status="<?php echo htmlspecialchars($status11547); ?>"
                        data-category="<?php echo htmlspecialchars($category11547); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11547); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11547); ?>; 
    position:absolute; top:357px; left:622px;'>
                    </div>

                    <!-- ASSET 11548 -->
                    <img src='../image.php?id=11548'
                        style='width:15px; cursor:pointer; position:absolute; top:362px; left:680px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11548' onclick='fetchAssetData(11548);'
                        class="asset-image" data-id="<?php echo $assetId11548; ?>"
                        data-room="<?php echo htmlspecialchars($room11548); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11548); ?>"
                        data-image="<?php echo base64_encode($upload_img11548); ?>"
                        data-status="<?php echo htmlspecialchars($status11548); ?>"
                        data-category="<?php echo htmlspecialchars($category11548); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11548); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11548); ?>; 
    position:absolute; top:357px; left:690px;'>
                    </div>

                    <!-- ASSET 11549 -->
                    <img src='../image.php?id=11549'
                        style='width:15px; cursor:pointer; position:absolute; top:400px; left:612px;' alt='Asset Image'
                        data-bs-toggle='modal' data-bs-target='#imageModal11549' onclick='fetchAssetData(11549);'
                        class="asset-image" data-id="<?php echo $assetId11549; ?>"
                        data-room="<?php echo htmlspecialchars($room11549); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11549); ?>"
                        data-image="<?php echo base64_encode($upload_img11549); ?>"
                        data-status="<?php echo htmlspecialchars($status11549); ?>"
                        data-category="<?php echo htmlspecialchars($category11549); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11549); ?>">
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status11549); ?>; 
    position:absolute; top:395px; left:622px;'>
                    </div>

                    <!-- ASSET 11550 -->
                    <img src='../image.php?id=11550'
                        style='width:15px; cursor:pointer; position:absolute; top:380px; left:612px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11550'
                        onclick='fetchAssetData(11550);' class="asset-image" data-id="<?php echo $assetId11550; ?>"
                        data-room="<?php echo htmlspecialchars($room11550); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11550); ?>"
                        data-image="<?php echo base64_encode($upload_img11550); ?>"
                        data-status="<?php echo htmlspecialchars($status11550); ?>"
                        data-category="<?php echo htmlspecialchars($category11550); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11550); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11550); ?>; 
    position:absolute; top:375px; left:622px;'>
                    </div>

                    <!-- ASSET 11552 -->
                    <img src='../image.php?id=11552'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:612px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11552'
                        onclick='fetchAssetData(11552);' class="asset-image" data-id="<?php echo $assetId11552; ?>"
                        data-room="<?php echo htmlspecialchars($room11552); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11552); ?>"
                        data-image="<?php echo base64_encode($upload_img11552); ?>"
                        data-status="<?php echo htmlspecialchars($status11552); ?>"
                        data-category="<?php echo htmlspecialchars($category11552); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11552); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11552); ?>; 
    position:absolute; top:295px; left:622px;'>
                    </div>

                    <!-- ASSET 11553 -->
                    <img src='../image.php?id=11553'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:660px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11553'
                        onclick='fetchAssetData(11553);' class="asset-image" data-id="<?php echo $assetId11553; ?>"
                        data-room="<?php echo htmlspecialchars($room11553); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11553); ?>"
                        data-image="<?php echo base64_encode($upload_img11553); ?>"
                        data-status="<?php echo htmlspecialchars($status11553); ?>"
                        data-category="<?php echo htmlspecialchars($category11553); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11553); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11553); ?>; 
    position:absolute; top:295px; left:670px;'>
                    </div>

                    <!-- ASSET 11554 -->
                    <img src='../image.php?id=11554'
                        style='width:15px; cursor:pointer; position:absolute; top:340px; left:612px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11554'
                        onclick='fetchAssetData(11554);' class="asset-image" data-id="<?php echo $assetId11554; ?>"
                        data-room="<?php echo htmlspecialchars($room11554); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11554); ?>"
                        data-image="<?php echo base64_encode($upload_img11554); ?>"
                        data-status="<?php echo htmlspecialchars($status11554); ?>"
                        data-category="<?php echo htmlspecialchars($category11554); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11554); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11554); ?>; 
    position:absolute; top:335px; left:622px;'>
                    </div>

                    <!-- ASSET 11555 -->
                    <img src='../image.php?id=11555'
                        style='width:15px; cursor:pointer; position:absolute; top:340px; left:660px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11555'
                        onclick='fetchAssetData(11555);' class="asset-image" data-id="<?php echo $assetId11555; ?>"
                        data-room="<?php echo htmlspecialchars($room11555); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11555); ?>"
                        data-image="<?php echo base64_encode($upload_img11555); ?>"
                        data-status="<?php echo htmlspecialchars($status11555); ?>"
                        data-category="<?php echo htmlspecialchars($category11555); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11555); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11555); ?>; 
    position:absolute; top:335px; left:670px;'>
                    </div>

                    <!-- ASSET 11556 -->
                    <img src='../image.php?id=11556'
                        style='width:15px; cursor:pointer; position:absolute; top:314px; left:612px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11556'
                        onclick='fetchAssetData(11556);' class="asset-image" data-id="<?php echo $assetId11556; ?>"
                        data-room="<?php echo htmlspecialchars($room11556); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11556); ?>"
                        data-image="<?php echo base64_encode($upload_img11556); ?>"
                        data-status="<?php echo htmlspecialchars($status11556); ?>"
                        data-category="<?php echo htmlspecialchars($category11556); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11556); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11556); ?>; 
    position:absolute; top:314px; left:625px;'>
                    </div>

                    <!-- ASSET 11557 DOOR -->
                    <!-- <img src='../image.php?id=11557'
                        style='width:15px; cursor:pointer; position:absolute; top:314px; left:612px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11557'
                        onclick='fetchAssetData(11557);' class="asset-image" data-id="<?php echo $assetId11557; ?>"
                        data-room="<?php echo htmlspecialchars($room11557); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11557); ?>"
                        data-image="<?php echo base64_encode($upload_img11557); ?>"
                        data-status="<?php echo htmlspecialchars($status11557); ?>"
                        data-category="<?php echo htmlspecialchars($category11557); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11557); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11557); ?>; 
    position:absolute; top:314px; left:625px;'>
                    </div> -->

                    <!-- ASSET 11558 -->
                    <img src='../image.php?id=11558'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:720px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11558'
                        onclick='fetchAssetData(11558);' class="asset-image" data-id="<?php echo $assetId11558; ?>"
                        data-room="<?php echo htmlspecialchars($room11558); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11558); ?>"
                        data-image="<?php echo base64_encode($upload_img11558); ?>"
                        data-status="<?php echo htmlspecialchars($status11558); ?>"
                        data-category="<?php echo htmlspecialchars($category11558); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11558); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11558); ?>; 
    position:absolute; top:295px; left:730px;'>
                    </div>

                    <!-- ASSET 11559 -->
                    <img src='../image.php?id=11559'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:750px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11559'
                        onclick='fetchAssetData(11559);' class="asset-image" data-id="<?php echo $assetId11559; ?>"
                        data-room="<?php echo htmlspecialchars($room11559); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11559); ?>"
                        data-image="<?php echo base64_encode($upload_img11559); ?>"
                        data-status="<?php echo htmlspecialchars($status11559); ?>"
                        data-category="<?php echo htmlspecialchars($category11559); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11559); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11559); ?>; 
    position:absolute; top:295px; left:760px;'>
                    </div>

                    <!-- ASSET 11560 -->
                    <img src='../image.php?id=11560'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:780px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11560'
                        onclick='fetchAssetData(11560);' class="asset-image" data-id="<?php echo $assetId11560; ?>"
                        data-room="<?php echo htmlspecialchars($room11560); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11560); ?>"
                        data-image="<?php echo base64_encode($upload_img11560); ?>"
                        data-status="<?php echo htmlspecialchars($status11560); ?>"
                        data-category="<?php echo htmlspecialchars($category11560); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11560); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11560); ?>; 
    position:absolute; top:295px; left:790px;'>
                    </div>

                    <!-- ASSET 11561 -->
                    <img src='../image.php?id=11561'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:810px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11561'
                        onclick='fetchAssetData(11561);' class="asset-image" data-id="<?php echo $assetId11561; ?>"
                        data-room="<?php echo htmlspecialchars($room11561); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11561); ?>"
                        data-image="<?php echo base64_encode($upload_img11561); ?>"
                        data-status="<?php echo htmlspecialchars($status11561); ?>"
                        data-category="<?php echo htmlspecialchars($category11561); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11561); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11561); ?>; 
    position:absolute; top:295px; left:820px;'>
                    </div>

                    <!-- ASSET 11562 -->
                    <img src='../image.php?id=11562'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:840px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11562'
                        onclick='fetchAssetData(11562);' class="asset-image" data-id="<?php echo $assetId11562; ?>"
                        data-room="<?php echo htmlspecialchars($room11562); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11562); ?>"
                        data-image="<?php echo base64_encode($upload_img11562); ?>"
                        data-status="<?php echo htmlspecialchars($status11562); ?>"
                        data-category="<?php echo htmlspecialchars($category11562); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11562); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11562); ?>; 
    position:absolute; top:295px; left:850px;'>
                    </div>

                    <!-- ASSET 11563 -->
                    <img src='../image.php?id=11563'
                        style='width:15px; cursor:pointer; position:absolute; top:300px; left:870px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11563'
                        onclick='fetchAssetData(11563);' class="asset-image" data-id="<?php echo $assetId11563; ?>"
                        data-room="<?php echo htmlspecialchars($room11563); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11563); ?>"
                        data-image="<?php echo base64_encode($upload_img11563); ?>"
                        data-status="<?php echo htmlspecialchars($status11563); ?>"
                        data-category="<?php echo htmlspecialchars($category11563); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11563); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11563); ?>; 
    position:absolute; top:295px; left:880px;'>
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
                    <div class='modal fade' id='imageModal<?php echo $assetId; ?>' tabindex='-1'
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
                                            value="<?php echo htmlspecialchars($assetId); ?>">
                                        <!--START DIV FOR IMAGE -->
                                        <!--First Row-->
                                        <!--IMAGE HERE-->
                                        <div class="col-12 center-content">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($upload_img); ?>"
                                                alt="No Image" style="width: 100%; max-width: 50px; height: 50px;">
                                        </div>
                                        <!--END DIV FOR IMAGE -->
                                        <div class="col-4" style="display:none">
                                            <label for="assetId" class="form-label">Tracking #:</label>
                                            <input type="text" class="form-control" id="assetId" name="assetId"
                                                value="<?php echo htmlspecialchars($assetId); ?>" readonly />
                                        </div>

                                        <!--Second Row-->
                                        <div class="col-6">
                                            <input type="text" class="form-control" id="room" name="room"
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
                                                name="category" value="<?php echo htmlspecialchars($category); ?>"
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
                                                <option value="Working" <?php echo ($status == 'Working') ? 'selected="selected"' : ''; ?>>Working</option>
                                                <option value="Under Maintenance" <?php echo ($status == 'Under Maintenance') ? 'selected="selected"' : ''; ?>>Under Maintenance</option>
                                                <option value="For Replacement" <?php echo ($status == 'For Replacement') ? 'selected="selected"' : ''; ?>>For Replacement</option>
                                                <option value="Need Repair" <?php echo ($status == 'Need Repair') ? 'selected="selected"' : ''; ?>>Need Repair</option>
                                            </select>
                                        </div>
                                        <div class="col-4" style="display:none">
                                            <label for="assignedName" class="form-label">Assigned Name:</label>
                                            <input type="text" class="form-control" id="assignedName" name="assignedName"
                                                value="<?php echo htmlspecialchars($assignedName); ?>" readonly />
                                        </div>
                                        <div class="col-4" style="display:none">
                                            <label for="assignedBy" class="form-label">Assigned By:</label>
                                            <input type="text" class="form-control" id="assignedBy" name="assignedBy"
                                                value="<?php echo htmlspecialchars($assignedBy); ?>" readonly />
                                        </div>
                                        <!--End of Fourth Row-->
                                        <!--Fifth Row-->
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="description" name="description"
                                                value="<?php echo htmlspecialchars($description); ?>" />
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