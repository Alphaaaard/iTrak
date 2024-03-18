<?php

// Database connection variables
$host = "localhost";
$username = "root";
$password = "";
$database = "upkeep";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$conn->close();

?>
<!DOCTYPE HTML>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<link rel="stylesheet" href="../../src/css/taskchart.css">
</head>
<body>
<div class="chart-container">
    <div class="filter-container flex-container">
        <h2>Task Completion</h2>
            <div class="filter-wrapper">
                <select id="monthFilter">
                    <option value="">Select Month</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
            <select id="employeeFilter">
                <option value="">Select Employee</option>
                <!-- Employee options will be populated dynamically via AJAX -->
            </select>
    </div>
        <canvas id="statusChart"></canvas>
</div>
<script>
$(document).ready(function() {
    // Define default month and week as January and Week 1
    var defaultMonth = 3; // January
    var defaultWeek = 1; // Week 1

    // Set default selections for month and week filters
    $('#monthFilter').val(defaultMonth);
    $('#weekFilter').val(defaultWeek);

    // Populate employee filter options via AJAX
    $.ajax({
        url: 'get_employee_names.php',
        type: 'GET',
        dataType: 'json',
        success: function(employeeNames) {
            $('#employeeFilter').empty().append($('<option>').text('Select Employee').attr('value', ''));
            employeeNames.forEach(function(name) {
                $('#employeeFilter').append($('<option>').text(name).attr('value', name));
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching employee names:", error);
        }
    });

    var statusChart;

    function updateWeekOptions() {
        $('#weekFilter').empty().append($('<option>').text('Select Week').attr('value', ''));
        for (var i = 1; i <= 5; i++) {
            $('#weekFilter').append($('<option>').text('Week ' + i).attr('value', i));
        }
    }

    // Update week options when month selection changes
    $('#monthFilter').on('change', updateWeekOptions);

    function fetchData() {
        var month = $('#monthFilter').val() || defaultMonth;
        var week = $('#weekFilter').val() || defaultWeek;
        var employee = $('#employeeFilter').val() || '';

        // Calculate the start date of the selected week within the selected month
        var startDate = moment([new Date().getFullYear(), month - 1]).startOf('month').add((week - 1) * 7, 'days');
        // Adjust to the start of the week (Monday)
        var weekStart = startDate.clone().startOf('isoWeek');
        if (weekStart.month() + 1 !== month) { // Ensure week start is within the month
            weekStart = startDate.clone();
        }

        // Calculate the end date of the selected week within the selected month
        var endDate = weekStart.clone().add(6, 'days');
        if (endDate.month() + 1 !== month) { // Ensure week end is within the month
            endDate = moment([new Date().getFullYear(), month - 1]).endOf('month');
        }

        // Ensure both month and week are selected
        if (!month || !week) {
            console.log('Both month and week need to be selected.');
            return; // Exit the function if either is not selected
        }

        $.ajax({
            url: 'fetch_data.php',
            type: 'GET',
            data: {
                month: month,
                week: week,
                employee: employee,
                start: weekStart.format('YYYY-MM-DD'),
                end: endDate.format('YYYY-MM-DD')
            },
            dataType: 'json',
            success: function(response) {
                if (statusChart) {
                    statusChart.destroy();
                }
                var ctx = document.getElementById('statusChart').getContext('2d');
                statusChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Task Completion',
                            data: response.data,
                            backgroundColor: [
                                'rgba(22, 49, 114)',
                                'rgba(22, 49, 114)',
                                'rgba(22, 49, 114)',
                                'rgba(22, 49, 114)'
                            ],
                            borderColor: [
                                'rgba(22, 49, 114)',
                                'rgba(22, 49, 114)',
                                'rgba(22, 49, 114)',
                                'rgba(22, 49, 114)'
                            ],
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
            grid: {
                drawBorder: false,
                drawTicks: false,
                display: false
            },
            ticks: {
                display: true // Keep this true to show the labels
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value, index, values) {
                    // Only return the tick if it is an even number
                    if (value % 2 === 0) {
                        return value;
                    }
                }
            }
        }
    },
    tooltips: {
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
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    // Trigger fetchData when selections change
    $('#monthFilter, #weekFilter, #employeeFilter').change(fetchData);

    // Call updateWeekOptions and fetchData on page load to display the default data
    updateWeekOptions(); // This will populate the week filter based on the current month
    fetchData(); // This will fetch and display data for the current month and week
});
</script>




</body>
</html>