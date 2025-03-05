<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
include("scripts/settings.php");
logvalidate('','');
$colspan=5;

$sql = 'select * from general_settings where `desc`="state"';
$state = mysqli_fetch_assoc(execute_query($sql));

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
	$custid = $customer['sno'];

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
	
	$sql11="SELECT * FROM `customer_transactions` WHERE cust_id='".$custid."' and allotment_id = '".$_GET['id']."' and type='sale_restaurant' and (remarks='credit' or remarks='6')";
	//echo $sql11;
	$rest_result = execute_query($sql11);
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

	#tablediv table tr{height: 14px;}
	
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
		    <img src="images/a2.png" height="150px;" width="170px;" style="text-align:center;top:35px;left:15px; margin-left:290px; object-fit:cover;" />
		<tr>
			<th colspan="3"><h3 style="text-decoration:underline;">
				<?php if($details['invoice_type']=='tax'){ ?>
				TAX INVOICE
				<?php } else{?>
				TAX INVOICE
				<?php } ?></h3></th>
		</tr>
		<tr>
			<th colspan="3"><h2>BEDI'S DREAM LAND HOTEL AND RESORT</h2></th>
		</tr>
		</tr>
		<tr>
			<th colspan="3"><h2>Maheshpur, Near Saryu Bridge </h2></th>
		</tr>
		<tr>
			<th colspan="3"><h3>Ayodhya-224001 (U.P)</h3></th>
		</tr>

		<tr>
			<th colspan="3"><h3>Contact No : +91 8989441919, +91 8400334035/34</h3></th>
		</tr>
		<tr>
			<th colspan="3"><h3>E-Mail : bedisdreamland@gmail.com, Website: www.bedisdreamland.com</h3></th>
		</tr>
		<tr>
			<th><center></center><h3>GSTIN :09AAOFB3645G1ZA</h3></center></th>
		</tr>
	</table>
