<?php

session_start();
include_once("../../config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {


    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }

    $filterRole = isset($_GET['filterRole']) ? $_GET['filterRole'] : 'all';

    $conditions = [];
    $params = [];
    $types = '';

    // Modify the conditions to exclude "Administrator" role
    if ($filterRole !== 'all') {
        $conditions[] = "role = ?";
        $params[] = $filterRole;
        $types .= 's';
    } else {
        $conditions[] = "LOWER(role) != 'Administrator'";
    }

    // Construct the SQL query based on conditions
    $query = "SELECT accountId, picture, firstname, lastname, role FROM account WHERE LOWER(role) != 'Administrator' AND UserLevel != 1";
    $sql = "SELECT accountId, picture, firstname, lastname, role FROM account WHERE = 'Maintenance Manager' AND UserLevel != 2";
    $conditions = [];
    $params = [];
    $types = '';

    if ($filterRole !== 'all') {
        $conditions[] = "role = ?";
        $params[] = $filterRole;
        $types .= 's';
    }

    if (!empty($conditions)) {
        $query .= " AND " . implode(' AND ', $conditions);
    }

    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        die('Error executing the query: ' . $stmt->error);
    }

    $result = $stmt->get_result();





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
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Attendance Logs</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
        <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css">
        <link rel="stylesheet" href="../../src/css/attendance-logs.css">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                <li class="active">
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
                <li>
                    <a href="./gps.php">
                        <i class="bi bi-geo-alt"></i>
                        <span class="text">GPS</span>
                    </a>
                </li>
                <li>
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
        <!-- SIDEBAR -->
        <!-- CONTENT -->
        <section id="content">
            <!-- MAIN -->
            <main>
                <div class="content-container">
                    <header>
                        <div class="cont-header">
                            <h1 class="tab-name"></h1>
                            <div class="form-wrapper">
                                <div class="tbl-filter">
                                    <!-- Wrap the form in a div with the class 'form-container' -->
                                    <!-- <form action="attendance-logs.php" method="get">
                                        <select name="filterRole" id="filterRole" onchange="this.form.submit()">
                                            <option value="all">All Roles</option>
                                            <option value="Maintenance Manager" <?php echo (isset($_GET['filterRole']) && $_GET['filterRole'] === "Maintenance Manager") ? 'selected' : ''; ?>>Manager</option>
                                            <option value="Maintenance Personnel" <?php echo (isset($_GET['filterRole']) && $_GET['filterRole'] === "Maintenance Personnel") ? 'selected' : ''; ?>>Personnel</option>
                                        </select>
                                    </form> -->

                                    <form class="d-flex" role="search">
                                        <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" onkeyup="searchTable()" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div class="new-nav-container">
                        <!--Content start of tabs-->
                    <div class="new-nav">
                        <ul>
                            <li><a href="#" class="nav-link active" id="manager-pill" data-bs-target="pills-manager">Manager</a></li>
                            <li><a href="#" class="nav-link" id="personnel-pill" data-bs-target="pills-personnel">Personnel</a></li>
                        </ul>
                    </div>

                    <!-- Export button
                    <div class="export-mob-hide">
                            <form method="post" id="exportForm">
                                <input type="hidden" name="status" id="statusField" value="For Replacement">
                                <button type="button" id="exportBtn" class="btn btn-outline-danger">Export Data</button>
                            </form>
                        </div> -->
                    </div>

                    <!--PILL TABS-->
                    <!-- Maintenance Manager -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="pills-manager" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-content">
                                <div class="table-header">
                                    <table>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>NAME</th>
                                            <th>ROLE</th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                // Modify your SQL query to fetch only the subset of results
                                $result = $conn->query("SELECT accountId, picture, firstname, lastname, role FROM account WHERE role = 'Maintenance Manager'");
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row = $result->fetch_assoc()) {
                                        // Output account information in each row
                                        echo '<tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#attendanceModal' . $row['accountId'] . '" data-account-id="' . $row['accountId'] . '">';
                                        echo '<td>' . $row['accountId'] . '</td>';
                                        echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row['picture']) . '" class="rounded-img" alt="Profile Image" style="width: 50px; height: 50px; "></td>';
                                        echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
                                        echo '<td style="display:none">' . $row['firstname'] . '</td>';
                                        echo '<td style="display:none">' . $row['lastname'] . '</td>';
                                        echo '<td>' . $row['role'] . '</td>';
                                        echo '</tr>';
                                    }
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='noDataImgH'>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

