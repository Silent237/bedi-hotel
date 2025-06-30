<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
$kot='';
$stop = 0;
$kot_num='';
$username=$_SESSION['username'];
function gen_epin(){
	$length=9;
	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$char_length=(strlen($chars)-1);
	$string=$chars[rand(0,$char_length)];
	for($i=1;$i<$length;$i=strlen($string)){
		$r=$chars[rand(0,$char_length)];
		if($r!=$string[$i-1]){
			$string .= $r;
		}
	}
	return $string;	
}
while('1'=='1'){
	$epin = gen_epin();
	$sql = "select * from kitchen_ticket_temp where e_pin = '".$epin."'";
	$epin_result = execute_query($sql);
	if(mysqli_num_rows($epin_result)==0){
		break;
	}
}
date_default_timezone_set('Asia/Calcutta');
page_header();
?>
<link href="css/dine_in_order1.css" rel="stylesheet">
<?php
$res_chk='';
if (isset($_GET['edit_id'])) {
	$sql_invoice='select * from `invoice_sale_restaurant` where `sno`="'.$_GET['edit_id'].'"';
	//echo $sql_invoice;
	$result_invoice=execute_query($sql_invoice);
	$row_invoice=mysqli_fetch_array($result_invoice);
	if(isset($_POST['nocharge_kot'])){
    	$kot_type='1';
    }
    else{
    	$kot_type='0';
    }

	$sql_stock='select * from `stock_sale_restaurant` where `invoice_no`="'.$row_invoice['sno'].'"';
	//echo $sql_stock;
	$result_stock=execute_query($sql_stock);
	while($row_stock=mysqli_fetch_array($result_stock)) {
		$sql_stock_available='select * from `stock_available` where `sno`="'.$row_stock['part_id'].'"';
		$result_stock_available=execute_query($sql_stock_available);
		$row_stock_available=mysqli_fetch_array($result_stock_available);
	 	$sql_ktt= 'INSERT INTO `kitchen_ticket_temp`(`unit`,`item_id`, `item_name`, `item_price`, `table_id`,`time_stamp`,`kot_type`,`kot_no`) VALUES("'.$row_stock['qty'].'","'.$row_stock['part_id'].'","'.$row_stock_available['description'].'","'.$row_stock_available['mrp'].'","'.$row_invoice['storeid'].'","'.date("Y-m-d H:i:sa").'","'.$kot_type.'","'.$row_invoice['kot_no'].'")';
	 	execute_query($sql_ktt);

	 	$sql_ktt1= 'INSERT INTO `kitchen_ticket_temp_2`(`unit`,`item_id`, `item_name`, `item_price`, `table_id`,`time_stamp`,`kot_type`,`kot_no`) VALUES("'.$row_stock['qty'].'","'.$row_stock['part_id'].'","'.$row_stock_available['description'].'","'.$row_stock_available['mrp'].'","'.$row_invoice['storeid'].'","'.date("Y-m-d H:i:sa").'","'.$kot_type.'","'.$row_invoice['kot_no'].'")';
	 	execute_query($sql_ktt1);
	 } 

	
}
if(isset($_POST['nocharge_bill'])){

	$custid=$_POST['customer_sno'];
	$end=$_POST['current_id'];
	$timestamp= microtime(true);
	if(isset($_POST['table_sno'])){
		$table=$_POST['table_sno'];	
	}
	elseif(isset($_POST['room_sno'])){
		$table="room_".$_POST['room_sno'];
	}
	$flag=0;
	$grand_total=0;
	$sql = 'select * from invoice_sale_restaurant order by abs(nck_no) desc limit 1';
		//echo $sql;
	$nonkot_result = execute_query($sql);
	if(mysqli_num_rows($nonkot_result)!=0){
		$nonkot_no = mysqli_fetch_array($nonkot_result);
		$nck_no = $nonkot_no['nck_no']+1;
	}
	else{
		$nck_no = 1;
	}

	$sql = 'INSERT INTO `invoice_sale_restaurant` (`concerned_person`,`invoice_type`, `invoice_no`, `total_amount`, `taxable_amount`, `dateofdispatch`, `user_id`, `timestamp`, `supplier_id`, `remark`, `quantity`, `tot_vat`, `tot_sat`, `round_off`, `tot_disc`, `other_discount`, `grand_total`, `storeid`, `created_by`, `creation_time`, `financial_year`, `mode_of_payment`,`table_no`,`waitor_name`,`nck_no`) 
	VALUES ("'.$_POST['customer_name'].'","","", "", "", "'.date("Y-m-d").'", "'.$username.'", "'.date("Y-m-d").'", "'.$custid.'", "", "", "", "",  "", "", "", "", "'.$table.'", "","'.date("Y-m-d H:i:s").'", "", "nocharge","","","'.$nck_no.'")';
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 1  : '.mysqli_error($db).' >> '.$sql.'</li>';
		$inv=0;
	}
	else{
		$inv = insert_id($db);
	}

	for($i=1;$i<=$end;$i++){
		$curr_id="data_id_".$i;
        $curr_sno=$_POST[$curr_id];
        $unit_id="unit_data_".$curr_sno;
        $item_id="item_name_".$curr_sno;
        $price_id="unit_price_".$curr_sno;
        $unit=$_POST[$unit_id];
        $item=$_POST[$item_id];
        $price=$_POST[$price_id];
        $date=date("Y-m-d");
        $timestamp=date("Y-m-d H:i:sa");
        $total=$unit*$price;
        $grand_total+=$total;
		$sql = "INSERT INTO `stock_sale_restaurant` ( `supplier_id`, `part_id`, `basicprice`, `discount`, `discount_value`, `vat`, `vat_value`, `excise`, `excise_value`, `taxable_amount`, `effective_price`, `qty`, `part_dateofpurchase`, `amount`, `unit`, `admin_remarks`,`nck_no` , `invoice_no`) 
		VALUES ('".$custid."', '".$curr_sno."', '".$price."', '', '', '', '', '', '', '".$total."', '".$price."', '".$unit."', '".$date."', '".$total."', '".$unit."', '".$timestamp."','".$nck_no."','".$inv."')";
		//echo $sql.'<br>';
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
		}

	}

	$sql_update = 'UPDATE `invoice_sale_restaurant` SET `total_amount`="'.$grand_total.'" , `taxable_amount`="'.$grand_total.'" , `grand_total`="'.$grand_total.'" WHERE `sno`="'.$inv.'"';
	execute_query($sql_update);
    
	$sql = "INSERT INTO `customer_transactions` (`cust_id`, `type`, `number`, `amount`, `timestamp`, `remarks`, `account`,`financial_year`,`invoice_no`,mop) 
	VALUES ('".$custid."', 'sale_restaurant', '', '".$grand_total."', '".$timestamp."', 'nocharge','', '','".$nck_no."','nocharge')";
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 3 : '.mysqli_error($db).' >> '.$sql.'</li>';
	}
}
if(isset($_POST['cancel'])){
	if(isset($_POST['room_sno'])){
		$tableid="room_".$_POST['room_sno'];
	}
    $sql_delete = 'update `kitchen_ticket_temp` set cancel_timestamp="'.date("Y-m-d H:i:s").'" WHERE `table_id`="'.$tableid.'"';
    execute_query($sql_delete);
    $sql_delete = 'update `kitchen_ticket_temp_2` set cancel_timestamp="'.date("Y-m-d H:i:s").'" WHERE `table_id`="'.$tableid.'"';
    execute_query($sql_delete); 
	$sql = 'UPDATE `room_master` SET `booked_status`="0" WHERE sno="'.$_POST['room_sno'].'"';
	$res = execute_query($sql);
	if($res){
		header("location:dine_in_order_room.php");
	}
}
else{
	if(isset($_POST['done'])){
		$end=$_POST['current_id'];
		$end_other = $_POST['current_id_other'];
		$timestamp= microtime(true);
		if(isset($_POST['table_sno'])){
			$table=$_POST['table_sno'];	
		}
		elseif(isset($_POST['room_sno'])){
			$table="room_".$_POST['room_sno'];
		}
		$flag=1;
		$sql_check_kitchen = 'SELECT * FROM `kitchen_ticket_temp` WHERE `e_pin`="'.$_POST['epin'].'"';
		$result_check_kitchen = execute_query($sql_check_kitchen);
		if(mysqli_num_rows($result_check_kitchen)==0){
			while(1==1){
				$sql = 'select * from invoice_sale_restaurant  order by abs(kot_no) desc limit 1';
				$kot_res = execute_query($sql);
				$sql="SELECT * FROM `kitchen_ticket_temp` order by abs(kot_no) desc limit 1";
				$k_kot=execute_query($sql);
				$numrowkot=mysqli_num_rows($k_kot);
				if(mysqli_num_rows($kot_res)!=0 || $numrowkot !=0){
					if(mysqli_num_rows($k_kot) != 0){
						$r_kot=mysqli_fetch_array($k_kot);
						$kot_num=$r_kot['kot_no'];
					}
					$kot_no = mysqli_fetch_array($kot_res);
					$kot=$kot_no['kot_no'];
					if($kot_num > $kot){
						$kotno=$kot_num+1;
					}
					else{
						$kotno= $kot_no['kot_no']+1;
					}
					
				}
				else{
					$kotno = 1;
				}
				$sql_check = 'SELECT * FROM `kitchen_ticket_temp` WHERE `kot_no`="'.$kotno.'"';
				$result_check = execute_query($sql_check);
				if (mysqli_num_rows($result_check) == 0) {
					//echo 'Done';
					//echo $sql_check;
				 	break;
				}
			}
			for($i=1;$i<=$end_other;$i++){
				$curr_id="data_id_".$i;
				if (isset($_POST[$curr_id])) {
			        $curr_sno=$_POST[$curr_id];
			        $unit_id="unit_data_".$curr_sno;
			        $item_id="item_name_".$curr_sno;
			        $price_id="unit_price_".$curr_sno;
			        $unit=$_POST[$unit_id];
			        $item=$_POST[$item_id];
			        $price=$_POST[$price_id];
			        if(isset($_POST['nocharge_kot'])){
			        	$kot_type='1';
			        }
			        else{
			        	$kot_type='0';
			        }
			        $sql="INSERT INTO `kitchen_ticket_temp`(`unit`,`item_id`, `item_name`, `item_price`, `table_id`,`time_stamp`,`kot_type`,`kot_no`,`e_pin`) VALUES('$unit','$curr_sno','$item','$price','$table','$timestamp','$kot_type','$kotno','".$_POST['epin']."')";
			        $res_chk=execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<li>Error # 0012 >> '.mysqli_error($db).' >> '.$sql;
					}
					else{
						$flag=1;
					}
				}
				else{
					//echo 'Not Found';
				}
		    }
		}
		if($flag==1){
			$sql_print = 'SELECT * FROM `kitchen_ticket_temp` WHERE `e_pin`="'.$_POST['epin'].'" ORDER BY `sno` DESC LIMIT 1';
			$row_print = mysqli_fetch_array(execute_query($sql_print));
			$msg .= '<script>window.open("kot.php?tid='.$table.'&ts='.$row_print['time_stamp'].'&kot='.$row_print['kot_no'].'");</script>
			<li>Saved</li>';
			if(isset($_POST['table_sno'])){
				$table=$_POST['table_sno'];	
				$sql="UPDATE `res_table` SET `booked_status`='1' WHERE sno='$table'";
				execute_query($sql);
			}
			elseif(isset($_POST['room_sno'])){
				$table=$_POST['room_sno'];
				$sql="UPDATE `room_master` SET `booked_status`='1' WHERE sno='$table'";
				execute_query($sql);
			}

		}
		
	}
}
if(isset($_GET['table_id']) || isset($_GET['room_id']) || isset($_GET['edit_id'])){
	$response=2;
} 

