<?php
include("scripts/settings.php");
$msg='';
if(isset($_POST['submit'])) {
	 
	 if($_POST['username']!='' && $_POST['userpwd']!='') {
		
		$sql = 'select * from users where userid="'.$_POST['username'].'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)!=0) {			
			
			$row = mysqli_fetch_array(execute_query($sql));
			if($_POST['userpwd']==$row['pwd']) {
				$sql='select * from user_access_detail where user_id = "'.$row['sno'].'"';
				$row1 = mysqli_fetch_array(execute_query($sql));
				$_SESSION['usersno'] = $row['sno'];
				$_SESSION['username'] = $row['userid'];
				$_SESSION['userpwd'] = $row['pwd'];
				$_SESSION['usertype'] = $row['type'];
				$_SESSION['session_id'] = randomstring();
				$_SESSION['startdate'] = date('y-m-d');
				$_SESSION['accessid'] = $row1['auth_id'];
				$_SESSION['authcode']='';
				$time = localtime();
		        $time = $time[2].':'.$time[1].':'.$time[0];
				//echo $time;
		        $_SESSION['starttime']=$time;
				
				
				$sql = "insert into session (user,s_id,s_start_date,s_start_time) values ('".$_SESSION['username']."','".$_SESSION['session_id']."','".$_SESSION['startdate']."','".$_SESSION['starttime']."')";
		        execute_query($sql);
				
		       $sql = "update auth set session_user='".$_SESSION['username']."', status=1 where timestamp='".$_SESSION['starttime']."' and s_id='".$_SESSION['authcode']."'";
		       //execute_query($sql);		
		       $msg='<h1>Welcome '.$_SESSION['username'].'</h1>';
				
				
				$response=2;
			}
			else {
				echo '<script>alert("Please Enter Valid User Password")</script>';
				$response=1;
			}
		}
		else {
			 echo '<script>alert("Please Enter Valid User Password")</script>';
				$response=1;
		}		 
	 }
	 else {
		 echo '<script>alert("Please Enter User Detail")</script>';
		 $response=1;
	 }
 }

