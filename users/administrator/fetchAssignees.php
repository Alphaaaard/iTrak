<?php
// Include the database connection
include_once ("../../config/connection.php");

// Create a new database connection
$conn = connection();

// Check if the connection was successful
if (!$conn) {
    $response = 'Connection failed: ' . mysqli_connect_error();
    echo $response;
    exit;
}

// Retrieve the category value from the AJAX request
$category = $_GET['category'];

// Query the database for assignees based on the category
$stmt = $conn->prepare("SELECT accountId, firstName, lastName FROM account WHERE expertise = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the assignees
$assignees = [];
while ($row = $result->fetch_assoc()) {
    $assignees[] = $row;
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

// Return the assignees as JSON
echo json_encode($assignees);
?>