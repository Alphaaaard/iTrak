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
    
    $assetIds = range(9715, 9932);
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
    $assetIds = range(9715, 9932); // Add more asset IDs here
   
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



<!-- ASSET 9758 chair -->
<img src="../image.php?id=9758" class="asset-image" data-id="<?php echo $assetId9758; ?>" style="width:10px; cursor:pointer; position:absolute; top:53px; left:434px; transform: rotate(180deg);" alt="Asset Image 9758" data-bs-toggle="modal" data-bs-target="#imageModal9758" onclick="fetchAssetData(9758);" data-room="<?php echo htmlspecialchars($room9758); ?>" data-floor="<?php echo htmlspecialchars($floor9758); ?>" data-image="<?php echo base64_encode($upload_img9758); ?>" data-category="<?php echo htmlspecialchars($category9758); ?>" data-status="<?php echo htmlspecialchars($status9758); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9758); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9758); ?>; position:absolute; top:53px; left:438px;'>
</div>
<!-- ASSET 9759 chair -->
<img src="../image.php?id=9759" class="asset-image" data-id="<?php echo $assetId9759; ?>" style="width:10px; cursor:pointer; position:absolute; top:67px; left:432px; transform: rotate(90deg)" alt="Asset Image 9759" data-bs-toggle="modal" data-bs-target="#imageModal9759" onclick="fetchAssetData(9759);" data-room="<?php echo htmlspecialchars($room9759); ?>" data-floor="<?php echo htmlspecialchars($floor9759); ?>" data-image="<?php echo base64_encode($upload_img9759); ?>" data-category="<?php echo htmlspecialchars($category9759); ?>" data-status="<?php echo htmlspecialchars($status9759); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9759); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9759); ?>; position:absolute; top:67px; left:436px;'>
</div>

<!-- ASSET 9760 chair -->
<img src="../image.php?id=9760" class="asset-image" data-id="<?php echo $assetId9760; ?>" style="width:10px; cursor:pointer; position:absolute; top:98px; left:378px;" alt="Asset Image 9760" data-bs-toggle="modal" data-bs-target="#imageModal9760" onclick="fetchAssetData(9760);" data-room="<?php echo htmlspecialchars($room9760); ?>" data-floor="<?php echo htmlspecialchars($floor9760); ?>" data-image="<?php echo base64_encode($upload_img9760); ?>" data-category="<?php echo htmlspecialchars($category9760); ?>" data-status="<?php echo htmlspecialchars($status9760); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9760); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9760); ?>; position:absolute; top:98px; left:375px;'>
</div>





<!-- ASSET 9761 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9761; ?>" style="width:17px; cursor:pointer; position:absolute; top:73px; left:425px; transform: rotate(90deg);" alt="Asset Image 9761" data-bs-toggle="modal" data-bs-target="#imageModal9761" onclick="fetchAssetData(9761);" data-room="<?php echo htmlspecialchars($room9761); ?>" data-floor="<?php echo htmlspecialchars($floor9761); ?>" data-image="<?php echo base64_encode($upload_img9761); ?>" data-category="<?php echo htmlspecialchars($category9761); ?>" data-status="<?php echo htmlspecialchars($status9761); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9761); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9761); ?>; position:absolute; top:73px; left:445px;'>
</div>

<!-- ASSET 9762 desk -->
<img src="../image.php?id=9762" class="asset-image" data-id="<?php echo $assetId9762; ?>" style="width:17px; cursor:pointer; position:absolute; top:42px; left:418px; transform: rotate(180deg);" alt="Asset Image 9762" data-bs-toggle="modal" data-bs-target="#imageModal9762" onclick="fetchAssetData(9762);" data-room="<?php echo htmlspecialchars($room9762); ?>" data-floor="<?php echo htmlspecialchars($floor9762); ?>" data-image="<?php echo base64_encode($upload_img9762); ?>" data-category="<?php echo htmlspecialchars($category9762); ?>" data-status="<?php echo htmlspecialchars($status9762); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9762); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9762); ?>; position:absolute; top:42px; left:432px;'>
</div>
<!-- ASSET 9763 desk -->
<img src="../image.php?id=9763" class="asset-image" data-id="<?php echo $assetId9763; ?>" style="width:17px; cursor:pointer; position:absolute; top:95px; left:387px; transform: rotate(360deg);" alt="Asset Image 9763" data-bs-toggle="modal" data-bs-target="#imageModal9763" onclick="fetchAssetData(9763);" data-room="<?php echo htmlspecialchars($room9763); ?>" data-floor="<?php echo htmlspecialchars($floor9763); ?>" data-image="<?php echo base64_encode($upload_img9763); ?>" data-category="<?php echo htmlspecialchars($category9763); ?>" data-status="<?php echo htmlspecialchars($status9763); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9763); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9763); ?>; position:absolute; top:95px; left:400px;'>
</div>


<!-- ASSET 9764 chair -->
<img src="../image.php?id=9764" class="asset-image" data-id="<?php echo $assetId9764; ?>" style="width:11px; cursor:pointer; position:absolute; top:48px; left:366px; transform: rotate(180deg);" alt="Asset Image 9764" data-bs-toggle="modal" data-bs-target="#imageModal9764" onclick="fetchAssetData(9764);" data-room="<?php echo htmlspecialchars($room9764); ?>" data-floor="<?php echo htmlspecialchars($floor9764); ?>" data-image="<?php echo base64_encode($upload_img9764); ?>" data-category="<?php echo htmlspecialchars($category9764); ?>" data-status="<?php echo htmlspecialchars($status9764); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9764); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9764); ?>; position:absolute; top:43px; left:372px;'>
</div>

<!-- ASSET 9765 chair -->
<img src="../image.php?id=9765" class="asset-image" data-id="<?php echo $assetId9765; ?>" style="width:11px; cursor:pointer; position:absolute; top:48px; left:380px; transform: rotate(180deg);" alt="Asset Image 9765" data-bs-toggle="modal" data-bs-target="#imageModal9765" onclick="fetchAssetData(9765);" data-room="<?php echo htmlspecialchars($room9765); ?>" data-floor="<?php echo htmlspecialchars($floor9765); ?>" data-image="<?php echo base64_encode($upload_img9765); ?>" data-category="<?php echo htmlspecialchars($category9765); ?>" data-status="<?php echo htmlspecialchars($status9765); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9765); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9765); ?>; position:absolute; top:43px; left:387px;'>
</div>
<!-- ASSET 9766 chair -->
<img src="../image.php?id=9766" class="asset-image" data-id="<?php echo $assetId9766; ?>" style="width:11px; cursor:pointer; position:absolute; top:76px; left:366px;" alt="Asset Image 9766" data-bs-toggle="modal" data-bs-target="#imageModal9766" onclick="fetchAssetData(9766);" data-room="<?php echo htmlspecialchars($room9766); ?>" data-floor="<?php echo htmlspecialchars($floor9766); ?>" data-image="<?php echo base64_encode($upload_img9766); ?>" data-category="<?php echo htmlspecialchars($category9766); ?>" data-status="<?php echo htmlspecialchars($status9766); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9766); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9766); ?>; position:absolute; top:79px; left:372px;'>
</div>
<!-- ASSET 9767 chair -->
<img src="../image.php?id=9767" class="asset-image" data-id="<?php echo $assetId9767; ?>" style="width:11px; cursor:pointer; position:absolute; top:76px; left:380px;" alt="Asset Image 9767" data-bs-toggle="modal" data-bs-target="#imageModal9767" onclick="fetchAssetData(9767);" data-room="<?php echo htmlspecialchars($room9767); ?>" data-floor="<?php echo htmlspecialchars($floor9767); ?>" data-image="<?php echo base64_encode($upload_img9767); ?>" data-category="<?php echo htmlspecialchars($category9767); ?>" data-status="<?php echo htmlspecialchars($status9767); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9767); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9767); ?>; position:absolute; top:79px; left:387px;'>
</div>



<!-- ASSET 9768 table -->
<img src="../image.php?id=9768" class="asset-image" data-id="<?php echo $assetId9768; ?>" style="width:34px; cursor:pointer; position:absolute; top:55px; left:361px;" alt="Asset Image 9768" data-bs-toggle="modal" data-bs-target="#imageModal9768" onclick="fetchAssetData(9768);" data-room="<?php echo htmlspecialchars($room9768); ?>" data-floor="<?php echo htmlspecialchars($floor9768); ?>" data-image="<?php echo base64_encode($upload_img9768); ?>" data-category="<?php echo htmlspecialchars($category9768); ?>" data-status="<?php echo htmlspecialchars($status9768); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9768); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9768); ?>; position:absolute; top:61px; left:392px;'>
</div>





<!-- ASSET 9770 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9770; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:309px;" alt="Asset Image 9770" data-bs-toggle="modal" data-bs-target="#imageModal9770" onclick="fetchAssetData(9770);" data-room="<?php echo htmlspecialchars($room9770); ?>" data-floor="<?php echo htmlspecialchars($floor9770); ?>" data-image="<?php echo base64_encode($upload_img9770); ?>" data-category="<?php echo htmlspecialchars($category9770); ?>" data-status="<?php echo htmlspecialchars($status9770); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9770); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9770); ?>; position:absolute; top:76px; left:319px;'>
</div>


<!-- ASSET 9771 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9771; ?>" style="width:15px; cursor:pointer; position:absolute; top:76px; left:208px;" alt="Asset Image 9771" data-bs-toggle="modal" data-bs-target="#imageModal9771" onclick="fetchAssetData(9771);" data-room="<?php echo htmlspecialchars($room9771); ?>" data-floor="<?php echo htmlspecialchars($floor9771); ?>" data-image="<?php echo base64_encode($upload_img9771); ?>" data-category="<?php echo htmlspecialchars($category9771); ?>" data-status="<?php echo htmlspecialchars($status9771); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9771); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9771); ?>; position:absolute; top:76px; left:218px;'>
</div>


<!-- ASSET 9772 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9772; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:309px;" alt="Asset Image 9772" data-bs-toggle="modal" data-bs-target="#imageModal9772" onclick="fetchAssetData(9772);" data-room="<?php echo htmlspecialchars($room9772); ?>" data-floor="<?php echo htmlspecialchars($floor9772); ?>" data-image="<?php echo base64_encode($upload_img9772); ?>" data-category="<?php echo htmlspecialchars($category9772); ?>" data-status="<?php echo htmlspecialchars($status9772); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9772); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9772); ?>; position:absolute; top:45px; left:319px;'>
</div>

<!-- ASSET 9773 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9773; ?>" style="width:15px; cursor:pointer; position:absolute; top:45px; left:208px;" alt="Asset Image 9773" data-bs-toggle="modal" data-bs-target="#imageModal9773" onclick="fetchAssetData(9773);" data-room="<?php echo htmlspecialchars($room9773); ?>" data-floor="<?php echo htmlspecialchars($floor9773); ?>" data-image="<?php echo base64_encode($upload_img9773); ?>" data-category="<?php echo htmlspecialchars($category9773); ?>" data-status="<?php echo htmlspecialchars($status9773); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9773); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9773); ?>; position:absolute; top:45px; left:218px;'>
</div>



<!-- ASSET 9774 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9774; ?>" style="width:17px; cursor:pointer; position:absolute; top:50px; left:240px; transform: rotate(90deg);" alt="Asset Image 9774" data-bs-toggle="modal" data-bs-target="#imageModal9774" onclick="fetchAssetData(9774);" data-room="<?php echo htmlspecialchars($room9774); ?>" data-floor="<?php echo htmlspecialchars($floor9774); ?>" data-image="<?php echo base64_encode($upload_img9774); ?>" data-category="<?php echo htmlspecialchars($category9774); ?>" data-status="<?php echo htmlspecialchars($status9774); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9774); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9774); ?>; position:absolute; top:52px; left:259px;'>
</div>


<!-- ASSET 9775 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9775; ?>" style="width:17px; cursor:pointer; position:absolute; top:50px; left:274px; transform: rotate(90deg);" alt="Asset Image 9775" data-bs-toggle="modal" data-bs-target="#imageModal9775" onclick="fetchAssetData(9775);" data-room="<?php echo htmlspecialchars($room9775); ?>" data-floor="<?php echo htmlspecialchars($floor9775); ?>" data-image="<?php echo base64_encode($upload_img9775); ?>" data-category="<?php echo htmlspecialchars($category9775); ?>" data-status="<?php echo htmlspecialchars($status9775); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9775); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9775); ?>; position:absolute; top:52px; left:293px;'>
</div>



<!-- ASSET 9776 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9776; ?>" style="width:11px; cursor:pointer; position:absolute; top:48px; left:246px;" alt="Asset Image 9776" data-bs-toggle="modal" data-bs-target="#imageModal9776" onclick="fetchAssetData(9776);" data-room="<?php echo htmlspecialchars($room9776); ?>" data-floor="<?php echo htmlspecialchars($floor9776); ?>" data-image="<?php echo base64_encode($upload_img9776); ?>" data-category="<?php echo htmlspecialchars($category9776); ?>" data-status="<?php echo htmlspecialchars($status9776); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9776); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9776); ?>; position:absolute; top:42px; left:252px;'>
</div>

