<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
include("scripts/settings.php");
logvalidate('','');

$colspan=5;

$sql = 'select * from general_settings where `desc`="company"';
$company = mysqli_fetch_assoc(execute_query($sql));
$company = $company['rate'];

$sql = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_assoc(execute_query($sql));
$address = $address['rate'];

$sql = 'select * from general_settings where `desc`="contact"';
$contact = mysqli_fetch_assoc(execute_query($sql));
$contact = $contact['rate'];

$sql = 'select * from general_settings where `desc`="email"';
$email = mysqli_fetch_assoc(execute_query($sql));
$email = $email['rate'];

$sql = 'select * from general_settings where `desc`="website"';
$web = mysqli_fetch_assoc(execute_query($sql));
$web = $web['rate'];

$sql = 'select * from general_settings where `desc`="gstin"';
$gstin = mysqli_fetch_assoc(execute_query($sql));
$gstin = $gstin['rate'];

$sql = 'select * from general_settings where `desc`="state"';
$state = mysqli_fetch_assoc(execute_query($sql));



if(isset($_GET['id'])){
	$sql= 'select * from allotment where sno="'.$_GET['id'].'"';
	$details=mysqli_fetch_array(execute_query($sql));
	$tax_rate = $details['tax_rate']/2;
	
	$sql = "select * from customer where sno=".$details['cust_id'];
	$customer=mysqli_fetch_array(execute_query($sql));
	if($details['exit_date']==''){
		$exit = 'exit_date is null';
	}
	else{
		$exit = 'exit_date="'.$details['exit_date'].'"';
	}
	
	if($details['exit_date']==''){
		$details['exit_date'] = date("Y-m-d H:i");
	}
	
	$days = get_days($details['allotment_date'], $details['exit_date']);
	
	$sql = 'select * from customer_transactions where cust_id="'.$details['cust_id'].'" and allotment_id="'.$details['sno'].'"';
	//echo $sql;
	$cust_transact= mysqli_fetch_array(execute_query($sql));
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
	#tablediv table tr{height: 14px;}

</style>
<script language="javascript" type="text/javascript">
//window.print();
</script>
</head>
<body>

	<div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />&nbsp;&nbsp;<a href="print_combined.php?id=<?php echo $_GET['id']; ?>">Print Combined Receipt</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="print_grouped.php?id=<?php echo $_GET['id']; ?>">Print Grouped Receipt</a></div></div>
<div id="wrapper" style="page-break-after:avoid;">


<div id="tablediv">
		<table width="100%" border="0" style="border-bottom: 1px solid;">
		    <img src="images/a2.png" height="170px;" width="250px;" style="margin-left:250px;" />
		<tr>
			<th colspan="3"><h3 style="text-decoration:underline;">
				<?php if($details['invoice_type']=='tax'){ ?>
				TAX INVOICE
				<?php } else{?>
				TAX INVOICE
				<?php } ?></h3></th>
		</tr>
		<tr>
			<th colspan="3"><h2><?php echo $company; ?></h2></th>
		</tr>
		</tr>
		<tr>
			<th colspan="3"><h2><?php echo $address; ?></h2></th>
		</tr>
		
		<tr>
			<th colspan="3"><h3><?php echo $contact; ?></h3></th>
		</tr>
		<tr>
			<th colspan="3"><h3>E-Mail : <?php echo $email; ?>, Website: <?php echo $web; ?></h3></th>
		</tr>
		<tr>
			<th><center></center><h3>GSTIN :<?php echo $gstin; ?></h3></center></th>
		</tr>
	</table>
</div>

<table width="100%" style="border-bottom: 1px solid;">
	<tr>
		<td style="border: 0px solid; line-height: 24px;" width="50%">
			<strong><h3 style="margin: 0px; padding: 0px; font-size: 18px;">Bill To,</h3></strong>
			<?php echo ($details['guest_name']!='')?'<strong>GUEST NAME : </strong>'.strtoupper($details['guest_name']).'<br>':'<strong>Guest Name : </strong><br/>';?>
			<?php echo '<strong>COMPANY NAME : </strong>'.strtoupper($customer['company_name']); ?>
			<?php echo '<br/><strong>GSTIN : </strong>'; echo ($customer['id_2']!='')?$customer['id_2']:''; ?>
			<?php echo ($customer['id_3']!='')?'<br/><strong>'.$customer['id_type'].'</strong> : '.$customer['id_3']:''; ?>
			<?php echo '<br/><strong>SAC/HSN : </strong>'; if($customer['id_1'] !=''){echo $customer['id_1']; } ?>
			<?php echo '<br/><strong>ADDRESS :</strong>';  if($customer['address'] != '' || $details['guest_address']!=''){if($customer['address'] != ''){ echo $customer['address']; }else{ echo $details['guest_address']; }} ?>
			
			<?php echo '<br/><strong>MOBILE NO :</strong>'; if($customer['mobile'] != '' || $details['mobile']!=''){if($customer['mobile'] != ''){ echo $customer['mobile']; }else{ echo $details['mobile']; }} ?>

		</td>
		<td style="border: 0px solid;" width="50%" valign="top">
			<table width="100%" style="border-bottom: 0px solid;" cellpadding="0" cellspacing="0">
				<tr>
					<td><strong>DATE :</strong></td>
					<td><?php if($details['bill_create_date'] != ''){echo date("d-m-Y",strtotime($details['bill_create_date']));}else{echo date("d-m-Y");} ?></td>
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
		$colspan=7;
		$customer['state'] = $state['rate'];
		if(abs($state['rate'])==abs($customer['state'])){
			$tax_head = '<th width="15%">CGST ('.$tax_rate.'%)</th>
			<th width="15%">SGST ('.$tax_rate.'%)</th>';
		}
		else{
			$tax_head = '<th width="15%">IGST ('.($tax_rate*2).'%)</th>';
			$colspan--;
		}
		
		if($details['discount'] !=0){ 
			$colspan+=2;
		?>
		<tr>
			<th width="20%">Invoice No. and Date</th>
			<th width="20%">Reg. No.</th>
			<th width="30%">Room Name</th>
			<th width="8%">Rate</th>
			<th width="8%">Discount</th>
			<th width="8%">Taxable Rate</th>
			<?php echo $tax_head; ?>
			<th width="10%">Total</th>
		</tr>

		<?php
	}else{
		echo '<tr>
			<th width="20%">Invoice No. and Date</th>
			<th width="20%">Reg. No.</th>
			<th width="30%">Room Name</th>
			<th width="8%">Taxable Rate</th>
			'.$tax_head.'
			<th width="10%">Total</th>
		</tr>';

	}
		$sql = 'SELECT room_name, room_id, room_rent, discount, discount_value, original_room_rent, other_charges, invoice_type, registration_no, financial_year, invoice_no, other_discount FROM `allotment` join room_master on room_master.sno = allotment.room_id where cust_id="'.$details['cust_id'].'" and allotment_date="'.$details['allotment_date'].'" and '.$exit;
		//echo $sql;
		$rooms_result = execute_query($sql);

		$tot_tax = 0;
		$tot=0;
		$taxable_tot=0;
		$tot_original_rent=0;
		$tot_disc=0;
		
		$full_page = 290;
		$header_size = 90;
		$footer_size = 45;
		$row_height = 11;
		$page_size = $header_size + $footer_size + $row_height;
		$page = 1;
								
		while($rooms = mysqli_fetch_array($rooms_result)){
			$sql_type = "select * from room_master where sno=".$rooms['room_id'];
	        $sql_run=execute_query($sql_type);
	        while ($row_type=mysqli_fetch_array($sql_run)) {
				$start = date("d-m-Y H:i:s", strtotime($details['allotment_date']));
				$exit = $details['exit_date'];
				for($i=0; $i<$days; $i++){
				
				$remaining = $full_page - $page_size;
				if($remaining<0){

					$new = $full_page - $page_size + $footer_size;
					echo '<tr><th>&nbsp;</th></tr>
					<tr><th>Page '.$page.'</th></tr>
					<tr><th width="20%" colspan="3" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>';
				
					echo '
						<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>';
					
					if(abs($state['rate'])==abs($customer['state'])){
						echo '<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
						<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>';
					}
					else{
						echo '<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax*2,2).'</td>';
					}
						echo '<th width="20%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td></tr>';
					
					echo '<tr style="height:8mm;"><td colspan="'.$colspan.'">&nbsp;</td></tr>';

					$page_size=$footer_size;
					$remaining = $full_page - $page_size;
					$page++;
				}
				$page_size += $row_height;
				if($page!=1){
					$row_height=11;
				}
					
				$tot += $rooms['room_rent'];

		?>
		<tr>
			<td>BDL/<?php echo $rooms['financial_year'].'/'.$rooms['invoice_no']; ?><br/><?php echo $start; ?></td>
			<td><?php echo $rooms['financial_year'].'/'.$rooms['registration_no']; ?></td>
			<td><?php echo room_name($rooms['room_id']).' ('.floor_name($row_type['floor_id']).')'; ?><br/><?php echo category_name($row_type['category_id']); ?></td>
			<?php 
				if($rooms['discount']!=0){
					if($rooms['other_discount']!=''){
					    if(strpos($rooms['discount'], "%")===false){
					        $disc_symbol = 0;
					    }
					    else{
					        $disc_symbol = 1;
					        
					    }
						$rooms['discount'] = str_replace("%", "", $rooms['discount']);
						if($disc_symbol==1){
						    $rooms['discount'] = $rooms['discount'].'%';   
						}
						else{
						    $rooms['discount'] = $rooms['discount'];
						}
						
					}
					echo '<td>'.$rooms['original_room_rent'].'</td><td>'.$rooms['discount'].'</td>';
					$base_rent = $rooms['original_room_rent']-($rooms['discount_value']+$rooms['other_discount'])+floatval($rooms['other_charges']);
					//echo $base_rent;
					$taxable_tot+=$base_rent;
					$tot_disc += ($rooms['discount_value']+$rooms['other_discount']);
				}
				else{
					$base_rent = $rooms['original_room_rent'];
					//echo '<td>'.$rooms['original_room_rent'].'</td>';
					$taxable_tot+=$base_rent;
				}
			?>
			<td>
			<?php 
			if($rooms['invoice_type']=='tax'){
				$tax = round($base_rent*0.06,2);
				$temp_rent = $base_rent+$tax+$tax;;
				echo $base_rent;
			}
			else{
				$temp_rent = $rooms['room_rent'];
				echo $rooms['room_rent'];
				$tax=0;
			}
			$tot_tax += $tax;
			$tot_original_rent+=$rooms['original_room_rent'];
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
			<td><?php echo $rooms['room_rent']; ?></td>
		</tr>
		<?php 
				$start = date("d-m-Y H:i:s", strtotime($start)+86400);
			}
		}
	}
		//echo $page; 
		echo '<tr><th colspan="'.$colspan.'">&nbsp;</th></tr>';?>
		<tr>
			<th colspan="<?php echo $colspan; ?>">&nbsp;</th>
		</tr>
		<tr>
			<th colspan="3" width="45%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </td>
			<?php
			echo '
			<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_original_rent,2).'</td>';
			if($tot_disc!='' && $tot_disc!='0'){
				echo '<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_disc,2).'</td>
				<th width="8%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($taxable_tot,2).'</td>';
			}
			if(abs($state['rate'])==abs($customer['state'])){
				echo '
				<th width="8%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>
				<th width="8%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax,2).'</td>';
			}
			else{
				echo '<th width="8%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_tax*2,2).'</td>';
			}
			echo '<th width="6%" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot,2).'</td>';
			?>
		</tr>
		<tr>
			<th colspan="<?php echo $colspan-1; ?>" align="right">Round Off:</th>
			<th>
				<?php
				$tot = round($tot,2);
				$round_off = round($tot);
				$round_off = round($round_off-$tot,2);
				echo $round_off;
				
				?>
			</th>
		<tr>
			<th colspan="<?php echo $colspan-1; ?>" align="right">Amount Payable :</th>
			<th>
				<?php
				echo round($tot);
				
				?>
			</th>
		</tr>
		<tr>
			<td colspan="<?php echo $colspan-5; ?>"><h3 style="text-transform: capitalize;">Amount Payable (In Words) : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
			<td colspan="5" style="text-align: right;">For :BEDIS DREAM LAND<br /></td>
		</tr>
		<tr><th colspan="<?php echo $colspan-4; ?>"><p>Remarks : <?php echo $cust_transact['remarks']; ?><br> &nbsp; &nbsp; <br></p></th>
		    <th colspan="5" style="text-align: right;"><br />(Authorised Signatory)</th></tr>
		<tr>
			<td colspan="8" style="line-height: 18px;">CHECK OUT TIME 11:00 AM. </br>This Is A Computer Genrated Invoice. Does Not Required Signature.<br/>Subject To Ayodhya Jurisdiction only</td>
		</tr>
	</table>
	</div>
</div>


    <div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />&nbsp;&nbsp;<a href="print_combined.php?id=<?php echo $_GET['id']; ?>">Print Combined Receipt</a></div>
</body>
</html>