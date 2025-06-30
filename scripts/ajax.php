<?php
session_cache_limiter('nocache');
session_start();
include("settings.php");
$final="";
extract($_POST);
if(isset($_POST['check'])){
	$disval= $_POST['base']* 10 /100;
	$basebal=$_POST['base']-$disval;
	$sql="UPDATE `allotment` SET `other_discount`='".$disval."',`room_rent`='".$basebal."' WHERE sno='".$_POST['check']."'";
	
	$run=execute_query($sql);
	$sql="UPDATE `allotment_2` SET `other_discount`='".$disval."',`room_rent`='".$basebal."' WHERE allotment_id='".$_POST['check']."'";
	
	$run=execute_query($sql);
}

if(isset($_POST['deletecheck'])){
	$sql="UPDATE  `allotment` SET `other_discount`='',`room_rent`='".$_POST['deletebase']."' WHERE sno='".$_POST['deletecheck']."'";
	
	$run=execute_query($sql);
	$sql="UPDATE  `allotment_2` SET `other_discount`='',`room_rent`='".$_POST['deletebase']."' WHERE allotment_id='".$_POST['deletecheck']."'";
	
	$run=execute_query($sql);
}

if(!isset($_GET['term'])){
	return;
}
else{
	$q = $_GET['term'];
}

if($_GET['id']=='category'){
	
	$sql = 'select * from category where sno like "%'.$q.'%"';
	$result= execute_query($sql);
	while($row = mysqli_fetch_array($result)){
		echo $row['rent'];	
	}
}
if ($_GET['id'] == 'cust_name') {
    if (!isset($_GET['exit_date'])) {
        $_GET['exit_date'] = date("Y-m-d H:i:s");
    }

    $q = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
    $final = [];
    $rooms = [];

    $sql = "SELECT customer.sno AS sno, allotment.sno AS allot_id, advance_booking_id , allotment.hold_date as hold_date , cust_name , company_name , mobile, room_id, room_rent , original_room_rent , taxable_amount , other_charges , allotment_date, cust_id, guest_name , count(*) as count 
FROM allotment 
LEFT JOIN customer ON customer.sno = allotment.cust_id 
WHERE (exit_date IS NULL OR exit_date = '') 
AND (cust_name LIKE '%$q%' OR company_name LIKE '%$q%') 
GROUP BY cust_id";
    $result = execute_query($sql);

    while ($row = mysqli_fetch_array($result)) {
        $res_allot = [];

        if ($row['count'] > 1) {
            $sql_multi = "SELECT * FROM allotment WHERE cust_id = {$row['sno']} AND (exit_date IS NULL OR exit_date = '')";
            $result_multi = execute_query($sql_multi);

            while ($row_allot = mysqli_fetch_array($result_multi)) {
                $balance = check_pendency($row_allot['room_rent'], $row_allot['allotment_date'], $row_allot['cust_id'], $_GET['exit_date'], $row_allot['sno']);
                $res_allot[] = $row_allot['sno'];

                $day1 = get_days($row_allot['allotment_date'], $_GET['exit_date']);
                $extra = $row_allot['other_charges'] ?? 0;

                $sql1 = 'SELECT * FROM room_master WHERE sno = ' . intval($row_allot['room_id']);
                $result1 = execute_query($sql1);
                $room = mysqli_fetch_assoc($result1);

                $hold = ($row_allot['hold_date'] == '') ? '' : '<span style="color:red;font-size:18px;"><b>Hold</b></span>';

                array_push($rooms, [
                    "hold" => $hold,
                    "customer_id" => $row_allot['cust_id'],
                    "room_id" => $room['sno'],
                    "label" => $room['room_name'],
                    "allotment_id" => $row_allot['sno'],
                    "balance" => $balance,
                    "extra_bed" => $extra,
                    "tax_rent" => floatval($row_allot['taxable_amount']) * $day1,
                    "org_rent" => floatval($row_allot['original_room_rent']) * $day1,
                    "org" => floatval($row_allot['original_room_rent']) + floatval($extra),
                    "guest_name" => $row_allot['guest_name']
                ]);
            }
        }
		else{
			if($row['other_charges'] == NULL)
			{
				$extra=0;
			}
			else{
				$extra=$row['other_charges'];
			}
			//echo $row['allotment_date'];
			//echo $_GET['exit_date'];
			$balance = check_pendency($row['room_rent'], $row['allotment_date'], $row['cust_id'], $_GET['exit_date'], $row['allot_id']);
			//echo $balance;
			$day1=get_days($row['allotment_date'], $_GET['exit_date']);
			//echo $day1.'<br>';
			$res_allot[] = $row['allot_id'];
			$sql1='select * from room_master where sno='.$row['room_id'];
			$result1 = execute_query($sql1);
			$room = mysqli_fetch_assoc($result1);
			$hold = '';
			if($row['hold_date'] == ''){
				$hold = '';
			}
			else{
				$hold = '<span style="color:red;font-size:18px;"><b>Hold</b></span>';
			}
			array_push($rooms, array("hold"=>$hold , "customer_id"=>$row['sno'], "room_id"=>$room['sno'], "label"=>$room['room_name'], "allotment_id"=>$row['allot_id'], "balance"=>$balance,"extra_bed"=>$extra,"tax_rent"=>floatval($row['taxable_amount']) * $day1,"org_rent"=>floatval($row['original_room_rent']) * $day1,"org"=>floatval($row['original_room_rent'])+$extra , "guest_name"=>$row['guest_name']));
		}
		$allot=$row['allotment_date'];
		$allot=date('Y-m-d',strtotime($allot));
		$exitdate=$_GET['exit_date'];
		$exitdate=date('Y-m-d',strtotime($exitdate));
		$custid=$row['sno'];
		$allotid=$row['allot_id'];
		$sql11="SELECT sum(amount) as amount FROM `customer_transactions` WHERE cust_id='".$custid."' and allotment_id in (".implode(",", $res_allot).") and type='sale_restaurant' and remarks='credit' and mop ='credit'";
		$result11 = execute_query($sql11);
		$resbal = mysqli_fetch_assoc($result11);
		$room_res_bill = $resbal['amount'];
		$res_amt='';
		$sql_last="SELECT * FROM `allotment` WHERE `cust_id`='".$custid."' and `invoice_no` !='0' order by sno desc limit 1";
		$run_last=execute_query($sql_last);
		$row_last=mysqli_fetch_array($run_last);
        $sql_cust="select * from allotment where cust_id='".$custid."' and invoice_no='0'";
        $run_cust=execute_query($sql_cust);
        $cust_row=mysqli_fetch_array($run_cust);
        $sql_res="select sum(amount) as total from `customer_transactions` where cust_id='".$custid."' and type='sale_restaurant' and mop='credit' and payment_for='res' and allotment_id='".$cust_row['sno']."'";
		$run_res= execute_query($sql_res);
		$row_res= mysqli_fetch_array($run_res);
		$res_amt= $row_res['total'];
		$sql_advance="select sum(amount) as total from `customer_transactions` where cust_id='".$custid."' and type='ADVANCE_AMT' and payment_for='room_rent'";
		$advance_res=execute_query($sql_advance);
		$row_advance=mysqli_fetch_array($advance_res);
		$advance_amt=$row_advance['total'];
		$sql_paid="select sum(amount) as total from `customer_transactions` where cust_id='".$custid."' and type='ADVANCE_PAID' and payment_for='room_rent'";
		$paid_res=execute_query($sql_paid);
		$row_paid=mysqli_fetch_array($paid_res);
		$paid_amt=$row_paid['total'];
		$pen_amount = $advance_amt - $paid_amt;
		$opening=get_cust_balance("1970-01-01",$_GET['exit_date'], $row['sno']);
		array_push($final, array("advance_amt"=>$pen_amount , "id"=>$row['sno'], "label"=>$row['cust_name'].' ('.$row['count'].')', "cust_name"=>$row['cust_name'].'-'.$row['company_name'], "mobile" => $row['mobile'], "rooms"=>$rooms, "opening"=>$opening,"res_bal"=>$room_res_bill));
	}
}