<!-- ASSET 9777 chair -->
<img src="../image.php?id=9777" class="asset-image" data-id="<?php echo $assetId9777; ?>" style="width:11px; cursor:pointer; position:absolute; top:48px; left:280px;" alt="Asset Image 9777" data-bs-toggle="modal" data-bs-target="#imageModal9777" onclick="fetchAssetData(9777);" data-room="<?php echo htmlspecialchars($room9777); ?>" data-floor="<?php echo htmlspecialchars($floor9777); ?>" data-image="<?php echo base64_encode($upload_img9777); ?>" data-category="<?php echo htmlspecialchars($category9777); ?>" data-status="<?php echo htmlspecialchars($status9777); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9777); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9777); ?>; position:absolute; top:42px; left:287px;'>
</div>






  <!-- ASSET 9779 ac-->
  <img src="../image.php?id=9779" class="asset-image" data-id="<?php echo $assetId9779; ?>" style="width:30px; cursor:pointer; position:absolute; top:131px; left:199px;" alt="Asset Image 9779" data-bs-toggle="modal" data-bs-target="#imageModal9779" onclick="fetchAssetData(9779);" data-room="<?php echo htmlspecialchars($room9779); ?>" data-floor="<?php echo htmlspecialchars($floor9779); ?>" data-image="<?php echo base64_encode($upload_img9779); ?>" data-category="<?php echo htmlspecialchars($category9779); ?>" data-status="<?php echo htmlspecialchars($status9779); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9779); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9779); ?>; position:absolute; top:131px; left:221px;'>
                        </div>



                          <!-- ASSET 9780 ac-->
                          <img src="../image.php?id=9780" class="asset-image" data-id="<?php echo $assetId9780; ?>" style="width:30px; cursor:pointer; position:absolute; top:131px; left:256px;" alt="Asset Image 9780" data-bs-toggle="modal" data-bs-target="#imageModal9780" onclick="fetchAssetData(9780);" data-room="<?php echo htmlspecialchars($room9780); ?>" data-floor="<?php echo htmlspecialchars($floor9780); ?>" data-image="<?php echo base64_encode($upload_img9780); ?>" data-category="<?php echo htmlspecialchars($category9780); ?>" data-status="<?php echo htmlspecialchars($status9780); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9780); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9780); ?>; position:absolute; top:131px; left:278px;'>
                        </div>

<!-- ASSET 9781 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9781; ?>" style="width:15px; cursor:pointer; position:absolute; top:115px; left:168px;" alt="Asset Image 9781" data-bs-toggle="modal" data-bs-target="#imageModal9781" onclick="fetchAssetData(9781);" data-room="<?php echo htmlspecialchars($room9781); ?>" data-floor="<?php echo htmlspecialchars($floor9781); ?>" data-image="<?php echo base64_encode($upload_img9781); ?>" data-category="<?php echo htmlspecialchars($category9781); ?>" data-status="<?php echo htmlspecialchars($status9781); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9781); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9781); ?>; position:absolute; top:115px; left:178px;'>
</div>

<!-- ASSET 9782 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9782; ?>" style="width:15px; cursor:pointer; position:absolute; top:169px; left:168px;" alt="Asset Image 9782" data-bs-toggle="modal" data-bs-target="#imageModal9782" onclick="fetchAssetData(9782);" data-room="<?php echo htmlspecialchars($room9782); ?>" data-floor="<?php echo htmlspecialchars($floor9782); ?>" data-image="<?php echo base64_encode($upload_img9782); ?>" data-category="<?php echo htmlspecialchars($category9782); ?>" data-status="<?php echo htmlspecialchars($status9782); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9782); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9782); ?>; position:absolute; top:169px; left:178px;'>
</div>


<!-- ASSET 9783 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9783; ?>" style="width:15px; cursor:pointer; position:absolute; top:115px; left:238px;" alt="Asset Image 9783" data-bs-toggle="modal" data-bs-target="#imageModal9783" onclick="fetchAssetData(9783);" data-room="<?php echo htmlspecialchars($room9783); ?>" data-floor="<?php echo htmlspecialchars($floor9783); ?>" data-image="<?php echo base64_encode($upload_img9783); ?>" data-category="<?php echo htmlspecialchars($category9783); ?>" data-status="<?php echo htmlspecialchars($status9783); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9783); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9783); ?>; position:absolute; top:115px; left:248px;'>
</div>

<!-- ASSET 9784 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9784; ?>" style="width:15px; cursor:pointer; position:absolute; top:169px; left:238px;" alt="Asset Image 9784" data-bs-toggle="modal" data-bs-target="#imageModal9784" onclick="fetchAssetData(9784);" data-room="<?php echo htmlspecialchars($room9784); ?>" data-floor="<?php echo htmlspecialchars($floor9784); ?>" data-image="<?php echo base64_encode($upload_img9784); ?>" data-category="<?php echo htmlspecialchars($category9784); ?>" data-status="<?php echo htmlspecialchars($status9784); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9784); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9784); ?>; position:absolute; top:169px; left:248px;'>
</div>



<!-- ASSET 9785 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9785; ?>" style="width:15px; cursor:pointer; position:absolute; top:115px; left:310px;" alt="Asset Image 9785" data-bs-toggle="modal" data-bs-target="#imageModal9785" onclick="fetchAssetData(9785);" data-room="<?php echo htmlspecialchars($room9785); ?>" data-floor="<?php echo htmlspecialchars($floor9785); ?>" data-image="<?php echo base64_encode($upload_img9785); ?>" data-category="<?php echo htmlspecialchars($category9785); ?>" data-status="<?php echo htmlspecialchars($status9785); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9785); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9785); ?>; position:absolute; top:115px; left:320px;'>
</div>

<!-- ASSET 9786 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9786; ?>" style="width:15px; cursor:pointer; position:absolute; top:169px; left:310px;" alt="Asset Image 9786" data-bs-toggle="modal" data-bs-target="#imageModal9786" onclick="fetchAssetData(9786);" data-room="<?php echo htmlspecialchars($room9786); ?>" data-floor="<?php echo htmlspecialchars($floor9786); ?>" data-image="<?php echo base64_encode($upload_img9786); ?>" data-category="<?php echo htmlspecialchars($category9786); ?>" data-status="<?php echo htmlspecialchars($status9786); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9786); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9786); ?>; position:absolute; top:169px; left:320px;'>
</div>


<!-- ASSET 9787 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9787; ?>" style="width:15px; cursor:pointer; position:absolute; top:141px; left:238px;" alt="Asset Image 9787" data-bs-toggle="modal" data-bs-target="#imageModal9787" onclick="fetchAssetData(9787);" data-room="<?php echo htmlspecialchars($room9787); ?>" data-floor="<?php echo htmlspecialchars($floor9787); ?>" data-image="<?php echo base64_encode($upload_img9787); ?>" data-category="<?php echo htmlspecialchars($category9787); ?>" data-status="<?php echo htmlspecialchars($status9787); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9787); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9787); ?>; position:absolute; top:141px; left:248px;'>
</div>

<!-- ASSET 9790 Sofa -->
<img src="../image.php?id=9790" class="asset-image" data-id="<?php echo $assetId9790; ?>" style="width:40px; cursor:pointer; position:absolute; top:165px; left:190px;" alt="Asset Image 9790" data-bs-toggle="modal" data-bs-target="#imageModal9790" onclick="fetchAssetData(9790);" data-room="<?php echo htmlspecialchars($room9790); ?>" data-floor="<?php echo htmlspecialchars($floor9790); ?>" data-image="<?php echo base64_encode($upload_img9790); ?>" data-category="<?php echo htmlspecialchars($category9790); ?>" data-status="<?php echo htmlspecialchars($status9790); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9790); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9790); ?>; position:absolute; top:161px; left:224px;'>
</div>




<!-- ASSET 9791 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9791; ?>" style="width:12px; cursor:pointer; position:absolute; top:119px; left:189px; transform: rotate(90deg);" alt="Asset Image 9791" data-bs-toggle="modal" data-bs-target="#imageModal9791" onclick="fetchAssetData(9791);" data-room="<?php echo htmlspecialchars($room9791); ?>" data-floor="<?php echo htmlspecialchars($floor9791); ?>" data-image="<?php echo base64_encode($upload_img9791); ?>" data-category="<?php echo htmlspecialchars($category9791); ?>" data-status="<?php echo htmlspecialchars($status9791); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9791); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status9791); ?>; position:absolute; top:119px; left:203px;'>
</div>


<!-- ASSET 9792 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9792; ?>" style="width:12px; cursor:pointer; position:absolute; top:119px; left:290px; transform: rotate(90deg);" alt="Asset Image 9792" data-bs-toggle="modal" data-bs-target="#imageModal9792" onclick="fetchAssetData(9792);" data-room="<?php echo htmlspecialchars($room9792); ?>" data-floor="<?php echo htmlspecialchars($floor9792); ?>" data-image="<?php echo base64_encode($upload_img9792); ?>" data-category="<?php echo htmlspecialchars($category9792); ?>" data-status="<?php echo htmlspecialchars($status9792); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9792); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status9792); ?>; position:absolute; top:119px; left:302px;'>
</div>

<!-- ASSET 9793 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9793; ?>" style="width:12px; cursor:pointer; position:absolute; top:153px; left:298px; transform: rotate(270deg);" alt="Asset Image 9793" data-bs-toggle="modal" data-bs-target="#imageModal9793" onclick="fetchAssetData(9793);" data-room="<?php echo htmlspecialchars($room9793); ?>" data-floor="<?php echo htmlspecialchars($floor9793); ?>" data-image="<?php echo base64_encode($upload_img9793); ?>" data-category="<?php echo htmlspecialchars($category9793); ?>" data-status="<?php echo htmlspecialchars($status9793); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9793); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status9793); ?>; position:absolute; top:153px; left:310px;'>
</div>



<!-- ASSET 9794 chair -->
<img src="../image.php?id=9794" class="asset-image" data-id="<?php echo $assetId9794; ?>" style="width:11px; cursor:pointer; position:absolute; top:114px; left:289px;" alt="Asset Image 9794" data-bs-toggle="modal" data-bs-target="#imageModal9794" onclick="fetchAssetData(9794);" data-room="<?php echo htmlspecialchars($room9794); ?>" data-floor="<?php echo htmlspecialchars($floor9794); ?>" data-image="<?php echo base64_encode($upload_img9794); ?>" data-category="<?php echo htmlspecialchars($category9794); ?>" data-status="<?php echo htmlspecialchars($status9794); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9794); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status9794); ?>; position:absolute; top:111px; left:297px;'>
</div>

<!-- ASSET 9795 chair -->
<img src="../image.php?id=9795" class="asset-image" data-id="<?php echo $assetId9795; ?>" style="width:11px; cursor:pointer; position:absolute; top:114px; left:190px;" alt="Asset Image 9795" data-bs-toggle="modal" data-bs-target="#imageModal9795" onclick="fetchAssetData(9795);" data-room="<?php echo htmlspecialchars($room9795); ?>" data-floor="<?php echo htmlspecialchars($floor9795); ?>" data-image="<?php echo base64_encode($upload_img9795); ?>" data-category="<?php echo htmlspecialchars($category9795); ?>" data-status="<?php echo htmlspecialchars($status9795); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9795); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status9795); ?>; position:absolute; top:111px; left:197px;'>
</div>


<!-- ASSET 9796 chair -->
<img src="../image.php?id=9796" class="asset-image" data-id="<?php echo $assetId9796; ?>" style="width:11px; cursor:pointer; position:absolute; top:167px; left:296px; transform: rotate(180deg);" alt="Asset Image 9796" data-bs-toggle="modal" data-bs-target="#imageModal9796" onclick="fetchAssetData(9796);" data-room="<?php echo htmlspecialchars($room9796); ?>" data-floor="<?php echo htmlspecialchars($floor9796); ?>" data-image="<?php echo base64_encode($upload_img9796); ?>" data-category="<?php echo htmlspecialchars($category9796); ?>" data-status="<?php echo htmlspecialchars($status9796); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9796); ?>">
<div style='width:6px; height:6px; border-radius:50%; background-color: <?php echo getStatusColor($status9796); ?>; position:absolute; top:173px; left:294px;'>
</div>



