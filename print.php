<?php
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
include("scripts/settings.php");
logvalidate('','');
$colspan = 5;

// Ensure $db is available from settings.php
global $db;
if (!isset($db) || !$db instanceof mysqli) {
    die("Database connection not established. Check settings.php.");
}

// Fetch general settings
$sql = 'select * from general_settings where `desc`="company"';
$company = mysqli_fetch_assoc(execute_query($sql));
$company = $company['rate'] ?? 'BEDIS DREAM LAND HOTEL';

$sql = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_assoc(execute_query($sql));
$address = $address['rate'] ?? 'Ayodhya-224001 (U.P.)';

$sql = 'select * from general_settings where `desc`="contact"';
$contact = mysqli_fetch_assoc(execute_query($sql));
$contact = $contact['rate'] ?? '+91 933452112, +91 7755004900';

$sql = 'select * from general_settings where `desc`="email"';
$email = mysqli_fetch_assoc(execute_query($sql));
$email = $email['rate'] ?? 'bedisdreamland@gmail.com';

$sql = 'select * from general_settings where `desc`="website"';
$web = mysqli_fetch_assoc(execute_query($sql));
$web = $web['rate'] ?? 'www.bedisdreamland.com';

$sql = 'select * from general_settings where `desc`="gstin"';
$gstin = mysqli_fetch_assoc(execute_query($sql));
$gstin = $gstin['rate'] ?? '09CUPYS5983AZP';

$sql = 'select * from general_settings where `desc`="state"';
$state = mysqli_fetch_assoc(execute_query($sql));

