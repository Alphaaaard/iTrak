<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

session_start();
include_once("../../config/connection.php");
date_default_timezone_set('Asia/Manila');
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

    // SQL query to retrieve tasks for the San Bartolome campus
    $sql = "SELECT * FROM request 
   WHERE campus = 'San Bartolome' 
   AND (status IN ('Pending', 'For Approval', 'Overdue') OR 
        (status = 'Overdue' AND deadline < CURDATE() AND deadline IS NOT NULL AND deadline != '0000-00-00'))
   AND category != 'Outsource' 
   ORDER BY date DESC";
    $result = $conn->query($sql) or die($conn->error);

    // SQL query to retrieve tasks for the San Bartolome campus with category 'Outsource'
    $sql2 = "SELECT * FROM request 
    WHERE campus = 'San Bartolome' 
    AND (status IN ('Pending', 'Overdue') OR 
         (status = 'Overdue' AND deadline < CURDATE() AND deadline IS NOT NULL AND deadline != '0000-00-00'))
    AND category = 'Outsource' 
    ORDER BY date DESC";
    $result2 = $conn->query($sql2) or die($conn->error);

    // SQL query to retrieve tasks for the San Bartolome campus that are 'Done'
    $sql4 = "SELECT * FROM request 
    WHERE campus = 'San Bartolome' 
    AND status = 'Done' 
    ORDER BY date DESC";
    $result4 = $conn->query($sql4) or die($conn->error);




    function logActivity($conn, $accountId, $actionDescription, $tabValue)
    {
        // Add 8 hours to the current date
        $date = date('Y-m-d H:i:s', strtotime('+0 hours'));

        $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab, seen, m_seen, p_seen) VALUES (?, ?, ?, ?, 1, 1, 1)");
        $stmt->bind_param("isss", $accountId, $date, $actionDescription, $tabValue);
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
        $outsource_info = $_POST['new_outsource_info'];
        $first_assignee = $_POST['new_first_assignee'];
        $admins_remark = $_POST['new_admins_remark'];
        $mp_remark = $_POST['new_mp_remark'];

        // Calculate the current date plus 8 hours
        $adjusted_date = date('Y-m-d H:i:s', strtotime('+0 hours'));

        // Insert data into the request table
        $insertQuery = "INSERT INTO request (request_id, campus, building, floor, room, equipment, req_by, category, assignee, status, description, deadline, date, outsource_info, first_assignee, admins_remark, mp_remark)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssssssssssssssss", $request_id, $campus, $building, $floor, $room, $equipment, $req_by, $category, $assignee, $status, $description, $deadline, $adjusted_date, $outsource_info, $first_assignee, $admins_remark, $mp_remark);

        // Execute the statement


        // Rest of your code after insertion


        if ($stmt->execute()) {
            // Log activity for task creation and assignment
            $action = "$nomiddlename Created and assigned task to $assignee.";
            logActivity($conn, $_SESSION['accountId'], $action, 'General');

            // Redirect to the desired page
            header("Location: sanBartolome.php");
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
        $outsource_info2 = $_POST['outsource_info'];
        $first_assignee2 = $_POST['first_assignee'];
        $admins_remark2 = $_POST['admins_remark'];
        // Calculate the current date plus 8 hours
        $adjusted_date = date('Y-m-d H:i:s', strtotime('+0 hours'));

        // SQL UPDATE query
        $sql3 = "UPDATE request 
   SET campus = ?, building = ?, floor = ?, room = ?, 
       equipment = ?, category = ?, assignee = ?, 
       status = ?, description = ?, deadline = ?, outsource_info = ?,first_assignee = ?, admins_remark = ?, date = ?
   WHERE request_id = ?";

        // Prepare the SQL statement
        $stmt3 = $conn->prepare($sql3);

        // Bind parameters
        $stmt3->bind_param("ssssssssssssssi", $campus2, $building2, $floor2, $room2, $equipment2, $category2, $assignee2, $status2, $description2, $deadline2, $outsource_info2, $first_assignee2, $admins_remark2, $adjusted_date, $request_id2);
        if ($stmt3->execute()) {
            // Log activity for admin approval with new assignee
            $approval_action = "$nomiddlename approved Task ID $request_id2 with $assignee2 as new assignee.";
            $reassignment_action = "Task ID $request_id2 reassigned to $assignee2.";
            logActivity($conn, $_SESSION['accountId'], $approval_action, 'General');
            logActivity($conn, $_SESSION['accountId'], $reassignment_action, 'General');

            // Redirect back to the page
            header("Location: sanBartolome.php");

            exit();
        } else {
            // Error occurred while updating
            echo "Error updating request: " . $stmt3->error;
        }

        // Close statement
        $stmt3->close();
    }

    if (isset($_POST['outsource'])) {
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

        // Calculate the current date plus 8 hours
        $adjusted_date = date('Y-m-d H:i:s', strtotime('+0 hours'));

        // Update data in the request table
        $updateQuery = "UPDATE request SET campus=?, building=?, floor=?, room=?, equipment=?, req_by=?, category=?, assignee=?, status=?, description=?, deadline=?, date=? WHERE request_id=?";

        $stmt4 = $conn->prepare($updateQuery);

        // Bind parameters
        $stmt4->bind_param("ssssssssssssi", $campus4, $building4, $floor4, $room4, $equipment4, $req_by4, $category4, $assignee4, $status4, $description4, $deadline4, $adjusted_date, $request_id4);

        // Execute the query
        if ($stmt4->execute()) {
            // Log activity for admin approval with outsource as new assignee
            $action4 = "$nomiddlename change the status of $request_id4  as Completed.";

            logActivity($conn, $_SESSION['accountId'], $action4, 'General');

            // Redirect to the desired page
            header("Location: sanBartolome.php");
            exit(); // Make sure to exit to prevent further execution
        } else {
            echo "Error updating data: " . $conn->error;
        }

        $conn->close();
    }



    // Function to send email notifications for approaching deadlines
    function sendDeadlineNotifications($conn)
    {
        // Set up the time threshold to 1 day before the deadline
        $deadlineThreshold = date('Y-m-d', strtotime(' +1 day'));

        // Query tasks with approaching deadlines based on the input date from the modal form
        $sql = "SELECT req.*, acc.email 
           FROM request AS req 
           JOIN account AS acc ON req.assignee = CONCAT(acc.firstName, ' ', acc.lastName)
           WHERE req.deadline = ? AND req.notification_sent = 0"; // Add condition to check if notification has not been sent
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $deadlineThreshold);
        $stmt->execute();
        $result = $stmt->get_result();

        // Iterate through results and send email notifications
        while ($row = $result->fetch_assoc()) {
            $assigneeEmail = $row['email'];
            $taskDescription = $row['description'];

            // Create a new PHPMailer instance
            $mail = new PHPMailer;

            // Set up SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'qcu.upkeep@gmail.com';
            $mail->Password = 'qvpx bbcm bgmy hcvf';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('qcu.upkeep@gmail.com', 'iTrak');
            $mail->addAddress($assigneeEmail);
            $mail->Subject = 'Deadline Reminder: ' . $taskDescription;
            $mail->Body = 'This is a reminder that the deadline for task "' . $taskDescription . '" is approaching. Please ensure it is completed on time.';

            // Send the email
            if (!$mail->send()) {
                echo 'Email could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                // Mark the task as notification sent
                $taskId = $row['request_id'];
                $updateSql = "UPDATE request SET notification_sent = 1 WHERE request_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("i", $taskId);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }
    }

    // Call the function when needed, for example in your PHP script:
    sendDeadlineNotifications($conn);


    // Function to update status to "Overdue" for tasks with overdue deadlines
    function updateOverdueTasks($conn)
    {
        // Get today's date
        $today = date('Y-m-d');

        // SQL query to update status for overdue tasks
        $sql = "UPDATE request SET status = 'Overdue' WHERE deadline < ? AND deadline IS NOT NULL AND deadline != '0000-00-00' AND status != 'Done'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "s",
            $today
        );
        $stmt->execute();
        $stmt->close();
    }

    // Call the function to update overdue tasks
    updateOverdueTasks($conn);




