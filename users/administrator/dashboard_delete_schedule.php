<?php
// Include your database connection here
include_once("../../config/connection.php");
$conn = connection();

// Check if sbId is set
if (isset($_POST['sbId'])) {
    $sbId = $_POST['sbId'];

    // Prepare your DELETE query
    $sql = "DELETE FROM scheduleboard WHERE sbId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sbId);

    // Execute the query
    if ($stmt->execute()) {
        // On success, send a positive response back
        echo "Success";
    } else {
        // On failure, send an error message
        echo "Error: " . $conn->error;
    }
}
?>
