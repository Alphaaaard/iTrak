<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

function logActivity($conn, $accountId, $actionDescription, $tabValue)
{
    $stmt = $conn->prepare("INSERT INTO activitylogs (accountId, date, action, tab) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param("iss", $accountId, $actionDescription, $tabValue);
    if (!$stmt->execute()) {
        echo "Error logging activity: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {

    $sql = "SELECT * FROM asset WHERE status = 'Working'";
    $result = $conn->query($sql) or die($conn->error);

    $sql2 = "SELECT * FROM asset WHERE status = 'Under Maintenance'";
    $result2 = $conn->query($sql2) or die($conn->error);

    $sql3 = "SELECT * FROM asset WHERE status = 'For Replacement'";
    $result3 = $conn->query($sql3) or die($conn->error);

    $sql4 = "SELECT * FROM asset WHERE status = 'Need Repair'";
    $result4 = $conn->query($sql4) or die($conn->error);


    //Edit
    if (isset($_POST['edit'])) {
        $assetId = $_POST['assetId'];
        $category = $_POST['category'];
        $building = $_POST['building'];
        $floor = $_POST['floor'];
        $room = $_POST['room'];

        $status = $_POST['status'];
        $assignedName = $_POST['assignedName'];
        $assignedBy = $_POST['assignedBy'];
        $date = $_POST['date'];




        $updateSql = "UPDATE `asset` SET `category`='$category', `building`='$building', `floor`='$floor', `room`='$room', `status`='$status', `assignedName`='$assignedName', `assignedBy`='$assignedBy', `date`='$date' WHERE `assetId`='$assetId'";
        if ($conn->query($updateSql) === TRUE) {
            logActivity($conn, $_SESSION['accountId'], "Changed status of asset ID $assetId to $status.", 'Report');
        } else {
            echo "Error updating asset: " . $conn->error;
        }
        header("Location: reports.php");
    }



    if (isset($_POST['assignMaintenance'])) {
        $assetId = $_POST['assetId'];
        $assignedName = $_POST['assignedName'];
        $assignSql = "UPDATE `asset` SET `assignedName`='$assignedName' WHERE `assetId`='$assetId'";
        // Define the SQL statement for assigning maintenance personnel


        // Perform the query only if $assignSql is not empty
        if (!empty($assignSql) && $conn->query($assignSql) === TRUE) {
            logActivity($conn, $_SESSION['accountId'], "Assigned maintenance personnel $assignedName to asset ID $assetId.", 'Report');
        } else {
            echo "Error assigning maintenance personnel: " . $conn->error;
        }
        header("Location: reports.php");
    }

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Reports</title>
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/reports.css" />


        <!--JS for the fcking tabs-->
        <script>
            $(document).ready(function() {

                let tabLastSelected = sessionStorage.getItem("lastTabReport");

                if (!tabLastSelected) {
                    // if no last tab was selected, use the pills-manager for default
                    $("#pills-manager").addClass("show active");
                    // $("#pills-profile").removeClass("show active");
                    $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                    // $(".nav-link[data-bs-target='pills-profile']").removeClass("active");

                } else {

                    switch (tabLastSelected) {
                        case 'pills-manager':
                            $("#pills-manager").addClass("show active");
                            $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                            $(".nav-link[data-bs-target='pills-profile']").removeClass("active");
                            break;
                        case 'pills-profile':
                            $("#pills-profile").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-profile']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                        case 'pills-replace':
                            $("#pills-replace").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-replace']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                        case 'pills-repair':
                            $("#pills-repair").addClass("show active");
                            $("#pills-manager").removeClass("show active");
                            $(".nav-link[data-bs-target='pills-repair']").addClass("active");
                            $(".nav-link[data-bs-target='pills-manager']").removeClass("active");
                            break;
                    }
                }

                // $("#pills-manager").addClass("show active");
                // $("#pills-profile").removeClass("show active");
                // $(".nav-link[data-bs-target='pills-manager']").addClass("active");
                // $(".nav-link[data-bs-target='pills-profile']").removeClass("active");

                $(".nav-link").click(function() {
                    const targetId = $(this).data("bs-target");

                    sessionStorage.setItem("lastTabReport", targetId); //* sets the targetId to the sessionStorage lastTab item

                    $(".tab-pane").removeClass("show active");
                    $(`#${targetId}`).addClass("show active");
                    $(".nav-link").removeClass("active");
                    $(this).addClass("active");
                });
            });
        </script>
    </head>

    <body>
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
                <li>
                    <a href="./map.php">
                        <i class="bi bi-map"></i>
                        <span class="text">Map</span>
                    </a>
                </li>
                <li class="active">
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
        <section id="content">
            <main>
                <div class="content-container">
                    <header>
                        <div class="cont-header">
                            <!-- <h1 class="tab-name">Reports</h1> -->
                            <div class="tbl-filter">
                                <form class="d-flex" role="search">
                                    <input class="form-control icon" type="search" placeholder="Search" aria-label="Search" id="search-box" onkeyup="searchTable()" />
                                </form>

                            </div>
                        </div>
                    </header>

                    <!--Content start of tabs-->
                    <div class="new-nav">
                        <ul>
                            <li><a href="#" class="nav-link" data-bs-target="pills-manager">Working</a></li>
                            <li><a href="#" class="nav-link" data-bs-target="pills-profile">Under Maintenance</a></li>
                            <li><a href="#" class="nav-link" data-bs-target="pills-replace">For Replacement</a></li>
                            <li><a href="#" class="nav-link" data-bs-target="pills-repair">For Repair</a></li>
                        </ul>
                    </div>

                    <!--Tab for table 1-->
                    <div class="tab-content pt" id="myTabContent">
                        <div class="tab-pane fade show active" id="pills-manager" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>TRACKING #</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                if ($result->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row['assetId'] . '</td>';
                                        echo '<td >' . $row['date'] . '</td>';
                                        echo '<td >' . $row['category'] . '</td>';
                                        echo '<td >' . $row['building'] . " / " . $row['floor'] . " / " . $row['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row['images'] . '</td>';
                                        echo '<td >' . $row['status'] . '</td>';

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


                        <!--Tab for table 2-->
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>TRACKING #</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>STATUS</th>
                                        </tr>

                                    </table>
                                </div>
                                <!--Content of table 2-->
                                <?php
                                if ($result2->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row2 = $result2->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row2['assetId'] . '</td>';
                                        echo '<td >' . $row2['date'] . '</td>';
                                        echo '<td >' . $row2['category'] . '</td>';
                                        echo '<td >' . $row2['building'] . " / " . $row2['floor'] . " / " . $row2['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row2['images'] . '</td>';
                                        echo '<td >' . $row2['status'] . '</td>';
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

                        <!--Tab for table 3 - Replacement -->
                        <div class="tab-pane fade" id="pills-replace" role="tabpanel" aria-labelledby="replace-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>TRACKING #</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </table>
                                </div>
                                <!--Content of table 3-->
                                <?php
                                if ($result3->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row3 = $result3->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row3['assetId'] . '</td>';
                                        echo '<td >' . $row3['date'] . '</td>';
                                        echo '<td >' . $row3['category'] . '</td>';
                                        echo '<td >' . $row3['building'] . " / " . $row3['floor'] . " / " . $row3['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row3['images'] . '</td>';
                                        echo '<td >' . $row3['status'] . '</td>';
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

                        <!--Tab for table 4 - Repair -->
                        <div class="tab-pane fade" id="pills-repair" role="tabpanel" aria-labelledby="repair-tab">
                            <div class="table-content">
                                <div class='table-header'>
                                    <table>
                                        <tr>
                                            <th>TRACKING #</th>
                                            <th>DATE & TIME</th>
                                            <th>CATEGORY</th>
                                            <th>LOCATION</th>
                                            <th>STATUS</th>
                                            <th>ASSIGNEE</th>
                                        </tr>
                                    </table>
                                </div>
                                <!--Content of table 4-->
                                <?php
                                if ($result4->num_rows > 0) {
                                    echo "<div class='table-container'>";
                                    while ($row4 = $result4->fetch_assoc()) {
                                        echo "<table>";
                                        echo '<tr>';
                                        echo '<td>' . $row4['assetId'] . '</td>';
                                        echo '<td>' . $row4['date'] . '</td>';
                                        echo '<td>' . $row4['category'] . '</td>';
                                        echo '<td>' . $row4['building'] . " / " . $row4['floor'] . " / " . $row4['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['building'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['floor'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['room'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['images'] . '</td>';
                                        echo '<td >' . $row4['status'] . '</td>';
                                        echo '<td style="display: none;">' . $row4['assignedBy'] . '</td>';
                                        if (empty($row4['assignedName'])) {
                                            // Pagwalang data eto ilalabas
                                            echo '<td>';
                                            echo '<form method="post" action="">';
                                            echo '<input type="hidden" name="assetId" value="' . $row4['assetId'] . '">';
                                            echo '<button type="button" class="btn btn-primary view-btn archive-btn" data-bs-toggle="modal" data-bs-target="#exampleModal5">Assign</button>';
                                            echo '</form>';
                                            echo '</td>';
                                        } else {
                                            // Pagmeron data eto ilalabas
                                            echo '<td>' . $row4['assignedName'] . '</td>';
                                        }
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
                    </div>
                </div>
            </main>
        </section>

        <section>
            <!--Modal sections-->
            <!--Modal for table 1-->
            <div class="modal-parent">
                <div class="modal modal-xl fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3" id="workingForm">
                                    <h5>Report Modal for Working</h5>

                                    <input type="hidden" name="edit">

                                    <div class="col-4">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="Building" class="form-label">Building:</label>
                                        <input type="text" class="form-control" id="building" name="building" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="floor" class="form-label">Floor:</label>
                                        <input type="text" class="form-control" id="floor" name="floor" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display: none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="" readonly />
                                    </div>

                                    <div class="col-4" style="display: none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="" readonly />
                                    </div>
                                </form>
                            </div>

                            <div class="footer">
                                <button type="button" class="btn add-modal-btn">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit for table 1-->
            <!-- <div class="modal" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="edit" data-bs-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->


            <!--Modal for table 2-->
            <div class="modal-parent">
                <div class="modal modal-xl fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3" id="maintenanceForm">
                                    <h5>Report Modal for Under Maintenance</h5>

                                    <input type="hidden" name="edit">

                                    <div class="col-4">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="building" class="form-label">Building:</label>
                                        <input type="text" class="form-control" id="building" name="building" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="floor" class="form-label">Floor:</label>
                                        <input type="text" class="form-control" id="floor" name="floor" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display: none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="" readonly />
                                    </div>

                                    <div class="col-4" style="display: none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="" readonly />
                                    </div>
                                </form>
                            </div>

                            <div class="footer">
                                <button type="button" class="btn add-modal-btn">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit for table 2-->
            <!-- <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="edit" data-bs-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->



            <!--Modal for table 3-->
            <div class="modal-parent">
                <div class="modal modal-xl fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3" id="replacementForm">
                                    <h5>Report Modal for Replacement</h5>

                                    <input type="hidden" name="edit">

                                    <div class="col-4">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="building" class="form-label">Building:</label>
                                        <input type="text" class="form-control" id="building" name="building" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="floor" class="form-label">Floor:</label>
                                        <input type="text" class="form-control" id="floor" name="floor" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display: none">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" value="" readonly />
                                    </div>

                                    <div class="col-4" style="display: none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" value="" readonly />
                                    </div>
                                </form>
                            </div>

                            <div class="footer">
                                <button type="button" class="btn add-modal-btn">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit for table 3-->
            <!-- <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="edit" data-bs-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->



            <!--Modal for table 4-->
            <div class="modal-parent">
                <div class="modal modal-xl fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3" id="repairForm">
                                    <h5>Report Modal for Repair</h5>

                                    <input type="hidden" name="edit">

                                    <div class="col-4">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="building" class="form-label">Building:</label>
                                        <input type="text" class="form-control" id="building" name="building" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="floor" class="form-label">Floor:</label>
                                        <input type="text" class="form-control" id="floor" name="floor" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-4">
                                        <label for="assignedName" class="form-label">Assigned Name:</label>
                                        <input type="text" class="form-control" id="assignedName" name="assignedName" readonly />
                                    </div>

                                    <div class="col-4">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" readonly />
                                    </div>
                                </form>
                            </div>

                            <div class="footer">
                                <button type="button" class="btn add-modal-btn" onclick="confirmAlert('repair')">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit for table 4-->
            <!-- <div class="modal fade" id="staticBackdrop4" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="edit" data-bs-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->


            <!--Assign Modal for table 4-->
            <div class="modal-parent">
                <div class="modal modal-xl fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content assingee-container">
                            <div class="assignee-header">
                                <label for="assignedName" class="form-label assignee-tag">CHOOSE A MAINTENANCE PERSONNEL: </label>
                            </div>
                            <div class="header">
                                <button class="btn btn-close-modal-emp close-modal-btn" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="row g-3" id="assignPersonnelForm">
                                    <h5></h5>

                                    <input type="hidden" name="assignMaintenance">

                                    <div class="col-4" style="display:none">
                                        <label for="assetId" class="form-label">Tracking #:</label>
                                        <input type="text" class="form-control" id="assetId" name="assetId" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="date" class="form-label">Date:</label>
                                        <input type="text" class="form-control" id="date" name="date" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="category" class="form-label">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="building" class="form-label">Building:</label>
                                        <input type="text" class="form-control" id="building" name="building" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="floor" class="form-label">Floor:</label>
                                        <input type="text" class="form-control" id="floor" name="floor" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="room" class="form-label">Room:</label>
                                        <input type="text" class="form-control" id="room" name="room" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="images" class="form-label">Images:</label>
                                        <input type="text" class="form-control" id="" name="images" readonly />
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="status" class="form-label">Status:</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Working">Working</option>
                                            <option value="Under Maintenance">Under Maintenance</option>
                                            <option value="For Replacement">For Replacement</option>
                                            <option value="Need Repair">Need Repair</option>
                                        </select>
                                    </div>

                                    <div class="col-6">
                                        <select class="form-select assignedName" id="assignedName" name="assignedName" style="color: black;">
                                            <?php
                                            // Assuming you have a database connection established in $conn
                                            // SQL to fetch personnel with the role of "Maintenance Personnel"
                                            $assignSql = "SELECT firstName, middleName, lastName FROM account WHERE userlevel = '3'";
                                            $personnelResult = $conn->query($assignSql);


                                            if ($personnelResult) {
                                                while ($row = $personnelResult->fetch_assoc()) {
                                                    $fullName = $row['firstName'] . ' ' . $row['middleName'] . ' ' . $row['lastName'];
                                                    // Echo each option within the select
                                                    echo '<option value="' . htmlspecialchars($fullName) . '">' . htmlspecialchars($fullName) . '</option>';
                                                }
                                            } else {
                                                // Handle potential errors or no results
                                                echo '<option value="No Maintenance Personnel Found">';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-4" style="display:none">
                                        <label for="assignedBy" class="form-label">Assigned By:</label>
                                        <input type="text" class="form-control" id="assignedBy" name="assignedBy" readonly />
                                    </div>
                                </form>
                            </div>

                            <div class="footer">
                                <button type="button" class="btn add-modal-btn" onclick="assignPersonnel()">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit for table 4-->
            <!-- <div class="modal fade" id="staticBackdrop5" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-footer">
                            Are you sure you want to save changes?
                            <div class="modal-popups">
                                <button type="button" class="btn close-popups" data-bs-dismiss="modal">No</button>
                                <button class="btn add-modal-btn" name="assignMaintenance" data-bs-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
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

        <script src="../../src/js/main.js"></script>
        <script src="../../src/js/archive.js"></script>
        <script src="../../src/js/profileModalController.js"></script>
        <script src="../../src/js/reports.js"></script>
        <!-- Add this script after your existing scripts -->
        <!-- Add this script after your existing scripts -->



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

        <script>
            $(document).ready(function() {
                // Function to populate the modal fields
                function populateModal(row, modalId) {
                    $(modalId + " #assetId").val(row.find("td:eq(0)").text());
                    $(modalId + " #date").val(row.find("td:eq(1)").text());
                    $(modalId + " #category").val(row.find("td:eq(2)").text());
                    $(modalId + " #building").val(row.find("td:eq(4)").text());
                    $(modalId + " #floor").val(row.find("td:eq(5)").text());
                    $(modalId + " #room").val(row.find("td:eq(6)").text());
                    $(modalId + " #images").val(row.find("td:eq(7)").text());
                    $(modalId + " #status").val(row.find("td:eq(8)").text());
                    $(modalId + " #assignedBy").val(row.find("td:eq(9)").text());
                    $(modalId + " #assignedName").val(row.find("td:eq(10)").text());
                }

                // Event delegation for dynamically loaded content
                // For "Working" tab table rows
                $(document).on("click", "#pills-manager .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal");
                    $("#exampleModal").modal("show");
                });

                // For "Under Maintenance" tab table rows
                $(document).on("click", "#pills-profile .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal2");
                    $("#exampleModal2").modal("show");
                });

                $(document).on("click", "#pills-replace .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal3");
                    $("#exampleModal3").modal("show");
                });

                $(document).on("click", "#pills-repair .table-container table tbody tr", function() {
                    var row = $(this);
                    populateModal(row, "#exampleModal4");
                    $("#exampleModal4").modal("show");
                });

                //PARA TO SA PAGASSIGN MODAL
                $(document).on("click", "#pills-repair .view-btn", function(event) {
                    event.stopPropagation(); // Prevent the click from reaching the parent <tr>

                    // Get the closest parent row of the clicked button
                    var row = $(this).closest("tr");

                    // Populate the modal with data from the row
                    $("#exampleModal5 #assetId").val(row.find("td:eq(0)").text());
                    $("#exampleModal5 #date").val(row.find("td:eq(1)").text());
                    $("#exampleModal5 #category").val(row.find("td:eq(2)").text());
                    $("#exampleModal5 #building").val(row.find("td:eq(4)").text());
                    $("#exampleModal5 #floor").val(row.find("td:eq(5)").text());
                    $("#exampleModal5 #room").val(row.find("td:eq(6)").text());
                    $("#exampleModal5 #images").val(row.find("td:eq(7)").text());
                    $("#exampleModal5 #status").val(row.find("td:eq(8)").text()).change();
                    $("#exampleModal5 #assignedBy").val(row.find("td:eq(9)").text());
                    $("#exampleModal5 #assignedName").val(row.find("td:eq(10)").text());

                    // Finally, show the modal
                    $("#exampleModal5").modal("show");
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


    </body>

    </html>