if (isset($_GET['id'])) {
    $id = trim($_GET['id']); // Remove any leading/trailing spaces
    // Try sno as the primary ID column, test both string and integer
    $is_numeric_id = is_numeric($id) ? intval($id) : "'" . mysqli_real_escape_string($db, $id) . "'";
    $sql = "SELECT * FROM allotment WHERE sno = $is_numeric_id";
    $result = execute_query($sql);
    $details = mysqli_fetch_array($result);
    
    if (!$details) {
        // Enhanced debug
        echo "Debug: SQL Query = " . htmlspecialchars($sql) . "<br>";
        $count_sql = "SELECT COUNT(*) as count FROM allotment WHERE sno = $is_numeric_id";
        $count_result = execute_query($count_sql);
        $count_row = mysqli_fetch_assoc($count_result);
        echo "Debug: Row count for sno = $id: " . $count_row['count'] . "<br>";
        $result_check = $db->query("SHOW TABLES LIKE 'allotment'");
        if ($result_check->num_rows == 0) {
            die("Table 'allotment' does not exist.");
        }
        // List table columns for reference
        $columns_result = $db->query("SHOW COLUMNS FROM allotment");
        echo "Debug: Available columns in 'allotment' table: <br>";
        while ($row = $columns_result->fetch_assoc()) {
            echo $row['Field'] . "<br>";
        }
        die("No allotment details found for ID: " . $id . ". Please verify the ID exists in the 'sno' column of the 'allotment' table.");
    }

    // Validate allotment_date
    if (empty($details['allotment_date']) || strtotime($details['allotment_date']) === false) {
        $details['allotment_date'] = date("d-m-Y 12:00", strtotime('-1 day')); // Fallback to previous day noon
    }

    // Handle discounts
    $disc = (!empty($details['other_discount']) && $details['other_discount'] != 0) || !empty($details['discount']) ? 1 : 0;

    // Handle other charges
    $charges = !empty($details['other_charges']) ? 1 : 0;
    
    $tax_rate = $details['tax_rate'] / 2;

    $sql = "select * from customer where sno='" . mysqli_real_escape_string($db, $details['cust_id']) . "'";
    $customer = mysqli_fetch_array(execute_query($sql));
    $custid = $customer['sno'] ?? null;

    $sql_type = "select * from room_master where sno='" . mysqli_real_escape_string($db, $details['cust_id']) . "'";
    $row_type = mysqli_fetch_array(execute_query($sql_type));

    $sql_cat = "select * from category where sno='" . mysqli_real_escape_string($db, $row_type['category_id']) . "'";
    $row_cat = mysqli_fetch_array(execute_query($sql_cat));

    $sql = "select * from customer_transactions where cust_id='" . mysqli_real_escape_string($db, $details['cust_id']) . "' and allotment_id='" . mysqli_real_escape_string($db, $details['sno']) . "'";
    $cust_transact = mysqli_fetch_array(execute_query($sql));

    $sql = "select room_name, room_type, floor_name, category_id from room_master join category on category.sno = category_id join floor_master on floor_master.sno = floor_id where room_master.sno='" . mysqli_real_escape_string($db, $details['room_id']) . "'";
    $room_details = mysqli_fetch_array(execute_query($sql));

    // Handle exit_date
    if (empty($details['exit_date']) || strtotime($details['exit_date']) === false) {
        $details['exit_date'] = isset($_GET['vt']) && strtotime($_GET['vt']) !== false ? $_GET['vt'] : date("d-m-Y H:i"); // Use current time
    }

    // Ensure check-out is after check-in
    $checkin_ts = strtotime($details['allotment_date']);
    $checkout_ts = strtotime($details['exit_date']);
    if ($checkout_ts <= $checkin_ts) {
        $details['exit_date'] = date("d-m-Y H:i", strtotime($details['allotment_date'] . '+1 day 11:00')); // Next day 11:00 AM
    }

    $days = get_days($details['allotment_date'], $details['exit_date']);
    
    $sql11 = "SELECT * FROM `customer_transactions` WHERE cust_id='" . mysqli_real_escape_string($db, $custid) . "' and allotment_id = '" . mysqli_real_escape_string($db, $details['sno']) . "' and type='sale_restaurant' and (remarks='credit' or remarks='6')";
    $rest_result = execute_query($sql11);

    // Prepare restaurant data for calculate_invoice_amount
    $restaurant_data = [];
    while ($rest_row = mysqli_fetch_assoc($rest_result)) {
        $restaurant_data[] = ['amount' => $rest_row['amount']];
    }
    mysqli_data_seek($rest_result, 0); // Reset result pointer

    // Fetch hotel state from settings
    $hotel_state = $state['rate'] ?? 'Uttar Pradesh';

    // Calculate invoice amounts using the function
    $invoice = calculate_invoice_amount($details, $customer['state'] ?? '', $hotel_state, $restaurant_data);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/pop.css" TYPE="text/css" REL="stylesheet" media="all">
<style type="text/css">
@media print {
    input#btnPrint {
        display: none;
    }	
}
td, th { border: 0px solid; }
#tablediv table tr { height: 14px; }
</style>
</head>
<body>
    <div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /> <a href="print_combined.php?id=<?php echo $details['sno']; ?>">Print Combined Receipt</a></div>
    <div id="wrapper" style="page-break-after:avoid;">
        <div id="tablediv">
            <table width="100%" border="0" style="border-bottom: 1px solid;">
                <tr>
                    <th colspan="3"><img src="images/a2.png" height="150px;" width="170px;" style="text-align:center; margin-left:290px; object-fit:cover;" /></th>
                </tr>
                <tr>
                    <th colspan="3"><h3 style="text-decoration:underline;"><?php echo $details['invoice_type'] == 'tax' ? 'TAX INVOICE' : 'TAX INVOICE'; ?></h3></th>
                </tr>
                <tr>
                    <th colspan="3"><h2><?php echo $company; ?></h2></th>
                </tr>
                <tr>
                    <th colspan="3"><h2><?php echo $address; ?></h2></th>
                </tr>
                <tr>
                    <th colspan="3"><h3><?php echo $contact; ?></h3></th>
                </tr>
                <tr>
                    <th colspan="3"><h3>E-Mail: <?php echo $email; ?>, Website: <?php echo $web; ?></h3></th>
                </tr>
                <tr>
                    <th><h3>GSTIN: <?php echo $gstin; ?></h3></th>
                </tr>
            </table>
        </div>
        <table width="100%" style="border-bottom: 1px solid;">
            <tr>
                <td style="border: 0px solid; line-height: 24px;" width="50%">
                    <strong><h3 style="margin: 0px; padding: 0px; font-size: 18px;">Bill To,</h3></strong>
                    <?php echo !empty($details['guest_name']) ? '<strong>GUEST NAME: </strong>' . strtoupper($details['guest_name']) . '<br>' : '<strong>Guest Name: </strong><br>'; ?>
                    <?php echo '<strong>COMPANY NAME: </strong>' . strtoupper($customer['company_name'] ?? ''); ?>
                    <?php echo !empty($customer['id_2']) ? '<br><strong>GSTIN: </strong>' . $customer['id_2'] : '<br><strong>GSTIN: </strong>'; ?>
                    <?php echo !empty($customer['mobile']) ? '<br><strong>MOBILE: </strong>' . $customer['mobile'] : '<br><strong>MOBILE: </strong>'; ?>
                    <?php echo !empty($customer['city']) ? '<br><strong>CITY: </strong>' . $customer['city'] : '<br><strong>CITY: </strong>'; ?>
                    <?php echo !empty($customer['zipcode']) ? '<br><strong>PINCODE: </strong>' . $customer['zipcode'] : '<br><strong>PINCODE: </strong>'; ?>
                    <?php echo !empty($customer['id_3']) ? '<br><strong>' . $customer['id_type'] . '</strong>: ' . $customer['id_3'] : ''; ?>
                    <?php echo '<br><strong>SAC/HSN: </strong>' . (!empty($customer['id_1']) ? $customer['id_1'] : ''); ?>
                    <?php echo '<br><strong>ADDRESS: </strong>' . (!empty($customer['address']) ? $customer['address'] : $details['guest_address'] ?? ''); ?>
                    <?php echo '<br><strong>OCCUPANCY: </strong>' . $details['occupancy'] ?? '0'; ?>
                </td>
                <td style="border: 0px solid;" width="50%">
                    <table width="100%" style="border-bottom: 0px solid;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><strong>INVOICE NO:</strong></td>
                            <td>BDL/<?php echo $details['financial_year'] . '/' . $details['invoice_no'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>REGISTRATION NO:</strong></td>
                            <td><?php echo $details['financial_year'] . '/' . $details['registration_no'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>DATE:</strong></td>
                            <td><?php echo !empty($details['exit_date']) ? date("d-m-Y", strtotime($details['exit_date'])) : date("d-m-Y"); ?></td>
                        </tr>
                        <tr>
                            <td><strong>ROOM CATEGORY:</strong></td>
                            <td><?php echo $room_details['room_type'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>ROOM NUMBER:</strong></td>
                            <td><?php echo $room_details['room_name'] . ' (' . ($room_details['floor_name'] ?? '') . ')'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>CHECK IN:</strong></td>
                            <td><?php echo date("d-m-Y H:i:s", strtotime($details['allotment_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>CHECK OUT:</strong></td>
                            <td><?php echo date("d-m-Y H:i:s", strtotime($details['exit_date'])); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" class="td-center">
            <tr>
                <th width="25%">Date</th>
                <th width="20%">Room Rent</th>
                <th width="15%">CGST (6%)</th>
                <th width="15%">SGST (6%)</th>
                <th width="25%">Total</th>
            </tr>
            <?php
            foreach ($invoice['daily_breakdown'] as $day) {
            ?>
                <tr>
                    <td><?php echo $day['date']; ?></td>
                    <td><?php echo round($day['room_rent'], 2); ?></td>
                    <td><?php echo round($day['cgst'], 2); ?></td>
                    <td><?php echo round($day['sgst'], 2); ?></td>
                    <td><?php echo round($day['total'], 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <th>Total:</th>
                <th><?php echo $invoice['taxable_total']; ?></th>
                <th><?php echo $invoice['total_cgst']; ?></th>
                <th><?php echo $invoice['total_sgst']; ?></th>
                <th><?php echo $invoice['grand_total']; ?></th>
            </tr>
            <tr>
                <th colspan="3"></th>
                <th>Round Off:</th>
                <th><?php echo $invoice['round_off']; ?></th>
            </tr>
            <tr>
                <th colspan="3"></th>
                <th>Amount Payable:</th>
                <th><?php echo $invoice['amount_payable']; ?></th>
            </tr>
        </table>
        <table width="100%" style="border-top: 1px solid;">
            <tr>
                <td colspan="<?php echo $colspan - 2; ?>"><h3 style="text-transform: capitalize;">Amount Payable (In Words): <?php echo int_to_words($invoice['amount_payable']); ?> Rupees Only</h3></td>
                <td colspan="5" style="text-align: right;">For: <?php echo $company; ?><br /></td>
            </tr>
            <tr>
                <th colspan="<?php echo $colspan - 4; ?>"><p>Remarks: <?php echo $cust_transact['remarks'] ?? ''; ?><br>     <br></p></th>
                <th><td colspan="5" style="text-align: right;"><br>(Authorised Signatory)</td></th>
            </tr>
            <tr>
                <td colspan="5" style="line-height: 18px;">CHECK OUT TIME: <?php echo date("h:i A"); ?><br>This Is A Computer Generated Invoice. Does Not Require Signature.<br>Subject To Ayodhya Jurisdiction only</td>
            </tr>
        </table>
    </div>
</body>
</html>