<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
error_reporting(E_ALL);
logvalidate('','');
$colspan=0;
if(isset($_GET['id'])){
	$sql= 'select * from dummy_room where sno="'.$_GET['id'].'"';
	$details=mysqli_fetch_assoc(execute_query($sql));
	$days = get_days($details['checkin_date'], $details['checkout_date']);
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
		
		<tr>
			<th rowspan="5" align="left" width="5%"><image src="images/a2.jpeg" height="100px;" width="100px;"/></th>
&nbsp;&nbsp;&nbsp;
			<th colspan="">
				<span style="text-decoration:underline;font-size: 20px; margin-left:-230px;">
					DUMMY INVOICE
				</span><br>
			</th>
		</tr>
		<tr>
			
			<th align="left" colspan="3" ><h2 style="margin-left:-153px;font-size:42px;">HOTEL PRAKASH INN</h2></th>
		</tr>
		<tr>
			
			<th align="left" colspan="3" ><h4 style="margin-left:-120px;font-size:25px;">(A Unit Of Better And Better)</h4></th>
		</tr>
		<tr>
			<th colspan="3"  align="left"><span style="font-size: 18px;margin-left:-130px;" >B3/14, Vinay Khand-3, Gomti Nagar, Lucknow-226010</span><span style="font-size: 18px;margin-left:-150px;"></span></th>
		</tr>
		<tr>
			<th colspan="3"  align="left"><span style="font-size: 14px;margin-left:-110px;margin-top:20px;" ><b>Contact No.:+91 7991992999 </span><span style="font-size:15px;margin-left:-13px;" >E-Mail :sv967857@gmail.com</b></span></th>
		</tr>
		<tr>
			<th colspan="3"  align="left"><span style="font-size: 14px;margin-left:300px;margin-top:20px;" ><b>GSTIN : 09AAZFB2216Q1ZP,</span>&nbsp;&nbsp;<span style="font-size:15px;margin-left:-13px;" > PAN: AAZFB2216Q</b></span></th>
		</tr>
		<tr>
			<th colspan="3" align="left"><span style="font-size: 15px;margin-left:-180px;" >&nbsp;</span></th>
			
		</tr>
		<tr>
			<th width="50%;"></th>
			<th width="50%;"></th>
			
			</th>
		</tr>
	</table>
</div>
	<table width="100%" style="border-bottom: 1px solid;">
		<tr>
			<td>Guest Name :</td>
			<td><?php echo strtoupper($details['guest_name']); ?></td>
			<td>Address :</td>
			<td><?php echo $details['address']; ?></td>
			
		</tr>
		<tr>
			<td>Company Name :</td>
			<td><?php echo strtoupper($details['company_name']); ?></td>
			<td>Mobile No. :</td>
			<td><?php echo $details['mobile_number']; ?></td>
			
		</tr>
		<tr>
			<td style="border-bottom: 1px solid;">&nbsp;</td>
			<td style="border-bottom: 1px solid;">&nbsp;</td>
			<td style="border-bottom: 1px solid;">&nbsp;</td>
			<td style="border-bottom: 1px solid;">&nbsp;</td>
		</tr>
		<tr>
			<td>Invoice No :</td>
			<td>HPI<?php echo '/'.$details['sno']; ?></td>
			<td>Date :</td>
			<td><?php echo date("d-m-Y"); ?></td>
		</tr>
		
		<tr>
			<td>Check In :</td>
			<td><?php echo date("d-m-Y H:i:s",strtotime($details['checkin_date'])); ?></td>
			<td>Check Out :</td>
			<td><?php echo date("d-m-Y H:i:s",strtotime($details['checkout_date'])); ?></td>
		</tr>
	</table>
	<table width="100%" class="td-center">
		
		<?php if($details['extra_bed'] != '0' && $details['extra_bed'] != ''){?>
		
		<tr>
			<th width="10%">Date</th>
			<th width="10%">Room<br/> No</th>
			<th width="10%">Room<br/> Rent</th>
			<th width="10%">Extra<br/> Bed</th>
			<th width="10%">Discount</th>
			<th width="20%">Taxable<br/> Rent</th>
			<th width="10%">CGST</br> (<?php //echo $invoice['cgst']; ?>6%)</th>
			<th width="10%">SGST </br>(<?php //echo $invoice['sgst']; ?>6%)</th>
			<th width="20%">Net<br/> Price</th>
			
		</tr>
		<tr>
			<th colspan = "9">
				<hr>&nbsp;</hr>
			</th>
		</tr>
		<?php
		
		
		$exit = $details['checkout_date'];
		$tot_base_rent=0;
		$tot_extra_bed=0;
		$tot_tax = 0;
		$tot=0;
		$tot_discount=0;
		$tot_taxable_amount=0;
		
		$full_page = 250;
		$header_size = 90;
		$footer_size = 45;
		$row_height = 8;
		$page_size = $header_size + $footer_size + $row_height;
		$page = 1;
		$sql = 'select * from dummy_room_invoice where invoice_no="'.$details['sno'].'"';
		$invoice_result = execute_query($sql);
		while($invoice = mysqli_fetch_assoc($invoice_result)){
			$start = date("d-m-Y H:i:s", strtotime($details['checkin_date']));
			$tax_rate = $invoice['taxable_amount'] * ($invoice['cgst']/100);
			for($i=0; $i<$days; $i++){
				$remaining = $full_page - $page_size;
				
			
				
			?>
			<tr>
				<th width="10%"><?php echo date("d-m-Y",strtotime($start)); ?></th>
				<th width="10%"><?php echo $invoice['room_no']; ?></th>
				<th width="10%"><?php echo $invoice['base_rent']; ?></th>
				<th width="10%"><?php echo $invoice['extra_bed']; ?></th>
				<th width="10%"><?php echo $invoice['discount']; ?></th>
				<th width="20%"><?php echo $invoice['taxable_amount']; ?></th>
				<th width="10%"><?php echo $tax_rate; ?></th>
				<th width="10%"><?php echo $tax_rate; ?></th>
				<th width="20%"><?php echo $invoice['total']; ?></th>
			</tr>
			
			
			
			<?php
				$tot_tax += $tax_rate;
				$tot_base_rent+=$invoice['base_rent'] ;
				$tot_taxable_amount+=$invoice['taxable_amount'];
				$tot_extra_bed+=$invoice['extra_bed'];
				$tot_discount+=$invoice['discount'];
				$tot+=$invoice['total'];
				
			$start = date("d-m-Y H:i:s", strtotime($start)+86400);
			}
		}
		
		echo '<tr><th colspan="'.$colspan.'" style="line-height:'.($remaining-10).'mm;">&nbsp;</th></tr>';
		?>
		<tr>
			<th colspan="6">&nbsp;</th>
		</tr>
		<tr>
			<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>
			<?php
			
			$colspan=9;
		
		echo '
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">&nbsp;</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_base_rent,2).'</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_extra_bed,2).'</td>
			
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_discount,2).'</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_taxable_amount,2).'</td>
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
		<?php 
		}elseif ($details['extra_bed'] == '0'){?>
			
		<tr>
			<th width="10%">Date</th>
			<th width="10%">Room No</th>
			<th width="10%">Room Rent</th>
			
			
			<th width="20%">Taxable Rent</th>
			<th width="10%">CGST</br> (<?php //echo $invoice['cgst']; ?>6%)</th>
			<th width="10%">SGST </br>(<?php //echo $invoice['sgst']; ?>6%)</th>
			<th width="20%">Net Price</th>
		</tr>
		<tr>
			<th colspan = "9">
				<hr>&nbsp;</hr>
			</th>
		</tr>
		<?php
		
		
		$exit = $details['checkout_date'];
		$tot_base_rent=0;
		$tot_extra_bed=0;
		$tot_tax = 0;
		$tot=0;
		$tot_discount=0;
		$tot_taxable_amount=0;
		
		$full_page = 250;
		$header_size = 90;
		$footer_size = 45;
		$row_height = 8;
		$page_size = $header_size + $footer_size + $row_height;
		$page = 1;
		$sql = 'select * from dummy_room_invoice where invoice_no="'.$details['sno'].'"';
		$invoice_result = execute_query($sql);
		while($invoice = mysqli_fetch_assoc($invoice_result)){
			$start = date("d-m-Y H:i:s", strtotime($details['checkin_date']));
			$tax_rate = $invoice['taxable_amount'] * ($invoice['cgst']/100);
			for($i=0; $i<$days; $i++){
				$remaining = $full_page - $page_size;
				
			
				
			?>
			<tr>
				<th width="10%"><?php echo date("d-m-Y",strtotime($start)); ?></th>
				<th width="10%"><?php echo $invoice['room_no']; ?></th>
				<th width="10%"><?php echo $invoice['base_rent']; ?></th>
				
				
				<th width="20%"><?php echo $invoice['taxable_amount']; ?></th>
				<th width="10%"><?php echo $tax_rate; ?></th>
				<th width="10%"><?php echo $tax_rate; ?></th>
				<th width="20%"><?php echo $invoice['total']; ?></th>
			</tr>
			
			
			
			<?php
				$tot_tax += $tax_rate;
				$tot_base_rent+=$invoice['base_rent'] ;
				$tot_taxable_amount+=$invoice['taxable_amount'];
				$tot_extra_bed+=$invoice['extra_bed'];
				$tot_discount+=$invoice['discount'];
				$tot+=$invoice['total'];
				
			$start = date("d-m-Y H:i:s", strtotime($start)+86400);
			}
		}
		
		echo '<tr><th colspan="'.$colspan.'" style="line-height:'.($remaining-10).'mm;">&nbsp;</th></tr>';
		?>
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
		<tr>
			<th width="10%" style="border-top: 1px solid; border-bottom: 1px solid;">Total : </th>
			<?php
			
			$colspan=8;
		
		echo '
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">&nbsp;</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_base_rent,2).'</td>
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_extra_bed,2).'</td>
			
			
			<th width="" style="border-top: 1px solid; border-bottom: 1px solid;">'.round($tot_taxable_amount,2).'</td>
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
		
		<?php 
		}?>
		<tr>
			<td colspan="<?php echo $colspan-1; ?>"><h3 style="text-transform: capitalize;">Amount Payable : <?php echo int_to_words(round($tot,0)); ?> Rupees Only</h3></td>
			<td colspan="2" style="text-align: right;">For : HOTEL PRAKASH INN<br /><br /></td>
		</tr>
		<tr><th colspan="<?php echo $colspan-1; ?>"><p>CHECK OUT TIME 12 NOON. &nbsp; THANK YOU<br> &nbsp; &nbsp; Subject To Lucknow Jurisdiction only</p></th>
		<th><td colspan="2" style="text-align: right;"><br />(Authorised Signatory)</td></th></tr>
	</table>
</div>
</body>
</html>