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





    // Fetch General activity logs
    $sqlGeneral = "SELECT ac.*, a.firstName, a.middleName, a.lastName
    FROM activitylogs AS ac
    LEFT JOIN account AS a ON ac.accountID = a.accountID
    WHERE ac.tab='General'
    ORDER BY ac.date DESC";
    $resultGeneral = $conn->query($sqlGeneral) or die($conn->error);

    // Fetch Report activity logs
    $sqlReport = "SELECT ac.*, a.firstName, a.middleName, a.lastName
    FROM activitylogs AS ac
    LEFT JOIN account AS a ON ac.accountID = a.accountID
    WHERE ac.tab='Report'
    ORDER BY ac.date DESC";
    $resultReport = $conn->query($sqlReport) or die($conn->error);

    $sql2 = "SELECT ac.*, a.firstName, a.middleName, a.lastName 
    FROM activitylogs AS ac
    LEFT JOIN account AS a ON ac.accountID = a.accountID";
    $result = $conn->query($sql2) or die($conn->error);


    $sql2 = "SELECT * FROM reportlogs";
    $result2 = $conn->query($sql2) or die($conn->error);

    //! commented code also in line 125
    //! bracket later remove this later

















?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | Activity Logs</title>
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
                <li>
                    <a href="./archive.php">
                        <i class="bi bi-archive"></i>
                        <span class="text">Archive</span>
                    </a>
                </li>
                <li class="active">
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
                    <div class="new-nav-container">
                        <!--Content start of tabs-->
                        <div class="new-nav">
                            <ul>
                                <li><a href="#" class="nav-link active pills-general" data-bs-target="pills-general">General History</a></li>
                                <li><a href="#" class="nav-link pills-report" data-bs-target="pills-report">Report History</a></li>
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
                    <div class="tab-content pt" id="myTabContent">
                        <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="home-tab">
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


                                <!-- General History Tab Content -->
                                <?php
                                if ($resultGeneral->num_rows > 0) {
                                    echo "<div class='table-container general-table'>";
                                    echo "<table>";
                                    echo "<tbody>";
                                    while ($row = $resultGeneral->fetch_assoc()) {
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
                            <div id="pagination-container-general" class="pagination-container"></div>
                        </div>
                        <!-- Report History Tab Content -->
                        <div class="tab-pane fade" id="pills-report" role="tabpanel" aria-labelledby="profile-tab">
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
                                    echo "<div class='table-container report-table'>";
                                    echo "<table>";
                                    echo "<tbody>";
                                    while ($row = $resultReport->fetch_assoc()) {


                                        $date = new DateTime($row['date']); // Create DateTime object from fetched date
                                        $date->modify('+8 hours'); // Add 8 hours
                                        $formattedDate = $date->format('Y-m-d H:i:s'); // Format to SQL datetime format



                                        echo '<tr>';
                                        echo '<td>' . $row['firstName'] . " " . $row['lastName'] . '</td>';
                                        echo '<td style="display:none">' . $row['activityId'] . '</td>';
                                        echo '<td style="display:none">' . $row['firstName'] . '</td>';
                                        echo '<td style="display:none">' . $row['middleName'] . '</td>';
                                        echo '<td style="display:none">' . $row['lastName'] . '</td>';
                                        echo '<td>' . $formattedDate . '</td>'; // Display the adjusted date

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
        <script src="../../src/js/logout.js"></script>

        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <!-- SCRIPTS -->







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
            $(document).ready(function() {
                $("#pills-general").addClass("show active");
                $("#pills-report").removeClass("show active");
                $(".nav-link[data-bs-target='pills-general']").addClass("active");
                $(".nav-link[data-bs-target='pills-report']").removeClass("active");

                $(".nav-link").click(function() {
                    const targetId = $(this).data("bs-target");
                    $(".tab-pane").removeClass("show active");
                    $(`#${targetId}`).addClass("show active");
                    $(".nav-link").removeClass("active");
                    $(this).addClass("active");
                });
            });
        </script>
        <script>
            $(document).ready(function() {

                // Bind the filter function to the search input field
                $("#search-box").on("input", function() {
                    var query = $(this).val().toLowerCase();
                    filterTable(query);

                    // Recalculate and reset pagination for each tab after filtering
                    resetPaginationForFilteredResults('#pills-general .table-container tbody', 'pagination-container-general', 5);
                    resetPaginationForFilteredResults('#pills-report .table-container tbody', 'pagination-container-report', 5);
                });

                // Updated filterTable function
                function filterTable(query) {

                    //* keep tracks of the tables row count
                    let generalHasData = false;
                    let reportsHasData = false;

                    $(".general-table tbody tr").each(function() {
                        var row = $(this);
                        var text = row.text().toLowerCase();
                        console.log(text);
                        var isMatch = text.includes(query);
                        // row.toggle(isMatch); // Show or hide the row based on the search match

                        //Show or hide the row based on the result
                        if (isMatch) {
                            generalHasData = true;
                            row.show();
                        } else {
                            row.hide();
                        }
                    });

                    $(".report-table tbody tr").each(function() {
                        var row = $(this);
                        var text = row.text().toLowerCase();
                        var isMatch = text.includes(query);
                        // row.toggle(isMatch); // Show or hide the row based on the search match

                        //Show or hide the row based on the result
                        if (isMatch) {
                            reportsHasData = true;
                            row.show();
                        } else {
                            row.hide();
                        }
                    });

                    // * checks if rows are empty or not using the HasData variables
                    //* appends the tr-td child on the manager or personnel table 
                    if (!generalHasData) {
                        $(".general-table tbody").append("<tr class='emptyMsg'><td>No results found</td></tr>");
                    } else {
                        $('.general-table tbody .emptyMsg').remove();
                    }

                    if (!reportsHasData) {
                        $(".report-table tbody").append("<tr class='emptyMsg'><td>No results found</td></tr>");
                    } else {
                        $('.report-table tbody .emptyMsg').remove();
                    }
                }
            });
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
        document.addEventListener('DOMContentLoaded', function() {

            // Updated filterTable function
            function filterTable(query) {

                let generalHasData = false;
                let reportsHasData = false;

                $(".general-table tbody tr").each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    console.log(text);
                    var isMatch = text.includes(query);
                    // row.toggle(isMatch); // Show or hide the row based on the search match

                    //Show or hide the row based on the result
                    if (isMatch) {
                        generalHasData = true;
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                $(".report-table tbody tr").each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    var isMatch = text.includes(query);
                    // row.toggle(isMatch); // Show or hide the row based on the search match

                    //Show or hide the row based on the result
                    if (isMatch) {
                        reportsHasData = true;
                        row.show();
                    } else {
                        row.hide();
                    }
                });


                // * checks if rows are empty or not using the HasData variables
                //* appends the tr-td child on the manager or personnel table 
                if (!generalHasData) {
                    $(".general-table tbody").append("<tr class='emptyMsg'><td>No results found</td></tr>");
                } else {
                    $('.general-table tbody .emptyMsg').remove();
                }

                if (!reportsHasData) {
                    $(".report-table tbody").append("<tr class='emptyMsg'><td>No results found</td></tr>");
                } else {
                    $('.report-table tbody .emptyMsg').remove();
                }
            }

            //Initial setup for pagination on page load for both tabs
            setupPagination('#pills-general .table-container tbody', 'pagination-container-general', 25);
            setupPagination('#pills-report .table-container tbody', 'pagination-container-report', 25);

            // Tab click event listeners for dynamic pagination setup on tab switch
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-bs-target');

                    // Clear existing pagination from all tabs
                    document.getElementById('pagination-container-general').innerHTML = '';
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
                    // console.log(`Showing page: ${pageNumber} for ${tableBodySelector}`); // Debugging log
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

                    // Calculate the range of pages to display
                    const maxPagesToShow = 3;
                    let startPage = currentPage - Math.floor(maxPagesToShow / 2);
                    startPage = Math.max(startPage, 1);
                    let endPage = startPage + maxPagesToShow - 1;
                    endPage = Math.min(endPage, pageCount);

                    if (endPage - startPage < maxPagesToShow - 1) {
                        startPage = endPage - maxPagesToShow + 1;
                        startPage = Math.max(startPage, 1); // Ensure startPage does not go below 1
                    }

                    // Create Previous Button
                    createPageButton('<<', () => Math.max(currentPage - 1, 1), currentPage === 1);

                    // Create page number buttons within the range
                    for (let i = startPage; i <= endPage; i++) {
                        createPageButton(i, () => i, i === currentPage);
                    }

                    // Create Next Button
                    createPageButton('>>', () => Math.min(currentPage + 1, pageCount), currentPage === pageCount);

                    paginationContainer.appendChild(ul);

                    function createPageButton(text, pageResolver, isDisabled) {
                        const li = document.createElement('li');
                        li.className = 'page-item';
                        if (isDisabled) {
                            li.classList.add('disabled');
                        }
                        const a = document.createElement('a');
                        a.className = 'page-link';
                        a.href = '#';
                        a.textContent = text;
                        a.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (!isDisabled) {
                                const newPage = pageResolver();
                                currentPage = newPage;
                                showPage(currentPage);
                                createPaginationControls(); // Recreate the pagination controls to reflect the new current page
                            }
                        });
                        li.appendChild(a);
                        ul.appendChild(li);
                    }
                }

                showPage(currentPage); // Initialize to show the first page
                createPaginationControls();

                filterTable($("#search-box").val().toLowerCase());
            }
        });
    </script>

    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {
            console.log("Export button clicked");
            var searchQuery = document.getElementById('search-box').value;
            var formData = new FormData(document.getElementById('exportForm'));

            // Determine the tab based on the active tab class
            var activeTab = document.querySelector('.nav-link.active').getAttribute('data-bs-target');
            var tab = activeTab === 'pills-general' ? 'General' : 'Report';

            formData.append('searchQuery', searchQuery);
            formData.append('tab', tab); // Append the active tab context to formData instead of role

            Swal.fire({
                title: 'Choose the file format',
                showDenyButton: true,
                confirmButtonText: 'PDF',
                denyButtonText: 'Excel',
                didOpen: () => {
                    Swal.getConfirmButton().style.setProperty('background-color', '#ff4c4c', 'important');
                    Swal.getConfirmButton().style.setProperty('color', 'white', 'important');

                    Swal.getDenyButton().style.setProperty('background-color', '#09ba23', 'important');
                    Swal.getDenyButton().style.setProperty('color', 'white', 'important');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formData.append('submit', 'Export to PDF');
                    performExport(formData, 'export-pdf-activitylogs.php');
                } else if (result.isDenied) {
                    formData.append('submit', 'Export to Excel');
                    performExport(formData, 'export-excel-activitylogs.php');
                }
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
                    const tabFormatted = formData.get('tab').replace(/ /g, '-'); // Get the tab and replace spaces with hyphens for the file name
                    const fileExtension = getFileExtension(endpoint);
                    const fileName = `${tabFormatted}.${fileExtension}`;

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
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 1300, // closes after 2000 milliseconds (2 seconds)
                        timerProgressBar: true // shows a visual progress bar for the timer
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


    </html>