if($_GET['id']=='room_number'){
	if(!isset($_GET['exit_date'])){
		$_GET['exit_date'] = date("Y-m-d H:i:s");
	}
	$final=array();
	$rooms=array();

	$sql='select customer.sno as sno, allotment.sno as allot_id,advance_booking_id, allotment.hold_date as hold_date , cust_name, room_name, mobile, room_id,original_room_rent, room_rent,taxable_amount,other_charges, allotment_date, cust_id, guest_name, count(*) as count from allotment left join customer on customer.sno = allotment.cust_id left join room_master on room_master.sno=allotment.room_id where (exit_date is null or exit_date = "") and room_name like "%'.$q.'%" group by cust_id';
	$result = execute_query($sql);
	while($row = mysqli_fetch_array($result)){
		$res_allot=array();
		if($row['count']>1){
			$sql='select * from allotment where cust_id='.$row['sno'].' and (exit_date is null or exit_date = "")';
			$result = execute_query($sql);
			while($row = mysqli_fetch_array($result)){
				$balance = check_pendency($allotment['room_rent'], $allotment['allotment_date'], $allotment['cust_id'], $_GET['exit_date'], $allotment['sno']);
				$day1=get_days($row['allotment_date'], $_GET['exit_date']);
				$res_allot[] = $allotment['sno'];
				$sql1='select * from room_master where sno='.$allotment['room_id'];
				$result1 = execute_query($sql1);
				$extra=0;
				while($room=mysqli_fetch_array($result1)){
					if($allotment['other_charges'] == NULL)
					{
						$extra=0;
					}
					else{
						$extra=$allotment['other_charges'];
					}
					$hold = '';
					if($row['hold_date'] == ''){
						$hold = '';
					}
					else{
						$hold = '<span style="color:red;font-size:18px;"><b>Hold</b></span>';
					}
					array_push($rooms, array("hold"=>$hold , "customer_id"=>$row['sno'], "room_id"=>$room['sno'], "label"=>$room['room_name'], "allotment_id"=>$allotment['sno'], "balance"=>$balance, "extra_bed"=>$extra, "tax_rent"=>$allotment['taxable_amount'] * $day1, "org_rent"=>$allotment['original_room_rent'] * $day1,"org"=>$row['original_room_rent']+$extra , "guest_name"=>$allotment['guest_name']));
			
				}
			}
		}
		else{
			if($row['other_charges'] == NULL)
			{
				$extra=0;
			}
			else{
				$extra=$row['other_charges'];
			}
			$balance = check_pendency($row['room_rent'], $row['allotment_date'], $row['cust_id'], $_GET['exit_date'], $row['allot_id']);
			$day1=get_days($row['allotment_date'], $_GET['exit_date']);
			$res_allot[] = $row['allot_id'];
			$sql1='select * from room_master where sno='.$row['room_id'];
			$result1 = execute_query($sql1);
			$room = mysqli_fetch_assoc($result1);
			$hold = '';
			if($row['hold_date'] == ''){
				$hold = '';
			}
			else{
				$hold = '<span style="color:red;font-size:18px;"><b>Hold</b></span>';
			}
			array_push($rooms, array("hold"=>$hold , "customer_id"=>$row['sno'], "room_id"=>$room['sno'], "label"=>$room['room_name'], "allotment_id"=>$row['allot_id'], "balance"=>$balance,"extra_bed"=>$extra,"tax_rent"=>$row['taxable_amount'] * $day1,"org_rent"=>$row['original_room_rent'] * $day1,"org"=>$row['original_room_rent']+$extra,"guest_name"=>$row['guest_name']));
		}
		$allot=$row['allotment_date'];
		$allot=date('Y-m-d',strtotime($allot));
		$exitdate=$_GET['exit_date'];
		$exitdate=date('Y-m-d',strtotime($exitdate));
		$custid=$row['sno'];
		$allotid=$row['allot_id'];
		$sql_last="select * from allotment where cust_id='".$custid."' and invoice_no !='0' order by sno desc limit 1";
		$run_last=execute_query($sql_last);
		$row_last=mysqli_fetch_array($run_last);
		$sql11="SELECT sum(amount) as amount FROM `customer_transactions` WHERE cust_id='".$custid."' and allotment_id in (".implode(",", $res_allot).") and type='sale_restaurant' and (remarks='credit' or remarks='6') and (mop ='credit' or mop ='6')";
		$result11 = execute_query($sql11);
		$resbal = mysqli_fetch_assoc($result11);
	    $res_amt=$resbal['amount'];
		$sql_advance="select sum(amount) as total from `customer_transactions` where cust_id='".$custid."' and type='ADVANCE_AMT' and payment_for='room_rent'";
		$advance_res=execute_query($sql_advance);
		$row_advance=mysqli_fetch_array($advance_res);
		$advance_amt=$row_advance['total'];
		$sql_paid="select sum(amount) as total from `customer_transactions` where cust_id='".$custid."' and type='ADVANCE_PAID' and payment_for='room_rent'";
		$paid_res=execute_query($sql_paid);
		$row_paid=mysqli_fetch_array($paid_res);
		$paid_amt=$row_paid['total'];
		$pen_amount = $advance_amt - $paid_amt;
		$opening=get_cust_balance("1970-01-01", $row_last['exit_date'], $row['sno']);
		array_push($final, array("advance_amt"=>$pen_amount , "id"=>$row['sno'], "label"=>$row['room_name'].' ('.$row['count'].')', "cust_name"=>$row['cust_name'], "mobile" => $row['mobile'], "rooms"=>$rooms, "opening"=>$opening,"res_bal"=>$res_amt));
	}

}

