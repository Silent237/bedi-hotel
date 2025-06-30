<?php
include ("scripts/settings.php");
$tab=1;
$msg='';
$response=1;

$sql = 'select * from general_settings where `desc`="state"';
$state = mysqli_fetch_assoc(execute_query($sql));
$json_base_array = array();

// if(isset($_POST['einv_form'])){
	// $sql = 'SELECT * FROM `banquet_hall` WHERE 1=1 ';
	// $_POST['date_to'] = date('Y-m-d H:i' , strtotime($_POST['date_to'])+86400);
	// if($_POST['supplier_sno'] != ''){
		// $sql .= ' AND `cust_id`="'.$_POST['supplier_sno'].'" ';
	// }
	// if($_POST['invoice_no'] != ''){
		// $sql .= ' AND `invoice_no`="'.$_POST['invoice_no'].'" ';
	// }
	// if($_POST['mop'] != ''){
		// $sql .= ' AND `mop`="'.$_POST['mop'].'" ';
	// }
	// if ($_POST['date_type'] != '') {
		// if ($_POST['date_type'] == 'event') {
			// $sql .= ' AND `check_in_date`>="'.$_POST['date_from'].'" AND `check_in_date`<="'.$_POST['date_to'].'"';
		// }
		// if ($_POST['date_type'] == 'booking') {
			// $sql .= ' AND `booking_date`>="'.$_POST['date_from'].'" AND `booking_date`<="'.$_POST['date_to'].'"';
		// }
	// }
	// else{
		// $sql .= ' AND `created_on`>"'.date('Y-m-d').'"';
	// }
	// $sql .= ' and status="0"';
	//echo $sql;
	// $result = execute_query($sql);
		
	// $SellerDtls = array("Gstin"=>"09AAHCS2262A1ZN", "LglNm"=>"Shane Avadh Hotel Pvt Ltd", "Addr1"=>"Civil Lines", "Addr2"=>"Ayodhya", "Loc"=>"Ayodhya", "Pin"=>224001, "Stcd"=>$state['rate']);
	// ini_set( 'serialize_precision', -1 );
	// while($row = mysqli_fetch_assoc($result)){
		// unset($ItemList);
		// if(isset($_POST['einv_'.$row['sno']])){
			// $tot_taxable=0;
			// $tot_tax=0;
			// $tot_itax=0;
			// $tot_inv_amt=0;
			// //print_r($row);
			////echo $row['bill_create_date'].'<Br>';
			
			// $DocDtls = array("Typ"=>"INV", "No"=>"SA/".$row['financial_year']."/".$row['invoice_no'], "Dt"=>date("d/m/Y",strtotime($row['booking_date'])));
			
			// $sql_customer = 'SELECT * FROM `customer` WHERE `sno`="'.$row['cust_id'].'"';
			// $result_customer = execute_query($sql_customer);
			// $row_customer = mysqli_fetch_array($result_customer);
			
			// $BuyerDtls = array("Gstin"=>$row_customer['id_2'], "LglNm"=>$row_customer['company_name'], "Addr1"=>($row_customer['address'] != '')?$row_customer['address']:$details['guest_address'], "Addr2"=>$row_customer['city'], "Loc"=>$row_customer['city'], "Pos"=>$row_customer['state'], "Pin"=>round($row_customer['zipcode']), "Stcd"=>substr($row['id_2'], 0, 2));
			
			// $BuyerDtls = array("Gstin"=>$row_customer['id_2'], "LglNm"=>$row_customer['company_name'], "Addr1"=>($row_customer['address'] != '')?$row_customer['address']:$details['guest_address'], "Addr2"=>"-NA-", "Loc"=>"-NA-", "Pos"=>"09", "Pin"=>round($row_customer['zipcode']), "Stcd"=>substr($row_customer['id_2'], 0, 2));
			
			// $sql = 'SELECT * FROM `banquet_particular` WHERE `banquet_id`="'.$row['sno'].'"';
			// $result_stock = execute_query($sql);
			// $HsnCd = '996311';
			// $i=1;
			// while($row_stock = mysqli_fetch_assoc($result_stock)){
				// $taxable = $row_stock['amount'];
				// if(abs($state['rate']==$state['rate'])){
					// $itax = 0;
					// $tax = round(($taxable*(18/2))/100,2);
				// }
				// else{
					// $itax = round((($taxable*18)/100),2);
					// $tax=0;
				// }
				// $ItemList[] = array("SlNo"=> strval($i++), "PrdDesc"=> $row_stock['particular'], "IsServc"=> "Y", "HsnCd"=> $HsnCd, "Qty"=> (float)$row_stock['quantity'], "FreeQty"=> 0, "Unit"=> "NOS", "UnitPrice"=> (float)$row_stock['rate'], "TotAmt"=> (float)$row_stock['amount'], "Discount"=> 0, "PreTaxVal"=> 0, "AssAmt"=> (float)$row_stock['amount'], "GstRt"=> 18, "IgstAmt"=> $itax, "CgstAmt"=> $tax, "SgstAmt"=> $tax, "CesRt"=> 0, "CesAmt"=> 0, "CesNonAdvlAmt"=> 0, "StateCesRt"=> 0, "StateCesAmt"=> 0, "StateCesNonAdvlAmt"=> 0, "OthChrg"=> 0, "TotItemVal"=> (float)$row_stock['grand_total']);
				
				// $tot_taxable += $taxable;
				// $tot_tax += $tax;
				// $tot_itax += $itax;
				// $tot_inv_amt += $row_stock['grand_total'];
			// }
			
			// $ValDtls = array("AssVal"=>round($tot_taxable,2), "IgstVal"=> round($tot_itax,2), "CgstVal"=> round($tot_tax,2), "SgstVal"=> round($tot_tax,2), "CesVal"=> 0, "StCesVal"=> 0, "Discount"=> 0, "OthChrg"=> 0, "RndOffAmt"=> 0, "TotInvVal"=> round($tot_inv_amt,2));
			
			// $json_base_array[] = array("Version"=>"1.1", "TranDtls"=>array("TaxSch"=>"GST", "SupTyp"=>"B2B"), "DocDtls"=>$DocDtls, "SellerDtls"=>$SellerDtls,  "BuyerDtls"=>$BuyerDtls, "ValDtls"=>$ValDtls, "ItemList"=>$ItemList);
		// }
	// }
	
	// $array = json_encode($json_base_array, JSON_UNESCAPED_SLASHES);
	
	// echo $array;
	
	// $file = "e_invoice.json";
	// $txt = fopen($file, "w") or die("Unable to open file!");
	// fwrite($txt, $array);
	// fclose($txt);

	// header('Content-Description: File Transfer');
	// header('Content-Disposition: attachment; filename='.basename($file));
	// header('Expires: 0');
	// header('Cache-Control: must-revalidate');
	// header('Pragma: public');
	// header('Content-Length: ' . filesize($file));
	// header("Content-Type: text/plain");
	// readfile($file);
	// exit();
