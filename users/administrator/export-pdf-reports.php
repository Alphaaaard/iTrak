<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed
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

    // Start the HTML content
    $html = '<div style="text-align:center; margin-bottom: 20px;">';
    $html .= '<img src="data:image/png;base64,' . $leftLogoData . '" style="height:50px;"/> ';
    $html .= '<h1 style="display:inline; margin: 0 10px;">QUEZON CITY UNIVERSITY</h1>';
    $html .= '<img src="data:image/png;base64,' . $rightLogoData . '" style="height:50px;"/> ';
    $html .= '<div style="clear:both;"></div>'; // Ensure the text goes below images and header
    $html .= '<h4 style="margin-top: 10px;">ITRAK MAINTENANCE TEAM</h4>'; // Your additional text
    $html .= '</div>';


    $html .= '<h2 align="center">' . htmlspecialchars($status) . ' Assets</h2>';
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
