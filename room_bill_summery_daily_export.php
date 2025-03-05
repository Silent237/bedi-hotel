<?php
session_start();
include("scripts/settings.php");
error_reporting(E_ALL);
if(isset($_SESSION['sql5'])){
	 $sql=$_SESSION['sql5'];

}
if(isset($_SESSION['sql6'])){
	 $sql_inv=$_SESSION['sql6'];

}

if(isset($_SESSION['sql_credit'])){
	 $sql_credit=$_SESSION['sql_credit'];

}
if(isset($_SESSION['sql_paytm'])){
	 $sql_paytm=$_SESSION['sql_paytm'];

}
if(isset($_SESSION['sql_cheque'])){
	 $sql_cheque=$_SESSION['sql_cheque'];

}
if(isset($_SESSION['sql_bank_transfer'])){
	 $sql_bank_transfer=$_SESSION['sql_bank_transfer'];

}
if(isset($_SESSION['sql_card'])){
	 $sql_card=$_SESSION['sql_card'];

}
if(isset($_SESSION['sql_cash'])){
	 $sql_cash=$_SESSION['sql_cash'];

}
if(isset($_SESSION['sql_all'])){
	 $sql_all=$_SESSION['sql_all'];

}
if(isset($_SESSION['sql_r'])){
	 $sql_r=$_SESSION['sql_r'];

}
if(isset($_SESSION['sql_ro'])){
	 $sql_ro=$_SESSION['sql_ro'];

}
if(isset($_SESSION['sql_room_type'])){
	 $sql_room_type=$_SESSION['sql_room_type'];

}
if(isset($_SESSION['sql_receipt_type'])){
	 $sql_receipt_type=$_SESSION['sql_receipt_type'];

}

