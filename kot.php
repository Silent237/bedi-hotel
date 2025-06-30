<?php
session_cache_limiter('nocache');
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
$company=$company['rate'];

$sql = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_array(execute_query($sql));
$address=$address['rate'];

$sql = 'select * from kitchen_ticket_temp where table_id="'.$table.'" and time_stamp="'.$stamp.'"';
$result = execute_query($sql);

$row = mysqli_fetch_assoc($result);
echo '
<html>
	<head>
		<title>KOT</title>
		<style>
		@page {
			/*size: 60mm;*/
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
			font-size:13px;
			line-height:14px;
		}
		#wrapper { 
			width: 60mm;
			background-color: #FFF;
			box-sizing: border-box;
			page-break-after:always;
		}
		
		table {
		  border-collapse: collapse;
		  margin-bottom:16px;
		}
		td{padding:2px;}
		body {font-family:"Myriad Web Pro";}
		</style>
	</head>
	<body onload="window.print();">
		<div id="wrapper">
		<table width="100%" style="font-weight:bold">
			<tr>
				<th colspan="4" style="font-size:15px">'.$company.'</th>
			</tr>
			<tr>
				<th colspan="4" style="font-size:15px">'.$address.'</th>
			</tr>
			<tr>
				<th colspan="4" style="font-size:15px">K.O.T.</th>
			<tr>
				<td colspan="2"><b style="font-size:13px">KOT No.:</b></td>
				
				<td colspan="2" style="font-size:13px"><b>'.$_GET['kot'].'</b></td>
			</tr>
			<tr>';
			if(strpos($row['table_id'], "room")===false){
				echo '<td><b style="font-size:13px">Table :</b></td>
				<td><b style="font-size:13px">'.get_table($row['table_id']).'</b></td>';
			}
			else{
				$room_id = substr($row['table_id'], 5);
				$sql = 'select * from room_master where sno="'.$room_id.'"';
				// echo $sql;
				$room = mysqli_fetch_array(execute_query($sql));
				echo '<td><b style="font-size:13px">Room :</b></td>
				<td><b style="font-size:13px">'.$room['room_name'].'</b></td>';

			}
				
				echo '<td><b style="font-size:13px">Date : </b></td>
				<td><b style="font-size:13px">'.date("d-m-Y H:i:s", $row['time_stamp']).'</b></td>
				
			</tr>
		</table><hr/>';
		if($row['kot_type']=='1'){
			echo "NON CHARGEABLE";
		}
		echo'<table width="100%" border="1"><tr><th style="font-size:16px">S.No</th><th style="font-size:16px">Item</th><th style="font-size:16px">Unit</th></tr>';
$i=1;
$sql = 'select * from kitchen_ticket_temp where table_id="'.$table.'" and time_stamp="'.$stamp.'"';
$result = execute_query($sql);
while($row = mysqli_fetch_assoc($result)){
	echo '<tr>
	<td style="font-size:18px">'.$i++.'</td>
	<td style="font-size:18px">'.$row['item_name'];
	if($row['cooking_instructions']!=''){
		echo '<br/><small><u><strong>Cooking Instructions: '.$row['cooking_instructions'].'</strong></u></small>';
	}
		
	echo '</td>
	<td style="font-size:18px">'.$row['unit'].'</td>
	</tr>';
}
echo '</table>
<br>

</div>';


$table = $_GET['tid'];
$stamp = $_GET['ts'];

$sql = 'select * from general_settings where `desc`="company"';
$company = mysqli_fetch_array(execute_query($sql));
$company=$company['rate'];

$sql = 'select * from general_settings where `desc`="address"';
$address = mysqli_fetch_array(execute_query($sql));
$address=$address['rate'];

$sql = 'select * from kitchen_ticket_temp where table_id="'.$table.'" and time_stamp="'.$stamp.'"';
$result = execute_query($sql);

$row = mysqli_fetch_assoc($result);



echo '<div id="wrapper">
		<table width="100%" style="font-weight:bold">
			<tr>
				<th colspan="4" style="font-size:15px">'.$company.'</th>
			</tr>
			<tr>
				<th colspan="4" style="font-size:15px">'.$address.'</th>
			</tr>
			<tr>
				<th colspan="4" style="font-size:15px">K.O.T.</th>
			<tr>
				<td colspan="2"><b style="font-size:13px">KOT No.:</b></td>
				
				<td colspan="2" style="font-size:13px"><b>'.$_GET['kot'].'</b></td>
			</tr>
			<tr>';
			if(strpos($row['table_id'], "room")===false){
				echo '<td><b style="font-size:13px">Table :</b></td>
				<td><b style="font-size:13px">'.get_table($row['table_id']).'</b></td>';
			}
			else{
				$room_id = substr($row['table_id'], 5);
				$sql = 'select * from room_master where sno="'.$room_id.'"';
				$room = mysqli_fetch_array(execute_query($sql));
				echo '<td><b style="font-size:13px">Room :</b></td>
				<td><b style="font-size:13px">'.$room['room_name'].'</b></td>';

			}
				
				echo '<td><b style="font-size:13px">Date : </b></td>
				<td><b style="font-size:13px">'.date("d-m-Y H:i:s", $row['time_stamp']).'</b></td>
				
			</tr>
		</table><hr/>';
		if($row['kot_type']=='1'){
			echo "NON CHARGEABLE";
		}
		echo'<table width="100%" border="1"><tr><th style="font-size:16px">S.No</th><th style="font-size:16px">Item</th><th style="font-size:16px">Unit</th></tr>';
$i=1;
$sql = 'select * from kitchen_ticket_temp where table_id="'.$table.'" and time_stamp="'.$stamp.'"';
$result = execute_query($sql);
while($row = mysqli_fetch_assoc($result)){
	echo '<tr>
	<td style="font-size:16px">'.$i++.'</td>
	<td style="font-size:18px">'.$row['item_name'];
	if($row['cooking_instructions']!=''){
		echo '<br/><small><u><strong>Cooking Instructions: '.$row['cooking_instructions'].'</strong></u></small>';
	}
		
	echo '</td>
	<td style="font-size:16px">'.$row['unit'].'</td>
	</tr>';
}
echo '</table>
<br>

</div>

</body>
</html>';
?>