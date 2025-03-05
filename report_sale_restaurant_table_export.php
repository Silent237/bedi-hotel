<?php
session_start();
include("scripts/settings.php");
error_reporting(E_ALL);
if(isset($_SESSION['sql5'])){
	 $sql=$_SESSION['sql5'];

}
if(isset($_SESSION['sql6'])){
	 $sql_sum=$_SESSION['sql6'];

}
if(isset($_SESSION['sql7'])){
	 $sql_summary=$_SESSION['sql7'];

}

       $html ='<table>
        	<thead>
            	<tr style="background:#333; color:#FFF; text-align:center; font-size:13px;">
					<th>S.No.</th>
					<th>Company Name</th>
					<th>Guest Name</th>
					<th>GSTIN</th>
					<th>Taxable<br />Amount</th>
					<th>SGST</th>
					<th>CGST</th>
					<th>Invoice<br />Amount</th>
					<th>Discount</th>
					<th>Amount<br />Payable</th>
					<th>Sale Date</th>
					<th>Unit</th>
					<th>Mode of<br />Payment</th>
					<th>Table/Room</th>
					<th>Kot No.</th>
					<th>Invoice No.</th>
           	    </tr>
            </thead>'; 
              
				$result=execute_query($sql);
				$row_sum = mysqli_fetch_array(execute_query($sql_sum, dbconnect()));
				
				
				include ('pagination/paginate.php'); //include of paginat page
				$total_results = mysqli_num_rows($result);
				$total_pages = ceil($total_results / $per_page);//total pages we going to have
				$tpages=$total_pages;
				if (isset($_GET['page'])) {
					$show_page = $_GET['page'];             //it will telles the current page
					if ($show_page > 0 && $show_page <= $total_pages) {
						$start = ($show_page - 1) * $per_page;
						$end = $start + $per_page;
					} else {
						// error - show first set of results
						$start = 0;              
						$end = $per_page;
					}
				} else {
					// if page isn't set, show first set of results
					$_GET['page'] = 1;
					$show_page = 1;
					$start = 0;
					$end = $per_page;
				}
				// display pagination
				$page = intval($_GET['page']);

				if ($page <= 0)
					$page = 1;


				$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages;
				 $html .='<div class="pagination"><ul>';
				if ($total_pages > 1) {
					 $html .= paginate($reload, $show_page, $total_pages);
				}
				 $html .= "</ul></div>";
			
				

				$i=1;
				$tot_qty=0;
				$tot_tax =0;
				$tot_taxable=0;
				$tot_amount=0;
				$tot_invoice=0;
				$tot_discount=0;
				$tot_cgst=0;
				$tot_sgst=0;
				for ($pgid = $start; $pgid < $end; $pgid++) {
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
					//print_r($row);
					if ($pgid == $total_results) {
						break;
					}
					mysqli_data_seek($result, $pgid);
					$row = mysqli_fetch_array($result);
					$i = $pgid+1;
					 $html .='
					<tr style="z-index:9999" style="background:'.$col.';border:1px solid black">
						<th>'.$i.'</th>
						<td>'.$row['company_name'].'</td>
						<td>'.$row['concerned_person'];
					/**	if($row['concerned_person']!=''){
							echo '<br /><small><b>'.$row['concerned_person'].'</b></small>';
						}**/
						if($row['department']!=''){
							 $html .='<br /><small><b>'.$row['department'].'</b></small>';
						}
						if($row['agent_id']!=''){
							 $html .='<br /><small>Agent : <b>'.get_agent_name($row['agent_id']).'</b></small>';
						}
						 $html .='</td>
						<td>'.$row['tin'].'</td>
						<td class="right">'.$row['taxable_amount'].'</td>
						<td class="right">'.($row['tot_vat']).'</td>
						<td class="right">'.($row['tot_sat']).'</td>
						<td class="right">'.$row['total_amount'].'</td>';
					
						if($row['tot_disc']==''){
							 $html .='<td class="right">'.$row['tot_disc'].'</td>';
							$tot_discount += ($row['tot_disc']);
						}
						else{
							 $html .='<td class="right">'.$row['other_discount'].'</td>';
							$tot_discount += ($row['other_discount']);
						}
						 $html .='
						<td class="right">'.round($row['grand_total'] , 2).'</td>
						<td>'.date("d-m-Y", strtotime($row['timestamp'])).'</td>
						<td class="right">'.$row['quantity'].'</td>
						<td class="editable" id="row_'.$row['sno'].'"> <span style="text-transform:uppercase">';
						if ($row['mode_of_payment'] == 'bank_transfer') {
							 $html .= 'BANK TRANSFER';
						}
						else{
							 $html .= $row['mode_of_payment'];
						}
						//echo $row['mode_of_payment'];
						 $html .='</span></td>';
						if(strpos($row['storeid'], "room")===false){
							 $html .='<td>T-'.get_table($row['storeid']).'</td>';
						}
						else{
							$row['storeid'] = str_replace("room_", "", $row['storeid']);
							$sql="SELECT * FROM `room_master` where sno=".$row['storeid'];
							$room_details=mysqli_fetch_assoc(execute_query($sql));
							 $html .='<td>R-'.$room_details['room_name'].'</td>';
						}
						$qry="SELECT * FROM `invoice_sale_restaurant` WHERE invoice_no='".$row['invoice_no']."'";
						$res=execute_query($qry);
						$kotrow=mysqli_fetch_array($res);
						$sql_modified_bill="SELECT * FROM `invoice_sale_restaurant_2` WHERE `invoice_sale_id`='".$row['sno']."' AND `edition_time` IS NOT NULL";
						$result_modified_bill=execute_query($sql_modified_bill);
						$num_modified_bill=mysqli_num_rows($result_modified_bill);
						 $html .='<td>'.$kotrow['kot_no'].'</td>';
						 $html .='<td>'.$row['invoice_no'].'</td>
						
					</tr>';
					$tot_qty += $row['quantity'];
					$tot_amount += $row['grand_total'];
					$tot_invoice += $row['total_amount'];
					$tot_sgst += $row['tot_vat'];
					$tot_cgst += $row['tot_sat'];
					$tot_taxable += $row['taxable_amount'];
					
				}
				$html .='<tr style="background:'.$col.';border:1px solid black">
				<th>&nbsp;</th>
				<th colspan="3" style="color:black;font-size:18px;">Total</th>
				<th class="right" style="color:black;font-size:18px;">'.round($tot_taxable,2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($tot_sgst,2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($tot_cgst,2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($tot_invoice,2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($tot_discount,2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($tot_amount,2).'</th>
				<th>&nbsp;</th>
				<th class="right" style="color:black;font-size:18px;">'.$tot_qty.'</th>
				<th colspan="4">&nbsp;</th>
			</tr>';
			$html .='<tr style="background:'.$col.';border:1px solid black">
				<th>&nbsp;</th>
				<th colspan="3" style="color:black;font-size:18px;">Grand Total</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['taxable_amount'],2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['tot_vat'],2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['tot_sat'],2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['total_amount'],2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['total_discount'],2).'</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['grand_total'],2).'</th>
				<th>&nbsp;</th>
				<th class="right" style="color:black;font-size:18px;">'.round($row_sum['quantity'],2).'</th>
				<th colspan="4">&nbsp;</th>
			</tr>';
	
				$html .='</table>';
				$html .='<table>
					<tr style="background:#333; color:#FFF; text-align:center; font-size:13px;">
						<th>S.No.</th>
						<th>Mode of Payment</th>
						<th>Count</th>
						<th>Amount</th>
					</tr>';
					$result2 = execute_query($sql_summary);
					$i=1;
					$total = 0;
					if(mysqli_num_rows($result2) != 0){
					while($row1 = mysqli_fetch_assoc($result2)){
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
						<td style="color:black;font-size:18px;">'.$i++.'</td>
						<td style="color:black;font-size:18px;"> <span style="text-transform:uppercase">';
						if ($row1['mode_of_payment'] == 'bank_transfer') {
							$html .='BANK TRANSFER';
						}
						else{
							$html .=$row1['mode_of_payment'];
						}
						//echo $row1['mode_of_payment'];
						$html .='</span></td>
						<td style="color:black;font-size:18px;">'.$row1['count'].'</td>
						<td class="right" style="color:black;font-size:18px;">'.round($row1['grand_total'] , 2).'</td>
						</tr>';
						$total+=$row1['grand_total'];
					}
				}
				$html .='<tr style="background:'.$col.';border:1px solid black">
							<th colspan="2">&nbsp;</th>
							<th class="right" style="color:black;font-size:18px;">Total : </th>
							<th style="color:black;font-size:18px;">'.$total.'</th>
						</tr>';
				$html .='</table>';
				
				header("Content-Type:application/xls");
                header("Content-Disposition:attachment;filename=download.xls");
                echo $html; ?>
	