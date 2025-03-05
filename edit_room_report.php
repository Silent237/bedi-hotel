<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
	logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
	logvalidate('admin');
$response=1;
$msg='';
page_header();

if(isset($_GET['cancel'])){
	$sql = 'select * from allotment where sno='.$_GET['cancel'];
	$row = mysqli_fetch_array(execute_query($sql));
	if($row['cancel_date']==''){
		$sql = 'update allotment set cancel_date=CURRENT_TIMESTAMP where sno='.$_GET['cancel'];
		execute_query($sql);
	}
	else{
		$sql = 'update allotment set cancel_date=NULL where sno='.$_GET['cancel'];
		execute_query($sql);
		
	}
}

?>
 <div id="container">
	<h2>Modified Bill Report</h2>
	<div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
                
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
					document.writeln(DateInput('allot_from', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1))
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4))
                    </script>
                    </span>
                    </td>
                </tr>
                <tr>
                	
                	<th>Invoice Type</th>
                    <th>
                    <select name="invoice_type" id="invoice_type">
                    	<option value="all" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='all'){echo 'selected="selected"';}}?>>All</option>
                    	<option value="tax_invoice" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='tax_invoice'){echo 'selected="selected"';}}?>>Tax Invoice All</option>
                    	<option value="tax_invoice_w_gstin" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='tax_invoice_w_gstin'){echo 'selected="selected"';}}?>>Tax Invoice with GSTIN</option>
                    	<option value="tax_invoice_wo_gstin" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='tax_invoice_wo_gstin'){echo 'selected="selected"';}}?>>Tax Invoice without GSTIN</option>
                    	<option value="bill_of_supply" <?php if(isset($_POST['invoice_type'])){if($_POST['invoice_type']=='bill_of_supply'){echo 'selected="selected"';}}?>>Non Taxable Invoice</option>
                    </select></th>
                  	<th>Invoice No</th>
					<td><input type="text" name="inv" id="inv"></td>
                </tr>
            	<tr class="no-print">
                	<th colspan="3">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="3">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
            </table>
		<?php
		if(isset($_POST['invoice_type'])){
			$_POST['allot_from'] = date("Y-m-d", strtotime($_POST['allot_from']));
			$_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to'])+86400);
			//echo $_POST['allot_to'];
		
				$rowspan='rowspan="2"';
				$colspan='colspan="7"';
			
			
		?>
		<table>
			<tr>
				<th width="5%" <?php echo $rowspan; ?>>S.No.</th>
				<th width="21%" <?php echo $rowspan; ?>>Room No.Customer Name</th>
				<th width="5%" <?php echo $rowspan; ?>>Invoice No.</th>
				<th width="5%" <?php echo $rowspan; ?>>Room Rent</th>
				<th width="7%" <?php echo $rowspan; ?>>Extra Bed</th>
				<th width="7%" <?php echo $rowspan; ?>>Disc.</th>
				
				<th width="5%" <?php echo $rowspan; ?>>Taxable Price</th>
				<th width="5%" <?php echo $rowspan; ?>>SGST</th>
				<th width="5%" <?php echo $rowspan; ?>>CGST</th>
			
				<th width="5%" <?php echo $rowspan; ?>>Rent</th>
				<th width="5%" <?php echo $colspan; ?>>Total</th>
				<th width="5%" <?php echo $rowspan; ?> class="no-print"></th>
				
			</tr>
			
			<tr>
				<th>Amount</th>
				<th>Extra Bed</th>				
				<th>Net Amount</th>
				<th>CGST</th>
				<th>SGST</th>
				<th>Other Disc</th>
				<th>Invoice Amount</th>
			</tr>
		
			<?php
			$_POST['searchby']='checkin';
			if($_POST['searchby']=='checkin'){
				$sql = 'select allotment_id,room_name,other_discount, cust_name, allotment_date, exit_date, other_charges, tax_rate, cancel_date, allotment_2.sno, room_rent, discount, original_room_rent, discount_value, invoice_no, invoice_type, id_2 from allotment_2 left join customer on customer.sno = cust_id left join room_master on room_master.sno = allotment_2.room_id where exit_date is not null and allotment_2.created_on >="'.$_POST['allot_from'].'" and allotment_2.created_on <="'.$_POST['allot_to'].'" and allotment_2.edit_status=1';
				//echo $sql;
			}
			else{
				$sql = 'select room_name, cust_name, allotment_date, exit_date, other_charges, tax_rate, cancel_date, allotment.sno, room_rent, discount, original_room_rent, discount_value, invoice_no, invoice_type, id_2 from allotment left join customer on customer.sno = cust_id left join room_master on room_master.sno = allotment.room_id where exit_date is not null and allotment.exit_date >="'.$_POST['allot_from'].'" and allotment.exit_date <="'.$_POST['allot_to'].'" ';
			}
			
			//echo $sql;
			if($_POST['invoice_type']=='bill_of_supply'){
				$sql .= ' and invoice_type="bill_of_supply"';
			}
			elseif($_POST['invoice_type']=='tax_invoice'){
				$sql .= ' and invoice_type="tax"';
			}
			elseif($_POST['invoice_type']=='tax_invoice_w_gstin'){
				$sql .= '  and invoice_type="tax" and id_2!=""';
			}
			elseif($_POST['invoice_type']=='tax_invoice_wo_gstin'){
				$sql .= '  and invoice_type="tax" and (id_2="" or id_2 is null)';
			}
			if($_POST['inv'] !=''){
				$sql .=' and invoice_no="'.$_POST['inv'].'"';
			}
			$sql .= ' order by abs(invoice_no)';
			//echo $sql;
			$result = execute_query($sql);
			
			$sql = 'SELECT MIN(abs(invoice_no)) as inv_start, MAX(abs(invoice_no)) as inv_end FROM allotment  where exit_date is not null and created_on>="'.$_POST['allot_from'].'" and created_on<="'.$_POST['allot_to'].'" ';
			$inv_min_max = mysqli_fetch_array(execute_query($sql));
			
			$i=1;
			$tot_taxable=0;
			$tot_amount=0;
			$tot_original_amount=0;
			$tot_tax=0;
			$tot_discount=0;
			$tot_other_charges=0;
			
			$tot_tax_taxable=0;
			$tot_tax_amount=0;
			$tot_tax_original_amount=0;
			$tot_tax_tax=0;
			$tot_tax_discount=0;
			$tot_tax_other_charges=0;
			
			$count=0;
			$count_tax=0;
			$cancel_count=0;
			
			$grand_tot_taxable=0;
			$grand_tot_amount=0;
			$grand_tot_original_amount=0;
			$grand_tot_tax=0;
			$grand_tot_discount=0;
			$grand_tot_other_charges=0;
			
			$cancel_tot_taxable=0;
			$cancel_original_amount=0;
			$cancel_tot_amount=0;
			$cancel_tot_tax=0;
			$cancel_tot_discount=0;
			$cancel_tot_other_charges=0;
			
			$row_old='';
		
				$colspan=9;
			$totdisc=0;
			echo '<tr>
			<td colspan="'.($colspan-1).'"><h4>Invoice Numbers from : '.$inv_min_max['inv_start'].'-'.$inv_min_max['inv_end'].'</h4></td></<tr>';
			while($row = mysqli_fetch_array($result)){
				if($row_old==''){
					$row_old=$row['invoice_type'];
				}
				$days = get_days($row['allotment_date'], $row['exit_date']);
				
				if($row['other_charges']==''){
					$other_charge = 0;
				}
				if($row['other_charges']!=''){
					$other_charge = $row['other_charges'];
				}
				
				if(strtolower(trim($row['invoice_type']))!='bill_of_supply'){
					$base_rent = round(($row['original_room_rent']-$row['discount_value']+$other_charge),2);
					$tax = round((($base_rent*$row['tax_rate']/200)),2);
				}
				else{
					$base_rent=$row['room_rent']+$other_charge;
					$tax=0;
				}
				
				$amount = $row['room_rent']*$days;
				if($row['cancel_date']==''){
					if($row['invoice_type']=='tax'){
						$tot_tax_discount+=$row['discount_value']*$days;
						$tot_tax_other_charges+=$row['other_charges']*$days;
						$tot_tax_original_amount+=$row['original_room_rent']*$days;
						$tot_tax_amount+=$amount;
						$tot_tax_tax += $tax*$days;
						$tot_tax_taxable+=$base_rent*$days;
						$count_tax++;

					}
					else{
						$tot_discount+=$row['discount_value']*$days;
						$tot_other_charges+=$row['other_charges']*$days;
						$tot_original_amount+=$row['original_room_rent']*$days;
						$tot_amount+=$amount;
						$tot_tax += $tax*$days;
						$tot_taxable+=$base_rent*$days;
						$count++;
					}
					$row_col = '';
					$cancel = '';
					$cancel_display = 'Cancel';
					
					$grand_tot_discount+=$row['discount_value']*$days;
					$grand_tot_other_charges+=$row['other_charges']*$days;
					$grand_tot_original_amount+=$row['original_room_rent']*$days;
					$grand_tot_amount+=$amount;
					$grand_tot_tax += $tax*$days;
					$grand_tot_taxable+=$base_rent*$days;
				}
				elseif($row['cancel_date'] !=''){
					$cancel_tot_taxable+=$base_rent*$days;
					$cancel_tot_amount+=$amount;
					$cancel_original_amount+=$row['original_room_rent']*$days;
					$cancel_tot_tax+=$tax*$days;
					$cancel_tot_discount+=$row['discount_value']*$days;
					$cancel_tot_other_charges+=$row['other_charges']*$days;
					$cancel_count++;

					$row_col = 'style="background:#FF0"';
					$cancel = '<br />Cancelled On : '.$row['cancel_date'];
					$cancel_display = 'Uncancel';
				}
				if($row['id_2']!=''){
					$gstin = '<br/>GSTIN : <b>'.$row['id_2'].'</b>';
				}
				else{
					$gstin='';
				}
				echo '<tr '.$row_col.'>
				<td>'.$i++.'</td>
				<td><b>'.$row['room_name'].'</b><br>'.$row['cust_name'].'<br />In : '.$row['allotment_date'].'<br />Out :'.$row['exit_date'].'<br />Days : '.$days.$cancel.$gstin.'</td>
				<td>'.$row['invoice_no'].'</td>
				<td class="number">'.number_format((float)$row['original_room_rent'], 2, '.', '').'</td>
				<td class="number">';
				
				$pos = strpos($row['other_charges'], "%");
				$other_new = str_replace("%", "", $row['other_charges']);
				if($pos===false){
					echo number_format((float)$row['other_charges'], 2, '.', '');
				}
				else{
					echo number_format((float)$row['other_charges'], 2, '.', '').' ('.$row['other_charges'].')';
				}
					
				echo '</td>
				<td class="number">';
				
				$pos = strpos($row['discount'], "%");
				$disc_new = str_replace("%", "", $row['discount']);
				if($pos===false){
					echo number_format((float)$row['discount'], 2, '.', '');
				}
				else{
					echo number_format((float)$row['discount_value'], 2, '.', '').' ('.$row['discount'].')';
				}
					
				echo '</td>
				<td class="number">'.$base_rent.'</td>';
					echo '
						<td class="number">'.number_format((float)($tax), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($tax), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($row['room_rent']), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($row['original_room_rent']*$days), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($row['other_charges']*$days), 2, '.', '').'</td>
						
						<td class="number">'.number_format((float)($base_rent*$days), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($tax*$days), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($tax*$days), 2, '.', '').'</td>
						<td class="number">'.number_format((float)($row['other_discount']), 2, '.', '').'</td>';
						$totdisc+=$row['other_discount'];
			
				echo '
					<td class="number">'.number_format((float)$amount, 2, '.', '').'</td>
					<td class="no-print"><a href="print.php?id='.$row['sno'].'&&edit_id=edit" target="_blank">View</a></td>
					
					</tr>';
				}
			echo '<tr><td colspan="'.($colspan-1).'">&nbsp;</td>
			<td colspan="2">Grand Total : </td>
			<td class="number"><b>'.number_format((float)$grand_tot_original_amount, 2, '.', '').'</b></td>
			<td class="number"><b>'.number_format((float)$grand_tot_other_charges, 2, '.', '').'</b></td>
			
			<td class="number"><b>'.number_format((float)$grand_tot_taxable, 2, '.', '').'</b></td>
			<td class="number"><b>'.number_format((float)$grand_tot_tax, 2, '.', '').'</b></td>
			<td class="number"><b>'.number_format((float)$grand_tot_tax, 2, '.', '').'</b></td>
			<td class="number"><b>'.number_format((float)$totdisc, 2, '.', '').'</b></td>
			<td class="number"><b>'.number_format((float)$grand_tot_amount, 2, '.', '').'</b></td>
			<td class="no-print">&nbsp;</td><td class="no-print">&nbsp;</td><td class="no-print">&nbsp;</td></tr>';
		}
			?>
		</table>		
		<br/>
	
	</form>
</div>
<?php
page_footer();
?>