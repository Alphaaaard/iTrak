<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila');
if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if($_SESSION['userLevel'] != 3) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }
    



// Fetch Report activity logs
$loggedInUserFirstName = $_SESSION['firstName']; // or the name field you have in session that you want to check against
$loggedInUsermiddleName = $_SESSION['middleName']; // assuming you also have the last name in the session
$loggedInUserLastName = $_SESSION['lastName']; //kung ano ung naka declare dito eto lang ung magiging data 
// Concatenate first name and last name for the action field check
$loggedInFullName = $loggedInUserFirstName . " " . $loggedInUsermiddleName . " " . $loggedInUserLastName; //kung ano ung naka declare dito eto lang ung magiging data 

// Adjust the SQL to check the 'action' field for the logged-in user's name
$sqlReport = "SELECT ac.*, a.firstName, a.middleName, a.lastName
FROM activitylogs AS ac
LEFT JOIN account AS a ON ac.accountID = a.accountID
WHERE ac.tab='Report' AND ac.action LIKE ?
ORDER BY ac.date DESC";

// Prepare the SQL statement
$stmt = $conn->prepare($sqlReport);

// Create a wildcard search term for the name
$searchTerm = "%" . $loggedInFullName . "%";

// Bind the parameter and execute
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$resultReport = $stmt->get_result();


// for notif below
// Update the SQL to join with the account and asset tables to get the admin's name and asset information
$loggedInUserFirstName = $_SESSION['firstName']; 
$loggedInUserMiddleName = $_SESSION['middleName']; // Get the middle name from the session
$loggedInUserLastName = $_SESSION['lastName'];

$loggedInFullName = $loggedInUserFirstName . ' '.$loggedInUserMiddleName .' '. $loggedInUserLastName;



// Adjust the SQL to fetch only the notifications for the logged-in user
$sqlLatestLogs = "SELECT al.*, acc.firstName AS adminFirstName, acc.middleName AS adminMiddleName, acc.lastName AS adminLastName
              FROM activitylogs AS al
              JOIN account AS acc ON al.accountID = acc.accountID
              WHERE al.tab='Report' 
              AND al.seen = '0' AND al.action LIKE ?
              ORDER BY al.date DESC 
              LIMIT 1000";

// Prepare the SQL statement
$stmtLatestLogs = $conn->prepare($sqlLatestLogs);

// Create a wildcard search term for the logged-in user's full name
$searchTerm = "%Assigned maintenance personnel " . $loggedInFullName . "%";

// Bind the parameter and execute
$stmtLatestLogs->bind_param("s", $searchTerm);
$stmtLatestLogs->execute();
$resultLatestLogs = $stmtLatestLogs->get_result();

$unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE seen = '3'";
$result = $conn->query($unseenCountQuery);
$unseenCountRow = $result->fetch_assoc();

$unseenCount = $unseenCountRow['unseenCount'];
if (isset($_SESSION['accountId'])) {
    $accountId = $_SESSION['accountId'];
    $todayDate = date("Y-m-d");

    // Check if there's a timeout value for this user for today
    $timeoutQuery = "SELECT timeout FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
    $timeoutResult = $conn->query($timeoutQuery);
    $timeoutRow = $timeoutResult->fetch_assoc();

    if ($timeoutRow && $timeoutRow['timeout'] !== null) {
        // User has a timeout value, force logout
        session_destroy(); // Destroy all session data
        header("Location: ../../index.php?logout=timeout"); // Redirect to the login page with a timeout flag
        exit;
    }



}
?>

    <!DOCTYPE html>
    
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Map</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/map.css" />
    </head>

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
<span id="noti_number"><?php echo $unseenCount; ?></span>

    </td>
    </tr>
    </table>
    <script type="text/javascript">
        function loadDoc() {


            setInterval(function() {

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("noti_number").innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "update_single_notification.php", true);
                xhttp.send();

            }, 10);


        }
        loadDoc();
    </script>

</a>



<div class="dropdown-content" id="notification-dropdown-content">
    <h6 class="dropdown-header">Alerts Center</h6>
    <!-- PHP code to display notifications will go here -->
    <?php
