<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use Dompdf\Dompdf;

if (isset($_POST['submit']) && isset($_POST['role'])) {
    $conn = connection();

    $role = $_POST['role'];
    $searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

    $sql = "SELECT accountID, CONCAT(firstName, ' ', lastName) AS fullName, role, picture FROM account WHERE role = ?";
    $params = [$role];
    $types = 's';

    if (!empty($searchQuery)) {
        $sql .= " AND (firstName LIKE ? OR lastName LIKE ?)";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
        $types .= 'ss';
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $html = '<h2 align="center">'.htmlspecialchars($role).' Accounts</h2>';
    $html .= '<style>
        th, td {text-align: center; vertical-align: middle; border: 1px solid #ddd; padding: 8px;}
        img {border-radius: 50%; width: 50px; height: 50px; object-fit: cover; border: 2px solid #000;}
        table {border-collapse: collapse; width: 100%;}
        </style>';
    $html .= '<table><tr>';
    $html .= '<th>Account ID</th>';
    $html .= '<th>Picture</th>';
    $html .= '<th>Name</th>';
    $html .= '<th>Role</th>';
    $html .= '</tr>';

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $base64Image = base64_encode($row['picture']);
            $html .= '<tr>';
            $html .= '<td>'.htmlspecialchars($row['accountID']).'</td>';
            $html .= '<td><img src="data:image/jpeg;base64,'. $base64Image .'"></td>';
            $html .= '<td>'.htmlspecialchars($row['fullName']).'</td>';
            $html .= '<td>'.htmlspecialchars($row['role']).'</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="4">No data available</td></tr>';
    }
    $html .= '</table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfName = str_replace(' ', '-', strtolower($role)) . '-accounts.pdf';
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$pdfName\"");

    $dompdf->stream($pdfName, array("Attachment" => true));
    exit;
}
?>
