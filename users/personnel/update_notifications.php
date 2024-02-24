<?php
// update_notifications.php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Create connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// for notif below
// Update the SQL to join with the account and asset tables to get the admin's name and asset information

// Prepare and execute the SQL statement for the latest logs


if (isset($_POST['update_seen']) && isset($_SESSION['accountId']) && isset($_SESSION['email']) && isset($_SESSION['role']) && isset($_SESSION['userLevel'])) {

  
        // For personnel page, check if userLevel is 3
        if($_SESSION['userLevel'] != 3) {
            // If not personnel, redirect to an error page or login
            header("Location:error.php");
            exit;
        }
    
    $sql = "UPDATE activitylogs 
            SET seen = '1' 
            WHERE seen = '0' AND tab='Report' 
            ORDER BY date DESC 
            LIMIT 5";
    
    if ($conn->query($sql) === TRUE) {
        echo "Notifications updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}
$sql = "SELECT al.*, acc.firstName AS adminFirstName, acc.lastName AS adminLastName
FROM activitylogs AS al
JOIN account AS acc ON al.accountId = acc.accountId
WHERE al.tab='Report' 
AND al.seen LIKE '0%' AND al.action LIKE 'Assigned maintenance personnel%'
ORDER BY al.date DESC 
LIMIT 5";

$result = $conn->query($sql);

echo $result->num_rows;
/*
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Notification: " . $row["description"];
    }
} else {
    echo "0 results";
}
*/
$conn->close();
?>

