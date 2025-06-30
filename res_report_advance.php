<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
page_header();
navigation('');
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
if(isset($_GET['cancel_id'])){
	$sql='update customer_transactions set `type`="ADVANCE_AMT_CANCEL" where sno='.$_GET['cancel_id'];
	$result = execute_query($sql);
}
?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
<script type="text/javascript" language="javascript">
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name1",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='cust_name']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=company_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company_name']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#company_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>
<!--<div id="content" class="print-only">
    <div style="border:0px solid; margin:0 auto; text-align:center"><h1>Shane Avadh Hotel</h1>
		<h1>Civil Lines Faizabad, Ayodhya-224001<br/>Registration No. 02/476 J.A./Sarai Act/2003. CIN: U55101UP1980PTC005050</h1>
		<h1>GSTIN : 09AAHCS2262A1ZN</h1>
    </div>
</div>-->
 <div id="container">
        <h2>Advance Report</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="res_report_advance.php" id="report_form" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
            		<td>Date Type</td>
            		<td>
            			<select name="date_type" id="date_type">
            				<option value="booking_wise" <?php if(isset($_POST['date_type'])){if($_POST['date_type'] == 'booking_wise'){echo 'selected';}} ?>>Entry Date</option>
            				<option value="allotment_wise" <?php if(isset($_POST['date_type'])){if($_POST['date_type'] == 'allotment_wise'){echo 'selected';}} ?>>Booking Date</option>
            			</select>
            		</td>
            		<td>Date From :</td>
                    <td>
                    <span> 
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1));
                    </script>
                    </span>
	                </td>
	                <td>Date To :</td>
	                <td>
                    <span> 
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4));
                    </script>
                    </span>
                    </td>
                </tr>
                <tr>
                	<td>Guest Name</td>
                	<td>
                		<input type="text" name="cust_name" id="cust_name" value="<?php if(isset($_POST['cust_name'])){echo $_POST['cust_name'];}?>">
                		<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_POST['cust_sno'])){echo $_POST['cust_sno'];}?>">
                	</td>
                	<td>Company Name</td>
                	<td><input type="text" name="company_name" id="company_name" value="<?php if(isset($_POST['company_name'])){echo $_POST['company_name'];}?>"></td>
                	<td>Type</td>
                	<td>
                		<select name="type" id="type">
                			<option value="">-All-</option>
                			<option value="room_rent" <?php if(isset($_POST['type'])){if($_POST['type'] == 'room_rent'){echo 'selected';}} ?>>Room Booking</option>
                			<option value="banquet_rent" <?php if(isset($_POST['type'])){if($_POST['type'] == 'banquet_rent'){echo 'selected';}} ?>>Banquet Booking</option>
                		</select>
                	</td>
                </tr>
                <tr>
                	<td>Mode Of Payment</td>
                	<td>
                		<select name="mop" id="mop">
                			<option value="">-All-</option>
                			<option value="cash" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'cash'){ ?> selected="selected"<?php }} ?>>Cash</option>
						   	<option value="card" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'card'){ ?> selected="selected"<?php }} ?>>Card</option>
						   	<option value="other" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'other'){ ?> selected="selected"<?php }} ?>>Other</option>
						   	<option value="bank_transfer" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'bank_transfer'){ ?> selected="selected"<?php }} ?>>Bank Transfer</option>
						   	<option value="cheque" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'cheque'){ ?> selected="selected"<?php }} ?>>Cheque</option>
						   	<option value="paytm" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'paytm'){ ?> selected="selected"<?php }} ?>>Paytm</option>
						   	<option value="card_sbi" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'card_sbi'){ ?> selected="selected"<?php }} ?>>Card S.B.I</option>
						   	<option value="card_pnb" <?php if(isset($_POST['mop'])){if($_POST['mop'] == 'cheque'){ ?> selected="selected"<?php }} ?>>Card P.N.B.</option>
                		</select>
                	</td>
                	<td>Status</td>
                	<td>
                		<select name="status" id="status">
                			<option value="">-All-</option>
                			<option value="1" <?php if(isset($_POST['status'])){if($_POST['status'] == '1'){echo 'selected';}} ?>>BOOKED</option>
                			<option value="0" <?php if(isset($_POST['status'])){if($_POST['status'] == '0'){echo 'selected';}} ?>>NON BOOKED</option>
                		</select>
                	</td>
                	<td>Cancel Status</td>
                	<td>
                		<select name="cancel_status" id="cancel_status">
                			<option value="">-All-</option>
                			<option value="ADVANCE_AMT_CANCEL" <?php if(isset($_POST['cancel_status'])){if($_POST['cancel_status'] == 'ADVANCE_AMT_CANCEL'){echo 'selected';}} ?>>Canceled</option>
                			<option value="ADVANCE_AMT" <?php if(isset($_POST['cancel_status'])){if($_POST['cancel_status'] == 'ADVANCE_AMT'){echo 'selected';}} ?>>Not-Canceled</option>
                		</select>
                	</td>
                </tr>
            	<tr class="no-print">
                	<th colspan="2">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="2">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
					<th colspan="2">
                <input type="submit" name="download_excel" value="Download in Excel" class="btTxt submit btn-danger" onclick="return submitExportExcel(event);">
            </th>
                </tr>
            </table>	
		</form>
			<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Receipt No.</th>
					<th>Comapny Name</th>
					<th>Guest Name</th>
					<th>Mobile</th>
					<th>Date Of Entry</th>
                    <th>Type</th>
                    <th>MOP</th>
                    <th>Total Amount</th>
                    <th>Advance Amount</th>
                    <th>Due Amount</th>
                    <th>Booking Date</th>
                    <th>Check In Date</th>
                    <th>Check Out Date</th>
                    <th>No. Of Rooms</th>
                    <th>Room Number</th>
					<th class="no-print">Status</th>
					<th class="no-print">Edit</th>
					<th class="no-print">View</th>
					<th class="no-print">Cancel</th>
				</tr>
    <?php
    			$sql_mop = '';
				$sql = 'select * from res_advance_booking where 1=1 ';
				if(isset($_POST['submit_form'])){
					if($_POST['date_type'] == 'booking_wise'){
						$sql .= ' and created_on>="'.$_POST['allot_from'].'" and created_on<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
					else if($_POST['date_type'] == 'allotment_wise'){
						$sql .= ' and allotment_date>="'.$_POST['allot_from'].'" and allotment_date<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
					else{
						$sql .= ' and created_on>="'.date("Y-m-d").'" and created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
					}
					if ($_POST['cust_sno'] != '') {
						$sql .= ' AND `cust_id`="'.$_POST['cust_sno'].'" ';
					}
					if ($_POST['type'] != '') {
						$sql .= ' AND `purpose`="'.$_POST['type'].'" ';
					}
					if ($_POST['status'] != '') {
						$sql .= ' AND `status`="'.$_POST['status'].'" ';
					}
				}
				else{
					$sql .= ' and created_on>="'.date("Y-m-d").'" and created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				$result=execute_query($sql);
				$i=1;
				$tot=0;
				$tot_total=0;
				$tot_due=0;
				foreach($result as $row)
				{
					$sql_mop = 'SELECT * FROM `customer_transactions` WHERE `advance_booking_id`="'.$row['sno'].'" ';
					//echo $sql_mop;
					if(isset($_POST['submit_form'])){
						if ($_POST['mop'] != '') {
							$sql_mop .= ' AND `mop`="'.$_POST['mop'].'" ';
						}
						if ($_POST['cancel_status'] != '') {
							$sql_mop .= ' AND `type`="'.$_POST['cancel_status'].'" ';
						}
					}
				// 	echo $sql_mop;
					$result_mop = execute_query($sql_mop);
					while($row_mop = mysqli_fetch_array($result_mop)){
						if($i%2==0){
							$col = '#CCC';
						}
						else{
							$col = '#EEE';
						}
						if ($row_mop['type'] == 'ADVANCE_AMT_CANCEL') {
							$col = '#dd4a4a';
						}
						$sql='select * from customer where sno='.$row['cust_id'];
						$result=execute_query($sql);
						$details=mysqli_fetch_array($result);
						$tot+= $row['advance_amount'];
						$tot_total+= floatval($row['total_amount']);
						$tot_due+= $row['due_amount'];
						echo '<tr style="background:'.$col.'">
						<td>'.$i++.'</td>
						<td>'.$row['sno'].'</td>
						<td>'.$details['company_name'].'</td>
						<td>'.$row['guest_name'].'</td>
						<td>'.$details['mobile'].'</td>
						<td>'.date('d-m-Y' , strtotime($row['created_on'])).'</td>';
						if($row['purpose'] == "room_rent"){
							echo '<td>Room Booking</td>';
						}
						elseif($row['purpose'] == "banquet_rent"){
							echo '<td>Banquet Booking</td>';
						}
						elseif($row['purpose'] == "advance_for"){
							$sql_advance_for = 'SELECT * FROM `res_advance_booking` WHERE `sno`="'.$row['advance_for_id'].'"';
							$result_advance_for = execute_query($sql_advance_for);
							$row_advance_for = mysqli_fetch_array($result_advance_for);
							if($row_advance_for['purpose'] == "room_rent"){
								echo '<td>Room Booking(Plus Amount)</td>';
							}
							elseif($row_advance_for['purpose'] == "banquet_rent"){
								echo '<td>Banquet Booking(Plus Amount)</td>';
							}
						}
						elseif($row['purpose'] == "advance_for_checkin"){
							echo '<td>Room Booking(In House Guest)</td>';
						}
						else{
							echo '<td></td>';
						}
						?>
						<td class="editable" id="row_<?php echo $row_mop['sno']; ?>"><?php if($row_mop['mop'] == "bank_transfer"){echo 'BANK TRANSFER';}elseif($row_mop['mop'] == "card_sbi"){echo 'CARD S.B.I.';}elseif($row_mop['mop'] == "card_pnb"){echo 'CARD P.N.B.';}else{echo strtoupper($row_mop['mop']);} ?></td>
						<td><?php echo $row['total_amount']; ?></td>
						<?php
						//if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
							echo '<td class="editable_amount" id="row_amount_'.$row['sno'].'">'.$row['advance_amount'].'</td>';
						/**}
						else{
							echo '<td>'.$row['advance_amount'].'</td>';
						}**/
						echo '<td>'.$row['due_amount'].'</td>';
						echo '<td>'.date('d-m-Y h:i:s' , strtotime($row['allotment_date'])).'</td>';
						echo '<td>'.date('d-m-Y h:i:s' , strtotime($row['check_in'])).'</td>';
						echo '<td>'.date('d-m-Y h:i:s' , strtotime($row['check_out'])).'</td>';
						echo '<td>'.$row['number_of_room'].'</td>
						<td>'.$row['room_number'].'</td>';

						if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
							if($row['purpose'] == "room_rent"){
								echo '<td class="no-print"><a href="allotment.php?check_in='.$row['sno'].'"  target="_blank">Allot Room</a></td>';
							}
							elseif($row['purpose'] == "banquet_rent"){
								echo '<td class="no-print"><a href="banquet_hall.php?allot='.$row['sno'].'"  target="_blank">Allot Banquet</a></td>';
							}
							else{
								echo '<td class="no-print">&nbsp;</td>';
							}
						}
						elseif($row_mop['type'] == 'ADVANCE_AMT_CANCEL'){
							echo '<td class="no-print">Canceled</td>';
						}
						else{
							echo '<td class="no-print">Booked</td>';
						}
						if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
						echo '<td class="no-print"><a href="res_advance_booking.php?e_id='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-edit"></i></a></td>';
						}
						else{
							echo '<td class="no-print"></td>';
						}
						echo '<td class="no-print"><a href="res_advance_print.php?print_id='.$row['sno'].'"  target="_blank"><i class="fas fa-eye"></i></a></td>';
						if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
						echo '<td class="no-print"><a href="res_report_advance.php?cancel_id='.$row_mop['sno'].'" onclick="return confirm(\'Are you sure?\');">Cancel</a></td>';
						}
						else{
							echo '<td class="no-print"></td>';
						}
						echo '</tr>';
					}
				}
				echo '<tr>
				    <th colspan="8">Total :</th>
				    <th>'.$tot_total.'</th>
				    <th>'.$tot.'</th>
				    <th>'.$tot_due.'</th>
				    <th colspan="9">&nbsp;</th>
				</tr>';