<!-- ASSET 9797 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9797; ?>" style="width:15px; cursor:pointer; position:absolute; top:189px; left:168px;" alt="Asset Image 9797" data-bs-toggle="modal" data-bs-target="#imageModal9797" onclick="fetchAssetData(9797);" data-room="<?php echo htmlspecialchars($room9797); ?>" data-floor="<?php echo htmlspecialchars($floor9797); ?>" data-image="<?php echo base64_encode($upload_img9797); ?>" data-category="<?php echo htmlspecialchars($category9797); ?>" data-status="<?php echo htmlspecialchars($status9797); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9797); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9797); ?>; position:absolute; top:189px; left:178px;'>
</div>


<!-- ASSET 9798 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9798; ?>" style="width:15px; cursor:pointer; position:absolute; top:189px; left:238px;" alt="Asset Image 9798" data-bs-toggle="modal" data-bs-target="#imageModal9798" onclick="fetchAssetData(9798);" data-room="<?php echo htmlspecialchars($room9798); ?>" data-floor="<?php echo htmlspecialchars($floor9798); ?>" data-image="<?php echo base64_encode($upload_img9798); ?>" data-category="<?php echo htmlspecialchars($category9798); ?>" data-status="<?php echo htmlspecialchars($status9798); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9798); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9798); ?>; position:absolute; top:189px; left:248px;'>
</div>


<!-- ASSET 9799 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9799; ?>" style="width:15px; cursor:pointer; position:absolute; top:189px; left:310px;" alt="Asset Image 9799" data-bs-toggle="modal" data-bs-target="#imageModal9799" onclick="fetchAssetData(9799);" data-room="<?php echo htmlspecialchars($room9799); ?>" data-floor="<?php echo htmlspecialchars($floor9799); ?>" data-image="<?php echo base64_encode($upload_img9799); ?>" data-category="<?php echo htmlspecialchars($category9799); ?>" data-status="<?php echo htmlspecialchars($status9799); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9799); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9799); ?>; position:absolute; top:189px; left:320px;'>
</div>


<!-- ASSET 9800 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9800; ?>" style="width:15px; cursor:pointer; position:absolute; top:241px; left:310px;" alt="Asset Image 9800" data-bs-toggle="modal" data-bs-target="#imageModal9800" onclick="fetchAssetData(9800);" data-room="<?php echo htmlspecialchars($room9800); ?>" data-floor="<?php echo htmlspecialchars($floor9800); ?>" data-image="<?php echo base64_encode($upload_img9800); ?>" data-category="<?php echo htmlspecialchars($category9800); ?>" data-status="<?php echo htmlspecialchars($status9800); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9800); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9800); ?>; position:absolute; top:241px; left:320px;'>
</div>


<!-- ASSET 9801 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9801; ?>" style="width:15px; cursor:pointer; position:absolute; top:241px; left:168px;" alt="Asset Image 9801" data-bs-toggle="modal" data-bs-target="#imageModal9801" onclick="fetchAssetData(9801);" data-room="<?php echo htmlspecialchars($room9801); ?>" data-floor="<?php echo htmlspecialchars($floor9801); ?>" data-image="<?php echo base64_encode($upload_img9801); ?>" data-category="<?php echo htmlspecialchars($category9801); ?>" data-status="<?php echo htmlspecialchars($status9801); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9801); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9801); ?>; position:absolute; top:241px; left:178px;'>
</div>


<!-- ASSET 9802 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9802; ?>" style="width:15px; cursor:pointer; position:absolute; top:241px; left:238px;" alt="Asset Image 9802" data-bs-toggle="modal" data-bs-target="#imageModal9802" onclick="fetchAssetData(9802);" data-room="<?php echo htmlspecialchars($room9802); ?>" data-floor="<?php echo htmlspecialchars($floor9802); ?>" data-image="<?php echo base64_encode($upload_img9802); ?>" data-category="<?php echo htmlspecialchars($category9802); ?>" data-status="<?php echo htmlspecialchars($status9802); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9802); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9802); ?>; position:absolute; top:241px; left:248px;'>
</div>


      <!-- ASSET 9803 ac-->
      <img src="../image.php?id=9803" class="asset-image" data-id="<?php echo $assetId9803; ?>" style="width:30px; cursor:pointer; position:absolute; top:214px; left:199px;" alt="Asset Image 9803" data-bs-toggle="modal" data-bs-target="#imageModal9803" onclick="fetchAssetData(9803);" data-room="<?php echo htmlspecialchars($room9803); ?>" data-floor="<?php echo htmlspecialchars($floor9803); ?>" data-image="<?php echo base64_encode($upload_img9803); ?>" data-category="<?php echo htmlspecialchars($category9803); ?>" data-status="<?php echo htmlspecialchars($status9803); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9803); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9803); ?>; position:absolute; top:214px; left:221px;'>
                        </div>

    <!-- ASSET 9804 ac-->
    <img src="../image.php?id=9804" class="asset-image" data-id="<?php echo $assetId9804; ?>" style="width:30px; cursor:pointer; position:absolute; top:214px; left:253px;" alt="Asset Image 9804" data-bs-toggle="modal" data-bs-target="#imageModal9804" onclick="fetchAssetData(9804);" data-room="<?php echo htmlspecialchars($room9804); ?>" data-floor="<?php echo htmlspecialchars($floor9804); ?>" data-image="<?php echo base64_encode($upload_img9804); ?>" data-category="<?php echo htmlspecialchars($category9804); ?>" data-status="<?php echo htmlspecialchars($status9804); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9804); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9804); ?>; position:absolute; top:214px; left:275px;'>
                        </div>



<!-- ASSET 9810 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9810; ?>" style="width:15px; cursor:pointer; position:absolute; top:195px; left:187px; transform: rotate(90deg);" alt="Asset Image 9810" data-bs-toggle="modal" data-bs-target="#imageModal9810" onclick="fetchAssetData(9810);" data-room="<?php echo htmlspecialchars($room9810); ?>" data-floor="<?php echo htmlspecialchars($floor9810); ?>" data-image="<?php echo base64_encode($upload_img9810); ?>" data-category="<?php echo htmlspecialchars($category9810); ?>" data-status="<?php echo htmlspecialchars($status9810); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9810); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9810); ?>; position:absolute; top:197px; left:203px;'>
</div>



<!-- ASSET 9811 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9811; ?>" style="width:15px; cursor:pointer; position:absolute; top:195px; left:216px; transform: rotate(90deg);" alt="Asset Image 9811" data-bs-toggle="modal" data-bs-target="#imageModal9811" onclick="fetchAssetData(9811);" data-room="<?php echo htmlspecialchars($room9811); ?>" data-floor="<?php echo htmlspecialchars($floor9811); ?>" data-image="<?php echo base64_encode($upload_img9811); ?>" data-category="<?php echo htmlspecialchars($category9811); ?>" data-status="<?php echo htmlspecialchars($status9811); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9811); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9811); ?>; position:absolute; top:197px; left:232px;'>
</div>


<!-- ASSET 9812 desk -->
<img src="../image.php?id=9812" class="asset-image" data-id="<?php echo $assetId9812; ?>" style="width:15px; cursor:pointer; position:absolute; top:209px; left:298px; transform: rotate(360deg);" alt="Asset Image 9812" data-bs-toggle="modal" data-bs-target="#imageModal9812" onclick="fetchAssetData(9812);" data-room="<?php echo htmlspecialchars($room9812); ?>" data-floor="<?php echo htmlspecialchars($floor9812); ?>" data-image="<?php echo base64_encode($upload_img9812); ?>" data-category="<?php echo htmlspecialchars($category9812); ?>" data-status="<?php echo htmlspecialchars($status9812); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9812); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9812); ?>; position:absolute; top:205px; left:308px;'>
</div>


<!-- ASSET 9805 chair -->
<img src="../image.php?id=9805" class="asset-image" data-id="<?php echo $assetId9805; ?>" style="width:11px; cursor:pointer; position:absolute; top:193px; left:191px; transform: rotate(180deg);" alt="Asset Image 9805" data-bs-toggle="modal" data-bs-target="#imageModal9805" onclick="fetchAssetData(9805);" data-room="<?php echo htmlspecialchars($room9805); ?>" data-floor="<?php echo htmlspecialchars($floor9805); ?>" data-image="<?php echo base64_encode($upload_img9805); ?>" data-category="<?php echo htmlspecialchars($category9805); ?>" data-status="<?php echo htmlspecialchars($status9805); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9805); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9805); ?>; position:absolute; top:187px; left:198px;'>
</div>


<!-- ASSET 9806 chair -->
<img src="../image.php?id=9806" class="asset-image" data-id="<?php echo $assetId9806; ?>" style="width:11px; cursor:pointer; position:absolute; top:193px; left:220px; transform: rotate(180deg);" alt="Asset Image 9806" data-bs-toggle="modal" data-bs-target="#imageModal9806" onclick="fetchAssetData(9806);" data-room="<?php echo htmlspecialchars($room9806); ?>" data-floor="<?php echo htmlspecialchars($floor9806); ?>" data-image="<?php echo base64_encode($upload_img9806); ?>" data-category="<?php echo htmlspecialchars($category9806); ?>" data-status="<?php echo htmlspecialchars($status9806); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9806); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9806); ?>; position:absolute; top:187px; left:228px;'>
</div>



<!-- ASSET 9807 chair -->
<img src="../image.php?id=9807" class="asset-image" data-id="<?php echo $assetId9807; ?>" style="width:11px; cursor:pointer; position:absolute; top:210px; left:288px; transform: rotate(180deg);" alt="Asset Image 9807" data-bs-toggle="modal" data-bs-target="#imageModal9807" onclick="fetchAssetData(9807);" data-room="<?php echo htmlspecialchars($room9807); ?>" data-floor="<?php echo htmlspecialchars($floor9807); ?>" data-image="<?php echo base64_encode($upload_img9807); ?>" data-category="<?php echo htmlspecialchars($category9807); ?>" data-status="<?php echo htmlspecialchars($status9807); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9807); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9807); ?>; position:absolute; top:205px; left:286px;'>
</div>



<!-- ASSET 9808 chair -->
<img src="../image.php?id=9808" class="asset-image" data-id="<?php echo $assetId9808; ?>" style="width:11px; cursor:pointer; position:absolute; top:225px; left:288px; transform: rotate(360deg);" alt="Asset Image 9808" data-bs-toggle="modal" data-bs-target="#imageModal9808" onclick="fetchAssetData(9808);" data-room="<?php echo htmlspecialchars($room9808); ?>" data-floor="<?php echo htmlspecialchars($floor9808); ?>" data-image="<?php echo base64_encode($upload_img9808); ?>" data-category="<?php echo htmlspecialchars($category9808); ?>" data-status="<?php echo htmlspecialchars($status9808); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9808); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9808); ?>; position:absolute; top:232px; left:286px;'>
</div>

<!-- ASSET 9809 chair -->
<img src="../image.php?id=9796" class="asset-image" data-id="<?php echo $assetId9809; ?>" style="width:11px; cursor:pointer; position:absolute; top:222px; left:314px; transform: rotate(134deg);" alt="Asset Image 9809" data-bs-toggle="modal" data-bs-target="#imageModal9809" onclick="fetchAssetData(9809);" data-room="<?php echo htmlspecialchars($room9809); ?>" data-floor="<?php echo htmlspecialchars($floor9809); ?>" data-image="<?php echo base64_encode($upload_img9809); ?>" data-category="<?php echo htmlspecialchars($category9809); ?>" data-status="<?php echo htmlspecialchars($status9809); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9809); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9809); ?>; position:absolute; top:224px; left:321px;'>
</div>


<!-- ASSET 9815 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9815; ?>" style="width:15px; cursor:pointer; position:absolute; top:260px; left:168px;" alt="Asset Image 9815" data-bs-toggle="modal" data-bs-target="#imageModal9815" onclick="fetchAssetData(9815);" data-room="<?php echo htmlspecialchars($room9815); ?>" data-floor="<?php echo htmlspecialchars($floor9815); ?>" data-image="<?php echo base64_encode($upload_img9815); ?>" data-category="<?php echo htmlspecialchars($category9815); ?>" data-status="<?php echo htmlspecialchars($status9815); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9815); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9815); ?>; position:absolute; top:260px; left:178px;'>
</div>


<!-- ASSET 9816 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9816; ?>" style="width:15px; cursor:pointer; position:absolute; top:260px; left:238px;" alt="Asset Image 9816" data-bs-toggle="modal" data-bs-target="#imageModal9816" onclick="fetchAssetData(9816);" data-room="<?php echo htmlspecialchars($room9816); ?>" data-floor="<?php echo htmlspecialchars($floor9816); ?>" data-image="<?php echo base64_encode($upload_img9816); ?>" data-category="<?php echo htmlspecialchars($category9816); ?>" data-status="<?php echo htmlspecialchars($status9816); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9816); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9816); ?>; position:absolute; top:260px; left:248px;'>
</div>


