<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {




    $sqlLatestLogs = "SELECT al.*, acc.firstName AS adminFirstName, acc.lastName AS adminLastName
                  FROM activitylogs AS al
                  JOIN account AS acc ON al.accountId = acc.accountId
                  WHERE al.tab='Report' 
                  AND al.action LIKE 'Assigned maintenance personnel%'
                  ORDER BY al.date DESC 
                  LIMIT 5";

   // Fetch Report activity logs
$loggedInUserFirstName = $_SESSION['firstName']; // or the name field you have in session that you want to check against
$loggedInUsermiddleName = $_SESSION['middleName']; // assuming you also have the last name in the session
$loggedInUserLastName = $_SESSION['lastName']; //kung ano ung naka declare dito eto lang ung magiging data 

// Concatenate first name and last name for the action field check
$loggedInFullName = $loggedInUserFirstName . " " . $loggedInUsermiddleName . " " . $loggedInUserLastName; //kung ano ung naka declare dito eto lang ung magiging data 

// Adjust the SQL to check the 'action' field for the logged-in user's name
$sqlReport = "SELECT * FROM asset WHERE status = 'Need Repair' AND assignedName LIKE ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sqlReport);

// Create a wildcard search term for the name
$searchTerm = "%" . $loggedInFullName . "%";

// Bind the parameter and execute
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$PendingTask = $stmt->get_result();

 // Fetch Report activity logs
 $loggedInUserFirstName = $_SESSION['firstName']; // or the name field you have in session that you want to check against
 $loggedInUsermiddleName = $_SESSION['middleName']; // assuming you also have the last name in the session
 $loggedInUserLastName = $_SESSION['lastName']; //kung ano ung naka declare dito eto lang ung magiging data 
 
 // Concatenate first name and last name for the action field check
 $loggedInFullName = $loggedInUserFirstName . " " . $loggedInUsermiddleName . " " . $loggedInUserLastName; //kung ano ung naka declare dito eto lang ung magiging data 
 
 // Adjust the SQL to check the 'action' field for the logged-in user's name
 $sqlReport = "SELECT * FROM asset WHERE status = 'Working' AND assignedName LIKE ?";
 
 // Prepare the SQL statement
 $stmt = $conn->prepare($sqlReport);
 
 // Create a wildcard search term for the name
 $searchTerm = "%" . $loggedInFullName . "%";
 
 // Bind the parameter and execute
 $stmt->bind_param("s", $searchTerm);
 $stmt->execute();
 $CompletedTask = $stmt->get_result();

    $current_date = date('Y-m-d');
    $sql = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
        FROM attendancelogs AS a 
        INNER JOIN account AS acc ON a.accountId = acc.accountId 
        WHERE acc.userLevel = 3 AND a.date = '$current_date'";
    $result = $conn->query($sql) or die($conn->error);


    // Get the current date in the same format as your date column
    //Manager Attendance
    $current_date20 = date('Y-m-d');
    $sql20 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
        FROM attendancelogs AS a 
        INNER JOIN account AS acc ON a.accountId = acc.accountId 
        WHERE acc.userLevel = 2 AND a.date = '$current_date20'";
    $result20 = $conn->query($sql20) or die($conn->error);






?>
  <style>
            #map {
                display: none;
            }
        </style>
         <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard</title>
        <!-- BOOTSTRAP -->
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- jQuery UI library -->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/dashboard.css" />
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
                            <!-- <i class="bi bi-bell"></i> -->

                        </a>
                        <div class="dropdown-content" id="notification-dropdown-content">
                            <h6 class="dropdown-header">Alerts Center</h6>
                            <!-- PHP code to display notifications will go here -->
                            <?php
                            if ($resultLatestLogs && $resultLatestLogs->num_rows > 0) {
                                while ($row = $resultLatestLogs->fetch_assoc()) {
                                    $adminName = $row["adminFirstName"] . ' ' . $row["adminLastName"]; // Get the admin's full name

                                    // Parse the action text to extract personnel name and asset ID
                                    // Assuming the action text is something like "Assigned maintenance personnel John Doe to asset ID 20."
                                    $actionText = $row["action"];
                                    if (preg_match('/Assigned maintenance personnel (.*?) to asset ID (\d+)/', $actionText, $matches)) {
                                        $assignedName = $matches[1]; // Captured assigned personnel name
                                        $assetId = $matches[2]; // Captured asset ID
                                    } else {
                                        // If the pattern does not match, default to 'unknown'
                                        $assignedName = 'unknown';
                                        $assetId = 'unknown';
                                    }

                                    // Now create the notification text
                                    $notificationText = "Admin  
                                         assigned You to  
                                         to asset ID " . htmlspecialchars($assetId);
                                    echo '<a href="#">' . $notificationText . '</a>';
                                }
                            } else {
                                echo '<a href="#">No new notifications</a>';
                            }
                            ?>
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
            <li class="active">
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
                    <!-- <div class="cont-header">
                        <h1 class="tab-name-only">Dashboard</h1>
                    </div> -->
                </header>

                <div class="content-container">
                    <section class="content1">
                        <main class="present-buttons-container">
                            <div class="present-total-container">
                                <section class="present-total-section">
                                    <h5>
                                        Overall Task
                                    </h5>
                                    <?php
                                     // After executing the query and fetching the result
                                     $totalRows = $PendingTask->num_rows + $CompletedTask->num_rows; // This will hold the total number of rows returned by the query
                                     // Now, display the total number of rows
                                     echo "<p class='total-p'>" . $totalRows . "</p>"; // Display the total number of rows
                                    ?>
                                    <section>
                            </div>
                            <div class="present-main-per">
                                <div class="button-1">

                                    <!--Para sa presents today ng personnel-->
                                    <?php ($result->num_rows > 0) ?>

                                    <div class="present-container">
                                        <section class="present-image">
                                            <img src="../../src/img/Vector.png" class="icon-present" />
                                        </section>

                                        <section class="present-numbers">
                                            <p class="first-p">
                                                <?php 
                                                $PendingTaskList = $PendingTask->num_rows; // This will hold the total number of rows returned by the query
                                                echo $PendingTaskList; ?>
                                            </p> <!-- Dynamically display the count -->
                                            <p class="second-p">
                                                <?php
                                                if ($PendingTask->num_rows === 0) {
                                                    echo 'Pending Task'; // Case for 0 attendees
                                                } elseif ($PendingTask->num_rows === 1) {
                                                    echo 'Pending Task'; // Case for 1 attendee
                                                } else {
                                                    echo 'Pending Tasks'; // Case for multiple attendees
                                                }
                                                ?>
                                            </p> <!-- Adjust text based on count -->
                                        </section>

                                    </div>

                                </div>
                                <!--End of div for button-1-->
                               


                                <div class="button-2">

                                    <!--Para sa presents today ng manager-->
                                    <?php ($result20->num_rows > 0)  ?>

                                    <div class="present-container-2">
                                        <section class="present-image">
                                            <img src="../../src/img/Vector.png" class="icon-present" />
                                        </section>

                                        <section class="present-numbers">
                                            <p class="first-p">
                                                <?php 
                                                $CompletedTaskList = $CompletedTask->num_rows;
                                                echo $CompletedTaskList; ?>
                                            </p> <!-- Dynamically display the count -->
                                            <p class="second-p">
                                                <?php
                                                if ($result20->num_rows === 0) {
                                                    echo 'Completed Task'; // Case for 0 attendees
                                                } elseif ($result20->num_rows === 1) {
                                                    echo 'Completed Task'; // Case for 1 attendee
                                                } else {
                                                    echo 'Completed Tasks'; // Case for multiple attendees
                                                }
                                                ?>
                                            </p> <!-- Adjust text based on count -->
                                        </section>

                                    </div>

                                </div>
                                <!--End of div for button-2-->

                            
                            </div>
                            <!-- Personnel Attendance Modal -->
                            <div class="modal-parent">
                                <div class="modal modal-xl fade" id="Modal-Personnel" data-bs-backdrop="static" tabindex="-1" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="header">
                                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                            </div>
                                            <div class="modal-body modal-new-class">
                                                <!-- Modal Body-->
                                                <main>
                                                    <h5>Personnel Present Today:</h5>

                                                    <section class="list-names">
                                                        <ul class="ul-list">
                                                            <?php while ($row = $result->fetch_assoc()) : ?>
                                                                <li>
                                                                    <?php echo htmlspecialchars($row['fullName']); ?>
                                                                </li>
                                                            <?php endwhile; ?>
                                                        </ul>
                                                    </section>
                                                </main>

                                            </div>
                                            <div class="footer">
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Personnel Attendance Modal -->



                            <!-- Manager Attendance Modal -->
                            <div class="modal-parent">
                                <div class="modal modal-xl fade" id="Modal-Manager" data-bs-backdrop="static" tabindex="-1" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="header">
                                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                            </div>
                                            <div class="modal-body modal-new-class">
                                                <!-- Modal Body-->
                                                <main>
                                                    <h5>Manager Present Today:</h5>

                                                    <section class="list-names">
                                                        <ul class="ul-list">
                                                            <?php while ($row20 = $result20->fetch_assoc()) : ?>
                                                                <li>
                                                                    <?php echo htmlspecialchars($row20['fullName']); ?>
                                                                </li>
                                                            <?php endwhile; ?>
                                                        </ul>
                                                    </section>
                                                </main>

                                            </div>
                                            <div class="footer">
                                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">Close</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Manager Attendance Modal -->
                        </main>
                        <!--End of main for the two button container-->

                        <main id="calendar">
                            <div id="calendar-header">
                                <span class="calendar-header-span" id="current-date"></span>
                            </div>

                            <div id="calendar-body">
                                <div class="personnel-building">
                                    <!--Buildings Assigned Here-->
                                  
                                </div>
                              
                            </div>

                        

        </main>
        </section>

      
        <script src="../../src/js/locationTracker.js"></script>
      
       
      


        <!--End of Section content1-->


        <section class="content2">
            <div class="calendar-container">
                <div class="calendar">

                    <div class="month-indicator">

                        <span class="today-btn">Today</span>

                        <input type="text" id="datepicker" style="display: none;">
                        <span class="month clickMe span-label">January</span>
                        <span class="year clickMe span-label-2">2024</span>
                    </div>

                    <div class="calendar-ulit">
                        <div class="day-of-week">
                            <div>SUN</div>
                            <div>MON</div>
                            <div>TUE</div>
                            <div>WED</div>
                            <div>THU</div>
                            <div>FRI</div>
                            <div>SAT</div>
                        </div>

                        <div class="date-grid">
                            <!-- Dynamically generated dates will go here -->
                        </div>
                    </div>

                </div>

            </div>
            <!--End of div for calendar-container-->
            <!-- Building Filter and Chart Container -->
            <div class="doughnut-chart-container">
                <div class="statistics">
                    <h5>Select a Building</h5>
                </div>
                <div class="filter-container">
                    <select id="filter-select" onchange="updateChart()">
                        <option value="">Choose a Building</option>
                        <?php
                        $buildingQuery = "SELECT DISTINCT building FROM asset";
                        $buildings = $conn->query($buildingQuery);
                        while ($building = $buildings->fetch_assoc()) {
                            echo "<option value='" . $building['building'] . "'>" . $building['building'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div id="chart-container">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
            <!--End of div for dougnut-chart-container-->

        </section>
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

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Status to color mapping
            var statusColors = {
                'Working': 'green',
                'Under Maintenance': 'yellow',
                'For Replacement': 'orange',
                'Need to Repair': 'red'
            };

            // Initialize the chart with a gray segment
            var ctx = document.getElementById('doughnutChart').getContext('2d');
            var doughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Select a Building"],
                    datasets: [{
                        data: [1], // A dummy value to show the gray segment
                        backgroundColor: ['gray']
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Building Status Chart'
                    }
                }
            });

            // Update the chart when a new building is selected
            function updateChart() {
                var selectedBuilding = document.getElementById('filter-select').value;
                if (selectedBuilding === "") {
                    // Reset the chart to its initial gray state
                    doughnutChart.data.labels = ["Select a Building"];
                    doughnutChart.data.datasets[0].data = [1];
                    doughnutChart.data.datasets[0].backgroundColor = ['gray'];
                    doughnutChart.update();
                } else {
                    // Make an AJAX request to fetch data for the selected building
                    $.ajax({
                        url: 'get_building_data.php',
                        type: 'GET',
                        data: {
                            buildingName: selectedBuilding
                        },
                        success: function(response) {
                            // Map the response to the chart data and colors
                            var newLabels = Object.keys(response);
                            var newValues = Object.values(response);
                            var backgroundColors = newLabels.map(status => statusColors[status] || 'red');

                            // Update the chart with the new data and colors
                            doughnutChart.data.labels = newLabels;
                            doughnutChart.data.datasets[0].data = newValues;
                            doughnutChart.data.datasets[0].backgroundColor = backgroundColors;
                            doughnutChart.update();
                        },
                        error: function(xhr, status, error) {
                            console.error("An error occurred: " + xhr.status + " " + error);
                        }
                    });
                }
            }

            // Trigger the chart update function when the page loads
            $(document).ready(function() {
                updateChart(); // This will set the chart to the initial gray state if no building is selected
            });
        </script>



        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <script src="../../src/js/main.js"></script>
        <script src="../../src/js/dashboard-personnel.js"></script>
        <script src="../../src/js/profileModalController.js"></script>


    </body>

    </html>