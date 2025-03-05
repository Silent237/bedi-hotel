<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
navigation('');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$tab=1;
$con = $db;
if(isset($_GET['alt'])){
	$sql = 'select * from customer where sno='.$_GET['alt'];
	$result = execute_query($sql);
	$row1=mysqli_fetch_assoc( $result );
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

		$sql = 'update allotment_2 set cancel_date=CURRENT_TIMESTAMP where sno='.$_GET['cancel'];
		execute_query($sql);

		//$sql = 'update room_master set status=NULL where sno='.$row['room_id'];
		//execute_query($sql);
	}
	else{
		$sql = 'update allotment set cancel_date=NULL where sno='.$_GET['cancel'];
		execute_query($sql);
		//$sql = 'update room_master set status=1 where sno='.$row['room_id'];
		//echo $sql;
		//execute_query($sql);
		
	}
}
if(isset($_POST['submit'])){
	if($_POST['room_id']==''){
		$msg .= '<li class="error">Select Room.</li>';
	}
	if($_POST['cust_name1']==''){
		//$msg .= '<li class="error">Enter Bill To Details.</li>';
	}
	if($msg==''){
		if($_POST['cust_sno']=='' OR isset($_POST['new_user'])){
			$stmt= 'INSERT INTO customer (company_name, cust_name, mobile, id_1, id_2, id_3, id_type, address, created_by, created_on , state, city, zipcode) VALUES ("'.$_POST['company_name'].'", "'.$_POST['cust_name1'].'", "'.$_POST['mobile'].'", "'.$_POST['id_1'].'", "'.$_POST['id_2'].'", "'.$_POST['id_3'].'", "'.$_POST['id_type'].'", "'.$_POST['address'].'", "'.$_SESSION['username'].'" ,"'.date('Y-m-d H:i:s').'" , "'.$_POST['state'].'", "'.$_POST['city'].'", "'.$_POST['zipcode'].'")';
			execute_query($stmt);
			$msg .= '<li class="error">Customer Added successfully</li>';
			$_POST['cust_sno'] = $con->insert_id;		
		}
		else{
			$sql='update customer set 
			company_name="'.$_POST['company_name'].'",
			cust_name="'.$_POST['cust_name1'].'", 
			mobile="'.$_POST['mobile'].'",
			state="'.$_POST['state'].'",
			city="'.$_POST['city'].'",
			zipcode="'.$_POST['zipcode'].'",
			id_type= "'.$_POST['id_type'].'",
			id_3= "'.$_POST['id_3'].'",
			id_2= "'.$_POST['id_2'].'",
			id_1="'.$_POST['id_1'].'",
			address="'.$_POST['address'].'",
			edited_by="'.$_SESSION['username'].'", 
			edited_on="'.date('Y-m-d H:i:s').'"  
			where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
		}
		if($_POST['allot2_sno'] !=''){
			$sql='select * from allotment_2 where allotment_id="'.$_POST['allot_sno'].'" order by allotment_id desc limit 1';
			$result = execute_query($sql);
			$room=mysqli_fetch_assoc( $result );
			$inv_no = $room['invoice_no'];
			$exit_date = $room['exit_date'];			
			$msg .= '<li class="error">Update successful.</li>';			
		}
		else if($_POST['id'] !=''){
			$sql='select * from allotment_2 where allotment_id="'.$_POST['id'].'" order by allotment_id desc limit 1';
			$result = execute_query($sql);
			$room=mysqli_fetch_assoc( $result );
			$inv_no = $room['invoice_no'];
			$exit_date = $room['exit_date'];
			$msg .= '<li class="error">Update successful.</li>';			
		}
		else if($_POST['allot_sno'] !=''  && $_POST['flage'] !=''){
			$sql='select * from allotment where sno='.$_POST['allot_sno'];
			$result = execute_query($sql);
			$room=mysqli_fetch_assoc( $result );
			$inv_no = $room['invoice_no'];
			$exit_date = $room['exit_date'];
			$sql = 'delete from allotment where sno='.$_POST['allot_sno'];
			
			$result = execute_query($sql);
			
			$sql = 'delete from allotment_2 where allotment_id='.$_POST['allot_sno'];
			$result = execute_query($sql);

			$sql_room='update room_master set status=NULL where sno='.$room['room_id'];
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
        $i=0;
		//print_r($_POST);
		foreach($_POST['room_id'] as $k => $v){
			$without=$_POST['room_'.$v.''];
			if(isset($_POST['discount_'.$v])){
				 $dis=$without * $_POST['discount_'.$v] /100;
			}
			else{
				 $dis=0;
			}
			$dis = $_POST['discount_value_'.$v];
			if($_POST['edit_room_sno'] !=''){
				$taxable_amt=$_POST['room_'.$v] + $_POST['extrabed_'.$v]-$dis-$_POST['other_discount'];
			}
			else{
				$taxable_amt=$_POST['room_'.$v] + $_POST['extrabed_'.$v]-$dis-$_POST['other_discount'];
			}
			//echo $v.'$$'.$taxable_amt.'>>'.$_POST['extrabed_'.$v].'@@'.$dis.'>>'.$_POST['other_discount'].'##';
			if($taxable_amt > 100 && $taxable_amt<=7499){
				$tax_rate = '12';
				$inv_type = 'tax';
				
			}
			if($taxable_amt > 7499){
				$tax_rate = '18';
				$inv_type = 'tax';
				
			}
			else{
				$inv_type = 'bill_of_supply';
				$tax_rate = '0';
			}
			
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
			//$grand_tot=$_POST['net_room_rent_'.$v];
			
			 
			
			$sql = 'select rent from room_master where sno='.$v; 
			$rent = mysqli_fetch_array(execute_query($sql));
		    $tot=$taxable_amt*$tax_rate/100;
		    $grand_tot=$taxable_amt+$tot;
		   	//echo $taxable_amt.'>>'.$tot.'>>'. $grand_tot;
			if($_POST['allot2_sno'] !=''){
				$sql = 'select * from allotment_2 where allotment_id='.$_POST['allot2_sno'].' order by sno desc';
				$allot2 = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'INSERT INTO allotment_2 (guest_name, cust_id, room_id , room_rent, discount, discount_value, other_discount, original_room_rent, financial_year, invoice_no, registration_no, invoice_type, tax_rate, allotment_date, departure_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on,plans,taxable_amount,allotment_id,edit_status, reference) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$v.'" , "'.$grand_tot.'", "'.$_POST['discount_'.$v].'", "'.$_POST['discount_value_'.$v].'", "'.$dis.'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$_POST['registration_no'].'", "'.$inv_type.'", "'.$tax_rate.'",  "'.$_POST['allotment_date'].'", "'.$_POST['departure_date'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['extrabed_'.$v].'", '.$edited_by.', '.$edition_time.',"'.$_POST['plan'].'","'.$taxable_amt.'","'.$_POST['allot_sno'].'","1", "'.$_POST['admin_reference'].'")';
				$result = execute_query($sql);
			}
			else if($_POST['id'] !=''){
				//echo $taxable_amt.'a</br>'.$_POST['room_'.$v].'b</br>'.$_POST['extrabed_'.$v];

				$amount_2 = $_POST['room_'.$v]+$_POST['extrabed_'.$v] - ($_POST['discount_value_'.$v]+$_POST['other_discount']);
				$sql='UPDATE `allotment` SET 
				`guest_name`="'.$_POST['cust_name1'].'",
				`cust_id`="'.$_POST['cust_sno'].'",
				`room_id`= "'.$v.'",
				`room_rent`="'.$grand_tot.'", 
				`discount`= "'.$_POST['discount_'.$v].'",
				`discount_value`= "'.$_POST['discount_value_'.$v].'",
				`other_discount`= "'.$_POST['other_discount'].'",
				`original_room_rent`= "'.$_POST['room_'.$v].'", 
				`invoice_type`= "'.$inv_type.'",
				`tax_rate`= "'.$tax_rate.'",
				`allotment_date`=  "'.$_POST['allotment_date'].'",
				`departure_date`=  "'.$_POST['departure_date'].'",
				`remarks`= "'.$_POST['remarks'].'",
				`occupancy`="'.$_POST['occupancy_'.$v].'",
				`other_charges`= "'.$_POST['extrabed_'.$v].'",
				`edited_by`= '.$edited_by.', 
				`edited_on`='.$edition_time.',
				`taxable_amount`="'.$amount_2.'" ,
				`reference`="'.$_POST['admin_reference'].'",
				`exit_date`="'.$_POST['exit_date'].'"
				where sno="'.$_POST['id'].'"';
				$run=execute_query($sql);
				//echo $sql.'<Br><br>';
				if(mysqli_error($db)){
					$msg .= '<li class="error">Error # 1.02 : '.$sql.' >> '.mysqli_error($db).'</li>';
				}

			    if($run==true){

					$sql='UPDATE `allotment_2` SET 
					`guest_name`="'.$_POST['cust_name1'].'",
					`cust_id`="'.$_POST['cust_sno'].'",
					`room_id`= "'.$v.'",
					`room_rent`="'.$grand_tot.'", 
					`discount`= "'.$_POST['discount_'.$v].'",
					`discount_value`= "'.$_POST['discount_value_'.$v].'",
					`other_discount`= "'.$_POST['other_discount'].'",
					`original_room_rent`= "'.$_POST['room_'.$v].'", 
					`invoice_type`= "'.$inv_type.'",
					`tax_rate`= "'.$tax_rate.'",
					`allotment_date`=  "'.$_POST['allotment_date'].'",
					`departure_date`=  "'.$_POST['departure_date'].'",
					`remarks`= "'.$_POST['remarks'].'",
					`occupancy`="'.$_POST['occupancy_'.$v].'",
					`other_charges`= "'.$_POST['extrabed_'.$v].'",
					`edited_by`= '.$edited_by.', 
					`edited_on`='.$edition_time.',
					`taxable_amount`="'.$amount_2.'",
					`allotment_id`="'.$_POST['id'].'" , 
					`exit_date`="'.$_POST['exit_date'].'"
					where allotment_id="'.$_POST['id'].'"  AND `edit_status` IS NULL';
					//echo $sql;
					$run=execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<li class="error">Error # 1.02 : '.$sql.' >> '.mysqli_error($db).'</li>';
					}
					if($run==true){
						$days = get_days($_POST['allotment_date'] , $_POST['exit_date']);
						$balance = $grand_tot * $days;
						$sql='UPDATE `customer_transactions` set `cust_id`="'.$_POST['cust_sno'].'",`allotment_id`="'.$_POST['id'].'",`amount`="'.$balance.'" where allotment_id="'.$_POST['id'].'" AND `type`="RENT"';
						$run=execute_query($sql);
						if($run==true){
							echo '<script>window.location.href="allotment.php";</script>';
						}

					}
				}
			}
			
			else{
				
				$sql = 'select * from allotment where financial_year="'.$year.'" order by abs(registration_no) desc limit 1';
				$invoice_result = execute_query($sql);
				if(mysqli_num_rows($invoice_result)!=0){
					$invoice_no = mysqli_fetch_array($invoice_result);
					$_POST['registration_no'] = $invoice_no['registration_no']+1;

				}
				else{
					$_POST['registration_no'] = 1;
				}

				
				$sql = 'INSERT INTO allotment_2 (guest_name, cust_id, room_id , room_rent, discount, discount_value, original_room_rent, financial_year, invoice_no, registration_no, invoice_type, tax_rate, allotment_date, departure_date, exit_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on,plans,taxable_amount,allotment_id,edit_status, reference) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$v.'" , "'.$grand_tot.'", "'.$_POST['discount_'.$v].'", "'.$_POST['discount_value_'.$v].'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$_POST['registration_no'].'", "'.$inv_type.'", "'.$tax_rate.'",  "'.$_POST['allotment_date'].'", "'.$_POST['departure_date'].'", '.$exit_date.', "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['extrabed_'.$v].'", '.$edited_by.', '.$edition_time.',"'.$_POST['plan'].'","'.$taxable_amt.'","'.$_POST['allot_sno'].'","1", ,"'.$_POST['admin_reference'].'")';
				$result = execute_query($sql);
				$sql="select * from allotment order by sno desc limit 1";
				$result = execute_query($sql);
				$allot_id=mysqli_fetch_assoc( $result );
				$sql = 'INSERT INTO allotment_2 (guest_name, cust_id, room_id , room_rent, discount, discount_value, original_room_rent, financial_year, invoice_no, registration_no, invoice_type, tax_rate, allotment_date, departure_date, exit_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on,plans,taxable_amount,allotment_id, reference) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$v.'" , "'.$grand_tot.'", "'.$_POST['discount_'.$v].'", "'.$_POST['discount_value_'.$v].'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$_POST['registration_no'].'", "'.$inv_type.'", "'.$tax_rate.'",  "'.$_POST['allotment_date'].'", "'.$_POST['departure_date'].'", '.$exit_date.', "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['extrabed_'.$v].'", '.$edited_by.', '.$edition_time.',"'.$_POST['plan'].'","'.$taxable_amt.'","'.$alot_id.'", ,"'.$_POST['admin_reference'].'")';
				$result = execute_query($sql);
			}
		
			
			$sql='select * from allotment where cust_id='.$_POST['cust_sno'].' and room_id='.$v;
			$result = execute_query($sql);
			$allotid=mysqli_fetch_assoc( $result );
			$sql='select * from room_master where sno='.$v;
			$roomdetails=mysqli_fetch_array(execute_query($sql));
			if($roomdetails['multiple']=="yes"){
				$sql='select sum(occupancy) as occupancy from allotment where room_id='.$v;
				$occupants=mysqli_fetch_array(execute_query($sql));
				
			}
			
			$msg .= '<li class="error">Room Alloted successfully</li>';
		}
	}

}
else if(isset($_GET['id'])){
	$sql = 'select * from allotment where sno='.$_GET['id'];
	$result = execute_query($sql);
	$old_data=mysqli_fetch_assoc( $result );
	
	$total_rent=$old_data['room_rent'];
	$sql = 'select * from customer_transactions where allotment_id='.$_GET['id'];
	$result = execute_query($sql);
	$rent_details=mysqli_fetch_assoc( $result );
	
	$sql = 'select * from customer where sno='.$old_data['cust_id'];
	$result = execute_query($sql);
	$cust_details=mysqli_fetch_assoc( $result );
	
	$sql = 'select * from room_master where sno='.$old_data['room_id'];
	$result = execute_query($sql);
	$room_details=mysqli_fetch_assoc( $result );

}
else{
	$old_data['cust_id']='';
	$old_data['original_room_rent']='';
	$old_data['discount']='';
	$old_data['extrabed']='';
	$old_data['net_room_rent']='';
	$old_data['discount_value']='';
	$old_data['room_rent']='';
	$old_data['occupancy']='';
	$old_data['other_charges']='';
	$old_data['guest_address']='';
	$total_rent='';
	$cust_details['mobile']='';
	$cust_details['address']='';
	$cust_details['company_name']='';
}
if(isset($_GET['del'])){
	$sql='select * from allotment_2 where sno='.$_GET['del'];
	$result = execute_query($sql);

	$sql='select * from allotment where sno='.$_GET['del'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
	$stmt->execute();
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
	allot = $("#id").val();
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
			txt += '</td><td><input type="text" name="room_'+value.id+'" id="room_'+value.id+'" value="'+value.rent+'" onBlur="calculate()"></td><td><input type="text" name="extrabed_'+value.id+'" id="extrabed_'+value.id+'" value="'+value.extra_bed+'" onBlur="calculate()"> <input type="hidden" name="discount_'+value.id+'" id="discount_'+value.id+'" value="'+disc_value+'" onBlur="calculate()"><input type="hidden" name="discount_value_'+value.id+'" id="discount_value_'+value.id+'" value="" onBlur="calculate()"></td><td id="taxable_'+value.id+'"></td><td id="cgst_'+value.id+'"></td><td id="sgst_'+value.id+'"></td><td><input type="text" name="net_room_rent_'+value.id+'" id="net_room_rent_'+value.id+'" value="" onblur="reverse_calculate('+value.id+')"></td></tr>';
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
	var other_discount = parseFloat($("#other_discount").val());
	if(!other_discount){
		other_discount=0;
	}
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
				var discount_value = parseFloat(discount);
				if(!discount_value){
					discount_value=0;
				}
			}
			else{
				discount = parseFloat(discount.replace("%",""));
				var discount_value = Math.round(((rent * discount)/100)*100)/100;
			}
			if(!discount_value){
				discount_value=0;
			}
			
			var extrabed = $("#extrabed_"+value).val();
			if(extrabed.search('%')==-1){
				var extrabed_value = parseFloat(extrabed);	
			}
			else{
				extrabed = parseFloat(extrabed.replace("%",""));
				var extrabed_value = Math.round(((rent * extrabed)/100)*100)/100;
			}
			if(!extrabed_value){
				extrabed_value=0;
			}
			if(!extrabed){
				extrabed_value='';
			}
			
			var taxable = rent + extrabed_value - (discount_value+other_discount);
			if(!taxable){
				taxable=0;
			}
			if(taxable>100 && taxable<=7499){
				var cgst = taxable*6/100;
				var sgst = taxable*6/100;
				net_rent =  Math.round(taxable+cgst+sgst);
				
			}
			else if(taxable>7499){
				var cgst = taxable*9/100;
				var sgst = taxable*9/100;
				net_rent =  Math.round(taxable+cgst+sgst);
				
			}
			else{
				var cgst = 0;
				var sgst = 0;
				net_rent =  Math.round(taxable+cgst+sgst);
			}
			$("#room_"+value).val(rent);
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

function reverse_calculate(id){
	var net_rent = $("#net_room_rent_"+id).val();
	//console.log(net_rent);
	if(net_rent!=''){
		net_rent = parseFloat(net_rent);
		if(!net_rent){
			net_rent=0;
		}
		if(net_rent>0){
		    if(net_rate>=8850){
		        var taxable = Math.round(net_rent/1.18*100)/100;
			    var cgst = Math.round(taxable*9)/100;
			
		    }
		    else{
		        var taxable = Math.round(net_rent/1.12*100)/100;
    			var cgst = Math.round(taxable*6)/100;
    			    
		    }
			$("#taxable_"+id).html(taxable);
			$("#cgst_"+id).html(cgst);
			$("#sgst_"+id).html(cgst);
			//console.log(net_rent+'>>'+cgst+'>>'+taxable);
			
			
			var discount = $("#discount_"+id).val();
			if(discount.search('%')==-1){
				var discount_value = parseFloat(discount);
				if(!discount_value){
					discount_value=0;
				}
			}
			else{
				discount = parseFloat(discount.replace("%",""));
				var discount_value = (taxable*100)/(100-discount);
				discount_value = Math.round(discount_value*100)/100;
				discount_value = discount_value - taxable;
			}
			if(!discount_value){
				discount_value=0;
			}
			
			var extrabed = $("#extrabed_"+id).val();
			if(extrabed.search('%')==-1){
				var extrabed_value = parseFloat(extrabed);	
			}
			else{
				extrabed = parseFloat(extrabed.replace("%",""));
				var extrabed_value = Math.round(((rent * extrabed)/100)*100)/100;
			}
			if(!extrabed_value){
				extrabed_value=0;
			}
			if(!extrabed){
				extrabed_value='';
			}
			
			var rent = taxable - extrabed_value + discount_value;
			$("#room_"+id).val(rent);
			
			
		}
	}
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
        <h2>New Allotment</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="allotment2.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >

			<table>
				<tr><td>Guest Name</td><td><input id="cust_name1" name="cust_name1" value="<?php if(isset($row1['cust_name'])){echo $row1['cust_name'];} else if(isset($_GET['id'])){ echo $old_data['guest_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>">
					<input type="checkbox" name="new_user" id="new_user">New User
				</td>
				<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_GET['alt'])){echo $_GET['alt'];} else{echo $old_data['cust_id'];}?>" />
				<td>Registation Number</td>
					<td><input id="registration_no" name="registration_no" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['registration_no'];}elseif(isset($old_data['registration_no'])){echo $old_data['registration_no'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
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
					<td><input id="address" name="address" value="<?php if(isset($row1['address'])){echo $row1['address'];} else { echo $cust_details['address'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" required /></td>
				</tr>
				<tr>
					<td>City*</td>
					<td><input id="city" name="city" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['city'];}elseif(isset($row1['city'])){echo $row1['city'];} else if(isset($_GET['id'])){ echo $cust_details['city'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" required /> &nbsp; PIN* : &nbsp; <input id="zipcode" name="zipcode" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['zipcode'];}elseif(isset($row1['zipcode'])){echo $row1['zipcode'];} else if(isset($_GET['id'])){ echo $cust_details['zipcode'];}?>" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" required /></td>
					<td>State*</td>
					<td><select id="state" name="state" class="field select addr" tabindex="<?php echo $tab++;?>" required >
					<?php
						print_r($cust_details);
					$sql = 'select * from state_name';
					$result_state = execute_query($sql);
					while($row_state = mysqli_fetch_assoc($result_state)){
						echo '<option value="'.$row_state['state_code'].'" ';
						if(isset($_GET['id'])){
							if(abs($cust_details['state'])==abs($row_state['state_code'])){
								echo ' selected="selected" ';
							}
						}
						else{
							if($row_state['state_code']=='9'){
								echo ' selected="selected" ';
							}
						}
						echo '>'.$row_state['indian_states'].'</option>';
					}

					?>
					</select></td>	
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
				    <td>ID Type and Number</td>
				    <td><select id="id_type" name="id_type" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" >
				    	<option value="AADHAAR" <?php echo ($cust_details['id_type']=='AADHAAR')?"selected":""; ?>>Aadhaar</option>
				    	<option value="PAN" <?php echo ($cust_details['id_type']=='PAN')?"selected":""; ?>>PAN</option>
				    	<option value="DL" <?php echo ($cust_details['id_type']=='DL')?"selected":""; ?>>Driving License</option>
				    	<option value="OTHERS" <?php echo ($cust_details['id_type']=='OTHERS')?"selected":""; ?>>Others</option>
				    
				    </select><input id="id_3" name="id_3" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['id_3'];}elseif(isset($row1['id_3'])){echo $row1['id_3'];} else if(isset($_GET['id'])){ echo $cust_details['id_3'];}?>" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				    <td>Remarks</td>
					<td>
						<input id="remarks" name="remarks" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['remarks'];}elseif(isset($old_data['remarks'])){echo $old_data['remarks'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
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
										while($row_room = mysqli_fetch_assoc($result)){
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
					<td>Exit Date</td>
					<td><input name="exit_date" type="text" value="<?php if(isset($old_data['exit_date'])){echo $old_data['exit_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="exit_date" /></td>
					<!--<td>Departure Date</td>
					<td><input name="departure_date" type="text" value="<?php if(isset($old_data['departure_date'])){echo $old_data['departure_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="departure_date" /></td>-->
					
				</tr>
				<tr>
					<td>Reference</td>
					<td>
						<select name="admin_reference">
							<option value="">Select</option>
							<?php
								$sql="SELECT * FROM admin_reference";
								$res=execute_query($sql);
								while($admin_reference=mysqli_fetch_array($res)){
									echo'<option  value="'.$admin_reference['sno'].'"';
						        		if(isset($_GET['id'])){
											if($old_data['reference']==$admin_reference['sno']){
												echo 'selected="selected"';
											}
										}
						        		echo'>'.$admin_reference['name'].'</option>';
								}
							?>
						</select>
					</td>
					<!--<td>Other Discount</td>
					<td><input name="other_discount" type="text" value="<?php if(isset($old_data['other_discount'])){echo $old_data['other_discount'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="other_discount" onBlur="calculate();" /></td>
					<td colspan="2">&nbsp;</td>-->
				</tr>
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
					<td colspan="4" id="insertrow">
						<?php
						if(isset($_GET['id'])){
						?>
						<table width="100%">
							<tbody>
								<tr>
									<th>Room No.</th>
									<th>Occupancy</th>
									<th>Base Rent</th>
									<th>Extra Bed</th>
									<th>Discount</th>
									<th>Taxable</th>
									<th>CGST</th>
									<th>SGST</th>
									<th>Net Price</th>
								</tr>
								<tr>
									<td><?php echo $room_details['room_name']; ?></td>
									<td>
										<select name="occupancy_<?php echo $room_details['sno']; ?>" id="occupancy_<?php echo $room_details['sno']; ?>" onchange="calculate()" class="small">
											<option value="1" <?php echo $old_data['occupancy']==1?'selected':''; ?>>1</option>
											<option value="2" <?php echo $old_data['occupancy']==2?'selected':''; ?>>2</option>
											<option value="3" <?php echo $old_data['occupancy']==3?'selected':''; ?>>3</option>
										</select>
										<input type="hidden" name="occupancy_total_<?php echo $room_details['sno']; ?>" id="occupancy_total_<?php echo $room_details['sno']; ?>" value="<?php echo $room_details['occupancy']; ?>">
										<input type="hidden" name="rent_single_<?php echo $room_details['sno']; ?>" id="rent_single_<?php echo $room_details['sno']; ?>" value="<?php echo $room_details['rent']; ?>">
										<input type="hidden" name="rent_double_<?php echo $room_details['sno']; ?>" id="rent_double_<?php echo $room_details['sno']; ?>" value="<?php echo $room_details['rent_double']; ?>">
										<input type="hidden" name="rent_extra_<?php echo $room_details['sno']; ?>" id="rent_extra_<?php echo $room_details['sno']; ?>" value="<?php echo $room_details['rent_extra']; ?>"></td>
									<td><input type="text" name="room_<?php echo $room_details['sno']; ?>" id="room_<?php echo $room_details['sno']; ?>" value="<?php echo $old_data['original_room_rent']; ?>" onblur="calculate()" class="small"></td>
									<td><input type="text" name="extrabed_<?php echo $room_details['sno']; ?>" id="extrabed_<?php echo $room_details['sno']; ?>" value="<?php echo $old_data['other_charges']; ?>" onblur="calculate()" class="small"></td>
									<td> <input type="text" name="discount_<?php echo $room_details['sno']; ?>" id="discount_<?php echo $room_details['sno']; ?>" value="<?php echo $old_data['discount']; ?>" onblur="calculate()" class="small"><input type="hidden" name="discount_value_<?php echo $room_details['sno']; ?>" id="discount_value_<?php echo $room_details['sno']; ?>" value="<?php echo $old_data['discount_value']; ?>" onblur="calculate()" class="small"></td>
									<td id="taxable_<?php echo $room_details['sno']; ?>"><?php echo $old_data['taxable_amount']; ?></td>
									<td id="cgst_<?php echo $room_details['sno']; ?>"></td>
									<td id="sgst_<?php echo $room_details['sno']; ?>"></td>
									<td><input type="text" name="net_room_rent_<?php echo $room_details['sno']; ?>" id="net_room_rent_<?php echo $room_details['sno']; ?>" value="<?php echo $old_data['room_rent']; ?>" onblur="reverse_calculate(<?php echo $room_details['sno']; ?>)" class="small"></td>
								</tr>
							</tbody>
						</table>
						
						<?php } ?>
					</td>
				</tr>
				
				<tr>
					<td colspan="4" id="insertrow"></td>
				</tr>
				<tr>
					<td colspan="4">
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Allot Room" onMouseDown="" tabindex="<?php echo $tab++;?>"></td>
					<input type="hidden" name="flage" id="" value="<?php if(isset($_GET['f'])){echo $_GET['f'];}?>" />
					<input type="hidden" name="allot2_sno"  value="<?php if(isset($_GET['aid'])){echo $_GET['aid'];}?>" />
					<input type="hidden" name="id" id="id"  value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
				</tr>

			</table>
		</form>
		<table width="100%">
			<tr style="background:#000; color:#FFF;">
				<th>S.No.</th>
				<th>Guest Name</th>
				<th>Company Name</th>
				<th>Occupancy</th>
				<th>Room No.</th>
				<th>Extra Bed</th>
				<th>Total Rent</th>
				<th>Allotment Date</th>
				<th></th>
				<th></th>
				<!--<th></th>-->
                <th></th>
			</tr>	
    <?php
	$sql = 'select * from allotment where exit_date is null or exit_date=""';
	$result=execute_query($sql);
	$i=1;
	while($row = mysqli_fetch_assoc($result)){
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
		$total_rent=floatval($row['room_rent'])*$days;
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
		<td>'.$row['occupancy'].'</td>
		<td>'.get_room($row['room_id']).'</td>
		<td>'.$row['other_charges'].'</td>
		<td>'.$total_rent.'</td>
		
		<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
		<td><a href="allotment.php?id='.$row['sno'].'&f=1">Edit</a></td>';
		//<td class="no-print"><a href="allotment.php?cancel='.$row['sno'].'">'.$cancel_display.'</a></td>
		echo '<td><!--<a href="allotment.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a>--></td>
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
	
	$('#departure_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	elseif(isset($_GET['id'])){
		echo $old_data['departure_date'];
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

<?php
	if(isset($_GET['id'])){
?>
$(document).ready(function(){
	calculate();
});	
	
<?php } ?>
</script>

<?php
page_footer();
?>