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
/*$sql_invoice = 'select * from invoice_sale_restaurant where sno="'.$_SESSION['invoice_no'].'"';
$invoice=mysqli_fetch_assoc(execute_query($sql_invoice));

$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
$res = execute_query($sql1); 
$stock_sale_restaurant=mysqli_fetch_assoc($res);*/


$_GET['style'] = $bill_style;

if(isset($_GET['style'])){
	
	if($_GET['style']=='thermal'){
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
			<!--<input type="button" id="btnPrint" onclick="location.href='../dine_in_order.php?edit_id=<?php echo $_SESSION['invoice_no']; ?>';" value="Edit" />
			<input type="button" id="btnPrint" onclick="location.href='../report_sale.php?del=<?php echo $_SESSION['invoice_no']; ?>'; return confirm('Are you sure?')" value="Delete" />-->
			<div id="company_info">
				<div id="company">
					<div id="company" style="margin-top:10px">
						<div id="logo"><h1 style="font-size:22px;"><?php echo $company; ?></h1><br><h1 style="font-size:16px;"><?php echo $slogan; ?></h1><br><h4 style="font-size:12px;letter-spacing:2px"></b></h4></div>
					</div>
					<div id="address" style="margin-top:4px">
						<p style="font-size:16px;letter-spacing: 1px; margin-bottom:3px;"><?php echo $address; ?></p>
						<p style="font-size:16px;letter-spacing: 1px; margin-bottom:3px;"> </p>
						<p style="font-size:16px;letter-spacing: 1px"></p>
					</div>
					<div id="address" style="margin-top:4px">
						<p style="font-size:15px;letter-spacing: 1px"></p>
					</div>
					<div id="address" style="margin-top:10px">
						<p style="font-size:15px;letter-spacing: 1px;line-height:16px;">Pincode - 224001</p>
						<p style="font-size:15px;letter-spacing: 1px;line-height:16px;">Contact No: +91 - 7393957373</p>
						<p style="font-size:15px;letter-spacing: 1px;line-height:16px;">Email ID: tipsytown2023@gmail.com</p>
					</div>
					<div id="address" style="margin-top:10px">

						<!--<p style="font-size:15px;letter-spacing: 1px">Contact No: +91 +917991992999</p>
					</div>
					<div id="address" style="margin-top:10px">
						<p style="font-size:15px;letter-spacing: 1px">E-Mail :  sv967857@gmail.com</p>-->
					</div>
					<!--<div id="address" style="margin-top:10px">
						<p style="font-size:15px;letter-spacing: 1px">www.indraprasthainn.com</p>
					</div>-->
					<div id="address" style="margin-top:10px">
						<p style="font-size:15px;letter-spacing: 1px"><?php
								if($gstin!=''){
									echo "<br/><strong style='font-size:15px;letter-spacing: 1px'>GSTIN : 09AAZFB2216Q1ZP</strong>";
								}
							?> </p>
					</div>
					<div id="address" style="margin-top:10px">
						<p style="font-size:15px;letter-spacing: 1px">
						<hr style="border-top: dotted 1px;margin-top:10px;" /> 
						</p>
					</div>
				</div>

				<div id="invoice">
					<div id="bill_detail">
						<table border="0" bordercolor="#ccc" cellpadding="0" cellspacing="0" width="100%" >
							<tr style="height:20px;font-size:15px">
								<td style="font-size: 15px">Date: <?php echo date("d-m-Y");?></td>
							</tr>
							<tr style="height:20px;font-size:15px">
								<td style="font-size: 15px">
									<?php
									$sql = 'select * from res_table where sno='.$_SESSION['invoice_no'];
									$table = mysqli_fetch_array(execute_query($sql));

									echo 'Table : '.$table['table_number'];
									?>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<table border="0" bordercolor="#ccc"  cellpadding="0"  cellspacing="0" width="100%">
					<div id="bill">
						<tr style="height:25px">
							<b><th style="border:2px solid;width:20%;font-size: 14px">Sno</th>
							<th style="border:2px solid;width:50%;font-size: 14px">Item</th>
							<th style="border:2px solid;width:20%;font-size: 14px">Qty</th>
							<th style="border:2px solid;width:20%;font-size: 14px">Price</th>
							
							<th style="border:2px solid;padding-right:8px;font-size: 14px">Total</th></b>
							<th></th>

						 </tr> 
						<?php
						$subtotal=0;
						$sno=1;
						$_GET['table_id'] = $tableid = $_GET['id'];
						if(isset($_GET['table_id'])){
							$sql="SELECT `description`, `vat`, `excise`, sum(`kitchen_ticket_temp`.`unit`) as qty, `kitchen_ticket_temp`.`kot_no` as kotno ,item_price FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid' and (invoice_no is null or invoice_no='') and cancel_timestamp is null group by  `kitchen_ticket_temp`.item_id";
							//echo $sql;
							$res=execute_query($sql);
							$tot_taxable_hidden=0;
							$total_cgst_hidden=0;
							$total_sgst_hidden=0;
							$tot_tax=0;
							$taxable=0;
							$tot_qty_hidden=0;
							$total_amount_hidden=0;
							if (mysqli_num_rows($res) == 0) {
								echo '<script>window.open("index.php" , "_self")</script>';
							}
							while($row=mysqli_fetch_array($res)){
								$tot_tax_rate = $row['vat']+$row['excise'];
								//$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
								$base_price=$row['item_price'];
								$taxable = $base_price*$row['qty'];
								$cgst_amt = (round((($taxable*$row['vat'])/100),2));
								$sgst_amt = (round((($taxable*$row['excise'])/100),2));
								$e_price = round($row['item_price'] + (($row['item_price']*$row['vat'])/100) + (($row['item_price']*$row['vat'])/100),2);
								$total=$e_price*$row['qty'];

								$tot_taxable_hidden+=$taxable;
								$total_cgst_hidden+=$cgst_amt;
								$total_sgst_hidden+=$sgst_amt;
								$total_amount_hidden+=$total;
								$tot_qty_hidden += $row['qty'];
								$kotno=$row['kotno'];
								echo'<tr style="height:20px">
									<td style="border-left:2px solid; text-align:center;font-size: 13px">'.$sno.'</td>
									<td style="border-left:2px solid; padding-left:5px;font-size: 13px">'.$row['description'].'</td>
									<td style="text-align:center; border-left:2px solid;font-size: 13px">'.$row['qty'].'</td>
									<td id="align_right" style="border-right:2px solid;font-size: 13px">'.$e_price.'</td>
									<td style="text-align:right; border-right:2px solid;font-size:13px">'.$total.'</td>
								</tr>';
								$sno++;
							}
							echo '<tr style="height:20px">
							<th>&nbsp;</th>
							<td style="border-left:2px solid; text-align:center;font-size: 13px">Total : </td>
							<td style="border-left:2px solid; text-align:center;font-size: 13px">'.$tot_qty_hidden.'</td>
							<th style="border-left:2px solid; text-align:center;font-size: 13px">&nbsp;</th>
							<td style="border-right:2px solid; border-left:2px solid; text-align:center;font-size: 13px">'.$total_amount_hidden.'</td>
							</tr>';

							$round_off = $total_amount_hidden;
							$dummy_grand_total = round($total_amount_hidden, 2);
							$round_off = round(round($total_amount_hidden) - $dummy_grand_total,2);
							$grand_total = $total_amount_hidden+$round_off;

						}
						?>
						<tr id="service_charge" style="display: ">
							<td style="border-top:2px solid;"></td>
							<td colspan="3" style="border-top:2px solid;font-size:13px">Service Charge @ 5% : </td>
							<td style="border-top:2px solid;font-size:13px"><?php $service_charge = round($total_amount_hidden*0.05, 2); echo $service_charge; ?></td>
						</tr>
						<tr id="service_charge" style="display: ">
							<td style="border-top:2px solid;"></td>
							<td colspan="3" style="border-top:2px solid;font-size:13px">Grand Total : </td>
							<td style="border-top:2px solid;font-size:13px"><?php $grand_total += $service_charge; echo $grand_total; ?></td>
						</tr>
						<tr>
							<td colspan="5" style="border-top:2px solid;font-size:13px">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="5" style="font-size:15px; line-height: 15px;">In Words.: <?php 
							$tot_amount = round(($grand_total),0);
							echo strtoupper(int_to_words($tot_amount)); ?> </td>
						</tr>
						<tr>

							<td colspan="5" style="border:none;">
								<div style="width: 100%; border: 0px solid; float: left;">
									<span style="font-size:12px"></span><br><br>

									
									<b><u>Terms &amp; Condition</span></b>
									<p id="terms"><?php echo $terms; ?></p>
								</div>
								<div style="float:left; margin-top:5px; text-align:center;font-size:10px; border:0px solid; width: 100%;">
									Subject to Ayodhya Jurisdiction
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
}
?>