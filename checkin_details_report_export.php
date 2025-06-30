<?php
require 'scripts/settings.php'; // Adjust path if needed
require 'vendor/autoload.php'; // Adjust path if needed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header
$sheet->fromArray([
    'S.No.', 'Guest Name', 'Mobile', 'Address', 'Room No.',
    'Allotment Date', 'Night', 'Total Rent (+)', 'Room Service (+)',
    'Advance Amount (-)', 'Total (=)'
], NULL, 'A1');

$rowNum = 2;
$i = 1;
$grand_total_rent = $grand_room_service = $grand_advance = $grand = $night = 0;

$sql = 'SELECT `allotment`.*, `allotment`.`sno` AS allot_id 
        FROM `allotment` 
        LEFT JOIN `room_master` ON `room_master`.`sno` = `allotment`.`room_id` 
        WHERE (`allotment`.`exit_date` IS NULL OR `allotment`.`exit_date`="")';

if (isset($_POST['allot_from']) && isset($_POST['allot_to'])) {
    $from = date("Y-m-d", strtotime($_POST['allot_from']));
    $to = date("Y-m-d", strtotime($_POST['allot_to']) + 86400);
    $sql .= " AND `allotment`.`allotment_date` >= '$from' AND `allotment`.`allotment_date` < '$to'";

    if (!empty($_POST['cust_id'])) {
        $sql .= ' AND `allotment`.`cust_id` = "' . $_POST['cust_id'] . '"';
    } elseif (!empty($_POST['cust_name'])) {
        $sql .= ' AND `allotment`.`guest_name` LIKE "%' . $_POST['cust_name'] . '%"';
    }
}

$sql .= ' ORDER BY `allotment`.`allotment_date` DESC';

$result = execute_query($sql);

foreach ($result as $row) {
    $days = ($row['exit_date'] == '') 
        ? get_days($row['allotment_date'], date("d-m-Y H:i")) 
        : get_days($row['allotment_date'], $row['exit_date']);

    $total_rent = intval($row['room_rent']) * $days;

    $cust = mysqli_fetch_array(execute_query("SELECT * FROM customer WHERE sno = '{$row['cust_id']}'"));
    $mobile = $cust['mobile'];
    $address = $cust['address'] ?: $row['guest_address'];
    $room_no = get_room($row['room_id']);
    $allotment_date = date("d-m-Y, h:i A", strtotime($row['allotment_date']));

    $room_service_amount = mysqli_fetch_array(execute_query(
        'SELECT SUM(`taxable_amount`) AS amt FROM `invoice_sale_restaurant` WHERE `mode_of_payment`="credit"'
    ))['amt'] ?? 0;

    $advance_amount = mysqli_fetch_array(execute_query(
        'SELECT SUM(`advance_amount`) AS amt FROM `advance_booking` 
         WHERE `advance_for_checkin_id`="' . $row['allot_id'] . '" 
         AND `purpose`="advance_for_checkin"'
    ))['amt'] ?? 0;

    $total = $total_rent + $room_service_amount - $advance_amount;

    $sheet->fromArray([
        $i++, $row['guest_name'], $mobile, $address, $room_no,
        $allotment_date, $days, $total_rent, $room_service_amount,
        $advance_amount, $total
    ], NULL, 'A' . $rowNum++);

    $grand_total_rent += $total_rent;
    $grand_room_service += $room_service_amount;
    $grand_advance += $advance_amount;
    $grand += $total;
    $night += $days;
}

// Add total row
$sheet->fromArray(['', '', '', '', 'Total', '', $night, $grand_total_rent, $grand_room_service, $grand_advance, $grand], NULL, 'A' . $rowNum);

if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Test.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;