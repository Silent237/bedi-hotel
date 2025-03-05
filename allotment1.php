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
$tab=1;
$con = $db;
if(isset($_GET['alt'])){
	$sql = 'select * from customer where sno='.$_GET['alt'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
	$sql='select * from customer_transactions where allotment_id="" and cust_id='.$_GET['alt'];
	$result = execute_query($sql);
	$advance=mysqli_fetch_assoc( $result );
}

if(isset($_GET['cancel'])){
	$sql = 'select * from allotment where sno='.$_GET['cancel'];
	$row = mysqli_fetch_array(execute_query($sql));
	if($row['cancel_date']==''){
		$sql = 'update allotment set cancel_date=CURRENT_TIMESTAMP where sno='.$_GET['cancel'];
		execute_query($sql);
		$sql = 'update room_master set status=NULL where sno='.$row['room_id'];
		execute_query($sql);
	}
	else{
		$sql = 'update allotment set cancel_date=NULL where sno='.$_GET['cancel'];
		execute_query($sql);
		$sql = 'update room_master set status=1 where sno='.$row['room_id'];
		execute_query($sql);
		
	}
}


if(isset($_POST['submit'])){
	if($_POST['room_id']==''){
		$msg .= '<li class="error">Select Room.</li>';
	}
	if($_POST['cust_name1']==''){
		$msg .= '<li class="error">Enter Bill To Details.</li>';
	}
	if($msg==''){
		if($_POST['cust_sno']==''){
			$sql= 'INSERT INTO customer (company_name, cust_name, mobile, id_1, id_2, address, created_by, created_on) VALUES ("'.$_POST['company_name'].'", "'.$_POST['cust_name1'].'", "'.$_POST['mobile'].'", "'.$_POST['id_1'].'", "'.$_POST['id_2'].'", "'.$_POST['address'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP)';
			$result = execute_query($sql);
			$msg .= '<li class="error">Customer Added successfully</li>';
			$_POST['cust_sno'] = $con->insert_id;		
		}
		if($_POST['allot_sno']!=''){
			$sql='select * from allotment where sno='.$_POST['allot_sno'];
			$result = execute_query($sql);
			$row=mysqli_fetch_assoc( $result );
			$inv_no = $room['invoice_no'];
			$exit_date = $room['exit_date'];
			
			$sql = 'delete from allotment where sno='.$_POST['allot_sno'];
			$result = execute_query($sql);
			
			$sql_room='update room_master set status=NULL where sno='.$room['room_id'];
			$result = execute_query($sql_room);
			
			$sql='update customer set 
			company_name="'.$_POST['company_name'].'",
			cust_name="'.$_POST['cust_name1'].'", 
			mobile="'.$_POST['mobile'].'",
			id_2= "'.$_POST['id_2'].'",
			id_1="'.$_POST['id_1'].'",
			address="'.$_POST['address'].'",
			edited_by="'.$_SESSION['username'].'", 
			advance="'.$_POST['advance'].'", 
			edited_on=CURRENT_TIMESTAMP  
			where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
			
			$msg .= '<li class="error">Update successful.</li>';
			
		}
		else{
			$inv_no='';
			$exit_date='';
		}
			
		$con = $db;
		$date = $_POST['allotment_date'];
		$time = strtotime($date);
		$month = date("m",$time);
		$year = date("Y",$time);
		if($month>=1 && $month<=3){
			$year = $year-1;
		}

		foreach($_POST['room_id'] as $k => $v){
			if($_POST['net_room_rent_'.$v]>999){
				$inv_type = 'tax';
			}
			else{
				$inv_type = 'bill_of_supply';
			}
			/*
			$sql = 'select * from allotment where financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			$invoice_result = execute_query($sql);
			if(mysqli_num_rows($invoice_result)!=0){
				$invoice_no = mysqli_fetch_array($invoice_result);
				$_POST['invoice_no'] = $invoice_no['invoice_no']+1;
			}
			else{
				$_POST['invoice_no'] = 1;
			}*/
			if($_POST['allot_sno']!=''){
				$_POST['invoice_no'] = $inv_no;
				$edition_time = "CURRENT_TIMESTAMP";
				$edited_by = "'".$_SESSION['username']."'";
				$exit_date = "'".$exit_date."'";
			}
			else{
				$_POST['invoice_no']='0';
				$edition_time = "NULL";
				$edited_by = 'NULL';
				$exit_date = 'NULL';
			}
			
			$sql = 'select rent from room_master where sno='.$v; 
			$rent = mysqli_fetch_array(execute_query($sql));
			$sql = 'INSERT INTO allotment (cust_id, room_id , room_rent, discount, discount_value, original_room_rent, financial_year, invoice_no, invoice_type, allotment_date, exit_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on) VALUES ("'.$_POST['cust_sno'].'", "'.$v.'" , "'.$_POST['net_room_rent_'.$v].'", "'.$_POST['discount_'.$v].'", "'.$_POST['discount_value_'.$v].'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$inv_type.'", "'.$_POST['allotment_date'].'", '.$exit_date.', "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['other_charges'].'", '.$edited_by.', '.$edition_time.')';
			$result = execute_query($sql);
			$sql='select * from allotment where cust_id='.$_POST['cust_sno'].' and room_id='.$v;
			$result = execute_query($sql);
			$allotid=mysqli_fetch_assoc( $result );
			$sql='select * from room_master where sno='.$v;
			$roomdetails=mysqli_fetch_array(execute_query($sql));
			if($roomdetails['multiple']=="yes"){
				$sql='select sum(occupancy) as occupancy from allotment where room_id='.$v;
				$occupants=mysqli_fetch_array(execute_query($sql));
				if($occupants['occupancy']>=$roomdetails['occupancy']){
					$sql='update room_master set status="1" where sno="'.$v.'"';
					$result = execute_query($sql);
				}
			}
			else{
				$sql='update room_master set status="1" where sno="'.$v.'"';
				$result = execute_query($sql);
			}
			$msg .= '<li class="error">Room Alloted successfully</li>';
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from allotment where sno='.$_GET['id'];
	$result = execute_query($sql);
	$old_data=mysqli_fetch_assoc( $result );
	$total_rent=$old_data['room_rent']+$old_data['other_charges'];
	$sql = 'select * from customer_transactions where allotment_id='.$_GET['id'];
	$result = execute_query($sql);
	$rent_details=mysqli_fetch_assoc( $result );
	$sql = 'select * from customer where sno='.$old_data['cust_id'];
	$result = execute_query($sql);
	$cust_details=mysqli_fetch_assoc( $result );
}
else{
	$old_data['cust_id']='';
	$old_data['original_room_rent']='';
	$old_data['discount']='';
	$old_data['net_room_rent']='';
	$old_data['discount_value']='';
	$old_data['room_rent']='';
	$old_data['occupancy']='';
	$old_data['other_charges']='';
	$total_rent='';
	$cust_details['mobile']='';
	$cust_details['address']='';
	$cust_details['company_name']='';
}
if(isset($_GET['del'])){
	$sql='select * from allotment where sno='.$_GET['del'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
	$sql='update room_master set status=0 where sno='.$row['room_id'];
	$result = execute_query($sql);
	$sql='delete from customer_transactions where allotment_id='.$row['sno'];
	$result = execute_query($sql);
	$sql = 'delete from allotment where sno='.$_GET['del'];
	$result = execute_query($sql);
	
}
?>
<style>
.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>
<script src="js/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" language="javascript">
function get_rent(){
	var room_rent=document.getElementById("rent").value;
	var other=document.getElementById("other_charges").value;
	room_rent=parseFloat(room_rent);
	other=parseFloat(other);
	var total_rent=room_rent+ other;
	total_rent=parseFloat(total_rent);
	document.getElementById('total_rent').value=total_rent;
}
	
function get_room_rent(){
	val = $("#room_id").val();
	$.ajax({
		url: "scripts/ajax.php?id=rent_room&term="+val,
		dataType:"json"
	})
	.done(function( data ) {
		//data = data[0];
		var txt = '<table width="100%"><tr><th>Room Name</th><th>Occupancy</th><th>Base Rent</th><th>Discount</th><th>Taxable</th><th>CGST</th><th>SGST</th><th>Net Price</th>';
		var tot_rent = 0;
		$.each( data, function( index, value ) {
			var allot_sno = $("#allot_sno").val();
			var occupancy = $("#occupancy").val();
			if(allot_sno!=''){
				var selected_id=parseFloat(occupancy);
				var disc_value = $("#discount").val();
			}
			else{
				var selected_id='';
				var disc_value = '';
			}
			
			txt += '<tr><td>'+value.label+'</td><td><select name="occupancy_'+value.id+'" id="occupancy_'+value.id+'" onChange="calculate()" class="medium">';
			for(var i=1; i<=(parseFloat(value.occupancy)+1); i++){
				txt += '<option value="'+i+'" ';
				if(selected_id==i){
					txt += ' selected="selected"';
				}
				txt += '>'+i+'</option>';
			}
			txt += '</select><input type="hidden" name="occupancy_total_'+value.id+'" id="occupancy_total_'+value.id+'" value="'+value.occupancy+'"><input type="hidden" name="rent_single_'+value.id+'" id="rent_single_'+value.id+'" value="'+value.rent+'"><input type="hidden" name="rent_double_'+value.id+'" id="rent_double_'+value.id+'" value="'+value.rent_double+'"><input type="hidden" name="rent_extra_'+value.id+'" id="rent_extra_'+value.id+'" value="'+value.rent_extra+'">';
			txt += '</td><td><input type="text" name="room_'+value.id+'" id="room_'+value.id+'" value="'+value.rent+'" onBlur="calculate()"></td><td><input type="text" name="discount_'+value.id+'" id="discount_'+value.id+'" value="'+disc_value+'" onBlur="calculate()"><input type="hidden" name="discount_value_'+value.id+'" id="discount_value_'+value.id+'" value="" onBlur="calculate()"></td><td id="taxable_'+value.id+'"></td><td id="cgst_'+value.id+'"></td><td id="sgst_'+value.id+'"></td><td><input type="text" name="net_room_rent_'+value.id+'" id="net_room_rent_'+value.id+'" value="" onBlur="calculate()"></td></tr>';
			tot_rent += parseFloat(value.rent);
		});
		txt += '</table>';
		$("td#insertrow").html(txt);
		document.getElementById('rent').value = tot_rent;
		calculate();
	});			  
}

function calculate(){
	var tot_selected = $("#room_id").val();
	if(!Array.isArray(tot_selected)){
		var tot_selected = [tot_selected];
	}
	var tot_rent = 0;
	var total_discount=0;
	var net_rate=0;
	$.each(tot_selected, function(index, value){
		var occupancy = parseFloat($("#occupancy_"+value).val());
		if(!occupancy){
			occupancy = 0;
		}
		var occupancy_hidden = parseFloat($("#occupancy_total_"+value).val());
		if(!occupancy_hidden){
			occupancy_hidden = 0;
		}
		var rent = parseFloat($("#room_"+value).val());
		if(!rent){
			rent = 0;
		}
		var rent_single = parseFloat($("#rent_single_"+value).val());
		if(!rent_single){
			rent_single = 0;
		}
		var rent_double = parseFloat($("#rent_double_"+value).val());
		if(!rent_double){
			rent_double = 0;
		}
		var rent_extra = $("#rent_extra_"+value).val();
		rent_extra = parseFloat(rent_extra.replace("%",""));
		if(!rent_extra){
			rent_extra = 0;
		}
		var temp_rent_extra = rent_double + rent_double*rent_extra/100;
		//console.log(occupancy+'-'+occupancy_hidden+'-'+rent+'-'+rent_single+'-'+rent_double);
		if(rent!=rent_single && rent!=rent_double && rent!=temp_rent_extra){
			//console.log('cond0');
		}
		else{
			if(occupancy>1 && occupancy<=occupancy_hidden){
				rent = rent_double;
				//console.log('cond1');
			}
			else if(occupancy>occupancy_hidden){
				rent_extra = rent_double*rent_extra/100;
				rent = rent_double + rent_extra;
				//console.log('cond2');
			}
			else if(occupancy==1){
				rent = rent_single;
				//console.log('cond3');
			}
		}
		
		var discount = $("#discount_"+value).val();
		if(discount.search('%')==-1){
			var discount_value = parseFloat(discount);
		}
		else{
			discount = discount.replace("%","");
			var discount_value = Math.round(((rent * discount)/100)*100)/100;
		}
		if(!discount_value){
			discount_value=0;
		}
		var taxable = rent - discount_value;
		if(!taxable){
			taxable=0;
		}
		if(taxable>999){
			var cgst = taxable*6/100;
			var sgst = taxable*6/100;
			net_rent = taxable+cgst+sgst;
			
		}
		else{
			var cgst = 0;
			var sgst = 0;
			net_rent = taxable+cgst+sgst;
		}
		$("#room_"+value).val(rent);
		$("#discount_value_"+value).val(discount_value);
		$("#taxable_"+value).html(taxable);
		$("#cgst_"+value).html(cgst);
		$("#sgst_"+value).html(sgst);
		$("#net_room_rent_"+value).val(net_rent);
		//console.log(rent);
		tot_rent += net_rent;
	});
	if(!tot_rent){
		tot_rent=0;
	}
	$("#rent").val(tot_rent);
}	

function calc(){
	var rent = parseFloat($("#rent").val());
	if(!rent){
		rent=0;
	}
	var discount = $("#discount").val();
	if(discount.search("%")==-1){
		discount = parseFloat(discount);
		if(!discount){
			discount = 0;
		}
		var net_rent = rent-discount;
		var discount_value = discount;
	}
	else{
		discount.replace("%","");
		discount = parseFloat(discount);
		if(!discount){
			discount = 0;
		}
		var discount_value = Math.round((rent*discount/100)*100)/100;
		var net_rent = rent-discount_value;
	}
	if(!net_rent){
		net_rent=0;
	}
	$(".net_room_rent").html(net_rent);
	$("#net_room_rent").val(net_rent);
}
	
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
		    $("[name='cust_name']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$('#cust_name1').val(ui.item.cust_name);
			$('#mobile').val(ui.item.mobile);
			$('#company_name').val(ui.item.company);
			$('#address').val(ui.item.address);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name1").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
	
</script>
 <div id="container">
        <h2>New Allotment</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="allotment.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr><td>Company Name</td><td><input id="cust_name1" name="cust_name1" value="<?php if(isset($row1['cust_name'])){echo $row1['cust_name'];} else if(isset($_GET['id'])){ echo $cust_details['cust_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>"></td></tr>
				<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_GET['alt'])){echo $_GET['alt'];} else{echo $old_data['cust_id'];}?>" />
				<tr>
					<td>Gust Name</td>
					<td><input id="company_name" name="company_name" value="<?php if(isset($row1['company_name'])){echo $row1['company_name'];} else if(isset($_GET['id'])){ echo $cust_details['company_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					
					<td>Mobile</td>
					<td><input id="mobile" name="mobile" value="<?php if(isset($row1['mobile'])){echo $row1['mobile'];} else { echo $cust_details['mobile'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
					<tr>
					<td>ID Card No.</td>
					<td><input id="id_1" name="id_1" value="<?php if(isset($row1['id_1'])){echo $row1['id_1'];} else if(isset($_GET['id'])){ echo $cust_details['id_1'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Address</td>
					<td><input id="address" name="address" value="<?php if(isset($row1['address'])){echo $row1['address'];} else { echo $cust_details['address'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<tr>
					<td>GSTIN</td>
					<td><input id="id_2" name="id_2" value="<?php if(isset($row1['id_2'])){echo $row1['id_2'];} else if(isset($_GET['id'])){ echo $cust_details['id_2'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td></td><td></td>
				</tr>
				<tr>
					<td>Room Name</td>
					<td>	
						<table width="100%">
							<tr style="background: #ccc">
								<td width="90%">
									<select name="room_id[]" id="room_id" tabindex="<?php echo $tab++;?>" class="room_id" <?php if(!isset($_GET['id'])){echo 'multiple="multiple"';}?> onBlur="get_room_rent();" >
									<?php
										$sql = 'select * from room_master order by abs(room_name)';
										$result = execute_query($sql);
										while($row_room = mysqli_fetch_array($result)){
											if($row_room['status']==1){
												if(isset($old_data['room_id'])){
													if($old_data['room_id']==$row_room['sno']){
														echo '<option value="'.$row_room['sno'].'" ';
														echo 'selected="selected"';
														echo '>'.$row_room['room_name'].'</option>';
													}
												}
											}
											else{
												if(isset($old_data['room_id'])){
													if($old_data['room_id']==$row_room['sno']){
														echo '<option value="'.$row_room['sno'].'" ';
														echo 'selected="selected"';
														echo '>'.$row_room['room_name'].'</option>';
													}
													else{
														echo '<option value="'.$row_room['sno'].'" ';
														echo '>'.$row_room['room_name'].'</option>';
													}
												}
												else{
													echo '<option value="'.$row_room['sno'].'" ';
													echo '>'.$row_room['room_name'].'</option>';
												}
											}
										}
									?>
									</select>
									<input type="hidden" name="room_sno" id="room_sno" value="" />
								</td>
								<td><input type="button" onClick="get_room_rent();" style="float: right;" class="small" value="Fetch"></td>
							</tr>
						</table>
					</td>
                    <input type="hidden" name="room_sno" id="room_sno" value="" />
					<td>Allotment Date</td>
					<td><input name="allotment_date" type="text" value="<?php if(isset($old_data['allotment_date'])){echo $old_data['allotment_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="allotment_date" /></td>
				</tr>
                <tr>
                	<td>Rent of Room</td>
					<td><input id="rent" name="rent" value="<?php echo $old_data['original_room_rent'] ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
                	
                	<?php if(isset($_GET['id'])){ ?>
               		<td>Discount</td>
					<td><input id="discount" name="discount" value="<?php echo $old_data['discount'] ?>" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" onBlur="calc();" />Net Rate : <span class="net_room_rent"><?php echo $old_data['room_rent'] ?></span><input type="hidden" name="net_room_rent" id="net_room_rent" value="<?php echo $old_data['room_rent'] ?>"><input type="hidden" name="discount_value" id="discount_value" value="<?php echo $old_data['discount_value'] ?>"></td>
					<?php } else {?>
               		<td>Discount</td>
					<td><input id="discount" name="discount" value="<?php echo $old_data['discount'] ?>" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" type="hidden" onBlur="calc();" />Net Rate : <span class="net_room_rent"><?php echo $old_data['room_rent'] ?></span><input type="hidden" name="net_room_rent" id="net_room_rent" value="<?php echo $old_data['room_rent'] ?>"><input type="hidden" name="discount_value" id="discount_value" value="<?php echo $old_data['discount_value'] ?>"></td>
					<?php } ?>
				</tr>
               <tr>
                	<td>Occupancy</td>
					<td><input id="occupancy" name="occupancy" value="<?php echo $old_data['occupancy'] ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text"  /></td>               
					<td>Other Charges</td>
					<td><input id="other_charges" name="other_charges" value="<?php echo $old_data['other_charges']?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" onBlur="get_rent();" /></td>
                </tr>
                <tr>
					<td>Total Rent</td>
					<td><input id="total_rent" name="total_rent" value="<?php echo $total_rent?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Remarks</td>
					<td>
						<input id="remarks" name="remarks" value="<?php if(isset($old_data['remarks'])){echo $old_data['remarks'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
				</tr>
				
				<tr>
					<td colspan="4" id="insertrow"></td>
				</tr>
				<tr>
					<td colspan="4"><input type="hidden" name="allot_sno" id="allot_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Allot Room" onMouseDown="" tabindex="<?php echo $tab++;?>"></td>
				</tr>
			</table>
		</form>
		<table width="100%">
			<tr style="background:#000; color:#FFF;">
				<th>S.No.</th>
				<th>Company Name</th>
				<th>Gust Name</th>
				<th>Occupancy</th>
				<th>Room Name</th>
				<th>Inv No.</th>
				<th>Total Rent</th>
				
				<th>Allotment Date</th>
				<th></th>
				<th></th>
				<th></th>
                <th></th>
			</tr>
    <?php
	$sql = 'select * from allotment where exit_date is null or exit_date=""';
	$result=mysqli_fetch_assoc(execute_query($sql));
	$i=1;
	foreach($result as $row){
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
		if($row['exit_date']==''){
			$row['exit_date'] = date("d-m-Y H:i");
		}
		$days = (strtotime($row['exit_date'])-strtotime($row['allotment_date']));
		$days = date("d", $days);
		$total_rent=($row['room_rent']+$row['other_charges'])*$days;
		if($row['cancel_date']!=''){
			$col = '#F00"';
			$cancel = '<br />Cancelled On : '.$row['cancel_date'];
			$cancel_display = 'Uncancel';
		}
		else{
			$row_col = '';
			$cancel = '';
			$cancel_display = 'Cancel';
		}
		echo '<tr style="background:'.$col.'; text-align:center;">
		<td>'.$i++.'</td>
		<td>'.strtoupper(get_cust_name($row['cust_id'])).$cancel.'</td>
		<td>'.strtoupper(get_company_name($row['cust_id'])).'</td>
		<td>'.$row['occupancy'].'</td>
		<td>'.get_room($row['room_id']).'</td>
		<td>'.$row['invoice_no'].'</td>
		<td>'.$total_rent.'</td>
		
		<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
		<td><a href="allotment.php?id='.$row['sno'].'">Edit</a></td>
		<td class="no-print"><a href="allotment.php?cancel='.$row['sno'].'">'.$cancel_display.'</a></td>
		<td><!--<a href="allotment.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a>--></td>
	</tr>';
	}
?>
</table>
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#allotment_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['id'])){
		echo $old_data['allotment_date'];
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

$(document).ready(function(){
	get_room_rent();
});
</script>

<?php
page_footer();
?>
