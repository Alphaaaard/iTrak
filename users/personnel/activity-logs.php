<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

date_default_timezone_set('Asia/Manila');

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

    // Adjust the SQL to check the 'action' field for the logged-in user's name
 // Adjust the SQL to fetch logs for the logged-in user's accountID
$sqlReport = "SELECT ac.*, a.firstName, a.middleName, a.lastName
FROM activitylogs AS ac
LEFT JOIN account AS a ON ac.accountID = a.accountID
WHERE ac.tab='Report' AND ac.accountID = ?
ORDER BY ac.date DESC";

// Prepare the SQL statement
$stmt = $conn->prepare($sqlReport);

// Bind the parameter and execute
$stmt->bind_param("i", $accountId);
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
               WHERE al.tab='Report' AND al.p_seen = '0' AND al.accountID != ? AND action NOT LIKE 'Changed status of asset ID%'
               ORDER BY al.date DESC 
               LIMIT 5"; // Set limit to 5

// Prepare the SQL statement
$stmtLatestLogs = $conn->prepare($sqlLatestLogs);

// Bind the parameter to exclude the current user's account ID
$stmtLatestLogs->bind_param("i", $loggedInAccountId);

// Execute the query
$stmtLatestLogs->execute();
$resultLatestLogs = $stmtLatestLogs->get_result();

$unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs 
WHERE p_seen = '0' AND accountID != ? AND action NOT LIKE 'Changed status of asset ID%'";
$stmt = $conn->prepare($unseenCountQuery);
$stmt->bind_param("i", $loggedInAccountId);
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
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Activity Logs</title>
        <!-- BOOTSTRAP -->
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/activity-logs.css" />
        <style>
            #map {
                display: none;
            }
        </style>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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

                <a href="./activity-logs.php">
                    <li class="active">
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
                    <div id="map"></div>

                    <header>
                        <div class="cont-header">
                            <h1 class="tab-name">Activity Logs</h1>
                            <div class="tbl-filter">
                                <select name="filterRole" id="filterRole" onchange="filterDate(this.value)">
                                    <option value="newest">Newest</option>
                                    <option value="oldest">Oldest</option>
                                </select>
                                <form class="d-flex" role="search">
                                    <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" onkeyup="searchTable()" />
                                </form>
                            </div>
                        </div>
                    </header>
                    <div class="new-nav">
                        <ul>
                            <li><a href="#" class="nav-link" data-bs-target="pills-report">Report History</a></li>
                        </ul>
                    </div>
                    <div class="tab-content pt" id="myTabContent">

                    </div>
                    <!-- Report History Tab Content -->
                    <div class="tab-pane fade show active" id="pills-report" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="table-content">
                            <div class='table-header'>
                                <table>
                                    <tr>
                                        <th>NAME</th>
                                        <th>DATE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            if ($resultReport->num_rows > 0) {
                                echo "<div class='table-container'>";
                                echo "<table>";
                                echo "<tbody>";
                                while ($row = $resultReport->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['firstName'] . " " . $row['lastName'] . '</td>';
                                    echo '<td style="display:none">' . $row['activityId'] . '</td>';
                                    echo '<td style="display:none">' . $row['firstName'] . '</td>';
                                    echo '<td style="display:none">' . $row['middleName'] . '</td>';
                                    echo '<td style="display:none">' . $row['lastName'] . '</td>';
                                    echo '<td>' . $row['date'] . '</td>';
                                    echo '<td>' . $row['action'] . '</td>';
                                    echo '</tr>';
                                }
                                echo "</tbody>";
                                echo "</table>";
                                echo "</div>";
                            } else {
                                echo '<table>';
                                echo "<div class=noDataImgH>";
                                echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                echo "</tbody>";
                                echo "</table>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                        <div id="pagination-container-report" class="pagination-container"></div>
                    </div>
                    <!--EMPLOYEE INFORMATION MODALS-->

                </div>
                </div>
            </main>
            <!-- MAIN -->
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

        <!-- CONTENT -->
        <!-- SCRIPTS -->
        <script src="../../src/js/main.js"></script>
        <script src="../../src/js/profileModalController.js"></script>
        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <!-- SCRIPTS -->


        <script>
            $(document).ready(function() {
                // Bind the filter function to the search input field
                $("#search-box").on("input", function() {
                    var query = $(this).val().toLowerCase();
                    filterTable(query);

                    // Recalculate and reset pagination for each tab after filtering

                    resetPaginationForFilteredResults('#pills-report .table-container tbody', 'pagination-container-report', 5);
                });

                // Updated filterTable function
                function filterTable(query) {
                    $(".table-container tbody tr").each(function() {
                        var row = $(this);
                        var text = row.text().toLowerCase();
                        var isMatch = text.includes(query);
                        row.toggle(isMatch); // Show or hide the row based on the search match
                    });
                }

                // Show or hide the row based on the result
                if (showRow) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        </script>
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
            function filterDate(order) {
                var activeTabContainer = document.querySelector('.tab-pane.fade.active .table-container'); // Select only the active tab's table container
                var rows = Array.from(activeTabContainer.querySelectorAll('table tbody tr')); // Select only rows within tbody of the active tab's table


                // Sort rows based on the date
                var sortedRows = rows.sort(function(a, b) {
                    var dateA = new Date(a.cells[5].textContent); // Adjust to 5th cell for the date
                    var dateB = new Date(b.cells[5].textContent);

                    return (order === 'newest') ? dateB - dateA : dateA - dateB;
                });

                // Re-append rows to the table in sorted order
                var tableBody = activeTabContainer.querySelector('tbody'); // Select the tbody of the active tab's table
                sortedRows.forEach(row => {
                    tableBody.appendChild(row);
                });
            }
        </script>
    </body>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initial setup for pagination on page load for both tabs

            setupPagination('#pills-report .table-container tbody', 'pagination-container-report', 25);

            // Tab click event listeners for dynamic pagination setup on tab switch
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-bs-target');

                    // Clear existing pagination from all tabs

                    document.getElementById('pagination-container-report').innerHTML = '';

                    // Use setTimeout to delay the setupPagination call, ensuring tab content is fully visible
                    setTimeout(() => {
                        if (targetId.includes('general')) {
                            setupPagination('#pills-general .table-container tbody', 'pagination-container-general', 25);
                        } else if (targetId.includes('report')) {
                            setupPagination('#pills-report .table-container tbody', 'pagination-container-report', 25);
                        }
                    }, 100); // Adjust the delay as needed, 100ms is just an example
                });
            });

            // The setupPagination function definition
            function setupPagination(tableBodySelector, paginationContainerId, itemsPerPage) {
                const tbody = document.querySelector(tableBodySelector);
                const rows = tbody.querySelectorAll('tr');
                const pageCount = Math.ceil(rows.length / itemsPerPage);
                const paginationContainer = document.getElementById(paginationContainerId);

                let currentPage = 1;

                function showPage(pageNumber) {
                    console.log(`Showing page: ${pageNumber} for ${tableBodySelector}`); // Debugging log
                    const start = (pageNumber - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    rows.forEach((row, index) => {
                        row.style.display = 'none'; // Hide all rows
                        if (index >= start && index < end) {
                            row.style.display = ''; // Show rows for the current page
                        }
                    });
                }

                function createPaginationControls() {
                    paginationContainer.innerHTML = ''; // Clear existing controls
                    const ul = document.createElement('ul');
                    ul.className = 'pagination';

                    // Create Previous Button
                    createPageButton('Previous', () => currentPage - 1);

                    // Create page number buttons
                    for (let i = 1; i <= pageCount; i++) {
                        createPageButton(i, () => i);
                    }

                    // Create Next Button
                    createPageButton('Next', () => currentPage + 1);

                    paginationContainer.appendChild(ul);

                    function createPageButton(text, pageResolver) {
                        const li = document.createElement('li');
                        li.className = 'page-item';
                        const a = document.createElement('a');
                        a.className = 'page-link';
                        a.href = '#';
                        a.textContent = text;
                        a.addEventListener('click', (e) => {
                            e.preventDefault();
                            const newPage = pageResolver();
                            if (newPage >= 1 && newPage <= pageCount) {
                                currentPage = newPage;
                                showPage(currentPage);
                            }
                        });
                        li.appendChild(a);
                        ul.appendChild(li);
                    }
                }

                showPage(currentPage); // Initialize to show the first page
                createPaginationControls();
            }
        });
    </script>


    </html>