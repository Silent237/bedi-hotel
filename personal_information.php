<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$tab=1;
$con = $db;
if(isset($_POST['submit'])){
	if($_POST['edit_sno'] != ''){
		$sql_update = 'UPDATE `personal_information` SET
				`name`="'.$_POST['name'].'" ,
				`father_name`="'.$_POST['father_name'].'" ,
				`address`="'.$_POST['address'].'" ,
				`police_station`="'.$_POST['police_station'].'" ,
				`mobile_number`="'.$_POST['mobile_number'].'" ,
				`district`="'.$_POST['district'].'" ,
				`state`="'.$_POST['state'].'" ,
				`occupation`="'.$_POST['occupation'].'" ,
				`reason_for_come`="'.$_POST['reason_for_come'].'" ,
				`edited_by`="'.$_SESSION['username'].'" ,
				`edition_time`="'.date('Y-m-d h:i:s').'" 
				WHERE `sno`="'.$_POST['edit_sno'].'"';
			$res_update = execute_query($sql_update);
			if($res_update){
				echo 'Data Updated...';
			} 
	}
	else{
		$sql_insert = 'INSERT INTO `personal_information`(`name`, `father_name`, `address`, `police_station`, `mobile_number`, `district`, `state`, `occupation`, `reason_for_come`, `created_by`, `creation_time`) VALUES ("'.$_POST['name'].'" , "'.$_POST['father_name'].'" , "'.$_POST['address'].'" , "'.$_POST['police_station'].'" , "'.$_POST['mobile_number'].'" , "'.$_POST['district'].'" , "'.$_POST['state'].'" , "'.$_POST['occupation'].'" , "'.$_POST['reason_for_come'].'" , "'.$_SESSION['username'].'" , "'.date('Y-m-d h:i:s').'")';
		$res_insert = execute_query($sql_insert);
		if($res_insert){
			echo 'Data Inserted...';
		} 
	}
}
if (isset($_GET['e_id'])) {
	$sql_edit = 'SELECT * FROM `personal_information` WHERE `sno`="'.$_GET['e_id'].'"';
	$result_edit = execute_query($sql_edit);
	$row_edit = mysqli_fetch_array($result_edit);
}
?>
<style>
.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>
<script src="js/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" language="javascript">
</script>
 <div id="container">
        <h2>Personal Information <span style="float: right;"><a href="report_personal_information.php">Report</a></span></h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; $tab=1;?>
		<form action="personal_information.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Name</td>
					<td>
						<input type="text" name="name" id="name" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['name'];} ?>" required>
					</td>
					<td>Father Name</td>
					<td>
						<input type="text" name="father_name" id="father_name" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['father_name'];} ?>">
					</td>
				</tr>
				<tr>
					<td>Mobile Number</td>
					<td>
						<input type="text" name="mobile_number" id="mobile_number" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['mobile_number'];} ?>">
					</td>
					<td>Occupation</td>
					<td>
						<input type="text" name="occupation" id="occupation" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['occupation'];} ?>">
					</td>
				</tr>
				<tr>
					<td>Address</td>
					<td>
						<input type="text" name="address" id="address" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['address'];} ?>">
					</td>
					<td>Police Station</td>
					<td>
						<input type="text" name="police_station" id="police_station" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['police_station'];} ?>">
					</td>
				</tr>
				<tr>
					<td>District</td>
					<td>
						<input type="text" name="district" id="district" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['district'];} ?>">
					</td>
					<td>State</td>
					<td>
						<input type="text" name="state" id="state" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['state'];} ?>">
					</td>
				</tr>
				<tr>
					<td>Reason For Come</td>
					<td>
						<input type="text" name="reason_for_come" id="reason_for_come" tabindex="<?php echo $tab++; ?>" value="<?php if(isset($_GET['e_id'])){echo $row_edit['reason_for_come'];} ?>">
					</td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4">
						<input id="submit" name="submit" class="btTxt submit" type="submit" value="Done" onMouseDown="" tabindex="<?php echo $tab++;?>">
						<input type="hidden" name="edit_sno" id="edit_sno" value="<?php echo $row_edit['sno'];?>">
					</td>
				</tr>
			</table>
		</form>
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

/**$(document).ready(function(){
	get_room_rent();
});**/
</script>

<?php
navigation('');
page_footer();
?>