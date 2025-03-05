<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
date_default_timezone_set('Asia/Calcutta');
page_header();
$msg='';
if(isset($_POST['submit'])){
	$newtableno=$_POST['table'];
	$oldtableid=$_POST['table_id'];
	$q="SELECT * FROM `res_table` WHERE table_number='".$newtableno."'";
	$trow=mysqli_fetch_array(execute_query($q));

	$q="UPDATE `kitchen_ticket_temp_2` SET `table_id`='".$trow['sno']."' where table_id='".$_POST['table_id']."'";
	$res=execute_query($q);
	if($res){
		$msg="Table Changed";
		if($_POST['edit_sno_table'] != ''){
			$i_2="UPDATE `invoice_sale_restaurant_2` SET `storeid`='".$trow['sno']."' , `table_no`='".$trow['table_number']."' where `invoice_sale_id`='".$_POST['edit_sno_table']."'";
			execute_query($i_2);
			$s_2="UPDATE `stock_sale_restaurant_2` SET `table_id`='".$trow['sno']."' where `invoice_no`='".$_POST['edit_sno_table']."'";
			execute_query($s_2);
			header("Location: dine_in_order_copy.php?edit_id=".$_POST['edit_sno_table']."");
		}
		else{
			header("Location: dine_in_order_table.php?table_id=".$trow['sno']."");
		}
	}
}
if(isset($_POST['save'])){
	$newroomno=$_POST['room'];
	$oldroomid=$_POST['room_id'];
	$q="SELECT * FROM `room_master` WHERE room_name='".$newroomno."'";
	$rrow=mysqli_fetch_array(execute_query($q));
	$newtab='room_'.$rrow['sno'];
	$oldtab='room_'.$_POST['room_id'];
	$q="UPDATE `kitchen_ticket_temp_2` SET `table_id`='".$newtab."' where table_id='".$oldtab."'";
	$res=execute_query($q);
	if($res){
		$msg="Room Changed";
		if($_POST['edit_sno_room'] != ''){
			$i_2="UPDATE `invoice_sale_restaurant_2` SET `storeid`='".$newtab."' where `invoice_sale_id`='".$_POST['edit_sno_room']."'";
			execute_query($i_2);
			$s_2="UPDATE `stock_sale_restaurant_2` SET `table_id`='".$newtab."' where `invoice_no`='".$_POST['edit_sno_room']."'";
			execute_query($s_2);
			header("Location: dine_in_order_copy.php?edit_id=".$_POST['edit_sno_room']."");
		}
		else{
			header("Location: dine_in_order_room.php?room_id=".$rrow['sno']."");
		}
	}
}
?>
<?php
if(isset($_GET['tableid'])){
?>
<div id="container">
        <h2>Change Table</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		
			<table>
				<tr>
					<td>Selected Table No. : <?php echo get_table($_GET['tableid']); ?></td>
					<td></td>
				</tr>
				<tr>
					<td>New Table Number</td>
					<td><input type="text" name="table"   value=""></td>
					<td><input type="submit" name="submit" value="Change"></td>
					<input type="hidden" name="table_id" value="<?php if(isset($_GET['tableid'])){ echo $_GET['tableid']; }?>">
					<input type="hidden" name="edit_sno_table" value="<?php if(isset($_GET['e_id'])){ echo $_GET['e_id']; }?>">
				</tr>
			</table>
		</form>
	</div>
<?php
}
if(isset($_GET['roomid'])){
?>
<div id="container">
        <h2>Change Table</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		
			<table>
				<tr>
					<td>Selected Room No. : <?php echo get_room($_GET['roomid']); ?></td>
					<td></td>
				</tr>
				<tr>
					<td>New Room Number</td>
					<td><input type="text" name="room"   value=""></td>
					<td><input type="submit" name="save" value="Change"></td>
					<input type="hidden" name="room_id" value="<?php if(isset($_GET['roomid'])){ echo $_GET['roomid']; }?>">
					<input type="hidden" name="edit_sno_room" value="<?php if(isset($_GET['e_id'])){ echo $_GET['e_id']; }?>">
				</tr>
			</table>
		</form>
	</div>
<?php
}
?>
<?php
page_footer();
?>