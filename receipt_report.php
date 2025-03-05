
<?php
session_cache_limiter('nocache');
session_start();
date_default_timezone_set('Asia/Calcutta');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
$sno1=1;
$tab=1;
$date_from='';
$date_to='';
$mop='';
$amount='';
$cust_id='';
$account_name='';
page_header();
?>
<div id="container">
	<?php echo $msg; ?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="admin_employee_from" enctype="multipart/form-data">
			<h2>Receipt Report</h2>
			<a href="payment.php" style="margin-left:90%">Receipt Entry</a>
		<table>
			<tr>
				<td>Date From</td>
				<td>
				<script required type="text/javascript" language="javascript">
					document.writeln(document.writeln(DateInput('date_from', 'admin_employee_from',true, 'YYYY-MM-DD','<?php if(isset($_POST['date_from'])){echo $_POST['date_from'];}else{echo date("Y-m-d");} ?>', <?php echo $tab++; $tab=$tab+3; ?>));
				</script>
				</td>
				<td>Date To</td>
				<td>
					<script required type="text/javascript" language="javascript">
						document.writeln(document.writeln(DateInput('date_to', 'admin_employee_from',true, 'YYYY-MM-DD','<?php if(isset($_POST['date_to'])){echo $_POST['date_to'];}else{echo date("Y-m-d");} ?>', <?php echo $tab++; $tab=$tab+3; ?>));
					</script>
				</td>
				<td>Mode Of Payment</td>
				<td>
					<select name="mop" class="form-control input-sm">
						<option value="">ALL</option>
						<option value="cash">CASH</option>
						<option value="rtgs">RTGS/NEFT</option>
						<option value="cheque">CHEQUE</option>
					</select>
				</td>
				</tr>
				<tr>
					<td>Customer Name</td>
					<td><input type="text" name="customer_name" id="cust_name" class="form-control input-sm"></td>
					<input type="hidden" id="cust_id" name="cust_id">
					<td>Amount</td>
					<td><input type="text" name="amount" class="form-control input-sm"></td>
					<td>Account Name</td>
					<td><input type="text" name="account_name" id="account_name" class="form-control input-sm"></td>
				</tr>
			</table>
					<input type="submit" name="submit" Value="Search" id="submit" class="form-control">
				
			<table>
				<tr>
					<th>Sno</th>
					<th>Customer Name</th>
					<th>Amount</th>
					<th>Invoice No</th>
					<th>Date</th>
					<th>Mode of payments</th>
					<th>Remarks</th>
					<th>Account</th>
					<th>View</th>
					<th>Edit</th>
					<th>Delete</th>
					</tr>
			</thead>
			<?php
			if(isset($_POST['submit'])){
				$date_from=$_POST['date_from'];
				$date_to=$_POST['date_to'];
				$mop=$_POST['mop'];
				echo $mop;
								$account_name=$_POST['account_name'];
								$amount=$_POST['amount'];
								$customer_name=$_POST['customer_name'];
								$cust_id=$_POST['cust_id'];
			                    $sql = "SELECT * FROM `customer_payment` WHERE type='receipt'"; 
			                    $condition = array();
			                    if($date_from !="") {
			                        $condition[] .= "date >='$date_from'";
			                      }
			                    if($date_to !="") {
			                        $condition[] .= "date <='$date_to'";
			                    }
			                    if($mop !="") {
			                        $condition[] .= "mop='$mop'";
			                      
			                    }
			                    if($account_name !="") {
			                        $condition[] .= "account='$account_name'";
			                    }
			                     if($amount!="") {
			                       	
			                        $condition[] .= "amount='$amount'";
			                     }
			                     if($cust_id !="") {
			                      
			                        $condition[] .= "customer_id='$cust_id'";
			                      	
			                     }
			                     
			                     if (count($condition) > 0 ){
			                         $sql.=" AND ".implode(' AND ', $condition);
			                      }
			                     	//echo $sql;
									$result = execute_query($sql);
									if($result){
										while($row = mysqli_fetch_array($result)){
			                           	 echo'<tr><td>'.$sno.'</td>';
			                           	 echo'<td>'.client_name($row['customer_id']).'</td>
			                           	 <td>'.$row['amount'].'</td>
			                           	 <td>'.$row['invoice_number'].'</td>
			                           	 <td>'.$row['date'].'</td>
			                           	 <td>'.$row['mop'].'</td>
			                           	 <td>'.$row['remark'].'</td>
			                           	 <td>'.$row['account'].'</td>';
			                           	 echo'<td><a href="">View</a></td>
			                           	 <td><a href="receipt.php?edit1='.$row["sno"].'">Edit</a></td>
			                           	 <td><a href="receipt.php?delete1='.$row["sno"].'">Delete</a></td></tr>';
			                           	 $sno++;
			                           	

			                        	}
			                        }
			                 }
			                 ?>
						
					</table>
		</form>
</div>
<?php
page_footer();	
?>

<script>
	$( function() {
		//alert('aac');
	 $( "#cust_name" ).autocomplete({
	  source: function( request, response ) {
	   // Fetch data
	   $.ajax({
	    url: "ajax.php",
	    type: 'post',
	    dataType: "json",
	    data: {
	     search:request.term
	    },
	    success: function( data ) {
	     response( data );
	    }
	   });
	  },
	  select: function (event, ui) {
	   // Set selection
	   $('#cust_name').val(ui.item.cust_name); 
	   $('#cust_id').val(ui.item.id);// display the selected text
	   // save selected id to input
	   return false;
	  }
 });
});
 </script>
 <script>
	$( function() {
		//alert('aac');
	 $( "#account_name" ).autocomplete({
	  source: function( request, response ) {
	   // Fetch data
	   $.ajax({
	    url: "ajax.php",
	    type: 'post',
	    dataType: "json",
	    data: {
	     search1:request.term
	    },
	    success: function( data ) {
	     response( data );
	    }
	   });
	  },
	  select: function (event, ui) {
	   // Set selection
	   $('#account_name').val(ui.item.cust_name);
	   return false;
	  }
 });
});

 </script>
