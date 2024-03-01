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
    

    exit(); // Important: terminate the script here
}
$loggedInUserFirstName = $_SESSION['firstName'];
$loggedInUserMiddleName = $_SESSION['middleName']; // Assuming you store middle name in the session
$loggedInUserLastName = $_SESSION['lastName'];
// Concatenate first, middle, and last names to form the full name
$loggedInFullName = $loggedInUserFirstName . ' ' . $loggedInUserMiddleName . ' ' . $loggedInUserLastName;

// If this is not an AJAX request, proceed to fetch the current notification count
// Modify the SQL to count only the unseen notifications for the logged-in user
 

// Old code with specific name condition
// $searchTerm = "%Assigned maintenance personnel " . $loggedInFullName . "%";

// New SQL query without the specific name condition
$sql = "SELECT COUNT(*) as unseenCount FROM activitylogs WHERE tab='Report' AND seen = '0'";

// Execute the query directly without a prepared statement since there is no user input
$result = $conn->query($sql);

// Check if the query was successful
if($result) {
    $row = $result->fetch_assoc();
    echo $row['unseenCount']; // Output the count of unseen notifications for the 'Report' tab
} else {
    echo "Error fetching notification count: " . $conn->error;
}

$conn->close();














?>