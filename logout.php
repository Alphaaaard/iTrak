<?php
session_start();
include_once("./config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

// Check if the user is logged in
if (isset($_SESSION['accountId'])) {
    $accountId = $_SESSION['accountId'];

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL to clear the latitude and longitude data for the user, and adjust the timestamp by +8 hours
    $sql = "UPDATE account SET latitude = NULL, longitude = NULL, timestamp = NULL, logout_time = DATE_ADD(NOW(), INTERVAL 8 HOUR) WHERE accountId = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $stmt->close();  // Close the statement
    } else {
        echo "Failed to prepare statement";
    }

    // Destroy the session
    unset($_SESSION['accountId']);
    session_destroy();

    // Close the database connection
    $conn->close();

    // Redirect the user to the login page
    header("Location: index.php");
    exit();  // Ensure no further execution of script after redirect
}
?>
