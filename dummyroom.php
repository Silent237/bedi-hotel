<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
$msg = '';
$tabindex=1;
$response=1;

$sql = 'select * from dummy_room order by sno desc limit 1';
$serial = mysqli_fetch_array(execute_query($sql));
//print_r($serial);
$invoice= $serial['invoice_no']+1;
if(isset($_POST['submit'])) {
	//print_r($_POST);
	if($_POST['guest_name']=='') {
		$msg .= '<li>Please Enter Guest Name.</li>';
	}
	
		if($_POST['guest_name'] != ''){
			$sql = 'insert into dummy_room (invoice_no, guest_name, company_name, address, mobile_number, remarks, checkin_date, checkout_date,total_base_rent, total_extra_bed, total_discount, total_taxable_amount, total_cgst, total_sgst, grand_total) values("'.$invoice.'", "'.$_POST['guest_name'].'", "'.$_POST['company_name'].'", "'.$_POST['address'].'",  "'.$_POST['mobile_number'].'", "'.$_POST['remarks'].'", "'.$_POST['checkin_date'].'", "'.$_POST['checkout_date'].'","'.$_POST['total_base_rent'].'","'.$_POST['total_extra_bed'].'","'.$_POST['total_discount'].'","'.$_POST['total_taxable_amount'].'","'.$_POST['total_cgst'].'","'.$_POST['total_sgst'].'","'.$_POST['grand_total'].'")';
			//echo $sql.'</br>';
			execute_query($sql);
			if(mysqli_error($dbconnect)){
				$msg .= '<li>Error # 1 : '.$sql.'</li>';
				$inv=0;
			}
			else{
				$inv = insert_id();
			}
			$sno = mysqli_insert_id(dbconnect());
			}
			
			if($inv!=0){
				for($i=1;$i<=$_POST['id'];$i++){
					if($_POST['base_rent_'.$i] != 0){
							
						$sql = 'insert into dummy_room_invoice (invoice_no, guest_id, room_no, room_type, base_rent, extra_bed, discount, taxable_amount, cgst, sgst, total, creation_time) values 
						("'.$inv.'","'.$sno.'","'.$_POST['room_no_'.$i].'", "'.$_POST['room_type_'.$i].'", "'.$_POST['base_rent_'.$i].'", "'.$_POST['extra_bed_'.$i].'", "'.$_POST['discount_'.$i].'", "'.$_POST['taxable_amount_'.$i].'", "'.$_POST['cgst_'.$i].'", "'.$_POST['sgst_'.$i].'", "'.$_POST['total_'.$i].'" ,"'.date("Y-m-d H:i:s").'")';
						//echo $sql;
							execute_query($sql);
						
					}
					
				}
				
				if(mysqli_error($dbconnect)){
							$msg .= '<li>Error # 2 : '.mysqli_error($dbconnect).' >> '.$sql;
						}
						else{
						$msg .= '<li>New Data Inserted</li>';
						$msg .= '<li><a href="print_dummy_room.php?id='.$sno.'" >Click Here For Print</a></li>';
						}
			}	
	}

if (isset($_GET['edit_id'])) {
	$sql_edit = 'SELECT * FROM `dummy_room` WHERE `sno`="'.$_GET['edit_id'].'"';
	$result_edit = execute_query($sql_edit);
	$row_edit = mysqli_fetch_array($result_edit);
}

