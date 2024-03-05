<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Check if this is an AJAX request to update a notification's seen status
if (isset($_POST['activityId'])) {
    $activityId = $_POST['activityId'];

    // Update the seen status for the notification with the provided activityId
    $stmt = $conn->prepare("UPDATE activitylogs SET seen = '1' WHERE activityId = ?");
    $stmt->bind_param("i", $activityId);
    
    if ($stmt->execute()) {
        echo "Notification updated successfully";
    } else {
        echo "Error updating notification: " . $stmt->error;
    }
    $stmt->close(); // Close the statement
    exit(); // Important: terminate the script here
}

// Get the current logged-in user's account ID from the session
$loggedInAccountId = $_SESSION['accountId'];

// Proceed to fetch the current notification count
// This SQL counts only the unseen notifications for the Report tab and excludes the current user's activities
$sql = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE tab='Report' AND seen = '0' AND accountID != ?";
$stmt = $conn->prepare($sql);

// Bind the parameter to exclude the current user's account ID
$stmt->bind_param("i", $loggedInAccountId);

$stmt->execute();
$stmt->store_result();
$stmt->bind_result($unseenCount);
$stmt->fetch();

// Output the count
echo $unseenCount;

// Close the statement and connection
$stmt->close();
$conn->close();
?>
