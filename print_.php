<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
logvalidate('','');
if(isset($_GET['cid'])){
	if(isset($_GET['id'])){
		$sql= 'select * from allotment where sno="'.$_GET['id'].'"';
		$details=mysqli_fetch_array(execute_query($sql));
		
		if($details['discount']!=''){
			$disc = 1;
		}
		else{
			$disc = 0;  
		}
		
		$sql = "select * from customer where sno=".$details['cust_id'];
		$customer=mysqli_fetch_array(execute_query($sql));
		$sql = 'select * from customer_transactions where cust_id="'.$details['cust_id'].'" and allotment_id="'.$details['sno'].'"';
		//echo $sql;
		$cust_transact= mysqli_fetch_array(execute_query($sql));

		$sql="select room_name, room_type, floor_name from room_master join category on category.sno = category_id join floor_master on floor_master.sno = floor_id where room_master.sno='".$details['room_id']."'";
		$room_details = mysqli_fetch_array(execute_query($sql));
		if($details['exit_date']==''){
			if(isset($_GET['vt'])){
				$details['exit_date'] = $_GET['vt'];
			}
			else{
				$details['exit_date'] = date("d-m-Y H:i");
			}
		}
		$days = get_days($details['allotment_date'], $details['exit_date']);
	}
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="css/pop.css" TYPE="text/css" REL="stylesheet" media="all">
	<style type="text/css">
	@media print {
	input#btnPrint {
	display: none;
		}	
	}
	/*table, tr, td {font-size:14px; border:none; font-weight:bold;}*/
	h3{ font-size:16px;}
	</style>
	<script language="javascript" type="text/javascript">
	//window.print();
	</script>
	</head>
	<body>

	<div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />&nbsp;&nbsp;	<a href="print_combined.php?id=<?php echo $_GET['id']; ?>">Print Combined Receipt</a>
		<a href="print_both.php?id=<?php echo $_GET['id']; ?>&cid=<?php echo $_GET['cid']; ?>"><input type="button" id="btnPrint" value="Print Both Bill" /></a>
	</div>
	<div id="wrapper" style="page-break-after:avoid; margin-left:25px;margin-top:25px;"><br>
		<table width="100%" border="0" style="border-bottom: 1px solid;">
			
			<tr>
				<th colspan="2"><h1>HOTEL GONARD</h1></th>
			</tr>
			
			<tr>
				<th colspan="2"><h3>Civil Lines Gonda-271001</h3></th>
			</tr>
			<tr>
				<th><h3>GSTIN : 09AACCP0999C1ZB</h3></th>
			</tr>
			<tr>
				<th><h3>PAN No. : AACCP0999C</h3></th>
				
			</tr>
		</table>
		<table width="100%" style="border-bottom: 1px solid;">
			<tr>
				<td>Bill To :</td>
				<td><?php echo $customer['company_name'];?></td>
				<td>Guest GSTIN : </td>
				<td><?php echo $customer['id_2']; ?></td>
				
			</tr>
			<tr>
				<td>Name</td>
				<td> <?php echo $customer['cust_name']; ?>
				</td>
			</tr>
			<tr>
				<td style="border-bottom: 1px solid;">Address :</td>
				<td style="border-bottom: 1px solid;"><?php echo $customer['address']; ?></td>
				<td style="border-bottom: 1px solid;">ID Number : </td>
				<td style="border-bottom: 1px solid;"><?php echo $customer['id_1'];?></td>
			</tr>
			<tr>
				<td>Invoice No :</td>
				<td>HG/<?php echo $details['financial_year'].'/'.$details['invoice_no']; ?></td>
				<td>Date :</td>
				<td><?php echo date("d-m-Y",strtotime($details['allotment_date'])); ?></td>
			</tr>
			<tr>
				<td>Room Type :</td>
				<td><?php echo $room_details['room_type']; ?></td>
				<td>Room Number :</td>
				<td><?php echo $room_details['room_name'].' ('.$room_details['floor_name'].')'; ?></td>
			</tr>
			<tr>
				<td>Check In :</td>
				<td><?php echo date("d-m-Y H:i:s",strtotime($details['allotment_date'])); ?></td>
				<td>Check Out :</td>
				<td><?php echo date("d-m-Y H:i:s",strtotime($details['exit_date'])); ?></td>
			</tr>
		</table>
		<table width="100%" class="td-center">
			<?php
			if($disc==0){
			?>
			<tr>
				<th width="40%">Date</th>
				<th width="20%">Rate</th>
				<th width="10%">CGST (6%)</th>
				<th width="10%">SGST (6%)</th>
				<th width="20%">Net Price</th>
			</tr>
			<?php 
			}
			else{
			?>
			<tr>
				<th width="20%">Date</th>
				<th width="10%">Room Rent</th>
				<th width="10%">Discount</th>
				<th width="20%">Taxable Rent</th>
				<th width="11%">CGST (6%)</th>
				<th width="10%">SGST (6%)</th>
				<th width="19%">Net Price</th>
			</tr>
			
			<?php
			}
			
			$start = date("d-m-Y H:i:s", strtotime($details['allotment_date']));
			$exit = $details['exit_date'];
			$tot_original_rent=0;
			$tot_disc=0;
			$taxable_tot=0;
			$tot_tax = 0;
			$tot=0;
			$colspan=7;
			$full_page = 250;
			$header_size = 85;
			$footer_size = 40;
			$row_height = 8;
			$page_size = $header_size + $footer_size + $row_height;
			$page = 1;
			
			for($i=0; $i<$days; $i++){
				$remaining = $full_page - $page_size;
				if($remaining<0){
					$colspan=7;
					$new = $full_page - $page_size + $footer_size;
					echo '<tr><th>&nbsp;</th></tr>
					<tr><th>Page '.$page.'</th></tr>
					<tr><th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>';
					if($tot_disc>0){
						$colspan=8;
						echo '
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
					}
					echo '
						<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
						<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td></tr>';
					echo '<tr style="height:8mm;"><td colspan="'.$colspan.'" style="border:1px solid; text-align:right;">Continued on next page...</td></tr>';
					echo '<tr style="height:8mm; page-break-after:always;"><td colspan="'.$colspan.'">&nbsp;</td></tr>
					<tr style="height:10mm;"><td colspan="'.$colspan.'">&nbsp;</td></tr>';

					$page_size=$footer_size;
					$remaining = $full_page - $page_size;
					$page++;
				}
				$page_size += $row_height;
				$tot += $details['room_rent'];
			?>
			<tr>
				<td><?php echo $start;?></td>
				<?php if($disc!=0){
					echo '<td>'.$details['original_room_rent'].'</td><td>'.$details['discount'].'</td>';
					$base_rent = $details['original_room_rent']-$details['discount_value'];
				}
				else{
					$base_rent = $details['original_room_rent'];
				}
				?>
				<td>
				<?php 
				if($details['invoice_type']=='tax'){
					$tax = round($base_rent*0.06,2);
					$temp_rent = $base_rent+$tax+$tax;;
					echo $base_rent;
					$taxable_tot+=$base_rent;
				}
				else{
					$temp_rent = $details['room_rent'];
					echo $details['room_rent'];
					$taxable_tot+=$details['room_rent'];
					$tax=0;
				}
				$tot_tax += $tax;
				$tot_original_rent+=$details['original_room_rent'];
				$tot_disc+=$details['discount_value'];
				?></td>
				<td><?php echo round($tax,2);?></td>
				<td><?php echo round($tax,2);?></td>
				<td><?php echo $details['room_rent']; ?></td>
			</tr>
			<?php 
				$start = date("d-m-Y H:i:s", strtotime($start)+86400);
			}
			echo '<tr><th colspan="'.$colspan.'" style="line-height:'.($remaining-35).'mm;">&nbsp;</th></tr>';
			?>
			<tr>
				<th colspan="5">&nbsp;</th>
			</tr>
			<tr>
				<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>
				<?php
			$colspan=4;
			if($tot_disc>0){
				$colspan=6;
				echo '
				<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
			}
			echo '
				<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>
				<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
				<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
				<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td>';
				?>
			</tr>
			<tr style=" border: 1px solid;">

				<td><b>Mode of Payment : </b></td>
				<td><?php echo $cust_transact['mop']; ?>&nbsp;</td>
			</tr>
			<tr>
				<th colspan="<?php echo $colspan; ?>" align="right">Round Off:</th>
				<th>
					<?php
					$tot = round($tot,2);
					$round_off = round($tot);
					$round_off = round($round_off-$tot,2);
					echo $round_off;
					
					?>
				</th>
			</tr>
			<tr>
				<th colspan="<?php echo $colspan; ?>" align="right">Amount Payable :</th>
				<th>
					<?php
					echo round($tot);
					
					?>
				</th>
			</tr>
			<tr>
				<td colspan="<?php echo $colspan-1; ?>"><h3 style="text-transform: capitalize;">Amount Payable : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
				<td colspan="2" style="text-align: right;">For : HOTEL GONARD<br /><br /></td>
			</tr>
			<tr><th colspan="<?php echo $colspan-1; ?>"><p>CHECK OUT TIME 12 NOON. &nbsp; THANK YOU<br> &nbsp; &nbsp; Subject To Gonda Jurisdiction only</p></th>
			<th><td colspan="2" style="text-align: right;"><br />(Authorised Signatory)</td></th></tr>
		</table><br><br><br>
	</div>

