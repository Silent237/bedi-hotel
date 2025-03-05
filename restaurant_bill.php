<?php 
include ("settings.php"); 

if(!isset($_GET['style'])){
	$_GET['style']='full';
}
if(isset($_GET['id'])){
	$_SESSION['invoice_no'] = $_GET['id'];
}
if(isset($_GET['inv'])){
	$_SESSION['invoice_no'] = $_GET['inv'];
}

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

//$sql = 'select * from general_settings where `desc`="pan"';
//$pan = mysqli_fetch_assoc(execute_query($sql));
//$pan = $pan['rate'];

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

/*$company = "WebPro Technologies";
$slogan = "";
$dealer = "Website Designing | Software Development | Graphic Designing | SMS Services | IT Consultancy | Hardware Supply | Annual Maintenance Contract | Technical Training | Office Automation | CCTV | PABX";
$address = "127, Shakti Nagar<br />
		Devkali Road<br />
        Faizabad";
$contact = "Mobile No. 9554969777, 9554969778<br />
        Website: www.webprotechnologies.com<br />
        E-Mail: info@webprotechnologies.com<br />
        <b>
        GSTIN No.: 09CJZPS8678A1Z5<br />
        PAN No.: CJZPS8678A</b>";
		
$terms = "1.Goods once sold will not be taken back. 2.Replacement of goods will be entertained between 12pm to 4pm. 3.No cash refundable. 4.Goods shall remain the property of the supplier until payment in full has been made. The supplier reserves the right to enter the PURCHASES premises to recover goods remaining unpaid for after the due payment date. 5.Complaints including breakages shortages must be reported within 7 days from the date of receipt of goods. No claim thereafter will be entertained. 6.E. & O.E.";
//$bank = "Bank of Baroda : 37850200000169 (IFSC:BARB0DEOKAL)";
$jurisdiction = "Faizabad";

*/
$sql_invoice = 'select * from invoice_sale_restaurant where sno="'.$_SESSION['invoice_no'].'"';
$invoice=mysqli_fetch_assoc(execute_query($sql_invoice));

$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
$res = execute_query($sql1); 
$stock_sale_restaurant=mysqli_fetch_assoc($res);

$sql_available = 'select * from stock_available where sno="'.$stock_sale_restaurant['part_id'].'"';
$avail=mysqli_fetch_assoc(execute_query($sql_available));

$sql_customer = 'select *, id_2 as tin, "" as country, "" as pan from customer where sno="'.$invoice['supplier_id'].'"';
$cust=mysqli_fetch_assoc(execute_query($sql_customer));

$_GET['style'] = $bill_style;

