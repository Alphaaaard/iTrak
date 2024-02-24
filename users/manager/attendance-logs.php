<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {

    if ($conn->connect_error) {
        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
    }

    $filterRole = isset($_GET['filterRole']) ? $_GET['filterRole'] : 'all';

    $query = "SELECT accountId, picture, firstname, lastname, role FROM account";
    $conditions = [];
    $params = [];
    $types = '';

    if ($filterRole !== 'all') {
        $conditions[] = "role = ?";
        $params[] = $filterRole;
        $types .= 's';
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $stmt = $conn->prepare($query);

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
        <title>Attendance Logs</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
                            <i class="bi bi-bell"></i>
                            <!-- <i class="bx bxs-bell"></i> -->
                            <span class="num"></span>
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
                    <header>
                        <div class="cont-header">
                            <h1 class="tab-name">Attendance</h1>
                            <div class="form-wrapper">
                                <div class="tbl-filter">
                                    <!-- Wrap the form in a div with the class 'form-container' -->
                                    <form action="attendance-logs.php" method="get">
                                        <select name="filterRole" id="filterRole" onchange="this.form.submit()">
                                            <option value="all">All Roles</option>
                                            <option value="Maintenance Manager" <?php echo (isset($_GET['filterRole']) && $_GET['filterRole'] === "Maintenance Manager") ? 'selected' : ''; ?>>Manager</option>
                                            <option value="Maintenance Personnel" <?php echo (isset($_GET['filterRole']) && $_GET['filterRole'] === "Maintenance Personnel") ? 'selected' : ''; ?>>Personnel</option>
                                        </select>
                                    </form>
                                    <form class="d-flex" role="search">
                                        <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" onkeyup="searchTable()" />
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
                                    <table>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>NAME</th>
                                            <th>ROLE</th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    while ($row = $result->fetch_assoc()) {
                                        // Output account information in each row
                                        echo '<tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#attendanceModal' . $row['accountId'] . '" data-account-id="' . $row['accountId'] . '">';
                                        echo '<td>' . $row['accountId'] . '</td>';
                                        echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row['picture']) . '" class="rounded-img" alt="Profile Image" style="width: 50px; height: 50px; "></td>';
                                        echo '<td>' . $row['firstname'] . ' ' . $row['lastname'] . '</td>';
                                        echo '<td style="display:none">' . $row['firstname'] . '</td>';
                                        echo '<td style="display:none">' . $row['lastname'] . '</td>';
                                        echo '<td>' . $row['role'] . '</td>';
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
                        <!-- Modal -->
                        <?php
                        // Fetch and display attendance log data within modals
                        if ($result->num_rows > 0) {
                            $result->data_seek(0); // Reset result pointer to the beginning

                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="modal fade" id="attendanceModal' . $row['accountId'] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                                echo '<div class="modal-dialog modal-lg modal-dialog-centered">';
                                echo '<div class="modal-content">';
                                echo '<div class="modal-footer">';
                                echo '<div class="modal-close">';
                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                echo '</div>';
                                echo '<div class="modal-content-header">';
                                echo '<p class="h5-like">' . $row['firstname'] . ' ' . $row['lastname'] . '</p>';
                                echo '<form class="filterType">';
                                echo '<select name="filterType" id="filterType' . $row['accountId'] . '" onchange="filterAttendanceData(' . $row['accountId'] . ')">';
                                echo '<option value="all">All Days</option>';
                                echo '<option value="week">This Week</option>';
                                echo '<option value="month">This Month</option>';
                                echo '<option value="year">This Year</option>';
                                echo '</select>';
                                echo '</form>';
                                echo '</div>';

                                $attendanceQuery = "SELECT date, timeIn, timeOut FROM attendancelogs WHERE accountId = ? ORDER BY date ASC";
                                $attendanceStmt = $conn->prepare($attendanceQuery);
                                $attendanceStmt->bind_param('i', $row['accountId']);
                                $attendanceStmt->execute();
                                $attendanceResult = $attendanceStmt->get_result();

                                if ($attendanceResult->num_rows > 0) {
                                    echo '<table id="attendanceTable' . $row['accountId'] . '">';
                                    echo '<thead class="table-header">';
                                    echo '<tr>';
                                    echo '<th>Day</th>';
                                    echo '<th>Date</th>';
                                    echo '<th>Time In</th>';
                                    echo '<th>Time Out</th>';
                                    echo '<th>Total Hours</th>';
                                    echo '</tr>';
                                    echo '</thead>';
                                    echo '<tbody>';

                                    while ($attendanceRow = $attendanceResult->fetch_assoc()) {
                                        // Get the day of the week
                                        $dayOfWeek = date('l', strtotime($attendanceRow['date']));

                                        // Format timeIn and timeOut to show only the time with AM or PM
                                        $timeInFormatted = date('h:i A', strtotime($attendanceRow['timeIn']));

                                        if ($attendanceRow['timeOut'] !== null) {
                                            $timeOutFormatted = date('h:i A', strtotime($attendanceRow['timeOut']));
                                        } else {
                                            $timeOutFormatted = ''; // Display an empty string for NULL TimeOut
                                        }

                                        if ($attendanceRow['timeOut'] !== null) {
                                            // Calculate total hours and minutes if "TimeOut" is not NULL
                                            $timeIn = strtotime($attendanceRow['timeIn']);
                                            $timeOut = strtotime($attendanceRow['timeOut']);
                                            $timeDifference = $timeOut - $timeIn;
                                            $hours = floor($timeDifference / 3600);

                                            // Convert the total hours and minutes to a single decimal value
                                            $totalHoursFormatted = $hours;
                                        } else {
                                            $totalHoursFormatted = ''; // Display an empty string for NULL TimeOut
                                        }

                                        echo '<tr data-day="' . $dayOfWeek . '">';
                                        echo '<td>' . $dayOfWeek . '</td>';
                                        echo '<td>' . $attendanceRow['date'] . '</td>';
                                        echo '<td>' . $timeInFormatted . '</td>';
                                        echo '<td>' . $timeOutFormatted . '</td>';
                                        echo '<td>' . $totalHoursFormatted . '</td>';
                                        echo '</tr>';
                                    }

                                    echo '</tbody>';

                                    // Add a tfoot element for the footer row
                                    echo '<tfoot>';
                                    echo '<tr>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '</tr>';
                                    echo '</tfoot>';

                                    echo '</table>';
                                } else {
                                    echo '<table>';
                                    echo "<div class='noDataImgH'>";
                                    echo '<img src="../../src/img/emptyTable.jpg" alt="No data available" class="noDataImg"/>';
                                    echo "</div>";
                                    echo '</table>';
                                }

                                // Close the attendance log statement
                                $attendanceStmt->close();

                                echo '<button type="button" class="btn btn-primary export-btn" onclick="exportTableToCSV(' . "'" . $row['firstname'] . "_" . $row['lastname'] . ".csv'" . ')">Export PDF</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                        <!-- Modal -->
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
        <!-- <link rel="stylesheet" href="path/to/bootstrap.min.css"> -->
        <!-- <script src="path/to/jquery.min.js"></script> -->
        <!-- <script src="path/to/bootstrap.min.js"></script> -->
        <script>
            function downloadCSV(csv, filename) {
                var csvFile;
                var downloadLink;

                // CSV file
                csvFile = new Blob([csv], {
                    type: "text/csv"
                });

                // Download link
                downloadLink = document.createElement("a");

                // File name
                downloadLink.download = filename;

                // Create a link to the file
                downloadLink.href = window.URL.createObjectURL(csvFile);

                // Hide download link
                downloadLink.style.display = "none";

                // Add the link to DOM
                document.body.appendChild(downloadLink);

                // Click download link
                downloadLink.click();
            }

            function exportTableToCSV(filename, personName) {
                var csv = [];
                var rows = document.querySelectorAll("table tr");

                for (var i = 0; i < rows.length; i++) {
                    // Check if the row contains the person's name
                    if (rows[i].innerText.includes(personName)) {
                        var row = [],
                            cols = rows[i].querySelectorAll("td, th");

                        for (var j = 0; j < cols.length; j++)
                            row.push('"' + cols[j].innerText + '"');

                        csv.push(row.join(","));
                        break; // Stop the loop after finding the right person
                    }
                }

                // Download CSV file if the person's data was found
                if (csv.length > 0) {
                    downloadCSV(csv.join("\n"), filename);
                } else {
                    alert('No data found for ' + personName);
                }
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