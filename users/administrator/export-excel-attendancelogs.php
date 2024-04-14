<?php

ini_set('memory_limit', '1024M'); // Adjust the value as needed

include_once("../../config/connection.php");
// require 'C:\xampp\htdocs\iTrak\vendor\autoload.php';
require '/home/u579600805/domains/itrak.site/public_html/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if (isset($_POST['submit']) && isset($_POST['accountId'])) {
    $conn = connection();

    $accountId = $_POST['accountId'];
    $filterType = $_POST['filterType'] ?? 'all'; // Default to 'all' if not provided

    $sql = "SELECT date, timeIn, timeOut FROM attendancelogs WHERE accountId = ?";
    $params = [$accountId];
    $types = 'i';

    if ($filterType === 'week') {
        $sql .= " AND YEARWEEK(`date`, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($filterType === 'month') {
        $sql .= " AND MONTH(`date`) = MONTH(CURDATE()) AND YEAR(`date`) = YEAR(CURDATE())";
    } elseif ($filterType === 'year') {
        $sql .= " AND YEAR(`date`) = YEAR(CURDATE())";
    }

    $sql .= " ORDER BY date ASC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->setCellValue('A1', 'Date');
    $sheet->mergeCells('A1:B1');
    $sheet->setCellValue('C1', 'Time In');
    $sheet->mergeCells('C1:D1');
    $sheet->setCellValue('E1', 'Time Out');
    $sheet->mergeCells('E1:F1');
    $sheet->setCellValue('G1', 'Total Hours');
    $sheet->mergeCells('G1:H1');

    // Style for header and rows
    $centerStyleArray = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'font' => [
            'bold' => true,
        ],
    ];

    $sheet->getStyle('A1:H1')->applyFromArray($centerStyleArray);

    $rowNumber = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $timeIn = $row['timeIn'] ? date('h:i A', strtotime($row['timeIn'])) : '---';
        $timeOut = $row['timeOut'] ? date('h:i A', strtotime($row['timeOut'])) : '';
        $timeInNextDay = strtotime($row['timeIn']) + (24 * 60 * 60);

        if ($row['timeOut'] && (time() > $timeInNextDay)) {
            $totalHours = floor((strtotime($row['timeOut']) - strtotime($row['timeIn'])) / 3600) - 1 . ' hours';
        } elseif (!$row['timeOut'] && (time() > $timeInNextDay)) {
            $totalHours = '4 hours';
            $timeOut = 'Not Timed Out';
        } else {
            $totalHours = '';  // Leave it empty if not past midnight and no timeout
        }

        $sheet->setCellValue('A' . $rowNumber, $row['date']);
        $sheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
        $sheet->setCellValue('C' . $rowNumber, $timeIn);
        $sheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);
        $sheet->setCellValue('E' . $rowNumber, $timeOut);
        $sheet->mergeCells('E' . $rowNumber . ':F' . $rowNumber);
        $sheet->setCellValue('G' . $rowNumber, $totalHours);
        $sheet->mergeCells('G' . $rowNumber . ':H' . $rowNumber);

        // Apply center style to each row
        $sheet->getStyle('A' . $rowNumber . ':H' . $rowNumber)->applyFromArray($centerStyleArray);

        $rowNumber++;
    }

        // Set column widths to make the content fit nicely
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Attendance-Logs.xlsx"');
        header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
}
?>
