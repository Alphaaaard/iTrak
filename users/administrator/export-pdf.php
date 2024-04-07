<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';
use Dompdf\Dompdf;
extract($_POST);

if (isset($submit) && isset($status)) { // Check if the form is submitted and the status is set
    $conn = connection(); // Call the connection function to get the mysqli object
    $sql = "SELECT assetId, category, building, floor, room, date, status FROM asset WHERE status = ?;"; // Use prepared statement
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $status); // 's' specifies the variable type => 'string'
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);

    $html = '';
    $html .= '
        <h2 align="center">'.htmlspecialchars($status).' Assets</h2> <!-- Dynamically set the heading based on status -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Tracking #</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Date & Time</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Category</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Location</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Status</th>
            </tr>';
    if (mysqli_num_rows($query) > 0) {
        while ($data = mysqli_fetch_assoc($query)) {
            $html .= '
            <tr>
                <td style="border:1px solid #ddd; padding:8px; text-align:left;">'.htmlspecialchars($data["assetId"]).'</td>
                <td style="border:1px solid #ddd; padding:8px; text-align:left;">'.htmlspecialchars($data["date"]).'</td>
                <td style="border:1px solid #ddd; padding:8px; text-align:left;">'.htmlspecialchars($data["category"]).'</td>
                <td style="border:1px solid #ddd; padding:8px; text-align:left;">'.htmlspecialchars($data["building"].' / '.$data["floor"].' / '.$data["room"]).'</td>
                <td style="border:1px solid #ddd; padding:8px; text-align:left;">'.htmlspecialchars($data["status"]).'</td>
            </tr>';
        }
        $html .= '</table>';
    } else {
        // If no rows are returned, display a message
        $html = '<h2 align="center">No data available for '.htmlspecialchars($status).' Assets</h2>';
    }
    
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper("A4", "portrait");
    $dompdf->render();

    // Headers for PDF output
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.str_replace(' ', '-', strtolower($status)).'.pdf"');

    // Output the PDF
    $dompdf->stream(str_replace(' ', '-', strtolower($status)).'.pdf', array("Attachment" => true));
    exit;
}
?>
