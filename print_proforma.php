<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
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
	$sql = 'SELECT * FROM `proforma_invoice` WHERE `sno`="'.$_GET['id'].'"';
	$result = execute_query($sql);
	$row_customer = mysqli_fetch_array($result);
	// $sql_customer = 'SELECT * FROM `customer` WHERE `sno`="'.$row['cust_id'].'"';
	// $result_customer = execute_query($sql_customer);
	// $row_customer = mysqli_fetch_array($result_customer);
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

	<div class="no-print"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />&nbsp;&nbsp;</div>
<div id="wrapper" style="page-break-after:avoid;">

<div id="tablediv">
		<table width="100%" border="0" style="border-bottom: 1px solid;">
		    <img src="images/a2.png" height="170px;" width="250px;" style="margin-left:250px" />
		<tr>
			<th colspan="3"><h3 style="text-decoration:underline;">
				<?php if($details['invoice_type']=='tax'){ ?>
				TAX INVOICE
				<?php } else{?>
				PROFARMA INVOICE
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
			<td>
				Guest Name :
			</td>
			<td>
				<?php echo strtoupper($row_customer['guest_name']);?>
			</td>
			<td>
				Serial No.
			</td>
			<td>
				<?php echo $row_customer['sno']; ?>
			</td>
			
			
		</tr>
		<tr>
			<td>
				Company Name :
			</td>
			<td>
				<?php echo strtoupper($row_customer['company_name']);?>
			</td>
			<td>
				Date
			</td>
			<td>
				<?php echo $row_customer['creation_time']; ?>
			</td>
			
			<!--<td>
				Booking Date
			</td>
			<td>
				<?php //echo date("d:m:Y" , strtotime($row_customer['booking_date'])); ?>
			</td>-->
		</tr>
		<tr> 
			<td>
				Guest GSTIN :
			</td>
			<td>
				<?php echo $row_customer['gstin']; ?>
			</td>
			<td style="">Check IN Date :</td>
			<td style="">
				<?php echo $row_customer['cindt']==NULL?"--":$row_customer['cindt']; ?>
			</td>
			
		</tr>
		
		<tr>
			<td style="">Mobile No. :</td>
			<td style="" ><?php echo $row_customer['mob_no']; ?></td>
			<td style="">Check OUT Date :</td>
			<td style="" >
				<?php echo $row_customer['coutdt']==NULL?"--":$row_customer['coutdt']; ?>
			</td>
			
			
		</tr>
		<tr>
			<td style="" >Address :</td>
			<td style="" colspan="3"><?php echo $row_customer['address']; ?></td>
			
			
		</tr>
		<tr>
			<td style="" >Remark :</td>
			<td style="" colspan="3"><?php echo $row_customer['remarks']; ?></td>
			
			
		</tr>
	</table>
	<table width="100%" class="td-center">
		<tr>
			<th>S.No</th>
			<th>Particular</th>
			<th>Rate</th>
			<th>Quantity</th>
			<th>Days</th>
			<th>Amount</th>
			<th>SGST</th>
			<th >CGST</th>
			<th>Grand Total</th>
		</tr>
		<?php 
		$i = 1;
		$tot = 0;
		$sql_particular = 'SELECT * FROM `proforma_transition` WHERE `proforma_invoice_sno`="'.$row_customer['sno'].'"';
		$result_particular = execute_query($sql_particular);
		while($row_particular = mysqli_fetch_array($result_particular)){
			$tot += $row_particular['total'];
			?>
		<tr style="line-height: 10mm;">
			<td><?php echo $i++; ?></td>
			<td><?php echo $row_particular['particulars']; ?></td>
			<td><?php echo $row_particular['rate']; ?></td>
			<td><?php echo $row_particular['quantity']; ?></td>
			<td><?php echo $row_particular['days']; ?></td>
			<td><?php echo $row_particular['amount']; ?></td>
			<td><?php echo $row_particular['sgst']; ?></td>
			<td><?php echo $row_particular['cgst']; ?></td>
			<td><?php echo $row_particular['total']; ?></td>
		</tr>
			<?php
		}
		$sp =  100 - (8*$i);
		$sp = round($sp);
		echo '<tr><td style="line-height:'.$sp.'mm;" colspan="8">&nbsp;</td></tr>';
		?>
		<tr>
			<th colspan="8" style="border-top: 1px solid black;border-bottom: 1px solid black;text-align: right;">Total :</th>
			<th style="border-top: 1px solid black;border-bottom: 1px solid black;"><?php echo $tot; ?></th>
		</tr>
		<tr>
			<th align="right" colspan="8">Round Off:</th>
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
			<th align="right" colspan="8">Amount Payable :</th>
			<th>
				<?php
				echo round($tot);
				?>
			</th>
		</tr>
		<tr>
			
		</tr>
		<tr>
			<td colspan="8"><h3 style="text-transform: capitalize;">Amount Payable : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
			<td style="text-align: right;" colspan="">For :BEDIS DREAM LAND<br /><br /></td>
		</tr>
		<tr>
			<th colspan="8" ><p style="margin-top:-10px;">CHECK OUT TIME 11:00 AM. &nbsp; THANK YOU<br> &nbsp; &nbsp; Subject To Ayodhya Jurisdiction only<br></p><p style="border: 1px solid black;"><span ></span></p></th>
			<th style="text-align: right;"><br />(Authorised Signatory)</th>
		</tr>
	</table>
</div>
</body>
</html>