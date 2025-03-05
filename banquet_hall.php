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
$con = $db;
$ban_id = '';
if(isset($_GET['allot'])){
	$sql = 'select * from advance_booking where sno='.$_GET['allot'];

	$result= execute_query($sql);
	$row_allot= mysqli_fetch_assoc($result);
	
	$sql='select * from customer where sno='.$row_allot['cust_id'];
	
	$result= execute_query($sql);
	$customer_allot= mysqli_fetch_assoc($result);
	
	$sql_advance_amount = 'SELECT SUM(`amount`) as advance_amount FROM `customer_transactions` WHERE `cust_id`="'.$row_allot['cust_id'].'" AND `type`="ADVANCE_AMT" AND `payment_for`="banquet_rent"';
	$row_advance_amount = mysqli_fetch_array(execute_query($sql_advance_amount));
	$sql_advance_paid = 'SELECT SUM(`amount`) as advance_paid FROM `customer_transactions` WHERE `cust_id`="'.$row_allot['cust_id'].'" AND `type`="ADVANCE_PAID" AND `payment_for`="banquet_rent"';
	$row_advance_paid = mysqli_fetch_array(execute_query($sql_advance_paid));
	$advance_amount = $row_advance_amount['advance_amount'] - $row_advance_paid['advance_paid'];
}
if (isset($_GET['e_id'])) {
	$sql_banquet_edit = 'SELECT * FROM `banquet_hall` WHERE `sno`="'.$_GET['e_id'].'"';
	$result_banquet_edit = execute_query($sql_banquet_edit);
	$row_banquet_edit = mysqli_fetch_array($result_banquet_edit);
	$sql_particular_edit = 'SELECT * FROM `banquet_particular` WHERE `banquet_id`="'.$_GET['e_id'].'"';
	$result_particular_edit = execute_query($sql_particular_edit);
	$num_particular_edit = mysqli_num_rows($result_particular_edit);
	$sql_customer_edit = 'SELECT * FROM `customer` WHERE `sno`="'.$row_banquet_edit['cust_id'].'"';
	$result_customer_edit = execute_query($sql_customer_edit);
	$row_customer_edit = mysqli_fetch_array($result_customer_edit);
}
if(isset($_POST['submit'])){
	if($_POST['cust_name1']==''  AND $_POST['company_name'] == ''){
		$msg .= '<li class="error">Enter Bill To Details.</li>';
	}
	if($msg==''){
		if($_POST['cust_sno']==''){
			$sql= 'INSERT INTO customer (company_name, cust_name, mobile, id_1, id_2, address, created_by, created_on , state) VALUES ("'.$_POST['company_name'].'", "'.$_POST['cust_name1'].'", "'.$_POST['mobile'].'", "'.$_POST['id_1'].'", "'.$_POST['id_2'].'", "'.$_POST['address'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "UTTAR PRADESH")';
			$result = execute_query($sql);
			$msg .= '<li class="error">Customer Added successfully</li>';
			$_POST['cust_sno'] = $con->insert_id;		
		}
		if($_POST['cust_sno']!=''){
			$sql='update customer set 
			cust_name = "'.$_POST['cust_name1'].'" ,
			company_name = "'.$_POST['company_name'].'"
			where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
		}
		if($_POST['cust_sno']!=''){
			if($_POST['edit_sno'] != ''){
				$sql_update = 'UPDATE `banquet_hall` SET 
							`cust_id`="'.$_POST['cust_sno'].'",
							`hall_type`="'.$_POST['hall_type'].'",
							`check_in_date`="'.$_POST['check_in_date'].'",
							`booking_date`="'.$_POST['booking_date'].'",
							`remarks`="'.$_POST['remarks'].'",
							`edited_by`="'.$_SESSION['username'].'",
							`edited_on`="'.date('Y-m-d H:i').'",
							`total_quantity`="'.$_POST['quantity'].'",
							`amount`="'.$_POST['amount'].'",
							`cgst`="'.$_POST['cgst'].'",
							`sgst`="'.$_POST['sgst'].'",
							`grand_total`="'.$_POST['grand_total'].'",
							`mop`="'.$_POST['mop'].'" 
							WHERE `sno`="'.$_POST['edit_sno'].'"';
							
				$res = execute_query($sql_update);
				if ($res) {
					$msg .= '<li class="error">Data Updated &nbsp; <a href="print_ban.php?id='.$_POST['edit_sno'].'" target="_blank">Print</a></li>';
					$sql = 'delete from banquet_particular where banquet_id='.$_POST['edit_sno'];
					//echo $sql;
					execute_query($sql);
					for ($i=1; $i <= $_POST['insert_id']; $i++) { 
						if($_POST['particular_'.$i] != ''){
							if($_POST['particular_sno_'.$i] != ''){
								
								
								$sql_particular_insert = 'INSERT INTO `banquet_particular`(`banquet_id`, `particular`, `rate`, `quantity`, `amount`, `cgst`, `sgst`, `grand_total`) VALUES ("'.$_POST['edit_sno'].'" , "'.$_POST['particular_'.$i].'" , "'.$_POST['rate_'.$i].'" , "'.$_POST['quantity_'.$i].'" , "'.$_POST['amount_'.$i].'" , "'.$_POST['cgst_'.$i].'" , "'.$_POST['sgst_'.$i].'" , "'.$_POST['grand_total_'.$i].'")';
								//echo $sql_particular_insert;
								execute_query($sql_particular_insert);
								
							}
							else{
								$sql_particular_insert = 'INSERT INTO `banquet_particular`(`banquet_id`, `particular`, `rate`, `quantity`, `amount`, `cgst`, `sgst`, `grand_total`) VALUES ("'.$_POST['edit_sno'].'" , "'.$_POST['particular_'.$i].'" , "'.$_POST['rate_'.$i].'" , "'.$_POST['quantity_'.$i].'" , "'.$_POST['amount_'.$i].'" , "'.$_POST['cgst_'.$i].'" , "'.$_POST['sgst_'.$i].'" , "'.$_POST['grand_total_'.$i].'")';
								execute_query($sql_particular_insert);
							}
						}
					}
				}
				$sql_trans_update = 'UPDATE `customer_transactions` SET `amount`="'.$_POST['grand_total'].'" , `mop`="'.$_POST['mop'].'" WHERE `allotment_id`="'.$_POST['edit_sno'].'" AND `type`="BAN_AMT"';
				execute_query($sql_trans_update);
			}
			else{
				$date = date('Y-m-d H:i');
				if ($date == '') {
					$date=$_POST['booking_date'];
				}
				$time = strtotime($date);
				$month = date("m",$time);
				$year = date("Y",$time);
				if($month>=1 && $month<=3){
					$year = $year-1;
				}
				$n_year = $year+1;
				$year = $year.'-'.$n_year;
				$sql_invoice = 'SELECT * FROM `banquet_hall` WHERE `financial_year`="'.$year.'" ORDER BY abs(`invoice_no`) DESC LIMIT 1';
				$result_invoice = execute_query($sql_invoice);
				$row_invoice = mysqli_fetch_array($result_invoice);
				$invoice_no = $row_invoice['invoice_no']+1;
				$sql ='INSERT INTO `banquet_hall`(`cust_id`, `invoice_no`, `hall_type`, `check_in_date`, `booking_date` , `remarks`, `created_by`, `created_on`, `amount`, `cgst`, `sgst`, `grand_total`, `mop` , `financial_year` , `total_quantity` , `advance_booking_id`) VALUES ("'.$_POST['cust_sno'].'" , "'.$invoice_no.'" , "'.$_POST['hall_type'].'" , "'.$_POST['check_in_date'].'" , "'.$_POST['booking_date'].'" , "'.$_POST['remarks'].'" , "'.$_SESSION['username'].'" , "'.date('Y-m-d H:i').'" , "'.$_POST['amount'].'" , "'.$_POST['cgst'].'" , "'.$_POST['sgst'].'" , "'.$_POST['grand_total'].'" , "'.$_POST['mop'].'" , "'.$year.'" , "'.$_POST['quantity'].'" , "'.$_POST['allot_sno'].'")';
				$res = execute_query($sql);
				if($res){
					$ban_id =mysqli_insert_id($db);
					// $ban_id = $con->lastInsertId();
					$sql_trans = 'INSERT INTO customer_transactions (cust_id, allotment_id , type , timestamp, amount, mop, created_by , created_on , remarks,invoice_no,financial_year ,advance_set_amt) VALUES ("'.$_POST['cust_sno'].'", "'.$ban_id.'", "BAN_AMT" , "'.date('Y-m-d').'"  , "'.$_POST['grand_total'].'" , "'.$_POST['mop'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'","'.$invoice_no.'","2020-2021","'.$_POST['advance_amount_paid'].'")';
					execute_query($sql_trans);
					$msg .= '<li class="error">Data Inserted &nbsp; <a href="print_ban.php?id='.$ban_id.'" target="_blank">Print</a></li>';
					if($_POST['advance_amount_paid'] > 0){
				    	$sql_inv = 'select * from customer_transactions order by abs(sno) desc limit 1';
						$inv_result = execute_query($sql_inv);
						$inv_no = mysqli_fetch_array($inv_result);
				    	$set_sno = $inv_no['sno'];
						$sql='INSERT INTO customer_transactions (cust_id , type , timestamp, amount, mop, created_by , created_on , remarks , invoice_no , financial_year , payment_for,set_sno) VALUES ("'.$_POST['cust_sno'].'", "ADVANCE_PAID" , "'.date('Y-m-d').'"  , "'.$_POST['advance_amount_paid'].'" , "cash", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'","","'.$year.'" , "banquet_rent" , "'.$set_sno.'")';
						$result = execute_query($sql);
						$sql_inv = 'select * from customer_transactions order by abs(sno) desc limit 1';
						$inv_result = execute_query($sql_inv);
						$inv_no = mysqli_fetch_array($inv_result);
						$inv = $inv_no['sno'];
						$sql_update = 'UPDATE `customer_transactions` SET `credit_bill_paid_sno`="'.$inv.'" WHERE `sno`="'.$set_sno.'"';
						execute_query($sql_update);
						//echo $sql_update;
					}
				}
				if($ban_id != ''){
					for ($i=1; $i <= $_POST['insert_id']; $i++) { 
						if($_POST['particular_'.$i] != ''){
							$sql_particular = 'INSERT INTO `banquet_particular`(`banquet_id`, `particular`, `rate`, `quantity`, `amount`, `cgst`, `sgst`, `grand_total`) VALUES ("'.$ban_id.'" , "'.$_POST['particular_'.$i].'" , "'.$_POST['rate_'.$i].'" , "'.$_POST['quantity_'.$i].'" , "'.$_POST['amount_'.$i].'" , "'.$_POST['cgst_'.$i].'" , "'.$_POST['sgst_'.$i].'" , "'.$_POST['grand_total_'.$i].'")';
							execute_query($sql_particular);
						}
					}
				}
				if($_POST['allot_sno'] != ''){
			    	$sql='update advance_booking set status="1" where sno="'.$_POST['allot_sno'].'" or advance_for_id="'.$_POST['allot_sno'].'"';
					$result = execute_query($sql);
			    }
			}
		}
	}
}		
?>
<style>
.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>
<script src="js/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" language="javascript">	
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name_banquet",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$('#cust_name1').val(ui.item.cust_name);
			$('#mobile').val(ui.item.mobile);
			$('#company_name').val(ui.item.company);
			$('#address').val(ui.item.address);
			$('#id_1').val(ui.item.id_no);
			$('#id_2').val(ui.item.gst_no);
			$('#advance_amount').val(ui.item.advance_amount);
			$('#advance_amount_paid').val(0);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name1").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});


$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=company_name_banquet",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			$('#company_name').val(ui.item.company);
			$('#address').val(ui.item.address);
			$('#id_1').val(ui.item.id_no);
			$('#id_2').val(ui.item.gst_no);
			$('#advance_amount').val(ui.item.advance_amount);
			$('#advance_amount_paid').val(0);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#company_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
	
</script>
<div id="container">
        <h2>Banquet Hall Entry</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="banquet_hall.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Guest Name : </td><td><input id="cust_name1" name="cust_name1" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['cust_name'];}elseif(isset($_GET['allot'])){echo $customer_allot['cust_name'];} ?>"> &nbsp; <a href="admin_customers.php?id=<?php echo $row_customer_edit['sno']; ?>">Edit</a></td>
					<td>Mobile : </td>
					<td><input id="mobile" name="mobile" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['mobile'];}elseif(isset($_GET['allot'])){echo $customer_allot['mobile'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['sno'];}elseif(isset($_GET['allot'])){echo $customer_allot['sno'];} ?>" />
				<tr>
					<td>Company Name : </td>
					<td><input id="company_name" name="company_name" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['company_name'];}elseif(isset($_GET['allot'])){echo $customer_allot['company_name'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>	
					<td>SAC/HSN : </td>
					<td><input id="id_1" name="id_1" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['id_1'];}elseif(isset($_GET['allot'])){echo $customer_allot['id_1'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<tr>
					<td>Address : </td>
					<td><input id="address" name="address" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['address'];}elseif(isset($_GET['allot'])){echo $customer_allot['address'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>GSTIN : </td>
					<td><input id="id_2" name="id_2" value="<?php if(isset($_GET['e_id'])){echo $row_customer_edit['id_2'];}elseif(isset($_GET['allot'])){echo $customer_allot['id_2'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<tr>
					<td>Hall Type : </td>
					<td>
						<select name="hall_type" tabindex="<?php echo $tab++;?>">
							<option value="">Select</option>
							<?php
								$sql="SELECT * FROM admin_hall_type";
								$res=execute_query($sql);
								while($plan=mysqli_fetch_array($res)){
									?>
									<option value="<?php echo $plan['hall_type']; ?>" <?php if(isset($_GET['e_id'])){if($row_banquet_edit['hall_type'] == $plan['hall_type']){echo 'selected';}} ?>><?php echo $plan['hall_type']; ?></option>
									<?php
								}
							?>
						</select>
					</td>
					<td>Remarks : </td>
					<td>
						<input id="remarks" name="remarks" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['remarks'];} ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
				</tr>
				<tr>
					<td>Booking Date : </td>
					<td><input name="booking_date" type="text" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['booking_date'];}elseif(isset($_GET['allot'])){echo $row_allot['booking_date'];} ?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="booking_date" /></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td>Event Date : </td>
					<td><input name="check_in_date" type="text" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['check_in_date'];}elseif(isset($_GET['allot'])){echo $row_allot['allotment_date'];} ?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="check_in_date" required/></td>
					<td>Total Amount : </td>
					<td><input type="text" name="total_amount" id="total_amount" readonly></td>
				</tr>
				<tr>
					<td>Advance Amount : </td>
					<td><input type="<?php if(isset($_GET['e_id'])){echo 'hidden';}else{echo 'text';} ?>" name="advance_amount" id="advance_amount" readonly value="<?php if(isset($_GET['allot'])){echo $advance_amount;}else{echo 0;} ?>"></td>
					<td>Advance Amount Paid : </td>
					<td><input type="<?php if(isset($_GET['e_id'])){echo 'hidden';}else{echo 'text';} ?>" name="advance_amount_paid" id="advance_amount_paid" onblur="amount_validation();" value="0"></td>
				</tr>	
				<tr>
					<td>MOP : </td>
					<td>
						<select name="mop" tabindex="<?php echo $tab++;?>" tabindex="<?php echo $tab++;?>">
							<option value="cash" <?php if(isset($_GET['e_id'])){if($row_banquet_edit['mop'] == 'cash'){echo 'selected';}} ?>>cash</option>
							<option value="card" <?php if(isset($_GET['e_id'])){if($row_banquet_edit['mop'] == 'card'){echo 'selected';}} ?>>card</option>
							<option value="credit" <?php if(isset($_GET['e_id'])){if($row_banquet_edit['mop'] == 'credit'){echo 'selected';}} ?>>credit</option>
						</select>
					</td>
					<td>Payable Amount : </td>
					<td><input type="text" name="payable_amount" id="payable_amount"></td>
				</tr>		
			</table>
		<table width="100%">
			<tr style="background:#000; color:#FFF;">
				<th rowspan="2">S.No.</th>
				<th rowspan="2">Particulars</th>
				<th rowspan="2">Rate</th>
				<th rowspan="2">Quantity</th>
				<th rowspan="2">Amount</th>
				<th colspan="2">GST</th>
				<th rowspan="2">Total</th>
			</tr>	
			<tr style="background:#000; color:#FFF;">
				<th>SGST 9 %</th>
				<th>CGST 9 %</th>
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
			<input type="hidden" name="insert_id" id="insert_id" value="<?php if(isset($_GET['e_id'])){echo $num_particular_edit+1;}else{ echo $i;} ?>">
			<?php 
			if(isset($_GET['e_id'])){
				while($row_particular_edit = mysqli_fetch_array($result_particular_edit)){
			?>
			<tr style="background: <?php echo $col; ?>;text-align: center;" width="100%">
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<input type="text" name="particular_<?php echo $i; ?>" id="particular_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" value="<?php echo $row_particular_edit['particular']; ?>">
					<input type="hidden" name="particular_sno_<?php echo $i; ?>" value="<?php echo $row_particular_edit['sno']; ?>">
				</td>
				<td>
					<input type="text" name="rate_<?php echo $i; ?>" id="rate_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" value="<?php echo $row_particular_edit['rate']; ?>" onblur="insert_row();">
				</td>
				<td>
					<input type="text" name="quantity_<?php echo $i; ?>" id="quantity_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" onblur="insert_row();" value="<?php echo $row_particular_edit['quantity']; ?>">
				</td>
				<td>
					<input type="text" name="amount_<?php echo $i; ?>" id="amount_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly value="<?php echo $row_particular_edit['amount']; ?>">
				</td>
				<td>
					<input type="text" name="sgst_<?php echo $i; ?>" id="sgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly value="<?php echo $row_particular_edit['sgst']; ?>">
				</td>
				<td>
					<input type="text" name="cgst_<?php echo $i; ?>" id="cgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly value="<?php echo $row_particular_edit['cgst']; ?>">
				</td>
				<td>
					<input type="text" name="grand_total_<?php echo $i; ?>" id="grand_total_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly value="<?php echo $row_particular_edit['grand_total']; ?>">
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
					<input type="text" name="amount_<?php echo $i; ?>" id="amount_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly>
				</td>
				<td>
					<input type="text" name="sgst_<?php echo $i; ?>" id="sgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly>
				</td>
				<td>
					<input type="text" name="cgst_<?php echo $i; ?>" id="cgst_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly>
				</td>
				<td>
					<input type="text" name="grand_total_<?php echo $i; ?>" id="grand_total_<?php echo $i; ?>" class="field text medium" tabindex="<?php echo $tab++;?>" style="width:100%;" readonly>
				</td>
			</tr>
			<tr id="total">
				<th colspan="3">Total</th>
				<th><input type="text" name="quantity" id="quantity" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['total_quantity'];} ?>"></th>
				<th><input type="text" name="amount" id="amount" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['amount'];} ?>"></th>
				<th><input type="text" name="sgst" id="sgst" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['sgst'];} ?>"></th>
				<th><input type="text" name="cgst" id="cgst" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['cgst'];} ?>"></th>
				<th><input type="text" name="grand_total" id="grand_total" readonly style="width:100%;" value="<?php if(isset($_GET['e_id'])){echo $row_banquet_edit['grand_total'];} ?>"></th>
			</tr>
			<tr>
				<td colspan="8">
					<input type="hidden" name="allot_sno" id="allot_sno" value="<?php if(isset($_GET['allot'])){echo $_GET['allot'];}?>" />
				<input type="hidden" name="edit_sno" value="<?php if(isset($_GET['e_id'])){echo $_GET['e_id'];} ?>">
				<input id="submit" name="submit" class="btTxt submit large" type="submit" value="Submit" onMouseDown="" ></td>
			</tr>
		</table>
	</form>
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#check_in_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
			
	?>',
	});
