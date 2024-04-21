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

    $assetIds = [11518, 11519, 11520, 11521, 11523, 11524, 11525, 11526, 11527, 11528, 11529, 11530, 11531, 11532, 11533, 11534, 11535, 11536, 11537, 11538, 11539, 11540, 11541, 11542, 11543, 11544, 11545, 11546, 11547, 11548, 11549, 11550, 11551, 11552, 11553, 11554, 11555, 11556, 11557, 11558, 11559, 11560, 11561, 11562, 11563, 11564, 11565, 11566, 11567, 11568, 11569, 11570, 11571, 11572, 11573, 11574, 11575, 11576, 11577, 11578, 11579, 11580, 11581, 11582, 11583, 11584, 11585, 11586, 11587, 11588, 11589, 11590, 11591, 11592, 11593, 11594, 11595, 11596, 11597, 11598, 11599, 11600, 11601, 11602, 11603, 11604, 11605, 11606, 11607, 11608, 11609, 11610, 11611, 11612, 11613, 11614, 11615, 11616, 11617, 11618, 11619, 11620, 11621, 11622, 11623, 11624, 11625, 11626, 11627, 11628, 11629, 11630, 11631, 11632, 11633, 11634, 11635, 11636, 11637, 11638, 11639, 11640, 11641, 11642, 11643, 11644, 11645, 11646, 11647, 11648, 11649, 11650, 11651, 11652, 11653, 11654, 11655, 11656, 11657, 11658, 11659, 11660, 11661, 11662, 11663, 11664, 11665, 11666, 11667, 11668, 11669, 11670, 11671, 11672, 11673, 11674, 11675, 11676, 11677, 11678, 11679, 11680, 11681, 11682, 11683, 11684, 11685, 11686, 11687, 11688, 11689, 11690, 11691, 11692, 11693, 11694, 11695, 11696, 11697, 11698, 11699, 11700, 11701, 11702, 11703, 11704, 11705, 11706, 11707, 11708, 11709, 11710, 11711, 11712, 11713, 11714, 11715, 11716, 11717, 11718, 11719, 11720, 11721, 11722, 11723, 11724, 11725, 11726, 11727, 11728, 11729, 11730, 11731, 11732, 11733, 11734, 11735, 11736, 11737, 11738, 11739, 11740, 11741, 11742, 11743, 11744, 11745, 11746, 11747, 11748, 11749, 11750, 11751, 11752, 11753, 11754, 11755, 11756, 11757, 11758, 11759, 11760, 11761, 11762, 11763, 11764, 11765, 11766, 11767, 11768, 11769, 11770, 11771, 11772, 11773, 11774, 11775, 11776, 11777, 11778, 11779, 11780, 11781, 11782, 11783, 11784, 11785, 11786, 11787, 11788, 11789, 11790, 11791, 11792, 11793, 11794, 11795, 11796, 11797, 11798, 11799, 11800, 11801, 11802, 11803, 11804, 11805, 11806, 11807, 11808, 11809];

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
    $assetIds = [11518, 11519, 11520, 11521, 11523, 11524, 11525, 11526, 11527, 11528, 11529, 11530, 11531, 11532, 11533, 11534, 11535, 11536, 11537, 11538, 11539, 11540, 11541, 11542, 11543, 11544, 11545, 11546, 11547, 11548, 11549, 11550, 11551, 11552, 11553, 11554, 11555, 11556, 11557, 11558, 11559, 11560, 11561, 11562, 11563, 11564, 11565, 11566, 11567, 11568, 11569, 11570, 11571, 11572, 11573, 11574, 11575, 11576, 11577, 11578, 11579, 11580, 11581, 11582, 11583, 11584, 11585, 11586, 11587, 11588, 11589, 11590, 11591, 11592, 11593, 11594, 11595, 11596, 11597, 11598, 11599, 11600, 11601, 11602, 11603, 11604, 11605, 11606, 11607, 11608, 11609, 11610, 11611, 11612, 11613, 11614, 11615, 11616, 11617, 11618, 11619, 11620, 11621, 11622, 11623, 11624, 11625, 11626, 11627, 11628, 11629, 11630, 11631, 11632, 11633, 11634, 11635, 11636, 11637, 11638, 11639, 11640, 11641, 11642, 11643, 11644, 11645, 11646, 11647, 11648, 11649, 11650, 11651, 11652, 11653, 11654, 11655, 11656, 11657, 11658, 11659, 11660, 11661, 11662, 11663, 11664, 11665, 11666, 11667, 11668, 11669, 11670, 11671, 11672, 11673, 11674, 11675, 11676, 11677, 11678, 11679, 11680, 11681, 11682, 11683, 11684, 11685, 11686, 11687, 11688, 11689, 11690, 11691, 11692, 11693, 11694, 11695, 11696, 11697, 11698, 11699, 11700, 11701, 11702, 11703, 11704, 11705, 11706, 11707, 11708, 11709, 11710, 11711, 11712, 11713, 11714, 11715, 11716, 11717, 11718, 11719, 11720, 11721, 11722, 11723, 11724, 11725, 11726, 11727, 11728, 11729, 11730, 11731, 11732, 11733, 11734, 11735, 11736, 11737, 11738, 11739, 11740, 11741, 11742, 11743, 11744, 11745, 11746, 11747, 11748, 11749, 11750, 11751, 11752, 11753, 11754, 11755, 11756, 11757, 11758, 11759, 11760, 11761, 11762, 11763, 11764, 11765, 11766, 11767, 11768, 11769, 11770, 11771, 11772, 11773, 11774, 11775, 11776, 11777, 11778, 11779, 11780, 11781, 11782, 11783, 11784, 11785, 11786, 11787, 11788, 11789, 11790, 11791, 11792, 11793, 11794, 11795, 11796, 11797, 11798, 11799, 11800, 11801, 11802, 11803, 11804, 11805, 11806, 11807, 11808, 11809]; // Add more asset IDs here
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
              
   <!--ASSET 11518 -->
   <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:150px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11518'
                        onclick='fetchAssetData(11518);' class="asset-image" data-id="<?php echo $assetId11518; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:190px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11519'
                        onclick='fetchAssetData(11519);' class="asset-image" data-id="<?php echo $assetId11519; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:395px; left:150px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11520'
                        onclick='fetchAssetData(11520);' class="asset-image" data-id="<?php echo $assetId11520; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:395px; left:190px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11521'
                        onclick='fetchAssetData(11521);' class="asset-image" data-id="<?php echo $assetId11521; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:220px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11523'
                        onclick='fetchAssetData(11523);' class="asset-image" data-id="<?php echo $assetId11523; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:270px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11524'
                        onclick='fetchAssetData(11524);' class="asset-image" data-id="<?php echo $assetId11524; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:270px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11525'
                        onclick='fetchAssetData(11525);' class="asset-image" data-id="<?php echo $assetId11525; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:220px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11526'
                        onclick='fetchAssetData(11526);' class="asset-image" data-id="<?php echo $assetId11526; ?>"
                        data-room="<?php echo htmlspecialchars($room11526); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11526); ?>"
                        data-image="<?php echo base64_encode($upload_img11526); ?>"
                        data-status="<?php echo htmlspecialchars($status11526); ?>"
                        data-category="<?php echo htmlspecialchars($category11526); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11526); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11526); ?>; 
    position:absolute; top:405px; left:230px;'>
                    </div>




                    <!--ASSET 11528 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:330px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11528'
                        onclick='fetchAssetData(11528);' class="asset-image" data-id="<?php echo $assetId11528; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:330px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11529'
                        onclick='fetchAssetData(11529);' class="asset-image" data-id="<?php echo $assetId11529; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:425px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11530'
                        onclick='fetchAssetData(11530);' class="asset-image" data-id="<?php echo $assetId11530; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:425px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11531'
                        onclick='fetchAssetData(11531);' class="asset-image" data-id="<?php echo $assetId11531; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:465px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11532'
                        onclick='fetchAssetData(11532);' class="asset-image" data-id="<?php echo $assetId11532; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:410px; left:465px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11533'
                        onclick='fetchAssetData(11533);' class="asset-image" data-id="<?php echo $assetId11533; ?>"
                        data-room="<?php echo htmlspecialchars($room11533); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11533); ?>"
                        data-image="<?php echo base64_encode($upload_img11533); ?>"
                        data-status="<?php echo htmlspecialchars($status11533); ?>"
                        data-category="<?php echo htmlspecialchars($category11533); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11533); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11533); ?>; 
    position:absolute; top:405px; left:475px;'>
                    </div>


                    <!-- ASSET 11537 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:505px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11537'
                        onclick='fetchAssetData(11537);' class="asset-image" data-id="<?php echo $assetId11537; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:370px; left:580px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11538'
                        onclick='fetchAssetData(11538);' class="asset-image" data-id="<?php echo $assetId11538; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:420px; left:505px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11539'
                        onclick='fetchAssetData(11539);' class="asset-image" data-id="<?php echo $assetId11539; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:420px; left:580px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11540'
                        onclick='fetchAssetData(11540);' class="asset-image" data-id="<?php echo $assetId11540; ?>"
                        data-room="<?php echo htmlspecialchars($room11540); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11540); ?>"
                        data-image="<?php echo base64_encode($upload_img11540); ?>"
                        data-status="<?php echo htmlspecialchars($status11540); ?>"
                        data-category="<?php echo htmlspecialchars($category11540); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11540); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11540); ?>; 
    position:absolute; top:415px; left:590px;'>
                    </div>

                    <!-- ASSET 11547 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:362px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11547'
                        onclick='fetchAssetData(11547);' class="asset-image" data-id="<?php echo $assetId11547; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:362px; left:680px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11548'
                        onclick='fetchAssetData(11548);' class="asset-image" data-id="<?php echo $assetId11548; ?>"
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11549'
                        onclick='fetchAssetData(11549);' class="asset-image" data-id="<?php echo $assetId11549; ?>"
                        data-room="<?php echo htmlspecialchars($room11549); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11549); ?>"
                        data-image="<?php echo base64_encode($upload_img11549); ?>"
                        data-status="<?php echo htmlspecialchars($status11549); ?>"
                        data-category="<?php echo htmlspecialchars($category11549); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11549); ?>">
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status11549); ?>; 
    position:absolute; top:395px; left:622px;'>
                    </div>



                    <!-- ASSET 11552 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:300px; left:612px; transform: rotate(180deg);'' alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:300px; left:660px;' alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:340px; left:612px; transform: rotate(180deg);'' alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:340px; left:660px;' alt='
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



                    <!-- ASSET 11557 DOOR -->
                    <!-- <img src='../image.php?id=11557'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:314px; left:612px; transform: rotate(180deg);'' alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:300px; left:720px;'
                        alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:300px; left:750px;'
                        alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:300px; left:780px;'
                        alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:300px; left:810px;'
                        alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1;  z-index: 1;cursor:pointer; position:absolute; top:300px; left:840px;'
                        alt='
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
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:300px; left:870px;'
                        alt='
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

                    <!-- ASSET 11564 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:330px; left:720px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11564'
                        onclick='fetchAssetData(11564);' class="asset-image" data-id="<?php echo $assetId11564; ?>"
                        data-room="<?php echo htmlspecialchars($room11564); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11564); ?>"
                        data-image="<?php echo base64_encode($upload_img11564); ?>"
                        data-status="<?php echo htmlspecialchars($status11564); ?>"
                        data-category="<?php echo htmlspecialchars($category11564); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11564); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; z-index:1; background-color: <?php echo getStatusColor($status11564); ?>; 
    position:absolute; top:325px; left:730px;'>
                    </div>

                    <!-- ASSET 11565 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;cursor:pointer; position:absolute; top:390px; left:870px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11565'
                        onclick='fetchAssetData(11565);' class="asset-image" data-id="<?php echo $assetId11565; ?>"
                        data-room="<?php echo htmlspecialchars($room11565); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11565); ?>"
                        data-image="<?php echo base64_encode($upload_img11565); ?>"
                        data-status="<?php echo htmlspecialchars($status11565); ?>"
                        data-category="<?php echo htmlspecialchars($category11565); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11565); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11565); ?>; 
    position:absolute; top:385px; left:880px;'>
                    </div>

                    <!-- ASSET 11566 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:330px; left:750px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11566'
                        onclick='fetchAssetData(11566);' class="asset-image" data-id="<?php echo $assetId11566; ?>"
                        data-room="<?php echo htmlspecialchars($room11566); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11566); ?>"
                        data-image="<?php echo base64_encode($upload_img11566); ?>"
                        data-status="<?php echo htmlspecialchars($status11566); ?>"
                        data-category="<?php echo htmlspecialchars($category11566); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11566); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11566); ?>; 
    position:absolute; top:325px; left:760px;'>
                    </div>

                    <!-- ASSET 11567 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1;z-index: 1;  cursor:pointer; position:absolute; top:330px; left:780px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11567'
                        onclick='fetchAssetData(11567);' class="asset-image" data-id="<?php echo $assetId11567; ?>"
                        data-room="<?php echo htmlspecialchars($room11567); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11567); ?>"
                        data-image="<?php echo base64_encode($upload_img11567); ?>"
                        data-status="<?php echo htmlspecialchars($status11567); ?>"
                        data-category="<?php echo htmlspecialchars($category11567); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11567); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11567); ?>; 
    position:absolute; top:325px; left:790px;'>
                    </div>

                    <!-- ASSET 11568 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1; cursor:pointer; position:absolute; top:330px; left:810px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11568'
                        onclick='fetchAssetData(11568);' class="asset-image" data-id="<?php echo $assetId11568; ?>"
                        data-room="<?php echo htmlspecialchars($room11568); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11568); ?>"
                        data-image="<?php echo base64_encode($upload_img11568); ?>"
                        data-status="<?php echo htmlspecialchars($status11568); ?>"
                        data-category="<?php echo htmlspecialchars($category11568); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11568); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11568); ?>; 
    position:absolute; top:325px; left:820px;'>
                    </div>

                    <!-- ASSET 11568 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:330px; left:810px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11568'
                        onclick='fetchAssetData(11568);' class="asset-image" data-id="<?php echo $assetId11568; ?>"
                        data-room="<?php echo htmlspecialchars($room11568); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11568); ?>"
                        data-image="<?php echo base64_encode($upload_img11568); ?>"
                        data-status="<?php echo htmlspecialchars($status11568); ?>"
                        data-category="<?php echo htmlspecialchars($category11568); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11568); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11568); ?>; 
    position:absolute; top:325px; left:820px;'>
                    </div>

                    <!--ASSET 11569 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:330px; left:840px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11569'
                        onclick='fetchAssetData(11569);' class="asset-image" data-id="<?php echo $assetId11569; ?>"
                        data-room="<?php echo htmlspecialchars($room11569); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11569); ?>"
                        data-image="<?php echo base64_encode($upload_img11569); ?>"
                        data-status="<?php echo htmlspecialchars($status11569); ?>"
                        data-category="<?php echo htmlspecialchars($category11569); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11569); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11569); ?>; 
    position:absolute; top:325px; left:850px;'>
                    </div>

                    <!--ASSET 11570 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:330px; left:870px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11570'
                        onclick='fetchAssetData(11570);' class="asset-image" data-id="<?php echo $assetId11570; ?>"
                        data-room="<?php echo htmlspecialchars($room11570); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11570); ?>"
                        data-image="<?php echo base64_encode($upload_img11570); ?>"
                        data-status="<?php echo htmlspecialchars($status11570); ?>"
                        data-category="<?php echo htmlspecialchars($category11570); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11570); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11570); ?>; 
    position:absolute; top:325px; left:880px;'>
                    </div>

                    <!--ASSET 11571 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:360px; left:720px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11571'
                        onclick='fetchAssetData(11571);' class="asset-image" data-id="<?php echo $assetId11571; ?>"
                        data-room="<?php echo htmlspecialchars($room11571); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11571); ?>"
                        data-image="<?php echo base64_encode($upload_img11571); ?>"
                        data-status="<?php echo htmlspecialchars($status11571); ?>"
                        data-category="<?php echo htmlspecialchars($category11571); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11571); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11571); ?>; 
    position:absolute; top:355px; left:730px;'>
                    </div>

                    <!--ASSET 11572 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:360px; left:750px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11572'
                        onclick='fetchAssetData(11572);' class="asset-image" data-id="<?php echo $assetId11572; ?>"
                        data-room="<?php echo htmlspecialchars($room11572); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11572); ?>"
                        data-image="<?php echo base64_encode($upload_img11572); ?>"
                        data-status="<?php echo htmlspecialchars($status11572); ?>"
                        data-category="<?php echo htmlspecialchars($category11572); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11572); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11572); ?>; 
    position:absolute; top:355px; left:760px;'>
                    </div>


                    <!--ASSET 11573 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1;  z-index: 1; cursor:pointer; position:absolute; top:360px; left:780px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11573'
                        onclick='fetchAssetData(11573);' class="asset-image" data-id="<?php echo $assetId11573; ?>"
                        data-room="<?php echo htmlspecialchars($room11573); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11573); ?>"
                        data-image="<?php echo base64_encode($upload_img11573); ?>"
                        data-status="<?php echo htmlspecialchars($status11573); ?>"
                        data-category="<?php echo htmlspecialchars($category11573); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11573); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11573); ?>; 
    position:absolute; top:355px; left:790px;'>
                    </div>

                    <!--ASSET 11574 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:360px; left:810px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11574'
                        onclick='fetchAssetData(11574);' class="asset-image" data-id="<?php echo $assetId11574; ?>"
                        data-room="<?php echo htmlspecialchars($room11574); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11574); ?>"
                        data-image="<?php echo base64_encode($upload_img11574); ?>"
                        data-status="<?php echo htmlspecialchars($status11574); ?>"
                        data-category="<?php echo htmlspecialchars($category11574); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11574); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11574); ?>; 
    position:absolute; top:355px; left:820px;'>
                    </div>

                    <!--ASSET 11575 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index: 1;  cursor:pointer; position:absolute; top:360px; left:840px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11575'
                        onclick='fetchAssetData(11575);' class="asset-image" data-id="<?php echo $assetId11575; ?>"
                        data-room="<?php echo htmlspecialchars($room11575); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11575); ?>"
                        data-image="<?php echo base64_encode($upload_img11575); ?>"
                        data-status="<?php echo htmlspecialchars($status11575); ?>"
                        data-category="<?php echo htmlspecialchars($category11575); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11575); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11575); ?>; 
    position:absolute; top:355px; left:850px;'>
                    </div>

                    <!--ASSET 11576 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index:1; cursor:pointer; position:absolute; top:360px; left:870px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11576'
                        onclick='fetchAssetData(11576);' class="asset-image" data-id="<?php echo $assetId11576; ?>"
                        data-room="<?php echo htmlspecialchars($room11576); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11576); ?>"
                        data-image="<?php echo base64_encode($upload_img11576); ?>"
                        data-status="<?php echo htmlspecialchars($status11576); ?>"
                        data-category="<?php echo htmlspecialchars($category11576); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11576); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11576); ?>; 
    position:absolute; top:355px; left:880px;'>
                    </div>

                    <!--ASSET 11577 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index:1; cursor:pointer; position:absolute; top:390px; left:720px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11577'
                        onclick='fetchAssetData(11577);' class="asset-image" data-id="<?php echo $assetId11577; ?>"
                        data-room="<?php echo htmlspecialchars($room11577); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11577); ?>"
                        data-image="<?php echo base64_encode($upload_img11577); ?>"
                        data-status="<?php echo htmlspecialchars($status11577); ?>"
                        data-category="<?php echo htmlspecialchars($category11577); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11577); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11577); ?>; 
    position:absolute; top:385px; left:730px;'>
                    </div>

                    <!--ASSET 11578 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index:1; cursor:pointer; position:absolute; top:390px; left:750px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11578'
                        onclick='fetchAssetData(11578);' class="asset-image" data-id="<?php echo $assetId11578; ?>"
                        data-room="<?php echo htmlspecialchars($room11578); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11578); ?>"
                        data-image="<?php echo base64_encode($upload_img11578); ?>"
                        data-status="<?php echo htmlspecialchars($status11578); ?>"
                        data-category="<?php echo htmlspecialchars($category11578); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11578); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11578); ?>; 
    position:absolute; top:385px; left:760px;'>
                    </div>

                    <!--ASSET 11579 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index:1; cursor:pointer; position:absolute; top:390px; left:780px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11579'
                        onclick='fetchAssetData(11579);' class="asset-image" data-id="<?php echo $assetId11579; ?>"
                        data-room="<?php echo htmlspecialchars($room11579); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11579); ?>"
                        data-image="<?php echo base64_encode($upload_img11579); ?>"
                        data-status="<?php echo htmlspecialchars($status11579); ?>"
                        data-category="<?php echo htmlspecialchars($category11579); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11579); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11579); ?>; 
    position:absolute; top:385px; left:790px;'>
                    </div>

                    <!--ASSET 11580 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; z-index:1; cursor:pointer; position:absolute; top:390px; left:810px;'
                        alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11580'
                        onclick='fetchAssetData(11580);' class="asset-image" data-id="<?php echo $assetId11580; ?>"
                        data-room="<?php echo htmlspecialchars($room11580); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11580); ?>"
                        data-image="<?php echo base64_encode($upload_img11580); ?>"
                        data-status="<?php echo htmlspecialchars($status11580); ?>"
                        data-category="<?php echo htmlspecialchars($category11580); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11580); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11580); ?>; 
    position:absolute; top:385px; left:820px;'>
                    </div>

                    <!--ASSET 11581 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:390px; left:840px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11581'
                        onclick='fetchAssetData(11581);' class="asset-image" data-id="<?php echo $assetId11581; ?>"
                        data-room="<?php echo htmlspecialchars($room11581); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11581); ?>"
                        data-image="<?php echo base64_encode($upload_img11581); ?>"
                        data-status="<?php echo htmlspecialchars($status11581); ?>"
                        data-category="<?php echo htmlspecialchars($category11581); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11581); ?>">
                    <div
                        style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11581); ?>; position:absolute; top:385px; left:850px;'>
                    </div>

                    <!-- ASSET 11527 -->
                    <img src='../image.php?id=11527'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:415px; left:270px; transform: rotate(90deg);'' alt='
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
                    <!-- ASSET 11534 -->
                    <img src='../image.php?id=11534'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:383px; left:469px; transform: rotate(0deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11534'
                        onclick='fetchAssetData(11534);' class="asset-image" data-id="<?php echo $assetId11534; ?>"
                       data-room="
                    <?php echo htmlspecialchars($room11534); ?>"
                    data-floor="
                    <?php echo htmlspecialchars($floor11534); ?>"
                    data-image="
                    <?php echo base64_encode($upload_img11534); ?>"
                        data-status="<?php echo htmlspecialchars($status11534); ?>"
                        data-category="<?php echo htmlspecialchars($category11534); ?>"
                    data-assignedname="
                    <?php echo htmlspecialchars($assignedName11534); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11534); ?>; 
    position:absolute; top:380px; left:464px;'>
                    </div>
                    <!--ASSET 11541 -->
                    <img src='../image.php?id=11541'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:383px; left:490px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11541'
                        onclick='fetchAssetData(11541);' class="asset-image" data-id="<?php echo $assetId11541; ?>"
                        data-room="<?php echo htmlspecialchars($room11541); ?>" data-floor="<?php echo htmlspecialchars($floor11541); ?>"
                        data-image=" <?php echo base64_encode($upload_img11541); ?>"
                    data-status="
                    <?php echo htmlspecialchars($status11541); ?>"
                    data-category="
                    <?php echo htmlspecialchars($category11541); ?>"
                    data-assignedname="
                    <?php echo htmlspecialchars($assignedName11541); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11541); ?>; 
    position:absolute; top:378px; left:500px;'>
                    </div>



                    <!--ASSET 11542 -->
                    <img src='../image.php?id=11542'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:383px; left:590px; transform: rotate(0deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11542'
                        onclick='fetchAssetData(11542);' class="asset-image" data-id="<?php echo $assetId11542; ?>"
                        data-room="<?php echo htmlspecialchars($room11542); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11542); ?>" data-image="<?php echo base64_encode($upload_img11542); ?>"
                        data-status=" <?php echo htmlspecialchars($status11542); ?>"
                    data-category="
                    <?php echo htmlspecialchars($category11542); ?>"
                    data-assignedname="
                    <?php echo htmlspecialchars($assignedName11542); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11542); ?>; 
    position:absolute; top:380px; left:590px;'>
                    </div>

                    <!--ASSET 11543 -->
                    <img src='../image.php?id=11543'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:395px; left:545px;' alt='
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
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:367px; left:520px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11545'
                        onclick=' fetchAssetData(11545);' class="asset-image" data-id="<?php echo $assetId11545; ?>"
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
                        data-floor="<?php echo htmlspecialchars($floor11546); ?>" data-image="<?php echo base64_encode($upload_img11546); ?>"
                        data-status=" <?php echo htmlspecialchars($status11546); ?>"
                    data-category="
                    <?php echo htmlspecialchars($category11546); ?>"
                    data-assignedname="
                    <?php echo htmlspecialchars($assignedName11546); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11546); ?>; 
    position:absolute; top:363px; left:542px;'>
                    </div>

                    <!-- ASSET 11550 -->
                    <img src='../image.php?id=11550'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:380px; left:612px; transform: rotate(180deg);'' alt='
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

                    <!-- ASSET 11556 -->
                    <img src='../image.php?id=11556'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:314px; left:612px; transform: rotate(180deg);'' alt='
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
                    <!--ASSET 11582 -->
                    <img src='../image.php?id=11582'
                        style='width:18px; cursor:pointer; position:absolute; top:315px; left:725px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11582'
                        onclick='fetchAssetData(11582);' class="asset-image" data-id="<?php echo $assetId11582; ?>"
                        data-room="<?php echo htmlspecialchars($room11582); ?>" data-floor="<?php echo htmlspecialchars($floor11582); ?>" data-image="<?php echo base64_encode($upload_img11582); ?>"
                        data-status="<?php echo htmlspecialchars($status11582); ?>" data-category="<?php echo htmlspecialchars($category11582); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName11582); ?>"> <div style='width:7px; height:7px; border-radius:50%;
                        background-color: <?php echo getStatusColor($status11582); ?>; position:absolute; top:310px;
                        left:720px;'>    
                </div>

                <!--ASSET 11583 -->
                <img src='../image.php?id=11583'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:740px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11583'
                    onclick='fetchAssetData(11583);' class="asset-image" data-id="<?php echo $assetId11583; ?>"
                    data-room="<?php echo htmlspecialchars($room11583); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11583); ?>"
                    data-image="<?php echo base64_encode($upload_img11583); ?>"
                    data-status="<?php echo htmlspecialchars($status11583); ?>"
                    data-category="<?php echo htmlspecialchars($category11583); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11583); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11583); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11584 -->
                <img src='../image.php?id=11584'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:755px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11584'
                    onclick='fetchAssetData(11584);' class="asset-image" data-id="<?php echo $assetId11584; ?>"
                    data-room="<?php echo htmlspecialchars($room11584); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11584); ?>"
                    data-image="<?php echo base64_encode($upload_img11584); ?>"
                    data-status="<?php echo htmlspecialchars($status11584); ?>"
                    data-category="<?php echo htmlspecialchars($category11584); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11584); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11584); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11585 -->
                <img src='../image.php?id=11585'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:770px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11585'
                    onclick='fetchAssetData(11585);' class="asset-image" data-id="<?php echo $assetId11585; ?>"
                    data-room="<?php echo htmlspecialchars($room11585); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11585); ?>"
                    data-image="<?php echo base64_encode($upload_img11585); ?>"
                    data-status="<?php echo htmlspecialchars($status11585); ?>"
                    data-category="<?php echo htmlspecialchars($category11585); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11585); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11585); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11586 -->
                <img src='../image.php?id=11586'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:785px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11586'
                    onclick='fetchAssetData(11586);' class="asset-image" data-id="<?php echo $assetId11586; ?>"
                    data-room="<?php echo htmlspecialchars($room11586); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11586); ?>"
                    data-image="<?php echo base64_encode($upload_img11586); ?>"
                    data-status="<?php echo htmlspecialchars($status11586); ?>"
                    data-category="<?php echo htmlspecialchars($category11586); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11586); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11586); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11587 -->
                <img src='../image.php?id=11587'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11587'
                    onclick='fetchAssetData(11587);' class="asset-image" data-id="<?php echo $assetId11587; ?>"
                    data-room="<?php echo htmlspecialchars($room11587); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11587); ?>"
                    data-image="<?php echo base64_encode($upload_img11587); ?>"
                    data-status="<?php echo htmlspecialchars($status11587); ?>"
                    data-category="<?php echo htmlspecialchars($category11587); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11587); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11587); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11588 -->
                <img src='../image.php?id=11588'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:815px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11588'
                    onclick='fetchAssetData(11588);' class="asset-image" data-id="<?php echo $assetId11588; ?>"
                    data-room="<?php echo htmlspecialchars($room11588); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11588); ?>"
                    data-image="<?php echo base64_encode($upload_img11588); ?>"
                    data-status="<?php echo htmlspecialchars($status11588); ?>"
                    data-category="<?php echo htmlspecialchars($category11588); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11588); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11588); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11589 -->
                <img src='../image.php?id=11589'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:830px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11589'
                    onclick='fetchAssetData(11589);' class="asset-image" data-id="<?php echo $assetId11589; ?>"
                    data-room="<?php echo htmlspecialchars($room11589); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11589); ?>"
                    data-image="<?php echo base64_encode($upload_img11589); ?>"
                    data-status="<?php echo htmlspecialchars($status11589); ?>"
                    data-category="<?php echo htmlspecialchars($category11589); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11589); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11589); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11590 -->
                <img src='../image.php?id=11590'
                    style='width:18px; cursor:pointer; position:absolute; top:315px; left:845px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11590'
                    onclick='fetchAssetData(11590);' class="asset-image" data-id="<?php echo $assetId11590; ?>"
                    data-room="<?php echo htmlspecialchars($room11590); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11590); ?>"
                    data-image="<?php echo base64_encode($upload_img11590); ?>"
                    data-status="<?php echo htmlspecialchars($status11590); ?>"
                    data-category="<?php echo htmlspecialchars($category11590); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11590); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11590); ?>; 
    position:absolute; top:310px; left:720px;'>
                </div>

                <!--ASSET 11592 -->
                <img src='../image.php?id=11592'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:726px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11592'
                    onclick='fetchAssetData(11592);' class="asset-image" data-id="<?php echo $assetId11592; ?>"
                    data-room="<?php echo htmlspecialchars($room11592); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11592); ?>"
                    data-image="<?php echo base64_encode($upload_img11592); ?>"
                    data-status="<?php echo htmlspecialchars($status11592); ?>"
                    data-category="<?php echo htmlspecialchars($category11592); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11592); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11592); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11593 -->
                <img src='../image.php?id=11593'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:741px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11593'
                    onclick='fetchAssetData(11593);' class="asset-image" data-id="<?php echo $assetId11593; ?>"
                    data-room="<?php echo htmlspecialchars($room11593); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11593); ?>"
                    data-image="<?php echo base64_encode($upload_img11593); ?>"
                    data-status="<?php echo htmlspecialchars($status11593); ?>"
                    data-category="<?php echo htmlspecialchars($category11593); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11593); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11593); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11594 -->
                <img src='../image.php?id=11594'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:756px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11594'
                    onclick='fetchAssetData(11594);' class="asset-image" data-id="<?php echo $assetId11594; ?>"
                    data-room="<?php echo htmlspecialchars($room11594); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11594); ?>"
                    data-image="<?php echo base64_encode($upload_img11594); ?>"
                    data-status="<?php echo htmlspecialchars($status11594); ?>"
                    data-category="<?php echo htmlspecialchars($category11594); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11594); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11594); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11595 -->
                <img src='../image.php?id=11595'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:771px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11595'
                    onclick='fetchAssetData(11595);' class="asset-image" data-id="<?php echo $assetId11595; ?>"
                    data-room="<?php echo htmlspecialchars($room11595); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11595); ?>"
                    data-image="<?php echo base64_encode($upload_img11595); ?>"
                    data-status="<?php echo htmlspecialchars($status11595); ?>"
                    data-category="<?php echo htmlspecialchars($category11595); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11595); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11595); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11596 -->
                <img src='../image.php?id=11596'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:786px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11596'
                    onclick='fetchAssetData(11596);' class="asset-image" data-id="<?php echo $assetId11596; ?>"
                    data-room="<?php echo htmlspecialchars($room11596); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11596); ?>"
                    data-image="<?php echo base64_encode($upload_img11596); ?>"
                    data-status="<?php echo htmlspecialchars($status11596); ?>"
                    data-category="<?php echo htmlspecialchars($category11596); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11596); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11596); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11597 -->
                <img src='../image.php?id=11597'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:801px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11597'
                    onclick='fetchAssetData(11597);' class="asset-image" data-id="<?php echo $assetId11597; ?>"
                    data-room="<?php echo htmlspecialchars($room11597); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11597); ?>"
                    data-image="<?php echo base64_encode($upload_img11597); ?>"
                    data-status="<?php echo htmlspecialchars($status11597); ?>"
                    data-category="<?php echo htmlspecialchars($category11597); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11597); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11597); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>


                <!--ASSET 11598 -->
                <img src='../image.php?id=11598'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:816px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11598'
                    onclick='fetchAssetData(11598);' class="asset-image" data-id="<?php echo $assetId11598; ?>"
                    data-room="<?php echo htmlspecialchars($room11598); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11598); ?>"
                    data-image="<?php echo base64_encode($upload_img11598); ?>"
                    data-status="<?php echo htmlspecialchars($status11598); ?>"
                    data-category="<?php echo htmlspecialchars($category11598); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11598); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11598); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11599 -->
                <img src='../image.php?id=11599'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:831px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11599'
                    onclick='fetchAssetData(11599);' class="asset-image" data-id="<?php echo $assetId11599; ?>"
                    data-room="<?php echo htmlspecialchars($room11599); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11599); ?>"
                    data-image="<?php echo base64_encode($upload_img11599); ?>"
                    data-status="<?php echo htmlspecialchars($status11599); ?>"
                    data-category="<?php echo htmlspecialchars($category11599); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11599); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11599); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11600 -->
                <img src='../image.php?id=11600'
                    style='width:18px; cursor:pointer; position:absolute; top:325px; left:846px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11600'
                    onclick='fetchAssetData(11600);' class="asset-image" data-id="<?php echo $assetId11600; ?>"
                    data-room="<?php echo htmlspecialchars($room11600); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11600); ?>"
                    data-image="<?php echo base64_encode($upload_img11600); ?>"
                    data-status="<?php echo htmlspecialchars($status11600); ?>"
                    data-category="<?php echo htmlspecialchars($category11600); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11600); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11600); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11602 -->
                <img src='../image.php?id=11602'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:725px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11602'
                    onclick='fetchAssetData(11602);' class="asset-image" data-id="<?php echo $assetId11602; ?>"
                    data-room="<?php echo htmlspecialchars($room11602); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11602); ?>"
                    data-image="<?php echo base64_encode($upload_img11602); ?>"
                    data-status="<?php echo htmlspecialchars($status11602); ?>"
                    data-category="<?php echo htmlspecialchars($category11602); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11602); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11602); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>

                <!--ASSET 11603 -->
                <img src='../image.php?id=11603'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:740px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11603'
                    onclick='fetchAssetData(11603);' class="asset-image" data-id="<?php echo $assetId11603; ?>"
                    data-room="<?php echo htmlspecialchars($room11603); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11603); ?>"
                    data-image="<?php echo base64_encode($upload_img11603); ?>"
                    data-status="<?php echo htmlspecialchars($status11603); ?>"
                    data-category="<?php echo htmlspecialchars($category11603); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11603); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11603); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>



                <!--ASSET 11605 -->
                <img src='../image.php?id=11605'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:755px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11605'
                    onclick='fetchAssetData(11605);' class="asset-image" data-id="<?php echo $assetId11605; ?>"
                    data-room="<?php echo htmlspecialchars($room11605); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11605); ?>"
                    data-image="<?php echo base64_encode($upload_img11605); ?>"
                    data-status="<?php echo htmlspecialchars($status11605); ?>"
                    data-category="<?php echo htmlspecialchars($category11605); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11605); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11605); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>

                <!--ASSET 11606 -->
                <img src='../image.php?id=11606'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:770px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11606'
                    onclick='fetchAssetData(11606);' class="asset-image" data-id="<?php echo $assetId11606; ?>"
                    data-room="<?php echo htmlspecialchars($room11606); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11606); ?>"
                    data-image="<?php echo base64_encode($upload_img11606); ?>"
                    data-status="<?php echo htmlspecialchars($status11606); ?>"
                    data-category="<?php echo htmlspecialchars($category11606); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11606); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11606); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>

                <!--ASSET 11607 -->
                <img src='../image.php?id=11607'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:785px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11607'
                    onclick='fetchAssetData(11607);' class="asset-image" data-id="<?php echo $assetId11607; ?>"
                    data-room="<?php echo htmlspecialchars($room11607); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11607); ?>"
                    data-image="<?php echo base64_encode($upload_img11607); ?>"
                    data-status="<?php echo htmlspecialchars($status11607); ?>"
                    data-category="<?php echo htmlspecialchars($category11607); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11607); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11607); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>

                <!--ASSET 11608 -->
                <img src='../image.php?id=11608'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11608'
                    onclick='fetchAssetData(11608);' class="asset-image" data-id="<?php echo $assetId11608; ?>"
                    data-room="<?php echo htmlspecialchars($room11608); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11608); ?>"
                    data-image="<?php echo base64_encode($upload_img11608); ?>"
                    data-status="<?php echo htmlspecialchars($status11608); ?>"
                    data-category="<?php echo htmlspecialchars($category11608); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11608); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11608); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>

                <!--ASSET 11609 -->
                <img src='../image.php?id=11609'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:815px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11609'
                    onclick='fetchAssetData(11609);' class="asset-image" data-id="<?php echo $assetId11609; ?>"
                    data-room="<?php echo htmlspecialchars($room11609); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11609); ?>"
                    data-image="<?php echo base64_encode($upload_img11609); ?>"
                    data-status="<?php echo htmlspecialchars($status11609); ?>"
                    data-category="<?php echo htmlspecialchars($category11609); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11609); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11609); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>

                <!--ASSET 11610 -->
                <img src='../image.php?id=11610'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:830px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11610'
                    onclick='fetchAssetData(11610);' class="asset-image" data-id="<?php echo $assetId11610; ?>"
                    data-room="<?php echo htmlspecialchars($room11610); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11610); ?>"
                    data-image="<?php echo base64_encode($upload_img11610); ?>"
                    data-status="<?php echo htmlspecialchars($status11610); ?>"
                    data-category="<?php echo htmlspecialchars($category11610); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11610); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11610); ?>; 
    position:absolute; top:365px; left:720px;'>
                </div>



                <!--ASSET 11612 -->
                <img src='../image.php?id=11612'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:726px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11612'
                    onclick='fetchAssetData(11612);' class="asset-image" data-id="<?php echo $assetId11612; ?>"
                    data-room="<?php echo htmlspecialchars($room11612); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11612); ?>"
                    data-image="<?php echo base64_encode($upload_img11612); ?>"
                    data-status="<?php echo htmlspecialchars($status11612); ?>"
                    data-category="<?php echo htmlspecialchars($category11612); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11612); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11612); ?>; 
    position:absolute; top:335px; left:720px;'>
                </div>

                <!--ASSET 11613 -->
                <img src='../image.php?id=11613'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:741px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11613'
                    onclick='fetchAssetData(11613);' class="asset-image" data-id="<?php echo $assetId11613; ?>"
                    data-room="<?php echo htmlspecialchars($room11613); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11613); ?>"
                    data-image="<?php echo base64_encode($upload_img11613); ?>"
                    data-status="<?php echo htmlspecialchars($status11613); ?>"
                    data-category="<?php echo htmlspecialchars($category11613); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11613); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11613); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11614 -->
                <img src='../image.php?id=11614'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:756px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11614'
                    onclick='fetchAssetData(11614);' class="asset-image" data-id="<?php echo $assetId11614; ?>"
                    data-room="<?php echo htmlspecialchars($room11614); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11614); ?>"
                    data-image="<?php echo base64_encode($upload_img11614); ?>"
                    data-status="<?php echo htmlspecialchars($status11614); ?>"
                    data-category="<?php echo htmlspecialchars($category11614); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11614); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11614); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11615 -->
                <img src='../image.php?id=11615'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:771px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11615'
                    onclick='fetchAssetData(11615);' class="asset-image" data-id="<?php echo $assetId11615; ?>"
                    data-room="<?php echo htmlspecialchars($room11615); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11615); ?>"
                    data-image="<?php echo base64_encode($upload_img11615); ?>"
                    data-status="<?php echo htmlspecialchars($status11615); ?>"
                    data-category="<?php echo htmlspecialchars($category11615); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11615); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11615); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11616 -->
                <img src='../image.php?id=11616'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:786px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11616'
                    onclick='fetchAssetData(11616);' class="asset-image" data-id="<?php echo $assetId11616; ?>"
                    data-room="<?php echo htmlspecialchars($room11616); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11616); ?>"
                    data-image="<?php echo base64_encode($upload_img11616); ?>"
                    data-status="<?php echo htmlspecialchars($status11616); ?>"
                    data-category="<?php echo htmlspecialchars($category11616); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11616); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11616); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11617 -->
                <img src='../image.php?id=11617'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:801px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11617'
                    onclick='fetchAssetData(11617);' class="asset-image" data-id="<?php echo $assetId11617; ?>"
                    data-room="<?php echo htmlspecialchars($room11617); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11617); ?>"
                    data-image="<?php echo base64_encode($upload_img11617); ?>"
                    data-status="<?php echo htmlspecialchars($status11617); ?>"
                    data-category="<?php echo htmlspecialchars($category11617); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11617); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11617); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11611 -->
                <img src='../image.php?id=11611'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:816px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11611'
                    onclick='fetchAssetData(11611);' class="asset-image" data-id="<?php echo $assetId11611; ?>"
                    data-room="<?php echo htmlspecialchars($room11611); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11611); ?>"
                    data-image="<?php echo base64_encode($upload_img11611); ?>"
                    data-status="<?php echo htmlspecialchars($status11611); ?>"
                    data-category="<?php echo htmlspecialchars($category11611); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11611); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11611); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11604 -->
                <img src='../image.php?id=11604'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:831px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11604'
                    onclick='fetchAssetData(11604);' class="asset-image" data-id="<?php echo $assetId11604; ?>"
                    data-room="<?php echo htmlspecialchars($room11604); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11604); ?>"
                    data-image="<?php echo base64_encode($upload_img11604); ?>"
                    data-status="<?php echo htmlspecialchars($status11604); ?>"
                    data-category="<?php echo htmlspecialchars($category11604); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11604); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11604); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11601 -->
                <img src='../image.php?id=11601'
                    style='width:18px; cursor:pointer; position:absolute; top:380px; left:846px;transform: rotate(-180deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11601'
                    onclick='fetchAssetData(11601);' class="asset-image" data-id="<?php echo $assetId11601; ?>"
                    data-room="<?php echo htmlspecialchars($room11601); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11601); ?>"
                    data-image="<?php echo base64_encode($upload_img11601); ?>"
                    data-status="<?php echo htmlspecialchars($status11601); ?>"
                    data-category="<?php echo htmlspecialchars($category11601); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11601); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11601); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11602 -->
                <img src='../image.php?id=11602'
                    style='width:18px; cursor:pointer; position:absolute; top:370px; left:846px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11602'
                    onclick='fetchAssetData(11602);' class="asset-image" data-id="<?php echo $assetId11602; ?>"
                    data-room="<?php echo htmlspecialchars($room11602); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11602); ?>"
                    data-image="<?php echo base64_encode($upload_img11602); ?>"
                    data-status="<?php echo htmlspecialchars($status11602); ?>"
                    data-category="<?php echo htmlspecialchars($category11602); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11602); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11602); ?>; 
    position:absolute; top:390px; left:720px;'>
                </div>

                <!--ASSET 11618 -->
                <img src='../image.php?id=11618'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:728px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11618'
                    onclick='fetchAssetData(11618);' class="asset-image" data-id="<?php echo $assetId11618; ?>"
                    data-room="<?php echo htmlspecialchars($room11618); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11618); ?>"
                    data-image="<?php echo base64_encode($upload_img11618); ?>"
                    data-status="<?php echo htmlspecialchars($status11618); ?>"
                    data-category="<?php echo htmlspecialchars($category11618); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11618); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11618); ?>; 
    position:absolute; top:300px; left:738px;'>
                </div>

                <!--ASSET 11619 -->
                <img src='../image.php?id=11619'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:743px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11619'
                    onclick='fetchAssetData(11619);' class="asset-image" data-id="<?php echo $assetId11619; ?>"
                    data-room="<?php echo htmlspecialchars($room11619); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11619); ?>"
                    data-image="<?php echo base64_encode($upload_img11619); ?>"
                    data-status="<?php echo htmlspecialchars($status11619); ?>"
                    data-category="<?php echo htmlspecialchars($category11619); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11619); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11619); ?>; 
    position:absolute; top:300px; left:753px;'>
                </div>

                <!--ASSET 11620 -->
                <img src='../image.php?id=11620'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:758px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11620'
                    onclick='fetchAssetData(11620);' class="asset-image" data-id="<?php echo $assetId11620; ?>"
                    data-room="<?php echo htmlspecialchars($room11620); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11620); ?>"
                    data-image="<?php echo base64_encode($upload_img11620); ?>"
                    data-status="<?php echo htmlspecialchars($status11620); ?>"
                    data-category="<?php echo htmlspecialchars($category11620); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11620); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11620); ?>; 
    position:absolute; top:300px; left:768px;'>
                </div>

                <!--ASSET 11621 -->
                <img src='../image.php?id=11621'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:773px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11621'
                    onclick='fetchAssetData(11621);' class="asset-image" data-id="<?php echo $assetId11621; ?>"
                    data-room="<?php echo htmlspecialchars($room11621); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11621); ?>"
                    data-image="<?php echo base64_encode($upload_img11621); ?>"
                    data-status="<?php echo htmlspecialchars($status11621); ?>"
                    data-category="<?php echo htmlspecialchars($category11621); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11621); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11621); ?>; 
    position:absolute; top:300px; left:783px;'>
                </div>

                <!--ASSET 11622 -->
                <img src='../image.php?id=11622'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:788px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11622'
                    onclick='fetchAssetData(11622);' class="asset-image" data-id="<?php echo $assetId11622; ?>"
                    data-room="<?php echo htmlspecialchars($room11622); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11622); ?>"
                    data-image="<?php echo base64_encode($upload_img11622); ?>"
                    data-status="<?php echo htmlspecialchars($status11622); ?>"
                    data-category="<?php echo htmlspecialchars($category11622); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11622); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11622); ?>; 
    position:absolute; top:300px; left:798px;'>
                </div>

                <!--ASSET 11623 -->
                <img src='../image.php?id=11623'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:803px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11623'
                    onclick='fetchAssetData(11623);' class="asset-image" data-id="<?php echo $assetId11623; ?>"
                    data-room="<?php echo htmlspecialchars($room11623); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11623); ?>"
                    data-image="<?php echo base64_encode($upload_img11623); ?>"
                    data-status="<?php echo htmlspecialchars($status11623); ?>"
                    data-category="<?php echo htmlspecialchars($category11623); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11623); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11623); ?>; 
    position:absolute; top:300px; left:813px;'>
                </div>

                <!--ASSET 11624 -->
                <img src='../image.php?id=11624'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:818px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11624'
                    onclick='fetchAssetData(11624);' class="asset-image" data-id="<?php echo $assetId11624; ?>"
                    data-room="<?php echo htmlspecialchars($room11624); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11624); ?>"
                    data-image="<?php echo base64_encode($upload_img11624); ?>"
                    data-status="<?php echo htmlspecialchars($status11624); ?>"
                    data-category="<?php echo htmlspecialchars($category11624); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11624); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11624); ?>; 
    position:absolute; top:300px; left:828px;'>
                </div>

                <!--ASSET 11625 -->
                <img src='../image.php?id=11625'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:833px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11625'
                    onclick='fetchAssetData(11625);' class="asset-image" data-id="<?php echo $assetId11625; ?>"
                    data-room="<?php echo htmlspecialchars($room11625); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11625); ?>"
                    data-image="<?php echo base64_encode($upload_img11625); ?>"
                    data-status="<?php echo htmlspecialchars($status11625); ?>"
                    data-category="<?php echo htmlspecialchars($category11625); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11625); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11625); ?>; 
    position:absolute; top:300px; left:843px;'>
                </div>

                <!--ASSET 11626 -->
                <img src='../image.php?id=11626'
                    style='width:12px; cursor:pointer; position:absolute; top:305px; left:848px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11626'
                    onclick='fetchAssetData(11626);' class="asset-image" data-id="<?php echo $assetId11626; ?>"
                    data-room="<?php echo htmlspecialchars($room11626); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11626); ?>"
                    data-image="<?php echo base64_encode($upload_img11626); ?>"
                    data-status="<?php echo htmlspecialchars($status11626); ?>"
                    data-category="<?php echo htmlspecialchars($category11626); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11626); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11626); ?>; 
    position:absolute; top:300px; left:858px;'>
                </div>

                <!--ASSET 11627 -->
                <img src='../image.php?id=11627'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:728px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11627'
                    onclick='fetchAssetData(11627);' class="asset-image" data-id="<?php echo $assetId11627; ?>"
                    data-room="<?php echo htmlspecialchars($room11627); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11627); ?>"
                    data-image="<?php echo base64_encode($upload_img11627); ?>"
                    data-status="<?php echo htmlspecialchars($status11627); ?>"
                    data-category="<?php echo htmlspecialchars($category11627); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11627); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11627); ?>; 
    position:absolute; top:343px; left:738px;'>
                </div>

                <!--ASSET 11628 -->
                <img src='../image.php?id=11628'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:743px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11628'
                    onclick='fetchAssetData(11628);' class="asset-image" data-id="<?php echo $assetId11628; ?>"
                    data-room="<?php echo htmlspecialchars($room11628); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11628); ?>"
                    data-image="<?php echo base64_encode($upload_img11628); ?>"
                    data-status="<?php echo htmlspecialchars($status11628); ?>"
                    data-category="<?php echo htmlspecialchars($category11628); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11628); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11628); ?>; 
    position:absolute; top:343px; left:753px;'>
                </div>

                <!--ASSET 11629 -->
                <img src='../image.php?id=11629'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:758px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11629'
                    onclick='fetchAssetData(11629);' class="asset-image" data-id="<?php echo $assetId11629; ?>"
                    data-room="<?php echo htmlspecialchars($room11629); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11629); ?>"
                    data-image="<?php echo base64_encode($upload_img11629); ?>"
                    data-status="<?php echo htmlspecialchars($status11629); ?>"
                    data-category="<?php echo htmlspecialchars($category11629); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11629); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11629); ?>; 
    position:absolute; top:343px; left:768px;'>
                </div>

                <!--ASSET 11630 -->
                <img src='../image.php?id=11630'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:773px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11630'
                    onclick='fetchAssetData(11630);' class="asset-image" data-id="<?php echo $assetId11630; ?>"
                    data-room="<?php echo htmlspecialchars($room11630); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11630); ?>"
                    data-image="<?php echo base64_encode($upload_img11630); ?>"
                    data-status="<?php echo htmlspecialchars($status11630); ?>"
                    data-category="<?php echo htmlspecialchars($category11630); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11630); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11630); ?>; 
    position:absolute; top:343px; left:783px;'>
                </div>

                <!--ASSET 11631 -->
                <img src='../image.php?id=11631'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:788px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11631'
                    onclick='fetchAssetData(11631);' class="asset-image" data-id="<?php echo $assetId11631; ?>"
                    data-room="<?php echo htmlspecialchars($room11631); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11631); ?>"
                    data-image="<?php echo base64_encode($upload_img11631); ?>"
                    data-status="<?php echo htmlspecialchars($status11631); ?>"
                    data-category="<?php echo htmlspecialchars($category11631); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11631); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11631); ?>; 
    position:absolute; top:343px; left:798px;'>
                </div>

                <!--ASSET 11632 -->
                <img src='../image.php?id=11632'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:803px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11632'
                    onclick='fetchAssetData(11632);' class="asset-image" data-id="<?php echo $assetId11632; ?>"
                    data-room="<?php echo htmlspecialchars($room11632); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11632); ?>"
                    data-image="<?php echo base64_encode($upload_img11632); ?>"
                    data-status="<?php echo htmlspecialchars($status11632); ?>"
                    data-category="<?php echo htmlspecialchars($category11632); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11632); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11632); ?>; 
    position:absolute; top:343px; left:813px;'>
                </div>

                <!--ASSET 11633 -->
                <img src='../image.php?id=11633'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:818px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11633'
                    onclick='fetchAssetData(11633);' class="asset-image" data-id="<?php echo $assetId11633; ?>"
                    data-room="<?php echo htmlspecialchars($room11633); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11633); ?>"
                    data-image="<?php echo base64_encode($upload_img11633); ?>"
                    data-status="<?php echo htmlspecialchars($status11633); ?>"
                    data-category="<?php echo htmlspecialchars($category11633); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11633); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11633); ?>; 
    position:absolute; top:343px; left:828px;'>
                </div>

                <!--ASSET 11634 -->
                <img src='../image.php?id=11634'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:833px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11634'
                    onclick='fetchAssetData(11634);' class="asset-image" data-id="<?php echo $assetId11634; ?>"
                    data-room="<?php echo htmlspecialchars($room11634); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11634); ?>"
                    data-image="<?php echo base64_encode($upload_img11634); ?>"
                    data-status="<?php echo htmlspecialchars($status11634); ?>"
                    data-category="<?php echo htmlspecialchars($category11634); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11634); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11634); ?>; 
    position:absolute; top:343px; left:843px;'>
                </div>

                <!--ASSET 11635 -->
                <img src='../image.php?id=11635'
                    style='width:12px; cursor:pointer; position:absolute; top:337px; left:848px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11635'
                    onclick='fetchAssetData(11635);' class="asset-image" data-id="<?php echo $assetId11635; ?>"
                    data-room="<?php echo htmlspecialchars($room11635); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11635); ?>"
                    data-image="<?php echo base64_encode($upload_img11635); ?>"
                    data-status="<?php echo htmlspecialchars($status11635); ?>"
                    data-category="<?php echo htmlspecialchars($category11635); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11635); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11635); ?>; 
    position:absolute; top:343px; left:858px;'>
                </div>

                <!--ASSET 11636 -->
                <img src='../image.php?id=11636'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:728px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11636'
                    onclick='fetchAssetData(11636);' class="asset-image" data-id="<?php echo $assetId11636; ?>"
                    data-room="<?php echo htmlspecialchars($room11636); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11636); ?>"
                    data-image="<?php echo base64_encode($upload_img11636); ?>"
                    data-status="<?php echo htmlspecialchars($status11636); ?>"
                    data-category="<?php echo htmlspecialchars($category11636); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11636); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11636); ?>; 
    position:absolute; top:354px; left:738px;'>
                </div>

                <!--ASSET 11637 -->
                <img src='../image.php?id=11637'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:743px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11637'
                    onclick='fetchAssetData(11637);' class="asset-image" data-id="<?php echo $assetId11637; ?>"
                    data-room="<?php echo htmlspecialchars($room11637); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11637); ?>"
                    data-image="<?php echo base64_encode($upload_img11637); ?>"
                    data-status="<?php echo htmlspecialchars($status11637); ?>"
                    data-category="<?php echo htmlspecialchars($category11637); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11637); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11637); ?>; 
    position:absolute; top:354px; left:753px;'>
                </div>

                <!--ASSET 11638 -->
                <img src='../image.php?id=11638'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:758px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11638'
                    onclick='fetchAssetData(11638);' class="asset-image" data-id="<?php echo $assetId11638; ?>"
                    data-room="<?php echo htmlspecialchars($room11638); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11638); ?>"
                    data-image="<?php echo base64_encode($upload_img11638); ?>"
                    data-status="<?php echo htmlspecialchars($status11638); ?>"
                    data-category="<?php echo htmlspecialchars($category11638); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11638); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11638); ?>; 
    position:absolute; top:354px; left:768px;'>
                </div>

                <!--ASSET 11639 -->
                <img src='../image.php?id=11639'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:773px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11639'
                    onclick='fetchAssetData(11639);' class="asset-image" data-id="<?php echo $assetId11639; ?>"
                    data-room="<?php echo htmlspecialchars($room11639); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11639); ?>"
                    data-image="<?php echo base64_encode($upload_img11639); ?>"
                    data-status="<?php echo htmlspecialchars($status11639); ?>"
                    data-category="<?php echo htmlspecialchars($category11639); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11639); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11639); ?>; 
    position:absolute; top:354px; left:783px;'>
                </div>

                <!--ASSET 11640 -->
                <img src='../image.php?id=11640'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:788px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11640'
                    onclick='fetchAssetData(11640);' class="asset-image" data-id="<?php echo $assetId11640; ?>"
                    data-room="<?php echo htmlspecialchars($room11640); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11640); ?>"
                    data-image="<?php echo base64_encode($upload_img11640); ?>"
                    data-status="<?php echo htmlspecialchars($status11640); ?>"
                    data-category="<?php echo htmlspecialchars($category11640); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11640); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11640); ?>; 
    position:absolute; top:354px; left:798px;'>
                </div>

                <!--ASSET 11641 -->
                <img src='../image.php?id=11641'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:803px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11641'
                    onclick='fetchAssetData(11641);' class="asset-image" data-id="<?php echo $assetId11641; ?>"
                    data-room="<?php echo htmlspecialchars($room11641); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11641); ?>"
                    data-image="<?php echo base64_encode($upload_img11641); ?>"
                    data-status="<?php echo htmlspecialchars($status11641); ?>"
                    data-category="<?php echo htmlspecialchars($category11641); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11641); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11641); ?>; 
    position:absolute; top:354px; left:813px;'>
                </div>

                <!--ASSET 11642 -->
                <img src='../image.php?id=11642'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:818px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11642'
                    onclick='fetchAssetData(11642);' class="asset-image" data-id="<?php echo $assetId11642; ?>"
                    data-room="<?php echo htmlspecialchars($room11642); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11642); ?>"
                    data-image="<?php echo base64_encode($upload_img11642); ?>"
                    data-status="<?php echo htmlspecialchars($status11642); ?>"
                    data-category="<?php echo htmlspecialchars($category11642); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11642); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11642); ?>; 
    position:absolute; top:354px; left:828px;'>
                </div>

                <!--ASSET 11643 -->
                <img src='../image.php?id=11643'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:833px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11643'
                    onclick='fetchAssetData(11643);' class="asset-image" data-id="<?php echo $assetId11643; ?>"
                    data-room="<?php echo htmlspecialchars($room11643); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11643); ?>"
                    data-image="<?php echo base64_encode($upload_img11643); ?>"
                    data-status="<?php echo htmlspecialchars($status11643); ?>"
                    data-category="<?php echo htmlspecialchars($category11643); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11643); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11643); ?>; 
    position:absolute; top:354px; left:843px;'>
                </div>

                <!--ASSET 11644 -->
                <img src='../image.php?id=11644'
                    style='width:12px; cursor:pointer; position:absolute; top:360px; left:848px;transform: rotate(-90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11644'
                    onclick='fetchAssetData(11644);' class="asset-image" data-id="<?php echo $assetId11644; ?>"
                    data-room="<?php echo htmlspecialchars($room11644); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11644); ?>"
                    data-image="<?php echo base64_encode($upload_img11644); ?>"
                    data-status="<?php echo htmlspecialchars($status11644); ?>"
                    data-category="<?php echo htmlspecialchars($category11644); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11644); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11644); ?>; 
    position:absolute; top:354px; left:858px;'>
                </div>

                <!--ASSET 11645 -->
                <img src='../image.php?id=11645'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:728px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11645'
                    onclick='fetchAssetData(11645);' class="asset-image" data-id="<?php echo $assetId11645; ?>"
                    data-room="<?php echo htmlspecialchars($room11645); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11645); ?>"
                    data-image="<?php echo base64_encode($upload_img11645); ?>"
                    data-status="<?php echo htmlspecialchars($status11645); ?>"
                    data-category="<?php echo htmlspecialchars($category11645); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11645); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11645); ?>; 
    position:absolute; top:397px; left:738px;'>
                </div>

                <!--ASSET 11646 -->
                <img src='../image.php?id=11646'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:743px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11646'
                    onclick='fetchAssetData(11646);' class="asset-image" data-id="<?php echo $assetId11646; ?>"
                    data-room="<?php echo htmlspecialchars($room11646); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11646); ?>"
                    data-image="<?php echo base64_encode($upload_img11646); ?>"
                    data-status="<?php echo htmlspecialchars($status11646); ?>"
                    data-category="<?php echo htmlspecialchars($category11646); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11646); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11646); ?>; 
    position:absolute; top:397px; left:753px;'>
                </div>

                <!--ASSET 11647 -->
                <img src='../image.php?id=11647'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:758px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11647'
                    onclick='fetchAssetData(11647);' class="asset-image" data-id="<?php echo $assetId11647; ?>"
                    data-room="<?php echo htmlspecialchars($room11647); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11647); ?>"
                    data-image="<?php echo base64_encode($upload_img11647); ?>"
                    data-status="<?php echo htmlspecialchars($status11647); ?>"
                    data-category="<?php echo htmlspecialchars($category11647); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11647); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11647); ?>; 
    position:absolute; top:397px; left:768px;'>
                </div>

                <!--ASSET 11648 -->
                <img src='../image.php?id=11648'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:773px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11648'
                    onclick='fetchAssetData(11648);' class="asset-image" data-id="<?php echo $assetId11648; ?>"
                    data-room="<?php echo htmlspecialchars($room11648); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11648); ?>"
                    data-image="<?php echo base64_encode($upload_img11648); ?>"
                    data-status="<?php echo htmlspecialchars($status11648); ?>"
                    data-category="<?php echo htmlspecialchars($category11648); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11648); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11648); ?>; 
    position:absolute; top:397px; left:783px;'>
                </div>

                <!--ASSET 11649 -->
                <img src='../image.php?id=11649'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:788px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11649'
                    onclick='fetchAssetData(11649);' class="asset-image" data-id="<?php echo $assetId11649; ?>"
                    data-room="<?php echo htmlspecialchars($room11649); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11649); ?>"
                    data-image="<?php echo base64_encode($upload_img11649); ?>"
                    data-status="<?php echo htmlspecialchars($status11649); ?>"
                    data-category="<?php echo htmlspecialchars($category11649); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11649); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11649); ?>; 
    position:absolute; top:397px; left:798px;'>
                </div>


                <!--ASSET 11650 -->
                <img src='../image.php?id=11650'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:803px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11650'
                    onclick='fetchAssetData(11650);' class="asset-image" data-id="<?php echo $assetId11650; ?>"
                    data-room="<?php echo htmlspecialchars($room11650); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11650); ?>"
                    data-image="<?php echo base64_encode($upload_img11650); ?>"
                    data-status="<?php echo htmlspecialchars($status11650); ?>"
                    data-category="<?php echo htmlspecialchars($category11650); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11650); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11650); ?>; 
    position:absolute; top:397px; left:813px;'>
                </div>

                <!--ASSET 11651 -->
                <img src='../image.php?id=11651'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:818px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11651'
                    onclick='fetchAssetData(11651);' class="asset-image" data-id="<?php echo $assetId11651; ?>"
                    data-room="<?php echo htmlspecialchars($room11651); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11651); ?>"
                    data-image="<?php echo base64_encode($upload_img11651); ?>"
                    data-status="<?php echo htmlspecialchars($status11651); ?>"
                    data-category="<?php echo htmlspecialchars($category11651); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11651); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11651); ?>; 
    position:absolute; top:397px; left:828px;'>
                </div>

                <!--ASSET 11652 -->
                <img src='../image.php?id=11652'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:833px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11652'
                    onclick='fetchAssetData(11652);' class="asset-image" data-id="<?php echo $assetId11652; ?>"
                    data-room="<?php echo htmlspecialchars($room11652); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11652); ?>"
                    data-image="<?php echo base64_encode($upload_img11652); ?>"
                    data-status="<?php echo htmlspecialchars($status11652); ?>"
                    data-category="<?php echo htmlspecialchars($category11652); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11652); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11652); ?>; 
    position:absolute; top:397px; left:843px;'>
                </div>

                <!--ASSET 11653 -->
                <img src='../image.php?id=11653'
                    style='width:12px; cursor:pointer; position:absolute; top:391px; left:848px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11653'
                    onclick='fetchAssetData(11653);' class="asset-image" data-id="<?php echo $assetId11653; ?>"
                    data-room="<?php echo htmlspecialchars($room11653); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11653); ?>"
                    data-image="<?php echo base64_encode($upload_img11653); ?>"
                    data-status="<?php echo htmlspecialchars($status11653); ?>"
                    data-category="<?php echo htmlspecialchars($category11653); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11653); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11653); ?>; 
    position:absolute; top:397px; left:858px;'>
                </div>

                <!--ASSET 11654 -->
                <img src='../image.php?id=11654'
                    style='width:15px; cursor:pointer; position:absolute; top:319px; left:880px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11654'
                    onclick='fetchAssetData(11654);' class="asset-image" data-id="<?php echo $assetId11654; ?>"
                    data-room="<?php echo htmlspecialchars($room11654); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11654); ?>"
                    data-image="<?php echo base64_encode($upload_img11654); ?>"
                    data-status="<?php echo htmlspecialchars($status11654); ?>"
                    data-category="<?php echo htmlspecialchars($category11654); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11654); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11654); ?>; 
    position:absolute; top:314px; left:895px;'>
                </div>

                <!--ASSET 11655 -->
                <img src='../image.php?id=11655'
                    style='width:15px; cursor:pointer; position:absolute; top:375px; left:880px;transform: rotate(90deg);'' alt='
                    Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11655'
                    onclick='fetchAssetData(11655);' class="asset-image" data-id="<?php echo $assetId11655; ?>"
                    data-room="<?php echo htmlspecialchars($room11655); ?>"
                    data-floor="<?php echo htmlspecialchars($floor11655); ?>"
                    data-image="<?php echo base64_encode($upload_img11655); ?>"
                    data-status="<?php echo htmlspecialchars($status11655); ?>"
                    data-category="<?php echo htmlspecialchars($category11655); ?>"
                    data-assignedname="<?php echo htmlspecialchars($assignedName11655); ?>">
                <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11655); ?>; 
    position:absolute; top:370px; left:895px;'>
                </div>




                    <!--ASSET 11654 -->
                    <img src='../image.php?id=11654'
                        style='width:15px; cursor:pointer; position:absolute; top:319px; left:880px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11654'
                        onclick='fetchAssetData(11654);' class="asset-image" data-id="<?php echo $assetId11654; ?>"
                        data-room="<?php echo htmlspecialchars($room11654); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11654); ?>"
                        data-image="<?php echo base64_encode($upload_img11654); ?>"
                        data-status="<?php echo htmlspecialchars($status11654); ?>"
                        data-category="<?php echo htmlspecialchars($category11654); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11654); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11654); ?>; 
    position:absolute; top:314px; left:895px;'>
                    </div>

                    <!--ASSET 11655 -->
                    <img src='../image.php?id=11655'
                        style='width:15px; cursor:pointer; position:absolute; top:375px; left:880px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11655'
                        onclick='fetchAssetData(11655);' class="asset-image" data-id="<?php echo $assetId11655; ?>"
                        data-room="<?php echo htmlspecialchars($room11655); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11655); ?>"
                        data-image="<?php echo base64_encode($upload_img11655); ?>"
                        data-status="<?php echo htmlspecialchars($status11655); ?>"
                        data-category="<?php echo htmlspecialchars($category11655); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11655); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11655); ?>; 
    position:absolute; top:370px; left:895px;'>
                    </div>

                    <!--ASSET 11656 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:319px; left:934px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11656'
                        onclick='fetchAssetData(11656);' class="asset-image" data-id="<?php echo $assetId11656; ?>"
                        data-room="<?php echo htmlspecialchars($room11656); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11656); ?>"
                        data-image="<?php echo base64_encode($upload_img11656); ?>"
                        data-status="<?php echo htmlspecialchars($status11656); ?>"
                        data-category="<?php echo htmlspecialchars($category11656); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11656); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11656); ?>; 
    position:absolute; top:314px; left:944px;'>
                    </div>

                    <!--ASSET 11657 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:319px; left:984px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11657'
                        onclick='fetchAssetData(11657);' class="asset-image" data-id="<?php echo $assetId11657; ?>"
                        data-room="<?php echo htmlspecialchars($room11657); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11657); ?>"
                        data-image="<?php echo base64_encode($upload_img11657); ?>"
                        data-status="<?php echo htmlspecialchars($status11657); ?>"
                        data-category="<?php echo htmlspecialchars($category11657); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11657); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11657); ?>; 
    position:absolute; top:314px; left:994px;'>
                    </div>

                    <!--ASSET 11658 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:388px; left:984px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11658'
                        onclick='fetchAssetData(11658);' class="asset-image" data-id="<?php echo $assetId11658; ?>"
                        data-room="<?php echo htmlspecialchars($room11658); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11658); ?>"
                        data-image="<?php echo base64_encode($upload_img11658); ?>"
                        data-status="<?php echo htmlspecialchars($status11658); ?>"
                        data-category="<?php echo htmlspecialchars($category11658); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11658); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11658); ?>; 
    position:absolute; top:383px; left:994px;'>
                    </div>

                    <!--ASSET 11659 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:388px; left:934px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11659'
                        onclick='fetchAssetData(11659);' class="asset-image" data-id="<?php echo $assetId11659; ?>"
                        data-room="<?php echo htmlspecialchars($room11659); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11659); ?>"
                        data-image="<?php echo base64_encode($upload_img11659); ?>"
                        data-status="<?php echo htmlspecialchars($status11659); ?>"
                        data-category="<?php echo htmlspecialchars($category11659); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11659); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11659); ?>; 
    position:absolute; top:383px; left:944px;'>
                    </div>

                    <!--ASSET 11660 -->
                    <img src='../image.php?id=11660'
                        style='width:20px; cursor:pointer; position:absolute; top:300px; left:980px;transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11660'
                        onclick='fetchAssetData(11660);' class="asset-image" data-id="<?php echo $assetId11660; ?>"
                        data-room="<?php echo htmlspecialchars($room11660); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11660); ?>"
                        data-image="<?php echo base64_encode($upload_img11660); ?>"
                        data-status="<?php echo htmlspecialchars($status11660); ?>"
                        data-category="<?php echo htmlspecialchars($category11660); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11660); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11660); ?>; 
    position:absolute; top:383px; left:944px;'>
                    </div>

                    <!--ASSET 11661 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:135px; left:1036px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11661'
                        onclick='fetchAssetData(11661);' class="asset-image" data-id="<?php echo $assetId11661; ?>"
                        data-room="<?php echo htmlspecialchars($room11661); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11661); ?>"
                        data-image="<?php echo base64_encode($upload_img11661); ?>"
                        data-status="<?php echo htmlspecialchars($status11661); ?>"
                        data-category="<?php echo htmlspecialchars($category11661); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11661); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11661); ?>; 
    position:absolute; top:383px; left:944px;'>
                    </div>

                    <!--ASSET 11662 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:135px; left:1086px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11662'
                        onclick='fetchAssetData(11662);' class="asset-image" data-id="<?php echo $assetId11662; ?>"
                        data-room="<?php echo htmlspecialchars($room11662); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11662); ?>"
                        data-image="<?php echo base64_encode($upload_img11662); ?>"
                        data-status="<?php echo htmlspecialchars($status11662); ?>"
                        data-category="<?php echo htmlspecialchars($category11662); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11662); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11662); ?>; 
    position:absolute; top:383px; left:944px;'>
                    </div>

                    <!--ASSET 11663 -->
                    <img src='../image.php?id=11663'
                        style='width:20px; cursor:pointer; position:absolute; top:242px; left:1015px;transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11663'
                        onclick='fetchAssetData(11663);' class="asset-image" data-id="<?php echo $assetId11663; ?>"
                        data-room="<?php echo htmlspecialchars($room11663); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11663); ?>"
                        data-image="<?php echo base64_encode($upload_img11663); ?>"
                        data-status="<?php echo htmlspecialchars($status11663); ?>"
                        data-category="<?php echo htmlspecialchars($category11663); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11663); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11663); ?>; 
    position:absolute; top:383px; left:944px;'>
                    </div>

                    <!--ASSET 11664 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:125px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11664'
                        onclick='fetchAssetData(11664);' class="asset-image" data-id="<?php echo $assetId11664; ?>"
                        data-room="<?php echo htmlspecialchars($room11664); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11664); ?>"
                        data-image="<?php echo base64_encode($upload_img11664); ?>"
                        data-status="<?php echo htmlspecialchars($status11664); ?>"
                        data-category="<?php echo htmlspecialchars($category11664); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11664); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11664); ?>; 
    position:absolute; top:120px; left:810px;'>
                    </div>

                    <!--ASSET 11665 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:125px; left:830px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11665'
                        onclick='fetchAssetData(11665);' class="asset-image" data-id="<?php echo $assetId11665; ?>"
                        data-room="<?php echo htmlspecialchars($room11665); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11665); ?>"
                        data-image="<?php echo base64_encode($upload_img11665); ?>"
                        data-status="<?php echo htmlspecialchars($status11665); ?>"
                        data-category="<?php echo htmlspecialchars($category11665); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11665); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11665); ?>; 
    position:absolute; top:120px; left:840px;'>
                    </div>

                    <!--ASSET 11666 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:125px; left:860px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11666'
                        onclick='fetchAssetData(11666);' class="asset-image" data-id="<?php echo $assetId11666; ?>"
                        data-room="<?php echo htmlspecialchars($room11666); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11666); ?>"
                        data-image="<?php echo base64_encode($upload_img11666); ?>"
                        data-status="<?php echo htmlspecialchars($status11666); ?>"
                        data-category="<?php echo htmlspecialchars($category11666); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11666); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11666); ?>; 
    position:absolute; top:120px; left:870px;'>
                    </div>

                    <!--ASSET 11667 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:125px; left:890px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11667'
                        onclick='fetchAssetData(11667);' class="asset-image" data-id="<?php echo $assetId11667; ?>"
                        data-room="<?php echo htmlspecialchars($room11667); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11667); ?>"
                        data-image="<?php echo base64_encode($upload_img11667); ?>"
                        data-status="<?php echo htmlspecialchars($status11667); ?>"
                        data-category="<?php echo htmlspecialchars($category11667); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11667); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11667); ?>; 
    position:absolute; top:120px; left:900px;'>
                    </div>

                    <!--ASSET 11668 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:125px; left:920px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11668'
                        onclick='fetchAssetData(11668);' class="asset-image" data-id="<?php echo $assetId11668; ?>"
                        data-room="<?php echo htmlspecialchars($room11668); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11668); ?>"
                        data-image="<?php echo base64_encode($upload_img11668); ?>"
                        data-status="<?php echo htmlspecialchars($status11668); ?>"
                        data-category="<?php echo htmlspecialchars($category11668); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11668); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11668); ?>; 
    position:absolute; top:120px; left:930px;'>
                    </div>

                    <!--ASSET 11669 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:125px; left:950px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11669'
                        onclick='fetchAssetData(11669);' class="asset-image" data-id="<?php echo $assetId11669; ?>"
                        data-room="<?php echo htmlspecialchars($room11669); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11669); ?>"
                        data-image="<?php echo base64_encode($upload_img11669); ?>"
                        data-status="<?php echo htmlspecialchars($status11669); ?>"
                        data-category="<?php echo htmlspecialchars($category11669); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11669); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11669); ?>; 
    position:absolute; top:120px; left:960px;'>
                    </div>

                    <!--ASSET 11670 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11670'
                        onclick='fetchAssetData(11670);' class="asset-image" data-id="<?php echo $assetId11670; ?>"
                        data-room="<?php echo htmlspecialchars($room11670); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11670); ?>"
                        data-image="<?php echo base64_encode($upload_img11670); ?>"
                        data-status="<?php echo htmlspecialchars($status11670); ?>"
                        data-category="<?php echo htmlspecialchars($category11670); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11670); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11670); ?>; 
    position:absolute; top:150px; left:810px;'>
                    </div>

                    <!--ASSET 11671 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:830px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11671'
                        onclick='fetchAssetData(11671);' class="asset-image" data-id="<?php echo $assetId11671; ?>"
                        data-room="<?php echo htmlspecialchars($room11671); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11671); ?>"
                        data-image="<?php echo base64_encode($upload_img11671); ?>"
                        data-status="<?php echo htmlspecialchars($status11671); ?>"
                        data-category="<?php echo htmlspecialchars($category11671); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11671); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11671); ?>; 
    position:absolute; top:150px; left:840px;'>
                    </div>

                    <!--ASSET 11672 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:860px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11672'
                        onclick='fetchAssetData(11672);' class="asset-image" data-id="<?php echo $assetId11672; ?>"
                        data-room="<?php echo htmlspecialchars($room11672); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11672); ?>"
                        data-image="<?php echo base64_encode($upload_img11672); ?>"
                        data-status="<?php echo htmlspecialchars($status11672); ?>"
                        data-category="<?php echo htmlspecialchars($category11672); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11672); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11672); ?>; 
    position:absolute; top:150px; left:870px;'>
                    </div>

                    <!--ASSET 11673 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:890px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11673'
                        onclick='fetchAssetData(11673);' class="asset-image" data-id="<?php echo $assetId11673; ?>"
                        data-room="<?php echo htmlspecialchars($room11673); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11673); ?>"
                        data-image="<?php echo base64_encode($upload_img11673); ?>"
                        data-status="<?php echo htmlspecialchars($status11673); ?>"
                        data-category="<?php echo htmlspecialchars($category11673); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11673); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11673); ?>; 
    position:absolute; top:150px; left:900px;'>
                    </div>

                    <!--ASSET 11674 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:920px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11674'
                        onclick='fetchAssetData(11674);' class="asset-image" data-id="<?php echo $assetId11674; ?>"
                        data-room="<?php echo htmlspecialchars($room11674); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11674); ?>"
                        data-image="<?php echo base64_encode($upload_img11674); ?>"
                        data-status="<?php echo htmlspecialchars($status11674); ?>"
                        data-category="<?php echo htmlspecialchars($category11674); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11674); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11674); ?>; 
    position:absolute; top:150px; left:930px;'>
                    </div>

                    <!--ASSET 11675 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:950px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11675'
                        onclick='fetchAssetData(11675);' class="asset-image" data-id="<?php echo $assetId11675; ?>"
                        data-room="<?php echo htmlspecialchars($room11675); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11675); ?>"
                        data-image="<?php echo base64_encode($upload_img11675); ?>"
                        data-status="<?php echo htmlspecialchars($status11675); ?>"
                        data-category="<?php echo htmlspecialchars($category11675); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11675); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11675); ?>; 
    position:absolute; top:150px; left:960px;'>
                    </div>

                    <!--ASSET 11676 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11676'
                        onclick='fetchAssetData(11676);' class="asset-image" data-id="<?php echo $assetId11676; ?>"
                        data-room="<?php echo htmlspecialchars($room11676); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11676); ?>"
                        data-image="<?php echo base64_encode($upload_img11676); ?>"
                        data-status="<?php echo htmlspecialchars($status11676); ?>"
                        data-category="<?php echo htmlspecialchars($category11676); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11676); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11676); ?>; 
    position:absolute; top:180px; left:810px;'>
                    </div>

                    <!--ASSET 11677 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:830px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11677'
                        onclick='fetchAssetData(11677);' class="asset-image" data-id="<?php echo $assetId11677; ?>"
                        data-room="<?php echo htmlspecialchars($room11677); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11677); ?>"
                        data-image="<?php echo base64_encode($upload_img11677); ?>"
                        data-status="<?php echo htmlspecialchars($status11677); ?>"
                        data-category="<?php echo htmlspecialchars($category11677); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11677); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11677); ?>; 
    position:absolute; top:180px; left:840px;'>
                    </div>

                    <!--ASSET 11678 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:860px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11678'
                        onclick='fetchAssetData(11678);' class="asset-image" data-id="<?php echo $assetId11678; ?>"
                        data-room="<?php echo htmlspecialchars($room11678); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11678); ?>"
                        data-image="<?php echo base64_encode($upload_img11678); ?>"
                        data-status="<?php echo htmlspecialchars($status11678); ?>"
                        data-category="<?php echo htmlspecialchars($category11678); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11678); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11678); ?>; 
    position:absolute; top:180px; left:870px;'>
                    </div>

                    <!--ASSET 11679 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:890px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11679'
                        onclick='fetchAssetData(11679);' class="asset-image" data-id="<?php echo $assetId11679; ?>"
                        data-room="<?php echo htmlspecialchars($room11679); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11679); ?>"
                        data-image="<?php echo base64_encode($upload_img11679); ?>"
                        data-status="<?php echo htmlspecialchars($status11679); ?>"
                        data-category="<?php echo htmlspecialchars($category11679); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11679); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11679); ?>; 
    position:absolute; top:180px; left:900px;'>
                    </div>

                    <!--ASSET 11680 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:920px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11680'
                        onclick='fetchAssetData(11680);' class="asset-image" data-id="<?php echo $assetId11680; ?>"
                        data-room="<?php echo htmlspecialchars($room11680); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11680); ?>"
                        data-image="<?php echo base64_encode($upload_img11680); ?>"
                        data-status="<?php echo htmlspecialchars($status11680); ?>"
                        data-category="<?php echo htmlspecialchars($category11680); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11680); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11680); ?>; 
    position:absolute; top:180px; left:930px;'>
                    </div>

                    <!--ASSET 11681 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:950px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11681'
                        onclick='fetchAssetData(11681);' class="asset-image" data-id="<?php echo $assetId11681; ?>"
                        data-room="<?php echo htmlspecialchars($room11681); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11681); ?>"
                        data-image="<?php echo base64_encode($upload_img11681); ?>"
                        data-status="<?php echo htmlspecialchars($status11681); ?>"
                        data-category="<?php echo htmlspecialchars($category11681); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11681); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11681); ?>; 
    position:absolute; top:180px; left:960px;'>
                    </div>

                    <!--ASSET 11682 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11682'
                        onclick='fetchAssetData(11682);' class="asset-image" data-id="<?php echo $assetId11682; ?>"
                        data-room="<?php echo htmlspecialchars($room11682); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11682); ?>"
                        data-image="<?php echo base64_encode($upload_img11682); ?>"
                        data-status="<?php echo htmlspecialchars($status11682); ?>"
                        data-category="<?php echo htmlspecialchars($category11682); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11682); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11682); ?>; 
    position:absolute; top:210px; left:810px;'>
                    </div>

                    <!--ASSET 11683 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:830px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11683'
                        onclick='fetchAssetData(11683);' class="asset-image" data-id="<?php echo $assetId11683; ?>"
                        data-room="<?php echo htmlspecialchars($room11683); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11683); ?>"
                        data-image="<?php echo base64_encode($upload_img11683); ?>"
                        data-status="<?php echo htmlspecialchars($status11683); ?>"
                        data-category="<?php echo htmlspecialchars($category11683); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11683); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11683); ?>; 
    position:absolute; top:210px; left:840px;'>
                    </div>

                    <!--ASSET 11684 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:860px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11684'
                        onclick='fetchAssetData(11684);' class="asset-image" data-id="<?php echo $assetId11684; ?>"
                        data-room="<?php echo htmlspecialchars($room11684); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11684); ?>"
                        data-image="<?php echo base64_encode($upload_img11684); ?>"
                        data-status="<?php echo htmlspecialchars($status11684); ?>"
                        data-category="<?php echo htmlspecialchars($category11684); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11684); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11684); ?>; 
    position:absolute; top:210px; left:870px;'>
                    </div>

                    <!--ASSET 11685 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:890px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11685'
                        onclick='fetchAssetData(11685);' class="asset-image" data-id="<?php echo $assetId11685; ?>"
                        data-room="<?php echo htmlspecialchars($room11685); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11685); ?>"
                        data-image="<?php echo base64_encode($upload_img11685); ?>"
                        data-status="<?php echo htmlspecialchars($status11685); ?>"
                        data-category="<?php echo htmlspecialchars($category11685); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11685); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11685); ?>; 
    position:absolute; top:210px; left:900px;'>
                    </div>

                    <!--ASSET 11686 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:920px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11686'
                        onclick='fetchAssetData(11686);' class="asset-image" data-id="<?php echo $assetId11686; ?>"
                        data-room="<?php echo htmlspecialchars($room11686); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11686); ?>"
                        data-image="<?php echo base64_encode($upload_img11686); ?>"
                        data-status="<?php echo htmlspecialchars($status11686); ?>"
                        data-category="<?php echo htmlspecialchars($category11686); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11686); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11686); ?>; 
    position:absolute; top:210px; left:930px;'>
                    </div>

                    <!--ASSET 11687 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:950px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11687'
                        onclick='fetchAssetData(11687);' class="asset-image" data-id="<?php echo $assetId11687; ?>"
                        data-room="<?php echo htmlspecialchars($room11687); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11687); ?>"
                        data-image="<?php echo base64_encode($upload_img11687); ?>"
                        data-status="<?php echo htmlspecialchars($status11687); ?>"
                        data-category="<?php echo htmlspecialchars($category11687); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11687); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11687); ?>; 
    position:absolute; top:210px; left:960px;'>
                    </div>

  <!--ASSET 11688 -->
                    <img src='../image.php?id=11688'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:800px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11688'
                        onclick='fetchAssetData(11688);' class="asset-image" data-id="<?php echo $assetId11688; ?>"
                        data-room="<?php echo htmlspecialchars($room11688); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11688); ?>"
                        data-image="<?php echo base64_encode($upload_img11688); ?>"
                        data-status="<?php echo htmlspecialchars($status11688); ?>"
                        data-category="<?php echo htmlspecialchars($category11688); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11688); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11688); ?>; 
    position:absolute; top:140px; left:810px;'>
                    </div>

                    <!--ASSET 11689 -->
                    <img src='../image.php?id=11689'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left: 816px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11689'
                        onclick='fetchAssetData(11689);' class="asset-image" data-id="<?php echo $assetId11689; ?>"
                        data-room="<?php echo htmlspecialchars($room11689); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11689); ?>"
                        data-image="<?php echo base64_encode($upload_img11689); ?>"
                        data-status="<?php echo htmlspecialchars($status11689); ?>"
                        data-category="<?php echo htmlspecialchars($category11689); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11689); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11689); ?>; 
    position:absolute; top:140px; left:826px;'>
                    </div>

                    <!--ASSET 11690 -->
                    <img src='../image.php?id=11690'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:831px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11690'
                        onclick='fetchAssetData(11690);' class="asset-image" data-id="<?php echo $assetId11690; ?>"
                        data-room="<?php echo htmlspecialchars($room11690); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11690); ?>"
                        data-image="<?php echo base64_encode($upload_img11690); ?>"
                        data-status="<?php echo htmlspecialchars($status11690); ?>"
                        data-category="<?php echo htmlspecialchars($category11690); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11690); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11690); ?>; 
    position:absolute; top:140px; left:841px;'>
                    </div>

                    <!--ASSET 11691 -->
                    <img src='../image.php?id=11691'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:846px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11691'
                        onclick='fetchAssetData(11691);' class="asset-image" data-id="<?php echo $assetId11691; ?>"
                        data-room="<?php echo htmlspecialchars($room11691); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11691); ?>"
                        data-image="<?php echo base64_encode($upload_img11691); ?>"
                        data-status="<?php echo htmlspecialchars($status11691); ?>"
                        data-category="<?php echo htmlspecialchars($category11691); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11691); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11691); ?>; 
    position:absolute; top:140px; left:856px;'>
                    </div>

                    <!--ASSET 11692 -->
                    <img src='../image.php?id=11692'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:861px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11692'
                        onclick='fetchAssetData(11692);' class="asset-image" data-id="<?php echo $assetId11692; ?>"
                        data-room="<?php echo htmlspecialchars($room11692); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11692); ?>"
                        data-image="<?php echo base64_encode($upload_img11692); ?>"
                        data-status="<?php echo htmlspecialchars($status11692); ?>"
                        data-category="<?php echo htmlspecialchars($category11692); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11692); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11692); ?>; 
    position:absolute; top:140px; left:871px;'>
                    </div>

                    <!--ASSET 11693 -->
                    <img src='../image.php?id=11693'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:876px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11693'
                        onclick='fetchAssetData(11693);' class="asset-image" data-id="<?php echo $assetId11693; ?>"
                        data-room="<?php echo htmlspecialchars($room11693); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11693); ?>"
                        data-image="<?php echo base64_encode($upload_img11693); ?>"
                        data-status="<?php echo htmlspecialchars($status11693); ?>"
                        data-category="<?php echo htmlspecialchars($category11693); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11693); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11693); ?>; 
    position:absolute; top:140px; left:886px;'>
                    </div>

                    <!--ASSET 11694 -->
                    <img src='../image.php?id=11694'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:891px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11694'
                        onclick='fetchAssetData(11694);' class="asset-image" data-id="<?php echo $assetId11694; ?>"
                        data-room="<?php echo htmlspecialchars($room11694); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11694); ?>"
                        data-image="<?php echo base64_encode($upload_img11694); ?>"
                        data-status="<?php echo htmlspecialchars($status11694); ?>"
                        data-category="<?php echo htmlspecialchars($category11694); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11694); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11694); ?>; 
    position:absolute; top:140px; left:901px;'>
                    </div>

                    <!--ASSET 11695 -->
                    <img src='../image.php?id=11695'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:906px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11695'
                        onclick='fetchAssetData(11695);' class="asset-image" data-id="<?php echo $assetId11695; ?>"
                        data-room="<?php echo htmlspecialchars($room11695); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11695); ?>"
                        data-image="<?php echo base64_encode($upload_img11695); ?>"
                        data-status="<?php echo htmlspecialchars($status11695); ?>"
                        data-category="<?php echo htmlspecialchars($category11695); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11695); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11695); ?>; 
    position:absolute; top:140px; left:916px;'>
                    </div>

                    <!--ASSET 11696 -->
                    <img src='../image.php?id=11696'
                        style='width:18px; cursor:pointer; position:absolute; top:135px; left:921px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11696'
                        onclick='fetchAssetData(11696);' class="asset-image" data-id="<?php echo $assetId11696; ?>"
                        data-room="<?php echo htmlspecialchars($room11696); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11696); ?>"
                        data-image="<?php echo base64_encode($upload_img11696); ?>"
                        data-status="<?php echo htmlspecialchars($status11696); ?>"
                        data-category="<?php echo htmlspecialchars($category11696); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11696); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11696); ?>; 
    position:absolute; top:140px; left:931px;'>
                    </div>

                    <!--ASSET 11697 -->
                    <img src='../image.php?id=11697'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:801px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11697'
                        onclick='fetchAssetData(11697);' class="asset-image" data-id="<?php echo $assetId11697; ?>"
                        data-room="<?php echo htmlspecialchars($room11697); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11697); ?>"
                        data-image="<?php echo base64_encode($upload_img11697); ?>"
                        data-status="<?php echo htmlspecialchars($status11697); ?>"
                        data-category="<?php echo htmlspecialchars($category11697); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11697); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11697); ?>; 
    position:absolute; top:150px; left:801px;'>
                    </div>

                    <!--ASSET 11698 -->
                    <img src='../image.php?id=11698'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:816px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11698'
                        onclick='fetchAssetData(11698);' class="asset-image" data-id="<?php echo $assetId11698; ?>"
                        data-room="<?php echo htmlspecialchars($room11698); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11698); ?>"
                        data-image="<?php echo base64_encode($upload_img11698); ?>"
                        data-status="<?php echo htmlspecialchars($status11698); ?>"
                        data-category="<?php echo htmlspecialchars($category11698); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11698); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11698); ?>; 
    position:absolute; top:150px; left:816px;'>
                    </div>

                    <!--ASSET 11699 -->
                    <img src='../image.php?id=11699'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:831px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11699'
                        onclick='fetchAssetData(11699);' class="asset-image" data-id="<?php echo $assetId11699; ?>"
                        data-room="<?php echo htmlspecialchars($room11699); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11699); ?>"
                        data-image="<?php echo base64_encode($upload_img11699); ?>"
                        data-status="<?php echo htmlspecialchars($status11699); ?>"
                        data-category="<?php echo htmlspecialchars($category11699); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11699); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11699); ?>; 
    position:absolute; top:150px; left:831px;'>
                    </div>

                    <!--ASSET 11701 -->
                    <img src='../image.php?id=11701'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:846px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11701'
                        onclick='fetchAssetData(11701);' class="asset-image" data-id="<?php echo $assetId11701; ?>"
                        data-room="<?php echo htmlspecialchars($room11701); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11701); ?>"
                        data-image="<?php echo base64_encode($upload_img11701); ?>"
                        data-status="<?php echo htmlspecialchars($status11701); ?>"
                        data-category="<?php echo htmlspecialchars($category11701); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11701); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11701); ?>; 
    position:absolute; top:150px; left:846px;'>
                    </div>

                    <!--ASSET 11702 -->
                    <img src='../image.php?id=11702'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:861px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11702'
                        onclick='fetchAssetData(11702);' class="asset-image" data-id="<?php echo $assetId11702; ?>"
                        data-room="<?php echo htmlspecialchars($room11702); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11702); ?>"
                        data-image="<?php echo base64_encode($upload_img11702); ?>"
                        data-status="<?php echo htmlspecialchars($status11702); ?>"
                        data-category="<?php echo htmlspecialchars($category11702); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11702); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11702); ?>; 
    position:absolute; top:150px; left:861px;'>
                    </div>

                    <!--ASSET 11703 -->
                    <img src='../image.php?id=11703'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:876px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11703'
                        onclick='fetchAssetData(11703);' class="asset-image" data-id="<?php echo $assetId11703; ?>"
                        data-room="<?php echo htmlspecialchars($room11703); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11703); ?>"
                        data-image="<?php echo base64_encode($upload_img11703); ?>"
                        data-status="<?php echo htmlspecialchars($status11703); ?>"
                        data-category="<?php echo htmlspecialchars($category11703); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11703); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11703); ?>; 
    position:absolute; top:150px; left:876px;'>
                    </div>

                    <!--ASSET 11704 -->
                    <img src='../image.php?id=11704'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:891px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11704'
                        onclick='fetchAssetData(11704);' class="asset-image" data-id="<?php echo $assetId11704; ?>"
                        data-room="<?php echo htmlspecialchars($room11704); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11704); ?>"
                        data-image="<?php echo base64_encode($upload_img11704); ?>"
                        data-status="<?php echo htmlspecialchars($status11704); ?>"
                        data-category="<?php echo htmlspecialchars($category11704); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11704); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11704); ?>; 
    position:absolute; top:150px; left:891px;'>
                    </div>

                    <!--ASSET 11705 -->
                    <img src='../image.php?id=11705'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:906px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11705'
                        onclick='fetchAssetData(11705);' class="asset-image" data-id="<?php echo $assetId11705; ?>"
                        data-room="<?php echo htmlspecialchars($room11705); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11705); ?>"
                        data-image="<?php echo base64_encode($upload_img11705); ?>"
                        data-status="<?php echo htmlspecialchars($status11705); ?>"
                        data-category="<?php echo htmlspecialchars($category11705); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11705); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11705); ?>; 
    position:absolute; top:150px; left:906px;'>
                    </div>

                    <!--ASSET 11706 -->
                    <img src='../image.php?id=11706'
                        style='width:18px; cursor:pointer; position:absolute; top:147px; left:921px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11706'
                        onclick='fetchAssetData(11706);' class="asset-image" data-id="<?php echo $assetId11706; ?>"
                        data-room="<?php echo htmlspecialchars($room11706); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11706); ?>"
                        data-image="<?php echo base64_encode($upload_img11706); ?>"
                        data-status="<?php echo htmlspecialchars($status11706); ?>"
                        data-category="<?php echo htmlspecialchars($category11706); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11706); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11706); ?>; 
    position:absolute; top:150px; left:921px;'>
                    </div>

                    <!--ASSET 11707 -->
                    <img src='../image.php?id=11707'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:801px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11707'
                        onclick='fetchAssetData(11707);' class="asset-image" data-id="<?php echo $assetId11707; ?>"
                        data-room="<?php echo htmlspecialchars($room11707); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11707); ?>"
                        data-image="<?php echo base64_encode($upload_img11707); ?>"
                        data-status="<?php echo htmlspecialchars($status11707); ?>"
                        data-category="<?php echo htmlspecialchars($category11707); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11707); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11707); ?>; 
    position:absolute; top:197px; left:810px;'>
                    </div>

                    <!--ASSET 11708 -->
                    <img src='../image.php?id=11708'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:816px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11708'
                        onclick='fetchAssetData(11708);' class="asset-image" data-id="<?php echo $assetId11708; ?>"
                        data-room="<?php echo htmlspecialchars($room11708); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11708); ?>"
                        data-image="<?php echo base64_encode($upload_img11708); ?>"
                        data-status="<?php echo htmlspecialchars($status11708); ?>"
                        data-category="<?php echo htmlspecialchars($category11708); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11708); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11708); ?>; 
    position:absolute; top:197px; left:826px;'>
                    </div>

                    <!--ASSET 11709 -->
                    <img src='../image.php?id=11709'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:831px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11709'
                        onclick='fetchAssetData(11709);' class="asset-image" data-id="<?php echo $assetId11709; ?>"
                        data-room="<?php echo htmlspecialchars($room11709); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11709); ?>"
                        data-image="<?php echo base64_encode($upload_img11709); ?>"
                        data-status="<?php echo htmlspecialchars($status11709); ?>"
                        data-category="<?php echo htmlspecialchars($category11709); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11709); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11709); ?>; 
    position:absolute; top:197px; left:841px;'>
                    </div>

                    <!--ASSET 11710 -->
                    <img src='../image.php?id=11710'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:846px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11710'
                        onclick='fetchAssetData(11710);' class="asset-image" data-id="<?php echo $assetId11710; ?>"
                        data-room="<?php echo htmlspecialchars($room11710); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11710); ?>"
                        data-image="<?php echo base64_encode($upload_img11710); ?>"
                        data-status="<?php echo htmlspecialchars($status11710); ?>"
                        data-category="<?php echo htmlspecialchars($category11710); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11710); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11710); ?>; 
    position:absolute; top:197px; left:856px;'>
                    </div>

                    <!--ASSET 11711 -->
                    <img src='../image.php?id=11711'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:861px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11711'
                        onclick='fetchAssetData(11711);' class="asset-image" data-id="<?php echo $assetId11711; ?>"
                        data-room="<?php echo htmlspecialchars($room11711); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11711); ?>"
                        data-image="<?php echo base64_encode($upload_img11711); ?>"
                        data-status="<?php echo htmlspecialchars($status11711); ?>"
                        data-category="<?php echo htmlspecialchars($category11711); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11711); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11711); ?>; 
    position:absolute; top:197px; left:871px;'>
                    </div>

                    <!--ASSET 11712 -->
                    <img src='../image.php?id=11712'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:876px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11712'
                        onclick='fetchAssetData(11712);' class="asset-image" data-id="<?php echo $assetId11712; ?>"
                        data-room="<?php echo htmlspecialchars($room11712); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11712); ?>"
                        data-image="<?php echo base64_encode($upload_img11712); ?>"
                        data-status="<?php echo htmlspecialchars($status11712); ?>"
                        data-category="<?php echo htmlspecialchars($category11712); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11712); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11712); ?>; 
    position:absolute; top:197px; left:886px;'>
                    </div>

                    <!--ASSET 11713 -->
                    <img src='../image.php?id=11713'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:891px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11713'
                        onclick='fetchAssetData(11713);' class="asset-image" data-id="<?php echo $assetId11713; ?>"
                        data-room="<?php echo htmlspecialchars($room11713); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11713); ?>"
                        data-image="<?php echo base64_encode($upload_img11713); ?>"
                        data-status="<?php echo htmlspecialchars($status11713); ?>"
                        data-category="<?php echo htmlspecialchars($category11713); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11713); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11713); ?>; 
    position:absolute; top:197px; left:901px;'>
                    </div>

                    <!--ASSET 11714 -->
                    <img src='../image.php?id=11714'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:906px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11714'
                        onclick='fetchAssetData(11714);' class="asset-image" data-id="<?php echo $assetId11714; ?>"
                        data-room="<?php echo htmlspecialchars($room11714); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11714); ?>"
                        data-image="<?php echo base64_encode($upload_img11714); ?>"
                        data-status="<?php echo htmlspecialchars($status11714); ?>"
                        data-category="<?php echo htmlspecialchars($category11714); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11714); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11714); ?>; 
    position:absolute; top:197px; left:916px;'>
                    </div>

                    <!--ASSET 11715 -->
                    <img src='../image.php?id=11715'
                        style='width:18px; cursor:pointer; position:absolute; top:192px; left:921px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11715'
                        onclick='fetchAssetData(11715);' class="asset-image" data-id="<?php echo $assetId11715; ?>"
                        data-room="<?php echo htmlspecialchars($room11715); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11715); ?>"
                        data-image="<?php echo base64_encode($upload_img11715); ?>"
                        data-status="<?php echo htmlspecialchars($status11715); ?>"
                        data-category="<?php echo htmlspecialchars($category11715); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11715); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11715); ?>; 
    position:absolute; top:197px; left:931px;'>
                    </div>

                    <!--ASSET 11716 -->
                    <img src='../image.php?id=11716'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:802px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11716'
                        onclick='fetchAssetData(11716);' class="asset-image" data-id="<?php echo $assetId11716; ?>"
                        data-room="<?php echo htmlspecialchars($room11716); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11716); ?>"
                        data-image="<?php echo base64_encode($upload_img11716); ?>"
                        data-status="<?php echo htmlspecialchars($status11716); ?>"
                        data-category="<?php echo htmlspecialchars($category11716); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11716); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11716); ?>; 
    position:absolute; top:204px; left:802px;'>
                    </div>

                    <!--ASSET 11717 -->
                    <img src='../image.php?id=11717'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:817px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11717'
                        onclick='fetchAssetData(11717);' class="asset-image" data-id="<?php echo $assetId11717; ?>"
                        data-room="<?php echo htmlspecialchars($room11717); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11717); ?>"
                        data-image="<?php echo base64_encode($upload_img11717); ?>"
                        data-status="<?php echo htmlspecialchars($status11717); ?>"
                        data-category="<?php echo htmlspecialchars($category11717); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11717); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11717); ?>; 
    position:absolute; top:204px; left:817px;'>
                    </div>

                    <!--ASSET 11718 -->
                    <img src='../image.php?id=11718'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:832px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11718'
                        onclick='fetchAssetData(11718);' class="asset-image" data-id="<?php echo $assetId11718; ?>"
                        data-room="<?php echo htmlspecialchars($room11718); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11718); ?>"
                        data-image="<?php echo base64_encode($upload_img11718); ?>"
                        data-status="<?php echo htmlspecialchars($status11718); ?>"
                        data-category="<?php echo htmlspecialchars($category11718); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11718); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11718); ?>; 
    position:absolute; top:204px; left:832px;'>
                    </div>

                    <!--ASSET 11719 -->
                    <img src='../image.php?id=11719'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:847px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11719'
                        onclick='fetchAssetData(11719);' class="asset-image" data-id="<?php echo $assetId11719; ?>"
                        data-room="<?php echo htmlspecialchars($room11719); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11719); ?>"
                        data-image="<?php echo base64_encode($upload_img11719); ?>"
                        data-status="<?php echo htmlspecialchars($status11719); ?>"
                        data-category="<?php echo htmlspecialchars($category11719); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11719); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11719); ?>; 
    position:absolute; top:204px; left:847px;'>
                    </div>

                    <!--ASSET 11720 -->
                    <img src='../image.php?id=11720'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:862px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11720'
                        onclick='fetchAssetData(11720);' class="asset-image" data-id="<?php echo $assetId11720; ?>"
                        data-room="<?php echo htmlspecialchars($room11720); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11720); ?>"
                        data-image="<?php echo base64_encode($upload_img11720); ?>"
                        data-status="<?php echo htmlspecialchars($status11720); ?>"
                        data-category="<?php echo htmlspecialchars($category11720); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11720); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11720); ?>; 
    position:absolute; top:204px; left:862px;'>
                    </div>

                    <!--ASSET 11722 -->
                    <img src='../image.php?id=11722'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:877px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11722'
                        onclick='fetchAssetData(11722);' class="asset-image" data-id="<?php echo $assetId11722; ?>"
                        data-room="<?php echo htmlspecialchars($room11722); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11722); ?>"
                        data-image="<?php echo base64_encode($upload_img11722); ?>"
                        data-status="<?php echo htmlspecialchars($status11722); ?>"
                        data-category="<?php echo htmlspecialchars($category11722); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11722); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11722); ?>; 
    position:absolute; top:204px; left:877px;'>
                    </div>

                    <!--ASSET 11723 -->
                    <img src='../image.php?id=11723'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:892px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11723'
                        onclick='fetchAssetData(11723);' class="asset-image" data-id="<?php echo $assetId11723; ?>"
                        data-room="<?php echo htmlspecialchars($room11723); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11723); ?>"
                        data-image="<?php echo base64_encode($upload_img11723); ?>"
                        data-status="<?php echo htmlspecialchars($status11723); ?>"
                        data-category="<?php echo htmlspecialchars($category11723); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11723); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11723); ?>; 
    position:absolute; top:204px; left:892px;'>
                    </div>


                    <!--ASSET 11721 -->
                    <img src='../image.php?id=11721'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:907px;  transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11721'
                        onclick='fetchAssetData(11721);' class="asset-image" data-id="<?php echo $assetId11721; ?>"
                        data-room="<?php echo htmlspecialchars($room11721); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11721); ?>"
                        data-image="<?php echo base64_encode($upload_img11721); ?>"
                        data-status="<?php echo htmlspecialchars($status11721); ?>"
                        data-category="<?php echo htmlspecialchars($category11721); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11721); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11721); ?>; 
    position:absolute; top:204px; left:907px;'>
                    </div>

                    <!--ASSET 11700 -->
                    <img src='../image.php?id=11700'
                        style='width:18px; cursor:pointer; position:absolute; top:201px; left:922px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11700'
                        onclick='fetchAssetData(11700);' class="asset-image" data-id="<?php echo $assetId11700; ?>"
                        data-room="<?php echo htmlspecialchars($room11700); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11700); ?>"
                        data-image="<?php echo base64_encode($upload_img11700); ?>"
                        data-status="<?php echo htmlspecialchars($status11700); ?>"
                        data-category="<?php echo htmlspecialchars($category11700); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11700); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11700); ?>; 
    position:absolute; top:204px; left:922px;'>
                    </div>

                    <!--ASSET 11724 -->
                    <img src='../image.php?id=11724'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:803px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11724'
                        onclick='fetchAssetData(11724);' class="asset-image" data-id="<?php echo $assetId11724; ?>"
                        data-room="<?php echo htmlspecialchars($room11724); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11724); ?>"
                        data-image="<?php echo base64_encode($upload_img11724); ?>"
                        data-status="<?php echo htmlspecialchars($status11724); ?>"
                        data-category="<?php echo htmlspecialchars($category11724); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11724); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11724); ?>; 
    position:absolute; top:120px; left:813px;'>
                    </div>

                    <!--ASSET 11725 -->
                    <img src='../image.php?id=11725'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:818px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11725'
                        onclick='fetchAssetData(11725);' class="asset-image" data-id="<?php echo $assetId11725; ?>"
                        data-room="<?php echo htmlspecialchars($room11725); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11725); ?>"
                        data-image="<?php echo base64_encode($upload_img11725); ?>"
                        data-status="<?php echo htmlspecialchars($status11725); ?>"
                        data-category="<?php echo htmlspecialchars($category11725); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11725); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11725); ?>; 
    position:absolute; top:120px; left:828px;'>
                    </div>

                    <!--ASSET 11726 -->
                    <img src='../image.php?id=11726'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:833px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11726'
                        onclick='fetchAssetData(11726);' class="asset-image" data-id="<?php echo $assetId11726; ?>"
                        data-room="<?php echo htmlspecialchars($room11726); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11726); ?>"
                        data-image="<?php echo base64_encode($upload_img11726); ?>"
                        data-status="<?php echo htmlspecialchars($status11726); ?>"
                        data-category="<?php echo htmlspecialchars($category11726); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11726); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11726); ?>; 
    position:absolute; top:120px; left:843px;'>
                    </div>

                    <!--ASSET 11727 -->
                    <img src='../image.php?id=11727'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:848px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11727'
                        onclick='fetchAssetData(11727);' class="asset-image" data-id="<?php echo $assetId11727; ?>"
                        data-room="<?php echo htmlspecialchars($room11727); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11727); ?>"
                        data-image="<?php echo base64_encode($upload_img11727); ?>"
                        data-status="<?php echo htmlspecialchars($status11727); ?>"
                        data-category="<?php echo htmlspecialchars($category11727); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11727); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11727); ?>; 
    position:absolute; top:120px; left:858px;'>
                    </div>

                    <!--ASSET 11728 -->
                    <img src='../image.php?id=11728'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:863px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11728'
                        onclick='fetchAssetData(11728);' class="asset-image" data-id="<?php echo $assetId11728; ?>"
                        data-room="<?php echo htmlspecialchars($room11728); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11728); ?>"
                        data-image="<?php echo base64_encode($upload_img11728); ?>"
                        data-status="<?php echo htmlspecialchars($status11728); ?>"
                        data-category="<?php echo htmlspecialchars($category11728); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11728); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11728); ?>; 
    position:absolute; top:120px; left:873px;'>
                    </div>

                    <!--ASSET 11729 -->
                    <img src='../image.php?id=11729'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:878px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11729'
                        onclick='fetchAssetData(11729);' class="asset-image" data-id="<?php echo $assetId11729; ?>"
                        data-room="<?php echo htmlspecialchars($room11729); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11729); ?>"
                        data-image="<?php echo base64_encode($upload_img11729); ?>"
                        data-status="<?php echo htmlspecialchars($status11729); ?>"
                        data-category="<?php echo htmlspecialchars($category11729); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11729); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11729); ?>; 
    position:absolute; top:120px; left:888px;'>
                    </div>

                    <!--ASSET 11730 -->
                    <img src='../image.php?id=11730'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:893px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11730'
                        onclick='fetchAssetData(11730);' class="asset-image" data-id="<?php echo $assetId11730; ?>"
                        data-room="<?php echo htmlspecialchars($room11730); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11730); ?>"
                        data-image="<?php echo base64_encode($upload_img11730); ?>"
                        data-status="<?php echo htmlspecialchars($status11730); ?>"
                        data-category="<?php echo htmlspecialchars($category11730); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11730); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11730); ?>; 
    position:absolute; top:120px; left:903px;'>
                    </div>

                    <!--ASSET 11731 -->
                    <img src='../image.php?id=11731'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:908px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11731'
                        onclick='fetchAssetData(11731);' class="asset-image" data-id="<?php echo $assetId11731; ?>"
                        data-room="<?php echo htmlspecialchars($room11731); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11731); ?>"
                        data-image="<?php echo base64_encode($upload_img11731); ?>"
                        data-status="<?php echo htmlspecialchars($status11731); ?>"
                        data-category="<?php echo htmlspecialchars($category11731); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11731); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11731); ?>; 
    position:absolute; top:120px; left:918px;'>
                    </div>

                    <!--ASSET 11732 -->
                    <img src='../image.php?id=11732'
                        style='width:12px; cursor:pointer; position:absolute; top:125px; left:924px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11732'
                        onclick='fetchAssetData(11732);' class="asset-image" data-id="<?php echo $assetId11732; ?>"
                        data-room="<?php echo htmlspecialchars($room11732); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11732); ?>"
                        data-image="<?php echo base64_encode($upload_img11732); ?>"
                        data-status="<?php echo htmlspecialchars($status11732); ?>"
                        data-category="<?php echo htmlspecialchars($category11732); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11732); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11732); ?>; 
    position:absolute; top:120px; left:934px;'>
                    </div>

                    <!--ASSET 11733 -->
                    <img src='../image.php?id=11733'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:803px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11733'
                        onclick='fetchAssetData(11733);' class="asset-image" data-id="<?php echo $assetId11733; ?>"
                        data-room="<?php echo htmlspecialchars($room11733); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11733); ?>"
                        data-image="<?php echo base64_encode($upload_img11733); ?>"
                        data-status="<?php echo htmlspecialchars($status11733); ?>"
                        data-category="<?php echo htmlspecialchars($category11733); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11733); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11733); ?>; 
    position:absolute; top:163px; left:813px;'>
                    </div>

                    <!--ASSET 11734 -->
                    <img src='../image.php?id=11734'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:818px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11734'
                        onclick='fetchAssetData(11734);' class="asset-image" data-id="<?php echo $assetId11734; ?>"
                        data-room="<?php echo htmlspecialchars($room11734); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11734); ?>"
                        data-image="<?php echo base64_encode($upload_img11734); ?>"
                        data-status="<?php echo htmlspecialchars($status11734); ?>"
                        data-category="<?php echo htmlspecialchars($category11734); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11734); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11734); ?>; 
    position:absolute; top:163px; left:828px;'>
                    </div>

                    <!--ASSET 11735 -->
                    <img src='../image.php?id=11735'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:833px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11735'
                        onclick='fetchAssetData(11735);' class="asset-image" data-id="<?php echo $assetId11735; ?>"
                        data-room="<?php echo htmlspecialchars($room11735); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11735); ?>"
                        data-image="<?php echo base64_encode($upload_img11735); ?>"
                        data-status="<?php echo htmlspecialchars($status11735); ?>"
                        data-category="<?php echo htmlspecialchars($category11735); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11735); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11735); ?>; 
    position:absolute; top:163px; left:843px;'>
                    </div>

                    <!--ASSET 11736 -->
                    <img src='../image.php?id=11736'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:848px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11736'
                        onclick='fetchAssetData(11736);' class="asset-image" data-id="<?php echo $assetId11736; ?>"
                        data-room="<?php echo htmlspecialchars($room11736); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11736); ?>"
                        data-image="<?php echo base64_encode($upload_img11736); ?>"
                        data-status="<?php echo htmlspecialchars($status11736); ?>"
                        data-category="<?php echo htmlspecialchars($category11736); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11736); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11736); ?>; 
    position:absolute; top:163px; left:858px;'>
                    </div>

                    <!--ASSET 11737 -->
                    <img src='../image.php?id=11737'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:863px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11737'
                        onclick='fetchAssetData(11737);' class="asset-image" data-id="<?php echo $assetId11737; ?>"
                        data-room="<?php echo htmlspecialchars($room11737); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11737); ?>"
                        data-image="<?php echo base64_encode($upload_img11737); ?>"
                        data-status="<?php echo htmlspecialchars($status11737); ?>"
                        data-category="<?php echo htmlspecialchars($category11737); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11737); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11737); ?>; 
    position:absolute; top:163px; left:873px;'>
                    </div>

                    <!--ASSET 11738 -->
                    <img src='../image.php?id=11738'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:878px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11738'
                        onclick='fetchAssetData(11738);' class="asset-image" data-id="<?php echo $assetId11738; ?>"
                        data-room="<?php echo htmlspecialchars($room11738); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11738); ?>"
                        data-image="<?php echo base64_encode($upload_img11738); ?>"
                        data-status="<?php echo htmlspecialchars($status11738); ?>"
                        data-category="<?php echo htmlspecialchars($category11738); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11738); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11738); ?>; 
    position:absolute; top:163px; left:888px;'>
                    </div>

                    <!--ASSET 11739 -->
                    <img src='../image.php?id=11739'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:893px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11739'
                        onclick='fetchAssetData(11739);' class="asset-image" data-id="<?php echo $assetId11739; ?>"
                        data-room="<?php echo htmlspecialchars($room11739); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11739); ?>"
                        data-image="<?php echo base64_encode($upload_img11739); ?>"
                        data-status="<?php echo htmlspecialchars($status11739); ?>"
                        data-category="<?php echo htmlspecialchars($category11739); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11739); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11739); ?>; 
    position:absolute; top:163px; left:903px;'>
                    </div>

                    <!--ASSET 11740 -->
                    <img src='../image.php?id=11740'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:908px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11740'
                        onclick='fetchAssetData(11740);' class="asset-image" data-id="<?php echo $assetId11740; ?>"
                        data-room="<?php echo htmlspecialchars($room11740); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11740); ?>"
                        data-image="<?php echo base64_encode($upload_img11740); ?>"
                        data-status="<?php echo htmlspecialchars($status11740); ?>"
                        data-category="<?php echo htmlspecialchars($category11740); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11740); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11740); ?>; 
    position:absolute; top:163px; left:918px;'>
                    </div>

                    <!--ASSET 11741 -->
                    <img src='../image.php?id=11741'
                        style='width:12px; cursor:pointer; position:absolute; top:158px; left:923px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11741'
                        onclick='fetchAssetData(11741);' class="asset-image" data-id="<?php echo $assetId11741; ?>"
                        data-room="<?php echo htmlspecialchars($room11741); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11741); ?>"
                        data-image="<?php echo base64_encode($upload_img11741); ?>"
                        data-status="<?php echo htmlspecialchars($status11741); ?>"
                        data-category="<?php echo htmlspecialchars($category11741); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11741); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11741); ?>; 
    position:absolute; top:163px; left:933px;'>
                    </div>

                    <!--ASSET 11742 -->
                    <img src='../image.php?id=11742'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:803px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11742'
                        onclick='fetchAssetData(11742);' class="asset-image" data-id="<?php echo $assetId11742; ?>"
                        data-room="<?php echo htmlspecialchars($room11742); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11742); ?>"
                        data-image="<?php echo base64_encode($upload_img11742); ?>"
                        data-status="<?php echo htmlspecialchars($status11742); ?>"
                        data-category="<?php echo htmlspecialchars($category11742); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11742); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11742); ?>; 
    position:absolute; top:177px; left:813px;'>
                    </div>

                    <!--ASSET 11743 -->
                    <img src='../image.php?id=11743'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:818px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11743'
                        onclick='fetchAssetData(11743);' class="asset-image" data-id="<?php echo $assetId11743; ?>"
                        data-room="<?php echo htmlspecialchars($room11743); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11743); ?>"
                        data-image="<?php echo base64_encode($upload_img11743); ?>"
                        data-status="<?php echo htmlspecialchars($status11743); ?>"
                        data-category="<?php echo htmlspecialchars($category11743); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11743); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11743); ?>; 
    position:absolute; top:177px; left:828px;'>
                    </div>

                    <!--ASSET 11744 -->
                    <img src='../image.php?id=11744'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:833px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11744'
                        onclick='fetchAssetData(11744);' class="asset-image" data-id="<?php echo $assetId11744; ?>"
                        data-room="<?php echo htmlspecialchars($room11744); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11744); ?>"
                        data-image="<?php echo base64_encode($upload_img11744); ?>"
                        data-status="<?php echo htmlspecialchars($status11744); ?>"
                        data-category="<?php echo htmlspecialchars($category11744); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11744); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11744); ?>; 
    position:absolute; top:177px; left:843px;'>
                    </div>

                    <!--ASSET 11745 -->
                    <img src='../image.php?id=11745'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:848px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11745'
                        onclick='fetchAssetData(11745);' class="asset-image" data-id="<?php echo $assetId11745; ?>"
                        data-room="<?php echo htmlspecialchars($room11745); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11745); ?>"
                        data-image="<?php echo base64_encode($upload_img11745); ?>"
                        data-status="<?php echo htmlspecialchars($status11745); ?>"
                        data-category="<?php echo htmlspecialchars($category11745); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11745); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11745); ?>; 
    position:absolute; top:177px; left:858px;'>
                    </div>



                    <!--ASSET 11747 -->
                    <img src='../image.php?id=11747'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:863px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11747'
                        onclick='fetchAssetData(11747);' class="asset-image" data-id="<?php echo $assetId11747; ?>"
                        data-room="<?php echo htmlspecialchars($room11747); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11747); ?>"
                        data-image="<?php echo base64_encode($upload_img11747); ?>"
                        data-status="<?php echo htmlspecialchars($status11747); ?>"
                        data-category="<?php echo htmlspecialchars($category11747); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11747); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11747); ?>; 
    position:absolute; top:177px; left:873px;'>
                    </div>

                    <!--ASSET 11748 -->
                    <img src='../image.php?id=11748'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:878px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11748'
                        onclick='fetchAssetData(11748);' class="asset-image" data-id="<?php echo $assetId11748; ?>"
                        data-room="<?php echo htmlspecialchars($room11748); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11748); ?>"
                        data-image="<?php echo base64_encode($upload_img11748); ?>"
                        data-status="<?php echo htmlspecialchars($status11748); ?>"
                        data-category="<?php echo htmlspecialchars($category11748); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11748); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11748); ?>; 
    position:absolute; top:177px; left:888px;'>
                    </div>

                    <!--ASSET 11749 -->
                    <img src='../image.php?id=11749'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:893px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11749'
                        onclick='fetchAssetData(11749);' class="asset-image" data-id="<?php echo $assetId11749; ?>"
                        data-room="<?php echo htmlspecialchars($room11749); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11749); ?>"
                        data-image="<?php echo base64_encode($upload_img11749); ?>"
                        data-status="<?php echo htmlspecialchars($status11749); ?>"
                        data-category="<?php echo htmlspecialchars($category11749); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11749); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11749); ?>; 
    position:absolute; top:177px; left:903px;'>
                    </div>

                    <!--ASSET 11750 -->
                    <img src='../image.php?id=11750'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:908px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11750'
                        onclick='fetchAssetData(11750);' class="asset-image" data-id="<?php echo $assetId11750; ?>"
                        data-room="<?php echo htmlspecialchars($room11750); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11750); ?>"
                        data-image="<?php echo base64_encode($upload_img11750); ?>"
                        data-status="<?php echo htmlspecialchars($status11750); ?>"
                        data-category="<?php echo htmlspecialchars($category11750); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11750); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11750); ?>; 
    position:absolute; top:177px; left:918px;'>
                    </div>

                    <!--ASSET 11751 -->
                    <img src='../image.php?id=11751'
                        style='width:12px; cursor:pointer; position:absolute; top:182px; left:923px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11751'
                        onclick='fetchAssetData(11751);' class="asset-image" data-id="<?php echo $assetId11751; ?>"
                        data-room="<?php echo htmlspecialchars($room11751); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11751); ?>"
                        data-image="<?php echo base64_encode($upload_img11751); ?>"
                        data-status="<?php echo htmlspecialchars($status11751); ?>"
                        data-category="<?php echo htmlspecialchars($category11751); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11751); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11751); ?>; 
    position:absolute; top:177px; left:933px;'>
                    </div>

                    <!--ASSET 11752 -->
                    <img src='../image.php?id=11752'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:803px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11752'
                        onclick='fetchAssetData(11752);' class="asset-image" data-id="<?php echo $assetId11752; ?>"
                        data-room="<?php echo htmlspecialchars($room11752); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11752); ?>"
                        data-image="<?php echo base64_encode($upload_img11752); ?>"
                        data-status="<?php echo htmlspecialchars($status11752); ?>"
                        data-category="<?php echo htmlspecialchars($category11752); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11752); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11752); ?>; 
    position:absolute; top:218px; left:813px;'>
                    </div>

                    <!--ASSET 11753 -->
                    <img src='../image.php?id=11753'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:818px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11753'
                        onclick='fetchAssetData(11753);' class="asset-image" data-id="<?php echo $assetId11753; ?>"
                        data-room="<?php echo htmlspecialchars($room11753); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11753); ?>"
                        data-image="<?php echo base64_encode($upload_img11753); ?>"
                        data-status="<?php echo htmlspecialchars($status11753); ?>"
                        data-category="<?php echo htmlspecialchars($category11753); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11753); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11753); ?>; 
    position:absolute; top:218px; left:828px;'>
                    </div>

                    <!--ASSET 11754 -->
                    <img src='../image.php?id=11754'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:833px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11754'
                        onclick='fetchAssetData(11754);' class="asset-image" data-id="<?php echo $assetId11754; ?>"
                        data-room="<?php echo htmlspecialchars($room11754); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11754); ?>"
                        data-image="<?php echo base64_encode($upload_img11754); ?>"
                        data-status="<?php echo htmlspecialchars($status11754); ?>"
                        data-category="<?php echo htmlspecialchars($category11754); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11754); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11754); ?>; 
    position:absolute; top:218px; left:843px;'>
                    </div>

                    <!--ASSET 11755 -->
                    <img src='../image.php?id=11755'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:848px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11755'
                        onclick='fetchAssetData(11755);' class="asset-image" data-id="<?php echo $assetId11755; ?>"
                        data-room="<?php echo htmlspecialchars($room11755); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11755); ?>"
                        data-image="<?php echo base64_encode($upload_img11755); ?>"
                        data-status="<?php echo htmlspecialchars($status11755); ?>"
                        data-category="<?php echo htmlspecialchars($category11755); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11755); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11755); ?>; 
    position:absolute; top:218px; left:858px;'>
                    </div>

                    <!--ASSET 11756 -->
                    <img src='../image.php?id=11756'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:863px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11756'
                        onclick='fetchAssetData(11756);' class="asset-image" data-id="<?php echo $assetId11756; ?>"
                        data-room="<?php echo htmlspecialchars($room11756); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11756); ?>"
                        data-image="<?php echo base64_encode($upload_img11756); ?>"
                        data-status="<?php echo htmlspecialchars($status11756); ?>"
                        data-category="<?php echo htmlspecialchars($category11756); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11756); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11756); ?>; 
    position:absolute; top:218px; left:873px;'>
                    </div>

                    <!--ASSET 11757 -->
                    <img src='../image.php?id=11757'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:878px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11757'
                        onclick='fetchAssetData(11757);' class="asset-image" data-id="<?php echo $assetId11757; ?>"
                        data-room="<?php echo htmlspecialchars($room11757); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11757); ?>"
                        data-image="<?php echo base64_encode($upload_img11757); ?>"
                        data-status="<?php echo htmlspecialchars($status11757); ?>"
                        data-category="<?php echo htmlspecialchars($category11757); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11757); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11757); ?>; 
    position:absolute; top:218px; left:888px;'>
                    </div>

                    <!--ASSET 11758 -->
                    <img src='../image.php?id=11758'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:893px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11758'
                        onclick='fetchAssetData(11758);' class="asset-image" data-id="<?php echo $assetId11758; ?>"
                        data-room="<?php echo htmlspecialchars($room11758); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11758); ?>"
                        data-image="<?php echo base64_encode($upload_img11758); ?>"
                        data-status="<?php echo htmlspecialchars($status11758); ?>"
                        data-category="<?php echo htmlspecialchars($category11758); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11758); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11758); ?>; 
    position:absolute; top:218px; left:903px;'>
                    </div>

                    <!--ASSET 11759 -->
                    <img src='../image.php?id=11759'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:908px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11759'
                        onclick='fetchAssetData(11759);' class="asset-image" data-id="<?php echo $assetId11759; ?>"
                        data-room="<?php echo htmlspecialchars($room11759); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11759); ?>"
                        data-image="<?php echo base64_encode($upload_img11759); ?>"
                        data-status="<?php echo htmlspecialchars($status11759); ?>"
                        data-category="<?php echo htmlspecialchars($category11759); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11759); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11759); ?>; 
    position:absolute; top:218px; left:918px;'>
                    </div>

                    <!--ASSET 11746 -->
                    <img src='../image.php?id=11746'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:923px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11746'
                        onclick='fetchAssetData(11746);' class="asset-image" data-id="<?php echo $assetId11746; ?>"
                        data-room="<?php echo htmlspecialchars($room11746); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11746); ?>"
                        data-image="<?php echo base64_encode($upload_img11746); ?>"
                        data-status="<?php echo htmlspecialchars($status11746); ?>"
                        data-category="<?php echo htmlspecialchars($category11746); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11746); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11746); ?>; 
    position:absolute; top:218px; left:933px;'>
                    </div>

                    <!--ASSET 11760 -->
                    <img src='../image.php?id=11760'
                        style='width:20px; cursor:pointer; position:absolute; top:238px; left:808px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11760'
                        onclick='fetchAssetData(11760);' class="asset-image" data-id="<?php echo $assetId11760; ?>"
                        data-room="<?php echo htmlspecialchars($room11760); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11760); ?>"
                        data-image="<?php echo base64_encode($upload_img11760); ?>"
                        data-status="<?php echo htmlspecialchars($status11760); ?>"
                        data-category="<?php echo htmlspecialchars($category11760); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11760); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11760); ?>; 
    position:absolute; top:218px; left:918px;'>
                    </div>

                    <!--ASSET 11761 -->
                    <img src='../image.php?id=11761'
                        style='width:20px; cursor:pointer; position:absolute; top:238px; left:975px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11761'
                        onclick='fetchAssetData(11761);' class="asset-image" data-id="<?php echo $assetId11761; ?>"
                        data-room="<?php echo htmlspecialchars($room11761); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11761); ?>"
                        data-image="<?php echo base64_encode($upload_img11761); ?>"
                        data-status="<?php echo htmlspecialchars($status11761); ?>"
                        data-category="<?php echo htmlspecialchars($category11761); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11761); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11761); ?>; 
    position:absolute; top:218px; left:918px;'>
                    </div>

                    <!--ASSET 11762 -->
                    <img src='../image.php?id=7183'
                        style='width:20px; cursor:pointer; position:absolute; top:120px; left:710px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11762'
                        onclick='fetchAssetData(11762);' class="asset-image" data-id="<?php echo $assetId11762; ?>"
                        data-room="<?php echo htmlspecialchars($room11762); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11762); ?>"
                        data-image="<?php echo base64_encode($upload_img11762); ?>"
                        data-status="<?php echo htmlspecialchars($status11762); ?>"
                        data-category="<?php echo htmlspecialchars($category11762); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11762); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11762); ?>; 
    position:absolute; top:218px; left:918px;'>
                    </div>

 <!--ASSET 11763 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:150px; left:710px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11763'
                        onclick='fetchAssetData(11763);' class="asset-image" data-id="<?php echo $assetId11763; ?>"
                        data-room="<?php echo htmlspecialchars($room11763); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11763); ?>"
                        data-image="<?php echo base64_encode($upload_img11763); ?>"
                        data-status="<?php echo htmlspecialchars($status11763); ?>"
                        data-category="<?php echo htmlspecialchars($category11763); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11763); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11763); ?>; 
    position:absolute; top:145px; left:720px;'>
                    </div>

                    <!--ASSET 11764 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:150px; left:760px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11764'
                        onclick='fetchAssetData(11764);' class="asset-image" data-id="<?php echo $assetId11764; ?>"
                        data-room="<?php echo htmlspecialchars($room11764); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11764); ?>"
                        data-image="<?php echo base64_encode($upload_img11764); ?>"
                        data-status="<?php echo htmlspecialchars($status11764); ?>"
                        data-category="<?php echo htmlspecialchars($category11764); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11764); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11764); ?>; 
    position:absolute; top:145px; left:770px;'>
                    </div>

                    <!--ASSET 11765 -->
                    <img src='../image.php?id=11765'
                        style='width:20px; cursor:pointer; position:absolute; top:120px; left:730px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11765'
                        onclick='fetchAssetData(11765);' class="asset-image" data-id="<?php echo $assetId11765; ?>"
                        data-room="<?php echo htmlspecialchars($room11765); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11765); ?>"
                        data-image="<?php echo base64_encode($upload_img11765); ?>"
                        data-status="<?php echo htmlspecialchars($status11765); ?>"
                        data-category="<?php echo htmlspecialchars($category11765); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11765); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11765); ?>; 
    position:absolute; top:115px; left:740px;'>
                    </div>

                    <!--ASSET 11764 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:760px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11764'
                        onclick='fetchAssetData(11764);' class="asset-image" data-id="<?php echo $assetId11764; ?>"
                        data-room="<?php echo htmlspecialchars($room11764); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11764); ?>"
                        data-image="<?php echo base64_encode($upload_img11764); ?>"
                        data-status="<?php echo htmlspecialchars($status11764); ?>"
                        data-category="<?php echo htmlspecialchars($category11764); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11764); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11764); ?>; 
    position:absolute; top:180px; left:770px;'>
                    </div>


                    <!--ASSET 11766 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:760px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11766'
                        onclick='fetchAssetData(11766);' class="asset-image" data-id="<?php echo $assetId11766; ?>"
                        data-room="<?php echo htmlspecialchars($room11766); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11766); ?>"
                        data-image="<?php echo base64_encode($upload_img11766); ?>"
                        data-status="<?php echo htmlspecialchars($status11766); ?>"
                        data-category="<?php echo htmlspecialchars($category11766); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11766); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11766); ?>; 
    position:absolute; top:180px; left:770px;'>
                    </div>

                    <!--ASSET 11767 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:710px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11767'
                        onclick='fetchAssetData(11767);' class="asset-image" data-id="<?php echo $assetId11767; ?>"
                        data-room="<?php echo htmlspecialchars($room11767); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11767); ?>"
                        data-image="<?php echo base64_encode($upload_img11767); ?>"
                        data-status="<?php echo htmlspecialchars($status11767); ?>"
                        data-category="<?php echo htmlspecialchars($category11767); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11767); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11767); ?>; 
    position:absolute; top:180px; left:720px;'>
                    </div>

                    <!--ASSET 11768 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:710px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11768'
                        onclick='fetchAssetData(11768);' class="asset-image" data-id="<?php echo $assetId11768; ?>"
                        data-room="<?php echo htmlspecialchars($room11768); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11768); ?>"
                        data-image="<?php echo base64_encode($upload_img11768); ?>"
                        data-status="<?php echo htmlspecialchars($status11768); ?>"
                        data-category="<?php echo htmlspecialchars($category11768); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11768); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11768); ?>; 
    position:absolute; top:210px; left:720px;'>
                    </div>


                    <!--ASSET 11769 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:760px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11769'
                        onclick='fetchAssetData(11769);' class="asset-image" data-id="<?php echo $assetId11769; ?>"
                        data-room="<?php echo htmlspecialchars($room11769); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11769); ?>"
                        data-image="<?php echo base64_encode($upload_img11769); ?>"
                        data-status="<?php echo htmlspecialchars($status11769); ?>"
                        data-category="<?php echo htmlspecialchars($category11769); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11769); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11769); ?>; 
    position:absolute; top:210px; left:770px;'>
                    </div>

                    <!--ASSET 11771 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:120px; left:680px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11771'
                        onclick='fetchAssetData(11771);' class="asset-image" data-id="<?php echo $assetId11771; ?>"
                        data-room="<?php echo htmlspecialchars($room11771); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11771); ?>"
                        data-image="<?php echo base64_encode($upload_img11771); ?>"
                        data-status="<?php echo htmlspecialchars($status11771); ?>"
                        data-category="<?php echo htmlspecialchars($category11771); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11771); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11771); ?>; 
    position:absolute; top:115px; left:690px;'>
                    </div>

                    <!--ASSET 11772 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:120px; left:620px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11772'
                        onclick='fetchAssetData(11772);' class="asset-image" data-id="<?php echo $assetId11772; ?>"
                        data-room="<?php echo htmlspecialchars($room11772); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11772); ?>"
                        data-image="<?php echo base64_encode($upload_img11772); ?>"
                        data-status="<?php echo htmlspecialchars($status11772); ?>"
                        data-category="<?php echo htmlspecialchars($category11772); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11772); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11772); ?>; 
    position:absolute; top:115px; left:630px;'>
                    </div>

                    <!--ASSET 11773 -->
                    <img src='../image.php?id=11773'
                        style='width:20px; cursor:pointer; position:absolute; top:130px; left:607px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11773'
                        onclick='fetchAssetData(11773);' class="asset-image" data-id="<?php echo $assetId11773; ?>"
                        data-room="<?php echo htmlspecialchars($room11773); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11773); ?>"
                        data-image="<?php echo base64_encode($upload_img11773); ?>"
                        data-status="<?php echo htmlspecialchars($status11773); ?>"
                        data-category="<?php echo htmlspecialchars($category11773); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11773); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11773); ?>; 
    position:absolute; top:125px; left:617px;'>
                    </div>

                    <!--ASSET 11654 --><!--SHOULD BE REPLACED ROOOM 3 CASSETE AIRCON -->
                    <img src='../image.php?id=11654'
                        style='width:20px; cursor:pointer; position:absolute; top:140px; left:980px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11654'
                        onclick='fetchAssetData(11654);' class="asset-image" data-id="<?php echo $assetId11654; ?>"
                        data-room="<?php echo htmlspecialchars($room11654); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11654); ?>"
                        data-image="<?php echo base64_encode($upload_img11654); ?>"
                        data-status="<?php echo htmlspecialchars($status11654); ?>"
                        data-category="<?php echo htmlspecialchars($category11654); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11654); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11654); ?>; 
    position:absolute; top:135px; left:1000px;'>
                    </div>

                    <!--ASSET 11654 --><!--SHOULD BE REPLACED ROOOM 3 CASSETE AIRCON -->
                    <img src='../image.php?id=11654'
                        style='width:20px; cursor:pointer; position:absolute; top:190px; left:980px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11654'
                        onclick='fetchAssetData(11654);' class="asset-image" data-id="<?php echo $assetId11654; ?>"
                        data-room="<?php echo htmlspecialchars($room11654); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11654); ?>"
                        data-image="<?php echo base64_encode($upload_img11654); ?>"
                        data-status="<?php echo htmlspecialchars($status11654); ?>"
                        data-category="<?php echo htmlspecialchars($category11654); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11654); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11654); ?>; 
    position:absolute; top:185px; left:1000px;'>
                    </div>

                    <!--ASSET 11771 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:120px; left:680px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11771'
                        onclick='fetchAssetData(11771);' class="asset-image" data-id="<?php echo $assetId11771; ?>"
                        data-room="<?php echo htmlspecialchars($room11771); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11771); ?>"
                        data-image="<?php echo base64_encode($upload_img11771); ?>"
                        data-status="<?php echo htmlspecialchars($status11771); ?>"
                        data-category="<?php echo htmlspecialchars($category11771); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11771); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11771); ?>; 
    position:absolute; top:115px; left:690px;'>
                    </div>

                    <!--ASSET 11774 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:620px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11774'
                        onclick='fetchAssetData(11774);' class="asset-image" data-id="<?php echo $assetId11774; ?>"
                        data-room="<?php echo htmlspecialchars($room11774); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11774); ?>"
                        data-image="<?php echo base64_encode($upload_img11774); ?>"
                        data-status="<?php echo htmlspecialchars($status11774); ?>"
                        data-category="<?php echo htmlspecialchars($category11774); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11774); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11774); ?>; 
    position:absolute; top:150px; left:630px;'>
                    </div>

                    <!--ASSET 11775 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:155px; left:680px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11775'
                        onclick='fetchAssetData(11775);' class="asset-image" data-id="<?php echo $assetId11775; ?>"
                        data-room="<?php echo htmlspecialchars($room11775); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11775); ?>"
                        data-image="<?php echo base64_encode($upload_img11775); ?>"
                        data-status="<?php echo htmlspecialchars($status11775); ?>"
                        data-category="<?php echo htmlspecialchars($category11775); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11775); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11775); ?>; 
    position:absolute; top:155px; left:690px;'>
                    </div>

                    <!--ASSET 11776 FOR DOOR -->

                    <!--ASSET 11777 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer;z-index:1; position:absolute; top:185px; left:680px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11777'
                        onclick='fetchAssetData(11777);' class="asset-image" data-id="<?php echo $assetId11777; ?>"
                        data-room="<?php echo htmlspecialchars($room11777); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11777); ?>"
                        data-image="<?php echo base64_encode($upload_img11777); ?>"
                        data-status="<?php echo htmlspecialchars($status11777); ?>"
                        data-category="<?php echo htmlspecialchars($category11777); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11777); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11777); ?>; 
    position:absolute; top:180px; left:690px;'>
                    </div>

                    <!--ASSET 11778 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; z-index:1; position:absolute; top:215px; left:680px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11778'
                        onclick='fetchAssetData(11778);' class="asset-image" data-id="<?php echo $assetId11778; ?>"
                        data-room="<?php echo htmlspecialchars($room11778); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11778); ?>"
                        data-image="<?php echo base64_encode($upload_img11778); ?>"
                        data-status="<?php echo htmlspecialchars($status11778); ?>"
                        data-category="<?php echo htmlspecialchars($category11778); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11778); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11778); ?>; 
    position:absolute; top:210px; left:690px;'>
                    </div>

                    <!--ASSET 11779 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:185px; left:620px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11779'
                        onclick='fetchAssetData(11779);' class="asset-image" data-id="<?php echo $assetId11779; ?>"
                        data-room="<?php echo htmlspecialchars($room11779); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11779); ?>"
                        data-image="<?php echo base64_encode($upload_img11779); ?>"
                        data-status="<?php echo htmlspecialchars($status11779); ?>"
                        data-category="<?php echo htmlspecialchars($category11779); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11779); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11779); ?>; 
    position:absolute; top:180px; left:630px;'>
                    </div>

                    <!--ASSET 11780 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:215px; left:620px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11780'
                        onclick='fetchAssetData(11780);' class="asset-image" data-id="<?php echo $assetId11780; ?>"
                        data-room="<?php echo htmlspecialchars($room11780); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11780); ?>"
                        data-image="<?php echo base64_encode($upload_img11780); ?>"
                        data-status="<?php echo htmlspecialchars($status11780); ?>"
                        data-category="<?php echo htmlspecialchars($category11780); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11780); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11780); ?>; 
    position:absolute; top:210px; left:630px;'>
                    </div>

                    <!--ASSET 11781 -->
                    <img src='../image.php?id=11781'
                        style='width:15px; cursor:pointer; position:absolute; top:190px; left:685px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11781'
                        onclick='fetchAssetData(11781);' class="asset-image" data-id="<?php echo $assetId11781; ?>"
                        data-room="<?php echo htmlspecialchars($room11781); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11781); ?>"
                        data-image="<?php echo base64_encode($upload_img11781); ?>"
                        data-status="<?php echo htmlspecialchars($status11781); ?>"
                        data-category="<?php echo htmlspecialchars($category11781); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11781); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11781); ?>; 
    position:absolute; top:185px; left:695px;'>
                    </div>

                    <!--ASSET 11782 -->
                    <img src='../image.php?id=11782'
                        style='width:20px; cursor:pointer; position:absolute; top:190px; left:655px;' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11782'
                        onclick='fetchAssetData(11782);' class="asset-image" data-id="<?php echo $assetId11782; ?>"
                        data-room="<?php echo htmlspecialchars($room11782); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11782); ?>"
                        data-image="<?php echo base64_encode($upload_img11782); ?>"
                        data-status="<?php echo htmlspecialchars($status11782); ?>"
                        data-category="<?php echo htmlspecialchars($category11782); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11782); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11782); ?>; 
    position:absolute; top:190px; left:668px;'>
                    </div>

                    <!--ASSET 11783 -->
                    <img src='../image.php?id=11783'
                        style='width:20px; cursor:pointer; position:absolute; top:200px; left:655.5px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11783'
                        onclick='fetchAssetData(11783);' class="asset-image" data-id="<?php echo $assetId11783; ?>"
                        data-room="<?php echo htmlspecialchars($room11783); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11783); ?>"
                        data-image="<?php echo base64_encode($upload_img11783); ?>"
                        data-status="<?php echo htmlspecialchars($status11783); ?>"
                        data-category="<?php echo htmlspecialchars($category11783); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11783); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11783); ?>; 
    position:absolute; top:205px; left:655.5px;'>
                    </div>

                    <!--ASSET 11784 -->
                    <img src='../image.php?id=11784'
                        style='width:12px; cursor:pointer; position:absolute; top:180px; left:658px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11784'
                        onclick='fetchAssetData(11784);' class="asset-image" data-id="<?php echo $assetId11784; ?>"
                        data-room="<?php echo htmlspecialchars($room11784); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11784); ?>"
                        data-image="<?php echo base64_encode($upload_img11784); ?>"
                        data-status="<?php echo htmlspecialchars($status11784); ?>"
                        data-category="<?php echo htmlspecialchars($category11784); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11784); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11784); ?>; 
    position:absolute; top:175px; left:668px;'>
                    </div>

                    <!--ASSET 11785 -->
                    <img src='../image.php?id=11785'
                        style='width:12px; cursor:pointer; position:absolute; top:213px; left:660px; transform: rotate(90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11785'
                        onclick='fetchAssetData(11785);' class="asset-image" data-id="<?php echo $assetId11785; ?>"
                        data-room="<?php echo htmlspecialchars($room11785); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11785); ?>"
                        data-image="<?php echo base64_encode($upload_img11785); ?>"
                        data-status="<?php echo htmlspecialchars($status11785); ?>"
                        data-category="<?php echo htmlspecialchars($category11785); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11785); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11785); ?>; 
    position:absolute; top:218px; left:670px;'>
                    </div>
                    <!--ASSET 11786 -->
                    <img src='../image.php?id=11786'
                        style='width:12px; cursor:pointer; position:absolute; top:196px; left:644px; transform: rotate(180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11786'
                        onclick='fetchAssetData(11786);' class="asset-image" data-id="<?php echo $assetId11786; ?>"
                        data-room="<?php echo htmlspecialchars($room11786); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11786); ?>"
                        data-image="<?php echo base64_encode($upload_img11786); ?>"
                        data-status="<?php echo htmlspecialchars($status11786); ?>"
                        data-category="<?php echo htmlspecialchars($category11786); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11786); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11786); ?>; 
    position:absolute; top:190px; left:639px;'>
                    </div>

                    <!--ASSET 11787 DOOR-->

                    <!--ASSET 11788 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:120px; left:580px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11788'
                        onclick='fetchAssetData(11788);' class="asset-image" data-id="<?php echo $assetId11788; ?>"
                        data-room="<?php echo htmlspecialchars($room11788); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11788); ?>"
                        data-image="<?php echo base64_encode($upload_img11788); ?>"
                        data-status="<?php echo htmlspecialchars($status11788); ?>"
                        data-category="<?php echo htmlspecialchars($category11788); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11788); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11788); ?>; 
    position:absolute; top:115px; left:590px;'>
                    </div>

                    <!--ASSET 11789 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:180px; left:580px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11789'
                        onclick='fetchAssetData(11789);' class="asset-image" data-id="<?php echo $assetId11789; ?>"
                        data-room="<?php echo htmlspecialchars($room11789); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11789); ?>"
                        data-image="<?php echo base64_encode($upload_img11789); ?>"
                        data-status="<?php echo htmlspecialchars($status11789); ?>"
                        data-category="<?php echo htmlspecialchars($category11789); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11789); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11789); ?>; 
    position:absolute; top:175px; left:590px;'>
                    </div>

                    <!--ASSET 11790 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:120px; left:500px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11790'
                        onclick='fetchAssetData(11790);' class="asset-image" data-id="<?php echo $assetId11790; ?>"
                        data-room="<?php echo htmlspecialchars($room11790); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11790); ?>"
                        data-image="<?php echo base64_encode($upload_img11790); ?>"
                        data-status="<?php echo htmlspecialchars($status11790); ?>"
                        data-category="<?php echo htmlspecialchars($category11790); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11790); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11790); ?>; 
    position:absolute; top:115px; left:510px;'>
                    </div>

                    <!--ASSET 11791 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:180px; left:500px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11791'
                        onclick='fetchAssetData(11791);' class="asset-image" data-id="<?php echo $assetId11791; ?>"
                        data-room="<?php echo htmlspecialchars($room11791); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11791); ?>"
                        data-image="<?php echo base64_encode($upload_img11791); ?>"
                        data-status="<?php echo htmlspecialchars($status11791); ?>"
                        data-category="<?php echo htmlspecialchars($category11791); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11791); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11791); ?>; 
    position:absolute; top:175px; left:510px;'>
                    </div>

                    <!--ASSET 11792 ELEVATOR -->
                    <!--ASSET 11793 ELEVATOR -->

                    <!--ASSET 11794 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:207px; left:458px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11794'
                        onclick='fetchAssetData(11794);' class="asset-image" data-id="<?php echo $assetId11794; ?>"
                        data-room="<?php echo htmlspecialchars($room11794); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11794); ?>"
                        data-image="<?php echo base64_encode($upload_img11794); ?>"
                        data-status="<?php echo htmlspecialchars($status11794); ?>"
                        data-category="<?php echo htmlspecialchars($category11794); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11794); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11794); ?>; 
    position:absolute; top:202px; left:468px;'>
                    </div>

                     <!--ASSET 11795 -->
                     <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:207px; left:415px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11795'
                        onclick='fetchAssetData(11795);' class="asset-image" data-id="<?php echo $assetId11795; ?>"
                        data-room="<?php echo htmlspecialchars($room11795); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11795); ?>"
                        data-image="<?php echo base64_encode($upload_img11795); ?>"
                        data-status="<?php echo htmlspecialchars($status11795); ?>"
                        data-category="<?php echo htmlspecialchars($category11795); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11795); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11795); ?>; 
    position:absolute; top:202px; left:425px;'>
                    </div>

                    <!--ASSET 11796 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:190px; left:220px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11796'
                        onclick='fetchAssetData(11796);' class="asset-image" data-id="<?php echo $assetId11796; ?>"
                        data-room="<?php echo htmlspecialchars($room11796); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11796); ?>"
                        data-image="<?php echo base64_encode($upload_img11796); ?>"
                        data-status="<?php echo htmlspecialchars($status11796); ?>"
                        data-category="<?php echo htmlspecialchars($category11796); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11796); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11796); ?>; 
    position:absolute; top:185px; left:230px;'>
                    </div>

                    <!--ASSET 11797 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:190px; left:275px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11797'
                        onclick='fetchAssetData(11797);' class="asset-image" data-id="<?php echo $assetId11797; ?>"
                        data-room="<?php echo htmlspecialchars($room11797); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11797); ?>"
                        data-image="<?php echo base64_encode($upload_img11797); ?>"
                        data-status="<?php echo htmlspecialchars($status11797); ?>"
                        data-category="<?php echo htmlspecialchars($category11797); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11797); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11797); ?>; 
    position:absolute; top:185px; left:285px;'>
                    </div>

                    <!--ASSET 11798 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:235px; left:220px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11798'
                        onclick='fetchAssetData(11798);' class="asset-image" data-id="<?php echo $assetId11798; ?>"
                        data-room="<?php echo htmlspecialchars($room11798); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11798); ?>"
                        data-image="<?php echo base64_encode($upload_img11798); ?>"
                        data-status="<?php echo htmlspecialchars($status11798); ?>"
                        data-category="<?php echo htmlspecialchars($category11798); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11798); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11798); ?>; 
    position:absolute; top:230px; left:230px;'>
                    </div>

                    <!--ASSET 11799 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:235px; left:275px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11799'
                        onclick='fetchAssetData(11799);' class="asset-image" data-id="<?php echo $assetId11799; ?>"
                        data-room="<?php echo htmlspecialchars($room11799); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11799); ?>"
                        data-image="<?php echo base64_encode($upload_img11799); ?>"
                        data-status="<?php echo htmlspecialchars($status11799); ?>"
                        data-category="<?php echo htmlspecialchars($category11799); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11799); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11799); ?>; 
    position:absolute; top:230px; left:285px;'>
                    </div>

                    <!--ASSET 11800 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:190px; left:330px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11780'
                        onclick='fetchAssetData(11780);' class="asset-image" data-id="<?php echo $assetId11780; ?>"
                        data-room="<?php echo htmlspecialchars($room11780); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11780); ?>"
                        data-image="<?php echo base64_encode($upload_img11780); ?>"
                        data-status="<?php echo htmlspecialchars($status11780); ?>"
                        data-category="<?php echo htmlspecialchars($category11780); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11780); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11780); ?>; 
    position:absolute; top:185px; left:340px;'>
                    </div>

                    <!--ASSET 11801 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:190px; left:375px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11781'
                        onclick='fetchAssetData(11781);' class="asset-image" data-id="<?php echo $assetId11781; ?>"
                        data-room="<?php echo htmlspecialchars($room11781); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11781); ?>"
                        data-image="<?php echo base64_encode($upload_img11781); ?>"
                        data-status="<?php echo htmlspecialchars($status11781); ?>"
                        data-category="<?php echo htmlspecialchars($category11781); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11781); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11781); ?>; 
    position:absolute; top:185px; left:385px;'>
                    </div>

                    <!--ASSET 11802 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:235px; left:375px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11782'
                        onclick='fetchAssetData(11782);' class="asset-image" data-id="<?php echo $assetId11782; ?>"
                        data-room="<?php echo htmlspecialchars($room11782); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11782); ?>"
                        data-image="<?php echo base64_encode($upload_img11782); ?>"
                        data-status="<?php echo htmlspecialchars($status11782); ?>"
                        data-category="<?php echo htmlspecialchars($category11782); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11782); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11782); ?>; 
    position:absolute; top:230px; left:385px;'>
                    </div>

                    <!--ASSET 11803 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:235px; left:330px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11783'
                        onclick='fetchAssetData(11783);' class="asset-image" data-id="<?php echo $assetId11783; ?>"
                        data-room="<?php echo htmlspecialchars($room11783); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11783); ?>"
                        data-image="<?php echo base64_encode($upload_img11783); ?>"
                        data-status="<?php echo htmlspecialchars($status11783); ?>"
                        data-category="<?php echo htmlspecialchars($category11783); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11783); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11783); ?>; 
    position:absolute; top:230px; left:340px;'>
                    </div>

                    <!--ASSET 11804 -->
                    <img src='../image.php?id=11804'
                        style='width:15px; cursor:pointer; position:absolute; top:180px; left:250px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11804'
                        onclick='fetchAssetData(11804);' class="asset-image" data-id="<?php echo $assetId11804; ?>"
                        data-room="<?php echo htmlspecialchars($room11804); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11804); ?>"
                        data-image="<?php echo base64_encode($upload_img11804); ?>"
                        data-status="<?php echo htmlspecialchars($status11804); ?>"
                        data-category="<?php echo htmlspecialchars($category11804); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11804); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11804); ?>; 
    position:absolute; top:190px; left:265px;'>
                    </div>

                    <!--ASSET 11805 -->
                    <img src='../image.php?id=11805'
                        style='width:15px; cursor:pointer; position:absolute; top:180px; left:355px; transform: rotate(-90deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11805'
                        onclick='fetchAssetData(11805);' class="asset-image" data-id="<?php echo $assetId11805; ?>"
                        data-room="<?php echo htmlspecialchars($room11805); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11805); ?>"
                        data-image="<?php echo base64_encode($upload_img11805); ?>"
                        data-status="<?php echo htmlspecialchars($status11805); ?>"
                        data-category="<?php echo htmlspecialchars($category11805); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11805); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11805); ?>; 
    position:absolute; top:190px; left:370px;'>
                    </div>

                    <!--ASSET 11806 -->
                    <img src='../image.php?id=11811'
                        style='width:20px; cursor:pointer; position:absolute; top:260px; left:216px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11806'
                        onclick='fetchAssetData(11806);' class="asset-image" data-id="<?php echo $assetId11806; ?>"
                        data-room="<?php echo htmlspecialchars($room11806); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11806); ?>"
                        data-image="<?php echo base64_encode($upload_img11806); ?>"
                        data-status="<?php echo htmlspecialchars($status11806); ?>"
                        data-category="<?php echo htmlspecialchars($category11806); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11806); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11806); ?>; 
    position:absolute; top:265px; left:226px;'>
                    </div>

                    <!--ASSET 11807 -->
                    <img src='../image.php?id=11811'
                        style='width:20px; cursor:pointer; position:absolute; top:260px; left:375px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11807'
                        onclick='fetchAssetData(11807);' class="asset-image" data-id="<?php echo $assetId11807; ?>"
                        data-room="<?php echo htmlspecialchars($room11807); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11807); ?>"
                        data-image="<?php echo base64_encode($upload_img11807); ?>"
                        data-status="<?php echo htmlspecialchars($status11807); ?>"
                        data-category="<?php echo htmlspecialchars($category11807); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11807); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11807); ?>; 
    position:absolute; top:265px; left:385px;'>
                    </div>

                    <!--ASSET 11808 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; cursor:pointer; position:absolute; top:265px; left:270px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11808'
                        onclick='fetchAssetData(11808);' class="asset-image" data-id="<?php echo $assetId11808; ?>"
                        data-room="<?php echo htmlspecialchars($room11808); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11808); ?>"
                        data-image="<?php echo base64_encode($upload_img11808); ?>"
                        data-status="<?php echo htmlspecialchars($status11808); ?>"
                        data-category="<?php echo htmlspecialchars($category11808); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11808); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11808); ?>; 
    position:absolute; top:260px; left:280px;'>
                    </div>

                    <!--ASSET 11809 MUST CHANGE-->
                    <img src='../image.php?id=11811'
                        style='width:20px; cursor:pointer; position:absolute; top:300px; left:270px; transform: rotate(-180deg);'' alt='
                        Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal11809'
                        onclick='fetchAssetData(11809);' class="asset-image" data-id="<?php echo $assetId11809; ?>"
                        data-room="<?php echo htmlspecialchars($room11809); ?>"
                        data-floor="<?php echo htmlspecialchars($floor11809); ?>"
                        data-image="<?php echo base64_encode($upload_img11809); ?>"
                        data-status="<?php echo htmlspecialchars($status11809); ?>"
                        data-category="<?php echo htmlspecialchars($category11809); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName11809); ?>">
                    <div style='width:7px; height:7px; border-radius:50%; background-color: <?php echo getStatusColor($status11809); ?>; 
    position:absolute; top:295px; left:280px;'>
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