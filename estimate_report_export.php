<?php
require 'vendor/autoload.php';
require 'scripts/settings.php'; // DB connection
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = 'SELECT * FROM billing_estimate WHERE 1=1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['date_type'] == 'booking_wise') {
        $sql .= ' AND created_on >= "' . $_POST['allot_from'] . '" AND created_on < "' . date("Y-m-d", strtotime($_POST['allot_to']) + 86400) . '"';
    } elseif ($_POST['date_type'] == 'allotment_wise') {
        $sql .= ' AND booking_date >= "' . $_POST['allot_from'] . '" AND booking_date < "' . date("Y-m-d", strtotime($_POST['allot_to']) + 86400) . '"';
    }

    if (!empty($_POST['guest_name'])) {
        $sql .= ' AND guest_name = "' . $_POST['guest_name'] . '"';
    }
    if (!empty($_POST['contact_number'])) {
        $sql .= ' AND contact_number = "' . $_POST['contact_number'] . '"';
    }
    if (!empty($_POST['mop'])) {
        $sql .= ' AND mop = "' . $_POST['mop'] . '"';
    }
    if (!empty($_POST['booking_date'])) {
        $sql .= ' AND booking_date = "' . $_POST['booking_date'] . '"';
    }
}

$sql .= ' ORDER BY sno';
$result = execute_query($sql);

// Spreadsheet setup
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Headers
$headers = ['S.No.', 'Booking Date', 'Guest Name', 'Address', 'Contact Number', 'Total Amount', 'Advance Amount', 'Mode of Payment', 'Particular'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
    $col++;
}

// Rows
$i = 2;
$sno = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$i", $sno++);
    $sheet->setCellValue("B$i", $row['booking_date']);
    $sheet->setCellValue("C$i", $row['guest_name']);
    $sheet->setCellValue("D$i", $row['address']);
    $sheet->setCellValue("E$i", $row['contact_number']);
    $sheet->setCellValue("F$i", $row['total_amount']);
    $sheet->setCellValue("G$i", $row['advance_amount']);
    $sheet->setCellValue("H$i", strtoupper($row['mop']));
    $sheet->setCellValue("I$i", $row['particular']);
    $i++;
}

// Auto-size columns
foreach (range('A', 'I') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Output Excel file
if (ob_get_length()) ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="estimate_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