?>
</table>
</div>
<script>
$(function () {
	$("td.editable").dblclick(function (e) {
		var currentEle = $(this);
		var id = $(this).attr('id');
		var value = $(this).html();
		id = id.replace("row_", "");
		var txt = '<select name="mode_of_payment" id="mode_of_payment_'+id+'" class="small"><option value="cash" ';
		if(value=='cash'){
			txt += ' selected="selected" ';
		}
		txt += '>CASH</option><option value="card" ';
		if(value=='card'){
			txt += ' selected="selected" ';
		}
		txt += '>CARD</option><option value="other" ';
		if(value=='other'){
			txt += ' selected="selected" ';
		}txt += '>Other</option><option value="paytm" ';
		if(value=='PAYTM'){
			txt += ' selected="selected" ';
		}txt += '>PAYTM</option><option value="bank_transfer" ';
		if(value=='bank_transfer'){
			txt += ' selected="selected" ';
		}txt += '>BANK TRANSFER</option><option value="cheque" ';
		if(value=='cheque'){
			txt += ' selected="selected" ';
		}txt += '>CHEQUE</option><option value="card_sbi" ';
		if(value=='card_sbi'){
			txt += ' selected="selected" ';
		}txt += '>Card S.B.I</option><option value="card_pnb" ';
		if(value=='card_pnb'){
			txt += ' selected="selected" ';
		}txt += '>Card P.N.B.</option></select><br /><input type="button" value="Save" name="save_button" class="small" onClick="edit_mode_of_payment('+id+');">';
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
			if (mop == "bank_transfer") {
				mop = "BANK TRANSFER";
			}
			$("#row_"+id).html(mop);
		}
		else{
			alert("Failed. Retry.");
			var txt = '<select name="mode_of_payment" id="mode_of_payment_'+id+'" class="small"><option value="CASH" ';
			txt += '>CASH</option><option value="CARD" ';
			txt += '>CARD</option><option value="CREDIT" ';
			txt += '>CREDIT</option></option><option value="PAYTM" ';
			txt += '>PAYTM</option></option><option value="bank_transfer" ';
			txt += '>BANK TRANSFER</option><option value="cheque" ';
			txt += '>CHEQUE</option></select><br/><input type="button" value="Save" name="save_button" class="small" onClick="edit_mode_of_payment('+id+');">';
			$("#row_"+id).html(txt);
		}
	});

}	
</script>
<script type="text/javascript">
$(function () {
	$("td.editable_amount").dblclick(function (e) {
		var currentEle = $(this);
		var id = $(this).attr('id');
		var value = $(this).html();
		id = id.replace("row_amount_", "");
		var txt = '<input type="text" name="edit_amount" id="edit_amount_'+id+'" value="'+value+'" class="small"><br /><input type="button" value="Save" name="save_button" class="small" onClick="edit_amount('+id+');">';
		$(this).html(txt);
	});
});
function edit_amount(id){
	//alert("#mode_of_payment_"+id);
	var amount = $("#edit_amount_"+id).val();
	$("#row_amount_"+id).html('<img src="images/loading_transparent.gif">');
	$.ajax({
		async: false,
		url: "scripts/ajax.php?id=advance_amount_edit&term="+id+"&amount="+amount,
		dataType: "json"
	})
	.done(function(data) {
		data = data[0];
		if(data.result=='true'){
			alert("Updated");
			$("#row_amount_"+id).html(amount);
		}
		else{
			alert("Failed. Retry.");
			var txt = '<input type="text" name="edit_amount" id="edit_amount_'+id+'" value="'+amount+'" class="small"><br /><input type="button" value="Save" name="save_button" class="small" onClick="edit_amount('+id+');">';
			$("#row_amount_"+id).html(txt);
		}
	});

}
</script>

<script>
function submitExportExcel(event) {
    event.preventDefault(); // Stop normal submit
    var form = document.getElementById('report_form');
    form.action = 'res_advance_report_export.php';
    form.submit();
    return false;
}
</script>
<?php

page_footer();
?>
