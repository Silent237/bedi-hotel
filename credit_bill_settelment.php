<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
navigation('');
page_footer();

if(isset($_POST['submit'])){
	$conn=$db;
	if($_POST['cust_id']==''){
		$msg .= '<li class="error">Enter Customer Name</li>';
	}
	if($_POST['amount']==''){
		$msg .= '<li class="error">Enter Amount</li>';
	}
	if($msg==''){
		$_POST['receipt_date'] = date("Y-m-d H:i:s", strtotime($_POST['receipt_date']));
		if($_POST['cust_transact_sno']!=''){
			$sql = 'update customer_transactions set 
			cust_id="'.$_POST['cust_id'].'", 
			timestamp="'.$_POST['receipt_date'].'", 
			edited_by="'.$_SESSION['username'].'", 
			edited_on=CURRENT_TIMESTAMP,
			remarks="'.$_POST['remarks'].'", 
			amount="'.$_POST['amount'].'",
			mop="'.$_POST['mop'].'"
			where sno='.$_POST['cust_transact_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
		}
		else{
			//$type=$_POST['pay_for'];
			$amount = $_POST['amount'];
			$remainning = 0;
			$set_sno = '';
			for($i = 1;$i <= $_POST['number_of_credit'];$i++){
				//echo $i.'ch';
				$paid = 0;
				if(isset($_POST['checkbox_'.$i])){
					//echo $i.'amt';
					if($amount > 0){
						//echo $i.'ch';
						if($_POST['amount_'.$i] <= $amount){
							$paid = $_POST['amount_'.$i];
							$amount = $amount - $_POST['amount_'.$i];
						}
						else if($_POST['amount_'.$i] > $amount){
							$paid = $amount;
							$amount = 0;
						}
						$paid = $paid + $_POST['credit_set_amount_'.$i];
						$credit_update = 'UPDATE `customer_transactions` SET `credit_set_amt`="'.$paid.'" WHERE `sno`="'.$_POST['sno_'.$i].'"';
						execute_query($credit_update);
						//echo $credit_update;
						$set_sno .= $_POST['sno_'.$i];
						if($amount > 0){
							$set_sno .= '#';
						}
					}
					//echo '<br/>';
				}
			}
			//echo $set_sno.'<br/>';
			$sql='INSERT INTO customer_transactions (cust_id , type , timestamp, amount, mop, created_by , created_on , remarks,payment_for,`set_sno`,`credit_settelment_remark`) VALUES ("'.$_POST['cust_id'].'", "receipt" , "'.$_POST['receipt_date'].'"  , "'.$_POST['amount'].'", "'.$_POST['mop'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "", "ROOM" ,"'.$set_sno.'","'.$_POST['credit_settelment_remark'].'")';
			$result = execute_query($sql);
			$sql_last_sno = 'SELECT * FROM `customer_transactions` ORDER BY ABS(`sno`) DESC LIMIT 1';
			$result_last_sno = execute_query($sql_last_sno);
			$row_last_sno = mysqli_fetch_array($result_last_sno);
			$last_sno = $row_last_sno['sno'];
			for($i = 1;$i <= $_POST['number_of_credit'];$i++){
				if(isset($_POST['checkbox_'.$i])){					
					$credit_update_sno = 'UPDATE `customer_transactions` SET `credit_bill_paid_sno`="'.$last_sno.'" WHERE `sno`="'.$_POST['sno_'.$i].'"';
					execute_query($credit_update_sno);
				}
			}
			$msg .= '<li class="error">Receipt Added</li>';
			//echo $sql;
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from customer_transactions where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row_cust_trans=mysqli_fetch_assoc( $result );
	$timestamp = $row_cust_trans['timestamp'];
	$sql='select *,allotment.sno as allotment_id, customer.sno as cust_id from customer join allotment where customer.sno='.$row_cust_trans['cust_id'];
	$result = execute_query($sql);
	$cust_details=mysqli_fetch_assoc( $result );
}
if(isset($_GET['cid'])){
	$sql='select * from customer where sno='.$_GET['cid'];
	$result = execute_query($sql);
	$row_cust_trans=mysqli_fetch_assoc( $result );
	$sql='select * from allotment where cust_id='.$_GET['cid'];
	$result = execute_query($sql);
	$room_id=mysqli_fetch_assoc( $result );
	$_GET['pending']=abs($_GET['pending']);
	
}
if(isset($_GET['del'])){
	$sql='delete from customer_transactions where sno='.$_GET['del'];
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
			$.getJSON("scripts/ajax.php?id=customer_credit",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='cust_name']").val(ui.item.label);
			$('#cust_id').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			//$('#amount').val(ui.item.amount);
			$('#amount').val(0);
			$('#amount_hidden').val(0);
			$('td#insertelement').html(ui.item.txt);
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
			$.getJSON("scripts/ajax.php?id=invoice_credit",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='invoice_number']").val(ui.item.label);
		    $('#cust_name').val(ui.item.cust_name);
			$('#cust_id').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			//$('#amount').val(ui.item.amount);
			$('#amount').val(0);
			$('#amount_hidden').val(0);
			$('td#insertelement').html(ui.item.txt);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#invoice_number").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>
 <div id="container">
        <h2>Credit Bill Settelment</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="credit_bill_settelment.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Invoice Number</td>
					<td><input type="text" name="invoice_number" id="invoice_number" value=""></td>
				</tr>
				<tr>
					<td>Company/Guest Name</td>
					<td><input id="cust_name" name="cust_name" class="field text medium" maxlength="255" tabindex="1" value="<?php if(isset($_GET['id'])){ echo $cust_details['cust_name'];} else if(isset($_GET['id'])) echo $row_cust_trans['cust_name']; ?>" type="text"> 
					<input id="cust_id" name="cust_id" type="hidden" value="<?php 
					if(isset($_GET['id'])){ 
						echo $cust_details['cust_id'];
					} 
					else if(isset($_GET['cid'])){
						echo $_GET['cid'];
					}?>"></td>
				
					<td>Mobile</td>
					<td><input name="mobile" type="text" value="<?php if(isset($_GET['id'])){echo $cust_details['mobile'];} else if(isset($_GET['id']))  { echo $row_cust_trans['mobile'];}?>" class="field text medium" tabindex="2" id="mobile" /></td>
				</tr>
				<tr>
                <td>Receipt Date</td>
					<td><input name="receipt_date" type="text" value="<?php if(isset($row_cust_trans['timestamp'])){echo $row_cust_trans['timestamp'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="receipt_date" />
					</td>
				
					<td>Mode of Payment</td>
				   <td><select id="mop" name="mop" class="field select medium">
				   	<option value="cash" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'cash'){ ?> selected="selected"<?php }} ?>>Cash</option>
				   	<option value="card" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'card'){ ?> selected="selected"<?php }} ?>>Card</option>
				   	<option value="other" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'other'){ ?> selected="selected"<?php }} ?>>Other</option>
				   	<option value="bank_transfer" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'bank_transfer'){ ?> selected="selected"<?php }} ?>>Bank Transfer</option>
				   	<option value="cheque" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'cheque'){ ?> selected="selected"<?php }} ?>>Cheque</option>
				   	<option value="paytm" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'paytm'){ ?> selected="selected"<?php }} ?>>Paytm</option>
				   	<option value="card_sbi" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'card_sbi'){ ?> selected="selected"<?php }} ?>>Card S.B.I</option>
				   	<option value="card_pnb" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'cheque'){ ?> selected="selected"<?php }} ?>>Card P.N.B.</option>
				   </select>
				</tr>
				 <tr>
					<!--<td>Receipt For</td>
				   <td><select id="pay_for" name="pay_for" class="field select medium">
				   	<option value="RENT" <?php if(isset($_GET['id'])){if($row_cust_trans['type'] == 'RENT'){ ?> selected="selected"<?php }} ?>>Rent</option>
				   	<option value="receipt" <?php if(isset($_GET['id'])){if($row_cust_trans['type'] == 'receipt'){ ?> selected="selected"<?php }} ?>>other</option>
				   	
				   </select>-->
					<td>Check All</td><td><input type="checkbox" name="checkbox_all" id="checkbox_all" onclick="check_all();"></td>
					<td>Amount</td>
					<td><input id="amount" name="amount" value="<?php if(isset($_GET['id'])){echo $row_cust_trans['amount'];} else if(isset($_GET['cid'])) { echo $_GET['pending'];}?>" class="field text medium" maxlength="255" tabindex="5" type="text"/ onblur="amount_validation();"><input id="amount_hidden" name="amount_hidden" class="field text medium" maxlength="255" type="hidden"/></td>

				</tr>
			<!--	<tr>
					
					<td>Payment For </td>
					<td>
						<select name="payment_for">
							<option value="">Select</option>
							<option value="1" <?php if(isset($_GET['id'])){if($row_cust_trans['payment_for'] == '1'){ ?> selected="selected"<?php }} ?> >Shane Avadh</option>
							<option value="2"  <?php if(isset($_GET['id'])){ if($row_cust_trans['payment_for'] == '2') { ?> selected="selected"<?php }} ?> >Room Booking</option>
							<option value="3"  <?php if(isset($_GET['id'])){if($row_cust_trans['payment_for'] == '3') { ?> selected="selected"<?php }} ?> >Party</option>
							<option value="advance"  <?php if(isset($_GET['id'])){if($row_cust_trans['payment_for'] == 'advance') { ?> selected="selected"<?php }} ?> >Advance</option>
						</select>
					</td>
				</tr> -->
				<tr>
					<td>Remarks</td>
					<td colspan="3"><input id="credit_settelment_remark" name="credit_settelment_remark" value="<?php if(isset($_GET['id'])){echo $row_cust_trans['credit_settelment_remark'];}?>" class="field text medium" maxlength="255" tabindex="6" type="text" />
				</tr>
				<tr>
					<td colspan="4" id="insertelement"></td>
				</tr>
				<tr><input type="hidden" name="cust_transact_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<td><input id="submit" name="submit" class="btTxt submit" type="submit" value="Submit" onMouseDown="" tabindex="24"></td>
				</tr>
				
			</table>
		</form>
 <table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Guest Name</th>
					<th>Mobile</th>
                    <th>Payment For</th>
                    <th>Mode of Payment</th>
                    <th>Amount</th>
                    <th>Date Of Receipt</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
			$sql = 'select  * from customer_transactions where type in ("receipt", "ADVANCE","RENT") and payment_for="ROOM" order by sno desc limit 50';
			$result = execute_query($sql);
	while($row = mysqli_fetch_array($result)){
	$i=1;
	foreach($result as $row_cust_trans)
	{
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
		$sql='select * from customer where sno='.$row_cust_trans['cust_id'];
		$result = execute_query($sql);
		$details=mysqli_fetch_assoc($result);
		$sql='select * from allotment where sno="'.$row_cust_trans['allotment_id'].'"';
		$result = execute_query($sql);
		$room_details=mysqli_fetch_assoc($result);
			echo '<tr style="background:'.$col.'">
			<td>'.$i++.'</td>
			<td>'.$details['cust_name'].'</td>
			<td>'.$details['mobile'].'</td>';
			if($row_cust_trans['payment_for']=='ROOM' && $row_cust_trans['type']=='RENT'){
				echo'<td>Room Booking</td>';
			}
			else{
				echo'<td>Advance</td>';
			}
			
			if($row_cust_trans['mop']=='cash'){
				echo '<td>Cash</td>';
			}
			else{
				echo '<td>Card</td>';
			}
			echo '
			<td>'.$row_cust_trans['amount'].'</td>
			<td>'.$row_cust_trans['timestamp'].'</td>
			<td><a href="receipts.php?id='.$row_cust_trans['sno'].'">Edit</a></td>
			<td><a href="receipts.php?del='.$row_cust_trans['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
			</tr>';
	}
}
?>
</table>
	
