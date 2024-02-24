<?php
session_start();
include_once("./config/connection.php");
date_default_timezone_set('Asia/Manila');
$conn = connection();

// Check if the user is logged in
if (isset($_SESSION['accountId'])) {
    // Get the user's account ID from the session
    $accountId = $_SESSION['accountId'];

    $conn = connection();
    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Clear the latitude and longitude data in the 'upkeep' table for the user
    $sql = "UPDATE account SET latitude = NULL, longitude = NULL WHERE accountId = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();

    // Destroy the session
    unset($_SESSION['accountId']);
    session_destroy();

    // Close the database connection
    $stmt->close();
    $conn->close();
}

// Redirect the user to the "index.php" page
header("Location: index.php");
