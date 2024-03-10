<?php
date_default_timezone_set('Asia/Manila');
session_start();
include_once("../../config/connection.php");
$conn = connection();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$employee = isset($_GET['employee']) ? $_GET['employee'] : null;

function getCountByWeek($conn, $weekStart, $weekEnd, $employeeFullName = null) {
    // Modified SQL query to join with the account table and to check for a specific action
    $sql = "SELECT COUNT(*) AS num 
            FROM activitylogs al
            INNER JOIN account ac ON al.accountId = ac.accountId
            WHERE CONCAT(ac.firstName, ' ', ac.lastName) LIKE CONCAT(?, '%')
            AND al.action LIKE '%Changed Status of% to Working%'
            AND DATE(al.date) BETWEEN ? AND ?";

    // Assuming $employeeFullName might be null, use a wildcard in such a case
    $employeeFullName = $employeeFullName ? $employeeFullName . '%' : '%';

    // Updated parameters - removed the first 's' since we're no longer using the 'action' column for the employee name
    $params = ["sss", $employeeFullName, $weekStart, $weekEnd];

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    return $row ? $row['num'] : 0;
}


function getWeeksData($selectedMonth) {
    $weeksData = [];
    $year = date('Y');
    $monthStart = new DateTime("$year-$selectedMonth-01");
    $monthEnd = new DateTime("$year-$selectedMonth-" . cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $year));

    // Adjust the month start to the first Monday of the month
    if ($monthStart->format('N') > 1) {
        $monthStart->modify('first monday of this month');
    }

    $weekCounter = 1;

    // Iterate starting from the first Monday
    while (true) {
        $weekStartDate = (clone $monthStart)->modify("+" . ($weekCounter - 1) * 7 . " days");

        // Normally, the week ends 6 days after the start
        $weekEndDate = (clone $weekStartDate)->modify('+6 days'); 

        // If it's the last iteration and the week start date is in the selected month but the end date spills over to the next month
        if ($weekStartDate->format('m') == $monthStart->format('m') && $weekEndDate->format('m') != $monthStart->format('m')) {
            // Extend the weekEndDate to include the spill-over days into the next month
            $weeksData["Week $weekCounter"] = [
                'start' => $weekStartDate->format('Y-m-d 00:00:00'),
                'end' => $weekEndDate->format('Y-m-d 23:59:59')
            ];
            break; // Exit the loop as this is the last week of the month
        } elseif ($weekStartDate > $monthEnd) {
            // If the week start date is beyond the month end, break the loop
            break;
        } else {
            // For all other weeks within the month
            if ($weekEndDate > $monthEnd) {
                $weekEndDate = clone $monthEnd;
            }

            $weeksData["Week $weekCounter"] = [
                'start' => $weekStartDate->format('Y-m-d 00:00:00'),
                'end' => $weekEndDate->format('Y-m-d 23:59:59')
            ];
        }

        $weekCounter++;
    }

    return $weeksData;
}

$weeksData = getWeeksData($month);

$labels = array_keys($weeksData);
$data = array_fill(0, count($weeksData), 0); // Initialize data array with zeros

foreach ($weeksData as $weekLabel => $weekData) {
    $data[array_search($weekLabel, $labels)] = getCountByWeek($conn, $weekData['start'], $weekData['end'], $employee);
}

$conn->close();

$response = [
    'labels' => $labels,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>
