<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
$tab=1;
$_POST['invoice_no']='';
$row='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$partial_val=0;
if(isset($_POST['submit'])){
	if (isset($_POST['res_bill'])) {
		$res_bill_print = "1";
	}
	else{
		$res_bill_print = "0";
	}
	$cust_name=$_POST['cust_name'];
	$cust_id=$_POST['cust_id'];
	$sql='select * from allotment where cust_id="'.$_POST['cust_id'].'" and (exit_date is null or exit_date="")';
	$res = execute_query($sql);
	$date = $_POST['exit_date'];
	$time = strtotime($date);
	$month = date("m",$time);
	$year = date("Y",$time);
	$num = mysqli_num_rows(execute_query($sql));
	$o_d = $_POST['other_discount_value'];
	//echo  $_POST['other_discount_value'];
	//echo $o_d;
	//die();
	
	if($month>=1 && $month<=3){
		$year = $year-1;
	}
	$paid = 0;
	$all_paid = 0;
	$kl = $_POST['advance_amount_paid'];
	$set_sno = '';
	while($row = mysqli_fetch_assoc($res)) {
		if(isset($_POST['check_'.$row['sno']])){
			$details=$row;
			$balance = check_pendency($row['room_rent'], $row['allotment_date'], $row['cust_id'], $_POST['exit_date'], $row['sno']);
			$allot=$row['allotment_date'];
			$exit=$_POST['exit_date'];
			$msg .= '<li class="error">Receipt Added</li>';	
			$sql = 'select * from allotment where financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			$invoice_result = execute_query($sql);
			if(mysqli_num_rows($invoice_result)!=0){
				$invoice_no = mysqli_fetch_array($invoice_result);
				$_POST['invoice_no'] = $invoice_no['invoice_no']+1;

			}
			else{
				$_POST['invoice_no'] = 1;
			}
			$sql = 'select * from allotment where financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			$invoice_result = execute_query($sql);
			if(mysqli_num_rows($invoice_result)!=0){
				$invoice_no = mysqli_fetch_array($invoice_result);
				$_POST['invoice_no'] = $invoice_no['invoice_no']+1;

			}
			else{
				$_POST['invoice_no'] = 1;
			}
			$sql='select * from allotment where sno='.$row['sno'];
			$result=execute_query($sql);
			$hold=mysqli_fetch_array($result);
			if($hold['hold_date'] != ''){
				$_POST['exit_date'] = $hold['hold_date'];
			}
			$sql1='update allotment_2 set exit_date="'.$_POST['exit_date'].'", invoice_no="'.$_POST['invoice_no'].'" , `bill_create_date`="'.date('Y-m-d H:i:s').'" , `res_bill_print`="'.$res_bill_print.'" where allotment_id='.$row['sno'];
			$result = execute_query($sql1);
			$sql='update allotment set exit_date="'.$_POST['exit_date'].'", invoice_no="'.$_POST['invoice_no'].'" , `bill_create_date`="'.date('Y-m-d H:i:s').'" , `res_bill_print`="'.$res_bill_print.'" where sno='.$row['sno'];
			$result = execute_query($sql);
			$sql='update room_master set status=2 where sno='.$row['room_id'];
			$result = execute_query($sql);
			$sql='update customer set destination="'.$_POST['destination'].'" , check_out_time="'.$_POST['exit_date'].'" where sno='.$_POST['cust_id'];
			$result = execute_query($sql);
			if(isset($_POST['discshow_'.$row['sno']])){
				$taxable_amt=floatval($row['taxable_amount'])-floatval($_POST['discshow_'.$row['sno']]);
				/**if($taxable_amt > 7499){
				$inv_type = 'tax';
				$tax_rate = '28';
				}
				elseif($taxable_amt > 2499){
					$inv_type = 'tax';
					$tax_rate = '18';
				}
				elseif($taxable_amt > 100){
					$inv_type = 'tax';
					$tax_rate = '12';
				}
				else{
					$inv_type = 'bill_of_supply';
					$tax_rate = '0';
				}**/
				if($taxable_amt > 100 && $taxable_amt<=7499){
					$inv_type = 'tax';
					$tax_rate = '12';
				}
				else if($taxable_amt > 7499){
					$inv_type = 'tax';
					$tax_rate = '18';
				}
				else{
					$inv_type = 'bill_of_supply';
					$tax_rate = '0';
				}
				$taxable_amt=$taxable_amt + round(($taxable_amt* $tax_rate/100),2);
				$dis_value=floatval($row['discount_value']) + floatval($_POST['discshow_'.$row['sno']]);
				$balance = check_pendency($taxable_amt, $row['allotment_date'], $row['cust_id'], $_POST['exit_date'], $row['sno']);
				//$balance=$balance-$dis_value;
				//echo $balance;
				//die();
				$sql='update allotment set `room_rent`="'.$taxable_amt.'",`invoice_type`="'.$inv_type.'", `other_discount`="'.$dis_value.'" where sno='.$row['sno'];
				execute_query($sql);
				//echo $sql;
				$sql1='update allotment_2 set `room_rent`="'.$taxable_amt.'",`invoice_type`="'.$inv_type.'", `other_discount`="'.$dis_value.'" where allotment_id='.$row['sno'];
				execute_query($sql1);
			}
			if($_POST['advance_amount_paid'] >= $balance AND $_POST['advance_amount_paid'] > 0){
				$_POST['advance_amount_paid'] -= $balance;
				$all_paid += $balance;
				$paid = $balance; 
				//echo $_POST['advance_amount_paid'].'<->'.$balance.'<->'.$paid.'<br/>';
			}
			else if($_POST['advance_amount_paid'] < $balance AND $_POST['advance_amount_paid'] > 0){
				$paid = $_POST['advance_amount_paid'];
				$all_paid += $_POST['advance_amount_paid'];
				$_POST['advance_amount_paid'] = 0;
				//echo $_POST['advance_amount_paid'].'<->'.$balance.'<->'.$paid.'<end<br/>';
			}
			else{
				$paid = 0;
			}
            $exit_date=date('Y-m-d',strtotime($_POST['exit_date']));
			$sql='INSERT INTO customer_transactions (cust_id, allotment_id , type , timestamp, amount, mop, created_by , created_on , remarks,invoice_no,financial_year,advance_set_amt) VALUES ("'.$_POST['cust_id'].'", "'.$row['sno'].'", "RENT" , "'.$exit_date.'"  , "'.$balance.'" , "'.$_POST['mop'].'", "'.$_SESSION['username'].'" ,"'.date('Y-m-d H:i:s').'", "'.$_POST['remarks'].'","'.$_POST['invoice_no'].'","'.$year.'","'.round($paid).'")';
			$result = execute_query($sql);
		    if($paid != 0){
		    	$sql_inv = 'select * from customer_transactions order by abs(sno) desc limit 1';
				$inv_result = execute_query($sql_inv);
				$inv_no = mysqli_fetch_array($inv_result);
		    	$set_sno .= $inv_no['sno'];
		    }
		    if($_POST['advance_amount_paid'] > 0){
		    	$set_sno .= '#';
		    }
			if($_POST['res_bill'] ==0 && $_POST['res_bill'] ==''){	
			    $sql="UPDATE `customer_transactions` SET `mop`='".$_POST['mop']."' where allotment_id='".$row['sno']."'  and payment_for !='res'";
			}
			else{
				$sql="UPDATE `customer_transactions` SET `mop`='".$_POST['mop']."' where cust_id='".$custid."' and allotment_id=".$row['sno']." and type='sale_restaurant' and remarks='credit' and mop ='credit'";
				
			}				
			execute_query($sql);
			$msg .='<li class="error">'.strtoupper($cust_name).' CAN VACATE ROOM. <a href="print.php?id='.$details['sno'].'" target="_blank">PRINT</a></li>';
		}
		//$msg .='<li class="error">'.strtoupper($cust_name).' CAN . <a href="continue_room.php?id='.$cust_id.'" target="_blank">Continue</a></li>';
	}
	if($kl > 0){
		$l = strlen($set_sno);
		$w = substr($set_sno , $l-1);
		//echo $l.'<br/>';
		//echo $w.'<br/>';
		if($w == "#" ){
			$set_sno = substr_replace($set_sno,"",$l-1);
		}
		$sql='INSERT INTO customer_transactions (cust_id , type , timestamp, amount, mop, created_by , created_on , remarks , invoice_no , financial_year , payment_for,set_sno) VALUES ("'.$_POST['cust_id'].'", "ADVANCE_PAID" , "'.date('Y-m-d').'"  , "'.$all_paid.'" , "cash", "'.$_SESSION['username'].'" ,"'.date('Y-m-d H:i:s').'", "'.$_POST['remarks'].'","","'.$year.'" , "room_rent" , "'.$set_sno.'")';
		$result = execute_query($sql);
		$sql_inv = 'select * from customer_transactions order by abs(sno) desc limit 1';
		$inv_result = execute_query($sql_inv);
		$inv_no = mysqli_fetch_array($inv_result);
		$inv = $inv_no['sno'];
		$sql_update = 'UPDATE `customer_transactions` SET `credit_bill_paid_sno`="'.$inv.'" WHERE `sno` IN ('.str_replace("#"," , ",$set_sno).')';
		execute_query($sql_update);
		//echo $sql_update;
	}			
}