?>
<?php
if(!isset($_SESSION['session_id'])) {
	page_struct();
?>	
<div class="full_container">
  <div class="form_title">
    <div class="card-header">
      <?php echo $msg; ?>
    </div>
    <h2>Hotel Login &nbsp;<i class="fa fa-user"></i></h2>
  </div>
  <div class="login">
    <form style="margin:80px 20px 0px 20px;" method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>">
      <!-- <h4>Login via User Details</h4><br> -->


	  
      <div class="mb-4">
        <label for="exampleInputEmail1" class="form-label">USERNAME</label>
        <input type="text" class="form-control" name="username" id="exampleInputEmail1" aria-describedby="emailHelp"
          required>
      </div>
      <div class="mb-4">
        <label for="exampleInputPassword1" class="form-label">PASSWORD</label>
        <input type="password" class="form-control" name="userpwd" id="exampleInputPassword1" required>
      </div>
	  <div class="text-center pt-3">
	  <button style="background-color:#3a3f51;text-align:center;" name="submit" type="submit" class="btn btn-primary mx-auto">Login</button>
      </div>
    </form>
  </div>
</div>
<?php 
}
else {
$sql = "SELECT COUNT(*) as total_rows FROM advance_booking WHERE DATE(check_in) = '" . date('Y-m-d') . "'";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalCheckin = $row['total_rows'];

$sql = "SELECT COUNT(*) as total_rows1 FROM advance_booking WHERE DATE(check_out) = '" . date('Y-m-d') . "'";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalCheckout = $row['total_rows1'];

$sql = 'SELECT COUNT(*) as room_rows FROM room_master';
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalRoom = $row['room_rows'];



$sql = 'SELECT COUNT(*) as item_rows FROM stock_available';
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalitem = $row['item_rows'];


$sql = 'SELECT COUNT(*) as table_rows FROM res_table';
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totaltable = $row['table_rows'];

$sql = 'SELECT COUNT(*) as row_count 
FROM room_master 
WHERE status = 0 OR status IS NULL;
';
$result = $db->query($sql);
$row = $result->fetch_assoc();
$avroom = $row['row_count'];


$sql = 'SELECT COUNT(*) as waiter_rows FROM admin_waiter';
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalwaiter = $row['waiter_rows'];

$sql="SELECT COUNT(*) as avail_table FROM `res_table` WHERE booked_status=1;";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$availtable = $row['avail_table'];

$sql="SELECT COUNT(*) as room_service FROM `room_master` where booked_status=1  order by abs(room_name);";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$roomservice = $row['room_service'];

page_header();
//title_bar();
?>
<div class="dashboard">
<div class="box bg-warning">
	<div class="num">
	<span id="count"><?php echo $totalRoom; ?></span><img src="images/bed.png" alt="hotel Image">
	</div>	
	<p>Total Rooms</p>
	</div>
	<div class="box bg-success">
		<div class="num">
	 <span id="count"><?php echo $totalCheckin; ?></span> <i style="color:white; margin:10px 10px 10px 0px;font-size:40px;" class="fa fa-2x fa-userfa-solid fa-person-walking-dashed-line-arrow-right"></i> <!--<img src="images/check-in.png" alt="hotel Image" > -->
	</div>
		<p>Total Check In</p>
	</div>
	<div class="box bg-danger">
		<div class="num">
	<span id="count"><?php echo $totalCheckout; ?></span><i style="color:white; margin:10px 10px 10px 0px;font-size:40px;"  class="fa-solid fa-2x fa-person-walking-luggage"></i> <!--<img src="images/checkout.png" alt="hotel Image" > -->
	</div>
		<p>Total Check Out</p>
	</div>
	<div class="box" style="background-color:#e67d21;">
	<div class="num">
	<span id="count"><?php echo $avroom; ?></span><img src="images/signal.png" alt="hotel Image">
	</div>	
	<p>Available Rooms</p>
	</div>
	<!-- <div class="box" id="box3">
	<div class="num">
	<span id="count"><?php echo $totalitem; ?></span><img src="images/reception.png" alt="hotel Image" >
	</div>	
	<p>Total Food-Item</p>
	</div>

	<div class="box">
	<div class="num">
	<span id="count"><?php echo $roomservice; ?></span><img src="images/hotel-service.png" alt="hotel Image">
	</div>	
	<p>Total Rooms Services</p>
	</div>
	
	<div class="box" id="box1">
		<div class="num">
	<span id="count"><?php echo $totaltable; ?></span><img src="images/restaurant.png" alt="hotel Image" >
	</div>
		<p>Total Tables</p>
	</div>
	<div class="box" style="background-color:#2196f3;">
		<div class="num">
	<span id="count"><?php echo $totaltable-$availtable; ?></span><img src="images/restaurant.png" alt="hotel Image" >
	</div>
		<p>Available Tables</p>
	</div> -->
	
	<!-- <div class="box" id="box3">
	<div class="num">
	<span id="count"><?php echo $totalwaiter; ?></span><img src="images/waiter.png" alt="hotel Image" >
	</div>	
	<p>Waiters</p>
	</div> -->
	
</div>


<?php
//  $dataPoints = array();
//  while($row = $result->fetch_assoc()) {
// 	$dataPoints[] = array("y" => $row['y'], "label" => $row['label']);
// }
$dataPoints = array( 
	array("y" => 3373.64, "label" => "January" ),
	array("y" => 2435.94, "label" => "February" ),
	array("y" => 1842.55, "label" => "March" ),
	array("y" => 1828.55, "label" => "April" ),
	array("y" => 1039.99, "label" => "May" ),
	array("y" => 765.215, "label" => "June" ),
	array("y" => 612.453, "label" => "July" )
);
 
?>
<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function() {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Hotel Revenue"
	},
	axisY: {
		title: "Hotel Revenue (in thousands)"
	},
	data: [{
		type: "column",
		yValueFormatString: "#,##0.## thousand",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 350px; width: 72%;margin:20px 0px 0px 290px;"></div>

</body>
</html>    

        <?php navigation(''); 
        page_footer();
        ?>

<?php
}

?>
