<?php
require 'scripts/settings.php'; // Your DB and helper functions
require 'vendor/autoload.php'; // PhpSpreadsheet autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Transaction Report');

// Excel headers
$headers = ['S.No.', 'Date', 'Name', 'Type', 'Invoice No.', 'Mode Of Payment', 'Amount', 'Remarks'];
$sheet->fromArray($headers, null, 'A1');

$n = 1;
$rowNum = 2;

$sql = 'SELECT * FROM `customer_transactions` WHERE (`type`="receipt" OR `type`="RENT") AND `payment_for`="ROOM"';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['allot_from']) && !empty($_POST['allot_to'])) {
        $sql .= ' AND `timestamp` >= "' . $_POST['allot_from'] . '" AND `timestamp` < "' . date("Y-m-d", strtotime($_POST['allot_to']) + 86400) . '"';
    }
    if (!empty($_POST['mop'])) {
        $sql .= ' AND `mop` = "' . $_POST['mop'] . '"';
    }
} else {
    $sql .= ' AND `timestamp` >= "' . date('Y-m-d') . '" AND `timestamp` < "' . date("Y-m-d", strtotime(date("Y-m-d")) + 86400) . '"';
}

$result = execute_query($sql);

$grand_amount = 0;

while ($row = mysqli_fetch_array($result)) {
    $sql_sub = 'SELECT * FROM `customer_transactions` WHERE `sno` IN (' . implode(',', array_map('intval', explode('#', $row['set_sno']))) . ')';
    $result_sub = execute_query($sql_sub);

    while ($row_sub = mysqli_fetch_array($result_sub)) {
        $sql_cust = 'SELECT * FROM `customer` WHERE `sno`="' . $row_sub['cust_id'] . '"';
        $result_cust = execute_query($sql_cust);
        $row_cust = mysqli_fetch_array($result_cust);

        if ($row['amount'] > $row_sub['amount']) {
            $amount = $row_sub['amount'];
            $row['amount'] -= $amount;
        } else {
            $amount = $row['amount'];
        }

        // Type detection
        $type = '';
        if ($row_sub['type'] == "RENT" && $row_sub['payment_for'] == "") {
            $type = "ROOM";
        } elseif ($row_sub['type'] == "sale_restaurant" && $row_sub['payment_for'] == "res") {
            $type = (strpos($row_sub['invoice_no'], 'R') !== false) ? "Room Service" : "Restaurant";
        } elseif ($row_sub['type'] == "BAN_AMT") {
            $type = "Banquet";
        }

        // Filter by type if set
        $show = 1;
        if (isset($_POST['type']) && $_POST['type'] != '') {
            $allowed = [
                'room' => "ROOM",
                'restaurant' => "Restaurant",
                'room_service' => "Room Service"
            ];
            $show = (isset($allowed[$_POST['type']]) && $allowed[$_POST['type']] == $type) ? 1 : 0;
        }

        if ($show) {
            $grand_amount += $amount;

            // Format MOP
            $mop = strtoupper($row['mop']);
            if ($mop == "BANK_TRANSFER") $mop = "BANK TRANSFER";
            if ($mop == "CARD_SBI") $mop = "CARD S.B.I.";
            if ($mop == "CARD_PNB") $mop = "CARD P.N.B.";

            $remarks = $row_sub['credit_settelment_remark'] ?: $row['credit_settelment_remark'];
            $sheet->fromArray([
                $n++,
                $row['timestamp'],
                $row_cust['company_name'] . '-' . $row_cust['cust_name'],
                $type,
                $row_sub['invoice_no'],
                $mop,
                $amount,
                $remarks
            ], null, 'A' . $rowNum++);
        }
    }
}

// Total row
$sheet->fromArray(['', '', '', '', '', 'Total:', $grand_amount, ''], null, 'A' . $rowNum);

// Auto-size columns
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download as Excel
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Customer_Transaction_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