?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
<script type="text/javascript" language="javascript">
$(function() {
	var options = {
		source: function (request, response){
			var dater = $("#exit_date").val();
			var room_val=document.getElementById("room_number").value;
			if(room_val ==''){
			$.getJSON("scripts/ajax.php?id=cust_name&exit_date="+dater,request, response);
		}else{
			$.getJSON("scripts/ajax.php?id=room_number&exit_date="+dater,request, response);
		}
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='cust_name']").val(ui.item.cust_name);
		    $("[name='room_number']").val(ui.item.label);
			$('#cust_id').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			$('#opening_val').val(ui.item.opening);
			$('#advance_amount').val(ui.item.advance_amt);
			$('#advance_amount_paid').val(ui.item.advance_amt);
			var vt = $("#exit_date").val();
			var txt='<td>Select Rooms<br> All &nbsp;&nbsp;&nbsp;<input type="checkbox" name="selectall" id="selectall" onchange="checkAll()"></td><td><ul>';
			var i=0;
			
			$.each( ui.item.rooms, function( index, value ) {
				if(value.customer_id == ui.item.id){
					txt=txt+'<li style="line-height:25px;"><input type="hidden" id="restaurant_credit_amt_'+i+'" value="'+parseInt(value.restaurant_credit_amt) +'"><input type="hidden" id="org_'+i+'" value="'+parseInt(value.org) +'"><input type="checkbox" value="'+value.allotment_id+'" id="check_'+i+'" name="check_'+value.allotment_id+'" onclick="update_rent()">'+value.label+'-'+value.guest_name+'('+value.hold+')<input type="text" value="'+parseInt(value.balance) +'" id="balance_'+i+'" name="balance_'+value.allotment_id+'"><input type="hidden" value="'+parseInt(value.tax_rent) +'" id="base11'+i+'" name="base11'+value.allotment_id+'"><input type="hidden" value="'+parseInt(value.org_rent) +'" id="orgrent'+i+'" name="orgrent'+value.allotment_id+'">&nbsp;<a href="print.php?id='+value.allotment_id+'&vt='+vt+'" target="_blank">View Invoice</a>&nbsp;&nbsp;<input  type="text"  id="discshow_'+i+'" name="discshow_'+value.allotment_id+'" style="width:40px;" readonly >Disc<input type="checkbox"  id="checkdisc_'+i+'" name="checkdisc_'+value.allotment_id+'" value="'+value.allotment_id+'"  onclick="update_rent();" ></li>';
					i++;
				}
			});
			
			txt+='</ul><td>Res Bill</td>';
			txt+='<td><input type="checkbox" name="res_bill" id="res_bill" onclick="update_rent(this.value)"><input type="text" name="res_bill_show" id="res_bill_show" value=""><input type="hidden" name="res_bal" id="res_bal" value="'+ui.item.res_bal+'"><input type="hidden" name="total_rooms" id="count" value="'+i+'"></td>';
			
			console.log(txt);
			$('#insert_rooms').html(txt);
			$('#room_name').val(ui.item.room_name);
			$('#allotment_id').val(ui.item.allotment_id);
			$("#ajax_loader").show();
			return false;
		}
	};


	$("input#room_number").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
$("input#cust_name" ).on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});

})




