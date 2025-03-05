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
if(isset($_POST['submit']))
{
		if($_POST['floor_name']=='') {
		$msg .= '<li>Please Enter Floor Name.</li>';
		}
		if($msg=='')
		{
		if($_POST['floor_sno']!='')
		{
			$sql = 'update floor_master set floor_name="'.$_POST['floor_name'].'", no_of_rooms="'.$_POST['no_of_rooms'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'" where sno='.$_POST['floor_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
		}
		else
		{
				$sql='select * from floor_master where floor_name="'.$_POST['floor_name'].'"';
				$result = execute_query($sql);
				if(mysqli_num_rows($result)==0)
				{
					$sql='INSERT INTO floor_master(floor_name,no_of_rooms,created_by,created_on,remarks) VALUES ("'.$_POST['floor_name'].'","'.$_POST['no_of_rooms'].'","'.$_SESSION['username'].'",CURRENT_TIMESTAMP,"'.$_POST['remarks'].'")';
					$result = execute_query($sql);
					$msg="Floor Added successfully";
				}
				else
				{
				$msg .= '<li>Floor already exists.</li>';
				}
		}
	}
}

if(isset($_GET['id']))
	{
			$sql = 'select * from floor_master where sno='.$_GET['id'];
			$result = execute_query($sql);
			$row=mysqli_fetch_assoc( $result );
	}
if(isset($_GET['del']))
{
			$sql = 'delete from floor_master where sno='.$_GET['del'];
			$result = execute_query($sql);

			
}
?>
<script type="text/javascript" language="javascript" src="form_validator.js"></script>
 <div id="container">
        <h2>Add New Floor</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="admin_floors.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Floor Name</td>
					<td><input id="floor_name" name="floor_name" value="<?php if(isset($row['floor_name'])){echo $row['floor_name'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" />
					<input id="floor_sno" name="floor_sno" type="hidden"></td>
				</tr>
				<tr>
					<td>No. Of Rooms</td>
					<td><input id="no_of_rooms" name="no_of_rooms" value="<?php if(isset($row['no_of_rooms'])){echo $row['no_of_rooms'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" />
				</tr>
				<tr>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" />
				</tr>
				<tr>
					<td colspan="2"><input type="hidden" name="floor_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Floor" onMouseDown="" tabindex="23"></td>
				</tr>
			</table>
		</form>
		<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Floor Name</th>
					<th>No. Of Rooms</th>
					<th>Remarks</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
			$sql = 'select * from floor_master';
			$result=execute_query($sql);
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
		<td>'.$row['floor_name'].'</td>
		<td>'.$row['no_of_rooms'].'</td>
		<td>'.$row['remarks'].'</td>
		<td><a href="admin_floors.php?id='.$row['sno'].'">Edit</a></td>
		<td><a href="admin_floors.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
		</tr>';
	}
?>
</table>
</div>
<?php
navigation('');
page_footer();
?>
