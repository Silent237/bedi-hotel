<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
$tab=0;
date_default_timezone_set('Asia/Calcutta');
page_header();

if(isset($_POST['submit'])){
	$conn=$db;
	if($_POST['cust_id']==''){
		$msg .= '<li class="error">Enter Customer Name</li>';
	}
	if($_POST['amount']==''){
		$msg .= '<li class="error">Enter Amount</li>';
	}
	if($msg==''){
		$_POST['payment_date'] = date("Y-m-d H:i:s", strtotime($_POST['payment_date']));
		if($_POST['cust_transact_sno']!=''){
			$sql = 'update customer_transactions set 
			cust_id="'.$_POST['cust_id'].'", 
			allotment_id="'.$_POST['allotment_id'].'", 
			timestamp="'.$_POST['payment_date'].'", 
			edited_by="'.$_SESSION['username'].'", 
			edited_on=CURRENT_TIMESTAMP,
			remarks="'.$_POST['remarks'].'", 
			amount="'.$_POST['amount'].'",
			mop="'.$_POST['mop'].'"
			where sno='.$_POST['cust_transact_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
			//echo $sql;
		}
		else{
			$type='payment';
			$sql='INSERT INTO customer_transactions (cust_id , type , timestamp, amount, mop, created_by , created_on , remarks) VALUES ("'.$_POST['cust_id'].'", "'.$type.'" , "'.$_POST['payment_date'].'"  , "'.$_POST['amount'].'", "'.$_POST['mop'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'")';
			$result = execute_query($sql);
			$msg .= '<li class="error">payment Added</li>';
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from customer_transactions where sno="'.$_GET['id'].'" and type="PAYMENT"';
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
			$.getJSON("scripts/ajax.php?id=customer_payment",request, response);
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
			$('#amount').val(ui.item.amount);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>
 <div id="container">
        <h2>Payments</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="payment.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Guest Name</td>
					<td><input id="cust_name" name="cust_name" class="field text medium" maxlength="255" tabindex="1" value="<?php if(isset($_GET['id'])){ echo $cust_details['cust_name'];} else if(isset($_GET['cid'])) echo $row_cust_trans['cust_name']; ?>" type="text"> 
					<input id="cust_id" name="cust_id" type="hidden" value="<?php 
					if(isset($_GET['id'])){ 
						echo $cust_details['cust_id'];
					} 
					else if(isset($_GET['cid'])){
						echo $_GET['cid'];
					}?>"></td>
				
					<td>Mobile</td>
					<td><input name="mobile" type="text" value="<?php if(isset($_GET['id'])){echo $cust_details['mobile'];} else if(isset($_GET['cid']))  { echo $row_cust_trans['mobile'];}?>" class="field text medium" tabindex="2" id="mobile" /></td>
				</tr>
				<tr>
                <td>Payment Date</td>
					<td>
						<input name="payment_date" type="text" value="<?php if(isset($row_cust_trans['timestamp'])){echo $row_cust_trans['timestamp'];} else{echo date("Y-m-d");} ?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="payment_date" />
					</td>
						
						
					</td>
				
					<td>Mode of Payment</td>
				   <td><select id="mop" name="mop" class="field select medium">
				   	<option value="cash" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'cash'){ ?> selected="selected"<?php }} ?>>Cash</option>
				   	<option value="card" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'card'){ ?> selected="selected"<?php }} ?>>Card</option>
				   	<option value="credit" <?php if(isset($_GET['id'])){if($row_cust_trans['mop'] == 'credit'){ ?> selected="selected"<?php }} ?>>Credit</option>
				   </select>
				</tr>
				<tr>
					<td>Amount</td>
					<td><input id="amount" name="amount" value="<?php if(isset($_GET['id'])){echo $row_cust_trans['amount'];} else if(isset($_GET['cid'])) { echo $_GET['pending'];}?>" class="field text medium" maxlength="255" tabindex="5" type="text" />
				
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="<?php if(isset($_GET['id'])){echo $row_cust_trans['remarks'];}?>" class="field text medium" maxlength="255" tabindex="6" type="text" />
				</tr>
				</tr>
				<tr><input type="hidden" name="cust_transact_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					
					<td colspan="4"><input id="submit" name="submit" class="btTxt submit" type="submit" value="Submit" onMouseDown="" tabindex="23"></td>
				</tr>
				
			</table>
		</form>

		
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#payment_date').datetimepicker({
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
<?php
navigation('');
page_footer();
?>
