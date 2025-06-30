<?php
require 'scripts/settings.php'; // your DB connection & execute_query()
require 'vendor/autoload.php';  // PhpSpreadsheet autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Build the SQL query with filters (same logic you used)
$sql = 'SELECT * FROM `restaurant_proforma_invoice` WHERE 1=1';

if (isset($_POST['date_type'])) {
    $dtfrom = date("Y-m-d", strtotime($_POST['datefrom']));
    $dtto = isset($_POST['dateto']) && $_POST['dateto'] != '' ? date("Y-m-d", strtotime($_POST['dateto'])) : date("Y-m-d");
    if ($_POST['date_type'] == "cdt") {
        $sql .= ' AND creation_time BETWEEN "' . $dtfrom . '" AND "' . $dtto . '"';
    } elseif ($_POST['date_type'] == "cincoutdt") {
        $sql .= ' AND cindt BETWEEN "' . $dtfrom . '" AND "' . $dtto . '"';
    }
} elseif (isset($_POST['datefrom']) && $_POST['datefrom'] != '') {
    $dtfrom = date("Y-m-d", strtotime($_POST['datefrom']));
    $dtto = isset($_POST['dateto']) && $_POST['dateto'] != '' ? date("Y-m-d", strtotime($_POST['dateto'])) : date("Y-m-d");
    $sql .= ' AND creation_time BETWEEN "' . $dtfrom . '" AND "' . $dtto . '"';
}

if (isset($_POST['cus']) && $_POST['cus'] != '') {
    $sql .= " AND guest_name LIKE '%" . $_POST['cus'] . "%'";
}

$result = execute_query($sql);

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Proforma Invoice Report');

// Header row
$headers = [
    'S.No.',
    'Customer Name',
    'Company Name',
    'Mobile No.',
    'GSTIN',
    'PIN Code',
    'Date',
    'Event Date',
    'Amount',
    'SGST (2.5%)',
    'CGST (2.5%)',
    'Grand Total',
    'Edit Link',
    'Print Link'
];
$sheet->fromArray($headers, null, 'A1');

// Data rows start from 2
$rowIndex = 2;

$total_amount = 0;
$total_sgst = 0;
$total_cgst = 0;
$total_grand = 0;
$serial = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['creation_time'] ? date("d-m-Y", strtotime($row['creation_time'])) : '--';
    $event_date = $row['cindt'] ? date("d-m-Y", strtotime($row['cindt'])) : '--';

    // Prepare edit and print URLs (as text)
    $edit_url = "restaurant_proforma.php?e_id=" . $row['sno'];
    $print_url = "restaurant_print_proforma.php?id=" . $row['sno'];

    $sheet->setCellValue('A' . $rowIndex, $serial++);
    $sheet->setCellValue('B' . $rowIndex, $row['guest_name']);
    $sheet->setCellValue('C' . $rowIndex, $row['company_name']);
    $sheet->setCellValue('D' . $rowIndex, $row['mob_no']);
    $sheet->setCellValue('E' . $rowIndex, $row['gstin']);
    $sheet->setCellValue('F' . $rowIndex, $row['pin_code']);
    $sheet->setCellValue('G' . $rowIndex, $date);
    $sheet->setCellValue('H' . $rowIndex, $event_date);
    $sheet->setCellValue('I' . $rowIndex, $row['amount']);
    $sheet->setCellValue('J' . $rowIndex, $row['sgst']);
    $sheet->setCellValue('K' . $rowIndex, $row['cgst']);
    $sheet->setCellValue('L' . $rowIndex, $row['totel']);
    $sheet->setCellValue('M' . $rowIndex, $edit_url);
    $sheet->setCellValue('N' . $rowIndex, $print_url);

    // Accumulate totals
    $total_amount += floatval($row['amount']);
    $total_sgst += floatval($row['sgst']);
    $total_cgst += floatval($row['cgst']);
    $total_grand += floatval($row['totel']);

    $rowIndex++;
}

// Write totals row
$sheet->setCellValue('A' . $rowIndex, '');
$sheet->setCellValue('B' . $rowIndex, '');
$sheet->setCellValue('C' . $rowIndex, '');
$sheet->setCellValue('D' . $rowIndex, '');
$sheet->setCellValue('E' . $rowIndex, '');
$sheet->setCellValue('F' . $rowIndex, '');
$sheet->setCellValue('G' . $rowIndex, 'Total:');
$sheet->setCellValue('H' . $rowIndex, '');
$sheet->setCellValue('I' . $rowIndex, round($total_amount, 3));
$sheet->setCellValue('J' . $rowIndex, round($total_sgst, 3));
$sheet->setCellValue('K' . $rowIndex, round($total_cgst, 3));
$sheet->setCellValue('L' . $rowIndex, round($total_grand, 3));
$sheet->setCellValue('M' . $rowIndex, '');
$sheet->setCellValue('N' . $rowIndex, '');

// Auto-size columns A to N
foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set header to force download Excel file
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Proforma_Invoice_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
