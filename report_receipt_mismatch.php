<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();


if(isset($_GET['del'])){
	$sql='delete from customer_transactions where sno='.$_GET['del'];
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
			var rooms='';
			$.each(ui.item.rooms, function( index, value ){
				rooms += '<option value="'+value.allotment_id+'">'+value.label+'</option>';
			});
			document.getElementById('room_name').innerHTML = rooms;
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>
 <div id="container">
        <h2>Receipts</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="report_receipt_mismatch.php" id="report_form" class="wufoo leftLabel page1" name="addnewdesignation" enctype="multipart/form-data" method="POST">
		<table width="100%">
            	<tr style="background:#CCC;">
                	<td>Date From</td>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1)
                    </script>
                    </span>
                    </td>
                	<td>Date To</td>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4)
                    </script>
                    </span>
                    </td>
                	<td>Mode of Payment</td>
                    <td>
                    <select name="mop" id="mop">
                    	<option value="all" <?php if(isset($_POST['mop'])){if($_POST['mop']=='all'){echo 'selected="selected"';}}?>>All</option>
                    	<option value="cash" <?php if(isset($_POST['mop'])){if($_POST['mop']=='cash'){echo 'selected="selected"';}}?>>Cash</option>
                    	<option value="credit" <?php if(isset($_POST['mop'])){if($_POST['mop']=='credit'){echo 'selected="selected"';}}?>>Credit Card</option>
                    </select></td>
                </tr>
            	<tr class="no-print">
                	<th colspan="3">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="3">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
            </table>	
		</form>
			<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Customer Name</th>
					<th>Mobile</th>
					<th>Room</th>
                    <th>Room Rent</th>
                    <th>Days</th>
                    <th>Invoice Amount</th>
                    <th>Type</th>
                    <th>Mode of Payment</th>
                    <th>Amount</th>
                    <th>Date Of Receipt</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
				$sql = 'select * from customer_transactions where type in ("RENT", "ADVANCE") ';
				if(isset($_POST['mop'])){
					$_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to'])+86400);
					$sql .= ' and created_on>="'.$_POST['allot_from'].'" and created_on<="'.$_POST['allot_to'].'"';
					if($_POST['mop']=='cash'){
						$sql .= ' and mop="cash"';
					}
					elseif($_POST['mop']=='credit'){
						$sql .= ' and mop="credit"';
					}
				}
				$result=mysqli_fetch_assoc(execute_query($sql));
				$i=1;
				$tot=0;
				$tot_invoice=0;
				foreach($result as $row)
				{
					if($i%2==0){
						$col = '#CCC';
					}
					else{
						$col = '#EEE';
					}
					$sql='select * from customer where sno='.$row['cust_id'];
					$result = execute_query($sql);
					$details=mysqli_fetch_assoc( $result );
					$sql='select * from allotment where sno='.$row['allotment_id'];
					$result = execute_query($sql);
					$room_details=mysqli_fetch_assoc( $result );
					$tot+= $row['amount'];
					$days = get_days($room_details['allotment_date'], $room_details['exit_date']);
					$inv_amount = ($room_details['room_rent']*$days);
					$tot_invoice += $inv_amount;
					
					if($inv_amount!=$row['amount']){
						$col = "#F00;";
					}
					
					echo '<tr style="background:'.$col.'">
					<td>'.$i++.'</td>
					<td>'.$details['cust_name'].'</td>
					<td>'.$details['mobile'].'</td>
					<td>'.get_room($room_details['room_id']).'</td>
					<td>'.$room_details['room_rent'].'</td>
					<td>'.$days.'</td>
					<td>'.$inv_amount.'</td>
					<td>'.$row['type'].'</td>';
					if($row['mop']=='cash'){
						echo '<td>Cash</td>';
					}
					else{
						echo '<td>Credit Card</td>';
					}
					echo '
					<td>'.$row['amount'].'</td>
					<td>'.$row['timestamp'].'</td>
					<td><a href="receipts.php?id='.$row['sno'].'"><i class="fas fa-edit"></i></a></td>
					<td><a href="report_receipt.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>
					</tr>';
				}
				echo '<tr><th colspan="6">Total :</th><th>'.$tot_invoice.'</th><th>&nbsp;</th><th>'.$tot.'</th><th colspan="4">&nbsp;</th></tr>';
?>
</table>
		
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#receipt_date').datetimepicker({
	step:15,
	format: 'd-m-Y H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	else{
		echo date("d-m-Y H:i");	
	}
	?>'
	});

	
</script>
<?php
page_footer();
?>
