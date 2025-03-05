<?php
session_cache_limiter('nocache');
session_start();

include ("scripts/settings.php");
require_once('tcpdf/tcpdf.php');

logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$sno=1;
$fill = 0;
if(isset($_GET['table_id'])){
	if(!isset($_GET['type'])){
		$_GET['type'] = 'table';
	}
}

if (!isset($_GET['edit_id'])) {
	if($_GET['type'] == 'room'){
		$room_id = str_replace("room_", "", $_GET['table_id']);
		$sql_inv = 'SELECT * FROM `allotment` WHERE (`exit_date` IS NULL OR `exit_date`="") AND `room_id`="'.$room_id.'" AND (`hold_date` IS NULL OR `hold_date`="")';
		//echo $sql_inv.'<br/>';
		$row_inv = mysqli_fetch_array(execute_query($sql_inv));
		$sql_cust_room = 'SELECT * FROM `customer` WHERE `sno`="'.$row_inv['cust_id'].'"';
		//echo $sql_cust_room;
		$row_cust_room = mysqli_fetch_array(execute_query($sql_cust_room));
		$fill = 1;
	}
	//echo '<script>alert('.$room_allot_id.');</script>';
}
if(isset($_GET['edit_id'])) { 
	$sql_customer1='select * from `customer_transactions` where `number`="'.$_GET['edit_id'].'"';
	//echo $sql_customer1;
	$result_customer1=execute_query($sql_customer1);
	$row_customer1=mysqli_fetch_array($result_customer1);
	$sql_customer='select * from `customer` where `sno`="'.$row_customer1['cust_id'].'"';
	$result_customer=execute_query($sql_customer);
	$row_customer=mysqli_fetch_array($result_customer);
	$sql_discount_waitor='select * from `invoice_sale_restaurant` where `sno`="'.$_GET['edit_id'].'"';
	echo $sql_discount_waitor;
	$result_discount_waitor=execute_query($sql_discount_waitor);
	$row_discount_waitor=mysqli_fetch_array($result_discount_waitor);
} 
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
if(isset($_POST['total_amount_hidden'])){
	if(isset($_POST['tableprint'])){
		$q="UPDATE `general_settings` SET `rate`='yes' WHERE sno='23'";
		execute_query($q);
	}
	else{
		$q="UPDATE `general_settings` SET `rate`='no' WHERE sno='23'";
		execute_query($q);
	}
	
	$response=2;
	$total=0;
	//print_r($_POST);
	if($_POST['edit_pass'] != '') {
		$supplier = add_customer($_POST['customer_name'], $_POST['customer_address'], $_POST['customer_mobile'], $_POST['customer_gstin'] , $_POST['company_name']);
	}
	elseif($_POST['customer_sno']!=''){
		$supplier = $_POST['customer_sno'];
		$c =$_POST['c'];
	}
	elseif($_POST['customer_name']!='' OR $_POST['company_name']!= ''){
		$supplier = add_customer($_POST['customer_name'], $_POST['customer_address'], $_POST['customer_mobile'], $_POST['customer_gstin'] , $_POST['company_name']);
	}
	else{
		$sql = 'select * from general_settings where `desc`="default_cash"';
		$default_cash = mysqli_fetch_assoc(execute_query($sql));
		$supplier=$default_cash['rate'];
	}
	//$_POST['sale_date'] = date("Y-m-d");
	$_POST['invoice_type'] = 'tax';
	if(isset($_POST['non_chargable_invoice'])){
		$_POST['invoice_type'] = 'non_chargable';
		
		$_POST['total_amount_hidden'] = 0;
		$_POST['tot_taxable_hidden'] = 0;
		$_POST['total_cgst_hidden'] = 0;
		$_POST['total_sgst_hidden'] = 0;
		$_POST['tot_discount_hidden'] = 0;
		$_POST['round_off_hidden'] = 0;
		$_POST['grand_total_hidden'] = 0;
		$_POST['service_charge_rate'] = 0;
		$_POST['service_charge_rate_amount'] = 0;
		$_POST['service_charge_tax_rate'] = 0;
		$_POST['service_charge_tax_amount'] = 0;

		
		
	}
	$_POST['storeid']=$tableid;
	$date = $_POST['sale_date'];
	$time = strtotime($date);
	$month = date("m",$time);
	$year = date("Y",$time);
	if($month>=1 && $month<=3){
		$year = $year-1;
	}
	
	
	/*if(isset($_POST['cash'])){
		$mop = 'CASH';
	}
	elseif(isset($_POST['card'])){
		$mop = 'CARD';
	}
	elseif(isset($_POST['paytm'])){
		$mop = 'PAYTM';
	}
	elseif(isset($_POST['swiggy'])){
		$mop = 'SWIGGY';
	}
	elseif(isset($_POST['zomato'])){
		$mop = 'ZOMATO';
	}
	elseif(isset($_POST['easy_dinner'])){
		$mop = 'EASY DINNER';
	}
	elseif(isset($_POST['bank_tansfer'])){
		$mop = 'BANK TRANSFER';
	}
	elseif(isset($_POST['cheque'])){
		$mop = 'CHEQUE';
	}
	elseif(isset($_POST['credit'])){
		$mop = 'credit';
	}
	elseif(isset($_POST['non_chargeable'])){
		$mop = 'nocharge';

	}*/
	
	$mop = $_POST['mode_of_payment'];
	if($_POST['edit_pass'] != '') {
		
		$sql= 'UPDATE `invoice_sale_restaurant` SET
		`invoice_type` = "'.$_POST['invoice_type'].'",
		`concerned_person`="'.$_POST['customer_name'].'",
		`total_amount` ="'.$_POST['total_amount_hidden'].'",
		`taxable_amount` = "'.$_POST['tot_taxable_hidden'].'",
		`user_id` ="'.$_SESSION['username'].'",
		`supplier_id` ="'.$supplier.'",
		`quantity` ="'.$_POST['tot_qty_hidden'].'",
		`tot_vat` ="'.$_POST['total_cgst_hidden'].'",
		`tot_sat` ="'.$_POST['total_sgst_hidden'].'", 
		`round_off` ="'.$_POST['round_off_hidden'].'", 
		`tot_disc` ="'.$_POST['tot_discount_hidden'].'", 
		`other_discount` ="'.$_POST['other_discount'].'", 
		`grand_total` ="'.$_POST['grand_total_hidden'].'", 
		`service_charge_rate` = "'.$_POST['service_charge_rate'].'",
		`service_charge_amount` = "'.$_POST['service_charge_rate_amount'].'",
		`service_charge_tax_rate` = "'.$_POST['service_charge_tax_rate'].'",
		`service_charge_tax_amount` = "'.$_POST['service_charge_tax_amount'].'",
		`service_charge_total` = "'.($_POST['service_charge_rate_amount']+$_POST['service_charge_tax_amount']).'",
		`storeid` ="'.$_POST['storeid'].'", 
		`created_by` ="'.$_SESSION['username'].'",  
		`mode_of_payment` ="'.$mop.'",
		`mop_ref_no` ="'.$_POST['mop_ref_no'].'",
		`mop_details` ="'.$_POST['mop_details'].'",
		`waitor_name`="'.$_POST['waitor_name'].'"
		WHERE `sno`="'.$_POST['edit_pass'].'"';
		//echo $sql;
		execute_query($sql);
		$sql = 'select * from invoice_sale_restaurant where sno="'.$_POST['edit_pass'].'"';
		$invoice_result = mysqli_fetch_array(execute_query($sql));
		$_POST['invoice_no'] = $invoice_result['invoice_no'];
		$inv = $_POST['edit_pass'];
		$invid = $inv;

		$sql= 'UPDATE `invoice_sale_restaurant_2` SET
		  `invoice_type` = "'.$_POST['invoice_type'].'",
		  `concerned_person`="'.$_POST['customer_name'].'",
		  `total_amount` ="'.$_POST['total_amount_hidden'].'",
		  `taxable_amount` = "'.$_POST['tot_taxable_hidden'].'",
		  `user_id` ="'.$_SESSION['username'].'",
		  `supplier_id` ="'.$supplier.'",
		  `quantity` ="'.$_POST['tot_qty_hidden'].'",
		  `tot_vat` ="'.$_POST['total_cgst_hidden'].'",
		  `tot_sat` ="'.$_POST['total_sgst_hidden'].'", 
		  `round_off` ="'.$_POST['round_off_hidden'].'", 
		  `tot_disc` ="'.$_POST['tot_discount_hidden'].'", 
		  `other_discount` ="'.$_POST['other_discount'].'", 
		  `grand_total` ="'.$_POST['grand_total_hidden'].'", 
		  `storeid` ="'.$_POST['storeid'].'", 
		  `created_by` ="'.$_SESSION['username'].'",  
		  `mode_of_payment` ="'.$mop.'",
		  `waitor_name`="'.$_POST['waitor_name'].'"

		WHERE `invoice_no`="'.$_POST['invoice_no'].'"';
		//echo $sql;
		execute_query($sql);
	
	}
	else{
		if(isset($_POST['tableprint'])){
			$q="UPDATE `general_settings` SET `rate`='yes' WHERE sno='23'";
			execute_query($q);
			// echo $sql;

		}
		else{
			$q="UPDATE `general_settings` SET `rate`='no' WHERE sno='23'";
			execute_query($q);
		
		}
		if($_GET['table_id']){
		$sql = 'select * from invoice_sale_restaurant where financial_year="'.$year.'" and storeid NOT LIKE "room_%" and invoice_type="'.$_POST['invoice_type'].'" order by abs(substr(invoice_no,2)) desc limit 1';
		//echo $sql;
		$invoice_result = execute_query($sql);

		if(mysqli_num_rows($invoice_result)!=0){
			$invoice_no = mysqli_fetch_array($invoice_result);
			$_POST['invoice_no'] = str_replace("T","",$invoice_no['invoice_no']);
			$_POST['invoice_no'] = $_POST['invoice_no']+1;
			$_POST['invoice_no'] = 'T'.$_POST['invoice_no'];
		}
		else{
			$_POST['invoice_no'] = "T1";
		}
		}
		if(isset($_GET['room_id'])){
			$sql = 'select * from invoice_sale_restaurant where  financial_year="'.$year.'" and storeid LIKE "room_%" and invoice_type="'.$_POST['invoice_type'].'" order by abs(substr(invoice_no,2)) desc limit 1';
				//echo $sql;
			$invoice_result = execute_query($sql);

			if(mysqli_num_rows($invoice_result)!=0){
				$invoice_no = mysqli_fetch_array($invoice_result);
				$_POST['invoice_no'] = str_replace("R","",$invoice_no['invoice_no']);
				$_POST['invoice_no'] = $_POST['invoice_no']+1;
				$_POST['invoice_no'] = 'R'.$_POST['invoice_no'];
			}
			else{
				$_POST['invoice_no'] =  "R1";
			}
		}
		
		$sql = 'INSERT INTO `invoice_sale_restaurant` (`invoice_type`,`concerned_person` , `invoice_no`, `total_amount`, `total_amount1`, `taxable_amount`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `tot_disc`, `other_discount`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`, `mop_ref_no`, `mop_details`, `table_no`,`waitor_name`,`kot_no`, `service_charge_rate`, `service_charge_amount`, `service_charge_tax_rate`, `service_charge_tax_amount`, `service_charge_total`) 
		VALUES ("'.$_POST['invoice_type'].'","'.$_POST['customer_name'].'", "'.$_POST['invoice_no'].'", "'.$_POST['total_amount_hidden'].'", "'.$_POST['total_amount1'].'", "'.round($_POST['tot_taxable_hidden'] , 3).'", "'.$_POST['sale_date'].'", "'.$_SESSION['username'].'", "'.$_POST['sale_date'].'", "'.$supplier.'", "", "'.$_POST['tot_qty_hidden'].'", "'.$_POST['total_cgst_hidden'].'", "'.$_POST['total_sgst_hidden'].'",  "'.round($_POST['round_off_hidden'] , 3).'", "'.$_POST['tot_discount_hidden'].'", "'.$_POST['other_discount'].'", "'.$_POST['grand_total_hidden'].'", "'.$_POST['storeid'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'", "'.$year.'", "'.$mop.'", "'.$_POST['mop_ref_no'].'", "'.$_POST['mop_details'].'",  "'.$t_no.'","'.$_POST['waitor_name'].'","'.$_POST['kotno'].'", "'.$_POST['service_charge_rate'].'", "'.$_POST['service_charge_rate_amount'].'", "'.$_POST['service_charge_tax_rate'].'", "'.$_POST['service_charge_tax_amount'].'", "'.((float)$_POST['service_charge_rate_amount']+(float)$_POST['service_charge_tax_amount']).'")';
		execute_query($sql);
		$invid=mysqli_insert_id($db);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 1  : '.mysqli_error($db).' >> '.$sql.'</li>';
			$inv=0;
		}
		else{
			$inv = insert_id($db);
		}

		$sql = 'INSERT INTO `invoice_sale_restaurant_2` (`invoice_type`,`concerned_person` , `invoice_no`, `total_amount`, `taxable_amount`, `total_amount1`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `tot_disc`, `other_discount`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`,`table_no`,`waitor_name`,`kot_no`,`invoice_sale_id`) 
		VALUES ("'.$_POST['invoice_type'].'", "'.$_POST['customer_name'].'" , "'.$_POST['invoice_no'].'", "'.$_POST['total_amount_hidden'].'", "'.$_POST['total_amount1'].'", "'.round($_POST['tot_taxable_hidden'] , 3).'", "'.$_POST['sale_date'].'", "'.$_SESSION['username'].'", "'.$_POST['sale_date'].'", "'.$supplier.'", "", "'.$_POST['tot_qty_hidden'].'", "'.$_POST['total_cgst_hidden'].'", "'.$_POST['total_sgst_hidden'].'",  "'.round($_POST['round_off_hidden'] , 3).'", "'.$_POST['tot_discount_hidden'].'", "'.$_POST['other_discount'].'", "'.$_POST['grand_total_hidden'].'", "'.$_POST['storeid'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'", "'.$year.'", "'.$mop.'","'.$t_no.'","'.$_POST['waitor_name'].'","'.$_POST['kotno'].'","'.$invid.'")';
		execute_query($sql);
			
	}
	if($_POST['edit_pass'] != '') {
		$sql='DELETE FROM `stock_sale_restaurant` WHERE `invoice_no`="'.$_POST['edit_pass'].'"';
		execute_query($sql);

		$sql='DELETE FROM `stock_sale_restaurant_2` WHERE `invoice_no`="'.$_POST['edit_pass'].'"';
		execute_query($sql);
	}
	$sql="SELECT * FROM `kitchen_ticket_temp` WHERE table_id='$tableid'  and (invoice_no is null or invoice_no='')";
	$sql="SELECT `description`, `vat`, `excise`, `kitchen_ticket_temp`.`unit` as qty,`kitchen_ticket_temp`.`kot_no` as kotno, item_price, item_id, time_stamp FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid'  and (invoice_no is null or invoice_no='') and cancel_timestamp is null";
	//echo $sql;
	$res=execute_query($sql);
	$i=1;
	$discount = $_POST['discount'].'%';

	while($row=mysqli_fetch_array($res)){
		$unit = 26;
		$tot_tax_rate = $row['vat']+$row['excise'];
		//$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
		$base_price=(float)$row['item_price'];
		
		if(preg_match("/%/", $discount)){
			//echo 'test<br>';
			$disc_temp = (float)str_replace("%", "", $discount);
			$discount_value = (($base_price*$disc_temp)/100);
			$base_price = $base_price-$discount_value;
		}
		else{
			$discount='';
			$discount_value='';
		}
		$unitprice = $base_price;
		
		$taxable_amount = $base_price*$row['qty'];
		$vat = $row['vat'];
		$vat_value = round((($taxable_amount*$row['vat'])/100),2);
		$sat = $row['excise'];
		$sat_value = round((($taxable_amount*$row['excise'])/100),2);
		$qty = $row['qty'];
		$eprice = $base_price+($vat_value/$row['qty'])+($sat_value/$row['qty']);
		$total = $eprice*$qty;
		$product_id = $row['item_id'];
		$kotno=$row['kotno'];
		if($_POST['invoice_type'] == 'non_chargable'){
			$unitprice = 0;
			$discount = 0;
			$discount_value = 0;
			$vat = 0;
			$vat_value = 0;
			$sat = 0;
			$sat_value = 0;
			$taxable_amount = 0;
			$base_price = 0;
			$total = 0;
			
		}
		
		$sql = "INSERT INTO `stock_sale_restaurant` (`invoice_no`, `supplier_id`, `part_id`, `basicprice`, `discount`, `discount_value`, `vat`, `vat_value`, `excise`, `excise_value`, `taxable_amount`, `effective_price`, `qty`, `part_dateofpurchase`, `amount`, `unit`, `admin_remarks`,`kot_no`,table_id, `mrp`) 
		VALUES ('".$invid."', '".$supplier."', '".$product_id."', '".$unitprice."', '".$discount."', '".$discount_value."', '".$vat."', '".$vat_value."', '".$sat."', '".$sat_value."', '".$taxable_amount."', '".$eprice."', '".$qty."', '".$_POST['sale_date']."', '".$total."', '".$unit."', '".$row['time_stamp']."', '".$kotno."','".$_POST['storeid']."', '".$row['item_price']."')";
		//echo $sql.'<br>';
		execute_query($sql);

		$sql = "INSERT INTO `stock_sale_restaurant_2` (`invoice_no`, `supplier_id`, `part_id`, `basicprice`, `discount`, `discount_value`, `vat`, `vat_value`, `excise`, `excise_value`, `taxable_amount`, `effective_price`, `qty`, `part_dateofpurchase`, `amount`, `unit`, `admin_remarks`,`kot_no`,table_id) 
		VALUES ('".$inv."', '".$supplier."', '".$product_id."', '".$unitprice."', '".$discount."', '".$discount_value."', '".$vat."', '".$vat_value."', '".$sat."', '".$sat_value."', '".$taxable_amount."', '".$base_price."', '".$qty."', '".$_POST['sale_date']."', '".$total."', '".$unit."', '".$row['time_stamp']."', '".$kotno."','".$_POST['storeid']."')";
		//echo $sql.'<br>';
		execute_query($sql);

		if(mysqli_error($db)){
			$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
		}
	}
	if(isset($_POST['check'])){
		$allotid=$_POST['check'];
	}
	else{
		$allotid='';
	}
	
	if($_POST['edit_pass'] != '') {
		$sql='DELETE FROM `customer_transactions` WHERE `number`="'.$_POST['edit_pass'].'"';
		execute_query($sql);
		$sql='DELETE FROM `customer_transactions_2` WHERE `number`="'.$_POST['edit_pass'].'"';
		execute_query($sql);
	}
	$sql = "INSERT INTO `customer_transactions` (`cust_id`,`allotment_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`,mop,payment_for) 
	VALUES ('".$supplier."','".$_POST['room_allot_id']."','sale_restaurant', '".$invid."', '".$_POST['grand_total_hidden']."', '".$_POST['sale_date']."', '$mop', 0, '".$year."','".$_POST['invoice_no']."','".$mop."','res')";
	//echo $sql;
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 3 : '.mysqli_error($db).' >> '.$sql.'</li>';
	}
	$sql = "INSERT INTO `customer_transactions_2` (`cust_id`,`allotment_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`,mop,payment_for) 
	VALUES ('".$supplier."','".$_POST['room_allot_id']."','sale_restaurant', '".$inv."', '".$_POST['grand_total_hidden']."', '".$_POST['sale_date']."', '$mop', 0, '".$year."','".$_POST['invoice_no']."','".$mop."','res')";
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 3 : '.mysqli_error($db).' >> '.$sql.'</li>';
	}
	if($msg==''){
		$sql="update `kitchen_ticket_temp` set invoice_no='$invid' WHERE table_id='$tableid' and (invoice_no is null or invoice_no='') and cancel_timestamp is null";
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
		if(isset($_POST['non_chargeable'])){
			header("location:dine_in_order.php");
			//echo'script>window.location.href="dine_in_order.php";</script>';
		}
		else{
			if($_POST['customer_mobile']!=''){
				//$whatsapp_msg .= 'Dear Gaurav Saxena Thanks for dining @KP Town! Your Invoice value is Rs'.$_POST['grand_total_hidden'];
				ob_start();  // start output buffering
				include 'http://localhost/kp_town/software/scripts/printing_sale_restaurant_pdf.php?inv='.$invid;
				$content = ob_get_clean(); // get content of the buffer and clean the buffer
				
				// create new PDF document
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('WeKnow Technologies');
				$pdf->SetTitle('Invoice Tipsy Town');
				$pdf->SetSubject('Invoice Tipsy Town');

				// set margins
				$pdf->SetMargins(10, 0, 10);

				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

				// set image scale factor
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

				// set some language-dependent strings (optional)
				if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
					require_once(dirname(__FILE__).'/lang/eng.php');
					$pdf->setLanguageArray($l);
				}

				// ---------------------------------------------------------

				// add a page
				$pdf->AddPage();

				// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
				// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)


				// output the HTML content
				$pdf->writeHTML($content, true, true, true, false, '');

				// ---------------------------------------------------------
				
				//Close and output PDF document
				//$pdf->Output($invid.'.pdf', 'F');
				//$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/kp_town/software/tmp_files/' . $invid.'.pdf', 'F');

				//============================================================+
				// END OF FILE
				//============================================================+

				
				

				$url = 'http://sms.weknowtech.in/pushwhatsapp.php?username=kptown&api_password=3896e0zdy7lpgbp25&sender=917393957373&priority=21&name=kp1&to=91'.$_POST['customer_mobile'].'&jsonapi=1';
				$url = 'http://sms.weknowtech.in/pushwhatsapp.php?username=kptown&api_password=3896e0zdy7lpgbp25&sender=917393957373&priority=21&name=welcome031223&to=91'.$_POST['customer_mobile'].'&jsonapi=1';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,  $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$buffer1 = curl_exec($ch);
				$sql = 'insert into sms_buffer_dump (request_url, raw_buffer_dump, buffer_dump, mobile, creation_time) values("'.$url.'", "'.htmlentities($buffer1).'", "'.json_decode($buffer1, true).'", "'.$_POST['customer_mobile'].'", "'.date("Y-m-d H:i:s").'")';
				//echo $sql;
				execute_query($sql);

				if(empty($buffer1)){
					echo $buffer1;
				}
				else{
					$buffer1 = json_decode($buffer1, true);
				}	

				$url = 'http://sms.weknowtech.in/pushwhatsapp.php?username=kptown&api_password=3896e0zdy7lpgbp25&sender=917393957373&priority=21&name=invoicercpt&doc=http://localhost/kp_town/software/tmp_files/'.$invid.'.pdf&to=91'.$_POST['customer_mobile'].'&value1=758&value2=25-11-2023&jsonapi=1';
				$url = 'http://sms.weknowtech.in/pushwhatsapp.php?username=kptown&api_password=3896e0zdy7lpgbp25&sender=917393957373&priority=21&name=invoice031223&to=91'.$_POST['customer_mobile'].'&value1='.$_POST['grand_total_hidden'].'&value2='.$_POST['sale_date'].'&jsonapi=1';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,  $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$buffer1 = curl_exec($ch);
				$sql = 'insert into sms_buffer_dump (request_url, raw_buffer_dump, buffer_dump, mobile, creation_time) values("'.$url.'", "'.htmlentities($buffer1).'", "'.json_decode($buffer1, true).'", "'.$_POST['customer_mobile'].'", "'.date("Y-m-d H:i:s").'")';
				//echo $sql;
				execute_query($sql);

				if(empty($buffer1)){
					echo $buffer1;
				}
				else{
					$buffer1 = json_decode($buffer1, true);
				}	
				/*$url = 'http://sms.weknowtech.in/pushwhatsapp.php?username=9554969777&api_password=45c48t4yaqqa3uf2p&sender=919198749777&priority=21&name=kptown&to='.$_POST['customer_mobile'].'&value1='.$_POST['customer_name'].'&value2='.$_POST['grand_total_hidden'].'&jsonapi=1';
				//echo $url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,  $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$buffer1 = curl_exec($ch);
				if(empty($buffer1)){
					echo $buffer1;
				}
				else{
					$buffer1 = json_decode($buffer1, true);
					//print_r($buffer1);
				}	*/
			}
			
			$msg .= '<script>window.open("scripts/printing_sale_restaurant.php?inv='.$invid.'");</script>';
		}
		
	}
}
?>
<style>
	#bill{
		width:55%;
		height: 580px;
		float: left;
		border:1px solid;
		overflow-y: scroll;
		float:left;
	}
	#bill td{
		font-size: 11px;
	}
	#bill th{
		font-size: 12px;
	}
	#div_total{
		width:43%;
		height: 220px;
		float: left;
		margin-left: 10px;
		border:1px solid;

	}
	#div_total td{
		font-size:12px;
	}
	#div_button{
		width:43%;
		height: 300px;
		float: left;
		margin-left: 10px;
		border: 1px solid;

	}
	#div_button button{
		border: 1px solid #ccc;
		border-radius: 5px;
		box-shadow: 1px 1px;
		outline: none;
		padding: 12px 16px;
		color: black;
		height:45px;
		width:130px;
		cursor: pointer;
		margin: 5px;
		padding:5px;
		margin-left:10px;
	}
	
	td.right, th.right{
		padding-right: 20px;
	}
