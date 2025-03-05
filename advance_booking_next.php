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
if(isset($_POST['submit'])){
	if($_POST['room_id']==''){
		$msg .= '<li class="error">Select Room.</li>';
	}
	if($_POST['company_name'] =='' AND $_POST['cust_name1'] == ''){
		$msg .= '<li class="error">Enter Bill To Details.</li>';
	}
	if($msg==''){
		if($_POST['cust_sno']==''){
			$sql= 'INSERT INTO customer (company_name, cust_name, mobile, id_1, id_2, address, created_by, created_on , state) VALUES ("'.$_POST['company_name'].'", "'.$_POST['cust_name1'].'", "'.$_POST['mobile'].'", "'.$_POST['id_1'].'", "'.$_POST['id_2'].'", "'.$_POST['address'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "UTTAR PRADESH")';
			$result = execute_query($sql);
			$msg .= '<li class="error">Customer Added successfully</li>';
			$_POST['cust_sno'] = $con->insert_id;		
		}
		if($_POST['cust_sno']!=''){
			$sql='update customer set 
			cust_name="'.$_POST['cust_name1'].'"
			where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
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
      $i=0;
      $n = 1;
      		$no_of_room = count($_POST['room_id']);
      		foreach($_POST['room_id'] as $k => $v){
      			$room_id .= $v;
      			if($n < $no_of_room){
      				$room_id .= ',';
      				$n++;
      			}
      		}
			$no_of_room = count($_POST['room_id']);
			//echo count($_POST['room_id']).'<br/>';
			$balance = round($_POST['advance_amount'] / $no_of_room);
			$sql = 'INSERT INTO advance_booking (guest_name, cust_id, room_id , financial_year, allotment_date , created_by , created_on, remarks, status,advance_amount) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$room_id.'" , "'.$year.'", "'.$_POST['allotment_date'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'", "0" ,"'.$_POST['advance_amount'].'")';
			$result = execute_query($sql);
			$sql="select * from advance_booking order by sno desc limit 1";
			$result = execute_query($sql);
			$row=mysqli_fetch_assoc( $result );
			$alot_id=$old_data1['sno'];
			if($_POST['advance_amount'] > 0){
				$sql='INSERT INTO customer_transactions (cust_id , advance_booking_id , type , timestamp, amount, mop, created_by , created_on , remarks , invoice_no , financial_year , payment_for) VALUES ("'.$_POST['cust_sno'].'", "'.$alot_id.'" , "ADVANCE_RENT" , "'.date('Y-m-d').'"  , "'.$_POST['advance_amount'].'" , "'.$_POST['mop'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'","","'.$year.'" , "advance")';
				$result = execute_query($sql);
			}
			$msg .= '<li class="error">Room Alloted successfully</li>';
	}
		
}
if(isset($_GET['del'])){
	$sql='select * from allotment_2 where sno='.$_GET['del'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );

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
	var extrabed=document.getElementById("extrabed").value;
	room_rent=parseFloat(room_rent);
	extrabed=parseFloat(extrabed);
	var total_rent=room_rent+extrabed;
	total_rent=parseFloat(total_rent);
	document.getElementById('total_rent').value=total_rent;
}
	
function get_room_rent(){
	val = $("#room_id").val();
	allot = $("#allot_sno").val();
	$.ajax({
		url: "scripts/ajax.php?id=rent_room&allot="+allot+"&term="+val,
		dataType:"json"
	})
	.done(function(data) {
		var txt = '<table width="100%"><tr><th>Room No.</th><th>Occupancy</th><th>Base Rent</th><th>Extra Bed</th><th >Taxable</th><th>CGST</th><th>SGST</th><th>Net Price</th>';
		var tot_rent = 0;
		$.each( data, function( index, value ) {
			var allot_sno = $("#allot_sno").val();
			var occupancy = $("#occupancy").val();
			if(allot_sno !=''){
				var selected_id=parseFloat(occupancy);
				var disc_value = $("#discount").val();
				//var extrabed = $("#extrabed").val();
				
			}
			else{
				var selected_id='';
				var disc_value='';
				var extrabed='';
				
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
			txt += '</td><td><input type="text" name="room_'+value.id+'" id="room_'+value.id+'" value="'+value.rent+'" onBlur="calculate()"></td><td><input type="text" name="extrabed_'+value.id+'" id="extrabed_'+value.id+'" value="'+value.extra_bed+'" onBlur="calculate()"> <input type="hidden" name="discount_'+value.id+'" id="discount_'+value.id+'" value="'+disc_value+'" onBlur="calculate()"><input type="hidden" name="discount_value_'+value.id+'" id="discount_value_'+value.id+'" value="" onBlur="calculate()"></td><td id="taxable_'+value.id+'"></td><td id="cgst_'+value.id+'"></td><td id="sgst_'+value.id+'"></td><td><input type="text" name="net_room_rent_'+value.id+'" id="net_room_rent_'+value.id+'" value="" onBlur="calculate()"></td></tr>';
			tot_rent += parseFloat(value.rent);
		});
		txt += '</table>';
		$("td#insertrow").html(txt);
		document.getElementById('rent').value = tot_rent;
		calculate();
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
		if(!!value){
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
			//alert("#rent_extra_"+value);
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
				if(discount == 1){
					var discount_value = Math.round(((rent * 10)/100)*100)/100;
				}
				if(discount == 2){
					var discount_value = Math.round(((rent * 20)/100)*100)/100;
				}
				if(discount == 3){
					var discount_value = Math.round(((rent * 30)/100)*100)/100;
				}
				if(discount == 4){
					var discount_value = Math.round(((rent * 40)/100)*100)/100;
				}
				if(discount == 5){
					var discount_value = Math.round(((rent * 50)/100)*100)/100;
				}
				if(discount == 6){
					var discount_value = Math.round(((rent * 60)/100)*100)/100;
				}
				if(discount == 7){
					var discount_value = Math.round(((rent * 70)/100)*100)/100;
				}
				if(discount == 8){
					var discount_value = Math.round(((rent * 80)/100)*100)/100;
				}
				if(discount == 9){
					var discount_value = Math.round(((rent * 90)/100)*100)/100;
				}
				if(discount == 10){
					var discount_value = Math.round(((rent * 100)/100)*100)/100;
				}
				if(discount > 10){
					var discount_value = parseFloat(discount);
				}
			}
			else{
				discount = discount.replace("%","");
				var discount_value = Math.round(((rent * discount)/100)*100)/100;
			}
			/*if(discount.search('%')==-1){
				var discount_value = parseFloat(discount);
			}
			else{
				discount = discount.replace("%","");
				var discount_value = Math.round(((rent * discount)/100)*100)/100;
			}*/
			if(!discount_value){
				discount_value=0;
			}
			
			var extrabed = $("#extrabed_"+value).val();
			//alert(extrabed);
			if(extrabed.search('%')==-1){
				if(extrabed == 1){
					var extrabed_value = Math.round(((rent * 25)/100)*100)/100;	
				}
				if(extrabed == 2){
					var extrabed_value = Math.round(((rent * 50)/100)*100)/100;	
				}
				if(extrabed == 3){
					var extrabed_value = Math.round(((rent * 75)/100)*100)/100;	
				}
				if(extrabed == 4){
					var extrabed_value = Math.round(((rent * 100)/100)*100)/100;	
				}
				if(extrabed > 4){
					var extrabed_value = parseFloat(extrabed);	
				}
			}
			else{
				extrabed = extrabed.replace("%","");
				var extrabed_value = Math.round(((rent * extrabed)/100)*100)/100;
				//var extrabed_value = parseInt(extrabed);
				//alert(extrabed_value);
			}
			/*if(extrabed.search('%')==-1){
				var extrabed_value = parseFloat(extrabed);
				//alert(extrabed_value);
			}
			else{
				extrabed = extrabed.replace("%","");
				var extrabed_value = Math.round(((rent * extrabed)/100)*100)/100;
				//var extrabed_value = parseInt(extrabed);
				//alert(extrabed_value);
			}*/
			if(!extrabed){
				extrabed_value='';
			}			
			var taxable = rent + extrabed_value - discount_value;
			if(!taxable){
				taxable=0;
			}
			if(taxable>999){
				var cgst = taxable*6/100;
				var sgst = taxable*6/100;
				net_rent =  Math.round(taxable+cgst+sgst);	
			}
			else{
				var cgst = 0;
				var sgst = 0;
				net_rent =  Math.round(taxable+cgst+sgst);
			}
			$("#room_"+value).val(rent);
			$("#discount_"+value).val(discount_value);
			$("#discount_value_"+value).val(discount_value);
			$("#extrabed_"+value).val(extrabed_value);
			$("#taxable_"+value).html(taxable);
			$("#cgst_"+value).html(cgst);
			$("#sgst_"+value).html(sgst);
			$("#net_room_rent_"+value).val(net_rent);
			//console.log(rent);
			tot_rent += net_rent;
		}
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
var extrabed = $("#extrabed").val();
	if(extrabed.search("%")==-1){
		extrabed = parseFloat(extrabed);
		if(!extrabed){
			extrabed = '';
		}
		var net_rent = rent+extrabed;
	}
		else{
		extrabed.replace("%","");
		extrabed = parseFloat(extrabed);
		if(!extrabed){
			extrabed = '';
		}
		
		var net_rent = rent+extrabed;
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
		    $("[name='company']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$('#cust_name1').val(ui.item.cust_name);
			$('#mobile').val(ui.item.mobile);
			$('#company_name').val(ui.item.company);
			$('#address').val(ui.item.address);
			$('#id_1').val(ui.item.id_no);
			$('#id_2').val(ui.item.gst_no);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name1").on("keydown.autocomplete", function() {
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
			$('#cust_sno').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			$('#cust_name1').val(ui.item.cust_name);
			$('#company_name').val(ui.item.company);
			$('#address').val(ui.item.address);
			$('#id_1').val(ui.item.id_no);
			$('#id_2').val(ui.item.gst_no);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#company_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
	
</script>
 <div id="container">
        <h2>Advance Booking</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="advance_booking.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr><td>Guest Name</td><td><input id="cust_name1" name="cust_name1" value="<?php if(isset($row1['cust_name'])){echo $row1['cust_name'];} else if(isset($_GET['id'])){ echo $cust_details['cust_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>"></td></tr>
				<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_GET['alt'])){echo $_GET['alt'];} else{echo $old_data['cust_id'];}?>" />
				<tr>
					<td>Company Name</td>
					<td><input id="company_name" name="company_name" value="<?php if(isset($row1['company_name'])){echo $row1['company_name'];} else if(isset($_GET['id'])){ echo $cust_details['company_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					
					<td>Mobile</td>
					<td><input id="mobile" name="mobile" value="<?php if(isset($row1['mobile'])){echo $row1['mobile'];} else { echo $cust_details['mobile'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
					<tr>
					<td>SAC/HSN</td>
					<td><input id="id_1" name="id_1" value="<?php if(isset($row1['id_1'])){echo $row1['id_1'];} else if(isset($_GET['id'])){ echo $cust_details['id_1'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Address</td>
					<td><input id="address" name="address" value="<?php if(isset($row1['address'])){echo $row1['address'];} else { echo $cust_details['address'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<tr>
					<td>GSTIN</td>
					<td><input id="id_2" name="id_2" value="<?php if(isset($row1['id_2'])){echo $row1['id_2'];} else if(isset($_GET['id'])){ echo $cust_details['id_2'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Plan</td>
					<td>
						<select name="plan">
							<option value="">Select</option>
							<?php
								$sql="SELECT * FROM admin_plans";
								$res=execute_query($sql);
								while($plan=mysqli_fetch_array($res)){
									echo'<option  value="'.$plan['plan_name'].'"';
						        		if(isset($_GET['id'])){
											if($old_data['plans']==$plan['plan_name']){
												echo 'selected="selected"';
											}
										}
						        		echo'>'.$plan['plan_name'].'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Room No.</td>
					<td>	
						<table width="100%">
							<tr style="background: #ccc">
								<td width="90%">
									<select required name="room_id[]" id="room_id" tabindex="<?php echo $tab++;?>" class="room_id" <?php if(!isset($_GET['id'])){echo 'multiple="multiple"';}?> onBlur="get_room_rent();" >
									<?php
										$sql = 'select * from room_master order by abs(room_name)';
										$result = execute_query($sql);
										while($row_room = mysqli_fetch_array($result)){
											echo '<option value="'.$row_room['sno'].'" ';
											echo '>'.$row_room['room_name'].'</option>';
										}
									?>
									</select>
								</td>
								<?php //echo $l; ?>
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
					<td><input id="discount" name="discount"  class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" type="checkbox" onBlur="calc();" value=""<?php if($old_data['other_discount'] !='' && $old_data['other_discount'] !=0){
						echo 'checked';
					}else{
						echo 'unchecked';
					} ?> />Net Rate : <span class="net_room_rent"><?php echo $old_data['other_discount'] ?></span><input type="hidden" name="net_room_rent" id="net_room_rent" value="<?php echo $old_data['room_rent'] ?>"><input type="hidden" name="discount1" id="discount_value" value="<?php echo $old_data['discount_value'] ?>"></td>
					<?php } else {?>
               		<td>Discount</td>
					<td><input id="discount" name="discount" value="<?php echo $old_data['discount'] ?>" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" type="hidden" onBlur="calc();" />Net Rate : <span class="net_room_rent"><?php echo $old_data['room_rent'] ?></span><input type="hidden" name="net_room_rent" id="net_room_rent" value="<?php echo $old_data['room_rent'] ?>"><input type="hidden" name="discount_value" id="discount_value" value="<?php echo $old_data['discount_value'] ?>"></td>
					<?php } ?>
				</tr>
               <tr>
                	<td>Occupancy</td>
					<td><input id="occupancy" name="occupancy" value="<?php echo $old_data['occupancy'] ?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text"  <?php if(!isset($_GET['id'])){ echo 'readonly'; } ?> /></td>  
					<td>Extra Bed</td>
					<?php if(isset($_GET['id'])){ ?>
					<td><input id="extrabed" name="extrabed" value="<?php echo $old_data['other_charges'] ?>" class="field text medium" maxlength="255" onBlur="calc();" tabindex="<?php echo $tab++;?>" type="text" readonly /></td>  
					<?php } else {?>  
					
					<td><input id="extrabed" name="extrabed" value="<?php echo $old_data['other_charges'] ?>" class="field text medium" maxlength="255" onBlur="calc();" tabindex="<?php echo $tab++;?>" type="hidden"  /></td>  
					<?php } ?>              
					
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
					<td>Advance Amount</td>
					<td><input id="advance_amount" name="advance_amount" value="" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Mode of Payment</td>
				   	<td>
				   		<select id="mop" name="mop" class="field select medium" tabindex="<?php echo $tab++;?>">
						   	<option value="cash">Cash</option>
						   	<option value="card">Card</option>
				    	</select>
				    </td>
				</tr>
				<?php 
					if(isset($_GET['aid'])){
						?>
				<tr>
					<td>Exit Date</td>
					<td><input name="exit_date" type="text" value="<?php if(isset($old_data['exit_date'])){echo $old_data['exit_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="exit_date" /></td>
					<td></td>
					<td></td>
				</tr>
						<?php
					}
				?>
				<input type="hidden" name="edit_room_sno" id="" value="<?php
                  $cut_discount='select * from allotment where sno="'.$_GET['id'].'"';
			//echo $cut_discount;
				$cut_dis_run=execute_query($cut_discount);
				$cut_row=mysqli_fetch_array($cut_dis_run); 
                 echo $cut_row['other_discount'];
				 ?>" />
				<!--<tr>
					<td>Adults </td>
					<td><input id="adults" name="adults" value="<?php// if(isset($old_data['no_of_male'])){echo $old_data['no_of_male'];}?>" class="field text medium" maxlength="255" tabindex="<?php// echo $tab++;?>" type="text" /></td>
					<td>Children</td>
					<td><input id="children" name="children" value="<?php //if(isset($old_data['no_of_kids'])){echo $old_data['no_of_kids'];}?>" class="field text medium" maxlength="255" tabindex="<?php// echo $tab++;?>" type="text" />
				</tr>-->
				<tr>
					<td colspan="4" id="insertrow"></td>
				</tr>
				<tr>
					<td colspan="4"><input type="hidden" name="allot_sno" id="allot_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Allot Room" onMouseDown="" tabindex="<?php echo $tab++;?>"></td>
					<input type="hidden" name="flage" id="" value="<?php if(isset($_GET['f'])){echo $_GET['f'];}?>" />
					<input type="hidden" name="allot2_sno"  value="<?php if(isset($_GET['aid'])){echo $_GET['aid'];}?>" />
				</tr>

			</table>
		</form>
		<table width="100%">
			<tr style="background:#000; color:#FFF;">
				<th>S.No.</th>
				<th>Guest Name</th>
				<th>Company Name</th>
				<th>Room No.</th>
				<th>Advance Amount</th>
				<th>Allotment Date</th>
				<th></th>
				<th></th>
				<!--<th></th>-->
                <th></th>
			</tr>	
    <?php
	$sql = 'select * from advance_booking where status="0"';
	$result=mysqli_fetch_assoc(execute_query($sql));
	$i=1;
	foreach($result as $row){
		if($i%2==0){
			$col = '#CCC';
		}
		elseif($row['hold_date']!=''){
			$col = 'red';
		}
		else{
			$col = '#EEE';
		}
		if($row['exit_date']==''){
			$row['exit_date'] = date("d-m-Y H:i");
		}
		$days = (strtotime($row['exit_date'])-strtotime($row['allotment_date']));
		$days = date("d", $days);
		$total_rent=($row['room_rent'])*$days;
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
		<td>'.$row['guest_name'].$cancel.'</td>
		<td>'. get_company_name($row['cust_id']).'</td>
		<td>'.get_room_advance($row['room_id']).'</td>
		<td>'.$row['advance_amount'].'</td>
		
		<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
		<td><a href="allotment.php?check_in='.$row['sno'].'">Check In</a></td>';
		/**if($row['hold_date']==''){
		echo '<td class="no-print"><a href="advance_booking.php?hold='.$row['sno'].'&room_id='.$row['room_id'].'">Hold</a></td>';
		}
		else{
			echo '<td>On Hold</td>';
		}**/
		echo '<!--<td class="no-print"><a href="advance_booking.php?cancel='.$row['sno'].'">'.$cancel_display.'</a></td>-->';
		echo '<td><!--<a href="advance_booking.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a>--></td>
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

$(document).ready(function(){
	get_room_rent();
});
</script>

<?php
page_footer();
?>