<?php
require 'scripts/settings.php'; // For DB connection and execute_query
require 'vendor/autoload.php';  // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Customer Payments');

// Header row
$headers = ['S.No.', 'Guest Name', 'Mobile', 'Mode of Payment', 'Payment For', 'Amount', 'Date Of Payment'];
$sheet->fromArray($headers, null, 'A1');

// SQL query base
$sql = 'SELECT * FROM customer_transactions WHERE type = "payment"';
if (isset($_POST['mop'])) {
    $_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to']) + 86400);
    $sql .= ' AND timestamp >= "' . $_POST['allot_from'] . '" AND timestamp <= "' . $_POST['allot_to'] . '"';
    if ($_POST['mop'] == 'cash') {
        $sql .= ' AND mop = "cash"';
    } elseif ($_POST['mop'] == 'credit') {
        $sql .= ' AND mop = "credit"';
    }
} else {
    $sql .= ' AND timestamp >= "' . date("Y-m-d") . '" AND timestamp < "' . date("Y-m-d", strtotime("+1 day")) . '"';
}

// Execute query
$run = execute_query($sql);
$i = 1;
$rowIndex = 2;
$totalAmount = 0;

while ($row = mysqli_fetch_array($run)) {
    $sql1 = 'SELECT * FROM customer WHERE sno = ' . $row['cust_id'];
    $result = execute_query($sql1);
    $details = mysqli_fetch_assoc($result);

    $guestName = $details['cust_name'] ?? 'N/A';
    $mobile = $details['mobile'] ?? 'N/A';
    $mop = ($row['mop'] == 'cash') ? 'Cash' : 'Credit Card';
    $paymentFor = $row['type'];
    $amount = floatval($row['amount']);
    $date = $row['timestamp'];

    $sheet->fromArray([
        $i++, $guestName, $mobile, $mop, $paymentFor, $amount, $date
    ], null, 'A' . $rowIndex++);

    $totalAmount += $amount;
}

// Add total row
$sheet->setCellValue('A' . $rowIndex, 'Total:');
$sheet->mergeCells("A{$rowIndex}:E{$rowIndex}");
$sheet->setCellValue('F' . $rowIndex, $totalAmount);

// Format
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output to browser
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Customer_Payment_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
