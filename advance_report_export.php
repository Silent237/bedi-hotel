<?php
session_start();
include ("scripts/settings.php");
error_reporting(0);
if(isset($_SESSION['sql5'])){
	 $sql=$_SESSION['sql5'];

}
if(isset($_SESSION['sql6'])){
	 $sql_mop=$_SESSION['sql6'];

}

       $html ='<table>
        	<thead>
            	<tr style="background:#333; color:#FFF; text-align:center; font-size:13px;">
					<th>S.No.</th>
					<th>Receipt No.</th>
					<th>Comapny Name</th>
					<th>Guest Name</th>
					<th>Mobile</th>
					<th>Date Of Entry</th>
                    <th>Type</th>
                    <th>MOP</th>
                    <th>Advance Amount</th>
                    <th>Booking Date</th>
                    <th>No. Of Rooms</th>
                    <th>Room Number</th>
					<th class="no-print">Status</th>
           	    </tr>
            </thead>'; 
              
				$result=execute_query($sql);
				$i=1;
				$tot=0;
				foreach($result as $row)
				{
					
					$result_mop = execute_query($sql_mop);
					while($row_mop = mysqli_fetch_array($result_mop)){
						if($i%2==0){
							$col = '#CCC';
						}
						else{
							$col = '#EEE';
						}
						if($i%10==0){
							$css = 'page-break-after:always;';
						}
						else{
							$css = '';
						}
						if ($row_mop['type'] == 'ADVANCE_AMT_CANCEL') {
							$col = '#dd4a4a';
						}
						$sql='select * from customer where sno='.$row['cust_id'];
						$result = execute_query($sql);
						$details=mysqli_fetch_assoc( $result );
						$tot+= $row['advance_amount'];
						$html .='<tr style="background:'.$col.';border:1px solid black">
						<td>'.$i++.'</td>
						<td>'.$row['sno'].'</td>
						<td>'.$details['company_name'].'</td>
						<td>'.$row['guest_name'].'</td>
						<td>'.$details['mobile'].'</td>
						<td>'.date('d-m-Y' , strtotime($row['created_on'])).'</td>';
						if($row['purpose'] == "room_rent"){
							$html .='<td>Room Booking</td>';
						}
						elseif($row['purpose'] == "banquet_rent"){
							$html .='<td>Banquet Booking</td>';
						}
						elseif($row['purpose'] == "advance_for"){
							$sql_advance_for = 'SELECT * FROM `advance_booking` WHERE `sno`="'.$row['advance_for_id'].'"';
							$result_advance_for = execute_query($sql_advance_for);
							$row_advance_for = mysqli_fetch_array($result_advance_for);
							if($row_advance_for['purpose'] == "room_rent"){
								$html .='<td>Room Booking(Plus Amount)</td>';
							}
							elseif($row_advance_for['purpose'] == "banquet_rent"){
								$html .='<td>Banquet Booking(Plus Amount)</td>';
							}
						}
						elseif($row['purpose'] == "advance_for_checkin"){
							$html .='<td>Room Booking(In House Guest)</td>';
						}
						else{
							$html .='<td></td>';
						}
						
						
						$html .='<td class="editable" id="row_'.$row_mop['sno'].'">';
						if($row_mop['mop'] == "bank_transfer"){
							$html .='BANK TRANSFER';
							}elseif($row_mop['mop'] == "card_sbi"){
								$html .='CARD S.B.I.';
							}elseif($row_mop['mop'] == "card_pnb"){
								$html .='CARD P.N.B.';
							}else{
								$html .=' '.strtoupper($row_mop['mop']).'';
							}
						$html .= '</td>';
						//if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
							$html .='<td class="editable_amount" id="row_amount_'.$row['sno'].'">'.$row['advance_amount'].'</td>';
						/**}
						else{
							$html .='<td>'.$row['advance_amount'].'</td>';
						}**/
						$html .='<td>'.date('d-m-Y h:i:s' , strtotime($row['allotment_date'])).'</td>
						<td>'.$row['number_of_room'].'</td>
						<td>'.$row['room_number'].'</td>';

						if($row['status'] == 0 AND $row_mop['type'] == 'ADVANCE_AMT'){
							if($row['purpose'] == "room_rent"){
								$html .='<td class="no-print"><a href="allotment.php?check_in='.$row['sno'].'"  target="_blank">Allot Room</a></td>';
							}
							elseif($row['purpose'] == "banquet_rent"){
								$html .='<td class="no-print"><a href="banquet_hall.php?allot='.$row['sno'].'"  target="_blank">Allot Banquet</a></td>';
							}
							else{
								$html .='<td class="no-print">&nbsp;</td>';
							}
						}
						elseif($row_mop['type'] == 'ADVANCE_AMT_CANCEL'){
							$html .='<td class="no-print">Canceled</td>';
						}
						else{
							$html .='<td class="no-print">Booked</td>';
						}
						
						$html .='</tr>';
					}
				}
				$html .='<tr style="background:'.$col.';border:1px solid black"><th colspan="8">Total :</th><th>'.$tot.'</th><th colspan="7">&nbsp;</th></tr>';
				
			    
				$html .='</table>';
				
				header("Content-Type:application/xls");
                header("Content-Disposition:attachment;filename=download.xls");
                echo $html; 
?>