?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Request</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/archive.css" />
        <link rel="stylesheet" href="../../src/css/reports.css" />
        <link rel="stylesheet" href="../../src/css/admin-request.css" />

        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
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

                $(".nav-link").click(function() {
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
        <section id="sidebar">
            <a href="#" class="brand" title="logo">
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
                    <li class="Map-Batasan">
                        <a href="./batasan.php">
                            <i class="bi bi-building"></i>
                            <span class="text">Batasan</span>
                        </a>
                    </li>
                    <li class="Map-SanBartolome  active">
                        <a href="./sanBartolome.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Bartolome</span>
                        </a>
                    </li>
                    <li class="Map-SanFrancisco ">
                        <a href="./sanFrancisco.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Francisco</span>
                        </a>
                    </li>
                </div>
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
                                <select id="status-filter">
                                    <option value="all">Choose a status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="For Approval">For Approval</option>
                                    <option value="Overdue">Overdue</option>
                                </select>
                                <form class="d-flex" role="search" id="searchForm">
                                    <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" name="q" />
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
                                <li><a href="#" class="nav-link" data-bs-target="pills-done">Done</a></li>
                            </ul>
                        </div>

                        <!-- Export button -->
                        <div class="export-mob-hide">
                            <form method="post" id="exportForm">
                                <input type="hidden" name="status" id="statusField" value="For Replacement">
                                <button type="button" id="exportBtn" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#addRequest">Add Task</button>
                            </form>
                        </div>
                    </div>


                    <div class="tab-content pt" id="myTabContent">
                        <!--TABLE FOR REQUEST-->
                        <div class="tab-pane fade show active" id="pills-manager" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>REQUEST ID</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>EQUIPMENT</th>
                                            <th>ASSIGNEE</th>
                                            <th>DEADLINE</th>
                                            <th>STATUS</th>
                                            <th></th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row = $result->fetch_assoc()) {
                                        // Check if the status is "Overdue"
                                        $status = $row['status'];
                                        $row_class = ($status == 'Overdue') ? 'past-due-row' : '';

                                        // Output the table row with the appropriate CSS class
                                        echo '<tr class="' . $row_class . '">';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['request_id'] . '</td>';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['date'] . '</td>';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['category'] . '</td>';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['building'] . ', ' . $row['floor'] . ', ' . $row['room'] . '</td>';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['equipment'] . '</td>';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['assignee'] . '</td>';
                                        echo '<td style="color: ' . (($status == 'Overdue') ? 'red' : 'black') . ';">' . $row['deadline'] . '</td>';
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
                                                $status_color = 'orange';
                                                break;
                                            case 'Overdue':
                                                $status_color = 'red'; // Choose an appropriate color for Overdue tasks
                                                break;
                                            default:
                                                // Default color if status doesn't match
                                                $status_color = 'black';
                                        }

                                        // Output the status with appropriate color
                                        echo '<td class="status-cell ' . $status_color . '">' . $status . '</td>';


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
                                        echo '<td style="display:none;">' . $row['outsource_info'] . '</td>';
                                        echo '<td style="display:none;">' . $row['first_assignee'] . '</td>';
                                        echo '<td style="display:none;">' . $row['admins_remark'] . '</td>';
                                        echo '<td style="display:none;">' . $row['mp_remark'] . '</td>';

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

                        <!--TABLE FOR OUTSOURCE-->
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>REQUEST ID</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>EQUIPMENT</th>
                                            <th>ASSIGNEE</th>
                                            <th>DEADLINE</th>
                                            <th>STATUS</th>
                                            <th></th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                if ($result2->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row2 = $result2->fetch_assoc()) {
                                        // Check if the status is "Overdue"
                                        $status2 = $row2['status'];
                                        $row_class2 = ($status2 == 'Overdue') ? 'past-due-row' : '';
                                        echo '<tr class="' . $row_class2 . '">';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['request_id'] . '</td>';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['date'] . '</td>';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['category'] . '</td>';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['building'] . ', ' . $row2['floor'] . ', ' . $row2['room'] . '</td>';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['equipment'] . '</td>';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['assignee'] . '</td>';
                                        echo '<td style="color: ' . (($status2 == 'Overdue') ? 'red' : 'black') . ';">' . $row2['deadline'] . '</td>';
                                        $status = $row2['status'];
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
                                                $status_color = 'orange';
                                                break;
                                            case 'Overdue':
                                                $status_color = 'red'; // Choose an appropriate color for Overdue tasks
                                                break;
                                            default:
                                                // Default color if status doesn't match
                                                $status_color = 'black';
                                        }

                                        // Output the status with appropriate color
                                        echo '<td class="status-cell ' . $status_color . '">' . $status . '</td>';

                                        // Check if status is "Pending"
                                        if ($row2['status'] == 'Pending') {
                                            // Display the button
                                            echo '<td>';
                                            echo '<form method="post" action="">';
                                            echo '<input type="hidden" name="request_id" value="' . $row2['request_id'] . '">';
                                            echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#ForOutsource">View</button>';
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
                                        echo '<td style="display:none;">' . $row2['outsource_info'] . '</td>';
                                        echo '<td style="display:none;">' . $row2['first_assignee'] . '</td>';
                                        echo '<td style="display:none;">' . $row2['admins_remark'] . '</td>';
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

                        <!--TABLE FOR DONE-->
                        <div class="tab-pane fade" id="pills-done" role="tabpanel" aria-labelledby="done-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>REQUEST ID</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>EQUIPMENT</th>
                                            <th>ASSIGNEE</th>
                                            <th>DEADLINE</th>
                                            <th>STATUS</th>
                                            <th></th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                if ($result4->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row4 = $result4->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . $row4['request_id'] . '</td>';
                                        echo '<td>' . $row4['date'] . '</td>';
                                        echo '<td>' . $row4['category'] . '</td>';
                                        echo '<td>' . $row4['building'] . ', ' . $row4['floor'] . ', ' . $row4['room'] . '</td>';
                                        echo '<td>' . $row4['equipment'] . '</td>';
                                        echo '<td>' . $row4['assignee'] . '</td>';
                                        echo '<td>' . $row4['deadline'] . '</td>';
                                        $status = $row4['status'];
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
                                                $status_color = 'orange';
                                                break;
                                            case 'Overdue':
                                                $status_color = 'red'; // Choose an appropriate color for Overdue tasks
                                                break;
                                            default:
                                                // Default color if status doesn't match
                                                $status_color = 'black';
                                        }

                                        // Output the status with appropriate color
                                        echo '<td class="' . $status_color . '">' . $status . '</td>';

                                        // Check if status is "Done"
                                        if ($row4['status'] == 'Done') {
                                            // Display the button
                                            echo '<td>';
                                            echo '<form method="post" action="">';
                                            echo '<input type="hidden" name="request_id" value="' . $row4['request_id'] . '">';
                                            echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#ForViewDone">View</button>';
                                            echo '</form>';
                                            echo '</td>';
                                        } else {
                                            // Otherwise, display an empty cell
                                            echo '<td></td>';
                                        }

                                        echo '<td style="display:none;">' . $row4['campus'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['building'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['floor'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['room'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['description'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['req_by'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['return_reason'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['outsource_info'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['first_assignee'] . '</td>';
                                        echo '<td style="display:none;">' . $row4['admins_remark'] . '</td>';

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

                        <!--MODAL FOR NEW REQUEST-->
                        <div class="modal-parent">
                            <div class="modal modal-xl fade" id="addRequest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>Add New Request:</h5>
                                            <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="addrequestForm" method="post" class="row g-3">
                                                <div class="col-4" style="display:none;">
                                                    <label for="new_request_id" class="form-label">Request ID:</label>
                                                    <input type="text" class="form-control" id="new_request_id" name="new_request_id" readonly />
                                                </div>
                                                <div class="col-4">
                                                    <label for="new_building" class="form-label">Building:</label>
                                                    <select class="form-control" id="new_building" name="new_building">
                                                        <option value="" selected="selected">Select Building</option>
                                                    </select>
                                                </div>

                                                <div class="col-4">
                                                    <label for="new_floor" class="form-label">Floor:</label>
                                                    <select class="form-control" id="new_floor" name="new_floor">
                                                        <option value="" selected="selected">Select Floor</option>
                                                    </select>
                                                </div>

                                                <div class="col-4">
                                                    <label for="new_room" class="form-label">Room: </label>
                                                    <select class="form-control" id="new_room" name="new_room">
                                                        <option value="" selected="selected">Select Room</option>
                                                    </select>
                                                </div>
                                                <div class="col-4" style="display:none;">
                                                    <label for="new_campus" class="form-label">Campus:</label>
                                                    <input type="text" class="form-control" id="new_campus" name="new_campus" value="San Bartolome" />
                                                </div>


                                                <div class="col-4">
                                                    <label for="new_equipment" class="form-label">Equipment :</label>
                                                    <select class="form-select" id="new_equipment" name="new_equipment">
                                                        <option value="Bed">Bed</option>
                                                        <option value="Bulb">Bulb</option>
                                                        <option value="LED Light">LED Light</option>
                                                        <option value="Chair">Chair</option>
                                                        <option value="Desk">Desk</option>
                                                        <option value="Sofa">Sofa</option>
                                                        <option value="Table">Table</option>
                                                        <option value="Toilet Seat">Toilet Seat</option>
                                                        <option value="Conference Table">Conference Table</option>
                                                        <option value="Ceiling Fan">Ceiling Fan</option>
                                                        <option value="Aircon">Aircon</option>
                                                        <option value="Cassette Aircon">Cassette Aircon</option>
                                                        <option value="Door">Door</option>
                                                        <option value="Swing Door">Swing Door</option>
                                                    </select>
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="new_req_by" class="form-label">Requested By: </label>
                                                    <input type="text" class="form-control" id="new_req_by" name="new_req_by" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="new_category" class="form-label">Category:</label>
                                                    <select class="form-select" id="new_category" name="new_category" onchange="fetchRandomAssignee1()">
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
                                                            success: function(response) {
                                                                $('#new_assignee').val(response);
                                                                $('#new_first_assignee').val(response); // Set first_assignee to the same value
                                                            },
                                                            error: function(xhr, status, error) {
                                                                alert('Error: ' + error);
                                                            }
                                                        });
                                                    }
                                                </script>

                                                <div class="col-4">
                                                    <label for="new_assignee" class="form-label">Assignee:</label>
                                                    <input type="text" class="form-control" id="new_assignee" name="new_assignee" />
                                                </div>

                                                <div class="col-4" style="display: none;">
                                                    <label for="new_status" class="form-label">Status:</label>
                                                    <input type="text" class="form-control" value="Pending" id="new_status" name="new_status" />
                                                </div>

                                                <div class="col-4" style="display: none;">
                                                    <label for="new_status" class="form-label">Status:</label>
                                                    <input type="text" class="form-control" value="Pending" id="new_status" name="new_status" />
                                                </div>
                                                <div class="col-4" style="display:none;" id="outsourceInfoField">
                                                    <label for="new_outsource_info" class="form-label">Outsource
                                                        Info:</label>
                                                    <input type="text" class="form-control" id="new_outsource_info" name="new_outsource_info" />
                                                </div>

                                                <script>
                                                    // Function to show or hide the outsource info field based on the selected category
                                                    function toggleOutsourceInfoField() {
                                                        var category = document.getElementById('new_category').value;
                                                        var outsourceInfoField = document.getElementById('outsourceInfoField');
                                                        if (category === 'Outsource') {
                                                            outsourceInfoField.style.display = 'block';
                                                        } else {
                                                            outsourceInfoField.style.display = 'none';
                                                        }
                                                    }

                                                    toggleOutsourceInfoField();
                                                    document.getElementById('new_category').addEventListener('change', toggleOutsourceInfoField);
                                                </script>

                                                <div class="col-md-4 offset-md-4">
                                                    <!-- Deadline textbox on the right -->
                                                    <label for="new_deadline" class="form-label text-end">Deadline:</label>
                                                    <input type="date" class="form-control" id="new_deadline" name="new_deadline" />
                                                </div>

                                                <div class="col-12">
                                                    <label for="new_description" class="form-label">Description:</label>
                                                    <input type="text" class="form-control" id="new_description" name="new_description" />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="new_first_assignee" class="form-label">First
                                                        Assignee:</label>
                                                    <input type="text" class="form-control" id="new_first_assignee" name="new_first_assignee" />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="new_mp_remark" class="form-label">MP Remark:</label>
                                                    <input type="text" class="form-control" id="new_mp_remark" name="new_mp_remark" />
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#ForAdd" onclick="showAddConfirmation()">
                                                        Save
                                                    </button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--add for new request-->
                        <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
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
                            <div class="modal modal-xl fade" id="ForApproval" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>For Approval:</h5>

                                            <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="approvalForm" method="post" class="row g-3">
                                                <div class="col-4" style="display:none;">
                                                    <label for="request_id" class="form-label">Request ID:</label>
                                                    <input type="text" class="form-control" id="request_id" name="request_id" readonly />
                                                </div>
                                                <div class="col-4" style="display:none;">
                                                    <label for="date" class="form-label">Date & Time:</label>
                                                    <input type="text" class="form-control" id="date" name="date" />
                                                </div>
                                                <div class="col-4" style="display:none;">
                                                    <label for="campus" class="form-label">Campus:</label>
                                                    <input type="text" class="form-control" id="campus" name="campus" value="San Bartolome" />
                                                </div>
                                                <div class="col-4">
                                                    <label for="building" class="form-label">Building:</label>
                                                    <input type="text" class="form-control" id="building" name="building" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="floor" class="form-label">Floor:</label>
                                                    <input type="text" class="form-control" id="floor" name="floor" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="room" class="form-label">Room: </label>
                                                    <input type="text" class="form-control" id="room" name="room" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="equipment" class="form-label">Equipment :</label>
                                                    <input type="text" class="form-control" id="equipment" name="equipment" readonly />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="req_by" class="form-label">Requested By: </label>
                                                    <input type="text" class="form-control" id="req_by" name="req_by" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="category" class="form-label">Category:</label>
                                                    <select class="form-select" id="category" name="category" onchange="fetchRandomAssignee()">

                                                        <option value="Carpentry">Carpentry</option>
                                                        <option value="Electrical">Electrical</option>
                                                        <option value="Plumbing">Plumbing</option>
                                                        <option value="Outsource">Outsource</option>
                                                    </select>
                                                </div>

                                                <!-- Add an empty assignee select element -->
                                                <div class="col-4">
                                                    <label id="assignee-label" for="assignee" class="form-label">Assignee:</label>
                                                    <select class="form-select" id="assignee" name="assignee"></select>

                                                    <input type="text" class="form-control" id="assigneeInput" name="assignee" style="display: none;">

                                                    <input type="text" class="form-control" id="assigneeInputreal" name="assigneereal" style="display:none;">
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="status" class="form-label">Status:</label>
                                                    <input type="text" class="form-control" value="Pending" id="status_modal" name="status" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="deadline" class="form-label">Deadline:</label>
                                                    <input type="date" class="form-control" id="deadline" name="deadline" />
                                                </div>
                                                <div class="col-4">
                                                    <label for="first_assignee" class="form-label">First
                                                        Assignee:</label>
                                                    <input type="text" class="form-control" id="first_assignee" name="first_assignee" readonly />
                                                </div>

                                                <!-- Add outsource_info field -->
                                                <div class="col-4" id="outsourceInfoFieldapprove" style="display: none;">
                                                    <label for="outsource_info" class="form-label">Outsource Info:</label>
                                                    <input type="text" class="form-control" id="outsource_info" name="outsource_info" />
                                                </div>


                                                <div class="col-12">
                                                    <label for="description" class="form-label">Description:</label>
                                                    <input type="text" class="form-control" id="description" name="description" />
                                                </div>

                                                <div class="col-12">
                                                    <label for="return_reason" class="form-label">Transfer
                                                        Reason:</label>
                                                    <input type="text" class="form-control" id="return_reason" name="return_reason" readonly />
                                                </div>






                                                <!-- JavaScript to toggle visibility based on category selection -->
                                                <script>
                                                    // Function to show or hide the outsource_info field based on the selected category
                                                    function toggleOutsourceInfoFieldapprove() {
                                                        var category = document.getElementById('category').value;
                                                        var outsourceInfoFieldapprove = document.getElementById('outsourceInfoFieldapprove');
                                                        if (category === 'Outsource') {
                                                            outsourceInfoFieldapprove.style.display = 'block';
                                                        } else {
                                                            outsourceInfoFieldapprove.style.display = 'none';
                                                        }
                                                    }

                                                    // Call the function initially and add an event listener to the category select element
                                                    toggleOutsourceInfoFieldapprove();
                                                    document.getElementById('category').addEventListener('change', toggleOutsourceInfoFieldapprove);
                                                </script>




                                                <div class="col-12">
                                                    <label for="admins_remark" class="form-label">Remarks</label>
                                                    <input type="text" class="form-control" id="admins_remark" name="admins_remark" />
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#ForApprovals" onclick="showApprovalConfirmation()">
                                                        Save
                                                    </button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Edit for approval-->
                        <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn" name="approval" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <!--MODAL FOR OUTSOURCE-->
                        <div class="modal-parent">
                            <div class="modal modal-xl fade" id="ForOutsource" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>For Outsource:</h5>
                                            <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="outsourcesForm" method="post" class="row g-3">
                                                <div class="col-4" style="display:none;">
                                                    <label for="new_request_id" class="form-label">Request ID:</label>
                                                    <input type="text" class="form-control" id="new2_request_id" name="new2_request_id" readonly />
                                                </div>
                                                <div class="col-4">
                                                    <label for="new2_building" class="form-label">Building:</label>
                                                    <input type="text" class="form-control" id="new2_building" name="new2_building" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="new2_floor" class="form-label">Floor:</label>
                                                    <input type="text" class="form-control" id="new2_floor" name="new2_floor" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="new2_room" class="form-label">Room: </label>
                                                    <input type="text" class="form-control" id="new2_room" name="new2_room" readonly />
                                                </div>
                                                <div class="col-4" style="display:none;">
                                                    <label for="new2_campus" class="form-label">Campus:</label>
                                                    <input type="text" class="form-control" id="new2_campus" name="new2_campus" value="San Bartolome" />
                                                </div>


                                                <div class="col-4">
                                                    <label for="new2_equipment" class="form-label">Equipment :</label>
                                                    <input type="text" class="form-control" id="new2_equipment" name="new2_equipment" readonly />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="new2_req_by" class="form-label">Requested By: </label>
                                                    <input type="text" class="form-control" id="new2_req_by" name="new2_req_by" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="new2_category" class="form-label">Category:</label>
                                                    <select class="form-select" id="new2_category" name="new2_category" onchange="fetchRandomAssignee1()" readonly style="background-color: #d6e4f0 !important;">
                                                        <option value="Outsource">Outsource</option>
                                                        <option value="Carpentry">Carpentry</option>
                                                        <option value="Electrical">Electrical</option>
                                                        <option value="Plumbing">Plumbing</option>
                                                    </select>
                                                </div>

                                                <script>
                                                    // Disable all options and prevent the default behavior of the select element
                                                    document.getElementById("new2_category").addEventListener("mousedown", function(event) {
                                                        event.preventDefault();
                                                    });
                                                </script>


                                                <!-- Add an empty assignee select element -->
                                                <div class="col-4">
                                                    <label id="assignee-label" for="new2_assignee" class="form-label">Assignee:</label>
                                                    <input type="text" class="form-control" id="new2_assignee" name="new2_assignee" readonly />
                                                    <!-- <select class="form-select" id="assignee" name="assignee"></select> -->

                                                </div>

                                                <div class="col-4" style="display: none;">
                                                    <label for="new2_status" class="form-label">Status:</label>
                                                    <input type="text" class="form-control" value="Pending" id="new2_status" name="new2_status" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="new2_deadline" class="form-label">Deadline:</label>
                                                    <input type="date" class="form-control" id="new2_deadline" name="new2_deadline" readonly />
                                                </div>

                                                <div class="col-12">
                                                    <label for="new2_description" class="form-label">Description:</label>
                                                    <input type="text" class="form-control" id="new2_description" name="new2_description" readonly />
                                                </div>


                                                <div class="col-12">
                                                    <label for="return_reason" class="form-label">Transfer
                                                        Reason:</label>
                                                    <input type="text" class="form-control" id="new2_return_reason" name="new2_return_reason" readonly />
                                                </div>

                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#ForOutsourcess" onclick="showOutsourcesConfirmation()">
                                                        Mark As Done
                                                    </button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--edit for outsource-->
                        <div class="modal fade" id="addoutsource" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <!-- <button class="btn add-modal-btn" name="outsource"
                                            data-bs-dismiss="modal">Yes</button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>


                        <!--MODAL FOR THE DONE VIEW-->
                        <div class="modal-parent">
                            <div class="modal modal-xl fade" id="ForViewDone" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>View Done Request:</h5>

                                            <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" class="row g-3">
                                                <div class="col-4" style="display:none;">
                                                    <label for="request_id_done" class="form-label">Request ID:</label>
                                                    <input type="text" class="form-control" id="request_id_done" name="request_id_done" readonly />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="date_done" class="form-label">Date & Time:</label>
                                                    <input type="text" class="form-control" id="date_done" name="date_done" />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="campus_done" class="form-label">Campus:</label>
                                                    <input type="text" class="form-control" id="campus_done" name="campus_done" value="San Bartolome" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="building_done" class="form-label">Building:</label>
                                                    <input type="text" class="form-control" id="building_done" name="building_done" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="floor_done" class="form-label">Floor:</label>
                                                    <input type="text" class="form-control" id="floor_done" name="floor_done" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="room_done" class="form-label">Room:</label>
                                                    <input type="text" class="form-control" id="room_done" name="room_done" readonly />
                                                </div>

                                                <div class="col-4">
                                                    <label for="equipment_done" class="form-label">Equipment :</label>
                                                    <input type="text" class="form-control" id="equipment_done" name="equipment_done" readonly />
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="req_by_done" class="form-label">Requested By:</label>
                                                    <input type="text" class="form-control" id="req_by_done" name="req_by_done" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="category_done" class="form-label">Category:</label>
                                                    <input type="text" class="form-control" id="category_done" name="category_done" readonly />

                                                </div>


                                                <!-- Add an empty assignee select element -->
                                                <div class="col-4">
                                                    <label id="assignee-label" for="assignee_done" class="form-label">Assignee:</label>
                                                    <input type="text" class="form-control" id="assignee_done" name="assignee_done" readonly />


                                                    <input type="text" class="form-control" id="assigneeInput_done" name="assignee_done" style="display: none;">

                                                    <input type="text" class="form-control" id="assigneeInputreal_done" name="assigneereal_done" style="display:none;">
                                                </div>

                                                <div class="col-4" style="display:none;">
                                                    <label for="status_done" class="form-label">Status:</label>
                                                    <input type="text" class="form-control" value="Pending" id="status_modal_done" name="status_done" />
                                                </div>

                                                <div class="col-4">
                                                    <label for="deadline_done" class="form-label">Deadline:</label>
                                                    <input type="text" class="form-control" id="deadline_done" name="deadline_done" readonly />
                                                </div>
                                                <div class="col-12">
                                                    <label for="description_done" class="form-label">Description:</label>
                                                    <input type="text" class="form-control" id="description_done" name="description_done" readonly />
                                                </div>

                                                <div class="col-12">
                                                    <label for="return_reason_done" class="form-label">Transfer
                                                        Reason:</label>
                                                    <input type="text" class="form-control" id="return_reason_done" name="return_reason_done" readonly />
                                                </div>

                                                <div class="col-4" id="outsourceInfoFieldapprove_done" style="display: none;">
                                                    <label for="outsource_info_done" class="form-label">Outsource
                                                        Info:</label>
                                                    <input type="text" class="form-control" id="outsource_info_done" name="outsource_info_done" />
                                                </div>

                                                <!-- JavaScript to toggle visibility based on category selection -->
                                                <script>
                                                    // Function to show or hide the outsource_info field based on the selected category
                                                    function toggleOutsourceInfoFieldapprove() {
                                                        var category = document.getElementById('category').value;
                                                        var outsourceInfoFieldapprove = document.getElementById('outsourceInfoFieldapprove');
                                                        if (category === 'Outsource') {
                                                            outsourceInfoFieldapprove.style.display = 'block';
                                                        } else {
                                                            outsourceInfoFieldapprove.style.display = 'none';
                                                        }
                                                    }

                                                    // Call the function initially and add an event listener to the category select element
                                                    toggleOutsourceInfoFieldapprove();
                                                    document.getElementById('category').addEventListener('change', toggleOutsourceInfoFieldapprove);
                                                </script>



                                                <div class="col-12" style="display:none;">
                                                    <label for="first_assignee_done" class="form-label">First
                                                        Assignee:</label>
                                                    <input type="text" class="form-control" id="first_assignee_done" name="first_assignee_done" readonly />
                                                </div>

                                                <div class="col-12">
                                                    <label for="admins_remark_done" class="form-label">Your
                                                        Remarks:</label>
                                                    <input type="text" class="form-control" id="admins_remark_done" name="admins_remark_done" readonly />
                                                </div>



                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal">
                                                        Close
                                                    </button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
            </main>
        </section>

        <!-- PROFILE MODALS -->
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
        <script src="../../src/js/archive.js"></script>
        <script src="../../src/js/profileModalController.js"></script>
        <script src="../../src/js/logout.js"></script>
        <script src="../../src/js/sanbartolomemanager.js"></script>


        <!-- Cascading Script -->
        <script>
            var subjectObject = {
                "New Academic": {
                    "1F": ["Pantry", "Recovery Room", "Dental Clinic", "Guidance Office", "Faculty Lounge", "CR", "Lobby", "Landing", "Storage", "Counseling Room", "Medical and Dental Clinic", "Nurse Room", "Dental Room", "Generator Room", "EE Room", "Server Room", "Medical Consultation"],
                    "2F": ["Librarians Office", "Digital Library", "University Library", "Baggage Counter", "Meeting Room 1", "Meeting Room 2", "Meeting Room 3", "Meeting Room 4", "Meeting Room 5", "Meeting Room 6", "Emergency Exit", "Server Room", "EE RM", "Coffee Shop", "CR"],
                    "3F": ["Lobby", "Lec Rm 301", "Lec Rm 302", "Lec Rm 303", "Lec Rm 304", "Lec Rm 305", "Lec Rm 306", "AV Rm 307", "CR", "Storage"],
                    "4F": ["Lec Rm 401", "Lec Rm 402", "Lec Rm 403", "Lec Rm 404", "Lec Rm 405", "Lec Rm 406", "Rm 407", "Landing", "EE RM"],
                    "5F": ["Lec Rm 501", "Lec Rm 502", "Lec Rm 503", "Lec Rm 504", "Lec Rm 505", "Lec Rm 506", "Rm 507", "Landing", "EE RM"],
                    "6F": ["Lec Rm 601", "Lec Rm 602", "Lec Rm 603", "Lec Rm 604", "Lec Rm 605", "Lec Rm 606", "Rm 607", "Landing", "EE RM"],
                    "7F": ["Lec Rm 701", "Lec Rm 702", "Lec Rm 703", "Lec Rm 704", "Lec Rm 705", "Lec Rm 706", "Rm 707", "Landing", "EE RM", "Rain Water Tank", "Storage Rm"]
                },
                "Yellow": {
                    "1F": ["IB101A", "IB102A", "IB103A", "IB104A", "IB105A", "IB106A", "IB107A", "IB108A", "IB109A", "IB110A", "CR FEMALE", "CR MALE", "HALLWAY"],
                    "2F": ["IB201F", "IB202C", "IB203B", "IB204B", "IB205B", "IB206B", "IB207B", "IB208B", "IB209C", "IB210D", "CR FEMALE", "CR MALE", "HALLWAY"]
                },
                "Techvoc": {
                    "1F": ["Dress Making Lab", "PF-BAGM Department", "OSAS", "Auto Mechanic Lab", "Carpentry", "Conference Room", "BDC Office", "Power RM", "Cuisine Art & Banquet Service", "PF-Stock RM", "Electrical Installation and Maintenance Lab", "Refrigeration and Aircon Lab", "Techvoc Gym", "CR"],
                    "2F": ["IA205", "IA206e", "IA207e", "IA208e", "IA209e", "IA210", "IA211a", "IA212a", "IA213a", "IA214a", "IA215a", "IA216", "Scholarship Office", "Management Information System Office", "Auto Mechanic Lec.", "Consumer Electronic Lab.", "CR"]
                },
                "Korphil": {
                    "1F": ["Room A", "Room B", "Directors's RM", "Lobby", "Room C", "Room D", "Room E", "Medical Staff", "Waiting Area", "Electric RM", "Generator RM", "Student Affairs Office", "Proposed Cafeteria"],
                    "2F": ["Utility RM", "Course Coord. RM", "E-Learning RM", "Server RM", "Lecture RM", "Utility RM", "Com Lab", "Temporary Lab"],
                    "3F": ["Storage", "Seminar RM", "Com Lab", "Storage", "Multi Purpose RM"]
                },
                "Admin": {
                    "Ground Floor": ["Lobby"]
                },
                "Belmonte": {
                    "1F": ["IC101a", "IC102a", "IC103a", "IC104a", "PE Faculty Room", "IC105a", "IC106a", "CR"],
                    "2F": ["IC201a", "IC202a", "IC203a", "IC204a", "Guidance Office", "IC205a", "IC206a", "IC207a", "CR"],
                    "3F": ["IC301a", "IC302a", "IC303a", "IC304a", "Stock Room", "IC305a", "IC306a", "IC307a", "CR"],
                    "4F": ["IC401a", "IC402a", "IC403a", "IC404a", "Research & Extension Office", "IC405a", "IC406a", "IC407a"]
                },
                "Bautista": {
                    "Basement": ["Canteen", "Entrance", "CR", "Storage", "Fire Exit", "Kitchen", "HE Room", "Food Stall", "Security Room", "RM 106", "Main Stairs", "PWD CR", "EE Room"],
                    "Ground Floor": ["Pump Room", "Receiving Area", "Lobby", "Room 1", "Storage Room", "Control Room", "Room 2", "Fire Exit", "AUX Exit", "Room 3", "Room 4", "Elevator Lobby", "Elevator 1", "Elevator 2", "Main Stair", "Toilet", "Janitor Room", "Corridor"],
                    "2F": ["Faculty Office", "Humanities Faculty Office", "Storage Room", "Control Room", "IK201", "Fire Exit", "IK202", "Storage Room", "Control Room", "EE Room", "AUX Room", "IK203", "Elevator Lobby", "Elevator 1", "Elevator 2", "Main Stair", "Janitor Room", "PWD", "Toilet", "Corridor"],
                    "3F": ["Storage Room", "Control Room", "Fire Exit", "Toilet", "Corridor", "Dry Pantry", "Archive", "Faculty", "IK301", "IK302", "Elevator Lobby", "Elevator 1", "Elevator 2", "Main Stair", "Janitor Room", "PWD"],
                    "4F": ["Storage Room", "Faculty", "Control Room", "IK401", "Fire Exit", "IK402", "IK403", "Elevator Lobby", "Elevator 1", "Elevator 2", "Main Stair", "Toilet", "Janitor Room", "Corridor", "PWD"],
                    "5F": ["Storage", "Archive", "Dry Pantry", "Faculty", "Control Room", "IK501", "Fire Exit", "IK502", "IK503", "AUX Room", "EE Room", "Elevator Lobby", "Elevator 1", "Elevator 2", "Main Stair", "Toilet", "Janitor Room", "PWD", "Corridor"],
                    "6F": ["Storage", "Faculty", "Control Room", "IK601", "Fire Exit", "IK602", "AUX Room", "EE Room", "IK603", "Elevator Lobby", "Elevator 1", "Elevator 2", "Main Stair", "Toilet", "Janitor Room", "PWD", "Corridor"]
                },
                "Multipurpose": {
                    "1F": ["Lobby"]
                },
                "Chinese B": {
                    "1F": ["Lobby"]
                }
            }
            window.onload = function() {
                var subjectSel = document.getElementById("new_building");
                var topicSel = document.getElementById("new_floor");
                var chapterSel = document.getElementById("new_room");

                for (var x in subjectObject) {
                    subjectSel.options[subjectSel.options.length] = new Option(x, x);
                }

                subjectSel.onchange = function() {
                    // Empty Floors- and Rooms-dropdowns
                    chapterSel.length = 1;
                    topicSel.length = 1;

                    // Display correct values for Floors
                    for (var y in subjectObject[this.value]) {
                        topicSel.options[topicSel.options.length] = new Option(y, y);
                    }
                }

                topicSel.onchange = function() {
                    // Empty Rooms dropdown
                    chapterSel.length = 1;

                    // Display correct values for Rooms
                    var rooms = subjectObject[subjectSel.value][this.value];
                    for (var i = 0; i < rooms.length; i++) {
                        chapterSel.options[chapterSel.options.length] = new Option(rooms[i], rooms[i]);
                    }
                }
            }
        </script>



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
                assigneeInput.addEventListener('input', function() {
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
                    xhr.onload = function() {
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
                            assigneeSelect.addEventListener('change', function() {
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
            $(document).ready(function() {
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

                    // Additional fields
                    $("#outsource_info").val(row.find("td:eq(16)").text());
                    $("#first_assignee").val(row.find("td:eq(17)").text());
                    $("#admins_remark").val(row.find("td:eq(18)").text());
                    $("#mp_remark").val(row.find("td:eq(19)").text());
                }

                // Click event for the "Approve" button
                $("button[data-bs-target='#ForApproval']").click(function() {
                    var row = $(this).closest("tr"); // Get the closest row to the clicked button
                    populateModal(row); // Populate modal fields with data from the row
                    $("#ForApproval").modal("show"); // Show the modal
                });
            });
        </script>


        <!--2 PANTAWAG SA MODAL TO DISPLAY SA INPUT BOXES-->
        <script>
            $(document).ready(function() {
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
                    // Additional fields
                    $("#new_outsource_info").val(row.find("td:eq(16)").text());
                    $("#new_first_assignee").val(row.find("td:eq(17)").text());
                    $("#new_admins_remark").val(row.find("td:eq(18)").text());
                    $("#new_mp_remark").val(row.find("td:eq(19)").text());
                }

                // Click event for the "Done" button for modal "ForOutsource" based on row 2
                $("button[data-bs-target='#ForOutsource']").click(function() {
                    var row = $(this).closest("tr"); // Get the closest row to the clicked button
                    populateModalForOutsource(row); // Populate modal fields with data from the row
                    $("#ForOutsource").modal("show"); // Show the modal
                });
            });
        </script>


        <!--2 PANTAWAG SA MODAL TO DISPLAY SA INPUT BOXES-->
        <script>
            $(document).ready(function() {
                // Function to populate modal fields for modal "ForViewDone" with data from row 2
                function populateModalForViewDone(row) {
                    // Populate modal fields with data from the row
                    $("#request_id_done").val(row.find("td:eq(0)").text());
                    $("#building_done").val(row.find("td:eq(3)").text().split(', ')[0]);
                    $("#floor_done").val(row.find("td:eq(3)").text().split(', ')[1]);
                    $("#room_done").val(row.find("td:eq(3)").text().split(', ')[2]);
                    $("#equipment_done").val(row.find("td:eq(4)").text());
                    $("#assignee_done").val(row.find("td:eq(5)").text());
                    $("#category_done").val(row.find("td:eq(2)").text());
                    $("#status_done").val(row.find("td:eq(6)").text());
                    $("#deadline_done").val(row.find("td:eq(6)").text());
                    $("#description_done").val(row.find("td:eq(13)").text());
                    $("#return_reason_done").val(row.find("td:eq(15)").text());

                    // Check if return_reason has a value
                    if (row.find("td:eq(15)").text().trim() !== '') {
                        $("#return_reason_done").closest('.col-12').show(); // Show the div if there's a value
                    } else {
                        $("#return_reason_done").closest('.col-12').hide(); // Hide the div if there's no value
                    }

                    // Additional fields
                    $("#outsource_info_done").val(row.find("td:eq(16)").text());
                    $("#first_assignee_done").val(row.find("td:eq(17)").text());
                    $("#admins_remark_done").val(row.find("td:eq(18)").text());
                    $("#mp_remark_done").val(row.find("td:eq(19)").text());
                }

                // Click event for the "Done" button for modal "ForViewDone" based on row 2
                $("button[data-bs-target='#ForViewDone']").click(function() {
                    var row = $(this).closest("tr"); // Get the closest row to the clicked button
                    populateModalForViewDone(row); // Populate modal fields with data from the row
                    $("#ForViewDone").modal("show"); // Show the modal
                });
            });
        </script>









        <script>
            $(document).ready(function() {
                $('.notification-item').on('click', function(e) {
                    e.preventDefault();
                    var activityId = $(this).data('activity-id');
                    var notificationItem = $(this); // Store the clicked element

                    $.ajax({
                        type: "POST",
                        url: "single_notification.php", // The URL to the PHP file
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





        <!-- Add this script after your existing scripts -->
        <!-- Add this script after your existing scripts -->
        <script>
            // Add a click event listener to the logout link
            document.getElementById('logoutBtn').addEventListener('click', function() {
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
            // JavaScript to convert input fields to textareas
            document.addEventListener("DOMContentLoaded", function() {
                var descriptionInput = document.getElementById("description");
                var returnReasonInput = document.getElementById("return_reason");

                // Convert input fields to textareas
                var descriptionTextarea = document.createElement("textarea");
                descriptionTextarea.className = "form-control";
                descriptionTextarea.name = "description";
                descriptionTextarea.id = "description";
                descriptionInput.parentNode.replaceChild(descriptionTextarea, descriptionInput);

                var returnReasonTextarea = document.createElement("textarea");
                returnReasonTextarea.className = "form-control";
                returnReasonTextarea.name = "return_reason";
                returnReasonTextarea.id = "return_reason";
                returnReasonTextarea.readOnly = true;
                returnReasonInput.parentNode.replaceChild(returnReasonTextarea, returnReasonInput);
            });
        </script>
        <script>
            // JavaScript to convert input fields to textareas
            document.addEventListener("DOMContentLoaded", function() {
                var descriptionInput = document.getElementById("new2_description");
                var returnReasonInput = document.getElementById("new2_return_reason");

                // Convert input fields to textareas
                var descriptionTextarea = document.createElement("textarea");
                descriptionTextarea.className = "form-control";
                descriptionTextarea.name = "new2_description";
                descriptionTextarea.id = "new2_description";
                descriptionInput.parentNode.replaceChild(descriptionTextarea, descriptionInput);

                var returnReasonTextarea = document.createElement("textarea");
                returnReasonTextarea.className = "form-control";
                returnReasonTextarea.name = "new2_return_reason";
                returnReasonTextarea.id = "new2_return_reason";
                returnReasonTextarea.readOnly = true;
                returnReasonInput.parentNode.replaceChild(returnReasonTextarea, returnReasonInput);
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script>
            // JavaScript to convert input fields to textareas
            document.addEventListener("DOMContentLoaded", function() {
                var descriptionInput = document.getElementById("new2_description");

                // Convert input field to textarea
                var descriptionTextarea = document.createElement("textarea");
                descriptionTextarea.className = "form-control";
                descriptionTextarea.name = "new2_description";
                descriptionTextarea.id = "new2_description";
                descriptionTextarea.readOnly = true; // Set readonly to true for description textarea
                descriptionTextarea.style.backgroundColor = "lightblue"; // Change background color
                descriptionInput.parentNode.replaceChild(descriptionTextarea, descriptionInput);
            });
        </script>

        <script>
            // Get the input element
            var inputElement = document.getElementById('new_description');

            // Create a new textarea element
            var textareaElement = document.createElement('textarea');

            // Copy attributes from input to textarea
            textareaElement.className = inputElement.className;
            textareaElement.id = inputElement.id;
            textareaElement.name = inputElement.name;

            // Replace input with textarea
            inputElement.parentNode.replaceChild(textareaElement, inputElement);
        </script>

        <script>
            // Select all <td> elements with the class "red", "blue", or "green"
            var tdElements = document.querySelectorAll("td.red, td.blue, td.green, td.orange");

            // Loop through each selected <td> element
            tdElements.forEach(function(tdElement) {
                // Get the text content of the <td> element
                var textContent = tdElement.textContent;

                // Create a new <span> element
                var spanElement = document.createElement("span");

                // Set the text content of the <span> element to the text content of the <td> element
                spanElement.textContent = textContent;

                // Add a class name based on the color of the <td> element
                if (tdElement.classList.contains("red")) {
                    spanElement.classList.add("red-value");
                } else if (tdElement.classList.contains("blue")) {
                    spanElement.classList.add("blue-value");
                } else if (tdElement.classList.contains("green")) {
                    spanElement.classList.add("green-value");
                } else if (tdElement.classList.contains("orange")) {
                    spanElement.classList.add("orange-value");
                }

                // Replace the text content of the <td> element with the <span> element
                tdElement.textContent = "";
                tdElement.appendChild(spanElement);
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusFilter = document.getElementById('status-filter');

                statusFilter.addEventListener('change', function() {
                    const selectedStatus = statusFilter.value;
                    const rows = document.querySelectorAll('.table-container table tr');

                    rows.forEach(row => {
                        const statusCell = row.querySelector('.status-cell');
                        if (selectedStatus === 'all' || statusCell.textContent === selectedStatus) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                let lastPillSelected = sessionStorage.getItem('lastPillArchive');

                if (!lastPillSelected) {
                    $("#pills-manager").addClass("show active");
                    $("#pills-profile").removeClass("show active");
                    $("#pills-done").removeClass("show active");
                    $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                    $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                    $(".nav-link[data-bs-target='pills-done']").removeClass("active");
                } else {
                    switch (lastPillSelected) {
                        case 'pills-manager':
                            $("#pills-manager").addClass("show active");
                            $("#pills-profile").removeClass("show active");
                            $("#pills-done").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                            $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                            $(".nav-link[data-bs-target='pills-done']").removeClass("active");
                            break;
                        case 'pills-profile':
                            $("#pills-profile").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $("#pills-done").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-profile']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            $(".nav-link[data-bs-target='pills-done']").removeClass("active");
                            break;
                        case 'pills-done':
                            $("#pills-done").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $("#pills-profile").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-done']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                            break;
                    }
                }

                // Check the active tab on page load
                let activeTab = $('.nav-link.active').data('bs-target');

                // If the active tab is "Outsource", remove the "For Approval" option from the dropdown
                if (activeTab === 'pills-profile') {
                    $("#status-filter option[value='For Approval']").remove();
                    $("#status-filter").prop('disabled', false);
                } else if (activeTab === 'pills-done') {
                    // Disable the dropdown when the "Done" tab is active
                    $("#status-filter").prop('disabled', true);
                } else {
                    // If the active tab is neither "Outsource" nor "Done", enable the dropdown and ensure that "For Approval" option exists
                    $("#status-filter").prop('disabled', false);
                    if ($("#status-filter option[value='For Approval']").length === 0) {
                        // Add "For Approval" option back if it doesn't exist
                        $("#status-filter").append('<option value="For Approval">For Approval</option>');
                    }
                }

                $(".nav-link").click(function() {
                    const targetId = $(this).data("bs-target");

                    sessionStorage.setItem('lastPillArchive', targetId);

                    $(".tab-pane").removeClass("show active");
                    $(`#${targetId}`).addClass("show active");
                    $(".nav-link").removeClass("active");
                    $(this).addClass("active");

                    // Check if the last tab is "Outsource"
                    if (targetId === 'pills-profile') {
                        // Remove the "For Approval" option from the dropdown
                        $("#status-filter option[value='For Approval']").remove();
                        // Enable the dropdown
                        $("#status-filter").prop('disabled', false);
                    } else if (targetId === 'pills-done') {
                        // Disable the dropdown when the "Done" tab is active
                        $("#status-filter").prop('disabled', true);
                    } else {
                        // If the last tab is neither "Outsource" nor "Done", enable the dropdown and ensure that "For Approval" option exists
                        $("#status-filter").prop('disabled', false);
                        if ($("#status-filter option[value='For Approval']").length === 0) {
                            // Add "For Approval" option back if it doesn't exist
                            $("#status-filter").append('<option value="For Approval">For Approval</option>');
                        }
                    }
                });
            });
        </script>

    </body>

    </html>