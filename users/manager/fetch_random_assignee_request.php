<?php
// Include your database connection file
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

// Get the current date
$currentDate = date('Y-m-d');

// Prepare SQL statement to select a random assignee with expertise in the specified category
$sql = "SELECT a.accountId, CONCAT(a.firstName, ' ', a.lastName) AS full_name 
        FROM account a 
        LEFT JOIN request r ON a.accountId = r.assignee AND r.date = ?
        WHERE a.expertise = ? AND r.request_id IS NULL 
        ORDER BY RAND() 
        LIMIT 1";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("ss", $currentDate, $category);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if there are rows returned
if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    // Echo the full name
    echo $row['full_name'];
} 

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();


?>