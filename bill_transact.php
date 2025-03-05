<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
	logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
	logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$errorMessage='';
if(isset($_POST['submit']))
{
	if($_POST['bill_sno']!=''){
		$sql='update monthly_bills_transactions set bill_id="'.$_POST['bill_id'].'",amount="'.$_POST['amount'].'", status="'.$_POST['paid'].'" ,bill_date="'.	$_POST['bill_date'].'" ,month_year="'.date("m-Y", strtotime($_POST['bill_date'])).'", mop="'.$_POST['mop'].'" , bank_detail="'.$_POST['bank_detail'].'" , chq_no="'.$_POST['chq_no'].'" , chq_date="'.$_POST['chq_date'].'" , edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'" where sno='.$_POST['bill_sno'];
		$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
	}
	else{
		$sql='insert into monthly_bills_transactions
		(bill_id, amount , status , bill_date ,month_year, mop, bank_detail , chq_no , chq_date , created_by , created_on ,remarks ) VALUES ("'.$_POST['bill_name'].'", "'.$_POST['amount'].'" , "'.$_POST['paid'].'" , "'.$_POST['bill_date'].'" ,"'.date("m-Y", strtotime($_POST['bill_date'])).'", "'.$_POST['mop'].'" , 
		"'.$_POST['bank_detail'].'" , "'.$_POST['chq_no'].'" , "'.$_POST['chq_date'].'" , "'.$_SESSION['username'].'" , CURRENT_TIMESTAMP , 
		"'.$_POST['remarks'].'")';
		$result = execute_query($sql);
		$msg="Bill Paid";
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from monthly_bills_transactions where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
	$sql='select * from monthly_bills where sno='.$row['bill_id'];
	$result = execute_query($sql);
	$bill_details=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql = 'delete from monthly_bills_transactions where sno='.$_GET['del'];
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
			$.getJSON("scripts/ajax.php?id=party_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='party_name']").val(ui.item.label);
			$('#bill_id').val(ui.item.id);
			document.getElementById('bill_name').innerHTML =(ui.item.bill_name);
			$('#amount').val(ui.item.amount);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#party_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>
 <div id="container">
        <h2>Bill Transactions</h2>
		<?php echo $msg; ?>	
		<form action="bill_transact.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
 	<table>
		<tr>
			<td>Party Name</td>
            <td><input id="party_name" name="party_name" class="field text medium" value="<?php echo $bill_details['party_name']?>"   maxlength="255" tabindex="1" type="text" />
			<td>Bill Name</td>
            <td><select id="bill_name" name="bill_name" class="field text medium" tabindex="2">
			<option><?php echo $bill_details['bill_name']?></select>
			</td>
			<td><input type="hidden" name="bill_id" id="bill_id" value="<?php echo $bill_details['sno']?>"></td>
		</tr>
		<tr>
			<td>Amount</td>
			<td><input id="amount" name="amount" type="text" class="field text addr" value="<?php echo $row['amount']?>"  tabindex="3" /></td>
			<td>Bill Date</td>
			<td><script type="text/javascript" language="javascript">
	  			document.writeln(DateInput('bill_date', false, 'YYYY-MM-DD', '<?php if(isset($row['bill_date'])){echo $row['bill_date'];}else{echo date("Y-m-d");} ?>', 4)</script></td>
		</tr>
		<tr>
			<td>Paid</td>
			<td><input type="checkbox" name="paid" tabindex="10" value="paid"<?php if ($row['status']=='paid') echo 'checked="checked"';?>  /></td>
			<td>Mode Of Payment</td>
			<td><select name="mop" id="mop" tabindex="11">
				<option value="cash">Cash</option>
				<option value="cheque">Cheque</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Bank Details</td>
            <td><input id="bank_detail" name="bank_detail" class="field text medium" value="<?php echo $row['bank_details']?>"   maxlength="255" tabindex="12" type="text" /></td>
			<td>Cheque No.</td>
            <td><input id="chq_no" name="chq_no" class="field text medium" value="<?php echo $row['chq_no']?>"   maxlength="255" tabindex="13" type="text" /></td>
		</tr>
		<tr>
			<td>Cheque Date</td>
			<td><script type="text/javascript" language="javascript">
	  			document.writeln(DateInput('chq_date', false, 'YYYY-MM-DD', '<?php if(isset($row['chq_date'])){echo $row['chq_date'];}else{echo date("Y-m-d");} ?>', 14)</script></td>
            </td
		></tr>
		<tr>
            <td>Remarks</td>
            <td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="18" type="text" />
		</tr>
		<tr>
			<td><input type="hidden" name="bill_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
            <input id="submit" name="submit" class="btTxt submit" type="submit" value="Pay Bill" onMouseDown="" tabindex="20"></td>
		</tr>
	</table>
	</form>
	<table width="100%">
		<tr style="background:#000; color:#FFF;">
			<th>S.No.</th>
			<th>Party Name</th>
			<th>Bill Name</th>
			<th>Amount</th>
			<th>Bill Date</th>
			<th>Mode Of Payment</th>
            <th>Edit</th>
			<th>Delete</th>
			</tr>
    <?php
			$sql = 'select monthly_bills.party_name, monthly_bills.bill_name, monthly_bills_transactions.amount, monthly_bills_transactions.bill_date,monthly_bills_transactions.mop,monthly_bills_transactions.sno from monthly_bills join monthly_bills_transactions on monthly_bills.sno = monthly_bills_transactions.bill_id';
			$result=mysqli_fetch_assoc(execute_query($sql));
	$i=1;
	foreach($result as $row)
	{
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
		echo '<tr style="background:'.$col.'">
				<td>'.$i++.'</td>
				<td>'.$row['party_name'].'</td>
				<td>'.$row['bill_name'].'</td>
				<td>'.$row['amount'].'</td>
				<td>'.$row['bill_date'].'</td>
				<td>'.$row['mop'].'</td>
				<td><a href="bill_transact.php?id='.$row['sno'].'">Edit</a></td>
				<td><a href="bill_transact.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
			</tr>';
	}
?>
</table>
</div>
<?php
page_footer();
?>
