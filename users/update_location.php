<?php
session_start();
include_once("../config/connection.php");

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {
    date_default_timezone_set('Asia/Manila');
    $conn = connection();

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (isset($_GET['lat']) && isset($_GET['lng'])) {
            $newLatitude = $_GET['lat'];
            $newLongitude = $_GET['lng'];
            $user_id = $_SESSION['accountId'];

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Begin transaction
            $conn->begin_transaction();

            // Retrieve the current location
            $selectQuery = "SELECT latitude, longitude, timestamp, qculocation FROM account WHERE accountId=?";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param("i", $user_id);
            $selectStmt->execute();
            $result = $selectStmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $oldLatitude = $row['latitude'];
                $oldLongitude = $row['longitude'];
                $oldTimestamp = $row['timestamp'];
                $oldQcLocation = $row['qculocation']; // Fetch the qculocation
            
                if ($oldLatitude != 0 && $oldLongitude != 0) {
                    // Insert the old location and qculocation into the locationhistory table
                    $insertLocationQuery = "INSERT INTO locationhistory (accountId, latitude, longitude, timestamp, qculocation) VALUES (?, ?, ?, ?, ?)";
                    $insertLocationStmt = $conn->prepare($insertLocationQuery);
                    if (!$insertLocationStmt) {
                        // Handle the error appropriately
                        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                        // Rollback if needed
                        $conn->rollback();
                        exit();
                    }
                    // Bind the qculocation parameter
                    $insertLocationStmt->bind_param("iddss", $user_id, $oldLatitude, $oldLongitude, $oldTimestamp, $oldQcLocation);
                    if (!$insertLocationStmt->execute()) {
                        // Handle the error appropriately
                        echo "Execute failed: (" . $insertLocationStmt->errno . ") " . $insertLocationStmt->error;
                        $conn->rollback();
                        exit();
                    }
                }


                // Update the new location in the account table
                $updateLocationQuery = "UPDATE account SET latitude=?, longitude=?, timestamp=CURRENT_TIMESTAMP WHERE accountId=?";
                $updateLocationStmt = $conn->prepare($updateLocationQuery);
                $updateLocationStmt->bind_param("ddi", $newLatitude, $newLongitude, $user_id);
                $updateLocationStmt->execute();

                // Commit transaction
                $conn->commit();
                echo "Location updated successfully!";
            } else {
                echo "No existing location data found!";
            }
        } else {
            echo "Latitude and longitude parameters are required!";
        }
    } else {
        echo "Invalid request method!";
    }
    $conn->close();
} else {
    echo "Session variables not set!";
}
?>
