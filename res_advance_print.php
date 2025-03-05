<?php 
date_default_timezone_set('Asia/Calcutta');
include ("scripts/settings.php"); 
$sql = 'select * from general_settings where `desc`="company"';
$company = mysqli_fetch_assoc(execute_query($sql));
$company = $company['rate'];

$sql = 'select * from general_settings where `desc`="slogan"';
$slogan = mysqli_fetch_assoc(execute_query($sql));
$slogan = $slogan['rate'];

$sql = 'select * from general_settings where `desc`="dealer"';
$dealer = mysqli_fetch_assoc(execute_query($sql));
$dealer = $dealer['rate'];

$sql = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_assoc(execute_query($sql));
$address = $address['rate'];

$sql = 'select * from general_settings where `desc`="contact"';
$contact = mysqli_fetch_assoc(execute_query($sql));
$contact = $contact['rate'];

$sql = 'select * from general_settings where `desc`="gstin"';
$gstin = mysqli_fetch_assoc(execute_query($sql));
$gstin = $gstin['rate'];

$sql = 'select * from general_settings where `desc`="pan"';
$pan = mysqli_fetch_assoc(execute_query($sql));
$pan = $pan['rate'];

$sql = 'select * from general_settings where `desc`="invoice_prefix"';
$invoice_prefix = mysqli_fetch_assoc(execute_query($sql));
$invoice_prefix = $invoice_prefix['rate'];

$sql = 'select * from general_settings where `desc`="firm_type"';
$firm_type = mysqli_fetch_assoc(execute_query($sql));
$firm_type = $firm_type['rate'];

$sql = 'select * from general_settings where `desc`="bill_style"';
$bill_style = mysqli_fetch_assoc(execute_query($sql));
$bill_style = $bill_style['rate'];

$sql = 'select * from general_settings where `desc`="terms"';
$terms = mysqli_fetch_assoc(execute_query($sql));
$terms = $terms['rate'];

$sql = 'select * from general_settings where `desc`="bank"';
$bank = mysqli_fetch_assoc(execute_query($sql));
$bank = $bank['rate'];

$sql = 'select * from general_settings where `desc`="jurisdiction"';
$jurisdiction = mysqli_fetch_assoc(execute_query($sql));
$jurisdiction = $jurisdiction['rate'];

$sql = 'select * from general_settings where `desc`="software_type"';
$software_type = mysqli_fetch_assoc(execute_query($sql));
$software_type = $software_type['rate'];

$sql = 'select * from general_settings where `desc`="Print Table No On Bill"';
$tabl = mysqli_fetch_assoc(execute_query($sql));
$tableno = $tabl['rate'];


$sql_invoice = 'select * from res_advance_booking where sno="'.$_GET['print_id'].'"';
$invoice=mysqli_fetch_assoc(execute_query($sql_invoice));
$sql_cust = 'SELECT * FROM `customer` WHERE `sno`="'.$invoice['cust_id'].'"';
$cust = mysqli_fetch_array(execute_query($sql_cust));
$sql_mop = 'SELECT * FROM `customer_transactions` WHERE `advance_booking_id`="'.$invoice['sno'].'" ';
$row_mop = mysqli_fetch_array(execute_query($sql_mop));
$style = 'thermal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>ADVANCE INVOICE</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
	
    .fontsize{
       margin:0px!important; 
    }
    .term{
        margin:0px!important; 
        font-size:10px;
    }
    #invoiceno{
         margin:0px!important;
         font-size:15px;
         margin-top:-25px!important;
    }
  </style>

