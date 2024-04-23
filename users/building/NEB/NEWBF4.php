<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

session_start();
include_once ("../../../config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();
//need ata to sa lahat ng page para sa security hahah 


if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
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

     //ENCABS 1 DITO
 $assetIds = [16795, 16796];

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
                header("Location: NEWBF4.php");
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
                header("Location: NEWBF4.php");
            } else {
                echo "<script>alert('Failed to update asset and image. Error: " . $stmt->error . "');</script>";
            }
        }
    }
    //ENCABS DITO
    // Call updateAsset function for each asset ID you want to handle
    $assetIds = [16795 ,16796]; // Add more asset IDs here
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../../src/css/main.css" />
        <link rel="stylesheet" href="../../buildingCSS/NEB/NEWBF1.css" />
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>

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
                    <img class="Floor-container-1" src="../../../src/floors/newAcademicB/NAB4F.png" alt="">

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
                    <!-- DITO START ENCABS THEN WALA KANA GAGALAWIN SA TAAS NG PART NA CODE MORE ON ASSETS NALANG -->

                    <!-- ASSET 16795 -->
                    <img src='../image.php?id=7183'
                        style='width:15px; z-index:1; cursor:pointer; position:absolute; top:400px; left:612px;'
                        alt='Asset Image' data-bs-toggle='modal' data-bs-target='#imageModal16795'
                        onclick='fetchAssetData(16795);' class="asset-image" data-id="<?php echo $assetId16795; ?>"
                        data-room="<?php echo htmlspecialchars($room16795); ?>"
                        data-floor="<?php echo htmlspecialchars($floor16795); ?>"
                        data-image="<?php echo base64_encode($upload_img16795); ?>"
                        data-status="<?php echo htmlspecialchars($status16795); ?>"
                        data-category="<?php echo htmlspecialchars($category16795); ?>"
                        data-assignedname="<?php echo htmlspecialchars($assignedName16795); ?>">
                    <div style='width:7px; height:7px; z-index:2; border-radius:50%; background-color: <?php echo getStatusColor($status16795); ?>; 
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