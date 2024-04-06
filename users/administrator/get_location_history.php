<?php
include_once("../../config/connection.php");

// Function to get all locations from the database
function getAllLocationsFromDatabase($accountId = null)
{
    $conn = connection();
    $locations = array();

    $sql = "SELECT a.firstName, a.latitude, a.longitude, a.picture, lh.*, a.color
            FROM locationhistory AS lh
        
            LEFT JOIN account AS a ON a.accountId = lh.accountId";
    if ($accountId) {
        $sql .= " WHERE a.accountId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $accountId);
    } else {
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Convert BLOB data to base64 encoding for the picture
        $pictureBase64 = base64_encode($row['picture']);
        // Add base64 encoded picture to the row
        $row['picture'] = $pictureBase64;
        $locations[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $locations;
}

$accountId = isset($_GET['accountId']) ? intval($_GET['accountId']) : null;

$locations = getAllLocationsFromDatabase($accountId);

echo json_encode($locations);
