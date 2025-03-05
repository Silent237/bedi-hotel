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
}
else{
	$tableid=$_GET['room_id'];
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
<script>
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=customer",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
			$("#customer_name").val(ui.item.label);
			$("#customer_mobile").val(ui.item.mobile);
			$("#customer_address").val(ui.item.address);
			$("#customer_gstin").val(ui.item.gstin);
			$("#customer_sno").val(ui.item.id);
			return false;
		}
	};
$("input#customer_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
$("input#customer_mobile").on("keydown.autocomplete", function() {
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
		var total_taxable = total_taxable-total_disc;
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
					$sql="SELECT `description`, `vat`, `excise`, `kitchen_ticket_temp`.`unit` as qty, item_price FROM `kitchen_ticket_temp` left join stock_available on stock_available.sno = kitchen_ticket_temp.item_id WHERE table_id='$tableid'";
					//echo $sql;
					$res=execute_query($sql);
					$tot_taxable_hidden=0;
					$total_cgst_hidden=0;
					$total_sgst_hidden=0;
					$tot_tax=0;
					$tot_qty_hidden=0;
					$total_amount_hidden=0;
					while($row=mysqli_fetch_array($res)){
						$tot_tax_rate = $row['vat']+$row['excise'];
						$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
						
						$cgst_amt = ($row['qty']*round((($base_price*$row['vat'])/100),2));
						$sgst_amt = ($row['qty']*round((($base_price*$row['excise'])/100),2));
						
						$tot_taxable_hidden+=($base_price*$row['qty']);
						$total_cgst_hidden+=$cgst_amt;
						$total_sgst_hidden+=$sgst_amt;
						$total_amount_hidden+=($row['item_price']);
						$tot_qty_hidden += $row['qty'];
						echo'<tr>
							<td>'.$sno.'</td>
							<td>'.$row['description'].'</td>
							<td id="qty_'.$sno.'">'.$row['qty'].'</td>
							<td id="base_price_'.$sno.'">'.$base_price.'</td>
							<td id="disc_'.$sno.'"></td>
							<td id="taxable_'.$sno.'">'.($base_price*$row['qty']).'</td>
							<td id="cgst_rate_'.$sno.'">'.$row['vat'].'</td>
							<td id="cgst_amt_'.$sno.'">'.$cgst_amt.'</td>
							<td id="sgst_rate_'.$sno.'">'.$row['excise'].'</td>
							<td id="sgst_amt_'.$sno.'">'.$sgst_amt.'</td>
							<td id="price_'.$sno.'">'.($row['item_price']/$row['qty']).'</td>
							<td id="total_'.$sno.'">'.$row['item_price'].'</td>
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
					$round_off = round($total_amount_hidden,0) - $dummy_grand_total;
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
					<input type="hidden" name="grand_total_hidden" id="grand_total_hidden" value="'.$grand_total.'">';
					
				}
				?>
			</table>
		</div>
		<div id="div_total">
			<table>
				<tr><td class="right">Sub Total :</td><td class="right"><?php echo amount_format($tot_taxable_hidden);?></td></tr>
				<tr><td class="right">Discount :</td><td class="right"><input type="text" name="discount" id="discount" class="small" onBlur="calculate();"></td></tr>
				<tr><td class="right">Total Taxable :</td><td class="right" id="total_taxable"></td></tr>
				<tr><td class="right">Tax :</td><td class="right" id="total_tax"><?php echo  amount_format($total_cgst_hidden+$total_sgst_hidden);  ?></td></tr>
				<tr><td class="right">Total :</td><td class="right" id="total_amount"><?php echo amount_format($total_amount_hidden); ?></td></tr>
				<tr><td class="right">Round Off :</td><td class="right" id="round_off"><?php echo amount_format($round_off); ?></td></tr>
				<tr><td class="right">Grand Total :</td><td class="right" id="grand_total"><?php echo amount_format($grand_total); ?></td></tr>
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
					<td><input type="text" name="customer_name" id="customer_name" placeholder="Customer Name" class="medium"><input type="hidden" name="customer_sno" id="customer_sno"></td>
					<td><input type="text" name="customer_mobile" id="customer_mobile" placeholder="Mobile Number" class="medium"></td>
				</tr>
				<tr>
					<td><input type="text" name="customer_address" id="customer_address" placeholder="Address" class="medium"></td>
					<td><input type="text" name="customer_gstin" id="customer_gstin" placeholder="GSTIN" class="medium"></td>
				</tr>
			</table>
			
		</div>
	</form>
</div>
<?php

	$total=0;
	//print_r($_POST);
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
	$sql = 'select * from invoice_sale_restaurant where invoice_type="'.$_POST['invoice_type'].'" and financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
		//echo $sql;
	$invoice_result = execute_query($sql);
	if(mysqli_num_rows($invoice_result)!=0){
		$invoice_no = mysqli_fetch_array($invoice_result);
		$_POST['invoice_no'] = $invoice_no['invoice_no']+1;
	}
	else{
		$_POST['invoice_no'] = 1;
	}
	
	
	$sql = 'INSERT INTO `invoice_sale_restaurant` (`invoice_type`, `invoice_no`, `total_amount`, `taxable_amount`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `tot_disc`, `other_discount`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`) 
	VALUES ("'.$_POST['invoice_type'].'", "'.$_POST['invoice_no'].'", "'.$_POST['total_amount_hidden'].'", "'.$_POST['tot_taxable_hidden'].'", "'.$_POST['sale_date'].'", "'.$_SESSION['username'].'", "'.$_POST['sale_date'].'", "'.$supplier.'", "", "'.$_POST['tot_qty_hidden'].'", "'.$_POST['total_cgst_hidden'].'", "'.$_POST['total_sgst_hidden'].'",  "'.$_POST['round_off_hidden'].'", "'.$_POST['tot_discount_hidden'].'", "'.$_POST['other_discount'].'", "'.$_POST['grand_total_hidden'].'", "'.$_POST['storeid'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'", "'.$year.'", "nocharge")';
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
	//echo $sql;
	$res=execute_query($sql);
	$i=1;
	$discount = $_POST['discount'];
	while($row=mysqli_fetch_array($res)){
		$unit = 26;
		$tot_tax_rate = $row['vat']+$row['excise'];
		$base_price = round($row['item_price']/(1+($tot_tax_rate/100))/$row['qty'],2);
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
		
		$sql = "INSERT INTO `stock_sale_restaurant` (`invoice_no`, `supplier_id`, `part_id`, `basicprice`, `discount`, `discount_value`, `vat`, `vat_value`, `excise`, `excise_value`, `taxable_amount`, `effective_price`, `qty`, `part_dateofpurchase`, `amount`, `unit`, `admin_remarks`) 
		VALUES ('".$inv."', '".$supplier."', '".$product_id."', '".$unitprice."', '".$discount."', '".$discount_value."', '".$vat."', '".$vat_value."', '".$sat."', '".$sat_value."', '".$taxable_amount."', '".$base_price."', '".$qty."', '".$_POST['sale_date']."', '".$total."', '".$unit."', '".$row['time_stamp']."')";
		//echo $sql.'<br>';
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
		}
	}
	$sql = "INSERT INTO `customer_transactions` (`cust_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`) 
	VALUES ('".$supplier."', 'sale_restaurant', '".$inv."', '".$_POST['grand_total_hidden']."', '".$_POST['sale_date']."', '$mop', 0, '".$year."','".$_POST['invoice_no']."')";
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
			echo'script>window.open("dine_in_order.php");</script>';
		
		
	}

?>


<?php
page_footer();
?>