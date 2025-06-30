<?php
require 'scripts/settings.php'; // Database connection & execute_query()
require 'vendor/autoload.php';  // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['part_type'] == '' && $_POST['item'] == '') {
    $from = $_POST['from'];
    $to = $_POST['to'];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Stock Summary');

    // Headers
    $headers = ['S.No.', 'Group Name', 'Quantity', 'Amount'];
    $sheet->fromArray($headers, null, 'A1');

    $sql = 'SELECT * FROM new_type ORDER BY description';
    $result = execute_query($sql);

    $i = 1;
    $rowIndex = 2;

    while ($row = mysqli_fetch_assoc($result)) {
        $sql_sum = 'SELECT SUM(amount) AS amount, SUM(qty) AS quantity
                    FROM stock_sale_restaurant 
                    LEFT JOIN stock_available ON stock_available.sno = part_id 
                    WHERE stock_available.type = "' . $row['sno'] . '" 
                    AND part_dateofpurchase >= "' . $from . '" 
                    AND part_dateofpurchase <= "' . $to . '"';

        $row_sum = mysqli_fetch_assoc(execute_query($sql_sum));
        $quantity = $row_sum['quantity'] ?? 0;
        $amount = round($row_sum['amount'] ?? 0, 2);

        $sheet->fromArray([
            $i++,
            $row['description'],
            $quantity,
            $amount
        ], null, 'A' . $rowIndex++); 
    }

    // Autosize columns
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output the file
    if (ob_get_length()) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Stock_Summary_Report.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
