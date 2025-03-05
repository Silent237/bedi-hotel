<?php

include("scripts/settings.php");
if(isset($_POST['searchroom'])){
			$sql="select * from room_master where room_name like '%".$_POST['searchroom']."%' and status=1";
			$run=execute_query($sql);
			while($row=mysqli_fetch_array($run)){
				echo  '<center><div style="height:50px; line-height:50px; text-align:center; color:white; width:50px; border:1px solid; margin:10px; border-radius:10px; background:red; font-size:18px;" onclick="Update('.$row['sno'].')">'.$row['room_name'].'</div></center>';
			}
		}

		if(isset($_POST['getid'])){
			$sql="update  room_master set status=0 where sno='".$_POST['getid']."'";
			//echo $sql;
			$run=execute_query($sql);
		}
			
		


 ?>