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

$sql = "SELECT number_of_room FROM advance_booking WHERE DATE(check_in) = '" . date('Y-m-d') . "' and status=0";
	$result = execute_query( $sql);
	
	$advCheckin = 0;
	
	while ($row = mysqli_fetch_assoc($result)) {
		$numbers = explode(',', $row['number_of_room']); // Split CSV values
		$advCheckin += array_sum(array_map('intval', $numbers)); // Convert to int and sum
	}

$sql = "SELECT COUNT(*) as total_rows FROM allotment WHERE DATE(allotment_date) = '" . date('Y-m-d') . "'";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalCheckin = $row['total_rows'];

$sql = "SELECT COUNT(*) as total_rows1 FROM allotment WHERE DATE(exit_date) = '" . date('Y-m-d') . "'";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$totalCheckout = $row['total_rows1'];

$sql = 'SELECT COUNT(*) as room_rows FROM room_master where room_status=1';
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
	<div class="box bg-warning" data-type="total_rooms">
		<div class="num">
			<span id="count"><?php echo $totalRoom; ?></span><img src="images/bed.png" alt="hotel Image">
		</div>	
		<p>Total Rooms</p>
	</div>

	<div class="box" style="background-color:#e67d21;" data-type="available_rooms">
		<div class="num">
			<span id="count"><?php echo $avroom; ?></span><img src="images/signal.png" alt="hotel Image">
		</div>	
		<p>Available Rooms</p>
	</div>

	<div class="box bg-success" data-type="total_checkin">
		<div class="num">
			<span id="count"><?php echo $totalCheckin; ?></span>
			<i style="color:white; margin:10px 10px 10px 0px;font-size:40px;" class="fa fa-2x fa-userfa-solid fa-person-walking-dashed-line-arrow-right"></i>
		</div>
		<p>Total Check In</p>
	</div>

	<div class="box bg-danger" data-type="total_checkout">
		<div class="num">
			<span id="count"><?php echo $totalCheckout; ?></span>
			<i style="color:white; margin:10px 10px 10px 0px;font-size:40px;" class="fa-solid fa-2x fa-person-walking-luggage"></i>
		</div>
		<p>Total Check Out</p>
	</div>

	<div class="box" id="box3" data-type="advance_checkin">
		<div class="num">
			<span id="count"><?php echo $advCheckin; ?></span><img src="images/reception.png" alt="hotel Image" >
		</div>	
		<p>Today Advance Check In</p>
	</div>

	<div class="box" data-type="running_services">
		<div class="num">
			<span id="count"><?php echo $roomservice; ?></span><img src="images/hotel-service.png" alt="hotel Image">
		</div>	
		<p>Running Rooms Services</p>
	</div>

	<div class="box" id="box1" data-type="total_tables">
		<div class="num">
			<span id="count"><?php echo $totaltable; ?></span><img src="images/restaurant.png" alt="hotel Image" >
		</div>
		<p>Total Tables</p>
	</div>

	<div class="box" style="background-color:#2196f3;" data-type="running_tables">
		<div class="num">
			<span id="count"><?php echo $availtable; ?></span><img src="images/restaurant.png" alt="hotel Image" >
		</div>
		<p>Running Tables</p>
	</div>	
</div>


<!-- Modal HTML -->
<div id="reportModal" class="modal" style="display:none;">
	<div class="modal-content" style="padding:20px; max-width: 90%; margin: auto; background:#f7f3f3; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.2);">
		<span class="close" style="float:right; font-size: 24px; cursor: pointer;">&times;</span>
		<div id="modalBody">Loading...</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
	$('.box').on('click', function () {
		let type = $(this).data('type');

		// Show modal and loading state
		$('#modalBody').html('Loading...');
		$('#reportModal').fadeIn();

		// AJAX request
		$.ajax({
			url: 'fetch_report.php', // 🔁 Your backend file
			type: 'POST',
			data: { report_type: type },
			success: function (data) {
				$('#modalBody').html(data); // Inject response
			},
			error: function () {
				$('#modalBody').html('<p style="color:red;">Failed to load report.</p>');
			}
		});
	});

	// Close modal
	$('.close').on('click', function () {
		$('#reportModal').fadeOut();
	});

	// Close when clicking outside modal content
	$(window).on('click', function (e) {
		if ($(e.target).is('#reportModal')) {
			$('#reportModal').fadeOut();
		}
	});
});
</script>




<?php

$sql = "SELECT 
    MONTH(allotment_date) AS month,
    SUM(total_amount) AS total_amount
FROM 
    advance_booking
WHERE 
    YEAR(allotment_date) = YEAR(CURDATE()) 
GROUP BY 
    MONTH(allotment_date)
ORDER BY 
    MONTH(allotment_date)";

$res = execute_query($sql);

// Initialize an array with all months set to 0
$monthNames = array(
    1 => "January", 2 => "February", 3 => "March", 4 => "April",
    5 => "May", 6 => "June", 7 => "July", 8 => "August",
    9 => "September", 10 => "October", 11 => "November", 12 => "December"
);

$monthlyTotals = array_fill(1, 12, 0);

// Fill in totals from the query
while ($row = mysqli_fetch_assoc($res)) {
    $month = (int)$row['month'];
    $monthlyTotals[$month] = (float)$row['total_amount'];
}

// Format for chart
$dataPoints = array();
foreach ($monthNames as $num => $name) {
    $dataPoints[] = array("y" => $monthlyTotals[$num], "label" => $name);
}

// Optional: print for testing
// print_r($dataPoints);


 
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
	axisX: {
		interval: 1,
		labelAutoFit: false,
		labelFontSize: 12
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
<div id="chartContainer" style="height: 250px; width: 72%;margin:120px 0px 0px 290px;"></div>

</body>
</html>    

        <?php navigation(''); 
        page_footer();
        ?>

<?php
}

?>
