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
<script type="text/javascript">
	$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=customer",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {

			$('#cust_id').val(ui.item.id);
			$('#cust_name1').val(ui.item.cust_name);
			
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name1").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
	
</script>

 <div id="container">
	<h2>Check In Report</h2>
	<div class="no-print" style="text-align: right;"><input class="large" type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
                
                	<td>Summary Date</td>
                    <td><input name="allotment_date" type="text" value="<?php if(isset($_POST['allotment_date'])){echo $_POST['allotment_date'];}else{echo date("Y-m-d H:i:s");}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="allotment_date" /></td>
                    <td colspan="2">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </td>
                    <td colspan="2">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </td>
                </tr>
                <!--<tr class="no-print">
                		<th>Guest Name</th>
						<td><input type="text" id="cust_name1" name="cust_name" value="<?php if(isset($_POST['cust_name'])){echo $_POST['cust_name'];} ?>">
							<input type="hidden" id="cust_id" name="cust_id" value="<?php if(isset($_POST['cust_id'])){echo $_POST['cust_id'];} ?>">
						</td>
						<th>Status</th>
						<td>
							<select name="status">
								<option value="">-All-</option>
								<option value="in" <?php if(isset($_POST['status'])){if($_POST['status'] == 'in'){echo 'selected';}} ?>>In</option>
								<option value="out" <?php if(isset($_POST['status'])){if($_POST['status'] == 'out'){echo 'selected';}} ?>>Out</option>
							</select>
						</td>-->
						<!--<th>Invoice No</th>
						<td><input type="text" name="inv" id="inv" value="<?php if(isset($_POST['inv'])){echo $_POST['inv'];} ?>"></td>
                </tr>
                <tr>
                	<th>Reference</th>
					<td>
						<select name="reference" id="reference">
							<option value="">-Select Any One-</option>
						<?php 
							$sql_reference = 'SELECT * FROM `admin_reference`';
							$result_reference = execute_query($sql_reference);
							while ($row_reference = mysqli_fetch_array($result_reference)) {
						?>
							<option value="<?php echo $row_reference['sno']; ?>" <?php if(isset($_POST['reference'])){if($_POST['reference'] == $row_reference['sno']){echo 'selected';}} ?>><?php echo $row_reference['name']; ?></option>
						<?php
							}
						?>
						</select>
					</td>
                </tr>-->
                <!--<tr>
                	<th>Counter Name</th>
					<td>
						<select name="counter_id">
							<option value="">-All-</option>
							
						</select>
					</td>
                </tr>
                <tr class="no-print">
                	
                	<th>Invoice Type</th>
                    <th>
                    <select name="invoice_type" id="invoice_type">
                    	<option value="all" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='all'){echo 'selected="selected"';}}?>>All</option>
                    	<option value="tax_invoice" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='tax_invoice'){echo 'selected="selected"';}}?>>Tax Invoice All</option>
                    	<option value="tax_invoice_w_gstin" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='tax_invoice_w_gstin'){echo 'selected="selected"';}}?>>Tax Invoice with GSTIN</option>
                    	<option value="tax_invoice_wo_gstin" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='tax_invoice_wo_gstin'){echo 'selected="selected"';}}?>>Tax Invoice without GSTIN</option>
                    	<option value="bill_of_supply" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='bill_of_supply'){echo 'selected="selected"';}}?>>Non Taxable Invoice</option>
                    </select></th>
                </tr>-->
        </table>
        <table width="100%">
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
				<th>Allotment Date</th>
				<th>Exit Date</th>
				<!--<th>Counter Booking</th>-->
				<!--<th>Number Of Person</th>-->
				<th>Reference</th>
				<!--<th>Received Amount</th>-->
				<th>Staus</th>
				<!--<th></th>-->
                <!--<th class="no-print"></th>-->
			</tr>	
		    <?php
			if (isset($_POST['submit_form'])) {
			    $_POST['allotment_date'] = date("Y-m-d 23:59:59", strtotime($_POST['allotment_date']));
				$sql = 'SELECT `allotment`.* FROM `allotment` LEFT JOIN `room_master` on `room_master`.`sno` = `allotment`.`room_id` where ("'.$_POST['allotment_date'].'" between allotment_date and exit_date) or ((exit_date is null or exit_date="")  and allotment_date<="'.$_POST['allotment_date'].'")'; 
				$sql .= ' ORDER BY `allotment`.`allotment_date` DESC';
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
					// $total_rent=($row['room_rent'])*$days;
					$total_rent = intval($row['room_rent']) * intval($days);
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
					<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
					<td>'.($row['exit_date']==''?'':date("d-m-Y,h-i A" ,strtotime($row['exit_date']))).'</td>
					';
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
				   $occcu =floatval($occcu)+floatval($row['occupancy']);
				}
				/**echo '<tr>
				<td></td>
				<td></td>
				<td></td>
				<td class="no-print"></td>
				<td class="" style=""><b style="font-size:20px" >Occupancy<b></td><td><b style="font-size:20px">'.$occcu.'</b></td><td></td><td></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td></tr>';**/
				echo '<tr><th colspan="6">Total :</th><th>'.$grand_total_rent.'</th><th>'.$night.'</th><th colspan="4">&nbsp;</th></tr>';
			}
		?>
		</table>
    </form>
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#allotment_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['allotment_date'])){
		echo $_POST['allotment_date'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>',
	});
</script>
