<?php
// Include your database connection file
include_once ("../../config/connection.php");

// Create a new database connection
$conn = connection();

// Check if the connection was successful
if (!$conn) {
    $response = 'Connection failed: ' . mysqli_connect_error();
    echo $response;
    exit;
}

// Get the selected category from the POST data
$category = $_POST['category'];

// Prepare SQL statement to select a random assignee with expertise in the selected category
// Prepare SQL statement to select a random assignee with the specified expertise
$sql = "SELECT CONCAT(firstName, ' ', lastName) AS full_name FROM account WHERE expertise = ? ORDER BY RAND() LIMIT 1";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameter
$stmt->bind_param("s", $category);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the row
$row = $result->fetch_assoc();

// Echo the full name
echo $row['full_name'];


?>