</head>
<body>
		<!--<input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />-->
		<div id="wrapper" style="border: 1px solid; padding:10px; ">
			<div id="bill_detail">
				<table width="100%" border="0" style="text-align:center">
				    <img src="images/a2.png" height="120px;" width="120px;" style="position:absolute;top:25px;left:15px; object-fit:cover;" />
					<tr>
						<th colspan="3"><h5 style="text-decoration:underline;">
							ADVANCE RECEIPT
							</h5></th>
					</tr>
					<tr>
						<th colspan="3"><h4 class="fontsize">BEDI'S DREAM LAND HOTEL AND RESORT</h4></th>
					</tr>
					<tr>
						<th colspan="3"><h6 class="fontsize">Maheshpur, Near Saryu Bridge<br>Ayodhya-224001 (U.P)</h6></th>
					</tr>
					<tr>
						<th colspan="3"><h6 class="fontsize"></h6>Mob No :+91 8989441919, +91 8400334035/34 <br> E-Mail : bedisdreamland@gmail.com , Website: www.bedisdreamland.com</h6></th>
					</tr>
					<tr class="fontsize">
						<th><center></center><h6>GSTIN : 09AAOFB3645G1ZA</h6></center></th>
					</tr>
				</table>
	
			</div>
			<div id="invoiceno">
				<table style="border:none;" id="noborder" width="100%">
				    <tr><td colspan="4"><hr><td></tr>
					<tr>
					<td  width="50%" colspan="2">Guest Name : <?php echo $cust['cust_name'];?></td>
					<td width="50%" colspan="2">Receipt No. : <?php echo $invoice['sno'];?></td>
					</tr>
					<tr>
					<td  width="50%" colspan="2">Company Name : <?php echo $cust['company_name'];?></td>
					<td width="50%" colspan="2"> Date: <?php echo date("d-m-Y",strtotime($invoice['created_on']));?></td>
					</tr>
					<tr>
						<td  width="50%" colspan="2">GSTIN No : <?php echo $cust['id_2'];?></td>
						<td width="50%" colspan="2">Check In Date : <?php echo date('d-m-Y h-i A' , strtotime($invoice['check_in'])); ?></td>
					</tr>
					<tr>
						<td width="50%" colspan="2">Address : <?php echo $cust['address']; ?></td>
						<td width="50%" colspan="2">Check Out Date : <?php echo date('d-m-Y h-i A' , strtotime($invoice['check_out'])); ?></td>
					</tr>
					<tr>
						<td  width="50%" colspan="2">Mobile No: <?php echo $cust['mobile'];?></td>
						<td colspan="2">Mode Of Payement : <span style="text-transform:uppercase"><?php if($row_mop['mop'] == "bank_transfer"){echo 'BANK TRANSFER';}elseif($row_mop['mop'] == "card_sbi"){echo 'CARD S.B.I.';}elseif($row_mop['mop'] == "card_pnb"){echo 'CARD P.N.B.';}else{echo strtoupper($row_mop['mop']);} ?></span></td>
					</tr>
					<tr>
					<?php if($invoice['number_of_room'] != ''){ ?>
					
						<td colspan="2">No. Of Rooms : <?php echo $invoice['number_of_room']; ?></td>
					
					<?php } ?>
					<?php if($invoice['room_number'] != ''){ ?>
						<td colspan="2">Room Number : <?php echo $invoice['room_number']; ?></td>
					
					<?php } ?>
					
					</tr>
					<tr>
						
						
					</tr>
				</table>
			</div>
				<table border="0" bordercolor="#ccc"  cellpadding="0"  cellspacing="0" width="100%">
					<div id="bill">
						<thead>
						<tr style="height:25px">
							<th style="border:2px solid;width:25%;font-size: 14px">S.No</th>
							<th style="border:2px solid;width:50%;font-size: 14px">Advance Type</th>
							<th style="border:2px solid;width:25%;font-size: 14px">Amount</th>
						 </tr>        
						</thead>
						<?php 
							$advance_type = '';
							if($invoice['purpose'] == "room_rent"){
								$advance_type = 'Room Booking';
							}
							elseif($invoice['purpose'] == "banquet_rent"){
								$advance_type = 'Banquet Booking';
							}
							elseif($invoice['purpose'] == "advance_for"){
								$sql_for = 'SELECT* FROM `res_advance_booking` WHERE `sno`="'.$invoice['advance_for_id'].'"';
								$row_for = mysqli_fetch_array(execute_query($sql_for));
								if($row_for['purpose'] == 'room_rent'){
									$advance_type = 'Room Booking(Plus Amount)';
								}
								elseif($row_for['purpose'] == 'banquet_rent'){
									$advance_type = 'Banquet Booking(Plus Amount)';
								}	
							}
							elseif($invoice['purpose'] == "advance_for_checkin"){
								$advance_type = 'Room Booking(In House Guest)';
							}
							echo '<tr><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">1</th><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">Total Amount</th><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">'.$invoice['total_amount'].'</th></tr>';
		 					echo '<tr><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">2</th><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">Advance Amount</th><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">'.$invoice['advance_amount'].'</th></tr>';
		 					echo '<tr><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">3</th><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">Due Amount</th><th style="border:2px solid;width:25%;font-size: 12px;line-height:18px;">'.$invoice['due_amount'].'</th></tr>';
						?>
					<!--	<tr>
							<td colspan="3" style="border:1px solid;font-size: 14px;line-height: 18px;">Rs.: <?php 
							$tot_amount = round(($invoice['due_amount']),0);
							echo strtoupper(int_to_words($tot_amount)); ?> ONLY</td>
						</tr>-->
						<?php
						if($invoice['remarks']!=''){
						?>
						<tr>
							<td colspan="3" style="border:1px solid;font-size: 14px;line-height: 18px;">Remarks :<?php echo $invoice['remarks'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="3" style="border:none;"class="term">
								<div style="width: 100%; border: 0px solid; float: left;">
									<b><u>Terms &amp; Condition</u></b><br/>
								    A)- ADVANCE WILL NOT BE REFUNDABLE.
								</div>

							</td>
						</tr>
						<tr><td colspan="3" style="text-align:center; text-decoration:bold;"></td></tr>
					</div>
				</table>
		</div>


</body>
</html>
