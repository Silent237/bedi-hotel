<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
logvalidate('','');
$colspan=0;
if(isset($_GET['id'])){
	$sql= 'select * from allotment_2 where allotment_id="'.$_GET['id'].'"';
	if(isset($_GET['edit_id'])){
		$sql= 'select * from allotment_2 where sno="'.$_GET['id'].'"';
	}
	//echo $sql.'<br/>';
	//die();
	$details=mysqli_fetch_array(execute_query($sql));
	if(($details['other_discount']!='' and $details['other_discount']!=NULL and $details['other_discount']!='0') || $details['discount']!=''){
		$disc = 1;
	}
	else{
		$disc = 0;

	}

	if($details['other_charges']!=''){
		$charges = 1;
	}
	else{
		//echo $details['other_charges'];
		$charges = 0;
	}
	
	$tax_rate = $details['tax_rate']/2;
	
	$sql = "select * from customer where sno=".$details['cust_id'];
	//echo $sql;
	$customer=mysqli_fetch_array(execute_query($sql));

	$sql_type = "select * from room_master where sno=".$details['cust_id'];
	//echo $sql_type;
	$row_type=mysqli_fetch_array(execute_query($sql_type));

	$sql_cat = "select * from category where sno=".$row_type['category_id'];
	//echo $sql_cat;
	$row_cat=mysqli_fetch_array(execute_query($sql_cat));


	$sql = 'select * from customer_transactions where cust_id="'.$details['cust_id'].'" and allotment_id="'.$details['sno'].'"';
	//echo $sql;
	$cust_transact= mysqli_fetch_array(execute_query($sql));

	$sql="select room_name, room_type, floor_name , category_id from room_master join category on category.sno = category_id join floor_master on floor_master.sno = floor_id where room_master.sno='".$details['room_id']."'";
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
td, th{border:0px solid;}

</style>
<script language="javascript" type="text/javascript">
//window.print();
</script>
</head>
<body>

	<div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />&nbsp;&nbsp;<a href="print_combined.php?id=<?php echo $_GET['id']; ?>">Print Combined Receipt</a></div>
<div id="wrapper" style="page-break-after:avoid;">

<div id="tablediv">
	<table width="100%" border="0" style="border-bottom: 1px solid;">
		<tr>
			<th rowspan="5" align="left" width="5%"><image src="images/a2.jpeg" height="100px;"/></th>

			<th colspan="" ><span style="text-decoration:underline;font-size: 15px;">
				<?php if($details['invoice_type']=='tax'){ ?>
				 TAX INVOICE
				<?php } else{?>
				INVOICE
				<?php } ?></span>
			</th>
		</tr>
		<tr>
			
			<th align="center"><h2 style="font-size:25px; margin:0px;">HOTEL KRISHNA PALACE</h2><h3>(A Unit of Rajput Associates (P) Ltd.)</h3></th>
		</tr>
		<tr>
			<th align="center"><span style="font-size: 15px;" >1/13/357, Civil Lines, Ayodhya (U.P)-224001</span></th>
		</tr>
		<tr>
			<th align="center"><span style="font-size: 15px;" >Ph. No.: 05278- 221367, 221368, M.No.: +91-8874210002, 8874210003<br/></span></th>
		</tr>
		<tr>
			<th align="center" colspan="2"><span style="font-size: 14px;" >E-Mail : hotelkrishnapalace@gmail.com, Website : www.krishnapalace.in</br>
			Sarai Act Regn. No. 01 (DT:27-06-2003): FSSAI NO: 12715020000073</br>GSTIN : 09AABCR0599R1ZJ (DT:27-06-2017) : PAN:  AABCR0599R<br></span></th>
		</tr>
	</table>
</div>
	<table width="100%" style="border-bottom: 1px solid;">
		<tr>
			<td><strong>Guest Name :</strong></td>
			<td><?php echo strtoupper($details['guest_name']);?></td>
			<td><strong>Guest GSTIN :</strong> <?php echo $customer['id_2']; ?></td>
			<td><strong>ID Proof : </strong><?php echo $customer['id_type'].' : '.$customer['id_3']; ?></td>
		</tr>
		<tr>
		    <td><strong>Company Name :</strong></td>
		    <td><?php echo strtoupper($customer['company_name']); ?></td>
		    <td><?php if($customer['id_1'] !=''){echo '<strong>SAC/HSN : </strong>'.$customer['id_1']; } ?></td>
		</tr>
		<tr>
			<td style="border-bottom: 1px solid;"><strong>Address :</strong></td>
			<td style="border-bottom: 1px solid;"><?php if($customer['address'] != ''){ echo $customer['address']; }else{ echo $details['guest_address']; } ?></td>
			
			<td style="border-bottom: 1px solid;"></td>
			<td style="border-bottom: 1px solid;"></td>
		</tr>
		<tr>
			<td><strong>Invoice No :</strong></td>
			<td>KP/<?php echo $details['financial_year'].'/'.$details['invoice_no']; ?></td>
			<td><strong>Date :</strong></td>
			<td><?php if($details['bill_create_date'] != ''){echo date("d-m-Y",strtotime($details['bill_create_date']));}else{echo date("d-m-Y");} ?></td>
		</tr>
		<tr>
			<td><strong>Room Category :</strong></td>
			<td><?php echo $room_details['room_type']; ?></td>
			<td><strong>Room Number :</strong></td>
			<td><?php echo $room_details['room_name'].' ('.$room_details['floor_name'].')'; ?></td>
		</tr>
		<tr>
			<td><strong>Check In :</strong></td>
			<td><?php echo date("d-m-Y H:i:s",strtotime($details['allotment_date'])); ?></td>
			<td><strong>Check Out :</strong></td>
			<td><?php echo date("d-m-Y H:i:s",strtotime($details['exit_date'])); ?></td>
		</tr>
	</table>
	<table width="100%" class="td-center">
		<?php
		if($disc== 0  && $charges== 0){
		?>
		<tr>
			<th width="25%">Date</th>
			<th width="20%">Room Rent</th>
			<th width="15%">CGST (<?php echo $tax_rate; ?>%)</th>
			<th width="15%">SGST (<?php echo $tax_rate; ?>%)</th>
			<th width="25%">Net Price</th>
		</tr>
		<?php 
		}
		elseif ($disc==0 && $charges!=0) {
		?>
		<tr>
			<th width="10%">Date</th>
			<th width="10%">Room Rent</th>
			<th width="10%">Extra Bed</th>
			<th width="20%">Taxable Rent</th>
			<th width="10%">CGST (<?php echo $tax_rate; ?>%)</th>
			<th width="10%">SGST (<?php echo $tax_rate; ?>%)</th>
			<th width="20%">Net Price</th>
		</tr>
		<?php	
		}
		elseif ($disc!=0 && $charges!=0) {
		?>
		<tr>
			<th width="10%">Date</th>
			<th width="10%">Room Rent</th>
			<th width="10%">Extra Bed</th>
			<th width="10%">Discount</th>
			<th width="20%">Taxable Rent</th>
			<th width="10%">CGST (<?php echo $tax_rate; ?>%)</th>
			<th width="10%">SGST (<?php echo $tax_rate; ?>%)</th>
			<th width="20%">Net Price</th>
		</tr>
		<?php	
		}
		else{
		?>
		<tr>
			<th width="15%">Date</th>
			<th width="15%"> Room Rent</th>
			<th>Discount</th>
			<th width="20%">Taxable Rent</th>
			<th width="11%">CGST (<?php echo $tax_rate; ?>%)</th>
			<th width="10%">SGST (<?php echo $tax_rate; ?>%)</th>
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
		$tot_other_charge=0;
		
		$full_page = 250;
		$header_size = 90;
		$footer_size = 45;
		$row_height = 8;
		$page_size = $header_size + $footer_size + $row_height;
		$page = 1;
		
		for($i=0; $i<$days; $i++){
			$remaining = $full_page - $page_size;
		/**	if($remaining<0){
				$colspan=5;
				$new = $full_page - $page_size + $footer_size;
				echo '<tr><th>&nbsp;</th></tr>
				<tr><th>Page '.$page.'</th></tr>
				<tr><th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>';
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
					<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td></tr>';
				echo '<tr style="height:8mm;"><td colspan="'.$colspan.'" style="border:1px solid; text-align:right;">Continued on next page...</td></tr>';
				echo '<tr style="height:8mm; page-break-after:always;"><td colspan="'.$colspan.'">&nbsp;</td></tr>';

				$page_size=$footer_size;
				$remaining = $full_page - $page_size;
				$page++;
			}**/
			$page_size += $row_height;
			//$tot += $details['room_rent'];
			//$tot=0;
		?>
		<tr>
			<td><?php echo date("d-m-Y",strtotime($start)); ?></td>
			<?php 
			if($details['discount']!=''){
				$discount = str_replace("%","",$details['discount']);
			}
			if($details['other_discount']!=''){
				$discount_percent = $details['other_discount']*100/$details['original_room_rent'];
				$discount += $discount_percent;
			}

			if($disc!=0 && $charges==0){
				echo '<td>'.$details['original_room_rent'].'</td><td>'.$details['discount'].'</td>';
				$base_rent = $details['taxable_amount']-$details['other_discount'];
			}
			elseif($disc==0 && $charges!=0){
			
				echo '<td>'.$details['original_room_rent'].'</td><td>'.$details['other_charges'].'</td>';
				$base_rent = $details['original_room_rent']+$details['other_charges'];
			}
			elseif($disc!=0 && $charges!=0){
				echo '<td>'.$details['original_room_rent'].'</td><td>'.$details['other_charges'].'</td><td>'.$details['discount'].'</td>';
				$base_rent = $details['original_room_rent']-($details['other_discount']+$details['discount_value'])+$details['other_charges'];
			}
			else{
				$base_rent = $details['original_room_rent'];
			}
			?>
			<td>
			<?php 
			
			if($details['invoice_type']=='tax'){
				$tax = round($base_rent*($tax_rate/100),2);
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
			$tot_disc+=$details['other_discount'];
			$tot_other_charge+=$details['other_charges'];

			?></td>
			<td><?php echo round($tax,2);?></td>
			<td><?php echo round($tax,2);?></td>
			<td><?php echo $base_rent+ $tax + $tax; ?></td>
		</tr>
		<?php 
		
		//echo $tot;
			$start = date("d-m-Y H:i:s", strtotime($start)+86400);
		}
		echo '<tr><th colspan="'.$colspan.'" style="line-height:'.($remaining-10).'mm;">&nbsp;</th></tr>';
		?>
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
		<tr>
			<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>
			<?php
			$tot = $taxable_tot+$tot_tax + $tot_tax;
			$colspan=5;
			
			if($disc!=0 && $charges==0){
				$colspan=7;
				echo '
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
			}

			else if($charges !=0 && $disc ==0 ){
				$colspan=7;
				echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_other_charge,2).'</td>';
			}
			else if($charges !=0 && $disc !=0 ){
				$colspan=8;
				echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_other_charge,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
			}
			echo '

			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td>';
			$colspan-=1;

			?>
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
			
		</tr>
		<tr>
			<td colspan="<?php echo $colspan-1; ?>"><h3 style="text-transform: capitalize;">Amount Payable (In Words) : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
			<td colspan="2" style="text-align: right;">For : HOTEL KRISHNA PALACE<br /><br /></td>
		</tr>
		<tr><th colspan="<?php echo $colspan-1; ?>"><p>CHECK OUT TIME 12 NOON. &nbsp;<br> &nbsp; &nbsp; Subject To Ayodhya Jurisdiction only<br>This Is Computer Genrated Invoice Does Not Required Signature.</p></th>
		<th><td colspan="2" style="text-align: right;"><br />(Authorised Signatory)</td></th></tr>
	</table>
</div>
</body>
</html>