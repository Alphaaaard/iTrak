<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {
    // Fetch General activity logs
    //!UNCOMMENT LATER
    //     $sqlGeneral = "SELECT ac.*, a.firstName, a.middleName, a.lastName
    // FROM activitylogs AS ac
    // LEFT JOIN account AS a ON ac.accountID = a.accountID
    // WHERE ac.tab='General'
    // ORDER BY ac.date DESC";
    //     $resultGeneral = $conn->query($sqlGeneral) or die($conn->error);

    //     // Fetch Report activity logs
    //     $sqlReport = "SELECT ac.*, a.firstName, a.middleName, a.lastName
    // FROM activitylogs AS ac
    // LEFT JOIN account AS a ON ac.accountID = a.accountID
    // WHERE ac.tab='Report'
    // ORDER BY ac.date DESC";
    //     $resultReport = $conn->query($sqlReport) or die($conn->error);

    //     // $sql2 = "SELECT ac.*, a.firstName, a.middleName, a.lastName 
    //     // FROM activitylogs AS ac
    //     // LEFT JOIN account AS a ON ac.accountID = a.accountID";
    //     // $result = $conn->query($sql2) or die($conn->error);






    //     $sql2 = "SELECT * FROM reportlogs";
    //     $result2 = $conn->query($sql2) or die($conn->error);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link rel="stylesheet" href="../../src/css/main.css" />
    <link rel="stylesheet" href="../../src/css/activity-logs.css" />
</head>

<body>
    <!-- NAVBAR -->
    <div id="navbar" class="">
        <nav>
            <div class="hamburger">
                <i class="bi bi-list"></i>
                <a href="#" class="brand" title="logo">
                    <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i>
                </a>
            </div>
            <div class="content-nav">
                <div class="notification-dropdown">
                    <a href="#" class="notification" id="notification-button">
                        <i class="bi bi-bell"></i>
                        <!-- <i class="bx bxs-bell"></i> -->
                        <span class="num">8</span>
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
                <a href="#" class="settings profile" title="settings">
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
                </a>
                <div id="settings-dropdown" class="dropdown-content1" style="position: absolute; top: 58px; right: 10px">
                    <a class="profile-name">Hello, <?php echo $_SESSION['firstName']; ?>!</a>
                    <hr>
                    <a class="profile-hover" href="#" data-bs-toggle="modal" data-bs-target="#viewModal"><i class="bi bi-person profile-icons"></i>Profile</a>
                    <a class="profile-hover" href="#" id="logoutBtn"><i class="bi bi-box-arrow-left "></i>Logout</a>
                </div>
                <?php
                //! UNCOMMENT
                // } else {
                //     header("Location:../../index.php");
                //     exit();
                // }
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
                    <img src="../../src/icons/grid.svg" alt="" class="icons">
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="./attendance-logs.php">
                    <img src="../../src/icons/Calendar.svg" alt="" class="icons">
                    <span class="text">Attendance Logs</span>
                </a>
            </li>
            <li>
                <a href="./gps.php">
                    <img src="../../src/icons/Location.svg" alt="" class="icons">
                    <span class="text">GPS</span>
                </a>
            </li>
            <li>
                <a href="./map.php">
                    <img src="../../src/icons/Logs.svg" alt="" class="icons">
                    <span class="text">Map</span>
                </a>
            </li>
            <li>
                <a href="./reports.php">
                    <img src="../../src/icons/Clipboard.svg" alt="" class="icons">
                    <span class="text">Reports</span>
                </a>
            </li>
            <li class="active">
                <a href="./activity-logs.php">
                    <img src="../../src/icons/History.svg" alt="" class="icons">
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
                        <li><a href="#" class="nav-link active" data-bs-target="pills-general">General History</a></li>
                        <li><a href="#" class="nav-link" data-bs-target="pills-report">Report History</a></li>
                    </ul>
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
                                while ($row = $resultGeneral->fetch_assoc()) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
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
                                while ($row = $resultReport->fetch_assoc()) {
                                    echo "<div class='table-container'>";
                                    echo "<table>";
                                    echo '<tr>';
                                    echo '<td>' . $row['firstName'] . "" . $row['lastName'] . '</td>';
                                    echo '<td style="display:none">' . $row['activityId'] . '</td>';
                                    echo '<td style="display:none">' . $row['firstName'] . '</td>';
                                    echo '<td style="display:none">' . $row['middleName'] . '</td>';
                                    echo '<td style="display:none">' . $row['lastName'] . '</td>';
                                    echo '<td>' . $row['date'] . '</td>';
                                    echo '<td>' . $row['action'] . '</td>';
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
                    <!--EMPLOYEE INFORMATION MODALS-->

                </div>
            </div>
        </main>
        <!-- MAIN -->
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
        function filterDate(order) {
            var rows = Array.from(document.querySelectorAll('.table-container table tr'));

            // Sort rows based on the date
            var sortedRows = rows.sort(function(a, b) {
                var dateA = new Date(a.cells[1].textContent); // Adjust according to your date column
                var dateB = new Date(b.cells[1].textContent);

                return (order === 'newest') ? dateB - dateA : dateA - dateB;
            });

            // Re-append rows to the table in sorted order
            var tableBody = document.querySelector('.table-container table tbody');
            sortedRows.forEach(row => {
                tableBody.appendChild(row);
            });
        }
    </script>
</body>

</html>