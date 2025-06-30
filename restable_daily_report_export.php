<?php
require 'scripts/settings.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Assuming $result_data, $start, $end, $total_results are already defined as in your code

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$headers = [
    'S.No.', 'Table', 'Invoice No.', 'Item Total', 'Service Charge', 'Taxable Amount',
    'SGST', 'CGST', 'Invoice Amount', 'Discount', 'Amount Payable',
    'Sale Date', 'Unit', 'Mode of Payment', 'Paid Amount'
];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col.'1', $header);
    $col++;
}

$rowNum = 2;

$tot_service_charge = 0;
$total_credit_paid = 0;
$tot_taxable_w_sc = 0;
$tot_qty = 0;
$tot_sgst = 0;
$tot_cgst = 0;
$tot_taxable = 0;
$tot_amount = 0;
$tot_invoice = 0;
$tot_discount = 0;

for ($pgid = $start; $pgid < $end; $pgid++) {
    if ($pgid == $total_results) {
        break;
    }
    mysqli_data_seek($result_data, $pgid);
    $row = mysqli_fetch_array($result_data);

    // Calculations as in your code
    $row['service_charge_amount'] = ($row['service_charge_amount'] == '' ? 0 : $row['service_charge_amount']);
    $row['service_charge_tax_amount'] = $row['service_charge_tax_amount'] == '' ? 0 : $row['service_charge_tax_amount'];
    $row['service_charge_vat'] = round($row['service_charge_tax_amount'] / 2, 2);
    $row['service_charge_sat'] = round($row['service_charge_tax_amount'] / 2, 2);
    $row['tot_vat'] = $row['service_charge_vat'] + $row['tot_vat'];
    $row['tot_sat'] = $row['service_charge_sat'] + $row['tot_sat'];

    $i = $pgid + 1;

    // Table or Room logic
    if (strpos($row['storeid'], "room") === false) {
        $tableName = 'T-' . get_table($row['storeid']);
    } else {
        $storeid = str_replace("room_", "", $row['storeid']);
        $sql = "SELECT * FROM `room_master` where sno=" . intval($storeid);
        $room_details = mysqli_fetch_assoc(execute_query($sql));
        $tableName = 'R-' . $room_details['room_name'];
    }

    // Paid Amount calculation for credit payment
    $showPaid = '';
    if (strtolower($row['mop_type']) == 'credit') {
        $sql_mop = 'SELECT * FROM `customer_transactions` WHERE `number`="' . $row['sno'] . '"';
        $row_mop = mysqli_fetch_array(execute_query($sql_mop));
        $sql_credit_check = 'SELECT * FROM `customer_transactions` WHERE `sno`="' . $row_mop['credit_bill_paid_sno'] . '"';
        $row_credit_check = mysqli_fetch_array(execute_query($sql_credit_check));
        $paid_amount = $row_mop['advance_set_amt'] + $row_mop['credit_set_amt'];
        if ($paid_amount == 0 OR $paid_amount == '') {
            $showPaid = 'UN-PAID';
            $paid_amount = 0;
        } else if ($paid_amount == $row_mop['amount']) {
            $showPaid = 'PAID (' . $row_credit_check['timestamp'] . ') Amount: ' . $paid_amount;
        } else if ($paid_amount < $row_mop['amount']) {
            $showPaid = 'SEMI-PAID (' . $row_credit_check['timestamp'] . ') Amount: ' . $paid_amount;
        }
        $total_credit_paid += $paid_amount;
    }

    // Write row data
    $sheet->setCellValue('A' . $rowNum, $i);
    $sheet->setCellValue('B' . $rowNum, $tableName);
    $sheet->setCellValue('C' . $rowNum, $row['invoice_no']);
    $sheet->setCellValue('D' . $rowNum, $row['taxable_amount']);
    $sheet->setCellValue('E' . $rowNum, $row['service_charge_amount']);
    $sheet->setCellValue('F' . $rowNum, $row['taxable_amount'] + $row['service_charge_amount']);
    $sheet->setCellValue('G' . $rowNum, $row['tot_vat']);
    $sheet->setCellValue('H' . $rowNum, $row['tot_sat']);
    $sheet->setCellValue('I' . $rowNum, $row['total_amount'] + $row['service_charge_total']);
    // Discount logic
    if ($row['tot_disc'] == '') {
        $sheet->setCellValue('J' . $rowNum, $row['tot_disc']);
        $tot_discount += (float)$row['tot_disc'];
    } else {
        if ($row['other_discount'] != 0) {
            $sheet->setCellValue('J' . $rowNum, $row['other_discount'] . ' (' . $row['tot_disc'] . ')');
            $tot_discount += (float)$row['tot_disc'];
        } else {
            $sheet->setCellValue('J' . $rowNum, '');
        }
    }
    $sheet->setCellValue('K' . $rowNum, $row['grand_total']);
    $sheet->setCellValue('L' . $rowNum, date("d-m-Y", strtotime($row['timestamp'])));
    $sheet->setCellValue('M' . $rowNum, $row['quantity']);
    $sheet->setCellValue('N' . $rowNum, strtoupper($row['mode_of_payment']));
    $sheet->setCellValue('O' . $rowNum, $showPaid);

    // Totals calculation
    $tot_qty += $row['quantity'];
    $tot_service_charge += $row['service_charge_amount'];
    $tot_amount += $row['grand_total'];
    $tot_invoice += ($row['total_amount'] + $row['service_charge_total']);
    $tot_sgst += $row['tot_vat'];
    $tot_cgst += $row['tot_sat'];
    $tot_taxable += $row['taxable_amount'];
    $tot_taxable_w_sc += ($row['taxable_amount'] + $row['service_charge_amount']);

    $rowNum++;
}

// Add total row
$sheet->setCellValue('A' . $rowNum, '');
$sheet->setCellValue('B' . $rowNum, 'Total');
$sheet->setCellValue('D' . $rowNum, round($tot_taxable, 2));
$sheet->setCellValue('E' . $rowNum, round($tot_service_charge, 2));
$sheet->setCellValue('F' . $rowNum, round($tot_taxable_w_sc, 2));
$sheet->setCellValue('G' . $rowNum, round($tot_sgst, 2));
$sheet->setCellValue('H' . $rowNum, round($tot_cgst, 2));
$sheet->setCellValue('I' . $rowNum, round($tot_invoice, 2));
$sheet->setCellValue('J' . $rowNum, round($tot_discount, 2));
$sheet->setCellValue('K' . $rowNum, round($tot_amount, 2));
$sheet->setCellValue('M' . $rowNum, $tot_qty);

// Set headers for download
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="sales_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
