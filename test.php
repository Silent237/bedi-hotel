<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
error_reporting(E_ALL);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
$con = connect();

//serialize_invoice_restaurant();
//serialize_invoice();
//date_change();
//date_change1();
invoice_verify();
function date_change(){
	$sql = 'select * from allotment';
	$result=mysqli_fetch_assoc(execute_query($sql));
	echo '<table border=1>';
	foreach($result as $row){
		$sql = 'update allotment set allotment_date = "'.date("Y-m-d H:i", strtotime($row['allotment_date'])).'"';
		if($row['exit_date']!=''){
			$sql .= ', exit_date="'.date("Y-m-d H:i", strtotime($row['exit_date'])).'"';
		}
		$sql .= ' where sno='.$row['sno'];
		$result = execute_query($sql);
		echo '<tr>
		<td>'.$row['allotment_date'].'</td>
		<td>'.date("Y-m-d H:i", strtotime($row['allotment_date'])).'</td>
		<td>'.$row['exit_date'].'</td>
		<td>'.date("Y-m-d H:i", strtotime($row['exit_date'])).'</td><td>'.$sql.'</td></tr>';
	}	
}
function date_change1(){
	$sql = 'select * from customer_transactions';
	$result=mysqli_fetch_assoc(execute_query($sql));
	echo '<table border=1>';
	foreach($result as $row){
		$sql = 'update customer_transactions set timestamp = "'.date("Y-m-d H:i", strtotime($row['timestamp'])).'"';
		$sql .= ' where sno='.$row['sno'];
		$result = execute_query($sql);
		echo '<tr>
		<td>'.$row['timestamp'].'</td>
		<td>'.date("Y-m-d H:i", strtotime($row['timestamp'])).'</td></tr>';
	}	
}

function serialize_invoice(){
    global $db;
	$sql = 'select * from allotment order by exit_date';
	$result=execute_query($sql);
	echo '<table border=1>';
	$i=161;
	while($row = mysqli_fetch_assoc($result)){
	    if($row['exit_date']==''){
	        $a=$i;
	        $i=0;
	    }
		$sql = 'update allotment set invoice_no = "'.$i.'" where sno='.$row['sno'];
		execute_query($sql);
		if(mysqli_error($db)){
		    die("Error # 1.02 : ".mysqli_error($db).' >> '.$sql);
		}
		$sql = 'update allotment_2 set invoice_no = "'.$i.'" where sno='.$row['sno'];
		execute_query($sql);
		if(mysqli_error($db)){
		    die("Error # 1.03 : ".mysqli_error($db).' >> '.$sql);
		}
		$sql = 'update customer_transactions set invoice_no = "'.$i.'" where allotment_id='.$row['sno'];
		execute_query($sql);
		if(mysqli_error($db)){
		    die("Error # 1.04 : ".mysqli_error($db).' >> '.$sql);
		}
		if($row['exit_date']==''){
	        $i=$a;
	    }
	    else{
	        $i++;
	    }
		
		
	}
	echo 'Done';
}

function serialize_invoice_restaurant(){
    global $db;
    $sql = 'SELECT * FROM `invoice_sale_restaurant` where storeid like "room%" and mode_of_payment!="nocharge" order by dateofdispatch';
    $sql = 'SELECT * FROM `invoice_sale_restaurant` where storeid not like "room%" and mode_of_payment!="nocharge"';
    $result = execute_query($sql);
    $i=1;
    while($row = mysqli_fetch_assoc($result)){
        //$sql = 'update invoice_sale_restaurant set invoice_no="R'.$i++.'" where sno='.$row['sno'];
         $sql = 'update invoice_sale_restaurant set invoice_no="M'.$i++.'" where sno='.$row['sno'];
        execute_query($sql);
        if(mysqli_error($db)){
            die("Error # 1 : ".$sql." >> ".mysqli_error($db));
        }
    }
    echo 'Completed';
}

