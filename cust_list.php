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
?>
<div>
<h2>Customer List</h2>
<h3 style="text-align:right;"><a href="cust_ledger.php" style="text-decoration:none; font-size:18px;" >Customer Ledger</a></h3>	
	<table width="100%">
		<tr style="background:#000; color:#FFF;">
			<th>S.No.</th>
			<th>Customer Name</th>
			<th>Amount</th>
			<th>Date Of Amount</th>
            <th>Booking Date(From)</th>
             <th>Booking Date(To)</th>
              <th>No.Of.Rooms</th>
            <th>Delete</th>
            <th>Allot Room</th>
		</tr>
    <?php
			$sql = 'select * from customer_transactions where allotment_id is null';
			$result = execute_query($sql);
			$i=1;
			while($row= mysqli_fetch_array($result)){
				if($i%2==0){
					$col = '#CCC';
				}
				else{
					$col = '#EEE';
				}
				$sql='select * from booking_details where cust_id='.$row['cust_id'];
				$result = execute_query($sql);
				$details=mysqli_fetch_assoc( $result );
				echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td>'.get_cust_name($row['cust_id']).'</td>
				<td>'.$row['amount'].'</td>
				<td>'.date("d-m-Y",strtotime($row['timestamp'])).'</td>
				<td>'.date("d-m-Y",strtotime($details['booking_from'])).'</td>
				<td>'.date("d-m-Y",strtotime($details['booking_to'])).'</td>
				<td>'.$details['no_of_rooms'].'</td>
				<td><a href="cust_list.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
				<td><a href="allotment.php?alt='.$row['cust_id'].'">Allot Room</a></td>
				</tr>';
			}
?>
</table>
</div>
<?php
page_footer();
?>