if(isset($_GET['style'])){
	if($_GET['style']=='full_page'){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SALE INVOICE</title>
		<link href="../css/pop_full_page.css" TYPE="text/css" REL="stylesheet" media="all">
		<style type="text/css">
		@media print {
			input#btnPrint {
				display: none;
			}
		}
		</style>
		<script language="javascript" type="text/javascript">
		//window.print();
		</script>
	</head>
	<body>
		<div id="wrapper">
			<input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />
			<input type="button" id="btnPrint" onclick="location.href='../sale_new.php?id=<?php echo $_SESSION['invoice_no']; ?>';" value="Edit" />
			<input type="button" id="btnPrint" onclick="location.href='../report_sale.php?del=<?php echo $_SESSION['invoice_no']; ?>'; return confirm('Are you sure?')" value="Delete" />
			<div id="company_info">
				<?php if($firm_type=="non_composition"){?>
				<center><p style="font-size:24px;"><?php if($invoice['invoice_type']=='SALE'){echo "INVOICE";}elseif($invoice['invoice_type']=='TAX'){echo "INVOICE";}else{echo "";}?></p></center>
				<?php } else{?>
				<center><p style="font-size:24px;"><?php if($invoice['invoice_type']=='sale'){echo "BILL OF SUPPLY";}elseif($invoice['invoice_type']=='TAX'){echo "BILL OF SUPPLY";}else{echo "";}?></p></center>
				<?php } ?>
				<div id="company">
					<div id="logo"><img src="../images/logo.gif"/><?php echo $company; ?></span></div><br />
					<div id="slogan"><?php echo $slogan; ?></div>
					<div id="dealer" style="line-height:20px;"><?php echo $dealer; ?></div>
				</div>
				<div id="address">
					<p style="font-size:18px; font-weight:bold; line-height:17px;"><?php echo $address; ?></p>
					<?php echo $contact; ?>
					<?php 
						if($gstin!=''){
							echo "<br/><strong>GSTIN : $gstin</strong>";
						}
					?>
					<?php 
						//if($pan!=''){
							//echo "<br/><strong>PAN : $pan</strong>";
						//}
					?>
				</div>
			</div>
			<div id="invoice">
				<div id="bill_detail">
					<table border="0" bordercolor="#ccc" cellpadding="0" cellspacing="0" width="100%"> 
					<tr><td style="border:1px solid;">
						<div id="party">
							To,<br />
							<?php if($invoice['concerned_person']!=''){ ?>
							<b><?php echo $invoice['concerned_person']; ?></b><br />
							<?php } ?>
							<?php echo $cust['cust_name']; ?></b><br />
							<?php
							if($invoice['department']!=''){
							?>
							Department: <?php echo $invoice['department']; ?><br />
							<?php } ?>
							<?php echo $cust['address'].'<br />'.$cust['add_2'].'<br />'.$cust['city'].'-'.$cust['zipcode'].'<br />'.$cust['state'].' - '.$cust['country'];?><br />
							<?php 
							if($cust['tin']!=''){
								echo '<strong>GSTIN: '.$cust['tin'].'</strong><br>';
							}
							//if($cust['pan']!=''){
								//echo 'PAN: '.$cust['pan'].'<br>';
							//}
							if($cust['mobile']!=''){
								echo 'Tel: '.$cust['mobile'].'<br>';
							}
							?>
						</div>
						<div id="invoiceno">
							<table style="border:none;" id="noborder">
								<tr>
								<td colspan="2"><b>Invoice No. : <?php if($invoice_prefix!=''){ echo $invoice_prefix.'/';}?><?php echo $invoice['financial_year'].'-'.($invoice['financial_year']+1); ?>/INV/<?php echo $invoice['invoice_no'];?></b></td>
								</tr>
								<tr>
								<td><b>Invoice Date: <?php echo date("d-m-Y",strtotime($invoice['timestamp']));?></b></td>
									<td><b>Book No. : <?php echo ceil($invoice['invoice_no']/50); ?></b></td>
								</tr>
								<tr>
									<td><b>Challan No : <?php echo $invoice['challan_no']; ?></b></td>
									<td><b>Order No : <?php echo $invoice['order_no']; ?></b></td>
								</tr>
								<tr>
									<td><b>Challan Date : <?php echo $invoice['challan_date']; ?></b></td>
									<td><b>Order Date : <?php echo $invoice['order_date']; ?></b></td>
								</tr>
							</table>
						</div>
					</td>
					</tr>
					</table>
				</div>
				<table border="0" bordercolor="#ccc"  cellpadding="0"  cellspacing="0">
					<div id="bill">
						<thead>
						<tr>
							<th rowspan="2" style="width:5mm;">Sno</th>
							<th rowspan="2" style="width:70mm;">Product</th>
							<th rowspan="2">HSN Code</th>
							<?php
							if($software_type=='pharma'){
								echo '<th rowspan="2">Batch</th>
								<th rowspan="2">Expiry</th>';
								
							}		
							?>
							<th rowspan="2">Qty</th>
							<th rowspan="2">Unit</th>
							<th rowspan="2">Price</th>
							<th rowspan="2">Disc</th>
							<?php if($firm_type=='non_composition'){?>
								<th rowspan="2">Taxable Amt</th>
								<th colspan="2">CGST</th>
								<th colspan="2">SGST</th>
							<?php } ?>
							<th rowspan="2">E. Price</th>
							<th rowspan="2">Total</th>
						 </tr>        
						</thead>
						<?php 
						$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
						$res= execute_query($sql1); 
						$tot_price=0;
						$tot_amount=0;
						$qty=0;
						$i=0;
						$taxable=0;
						$full_page = 290;
						$header_size = 90;
						$footer_size = 90;
						$row_header = 8;
						$row_height = 6;
						$description_height = 2;
						$page_size = $header_size + $footer_size + $row_header;
						$page = 1;
						$tot_colspan=3;
						if($firm_type=='non_composition'){
							$total_col = 14;
						}
						else{
							$total_col = 9;
						}
						if($software_type=='pharma'){
							$total_col = $total_col+2;
							$tot_colspan = $tot_colspan+2;
						}
						while($row=mysqli_fetch_assoc($res)) {
							$remaining = $full_page - $page_size;
							if($remaining<0){
								$new = $full_page - $page_size + $footer_size - 10 - 20 - 10;
								echo '<tr style="height:10mm;"><td colspan="'.$total_col.'" style="border:1px solid;">&nbsp;</td></tr>';
								echo '
								<tr style="height:10mm;">
									<th colspan="2">Total (This page) :</th>
									<th style="width:10mm;">'.$qty.'</th>
									<th colspan="'.($total_col-5).'">&nbsp;</th>
									<th style="width:15mm;">'.$tot_amount.'</th>
								</tr>';
								echo '<tr style="height:10mm;"><td colspan="'.$total_col.'" style="border:1px solid; text-align:right;">Continued on next page...</td></tr>';
								echo '<tr style="height:'.$row_header.'mm; page-break-after:always;"><td colspan="'.$total_col.'">&nbsp;</td></tr>';
								$tot_amount=0;
								$qty=0;
								$page_size=$footer_size;
								$remaining = $full_page - $page_size;
								$page++;

							}
							$page_size += $row_height;
							$sql_available = 'select * from stock_available where sno="'.$row['part_id'].'"'; 
							$avail=mysqli_fetch_assoc(execute_query($sql_available));
							$tot_price=$tot_price+$row['amount'];
							$qty=$qty+$row['qty'];
							?>
							<tr>
							<td style="border-left:1px solid; text-align:center;"><?php echo ++$i;?></td>
							<td style="border-left:1px solid; padding-left:5px;">
								<?php 
								echo htmlspecialchars_decode($avail['description'], ENT_QUOTES); 
								if($row['description']!=''){
									echo '<br /><small>('.htmlspecialchars_decode($row['description'], ENT_QUOTES).')</small>';
									$page_size += $description_height;
								}
								$sql = 'select * from barcode_new where type="sale" and number="'.$_SESSION['invoice_no'].'" and part_desc="part_desc'.$i.'"';
								$res_barcode = execute_query($sql);
								if(mysqli_num_rows($res_barcode)!=0){
									while($row_barcode=mysqli_fetch_assoc($res_barcode)){
										echo '<br /><small>('.$row_barcode['barcode'].')</small>';
										$page_size += $description_height;
									}
								}
								$tot_amount += $row['amount'];
								$qty += $row['qty'];
								?>
							</td>
							<td style="text-align:center; border-left:1px solid;"><?php echo $avail['part_no'];?></td>
							<?php
							if($software_type=='pharma'){
								echo '<td style="text-align:center; border-left:1px solid;">'.htmlspecialchars_decode($row['batch_no'], ENT_QUOTES).'</td>
								<td style="text-align:center; border-left:1px solid;">'.htmlspecialchars_decode(date("Y-m", strtotime($row['expiry'])), ENT_QUOTES).'</td>';
							}
							?>
							<td style="text-align:center; border-left:1px solid;"><?php echo $row['qty'];?></td>
							<td style="text-align:center; padding-right:5px; border-left:1px solid;"><?php echo get_unit($row['unit']);?></td>
							<?php if($firm_type!='non_composition'){
								$row['basicprice'] = $row['effective_price']+$row['discount_value'];	
							}?>
							<td id="align_right"><?php echo $row['basicprice'];?></td>
							<td id="align_right">
							<?php 
							echo $row['discount'];
							if($row['scheme']!=''){
								echo '<br /><p style="font-size:10px;">Scheme: ';
								if(strpos($row['scheme'], "%")===false){
									echo $row['scheme'].'&nbsp;'.get_unit($row['unit']);
								}
								else{
									echo $row['scheme'];
								}
								echo '</p>';
							}
							
								
							?></td>
							<?php if($firm_type=='non_composition'){?>
								<td id="align_right"><?php echo $row['taxable_amount'];?></td>
								<td id="align_right"><?php echo $row['vat'];?></td>
								<td id="align_right"><?php echo $row['vat_value'];?></td>
								<td id="align_right"><?php echo $row['excise'];?></td>
								<td id="align_right"><?php echo $row['excise_value'];?></td>
							<?php } ?>
							<td id="align_right"><?php echo $row['effective_price'];?></td>
							<td id="align_right" style="border-right:1px solid;"><?php echo $row['amount'];?></td>
							</tr>
							<?php 
							if(preg_match("/%/", $row['discount'])){
								$e_price = $row['basicprice']-($row['basicprice']*($row['discount']/100));
								//echo '@';
							}
							else{
								$e_price = $row['basicprice']-$row['discount'];
							}
							$taxable+=$e_price*$row['qty'];
						} 
						if($remaining!=0){
							$new = $remaining - $footer_size;
							if($page!=1){
								$remaining -= 35;
							}
							echo '<tr style="height:'.$remaining.'mm;">
							<td style="border-left:1px solid; text-align:center;"></td>
							<td style="border-left:1px solid; padding-left:5px;">&nbsp;</td>
							<td style="text-align:center; border-left:1px solid;">&nbsp;</td>
							<td id="align_right">&nbsp;</td>
							<td id="align_right">&nbsp;</td>
							<td id="align_right">&nbsp;</td>';
							if($firm_type=='non_composition'){
								echo '<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>';
							}
							if($software_type=='pharma'){
								echo '<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>';
							}
							echo '
							<td id="align_right">&nbsp;</td>
							<td id="align_right">&nbsp;</td>
							<td style="text-align:right; padding-right:5px; border-left:1px solid; border-right:1px solid;">&nbsp;</td>

							</tr>';
						}
						?>
						<tr>
							<td colspan="<?php echo $tot_colspan; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total :</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['quantity']; ?></td>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_disc']; ?></td>
							<?php if($firm_type=='non_composition'){?>
								<td style="text-align:right; border:1px solid;"><?php echo $invoice['taxable_amount']; ?></td>
								<td style="text-align:right; border:1px solid;">&nbsp;</td>
								<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_vat']; ?></td>
								<td style="text-align:right; border:1px solid;">&nbsp;</td>
								<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_sat']; ?></td>
							<?php } ?>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['total_amount']; ?></td>
						</tr>
						<?php
						if($invoice['other_discount']>0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Other Discount (-):</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['other_discount'];?></td>
						</tr>
						<?php } ?>
						<?php
						if($invoice['round_off']!=0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Round Off:</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['round_off'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total Amount :</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['total_amount1'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col-5; ?>" style="text-align:right; padding-right:10px; border:1px solid;">
							<?php
							if($invoice['other_expense']!=0){
								echo ' <b>Labor Charges : </b>'.$invoice['other_expense'];
							}
							if($invoice['transport_charge']!=0){
								echo ' <b>Transport Charges : </b>'.$invoice['transport_charge'];
							}
							?>
							</td>
							<td colspan="4" style="text-align:right; padding-right:10px; border:1px solid;"><b>Expenses (+):</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['other_expense']+$invoice['transport_charge'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Amount Payable:</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['grand_total'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:1px solid;">Rs.: <?php 
							$tot_amount = round(($invoice['grand_total']),0);
							echo strtoupper(int_to_words($tot_amount)); ?> ONLY</td>
						</tr>
						<?php
						if($invoice['remark']!=''){
						?>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:1px solid;"><b>Remarks :</b><?php echo $invoice['remark'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:none;">
								<div style="width: 500px; border: 0px solid; float: left;">
									<b><u>Terms &amp; Condition</u></b>
									<p id="terms"><?php echo $terms; ?></p>
								</div>
								<div style="border:none; float: right; border: 0px solid; text-align:right; width: 230px; padding-top: 10px;">
									For <?php echo $company; ?>
									<br /><br />
									<img src="../images/sign.png" height="40">
									<br /><br />
									Authorised Signatory
								</div>
								<div style="float:left; width: 230px; border: 0px solid;">
									<b>Pre Authenticated</b><br />
									For <?php echo $company; ?>
									<br />
									<img src="../images/sign.png" height="30">
									<br />
									Authorised Signatory
								</div>
								<div style="float:left; margin-top:5px; text-align:center;font-size:14px; border:0px solid; width: 280px;">
									<p style="font-weight:bold;font-size:14px; margin-bottom:10px;line-height:16px;">
									<?php echo $bank; ?><br />
									</p>
									Subject to <?php echo $jurisdiction; ?> Jurisdiction
								</div>
							</td>
						</tr>
						<tr><td colspan="<?php echo $total_col; ?>" style="text-align:center; text-decoration:bold;"></td></tr>
					</div>
				</table>
			</div>
	</body>
</html>
<?php

	}
	elseif($_GET['style']=='half_page'){
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SALE INVOICE</title>
		<link href="../css/pop_half_page.css" TYPE="text/css" REL="stylesheet" media="all">
		<style type="text/css">
		@media print {
			input#btnPrint {
				display: none;
			}
		}
		</style>
		<script language="javascript" type="text/javascript">
		//window.print();
		</script>
	</head>
	<body>
		<div id="wrapper">
			<input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />
			<input type="button" id="btnPrint" onclick="location.href='../sale_new.php?id=<?php echo $_SESSION['invoice_no']; ?>';" value="Edit" />
			<input type="button" id="btnPrint" onclick="location.href='../report_sale.php?del=<?php echo $_SESSION['invoice_no']; ?>'; return confirm('Are you sure?')" value="Delete" />
			<div id="company_info">
				<center><p style="font-size:14px;"><?php if($invoice['invoice_type']=='SALE'){echo "INVOICE";}elseif($invoice['invoice_type']=='TAX'){echo "INVOICE";}else{echo "";}?></p></center>
				<div id="company">
					<div id="logo"><img src="../images/logo.gif"/><?php echo $company; ?></span></div><br />
					<div id="slogan"><?php echo $slogan; ?></div>
					<div id="dealer" style="line-height:10px;"><?php echo $dealer; ?></div>
				</div>
				<div id="address">
					<p style="font-size:12px; font-weight:bold; line-height:12px;"><?php echo $address; ?></p>
					<?php echo $contact; ?>
					<?php 
						if($gstin!=''){
							echo "<br/><strong>GSTIN : $gstin</strong>";
						}
					?>
					<?php 
						//if($pan!=''){
						//	echo "<br/><strong>PAN : $pan</strong>";
						//}
					?>
				</div>
			</div>
			<div id="invoice">
				<div id="bill_detail">
					<table border="0" bordercolor="#ccc" cellpadding="0" cellspacing="0" width="100%"> 
					<tr><td style="border:1px solid;">
						<div id="party">
							To,<br />
							<?php if($invoice['concerned_person']!=''){ ?>
							<b><?php echo $invoice['concerned_person']; ?></b><br />
							<?php } ?>
							<?php echo $cust['cust_name']; ?></b><br />
							<?php
							if($invoice['department']!=''){
							?>
							Department: <?php echo $invoice['department']; ?><br />
							<?php } ?>
							<?php echo $cust['address'].'<br />'.$cust['add_2'].'<br />'.$cust['city'].'-'.$cust['zipcode'].'<br />'.$cust['state'].' - '.$cust['country'];?><br />
							<?php 
							if($cust['tin']!=''){
								echo '<strong>GSTIN: '.$cust['tin'].'</strong><br>';
							}
							//if($cust['pan']!=''){
								//echo 'PAN: '.$cust['pan'].'<br>';
							//}
							if($cust['mobile']!=''){
								echo 'Tel: '.$cust['mobile'].'<br>';
							}
							?>
						</div>
						<div id="invoiceno">
							<table style="border:none;" id="noborder">
								<tr>
								<td colspan="2"><b>Invoice No. : <?php if($invoice_prefix!=''){ echo $invoice_prefix.'/';}?><?php echo $invoice['financial_year'].'-'.($invoice['financial_year']+1); ?>/INV/<?php echo $invoice['invoice_no'];?></b></td>
								</tr>
								<tr>
								<td><b>Invoice Date: <?php echo date("d-m-Y",strtotime($invoice['timestamp']));?></b></td>
									<td><b>Book No. : <?php echo ceil($invoice['invoice_no']/50); ?></b></td>
								</tr>
								<tr>
									<td><b>Challan No : <?php echo $invoice['challan_no']; ?></b></td>
									<td><b>Order No : <?php echo $invoice['order_no']; ?></b></td>
								</tr>
								<tr>
									<td><b>Challan Date : <?php echo $invoice['challan_date']; ?></b></td>
									<td><b>Order Date : <?php echo $invoice['order_date']; ?></b></td>
								</tr>
							</table>
						</div>
					</td>
					</tr>
					</table>
				</div>
				<table border="0" bordercolor="#ccc"  cellpadding="0"  cellspacing="0">
					<div id="bill">
						<thead>
						<tr>
							<th rowspan="2" style="width:5mm;">Sno</th>
							<th rowspan="2" style="width:70mm;">Product</th>
							<th rowspan="2">HSN</th>
							<th rowspan="2">Qty</th>
							<th rowspan="2">UOM</th>
							<th rowspan="2">Price</th>
							<th rowspan="2">Disc</th>
							<?php if($firm_type=='non_composition'){?>
								<th rowspan="2">Tax. Amt</th>
								<th colspan="2">CGST</th>
								<th colspan="2">SGST</th>
							<?php } ?>
							<th rowspan="2">E. Price</th>
							<th rowspan="2">Total</th>
						 </tr>        
						</thead>
						<?php 
						$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
						$res= execute_query($sql1); 
						$tot_price=0;
						$tot_amount=0;
						$qty=0;
						$i=0;
						$taxable=0;
						$full_page = 200;
						$header_size = 60;
						$footer_size = 60;
						$row_header = 7;
						$row_height = 5;
						$description_height = 2;
						$page_size = $header_size + $footer_size + $row_header;
						$page = 1;
						if($firm_type=='non_composition'){
							$total_col = 14;
						}
						else{
							$total_col = 9;
						}
						while($row=mysqli_fetch_assoc($res)) {
							$remaining = $full_page - $page_size;
							if($remaining<0){
								$new = $full_page - $page_size + $footer_size - 10 - 20 - 10;
								echo '<tr style="height:10mm;"><td colspan="'.$total_col.'" style="border:1px solid;">&nbsp;</td></tr>';
								echo '
								<tr style="height:10mm;">
									<th colspan="2">Total (This page) :</th>
									<th style="width:10mm;">'.$qty.'</th>
									<th colspan="'.($total_col-4).'">&nbsp;</th>
									<th style="width:15mm;">'.$tot_amount.'</th>
								</tr>';
								echo '<tr style="height:10mm;"><td colspan="'.$total_col.'" style="border:1px solid; text-align:right;">Continued on next page...</td></tr>';
								echo '<tr style="height:'.$row_header.'mm; page-break-after:always;"><td colspan="14">&nbsp;</td></tr>';
								$tot_amount=0;
								$qty=0;
								$page_size=$footer_size;
								$remaining = $full_page - $page_size;
								$page++;

							}
							$page_size += $row_height;
							$sql_available = 'select * from stock_available where sno="'.$row['part_id'].'"'; 
							$avail=mysqli_fetch_assoc(execute_query($sql_available));
							$tot_price=$tot_price+$row['amount'];
							$qty=$qty+$row['qty'];
							?>
							<tr>
							<td style="border-left:1px solid; text-align:center;"><?php echo ++$i;?></td>
							<td style="border-left:1px solid; padding-left:5px;">
								<?php 
								echo htmlspecialchars_decode($avail['description'], ENT_QUOTES); 
								if($row['description']!=''){
									echo '<br /><small>('.htmlspecialchars_decode($row['description'], ENT_QUOTES).')</small>';
									$page_size += $description_height;
								}
								$sql = 'select * from barcode_new where type="sale" and number="'.$_SESSION['invoice_no'].'" and part_desc="part_desc'.$i.'"';
								$res_barcode = execute_query($sql);
								if(mysqli_num_rows($res_barcode)!=0){
									while($row_barcode=mysqli_fetch_assoc($res_barcode)){
										echo '<br /><small>('.$row_barcode['barcode'].')</small>';
										$page_size += $description_height;
									}
								}
								$tot_amount += $row['amount'];
								$qty += $row['qty'];
								?>
							</td>
							<td style="text-align:center; border-left:1px solid;"><?php echo $avail['part_no'];?></td>
							<td style="text-align:center; border-left:1px solid;"><?php echo $row['qty'];?></td>
							<td style="text-align:center; padding-right:5px; border-left:1px solid;"><?php echo get_unit($row['unit']);?></td>
							<td id="align_right"><?php echo $row['basicprice'];?></td>
							<td id="align_right"><?php echo $row['discount'];?></td>
							<?php if($firm_type=='non_composition'){?>
								<td id="align_right"><?php echo $row['taxable_amount'];?></td>
								<td id="align_right"><?php echo $row['vat'];?></td>
								<td id="align_right"><?php echo $row['vat_value'];?></td>
								<td id="align_right"><?php echo $row['excise'];?></td>
								<td id="align_right"><?php echo $row['excise_value'];?></td>
							<?php } ?>
							<td id="align_right"><?php echo $row['effective_price'];?></td>
							<td id="align_right" style="border-right:1px solid;"><?php echo $row['amount'];?></td>
							</tr>
							<?php 
							if(preg_match("/%/", $row['discount'])){
								$e_price = $row['basicprice']-($row['basicprice']*($row['discount']/100));
								//echo '@';
							}
							else{
								$e_price = $row['basicprice']-$row['discount'];
							}
							$taxable+=$e_price*$row['qty'];
						} 
						if($remaining!=0){
							$new = $remaining - $footer_size;
							if($page!=1){
								$remaining -= 35;
							}
							echo '<tr style="height:'.$remaining.'mm;">
							<td style="border-left:1px solid; text-align:center;"></td>
							<td style="border-left:1px solid; padding-left:5px;">&nbsp;</td>
							<td style="text-align:center; border-left:1px solid;">&nbsp;</td>
							<td id="align_right">&nbsp;</td>
							<td id="align_right">&nbsp;</td>
							<td id="align_right">&nbsp;</td>';
							if($firm_type=='non_composition'){
								echo '<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>';
							}
							echo '
							<td id="align_right">&nbsp;</td>
							<td id="align_right">&nbsp;</td>
							<td style="text-align:right; padding-right:5px; border-left:1px solid; border-right:1px solid;">&nbsp;</td>

							</tr>';
						}
						?>
						<tr>
							<td colspan="3" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total :</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['quantity']; ?></td>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_disc']; ?></td>
							<?php if($firm_type=='non_composition'){?>
								<td style="text-align:right; border:1px solid;"><?php echo $invoice['taxable_amount']; ?></td>
								<td style="text-align:right; border:1px solid;">&nbsp;</td>
								<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_vat']; ?></td>
								<td style="text-align:right; border:1px solid;">&nbsp;</td>
								<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_sat']; ?></td>
							<?php } ?>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['total_amount']; ?></td>
						</tr>
						<?php
						if($invoice['other_discount']>0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Other Discount (-):</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['other_discount'];?></td>
						</tr>
						<?php } ?>
						<?php
						if($invoice['round_off']!=0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Round Off:</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['round_off'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total Amount :</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['total_amount1'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col-5; ?>" style="text-align:right; padding-right:10px; border:1px solid;">
							<?php
							if($invoice['other_expense']!=0){
								echo ' <b>Labor Charges : </b>'.$invoice['other_expense'];
							}
							if($invoice['transport_charge']!=0){
								echo ' <b>Transport Charges : </b>'.$invoice['transport_charge'];
							}
							?>
							</td>
							<td colspan="4" style="text-align:right; padding-right:10px; border:1px solid;"><b>Expenses (+):</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['other_expense']+$invoice['transport_charge'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Amount Payable:</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['grand_total'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:1px solid;">Rs.: <?php 
							$tot_amount = round(($invoice['grand_total']),0);
							echo strtoupper(int_to_words($tot_amount)); ?> ONLY</td>
						</tr>
						<?php
						if($invoice['remark']!=''){
						?>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:1px solid;"><b>Remarks :</b><?php echo $invoice['remark'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:none;">
								<div style="width: 500px; border: 0px solid; float: left;">
									<b><u>Terms &amp; Condition</u></b>
									<p id="terms"><?php echo $terms; ?></p>
								</div>
								<div style="border:none; float: right; border: 0px solid; text-align:right; width: 40mm; padding-top: 10px;">
									For <?php echo $company; ?>
									<br /><br />
									<img src="../images/sign.png" height="30">
									<br /><br />
									Authorised Signatory
								</div>
								<div style="float:left; width: 40mm; border: 0px solid;">
									<b>Pre Authenticated</b><br />
									For <?php echo $company; ?>
									<br />
									<img src="../images/sign.png" height="30">
									<br />
									Authorised Signatory
								</div>
								<div style="float:left; margin-top:5px; text-align:center;font-size:14px; border:0px solid; width: 50mm;">
									<p style="font-weight:bold;font-size:14px; margin-bottom:10px;line-height:16px;">
									<?php echo $bank; ?><br />
									</p>
									Subject to <?php echo $jurisdiction; ?> Jurisdiction
								</div>
							</td>
						</tr>
						<tr><td colspan="<?php echo $total_col; ?>" style="text-align:center; text-decoration:bold;"></td></tr>
					</div>
				</table>
			</div>
	</body>
</html>
<?php		
	}
	elseif($_GET['style']=='thermal'){
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SALE INVOICE</title>
		<link href="../css/pop_thermal.css" TYPE="text/css" REL="stylesheet" media="all">
		<style type="text/css">
		@media print {
			input#btnPrint {
				display: none;
			}
		}
		</style>
		<script language="javascript" type="text/javascript">
		//window.print();
		</script>
	</head>
	<body>
		<div id="wrapper">
			<input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />
			<input type="button" id="btnPrint" onclick="location.href='../sale_new.php?id=<?php echo $_SESSION['invoice_no']; ?>';" value="Edit" />
			<input type="button" id="btnPrint" onclick="location.href='../report_sale.php?del=<?php echo $_SESSION['invoice_no']; ?>'; return confirm('Are you sure?')" value="Delete" />
			<div id="company_info">
				<center><p style="font-size:14px; text-decoration: underline;"><?php if($invoice['invoice_type']=='SALE'){echo "INVOICE";}elseif($invoice['invoice_type']=='TAX'){echo "INVOICE";}else{echo "";}?></p></center>
				<div id="company">
					<div id="logo"><?php echo $company; ?></div>
				</div>
				<div id="address">
					<p style="font-size:12px; font-weight:bold; line-height:12px;"><?php echo $address; ?></p>
					<?php echo $contact; ?>
					<?php 
						if($gstin!=''){
							echo "<br/><strong>GSTIN : $gstin</strong>";
						}
					?>
					<?php 
						//if($pan!=''){
						//	echo "<br/><strong>PAN : $pan</strong>";
						//}
					?>
				</div>
			</div>
			<div id="invoice">
				<div id="bill_detail">
					<table border="0" bordercolor="#ccc" cellpadding="0" cellspacing="0" width="100%"> 
					<tr><td style="border:0px solid;">
						<div id="party">
							To,<br />
							<?php if($invoice['concerned_person']!=''){ ?>
							<b><?php echo $invoice['concerned_person']; ?></b><br />
							<?php } ?>
							<?php echo $cust['cust_name']; ?></b><br />
							<?php
							if($invoice['department']!=''){
							?>
							Department: <?php echo $invoice['department']; ?><br />
							<?php } ?>
							<?php echo $cust['address'].'<br />'.$cust['add_2'].'<br />'.$cust['city'].'-'.$cust['zipcode'].'<br />'.$cust['state'].' - '.$cust['country'];?><br />
							<?php 
							if($cust['tin']!=''){
								echo '<strong>GSTIN: '.$cust['tin'].'</strong><br>';
							}
							//if($cust['pan']!=''){
								//echo 'PAN: '.$cust['pan'].'<br>';
							//}
							if($cust['mobile']!=''){
								echo 'Tel: '.$cust['mobile'].'<br>';
							}
							?>
						</div>
						<div id="invoiceno">
							<table style="border:none;" id="noborder">
								<tr>
								<td>Invoice No. : <?php if($invoice_prefix!=''){ echo $invoice_prefix.'/';}?><?php echo $invoice['financial_year'].'-'.($invoice['financial_year']+1); ?>/INV/<?php echo $invoice['invoice_no'];?></td>
								</tr>
								<tr>
								<td>Invoice Date: <?php echo date("d-m-Y",strtotime($invoice['timestamp']));?></td></tr>
							</table>
						</div>
					</td>
					</tr>
					</table>
				</div>
				<table border="0" bordercolor="#ccc"  cellpadding="0"  cellspacing="0">
					<div id="bill">
						<thead>
						<tr>
							<th style="border:2px solid;">Sno</th>
							<th style="border:2px solid;">Product</th>
							<th style="border:2px solid;">Qty</th>
							<th style="border:2px solid;">Price</th>
							<th style="border:2px solid;">Disc</th>
							<th style="border:2px solid;">Total</th>
						 </tr>        
						</thead>
						<?php 
						$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
						$res= execute_query($sql1); 
						$tot_price=0;
						$tot_amount=0;
						$qty=0;
						$i=0;
						$taxable=0;
						$full_page = 200;
						$header_size = 60;
						$footer_size = 60;
						$row_header = 7;
						$row_height = 5;
						$description_height = 2;
						$page_size = $header_size + $footer_size + $row_header;
						$page = 1;
						$total_col = 6;
						$hsn = array();
						$hsn_old='';
						while($row=mysqli_fetch_assoc($res)) {
							$page_size += $row_height;
							$sql_available = 'select * from stock_available where sno="'.$row['part_id'].'"'; 
							$avail=mysqli_fetch_assoc(execute_query($sql_available));
							$tot_price=$tot_price+$row['amount'];
							$qty=$qty+$row['qty'];
							?>
							<tr>
							<td style="border-left:2px solid; text-align:center;"><?php echo ++$i;?></td>
							<td style="border-left:2px solid; padding-left:5px;">
								<?php 
								echo htmlspecialchars_decode($avail['description'], ENT_QUOTES); 
								echo '<br/><small>HSN : '.$avail['part_no'].'</small>';
								if($avail['part_no']!=$hsn_old){
									
								}
								if($row['description']!=''){
									echo '<br /><small>('.htmlspecialchars_decode($row['description'], ENT_QUOTES).')</small>';
									$page_size += $description_height;
								}
								$tot_amount += $row['amount'];
								$qty += $row['qty'];
								?>
							</td>
							<td style="text-align:center; border-left:2px solid;"><?php echo $row['qty'];?>&nbsp;<?php echo get_unit($row['unit']);?></td>
							<td id="align_right"><?php 
							if(strpos($row['discount'], "%")===false){
								echo $row['effective_price']+$row['discount_value'];
							}
							else{
								$new_disc = str_replace("%", "", $row['discount']);
								echo $row['effective_price']/((100-$new_disc)/100);
							}
							?></td>
							<td id="align_right"><?php echo $row['discount'];?></td>
							<td id="align_right" style="border-right:2px solid;"><?php echo $row['amount'];?></td>
							</tr>
							<?php 
							if(preg_match("/%/", $row['discount'])){
								$e_price = $row['basicprice']-($row['basicprice']*($row['discount']/100));
								//echo '@';
							}
							else{
								$e_price = $row['basicprice']-$row['discount'];
							}
							$taxable+=$e_price*$row['qty'];
						} 
						?>
						<tr>
							<td colspan="2" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total :</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['quantity']; ?></td>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['tot_disc']; ?></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['total_amount']; ?></td>
						</tr>
						<?php
						if($invoice['other_discount']>0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Other Discount (-):</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['other_discount'];?></td>
						</tr>
						<?php } ?>
						<?php
						if($invoice['round_off']!=0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Round Off:</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['round_off'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total Amount :</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['total_amount1'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col-5; ?>" style="text-align:right; padding-right:10px; border:1px solid;">
							<?php
							if($invoice['other_expense']!=0){
								echo ' <b>Labor Charges : </b>'.$invoice['other_expense'];
							}
							if($invoice['transport_charge']!=0){
								echo ' <b>Transport Charges : </b>'.$invoice['transport_charge'];
							}
							?>
							</td>
							<td colspan="<?php echo $total_col-2; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Expenses (+):</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['other_expense']+$invoice['transport_charge'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Amount Payable:</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['grand_total'];?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:0px solid;">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:2px solid;">Rs.: <?php 
							$tot_amount = round(($invoice['grand_total']),0);
							echo strtoupper(int_to_words($tot_amount)); ?> ONLY</td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:0px solid;">&nbsp;</td>
						</tr>
						<?php
						if($firm_type=='non_composition'){
							$sql = 'select part_id, stock_sale_restaurant.vat as vat, vat_value as vat_value, stock_sale_restaurant.excise as excise, excise_value as excise_value, taxable_amount as taxable_amount, part_no, amount from stock_sale_restaurant join stock_available on stock_available.sno = part_id where invoice_no='.$_SESSION['invoice_no'].' order by s_no';
							//echo $sql;
							echo '<tr>
								<th colspan="2" style="border:1px solid;">HSN/SAC</th>
								<th style="border:1px solid;">Taxable Value</th>
								<th style="border:1px solid;">CGST</th>
								<th style="border:1px solid;">SGST</th>
								<th style="border:1px solid;">Amount</th>
							</tr>';
							$result_tax = execute_query($sql);
							$tot_vat = 0;
							$tot_excise = 0;
							$tot_taxable = 0;
							$tot_total=0;
							while($row_tax = mysqli_fetch_assoc($result_tax)){
								$tot_vat += $row_tax['vat_value'];
								$tot_excise += $row_tax['excise_value'];
								$tot_taxable += $row_tax['taxable_amount'];
								$tot_total += $row_tax['amount'];
								echo '<tr>
								<td style="border:1px solid;" colspan="2">'.$row_tax['part_no'].' @ '.($row_tax['vat']+$row_tax['excise']).'%</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['taxable_amount'].'</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['vat_value'].'</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['excise_value'].'</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['amount'].'</td>
								</tr>';
							}
							echo '<tr>
							<th colspan="2" style="border:1px solid;">Total :</th>
							<th id="align_right" style="border:1px solid;">'.$tot_taxable.'</th>
							<th id="align_right" style="border:1px solid;">'.$tot_vat.'</th>
							<th id="align_right" style="border:1px solid;">'.$tot_excise.'</th>
							<th id="align_right" style="border:1px solid;">'.$tot_total.'</th>
							</tr>';
						}
						?>						
						<?php
						if($invoice['remark']!=''){
						?>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:1px solid;"><b>Remarks :</b><?php echo $invoice['remark'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:none;">
								<div style="width: 100%; border: 0px solid; float: left;">
									<b><u>Terms &amp; Condition</u></b>
									<p id="terms"><?php echo $terms; ?></p>
								</div>
								<div style="float:left; margin-top:5px; text-align:center;font-size:10px; border:0px solid; width: 100%;">
									Subject to <?php echo $jurisdiction; ?> Jurisdiction
								</div>
							</td>
						</tr>
						<tr><td colspan="<?php echo $total_col; ?>" style="text-align:center; text-decoration:bold;"></td></tr>
					</div>
				</table>
			</div>
	</body>
</html>
<?php
	}
	elseif($_GET['style']=='restaurant'){
		
		$sql = 'select * from general_settings where `desc`="restaurant_name"';
		$restaurant_name = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_name = $restaurant_name['rate'];

		$sql = 'select * from general_settings where `desc`="restaurant_gstin"';
		$restaurant_gstin = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_gstin = $restaurant_gstin['rate'];

		//$sql = 'select * from general_settings where `desc`="restaurant_pan"';
		//$restaurant_pan = mysqli_fetch_assoc(execute_query($sql));
		//$restaurant_pan = $restaurant_pan['rate'];

		
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SALE INVOICE</title>
		<link href="../css/pop_restaurant.css" TYPE="text/css" REL="stylesheet" media="all">
		<style type="text/css">
		@media print {
			input#btnPrint {
				display: none;
			}
		}
		</style>
		<script language="javascript" type="text/javascript">
		//window.print();
		</script>
	</head>
	<body>
		<div id="wrapper">
			<input type="button" id="btnPrint" onclick="window.print();" value="Print Page" />
			<input type="button" id="btnPrint" onclick="location.href='../sale_new.php?id=<?php echo $_SESSION['invoice_no']; ?>';" value="Edit" />
			<input type="button" id="btnPrint" onclick="location.href='../report_sale.php?del=<?php echo $_SESSION['invoice_no']; ?>'; return confirm('Are you sure?')" value="Delete" />
			<div id="company_info">
				<center><p style="font-size:14px; text-decoration: underline;"><?php if($invoice['invoice_type']=='SALE'){echo "INVOICE";}elseif($invoice['invoice_type']=='TAX'){echo "INVOICE";}else{echo "";}?></p></center>
				<div id="company">
					<div id="logo"><?php echo $restaurant_name; ?></div>
				</div>
				<div id="address">
					<?php echo $address; ?><br />
					<?php echo $contact; ?>
					<?php 
						if($gstin!=''){
							echo "<br/>GSTIN : $restaurant_gstin";
						}
					?>
					
				</div>
			</div>
			<table width="100%">
				<tr>
					<td colspan="5">
						<?php if($firm_type=="non_composition"){?>
						<center><p style="font-size:24px;"><?php echo "INVOICE";?></p></center>
						<?php } else{?>
						<center><p style="font-size:24px;"><?php echo "BILL OF SUPPLY";?></p></center>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th>Invoice No.</th>
					<th><?php echo $invoice['invoice_no']; ?></th>
				</tr>
				<tr>
					<th>Customer Name : </th>
					<th><?php echo $cust['cust_name']; ?></th>
					<th width="40%">&nbsp;</th>
					<th>Date :</th>
					<th><?php echo date("d-m-Y",strtotime($invoice['timestamp']));?></th>
				</tr>
				<tr>
					<th>Customer GSTIN : </th>
					<th><?php echo $cust['tin']; ?></th>
					<th width="40%">&nbsp;</th>
					<?php
					if(strpos($invoice['storeid'], "room")===false){
					?>
					<th>Table No :</th>
					<th><?php echo $invoice['storeid'];?></th>
					<?php 
					}
					else{
						$invoice['storeid'] = str_replace("room_", "", $invoice['storeid']);
						$sql="SELECT * FROM `room_master` where sno=".$invoice['storeid'];
						$room_details=mysqli_fetch_assoc(execute_query($sql));
					?>
					<th>Room No :</th>
					<th><?php echo $room_details['room_name'];?></th>
					<?php } ?>
				</tr>
			</table>
			<div id="invoice">
				<table border="0" bordercolor="#ccc"  cellpadding="0"  cellspacing="0" width="100%">
					<div id="bill">
						<thead>
						<tr>
							<th style="border:1px solid;">Sno</th>
							<th style="border:1px solid;">Item Name</th>
							<th style="border:1px solid;">Qty</th>
							<th style="border:1px solid;">Price</th>
							<?php
							if($invoice['tot_disc']!='' && $invoice['tot_disc']!=0){
								echo '<th style="border:1px solid;">Discount</th>
								<th style="border:1px solid;">Net Price</th>';
							}
							?>
							<th style="border:1px solid;">Total</th>
						 </tr>        
						</thead>
						<?php 
						$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
						$res= execute_query($sql1); 
						$tot_price=0;
						$tot_amount=0;
						$qty=0;
						$i=0;
						$taxable=0;
						$full_page = 200;
						$header_size = 60;
						$footer_size = 60;
						$row_header = 7;
						$row_height = 5;
						$description_height = 2;
						$page_size = $header_size + $footer_size + $row_header;
						$page = 1;
						$total_col = 5;
						if($invoice['tot_disc']!='' && $invoice['tot_disc']!=0){
							$total_col+=2;
						}
						$hsn = array();
						$hsn_old='';
						while($row=mysqli_fetch_assoc($res)) {
							$remaining = $full_page - $page_size;
							if($remaining<0){
								$new = $full_page - $page_size + $footer_size - 10 - 20 - 10;
								echo '<tr style="height:10mm;"><td colspan="'.$total_col.'" style="border:1px solid;">&nbsp;</td></tr>';
								echo '
								<tr style="height:10mm;">
									<th colspan="2">Total (This page) :</th>
									<th style="width:10mm;">'.$qty.'</th>
									<th colspan="'.($total_col-4).'">&nbsp;</th>
									<th style="width:15mm;">'.$tot_amount.'</th>
								</tr>';
								echo '<tr style="height:10mm;"><td colspan="'.$total_col.'" style="border:1px solid; text-align:right;">Continued on next page...</td></tr>';
								echo '<tr style="height:'.$row_header.'mm; page-break-after:always;"><td colspan="14">&nbsp;</td></tr>';
								$tot_amount=0;
								$qty=0;
								$page_size=$footer_size;
								$remaining = $full_page - $page_size;
								$page++;

							}
							
							$page_size += $row_height;
							$sql_available = 'select * from stock_available where sno="'.$row['part_id'].'"'; 
							$avail=mysqli_fetch_assoc(execute_query($sql_available));
							$tot_price=$tot_price+$row['amount'];
							$qty=$qty+$row['qty'];
							?>
							<tr>
							<td style="border-left:1px solid; text-align:center;"><?php echo ++$i;?></td>
							<td style="border-left:1px solid; padding-left:5px;">
								<?php 
								echo htmlspecialchars_decode($avail['description'], ENT_QUOTES); 
								if($avail['part_no']!=$hsn_old){
									
								}
								if($row['description']!=''){
									echo '<br /><small>('.htmlspecialchars_decode($row['description'], ENT_QUOTES).')</small>';
									$page_size += $description_height;
								}
								$tot_amount += $row['amount'];
								$qty += $row['qty'];
								?>
							</td>
							<td style="text-align:center; border-left:1px solid;"><?php echo $row['qty'];?></td>
							<td id="align_right"><?php 
							if(!preg_match("/%/", $row['discount'])){
								echo $row['effective_price']+$row['discount_value'];
							}
							else{
								$new_disc = str_replace("%", "", $row['discount']);
								echo $row['effective_price']/((100-$new_disc)/100);
							}
							?></td>
							<?php
							if($invoice['tot_disc']!='' && $invoice['tot_disc']!=0){
							?>
							<td id="align_right" style="border-right:1px solid;"><?php if($row['discount']!=0 and $row['discount']!=''){echo $row['discount'];}?></td>
							<td id="align_right" style="border-right:1px solid;"><?php echo $row['effective_price'];?></td>
							<?php } ?>
							<td id="align_right" style="border-right:1px solid;"><?php echo $row['amount'];?></td>
							</tr>
							<?php 
							if(preg_match("/%/", $row['discount'])){
								$e_price = $row['basicprice']-($row['basicprice']*($row['discount']/100));
								//echo '@';
							}
							else{
								$e_price = $row['basicprice']-$row['discount'];
							}
							$taxable+=$e_price*$row['qty'];
						} 
						
						if($remaining!=0){
							$new = $remaining - $footer_size;
							if($page!=1){
								$remaining -= 35;
							}
							echo '<tr style="height:'.$remaining.'mm;">
							<td style="border-left:1px solid; text-align:center;"></td>
							<td style="border-left:1px solid; padding-left:5px;">&nbsp;</td>
							<td style="border-left:1px solid; padding-left:5px;">&nbsp;</td>';
							if($invoice['tot_disc']!='' && $invoice['tot_disc']!=0){
								echo '<td style="border-left:1px solid; padding-left:5px;">&nbsp;</td>
								<td style="text-align:center; border-left:1px solid;">&nbsp;</td>';
							}
							echo '
							<td id="align_right">&nbsp;</td>';
							if($firm_type=='non_composition'){
								echo '<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>
								<td id="align_right">&nbsp;</td>';
							}
							echo '
							<td style="text-align:right; padding-right:5px; border-left:1px solid; border-right:1px solid;">&nbsp;</td>

							</tr>';
						}
						?>
						<tr>
							<td colspan="2" style="text-align:right; padding-right:10px; border:1px solid;"><b>Total :</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['quantity']; ?></td>
							<?php
							if($invoice['tot_disc']!='' && $invoice['tot_disc']!=0){
							?>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<?php } ?>
							<td style="text-align:right; border:1px solid;">&nbsp;</td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['total_amount']; ?></td>
						</tr>
						<?php
						if($invoice['round_off']!=0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Round Off:</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['round_off'];?></td>
						</tr>
						<?php } ?>
						<?php
						if($invoice['other_discount']!=0){
						?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Other Discount:</b></td>
							<td style="border:1px solid; text-align:right;"><?php echo $invoice['other_discount'];?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="<?php echo $total_col-1; ?>" style="text-align:right; padding-right:10px; border:1px solid;"><b>Amount Payable:</b></td>
							<td style="text-align:right; border:1px solid;"><?php echo $invoice['grand_total'];?></td>
						</tr>
						<tr>
							<td style="border:1px solid;" colspan="<?php echo $total_col; ?>" style="border:0px solid;">Mode of Payment : <?php echo $invoice['mode_of_payment']; ?></td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:1px solid;">Rs.: <?php 
							$tot_amount = round(($invoice['grand_total']),0);
							echo strtoupper(int_to_words($tot_amount)); ?> ONLY</td>
						</tr>
						<tr>
							<td style="border:1px solid;" colspan="<?php echo $total_col; ?>" style="border:0px solid;">Composition Taxable Person. Not Eligble To Collect Tax On Supplies.</td>
						</tr>
						<tr>
							<td colspan="<?php echo $total_col; ?>" style="border:0px solid;">&nbsp;</td>
						</tr>
						<?php
						if($firm_type=='non_composition'){
							$sql = 'select part_id, stock_sale_restaurant.vat as vat, vat_value as vat_value, stock_sale_restaurant.excise as excise, excise_value as excise_value, taxable_amount as taxable_amount, part_no, amount from stock_sale_restaurant join stock_available on stock_available.sno = part_id where invoice_no='.$_SESSION['invoice_no'].' order by s_no';
							//echo $sql;
							echo '<tr>
								<th colspan="1" style="border:1px solid;">HSN/SAC</th>
								<th style="border:1px solid;">Taxable Value</th>
								<th style="border:1px solid;">CGST</th>
								<th style="border:1px solid;">SGST</th>
								<th style="border:1px solid;">Amount</th>
							</tr>';
							$result_tax = execute_query($sql);
							$tot_vat = 0;
							$tot_excise = 0;
							$tot_taxable = 0;
							$tot_total=0;
							while($row_tax = mysqli_fetch_assoc($result_tax)){
								$tot_vat += $row_tax['vat_value'];
								$tot_excise += $row_tax['excise_value'];
								$tot_taxable += $row_tax['taxable_amount'];
								$tot_total += $row_tax['amount'];
								echo '<tr>
								<td style="border:1px solid;" colspan="1">'.$row_tax['part_no'].' @ '.($row_tax['vat']+$row_tax['excise']).'%</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['taxable_amount'].'</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['vat_value'].'</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['excise_value'].'</td>
								<td id="align_right" style="border:1px solid;">'.$row_tax['amount'].'</td>
								</tr>';
							}
							echo '<tr>
							<th colspan="1" style="border:1px solid;">Total :</th>
							<th id="align_right" style="border:1px solid;">'.$tot_taxable.'</th>
							<th id="align_right" style="border:1px solid;">'.$tot_vat.'</th>
							<th id="align_right" style="border:1px solid;">'.$tot_excise.'</th>
							<th id="align_right" style="border:1px solid;">'.$tot_total.'</th>
							</tr>';
						}
						?>						
						<tr>
							<td colspan="<?php echo $total_col-2; ?>" width="75%" style="border:none;">
								<div style="width: 100%; border: 0px solid; float: left;">
									
									<p id="terms"><?php echo $terms; ?></p>
								</div>
								<div style="float:left; margin-top:5px; text-align:center;font-size:14px; border:0px solid; width: 100%;">
									Subject to <?php echo $jurisdiction; ?> Jurisdiction<br />
								</div>
								<div style="float:left; margin-top:5px; text-align:center;font-size:14px; border:0px solid; width: 100%; text-decoration: underline; font-weight: bold;">
									Have a nice day.
								</div>
							</td>
							<td colspan="2">
								For <?php echo $restaurant_name; ?>
								<br /><br /><br />
								Authorised Signatory
							</td>
						</tr>
						<tr><td colspan="<?php echo $total_col; ?>" style="text-align:center; text-decoration:bold;"></td></tr>
					</div>
				</table>
			</div>
	</body>
</html>
<?php		
	
	}
}
?>