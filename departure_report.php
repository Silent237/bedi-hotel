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
$tab=1;
$con = $db;
?>
<div id="container">
    <h2>Departure Report</h2>
	<form name="main_form" method="POST" action="departure_report.php" enctype="multipart/formdata">
		<table width="100%">
            	<tr style="background:#CCC;">
                
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
					document.writeln(DateInput('allot_from', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1))
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4))
                    </script>
                    </span>
                    </td>
                </tr>
				<tr class="no-print">
                	<th colspan="4">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    	
                    </th>
                </tr>
		</table>
		</form>
		<?php
			if(isset($_POST['submit_form'])){
				$_POST['allot_from'] = date("Y-m-d", strtotime($_POST['allot_from']));
				$_POST['allot_to'] = date("Y-m-d 23:59:59", strtotime($_POST['allot_to']));
				
				//echo $_POST['allot_to'];
		
				$rowspan='rowspan="2"';
				$colspan='colspan="8"';	
		?>		
		<table width="100%">
				<tr  class="no-print">
					<td colspan="12" float="left" class="no-print text-center" >
					<a href="departure_report_export.php"><input type="button" style="margin-top:20px; background-color: #f44f4f; color:white;width:200px;" name="student_ledger" class="form-control btn btn-danger"  style="float: left;" value="Download In Excel"></a></span>
					</td>
				</tr>
					<tr style="background:#000; color:#FFF;">
						<th>S.No.</th>
						<th>Guest Name</th>
						<th>Company Name</th>
						<th>Mobile</th>
						<th>Address</th>
						<th>ID Number</th>
						<th>Occupancy</th>
						<th>Room No.</th>
						<th class="no-print">Extra Bed</th>
						<th class="no-print">Total Rent</th>
						<th>Allotment Date</th>
						<th>Departure Date</th>
						
					</tr>	
			<?php
			
			$sql = 'SELECT `allotment`.* FROM `allotment` LEFT JOIN `room_master` on `room_master`.`sno` = `allotment`.`room_id` WHERE 1=1';
			
			
			if(isset($_POST['submit_form'])){
				$sql .= ' and `allotment`.`departure_date` >="'.$_POST['allot_from'].'" and `allotment`.`departure_date` <="'.$_POST['allot_to'].'"';
				
				$sql .= ' and `allotment`.`exit_date` IS NULL OR `allotment`.`exit_date`=""';
			}
			$sql .= ' ORDER BY allotment.departure_date ASC, `room_master`.`room_name` ASC ';
			//echo $sql;
			$_SESSION['sql5']= $sql;
			$result=execute_query($sql);
			$i=1;
			$occcu='';
			foreach($result as $row){
				if($i%2==0){
					$col = '#CCC';
				}
				else{
					$col = '#EEE';
				}
				if($row['hold_date']!=''){
					$col = 'red';
				}
				if($row['exit_date']==''){
					$row['exit_date'] = date("d-m-Y H:i");
				}
				$days = (strtotime($row['exit_date'])-strtotime($row['allotment_date']));
				$days = date("d", $days);
				$total_rent=floatval($row['room_rent'])*$days;
				if($row['cancel_date']!=''){
					$col = '#F00"';
					$cancel = '<br />Cancelled On : '.$row['cancel_date'];
					$cancel_display = 'Uncancel';
				}
				else{
					$row_col = '';
					$cancel = '';
					$cancel_display = 'Cancel';
				}
				echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td>'.$row['guest_name'].$cancel.'</td>
				<td>'. get_company_name($row['cust_id']).'</td>';
				$sql_cus="select * from customer where sno='".$row['cust_id']."'";
				$sql_run=execute_query($sql_cus);
				$row_cust=mysqli_fetch_array($sql_run);
			   echo '<td>'.$row_cust['mobile'].'</td>';
			   if($row_cust['address'] != ''){
				echo '<td>'.$row_cust['address'].'</td>';
			   }
			   else{
				echo '<td>'.$row['guest_address'].'</td>';
			   }
			   echo '
			   <td>'.$row_cust['id_type'].' : '.$row_cust['id_3'].'</td>
			   <td>'.$row['occupancy'].'</td>
				<td>'.get_room($row['room_id']).'</td>
				<td class="no-print">'.$row['other_charges'].'</td>
				<td class="no-print">'.$total_rent.'</td>
				
				<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
				<td>'.date("d-m-Y,h-i A" ,strtotime($row['departure_date'])).'</td>
				
				
				
			</tr>'
			;
			//  $occcu +=$row['occupancy'];
			//$occcu = 0;
			$occcu =intval($occcu)+ intval($row['occupancy']);

			}
			echo '<tr>
			<td></td>
			<td></td>
			<td></td>

			<td></td>
			<td class="no-print"></td>
			<td class="" style=""><b style="font-size:20px" >Occupancy<b></td><td><b style="font-size:20px">'.$occcu.'</b></td><td></td><td></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td></tr>';
			}
		?>
		</table>
</div>

<?php

page_footer();
?>