/*Function to Update Calculation of Sale Invoices*/
function tax_update(){
	global $db;
	$sql = 'select * from invoice_sale';
	$result = execute_query($sql);
	while($row = mysqli_fetch_array($result)){
		$taxable=0;
		$tot_vat = 0;
		$tot_total = 0;
		$sql='select * from stock_sale where invoice_no='.$row['sno'];
		$result_stock = execute_query($sql);
		while($row_stock = mysqli_fetch_array($result_stock)){
			if(strpos($row_stock['discount'], "%")===false){
				
			}
			else{
				$row_stock['discount_value'] = $row_stock['basicprice']*$row_stock['discount']/100;
			}
			$basicprice = $row_stock['basicprice'] - $row_stock['discount_value'];
			$taxable_amount = $basicprice * $row_stock['qty'];
			
			$sql = 'select * from stock_available where sno='.$row_stock['part_id'];
			$stock_details = mysqli_fetch_assoc(execute_query($sql));
			
			$tax = str_replace("%", "", $stock_details['vat']);
			$vat_unit_value = $basicprice*$tax/100;
			$eff_price = $basicprice+$vat_unit_value+$vat_unit_value;
			$eff_price = round($eff_price,2);
			$vat_value = ($taxable_amount*$tax)/100;
			$taxable += $taxable_amount;
			$tot_vat+=$vat_value;
			$total = $taxable_amount+$vat_value+$vat_value;
			$tot_total += $total;
			$sql = 'update stock_sale set vat="'.$stock_details['vat'].'", excise="'.$stock_details['vat'].'", vat_value="'.$vat_value.'", excise_value="'.$vat_value.'", taxable_amount="'.$taxable_amount.'", amount="'.$total.'", effective_price="'.$eff_price.'" where s_no='.$row_stock['s_no'];
			execute_query($sql);
			if(mysqli_error($db)){
				echo 'Error >> '.mysqli_error($db).' >> '.$sql.' <br>';
			}
		}
		$tot_vat = round($tot_vat,2);
		$sql = 'update invoice_sale_restaurant set taxable_amount="'.$taxable.'", tot_vat="'.$tot_vat.'", tot_sat="'.$tot_vat.'", total_amount="'.$tot_total.'" where sno='.$row['sno'];
		execute_query($sql);
		if(mysqli_error($db)){
			echo 'Error ## '.mysqli_error($db).' >> '.$sql.' <br>';
		}
		
	}
	echo '<h1>Sale Update Done</h1>';
}

