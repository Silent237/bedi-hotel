<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
$username=$_SESSION['username'];
date_default_timezone_set('Asia/Calcutta');
page_header();
?>
<style type="text/css">
#tables button{
  border: none;
  outline: none;
  background-color:#66AAAA;
  color: white;
  height:70px;
  width:110px;
  cursor: pointer;
  margin: 5px;
  font-size: 30px;
}
</style>
<div id="container">
    <h2>Room Service</h2>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<div id="tables">
		<?php
			$sql="SELECT * FROM `room_master` Where status='1' order by abs(room_name)";
			$res=execute_query($sql);
			while($row=mysqli_fetch_array($res)){
				$id=$row['sno'];
                if($row['booked_status'] ==1){
			        echo '<button type="button" onclick="window.open(\'dine_in_order.php?room_id='.$id.'\', \'_self\');" id="table_'.$id.'" style="background-color:red;">'.$row['room_name'].'</button></a>';
			    }
                else{
                     echo '<button type="button"  onclick="window.open(\'dine_in_order.php?room_id='.$id.'\', \'_self\');" id="table_'.$id.'">'.$row['room_name'].'</button></a>';
                }
            }

		?>
	</div>
</div>
<?php
page_footer(); 
?> 	