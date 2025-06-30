<?php
require 'scripts/settings.php';
require 'vendor/autoload.php';  // PhpSpreadsheet autoload

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start(); // make sure session is started

// Define a helper function to execute queries (assuming $db is mysqli connection)

// Prepare base SQL and filter
if (isset($_SESSION['sale_date_from'])) {
    $sale_date_from = mysqli_real_escape_string($db, $_SESSION['sale_date_from']);
    $sale_date_to = mysqli_real_escape_string($db, $_SESSION['sale_date_to']);

    $sql = "SELECT invoice_sale_restaurant.sno, cust_name, company_name, concerned_person, department, 
            id_2 AS tin, taxable_amount, tot_vat, tot_sat, total_amount, tot_disc, other_discount, grand_total, 
            timestamp, quantity, type, invoice_type, invoice_no, agent_id, 
            mode_of_payments.mode_of_payment AS mode_of_payment, mop_type, storeid, 
            service_charge_amount, service_charge_tax_amount, service_charge_total 
            FROM invoice_sale_restaurant 
            LEFT JOIN customer ON customer.sno = invoice_sale_restaurant.supplier_id 
            LEFT JOIN mode_of_payments ON mode_of_payments.sno = invoice_sale_restaurant.mode_of_payment  
            WHERE timestamp >= '$sale_date_from' AND timestamp <= '$sale_date_to' AND storeid LIKE 'room%'";

    $sql_sum = "SELECT SUM(taxable_amount) AS taxable_amount, SUM(tot_vat) AS tot_vat, SUM(tot_sat) AS tot_sat, 
            SUM(total_amount) AS total_amount, SUM(grand_total) AS grand_total, 
            IF(other_discount='', SUM(tot_disc), SUM(other_discount)) AS total_discount, SUM(quantity) AS quantity 
            FROM invoice_sale_restaurant 
            LEFT JOIN customer ON customer.sno = invoice_sale_restaurant.supplier_id 
            WHERE timestamp >= '$sale_date_from' AND timestamp <= '$sale_date_to' AND storeid LIKE 'room%'";

    // Apply filters safely, escaping as necessary
    if (!empty($_SESSION['sale_supplier_sno'])) {
        $supplier_id = (int)$_SESSION['sale_supplier_sno'];
        $sql .= " AND supplier_id = $supplier_id";
        $sql_sum .= " AND supplier_id = $supplier_id";
    }
    if (!empty($_SESSION['sale_quantity'])) {
        $quantity = (int)$_SESSION['sale_quantity'];
        $qty_symbol = in_array($_SESSION['sale_qty_symbol'], ['=', '<', '>', '<=', '>=', '<>']) ? $_SESSION['sale_qty_symbol'] : '=';
        $sql .= " AND quantity $qty_symbol $quantity";
        $sql_sum .= " AND quantity $qty_symbol $quantity";
    }
    if (!empty($_SESSION['sale_amount'])) {
        $amount = (float)$_SESSION['sale_amount'];
        $amount_symbol = in_array($_SESSION['sale_amount_symbol'], ['=', '<', '>', '<=', '>=', '<>']) ? $_SESSION['sale_amount_symbol'] : '=';
        $sql .= " AND grand_total $amount_symbol $amount";
        $sql_sum .= " AND grand_total $amount_symbol $amount";
    }

    if (!empty($_SESSION['sale_invoice_type']) && $_SESSION['sale_invoice_type'] != 'all') {
        if ($_SESSION['sale_invoice_type'] == 'tax_gst') {
            $sql .= " AND id_2 != ''";
            $sql_sum .= " AND id_2 != ''";
        } elseif ($_SESSION['sale_invoice_type'] == 'tax_wo_gst') {
            $sql .= " AND id_2 = ''";
            $sql_sum .= " AND id_2 = ''";
        } else {
            $invoice_type = mysqli_real_escape_string($db, $_SESSION['sale_invoice_type']);
            $sql .= " AND invoice_type = '$invoice_type'";
            $sql_sum .= " AND invoice_type = '$invoice_type'";
        }
    }

    if (!empty($_SESSION['sale_invoice_no'])) {
        $invoice_no = mysqli_real_escape_string($db, $_SESSION['sale_invoice_no']);
        $sql .= " AND invoice_no = '$invoice_no'";
        $sql_sum .= " AND invoice_no = '$invoice_no'";
    }

    if (!empty($_SESSION['sale_mop']) && $_SESSION['sale_mop'] != 'all') {
        if ($_SESSION['sale_mop'] == 'all_nocharge') {
            $sql .= " AND invoice_sale_restaurant.mode_of_payment != 'nocharge'";
            $sql_sum .= " AND mode_of_payment != 'nocharge'";
        } else {
            $mop = mysqli_real_escape_string($db, $_SESSION['sale_mop']);
            $sql .= " AND invoice_sale_restaurant.mode_of_payment = '$mop'";
            $sql_sum .= " AND mode_of_payment = '$mop'";
        }
    }

    $sql .= " ORDER BY timestamp DESC, ABS(SUBSTR(invoice_no, 2)) DESC";

    $result_data = execute_query($sql);
    $row_sum = mysqli_fetch_assoc(execute_query($sql_sum));

} else {
    // Default filter for today
    $today = date('Y-m-d');
    $filter_summary = '';
    if (isset($_SESSION['sale_invoice_type']) && $_SESSION['sale_invoice_type'] != '') {
        $invoice_type = mysqli_real_escape_string($db, $_SESSION['sale_invoice_type']);
        $filter_summary = " AND invoice_type = '$invoice_type'";
    }
    $sql = "SELECT invoice_sale_restaurant.sno, cust_name, company_name, id_2 AS tin, concerned_person, department, taxable_amount, tot_vat, tot_sat, total_amount, tot_disc, other_discount, grand_total, timestamp, quantity, type, invoice_type, invoice_no, agent_id, mode_of_payments.mode_of_payment AS mode_of_payment, mop_type, storeid, service_charge_amount, service_charge_tax_amount, service_charge_total 
            FROM invoice_sale_restaurant 
            LEFT JOIN customer ON customer.sno = invoice_sale_restaurant.supplier_id 
            LEFT JOIN mode_of_payments ON mode_of_payments.sno = invoice_sale_restaurant.mode_of_payment  
            WHERE timestamp >= '$today' AND timestamp <= '$today' AND invoice_sale_restaurant.mode_of_payment != 'nocharge' $filter_summary AND storeid LIKE 'room%' 
            ORDER BY timestamp DESC, ABS(SUBSTR(invoice_no , 2)) DESC";

    $result_data = execute_query($sql);

    $sql_sum = "SELECT SUM(taxable_amount) AS taxable_amount, SUM(tot_vat) AS tot_vat, SUM(tot_sat) AS tot_sat, SUM(total_amount) AS total_amount, IF(other_discount='', SUM(tot_disc), SUM(other_discount)) AS total_discount, SUM(grand_total) AS grand_total, SUM(quantity) AS quantity 
                FROM invoice_sale_restaurant 
                LEFT JOIN customer ON customer.sno = invoice_sale_restaurant.supplier_id 
                WHERE timestamp >= '$today' AND timestamp <= '$today' AND invoice_sale_restaurant.mode_of_payment != 'nocharge' $filter_summary AND storeid LIKE 'room%'";

    $row_sum = mysqli_fetch_assoc(execute_query($sql_sum));
}

