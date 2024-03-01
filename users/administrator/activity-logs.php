<?php
session_start();
include_once("../../config/connection.php");
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
 
 $loggedInFullName = $loggedInUserFirstName . ' '.$loggedInUserMiddleName .' '. $loggedInUserLastName;


 
 // Adjust the SQL to fetch only the notifications for the logged-in user
// Old code with specific name condition
// $searchTerm = "%Assigned maintenance personnel " . $loggedInFullName . "%";

// New SQL query without the specific name condition
$sqlLatestLogs = "SELECT al.*, acc.firstName AS adminFirstName, acc.middleName AS adminMiddleName, acc.lastName AS adminLastName
               FROM activitylogs AS al
               JOIN account AS acc ON al.accountID = acc.accountID
               WHERE al.tab='Report' 
               AND al.seen = '0'
               ORDER BY al.date DESC 
               LIMIT 1000";

// Prepare and execute the new SQL statement
$stmtLatestLogs = $conn->prepare($sqlLatestLogs);
$stmtLatestLogs->execute();
$resultLatestLogs = $stmtLatestLogs->get_result();


$unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE seen = '3'";
$result = $conn->query($unseenCountQuery);
$unseenCountRow = $result->fetch_assoc();
$unseenCount = $unseenCountRow['unseenCount'];




    
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
    </head>

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
$assignedName = "default value or empty string"; // Set a default value

// ... your existing code ...

// Inside the if statement where you expect $assignedName to be set
if (preg_match('/Assigned maintenance personnel (.*?) to asset ID (\d+)/', $actionText, $matches)) {
    $assignedName = $matches[1];
    $assetId = $matches[2];
}

// ... rest of your code ...


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
                    <a href="./staff.php">
                        <i class="bi bi-person"></i>
                        <span class="text">Staff</span>
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
                                    echo "<div class='table-container'>";
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
            // Initial setup for pagination on page load for both tabs
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
            }
        });
    </script>


    </html>