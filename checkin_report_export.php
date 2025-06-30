<?php
require 'scripts/settings.php'; // Adjust path as needed
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$sheet->fromArray([
    'S.No.', 'Guest Name', 'Mobile', 'Address', 'Room No.',
    'Extra Bed', 'Total Rent', 'Night', 'Allotment Date', 'Reference', 'Status'
], NULL, 'A1');

$rowNum = 2;
$i = 1;
$grand_total_rent = 0;
$night = 0;

$sql = 'SELECT `allotment`.*, `allotment`.`sno` AS allot_id 
        FROM `allotment` 
        LEFT JOIN `room_master` ON `room_master`.`sno` = `allotment`.`room_id` 
        WHERE 1=1 ';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['allot_from'] = date("Y-m-d", strtotime($_POST['allot_from']));
    $_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to']));
    $_POST['allot_to_re'] = date("Y-m-d", strtotime($_POST['allot_to']) + 86400);
    $sql .= ' AND `allotment`.`allotment_date` >= "' . $_POST['allot_from'] . '" AND `allotment`.`allotment_date` < "' . $_POST['allot_to_re'] . '"';

    if ($_POST['status'] != '') {
        if ($_POST['status'] == 'in') {
            $sql .= ' AND (`allotment`.`exit_date` IS NULL OR `allotment`.`exit_date` = "")';
        } elseif ($_POST['status'] == 'out') {
            $sql .= ' AND (`allotment`.`exit_date` != "")';
        }
    }

    if ($_POST['reference'] != '') {
        $sql .= ' AND reference = "' . $_POST['reference'] . '"';
    }

    if ($_POST['cust_id'] != '') {
        $sql .= ' AND `allotment`.`cust_id` = "' . $_POST['cust_id'] . '"';
    } elseif ($_POST['cust_name'] != '') {
        $sql .= ' AND `allotment`.`guest_name` LIKE "%' . $_POST['cust_name'] . '%"';
    }
} else {
    $sql .= ' AND `allotment`.`allotment_date` >= "' . date('Y-m-d') . '" AND `allotment`.`allotment_date` < "' . date("Y-m-d", strtotime(date('Y-m-d')) + 86400) . '"';
}

$sql .= ' ORDER BY `allotment`.`allotment_date` DESC';
echo $sql;
$result = execute_query($sql);

foreach ($result as $row) {
    // Calculate nights
    $days = ($row['exit_date'] == '')
        ? get_days($row['allotment_date'], date("d-m-Y H:i"))
        : get_days($row['allotment_date'], $row['exit_date']);

    $total_rent = floatval($row['room_rent']) * $days;

    // Get customer details
    $cust = mysqli_fetch_array(execute_query("SELECT * FROM customer WHERE sno = '{$row['cust_id']}'"));
    $mobile = $cust['mobile'];
    $address = $cust['address'] != '' ? $cust['address'] : $row['guest_address'];
    $room_no = get_room($row['room_id']);
    $reference = get_reference($row['reference']);
    $status = $row['exit_date'] == '' ? 'IN' : 'OUT';
    $allotment_date = date("d-m-Y, h:i A", strtotime($row['allotment_date']));

    $sheet->fromArray([
        $i++, $row['guest_name'], $mobile, $address, $room_no,
        $row['other_charges'], $total_rent, $days, $allotment_date, $reference, $status
    ], NULL, 'A' . $rowNum++);

    $grand_total_rent += $total_rent;
    $night += $days;
}

// Total row
$sheet->fromArray([
    '', '', '', '', 'Total', '', $grand_total_rent, $night, '', '', ''
], NULL, 'A' . $rowNum);

//Output as Excel file
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="checkin_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
