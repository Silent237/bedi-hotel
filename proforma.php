<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$tab=1;
$conn = $db;
$ban_id = '';
//print_r($_POST);
//echo date("d-m-Y H:i:s");
	if(isset($_POST['submit'])){
		if(isset($_POST['edit_id'])&&($_POST['edit_id'])!=''){
			$sql_up = "UPDATE `proforma_invoice` SET
				  `guest_name` = '".$_POST['cust_name1']."',
				  `mob_no` = '".$_POST['mobile']."',
				  `company_name` = '".$_POST['company_name']."',
				  `address` = '".$_POST['address']."',
				  `gstin` = '".$_POST['id_2']."',
				  `pin_code` = '".$_POST['zipcode']."',
				  `cindt` = '".$_POST['cindt']."',
				  `coutdt` = '".$_POST['coutdt']."',
				  `remarks` = '".$_POST['remarks']."',
				  `edited_by` = '".$_SESSION['username']."',
				  `edition_time` = '".date("Y-m-d H:i:s")."'
				WHERE `sno` = '".$_POST['edit_id']."';
				";
				//echo $sql_up;
				$res_up = execute_query($sql_up);
				if(!mysqli_error($db)){
				    $msg="<h4 class='alert alert-success' style='color:green;'>Data Updated</h4>";
				    $sql = 'delete from proforma_transition where proforma_invoice_sno="'.$_POST['edit_id'].'"';
				    execute_query($sql);
				    $inserted_id = $_POST['edit_id'];
				}else{
					$msg="<h4 class='alert alert-danger' style='color:red;'>Data Could not Update</h4>";
				}
		}
		else{
    		$sql='INSERT INTO `proforma_invoice`(`guest_name`, `mob_no`, `company_name`, `address`, `gstin`, `pin_code`, `cindt`, `coutdt`, `remarks`, `quantity`,`days`, `amount`, `sgst`, `cgst`, `totel`, `created_by`, `creation_time`) VALUES ("'.$_POST['cust_name1'].'", "'.$_POST['mobile'].'", "'.$_POST['company_name'].'", "'.$_POST['address'].'", "'.$_POST['id_2'].'", "'.$_POST['zipcode'].'", "'.$_POST['cindt'].'","'.$_POST['coutdt'].'", "'.$_POST['remarks'].'", "'.$_POST['quantity'].'", "'.$_POST['days'].'", "'.$_POST['amount'].'", "'.$_POST['sgst'].'", "'.$_POST['cgst'].'", "'.$_POST['grand_total'].'", "'.$_SESSION['username'].'","'.date("d-m-Y H:i:s").'")';
    		execute_query($sql);
    		$inserted_id=mysqli_insert_id($db);
		}
		//$_POST['insert_id'] = 3;
		for($i=1;$i<=$_POST['insert_id'];$i++){
		    if($_POST['quantity_'.$i]!='' && $_POST['quantity_'.$i]!='0'){
    			$insql='INSERT INTO `proforma_transition`(`proforma_invoice_sno`, `particulars`, `rate`,  `quantity`, `days`, `amount`, `sgst`, `cgst`, `total`, `creation_time`) VALUES ("'.$inserted_id.'", "'.$_POST['particular_'.$i].'","'.$_POST['rate_'.$i].'","'.$_POST['quantity_'.$i].'","'.$_POST['days_'.$i].'","'.$_POST['amount_'.$i].'","'.$_POST['sgst_'.$i].'","'.$_POST['cgst_'.$i].'","'.$_POST['grand_total_'.$i].'","'.date("d-m-Y H:i:s").'")';
    			
    			if(execute_query($insql)){
    				$msg.="<h4 class='alert alert-success' style='color:green;'>Data inserted</h4>";
    				
    			}
    			else{
    				$msg.="<h4 class='alert alert-danger' style='color:red;'>Data Could not insert</h4>";
    			}
	        }
			
			
		}
		$msg=$msg1;
		$msg .= '<li class="error">Data Inserted &nbsp; <a href="print_proforma.php?id='.$inserted_id.'" target="_blank">Print</a></li>';
	}
