<?php
set_time_limit(0);
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$errorMessage='';
if(isset($_POST['submit'])){
	if($_POST['name']=='') {
		$msg .= "<li>Please Enter Donator's Name .</li>";
	}
	if($msg==''){
		if($_POST['room_sno']!=''){
			$sql = 'update donation set name="'.$_POST['name'].'", district="'.$_POST['district'].'", amount="'.$_POST['amount'].'", purpose="'.$_POST['purpose'].'",donation_date="'.$_POST['donation_date'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'" where sno='.$_POST['room_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
		}
		else{
			$sql='INSERT INTO donation (name,district,amount,purpose,donation_date,created_by,created_on,remarks) VALUES ("'.$_POST['name'].'", "'.$_POST['district'].'","'.$_POST['amount'].'","'.$_POST['purpose'].'","'.$_POST['donation_date'].'","'.$_SESSION['username'].'",CURRENT_TIMESTAMP,"'.$_POST['remarks'].'")';
			$result = execute_query($sql);
			$msg="Donation Added successfully";
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from donation where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=$stmt->fetch();
}
if(isset($_GET['del'])){
	$sql = 'delete from donation where sno='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<script type="text/javascript" language="javascript" src="form_validator.js"></script>
 <div id="container">
        <h2>Add Donation</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="add_donation.php" id="report_form" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Name</td>
					<td><input id="name" name="name" value="<?php if(isset($row['name'])){echo $row['name'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" /></td>
					<td>District</td>
					<td><input id="district" name="district" value="<?php if(isset($row['district'])){echo $row['district'];}?>" class="field text medium" maxlength="255" tabindex="2" type="text" />
				</tr>
                <tr>
					<td>Amount</td>
					<td><input id="amount" name="amount" value="<?php if(isset($row['amount'])){echo $row['amount'];}?>" class="field text medium" maxlength="255" tabindex="3" type="text" /></td>
					<td>Purpose</td>
					<td><input id="purpose" name="purpose" value="<?php if(isset($row['purpose'])){echo $row['purpose'];}?>" class="field text medium" maxlength="255" tabindex="4" type="text" />
				</tr>
				<tr>
                	<td>Date</td>
					<td><script type="text/javascript" language="javascript">
	  				document.writeln(DateInput('donation_date', 'report_form', false, 'YYYY-MM-DD', '<?php if(isset($row['donation_date'])){echo $row['donation_date'];}else{echo date("Y-m-d");} ?>', 5))</script>
					</td>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="10" type="text" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="hidden" name="room_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Donation" onMouseDown="" tabindex="23"></td>
				</tr>
			</table>
		</form>
		<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Name</th>
					<th>District</th>
                    <th>Amount</th>
                    <th>Purpose</th>
                    <th>Date</th>
					<th>Remarks</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
		$sql = 'select * from donation';
		$result=mysqli_fetch_assoc(execute_query($sql));
		$i=1;
		foreach($result as $row){
			if($i%2==0){
				$col = '#CCC';
			}
			else{
				$col = '#EEE';
			}
			echo '<tr style="background:'.$col.'; text-align:center;">
			<td>'.$i++.'</td>
			<td>'.$row['name'].'</td>
			<td>'.$row['district'].'</td>
			<td>'.$row['amount'].'</td>
			<td>'.$row['purpose'].'</td>
			<td>'.date("d-m-Y", strtotime($row['donation_date'])).'</td>
			<td>'.$row['remarks'].'</td>
			<td><a href="add_donation.php?id='.$row['sno'].'">Edit</a></td>
			<td><a href="add_donation.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
			</tr>';
		}
?>
</table>
</div>
<?php
page_footer();
?>
