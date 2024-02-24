<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {

    if ($conn->connect_error) {
        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
    }

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
?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Attendance-Logs</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" /> -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="../../src/css/main.css">
        <link rel="stylesheet" href="../../src/css/attendance-logs.css">
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->

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
                            <!-- <i class="bi bi-bell"></i> -->
                            <!-- <i class="bx bxs-bell"></i> -->
                            <!-- <span class="num"></span> -->
                        </a>
                        <div class="dropdown-content" id="notification-dropdown-content">
                            <h6 class="dropdown-header">Alerts Center</h6>
                            <a href="#">May hindi nagbuhos sa Cr sa Belmonte building</a>
                            <a href="#">Notification 2</a>
                            <a href="#">Notification 3</a>
                            <!-- Add more notification items here -->
                            <a href="#" class="view-all">View All</a>
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
                        <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><img src="../../src/icons/Profile.svg" alt="" class="profile-icons">Profile</a>
                        <!-- <a class="profile-hover" href="#"><img src="../../src/icons/Logout.svg" alt="" class="profile-icons">Settings</a> -->
                        <a class="profile-hover" href="#" id="logoutBtn"><img src="../../src/icons/Settings.svg" alt="" class="profile-icons">Logout</a>
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
                    <header>
                        <div class="cont-header">
                            <h1 class="tab-name">Attendance</h1>
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
                            <div class="table-content">
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


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../src/js/main.js"></script>
        <script src="../../src/js/profileModalController.js"></script>
        <!-- BOOTSTRAP -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->
        <link rel="stylesheet" href="path/to/bootstrap.min.css">
        <script src="path/to/jquery.min.js"></script>
        <script src="path/to/bootstrap.min.js"></script>


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
                var selectedValue = document.getElementById('filterDate').value;
                var tableRows = document.querySelectorAll('#attendanceTable' + accountId + ' tbody tr');
                var currentDate = new Date();

                tableRows.forEach(function(row) {
                    var dateCell = row.querySelector("td:nth-child(2)"); // Assuming date is in the 2nd column
                    var rowDate = new Date(dateCell.textContent.trim());

                    switch (selectedValue) {
                        case 'all':
                            row.style.display = 'table-row';
                            break;
                        case 'this_day':
                            if (isSameDay(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                        case 'this_week':
                            if (isSameWeek(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                        case 'this_month':
                            if (isSameMonth(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                        case 'this_year':
                            if (isSameYear(currentDate, rowDate)) {
                                row.style.display = 'table-row';
                            } else {
                                row.style.display = 'none';
                            }
                            break;
                    }
                });
            }

            function isSameDay(date1, date2) {
                return date1.toDateString() === date2.toDateString();
            }

            function isSameWeek(date1, date2) {
                var oneDay = 24 * 60 * 60 * 1000; // One day in milliseconds
                var diffDays = Math.round(Math.abs((date1 - date2) / oneDay));
                return diffDays <= 7;
            }

            function isSameMonth(date1, date2) {
                return date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
            }

            function isSameYear(date1, date2) {
                return date1.getFullYear() === date2.getFullYear();
            }
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


    </body>

    </html>