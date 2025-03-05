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
<form action="bill_pending.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
<table>
	<tr>
		<td>Bill Date</td>
		<td><script type="text/javascript" language="javascript">
	  	document.writeln(DateInput('bill_date', false, 'YYYY-MM-DD', '<?php echo date("Y-m-d"); ?>', 12))</script></td>
        </td>
		<td><input id="submit" name="submit" class="btTxt submit" type="submit" value="Submit" onMouseDown="" tabindex="23"></td>
	</tr>
</table>
</form>
<?php 
if(isset($_POST['submit']))
	{
		$sql='select * from general_setting';
		$result = execute_query($sql);
		$row=mysqli_fetch_assoc( $result );
		$date = $row['value'];
		$time = strtotime($date);
		$month = date("m",$time);
		$date1 = $_POST['bill_date'];
		$time = strtotime($date1);
		$month1 = date("m",$time);
		$month2=date("F",$time);
		$diff=$month1-$month;
		if($diff>0){
			$diff++;
		}
		else{
			$diff=$diff+12;
		}
		$sql='select * from monthly_bills';
		$result = execute_query($sql);
		echo "<table>";
		echo "<tr><th>Bill Name</th><th>Estimated Amount</th><th>Status</th><th>Paid Amount</th><th>Bill Date</th></tr>";
		while($row=mysqli_fetch_assoc($result)){
			$i=0;
			if(is_numeric($row['recurring_duration'])){
				if($diff%$row['recurring_duration']==0){
					echo "<tr><td>".$row['bill_name']."</td><td>".$row['amount']."</td>";
					$i++;
				}
			}
			else{
				 if($month2==$row['recurring_duration']) {
					 echo "<tr><td>".$row['bill_name']."</td><td>".$row['amount']."</td>";
					 $i++;
				}
			}
			$toatl='';
			if($i==1){
				$sql='select * from monthly_bills_transactions where bill_id='.$row['sno'].' and status="paid" and month_year="'.date('m-Y', strtotime($_POST['bill_date'])).'"';
				$details=mysqli_fetch_array(execute_query($sql));
				if($details['status']!=''){
					echo '<td>'.$details['status'].'</td><td>'.$details['amount'].'</td><td>'.date("d-m-Y", strtotime($details['bill_date'])).'</td></tr>';
					$total+=$details['amount'];
				}
				else{
					echo '<td>Not Paid</td><td>0</td><td>-</td></tr>';
				}
			}
		}
			echo '<tr><td></td><td></td><td>Total</td><td colspan=2>'.$total.'</td></tr>';
			echo "</table>";
	}
?>
<?php
page_footer();
?>