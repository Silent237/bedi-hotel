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
?>
<?php
	if(isset($_POST['save'])){
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
			
			foreach($_POST['room_id'] as $k => $v){
			}
		$sql='UPDATE `allotment` SET `cust_id`="'.$_POST['cust_sno'].'",`room_id`="'.$v.'",`room_rent`="'.$_POST['rent'].'",
		`discount`="'.$_POST['discount'].'", `discount_value`="'.$_POST['discount_value'].'", `allotment_date`="'.$_POST['allotment_date'].'",`occupancy`="'.$_POST['cust_sno'].'",
		`other_charges`="'.$_POST['other_charges'].'" `exit_date`="'.$exit.'" WHERE sno="'.$_POST['edit_sno'].'"';
		$result = execute_query($sql);
		if($result){
			$msg .= '<li class="error">Update successfully</li>';
		}else{
			$msg .= '<li class="error">Error In Updation !</li>';
		}
	}
?>

<div id="container">
	<h2>Edit Invoice Details</h2>
	<form action="" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table>
			<tr>
				<td>Invoice Number</td>
				<td><input type="text" name="invoice_no" id="invoice_no"></td>
				<td>Financial Year</td>
				<td><input type="text" name="year" id="year"></td>
				<td><input type="submit" value="Search" name="submit"></td>
			</tr>
		</table>
	<?php
	if(isset($_POST['submit']) && $_POST['id']==''){
		$invoice_no=$_POST['invoice_no'];
		$year=$_POST['year'];
		$sql="SELECT * FROM `allotment` WHERE invoice_no='$invoice_no' AND financial_year='$year'";
		$result = execute_query($sql);
	    $row=mysqli_fetch_assoc( $result );
			echo'<table>
					<tr>
						<th>Name</th>
						<th>Room No</th>
						<th>Room Rent</th>
						<th>Allotment Date</th>
						<th>Edit</th>
					</tr>
					<tr>
						<td>'.$row['cust_id'].'</td>
						<td>'.$row['room_id'].'</td>
						<td>'.$row['room_rent'].'</td>
						<td>'.$row['allotment_date'].'</td>
						<td><a href="invoice_edit.php?id='.$row['sno'].'">Edit</a></td>
					</tr></table>';
		
	}
	?>
</div>
<?php
	if($_GET['id'] !=''){  
	
		$id=$_GET['id'];
		$sql="SELECT * FROM `allotment` WHERE sno='$id'";
		$result = execute_query($sql);
		$row=mysqli_fetch_assoc( $result );
		$cust_sno=$row['cust_id'];

		$sql="SELECT * FROM `customer` WHERE sno='$cust_sno'";
		$result = execute_query($sql);
		$cust_details=mysqli_fetch_assoc( $result );
		$room_id=$row['room-id'];

		$sql="SELECT * FROM `room_master` WHERE sno='$room_id'";
		$result = execute_query($sql);
		$room_details=mysqli_fetch_assoc( $result );
		
	}
?>
		<div id="container">
			<h2>Edit </h2>
			<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
			<table>
				<tr>
					<td>Bill to</td><td><input type="text" name="bill_to" value="<?php if(isset($_GET['id'])) { echo $cust_details['cust_name']; } ?>"></td>
					<input type="hidden" id="cust_sno" value="<?php if(isset($_GET['id'])) { echo $row['cust_id']; } ?>" name="cust_sno">
					<td>Gust Name</td>
					<td><input type="text" id="cust_name1" name="bill_to" value="<?php if(isset($_GET['id'])) { echo $cust_details['company_name']; } ?>"></td>
				</tr>
				<tr>
					<td>ID Card No.</td>
					<td><input id="id_1" name="id_card" value="<?php if(isset($_GET['id'])){ echo $cust_details['id_1'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					<td>Address</td>
					<td><input id="address" name="address" value="<?php if(isset($_GET['id'])){ echo $cust_details['address'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
				</tr>
				<tr>
					<td>GSTIN</td>
					<td><input id="gstin" name="id_2"  value="<?php if(isset($_GET['id'])){ echo $cust_details['id_2'];}?>"  class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					<td>Mobile</td>
					<td><input type="text" name="mobile"  value="<?php if(isset($_GET['id'])){ echo $cust_details['mobile'];}?>" ></td>
				</tr>
				<tr>
					<td>Room No</td>
					<td>
						<select name="room_id[]" id="room_id" tabindex="<?php echo $tab++;?>" class="room_id" multiple="multiple" onBlur="get_room_rent();" >
									<?php
										$sql = 'select * from room_master order by abs(room_name)';
										$result = execute_query($sql);
										while($row_room = mysqli_fetch_assoc(execute_query($sql))){
											if($row_room['status']==1){
												if(isset($row['room_id'])){
													if($row['room_id']==$row_room['sno']){
														echo '<option value="'.$row_room['sno'].'" ';
														echo 'selected="selected"';
														echo '>'.$row_room['room_name'].'</option>';
													}
												}
											}
											else{
												echo '<option value="'.$row_room['sno'].'" ';
												echo '>'.$row_room['room_name'].'</option>';
											}
										}
									?>
									</select>
					</td>
					<input type="hidden" name="room_id" value="<?php if(isset($_GET['id'])) { echo $room_details['sno']; } ?>">
					<td>Rent Of Room</td>
					<td><input type="text" name="room_rent" value="<?php if(isset($_GET['id'])) { echo $row['original_room_rent']; } ?>"></td>
				</tr>
				<tr>
					<td>Discount</td>
					<td><input type="text" name="discount" value="<?php if(isset($_GET['id'])) { echo $row['discount']; } ?>"></td>
					<td>Discount Value</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['discount_value']; } ?>" name="discount_value"></td>
				</tr>
				<tr>
					<td>Total Rent</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['room_rent']; } ?>" name="original_room_rent"></td>
					<td>Invoice Type</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['invoice_type']; } ?>" name="invoice_type"></td>
				</tr>
				<tr>
					<td>Allotment Date</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['allotment_date']; } ?>" name="allotment_date"></td>
					<td>Exit Date</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['exit_date']; } ?>" name="exit_date"></td>
				</tr>
				<tr>
					<td>Occupancy</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['occupancy']; } ?>" name="occupancy"></td>
					<td>Other Charges</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['other_charges']; } ?>"name="other_charges"></td>
				</tr>
				<tr>
					<td>Net Price</td>
					<td><input type="text"  value="<?php if(isset($_GET['id'])) { echo $row['room_rent']; } ?>" name="cgst"></td>
					
				</tr>
				<tr>
					
					<td colspan="3"></td>
					<input type="hidden" value="<?php if(isset($_GET['id'])) { echo $_GET['id']; } ?>" name="allot_id">
					<td><input type="submit" name="save" value="Save"></td>
				</tr>
			</table>
		</div>

<?php
page_footer();
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
<script>
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
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name1").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>