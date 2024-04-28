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

    $sql = "SELECT * FROM request WHERE campus = 'Batasan' AND status IN ('Pending', 'Done', 'For Approval') AND category != 'Outsource'";
    $result = $conn->query($sql) or die($conn->error);


    $sql2 = "SELECT * FROM request WHERE campus = 'Batasan' AND category = 'Outsource'";
    $result2 = $conn->query($sql2) or die($conn->error);

    function logActivity($conn, $accountId, $actionDescription, $tabValue)
    {
        $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("iss", $accountId, $actionDescription, $tabValue);
        if (!$stmt->execute()) {
            echo "Error logging activity: " . $stmt->error;
        }
        $stmt->close();
    }



    if (isset($_POST['add'])) {
        $request_id = $_POST['new_request_id'];
        $campus = $_POST['new_campus'];
        $building = $_POST['new_building'];
        $floor = $_POST['new_floor'];
        $room = $_POST['new_room'];
        $equipment = $_POST['new_equipment'];
        $req_by = $_POST['new_req_by'];
        $category = $_POST['new_category'];
        $assignee = $_POST['new_assignee'];
        $status = $_POST['new_status'];
        $description = $_POST['new_description'];
        $deadline = $_POST['new_deadline'];

        // Insert data into the request table
        $insertQuery = "INSERT INTO request (request_id, campus, building, floor, room, equipment, req_by, category, assignee, status, description, deadline)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertQuery);

        // Bind parameters
        $stmt->bind_param("ssssssssssss", $request_id, $campus, $building, $floor, $room, $equipment, $req_by, $category, $assignee, $status, $description, $deadline);

        if ($stmt->execute()) {
            // Log activity for task creation and assignment
            $action = "Created and assigned task to $assignee";
            logActivity($conn, $_SESSION['accountId'], $action, 'General');

            // Redirect to the desired page
            header("Location: batasan.php");
            exit(); // Make sure to exit to prevent further execution
        } else {
            echo "Error inserting data: " . $conn->error;
        }

        $conn->close();
    }


    if (isset($_POST['approval'])) {
        // Retrieve request_id from the form
        $request_id2 = $_POST['request_id'];

        // Retrieve other form data
        $campus2 = $_POST['campus'];
        $building2 = $_POST['building'];
        $floor2 = $_POST['floor'];
        $room2 = $_POST['room'];
        $equipment2 = $_POST['equipment'];
        $category2 = $_POST['category'];
        $assignee2 = $_POST['assigneereal'];
        $status2 = $_POST['status'];
        $description2 = $_POST['description'];
        $deadline2 = $_POST['deadline'];

        // SQL UPDATE query
        $sql3 = "UPDATE request 
                 SET campus = ?, building = ?, floor = ?, room = ?, 
                     equipment = ?, category = ?, assignee = ?, 
                     status = ?, description = ?, deadline = ? 
                 WHERE request_id = ?";

        // Prepare the SQL statement
        $stmt3 = $conn->prepare($sql3);

        // Bind parameters
        $stmt3->bind_param("ssssssssssi", $campus2, $building2, $floor2, $room2, $equipment2, $category2, $assignee2, $status2, $description2, $deadline2, $request_id2);

        // Execute the query
    if ($stmt3->execute()) {
        // Log activity for admin approval with new assignee
        $approval_action = "Task ID $request_id2 approved with $assignee2 as new assignee.";
        $reassignment_action =  "Task ID $request_id2 reassigned to $assignee2.";
        logActivity($conn, $_SESSION['accountId'], $approval_action, 'General');
        logActivity($conn, $_SESSION['accountId'], $reassignment_action, 'General');
    
        // Redirect back to the page
        header("Location: batasan.php");
        
        exit();
    } else {
        // Error occurred while updating
        echo "Error updating request: " . $stmt3->error;
    }

    // Close statement
    $stmt3->close();
}

    if (isset($_POST['Outsource'])) {
        $request_id4 = $_POST['new2_request_id'];
        $campus4 = $_POST['new2_campus'];
        $building4 = $_POST['new2_building'];
        $floor4 = $_POST['new2_floor'];
        $room4 = $_POST['new2_room'];
        $equipment4 = $_POST['new2_equipment'];
        $req_by4 = $_POST['new2_req_by'];
        $category4 = $_POST['new2_category'];
        $assignee4 = $_POST['new2_assignee'];
        $status4 = 'Done';
        $description4 = $_POST['new2_description'];
        $deadline4 = $_POST['new2_deadline'];

        // Update data in the request table
        $updateQuery = "UPDATE request SET campus=?, building=?, floor=?, room=?, equipment=?, req_by=?, category=?, assignee=?, status=?, description=?, deadline=? WHERE request_id=?";

        $stmt4 = $conn->prepare($updateQuery);

        // Bind parameters
        $stmt4->bind_param("ssssssssssss", $campus4, $building4, $floor4, $room4, $equipment4, $req_by4, $category4, $assignee4, $status4, $description4, $deadline4, $request_id4);

       // Execute the query
    if ($stmt4->execute()) {
        // Log activity for admin approval with outsource as new assignee
        $action4 = "Task ID $request_id4 approved with Outsource ($assignee4) as new assignee.";

        logActivity($conn, $_SESSION['accountId'], $action4, 'General');

        // Redirect back to the page
        header("Location: batasan.php");
        exit();
    } else {
        echo "Error updating data: " . $conn->error;
    }

    $stmt4->close();
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


        .blue {
            color: blue;
        }

        .green {
            color: green;
        }

        .red {
            color: red;
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
                                        <th>Deadline</th>
                                        <th>Status</th>

                                        <th></th>
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
                                    echo '<td>' . $row['deadline'] . '</td>';
                                    $status = $row['status'];
                                    $status_color = '';

                                    // Set the color based on the status
                                    switch ($status) {
                                        case 'Pending':
                                            $status_color = 'blue';
                                            break;
                                        case 'Done':
                                            $status_color = 'green';
                                            break;
                                        case 'For Approval':
                                            $status_color = 'red';
                                            break;
                                        default:
                                            // Default color if status doesn't match
                                            $status_color = 'black';
                                    }

                                    // Output the status with appropriate color
                                    echo '<td class="' . $status_color . '">' . $status . '</td>';

                                    // Check if status is "For Approval"
                                    if ($row['status'] == 'For Approval') {
                                        // Display the button
                                        echo '<td>';
                                        echo '<form method="post" action="">';
                                        echo '<input type="hidden" name="request_id" value="' . $row['request_id'] . '">';
                                        echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#ForApproval">Approve</button>';
                                        echo '</form>';
                                        echo '</td>';
                                    } else {
                                        // Otherwise, display an empty cell
                                        echo '<td></td>';
                                    }
                                    echo '<td style="display:none;">' . $row['campus'] . '</td>';
                                    echo '<td style="display:none;">' . $row['building'] . '</td>';
                                    echo '<td style="display:none;">' . $row['floor'] . '</td>';
                                    echo '<td style="display:none;">' . $row['room'] . '</td>';
                                    echo '<td style="display:none;">' . $row['description'] . '</td>';
                                    echo '<td style="display:none;">' . $row['req_by'] . '</td>';
                                    echo '<td style="display:none;">' . $row['return_reason'] . '</td>';
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
                                        <th></th>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            if ($result2->num_rows > 0) {
                                echo "<div class='table-container'>";
                                echo "<table>";
                                while ($row2 = $result2->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row2['request_id'] . '</td>';
                                    echo '<td>' . $row2['date'] . '</td>';
                                    echo '<td>' . $row2['category'] . '</td>';
                                    echo '<td>' . $row2['building'] . ', ' . $row2['floor'] . ', ' . $row2['room'] . '</td>';
                                    echo '<td>' . $row2['equipment'] . '</td>';
                                    echo '<td>' . $row2['assignee'] . '</td>';
                                    echo '<td >' . $row2['status'] . '</td>';
                                    echo '<td>' . $row2['deadline'] . '</td>';

                                    // Check if status is "Pending"
                                    if ($row2['status'] == 'Pending') {
                                        // Display the button
                                        echo '<td>';
                                        echo '<form method="post" action="">';
                                        echo '<input type="hidden" name="request_id" value="' . $row2['request_id'] . '">';
                                        echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#ForOutsource">Done</button>';
                                        echo '</form>';
                                        echo '</td>';
                                    } else {
                                        // Otherwise, display an empty cell
                                        echo '<td></td>';
                                    }

                                    echo '<td style="display:none;">' . $row2['campus'] . '</td>';
                                    echo '<td style="display:none;">' . $row2['building'] . '</td>';
                                    echo '<td style="display:none;">' . $row2['floor'] . '</td>';
                                    echo '<td style="display:none;">' . $row2['room'] . '</td>';
                                    echo '<td style="display:none;">' . $row2['description'] . '</td>';
                                    echo '<td style="display:none;">' . $row2['req_by'] . '</td>';
                                    echo '<td style="display:none;">' . $row2['return_reason'] . '</td>';
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
                                        <h5>Add New Request:</h5>
                                        <button class="btn btn-close-modal-emp close-modal-btn"
                                            data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addrequestForm" method="post" class="row g-3">
                                            <div class="col-4" style="display:none;">
                                                <label for="new_request_id" class="form-label">Request ID:</label>
                                                <input type="text" class="form-control" id="new_request_id"
                                                    name="new_request_id" readonly />
                                            </div>
                                            <div class="col-4">
                                                <label for="new_building" class="form-label">Building:</label>
                                                <input type="text" class="form-control" id="new_building"
                                                    name="new_building" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new_floor" class="form-label">Floor:</label>
                                                <input type="text" class="form-control" id="new_floor"
                                                    name="new_floor" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new_room" class="form-label">Room: </label>
                                                <input type="text" class="form-control" id="new_room" name="new_room" />
                                            </div>
                                            <div class="col-4" style="display:none;">
                                                <label for="new_campus" class="form-label">Campus:</label>
                                                <input type="text" class="form-control" id="new_campus"
                                                    name="new_campus" value="Batasan" />
                                            </div>


                                            <div class="col-4">
                                                <label for="new_equipment" class="form-label">Equipment :</label>
                                                <input type="text" class="form-control" id="new_equipment"
                                                    name="new_equipment" />
                                            </div>

                                            <div class="col-4" style="display:none;">
                                                <label for="new_req_by" class="form-label">Requested By: </label>
                                                <input type="text" class="form-control" id="new_req_by"
                                                    name="new_req_by" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new_category" class="form-label">Category:</label>
                                                <select class="form-select" id="new_category" name="new_category"
                                                    onchange="fetchRandomAssignee1()">
                                                    <option value="Outsource">Outsource</option>
                                                    <option value="Carpentry">Carpentry</option>
                                                    <option value="Electrical">Electrical</option>
                                                    <option value="Plumbing">Plumbing</option>

                                                </select>
                                            </div>

                                            <script>
                                                function fetchRandomAssignee1() {
                                                    var category = document.getElementById('new_category').value;
                                                    $.ajax({
                                                        url: 'fetch_random_assignee_request.php', // PHP script to fetch random assignee
                                                        type: 'POST',
                                                        data: {
                                                            category: category
                                                        },
                                                        success: function (response) {
                                                            $('#new_assignee').val(response);
                                                        },
                                                        error: function (xhr, status, error) {
                                                            alert('Error: ' + error);
                                                        }
                                                    });
                                                }
                                            </script>

                                            <div class="col-4">
                                                <label for="new_assignee" class="form-label">Assignee:</label>
                                                <input type="text" class="form-control" id="new_assignee"
                                                    name="new_assignee" />
                                            </div>

                                            <div class="col-4" style="display: none;">
                                                <label for="new_status" class="form-label">Status:</label>
                                                <input type="text" class="form-control" value="Pending" id="new_status"
                                                    name="new_status" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new_deadline" class="form-label">Deadline:</label>
                                                <input type="date" class="form-control" id="new_deadline"
                                                    name="new_deadline" />
                                            </div>

                                            <div class="col-12">
                                                <label for="new_description" class="form-label">Description:</label>
                                                <input type="text" class="form-control" id="new_description"
                                                    name="new_description" />
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                                    data-bs-target="#ForAdd" onclick="showAddConfirmation()">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--add for new request-->
                    <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    Are you sure you want to save changes?
                                    <div class="modal-popups">
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                        <!-- <button class="btn add-modal-btn" name="add"
                                            data-bs-dismiss="modal">Yes</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!--MODAL FOR THE APPROVAL-->
                    <div class="modal-parent">
                        <div class="modal modal-xl fade" id="ForApproval" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>For Approval:</h5>

                                        <button class="btn btn-close-modal-emp close-modal-btn"
                                            data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="approvalForm" method="post" class="row g-3">
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
                                                <input type="text" class="form-control" id="campus" name="campus"
                                                    value="Batasan" />
                                            </div>
                                            <div class="col-4">
                                                <label for="building" class="form-label">Building:</label>
                                                <input type="text" class="form-control" id="building" name="building"
                                                    readonly />
                                            </div>

                                            <div class="col-4">
                                                <label for="floor" class="form-label">Floor:</label>
                                                <input type="text" class="form-control" id="floor" name="floor"
                                                    readonly />
                                            </div>

                                            <div class="col-4">
                                                <label for="room" class="form-label">Room: </label>
                                                <input type="text" class="form-control" id="room" name="room"
                                                    readonly />
                                            </div>

                                            <div class="col-4">
                                                <label for="equipment" class="form-label">Equipment :</label>
                                                <input type="text" class="form-control" id="equipment" name="equipment"
                                                    readonly />
                                            </div>

                                            <div class="col-4" style="display:none;">
                                                <label for="req_by" class="form-label">Requested By: </label>
                                                <input type="text" class="form-control" id="req_by" name="req_by" />
                                            </div>

                                            <div class="col-4">
                                                <label for="category" class="form-label">Category:</label>
                                                <select class="form-select" id="category" name="category"
                                                    onchange="fetchRandomAssignee()">
                                                    <option value="Outsource">Outsource</option>
                                                    <option value="Carpentry">Carpentry</option>
                                                    <option value="Electrical">Electrical</option>
                                                    <option value="Plumbing">Plumbing</option>
                                                </select>
                                            </div>

                                            <!-- Add an empty assignee select element -->
                                            <div class="col-4">
                                                <label id="assignee-label" for="assignee"
                                                    class="form-label">Assignee:</label>
                                                <select class="form-select" id="assignee" name="assignee"></select>

                                                <input type="text" class="form-control" id="assigneeInput"
                                                    name="assignee" style="display: none;">

                                                <input type="text" class="form-control" id="assigneeInputreal"
                                                    name="assigneereal" style="display:none;">
                                            </div>

                                            <div class="col-4" style="display:none;">
                                                <label for="status" class="form-label">Status:</label>
                                                <input type="text" class="form-control" value="Pending"
                                                    id="status_modal" name="status" />
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

                                            <div class="col-12">
                                                <label for="return_reason" class="form-label">Transfer
                                                    Reason:</label>
                                                <input type="text" class="form-control" id="return_reason"
                                                    name="return_reason" readonly />
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                                    data-bs-target="#ForApprovals" onclick="showApprovalConfirmation()">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Edit for approval-->
                    <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    Are you sure you want to save changes?
                                    <div class="modal-popups">
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                        <button class="btn add-modal-btn" name="approval"
                                            data-bs-dismiss="modal">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>

                    <!--MODAL FOR OUTSOURCE-->
                    <div class="modal-parent">
                        <div class="modal modal-xl fade" id="ForOutsource" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>For Outsource:</h5>
                                        <button class="btn btn-close-modal-emp close-modal-btn"
                                            data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="outsourceForm" method="post" class="row g-3">
                                            <div class="col-4" style="display:none;">
                                                <label for="new_request_id" class="form-label">Request ID:</label>
                                                <input type="text" class="form-control" id="new2_request_id"
                                                    name="new2_request_id" readonly />
                                            </div>
                                            <div class="col-4">
                                                <label for="new2_building" class="form-label">Building:</label>
                                                <input type="text" class="form-control" id="new2_building"
                                                    name="new2_building" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new2_floor" class="form-label">Floor:</label>
                                                <input type="text" class="form-control" id="new2_floor"
                                                    name="new2_floor" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new2_room" class="form-label">Room: </label>
                                                <input type="text" class="form-control" id="new2_room"
                                                    name="new2_room" />
                                            </div>
                                            <div class="col-4" style="display:none;">
                                                <label for="new2_campus" class="form-label">Campus:</label>
                                                <input type="text" class="form-control" id="new2_campus"
                                                    name="new2_campus" value="Batasan" />
                                            </div>


                                            <div class="col-4">
                                                <label for="new2_equipment" class="form-label">Equipment :</label>
                                                <input type="text" class="form-control" id="new2_equipment"
                                                    name="new2_equipment" />
                                            </div>

                                            <div class="col-4" style="display:none;">
                                                <label for="new2_req_by" class="form-label">Requested By: </label>
                                                <input type="text" class="form-control" id="new2_req_by"
                                                    name="new2_req_by" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new2_category" class="form-label">Category:</label>
                                                <select class="form-select" id="new2_category" name="new2_category"
                                                    onchange="fetchRandomAssignee1()">
                                                    <option value="Outsource">Outsource</option>
                                                    <option value="Carpentry">Carpentry</option>
                                                    <option value="Electrical">Electrical</option>
                                                    <option value="Plumbing">Plumbing</option>

                                                </select>
                                            </div>

                                            <!-- Add an empty assignee select element -->
                                            <div class="col-4">
                                                <label id="assignee-label" for="assignee"
                                                    class="form-label">Assignee:</label>
                                                <select class="form-select" id="assignee" name="assignee"></select>
                                            </div>

                                            <div class="col-4" style="display: none;">
                                                <label for="new2_status" class="form-label">Status:</label>
                                                <input type="text" class="form-control" value="Pending" id="new2_status"
                                                    name="new2_status" />
                                            </div>

                                            <div class="col-4">
                                                <label for="new2_deadline" class="form-label">Deadline:</label>
                                                <input type="date" class="form-control" id="new2_deadline"
                                                    name="new2_deadline" />
                                            </div>

                                            <div class="col-12">
                                                <label for="new2_description" class="form-label">Description:</label>
                                                <input type="text" class="form-control" id="new2_description"
                                                    name="new2_description" />
                                            </div>


                                            <div class="col-12">
                                                <label for="return_reason" class="form-label">Transfer
                                                    Reason:</label>
                                                <input type="text" class="form-control" id="new2_return_reason"
                                                    name="new2_return_reason" readonly />
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                                    data-bs-target="#ForOutsources"
                                                    onclick="showOutsourceConfirmation()">
                                                    Save
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--edit for outsource-->
                    <div class="modal fade" id="addoutsource" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    Are you sure you want to save changes?
                                    <div class="modal-popups">
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                        <!-- <button class="btn add-modal-btn" name="outsource"
                                            data-bs-dismiss="modal">Yes</button> -->
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
    <script src="../../src/js/batasan.js"></script>



    <script>
        // Get today's date
        var today = new Date();

        // Set tomorrow's date
        var tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        // Format tomorrow's date as yyyy-mm-dd
        var tomorrowFormatted = tomorrow.toISOString().split('T')[0];

        // Set the minimum date of the input field to tomorrow
        document.getElementById("new_deadline").min = tomorrowFormatted;
    </script>

    <!--PARA SA PAGCHANGE NG LABEL-->
    <script>
        function fetchRandomAssignee() {
            // Get the selected category
            var category = document.getElementById('category').value;

            // Get the assignee select and input elements
            var assigneeSelect = document.getElementById('assignee');
            var assigneeInput = document.getElementById('assigneeInput');
            var assigneeInputReal = document.getElementById('assigneeInputreal');

            // Function to update assigneeInputreal value
            function updateAssigneeInputReal(value) {
                assigneeInputReal.value = value;
            }

            // Event listener for assigneeInput
            assigneeInput.addEventListener('input', function () {
                updateAssigneeInputReal(this.value);
            });

            // Check if the selected category is "Outsource"
            if (category === "Outsource") {
                // If it is, show the input and hide the select
                assigneeSelect.style.display = 'none';
                assigneeInput.style.display = 'block';

                // Copy the value from the input to assigneeInputreal
                updateAssigneeInputReal(assigneeInput.value);
            } else {
                // Otherwise, show the select and hide the input
                assigneeInput.style.display = 'none';
                assigneeSelect.style.display = 'block';

                // Make an AJAX request to fetch assignees
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'fetchAssignees.php?category=' + category, true);
                xhr.onload = function () {
                    if (xhr.status == 200) {
                        // Parse the JSON response
                        var assignees = JSON.parse(xhr.responseText);

                        // Clear previous options
                        assigneeSelect.innerHTML = '';

                        // Populate the assignee select element
                        for (var i = 0; i < assignees.length; i++) {
                            var option = document.createElement('option');
                            // Set the option value to concatenated firstName and lastName
                            option.value = assignees[i].firstName + ' ' + assignees[i].lastName;
                            option.textContent = assignees[i].firstName + ' ' + assignees[i].lastName;
                            assigneeSelect.appendChild(option);
                        }

                        // Automatically select the first option if available
                        if (assignees.length > 0) {
                            assigneeSelect.value = assignees[0].firstName + ' ' + assignees[0].lastName;
                            updateAssigneeInputReal(assignees[0].firstName + ' ' + assignees[0].lastName);
                        }

                        // Event listener for assigneeSelect
                        assigneeSelect.addEventListener('change', function () {
                            updateAssigneeInputReal(assigneeSelect.value);
                        });
                    }
                };
                xhr.send();
            }
        }

    </script>



    <!--PANTAWAG SA MODAL TO DISPLAY SA INPUT BOXES-->
    <script>
        $(document).ready(function () {
            // Function to populate modal fields
            function populateModal(row) {
                // Populate modal fields with data from the row
                $("#request_id").val(row.find("td:eq(0)").text());
                $("#date").val(row.find("td:eq(1)").text());
                $("#category").val(row.find("td:eq(2)").text());
                // If building, floor, and room are concatenated in a single cell, split them
                var buildingFloorRoom = row.find("td:eq(3)").text().split(', ');
                $("#building").val(buildingFloorRoom[0]);
                $("#floor").val(buildingFloorRoom[1]);
                $("#room").val(buildingFloorRoom[2]);
                $("#equipment").val(row.find("td:eq(4)").text());
                $("#assignee").val(row.find("td:eq(5)").text());
                $("#status").val(row.find("td:eq(6)").text());
                $("#deadline").val(row.find("td:eq(7)").text());
                $("#description").val(row.find("td:eq(13)").text());
                $("#return_reason").val(row.find("td:eq(15)").text());

                // Check if return_reason has a value
                if (row.find("td:eq(15)").text().trim() !== '') {
                    $("#return_reason").closest('.col-12').show(); // Show the div if there's a value
                } else {
                    $("#return_reason").closest('.col-12').hide(); // Hide the div if there's no value
                }
            }

            // Click event for the "Approve" button
            $("button[data-bs-target='#ForApproval']").click(function () {
                var row = $(this).closest("tr"); // Get the closest row to the clicked button
                populateModal(row); // Populate modal fields with data from the row
                $("#ForApproval").modal("show"); // Show the modal
            });
        });
    </script>

    <!--2 PANTAWAG SA MODAL TO DISPLAY SA INPUT BOXES-->
    <script>
        $(document).ready(function () {
            // Function to populate modal fields for modal "ForOutsource" with data from row 2
            function populateModalForOutsource(row) {
                // Populate modal fields with data from the row
                $("#new2_request_id").val(row.find("td:eq(0)").text());
                $("#new2_building").val(row.find("td:eq(3)").text().split(', ')[0]);
                $("#new2_floor").val(row.find("td:eq(3)").text().split(', ')[1]);
                $("#new2_room").val(row.find("td:eq(3)").text().split(', ')[2]);
                $("#new2_equipment").val(row.find("td:eq(4)").text());
                $("#new2_assignee").val(row.find("td:eq(5)").text());
                $("#new2_category").val(row.find("td:eq(2)").text());
                $("#new2_status").val(row.find("td:eq(6)").text());
                $("#new2_deadline").val(row.find("td:eq(7)").text());
                $("#new2_description").val(row.find("td:eq(13)").text());
                $("#new2_return_reason").val(row.find("td:eq(15)").text());

                // Check if return_reason has a value
                if (row.find("td:eq(15)").text().trim() !== '') {
                    $("#new2_return_reason").closest('.col-12').show(); // Show the div if there's a value
                } else {
                    $("#new2_return_reason").closest('.col-12').hide(); // Hide the div if there's no value
                }
            }

            // Click event for the "Done" button for modal "ForOutsource" based on row 2
            $("button[data-bs-target='#ForOutsource']").click(function () {
                var row = $(this).closest("tr"); // Get the closest row to the clicked button
                populateModalForOutsource(row); // Populate modal fields with data from the row
                $("#ForOutsource").modal("show"); // Show the modal
            });
        });
    </script>







    <script>
        $(document).ready(function () {
            $('.notification-item').on('click', function (e) {
                e.preventDefault();
                var activityId = $(this).data('activity-id');
                var notificationItem = $(this); // Store the clicked element

                $.ajax({
                    type: "POST",
                    url: "single_notification.php", // The URL to the PHP file
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

</body>

</html>