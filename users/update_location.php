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

            // Update the user's location in the account table
            try {
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
?>