if($_GET['id']=='room'){
	if(!$_GET['exit_date']){
		$_GET['exit_date'] = date("Y-m-d H:i:s");
	}
	$final=array();
	$rooms=array();
	
	$sql = 'select * from allotment where room_id="'.$_GET['rid'].'" and exit_date is null  or exit_date=""';
	$result=execute_query($sql);
	$allotment=mysqli_fetch_assoc($result);
	$sql = 'select * from customer where sno='.$allotment['cust_id']; 
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	$balance = check_pendency($allotment['room_rent'], $allotment['allotment_date'], $allotment['cust_id'], $_GET['exit_date'], $allotment['sno']);
	$sql1='select * from room_master where sno='.$allotment['room_id'];
	$result1 = execute_query($sql1);
	$room = mysqli_fetch_assoc($result1);
	array_push($rooms, array("room_id"=>$room['sno'], "label"=>$room['room_name'], "allotment_id"=>$allotment['sno'], "balance"=>$balance));
	array_push($final, array("id"=>$row['sno'], "label"=>$row['cust_name'], "cust_name"=>$row['cust_name'], "mobile" => $row['mobile'], "rooms"=>$rooms));
}
if($_GET['id']=='rent'){
	$final=array();
	$sql = 'select sno, room_name, sum(rent) as rent, rent_double, rent_extra, occupancy from room_master where sno in ('.$q.')'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		array_push($final, array("id"=>$row['sno'], "label"=>$row['room_name'], "rent"=>$row['rent'], "rent_double"=>$row['rent_double'], "rent_extra"=>$row['rent_extra'], "occupancy"=>$row['occupancy']));
	}
	
}
if($_GET['id']=='rent_room'){
	$sql = 'select * from allotment where sno="'.$_GET['allot'].'"';
	$result = execute_query($sql);
	$old_data= mysqli_fetch_assoc($result);
	$final=array();
	$sql = 'select sno, room_name, rent, rent_double, rent_extra, occupancy from room_master where sno in ('.$q.')'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		array_push($final, array("id"=>$row['sno'], "label"=>$row['room_name'], "rent"=>$row['rent'], "rent_double"=>$row['rent_double'], "rent_extra"=>$row['rent_extra'], "occupancy"=>$row['occupancy'],"extra_bed"=>$old_data['other_charges']));
	}
	
}

