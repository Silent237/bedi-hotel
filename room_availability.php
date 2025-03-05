<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
	logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
	logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
navigation('');
page_footer();

?>

</script>
 <div id="container">
	<h2>Room Status</h2>	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
   		<table width="50%">
   			<tr>
   				<th>Report Date</th>
   				<th><input name="allotment_date" type="text" value="<?php if(isset($_POST['allotment_date'])){echo $_POST['allotment_date'];}else{echo date("Y-m-d H:i:s");}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="allotment_date" /></th>
  				<th><input type="submit" name="search" value="Show Report"></th>
   			</tr>
   		</table>
   	</form>
    <?php
	if(!isset($_POST['allotment_date'])){		
		$i=1;
		$floor='';
		echo '<div>';

		$sql = 'select room_master.sno as sno, room_name, status, floor_name from room_master join floor_master on floor_master.sno = floor_id order by floor_id, room_name';
		$result = execute_query($sql);
		//$result=$stmt->fetchAll();		
		foreach($result as $row)
		{
			if($floor!=$row['floor_name']){
				$floor=$row['floor_name'];
				echo '</div><div style="border:0px solid; float:left; width:95%"><h2>'.$row['floor_name'].'</h2><div style="clear:both;"></div>';
			}
			if($row['status']=='' || $row['status']==0){
				$col = '#bbff33';
				$text_col = '#666666';
			}
			else{
				$col = '#F00';
				$text_col = '#fff';
			}
			//echo '<div style="height:50px; line-height:50px; text-align:center; color:'.$text_col.'; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:'.$col.'; font-size:18px;" onclick="window.open(\'vacant_room_room.php?rid='.$row['sno'].'\');">'.$row['room_name'].'</div>';
			echo '<div style="height:50px; line-height:50px; text-align:center; color:'.$text_col.'; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:'.$col.'; font-size:18px;" onclick="">'.$row['room_name'].'</div>';
		}
	}
	else{
		$i=1;
		$floor='';
		echo '<div>';
		$sql = 'select room_master.sno as sno, room_name, status, floor_name from room_master join floor_master on floor_master.sno = floor_id order by floor_id, room_name';
		$result=execute_query($sql);
		$rows = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
		$_POST['allotment_date'] = date("Y-m-d 23:59:59", strtotime($_POST['allotment_date']));
		foreach($rows as $row){
			if($floor!=$row['floor_name']){
				$floor=$row['floor_name'];
				echo '</div><div style="border:0px solid; float:left; width:95%"><h2>'.$row['floor_name'].'</h2><div style="clear:both;"></div>';
			}
			$sql = 'select * from allotment where room_id="'.$row['sno'].'" and "'.$_POST['allotment_date'].'" between allotment_date and exit_date';
			$result = execute_query($sql);
			if(mysqli_num_rows($result)!=0){
				$col = '#F00';
				$text_col = '#fff';
			}
			else{
				$col = '#bbff33';
				$text_col = '#666666';	
			}
			

			//echo '<div style="height:50px; line-height:50px; text-align:center; color:'.$text_col.'; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:'.$col.'; font-size:18px;" onclick="window.open(\'vacant_room_room.php?rid='.$row['sno'].'\');">'.$row['room_name'].'</div>';
			echo '<div style="height:50px; line-height:50px; text-align:center; color:'.$text_col.'; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:'.$col.'; font-size:18px;" onclick="">'.$row['room_name'].'</div>';
		}
	}
?>

</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#allotment_date').datetimepicker({
	step:15,
	format: 'Y-m-d H:i',
	value: '<?php
	if(isset($_POST['allotment_date'])){
		echo $_POST['allotment_date'];
	}
	else{
		echo date("Y-m-d H:i");	
	}
	?>',
	});
</script>

