<?php

include_once("../../config/connection.php");

// Function to get locations for a specific account from the database
function getLocationsForAccount($accountId)
{
    $conn = connection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    try {
        $sql = "SELECT lh.id, lh.accountid, lh.latitude, lh.longitude, lh.timestamp, a.firstName, a.lastName
                FROM locationhistory AS lh
                INNER JOIN account AS a ON lh.accountid = a.accountId
                WHERE lh.accountid = ?
                ORDER BY lh.timestamp DESC";

        $stmt = $conn->prepare($sql);

        // Bind the accountId parameter
        $stmt->bind_param("i", $accountId);

        $stmt->execute();

        $result = $stmt->get_result();

        $locations = array();

        while ($row = $result->fetch_assoc()) {
            $locations[] = $row;
        }

        return $locations;
    } catch (Exception $e) {
        return array('error' => $e->getMessage());
    } finally {
        $stmt->close(); // Close the prepared statement
        $conn->close(); // Close the database connection
    }
}

// Check if accountId is set in the URL
if (isset($_GET['accountId'])) {
    $accountId = $_GET['accountId'];
    $locations = getLocationsForAccount($accountId);
} else {
    // Handle case where accountId is not provided
    $locations = array('error' => 'No accountId provided');
}

// Display the location history data
if (!empty($locations)) {
    // Output location history data as per your requirements
    // For example, you could iterate over $locations and display each location
    foreach ($locations as $location) {
        echo "Latitude: " . $location['latitude'] . "<br>";
        echo "Longitude: " . $location['longitude'] . "<br>";
        echo "Timestamp: " . $location['timestamp'] . "<br>";
        // Add more fields as needed
    }
} else {
    echo "No location history found.";
}
