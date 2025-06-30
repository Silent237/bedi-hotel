<?php
include("scripts/settings.php");

echo $id = $_POST['id'];
echo $status = $_POST['status'];

echo $sql = "UPDATE room_master SET room_status = '$status' WHERE sno = '$id'";
execute_query($sql);
?>