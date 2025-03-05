<?php
	if(isset($_GET['cust_id'])){
		$cust_id=$_POST['cust_id'];
		$sql='select * from allotment where cust_id='.$cust_id.' and (exit_date is null or exit_date="")';
		$result=mysqli_query($conn,$sql);
		$date = $_POST['exit_date'];
		$time = strtotime($date);
		$month = date("m",$time);
		$year = date("Y",$time);
		if($month>=1 && $month<=3){
			$year = $year-1;
		}
	
	while($row = mysqli_fetch_array($result)) {
		if(isset($_POST['check_'.$row['sno']])){
			$details=$row;
			$balance = check_pendency($row['room_rent'], $row['allotment_date'], $row['cust_id'], $_POST['exit_date'], $row['sno']);
			
			$msg .= '<li class="error">Receipt Added</li>';	
			$sql = 'select * from allotment where financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			$invoice_result = execute_query($sql);
			if(mysqli_num_rows($invoice_result)!=0){
				$invoice_no = mysqli_fetch_array($invoice_result);
				$_POST['invoice_no'] = $invoice_no['invoice_no']+1;

			}
			else{
				$_POST['invoice_no'] = 1;
			}
			$sql = 'select * from allotment where financial_year="'.$year.'" order by abs(invoice_no) desc limit 1';
			$invoice_result = execute_query($sql);
			if(mysqli_num_rows($invoice_result)!=0){
				$invoice_no = mysqli_fetch_array($invoice_result);
				$_POST['invoice_no'] = $invoice_no['invoice_no']+1;

			}
			else{
				$_POST['invoice_no'] = 1;
			}

			$sql='update allotment set exit_date="'.$_POST['exit_date'].'", invoice_no="'.$_POST['invoice_no'].'" where sno='.$row['sno'];
			$result = execute_query($sql);
			$sql='update room_master set status=0 where sno='.$row['room_id'];
			$result = execute_query($sql);
			$sql='update customer set destination="'.$_POST['destination'].'" , check_out_time="'.$_POST['exit_date'].'" where sno='.$_POST['cust_id'];
			$result = execute_query($sql);

	}
	}
}
?>