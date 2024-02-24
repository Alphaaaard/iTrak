<?php
// Start the session
session_start();
// Include your database connection script
include_once("../../config/connection.php");
$conn = connection(); // Make sure this function returns a PDO or mysqli connection

// Check if an asset ID is specified
$assetId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($assetId > 0) {
    // Prepare the SQL statement to prevent SQL injection
    $query = "SELECT images FROM asset WHERE assetId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $assetId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($imageData);
        $stmt->fetch();
        // Output the header
        header('Content-Type: image/jpeg'); // Make sure the content type matches your image type
        // Echo out the image data
        echo $imageData;
    } else {
        // If no image found
        header("HTTP/1.0 404 Not Found");
        echo 'Image not found for the specified asset.';
    }

    // Close the statement
    $stmt->close();
} else {
    header("HTTP/1.0 400 Bad Request");
    echo 'Invalid asset ID.';
}

// Close the connection
$conn->close();
?>
