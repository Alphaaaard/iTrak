    <?php
    include_once("../../config/connection.php");
    $conn = connection();
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set default timezone to Asia/Manila
    date_default_timezone_set('Asia/Manila');

    $currentDate = date('Y-m-d');

    $sql = "SELECT al.*, a.firstName, a.latitude, a.lastName, a.longitude, a.timestamp, a.qculocation, a.color, a.picture
            FROM attendancelogs AS al
            LEFT JOIN account AS a ON al.accountID = a.accountID
            WHERE date = '$currentDate' AND (al.timeOut IS NULL OR al.timeOut = '') AND a.role = 'Maintenance Personnel'";

    $result = $conn->query($sql);

    // Display the user table
    if ($result->num_rows > 0) {
        echo "<div class='accordion' id='accordionGPS'>";
        echo "<div class='fake-header'>";
        echo "<p>NAME</p>";
        echo "</div>";

        while ($row = $result->fetch_assoc()) {
            $accountId = $row["accountId"];
            $firstName = $row["firstName"];
            $lastName = $row["lastName"];
            $collapseId = "collapse" . $accountId;
            $headerId = "heading" . $accountId;
            $latitude = $row["latitude"];
            $longitude = $row["longitude"];


            // Convert timestamp to standard 12-hour time format and add 8 hours
            $timestamp = date('h:i A', strtotime($row["timestamp"] . ' +8 hours'));

            $status = ($latitude != 0 && $longitude != 0) ? 'Online' : 'Offline';
            // Accordion item
            echo "<div class='gps-container'>";
            echo "<div class='accordion-item'>";
            echo "<h2 class='accordion-header' id='" . $headerId . "'>";
            echo "<button class='accordion-btn gps-info' type='button' data-bs-toggle='collapse' data-bs-target='#" . $collapseId . "' aria-expanded='false' aria-controls='" . $collapseId . "' data-firstName='" . $firstName . "' data-accountId='" . $accountId . "'>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($row["picture"]) . "' alt='Profile Picture' class='rounded-img'/>";
            echo "<span style='color: " . $row["color"] . ";'><i class='bi bi-circle-fill'></i></span>";
            echo htmlspecialchars($firstName . " " . $lastName);
            echo "</button>";
            echo "</h2>";
            echo "<div id='" . $collapseId . "' class='accordion-collapse collapse' aria-labelledby='" . $headerId . "' data-bs-parent='#accordionGPS'>"; // Ensure this points to the main container ID
            echo "<div class='accordion-body'>";
            echo "Status: " . $status . "<br>";

            // Only display location if status is 'Online'
            if ($status === 'Online') {

                echo "Timestamp: " . $timestamp . "<br>";
                echo "Location: " . $row["qculocation"] . "<br>";
            }

            echo "</div>"; // End of accordion body
            echo "</div>"; // End of accordion collapse
            echo "</div>"; // End of accordion item
            echo "</div>"; // End of gps-container
        }
        echo "</div>"; // Close the main container for the accordion
    } else {
        echo "No users found.";
    }
