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
$errorMessage='';
if(isset($_POST['submit'])){
	if($_POST['bill_name']==''){
		$msg .= '<li>Please Enter Bill Name.</li>';
	}
	$rduration='';
	if($msg==''){
		if($_POST['bill_sno']!=''){
			if((is_numeric($_POST['duration']))){
				$rduration=$_POST['duration'];
			}
			else{
				$rduration=$_POST['month'];
			}
			$sql = 'update monthly_bills set party_name="'.$_POST['party_name'].'", bill_name="'.$_POST['bill_name'].'", recurring_duration="'.$rduration.'", amount="'.$_POST['amount'].'",bill_date="'.$_POST['bill_date'].'",edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'" where sno='.$_POST['bill_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
			
		}
		else{
			if((is_numeric($_POST['duration']))){
				$rduration=$_POST['duration'];
			}
			else{
				$rduration=$_POST['month'];
			}
			$sql='INSERT INTO monthly_bills (party_name , bill_name , recurring_duration , amount , bill_date , created_by , created_on ,remarks) VALUES ("'.$_POST['party_name'].'" , "'.$_POST['bill_name'].'" , "'.$rduration.'" , "'.$_POST['amount'].'" , "'.$_POST['bill_date'].'", "'.$_SESSION['username'].'" ,CURRENT_TIMESTAMP,"'.$_POST['remarks'].'")';
			$result = execute_query($sql);
			$msg="Bill Added successfully";
		}
	}
}

if(isset($_GET['id'])){
	$sql = 'select * from monthly_bills where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql = 'delete from monthly_bills where sno='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<script type="text/javascript" language="javascript">
function change_type(id){
	if(id=='specific_month'){
		document.getElementById('sp_month').style.display = 'block';
		
	}
	else{
		document.getElementById('sp_month').style.display = 'none';		
	}
}
</script>
 <div id="container">
        <h2>Add New Bill</h2>
		<?php echo $msg;
		$tab=1; ?>	
		<form action="addmonthlybills.php" id="form_report" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
 	<table>
		<tr>
			<td>Party Name</td>
            <td><input id="party_name" name="party_name" class="field text medium" value="<?php if(isset($row['party_name'])){echo $row['party_name'];}?>"   maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
			<td>Bill Name</td>
            <td><input id="bill_name" name="bill_name" class="field text medium" value="<?php if(isset($row['bill_name'])){echo $row['bill_name'];}?>"   maxlength="255" tabindex="1" type="text" />
		</tr>
		<tr>
        	<td>Estimated Amount</td>
			<td><input id="amount" name="amount" type="text" class="field text addr" value="<?php if(isset($row['amount'])){echo $row['amount'];}?>"  tabindex="<?php echo $tab++;?>" /></td>
			<td>Recurring Duration</td>
			<td><select name="duration" id="duration" onfocus="change_type(this.value)" onblur="change_type(this.value)" onchange="change_type(this.value)" tabindex="<?php echo $tab++;?>">
				<option value="1" <?php if(isset($_GET['id'])){if($row['recurring_duration']=='1'){echo ' selected';}}?>>1</option>
				<option value="2" <?php if(isset($_GET['id'])){if($row['recurring_duration']=='2'){echo ' selected';}} ?>>2</option>
				<option value="4" <?php if(isset($_GET['id'])){if($row['recurring_duration']=='4'){echo ' selected';}}?>>4</option>
				<option value="6" <?php if(isset($_GET['id'])){if($row['recurring_duration']=='6'){echo ' selected';}}?>>6</option>
				<option value="12" <?php if(isset($_GET['id'])){if($row['recurring_duration']=='12'){echo ' selected';}}?>>12</option>
				<option value="specific_month" <?php if(isset($_GET['id'])){if($row['recurring_duration']=='specific_month'){echo ' selected';}}?>>Specific Month</option>
				</select>
			</td>
			<td style="display:none;" id="sp_month">Specific Month
			<select name="month" id="month">
				<option value=""></option>
				<option value="January">January</option>
				<option value="February">February</option>
				<option value="March">March</option>
				<option value="April">April</option>
				<option value="May">May</option>
				<option value="June">June</option>
				<option value="July">July</option>
				<option value="August">August</option>
				<option value="September">September</option>
				<option value="October">October</option>
				<option value="November">November</option>
				<option value="December">December</option>
				</select><td>
		</tr>
		<tr>
			<td>Bill Date</td>
			<td><script type="text/javascript" language="javascript">
	  				document.writeln(DateInput('bill_date', 'form_report', false, 'YYYY-MM-DD', '<?php if(isset($row['bill_date'])){echo $row['bill_date'];}else{echo date("Y-m-d");} ?>', <?php echo $tab++;?>))</script></td>
            </td>
            <td>Remarks</td>
            <td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
		</tr>
		<tr>
			<td colspan="4"><input type="hidden" name="bill_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
			<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Bill" onMouseDown="" tabindex="<?php echo $tab++;?>"></td>
		</tr>
	</table>
	</form>
<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Party Name</th>
					<th>Bill Name</th>
					<th>Bill Date</th>
					<th>Recurring Duration</th>
					<th>Estimated Amount</th>
					<th>Remarks</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
			$sql = 'select * from monthly_bills';
			$result=mysqli_fetch_assoc(execute_query($sql));
	$i=1;
	foreach($result as $row)
	{
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
		echo '<tr style="background:'.$col.'">
		<td>'.$i++.'</td>
		<td>'.$row['party_name'].'</td>
		<td>'.$row['bill_name'].'</td>
		<td>'.$row['bill_date'].'</td>
		<td>'.$row['recurring_duration'].'</td>
		<td>'.$row['amount'].'</td>
		<td>'.$row['remarks'].'</td>
		<td><a href="addmonthlybills.php?id='.$row['sno'].'">Edit</a></td>
		<td><a href="addmonthlybills.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
		</tr>';
	}
?>
</table>
</div>
<?php
page_footer();
?>
