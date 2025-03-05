<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();

if(isset($_POST['submit'])){
	$con=$db;
	if($_POST['book_sno']!=''){
		$sql = 'update booking_details set total_package="'.$_POST['total_package'].'", no_of_rooms="'.$_POST['no_of_room'].'", booking_date="'.$_POST['booking_date'].'",booking_from="'.$_POST['date_from'].'", booking_to="'.$_POST['date_to'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'", category="'.$_POST['category_id'].'" where sno="'.$_POST['book_sno'].'"';
		$result = execute_query($sql);
			$sql='update customer set cust_name="'.$_POST['cust_name'].'", mobile="'.$_POST['mobile'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP where sno='.$_POST['cust_sno'];
			$result = execute_query($sql);
			$sql='update customer_transactions set amount="'.$_POST['amount'].'",edited_by="'.$_SESSION['username'].'", timestamp="'.$_POST['booking_date'].'", edited_on=CURRENT_TIMESTAMP where cust_id="'.$_POST['cust_sno'].'"';
			$result = execute_query($sql);
			$msg .= '<li>Update successful.</li>';
		}
	else{
		$sql='insert into customer (cust_name,mobile) values ("'.$_POST['cust_name'].'", "'.$_POST['mobile'].'")';
		$result = execute_query($sql);
		$_POST['cust_sno']=$con->insert_id;
		$sql='INSERT INTO customer_transactions (cust_id,  type , timestamp, amount , created_by , created_on , remarks) VALUES ("'.$_POST['cust_sno'].'", "ADVANCE BOOKING", "'.$_POST['booking_date'].'"  , "'.$_POST['amount'].'" , "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'")';
		
		$result = execute_query($sql);
		$sql='INSERT INTO booking_details (cust_id, total_package , no_of_rooms, booking_date ,booking_to,booking_from, created_by , created_on , remarks,category) VALUES ("'.$_POST['cust_sno'].'", "'.$_POST['total_package'].'", "'.$_POST['no_of_room'].'"  , "'.$_POST['booking_date'].'","'.$_POST['date_to'].'","'.$_POST['date_from'].'" , "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP, "'.$_POST['remarks'].'","'.$_POST['category_id'].'")';
		$result = execute_query($sql);
		$msg="Booking Details Successfully Added";
		}
}
if(isset($_GET['id'])){
	$sql = 'select * from booking_details where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
	$sql = 'select * from customer_transactions where cust_id='.$row['cust_id'];
	$result = execute_query($sql);
	$rent_details=mysqli_fetch_assoc( $result );
	$sql = 'select * from customer where sno='.$row['cust_id'];
	$result = execute_query($sql);
	$cust_details=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql='select * from booking_details where sno='.$_GET['del'];
	$result = execute_query($sql);
	$row_del=mysqli_fetch_assoc( $result );
	$sql='select * from customer where sno='.$row_del['cust_id'];
	$result = execute_query($sql);
	$cust=mysqli_fetch_assoc( $result );
	$sql='delete from customer_transactions where cust_id='.$cust['sno'];
	$result = execute_query($sql);
	$sql='delete from customer where sno='.$row_del['cust_id'];
	$result = execute_query($sql);
	$sql='delete from booking_details where sno='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
<script type="text/javascript" language="javascript">
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='cust_name']").val(ui.item.label);
			$('#cust_id').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			document.getElementById('room_name').innerHTML =(ui.item.room_name);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
function get_room_rent(id){
	$.ajax({
		type: "GET",
		url: "scripts/ajax.php?id=category",
		data: { term: id}
	})
	.done(function(msg) {
		var no_of_room=document.getElementById('no_of_room').value;
		msg=no_of_room*msg;
		$('#total_package').val(msg);
	});	
}
</script>
 <div id="container">
        <h2>Advance Booking</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="booking.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Customer Name</td>
					<td><input id="cust_name" name="cust_name" class="fieldtextmedium" maxlength="255" tabindex="1" value="<?php echo $cust_details['cust_name']?>" type="text"> 
					<input id="cust_sno" name="cust_sno" type="hidden" value="<?php if(isset($_GET['id'])){ echo $cust_details['sno'];}?>"></td>
					<td>Mobile</td>
					<td><input name="mobile" type="text" value="<?php echo $cust_details['mobile']?>" class="field text medium" tabindex="2" id="mobile" /></td>
				</tr>
				<tr>
					<td>No. Of Rooms</td>
					<td><input name="no_of_room" type="text" value="<?php echo $row['no_of_rooms']?>" class="field text medium" tabindex="3" id="no_of_room" /></td>
                    <td>Category</td>
					 <td>
                          <select name="category_id" id="category_id" tabindex="4" onchange="get_room_rent(this.value)">
                          <option></option>
                          <?php
								$sql = 'select * from category';
								$result = execute_query($sql);
								while($row_category = mysqli_fetch_assoc($result)){
                                    echo '<option value="'.$row_category['sno'].'" ';
                                    if(isset($row['category'])){
                                        if($row['category']==$row_category['sno']){
                                            echo ' selected="selected"';
                                        }
                                    }
                                    echo '>'.$row_category['room_type'].'</option>';
                                }
                            ?>
                            </select>
						</td>
                  </tr>
                  
                <tr>
					<td>Package Amount</td>
					<td><input id="total_package" name="total_package" value="<?php echo $row['total_package']?>" class="field text medium" maxlength="255" tabindex="5" type="text" />
                    <td>Advance Amount</td>
					<td><input id="amount" name="amount" value="<?php echo $rent_details['amount']?>" class="field text medium" maxlength="255" tabindex="6" type="text" />
				</tr>
				<tr>
                	<td>Date From</td>
					<td><script type="text/javascript" language="javascript">
	  				document.writeln(DateInput('date_from', false, 'YYYY-MM-DD', '<?php if(isset($row['booking_from'])){echo $row['booking_from'];}else{echo date("Y-m-d");} ?>', 7)</script>
					</td>
                    <td>Date To</td>
					<td><script type="text/javascript" language="javascript">
	  				document.writeln(DateInput('date_to', false, 'YYYY-MM-DD', '<?php if(isset($row['booking_to'])){echo $row['booking_to'];}else{echo date("Y-m-d");} ?>', 11)</script>
					</td>
                </tr>
                <tr>
					<td>Booking Date</td>
					<td><script type="text/javascript" language="javascript">
	  				document.writeln(DateInput('booking_date', false, 'YYYY-MM-DD', '<?php if(isset($row['booking_date'])){echo $row['booking_date'];}else{echo date("Y-m-d");} ?>', 16)</script>
					</td>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="<?php echo $row['remarks']?>" class="field text medium" maxlength="255" tabindex="17" type="text" />
				</tr>
				</tr>
				<tr><input type="hidden" name="book_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<td colspan="4"><input id="submit" name="submit" class="btTxt submit" type="submit" value="Submit" onMouseDown="" tabindex="20"></td>
				</tr>
				
			</table>
		</form>
 <table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Customer Name</th>
					<th>Mobile</th>
					<th>No. Of Rooms</th>
                    <th>Category</th>
                    <th>Booking Date</th>
                    <th>Total Package</th>
                    <th>Amount</th>
                    <th>Date From</th>
                    <th>Date To</th>
					<th>Print</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
			$sql = 'select * from booking_details';
			$result=mysqli_fetch_assoc(execute_query($sql));
			$i=1;
			foreach($result as $row){
				if($i%2==0){
					$col = '#CCC';
				}
				else{
				$col = '#EEE';
				}
				$sql='select * from customer where sno='.$row['cust_id'];
				$result = execute_query($sql);
				$details=mysqli_fetch_assoc( $result );
				$sql='select * from customer_transactions where cust_id='.$details['sno'];
				$result = execute_query($sql);
				$advance_details=mysqli_fetch_assoc( $result );
				$sql='select * from category where sno='.$row['category'];
				$result = execute_query($sql);
				$cat_details=mysqli_fetch_assoc( $result );
				echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td>'.$details['cust_name'].'</td>
				<td>'.$details['mobile'].'</td>
				<td>'.$row['no_of_rooms'].'</td>
				<td>'.$cat_details['room_type'].'</td>
				<td>'.date("d-m-Y",strtotime($row['booking_date'])).'</td>
				<td>'.$row['total_package'].'</td>
				<td>'.$advance_details['amount'].'</td>
				<td>'.date("d-m-Y",strtotime($row['booking_from'])).'</td>
				<td>'.date("d-m-Y",strtotime($row['booking_to'])).'</td>
				<td><a href="print_advance_booking.php?id='.$row['sno'].'" target="_blank">Print</a></td>
				<td><a href="booking.php?id='.$row['sno'].'">Edit</a></td>
				<td><a href="booking.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
				</tr>';
	}
?>
</table>
		
</div>
<?php
page_footer();
?>
