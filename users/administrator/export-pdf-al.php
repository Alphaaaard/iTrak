<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use Dompdf\Dompdf;

if (isset($_POST['submit']) && isset($_POST['accountId'])) {
    $conn = connection();

    $accountId = $_POST['accountId']; // Retrieve the accountId from the POST data
    // Retrieve the filterType from the POST data
    $filterType = isset($_POST['filterType']) ? $_POST['filterType'] : 'all';

    // Start building the SQL query
    $sql = "SELECT date, timeIn, timeOut FROM attendancelogs WHERE accountId = ?";
    $params = [$accountId]; 
    $types = 'i'; 

    // Append conditions to the SQL based on the filterType
    switch ($filterType) {
    case 'week':
        echo "Filtering by week"; // Debug statement
        $sql .= " AND YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)"; // Filter this week's data
        break;
    case 'month':
        echo "Filtering by month"; // Debug statement
        $sql .= " AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())"; // Filter this month's data
        break;
    case 'year':
        echo "Filtering by year"; // Debug statement
        $sql .= " AND YEAR(date) = YEAR(CURDATE())"; // Filter this year's data
        break;
    // The 'all' case doesn't need additional SQL
}

    $sql .= " ORDER BY date ASC"; // Add an ORDER BY clause

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch additional employee information if needed
    $employeeInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT firstname, lastname FROM account WHERE accountId = $accountId"));

    // Convert images to base64 for logos
    $leftLogoPath = '../../src/img/left-logo.png'; 
    $rightLogoPath = '../../src/img/right-logo.png'; 
    $leftLogoData = base64_encode(file_get_contents($leftLogoPath));
    $rightLogoData = base64_encode(file_get_contents($rightLogoPath));

    // Start the HTML content for PDF
    $html = '<div style="text-align:center; margin-bottom: 20px;">' .
            '<img src="data:image/png;base64,' . $leftLogoData . '" style="height:50px;"/> ' .
            '<h1 style="display:inline; margin: 0 10px;">QUEZON CITY UNIVERSITY</h1>' .
            '<img src="data:image/png;base64,' . $rightLogoData . '" style="height:50px;"/> ' .
            '<div style="clear:both;"></div>' . // Ensure the text goes below images and header
            '<h4 style="margin-top: 10px;">UPKEEP MAINTENANCE TEAM</h4>' . // Your additional text
            '</div>';

    $html .= '<h2 align="center">Attendance Log for ' . htmlspecialchars($employeeInfo['firstname']) . ' ' . htmlspecialchars($employeeInfo['lastname']) . '</h2>';
    $html .= '<style> th, td { text-align: center; vertical-align: middle; border: 1px solid #ddd; padding: 8px; } ' .
             'img { border-radius: 50%; width: 50px; height: 50px; object-fit: cover; border: 2px solid #000; } ' .
             'table { border-collapse: collapse; width: 100%; } </style>';
    $html .= '<table><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Total Hours</th></tr>';

    // Populate the table rows based on the fetched data
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $timeInFormatted = date('h:i A', strtotime($row['timeIn']));
            $isTimedOut = isset($row['timeOut']) && !empty($row['timeOut']);
            $timeOutFormatted = $isTimedOut ? date('h:i A', strtotime($row['timeOut'])) : 'Not Timed Out';

            // Calculate total hours
            if ($isTimedOut) {
                $totalHours = (strtotime($row['timeOut']) - strtotime($row['timeIn'])) / 3600 - 1; // Deduct one hour
            } else {
                $totalHours = 4; // Default to 4 hours if timeOut is NULL or empty
            }

            // Format total hours based on whether it's a whole number
            if (floor($totalHours) == $totalHours) {
                $totalHoursFormatted = floor($totalHours); // It's a whole number, no decimal point
            } else {
                $totalHoursFormatted = number_format($totalHours, 0); // One decimal place
            }
            $totalHoursFormatted .= ' hours';

            $html .= '<tr><td>' . $row['date'] . '</td><td>' . $timeInFormatted . '</td><td>' . $timeOutFormatted . '</td><td>' . $totalHoursFormatted . '</td></tr>';
        }
    } else {
        $html .= '<tr><td colspan="4">No data available</td></tr>';
    }

    $html .= '</table>';

    // Generate and stream the PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfName = 'attendance-log-' . $accountId . '.pdf';
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$pdfName\"");

    $dompdf->stream($pdfName, array("Attachment" => true));
    exit;
}
?>
