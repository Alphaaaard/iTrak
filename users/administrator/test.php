<?php

// Database connection variables
$host = "localhost";
$username = "root";
$password = "";
$database = "upkeep";

$conn = new mysqli($host, $username, $password, $database);

// Check if the 'employee' URL parameter is set and not empty
if (isset($_GET['employee']) && !empty($_GET['employee'])) {
    $selectedEmployee = $_GET['employee']; // Use the value from the URL parameter
} else {
    $selectedEmployee = ''; // Set a default value (empty string or any default you prefer)
}

$employeeFullNames = [];
// Query to fetch and concatenate firstName, middleName, and lastName into full names for userLevel 3
$employeeQuery = "SELECT CONCAT(firstName) AS fullName FROM account WHERE userLevel = 3";
$result = $conn->query($employeeQuery);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $employeeFullNames[] = $row['fullName'];
    }
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the default timezone, adjust according to your needs
date_default_timezone_set('Asia/Manila');

// Determine the current month or the selected month
$currentMonth = date('n'); // Numeric representation of the current month without leading zeros

// Calculate the previous month and year
$previousMonthTime = strtotime("-1 month");
$previousMonth = date('n', $previousMonthTime); // Numeric representation of the previous month without leading zeros
$yearOfPreviousMonth = date('Y', $previousMonthTime); // Year of the previous month

// Use the previous month and its first week as the default, unless overridden by URL parameters
$selectedMonth = isset($_GET['month']) && $_GET['month'] >= 1 && $_GET['month'] <= 12 ? (int)$_GET['month'] : $currentMonth;
$yearOfPreviousMonth = $selectedMonth == $currentMonth ? date('Y') : date('Y', strtotime("-1 month"));
$selectedWeek = isset($_GET['week']) ? (int)$_GET['week'] : 1; // Default to the first week of the selected month



// Log the selected month
error_log("Selected Month: " . $selectedMonth);

// Calculate the total number of days in the selected month
$totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $yearOfPreviousMonth);

// Calculate the number of weeks. Any remaining days at the end form an additional week.
$numberOfWeeks = ceil($totalDaysInMonth / 7);

function getCountByDayAndAction($conn, $selectedMonth, $selectedWeek, $employeeFullName, $totalDaysInMonth, $dayOfWeek) {
    date_default_timezone_set('Asia/Manila');
    $year = date('Y'); // Use the current year or adjust as needed

    if ($selectedMonth && $selectedWeek) {
        // Calculate the start date of the first day of the selected month
        $monthStart = new DateTime("$year-$selectedMonth-01");
        
        // Calculate the start and end date of the 'week'
        $weekStartDay = ($selectedWeek - 1) * 7 + 1; // Calculate start day of the week
        $weekEndDay = min($weekStartDay + 6, $totalDaysInMonth); // Ensure it does not exceed the month
        
        // Ensure the day part does not lead to an invalid date
        if ($weekStartDay > $totalDaysInMonth) {
            $weekStartDay = $totalDaysInMonth; // Adjust to the last day of the month if over
        }
        if ($weekEndDay > $totalDaysInMonth) {
            $weekEndDay = $totalDaysInMonth; // Adjust to the last day of the month if over
        }

        $weekStartDate = new DateTime("$year-$selectedMonth-$weekStartDay");
        $weekEndDate = new DateTime("$year-$selectedMonth-$weekEndDay");

        // Adjust for the specific day of the week within the calculated week range
        if ($weekStartDate->format('l') !== $dayOfWeek) {
            $weekStartDate->modify("next $dayOfWeek");
        }
        if ($weekEndDate->format('l') !== $dayOfWeek) {
            $weekEndDate->modify("last $dayOfWeek");
        }

        $dayStart = $weekStartDate->format('Y-m-d 00:00:00');
        $dayEnd = $weekEndDate->format('Y-m-d 23:59:59');
    } else {
        // Default to the current day's range if no month or week is selected
        $today = new DateTime("now");
        $today->modify($dayOfWeek); // Move to the specified day of this week
        $dayStart = $today->format('Y-m-d 00:00:00');
        $dayEnd = $today->format('Y-m-d 23:59:59');
    }

    // Construct the SQL query based on the selected filters
    $sql = "SELECT COUNT(*) AS num FROM activitylogs WHERE action LIKE CONCAT(?, ' %logged in%') AND date BETWEEN ? AND ?";
    $params = ["sss", "$employeeFullName%", $dayStart, $dayEnd];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    return $row ? $row['num'] : 0;
    }

// Days of the week to check
$daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

$labels = [];
$data = [];

// Get the day of the week for the first day of the selected month (0 for Sunday, 6 for Saturday)
$firstDayOfMonth = new DateTime("$yearOfPreviousMonth-$selectedMonth-01");
$firstDayOfWeek = (int) $firstDayOfMonth->format('w'); // 'w' gives numeric representation of the day of the week

// Calculate how many days are in the first partial week
$daysInFirstWeek = 7 - $firstDayOfWeek;

