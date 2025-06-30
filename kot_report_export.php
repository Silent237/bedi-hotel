<?php
require 'scripts/settings.php'; // For execute_query()
require 'vendor/autoload.php';  // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('KOT Report');

// Header row
$headers = ['S.No', 'KOT No', 'Type', 'Qty', 'Table/Room No', 'Status', 'Date'];
$sheet->fromArray($headers, null, 'A1');

// Prepare SQL
$type = [];
$menu = '';
$startdate = strtotime(date("Y-m-d"));
$enddate = $startdate + 86400;

$sql = "SELECT * FROM `kitchen_ticket_temp` WHERE 1=1 ";
$condition = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['status'] == 'cancel') {
        $sql .= ' AND cancel_timestamp IS NOT NULL ';
    }
    if ($_POST['status'] == 'invoice') {
        $sql .= ' AND invoice_no IS NOT NULL ';
    }
    if ($_POST['din_from'] != "" && $_POST['din_to'] != "") {
        $startdate = strtotime($_POST['din_from']);
        $enddate = strtotime($_POST['din_to']) + 86400;
        $condition[] = " AND `time_stamp` >= '{$startdate}' AND `time_stamp` <= '{$enddate}'";
    }
    if ($_POST['number'] != "") {
        $condition[] = " AND kot_no = '{$_POST['number']}'";
    }
    if ($_POST['type'] == 'room') {
        $condition[] = " AND table_id LIKE '%room%'";
    }
    if ($_POST['type'] == 'table') {
        $condition[] = " AND table_id NOT LIKE '%room%'";
    }
} else {
    $condition[] = " AND `time_stamp` >= '{$startdate}' AND `time_stamp` <= '{$enddate}'";
}

$condition[] = " GROUP BY kot_no";
$sqql = $sql . implode($condition);
$run = execute_query($sqql);

// Loop over rows
$i = 1;
$rowIndex = 2;

while ($row = mysqli_fetch_array($run)) {
    $kotNo = $row['kot_no'];
    $statusText = 'Pending';
    if ($row['cancel_timestamp'] != '') {
        $statusText = 'Cancelled';
    } elseif ($row['invoice_no'] != '') {
        $statusText = 'Invoice Generated';
    }

    // Get item names and quantity
    $sql1 = "SELECT * FROM `kitchen_ticket_temp` WHERE kot_no = '$kotNo'";
    $run1 = execute_query($sql1);

    $typeText = '';
    $qty = 0;

    while ($row1 = mysqli_fetch_array($run1)) {
        $name_item = "SELECT * FROM `stock_available` WHERE sno = '" . $row1['item_id'] . "'";
        $item_data = mysqli_fetch_array(execute_query($name_item));
        $typeText .= $item_data['description'] . '(' . $row1['unit'] . '), ';
        $qty += $row1['unit'];
    }

    // Get Table/Room info
    if (strpos($row['table_id'], "room") === false) {
        $table_room = 'T-' . get_table($row['table_id']);
    } else {
        $room_id = substr($row['table_id'], 5);
        $room_data = mysqli_fetch_array(execute_query("SELECT * FROM room_master WHERE sno = '$room_id'"));
        $table_room = 'R-' . $room_data['room_name'];
    }

    $dateFormatted = date('d-m-Y H:i:s', (int) $row['time_stamp']);

    $sheet->fromArray([
        $i++, $kotNo, rtrim($typeText, ', '), $qty, $table_room, $statusText, $dateFormatted
    ], null, 'A' . $rowIndex++);
}

// Auto-size columns
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="KOT_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
