<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
date_default_timezone_set('Asia/Calcutta');

$msg='';
$sno=1;
$tab=1;
session_start();
page_header();

?>
<div class="container">
	<?php echo $msg; ?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
			<h2>Customer Ledger</h2>
					<table>
							<tr>
								<td>Name</td>
								<td><input type="text" name="name" class="form-control input-sm"></td>
								<td>Date From </td>
								<td><script required type="text/javascript" language="javascript">
									document.writeln(document.writeln(DateInput('date_from', 'admin_employee_from',true, 'YYYY-MM-DD','<?php if(isset($_GET['edit'])){echo $joining_date;}else{echo date("Y-m-d");} ?>', <?php echo $tab++; $tab=$tab+3; ?>));
									</script>
									
								</td>
								<td>Date To</td>
								<td><script required type="text/javascript" language="javascript">
									document.writeln(document.writeln(DateInput('date_to', 'admin_employee_from',true, 'YYYY-MM-DD','<?php if(isset($_GET['edit'])){echo $joining_date;}else{echo date("Y-m-d");} ?>', <?php echo $tab++; $tab=$tab+3; ?>));
									</script>
								<?php
									
									if(isset($_POST['submit'])){
									$_SESSION['df']=$_POST['date_from'];
									$_SESSION['dt']=$_POST['date_to'];
									}
								?>
								</td>

							</tr>
				
					</table>
						<input type="submit" name="submit" value="submit" id="submit" class="form-control input-sm">
					<table>
				
							<tr>
								<th>Sno</th>
								<th>Name</th>
								<th>Address</th>
								<th>Mobile</th>
								<th>Balance</th>
							</tr>
				
							<?php
								$sql="SELECT * FROM `customer_payment`";
								$result=execute_query($sql);
								while($row=mysqli_fetch_array($result)){
									echo'<tr><td>'.$sno++.'</td>
									<td><a href="customer_ledger_details.php?id='.$row['customer_id'].'">'.client_name($row['customer_id']).'</a></td>';
									$sql1='SELECT * FROM `client_master` WHERE sno="'.$row['customer_id'].'"';
									$result1=execute_query($sql1);
									$row1=mysqli_fetch_array($result1);
									echo'<td>'.$row1['address'].'</td>
									<td>'.$row1['mobile'].'</td>
									<td>'.$row['amount'].'</td></tr>';
								}
							?>
					
					</table>
		</form>
</div>

<?php
page_footer();	
?>