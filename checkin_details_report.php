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
	<h2>Check In Amount Detailed Report</h2>
	<div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
                	<th>Check In Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
					document.writeln(DateInput('allot_from', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1))
                    </script>
                    </span>
                    </td>
                	<th>Check In Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4))
                    </script>
                    </span>
                    </td>
                </tr>
                <tr class="no-print">
                		<th>Guest Name</th>
						<td><input type="text" id="cust_name1" name="cust_name" value="<?php if(isset($_POST['cust_name'])){echo $_POST['cust_name'];} ?>">
							<input type="hidden" id="cust_id" name="cust_id" value="<?php if(isset($_POST['cust_id'])){echo $_POST['cust_id'];} ?>">
						</td>
						<!-- <th>Status</th>
						<td>
							<select name="status">
								<option value="">-All-</option>
								<option value="in" <?php if(isset($_POST['status'])){if($_POST['status'] == 'in'){echo 'selected';}} ?>>In</option>
								<option value="out" <?php if(isset($_POST['status'])){if($_POST['status'] == 'out'){echo 'selected';}} ?>>Out</option>
							</select>
						</td> -->
						<!--<th>Invoice No</th>
						<td><input type="text" name="inv" id="inv" value="<?php if(isset($_POST['inv'])){echo $_POST['inv'];} ?>"></td>-->
                	<!-- <th>Reference</th>
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
					</td> -->
                </tr>
				<tr class="no-print">
                	<th colspan="2">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="2">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
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
				<!-- <th>Extra Bed</th> -->
				<th>Allotment Date</th>
				<th>Night</th>
				<th>Total Rent (+)</th>
				<th>Room Service (+)</th>
				<th>Advance Amount (-)</th>
				<th>Total (=)</th>
				
			</tr>	
		    <?php
			$sql = 'SELECT `allotment`.*, `allotment`.`sno` AS allot_id  FROM `allotment` LEFT JOIN `room_master` on `room_master`.`sno` = `allotment`.`room_id` WHERE (`allotment`.`exit_date` IS NULL OR `allotment`.`exit_date`="") ';
			if (isset($_POST['submit_form'])) {
				$_POST['allot_from'] = date("Y-m-d", strtotime($_POST['allot_from']));
				$_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to']));
				$_POST['allot_to_re'] = date("Y-m-d", strtotime($_POST['allot_to'])+86400);
				$sql .= ' AND `allotment`.`allotment_date`>="'.$_POST['allot_from'].'" AND `allotment`.`allotment_date`<"'.$_POST['allot_to_re'].'"'; 

				if ($_POST['cust_id'] != '') {
					$sql .= ' AND `allotment`.`cust_id`="'.$_POST['cust_id'].'"';
				}
				elseif ($_POST['cust_name'] != '') {
					$sql .= ' AND `allotment`.`guest_name` LIKE "%'.$_POST['cust_name'].'%"';
				}
				
			}
		
			$sql .= ' ORDER BY `allotment`.`allotment_date` DESC';
			$result = execute_query($sql);
			$i=1;
			$occcu='';
			$night = 0;
			$grand_total_rent = 0;
			$total_rec_amount = 0;
			$grand_room_serive = 0;
			$grand_advance = 0;
			$grand = 0;

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
		     
				echo '<td>'.get_room($row['room_id']).'</td>';
			
				echo '<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>';
				echo '<td>'.$days.'</td>';
				echo '<td>'.$total_rent.'</td>';
				//`allotment_id`="'.$row['allot_id'].'" AND
				$room_service_amount = mysqli_fetch_array(execute_query('SELECT SUM(`taxable_amount`) AS amt FROM `invoice_sale_restaurant` WHERE  `mode_of_payment`="credit"'))['amt'];
				$grand_room_serive += $room_service_amount;
				echo '<td>'.$room_service_amount.'</td>';
				$advance_amount = mysqli_fetch_array(execute_query('SELECT SUM(`advance_amount`) AS amt FROM `advance_booking` WHERE `advance_for_checkin_id`="'.$row['allot_id'].'" AND `purpose`="advance_for_checkin"'))['amt'];
				$grand_advance += $advance_amount;
				echo '<td>'.$advance_amount.'</td>';
				$total = $total_rent + $room_service_amount - $advance_amount;
				$grand += $total;
				echo '<td>'.$total.'</td>';
				
			echo '</tr>';
			
				$night += $days;
				$grand_total_rent += $total_rent;
			   $occcu =intval($occcu)+intval($row['occupancy']);
			}
			
			echo '<tr><th colspan="6">Total :</th><th>'.$night.'</th><th>'.$grand_total_rent.'</th><th>'.$grand_room_serive.'</th><th>'.$grand_advance.'</th><th>'.$grand.'</th></tr>';
		?>
		</table>
    </form>
</div>
