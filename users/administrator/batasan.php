<?php
session_start();
include_once ("../../config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();
if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {


    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
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

    $sql = "SELECT * FROM request WHERE campus = 'Batasan'";
    $result = $conn->query($sql) or die($conn->error);

    if (isset($_POST['edit'])) {
        $request_id = $_POST['request_id'];

        $campus = $_POST['campus'];
        $building = $_POST['building'];
        $floor = $_POST['floor'];
        $room = $_POST['room'];
        $equipment = $_POST['equipment'];
        $req_by = $_POST['req_by'];
        $category = $_POST['category'];
        $assignee = $_POST['assignee'];
        $status = $_POST['status'];
        $description = $_POST['description'];
        $deadline = $_POST['deadline'];

        // Insert data into the request table
        $insertQuery = "INSERT INTO request (request_id, campus, building, floor, room, equipment, req_by, category, assignee, status, description, deadline)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertQuery);

        // Bind parameters
        $stmt->bind_param("ssssssssssss", $request_id, $campus, $building, $floor, $room, $equipment, $req_by, $category, $assignee, $status, $description, $deadline);

        if ($stmt->execute()) {
            // Data inserted successfully, redirect to batasan.php
            header("Location: batasan.php");
            exit(); // Make sure to exit to prevent further execution
        } else {
            echo "Error inserting data: " . $conn->error;
        }

        $conn->close();
    }
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Request</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/archive.css" />
        <link rel="stylesheet" href="../../src/css/reports.css" />

        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function () {
                let lastPillSelected = sessionStorage.getItem('lastPillArchive');

                if (!lastPillSelected) {
                    $("#pills-manager").addClass("show active");
                    $("#pills-profile").removeClass("show active");
                    $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                    $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                } else {
                    switch (lastPillSelected) {
                        case 'pills-manager':
                            $("#pills-manager").addClass("show active");
                            $("#pills-profile").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                            $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                            break;
                        case 'pills-profile':
                            $("#pills-profile").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-profile']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                    }
                }

                $(".nav-link").click(function () {
                    const targetId = $(this).data("bs-target");

                    sessionStorage.setItem('lastPillArchive', targetId);

                    $(".tab-pane").removeClass("show active");
                    $(`#${targetId}`).addClass("show active");
                    $(".nav-link").removeClass("active");
                    $(this).addClass("active");
                });
            });
        </script>
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
                        <!-- <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i> -->
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
        <a href="./dashboard.php" class="brand" title="logo">
            <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i>
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
            <div class="Map-cont" onclick="toggleMAP()">
                <li class="Map-dropdown active">
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
            <div class="Map-container aaa">
                <li class="Map-Batasan active">
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
    <section id="content">
        <main>
            <div class="content-container">
                <header>
                    <div class="cont-header">
                        <h1 class="tab-name"></h1>
                        <div class="tbl-filter">
                            <form class="d-flex" role="search" id="searchForm">
                                <input class="form-control icon" type="search" placeholder="Search" aria-label="Search"
                                    id="search-box" name="q" />
                            </form>
                        </div>
                    </div>
                </header>
                <div class="new-nav-container">
                    <!--Content start of tabs-->
                    <div class="new-nav">
                        <ul>
                            <li><a href="#" class="nav-link" data-bs-target="pills-manager">Request</a></li>
                            <li><a href="#" class="nav-link" data-bs-target="pills-profile">Outsource</a></li>
                        </ul>
                    </div>

                    <!-- Export button -->
                    <div class="export-mob-hide">
                        <form method="post" id="exportForm">
                            <input type="hidden" name="status" id="statusField" value="For Replacement">
                            <button type="button" id="exportBtn" class="btn btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#addRequest">Add Task</button>
                        </form>
                    </div>
                </div>


                <div class="tab-content pt" id="myTabContent">
                    <div class="tab-pane fade show active" id="pills-manager" role="tabpanel"
                        aria-labelledby="home-tab">
                        <div class="table-content">
                            <div class='table-header'>
                                <table>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Date & Time</th>
                                        <th>Category</th>
                                        <th>Location</th>
                                        <th>Equipment</th>
                                        <th>Assignee</th>
                                        <th>Status</th>
                                        <th>Deadline</th>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            if ($result->num_rows > 0) {
                                echo "<div class='table-container'>";
                                echo "<table>";
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['request_id'] . '</td>';
                                    echo '<td>' . $row['date'] . '</td>';
                                    echo '<td>' . $row['category'] . '</td>';
                                    echo '<td>' . $row['building'] . ', ' . $row['floor'] . ', ' . $row['room'] . '</td>';
                                    echo '<td>' . $row['equipment'] . '</td>';
                                    echo '<td>' . $row['assignee'] . '</td>';
                                    echo '<td >' . $row['status'] . '</td>';
                                    echo '<td>' . $row['deadline'] . '</td>';

                                    echo '<td style="display:none;">' . $row['campus'] . '</td>';
                                    echo '<td style="display:none;">' . $row['building'] . '</td>';
                                    echo '<td style="display:none;">' . $row['floor'] . '</td>';
                                    echo '<td style="display:none;">' . $row['room'] . '</td>';
                                    echo '<td style="display:none;">' . $row['description'] . '</td>';
                                    echo '<td style="display:none;">' . $row['req_by'] . '</td>';
                                    echo '</tr>';
                                }
                                echo "</table>";
                                echo "</div>";
                            } else {
                                echo '<table>';
                                echo "<div class=noDataImgH>";
                                echo '<img src="../../src/img/emptyTable.png" alt="No data available" class="noDataImg"/>';
                                echo "</div>";
                                echo '</table>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="table-content">
                            <div class='table-header'>
                                <table>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Date & Time</th>
                                        <th>Category</th>
                                        <th>Location</th>
                                        <th>Equipment</th>
                                        <th>Assignee</th>
                                        <th>Status</th>
                                        <th>Deadline</th>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            if ($result->num_rows > 0) {
                                echo "<div class='table-container'>";
                                echo "<table>";
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['request_id'] . '</td>';
                                    echo '<td>' . $row['date'] . '</td>';
                                    echo '<td>' . $row['category'] . '</td>';
                                    echo '<td>' . $row['building'] . ', ' . $row['floor'] . ', ' . $row['room'] . '</td>';
                                    echo '<td>' . $row['equipment'] . '</td>';
                                    echo '<td>' . $row['assignee'] . '</td>';
                                    echo '<td >' . $row['status'] . '</td>';
                                    echo '<td>' . $row['deadline'] . '</td>';

                                    echo '<td style="display:none;">' . $row['campus'] . '</td>';
                                    echo '<td style="display:none;">' . $row['building'] . '</td>';
                                    echo '<td style="display:none;">' . $row['floor'] . '</td>';
                                    echo '<td style="display:none;">' . $row['room'] . '</td>';
                                    echo '<td style="display:none;">' . $row['description'] . '</td>';
                                    echo '<td style="display:none;">' . $row['req_by'] . '</td>';
                                    echo '</tr>';
                                }
                                echo "</table>";
                                echo "</div>";
                            } else {
                                echo '<table>';
                                echo "<div class=noDataImgH>";
                                echo '<img src="../../src/img/emptyTable.png" alt="No data available" class="noDataImg"/>';
                                echo "</div>";
                                echo '</table>';
                            }
                            ?>
                        </div>
                    </div>
                    <!--MODAL FOR NEW REQUEST-->
                    <div class="modal-parent">
                        <div class="modal modal-xl fade" id="addRequest" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Maintenance Staff Information</h5>

                                        <button class="btn btn-close-modal-emp close-modal-btn"
                                            data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" class="row g-3">
                                            <div class="col-4" style="display:none;">
                                                <label for="request_id" class="form-label">Request ID:</label>
                                                <input type="text" class="form-control" id="request_id"
                                                    name="request_id" readonly />
                                            </div>
                                            <div class="col-4" style="display:none;">
                                                <label for="date" class="form-label">Date & Time:</label>
                                                <input type="text" class="form-control" id="date" name="date" />
                                            </div>
                                            <div class="col-4" style="display:none;">
                                                <label for="campus" class="form-label">Campus:</label>
                                                <input type="text" class="form-control" id="campus" name="campus" />
                                            </div>
                                            <div class="col-4">
                                                <label for="building" class="form-label">Building:</label>
                                                <input type="text" class="form-control" id="building" name="building" />
                                            </div>

                                            <div class="col-4">
                                                <label for="floor" class="form-label">Floor:</label>
                                                <input type="text" class="form-control" id="floor" name="floor" />
                                            </div>

                                            <div class="col-4">
                                                <label for="room" class="form-label">Room: </label>
                                                <input type="text" class="form-control" id="room" name="room" />
                                            </div>

                                            <div class="col-4">
                                                <label for="equipment" class="form-label">Equipment :</label>
                                                <input type="text" class="form-control" id="equipment"
                                                    name="equipment" />
                                            </div>

                                            <div class="col-4" style="display:none;">
                                                <label for="req_by" class="form-label">Requested By: </label>
                                                <input type="text" class="form-control" id="req_by" name="req_by" />
                                            </div>

                                            <div class="col-4">
                                                <label for="category" class="form-label">Category: </label>
                                                <select class="form-select" id="category" name="category">
                                                    <option value="Carpentry">Carpentry</option>
                                                    <option value="Electrical">Electrical</option>
                                                    <option value="Plumbing">Plumbing</option>
                                                </select>
                                            </div>

                                            <div class="col-4">
                                                <label for="assignee" class="form-label">Assignee:</label>
                                                <input type="text" class="form-control" id="assignee" name="assignee" />
                                            </div>

                                            <div class="col-4" style="display: none;">
                                                <label for="status" class="form-label">Status:</label>
                                                <input type="text" class="form-control" value="Assigned" id="status"
                                                    name="status" />
                                            </div>

                                            <div class="col-4">
                                                <label for="deadline" class="form-label">Deadline:</label>
                                                <input type="date" class="form-control" id="deadline" name="deadline" />
                                            </div>

                                            <div class="col-12">
                                                <label for="description" class="form-label">Description:</label>
                                                <input type="text" class="form-control" id="description"
                                                    name="description" />
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                                    data-bs-target="#staticBackdrop1">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Edit for table 1-->
                    <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    Are you sure you want to save changes?
                                    <div class="modal-popups">
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                        <button class="btn add-modal-btn" name="edit"
                                            data-bs-dismiss="modal">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </form>



                </div>
            </div>
        </main>
    </section>

    <!-- PROFILE MODALS -->
    <?php include_once 'modals/modal_layout.php'; ?>


    <!-- RFID MODAL -->
    <div class="modal" id="staticBackdrop112" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
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
    <script src="../../src/js/archive.js"></script>
    <script src="../../src/js/profileModalController.js"></script>
    <script src="../../src/js/logout.js"></script>



    <script>
        $(document).ready(function () {
            $('.notification-item').on('click', function (e) {
                e.preventDefault();
                var activityId = $(this).data('activity-id');
                var notificationItem = $(this); // Store the clicked element

                $.ajax({
                    type: "POST",
                    url: "update_single_notification.php", // The URL to the PHP file
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

    <!-- Add this script after your existing scripts -->
    <!-- Add this script after your existing scripts -->
    <script>
        // Add a click event listener to the logout link
        document.getElementById('logoutBtn').addEventListener('click', function () {
            // Display SweetAlert
            Swal.fire({
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user clicks "Yes, logout!" execute the logout action
                    window.location.href = '../../logout.php';
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            let fullname;

            class AccountManager {
                constructor() {
                    this.setupEventListeners();
                }

                showAlert(title, text, icon, timer = null) {
                    const options = {
                        title: title,
                        text: text,
                        icon: icon,
                        showConfirmButton: false,
                    };

                    if (timer !== null) {
                        options.timer = timer;
                    }

                    Swal.fire(options);
                }

                restoreAccount(archiveId) {
                    $.ajax({
                        type: "POST",
                        url: "archive.php",
                        data: {
                            accept: true,
                            archiveId: archiveId,
                        },
                        success: (response) => {
                            console.log(response);
                            this.showAlert("", fullname + " has been restored successfully!", "success");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },
                        error: (error) => {
                            console.error("Error restoring account:", error);
                            this.showAlert("", "Failed to restore account.", "error");
                        },
                    });
                }

                confirmRestore(rowData) {
                    fullname = rowData.firstName + ' ' + rowData.lastName;

                    Swal.fire({
                        title: `Are you sure you want to restore<span style="font-weight: bold;">${rowData.firstName}</span> <span style="font-weight: bold;">${rowData.lastName}</span>?`,
                        showCancelButton: true,
                        allowOutsideClick: false,
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        icon: "warning",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.restoreAccount(rowData.archiveId);
                        } else {
                            this.showAlert("", "Account restoration cancelled.", "info", 1000);
                        }
                    });
                }



                setupEventListeners() {
                    $(".restore-btn").click((event) => {
                        event.stopPropagation();
                        const row = $(event.target).closest("tr");
                        const rowData = {
                            archiveId: row.find("td:eq(0)").text(),
                            firstName: row.find("td:eq(1)").text(),
                            lastName: row.find("td:eq(2)").text(),
                        };

                        this.confirmRestore(rowData);
                    });
                }
            }

            // Instantiate the class
            const accountManager = new AccountManager();
        });
    </script>

    <script>
        $(document).ready(function () {
            function populateModal(row) {
                // Your existing code to populate the modal fields
                $("#archiveId").val(row.find("td:eq(0)").text());
                $("#picture").val(row.find("td:eq(1)").text());
                $("#firstName").val(row.find("td:eq(3)").text());
                $("#middleName").val(row.find("td:eq(4)").text());
                $("#lastName").val(row.find("td:eq(5)").text());
                $("#role").val(row.find("td:eq(10)").text());
                $("#contact").val(row.find("td:eq(8)").text());
                $("#email").val(row.find("td:eq(6)").text());
                $("#password").val(row.find("td:eq(7)").text());
            }

            // Click event for the table row
            $(".table-container table tbody tr").click(function () {
                var row = $(this);
                populateModal(row);
                $("#exampleModal").modal("show");
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Bind the filter function to the input field
            $("#search-box").on("input", function () {
                var query = $(this).val().toLowerCase();
                filterTable(query);
            });

            function filterTable(query) {
                let hasData = false;
                let child = $("<tr class='emptyMsg'><td>No results found</td></tr>");

                $(".table-container tbody tr").each(function () {
                    var row = $(this);
                    var archiveIDCell = row.find("td:eq(0)"); // Archive ID column
                    var firstNameCell = row.find("td:eq(1)"); // FirstName column
                    var middleNameCell = row.find("td:eq(2)"); // LastName column
                    var lastNameCell = row.find("td:eq(3)"); // LastName column
                    var roleCell = row.find("td:eq(10)"); // LastName column

                    // Get the text content of each cell
                    var archiveIDText = archiveIDCell.text().toLowerCase();
                    var firstNameText = firstNameCell.text().toLowerCase();
                    var middleNameText = middleNameCell.text().toLowerCase();
                    var lastNameText = lastNameCell.text().toLowerCase();
                    var roleText = roleCell.text().toLowerCase();

                    // Check if any of the cells contain the query
                    var showRow = archiveIDText.includes(query) ||
                        firstNameText.includes(query) ||
                        middleNameText.includes(query) ||
                        lastNameText.includes(query) ||
                        roleText.includes(query) ||
                        archiveIDText == query || // Exact match for Archive ID
                        firstNameText == query || // Exact match for FirstName
                        middleNameText == query || // Exact match for LastName
                        lastNameText == query || // Exact match for LastName
                        roleText == query; // Exact match for LastName

                    // Show or hide the row based on the result
                    if (showRow) {
                        hasData = true;
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                if (!hasData) {
                    $(".table-container tbody").append("<tr class='emptyMsg'><td>No results found</td></tr>");
                } else {
                    $('.emptyMsg').remove();
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

</body>

</html>