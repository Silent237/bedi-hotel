<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
$tabindex=1;
$response=1;
page_header();

			switch($response){
				case 1:{
?>
			<div id="content">
				<h2>Dummy Report</h2>
				<form name="main_form" method="POST" action="dummy_room_report.php" enctype="multipart/formdata">
					<table width="100%">
						<tr>
							<td>Check-In Date :</td>
							<td><input type="date" name="checkin_date" id="checkin_date" value="" ></td>
							<td>Check-Out Date :</td>
							<td><input type="date" name="checkout_date" id="checkout_date" value="" ></td>
						</tr>
						<tr>
							<td>Guest Name :</td>
							<td><input type="text" name="guest_name" tabindex="<?php echo $tabindex++; ?>">
							</td>
							<td>Mobile Number:</td>
							<td><input type="text" name="mobile_number" tabindex="<?php echo $tabindex++; ?>"></td>
						</tr>
						
						<tr>
							<td colspan="4" style="text-align: center;"><input type="submit" name="submit" value="Search" tabindex="<?php echo $tabindex++; ?>"></td>
						</tr>
					
					</table>			
				</form>
				<table>
					<tr>
						<th>S.No.</th>
						<th>Check-In Date</th>
						<th>Check-Out Date</th>
						<th>Guest Name</th>
						<th>Mobile</th>
						<th>Address</th>
						<th>Room No</th>
						<th>Occupancy</th>
						<th>Base Rent</th>
						<th>Extra Bed</th>
						<th>Discount</th>
						<th>Taxable Amount</th>
						<th>cgst%</th>
						<th>sgst%</th>
						<th>Total Rent</th>
						<th> &nbsp;</th>
					</tr>
					<?php
					$tot_taxable_amount=0;
					$grand_total=0;
					$sql = 'select * from dummy_room where 1=1';
					if(isset($_POST['submit'])){
						if($_POST['guest_name']!=''){
							$sql .= ' and guest_name = "'.$_POST['guest_name'].'"';
						}
						if($_POST['mobile_number']!=''){
							$sql .= ' and mobile_number = "'.$_POST['mobile_number'].'"';
						}
						if($_POST['checkin_date']!=''){
							$sql .= ' and checkin_date = "'.$_POST['checkin_date'].'"';
						}
						if($_POST['checkout_date']!=''){
							$sql .= ' and checkout_date = "'.$_POST['checkout_date'].'"';
						}
						
						
					}
						$sql .= ' order by sno';
					//echo $sql;
					$result = execute_query($sql);
					$i=1;
					while($row = mysqli_fetch_array($result)){
						$sql_invoice = 'select * from dummy_room_invoice where invoice_no = "'.$row['invoice_no'].'"';
						//echo $sql_invoice.'</br>';
						$row_invoice = mysqli_fetch_array(execute_query($sql_invoice));
					echo '<tr>
						<td>'.$i++.'</td>
						<td>'.$row['checkin_date'].'</td>
						<td>'.$row['checkout_date'].'</td>
						<td>'.$row['guest_name'].'</td>
						<td>'.$row['mobile_number'].'</td>
						<td>'.$row['address'].'</td>
						<td>'.$row_invoice['room_no'].'</td>
						<td>'.$row_invoice['room_type'].'</td>
						<td>'.$row['total_base_rent'].'</td>
						<td>'.$row['total_extra_bed'].'</td>
						<td>'.$row['total_discount'].'</td>
						<td>'.$row['total_taxable_amount'].'</td>
						<td>'.$row['total_cgst'].'%</td>
						<td>'.$row['total_sgst'].'%</td>
						<td>'.round($row['grand_total'],3).'</td>
						
						<td><a href="print_dummy_room.php?id='.$row['sno'].'" target="_blank">Print Receipt</a></td>
					</tr>';
					$tot_taxable_amount+=$row['total_taxable_amount'];
					$grand_total+=$row['grand_total'];		
					}
					?>
					<tr><td colspan="10">&nbsp;</td><td>Total :</td><td><?php echo $tot_taxable_amount; ?></td><td>&nbsp;</td><td>&nbsp;</td><td><?php echo $grand_total; ?></td><td>&nbsp;</td>
					<td>&nbsp;</td>
					</tr>
				</table>
			</div>		
<?php				
				
					break;
				}
			}
page_footer();
?>