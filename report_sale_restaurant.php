<?php
include ("scripts/settings.php");
$tab=1;
$msg='';
$response=1;
page_header();
if(isset($_POST['submit_form'])){
	foreach($_POST as $k=>$v){
		$_SESSION['sale_'.$k] = $v;
	}
}

if(isset($_POST['reset_form'])){
	foreach($_POST as $k=>$v){
		unset($_SESSION['sale_'.$k]);
	}
}
if(isset($_GET['del'])){
	$sql = 'delete from invoice_sale_restaurant where sno='.$_GET['del'];
	execute_query($sql);
	$sql = 'delete from stock_sale_restaurant where invoice_no='.$_GET['del'];
	execute_query($sql);
	$sql = 'delete from customer_transactions where number='.$_GET['del'].' and type="sale_restaurant"';
	execute_query($sql);
	$sql='delete from barcode_new where number='.$_GET['del'].' and type="sale"';
	execute_query($sql);
}
if(isset($_SESSION['sale_date_from'])){
	$sql = 'select invoice_sale_restaurant.sno, cust_name,company_name , concerned_person, department, id_2 as tin, taxable_amount, tot_vat, tot_sat, total_amount, tot_disc, other_discount, grand_total, timestamp, quantity, type, invoice_type, invoice_no, agent_id, mode_of_payment, storeid from invoice_sale_restaurant join customer on customer.sno = invoice_sale_restaurant.supplier_id where timestamp>="'.$_SESSION['sale_date_from'].'" and timestamp<="'.$_SESSION['sale_date_to'].'" and invoice_sale_restaurant.mode_of_payment !="nocharge"';
	//echo $sql;

	$sql_sum = 'select sum(taxable_amount) as taxable_amount, sum(tot_vat) as tot_vat, sum(tot_sat) as tot_sat, sum(total_amount) as total_amount, sum(grand_total) as grand_total, if(other_discount="", sum(tot_disc), sum(other_discount)) as total_discount, sum(quantity) as quantity from invoice_sale_restaurant left join customer on customer.sno = invoice_sale_restaurant.supplier_id where timestamp>="'.$_SESSION['sale_date_from'].'" and timestamp<="'.$_SESSION['sale_date_to'].'" and invoice_sale_restaurant.mode_of_payment !="nocharge"';
	//echo $sql_sum;
	$filter_summary = ' where timestamp>="'.$_SESSION['sale_date_from'].'" and timestamp<="'.$_SESSION['sale_date_to'].'"';
	if(isset($_SESSION['sale_supplier_sno'])){
		if($_SESSION['sale_supplier_sno']!=''){
			$sql .= ' and supplier_id='.$_SESSION['sale_supplier_sno'];
			$sql_sum .= ' and supplier_id='.$_SESSION['sale_supplier_sno'];
			$filter_summary .= ' and supplier_id='.$_SESSION['sale_supplier_sno'];
		}
		if($_SESSION['sale_quantity']!=''){
			$sql .= ' and quantity '.$_SESSION['sale_qty_symbol'].$_SESSION['sale_quantity'];
			$sql_sum .= ' and quantity '.$_SESSION['sale_qty_symbol'].$_SESSION['sale_quantity'];
			$filter_summary .= ' and quantity '.$_SESSION['sale_qty_symbol'].$_SESSION['sale_quantity'];
		}
		if($_SESSION['sale_amount']!=''){
			$sql .= ' and grand_total '.$_SESSION['sale_amount_symbol'].$_SESSION['sale_amount'];
			$sql_sum .= ' and grand_total '.$_SESSION['sale_amount_symbol'].$_SESSION['sale_amount'];
			$filter_summary .= ' and grand_total '.$_SESSION['sale_amount_symbol'].$_SESSION['sale_amount'];
		}
		if($_SESSION['sale_store_type']!=''){
			if($_SESSION['sale_store_type']=='room'){
				$sql .= ' and storeid like "room%" ';
				$sql_sum .= ' and storeid like "room%" ';
				$filter_summary .= ' and storeid like "room%" ';
				
			}
			else{
				$sql .= ' and storeid not like "room%" ';
				$sql_sum .= ' and storeid not like "room%" ';
				$filter_summary .= ' and storeid not like "room%" ';
				
			}
		}
		if($_SESSION['sale_invoice_type']!='all'){
			switch($_SESSION['sale_invoice_type']){
				case 'tax_gst':{
					$sql .= ' and id_2!=""';
					$sql_sum .= ' and id_2!=""';
					$filter_summary .= ' and id_2!=""';
					
					break;
				}
				case 'tax_wo_gst':{
					$sql .= ' and id_2=""';
					$sql_sum .= ' and id_2=""';
					$filter_summary .= ' and id_2=""';
					
					break;
				}
				default:{
					$sql .= ' and invoice_type="'.$_SESSION['sale_invoice_type'].'"';
					$sql_sum .= ' and invoice_type="'.$_SESSION['sale_invoice_type'].'"';
					$filter_summary .= ' and invoice_type="'.$_SESSION['sale_invoice_type'].'"';
					break;
				}
			}
		}
		if($_SESSION['sale_invoice_no']!=''){
			$sql .= ' and invoice_no='.$_SESSION['sale_invoice_no'];
			$sql_sum .= ' and invoice_no='.$_SESSION['sale_invoice_no'];
			$filter_summary .= ' and invoice_no='.$_SESSION['sale_invoice_no'];
		}
		if($_SESSION['sale_mop']!='all'){
			if($_SESSION['sale_mop'] != 'nocharge'){
				$sql .= ' and mode_of_payment="'.$_SESSION['sale_mop'].'"';
				$sql_sum .= ' and mode_of_payment="'.$_SESSION['sale_mop'].'"';
				$filter_summary .= ' and mode_of_payment="'.$_SESSION['sale_mop'].'"';
			}
			
		}
	}
	
	$sql .= ' order by timestamp desc, abs(substr(invoice_no,2)) desc';
	//echo $sql_sum.'<br/>';
	//echo $sql;
	$result_data = execute_query($sql);
	$row_sum = mysqli_fetch_array(execute_query($sql_sum, dbconnect()));
}
else{
	$filter_summary='';
	$sql = 'select invoice_sale_restaurant.sno, cust_name,company_name, id_2 as tin, concerned_person, department, taxable_amount, tot_vat, tot_sat, total_amount, tot_disc, other_discount, grand_total, timestamp, quantity, type, invoice_type, invoice_no, agent_id, mode_of_payment, storeid from invoice_sale_restaurant left join customer on customer.sno = invoice_sale_restaurant.supplier_id where timestamp>="'.date('Y-m-d').'" and timestamp<="'.date('Y-m-d').'" and invoice_sale_restaurant.mode_of_payment !="nocharge" order by timestamp desc, abs(substr(invoice_sale_restaurant.invoice_no , 2 )) desc';
	$result_data = execute_query($sql);	

	$sql_sum = 'select sum(taxable_amount) as taxable_amount, sum(tot_vat) as tot_vat, sum(tot_sat) as tot_sat, sum(total_amount) as total_amount, if(other_discount="", sum(tot_disc), sum(other_discount)) as total_discount, sum(grand_total) as grand_total, sum(quantity) as quantity from invoice_sale_restaurant left join customer on customer.sno = invoice_sale_restaurant.supplier_id and invoice_sale_restaurant.mode_of_payment !="nocharge" WHERE timestamp>="'.date('Y-m-d').'" and timestamp<="'.date('Y-m-d').'"';
	$row_sum = mysqli_fetch_array(execute_query($sql_sum, dbconnect()));
}
//echo $sql;
?>
<script language="javascript" type="text/javascript">
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='supplier']").val(ui.item.label);
			$('#supplier_sno').val(ui.item.id);
			$('#address1').val(ui.item.address);
			$('#address2').val(ui.item.address);
			$('#mob').val(ui.item.mobile);
			$('#balance').val(ui.item.balance);
			$('#last_balance').val(ui.item.balance);
			$('#tin').val(ui.item.tin);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#supplier").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});

