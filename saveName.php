<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you've already connected to your database and it's stored in $conn

    // The name to save
    $assignedName = $_POST['assignedName'];

    // Sanitize and validate your input as needed
    $assignedName = filter_var($assignedName, FILTER_SANITIZE_STRING);

    // Prepare your SQL statement (using prepared statements to prevent SQL injection)
    $stmt = $conn->prepare("INSERT INTO assets (assignedName) VALUES (?)");
    $stmt->bind_param("s", $assignedName);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        echo "Name saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();

    // Close the database connection
    $conn->close();
} else {
    // Not a POST request, handle the error
    echo "Invalid request method.";
}
?>
