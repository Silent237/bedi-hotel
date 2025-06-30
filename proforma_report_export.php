<?php
require 'scripts/settings.php'; // Your DB and execute_query
require 'vendor/autoload.php';  // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Spreadsheet setup
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Proforma Invoice');

// Header row
$headers = ['S.No.', 'Customer Name', 'Company Name', 'Mobile No.', 'GSTIN', 'PIN Code', 'Date', 'Check In Date', 'Check Out Date', 'Amount', 'SGST', 'CGST', 'Grand Total'];
$sheet->fromArray($headers, null, 'A1');

// SQL filter logic
$sql = 'SELECT * FROM `proforma_invoice` WHERE 1=1';

if (isset($_POST['date_type'])) {
    $dtfrom = date("d-m-Y", strtotime($_POST['datefrom']));
    $dtto = date("d-m-Y", strtotime($_POST['dateto']));

    if ($_POST['date_type'] == "cdt") {
        if (!empty($_POST['datefrom'])) {
            $sql .= ' AND creation_time BETWEEN "' . $dtfrom . '" AND "' . ($dtto ?: date('d-m-Y')) . '"';
        }
    } elseif ($_POST['date_type'] == "cincoutdt") {
        if (!empty($_POST['datefrom'])) {
            $sql .= ' AND cindt BETWEEN "' . $dtfrom . '" AND "' . ($dtto ?: date('d-m-Y')) . '"';
        }
    }
} elseif (!empty($_POST['datefrom'])) {
    $dtfrom = date("d-m-Y", strtotime($_POST['datefrom']));
    $dtto = date("d-m-Y", strtotime($_POST['dateto']));
    $sql .= ' AND creation_time BETWEEN "' . $dtfrom . '" AND "' . ($dtto ?: date('d-m-Y')) . '"';
}

if (!empty($_POST['cus'])) {
    $sql .= " AND guest_name LIKE '%" . $_POST['cus'] . "%'";
}

// Fetch results
$result = execute_query($sql);

$i = 1;
$rowIndex = 2;
$total_amount = $total_sgst = $total_cgst = $total_grand = 0;

while ($row = mysqli_fetch_array($result)) {
    $amount = intval($row['amount']);
    $sgst = intval($row['sgst']);
    $cgst = intval($row['cgst']);
    $grand_total = intval($row['totel']);

    $sheet->fromArray([
        $i++,
        $row['guest_name'],
        $row['company_name'],
        $row['mob_no'],
        $row['gstin'],
        $row['pin_code'],
        $row['creation_time'] ? date("d-m-Y", strtotime($row['creation_time'])) : "--",
        $row['cindt'] ? date("d-m-Y", strtotime($row['cindt'])) : "--",
        $row['coutdt'] ? date("d-m-Y", strtotime($row['coutdt'])) : "--",
        $amount,
        $sgst,
        $cgst,
        $grand_total
    ], null, 'A' . $rowIndex++);

    $total_amount += $amount;
    $total_sgst += $sgst;
    $total_cgst += $cgst;
    $total_grand += $grand_total;
}

// Total row
$sheet->setCellValue('A' . $rowIndex, 'Total:');
$sheet->mergeCells("A{$rowIndex}:I{$rowIndex}");
$sheet->setCellValue("J{$rowIndex}", $total_amount);
$sheet->setCellValue("K{$rowIndex}", $total_sgst);
$sheet->setCellValue("L{$rowIndex}", $total_cgst);
$sheet->setCellValue("M{$rowIndex}", $total_grand);

// Auto column width
foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Proforma_Invoice_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
