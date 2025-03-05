<?php
include ("scripts/settings.php");
$msg='';
$response=0;
page_header();

$state = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='state'"));
$state = $state['rate'];

$software_type = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='software_type'"));
$software_type = $software_type['rate'];

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
if(isset($_SESSION['gst_date_from'])){
	$date = $_SESSION['gst_date_to'];
    $date = strtotime($date);
    $date = date('Y-m-d',strtotime("+1 day", $date));
  
    
	
	//$sql_sale_igst = 'select part_id, sum(qty) as qty, '.$stock_sale_table.'.vat as vat, sum(vat_value) as vat_value, '.$stock_sale_table.'.excise as excise, sum(excise_value) as excise_value, sum('.$stock_sale_table.'.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from '.$stock_sale_table.' left join stock_available on stock_available.sno = part_id left join '.$invoice_sale_table.' on '.$invoice_sale_table.'.sno = '.$stock_sale_table.'.invoice_no left join customer on customer.sno = '.$invoice_sale_table.'.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and state!="'.$state.'" ';
	
	$stock_sale_table = 'stock_sale_restaurant';
	$invoice_sale_table = 'invoice_sale_restaurant';
	
	$sql_restaurant_gst = 'select part_id, sum(qty) as qty, '.$stock_sale_table.'.vat as vat, sum(vat_value) as vat_value, '.$stock_sale_table.'.excise as excise, sum(excise_value) as excise_value, sum('.$stock_sale_table.'.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from '.$stock_sale_table.' left join stock_available on stock_available.sno = part_id left join '.$invoice_sale_table.' on '.$invoice_sale_table.'.sno = '.$stock_sale_table.'.invoice_no left join customer on customer.sno = '.$invoice_sale_table.'.supplier_id where invoice_type="TAX" and  part_dateofpurchase >= "'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'"  group by vat';
	//echo $sql_restaurant_gst;
	$sql_restaurant_igst = 'select part_id, sum(qty) as qty, '.$stock_sale_table.'.vat as vat, sum(vat_value) as vat_value, '.$stock_sale_table.'.excise as excise, sum(excise_value) as excise_value, sum('.$stock_sale_table.'.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from '.$stock_sale_table.' left join stock_available on stock_available.sno = part_id left join '.$invoice_sale_table.' on '.$invoice_sale_table.'.sno = '.$stock_sale_table.'.invoice_no left join customer on customer.sno = '.$invoice_sale_table.'.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" group by vat';
	//$sql_gst=''
	
	$sql = "($sql_restaurant_gst) union  ($sql_restaurant_igst)";
	//echo $sql;
	$result_data_restaurant = execute_query($sql);	
	
	if(sizeof($_GET)==0){
		$response=1;
	}
}
?>
    <div id="container">
        <h2>GST SUMMARY</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form id="purchase_report" name="purchase_report" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">	
        	<table width="100%" class="no-print">
        		<tr>
        			<th>Search By</th>
                    <th>
                    	<select name="searchby">
                    		<option value="checkin">CHECK IN</option>
                    		<option value="checkout">CHECK OUT</option>
                    	</select>
                    </th>
        		</tr>
           		<tr style="background:#CCC;">
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('date_from', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['gst_date_from'])){echo $_SESSION['gst_date_from'];}else{echo date("Y-m-d");}?>', 1));
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('date_to', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['gst_date_to'])){echo $_SESSION['gst_date_to'];}else{echo date("Y-m-d");}?>', 4));
                    </script>
                    </span>
                    </td>
                </tr>
            	
                <tr>
                	<th colspan="2">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="2">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
            </table>
		</form>