page_header();

			switch($response){
				case 1:{
?>
<style>
.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>
<script type="text/javascript">

function calculate(value) {
		
		var id = parseFloat(document.getElementById('id').value);
		//console.log(total_taxable_amount_result);
		var tot_base_rent = 0;
		var tot_extra_bed = 0;
		var tot_discount = 0;
		var tot_taxable_amount = 0;
		var tot_sgst = 0;
		var tot_cgst = 0;
		var tot_grand_total = 0;
		
		for(i=1;i<=id;i++){
			var base_rent = document.getElementById('base_rent_'+i+'').value;
			var extra_bed = document.getElementById('extra_bed_'+i+'').value;
			var discount = document.getElementById('discount_'+i+'').value;
			var total_taxable_amount = document.getElementById('taxable_amount_'+i+'');
			
			if(!extra_bed){
			extra_bed=0;
			}
			if(!base_rent){
			base_rent=0;
			}
			if(!discount){
			discount=0;
			}
			var discount_result = (parseFloat(base_rent) + parseFloat(extra_bed)) * parseFloat(discount)/100;
			var total_taxable_amount_result = (parseFloat(base_rent) + parseFloat(extra_bed)) - parseFloat(discount_result);
			total_taxable_amount.value = total_taxable_amount_result;
			
			//console.log(total_taxable_amount_result);
			//console.log(base_rent+'-'+extra_bed+'-'+total_taxable_amount_result);
			//console.log(total_taxable_amount);
			var cgst = document.getElementById('cgst_'+i+'').value;
			var sgst = document.getElementById('sgst_'+i+'').value;
			if(!cgst){
			cgst=0;
			}
			if(!sgst){
			sgst=0;
			}
			var cgst_value = document.getElementById('cgst_value_'+i+'');
			var cgstResult = (total_taxable_amount_result*cgst/100);
			cgstResult = Math.round(cgstResult*100)/100;
			cgst_value.value = cgstResult;
			console.log(cgstResult);
			
			var sgst_value = document.getElementById('sgst_value_'+i+'');
			var sgstResult = (total_taxable_amount_result*sgst/100);
			sgstResult = Math.round(sgstResult*100)/100;
			sgst_value.value = sgstResult;
			console.log(sgstResult);
			
			
			
			var result = document.getElementById('total_'+i+'');
			var myResult = parseFloat(total_taxable_amount_result) + parseFloat(cgstResult) + parseFloat(sgstResult);
			
			result.value = Math.round(myResult*100)/100;
			
			tot_base_rent += parseFloat(base_rent);
			tot_extra_bed += parseFloat(extra_bed);
			tot_discount += parseFloat(discount);
			tot_taxable_amount += parseFloat(total_taxable_amount_result);
			tot_sgst += parseFloat(sgst);
			tot_cgst += parseFloat(cgst);
			tot_grand_total += Math.round(parseFloat(myResult)*100)/100;
		}
		document.getElementById('total_base_rent').value = tot_base_rent;
		document.getElementById('total_extra_bed').value = tot_extra_bed;
		document.getElementById('total_discount').value = tot_discount;
		document.getElementById('total_taxable_amount').value = tot_taxable_amount;
		document.getElementById('total_sgst').value = tot_sgst;
		document.getElementById('total_cgst').value = tot_cgst;
		document.getElementById('grand_total').value = tot_grand_total;
	}

function tab_fill(id,tab){
	var current = document.getElementById('current').value;
	id = parseFloat(document.getElementById('id').value)+1;
	var room_no = document.getElementById('room_no_'+current).value;
	tab = (id*12)+30;
	
	var inputHTML = '<tr><td><input type="text" name="room_no_'+id+'" id="room_no_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" onFocus="getCurrent('+id+')"></td><td><input type="text" name="room_type_'+id+'" id="room_type_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;"></td><td><input type="text" name="base_rent_'+id+'" id="base_rent_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" onchange="tab_fill('+id+',12);"  onBlur="tab_fill('+id+',12);"></td><td><input type="text" name="extra_bed_'+id+'" id="extra_bed_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" oninput="calculate('+id+')"></td><td><input type="text" name="discount_'+id+'" id="discount_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" oninput="calculate('+id+')"></td><td><input type="text" name="taxable_amount_'+id+'" id="taxable_amount_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" oninput="calculate('+id+')" readonly></td><td><input type="text" name="cgst_'+id+'" id="cgst_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" oninput="calculate('+id+')"><input type="hidden" name="cgst_value_'+id+'" id="cgst_value_'+id+'" tabindex="'+(tab++)+'" value=""></td><td><input type="text" name="sgst_'+id+'" id="sgst_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" oninput="calculate('+id+')" onchange="tab_fill('+id+',12);"  onBlur="tab_fill('+id+',12);"><input type="hidden" name="sgst_value_'+id+'" id="sgst_value_'+id+'" tabindex="'+(tab++)+'" value=""></td><td><input type="text" name="total_'+id+'" id="total_'+id+'" tabindex="'+(tab++)+'" value="" style="width:100px;" readonly></td></tr>';
	console.log(id+">>"+current);
	if((id-current)==1 && room_no!='' && room_no!=0){
		$(inputHTML).insertBefore("tr#finalValues");
		document.getElementById('id').value = id;
	}
	calculate();
}

function getCurrent(id){
	document.getElementById('current').value = id;
}	
</script>




			<div id="content">
				<h2>&nbsp;</h2>
				<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
				<form name="main_form" method="POST" action="dummyroom.php" enctype="multipart/formdata">
					<table width="100%">
						
						<tr>
							<td>Guest Name :</td>
							<td><input type="text" name="guest_name" id="guest_name" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['guest_name'];} ?>"></td>
							
							
							<td>Address : </td>
							<td><input type="text" name="address" id="address" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['address'];} ?>" ></td>
						</tr>
						<tr>
							<td>Company Name : </td>
							<td><input type="text" name="company_name" id="company_name" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['company_name'];} ?>" ></td>
							<td>Mobile Number : </td>
							<td><input type="text" name="mobile_number" id="mobile_number" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['mobile_number'];} ?>" ></td>
						</tr>
						
						<tr>
							<td>Check-In Date</td>
							
							<td><input name="checkin_date" type="text" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['checkin_date'];}?>" class="field text medium" tabindex="<?php echo $tabindex++;?>" id="checkin_date" /></td>
								
							
							<td>Chect-Out Date</td>
							<td><input name="checkout_date" type="text" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['checkout_date'];}?>" class="field text medium" tabindex="<?php echo $tabindex++;?>" id="checkout_date" /></td>
							
								
						</tr>
						<tr>
							<td>Remarks : </td>
							<td><input type="text" name="remarks" id="remarks" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['remarks'];} ?>" ></td>
						</tr>
					</table>
				
					<table width="100%">
						<tr>
							
							<th>Room NO</th>
							<th>Room Type</th>
							<th>Base Rent</th>
							<th>Extra Bed</th>
							<th>Discount</th>
							<th>Taxable Amount</th>
							<th>CGST</th>
							<th>SGST</th>
							<th>Net Price</th>
						</tr>
						
						<tr>		
							
							<td><input type="text" id="room_no_1" name="room_no_1"  value="<?php if(isset($_GET['edit_id'])){echo $row_edit['room_no'];} ?>"  tabindex="<?php echo $tabindex++; ?>" style="width:100px;" onFocus="getCurrent(1)"></td>
							<td><input type="text" id="room_type_1" name="room_type_1"  value="<?php if(isset($_GET['edit_id'])){echo $row_edit['room_type'];} ?>" tabindex="<?php echo $tabindex++; ?>"  style="width:100px;"></td>
							<td><input type="text" id="base_rent_1" name="base_rent_1"  value="<?php if(isset($_GET['edit_id'])){echo $row_edit['base_rent'];} ?>" tabindex="<?php echo $tabindex++; ?>"  style="width:100px;" onchange="tab_fill(1,12);"  onBlur="tab_fill(1,12);"></td>
							<td><input type="text" id="extra_bed_1" name="extra_bed_1"  value="<?php if(isset($_GET['edit_id'])){echo $row_edit['extra_bed'];} ?>" tabindex="<?php echo $tabindex++; ?>" style="width:100px;" oninput="calculate(1)" ></td>
							<td><input type="text" id="discount_1" name="discount_1"  value="<?php if(isset($_GET['edit_id'])){echo $row_edit['discount_1'];} ?>" tabindex="<?php echo $tabindex++; ?>" style="width:100px;" oninput="calculate(1)" ></td>
							<td><input type="text" id="taxable_amount_1" name="taxable_amount_1"  value="<?php if(isset($_GET['edit_id'])){echo $row_edit['taxable_amount'];} ?>" tabindex="<?php echo $tabindex++; ?>"  style="width:100px;" oninput="calculate(1)" readonly ></td>
							<td><input type="text" id="cgst_1" name="cgst_1" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['cgst'];} ?> "  tabindex="<?php echo $tabindex++; ?>" style="width:100px;" oninput="calculate(1)">
							
							<input type="hidden" id="cgst_value_1" name="cgst_value_1" value=""  style="width:100px;" oninput="calculate(1)" tabindex="<?php echo $tabindex++; ?>"></td>
							
							<td><input type="text" id="sgst_1" name="sgst_1" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['cgst'];} ?> " tabindex="<?php echo $tabindex++; ?>" style="width:100px;" oninput="calculate(1)" onchange="tab_fill(1,12);"  onBlur="tab_fill(1,12);">
							
							<input type="hidden" id="sgst_value_1" name="sgst_value_1" value=""  style="width:100px;" oninput="calculate(1)" tabindex="<?php echo $tabindex++; ?>"></td>
							
							<td><input type="text" id="total_1" name="total_1" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['total'];} ?> " tabindex="<?php echo $tabindex++; ?>" style="width:100px;" oninput="calculate(1)" readonly></td>
						</tr>
						
						<tr id="finalValues"></tr>
						<tr id="total">
							<th colspan="2">Total</th>
							<th><input type="text" name="total_base_rent" id="total_base_rent" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['total_base_rent'];} ?>" ></th>
							<th><input type="text" name="total_extra_bed" id="total_extra_bed" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['total_extra_bed'];} ?>"></th>
							<th><input type="text" name="total_discount" id="total_discount" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['total_discount'];} ?>"></th>
							<th><input type="text" name="total_taxable_amount" id="total_taxable_amount" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['total_taxable_amount'];} ?>"></th>
							<th><input type="text" name="total_sgst" id="total_sgst" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['total_sgst'];} ?>"></th>
							<th><input type="text" name="total_cgst" id="total_cgst" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['total_cgst'];} ?>"></th>
							<th><input type="text" name="grand_total" id="grand_total" readonly style="width:100px;" value="<?php if(isset($_GET['edit_id'])){echo $row_banquet_edit['grand_total'];} ?>"></th>
						</tr>
						<tr>
							<th colspan="11"><input type="submit" name="submit" value="Submit" tabindex="<?php echo $tabindex++; ?>"></th>
							<input type="hidden" name="edit_sno" value="<?php if(isset($_GET['edit_id'])){echo $_GET['edit_id'];} ?>">
							<input type="hidden" value="1" name="id" id="current">
							
							<input type="hidden" value="1" name="id" id="id">
						</tr>
					</table>
				</form>
			</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#checkin_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['id'])){
		echo $old_data['checkin_date'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>',
	});
$('#exit_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_GET['id'])){
		echo $old_data['exit_date'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>',
	});

$('select[multiple]').multiselect({
    columns: 1,
    placeholder: 'Select options'
});


</script>

<script language="JavaScript">
$('#checkout_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['id'])){
		echo $old_data['checkout_date'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>',
	});
$('#exit_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_GET['id'])){
		echo $old_data['exit_date'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>',
	});

$('select[multiple]').multiselect({
    columns: 1,
    placeholder: 'Select options'
});


</script>
			
<?php
						
						
					break;
				}
			case 2:{
?>
			<div id="content">
				<h2>&nbsp;</h2>
				<h2><input type="button" name="print_button" onclick="loadOtherPage();" value="Print Receipt"></h2>
			</div>
<script type="text/javascript">
function loadOtherPage() {
    $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "print_dummy_report.php?id=<?php echo $sno;?>") // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page
}
</script>

				
					
<?php				
				
					break;
				}
			}
page_footer();
?>