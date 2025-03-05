<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
$_POST['cust_id']='';
$tab=1;
$sno=1;
$total_rent=0;
if(!isset($_POST['date_from'])){
	$_POST['date_from'] = date("Y-m-01");
	$_POST['date_to'] = date("Y-m-d");
}
if(isset($_GET['id'])){
	$response=2;
}
else{
	$response=1;
}

switch($response){
	case 1:{
?>
<script type="text/javascript">
	$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=ledger_customer",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company']").val(ui.item.label);
			$('#cust_id').val(ui.item.id);
			$('#cust_name1').val(ui.item.label);
			
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name1").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
	
</script>

<div id="container">
    <h2>Ledger</h2>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="ledger.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" style="margin-bottom: -100px;">
		<table>
			<tr>
				<td>Name : </td>
				<td><input type="text" id="cust_name1" name="cust_name">
					<input type="hidden" id="cust_id" name="cust_id">
				</td>
				<td>From : </td>
				<td>
					<script type="text/javascript" language="javascript">
					document.writeln(DateInput('date_from', 'addnewdesignation', true, 'YYYY-MM-DD', '<?php echo $_POST['date_from'];?>', 1));
                    </script>
				</td>
				<td>To : </td>
				<td>
					<script type="text/javascript" language="javascript">
					document.writeln(DateInput('date_to', 'addnewdesignation', true, 'YYYY-MM-DD', '<?php echo $_POST['date_to'];?>', 1));
                    </script>				</td>
				<td><input class="large" type="submit" name="submit" value="Search"></td>
			</tr>
		</table>

	</form>
	<table style="margin-top: 120px;">
		<tr>
			<th>Sno</th>
			<th>Company Name/Customer Name</th>
			<th>Address</th>
			<th>Mobile</th>
			<th>Balance</th>
			<th>View</th>
		</tr>
		<?php           
         $sql = "SELECT * FROM customer_transactions WHERE `cust_id` != '1' GROUP BY cust_id";
         $result = execute_query($sql);
         $sno = 1; 
         while ($row = mysqli_fetch_array($result)) {
             $row['cust_id'] = $row['cust_id'] == '' ? 0 : $row['cust_id'];
             $tot = get_cust_balance($_POST['date_from'], $_POST['date_to'], $row['cust_id']);
             // echo $tot . '<br>';
             if ($tot != 0) {
                 $bg_color = $sno % 2 == 0 ? '#EEE' : '#CCC';       
                 echo '<tr style="background:' . $bg_color . ';">';
                 echo '<td>' . $sno++ . '</td>';
                 
                 if (get_company_name($row['cust_id']) == '') {
                     echo '<td>' . username($row['cust_id']) . '</td>';
                 } else {
                     echo '<td>' . get_company_name($row['cust_id']) . '</td>';
                 } 
                 
                 echo '<td>' . address($row['cust_id']) . '</td>
                       <td>' . mobile($row['cust_id']) . '</td>
                       <td>' . $tot . '</td>
                       <td><a href="ledger.php?id=' . $row['cust_id'] . '&date_from=' . $_POST['date_from'] . '&date_to=' . $_POST['date_to'] . '">View</a></td></tr>';
             }
         }
         ?>

	</table>
</div>

<?php

		break;
	}
	case 2:{
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
				if($cust_details['company_name'] == ''){
					echo 'Customer Name : '.$cust_details['cust_name'].'<br>';
				}
				else{
					echo 'Company Name : '.$cust_details['company_name'].'<br>';
				}
					echo 'Address : '.$cust_details['address'].'<br>';
					echo 'Mobile : '.$cust_details['mobile'];
				?>
			</div>
			<div id="right" style="float:right">
				<br>Leder From :<?php  echo $_POST['date_from']; ?><br>
				Ledger To : <?php echo $_POST['date_to']; ?><br>
			</div>
			<table style="margin-top: 15px;">

				<tr>
					<th>Sno</th>
					<th>Date</th>					
					<th>Mop</th>
					<th>Description</th>
					<th>Debit</th>
					<th>Credit</th>
					<th>Balance</th>
					<th>View</th>
				</tr>
				<?php
			
				$dr=0;
				$cr=0;
				$opening = get_cust_balance("1970-01-01", date("Y-m-d", strtotime($_GET['date_from'])-86400), $_GET['id']);
				echo '<tr>
				<td colspan="6">Opnening Balance :</td>
				<td>'.$opening.'</td></tr>';
				$date_to= date("Y-m-d", strtotime($_GET['date_to'])+86400);
				$sql="select * from customer_transactions where cust_id=".$_GET['id']." and timestamp >= '".$_GET['date_from']."' and timestamp <= '".$date_to."'";
				$result=execute_query($sql);
				$balance=$opening;
				$k = 0;
				$amt = 0;
				while($row=mysqli_fetch_array($result)){
					if($row['type']=='ADVANCE_AMT' AND $row['payment_for']=='room_rent'){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>';
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
						$balance-= $row['amount'];
						echo '<td>Advance Amount(ROOM) </td>
						<td></td>
						<td>'.$row['amount'].'</td>
						<td>'.$balance.'</td><td></td>';
						$cr+=$row['amount'];
					}
					else if($row['type']=='ADVANCE_PAID' AND $row['payment_for']=='room_rent'){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
						$balance+=$row['amount'];
						echo '<td>Advance Amount Paid(ROOM)  </td>
						<td>'.$row['amount'].'</td>
						<td></td>
						<td>'.$balance.'</td><td></td>';
						$dr+=$row['amount'];
						
					}
					else if($row['type']=='ADVANCE_AMT' AND $row['payment_for']=='banquet_rent'){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>';
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
						$balance-= $row['amount'];
						echo '<td>Advance Amount(BANQUET) </td>
						<td></td>
						<td>'.$row['amount'].'</td>
						<td>'.$balance.'</td><td></td>';
						$cr+=$row['amount'];
					}
					else if($row['type']=='ADVANCE_PAID' AND $row['payment_for']=='banquet_rent'){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
						$balance+=$row['amount'];
						echo '<td>Advance Amount Paid(BANQUET)  </td>
						<td>'.$row['amount'].'</td>
						<td></td>
						<td>'.$balance.'</td><td></td>';
						$dr+=$row['amount'];						
					}
					else if($row['type']=='RENT'  && ($row['mop']=='credit' || $row['mop']=='CREDIT') && $row['payment_for'] !='ROOM'){
						$amt = $row['amount']-$row['advance_set_amt'];
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
					 	$balance+=$amt;
						echo '<td>Room invoice : GH/'.$row['financial_year'].'/'.$row['invoice_no'].'</td>';
						echo '<td>'.$row['amount'].'</td>';
						echo '<td>'.$row['advance_set_amt'].'</td>';
						echo '<td>'.$balance.'</td>';
						echo '<td><a href="print.php?id='.$row['allotment_id'].'">view</a><td>';
						$dr+=$row['amount'];
						$cr+=$row['advance_set_amt'];
					}
					elseif($row['type']=='BAN_AMT'  && ($row['mop']=='credit' || $row['mop']=='CREDIT')){
						$amt = $row['amount']-$row['advance_set_amt'];
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
					 	$balance+=$amt;
						echo '<td>BANQUET invoice : BAN/'.$row['financial_year'].'/'.$row['invoice_no'].'</td>
						<td>'.$row['amount'].'</td>
						<td>'.$row['advance_set_amt'].'</td>
						<td>'.$balance.'</td>
						<td><a href="print_ban.php?id='.$row['allotment_id'].'">view</a><td>';
						$dr+=$row['amount'];
						$cr+=$row['advance_set_amt'];
					}
					else if($row['type']=='RENT' && $row['payment_for'] =='ROOM'){
						$pay_for='Rent Receipt';
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td><td>'.strtoupper($row['mop']).'</td>';
						$balance-= $row['amount'];
						echo '<td>Receipt :'.$pay_for.'</td>
						<td></td>
						<td>'.$row['amount'].'</td>
						<td>'.$balance.'</td><td></td>';
						$cr+=$row['amount'];

					}
					else if($row['type']=='sale_restaurant' && $row['payment_for'] =='res' && ($row['mop']=='credit' || $row['mop']=='CREDIT')){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
					 	$balance+=$row['amount'];
						echo '<td>Restaurant invoice : GH/'.$row['financial_year'].'/'.$row['invoice_no'].'</td>
						<td>'.$row['amount'].'</td>
						<td></td>
						<td>'.$balance.'</td>
						<td><a href="scripts/printing_sale_restaurant.php?inv='.$row['number'].'">view</a><td>';
						$dr+=$row['amount'];

					}
					
					else if($row['type']=='receipt'){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>';
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
						$balance-= $row['amount'];
						echo '<td>Receipt </td>
						<td></td>
						<td>'.$row['amount'].'</td>
						<td>'.$balance.'</td><td></td>';
						$cr+=$row['amount'];
					}
					else if($row['type']=='payment'){
						echo'<tr><td>'.$sno++.'</td>
						<td>'.$row['timestamp'].'</td>
						<td>'.strtoupper($row['mop']).'</td>';
						$balance+=$row['amount'];
						echo '<td>Payment  </td>
						<td>'.$row['amount'].'</td>
						<td></td>
						<td>'.$balance.'</td><td></td>';
						$dr+=$row['amount'];
						
					}

					echo '</tr>';
				}
				?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><b><?php echo $dr; ?></b></td>
					<td><b><?php echo $cr; ?></b></td>
					<td><b><?php echo $dr-$cr+$opening; ?></b></td>
				</tr>
			</table>
		</form>
</div>
<?php
        break;
    	}
    }
navigation('');
page_footer();	
?>