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
if(isset($_POST['submit_form'])){
	foreach($_POST as $k=>$v){
		$_SESSION['purchase_'.$k] = $v;
	}
}
if(isset($_POST['reset_form'])){
	foreach($_POST as $k=>$v){
		unset($_SESSION['purchase_'.$k]);
	}
}
if(isset($_SESSION['purchase_allot_from'])){
	$sql='select * from allotment where 1=1';
	if($_SESSION['purchase_cust_sno']!=''){
		$sql .= ' and cust_id='.$_SESSION['purchase_cust_sno'];
	}
	if($_SESSION['purchase_allot_from']!=date("Y-m-d")){
		$sql .= ' and allotment_date >="'.$_SESSION['purchase_allot_from'].'"';
	}
	if($_SESSION['purchase_allot_to']!=date("Y-m-d")){
		$sql .= ' and allotment_date<="'.$_SESSION['purchase_allot_to'].'"';
	}
	$sql.= ' group by room_id';
	$result=mysqli_fetch_assoc(execute_query($sql));
	
}
else{
	$sql = 'select * from allotment  where exit_date IS NULL';
	$result=mysqli_fetch_assoc(execute_query($sql));
}
?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
<script type="text/javascript" language="javascript">
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
 <div id="container">
        <h2>Room Allotment Details</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="room_detail.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table width="100%">
            	<tr style="background:#CCC;">
                <td>Allotment Date</td>
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['purchase_allot_from'])){echo $_SESSION['purchase_allot_from'];}else{echo date("Y-m-d");}?>', 1)
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['purchase_allot_to'])){echo $_SESSION['purchase_allot_to'];}else{echo date("Y-m-d");}?>', 4)
                    </script>
                    </span>
                    </td>
                </tr>
                <tr>
                	<th>Customer Name</th>
                    <td>
                    <input id="cust_name1" name="cust_name1" class="fieldtextmedium" maxlength="255" tabindex="7" type="text" value="<?php if(isset($_SESSION['purchase_cust_name1'])){echo $_SESSION['purchase_cust_name1'];}?>">
                    <input id="cust_sno" name="cust_sno" type="hidden" value="<?php if(isset($_SESSION['purchase_cust_sno'])){echo $_SESSION['purchase_supplier_sno'];}?>">
                    </td>
                </tr>
            	<tr>
                	<th colspan="3">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="3">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
            </table>
            <br>
		</form>
<table width="100%">
			<tr style="background:#000; color:#FFF;">
				<th>S.No.</th>
				<th>Room Name</th>
                <th>Occupancy</th>
				<th>Occupants</th>
                <th>View Details</th>
			</tr>
    <?php
			
	$i=1;
	foreach($result as $row)
	{
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
		$room_details=get_room($row['room_id']);
		echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td>'.$room_details['room_name'].'</td>
				<td>'.$room_details['occupancy'].'</td>
				<td>'.$row['occupancy'].'</td>';
			echo '<td><a href="cust_ledger.php?room_id='.$room_details['sno'].'">View</a></td></tr>';
	}
?>
</table>
</div>
<?php
page_footer();
?>
