<?php

include_once("../../config/connection.php");
$conn = connection();
require_once("../../auto_logout.php");
if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {

    date_default_timezone_set('Asia/Manila');
    $sql2 = "SELECT * FROM scheduleboard";
    $result2 = $conn->query($sql2) or die($conn->error);

    //Add Employee
    if (isset($_POST['save'])) {
        $sbId = $_POST['sbId'];
        $date = $_POST['date'];
        $techVoc = $_POST['techVoc'];
        $oldAcad = $_POST['oldAcad'];
        $belmonte = $_POST['belmonte'];
        $metalcasting = $_POST['metalcasting'];
        $korphil = $_POST['korphil'];
        $multipurpose = $_POST['multipurpose'];
        $chineseA = $_POST['chineseA'];
        $chineseB = $_POST['chineseB'];
        $urbanFarming = $_POST['urbanFarming'];
        $administration = $_POST['administration'];
        $bautista = $_POST['bautista'];
        $newAcad = $_POST['newAcad'];

        $sql1 = "INSERT INTO `scheduleboard`(`sbId`, `date`, `techVoc`, `oldAcad`, `belmonte`, `metalcasting`, `korphil`, `multipurpose`, `chineseA`, `chineseB`, `urbanFarming`, `administration`, `bautista`, `newAcad`)
  VALUES ('$sbId', '$date', '$techVoc', '$oldAcad', '$belmonte', '$metalcasting', '$korphil', '$multipurpose', '$chineseA', '$chineseB', '$urbanFarming', '$administration' , '$bautista' , '$newAcad')
  ";

        if ($conn->query($sql1) === TRUE) {
            // Query executed successfully
            header("Location: dashboard.php");
            exit;
        } else {
            // Query execution failed
            echo "Error: " . $sql1 . "<br>" . $conn->error;
        }
    }

    // Get the current date in the same format as your date column
    //Para madisplay mga tao sa araw na'yon
    $current_date = date('Y-m-d');

    $sql = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
        FROM attendancelogs AS a 
        INNER JOIN account AS acc ON a.accountId = acc.accountId 
        WHERE acc.userLevel = 3 AND a.date = '$current_date'";
    $result = $conn->query($sql) or die($conn->error);
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <!-- BOOTSTRAP -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script> -->
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
                <header>
                    <div class="cont-header">
                        <h1 class="tab-name-only">Dashboard</h1>
                    </div>
                </header>

                <div class="content-container">
                    <section class="content1">
                        <form method="post">
                            <main id="calendar">
                                <div id="calendar-header">
                                    <span id="current-date"></span>
                                </div>

                                <div id="calendar-body">
                                    <div class="building">
                                        <div class="mini-header">
                                            Tech-Voc
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal3" id="techVoc" name="techVoc">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Old Acad
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal4" id="oldAcad" name="oldAcad">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Belmonte
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal5" id="belmonte" name="belmonte">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Metal Casting
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal6" id="metalcasting" name="metalcasting">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Korphil
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal7" id="korphil" name="korphil">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Multipurpose
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal8" id="multipurpose" name="multipurpose">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Bautista
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal13" id="bautista" name="bautista">

                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            New-Acad
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal14" id="newAcad" name="newAcad">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Administration
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal12" id="administration" name="administration">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Urban Farming
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal11" id="urbanFarming" name="urbanFarming">

                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Chinese A
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal9" id="chineseA" name="chineseA">


                                        </div>
                                    </div>

                                    <div class="building">
                                        <div class="mini-header">
                                            Chinese B
                                        </div>
                                        <div class="building-body" data-bs-toggle="modal" data-bs-target="#exampleModal10" id="chineseB" name="chineseB">


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

                                <!--Modal for Tech-Voc-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Tech-Voc Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="techVoc" class="form-label">Choose a personnel for
                                                            Tech-Voc Building:</label>
                                                        <select class="form-control col-6" id="techVoc" name="techVoc">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date3 = date('Y-m-d');
                                                            $sql3 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date3'";
                                                            $result3 = $conn->query($sql3) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result3->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row3 = $result3->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row3['fullName']) . '">' . htmlspecialchars($row3['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--Modal for Old Acad-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Old Academic Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="oldAcad" class="form-label">Choose a personnel for
                                                            Old Academic Building:</label>
                                                        <select class="form-control col-6" id="oldAcad" name="oldAcad">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date4 = date('Y-m-d');
                                                            $sql4 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date4'";
                                                            $result4 = $conn->query($sql4) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result4->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row4 = $result4->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row4['fullName']) . '">' . htmlspecialchars($row4['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for belmonte-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Belmonte Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="belmonte" class="form-label">Choose a personnel for
                                                            Belmonte Building:</label>
                                                        <select class="form-control col-6" id="belmonte" name="belmonte">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date5 = date('Y-m-d');
                                                            $sql5 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date5'";
                                                            $result5 = $conn->query($sql5) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result5->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row5 = $result5->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row5['fullName']) . '">' . htmlspecialchars($row5['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for metalcasting-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal6" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Metal Casting Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="metalcasting" class="form-label">Choose a personnel for
                                                            Metal Casting Building:</label>
                                                        <select class="form-control col-6" id="metalcasting" name="metalcasting">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date6 = date('Y-m-d');
                                                            $sql6 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date6'";
                                                            $result6 = $conn->query($sql6) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result6->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row6 = $result6->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row6['fullName']) . '">' . htmlspecialchars($row6['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for korphil-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal7" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Korphil Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="korphil" class="form-label">Choose a personnel for
                                                            Korphil Building:</label>
                                                        <select class="form-control col-6" id="korphil" name="korphil">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date7 = date('Y-m-d');
                                                            $sql7 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date7'";
                                                            $result7 = $conn->query($sql7) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result7->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row7 = $result7->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row7['fullName']) . '">' . htmlspecialchars($row7['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for multipurpose-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal8" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Multipurpose Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="multipurpose" class="form-label">Choose a personnel for
                                                            Multipurpose Building:</label>
                                                        <select class="form-control col-6" id="multipurpose" name="multipurpose">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date8 = date('Y-m-d');
                                                            $sql8 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date8'";
                                                            $result8 = $conn->query($sql8) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result8->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row8 = $result8->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row8['fullName']) . '">' . htmlspecialchars($row8['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for chineseA-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal9" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Chinese A Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="chineseA" class="form-label">Choose a personnel for
                                                            Chinese A Building:</label>
                                                        <select class="form-control col-6" id="chineseA" name="chineseA">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date9 = date('Y-m-d');
                                                            $sql9 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date9'";
                                                            $result9 = $conn->query($sql9) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result9->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row9 = $result9->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row9['fullName']) . '">' . htmlspecialchars($row9['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for chineseB-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal10" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Chinese B Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="chineseB" class="form-label">Choose a personnel for
                                                            Chinese B Building:</label>
                                                        <select class="form-control col-6" id="chineseB" name="chineseB">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date10 = date('Y-m-d');
                                                            $sql10 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date10'";
                                                            $result10 = $conn->query($sql10) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result10->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row10 = $result10->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row10['fullName']) . '">' . htmlspecialchars($row10['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for urbanFarming-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal11" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Urban Farming Site</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="urbanFarming" class="form-label">Choose a personnel for
                                                            Urban Farming Site:</label>
                                                        <select class="form-control col-6" id="urbanFarming" name="urbanFarming">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date11 = date('Y-m-d');
                                                            $sql11 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date11'";
                                                            $result11 = $conn->query($sql11) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result11->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row11 = $result11->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row11['fullName']) . '">' . htmlspecialchars($row11['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for administration-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal12" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Urban Farming Site</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="administration" class="form-label">Choose a personnel
                                                            for
                                                            Urban Farming Site:</label>
                                                        <select class="form-control col-6" id="administration" name="administration">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date12 = date('Y-m-d');
                                                            $sql12 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date12'";
                                                            $result12 = $conn->query($sql12) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result12->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row12 = $result12->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row12['fullName']) . '">' . htmlspecialchars($row12['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for bautista-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal13" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for Bautista</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="bautista" class="form-label">Choose a personnel for
                                                            Bautista Building:</label>
                                                        <select class="form-control col-6" id="bautista" name="bautista">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date13 = date('Y-m-d');
                                                            $sql13 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date13'";
                                                            $result13 = $conn->query($sql13) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result13->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row13 = $result13->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row13['fullName']) . '">' . htmlspecialchars($row13['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Modal for newAcad-->
                                <div class="modal-parent">
                                    <div class="modal modal-xl fade" id="exampleModal14" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="header">
                                                    <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal">X</button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- <form method="post" class="row g-3"> -->
                                                    <h5>Add personnel for New Academic Building</h5>

                                                    <div class="col-4" style="display: none">
                                                        <label for="date" class="form-label">Date:</label>
                                                        <input type="text" class="form-control for-names" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="display:none" />
                                                    </div>

                                                    <div class="col-8">
                                                        <label for="newAcad" class="form-label">Choose a personnel for
                                                            New Academic Building:</label>
                                                        <select class="form-control col-6" id="newAcad" name="newAcad">
                                                            <option value="">Select Personnel</option>
                                                            <?php
                                                            // Execute your SQL query
                                                            $current_date14 = date('Y-m-d');
                                                            $sql14 = "SELECT a.accountId, CONCAT(acc.firstName, ' ', IFNULL(acc.middleName, ''), ' ', acc.lastName) AS fullName 
                                                           FROM attendancelogs AS a 
                                                           INNER JOIN account AS acc ON a.accountId = acc.accountId 
                                                           WHERE acc.userLevel = 3 AND a.date = '$current_date14'";
                                                            $result14 = $conn->query($sql14) or die($conn->error);

                                                            // Check if there are any results
                                                            if ($result14->num_rows > 0) {
                                                                // Iterate through the results and create an option for each
                                                                while ($row14 = $result14->fetch_assoc()) {
                                                                    //If need ID remove tong comment
                                                                    //echo '<option value="'.htmlspecialchars($row3['accountId']).'">'.htmlspecialchars($row3['fullName']).'</option>';
                                                                    echo '<option value="' . htmlspecialchars($row14['fullName']) . '">' . htmlspecialchars($row14['fullName']) . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="footer">
                                                    <button type="button" class="btn add-modal-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>





                                <!--MODAL for Save-->
                                <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-footer">
                                                Are you sure you want to add this personnel to this changes?
                                                <div class="modal-popups">
                                                    <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                                    <button class="btn add-modal-btn" name="save" data-bs-dismiss="modal">Yes</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </main>
                        </form>

                        <!--Para sa presents today-->
                        <?php if ($result->num_rows > 0) : ?>
                            <section class="present-part">
                                <div class="present-container">
                                    <div class="present-header">
                                        <img src="../../src/img/Vector.png" class="icon-present" />
                                        <p>
                                            <?php echo $result->num_rows; ?>
                                        </p> <!-- Dynamically display the count -->
                                        <p>
                                            <?php
                                            if ($result->num_rows === 0) {
                                                echo 'No Attendees Present Today'; // Case for 0 attendees
                                            } elseif ($result->num_rows === 1) {
                                                echo 'Present Today'; // Case for 1 attendee
                                            } else {
                                                echo 'Presents Today'; // Case for multiple attendees
                                            }
                                            ?>
                                        </p> <!-- Adjust text based on count -->
                                    </div>
                                    <div class="list-names">
                                        <ul class="ul-list">
                                            <?php while ($row = $result->fetch_assoc()) : ?>
                                                <li>
                                                    <?php echo htmlspecialchars($row['fullName']); ?>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </div>

                                    <div class="present-pagination">
                                    </div>

                                </div>
                            </section>
                        <?php else : ?>
                            <section class="present-part">
                                <div class="present-container">
                                    <div class="present-header">
                                        <img src="../../src/img/Vector.png" class="icon-present" />
                                        <p>No Present Today</p> <!-- Show no attendees message -->
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>

                    </section>


                    <section class="content2">
                        <div class="calendar-container">


                            <div class="calendar">
                                <div class="month-indicator">
                                    <span class="month">January</span>
                                    <span class="year">2024</span>
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
                        <div class="filter-container">
                            <select id="filter-select" class="form-control" onchange="updateChart()">
                                <option value="" disabled selected style="color: #999B9F;">
                                    <i class="fas fa-caret-down"></i> Choose a Building
                                </option>
                                <option value="Tech-Voc">Tech-Voc</option>
                                <option value="Old Acad">Old Acad</option>
                                <option value="Belmonte">Belmonte</option>
                                <!-- Add other building options here -->
                            </select>
                        </div>
                        <div id="chart-container">
                            <canvas id="doughnut-chart" width="312" height="312" style="display: block; box-sizing: border-box; height: 300px; width: 300px;"></canvas>
                        </div>


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

        <!-- SCRIPTS -->

        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <script src="../../src/js/main.js"></script>
        <script src="../../src/js/dashboard.js"></script>
        <script src="../../src/js/profileModalController.js"></script>
    </body>

    </html>