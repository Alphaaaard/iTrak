<?php
require_once("config/connection.php");
session_start();
$conn = connection();

$accountId = $_SESSION['accountId'];
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in
if (isset($_SESSION['accountId'])) {
  // Get the current time
  $currentTime = date("H:i:s");

  // Check if it's midnight (00:00:00)
  if ($currentTime === "00:00:00") {
    // Clear the latitude and longitude data in the 'account' table for the user

    $sql = "UPDATE account SET latitude = NULL, longitude = NULL, timestamp = NULLIF(:timestamp, '0000-00-00 00:00:00') WHERE accountId = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $stmt->close();

    // Destroy the session
    unset($_SESSION['accountId']);
    session_destroy();

    // Redirect the user to the "index.php" page
    header("Location: ../../index.php");
    exit();
  }
}
