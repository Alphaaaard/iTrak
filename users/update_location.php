<?php
session_start();
include_once("../config/connection.php");

if (isset($_SESSION['accountId']) && isset($_SESSION['email']))

    date_default_timezone_set('Asia/Manila');
$conn = connection();

if (isset($_SESSION['accountId'])) {
    $accountId = $_SESSION['accountId'];
    $todayDate = date("Y-m-d");

    // Check if there's a timeout value for this user for today
    $timeoutQuery = "SELECT timeout FROM attendancelogs WHERE accountId = '$accountId' AND date = '$todayDate'";
    $timeoutResult = $conn->query($timeoutQuery);
    $timeoutRow = $timeoutResult->fetch_assoc();

    if ($timeoutRow && $timeoutRow['timeout'] !== null) {
        // User has a timeout value, force logout
        session_destroy(); // Destroy all session data
        header("Location: ../../index.php?logout=timeout"); // Redirect to the login page with a timeout flag
        exit;
    }
}

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
            // Update the user's location in the database
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
