<?php
	include ("scripts/settings.php");
	$msg='';
	$response=0;
	page_header();
	$state = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='state'"));
	$state = $state['rate'];

?>
<div id="container">
	<h2>GST SUMMARY</h2>
    <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		
		<table width="100%">
    		<thead>
    	<tr>
    		<th colspan="15">OUTPUT GST (HOTEL)</th>
    	</tr>
		<tr>
			<th rowspan="2">S.No.</th>
			<th rowspan="2">Name</th>
			<th rowspan="2">Allot Date</th>
			<th rowspan="2">Room No.</th>
			<th rowspan="2">Days</th>
			<th rowspan="2">Taxable Value</th>
			<th colspan="2">CGST</th>
			<th colspan="2">SGST</th>
			<th colspan="2">IGST</th>
			<th rowspan="2">Total Tax</th>
			<th rowspan="2">Other Disc</th>
			<th rowspan="2">Total </th>
		</tr>
		<tr>
			<th>Rate</th>
			<th>Amount</th>
			<th>Rate</th>
			<th>Amount</th>
			<th>Rate</th>
			<th>Amount</th>
		</tr>
        </thead>
        <tbody>
        <?php
        $df=$_GET['df'];
        $dt=$_GET['dt'];
        $id=$_GET['id'];
     	if($_GET['s']==1){
     		$from='exit_date >="'.$df.'"';
		 	 $to='exit_date <= "'.$dt.'"' ;
		 	
		}
		else{
			$from='allotment_date >="'.$df.'"';
		 	$to='allotment_date <= "'.$dt.'"' ;
		 	
		}
        $sql = 'select allotment.sno as sno,other_discount,allotment_date as date, tax_rate, allotment_date, exit_date, original_room_rent as original_room_rent, taxable_amount as taxable_amount, room_rent as room_rent, discount_value as discount_value,other_charges,room_id as roomid,customer.cust_name, state from allotment left join customer on customer.sno = cust_id where '.$from.' and '.$to.' and tax_rate="'.$id.'" and exit_date !=""';
	//echo $sql;
			$result = execute_query($sql);
        
		$i=1;
		$tot_qty=0;
		$tot_taxable = 0;
		$tot_vat = 0;
		$tot_excise = 0;
		$tot_tax = 0;
		$tot_amount = 0;
		$tot_qty_op=0;
		$tot_sale_op =0;
		$tot_excise_op=0;
		$tot_igst_op=0;
		$tot_vat_op=0;
			$tot_taxable_op=0;
		$tot_disc_op=0;
		while($row = mysqli_fetch_array($result)){
			$days = get_days($row['allotment_date'], $row['exit_date']);
			$taxable_amount = round(($row['original_room_rent']+$row['other_charges']-$row['discount_value'])*$days,2);
			$tax = ($taxable_amount*($row['tax_rate']/2)/100);
			$tot_qty_op += $days;
			$tot_taxable_op += $taxable_amount;
			$tot_sale_op += $taxable_amount+$tax+$tax;
			$tot_disc_op += $row['other_discount'];
			echo '<tr>
			<td>'.$i++.'</td>';
			if(trim(strtoupper($row['state']))==$state){
				echo '
				<td><a href="print.php?id='.$row['sno'].'">'.$row['cust_name'].'</a></td>
				<td style="text-align:;">'.date("d-m-Y",strtotime($row['date'])).'</td>
				<td style="text-align:right;">'.get_room($row['roomid']).'</td>
				<td style="text-align:center">'.$days.'</td>
				<td style="text-align:right;">'.$taxable_amount.'</td>
				<td style="text-align:right;">'.($row['tax_rate']/2).'</td>
				<td style="text-align:right;">'.$tax.'</td>
				<td style="text-align:right;">'.($row['tax_rate']/2).'</td>
				<td style="text-align:right;">'.$tax.'</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>';
				$tot_vat_op += $tax;
				$tot_excise_op += $tax;
				
			}
			else{
				echo '
				<td><a href="print.php?id='.$row['sno'].'">GST Sale @ '.str_replace("%","",($row['tax_rate'])).'%</a></td>
				<td style="text-align:center">'.$row['c'].'</td>
				<td style="text-align:right;">'.$taxable_amount.'</td>
				<td style="text-align:right;">'.($row['tax_rate']/2).'</td>
				<td style="text-align:right;">'.$tax.'</td>
				<td style="text-align:right;">'.($row['tax_rate']/2).'</td>
				<td style="text-align:right;">'.$tax.'</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>';
				$tot_vat_op += $tax;
				$tot_excise_op += $tax;
				
			}
			
			echo '
			<td style="text-align:right;">'.round($tax+$tax,2).'</td>
			<td style="text-align:right;">'.round($row['other_discount'],2).'</td>
			<td style="text-align:right;">'.round($taxable_amount+$tax+$tax-$row['other_discount'],2).'</td>
			</tr>';
		}
		echo '<tr>
			<th colspan="5">Total</th>
			
			<th style="text-align:right;">'.round($tot_taxable_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_vat_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_excise_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_igst_op,2).'</th>
			<th style="text-align:right;">'.round($tot_vat_op+$tot_excise_op+$tot_igst_op,2).'</th>
			<th style="text-align:right;">'.round($tot_disc_op,2).'</th>
			<th style="text-align:right;">'.round($tot_sale_op-$tot_disc_op,2).'</th>
		</tr>';
		echo '<tr><td colspan="15">&nbsp;</td></tr>';
		?>
		</table>
	</div>
</div>	
<?php
page_footer();


?>