<!-- ASSET 9817 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9817; ?>" style="width:15px; cursor:pointer; position:absolute; top:260px; left:310px;" alt="Asset Image 9817" data-bs-toggle="modal" data-bs-target="#imageModal9817" onclick="fetchAssetData(9817);" data-room="<?php echo htmlspecialchars($room9817); ?>" data-floor="<?php echo htmlspecialchars($floor9817); ?>" data-image="<?php echo base64_encode($upload_img9817); ?>" data-category="<?php echo htmlspecialchars($category9817); ?>" data-status="<?php echo htmlspecialchars($status9817); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9817); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9817); ?>; position:absolute; top:260px; left:320px;'>
</div>


<!-- ASSET 9818 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9818; ?>" style="width:15px; cursor:pointer; position:absolute; top:313px; left:168px;" alt="Asset Image 9818" data-bs-toggle="modal" data-bs-target="#imageModal9818" onclick="fetchAssetData(9818);" data-room="<?php echo htmlspecialchars($room9818); ?>" data-floor="<?php echo htmlspecialchars($floor9818); ?>" data-image="<?php echo base64_encode($upload_img9818); ?>" data-category="<?php echo htmlspecialchars($category9818); ?>" data-status="<?php echo htmlspecialchars($status9818); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9818); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9818); ?>; position:absolute; top:313px; left:178px;'>
</div>


<!-- ASSET 9819 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9819; ?>" style="width:15px; cursor:pointer; position:absolute; top:313px; left:238px;" alt="Asset Image 9819" data-bs-toggle="modal" data-bs-target="#imageModal9819" onclick="fetchAssetData(9819);" data-room="<?php echo htmlspecialchars($room9819); ?>" data-floor="<?php echo htmlspecialchars($floor9819); ?>" data-image="<?php echo base64_encode($upload_img9819); ?>" data-category="<?php echo htmlspecialchars($category9819); ?>" data-status="<?php echo htmlspecialchars($status9819); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9819); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9819); ?>; position:absolute; top:313px; left:248px;'>
</div>


<!-- ASSET 9820 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9820; ?>" style="width:15px; cursor:pointer; position:absolute; top:313px; left:310px;" alt="Asset Image 9820" data-bs-toggle="modal" data-bs-target="#imageModal9820" onclick="fetchAssetData(9820);" data-room="<?php echo htmlspecialchars($room9820); ?>" data-floor="<?php echo htmlspecialchars($floor9820); ?>" data-image="<?php echo base64_encode($upload_img9820); ?>" data-category="<?php echo htmlspecialchars($category9820); ?>" data-status="<?php echo htmlspecialchars($status9820); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9820); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9820); ?>; position:absolute; top:313px; left:320px;'>
</div>

 <!-- ASSET 9821 ac-->
 <img src="../image.php?id=9821" class="asset-image" data-id="<?php echo $assetId9821; ?>" style="width:30px; cursor:pointer; position:absolute; top:286px; left:253px;" alt="Asset Image 9821" data-bs-toggle="modal" data-bs-target="#imageModal9821" onclick="fetchAssetData(9821);" data-room="<?php echo htmlspecialchars($room9821); ?>" data-floor="<?php echo htmlspecialchars($floor9821); ?>" data-image="<?php echo base64_encode($upload_img9821); ?>" data-category="<?php echo htmlspecialchars($category9821); ?>" data-status="<?php echo htmlspecialchars($status9821); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9821); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9821); ?>; position:absolute; top:286px; left:275px;'>
                        </div>

                          <!-- ASSET 9822 ac-->
  <img src="../image.php?id=9822" class="asset-image" data-id="<?php echo $assetId9822; ?>" style="width:30px; cursor:pointer; position:absolute; top:286px; left:199px;" alt="Asset Image 9822" data-bs-toggle="modal" data-bs-target="#imageModal9822" onclick="fetchAssetData(9822);" data-room="<?php echo htmlspecialchars($room9822); ?>" data-floor="<?php echo htmlspecialchars($floor9822); ?>" data-image="<?php echo base64_encode($upload_img9822); ?>" data-category="<?php echo htmlspecialchars($category9822); ?>" data-status="<?php echo htmlspecialchars($status9822); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9822); ?>">
                        <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9822); ?>; position:absolute; top:286px; left:221px;'>
                        </div>

<!-- ASSET 9828 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9828; ?>" style="width:15px; cursor:pointer; position:absolute; top:267px; left:187px; transform: rotate(90deg);" alt="Asset Image 9828" data-bs-toggle="modal" data-bs-target="#imageModal9828" onclick="fetchAssetData(9828);" data-room="<?php echo htmlspecialchars($room9828); ?>" data-floor="<?php echo htmlspecialchars($floor9828); ?>" data-image="<?php echo base64_encode($upload_img9828); ?>" data-category="<?php echo htmlspecialchars($category9828); ?>" data-status="<?php echo htmlspecialchars($status9828); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9828); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9828); ?>; position:absolute; top:269px; left:203px;'>
</div>



<!-- ASSET 9829 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9829; ?>" style="width:15px; cursor:pointer; position:absolute; top:267px; left:216px; transform: rotate(90deg);" alt="Asset Image 9829" data-bs-toggle="modal" data-bs-target="#imageModal9829" onclick="fetchAssetData(9829);" data-room="<?php echo htmlspecialchars($room9829); ?>" data-floor="<?php echo htmlspecialchars($floor9829); ?>" data-image="<?php echo base64_encode($upload_img9829); ?>" data-category="<?php echo htmlspecialchars($category9829); ?>" data-status="<?php echo htmlspecialchars($status9829); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9829); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9829); ?>; position:absolute; top:269px; left:232px;'>
</div>


<!-- ASSET 9830 desk -->
<img src="../image.php?id=9812" class="asset-image" data-id="<?php echo $assetId9830; ?>" style="width:15px; cursor:pointer; position:absolute; top:279px; left:298px; transform: rotate(90deg);" alt="Asset Image 9830" data-bs-toggle="modal" data-bs-target="#imageModal9830" onclick="fetchAssetData(9830);" data-room="<?php echo htmlspecialchars($room9830); ?>" data-floor="<?php echo htmlspecialchars($floor9830); ?>" data-image="<?php echo base64_encode($upload_img9830); ?>" data-category="<?php echo htmlspecialchars($category9830); ?>" data-status="<?php echo htmlspecialchars($status9830); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9830); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9830); ?>; position:absolute; top:295px; left:315px;'>
</div>

<!-- ASSET 9823 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9823; ?>" style="width:11px; cursor:pointer; position:absolute; top:263px; left:191px;" alt="Asset Image 9823" data-bs-toggle="modal" data-bs-target="#imageModal9823" onclick="fetchAssetData(9823);" data-room="<?php echo htmlspecialchars($room9823); ?>" data-floor="<?php echo htmlspecialchars($floor9823); ?>" data-image="<?php echo base64_encode($upload_img9823); ?>" data-category="<?php echo htmlspecialchars($category9823); ?>" data-status="<?php echo htmlspecialchars($status9823); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9823); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9823); ?>; position:absolute; top:257px; left:198px;'>
</div>

<!-- ASSET 9824 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9824; ?>" style="width:11px; cursor:pointer; position:absolute; top:263px; left:220px;" alt="Asset Image 9824" data-bs-toggle="modal" data-bs-target="#imageModal9824" onclick="fetchAssetData(9824);" data-room="<?php echo htmlspecialchars($room9824); ?>" data-floor="<?php echo htmlspecialchars($floor9824); ?>" data-image="<?php echo base64_encode($upload_img9824); ?>" data-category="<?php echo htmlspecialchars($category9824); ?>" data-status="<?php echo htmlspecialchars($status9824); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9824); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9824); ?>; position:absolute; top:257px; left:228px;'>
</div>


<!-- ASSET 9825 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9825; ?>" style="width:11px; cursor:pointer; position:absolute; top:276px; left:307px; transform: rotate(90deg);" alt="Asset Image 9825" data-bs-toggle="modal" data-bs-target="#imageModal9825" onclick="fetchAssetData(9825);" data-room="<?php echo htmlspecialchars($room9825); ?>" data-floor="<?php echo htmlspecialchars($floor9825); ?>" data-image="<?php echo base64_encode($upload_img9825); ?>" data-category="<?php echo htmlspecialchars($category9825); ?>" data-status="<?php echo htmlspecialchars($status9825); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9825); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9825); ?>; position:absolute; top:273px; left:315px;'>
</div>


<!-- ASSET 9826 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9826; ?>" style="width:11px; cursor:pointer; position:absolute; top:277px; left:293px; transform: rotate(270deg);" alt="Asset Image 9826" data-bs-toggle="modal" data-bs-target="#imageModal9826" onclick="fetchAssetData(9826);" data-room="<?php echo htmlspecialchars($room9826); ?>" data-floor="<?php echo htmlspecialchars($floor9826); ?>" data-image="<?php echo base64_encode($upload_img9826); ?>" data-category="<?php echo htmlspecialchars($category9826); ?>" data-status="<?php echo htmlspecialchars($status9826); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9826); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9826); ?>; position:absolute; top:273px; left:289px;'>
</div>


<!-- ASSET 9827 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9827; ?>" style="width:11px; cursor:pointer; position:absolute; top:301px; left:295px; transform: rotate(225deg);" alt="Asset Image 9827" data-bs-toggle="modal" data-bs-target="#imageModal9827" onclick="fetchAssetData(9827);" data-room="<?php echo htmlspecialchars($room9827); ?>" data-floor="<?php echo htmlspecialchars($floor9827); ?>" data-image="<?php echo base64_encode($upload_img9827); ?>" data-category="<?php echo htmlspecialchars($category9827); ?>" data-status="<?php echo htmlspecialchars($status9827); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9827); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9827); ?>; position:absolute; top:308px; left:297px;'>
</div>



<!-- ASSET 9831 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9831; ?>" style="width:15px; cursor:pointer; position:absolute; top:333px; left:168px;" alt="Asset Image 9831" data-bs-toggle="modal" data-bs-target="#imageModal9831" onclick="fetchAssetData(9831);" data-room="<?php echo htmlspecialchars($room9831); ?>" data-floor="<?php echo htmlspecialchars($floor9831); ?>" data-image="<?php echo base64_encode($upload_img9831); ?>" data-category="<?php echo htmlspecialchars($category9831); ?>" data-status="<?php echo htmlspecialchars($status9831); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9831); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9831); ?>; position:absolute; top:333px; left:178px;'>
</div>



<!-- ASSET 9832 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9832; ?>" style="width:15px; cursor:pointer; position:absolute; top:333px; left:310px;" alt="Asset Image 9832" data-bs-toggle="modal" data-bs-target="#imageModal9832" onclick="fetchAssetData(9832);" data-room="<?php echo htmlspecialchars($room9832); ?>" data-floor="<?php echo htmlspecialchars($floor9832); ?>" data-image="<?php echo base64_encode($upload_img9832); ?>" data-category="<?php echo htmlspecialchars($category9832); ?>" data-status="<?php echo htmlspecialchars($status9832); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9832); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9832); ?>; position:absolute; top:333px; left:320px;'>
</div>


<!-- ASSET 9833 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9833; ?>" style="width:15px; cursor:pointer; position:absolute; top:384px; left:168px;" alt="Asset Image 9833" data-bs-toggle="modal" data-bs-target="#imageModal9833" onclick="fetchAssetData(9833);" data-room="<?php echo htmlspecialchars($room9833); ?>" data-floor="<?php echo htmlspecialchars($floor9833); ?>" data-image="<?php echo base64_encode($upload_img9833); ?>" data-category="<?php echo htmlspecialchars($category9833); ?>" data-status="<?php echo htmlspecialchars($status9833); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9833); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9833); ?>; position:absolute; top:384px; left:178px;'>
</div>


<!-- ASSET 9834 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9834; ?>" style="width:15px; cursor:pointer; position:absolute; top:384px; left:238px;" alt="Asset Image 9834" data-bs-toggle="modal" data-bs-target="#imageModal9834" onclick="fetchAssetData(9834);" data-room="<?php echo htmlspecialchars($room9834); ?>" data-floor="<?php echo htmlspecialchars($floor9834); ?>" data-image="<?php echo base64_encode($upload_img9834); ?>" data-category="<?php echo htmlspecialchars($category9834); ?>" data-status="<?php echo htmlspecialchars($status9834); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9834); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9834); ?>; position:absolute; top:384px; left:248px;'>
</div>


