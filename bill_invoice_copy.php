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
if(isset($_GET['edit_id'])) { 
$sql_customer1='select * from `customer_transactions_2` where `number`="'.$_GET['edit_id'].'"';
$result_customer1=execute_query($sql_customer1);
$row_customer1=mysqli_fetch_array($result_customer1);
$sql_customer='select * from `customer` where `sno`="'.$row_customer1['cust_id'].'"';
$result_customer=execute_query($sql_customer);
$row_customer=mysqli_fetch_array($result_customer);
$sql_discount_waitor='select * from `invoice_sale_restaurant_2` where `invoice_sale_id`="'.$_GET['edit_id'].'"';
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
	//print_r($_POST);to
	if($_POST['customer_sno']!=''){
		$supplier = $_POST['customer_sno'];
	}
	elseif($_POST['customer_name']!=''){
		$supplier = add_customer($_POST['customer_name'], $_POST['customer_address'], $_POST['customer_mobile'], $_POST['customer_gstin']);
	}
	else{
		$sql = 'select * from general_settings where `desc`="default_cash"';
		$default_cash = mysqli_fetch_assoc(execute_query($sql));
		$supplier=$default_cash['rate'];
	}
	//$_POST['sale_date'] = date("Y-m-d");
	$_POST['invoice_type'] = 'tax';
	$_POST['storeid']=$tableid;
	$date = $_POST['sale_date'];
	$time = strtotime($date);
	$month = date("m",$time);
	$year = date("Y",$time);
	if($month>=1 && $month<=3){
		$year = $year-1;
	}
	
	
	if(isset($_POST['cash'])){
		$mop = 'CASH';
	}
	elseif(isset($_POST['card'])){
		$mop = 'CARD';
	}
	elseif(isset($_POST['other'])){
		$mop = 'OTHER';
	}
	elseif(isset($_POST['credit'])){
		$mop = 'credit';
	}
	elseif(isset($_POST['non_chargeable'])){
		$mop = 'nocharge';

	}
	if($_POST['edit_pass'] != '') {
	$supplier = add_customer($_POST['customer_name'], $_POST['customer_address'], $_POST['customer_mobile'], $_POST['customer_gstin'] , $_POST['company_name']);

	$sql= 'UPDATE `invoice_sale_restaurant_2` SET
		  `invoice_type` = "'.$_POST['invoice_type'].'",
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
		   `edition_time` ="'.date("Y-m-d").'",  
		  `waitor_name`="'.$_POST['waitor_name'].'" ,
		  `concerned_person`="'.$_POST['customer_name'].'"
		WHERE `invoice_sale_id`="'.$_POST['edit_pass'].'"';
		//echo $sql;
		execute_query($sql);
		$sql = 'select * from invoice_sale_restaurant_2 where sno="'.$_POST['edit_pass'].'"';
		$invoice_result = mysqli_fetch_array(execute_query($sql));
		$_POST['invoice_no'] = $invoice_result['invoice_no'];
		$inv = $_POST['edit_pass'];
	}
	else{
		$sql = 'select * from invoice_sale_restaurant_2 where invoice_type="'.$_POST['invoice_type'].'" and financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			//echo $sql;
		$invoice_result = execute_query($sql);
		if(mysqli_num_rows($invoice_result)!=0){
			$invoice_no = mysqli_fetch_array($invoice_result);
			$_POST['invoice_no'] = $invoice_no['invoice_no']+1;
		}
		else{
			$_POST['invoice_no'] = 1;
		}
		$sql = 'INSERT INTO `invoice_sale_restaurant_2` (`invoice_type`, `invoice_no`, `total_amount`, `taxable_amount`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `tot_disc`, `other_discount`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`,`table_no`,`waitor_name`,`kot_no`) 
		VALUES ("'.$_POST['invoice_type'].'", "'.$_POST['invoice_no'].'", "'.$_POST['total_amount_hidden'].'", "'.$_POST['tot_taxable_hidden'].'", "'.$_POST['sale_date'].'", "'.$_SESSION['username'].'", "'.$_POST['sale_date'].'", "'.$supplier.'", "", "'.$_POST['tot_qty_hidden'].'", "'.$_POST['total_cgst_hidden'].'", "'.$_POST['total_sgst_hidden'].'",  "'.$_POST['round_off_hidden'].'", "'.$_POST['tot_discount_hidden'].'", "'.$_POST['other_discount'].'", "'.$_POST['grand_total_hidden'].'", "'.$_POST['storeid'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'", "'.$year.'", "'.$mop.'","'.$t_no.'","'.$_POST['waitor_name'].'","'.$_POST['kotno'].'")';
		execute_query($sql);

		
		
		if(mysqli_error($db)){
			$msg .= '<li>Error # 1  : '.mysqli_error($db).' >> '.$sql.'</li>';
			$inv=0;
		}
		else{
			$inv = insert_id($db);
		}	
	}
	if($_POST['edit_pass'] != '') {
			$sql='DELETE FROM `stock_sale_restaurant_2` WHERE `invoice_no`="'.$_POST['edit_pass'].'"';
			execute_query($sql);


		}
	$sql="SELECT * FROM `kitchen_ticket_temp_2` WHERE table_id='$tableid'";
	//echo $sql;
	$sql="SELECT `description`, `vat`, `excise`, sum(`kitchen_ticket_temp_2`.`unit`) as qty,`kitchen_ticket_temp_2`.`kot_no` as kotno, item_price, item_id, time_stamp FROM `kitchen_ticket_temp_2` left join stock_available on stock_available.sno = kitchen_ticket_temp_2.item_id WHERE table_id='$tableid' group by item_id";
	//echo $sql;
	$res=execute_query($sql);
	$i=1;
	$discount = $_POST['discount'];

	while($row=mysqli_fetch_array($res)){
		$unit = 26;
		$tot_tax_rate = $row['vat']+$row['excise'];
		//$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
		$base_price=$row['item_price'];
		$unitprice = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
		
		if(preg_match("/%/", $discount)){
			//echo 'test<br>';
			$disc_temp = str_replace("%", "", $discount);
			$discount_value = (($base_price*$disc_temp)/100);
			$base_price = $base_price-$discount_value;
		}
		else{
			$discount='';
			$discount_value='';
		}
		$taxable_amount = $base_price*$row['qty'];
		$vat = $row['vat'];
		$vat_value = ($row['qty']*round((($taxable_amount*$row['vat'])/100),2));
		$sat = $row['excise'];
		$sat_value = ($row['qty']*round((($taxable_amount*$row['excise'])/100),2));
		$qty = $row['qty'];
		$total = $base_price*$qty;
		$eprice = $row['item_price']/$row['qty'];
		$product_id = $row['item_id'];
		$kotno=$row['kotno'];
		
		

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
			$sql='DELETE FROM `customer_transactions_2` WHERE `number`="'.$_POST['edit_pass'].'"';
			execute_query($sql);
		}
	$sql = "INSERT INTO `customer_transactions_2` (`cust_id`,`allotment_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`,mop) 
	VALUES ('".$supplier."','".$allotid."','sale_restaurant', '".$inv."', '".$_POST['grand_total_hidden']."', '".$_POST['sale_date']."', '$mop', 0, '".$year."','".$_POST['invoice_no']."','".$mop."')";
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 3 : '.mysqli_error($db).' >> '.$sql.'</li>';
	}
	if($msg==''){
		$sql="DELETE FROM `kitchen_ticket_temp_2` WHERE table_id='$tableid'";
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
			$msg .= '<script>window.open("scripts/printing_sale_restaurant_copy.php?inv='.$inv.'");</script>';
		}
		
	}
}
?>
<style>
#bill{
	width:60%;
	height: 600px;
	float: left;
	border:1px solid;
	overflow-y: scroll;
	float:left;
}
#div_total{
	width:30%;
	height: 250px;
	float: left;
	margin-left: 50px;
	border::1px solid;
	
}
#div_button{
	width:38%;
	height: 350px;
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
	height:60px;
	width:130px;
	cursor: pointer;
	margin: 5px;
	padding:5px;
	margin-left:10px;
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
})


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
	console.log(total_disc);
	if(disc_symbol==1){
	}
	else{
		var total_taxable = total_amt-total_disc;
	}
	if(disc_symbol==1){
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
			
			var eprice = total_taxable;
			var sgst_value = (total_taxable*sgst_rate)/100;
			var cgst_value = (total_taxable*cgst_rate)/100;
			var row_total = Math.round((total_taxable+cgst_value+sgst_value)*100)/100;
			
			total_disc_value = Math.round(total_disc_value*100)/100;
			total_taxable = Math.round(total_taxable*100)/100
			cgst_value = Math.round(cgst_value*100)/100;
			sgst_value = Math.round(sgst_value*100)/100;
			eprice = Math.round(eprice*100)/100;
			row_total = Math.round(row_total*100)/100;
			
			tot_qty += qty;
			tot_discount += total_disc_value;
			tot_taxable += total_taxable;
			tot_cgst += cgst_value;
			tot_sgst += sgst_value;
			tot_total += row_total;
			
			
			$("#disc_"+i).html(total_disc_value);
			$("#taxable_"+i).html(total_taxable);
			$("#cgst_amt_"+i).html(cgst_value);
			$("#sgst_amt_"+i).html(sgst_value);
			$("#price_"+i).html(eprice);
			$("#total_"+i).html(row_total);

		}

		$("#tot_qty").html(tot_qty);
		$("#tot_discount").html(tot_discount);
		$("#tot_taxable").html(tot_taxable);
		$("#total_taxable").html(tot_taxable);
		$("#tot_cgst").html(tot_cgst);
		$("#tot_sgst").html(tot_sgst);
		$("#total_tax").html(tot_cgst+tot_sgst);
		$("#tot_total").html(tot_total);
		$("#total_amount").html(tot_total);
		
		var round_off = tot_total;
		var dummy_grand_total = tot_total.toFixed(2);
		round_off = round_off.toFixed(0) - dummy_grand_total;
		
		$("#round_off").html(round_off);
		grand_total = tot_total+round_off;
		$("#grand_total").html(grand_total);
		
		$("#grand_total_hidden").val(grand_total);
		$("#round_off_hidden").val(round_off);
		$("#tot_qty_hidden").val(tot_qty);
		$("#tot_discount_hidden").val(tot_discount);
		$("#tot_taxable_hidden").val(tot_taxable);
		$("#total_cgst_hidden").val(tot_cgst);
		$("#total_sgst_hidden").val(tot_sgst);
		$("#new_row").remove();
		

	}
	else{
		var grand_total = $("#grand_total").html();
		grand_total = grand_total.replace("₹&nbsp;", "");
		grand_total = grand_total.replace("Rs&nbsp;", "");
		grand_total = grand_total.replace(",", "");
		var total_taxable = grand_total-total_disc;
		$("#other_discount").val(total_disc);
		$("#tot_discount_hidden").val(total_disc);
		$("#grand_total_hidden").val(total_taxable);
		$("#new_row").remove();
		var row = '<tr id="new_row"><td class="right">Net Amount Payable :</td><td class="right">₹ '+total_taxable+'</tr>';
		$("#div_total table").append(row);
	}
	
}
</script>
		
