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
	if($_POST['category']=='') {
		$msg .= '<li>Please Enter Category.</li>';
	}
	if($msg==''){
		if($_POST['room_sno']!=''){
			$sql = 'update category set room_type="'.$_POST['category'].'", rent="'.$_POST['rent'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'" where sno='.$_POST['room_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
		}
		else{
			$sql='select * from category where room_type="'.$_POST['category'].'"';
			$result = execute_query($sql);
			if(mysqli_num_rows($result)==0){
				$sql='INSERT INTO category (room_type,rent,created_by,created_on,remarks) VALUES ("'.$_POST['category'].'", "'.$_POST['rent'].'","'.$_SESSION['username'].'",CURRENT_TIMESTAMP,"'.$_POST['remarks'].'")';
				$result = execute_query($sql);
				$msg="Category Added successfully";
			}
			else{
				$msg .= '<li>Category already exists.</li>';
			}
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from category where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql = 'delete from category where sno='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<script type="text/javascript" language="javascript" src="form_validator.js"></script>
 <div id="container">
        <h2>Add Room Category</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="admin_room_types.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post"  >
			<table>
				<tr>
					<td>Category</td>
					<td><input id="category" name="category" value="<?php if(isset($row['room_type'])){echo $row['room_type'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" /></td>
				
					<td>Rent</td>
					<td><input id="rent" name="rent" value="<?php if(isset($row['rent'])){echo $row['rent'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" />
				
					<td>No. of Rooms</td>
					<td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" />
			
					<td colspan="2"><input type="hidden" name="room_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Room Category" onMouseDown="" tabindex="23"></td>
				</tr>
			</table>
		</form>
		<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Category</th>
					<th>Rent</th>
					<th>No. of Rooms</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
		$sql = 'select * from category order by room_type';
		$result=execute_query($sql);
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
			<td>'.$row['room_type'].'</td>
			<td>'.$row['rent'].'</td>
			<td>'.$row['remarks'].'</td>
			<td><a href="admin_room_types.php?id='.$row['sno'].'"><i class="fas fa-edit"></i></a></td>
			<td><a href="admin_room_types.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>
			</tr>';
		}
?>
</table>
</div>
<?php
navigation('');
page_footer();
?>
