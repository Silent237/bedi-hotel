<?php
require 'scripts/settings.php'; // for DB and execute_query
require 'vendor/autoload.php';  // for PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Setup spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Estimate Report');

// Define headers
$headers = ['S.No.', 'Company Name / Guest Name', 'GSTIN', 'Item Total', 'Service Charge', 'Taxable Amount', 'SGST', 'CGST', 'Invoice Amount', 'Discount', 'Amount Payable', 'Sale Date', 'Unit', 'Mode of Payment', 'Table', 'Invoice No.'];
$sheet->fromArray($headers, null, 'A1');

// Fetch data from database as per your query
$sql = "SELECT * FROM invoice_sale_restaurant WHERE 1=1"; // Add your filter conditions here
$result = execute_query($sql);

$i = 1;
$rowIndex = 2;

// Totals
$total_item = $total_service = $total_taxable = $total_sgst = $total_cgst = $total_invoice = $total_discount = $total_amount = $total_qty = 0;

while ($row = mysqli_fetch_array($result)) {
    $guest = $row['company_name'];
    if ($row['concerned_person']) {
        $guest .= "\nGuest Name: " . $row['concerned_person'];
    }

    $sgst = round(($row['service_charge_tax_amount'] ?? 0) / 2, 2);
    $cgst = $sgst;
    $taxable_w_sc = $row['taxable_amount'] + $row['service_charge_amount'];
    $invoice_amt = $row['total_amount'] + $row['service_charge_total'];
    $payable = $row['grand_total'];

    $sheet->fromArray([
        $i++,
        $guest,
        $row['tin'],
        $row['taxable_amount'],
        $row['service_charge_amount'],
        $taxable_w_sc,
        $sgst,
        $cgst,
        $invoice_amt,
        $row['tot_disc'] ?? '',
        $payable,
        date("d-m-Y", strtotime($row['timestamp'])),
        $row['quantity'],
        strtoupper($row['mode_of_payment']),
        strpos($row['storeid'], "room") === false ? 'T-' . get_table($row['storeid']) : 'R-' . get_room_name($row['storeid']),
        $row['invoice_no']
    ], null, 'A' . $rowIndex++);

    // Update totals
    $total_item += $row['taxable_amount'];
    $total_service += $row['service_charge_amount'];
    $total_taxable += $taxable_w_sc;
    $total_sgst += $sgst;
    $total_cgst += $cgst;
    $total_invoice += $invoice_amt;
    $total_discount += $row['tot_disc'];
    $total_amount += $payable;
    $total_qty += $row['quantity'];
}

// Add total row
$sheet->fromArray([
    '',
    'Total',
    '',
    round($total_item, 2),
    round($total_service, 2),
    round($total_taxable, 2),
    round($total_sgst, 2),
    round($total_cgst, 2),
    round($total_invoice, 2),
    round($total_discount, 2),
    round($total_amount, 2),
    '',
    $total_qty,
    '',
    '',
    ''
], null, 'A' . $rowIndex);

// Auto width
foreach (range('A', 'P') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output Excel
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Estimate_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;


function get_room_name($room_id) {
    $id = str_replace("room_", "", $room_id);
    $sql = "SELECT room_name FROM room_master WHERE sno = '$id'";
    $result = execute_query($sql);
    $row = mysqli_fetch_assoc($result);
    return $row['room_name'] ?? 'N/A';
}
?>
