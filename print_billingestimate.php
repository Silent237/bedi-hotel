<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
logvalidate('','');
$colspan=0;
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
if(isset($_GET['id'])){
	$sql= 'select * from billing_estimate where sno="'.$_GET['id'].'"';
	$details=mysqli_fetch_array(execute_query($sql));
		
	
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

	<div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>
<div id="wrapper" style="page-break-after:avoid;">
<div id="tablediv">
		<table width="100%" border="0" style="border-bottom: 1px solid;">
		    <img src="images/a2.png" height="170px;" width="250px;" style="margin-left:250px;" />
		<tr>
			<th colspan="3"><h3 style="text-decoration:underline;">
				<?php if($details['invoice_type']=='tax'){ ?>
				ESTIMATE
				<?php } else{?>
				ESTIMATE
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
			<td>Invoice No :</td>
			<td>BDL/<?php echo $details['sno']; ?></td>
			<td>Invoice Date :</td>
			<td><?php echo date("d-m-Y"); ?></td>
			
		</tr>
		<tr>
			<td>Guest Name :</td>
			<td><?php echo strtoupper($details['guest_name']); ?></td>
			<td>Booking/Event Date :</td>
			<td><?php echo $details['booking_date']; ?></td>
			
		</tr>
		<tr>
			<td>Address : </td>
			<td><?php echo $details['address']; ?></td>
			<td>Contact Number : </td>
			<td><?php echo $details['contact_number']; ?></td>
			
		</tr>
		<tr>
			<td>Total Amount  :</td>
			<td><b><?php echo $details['total_amount']; ?></b></td>
			<td>Advance Amount :</td>
			<td><b><?php echo $details['advance_amount']; ?></b></td>
		</tr>
		<tr>
			<td>Mode of Payment  :</td>
			<td><?php echo strtoupper($details['mop']); ?></td>
			
		</tr>
		
	</table>
	<table width="100%" >
		<tr>
			<td><b style="font-size:22px; line-height:24px;">Particular : </b> <?php echo $details['particular']; ?></td>
			
		</tr>
		<tr>
			<td>&nbsp; </td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table width="100%">
	<hr style="border: 1px solid #000000;"></hr>
		<h4 style="text-align:center; margin-left:40px;">Terms & Condition</h4>
		
				<ol style="text-align:center;">
					<li>No Cash Refundable</li>
					<li>Check Out Time 12:00 Noon</li>
				</ol>
			
	</table>
</div>
</body>
</html>