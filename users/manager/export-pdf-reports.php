<?php

ini_set('memory_limit', '20000M'); // Adjust the value as needed
ini_set('max_execution_time', 400); // Set maximum execution time to 300 seconds (10 minutes)

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use Dompdf\Dompdf;

extract($_POST);

if (isset($submit) && isset($status)) {
    $conn = connection();
    $sql = "SELECT assetId, category, building, floor, room, date, status FROM asset WHERE status = ?";
    $types = 's';
    $params = [$status];

    if (!empty($searchQuery)) {
        $sql .= " AND (assetId LIKE ? OR date LIKE ? OR category LIKE ? OR CONCAT(building, ' ', floor, ' ', room) LIKE ?)";
        $types .= 'ssss';
        $likeQuery = '%' . $searchQuery . '%';
        array_push($params, $likeQuery, $likeQuery, $likeQuery, $likeQuery);
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);

    // Convert images to base64
    $leftLogoPath = '../../src/img/left-logo.png'; // Replace with the path to your logo
    $rightLogoPath = '../../src/img/right-logo.png'; // Replace with the path to your logo
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


    $html .= '<h2 align="center">' . htmlspecialchars($status) . ' Assets</h2>';
    $html .= '<style> th, td { text-align: center; vertical-align: middle; border: 1px solid #ddd; padding: 8px; } ' .
    'img { border-radius: 50%; width: 50px; height: 50px; object-fit: cover; border: 2px solid #000; } ' .
    'table { border-collapse: collapse; width: 100%; } </style>';
    $html .= '<table style="width:100%; border-collapse:collapse;">';
    $html .= '<tr>';
    $html .= '<th style="border:1px solid #ddd; padding:8px;">Tracking #</th>';
    $html .= '<th style="border:1px solid #ddd; padding:8px;">Date & Time</th>';
    $html .= '<th style="border:1px solid #ddd; padding:8px;">Category</th>';
    $html .= '<th style="border:1px solid #ddd; padding:8px;">Location</th>';
    $html .= '<th style="border:1px solid #ddd; padding:8px;">Status</th>';
    $html .= '</tr>';

    if (mysqli_num_rows($query) > 0) {
        while ($data = mysqli_fetch_assoc($query)) {
            $html .= '<tr>';
            $html .= '<td style="border:1px solid #ddd; padding:8px;">' . htmlspecialchars($data["assetId"]) . '</td>';
            $html .= '<td style="border:1px solid #ddd; padding:8px;">' . htmlspecialchars($data["date"]) . '</td>';
            $html .= '<td style="border:1px solid #ddd; padding:8px;">' . htmlspecialchars($data["category"]) . '</td>';
            $html .= '<td style="border:1px solid #ddd; padding:8px;">' . htmlspecialchars($data["building"] . ' / ' . $data["floor"] . ' / ' . $data["room"]) . '</td>';
            $html .= '<td style="border:1px solid #ddd; padding:8px;">' . htmlspecialchars($data["status"]) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="5" style="border:1px solid #ddd; padding:8px; text-align:center;">No data available</td></tr>';
    }
    $html .= '</table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper("A4", "portrait");
    $dompdf->render();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . str_replace(' ', '-', strtolower($status)) . '_assets.pdf"');

    $dompdf->stream(str_replace(' ', '-', strtolower($status)) . '_assets.pdf', ['Attachment' => true]);
    exit;
}
?>
