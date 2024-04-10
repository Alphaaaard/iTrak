<?php
include_once("../../config/connection.php");

// Function to get all locations from the database by accountId and date
function getLocationsByAccountAndDate($accountId, $date)
{
    $conn = connection();
    $locations = array();

    $sql = "SELECT a.firstName, a.latitude, a.longitude, a.picture, lh.*, a.color
        FROM locationhistory AS lh
        LEFT JOIN account AS a ON a.accountId = lh.accountId
        WHERE DATE(lh.timestamp) = ?"; // Filter by date

    if ($accountId) {
        $sql .= " AND a.accountId = ?"; // Filter by accountId if provided
    }


    $stmt = $conn->prepare($sql);

    // Bind parameters based on whether an account ID was provided
    if ($accountId) {
        $stmt->bind_param("si", $date, $accountId);
    } else {
        $stmt->bind_param("s", $date);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $pictureBase64 = base64_encode($row['picture']);
        $row['picture'] = $pictureBase64;
        $locations[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $locations;
}

// Get the accountId and date from the GET parameters
$accountId = isset($_GET['accountId']) ? intval($_GET['accountId']) : null;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); // Default to current date if not provided

// Fetch and echo location data as JSON
if ($accountId !== null && $date !== null) {
    // Fetch and echo location data for the specified accountId and date
    echo json_encode(getLocationsByAccountAndDate($accountId, $date));
} else {
    // Handle the error: accountId or date is not provided
    echo json_encode(array("error" => "Account ID or date not provided."));
}
