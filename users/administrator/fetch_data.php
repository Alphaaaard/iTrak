<?php
date_default_timezone_set('Asia/Manila');

// Database connection variables
$host = "localhost";
$username = "root";
$password = "";
$database = "upkeep";

$conn = new mysqli($host, $username, $password, $database);
$conn->query("SET time_zone = '+08:00';");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$employee = isset($_GET['employee']) ? $_GET['employee'] : null;

function getCountByWeek($conn, $weekStart, $weekEnd, $employeeFullName = null) {
    $sql = "SELECT COUNT(*) AS num FROM activitylogs WHERE action LIKE CONCAT(?, ' %logged in%') AND DATE(date) BETWEEN ? AND ?";
    $params = ["sss", $employeeFullName . '%', $weekStart, $weekEnd];

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
    if ($monthStart->format('N') > 5) {
        $monthStart->modify('next monday');
    }

    // Iterate through each week of the month
    for ($week = 1; $week <= 5; $week++) {
        $weekStartDate = (clone $monthStart)->modify("+".($week - 1) * 7 ." days");

        // If the week start date is beyond the month end, we break the loop
        if ($weekStartDate > $monthEnd) break;

        $weekEndDate = (clone $weekStartDate)->modify('+4 days'); // Add 4 days to get to Friday

        // If the week end date is beyond the month end, set it to the month end
        if ($weekEndDate > $monthEnd) {
            $weekEndDate = clone $monthEnd;
        }

        // Ensure the week's dates are within the month and weekdays only
        if ($weekStartDate <= $monthEnd) {
            $weeksData["Week $week"] = [
                'start' => $weekStartDate->format('Y-m-d 00:00:00'),
                'end' => $weekEndDate->format('Y-m-d 23:59:59')
            ];
        }
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