<!-- Modal -->
<?php
                    // Fetch and display attendance log data within modals
                    if ($result->num_rows > 0) {
                        $result->data_seek(0); // Reset result pointer to the beginning

                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="modal fade" id="attendanceModal' . $row['accountId'] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-lg modal-dialog-centered">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<div class="modal-close">';
                            echo '<button class="btn btn-close-modal-emp close-modal-btn" id="closeAddModal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="modal-footer">';

                            echo '<div class="modal-content-header">';
                            echo '<p class="h5-like">' . $row['firstname'] . ' ' . $row['lastname'] . '</p>';
                            echo '<form class="filterType">';
                            echo '<select name="filterType" id="filterType' . $row['accountId'] . '" onchange="filterAttendanceData(' . $row['accountId'] . ')" class="custom-select">';
                            echo '<option value="all">All</option>';
                            echo '<option value="week">This Week</option>';
                            echo '<option value="month">This Month</option>';
                            echo '<option value="year">This Year</option>';
                            echo '</select>';
                            echo '</form>';
                            echo '</div>';

                            $attendanceQuery = "SELECT date, timeIn, timeOut FROM attendancelogs WHERE accountId = ? ORDER BY date ASC";
                            $attendanceStmt = $conn->prepare($attendanceQuery);
                            $attendanceStmt->bind_param('i', $row['accountId']);
                            $attendanceStmt->execute();
                            $attendanceResult = $attendanceStmt->get_result();

                            if ($attendanceResult->num_rows > 0) {

                                // Table header
                                echo '<div class="table-whole-content1" id="exportContent' . $row['accountId'] . '">';
                                echo '<p class="h5-like visually-hidden" id="nameHeader' . $row['accountId'] . '">' . $row['firstname'] . ' ' . $row['lastname'] . '</p>';
                                echo '<div class="table-header1">';
                                echo '<table>';
                                echo '<tr>';
                                echo '<th>Day</th>';
                                echo '<th>Date</th>';
                                echo '<th>Time In</th>';
                                echo '<th>Time Out</th>';
                                echo '<th>Total Hours</th>';
                                echo '</tr>';
                                echo '</table>';
                                echo '</div>';

                                echo '<div class="modal-content-th">';
                                // Start the table and use a unique ID
                                echo '<table id="attendanceTable' . $row['accountId'] . '">';
                                // Table body
                                while ($attendanceRow = $attendanceResult->fetch_assoc()) {
                                    // Get the day of the week
                                    $dayOfWeek = date('l', strtotime($attendanceRow['date']));

                                    // Format timeIn and timeOut to show only the time with AM or PM
                                    $timeInFormatted = date('h:i A', strtotime($attendanceRow['timeIn']));

                                    date_default_timezone_set('Asia/Manila'); // Set the correct time zone, e.g., 'America/New_York'

                                    // Get the current timestamp
                                    $currentTimestamp = time();



                                    if (isset($attendanceRow['timeIn'])) {
                                        $timeIn = strtotime($attendanceRow['timeIn']);
                                        $currentTime = time(); // Current timestamp
                                        // Convert $timeSinceIn and current timestamp to date strings to compare dates
                                        $dateOfTimeSinceIn = date('Y-m-d', $timeIn);
                                        $currentDate = date('Y-m-d', $currentTimestamp);

                                        if (isset($attendanceRow['timeOut'])) {
                                            $timeOut = strtotime($attendanceRow['timeOut']);
                                            $timeDifference = $timeOut - $timeIn;
                                            $hours = floor($timeDifference / 3600);
                                            $hours -=1;
                                            $totalHoursFormatted = $hours;
                                            $timeOutFormatted = date('h:i A', $timeOut);
                                        } else {
                                            $timeSinceIn = $currentTime - $timeIn;

                                        // Check if the current time is past 12 AM and if the date has changed
                                        if ($currentDate > $dateOfTimeSinceIn || date('H', $currentTimestamp) == '00') {
                                            // If it's past 12 AM or the next day, set 'Not Timed Out' and '4 hours'
                                            $totalHoursFormatted = "4";
                                            $timeOutFormatted = 'Not Timed Out';
                                        } else {
                                            // If it's the same day and before 12 AM, keep both values empty
                                            $totalHoursFormatted = ''; // Keep totalHours empty
                                            $timeOutFormatted = ''; // Keep timeOut empty
                                        }
                                        }
                                    } else {
                                        $totalHoursFormatted = "No TimeIn Recorded"; // In case the user hasn't timed in yet
                                        $timeOutFormatted = ''; // Default value for timeOut in this case
                                    }

                                    echo '<tr data-day="' . $dayOfWeek . '">';
                                    echo '<td>' . $dayOfWeek . '</td>';
                                    echo '<td>' . $attendanceRow['date'] . '</td>';
                                    echo '<td>' . $timeInFormatted . '</td>';
                                    echo '<td>' . $timeOutFormatted . '</td>';
                                    echo '<td>' . $totalHoursFormatted . '</td>';
                                    echo '</tr>';
                                }

                                echo '</table>';
                                echo "</div>";
                                echo "</div>";
                            } else {
                                echo '<table>';
                                echo "<div class='noDataImgH'>";
                                echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                echo "</div>";
                                echo '</table>';
                            }

                            // Close the attendance log statement
                            $attendanceStmt->close();

                            echo '<button type="button" class="btn export-btn" id="exportBtn' . $row['accountId'] . '">EXPORT PDF</button>';



                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                    <!-- Modal -->

                    <!-- Maintenance Personnel -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane" id="pills-personnel" role="tabpanel" aria-labelledby="personnel-tab">
                            <div class="table-content">
                                <div class="table-header">
                                    <table>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>NAME</th>
                                            <th>ROLE</th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                // Modify your SQL query to fetch only the subset of results
                                $result = $conn->query("SELECT accountId, picture, firstname, lastname, role FROM account WHERE role = 'Maintenance Personnel'");
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row = $result->fetch_assoc()) {
                                        // Output account information in each row
                                        echo '<tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#attendanceModal' . $row['accountId'] . '" data-account-id="' . $row['accountId'] . '">';
                                        echo '<td>' . $row['accountId'] . '</td>';
                                        echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row['picture']) . '" class="rounded-img" alt="Profile Image" style="width: 50px; height: 50px; "></td>';
                                        echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
                                        echo '<td style="display:none">' . $row['firstname'] . '</td>';
                                        echo '<td style="display:none">' . $row['lastname'] . '</td>';
                                        echo '<td>' . $row['role'] . '</td>';
                                        echo '</tr>';
                                    }
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='noDataImgH'>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>