// }

// if(isset($_GET['cancel'])){
	// $sql = 'update banquet_hall set status=1 where sno="'.$_GET['cancel'].'"';
	// execute_query($sql);
// }

// if(isset($_GET['uncancel'])){
	// $sql = 'update banquet_hall set status=0 where sno="'.$_GET['uncancel'].'"';
	// execute_query($sql);
// }

// if(isset($_GET['detail'])){
	// $response = 2;
// }
page_header();
?>

	
    <div id="container">
        <h2>Proforma Report</h2>
        <div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form id="purchase_report" name="purchase_report" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">	
			<table width="100%">
					
            	<tr style="background:#CCC;">
					<td>
	                    <select id="date_type" name="date_type" class="field select addr" tabindex="">
	                    	<option  value="" disabled selected>All Dates</option>
	                    	<option value="cdt">Create Date</option>
	                    	<option value="cincoutdt">Check IN/Out Date</option>
	                    </select>
               		</td>
                	<th>Date From</th>
                    <td>
						<input name="datefrom" type="date" value="" class="field text medium" tabindex="<?php echo $tab++;?>" id="datefrom" />	
                    </td>
                	<th>Date To</th>
					<td>
						<input name="dateto" type="date" value="" class="field text medium" tabindex="<?php echo $tab++;?>" id="dateto" />	
					</td>
                </tr>
            	<tr>
                    <th>Customer Name</th>
                    <td>
                    <input id="cus" name="cus" class="fieldtextmedium" maxlength="255" tabindex="7" type="text" value="">
                    </td>
                </tr>
                <tr>
                	<th colspan="2">
                    	<input type="submit" name="search" value="Search with  Filters" class="btTxt submit">
                    </th>
                    <th colspan="2">
                    	<a href="report_proforma.php" class="btn btn-primary">Reset Filters</a>
                    </th>
					<th >
                <input type="submit" name="download_excel" value="Download in Excel" class="btTxt submit btn-danger" onclick="return submitExportExcel(event);">
            </th>
                </tr>
            </table>
		

	<table width="100%">
    	<tr>
    		<th>S.No.</th>
    		<th>Customer Name</th>
    		<th>Company Name</th>
			<th>Mobile No.</th>
			<th>GSTIN</th>
    		<th>PIN Code</th>
			<th>Date</th>
    		<th>Check In Date</th>
			<th>Check Out Date</th>
    		<th>Amount</th>
    		<th>SGST</th>
    		<th>CGST</th>
    		<th>Grand Total</th>
    		<th>Edit</th>
    		<th>Print</th>
    		
    	</tr>
    	<?php 
			$sql = 'SELECT * FROM `proforma_invoice` WHERE 1=1';
			if(isset($_POST['date_type'])){
				$dtfrom=date("d-m-Y", strtotime($_POST['datefrom']));
				$dtto=date("d-m-Y", strtotime($_POST['dateto']));
				
				if($_POST['date_type']=="cdt"){
					if(isset($_POST['datefrom']) && ($_POST['datefrom'] != '')){
						if(isset($_POST['dateto']) && ($_POST['dateto'] != '')){
							$sql .= ' AND creation_time BETWEEN "'.$dtfrom.'" AND "'.$dtto.'"';
						}
						else{
							$sql .= ' AND creation_time BETWEEN "'.$dtfrom.'" AND "'.date('d-m-Y').'"';
						}
					}
				}
				else if($_POST['date_type']=="cincoutdt"){
					if(isset($_POST['datefrom']) && ($_POST['datefrom'] != '')){
						if(isset($_POST['dateto']) && ($_POST['dateto'] != '')){
							$sql .= ' AND cindt BETWEEN "'.$dtfrom.'" AND "'.$dtto.'"';
						}
						else{
							$sql .= ' AND cindt BETWEEN "'.$dtfrom.'" AND "'.date('d-m-Y').'"';
						}
					}
				}
			}
			else if(isset($_POST['datefrom']) && ($_POST['datefrom'] != '')){
				$dtfrom=date("d-m-Y", strtotime($_POST['datefrom']));
				$dtto=date("d-m-Y", strtotime($_POST['dateto']));
						if(isset($_POST['dateto']) && ($_POST['dateto'] != '')){
							$sql .= ' AND creation_time BETWEEN "'.$dtfrom.'" AND "'.$dtto.'"';
						}
						else{
							$sql .= ' AND creation_time BETWEEN "'.$dtfrom.'" AND "'.date('d-m-Y').'"';
						}
				}
			
    		
			if(isset($_POST['cus'])){
				$sql .= " AND guest_name LIKE '%".$_POST['cus']."%'";
			}
			
			//echo $sql;
    		$result = execute_query($sql);
            $amount = 0;
            $cgst = 0;
            $sgst = 0;
            $grand_total = 0;
			$i = 1;
    		while($row = mysqli_fetch_array($result)){
				// if($row['status']=='1'){
					// $col = 'background:#f90;';
				// }
				// else{
					// $col = '';
				// }
				$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
    			?>
    	<tr style="background:<?php echo $bg_color; ?>;">
    		<td><?php echo $i++; ?></td>
    		<td><?php echo $row['guest_name']; ?></td>
    		<td><?php echo $row['company_name']; ?></td>
			<td><?php echo $row['mob_no']; ?></td>
    		<td><?php echo $row['gstin']; ?></td>
    		<td><?php echo $row['pin_code']; ?></td>
            <td><?php echo $row['creation_time']==NULL?"--":date("d:m:Y",strtotime($row['creation_time'])); ?></td>
            <td><?php echo $row['cindt']==NULL?"--":date("d:m:Y",strtotime($row['cindt'])); ?></td>
            <td><?php echo $row['coutdt']==NULL?"--":date("d:m:Y",strtotime($row['coutdt'])); ?></td>
    		<td><?php echo $row['amount']; ?></td>
    		<td><?php echo $row['sgst']; ?></td>
    		<td><?php echo $row['cgst']; ?></td>
    		<td><?php echo $row['totel']; ?></td>
    		<!--<td><a href="report_banquet_hall.php?detail=<?php //echo $row['sno']; ?>">Detail</a></td>-->
    		<td><a href="proforma.php?e_id=<?php echo $row['sno']; ?>" target="_blank"><i class="fas fa-edit"></i></a></td>
    		<td><a href="print_proforma.php?id=<?php echo $row['sno']; ?>" target="_blank">Print</a></td>
            <!--<td><a href="banquet_hall.php?e_id=<?php //echo $row['sno']; ?>" target="_blank">Edit</a></td>-->
            <?php
				//if($row['status']=='0'){
			?>
				<!--<td><a href="report_banquet_hall.php?cancel=<?php //echo $row['sno']; ?>" style="color: #f00;" onClick="return confirm('Are you sure?');">Cancel</a></td>-->
			<?php		
				//}
				//else{
			?>
				<!--<td><a href="report_banquet_hall.php?uncancel=<?php //echo $row['sno']; ?>" style="color: #0f0;" onClick="return confirm('Are you sure?');">UnCancel</a></td>-->
			<?php
					
				//}
			?>
            
            <?php
			// if($row_customer['id_2']!=''){
				// echo '<td class="no-print"><input type="checkbox" name="einv_'.$row['sno'].'" value="'.$row['sno'].'"></td>';	
			// }
			// else{
				// echo '<td>&nbsp;</td>';
			// }	
				
			?>
    	</tr>
    			<?php
                // $amount += $row['amount'];
				$amount = 0;
				$sgst = 0;
				$cgst = 0;
				$grand_total = 0;
				$amount += intval($row['amount']);
                $sgst += intval($row['sgst']);
                $cgst += intval($row['cgst']);
                $grand_total += intval($row['totel']); 
    		}
    	?>
        <tr>
            <th colspan="9">Total:</th>
            <th><?php echo round($amount , 3); ?></th>
            <th><?php echo round($sgst , 3); ?></th>
            <th><?php echo round($cgst , 3); ?></th>
            <th><?php echo round($grand_total , 3); ?></th>
            <th colspan="5">&nbsp;</th>
        </tr>
    </table>

	
  </form>      
	</div>
</div>
<script>
function submitExportExcel(event) {
    event.preventDefault(); // Stop normal submit
    var form = document.getElementById('purchase_report');
    form.action = 'proforma_report_export.php';
    form.submit();
    return false;
}
</script>
<?php
navigation('');
page_footer();
?>