if(isset($_SESSION['sql_advance'])){
	 $sql_advance=$_SESSION['sql_advance'];

}



				$rowspan='rowspan="2"';
				$colspan='colspan="9"';
       $html ='<table>
        	<thead>
            	
            <tr style="background:#333; color:#FFF; text-align:center; font-size:13px;">
				<th '.$rowspan.'>S.No.</th>
				<th '.$rowspan.'>Room No.</th>
				<th '.$rowspan.'>Invoice No.</th>
				<th '.$colspan.'>Total</th>
				<th '.$rowspan.'>Mode Of Payment</th>
				<th '.$rowspan.'>Company/Guest Name</th>
				<th '.$rowspan.'>Paid Info</th>
				
			</tr>
			
			<tr style="background:#333; color:#FFF; text-align:center; font-size:13px;">
				<th>Amount</th>
				<th>Extra Bed</th>
				<th> Disc</th>				
				<th>Net Amount</th>
				<th>CGST</th>
				<th>SGST</th>				
				<th>Invoice Amount</th>
				<th>Advance Amount</th>
				<th>Amount</th>
			</tr>
					
					
           	   
            </thead>'; 
              
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
			
			$row_old='';
		
				$colspan=12;
			$totdisc=0;
			$total_credit_paid = 0;
			
			$inv_min_max = mysqli_fetch_array(execute_query($sql_inv));
				$html .='<tr style="border:1px solid black">
							<td colspan="'.($colspan-1).'">Invoice Numbers from : '.$inv_min_max['inv_start'].'-'.$inv_min_max['inv_end'].'</td>
						</tr>';
				$result=execute_query($sql);
				while($row = mysqli_fetch_array($result)){
						if($row_old==''){
							$row_old=$row['invoice_type'];
						}
						$days = get_days($row['allotment_date'], $row['exit_date']);
						
						if($row['other_charges']==''){
							$other_charge = 0;
						}
						if($row['other_charges']!=''){
							$other_charge = $row['other_charges'];
						}
						
						if(strtolower(trim($row['invoice_type']))!='bill_of_supply'){
							$base_rent = round(($row['original_room_rent']-($row['other_discount']+$row['discount_value'])+$other_charge),2);
							$tax = round((($base_rent*$row['tax_rate']/200)),2);
						}
						else{
							$base_rent=$row['room_rent']+$other_charge;
							$tax=0;
						}
						$row['other_discount']+=$row['discount_value'];
						$amount = $row['room_rent']*$days;
						if($row['cancel_date']==''){
							if($row['invoice_type']=='tax'){
								$tot_tax_discount+=$row['other_discount']*$days;
								$tot_tax_other_charges+=$row['other_charges']*$days;
								$tot_tax_original_amount+=$row['original_room_rent']*$days;
								$tot_tax_amount+=$amount;
								$tot_tax_tax += $tax*$days;
								$tot_tax_taxable+=$base_rent*$days;
								$count_tax++;

							}
							else{
								$tot_discount+=$row['other_discount']*$days;
								$tot_other_charges+=$row['other_charges']*$days;
								$tot_original_amount+=$row['original_room_rent']*$days;
								$tot_amount+=$amount;
								$tot_tax += $tax*$days;
								$tot_taxable+=$base_rent*$days;
								$count++;
							}
							$row_col = '';
							$cancel = '';
							$cancel_display = 'Cancel';
							
							$grand_tot_discount+=$row['other_discount']*$days;
							$grand_tot_other_charges+=$row['other_charges']*$days;
							$grand_tot_original_amount+=$row['original_room_rent']*$days;
							$grand_tot_amount+=$amount;
							$grand_tot_tax += $tax*$days;
							$grand_tot_taxable+=$base_rent*$days;
						}
						elseif($row['cancel_date'] !=''){
							$cancel_tot_taxable+=$base_rent*$days;
							$cancel_tot_amount+=$amount;
							$cancel_original_amount+=$row['original_room_rent']*$days;
							$cancel_tot_tax+=$tax*$days;
							$cancel_tot_discount+=$row['other_discount']*$days;
							$cancel_tot_other_charges+=$row['other_charges']*$days;
							$cancel_count++;

							$row_col = 'style="background:#FF0"';
							$cancel = '<br />Cancelled On : '.$row['cancel_date'];
							$cancel_display = 'Uncancel';
						}
						if($row['id_2']!=''){
							$gstin = '<br/>GSTIN : <b>'.$row['id_2'].'</b>';
						}
						else{
							$gstin='';
						}
						if($i%2==0){
							$col = '#CCC';
						}
						else{
							$col = '#EEE';
						}
						if($i%10==0){
							$css = 'page-break-after:always;';
						}
						else{
							$css = '';
						}
						
						$html .='<tr '.$row_col.' style="background:'.$col.';border:1px solid black">
						<td>'.$i++.'</td>';
						$html .='<td><b>'.$row['room_name'].'</b></td>';
						/**echo '<td><b>'.$row['room_name'].'</b><br><b>Company Name:</b>'.$row['company_name'].'<br /><b>Guest ame:</b>'.$row['guest_name'].'<br />In : '.$row['allotment_date'].'<br />Out :'.$row['exit_date'].'<br />Days : '.$days.$cancel.$gstin.'</td>';**/
						$html .='<td>'.$row['invoice_no'].'</td>';
						/**echo '<td class="number">'.number_format((float)$row['original_room_rent'], 2, '.', '').'</td>
						<td class="number">';
						
						$pos = strpos($row['other_charges'], "%");
						$other_new = str_replace("%", "", $row['other_charges']);
						if($pos===false){
							echo number_format((float)$row['other_charges'], 2, '.', '');
						}
						else{
							echo number_format((float)$row['other_charges'], 2, '.', '').' ('.$row['other_charges'].')';
						}
							
						echo '</td>
						<td class="number">';
						
						$pos = strpos($row['other_discount'], "%");
						$disc_new = str_replace("%", "", $row['other_discount']);
						if($pos===false){
							echo number_format((float)$row['other_discount'], 2, '.', '');
						}
						else{
							echo number_format((float)$row['other_discount'], 2, '.', '').' ('.$row['discount'].')';
						}
							
						echo '</td>
						<td class="number">'.$base_rent.'</td>';
							echo '
								<td class="number">'.number_format((float)($tax), 2, '.', '').'</td>
								<td class="number">'.number_format((float)($tax), 2, '.', '').'</td>
								<td class="number">'.number_format((float)($row['room_rent']), 2, '.', '').'</td>';**/
							$sql_mop = 'SELECT * FROM `customer_transactions` WHERE `allotment_id`="'.$row['sno'].'" AND `type`="RENT"';
							$row_mop = mysqli_fetch_array(execute_query($sql_mop));
								$html .='<td class="number">'.number_format((float)($row['original_room_rent']*$days), 2, '.', '').'</td>
								<td class="number">'.number_format((float)($row['other_charges']*$days), 2, '.', '').'</td>
								<td class="number">'.number_format((float)(($row['other_discount'])*$days), 2, '.', '').'</td>';
								$html .='<td class="number">'.number_format((float)($base_rent*$days), 2, '.', '').'</td>
								<td class="number">'.number_format((float)($tax*$days), 2, '.', '').'</td>
								<td class="number">'.number_format((float)($tax*$days), 2, '.', '').'</td>';
								
								$totdisc+=$row['other_discount']*$days;
								$pay_amount = $amount - $row_mop['advance_set_amt'];
								$grand_pay_amount += $pay_amount; 
								$grand_advance_amount += $row_mop['advance_set_amt'];
						$html .='
							<td class="number">'.number_format((float)$amount, 2, '.', '').'</td><td>'.$row_mop['advance_set_amt'].'</td><td>'.$pay_amount.'</td>';
							//$sql_mop = 'SELECT * FROM `customer_transactions` WHERE `allotment_id`="'.$row['sno'].'"';
							//$row_mop = mysqli_fetch_array(execute_query($sql_mop));
							$html .='
							<td class="editable" id="row_'.$row_mop['sno'].'"> <span style="text-transform:uppercase">';
							if ($row_mop['mop'] == 'bank_transfer') {
								$html .='BANK TRANSFER';
							}
							else{
								$html .=$row_mop['mop'];
							}
							$html .='</span></td>';
							if ($row_mop['mop_edited_by'] != '' AND ($row_mop['mop']=='credit' OR $row_mop['mop']=='CREDIT')) {
								$sql_customer = 'SELECT * FROM `customer` WHERE `sno`="'.$row_mop['cust_id'].'"';
								$row_customer = mysqli_fetch_array(execute_query($sql_customer));
								if ($row_customer['company_name'] != '') {
									$html .='<td>'.$row_customer['company_name'].'</td>';
								}
								else{
									$html .='<td>'.$row_customer['cust_name'].'</td>';
								}
							}
							else{
								$html .='<td>&nbsp;</td>';
							}
							/**echo '<td class="no-print"><a href="print.php?id='.$row['sno'].'" target="_blank">View</a></td>
							<td class="no-print"><a href="report_allotment_daily.php?cancel='.$row['sno'].'">'.$cancel_display.'</a></td>
							<td class="no-print"><a href="allotment2.php?id='.$row['sno'].'" target="_blank">Edit</a></td>
							<td class="no-print"><a href="allotment.php?aid='.$row['sno'].'" target="_blank">Bill Edit</a></td>';**/
							if($row_mop['mop'] == 'credit' OR $row_mop['mop'] == 'CREDIT'){
								$sql_credit_check = 'SELECT * FROM `customer_transactions` WHERE `sno`="'.$row_mop['credit_bill_paid_sno'].'"';
								$row_credit_check = mysqli_fetch_array(execute_query($sql_credit_check));
								$paid_amount = $row_mop['credit_set_amt'];
								$paid = $row_mop['advance_set_amt'] + $row_mop['credit_set_amt'];
								if($paid == 0 OR $paid == ''){
									$show = 'UN-PAID';
									$paid_amount = 0;
								}
								else if($paid == $row_mop['amount']){
									$show = 'PAID('.$row_credit_check['timestamp'].')<br/>Amount : '.$paid_amount.'<br/>(';
									if($row_mop['credit_set_amt'] > 0){
										$show .= 'Credit';
									}
									$show .= ')';
								}
								else if($paid < $row_mop['amount']){
									$show = 'SEMI-PAID('.$row_credit_check['timestamp'].')<br/>Amount : '.$paid_amount.'(';
									if($row_mop['credit_set_amt'] > 0){
										$show .= 'Credit';
									}
									$show .= ')';
								}
								$total_credit_paid += $paid_amount;
							}
							else{
								$show = '';
							}
							$html .='<td>'.$show.'</td></tr>';
						}
					$html .='<tr><td colspan="'.($colspan-11).'">&nbsp;</td>
					<td colspan="2" style="text-align:right;">Grand Total : </td>
					<td class="number"><b>'.number_format((float)$grand_tot_original_amount, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_tot_other_charges, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$totdisc, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_tot_taxable, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_tot_tax, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_tot_tax, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_tot_amount, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_advance_amount, 2, '.', '').'</b></td>
					<td class="number"><b>'.number_format((float)$grand_pay_amount, 2, '.', '').'</b></td>
					<td colspan="2">&nbsp;</td>';
					/**echo '<td class="no-print">&nbsp;</td><td class="no-print">&nbsp;</td><td class="no-print">&nbsp;</td>';**/
					$html .='<td>'.$total_credit_paid.'</td></tr>';
							
							$html .='</table>';
				
				 
					$cash = mysqli_fetch_array(execute_query($sql_cash));
					$paytm = mysqli_fetch_array(execute_query($sql_paytm));
					$cheque = mysqli_fetch_array(execute_query($sql_cheque));
					$bank_transfer = mysqli_fetch_array(execute_query($sql_bank_transfer));
					$credit = mysqli_fetch_array(execute_query($sql_credit));
					$card = mysqli_fetch_array(execute_query($sql_card));
					$all = mysqli_fetch_array(execute_query($sql_all));
					$r_room_card= mysqli_fetch_array(execute_query($sql_r));
					$r_room_cash= mysqli_fetch_array(execute_query($sql_ro));
					//echo $all['amount'];
					//echo " ".$cash['amount'];
					//echo $r_room_card['c'];
					//echo $r_room_cash['c'];
					$cash1=$cash['amount'];
					$paytm1=$paytm['amount'];
					$cheque1=$cheque['amount'];
					$bank_transfer1=$bank_transfer['amount'];
					$cr=$credit['amount']-$r_room_card['c']-$r_room_cash['c'];
					$cr_vik = $credit['amount'];
					$card1=$card['amount'];
					$all1=$all['amount']-$r_room_card['c']-$r_room_cash['c'];
					$all_vik = $all['amount'];
					//  echo "  ".$all1;
					$allno=$all['c'];
					$cashno=$cash['c'];
					$paytmno=$paytm['c'];
					$chequeno=$cheque['c'];
					$bank_transferno=$bank_transfer['c'];
					$cardno=$card['c'];
					$crno=$credit['c'];
					
				$html .='<table >
							<tr>
				<th colspan="12" style="background:#333; color:#FFF; text-align:center; font-size:13px; border:1px solid black">Receipts Summary</th>
			</tr>
			<tr style="background:#CCC; border:1px solid black;">
				<td class="number" style="color:black;font-size:18px;"><b>Total Receipts</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$all_vik, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$allno.' Nos.</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>Cash Receipts</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$cash1, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$cashno.'Nos.</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>Card Receipt</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$card1, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$cardno.' Nos.</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>Credit Receipt</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$cr_vik, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$crno.' Nos.</b></td>
			</tr>
			<tr style="background:#CCC; border:1px solid black;">
				<td class="number" style="color:black;font-size:18px;"><b>Paytm</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$paytm1, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$paytmno.' Nos.</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>Bank Transfer</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$bank_transfer1, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$bank_transferno.' Nos.</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>Cheque</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$cheque1, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;"><b>'.$chequeno.' Nos.</b></td>
				<td style="color:black;font-size:18px;"><b>Advance Amount</b></td>
				<td class="number" style="color:black;font-size:18px;"><b>'.number_format((float)$grand_advance_amount, 2, '.', '').'</b></td>
				<td style="color:black;font-size:18px;">&nbsp;</td>
			</tr>
						</table>';
						$row_room_type = mysqli_fetch_array(execute_query($sql_room_type));
						$row_receipt_type = mysqli_fetch_array(execute_query($sql_receipt_type));
						$type_count = $row_room_type['COUNT']+$row_receipt_type['COUNT'];
						$type_amount = $row_room_type['SUM']+$row_receipt_type['SUM'];

						
						
						$html .='
						<br />
						<table>
						<tr><th colspan="10" style="background:#333; color:#FFF; text-align:center; font-size:13px; border:1px solid black">Invoice Summary</th></tr>
						<tr style="background:#333; color:#FFF; text-align:center; font-size:13px; border:1px solid black">
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
						<tr style="background:#CCC; border:1px solid black;">
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
						<tr style="background:#CCC; border:1px solid black;">
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
						</table>';
						$result_advance = execute_query($sql_advance);
						
						$html .='
						<table>
							<tr style="background:#333; color:#FFF; text-align:center; font-size:13px; border:1px solid black"><th colspan="4">Advance Summary</th></tr><tr style="background:#333; color:#FFF; text-align:center; font-size:13px; border:1px solid black"><th>S.No.</th><th>Advance Amount</th><th>Date Of Booking</th><th>Type</th></tr>';
							$sno = 1;
							$grand_advance = 0;
							while($row_advance = mysqli_fetch_array($result_advance)){
								$grand_advance += $row_advance['amount'];
								$html .='<tr style="background:#CCC; border:1px solid black;"><th>'.$sno++.'</th><td>'.$row_advance['amount'].'</td><td>'.date('d-m-Y h:i:s' , strtotime($row_advance['created_on'])).'</td>';
								if($row_advance['payment_for'] == 'banquet_rent'){
									$html .='<td>Banquet</td>';
								}
								elseif($row_advance['payment_for'] == 'room_rent'){
									$html .='<td>Room</td>';
								}
								$html .='</tr>';
							}
							$html .='<tr style="background:#CCC; border:1px solid black;"><th>&nbsp;</th><th>Total : '.$grand_advance.'</th><th colspan="2">&nbsp;</th></tr>
						</table>
						';
				
				
				 header("Content-Type:application/xls");
                header("Content-Disposition:attachment;filename=download.xls");
                echo $html; ?>
				
				
