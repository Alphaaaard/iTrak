<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if (isset($_POST['submit']) && $_POST['submit'] == 'Export to Excel') {
    $conn = connection();
    $status = $_POST['status'];
    $sql = "SELECT assetId, category, building, floor, room, date, status FROM asset WHERE status = ?;";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Define the cell style with center alignment
    $centerStyleArray = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];

    // Set headers for the spreadsheet and merge cells for each header to span two columns
    $sheet->setCellValue('A1', 'Tracking #');
    $sheet->mergeCells('A1:B1');
    $sheet->setCellValue('C1', 'Date & Time');
    $sheet->mergeCells('C1:D1');
    $sheet->setCellValue('E1', 'Category');
    $sheet->mergeCells('E1:F1');
    $sheet->setCellValue('G1', 'Location');
    $sheet->mergeCells('G1:I1');
    $sheet->setCellValue('J1', 'Status');
    $sheet->mergeCells('J1:K1');

    // Apply center style to header cells
    $sheet->getStyle('A1:K1')->applyFromArray($centerStyleArray);

    $rowNumber = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNumber, $row['assetId']);
        $sheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('C' . $rowNumber, $row['date']);
        $sheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('E' . $rowNumber, $row['category']);
        $sheet->mergeCells('E' . $rowNumber . ':F' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('G' . $rowNumber, $row['building'] . ' / ' . $row['floor'] . ' / ' . $row['room']);
        $sheet->mergeCells('G' . $rowNumber . ':I' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('J' . $rowNumber, $row['status']);
        $sheet->mergeCells('J' . $rowNumber . ':K' . $rowNumber); // Merges cells for each data row
        // Apply center style to data cells that have been merged
        $sheet->getStyle('A' . $rowNumber . ':K' . $rowNumber)->applyFromArray($centerStyleArray);
        $rowNumber++;
    }

   // Redirect output to the browser
   ob_end_clean();
   ob_start();

   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
   header('Content-Disposition: attachment; filename="exported_data.xlsx"');
   header('Cache-Control: max-age=0');
   
   $writer = new Xlsx($spreadsheet);
   $writer->save('php://output');
   ob_end_flush();

   exit;
}
?>
