<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use Dompdf\Dompdf;

if (isset($_POST['submit']) && isset($_POST['accountId'])) {
    $conn = connection();

    $accountId = $_POST['accountId']; // Retrieve the accountId from the POST data
    // Retrieve the filter type from POST data
    $filterType = $_POST['filterType'] ?? 'all'; // Default to 'all' if not provided

    // Start building the SQL query
    $sql = "SELECT date, timeIn, timeOut FROM attendancelogs WHERE accountId = ?";
    $params = [$accountId];
    $types = 'i';

    // Depending on the filterType, append the SQL where clause conditions
    if ($filterType === 'week') {
        $sql .= " AND YEARWEEK(`date`, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($filterType === 'month') {
        $sql .= " AND MONTH(`date`) = MONTH(CURDATE()) AND YEAR(`date`) = YEAR(CURDATE())";
    } elseif ($filterType === 'year') {
        $sql .= " AND YEAR(`date`) = YEAR(CURDATE())";
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
    '<div style="display:inline-block; vertical-align:middle; height:50px;">' .
    '<img src="data:image/png;base64,' . $leftLogoData . '" style="height:100%;"/>' .
    '</div>' .
    '<h1 style="display:inline; vertical-align:middle; margin: 0 10px;">QUEZON CITY UNIVERSITY</h1>' .
    '<div style="display:inline-block; vertical-align:middle; height:50px;">' .
    '<img src="data:image/png;base64,' . $rightLogoData . '" style="height:100%;"/>' .
    '</div>' .
    '<div style="clear:both;"></div>' .
    '<h4 style="margin-top: 0px;">673 Quirino Hwy, Novaliches, Quezon City, Metro Manila</h4>' .
    '<div style="clear:both;"></div>' .
    '<h4 style="margin-top: 5px;">ITRAK MAINTENANCE TEAM</h4>' .
    '<hr style="border:0; height:2px; background:#333; margin-top:5px;" />' . // Horizontal line
    '</div>';

    $html .= '<h2 align="center">Attendance Logs</h2>';
    $html .= '<h4 align="left">Name: '. htmlspecialchars($employeeInfo['firstname']) . ' ' . htmlspecialchars($employeeInfo['lastname']) .'</h4>';
    $html .= '<style> th, td { text-align: center; vertical-align: middle; border: 1px solid #ddd; padding: 8px; } ' .
        'img { border-radius: 50%; width: 50px; height: 50px; object-fit: cover; border: 2px solid #000; } ' .
        'table { border-collapse: collapse; width: 100%; } </style>';
    $html .= '<table><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Total Hours</th></tr>';

    // Populate the table rows based on the fetched data
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
        $timeInFormatted = date('h:i A', strtotime($row['timeIn']));
        $isTimedOut = isset($row['timeOut']) && !empty($row['timeOut']);
        $timeInNextDay = strtotime($row['timeIn']) + (24 * 60 * 60); // Adding 24 hours to timeIn

        // Determine the content for timeOutFormatted
        if ($isTimedOut) {
            $timeOutFormatted = date('h:i A', strtotime($row['timeOut']));
        } elseif (time() > $timeInNextDay) {
            $timeOutFormatted = 'Not Timed Out'; // Only show 'Not Timed Out' if it's past midnight
        } else {
            $timeOutFormatted = ''; // Leave TimeOut empty if it's not past midnight and not timed out
        }

        // Calculate total hours
        if ($isTimedOut) {
            $totalHours = (strtotime($row['timeOut']) - strtotime($row['timeIn'])) / 3600 - 1; // Deduct one hour
        } elseif (time() > $timeInNextDay) {
            $totalHours = 4; // Default to 4 hours if it's past midnight and not timed out
        } else {
            $totalHours = null; // Leave total hours empty if it's not past midnight
        }

        // Format total hours if not null
        if ($totalHours !== null) {
            $totalHoursFormatted = floor($totalHours) . ' hours';
        } else {
            $totalHoursFormatted = ''; // Leave the total hours cell empty
        }

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
