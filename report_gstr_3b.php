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
        <h2>Form GSTR-3B</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
<?php
	switch($response){
		case $response==1:{
			$date = $_SESSION['gst_date_from'];
			$time = strtotime($date);
			$month = date("m",$time);
			$year = date("Y",$time);
			if($month>=1 && $month<=3){
				$year = $year-1;
			}
			
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
		
			$state = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='state'"));
			$state = $state['rate'];

?>
<script>
	function generate_excel(){
		
	}
	
	function generate_json(){
		var json_3b = '{"gstin" : "27GDSPS3444H1ZV","ret_period": "042018","sup_details": {"osup_det":{"txval":12,"iamt":15750,"camt":10,"samt":10,"csamt":12},"osup_zero":{"txval":10,"iamt":0,"camt":0,"samt":0,"csamt":0},"osup_nil_exmp":{"txval":50,"iamt":0,"camt":0,"samt":0,"csamt":0},"isup_rev":{"txval":100,"iamt":2,"camt":2,"samt":2,"csamt":2},"osup_nongst":{"txval":500,"iamt":0,"camt":0,"samt":0,"csamt":0}},"itc_elg":{ "itc_avl":[ {"ty":"IMPG","iamt":10,"camt":0,"samt":0,"csamt":10},{"ty":"IMPS","iamt":15,"camt":0,"samt":0,"csamt":0},{"ty":"ISRC","iamt":20,"camt":5,"samt":5,"csamt":5},{"ty":"ISD","iamt":25,"camt":1,"samt":1,"csamt":1},{"ty":"OTH","iamt":30,"camt":10,"samt":10,"csamt":0}],"itc_rev":[ {"ty":"RUL","iamt":35,"camt":5,"samt":5,"csamt":0},{"ty":"OTH","iamt":40,"camt":5,"samt":5,"csamt":0}],"itc_net":{ "iamt":25,"camt":6,"samt":6,"csamt":16},"itc_inelg":[ {"ty":"RUL","iamt":40,"camt":5,"samt":5,"csamt":0},{"ty":"OTH","iamt":45,"camt":5,"samt":5,"csamt":0}]},"inward_sup":{"isup_details":[{"ty":"GST","inter":1,"intra":1},{"ty":"NONGST","inter":5,"intra":0}]},"intr_ltfee":{"intr_details":{"iamt":1,"camt":1,"samt":1,"csamt":1},"ltfee_details":{}},"inter_sup":{"unreg_details":[{"pos":"07","txval":25000,"iamt":1250},{"pos":"10","txval":15000,"iamt":2500}],"comp_details":[{"pos":"07","txval":35000,"iamt":1500},{"pos":"10","txval":20000,"iamt":3000}],"uin_details":[{"pos":"07","txval":40000,"iamt":4000},{"pos":"10","txval":25000,"iamt":3500}]}}';
	}
</script>
	<table width="100%">
    	<tr>
   			<th>Year :</th>
   			<th><select name="gst_year" id="gst_year">
   				<option value=""></option>
   				<?php
				for($i=2017; $i<=date("Y"); $i++){
					echo '<option value="'.$i.'" '; 
					if($i==$year){
						echo 'selected="selected"';
					}
					echo '>'.$i.'-'.($i+1).'</option>';
				}
		
				?>
   			</select></th>
   			<th>Month :</th>
   			<th><select name="gst_year" id="gst_year">
   				<option value=""></option>
   				<?php
				for($i=1; $i<=12; $i++){
					$d = "2000-".$i."-05";
					echo '<option value="'.str_pad($i,2,"0", STR_PAD_LEFT).'" '; 
					if($i==$month){
						echo 'selected="selected"';
					}
					echo '>'.date("M", strtotime($d)).'</option>';
				}
				?>
   			</select></th>
   		</tr>
   		<tr>
   			<th colspan="4"><input type="submit" name="submit" value="Submit"></th>
   		</tr>
	</table>
   	<table>
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
			<th><h3>Form GSTR-3B </h3></th>
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
			<th>GSTIN : <?php echo $gstin; ?></th>
			<th colspan="5">Legal Name of registered person : <?php echo $company; ?></th>
		</tr>
		<tr>
			<td style="text-align: center" colspan="3"><input type="button" value="Generate Excel" name="generate_excel" onClick="generate_excel();"></td>
			<td style="text-align: center" colspan="3"><input type="button" value="Generate JSON" name="generate_json" onClick="generate_json();"></td>
		</tr>
		
		<tr>
			<th colspan="6" class="left">3.1 Details of Outward Supplies and inward supplies liable to reverse charge</th>
		</tr>
		<tr>
			<th>Nature of Supplier</th>
			<th class="right">Taxable Value</th>
			<th class="right">IGST</th>
			<th class="right">CGST</th>
			<th class="right">State/UT Tax</th>
			<th class="right">Cess</th>
		</tr>
        </thead>
        <tbody>
        <?php
		$i=1;
		$taxable=0;
		$igst_value=0;
		$sgst_value=0;
		$cgst_value=0;
		$tot_taxable_op = 0;
		$tot_vat_op = 0;
		$tot_excise_op = 0;
		$tot_igst_op = 0;
		$tot_cess_op = 0;
		
		$sql = 'select part_id, sum(qty) as qty, stock_sale.vat as vat, sum(vat_value) as vat_value, stock_sale.excise as excise, sum(excise_value) as excise_value, sum(stock_sale.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from stock_sale left join stock_available on stock_available.sno = part_id left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and stock_sale.vat not in (0,"") and stock_available.inv_type!="exempt" group by state';		
		//echo $sql;
		
		$result_taxable = execute_query($sql);
		while($row_taxable = mysqli_fetch_array($result_taxable)){
			$taxable += $row_taxable['taxable_amount'];
			$tot_taxable_op += $row_taxable['taxable_amount'];
			if(strtoupper(trim($row_taxable['state']))!=strtoupper(trim($state))){
				$tot_igst_op = $tot_igst_op+$row_taxable['vat_value']+$row_taxable['excise_value'];
				$igst_value += $row_taxable['vat_value']+$row_taxable['excise_value'];
			}
			if(strtoupper(trim($row_taxable['state']))==strtoupper(trim($state))){
				$sgst_value += $row_taxable['vat_value'];
				$cgst_value += $row_taxable['excise_value'];
				$tot_vat_op += $row_taxable['vat_value'];
				$tot_excise_op += $row_taxable['excise_value'];
			}
		}
		?>
   		<tr>
   			<td>(a)Outward txbl. supplies (other than zero rated, null rated and exempted)</td>
   			<td class="right"><?php echo round($taxable,2); ?></td>
   			<td class="right"><?php echo round($igst_value,2); ?></td>
   			<td class="right"><?php echo round($cgst_value,2); ?></td>
   			<td class="right"><?php echo round($sgst_value,2); ?></td>
   			<td></td>
   		</tr>
   		
        <?php
		$sql = 'select part_id, sum(qty) as qty, stock_sale.vat as vat, sum(vat_value) as vat_value, stock_sale.excise as excise, sum(excise_value) as excise_value, sum(stock_sale.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from stock_sale left join stock_available on stock_available.sno = part_id left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and stock_sale.vat="0"';
		
		//echo $sql;
		$result_taxable = execute_query($sql);
		$row_taxable = mysqli_fetch_array($result_taxable);
		
		$tot_taxable_op += $row_taxable['taxable_amount'];
		if($row_taxable['state']!="UTTAR PRADESH"){
			$tot_igst_op = $tot_igst_op+$row_taxable['vat_value']+$row_taxable['excise_value'];
			$row_taxable['igst_value'] = $row_taxable['vat_value']+$row_taxable['excise_value'];
		}
		else{
			$tot_vat_op += $row_taxable['vat_value'];
			$tot_excise_op += $row_taxable['excise_value'];
			$row_taxable['igst_value'] = '';
		}
		?>
   		<tr>
   			<td>(b)Outward txbl. supplies (zero rated)</td>
   			<td class="right"><?php echo round($row_taxable['taxable_amount'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['igst_value'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['vat_value'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['excise_value'],2); ?></td>
   			<td></td>
   		</tr>
   		
        <?php
		$sql = 'select part_id, sum(qty) as qty, stock_sale.vat as vat, sum(vat_value) as vat_value, stock_sale.excise as excise, sum(excise_value) as excise_value, sum(stock_sale.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from stock_sale left join stock_available on stock_available.sno = part_id left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and (stock_sale.vat="" or stock_available.inv_type="exempt")';
		
		//echo $sql;
		$result_taxable = execute_query($sql);
		$row_taxable = mysqli_fetch_array($result_taxable);
		
		$tot_taxable_op += $row_taxable['taxable_amount'];
		if($row_taxable['state']!="UTTAR PRADESH"){
			$tot_igst_op = $tot_igst_op+$row_taxable['vat_value']+$row_taxable['excise_value'];
			$row_taxable['igst_value'] = $row_taxable['vat_value']+$row_taxable['excise_value'];
		}
		else{
			$tot_vat_op += $row_taxable['vat_value'];
			$tot_excise_op += $row_taxable['excise_value'];
			$row_taxable['igst_value'] = '';
		}
		?>
   		<tr>
   			<td>(c)Outward txbl. supplies (nil rated, exempted)</td>
   			<td class="right"><?php echo round($row_taxable['taxable_amount'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['igst_value'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['vat_value'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['excise_value'],2); ?></td>
   			<td></td>
   		</tr>
   		
   		<?php
		$sql = 'select part_id, sum(qty) as qty, stock_sale.vat as vat, sum(vat_value) as vat_value, stock_sale.excise as excise, sum(excise_value) as excise_value, sum(stock_sale.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state from stock_sale left join stock_available on stock_available.sno = part_id left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and inv_type_purchase="YES"';
		
		//echo $sql;
		$result_taxable = execute_query($sql);
		$row_taxable = mysqli_fetch_array($result_taxable);
		
		$tot_taxable_op += $row_taxable['taxable_amount'];
		if($row_taxable['state']!="UTTAR PRADESH"){
			$tot_igst_op = $tot_igst_op+$row_taxable['vat_value']+$row_taxable['excise_value'];
			$row_taxable['igst_value'] = $row_taxable['vat_value']+$row_taxable['excise_value'];
		}
		else{
			$tot_vat_op += $row_taxable['vat_value'];
			$tot_excise_op += $row_taxable['excise_value'];
			$row_taxable['igst_value'] = '';
		}
		?>
   		
   		<tr>
   			<td>(d)Inward supplies (liable to Rev. charge)</td>
   			<td class="right"><?php echo round($row_taxable['taxable_amount'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['igst_value'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['vat_value'],2); ?></td>
   			<td class="right"><?php echo round($row_taxable['excise_value'],2); ?></td>
   			<td></td>
   		</tr>
   		
   		<?php
		$sql = 'select part_id, sum(qty) as qty, sum(stock_sale.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state, `stock_available`.`inv_type` from stock_sale left join stock_available on stock_available.sno = part_id left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and `stock_available`.`inv_type`="EXEMPT"';
		
		//echo $sql;
		$result_taxable = execute_query($sql);
		$row_taxable = mysqli_fetch_array($result_taxable);
		
		$tot_taxable_op += $row_taxable['taxable_amount'];
		?>
   		
   		<tr>
   			<td>(e)Non-GST outward supplies</td>
   			<td class="right"><?php echo round($row_taxable['amount'],2); ?></td>
   			<td class="right"></td>
   			<td class="right"></td>
   			<td class="right"></td>
   			<td></td>
   		</tr>
   		
   		<tr>
   			<th class="right">Total :</th>
   			<th class="right"><?php echo round($tot_taxable_op,2); ?></th>
   			<th class="right"><?php echo round($tot_igst_op,2); ?></th>
   			<th class="right"><?php echo round($tot_vat_op,2); ?></th>
   			<th class="right"><?php echo round($tot_excise_op,2); ?></th>
   			<th class="right">0.00</th>
   		</tr>
    	</tbody>
    </table>
    
    <table>
		<tr>
			<th colspan="4" class="left">3.2 Of the Supplies shown in 3.1(a) above, details of inter-State supplies made to unregisteres persons, composition taxable persons and UIN holders</th>
		</tr>
        <tbody>
        <tr>
        	<td rowspan="2" class="center">Place of Supply(State/UT)</td>
        	<td colspan="2" class="center">Supplies made to UnReg. Persons</td>
        	<td colspan="2" class="center">Supp. made to Composition Dealers</td>
        	<td colspan="2" class="center">Supplies made to UIN holders</td>
        </tr>
        <tr>
   			<td class="center">Total Taxable value</td>
   			<td class="center">Amount of Integrated Tax</td>
   			<td class="center">Total Taxable value</td>
   			<td class="center">Amount of Integrated Tax</td>
   			<td class="center">Total Taxable value</td>
   			<td class="center">Amount of Integrated Tax</td>
   		</tr>
   		<tr>
   			<td class="center">1</td>
   			<td class="center">2</td>
   			<td class="center">3</td>
   			<td class="center">4</td>
   			<td class="center">5</td>
   			<td class="center">6</td>
   			<td class="center">7</td>
   		</tr>
   		<?php
		$sql = 'select part_id, sum(qty) as qty, sum(stock_sale.taxable_amount) as taxable_amount, part_no, sum(amount) as amount, state, `stock_available`.`inv_type` from stock_sale left join stock_available on stock_available.sno = part_id left join invoice_sale on invoice_sale.sno = stock_sale.invoice_no left join customer on customer.sno = invoice_sale.supplier_id where invoice_type="TAX" and  part_dateofpurchase>="'.$_SESSION['gst_date_from'].'" and part_dateofpurchase<="'.$_SESSION['gst_date_to'].'" and `customer`.`state`!="'.$state.'" group by `state`';
		
		?>
    	</tbody>
    </table>
    
    <table>
    	<tr>
    		<th colspan="5" class="left">4. Eligible ITC</th>
    	</tr>
    	<tr>
    		<th>Details</th>
    		<th>Itegrated Tax</th>
    		<th>Central Tax</th>
    		<th>State/UT Tax</th>
    		<th>Cess</th>
    	</tr>
    	<tr>
    		<td>(A) ITC Available(whether in full or part)</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(1) Import of goods</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(2) Import of services</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(3) Inward supplies liable to reverse (other than 1 &amp; 2 above)</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(4) Inward supplies from ISD</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(5) All other ITC</td>
    	</tr>
    	<tr>
    		<td>(B) ITC Reversed</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(1) As per rules 42 &amp; 43 of CGST Rules</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(2) Others</td>
    	</tr>
    	<tr>
    		<td>(C) Net ITC Available(A)-(B)</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td>(D) Ineligible ITC</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    		<td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(1) As per section 17(5)</td>
    	</tr>
    	<tr>
    		<td class="left" style="padding-left:40px;">(2) Others</td>
    	</tr>
    </table>
    
    <table>
    	<tr>
    		<th colspan="3" class="left">5. Values of exempt, nil-rated and non-GST inward supplies</th>
    	</tr>
    	<tr>
    		<th>Nature of supplies</th>
    		<th>Inter-State supplies</th>
    		<th>Intra-State supplies</th>
    	</tr>
    	<tr>
    		<td>From a supplier under composition scheme, Exempt and Nil rated supply</td>
		</tr>
   		<tr>
    		<td>Non GST Supply</td>
		</tr>
	</table>
   <table>
   	<tr>
   		<th colspan="7">3.2  Of the supplies shown in 3.1 (a), details of inter-state supplies made to unregistered persons, composition taxable person and UIN holders</th>
   	</tr>
   	<tr>
   		<th rowspan="2">Place of Supply(State/UT)</th>
   		<th colspan="2">Supplies made to Unregistered Persons</th>
   		<th colspan="2">Supplies made to Composition Taxable Persons</th>
   		<th colspan="2">Supplies made to UIN holders</th>
   	</tr>
   	<tr>
   		<th>Total Taxable value</th>
   		<th>Amount of Integrated Tax</th>
   		<th>Total Taxable value</th>
   		<th>Amount of Integrated Tax</th>
   		<th>Total Taxable value</th>
   		<th>Amount of Integrated Tax</th>
   	</tr>
   </table>
    
	<form id="purchase_report" name="purchase_report" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">	
		<table width="100%" class="no-print">
			<tr>
				<th colspan="2">
					<input type="submit" name="submit_form" value="Create JSON" class="btTxt submit">
				</th>
			</tr>
		</table>
	</form>
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