// --- Now, fetch summary data by mode_of_payment ---

$sql_summary_mode = "SELECT mode_of_payments.mode_of_payment, COUNT(*) AS count, SUM(grand_total) AS grand_total
                     FROM invoice_sale_restaurant
                     LEFT JOIN mode_of_payments ON mode_of_payments.sno = invoice_sale_restaurant.mode_of_payment
                     WHERE timestamp >= '$sale_date_from' AND timestamp <= '$sale_date_to' AND storeid LIKE 'room%'";

// Apply the same filters as before to summary query
if (isset($supplier_id)) {
    $sql_summary_mode .= " AND supplier_id = $supplier_id";
}
if (isset($quantity)) {
    $sql_summary_mode .= " AND quantity $qty_symbol $quantity";
}
if (isset($amount)) {
    $sql_summary_mode .= " AND grand_total $amount_symbol $amount";
}
if (!empty($_SESSION['sale_invoice_type']) && $_SESSION['sale_invoice_type'] != 'all') {
    if ($_SESSION['sale_invoice_type'] == 'tax_gst') {
        $sql_summary_mode .= " AND id_2 != ''";
    } elseif ($_SESSION['sale_invoice_type'] == 'tax_wo_gst') {
        $sql_summary_mode .= " AND id_2 = ''";
    } else {
        $sql_summary_mode .= " AND invoice_type = '$invoice_type'";
    }
}
if (!empty($_SESSION['sale_invoice_no'])) {
    $sql_summary_mode .= " AND invoice_no = '$invoice_no'";
}
if (!empty($_SESSION['sale_mop']) && $_SESSION['sale_mop'] != 'all') {
    if ($_SESSION['sale_mop'] == 'all_nocharge') {
        $sql_summary_mode .= " AND invoice_sale_restaurant.mode_of_payment != 'nocharge'";
    } else {
        $sql_summary_mode .= " AND invoice_sale_restaurant.mode_of_payment = '$mop'";
    }
}