</style>
<?php
switch($response){
	case 1 :{
?>
<script>
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $('#customer_name').val(ui.item.cust_name);
			$('#customer_sno').val(ui.item.id);
			$('#customer_mobile').val(ui.item.mobile);
			$('#customer_address').val(ui.item.opening);
			$("#customer_gstin").val(ui.item.gstin);
			$("#c").val(ui.item.allotment_id);
			var txt='<td>Select Rooms</td><td><ul>';
			var i=0;
			//console.log(ui.item.rooms);
			$.each( ui.item.rooms, function( index, value ) {
				txt=txt+'<input type="radio"  value="'+value.allotment_id+'" id="check_'+value.allotment_id+'" name="check" onclick="update(this.value)">'+value.label+'&nbsp;';
				i++;
			});
			$('#insert_room').html(txt);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#customer_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});

$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name1",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company']").val(ui.item.label);
			$('#customer_sno').val(ui.item.id);
			$('#customer_name').val(ui.item.cust_name);
			$('#customer_mobile').val(ui.item.mobile);
			$('#company_name').val(ui.item.company);
			$('#customer_address').val(ui.item.address);
			$('#customer_gstin').val(ui.item.gst_no);
			$("#c").val(ui.item.allotment_id);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#customer_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});

$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=company_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company']").val(ui.item.label);
			$('#customer_sno').val(ui.item.id);
			//$('#customer_name').val(ui.item.cust_name);
			$('#customer_mobile').val(ui.item.mobile);
			$('#company_name').val(ui.item.company);
			$('#customer_address').val(ui.item.address);
			$('#customer_gstin').val(ui.item.gst_no);
			$("#c").val(ui.item.allotment_id);

			$("#ajax_loader").show();
			return false;
		}
	};
