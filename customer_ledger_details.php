<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
$sno=1;
date_default_timezone_set('Asia/Calcutta');
page_header();
?>
 <div id="container">
        <h2>Ledger Details</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<?php
				if(isset($_GET['id'])){
					$cust_id=$_GET['id'];
					$sql="SELECT * FROM `customer` WHERE sno='$cust_id'";
					$result=execute_query($sql);
					$cust_details=mysqli_fetch_array($result);
				}
			?>
			<div id="left" style="float:left">
				<?php
					echo '<br> Name : '.$cust_details['cust_name'].'<br>';
					echo 'Address : '.$cust_details['address'].'<br>';
					echo 'Mobile : '.$cust_details['mobile'];
				?>
			</div>
			<div id="right" style="float:right">
				<br>Leder From : <br>
				Ledger To : <br>
			</div>
			<table style="margin-top: 15px;">
				<tr>
					<th>Sno</th>
					<th>Date</th>
					<th>Description</th>
					<th>Debit</th>
					<th>Credit</th>
					<th>Balance</th>
				</tr>
				<?php
				$balance=0;
					$sql="SELECT * FROM `customer_transactions` WHERE cust_id='$cust_id'";
					//echo $sql;
					$result=execute_query($sql);
					while($row=mysqli_fetch_array($result)){
						if($row['type'] =='payment' or $row['type'] =='RENT'){
							echo'<tr><td>'.$sno++.'</td>
								<td>'.$row['timestamp'].'</td>
								<td>'.$row['remarks'].'</td>
								<td>'.$row['amount'].'</td>
								<td> 00</td>
								<td>'.$balance+=$row['amount'].'</td></tr>';
						}
						else{
							echo'<tr><td>'.$sno++.'</td>
								<td>'.$row['timestamp'].'</td>
								<td>'.$row['remarks'].'</td>
								<td> 00</td>
								<td>'.$row['amount'].'</td>
								<td>'.$balance-=$row['amount'].'</td></tr>';
						}
						
					}
				?>
			</table>
		</form>
</div>
<?php
page_footer();	
?>