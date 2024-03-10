<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Function to fetch data for a specific building
function getBuildingStatusData($conn, $buildingName)
{
    if ($buildingName === 'all') {
        // Fetch and aggregate data for all buildings
        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM asset GROUP BY status");
    } else {
        // Fetch data for a specific building
        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM asset WHERE building = ? GROUP BY status");
        $stmt->bind_param("s", $buildingName);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['status']] = $row['count'];
    }
    $stmt->close();
    return $data;
}

// Check if the building name is passed and not empty
if (isset($_GET['buildingName']) && !empty($_GET['buildingName'])) {
    $buildingName = $_GET['buildingName'];
    $data = getBuildingStatusData($conn, $buildingName);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>