if($_GET['id']=='cust_name1'){
	$final=array();
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or company_name like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		$cust_name=$row['cust_name'];

		$sql_allot="select * from allotment where cust_id='".$row['sno']."' and invoice_no='0'";
	    $run_allot=execute_query($sql_allot);
	    $allot_row=mysqli_fetch_array($run_allot);
	    $allot_id=$allot_row['sno'];
	
		array_push($final, array("id"=>$row['sno'], "label"=>$cust_name, "cust_name"=>$cust_name,"mobile" => $row['mobile'],"company" => $row['company_name'],"address" => $row['address'],"id_no" => $row['id_1'],"gst_no" => $row['id_2'], "id_3"=>$row['id_3'], "allotment_id" => $allot_id));
	}
}
if($_GET['id']=='ledger_customer'){
	$final=array();
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or mobile like "%'.$q.'%" or company_name like "%'.$q.'%" '; 
	//echo $sql;
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'].'-'.$row['cust_name']));
	}
	
}

if($_GET['id']=='customer'){
	$final=array();
	$date = date('Y-m-d');
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or mobile like "%'.$q.'%" or company_name like "%'.$q.'%" '; 
	//echo $sql;
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		
		//echo $row1['addva'];
         	$amount = get_cust_balance("1970-01-01" , $date , $row['sno']);
         	if($amount <0){
         		$amount=0;
         	}else{
         		$amount;
         	}

		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'].'-'.$row['cust_name'], "cust_name"=>$row['cust_name'],"mobile" => $row['mobile'], "gstin" => $row['id_2'], "address" => $row['address'] , "amount" => $amount));
	}
	
}
if($_GET['id']=='customer_payment'){
	$final=array();
	$date = date('Y-m-d');
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or mobile like "%'.$q.'%" or company_name like "%'.$q.'%" '; 
	//echo $sql;
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		
		//echo $row1['addva'];
         	$amount = get_cust_balance("1970-01-01" , $date , $row['sno']);
         	if($amount <0){
         		$amount=substr($amount,1);
         	}else{
         		$amount = 0;
         	}

		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'].'-'.$row['cust_name'], "cust_name"=>$row['cust_name'],"mobile" => $row['mobile'], "gstin" => $row['id_2'], "address" => $row['address'] , "amount" => $amount));
	}
	
}
if($_GET['id']=='customer_receipt'){
	$final=array();
	$date = date('Y-m-d');
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or mobile like "%'.$q.'%" or company_name like "%'.$q.'%" '; 
	//echo $sql;
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		
		//echo $row1['addva'];
         	$amount = get_cust_balance("1970-01-01" , $date , $row['sno']);
         	if($amount <0){
         		$amount=substr($amount,1);
         	}else{
         		$amount;
         	}

		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'].'-'.$row['cust_name'], "cust_name"=>$row['cust_name'],"mobile" => $row['mobile'], "gstin" => $row['id_2'], "address" => $row['address'] , "amount" => $amount));
	}
	
}
if($_GET['id']=='customer_credit'){
	$final=array();
	$date = date('Y-m-d');
	$txt = '';
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or mobile like "%'.$q.'%" or company_name like "%'.$q.'%" '; 
	//echo $sql;
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		
		//echo $row1['addva'];
        $amount = get_cust_balance("1970-01-01" , $date , $row['sno']);
        if($amount <0){
        	$amount=0;
        }else{
        	$amount;
        }
        $sql_credit = 'SELECT * FROM `customer_transactions` WHERE `cust_id`="'.$row['sno'].'" AND `mop`="credit" AND `type` IN ("sale_restaurant" , "BAN_AMT" , "RENT")';
        $result_credit = execute_query($sql_credit);
        $n = 0;
        $grand_total = 0;
        $txt = '<table><tr><th>S.No.</th><th>Checkbox</th><th>Bill Type</th><th>Invoice No.</th><th>Amount</th></tr>';
        while($row_credit = mysqli_fetch_array($result_credit)){
        	$credit_amount = $row_credit['amount'] - $row_credit['advance_set_amt'] - $row_credit['credit_set_amt'];
        	if($credit_amount > 0){
        		$bill_type = '';
	        	$grand_total += $credit_amount;
	        	if($row_credit['type']=="RENT" AND $row_credit['payment_for']==""){
					$bill_type = "ROOM";
				}
				else if($row_credit['type']=="sale_restaurant" AND $row_credit['payment_for']="res"){
					if (strpos($row_credit['invoice_no'], 'R') !== false){
						$bill_type = "Room Service";
					}
					else{
						$bill_type = "Restaurant";
					}
				}
				else if($row_credit['type']=="BAN_AMT"){
					$bill_type = "Banquet";
				}
	        	$txt .= '<tr><td>'.++$n.'</td><td><input type="hidden" name="sno_'.$n.'" id="sno_'.$n.'" value="'.$row_credit['sno'].'"><input type="checkbox" name="checkbox_'.$n.'" id="checkbox_'.$n.'" class="form-control" onclick="add_amount();"></td><td>'.$bill_type.'</td><td>'.$row_credit['invoice_no'].'</td><td><input type="text" name="amount_'.$n.'" id="amount_'.$n.'" value="'.$credit_amount.'" readonly><input type="hidden" name="credit_set_amount_'.$n.'" id="credit_set_amount_'.$n.'" value="'.$row_credit['credit_set_amt'].'"></td></tr>';
	        }
        }
        $txt .= '<tr><th colspan="4"><input type="hidden" name="number_of_credit" id="number_of_credit" value="'.$n.'">Total :</th><th>'.$grand_total.'</th></tr></table>';
		array_push($final, array("txt"=>$txt , "id"=>$row['sno'], "label"=>$row['company_name'].'-'.$row['cust_name'], "cust_name"=>$row['cust_name'],"mobile" => $row['mobile'], "gstin" => $row['id_2'], "address" => $row['address'] , "amount" => $amount));
	}
	
}
if($_GET['id']=='invoice_credit'){
	$final=array();
	$date = date('Y-m-d');
	$txt = '';
	$sql_invoice = 'select * from customer_transactions where `mop`="credit" AND `type` IN ("sale_restaurant" , "BAN_AMT" , "RENT") AND `invoice_no` like "'.$q.'" '; 
	//echo $sql;
	$res_invoice = execute_query($sql_invoice);
	while($row_invoice = mysqli_fetch_array($res_invoice)){
		$sql = 'select * from customer where sno="'.$row_invoice['cust_id'].'" '; 
		//echo $sql;
		$res = execute_query($sql);
		$row = mysqli_fetch_array($res);
		//echo $row1['addva'];
        $amount = get_cust_balance("1970-01-01" , $date , $row['sno']);
        if($amount <0){
        	$amount=0;
        }else{
        	$amount;
        }
        $sql_credit = 'SELECT * FROM `customer_transactions` WHERE `sno`="'.$row_invoice['sno'].'"';
        $result_credit = execute_query($sql_credit);
        $n = 0;
        $grand_total = 0;
        $txt = '<table><tr><th>S.No.</th><th>Checkbox</th><th>Bill Type</th><th>Invoice No.</th><th>Amount</th></tr>';
        while($row_credit = mysqli_fetch_array($result_credit)){
        	$credit_amount = $row_credit['amount'] - $row_credit['advance_set_amt'] - $row_credit['credit_set_amt'];
        	if($credit_amount > 0){
        		$bill_type = '';
	        	$grand_total += $credit_amount;
	        	if($row_credit['type']=="RENT" AND $row_credit['payment_for']==""){
					$bill_type = "ROOM";
				}
				else if($row_credit['type']=="sale_restaurant" AND $row_credit['payment_for']="res"){
					if (strpos($row_credit['invoice_no'], 'R') !== false){
						$bill_type = "Room Service";
					}
					else{
						$bill_type = "Restaurant";
					}
				}
				else if($row_credit['type']=="BAN_AMT"){
					$bill_type = "Banquet";
				}
	        	$txt .= '<tr><td>'.++$n.'</td><td><input type="hidden" name="sno_'.$n.'" id="sno_'.$n.'" value="'.$row_credit['sno'].'"><input type="checkbox" name="checkbox_'.$n.'" id="checkbox_'.$n.'" class="form-control" onclick="add_amount();"></td><td>'.$bill_type.'</td><td>'.$row_credit['invoice_no'].'</td><td><input type="text" name="amount_'.$n.'" id="amount_'.$n.'" value="'.$credit_amount.'" readonly><input type="hidden" name="credit_set_amount_'.$n.'" id="credit_set_amount_'.$n.'" value="'.$row_credit['credit_set_amt'].'"></td></tr>';
	        }
        }
        $txt .= '<tr><th colspan="4"><input type="hidden" name="number_of_credit" id="number_of_credit" value="'.$n.'">Total :</th><th>'.$grand_total.'</th></tr></table>';
		array_push($final, array("txt"=>$txt , "id"=>$row['sno'], "label"=>$row['company_name'].'-'.$row['cust_name'].'-'.$row_invoice['invoice_no'], "cust_name"=>$row['company_name'].'-'.$row['cust_name'],"mobile" => $row['mobile'], "gstin" => $row['id_2'], "address" => $row['address'] , "amount" => $amount));
	}
	
}
if($_GET['id']=='company_name'){
	$final=array();
	$sql = 'select * from customer where company_name like "%'.$q.'%" or mobile like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'], "cust_name"=>$row['cust_name'],"company" => $row['company_name'],"mobile" => $row['mobile'], "address" => $row['address'],"gst_no" => $row['id_2'],"id_no" => $row['id_1']));
	}
	
}
if($_GET['id']=='company_name_advance'){
	$final=array();
	$sql = 'select * from customer where company_name like "%'.$q.'%" or mobile like "%'.$q.'%"'; 
	$res = execute_query($sql);
	$ad = '';
	while($row = mysqli_fetch_array($res)){
		$room_number_inhouse = '';
		$sql_advance = 'SELECT * FROM `advance_booking` WHERE `cust_id`="'.$row['sno'].'" AND purpose!="advance_for" AND `status`="0" ORDER BY `sno` DESC';
		$result_advance = execute_query($sql_advance);
		$ad = '<td colspan="2">&nbsp;</td><td>On Previous Booking</td><td><select name="advance_for" id="advance_for">';
		while($row_advance = mysqli_fetch_array($result_advance)){
			$pur = '';
			if($row_advance['purpose'] == "room_rent"){
				$pur = "Room Rent";
			}
			elseif($row_advance['purpose'] == "banquet_rent"){
				$pur = "Banquet Rent";
			}
			$ad .= '<option value="'.$row_advance['sno'].'">'.$row_advance['allotment_date'].'-'.$pur.'</option>';
		}
		$ad .= '</select></td>';
		$sql_advance_checkin = 'SELECT * FROM `allotment` WHERE `cust_id`="'.$row['sno'].'" AND (`exit_date`="" OR `exit_date` IS NULL) ORDER BY `sno` DESC';
		$result_advance_checkin = execute_query($sql_advance_checkin);
		$ad_checkin = '<td colspan="2">&nbsp;</td><td>On Checkin</td><td><select name="advance_for_checkin" id="advance_for_checkin">';
		while($row_advance_checkin = mysqli_fetch_array($result_advance_checkin)){
			$sql_checkin = 'SELECT * FROM `room_master` WHERE `sno`="'.$row_advance_checkin['room_id'].'"';
			$result_checkin = execute_query($sql_checkin);
			$row_checkin = mysqli_fetch_array($result_checkin);
			$ad_checkin .= '<option value="'.$row_advance_checkin['sno'].'">'.$row_advance_checkin['allotment_date'].'-Room:'.$row_checkin['room_name'].'</option>';
			$room_number_inhouse .= '('.$row_checkin['room_name'].')';
		}
		$ad_checkin .= '</select></td>';
		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'].'-'.$room_number_inhouse, "cust_name"=>$row['cust_name'],"company" => $row['company_name'],"mobile" => $row['mobile'], "address" => $row['address'],"gst_no" => $row['id_2'],"id_no" => $row['id_1'] , "advance"=>$ad , "advance_checkin"=>$ad_checkin));
	}
}

