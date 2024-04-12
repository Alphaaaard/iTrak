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
    $sheet->setCellValue('A1', 'Account ID');
    $sheet->mergeCells('A1:B1');
    // $sheet->setCellValue('C1', 'Picture');
    // $sheet->mergeCells('C1:D1');
    $sheet->setCellValue('C1', 'Name');
    $sheet->mergeCells('C1:D1');
    $sheet->setCellValue('E1', 'Role');
    $sheet->mergeCells('E1:F1');
    $sheet->getStyle('A1:F1')->applyFromArray($headerStyleArray);

    // Set data rows
    $rowNumber = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNumber, $row['accountID']);
        $sheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
        // if ($row['picture']) {
            // Add image to the sheet, uncomment and handle the image as needed
        // }
        $sheet->setCellValue('C' . $rowNumber, $row['fullName']);
        $sheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);
        $sheet->setCellValue('E' . $rowNumber, $row['role']);
        $sheet->mergeCells('E' . $rowNumber . ':F' . $rowNumber);
        
        // Apply styles to each row
        $sheet->getStyle('A' . $rowNumber . ':F' . $rowNumber)->applyFromArray($headerStyleArray);
        
        $rowNumber++;
    }

    // Set column widths to make the content fit nicely
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('E')->setWidth(20);

    // Redirect output to the browser
    ob_end_clean();
    ob_start();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($role) . '_accounts.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    ob_end_flush();

    exit;
}
?>