<!-- ASSET 9835 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9835; ?>" style="width:15px; cursor:pointer; position:absolute; top:384px; left:310px;" alt="Asset Image 9835" data-bs-toggle="modal" data-bs-target="#imageModal9835" onclick="fetchAssetData(9835);" data-room="<?php echo htmlspecialchars($room9835); ?>" data-floor="<?php echo htmlspecialchars($floor9835); ?>" data-image="<?php echo base64_encode($upload_img9835); ?>" data-category="<?php echo htmlspecialchars($category9835); ?>" data-status="<?php echo htmlspecialchars($status9835); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9835); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9835); ?>; position:absolute; top:384px; left:320px;'>
</div>




 <!-- ASSET 9836 ac-->
 <img src="../image.php?id=9836" class="asset-image" data-id="<?php echo $assetId9836; ?>" style="width:30px; cursor:pointer; position:absolute; top:354px; left:253px;" alt="Asset Image 9836" data-bs-toggle="modal" data-bs-target="#imageModal9836" onclick="fetchAssetData(9836);" data-room="<?php echo htmlspecialchars($room9836); ?>" data-floor="<?php echo htmlspecialchars($floor9836); ?>" data-image="<?php echo base64_encode($upload_img9836); ?>" data-category="<?php echo htmlspecialchars($category9836); ?>" data-status="<?php echo htmlspecialchars($status9836); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9836); ?>">
  <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9836); ?>; position:absolute; top:354px; left:275px;'>
  </div>


   <!-- ASSET 9837 ac-->
   <img src="../image.php?id=9837" class="asset-image" data-id="<?php echo $assetId9837; ?>" style="width:30px; cursor:pointer; position:absolute; top:354px; left:199px;" alt="Asset Image 9837" data-bs-toggle="modal" data-bs-target="#imageModal9837" onclick="fetchAssetData(9837);" data-room="<?php echo htmlspecialchars($room9837); ?>" data-floor="<?php echo htmlspecialchars($floor9837); ?>" data-image="<?php echo base64_encode($upload_img9837); ?>" data-category="<?php echo htmlspecialchars($category9837); ?>" data-status="<?php echo htmlspecialchars($status9837); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9837); ?>">
  <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9837); ?>; position:absolute; top:354px; left:221px;'>
  </div>


<!-- ASSET 9838 Sofa -->
<img src="../image.php?id=9790" class="asset-image" data-id="<?php echo $assetId9838; ?>" style="width:38px; cursor:pointer; position:absolute; top:357px; left:157px; transform: rotate(90deg);" alt="Asset Image 9838" data-bs-toggle="modal" data-bs-target="#imageModal9838" onclick="fetchAssetData(9838);" data-room="<?php echo htmlspecialchars($room9838); ?>" data-floor="<?php echo htmlspecialchars($floor9838); ?>" data-image="<?php echo base64_encode($upload_img9838); ?>" data-category="<?php echo htmlspecialchars($category9838); ?>" data-status="<?php echo htmlspecialchars($status9838); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9838); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9838); ?>; position:absolute; top:362px; left:181px;'>
</div>


<!-- ASSET 9839 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9839; ?>" style="width:15px; cursor:pointer; position:absolute; top:336px; left:195px; transform: rotate(90deg);" alt="Asset Image 9839" data-bs-toggle="modal" data-bs-target="#imageModal9839" onclick="fetchAssetData(9839);" data-room="<?php echo htmlspecialchars($room9839); ?>" data-floor="<?php echo htmlspecialchars($floor9839); ?>" data-image="<?php echo base64_encode($upload_img9839); ?>" data-category="<?php echo htmlspecialchars($category9839); ?>" data-status="<?php echo htmlspecialchars($status9839); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9839); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9839); ?>; position:absolute; top:337px; left:211px;'>
</div>

<!-- ASSET 9840 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9840; ?>" style="width:15px; cursor:pointer; position:absolute; top:336px; left:225px; transform: rotate(90deg);" alt="Asset Image 9840" data-bs-toggle="modal" data-bs-target="#imageModal9840" onclick="fetchAssetData(9840);" data-room="<?php echo htmlspecialchars($room9840); ?>" data-floor="<?php echo htmlspecialchars($floor9840); ?>" data-image="<?php echo base64_encode($upload_img9840); ?>" data-category="<?php echo htmlspecialchars($category9840); ?>" data-status="<?php echo htmlspecialchars($status9840); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9840); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9840); ?>; position:absolute; top:337px; left:240px;'>
</div>


<!-- ASSET 9841 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9841; ?>" style="width:15px; cursor:pointer; position:absolute; top:336px; left:282px; transform: rotate(90deg);" alt="Asset Image 9841" data-bs-toggle="modal" data-bs-target="#imageModal9841" onclick="fetchAssetData(9841);" data-room="<?php echo htmlspecialchars($room9841); ?>" data-floor="<?php echo htmlspecialchars($floor9841); ?>" data-image="<?php echo base64_encode($upload_img9841); ?>" data-category="<?php echo htmlspecialchars($category9841); ?>" data-status="<?php echo htmlspecialchars($status9841); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9841); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9841); ?>; position:absolute; top:337px; left:299px;'>
</div>


<!-- ASSET 9842 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9842; ?>" style="width:11px; cursor:pointer; position:absolute; top:333px; left:199px;" alt="Asset Image 9842" data-bs-toggle="modal" data-bs-target="#imageModal9842" onclick="fetchAssetData(9842);" data-room="<?php echo htmlspecialchars($room9842); ?>" data-floor="<?php echo htmlspecialchars($floor9842); ?>" data-image="<?php echo base64_encode($upload_img9842); ?>" data-category="<?php echo htmlspecialchars($category9842); ?>" data-status="<?php echo htmlspecialchars($status9842); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9842); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9842); ?>; position:absolute; top:328px; left:205px;'>
</div>

<!-- ASSET 9843 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9843; ?>" style="width:11px; cursor:pointer; position:absolute; top:333px; left:229px;" alt="Asset Image 9843" data-bs-toggle="modal" data-bs-target="#imageModal9843" onclick="fetchAssetData(9843);" data-room="<?php echo htmlspecialchars($room9843); ?>" data-floor="<?php echo htmlspecialchars($floor9843); ?>" data-image="<?php echo base64_encode($upload_img9843); ?>" data-category="<?php echo htmlspecialchars($category9843); ?>" data-status="<?php echo htmlspecialchars($status9843); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9843); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9843); ?>; position:absolute; top:328px; left:235px;'>
</div>

<!-- ASSET 9844 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9844; ?>" style="width:11px; cursor:pointer; position:absolute; top:333px; left:286px;" alt="Asset Image 9844" data-bs-toggle="modal" data-bs-target="#imageModal9844" onclick="fetchAssetData(9844);" data-room="<?php echo htmlspecialchars($room9844); ?>" data-floor="<?php echo htmlspecialchars($floor9844); ?>" data-image="<?php echo base64_encode($upload_img9844); ?>" data-category="<?php echo htmlspecialchars($category9844); ?>" data-status="<?php echo htmlspecialchars($status9844); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9844); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9844); ?>; position:absolute; top:328px; left:292px;'>
</div>

 <!-- ASSET 9851 ac-->
 <img src="../image.php?id=9851" class="asset-image" data-id="<?php echo $assetId9851; ?>" style="width:30px; cursor:pointer; position:absolute; top:423px; left:266px;" alt="Asset Image 9851" data-bs-toggle="modal" data-bs-target="#imageModal9851" onclick="fetchAssetData(9851);" data-room="<?php echo htmlspecialchars($room9851); ?>" data-floor="<?php echo htmlspecialchars($floor9851); ?>" data-image="<?php echo base64_encode($upload_img9851); ?>" data-category="<?php echo htmlspecialchars($category9851); ?>" data-status="<?php echo htmlspecialchars($status9851); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9851); ?>">
  <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9851); ?>; position:absolute; top:423px; left:288px;'>
  </div>

  <!-- ASSET 9852 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9852; ?>" style="width:15px; cursor:pointer; position:absolute; top:407px; left:310px;" alt="Asset Image 9852" data-bs-toggle="modal" data-bs-target="#imageModal9852" onclick="fetchAssetData(9852);" data-room="<?php echo htmlspecialchars($room9852); ?>" data-floor="<?php echo htmlspecialchars($floor9852); ?>" data-image="<?php echo base64_encode($upload_img9852); ?>" data-category="<?php echo htmlspecialchars($category9852); ?>" data-status="<?php echo htmlspecialchars($status9852); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9852); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9852); ?>; position:absolute; top:407px; left:320px;'>
</div>


  <!-- ASSET 9853 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9853; ?>" style="width:15px; cursor:pointer; position:absolute; top:458px; left:310px;" alt="Asset Image 9853" data-bs-toggle="modal" data-bs-target="#imageModal9853" onclick="fetchAssetData(9853);" data-room="<?php echo htmlspecialchars($room9853); ?>" data-floor="<?php echo htmlspecialchars($floor9853); ?>" data-image="<?php echo base64_encode($upload_img9853); ?>" data-category="<?php echo htmlspecialchars($category9853); ?>" data-status="<?php echo htmlspecialchars($status9853); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9853); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9853); ?>; position:absolute; top:458px; left:320px;'>
</div>

  <!-- ASSET 9854 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9854; ?>" style="width:15px; cursor:pointer; position:absolute; top:407px; left:243px;" alt="Asset Image 9854" data-bs-toggle="modal" data-bs-target="#imageModal9854" onclick="fetchAssetData(9854);" data-room="<?php echo htmlspecialchars($room9854); ?>" data-floor="<?php echo htmlspecialchars($floor9854); ?>" data-image="<?php echo base64_encode($upload_img9854); ?>" data-category="<?php echo htmlspecialchars($category9854); ?>" data-status="<?php echo htmlspecialchars($status9854); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9854); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9854); ?>; position:absolute; top:407px; left:253px;'>
</div>

  <!-- ASSET 9855 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9855; ?>" style="width:15px; cursor:pointer; position:absolute; top:458px; left:243px;" alt="Asset Image 9855" data-bs-toggle="modal" data-bs-target="#imageModal9855" onclick="fetchAssetData(9855);" data-room="<?php echo htmlspecialchars($room9855); ?>" data-floor="<?php echo htmlspecialchars($floor9855); ?>" data-image="<?php echo base64_encode($upload_img9855); ?>" data-category="<?php echo htmlspecialchars($category9855); ?>" data-status="<?php echo htmlspecialchars($status9855); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9855); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9855); ?>; position:absolute; top:458px; left:253px;'>
</div>

 <!-- ASSET 9856 light -->
 <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9856; ?>" style="width:15px; cursor:pointer; position:absolute; top:404px; left:164px;" alt="Asset Image 9856" data-bs-toggle="modal" data-bs-target="#imageModal9856" onclick="fetchAssetData(9856);" data-room="<?php echo htmlspecialchars($room9856); ?>" data-floor="<?php echo htmlspecialchars($floor9856); ?>" data-image="<?php echo base64_encode($upload_img9856); ?>" data-category="<?php echo htmlspecialchars($category9856); ?>" data-status="<?php echo htmlspecialchars($status9856); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9856); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9856); ?>; position:absolute; top:404px; left:174px;'>
</div>


  <!-- ASSET 9857 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9857; ?>" style="width:15px; cursor:pointer; position:absolute; top:404px; left:219px;" alt="Asset Image 9857" data-bs-toggle="modal" data-bs-target="#imageModal9857" onclick="fetchAssetData(9857);" data-room="<?php echo htmlspecialchars($room9857); ?>" data-floor="<?php echo htmlspecialchars($floor9857); ?>" data-image="<?php echo base64_encode($upload_img9857); ?>" data-category="<?php echo htmlspecialchars($category9857); ?>" data-status="<?php echo htmlspecialchars($status9857); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9857); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9857); ?>; position:absolute; top:404px; left:229px;'>
</div>

 <!-- ASSET 9858 light -->
 <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9858; ?>" style="width:15px; cursor:pointer; position:absolute; top:421px; left:164px;" alt="Asset Image 9858" data-bs-toggle="modal" data-bs-target="#imageModal9858" onclick="fetchAssetData(9858);" data-room="<?php echo htmlspecialchars($room9858); ?>" data-floor="<?php echo htmlspecialchars($floor9858); ?>" data-image="<?php echo base64_encode($upload_img9858); ?>" data-category="<?php echo htmlspecialchars($category9858); ?>" data-status="<?php echo htmlspecialchars($status9858); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9858); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9858); ?>; position:absolute; top:421px; left:174px;'>
</div>






  <!-- ASSET 9859 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9859; ?>" style="width:15px; cursor:pointer; position:absolute; top:421px; left:219px;" alt="Asset Image 9859" data-bs-toggle="modal" data-bs-target="#imageModal9859" onclick="fetchAssetData(9859);" data-room="<?php echo htmlspecialchars($room9859); ?>" data-floor="<?php echo htmlspecialchars($floor9859); ?>" data-image="<?php echo base64_encode($upload_img9859); ?>" data-category="<?php echo htmlspecialchars($category9859); ?>" data-status="<?php echo htmlspecialchars($status9859); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9859); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9859); ?>; position:absolute; top:421px; left:229px;'>