<div id="container">
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<?php
	if(strpos($tableid, "room")===false){
		$param = 'table_id='.$tableid;
	}
	else{
		$param = 'room_id='.$tableid;
	}
	?>
	<form action="bill_invoice_copy.php?<?php echo $param;?>" id="purchase_report" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onsubmit="return confirm('Are you sure ?');">
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
					$sql="SELECT `description`, `vat`, `excise`, sum(`kitchen_ticket_temp_2`.`unit`) as qty, `kitchen_ticket_temp_2`.`kot_no` as kotno ,item_price FROM `kitchen_ticket_temp_2` left join stock_available on stock_available.sno = kitchen_ticket_temp_2.item_id WHERE table_id='$tableid' group by `kitchen_ticket_temp_2`.`item_id`";
					//echo $sql;
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
						$kotno=$row['kotno'];
						echo'<tr>
							<td>'.$sno.'</td>
							<td>'.$row['description'].'</td>
							<td id="qty_'.$sno.'">'.$row['qty'].'</td>
							<td id="base_price_'.$sno.'">'.$base_price.'</td>
							<td id="disc_'.$sno.'"></td>
							<td id="taxable_'.$sno.'">'.$taxable.'</td>
							<td id="cgst_rate_'.$sno.'">'.$row['vat'].'</td>
							<td id="cgst_amt_'.$sno.'">'.$cgst_amt.'</td>
							<td id="sgst_rate_'.$sno.'">'.$row['excise'].'</td>
							<td id="sgst_amt_'.$sno.'">'.$sgst_amt.'</td>
							<td id="price_'.$sno.'">'.$e_price.'</td>
							<td id="total_'.$sno.'">'.$total.'</td>
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
					$round_off = round(round($total_amount_hidden,0) - $dummy_grand_total,2);
					$grand_total = $total_amount_hidden+$round_off;
					
					echo '
					<input type="hidden" name="total_amount_hidden" id="total_amount_hidden" value="'.$total_amount_hidden.'">
					<input type="hidden" name="tot_discount_hidden" id="tot_discount_hidden" value="">
					<input type="hidden" name="tot_taxable_hidden" id="tot_taxable_hidden" value="'.$tot_taxable_hidden.'">
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
			<table>
				<tr><td class="right">Sub Total :</td><td class="right"><?php echo $tot_taxable_hidden;?></td></tr>
				<tr><td class="right">Discount :</td><td class="right"><input type="text" name="discount" id="discount" class="small" value="<?php if(isset($_GET['edit_id'])) { echo $row_discount_waitor['other_discount']; }  ?>" onBlur="calculate();"></td></tr>
				<tr><td class="right">Total Taxable :</td><td class="right" id="total_taxable"></td></tr>
				<tr><td class="right">Tax :</td><td class="right" id="total_tax"><?php echo  $total_cgst_hidden+$total_sgst_hidden;  ?></td></tr>
				<tr><td class="right">Total :</td><td class="right" id="total_amount"><?php echo $total_amount_hidden; ?></td></tr>
				<tr><td class="right">Round Off :</td><td class="right" id="round_off"><?php echo $round_off; ?></td></tr>
				<tr><td class="right">Grand Total :</td><td class="right" id="grand_total"><?php echo $grand_total; ?></td></tr>
			</table>
		</div>
		<div id="div_button">
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
					<td><input type="text" name="customer_name" id="customer_name" placeholder="Guest Name" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['cust_name']; }  ?>"><input type="hidden" name="customer_sno" id="customer_sno" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['sno']; }  ?>"></td>
					<td><input type="text" name="customer_mobile" id="customer_mobile" placeholder="Mobile Number" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['mobile']; }  ?>"></td>
				</tr>
				<tr>
					<td><input type="text" name="customer_address" id="customer_address" placeholder="Address" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_customer['address']; }  ?>"></td>
					<td><input type="text" name="customer_gstin" id="customer_gstin" placeholder="GSTIN" class="medium" value=""></td>
				</tr>
				<tr>
					<td> <input type="text" name="waitor_name" id="waitor_name" placeholder="Waitor Name" class="medium" value="<?php if(isset($_GET['edit_id'])) { echo $row_discount_waitor['waitor_name']; }  ?>"><input type="hidden" name="waitor_id" id="waitor_id"></td>
					<td><input type="checkbox" name="tableprint" value="" checked>Print Table </td>
				</tr>
			</table>
			<!--<button name="cash" type="submit" id="cash" >Cash</button>
			<button name="card" type="submit" id="card">Card</button>
			<button name="credit" type="submit" id="credit" >Credit</button>-->
			<?php if(isset($_GET['kot'])){
				echo'<button name="non_chargeable" type="submit" id="non_chargeable" >Non Chargeable</button>';
			}
			?>
			
			<!--<button name="other" type="submit" id="other" >Other</button>-->
			<button name="submit" type="submit" id="submit" >Submit</button>
			<div id="insert_room">

			</div>
		</div>
		<input type="hidden" name="edit_pass" id="edit_pass" value="<?php if(isset($_GET['edit_id'])) { echo $_GET['edit_id']; }  ?>" >
	</form>
</div>
<?php	
		break;
	}
	case 2 :{
?>
<div id="container">
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>	
	<div id="div_button">
			<button name="cash" type="button" onClick="window.open('report_sale_modifiy.php', '_self');">Back To Home</button>
			<button name="card" type="button" id="card" onClick="window.open('scripts/printing_sale_restaurant_copy.php?inv=<?php echo $inv;?>', '_blank');">Print Bill</button>
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