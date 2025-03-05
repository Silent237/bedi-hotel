<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');

$table = $_GET['tid'];
$stamp = $_GET['ts'];

$sql = 'select * from general_settings where `desc`="company"';
$company = mysqli_fetch_array(execute_query($sql));

$sql = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_array(execute_query($sql));

$sql = 'select * from stock_sale_restaurant where table_id="'.$table.'" and part_dateofpurchase="'.$stamp.'"';
$result = execute_query($sql);

$row = mysqli_fetch_assoc($result);
echo '
<html>
	<head>
		<title>KOT</title>
		<style>
		@page {
			size: 60mm;
		}

		@media print
		{    
			.no-print
			{
				display: none !important;
			}
			.print-only{
				display: block;
			}
		}

		@media screen{
			.print-only{display:none;}
		}


		* {
			margin: 1mm;
			margin-top:1px;
			font-size:12px;
			line-height:14px;
		}
		#wrapper { 
			width: 60mm;
			background-color: #FFF;
			box-sizing: border-box;
			position: absolute;
		}
		
		table {
		  border-collapse: collapse;
		}
		body {font-family:"Myriad Web Pro";}
		</style>
	</head>
	<body onload="window.print();">
		<div id="wrapper">
		<table width="100%">
			<tr>
				<th colspan="4">'.$company['rate'].'</th>
			</tr>
			<tr>
				<th colspan="4">'.$address['rate'].'</th>
			</tr>
			<tr>
				<th colspan="4">K.O.T.</th>
			<tr>
				<td>KOT No.:</td>
				<td colspan="3">'.$_GET['kot'].'</td>
			</tr>
			<tr>';
			if(strpos($row['table_id'], "room")===false){
				echo '<td>Table :</td>
				<td>'.get_table($row['table_id']).'</td>';
			}
			else{
				$room_id = substr($row['table_id'], 5);
				$sql = 'select * from room_master where sno='.$room_id;
				$room = mysqli_fetch_array(execute_query($sql));
				echo '<td>Room :</td>
				<td>'.$room['room_name'].'</td>';

			}
				
				echo '<td>Date : </td>
				<td>'.date('d-m-Y',strtotime($row['part_dateofpurchase'])).'</td>
			</tr>
		</table><hr/>';
		
		echo'<table width="100%" border="1"><tr><th>S.No</th><th>Item</th><th>Unit</th></tr>';
$i=1;
$sql = 'select * from stock_sale_restaurant where table_id="'.$table.'" and part_dateofpurchase="'.$stamp.'"';
$result = execute_query($sql);
while($row = mysqli_fetch_assoc($result)){
	$name_item="SELECT * FROM `stock_available` where sno='".$row['part_id']."'";
				$name_item1=execute_query($name_item);
				$name_item_row=mysqli_fetch_array($name_item1);
	echo '<tr>
	<td>'.$i++.'</td>
	<td>'.$name_item_row['description'].'</td>
	<td>'.$row['qty'].'</td>
	</tr>';
}
echo '</table>
</div>
</body>
</html>';
?>