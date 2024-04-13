<?php

include_once("../../config/connection.php");

// Function to get all locations from the database
function getAllLocationsFromDatabase()
{
    $conn = connection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    try {

        $sql = "SELECT account.firstName, account.latitude, account.longitude, account.timestamp, account.color, account.qculocation, account.picture, attendancelogs.attendanceId
        FROM attendancelogs
        LEFT JOIN account ON account.accountId = attendancelogs.accountId
        ORDER BY account.timestamp DESC";

        $result = $conn->query($sql);

        $locations = array();

        while ($row = $result->fetch_assoc()) {
            // Convert BLOB data to base64 encoding for the picture
            $pictureBase64 = base64_encode($row['picture']);
            // Add base64 encoded picture to the row
            $row['picture'] = $pictureBase64;
            $locations[] = $row;
        }

        return $locations;
    } catch (Exception $e) {
        return array('error' => $e->getMessage());
    } finally {
        $conn->close();
    }
}

// Get all locations from the database
$locations = getAllLocationsFromDatabase();

// Send response back to the JavaScript code
echo json_encode($locations);
