<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
navigation('');

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
        <h2>Payments</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="payment_report.php" id="report_form" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
                	<td>Date From</td>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1));
                    </script>
                    </span>
                    </td>
                	<td>Date To</td>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4));
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
					<th>Guest Name</th>
					<th>Mobile</th>
                    <th>Mode of Payment</th>
                    <th>Payment For</th>
                    <th>Amount</th>
                    <th>Date Of Payment</th>
					<th class="no-print">Edit</th>
					<th class="no-print">Delete</th>
				</tr>
    <?php
				$sql = 'select * from customer_transactions where type in ("payment") ';
				if(isset($_POST['mop'])){
					$_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to'])+86400);
					$sql .= ' and timestamp>="'.$_POST['allot_from'].'" and timestamp<="'.$_POST['allot_to'].'"';
					if($_POST['mop']=='cash'){
						$sql .= ' and mop="cash"';
					}
					elseif($_POST['mop']=='credit'){
						$sql .= ' and mop="credit"';
					}
				}
				else{
					$sql .= ' and timestamp>="'.date("Y-m-d").'" and timestamp<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				//echo $sql;
				$run=execute_query($sql);
				$tot='';
				$i=1;
				while($row = mysqli_fetch_array($run)){

					
							$sql1='select * from customer where sno='.$row['cust_id'];
							$result = execute_query($sql1);
							$details=mysqli_fetch_assoc( $result );
							
							echo '<tr>
							<td>'.$i++.'</td>
							<td>'.$details['cust_name'].'</td>
							<td>'.$details['mobile'].'</td>
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
							<td class="no-print"><a href="payment.php?id='.$row['sno'].'">Edit</a></td>
							<td class="no-print"><a href="payment_report.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
							</tr>';
							$i++;
							$tot =floatval($tot)+floatval($row['amount']);
						
					}
					echo '<tr><th colspan="7">Total :</th><th>'.$tot.'</th><th colspan="3">&nbsp;</th></tr>';
				

?>
</table>
		
</div>
<?php

page_footer();
?>
