<?php
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the period parameter from the query string
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

// Initialize the query variable
$query = "";

// Adjust the query based on the period
switch ($period) {
    case 'week':
        // Weekly attendance data for the current week
        $query = "SELECT DAYNAME(date) as day, 
                         SUM(IF(accountId = 2, 1, 0)) as ManagerCount, 
                         SUM(IF(accountId = 3, 1, 0)) as PersonnelCount
                  FROM attendancelogs 
                  WHERE YEARWEEK(date) = YEARWEEK(CURRENT_DATE())
                  GROUP BY DAYNAME(date)
                  ORDER BY DAYOFWEEK(date);";
        break;
    case 'month':
        // Monthly attendance data (existing query can be used here)
        $query = "SELECT WEEK(date) as week, 
                         SUM(IF(accountId = 2, 1, 0)) as ManagerCount, 
                         SUM(IF(accountId = 3, 1, 0)) as PersonnelCount
                  FROM attendancelogs 
                  WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())
                  GROUP BY WEEK(date)
                  ORDER BY WEEK(date);";
        break;
    case 'year':
        // Yearly attendance data
        $query = "SELECT MONTHNAME(date) as month, 
                         SUM(IF(accountId = 2, 1, 0)) as ManagerCount, 
                         SUM(IF(accountId = 3, 1, 0)) as PersonnelCount
                  FROM attendancelogs 
                  WHERE YEAR(date) = YEAR(CURRENT_DATE())
                  GROUP BY MONTH(date)
                  ORDER BY MONTH(date);";
        break;
    default:
        // Default to monthly if the period parameter is not recognized
        $query = "SELECT WEEK(date) as week, 
                         SUM(IF(accountId = 2, 1, 0)) as ManagerCount, 
                         SUM(IF(accountId = 3, 1, 0)) as PersonnelCount
                  FROM attendancelogs 
                  WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())
                  GROUP BY WEEK(date)
                  ORDER BY WEEK(date);";
        break;
}

$result = $conn->query($query);

// Prepare data to send back to AJAX call based on the period
$attendanceData = [
    'Manager' => [],
    'Personnel' => [],
    'labels' => []
];

while ($row = $result->fetch_assoc()) {
    switch ($period) {
        case 'week':
            $attendanceData['labels'][] = $row['day'];
            break;
        case 'month':
            $attendanceData['labels'][] = "Week " . $row['week'];
            break;
        case 'year':
            $attendanceData['labels'][] = $row['month'];
            break;
    }
    $attendanceData['Manager'][] = (int)$row['ManagerCount'];
    $attendanceData['Personnel'][] = (int)$row['PersonnelCount'];
}

// Close connection
$conn->close();

// Output the JSON encoded data
echo json_encode($attendanceData);