function edit_mode_of_payment(id){
	//alert("#mode_of_payment_"+id);
	var mop = $("#mode_of_payment_"+id).val();
	$("#row_"+id).html('<img src="images/loading_transparent.gif">');
	$.ajax({
		async: false,
		url: "scripts/ajax.php?id=mop&term="+id+"&mop="+mop,
		dataType: "json"
	})
	.done(function(data) {
		data = data[0];
		if(data.result=='true'){
			alert("Updated");
			$("#row_"+id).html(mop);
		}
		else{
			alert("Failed. Retry.");
			var txt = '<select name="mode_of_payment" id="mode_of_payment_'+id+'" class="small"><option value="CASH" ';
			txt += '>CASH</option><option value="CARD" ';
			txt += '>CARD</option></select><br/><input type="button" value="Save" name="save_button" class="small" onClick="edit_mode_of_payment('+id+');">';
			$("#row_"+id).html(txt);
		}
	});

}	
</script>
    <div id="container">
        <div style="float:right; margin-right:50px;"><a href="report_nonchargeable.php"><img src="images/images.jpg" style="width:30px;">Non Chargable Report</a></div>
        <h2>Restaurant Report</h2>
        <div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form id="purchase_report" name="purchase_report" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">	
        	<table width="100%">
            	<tr style="background:#CCC;">
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('date_from', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['sale_date_from'])){echo $_SESSION['sale_date_from'];}else{echo date("Y-m-d");}?>', 1));
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('date_to', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_SESSION['sale_date_to'])){echo $_SESSION['sale_date_to'];}else{echo date("Y-m-d");}?>', 4));
                    </script>
                    </span>
                    </td>
                    <th>Room/Table</th>
                        <td>
                        <select name="store_type" id="store_type" tabindex="<?php echo $tab++; ?>">
    					<option value=""></option>
                        <option value="room">Rooms</option>
                        <option value="tables">Tables</option>
                        </select>
                     	</td>
                </tr>
            	<tr class="no-print">
                	<th>Customer Name</th>
                    <td>
                    <input id="supplier" name="supplier" class="fieldtextmedium" maxlength="255" tabindex="7" type="text" value="<?php if(isset($_SESSION['sale_supplier'])){echo $_SESSION['sale_supplier'];}?>">
                    <input id="supplier_sno" name="supplier_sno" type="hidden" value="<?php if(isset($_SESSION['sale_supplier_sno'])){echo $_SESSION['sale_supplier_sno'];}?>">
                    </td>
                	<th>Quantity</th>
                    	<td>
                    	<select name="qty_symbol" id="qty_symbol" tabindex="9">
                        	<option value="=" <?php if(isset($_SESSION['sale_qty_symbol'])){if($_SESSION['sale_qty_symbol']=='='){echo 'selected';}}?>>=</option>
                        	<option value=">=" <?php if(isset($_SESSION['sale_qty_symbol'])){if($_SESSION['sale_qty_symbol']=='>='){echo 'selected';}}?>>>=</option>
                        	<option value="<=" <?php if(isset($_SESSION['sale_qty_symbol'])){if($_SESSION['sale_qty_symbol']=='<='){echo 'selected';}}?>><=</option>
                        </select>
                    	<input id="quantity" name="quantity" class="fieldtextmedium" maxlength="255" tabindex="10" type="text" value="<?php if(isset($_SESSION['sale_quantity'])){echo $_SESSION['sale_quantity'];}?>">
                    	</td>
                	<th>Amount</th>
                   		<td>
                    	<select name="amount_symbol" id="amount_symbol" tabindex="11">
                        	<option value="=" <?php if(isset($_SESSION['sale_amount_symbol'])){if($_SESSION['sale_amount_symbol']=='='){echo 'selected';}}?>>=</option>
                        	<option value=">=" <?php if(isset($_SESSION['sale_amount_symbol'])){if($_SESSION['sale_amount_symbol']=='>='){echo 'selected';}}?>>>=</option>
                        	<option value="<=" <?php if(isset($_SESSION['sale_amount_symbol'])){if($_SESSION['sale_amount_symbol']=='<='){echo 'selected';}}?>><=</option>
                        </select>
                    	<input id="amount" name="amount" class="fieldtextmedium" maxlength="255" tabindex="12" type="text" value="<?php if(isset($_SESSION['sale_amount'])){echo $_SESSION['sale_amount'];}?>">
                    	</td>
                </tr>
            	<tr class="no-print" style="background:#CCC;">
                	<th>Invoice Type</th>
                    	<td>
                        <select id="invoice_type" name="invoice_type" class="field select addr" tabindex="13">
                        <option  value="all">All Invoices</option>
                        <option value="tax" <?php if(isset($_SESSION['sale_invoice_type'])){if($_SESSION['sale_invoice_type']=='tax'){echo 'selected';}}?>>Tax Invoice</option>
                        <option  value="tax_gst" <?php if(isset($_SESSION['sale_invoice_type'])){if($_SESSION['sale_invoice_type']=='tax_gst'){echo 'selected';}}?>>Tax Invoice with GSTIN</option>
                        <option  value="tax_wo_gst" <?php if(isset($_SESSION['sale_invoice_type'])){if($_SESSION['sale_invoice_type']=='tax_wo_gst'){echo 'selected';}}?>>Tax Invoice without GSTIN</option>
                        <option value="other" <?php if(isset($_SESSION['sale_invoice_type'])){if($_SESSION['sale_invoice_type']=='other'){echo 'selected';}}?>>Other</option>
                        </select>
                    	</td>
                	<th>Invoice Number</th>
                    	<td>
                        <input name="invoice_no" type="text" value="<?php if(isset($_SESSION['sale_invoice_no'])){echo $_SESSION['sale_invoice_no'];}?>"  class="fieldtextmedium" tabindex="14" id="invoice_no"/>
                   		</td>
                	<th>Mode of Payment</th>
                    	<td>
                        <select id="mop" name="mop" class="field select addr" tabindex="15">
                        <option  value="all">All Invoices</option>
                        <?php
						$sql='select * from invoice_sale_restaurant group by mode_of_payment';
						$result_sale = execute_query($sql);
						while($row_sale = mysqli_fetch_array($result_sale)){
							echo '<option  value="'.$row_sale['mode_of_payment'].'" ';
							if(isset($_SESSION['sale_mop'])){
								if($_SESSION['sale_mop']==$row_sale['mode_of_payment']){
									echo 'selected';
								}
							}
							echo '>'.$row_sale['mode_of_payment'].'</option>';	
						}
						?>
                        </select>
                   		</td>
                </tr>
                <tr class="no-print">
                	<th colspan="3">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th>
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
                    <th colspan="2">
                    	<?php
						if(isset($_SESSION['sale_agent'])){
							if($_SESSION['sale_agent']!=''){
								$sql='select * from agent_details where sno='.$_SESSION['sale_agent'];
								$name=mysqli_fetch_array(execute_query($sql));
								$comm = ($row_sum['grand_total']*$name['commission']/100);
								echo "Target : ".$name['target']." Commission : ".$comm;
								echo '&nbsp; <input type="submit" name="post_commission" value="Post Commission" class="btTxt submit"><input type="hidden" name="commission_amount" value="'.$comm.'">';
							}
						}
						?>
                    </th>
                </tr>
            </table>
		</form>
