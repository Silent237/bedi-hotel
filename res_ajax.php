<?php
include ("scripts/settings.php");
if(isset($_GET['id'])){
	$id=$_GET['id'];
	$sql="SELECT * FROM `item` where group_id='$id'";
	//echo $sql;
	
	$res=execute_query($sql);
	while($row=mysqli_fetch_array($res)){
		$id=$row['sno'];
		$item_name=$row['item_name'];
		$result_array[] = array("id" => $id,
                    "item_name" => $item_name);
	}
	echo json_encode($result_array);
}

if(isset($_GET['item_id'])){
	$id=$_GET['item_id'];
	$sql="SELECT * FROM `item` where sno='$id'";
	$res=execute_query($sql);
	$row=mysqli_fetch_array($res);
	$item_name=$row['item_name'];
	$price=$row['price'];
	$result_array[] = array("item_name" => $item_name,
                    "price" => $price);
	echo json_encode($result_array);
}

if(isset($_POST['unit'])){
	$unit=$_POST['unit'];
	$price=$_POST['price'];
	$item=$_POST['item'];
	$table=$_POST['table'];
	$time = microtime(true);
	$sql="INSERT INTO `kitchen_ticket_temp`(`unit`, `item_name`, `item_price`, `table_id`) VALUES ('$unit','$item','$price', '$table')";
	execute_query($sql);
}
?>