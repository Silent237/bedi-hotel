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
	if($_POST['room_name']=='') {
		$msg .= '<li>Please Enter Room Name.</li>';
	}
	if($msg==''){
		if($_POST['room_sno']!=''){
			$sql = 'update room_master set room_name="'.$_POST['room_name'].'", floor_id="'.$_POST['floor_id'].'", rent="'.$_POST['rent'].'", rent_double="'.$_POST['rent_double'].'", rent_extra="'.$_POST['rent_extra'].'", occupancy="'.$_POST['occupancy'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'", category_id="'.$_POST['category_id'].'",multiple="'.$_POST['multiple'].'" where sno='.$_POST['room_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
		}
		else{
			$sql='select * from room_master where room_name="'.$_POST['room_name'].'"';
			$result = execute_query($sql);
				if(mysqli_num_rows($result)==0){
					$sql='INSERT INTO room_master (room_name, floor_id, rent, rent_double, rent_extra, occupancy, created_by, created_on, remarks, category_id, multiple) VALUES ("'.$_POST['room_name'].'","'.$_POST['floor_id'].'", "'.$_POST['rent'].'", "'.$_POST['rent_double'].'", "'.$_POST['rent_extra'].'", "'.$_POST['occupancy'].'", "'.$_SESSION['username'].'", CURRENT_TIMESTAMP, "'.$_POST['remarks'].'", "'.$_POST['category_id'].'", "'.$_POST['multiple'].'")';
					$result = execute_query($sql);
					$msg="Room Added successfully";
				}
			else{
				$msg .= '<li>Room already exists.</li>';
			}
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from room_master where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql = 'delete from room_master where sno='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<script type="text/javascript" language="javascript" src="form_validator.js"></script>
<script type="text/javascript" language="javascript">
function get_room_rent(id){
	$.ajax({
		type: "GET",
		url: "scripts/ajax.php?id=category",
		data: { term: id}
	})
	.done(function(msg) {
		alert(msg);
		$('#rent').val(msg);
	});	
}
</script>
 <div id="container">
        <h2>Add New Room</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>';
		$tab=1; ?>
		<form action="admin_rooms.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Room Name</td>
					<td><input id="room_name" name="room_name" value="<?php if(isset($row['room_name'])){echo $row['room_name'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
					<input id="room_sno" name="room_sno" type="hidden"></td>
					<td>Floor Name</td>
					<td>
                            <select name="floor_id" id="floor_id" tabindex="<?php echo $tab++;?>">
                            <?php
								$sql = 'select * from floor_master';
								$result=execute_query($sql);
								while($row_floor = mysqli_fetch_assoc($result)){
                                    echo '<option value="'.$row_floor['sno'].'" ';
                                    if(isset($row['floor_id'])){
                                        if($row['floor_id']==$row_floor['sno']){
                                            echo ' selected="selected"';
                                        }
                                    }
                                    echo '>'.$row_floor['floor_name'].'</option>';
                                }
                            ?>
                            </select>
						</td>
				</tr>
				<tr>
					<td>Category</td>
					<td>
						<select name="category_id" id="category_id" tabindex="<?php echo $tab++;?>" onchange="get_room_rent(this.value)">
							<option></option>
							<?php
							$sql = 'select * from category';
							$result=execute_query($sql);
							while($row_category = mysqli_fetch_assoc($result)){
								echo '<option value="'.$row_category['sno'].'" ';
								if(isset($row['category_id'])){
									if($row['category_id']==$row_category['sno']){
										echo ' selected="selected"';
									}
								}
								echo '>'.$row_category['room_type'].'</option>';
								}
							?>
						</select>
					</td>
					<td>Occupancy</td>
					<td><input id="occupancy" name="occupancy" value="<?php if(isset($row['occupancy'])){echo $row['occupancy'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>					
				</tr>
				<tr>
					<td>Single Rent</td>
					<td><input id="rent" name="rent" value="<?php if(isset($row['rent'])){echo $row['rent'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
                    <td>Double Rent</td>
					<td><input id="rent_double" name="rent_double" value="<?php if(isset($row['rent_double'])){echo $row['rent_double'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
				</tr>
                <tr>
                    <td>Extra Rent</td>
					<td><input id="rent_extra" name="rent_extra" value="<?php if(isset($row['rent_extra'])){echo $row['rent_extra'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
                    <td>Multiple Occupancy</td>
                    <td><input type="checkbox" name="multiple" value="yes"<?php if(isset($row['multiple'])){ if($row['multiple']=="yes"){ echo 'checked="checked"';}}?>></td>
                </tr>
                <tr>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="<?php echo $tab++;?>"type="text" /></td>
                    <td colspan="2"></td>
				</tr>
				<tr>
					<td colspan="4"><input type="hidden" name="room_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Room" onMouseDown="" tabindex="<?php echo $tab++;?>"></td>
				</tr>
			</table>
		</form>
		<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Room Name</th>
                    <th>Category</th>
					<th>Floor Name</th>
					<th>Rent</th>
					<th>Double Rent</th>
					<th>Extra Rent</th>
					<th>Occupancy</th>
                    <th>Multiple Occupancy</th>
					<th>Remarks</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
			$sql = 'select * from room_master';
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
		echo '<tr style="background:'.$col.'; text-align:center;">
		<td>'.$i++.'</td>
		<td>'.$row['room_name'].'</td>
		<td>'.get_category($row['category_id']).'</td>
		<td>'.get_floor($row['floor_id']).'</td>
		<td>'.$row['rent'].'</td>
		<td>'.$row['rent_double'].'</td>
		<td>'.$row['rent_extra'].'</td>
		<td>'.$row['occupancy'].'</td>
		<td>'.$row['multiple'].'</td>
		<td>'.$row['remarks'].'</td>
		<td><a href="admin_rooms.php?id='.$row['sno'].'">Edit</a></td>
		<td><a href="admin_rooms.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
		</tr>';
	}
?>
</table>
</div>
<?php
navigation('');
page_footer();
?>
