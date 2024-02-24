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


    //TECHVOC SHOW DATA ONLY
    $current_date3 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql3 = "SELECT * FROM scheduleboard WHERE date = ? AND techVoc <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql3);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date3);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result3 = $stmt->get_result();


    //OLD ACAD SHOW DATA ONLY
    $current_date4 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql4 = "SELECT * FROM scheduleboard WHERE date = ? AND oldAcad <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql4);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date4);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result4 = $stmt->get_result();


    //BELMONTE SHOW DATA ONLY
    $current_date5 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql5 = "SELECT * FROM scheduleboard WHERE date = ? AND belmonte <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql5);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date5);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result5 = $stmt->get_result();


    //METALCASTINGS SHOW DATA ONLY
    $current_date6 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql6 = "SELECT * FROM scheduleboard WHERE date = ? AND metalcasting <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql6);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date6);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result6 = $stmt->get_result();


    //KORPHIL SHOW DATA ONLY
    $current_date7 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql7 = "SELECT * FROM scheduleboard WHERE date = ? AND korphil <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql7);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date7);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result7 = $stmt->get_result();


    //MULTIPURPOSE SHOW DATA ONLY
    $current_date8 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql8 = "SELECT * FROM scheduleboard WHERE date = ? AND multipurpose <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql8);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date8);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result8 = $stmt->get_result();


    //CHINESE A SHOW DATA ONLY
    $current_date9 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql9 = "SELECT * FROM scheduleboard WHERE date = ? AND chineseA <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql9);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date9);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result9 = $stmt->get_result();


    //CHINESE B SHOW DATA ONLY
    $current_date10 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql10 = "SELECT * FROM scheduleboard WHERE date = ? AND chineseB <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql10);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date10);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result10 = $stmt->get_result();


    //URBAN FARMING SHOW DATA ONLY
    $current_date11 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql11 = "SELECT * FROM scheduleboard WHERE date = ? AND urbanFarming <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql11);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date11);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result11 = $stmt->get_result();


    //ADMINISTRATION SHOW DATA ONLY
    $current_date12 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql12 = "SELECT * FROM scheduleboard WHERE date = ? AND administration <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql12);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date12);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result12 = $stmt->get_result();


    //ADMINISTRATION SHOW DATA ONLY
    $current_date13 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql13 = "SELECT * FROM scheduleboard WHERE date = ? AND bautista <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql13);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date13);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result13 = $stmt->get_result();


    //ADMINISTRATION SHOW DATA ONLY
    $current_date14 = date('Y-m-d');
    // Use a prepared statement to avoid SQL injection
    $sql14 = "SELECT * FROM scheduleboard WHERE date = ? AND newAcad <> ''";
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql14);
    // Bind the current date parameter
    $stmt->bind_param("s", $current_date14);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result14 = $stmt->get_result();



    //Add Employee
    //     if (isset($_POST['save'])) {
    //         $sbId = $_POST['sbId'];
    //         $date = $_POST['date'];
    //         $techVoc = $_POST['techVoc'];
    //         $oldAcad = $_POST['oldAcad'];
    //         $belmonte = $_POST['belmonte'];
    //         $metalcasting = $_POST['metalcasting'];
    //         $korphil = $_POST['korphil'];
    //         $multipurpose = $_POST['multipurpose'];
    //         $chineseA = $_POST['chineseA'];
    //         $chineseB = $_POST['chineseB'];
    //         $urbanFarming = $_POST['urbanFarming'];
    //         $administration = $_POST['administration'];
    //         $bautista = $_POST['bautista'];
    //         $newAcad = $_POST['newAcad'];

    //         $sql1 = "INSERT INTO `scheduleboard`(`sbId`, `date`, `techVoc`, `oldAcad`, `belmonte`, `metalcasting`, `korphil`, `multipurpose`, `chineseA`, `chineseB`, `urbanFarming`, `administration`, `bautista`, `newAcad`)
    //   VALUES ('$sbId', '$date', '$techVoc', '$oldAcad', '$belmonte', '$metalcasting', '$korphil', '$multipurpose', '$chineseA', '$chineseB', '$urbanFarming', '$administration' , '$bautista' , '$newAcad')
    //   ";


    //         if ($conn->query($sql1) === TRUE) {
    //             // Query executed successfully
    //             header("Location: dashboard.php");
    //             exit;
    //         } else {
    //             // Query execution failed
    //             echo "Error: " . $sql1 . "<br>" . $conn->error;
    //         }
    //     }
    //     // SQL query to fetch data from the asset table
    //     $sql100 = "SELECT status, COUNT(*) as count FROM asset GROUP BY status";
    //     $result50 = $conn->query($sql100);

    //     // Fetching data and preparing for the chart
    //     $data = [];
    //     while ($row50 = $result50->fetch_assoc()) {
    //         $data[$row50['status']] = $row50['count'];
    //     }


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
                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Tech-Voc
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal3" id="techVoc" name="techVoc" onclick="setBuilding('techvoc')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Yellow
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal4" id="oldAcad" name="oldAcad" onclick="setBuilding('oldacad')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Belmonte
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal5" id="belmonte" name="belmonte" onclick="setBuilding('belmonte')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Metal Casting
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal6" id="metalcasting" name="metalcasting" onclick="setBuilding('metalcasting')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            KorPhil
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal7" id="korphil" name="korphil" onclick="setBuilding('korphil')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Multipurpose
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal8" id="multipurpose" name="multipurpose" onclick="setBuilding('multipurpose')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Bautista
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal13" id="bautista" name="bautista" onclick="setBuilding('bautista')">

                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            New Academic
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal14" id="newAcad" name="newAcad" onclick="setBuilding('newacad')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Administration
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal12" id="administration" name="administration" onclick="setBuilding('administration')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Urban Farming
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal11" id="urbanFarming" name="urbanFarming" onclick="setBuilding('urbanfarming')">

                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Chinese A
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal9" id="chineseA" name="chineseA" onclick="setBuilding('chineseA')">


                                    </div>
                                </div>

                                <div class="building">
                                    <div class="mini-header">
                                        <div class="mini-header-border">
                                            Chinese B
                                        </div>
                                    </div>
                                    <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal10" id="chineseB" name="chineseB" onclick="setBuilding('chineseB')">


                                    </div>
                                </div>

                                <div class="pagination-container">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Previous" onclick="showBuildings(-1)">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <li class="page-item"><a class="page-link" href="#" onclick="showBuildings(1)">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#" onclick="showBuildings(2)">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#" onclick="showBuildings(3)">3</a></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next" onclick="showBuildings(2)">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- 
                            Change the position of the form tag in all building modals;
                            Commented old confirmation modals and changed into sweetalert modals.
                         -->
                            <!--Modal for Tech-Voc-->
                            <div class="modal-parent ">
                                <div class="modal modal-xl fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="header-modal">
                                                <h5>Tech-Voc Building</h5>
                                                <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                                            </div>

                                            <div class="modal-body modal-new-class">
                                                <form method="post" id="techVocForm">
                                                    <section class="choose-personnel">
                                                        <!-- <form method="post" class="row g-3"> -->
                                                        <div class="col-4" style="display: none">
                                                            <label for="date" class="form-label">Date:</label>
                                                            <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                        </div>

                                                        <div class="col-12 choosy-new">

                                                            <label for="techVoc col-8" class="form-label">
                                                                Choose a maintenance personnel:
                                                            </label>

                                                            <select class="form-control col-4 select-new" id="techVoc" name="techVoc">
                                                                <option value="">Choose</option>
                                                                <?php
                                                                // Execute your SQL query
                                                                $new_date3 = date('Y-m-d');
                                                                $new_sql3 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                                FROM attendancelogs AS a 
                                                                INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                                WHERE acc.userLevel = 3 AND a.date = '$new_date3'";

                                                                $new_result3 = $conn->query($new_sql3) or die($conn->error);

                                                                // Check if there are any results
                                                                if ($new_result3->num_rows > 0) {
                                                                    // Iterate through the results and create an option for each
                                                                    while ($new_row3 = $new_result3->fetch_assoc()) {
                                                                        //If need ID remove tong comment
                                                                        //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                        echo '<option value="' . htmlspecialchars($new_row3['fullName']) . '">' . htmlspecialchars($new_row3['fullName']) . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </section>

                                                    <section class="table-personnel">
                                                        <div>
                                                            <label for="techVoc" class="form-label">
                                                                Assigned maintenance personnel:
                                                            </label>

                                                            <?php
                                                            if ($result3->num_rows > 0) {
                                                                echo "<div class='table-container-new'>";
                                                                echo "<table class='new-table'>";
                                                                echo "<tbody class='table-body'>"; // Start tbody

                                                                while ($row3 = $result3->fetch_assoc()) {
                                                                    echo '<tr>';
                                                                    // Button comes first
                                                                    echo '<td>';

                                                                    echo '
                                                                <button type="button" class="new-delete-btn" data-sbid="' . $row3['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row3['sbId'] . '</td><td style="display: none;">' . $row3['date'] . '</td><td>' . $row3['techVoc'] . '</td><td style="display: none;">' . $row3['oldAcad'] . '</td><td style="display: none;">' . $row3['belmonte'] . '</td><td style="display: none;">' . $row3['metalcasting'] . '</td><td style="display: none;">' . $row3['korphil'] . '</td><td style="display: none;">' . $row3['multipurpose'] . '</td><td style="display: none;">' . $row3['chineseA'] . '</td><td style="display: none;">' . $row3['chineseB'] . '</td><td style="display: none;">' . $row3['urbanFarming'] . '</td><td style="display: none;">' . $row3['administration'] . '</td><td style="display: none;">' . $row3['bautista'] . '</td><td style="display: none;">' . $row3['newAcad'] . '</td></tr>') . '">
                                                                    <i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i>
                                                                </button>';

                                                                    echo '</td>';
                                                                    // Then the hidden sbId field
                                                                    echo '<td style="display: none;">' . $row3['sbId'] . '</td>';
                                                                    // Followed by the techVoc field
                                                                    echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; margin-left: 1em; font-weight: 500 !important;">' . $row3['techVoc'] . '</td>';
                                                                    // Hidden input field
                                                                    echo '<td>';
                                                                    echo '<input type="hidden" name="report_id" value="' . $row3['sbId'] . '">';
                                                                    echo '</td>';
                                                                    echo '</tr>';
                                                                }

                                                                echo "</tbody>"; // End tbody
                                                                echo "</table>";
                                                                echo "</div>";
                                                            } else {
                                                                echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </section>
                                                </form>
                                            </div>

                                            <div class="footer">
                                                <!-- <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#save3"> -->
                                                <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End of modal-->

                            <!--MODAL for Save-->
                            <!-- <div class="modal fade" id="save3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-footer">
                                            Are you sure you want to save this changes?
                                            <div class="modal-popups">
                                                <button type="button" class="btn close-popups" data-bs-toggle="modal" data-bs-target="#exampleModal3">No</button>
                                                <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <!--END OF TECHVOC-->


                            <!--Modal for Old Acad-->
                            <div class="modal-parent">
                                <div class="modal modal-xl fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="header-modal">
                                                <h5>Yellow Building</h5>
                                                <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                                            </div>

                                            <div class="modal-body modal-new-class">
                                                <form method="post" id="oldacadForm">
                                                    <section class="choose-personnel">


                                                        <div class="col-4" style="display: none">
                                                            <label for="date" class="form-label">Date:</label>
                                                            <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                        </div>


                                                        <div class="col-12 choosy-new">
                                                            <label for="techVoc col-8" class="form-label">Choose a
                                                                maintenance personnel:</label>
                                                            <select class="form-control col-4 select-new" id="oldAcad" name="oldAcad">
                                                                <option value="">Choose</option>
                                                                <?php
                                                                // Execute your SQL query
                                                                $new_date4 = date('Y-m-d');
                                                                $new_sql4 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                    FROM attendancelogs AS a 
                                                    INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                    WHERE acc.userLevel = 3 AND a.date = '$new_date4'";
                                                                $new_result4 = $conn->query($new_sql4) or die($conn->error);

                                                                // Check if there are any results
                                                                if ($new_result4->num_rows > 0) {
                                                                    // Iterate through the results and create an option for each
                                                                    while ($new_row4 = $new_result4->fetch_assoc()) {
                                                                        //If need ID remove tong comment
                                                                        //echo '<option value="'.htmlspecialchars($row4['accountId']).'">'.htmlspecialchars($row4['fullName']).'</option>';
                                                                        echo '<option value="' . htmlspecialchars($new_row4['fullName']) . '">' . htmlspecialchars($new_row4['fullName']) . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </section>

                                                    <section class="table-personnel">
                                                        <div>
                                                            <label for="oldAcad" class="form-label">Assigned maintenance personnel:</label>
                                                            <?php
                                                            if ($result4->num_rows > 0) {
                                                                echo "<div class='table-container-new'>";
                                                                echo "<table class='new-table'>";
                                                                echo "<tbody class='table-body'>"; // Start tbody
                                                                while ($row4 = $result4->fetch_assoc()) {
                                                                    echo '<tr>';
                                                                    // Button comes first
                                                                    echo '<td>';
                                                                    echo '<button type="button" class="new-delete-btn" data-sbid="' . $row4['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row4['sbId'] . '</td><td style="display: none;">' . $row4['date'] . '</td><td>' . $row4['techVoc'] . '</td><td style="display: none;">' . $row4['oldAcad'] . '</td><td style="display: none;">' . $row4['belmonte'] . '</td><td style="display: none;">' . $row4['metalcasting'] . '</td><td style="display: none;">' . $row4['korphil'] . '</td><td style="display: none;">' . $row4['multipurpose'] . '</td><td style="display: none;">' . $row4['chineseA'] . '</td><td style="display: none;">' . $row4['chineseB'] . '</td><td style="display: none;">' . $row4['urbanFarming'] . '</td><td style="display: none;">' . $row4['administration'] . '</td><td style="display: none;">' . $row4['bautista'] . '</td><td style="display: none;">' . $row4['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                                                    echo '</td>';
                                                                    // Then the hidden sbId field
                                                                    echo '<td style="display: none;">' . $row4['sbId'] . '</td>';
                                                                    // Followed by the techVoc field
                                                                    echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row4['oldAcad'] . '</td>';
                                                                    // Hidden input field
                                                                    echo '<td>';
                                                                    echo '<input type="hidden" name="report_id" value="' . $row4['sbId'] . '">';
                                                                    echo '</td>';
                                                                    echo '</tr>';
                                                                }
                                                                echo "</tbody>"; // End tbody
                                                                echo "</table>";
                                                                echo "</div>";
                                                            } else {
                                                                echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </section>
                                                </form>
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--MODAL for Save-->
                            <!-- <div class="modal fade" id="save4" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                            <!--Modal for belmonte-->
                            <div class="modal-parent">
                                <div class="modal modal-xl fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="header-modal">
                                                <h5>Belmonte Building</h5>
                                                <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                                            </div>

                                            <div class="modal-body modal-new-class">
                                                <form method="POST" id="belmonteForm">
                                                    <section class="choose-personnel">
                                                        <!-- <form method="post" class="row g-3"> -->

                                                        <div class="col-4" style="display: none">
                                                            <label for="date" class="form-label">Date:</label>
                                                            <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                        </div>

                                                        <form method="POST">
                                                            <div class="col-12 choosy-new">
                                                                <label for="techVoc col-8" class="form-label">Choose a
                                                                    maintenance personnel:</label>
                                                                <select class="form-control col-4 select-new" id="belmonte" name="belmonte">
                                                                    <option value="">Choose</option>
                                                                    <?php
                                                                    // Execute your SQL query
                                                                    $new_date5 = date('Y-m-d');
                                                                    $new_sql5 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date5'";
                                                                    $new_result5 = $conn->query($new_sql5) or die($conn->error);

                                                                    // Check if there are any results
                                                                    if ($new_result5->num_rows > 0) {
                                                                        // Iterate through the results and create an option for each
                                                                        while ($new_row5 = $new_result5->fetch_assoc()) {
                                                                            //If need ID remove tong comment
                                                                            //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                            echo '<option value="' . htmlspecialchars($new_row5['fullName']) . '">' . htmlspecialchars($new_row5['fullName']) . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </form>
                                                    </section>


                                                    <section class="table-personnel">
                                                        <div>
                                                            <label for="oldAcad" class="form-label">Assigned maintenance personnel:</label>

                                                            <?php
                                                            if ($result5->num_rows > 0) {
                                                                echo "<div class='table-container-new'>";
                                                                echo "<table class='new-table'>";
                                                                echo "<tbody class='table-body'>"; // Start tbody
                                                                while ($row5 = $result5->fetch_assoc()) {
                                                                    echo '<tr>';
                                                                    // Button comes first
                                                                    echo '<td>';
                                                                    echo '<button type="button" class="new-delete-btn" data-sbid="' . $row5['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row5['sbId'] . '</td><td style="display: none;">' . $row5['date'] . '</td><td>' . $row5['techVoc'] . '</td><td style="display: none;">' . $row5['oldAcad'] . '</td><td style="display: none;">' . $row5['belmonte'] . '</td><td style="display: none;">' . $row5['metalcasting'] . '</td><td style="display: none;">' . $row5['korphil'] . '</td><td style="display: none;">' . $row5['multipurpose'] . '</td><td style="display: none;">' . $row5['chineseA'] . '</td><td style="display: none;">' . $row5['chineseB'] . '</td><td style="display: none;">' . $row5['urbanFarming'] . '</td><td style="display: none;">' . $row5['administration'] . '</td><td style="display: none;">' . $row5['bautista'] . '</td><td style="display: none;">' . $row5['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                                                    echo '</td>';
                                                                    // Then the hidden sbId field
                                                                    echo '<td style="display: none;">' . $row5['sbId'] . '</td>';
                                                                    // Followed by the techVoc field
                                                                    echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row5['belmonte'] . '</td>';
                                                                    // Hidden input field
                                                                    echo '<td>';
                                                                    echo '<input type="hidden" name="report_id" value="' . $row5['sbId'] . '">';
                                                                    echo '</td>';
                                                                    echo '</tr>';
                                                                }
                                                                echo "</tbody>"; // End tbody
                                                                echo "</table>";
                                                                echo "</div>";
                                                            } else {
                                                                echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </section>
                                                </form>
                                            </div>

                                            <div class="footer">
                                                <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                                    Save
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--MODAL for Save-->
                            <!-- <div class="modal fade" id="save5" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


                            <!--Modal for metalcasting-->
                            <div class="modal-parent">
                                <div class="modal modal-xl fade" id="exampleModal6" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="header-modal">
                                                <h5>Metal Casting Building</h5>
                                                <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                                            </div>
                                            <div class="modal-body modal-new-class">
                                                <form method="post" id="metalcastingForm">
                                                    <section class="choose-personnel">
                                                        <!-- <form method="post" class="row g-3"> -->

                                                        <div class="col-4" style="display: none">
                                                            <label for="date" class="form-label">Date:</label>
                                                            <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                        </div>


                                                        <div class="col-12 choosy-new">
                                                            <label for="techVoc col-8" class="form-label">Choose a
                                                                maintenance personnel:</label>
                                                            <select class="form-control col-4 select-new" id="metalcasting" name="metalcasting">
                                                                <option value="">Choose</option>
                                                                <?php
                                                                // Execute your SQL query
                                                                $new_date6 = date('Y-m-d');
                                                                $new_sql6 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date6'";
                                                                $new_result6 = $conn->query($new_sql6) or die($conn->error);

                                                                // Check if there are any results
                                                                if ($new_result6->num_rows > 0) {
                                                                    // Iterate through the results and create an option for each
                                                                    while ($new_row6 = $new_result6->fetch_assoc()) {
                                                                        //If need ID remove tong comment
                                                                        //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                        echo '<option value="' . htmlspecialchars($new_row6['fullName']) . '">' . htmlspecialchars($new_row6['fullName']) . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                </form>
                    </section>

                    <section class="table-personnel">
                        <div>
                            <label for="metalcasting" class="form-label">Assigned maintenance personnel:</label>

                            <?php
                            if ($result6->num_rows > 0) {
                                echo "<div class='table-container-new'>";
                                echo "<table class='new-table'>";
                                echo "<tbody class='table-body'>"; // Start tbody
                                while ($row6 = $result6->fetch_assoc()) {
                                    echo '<tr>';
                                    // Button comes first
                                    echo '<td>';
                                    echo '<button type="button" class="new-delete-btn" data-sbid="' . $row6['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row6['sbId'] . '</td><td style="display: none;">' . $row6['date'] . '</td><td>' . $row6['techVoc'] . '</td><td style="display: none;">' . $row6['oldAcad'] . '</td><td style="display: none;">' . $row6['belmonte'] . '</td><td style="display: none;">' . $row6['metalcasting'] . '</td><td style="display: none;">' . $row6['korphil'] . '</td><td style="display: none;">' . $row6['multipurpose'] . '</td><td style="display: none;">' . $row6['chineseA'] . '</td><td style="display: none;">' . $row6['chineseB'] . '</td><td style="display: none;">' . $row6['urbanFarming'] . '</td><td style="display: none;">' . $row6['administration'] . '</td><td style="display: none;">' . $row6['bautista'] . '</td><td style="display: none;">' . $row6['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                    echo '</td>';
                                    // Then the hidden sbId field
                                    echo '<td style="display: none;">' . $row6['sbId'] . '</td>';
                                    // Followed by the techVoc field
                                    echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row6['metalcasting'] . '</td>';
                                    // Hidden input field
                                    echo '<td>';
                                    echo '<input type="hidden" name="report_id" value="' . $row6['sbId'] . '">';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                echo "</tbody>"; // End tbody
                                echo "</table>";
                                echo "</div>";
                            } else {
                                echo '<div class="no-data-message">No personnel assigned for today.</div>';
                            }
                            ?>
                        </div>
                    </section>
                    </form>
                </div>

                <div class="footer">
                    <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                        Save
                    </button>

                </div>
                </div>
                </div>
                </div>
                </div>

                <!--MODAL for Save-->
                <!-- <div class="modal fade" id="save6" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


                <!--Modal for korphil-->
                <div class="modal-parent">
                    <div class="modal modal-xl fade" id="exampleModal7" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="header-modal">
                                    <h5>KorPhil Building</h5>
                                    <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                                </div>
                                <div class="modal-body modal-new-class">
                                    <form method="post" id="korphilForm">
                                        <section class="choose-personnel">


                                            <div class="col-4" style="display: none">
                                                <label for="date" class="form-label">Date:</label>
                                                <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                            </div>


                                            <div class="col-12 choosy-new">
                                                <label for="techVoc col-8" class="form-label">Choose a
                                                    maintenance personnel:</label>
                                                <select class="form-control col-4 select-new" id="korphil" name="korphil">
                                                    <option value="">Choose</option>
                                                    <?php
                                                    // Execute your SQL query
                                                    $new_date7 = date('Y-m-d');
                                                    $new_sql7 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date7'";
                                                    $new_result7 = $conn->query($new_sql7) or die($conn->error);

                                                    // Check if there are any results
                                                    if ($new_result7->num_rows > 0) {
                                                        // Iterate through the results and create an option for each
                                                        while ($new_row7 = $new_result7->fetch_assoc()) {
                                                            //If need ID remove tong comment
                                                            //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                            echo '<option value="' . htmlspecialchars($new_row7['fullName']) . '">' . htmlspecialchars($new_row7['fullName']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                    </form>
        </section>

        <section class="table-personnel">
            <div>
                <label for="korphil" class="form-label">Assigned maintenance personnel:</label>

                <?php
                if ($result7->num_rows > 0) {
                    echo "<div class='table-container-new'>";
                    echo "<table class='new-table'>";
                    echo "<tbody class='table-body'>"; // Start tbody
                    while ($row7 = $result7->fetch_assoc()) {
                        echo '<tr>';
                        // Button comes first
                        echo '<td>';
                        echo '<button type="button" class="new-delete-btn" data-sbid="' . $row7['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row7['sbId'] . '</td><td style="display: none;">' . $row7['date'] . '</td><td>' . $row7['techVoc'] . '</td><td style="display: none;">' . $row7['oldAcad'] . '</td><td style="display: none;">' . $row7['belmonte'] . '</td><td style="display: none;">' . $row7['metalcasting'] . '</td><td style="display: none;">' . $row7['korphil'] . '</td><td style="display: none;">' . $row7['multipurpose'] . '</td><td style="display: none;">' . $row7['chineseA'] . '</td><td style="display: none;">' . $row7['chineseB'] . '</td><td style="display: none;">' . $row7['urbanFarming'] . '</td><td style="display: none;">' . $row7['administration'] . '</td><td style="display: none;">' . $row7['bautista'] . '</td><td style="display: none;">' . $row7['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                        echo '</td>';
                        // Then the hidden sbId field
                        echo '<td style="display: none;">' . $row7['sbId'] . '</td>';
                        // Followed by the techVoc field
                        echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row7['korphil'] . '</td>';
                        // Hidden input field
                        echo '<td>';
                        echo '<input type="hidden" name="report_id" value="' . $row7['sbId'] . '">';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo "</tbody>"; // End tbody
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo '<div class="no-data-message">No personnel assigned for today.</div>';
                }
                ?>
            </div>
        </section>
        </form>
        </div>

        <div class="footer">
            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                Save
            </button>

        </div>
        </div>
        </div>
        </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save7" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


        <!--Modal for multipurpose-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal8" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>Multipurpose Building</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body modal-new-class">
                            <form method="post" id="multipurposeForm">
                                <section class="choose-personnel">
                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>


                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="multipurpose" name="multipurpose">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date8 = date('Y-m-d');
                                            $new_sql8 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date8'";
                                            $new_result8 = $conn->query($new_sql8) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result8->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row8 = $new_result8->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row8['fullName']) . '">' . htmlspecialchars($new_row8['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </form>
                            </section>

                            <section class="table-personnel">
                                <div>
                                    <label for="multipurpose" class="form-label">Assigned maintenance personnel:</label>

                                    <?php
                                    if ($result8->num_rows > 0) {
                                        echo "<div class='table-container-new'>";
                                        echo "<table class='new-table'>";
                                        echo "<tbody class='table-body'>"; // Start tbody
                                        while ($row8 = $result8->fetch_assoc()) {
                                            echo '<tr>';
                                            // Button comes first
                                            echo '<td>';
                                            echo '<button type="button" class="new-delete-btn" data-sbid="' . $row8['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row8['sbId'] . '</td><td style="display: none;">' . $row8['date'] . '</td><td>' . $row8['techVoc'] . '</td><td style="display: none;">' . $row8['oldAcad'] . '</td><td style="display: none;">' . $row8['belmonte'] . '</td><td style="display: none;">' . $row8['metalcasting'] . '</td><td style="display: none;">' . $row8['korphil'] . '</td><td style="display: none;">' . $row8['multipurpose'] . '</td><td style="display: none;">' . $row8['chineseA'] . '</td><td style="display: none;">' . $row8['chineseB'] . '</td><td style="display: none;">' . $row8['urbanFarming'] . '</td><td style="display: none;">' . $row8['administration'] . '</td><td style="display: none;">' . $row8['bautista'] . '</td><td style="display: none;">' . $row8['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                            echo '</td>';
                                            // Then the hidden sbId field
                                            echo '<td style="display: none;">' . $row8['sbId'] . '</td>';
                                            // Followed by the techVoc field
                                            echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row8['multipurpose'] . '</td>';
                                            // Hidden input field
                                            echo '<td>';
                                            echo '<input type="hidden" name="report_id" value="' . $row8['sbId'] . '">';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        echo "</tbody>"; // End tbody
                                        echo "</table>";
                                        echo "</div>";
                                    } else {
                                        echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                    }
                                    ?>
                                </div>
                            </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save8" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->



        <!--Modal for chineseA-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal9" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>Chinese A Building</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>

                        <div class="modal-body modal-new-class">
                            <form method="post" id="chineseAForm">
                                <section class="choose-personnel">
                                    <!-- <form method="post" class="row g-3"> -->


                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>


                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="chineseA" name="chineseA">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date9 = date('Y-m-d');
                                            $new_sql9 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date9'";
                                            $new_result9 = $conn->query($new_sql9) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result9->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row9 = $new_result9->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row9['fullName']) . '">' . htmlspecialchars($new_row9['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </form>
                            </section>

                            <section class="table-personnel">
                                <div>
                                    <label for="chineseA" class="form-label">Assigned maintenance personnel:</label>

                                    <?php
                                    if ($result9->num_rows > 0) {
                                        echo "<div class='table-container-new'>";
                                        echo "<table class='new-table'>";
                                        echo "<tbody class='table-body'>"; // Start tbody
                                        while ($row9 = $result9->fetch_assoc()) {
                                            echo '<tr>';
                                            // Button comes first
                                            echo '<td>';
                                            echo '<button type="button" class="new-delete-btn" data-sbid="' . $row9['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row9['sbId'] . '</td><td style="display: none;">' . $row9['date'] . '</td><td>' . $row9['techVoc'] . '</td><td style="display: none;">' . $row9['oldAcad'] . '</td><td style="display: none;">' . $row9['belmonte'] . '</td><td style="display: none;">' . $row9['metalcasting'] . '</td><td style="display: none;">' . $row9['korphil'] . '</td><td style="display: none;">' . $row9['multipurpose'] . '</td><td style="display: none;">' . $row9['chineseA'] . '</td><td style="display: none;">' . $row9['chineseB'] . '</td><td style="display: none;">' . $row9['urbanFarming'] . '</td><td style="display: none;">' . $row9['administration'] . '</td><td style="display: none;">' . $row9['bautista'] . '</td><td style="display: none;">' . $row9['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                            echo '</td>';
                                            // Then the hidden sbId field
                                            echo '<td style="display: none;">' . $row9['sbId'] . '</td>';
                                            // Followed by the techVoc field
                                            echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row9['chineseA'] . '</td>';
                                            // Hidden input field
                                            echo '<td>';
                                            echo '<input type="hidden" name="report_id" value="' . $row9['sbId'] . '">';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        echo "</tbody>"; // End tbody
                                        echo "</table>";
                                        echo "</div>";
                                    } else {
                                        echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                    }
                                    ?>
                                </div>
                            </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save9" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->



        <!--Modal for chineseB-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal10" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>Chinese B Building</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body modal-new-class">
                            <form method="post" id="chineseBForm">
                                <section class="choose-personnel">
                                    <!-- <form method="post" class="row g-3"> -->

                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>


                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="chineseB" name="chineseB">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date10 = date('Y-m-d');
                                            $new_sql10 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date10'";
                                            $new_result10 = $conn->query($new_sql10) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result10->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row10 = $new_result10->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row10['fullName']) . '">' . htmlspecialchars($new_row10['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </form>
                            </section>

                            <section class="table-personnel">
                                <div>
                                    <label for="chineseB" class="form-label">Assigned maintenance personnel:</label>

                                    <?php
                                    if ($result10->num_rows > 0) {
                                        echo "<div class='table-container-new'>";
                                        echo "<table class='new-table'>";
                                        echo "<tbody class='table-body'>"; // Start tbody
                                        while ($row10 = $result10->fetch_assoc()) {
                                            echo '<tr>';
                                            // Button comes first
                                            echo '<td>';
                                            echo '<button type="button" class="new-delete-btn" data-sbid="' . $row10['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row10['sbId'] . '</td><td style="display: none;">' . $row10['date'] . '</td><td>' . $row10['techVoc'] . '</td><td style="display: none;">' . $row10['oldAcad'] . '</td><td style="display: none;">' . $row10['belmonte'] . '</td><td style="display: none;">' . $row10['metalcasting'] . '</td><td style="display: none;">' . $row10['korphil'] . '</td><td style="display: none;">' . $row10['multipurpose'] . '</td><td style="display: none;">' . $row10['chineseA'] . '</td><td style="display: none;">' . $row10['chineseB'] . '</td><td style="display: none;">' . $row10['urbanFarming'] . '</td><td style="display: none;">' . $row10['administration'] . '</td><td style="display: none;">' . $row10['bautista'] . '</td><td style="display: none;">' . $row10['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                            echo '</td>';
                                            // Then the hidden sbId field
                                            echo '<td style="display: none;">' . $row10['sbId'] . '</td>';
                                            // Followed by the techVoc field
                                            echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row10['chineseB'] . '</td>';
                                            // Hidden input field
                                            echo '<td>';
                                            echo '<input type="hidden" name="report_id" value="' . $row10['sbId'] . '">';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        echo "</tbody>"; // End tbody
                                        echo "</table>";
                                        echo "</div>";
                                    } else {
                                        echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                    }
                                    ?>
                                </div>
                            </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save10" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


        <!--Modal for urbanFarming-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal11" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>Urban Farming Site</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body modal-new-class">
                            <form method="post" id="urbanFarmingForm">
                                <section class="choose-personnel">
                                    <!-- <form method="post" class="row g-3"> -->

                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>


                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="urbanFarming" name="urbanFarming">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date11 = date('Y-m-d');
                                            $new_sql11 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date11'";
                                            $new_result11 = $conn->query($new_sql11) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result11->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row11 = $new_result11->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row11['fullName']) . '">' . htmlspecialchars($new_row11['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div id="map"></div>
                                    </div>
                            </form>
                            </section>

                            <section class="table-personnel">
                                <div>
                                    <label for="urbanFarming" class="form-label">Assigned maintenance personnel:</label>

                                    <?php
                                    if ($result11->num_rows > 0) {
                                        echo "<div class='table-container-new'>";
                                        echo "<table class='new-table'>";
                                        echo "<tbody class='table-body'>"; // Start tbody
                                        while ($row11 = $result11->fetch_assoc()) {
                                            echo '<tr>';
                                            // Button comes first
                                            echo '<td>';
                                            echo '<button type="button" class="new-delete-btn" data-sbid="' . $row11['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row11['sbId'] . '</td><td style="display: none;">' . $row11['date'] . '</td><td>' . $row11['techVoc'] . '</td><td style="display: none;">' . $row11['oldAcad'] . '</td><td style="display: none;">' . $row11['belmonte'] . '</td><td style="display: none;">' . $row11['metalcasting'] . '</td><td style="display: none;">' . $row11['korphil'] . '</td><td style="display: none;">' . $row11['multipurpose'] . '</td><td style="display: none;">' . $row11['chineseA'] . '</td><td style="display: none;">' . $row11['chineseB'] . '</td><td style="display: none;">' . $row11['urbanFarming'] . '</td><td style="display: none;">' . $row11['administration'] . '</td><td style="display: none;">' . $row11['bautista'] . '</td><td style="display: none;">' . $row11['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                            echo '</td>';
                                            // Then the hidden sbId field
                                            echo '<td style="display: none;">' . $row11['sbId'] . '</td>';
                                            // Followed by the techVoc field
                                            echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row11['urbanFarming'] . '</td>';
                                            // Hidden input field
                                            echo '<td>';
                                            echo '<input type="hidden" name="report_id" value="' . $row11['sbId'] . '">';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        echo "</tbody>"; // End tbody
                                        echo "</table>";
                                        echo "</div>";
                                    } else {
                                        echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                    }
                                    ?>
                                </div>
                            </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save11" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


        <!--Modal for administration-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal12" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>Administration Building</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>

                        <div class="modal-body modal-new-class">
                            <form method="post" id="administrationForm">
                                <section class="choose-personnel">


                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>

                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="administration" name="administration">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date12 = date('Y-m-d');
                                            $new_sql12 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                        FROM attendancelogs AS a 
                                                        INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                        WHERE acc.userLevel = 3 AND a.date = '$new_date12'";
                                            $new_result12 = $conn->query($new_sql12) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result12->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row12 = $new_result12->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row12['fullName']) . '">' . htmlspecialchars($new_row12['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </section>

                                <section class="table-personnel">
                                    <div>
                                        <label for="administration" class="form-label">Assigned maintenance personnel:</label>

                                        <?php
                                        if ($result12->num_rows > 0) {
                                            echo "<div class='table-container-new'>";
                                            echo "<table class='new-table'>";
                                            echo "<tbody class='table-body'>"; // Start tbody
                                            while ($row12 = $result12->fetch_assoc()) {
                                                echo '<tr>';
                                                // Button comes first
                                                echo '<td>';
                                                echo '<button type="button" class="new-delete-btn" data-sbid="' . $row12['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row12['sbId'] . '</td><td style="display: none;">' . $row12['date'] . '</td><td>' . $row12['techVoc'] . '</td><td style="display: none;">' . $row12['oldAcad'] . '</td><td style="display: none;">' . $row12['belmonte'] . '</td><td style="display: none;">' . $row12['metalcasting'] . '</td><td style="display: none;">' . $row12['korphil'] . '</td><td style="display: none;">' . $row12['multipurpose'] . '</td><td style="display: none;">' . $row12['chineseA'] . '</td><td style="display: none;">' . $row12['chineseB'] . '</td><td style="display: none;">' . $row12['urbanFarming'] . '</td><td style="display: none;">' . $row12['administration'] . '</td><td style="display: none;">' . $row12['bautista'] . '</td><td style="display: none;">' . $row12['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                                echo '</td>';
                                                // Then the hidden sbId field
                                                echo '<td style="display: none;">' . $row12['sbId'] . '</td>';
                                                // Followed by the techVoc field
                                                echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row12['administration'] . '</td>';
                                                // Hidden input field
                                                echo '<td>';
                                                echo '<input type="hidden" name="report_id" value="' . $row12['sbId'] . '">';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            echo "</tbody>"; // End tbody
                                            echo "</table>";
                                            echo "</div>";
                                        } else {
                                            echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                        }
                                        ?>
                                    </div>
                                </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save12" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->



        <!--Modal for bautista-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal13" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>Bautista Building</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>

                        <div class="modal-body modal-new-class">
                            <form method="post" id="bautistaForm">
                                <section class="choose-personnel">



                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>


                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="bautista" name="bautista">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date13 = date('Y-m-d');
                                            $new_sql13 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$new_date13'";
                                            $new_result13 = $conn->query($new_sql13) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result13->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row13 = $new_result13->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row13['fullName']) . '">' . htmlspecialchars($new_row13['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </form>
                            </section>

                            <section class="table-personnel">
                                <div>
                                    <label for="bautista" class="form-label">Assigned maintenance personnel:</label>

                                    <?php
                                    if ($result13->num_rows > 0) {
                                        echo "<div class='table-container-new'>";
                                        echo "<table class='new-table'>";
                                        echo "<tbody class='table-body'>"; // Start tbody
                                        while ($row13 = $result13->fetch_assoc()) {
                                            echo '<tr>';
                                            // Button comes first
                                            echo '<td>';
                                            echo '<button type="button" class="new-delete-btn" data-sbid="' . $row13['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row13['sbId'] . '</td><td style="display: none;">' . $row13['date'] . '</td><td>' . $row13['techVoc'] . '</td><td style="display: none;">' . $row13['oldAcad'] . '</td><td style="display: none;">' . $row13['belmonte'] . '</td><td style="display: none;">' . $row13['metalcasting'] . '</td><td style="display: none;">' . $row13['korphil'] . '</td><td style="display: none;">' . $row13['multipurpose'] . '</td><td style="display: none;">' . $row13['chineseA'] . '</td><td style="display: none;">' . $row13['chineseB'] . '</td><td style="display: none;">' . $row13['urbanFarming'] . '</td><td style="display: none;">' . $row13['administration'] . '</td><td style="display: none;">' . $row13['bautista'] . '</td><td style="display: none;">' . $row13['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                            echo '</td>';
                                            // Then the hidden sbId field
                                            echo '<td style="display: none;">' . $row13['sbId'] . '</td>';
                                            // Followed by the techVoc field
                                            echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row13['bautista'] . '</td>';
                                            // Hidden input field
                                            echo '<td>';
                                            echo '<input type="hidden" name="report_id" value="' . $row13['sbId'] . '">';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        echo "</tbody>"; // End tbody
                                        echo "</table>";
                                        echo "</div>";
                                    } else {
                                        echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                    }
                                    ?>
                                </div>
                            </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save13" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->



        <!--Modal for newAcad-->
        <div class="modal-parent">
            <div class="modal modal-xl fade" id="exampleModal14" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="header-modal">
                            <h5>New Academic Building</h5>
                            <button class="btn btn-close-modal-emp close-modal-btn-new" data-bs-dismiss="modal">X</button>
                        </div>

                        <div class="modal-body modal-new-class">
                            <form method="post" id="newacadForm">
                                <section class="choose-personnel">
                                    <!-- <form method="post" class="row g-3"> -->


                                    <div class="col-4" style="display: none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                    </div>

                                    <div class="col-12 choosy-new">
                                        <label for="techVoc col-8" class="form-label">Choose a
                                            maintenance personnel:</label>
                                        <select class="form-control col-4 select-new" id="newAcad" name="newAcad">
                                            <option value="">Choose</option>
                                            <?php
                                            // Execute your SQL query
                                            $new_date14 = date('Y-m-d');
                                            $new_sql14 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                            FROM attendancelogs AS a 
                                                            INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                            WHERE acc.userLevel = 3 AND a.date = '$current_date14'";
                                            $new_result14 = $conn->query($new_sql14) or die($conn->error);

                                            // Check if there are any results
                                            if ($new_result14->num_rows > 0) {
                                                // Iterate through the results and create an option for each
                                                while ($new_row14 = $new_result14->fetch_assoc()) {
                                                    //If need ID remove tong comment
                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                    echo '<option value="' . htmlspecialchars($new_row14['fullName']) . '">' . htmlspecialchars($new_row14['fullName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </section>

                                <section class="table-personnel">
                                    <div>
                                        <label for="newAcad" class="form-label">Assigned maintenance personnel:</label>

                                        <?php
                                        if ($result14->num_rows > 0) {
                                            echo "<div class='table-container-new'>";
                                            echo "<table class='new-table'>";
                                            echo "<tbody class='table-body'>"; // Start tbody
                                            while ($row14 = $result14->fetch_assoc()) {
                                                echo '<tr>';
                                                // Button comes first
                                                echo '<td>';
                                                echo '<button type="button" class="new-delete-btn" data-sbid="' . $row14['sbId'] . '" data-row-html="' . htmlentities('<tr class="solid"><td style="display: none;">' . $row14['sbId'] . '</td><td style="display: none;">' . $row14['date'] . '</td><td>' . $row14['techVoc'] . '</td><td style="display: none;">' . $row14['oldAcad'] . '</td><td style="display: none;">' . $row14['belmonte'] . '</td><td style="display: none;">' . $row14['metalcasting'] . '</td><td style="display: none;">' . $row14['korphil'] . '</td><td style="display: none;">' . $row14['multipurpose'] . '</td><td style="display: none;">' . $row14['chineseA'] . '</td><td style="display: none;">' . $row14['chineseB'] . '</td><td style="display: none;">' . $row14['urbanFarming'] . '</td><td style="display: none;">' . $row14['administration'] . '</td><td style="display: none;">' . $row14['bautista'] . '</td><td style="display: none;">' . $row14['newAcad'] . '</td></tr>') . '"><i><img src="../../src/img/Undo.png" alt="" class="undo-logo"/></i></button>';
                                                echo '</td>';
                                                // Then the hidden sbId field
                                                echo '<td style="display: none;">' . $row14['sbId'] . '</td>';
                                                // Followed by the techVoc field
                                                echo '<td style="background-color:#D6E4F0; border-radius:15px; padding: 10px; color:#1E56A0; font-size:12px; font-weight: 500 !important;margin-left: 1em;">' . $row14['newAcad'] . '</td>';
                                                // Hidden input field
                                                echo '<td>';
                                                echo '<input type="hidden" name="report_id" value="' . $row14['sbId'] . '">';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            echo "</tbody>"; // End tbody
                                            echo "</table>";
                                            echo "</div>";
                                        } else {
                                            echo '<div class="no-data-message">No personnel assigned for today.</div>';
                                        }
                                        ?>
                                    </div>
                                </section>
                            </form>
                        </div>

                        <div class="footer">
                            <button type="button" class="btn add-modal-btn" onclick="confirmAlert()">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL for Save-->
        <!-- <div class="modal fade" id="save14" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-footer">
                                        Are you sure you want to save this changes?
                                        <div class="modal-popups">
                                            <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                            <button class="btn add-modal-btn confirm-delete-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

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
        <script src="../../src/js/dashboard.js"></script>
        <script src="../../src/js/profileModalController.js"></script>


    </body>

    </html>