if($_GET['id']=='cust_name_advance'){
	$final=array();
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or company_name like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		$room_number_inhouse = '';
		$cust_name=$row['cust_name'];
		$sql_advance = 'SELECT * FROM `advance_booking` WHERE `cust_id`="'.$row['sno'].'" AND purpose!="advance_for" AND `status`="0" ORDER BY `sno` DESC';
		$result_advance = execute_query($sql_advance);
		$ad = '<td colspan="2">&nbsp;</td><td>On Previous Booking</td><td><select name="advance_for" id="advance_for">';
		while($row_advance = mysqli_fetch_array($result_advance)){
			$pur = '';
			if($row_advance['purpose'] == "room_rent"){
				$pur = "Room Rent";
			}
			elseif($row_advance['purpose'] == "banquet_rent"){
				$pur = "Banquet Rent";
			}
			$ad .= '<option value="'.$row_advance['sno'].'">'.$row_advance['allotment_date'].'-'.$pur.'</option>';
		}
		$ad .= '</select></td>';
		$sql_advance_checkin = 'SELECT * FROM `allotment` WHERE `cust_id`="'.$row['sno'].'" AND (`exit_date`="" OR `exit_date` IS NULL) ORDER BY `sno` DESC';
		$result_advance_checkin = execute_query($sql_advance_checkin);
		$ad_checkin = '<td colspan="2">&nbsp;</td><td>On Checkin</td><td><select name="advance_for_checkin" id="advance_for_checkin">';
		while($row_advance_checkin = mysqli_fetch_array($result_advance_checkin)){
			$sql_checkin = 'SELECT * FROM `room_master` WHERE `sno`="'.$row_advance_checkin['room_id'].'"';
			$result_checkin = execute_query($sql_checkin);
			$row_checkin = mysqli_fetch_array($result_checkin);
			$ad_checkin .= '<option value="'.$row_advance_checkin['sno'].'">'.$row_advance_checkin['allotment_date'].'-Room:'.$row_checkin['room_name'].'</option>';
			$room_number_inhouse .= '('.$row_checkin['room_name'].')';
		}
		$ad_checkin .= '</select></td>';
		array_push($final, array("id"=>$row['sno'], "label"=>$cust_name.'-'.$room_number_inhouse, "cust_name"=>$cust_name,"mobile" => $row['mobile'],"company" => $row['company_name'],"address" => $row['address'],"id_no" => $row['id_1'],"gst_no" => $row['id_2'],"advance"=>$ad , "advance_checkin"=>$ad_checkin));
	}
}

