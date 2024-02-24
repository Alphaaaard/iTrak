<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Map</title>
        <!-- BOOTSTRAP -->
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/map.css" />
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
                <li class="active">
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
                <div class="content-container" id="content-container">

                    <header>
                        <div class="cont-header">
                            <h1 class="tab-name-only">3D Map</h1>
                        </div>
                    </header>

                    <div id="model-container" class="content"></div>

                    <!-- Mobile View Building Selection -->
                    <div class="buildings" id="buildings" style="visibility:hidden">
                        <div class="building building1" style="display: none;">Floor</div>
                        <div class="building building2">TechVoc</div>
                        <div class="building building3">Old Academic</div>
                        <div class="building building4">Belmonte</div>
                        <div class="building building5">Metalcasting</div>
                        <div class="building building6">KORPHIL</div>
                        <div class="building building7">Multipurpose</div>
                        <div class="building building8">Admin</div>
                        <div class="building building9">Bautista</div>
                        <div class="building building10">Academic</div>
                        <div class="building building11">Ballroom</div>
                    </div>

                    <!-- FUCK THIS SHIT -->
                    <div id="myModal1" class="modal">
                        <div class=" modal-content">
                            <div id="modalContent1">
                            </div>
                            <span class="close" id="closeModal1">&times;</span>
                        </div>
                    </div>

                    <!-- TECHVOC-->
                    <div id="myModal2" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal2">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>TECHVOC</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="techvoc-floor1-tab" data-floor-target="#TECHVOC-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="techvoc-floor2-tab" data-floor-target="#TECHVOC-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- TECHVOC FLOOR CONTENT-->
                    <div id="TECHVOC-F1" class="content">
                        <p>TECHVOC-F1</p>
                    </div>

                    <div id="TECHVOC-F2" class="content">
                        <p>TECHVOC-F2</p>
                    </div>

                    <!-- OLD ACADEMIC BUILDING-->
                    <div id="myModal3" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal3">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>OLD ACADEMIC BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="old-floor1-tab" data-floor-target="#old-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="old-floor2-tab" data-floor-target="#old-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- OLD FLOOR CONTENT-->
                    <div id="old-F1" class="content">
                        <img src="../../src/floors/oldAcademicB/OAB1F.png" alt="" class="Floor-container">
                    </div>

                    <div id="old-F2" class="content">
                        <img src="../../src/floors/oldAcademicB/OAB2F.png" alt="" class="Floor-container">
                    </div>

                    <!-- BELMONTE ACADEMIC BUILDING-->
                    <div id="myModal4" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal4">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>BELMONTE BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="belmonte-floor1-tab" data-floor-target="#belmonte-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="belmonte-floor2-tab" data-floor-target="#belmonte-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="belmonte-floor3-tab" data-floor-target="#belmonte-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="belmonte-floor4-tab" data-floor-target="#belmonte-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- BELMONTE FLOOR CONTENT-->
                    <div id="belmonte-F1" class="content">
                        <img src="../../src/floors/belmonteB/BB1F.png" alt="" class="Floor-container">
                    </div>

                    <div id="belmonte-F2" class="content">
                        <img src="../../src/floors/belmonteB/BB2F.png" alt="" class="Floor-container">
                    </div>

                    <div id="belmonte-F3" class="content">
                        <img src="../../src/floors/belmonteB/BB3F.png" alt="" class="Floor-container">
                    </div>

                    <div id="belmonte-F4" class="content">
                        <img src="../../src/floors/belmonteB/BB4F.png" alt="" class="Floor-container">
                    </div>

                    <!-- METALCASTING ACADEMIC BUILDING-->
                    <div id="myModal5" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal5">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>METAL CASTING BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="metalcasting-floor1-tab" data-floor-target="#metalcasting-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="metalcasting-floor2-tab" data-floor-target="#metalcasting-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="metalcasting-floor3-tab" data-floor-target="#metalcasting-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="metalcasting-floor4-tab" data-floor-target="#metalcasting-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- METALCASTING FLOOR CONTENT-->
                    <div id="metalcasting-F1" class="content">
                        <p>METALCASTING ACADEMIC BUILDING - F1</p>
                    </div>

                    <div id="metalcasting-F2" class="content">
                        <p>METALCASTING ACADEMIC BUILDING - F2</p>
                    </div>

                    <div id="metalcasting-F3" class="content">
                        <p>METALCASTING ACADEMIC BUILDING - F3</p>
                    </div>

                    <div id="metalcasting-F4" class="content">
                        <p>METALCASTING ACADEMIC BUILDING - F4</p>
                    </div>

                    <!-- KORPHIL ACADEMIC BUILDING-->
                    <div id="myModal6" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal6">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>KORPHIL CASTING BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="korphil-floor1-tab" data-floor-target="#korphil-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="korphil-floor2-tab" data-floor-target="#korphil-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="korphil-floor3-tab" data-floor-target="#korphil-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="korphil-floor4-tab" data-floor-target="#korphil-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- KORPHIL FLOOR CONTENT-->
                    <div id="korphil-F1" class="content">
                        <p>KORPHIL ACADEMIC BUILDING - F1</p>
                    </div>

                    <div id="korphil-F2" class="content">
                        <p>KORPHIL ACADEMIC BUILDING - F2</p>
                    </div>

                    <div id="korphil-F3" class="content">
                        <p>KORPHIL ACADEMIC BUILDING - F3</p>
                    </div>

                    <div id="korphil-F4" class="content">
                        <p>KORPHIL ACADEMIC BUILDING - F4</p>
                    </div>

                    <!-- MULTIPURPOSE ACADEMIC BUILDING-->
                    <div id="myModal7" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal7">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>MULTIPURPOSE CASTING BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="multipurpose-floor1-tab" data-floor-target="#multipurpose-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="multipurpose-floor2-tab" data-floor-target="#multipurpose-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="multipurpose-floor3-tab" data-floor-target="#multipurpose-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="multipurpose-floor4-tab" data-floor-target="#multipurpose-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- MULTIPURPOSE FLOOR CONTENT-->
                    <div id="multipurpose-F1" class="content">
                        <p>MULTIPURPOSE ACADEMIC BUILDING - F1</p>
                    </div>

                    <div id="multipurpose-F2" class="content">
                        <p>MULTIPURPOSE ACADEMIC BUILDING - F2</p>
                    </div>

                    <div id="multipurpose-F3" class="content">
                        <p>MULTIPURPOSE ACADEMIC BUILDING - F3</p>
                    </div>

                    <div id="multipurpose-F4" class="content">
                        <p>MULTIPURPOSE ACADEMIC BUILDING - F4</p>
                    </div>

                    <!-- ADMIN ACADEMIC BUILDING-->
                    <div id="myModal8" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal8">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>ADMIN BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="admin-floor1-tab" data-floor-target="#admin-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="admin-floor2-tab" data-floor-target="#admin-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="admin-floor3-tab" data-floor-target="#admin-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="admin-floor4-tab" data-floor-target="#admin-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="admin-floor5-tab" data-floor-target="#admin-F5" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- ADMIN FLOOR CONTENT-->
                    <div id="admin-F1" class="content">
                        <img src="../../src/floors/adminB/AB1F.png" alt="" class="Floor-container">
                    </div>

                    <div id="admin-F2" class="content">
                        <img src="../../src/floors/adminB/AB2F.png" alt="" class="Floor-container">
                    </div>

                    <div id="admin-F3" class="content">
                        <img src="../../src/floors/adminB/AB3F.png" alt="" class="Floor-container">
                    </div>

                    <div id="admin-F4" class="content">
                        <img src="../../src/floors/adminB/AB4F.png" alt="" class="Floor-container">
                    </div>

                    <div id="admin-F5" class="content">
                        <img src="../../src/floors/adminB/AB5F.png" alt="" class="Floor-container">
                    </div>

                    <!-- BAUTISTA ACADEMIC BUILDING-->
                    <div id="myModal9" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal9">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>BAUTISTA BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="bautista-floor1-tab" data-floor-target="#bautista-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="bautista-floor2-tab" data-floor-target="#bautista-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="bautista-floor3-tab" data-floor-target="#bautista-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="bautista-floor4-tab" data-floor-target="#bautista-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- BAUTISTA FLOOR CONTENT-->
                    <div id="bautista-F1" class="content">
                        <img src="../../src/floors/bautistaB/BB0F.png" alt="" class="Floor-container">
                    </div>

                    <div id="bautista-F2" class="content">
                        <img src="../../src/floors/bautistaB/BB1F.png" alt="" class="Floor-container">
                    </div>

                    <div id="bautista-F3" class="content">
                        <img src="../../src/floors/bautistaB/BB2F.png" alt="" class="Floor-container">
                    </div>

                    <div id="bautista-F4" class="content">
                        <img src="../../src/floors/bautistaB/BB3F.png" alt="" class="Floor-container">
                    </div>

                    <!-- ACADEMIC BUILDING-->
                    <div id="myModal10" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal10">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>ACADEMIC BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor1-tab" data-floor-target="#academic-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor2-tab" data-floor-target="#academic-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor3-tab" data-floor-target="#academic-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor4-tab" data-floor-target="#academic-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor5-tab" data-floor-target="#academic-F5" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor5</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor6-tab" data-floor-target="#academic-F6" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor6</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="academic-floor7-tab" data-floor-target="#academic-F7" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor7</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- ACADEMIC FLOOR CONTENT-->
                    <div id="academic-F1" class="content">
                        <img src="../../src/floors/newAcademicB/NAB1F.png" alt="" class="Floor-container">
                    </div>

                    <div id="academic-F2" class="content">
                        <img src="../../src/floors/newAcademicB/NAB2F.png" alt="" class="Floor-container">
                    </div>

                    <div id="academic-F3" class="content">
                        <img src="../../src/floors/newAcademicB/NAB3F.png" alt="" class="Floor-container">
                    </div>

                    <div id="academic-F4" class="content">
                        <img src="../../src/floors/newAcademicB/NAB4F.png" alt="" class="Floor-container">
                    </div>

                    <div id="academic-F5" class="content">
                        <img src="../../src/floors/newAcademicB/NAB5F.png" alt="" class="Floor-container">
                    </div>

                    <div id="academic-F6" class="content">
                        <img src="../../src/floors/newAcademicB/NAB6F.png" alt="" class="Floor-container">
                    </div>

                    <div id="academic-F7" class="content">
                        <img src="../../src/floors/newAcademicB/NAB7F.png" alt="" class="Floor-container">
                    </div>

                    <!-- BALLROOM ACADEMIC BUILDING-->
                    <div id="myModal11" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal11">&times;</span>
                            <ul class="nav nav-tabs" id="floorTab" role="tablist">
                                <h3>BALLROOM BUILDING</h3>
                                <div class="nav-container">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ballroom-floor1-tab" data-floor-target="#ballroom-F1" type="button" role="tab" aria-controls="floor1" aria-selected="true">Floor1</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ballroom-floor2-tab" data-floor-target="#ballroom-F2" type="button" role="tab" aria-controls="floor2" aria-selected="false">Floor2</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ballroom-floor3-tab" data-floor-target="#ballroom-F3" type="button" role="tab" aria-controls="floor3" aria-selected="false">Floor3</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ballroom-floor4-tab" data-floor-target="#ballroom-F4" type="button" role="tab" aria-controls="floor4" aria-selected="false">Floor4</button>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <!-- BALLROOM FLOOR CONTENT-->
                    <div id="ballroom-F1" class="content">
                        <p>BALLROOM ACADEMIC BUILDING - F1</p>
                    </div>

                    <div id="ballroom-F2" class="content">
                        <p>BALLROOM ACADEMIC BUILDING - F2</p>
                    </div>

                    <div id="ballroom-F3" class="content">
                        <p>BALLROOM ACADEMIC BUILDING - F3</p>
                    </div>

                    <div id="ballroom-F4" class="content">
                        <p>BALLROOM ACADEMIC BUILDING - F4</p>
                    </div>

                    <div id="modalTemplate1" style="display: none">
                        <h2 id="modalTitle1"></h2>
                        <p id="modalDescription1"></p>
                    </div>

                    <div id="modalTemplate2" style="display: none">
                        <h2 id="modalTitle2"></h2>
                        <p id="modalDescription2"></p>
                    </div>

                    <div id="modalTemplate3" style="display: none">
                        <h2 id="modalTitle3"></h2>
                        <p id="modalDescription3"></p>
                    </div>

                    <div id="modalTemplate4" style="display: none">
                        <h2 id="modalTitle4"></h2>
                        <p id="modalDescription4"></p>
                    </div>

                    <div id="modalTemplate5" style="display: none">
                        <h2 id="modalTitle5"></h2>
                        <p id="modalDescription5"></p>
                    </div>

                    <div id="modalTemplate6" style="display: none">
                        <h2 id="modalTitle6"></h2>
                        <p id="modalDescription6"></p>
                    </div>

                    <div id="modalTemplate7" style="display: none">
                        <h2 id="modalTitle7"></h2>
                        <p id="modalDescription7"></p>
                    </div>

                    <div id="modalTemplate8" style="display: none">
                        <h2 id="modalTitle8"></h2>
                        <p id="modalDescription8"></p>
                    </div>

                    <div id="modalTemplate9" style="display: none">
                        <h2 id="modalTitle9"></h2>
                        <p id="modalDescription9"></p>
                    </div>

                    <div id="modalTemplate10" style="display: none">
                        <h2 id="modalTitle10"></h2>
                        <p id="modalDescription10"></p>
                    </div>

                    <div id="modalTemplate11" style="display: none">
                        <h2 id="modalTitle11"></h2>
                        <p id="modalDescription11"></p>
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


        <script>
            const tabButtons = document.querySelectorAll('.nav-link');
            const contentSections = document.querySelectorAll('.content');
            const modelContainer = document.getElementById('model-container');
            const mobileModelContainer = document.getElementById('buildings');
            const modalClose1 = document.getElementById('myModal1');
            const modalClose2 = document.getElementById('myModal2');
            const modalClose3 = document.getElementById('myModal3');
            const modalClose4 = document.getElementById('myModal4');
            const modalClose5 = document.getElementById('myModal5');
            const modalClose6 = document.getElementById('myModal6');
            const modalClose7 = document.getElementById('myModal7');
            const modalClose8 = document.getElementById('myModal8');
            const modalClose9 = document.getElementById('myModal9');
            const modalClose10 = document.getElementById('myModal10');
            const modalClose11 = document.getElementById('myModal11');

            function handleTabClick(targetContentId) {
                modelContainer.style.display = 'none';
                mobileModelContainer.style.display = 'none';
                modalClose1.style.display = 'none';
                modalClose2.style.display = 'none';
                modalClose3.style.display = 'none';
                modalClose4.style.display = 'none';
                modalClose5.style.display = 'none';
                modalClose6.style.display = 'none';
                modalClose7.style.display = 'none';
                modalClose8.style.display = 'none';
                modalClose9.style.display = 'none';
                modalClose10.style.display = 'none';
                modalClose11.style.display = 'none';

                contentSections.forEach(content => {
                    content.classList.remove('active-content');
                });

                const targetContent = document.querySelector(targetContentId);
                targetContent.classList.add('active-content');
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetContentId = this.getAttribute('data-floor-target');
                    handleTabClick(targetContentId);
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const buildings = document.querySelectorAll('.building');
                const modals = [
                    document.getElementById('myModal1'),
                    document.getElementById('myModal2'),
                    document.getElementById('myModal3'),
                    document.getElementById('myModal4'),
                    document.getElementById('myModal5'),
                    document.getElementById('myModal6'),
                    document.getElementById('myModal7'),
                    document.getElementById('myModal8'),
                    document.getElementById('myModal9'),
                    document.getElementById('myModal10'),
                    document.getElementById('myModal11')
                ];

                buildings.forEach((building, index) => {
                    building.addEventListener('click', () => {
                        modals.forEach(modal => {
                            if (modal) {
                                modal.style.display = 'none';
                            }
                        });

                        if (modals[index]) {
                            modals[index].style.display = 'block';
                        }
                    });
                });

                const closeButtons = document.querySelectorAll('.close');
                closeButtons.forEach((closeButton, index) => {
                    closeButton.addEventListener('click', () => {
                        modals[index].style.display = 'none';
                    });
                });
            });
        </script>
        <script src="../../src/js/main.js"></script>
        <script type="module" src="../../src/js/map.js"></script>
        <script src="../../src/js/profileModalController.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>