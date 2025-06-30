<?php
include("../scripts/settings.php");

$id = $_POST['id'];
$amount = 0;

$sql = "SELECT * FROM allotment WHERE sno='" . mysqli_real_escape_string($db, $id) . "'";
$result = execute_query($sql);
$details = mysqli_fetch_assoc($result);

if ($details) {
    $tot_original_rent = $details['original_room_rent'] ?? 0;
    $tot_disc = ($details['other_discount'] ?? 0) + ($details['discount_value'] ?? 0);
    $tot_other_charge = $details['other_charges'] ?? 0;
    $taxable_tot = $tot_original_rent + $tot_other_charge - $tot_disc;

    $tax_rate = $details['tax_rate'] ?? 0;
    $tax = round($taxable_tot * $tax_rate / 100, 2);
    $tot = $taxable_tot + $tax * 2;

    // Restaurant Bill
    $tot_res = 0;
    $sql_rest = "SELECT amount FROM customer_transactions WHERE cust_id='" . mysqli_real_escape_string($db, $details['cust_id']) . "' AND allotment_id='" . $id . "' AND type='sale_restaurant'";
    $rest_result = execute_query($sql_rest);
    while ($rest_row = mysqli_fetch_assoc($rest_result)) {
        $tot_res += (float)$rest_row['amount'];
    }

    $tot += $tot_res;
    $amount = round($tot); // Final Payable
}
echo $amount;
