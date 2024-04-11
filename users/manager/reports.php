<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

session_start();
include_once("../../config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

function logActivity($conn, $accountId, $actionDescription, $tabValue)
{
    $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param("iss", $accountId, $actionDescription, $tabValue);
    if (!$stmt->execute()) {
        echo "Error logging activity: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 2) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }

    $sql = "SELECT * FROM asset WHERE status = 'Working'";
    $result = $conn->query($sql) or die($conn->error);

    $sql2 = "SELECT * FROM asset WHERE status = 'Under Maintenance'";
    $result2 = $conn->query($sql2) or die($conn->error);

    $sql3 = "SELECT * FROM asset WHERE status = 'For Replacement'";
    $result3 = $conn->query($sql3) or die($conn->error);

    $sql4 = "SELECT * FROM asset WHERE status = 'Need Repair'";
    $result4 = $conn->query($sql4) or die($conn->error);


    //Edit
    if (isset($_POST['edit'])) {
        $assetId = $_POST['assetId'];
        $category = $_POST['category'];
        $building = $_POST['building'];
        $floor = $_POST['floor'];
        $room = $_POST['room'];

        $status = $_POST['status'];
        $assignedName = $_POST['assignedName'];
        $assignedBy = $_POST['assignedBy'];
        $date = $_POST['date'];




        $updateSql = "UPDATE `asset` SET `category`='$category', `building`='$building', `floor`='$floor', `room`='$room', `status`='$status', `assignedName`='$assignedName', `assignedBy`='$assignedBy', `date`='$date' WHERE `assetId`='$assetId'";
        if ($conn->query($updateSql) === TRUE) {
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId to $status.", 'Report');
        } else {
            echo "Error updating asset: " . $conn->error;
        }
        header("Location: reports.php");
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


    if (isset($_POST['assignMaintenance'])) {
        $assetId = $_POST['assetId'];
        $assignedName = $_POST['assignedName'];
        $assignSql = "UPDATE `asset` SET `assignedName`='$assignedName' WHERE `assetId`='$assetId'";

        if (!empty($assignSql) && $conn->query($assignSql) === TRUE) {
            logActivity($conn, $_SESSION['accountId'], "Assigned maintenance personnel $assignedName to asset ID $assetId.", 'General');

            // Fetch the asset details
            $assetDetailsQuery = "SELECT `category`, `building`, `floor`, `room` FROM `asset` WHERE `assetId` = ?";
            $stmt = $conn->prepare($assetDetailsQuery);
            $stmt->bind_param("i", $assetId);
            $stmt->execute();
            $assetDetailsResult = $stmt->get_result();

            if ($assetDetailsResult->num_rows > 0) {
                $assetDetails = $assetDetailsResult->fetch_assoc();

                // Fetch the email of the assigned personnel
                $emailQuery = "SELECT email FROM account WHERE CONCAT(firstName, ' ', lastName) = ?";
                $stmt = $conn->prepare($emailQuery);
                $stmt->bind_param("s", $assignedName);
                $stmt->execute();
                $emailResult = $stmt->get_result();

                if ($emailResult->num_rows > 0) {
                    $row = $emailResult->fetch_assoc();
                    $toEmail = $row['email'];

                    // Set up PHPMailer
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

                    try {
                        //Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'qcu.upkeep@gmail.com';
                        $mail->Password = 'qvpx bbcm bgmy hcvf';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        //Recipients
                        $mail->setFrom('qcu.upkeep@gmail.com', 'iTrak');
                        $mail->addAddress($toEmail);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Task Assignment Notification';
                        $mail->Body = 'Dear ' . $assignedName . ',<br><br>

                        I hope this message finds you well.<br><br>

                        The administrator has assigned you to address the issues with the following details:<br>
                        + Tracking Number: ' . $assetId . '<br>
                        + Category: ' . $assetDetails['category'] . '<br> 
                        + Location: ' . $assetDetails['building'] . ' ' . $assetDetails['floor'] . ' ' . $assetDetails['room'] . '<br><br>

                        Please check the system for further details regarding this assignment.<br><br>

                        Best regards,<br><br>

                        Allyssa Bea Marie Cabal<br>

                        Administrator<br>

                        iTrak';

                        $mail->send();
                    } catch (Exception $e) {
                        // Handle errors with mail sending here
                    }
                }
            }
        } else {
            echo "Error assigning maintenance personnel: " . $conn->error;
        }
        header("Location: reports.php");
    }


?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Reports</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/reports.css" />
        <script src="../../src/js/reports.js"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>



        <!--JS for the fcking tabs-->
        <script>
            $(document).ready(function() {
                // this is for staying at the same pill when reloading.
                let tabLastSelected = sessionStorage.getItem("lastTab");

                if (!tabLastSelected) {
                    // if no last tab was selected, use the pills-manager for default
                    $("#pills-manager").addClass("show active");
                    // $("#pills-profile").removeClass("show active");
                    $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                    // $(".nav-link[data-bs-target='pills-profile']").removeClass("active");

                } else {

                    //* checks the last tab that was selected
                    switch (tabLastSelected) {
                        case 'pills-manager':
                            $("#pills-manager").addClass("show active");
                            $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                            $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                            break;
                        case 'pills-profile':
                            $("#pills-profile").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-profile']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                        case 'pills-replace':
                            $("#pills-replace").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-replace']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                        case 'pills-repair':
                            $("#pills-repair").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-repair']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                    }
                }

                // $("#pills-manager").addClass("show active");
                // $("#pills-profile").removeClass("show active");

                $(".nav-link").click(function() {
                    const targetId = $(this).data("bs-target");

                    sessionStorage.setItem("lastTab", targetId); //* sets the targetId to the sessionStorage lastTab item

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
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><img src="../../src/icons/Profile.svg" alt="" class="profile-icons">Profile</a>
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
                <li>
                    <a href="./map.php">
                        <i class="bi bi-map"></i>
                        <span class="text">Map</span>
                    </a>
                </li>
                <li class="active">
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
                            <!-- <h1 class="tab-name">Reports</h1> -->
                            <div class="tbl-filter">
                                <select id="filter-criteria">
                                    <option value="all">All</option> <!-- Added "All" option -->
                                    <option value="reportId">Tracking ID</option>
                                    <option value="date">Date</option>
                                    <option value="category">Category</option>
                                    <option value="location">Location</option>
                                </select>


                                <select id="rows-display-dropdown" class="form-select dropdown-rows" aria-label="Default select example">
                                    <option value="20" selected>Show 20 rows</option>
                                    <option class="hidden"></option>
                                    <option value="50">Show 50 rows</option>
                                    <option value="100">Show 100 rows</option>
                                    <option value="150">Show 150 rows</option>
                                    <option value="200">Show 200 rows</option>
                                </select>

                                <!-- Search Box -->
                                  <!-- Search Box -->
                                  <form class="d-flex col-sm-5" role="search" id="searchForm">
                                    <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" name="q" />
                                </form>
                            </div>
                        </div>
                    </header>
                    <script>
  // Get elements from the DOM
  const filterCriteria = document.getElementById('filter-criteria');
  const searchBox = document.getElementById('search-box');

  // Event listener for the filter dropdown changes
  filterCriteria.addEventListener('change', function() {
    if (this.value === 'date') {
      // If "Date" is selected, change the search box to a date picker
      searchBox.type = 'date';
      searchBox.placeholder = 'Select a date';
    } else {
      // For all other options, change it back to a regular search box
      searchBox.type = 'search';
      searchBox.placeholder = 'Search';
    }
  });
</script>
<div class="new-nav-container">
                    <!--Content start of tabs-->
                    <div class="new-nav">
                            <ul>
                                <li><a href="#" class="nav-link" data-bs-target="pills-manager">Working</a></li>
                                <li><a href="#" class="nav-link" data-bs-target="pills-profile">Under Maintenance</a></li>
                                <li><a href="#" class="nav-link" data-bs-target="pills-replace">For Replacement</a></li>
                                <li><a href="#" class="nav-link" data-bs-target="pills-repair">Need Repair</a></li>
                                <li></li>
                                <li></li>
                            </ul>
                        </div>

                        <!-- Export button -->
                        <div class="export-mob-hide">
                            <form method="post" id="exportForm">
                                <input type="hidden" name="status" id="statusField" value="For Replacement">
                                <button type="button" id="exportBtn" class="btn btn-outline-danger">Export Data</button>
                            </form>
                        </div>
                    </div>


                    <!--Tab for table 1-->
                    <div class="tab-content pt" id="myTabContent">
                        <div class="tab-pane fade show active" id="pills-manager" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-content" id="exportContentWorking">
                                <div class='table-header'>
                                    <div class='headerskie'>
                                        <span>TRACKING #</span>
                                        <span>DATE & TIME</span>
                                        <span>CATEGORY</span>
                                        <span>LOCATION</span>
                                        <span>STATUS</span>
                                    </div>
                                </div>
                                <?php
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row['assetId'] . '</td>';
                                        echo '<td >' . $row['date'] . '</td>';
                                        echo '<td >' . $row['category'] . '</td>';
                                        echo '<td >' . $row['building'] . " / " . $row['floor'] . " / " . $row['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row['images'] . '</td>';
                                        echo '<td >' . $row['status'] . '</td>';

                                        echo '</tr>';
                                    }
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo '<table>';
                                    echo "<div class=noDataImgH>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                    echo '</table>';
                                }
                                ?>
                            </div>
                        </div>


                        <!--Tab for table 2-->
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="table-content" id="exportContentUnderMaintenance">
                                <div class='table-header'>
                                    <div class='headerskie'>
                                        <span>TRACKING #</span>
                                        <span>DATE & TIME</span>
                                        <span>CATEGORY</span>
                                        <span>LOCATION</span>
                                        <span>STATUS</span>
                                    </div>
                                </div>
                                <!--Content of table 2-->
                                <?php
                                if ($result2->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row2 = $result2->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row2['assetId'] . '</td>';
                                        echo '<td >' . $row2['date'] . '</td>';
                                        echo '<td >' . $row2['category'] . '</td>';
                                        echo '<td >' . $row2['building'] . " / " . $row2['floor'] . " / " . $row2['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['images'] . '</td>';
                                        echo '<td >' . $row2['status'] . '</td>';
                                        echo '</tr>';
                                    }
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo '<table>';
                                    echo "<div class=noDataImgH>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                    echo '</table>';
                                }
                                ?>
                            </div>
                        </div>

                        <!--Tab for table 3 - Replacement -->
                        <div class="tab-pane fade" id="pills-replace" role="tabpanel" aria-labelledby="replace-tab">
                            <div class="table-content" id="exportContentReplacement">
                                <div class='table-header'>
                                    <div class='headerskie'>
                                        <span>TRACKING #</span>
                                        <span>DATE & TIME</span>
                                        <span>CATEGORY</span>
                                        <span>LOCATION</span>
                                        <span>STATUS</span>
                                    </div>
                                </div>
                                <!--Content of table 3-->
                                <?php
                                if ($result3->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row3 = $result3->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row3['assetId'] . '</td>';
                                        echo '<td >' . $row3['date'] . '</td>';
                                        echo '<td >' . $row3['category'] . '</td>';
                                        echo '<td >' . $row3['building'] . " / " . $row3['floor'] . " / " . $row3['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['images'] . '</td>';
                                        echo '<td >' . $row3['status'] . '</td>';
                                        echo '</tr>';
                                    }
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo '<table>';
                                    echo "<div class=noDataImgH>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                    echo '</table>';
                                }
                                ?>
                            </div>
                        </div>

                        <!--Tab for table 4 - Repair -->
                        <div class="tab-pane fade" id="pills-repair" role="tabpanel" aria-labelledby="repair-tab">
                            <div class="table-content" id="exportContentNeedforRepair">
                                <div class='table-header'>
                                    <div class='headerskie4'>
                                        <span class="tab4">TRACKING #</span>
                                        <span class="tab4">DATE & TIME</span>
                                        <span class="tab4">CATEGORY</span>
                                        <span class="tab4">LOCATION</span>
                                        <span class="tab4">STATUS</span>
                                        <span class="tab4">ASSIGNED NAME</span>
                                    </div>
                                </div>
                                <!--Content of table 4-->
                                <?php
                                if ($result4->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row4 = $result4->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row4['assetId'] . '</td>';
                                        echo '<td>' . $row4['date'] . '</td>';
                                        echo '<td>' . $row4['category'] . '</td>';
                                        echo '<td>' . $row4['building'] . " / " . $row4['floor'] . " / " . $row4['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['images'] . '</td>';
                                        echo '<td >' . $row4['status'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['assignedBy'] . '</td>';
                                        if (empty($row4['assignedName'])) {
                                            // Pagwalang data eto ilalabas
                                            echo '<td>';
                                            echo '<form method="post" action="">';
                                            echo '<input type="hidden" name="assetId" value="' . $row4['assetId'] . '">';
                                            echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#exampleModal5">Assign</button>';
                                            echo '</form>';
                                            echo '</td>';
                                        } else {
                                            // Pagmeron data eto ilalabas
                                            echo '<td>' . $row4['assignedName'] . '</td>';
                                        }
                                        echo '</tr>';
                                    }
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo '<table>';
                                    echo "<div class=noDataImgH>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                    echo '</table>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <div class="pagination-reports">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>

        <section>
            <!--Modal sections-->
            <!--Assign Modal for table 4-->
            <div class="modal-parent">
                <div class="modal modal-xl fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content assingee-container">
                            <div class="assignee-header">
                                <label for="assignedName" class="form-label assignee-tag">CHOOSE A MAINTENANCE PERSONNEL:
                                </label>
                            </div>
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3" id="assignPersonnelForm">
                                    <h5></h5>
                                    <input type="hidden" name="assignMaintenance">
                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="building" class="form-label">Building:</label>
                                        <input type="text" class="form-control" id="building" name="building" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="floor" class="form-label">Floor:</label>
                                        <input type="text" class="form-control" id="floor" name="floor" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select assignedName" id="assignedName" name="assignedName" style="color: black;">
                                            <?php
                                            // Assuming you have a database connection established in $conn
                                            // SQL to fetch personnel with the role of "Maintenance Personnel"
                                            $assignSql = "SELECT firstName, middleName, lastName FROM account WHERE userlevel = '3'";
                                            $personnelResult = $conn->query($assignSql);


                                            if ($personnelResult) {
                                                while ($row = $personnelResult->fetch_assoc()) {
                                                    $fullName = $row['firstName'] . ' ' . $row['lastName'];
                                                    // Echo each option within the select
                                                    echo '<option value="' . htmlspecialchars($fullName) . '">' . htmlspecialchars($fullName) . '</option>';
                                                }
                                            } else {
                                                // Handle potential errors or no results
                                                echo '<option value="No Maintenance Personnel Found">';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </form>

                                <div class="col-4" style="display:none">
                                    <label for="assignedBy" class="form-label">Assigned By:</label>
                                    <input type="text" class="form-control" id="assignedBy" name="assignedBy" readonly />
                                </div>
                            </div>
                            <div class="footer">
                                <button type="button" class="btn add-modal-btn" onclick="assignPersonnel()">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit for table 4
            <div class="modal fade" id="staticBackdrop5" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="assignMaintenance" data-bs-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form> -->
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
        <script src="../../src/js/reports.js"></script>
        <!-- Add this script after your existing scripts -->
        <!-- Add this script after your existing scripts -->



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

        <script>
            //PARA MAGDIRECT KA SA PAGE 
            function redirectToPage(building, floor, assetId) {
                var newLocation = '';
                if (building === 'New Academic' && floor === '1F') {
                    newLocation = "../../users/building-manager/NEB/NEWBF1.php";
                } else if (building === 'Yellow' && floor === '1F') {
                    newLocation = "../../users/building-manager/OLB/OLBF1.php";
                } else if (building === 'Korphil' && floor === '1F') {
                    newLocation = "../../users/building-manager/KOB/KOBF1.php";
                } else if (building === 'Bautista' && floor === 'Basement') {
                    newLocation = "../../users/building-manager/BAB/BABF1.php";
                } else if (building === 'Belmonte' && floor === '1F') {
                    newLocation = "../../users/building-manager/BEB/BEBF1.php";
                } else if (building === 'Admin' && floor === '1F') {
                    newLocation = "../../users/building-manager/ADB/ADBF1.php";
                } else if (building === 'Techvoc' && floor === '1F') {
                    newLocation = "../../users/building-manager/TEB/TEBF1.php";
                } else if (building === 'Chinese B' && floor === '1F') {
                    newLocation = "../../users/building-manager/CHB/CHBF1.php";
                } else if (building === 'Multipurpose' && floor === '1F') {
                    newLocation = "../../users/building-manager/MUB/MUBF1.php";
                }

                // Append the assetId to the URL as a query parameter
                window.location.href = newLocation + '?assetId=' + assetId;
            }

            $(document).on('click', 'table tr', function() {
                var assetId = $(this).find('td:eq(0)').text(); // Assuming first TD is the assetId
                var building = $(this).find('td:eq(3)').text().split(' / ')[0]; // Adjust the index as needed
                var floor = $(this).find('td:eq(3)').text().split(' / ')[1]; // Adjust the index as needed
                redirectToPage(building, floor, assetId);
            });
        </script>


        <script>
            $(document).ready(function() {

                // Function to populate the modal fields
                function populateModal(row, modalId) {
                    $(modalId + " #assetId").val(row.find("td:eq(0)").text());
                    $(modalId + " #date").val(row.find("td:eq(1)").text());
                    $(modalId + " #category").val(row.find("td:eq(2)").text());
                    $(modalId + " #building").val(row.find("td:eq(4)").text());
                    $(modalId + " #floor").val(row.find("td:eq(5)").text());
                    $(modalId + " #room").val(row.find("td:eq(6)").text());
                    $(modalId + " #images").val(row.find("td:eq(7)").text());
                    $(modalId + " #status").val(row.find("td:eq(8)").text());
                    $(modalId + " #assignedBy").val(row.find("td:eq(9)").text());
                    $(modalId + " #assignedName").val(row.find("td:eq(10)").text());
                }

                // Event delegation for dynamically loaded content
                // For "Working" tab table rows
                $(document).on("click", "#pills-manager .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal");
                    $("#exampleModal").modal("show");
                });

                // For "Under Maintenance" tab table rows
                $(document).on("click", "#pills-profile .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal2");
                    $("#exampleModal2").modal("show");
                });

                $(document).on("click", "#pills-replace .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal3");
                    $("#exampleModal3").modal("show");
                });

                $(document).on("click", "#pills-repair .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal4");
                    $("#exampleModal4").modal("show");
                });

                //PARA TO SA PAGASSIGN MODAL
                $(document).on("click", "#pills-repair .view-btn", function(event) {
                    event.stopPropagation(); // Prevent the click from reaching the parent <tr>

                    // Get the closest parent row of the clicked button
                    var row = $(this).closest("tr");

                    // Populate the modal with data from the row
                    $("#exampleModal5 #assetId").val(row.find("td:eq(0)").text());
                    $("#exampleModal5 #date").val(row.find("td:eq(1)").text());
                    $("#exampleModal5 #category").val(row.find("td:eq(2)").text());
                    $("#exampleModal5 #building").val(row.find("td:eq(4)").text());
                    $("#exampleModal5 #floor").val(row.find("td:eq(5)").text());
                    $("#exampleModal5 #room").val(row.find("td:eq(6)").text());
                    $("#exampleModal5 #images").val(row.find("td:eq(7)").text());
                    $("#exampleModal5 #status").val(row.find("td:eq(8)").text()).change();
                    $("#exampleModal5 #assignedBy").val(row.find("td:eq(9)").text());
                    $("#exampleModal5 #assignedName").val(row.find("td:eq(10)").text());

                    // Finally, show the modal
                    $("#exampleModal5").modal("show");
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                // Bind the filter function to the input field
                $("#search-box").on("input", function() {
                    var query = $(this).val().toLowerCase();
                    filterTable(query);
                });

                function filterTable(query) {
                    $(".table-container tbody tr").each(function() {
                        var row = $(this);
                        var archiveIDCell = row.find("td:eq(0)"); // Archive ID column
                        var firstNameCell = row.find("td:eq(1)"); // FirstName column
                        var middleNameCell = row.find("td:eq(2)");
                        var lastNameCell = row.find("td:eq(3)");
                        var dateCell = row.find("td:eq(5)");
                        var actionCell = row.find("td:eq(6)");

                        // Get the text content of each cell
                        var archiveIDText = archiveIDCell.text().toLowerCase();
                        var firstNameText = firstNameCell.text().toLowerCase();
                        var middleNameText = middleNameCell.text().toLowerCase();
                        var lastNameText = lastNameCell.text().toLowerCase();
                        var dateText = dateCell.text().toLowerCase();
                        var actionText = actionCell.text().toLowerCase();

                        // Check if any of the cells contain the query
                        var showRow = archiveIDText.includes(query) ||
                            firstNameText.includes(query) ||
                            middleNameText.includes(query) ||
                            lastNameText.includes(query) ||
                            dateText.includes(query) ||
                            actionText.includes(query) ||
                            archiveIDText == query || // Exact match for Archive ID
                            firstNameText == query || // Exact match for FirstName
                            middleNameText == query || // Exact match for LastName
                            lastNameText == query || // Exact match for LastName
                            dateText == query || // Exact match for LastName
                            actionText == query; // Exact match for LastName

                        // Show or hide the row based on the result
                        if (showRow) {
                            row.show();
                        } else {
                            row.hide();
                        }
                    });
                }
            });
        </script>

        <script>
            $(document).ready(function() {
                function filterTable() {
                    var searchQuery = $('#search-box').val().toLowerCase();
                    var columnIndex = parseInt($('#search-filter').val());

                    $('#data-table tbody tr').each(function() {
                        var cellText = $(this).find('td').eq(columnIndex).text().toLowerCase();
                        if (cellText.indexOf(searchQuery) !== -1) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }

                // Event listener for search input
                $('#search-box').on('input', filterTable);

                // Event listener for filter dropdown change
                $('#search-filter').change(function() {
                    $('#search-box').val(''); // Clear the search input
                    filterTable(); // Filter table with new criteria
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                function searchTable() {
                    var input, filter, table, tr, td, i;
                    input = document.getElementById("search-box");
                    filter = input.value.toUpperCase();
                    table = document.getElementById("myTabContent"); // Use the ID of your table container
                    tr = table.getElementsByTagName("tr");
                    var selectedFilter = document.getElementById("filter-criteria").value;

                    for (i = 1; i < tr.length; i++) { // Start with 1 to avoid the header
                        td = tr[i].getElementsByTagName("td");
                        if (td.length > 0) {
                            var searchText = "";
                            if (selectedFilter === "all") {
                                // Concatenate all the text content from the cells for "All" search
                                for (var j = 0; j < td.length; j++) {
                                    searchText += td[j].textContent.toUpperCase();
                                }
                            } else {
                                // Find the index for the selected filter
                                var columnIndex = getColumnIndex(selectedFilter);
                                searchText = td[columnIndex].textContent.toUpperCase();
                            }

                            // Show or hide the row based on whether the searchText contains the filter
                            if (searchText.indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else {
                                tr[i].style.display = "none";
                            }
                        }
                    }
                }

                // Utility function to get the column index based on the filter selected
                function getColumnIndex(filter) {
                    // Adjust these indices to match your table's structure
                    var columns = {
                        'reportId': 0,
                        'date': 1,
                        'category': 2,
                        'location': 3, // Assuming 'location' is a single column that includes building/floor/room
                        'status': 4
                    };
                    return columns[filter] || 0; // Default to the first column if the filter is not found
                }

                // Attach the search function to the keyup event of the search box
                $("#search-box").keyup(searchTable);
            });
        </script>

        <script>
            $(document).ready(function() {
                // Function to update hidden input with the active status
                function updateStatusInput(tab) {
                    let status;
                    switch (tab) {
                        case 'pills-manager':
                            status = 'Working';
                            break;
                        case 'pills-profile':
                            status = 'Under Maintenance';
                            break;
                        case 'pills-replace':
                            status = 'For Replacement';
                            break;
                        case 'pills-repair':
                            status = 'Need Repair';
                            break;
                        default:
                            status = 'Unknown';
                    }
                    $('input[name="status"]').val(status); // Update the hidden input's value
                }

                // Initial tab selection handling
                let tabLastSelected = sessionStorage.getItem("lastTab");
                if (!tabLastSelected) {
                    $("#pills-manager").addClass("show active");
                    $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                    updateStatusInput('pills-manager'); // Set default status
                } else {
                    $(`#${tabLastSelected}`).addClass("show active");
                    $(`.nav-link[data-bs-target='${tabLastSelected}']`).addClass("active");
                    updateStatusInput(tabLastSelected); // Update status based on sessionStorage
                }

                // Tab click event handling
                $(".nav-link").click(function() {
                    const targetId = $(this).data("bs-target");
                    sessionStorage.setItem("lastTab", targetId); // Update lastTab in sessionStorage
                    $(".tab-pane").removeClass("show active");
                    $(`#${targetId}`).addClass("show active");
                    $(".nav-link").removeClass("active");
                    $(this).addClass("active");
                    updateStatusInput(targetId); // Update the hidden input with the new status
                });
            });
        </script>

        <script>
            document.getElementById('exportBtn').addEventListener('click', function() {
                var filterCriteria = document.getElementById('filter-criteria').value;
                var searchQuery = document.getElementById('search-box').value; // Get the value of the search box
                var formData = new FormData(document.getElementById('exportForm'));
                formData.append('filterType', filterCriteria);
                formData.append('searchQuery', searchQuery); // Include the search query in the FormData

                Swal.fire({
                    title: 'Choose the file format',
                    showDenyButton: true,
                    // showCancelButton: true,
                    confirmButtonText: 'PDF',
                    denyButtonText: `Excel`,
                    // cancelButtonText: 'Word',
                }).then((result) => {
                    if (result.isConfirmed) {
                        formData.append('submit', 'Export to PDF');
                        performExport(formData, 'export-pdf.php');
                    } else if (result.isDenied) {
                        formData.append('submit', 'Export to Excel');
                        performExport(formData, 'export-excel.php');
                    }
                    // else if (result.dismiss === Swal.DismissReason.cancel) {
                    //     formData.append('submit', 'Export to Word');
                    //     performExport(formData, 'export-word.php');
                    // }
                });
            });

            function performExport(formData, endpoint) {
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
                        const tabIdentifier = sessionStorage.getItem("lastTab") || 'pills-manager';
                        const tabNameMap = {
                            'pills-manager': 'Working-Assets',
                            'pills-profile': 'Under-Maintenance-Assets',
                            'pills-replace': 'For-Replacement-Assets',
                            'pills-repair': 'Need-Repair-Assets',
                        };
                        const activeTabName = tabNameMap[tabIdentifier] || 'Exported-Data';
                        const fileExtension = getFileExtension(endpoint);
                        const fileName = `${activeTabName}.${fileExtension}`;

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
                if (endpoint.includes('word')) return 'docx';
                return '';
            }
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