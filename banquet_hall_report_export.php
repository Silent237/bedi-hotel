<?php
session_start();
include("scripts/settings.php");
//error_reporting(E_ALL);
//print_r($_SESSION);
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
				$amount = 0;
				$cgst = 0;
				$sgst = 0;
				$grand_total = 0;
				while($row = mysqli_fetch_array($result)){
				$sql_customer = 'SELECT * FROM `customer` WHERE `sno`="'.$row['cust_id'].'"';
				$result_customer = execute_query($sql_customer);
				$row_customer = mysqli_fetch_array($result_customer);
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
				
							
					$html .='<tr style="background:'.$col.';border:1px solid black">
						<th>'.$i++.'</th>
						<td>'.$row_customer['cust_name'].'</td>
						<td>'.$row_customer['company_name'].'</td>
						<td>'.$row['invoice_no'].'</td>
						<td>'.$row['hall_type'].'</td>
						<td>'.$row['booking_date'].'</td>
						<td>'.$row['check_in_date'].'</td>
						<td>'.$row['mop'].'</td>
						<td>'.$row['amount'].'</td>
						<td>'.$row['sgst'].'</td>
						<td>'.$row['cgst'].'</td>
						<td>'.$row['grand_total'].'</td>
						';
					$html .='</tr>';
							
							$amount += $row['amount'];
							$sgst += $row['sgst'];
							$cgst += $row['cgst'];
							$grand_total += $row['grand_total']; 
						}
					
					$html .='<tr>
						<th colspan="8">Total:</th>
						<th>'.round($amount , 3).'</th>
						<th>'.round($sgst , 3).'</th>
						<th>'.round($cgst , 3).'</th>
						<th>'.round($grand_total , 3).'</th>
						<th colspan="3">&nbsp;</th>
					</tr>';
				
			    
				$html .='</table>';
				
				header("Content-Type:application/xls");
                header("Content-Disposition:attachment;filename=download.xls");
                echo $html; ?>
	