</div>

 <!-- ASSET 9860 light -->
 <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9860; ?>" style="width:15px; cursor:pointer; position:absolute; top:441px; left:164px;" alt="Asset Image 9860" data-bs-toggle="modal" data-bs-target="#imageModal9860" onclick="fetchAssetData(9860);" data-room="<?php echo htmlspecialchars($room9860); ?>" data-floor="<?php echo htmlspecialchars($floor9860); ?>" data-image="<?php echo base64_encode($upload_img9860); ?>" data-category="<?php echo htmlspecialchars($category9860); ?>" data-status="<?php echo htmlspecialchars($status9860); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9860); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9860); ?>; position:absolute; top:441px; left:174px;'>
</div>


  <!-- ASSET 9861 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9861; ?>" style="width:15px; cursor:pointer; position:absolute; top:441px; left:219px;" alt="Asset Image 9861" data-bs-toggle="modal" data-bs-target="#imageModal9861" onclick="fetchAssetData(9861);" data-room="<?php echo htmlspecialchars($room9861); ?>" data-floor="<?php echo htmlspecialchars($floor9861); ?>" data-image="<?php echo base64_encode($upload_img9861); ?>" data-category="<?php echo htmlspecialchars($category9861); ?>" data-status="<?php echo htmlspecialchars($status9861); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9861); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9861); ?>; position:absolute; top:441px; left:229px;'>
</div>

 <!-- ASSET 9862 light -->
 <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9862; ?>" style="width:15px; cursor:pointer; position:absolute; top:459px; left:164px;" alt="Asset Image 9862" data-bs-toggle="modal" data-bs-target="#imageModal9862" onclick="fetchAssetData(9862);" data-room="<?php echo htmlspecialchars($room9862); ?>" data-floor="<?php echo htmlspecialchars($floor9862); ?>" data-image="<?php echo base64_encode($upload_img9862); ?>" data-category="<?php echo htmlspecialchars($category9862); ?>" data-status="<?php echo htmlspecialchars($status9862); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9862); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9862); ?>; position:absolute; top:459px; left:174px;'>
</div>


  <!-- ASSET 9863 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9863; ?>" style="width:15px; cursor:pointer; position:absolute; top:459px; left:219px;" alt="Asset Image 9863" data-bs-toggle="modal" data-bs-target="#imageModal9863" onclick="fetchAssetData(9863);" data-room="<?php echo htmlspecialchars($room9863); ?>" data-floor="<?php echo htmlspecialchars($floor9863); ?>" data-image="<?php echo base64_encode($upload_img9863); ?>" data-category="<?php echo htmlspecialchars($category9863); ?>" data-status="<?php echo htmlspecialchars($status9863); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9863); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9863); ?>; position:absolute; top:459px; left:229px;'>
</div>







  <!-- ASSET 9867 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9867; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:428px;" alt="Asset Image 9867" data-bs-toggle="modal" data-bs-target="#imageModal9867" onclick="fetchAssetData(9867);" data-room="<?php echo htmlspecialchars($room9867); ?>" data-floor="<?php echo htmlspecialchars($floor9867); ?>" data-image="<?php echo base64_encode($upload_img9867); ?>" data-category="<?php echo htmlspecialchars($category9867); ?>" data-status="<?php echo htmlspecialchars($status9867); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9867); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9867); ?>; position:absolute; top:479px; left:438px;'>
</div>
  <!-- ASSET 9868 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9868; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:373px;" alt="Asset Image 9868" data-bs-toggle="modal" data-bs-target="#imageModal9868" onclick="fetchAssetData(9868);" data-room="<?php echo htmlspecialchars($room9868); ?>" data-floor="<?php echo htmlspecialchars($floor9868); ?>" data-image="<?php echo base64_encode($upload_img9868); ?>" data-category="<?php echo htmlspecialchars($category9868); ?>" data-status="<?php echo htmlspecialchars($status9868); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9868); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9868); ?>; position:absolute; top:479px; left:383px;'>
</div>

  <!-- ASSET 9869 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9869; ?>" style="width:15px; cursor:pointer; position:absolute; top:500px; left:334px;" alt="Asset Image 9869" data-bs-toggle="modal" data-bs-target="#imageModal9869" onclick="fetchAssetData(9869);" data-room="<?php echo htmlspecialchars($room9869); ?>" data-floor="<?php echo htmlspecialchars($floor9869); ?>" data-image="<?php echo base64_encode($upload_img9869); ?>" data-category="<?php echo htmlspecialchars($category9869); ?>" data-status="<?php echo htmlspecialchars($status9869); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9869); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9869); ?>; position:absolute; top:500px; left:344px;'>
</div>

  <!-- ASSET 9870 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9870; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:334px;" alt="Asset Image 9870" data-bs-toggle="modal" data-bs-target="#imageModal9870" onclick="fetchAssetData(9870);" data-room="<?php echo htmlspecialchars($room9870); ?>" data-floor="<?php echo htmlspecialchars($floor9870); ?>" data-image="<?php echo base64_encode($upload_img9870); ?>" data-category="<?php echo htmlspecialchars($category9870); ?>" data-status="<?php echo htmlspecialchars($status9870); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9870); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9870); ?>; position:absolute; top:531px; left:344px;'>
</div>

  <!-- ASSET 9871 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9871; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:428px;" alt="Asset Image 9871" data-bs-toggle="modal" data-bs-target="#imageModal9871" onclick="fetchAssetData(9871);" data-room="<?php echo htmlspecialchars($room9871); ?>" data-floor="<?php echo htmlspecialchars($floor9871); ?>" data-image="<?php echo base64_encode($upload_img9871); ?>" data-category="<?php echo htmlspecialchars($category9871); ?>" data-status="<?php echo htmlspecialchars($status9871); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9871); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9871); ?>; position:absolute; top:531px; left:438px;'>
</div>
 <!-- ASSET 9872 ac-->
 <img src="../image.php?id=9872" class="asset-image" data-id="<?php echo $assetId9872; ?>" style="width:30px; cursor:pointer; position:absolute; top:494px; left:375px;" alt="Asset Image 9872" data-bs-toggle="modal" data-bs-target="#imageModal9872" onclick="fetchAssetData(9872);" data-room="<?php echo htmlspecialchars($room9872); ?>" data-floor="<?php echo htmlspecialchars($floor9872); ?>" data-image="<?php echo base64_encode($upload_img9872); ?>" data-category="<?php echo htmlspecialchars($category9872); ?>" data-status="<?php echo htmlspecialchars($status9872); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9872); ?>">
  <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9872); ?>; position:absolute; top:494px; left:397px;'>
  </div>


  <!-- ASSET 9879 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9879; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:553px;" alt="Asset Image 9879" data-bs-toggle="modal" data-bs-target="#imageModal9879" onclick="fetchAssetData(9879);" data-room="<?php echo htmlspecialchars($room9879); ?>" data-floor="<?php echo htmlspecialchars($floor9879); ?>" data-image="<?php echo base64_encode($upload_img9879); ?>" data-category="<?php echo htmlspecialchars($category9879); ?>" data-status="<?php echo htmlspecialchars($status9879); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9879); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9879); ?>; position:absolute; top:479px; left:563px;'>
</div>

  <!-- ASSET 9880 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9880; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:451px;" alt="Asset Image 9880" data-bs-toggle="modal" data-bs-target="#imageModal9880" onclick="fetchAssetData(9880);" data-room="<?php echo htmlspecialchars($room9880); ?>" data-floor="<?php echo htmlspecialchars($floor9880); ?>" data-image="<?php echo base64_encode($upload_img9880); ?>" data-category="<?php echo htmlspecialchars($category9880); ?>" data-status="<?php echo htmlspecialchars($status9880); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9880); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9880); ?>; position:absolute; top:479px; left:461px;'>
</div>

  <!-- ASSET 9881 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9881; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:553px;" alt="Asset Image 9881" data-bs-toggle="modal" data-bs-target="#imageModal9881" onclick="fetchAssetData(9881);" data-room="<?php echo htmlspecialchars($room9881); ?>" data-floor="<?php echo htmlspecialchars($floor9881); ?>" data-image="<?php echo base64_encode($upload_img9881); ?>" data-category="<?php echo htmlspecialchars($category9881); ?>" data-status="<?php echo htmlspecialchars($status9881); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9881); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9881); ?>; position:absolute; top:531px; left:563px;'>
</div>

  <!-- ASSET 9882 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9882; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:451px;" alt="Asset Image 9882" data-bs-toggle="modal" data-bs-target="#imageModal9882" onclick="fetchAssetData(9882);" data-room="<?php echo htmlspecialchars($room9882); ?>" data-floor="<?php echo htmlspecialchars($floor9882); ?>" data-image="<?php echo base64_encode($upload_img9882); ?>" data-category="<?php echo htmlspecialchars($category9882); ?>" data-status="<?php echo htmlspecialchars($status9882); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9882); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9882); ?>; position:absolute; top:531px; left:461px;'>
</div>

 <!-- ASSET 9883 ac-->
 <img src="../image.php?id=9883" class="asset-image" data-id="<?php echo $assetId9883; ?>" style="width:30px; cursor:pointer; position:absolute; top:494px; left:495px;" alt="Asset Image 9883" data-bs-toggle="modal" data-bs-target="#imageModal9883" onclick="fetchAssetData(9883);" data-room="<?php echo htmlspecialchars($room9883); ?>" data-floor="<?php echo htmlspecialchars($floor9883); ?>" data-image="<?php echo base64_encode($upload_img9883); ?>" data-category="<?php echo htmlspecialchars($category9883); ?>" data-status="<?php echo htmlspecialchars($status9883); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9883); ?>">
  <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9883); ?>; position:absolute; top:494px; left:517px;'>
  </div>


<!-- ASSET 9884 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9884; ?>" style="width:15px; cursor:pointer; position:absolute; top:518px; left:479px; transform: rotate(270deg);" alt="Asset Image 9884" data-bs-toggle="modal" data-bs-target="#imageModal9884" onclick="fetchAssetData(9884);" data-room="<?php echo htmlspecialchars($room9884); ?>" data-floor="<?php echo htmlspecialchars($floor9884); ?>" data-image="<?php echo base64_encode($upload_img9884); ?>" data-category="<?php echo htmlspecialchars($category9884); ?>" data-status="<?php echo htmlspecialchars($status9884); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9884); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9884); ?>; position:absolute; top:520px; left:472px;'>
</div>

<!-- ASSET 9886 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9886; ?>" style="width:11px; cursor:pointer; position:absolute; top:537px; left:479px; transform: rotate(180deg);" alt="Asset Image 9886" data-bs-toggle="modal" data-bs-target="#imageModal9886" onclick="fetchAssetData(9886);" data-room="<?php echo htmlspecialchars($room9886); ?>" data-floor="<?php echo htmlspecialchars($floor9886); ?>" data-image="<?php echo base64_encode($upload_img9886); ?>" data-category="<?php echo htmlspecialchars($category9886); ?>" data-status="<?php echo htmlspecialchars($status9886); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9886); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9886); ?>; position:absolute; top:541px; left:476px;'>
</div>

<!-- ASSET 9885 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9885; ?>" style="width:15px; cursor:pointer; position:absolute; top:518px; left:526px; transform: rotate(270deg);" alt="Asset Image 9885" data-bs-toggle="modal" data-bs-target="#imageModal9885" onclick="fetchAssetData(9885);" data-room="<?php echo htmlspecialchars($room9885); ?>" data-floor="<?php echo htmlspecialchars($floor9885); ?>" data-image="<?php echo base64_encode($upload_img9885); ?>" data-category="<?php echo htmlspecialchars($category9885); ?>" data-status="<?php echo htmlspecialchars($status9885); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9885); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9885); ?>; position:absolute; top:520px; left:540px;'>
</div>

<!-- ASSET 9887 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9887; ?>" style="width:11px; cursor:pointer; position:absolute; top:537px; left:526px; transform: rotate(180deg);" alt="Asset Image 9887" data-bs-toggle="modal" data-bs-target="#imageModal9887" onclick="fetchAssetData(9887);" data-room="<?php echo htmlspecialchars($room9887); ?>" data-floor="<?php echo htmlspecialchars($floor9887); ?>" data-image="<?php echo base64_encode($upload_img9887); ?>" data-category="<?php echo htmlspecialchars($category9887); ?>" data-status="<?php echo htmlspecialchars($status9887); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9887); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9887); ?>; position:absolute; top:541px; left:523px;'>
</div>




  <!-- ASSET 9892 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9892; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:678px;" alt="Asset Image 9892" data-bs-toggle="modal" data-bs-target="#imageModal9892" onclick="fetchAssetData(9892);" data-room="<?php echo htmlspecialchars($room9892); ?>" data-floor="<?php echo htmlspecialchars($floor9892); ?>" data-image="<?php echo base64_encode($upload_img9892); ?>" data-category="<?php echo htmlspecialchars($category9892); ?>" data-status="<?php echo htmlspecialchars($status9892); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9892); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9892); ?>; position:absolute; top:479px; left:688px;'>
