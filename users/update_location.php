<?php
session_start();
include_once("../config/connection.php");

if (isset($_SESSION['accountId']) && isset($_SESSION['email'])) {
    date_default_timezone_set('Asia/Manila');
    $conn = connection();

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (isset($_GET['lat']) && isset($_GET['lng'])) {
            $latitude = $_GET['lat'];
            $longitude = $_GET['lng'];
            $user_id = $_SESSION['accountId'];

            // Update the user's location in the account table and insert into locationHistory
            try {
                $conn->begin_transaction(); // Start the transaction

                // Update account table
                $updateLocationQuery = "UPDATE account SET latitude=?, longitude=?, timestamp=CURRENT_TIMESTAMP WHERE accountId=?";
                $updateLocationStmt = $conn->prepare($updateLocationQuery);
                if ($updateLocationStmt === false) {
                    throw new Exception("Unable to prepare location update statement.");
                }
                $updateLocationStmt->bind_param("ddi", $latitude, $longitude, $user_id);
                $updateLocationStmt->execute();
                
                // Insert into locationHistory table
                $insertLocationHistoryQuery = "INSERT INTO locationHistory (accountId, latitude, longitude, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
                $insertLocationHistoryStmt = $conn->prepare($insertLocationHistoryQuery);
                if ($insertLocationHistoryStmt === false) {
                    throw new Exception("Unable to prepare location history insert statement.");
                }
                $insertLocationHistoryStmt->bind_param("idd", $user_id, $latitude, $longitude);
                $insertLocationHistoryStmt->execute();

                $conn->commit(); // Commit the transaction if both queries were successful
                echo "Location updated and history recorded successfully!";
            } catch (Exception $e) {
                $conn->rollback(); // Rollback the transaction on error
                echo "Error: " . $e->getMessage();
            } finally {
                if (isset($updateLocationStmt)) $updateLocationStmt->close();
                if (isset($insertLocationHistoryStmt)) $insertLocationHistoryStmt->close();
                $conn->close();
            }
        } else {
            echo "Latitude and longitude parameters are required!";
        }
    } else {
        echo "Invalid request method!";
    }
} else {
    echo "Session variables not set!";
}
?>
