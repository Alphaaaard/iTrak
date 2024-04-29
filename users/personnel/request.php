<?php
session_start();
include_once ("../../config/connection.php");
$conn = connection();

date_default_timezone_set('Asia/Manila');


function insertActivityLog($conn, $accountId, $action)
{
    // Set default values for additional columns
    $tab = "General";
    $seen = 0;
    $m_seen = 0;
    $p_seen = 1;

    $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab, seen, m_seen, p_seen) VALUES (?, NOW(), ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiii", $accountId, $action, $tab, $seen, $m_seen, $p_seen);
    $stmt->execute();
    $stmt->close();
}

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 3) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }
    $accountId = $_SESSION['accountId'];

    // Prepare a statement to count unseen notifications
    $stmt = $conn->prepare("SELECT COUNT(*) AS unseenCount FROM activitylogs WHERE p_seen = '0' AND accountID = ?");
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Now you can echo the count where needed
    $unseenCount = $row['unseenCount'];

    // Fetch Report activity logs
    $loggedInUserFirstName = $_SESSION['firstName']; // or the name field you have in session that you want to check against
    $loggedInUsermiddleName = $_SESSION['middleName']; // assuming you also have the last name in the session
    $loggedInUserLastName = $_SESSION['lastName']; //kung ano ung naka declare dito eto lang ung magiging data 
    // Concatenate first name and last name for the action field check
    $loggedInFullName = $loggedInUserFirstName . " " . $loggedInUsermiddleName . " " . $loggedInUserLastName; //kung ano ung naka declare dito eto lang ung magiging data 



    $sqlGeneral = "SELECT ac.*, a.firstName, a.middleName, a.lastName
    FROM activitylogs AS ac
    LEFT JOIN account AS a ON ac.accountID = a.accountID
    WHERE (ac.tab = 'General' AND ac.action LIKE 'Assigned maintenance personnel%' AND ac.action LIKE ?)
    ORDER BY ac.date DESC";

    // Prepare the SQL statement
    $stmtg = $conn->prepare($sqlGeneral);

    // Bind the parameter and execute
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";
    $stmtg->bind_param("s", $pattern);
    $stmtg->execute();
    $resultGeneral = $stmtg->get_result();



    // Adjust the SQL to check the 'action' field for the logged-in user's name
    $sqlReport = "SELECT ac.*, a.firstName, a.middleName, a.lastName
    FROM activitylogs AS ac
    LEFT JOIN account AS a ON ac.accountID = a.accountID
    WHERE (ac.tab = 'Report' AND ac.accountID = ?)

    ORDER BY ac.date DESC";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sqlReport);

    // Bind the parameter and execute

    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $resultReport = $stmt->get_result();

    // Assuming $loggedInAccountId contains the ID of the logged-in user

    // Modify the first query to filter by the logged-in account ID
    $sql01 = "SELECT r.* FROM request r
    INNER JOIN account a ON r.assignee = CONCAT(a.firstName, ' ', a.lastName)
    WHERE r.campus IN ('Batasan', 'San Bartolome', 'San Francisco') 
    AND r.status IN ( 'For Approval', 'Pending') AND a.accountId = ?";
    $stmt01 = $conn->prepare($sql01);
    $stmt01->bind_param("i", $accountId);
    $stmt01->execute();
    $result01 = $stmt01->get_result();

    // Modify the first query to filter by the logged-in account ID
    $sql06 = "SELECT r.* FROM request r
    INNER JOIN account a ON r.assignee = CONCAT(a.firstName, ' ', a.lastName)
    WHERE r.campus IN ('Batasan', 'San Bartolome', 'San Francisco') 
    AND r.status = 'Done' AND a.accountId = ?";
    $stmt06 = $conn->prepare($sql06);
    $stmt06->bind_param("i", $accountId);
    $stmt06->execute();
    $result06 = $stmt06->get_result();

    // Fetch data from "request" table for "Outsource" status based on the logged-in user's name
    $sql02 = "SELECT r.* FROM request r