</div>

  <!-- ASSET 9893 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9893; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:576px;" alt="Asset Image 9893" data-bs-toggle="modal" data-bs-target="#imageModal9893" onclick="fetchAssetData(9893);" data-room="<?php echo htmlspecialchars($room9893); ?>" data-floor="<?php echo htmlspecialchars($floor9893); ?>" data-image="<?php echo base64_encode($upload_img9893); ?>" data-category="<?php echo htmlspecialchars($category9893); ?>" data-status="<?php echo htmlspecialchars($status9893); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9893); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9893); ?>; position:absolute; top:479px; left:586px;'>
</div>

  <!-- ASSET 9894 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9894; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:678px;" alt="Asset Image 9894" data-bs-toggle="modal" data-bs-target="#imageModal9894" onclick="fetchAssetData(9894);" data-room="<?php echo htmlspecialchars($room9894); ?>" data-floor="<?php echo htmlspecialchars($floor9894); ?>" data-image="<?php echo base64_encode($upload_img9894); ?>" data-category="<?php echo htmlspecialchars($category9894); ?>" data-status="<?php echo htmlspecialchars($status9894); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9894); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9894); ?>; position:absolute; top:531px; left:688px;'>
</div>

  <!-- ASSET 9895 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9895; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:576px;" alt="Asset Image 9895" data-bs-toggle="modal" data-bs-target="#imageModal9895" onclick="fetchAssetData(9895);" data-room="<?php echo htmlspecialchars($room9895); ?>" data-floor="<?php echo htmlspecialchars($floor9895); ?>" data-image="<?php echo base64_encode($upload_img9895); ?>" data-category="<?php echo htmlspecialchars($category9895); ?>" data-status="<?php echo htmlspecialchars($status9895); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9895); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9895); ?>; position:absolute; top:531px; left:586px;'>
</div>


 <!-- ASSET 9896 ac-->
 <img src="../image.php?id=9896" class="asset-image" data-id="<?php echo $assetId9896; ?>" style="width:30px; cursor:pointer; position:absolute; top:494px; left:622px;" alt="Asset Image 9896" data-bs-toggle="modal" data-bs-target="#imageModal9896" onclick="fetchAssetData(9896);" data-room="<?php echo htmlspecialchars($room9896); ?>" data-floor="<?php echo htmlspecialchars($floor9896); ?>" data-image="<?php echo base64_encode($upload_img9896); ?>" data-category="<?php echo htmlspecialchars($category9896); ?>" data-status="<?php echo htmlspecialchars($status9896); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9896); ?>">
  <div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9896); ?>; position:absolute; top:494px; left:644px;'>
  </div>



<!-- ASSET 9890 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9890; ?>" style="width:15px; cursor:pointer; position:absolute; top:518px; left:603px; transform: rotate(270deg);" alt="Asset Image 9890" data-bs-toggle="modal" data-bs-target="#imageModal9890" onclick="fetchAssetData(9890);" data-room="<?php echo htmlspecialchars($room9890); ?>" data-floor="<?php echo htmlspecialchars($floor9890); ?>" data-image="<?php echo base64_encode($upload_img9890); ?>" data-category="<?php echo htmlspecialchars($category9890); ?>" data-status="<?php echo htmlspecialchars($status9890); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9890); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9890); ?>; position:absolute; top:520px; left:616px;'>
</div>

<!-- ASSET 9888 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9888; ?>" style="width:11px; cursor:pointer; position:absolute; top:537px; left:603px; transform: rotate(180deg);" alt="Asset Image 9888" data-bs-toggle="modal" data-bs-target="#imageModal9888" onclick="fetchAssetData(9888);" data-room="<?php echo htmlspecialchars($room9888); ?>" data-floor="<?php echo htmlspecialchars($floor9888); ?>" data-image="<?php echo base64_encode($upload_img9888); ?>" data-category="<?php echo htmlspecialchars($category9888); ?>" data-status="<?php echo htmlspecialchars($status9888); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9888); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9888); ?>; position:absolute; top:541px; left:599px;'>
</div>

<!-- ASSET 9885 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9885; ?>" style="width:15px; cursor:pointer; position:absolute; top:493px; left:664px; transform: rotate(180deg);" alt="Asset Image 9885" data-bs-toggle="modal" data-bs-target="#imageModal9885" onclick="fetchAssetData(9885);" data-room="<?php echo htmlspecialchars($room9885); ?>" data-floor="<?php echo htmlspecialchars($floor9885); ?>" data-image="<?php echo base64_encode($upload_img9885); ?>" data-category="<?php echo htmlspecialchars($category9885); ?>" data-status="<?php echo htmlspecialchars($status9885); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9885); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9885); ?>; position:absolute; top:489px; left:660px;'>
</div>

<!-- ASSET 9887 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9887; ?>" style="width:11px; cursor:pointer; position:absolute; top:502px; left:677px; transform: rotate(90deg);" alt="Asset Image 9887" data-bs-toggle="modal" data-bs-target="#imageModal9887" onclick="fetchAssetData(9887);" data-room="<?php echo htmlspecialchars($room9887); ?>" data-floor="<?php echo htmlspecialchars($floor9887); ?>" data-image="<?php echo base64_encode($upload_img9887); ?>" data-category="<?php echo htmlspecialchars($category9887); ?>" data-status="<?php echo htmlspecialchars($status9887); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9887); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9887); ?>; position:absolute; top:508px; left:683px;'>
</div>







 <!-- ASSET 9901 light -->
 <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9901; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:805px;" alt="Asset Image 9901" data-bs-toggle="modal" data-bs-target="#imageModal9901" onclick="fetchAssetData(9901);" data-room="<?php echo htmlspecialchars($room9901); ?>" data-floor="<?php echo htmlspecialchars($floor9901); ?>" data-image="<?php echo base64_encode($upload_img9901); ?>" data-category="<?php echo htmlspecialchars($category9901); ?>" data-status="<?php echo htmlspecialchars($status9901); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9901); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9901); ?>; position:absolute; top:479px; left:815px;'>
</div>

  <!-- ASSET 9902 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9902; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:703px;" alt="Asset Image 9902" data-bs-toggle="modal" data-bs-target="#imageModal9902" onclick="fetchAssetData(9902);" data-room="<?php echo htmlspecialchars($room9902); ?>" data-floor="<?php echo htmlspecialchars($floor9902); ?>" data-image="<?php echo base64_encode($upload_img9902); ?>" data-category="<?php echo htmlspecialchars($category9902); ?>" data-status="<?php echo htmlspecialchars($status9902); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9902); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9902); ?>; position:absolute; top:479px; left:713px;'>
</div>

  <!-- ASSET 9903 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9903; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:805px;" alt="Asset Image 9903" data-bs-toggle="modal" data-bs-target="#imageModal9903" onclick="fetchAssetData(9903);" data-room="<?php echo htmlspecialchars($room9903); ?>" data-floor="<?php echo htmlspecialchars($floor9903); ?>" data-image="<?php echo base64_encode($upload_img9903); ?>" data-category="<?php echo htmlspecialchars($category9903); ?>" data-status="<?php echo htmlspecialchars($status9903); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9903); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9903); ?>; position:absolute; top:531px; left:815px;'>
</div>

  <!-- ASSET 9904 light -->
  <img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9904; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:703px;" alt="Asset Image 9904" data-bs-toggle="modal" data-bs-target="#imageModal9904" onclick="fetchAssetData(9904);" data-room="<?php echo htmlspecialchars($room9904); ?>" data-floor="<?php echo htmlspecialchars($floor9904); ?>" data-image="<?php echo base64_encode($upload_img9904); ?>" data-category="<?php echo htmlspecialchars($category9904); ?>" data-status="<?php echo htmlspecialchars($status9904); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9904); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9904); ?>; position:absolute; top:531px; left:713px;'>
</div>



<!-- ASSET 9905 ac-->
<img src="../image.php?id=9905" class="asset-image" data-id="<?php echo $assetId9905; ?>" style="width:30px; cursor:pointer; position:absolute; top:494px; left:749px;" alt="Asset Image 9905" data-bs-toggle="modal" data-bs-target="#imageModal9905" onclick="fetchAssetData(9905);" data-room="<?php echo htmlspecialchars($room9905); ?>" data-floor="<?php echo htmlspecialchars($floor9905); ?>" data-image="<?php echo base64_encode($upload_img9905); ?>" data-category="<?php echo htmlspecialchars($category9905); ?>" data-status="<?php echo htmlspecialchars($status9905); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9905); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9905); ?>; position:absolute; top:494px; left:771px;'>
</div>

<!-- ASSET 9906 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9906; ?>" style="width:15px; cursor:pointer; position:absolute; top:518px; left:730px; transform: rotate(270deg);" alt="Asset Image 9906" data-bs-toggle="modal" data-bs-target="#imageModal9906" onclick="fetchAssetData(9906);" data-room="<?php echo htmlspecialchars($room9906); ?>" data-floor="<?php echo htmlspecialchars($floor9906); ?>" data-image="<?php echo base64_encode($upload_img9906); ?>" data-category="<?php echo htmlspecialchars($category9906); ?>" data-status="<?php echo htmlspecialchars($status9906); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9906); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9906); ?>; position:absolute; top:520px; left:743px;'>
</div>

<!-- ASSET 9908 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9908; ?>" style="width:11px; cursor:pointer; position:absolute; top:537px; left:730px; transform: rotate(180deg);" alt="Asset Image 9908" data-bs-toggle="modal" data-bs-target="#imageModal9908" onclick="fetchAssetData(9908);" data-room="<?php echo htmlspecialchars($room9908); ?>" data-floor="<?php echo htmlspecialchars($floor9908); ?>" data-image="<?php echo base64_encode($upload_img9908); ?>" data-category="<?php echo htmlspecialchars($category9908); ?>" data-status="<?php echo htmlspecialchars($status9908); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9908); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9908); ?>; position:absolute; top:541px; left:726px;'>
</div>

<!-- ASSET 9908 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9908; ?>" style="width:15px; cursor:pointer; position:absolute; top:493px; left:791px; transform: rotate(180deg);" alt="Asset Image 9908" data-bs-toggle="modal" data-bs-target="#imageModal9908" onclick="fetchAssetData(9908);" data-room="<?php echo htmlspecialchars($room9908); ?>" data-floor="<?php echo htmlspecialchars($floor9908); ?>" data-image="<?php echo base64_encode($upload_img9908); ?>" data-category="<?php echo htmlspecialchars($category9908); ?>" data-status="<?php echo htmlspecialchars($status9908); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9908); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9908); ?>; position:absolute; top:489px; left:787px;'>
</div>

<!-- ASSET 9909 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9909; ?>" style="width:11px; cursor:pointer; position:absolute; top:502px; left:804px; transform: rotate(90deg);" alt="Asset Image 9909" data-bs-toggle="modal" data-bs-target="#imageModal9909" onclick="fetchAssetData(9909);" data-room="<?php echo htmlspecialchars($room9909); ?>" data-floor="<?php echo htmlspecialchars($floor9909); ?>" data-image="<?php echo base64_encode($upload_img9909); ?>" data-category="<?php echo htmlspecialchars($category9909); ?>" data-status="<?php echo htmlspecialchars($status9909); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9909); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9909); ?>; position:absolute; top:508px; left:810px;'>
</div>


<!-- ASSET 9913 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9913; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:932px;" alt="Asset Image 9913" data-bs-toggle="modal" data-bs-target="#imageModal9913" onclick="fetchAssetData(9913);" data-room="<?php echo htmlspecialchars($room9913); ?>" data-floor="<?php echo htmlspecialchars($floor9913); ?>" data-image="<?php echo base64_encode($upload_img9913); ?>" data-category="<?php echo htmlspecialchars($category9913); ?>" data-status="<?php echo htmlspecialchars($status9913); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9913); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9913); ?>; position:absolute; top:479px; left:942px;'>
</div>

<!-- ASSET 9914 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9914; ?>" style="width:15px; cursor:pointer; position:absolute; top:479px; left:830px;" alt="Asset Image 9914" data-bs-toggle="modal" data-bs-target="#imageModal9914" onclick="fetchAssetData(9914);" data-room="<?php echo htmlspecialchars($room9914); ?>" data-floor="<?php echo htmlspecialchars($floor9914); ?>" data-image="<?php echo base64_encode($upload_img9914); ?>" data-category="<?php echo htmlspecialchars($category9914); ?>" data-status="<?php echo htmlspecialchars($status9914); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9914); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9914); ?>; position:absolute; top:479px; left:840px;'>
</div>

<!-- ASSET 9915 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9915; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:932px;" alt="Asset Image 9915" data-bs-toggle="modal" data-bs-target="#imageModal9915" onclick="fetchAssetData(9915);" data-room="<?php echo htmlspecialchars($room9915); ?>" data-floor="<?php echo htmlspecialchars($floor9915); ?>" data-image="<?php echo base64_encode($upload_img9915); ?>" data-category="<?php echo htmlspecialchars($category9915); ?>" data-status="<?php echo htmlspecialchars($status9915); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9915); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9915); ?>; position:absolute; top:531px; left:942px;'>
</div>