if($_GET['id']=='company_name_banquet'){
	$final=array();
	$sql = 'select * from customer where company_name like "%'.$q.'%" or mobile like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		$sql_advance_amount = 'SELECT SUM(`amount`) as advance_amount FROM `customer_transactions` WHERE `cust_id`="'.$row['sno'].'" AND `type`="ADVANCE_AMT" AND `payment_for`="banquet_rent"';
		$row_advance_amount = mysqli_fetch_array(execute_query($sql_advance_amount));
		$sql_advance_paid = 'SELECT SUM(`amount`) as advance_paid FROM `customer_transactions` WHERE `cust_id`="'.$row['sno'].'" AND `type`="ADVANCE_PAID" AND `payment_for`="banquet_rent"';
		$row_advance_paid = mysqli_fetch_array(execute_query($sql_advance_paid));
		$advance_amount = $row_advance_amount['advance_amount'] - $row_advance_paid['advance_paid'];
		array_push($final, array("id"=>$row['sno'], "label"=>$row['company_name'], "cust_name"=>$row['cust_name'],"company" => $row['company_name'],"mobile" => $row['mobile'], "address" => $row['address'],"gst_no" => $row['id_2'],"id_no" => $row['id_1'] , "advance_amount"=>$advance_amount));
	}	
}
if($_GET['id']=='cust_name_banquet'){
	$final=array();
	$sql = 'select * from customer where cust_name like "%'.$q.'%" or company_name like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		$sql_advance_amount = 'SELECT SUM(`amount`) as advance_amount FROM `customer_transactions` WHERE `cust_id`="'.$row['sno'].'" AND `type`="ADVANCE_AMT" AND `payment_for`="banquet_rent"';
		$row_advance_amount = mysqli_fetch_array(execute_query($sql_advance_amount));
		$sql_advance_paid = 'SELECT SUM(`amount`) as advance_paid FROM `customer_transactions` WHERE `cust_id`="'.$row['sno'].'" AND `type`="ADVANCE_PAID" AND `payment_for`="banquet_rent"';
		$row_advance_paid = mysqli_fetch_array(execute_query($sql_advance_paid));
		$advance_amount = $row_advance_amount['advance_amount'] - $row_advance_paid['advance_paid'];
		array_push($final, array("id"=>$row['sno'], "label"=>$row['cust_name'], "cust_name"=>$row['cust_name'],"mobile" => $row['mobile'],"company" => $row['company_name'],"address" => $row['address'],"id_no" => $row['id_1'],"gst_no" => $row['id_2'] , "advance_amount"=>$advance_amount));
	}
}
if($_GET['id']=='waitor'){
	$final=array();
	$sql = 'SELECT * FROM `admin_waiter` WHERE name like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		array_push($final, array("id"=>$row['sno'], "label"=>$row['name'], "waiter_name"=>$row['name']));
	}
	
}
if($_GET['id']=='party_name'){
	$final=array();
	$sql = 'select * from monthly_bills where party_name like "%'.$q.'%" group by party_name '; 
	$result = execute_query($sql);
	while($row = mysqli_fetch_array($result)){
		$abc='';
		$sql = 'select * from monthly_bills where party_name like "'.$row['party_name'].'"'; 
		$result1 = execute_query($sql1);
		$abc='';
		while($row1=mysqli_fetch_assoc($result1))
		{
			$abc .= '<option value="'.$row1['sno'].'">'.$row1['bill_name'].'</option>';
		}
		array_push($final, array("id"=>$row['sno'], "label"=>$row['party_name'], "party_name"=>$row['party_name'],"amount" => $row['amount'], "bill_name"=>$abc));
	}
}
if($_GET['id']=='prod'){
	$final=array();
	$sql = 'select * from stock_available where description like "%'.$q.'%" or sno="'.$q.'%" or barcode like "%'.$q.'%"'; 
	$res = execute_query($sql);
	while($row = mysqli_fetch_array($res)){
		array_push($final, array("id"=>$row['sno'], "label"=>$row['description']));
	}
	
}
if($_GET['id']=='red_kot'){
	$final = array();
	$sql = 'select * from kitchen_ticket_temp where sno='.$q;
	//echo $sql;
	$result = execute_query($sql);
	$row = mysqli_fetch_assoc($result);
	//echo $row['item_price'];
	$price = $row['item_price'];
	//echo $price;
	$unit=$row['unit']-1;
	$sql = 'update kitchen_ticket_temp set unit="'.$unit.'" where sno="'.$q.'"';
	//echo $sql;
	execute_query($sql);
	$sql = 'select * from kitchen_ticket_temp where sno='.$q;
	
	$result = execute_query($sql);
	$row = mysqli_fetch_assoc($result);
	if($row['unit']==0){
		$sql = 'delete from kitchen_ticket_temp where sno="'.$q.'"';
		execute_query($sql);
	}
	array_push($final, array("qty"=>$row['unit'], "amount"=>$row['item_price']* $unit, "rate"=>$price));
}

