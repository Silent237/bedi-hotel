<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
page_header();
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
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
<div id="content" class="print-only">
            <div style="border:0px solid; margin:0 auto; text-align:center"><h1>Shane Avadh Hotel</h1>
				<h1>Civil Lines Faizabad, Ayodhya-224001<br/>Registration No. 02/476 J.A./Sarai Act/2003. CIN: U55101UP1980PTC005050</h1>
				<h1>GSTIN : 09AAHCS2262A1ZN</h1>
            </div>
        </div>
 <div id="container">
        <h2>Receipts</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="report_receipt.php" id="report_form" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
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
                </tr>
                <tr>
                	<td>Invoice No</td>
                	<td><input type="text" name="invoice_no" value="<?php if(isset($_POST['invoice_no'])){echo $_POST['invoice_no'];} ?>"></td>
                	<td>Mode of Payment</td>
                    <td>
                    <select name="mop" id="mop">
                    	<option value="all" <?php if(isset($_POST['mop'])){if($_POST['mop']=='all'){echo 'selected="selected"';}}?>>All</option>
                    	<option value="cash" <?php if(isset($_POST['mop'])){if($_POST['mop']=='cash'){echo 'selected="selected"';}}?>>Cash</option>
                    	<option value="card" <?php if(isset($_POST['mop'])){if($_POST['mop']=='card'){echo 'selected="selected"';}}?>>Card</option>\
                    	<option value="credit" <?php if(isset($_POST['mop'])){if($_POST['mop']=='credit'){echo 'selected="selected"';}}?>>Credit</option>
                    </select></td>
                </tr>
            	<tr class="no-print">
                	<th colspan="2">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="2">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
            </table>	
		</form>
			<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Comapny Name</th>
					<th>Guest Name</th>
					<th>Mobile</th>
					<th>Invoice No.</th>
                    <th>Room</th>
                    <th>Room Rent</th>
                    <th class="no-print">Type</th>
                    <th>Mode of Payment</th>
                    <th>Amount</th>
                    <th>Date Of Receipt</th>
					<th class="no-print">Edit</th>
					<th class="no-print">Delete</th>
				</tr>
    <?php
    			$sql_mop = '';
				$sql = 'select * from customer_transactions where type in ("RENT", "receipt") ';
				if(isset($_POST['mop'])){
					$_POST['allot_to'] = date("Y-m-d", strtotime($_POST['allot_to'])+86400);
					$sql .= ' and created_on>="'.$_POST['allot_from'].'" and created_on<="'.$_POST['allot_to'].'"';
					$sql_mop .= ' and customer_transactions.created_on>="'.$_POST['allot_from'].'" and customer_transactions.created_on<="'.$_POST['allot_to'].'"';
					if($_POST['mop']=='cash'){
						$sql .= ' and mop="cash"';
						$sql_mop.= ' and mop="cash"';
					}
					elseif($_POST['mop']=='credit'){
						$sql .= ' and mop="credit"';
						$sql_mop .= ' and mop="credit"';
					}
					elseif($_POST['mop']=='card'){
						$sql .= ' and mop="card"';
						$sql_mop .= ' and mop="card"';
					}
					elseif($_POST['invoice_no'] != ''){
						$sql .= ' and `invoice_no`="'.$_POST['invoice_no'].'" ';
						$sql_mop .= ' and `invoice_no`="'.$_POST['invoice_no'].'" ';
					}
				}
				else{
					$sql .= ' and created_on>="'.date("Y-m-d").'" and created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
					$sql_mop .= ' and customer_transactions.created_on>="'.date("Y-m-d").'" and customer_transactions.created_on<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				$result=mysqli_fetch_assoc(execute_query($sql));
				$i=1;
				$tot=0;
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
					echo '<tr style="background:'.$col.'">
					<td>'.$i++.'</td>
					<td>'.$details['company_name'].'</td>
					<td>'.$room_details['guest_name'].'</td>
					<td>'.$details['mobile'].'</td>
					<td>'.$row['invoice_no'].'</td>
					<td>'.get_room($room_details['room_id']).'</td>
					<td>'.$room_details['room_rent'].'</td>
					<td>'.$row['type'].': '.$row['payment_for'].'</td>';
					if($row['mop']=='cash'){
						echo '<td>Cash</td>';
					}
					else if($row['mop']=='card'){
						echo '<td>Card</td>';
					}
					else{
						echo '<td>Credit</td>';
					}
					echo '
					<td>'.$row['amount'].'</td>
					<td>'.$row['timestamp'].'</td>
					<td class="no-print"><a href="receipts.php?id='.$row['sno'].'">Edit</a></td>
					<td class="no-print"><a href="report_receipt.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
					</tr>';
				}
				echo '<tr><th colspan="9">Total :</th><th>'.$tot.'</th><th colspan="3">&nbsp;</th></tr>';
?>
</table>
<table>
	<tr>
		<th>S.No.</th>
		<th>Mode of Payment</th>
		<th>Count</th>
		<th>Amount</th>
	</tr>
	<?php
	if(!isset($_POST['mop'])){
		$sql_summary = 'SELECT mop, count(*) as count, sum(amount) as amount FROM `customer_transactions` left join customer on customer.sno = customer_transactions.cust_id WHERE mop !="nocharge" AND type in ("RENT", "receipt") '.$sql_mop.' group by mop';
	}
	else{
		$sql_summary = 'SELECT mop, count(*) as count, sum(amount) as amount FROM `customer_transactions` left join customer on customer.sno = customer_transactions.cust_id WHERE mop !="nocharge" AND type in ("RENT", "receipt") '.$sql_mop.' group by mop';
		//echo $sql_summary;
	}
	//echo $sql_summary;
	$result = execute_query($sql_summary);
	$i=1;
	$total = 0;
	if(mysqli_num_rows($result) != 0){
		while($row = mysqli_fetch_assoc($result)){
			echo '<tr>
			<td>'.$i++.'</td>
			<td> <span style="text-transform:uppercase">'.$row['mop'].'</span></td>
			<td>'.$row['count'].'</td>
			<td class="right">'.round($row['amount'] , 3).'</td>
			</tr>';
			$total+=$row['amount'];
		}
	}
	echo '<tr>
	<th colspan="2">&nbsp;</th>
	<th class="right">Total : </th>
	<th>'.round($total , 3).'</th>
	</tr>';
	
	
	?>
</table>
		
</div>
<?php
page_footer();
?>
