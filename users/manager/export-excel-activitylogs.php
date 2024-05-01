<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

if (isset($_POST['submit']) && $_POST['submit'] == 'Export to Excel') {
    $conn = connection();
    $tab = $_POST['tab']; // Using tab to filter the records
    $searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';

    // Adjusted SQL to fetch from activitylogs joined with the account table
    $sql = "SELECT CONCAT(a.firstName, ' ', a.lastName) AS fullName, l.date, l.action FROM activitylogs l INNER JOIN account a ON l.accountID = a.accountID WHERE l.tab = ?";
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

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Apply styles to header
    $headerStyleArray = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    // Set headers and merge cells
    $sheet->setCellValue('A1', 'Name');
    $sheet->mergeCells('A1:B1');
    $sheet->setCellValue('C1', 'Date');
    $sheet->mergeCells('C1:D1');
    $sheet->setCellValue('E1', 'Action');
    $sheet->mergeCells('E1:H1');
    $sheet->getStyle('A1:H1')->applyFromArray($headerStyleArray);

    // Set data rows
    $rowNumber = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNumber, $row['fullName']);
        $sheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
        $sheet->setCellValue('C' . $rowNumber, $row['date']);
        $sheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);
        $sheet->setCellValue('E' . $rowNumber, $row['action']);
        $sheet->mergeCells('E' . $rowNumber . ':H' . $rowNumber);
        
        $sheet->getStyle('A' . $rowNumber . ':H' . $rowNumber)->applyFromArray($headerStyleArray);
        
        $rowNumber++;
    }

    // Set column widths to make the content fit nicely
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(30);

    // Redirect output to the browser
    ob_end_clean();
    ob_start();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($tab) . '-activity-logs.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    ob_end_flush();

    exit;
}
?>
