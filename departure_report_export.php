<?php
session_start();
include("scripts/settings.php");
//error_reporting(E_ALL);
if(isset($_SESSION['sql5'])){
	 $sql=$_SESSION['sql5'];

}

       $html ='<table>
        	<thead>
            	<tr style="background:#333; color:#FFF; text-align:center; font-size:13px;">
					<th>S.No.</th>
						<th>Guest Name</th>
						<th>Company Name</th>
						<th>Mobile</th>
						<th>Address</th>
						<th>ID Number</th>
						<th>Occupancy</th>
						<th>Room No.</th>
						<th class="no-print">Extra Bed</th>
						<th class="no-print">Total Rent</th>
						<th>Allotment Date</th>
						<th>Departure Date</th>
           	    </tr>
            </thead>'; 
              
				$result=execute_query($sql);

				$i=1;
			$occcu='';
			foreach($result as $row){
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
				if($row['hold_date']!=''){
					$col = 'red';
				}
				if($row['exit_date']==''){
					$row['exit_date'] = date("d-m-Y H:i");
				}
				$days = (strtotime($row['exit_date'])-strtotime($row['allotment_date']));
				$days = date("d", $days);
				$total_rent=($row['room_rent'])*$days;
				if($row['cancel_date']!=''){
					$col = '#F00"';
					$cancel = '<br />Cancelled On : '.$row['cancel_date'];
					$cancel_display = 'Uncancel';
				}
				else{
					$row_col = '';
					$cancel = '';
					$cancel_display = 'Cancel';
				}
				$html .='<tr style="background:'.$col.';border:1px solid black">
				<td>'.$i++.'</td>
				<td>'.$row['guest_name'].$cancel.'</td>
				<td>'. get_company_name($row['cust_id']).'</td>';
				$sql_cus="select * from customer where sno='".$row['cust_id']."'";
				$sql_run=execute_query($sql_cus);
				$row_cust=mysqli_fetch_array($sql_run);
			  $html .='<td>'.$row_cust['mobile'].'</td>';
			   if($row_cust['address'] != ''){
				$html .='<td>'.$row_cust['address'].'</td>';
			   }
			   else{
				$html .='<td>'.$row['guest_address'].'</td>';
			   }
			  $html .='
			   <td>'.$row_cust['id_type'].' : '.$row_cust['id_3'].'</td>
			   <td>'.$row['occupancy'].'</td>
				<td>'.get_room($row['room_id']).'</td>
				<td class="no-print">'.$row['other_charges'].'</td>
				<td class="no-print">'.$total_rent.'</td>
				
				<td>'.date("d-m-Y,h-i A" ,strtotime($row['allotment_date'])).'</td>
				<td>'.date("d-m-Y,h-i A" ,strtotime($row['departure_date'])).'</td>
				
				
				
			</tr>'
			;
			   $occcu +=$row['occupancy'];
			}
			$html .='<tr style="background:'.$col.';border:1px solid black">
			<td></td>
			<td></td>
			<td></td>

			<td></td>
			<td class="no-print"></td>
			<td class="" style=""><b style="font-size:20px" >Occupancy<b></td><td><b style="font-size:20px">'.$occcu.'</b></td><td></td><td></td><td class="no-print"></td><td class="no-print"></td><td class="no-print"></td></tr>';
				
			    
				$html .='</table>';
				
				header("Content-Type:application/xls");
                header("Content-Disposition:attachment;filename=download.xls");
                echo $html; ?>
	