$('#booking_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
			
	?>',
	});
$('#check_out_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
			
	?>',
	});
</script>
<?php
navigation('');
page_footer();
?>
<script type="text/javascript">
	function insert_row(){
		var total_quantity = 0;
		var total_amount = 0;
		var total_sgst = 0;
		var total_cgst = 0;
		var total_grand_total = 0; 
		var insert = 0;
		var i = document.getElementById('insert_id').value;
		var advance_amount = parseFloat(document.getElementById('advance_amount').value);
		for (var k = 1; k <= i; k++) {
			var particular = document.getElementById('particular_'+k).value;
			if(particular == ''){
				insert = insert+1;
			}
			else{
				var rate = parseFloat(document.getElementById('rate_'+k).value);
				var quantity = parseFloat(document.getElementById('quantity_'+k).value);
				if(!rate){
					rate = 0;
				}
				if(!quantity){
					quantity = 0;
				}
				var amount = rate * quantity;
				var sgst = (amount * 9)/100;
				var cgst = (amount * 9)/100;
				var grand_total = amount + sgst + cgst;
				$('#amount_'+k).val(amount.toFixed(3));
				$('#sgst_'+k).val(sgst.toFixed(3));
				$('#cgst_'+k).val(cgst.toFixed(3));
				$('#grand_total_'+k).val(grand_total.toFixed(3));
				total_quantity += quantity;
				total_amount += amount;
				total_sgst += sgst;
				total_cgst += cgst;
				total_grand_total += grand_total;  
			}
		}
		$("#quantity").val(total_quantity.toFixed(3));
		$("#amount").val(total_amount.toFixed(3));
		$("#sgst").val(total_sgst.toFixed(3));
		$("#cgst").val(total_cgst.toFixed(3));
		$("#grand_total").val(total_grand_total.toFixed(3));
		$("#total_amount").val(total_grand_total.toFixed(3));
		if (advance_amount > total_grand_total) {
			$("#advance_amount_paid").val(total_grand_total.toFixed(3));
		}
		else{
			$("#advance_amount_paid").val(advance_amount.toFixed(3));
		}
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
			var txt = '<tr style="background:'+col+'"><td>'+i+'</td><td><input type="text" name="particular_'+i+'" id="particular_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;"><input type="hidden" name="particular_sno_'+i+'"></td><td><input type="text" name="rate_'+i+'" id="rate_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();"></td><td><input type="text" name="quantity_'+i+'" id="quantity_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" onblur="insert_row();"></td><td><input type="text" name="amount_'+i+'" id="amount_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" readonly></td><td><input type="text" name="sgst_'+i+'" id="sgst_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" readonly></td><td><input type="text" name="cgst_'+i+'" id="cgst_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" readonly></td><td><input type="text" name="grand_total_'+i+'" id="grand_total_'+i+'" class="field text medium" tabindex="'+tab+++'" style="width:100%;" readonly></td></tr>';
			$(txt).insertBefore("#total");
			$("#insert_id").val(i);
		}
		amount_validation();
	}
	function amount_validation(){
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
	}
</script>