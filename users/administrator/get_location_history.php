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

        $sql = "SELECT a.firstName, a.latitude, a.longitude, lh.*, a.color
        FROM locationhistory AS lh
        LEFT JOIN account AS a ON a.accountId = lh.accountId
        ORDER BY lh.timestamp DESC";

        $result = $conn->query($sql);

        $locations = array();

        while ($row = $result->fetch_assoc()) {
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