<!-- ASSET 9916 light -->
<img src="../image.php?id=9724" class="asset-image" data-id="<?php echo $assetId9916; ?>" style="width:15px; cursor:pointer; position:absolute; top:531px; left:820px;" alt="Asset Image 9916" data-bs-toggle="modal" data-bs-target="#imageModal9916" onclick="fetchAssetData(9916);" data-room="<?php echo htmlspecialchars($room9916); ?>" data-floor="<?php echo htmlspecialchars($floor9916); ?>" data-image="<?php echo base64_encode($upload_img9916); ?>" data-category="<?php echo htmlspecialchars($category9916); ?>" data-status="<?php echo htmlspecialchars($status9916); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9916); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9916); ?>; position:absolute; top:531px; left:830px;'>
</div>

<!-- ASSET 9917 ac-->
<img src="../image.php?id=9917" class="asset-image" data-id="<?php echo $assetId9917; ?>" style="width:30px; cursor:pointer; position:absolute; top:494px; left:874px;" alt="Asset Image 9917" data-bs-toggle="modal" data-bs-target="#imageModal9917" onclick="fetchAssetData(9917);" data-room="<?php echo htmlspecialchars($room9917); ?>" data-floor="<?php echo htmlspecialchars($floor9917); ?>" data-image="<?php echo base64_encode($upload_img9917); ?>" data-category="<?php echo htmlspecialchars($category9917); ?>" data-status="<?php echo htmlspecialchars($status9917); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9917); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9917); ?>; position:absolute; top:494px; left:896px;'>
</div>

<!-- ASSET 9918 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9918; ?>" style="width:15px; cursor:pointer; position:absolute; top:518px; left:858px; transform: rotate(270deg);" alt="Asset Image 9918" data-bs-toggle="modal" data-bs-target="#imageModal9918" onclick="fetchAssetData(9918);" data-room="<?php echo htmlspecialchars($room9918); ?>" data-floor="<?php echo htmlspecialchars($floor9918); ?>" data-image="<?php echo base64_encode($upload_img9918); ?>" data-category="<?php echo htmlspecialchars($category9918); ?>" data-status="<?php echo htmlspecialchars($status9918); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9918); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9918); ?>; position:absolute; top:520px; left:851px;'>
</div>

<!-- ASSET 9910 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9910; ?>" style="width:11px; cursor:pointer; position:absolute; top:537px; left:858px; transform: rotate(180deg);" alt="Asset Image 9910" data-bs-toggle="modal" data-bs-target="#imageModal9910" onclick="fetchAssetData(9910);" data-room="<?php echo htmlspecialchars($room9910); ?>" data-floor="<?php echo htmlspecialchars($floor9910); ?>" data-image="<?php echo base64_encode($upload_img9910); ?>" data-category="<?php echo htmlspecialchars($category9910); ?>" data-status="<?php echo htmlspecialchars($status9910); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9910); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9910); ?>; position:absolute; top:541px; left:854px;'>
</div>

<!-- ASSET 9919 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9919; ?>" style="width:15px; cursor:pointer; position:absolute; top:518px; left:884px; transform: rotate(270deg);" alt="Asset Image 9919" data-bs-toggle="modal" data-bs-target="#imageModal9919" onclick="fetchAssetData(9919);" data-room="<?php echo htmlspecialchars($room9919); ?>" data-floor="<?php echo htmlspecialchars($floor9919); ?>" data-image="<?php echo base64_encode($upload_img9919); ?>" data-category="<?php echo htmlspecialchars($category9919); ?>" data-status="<?php echo htmlspecialchars($status9919); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9919); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9919); ?>; position:absolute; top:520px; left:898px;'>
</div>

<!-- ASSET 9911 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9911; ?>" style="width:11px; cursor:pointer; position:absolute; top:537px; left:884px; transform: rotate(180deg);" alt="Asset Image 9911" data-bs-toggle="modal" data-bs-target="#imageModal9911" onclick="fetchAssetData(9911);" data-room="<?php echo htmlspecialchars($room9911); ?>" data-floor="<?php echo htmlspecialchars($floor9911); ?>" data-image="<?php echo base64_encode($upload_img9911); ?>" data-category="<?php echo htmlspecialchars($category9911); ?>" data-status="<?php echo htmlspecialchars($status9911); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9911); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9911); ?>; position:absolute; top:541px; left:883px;'>
</div>

<!-- ASSET 9920 desk -->
<img src="../image.php?id=9761" class="asset-image" data-id="<?php echo $assetId9920; ?>" style="width:15px; cursor:pointer; position:absolute; top:492px; left:841px;" alt="Asset Image 9920" data-bs-toggle="modal" data-bs-target="#imageModal9920" onclick="fetchAssetData(9920);" data-room="<?php echo htmlspecialchars($room9920); ?>" data-floor="<?php echo htmlspecialchars($floor9920); ?>" data-image="<?php echo base64_encode($upload_img9920); ?>" data-category="<?php echo htmlspecialchars($category9920); ?>" data-status="<?php echo htmlspecialchars($status9920); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9920); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9920); ?>; position:absolute; top:489px; left:852px;'>
</div>

<!-- ASSET 9912 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9912; ?>" style="width:11px; cursor:pointer; position:absolute; top:497px; left:832px; transform: rotate(270deg);" alt="Asset Image 9912" data-bs-toggle="modal" data-bs-target="#imageModal9912" onclick="fetchAssetData(9912);" data-room="<?php echo htmlspecialchars($room9912); ?>" data-floor="<?php echo htmlspecialchars($floor9912); ?>" data-image="<?php echo base64_encode($upload_img9912); ?>" data-category="<?php echo htmlspecialchars($category9912); ?>" data-status="<?php echo htmlspecialchars($status9912); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9912); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9912); ?>; position:absolute; top:541px; left:831px;'>
</div>
<!-- ASSET 9923 Sofa -->
<img src="../image.php?id=9923" class="asset-image" data-id="<?php echo $assetId9923; ?>" style="width:50px; cursor:pointer; position:absolute; top:456px; left:973px;" alt="Asset Image 9923" data-bs-toggle="modal" data-bs-target="#imageModal9923" onclick="fetchAssetData(9923);" data-room="<?php echo htmlspecialchars($room9923); ?>" data-floor="<?php echo htmlspecialchars($floor9923); ?>" data-image="<?php echo base64_encode($upload_img9923); ?>" data-category="<?php echo htmlspecialchars($category9923); ?>" data-status="<?php echo htmlspecialchars($status9923); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9923); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9923); ?>; position:absolute; top:454px; left:1017px;'>
</div>
<!-- ASSET 9917 ac-->
<img src="../image.php?id=9917" class="asset-image" data-id="<?php echo $assetId9917; ?>" style="width:30px; cursor:pointer; position:absolute; top:485px; left:1006px;" alt="Asset Image 9917" data-bs-toggle="modal" data-bs-target="#imageModal9917" onclick="fetchAssetData(9917);" data-room="<?php echo htmlspecialchars($room9917); ?>" data-floor="<?php echo htmlspecialchars($floor9917); ?>" data-image="<?php echo base64_encode($upload_img9917); ?>" data-category="<?php echo htmlspecialchars($category9917); ?>" data-status="<?php echo htmlspecialchars($status9917); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9917); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9917); ?>; position:absolute; top:485px; left:1028px;'>
</div>
<!-- ASSET 9924 ac-->
<img src="../image.php?id=9924" class="asset-image" data-id="<?php echo $assetId9924; ?>" style="width:30px; cursor:pointer; position:absolute; top:485px; left:1006px;" alt="Asset Image 9924" data-bs-toggle="modal" data-bs-target="#imageModal9924" onclick="fetchAssetData(9924);" data-room="<?php echo htmlspecialchars($room9924); ?>" data-floor="<?php echo htmlspecialchars($floor9924); ?>" data-image="<?php echo base64_encode($upload_img9924); ?>" data-category="<?php echo htmlspecialchars($category9924); ?>" data-status="<?php echo htmlspecialchars($status9924); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9924); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9924); ?>; position:absolute; top:485px; left:1028px;'>
</div>
<!-- ASSET 9930 desk -->
<img src="../image.php?id=9930" class="asset-image" data-id="<?php echo $assetId9930; ?>" style="width:15px; cursor:pointer; position:absolute; top:472px; left:1050px;" alt="Asset Image 9930" data-bs-toggle="modal" data-bs-target="#imageModal9930" onclick="fetchAssetData(9930);" data-room="<?php echo htmlspecialchars($room9930); ?>" data-floor="<?php echo htmlspecialchars($floor9930); ?>" data-image="<?php echo base64_encode($upload_img9930); ?>" data-category="<?php echo htmlspecialchars($category9930); ?>" data-status="<?php echo htmlspecialchars($status9930); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9930); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9930); ?>; position:absolute; top:469px; left:1047px;'>
</div>

<!-- ASSET 9926 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9926; ?>" style="width:11px; cursor:pointer; position:absolute; top:482px; left:1062px; transform: rotate(90deg);" alt="Asset Image 9926" data-bs-toggle="modal" data-bs-target="#imageModal9926" onclick="fetchAssetData(9926);" data-room="<?php echo htmlspecialchars($room9926); ?>" data-floor="<?php echo htmlspecialchars($floor9926); ?>" data-image="<?php echo base64_encode($upload_img9926); ?>" data-category="<?php echo htmlspecialchars($category9926); ?>" data-status="<?php echo htmlspecialchars($status9926); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9926); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9926); ?>; position:absolute; top:478px; left:1068px;'>
</div>


<!-- ASSET 9931 desk -->
<img src="../image.php?id=9931" class="asset-image" data-id="<?php echo $assetId9931; ?>" style="width:15px; cursor:pointer; position:absolute; top:506px; left:1048px; transform: rotate(360deg);" alt="Asset Image 9931" data-bs-toggle="modal" data-bs-target="#imageModal9931" onclick="fetchAssetData(9931);" data-room="<?php echo htmlspecialchars($room9931); ?>" data-floor="<?php echo htmlspecialchars($floor9931); ?>" data-image="<?php echo base64_encode($upload_img9931); ?>" data-category="<?php echo htmlspecialchars($category9931); ?>" data-status="<?php echo htmlspecialchars($status9931); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9931); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9931); ?>; position:absolute; top:502px; left:1058px;'>
</div>







<!-- ASSET 9927 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9927; ?>" style="width:11px; cursor:pointer; position:absolute; top:520px; left:1062px; transform: rotate(138deg);" alt="Asset Image 9927" data-bs-toggle="modal" data-bs-target="#imageModal9927" onclick="fetchAssetData(9927);" data-room="<?php echo htmlspecialchars($room9927); ?>" data-floor="<?php echo htmlspecialchars($floor9927); ?>" data-image="<?php echo base64_encode($upload_img9927); ?>" data-category="<?php echo htmlspecialchars($category9927); ?>" data-status="<?php echo htmlspecialchars($status9927); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9927); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9927); ?>; position:absolute; top:520px; left:1070px;'>
</div>

<!-- ASSET 9928 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9928; ?>" style="width:11px; cursor:pointer; position:absolute; top:507px; left:1038px;" alt="Asset Image 9928" data-bs-toggle="modal" data-bs-target="#imageModal9928" onclick="fetchAssetData(9928);" data-room="<?php echo htmlspecialchars($room9928); ?>" data-floor="<?php echo htmlspecialchars($floor9928); ?>" data-image="<?php echo base64_encode($upload_img9928); ?>" data-category="<?php echo htmlspecialchars($category9928); ?>" data-status="<?php echo htmlspecialchars($status9928); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9928); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9928); ?>; position:absolute; top:528px; left:1034px;'>
</div>


<!-- ASSET 9929 chair -->
<img src="../image.php?id=9776" class="asset-image" data-id="<?php echo $assetId9929; ?>" style="width:11px; cursor:pointer; position:absolute; top:523px; left:1038px; transform: rotate(180deg);" alt="Asset Image 9929" data-bs-toggle="modal" data-bs-target="#imageModal9929" onclick="fetchAssetData(9929);" data-room="<?php echo htmlspecialchars($room9929); ?>" data-floor="<?php echo htmlspecialchars($floor9929); ?>" data-image="<?php echo base64_encode($upload_img9929); ?>" data-category="<?php echo htmlspecialchars($category9929); ?>" data-status="<?php echo htmlspecialchars($status9929); ?>" data-assignedname="<?php echo htmlspecialchars($assignedName9929); ?>">
<div style='width:8px; height:8px; border-radius:50%; background-color: <?php echo getStatusColor($status9929); ?>; position:absolute; top:502px; left:1040px;'>
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