if($_GET['id']=='red_kot_copy'){
	$final = array();
	$sql = 'select * from kitchen_ticket_temp_2 where sno='.$q;
//	echo $sql;
	$result = execute_query($sql);
	$row = mysqli_fetch_assoc($result);
	$price = $row['item_price'];
	//echo $price;
	$unit=$row['unit']-1;
	$sql = 'update kitchen_ticket_temp_2 set unit="'.$unit.'" where sno="'.$q.'"';
	execute_query($sql);
	$sql = 'select * from kitchen_ticket_temp_2 where sno='.$q;
	$result = execute_query($sql);
	$row = mysqli_fetch_assoc($result);
	if($row['unit']==0){
		$sql = 'delete from kitchen_ticket_temp_2 where sno="'.$q.'"';
		execute_query($sql);
	}
	array_push($final, array("qty"=>$row['unit'], "amount"=>$row['item_price'] * $unit, "rate"=>$price));
}
if($_GET['id']=='mop'){
	$final = array();
	$sql = 'update invoice_sale_restaurant set mode_of_payment="'.$_GET['mop'].'" , mop_edited_by="'.$_SESSION['username'].'" , mop_edition_time="'.time().'" where sno='.$q;
	execute_query($sql);
	if(!mysqli_error($db)){
		array_push($final, array("result"=>"true"));
		$sql_customer_transaction = 'update customer_transactions set mop="'.$_GET['mop'].'" , mop_edited_by="'.$_SESSION['username'].'" , mop_edition_time="'.time().'" where number='.$q;
		execute_query($sql_customer_transaction);
	}
	else{
		array_push($final, array("result"=>"false"));
		
	}
}
if($_GET['id']=='advance_amount_edit'){
	$final = array();
	$sql_ad = 'update advance_booking set advance_amount="'.$_GET['amount'].'",amount_edited_by="'.$_SESSION['username'].'" , amount_edition_time="'.time().'" where `sno`='.$q;
		execute_query($sql_ad);
		if(!mysqli_error($db)){
			array_push($final, array("result"=>"true"));	
			$sql_customer_transaction = 'update customer_transactions set amount="'.$_GET['amount'].'" where `advance_booking_id`='.$q;
			execute_query($sql_customer_transaction);
		}
		else{
			array_push($final, array("result"=>"false"));
		}
}
if($_GET['id']=='mop_room'){
	$final = array();
	$sql_customer_transaction = 'update customer_transactions set mop="'.$_GET['mop'].'" , mop_edited_by="'.$_SESSION['username'].'" , mop_edition_time="'.time().'" where `sno`='.$q;
	execute_query($sql_customer_transaction);
	if(!mysqli_error($db)){
		array_push($final, array("result"=>"true"));
		
	}
	else{
		array_push($final, array("result"=>"false"));
		
	}
}
if($_GET['id']=='remark_change'){
	$final = array();
	$sql_customer_transaction = 'update customer_transactions set credit_settelment_remark="'.$_GET['remark'].'" where `sno`='.$q;
	execute_query($sql_customer_transaction);
	if(!mysqli_error($db)){
		array_push($final, array("result"=>"true"));
		
	}
	else{
		array_push($final, array("result"=>"false"));
		
	}
}
echo array_to_json($final);

function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = json_encode($key);

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = json_encode($value);
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = json_encode($value);
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}

if(isset($_POST['search'])){
			 $search= $_POST['search'];
			 //echo "$search";
			 $query = "SELECT * FROM `client_master` WHERE  parents='' AND customer_name like '%".$search."%'";
			 //echo $query;
			 $result = execute_query($query);
			 $response = array();
			 while($row = mysqli_fetch_array($result) ){
			   $response[] = array("id"=>$row['sno'], "cust_name"=>$row['customer_name'], "address"=>$row['address'], "gst"=>$row['gstin'],"mobile"=>$row['mobile'], "label"=>$row['customer_name']);
			 }

			 echo json_encode($response);
		}

		if(isset($_POST['search1'])){
			 $search= $_POST['search1'];
			 //echo "$search";
			 $query = "SELECT * FROM `customer` WHERE  cust_name like '%".$search."%'";
			 //echo $query;
			 $result = execute_query($query);
			 $response = array();
			 while($row = mysqli_fetch_array($result) ){
			   $response[] = array( "cust_name"=>$row['cust_name'], "label"=>$row['cust_name']);
			 }

			 echo json_encode($response);
		}
		
?>