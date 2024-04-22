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

                // Calculate the distance between old and new coordinates
                $distanceThreshold = 2; // Adjust this threshold as needed (in meters)
                $distance = calculateDistance($oldLatitude, $oldLongitude, $newLatitude, $newLongitude);

                if ($distance >= $distanceThreshold) {
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
                    $insertLocationStmt->bind_param("iddss", $user_id, $oldLatitude, $oldLongitude, $oldTimestamp, $oldQcLocation);
                    if (!$insertLocationStmt->execute()) {
                        // Handle the error appropriately
                        echo "Execute failed: (" . $insertLocationStmt->errno . ") " . $insertLocationStmt->error;
                        $conn->rollback();
                        exit();
                    }
                }

                // Update the new location in the account table with adjusted timestamp
                $updateLocationQuery = "UPDATE account SET latitude=?, longitude=?, timestamp=DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 8 HOUR) WHERE accountId=?";
                $updateLocationStmt = $conn->prepare($updateLocationQuery);
                $updateLocationStmt->bind_param("ddi", $newLatitude, $newLongitude, $user_id);
                if (!$updateLocationStmt->execute()) {
                    echo "Update failed: (" . $updateLocationStmt->errno . ") " . $updateLocationStmt->error;
                    $conn->rollback();
                    exit();
                }

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

// Function to calculate distance between two coordinates using Haversine formula
function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $R = 6371000; // Earth radius in meters
    $phi1 = deg2rad($lat1);
    $phi2 = deg2rad($lat2);
    $deltaPhi = deg2rad($lat2 - $lat1);
    $deltaLambda = deg2rad($lon2 - $lon1);

    $a = sin($deltaPhi / 2) * sin($deltaPhi / 2) + cos($phi1) * cos($phi2) * sin($deltaLambda / 2) * sin($deltaLambda / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $R * $c;

    return $distance;
}
