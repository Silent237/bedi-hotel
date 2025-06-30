<?php
require 'scripts/settings.php'; // adjust path as needed
require 'vendor/autoload.php'; // PhpSpreadsheet autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check form submission
if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
    die("Form not submitted.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$headers = [
    'S.No.', 'Guest Name', 'Mobile', 'Address', 'Room No.', 'Extra Bed',
    'Total Rent', 'Night', 'Allotment Date', 'Exit Date', 'Reference', 'Status'
];
$sheet->fromArray($headers, NULL, 'A1');

// Prepare date for SQL condition
$allotment_date = date("Y-m-d 23:59:59", strtotime($_POST['allotment_date']));

// SQL query to fetch allotments
$sql = 'SELECT `allotment`.* FROM `allotment`
        LEFT JOIN `room_master` ON `room_master`.`sno` = `allotment`.`room_id`
        WHERE ("'.$allotment_date.'" BETWEEN allotment_date AND exit_date)
        OR ((exit_date IS NULL OR exit_date="") AND allotment_date <= "'.$allotment_date.'")
        ORDER BY `allotment`.`allotment_date` DESC';

$result = execute_query($sql);

$rowNum = 2;
$i = 1;
$night = 0;
$grand_total_rent = 0;

foreach ($result as $row) {
    // Calculate days stayed
    if ($row['exit_date'] == '') {
        $days = get_days($row['allotment_date'], date("d-m-Y H:i"));
    } else {
        $days = get_days($row['allotment_date'], $row['exit_date']);
    }
    $total_rent = intval($row['room_rent']) * intval($days);

    // Fetch customer details
    $cust = mysqli_fetch_array(execute_query("SELECT * FROM customer WHERE sno='" . $row['cust_id'] . "'"));
    $mobile = $cust['mobile'];
    $address = $cust['address'] ?: $row['guest_address'];

    $room_no = get_room($row['room_id']);
    $allotment_date_formatted = date("d-m-Y, h:i A", strtotime($row['allotment_date']));
    $exit_date_formatted = ($row['exit_date'] == '') ? '' : date("d-m-Y, h:i A", strtotime($row['exit_date']));
    $reference = get_reference($row['reference']);
    $status = ($row['exit_date'] == '') ? 'IN' : 'OUT';

    $sheet->fromArray([
        $i++,
        $row['guest_name'],
        $mobile,
        $address,
        $room_no,
        $row['other_charges'],
        $total_rent,
        $days,
        $allotment_date_formatted,
        $exit_date_formatted,
        $reference,
        $status
    ], NULL, 'A' . $rowNum++);

    $night += $days;
    $grand_total_rent += $total_rent;
}

// Add totals row
$sheet->fromArray([
    '',
    '',
    '',
    '',
    '',
    'Total :',
    $grand_total_rent,
    $night,
    '',
    '',
    '',
    ''
], NULL, 'A' . $rowNum);

// Auto-size columns for readability
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Send headers to force download Excel file
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Allotment_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
