<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();
// include_once 'get_current_user_data.php';
date_default_timezone_set('Asia/Manila');
if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['middleName'])&& isset($_SESSION['role']) && isset($_SESSION['lastName']) && isset($_SESSION['userLevel'])) {
  
        // For personnel page, check if userLevel is 3
        if($_SESSION['userLevel'] != 3) {
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


    $stmt = $conn->prepare("SELECT picture FROM account WHERE accountId = ?");
    $stmt->bind_param('i', $_SESSION['accountId']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $accountId = $_SESSION['accountId'];
    $fname = $_SESSION['firstName'];
    $middleName = $_SESSION['middleName'];
    $lastName = $_SESSION['lastName'];

    $sql = "SELECT a.* FROM asset AS a
            JOIN account AS b ON CONCAT(b.firstName, ' ', b.middleName, ' ', b.lastName) = a.assignedName
            WHERE a.status = 'Need Repair' AND b.firstName = '$fname' AND b.middleName = '$middleName' AND b.lastName = '$lastName'";

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




        $updateSql = "UPDATE `asset` SET `category`='$category', `building`='$building', `floor`='$floor', `room`='$room', `status`='$status', `assignedName`='$assignedName', `assignedBy`='$assignedBy', `date`='$date' WHERE `assetId`='$assetId'";
        if ($conn->query($updateSql) === TRUE) {
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId to $status.", 'Report');
        } else {
            echo "Error updating asset: " . $conn->error;
        }
        header("Location: reports.php");
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







?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Assigned Tasks</title>
        <!-- BOOTSTRAP -->
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>  

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/reports.css" />
        <style>
            #map {
                display: none;
            }
        </style>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    </head>

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
                <header>
                    <div class="cont-header">
                        <h1 class="tab-name">Activity Logs</h1>
                        <div class="tbl-filter">
                            <select name="filterRole" id="filterRole" onchange="">
                                <option value="all">Sort by date</option>
                                <option value="all">Filter</option>
                                <option value="all">Filter</option>
                                <option value="all">Filter</option>
                            </select>
                            <form class="d-flex" role="search">
                                <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" onkeyup="searchTable()" />
                            </form>
                        </div>
                    </div>
                </header>
                        <!--Tab for table 4 - Repair -->
                        <div class="tab-pane fade show active" id="pills-repair" role="tabpanel" aria-labelledby="repair-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>TRACKING #</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>STATUS</th>
                                            <th>ASSIGNEE</th>
                                        </tr>
                                    </table>
                                </div>
                                <!--Content of table 4-->
                                <?php
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row['assetId'] . '</td>';
                                        echo '<td>' . $row['date'] . '</td>';
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
        </section>

           
            <!--Modal for table 4-->
            <div class="modal-parent">
                <div class="modal modal-xl fade show active" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3">
                                    <h5>Report Modal for Repair</h5>
                                    <div class="col-4">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
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
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" readonly />
                                    </div>
                            </div>
                            <div class="footer">
                                <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop4">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit for table 4-->
            <div class="modal fade" id="staticBackdrop4" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="edit" data-bs-dismiss="modal">Yes</button>
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



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>



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

<script src="../../src/js/main.js"></script>
<script src="../../src/js/locationTracker.js"></script>
<script src="../../src/js/profileModalController.js"></script>
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

             
                $(document).on("click", "#pills-repair .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal4");
                    $("#exampleModal4").modal("show");
                })});
            
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
                    if(cellText.indexOf(searchQuery) !== -1) {
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


    </body>

    </html>