<?php 
date_default_timezone_set('Asia/Calcutta');
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
include("get_computer.php");
logvalidate('','');
$sql = "select * from register_users where user_name='".$_SESSION['username']."'";
$student=mysqli_fetch_array(execute_query($sql));
if(isset($_GET['id'])){
	$sql= 'select * from booking_details where sno="'.$_GET['id'].'"';
	$details=mysqli_fetch_array(execute_query($sql));
	
	$sql = "select * from customer where sno=".$details['cust_id'];
	$customer=mysqli_fetch_array(execute_query($sql));
	
	$sql = 'select * from category where sno="'.$details['category'].'"';
	$category=mysqli_fetch_array(execute_query($sql));
	
	$sql='select sum(amount)as amount from customer_transactions where cust_id='.$details['cust_id'];
	$advance=mysqli_fetch_array(execute_query($sql));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="pop.css" TYPE="text/css" REL="stylesheet" media="all">
<style type="text/css">
@media print {
input#btnPrint {
display: none;
	}	
}
table, tr, td {font-size:14px; border:none; font-weight:bold;}
h3{ font-size:16px;}
</style>
<script language="javascript" type="text/javascript">
//window.print();
</script>
</head>
<body>
<div id="newdiv" style="page-break-after:avoid; margin-bottom:50px;">
     <h5 style="text-align:center; text-decoration:underline;">Shri Ram Hotel<br></h5>
        <h3 style="text-align:center"><br>Shringar Haat, Ayodhya, 9415112573</h3>
			<table width="100%">
            <tr><td>Invoice No-<b><?php echo $details['sno']; ?></b></td>
                <td>Receipt generated on-<b><?php echo date("d-m-Y",strtotime($details['allotment_date'])); ?></b></td>
            </tr>
    		<tr>
              <tr><td><b>Name</b></td><td><?php echo $customer['cust_name'];?></td></tr>
              <tr><td><b>No. of Rooms</b></td><td><?php echo $details['no_of_rooms'];?></td></tr>
			<tr><td><b>Category</td><td><?php echo $category['room_type']; ?></td></tr>
			<tr><td><b>Booking Date</td><td><?php echo $details['booking_date']; ?></td></tr>
              <tr><td><b>Total Package</b></td><td><?php echo $details['total_package'];?></td></tr>
              <tr><td><b>Booking Date From </b></td><td><?php echo $details['booking_from'];?></td></tr>
              <tr><td><b>Booking Date To </b></td><td><?php echo $details['booking_to'];?></td></tr>
              <tr><td><b>Amount paid</b></td>
              <td style="text-align:left; white-space: wrap;"><?php echo $advance['amount'];?></td></tr>
             <tr><td>&nbsp;</td></tr>
			</table>
		<div style="float:right; padding-right:20px;margin-top:-15px;"><b>(Cashier's Signature)</b></div>
	</div>


    <div><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>
</body>
</html>