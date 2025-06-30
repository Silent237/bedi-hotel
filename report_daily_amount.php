<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
page_header();
navigation('');
page_footer();

?>
 <div id="container">
	<h2>Daily Amount Summary</h2>
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
            	<th>
                	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                </th>
                <th >
                	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                </th>
				<th colspan="2">
                <input type="submit" name="download_excel" value="Download in Excel" class="btTxt submit btn-danger" onclick="return submitExportExcel(event);">
            </th>
            </tr>
        </table>
	</form>
	<table>
		<tr>
			<th>Type</th>
			<th>Cash Amount</th>
			<th>Credit Amount</th>
		</tr>
		<?php 
		$cash = 0;
		$credit = 0;
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
		if($_POST['allot_from']<$_POST['allot_to_re']){	
			$sql_type_cash_rent = 'SELECT count(*) AS count , SUM(`amount`) AS amount , SUM(`credit_set_amt`) AS credit_set_amt , SUM(`advance_set_amt`) AS advance_set_amt  FROM `customer_transactions` WHERE `created_on` >="'.$_POST['allot_from'].'" AND `created_on` <"'.$_POST['allot_to_re'].'" AND `type`="RENT" AND `mop`!="credit"';
			//echo $sql_type_cash_rent;
			$row_cash_rent = mysqli_fetch_array(execute_query($sql_type_cash_rent));
			$sql_type_credit_rent = 'SELECT count(*) AS count , SUM(`amount`) AS amount , SUM(`credit_set_amt`) AS credit_set_amt , SUM(`advance_set_amt`) AS advance_set_amt FROM `customer_transactions` WHERE `created_on` >="'.$_POST['allot_from'].'" AND `created_on` <"'.$_POST['allot_to_re'].'" AND `type`="RENT" AND `mop`="credit"';
			$row_credit_rent = mysqli_fetch_array(execute_query($sql_type_credit_rent));
			$cash_rent = $row_cash_rent['amount']-$row_cash_rent['credit_set_amt']-$row_cash_rent['advance_set_amt'];
			$credit_rent = $row_credit_rent['amount']-$row_credit_rent['credit_set_amt']-$row_credit_rent['advance_set_amt'];
			$cash += $cash_rent;
			$credit += $credit_rent;
			echo '<tr><th>Room Rent</th><td>'.$cash_rent.'</td><td>'.$credit_rent.'</td></tr>';

			$sql_type_cash_sale_restaurant = 'SELECT count(*) AS count , SUM(`amount`) AS amount , SUM(`credit_set_amt`) AS credit_set_amt , SUM(`advance_set_amt`) AS advance_set_amt FROM `customer_transactions` WHERE `timestamp` >="'.$_POST['allot_from'].'" AND `timestamp` <"'.$_POST['allot_to_re'].'" AND `type`="sale_restaurant" AND `mop` NOT IN ("credit","nocharge")';
			$row_cash_sale_restaurant = mysqli_fetch_array(execute_query($sql_type_cash_sale_restaurant));
			$sql_type_credit_sale_restaurant = 'SELECT count(*) AS count , SUM(`amount`) AS amount , SUM(`credit_set_amt`) AS credit_set_amt , SUM(`advance_set_amt`) AS advance_set_amt FROM `customer_transactions` WHERE `timestamp` >="'.$_POST['allot_from'].'" AND `timestamp` <"'.$_POST['allot_to_re'].'" AND `type`="sale_restaurant" AND `mop`="credit"';
			$row_credit_sale_restaurant = mysqli_fetch_array(execute_query($sql_type_credit_sale_restaurant));
			$cash_sale_restaurant = $row_cash_sale_restaurant['amount']-$row_cash_sale_restaurant['credit_set_amt']-$row_cash_sale_restaurant['advance_set_amt'];
			$credit_sale_restaurant = $row_credit_sale_restaurant['amount']-$row_credit_sale_restaurant['credit_set_amt']-$row_credit_sale_restaurant['advance_set_amt'];
			$cash += $cash_sale_restaurant;
			$credit += $credit_sale_restaurant;
			echo '<tr><th>Room Service</th><td>'.$cash_sale_restaurant.'</td><td>'.$credit_sale_restaurant.'</td></tr>';

			$sql_type_cash_BAN_AMT = 'SELECT count(*) AS count , SUM(`amount`) AS amount , SUM(`credit_set_amt`) AS credit_set_amt , SUM(`advance_set_amt`) AS advance_set_amt FROM `customer_transactions` WHERE `timestamp` >="'.$_POST['allot_from'].'" AND `timestamp` <"'.$_POST['allot_to_re'].'" AND `type`="BAN_AMT" AND `mop`!="credit"';
			$row_cash_BAN_AMT = mysqli_fetch_array(execute_query($sql_type_cash_BAN_AMT));
			$sql_type_credit_BAN_AMT = 'SELECT count(*) AS count , SUM(`amount`) AS amount , SUM(`credit_set_amt`) AS credit_set_amt , SUM(`advance_set_amt`) AS advance_set_amt FROM `customer_transactions` WHERE `timestamp` >="'.$_POST['allot_from'].'" AND `timestamp` <"'.$_POST['allot_to_re'].'" AND `type`="BAN_AMT" AND `mop`="credit"';
			$row_credit_BAN_AMT = mysqli_fetch_array(execute_query($sql_type_credit_BAN_AMT));
			$cash_BAN_AMT = $row_cash_BAN_AMT['amount']-$row_cash_BAN_AMT['credit_set_amt']-$row_cash_BAN_AMT['advance_set_amt'];
			$credit_BAN_AMT =$row_credit_BAN_AMT['amount']-$row_credit_BAN_AMT['credit_set_amt']-$row_credit_BAN_AMT['advance_set_amt'];
			$cash += $cash_BAN_AMT;
			$credit += $credit_BAN_AMT;
			echo '<tr><th>Banquet</th><td>'.$cash_BAN_AMT.'</td><td>'.$credit_BAN_AMT.'</td></tr>';

			$sql_cash_advance = 'SELECT count(*) AS count , SUM(`amount`) AS amount FROM `customer_transactions` WHERE `timestamp` >="'.$_POST['allot_from'].'" AND `timestamp` <"'.$_POST['allot_to_re'].'" AND `type`="ADVANCE_AMT"'; 
			$row_cash_advance = mysqli_fetch_array(execute_query($sql_cash_advance));
			$cash += $row_cash_advance['amount'];
			$credit += 0;
			echo '<tr><th>Advance Amount</th><td>'.$row_cash_advance['amount'].'</td><td>0</td></tr>';

			$sql_credit_settelment = 'SELECT count(*) AS count , SUM(`amount`) AS amount FROM `customer_transactions` WHERE `timestamp` >="'.$_POST['allot_from'].'" AND `timestamp` <"'.$_POST['allot_to_re'].'" AND `type`="receipt"'; 
			$row_credit_settelment = mysqli_fetch_array(execute_query($sql_credit_settelment));
			$cash += $row_credit_settelment['amount'];
			$credit += 0;
			echo '<tr><th>Credit Settelment</th><td>'.$row_credit_settelment['amount'].'</td><td>0</td></tr>';

			echo '<tr><th>Total</th><th>'.$cash.'</th><th>'.$credit.'</th></tr>';
		?>
	</table>
	
	<br/>
	<table width="100%">
		<tr><th colspan="11">Check In Report</th></tr>
			<tr style="background:#000; color:#FFF;">
				<th>S.No.</th>
				<th>Guest Name</th>
				<!--<th>Company Name</th>-->
				<th>Mobile</th>
				<th>Address</th>
				<!--<th>Occupancy</th>-->
				<th>Room No.</th>
				<th>Extra Bed</th>
				<th>Total Rent</th>
				<th>Night</th>
				<!-- <th>n++</th> -->
				<th>Allotment Date</th>
				<th>Exit Date</th>
				<!--<th>Counter Booking</th>-->
				<th>Reference</th>
				<!--<th>Received Amount</th>-->
				<th>Staus</th>
				<!--<th></th>-->
                <!--<th class="no-print"></th>-->
			</tr>	
		    <?php
			$sql = 'SELECT `allotment`.* FROM `allotment` LEFT JOIN `room_master` on `room_master`.`sno` = `allotment`.`room_id` WHERE (`allotment`.`exit_date` IS NULL OR `allotment`.`exit_date`="") AND `allotment`.`allotment_date`<"'.$_POST['allot_to_re'].'"  ORDER BY `room_master`.`room_name` ASC';
			$result=execute_query($sql);
			$i=1;
			$occcu='';
			$night = 0;
			$grand_total_rent = 0;
			$total_rec_amount = 0;
			foreach($result as $row){
				if($i%2==0){
					$col = '#CCC';
				}
				elseif($row['hold_date']!=''){
					$col = 'red';
				}
				else{
					$col = '#EEE';
				}
				if($row['exit_date']==''){
					$days = get_days($row['allotment_date'] , date("d-m-Y H:i"));
				}
				else{
					$days = get_days($row['allotment_date'] , $row['exit_date']);
				}
				//$days = date("d", $days);
				$total_rent=floatval($row['room_rent'])*$days;
				if($row['cancel_date']!=''){
					$col = '#F00"';
					$cancel = '<br />Cancelled On : '.$row['cancel_date'];
					$cancel_display = 'Uncancel';
				}
				else{
					$row_col = '';
					$cancel = '';
					$cancel_display = 'Cancel';
				}
				echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td>'.$row['guest_name'].$cancel.'</td>';
				//echo '<td>'. get_company_name($row['cust_id']).'</td>';
				$sql_cus="select * from customer where sno='".$row['cust_id']."'";
				$sql_run=execute_query($sql_cus);
				$row_cust=mysqli_fetch_array($sql_run);
		       echo '<td>'.$row_cust['mobile'].'</td>';
		       if($row_cust['address'] != ''){
		       	echo '<td>'.$row_cust['address'].'</td>';
		       }
		       else{
		       	echo '<td>'.$row['guest_address'].'</td>';
		       }
		      // echo '<td>'.$row['occupancy'].'</td>';
				echo '<td>'.get_room($row['room_id']).'</td>
				<td>'.$row['other_charges'].'</td>
				<td>'.$total_rent.'</td>
				<td>'.$days.'</td>
				<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td><td>-</td>';
				//<td>'.get_counter($row['counter_id']).'</td>
				echo '<td>'.get_reference($row['reference']).'</td>';
				//echo '<td class="no-print"><a href="received_amount_print.php?id='.$row['sno'].'" target="_blank">';if($row['received_amount'] > 0){echo $row['received_amount'];}echo '</a></td><td class="print-only">';if($row['received_amount'] > 0){echo $row['received_amount'];}echo '</td>';
				if ($row['exit_date'] == '') {
					echo '<td>IN</td>';
				}
				else{
					echo '<td>OUT</td>';
				}
				/**echo '<td class="no-print"><a href="allotment.php?id='.$row['sno'].'&f=1">Edit</a></td>';
				if($row['hold_date']==''){
				echo '<td class="no-print"><a href="allotment.php?hold='.$row['sno'].'&room_id='.$row['room_id'].'">Hold</a></td>';
				}
				else{
					echo '<td class="no-print">On Hold</td>';
				}
				echo '<td class="no-print"><!--<a href="allotment.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a>--></td>**/
			echo '</tr>'
			;
				$night += $days;
				$grand_total_rent += $total_rent;
				//$total_rec_amount += $row['received_amount'];
			   $occcu =intval($occcu)+intval($row['occupancy']);
			}
			$sql_out = 'SELECT `allotment`.* FROM `allotment` LEFT JOIN `room_master` on `room_master`.`sno` = `allotment`.`room_id` WHERE 1=1 AND `allotment`.`exit_date`>"'.$_POST['allot_from'].'" AND `allotment`.`exit_date`!=""';
			$result=execute_query($sql);
			foreach($result as $row){
				$kl=get_days_other($_POST['allot_from'],$row['exit_date']);
				if (($kl>0 AND $_POST['allot_to_re']>$row['allotment_date']) OR $_POST['allot_from']<$row['allotment_date']) {
				if($i%2==0){
					$col = '#CCC';
				}
				elseif($row['hold_date']!=''){
					$col = 'red';
				}
				else{
					$col = '#EEE';
				}
				if($row['exit_date']==''){
					$days = get_days($row['allotment_date'] , date("d-m-Y H:i"));
				}
				else{
					$days = get_days($row['allotment_date'] , $row['exit_date']);
				}
				//$days = date("d", $days);
				$total_rent=($row['room_rent'])*$days;
				if($row['cancel_date']!=''){
					$col = '#F00"';
					$cancel = '<br />Cancelled On : '.$row['cancel_date'];
					$cancel_display = 'Uncancel';
				}
				else{
					$row_col = '';
					$cancel = '';
					$cancel_display = 'Cancel';
				}
				echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td>'.$row['guest_name'].$cancel.'</td>';
				//echo '<td>'. get_company_name($row['cust_id']).'</td>';
				$sql_cus="select * from customer where sno='".$row['cust_id']."'";
				$sql_run=execute_query($sql_cus);
				$row_cust=mysqli_fetch_array($sql_run);
		       echo '<td>'.$row_cust['mobile'].'</td>';
		       if($row_cust['address'] != ''){
		       	echo '<td>'.$row_cust['address'].'</td>';
		       }
		       else{
		       	echo '<td>'.$row['guest_address'].'</td>';
		       }
		      // echo '<td>'.$row['occupancy'].'</td>';
				echo '<td>'.get_room($row['room_id']).'</td>
				<td>'.$row['other_charges'].'</td>
				<td>'.$total_rent.'</td>
				<td>'.$days.'</td>
				
				<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td><td>'.date("d-m-Y,h-i A" ,strtotime($row['exit_date'])).'</td>';
				//<td>'.get_counter($row['counter_id']).'</td>
				echo '<td>'.get_reference($row['reference']).'</td>';
				//echo '<td class="no-print"><a href="received_amount_print.php?id='.$row['sno'].'" target="_blank">';if($row['received_amount'] > 0){echo $row['received_amount'];}echo '</a></td><td class="print-only">';if($row['received_amount'] > 0){echo $row['received_amount'];}echo '</td>';
				if ($row['exit_date'] == '') {
					echo '<td>IN</td>';
				}
				else{
					echo '<td>OUT</td>';
				}
				/**echo '<td class="no-print"><a href="allotment.php?id='.$row['sno'].'&f=1">Edit</a></td>';
				if($row['hold_date']==''){
				echo '<td class="no-print"><a href="allotment.php?hold='.$row['sno'].'&room_id='.$row['room_id'].'">Hold</a></td>';
				}
				else{
					echo '<td class="no-print">On Hold</td>';
				}
				echo '<td class="no-print"><!--<a href="allotment.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a>--></td>**/
			echo '</tr>'
			;
				$night += $days;
				$grand_total_rent += $total_rent;
				//$total_rec_amount += $row['received_amount'];
			   $occcu +=$row['occupancy'];
			}
			}
			/**echo '<tr>
			<td></td>
			<td></td>
			<td></td>
			<td class="no-print"></td>
			<td class="" style=""><b style="font-size:20px" >Occupancy<b></td><td><b style="font-size:20px">'.$occcu.'</b></td><td></td><td></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td></tr>';**/
			echo '<tr><th colspan="6">Total :</th><th>'.$grand_total_rent.'</th><th>'.$night.'</th><th colspan="3">&nbsp;</th></tr>';
		?>
		</table>
	<?php } ?>
	</div>
	<script>
function submitExportExcel(event) {
    event.preventDefault(); // Stop normal submit
    var form = document.getElementById('report_allotment_daily');
    form.action = 'dailyamount_report_export.php';
    form.submit();
    return false;
}
</script>
