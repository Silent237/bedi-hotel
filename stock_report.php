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
	if($_POST['name_of_object']=='') {
		$msg .= '<li>Please Enter Stock Name .</li>';
	}
	if($msg==''){
		if($_POST['room_sno']!=''){
			$sql = 'update stock_report set name_of_object="'.$_POST['name_of_object'].'", number="'.$_POST['number'].'", edited_by="'.$_SESSION['username'].'", edited_on=CURRENT_TIMESTAMP,remarks="'.$_POST['remarks'].'" where sno='.$_POST['room_sno'];
			$result = execute_query($sql);
			$msg .= '<li>Update sucessful.</li>';
		}
		else{
			$sql='select * from stock_report where name_of_object="'.$_POST['name_of_object'].'"';
			$result = execute_query($sql);
			if(mysqli_num_rows($result)==0){
				$sql='INSERT INTO stock_report (name_of_object,number,created_by,created_on,remarks) VALUES ("'.$_POST['name_of_object'].'", "'.$_POST['number'].'","'.$_SESSION['username'].'",CURRENT_TIMESTAMP,"'.$_POST['remarks'].'")';
				$result = execute_query($sql);
				$msg="Stock Added successfully";
			}
			else{
				$msg .= '<li>Stock already exists.</li>';
			}
		}
	}
}
if(isset($_GET['id'])){
	$sql = 'select * from stock_report where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql = 'delete from stock_report where sno='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<script type="text/javascript" language="javascript" src="form_validator.js"></script>
 <div id="container">
        <h2>Add Stock</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="stock_report.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Name Of Object</td>
					<td><input id="name_of_object" name="name_of_object" value="<?php if(isset($row['name_of_object'])){echo $row['name_of_object'];}?>" class="field text medium" maxlength="255" tabindex="1" type="text" /></td>
				</tr>
				<tr>
					<td>Number</td>
					<td><input id="number" name="number" value="<?php if(isset($row['number'])){echo $row['number'];}?>" class="field text medium" maxlength="255" tabindex="2" type="text" />
				</tr>
				<tr>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" maxlength="255" tabindex="3" type="text" />
				</tr>
				<tr>
					<td colspan="2"><input type="hidden" name="room_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
					<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Stock" onMouseDown="" tabindex="23"></td>
				</tr>
			</table>
		</form>
		<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Name Of Object</th>
					<th>Number</th>
					<th>Remarks</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
    <?php
		$sql = 'select * from stock_report';
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
			<td>'.$row['name_of_object'].'</td>
			<td>'.$row['number'].'</td>
			<td>'.$row['remarks'].'</td>
			<td><a href="stock_report.php?id='.$row['sno'].'">Edit</a></td>
			<td><a href="stock_report.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
			</tr>';
		}
?>
</table>
</div>
<?php
page_footer();
?>
