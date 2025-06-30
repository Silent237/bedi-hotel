<?php
require 'scripts/settings.php'; // DB connection and execute_query
require 'vendor/autoload.php';  // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Advance Booking Report');

// Header
$headers = [
    'S.No.', 'Receipt No.', 'Company Name', 'Guest Name', 'Mobile',
    'Date Of Entry', 'Type', 'MOP', 'Total Amount', 'Advance Amount',
    'Due Amount', 'Booking Date', 'Check In Date', 'Check Out Date',
    'No. Of Rooms', 'Room Number', 'Status'
];
$sheet->fromArray($headers, null, 'A1');

// SQL for advance booking
$sql = 'SELECT * FROM res_advance_booking WHERE 1=1';

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    if ($_POST['date_type'] == 'booking_wise') {
        $sql .= ' AND created_on >= "' . $_POST['allot_from'] . '" AND created_on < "' . date("Y-m-d", strtotime($_POST['allot_to']) + 86400) . '"';
    } elseif ($_POST['date_type'] == 'allotment_wise') {
        $sql .= ' AND allotment_date >= "' . $_POST['allot_from'] . '" AND allotment_date < "' . date("Y-m-d", strtotime($_POST['allot_to']) + 86400) . '"';
    } else {
        $sql .= ' AND created_on >= "' . date("Y-m-d") . '" AND created_on < "' . date("Y-m-d", strtotime(date("Y-m-d")) + 86400) . '"';
    }

    if ($_POST['cust_sno'] != '') {
        $sql .= ' AND cust_id="' . $_POST['cust_sno'] . '"';
    }
    if ($_POST['type'] != '') {
        $sql .= ' AND purpose="' . $_POST['type'] . '"';
    }
    if ($_POST['status'] != '') {
        $sql .= ' AND status="' . $_POST['status'] . '"';
    }
} else {
    $sql .= ' AND created_on >= "' . date("Y-m-d") . '" AND created_on < "' . date("Y-m-d", strtotime(date("Y-m-d")) + 86400) . '"';
}

$result = execute_query($sql);

$rowIndex = 2;
$i = 1;
$tot_total = 0;
$tot_advance = 0;
$tot_due = 0;

foreach ($result as $row) {
    $sql_mop = 'SELECT * FROM customer_transactions WHERE advance_booking_id="' . $row['sno'] . '"';
    if (isset($_POST['submit_form'])) {
        if ($_POST['mop'] != '') {
            $sql_mop .= ' AND mop="' . $_POST['mop'] . '"';
        }
        if ($_POST['cancel_status'] != '') {
            $sql_mop .= ' AND type="' . $_POST['cancel_status'] . '"';
        }
    }
    $result_mop = execute_query($sql_mop);
    while ($row_mop = mysqli_fetch_array($result_mop)) {
        $cust = mysqli_fetch_array(execute_query('SELECT * FROM customer WHERE sno=' . $row['cust_id']));

        $purpose = '';
        if ($row['purpose'] == 'room_rent') {
            $purpose = 'Room Booking';
        } elseif ($row['purpose'] == 'banquet_rent') {
            $purpose = 'Banquet Booking';
        } elseif ($row['purpose'] == 'advance_for_checkin') {
            $purpose = 'Room Booking (In House Guest)';
        } elseif ($row['purpose'] == 'advance_for') {
            $advance_for = mysqli_fetch_array(execute_query('SELECT * FROM res_advance_booking WHERE sno="' . $row['advance_for_id'] . '"'));
            if ($advance_for['purpose'] == 'room_rent') {
                $purpose = 'Room Booking (Plus Amount)';
            } elseif ($advance_for['purpose'] == 'banquet_rent') {
                $purpose = 'Banquet Booking (Plus Amount)';
            }
        }

        $mop = strtoupper($row_mop['mop']);
        if ($mop == "BANK_TRANSFER") $mop = "BANK TRANSFER";
        elseif ($mop == "CARD_SBI") $mop = "CARD S.B.I.";
        elseif ($mop == "CARD_PNB") $mop = "CARD P.N.B.";

        $status = ($row_mop['type'] == 'ADVANCE_AMT_CANCEL') ? 'Canceled' :
                  (($row['status'] == 0 && $row_mop['type'] == 'ADVANCE_AMT') ? 'Pending' : 'Booked');

        $sheet->fromArray([
            $i++,
            $row['sno'],
            $cust['company_name'],
            $row['guest_name'],
            $cust['mobile'],
            date('d-m-Y', strtotime($row['created_on'])),
            $purpose,
            $mop,
            $row['total_amount'],
            $row['advance_amount'],
            $row['due_amount'],
            date('d-m-Y h:i:s', strtotime($row['allotment_date'])),
            date('d-m-Y h:i:s', strtotime($row['check_in'])),
            date('d-m-Y h:i:s', strtotime($row['check_out'])),
            $row['number_of_room'],
            $row['room_number'],
            $status
        ], null, 'A' . $rowIndex++);

        $tot_total += intval($row['total_amount']);
        $tot_advance += $row['advance_amount'];
        $tot_due += $row['due_amount'];
    }
}

// Total row
$sheet->setCellValue('A' . $rowIndex, 'Total:');
$sheet->mergeCells("A{$rowIndex}:H{$rowIndex}");
$sheet->setCellValue("I{$rowIndex}", $tot_total);
$sheet->setCellValue("J{$rowIndex}", $tot_advance);
$sheet->setCellValue("K{$rowIndex}", $tot_due);

// Auto column width
foreach (range('A', 'Q') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="advance_booking_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