</div>

<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#receipt_date').datetimepicker({
	step:15,
	format: 'd-m-Y H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['id'])){
		echo date("d-m-Y H:i", strtotime($timestamp));
	}
	else{
		echo date("d-m-Y H:i");	
	}
	?>'
	});

	
</script>
<script>
	function add_amount(){
		var grand_credit_total = 0;
		var amt = 0;
		var n = document.getElementById('number_of_credit').value;
		//alert(n);
		for(var i=1; i<=n; i++){
			if($('#checkbox_'+i).prop("checked") == true){
                amt = parseFloat(document.getElementById('amount_'+i).value);
                //alert(amt);
            }
            else{
            	amt = 0;
            }
            grand_credit_total += amt;
		}
		$('#amount_hidden').val(grand_credit_total);
		$('#amount').val(grand_credit_total);
	}
	function amount_validation(){
	 	var amt = parseFloat(document.getElementById('amount').value);
	 	var amt_hidden = parseFloat(document.getElementById('amount_hidden').value);
	 	if(amt > amt_hidden){
	 		$('#amount').val(amt_hidden);
	 		alert("More Than Cheked Amount Is Not Allow...");
	 	}
	}
	function check_all(){
		var n = document.getElementById('number_of_credit').value;
		//alert(n);
		for(var i=1; i<=n; i++){
			if($('#checkbox_all').prop("checked") == true){
                $('#checkbox_'+i).prop('checked', true);
            }
            else{
            	$('#checkbox_'+i).prop('checked', false);
            }
		}
		add_amount();
	}
</script>