<!-- Modal -->
<?php
                    // Fetch and display attendance log data within modals
                    if ($result->num_rows > 0) {
                        $result->data_seek(0); // Reset result pointer to the beginning

                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="modal fade" id="attendanceModal' . $row['accountId'] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-lg modal-dialog-centered">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<div class="modal-close">';
                            echo '<button class="btn btn-close-modal-emp close-modal-btn" id="closeAddModal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="modal-footer">';

                            echo '<div class="modal-content-header">';
                            echo '<p class="h5-like">' . $row['firstname'] . ' ' . $row['lastname'] . '</p>';
                            echo '<form class="filterType">';
                            echo '<select name="filterType" id="filterType' . $row['accountId'] . '" onchange="filterAttendanceData(' . $row['accountId'] . ')" class="custom-select">';
                            echo '<option value="all">All</option>';
                            echo '<option value="week">This Week</option>';
                            echo '<option value="month">This Month</option>';
                            echo '<option value="year">This Year</option>';
                            echo '</select>';
                            echo '</form>';
                            echo '</div>';

                            $attendanceQuery = "SELECT date, timeIn, timeOut FROM attendancelogs WHERE accountId = ? ORDER BY date ASC";
                            $attendanceStmt = $conn->prepare($attendanceQuery);
                            $attendanceStmt->bind_param('i', $row['accountId']);
                            $attendanceStmt->execute();
                            $attendanceResult = $attendanceStmt->get_result();

                            if ($attendanceResult->num_rows > 0) {

                                // Table header
                                echo '<div class="table-whole-content1" id="exportContent' . $row['accountId'] . '">';
                                echo '<p class="h5-like visually-hidden" id="nameHeader' . $row['accountId'] . '">' . $row['firstname'] . ' ' . $row['lastname'] . '</p>';
                                echo '<div class="table-header1">';
                                echo '<table>';
                                echo '<tr>';
                                echo '<th>Day</th>';
                                echo '<th>Date</th>';
                                echo '<th>Time In</th>';
                                echo '<th>Time Out</th>';
                                echo '<th>Total Hours</th>';
                                echo '</tr>';
                                echo '</table>';
                                echo '</div>';

                                echo '<div class="modal-content-th">';
                                // Start the table and use a unique ID
                                echo '<table id="attendanceTable' . $row['accountId'] . '">';
                                // Table body
                                while ($attendanceRow = $attendanceResult->fetch_assoc()) {
                                    // Get the day of the week
                                    $dayOfWeek = date('l', strtotime($attendanceRow['date']));

                                    // Format timeIn and timeOut to show only the time with AM or PM
                                    $timeInFormatted = date('h:i A', strtotime($attendanceRow['timeIn']));

                                    date_default_timezone_set('Asia/Manila'); // Set the correct time zone, e.g., 'America/New_York'

                                    // Get the current timestamp
                                    $currentTimestamp = time();



                                    if (isset($attendanceRow['timeIn'])) {
                                        $timeIn = strtotime($attendanceRow['timeIn']);
                                        $currentTime = time(); // Current timestamp
                                        // Convert $timeSinceIn and current timestamp to date strings to compare dates
                                        $dateOfTimeSinceIn = date('Y-m-d', $timeIn);
                                        $currentDate = date('Y-m-d', $currentTimestamp);

                                        if (isset($attendanceRow['timeOut'])) {
                                            $timeOut = strtotime($attendanceRow['timeOut']);
                                            $timeDifference = $timeOut - $timeIn;
                                            $hours = floor($timeDifference / 3600);
                                            $hours -=1;
                                            $totalHoursFormatted = $hours;
                                            $timeOutFormatted = date('h:i A', $timeOut);
                                        } else {
                                            $timeSinceIn = $currentTime - $timeIn;

                                        // Check if the current time is past 12 AM and if the date has changed
                                        if ($currentDate > $dateOfTimeSinceIn || date('H', $currentTimestamp) == '00') {
                                            // If it's past 12 AM or the next day, set 'Not Timed Out' and '4 hours'
                                            $totalHoursFormatted = "4";
                                            $timeOutFormatted = 'Not Timed Out';
                                        } else {
                                            // If it's the same day and before 12 AM, keep both values empty
                                            $totalHoursFormatted = ''; // Keep totalHours empty
                                            $timeOutFormatted = ''; // Keep timeOut empty
                                        }
                                        }
                                    } else {
                                        $totalHoursFormatted = "No TimeIn Recorded"; // In case the user hasn't timed in yet
                                        $timeOutFormatted = ''; // Default value for timeOut in this case
                                    }

                                    echo '<tr data-day="' . $dayOfWeek . '">';
                                    echo '<td>' . $dayOfWeek . '</td>';
                                    echo '<td>' . $attendanceRow['date'] . '</td>';
                                    echo '<td>' . $timeInFormatted . '</td>';
                                    echo '<td>' . $timeOutFormatted . '</td>';
                                    echo '<td>' . $totalHoursFormatted . '</td>';
                                    echo '</tr>';
                                }

                                echo '</table>';
                                echo "</div>";
                                echo "</div>";
                            } else {
                                echo '<table>';
                                echo "<div class='noDataImgH'>";
                                echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                echo "</div>";
                                echo '</table>';
                            }

                            // Close the attendance log statement
                            $attendanceStmt->close();

                            echo '<button type="button" class="btn export-btn" id="exportBtn' . $row['accountId'] . '">EXPORT PDF</button>';


                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                    <!-- Modal -->
                </div>
                </div>
            </main>
        </section>

        <!-- MODALS -->
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


        <script src="../../src/js/main.js"></script>
        <script src="../../src/js/attendance.js"></script>

        <!-- BOOTSTRAP -->
        <!-- <link rel="stylesheet" href="path/to/bootstrap.min.css"> -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- <script src="path/to/bootstrap.min.js"></script> -->
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

        <script>
            var managerPill = document.getElementById('manager-pill');
            var personnelPill = document.getElementById('personnel-pill');
            var managerContent = document.getElementById('pills-manager');
            var personnelContent = document.getElementById('pills-personnel');

            function activateTab(pill, content, role) {
                // Remove active class from all tabs and contents
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');

                // Add active class to the clicked tab and its content
                pill.addClass('active');
                content.addClass('show active');

                // Store the last active pill in session storage
                sessionStorage.setItem('lastPill', role);

                // Call the filterTable function with the active role
                filterTable(role);
            }

            managerPill.addEventListener('click', function(e) {
                e.preventDefault();
                activateTab(managerPill, managerContent, 'manager');
            });

            personnelPill.addEventListener('click', function(e) {
                e.preventDefault();
                activateTab(personnelPill, personnelContent, 'personnel');
            });

            // Directly check and activate the tab from session storage on page load
            var lastPill = sessionStorage.getItem('lastPill') || 'manager';
            if (lastPill === 'personnel') {
                activateTab(personnelPill, personnelContent, 'personnel');
            } else {
                activateTab(managerPill, managerContent, 'manager');
            }

            function filterTable(activeRole) {
                var query = document.getElementById('search-box').value.toLowerCase();
                var rows = document.querySelectorAll('.table-container tbody tr');

                rows.forEach(function(row) {
                    var roleCell = row.querySelector("td:last-child").textContent.toLowerCase(); // Using :last-child pseudo-class
                    var isRoleMatch = (activeRole === 'manager' && roleCell.includes('maintenance manager')) || (activeRole === 'personnel' && roleCell.includes('maintenance personnel'));
                    var isQueryMatch = row.textContent.toLowerCase().includes(query);
                    row.style.display = (isRoleMatch && isQueryMatch) ? '' : 'none';
                });
            }
        </script>


        <script>
function exportTableToPDF(exportContentId, filename, employeeName) {
    const exportContent = document.getElementById(exportContentId);

    if (!exportContent) {
        Swal.fire({
            title: 'Failed!',
            text: 'No data available to export.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Store original styles
    const modalContent = document.querySelector('.modal-content-th');
    const originalMaxHeight = modalContent.style.maxHeight;

    // Temporarily change the style for export
    modalContent.style.overflow = 'visible';
    modalContent.style.maxHeight = 'none';

    Swal.fire({
        title: 'Preparing your PDF...',
        text: 'Please wait...',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    html2canvas(exportContent, {
        useCORS: true,
        scale: 1, // You might want to reduce the scale if the image is too large.
        windowHeight: exportContent.scrollHeight, // This ensures the height includes all scrollable content.
        onclone: function (clonedDocument) {
            // Explicitly set the height of the cloned element to its scrollHeight.
            const clonedElement = clonedDocument.getElementById(exportContentId);
            clonedElement.style.height = `${exportContent.scrollHeight}px`;
        }
    }).then(canvas => {
        const pdf = new jspdf.jsPDF({
            orientation: 'portrait', // Change to landscape if required
            unit: 'mm',
            format: [215.9, 655.6] // Legal dimensions in mm
        });
        const addHeader = () => {
            pdf.setFontSize(15); // Increased font size for header
            pdf.text('Employee - ' + employeeName, 10, 10); // Adjusted y position for larger header
        };

        const imgData = canvas.toDataURL('image/png');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const imgWidth = pdfWidth; // Use full page width for the image
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;

        addHeader(); // Add the header for the first page
        let position = 20; // Position of the table image

        // Adjust position and height for the header
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pdfHeight;

        // Add additional pages if the table doesn't fit on one page
        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            addHeader(); // Add the header for subsequent pages
            pdf.addImage(imgData, 'PNG', 0, position - pdfHeight + 20, imgWidth, imgHeight); // Adjust the table position
            heightLeft -= pdfHeight;
        }

        pdf.save(filename); // Save the PDF

        // After saving the PDF, restore the original styles
        modalContent.style.maxHeight = originalMaxHeight;

        Swal.close();
        Swal.fire({
            title: 'Done!',
            text: 'Your PDF has been downloaded.',
            icon: 'success',
            timer: 1000,
            showConfirmButton: false
        });
    }).catch(error => {
        // In case of an error, also restore the original styles
        modalContent.style.maxHeight = originalMaxHeight;

        Swal.fire({
            title: 'Error!',
            text: 'There was a problem generating the PDF.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        console.error('Error generating PDF: ', error);
    });
}

        </script>

        <script>
            function filterAttendanceData(accountId) {
                var selectedValue = document.getElementById('filterType' + accountId).value;
                var tableRows = document.querySelectorAll('#attendanceTable' + accountId + ' tbody tr');

                var lastVisibleRow = null;

                tableRows.forEach(function(row, index) {
                    if (selectedValue === 'all' || row.getAttribute('data-day') === selectedValue) {
                        row.style.display = 'table-row';
                        lastVisibleRow = row; // Store the last visible row
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Remove the border from all rows
                tableRows.forEach(function(row) {
                    row.style.borderBottom = 'none';
                });

                // Add border-bottom to the last visible row
                if (lastVisibleRow) {
                    lastVisibleRow.style.borderBottom = '1px solid black';
                }
            }
        </script>

        <script>
            $(window).on('load', function() {
    var managerPill = $('#manager-pill');
    var personnelPill = $('#personnel-pill');
    var managerContent = $('#pills-manager');
    var personnelContent = $('#pills-personnel');
    var searchBox = $("#search-box");

    // Function to explicitly set the active tab based on the lastPill value and reapply the filter
    function activateLastPill() {
        var lastPill = sessionStorage.getItem('lastPill') || 'manager';

        // Reset active states
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');

        if (lastPill === 'personnel') {
            personnelPill.addClass('active');
            personnelContent.addClass('show active');
        } else {
            managerPill.addClass('active');
            managerContent.addClass('show active');
        }

        filterTable(); // Reapply the filter whenever a tab is activated
    }

    // Event listeners for tab clicks, including reapplying the filter
    managerPill.on('click', function(e) {
        e.preventDefault();
        sessionStorage.setItem('lastPill', 'manager');
        activateLastPill();
    });

    personnelPill.on('click', function(e) {
        e.preventDefault();
        sessionStorage.setItem('lastPill', 'personnel');
        activateLastPill();
    });

    function filterTable() {
        var query = searchBox.val().toLowerCase();
        var activeRole = managerPill.hasClass('active') ? 'Maintenance Manager' : 'Maintenance Personnel';

        $(".table-container tbody tr").each(function() {
            var row = $(this);
            var roleCell = row.find("td").last().text().toLowerCase();

            if (roleCell === activeRole.toLowerCase() && row.text().toLowerCase().includes(query)) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Bind the input event to the search box for dynamic filtering
    searchBox.on("input", filterTable);

    // Check if the last active tab was personnel, and activate it immediately upon page load
    activateLastPill();
});

        </script>

        <script>
            function filterAttendanceData(accountId) {
                var selectedValue = document.getElementById('filterType' + accountId).value;
                var tableRows = document.querySelectorAll('#attendanceTable' + accountId + ' tbody tr');
                var currentDate = new Date();

                tableRows.forEach(function(row) {
                    var dateCell = row.querySelector("td:nth-child(2)"); // Assuming date is in the 2nd column
                    var rowDate = new Date(dateCell.textContent.trim());

                    switch (selectedValue) {
                        case 'all':
                            row.style.display = 'table-row';
                            break;
                        case 'week':
                            if (isSameWeek(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                        case 'month':
                            if (isSameMonth(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                        case 'year':
                            if (isSameYear(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                    }
                });
            }

            function isSameWeek(date1, date2) {
                var startOfWeek = new Date(date1);
                startOfWeek.setDate(date1.getDate() - date1.getDay()); // Start of the current week (Sunday)
                var endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6); // End of the current week (Saturday)

                return date2 >= startOfWeek && date2 <= endOfWeek;
            }

            function isSameMonth(date1, date2) {
                return date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
            }

            function isSameYear(date1, date2) {
                return date1.getFullYear() === date2.getFullYear();
            }
        </script>



<script>
document.body.addEventListener('click', function(event) {
    if (event.target.matches('.export-btn')) {
        var accountId = event.target.id.replace('exportBtn', '');
        var nameHeaderElement = document.getElementById('nameHeader' + accountId);
        var filterTypeElement = document.getElementById('filterType' + accountId);

        if (!nameHeaderElement || !filterTypeElement) {
            console.error('Required element(s) not found for accountId:', accountId);
            Swal.fire({ // SweetAlert to inform the user
                title: 'No Data Available',
                text: 'Required data is missing for this account. Please check and try again.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        var nameHeader = nameHeaderElement.textContent;
        var filterType = filterTypeElement.value;
        var formData = new FormData();
        formData.append('accountId', accountId);
        formData.append('name', nameHeader);
        formData.append('filterType', filterType);

        console.log('Account ID:', accountId);
        console.log('Name:', nameHeader);

        Swal.fire({
            title: 'Choose the file format',
            showDenyButton: true,
            confirmButtonText: 'PDF',
            denyButtonText: 'Excel',
        }).then((result) => {
            if (result.isConfirmed) {
                formData.append('submit', 'Export to PDF');
                performExport(formData, 'export-pdf-attendancelogs.php');
            } else if (result.isDenied) {
                formData.append('submit', 'Export to Excel');
                performExport(formData, 'export-excel-attendancelogs.php');
            }
        });
    }
});

function performExport(formData, endpoint) {
    console.log('Performing export to:', endpoint);

    Swal.fire({
        title: 'Exporting...',
        html: 'Please wait while the file is being generated.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        },
    });

    fetch(endpoint, {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.blob();
    })
    .then(blob => {
        const fileExtension = getFileExtension(endpoint);
        const fileName = `${formData.get('name').replace(/ /g, '-')}.${fileExtension}`;

        console.log('Generated File Name:', fileName);

        const downloadUrl = window.URL.createObjectURL(blob);
        const downloadLink = document.createElement('a');
        downloadLink.href = downloadUrl;
        downloadLink.download = fileName;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        window.URL.revokeObjectURL(downloadUrl);
        document.body.removeChild(downloadLink);

        Swal.fire({
            title: 'Exporting Done',
            text: 'Your file has been successfully generated.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    })
    .catch(error => {
        console.error('Export Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'There was an issue generating the file.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function getFileExtension(endpoint) {
    if (endpoint.includes('pdf')) return 'pdf';
    if (endpoint.includes('excel')) return 'xlsx';
    return '';
}

</script>

    </body>

    </html>