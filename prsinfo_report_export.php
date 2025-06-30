<?php
require 'scripts/settings.php'; // Include your DB connection & execute_query function
require 'vendor/autoload.php';  // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create spreadsheet and sheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Personal Information');

// Set header
$headers = ['S.No.', 'Name', 'Father Name', 'Mobile Number', 'Date Of Entry', 'Occupation', 'Address', 'Police Station', 'District', 'State', 'Reason For Come'];
$sheet->fromArray($headers, null, 'A1');

// Build query
$sql = 'SELECT * FROM personal_information WHERE 1=1';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to']) + 86400);
    $sql .= ' AND creation_time >= "' . $_POST['allot_from'] . '" AND creation_time < "' . $_POST['allot_to'] . '"';
    if (!empty($_POST['name'])) {
        $sql .= ' AND name LIKE "%' . $_POST['name'] . '%"';
    }
    if (!empty($_POST['father_name'])) {
        $sql .= ' AND father_name LIKE "%' . $_POST['father_name'] . '%"';
    }
} else {
    $sql .= ' AND creation_time >= "' . date("Y-m-d") . '" AND creation_time < "' . date("Y-m-d", strtotime("+1 day")) . '"';
}

// Fetch data
$result = execute_query($sql);
$rowIndex = 2;
$i = 1;

// Fill data
foreach ($result as $row) {
    $sheet->fromArray([
        $i++,
        $row['name'],
        $row['father_name'],
        $row['mobile_number'],
        date('d-m-Y', strtotime($row['creation_time'])),
        $row['occupation'],
        $row['address'],
        $row['police_station'],
        $row['district'],
        $row['state'],
        $row['reason_for_come']
    ], null, 'A' . $rowIndex++);
}

// Auto size columns
foreach (range('A', 'K') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Send output to browser
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Personal_Information_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
