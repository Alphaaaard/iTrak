<?php
include_once("../../config/connection.php");

// Function to get all locations from the database by date, and accountId (optional)
function getLocationsByDate($date, $accountId = null)
{
    $conn = connection();
    $locations = array();

    // Base SQL query to select location data
    $sql = "SELECT a.firstName, a.latitude, a.longitude, a.qculocation, a.picture, lh.*, a.color
    FROM locationhistory AS lh
    LEFT JOIN account AS a ON a.accountId = lh.accountId
    WHERE DATE(lh.timestamp) = ? 
    AND lh.qculocation != 'Outside of QCU'";

    // If an accountId is provided, add it as a filter
    if ($accountId !== null) {
        $sql .= " AND a.accountId = ?";
    }

    $stmt = $conn->prepare($sql);

    // Bind parameters based on whether an accountId was provided
    if ($accountId !== null) {
        $stmt->bind_param("si", $date, $accountId);
    } else {
        $stmt->bind_param("s", $date);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $pictureBase64 = base64_encode($row['picture']);
        $row['picture'] = $pictureBase64; // Convert the picture to Base64 for easy embedding in JSON
        $locations[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $locations;
}

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Get the date from the GET parameters or default to the current date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get the accountId from the GET parameters, if provided
$accountId = isset($_GET['accountId']) ? intval($_GET['accountId']) : null;

// Fetch and echo location data as JSON for the given date and optional accountId
echo json_encode(getLocationsByDate($date, $accountId));
