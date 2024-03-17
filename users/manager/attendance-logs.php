<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila');
if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {


    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 2) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
    }

    if ($conn->connect_error) {
        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
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


$filterDate = isset($_GET['filterDate']) ? $_GET['filterDate'] : 'all';

$userAccountId = $_SESSION['accountId']; // User's account ID

$query = "SELECT attendanceId, accountId, date, timeIn, timeOut FROM attendancelogs WHERE accountId = ?";

$params = [$userAccountId];
$types = 'i';

if ($filterDate !== 'all') {
    $conditions = [];

    if ($filterDate === 'this_day') {
        $conditions[] = "DATE(date) = CURDATE()";
    } elseif ($filterDate === 'this_week') {
        $conditions[] = "YEARWEEK(date) = YEARWEEK(NOW())";
    } elseif ($filterDate === 'this_month') {
        $conditions[] = "YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())";
    } elseif ($filterDate === 'this_year') {
        $conditions[] = "YEAR(date) = YEAR(NOW())";
    }

    if (!empty($conditions)) {
        $query .= " AND (" . implode(' OR ', $conditions) . ")";
    }
}

$stmt = $conn->prepare($query);

if (!$stmt) {
    die('Error preparing the query: ' . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    die('Error executing the query: ' . $stmt->error);
}

$result = $stmt->get_result();

$stmt->close();

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
                    <div id="map"></div>

                    <header>
                        <div class="cont-header">
                            <h1 class="tab-name"></h1>
                            <div class="form-wrapper">
                                <div class="tbl-filter">
                                    <!-- Wrap the form in a div with the class 'form-container' -->
                                    <!-- <form class="filterType">
                                        <select name="filterType" id="filterType<?= $row['accountId'] ?>" onchange="filterAttendanceData(<?= $row['accountId'] ?>)">
                                            <option value="all">All Days</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                            <option value="year">This Year</option>
                                        </select>
                                    </form> -->

                                    <form action="attendance-logs.php" method="get">
                                        <select name="filterDate" id="filterDate" onchange="this.form.submit()">
                                            <option value="all">All</option>
                                            <option value="this_day" <?php echo ($filterDate === "this_day") ? 'selected' : ''; ?>>This Day</option>
                                            <option value="this_week" <?php echo ($filterDate === "this_week") ? 'selected' : ''; ?>>This Week</option>
                                            <option value="this_month" <?php echo ($filterDate === "this_month") ? 'selected' : ''; ?>>This Month</option>
                                            <option value="this_year" <?php echo ($filterDate === "this_year") ? 'selected' : ''; ?>>This Year</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!--PILL TABS-->
                    <!-- Pills Tabs -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-content personnel-table-content">
                                <div class="table-header">
                                    <table id="attendanceTable<?= $row['accountId'] ?>">
                                        <tr>
                                            <th>Day</th>
                                            <th>Date</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Total Hours</th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row = $result->fetch_assoc()) {
                                        $dayOfWeek = date('l', strtotime($row['date']));
                                        $timeInFormatted = date('h:i A', strtotime($row['timeIn']));
                                        $timeOutFormatted = '';

                                        date_default_timezone_set('Asia/Manila'); // Set the correct time zone, e.g., 'America/New_York'

                                        if (isset($row['timeIn'])) {
                                            $timeIn = strtotime($row['timeIn']);
                                            $currentTime = time(); // Current timestamp

                                            if (isset($row['timeOut'])) {
                                                $timeOut = strtotime($row['timeOut']);
                                                $timeDifference = $timeOut - $timeIn;
                                                $hours = floor($timeDifference / 3600);
                                                $totalHoursFormatted = $hours;
                                                $timeOutFormatted = date('h:i A', $timeOut);
                                            } else {
                                                $timeSinceIn = $currentTime - $timeIn;

                                                $currentHourAndMinute = date('H:i'); // Get the current hour and minute

                                                if ($timeSinceIn > (8 * 3600) || $currentHourAndMinute == '00:00') {
                                                    $totalHoursFormatted = "4";
                                                    $timeOutFormatted = 'Not Timed Out';
                                                } else {
                                                    $totalHoursFormatted = ''; // Set totalHours to empty if 8 hours have NOT been exceeded and it's not midnight
                                                    $timeOutFormatted = ''; // Set timeOut to empty if 8 hours have NOT been exceeded and it's not midnight
                                                }
                                            }
                                        } else {
                                            $totalHoursFormatted = "No TimeIn Recorded"; // In case the user hasn't timed in yet
                                            $timeOutFormatted = ''; // Default value for timeOut in this case
                                        }

                                        echo '<tr data-day="' . htmlspecialchars($dayOfWeek) . '">';
                                        echo '<td>' . htmlspecialchars($dayOfWeek) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                                        echo '<td>' . htmlspecialchars($timeInFormatted) . '</td>';
                                        echo '<td>' . htmlspecialchars($timeOutFormatted) . '</td>';
                                        echo '<td>' . htmlspecialchars($totalHoursFormatted) . '</td>';
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
        <!-- <script>
 document.addEventListener('DOMContentLoaded', function() {
    var managerPill = document.getElementById('manager-pill');
    var personnelPill = document.getElementById('personnel-pill');
    var managerContent = document.getElementById('pills-manager');
    var personnelContent = document.getElementById('pills-personnel');

    function activateTab(pill, content, role) {
        managerPill.classList.remove('active');
        personnelPill.classList.remove('active');
        managerContent.classList.remove('show', 'active');
        personnelContent.classList.remove('show', 'active');

        pill.classList.add('active');
        content.classList.add('show', 'active');

        sessionStorage.setItem('lastPill', role);
        filterTable(role); // Adjusted to pass the active role
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
});

// Optimized filterTable function
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


            </script> -->

        <script>
            var managerPill = document.getElementById('manager-pill');
            var personnelPill = document.getElementById('personnel-pill');
            var managerContent = document.getElementById('pills-manager');
            var personnelContent = document.getElementById('pills-personnel');

            function activateTab(pill, content, role) {
                managerPill.classList.remove('active');
                personnelPill.classList.remove('active');
                managerContent.classList.remove('show', 'active');
                personnelContent.classList.remove('show', 'active');

                pill.classList.add('active');
                content.classList.add('show', 'active');

                sessionStorage.setItem('lastPill', role);
                filterTable(role); // Adjusted to pass the active role
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
            function exportTableToPDF(exportContentId, filename, name) {
                const exportContent = document.getElementById(exportContentId);

                // Find the visually hidden name header within the export content
                const nameHeader = exportContent.querySelector('.h5-like.visually-hidden');
                // Find the table header and increase its top margin to make space for the name
                const tableHeader = exportContent.querySelector('.table-header1');

                // Temporarily update the style to make it visible for the screenshot
                if (nameHeader) {
                    nameHeader.style.position = 'relative'; // Make it affect layout
                    nameHeader.style.visibility = 'visible'; // Make it visible
                    nameHeader.style.fontFamily = 'Poppins, sans-serif'; // Set font family to Poppins
                    nameHeader.style.fontSize = '20px'; // Increase font size
                    nameHeader.style.fontWeight = 'bold'; // Make font-weight bold
                    nameHeader.style.marginBottom = '10px'; // Add some space below the name
                }

                // Increase the top margin of the table header
                if (tableHeader) {
                    tableHeader.style.marginTop = '50px'; // Adjust the space as needed
                }

                // Check if the export content exists
                if (!exportContent) {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'No data available to export.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Exit the function if no content is found
                }

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
                    useCORS: true // This is important if you have images from other domains
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
                    pdf.addImage(imgData, 'PNG', 0, 0);

                    // Add the name text directly on the PDF
                    pdf.setFontSize(13); // Set font size
                    pdf.text(name, 10, 10); // Adjust coordinates as needed
                    pdf.save(filename); // Save the PDF after adding the text

                    Swal.close();
                    Swal.fire({
                        title: 'Done!',
                        text: 'Your PDF has been downloaded.',
                        icon: 'success',
                        timer: 1000,
                        showConfirmButton: false
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location.reload();
                        }
                    });
                }).catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was a problem generating the PDF.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error generating PDF: ', error);
                }).finally(() => {
                    // Revert the name header style to its original state
                    if (nameHeader) {
                        nameHeader.style.position = '';
                        nameHeader.style.visibility = '';
                        nameHeader.style.fontSize = '';
                        nameHeader.style.marginBottom = '';
                    }
                    // Revert the table header style to its original state
                    if (tableHeader) {
                        tableHeader.style.marginTop = ''; // Remove the added margin
                    }
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
    </body>

    </html>