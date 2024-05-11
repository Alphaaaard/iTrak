<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hardcoded campus names
$campusNames = array("Batasan", "San Bartolome", "San Francisco");

// Return employee names as JSON
header('Content-Type: application/json');
echo json_encode($campusNames);
?>
