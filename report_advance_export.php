<?php
// export_excel.php

require 'vendor/autoload.php';
require 'scripts/settings.php';  // Your existing DB connection file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Use $conn as your mysqli connection

// Initialize base SQL
$sql = "SELECT * FROM advance_booking WHERE 1=1";

// Handle POST filters for the report
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $date_type = $_POST['date_type'] ?? 'booking_wise';
    $allot_from = $_POST['allot_from'] ?? null;
    $allot_to = $_POST['allot_to'] ?? null;

    if ($allot_from && $allot_to) {
        $from = $db->real_escape_string($allot_from);
        // Add 1 day to include whole end date (less than next day)
        $to = date("Y-m-d", strtotime($allot_to) + 86400);

        if ($date_type == 'booking_wise') {
            // Assuming created_on is entry date in your DB
            $sql .= " AND created_on >= '$from' AND created_on < '$to'";
        } else if ($date_type == 'allotment_wise') {
            // Assuming allotment_date is booking date
            $sql .= " AND allotment_date >= '$from' AND allotment_date < '$to'";
        }
    }
} else {
    // Default filter: today only for created_on
    $today = date("Y-m-d");
    $tomorrow = date("Y-m-d", strtotime($today) + 86400);
    $sql .= " AND created_on >= '$today' AND created_on < '$tomorrow'";
}
echo $sql;
// You can add more filters here like cust_name, type, mop, status, etc. similar to above

// Run main query
$result = $db->query($sql);
if (!$result) {
    die("Query failed: " . $db->error);
}

// Prepare Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'S.No.', 'Receipt No.', 'Company Name', 'Guest Name', 'Mobile', 'Date Of Entry', 'Type', 'MOP', 'Total Amount', 'Advance Amount', 'Due Amount',
    'Booking Date', 'Check In Date', 'Check Out Date', 'Room Category', 'No. Of Rooms', 'Room Number', 'Status'
];

// Set headers in bold
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
    $col++;
}

$rowIndex = 2;
$sno = 1;

// Helper functions to fetch related info (replace table/column names accordingly)
function getCustomerInfo($conn, $cust_sno) {
    $cust_sno = (int)$cust_sno;
    $sql = "SELECT cust_name, company_name, mobile FROM customer WHERE sno = $cust_sno LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        return $res->fetch_assoc();
    }
    return ['cus_name' => '-', 'company_name' => '-', 'mobile' => '-'];
}

function getRoomCategory($conn, $cat_sno) {
    $cat_sno = (int)$cat_sno;
    $sql = "SELECT room_type FROM category WHERE sno = $cat_sno LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        return $res->fetch_assoc()['room_type'];
    }
    return '-';
}

function getStatusText($status) {
    return ($status == 1) ? 'BOOKED' : 'NON BOOKED';
}

function getBookingMopText($conn, $advance_booking_id) {
    $advance_booking_id = (int)$advance_booking_id;
    $sql = "SELECT mop FROM customer_transactions WHERE advance_booking_id = $advance_booking_id LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $mop = $row['mop'];
        if ($mop == 'bank_transfer') return 'BANK TRANSFER';
        if ($mop == 'card_sbi') return 'CARD S.B.I.';
        if ($mop == 'card_pnb') return 'CARD P.N.B.';
        return strtoupper($mop);
    }
    return '-';
}


function getTypeText($type) {
    $type_map = [
        'room_rent' => 'Room Booking',
        'banquet_rent' => 'Banquet Booking'
    ];
    return $type_map[$type] ?? $type;
}

// Loop through data rows
while ($row = $result->fetch_assoc()) {
    // Get customer info
    $cust_info = getCustomerInfo($db, $row['cust_id'] ?? 0);

    // Get room category name
    $room_category = getRoomCategory($db, $row['cat_id'] ?? 0);

    // Prepare data array for each row in excel
    $data = [
        $sno++,
        $row['sno'] ?? '-',
        $cust_info['company_name'] ?? '-',
        $cust_info['cust_name'] ?? '-',
        $cust_info['mobile'] ?? '-',
        isset($row['created_on']) ? date('d-m-Y', strtotime($row['created_on'])) : '-',
        getTypeText($row['purpose'] ?? ''),
        getBookingMopText($db, $row['sno']),
        $row['total_amount'] ?? '-',
        $row['advance_amount'] ?? '-',
        $row['due_amount'] ?? '-',
        isset($row['allotment_date']) ? date('d-m-Y H:i:s', strtotime($row['allotment_date'])) : '-',
        isset($row['check_in']) ? date('d-m-Y H:i:s', strtotime($row['check_in'])) : '-',
        isset($row['check_out']) ? date('d-m-Y H:i:s', strtotime($row['check_out'])) : '-',
        $room_category,
        $row['number_of_room'] ?? '-',
        $row['room_number'] ?? '-',
        getStatusText($row['status'] ?? 0)
    ];

    $col = 'A';
    foreach ($data as $cell) {
        $sheet->setCellValue($col . $rowIndex, $cell);
        $col++;
    }
    $rowIndex++;
}

// Auto-size columns A to last column
$lastCol = chr(ord('A') + count($headers) - 1);
foreach (range('A', $lastCol) as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="advance_booking_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