?>


<?php
switch($response){
	case 1:{
?>
<style type="text/css">
#tables button{
  border: none;
  outline: none;
  background-color:#66AAAA;
  color: white;
  height:70px;
  width:110px;
  cursor: pointer;
  margin: 5px;
  font-size: 30px;
}
</style>
<div id="container">
	
        <!--<h2>Table</h2>-->
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>

		<form action="" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="">
			<!--<div id="tables">
			<?php
				$sql="SELECT * FROM `res_table`";
				$res=execute_query($sql);
				while($row=mysqli_fetch_array($res)){
					$id=$row['sno'];
                    if($row['booked_status'] ==1){
				        echo '<button type="button" onclick="window.open(\'dine_in_order_room.php?table_id='.$id.'\', \'_self\');" id="table_'.$id.'" style="background-color:red;">'.$row['table_number'].'</button></a>';
				    }
                    else{
                         echo '<button type="button"  onclick="window.open(\'dine_in_order_room.php?table_id='.$id.'\', \'_self\');" id="table_'.$id.'">'.$row['table_number'].'</button></a>';
                    }
                }

			?>
			</div>-->
			<h2>Room Service</h2>	
			<div id="tables">
			<?php
				$sql="SELECT * FROM `room_master` order by abs(room_name)";
				$res=execute_query($sql);
				while($row=mysqli_fetch_array($res)){
					$id=$row['sno'];
                    if($row['booked_status'] == 1){
				        echo '<button type="button" onclick="window.open(\'dine_in_order_room.php?room_id='.$id.'\', \'_self\');" id="table_'.$id.'" style="background-color:red;">'.$row['room_name'].'</button></a>';
				    }
                    else{
                         echo '<button type="button"  onclick="window.open(\'dine_in_order_room.php?room_id='.$id.'\', \'_self\');" id="table_'.$id.'">'.$row['room_name'].'</button></a>';
                    }
                }

			?>
			</div>
		</form>
</div>

<?php
		break;
	}
	case 2:{
		if (isset($_GET['edit_id'])) {
			$tab_id=$row_invoice['storeid'];
		}
		elseif(isset($_GET['table_id'])){
			$tab_id=$_GET['table_id'];
		}
		else{
			$tab_id="room_".$_GET['room_id'];
			$sql="SELECT * FROM `room_master` where sno=".$_GET['room_id'];
			$room_details=mysqli_fetch_assoc(execute_query($sql));
		}
		$totPrice=0;
		$sql="SELECT * FROM `kitchen_ticket_temp` WHERE table_id='$tab_id'";
		$res=execute_query($sql);
		$rowcount=mysqli_num_rows($res);
		if($rowcount > 0){
			while($row1=mysqli_fetch_array($res)){
				$totPrice+=$row1['item_price'];
			}
			echo '<input type="hidden" name="pre_total" id="pre_total" value="'.$totPrice.'">';
		}
		?>
		
		<script>

function filter_item(id){
	var item_id='#item_list_'+id;
	$("#display_box").html($(item_id).clone());
	$('.item_box').hide();
	$(item_id).show(); 
}

$(document).ready(function() {
	var tid = "";
	$('#mytable tr').click(function(event) {
		tid = $(this).attr('id');
	});
	$("#del").click(function() {
		if ($('#' + tid).length) {
			$('#' + tid).remove();
		}
	});
});

var totalPrice=0;
function insert_data(id){
	var item_name='item_name_'+id;
	var item_price='item_price_'+id; 
	var item=document.getElementById(item_name).value;
	var price=parseFloat(document.getElementById(item_price).value);
	var totalPrice = parseFloat($("#show_total").html()) || 0;
	var current_id = parseFloat($("#current_id").val());
	var current_id_other = parseFloat($("#current_id_other").val());
	var chk_row = document.getElementById("row_"+id);
	if(chk_row){
		alert("Item already added");
		return;
	}
	current_id++;
	current_id_other++;
	$("#current_id").val(current_id);
	$("#current_id_other").val(current_id_other);
	totalPrice+=price;
	$("#show_total").html(totalPrice);
	$("#show_total1").html(totalPrice);
	$('#mytable > tbody:last').append('<tr id="row_'+id+'">'+
		'<td><input style="width:40px" type="number" min="1" value="1" name="unit_data_'+id+'" id="unit_input_'+id+'" onchange="update_quantity('+id+');"></td>'+ 
		'<td id="item_name_'+id+'">' + item + '<br/><input type="text" name="item_cooking_'+id+'" id="item_cooking_'+id+'" placeholder="Cooking Instructions" style="width:140px;"></td>'+ 
		'<input type="hidden" name="item_name_'+id+'" id="item1_'+id+'" value="'+item+'">'+
		'<td id="item_price'+id+'">' + price + '</td>'+ 
		'<input type="hidden" name="item_price'+id+'" id="price1_'+id+'" value="'+price+'">'+
		'<input type="hidden" name="unit_price_'+id+'" id="unit_price_'+id+'" value="'+price+'">'+
		'<input type="hidden" name="data_id_'+current_id_other+'" id="data_id_'+current_id_other+'" value="'+id+'">'+
		'<td><button type="button" onclick="remove_data('+id+')"><i class="fas fa-close" style="color:red;"></i></button></td>'+
		'</tr>');

	var total_quantity = parseFloat(document.getElementById('total_quantity').value);
	total_quantity = total_quantity + 1;
	$("#total_quantity").val(total_quantity);
	$('#done').show();
}

function update_quantity(id) {
	let qty = parseInt($('#unit_input_' + id).val());
	if (!qty || qty < 1) {
		qty = 1;
		$('#unit_input_' + id).val(1);
	}
	let unitPrice = parseFloat($('#unit_price_' + id).val());
	let newPrice = qty * unitPrice;
	$('#item_price' + id).html(newPrice.toFixed(2));
	$('#price1_' + id).val(newPrice);
	let total = 0;
	$("#mytable tbody tr").each(function () {
		let rowId = $(this).attr('id');
		let itemId = rowId.split('_')[1];
		let price = parseFloat($('#price1_' + itemId).val()) || 0;
		total += price;
	});
	$('#show_total').html(total.toFixed(2));
	$('#show_total1').html(total.toFixed(2));
	let total_quantity = 0;
	$("#mytable tbody tr").each(function () {
		let rowId = $(this).attr('id');
		let itemId = rowId.split('_')[1];
		let qty = parseInt($('#unit_input_' + itemId).val()) || 0;
		total_quantity += qty;
	});
	$('#total_quantity').val(total_quantity);
	if (total_quantity < 1) {
		$('#done').hide();
	}
}

function remove_data(id){
	$('#row_'+id).remove();
	update_quantity(0); // Recalculate total price and quantity
}

$(function() {
	$("input#search_product").on("keydown.autocomplete", function() {
		$(this).autocomplete({
			source: function (request, response){
				$.getJSON("scripts/ajax.php?id=prod",request, response);
			},
			minLength: 1,
			select: function( event, ui ) {
				insert_data(ui.item.id);
				$("#search_product").val('');
				$("#search_product").focus();
				return false;
			}
		});
	});
});

$(function () {
	$("#nocharge_kot").click(function () {
		if ($(this).is(":checked")) {
			$("#show_div").show();
			$("#total_nocharge").show();
			$("#total_bill").hide();
		} else {
			$("#show_div").hide();
			$("#total_nocharge").hide();
			$("#total_bill").show();
		}
	});
});

$(function() {
	$("input#customer_name").on("keydown.autocomplete", function() {
		$(this).autocomplete({
			source: function (request, response){
				$.getJSON("scripts/ajax.php?id=customer",request, response);
			},
			minLength: 1,
			select: function( event, ui ) {
				$("#customer_name").val(ui.item.label);
				$("#customer_sno").val(ui.item.id);
			}
		});
	});
});

</script>


		<div id="container">
			<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
			<form action="dine_in_order_room.php" class="wufoo leftLabel page1"  name="purchase_report" id="purchase_report" enctype="multipart/form-data" method="post" onSubmit="" >

				<div id="left_container">
					<div id="left_div">
						<div id="ajax_loader" style="display: none;"><img src="images/loading_transparent.gif"></div>
						<table id="mytable">
							<tr>
								<th>Unit</th>
								<th>Item</th>
								<th>Price</th>					
								<th colspan="2"></th>
							</tr>
							<?php
							$total=0;
							if (isset($_GET['edit_id'])) {
								$tableid=$row_invoice['storeid'];
							}
							elseif(isset($_GET['table_id'])){
								$tableid=$_GET['table_id'];
							}
							else{
								$tableid="room_".$_GET['room_id'];
							}
							$sql="SELECT * FROM `kitchen_ticket_temp` WHERE table_id='$tableid' and (invoice_no is null or invoice_no='') and cancel_timestamp is null";
							//echo $sql;
							$res=execute_query($sql);
							while($item_details=mysqli_fetch_array($res)){
								$total+=($item_details['item_price']*$item_details['unit']);
								echo'<tr style="background-color:yellow;" id="tr_'.$item_details['sno'].'">
									<td id="unit">'.$item_details['unit'].'</td>
									<td id="item">'.$item_details['item_name'].'</td>
									<td id="price">'.$item_details['item_price']*$item_details['unit'].'</td>
									<td id="reduce"><button type="button" onclick="reduce_kot('.$item_details['sno'].')">-</button></td>
								</tr>';
								if($item_details['kot_type']=='1'){
									$kot='1';
								}
								else{
									$kot='';
								}
							}
							?>
						</table>
						<input type="hidden" name="current_id_other" id="current_id_other" value="0">
						<input type="hidden" name="current_id" id="current_id" value="0">
						<input type="hidden" name="total_quantity" id="total_quantity" value="0">
					</div>
					<div id="total_show">
						<b>NON CHARGEABLE KOT</b><input type="checkbox" name="nocharge_kot" id="nocharge_kot" value="">
						<div id="show_div" style="display: none">
							<input type="text" name="customer_name" id="customer_name" placeholder="Customer">
							<input type="hidden" name="customer_sno" id="customer_sno">
						</div>
						<div id="total_bill">
							<a href="bill_invoice.php?table_id=<?php echo $tableid; if(isset($_GET['edit_id'])){echo '&edit_id='.$_GET['edit_id'];}?>&type=room"><button type="button" id="total_cal"><h3>Total : Rs.<span id="show_total"><?php echo $total; ?></span></h3></button></a>
						</div>
						<div id="total_nocharge" style="display:none;" >
							<button type="submit" name="nocharge_bill"><b>Total : Rs.</b><span id="show_total1"><?php echo $total;?></span></button>
						</div>
						
					</div>
				</div>
				<div id="item_list">
					<div id="inside_item">
						<div id="search_box">
							Search Product : <input type="text" name="search_product" id="search_product" placeholder="Shortcut : Ctrl+; (Control with Semi-Colon)">
						</div>
						<div id="display_box">
							
						</div>

					</div>
					<div id="div_button">
				
						<?php
						if (isset($_GET['edit_id'])) {
							$pos = strpos($row_invoice['storeid'], "room_");
							//echo '@@>>'.$pos;
							if($pos !== false){
								$row_invoice['storeid'] = str_replace("room_", "", $row_invoice['storeid']);
								$sql = 'select * from room_master where sno='.$row_invoice['storeid'];
								//echo $sql;
								$room_details = mysqli_fetch_array(execute_query($sql));
								
								echo '<button type="button" name="Table" >Room: '.$room_details['room_name'].'</button>';
								echo '<input type="hidden" id="room_sno" name="room_sno" value="'.$row_invoice['storeid'].'">';
							}
							else{
								;
								$sql = 'select * from res_table where sno='.$row_invoice['storeid'];
								//echo $sql;
								$table_name = mysqli_fetch_array(execute_query($sql));
								echo '<button type="button" name="Table" >Table: '.$table_name['table_number'].'</button>';
								echo '<input type="hidden" id="table_sno" name="table_sno" value="'.$row_invoice['storeid'].'">';
							}
						}
						elseif(isset($_GET['table_id'])){
							$sql = 'select * from res_table where sno='.$tableid;
							$table_name = mysqli_fetch_array(execute_query($sql));
							echo '<button type="button" name="Table" >Table: '.$table_name['table_number'].'</button>';
							echo '<input type="hidden" id="table_sno" name="table_sno" value="'.$_GET['table_id'].'">';
						}
						else{
							echo '<button type="button" name="Table" >Table: '.$room_details['room_name'].'</button>';
							echo '<input type="hidden" id="room_sno" name="room_sno" value="'.$_GET['room_id'].'">';
						}
						?>
						<a onclick="confirmation();" style="height: 50px;width: 120px;margin-left: 10px; border-radius: 5px;background-color: #cccccc;box-shadow: 1px 1px #666666;    appearance: button; -webkit-writing-mode: horizontal-tb !important;display:none;    text-align: center;align-items: flex-start;margin-top: 0px;padding-top: 0px;" id="verify"><b>Verify</b></a>
						<input type="hidden" name="epin" id="epin" value="<?php echo $epin; ?>">
						<button type="submit" name="done" id="done" <?php if (isset($_GET['edit_id'])) {?>disabled <?php } ?> style="display: none;">Done</button>
						<button type="submit" name="cancel" id="cancel">Bill Wash</button>
						<?php
						if(isset($_GET['table_id'])){
							$sql = 'select * from res_table where sno='.$tableid;
							$table_name = mysqli_fetch_array(execute_query($sql));
							echo '<a href="change_table.php?tableid='.$table_name['sno'].'"><button type="button" name="Table" >Change Table: '.$table_name['table_number'].'</button></a>';
							
						}
						else if(!isset($_GET['edit_id'])){
							echo '<a href="change_table.php?roomid='.$room_details['sno'].'"><button type="button" name="Table" >Change Table: '.$room_details['room_name'].'</button></a>';
							
						}
						else{
							echo '<button type="button" name="Table" >Change Room: </button>';
						}
						?>
					</div>			
				</div>

				<div id="category_item">
					<div class="mySlides fade" id="category_list">
						<?php
						$sql="SELECT * FROM `new_type`";
						$res=execute_query($sql);
						while($cat_item=mysqli_fetch_array($res)){
							$id=$cat_item['sno'];                    
							echo '<button type="button" id="cat_'.$cat_item['sno'].'" class="cat_'.$cat_item['sno'].'"  onclick="filter_item('.$id.')" >'.$cat_item['description'].'</button>';
						}
						?>
					</div>
				</div>

			  



		   </form>
		   
<?php
$sql="SELECT * FROM `new_type` order by description";
$res1=execute_query($sql);
while($cat_item1=mysqli_fetch_array($res1)){
?>
	<div id="item_list_<?php echo $cat_item1['sno']; ?>" style="display:none;" class="item_box">
	<?php 
	$sql="SELECT * FROM `stock_available` where `type`=".$cat_item1['sno'];
	$res=execute_query($sql);
	while($row=mysqli_fetch_array($res)){
		$id=$row['sno'];
		echo '<div id="item_container"><div id="item_'.$row['sno'].'" type="button" class="item_'.$row['type'].'" onclick="insert_data('.$row['sno'].');">'.$row['description'].'</div>
		<input name="unit_'.$row['sno'].'" id="unit_'.$row['sno'].'" type="hidden" value="1">
		<input name="item_id_'.$row['sno'].'" id="item_id_'.$row['sno'].'" type="hidden" value="'.$row['sno'].'">
		<input name="item_name_'.$row['sno'].'"  id="item_name_'.$row['sno'].'" type="hidden" value="'.$row['description'].'">
		<input name="item_price_'.$row['sno'].'"  id="item_price_'.$row['sno'].'" type="hidden" value="'.$row['mrp'].'"></div>';
	}
	?>
	</div>
<?php } ?>
		</div>

<?php
		break;
	}
}
navigation('');
page_footer(); 
?> 	
<script type="text/javascript">
	function confirmation() {
		var show = 'These are the items and quantities which have been selected by you.';
		var id = document.getElementById('current_id_other').value;
		for (var i = 1; i <= id; i++) {
			var item = document.getElementById('data_id_'+i);
			if (!item) {}
			else{
				item = item.value;
				var name = document.getElementById('item1_'+item).value;
				var unit = document.getElementById('unit1_'+item).value;
				show += '\n'+name + '-' +unit; 
			}
		}
		if (confirm(show)) {
			$("#done").show();
			$("#verify").hide();
		}
		else{
			$("#done").hide();
			$("#verify").show();
		}
	}
</script>