<?php
	switch($response){
		case 1:{
?>
<script>
function open_gstr3b(){
	window.open("report_gstr_3b.php");
}
function open_gstr1(){
	window.open("report_gstr_1.php");
}
</script>
	<table width="100%">
    	<thead>
    	<tr>
    		<th colspan="13">OUTPUT GST (HOTEL)</th>
    	</tr>
		<tr>
			<th rowspan="2">S.No.</th>
			<th rowspan="2">Tax Rate</th>
			<th rowspan="2">Quantity</th>
			<th rowspan="2">Taxable Value</th>
			<th colspan="2">CGST</th>
			<th colspan="2">SGST</th>
			<th colspan="2">IGST</th>
			<th rowspan="2">Total Tax</th>
			<th rowspan="2">Other Disc</th>
			<th rowspan="2">Total Sale</th>
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
        $state='UTTAR PARDESH';
		$i=1;
		$taxrate='0';
		$ctot=0;
		$tot_qty_op=0;
			$tot_vat_op = 0;
			$tot_excise_op = 0;
			$tot_igst_op = 0;
			$tot_taxable_op = 0;
			$tot_sale_op = 0;
			$tot_disc_op=0;
		for($loop=0;$loop<4;$loop++){
			$amount=0;
			$c=0;
			$taxamount=0;
			$taxamount=0;
			 if($_POST['searchby'] == 'checkout'){
			 	$from='exit_date >="'.$_SESSION['gst_date_from'].'"';
			 	 $to='exit_date <= "'.$date.'"' ;
			 	 $df=$_SESSION['gst_date_from'];
			 	 $dt=$date;
			 	 $search=1;
			 }
			 else{
			 	$from='allotment_date >="'.$_SESSION['gst_date_from'].'"';
			 	$to='allotment_date <= "'.$date.'"' ;
			 	$df=$_SESSION['gst_date_from'];
			 	 $dt=$date;
			 	 $search=2;
			 }
			// echo "tax".$taxrate;
			 $otherdisc=0;
			if($taxrate == '0'){
				$tax=0;
			 	$sql = 'select allotment.sno as sno, tax_rate,other_discount, allotment_date, exit_date, original_room_rent as original_room_rent, taxable_amount as taxable_amount, room_rent as room_rent, discount_value as discount_value, state from allotment left join customer on customer.sno = cust_id where '.$from.' and '.$to.' and tax_rate="'.$tax.'" and exit_date !=""';
			 	//echo $sql;
			 	$res=execute_query($sql);
			 	while($row = mysqli_fetch_array($res)){
			 		$c++;
			 		$days = get_days($row['allotment_date'], $row['exit_date']);
					//$tot_qty_op += $days;
					$amount+=($row['taxable_amount'] - $row['other_discount'])*$days;
					$taxamount += ((($row['taxable_amount']-$row['other_discount'])*$days)*$tax)/100;
					$otherdisc+=$row['other_discount'];

			}
			 	
				echo '<tr><td>'.$i++.'</td>
				<td><a href="report_summary_gst.php?id='.$tax.'&df='.$df.'&dt='.$dt.'&s='.$search.'">GST Sale @ '.$taxrate.'%</a></td>
				<td style="text-align:center">'.$c.'</td>
				<td style="text-align:right;">'.$amount.'</td>
				<td style="text-align:right;">'.$tax.'</td>
				<td style="text-align:right;">'.$taxamount.'</td>
				<td style="text-align:right;">'.$tax.'</td>
				<td style="text-align:right;">'.$taxamount.'</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>';
				
				echo '
				<td style="text-align:right;">'.round($taxamount+$taxamount,2).'</td>
				<td style="text-align:right;">'.round($otherdisc,2).'</td>
				<td style="text-align:right;">'.round($amount+$taxamount+$taxamount,2).'</td>
				</tr>';
				$tot_vat_op += $taxamount;
				$tot_excise_op += $taxamount;
				$tot_taxable_op += $amount;
				$tot_sale_op += $amount+$taxamount+$taxamount;
			 	$taxrate=12;
			 	$ctot+=$c;
			 	$tot_disc_op+=$otherdisc;


			 }
			  else if($taxrate == 12){
				$c=0;
				$tax=6;
				$otherdisc=0;
			 	$sql = 'select allotment.sno as sno, tax_rate,other_discount, allotment_date, exit_date, original_room_rent as original_room_rent, taxable_amount as taxable_amount, room_rent as room_rent, discount_value as discount_value, state from allotment left join customer on customer.sno = cust_id where '.$from.' and '.$to.' and tax_rate="'.$taxrate.'" and exit_date !=""';
			 //echo $sql;
			 	$res=execute_query($sql);
			 	//$extdisc=0;
			 	if(mysqli_num_rows($res) > 0){
				 	while($row = mysqli_fetch_array($res)){
					 	$c++;
				 		$days = get_days($row['allotment_date'], $row['exit_date']);
				 		
						//$tot_qty_op += $days;
						//$extdisc +=$row['discount_value'];
						$amount += ($row['taxable_amount']-$row['other_discount'])*$days;
						//echo "  ".$amount;
						$taxamount += ((($row['taxable_amount']-$row['other_discount'])*$days)*$tax)/100;
						//echo"<br> ".$taxamount;
						$otherdisc+=$row['other_discount'];

				 	}
				 //	$amount -= $extdisc;
					echo '<tr><td>'.$i++.'</td>
					<td><a href="report_summary_gst.php?id='.$taxrate.'&df='.$df.'&dt='.$dt.'&s='.$search.'">GST Sale @ '.$taxrate.'%</a></td>
					<td style="text-align:center">'.$c.'</td>
					<td style="text-align:right;">'.$amount.'</td>
					<td style="text-align:right;">'.($tax).'</td>
					<td style="text-align:right;">'.$taxamount.'</td>
					<td style="text-align:right;">'.($tax).'</td>
					<td style="text-align:right;">'.$taxamount.'</td>
					<td style="text-align:right;">&nbsp;</td>
					<td style="text-align:right;">&nbsp;</td>';
				echo '
				<td style="text-align:right;">'.round($taxamount+$taxamount,2).'</td>
				<td style="text-align:right;">'.round($otherdisc,2).'</td>
				<td style="text-align:right;">'.round($amount+$taxamount+$taxamount,2).'</td>
				</tr>';
			}
				$tot_taxable_op += $amount;
					$tot_sale_op += $amount+$taxamount+$taxamount;
					$tot_vat_op += $taxamount;
					$tot_excise_op += $taxamount;
				 	$taxrate=18;
				 	$ctot+=$c;
				 	$tot_disc_op+=$otherdisc;

				 
			}
			 else if($taxrate == 18){
				$tax=9;
			 	$sql = 'select allotment.sno as sno, tax_rate, allotment_date, exit_date, original_room_rent as original_room_rent, taxable_amount as taxable_amount, room_rent as room_rent, discount_value as discount_value, state from allotment left join customer on customer.sno = cust_id where '.$from.' and '.$to.' and tax_rate="'.$taxrate.'" and exit_date !=""';
			 	//echo $sql;
			 	$res=execute_query($sql);
			 	if(mysqli_num_rows($res)){
			 		while($row = mysqli_fetch_array($res)){
					 	$c++;
				 		$days = get_days($row['allotment_date'], $row['exit_date']);
						$amount+=$row['taxable_amount']*$days;
						$taxamount += (($row['taxable_amount']*$days)*$tax)/100;
						$otherdisc+=$row['other_discount'];
						
				 	}
				 	
					echo '<td>'.$i++.'</td>
					<td><a href="report_summary_gst.php?id='.$taxrate.'&df='.$df.'&dt='.$dt.'&s='.$search.'">GST Sale @ '.$taxrate.'%</a></td>
					<td style="text-align:center">'.$c.'</td>
					<td style="text-align:right;">'.$amount.'</td>
					<td style="text-align:right;">'.($tax).'</td>
					<td style="text-align:right;">'.$taxamount.'</td>
					<td style="text-align:right;">'.($tax).'</td>
					<td style="text-align:right;">'.$taxamount.'</td>
					<td style="text-align:right;">&nbsp;</td>
					<td style="text-align:right;">&nbsp;</td>';
				
					echo '
					<td style="text-align:right;">'.round($taxamount+$taxamount,2).'</td>
					<td style="text-align:right;">'.round($otherdisc,2).'</td>
					<td style="text-align:right;">'.round($amount+$taxamount+$taxamount-$otherdisc,2).'</td>
					</tr>';

				}
				$tot_taxable_op += $amount;
				$tot_sale_op += $amount+$taxamount+$taxamount;
				$tot_vat_op += $taxamount;
				$tot_excise_op += $taxamount;
				$taxrate=9;
				$tot_disc_op+=$otherdisc;
			}

			 else if($taxrate == 28){
				$tax=14;
			 	$sql = 'select allotment.sno as sno, tax_rate, allotment_date, exit_date, original_room_rent as original_room_rent, taxable_amount as taxable_amount, room_rent as room_rent, discount_value as discount_value, state from allotment left join customer on customer.sno = cust_id where '.$from.' and '.$to.' and tax_rate="'.$taxrate.'" and exit_date !=""';
			 	//echo $sql;
			 	$res=execute_query($sql);
			 	if(mysqli_num_rows($res)){
			 	while($row = mysqli_fetch_array($res)){
			 		$c++;
			 		$days = get_days($row['allotment_date'], $row['exit_date']);
					//$tot_qty_op += $days;
					$amount+=$row['taxable_amount'] * $days;
					$taxamount += (($row['taxable_amount']*$days)*$tax)/100;
					$otherdisc+=$row['other_discount'];
					
			 	}
			 	
				echo '<td>'.$i++.'</td>
				<td><a href="report_summary_gst.php?id='.$tax.'&df='.$df.'&dt='.$dt.'&s='.$search.'">GST Sale @ '.$tax.'%</a></td>
				<td style="text-align:center">'.$c.'</td>
				<td style="text-align:right;">'.$amount.'</td>
				<td style="text-align:right;">'.($tax).'</td>
				<td style="text-align:right;">'.$taxamount.'</td>
				<td style="text-align:right;">'.($tax).'</td>
				<td style="text-align:right;">'.$taxamount.'</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>';
			
				echo '
				<td style="text-align:right;">'.round($taxamount+$taxamount,2).'</td>
				<td style="text-align:right;">'.round($otherdisc,2).'</td>
				<td style="text-align:right;">'.round($amount+$taxamount+$taxamount-$otherdisc,2).'</td>
				</tr>';
			}
			$tot_taxable_op += $amount;
			$tot_sale_op += $amount+$taxamount+$taxamount;
			$tot_vat_op += $taxamount;
			$tot_excise_op += $taxamount;
			$tot_disc_op+=$otherdisc;
		}
	}
			
		echo '<tr>
			<th colspan="2">Total</th>
			<th>'.round($ctot,3).'</th>
			<th style="text-align:right;">'.round($tot_taxable_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_vat_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_excise_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_igst_op,2).'</th>
			<th style="text-align:right;">'.round($tot_vat_op+$tot_excise_op+$tot_igst_op,2).'</th>
			<th style="text-align:right;">'.round($tot_disc_op,2).'</th>
			<th style="text-align:right;">'.round($tot_sale_op,2).'</th>
		</tr>';
		echo '<tr><td colspan="13">&nbsp;</td></tr>
		</table>
		<table>
		<tr><th colspan="6">TOTAL GST ADJUSTMENT</th></tr>
		<tr>
			<th>S.No.</th>
			<th>&nbsp;</th>
			<th>CGST AMOUNT</th>
			<th>SGST AMOUNT</th>
			<th>IGST AMOUNT</th>
			<th>TOTAL TAX</th>
		</tr>
		<tr>
			<td>1</td>
			<td>Output Tax</td>
			<td style="text-align:right">'.round($tot_vat_op,2).'</td>
			<td style="text-align:right">'.round($tot_excise_op,2).'</td>
			<td style="text-align:right">'.round($tot_igst_op,2).'</td>
			<td style="text-align:right">'.round($tot_vat_op+$tot_excise_op+$tot_igst_op,2).'</td>
		</tr>
		';		?>
    	</tbody>
    </table>
    <table width="100%">
    	<thead>
    	<tr>
    		<th colspan="12">OUTPUT GST (RESTAURANT)</th>
    	</tr>
		<tr>
			<th rowspan="2">S.No.</th>
			<th rowspan="2">Tax Rate</th>
			<th rowspan="2">Quantity</th>
			<th rowspan="2">Taxable Value</th>
			<th colspan="2">CGST</th>
			<th colspan="2">SGST</th>
			<th colspan="2">IGST</th>
			<th rowspan="2">Total Tax</th>

			<th rowspan="2">Total Sale</th>
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
		$i=1;
		$tot_qty_op=0;
		$tot_vat_op = 0;
		$tot_excise_op = 0;
		$tot_igst_op = 0;
		$tot_taxable_op = 0;
		$tot_sale_op = 0;
		
		while($row = mysqli_fetch_array($result_data_restaurant)){
			$tot_qty_op += $row['qty'];
			$tot_taxable_op += $row['taxable_amount'];
			$tot_sale_op += $row['amount'];
			echo '<tr>
			<td>'.$i++.'</td>';
			if(trim(strtoupper($row['state']))!=$state){
				echo '
				<td><a href="res_gst_summary.php?gst='.$row['vat'].'&df='.$_SESSION['gst_date_from'].'&dt='.$_SESSION['gst_date_to'].'">GST Sale @ '.str_replace("%","",($row['vat']+$row['excise'])).'%</a></td>
				<td style="text-align:center">'.round($row['qty'],3).'</td>
				<td style="text-align:right;">'.round($row['taxable_amount'],2).'</td>
				<td style="text-align:right;">'.$row['vat'].'</td>
				<td style="text-align:right;">'.round($row['vat_value'],2).'</td>
				<td style="text-align:right;">'.$row['excise'].'</td>
				<td style="text-align:right;">'.round($row['excise_value'],2).'</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>';
				$tot_vat_op += $row['vat_value'];
				$tot_excise_op += $row['excise_value'];
			}
			else{
				echo '
				<td><a href="report_gst_summary.php?gst_op='.$row['vat'].'&type=igst">IGST Sale @ '.str_replace("%","",($row['vat']+$row['excise'])).'%</a></td>
				<td style="text-align:center">'.round($row['qty'],3).'</td>
				<td style="text-align:right;">'.round($row['taxable_amount'],2).'</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">&nbsp;</td>
				<td style="text-align:right;">'.($row['vat']*2).'</td>
				<td style="text-align:right;">'.round($row['vat_value']*2,2).'</td>';
				$tot_igst_op += $row['vat_value'];
				$tot_igst_op += $row['vat_value'];
			}
			
			echo '
			<td style="text-align:right;">'.round($row['excise_value']+$row['vat_value'],2).'</td>
			<td style="text-align:right;">'.round($row['amount'],2).'</td>
			</tr>';
		}
		echo '<tr>
			<th colspan="2">Total</th>
			<th>'.round($tot_qty_op,3).'</th>
			<th style="text-align:right;">'.round($tot_taxable_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_vat_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_excise_op,2).'</th>
			<th>&nbsp;</th>
			<th style="text-align:right;">'.round($tot_igst_op,2).'</th>
			<th style="text-align:right;">'.round($tot_vat_op+$tot_excise_op+$tot_igst_op,2).'</th>
			<th style="text-align:right;">'.round($tot_sale_op,2).'</th>
		</tr>';
		echo '<tr><td colspan="12">&nbsp;</td></tr>
		</table>
		<table>
		<tr><th colspan="6">TOTAL GST ADJUSTMENT</th></tr>
		<tr>
			<th>S.No.</th>
			<th>&nbsp;</th>
			<th>CGST AMOUNT</th>
			<th>SGST AMOUNT</th>
			<th>IGST AMOUNT</th>
			<th>TOTAL TAX</th>
		</tr>
		<tr>
			<td>1</td>
			<td>Output Tax</td>
			<td style="text-align:right">'.round($tot_vat_op,2).'</td>
			<td style="text-align:right">'.round($tot_excise_op,2).'</td>
			<td style="text-align:right">'.round($tot_igst_op,2).'</td>
			<td style="text-align:right">'.round($tot_vat_op+$tot_excise_op+$tot_igst_op,2).'</td>
		</tr>';		
		?>
    	</tbody>
    </table>
<?php
			break;
		}
		case 2 :{
?>
		
				
<?php		
			break;
		}
		case 3:{
?>
		
		
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