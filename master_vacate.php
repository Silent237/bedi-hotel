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
if(isset($_GET['id'])){
$update_sql="UPDATE `room_master` SET `status`=0 WHERE sno='".$_GET['id']."'";
$update_run=execute_query($update_sql);

}


?>

<html>
<head></head>
<style type="text/css">
	#room{
		height:30px;
		width:200px;
	}
	p{
		font-family:Arial Narrow;
		font-size:22px;
	}
	#container{
		height:300px;
	}
</style>
<body>
 <div id="container">
	<h2>Room Status</h2>
	<center><p>Search Room</p>	<br>
	<input type="text" name="room" id="room" class="field text medium" onkeyup="myfun();" autocomplete="off">
	<div id="demo"></div>
</center>
	
</div>
<script>
	function myfun(){
	var searchroom=$('#room').val();
	$.ajax({
		url:"ajax2.php",
		type:"post",
		data:{searchroom:searchroom},
		success:function(data,status){
			$("#demo").html(data);
		}
	});
}

function Update(id){
	 alert("Are You sure want to Change this Status");
	 var getid=id;
	$.ajax({
		url:"ajax2.php",
		type:"post",
		data:{getid:getid},
		success:function(data,status){
			alert('Update Successfully');
		    window.location.href='master_vacate.php';
		}
	});

}
</script>
</body>
</html>
   <?php
			
	/*$i=1;
	$floor='';
	 echo '<div>';
	foreach($result as $row)
	{
		
		if($row['status']=='' || $row['status']==0){
			$col = '#bbff33';
			$text_col = '#666666';
		}
		else{
			$col = '#F00';
			$text_col = '#fff';
		}
		 if($row['status'] ==1){
		 	echo '<a href="master_vacate.php?id='.$row['sno'].'" onclick="return confirm(\'Are you sure want to change room status?\');">';}
		 	
		 	 echo '<div style="height:50px; line-height:50px; text-align:center; color:'.$text_col.'; width:50px; border:1px solid; margin:10px; border-radius:10px; float:left; background:'.$col.'; font-size:18px;">'.$row['room_name'].'</div></a>';

	}*/

?>
</table>
</div>
<?php
page_footer();
?>