if ($resultLatestLogs && $resultLatestLogs->num_rows > 0) {
// Loop through each notification
while ($row = $resultLatestLogs->fetch_assoc()) {
$adminName = $row["adminFirstName"] . ' ' . $row["adminLastName"];
$actionText = $row["action"];
$assetId = 'unknown'; // Default value

// Extract personnel name and asset ID from action text
if (preg_match('/Assigned maintenance personnel (.*?) to asset ID (\d+)/', $actionText, $matches)) {
$assignedName = $matches[1];
$assetId = $matches[2];
}

// Generate the notification text
// Generate the notification text including the name of the assigned personnel
$notificationText = "Admin $adminName assigned $assignedName to asset ID " . htmlspecialchars($assetId);


// Output the notification as a clickable element with a data attribute for the activityId
echo '<a href="#" class="notification-item" data-activity-id="' . $row["activityId"] . '">' . $notificationText . '</a>';
}
} else {
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
            <li >
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
             
                <li class="active">
                    <a href="./map.php">
                        <i class="bi bi-map"></i>
                        <span class="text">Map</span>
                    </a>
                </li>
                <li>
                    <a href="./assigned-tasks.php">
                        <i class="bi bi-geo-alt"></i>
                        <span class="text">Assigned Tasks</span>
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
                        <div class="building building5">Metalcasting</div>
                        <div class="building building6">KORPHIL</div>
                        <div class="building building7">Multipurpose</div>
                        <div class="building building8">Admin</div>
                        <div class="building building9">Bautista</div>
                        <div class="building building10">Academic</div>
                        <div class="building building11">Ballroom</div>
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
                            <span class="close" id="closeModal2"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>TECHVOC</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="techvoc-floor1-tab" href="../building/TEB/TEBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="techvoc-floor2-tab" href="../building/TEB/TEBF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 3 -->
                    <div id="myModal3" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal3"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>OLD ACADEMIC BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="old-floor1-tab" href="../building/OLB/OLBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="old-floor2-tab" href="../building/OLB/OLBF1.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 4 -->
                    <div id="myModal4" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal4"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>BELMONTE BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="belmonte-floor1-tab" href="../building/BEB/BEBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="belmonte-floor2-tab" href="../building/BEB/BEBF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="belmonte-floor3-tab" href="../building/BEB/BEBF3.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="belmonte-floor4-tab" href="../building/BEB/BEBF4.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 5 -->
                    <div id="myModal5" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal5"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>KORPHIL CASTING BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="korphil-floor1-tab" href="../building/KOB/KOBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="korphil-floor2-tab" href="../building/KOB/KOBF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="korphil-floor3-tab" href="../building/KOB/KOBF3.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="korphil-floor4-tab" href="../building/KOB/KOBF4.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 6 -->
                    <div id="myModal6" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal6"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>BALLROOM BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="ballroom-floor1-tab" href="../building/CHB/CHBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="ballroom-floor2-tab" href="../building/CHB/CHBF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="ballroom-floor3-tab" href="../building/CHB/CHBF3.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="ballroom-floor4-tab" href="../building/CHB/CHBF4.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 7 -->
                    <div id="myModal7" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal7"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>MULTIPURPOSE CASTING BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="multipurpose-floor1-tab" href="../building/MUB/MUBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="multipurpose-floor2-tab" href="../building/MUB/MUBF1.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="multipurpose-floor3-tab" href="../building/MUB/MUBF1.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="multipurpose-floor4-tab" href="../building/MUB/MUBF1.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 8 -->
                    <div id="myModal8" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal8"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>ADMIN BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="admin-floor1-tab" href="../building/ADB/ADBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="admin-floor2-tab" href="../building/ADB/ADBF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="admin-floor3-tab" href="../building/ADB/ADBF3.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="admin-floor4-tab" href="../building/ADB/ADBF4.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="admin-floor5-tab" href="../building/ADB/ADBF5.php" role="tab" aria-controls="floor4" aria-selected="false">Floor5</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 9 -->
                    <div id="myModal9" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal9"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>BAUTISTA BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="bautista-floor1-tab" href="../building/BAB/BABF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="bautista-floor2-tab" href="../building/BAB/BABF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="bautista-floor3-tab" href="../building/BAB/BABF3.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="bautista-floor4-tab" href="../building/BAB/BABF4.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MODAL 10 -->
                    <div id="myModal10" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal10"><i class="bi bi-x-lg"></i></span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>ACADEMIC BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor1-tab" href="../building/NEB/NEWBF1.php" role="tab" aria-controls="floor1" aria-selected="true">Floor1</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor2-tab" href="../building/NEB/NEWBF2.php" role="tab" aria-controls="floor2" aria-selected="false">Floor2</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor3-tab" href="../building/NEB/NEWBF3.php" role="tab" aria-controls="floor3" aria-selected="false">Floor3</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor4-tab" href="../building/NEB/NEWBF4.php" role="tab" aria-controls="floor4" aria-selected="false">Floor4</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor5-tab" href="../building/NEB/NEWBF5.php" role="tab" aria-controls="floor4" aria-selected="false">Floor5</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor6-tab" href="../building/NEB/NEWBF6.php" role="tab" aria-controls="floor4" aria-selected="false">Floor6</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="academic-floor7-tab" href="../building/NEB/NEWBF7.php" role="tab" aria-controls="floor4" aria-selected="false">Floor7</a>
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


        <script src="../../src/js/locationTracker.js"></script>
      
       
        <script>
        setInterval(function() {
            // Call a script to check if the user has timed out
            fetch('../../check_timeout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.timeout) {
                        alert('You have been logged out due to timeout.');
                        window.location.href = '../index.php?logout=timeout'; // Redirect to login page
                    }
                });
        }, 60000); // Checks every minute, you can adjust the interval
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
            data: { activityId: activityId },
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



        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <script src="../../src/js/main.js"></script>
        <script type="module" src="../../src/js/map.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="../../src/js/profileModalController.js"></script>
    </body>

    </html>