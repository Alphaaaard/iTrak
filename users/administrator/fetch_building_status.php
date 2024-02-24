<?php
// get_building_status.php
header('Content-Type: application/json');

// Fetch POST data
$input = json_decode(file_get_contents('php://input'), true);
$buildingId = $input['building_id'];

include_once("../../config/connection.php");
$conn = connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL query
$query = "SELECT status, COUNT(*) as count FROM asset WHERE building_id = ? GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $buildingId);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    // Map the status to the appropriate key
    if ($row['status'] == 'Working') {
        $data['working'] = $row['count'];
    } elseif ($row['status'] == 'Under Maintenance') {
        $data['under_maintenance'] = $row['count'];
    } elseif ($row['status'] == 'Need to Repair') {
        $data['need_to_repair'] = $row['count'];
    }
}

// Close connection
$stmt->close();
$conn->close();

// Return data as JSON
echo json_encode($data);