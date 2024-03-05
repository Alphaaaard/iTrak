<?php
// Database connection variables
$host = "localhost";
$username = "root";
$password = "";
$database = "upkeep";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee names from the database
$query = "SELECT firstName, lastName FROM account WHERE userLevel = 3";
$result = $conn->query($query);

$employeeNames = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employeeNames[] = $row['firstName'] . ' ' . $row['lastName'];
    }
}

$conn->close();

// Return employee names as JSON
header('Content-Type: application/json');
echo json_encode($employeeNames);
?>
