<?php
session_start();
include_once("../config/connection.php");

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {
    date_default_timezone_set('Asia/Manila');
    $conn = connection();

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (isset($_GET['lat']) && isset($_GET['lng']) && isset($_GET['qcuLocation']) && $_GET['qcuLocation'] !== "Outside of QCU") {
            $newLatitude = $_GET['lat'];
            $newLongitude = $_GET['lng'];
            $newQcLocation = $_GET['qcuLocation']; // The new location value
            $user_id = $_SESSION['accountId'];

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Begin transaction
            $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

            // Insert the new location into the locationhistory table
            $insertLocationQuery = "INSERT INTO locationhistory (accountId, latitude, longitude, timestamp, qculocation) VALUES (?, ?, ?, NOW(), ?)";
            $insertLocationStmt = $conn->prepare($insertLocationQuery);
            if (!$insertLocationStmt) {
                echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                $conn->rollback();
                exit();
            }
            $insertLocationStmt->bind_param("idds", $user_id, $newLatitude, $newLongitude, $newQcLocation);
            if (!$insertLocationStmt->execute()) {
                echo "Execute failed: (" . $insertLocationStmt->errno . ") " . $insertLocationStmt->error;
                $conn->rollback();
                exit();
            }

            // Update the new location in the account table with adjusted timestamp
            $updateLocationQuery = "UPDATE account SET latitude=?, longitude=?, timestamp=NOW(), qculocation=? WHERE accountId=?";
            $updateLocationStmt = $conn->prepare($updateLocationQuery);
            $updateLocationStmt->bind_param("ddsi", $newLatitude, $newLongitude, $newQcLocation, $user_id);
            if (!$updateLocationStmt->execute()) {
                echo "Update failed: (" . $updateLocationStmt->errno . ") " . $updateLocationStmt->error;
                $conn->rollback();
                exit();
            }

            // Commit transaction
            $conn->commit();
            echo "Location updated successfully!";
        } else {
            echo "Latitude, longitude, and qcuLocation parameters are required, and qcuLocation must not be 'Outside of QCU'.";
        }
    } else {
        echo "Invalid request method!";
    }
    $conn->close();
} else {
    echo "Session variables not set!";
}
?>