</div>
	<table width="100%" style="border-bottom: 1px solid;">
		<tr>
			<td style="border: 0px solid; line-height: 24px;" width="50%">
				<strong><h3 style="margin: 0px; padding: 0px; font-size: 18px;">Bill To,</h3></strong>
				<?php echo ($details['guest_name']!='')?'<strong>GUEST NAME : </strong>'.strtoupper($details['guest_name']).'<br>':'<strong>Guest Name : </strong><br/>';?>
				<?php echo '<strong>COMPANY NAME : </strong>'.strtoupper($customer['company_name']); ?>
				<?php echo ($customer['id_2']!='')?'<br/><strong>GSTIN : </strong>'.$customer['id_2']:'<br/><strong>GSTIN : </strong>'; ?>
				<?php echo ($customer['mobile']!='')?'<br/><strong>MOBILE : </strong>'.$customer['mobile']:'<br/><strong>MOBILE : </strong>'; ?>
				<?php echo ($customer['city']!='')?'<br/><strong>CITY : </strong>'.$customer['city']:'<br/><strong>CITY : </strong>'; ?>
				<?php echo ($customer['zipcode']!='')?'<br/><strong>PINCODE : </strong>'.$customer['zipcode']:'<br/><strong>PINCODE : </strong>'; ?>
				<?php echo ($customer['state']!='')?'<br/><strong>STATE : </strong>'.get_state($customer['state']):'<br/><strong>STATE : </strong>'; ?>
				<?php echo ($customer['id_3']!='')?'<br/><strong>'.$customer['id_type'].'</strong> : '.$customer['id_3']:''; ?>
				<?php echo '<br/><strong>SAC/HSN : </strong>'; if($customer['id_1'] !=''){echo $customer['id_1']; } ?>
				<?php echo '<br/><strong>ADDRESS :</strong>'; if($customer['address'] != '' || $details['guest_address']!=''){if($customer['address'] != ''){ echo $customer['address']; }else{ echo $details['guest_address']; }} ?>
                <?php echo '<br/><strong>OCCUPANCY : </strong>'; echo $details['occupancy']; ?>
			
			</td>
			<td style="border: 0px solid;" width="50%">
				<table width="100%" style="border-bottom: 0px solid;" cellpadding="0" cellspacing="0">
					<tr>
						<td><strong>INVOICE NO :</strong></td>
						<td>BDL/<?php echo $details['financial_year'].'/'.$details['invoice_no']; ?></td>
					</tr>
					<tr>
						<td><strong>REGISTRATION NO :</strong></td>
						<td><?php echo $details['financial_year'].'/'.$details['registration_no']; ?></td>
					</tr>
					<tr>
						<td><strong>DATE :</strong></td>
						<td><?php if($details['exit_date'] != ''){echo date("d-m-Y",strtotime($details['exit_date']));}else{echo date("d-m-Y");} ?></td>
					</tr>
					<tr>
						<td><strong>ROOM CATEGORY :</strong></td>
						<td><?php echo $room_details['room_type']; ?></td>
					</tr>
					<tr>
						<td><strong>ROOM NUMBER :</strong></td>
						<td><?php echo $room_details['room_name'].' ('.$room_details['floor_name'].')'; ?></td>
					</tr>
					<tr>
						<td><strong>CHECK IN :</strong></td>
						<td><?php echo date("d-m-Y H:i:s",strtotime($details['allotment_date'])); ?></td>
					</tr>
					<tr>
						<td><strong>CHECK OUT :</strong></td>
						<td><?php echo date("d-m-Y H:i:s",strtotime($details['exit_date'])); ?></td>
					</tr>
					
				</table>			
				
			</td>
		</tr>
	</table>
	
	<table width="100%" class="td-center">
		<?php
		$col_count=$colspan;
		$customer['state'] = $state['rate'];
		if(abs($state['rate'])==abs($customer['state'])){
			$tax_head = '<th width="15%">CGST ('.$tax_rate.'%)</th>
			<th width="15%">SGST ('.$tax_rate.'%)</th>';
		}
		else{
			$tax_head = '<th width="15%">IGST ('.($tax_rate*2).'%)</th>';
			$colspan--;
			$col_count--;
		}
		if($disc== 0  && $charges== 0){
		?>
		<tr>
			<th width="25%">Date</th>
			<th width="20%">Room Rent</th>
			<?php echo $tax_head;?>
			<th width="25%">Total</th>
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
			<?php echo $tax_head; ?>
			<th width="20%">Total</th>
		</tr>
		<?php	
		    $col_count+=2;
		}
		elseif ($disc!=0 && $charges!=0) {
		?>
		<tr>
			<th width="10%">Date</th>
			<th width="10%">Room Rent</th>
			<th width="10%">Extra Bed</th>
			<th width="10%">Discount</th>
			<th width="20%">Taxable Rent</th>
			<?php echo $tax_head; ?>
			<th width="20%">Total</th>
		</tr>
		<?php	
		    $col_count+=3;
		}
		else{
		?>
		<tr>
			<th width="15%">Date</th>
			<th width="15%"> Room Rent</th>
			<th>Discount</th>
			<th width="20%">Taxable Rent</th>
			<?php echo $tax_head; ?>
			<th width="19%">Total</th>
		</tr>
		
		<?php
		    $col_count+=2;
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
		$header_size = 140;
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
			$tot_disc+=($details['other_discount']+$details['discount_value']);
			$tot_other_charge+=floatval($details['other_charges']);

			?></td>
			<?php
			if(abs($state['rate'])==abs($customer['state'])){
			?>
			<td><?php echo round($tax,2);?></td>
			<td><?php echo round($tax,2);?></td>
			<?php }
			else{
			?>
			<td><?php echo round($tax*2,2);?></td>
			
			<?php
			}
			?>
			<td><?php echo $base_rent+ $tax + $tax; ?></td>
		</tr>
		<?php 
		
		//echo $tot;
			$start = date("d-m-Y H:i:s", strtotime($start)+86400);
		}
		$tot_res = 0;
		if(mysqli_num_rows($rest_result)!=0){
		    echo '<tr>
		    <td colspan="'.$col_count.'"><table width="100%" border="1"><tr><th>Date</th><th>Room Service Amt</th></tr>';
    	    while($rest_row = mysqli_fetch_assoc($rest_result)){
    	        echo '<tr><td>'.$rest_row['timestamp'].'</td><td>'.$rest_row['amount'].'</td></tr>';
    	        $tot_res += (float)$rest_row['amount'];
    	    }
    	    echo '<tr><th>Total : </th><th>'.$tot_res.'</th></tr></table></td></tr>';
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
			
			
			if($disc!=0 && $charges==0){
				$colspan+=2;
				echo '
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
			}

			else if($charges !=0 && $disc ==0 ){
				$colspan+=2;
				echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_other_charge,2).'</td>';
			}
			else if($charges !=0 && $disc !=0 ){
				$colspan+=3;
				echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_other_charge,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>';
			}
			echo '

			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>';
			if(abs($state['rate'])==abs($customer['state'])){
				echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
				<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>';
			}
			else{
				echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax*2,2).'</td>';
			}
			$tot += $tot_res;
				
			echo '<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td>';
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
			<td colspan="<?php echo $colspan-2; ?>"><h3 style="text-transform: capitalize;">Amount Payable (In Words) : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
			<td colspan="5" style="text-align: right;">For : BEDIS DREAM LAND<br /></td>
		</tr>
		<tr><th colspan="<?php echo $colspan-4; ?>"><p>Remarks : <?php echo $cust_transact['remarks']; ?><br> &nbsp; &nbsp; <br></p></th>
		<th><td colspan="5" style="text-align: right;"><br />(Authorised Signatory)</td></th></tr>
		<tr>
			<td colspan="5" style="line-height: 18px;">CHECK OUT TIME 11:00 AM. </br>This Is A Computer Genrated Invoice. Does Not Required Signature.<br/>Subject To Ayodhya Jurisdiction only</td>
		</tr>
	</table>
</div>
</body>
</html>