<?php
} 
else{

	if(isset($_GET['id'])){
		$sql= 'select * from allotment where sno="'.$_GET['id'].'"';
		$details=mysqli_fetch_array(execute_query($sql));
		
		if($details['discount']!=''){
			$disc = 1;
		}
		else{
			$disc = 0;
		}
		
		$sql = "select * from customer where sno=".$details['cust_id'];
		$customer=mysqli_fetch_array(execute_query($sql));
		$sql = 'select * from customer_transactions where cust_id="'.$details['cust_id'].'" and allotment_id="'.$details['sno'].'"';
		//echo $sql;
		$cust_transact= mysqli_fetch_array(execute_query($sql));

		$sql="select room_name, room_type, floor_name from room_master join category on category.sno = category_id join floor_master on floor_master.sno = floor_id where room_master.sno='".$details['room_id']."'";
		$room_details = mysqli_fetch_array(execute_query($sql));
		if($details['exit_date']==''){
			if(isset($_GET['vt'])){
				$details['exit_date'] = $_GET['vt'];
			}
			else{
				$details['exit_date'] = date("d-m-Y H:i");
			}
		}
		$days = get_days($details['allotment_date'], $details['exit_date']);
	}
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="css/pop.css" TYPE="text/css" REL="stylesheet" media="all">
	<style type="text/css">
	@media print {
	input#btnPrint {
	display: none;
		}	
	}
	/*table, tr, td {font-size:14px; border:none; font-weight:bold;}*/
	h3{ font-size:16px;}
	</style>
	<script language="javascript" type="text/javascript">
	//window.print();
	</script>
	</head>
	<body>


	<div id="wrapper" style="page-break-after:avoid; margin-left:25px;margin-top:15px;">
		<table width="100%" border="0" style="border-bottom: 1px solid;" cellpadding="0" cellspacing="0">
			<tr>
				<th colspan="2"><h1>HOTEL GONARD</h1></th>
			</tr>
			
			<tr>
				<th colspan="2">Civil Lines, Gonda-271001 <br/>M : 6387238586, 8081339445</th>
			</tr>
			<tr>
				<td align="left">GSTIN : 09AACCP0999C1ZB</td>
			</tr>
		</table>
		<table width="100%" style="border-bottom: 1px solid; font-weight: bold;">
			<tr>
				<td>Name Mr./Mrs./Miss. : </td>
				<td><span style="text-transform:uppercase"><?php echo $customer['company_name']; ?></span></td>
			</tr>
			<tr>
			<td>Company Name : </td>
			<td><span style="text-transform:uppercase"><?php echo $customer['cust_name'];')'; ?></span></td>
			
		</tr>
		
		<tr>
			<td >Guest Address : </td>
			<td ><span style="text-transform:uppercase"><?php echo $customer['address']; ?></span>	</td>
			
		</tr>
		<tr>
			<td>Register S.L.No : </td>
			<td><span><?php echo $details['sno']; ?></span>	
		<tr>
			<td>Date of Arrival :</td>
			<td><?php echo date("d-m-Y",strtotime($details['allotment_date'])); ?></td>
			<td>Arrival Time</td>
			<td><?php echo date("H:i:s",strtotime($details['allotment_date'])); ?></td>
			
			
		</tr>
		<tr>
			<td>Date of Departure :</td>
			<td><?php echo date("d-m-Y",strtotime($details['exit_date'])); ?></td>
			<td>Departure Time</td>
			<td><?php echo date("H:i:s",strtotime($details['exit_date'])); ?></td>
			
		</tr>
		<tr>
			<td>Number of person : </td>
			<td><?php echo $details['occupancy']; ?></td>
			<td>Adultes : </td>
			<td> <?php echo $details['no_of_male']; ?></td>
			<td>Childran : </td>
			<td> <?php echo $details['no_of_kids']; ?></td>
		</tr>
		<tr>
			<td>Invoice No.</td>
			<td>HG/<?php echo $details['financial_year'].'/'.$details['invoice_no']; ?></td>
			<td>Date</td>
			<td><?php echo date("d-m-Y"); ?></td>
		</tr>
		</table>
		<table width="100%" class="td-center" cellpadding="">
			<?php
			//if($disc==0){
			?>
			
			<?php 
			//}
			//else{
			?>
			<tr>
				<th width="20%" style="border-bottom: 1px solid;">Date</th>
				<th width="15%" style="border-bottom: 1px solid;">Recpt/kot No.</th>
				<th width="10%" style="border-bottom: 1px solid;">Room No.</th>
				<th width="35%" style="border-bottom: 1px solid;">Charge Of</th>
				<th width="20%" style="border-bottom: 1px solid;">Total Amount</th>
				<th width="20%" style="border-bottom: 1px solid;">&nbsp;</th>
				<th width="20%" style="border-bottom: 1px solid;">&nbsp;</th>
			</tr>
			
			<?php
			//}
			
			$start = date("d-m-Y H:i:s", strtotime($details['allotment_date']));
			$exit = $details['exit_date'];
			$tot_original_rent=0;
			$tot_disc=0;
			$taxable_tot=0;
			$tot_tax = 0;
			$tot=0;
			$colspan=7;
			$full_page = 200;
			$header_size = 85;
			$footer_size = 40;
			$row_height = 8;
			$page_size = $header_size + $footer_size + $row_height;
			$page = 1;
			$discount=0;
			for($i=0; $i<1; $i++){
				$remaining = $full_page - $page_size;
				if($remaining<0){ 
					$colspan=7;
					$new = $full_page - $page_size + $footer_size;
					echo '<tr><th>&nbsp;</th></tr>
					<tr><th>Page '.$page.'</th></tr>
					<tr><th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>';
					if($tot_disc>0){
						$colspan=8;
						echo '
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
					}
					echo '
						<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
						<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td></tr>';
					echo '<tr style="height:8mm;"><td colspan="'.$colspan.'" style="border:1px solid; text-align:right;">Continued on next page...</td></tr>';
					echo '<tr style="height:8mm; page-break-after:always;"><td colspan="'.$colspan.'">&nbsp;</td></tr>
					<tr style="height:10mm;"><td colspan="'.$colspan.'">&nbsp;</td></tr>';

					$page_size=$footer_size;
					$remaining = $full_page - $page_size;
					$page++;
				}
				$page_size += $row_height;
			?>
			<tr>
				<td><?php echo date("d-m-Y",strtotime($start)); $to=strtotime($start); $date_to = strtotime("+1 day", $to);?><br> To <br><?php echo date("d-m-Y",strtotime($details['exit_date'])); ?></td>
				<?php
					if($details['occupancy']==1){
						echo '<td>Single('.$details['plans'].')</td>';
					}
					else if($details['occupancy']==2){
						echo '<td>Double('.$details['plans'].')</td>';
					}
					else if($details['occupancy']==3){
						echo '<td>Triple('.$details['plans'].')</td>';
					}
					else{
						echo '<td>'.$details['plans'].'</td>';
					}
					/*switch($details['occupancy']){
						case 1:{
							echo 'Single (EP)';
							break;
						}
						case 2:{
							echo 'Double (EP)';
							break;
						}
						case 3:{
							echo 'Triple (EP)';
							break;
						}
						default:{
							echo 'Multiple (EP)';
							break;
						}	
							
					} */
				
					$net_rate=0;
					$tot=0;
	
				?>
				<td>
				<?php 
				echo $room_details['room_name'];
				$net_rate=$details['original_room_rent'];
				?></td>
				
				<td style="line-height: 16px;">Room charges @ <?php echo $details['original_room_rent'].'/- ';
				if($details['other_charges'] > 0){
						echo '<br>Extra Bed charges :  (Rs.'.$details['other_charges'].')';
						$taxable_tot+=$details['other_charges'];
						$net_rate+=$details['other_charges'];
				}
				if($details['discount_value']>0){
					echo '<br>Discount : ';
					if($details['discount']!=''){
						echo $details['discount'].' (Rs.'.$details['discount_value'].')';
						
					}
					else{
						echo 'Rs.'.$details['discount_value'];
					}
					
					$net_rate-=$details['discount_value'];
					echo '<br/>Net Rate : Rs.'.$net_rate.'/- X ';
				}
				else{
					echo ' X ';
				}
				
				echo $days;?></td>
				<td>Rs.<?php echo $net_rate*$days; ?></td>
				<?php
				$hotel_tot = $net_rate*$days;
				$tot=$hotel_tot;
				?>
			</tr>
			<?php 
				$start = date("d-m-Y H:i:s", strtotime($start)+86400);
			}
			$id=$_GET['id'];
			$sql11="SELECT sno,timestamp, amount ,invoice_no FROM `customer_transactions` WHERE allotment_id='".$id."' and type='sale_restaurant'";
			$result = execute_query($sql11);
			$tot_res=0;
			while($resbal=mysqli_fetch_array($result)){
				$id=$resbal['sno'];
				$inv=$resbal['invoice_no'];
				$sql="SELECT * FROM `invoice_sale_restaurant` WHERE invoice_no='$inv'";
				$res=execute_query($sql);
				$kot_num=mysqli_fetch_array($res);
				echo '<tr>
				<td>'.date("d-m-Y", strtotime($resbal['timestamp'])).'</td>
				<td>'.$kot_num['kot_no'].'</td>
				<td>&nbsp;</td>
				<td>Fooding Charges</td>
				<td>Rs.'.$resbal['amount'].'</td></tr>';	
				$tot_res += $resbal['amount'];
				$tot+=$resbal['amount'];
				

			}
			//echo $resbal['amount'];
			echo '<tr><th colspan="'.$colspan.'" style="line-height:'.($remaining-35).'mm;">&nbsp;</th></tr>';
			?>
		
			
		<tr>
			<td colspan="<?php echo $colspan+1; ?>" style="border-top:1px solid;">

			</td>
		</tr>
		<tr>
			
			<th colspan="<?php echo $colspan-1; ?>" align="left" >Total : </th>
			<td> Rs.<?php echo $tot; ?>  </td>
			
		</tr>
		<tr>
			<td colspan="<?php echo $colspan-1; ?>" >SGST 6% on amount [<?php echo $hotel_tot; ?>]</td>
			<td> Rs.<?php if($details['room_rent']>1000){ $sgst= $hotel_tot*6/100; echo $sgst;} else{$sgst=0;echo"0";} ?></td>
		</tr>
		<tr>
			<td colspan="<?php echo $colspan-1; ?>" >CGST 6% on amount [<?php echo $hotel_tot;  ?>]</td>
			<td>Rs.<?php if($details['room_rent']>1000){ $cgst= $hotel_tot*6/100; echo $cgst;}else{$cgst=0;echo"0";} ?></td>
		</tr>
		<tr>
			<td colspan="<?php echo $colspan+1; ?>" style="border-top:1px solid;">

			</td>
		</tr>
		<tr>
			<td colspan="<?php echo $colspan-1; ?>">Net Total</td>
			<td>Rs.<?php $withgst= $tot+$cgst+$sgst; echo round($withgst,2); ?></td>
		</tr>
		<tr>
				<td colspan="<?php echo $colspan-1; ?>" align="">Round Off:</td>
				<td>
					<?php
					$totl=round($withgst,2);
					$roundoff=floor($totl);
					$round_off = round($totl-$roundoff,2);
					echo $round_off;
					
					?>
				</td>
		</tr>
			<tr>
			<td colspan="<?php echo $colspan+1; ?>" style="border-top:1px solid;">

			</td>
		</tr>
		<tr>
		
			
			<tr>
				<th colspan="<?php echo $colspan-1; ?>" align="right">Amount Payable :</th>
				<th>
					<?php
					echo round($withgst);
					
					?>
				</th>
			</tr>
			<tr >

				<th colspan="<?php echo $colspan-1; ?>" align="right" >Mode of Payment : </th>
				<th><?php echo strtoupper($cust_transact['mop']); ?>&nbsp;</th>
			</tr>
			<tr>
				<td colspan="<?php echo $colspan+1; ?>" style="border-top:1px solid;"></td>
			</tr>
			<tr>
				<td colspan="<?php echo $colspan-0; ?>" style="text-align:left"><span style="text-transform: capitalize;">Rupees(inwords) : <?php echo int_to_words(round($withgst,0)); ?> Only</span>
				</td>
			</tr>
			<tr>
				<td  colspan="<?php echo $colspan-5; ?>"  style="text-align:left">Signature of Guest : <br> E.& O.E.</td>
				<td  colspan="<?php echo $colspan+2; ?>" style="text-align:right">For : HOTEL GONARD<br /><br /></td>
			</tr>
			<tr>
				<td colspan="<?php echo $colspan-5; ?>"></td>
				<td colspan="<?php echo $colspan-5; ?>" align="center"></td>
				
			</tr>
			<tr>
				<td colspan="<?php echo $colspan-5; ?>" style="text-align:left">User Name : <?php echo $details['created_by'];  ?></td>
				<td colspan="<?php echo $colspan-3; ?>" style="text-align:right">Receptionist</td>
			</tr>
				<tr>
					<td colspan="<?php echo $colspan; ?>"  style="text-align:center ;">Thanks for your kind Visit !</td>
				</tr>
		</table>
	</div>
<?php } ?>
</body>
</html>