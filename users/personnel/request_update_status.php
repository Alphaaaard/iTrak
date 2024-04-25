<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

date_default_timezone_set('Asia/Manila');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the request ID is set and is a valid integer
    if (isset($_POST['requestId']) && filter_var($_POST['requestId'], FILTER_VALIDATE_INT)) {
        $requestId = $_POST['requestId'];

        // Update the status in the database
        $sql = "UPDATE request SET status = 'Done' WHERE request_id = $requestId";

        if ($conn->query($sql) === TRUE) {
            // Return success message
            echo "success";
        } else {
            // Return error message
            echo "Error updating status: " . $conn->error;
        }
    } else {
        // Return error message for invalid request ID
        echo "Invalid request ID";
    }
} else {
    // Return error message for invalid request method
    echo "Invalid request method";
}

// Close the database connection if needed
$conn->close();
?>
