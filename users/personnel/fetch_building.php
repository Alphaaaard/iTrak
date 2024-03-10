<?php
session_start();
header('Content-Type: application/json');
require_once "../../config/connection.php"; // Adjust the path as necessary

$db = connection(); // Ensure this function returns a valid PDO or mysqli connection

if (!$db) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$selectedBuilding = isset($_GET['building']) ? $_GET['building'] : '';

// Check if a building has been selected
if (!empty($selectedBuilding)) {
    // Prepare a SQL query to count assets by status for the selected building
    $query = $db->prepare("SELECT status, COUNT(*) AS count FROM asset WHERE building = ? GROUP BY status");
    $query->execute([$selectedBuilding]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize status counts
    $statusCounts = [
        'Working' => 0,
        'Under Maintenance' => 0,
        'For Replacement' => 0,
        'Need Repair' => 0
    ];
    
    
    // Populate the status counts
    foreach ($result as $row) {
        $status = $row['status'];
        if (array_key_exists($status, $statusCounts)) {
            $statusCounts[$status] = (int)$row['count'];
        }
    }
    
    // Return the counts as a JSON object
    echo json_encode($statusCounts);
} else {
    echo json_encode(['error' => 'No building selected']);
}
?>