if(isset($_GET['e_id'])){
	$sql = 'SELECT * FROM `proforma_invoice` WHERE `sno`="'.$_GET['e_id'].'"';
	$result = execute_query($sql);
	$row_customer_edit = mysqli_fetch_array($result);
}	
?>
<style>
	.ui-autocomplete-loading { 
		background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; 
	}
</style>
<script src="js/jquery.datetimepicker.full.js"></script>

<div id="container">
        <h2>Proforma Entry</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="<?php echo $_SERVER['PHP_SELF']?>" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Guest Name</td>
					<td>
						<input id="cust_name1" name="cust_name1" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['guest_name'];} ?>">
					</td>
					<td>Mobile</td>
					<td>
						<input id="mobile" name="mobile" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['mob_no'];}elseif(isset($_GET['allot'])){echo $customer_allot['mobile'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
				</tr>
				<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['sno'];}elseif(isset($_GET['allot'])){echo $customer_allot['sno'];} ?>" />
				<tr>
					<td>Company Name</td>
					<td>
						<input id="company_name" name="company_name" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['company_name'];}elseif(isset($_GET['allot'])){echo $customer_allot['company_name'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>	
					<td>Address</td>
					<td>
						<input id="address" name="address" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['address'];}elseif(isset($_GET['allot'])){echo $customer_allot['address'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
				</tr>
				<tr>
					
					<td>GSTIN</td>
					<td>
						<input id="id_2" name="id_2" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['gstin'];}elseif(isset($_GET['allot'])){echo $customer_allot['id_2'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
					<td>PIN Code</td>
					<td>
						<input name="zipcode" type="text" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['pin_code'];}elseif(isset($_GET['allot'])){echo $customer_allot['zipcode'];} ?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="zipcode" />
					</td>
				</tr>
				<tr>
					<td>Check-In Date</td>
					<td>
						<input name="cindt" type="text" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['cindt'];} ?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="cindt" />
					</td>
					
					<td>Check-Out Date</td>
					<td>
						<input id="coutdt" name="coutdt" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['coutdt'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
				</tr>
				<tr>
					
					
					<td>Remarks</td>
					<td>
						<input id="remarks" name="remarks" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['remarks'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
						<input type="hidden" name="edit_id" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['sno'];} ?>">
					</td>
				</tr>
				<tr>
					
				</tr>
				
					
				
			</table>
		<table width="100%">
			<tr style="background:#000; color:#FFF;">
				<th rowspan="2">S.No.</th>
				<th rowspan="2">Particulars</th>
				<th rowspan="2">Rate</th>
				<th rowspan="2">Quantity</th>
				<th rowspan="2">Days</th>
				<th rowspan="2">Amount</th>
				<th colspan="2">GST</th>
				<th rowspan="2">Total</th>
			</tr>	
			<tr style="background:#000; color:#FFF;">
				<th>SGST </th>
				<th>CGST </th>
			</tr>
    <?php
    	$i = 1;
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
?>
			
			<?php 
			if(isset($_GET['e_id'])){
			    $sql = 'select * from proforma_transition where proforma_invoice_sno='.$row_customer_edit['sno'];
			    $result_particular_edit = execute_query($sql);
			    //echo $sql;
				while($row_particular_edit = mysqli_fetch_array($result_particular_edit)){
			?>
			<tr style="background: <?php echo $col; ?>;text-align: center;" width="100%">
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<input type="text" name="particular_<?php echo $i; ?>" id="particular_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" value="<?php echo $row_particular_edit['particulars']; ?>">
					<input type="hidden" name="particular_sno_<?php echo $i; ?>" value="<?php echo $row_particular_edit['sno']; ?>">
				</td>
				<td>
					<input type="text" name="rate_<?php echo $i; ?>" id="rate_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" value="<?php echo $row_particular_edit['rate']; ?>" onblur="insert_row();">
				</td>
				<td>
					<input type="text" name="quantity_<?php echo $i; ?>" id="quantity_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();" value="<?php echo $row_particular_edit['quantity']; ?>" >
				</td>
				<td>
					<input type="text" name="days_<?php echo $i; ?>" id="days_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();" value="<?php echo $row_particular_edit['days']; ?>" >
				</td>
				<td>
					<input type="text" name="amount_<?php echo $i; ?>" id="amount_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly value="<?php echo $row_particular_edit['amount']; ?>">
				</td>
				<td>
					<input type="text" name="sgst_<?php echo $i; ?>" id="sgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;"  value="<?php echo $row_particular_edit['sgst']; ?>" onblur="insert_row();">
				</td>
				<td>
					<input type="text" name="cgst_<?php echo $i; ?>" id="cgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;"  value="<?php echo $row_particular_edit['cgst']; ?>" onblur="insert_row();">
				</td>
				<td>
					<input type="text" name="grand_total_<?php echo $i; ?>" id="grand_total_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly value="<?php echo $row_particular_edit['total']; ?>">
				</td>
			</tr>
			<?php
				$i++;
				}
			}
			?>
			<tr style="background: <?php echo $col; ?>;text-align: center;" width="100%">
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<input type="text" name="particular_<?php echo $i; ?>" id="particular_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;">
					<input type="hidden" name="particular_sno_<?php echo $i; ?>">
				</td>
				<td>
					<input type="text" name="rate_<?php echo $i; ?>" id="rate_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();">
				</td>
				<td>
					<input type="text" name="quantity_<?php echo $i; ?>" id="quantity_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();">
				</td>
				<td>
					<input type="text" name="days_<?php echo $i; ?>" id="days_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();" value="<?php echo $row_particular_edit['quantity']; ?>" >
				</td>
				<td>
					<input type="text" name="amount_<?php echo $i; ?>" id="amount_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly>
				</td>
				<td>
					<input type="text" name="sgst_<?php echo $i; ?>" id="sgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();" >
				</td>
				<td>
					<input type="text" name="cgst_<?php echo $i; ?>" id="cgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();" >
				</td>
				<td>
					<input type="text" name="grand_total_<?php echo $i; ?>" id="grand_total_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly>
				</td>
			</tr>
			<tr id="total">
				<th colspan="3">Total</th>
				<th><input type="text" name="quantity" id="quantity" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['total_quantity'];} ?>"></th>
				<th><input type="text" name="days" id="days" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['total_days'];} ?>"></th>
				<th><input type="text" name="amount" id="amount" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['amount'];} ?>"></th>
				<th><input type="text" name="sgst" id="sgst"  style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['sgst'];} ?>"></th>
				<th><input type="text" name="cgst" id="cgst"  style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['cgst'];} ?>"></th>
				<th><input type="text" name="grand_total" id="grand_total" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['grand_total'];} ?>"></th>
			</tr>
			<tr>
				<td colspan="8">
					<input type="hidden" name="allot_sno" id="allot_sno" value="<?php if(isset($_GET['allot'])){echo $_GET['allot'];}?>" />
				<input type="hidden" name="edit_sno" value="<?php if(isset($_GET['e_id'])){echo $_GET['e_id'];} ?>">
				<input id="submit" name="submit" class="btTxt submit" type="submit" value="Submit" onMouseDown="" ></td>
			</tr>
		</table>
		<input type="hidden" name="insert_id" id="insert_id" value="<?php echo $i; ?>">
	</form>