// Calculate total full weeks, subtracting the first partial week's days
$fullWeeks = ($totalDaysInMonth - $daysInFirstWeek) / 7;

// Add 1 for the first partial week and ceil to account for any remaining days as a partial last week
$numberOfWeeks = 1 + ceil($fullWeeks);

// Get selected action from the URL parameter
$selectedAction = isset($_GET['action']) ? $_GET['action'] : '';

// Inside your loop that sets up $labels and $data
foreach ($daysOfWeek as $dayOfWeek) {
    $count = getCountByDayAndAction($conn, $selectedMonth, $selectedWeek, $selectedEmployee, $totalDaysInMonth, $dayOfWeek);
    $labels[] = $dayOfWeek;
    $data[] = $count > 0 ? $count : null;
}


?>

<!DOCTYPE HTML>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    @media (max-width: 600px) {
        .filters-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
</head>
<body>
<div style="
    width: 50%; /* Smaller width */
    max-width: 805px; /* Adjusted maximum size to match your image */
    margin: auto; /* Center the container */
    padding: 20px; /* Add some padding for aesthetics */
    border-radius: 15px; /* Rounded corners */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    background-color: #f9f9f9; /* Light background color */
    display: flex; /* Use flexbox to layout children */
    flex-direction: column; /* Stack children vertically */
    align-items: center; /* Center children horizontally */
">
    <div style="
    width: 100%; /* Take the full width */
    display: flex; /* Use flexbox to layout children */
    justify-content: space-between; /* Space between title and filters */
    align-items: center; /* Center items vertically */
    padding-bottom: 20px; /* Space below the header */
    flex-wrap: wrap; /* Allow items to wrap */
">
    <h2 style="font-family: 'Poppins', sans-serif; color: #1e56a0; margin: 0; flex-grow: 1;">Task Completion</h2>
    <div class="filters-container" style="
        display: flex; /* Use flexbox to layout children */
        gap: 10px; /* Gap between filters */
        flex-grow: 2; /* Filters container takes more space */
        justify-content: center; /* Center filters within their container */
        flex-wrap: wrap; /* Allow filters to wrap */
    ">
            <select id="monthFilter" style="border-radius: 10px; padding: 5px; min-width: 150px;">
            <option value="">Select a Month</option>
        <?php for($m=1; $m<=12; ++$m): ?>
        <option value="<?php echo $m; ?>" <?php if($m == $selectedMonth) echo 'selected'; ?>>
            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
        </option>
        <?php endfor; ?>
            </select>
            <select id="weekFilter" style="border-radius: 10px; padding: 5px; min-width: 150px;">
            <option value="">Select a Week</option>
        <?php for ($w = 1; $w <= $numberOfWeeks; $w++): ?>
        <option value="<?php echo $w; ?>" <?php if (isset($_GET['week']) && $w == $_GET['week']) echo 'selected'; ?>>
        Week <?php echo $w; ?>
        </option>
        <?php endfor; ?>
            </select>
            <select id="employeeFilter" style="border-radius: 10px; padding: 5px; min-width: 150px;">
            <option value="">Select an Employee</option>
        <?php foreach($employeeFullNames as $name): ?>
        <option value="<?php echo htmlspecialchars($name); ?>" <?php if(isset($_GET['employee']) && $_GET['employee'] == $name) echo 'selected'; ?>>
        <?php echo htmlspecialchars($name); ?>
        </option>
        <?php endforeach; ?>
            </select>
        </div>
    </div>

    <canvas id="statusChart" style="width: 100%;"></canvas>
</div>





<script>
window.onload = function() {
    var ctx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Task Completion',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: 'rgba(22, 49, 114)',
                borderColor: 'rgba(22, 49, 114))',
                borderWidth: 1,
                borderRadius: 37
            }]
        },
        options: {
    plugins: {
        legend: {
            display: false // This will hide the legend
        }
    },
    scales: {
        x: {
            categoryPercentage: 0.5,
            barPercentage: 0.3,
        },
        y: {
            
            ticks: {
                // Modify this part of your existing code
                callback: function(value, index, values) {
                    // Check if the value is an integer
                    if (value % 1 === 0) {
                        return value;
                    }
                }
            }
        }
    },
    tooltip: {
        callbacks: {
            label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                    label += ': ';
                }
                label += new Intl.NumberFormat().format(context.parsed.y);
                return label;
            }
        }
    }
}
    });
}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
document.getElementById('monthFilter').addEventListener('change', updateFilters);
document.getElementById('weekFilter').addEventListener('change', updateFilters);
document.getElementById('employeeFilter').addEventListener('change', updateFilters);

function updateFilters() {
    var month = document.getElementById('monthFilter').value || '';
    var week = document.getElementById('weekFilter').value || '';
    var employee = document.getElementById('employeeFilter').value || '';
    window.location.href = '?month=' + month + '&week=' + week + '&employee=' + encodeURIComponent(employee);
}
</script>

</body>
</html>