$sql_summary_mode .= " GROUP BY mode_of_payments.mode_of_payment";

$result_summary = execute_query($sql_summary_mode);


// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row for detailed report
$header = [
    'S.No.', 'Table', 'Invoice No.', 'Item Total', 'Service Charge', 'Taxable Amount', 'SGST', 'CGST', 
    'Invoice Amount', 'Discount', 'Amount Payable', 'Sale Date', 'Unit', 'Mode of Payment', 'Paid Status'
];
$sheet->fromArray($header, NULL, 'A1');

// Helper function for table/room display
function getTableOrRoomName($row, $db) {
    if (strpos($row['storeid'], "room") === false) {
        // Assume function get_table exists, else return storeid as fallback
        if (function_exists('get_table')) {
            return 'T-' . get_table($row['storeid']);
        } else {
            return 'T-' . $row['storeid'];
        }
    } else {
        $roomid = str_replace("room_", "", $row['storeid']);
        $sql = "SELECT room_name FROM room_master WHERE sno = $roomid";
        $res_room = mysqli_query($db, $sql);
        $room_details = mysqli_fetch_assoc($res_room);
        return 'R-' . ($room_details['room_name'] ?? $roomid);
    }
}

// Fill detailed data
$rowIndex = 2;
$serial = 1;
while ($row = mysqli_fetch_assoc($result_data)) {
    $service_charge_amount = (float)($row['service_charge_amount'] ?? 0);
    $service_charge_tax_amount = (float)($row['service_charge_tax_amount'] ?? 0);
    $service_charge_vat = round($service_charge_tax_amount / 2, 2);
    $service_charge_sat = round($service_charge_tax_amount / 2, 2);

    $tot_vat = $service_charge_vat + (float)$row['tot_vat'];
    $tot_sat = $service_charge_sat + (float)$row['tot_sat'];

    $table = getTableOrRoomName($row, $db);

    // Calculate Paid Status
    $paid_status = '';
    if (strtolower($row['mop_type']) == 'credit') {
        $sql_mop = 'SELECT * FROM `customer_transactions` WHERE `number`="' . $row['sno'] . '"';
        $row_mop = mysqli_fetch_assoc(mysqli_query($db, $sql_mop));
        $paid_amount = (($row_mop['advance_set_amt'] ?? 0) + ($row_mop['credit_set_amt'] ?? 0));
        if ($paid_amount == 0) {
            $paid_status = 'UN-PAID';
        } elseif ($paid_amount == $row_mop['amount']) {
            $paid_status = 'PAID';
        } elseif ($paid_amount < $row_mop['amount']) {
            $paid_status = 'SEMI-PAID';
        }
    }

    $rowData = [
        $serial++,
        $table,
        $row['invoice_no'],
        $row['taxable_amount'],
        $service_charge_amount,
        $row['taxable_amount'] + $service_charge_amount,
        $tot_vat,
        $tot_sat,
        $row['total_amount'] + (float)$row['service_charge_total'],
        $row['tot_disc'] ?: '',
        $row['grand_total'],
        date("d-m-Y", strtotime($row['timestamp'])),
        $row['quantity'],
        strtoupper($row['mode_of_payment']),
        $paid_status,
    ];

    $sheet->fromArray($rowData, NULL, 'A' . $rowIndex++);
}

// Leave a gap then add Summary table header
$rowIndex += 2;
$summaryHeader = ['S.No.', 'Mode of Payment', 'Count', 'Amount'];
$sheet->fromArray($summaryHeader, NULL, 'A' . $rowIndex);
$rowIndex++;

$serial = 1;
$total = 0;
while ($row = mysqli_fetch_assoc($result_summary)) {
    $mode = ($row['mode_of_payment'] == 'bank_transfer') ? 'BANK TRANSFER' : strtoupper($row['mode_of_payment']);
    $count = $row['count'];
    $amount = round($row['grand_total'], 2);
    $total += $amount;

    $summaryRow = [$serial++, $mode, $count, $amount];
    $sheet->fromArray($summaryRow, NULL, 'A' . $rowIndex++);
}

// Add total row
$sheet->fromArray(['', '', 'Total:', $total], NULL, 'A' . $rowIndex);

// Output to browser as downloadable Excel file
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
