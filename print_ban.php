<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
logvalidate('','');
$colspan=0;
if(isset($_GET['id'])){
	$sql = 'SELECT * FROM `banquet_hall` WHERE `sno`="'.$_GET['id'].'"';
	$result = execute_query($sql);
	$row = mysqli_fetch_array($result);
	$sql_customer = 'SELECT * FROM `customer` WHERE `sno`="'.$row['cust_id'].'"';
	$result_customer = execute_query($sql_customer);
	$row_customer = mysqli_fetch_array($result_customer);
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
<div class="printout" style="text-align:center;">
<div class="no-print"><input type="button" class="large" id="btnPrint" onclick="window.print();" value="Print Page" />&nbsp;&nbsp;</div>
<div id="wrapper" style="page-break-after:avoid;">
<div id="tablediv">
		<table width="100%" border="0" style="border-bottom: 1px solid;">
		    <img src="images/a2.png" height="150px;" width="150px;" style="top:35px;left:15px; object-fit:cover;" />
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
<!--<div id="tablediv">
<table width="100%" border="0" style="border-bottom: 1px solid;padding-left:4rem;position:relative; ">
		<!--<img src="images/a2.jpeg" height="100px;" width="100px;" style="position:absolute;top:35px;left:15px;" />-->
	<!--	<tr >
			
			<th style="padding-bottom:10px!important;">
				<span style="text-decoration:underline;font-size: 20px; ">
					TAX INVOICE
				</span><br>
			</th>
		</tr>
		<tr>
			<th style="padding-bottom:10px!important;">
				<h2 style="font-size:35px;">HOTEL RAJ PALACE</h2>
			</th>
		</tr>
		<tr>
			<th style="padding-bottom:6px!important;"><span style="font-size: 20px;" >Deokali-Ftehganj Road, Wazirganj Japti <br>Near Primary School, Ayodhya-224001 (U.P)</span></th>
		</tr>
		<tr>
			<th style="padding-bottom:6px!important;" ><span style="font-size: 14px;" ><b>Mob No : 02578-316015 +91 9335452112, +91 7755004900 <br> E-Mail : hotelrajpalace.biz@gmail.com, Website: www.hotelrajpalace.biz</b></span></th>
		</tr>
		<tr>
			<th style="padding-bottom:6px!important;" ><span style="font-size: 14px;" ><b>GSTIN : 09AAGCH5319E1ZN,</span><span style="font-size:15px;"> PAN:  AAGCH5319E</b></span></th>
		</tr>
		
	</table>
</div>-->
	<table width="100%" style="border-bottom: 1px solid;">
		<tr>
			<td width="20%" >Guest Name :</td>
			<td colspan="3"> <?php echo strtoupper($row_customer['cust_name']);?> </td>
			
		</tr>
		<tr>
		    <td>Company Name :</td>
		    <td colspan="3"><?php echo strtoupper($row_customer['company_name']);?></td>
		</tr>
		<tr>
		    <td>Company Gstin :</td>
		    <td colspan="3"><?php echo $row_customer['id_2']; ?></td>
		</tr>
		<tr>
		    <td style="">Address : </td>
		    <td style="" colspan="3"><?php echo $row_customer['address']; ?></td>
		</tr>
		<tr>
		    <td style="">Mobile No. : </td>
		    <td style="" colspan="3"><?php echo $row_customer['mobile']; ?></td>
		</tr>
		<?php 
		    if($row_customer['id_1'] !=''){
    		    ?>
    		    <tr>
    		        <td>
    		            SAC/HSN :
    		        </td>
    		        <td colspan="3">
    		            <?php
    		           echo $row_customer['id_1'];
    		            ?>
    		        </td>
    		    </tr>
    	        <?php
    		}
	    ?>
		
		<tr>
		    <td style="border-bottom: 1px solid;"></td>
		    <td style="border-bottom: 1px solid;" colspan="3"></td>
		</tr>
		
		<tr>
			<td>Invoice No :</td>
			<td>BDL/BAN/<?php echo $row['financial_year'].'/'.$row['invoice_no']; ?></td>
			<td>Date :</td>
			<td><?php if($row['booking_date'] != ''){echo date('d-m-Y',strtotime($row['booking_date']));}else{echo date("d-m-Y",strtotime($row['check_in_date']));} ?></td>
		</tr>
		<tr>
			<?php if($row['check_in_date'] != ''){ ?>
			<td>Event Date :</td>
			<td><?php echo date("d-m-Y H:i:s",strtotime($row['check_in_date'])); ?></td>
			<?php }
			else{
				echo '<td>&nbsp;</td>';
			}
			?>
			<!--<?php if($row['check_out_date'] != ''){ ?>
			<td>Check Out :</td>
			<td><?php echo date("d-m-Y H:i:s",strtotime($row['check_out_date'])); ?></td>
			<?php }
			else{
				echo '<td>&nbsp;</td>';
			}
			?>-->
			<td>Hall Type :</td>
			<td><?php echo $row['hall_type']; ?></td>
		</tr>
	</table>
	<table width="100%" class="td-center">
		<tr>
			<th>S.No</th>
			<th>Particular</th>
			<th>Rate</th>
			<th>Quantity</th>
			<th>Amount</th>
			<th>SGST 9%</th>
			<th>CGST 9%</th>
			<th>Grand Total</th>
		</tr>
		<?php 
		$i = 1;
		$tot = 0;
		$sql_particular = 'SELECT * FROM `banquet_particular` WHERE `banquet_id`="'.$row['sno'].'"';
		$result_particular = execute_query($sql_particular);
		while($row_particular = mysqli_fetch_array($result_particular)){
			$tot += $row_particular['grand_total'];
			?>
		<tr style="line-height: 10mm;">
			<td><?php echo $i++; ?></td>
			<td><?php echo $row_particular['particular']; ?></td>
			<td><?php echo $row_particular['rate']; ?></td>
			<td><?php echo $row_particular['quantity']; ?></td>
			<td><?php echo $row_particular['amount']; ?></td>
			<td><?php echo $row_particular['sgst']; ?></td>
			<td><?php echo $row_particular['cgst']; ?></td>
			<td><?php echo $row_particular['grand_total']; ?></td>
		</tr>
			<?php
		}
		$sp =  110 - (8*$i);
		$sp = round($sp);
		echo '<tr><td style="line-height:'.$sp.'mm;" colspan="8">&nbsp;</td></tr>';
		?>
		<tr>
			<th colspan="7" style="border-top: 1px solid black;border-bottom: 1px solid black;text-align: right;">Total :</th>
			<th style="border-top: 1px solid black;border-bottom: 1px solid black;"><?php echo $tot; ?></th>
		</tr>
		<tr>
			<th align="right" colspan="7">Round Off:</th>
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
			<th align="right" colspan="7">Amount Payable :</th>
			<th>
				<?php
				echo round($tot);
				?>
			</th>
		</tr>
		<tr>
			
		</tr>
		<tr>
			<td colspan="7"><h3 style="text-transform: capitalize;">Amount Payable : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
			<td style="text-align: right;" colspan="">For : BEDIS DREAM LAND<br /><br /></td>
		</tr>
		<tr>
			<th colspan="7"><p> &nbsp; THANK YOU<br> &nbsp; &nbsp; Subject To Ayodhya Jurisdiction only</p></th>
			<th style="text-align: right;"><br />(Authorised Signatory)</th>
		</tr>
	</table>
</div>
</div>
</body>
</html>