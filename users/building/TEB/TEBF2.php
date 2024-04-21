<?php
session_start();
include_once("../../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 




// ******************************************BASAHIN NYO MUNA TO**********************************************


// ******************************************BASAHIN NYO MUNA TO**********************************************


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




    //FOR ID 910 LIGHTS
    
    $assetIds = range(9715, 9790);
    // $assetIds = [910, 911, 912, 913, 914, 915, 916, 917, 918, 9727]; // Add more asset IDs here

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
                header("Location: TEBF2.php");
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
    $assetIds = range(9715, 9790); // Add more asset IDs here
   
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
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
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
                        <img class="Floor-container-1" src="../../../src/floors/techvocB/TV2F.png" alt="">

                        <div class="legend-body" id="legendBody">
                            <!-- Your legend body content goes here -->
                            <div class="legend-item"><img src="../../../src/legend/BULB.jpg" alt="" class="legend-img">
                                <p>BULB</p>
                            </div>
                            <div class="legend-item"><img src="../../../src/legend/CASSETTE-AC.jpg" alt="" class="legend-img">
                                <p>CASSETTE-AC</p>
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
                        <!-- assetss -->

                        <!-- ASSET 9715 -->
                        <img src="../image.php?id=9715" class="asset-image" data-id="<?php echo $assetId9715; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:1060px;" alt="Asset Image 9715" data-bs-toggle="modal" data-bs-target="#imageModal9715" onclick="fetchAssetData(9715);" data-room="<?php echo htmlspecialchars($room9715); ?>" data-floor="<?php echo htmlspecialchars($floor9715); ?>" data-image="<?php echo base64_encode($upload_img9715); ?>" data-category="<?php echo htmlspecialchars($category9715); ?>" data-status="<?php echo htmlspecialchars($status9715); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9715); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9715); ?>; position:absolute; top:130px; left:1070px;'>
                        </div>

                          <!-- ASSET 9716 -->
  <img src="../image.php?id=9716" class="asset-image" data-id="<?php echo $assetId9716; ?>" style="width:15px; cursor:pointer; position:absolute; top:90px; left:1060px;" alt="Asset Image 9716" data-bs-toggle="modal" data-bs-target="#imageModal9716" onclick="fetchAssetData(9716);" data-room="<?php echo htmlspecialchars($room9716); ?>" data-floor="<?php echo htmlspecialchars($floor9716); ?>" data-image="<?php echo base64_encode($upload_img9716); ?>" data-category="<?php echo htmlspecialchars($category9716); ?>" data-status="<?php echo htmlspecialchars($status9716); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9716); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9716); ?>; position:absolute; top:90px; left:1070px;'>
                        </div>


                          <!-- ASSET 9717 -->
                          <img src="../image.php?id=9717" class="asset-image" data-id="<?php echo $assetId9717; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:1060px;" alt="Asset Image 9717" data-bs-toggle="modal" data-bs-target="#imageModal9717" onclick="fetchAssetData(9717);" data-room="<?php echo htmlspecialchars($room9717); ?>" data-floor="<?php echo htmlspecialchars($floor9717); ?>" data-image="<?php echo base64_encode($upload_img9717); ?>" data-category="<?php echo htmlspecialchars($category9717); ?>" data-status="<?php echo htmlspecialchars($status9717); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9717); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9717); ?>; position:absolute; top:45px; left:1070px;'>
                        </div>

                          <!-- ASSET 9718 -->
                          <img src="../image.php?id=9718" class="asset-image" data-id="<?php echo $assetId9718; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:970px;" alt="Asset Image 9718" data-bs-toggle="modal" data-bs-target="#imageModal9718" onclick="fetchAssetData(9718);" data-room="<?php echo htmlspecialchars($room9718); ?>" data-floor="<?php echo htmlspecialchars($floor9718); ?>" data-image="<?php echo base64_encode($upload_img9718); ?>" data-category="<?php echo htmlspecialchars($category9718); ?>" data-status="<?php echo htmlspecialchars($status9718); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9718); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9718); ?>; position:absolute; top:130px; left:980px;'>
                        </div>



                          <!-- ASSET 9719 -->
                          <img src="../image.php?id=9719" class="asset-image" data-id="<?php echo $assetId9719; ?>" style="width:15px; cursor:pointer; position:absolute; top:90px; left:970px;" alt="Asset Image 9719" data-bs-toggle="modal" data-bs-target="#imageModal9719" onclick="fetchAssetData(9719);" data-room="<?php echo htmlspecialchars($room9719); ?>" data-floor="<?php echo htmlspecialchars($floor9719); ?>" data-image="<?php echo base64_encode($upload_img9719); ?>" data-category="<?php echo htmlspecialchars($category9719); ?>" data-status="<?php echo htmlspecialchars($status9719); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9719); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9719); ?>; position:absolute; top:90px; left:980px;'>
                        </div>


                          <!-- ASSET 9720 -->
                          <img src="../image.php?id=9720" class="asset-image" data-id="<?php echo $assetId9720; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:970px;" alt="Asset Image 9720" data-bs-toggle="modal" data-bs-target="#imageModal9720" onclick="fetchAssetData(9720);" data-room="<?php echo htmlspecialchars($room9720); ?>" data-floor="<?php echo htmlspecialchars($floor9720); ?>" data-image="<?php echo base64_encode($upload_img9720); ?>" data-category="<?php echo htmlspecialchars($category9720); ?>" data-status="<?php echo htmlspecialchars($status9720); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9720); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9720); ?>; position:absolute; top:45px; left:980px;'>
                        </div>


                          <!-- ASSET 9721 -->
                          <img src="../image.php?id=9721" class="asset-image" data-id="<?php echo $assetId9721; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:920px;" alt="Asset Image 9721" data-bs-toggle="modal" data-bs-target="#imageModal9721" onclick="fetchAssetData(9721);" data-room="<?php echo htmlspecialchars($room9721); ?>" data-floor="<?php echo htmlspecialchars($floor9721); ?>" data-image="<?php echo base64_encode($upload_img9721); ?>" data-category="<?php echo htmlspecialchars($category9721); ?>" data-status="<?php echo htmlspecialchars($status9721); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9721); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9721); ?>; position:absolute; top:45px; left:930px;'>
                        </div>

                          <!-- ASSET 9722 -->
                          <img src="../image.php?id=9722" class="asset-image" data-id="<?php echo $assetId9722; ?>" style="width:15px; cursor:pointer; position:absolute; top:90px; left:920px;" alt="Asset Image 9722" data-bs-toggle="modal" data-bs-target="#imageModal9722" onclick="fetchAssetData(9722);" data-room="<?php echo htmlspecialchars($room9722); ?>" data-floor="<?php echo htmlspecialchars($floor9722); ?>" data-image="<?php echo base64_encode($upload_img9722); ?>" data-category="<?php echo htmlspecialchars($category9722); ?>" data-status="<?php echo htmlspecialchars($status9722); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9722); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9722); ?>; position:absolute; top:90px; left:930px;'>
                        </div>


                          <!-- ASSET 9723 -->
                          <img src="../image.php?id=9723" class="asset-image" data-id="<?php echo $assetId9723; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:920px;" alt="Asset Image 9723" data-bs-toggle="modal" data-bs-target="#imageModal9723" onclick="fetchAssetData(9723);" data-room="<?php echo htmlspecialchars($room9723); ?>" data-floor="<?php echo htmlspecialchars($floor9723); ?>" data-image="<?php echo base64_encode($upload_img9723); ?>" data-category="<?php echo htmlspecialchars($category9723); ?>" data-status="<?php echo htmlspecialchars($status9723); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9723); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9723); ?>; position:absolute; top:130px; left:930px;'>
                        </div>



                          <!-- ASSET 9724 -->
                          <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9724; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:835px;" alt="Asset Image 9724" data-bs-toggle="modal" data-bs-target="#imageModal9724" onclick="fetchAssetData(9724);" data-room="<?php echo htmlspecialchars($room9724); ?>" data-floor="<?php echo htmlspecialchars($floor9724); ?>" data-image="<?php echo base64_encode($upload_img9724); ?>" data-category="<?php echo htmlspecialchars($category9724); ?>" data-status="<?php echo htmlspecialchars($status9724); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9724); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9724); ?>; position:absolute; top:45px; left:845px;'>
                        </div>


                          <!-- ASSET 9725 -->
                          <img src="../image.php?id=9725" class="asset-image" data-id="<?php echo $assetId9725; ?>" style="width:15px; cursor:pointer; position:absolute; top:90px; left:835px;" alt="Asset Image 9725" data-bs-toggle="modal" data-bs-target="#imageModal9725" onclick="fetchAssetData(9725);" data-room="<?php echo htmlspecialchars($room9725); ?>" data-floor="<?php echo htmlspecialchars($floor9725); ?>" data-image="<?php echo base64_encode($upload_img9725); ?>" data-category="<?php echo htmlspecialchars($category9725); ?>" data-status="<?php echo htmlspecialchars($status9725); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9725); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9725); ?>; position:absolute; top:90px; left:845px;'>
                        </div>


                          <!-- ASSET 9726 light -->
                          <img src="../image.php?id=9726" class="asset-image" data-id="<?php echo $assetId9726; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:835px;" alt="Asset Image 9726" data-bs-toggle="modal" data-bs-target="#imageModal9726" onclick="fetchAssetData(9726);" data-room="<?php echo htmlspecialchars($room9726); ?>" data-floor="<?php echo htmlspecialchars($floor9726); ?>" data-image="<?php echo base64_encode($upload_img9726); ?>" data-category="<?php echo htmlspecialchars($category9726); ?>" data-status="<?php echo htmlspecialchars($status9726); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9726); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9726); ?>; position:absolute; top:130px; left:845px;'>
                        </div>






                        <!-- ASSET 9727 ac-->
                        <img src="../image.php?id=9727" class="asset-image" data-id="<?php echo $assetId9727; ?>" style="width:30px; cursor:pointer; position:absolute; top:80px; left:990px;" alt="Asset Image 9727" data-bs-toggle="modal" data-bs-target="#imageModal9727" onclick="fetchAssetData(9727);" data-room="<?php echo htmlspecialchars($room9727); ?>" data-floor="<?php echo htmlspecialchars($floor9727); ?>" data-image="<?php echo base64_encode($upload_img9727); ?>" data-category="<?php echo htmlspecialchars($category9727); ?>" data-status="<?php echo htmlspecialchars($status9727); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9727); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9727); ?>; position:absolute; top:80px; left:1012px;'>
                        </div>

                        <!-- ASSET 9728 ac-->
                        <img src="../image.php?id=9728" class="asset-image" data-id="<?php echo $assetId9728; ?>" style="width:30px; cursor:pointer; position:absolute; top:80px; left:890px;" alt="Asset Image 9728" data-bs-toggle="modal" data-bs-target="#imageModal9728" onclick="fetchAssetData(9728);" data-room="<?php echo htmlspecialchars($room9728); ?>" data-floor="<?php echo htmlspecialchars($floor9728); ?>" data-image="<?php echo base64_encode($upload_img9728); ?>" data-category="<?php echo htmlspecialchars($category9728); ?>" data-status="<?php echo htmlspecialchars($status9728); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9728); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9728); ?>; position:absolute; top:80px; left:912px;'>
                        </div>


<!-- ASSET 9729 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9729; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:685px;" alt="Asset Image 9729" data-bs-toggle="modal" data-bs-target="#imageModal9729" onclick="fetchAssetData(9729);" data-room="<?php echo htmlspecialchars($room9729); ?>" data-floor="<?php echo htmlspecialchars($floor9729); ?>" data-image="<?php echo base64_encode($upload_img9729); ?>" data-category="<?php echo htmlspecialchars($category9729); ?>" data-status="<?php echo htmlspecialchars($status9729); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9729); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9729); ?>; position:absolute; top:45px; left:695px;'>
</div>

<!-- ASSET 9730 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9730; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:585px;" alt="Asset Image 9730" data-bs-toggle="modal" data-bs-target="#imageModal9730" onclick="fetchAssetData(9730);" data-room="<?php echo htmlspecialchars($room9730); ?>" data-floor="<?php echo htmlspecialchars($floor9730); ?>" data-image="<?php echo base64_encode($upload_img9730); ?>" data-category="<?php echo htmlspecialchars($category9730); ?>" data-status="<?php echo htmlspecialchars($status9730); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9730); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9730); ?>; position:absolute; top:45px; left:595px;'>
</div>



                    <!-- ASSET 9731 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9731; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:630px;" alt="Asset Image 9731" data-bs-toggle="modal" data-bs-target="#imageModal9731" onclick="fetchAssetData(9731);" data-room="<?php echo htmlspecialchars($room9731); ?>" data-floor="<?php echo htmlspecialchars($floor9731); ?>" data-image="<?php echo base64_encode($upload_img9731); ?>" data-category="<?php echo htmlspecialchars($category9731); ?>" data-status="<?php echo htmlspecialchars($status9731); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9731); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9731); ?>; position:absolute; top:45px; left:640px;'>
</div>


<!-- ASSET 9732 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9732; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:725px;" alt="Asset Image 9732" data-bs-toggle="modal" data-bs-target="#imageModal9732" onclick="fetchAssetData(9732);" data-room="<?php echo htmlspecialchars($room9732); ?>" data-floor="<?php echo htmlspecialchars($floor9732); ?>" data-image="<?php echo base64_encode($upload_img9732); ?>" data-category="<?php echo htmlspecialchars($category9732); ?>" data-status="<?php echo htmlspecialchars($status9732); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9732); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9732); ?>; position:absolute; top:45px; left:735px;'>
</div>

<!-- ASSET 9733 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9733; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:795px;" alt="Asset Image 9733" data-bs-toggle="modal" data-bs-target="#imageModal9733" onclick="fetchAssetData(9733);" data-room="<?php echo htmlspecialchars($room9733); ?>" data-floor="<?php echo htmlspecialchars($floor9733); ?>" data-image="<?php echo base64_encode($upload_img9733); ?>" data-category="<?php echo htmlspecialchars($category9733); ?>" data-status="<?php echo htmlspecialchars($status9733); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9733); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9733); ?>; position:absolute; top:45px; left:805px;'>
</div>






<!-- ASSET 9734 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9734; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:585px;" alt="Asset Image 9734" data-bs-toggle="modal" data-bs-target="#imageModal9734" onclick="fetchAssetData(9734);" data-room="<?php echo htmlspecialchars($room9734); ?>" data-floor="<?php echo htmlspecialchars($floor9734); ?>" data-image="<?php echo base64_encode($upload_img9734); ?>" data-category="<?php echo htmlspecialchars($category9734); ?>" data-status="<?php echo htmlspecialchars($status9734); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9734); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9734); ?>; position:absolute; top:76px; left:595px;'>
</div>


<!-- ASSET 9735 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9735; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:685px;" alt="Asset Image 9735" data-bs-toggle="modal" data-bs-target="#imageModal9735" onclick="fetchAssetData(9735);" data-room="<?php echo htmlspecialchars($room9735); ?>" data-floor="<?php echo htmlspecialchars($floor9735); ?>" data-image="<?php echo base64_encode($upload_img9735); ?>" data-category="<?php echo htmlspecialchars($category9735); ?>" data-status="<?php echo htmlspecialchars($status9735); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9735); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9735); ?>; position:absolute; top:76px; left:695px;'>
</div>



<!-- ASSET 9736 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9736; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:630px;" alt="Asset Image 9736" data-bs-toggle="modal" data-bs-target="#imageModal9736" onclick="fetchAssetData(9736);" data-room="<?php echo htmlspecialchars($room9736); ?>" data-floor="<?php echo htmlspecialchars($floor9736); ?>" data-image="<?php echo base64_encode($upload_img9736); ?>" data-category="<?php echo htmlspecialchars($category9736); ?>" data-status="<?php echo htmlspecialchars($status9736); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9736); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9736); ?>; position:absolute; top:76px; left:640px;'>
</div>

<!-- ASSET 9737 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9737; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:725px;" alt="Asset Image 9737" data-bs-toggle="modal" data-bs-target="#imageModal9737" onclick="fetchAssetData(9737);" data-room="<?php echo htmlspecialchars($room9737); ?>" data-floor="<?php echo htmlspecialchars($floor9737); ?>" data-image="<?php echo base64_encode($upload_img9737); ?>" data-category="<?php echo htmlspecialchars($category9737); ?>" data-status="<?php echo htmlspecialchars($status9737); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9737); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9737); ?>; position:absolute; top:76px; left:735px;'>
</div>

<!-- ASSET 9738 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9738; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:795px;" alt="Asset Image 9738" data-bs-toggle="modal" data-bs-target="#imageModal9738" onclick="fetchAssetData(9738);" data-room="<?php echo htmlspecialchars($room9738); ?>" data-floor="<?php echo htmlspecialchars($floor9738); ?>" data-image="<?php echo base64_encode($upload_img9738); ?>" data-category="<?php echo htmlspecialchars($category9738); ?>" data-status="<?php echo htmlspecialchars($status9738); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9738); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9738); ?>; position:absolute; top:76px; left:805px;'>
</div>







<!-- ASSET 9739 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9739; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:630px;" alt="Asset Image 9739" data-bs-toggle="modal" data-bs-target="#imageModal9739" onclick="fetchAssetData(9739);" data-room="<?php echo htmlspecialchars($room9739); ?>" data-floor="<?php echo htmlspecialchars($floor9739); ?>" data-image="<?php echo base64_encode($upload_img9739); ?>" data-category="<?php echo htmlspecialchars($category9739); ?>" data-status="<?php echo htmlspecialchars($status9739); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9739); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9739); ?>; position:absolute; top:130px; left:640px;'>
</div>

<!-- ASSET 9740 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9740; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:685px;" alt="Asset Image 9740" data-bs-toggle="modal" data-bs-target="#imageModal9740" onclick="fetchAssetData(9740);" data-room="<?php echo htmlspecialchars($room9740); ?>" data-floor="<?php echo htmlspecialchars($floor9740); ?>" data-image="<?php echo base64_encode($upload_img9740); ?>" data-category="<?php echo htmlspecialchars($category9740); ?>" data-status="<?php echo htmlspecialchars($status9740); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9740); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9740); ?>; position:absolute; top:130px; left:695px;'>
</div>


<!-- ASSET 9741 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9741; ?>" style="width:15px; cursor:pointer; position:absolute; top:130px; left:725px;" alt="Asset Image 9741" data-bs-toggle="modal" data-bs-target="#imageModal9741" onclick="fetchAssetData(9741);" data-room="<?php echo htmlspecialchars($room9741); ?>" data-floor="<?php echo htmlspecialchars($floor9741); ?>" data-image="<?php echo base64_encode($upload_img9741); ?>" data-category="<?php echo htmlspecialchars($category9741); ?>" data-status="<?php echo htmlspecialchars($status9741); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9741); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9741); ?>; position:absolute; top:130px; left:735px;'>
</div>

  <!-- ASSET 9742 ac-->
  <img src="../image.php?id=9742" class="asset-image" data-id="<?php echo $assetId9742; ?>" style="width:30px; cursor:pointer; position:absolute; top:67px; left:653px;" alt="Asset Image 9742" data-bs-toggle="modal" data-bs-target="#imageModal9742" onclick="fetchAssetData(9742);" data-room="<?php echo htmlspecialchars($room9742); ?>" data-floor="<?php echo htmlspecialchars($floor9742); ?>" data-image="<?php echo base64_encode($upload_img9742); ?>" data-category="<?php echo htmlspecialchars($category9742); ?>" data-status="<?php echo htmlspecialchars($status9742); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9742); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9742); ?>; position:absolute; top:67px; left:675px;'>
                        </div>

                        <!-- ASSET 9743 ac-->
                        <img src="../image.php?id=9743" class="asset-image" data-id="<?php echo $assetId9743; ?>" style="width:30px; cursor:pointer; position:absolute; top:67px; left:752px;" alt="Asset Image 9743" data-bs-toggle="modal" data-bs-target="#imageModal9743" onclick="fetchAssetData(9743);" data-room="<?php echo htmlspecialchars($room9743); ?>" data-floor="<?php echo htmlspecialchars($floor9743); ?>" data-image="<?php echo base64_encode($upload_img9743); ?>" data-category="<?php echo htmlspecialchars($category9743); ?>" data-status="<?php echo htmlspecialchars($status9743); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9743); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9743); ?>; position:absolute; top:67px; left:772px;'>
                        </div>


                        <!-- ASSET 9745 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9745; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:335px;" alt="Asset Image 9745" data-bs-toggle="modal" data-bs-target="#imageModal9745" onclick="fetchAssetData(9745);" data-room="<?php echo htmlspecialchars($room9745); ?>" data-floor="<?php echo htmlspecialchars($floor9745); ?>" data-image="<?php echo base64_encode($upload_img9745); ?>" data-category="<?php echo htmlspecialchars($category9745); ?>" data-status="<?php echo htmlspecialchars($status9745); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9745); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9745); ?>; position:absolute; top:45px; left:345px;'>
</div>



<!-- ASSET 9746 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9746; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:400px;" alt="Asset Image 9746" data-bs-toggle="modal" data-bs-target="#imageModal9746" onclick="fetchAssetData(9746);" data-room="<?php echo htmlspecialchars($room9746); ?>" data-floor="<?php echo htmlspecialchars($floor9746); ?>" data-image="<?php echo base64_encode($upload_img9746); ?>" data-category="<?php echo htmlspecialchars($category9746); ?>" data-status="<?php echo htmlspecialchars($status9746); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9746); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9746); ?>; position:absolute; top:45px; left:410px;'>
</div>

<!-- ASSET 9747 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9747; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:450px;" alt="Asset Image 9747" data-bs-toggle="modal" data-bs-target="#imageModal9747" onclick="fetchAssetData(9747);" data-room="<?php echo htmlspecialchars($room9747); ?>" data-floor="<?php echo htmlspecialchars($floor9747); ?>" data-image="<?php echo base64_encode($upload_img9747); ?>" data-category="<?php echo htmlspecialchars($category9747); ?>" data-status="<?php echo htmlspecialchars($status9747); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9747); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9747); ?>; position:absolute; top:45px; left:460px;'>
</div>

<!-- ASSET 9748 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9748; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:495px;" alt="Asset Image 9748" data-bs-toggle="modal" data-bs-target="#imageModal9748" onclick="fetchAssetData(9748);" data-room="<?php echo htmlspecialchars($room9748); ?>" data-floor="<?php echo htmlspecialchars($floor9748); ?>" data-image="<?php echo base64_encode($upload_img9748); ?>" data-category="<?php echo htmlspecialchars($category9748); ?>" data-status="<?php echo htmlspecialchars($status9748); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9748); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9748); ?>; position:absolute; top:45px; left:505px;'>
</div>

<!-- ASSET 9749 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9749; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:551px;" alt="Asset Image 9749" data-bs-toggle="modal" data-bs-target="#imageModal9749" onclick="fetchAssetData(9749);" data-room="<?php echo htmlspecialchars($room9749); ?>" data-floor="<?php echo htmlspecialchars($floor9749); ?>" data-image="<?php echo base64_encode($upload_img9749); ?>" data-category="<?php echo htmlspecialchars($category9749); ?>" data-status="<?php echo htmlspecialchars($status9749); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9749); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9749); ?>; position:absolute; top:45px; left:561px;'>
</div>





<!-- ASSET 9750 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9750; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:335px;" alt="Asset Image 9750" data-bs-toggle="modal" data-bs-target="#imageModal9750" onclick="fetchAssetData(9750);" data-room="<?php echo htmlspecialchars($room9750); ?>" data-floor="<?php echo htmlspecialchars($floor9750); ?>" data-image="<?php echo base64_encode($upload_img9750); ?>" data-category="<?php echo htmlspecialchars($category9750); ?>" data-status="<?php echo htmlspecialchars($status9750); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9750); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9750); ?>; position:absolute; top:76px; left:345px;'>
</div>


<!-- ASSET 9751 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9751; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:400px;" alt="Asset Image 9751" data-bs-toggle="modal" data-bs-target="#imageModal9751" onclick="fetchAssetData(9751);" data-room="<?php echo htmlspecialchars($room9751); ?>" data-floor="<?php echo htmlspecialchars($floor9751); ?>" data-image="<?php echo base64_encode($upload_img9751); ?>" data-category="<?php echo htmlspecialchars($category9751); ?>" data-status="<?php echo htmlspecialchars($status9751); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9751); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9751); ?>; position:absolute; top:76px; left:410px;'>
</div>

<!-- ASSET 9752 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9752; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:450px;" alt="Asset Image 9752" data-bs-toggle="modal" data-bs-target="#imageModal9752" onclick="fetchAssetData(9752);" data-room="<?php echo htmlspecialchars($room9752); ?>" data-floor="<?php echo htmlspecialchars($floor9752); ?>" data-image="<?php echo base64_encode($upload_img9752); ?>" data-category="<?php echo htmlspecialchars($category9752); ?>" data-status="<?php echo htmlspecialchars($status9752); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9752); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9752); ?>; position:absolute; top:76px; left:460px;'>
</div>



<!-- ASSET 9753 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9753; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:495px;" alt="Asset Image 9753" data-bs-toggle="modal" data-bs-target="#imageModal9753" onclick="fetchAssetData(9753);" data-room="<?php echo htmlspecialchars($room9753); ?>" data-floor="<?php echo htmlspecialchars($floor9753); ?>" data-image="<?php echo base64_encode($upload_img9753); ?>" data-category="<?php echo htmlspecialchars($category9753); ?>" data-status="<?php echo htmlspecialchars($status9753); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9753); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9753); ?>; position:absolute; top:76px; left:505px;'>
</div>

<!-- ASSET 9754 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9754; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:551px;" alt="Asset Image 9754" data-bs-toggle="modal" data-bs-target="#imageModal9754" onclick="fetchAssetData(9754);" data-room="<?php echo htmlspecialchars($room9754); ?>" data-floor="<?php echo htmlspecialchars($floor9754); ?>" data-image="<?php echo base64_encode($upload_img9754); ?>" data-category="<?php echo htmlspecialchars($category9754); ?>" data-status="<?php echo htmlspecialchars($status9754); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9754); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9754); ?>; position:absolute; top:76px; left:561px;'>
</div>




<!-- ASSET 9755 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9755; ?>" style="width:15px; cursor:pointer; position:absolute; top:109px; left:495px;" alt="Asset Image 9755" data-bs-toggle="modal" data-bs-target="#imageModal9755" onclick="fetchAssetData(9755);" data-room="<?php echo htmlspecialchars($room9755); ?>" data-floor="<?php echo htmlspecialchars($floor9755); ?>" data-image="<?php echo base64_encode($upload_img9755); ?>" data-category="<?php echo htmlspecialchars($category9755); ?>" data-status="<?php echo htmlspecialchars($status9755); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9755); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9755); ?>; position:absolute; top:109px; left:505px;'>
</div>



<!-- ASSET 9756 Sofa -->
<img src="../image.php?id=9756" class="asset-image" data-id="<?php echo $assetId9756; ?>" style="width:40px; cursor:pointer; position:absolute; top:70px; left:512px;" alt="Asset Image 9756" data-bs-toggle="modal" data-bs-target="#imageModal9756" onclick="fetchAssetData(9756);" data-room="<?php echo htmlspecialchars($room9756); ?>" data-floor="<?php echo htmlspecialchars($floor9756); ?>" data-image="<?php echo base64_encode($upload_img9756); ?>" data-category="<?php echo htmlspecialchars($category9756); ?>" data-status="<?php echo htmlspecialchars($status9756); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9756); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9756); ?>; position:absolute; top:69px; left:546px;'>
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
                        <div class="col-4" style="display:none">
                            <label for="date" class="form-label">Date:</label>
                            <input type="text" class="form-control" id="date" name="date"
                                value="<?php echo htmlspecialchars($date); ?>" readonly />
                        </div>
                        <!--Second Row-->
                        <div class="col-6">
                            <input type="text" class="form-control" id="room" name="room"
                                value="<?php echo htmlspecialchars($room); ?>" readonly />
                        </div>
                        <div class="col-6" style="display:none">
                            <input type="text" class="form-control  center-content" id="building"
                                name="building" value="<?php echo htmlspecialchars($building); ?>"
                                readonly />
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