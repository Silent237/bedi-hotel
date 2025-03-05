<?php
include ("scripts/settings.php");
$msg='';
$response=1;
page_header();

if(isset($_GET['gst_op'])){
	$response=2;
}
if(isset($_GET['gst_ip'])){
	$response=3;
}
if(isset($_POST['submit_form'])){
	foreach($_POST as $k=>$v){
		$_SESSION['gst_'.$k] = $v;
	}
}
if(isset($_POST['reset_form'])){
	foreach($_POST as $k=>$v){
		unset($_SESSION['gst_'.$k]);
	}
}
?>
    <div id="container">
        <h2>Form GSTR-1</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
<?php
	switch($response){
		case $response==1:{
			$sql = 'select * from general_settings where `desc`="company"';
			$company = mysqli_fetch_array(execute_query($sql));
			$company = $company['rate'];
		
			$sql = 'select * from general_settings where `desc`="address"';
			$address = mysqli_fetch_array(execute_query($sql));
			$address = $address['rate'];
		
			$sql = 'select * from general_settings where `desc`="gstin"';
			$gstin = mysqli_fetch_array(execute_query($sql));
			$gstin = $gstin['rate'];
		
			$sql = 'select * from general_settings where `desc`="pan"';
			$pan = mysqli_fetch_array(execute_query($sql));
			$pan = $pan['rate'];
?>
<script>
	function generate_excel(){
		
	}
	
	function generate_json(){
		
	}
</script>
	<table width="100%">
    	<thead>
    	<tr>
			<th ><h1><?php echo $company; ?></h1></th>
    	</tr>
    	<tr>
			<th><?php echo str_replace("<br />", ", ", $address); ?></th>
    	</tr>
    	<tr>
			<th><h3>GSTIN-<?php echo $gstin; ?></h3></th>
    	</tr>
    	<tr>
			<th><h3>Form GSTR-1 </h3></th>
    	</tr>
		<?php
		if(!isset($_SESSION['gst_date_from']) || !isset($_SESSION['gst_date_to'])){
			echo '<tr>
				<th>Incorrect Input</th>
			</tr>';
			die();
		}
		else{
		?>
			<tr>
				<th>For <?php echo date("d-m-Y", strtotime($_SESSION['gst_date_from'])).' to '.date("d-m-Y", strtotime($_SESSION['gst_date_to']));?></th>
			</tr>
		<?php } ?>
	</table>
	<table>
		<thead>
		<tr>
			<th width="50%">GSTIN : <?php echo $gstin; ?></th>
			<th>Legal Name of registered person : <?php echo $company; ?></th>
		</tr>
		<tr>
			<td style="text-align: center"><input type="button" value="Generate Excel" name="generate_excel" onClick="generate_excel();"></td>
			<td style="text-align: center"><input type="button" value="Generate JSON" name="generate_json" onClick="generate_json();"></td>
		</tr>
	</table>
	<table>
		<tr>
			<th colspan="12" class="left">B2b : Business to Business</th>
		</tr>
		<tr>
			<th>GSTIN/UIN of Recipient</th>
			<th>Receiver Name</th>
			<th>Invoice Number</th>
			<th>Invoice date</th>
			<th>Invoice Value</th>
			<th>Place Of Supply</th>
			<th>Reverse Charge</th>
			<th>Invoice Type</th>
			<th>E-Commerce GSTIN</th>
			<th>Rate</th>
			<th>Taxable Value</th>
			<th>Cess Amount</th>
		</tr>
        </thead>
        <tbody>
        <?php
		$sql = 'SELECT customer.tin as gstin, customer.cus_name as cus_name, invoice_sale.invoice_no as invoice_no, dateofdispatch as invoice_date, total_amount as invoice_value, customer.state as place_of_supply, invoice_sale.inv_type_purchase as reverse_charge, "Regular" as invoice_type, "" as e_commerce, stock_sale.vat as rate, sum(stock_sale.taxable_amount) as taxable_value, "" as cess FROM `stock_sale` left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where customer.tin!="" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX" group by stock_sale.invoice_no, stock_sale.vat';
		//echo $sql;
		$result_taxable = execute_query($sql);
		$i=0;
		$no_of_customer=0;
		$gst_chk='';
		$tot_invoice_value=0;
		$tot_taxable_value=0;
		while($row_taxable = mysqli_fetch_array($result_taxable)){
			echo '<tr>
			<td>'.$row_taxable['gstin'].'</td>
			<td>'.$row_taxable['cus_name'].'</td>
			<td>'.$row_taxable['invoice_no'].'</td>
			<td>'.$row_taxable['invoice_date'].'</td>
			<td class="right">'.$row_taxable['invoice_value'].'</td>
			<td>'.$row_taxable['place_of_supply'].'</td>
			<td>'.$row_taxable['reverse_charge'].'</td>
			<td>'.$row_taxable['invoice_type'].'</td>
			<td>'.$row_taxable['e_commerce'].'</td>
			<td class="right">'.($row_taxable['rate']*2).'</td>
			<td class="right">'.round($row_taxable['taxable_value'],2).'</td>
			<td>'.$row_taxable['cess'].'</td>
			</tr>';
			if($gst_chk!=$row_taxable['gstin']){
				$gst_chk = $row_taxable['gstin'];
				$no_of_customer++;
			}
			$i++;
			$tot_invoice_value+=$row_taxable['invoice_value'];
			$tot_taxable_value+=round($row_taxable['taxable_value'],2);
		}
		echo '<tr>
		<th>'.$no_of_customer.'</th>
		<th>'.$i.'</th>
		<th>&nbsp;</th>
		<th class="right">'.$tot_invoice_value.'</th>
		<th colspan="5">&nbsp;</th>
		<th class="right">'.$tot_taxable_value.'</th>
		<th>&nbsp;</th>
		</tr>';
		?>
		</tbody>
		</table>
  		<table>	
  			<tr>
  				<th colspan="8">B2Cl (5) : Supplies made to unregistered dealer or end consumer, where invoice value is more than Rs. 2,50,000</th>
  			</tr>
  			<tr>
				<th>Invoice Number</th>
				<th>Invoice date</th>
				<th>Invoice Value</th>
				<th>Place Of Supply</th>
				<th>Rate</th>
				<th>Taxable Value</th>
				<th>Cess Amount</th>
				<th>E-Commerce GSTIN</th>
  			</tr>
  			<?php
		
			$sql = 'SELECT customer.tin as gstin, invoice_sale.invoice_no as invoice_no, dateofdispatch as invoice_date, total_amount as invoice_value, customer.state as place_of_supply, invoice_sale.inv_type_purchase as reverse_charge, "Regular" as invoice_type, "" as e_commerce, stock_sale.vat as rate, sum(stock_sale.taxable_amount) as taxable_value, "" as cess FROM `stock_sale` left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_sale.total_amount>250000 and customer.tin="" and customer.state!="UTTAR PRADESH" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX" group by stock_sale.invoice_no, stock_sale.vat';
			//echo $sql;
			$result_taxable = execute_query($sql);
			$i=0;
			$no_of_customer=0;
			$gst_chk='';
			$tot_invoice_value=0;
			$tot_taxable_value=0;
			while($row_taxable = mysqli_fetch_array($result_taxable)){
				echo '<tr>
				<td>'.$row_taxable['invoice_no'].'</td>
				<td>'.$row_taxable['invoice_date'].'</td>
				<td class="right">'.$row_taxable['invoice_value'].'</td>
				<td>'.$row_taxable['place_of_supply'].'</td>
				<td class="right">'.($row_taxable['rate']*2).'</td>
				<td class="right">'.round($row_taxable['taxable_value'],2).'</td>
				<td>'.$row_taxable['cess'].'</td>
				<td>'.$row_taxable['e_commerce'].'</td>
				</tr>';
				if($gst_chk!=$row_taxable['gstin']){
					$gst_chk = $row_taxable['gstin'];
					$no_of_customer++;
				}
				$i++;
				$tot_invoice_value+=$row_taxable['invoice_value'];
				$tot_taxable_value+=round($row_taxable['taxable_value'],2);
			}
			echo '<tr>
			<th>'.$i.'</th>
			<th>&nbsp;</th>
			<th class="right">'.$tot_invoice_value.'</th>
			<th colspan="2">&nbsp;</th>
			<th class="right">'.$tot_taxable_value.'</th>
			<th colspan="2">&nbsp;</th>
			</tr>';
			?>		
		</table>
		
		<table>	
  			<tr>
  				<th colspan="8">B2CS (7) : all intra-state supplies in 7A and inter-state supplies having invoice value up to Rs. 2.5 Lakh made to unregistered dealer in 7B</th>
  			</tr>
  			<tr>
				<th>Type</th>
				<th>Place Of Supply</th>
				<th>Rate</th>
				<th>Taxable Value</th>
				<th>Cess Amount</th>
				<th>E-Commerce GSTIN</th>  			
  			</tr>
  			<?php
		
			$sql = 'SELECT customer.tin as gstin, invoice_sale.invoice_no as invoice_no, dateofdispatch as invoice_date, total_amount as invoice_value, customer.state as place_of_supply, invoice_sale.inv_type_purchase as reverse_charge, "Regular" as invoice_type, "" as e_commerce, stock_sale.vat as rate, sum(stock_sale.taxable_amount) as taxable_value, "" as cess FROM `stock_sale` left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_sale.total_amount<=250000 and customer.state = "UTTAR PRADESH" and customer.tin="" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX" group by customer.state, stock_sale.vat';
			//echo $sql;
			$result_taxable = execute_query($sql);
			$i=0;
			$no_of_customer=0;
			$gst_chk='';
			$tot_invoice_value=0;
			$tot_taxable_value=0;
			while($row_taxable = mysqli_fetch_array($result_taxable)){
				echo '<tr>
				<td>'.$row_taxable['invoice_type'].'</td>
				<td>'.$row_taxable['place_of_supply'].'</td>
				<td class="right">'.($row_taxable['rate']*2).'</td>
				<td class="right">'.round($row_taxable['taxable_value'],2).'</td>
				<td>'.$row_taxable['cess'].'</td>
				<td>'.$row_taxable['e_commerce'].'</td>
				</tr>';
				if($gst_chk!=$row_taxable['gstin']){
					$gst_chk = $row_taxable['gstin'];
					$no_of_customer++;
				}
				$i++;
				$tot_invoice_value+=$row_taxable['invoice_value'];
				$tot_taxable_value+=round($row_taxable['taxable_value'],2);
			}
			echo '<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th class="right">'.$tot_taxable_value.'</th>
			<th colspan="2">&nbsp;</th>
			</tr>';
			?>		
		</table>
		
		<table>	
  			<tr>
  				<th colspan="10">12 : HSN-wise summary of outward supplies</th>
  			</tr>
  			<tr>
				<th>HSN</th>
				<th>Description</th>
				<th>UQC</th>
				<th>Total Quantity</th>
				<th>Total Value</th>
				<th>Taxable Value</th>
				<th>Integrated Tax Amount</th>
				<th>Central Tax Amount</th>
				<th>State/UT Tax Amount</th>
				<th>Cess Amount</th>
			</tr>
  			<?php
		
			$sql = 'SELECT stock_available.part_no as hsn, stock_available.description as description, stock_available.unit as unit, sum(stock_sale.qty) as qty, sum(amount) as total_value, sum(stock_sale.taxable_amount) as taxable_value, sum(vat_value) as cst_value, "" as cess from stock_sale left join stock_available on stock_sale.part_id = stock_available.sno left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no where part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX" group by stock_available.description';
			//echo $sql;
			$result_taxable = execute_query($sql);
			$i=0;
			$no_of_customer=0;
			$gst_chk='';
			$tot_invoice_value=0;
			$tot_taxable_value=0;
			$tot_tax=0;
			$tot_qty=0;
			while($row_taxable = mysqli_fetch_array($result_taxable)){
				echo '<tr>
				<td>'.$row_taxable['hsn'].'</td>
				<td>'.$row_taxable['description'].'</td>
				<td>'.get_unit($row_taxable['unit']).'</td>
				<td class="right">'.round($row_taxable['qty'],2).'</td>
				<td class="right">'.round($row_taxable['total_value'],2).'</td>
				<td class="right">'.round($row_taxable['taxable_value'],2).'</td>
				<td class="right">&nbsp;</td>
				<td class="right">'.round($row_taxable['cst_value'],2).'</td>
				<td class="right">'.round($row_taxable['cst_value'],2).'</td>
				<td></td>
				</tr>';
				$i++;
				$tot_qty += round($row_taxable['qty'],2);
				$tot_invoice_value += round($row_taxable['total_value'],2);
				$tot_taxable_value += round($row_taxable['taxable_value'],2);
				$tot_tax += round($row_taxable['cst_value'],2);
			}
			echo '<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th class="right">'.$tot_qty.'</th>
			<th class="right">'.$tot_invoice_value.'</th>
			<th class="right">'.$tot_taxable_value.'</th>
			<th class="right">'.$tot_tax.'</th>
			<th class="right">'.$tot_tax.'</th>
			<th class="right">'.$tot_tax.'</th>
			<th>&nbsp;</th>
			</tr>';
			?>		
		</table>
		
		<table>	
  			<tr>
  				<th colspan="10">13 : Summary of documents issued during the tax period.</th>
  			</tr>
  			<tr>
				<th>Nature  of Document</th>
				<th>Sr. No. From</th>
				<th>Sr. No. To</th>
				<th>Total Number</th>
				<th>Cancelled</th>  			
			</tr>
  			<?php
		
			$sql = 'SELECT invoice_no, financial_year from invoice_sale where dateofdispatch>="'.$_SESSION['gst_date_from'].'" and dateofdispatch<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX" order by abs(invoice_no) asc limit 1';
			$inv_start = mysqli_fetch_array(execute_query($sql));
		
			$sql = 'SELECT invoice_no, financial_year from invoice_sale where dateofdispatch>="'.$_SESSION['gst_date_from'].'" and dateofdispatch<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX" order by abs(invoice_no) desc limit 1';
			$inv_end = mysqli_fetch_array(execute_query($sql));
		
			$sql = 'SELECT count(*) c from invoice_sale where dateofdispatch>="'.$_SESSION['gst_date_from'].'" and dateofdispatch<="'.$_SESSION['gst_date_to'].'" and invoice_sale.invoice_type="TAX"';
			$inv_count = mysqli_fetch_array(execute_query($sql));

			$sql = 'select * from general_settings where `desc`="invoice_prefix"';
			$invoice_prefix = mysqli_fetch_array(execute_query($sql));
			$invoice_prefix = $invoice_prefix['rate'];
		
			//echo $sql;
			$result_taxable = execute_query($sql);
			$inv_pfx = '';
			if($invoice_prefix!=''){ $inv_pfx .= $invoice_prefix.'/';}
			$inv_pfx .= $inv_start['financial_year'].'-'.($inv_start['financial_year']+1);
			$inv_pfx .= '/INV/';
			echo '<tr>
			<td>Invoice for outward supply</td>
			<td>'.$inv_pfx.$inv_start['invoice_no'].'</td>
			<td>'.$inv_pfx.$inv_end['invoice_no'].'</td>
			<td class="right">'.$inv_count['c'].'</td>
			<td class="right">0</td>
			</tr>';
			?>		
		</table>
<?php
			break;
		}
	}
?>          
	</div>
</div>
<?php
page_footer();


?>