/*Function to Cross Verify Invoice*/
function invoice_verify($date_from='1970-01-01', $date_to=''){
	global $db;
	if($date_to==''){
		$date_to = date('Y-m-d', strtotime(date("Y-m-d") . " +1 days"));
	}
	else{
		$date_to = date('Y-m-d', strtotime($date_to . " +1 days"));
	}
	$sql = 'select * from invoice_sale_restaurant where invoice_no!="" order by dateofdispatch desc';
	$result = execute_query($sql);
	echo '
	<style>
	#header-fixed {
	  position: -webkit-sticky; // this is for all Safari (Desktop & iOS), not for Chrome
    position: sticky;
    top: 0;
    z-index: 1; // any positive value, layer order is global
    background: #fff; // any bg-color to overlap
	}
	</style>
	
	<table class=" table table-striped table-hover table-bordered" border="1" cellspacing="0" cellpadding="0">
	<tr>
	<th colspan="14">Sale Invoice Calcualtion Verification</th>
	</tr>
	<tr id="header-fixed">
	<th>S.No.</th>
	<th>Serial</th>
	<th>Invoice No</th>
	<th>Supplier ID</th>
	<th>Date</th>
	<th>Qty</th>
	<th>Total Amount1</th>
	<th>Discount</th>
	<th>Discount Amount1</th>
	<th>Taxable Amount</th>
	<th>SGST Amount</th>
	<th>CGST Amount</th>
	<th>Total Tax</td>
	<th>Invoice Total</td>
	<th>SC Rate</td>
	<th>SC Amount</td>
	<th>SC Tax Rate</td>
	<th>SC Tax Amount</td>
	<th>SC Total</td>
	<th>Grand Total</td>
	<th>Stock Qty</th>
	<th>Stock Amount</th>
	<th>Stock Disc</th>
	<th>Stock Disc Amount</th>
	<th>Stock Taxable</th>
	<th>Stock CGST</th>
	<th>Stock SGST</th>
	<th>Stock Tax</th>
	<th>Stock Total</th>
	<th>Calc Rate</th>
	<th>Calc Disc</th>
	<th>Calc Taxable</th>
	<th>Calc CGST</th>
	<th>Calc SGST</th>
	<th>Calc Tax</th>
	<th>Calc Amount</th>
	</tr>';
	$tot_inv_qty=0;
	$tot_inv_amount=0;
	$tot_stock_qty=0;
	$tot_stock_amount=0;
	$i=1;
	$msg='0';
	$bg='';
	while($row = mysqli_fetch_array($result)){
		$sql = 'select * from stock_sale_restaurant where invoice_no="'.$row['sno'].'"';
		$result_stock = execute_query($sql);
		$tot_stock_qty=0;
		$tot_stock_rate=0;
		$tot_stock_disc=0;
		$tot_stock_disc_amt=0;
		$tot_stock_taxable=0;
		$tot_stock_cgst=0;
		$tot_stock_sgst=0;
		$tot_stock_tax=0;
		$tot_stock_total=0;
		
		$tot_calc_taxable=0;
		$tot_calc_disc=0;
		$tot_calc_vat=0;
		$tot_calc_sat=0;
		$tot_calc_amt=0;
		$stock_html = '';
		$rowspan=mysqli_num_rows($result_stock);
		$rowspan++;
		$b=1;
		while($row_stock = mysqli_fetch_assoc($result_stock)){
			$sql = 'select * from stock_available where sno="'.$row_stock['part_id'].'"';
			$stock_detail = mysqli_fetch_assoc(execute_query($sql));
			
			$row_stock['discount'] = ($row['other_discount']==''?0:$row_stock['discount']);
			
			$mrp = $stock_detail['mrp'];
			$row_stock['discount'] = str_replace("%", "", $row_stock['discount']);
			$stock_detail['vat'] = str_replace("%", "", $stock_detail['vat']);
			$stock_detail['excise'] = str_replace("%", "", $stock_detail['excise']);
			$disc_value_unit = (float)$stock_detail['mrp']*(float)$row_stock['discount']/100;
			$taxable = $row_stock['qty']*($mrp-$disc_value_unit);
			$disc_value = $row_stock['qty']*$disc_value_unit;
			$vat = ($taxable*(float)$stock_detail['vat']/100);
			$sat = ($taxable*(float)$stock_detail['vat']/100);
			$tot_tax = $vat+$sat;
			$tot_amt = ($taxable)+$vat+$sat;
			
			$eprice = $mrp-$disc_value_unit;
			$eprice = $eprice+($eprice*($stock_detail['vat']+$stock_detail['excise']))/100;
			
			$tot_calc_taxable+=$taxable;
			$tot_calc_disc+=$disc_value;
			$tot_calc_vat+=$vat;
			$tot_calc_sat+=$sat;
			$tot_calc_amt+=$tot_amt;
			
			
			if($b==1){
				$stock_html .= '
				<tr>
				<td rowspan="'.$rowspan.'">'.$i++.'</td>
				<td rowspan="'.$rowspan.'">'.$row['sno'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['invoice_no'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['supplier_id'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['dateofdispatch'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['quantity'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['total_amount1'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['other_discount'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['tot_disc'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['taxable_amount'].'</td>
				<td rowspan="'.$rowspan.'">'.((float)$row['tot_vat']+(float)$row['service_charge_tax_amount']/2).'</td>
				<td rowspan="'.$rowspan.'">'.((float)$row['tot_sat']+(float)$row['service_charge_tax_amount']/2).'</td>
				<td rowspan="'.$rowspan.'">'.((float)$row['tot_sat']+(float)$row['tot_vat']).'</td>
				<td rowspan="'.$rowspan.'">Rs.'.$row['total_amount'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['service_charge_rate'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['service_charge_amount'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['service_charge_tax_rate'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['service_charge_tax_amount'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['service_charge_total'].'</td>
				<td rowspan="'.$rowspan.'">'.$row['grand_total'].'</td>
				<td>'.$row_stock['qty'].'</td>
				<td>'.$stock_detail['mrp'].'</td>
				<td>'.$row_stock['discount'].'</td>
				<td>'.$row_stock['discount_value'].'</td>
				<td>'.$row_stock['taxable_amount'].'</td>
				<td>'.$row_stock['vat_value'].'</td>
				<td>'.$row_stock['excise_value'].'</td>
				<td>'.((float)$row_stock['excise_value']+(float)$row_stock['vat_value']).'</td>
				<td>'.$row_stock['amount'].'</td>
				<td>'.$mrp.'</td>
				<td>'.$disc_value.'</td>
				<td>'.$taxable.'</td>
				<td>'.$vat.'</td>
				<td>'.$sat.'</td>
				<td>'.$tot_tax.'</td>
				<td>'.$tot_amt.'</td>
				</tr>
				';
				$b++;
			}
			else{
				$stock_html .= '<tr>
				<td>'.$row_stock['qty'].'</td>
				<td>'.$stock_detail['mrp'].'</td>
				<td>'.$row_stock['discount'].'</td>
				<td>'.$row_stock['discount_value'].'</td>
				<td>'.$row_stock['taxable_amount'].'</td>
				<td>'.$row_stock['vat_value'].'</td>
				<td>'.$row_stock['excise_value'].'</td>
				<td>'.($row_stock['excise_value']+$row_stock['vat_value']).'</td>
				<td>'.$row_stock['amount'].'</td>
				<td>'.$mrp.'</td>
				<td>'.$disc_value.'</td>
				<td>'.$taxable.'</td>
				<td>'.$vat.'</td>
				<td>'.$sat.'</td>
				<td>'.$tot_tax.'</td>
				<td>'.$tot_amt.'</td></tr>
				';
			}
			
			$sql = 'update stock_sale_restaurant set
			mrp="'.$mrp.'",
			discount="'.$row['other_discount'].'",
			discount_value="'.round($disc_value,2).'",
			basicprice="'.round(($mrp-$disc_value_unit),2).'",
			vat_value="'.round($vat,2).'",
			excise_value="'.round($sat,2).'",
			taxable_amount="'.round($taxable,2).'",
			effective_price="'.round($eprice,2).'",
			amount="'.round($tot_amt,2).'"
			where s_no="'.$row_stock['s_no'].'"';
			//execute_query($sql);
			if(mysqli_error($db)){
				die("Err : ".mysqli_error($db).'>>'.$sql);
			}
			echo $sql.'<br>';
			
			$tot_stock_qty+=(float)$row_stock['qty'];
			$tot_stock_disc_amt+=(float)$row_stock['discount_value'];
			$tot_stock_taxable+=(float)$row_stock['taxable_amount'];
			$tot_stock_cgst+=(float)$row_stock['vat_value'];
			$tot_stock_sgst+=(float)$row_stock['excise_value'];
			$tot_stock_tax+=(float)$row_stock['excise_value']+(float)$row_stock['vat_value'];
			$tot_stock_total+=(float)$row_stock['amount'];

		}
		if($msg!=''){
			/*echo '<tr style="background:'.$bg.'">
			<td rowspan="'.$rowspan.'">'.$i++.'</td>
			<td rowspan="'.$rowspan.'">'.$row['sno'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['invoice_no'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['supplier_id'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['dateofdispatch'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['quantity'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['total_amount1'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['other_discount'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['tot_disc'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['taxable_amount'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['tot_vat'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['tot_sat'].'</td>
			<td rowspan="'.$rowspan.'">'.((float)$row['tot_sat']+(float)$row['tot_vat']).'</td>
			<td rowspan="'.$rowspan.'">'.$row['total_amount'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['service_charge_rate'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['service_charge_amount'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['service_charge_tax_rate'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['service_charge_amount'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['service_charge_total'].'</td>
			<td rowspan="'.$rowspan.'">'.$row['grand_total'].'</td>
			<td>'.$tot_stock_qty.'</td>
			</tr>';*/
			if(abs($tot_calc_amt-$row['total_amount'])>1){
				$bgcol = 'background:#f00; color:fff;';
			}
			else{
				$bgcol = '';
			}
			
			echo $stock_html;
			echo '<tr style="'.$bgcol.'">
			<th>'.$tot_stock_qty.'</th>
			<th></th>
			<th></th>
			<th>'.$tot_stock_disc_amt.'</th>
			<th>'.$tot_stock_taxable.'</th>
			<th>'.$tot_stock_cgst.'</th>
			<th>'.$tot_stock_sgst.'</th>
			<th>'.$tot_stock_tax.'</th>
			<th>'.$tot_stock_total.'</th>
			<th></th>
			<th>'.$tot_calc_disc.'</th>
			<th>'.$tot_calc_taxable.'</th>
			<th>'.$tot_calc_sat.'</th>
			<th>'.$tot_calc_vat.'</th>
			<th></th>
			<th>'.$tot_calc_amt.'</th>

			</tr>';
			
			$grand = round(($row['service_charge_total']+$tot_calc_amt),2);
			$round_off = round(round($grand,0)-$grand,2);
			$grand = round($grand,0);

			$sql = 'update invoice_sale_restaurant set
			taxable_amount="'.round($tot_calc_taxable,2).'",
			tot_vat="'.round($tot_calc_vat,2).'",
			tot_sat="'.round($tot_calc_sat,2).'",
			tot_disc="'.round($tot_calc_disc,2).'",
			total_amount="'.round($tot_calc_amt,2).'",
			round_off="'.$round_off.'",
			grand_total="'.$grand.'"
			where sno="'.$row['sno'].'"';
			echo round(($row['service_charge_total']+$tot_calc_amt),2).'>>'.$sql.'<br><br>';
			//execute_query($sql);
			if(mysqli_error($db)){
				die("Err : ".mysqli_error($db).'>>'.$sql);
			}
			
			
		
		}
	}	
	echo '<tr>
	<th colspan="5">&nbsp;</th>
	<th>'.$tot_inv_qty.'</th>
	<th>'.$tot_inv_amount.'</th>
	<th>'.$tot_stock_qty.'</th>
	<th>'.$tot_stock_amount.'</th></tr>';
	
	
}

?>