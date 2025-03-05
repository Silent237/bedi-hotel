<?php
include("scripts/settings.php");
page_header();
$msg='';

if(isset($_POST['submit'])) {
	if($_POST['rate_sno']=='') {
		$msg .= '<li>Please Enter Details.</li>';
	}
	if($msg==''){
		$sql = 'update general_settings set rate="'.$_POST['rate'].'" where sno='.$_POST['rate_sno'].' and `desc` in ("slogan", "address", "contact", "terms", "bank", "jurisdiction", "dealer", "result_per_page", "invoice_prefix", "firm_type", "bill_style")';
		execute_query($sql);
		//echo $sql;
		if(mysqli_error($db)){
			$msg .= '<li>Error # 2 : '.$sql.'</li>';
		}
		else{
			$msg .= '<li>Update sucessful.</li>';
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from general_settings where sno='.$_GET['id'].' and `desc` in ("slogan", "address", "contact", "terms", "bank", "jurisdiction", "dealer", "result_per_page", "invoice_prefix", "firm_type", "bill_style")';
	$res = execute_query($sql);
	$edit = mysqli_fetch_array($res);

}

$sql_slogan = 'select * from general_settings where `desc`="slogan"';
$slogan = mysqli_fetch_array(execute_query($sql_slogan));

$sql_address = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_array(execute_query($sql_address));

$sql_contact = 'select * from general_settings where `desc`="contact"';
$contact = mysqli_fetch_array(execute_query($sql_contact));

$sql_terms = 'select * from general_settings where `desc`="terms"';
$terms = mysqli_fetch_array(execute_query($sql_terms));

$sql_bank = 'select * from general_settings where `desc`="bank"';
$bank = mysqli_fetch_array(execute_query($sql_bank));

$sql_juridiction = 'select * from general_settings where `desc`="jurisdiction"';
$jurisdiction = mysqli_fetch_array(execute_query($sql_juridiction));

$sql_dealer = 'select * from general_settings where `desc`="Print Table No On Bill"';
$dealer = mysqli_fetch_array(execute_query($sql_dealer));

$sql_result = 'select * from general_settings where `desc`="result_per_page"';
$result = mysqli_fetch_array(execute_query($sql_result));

$sql_invoice = 'select * from general_settings where `desc`="invoice_prefix"';
$invoice = mysqli_fetch_array(execute_query($sql_invoice));

$sql_firm = 'select * from general_settings where `desc`="firm_type"';
$firm = mysqli_fetch_array(execute_query($sql_firm));

$sql_bill = 'select * from general_settings where `desc`="bill_style"';
$bill = mysqli_fetch_array(execute_query($sql_bill));

?>


<?php 

?>
    <div id="container">
    <h2>DETAILS</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
			<form action="general_settings.php" class="addtype" name="addtype" enctype="multipart/form-data" method="post" onSubmit="" >
            	<table width="100%">
                	<tr>
						<td>&nbsp;</td>
                    	<td>Discription :</td>
                        <td><input type="text" name="rate" id="rate" tabindex="1" class="input" value="<?php if(isset($_GET['id'])){echo $edit['rate'];}?>" onBlur="hide_show('type','1')" onKeyUp="formvalidation(this.value,'varchar','100','rate')"/>
						<input type="hidden" name="rate_sno" id="rate_sno" tabindex="1" class="input" value="<?php if(isset($_GET['id'])){echo $edit['sno'];}?>" /></td>
                        <td><input type="submit" class="submit1" tabindex="2" name="submit" value="Add" onClick="return confirmSubmit()"/>
                        </td>
                    </tr>
				</table>
			</form>
		</div>	
<table width="100%">
	<tr>
    	<th>S.No.</th>
        <th>Name</th>
		<th>Discription</th>
        <th>Edit</th>
	</tr>
	<tr>
		<td>1</td>
		<td>Slogan</td>
		<td><?php echo $slogan['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $slogan['sno'] ;?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>2</td>
		<td>Address</td>
		<td><?php echo $address['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $address['sno'] ;?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>3</td>
		<td>Contact</td>
		<td><?php echo $contact['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $contact['sno'];?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>4</td>
		<td>Terms</td>
		<td><?php echo $terms['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $terms['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>5</td>
		<td>Bank</td>
		<td><?php echo $bank['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $bank['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>6</td>
		<td>jurisdiction</td>
		<td><?php echo $jurisdiction['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $jurisdiction['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>7</td>
		<td>Print Table No On Bill</td>
		<td><?php echo $dealer['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $dealer['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>8</td>
		<td>Result Per Page</td>
		<td><?php echo $result['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $result['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>9</td>
		<td>Invoice Prefix</td>
		<td><?php echo $invoice['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $invoice['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>10</td>
		<td>Firm Type</td>
		<td><?php echo $firm['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $firm['sno']; ?>">Edit</a></td>
		
	</tr>
	<tr>
		<td>11</td>
		<td>Bill Style</td>
		<td><?php echo $bill['rate']; ?></td>
		<td><a href="general_settings.php?id=<?php echo $bill['sno']; ?>">Edit</a></td>
		
	</tr>
	
	
	
</table>
</div>
<?php
page_footer();
?>