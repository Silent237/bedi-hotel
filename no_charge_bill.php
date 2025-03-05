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
$sno=1;
if(isset($_GET['table_id'])){
	$tableid=$_GET['table_id'];
	$sql='SELECT * FROM `res_table` where sno="'.$_GET['table_id'].'"';
	$res=execute_query($sql);
	$row=mysqli_fetch_array($res);
	$t_no=$row['table_number'];

}
else{
	$tableid=$_GET['room_id'];
	$sql="SELECT * FROM `room_master` where sno='".$_GET['room_id']."'";
	$res=execute_query($sql);
	$row=mysqli_fetch_array($res);
	$t_no=$row['room_name'];
}
	$total=0;
	//print_r($_POST);
	if($_POST['customer_sno']!=''){
		$supplier = $_POST['customer_sno'];
	}
	elseif($_POST['customer_name']!=''){
		$supplier = add_customer($_POST['customer_name'], $_POST['customer_address'], $_POST['customer_mobile'], $_POST['customer_gstin']);
	}

	$sql="SELECT `description`, `vat`, `excise`, `kitchen_ticket_temp`.`unit` as qty, item_price FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid'";
	$res=execute_query($sql);
	$tot_taxable_hidden=0;
	$total_cgst_hidden=0;
	$total_sgst_hidden=0;
	$tot_tax=0;
	$taxable=0;
	$tot_qty_hidden=0;
	$total_amount_hidden=0;
	while($row=mysqli_fetch_array($res)){
		$tot_tax_rate = $row['vat']+$row['excise'];
		//$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
		$base_price=$row['item_price'];
		$taxable = $base_price*$row['qty'];
		$cgst_amt = (round((($taxable*$row['vat'])/100),2));
		$sgst_amt = (round((($taxable*$row['excise'])/100),2));
		$e_price = round($row['item_price'] + (($row['item_price']*$row['vat'])/100) + (($row['item_price']*$row['vat'])/100),2);
		$total=$e_price*$row['qty'];
		$tot_taxable_hidden+=$taxable;
		$total_cgst_hidden+=$cgst_amt;
		$total_sgst_hidden+=$sgst_amt;
		$total_amount_hidden+=$total;
		$tot_qty_hidden += $row['qty'];
		$round_off = $total_amount_hidden;
		$dummy_grand_total = round($total_amount_hidden, 2);
		$round_off = round(round($total_amount_hidden,0) - $dummy_grand_total,2);
		$grand_total = $total_amount_hidden+$round_off;
	}
		$round_off = $total_amount_hidden;
		$dummy_grand_total = round($total_amount_hidden, 2);
		$round_off = round(round($total_amount_hidden,0) - $dummy_grand_total,2);
		$grand_total = $total_amount_hidden+$round_off;
		$sale_date== date("Y-m-d");
		$timestamp=date("Y-m-d H:i:s");

	$sql = 'INSERT INTO `invoice_sale_restaurant` (`invoice_type`, `invoice_no`, `total_amount`, `taxable_amount`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `tot_disc`, `other_discount`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`,`table_no`,`waitor_name`) 
	VALUES ("", "", "'.$total_amount_hidden.'", "'.$tot_taxable_hidden.'", "'.$sale_date.'", "'.$_SESSION['username'].'", "'.$timestamp.'", "", "", "'.$tot_qty_hidden.'", "'.$total_cgst_hidden.'", "'.$total_sgst_hidden.'",  "'.$round_off.'", "", "", "'.$grand_total.'", "'.$storeid.'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'", "'.$year.'", "nocharge","'.$t_no.'","")';
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 1  : '.mysqli_error($db).' >> '.$sql.'</li>';
		$inv=0;
	}
	else{
		$inv = insert_id($db);
	}
	$sql="SELECT `description`, `vat`, `excise`, `kitchen_ticket_temp`.`unit` as qty, item_price, item_id, time_stamp FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid'";
	//echo $sql;
	$res=execute_query($sql);
	$i=1;
	while($row=mysqli_fetch_array($res)){
		$unit = 26;
		$tot_tax_rate = $row['vat']+$row['excise'];
		//$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
		$base_price=$row['item_price'];
		$unitprice = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
		$taxable_amount = $base_price*$row['qty'];
		$vat = $row['vat'];
		$vat_value = ($row['qty']*round((($taxable_amount*$row['vat'])/100),2));
		$sat = $row['excise'];
		$sat_value = ($row['qty']*round((($taxable_amount*$row['excise'])/100),2));
		$qty = $row['qty'];
		$total = $base_price*$qty;
		$eprice = $row['item_price']/$row['qty'];
		$product_id = $row['item_id'];
		
		$sql = "INSERT INTO `stock_sale_restaurant` (`invoice_no`, `supplier_id`, `part_id`, `basicprice`, `discount`, `discount_value`, `vat`, `vat_value`, `excise`, `excise_value`, `taxable_amount`, `effective_price`, `qty`, `part_dateofpurchase`, `amount`, `unit`, `admin_remarks`) 
		VALUES ('', '', '".$product_id."', '".$unitprice."', '', '', '".$vat."', '".$vat_value."', '".$sat."', '".$sat_value."', '".$taxable_amount."', '".$base_price."', '".$qty."', '".$sale_date."', '".$total."', '".$unit."', '".$time_stamp."')";
		//echo $sql.'<br>';
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
		}
	}
	$sql = "INSERT INTO `customer_transactions` (`cust_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`,mop) 
	VALUES ('".$supplier."', 'sale_restaurant', '".$inv."', '".$_POST['grand_total_hidden']."', '".$_POST['sale_date']."', 'nocharge', 0, '".$year."','".$_POST['invoice_no']."','".$mop."')";
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 3 : '.mysqli_error($db).' >> '.$sql.'</li>';
	}
	if($msg==''){
		$sql="DELETE FROM `kitchen_ticket_temp` WHERE table_id='$tableid'";
		execute_query($sql);
		if(isset($_GET['table_id'])){
			$tableid=$_GET['table_id'];
			$sql="UPDATE `res_table` SET `booked_status`='0' WHERE sno='$tableid'";
			execute_query($sql);
		}
		else{
			$tableid=str_replace("room_", "", $_GET['room_id']);
			$sql="UPDATE `room_master` SET `booked_status`='0' WHERE sno='$tableid'";
			execute_query($sql);
		}
	}
?>
<div id="container">
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>	
	<div id="div_button">
			<button name="cash" type="button" onClick="window.open('dine_in_order.php', '_self');">Back To Home</button>
			
	</div>
</div>
<?php
page_footer(); 
?> 	