function checkAll() {
   // var num = document.getElementsById("tota").value;
  //  alert(num);
    // var check = document.getElementById("selectall").value;
   	 var l=document.getElementById("count").value;
     if ($('#selectall').is(':checked') ) {
     	//alert('d');
         for (var i = 0; i < l; i++) {
         	var checkid = "check_"+i;
         //	console.log(checkid);
         
          $('#'+checkid).prop('checked', true);
           
         }
     }
     else {
     	//alert("ddddd");
         for (var i = 0; i < l; i++) {
         		//console.log(i);
            var checkid = "check_"+i;
           // update_rent();
             $('#'+checkid).prop('checked', false);
         }
     }
      update_rent();

}
 
function update_rent(){
	var l=document.getElementById("count").value;
 	var totbal=0;
 	var totbase=0;
 	var totdisc=0;
 	var total_restaurant_credit_amt = 0;
 	var opening_val=parseFloat($("#opening_val").val());
 	var advance_amount=parseFloat($("#advance_amount_paid").val());
 	for(var i=0;i<=l;i++){
 		var id = "#check_"+i;
 		if($(id).prop('checked')){

 			var balance = parseFloat($("#balance_"+i).val());
 			var restaurant_credit_amt = parseFloat($("#restaurant_credit_amt_"+i).val());
 			if(!restaurant_credit_amt){
 				restaurant_credit_amt = 0;
 			}
 			total_restaurant_credit_amt += restaurant_credit_amt;
 			var chkdis = "#checkdisc_"+i;
 			//alert(chkdis);
 			var base = parseFloat($("#org_"+i).val());
 			if($(chkdis).prop('checked')){
 				
 				var check=document.getElementById("check_"+i+"").value;
 				var base = parseFloat($("#org_"+i).val());
 				
		       $.ajax({
			  url:"scripts/ajax.php",
			  type:"post",
			 data:{check:check,base:base},
			 success:function(data,status){
			 	alert('Discount Update Successfuly');
            }
		});
		   

                var disval= base * 10 /100;
 				$("#discshow_"+i).val(disval);
 				totdisc += disval;
 				var disbase=base - disval;
				if(disbase > 100){
					tax_rate = 12;
				}
				else{
					tax_rate=0;
				}

				var gst=disbase * tax_rate/100;
				disbase = disbase + gst;
				//alert(disbase);
				totbal +=disbase;
 				totbase +=base;
 				//alert(totbal);
			}
			else{
	 			totbal +=parseFloat(balance);
	 			//alert(totbal);
	 			totbase +=base;
	 			$("#discshow_"+i).val('');

		   	var deletebase = parseFloat($("#org_"+i).val());
		   	var deletecheck=document.getElementById("check_"+i+"").value;
		   	  //alert(check);
		       $.ajax({
			  url:"scripts/ajax.php",
			  type:"post",
			 data:{deletecheck:deletecheck,deletebase:deletebase},
			 success:function(data,status){
			 	//alert(data);
			 	//alert('Discount Update Successfuly');
          }
		});
         

		   

			}
 		}
 	}
 	//alert(disbase);
 	var chkid="#res_bill";
	if($(chkid).prop('checked')){
		var resbal= parseFloat($("#res_bal").val());
		//var resbal = total_restaurant_credit_amt;
		if(!resbal){
			resbal=0;
		}
		//alert(resbal);
		$("#res_bill_show").val(resbal);
		$("#check_res").val('1');
		
	}
	else{
		$("#res_bill_show").val('');
		$("#check_res").val('0');
		resbal=0;
		
	}
 	totbal +=resbal;
 	if(totbal > advance_amount){
 		var amt_paid = totbal - advance_amount; 
 	}
 	else{
 		var amt_paid = 0;
 	}
 	$("#f_rent").val(totbal);
	$("#rent").val(totbase);
	$("#balance").val(totbal);
	$("#amount_paid").val(amt_paid);
	$("#other_discount").val(totdisc);


}
</script>
 <div id="container">
        <h2>CHECK-OUT</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="vacant_room.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<input type="hidden" name="check_res"  id="check_res" value="0">
			<table>
				<tr>
					<td>Exit Date <br/>(Exit Date Must Be Greater Than Allotment Date*)</td>
					<td><input name="exit_date" type="text" value="<?php if(isset($row['exit_date'])){echo $row['exit_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="exit_date" /></td>
					<td>Base Rent</td>
					<td><input name="rent" type="text" value="<?php if(isset($row['balance'])){echo $row['balance'];}?>" class="field text medium" id="rent" readonly />
					</td>
				</tr>
				<tr>
					<td>Room Number</td>
					<td><input id="room_number" name="room_number" value="" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					
					
					<td colspan="4"></td>
				</tr>
				<tr>
					<td>Guest/Company Name</td>
					<td><input id="cust_name" name="cust_name" value="" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					<input type="hidden" name="cust_id" id="cust_id" value="" /></td>
					<td>Opening balance</td>
					<td><input type="text" name="opening_val" id="opening_val" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>">
					</td>
					
				</tr>
               <tr>
					<td>Mode of Payment</td>
				   	<td><select id="mop" name="mop" class="field select medium" tabindex="<?php echo $tab++;?>" required>
				   	<option value="cash">Cash</option>
				   	<option value="card">Card</option>
				   	<option value="credit">Credit</option>
				   	<option value="paytm">Paytm</option>
				   	<option value="cheque">Cheque</option>
				   	<option value="online_transfer">Bank Transfer</option>
				   </select>
				   </td>
				   <td>Mobile</td>
					<td><input id="mobile" name="mobile" value="" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
				   	
				</tr>
                <tr>
                	<td>Balance</td>
					<td><input name="balance" type="text" value="<?php if(isset($row['balance'])){echo $row['balance'];}?>" class="field text medium" id="balance" readonly />
					</td>
					

					<td>Total Discount </td>
				   	<td><input type="text" name="other_discount" id="other_discount" class="field text medium"  tabindex="<?php echo $tab++;?>" readonly><span id="dis_show"> </span>
				   		
				   	</td>

				</tr>
				<tr>
					<td>Where He/She will Go</td>
					<td><input id="destination" name="destination" value="" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					<td>Total Rent</td>
				   	<td><input type="text" name="f_rent" id="f_rent" class="field text medium" tabindex="<?php echo $tab++;?>">
				   	</td>
				</tr>
				<tr>
					<td>Advance Amount Total</td>
				   	<td><input type="text" name="advance_amount" id="advance_amount" class="field text medium" tabindex="<?php echo $tab++;?>" readonly>
				   	</td>
				   	<td>Advance Amount</td>
				   	<td><input type="text" name="advance_amount_paid" id="advance_amount_paid" class="field text medium" tabindex="<?php echo $tab++;?>" onblur="update_rent()" onchange="amount_validation()">
				   	</td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					</td>
					<td>Amount Paid</td>
				   	<td><input type="text" name="amount_paid" id="amount_paid" class="field text medium" tabindex="<?php echo $tab++;?>">
				   	</td>
					<input type="hidden" name="other_discount_value" id="other_discount_value" />
				</tr>
				
				
				<tr id="insert_rooms"></tr>
				<tr>
				<td colspan="4" style="text-align: center;"><input id="submit" name="submit" class="btTxt submit large" type="submit" value="Vacat Room" tabindex="<?php echo $tab++;?>"></td>
				</tr>
		</table>
	</form>
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#exit_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>'
	});

	
