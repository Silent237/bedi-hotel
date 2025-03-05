<?php
include ("scripts/settings.php");
$msg='';
$response=0;
page_header();
$_GET['type']='gst';
?>
 <div id="container">
    <h2>GST SUMMARY</h2>
       
		<table width="100%">
    	<thead>
    	<tr>
    		<th colspan="12">OUTPUT GST DETAIL </th>
    	</tr>
		<tr>
			<th rowspan="2">S.No.</th>
			<th rowspan="2"> Name</th>
			<th rowspan="2">Invoice No.</th>
			<th rowspan="2">Invoice Date</th>
			<th rowspan="2">Taxable Amount</th>
			<th rowspan="2">Quantity</th>
			
			<?php
			if($_GET['type']=='gst'){
			?>
			<th colspan="2">CGST</th>
			<th colspan="2">SGST</th>
			<th rowspan="2">Total Tax</th>
			<th rowspan="2">Total </th>
		</tr>
		<tr>
			<th>Rate</th>
			<th>Amount</th>
			<th>Rate</th>
			<th>Amount</th>
		</tr>
			<?php
			}
			else{
			?>
			<th colspan="2">IGST</th>
			<th rowspan="2">Total Sale</th>
		</tr>
		<tr>
			<th>Rate</th>
			<th>Amount</th>
		</tr>
			<?php } ?>
        </thead>
        <tbody>
        <?php
        $stock_sale_table = 'stock_sale_restaurant';
		$invoice_sale_table = 'invoice_sale_restaurant';
		$sql = 'select '.$invoice_sale_table.'.sno as sno, '.$invoice_sale_table.'.invoice_no, '.$invoice_sale_table.'.dateofdispatch, sum('.$stock_sale_table.'.basicprice) as basicprice, sum('.$stock_sale_table.'.qty) as qty,stock_sale_restaurant.supplier_id as cid,sum('.$stock_sale_table.'.taxable_amount) as taxable_amount, '.$stock_sale_table.'.vat, sum('.$stock_sale_table.'.vat_value) as vat_value, '.$stock_sale_table.'.excise, sum('.$stock_sale_table.'.excise_value) as excise_value, sum('.$stock_sale_table.'.amount) as amount from '.$stock_sale_table.' join '.$invoice_sale_table.' on '.$invoice_sale_table.'.sno = '.$stock_sale_table.'.invoice_no  where '.$invoice_sale_table.'.invoice_type="TAX" and '.$stock_sale_table.'.vat="'.$_GET['gst'].'"and dateofdispatch>="'.$_GET['df'].'" and dateofdispatch <="'.$_GET['dt'].'" group by invoice_no';
		
		if(isset($_SESSION['gst_date_from'])){
			$sql .= ' and dateofdispatch>="'.$_SESSION['gst_date_from'].'" and dateofdispatch <="'.$_SESSION['gst_date_to'].'"';
			if($_SESSION['gst_invoice_type']!='all'){
				switch($_SESSION['gst_invoice_type']){
					case 'tax_gst':{
						$sql .= ' and tin!=""';
						$sql_purchase .= ' and tin!=""';

						break;
					}
					case 'tax_wo_gst':{
						$sql .= ' and tin=""';
						$sql_purchase .= ' and tin=""';

						break;
					}
					default:{
						$sql .= ' and invoice_type="'.$_SESSION['gst_invoice_type'].'"';
						$sql_purchase .= ' and invoice_type="'.$_SESSION['gst_invoice_type'].'"';
						break;
					}
				}
			}
		}
		//echo $sql;
		$result = execute_query($sql);
		$i=1;
		$tot_qty=0;
		$tot_taxable = 0;
		$tot_vat = 0;
		$tot_excise = 0;
		$tot_tax = 0;
		$tot_amount = 0;
		$price=0;
		while($row = mysqli_fetch_array($result)){
			echo '<tr>
			<td>'.$i++.'</td>
			<td>'.get_cust_name($row['cid']).'</td>
			
			<td><a target="_blank" href="scripts/printing_sale_restaurant.php?inv='.$row['invoice_no'].'">'.$row['invoice_no'].'</a></td>
			<td>'.$row['dateofdispatch'].'</td>
			<td style="text-align:right;">'.round($row['basicprice'],2).'</td>
			<td style="text-align:center;">'.$row['qty'].'</td>';
			$price+=round($row['basicprice'],2);
			if($_GET['type']=='gst'){
				echo '<td style="text-align:right;">'.$row['vat'].'</td>
				<td style="text-align:right;">'.$row['vat_value'].'</td>
				<td style="text-align:right;">'.$row['excise'].'</td>
				<td style="text-align:right;">'.$row['excise_value'].'</td>
				<td style="text-align:right;">'.round($row['vat_value']+$row['excise_value'],2).'</td>';
			}
			else{
				echo '<td style="text-align:right;">'.($row['vat']+$row['excise']).'</td>
				<td style="text-align:right;">'.($row['vat_value']+$row['excise_value']).'</td>';
			}
			echo '<td style="text-align:right;">'.$row['amount'].'</td>
			</tr>';
			$tot_qty += $row['qty'];
			$tot_vat += $row['vat_value'];
			$tot_excise += $row['excise_value'];
			$tot_tax += round($row['vat_value']+$row['excise_value'],2);
			$tot_amount += $row['amount'];
			$tot_taxable += $row['taxable_amount'];
		}
		echo '<tr>
		<th colspan="4" style="text-align:right;">Total :</th>
		<th style="text-align:center;">'.$price.'</th>
		<th style="text-align:center;">'.$tot_qty.'</th>';
		
		if($_GET['type']=='gst'){
			echo '<th>&nbsp;</th>
			<th style="text-align:right;">'.$tot_vat.'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.$tot_excise.'</th>
			<th style="text-align:right;">'.$tot_tax.'</th>';
		}
		else{
			echo '<th>&nbsp;</th>
			<th style="text-align:right;">'.($tot_vat+$tot_excise).'</th>';
		}
		echo '<th style="text-align:right;">'.$tot_amount.'</th></tr>';
		?>
		</tbody>
		</table>
	</div>
</div>
<?php
page_footer();
?>