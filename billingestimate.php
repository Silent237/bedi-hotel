<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
$msg = '';
$tabindex=1;
$response=1;
navigation('');		
$sql = 'select * from dummy_room order by sno desc limit 1';
$serial = mysqli_fetch_array(execute_query($sql));
//print_r($serial);
if($serial['invoice_number']>=9999){
	
	$invoice= $serial['invoice_number']+1;
}
else{
	$invoice = 1;
}
if(isset($_POST['submit'])) {
	//print_r($_POST);
	if($_POST['guest_name']=='') {
		$msg .= '<li>Please Enter Guest Name.</li>';
	}
	if($msg==''){
		if ($_POST['edit_sno']!='') {
				$sql = 'update billing_estimate set 
				`guest_name` =  "'.$_POST['guest_name'].'",
				`address` =  "'.$_POST['address'].'",
				`contact_number` =  "'.$_POST['contact_number'].'",
				`booking_date` =  "'.$_POST['booking_date'].'",
				`total_amount` =  "'.$_POST['total_amount'].'",
				`advance_amount` =  "'.$_POST['advance_amount'].'",
				`mop`="'.$_POST['mop'].'",
				`particular`="'.$_POST['particular'].'"
				where sno='.$_POST['edit_sno'];
				//echo $sql;
				execute_query($sql);
				if(mysqli_error($db)){
					$msg .= '<li>Error UP-01 : '.mysqli_error($db).' >> '.$sql.'</li>';
				}
				else{
					$msg .= '<li>Update Successfully</li>';
				}
				
		}else{
			
				$sql = 'insert into billing_estimate (invoice_number, guest_name, address, contact_number, booking_date, total_amount, advance_amount, mop, particular, created_on) values("'.$invoice.'", "'.$_POST['guest_name'].'", "'.$_POST['address'].'", "'.$_POST['contact_number'].'", "'.$_POST['booking_date'].'", "'.$_POST['total_amount'].'", "'.$_POST['advance_amount'].'", "'.$_POST['mop'].'", "'.$_POST['particular'].'", CURRENT_TIMESTAMP)';
				//echo $sql.'</br>';
				execute_query($sql);
				$sno = mysqli_insert_id($db);
					if(mysqli_error($db)){
						$msg .= '<li>Error # 1 : '.$sql.'</li>';
						
					}
					else{
						$msg .= '<li>New Data Inserted</li>';
						$msg .= '<li><a href="print_billingestimate.php?id='.$sno.'" >Click Here For Print</a></li>';
					}
				}
	}		
}

if (isset($_GET['edit_id'])) {
	$sql_edit = 'SELECT * FROM `billing_estimate` WHERE `sno`="'.$_GET['edit_id'].'"';
	$result_edit = execute_query($sql_edit);
	$row_edit = mysqli_fetch_array($result_edit);
}

page_header();

			switch($response){
				case 1:{
?>
     <div id="container">
			<div id="content">
				<h2>Estimate Billing</h2>
				<?php echo '<ul><h3>'.$msg.'</h3></ul>'; ?>
				<form name="main_form" method="POST" action="billingestimate.php" enctype="multipart/formdata">
					<table width="100%">
						
						<tr>
							<td>Guest Name :</td>
							<td><input type="text" name="guest_name" id="guest_name" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['guest_name'];} ?>"></td>
							
							
							<td>Address : </td>
							<td><input type="text" name="address" id="address" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['address'];} ?>" ></td>
						</tr>
						<tr>
							<td>Contact Number : </td>
							<td><input type="text" name="contact_number" id="contact_number" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['contact_number'];} ?>" ></td>
							
							<td>Booking/Event Date</td>
							<td>
    <input type="date" name="booking_date" id="booking_date" 
        value="<?php 
            if (isset($_GET['edit_id'])) {
                echo $row_edit['booking_date'];
            } else {
                echo date('Y-m-d'); // Set current date
            }
        ?>" 
        tabindex="<?php echo $tabindex++; ?>">
</td>

							
						</tr>
						
						<tr>
							<td>Total Amount : </td>
							<td><input type="text" name="total_amount" id="total_amount" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['total_amount'];} ?>" ></td>
								
							
							<td>Advance Amount : </td>
							<td><input type="text" name="advance_amount" id="advance_amount" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['advance_amount'];} ?>" ></td>
							
								
						</tr>
						<tr>
							<td>Mode of Payment</td>
							<td>
								<select id="mop" name="mop" class="field select medium" tabindex="<?php echo $tabindex++;?>">
										<option value="cash" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'cash'){ ?> selected="selected"<?php }} ?>>Cash</option>
										<option value="card" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'card'){ ?> selected="selected"<?php }} ?>>Card</option>
										<option value="other" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'other'){ ?> selected="selected"<?php }} ?>>Other</option>
										<option value="bank_transfer" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'bank_transfer'){ ?> selected="selected"<?php }} ?>>Bank Transfer</option>
										<option value="cheque" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'cheque'){ ?> selected="selected"<?php }} ?>>Cheque</option>
										<option value="paytm" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'paytm'){ ?> selected="selected"<?php }} ?>>Paytm</option>
										<option value="card_sbi" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'card_sbi'){ ?> selected="selected"<?php }} ?>>Card S.B.I</option>
										<option value="card_pnb" <?php if(isset($_GET['edit_id'])){if($row_edit['mop'] == 'cheque'){ ?> selected="selected"<?php }} ?>>Card P.N.B.</option>
								</select>
							</td>
							<td>Particular : </td>
							<td><textarea name="particular" id="particular" maxlength="550" tabindex="<?php echo $tabindex++; ?>"  style="width:200px; padding: 2px; margin: 2px; border-radius: 4px; border: 1px solid #66AAAA;" ><?php if(isset($_GET['edit_id'])){echo $row_edit['particular'];} ?></textarea></td>
						</tr>
						<tr>
							<td colspan="11"><input class="large" type="submit" name="submit" value="Submit" tabindex="<?php echo $tabindex++; ?>"></td>
							<input type="hidden" name="edit_sno" value="<?php if(isset($_GET['edit_id'])){echo $_GET['edit_id'];} ?>">
							
						</tr>
					</table>
				
					
				</form>
			</div>
			</div>


			
<?php
						
						
					break;
				}
			case 2:{
?>
			<div id="content">
				<h2>&nbsp;</h2>
				<h2><input type="button" name="print_button" onclick="loadOtherPage();" value="Print Receipt"></h2>
			</div>
<script type="text/javascript">
function loadOtherPage() {
    $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "print_dummy_report.php?id=<?php echo $sno;?>") // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page
}
</script>

				
					
<?php				
				
					break;
				}
			}
	
page_footer();
?>