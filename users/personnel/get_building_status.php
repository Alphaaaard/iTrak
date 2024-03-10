<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Function to fetch data for a specific building
function getBuildingStatusData($conn, $buildingName) {
    $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM asset WHERE building = ? GROUP BY status");
    $stmt->bind_param("s", $buildingName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Map the status to the appropriate key
        if ($row['status'] == 'Working') {
            $data['Working'] = $row['count']; // Use 'Working' instead of 'working'
        } elseif ($row['status'] == 'Under Maintenance') {
            $data['Under Maintenance'] = $row['count']; // Use 'Under Maintenance' instead of 'under_maintenance'
        } elseif ($row['status'] == 'Need to Repair') {
            $data['Need to Repair'] = $row['count']; // Use 'Need to Repair' instead of 'need_to_repair'
        } // You can remove this condition if you do not want to show 'For Replacement'
    }
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