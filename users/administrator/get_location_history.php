<?php
include_once("../../config/connection.php");

// Function to get all locations from the database
function getAllLocationsFromDatabase($accountId = null)
{
    $conn = connection();
    $locations = array();

    $sql = "SELECT a.firstName, a.latitude, a.longitude, lh.*, a.color
            FROM locationhistory AS lh
        
            LEFT JOIN account AS a ON a.accountId = lh.accountId";
if ($accountId) {
    $sql .= " WHERE a.accountId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
} else {
    $stmt = $conn->prepare($sql);
}


    if ($accountId) {
        $stmt->bind_param("i", $accountId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $locations;
}

$accountId = isset($_GET['accountId']) ? intval($_GET['accountId']) : null;

$locations = getAllLocationsFromDatabase($accountId);

echo json_encode($locations);
