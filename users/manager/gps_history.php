<?php
session_start();
include_once("../../config/connection.php");
date_default_timezone_set('Asia/Manila'); //need ata to sa lahat ng page para sa security hahah 
$conn = connection();

if (isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {
    // For personnel page, check if userLevel is 3
    if ($_SESSION['userLevel'] != 2) {
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
               WHERE  al.seen = '0' AND al.accountID != ?
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
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

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
        <link rel="stylesheet" href="../../src/css/gps-history.css" />
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
            <a href="./dashboard.php" class="brand" title="logo">
                <i><img src="../../src/img/UpKeep.png" alt="" class="logo" /></i>
                <div class="mobile-sidebar-close">
                    <i class="bi bi-arrow-left-circle"></i>
                </div>
            </a>
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
                <div class="GPS-cont" onclick="toggleGPS()">
                    <li class="GPS-dropdown active">
                        <div class="GPS-drondown-content">
                            <div class="GPS-side-cont">
                                <i class="bi bi-geo-alt"></i>
                                <span class="text">GPS</span>
                            </div>
                            <div class="GPS-ind">
                                <i id="chevron-icon" class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                    </li>
                </div>
                <div class="GPS-container aaa">
                    <li class="GPS-Tracker">
                        <a href="./gps.php">
                            <i class="bi bi-crosshair"></i>
                            <span class="text">GPS Tracker</span>
                        </a>
                    </li>
                    <li class="GPS-History active">
                        <a href="./gps_history.php">
                            <i class="bi bi-radar"></i>
                            <span class="text">GPS History</span>
                        </a>
                    </li>
                </div>
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
                <div class="Map-cont" onclick="toggleMAP()">
                    <li class="Map-dropdown">
                        <div class="Map-drondown-content">
                            <div class="Map-side-cont">
                                <i class="bi bi-receipt"></i>
                                <span class="text">Request</span>
                            </div>
                            <div class="Map-ind">
                                <i id="map-chevron-icon" class="bi bi-chevron-down"></i>
                            </div>
                        </div>
                    </li>
                </div>
                <div class="Map-container">
                    <li class="Map-Batasan">
                        <a href="./batasan.php">
                            <i class="bi bi-building"></i>
                            <span class="text">Batasan</span>
                        </a>
                    </li>
                    <li class="Map-SanBartolome">
                        <a href="./sanBartolome.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Bartolome</span>
                        </a>
                    </li>
                    <li class="Map-SanFrancisco">
                        <a href="./sanFrancisco.php">
                            <i class="bi bi-building"></i>
                            <span class="text">San Francisco</span>
                        </a>
                    </li>
                </div>
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
                        <!-- <div id="LocBurger" onclick="showLocation()"><i class="bi bi-list"></i></div> -->
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
                                echo "<button class='accordion-btn gps-info' type='button' data-bs-toggle='collapse' data-bs-target='#" . $collapseId . "' aria-expanded='false' aria-controls='" . $collapseId . "' data-firstName='" . $firstName . "' data-accountId='" . $accountId . "'>";
                                echo "<img src='data:image/jpeg;base64," . base64_encode($row["picture"]) . "' alt='Profile Picture' class='rounded-img' data-accountId='" . $accountId . "' />";
                                echo "</button>";
                                echo "</h2>";
                                echo "<div id='" . $collapseId . "' class='accordion-collapse collapse' aria-labelledby='" . $headerId . "' data-bs-parent='#accordionGPS'>"; // Ensure this points to the main container ID
                                echo "<div class='accordion-body'>";

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
                            var initialCoordinates = [14.70040, 121.03299];
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

                                // Add custom text overlay for the Bautista Building directly to the map
                                var bautistaBuildingText = L.marker([14.70065, 121.03241], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>Bautista Building</div>'
                                    })
                                }).addTo(map);

                                var AdminBuildingText = L.marker([14.70048, 121.03285], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>Admin Building</div>'
                                    })
                                }).addTo(map);

                                var OldAcademicBuildingText = L.marker([14.70059, 121.03327], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>Old Academic Building</div>'
                                    })
                                }).addTo(map);

                                var BelmonteBuildingText = L.marker([14.70090, 121.03305], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>Belmonte Building</div>'
                                    })
                                }).addTo(map);

                                var TechVocBuildingText = L.marker([14.70025, 121.03363], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>TechVoc Building</div>'
                                    })
                                }).addTo(map);

                                var BallroomBuildingText = L.marker([14.70057, 121.03379], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>Ballroom Building</div>'
                                    })
                                }).addTo(map);

                                var MultiBuildingText = L.marker([14.70047, 121.03396], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>Multipurpose Building</div>'
                                    })
                                }).addTo(map);

                                var NewAcadBuildingText = L.marker([14.70112, 121.03265], {
                                    icon: L.divIcon({
                                        className: 'custom-text-overlay',
                                        html: '<div>New Academic Building</div>'
                                    })
                                }).addTo(map);

                                // Define the coordinates for the rectangle vertices
                                var rectangleVertices = [
                                    [14.70104 - 0.00007, 121.03274 - 0.0002], // Lower left corner
                                    [14.70104 - 0.00007, 121.03274 + 0.0002], // Upper left corner
                                    [14.70104 + 0.00007, 121.03274 + 0.0002], // Upper right corner
                                    [14.70104 + 0.00007, 121.03274 - 0.0002] // Lower right corner
                                ];

                                // Define the rotation angle in degrees
                                var rotationAngleDegrees = 20;

                                // Convert the rotation angle to radians
                                var rotationAngleRadians = rotationAngleDegrees * (Math.PI / 180);

                                // Rotate each vertex by the specified angle around the center of the rectangle
                                var center = [(rectangleVertices[0][0] + rectangleVertices[2][0]) / 2, (rectangleVertices[0][1] + rectangleVertices[2][1]) / 2];
                                var rotatedVertices = rectangleVertices.map(function(vertex) {
                                    var dx = vertex[0] - center[0];
                                    var dy = vertex[1] - center[1];
                                    var newX = center[0] + dx * Math.cos(rotationAngleRadians) - dy * Math.sin(rotationAngleRadians);
                                    var newY = center[1] + dx * Math.sin(rotationAngleRadians) + dy * Math.cos(rotationAngleRadians);
                                    return [newX, newY];
                                });


                                // Create a polygon using the rotated vertices
                                var rotatedRectangle = L.polygon(rotatedVertices, {
                                    color: "#d9d0c9",
                                    fillColor: "#d9d0c9", // Set the fill color
                                    fillOpacity: 1 // Optionally set the fill opacity
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

                            // Modify coloredIcon function to accept isFirst parameter
                            function coloredIcon(color, isFirst) {
                                let markerColor = color; // default color

                                // If it's the first marker, set the color to white
                                if (isFirst) {
                                    markerColor = "Green";
                                }

                                return L.divIcon({
                                    className: 'custom-marker',
                                    iconSize: [20, 20],
                                    html: '<div style="background-color: ' + markerColor + '; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white;"></div>',
                                });
                            }

                            // Function to convert base64 string to Blob object
                            function base64ToBlob(base64String) {
                                const byteCharacters = atob(base64String);
                                const byteArray = new Uint8Array(byteCharacters.length);
                                for (let i = 0; i < byteCharacters.length; i++) {
                                    byteArray[i] = byteCharacters.charCodeAt(i);
                                }
                                return new Blob([byteArray], {
                                    type: 'image/jpeg'
                                }); // Adjust the type as per your image type
                            }

                            // Function to convert blob data to base64 string
                            function blobToBase64(blob) {
                                return new Promise((resolve, reject) => {
                                    const reader = new FileReader();
                                    reader.onloadend = () => {
                                        resolve(reader.result);
                                    };
                                    reader.onerror = reject;
                                    reader.readAsDataURL(blob);
                                });
                            }


                            var markersByFirstName = {};
                            var markers = [];
                            var polyline;
                            var polylinesByAccountId = {};

                            async function updateMarkers(locations) {
                                markers.forEach(marker => {
                                    map.removeLayer(marker);
                                });
                                markers = [];

                                // Clear existing polylines
                                for (let accountId in polylinesByAccountId) {
                                    map.removeLayer(polylinesByAccountId[accountId]);
                                }
                                polylinesByAccountId = {};

                                // Organize locations by accountId
                                var locationsByAccountId = locations.reduce((acc, location) => {
                                    var accountId = location.accountId;
                                    if (!acc[accountId]) {
                                        acc[accountId] = [];
                                    }
                                    acc[accountId].push(location);
                                    return acc;
                                }, {});

                                // Process each accountId's locations
                                for (let accountId in locationsByAccountId) {
                                    let userLocations = locationsByAccountId[accountId];
                                    let latLngs = [];

                                    // Sort userLocations array by timestamp
                                    userLocations.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

                                    for (let location of userLocations) {
                                        let latitude = location.latitude;
                                        let longitude = location.longitude;
                                        let firstName = location.firstName;
                                        let qculocation = location.qculocation;
                                        let timestamp = location.timestamp;
                                        let picture = location.picture;

                                        let marker;
                                        if (location === userLocations[userLocations.length - 1]) {
                                            const pictureBlob = base64ToBlob(picture);
                                            const pictureBase64 = await blobToBase64(pictureBlob);

                                            marker = L.marker([latitude, longitude], {
                                                icon: L.divIcon({
                                                    className: 'custom-marker',
                                                    iconSize: [40, 20],
                                                    html: `<img src="${pictureBase64}" alt="Profile Picture" class="marker-img" />`
                                                }),
                                                location: qculocation
                                            });
                                        } else {
                                            var color = location.color || "black";
                                            marker = L.marker([latitude, longitude], {
                                                icon: coloredIcon(color),
                                            });
                                        }

                                        let popupContent = "Personnel: " + firstName + "<br>Location: " + qculocation + "<br>Timestamp: " + new Date(timestamp).toLocaleString();
                                        marker.bindPopup(popupContent);

                                        marker.on('mouseover', function(e) {
                                            this.openPopup();
                                        });

                                        marker.on('mouseout', function(e) {
                                            this.closePopup();
                                        });

                                        marker.addTo(map);
                                        markers.push(marker);
                                        latLngs.push([latitude, longitude]);
                                    }

                                    // Draw polyline for each user
                                    let polyline = L.polyline(latLngs, {
                                        color: userLocations[0].color || 'blue'
                                    }).addTo(map);
                                    polylinesByAccountId[accountId] = polyline; // Store polyline by accountId
                                }
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



                            function getLocationFromDatabase(selectedDate, accountId = null) {
                                var xmlhttp = new XMLHttpRequest();
                                xmlhttp.onreadystatechange = function() {
                                    if (this.readyState == 4 && this.status == 200) {
                                        var locations = JSON.parse(this.responseText);

                                        if (locations && locations.length > 0) {
                                            // Update the map with the new locations
                                            updateMarkers(locations);
                                            (locations);
                                        } else {
                                            // Handle case where no location data is available
                                            // Handle case where no location data is available
                                            console.error("No location data available");
                                        }
                                    }
                                };

                                var url = "get_location_history.php?date=" + encodeURIComponent(selectedDate);
                                if (accountId) {
                                    url += "&accountId=" + encodeURIComponent(accountId);
                                }

                                xmlhttp.open("GET", url, true);
                                xmlhttp.send();
                            }

                            function getLocationFromDatabaseIMG(accountId, selectedDate) {
                                if (!accountId) {
                                    clearMap(); // Clear the map if no accountId is selected
                                    console.log("No accountId provided for getLocationFromDatabase");
                                    return; // Exit the function if no accountId is provided
                                }

                                // Clear the map
                                clearMap();

                                var xmlhttp = new XMLHttpRequest();
                                xmlhttp.onreadystatechange = function() {
                                    if (this.readyState == 4 && this.status == 200) {
                                        var locations = JSON.parse(this.responseText);

                                        if (locations && locations.length > 0) {
                                            // Update the map with the new locations
                                            updateMarkers(locations);
                                        } else {
                                            // Handle case where no location data is available
                                            console.error("No location data available");
                                        }
                                    }
                                };

                                xmlhttp.open("GET", "get_location_history.php?accountId=" + encodeURIComponent(accountId) + "&date=" + encodeURIComponent(selectedDate), true);
                                xmlhttp.send();
                            }

                            function clearMapData() {
                                markers.forEach(marker => map.removeLayer(marker));
                                markers = [];

                                for (let accountId in polylinesByAccountId) {
                                    map.removeLayer(polylinesByAccountId[accountId]);
                                }
                                polylinesByAccountId = {};
                            }

                            // Initialize the map when the page loads
                            window.onload = function() {
                                initMap();

                                // Get current date for default selection
                                var currentDate = new Date();
                                var currentDateString = currentDate.toISOString().slice(0, 10); // Format as 'YYYY-MM-DD'

                                // Initial fetch using current date
                                getLocationFromDatabase(null, currentDateString);
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
                    <!-- Calendar container -->
                    <div class="calendar-container" id="calendar-container">
                        <div class="calendar">
                            <div class="calendar-header">
                                <div class="reset-container">
                                    <button type="button" class="reset-btn">RESET</button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="cal-btn" onclick="prevMonth()">
                                        &lt;
                                    </button>
                                </div>
                                <div class="date-container">
                                    <h5 class="day-text" id="currentDay"></h5>
                                    <h5 class="month-year-text" id="currentMonthYear"></h5>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="cal-btn" onclick="nextMonth()">></button>
                                </div>
                            </div>
                            <div class="calendar-body" id="calendarBody">
                                <table class="table table-bordered calendar-table">
                                    <thead>
                                        <tr>
                                            <th>Sun</th>
                                            <th>Mon</th>
                                            <th>Tue</th>
                                            <th>Wed</th>
                                            <th>Thu</th>
                                            <th>Fri</th>
                                            <th>Sat</th>
                                        </tr>
                                    </thead>
                                    <tbody id="calendarContent">
                                        <!-- Calendar body content will be generated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
        <script src="../../src/js/logout.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const calendarContainer = document.getElementById("calendar-container");

                calendarContainer.addEventListener("click", function() {
                    calendarContainer.classList.toggle("active");
                });
            });
        </script>

        <script>
            var currentDate = new Date();
            var currentMonth = currentDate.getMonth();
            var currentYear = currentDate.getFullYear();
            var previousClickedDate = null;
            var selectedAccountId = null;

            function generateCalendar(month, year) {
                // Update the currentDate variable
                var currentDate = new Date(year, month, 1);

                // Update the currentMonth and currentYear variables
                var currentMonth = currentDate.getMonth();
                var currentYear = currentDate.getFullYear();

                var calendarContent = document.getElementById('calendarContent');
                var daysInMonth = new Date(year, month + 1, 0).getDate();

                var firstDay = new Date(year, month, 1).getDay();
                var lastDay = new Date(year, month, daysInMonth).getDay();

                var currentDay = 1; // Start from the first day of the month
                var html = '';

                while (currentDay <= daysInMonth) {
                    html += '<tr>';

                    for (var i = 0; i < 7; i++) {
                        if (currentDay > 0 && currentDay <= daysInMonth) {
                            var selectedDate = currentYear + '-' + (currentMonth + 1).toString().padStart(2, '0') + '-' + currentDay.toString().padStart(2, '0');
                            var hasData = checkDataAvailability(selectedDate, selectedAccountId); // Check if data is available for the selected date and user
                            var isCurrentDay = currentYear === new Date().getFullYear() && currentMonth === new Date().getMonth() && currentDay === new Date().getDate();

                            if (isCurrentDay) {
                                html += '<td class="days day-' + currentDay + ' current-day">' + currentDay + '</td>';
                            } else if (hasData) {
                                html += '<td class="days day-' + currentDay + ' has-data">' + currentDay + '</td>'; // Add 'has-data' class if data is available
                            } else {
                                html += '<td class="days day-' + currentDay + '">' + currentDay + '</td>';
                            }
                        } else {
                            html += '<td class="unselectable"></td>'; // Adding unselectable attribute
                        }
                        currentDay++;
                    }

                    html += '</tr>';
                }

                calendarContent.innerHTML = html;
                document.getElementById('currentMonthYear').textContent = getMonthName(month) + ' ' + year;

                // Call updateCurrentDay to initially set the current day
                updateCurrentDay();

                // Add event listener to days
                var days = document.querySelectorAll('.days');
                // Inside your generateCalendar function or wherever you're setting up the click event listener for the calendar dates
                // Modify the event listener for calendar days
                days.forEach(function(day) {
                    day.addEventListener('click', function() {
                        var selectedDay = parseInt(day.textContent);
                        var selectedDate = new Date(currentYear, currentMonth, selectedDay);
                        var formattedDate = selectedDate.getFullYear() + '-' + (selectedDate.getMonth() + 1).toString().padStart(2, '0') + '-' + selectedDate.getDate().toString().padStart(2, '0');

                        // Call the updateCurrentDay function to update the displayed date
                        updateCurrentDay(formattedDate);

                        // Your existing code for fetching location data and updating clicked date class
                        clearMapData();

                        if (selectedAccountId) {
                            getLocationFromDatabase(formattedDate, selectedAccountId);
                        } else {
                            getLocationFromDatabase(formattedDate);
                        }

                        if (previousClickedDate) {
                            previousClickedDate.classList.remove('clicked-date');
                        }

                        day.classList.add('clicked-date');
                        previousClickedDate = day;
                    });
                });


            }


            function updateCurrentDay(selectedDate) {
                // Get the current day element
                var currentDayElement = document.getElementById('currentDay');

                // Parse the selected date to ensure it's in the correct format
                var selectedDateObj = new Date(selectedDate);
                var selectedDay = selectedDateObj.getDate();

                // Check if the selected date is undefined (indicating the default current date)
                if (!selectedDate) {
                    var currentDate = new Date();
                    selectedDay = currentDate.getDate();

                    // Check if the displayed month is the same as the current month
                    if (currentMonth === currentDate.getMonth() && currentYear === currentDate.getFullYear()) {
                        currentDayElement.textContent = selectedDay.toString().padStart(2, '0'); // Display current day
                    } else {
                        currentDayElement.textContent = '01'; // Display the first day of the month
                    }
                } else {
                    // Update the current day element with the selected date
                    currentDayElement.textContent = selectedDay.toString().padStart(2, '0');

                    // Check if the displayed month is the same as the selected month
                    if (currentMonth === selectedDateObj.getMonth() && currentYear === selectedDateObj.getFullYear()) {
                        currentDayElement.textContent = selectedDay.toString().padStart(2, '0'); // Display selected day
                    } else {
                        currentDayElement.textContent = '01'; // Display the first day of the month
                    }
                }
            }




            function checkDataAvailability(selectedDate, accountId) {
                var xmlhttp = new XMLHttpRequest();
                var url = "get_location_history.php?date=" + encodeURIComponent(selectedDate);
                if (accountId) {
                    url += "&accountId=" + encodeURIComponent(accountId);
                }
                xmlhttp.open("GET", url, false); // Synchronous request
                xmlhttp.send();

                if (xmlhttp.status == 200) {
                    var locations = JSON.parse(xmlhttp.responseText);
                    return locations.length > 0; // Return true if data exists, false otherwise
                } else {
                    console.error("Error fetching data for date: " + selectedDate);
                    return false; // Error occurred or no data available
                }
            }

            function prevMonth() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                generateCalendar(currentMonth, currentYear);
            }

            function nextMonth() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                generateCalendar(currentMonth, currentYear);
            }

            function getMonthName(month) {
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                return months[month];
            }

            // Initial generation of the calendar
            generateCalendar(currentMonth, currentYear);

            // Event listener for clicking on a user's image
            document.body.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('rounded-img')) {
                    const clickedAccountId = e.target.getAttribute('data-accountId');

                    if (selectedAccountId === clickedAccountId) {
                        // If clicked on the same image again, reset the map
                        selectedAccountId = null;
                        generateCalendar(currentMonth, currentYear); // Refresh the calendar
                        fetchTodaysLocations(); // Fetch all accounts for today
                    } else {
                        // Otherwise, filter the map by the clicked account ID
                        selectedAccountId = clickedAccountId;
                        generateCalendar(currentMonth, currentYear); // Refresh the calendar
                        const currentDate = new Date().toISOString().slice(0, 10); // 'YYYY-MM-DD'
                        getLocationFromDatabaseIMG(selectedAccountId, currentDate);
                    }
                }
            });

            function fetchTodaysLocations(accountId) {
                const date = new Date().toISOString().slice(0, 10); // Get current date in YYYY-MM-DD format
                const url = accountId ?
                    `get_location_history.php?accountId=${accountId}&date=${date}` :
                    `get_location_history.php?date=${date}`;

                fetch(url)
                    .then(response => response.json())
                    .then(locations => {
                        // Process the locations here
                        console.log(locations);
                        // For example, if you're updating markers on a map:
                        updateMarkers(locations);
                    })
                    .catch(error => {
                        console.error('Error fetching data: ', error);
                    });
            }

            // Initialize the map when the page loads
            window.onload = function() {
                initMap(); // Your function to initialize the map
                fetchTodaysLocations(); // Fetch for all accounts today
            };

            function clearMap() {
                // Assuming 'markers' is an array holding your marker instances
                markers.forEach(marker => map.removeLayer(marker));
                markers = []; // Clear the array

                // If you have a polyline, remove it as well
                if (polyline) {
                    map.removeLayer(polyline);
                    polyline = null; // Clear the polyline reference
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Event listener for the reset button
                document.querySelector('.reset-btn').addEventListener('click', function() {
                    // Reset the calendar to the default date
                    currentMonth = currentDate.getMonth();
                    currentYear = currentDate.getFullYear();
                    selectedAccountId = null; // Reset selected account ID
                    generateCalendar(currentMonth, currentYear);
                    clearMap(); // Clear the map markers and polyline
                    fetchTodaysLocations(); // Fetch locations for today and update the map
                });
            });
        </script>





        <!-- BOOTSTRAP -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- BOOTSTRAP -->
        <!-- SCRIPTS -->
    </body>

    </html>