INNER JOIN account a ON r.assignee = CONCAT(a.firstName, ' ', a.lastName)
WHERE r.campus = 'Batasan' AND r.status = 'Outsource' AND a.accountId = ?";
    $stmt02 = $conn->prepare($sql02);
    $stmt02->bind_param("i", $accountId);
    $stmt02->execute();
    $result02 = $stmt02->get_result();

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
               WHERE al.tab = 'General' AND al.p_seen = '0' AND al.action LIKE 'Assigned maintenance personnel%' AND al.action LIKE ? AND al.accountID != ?
               ORDER BY al.date DESC 
               LIMIT 5"; // Set limit to 5

    // Prepare the SQL statement
    $stmtLatestLogs = $conn->prepare($sqlLatestLogs);
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

    // Bind the parameter to exclude the current user's account ID
    $stmtLatestLogs->bind_param("si", $pattern, $loggedInAccountId);

    // Execute the query
    $stmtLatestLogs->execute();
    $resultLatestLogs = $stmtLatestLogs->get_result();

    $unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs 
WHERE p_seen = '0' AND accountID != ? AND action LIKE 'Assigned maintenance personnel%' AND action LIKE ?";
    $pattern = "%Assigned maintenance personnel $loggedInUserFirstName%";

    $stmt = $conn->prepare($unseenCountQuery);
    $stmt->bind_param("is", $loggedInAccountId, $pattern);
    $stmt->execute();
    $stmt->bind_result($unseenCount);
    $stmt->fetch();
    $stmt->close();


    $sql2 = "SELECT ac.*, a.firstName, a.middleName, a.lastName 
  FROM activitylogs AS ac
  LEFT JOIN account AS a ON ac.accountID = a.accountID";
    $result = $conn->query($sql2) or die($conn->error);

    $sql2 = "SELECT * FROM reportlogs";
    $result2 = $conn->query($sql2) or die($conn->error);
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

    // UPDATE FOR APPROVAL
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
        $assignee2 = $_POST['assignee'];
        $status2 = "For Approval";
        $description2 = $_POST['description'];
        $deadline2 = $_POST['deadline'];
    
        // Calculate the current date plus 8 hours
        $adjusted_date = date('Y-m-d H:i:s', strtotime('+0 hours'));
    
        // Retrieve selected return_reason from radio buttons
        $return_reason = $_POST['return_reason'];
        // Check if status is being updated from pending to for approval
        $status_before_update_query = "SELECT status FROM request WHERE request_id = ?";
        $stmt_check_status = $conn->prepare($status_before_update_query);
        $stmt_check_status->bind_param("i", $request_id2);
        $stmt_check_status->execute();
        $result_status = $stmt_check_status->get_result();
        $row_status = $result_status->fetch_assoc();
        $status_before_update = $row_status['status'];
        $stmt_check_status->close();
    
        // SQL UPDATE query
        $sql3 = "UPDATE request 
                 SET campus = ?, building = ?, floor = ?, room = ?, 
                     equipment = ?, category = ?, assignee = ?, 
                     status = ?, description = ?, deadline = ?,
                     return_reason = ?, date = ?
                 WHERE request_id = ?";
    
        // Prepare the SQL statement
        $stmt3 = $conn->prepare($sql3);
    
        // Bind parameters
        $stmt3->bind_param("ssssssssssssi", $campus2, $building2, $floor2, $room2, $equipment2, $category2, $assignee2, $status2, $description2, $deadline2, $return_reason, $adjusted_date, $request_id2);
    
        // Execute the query
        if ($stmt3->execute()) {
            // Check if status changed from pending to for approval
            if ($status_before_update == 'Pending' && $status2 == 'For Approval') {
                // Log the activity
                $action = "Changed status of Task ID $request_id2 from Pending to For Approval";
                insertActivityLog($conn, $accountId, $action);
            }

            // Update successful, redirect back to request.php or any other page
            header("Location: request.php");
            exit();
        } else {
            // Error occurred while updating
            echo "Error updating request: " . $stmt3->error;
        }

        // Close statement
        $stmt3->close();
    }

    // UPDATE FOR DONE
    if (isset($_POST['done'])) {
        // Retrieve request_id from the form
        $request_id5 = $_POST['request_id'];
    
        // Retrieve other form data
        $campus5 = $_POST['campus'];
        $building5 = $_POST['building'];
        $floor5 = $_POST['floor'];
        $room5 = $_POST['room'];
        $equipment5 = $_POST['equipment'];
        $category5 = $_POST['category'];
        $assignee5 = $_POST['assignee'];
        $status5 = "Done";
        $description5 = $_POST['description'];
        $deadline5 = $_POST['deadline'];
    
        // Calculate the current date plus 8 hours
        $adjusted_date = date('Y-m-d H:i:s', strtotime('+0 hours'));

        // Retrieve selected return_reason from radio buttons
        $return_reason5 = $_POST['return_reason'];

        $status_before_update_query = "SELECT status FROM request WHERE request_id = ?";
    $stmt_check_status = $conn->prepare($status_before_update_query);
    $stmt_check_status->bind_param("i", $request_id5);
    $stmt_check_status->execute();
    $result_status = $stmt_check_status->get_result();
    $row_status = $result_status->fetch_assoc();
    $status_before_update = $row_status['status'];
    $stmt_check_status->close();


        // SQL UPDATE query
        $sql5 = "UPDATE request 
        SET campus = ?, building = ?, floor = ?, room = ?, 
            equipment = ?, category = ?, assignee = ?, 
            status = ?, description = ?, deadline = ?,
            return_reason = ?, date = ?
        WHERE request_id = ?";

        // Prepare the SQL statement
        $stmt5 = $conn->prepare($sql5);

        
      

    // Bind parameters
    $stmt5->bind_param("ssssssssssssi", $campus5, $building5, $floor5, $room5, $equipment5, $category5, $assignee5, $status5, $description5, $deadline5, $return_reason5, $adjusted_date, $request_id5);

        // Execute the query
        if ($stmt5->execute()) {
            // Check if status changed from pending to done
            if ($status_before_update == 'Pending' && $status5 == 'Done') {
                // Log the activity
                $action = "Changed status of Task ID $request_id5 from Pending to Done";
                insertActivityLog($conn, $accountId, $action);
            }

            // Update successful, redirect back to batasan.php or any other page
            header("Location: request.php");
            exit();
        } else {
            // Error occurred while updating
            echo "Error updating request: " . $stmt5->error;
        }

        // Close statement
        $stmt5->close();
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
        <link rel="stylesheet" href="../../src/css/request.css" />
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
    <!-- SIDEBAR -->
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
            <li class="active">
                <a href="./request.php">
                    <i class="bi bi-receipt"></i>
                    <span class="text">Request</span>
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
                            <li><a href="#" class="nav-link" data-bs-target="pills-done">Done</a></li>

                        </ul>
                    </div>

                    <!-- Export button -->
                    <div class="export-mob-hide">
                        <form method="post" id="exportForm">
                            <input type="hidden" name="status" id="statusField" value="For Replacement">

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
                                        <th>Deadline</th>
                                        <th>Status</th>

                                        <th></th>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            if ($result01->num_rows > 0) {
                                echo "<div class='table-container'>";
                                echo "<table>";
                                while ($row = $result01->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['request_id'] . '</td>';
                                    echo '<td>' . $row['date'] . '</td>';
                                    echo '<td>' . $row['category'] . '</td>';
                                    echo '<td>' . $row['building'] . ', ' . $row['floor'] . ', ' . $row['room'] . '</td>';
                                    echo '<td>' . $row['equipment'] . '</td>';
                                    echo '<td style="display:none;">' . $row['assignee'] . '</td>';
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
                                    echo '<td>';
                                    echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#ForView">View</button>';
                                    echo '</td>';
                                    echo '<td style="display:none;">' . $row['campus'] . '</td>';
                                    echo '<td style="display:none;">' . $row['building'] . '</td>';
                                    echo '<td style="display:none;">' . $row['floor'] . '</td>';
                                    echo '<td style="display:none;">' . $row['room'] . '</td>';
                                    echo '<td style="display:none;">' . $row['description'] . '</td>';
                                    echo '<td style="display:none;">' . $row['req_by'] . '</td>';
                                    echo '<td style="display:none;">' . $row['return_reason'] . '</td>';
                                    echo '<td></td>';
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

                    <div class="tab-pane fade" id="pills-done" role="tabpanel" aria-labelledby="done-tab">
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

                                    </tr>
                                </table>
                            </div>
                            <?php
                            if ($result06->num_rows > 0) {
                                echo "<div class='table-container'>";
                                echo "<table>";
                                while ($row6 = $result06->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row6['request_id'] . '</td>';
                                    echo '<td>' . $row6['date'] . '</td>';
                                    echo '<td>' . $row6['category'] . '</td>';
                                    echo '<td>' . $row6['building'] . ', ' . $row6['floor'] . ', ' . $row6['room'] . '</td>';
                                    echo '<td>' . $row6['equipment'] . '</td>';
                                    echo '<td>' . $row6['assignee'] . '</td>';
                                    echo '<td>' . $row6['deadline'] . '</td>';
                                    echo '<td >' . $row6['status'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['campus'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['building'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['floor'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['room'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['description'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['req_by'] . '</td>';
                                    echo '<td style="display:none;">' . $row6['return_reason'] . '</td>';
                                    echo '<td></td>';
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



                    <!--MODAL FOR THE VIEW-->
                    <!-- <form action="POST" id=""> -->
                    <div class="modal-parent">
                        <div class="modal modal-xl fade" id="ForView" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>View Task</h5>
                                        <button class="btn btn-close-modal-emp close-modal-btn"
                                            data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                    </div>

                                    <div class="modal-body">
                                        <form id="requestForm" method="post" class="row g-3">
                                            <div class="col-4" style="display:none;">
                                                <label for="request_id" class="form-label">Request ID:</label>
                                                <input type="text" class="form-control" id="request_id"
                                                    name="request_id" readonly />
                                            </div>
                                            <div class="col-4" style="display:none;">
                                                <label for="date" class="form-label">Date & Time:</label>
                                                <input type="text" class="form-control" id="date" name="date"
                                                    readonly />
                                            </div>
                                            <div class="col-4" style="display:none;">
                                                <label for="campus" class="form-label">Campus:</label>
                                                <input type="text" class="form-control" id="campus" name="campus" />
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
                                                <input type="text" class="form-control" id="category" name="category"
                                                    value="Carpentry" readonly>
                                            </div>


                                            <div class="col-4" style="display:none;">
                                                <label id="assignee-label" for="assignee"
                                                    class="form-label">Assignee:</label>
                                                <input type="text" class="form-control" id="assignee" name="assignee"
                                                    readonly />
                                            </div>

                                            <div class="col-4" style="display:none;">
                                                <label for="status" class="form-label">Status:</label>
                                                <input type="text" class="form-control" id="status" name="status"
                                                    readonly />
                                            </div>


                                            <div class="col-4">
                                                <label for="deadline" class="form-label">Deadline:</label>
                                                <input type="date" class="form-control" id="deadline" name="deadline"
                                                    readonly />
                                            </div>

                                            <div class="col-12">
                                                <label for="description" class="form-label">Description:</label>
                                                <input type="text" class="form-control" id="description"
                                                    name="description" readonly />
                                            </div>

                                            <div class="col-12" style="display:none">
                                                <label for="return_reason_show" class="form-label">Transfer
                                                    Reason:</label>
                                                <input type="text" class="form-control" id="return_reason_show"
                                                    name="return_reason" readonly />
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" id="transferBtn"
                                                    data-bs-toggle="modal" data-bs-target="#ForTransfer">
                                                    Transfer
                                                </button>
                                                <button type="button" class="btn add-modal-btn" id="doneBtn"
                                                    data-bs-toggle="modal" data-bs-target="#ForDones"
                                                    onclick="showTaskConfirmation()">
                                                    Done
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--MODAL FOR THE TRANSFER-->
                    <div class="modal-parent">
                        <div class="modal modal-xl fade" id="ForTransfer" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Transfer Task</h5>

                                        <button class="btn btn-close-modal-emp close-modal-btn"
                                            data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                    <div class="modal-body" id="transfer-body">

                                        <div class="col-12">
                                            <label class="form-label">Select a reason:</label>
                                        </div>

                                        <div class="col-12" id="transfer-options">
                                            <div class="form-check">
                                                <div>
                                                    <input class="form-check-input" type="radio" value="Lack of Tools"
                                                        id="reason_lack_of_tools" name="reason"
                                                        onchange="updateTextInput(this)">
                                                    <label class="form-check-label" for="reason_lack_of_tools">Lack of
                                                        Tools</label>

                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    value="Insufficient Personnel" id="reason_insufficient_personnel"
                                                    name="reason" onchange="updateTextInput(this)">
                                                <label class="form-check-label"
                                                    for="reason_insufficient_personnel">Insufficient Personnel</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="Skills Mismatch"
                                                    id="reason_skills_mismatch" name="reason"
                                                    onchange="updateTextInput(this)">
                                                <label class="form-check-label" for="reason_skills_mismatch">Skills
                                                    Mismatch</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    value="Coordination with Other Departments"
                                                    id="reason_coordination_with_other_departments" name="reason"
                                                    onchange="updateTextInput(this)">
                                                <label class="form-check-label"
                                                    for="reason_coordination_with_other_departments">Coordination with
                                                    Other Departments</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="Outsource"
                                                    id="reason_outsource" name="reason"
                                                    onchange="updateTextInput(this)">
                                                <label class="form-check-label" for="reason_outsource">Outsource</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="reason_others" value=""
                                                    name="reason" onchange="updateTextInput(this)">
                                                <label class=" form-check-label" for="reason_others">Others</label>
                                            </div>
                                        </div>

                                        <div class="col-12" id="othersInput" style="display:none;">
                                            <label for="description" class="form-label">Others:</label>
                                            <textarea class="form-control" id="return_reason"
                                                name="return_reason"></textarea>
                                        </div>
                                    </div>

                                    <div class="footer" id="transfer-footer">
                                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                                            data-bs-target="#ForSaves" onclick="showTransferConfirmation()">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Edit for done-->
                    <div class="modal fade" id="ForDone" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-footer">
                                    Are you sure you want to mark this task as completed?
                                    <div class="modal-popups">
                                        <button type="button" class="btn close-popups"
                                            data-bs-dismiss="modal">No</button>
                                        <!-- <button class="btn add-modal-btn" name="done" data-bs-dismiss="modal">Yes</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
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
    <script src="../../src/js/requestPersonnel.js"></script>


    <!-- PARA SA PAGLAGAY NG VALUE SA OTHER FIELD TYPE ALSO SA PAGAPPEAR NG OTHER DESCRIPTION BOX -->
    <script>
        function updateTextInput(radio) {
            console.log("updateTextInput function called");
            // Get the input field for "Others"
            var othersInput = document.getElementById('othersInput');
            console.log("othersInput:", othersInput);

            // If the radio button for "Others" is checked, display the input field; otherwise, hide it
            if (radio.checked && radio.value === '') {
                othersInput.style.display = 'block'; // Display the input field
            } else {
                othersInput.style.display = 'none'; // Hide the input field
            }

            // Update the value of the text input based on the selected radio button
            document.getElementById('return_reason').value = radio.value;
            console.log("return_reason value:", radio.value);
        }
    </script>

    <script>
        // Function to disable/enable transfer and done buttons based on status
        function updateButtons() {
            var statusInput = document.getElementById('status');
            var transferBtn = document.getElementById('transferBtn');
            var doneBtn = document.getElementById('doneBtn');

            if (statusInput.value === 'For Approval' || statusInput.value === 'Done') {
                transferBtn.disabled = true;
                doneBtn.disabled = true;
            } else {
                transferBtn.disabled = false;
                doneBtn.disabled = false;
            }
        }

        // Call updateButtons function when modal is shown or status input changes
        document.addEventListener('DOMContentLoaded', function () {
            // Call updateButtons when modal is shown
            $('#ForView').on('shown.bs.modal', function () {
                updateButtons();
            });

            // Call updateButtons when status input changes
            document.getElementById('status').addEventListener('change', function () {
                updateButtons();
            });
        });
    </script>





    <!--PANTAWAG SA MODAL TO DISPLAY SA INPUT BOXES-->
    <!-- <script>
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
                $("#return_reason_show").val(row.find("td:eq(15)").text());

            }

            // Click event for the "Approve" button
            $("button[data-bs-target='#ForView']").click(function () {
                var row = $(this).closest("tr"); // Get the closest row to the clicked button
                populateModal(row); // Populate modal fields with data from the row
                $("#ForView").modal("show"); // Show the modal
            });
        });
    </script> -->

    <script>
        $(document).ready(function () {
            // Function to populate modal fields
            function populateModal(row) {
                // Populate modal fields with data from the row
                $("#request_id").val(row.find("td:eq(0)").text());
                $("#date").val(row.find("td:eq(1)").text());
                $("#category").val(row.find("td:eq(2)").text());
                $("#campus").val(row.find("td:eq(9)").text());
                // If building, floor, and room are concatenated in a single cell, split them
                var buildingFloorRoom = row.find("td:eq(3)").text().split(', ');
                $("#building").val(buildingFloorRoom[0]);
                $("#floor").val(buildingFloorRoom[1]);
                $("#room").val(buildingFloorRoom[2]);
                $("#equipment").val(row.find("td:eq(4)").text());
                $("#assignee").val(row.find("td:eq(5)").text());
                $("#status").val(row.find("td:eq(7)").text());
                $("#deadline").val(row.find("td:eq(6)").text());
                $("#description").val(row.find("td:eq(13)").text());
                $("#return_reason_show").val(row.find("td:eq(15)").text());
            }

            // Click event for the "View" button
            $("button[data-bs-target='#ForView']").click(function () {
                var row = $(this).closest("tr"); // Get the closest row to the clicked button
                populateModal(row); // Populate modal fields with data from the row

                // Check if return_reason_show input has a value
                if ($("#return_reason_show").val().trim() !== '') {
                    // If it has a value, remove the display:none style
                    $("#return_reason_show").closest(".col-12").show();
                } else {
                    // If it's empty, keep it hidden
                    $("#return_reason_show").closest(".col-12").hide();
                }

                $("#ForView").modal("show"); // Show the modal
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
    <script>
        // Get the input elements
        const descriptionInput = document.getElementById('description');
        const reasonInput = document.getElementById('return_reason_show');

        // Create textarea elements
        const descriptionTextarea = document.createElement('textarea');
        const reasonTextarea = document.createElement('textarea');

        // Copy attributes from input to textarea
        descriptionTextarea.setAttribute('id', 'description');
        descriptionTextarea.setAttribute('name', 'description');
        descriptionTextarea.setAttribute('class', 'form-control');
        descriptionTextarea.setAttribute('readonly', 'true');
        descriptionTextarea.textContent = descriptionInput.value;

        reasonTextarea.setAttribute('id', 'return_reason_show');
        reasonTextarea.setAttribute('name', 'return_reason');
        reasonTextarea.setAttribute('class', 'form-control');
        reasonTextarea.setAttribute('readonly', 'true');
        reasonTextarea.textContent = reasonInput.value;

        // Replace inputs with textareas
        descriptionInput.parentNode.replaceChild(descriptionTextarea, descriptionInput);
        reasonInput.parentNode.replaceChild(reasonTextarea, reasonInput);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

</body>

</html>