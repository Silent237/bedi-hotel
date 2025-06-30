<?php
include ("scripts/settings.php");


$type = $_POST['report_type'] ?? '';

switch ($type) {
    case 'total_rooms':
    echo '<div style="background:grey;">';
    $floor = '';  
    $sql = 'SELECT room_master.sno as sno, room_name, status, floor_name 
            FROM room_master 
            JOIN floor_master ON floor_master.sno = floor_id where room_master.room_status=1
            ORDER BY floor_id, room_name';

    $result = execute_query($sql);

    if (!isset($_POST['allotment_date'])) {
        foreach ($result as $row) {
            if ($floor != $row['floor_name']) {
                $floor = $row['floor_name'];
                echo '</div><div style="border:0px solid; float:left; width:95%">
                        <h2>' . $floor . '</h2><div style="clear:both;"></div>';
            }

            if ($row['status'] == '' || $row['status'] == 0) {
                $col = '#bbff33';
                $text_col = '#666666';
            } else {
                $col = '#F00';
                $text_col = '#fff';
            }

            echo '<div style="height:50px; line-height:50px; text-align:center; color:' . $text_col . '; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:' . $col . '; font-size:18px;">' . $row['room_name'] . '</div>';
        }
    } else {
        $allotment_date = date("Y-m-d 23:59:59", strtotime($_POST['allotment_date']));
        foreach ($result as $row) {
            if ($floor != $row['floor_name']) {
                $floor = $row['floor_name'];
                echo '</div><div style="border:0px solid; float:left; width:95%">
                        <h2>' . $floor . '</h2><div style="clear:both;"></div>';
            }

            $room_id = $row['sno'];
            $sql2 = "SELECT * FROM allotment 
                     WHERE room_id = '$room_id' 
                     AND '$allotment_date' BETWEEN allotment_date AND exit_date";
            $res2 = execute_query($sql2);

            if (mysqli_num_rows($res2) != 0) {
                $col = '#F00';
                $text_col = '#fff';
            } else {
                $col = '#bbff33';
                $text_col = '#666666';
            }

            echo '<div style="height:50px; line-height:50px; text-align:center; color:' . $text_col . '; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:' . $col . '; font-size:18px;">' . $row['room_name'] . '</div>';
        }
    }

    echo '</div></div>';
        break;

    case 'available_rooms':
        echo '<div style="background:grey;">';
    $floor = '';  
    $sql = 'SELECT room_master.sno as sno, room_name, status, floor_name 
            FROM room_master 
            JOIN floor_master ON floor_master.sno = floor_id where status=0 and room_status=1
            ORDER BY floor_id, room_name';

    $result = execute_query($sql);

    if (!isset($_POST['allotment_date'])) {
        foreach ($result as $row) {
            if ($floor != $row['floor_name']) {
                $floor = $row['floor_name'];
                echo '</div><div style="border:0px solid; float:left; width:95%">
                        <h2>' . $floor . '</h2><div style="clear:both;"></div>';
            }

         $col = '#bbff33';
                $text_col = '#666666';

            echo '<div style="height:50px; line-height:50px; text-align:center; color:' . $text_col . '; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:' . $col . '; font-size:18px;">' . $row['room_name'] . '</div>';
        }
    } else {
        $allotment_date = date("Y-m-d 23:59:59", strtotime($_POST['allotment_date']));
        foreach ($result as $row) {
            if ($floor != $row['floor_name']) {
                $floor = $row['floor_name'];
                echo '</div><div style="border:0px solid; float:left; width:95%">
                        <h2>' . $floor . '</h2><div style="clear:both;"></div>';
            }

            $room_id = $row['sno'];
            $sql2 = "SELECT * FROM allotment 
                     WHERE room_id = '$room_id' 
                     AND '$allotment_date' BETWEEN allotment_date AND exit_date";
            $res2 = execute_query($sql2);

            if (mysqli_num_rows($res2) != 0) {
                $col = '#F00';
                $text_col = '#fff';
            } else {
                $col = '#bbff33';
                $text_col = '#666666';
            }

            echo '<div style="height:50px; line-height:50px; text-align:center; color:' . $text_col . '; width:50px; border:1px solid; margin:5px; border-radius:10px; float:left; background:' . $col . '; font-size:18px;">' . $row['room_name'] . '</div>';
        }
    }

    echo '</div></div>';
        break;

    case 'total_checkin':
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="5">
        <tr style="background:#000; color:#FFF;">
            <th>S.No.</th>
            <th>Guest Name</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Room No.</th>
            <th>Extra Bed</th>
            <th>Total Rent</th>
            <th>Night</th>
            <th>Allotment Date</th>
            <th>Reference</th>
            <th>Status</th>
        </tr>';

    $sql = 'SELECT * FROM allotment WHERE DATE(allotment_date) = "' . date('Y-m-d').'"';

    $result = execute_query($sql);

    $i = 1;
    $grand_total_rent = 0;
    $night = 0;

    foreach ($result as $row) {
        $bgcolor = ($i % 2 == 0) ? "#CCC" : "#EEE";
        if (!empty($row['hold_date'])) $bgcolor = "red";

        // Calculate days
        $days = ($row['exit_date'] == '') 
                ? get_days($row['allotment_date'], date("d-m-Y H:i")) 
                : get_days($row['allotment_date'], $row['exit_date']);

        // Calculate total rent
        $total_rent = floatval($row['room_rent']) * $days;

        // Get customer info
        $cust_query = "SELECT * FROM `customer` WHERE sno = '".$row['cust_id']."'";
        $cust_data = mysqli_fetch_array(execute_query($cust_query));

        $mobile = $cust_data['mobile'] ?? '';
        $address = !empty($cust_data['address']) ? $cust_data['address'] : $row['guest_address'];

        // Cancel note
        $cancel_note = (!empty($row['cancel_date'])) ? "<br>Cancelled On: ".$row['cancel_date'] : "";

        echo "<tr style='background:$bgcolor; text-align:center;'>
                <td>$i</td>
                <td>{$row['guest_name']} $cancel_note</td>
                <td>$mobile</td>
                <td>$address</td>
                <td>" . get_room($row['room_id']) . "</td>
                <td>{$row['other_charges']}</td>
                <td>$total_rent</td>
                <td>$days</td>
                <td>" . date("d-m-Y, h:i A", strtotime($row['allotment_date'])) . "</td>
                <td>" . get_reference($row['reference']) . "</td>
                <td>" . ($row['exit_date'] == '' ? 'IN' : 'OUT') . "</td>
              </tr>";

        $i++;
        $night += $days;
        $grand_total_rent += $total_rent;
    }

    echo "<tr style='font-weight:bold; text-align:center; background:#ddd;'>
            <td colspan='6'>Total</td>
            <td>$grand_total_rent</td>
            <td>$night</td>
            <td colspan='3'>&nbsp;</td>
          </tr>";
    echo '</table>';
  
        break;

    case 'total_checkout':
        echo '<table width="100%" border="1" cellspacing="0" cellpadding="5">
        <tr style="background:#000; color:#FFF;">
            <th>S.No.</th>
            <th>Guest Name</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Room No.</th>
            <th>Extra Bed</th>
            <th>Total Rent</th>
            <th>Night</th>
            <th>Allotment Date</th>
            <th>Reference</th>
            <th>Status</th>
        </tr>';

    $sql = 'SELECT * FROM allotment WHERE DATE(exit_date) = "' . date('Y-m-d').'"';

    $result = execute_query($sql);

    $i = 1;
    $grand_total_rent = 0;
    $night = 0;

    foreach ($result as $row) {
        $bgcolor = ($i % 2 == 0) ? "#CCC" : "#EEE";
        if (!empty($row['hold_date'])) $bgcolor = "red";

        // Calculate days
        $days = ($row['exit_date'] == '') 
                ? get_days($row['allotment_date'], date("d-m-Y H:i")) 
                : get_days($row['allotment_date'], $row['exit_date']);

        // Calculate total rent
        $total_rent = floatval($row['room_rent']) * $days;

        // Get customer info
        $cust_query = "SELECT * FROM `customer` WHERE sno = '".$row['cust_id']."'";
        $cust_data = mysqli_fetch_array(execute_query($cust_query));

        $mobile = $cust_data['mobile'] ?? '';
        $address = !empty($cust_data['address']) ? $cust_data['address'] : $row['guest_address'];

        // Cancel note
        $cancel_note = (!empty($row['cancel_date'])) ? "<br>Cancelled On: ".$row['cancel_date'] : "";

        echo "<tr style='background:$bgcolor; text-align:center;'>
                <td>$i</td>
                <td>{$row['guest_name']} $cancel_note</td>
                <td>$mobile</td>
                <td>$address</td>
                <td>" . get_room($row['room_id']) . "</td>
                <td>{$row['other_charges']}</td>
                <td>$total_rent</td>
                <td>$days</td>
                <td>" . date("d-m-Y, h:i A", strtotime($row['allotment_date'])) . "</td>
                <td>" . get_reference($row['reference']) . "</td>
                <td>" . ($row['exit_date'] == '' ? 'IN' : 'OUT') . "</td>
              </tr>";

        $i++;
        $night += $days;
        $grand_total_rent += $total_rent;
    }

    echo "<tr style='font-weight:bold; text-align:center; background:#ddd;'>
            <td colspan='6'>Total</td>
            <td>$grand_total_rent</td>
            <td>$night</td>
            <td colspan='3'>&nbsp;</td>
          </tr>";
    echo '</table>';
  
        break;
        

     case 'advance_checkin':
    echo '<div style="margin:0;" id="container">
        <h2>Today Arrival Report</h2>
        <table width="100%" class="table table-bordered">
            <tr>
                <th style="background:#00888d; color:#FFF;">S.No.</th>
                <th style="background:#00888d; color:#FFF;">Company Name</th>
                <th style="background:#00888d; color:#FFF;">Guest Name</th>
                <th style="background:#00888d; color:#FFF;">Mobile</th>
                <th style="background:#00888d; color:#FFF;">Meal Plan</th>
                <th style="background:#00888d; color:#FFF;">Amount</th>
                <th style="background:#00888d; color:#FFF;">Type</th>
                <th style="background:#00888d; color:#FFF;">MOP</th>
                <th style="background:#00888d; color:#FFF;">Total Amount</th>
                <th style="background:#00888d; color:#FFF;">Advance Amount</th>
                <th style="background:#00888d; color:#FFF;">Due Amount</th>
                <th style="background:#00888d; color:#FFF;">Booking Date</th>
                <th style="background:#00888d; color:#FFF;">Check In Date</th>
                <th style="background:#00888d; color:#FFF;">Check Out Date</th>
                
            </tr>';

    $sql = 'SELECT * FROM advance_booking WHERE DATE(check_in) = "' . date("Y-m-d") . '"';
    $result = execute_query($sql);

    $i = 1;
    $tot_total = $tot_advance = $tot_due = $total_room = 0;

    while ($row = mysqli_fetch_array($result)) {
        $col = ($i % 2 == 0) ? '#CCC' : '#EEE';

        // Get customer details
        $customer = execute_query('SELECT * FROM customer WHERE sno=' . $row['cust_id']);
        $details = mysqli_fetch_assoc($customer);

        // Get MOP
        $sql_mop = 'SELECT * FROM `customer_transactions` WHERE `advance_booking_id`="' . $row['sno'] . '"';
        if (isset($_POST['submit_form'])) {
            if (!empty($_POST['mop'])) $sql_mop .= ' AND `mop`="' . $_POST['mop'] . '"';
            if (!empty($_POST['cancel_status'])) $sql_mop .= ' AND `type`="' . $_POST['cancel_status'] . '"';
        }
        $mop_result = execute_query($sql_mop);
        $row_mop = mysqli_fetch_assoc($mop_result) ?: ['type' => '', 'sno' => '', 'mop' => ''];

        if ($row_mop['type'] == 'ADVANCE_AMT_CANCEL') {
            $col = '#dd4a4a';
        }

        // Calculate amounts
        $tm = floatval($row['total_amount']) + floatval($row['kitchen_amount']);
        $advanceAmount = floatval($row['advance_amount']);
        $dueAmount = $tm - $advanceAmount;
        $tot_total += $tm;
        $tot_advance += $advanceAmount;
        $tot_due += $dueAmount;

        // Room category info
        $catIds = explode(',', $row['cat_id']);
        $roomTypes = [];
        foreach ($catIds as $catId) {
            $catRes = execute_query('SELECT room_type FROM category WHERE sno="' . trim($catId) . '"');
            if ($catRow = mysqli_fetch_assoc($catRes)) $roomTypes[] = $catRow['room_type'];
        }
        $roomTypeList = implode(', ', $roomTypes);

        // Room numbers
        $room_numbers = explode(',', $row['number_of_room']);
        $room_count = array_sum($room_numbers);
        $total_room += $room_count;

        // Attachment
        $e_id = mysqli_real_escape_string($db, $row['sno']);
        $attachmentRes = execute_query("SELECT * FROM attachment WHERE advance_id = '$e_id'");
        $attachments = [];
        while ($row1 = mysqli_fetch_assoc($attachmentRes)) $attachments[] = $row1;
        $attachmentsJson = json_encode($attachments);

        echo '<tr style="background:' . $col . '">
                <td>' . $i++ . '</td>
                <td>' . $details['company_name'] . '</td>
                <td>' . $row['guest_name'] . '</td>
                <td>' . $details['mobile'] . '</td>
                <td>' . $row['kitchen_dining'] . '</td>
                <td>' . $row['kitchen_amount'] . '</td>
                <td>';

        // Purpose logic
        switch ($row['purpose']) {
            case "room_rent": echo 'Room Booking'; break;
            case "banquet_rent": echo 'Banquet Booking'; break;
            case "advance_for":
                $advanceFor = execute_query('SELECT * FROM advance_booking WHERE sno="' . $row['advance_for_id'] . '"');
                $advanceForRow = mysqli_fetch_assoc($advanceFor);
                if ($advanceForRow['purpose'] == "room_rent") echo 'Room Booking (Plus Amount)';
                elseif ($advanceForRow['purpose'] == "banquet_rent") echo 'Banquet Booking (Plus Amount)';
                break;
            case "advance_for_checkin": echo 'Room Booking (In House Guest)'; break;
            default: echo ''; break;
        }

        echo '</td>
                <td>' . strtoupper(str_replace('_', ' ', $row_mop['mop'])) . '</td>
                <td>' . number_format($tm, 2, '.', '') . '</td>
                <td>' . $advanceAmount . '</td>
                <td>' . $dueAmount . '</td>
                <td>' . date('d-m-Y h:i:s', strtotime($row['allotment_date'])) . '</td>
                <td>' . date('d-m-Y h:i:s', strtotime($row['check_in'])) . '</td>
                <td>' . date('d-m-Y h:i:s', strtotime($row['check_out'])) . '</td>';
               

      

        echo '</tr>';
    }

    $sql = "SELECT SUM(CAST(remarks AS UNSIGNED)) AS total_remarks FROM category";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $totalRoom = $row['total_remarks'] ?? 0;

    echo '<tr style="background:#00888d; color:#FFF;">
            <th colspan="8">Total :</th>
            <th>' . number_format($tot_total, 2, '.', '') . '</th>
            <th>' . number_format($tot_advance, 2, '.', '') . '</th>
            <th>' . number_format($tot_due, 2, '.', '') . '</th>
            <th colspan="3"></th>
           
        </tr>';

    echo '</table></div>';
    break;


   case 'running_services':
    echo '<h2>Room Service</h2>	
    <div id="tables">';

    $sql = "SELECT * FROM `room_master` where booked_status=1  ORDER BY ABS(room_name)";
    $res = execute_query($sql);

    while ($row = mysqli_fetch_array($res)) {
        $id = $row['sno'];
        $roomName = htmlspecialchars($row['room_name'], ENT_QUOTES, 'UTF-8');
        $btnStyle = $row['booked_status'] == 1 ? 'background-color:red;' : '';

        echo '<button type="button" onclick="window.open(\'dine_in_order_room.php?room_id=' . $id . '\', \'_self\');" id="table_' . $id . '" style="' . $btnStyle . ' width:100px;height:100px; color:white; border-radius:20px; margin-right:20px;">' . $roomName . '</button>';
    }

    echo '</div>';
    break;

    case 'total_tables':
        echo '<div id="tables">';
			
				$sql="SELECT * FROM `res_table`";
				$res=execute_query($sql);
				while($row=mysqli_fetch_array($res)){
					$id=$row['sno'];
                    if($row['booked_status'] ==1){
				        echo '<button   type="button" onclick="window.open(\'dine_in_order_table.php?table_id='.$id.'\', \'_self\');" id="table_'.$id.'" style="background-color:red;width:100px;height:100px;margin-right:20px; border-radius:20px;">'.$row['table_number'].'</button></a>';
				    }
                    else{
                         echo '<button style="width:100px;height:100px;margin-right:20px; border-radius:20px;" type="button"  onclick="window.open(\'dine_in_order_table.php?table_id='.$id.'\', \'_self\');" id="table_'.$id.'">'.$row['table_number'].'</button></a>';
                    }
                }

			echo '</div>';
    
        break;

    case 'running_tables':
       echo '<div id="tables">';
			
				$sql="SELECT * FROM `res_table`";
				$res=execute_query($sql);
				while($row=mysqli_fetch_array($res)){
					$id=$row['sno'];
                    if($row['booked_status'] ==1){
				        echo '<button  type="button" onclick="window.open(\'dine_in_order_table.php?table_id='.$id.'\', \'_self\');" id="table_'.$id.'" style="background-color:red;width:100px;height:100px;margin-right:20px; border-radius:20px;">'.$row['table_number'].'</button></a>';
				    }
                   
                }

			echo '</div>';
        break;

    default:
        echo "<h3>Invalid report type.</h3>";
        break;


        
    } 