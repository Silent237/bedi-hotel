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
if(isset($_POST['submit_form'])){
	foreach($_POST as $k=>$v){
		$_SESSION['purchase_'.$k] = $v;
	}
}
if(isset($_POST['reset_form'])){
	foreach($_POST as $k=>$v){
		unset($_SESSION['purchase_'.$k]);
	}
}
if(isset($_SESSION['purchase_allot_from'])){
	$sql='select *, customer.sno as sno, allotment.sno as allot_id from customer join allotment where allotment.cust_id=customer.sno';
	if($_SESSION['purchase_allot_from']!=date("Y-m-d")){
		$sql .= ' and allotment_date >="'.$_SESSION['purchase_allot_from'].'"';
	}
	if($_SESSION['purchase_allot_to']!=date("Y-m-d")){
		$sql .= ' and allotment_date<="'.$_SESSION['purchase_allot_to'].'"';
	}
	$sql .= ' group by cust_id';
	$result=mysqli_fetch_assoc(execute_query($sql));
	
}
else{
	$sql = 'select *, customer.sno as sno , allotment.sno as allot_id from customer left join allotment where allotment.cust_id=customer.sno group by cust_id';
	$result=mysqli_fetch_assoc(execute_query($sql));
}

if(isset($_GET['id'])){
	$response=2;
}

switch($response){
	case 1 :{
?>

<div>
<h2>Customer Ledger</h2>
<h3 style="text-align:right;"><a href="cust_list.php" style="text-decoration:none; font-size:18px;" >Customer List whom room have not been alloted</a></h3>
<form action="cust_ledger.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table width="100%">
            	<tr style="background:#CCC;">
                <td>Allotment Date</td>
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['purchase_allot_from'])){echo $_SESSION['purchase_allot_from'];}else{echo date("Y-m-d");}?>', 1))
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['purchase_allot_to'])){echo $_SESSION['purchase_allot_to'];}else{echo date("Y-m-d");}?>', 4))
                    </script>
                    </span>
                    </td>
                </tr>
            	<tr>
                	<th colspan="3">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="3">
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                </tr>
            </table>
            <br>
		</form>	
	<table width="100%">
		<tr style="background:#000; color:#FFF;">
			<th>S.No.</th>
			<th>Customer Name</th>
			<th>Total Rent</th>
			<th>Other Charges</th>
			<th>Amount Paid</th>
			<th>Balance Till Date</th>
		</tr>
    <?php
			$i=1;
			foreach($result as $row){
				if($i%2==0){
					$col = '#CCC';
				}
				else{
					$col = '#EEE';
				}
				$balance = get_balance($row['sno']);
				echo '<tr style="background:'.$col.'; text-align:center;">
				<td>'.$i++.'</td>
				<td><a href="cust_ledger.php?id='.$row['sno'].'">'.$row['cust_name'].'</a></td>
				<td>'.$balance[0].'</td>
				<td>'.$balance[1].'</td>
				<td>'.$balance[2].'</td>
				<td>'.$balance[3].'</td></tr>';
			}
?>
</table>
</div>
<?php
		break;
	}
	case 2:{
	$sql = 'select * from customer where sno='.$_GET['id'];
	$cust=mysqli_fetch_assoc(execute_query($sql));
	$cust=$cust[0];
?>
<div>
	<h2>Customer Detailed Ledger</h2>
	<table width="100%">
		<tr style="background:#CCC;">
			<td>Customer Name</td>
			<td><?php echo $cust['cust_name']; ?></td>
		</tr>		
	</table>
	<table width="100%">
		<tr style="background:#000; color:#FFF;">
			<th>S.No.</th>
			<th>Date</th>
			<th>Description</th>
			<th>Debit</th>
			<th>Credit</th>
			<th>Balance</th>
		</tr>
    <?php
		$sql = '(SELECT room_name, room_rent, allotment_date, IF(exit_date is null, CURRENT_TIMESTAMP, exit_date) as exit_date, DATEDIFF(IF(exit_date is null, CURRENT_TIMESTAMP, exit_date), allotment_date) as date_diff, (room_rent*DATEDIFF(IF(exit_date is null, CURRENT_TIMESTAMP, exit_date), allotment_date)) as total_rent, "ROOM" as type FROM `allotment` join room_master on room_master.sno = allotment.room_id where cust_id='.$cust['sno'].') union all (SELECT "" as room_name, "" as room_rent, `timestamp` as allotment_date, "" as exit_date, "" as date_diff, amount as total_rent, "PAYMENT" as type FROM `customer_transactions` where cust_id='.$cust['sno'].') order by allotment_date';
		$result=mysqli_fetch_assoc(execute_query($sql));
		$i=1;
		$dr=0;
		$cr=0;
		$balance=0;
		foreach($result as $row){
			if($i%2==0){
				$col = '#CCC';
			}
			else{
				$col = '#EEE';
			}
			echo '<tr style="background:'.$col.'; text-align:center;">
			<td>'.$i++.'</td>
			<td>'.$row['allotment_date'].'</td>';
			if($row['type']=='ROOM'){
				$balance += $row['total_rent'];
				echo '<td>Room Rent for '.$row['room_name'].' @ Rs.'.$row['room_rent'].' per day for '.$row['date_diff'].' days.</td>
				<td>&nbsp;</td><td>'.$row['total_rent'].'</td><td>'.$balance.'</td></tr>';
			}
			elseif($row['type']=='PAYMENT'){
				$balance -= $row['total_rent'];
				echo '<td>Payment Received</td>
				<td>'.$row['total_rent'].'</td><td>&nbsp;</td><td>'.$balance.'</td></tr>';
			}

		}
?>
</table>
</div>	
<?php
		break;
	}
}
page_footer();
?>