</script>
<?php
navigation('');
page_footer();
?>
<script>
	$(function () {
	
  $('input[name="partial"]').on('click', function () {
        if ($(this).prop('checked')) {
            $('input[name="fullpay"]').checked=false;
        } else {
            $('input[name="fullpay"]').checked=true;
        }
    });

    $('input[name="partial_val"]').hide();
    $('input[name="partial"]').on('click', function () {
        if ($(this).prop('checked')) {
            $('input[name="partial_val"]').fadeIn();
        } else {
            $('input[name="partial_val"]').hide();
        }
    });
});
</script>
<script type="text/javascript">
	function other_discount_calculation(){
		if(document.getElementById("fix_disc").checked){
			$("#dis_show").text('10%');
			var discount_value=document.getElementById('fix_disc').value;
			//var dis=discount_value;
			//alert(discount_value);
			var amount_base=parseFloat(document.getElementById('rent').value);
			//alert(amount_base);
			var tot_rent=parseFloat(document.getElementById('f_rent').value);
			//alert(amount_base);
			var day=Math.floor(tot_rent/amount_base);
			//alert(day);
			var text_rent= parseFloat(amount_base)* parseFloat(day);

			//alert(text_rent);
			var dif=tot_rent-text_rent;
			
			//alert(amount_base);
			var gst=Math.abs(dif*100/text_rent);

			if(discount_value.search('%')==-1){
				var discount_value = parseFloat(discount_value);
			}
			else{
				discount_value = discount_value.replace("%","");
				//alert(discount_value);
				//alert(amount_base);
				var dis_val = Math.round(((amount_base* discount_value)/100)*100)/100;
				var discount_value = Math.round(((text_rent * discount_value)/100)*100)/100;
				
				//alert(dis_val);
			}
			if(!discount_value){
				discount_value=0;
			}
			var opening_val=parseFloat($("#opening_val").val());
			if(!opening_val){
				opening_val = 0;
			}
			
		//alert(disc);
			text_rent = text_rent - discount_value;
			
			var tot=text_rent + parseFloat(text_rent*gst/100);
			balance = tot+opening_val;
			$("#f_rent").val(tot);
			$("#amount_paid").val(tot);
			$("#other_discount_value").val(dis_val);
			$("#other_discount").val(discount_value);
			$("#balance").val(balance);

		}
		else{
			var opening_val=parseFloat($("#opening_val").val());
			if(!opening_val){
				opening_val = 0;
			}
			
			var amount_paid=parseFloat(document.getElementById('no_discount_rent').value);
			
			amount_paid = amount_paid;
			var opening =amount_paid + opening_val;
			$("#amount_paid").val(amount_paid);
			$("#f_rent").val(amount_paid);
			$("#balance").val(opening);
			$("#other_discount").val(0);
			$("#other_discount_value").val(0);
			$("#dis_show").text('');
		}
	}
</script>
<script>
	function amount_validation(){
	 	var advance_amount = parseFloat(document.getElementById('advance_amount').value);
	 	var advance_amount_paid = parseFloat(document.getElementById('advance_amount_paid').value);
	 	if(advance_amount_paid > advance_amount){
	 		$('#advance_amount_paid').val(advance_amount);
	 		alert("More Than Advance Amount Is Not Allow...");
	 	}
	}
</script>