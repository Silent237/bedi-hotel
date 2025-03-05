<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$tableid=$_GET['table_sno'];
?>
<style>
#bill_div{
	position: absolute;
	top:9%;
	left:6%;
	height: auto;
	width:16%;
	border:1px solid black;
}
#print{
	
}
#print button{
	position: absolute;
	top:8%;
	left:86%;
	height: 60px;
	width: 100px;
}
</style>
<div id="bill_div">
		<?php
		$total=0;
		$supplier=1;
		$_POST['sale_date'] = date("Y-m-d");
		$_POST['invoice_type'] = 'tax';
		$_POST['storeid']=$tableid;
		$date = $_POST['sale_date'];
		$time = strtotime($date);
		$month = date("m",$time);
		$year = date("Y",$time);
		if($month>=1 && $month<=3){
			$year = $year-1;
		}
		$sql = 'select * from invoice_sale where invoice_type="'.$_POST['invoice_type'].'" and financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			//echo $sql;
		$invoice_result = execute_query($sql);
		if(mysqli_num_rows($invoice_result)!=0){
			$invoice_no = mysqli_fetch_array($invoice_result);
			$_POST['invoice_no'] = $invoice_no['invoice_no']+1;
		}
		else{
			$_POST['invoice_no'] = 1;
		}

		$sql = 'INSERT INTO `invoice_sale` (`invoice_type`, `invoice_no`, `total_amount`, `taxable_amount`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`) 
		VALUES ("'.$_POST['invoice_type'].'", "'.$_POST['invoice_no'].'", "'.$_POST['total_amount_hidden'].'", "'.$_POST['tot_taxable_hidden'].'", "'.$_POST['sale_date'].'", "'.$_SESSION['username'].'", "'.$_POST['sale_date'].'", "'.$supplier.'", "", "'.$_POST['tot_qty_hidden'].'", "'.$_POST['total_cgst_hidden'].'", "'.$_POST['total_sgst_hidden'].'",  "'.$_POST['round_off'].'", "'.$_POST['grand_total'].'", "'.$_POST['storeid'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'", "'.$year.'", "cash")';
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 1  : '.mysqli_error($db).' >> '.$sql.'</li>';
			$inv=0;
		}
		else{
			$inv = insert_id($db);
		}
		$sql="SELECT * FROM `kitchen_ticket_temp` WHERE table_id='$tableid'";
		$sql="SELECT `description`, `vat`, `excise`, `kitchen_ticket_temp`.`unit` as qty, item_price, item_id, time_stamp FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid'";
		$res=execute_query($sql);
		$i=1;
		while($row=mysqli_fetch_array($res)){
			$unit = 26;

			$tot_tax_rate = $row['vat']+$row['excise'];
			$base_price = round($row['item_price']/(1+($tot_tax_rate/100)),2);
			$unitprice = round($row['item_price']/(1+($tot_tax_rate/100)),2);

			$vat = $row['vat'];
			$vat_value = ($row['qty']*round((($base_price*$row['vat'])/100),2));
			$sat = $row['excise'];
			$sat_value = ($row['qty']*round((($base_price*$row['excise'])/100),2));

			$taxable_amount = $base_price*$row['qty'];

			$total = $row['item_price']*$row['qty'];
			$eprice = $row['item_price'];
			$product_id = $row['item_id'];
			$qty = $row['qty'];

			$sql = "INSERT INTO `stock_sale` (`invoice_no`, `supplier_id`, `part_id`, `basicprice`, `vat`, `vat_value`, `excise`, `excise_value`, `taxable_amount`, `effective_price`, `qty`, `part_dateofpurchase`, `amount`, `unit`, `admin_remarks`) 
			VALUES ('".$inv."', '".$supplier."', '".$product_id."', '".$unitprice."', '".$vat."', '".$vat_value."', '".$sat."', '".$sat_value."', '".$taxable_amount."', '".$eprice."', '".$qty."', '".$_POST['sale_date']."', '".$total."', '".$unit."', '".$row['time_stamp']."')";
			//echo $sql.'<br>';
			execute_query($sql);
			if(mysqli_error($db)){
				$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
			}
		}
		$sql = "INSERT INTO `customer_transactions` (`cust_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`) 
		VALUES ('".$supplier."', 'sale', '".$inv."', '".$_POST['grand_total']."', '".$_POST['sale_date']."', '', 0, '".$year."','".$_POST['invoice_no']."')";
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 3 : '.mysqli_error($db).' >> '.$sql.'</li>';
		}
		if($msg==''){
			$sql="DELETE FROM `kitchen_ticket_temp` WHERE table_id='$tableid'";
			execute_query($sql);
			$sql="UPDATE `res_table` SET `booked_status`='0' WHERE sno='$tableid'";
			execute_query($sql);
			$msg .= '<li><a href="scripts/printing_sale.php?inv='.$inv.'" target="_blank">Click Here To Print</a></li>';
			$msg .= '<script>window.open("scripts/printing_sale.php?inv='.$inv.'");</script>';
		}
		?>
		<tr>&nbsp;<td colspan="3">&nbsp;&nbsp;<h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grand Total: Rs. <?php echo $total; ?></h3></td></tr>
	</table>
	<?php
	?>

</div>
<div id="print">
	<?php echo $msg; ?>
	<button type="button" id="print"  onclick="printDiv('bill_div')"><h3>Print</h3></button>
</div>
<?php
?>
<script type="text/javascript">
function printDiv(divName) {
   	var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
	window.print();
	document.body.innerHTML = originalContents;
}
</script>
<?php
page_footer();
?>