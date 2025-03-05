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

if(isset($_POST['submit'])){
	$cust_name=$_POST['cust_name'];
	$sql='select * from allotment where cust_id='.$_POST['cust_id'].' and exit_date is null';
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)) {
		if(isset($_POST['check_'.$row['sno']])){
			$details=$row;
			$balance = check_pendency($row['room_rent'], $row['allotment_date'], $row['cust_id'], $_POST['exit_date'], $row['sno']);
			
			$sql='INSERT INTO customer_transactions (cust_id, allotment_id , type , timestamp, amount, mop, created_by , created_on , remarks) VALUES ("'.$_POST['cust_id'].'", "'.$row['sno'].'", "RENT" , "'.$_POST['exit_date'].'"  , "'.$balance.'" , "'.$_POST['mop'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'")';
			$result = execute_query($sql);
			$msg .= '<li class="error">Receipt Added</li>';

			$sql='update allotment set exit_date="'.$_POST['exit_date'].'" where sno='.$row['sno'];
			$result = execute_query($sql);
			$sql='update room_master set status=0 where sno='.$row['room_id'];
			$result = execute_query($sql);
			$sql='update customer set destination="'.$_POST['destination'].'" , check_out_time="'.$_POST['exit_date'].'" where sno='.$_POST['cust_id'];
			$result = execute_query($sql);
			$msg .='<li class="error">'.strtoupper($cust_name).' CAN VACATE ROOM. <a href="print.php?id='.$details['sno'].'" target="_blank">PRINT</a></li>';
		}
	}
}

?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
<script type="text/javascript" language="javascript">
function change_date(){
	//alert(rid);
	$.getJSON( "scripts/ajax.php?rid="+$("#rid").val()+"&exit_date="+$("#exit_date").val()+"&term=a&id=room")
		.done(function() {
			//alert( "Please Wait." );
		})
		.fail(function() {
			//alert( "Some Error." );
		})
		.always(function(ui) {
			//alert(ui.mobile);
			ui = ui[0];
		    $("[name='cust_name']").val(ui.cust_name);
			$('#cust_id').val(ui.id);
			$('#mobile').val(ui.mobile);
			var txt='<td>Select Rooms</td><td>';
			var i=0;
			var allot_id='';
			$.each( ui.rooms, function( index, value ) {
				txt=txt+'<input type="checkbox" checked="checked" value="'+value.allotment_id+'" id="check_'+value.allotment_id+'" name="check_'+value.allotment_id+'" onclick="update_rent(this.value)">'+value.label+'<input type="hidden" value="'+value.balance+'" id="balance_'+value.allotment_id+'" name="balance_'+value.allotment_id+'"><br />';
				i++;
				allot_id = value.allotment_id;
			});
			txt=txt+'<input type="hidden" name="total_rooms" id="total_rooms" value="'+i+'"></td>';
			$('#insert_rooms').html(txt);
			$('#room_name').val(ui.room_name);
			$('#allotment_id').val(ui.allotment_id);
			$("#balance").val('0');
			update_rent(allot_id);
			$("#ajax_loader").show();
			return false;		
	});	
}
function update_rent(val){
	var balance = parseFloat($("#balance").val());
	if(!balance){
		balance = 0;
	}
	var id = "#check_"+val;
	if($(id).prop('checked')){
		var room_rent = parseFloat($("#balance_"+val).val());
		if(!room_rent){
			room_rent=0;
		}
		balance = balance+room_rent
	}
	else{
		var room_rent = parseFloat($("#balance_"+val).val());
		if(!room_rent){
			room_rent=0;
		}
		balance = balance-room_rent
	}
	$("#balance").val(balance);
}
</script>
 <div id="container">
        <h2>Vacate Room</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="vacant_room_room.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Exit Date</td>
					<td><input name="exit_date" type="text" value="<?php if(isset($row['exit_date'])){echo $row['exit_date'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="exit_date" onBlur="change_date();" />
					</td>
					<td>Balance</td>
					<td><input name="balance" type="text" value="<?php if(isset($row['balance'])){echo $row['balance'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="balance" readonly />
					</td>
				</tr>
				<tr>
					<td>Company Name</td>
					<td><input id="cust_name" name="cust_name" value="" class="field text medium" maxlength="255" tabindex="1" type="text" />
					<input type="hidden" name="cust_id" id="cust_id" value="" />
					<td>Mobile</td>
					<td><input id="mobile" name="mobile" value="" class="field text medium" maxlength="255" tabindex="2" type="text" />
				</tr>
               <tr>
					<td>Mode of Payment</td>
				   <td><select id="mop" name="mop" class="field select medium"><option value="cash">Cash</option><option value="credit">Credit</option></select>
				</tr>
                <tr>
					<td>Where He/She will Go</td>
					<td><input id="destination" name="destination" value="" class="field text medium" maxlength="255" tabindex="2" type="text" />
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="" class="field text medium" maxlength="255" tabindex="10" type="text" />
				</tr>
				<tr id="insert_rooms"></tr>
				<tr>
				<td><input id="submit" name="submit" class="btTxt submit" type="submit" value="Vacat Room" onMouseDown="" tabindex="23">
				<input id="rid" name="rid" class="btTxt submit" type="hidden" value="<?php echo $_GET['rid']; ?>"></td>
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

$(document).ready(function(){
	change_date();
});
</script>
<?php
page_footer();
?>
