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
	<h2>Credit Bill Settelment Report</h2>
	<div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
                
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
					document.writeln(DateInput('allot_from', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1))
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4))
                    </script>
                    </span>
                    </td>
                </tr>
                <tr>
                	<th>Mode Of Payment</th>
                	<td>
                		<select name="mop" id="mop">
                			<option value="">-SELECT ANY ONE-</option>
                			<option value="cash" <?php if(isset($_POST['mop'])){if($_POST['mop']=="cash"){echo 'selected';}} ?>>CASH</option>
                			<option value="card" <?php if(isset($_POST['mop'])){if($_POST['mop']=="card"){echo 'selected';}} ?>>CARD</option>
                			<option value="other" <?php if(isset($_POST['mop'])){if($_POST['mop']=="other"){echo 'selected';}} ?>>OTHER</option>
                			<option value="bank_transfer" <?php if(isset($_POST['mop'])){if($_POST['mop']=="bank_transfer"){echo 'selected';}} ?>>BANK TRANSFER</option>
                			<option value="cheque" <?php if(isset($_POST['mop'])){if($_POST['mop']=="cheque"){echo 'selected';}} ?>>CHEQUE</option>
                			<option value="paytm" <?php if(isset($_POST['mop'])){if($_POST['mop']=="paytm"){echo 'selected';}} ?>>PAYTM</option>
                			<option value="card_sbi" <?php if(isset($_POST['mop'])){if($_POST['mop']=="card_sbi"){echo 'selected';}} ?>>CARD S.B.I.</option>
                			<option value="card_pnb" <?php if(isset($_POST['mop'])){if($_POST['mop']=="card_pnb"){echo 'selected';}} ?>>CARD P.N.B.</option>
                		</select>
                	</td>
                	<th>Type</th>
                	<td>
                		<select name="type" id="type">
                			<option value="">-SELECT ANY ONE-</option>
                			<option value="room" <?php if(isset($_POST['type'])){if($_POST['type']=="room"){echo 'selected';}} ?>>Room</option>
                			<option value="restaurant" <?php if(isset($_POST['type'])){if($_POST['type']=="restaurant"){echo 'selected';}} ?>>Restaurant</option>
                			<option value="room_service" <?php if(isset($_POST['type'])){if($_POST['type']=="room_service"){echo 'selected';}} ?>>Room Service</option>
                		</select>
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
		<table>
			<tr>
				<th>S.No.</th>
				<th>Date</th>
				<th>Name</th>
				<th>Type</th>
				<th>Invoice No.</th>
				<th>Mode Of Payment</th>
				<th>Amount</th>
				<th>Remarks</th>
			</tr>
			<?php
				$n = 1;
				$sql = 'SELECT * FROM `customer_transactions` WHERE (`type`="receipt" OR `type`="RENT") AND `payment_for`="ROOM" ';
				if(isset($_POST['submit_form'])){
					if($_POST['allot_from'] != ''){
						$sql .= ' AND `timestamp`>="'.$_POST['allot_from'].'" AND `timestamp`<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
					if($_POST['mop'] != ''){
						$sql .= ' AND `mop`="'.$_POST['mop'].'" ';
					}
				} 
				else{
					//$date = date("Y-m-d", strtotime(date("Y-m-d")+86400));
					$sql .= ' AND `timestamp`>="'.date('Y-m-d').'" AND `timestamp`<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				//echo $sql;
				$result = execute_query($sql);
				$total_amount = 0;
				$amount = 0;
				$grand_amount = 0;
				$credit_all = 0;
				$credit_rent = 0;
				$credit_banquet = 0;
				$credit_room_service = 0;
				$credit_restaurant = 0;
				$count_all = 0;
				$count_rent = 0;
				$count_banquet = 0;
				$count_room_service = 0;
				$count_restaurant = 0;
 				while($row = mysqli_fetch_array($result)){
					$total_amount = 0;
					$sql_sub = 'SELECT * FROM `customer_transactions` WHERE `sno` IN (' . implode(',', array_map('intval', explode('#', $row['set_sno']))) . ')';
					$result_sub = execute_query($sql_sub);
					//echo $sql_sub;
					while($row_sub = mysqli_fetch_array($result_sub)){
						$sql_cust = 'SELECT * FROM `customer` WHERE `sno`="'.$row_sub['cust_id'].'"';
						$result_cust = execute_query($sql_cust);
						$row_cust = mysqli_fetch_array($result_cust);
							if($row['amount'] > $row_sub['amount']){
								$amount = $row_sub['amount'];
								$row['amount'] -= $amount;
							}
							else{
								$amount = $row['amount'];
							}
						$type = '';
						$credit_all += $amount;
						$count_all += 1;
						if($row_sub['type']=="RENT" AND $row_sub['payment_for']==""){
							$credit_rent += $amount;
							$count_rent += 1;
							$type = "ROOM";
						}
						else if($row_sub['type']=="sale_restaurant" AND $row_sub['payment_for']="res"){
							if (strpos($row_sub['invoice_no'], 'R') !== false){
								$credit_room_service += $amount;
								$count_room_service += 1;
								$type = "Room Service";
							}
							else{
								$credit_restaurant += $amount;
								$count_restaurant += 1;
								$type = "Restaurant";
							}
						}
						else if($row_sub['type']=="BAN_AMT"){
							$count_banquet += 1; 
							$credit_banquet += $amount;
							$type = "Banquet";
						}
						$show = 0;
						if(isset($_POST['type'])){
							if($_POST['type'] == "room" AND $type == "ROOM"){
								$show = 1;
							}
							else if($_POST['type'] == "restaurant" AND $type == "Restaurant"){
								$show = 1;
							}
							else if($_POST['type'] == "room_service" AND $type == "Room Service"){
								$show = 1;
							}
							elseif($_POST['type'] == ''){
								$show = 1;
							}
							else{
								$show = 0;
							}
						}
						else{
							$show = 1;
						}
						if($show != 0){
							$grand_amount += $amount;
						?>
						<tr>
							<td><?php echo $n++; ?></td>
							<td><?php echo $row['timestamp']; ?></td>
							<td><?php echo $row_cust['company_name'].'-'.$row_cust['cust_name']; ?></td>
							<td><?php echo $type; ?></td>
							<td><?php echo $row_sub['invoice_no']; ?></td>
							<td class="editable" id="row_<?php echo $row['sno']; ?>"><?php if($row['mop'] == "bank_transfer"){echo 'BANK TRANSFER';}elseif($row['mop'] == "card_sbi"){echo 'CARD S.B.I.';}elseif($row['mop'] == "card_pnb"){echo 'CARD P.N.B.';}else{echo strtoupper($row['mop']);} ?></td>
							<td><?php echo $amount; ?></td>
							<td class="editable_remark" id="row_<?php echo $row_sub['sno']; ?>"><?php if($row_sub['credit_settelment_remark'] != ''){echo $row_sub['credit_settelment_remark'];}else{ echo $row['credit_settelment_remark']; }?></td>
						</tr>
						<?php
						}
					}
			?>
		<?php } ?>
			<tr><th colspan="6">Total :</th><th><?php echo $grand_amount; ?></th><th>&nbsp;</th></tr>
		</table>
		<table>
			<tr><th colspan="4">Receipts Summary</th></tr>
			<tr><th>S.No.</th><th>Mode Of Payement</th><th>Count</th><th>Amount</th></tr>
			<?php 
				$n = 1;
				$count_mop = 0;
				$amount_mop = 0;
				$sql_mop = 'SELECT SUM(`amount`) as amount , COUNT(*) AS count , `mop` FROM `customer_transactions` WHERE (`type`="receipt" OR `type`="RENT") AND `payment_for`="ROOM" ';
				if(isset($_POST['submit_form'])){
					if($_POST['allot_from'] != ''){
						$sql_mop .= ' AND `timestamp`>="'.$_POST['allot_from'].'" AND `timestamp`<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
				} 
				else{
					//$date = date("Y-m-d", strtotime(date("Y-m-d")+86400));
					$sql_mop .= ' AND `timestamp`>="'.date('Y-m-d').'" AND `timestamp`<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				$sql_mop .= ' GROUP BY `mop`';
				//echo $sql_mop;
				$result_mop = execute_query($sql_mop);
				while($row_mop = mysqli_fetch_array($result_mop)){
					?>
				<tr>
					<th><?php echo $n++; ?></th>
					<td><?php if($row_mop['mop'] == "bank_transfer"){echo 'BANK TRANSFER';}elseif($row_mop['mop'] == "card_sbi"){echo 'CARD S.B.I.';}elseif($row_mop['mop'] == "card_pnb"){echo 'CARD P.N.B.';}else{echo strtoupper($row_mop['mop']);} ?></td>
					<td><?php echo $row_mop['count']; ?></td>
					<td><?php echo $row_mop['amount']; ?></td>
				</tr>
					<?php
					$count_mop += $row_mop['count'];
					$amount_mop += $row_mop['amount'];
				}
			?>
				<tr>
					<th colspan="2">Total :</th>
					<th><?php echo $count_mop; ?></th>
					<th><?php echo $amount_mop; ?></th>
				</tr>
		</table>
		<table>
			<tr>
				<th colspan="4">Credit Settelment Summary</th>
			</tr>
			<tr>
				<th width="20%">S.No.</th>
				<th>Type</th>
				<th>Count</th>
				<th>Amount</th>
			</tr>
			<tr>
				<th>1</th>
				<td>Rent</td>
				<td><?php echo $count_rent; ?></td>
				<td><?php echo $credit_rent; ?></td>
			</tr>
			<tr>
				<th>2</th>
				<td>Room Service</td>
				<td><?php echo $count_room_service; ?></td>
				<td><?php echo $credit_room_service; ?></td>
			</tr>
			<tr>
				<th>3</th>
				<td>Restaurant</td>
				<td><?php echo $count_restaurant; ?></td>
				<td><?php echo $credit_restaurant; ?></td>
			</tr>
			<tr>
				<th>4</th>
				<td>Banquet</td>
				<td><?php echo $count_banquet; ?></td>
				<td><?php echo $credit_banquet; ?></td>
			</tr>
			<tr>
				<th colspan="2">Total</th>
				<th><?php echo $count_all; ?></th>
				<th><?php echo $credit_all; ?></th>
			</tr>
		</table>			

<script>
$(function () {
	$("td.editable").dblclick(function (e) {
		var currentEle = $(this);
		var id = $(this).attr('id');
		var value = $(this).html();
		id = id.replace("row_", "");
		var txt = '<select name="mode_of_payment" id="mode_of_payment_'+id+'" class="small"><option value="cash">CASH</option><option value="card">CARD</option><option value="other">OTHER</option><option value="bank_transfer">BANK TRANSFER</option><option value="cheque">CHEQUE</option><option value="paytm">PAYTM</option><option value="card_sbi">CARD S.B.I.</option><option value="card_pnb">CARD P.N.B.</option></select><br /><input type="button" value="Save" name="save_button" class="small" onClick="edit_mode_of_payment('+id+');">';
		$(this).html(txt);
	});
});
function edit_mode_of_payment(id){
	//alert("#mode_of_payment_"+id);
	var mop = $("#mode_of_payment_"+id).val();
	$("#row_"+id).html('<img src="images/loading_transparent.gif">');
	$.ajax({
		async: false,
		url: "scripts/ajax.php?id=mop_room&term="+id+"&mop="+mop,
		dataType: "json"
	})
	.done(function(data) {
		data = data[0];
		if(data.result=='true'){
			alert("Updated");
			$("#row_"+id).html(mop);
		}
		else{
			alert("Failed. Retry.");
			var txt = '<select name="mode_of_payment" id="mode_of_payment_'+id+'" class="small"><option value="cash">CASH</option><option value="card">CARD</option><option value="other">OTHER</option><option value="bank_transfer">BANK TRANSFER</option><option value="cheque">CHEQUE</option><option value="paytm">PAYTM</option><option value="card_sbi">CARD S.B.I.</option><option value="card_pnb">CARD P.N.B.</option></select><br /><input type="button" value="Save" name="save_button" class="small" onClick="edit_mode_of_payment('+id+');"> ';
			$("#row_"+id).html(txt);
		}
	});

}	
</script>
<script>
$(function () {
	$("td.editable_remark").dblclick(function (e) {
		var currentEle = $(this);
		var id = $(this).attr('id');
		var value = $(this).html();
		id = id.replace("row_", "");
		var txt = '<input type="text" name="remark" id="remark_'+id+'" class="small" value="'+value+'"><br/><input type="button" value="Save" name="save_button" class="small" onClick="edit_remark('+id+');">';
		$(this).html(txt);
	});
});
function edit_remark(id){
	//alert("#mode_of_payment_"+id);
	var remark = $("#remark_"+id).val();
	$("#row_"+id).html('<img src="images/loading_transparent.gif">');
	$.ajax({
		async: false,
		url: "scripts/ajax.php?id=remark_change&term="+id+"&remark="+remark,
		dataType: "json"
	})
	.done(function(data) {
		data = data[0];
		if(data.result=='true'){
			alert("Updated");
			$("#row_"+id).html(remark);
		}
		else{
			alert("Failed. Retry.");
			var txt = '<input type="text" name="remark" id="remark_'+id+'" class="small" value="'+value+'"><br/><input type="button" value="Save" name="save_button" class="small" onClick="edit_remark('+id+');">';
			$("#row_"+id).html(txt);
		}
	});

}	
</script>