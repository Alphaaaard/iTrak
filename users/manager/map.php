<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {

    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 2) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
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
   WHERE al.m_seen= '0' AND al.accountID != ?  AND action NOT LIKE '%logged in'
   ORDER BY al.date DESC 
   LIMIT 5"; // Set limit to 5

// Prepare the SQL statement
$stmtLatestLogs = $conn->prepare($sqlLatestLogs);

// Bind the parameter to exclude the current user's account ID
$stmtLatestLogs->bind_param("i", $loggedInAccountId);

// Execute the query
$stmtLatestLogs->execute();
$resultLatestLogs = $stmtLatestLogs->get_result();


$unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE m_seen= '0' AND action NOT LIKE '%logged in' AND accountID != ?";
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
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/map.css" />
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
        <!-- NAVBAR -->
        <div id="navbar" class="">
            <nav>
                <div class="hamburger">
                    <i class="bi bi-list"></i>
                    <a href="#" class="brand" title="logo">
                        <!-- <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i> -->
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
                                // Check the connection
                                if ($conn->connect_error) {
                                    die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
                                }

                                // Fetch the user's picture URL from the database
                                $userId = $_SESSION['accountId'];
                                $query = "SELECT picture FROM account WHERE accountId = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('i', $userId);
                                $stmt->execute();
                                $stmt->store_result();

                                if ($stmt->num_rows > 0) {
                                    $stmt->bind_result($userPicture);
                                    $stmt->fetch();

                                    // Display the user profile image
                                    echo "<img src='data:image/jpeg;base64," . base64_encode($userPicture) . "' title='profile-picture' />";
                                } else {
                                    // Fallback to displaying the user's first name
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
        <!-- NAVBAR -->
        <!-- SIDEBAR -->
        <section id="sidebar">
            <div href="#" class="brand" title="logo">
                <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i>
                <div class="mobile-sidebar-close">
                    <i class="bi bi-arrow-left-circle"></i>
                </div>
            </div>
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
                    <a href="./gps.php">
                        <i class="bi bi-geo-alt"></i>
                        <span class="text">GPS</span>
                    </a>
                </li>
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
                <li>
                    <a href="./activity-logs.php">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="text">Activity Logs</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- SIDEBAR -->

        <!-- CONTENT -->
        <section id="content">
            <!-- MAIN -->

            <main>
                <div class="content-container" id="content-container">

                    <!-- <header>
                        <div class="cont-header">
                            <h1 class="tab-name">3D Map</h1>
                        </div>
                    </header> -->

                    <div id="model-container" class="content"></div>

                    <!-- Mobile View Building Selection -->
                    <div class="buildings" id="buildings" style="visibility:hidden">
                        <div class="building building1" style="display: none;">Floor</div>
                        <div class="building building2">TechVoc</div>
                        <div class="building building3">Old Academic</div>
                        <div class="building building4">Belmonte</div>
                        <div class="building building5">KORPHIL</div>
                        <div class="building building6">Ballroom</div>
                        <div class="building building7">Multipurpose</div>
                        <div class="building building8">Admin</div>
                        <div class="building building9">Bautista</div>
                        <div class="building building10">Academic</div>
                    </div>

                    <!-- MODAL 1 -->
                    <div id="myModal1" class="modal">
                        <div class="modal-content">
                            <div id="modalContent1">
                            </div>
                            <span class="close" id="closeModal1"><i class="bi bi-x-lg"></i></span>
                        </div>
                    </div>

                    <!-- MODAL 2 -->
                    <div id="myModal2" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal2"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>TechVoc Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="techvoc-floor1-tab" href="../building-manager/TEB/TEBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="techvoc-floor2-tab" href="../building-manager/TEB/TEBF2.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 3 -->
                    <div id="myModal3" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal3"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Old Academic Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="old-floor1-tab" href="../building-manager/OLB/OLBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="old-floor2-tab" href="../building-manager/OLB/OLBF1.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 4 -->
                    <div id="myModal4" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal4"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Belmonte Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="belmonte-floor1-tab" href="../building-manager/BEB/BEBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="belmonte-floor2-tab" href="../building-manager/BEB/BEBF2.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="belmonte-floor3-tab" href="../building-manager/BEB/BEBF3.php" role="tab" aria-controls="floor3" aria-selected="false">3</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="belmonte-floor4-tab" href="../building-manager/BEB/BEBF4.php" role="tab" aria-controls="floor4" aria-selected="false">4</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 5 -->
                    <div id="myModal5" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal5"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>KorPhil Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="korphil-floor1-tab" href="../building-manager/KOB/KOBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="korphil-floor2-tab" href="../building-manager/KOB/KOBF2.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="korphil-floor3-tab" href="../building-manager/KOB/KOBF3.php" role="tab" aria-controls="floor3" aria-selected="false">3</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 6 -->
                    <div id="myModal6" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal6"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Ballroom Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="ballroom-floor1-tab" href="../building-manager/CHB/CHBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 7 -->
                    <div id="myModal7" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal7"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Multipurpose Building </h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="multipurpose-floor1-tab" href="../building-manager/MUB/MUBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 8 -->
                    <div id="myModal8" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal8"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Admin Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="admin-floor1-tab" href="../building-manager/ADB/ADBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="admin-floor2-tab" href="../building-manager/ADB/ADBF2.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="admin-floor3-tab" href="../building-manager/ADB/ADBF3.php" role="tab" aria-controls="floor3" aria-selected="false">3</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="admin-floor4-tab" href="../building-manager/ADB/ADBF4.php" role="tab" aria-controls="floor4" aria-selected="false">4</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 9 -->
                    <div id="myModal9" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal9"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Bautista Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor1-tab" href="../building-manager/BAB/BABF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class=" nav-link" id="bautista-floor2-tab" href="../building-manager/BAB/BABF2.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor3-tab" href="../building-manager/BAB/BABF3.php" role="tab" aria-controls="floor3" aria-selected="false">3</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor4-tab" href="../building-manager/BAB/BABF4.php" role="tab" aria-controls="floor4" aria-selected="false">4</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor5-tab" href="../building-manager/BAB/BABF5.php" role="tab" aria-controls="floor5" aria-selected="false">5</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor6-tab" href="../building-manager/BAB/BABF6.php" role="tab" aria-controls="floor6" aria-selected="false">6</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor7-tab" href="../building-manager/BAB/BABF7.php" role="tab" aria-controls="floor7" aria-selected="false">7</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="bautista-floor8-tab" href="../building-manager/BAB/BABF8.php" role="tab" aria-controls="floor8" aria-selected="false">8</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 10 -->
                    <div id="myModal10" class="modal">
                        <div class="modal-content">
                            <div class="modal-header"> <span class="close" id="closeModal10"><i class="bi bi-x-lg"></i></span>
                                <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                    <h3>Academic Building</h3>
                            </div>
                            <div class="nav-container">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor1-tab" href="../building-manager/NEB/NEWBF1.php" role="tab" aria-controls="floor1" aria-selected="true">1</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor2-tab" href="../building-manager/NEB/NEWBF2.php" role="tab" aria-controls="floor2" aria-selected="false">2</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor3-tab" href="../building-manager/NEB/NEWBF3.php" role="tab" aria-controls="floor3" aria-selected="false">3</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor4-tab" href="../building-manager/NEB/NEWBF4.php" role="tab" aria-controls="floor4" aria-selected="false">4</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor5-tab" href="../building-manager/NEB/NEWBF5.php" role="tab" aria-controls="floor4" aria-selected="false">5</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor6-tab" href="../building-manager/NEB/NEWBF6.php" role="tab" aria-controls="floor4" aria-selected="false">6</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="academic-floor7-tab" href="../building-manager/NEB/NEWBF7.php" role="tab" aria-controls="floor4" aria-selected="false">7</a>
                                </li>
                            </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODALS-->
                    <div id="modalTemplate1" style="display: none">
                        <h2 id="modalTitle1"></h2>
                        <p id="modalDescription1"></p>
                    </div>

                    <div id="modalTemplate2" style="display: none">
                        <h2 id="modalTitle2"></h2>
                        <p id="modalDescription2"></p>
                    </div>

                    <div id="modalTemplate3" style="display: none">
                        <h2 id="modalTitle3"></h2>
                        <p id="modalDescription3"></p>
                    </div>

                    <div id="modalTemplate4" style="display: none">
                        <h2 id="modalTitle4"></h2>
                        <p id="modalDescription4"></p>
                    </div>

                    <div id="modalTemplate5" style="display: none">
                        <h2 id="modalTitle5"></h2>
                        <p id="modalDescription5"></p>
                    </div>

                    <div id="modalTemplate6" style="display: none">
                        <h2 id="modalTitle6"></h2>
                        <p id="modalDescription6"></p>
                    </div>

                    <div id="modalTemplate7" style="display: none">
                        <h2 id="modalTitle7"></h2>
                        <p id="modalDescription7"></p>
                    </div>

                    <div id="modalTemplate8" style="display: none">
                        <h2 id="modalTitle8"></h2>
                        <p id="modalDescription8"></p>
                    </div>

                    <div id="modalTemplate9" style="display: none">
                        <h2 id="modalTitle9"></h2>
                        <p id="modalDescription9"></p>
                    </div>

                    <div id="modalTemplate10" style="display: none">
                        <h2 id="modalTitle10"></h2>
                        <p id="modalDescription10"></p>
                    </div>
                </div>
            </main>
        </section>

        <!-- MODALS -->
        <!-- commented some code in map.css (related to modals, it affects profile modal) -->
        <?php include_once 'modals/modal_layout.php'; ?>

        <!-- RFID MODAL -->
        <div class="modal" id="staticBackdrop112" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <img src="../../src/img/taprfid.jpg" width="100%" alt="" class="Scan" />

                        <form id="rfidForm">
                            <input type="text" id="rfid" name="rfid" value="">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="../../src/js/main.js"></script>
        <script type="module" src="../../src/js/map.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="../../src/js/profileModalController.js"></script>


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


    </body>

    </html>