<?php
	switch($response){
		case $response==1:{
?>
	<table width="100%">
    	<thead>
    	<tr>
    		<th colspan="17">
			<?php
				include ('pagination/paginate.php'); //include of paginat page
				$total_results = mysqli_num_rows($result_data);
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
				echo '<div class="pagination"><ul>';
				if ($total_pages > 1) {
					echo paginate($reload, $show_page, $total_pages);
				}
				echo "</ul></div>";
			?>
			</th>
		</tr>
    	<tr>
        	<th>S.No.</th>
        	<th>Company Name</th>
            <th>Guest Name</th>
            <th>GSTIN</th>
            <th>Taxable<br />Amount</th>
            <th>Tax<br />Amount</th>
            <th>Invoice<br />Amount</th>
            <th>Discount</th>
            <th>Amount<br />Payable</th>
            <th>Sale Date</th>
            <th>Unit</th>
            <th>Mode of<br />Payment</th>
            <th>Table/Room</th>
            <th>Kot No.</th>
            <th>Invoice No.</th>
            <th colspan="5" class="no-print">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
		$i=1;
		$tot_qty=0;
		$tot_tax =0;
		$tot_taxable=0;
		$tot_amount=0;
		$tot_invoice=0;
		$tot_discount=0;
		for ($pgid = $start; $pgid < $end; $pgid++) {
			//print_r($row);
			if ($pgid == $total_results) {
				break;
			}
			mysqli_data_seek($result_data, $pgid);
			$row = mysqli_fetch_array($result_data);
			$i = $pgid+1;
			echo '
			<tr style="z-index:9999">
				<th>'.$i.'</th>
				<td>'.$row['company_name'].'</td>
				<td>'.$row['concerned_person'];
			/**	if($row['concerned_person']!=''){
					echo '<br /><small><b>'.$row['concerned_person'].'</b></small>';
				}**/
				if($row['department']!=''){
					echo '<br /><small><b>'.$row['department'].'</b></small>';
				}
				if($row['agent_id']!=''){
					echo '<br /><small>Agent : <b>'.get_agent_name($row['agent_id']).'</b></small>';
				}
				echo '</td>
				<td>'.$row['tin'].'</td>
				<td class="right">'.$row['taxable_amount'].'</td>
				<td class="right">'.($row['tot_vat']+$row['tot_sat']).'</td>
				<td class="right">'.$row['total_amount'].'</td>';
			
				if($row['tot_disc']==''){
					echo '<td class="right">'.$row['tot_disc'].'</td>';
					$tot_discount += ($row['tot_disc']);
				}
				else{
					echo '<td class="right">'.$row['other_discount'].'</td>';
					$tot_discount += ($row['other_discount']);
				}
				echo '
				<td class="right">'.$row['grand_total'].'</td>
				<td>'.date("d-m-Y", strtotime($row['timestamp'])).'</td>
				<td class="right">'.$row['quantity'].'</td>
				<td class="editable" id="row_'.$row['sno'].'"> <span style="text-transform:uppercase">'.$row['mode_of_payment'].'</span></td>';
				if(strpos($row['storeid'], "room")===false){
					echo '<td>T-'.get_table($row['storeid']).'</td>';
				}
				else{
					$row['storeid'] = str_replace("room_", "", $row['storeid']);
					$sql="SELECT * FROM `room_master` where sno=".$row['storeid'];
					$room_details=mysqli_fetch_assoc(execute_query($sql));
					echo '<td>R-'.$room_details['room_name'].'</td>';
				}
				$qry="SELECT * FROM `invoice_sale_restaurant` WHERE invoice_no='".$row['invoice_no']."'";
				$res=execute_query($qry);
				$kotrow=mysqli_fetch_array($res);
				echo'<td>'.$kotrow['kot_no'].'</td>';
				echo '<td>'.$row['invoice_no'].'</td>
				<td class="no-print"><a href="dine_in_order.php?edit_id='.$row['sno'].'" target="_blank"><i class="fas fa-edit"></i></a></td>
				<td class="no-print"><a href="scripts/printing_sale_restaurant.php?inv='.$row['sno'].'" target="_blank"><i class="fas fa-eye"></i></a></td>
				<td class="no-print"><a href="dine_in_order_copy.php?edit_id='.$row['sno'].'" target="_blank">Bill Edit</a></td>
				<td class="no-print"><a href="report_sale_restaurant.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>
			</tr>';
			$tot_qty += $row['quantity'];
			$tot_amount += $row['grand_total'];
			$tot_invoice += $row['total_amount'];
			$tot_tax += $row['tot_vat']+$row['tot_sat'];
			$tot_taxable += $row['taxable_amount'];
			
		}
		echo '<tr>
			<th>&nbsp;</th>
			<th colspan="3">Total</th>
			<th class="right">'.round($tot_taxable,2).'</th>
			<th class="right">'.round($tot_tax,2).'</th>
			<th class="right">'.round($tot_invoice,2).'</th>
			<th class="right">'.round($tot_discount,2).'</th>
			<th class="right">'.round($tot_amount,2).'</th>
			<th>&nbsp;</th>
			<th class="right">'.$tot_qty.'</th>
			<th colspan="8">&nbsp;</th>
		</tr>';
		echo '<tr>
			<th>&nbsp;</th>
			<th colspan="3">Grand Total</th>
			<th class="right">'.round($row_sum['taxable_amount'],2).'</th>
			<th class="right">'.round($row_sum['tot_vat']+$row_sum['tot_sat'],2).'</th>
			<th class="right">'.round($row_sum['total_amount'],2).'</th>
			<th class="right">'.round($row_sum['total_discount'],2).'</th>
			<th class="right">'.round($row_sum['grand_total'],2).'</th>
			<th>&nbsp;</th>
			<th class="right">'.round($row_sum['quantity'],2).'</th>
			<th colspan="8">&nbsp;</th>
		</tr>';
		?>
    	</tbody>
    </table>
    <table>
    	<tr>
    		<th>S.No.</th>
    		<th>Mode of Payment</th>
    		<th>Count</th>
    		<th>Amount</th>
    	</tr>
    	<?php
    	if(!isset($_POST['submit_form'])){
			$sql_summary = 'SELECT mode_of_payment, count(*) as count, sum(grand_total) as grand_total FROM `invoice_sale_restaurant` left join customer on customer.sno = invoice_sale_restaurant.supplier_id and mode_of_payment !="nocharge" WHERE timestamp>="'.date('Y-m-d').'" and timestamp<="'.date('Y-m-d').'" group by mode_of_payment';
		}
    	elseif(!isset($_POST['mop'])){
			$sql_summary = 'SELECT mode_of_payment, count(*) as count, sum(grand_total) as grand_total FROM `invoice_sale_restaurant` left join customer on customer.sno = invoice_sale_restaurant.supplier_id '.$filter_summary.' WHERE mode_of_payment !="nocharge" group by mode_of_payment';
		}
		else{
			$sql_summary = 'SELECT mode_of_payment, count(*) as count, sum(grand_total) as grand_total FROM `invoice_sale_restaurant` left join customer on customer.sno = invoice_sale_restaurant.supplier_id '.$filter_summary.' and mode_of_payment !="nocharge" group by mode_of_payment';
			//echo $sql_summary;
		}
		//echo $sql_summary;
		$result = execute_query($sql_summary);
		$i=1;
		$total = 0;
		if(mysqli_num_rows($result) != 0){
			while($row = mysqli_fetch_assoc($result)){
				echo '<tr>
				<td>'.$i++.'</td>
				<td> <span style="text-transform:uppercase">'.$row['mode_of_payment'].'</span></td>
				<td>'.$row['count'].'</td>
				<td class="right">'.$row['grand_total'].'</td>
				</tr>';
				$total+=$row['grand_total'];
			}
		}
		echo '<tr>
		<th colspan="2">&nbsp;</th>
		<th class="right">Total : </th>
		<th>'.$total.'</th>
		</tr>';
		
		
		?>
    </table>
<?php
			break;
		}
	}
?>          
	</div>
</div>
<script>
$(function () {
	$("td.editable").dblclick(function (e) {
		var currentEle = $(this);
		var id = $(this).attr('id');
		var value = $(this).html();
		id = id.replace("row_", "");
		var txt = '<select name="mode_of_payment" id="mode_of_payment_'+id+'" class="small"><option value="CASH" ';
		if(value=='CASH'){
			txt += ' selected="selected" ';
		}
		txt += '>CASH</option><option value="CARD" ';
		if(value=='CARD'){
			txt += ' selected="selected" ';
		}
		txt += '>CARD</option><option value="CREDIT" ';
		if(value=='CREDIT'){
			txt += ' selected="selected" ';
		}txt += '>CREDIT<option/></select><br /><input type="button" value="Save" name="save_button" class="small" onClick="edit_mode_of_payment('+id+');">';
		$(this).html(txt);
	});
});
</script>
<?php
page_footer();


?>