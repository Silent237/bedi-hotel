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
	$row1=mysqli_fetch_assoc( $result );
	$sql='select * from customer_transactions where allotment_id="" and cust_id='.$_GET['alt'];
	$result = execute_query($sql);
	$advance=mysqli_fetch_assoc( $result );
}
if(isset($_GET['check_in'])){
	$sql = 'select * from advance_booking where sno='.$_GET['check_in'];
	$result = execute_query($sql);
	$row_check_in=mysqli_fetch_assoc( $result );
	$sql='select * from customer where sno='.$row_check_in['cust_id'];
	$result = execute_query($sql);
	$customer_check_in=mysqli_fetch_assoc( $result );
}
if(isset($_GET['hold'])){
	$sql='update allotment set hold_date="'.date('Y-m-d H:i').'" where sno='.$_GET['hold'];		
	$result = execute_query($sql);
	$sql_room='update room_master set status=0 where sno='.$_GET['room_id'];
	$result = execute_query($sql_room);
}
if(isset($_GET['cancel'])){
	$sql = 'select * from allotment where sno='.$_GET['cancel'];
	$row = mysqli_fetch_array(execute_query($sql));
	if($row['cancel_date']==''){
		$sql = 'update allotment set cancel_date=CURRENT_TIMESTAMP where sno='.$_GET['cancel'];
		execute_query($sql);

		$sql = 'update allotment_2 set cancel_date=CURRENT_TIMESTAMP where sno='.$_GET['cancel'];
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
	if($_POST['company_name'] =='' AND $_POST['cust_name1'] == ''){
		$msg .= '<li class="error">Enter Bill To Details.</li>';
	}
	if($msg==''){
		if($_POST['cust_sno']==''){
			$sql='INSERT INTO customer (company_name, cust_name, mobile, id_1, id_2, address, created_by, created_on , state) VALUES ("'.$_POST['company_name'].'", "'.$_POST['cust_name1'].'", "'.$_POST['mobile'].'", "'.$_POST['id_1'].'", "'.$_POST['id_2'].'", "'.$_POST['address'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "UTTAR PRADESH")';
			$result = execute_query($sql);
			$msg .= '<li class="error">Customer Added successfully</li>';
			$_POST['cust_sno'] = $con->insert_id;		
		}
		if($_POST['cust_sno']!=''){
			$sql='update customer set 
			cust_name="'.$_POST['cust_name1'].'" ,
			company_name="'.$_POST['company_name'].'" , 
			mobile="'.$_POST['mobile'].'" ,
			address="'.$_POST['address'].'" ,
			id_1="'.$_POST['id_1'].'" ,
			id_2="'.$_POST['id_2'].'"
			where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
		}
		if($_POST['allot2_sno'] !=''){
			$sql='select * from allotment_2 where allotment_id="'.$_POST['allot_sno'].'" order by allotment_id desc limit 1';
			$result = execute_query($sql);
			$row=mysqli_fetch_assoc( $result );
			$inv_no = $room['invoice_no'];
			$exit_date = $room['exit_date'];
			///die();
			//$sql = 'delete from allotment_2 where allotment_id='.$_POST['allot_sno'];
		//	echo $sql;
			//$stmt=connect()->prepare($sql);
			//$stmt->execute();
			
			//$sql_room='update room_master set status=NULL where sno='.$room['room_id'];
			//echo $sql_room;
			//$stmt=connect()->prepare($sql_room);
			//$stmt->execute();
			
			$sql='update customer set 
			company_name="'.$_POST['company_name'].'",
			cust_name="'.$_POST['cust_name1'].'", 
			mobile="'.$_POST['mobile'].'",
			id_2= "'.$_POST['id_2'].'",
			id_1="'.$_POST['id_1'].'",
			address="'.$_POST['address'].'",
			edited_by="'.$_SESSION['username'].'", 
			edited_on=CURRENT_TIMESTAMP  
			where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
			
			$msg .= '<li class="error">Update successful.</li>';
			
		}
		else if($_POST['allot_sno'] !=''  && $_POST['flage'] !=''){
			$sql='select * from allotment where sno='.$_POST['allot_sno'];
			$result = execute_query($sql);
			$row=mysqli_fetch_assoc( $result );
			$inv_no = $room['invoice_no'];
			$exit_date = $room['exit_date'];
			$sql = 'delete from allotment where sno='.$_POST['allot_sno'];
			$result = execute_query($sql);
			
			$sql = 'delete from allotment_2 where allotment_id='.$_POST['allot_sno'];
			$result = execute_query($sql); 

			$sql_room='update room_master set status=NULL where sno='.$room['room_id'];
			$result = execute_query($sql);
			
			$sql='update customer set 
			company_name="'.$_POST['company_name'].'",
			cust_name="'.$_POST['cust_name1'].'", 
			mobile="'.$_POST['mobile'].'",
			id_2= "'.$_POST['id_2'].'",
			id_1="'.$_POST['id_1'].'",
			address="'.$_POST['address'].'",
			edited_by="'.$_SESSION['username'].'", 
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
      $i=0;
		foreach($_POST['room_id'] as $k => $v){
			$taxable_amt=$_POST['room_'.$v] + $_POST['extrabed_'.$v] - $_POST['discount_value_'.$v];
			if($taxable_amt > 999){
				$inv_type = 'tax';
				$tax_rate = '12';
			}
			else{
				$inv_type = 'bill_of_supply';
				$tax_rate = '0';
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
			$grand_tot=$_POST['net_room_rent_'.$v];
			if($_POST['allot2_sno'] !=''){
				$without=$_POST['room_'.$v.''];
			    if(isset($_POST['discount'])){
				 $dis=$without * 10 /100;
			      }else{
				 $dis=0;
			      }
			       if($_POST['edit_room_sno'] =='' && $_POST['edit_room_sno'] ==0){
		             echo $taxable_amt=$_POST['room_'.$v] + $_POST['extrabed_'.$v]-$dis;
		    }else{
			         echo  $taxable_amt=$_POST['room_'.$v] + $_POST['extrabed_'.$v];
		   }
					if($taxable_amt > 999){
						$inv_type = 'tax';
						$tax_rate = '12';
					}
					else{
						$inv_type = 'bill_of_supply';
						$tax_rate = '0';
					}
				$tot=$taxable_amt*$tax_rate/100;
		        $grand_tot=$taxable_amt+$tot;
				$sql = 'INSERT INTO allotment_2 (guest_name, cust_id, room_id , room_rent,other_discount, original_room_rent, financial_year, invoice_no, invoice_type, tax_rate, allotment_date, exit_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on,plans,taxable_amount,allotment_id,edit_status,guest_address) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$v.'" , "'.$grand_tot.'","'.$dis.'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$inv_type.'", "'.$tax_rate.'",  "'.$_POST['allotment_date'].'", "'.$_POST['exit_date'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['extrabed_'.$v].'", '.$edited_by.', '.$edition_time.',"'.$_POST['plan'].'","'.$taxable_amt.'","'.$_POST['allot_sno'].'","1","'.$_POST['guest_address'].'")';
				$result = execute_query($sql);
			}
			else{
				$sql = 'INSERT INTO allotment (guest_name, cust_id, room_id , room_rent, discount, discount_value, original_room_rent, financial_year, invoice_no, invoice_type, tax_rate, allotment_date, exit_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on,plans,taxable_amount,advance_booking_id , guest_address) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$v.'" , "'.$grand_tot.'", "'.$_POST['discount_'.$v].'", "'.$_POST['discount_value_'.$v].'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$inv_type.'", "'.$tax_rate.'",  "'.$_POST['allotment_date'].'", '.$exit_date.', "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['extrabed_'.$v].'", '.$edited_by.', '.$edition_time.',"'.$_POST['plan'].'","'.$taxable_amt.'","'.$_POST['check_in_sno'].'" , "'.$_POST['guest_address'].'")';
				$result = execute_query($sql);
				$sql="select * from allotment order by sno desc limit 1";
				$result = execute_query($sql);
				$old_data1=mysqli_fetch_assoc( $result );
				$alot_id=$old_data1['sno'];

				$sql = 'INSERT INTO allotment_2 (guest_name, cust_id, room_id , room_rent, discount, discount_value, original_room_rent, financial_year, invoice_no, invoice_type, tax_rate, allotment_date, exit_date, created_by , created_on, remarks, occupancy, other_charges, edited_by, edited_on,plans,taxable_amount,allotment_id, guest_address) VALUES ("'.$_POST['cust_name1'].'","'.$_POST['cust_sno'].'", "'.$v.'" , "'.$grand_tot.'", "'.$_POST['discount_'.$v].'", "'.$_POST['discount_value'].'", "'.$_POST['room_'.$v].'", "'.$year.'", "'.$_POST['invoice_no'].'", "'.$inv_type.'", "'.$tax_rate.'",  "'.$_POST['allotment_date'].'", '.$exit_date.', "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP , "'.$_POST['remarks'].'","'.$_POST['occupancy_'.$v].'", "'.$_POST['extrabed_'.$v].'", '.$edited_by.', '.$edition_time.',"'.$_POST['plan'].'","'.$taxable_amt.'","'.$alot_id.'" , "'.$_POST['guest_address'].'")';
				$result = execute_query($sql);
			}
		
			
			$sql='select * from allotment where cust_id='.$_POST['cust_sno'].' and room_id='.$v;
			$result = execute_query($sql);
			$allotid=mysqli_fetch_assoc( $result );
			$sql='select * from room_master where sno='.$v;
			//echo $sql;
			$roomdetails=mysqli_fetch_array(execute_query($sql));
			if($_POST['allot2_sno'] ==''){
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
		}
			$msg .= '<li class="error">Room Alloted successfully</li>';		
	}
	if($_POST['check_in_sno'] != ''){
    	$sql='update advance_booking set status="1" where sno="'.$_POST['check_in_sno'].'"';
		$result = execute_query($sql);
    }
	}
}
if(isset($_GET['aid'])){
	$sql = 'select * from allotment_2 where allotment_id='.$_GET['aid'];
	$result = execute_query($sql);
	$old_data=mysqli_fetch_assoc( $result );
	$_GET['id']=$_GET['aid'];
	$total_rent=$old_data['room_rent']+$old_data['other_charges'];
	$sql = 'select * from customer_transactions where allotment_id='.$_GET['aid'];
	$result = execute_query($sql);
	$rent_details=mysqli_fetch_assoc( $result );
	$sql = 'select * from customer where sno='.$old_data['cust_id'];
	$result = execute_query($sql);
	$cust_details=mysqli_fetch_assoc( $result );
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
        <h2>New Allotment</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="allotment.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >

			<table class="no-print">
				<tr>
					<td>Guest Name</td><td><input id="cust_name1" name="cust_name1" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['cust_name'];}else if(isset($row1['cust_name'])){echo $row1['cust_name'];} else if(isset($_GET['id'])){ echo $cust_details['cust_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>"></td>
					<input type="hidden" name="cust_sno" id="cust_sno" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['sno'];}elseif(isset($_GET['alt'])){echo $_GET['alt'];} else{echo $old_data['cust_id'];}?>" />
					<td>Address 2</td>
					<td><input id="guest_address" name="guest_address" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['guest_address'];}elseif(isset($row1['address'])){echo $row1['guest_address'];} else { echo $cust_details['guest_address'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<tr>
					<td>Company Name</td>
					<td><input id="company_name" name="company_name" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['company_name'];}elseif(isset($row1['company_name'])){echo $row1['company_name'];} else if(isset($_GET['id'])){ echo $cust_details['company_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Address</td>
					<td><input id="address" name="address" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['guest_address'];}elseif(isset($row1['guest_address'])){echo $row1['guest_address'];} else { echo $old_data['guest_address'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>	
				</tr>
					<tr>
					<td>SAC/HSN</td>
					<td><input id="id_1" name="id_1" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['id_1'];}elseif(isset($row1['id_1'])){echo $row1['id_1'];} else if(isset($_GET['id'])){ echo $cust_details['id_1'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
					<td>Mobile</td>
					<td><input id="mobile" name="mobile" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['mobile'];}elseif(isset($row1['mobile'])){echo $row1['mobile'];} else { echo $cust_details['mobile'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
				<tr>
					<td>GSTIN</td>
					<td><input id="id_2" name="id_2" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['id_2'];}elseif(isset($row1['id_2'])){echo $row1['id_2'];} else if(isset($_GET['id'])){ echo $cust_details['id_2'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
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
					<td><input name="allotment_date" type="text" value="<?php if(isset($_GET['check_in'])){echo $row_check_in['allotment_date'];}elseif(isset($old_data['allotment_date'])){echo $old_data['allotment_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="allotment_date" /></td>
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
						<input id="remarks" name="remarks" value="<?php if(isset($_GET['check_in'])){echo $customer_check_in['remarks'];}elseif(isset($old_data['remarks'])){echo $old_data['remarks'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
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
					<input type="hidden" name="check_in_sno" id="check_in_sno" value="<?php if(isset($_GET['check_in'])){echo $_GET['check_in'];}?>" />
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
				<th>Mobile</th>
				<th>Address</th>
				<th>Occupancy</th>
				<th>Room No.</th>
				<th class="no-print">Extra Bed</th>
				<th class="no-print">Total Rent</th>
				<th>Allotment Date</th>
				<th class="no-print"></th>
				<th class="no-print"></th>
				<!--<th></th>-->
                <th class="no-print"></th>
			</tr>	
    <?php
	$sql = 'select * from allotment where exit_date is null or exit_date=""';
	$result=mysqli_fetch_assoc(execute_query($sql));
	$i=1;
	$occcu='';
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
		<td>'. get_company_name($row['cust_id']).'</td>';
		$sql_cus="select * from customer where sno='".$row['cust_id']."'";
		$sql_run=execute_query($sql_cus);
		$row_cust=mysqli_fetch_array($sql_run);
       echo '<td>'.$row_cust['mobile'].'</td>';
       if($row_cust['address'] != ''){
       	echo '<td>'.$row_cust['address'].'</td>';
       }
       else{
       	echo '<td>'.$row['guest_address'].'</td>';
       }
       echo '<td>'.$row['occupancy'].'</td>
		<td>'.get_room($row['room_id']).'</td>
		<td class="no-print">'.$row['other_charges'].'</td>
		<td class="no-print">'.$total_rent.'</td>
		
		<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
		<td class="no-print"><a href="allotment.php?id='.$row['sno'].'&f=1">Edit</a></td>';
		if($row['hold_date']==''){
		echo '<td class="no-print"><a href="allotment.php?hold='.$row['sno'].'&room_id='.$row['room_id'].'">Hold</a></td>';
		}
		else{
			echo '<td class="no-print">On Hold</td>';
		}
		//<td class="no-print"><a href="allotment.php?cancel='.$row['sno'].'">'.$cancel_display.'</a></td>
		echo '<td class="no-print"><!--<a href="allotment.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a>--></td>
	</tr>'
	;
	   $occcu +=$row['occupancy'];
	}
	echo '<tr>
	<td></td>
	<td></td>
	<td></td>

	<td></td>
	<td class="no-print"></td>
	<td class="" style=""><b style="font-size:20px" >Occupancy<b></td><td><b style="font-size:20px">'.$occcu.'</b></td><td></td><td></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td></tr>';
?>
</table>
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#allotment_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_GET['check_in'])){
		echo $row_check_in['allotment_date'];
	}
	elseif(isset($_POST['date_from'])){
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