$("input#company_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});


$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=waitor",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
			$("#waitor_name").val(ui.item.label);
			$("#waitor_id").val(ui.item.id);
			return false;
		}
	};
$("input#waitor_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});

function calculate(){
	var total_amt = parseFloat($("#total_amount_hidden").val());
	var tot_qty = 0;
	var tot_discount = 0;
	var tot_taxable = 0;
	var tot_cgst = 0;
	var tot_sgst = 0;
	var tot_total = 0;
	if(!total_amt){
		total_amt=0;
	}
	var total_disc = $("#discount").val();
	if(total_disc.search('%')==-1){
		total_disc = parseFloat(total_disc);
		var disc_symbol=0;
	}
	else{
		total_disc = total_disc.replace('%','');
		total_disc = parseFloat(total_disc);
		var disc_symbol=1;
	}
	if(!total_disc){
		total_disc=0;
	}
	disc_symbol=1;
	//console.log(total_amt+' >> '+total_disc);
	if(disc_symbol==1 && total_disc!=0){
		document.getElementById("discount").readOnly = true;
		$("#show_refresh").show();
		var tot_count = parseFloat($("#total_count").val());
		for(i=1; i<=tot_count; i++){
			var base_price = parseFloat($("#base_price_"+i).html());
			var qty = parseFloat($("#qty_"+i).html());
			var total_taxable = base_price*qty;
			total_disc_value = (total_taxable*total_disc/100);
			
			var total_taxable = total_taxable - total_disc_value;
			
			var cgst_rate = parseFloat($("#cgst_rate_"+i).html());
			if(!cgst_rate){
				cgst_rate=0;
			}
			var sgst_rate = parseFloat($("#sgst_rate_"+i).html());
			if(!sgst_rate){
				sgst_rate=0;
			}
			
			var sgst_value = (total_taxable*sgst_rate)/100;
			var cgst_value = (total_taxable*cgst_rate)/100;
			var eprice = (total_taxable+cgst_value+sgst_value)/qty;
			var row_total = eprice*qty;
			
			tot_qty += qty;
			tot_discount += total_disc_value;
			tot_taxable += total_taxable;
			tot_cgst += cgst_value;
			tot_sgst += sgst_value;
			tot_total += row_total;
			
			
			$("#disc_"+i).html(Math.round(total_disc_value*100)/100);
			$("#taxable_"+i).html(Math.round(total_taxable*100)/100);
			$("#cgst_amt_"+i).html(Math.round(cgst_value*100)/100);
			$("#sgst_amt_"+i).html(Math.round(sgst_value*100)/100);
			$("#price_"+i).html(Math.round(eprice*100)/100);
			$("#total_"+i).html(Math.round(row_total*100)/100);

		}
		
		$("#tot_qty").html(Math.round(tot_qty*100)/100);
		$("#tot_discount").html(Math.round(tot_discount*100)/100);
		$("#tot_taxable").html(Math.round(tot_taxable*100)/100);
		$("#total_taxable").html(Math.round(tot_taxable*100)/100);
		$("#sub_total").html(Math.round(tot_taxable*100)/100);
		$("#tot_cgst").html(Math.round(tot_cgst*100)/100);
		$("#tot_sgst").html(Math.round(tot_sgst*100)/100);
		$("#total_tax").html((Math.round(tot_cgst+tot_sgst)*100)/100);
		$("#tot_total").html(Math.round(tot_total*100)/100);
		$("#total_amount").html(Math.round(tot_total*100)/100);
		
		var round_off = tot_total;
		var dummy_grand_total = tot_total;
		round_off = round_off.toFixed(0) - dummy_grand_total;
		round_off = Math.round(round_off*100)/100;
		$("#round_off").html(round_off);
		grand_total = tot_total+round_off;
		$("#grand_total").html(Math.round(grand_total*100)/100);
		
		$("#total_amount_hidden").val(Math.round(tot_total*100)/100);
		$("#grand_total_hidden").val(Math.round(grand_total*100)/100);
		$("#round_off_hidden").val(round_off);
		$("#tot_qty_hidden").val(Math.round(tot_qty*100)/100);
		$("#tot_discount_hidden").val(Math.round(tot_discount*100)/100);
		$("#tot_taxable_hidden").val(Math.round(tot_taxable*100)/100);
		$("#total_cgst_hidden").val(Math.round(tot_cgst*100)/100);
		$("#total_sgst_hidden").val(Math.round(tot_sgst*100)/100);
		$("#other_discount").val(total_disc+'%');
		$("#new_row").remove();
		

	}
	else{
		var grand_total = $("#grand_total").html();
		grand_total = grand_total.replace("₹&nbsp;", "");
		grand_total = grand_total.replace("Rs&nbsp;", "");
		grand_total = grand_total.replace(",", "");
		console.log(grand_total+' >> '+total_disc);
		var total_taxable = Math.round((grand_total-total_disc)*100)/100;
		$("#other_discount").val(total_disc);
		$("#tot_discount_hidden").val(total_disc);
		$("#grand_total_hidden").val(total_taxable);
		$("#new_row").remove();
		var row = '<tr id="new_row"><td class="right" colspan="2">Net Amount Payable :</td><td class="right">₹ '+total_taxable+'</tr>';
		$("#div_total table").append(row);
	}
	var service_charge = parseFloat($("#service_charge_rate").val());
	if(service_charge){
		if(service_charge>0){
			var taxable = parseFloat($("#tot_taxable_hidden").val());
			if(!taxable){
				taxable=0;
			}
			//alert('tot_taxable_hidden:'+$("#tot_taxable_hidden").val()+'total_amt:'+total_amt+' total_taxable:'+total_taxable+' grand_total:'+grand_total);
			service_charge_value = (taxable*service_charge)/100;
			service_charge_value = service_charge_value;
			$("#service_charge_rate_amount").val(Math.round(service_charge_value*100)/100);
			total_taxable+=service_charge_value;
			service_charge_tax = (service_charge_value*5)/100;
			grand_total=parseFloat(grand_total);
			//console.log(grand_total);
			grand_total += service_charge_tax;
			//console.log(grand_total);
			grand_total+=service_charge_value;
			
			var round_off = grand_total;
			var dummy_grand_total = grand_total;
			round_off = round_off.toFixed(0) - dummy_grand_total;
			console.log(round_off+'>>'+dummy_grand_total);
			round_off = Math.round(round_off*100)/100;
			console.log(round_off+'>>'+dummy_grand_total);
			$("#round_off").html(round_off);
			grand_total = grand_total+round_off;
			grand_total = grand_total.toFixed(0);

			
			//console.log(grand_total);
			//alert('2tot_taxable_hidden:'+$("#tot_taxable_hidden").val()+'total_amt:'+total_amt+' total_taxable:'+total_taxable+' grand_total:'+grand_total);
			$("#service_charge_tax_amount").val(Math.round(service_charge_tax*100)/100);
			$("#service_charge_tax_rate").val('5');
			$("#service_charge_tax").html(Math.round(service_charge_tax*100)/100);
			$("#service_charge_amt").html(Math.round((service_charge_tax+service_charge_value)*100)/100);
			$("#grand_total_hidden").val(Math.round(grand_total*100)/100);
			$("#grand_total").val(Math.round(grand_total*100)/100);
			$("#new_row").remove();
			var row = '<tr id="new_row"><td class="right" colspan="3">Net Amount Payable :</td><td class="right">₹ '+(Math.round(grand_total*100)/100)+'</tr>';
			$("#div_total table").append(row);
		}
		//alert(service_charge+' >> '+service_charge_value);
	}
	
}
</script>
		
<div id="container">
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<?php
	if(strpos($tableid, "room")===false){
		$param = 'table_id='.$tableid;
		$param .= '&type=table';
	}
	else{
		$param = 'room_id='.$tableid;
		$param .= '&type=room';
	}
	?>
	<form action="bill_invoice.php?<?php echo $param;?>" id="purchase_report" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onsubmit="return confirm('Are you sure ?');">
		<div id="bill">
			<table style="background-color:#DDDDDD;border:1px solid black;">
				<tr>
					<th rowspan="2">Sno</th>
					<th rowspan="2">Item</th>
					<th rowspan="2">Unit</th>
					<th rowspan="2">Base Price</th>
					<th rowspan="2">Discount</th>
					<th rowspan="2">Taxable Amount</th>
					<th colspan="2">CGST</th>
					<th colspan="2">SGST</th>
					<th rowspan="2">Price</th>
					<th rowspan="2">Total</th>
				</tr>
				<tr>
					<th>Rate</th>
					<th>Amount</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
				<?php
				$subtotal=0;
				if(isset($_GET['table_id'])){
					$sql="SELECT `description`, `vat`, `excise`, sum(`kitchen_ticket_temp`.`unit`) as qty, `kitchen_ticket_temp`.`kot_no` as kotno ,item_price FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid' and (invoice_no is null or invoice_no='') and cancel_timestamp is null group by  `kitchen_ticket_temp`.item_id";
					//echo $sql;
					$res=execute_query($sql);
					$tot_taxable_hidden=0;
					$total_cgst_hidden=0;
					$total_sgst_hidden=0;
					$tot_tax=0;
					$taxable=0;
					$tot_qty_hidden=0;
					$total_amount_hidden=0;
					if (mysqli_num_rows($res) == 0) {
						echo '<script>window.open("index.php" , "_self")</script>';
					}
					while($row=mysqli_fetch_array($res)){
						$tot_tax_rate = $row['vat']+$row['excise'];
						//$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
						$base_price=$row['item_price'];
						$taxable = $base_price*$row['qty'];
						$cgst_amt = ($taxable*$row['vat'])/100;
						$sgst_amt = ($taxable*$row['excise'])/100;
						$e_price = $row['item_price'] + (($row['item_price']*$row['vat'])/100) + (($row['item_price']*$row['vat'])/100);
						$total=$e_price*$row['qty'];
						
						$tot_taxable_hidden+=$taxable;
						$total_cgst_hidden+=$cgst_amt;
						$total_sgst_hidden+=$sgst_amt;
						$total_amount_hidden+=$total;
						$tot_qty_hidden += $row['qty'];
						$kotno=$row['kotno'];
						echo'<tr>
							<td>'.$sno.'</td>
							<td>'.$row['description'].'</td>
							<td id="qty_'.$sno.'">'.$row['qty'].'</td>
							<td id="base_price_'.$sno.'">'.$base_price.'</td>
							<td id="disc_'.$sno.'"></td>
							<td id="taxable_'.$sno.'">'.$taxable.'</td>
							<td id="cgst_rate_'.$sno.'">'.$row['vat'].'</td>
							<td id="cgst_amt_'.$sno.'">'.round($cgst_amt,2).'</td>
							<td id="sgst_rate_'.$sno.'">'.$row['excise'].'</td>
							<td id="sgst_amt_'.$sno.'">'.round($sgst_amt,2).'</td>
							<td id="price_'.$sno.'">'.round($e_price,2).'</td>
							<td id="total_'.$sno.'">'.round($total,2).'</td>
						</tr>';
						$sno++;
					}
					echo '<tr>
					<th>&nbsp;</th>
					<th>Total : </th>
					<th id="tot_qty">'.$tot_qty_hidden.'</th>
					<th>&nbsp;</th>
					<th id="tot_discount">&nbsp;</th>
					<th id="tot_taxable">'.$tot_taxable_hidden.'</th>
					<th>&nbsp;</th>
					<th id="tot_cgst">'.$total_cgst_hidden.'</th>
					<th>&nbsp;</th>
					<th id="tot_sgst">'.$total_sgst_hidden.'</th>
					<th>&nbsp;</th>
					<th id="tot_total">'.$total_amount_hidden.'</th>
					</tr>';
					
					$round_off = $total_amount_hidden;
					$dummy_grand_total = round($total_amount_hidden, 2);
					$round_off = round(round($total_amount_hidden) - $dummy_grand_total,2);
					$grand_total = $total_amount_hidden+$round_off;
					
					echo '
					<input type="hidden" name="total_amount_hidden" id="total_amount_hidden" value="'.$total_amount_hidden.'">
					<input type="hidden" name="tot_discount_hidden" id="tot_discount_hidden" value="">
					<input type="hidden" name="tot_taxable_hidden" id="tot_taxable_hidden" value="'.$tot_taxable_hidden.'">
					<input type="hidden" name="total_amount1" id="total_amount1" value="'.$tot_taxable_hidden.'">
					<input type="hidden" name="tot_qty_hidden" id="tot_qty_hidden" value="'.$tot_qty_hidden.'">
					<input type="hidden" name="total_cgst_hidden" id="total_cgst_hidden" value="'.$total_cgst_hidden.'">
					<input type="hidden" name="total_sgst_hidden" id="total_sgst_hidden" value="'.$total_sgst_hidden.'">
					<input type="hidden" name="round_off_hidden" id="round_off_hidden" value="'.$round_off.'">
					<input type="hidden" name="total_count" id="total_count" value="'.--$sno.'">
					<input type="hidden" name="other_discount" id="other_discount" value="">
					<input type="hidden" name="grand_total_hidden" id="grand_total_hidden" value="'.$grand_total.'">
					<input type="hidden" name="kotno" id="kotno" value="'.$kotno.'">';					
				}
				?>
			</table>
		</div>
		<div id="div_total">
			<table width="100%">
				<tr>
					<td colspan="4"><a href="scripts/printing_sale_restaurant_temp.php?id=<?php echo $tableid; ?>" target="_blank">Preview Invoice</a></td>
				</tr>
				<tr>
					<td class="right">Discount :</td>
					<td class="right"><input type="text" name="discount" id="discount" class="small" style="width:50px;" value="<?php if(isset($_GET['edit_id'])) { echo $row_discount_waitor['other_discount']; }  ?>" onBlur="calculate();"><a id="show_refresh" href="bill_invoice.php?table_id=<?php echo $_GET['table_id'];?>" style="display: none;">Refresh</a></td>
					<td class="right">Service Charge:</td>
					<td class="right" id="service_charge_row"><input type="text" name="service_charge_rate" id="service_charge_rate" placeholder="Rate" class="small" value="<?php if(isset($_GET['edit_id'])) { echo $row_discount_waitor['service_charge_rate']; }  ?>" style="float:left; width: 50px;" onBlur="calculate();"></td>
				</tr>
				<tr>
					<td class="right">Total Taxable : <span class="right" id="total_taxable"><?php echo $tot_taxable_hidden;?></span></td>
					<td class="right">Tax :<span class="right" id="total_tax"><?php echo $total_cgst_hidden+$total_sgst_hidden;  ?></span></td>
					<td class="right">Total :</td>
					<td class="right" id="total_amount"><?php echo $total_amount_hidden; ?></td>
				</tr>
				<tr>
					<td class="right">Round Off :</td>
					<td class="right" id="round_off"><?php echo round($round_off,2); ?></td>
					<td class="right">Grand Total :</td>
					<td class="right" id="grand_total"><?php echo $grand_total; ?></td>
				</tr>
				<tr>
					<td>Service Charge Amount:</td>
					<td><input type="text" name="service_charge_rate_amount" id="service_charge_rate_amount" placeholder="Amount" class="small" value="" style="float:right; width:50px;" readonly></td>
					<td class="right">Tax : <input type="hidden" name="service_charge_tax_rate" id="service_charge_tax_rate"><input type="hidden" name="service_charge_tax_amount" id="service_charge_tax_amount"><span id="service_charge_tax"></span></td>
					<td class="right">Total Service Charge : <span id="service_charge_amt"></span></td>
				</tr>
				
				
			</table>
		</div>
		<div id="div_button">
			<?php 
				//echo '<script>alert('.$room_allot_id.');</script>';
			?>
			<table>
				<tr>
					<td width="50%">Sale Date :</td>
					<td>
						<script type="text/javascript" language="javascript">
						document.writeln(DateInput('sale_date', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['sale_date_from'])){echo $_SESSION['sale_date_from'];}else{echo date("Y-m-d");}?>', 1));
						</script>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" name="company_name" id="company_name" placeholder="Company Name" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['company_name']; }elseif($fill == "1"){echo $row_cust_room['company_name'];}  ?>">
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><input type="text" name="customer_name" id="customer_name" placeholder="Guest Name" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['cust_name']; }elseif($fill == "1"){echo $row_cust_room['cust_name'];}  ?>"><input type="hidden" name="customer_sno" id="customer_sno" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['sno']; }elseif($fill == "1"){echo $row_cust_room['sno'];}  ?>"></td>
					<input type="hidden" name="room_allot_id" id="room_allot_id" value="<?php if($fill == "1"){echo $row_inv['sno'];} ?>">
					<input type="hidden" name="c" id="c" value="<?php  if(isset($_GET['edit_id'])) { echo $row_customer1['allotment_id']; }  ?>">
					<td><input type="text" name="customer_mobile" id="customer_mobile" placeholder="Mobile Number" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['mobile']; }elseif($fill == "1"){echo $row_cust_room['mobile'];}  ?>"></td>
				</tr>
				<tr>
					<td><input type="text" name="customer_address" id="customer_address" placeholder="Address" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['address']; }elseif($fill == "1"){echo $row_cust_room['address'];}  ?>"></td>
					<td><input type="text" name="customer_gstin" id="customer_gstin" placeholder="GSTIN" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['id_2']; }elseif($fill == "1"){echo $row_cust_room['id_2'];}  ?>"></td>
				</tr>
				<tr>
					<td> <input type="text" name="waitor_name" id="waitor_name" placeholder="Waitor Name" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_discount_waitor['waitor_name']; } ?>"><input type="hidden" name="waitor_id" id="waitor_id"></td>
					<td><input type="checkbox" name="tableprint" value="" checked>Print Table </td>
				</tr>
			</table>
			
			<?php
			if($_GET['type'] == "room"){
			?>
				
				
				<?php if(isset($_GET['kot'])){
					echo '<button name="non_chargeable" type="submit" id="non_chargeable" >Non Chargeable</button>';
				}
			} 
			elseif($_GET['type'] == "table"){
			?>
				
				
				<?php if(isset($_GET['kot'])){
					echo '<button name="non_chargeable" type="submit" id="non_chargeable" >Non Chargeable</button>';
				}
			}
			else{
				/*
			?>
				<button name="cash" type="submit" id="cash" >Cash</button>
				<button name="credit" type="submit" id="credit" >Credit</button>
				<button name="card" type="submit" id="card">Card</button>
				<?php if(isset($_GET['kot'])){
					echo'<button name="non_chargeable" type="submit" id="non_chargeable" >Non Chargeable</button>';
				}
			*/} ?>
			<table>
				<tr>
					<td>
						<select name="mode_of_payment" id="mode_of_payment" class="small">
							<?php
			 				$sql = 'select * from mode_of_payments';
			 				$result_mop = execute_query($sql);
			 				while($row_mop = mysqli_fetch_assoc($result_mop)){
								echo '<option value="'.$row_mop['sno'].'" ';
								if(isset($_GET['edit_id'])) {
									if($row_discount_waitor['mode_of_payment']==$row_mop['sno']){
										echo ' selected="selected" ';
									}
								}
								echo '>'.$row_mop['mode_of_payment'].'</option>';	
							}
			 
			 				?>
						</select>
					</td>
					<td>
						<input type="text" placeholder="Payment Details" name="mop_details" id="mop_details" value="<?php if(isset($_GET['edit_id'])) {echo $row_discount_waitor['mop_details'];}?>">
					</td>
					<td>
						<input type="text" placeholder="Reference No" name="mop_ref_no" id="mop_ref_no" class="small" value="<?php if(isset($_GET['edit_id'])) {echo $row_discount_waitor['mop_ref_no'];}?>">
					</td>
				</tr>
				<tr>
					<th colspan="3" align="center">
						<button type="submit" name="generate_invoice" id="generate_invoice">Generate Invoice</button>
						<button type="submit" name="non_chargable_invoice" id="non_chargable_invoice" style="background: #f00; color: #fff; ">Non Chargable Invoice</button>
					</td>
				</tr>
			</table>
			
			<div id="insert_room">

			</div>
		</div>
		<input type="hidden" name="edit_pass" id="edit_pass" value="<?php if(isset($_GET['edit_id'])) { echo $_GET['edit_id']; }  ?>" >
	</form>
</div>
<script>
$(document).ready(function() {
	calculate();
})

</script>
<?php	
		break;
	}
	case 2 :{
?>
<div id="container">
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>	
	<div id="div_button">
		<?php 
			if($_GET['type'] == "room"){
		?>
			<button name="cash" type="button" onClick="window.open('dine_in_order_room.php', '_self');">Back To Home</button>
		<?php } ?>
		<?php 
			if($_GET['type'] == "table"){
		?>
			<button name="cash" type="button" onClick="window.open('dine_in_order_table.php', '_self');">Back To Home</button>
		<?php } ?>
			<button name="card" type="button" id="card" onClick="window.open('scripts/printing_sale_restaurant.php?inv=<?php echo $inv;?>', '_blank');">Print Bill</button>
	</div>
</div>
<?php	
		break;
	}
}
?>

<?php
page_footer();
?>