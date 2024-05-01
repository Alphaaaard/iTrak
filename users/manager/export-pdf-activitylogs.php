<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use Dompdf\Dompdf;

if (isset($_POST['submit']) && isset($_POST['tab'])) {
    $conn = connection();

    $tab = $_POST['tab'];
    $searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

    // Modified SQL to join account table and fetch names
    $sql = "SELECT a.accountID, a.firstName, a.lastName, l.date, l.action FROM activitylogs l INNER JOIN account a ON l.accountID = a.accountID WHERE l.tab = ?";
    $params = [$tab];
    $types = 's';

    if (!empty($searchQuery)) {
        $sql .= " AND (l.action LIKE ? OR a.firstName LIKE ? OR a.lastName LIKE ?)";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
        $types .= 'sss';
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

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
    '<h1 style="display:inline; vertical-align:middle; margin: 0 20px;">QUEZON CITY UNIVERSITY</h1>' .
    '<div style="display:inline-block; vertical-align:middle; height:50px;">' .
    '<img src="data:image/png;base64,' . $rightLogoData . '" style="height:100%;"/>' .
    '</div>' .
    '<div style="clear:both;"></div>' .
    '<h4 style="margin-top: 0px;">673 Quirino Hwy, Novaliches, Quezon City, Metro Manila</h4>' .
    '<div style="clear:both;"></div>' .
    '<h4 style="margin-top: 5px;">ITRAK MAINTENANCE TEAM</h4>' .
    '<hr style="border:0; height:2px; background:#333; margin-top:5px;" />' . // Horizontal line
    '</div>';

    $html .= '<h2 align="center">Activity Logs (' . htmlspecialchars($tab) . ')</h2>';
    $html .= '<style> th, td { text-align: center; vertical-align: middle; border: 1px solid #ddd; padding: 8px; } ' .
    'img { border-radius: 50%; width: 50px; height: 50px; object-fit: cover; border: 2px solid #000; } ' .
    'table { border-collapse: collapse; width: 100%; } </style>';
    $html .= '<table><tr>';
    $html .= '<th>Name</th>';
    $html .= '<th>Date</th>';
    $html .= '<th>Action</th>';
    $html .= '</tr>';

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $fullName = htmlspecialchars($row['firstName']) . ' ' . htmlspecialchars($row['lastName']);
            $html .= '<tr>';
            $html .= '<td>' . $fullName . '</td>';
            $html .= '<td>' . htmlspecialchars($row['date']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['action']) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="3">No data available</td></tr>';
    }
    $html .= '</table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfName = strtolower(str_replace(' ', '-', $tab)) . '-activity-logs.pdf';
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$pdfName\"");

    $dompdf->stream($pdfName, array("Attachment" => true));
    exit;
}
?>
