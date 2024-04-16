<?php
// Clean (erase) the output buffer and turn off output buffering
while (ob_get_level()) ob_end_clean();

// Set the content-type header to application/json to ensure proper json response
header('Content-Type: application/json');

// Disable any PHP errors, warnings, or notices from being outputted
ini_set('display_errors', 0);
error_reporting(0);

session_start();
include_once("../../config/connection.php");
$conn = connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$period = isset($_GET['period']) ? $_GET['period'] : 'month';
$query = "";

switch ($period) {
    case 'week':
        $query = "SELECT DATE(al.date) as date, 
                  DAYNAME(al.date) as day, 
                  SUM(IF(a.role = 'Maintenance Manager', 1, 0)) as ManagerCount, 
                  SUM(IF(a.role = 'Maintenance Personnel', 1, 0)) as PersonnelCount
                  FROM attendancelogs al
                  INNER JOIN account a ON al.accountId = a.accountId
                  WHERE YEARWEEK(al.date) = YEARWEEK(CURDATE())
                  GROUP BY DATE(al.date)
                  ORDER BY al.date;";
        break;
    
    case 'month':
        $start_of_month = new DateTime("first day of this month");
        $start_week_num = (int)$start_of_month->format("W");
        $query = "SELECT WEEK(date) - $start_week_num + 1 as week_num, 
                  SUM(IF(a.role = 'Maintenance Manager', 1, 0)) as ManagerCount, 
                  SUM(IF(a.role = 'Maintenance Personnel', 1, 0)) as PersonnelCount
                  FROM attendancelogs al
                  INNER JOIN account a ON al.accountId = a.accountId
                  WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())
                  GROUP BY WEEK(date)
                  ORDER BY WEEK(date);";
        break;

    case 'year':
        $query = "SELECT MONTH(al.date) as month_number, 
                  MONTHNAME(al.date) as month, 
                  SUM(IF(a.role = 'Maintenance Manager', 1, 0)) as ManagerCount, 
                  SUM(IF(a.role = 'Maintenance Personnel', 1, 0)) as PersonnelCount
                  FROM (SELECT 1 AS month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                        UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8
                        UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) AS all_months
                  LEFT JOIN attendancelogs al ON MONTH(al.date) = all_months.month
                  AND YEAR(al.date) = YEAR(CURRENT_DATE())
                  LEFT JOIN account a ON al.accountId = a.accountId
                  GROUP BY all_months.month, MONTHNAME(al.date)
                  ORDER BY all_months.month;";
        break;

    default:
        $query = "SELECT WEEK(date) as week, 
                  SUM(IF(a.role = 'Maintenance Manager', 1, 0)) as ManagerCount, 
                  SUM(IF(a.role = 'Maintenance Personnel', 1, 0)) as PersonnelCount
                  FROM attendancelogs al
                  INNER JOIN account a ON al.accountId = a.accountId
                  WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())
                  GROUP BY WEEK(date)
                  ORDER BY WEEK(date);";
        break;
}

$result = $conn->query($query);

$attendanceData = [
    'Manager' => [],
    'Personnel' => [],
    'labels' => []
];

if ($period === 'year') {
    $monthlyCounts = array_fill(1, 12, ['ManagerCount' => 0, 'PersonnelCount' => 0]);
    while ($row = $result->fetch_assoc()) {
        $monthNumber = date('n', strtotime($row['month']));
        $monthlyCounts[$monthNumber]['ManagerCount'] += (int)$row['ManagerCount'];
        $monthlyCounts[$monthNumber]['PersonnelCount'] += (int)$row['PersonnelCount'];
    }
    
    for ($i = 1; $i <= 12; $i++) {
        $monthName = date('M', mktime(0, 0, 0, $i, 1));
        array_push($attendanceData['labels'], $monthName);
        array_push($attendanceData['Manager'], $monthlyCounts[$i]['ManagerCount']);
        array_push($attendanceData['Personnel'], $monthlyCounts[$i]['PersonnelCount']);
    }
} else {
    while ($row = $result->fetch_assoc()) {
        if ($period === 'week') {
            $attendanceData['labels'][] = date('D', strtotime($row['date']));
        } elseif ($period === 'month') {
            $attendanceData['labels'][] = "Week " . $row['week_num'];
        }
        $attendanceData['Manager'][] = (int)$row['ManagerCount'];
        $attendanceData['Personnel'][] = (int)$row['PersonnelCount'];
    }
}

$conn->close();

echo json_encode($attendanceData);
exit();
?>
