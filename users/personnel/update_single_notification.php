<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Check if this is an AJAX request to update a notification's seen status
if (isset($_POST['activityId'])) {
    $activityId = $_POST['activityId'];

    // Update the seen status for the notification with the provided activityId
    $stmt = $conn->prepare("UPDATE activitylogs SET p_seen = '1' WHERE activityId = ?");
    $stmt->bind_param("i", $activityId);
    
    if ($stmt->execute()) {
        echo "Notification updated successfully";
    } else {
        echo "Error updating notification: " . $stmt->error;
    }
    

    exit(); // Important: terminate the script here
}
$loggedInUserFirstName = $_SESSION['firstName'];
$loggedInUserMiddleName = $_SESSION['middleName']; // Assuming you store middle name in the session
$loggedInUserLastName = $_SESSION['lastName'];
// Concatenate first, middle, and last names to form the full name
$loggedInFullName = $loggedInUserFirstName . ' ' . $loggedInUserMiddleName . ' ' . $loggedInUserLastName;

// If this is not an AJAX request, proceed to fetch the current notification count
// Modify the SQL to count only the unseen notifications for the logged-in user
 


$searchTerm = "%Assigned maintenance personnel " . $loggedInFullName . "%";

$sql = "SELECT COUNT(*) as unseenCount
FROM activitylogs AS al
JOIN account AS acc ON al.accountId = acc.accountId
WHERE al.tab='Report' 
AND al.action LIKE ?
AND al.p_seen = '0'";

$stmt = $conn->prepare($sql);

// The search term appears to be a string, so use 's' as the type


// Bind the parameter and execute
$stmt->bind_param("s", $searchTerm); // Corrected to match one placeholder with one variable
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($unseenCount);
$stmt->fetch();

echo $unseenCount; // Output the count of unseen notifications filtered by the logged-in user's name

$stmt->close();
$conn->close();










?>