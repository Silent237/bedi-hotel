<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
$tabindex=1;
$response=1;
page_header();
navigation('');	

			switch($response){
				case 1:{
?>
	<div id="container">
			<div id="content">
				<h2>Estimate Report</h2>
				<form name="main_form" method="POST" action="estimate_report.php" enctype="multipart/formdata">
					<table width="100%">
						<tr>
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
						
							
							
						</tr>
						<tr>
							<td>Guest Name :</td>
							<td><input type="text" name="guest_name" tabindex="<?php echo $tabindex++; ?>">
							</td>
							<td>Mobile Number:</td>
							<td><input type="text" name="contact_number" tabindex="<?php echo $tabindex++; ?>"></td>
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
						</tr>
						
						<tr>
							<td colspan="6" style="text-align: center;"><input class="large" type="submit" name="submit" value="Search" tabindex="<?php echo $tabindex++; ?>"></td>
						</tr>
					
					</table>			
				</form>
				<table>
					<tr>
						<th>S.No.</th>
						<th>Booking/Event Date</th>
						<th>Guest Name</th>
						<th>Address</th>
						<th>Contact Number</th>
						<th>Total Amount</th>
						<th>Advance Amount</th>
						<th>Mode of Payment</th>
						<th>Particular</th>
						<th>&nbsp;</th>
						<th> &nbsp;</th>
					</tr>
					<?php
					$tot_taxable_amount=0;
					$grand_total=0;
					$sql = 'select * from billing_estimate where 1=1';
					if(isset($_POST['submit'])){
						if($_POST['date_type'] == 'booking_wise'){
						$sql .= ' and created_on>="'.$_POST['allot_from'].'" and created_on<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
					else if($_POST['date_type'] == 'allotment_wise'){
						$sql .= ' and booking_date>="'.$_POST['allot_from'].'" and booking_date<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					}
					else{
						$sql .= ' and created_on>="'.date("Y-m-d").'" and created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
					}
						
						if($_POST['guest_name']!=''){
							$sql .= ' and guest_name = "'.$_POST['guest_name'].'"';
						}
						if($_POST['contact_number']!=''){
							$sql .= ' and contact_number = "'.$_POST['contact_number'].'"';
						}
						if($_POST['mop']!=''){
							$sql .= ' and mop = "'.$_POST['mop'].'"';
						}
						if($_POST['booking_date']!=''){
							$sql .= ' and booking_date = "'.$_POST['booking_date'].'"';
						}
						
						
						
					}
						$sql .= ' order by sno';
					//echo $sql;
					$result = execute_query($sql);
					$i=1;
					while($row = mysqli_fetch_array($result)){
						$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC'; 
					echo '<tr style="background:' . $bg_color . ';">
						<td>'.$i++.'</td>
						<td>'.$row['booking_date'].'</td>
						<td>'.$row['guest_name'].'</td>
						<td>'.$row['address'].'</td>
						<td>'.$row['contact_number'].'</td>
						<td>'.$row['total_amount'].'</td>
						<td>'.$row['advance_amount'].'</td>
						<td>'.$row['mop'].'</td>
						<td>'.$row['particular'].'</td>
						
						<td><a href="billingestimate.php?edit_id='.$row['sno'].'" target="_blank">Edit</a></td>
						<td><a href="print_billingestimate.php?id='.$row['sno'].'" target="_blank">Print</a></td>
					</tr>';
					$tot_taxable_amount+=floatval($row['total_amount']);
					$grand_total+=$row['advance_amount'];		
					}
					?>
					<tr><td colspan="4">&nbsp;</td><td>Total :</td><td><?php echo $tot_taxable_amount; ?></td><td><?php echo $grand_total; ?></td><td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					</tr>
				</table>
			</div>	
	</div>	
<?php				
				
					break;
				}
			}
navigation('');
page_footer();
?>