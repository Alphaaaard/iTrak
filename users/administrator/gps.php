<?php
session_start();

include_once("../../config/connection.php");

$conn = connection();
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 


if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {


    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 1) {
        // If not personnel, redirect to an error page or login
        header("Location:error.php");
        exit;
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
               WHERE al.tab='Report' AND al.seen = '0' AND al.accountID != ?
               ORDER BY al.date DESC 
               LIMIT 5"; // Set limit to 5

    // Prepare the SQL statement
    $stmtLatestLogs = $conn->prepare($sqlLatestLogs);

    // Bind the parameter to exclude the current user's account ID
    $stmtLatestLogs->bind_param("i", $loggedInAccountId);

    // Execute the query
    $stmtLatestLogs->execute();
    $resultLatestLogs = $stmtLatestLogs->get_result();


    $unseenCountQuery = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE seen = '0' AND accountID != ?";
    $stmt = $conn->prepare($unseenCountQuery);
    $stmt->bind_param("i", $loggedInAccountId);
    $stmt->execute();
    $stmt->bind_result($unseenCount);
    $stmt->fetch();
    $stmt->close();


?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>iTrak | GPS</title>
        <!-- BOOTSTRAP -->
        <link rel="icon" type="image/x-icon" href="../../src/img/tab-logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- LEAFLET -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://kit.fontawesome.com/64b2e81e03.js" crossorigin="anonymous"></script>
        <!-- CSS -->
        <link rel="stylesheet" href="../../src/css/main.css" />
        <link rel="stylesheet" href="../../src/css/gps.css" />
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
                <li class="active">
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
                        <!-- <h1 class="tab-name-only">GPS</h1> -->
                        <div id="LocBurger" onclick="showLocation()"><i class="bi bi-list"></i></div>
                    </div>
                </header>
                <div class="content-container">
                    <div class="locationTbl" id="locationTbl">
                        <?php
                        include_once("../../config/connection.php");
                        $conn = connection();
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $currentDate = date('Y-m-d');

                        $sql = "SELECT al.*, a.firstName, a.latitude, a.lastName, a.longitude, a.timestamp, a.color, a.picture
                        FROM attendancelogs AS al
                        LEFT JOIN account AS a ON al.accountID = a.accountID
                        WHERE date = '$currentDate' AND (al.timeOut IS NULL OR al.timeOut = '') AND a.role = 'Maintenance Personnel'";


                        $result = $conn->query($sql);

                        // Display the user table
                        if ($result->num_rows > 0) {
                            echo "<div class='accordion'id='accordionGPS'>";
                            echo "<div class='fake-header'>";

                            echo "<p>NAME</p>";

                            // echo "<p>Location</p>";

                            echo "</div>";

                            while ($row = $result->fetch_assoc()) {

                                $accountId = $row["accountId"];
                                $firstName = $row["firstName"];
                                $lastName = $row["lastName"];
                                $collapseId = "collapse" . $accountId;
                                $headerId = "heading" . $accountId;


                                // Accordion item
                                echo "<div class='gps-container'>";

                                echo "<div class='accordion-item'>";
                                echo "<h2 class='accordion-header' id='" . $headerId . "'>";
                                echo "<button class='accordion-btn gps-info' type='button' data-bs-toggle='collapse' data-bs-target='#" . $collapseId . "' aria-expanded='false' aria-controls='" . $collapseId . "' data-firstName='" . $firstName . "'>";
                                echo "<img src='data:image/jpeg;base64," . base64_encode($row["picture"]) . "' alt='Profile Picture' class='rounded-img'/>";
                                echo "<span style='color: " . $row["color"] . ";'><i class='bi bi-circle-fill'></i></span>";
                                echo htmlspecialchars($firstName . " " . $lastName);
                                echo "</button>";
                                echo "</h2>";
                                echo "<div id='" . $collapseId . "' class='accordion-collapse collapse' aria-labelledby='" . $headerId . "' data-bs-parent='#accordionGPS'>"; // Ensure this points to the main container ID
                                echo "<div class='accordion-body'>";
                                echo "Latitude: " . $row["latitude"] . "<br>";
                                echo "Longitude: " . $row["longitude"] . "<br>";
                                echo "Timestamp: " . $row["timestamp"];
                                echo "</div>"; // End of accordion body
                                echo "</div>"; // End of accordion collapse
                                echo "</div>"; // End of accordion item
                                echo "</div>"; // End of accordion item
                            }
                            echo "</div>"; // Close the main container for the accordion
                        } else {
                            echo "No users found.";
                        }
                        ?>
                    </div>
                    <div id="map">
                        <!-- User Table Section -->
                        <!-- End of User Table Section -->
                        <script>
                            var map;
                            var markers = [];
                            var initialCoordinates = [14.70040, 121.03362];
                            var zoomLevel = 18.2;
                            var dragTimeout;

                            function initMap() {
                                map = L.map("map", {
                                    center: initialCoordinates,
                                    zoom: zoomLevel,
                                });

                                L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                                    maxZoom: 18.2,
                                    minZoom: 2,
                                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                                }).addTo(map);

                                // Reset view to initial coordinates after delay during drag
                                map.on('dragstart', function() {
                                    clearTimeout(dragTimeout);
                                });

                                map.on('drag', function() {
                                    clearTimeout(dragTimeout);
                                    dragTimeout = setTimeout(function() {
                                        map.setView(initialCoordinates, zoomLevel);
                                    }, 1000); // 1 second delay
                                });

                                // Reset view to initial coordinates after zoom
                                map.on('zoom', function() {
                                    map.setView(initialCoordinates, zoomLevel);
                                });
                            }

                            function coloredIcon(color) {
                                return L.divIcon({
                                    className: 'custom-marker',
                                    iconSize: [20, 20],
                                    html: '<div style="background-color: ' + color + '; width: 20px; height: 20px; border-radius: 50%;"></div>',
                                });
                            }

                            var markersByFirstName = {};

                            function updateMarkers(locations) {
                                // Process the locations and update/create markers
                                locations.forEach(function(location) {
                                    var latitude = location.latitude;
                                    var longitude = location.longitude;
                                    var firstName = location.firstName;
                                    var color = location.color || "black";

                                    var locationName;
                                    // Determine the locationName based on the latitude and longitude

                                    // Check if a marker with this firstName already exists
                                    var existingMarker = markersByFirstName[firstName];

                                    if (existingMarker) {
                                        // If the marker exists, update its position and popup
                                        existingMarker.setLatLng([latitude, longitude]);
                                        existingMarker.bindPopup("Personnel: " + firstName);
                                    } else {
                                        // If the marker doesn't exist, create a new one
                                        var newMarker = L.marker([latitude, longitude], {
                                            icon: coloredIcon(color),
                                        }).addTo(map);

                                        newMarker.bindPopup("Personnel: " + firstName);
                                        markersByFirstName[firstName] = newMarker;
                                    }

                                    // Update the location in the user table
                                    var locationCell = document.getElementById('location_' + firstName);
                                    if (locationCell) {
                                        locationCell.innerHTML = locationName;
                                    }
                                });
                            }

                            function showMarker(firstName) {
                                console.log("Clicked on:", firstName);

                                // Retrieve the marker based on the firstName from the markersByFirstName object
                                var marker = markersByFirstName[firstName];

                                // If the marker exists
                                if (marker) {
                                    // Zoom to the marker and open its popup
                                    map.setView(marker.getLatLng(), zoomLevel);
                                    marker.openPopup();
                                } else {
                                    console.error("Marker not found for:", firstName);
                                }
                            }



                            function getLocationFromDatabase() {
                                // Fetch the locations from the server
                                var xmlhttp = new XMLHttpRequest();
                                xmlhttp.onreadystatechange = function() {
                                    if (this.readyState == 4 && this.status == 200) {
                                        var locations = JSON.parse(this.responseText);

                                        if (locations && locations.length > 0) {
                                            // Update the map with the new locations
                                            updateMarkers(locations);

                                            // Log locations to the console
                                            console.log("Locations:", locations);
                                        } else {
                                            // Handle case where no location data is available
                                            console.error("No location data available");
                                        }
                                    }
                                };

                                xmlhttp.open("GET", "get_location.php", true);
                                xmlhttp.send();
                            }


                            // Initialize the map when the page loads
                            window.onload = function() {
                                initMap();
                                getLocationFromDatabase();
                                // Refresh location every 1 minute
                                setInterval(getLocationFromDatabase, 1000); // 1 seconds
                            };
                        </script>

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
                        <style>
                            .custom-marker {
                                width: 20px;
                                height: 20px;
                                border-radius: 50%;
                            }
                        </style>
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
        <script src="../../src/js/gps.js"></script>
        <script src="../../src/js/profileModalController.js"></script>

        <script>
            // Assuming showMarker is defined elsewhere to handle the map logic
            document.querySelectorAll('.gps-info').forEach(function(button) {
                button.addEventListener('click', function() {
                    var firstName = this.getAttribute('data-firstName');
                    showMarker(firstName); // Call the showMarker function with the clicked person's first name
                });
            });
        </script>

        <script>
            var accordionButtons = document.querySelectorAll('.gps-info');
            accordionButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var firstName = this.getAttribute('data-firstName');
                    showMarker(firstName); // Call the showMarker function with the clicked person's first name
                });
            });
        </script>

        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <!-- SCRIPTS -->
    </body>

    </html>