</div>

<?php
navigation('');
page_footer();
?>
<script type="text/javascript">
	function insert_row(){
		var total_quantity = 0;
		var total_days = 0;
		var total_amount = 0;
		var total_sgst = 0;
		var total_cgst = 0;
		var total_grand_total = 0; 
		var insert = 0;
		var i = document.getElementById('insert_id').value;
		//var advance_amount = parseFloat(document.getElementById('advance_amount').value);
		for (var k = 1; k <= i; k++) {
			var particular = document.getElementById('particular_'+k).value;
			if(particular == ''){
				insert = insert+1;
			}
			else{
				var rate = parseFloat(document.getElementById('rate_'+k).value);
				var quantity = parseFloat(document.getElementById('quantity_'+k).value);
				var days = parseFloat(document.getElementById('days_'+k).value);
				if(!rate){
					rate = 0;
				}
				if(!quantity){
					quantity = 0;
				}
				if(!days){
					days = 0;
				}
				var amount = rate * quantity * days;
				var sgst = parseFloat(document.getElementById('sgst_'+k).value);
				if(!sgst){
					sgst=0;
				}

				var cgst = parseFloat(document.getElementById('cgst_'+k).value);
				if(!cgst){
					cgst=0;
				}
				
				var grand_total = amount + sgst + cgst;
				$('#amount_'+k).val(amount.toFixed(3));
				//$('#sgst_'+k).val(sgst.toFixed(3));
				//$('#cgst_'+k).val(cgst.toFixed(3));
				$('#grand_total_'+k).val(grand_total.toFixed(3));
				total_quantity += quantity;
				total_days += days;
				total_amount += amount;
				total_sgst += sgst;
				total_cgst += cgst;
				total_grand_total += grand_total;  
			}
		}
		$("#quantity").val(total_quantity.toFixed(3));
		$("#days").val(total_days.toFixed(3));
		$("#amount").val(total_amount.toFixed(3));
		$("#sgst").val(total_sgst.toFixed(3));
		$("#cgst").val(total_cgst.toFixed(3));
		$("#grand_total").val(total_grand_total.toFixed(3));
		$("#total_amount").val(total_grand_total.toFixed(3));
		/*if (advance_amount > total_grand_total) {
			$("#advance_amount_paid").val(total_grand_total.toFixed(3));
		}
		else{
			$("#advance_amount_paid").val(advance_amount.toFixed(3));
		}*/
		i++;
		var tab = 12;
		if(i%2==0){
			var col = '#CCC';
		}
		else{
			var col = '#EEE';
		}
		if(insert == 0){
			tab = tab + 7 * (i - 1);
			var txt = '<tr style="background:'+col+'"><td>'+i+'</td><td><input type="text" name="particular_'+i+'" id="particular_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;"><input type="hidden" name="particular_sno_'+i+'"></td><td><input type="text" name="rate_'+i+'" id="rate_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();"></td><td><input type="text" name="quantity_'+i+'" id="quantity_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();"><td><input type="text" name="days_'+i+'" id="days_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();"></td></td><td><input type="text" name="amount_'+i+'" id="amount_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" readonly></td><td><input type="text" name="sgst_'+i+'" id="sgst_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();" ></td><td><input type="text" name="cgst_'+i+'" id="cgst_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();" ></td><td><input type="text" name="grand_total_'+i+'" id="grand_total_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" readonly></td></tr>';
			$(txt).insertBefore("#total");
			$("#insert_id").val(i);
		}
		//amount_validation();
	}
	/*function amount_validation(){
		var total_amount = parseFloat(document.getElementById('total_amount').value);
		if(!total_amount){
			total_amount = 0;
		}
		var advance_amount = parseFloat(document.getElementById('advance_amount').value);
		if(!advance_amount){
			advance_amount = 0;
		}
		var advance_amount_paid = parseFloat(document.getElementById('advance_amount_paid').value);
		if(!advance_amount_paid){
			advance_amount_paid = 0;
		}
		if(advance_amount_paid > advance_amount){
			alert('More than advance amount is not permitted...');
			$('#advance_amount_paid').val(advance_amount);
			advance_amount_paid = advance_amount;
		}
		var payable_amount = total_amount - advance_amount_paid;
		if(total_amount > advance_amount_paid){
			$('#payable_amount').val(payable_amount);
		}
		else{
			$('#payable_amount').val(0);
		}
	}*/
</script>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#cindt').datetimepicker({
	step:15,
	format: 'd-m-Y H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['e_id'])){
		echo $row_customer_edit['cindt'];
	}
	else{
		echo date("d-m-Y H:i");	
	}
	?>',
	});
	$('#coutdt').datetimepicker({
	step:15,
	format: 'd-m-Y H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['e_id'])){
		echo $row_customer_edit['coutdt'];
	}
	else{
		echo date("d-m-Y H:i");	
	}
	?>',
	});
</script>