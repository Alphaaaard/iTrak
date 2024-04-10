<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

if (isset($_POST['submit']) && $_POST['submit'] == 'Export to Excel') {
    $conn = connection();
    $status = $_POST['status'];
    $searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

    // Prepare the SQL query with optional search term
    $sql = "SELECT assetId, category, building, floor, room, date, status FROM asset WHERE status = ?";
    $types = 's'; // Type for status
    $params = [$status]; // Parameters for the prepared statement

    // If a search query is provided, extend the SQL query to include a LIKE clause
    if (!empty($searchQuery)) {
        $searchTerm = "%$searchQuery%";
        $sql .= " AND (assetId LIKE ? OR date LIKE ? OR category LIKE ? OR CONCAT(building, ' ', floor, ' ', room) LIKE ?)";
        $types .= 'ssss'; // Add types for new parameters
        $params = array_merge($params, array_fill(0, 4, $searchTerm)); // Duplicate $searchTerm for each LIKE clause
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Define the cell style with center alignment and bold font
    $styleArray = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];

    // Set headers for the spreadsheet and merge cells for each header
    $sheet->setCellValue('A1', 'Tracking #');
    $sheet->mergeCells('A1:B1');
    $sheet->setCellValue('C1', 'Date & Time');
    $sheet->mergeCells('C1:D1');
    $sheet->setCellValue('E1', 'Category');
    $sheet->mergeCells('E1:F1');
    $sheet->setCellValue('G1', 'Location');
    $sheet->mergeCells('G1:J1'); // Merging four cells for Location
    $sheet->setCellValue('K1', 'Status');
    $sheet->mergeCells('K1:L1');

    // Apply style to header cells
    $sheet->getStyle('A1:L1')->applyFromArray($styleArray);

    $rowNumber = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNumber, $row['assetId']);
        $sheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('C' . $rowNumber, $row['date']);
        $sheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('E' . $rowNumber, $row['category']);
        $sheet->mergeCells('E' . $rowNumber . ':F' . $rowNumber); // Merges cells for each data row
        $sheet->setCellValue('G' . $rowNumber, $row['building'] . ' / ' . $row['floor'] . ' / ' . $row['room']);
        $sheet->mergeCells('G' . $rowNumber . ':J' . $rowNumber); // Merges four cells for Location
        $sheet->setCellValue('K' . $rowNumber, $row['status']);
        $sheet->mergeCells('K' . $rowNumber . ':L' . $rowNumber); // Merges cells for Status

        // Apply center style to data cells
        $sheet->getStyle('A' . $rowNumber . ':L' . $rowNumber)->applyFromArray($styleArray);
        $rowNumber++;
    }

    // Redirect output to the browser
    ob_end_clean();
    ob_start();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($status) . '_assets.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    ob_end_flush();

    exit;
}
?>
