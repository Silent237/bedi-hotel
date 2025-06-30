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
$sql_invoice = 'select * from invoice_sale_restaurant where sno="'.$_SESSION['invoice_no'].'"';
$invoice=mysqli_fetch_assoc(execute_query($sql_invoice));

$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
$res = execute_query($sql1); 
$stock_sale_restaurant=mysqli_fetch_assoc($res);

$sql_available = 'select * from stock_available where sno="'.$stock_sale_restaurant['part_id'].'"';
$avail=mysqli_fetch_assoc(execute_query($sql_available));

$sql_customer = 'select *, id_2 as tin, "" as country, "" as pan,company_name from customer where sno="'.$invoice['supplier_id'].'"';
$cust=mysqli_fetch_assoc(execute_query($sql_customer));

$_GET['style'] = $bill_style;

if(isset($_GET['style'])){
	if($_GET['style']=='thermal'){
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SALE INVOICE</title>
	</head>
	<body>
		<div id="wrapper" style="width:200px;">
			<table border="0" bordercolor="#ccc" cellpadding="0" cellspacing="0" width="100%" >
				<tr>
					<td><center><p style="font-size:14px; "><?php if($invoice['invoice_type']=='SALE'){echo "INVOICE";}elseif($invoice['invoice_type']=='TAX'){echo "INVOICE";}else{echo "";}?></p></center></td>
				</tr>
				<tr>
					<td><h1 style="font-size:20px;"><?php echo $company; ?></h1><h1 style="font-size:17px;"><?php echo $slogan; ?></h1></td>
				</tr>
				<tr>
					<td>
						
						<p style="font-size:14px;letter-spacing: 1px"><?php echo $address; ?></p>
						<p style="font-size:14px;letter-spacing: 1px"><?php
							if($gstin!=''){
								echo "<br/><strong style='font-size:14px;letter-spacing: 1px'>GSTIN : 09AAZFB2216Q1ZP</strong>";
							}
						?></p>
						<p style="font-size:14px;letter-spacing: 1px">*** TAX INVOICE ***</p>
					</td>
				</tr>
			</table>
			<table border="0" bordercolor="#ccc" cellpadding="0" cellspacing="0" width="100%" >
				<tr>
					<td style="font-size: 15px">Date: <?php echo date("d-m-Y",strtotime($invoice['timestamp']));?></td>
				</tr>
				<tr>
					<td style="font-size: 15px">Invoice No : <?php echo $invoice['invoice_no']; ?></td>
				</tr>
				<tr>
					<td style="font-size: 15px"><?php
						if($tableno == 'yes'){	  
							if(strpos($invoice['storeid'], "room")===false){
								$sql = 'select * from res_table where sno='.$invoice['storeid'];
								//echo $sql;
								$table = mysqli_fetch_array(execute_query($sql));

								echo 'Table : '.$table['table_number'];
							}
							else{
								$room_id = substr($invoice['storeid'], 5);
								$sql = 'select * from room_master where sno='.$room_id;
								//echo $sql;
								$room = mysqli_fetch_array(execute_query($sql));
								echo 'Room : '.$room['room_name'];
							}
						}
					?>
					</td>
				</tr>
				<tr>
					<td style="font-size:13px;">Company Name :
					<?php 
					if($cust['company_name']!=''){
						if($cust['company_name']!=''){ 
							echo $cust['company_name'];
						} 
					} 
					?>
					</td>
				</tr>
				<tr>
					<td style="font-size: 13px;">Guest Name : <?php echo $invoice['concerned_person']; ?></td>
				</tr>

				<tr>
					<td style="text-align:left; font-size:13px">Mobile No : <?php echo  $cust['mobile'];  ?></td>
				</tr>
				<tr>
					<td style="text-align:left; font-size:13px;">Address: <?php echo $cust['address']; ?></td>
				</tr>
				<tr>
					<td style="text-align:left; font-size:13px">GSTIN: <?php echo $cust['tin']; ?></td>
				</tr>
			</table>
			<table border="1" bordercolor="#ccc"  cellpadding="0"  cellspacing="0" width="50%">
				<tr>
					<th style=" width:10%; font-size: 14px">Sno</th>
					<th style=" width:40%; font-size: 14px">Item</th>
					<th style=" width:10%; font-size: 14px">Qty</th>
					<th style=" width:20%; font-size: 14px">Price</th>
					<th style=" font-size: 14px">Total</th>
				</tr>
				<?php 
				//$sql1 = 'select * from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'"';
				$sql1 = 'select sum(qty) as qty,part_id,sum(amount) as amount,invoice_no,kot_no,nck_no,supplier_id,table_id,basicprice,description,vat,vat_value,excise,excise_value,discount,discount_value,taxable_amount,effective_price,part_dateofpurchase,admin_remarks from stock_sale_restaurant where invoice_no="'.$_SESSION['invoice_no'].'" group by part_id';
				//echo $sql1;
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
						<td style="text-align:center;font-size: 13px"><?php echo ++$i;?></td>
						<td style="padding-left:5px;font-size: 13px">
							<?php 
							echo htmlspecialchars_decode($avail['description'], ENT_QUOTES); 
							echo '<br/><small>'.$avail['part_no'].'</small>';
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
						<td style="text-align:center; font-size: 13px"><?php echo $row['qty'];?></td>
						<td id="align_right" style="font-size: 13px"><?php 
						if(strpos($row['discount'], "%")===false){
							echo (float)$row['effective_price']+(float)$row['discount_value'];
						}
						else{
							$new_disc = str_replace("%", "", $row['discount']);
							echo $row['effective_price']/((100-$new_disc)/100);
						}
						?></td>

						<td id="align_right" style="font-size: 13px"><?php echo $row['amount'];?></td>
					</tr>
					<?php 
					if(preg_match("/%/", $row['discount'])){
						$e_price = $row['basicprice']-($row['basicprice']*($row['discount']/100));
						//echo '@';
					}
					else{
						$e_price = (float)$row['basicprice']-(float)$row['discount'];
					}
					$taxable+=$e_price*$row['qty'];
				} 
				?>
				<tr>
					<td colspan="2" style="text-align:right; padding-right:10px; font-size:14px"><b style="font-size:13px">Total</b></td>
					<td style="text-align:right; font-size:13px"><?php echo $invoice['quantity']; ?></td>
					<td style="text-align:right; font-size:13px">&nbsp;</td>

					<td style="text-align:right; font-size:13px"><?php echo $invoice['taxable_amount']; ?></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center; padding-right:10px; font-size:13px"><b style="font-size:13px">CGST : 2.5%</b></td>
					<td style=" text-align:right;font-size:13px"><?php echo $invoice['tot_vat'];?></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center; font-size:13px"><b style="font-size:13px">SGST : 2.5%</b></td>
					<td style=" text-align:right;font-size:13px"><?php echo $invoice['tot_sat'];?></td>
				</tr>
				<?php
				if($invoice['round_off']!=0){
				?>
				<tr>
					<td colspan="4" style="text-align:right; padding-right:10px; font-size:13px"><b style="font-size:13px">Round Off</b></td>
					<td style=" text-align:right;font-size:13px"><?php echo $invoice['round_off'];?></td>
				</tr>
				<?php } ?>
				<?php
				if($invoice['other_discount']>0){
				?>
				<tr style=" text-align:right;"></tr>
				<tr>
					<td colspan="4" style="text-align:center; font-size:13px ">Other Discount (-):</td>
					<td style=" text-align:right;font-size:13px"><?php echo $invoice['other_discount'];?></td>
				</tr>
				<?php } ?>
				<?php
				if($invoice['service_charge_amount']>0){
				?>
				<tr style=" text-align:right;"></tr>
				<tr>
					<td colspan="4" style="text-align:center; font-size:13px ">Service Charge @ <?php echo $invoice['service_charge_rate'].'%'.' + Tax @ '.$invoice['service_charge_tax_rate'].'%' ;?>:</td>
					<td style=" text-align:right;font-size:13px"><?php echo $invoice['service_charge_total'];?></td>
				</tr>
				<?php } ?>

				<tr>
					<td colspan="4" style="text-align:right; padding-right:10px; font-size:14px"><b>Amount Payable:</b></td>
					<td style="text-align:right; font-size:13px;"><?php echo round($invoice['grand_total'] , 2);?></td>
				</tr>
				<tr>
					<td colspan="<?php echo $total_col; ?>" style=" solid;font-size:13px">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="<?php echo $total_col; ?>" style="font-size:15px; line-height: 14px;">In Words.: <?php 
					$tot_amount = round(($invoice['grand_total']),0);
					echo strtoupper(int_to_words($tot_amount)); ?> </td>
				</tr>
				<tr>
					<td colspan="4" style=" solid;font-size:13px">&nbsp;</td>
				</tr>
				<?php
				if($firm_type=='non_composition'){
					$sql = 'select part_id, stock_sale_restaurant.vat as vat, vat_value as vat_value, stock_sale_restaurant.excise as excise, excise_value as excise_value, taxable_amount as taxable_amount, part_no, amount from stock_sale_restaurant join stock_available on stock_available.sno = part_id where invoice_no='.$_SESSION['invoice_no'].' order by s_no';
					//echo $sql;
					echo '<tr>
						<th colspan="3" style="">HSN/SAC</th>
						<th style="">Taxable Value</th>
						<th style="">CGST</th>
						<th style="">SGST</th>
						<th style="">Amount</th>
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
						<td style="" colspan="3">'.$row_tax['part_no'].' @ '.($row_tax['vat']+$row_tax['excise']).'%</td>
						<td id="align_right" style="">'.$row_tax['taxable_amount'].'</td>
						<td id="align_right" style="">'.$row_tax['vat_value'].'</td>
						<td id="align_right" style="">'.$row_tax['excise_value'].'</td>
						<td id="align_right" style="">'.$row_tax['amount'].'</td>
						</tr>';
					}
					echo '<tr>
					<th colspan="3" style="">Total :</th>
					<th id="align_right" style="">'.$tot_taxable.'</th>
					<th id="align_right" style="">'.$tot_vat.'</th>
					<th id="align_right" style="">'.$tot_excise.'</th>
					<th id="align_right" style="">'.$tot_total.'</th>
					</tr>';
				}
				?>						
				<?php
				if($invoice['remark']!=''){
				?>
				<tr>
					<td colspan="<?php echo $total_col; ?>" style=""><b>Remarks :</b><?php echo $invoice['remark'];?></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="<?php echo $total_col; ?>" style="">
						<div style="width: 100%; float: left;">
							<span style="font-size:12px"></span><br><br>


							<b><u>Terms &amp; Condition</span></b>
							<p id="terms"><?php echo $terms; ?></p>
						</div>
						<div style="float:left; text-align:center;font-size:10px;  solid; width: 100%;">
							Subject to Lucknow Jurisdiction
						</div>
					</td>
				</tr>
				<tr><td colspan="<?php echo $total_col; ?>" style="text-align:center; text-decoration:bold;"></td></tr>
			</table>
		</div>
	</body>
</html>
<?php
	}
	
}
?>