<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
page_header();
navigation('');
?>
 <div id="container">
	<h2>Daily Summary</h2>
	<div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment_daily" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
        	<tr style="background:#CCC;">
            
            	<th>Date From</th>
                <td>
                <span>
                <script type="text/javascript" language="javascript">
				document.writeln(DateInput('allot_from', 'report_allotment_daily', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1))
                </script>
                </span>
                </td>
            	<th>Date To</th>
                <td>
                <span>
                <script type="text/javascript" language="javascript">
                document.writeln(DateInput('allot_to', 'report_allotment_daily', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4))
                </script>
                </span>
                </td>
            </tr>
        	<tr class="no-print">
            	<th colspan="3">
                	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                </th>
                <th colspan="3">
                	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                </th>
            </tr>
        </table>
	</form>
	<table>
		<tr><th colspan="10" style="font-size:18px;">Invoice Summary(Room)</th></tr>
		<?php
			$i=1;
			$tot_taxable=0;
			$tot_amount=0;
			$tot_original_amount=0;
			$tot_tax=0;
			$tot_discount=0;
			$tot_other_charges=0;
			$pay_amount = 0;

			$tot_tax_taxable=0;
			$tot_tax_amount=0;
			$tot_tax_original_amount=0;
			$tot_tax_tax=0;
			$tot_tax_discount=0;
			$tot_tax_other_charges=0;
			
			$count=0;
			$count_tax=0;
			$cancel_count=0;
			
			$grand_advance_amount = 0;
			$grand_pay_amount = 0;
			$grand_tot_taxable=0;
			$grand_tot_amount=0;
			$grand_tot_original_amount=0;
			$grand_tot_tax=0;
			$grand_tot_discount=0;
			$grand_tot_other_charges=0;
			
			$cancel_tot_taxable=0;
			$cancel_original_amount=0;
			$cancel_tot_amount=0;
			$cancel_tot_tax=0;
			$cancel_tot_discount=0;
			$cancel_tot_other_charges=0;
			$totdisc = 0;
			if(isset($_POST['allot_from'])){
				$_POST['allot_from'] = date("Y-m-d", strtotime($_POST['allot_from']));
				$_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to']));
				$_POST['allot_to_re'] = date("Y-m-d", strtotime($_POST['allot_to'])+86400);
			}
			else{
				$_POST['allot_from'] = date("Y-m-d");
				$_POST['allot_to'] = date("Y-m-d");
				$_POST['allot_to_re'] = date("Y-m-d", strtotime(date('Y-m-d'))+86400);
			}
			// $sql_room = 'SELECT * FROM `allotment` WHERE `exit_date` IS NOT NUlL AND `exit_date`!="" AND `bill_create_date` >="'.$_POST['allot_from'].'" AND bill_create_date <"'.$_POST['allot_to_re'].'"';
			// //echo $sql_room;
			// $result_room = execute_query($sql_room);
			// while ($row_room = mysqli_fetch_array($result_room)) {
			// 	$days = get_days($row_room['allotment_date'], $row_room['exit_date']);
			
			// 	if($row_room['other_charges']==''){
			// 		$other_charge = 0;
			// 	}
			// 	if($row_room['other_charges']!=''){
			// 		$other_charge = $row_room['other_charges'];
			// 	}
				
			// 	if(strtolower(trim($row_room['invoice_type']))!='bill_of_supply'){
			// 		$base_rent = round(($row_room['original_room_rent']-($row_room['other_discount']+$row_room['discount_value'])+$other_charge),2);
			// 		$tax = round((($base_rent*$row_room['tax_rate']/200)),2);
			// 	}
			// 	else{
			// 		$base_rent=$row_room['room_rent']+$other_charge;
			// 		$tax=0;
			// 	}
				
			// 	$amount = $row_room['room_rent']*$days;
			// 	if($row_room['cancel_date']==''){
			// 		if($row_room['invoice_type']=='tax'){
			// 			$tot_tax_discount+=($row_room['discount_value']+$row_room['other_discount'])*$days;
			// 			$tot_tax_other_charges+=$row_room['other_charges']*$days;
			// 			$tot_tax_original_amount+=$row_room['original_room_rent']*$days;
			// 			$tot_tax_amount+=$amount;
			// 			$tot_tax_tax += $tax*$days;
			// 			$tot_tax_taxable+=$base_rent*$days;
			// 			$count_tax++;

			// 		}
			// 		else{
			// 			$tot_discount+=($row_room['discount_value']+$row_room['other_discount'])*$days;
			// 			$tot_other_charges+=$row_room['other_charges']*$days;
			// 			$tot_original_amount+=$row_room['original_room_rent']*$days;
			// 			$tot_amount+=$amount;
			// 			$tot_tax += $tax*$days;
			// 			$tot_taxable+=$base_rent*$days;
			// 			$count++;
			// 		}
					
			// 		$grand_tot_discount+=($row_room['other_discount']+$row_room['discount_value'])*$days;
			// 		$grand_tot_other_charges+=$row_room['other_charges']*$days;
			// 		$grand_tot_original_amount+=$row_room['original_room_rent']*$days;
			// 		$grand_tot_amount+=$amount;
			// 		$grand_tot_tax += $tax*$days;
			// 		$grand_tot_taxable+=$base_rent*$days;
			// 	}
			// }
			echo '<tr>
				<th>&nbsp;</th>
				<th>Count</th>
				<th>Gross Amount</th>
				<th>Extra Bed</th>
				<th>Discount</th>
				<th>Net Amount</th>
				<th>CGST</th>
				<th>SGST</th>
				<th>Total Tax</th>
				<th>Total</th>
			</tr>
			<tr>
				<th style="color:black;font-size:18px;">Taxable</th>
				<td class="number" style="color:black;font-size:18px;"><b>'.$count_tax.'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_tax_discount+$tot_tax_taxable), 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$tot_tax_other_charges, 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$tot_tax_discount, 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$tot_tax_taxable, 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$tot_tax_tax, 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$tot_tax_tax, 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_tax_tax+$tot_tax_tax), 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$tot_tax_amount, 2, '.', '').'</b></td>
			</tr>
			<tr style="background:#ccc;">
				<th style="color:black;font-size:18px;">Non Taxable</th>
				<td class="number" style="color:black;font-size:18px;"><b>'.$count.'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_taxable+$tot_discount), 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_other_charges), 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_discount), 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_taxable), 2, '.', '').'</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>0</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>0</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>0</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)($tot_amount), 2, '.', '').'</b></td>
			</tr>
			<tr>
			<th style="text-align:right;color:black;font-size:18px;">Grand Total : </th>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format($count + $count_tax).'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_original_amount, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_other_charges, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_discount, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_taxable, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_tax, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_tax, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_tax+$grand_tot_tax, 2, '.', '').'</b></td>
			<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_tot_amount, 2, '.', '').'</b></td>
			</tr>';
		?>
	</table>
	<table>
		<tr><th colspan="8" style="font-size:18px;">Invoice Summary(Room Service)</th></tr>
		<?php 
			$sql_room_service = 'select count(*) as count , sum(taxable_amount) as taxable_amount, sum(tot_vat) as tot_vat, sum(tot_sat) as tot_sat, sum(total_amount) as total_amount, if(other_discount="", sum(tot_disc), sum(other_discount)) as total_discount, sum(grand_total) as grand_total, sum(quantity) as quantity from invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND storeid like "room%"';
			//echo $sql_room_service;
			$result_room_service = execute_query($sql_room_service);
			while ( $row_room_service = mysqli_fetch_array($result_room_service)) {
			echo '<tr>
				<th>&nbsp;</th>
				<th>Count</th>
				<th>Taxable Amount</th>
				<th>SGST</th>
				<th>CGST</th>
				<th>Invoice Amount</th>
				<th>Discount</th>
				<th>Amount Payable</th>
			</tr>';
			echo '<tr><th style="color:black;font-size:18px;">Total</th><td style="color:black;font-size:18px;"><b>'.$row_room_service['count'].'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service['taxable_amount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service['tot_vat'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service['tot_sat'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service['total_amount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service['total_discount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service['grand_total'], 2, '.', '').'</b></td></tr>';
		}
		?>
	</table>
	<table>
		<tr><th colspan="8" style="font-size:18px;">Invoice Summary(Restaurant)</th></tr>
		<?php 
			$sql_restaurant = 'select count(*) as count , sum(taxable_amount) as taxable_amount, sum(tot_vat) as tot_vat, sum(tot_sat) as tot_sat, sum(total_amount) as total_amount, if(other_discount="", sum(tot_disc), sum(other_discount)) as total_discount, sum(grand_total) as grand_total, sum(quantity) as quantity from invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND storeid not like "room%"';
			//echo $sql_restaurant;
			$result_restaurant = execute_query($sql_restaurant);
			while ( $row_restaurant = mysqli_fetch_array($result_restaurant)) {
			echo '<tr>
				<th>&nbsp;</th>
				<th>Count</th>
				<th>Taxable Amount</th>
				<th>SGST</th>
				<th>CGST</th>
				<th>Invoice Amount</th>
				<th>Discount</th>
				<th>Amount Payable</th>
			</tr>';
			echo '<tr><th style="color:black;font-size:18px;">Total</th><td style="color:black;font-size:18px;"><b>'.$row_restaurant['count'].'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant['taxable_amount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant['tot_vat'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant['tot_sat'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant['total_amount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant['total_discount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant['grand_total'], 2, '.', '').'</b></td></tr>';
		}
		?>
	</table>
	<table>
		<tr><th colspan="8" style="font-size:18px;">Invoice Summary(Banquet)</th></tr>
		<?php 
			$sql_banquet = 'select count(*) as count , sum(amount) as amount, sum(cgst) as cgst, sum(sgst) as sgst, sum(grand_total) as grand_total , sum(total_quantity) as quantity from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'"';
			//echo $sql_banquet;
			$result_banquet = execute_query($sql_banquet);
			while ( $row_banquet = mysqli_fetch_array($result_banquet)) {
			echo '<tr>
				<th>&nbsp;</th>
				<th>Count</th>
				<th>Taxable Amount</th>
				<th>SGST</th>
				<th>CGST</th>
				<th>Grand Amount</th>
			</tr>';
			echo '<tr><th style="color:black;font-size:18px;">Total</th><td style="color:black;font-size:18px;"><b>'.$row_banquet['count'].'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet['amount'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet['cgst'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet['sgst'], 2, '.', '').'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet['grand_total'], 2, '.', '').'</b></td></tr>';
		}
		?>
	</table>
	<!--<table>
		<tr><th colspan="8" style="font-size:18px;">Advance Summary</th></tr>
		<?php 
			$sql_advance = 'select count(*) as count , sum(advance_amount) as amount from advance_booking where created_on>="'.$_POST['allot_from'].'" and created_on<"'.$_POST['allot_to_re'].'"';
			//echo $sql_banquet;
			$result_advance = execute_query($sql_advance);
			while ($row_advance = mysqli_fetch_array($result_advance)) {
			echo '<tr>
				<th>&nbsp;</th>
				<th>Count</th>
				<th>Amount</th>
			</tr>';
			echo '<tr><th style="color:black;font-size:18px;">Total</th><td style="color:black;font-size:18px;"><b>'.$row_advance['count'].'</b></td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_advance['amount'], 2, '.', '').'</b></td></tr>';
		}
		?>
	</table>-->
	<table>
		<tr><th colspan="15" style="font-size:18px;">Mode of Payment Summary</th></tr>
		<tr>
			<th rowspan="2">Type</th>
			<th colspan="2">Cash</th>
			<th colspan="2">Card</th>
			<th colspan="2">Credit</th>
			<th colspan="2">Paytm</th>
			<th colspan="2">Bank Transfer</th>
			<th colspan="2">Cheque</th>
			<th colspan="2">All</th>
		</tr>
		<tr>
			<th>Count</th>
			<th>Amount</th>
			<th>Count</th>
			<th>Amount</th>
			<th>Count</th>
			<th>Amount</th>
			<th>Count</th>
			<th>Amount</th>
			<th>Count</th>
			<th>Amount</th>
			<th>Count</th>
			<th>Amount</th>
			<th>Count</th>
			<th>Amount</th>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">Room</th>
		<?php 
			$sql_room_cash = 'SELECT count(*) as count , sum(`amount`) as amount FROM `customer_transactions` WHERE `type`="RENT" AND `mop`="cash" AND `created_on`>="'.$_POST['allot_from'].'" AND `created_on`<"'.$_POST['allot_to_re'].'"';
			$result_room_cash = execute_query($sql_room_cash);
			$row_room_cash = mysqli_fetch_array($result_room_cash);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_cash['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_cash['amount'], 2, '.', '').'</td>';

			$sql_room_card = 'SELECT count(*) as count , sum(`amount`) as amount FROM `customer_transactions` WHERE `type`="RENT" AND `mop`="card" AND `created_on`>="'.$_POST['allot_from'].'" AND `created_on`<"'.$_POST['allot_to_re'].'"';
			$result_room_card = execute_query($sql_room_card);
			$row_room_card = mysqli_fetch_array($result_room_card);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_card['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_card['amount'], 2, '.', '').'</td>';

			$sql_room_credit= 'SELECT count(*) as count , sum(`amount`) as amount FROM `customer_transactions` WHERE `type`="RENT" AND `mop`="credit" AND `created_on`>="'.$_POST['allot_from'].'" AND `created_on`<"'.$_POST['allot_to_re'].'"';
			$result_room_credit = execute_query($sql_room_credit);
			$row_room_credit = mysqli_fetch_array($result_room_credit);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_credit['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_credit['amount'], 2, '.', '').'</td>';

			$sql_room_paytm= 'SELECT count(*) as count , sum(`amount`) as amount FROM `customer_transactions` WHERE `type`="RENT" AND `mop`="PAYTM" AND `created_on`>="'.$_POST['allot_from'].'" AND `created_on`<"'.$_POST['allot_to_re'].'"';
			$result_room_paytm = execute_query($sql_room_paytm);
			$row_room_paytm = mysqli_fetch_array($result_room_paytm);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_paytm['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_paytm['amount'], 2, '.', '').'</td>';

			$sql_room_bank_transfer= 'SELECT count(*) as count , sum(`amount`) as amount FROM `customer_transactions` WHERE `type`="RENT" AND `mop`="bank_transfer" AND `created_on`>="'.$_POST['allot_from'].'" AND `created_on`<"'.$_POST['allot_to_re'].'"';
			$result_room_bank_transfer = execute_query($sql_room_bank_transfer);
			$row_room_bank_transfer = mysqli_fetch_array($result_room_bank_transfer);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_bank_transfer['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_bank_transfer['amount'], 2, '.', '').'</td>';

			$sql_room_cheque= 'SELECT count(*) as count , sum(`amount`) as amount FROM `customer_transactions` WHERE `type`="RENT" AND `mop`="cheque" AND `created_on`>="'.$_POST['allot_from'].'" AND `created_on`<"'.$_POST['allot_to_re'].'"';
			$result_room_cheque = execute_query($sql_room_cheque);
			$row_room_cheque = mysqli_fetch_array($result_room_cheque);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_cheque['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_cheque['amount'], 2, '.', '').'</td>';

			$room_count = $row_room_credit['count']+$row_room_card['count']+$row_room_cash['count']+$row_room_paytm['count']+$row_room_bank_transfer['count']+$row_room_cheque['count'];
			$room_amount = $row_room_credit['amount']+$row_room_card['amount']+$row_room_cash['amount']+$row_room_paytm['amount']+$row_room_bank_transfer['amount']+$row_room_cheque['amount'];
			echo '<td style="color:black;font-size:18px;"><b>'.$room_count.'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$room_amount, 2, '.', '').'</td>';
		?>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">Room Service</th>
		<?php
			$sql_room_service_cash = 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="cash" and storeid  like "room%"';
			//echo $sql_room_service_cash;
			$result_room_service_cash = execute_query($sql_room_service_cash);
			$row_room_service_cash = mysqli_fetch_array($result_room_service_cash);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_service_cash['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service_cash['amount'], 2, '.', '').'</td>';

			$sql_room_service_card = 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="card" and storeid like "room%"';
			$result_room_service_card = execute_query($sql_room_service_card);
			$row_room_service_card = mysqli_fetch_array($result_room_service_card);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_service_card['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service_card['amount'], 2, '.', '').'</td>';

			$sql_room_service_credit= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="credit" and storeid like "room%"';
			$result_room_service_credit = execute_query($sql_room_service_credit);
			$row_room_service_credit = mysqli_fetch_array($result_room_service_credit);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_service_credit['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service_credit['amount'], 2, '.', '').'</td>';

			$sql_room_service_paytm= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="paytm" and storeid like "room%"';
			$result_room_service_paytm = execute_query($sql_room_service_paytm);
			$row_room_service_paytm = mysqli_fetch_array($result_room_service_paytm);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_service_paytm['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service_paytm['amount'], 2, '.', '').'</td>';

			$sql_room_service_bank_transfer= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="bank_transfer" and storeid like "room%"';
			$result_room_service_bank_transfer = execute_query($sql_room_service_bank_transfer);
			$row_room_service_bank_transfer = mysqli_fetch_array($result_room_service_bank_transfer);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_service_bank_transfer['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service_bank_transfer['amount'], 2, '.', '').'</td>';

			$sql_room_service_cheque= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="cheque" and storeid like "room%"';
			$result_room_service_cheque = execute_query($sql_room_service_cheque);
			$row_room_service_cheque = mysqli_fetch_array($result_room_service_cheque);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_room_service_cheque['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_room_service_cheque['amount'], 2, '.', '').'</td>';

			$room_service_count = $row_room_service_credit['count']+$row_room_service_card['count']+$row_room_service_cash['count']+$row_room_service_paytm['count']+$row_room_service_bank_transfer['count']+$row_room_service_cheque['count'];
			$room_service_amount = $row_room_service_credit['amount']+$row_room_service_card['amount']+$row_room_service_cash['amount']+$row_room_service_paytm['amount']+$row_room_service_bank_transfer['amount']+$row_room_service_cheque['amount'];
			echo '<td style="color:black;font-size:18px;"><b>'.$room_service_count.'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$room_service_amount, 2, '.', '').'</td>';
		?>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">Restaurant</th>
		<?php 
			$sql_restaurant_cash = 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="cash" and storeid not like "room%"';
			//echo $sql_restaurant_cash;
			$result_restaurant_cash = execute_query($sql_restaurant_cash);
			$row_restaurant_cash = mysqli_fetch_array($result_restaurant_cash);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_restaurant_cash['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant_cash['amount'], 2, '.', '').'</td>';

			$sql_restaurant_card = 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="card" and storeid not like "room%"';
			$result_restaurant_card = execute_query($sql_restaurant_card);
			$row_restaurant_card = mysqli_fetch_array($result_restaurant_card);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_restaurant_card['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant_card['amount'], 2, '.', '').'</td>';

			$sql_restaurant_credit= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="credit" and storeid not like "room%"';
			$result_restaurant_credit = execute_query($sql_restaurant_credit);
			$row_restaurant_credit = mysqli_fetch_array($result_restaurant_credit);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_restaurant_credit['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant_credit['amount'], 2, '.', '').'</td>';

			$sql_restaurant_paytm= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="PAYTM" and storeid not like "room%"';
			$result_restaurant_paytm = execute_query($sql_restaurant_paytm);
			$row_restaurant_paytm = mysqli_fetch_array($result_restaurant_paytm);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_restaurant_paytm['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant_paytm['amount'], 2, '.', '').'</td>';

			$sql_restaurant_bank_transfer= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="bank_transfer" and storeid not like "room%"';
			$result_restaurant_bank_transfer = execute_query($sql_restaurant_bank_transfer);
			$row_restaurant_bank_transfer = mysqli_fetch_array($result_restaurant_bank_transfer);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_restaurant_bank_transfer['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant_bank_transfer['amount'], 2, '.', '').'</td>';

			$sql_restaurant_cheque= 'SELECT count(*) as count , sum(`grand_total`) as amount FROM invoice_sale_restaurant where mode_of_payment !="nocharge" and timestamp>="'.$_POST['allot_from'].'" and timestamp<"'.$_POST['allot_to_re'].'" AND mode_of_payment="cheque" and storeid not like "room%"';
			$result_restaurant_cheque = execute_query($sql_restaurant_cheque);
			$row_restaurant_cheque = mysqli_fetch_array($result_restaurant_cheque);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_restaurant_cheque['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_restaurant_cheque['amount'], 2, '.', '').'</td>';

			$restaurant_count = $row_restaurant_credit['count']+$row_restaurant_card['count']+$row_restaurant_cash['count']+$row_restaurant_paytm['count']+$row_restaurant_bank_transfer['count']+$row_restaurant_cheque['count'];
			$restaurant_amount = $row_restaurant_credit['amount']+$row_restaurant_card['amount']+$row_restaurant_cash['amount']+$row_restaurant_paytm['amount']+$row_restaurant_bank_transfer['amount']+$row_restaurant_cheque['amount'];
			echo '<td style="color:black;font-size:18px;"><b>'.$restaurant_count.'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$restaurant_amount, 2, '.', '').'</td>';
		?>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">Banquet</th>
		<?php 
			$sql_banquet_cash = 'select count(*) as count , sum(grand_total) as amount from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'" and `mop`="cash"';
			$result_banquet_cash = execute_query($sql_banquet_cash);
			$row_banquet_cash = mysqli_fetch_array($result_banquet_cash);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_banquet_cash['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet_cash['amount'], 2, '.', '').'</td>';

			$sql_banquet_card = 'select count(*) as count , sum(grand_total) as amount from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'" and `mop`="card"';
			$result_banquet_card = execute_query($sql_banquet_card);
			$row_banquet_card = mysqli_fetch_array($result_banquet_card);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_banquet_card['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet_card['amount'], 2, '.', '').'</td>';

			$sql_banquet_credit = 'select count(*) as count , sum(grand_total) as amount from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'" and `mop`="credit"';
			$result_banquet_credit = execute_query($sql_banquet_credit);
			$row_banquet_credit = mysqli_fetch_array($result_banquet_credit);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_banquet_credit['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet_credit['amount'], 2, '.', '').'</td>';

			$sql_banquet_paytm = 'select count(*) as count , sum(grand_total) as amount from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'" and `mop`="PAYTM"';
			$result_banquet_paytm = execute_query($sql_banquet_paytm);
			$row_banquet_paytm = mysqli_fetch_array($result_banquet_paytm);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_banquet_paytm['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet_paytm['amount'], 2, '.', '').'</td>';

			$sql_banquet_bank_transfer = 'select count(*) as count , sum(grand_total) as amount from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'" and `mop`="bank_transfer"';
			$result_banquet_bank_transfer = execute_query($sql_banquet_bank_transfer);
			$row_banquet_bank_transfer = mysqli_fetch_array($result_banquet_bank_transfer);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_banquet_bank_transfer['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet_bank_transfer['amount'], 2, '.', '').'</td>';

			$sql_banquet_cheque = 'select count(*) as count , sum(grand_total) as amount from banquet_hall where check_in_date>="'.$_POST['allot_from'].'" and check_in_date<"'.$_POST['allot_to_re'].'" and `mop`="cheque"';
			$result_banquet_cheque = execute_query($sql_banquet_cheque);
			$row_banquet_cheque = mysqli_fetch_array($result_banquet_cheque);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_banquet_cheque['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_banquet_cheque['amount'], 2, '.', '').'</td>';

			$banquet_count = $row_banquet_credit['count']+$row_banquet_card['count']+$row_banquet_cash['count']+$row_banquet_paytm['count']+$row_banquet_bank_transfer['count']+$row_banquet_cheque['count'];
			$banquet_amount = $row_banquet_credit['amount']+$row_banquet_card['amount']+$row_banquet_cash['amount']+$row_banquet_paytm['amount']+$row_banquet_bank_transfer['amount']+$row_banquet_cheque['amount'];
			echo '<td style="color:black;font-size:18px;"><b>'.$banquet_count.'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$banquet_amount, 2, '.', '').'</td>';
		?>
		</tr>
		<!--<tr>
			<th style="color:black;font-size:18px;">Advance</th>
		<?php 
			$sql_advance_cash = 'select count(*) as count , sum(amount) as amount from customer_transactions where created_on>="'.$_POST['allot_from'].'" and created_on<"'.$_POST['allot_to_re'].'" and `mop`="cash" and `type`="ADVANCE_AMT"';
			//echo $sql_advance_cash;
			$result_advance_cash = execute_query($sql_advance_cash);
			$row_advance_cash = mysqli_fetch_array($result_advance_cash);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_cash['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_advance_cash['amount'], 2, '.', '').'</td>';

			$sql_advance_card = 'select count(*) as count , sum(amount) as amount from customer_transactions where created_on>="'.$_POST['allot_from'].'" and created_on<"'.$_POST['allot_to_re'].'" and `mop`="card" and `type`="ADVANCE_AMT"';
			//echo $sql_advance_cash;
			$result_advance_card = execute_query($sql_advance_card);
			$row_advance_card = mysqli_fetch_array($result_advance_card);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_card['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_advance_card['amount'], 2, '.', '').'</td>';

			$sql_advance_credit = 'select count(*) as count , sum(amount) as amount from customer_transactions where created_on>="'.$_POST['allot_from'].'" and created_on<"'.$_POST['allot_to_re'].'" and `mop`="credit" and `type`="ADVANCE_AMT"';
			//echo $sql_advance_credit;
			$result_advance_credit = execute_query($sql_advance_credit);
			$row_advance_credit = mysqli_fetch_array($result_advance_credit);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_credit['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_advance_credit['amount'], 2, '.', '').'</td>';

			$sql_advance_paytm = 'select count(*) as count , sum(amount) as amount from customer_transactions where created_on>="'.$_POST['allot_from'].'" and created_on<"'.$_POST['allot_to_re'].'" and `mop`="paytm" and `type`="ADVANCE_AMT"';
			//echo $sql_advance_paytm;
			$result_advance_paytm = execute_query($sql_advance_paytm);
			$row_advance_paytm = mysqli_fetch_array($result_advance_paytm);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_paytm['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_advance_paytm['amount'], 2, '.', '').'</td>';

			$sql_advance_bank_transfer = 'select count(*) as count , sum(amount) as amount from customer_transactions where created_on>="'.$_POST['allot_from'].'" and created_on<"'.$_POST['allot_to_re'].'" and `mop`="bank_transfer" and `type`="ADVANCE_AMT"';
			//echo $sql_advance_bank_transfer;
			$result_advance_bank_transfer = execute_query($sql_advance_bank_transfer);
			$row_advance_bank_transfer = mysqli_fetch_array($result_advance_bank_transfer);
			echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_bank_transfer['count'].'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$row_advance_bank_transfer['amount'], 2, '.', '').'</td>';

			$advance_count = $row_advance_credit['count']+$row_advance_card['count']+$row_advance_cash['count']+$row_advance_paytm['count']+$row_advance_bank_transfer['count'];
			$advance_amount = $row_advance_credit['amount']+$row_advance_card['amount']+$row_advance_cash['amount']+$row_advance_paytm['amount']+$row_advance_bank_transfer['amount'];
			echo '<td style="color:black;font-size:18px;"><b>'.$advance_count.'</td><td style="color:black;font-size:18px;"><b>'.number_format((float)$advance_amount, 2, '.', '').'</td>';
		?>
		</tr>-->
		<tr>
		<?php 
			$grand_cash_count = $row_room_service_cash['count'] + $row_room_cash['count'] + $row_restaurant_cash['count'] + $row_banquet_cash['count'];
			$grand_card_count = $row_room_service_card['count'] + $row_room_card['count'] + $row_restaurant_card['count'] + $row_banquet_card['count'];
			$grand_credit_count = $row_room_service_credit['count'] + $row_room_credit['count'] + $row_restaurant_credit['count'] + $row_banquet_credit['count'] ;
			$grand_paytm_count = $row_room_service_paytm['count'] + $row_room_paytm['count'] + $row_restaurant_paytm['count'] + $row_banquet_paytm['count'];
			$grand_cheque_count = $row_room_service_cheque['count'] + $row_room_cheque['count'] + $row_restaurant_cheque['count'] + $row_banquet_cheque['count'];
			$grand_bank_transfer_count = $row_room_service_bank_transfer['count'] + $row_room_bank_transfer['count'] + $row_restaurant_bank_transfer['count'] + $row_banquet_bank_transfer['count'];
			$grand_cash_amount = $row_room_service_cash['amount'] + $row_room_cash['amount'] + $row_restaurant_cash['amount'] + $row_banquet_cash['amount'];
			$grand_paytm_amount = $row_room_service_paytm['amount'] + $row_room_paytm['amount'] + $row_restaurant_paytm['amount'] + $row_banquet_paytm['amount'];
			$grand_bank_transfer_amount = $row_room_service_bank_transfer['amount'] + $row_room_bank_transfer['amount'] + $row_restaurant_bank_transfer['amount'] + $row_banquet_bank_transfer['amount'];
			$grand_cheque_amount = $row_room_service_cheque['amount'] + $row_room_cheque['amount'] + $row_restaurant_cheque['amount'] + $row_banquet_cheque['amount'];
			$grand_card_amount = $row_room_service_card['amount'] + $row_room_card['amount'] + $row_restaurant_card['count'] + $row_banquet_card['amount'];
			$grand_credit_amount = $row_room_service_credit['amount'] + $row_room_credit['amount'] + $row_restaurant_credit['amount'] + $row_banquet_credit['amount'];
			$grand_count = $room_count + $room_service_count + $restaurant_count + $banquet_count;
			$grand_amount = $room_amount + $room_service_amount + $restaurant_amount + $banquet_amount;
		?>
			<th style="color:black;font-size:18px;">Total :</th>
			<th style="color:black;font-size:18px;"><?php echo $grand_cash_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_cash_amount, 2, '.', ''); ?></th>
			<th style="color:black;font-size:18px;"><?php echo $grand_card_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_card_amount, 2, '.', ''); ?></th>
			<th style="color:black;font-size:18px;"><?php echo $grand_credit_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_credit_amount, 2, '.', ''); ?></th>
			<th style="color:black;font-size:18px;"><?php echo $grand_paytm_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_paytm_amount, 2, '.', ''); ?></th>
			<th style="color:black;font-size:18px;"><?php echo $grand_bank_transfer_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_bank_transfer_amount, 2, '.', ''); ?></th>
			<th style="color:black;font-size:18px;"><?php echo $grand_cheque_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_cheque_amount, 2, '.', ''); ?></th>
			<th style="color:black;font-size:18px;"><?php echo $grand_count; ?></th>
			<th style="color:black;font-size:18px;"><?php echo number_format((float)$grand_amount, 2, '.', ''); ?></th>
		</tr>
	</table>
	<table>
		<tr>
			<th colspan="19" style="font-size:25px;">Advance Summary</th>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;width: 8%;" rowspan="2">Mop\Type</th>
			<th style="color:black;font-size:18px;width: 20%;" colspan="2">Room Booking</th>
			<th style="color:black;font-size:18px;width: 20%;" colspan="2">Banquet Booking</th>
			<th style="color:black;font-size:18px;width: 20%;" colspan="2">Previous Advance Room/Banquet Booking(Plus Amount)</th>
			<th style="color:black;font-size:18px;width: 20%;" colspan="2">Room Booking(In House Guest)</th>
			<th style="color:black;font-size:18px;width: 12%;" colspan="2">Total</th>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
		</tr>
	<?php 
		$grand_total = 0;
		$grand_count = 0;
		$amount_cash = 0;
		$count_cash = 0;
		$amount_card = 0;
		$count_card = 0;
		$amount_bank_transfer = 0;
		$count_bank_transfer = 0;
		$amount_other = 0;
		$count_other = 0;
		$amount_cheque = 0;
		$count_cheque = 0;
		$amount_card_sbi = 0;
		$count_card_sbi = 0;
		$amount_card_pnb = 0;
		$count_card_pnb = 0;
		$amount_paytm = 0;
		$count_paytm = 0;
		$amount_room_rent = 0;
		$count_room_rent = 0;
		$amount_banquet_rent = 0;
		$count_banquet_rent = 0;
		$amount_advance_for = 0;
		$count_advance_for = 0;
		$amount_advance_for_checkin = 0;
		$count_advance_for_checkin = 0;
		echo '<tr><th>CASH</th>';
		$sql_advance_room_rent_cash = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="cash"';
		$result_advance_room_rent_cash = execute_query($sql_advance_room_rent_cash);
		$row_advance_room_rent_cash = mysqli_fetch_array($result_advance_room_rent_cash);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_cash['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_cash['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_cash['amount'];
		$grand_count += $row_advance_room_rent_cash['count'];
		$amount_cash += $row_advance_room_rent_cash['amount'];
		$count_cash += $row_advance_room_rent_cash['count'];
		$amount_room_rent += $row_advance_room_rent_cash['amount'];
		$count_room_rent += $row_advance_room_rent_cash['count'];


		$sql_advance_banquet_rent_cash = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="cash"';
		$result_advance_banquet_rent_cash = execute_query($sql_advance_banquet_rent_cash);
		$row_advance_banquet_rent_cash = mysqli_fetch_array($result_advance_banquet_rent_cash);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_cash['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_cash['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_cash['amount'];
		$grand_count += $row_advance_banquet_rent_cash['count'];
		$amount_cash += $row_advance_banquet_rent_cash['amount'];
		$count_cash += $row_advance_banquet_rent_cash['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_cash['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_cash['count'];


		$sql_advance_advance_for_cash = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="cash"';
		$result_advance_advance_for_cash = execute_query($sql_advance_advance_for_cash);
		$row_advance_advance_for_cash = mysqli_fetch_array($result_advance_advance_for_cash);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_cash['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_cash['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_cash['amount'];
		$grand_count += $row_advance_advance_for_cash['count'];
		$amount_cash += $row_advance_advance_for_cash['amount'];
		$count_cash += $row_advance_advance_for_cash['count'];
		$amount_advance_for += $row_advance_advance_for_cash['amount'];
		$count_advance_for += $row_advance_advance_for_cash['count'];


		$sql_advance_advance_for_check_in_cash = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="cash"';
		$result_advance_advance_for_check_in_cash = execute_query($sql_advance_advance_for_check_in_cash);
		$row_advance_advance_for_check_in_cash = mysqli_fetch_array($result_advance_advance_for_check_in_cash);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_cash['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_cash['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_cash['amount'];
		$grand_count += $row_advance_advance_for_check_in_cash['count'];
		$amount_cash += $row_advance_advance_for_check_in_cash['amount'];
		$count_cash += $row_advance_advance_for_check_in_cash['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_cash['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_cash['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_cash.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_cash.'</b></td></tr>';


		echo '<tr><th>CARD</th>';
		$sql_advance_room_rent_card = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="card"';
		$result_advance_room_rent_card = execute_query($sql_advance_room_rent_card);
		$row_advance_room_rent_card = mysqli_fetch_array($result_advance_room_rent_card);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_card['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_card['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_card['amount'];
		$grand_count += $row_advance_room_rent_card['count'];
		$amount_card += $row_advance_room_rent_card['amount'];
		$count_card += $row_advance_room_rent_card['count'];
		$amount_room_rent += $row_advance_room_rent_card['amount'];
		$count_room_rent += $row_advance_room_rent_card['count'];


		$sql_advance_banquet_rent_card = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="card"';
		$result_advance_banquet_rent_card = execute_query($sql_advance_banquet_rent_card);
		$row_advance_banquet_rent_card = mysqli_fetch_array($result_advance_banquet_rent_card);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_card['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_card['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_card['amount'];
		$grand_count += $row_advance_banquet_rent_card['count'];
		$amount_card += $row_advance_banquet_rent_card['amount'];
		$count_card += $row_advance_banquet_rent_card['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_card['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_card['count'];


		$sql_advance_advance_for_card = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="card"';
		$result_advance_advance_for_card = execute_query($sql_advance_advance_for_card);
		$row_advance_advance_for_card = mysqli_fetch_array($result_advance_advance_for_card);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_card['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_card['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_card['amount'];
		$grand_count += $row_advance_advance_for_card['count'];
		$amount_card += $row_advance_advance_for_card['amount'];
		$count_card += $row_advance_advance_for_card['count'];
		$amount_advance_for += $row_advance_advance_for_card['amount'];
		$count_advance_for += $row_advance_advance_for_card['count'];


		$sql_advance_advance_for_check_in_card = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="card"';
		$result_advance_advance_for_check_in_card = execute_query($sql_advance_advance_for_check_in_card);
		$row_advance_advance_for_check_in_card = mysqli_fetch_array($result_advance_advance_for_check_in_card);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_card['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_card['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_card['amount'];
		$grand_count += $row_advance_advance_for_check_in_card['count'];
		$amount_card += $row_advance_advance_for_check_in_card['amount'];
		$count_card += $row_advance_advance_for_check_in_card['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_card['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_card['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_card.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_card.'</b></td></tr>';


		echo '<tr><th>PAYTM</th>';
		$sql_advance_room_rent_paytm = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="paytm"';
		$result_advance_room_rent_paytm = execute_query($sql_advance_room_rent_paytm);
		$row_advance_room_rent_paytm = mysqli_fetch_array($result_advance_room_rent_paytm);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_paytm['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_paytm['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_paytm['amount'];
		$grand_count += $row_advance_room_rent_paytm['count'];
		$amount_paytm += $row_advance_room_rent_paytm['amount'];
		$count_paytm += $row_advance_room_rent_paytm['count'];
		$amount_room_rent += $row_advance_room_rent_paytm['amount'];
		$count_room_rent += $row_advance_room_rent_paytm['count'];


		$sql_advance_banquet_rent_paytm = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="paytm"';
		$result_advance_banquet_rent_paytm = execute_query($sql_advance_banquet_rent_paytm);
		$row_advance_banquet_rent_paytm = mysqli_fetch_array($result_advance_banquet_rent_paytm);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_paytm['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_paytm['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_paytm['amount'];
		$grand_count += $row_advance_banquet_rent_paytm['count'];
		$amount_paytm += $row_advance_banquet_rent_paytm['amount'];
		$count_paytm += $row_advance_banquet_rent_paytm['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_paytm['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_paytm['count'];


		$sql_advance_advance_for_paytm = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="paytm"';
		$result_advance_advance_for_paytm = execute_query($sql_advance_advance_for_paytm);
		$row_advance_advance_for_paytm = mysqli_fetch_array($result_advance_advance_for_paytm);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_paytm['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_paytm['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_paytm['amount'];
		$grand_count += $row_advance_advance_for_paytm['count'];
		$amount_paytm += $row_advance_advance_for_paytm['amount'];
		$count_paytm += $row_advance_advance_for_paytm['count'];
		$amount_advance_for += $row_advance_advance_for_paytm['amount'];
		$count_advance_for += $row_advance_advance_for_paytm['count'];


		$sql_advance_advance_for_check_in_paytm = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="paytm"';
		$result_advance_advance_for_check_in_paytm = execute_query($sql_advance_advance_for_check_in_paytm);
		$row_advance_advance_for_check_in_paytm = mysqli_fetch_array($result_advance_advance_for_check_in_paytm);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_paytm['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_paytm['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_paytm['amount'];
		$grand_count += $row_advance_advance_for_check_in_paytm['count'];
		$amount_paytm += $row_advance_advance_for_check_in_paytm['amount'];
		$count_paytm += $row_advance_advance_for_check_in_paytm['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_paytm['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_paytm['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_paytm.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_paytm.'</b></td></tr>';


		echo '<tr><th>BANK TRANSFER</th>';
		$sql_advance_room_rent_bank_transfer = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="bank_transfer"';
		$result_advance_room_rent_bank_transfer = execute_query($sql_advance_room_rent_bank_transfer);
		$row_advance_room_rent_bank_transfer = mysqli_fetch_array($result_advance_room_rent_bank_transfer);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_bank_transfer['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_bank_transfer['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_bank_transfer['amount'];
		$grand_count += $row_advance_room_rent_bank_transfer['count'];
		$amount_bank_transfer += $row_advance_room_rent_bank_transfer['amount'];
		$count_bank_transfer += $row_advance_room_rent_bank_transfer['count'];
		$amount_room_rent += $row_advance_room_rent_bank_transfer['amount'];
		$count_room_rent += $row_advance_room_rent_bank_transfer['count'];


		$sql_advance_banquet_rent_bank_transfer = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="bank_transfer"';
		$result_advance_banquet_rent_bank_transfer = execute_query($sql_advance_banquet_rent_bank_transfer);
		$row_advance_banquet_rent_bank_transfer = mysqli_fetch_array($result_advance_banquet_rent_bank_transfer);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_bank_transfer['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_bank_transfer['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_bank_transfer['amount'];
		$grand_count += $row_advance_banquet_rent_bank_transfer['count'];
		$amount_bank_transfer += $row_advance_banquet_rent_bank_transfer['amount'];
		$count_bank_transfer += $row_advance_banquet_rent_bank_transfer['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_bank_transfer['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_bank_transfer['count'];


		$sql_advance_advance_for_bank_transfer = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="bank_transfer"';
		$result_advance_advance_for_bank_transfer = execute_query($sql_advance_advance_for_bank_transfer);
		$row_advance_advance_for_bank_transfer = mysqli_fetch_array($result_advance_advance_for_bank_transfer);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_bank_transfer['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_bank_transfer['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_bank_transfer['amount'];
		$grand_count += $row_advance_advance_for_bank_transfer['count'];
		$amount_bank_transfer += $row_advance_advance_for_bank_transfer['amount'];
		$count_bank_transfer += $row_advance_advance_for_bank_transfer['count'];
		$amount_advance_for += $row_advance_advance_for_bank_transfer['amount'];
		$count_advance_for += $row_advance_advance_for_bank_transfer['count'];


		$sql_advance_advance_for_check_in_bank_transfer = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="bank_transfer"';
		$result_advance_advance_for_check_in_bank_transfer = execute_query($sql_advance_advance_for_check_in_bank_transfer);
		$row_advance_advance_for_check_in_bank_transfer = mysqli_fetch_array($result_advance_advance_for_check_in_bank_transfer);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_bank_transfer['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_bank_transfer['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_bank_transfer['amount'];
		$grand_count += $row_advance_advance_for_check_in_bank_transfer['count'];
		$amount_bank_transfer += $row_advance_advance_for_check_in_bank_transfer['amount'];
		$count_bank_transfer += $row_advance_advance_for_check_in_bank_transfer['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_bank_transfer['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_bank_transfer['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_bank_transfer.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_bank_transfer.'</b></td></tr>';

		echo '<tr><th>CHEQUE</th>';
		$sql_advance_room_rent_cheque = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="cheque"';
		$result_advance_room_rent_cheque = execute_query($sql_advance_room_rent_cheque);
		$row_advance_room_rent_cheque = mysqli_fetch_array($result_advance_room_rent_cheque);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_cheque['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_cheque['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_cheque['amount'];
		$grand_count += $row_advance_room_rent_cheque['count'];
		$amount_cheque += $row_advance_room_rent_cheque['amount'];
		$count_cheque += $row_advance_room_rent_cheque['count'];
		$amount_room_rent += $row_advance_room_rent_cheque['amount'];
		$count_room_rent += $row_advance_room_rent_cheque['count'];


		$sql_advance_banquet_rent_cheque = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="cheque"';
		$result_advance_banquet_rent_cheque = execute_query($sql_advance_banquet_rent_cheque);
		$row_advance_banquet_rent_cheque = mysqli_fetch_array($result_advance_banquet_rent_cheque);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_cheque['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_cheque['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_cheque['amount'];
		$grand_count += $row_advance_banquet_rent_cheque['count'];
		$amount_cheque += $row_advance_banquet_rent_cheque['amount'];
		$count_cheque += $row_advance_banquet_rent_cheque['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_cheque['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_cheque['count'];


		$sql_advance_advance_for_cheque = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="cheque"';
		$result_advance_advance_for_cheque = execute_query($sql_advance_advance_for_cheque);
		$row_advance_advance_for_cheque = mysqli_fetch_array($result_advance_advance_for_cheque);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_cheque['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_cheque['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_cheque['amount'];
		$grand_count += $row_advance_advance_for_cheque['count'];
		$amount_cheque += $row_advance_advance_for_cheque['amount'];
		$count_cheque += $row_advance_advance_for_cheque['count'];
		$amount_advance_for += $row_advance_advance_for_cheque['amount'];
		$count_advance_for += $row_advance_advance_for_cheque['count'];


		$sql_advance_advance_for_check_in_cheque = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="cheque"';
		$result_advance_advance_for_check_in_cheque = execute_query($sql_advance_advance_for_check_in_cheque);
		$row_advance_advance_for_check_in_cheque = mysqli_fetch_array($result_advance_advance_for_check_in_cheque);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_cheque['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_cheque['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_cheque['amount'];
		$grand_count += $row_advance_advance_for_check_in_cheque['count'];
		$amount_cheque += $row_advance_advance_for_check_in_cheque['amount'];
		$count_cheque += $row_advance_advance_for_check_in_cheque['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_cheque['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_cheque['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_cheque.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_cheque.'</b></td></tr>';


		/**echo '<tr><th>CARD SBI</th>';
		$sql_advance_room_rent_card_sbi = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="card_sbi"';
		$result_advance_room_rent_card_sbi = execute_query($sql_advance_room_rent_card_sbi);
		$row_advance_room_rent_card_sbi = mysqli_fetch_array($result_advance_room_rent_card_sbi);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_card_sbi['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_card_sbi['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_card_sbi['amount'];
		$grand_count += $row_advance_room_rent_card_sbi['count'];
		$amount_card_sbi += $row_advance_room_rent_card_sbi['amount'];
		$count_card_sbi += $row_advance_room_rent_card_sbi['count'];
		$amount_room_rent += $row_advance_room_rent_card_sbi['amount'];
		$count_room_rent += $row_advance_room_rent_card_sbi['count'];


		$sql_advance_banquet_rent_card_sbi = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="card_sbi"';
		$result_advance_banquet_rent_card_sbi = execute_query($sql_advance_banquet_rent_card_sbi);
		$row_advance_banquet_rent_card_sbi = mysqli_fetch_array($result_advance_banquet_rent_card_sbi);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_card_sbi['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_card_sbi['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_card_sbi['amount'];
		$grand_count += $row_advance_banquet_rent_card_sbi['count'];
		$amount_card_sbi += $row_advance_banquet_rent_card_sbi['amount'];
		$count_card_sbi += $row_advance_banquet_rent_card_sbi['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_card_sbi['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_card_sbi['count'];


		$sql_advance_advance_for_card_sbi = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="card_sbi"';
		$result_advance_advance_for_card_sbi = execute_query($sql_advance_advance_for_card_sbi);
		$row_advance_advance_for_card_sbi = mysqli_fetch_array($result_advance_advance_for_card_sbi);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_card_sbi['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_card_sbi['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_card_sbi['amount'];
		$grand_count += $row_advance_advance_for_card_sbi['count'];
		$amount_card_sbi += $row_advance_advance_for_card_sbi['amount'];
		$count_card_sbi += $row_advance_advance_for_card_sbi['count'];
		$amount_advance_for += $row_advance_advance_for_card_sbi['amount'];
		$count_advance_for += $row_advance_advance_for_card_sbi['count'];


		$sql_advance_advance_for_check_in_card_sbi = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="card_sbi"';
		$result_advance_advance_for_check_in_card_sbi = execute_query($sql_advance_advance_for_check_in_card_sbi);
		$row_advance_advance_for_check_in_card_sbi = mysqli_fetch_array($result_advance_advance_for_check_in_card_sbi);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_card_sbi['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_card_sbi['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_card_sbi['amount'];
		$grand_count += $row_advance_advance_for_check_in_card_sbi['count'];
		$amount_card_sbi += $row_advance_advance_for_check_in_card_sbi['amount'];
		$count_card_sbi += $row_advance_advance_for_check_in_card_sbi['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_card_sbi['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_card_sbi['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_card_sbi.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_card_sbi.'</b></td></tr>';



		echo '<tr><th>CARD PNB</th>';
		$sql_advance_room_rent_card_pnb = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="card_pnb"';
		$result_advance_room_rent_card_pnb = execute_query($sql_advance_room_rent_card_pnb);
		$row_advance_room_rent_card_pnb = mysqli_fetch_array($result_advance_room_rent_card_pnb);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_card_pnb['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_card_pnb['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_card_pnb['amount'];
		$grand_count += $row_advance_room_rent_card_pnb['count'];
		$amount_card_pnb += $row_advance_room_rent_card_pnb['amount'];
		$count_card_pnb += $row_advance_room_rent_card_pnb['count'];
		$amount_room_rent += $row_advance_room_rent_card_pnb['amount'];
		$count_room_rent += $row_advance_room_rent_card_pnb['count'];


		$sql_advance_banquet_rent_card_pnb = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="card_pnb"';
		$result_advance_banquet_rent_card_pnb = execute_query($sql_advance_banquet_rent_card_pnb);
		$row_advance_banquet_rent_card_pnb = mysqli_fetch_array($result_advance_banquet_rent_card_pnb);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_card_pnb['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_card_pnb['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_card_pnb['amount'];
		$grand_count += $row_advance_banquet_rent_card_pnb['count'];
		$amount_card_pnb += $row_advance_banquet_rent_card_pnb['amount'];
		$count_card_pnb += $row_advance_banquet_rent_card_pnb['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_card_pnb['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_card_pnb['count'];


		$sql_advance_advance_for_card_pnb = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="card_pnb"';
		$result_advance_advance_for_card_pnb = execute_query($sql_advance_advance_for_card_pnb);
		$row_advance_advance_for_card_pnb = mysqli_fetch_array($result_advance_advance_for_card_pnb);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_card_pnb['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_card_pnb['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_card_pnb['amount'];
		$grand_count += $row_advance_advance_for_card_pnb['count'];
		$amount_card_pnb += $row_advance_advance_for_card_pnb['amount'];
		$count_card_pnb += $row_advance_advance_for_card_pnb['count'];
		$amount_advance_for += $row_advance_advance_for_card_pnb['amount'];
		$count_advance_for += $row_advance_advance_for_card_pnb['count'];


		$sql_advance_advance_for_check_in_card_pnb = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="card_pnb"';
		$result_advance_advance_for_check_in_card_pnb = execute_query($sql_advance_advance_for_check_in_card_pnb);
		$row_advance_advance_for_check_in_card_pnb = mysqli_fetch_array($result_advance_advance_for_check_in_card_pnb);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_card_pnb['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_card_pnb['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_card_pnb['amount'];
		$grand_count += $row_advance_advance_for_check_in_card_pnb['count'];
		$amount_card_pnb += $row_advance_advance_for_check_in_card_pnb['amount'];
		$count_card_pnb += $row_advance_advance_for_check_in_card_pnb['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_card_pnb['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_card_pnb['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_card_pnb.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_card_pnb.'</b></td></tr>';**/


		echo '<tr><th>OTHER</th>';
		$sql_advance_room_rent_other = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="room_rent" AND `customer_transactions`.`mop`="other"';
		$result_advance_room_rent_other = execute_query($sql_advance_room_rent_other);
		$row_advance_room_rent_other = mysqli_fetch_array($result_advance_room_rent_other);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_other['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_room_rent_other['amount'].'</b></td>';
		$grand_total += $row_advance_room_rent_other['amount'];
		$grand_count += $row_advance_room_rent_other['count'];
		$amount_other += $row_advance_room_rent_other['amount'];
		$count_other += $row_advance_room_rent_other['count'];
		$amount_room_rent += $row_advance_room_rent_other['amount'];
		$count_room_rent += $row_advance_room_rent_other['count'];


		$sql_advance_banquet_rent_other = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="banquet_rent" AND `customer_transactions`.`mop`="other"';
		$result_advance_banquet_rent_other = execute_query($sql_advance_banquet_rent_other);
		$row_advance_banquet_rent_other = mysqli_fetch_array($result_advance_banquet_rent_other);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_other['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_rent_other['amount'].'</b></td>';
		$grand_total += $row_advance_banquet_rent_other['amount'];
		$grand_count += $row_advance_banquet_rent_other['count'];
		$amount_other += $row_advance_banquet_rent_other['amount'];
		$count_other += $row_advance_banquet_rent_other['count'];
		$amount_banquet_rent += $row_advance_banquet_rent_other['amount'];
		$count_banquet_rent += $row_advance_banquet_rent_other['count'];


		$sql_advance_advance_for_other = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for" AND `customer_transactions`.`mop`="other"';
		$result_advance_advance_for_other = execute_query($sql_advance_advance_for_other);
		$row_advance_advance_for_other = mysqli_fetch_array($result_advance_advance_for_other);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_other['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_other['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_other['amount'];
		$grand_count += $row_advance_advance_for_other['count'];
		$amount_other += $row_advance_advance_for_other['amount'];
		$count_other += $row_advance_advance_for_other['count'];
		$amount_advance_for += $row_advance_advance_for_other['amount'];
		$count_advance_for += $row_advance_advance_for_other['count'];


		$sql_advance_advance_for_check_in_other = 'SELECT SUM(advance_amount) AS amount, COUNT(*) AS count FROM `advance_booking` LEFT JOIN `customer_transactions` ON `advance_booking`.`sno`=`customer_transactions`.`advance_booking_id` WHERE `advance_booking`.`created_on`>="'.$_POST['allot_from'].'" AND `advance_booking`.`created_on`<"'.$_POST['allot_to_re'].'" AND `advance_booking`.`purpose`="advance_for_checkin" AND `customer_transactions`.`mop`="other"';
		$result_advance_advance_for_check_in_other = execute_query($sql_advance_advance_for_check_in_other);
		$row_advance_advance_for_check_in_other = mysqli_fetch_array($result_advance_advance_for_check_in_other);
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_other['count'].'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$row_advance_advance_for_check_in_other['amount'].'</b></td>';
		$grand_total += $row_advance_advance_for_check_in_other['amount'];
		$grand_count += $row_advance_advance_for_check_in_other['count'];
		$amount_other += $row_advance_advance_for_check_in_other['amount'];
		$count_other += $row_advance_advance_for_check_in_other['count'];
		$amount_advance_for_checkin += $row_advance_advance_for_check_in_other['amount'];
		$count_advance_for_checkin += $row_advance_advance_for_check_in_other['count'];


		echo '<td style="color:black;font-size:18px;"><b>'.$count_other.'</b></td>';
		echo '<td style="color:black;font-size:18px;"><b>'.$amount_other.'</b></td></tr>';

		echo '<tr><th style="color:black;font-size:20px;"><b>GRAND TOTAL</b></th><th style="color:black;font-size:18px;"><b>'.$count_room_rent.'</b></th><th style="color:black;font-size:18px;"><b>'.$amount_room_rent.'</b></th><th style="color:black;font-size:18px;"><b>'.$count_banquet_rent.'</b></th><th style="color:black;font-size:18px;"><b>'.$amount_banquet_rent.'</b></th><th style="color:black;font-size:18px;"><b>'.$count_advance_for.'</b></th><th style="color:black;font-size:18px;"><b>'.$amount_advance_for.'</b></th><th style="color:black;font-size:18px;"><b>'.$count_advance_for_checkin.'</b></th><th style="color:black;font-size:18px;"><b>'.$amount_advance_for_checkin.'</b></th><th style="color:black;font-size:18px;"><b>'.$grand_count.'</b></th><th style="color:black;font-size:18px;"><b>'.$grand_total.'</b></th></tr>';
	?>
	</table>
	<table>
		<tr>
			<th colspan="4" style="font-size:18px;">Advance Settelment Summary</th>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">Type</th>
			<th style="color:black;font-size:18px;">Amount</th>
		</tr>
	<?php 
		$sql_advance_room_settelment = 'SELECT SUM(`amount`) AS amount FROM `customer_transactions` WHERE `type`="ADVANCE_PAID" AND `payment_for`="room_rent" AND `timestamp`>="'.$_POST['allot_from'].'" and `timestamp`<"'.$_POST['allot_to_re'].'"';
		$row_advance_room_settelment = mysqli_fetch_array(execute_query($sql_advance_room_settelment));
		$sql_advance_banquet_settelment = 'SELECT SUM(`amount`) AS amount FROM `customer_transactions` WHERE `type`="ADVANCE_PAID" AND `payment_for`="banquet_rent" AND `timestamp`>="'.$_POST['allot_from'].'" and `timestamp`<"'.$_POST['allot_to_re'].'"';
		$row_advance_banquet_settelment = mysqli_fetch_array(execute_query($sql_advance_banquet_settelment));
		$i = 1;
		$grand_total = $row_advance_room_settelment['amount']+$row_advance_banquet_settelment['amount'];
			echo '<tr><th style="color:black;font-size:18px;"><b>Room Settelment</b></th><td style="color:black;font-size:18px;"><b>'.$row_advance_room_settelment['amount'].'</b></td></tr><tr><th style="color:black;font-size:18px;"><b>Banquet Settelment</b></th><td style="color:black;font-size:18px;"><b>'.$row_advance_banquet_settelment['amount'].'</b></td></tr>';
	?>
		<tr><th colspan="1" style="color:black;font-size:18px;">Total</th><th style="color:black;font-size:18px;"><?php echo $grand_total; ?></th></tr>
	</table>
	<table>
		<tr>
			<th colspan="4" style="font-size:18px;">Credit Bill Settelment Summary</th>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">S.No.</th>
			<th style="color:black;font-size:18px;">Mode Of Payment</th>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
		</tr>
	<?php 
		$sql_credit_bill_settelment = 'SELECT COUNT(*) AS count , SUM(`amount`) AS amount , `mop` FROM `customer_transactions` WHERE `type`="receipt" AND `amount`!="0" AND `timestamp`>="'.$_POST['allot_from'].'" and `timestamp`<"'.$_POST['allot_to_re'].'" GROUP BY `mop`';
		$result_credit_bill_settelment = execute_query($sql_credit_bill_settelment);
		$i = 1;
		$grand_total = 0;
		while ($row_credit_bill_settelment = mysqli_fetch_array($result_credit_bill_settelment)) {
			if($row_credit_bill_settelment['mop'] == "bank_transfer"){
				$mop_name = 'BANK TRANSFER';
			}
			elseif($row_credit_bill_settelment['mop'] == "card_sbi"){
				$mop_name = 'CARD S.B.I.';
			}
			elseif($row_credit_bill_settelment['mop'] == "card_pnb"){
				$mop_name = 'CARD P.N.B.';
			}
			else{
				$mop_name = strtoupper($row_credit_bill_settelment['mop']);
			}
			echo '<tr><th style="color:black;font-size:18px;"><b>'.$i++.'</b></th><td style="color:black;font-size:18px;"><b>'.$mop_name.'</b></td><td style="color:black;font-size:18px;"><b>'.$row_credit_bill_settelment['count'].'</b></td><td style="color:black;font-size:18px;"><b>'.$row_credit_bill_settelment['amount'].'</b></td></tr>';
			$grand_total += $row_credit_bill_settelment['amount'];
		}
	?>
		<tr><th colspan="3" style="color:black;font-size:18px;">Mode Of Payment</th><th style="color:black;font-size:18px;"><?php echo $grand_total; ?></th></tr>
	</table>

	<!--<table>
		<tr>
			<th colspan="4" style="font-size:18px;">Advance Summary</th>
		</tr>
		<tr>
			<th style="color:black;font-size:18px;">S.No.</th>
			<th style="color:black;font-size:18px;">Mode Of Payment</th>
			<th style="color:black;font-size:18px;">Count</th>
			<th style="color:black;font-size:18px;">Amount</th>
		</tr>
	<?php 
		$sql_credit_bill_settelment = 'SELECT COUNT(*) AS count , SUM(`amount`) AS amount , `mop` FROM `customer_transactions` WHERE `type`="ADVANCE_AMT" AND `amount`!="0" AND `timestamp`>="'.$_POST['allot_from'].'" and `timestamp`<"'.$_POST['allot_to_re'].'" GROUP BY `mop`';
		$result_credit_bill_settelment = execute_query($sql_credit_bill_settelment);
		$i = 1;
		$grand_total = 0;
		while ($row_credit_bill_settelment = mysqli_fetch_array($result_credit_bill_settelment)) {
			if($row_credit_bill_settelment['mop'] == "bank_transfer"){
				$mop_name = 'BANK TRANSFER';
			}
			elseif($row_credit_bill_settelment['mop'] == "card_sbi"){
				$mop_name = 'CARD S.B.I.';
			}
			elseif($row_credit_bill_settelment['mop'] == "card_pnb"){
				$mop_name = 'CARD P.N.B.';
			}
			else{
				$mop_name = strtoupper($row_credit_bill_settelment['mop']);
			}
			echo '<tr><th style="color:black;font-size:18px;"><b>'.$i++.'</b></th><td style="color:black;font-size:18px;"><b>'.$mop_name.'</b></td><td style="color:black;font-size:18px;"><b>'.$row_credit_bill_settelment['count'].'</b></td><td style="color:black;font-size:18px;"><b>'.$row_credit_bill_settelment['amount'].'</b></td></tr>';
			$grand_total += $row_credit_bill_settelment['amount'];
		}
	?>
		<tr><th colspan="3" style="color:black;font-size:18px;">Mode Of Payment</th><th style="color:black;font-size:18px;"><?php echo $grand_total; ?></th></tr>
	</table>-->
</div>

<?php

page_footer();
?>