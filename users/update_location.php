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

            // Check if the user is logged in
            if (!isset($_SESSION['accountId'])) {
                echo "User not logged in!";
                exit();
            }

            $user_id = $_SESSION['accountId'];

            $conn = connection();

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            try {
                // Check if a location entry for the user has been made within the last minute
                $checkLastEntryQuery = "SELECT TIMESTAMPDIFF(SECOND, MAX(timestamp), NOW()) AS diff FROM locationHistory WHERE accountId = ?";
                $checkLastEntryStmt = $conn->prepare($checkLastEntryQuery);
                $checkLastEntryStmt->bind_param("i", $user_id);
                $checkLastEntryStmt->execute();
                $result = $checkLastEntryStmt->get_result();
                $row = $result->fetch_assoc();

                if ($row['diff'] > 60 || $row['diff'] === null) {
                    // Insert location data into locationHistory table
                    $insertLocationHistoryQuery = "INSERT INTO locationHistory (accountId, latitude, longitude, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
                    $insertLocationHistoryStmt = $conn->prepare($insertLocationHistoryQuery);
                    $insertLocationHistoryStmt->bind_param("idd", $user_id, $latitude, $longitude);
                    $insertLocationHistoryStmt->execute();

                    echo "Location inserted successfully!";
                } else {
                    echo "Location already updated within the last minute.";
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            } finally {
                $conn->close();
            }

            // Update the user's location in the account table
            try {
                $conn = connection();

                // Update the user's location in the account table
                $updateLocationQuery = "UPDATE account SET latitude=?, longitude=?, timestamp=CURRENT_TIMESTAMP WHERE accountId=?";
                $updateLocationStmt = $conn->prepare($updateLocationQuery);
                $updateLocationStmt->bind_param("ddi", $latitude, $longitude, $user_id);
                $updateLocationStmt->execute();

                echo "Location updated successfully!";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            } finally {
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
