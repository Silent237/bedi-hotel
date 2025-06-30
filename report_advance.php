<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
page_header();
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
	.table tr td {
        background-color: transparent !important;
        box-shadow: none !important;
    }
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
		<form action="report_advance.php" id="report_form" class="wufoo leftLabel page1"  
      name="addnewdesignation" enctype="multipart/form-data" method="post">

    <table width="100%">
        <tr>
            <td>Date Type</td>
            <td>
                <select name="date_type" id="date_type">
                    <option value="booking_wise" <?php if(isset($_POST['date_type']) && $_POST['date_type'] == 'booking_wise') echo 'selected'; ?>>Entry Date</option>
                    <option value="allotment_wise" <?php if(isset($_POST['date_type']) && $_POST['date_type'] == 'allotment_wise') echo 'selected'; ?>>Booking Date</option>
                </select>
            </td>
            <td>Date From :</td>
            <td>
                <span> 
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', "report_form", false, 'YYYY-MM-DD', '<?php echo isset($_POST['allot_from']) ? $_POST['allot_from'] : date("Y-m-d"); ?>', 1));
                    </script>
                </span>
            </td>
            <td>Date To :</td>
            <td>
                <span> 
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', "report_form", false, 'YYYY-MM-DD', '<?php echo isset($_POST['allot_to']) ? $_POST['allot_to'] : date("Y-m-d"); ?>', 4));
                    </script>
                </span>
            </td>
        </tr>

        <tr>
            <td>Guest Name</td>
            <td>
                <input type="text" name="cust_name" id="cust_name" value="<?php echo isset($_POST['cust_name']) ? htmlspecialchars($_POST['cust_name']) : ''; ?>">
                <input type="hidden" name="cust_sno" id="cust_sno" value="<?php echo isset($_POST['cust_sno']) ? htmlspecialchars($_POST['cust_sno']) : ''; ?>">
            </td>

            <td>Room Category</td>
            <td>
                <select id="cat" name="cat[]" class="field select medium" onchange="fetchRemainingRooms(this)">
                    <option value="">-- Select Room Category --</option>
                    <?php
                    // Fetch categories from the database
                    $query = "SELECT sno, room_type FROM category";
                    $result = execute_query($query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $roomsno = htmlspecialchars($row['sno']); // Category ID
                        $roomType = htmlspecialchars($row['room_type']); // Room Type
                        $selected = (isset($_POST['cat']) && in_array($roomsno, $_POST['cat'])) ? 'selected' : '';
                        echo "<option value='$roomsno' $selected>$roomType</option>";
                    }
                    ?>
                </select>
            </td>

            <td>Type</td>
            <td>
                <select name="type" id="type">
                    <option value="">-All-</option>
                    <option value="room_rent" <?php if(isset($_POST['type']) && $_POST['type'] == 'room_rent') echo 'selected'; ?>>Room Booking</option>
                    <option value="banquet_rent" <?php if(isset($_POST['type']) && $_POST['type'] == 'banquet_rent') echo 'selected'; ?>>Banquet Booking</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>Mode Of Payment</td>
            <td>
                <select name="mop" id="mop">
                    <option value="">-All-</option>
                    <option value="cash" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'cash') echo 'selected'; ?>>Cash</option>
                    <option value="card" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'card') echo 'selected'; ?>>Card</option>
                    <option value="other" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'other') echo 'selected'; ?>>Other</option>
                    <option value="bank_transfer" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'bank_transfer') echo 'selected'; ?>>Bank Transfer</option>
                    <option value="cheque" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'cheque') echo 'selected'; ?>>Cheque</option>
                    <option value="paytm" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'paytm') echo 'selected'; ?>>Paytm</option>
                    <option value="card_sbi" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'card_sbi') echo 'selected'; ?>>Card S.B.I</option>
                    <option value="card_pnb" <?php if(isset($_POST['mop']) && $_POST['mop'] == 'card_pnb') echo 'selected'; ?>>Card P.N.B.</option>
                </select>
            </td>

            <td>Status</td>
            <td>
                <select name="status" id="status">
                    <option value="">-All-</option>
                    <option value="1" <?php if(isset($_POST['status']) && $_POST['status'] == '1') echo 'selected'; ?>>BOOKED</option>
                    <option value="0" <?php if(isset($_POST['status']) && $_POST['status'] == '0') echo 'selected'; ?>>NON BOOKED</option>
                </select>
            </td>

            <td>Cancel Status</td>
            <td>
                <select name="cancel_status" id="cancel_status">
                    <option value="">-All-</option>
                    <option value="ADVANCE_AMT_CANCEL" <?php if(isset($_POST['cancel_status']) && $_POST['cancel_status'] == 'ADVANCE_AMT_CANCEL') echo 'selected'; ?>>Canceled</option>
                    <option value="ADVANCE_AMT" <?php if(isset($_POST['cancel_status']) && $_POST['cancel_status'] == 'ADVANCE_AMT') echo 'selected'; ?>>Not-Canceled</option>
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
			<table width="100%" class="table table-bordered">
				<tr>
					<th  style="background:#00888d; color:#FFF;">S.No.</th>
					<th  style="background:#00888d; color:#FFF;">Receipt No.</th>
					<th  style="background:#00888d; color:#FFF;">Comapny Name</th>
					<th  style="background:#00888d; color:#FFF;">Guest Name</th>
					<th  style="background:#00888d; color:#FFF;">Mobile</th>
					<th  style="background:#00888d; color:#FFF;">Date Of Entry</th>
                    <th  style="background:#00888d; color:#FFF;">Type</th>
                    <th  style="background:#00888d; color:#FFF;">MOP</th>
                    <th  style="background:#00888d; color:#FFF;">Total Amount</th>
                    <th  style="background:#00888d; color:#FFF;">Advance Amount</th>
                    <th  style="background:#00888d; color:#FFF;">Due Amount</th>
                    <th  style="background:#00888d; color:#FFF;">Booking Date</th>
                    <th  style="background:#00888d; color:#FFF;">Check In Date</th>
                    <th  style="background:#00888d; color:#FFF;">Check Out Date</th>
                    <th  style="background:#00888d; color:#FFF;">Room Category</th>
                    <th  style="background:#00888d; color:#FFF;">No. Of Rooms</th>
                    <th  style="background:#00888d; color:#FFF;">Room Number</th>
					<th  style="background:#00888d; color:#FFF;" class="no-print">Status</th>
					<th  style="background:#00888d; color:#FFF;" class="no-print">Edit</th>
					<th  style="background:#00888d; color:#FFF;" class="no-print">View</th>
					<th  style="background:#00888d; color:#FFF;" class="no-print">Cancel</th>
				</tr>
    <?php
    			$sql_mop = '';
				$sql = 'select * from advance_booking where 1=1 ';
				if(isset($_POST['submit_form'])){
					if($_POST['date_type'] == 'booking_wise'){
						$sql .= ' and created_on>="'.$_POST['allot_from'].'" and created_on<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
					else if($_POST['date_type'] == 'allotment_wise'){
						//$sql .= ' and allotment_date>="'.$_POST['allot_from'].'" and allotment_date<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
						
						$sql .= ' and (("'.$_POST['allot_from'].'" between check_in and check_out) or ("'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'" between check_in and check_out))';
					}
					else{
						$sql .= ' and created_on>="'.date("Y-m-d").'" and created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
					}
					if ($_POST['cust_sno'] != '') {
						$sql .= ' AND `cust_id`="'.$_POST['cust_sno'].'" ';
					}
					if (!empty($_POST['cust_name'])) {  
						$sql .= ' AND guest_name LIKE "%' . $_POST['cust_name'] . '%"';
					}
					if ($_POST['type'] != '') {
						$sql .= ' AND `purpose`="'.$_POST['type'].'" ';
					}
					if ($_POST['status'] != '') {
						$sql .= ' AND `status`="'.$_POST['status'].'" ';
					}
					if (!empty($_POST['cat'])) {
						// Remove empty values from array
						$selectedCategories = array_filter($_POST['cat']);
					
						if (!empty($selectedCategories)) { // Ensure at least one valid category is selected
							$categoryConditions = [];
							foreach ($selectedCategories as $category) {
								$categoryConditions[] = 'FIND_IN_SET("' . $category . '", cat_id)';
							}
							$sql .= ' AND (' . implode(' OR ', $categoryConditions) . ')';
						}
					}
					
					
				}
				else{
					$sql .= ' and created_on>="'.date("Y-m-d").'" and created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				//echo $sql;
				$result = execute_query($sql);
				while($row = mysqli_fetch_array($result)){
				$i=1;
				$tot=0;
				$tot_total=0;
				$tot_due=0;
				foreach($result as $row)
				{
					$sql_mop = 'SELECT * FROM `customer_transactions` WHERE `advance_booking_id`="'.$row['sno'].'" ';
					if(isset($_POST['submit_form'])){
						if ($_POST['mop'] != '') {
							$sql_mop .= ' AND `mop`="'.$_POST['mop'].'" ';
						}
						if ($_POST['cancel_status'] != '') {
							$sql_mop .= ' AND `type`="'.$_POST['cancel_status'].'" ';
						}
					}
					//echo $sql_mop.'<br>';
					$result_mop = execute_query($sql_mop);
					if(mysqli_num_rows($result_mop)!=0){
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
    					}
					}
					else{
					    
					    $row_mop['type'] = '';
					    $row_mop['sno'] = '';
					    $row_mop['mop'] = '';
					}
					$sql='select * from customer where sno='.$row['cust_id'];
					$result = execute_query($sql);
					$details=mysqli_fetch_assoc( $result );
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
						$sql_advance_for = 'SELECT * FROM `advance_booking` WHERE `sno`="'.$row['advance_for_id'].'"';
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
					<?php
					$catIds = explode(',', $row['cat_id']); // Convert cat_id string to an array
					$roomTypes = [];
				
					foreach ($catIds as $catId) {
						$catQuery = 'SELECT room_type FROM category WHERE sno="' . trim($catId) . '"';
						$catRes = execute_query($catQuery);
				
						if ($catRow = mysqli_fetch_assoc($catRes)) {
							$roomTypes[] = $catRow['room_type']; // Store room type in array
						}
					}
					$roomTypeList = implode(', ', $roomTypes);
					$sql_mop = 'SELECT * FROM `customer_transactions` WHERE `advance_booking_id`="'.$row['sno'].'" ';
					$result_mop = execute_query($sql_mop);
					$row_mop=mysqli_fetch_assoc($result_mop);
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
					echo '<td>'.$roomTypeList.'</td>
					<td>'.$row['number_of_room'].'</td>
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
					echo '<td class="no-print"><a href="advance_booking.php?e_id='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-edit"></i></a></td>';
					}
					else{
						echo '<td class="no-print"></td>';
					}
					echo '<td class="no-print"><a href="advance_print.php?print_id='.$row['sno'].'"  target="_blank"><i class="fas fa-eye"></i></a></td>';
					if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
					echo '<td class="no-print"><a href="report_advance.php?cancel_id='.$row_mop['sno'].'" onclick="return confirm(\'Are you sure?\');">Cancel</a></td>';
					}
					else{
						echo '<td class="no-print"></td>';
					}
					echo '</tr>';
				}
			
				echo '<tr>
				    <th colspan="8">Total :</th>
				    <th>'.$tot_total.'</th>
				    <th>'.$tot.'</th>
				    <th>'.$tot_due.'</th>
				    <th colspan="9">&nbsp;</th>
				</tr>';
			}
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
    form.action = 'report_advance_export.php'; // Change form action to export page
    form.submit();

    // Optionally reset action to original if needed later
    // setTimeout(() => form.action = 'report_advance.php', 1000);

    return false;
}
</script>

<?php
navigation('');
page_footer();
?>
