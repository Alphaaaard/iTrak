<?php
session_start();
include_once ("../../config/connection.php");
$conn = connection();
// include_once 'get_current_user_data.php';
date_default_timezone_set('Asia/Manila');
if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['middleName']) && isset($_SESSION['role']) && isset($_SESSION['lastName']) && isset($_SESSION['userLevel'])) {

    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 3) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }


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


    $stmt = $conn->prepare("SELECT picture FROM account WHERE accountId = ?");
    $stmt->bind_param('i', $_SESSION['accountId']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $accountId = $_SESSION['accountId'];
    $fname = $_SESSION['firstName'];
    // $middleName = $_SESSION['middleName'];
    $lastName = $_SESSION['lastName'];

    $sql = "SELECT a.* FROM asset AS a
    JOIN account AS b ON CONCAT(b.firstName, ' ', b.lastName) = a.assignedName
    WHERE (a.status = 'Need Repair' OR a.status = 'For Approval')
    AND b.firstName = '$fname' AND b.lastName = '$lastName'";


    $result = $conn->query($sql) or die($conn->error);

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


        function logActivity($conn, $accountId, $actionDescription, $tabValue)
        {
            $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab) VALUES (?, NOW(), ?, ?)");
            $stmt->bind_param("iss", $accountId, $actionDescription, $tabValue);
            if (!$stmt->execute()) {
                echo "Error logging activity: " . $stmt->error;
            }
            $stmt->close();
        }

        $updateSql = "UPDATE `asset` SET `category`='$category', `building`='$building', `floor`='$floor', `room`='$room', `status`='$status', `assignedName`='$assignedName', `assignedBy`='$assignedBy', `date`='$date' WHERE `assetId`='$assetId'";
        if ($conn->query($updateSql) === TRUE) {
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId to $status.", 'Report');
        } else {
            echo "Error updating asset: " . $conn->error;
        }
        header("Location: reports.php");
    }


    // UPDATE FOR APPROVAL
    if (isset($_POST['approval'])) {
        // Retrieve request_id from the form
        $asset_id2 = $_POST['asset_id'];

        // Retrieve other form data
        $building2 = $_POST['building'];
        $floor2 = $_POST['floor'];
        $room2 = $_POST['room'];
        // $equipment2 = $_POST['equipment'];
        $category2 = $_POST['category'];
        $assignee2 = $_POST['assignee'];
        $status2 = "For Approval";
        $description2 = $_POST['description'];
        $date2 = $_POST['brrt'];

        // Retrieve selected return_reason from radio buttons
        $return_reason = $_POST['return_reason'];


        // SQL UPDATE query
        $sql3 = "UPDATE asset 
             SET category = ?, building = ?, floor = ?, room = ?, 
                 assignedName = ?, status = ?, description = ?, date = ?,
                 return_reason = ?
             WHERE assetId = ?";

        // Prepare the SQL statement
        $stmt3 = $conn->prepare($sql3);

        // Bind parameters
        $stmt3->bind_param("sssssssssi", $category2, $building2, $floor2, $room2, $assignee2, $status2, $description2, $date2, $return_reason, $asset_id2);

        // Execute the query
        if ($stmt3->execute()) {
            // Update successful, redirect back to batasan.php or any other page
            header("Location: assigned-tasks.php");
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
            $asset_iddone = $_POST['asset_id'];
            $statusdone = "Working";
    
    
            // SQL UPDATE query
            $sqldone = "UPDATE asset
                 SET status = ?      
                 WHERE assetId = ?";
    
            // Prepare the SQL statement
            $stmt5 = $conn->prepare($sqldone);
    
            // Bind parameters
            $stmt5->bind_param("si", $statusdone, $asset_iddone);
    
    
            // Execute the query
            if ($stmt5->execute()) {
                // Check if status changed from pending to done
                if ($status_before_update == 'Need Repair' && $statusdone == 'Working') {
                    // Log the activity
                    $action = "Changed status of Task ID $request_id5 from Need Repair to Working";
                    insertActivityLog($conn, $accountId, $action);
                }
    
                // Update successful, redirect back to batasan.php or any other page
                header("Location: assigned-tasks.php");
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
        <title>iTrak | Assigned Tasks</title>
        <!-- BOOTSTRAP -->
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/reports.css" />
        <link rel="stylesheet" href="../../src/css/assigned-task.css" />
        <style>
            #map {
                display: none;
            }
        </style>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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
                            <a href="assigned-tasks.php" class="view-all">View All</a>

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
    <!-- NAVBAR -->
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
            <li class="active">
                <a href="./assigned-tasks.php">
                    <i class="bi bi-geo-alt"></i>
                    <span class="text">Assigned Tasks</span>
                </a>
            </li>
            <li>
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
    <!-- CONTENT -->
    <section id="content">
        <div id="map"></div>

        <!-- MAIN -->
        <main>
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

                        <select id="rows-display-dropdown" class="form-select dropdown-rows"
                            aria-label="Default select example">
                            <option value="20" selected>Show 20 rows</option>
                            <option class="hidden"></option>
                            <option value="50">Show 50 rows</option>
                            <option value="100">Show 100 rows</option>
                            <option value="150">Show 150 rows</option>
                            <option value="200">Show 200 rows</option>
                        </select>

                        <!-- Search Box -->
                        <form class="d-flex col-sm-5" role="search" id="searchForm">
                            <input class="form-control icon" type="search" placeholder="Search" aria-label="Search"
                                id="search-box" name="q" />
                        </form>


                    </div>
                </div>
            </header>
            <script>
                // Get elements from the DOM
                const filterCriteria = document.getElementById('filter-criteria');
                const searchBox = document.getElementById('search-box');

                // Event listener for the filter dropdown changes
                filterCriteria.addEventListener('change', function () {
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
            <!--Tab for table 4 - Repair -->
            <div class="tab-content pt" id="myTabContent">
                <div class="tab-pane fade show active" id="pills-repair" role="tabpanel" aria-labelledby="repair-tab">
                    <div class="table-content">
                        <div class='table-header personnel-table-header'>
                            <table>
                                <tr>
                                    <th>TRACKING #</th>
                                    <th>DATE & TIME</th>
                                    <th>CATEGORY</th>
                                    <th>LOCATION</th>
                                    <th>STATUS</th>
                                    <th>ASSIGNEE</th>
                                    <th></th>
                                </tr>
                            </table>
                        </div>
                        <!--Content of table 4-->
                        <?php
                        if ($result->num_rows > 0) {
                            echo "<div class='table-container'>";
                            while ($row = $result->fetch_assoc()) {
                                $date = new DateTime($row['date']); // Create DateTime object from fetched date
                                $date->modify('+8 hours'); // Add 8 hours
                                $formattedDate = $date->format('Y-m-d H:i:s'); // Format to SQL datetime format
                        
                                echo "<table>";
                                echo '<tr>';
                                echo '<td>' . $row['assetId'] . '</td>';
                                echo '<td>' . $formattedDate . '</td>';
                                echo '<td>' . $row['category'] . '</td>';
                                echo '<td>' . $row['building'] . " / " . $row['floor'] . " / " . $row['room'] . '</td>';
                                echo '<td style="display: none;">' . $row['building'] . '</td>';
                                echo '<td style="display: none;">' . $row['floor'] . '</td>';
                                echo '<td style="display: none;">' . $row['room'] . '</td>';
                                echo '<td style="display: none;">' . $row['images'] . '</td>';
                                echo '<td >' . $row['status'] . '</td>';
                                echo '<td style="display: none;">' . $row['assignedBy'] . '</td>';
                                if (empty($row['assignedName'])) {
                                    // Pagwalang data eto ilalabas
                                    echo '<td>';
                                    echo '<form method="post" action="">';
                                    echo '<input type="hidden" name="assetId" value="' . $row['assetId'] . '">';
                                    echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#exampleModal5">Assign</button>';
                                    echo '</form>';
                                    echo '</td>';
                                } else {
                                    // Pagmeron data eto ilalabas
                                    echo '<td>' . $row['assignedName'] . '</td>';
                                }
                                echo '<td>';
                                echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#ForView">View</button>';
                                echo '</td>';
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
            </div>
            </div>
            </div>
        </main>
    </section>

    <!--MODAL FOR THE VIEW-->
    <div class="modal-parent">
        <div class="modal modal-xl fade" id="ForView" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>View Task</h5>

                        <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" class="row g-3">
                            <div class="col-4" style="display:none;">
                                <label for="request_id" class="form-label">Tracking #:</label>
                                <input type="text" class="form-control" id="asset_id" name="asset_id" readonly />
                            </div>
                            <div class="col-4" style="display:none;">
                                <label for="date" class="form-label">Date & Time:</label>
                                <input type="text" class="form-control" id="date" name="date" readonly />
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
                                <label for="category" class="form-label">Category:</label>
                                <input type="text" class="form-control" id="category" name="category" value="Carpentry"
                                    readonly>
                            </div>

                            <div class="col-4" style="display:none;">
                                <label for="category" class="form-label">Status:</label>
                                <input type="text" class="form-control" id="status" name="status" readonly>
                            </div>

                            <div class="col-4">
                                <label id="assignee-label" for="assignee" class="form-label">Assignee:</label>
                                <input type="text" class="form-control" id="assignee" name="assignee" readonly />
                            </div>

                            <div class="col-4">
                                <label for="brrt" class="form-label">Date:</label>
                                <input type="text" class="form-control" id="brrt" name="brrt" readonly />
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description:</label>
                                <input type="text" class="form-control" id="description" name="description" readonly />
                            </div>

                            <div class="col-12" style="display:none">
                                <label for="return_reason_show" class="form-label">Transfer
                                    Reason:</label>
                                <input type="text" class="form-control" id="return_reason_show" name="return_reason"
                                    readonly />
                            </div>

                            <div class="footer">
                                <button type="button" class="btn add-modal-btn" id="transferBtn" data-bs-toggle="modal"
                                    data-bs-target="#ForTransfer">
                                    Transfer
                                </button>
                                <button type="button" class="btn add-modal-btn" id="doneBtn"
                                    data-bs-toggle="modal" data-bs-target="#ForDone">
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
        <div class="modal modal-xl fade" id="ForTransfer" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Transfer Task</h5>

                        <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body" id="transfer-body">

                        <div class="col-12">
                            <label class="form-label">Select a reason:</label>
                        </div>

                        <div class="col-12" id="transfer-options">
                            <div class="form-check">
                                <div>
                                    <input class="form-check-input" type="radio" value="Lack of Tools"
                                        id="reason_lack_of_tools" name="reason" onchange="updateTextInput(this)">
                                    <label class="form-check-label" for="reason_lack_of_tools">Lack of Tools</label>

                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Insufficient Personnel"
                                    id="reason_insufficient_personnel" name="reason" onchange="updateTextInput(this)">
                                <label class="form-check-label" for="reason_insufficient_personnel">Insufficient
                                    Personnel</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Skills Mismatch"
                                    id="reason_skills_mismatch" name="reason" onchange="updateTextInput(this)">
                                <label class="form-check-label" for="reason_skills_mismatch">Skills
                                    Mismatch</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Coordination with Other Departments"
                                    id="reason_coordination_with_other_departments" name="reason"
                                    onchange="updateTextInput(this)">
                                <label class="form-check-label"
                                    for="reason_coordination_with_other_departments">Coordination with
                                    Other Departments</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="Outsource" id="reason_outsource"
                                    name="reason" onchange="updateTextInput(this)">
                                <label class="form-check-label" for="reason_outsource">Outsource</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="reason_others" value="" name="reason"
                                    onchange="updateTextInput(this)">
                                <label class=" form-check-label" for="reason_others">Others</label>
                            </div>
                        </div>

                        <div class="col-12" id="othersInput" style="display:none;">
                            <label for="description" class="form-label">Others:</label>
                            <textarea class="form-control" id="return_reason" name="return_reason"></textarea>
                        </div>
                    </div>

                    <div class="footer" id="transfer-footer">
                        <button type="button" class="btn add-modal-btn" data-bs-toggle="modal"
                            data-bs-target="#ForSave">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Edit for approval-->
    <div class="modal fade" id="ForSave" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-footer">
                    Are you sure you want to transfer this task?
                    <div class="modal-popups">
                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                        <button class="btn add-modal-btn" name="approval" data-bs-dismiss="modal">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Edit for done-->
    <div class="modal fade" id="ForDone" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-footer">
                    Are you sure you want to mark this task as completed?
                    <div class="modal-popups">
                        <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                        <button class="btn add-modal-btn" name="done" data-bs-dismiss="modal">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>






    <!-- PROFILE MODALS -->
    <?php include_once 'modals/modal_layout.php'; ?>


    <!-- Add this script after your existing scripts -->
    <!-- Add this script after your existing scripts -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>


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
    <script src="../../src/js/main.js"></script>
    <script src="../../src/js/SIKE.js"></script>
    <script src="../../src/js/profileModalController.js"></script>
    <script src="../../src/js/logout.js"></script>

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


    <script>
        setInterval(function () {
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
        $(document).ready(function () {

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


            $(document).on("click", "#pills-repair .table-container table tbody tr", function () {
                var row = $(this);
                populateModal(row, "#exampleModal4");
                $("#exampleModal4").modal("show");
            })
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
                $(".table-container tbody tr").each(function () {
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
        $(document).ready(function () {
            function filterTable() {
                var searchQuery = $('#search-box').val().toLowerCase();
                var columnIndex = parseInt($('#search-filter').val());

                $('#data-table tbody tr').each(function () {
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
            $('#search-filter').change(function () {
                $('#search-box').val(''); // Clear the search input
                filterTable(); // Filter table with new criteria
            });
        });
    </script>
    <script>
        //PARA MAGDIRECT KA SA PAGE 
        function redirectToPage(building, floor, assetId) {
            var newLocation = '';
            if (building === 'New Academic' && floor === '1F') {
                newLocation = "../../users/building-personnel/NEB/NEWBF1.php";
            } else if (building === 'Yellow' && floor === '1F') {
                newLocation = "../../users/building-personnel/OLB/OLBF1.php";
            } else if (building === 'Korphil' && floor === '1F') {
                newLocation = "../../users/building-personnel/KOB/KOBF1.php";
            } else if (building === 'Bautista' && floor === 'Basement') {
                newLocation = "../../users/building-personnel/BAB/BABF1.php";
            } else if (building === 'Belmonte' && floor === '1F') {
                newLocation = "../../users/building-personnel/BEB/BEBF1.php";
            } else if (building === 'Admin' && floor === '1F') {
                newLocation = "../../users/building-personnel/ADB/ADBF1.php";
            } else if (building === 'Techvoc' && floor === '1F') {
                newLocation = "../../users/building-personnel/TEB/TEBF1.php";
            } else if (building === 'Chinese B' && floor === '1F') {
                newLocation = "../../users/building-personnel/CHB/CHBF1.php";
            } else if (building === 'Multipurpose' && floor === '1F') {
                newLocation = "../../users/building-personnel/MUB/MUBF1.php";
            }

            // Append the assetId to the URL as a query parameter
            window.location.href = newLocation + '?assetId=' + assetId;
        }

        $(document).on('click', 'table tr', function () {
            var assetId = $(this).find('td:eq(0)').text(); // Assuming first TD is the assetId
            var building = $(this).find('td:eq(3)').text().split(' / ')[0]; // Adjust the index as needed
            var floor = $(this).find('td:eq(3)').text().split(' / ')[1]; // Adjust the index as needed
            redirectToPage(building, floor, assetId);
        });
    </script>

    <script>
        $(document).ready(function () {
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

    <script>
        $(document).ready(function () {
            // Function to populate modal fields
            function populateModal(row) {
                // Populate modal fields with data from the row
                $("#asset_id").val(row.find("td:eq(0)").text());
                $("#category").val(row.find("td:eq(2)").text());
                // If building, floor, and room are concatenated in a single cell, split them
                var buildingFloorRoom = row.find("td:eq(3)").text().split('/ ');
                $("#building").val(buildingFloorRoom[0]);
                $("#floor").val(buildingFloorRoom[1]);
                $("#room").val(buildingFloorRoom[2]);
                $("#assignee").val(row.find("td:eq(10)").text());
                $("#brrt").val(row.find("td:eq(1)").text());
                $("#status").val(row.find("td:eq(8)").text());
                $("#description").val(row.find("td:eq(12)").text());
                $("#return_reason_show").val(row.find("td:eq(15)").text());
            }

            // Click event for the "View" button
            $("button[data-bs-target='#ForView']").click(function () {
                event.stopPropagation();
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
        function showTaskConfirmation() {
        Swal.fire({
            icon: "info",
            title: `Are you sure you want to mark this task as completed?`,
            showCancelButton: true,
            cancelButtonText: "No",
            focusConfirm: false,
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
            let swalConfirm = document.querySelector(".swal2-confirm");
            swalConfirm.setAttribute("name", "done");

            // AJAX
            let form = document.querySelector("#requestForm");
            let xhr = new XMLHttpRequest();

            xhr.open("POST", "../../users/personnel/request.php", true);

            xhr.onerror = function () {
                console.error("An error occurred during the XMLHttpRequest");
            };

            let formData = new FormData(form);
            formData.set("done", swalConfirm);
            xhr.send(formData);

            // success alertbox
            Swal.fire({
                text: "The task has been marked as done!",
                icon: "success",
                timer: 1000,
                showConfirmButton: false,
            }).then((result) => {
                if (result.dismiss || Swal.DismissReason.timer) {
                window.location.reload();
                }
            });
            }
        });
        }
    </script>   
</body>

</html>