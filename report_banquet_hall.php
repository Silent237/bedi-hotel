<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
$tab=1;
$msg='';
$response=1;
page_header();
navigation('');
//echo $sql;
if(isset($_GET['detail'])){
	$response = 2;
}
if(isset($_GET['del'])){
	$sql = 'delete from banquet_hall where sno="'.$_GET['del'].'"';
	execute_query($sql);
	$sql = 'delete from banquet_particular where banquet_id="'.$_GET['del'].'"';
	execute_query($sql);
	if(mysqli_error($db)){
		$msg = 'Error # '.mysqli_error($db);
	}
	else{
		$msg = 'Deleted';
	}
}
?>
<script language="javascript" type="text/javascript">
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name1",request, response);
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
</script>
    <div id="container">
        <h2>Banquet Hall Report</h2>
        <div class="no-print" style="text-align: right;"><input type="button" id="btnPrint" onclick="window.print();" value="Print Page" /></div>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form id="purchase_report" name="purchase_report" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">	
        	<table width="100%">
            	<tr style="background:#CCC;">
                    <th>Date Type</th>
                    <td>
                        <select name="date_type" id="date_type">
                            <option value="">-SELECT ANY ONE-</option>
                            <option value="event" <?php if(isset($_POST['date_type'])){if($_POST['date_type'] == 'event'){echo 'selected';}} ?>>Event Date</option>
                            <option value="booking" <?php if(isset($_POST['date_type'])){if($_POST['date_type'] == 'booking'){echo 'selected';}} ?>>Booking Date</option>
                        </select>
                    </td>
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('date_from', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_POST['submit'])){echo $_POST['date_from'];}else{echo date("Y-m-d");}?>', 1));
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('date_to', 'purchase_report', false, 'YYYY-MM-DD', '<?php if(isset($_POST['submit'])){echo $_POST['date_to'];}else{echo date("Y-m-d");}?>', 4));
                    </script>
                    </span>
                    </td>
                </tr>
            	<tr>
                    <th>Customer Name</th>
                    <td>
                    <input id="supplier" name="supplier" class="fieldtextmedium" maxlength="255" tabindex="7" type="text" value="<?php if(isset($_POST['submit'])){echo $_POST['supplier'];}?>">
                    <input id="supplier_sno" name="supplier_sno" type="hidden" value="<?php if(isset($_POST['submit'])){echo $_POST['supplier_sno'];}?>">
                    </td>
                	<th>Invoice Number</th>
                    	<td>
                        <input name="invoice_no" type="text" value="<?php if(isset($_POST['submit'])){echo $_POST['invoice_no'];}?>"  class="fieldtextmedium" tabindex="14" id="invoice_no"/>
                   		</td>
                	<th>Mode of Payment</th>
                	<td>
	                    <select id="mop" name="mop" class="field select addr" tabindex="15">
	                    	<option  value="">All Invoices</option>
	                    	<option value="cash">CASH</option>
	                    	<option value="credit">CREDIT</option>
	                    	<option value="card">CARD</option>
	                    </select>
               		</td>
                </tr>
                <tr>
                	<th colspan="3">
                    	<input type="submit" name="submit" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th>
                    	<a href="report_banquet_hall.php" class="btn btn-primary">Reset Filters</a>
                    </th>
					<th>
						
					</th>
                </tr>
            </table>
		</form>
<?php
	switch($response){
		case $response==1:{
?>
	<table width="100%">
		<tr  class="no-print">
					<td colspan="16" float="left" class="no-print text-center"  >
					<a href="banquet_hall_report_export.php"><input type="button" style=" background-color: #f44f4f; color:white; width:200px;" name="student_ledger" class="form-control btn btn-danger"  style="float: left;" value="Download In Excel"></a></span>
					</td>
				</tr>
    	<tr>
    		<th>S.No.</th>
    		<th>Customer Name</th>
    		<th>Company Name</th>
    		<th>Invoice No</th>
    		<th>Hall Type</th>
            <th>Date</th>
    		<th>Event Date</th>
    		<th>Mode Of Payment</th>
    		<th>Amount</th>
    		<th>SGST</th>
    		<th>CGST</th>
    		<th>Grand Total</th>
    		<th></th>
    		<th></th>
            <th></th>
            <th></th>
    	</tr>
    	<?php 
    		$i = 1;
    		$sql = 'SELECT * FROM `banquet_hall` WHERE 1=1 ';
    		if(isset($_POST['submit'])){
                $_POST['date_to'] = date('Y-m-d H:i' , strtotime($_POST['date_to'])+86400);
    			if($_POST['supplier_sno'] != ''){
    				$sql .= ' AND `cust_id`="'.$_POST['supplier_sno'].'" ';
    			}
    			if($_POST['invoice_no'] != ''){
    				$sql .= ' AND `invoice_no`="'.$_POST['invoice_no'].'" ';
    			}
    			if($_POST['mop'] != ''){
    				$sql .= ' AND `mop`="'.$_POST['mop'].'" ';
    			}
                if ($_POST['date_type'] != '') {
                    if ($_POST['date_type'] == 'event') {
                        $sql .= ' AND `check_in_date`>="'.$_POST['date_from'].'" AND `check_in_date`<="'.$_POST['date_to'].'"';
                    }
                    if ($_POST['date_type'] == 'booking') {
                        $sql .= ' AND `booking_date`>="'.$_POST['date_from'].'" AND `booking_date`<="'.$_POST['date_to'].'"';
                    }
                }
                else{
                    $sql .= ' AND `created_on`>"'.date('Y-m-d').'"';
                }
    		}
            else{
                $sql .= ' AND `created_on`>"'.date('Y-m-d').'"';
            }
    		//echo $sql;
			
			$_SESSION['sql5']= $sql;
			//echo $_SESSION['sql5'];
    		$result = execute_query($sql);
            $amount = 0;
            $cgst = 0;
            $sgst = 0;
            $grand_total = 0;
    		while($row = mysqli_fetch_array($result)){
    			$sql_customer = 'SELECT * FROM `customer` WHERE `sno`="'.$row['cust_id'].'"';
    			$result_customer = execute_query($sql_customer);
    			$row_customer = mysqli_fetch_array($result_customer);
    			?>
    	<tr>
    		<th><?php echo $i++; ?></th>
    		<td><?php echo $row_customer['cust_name']; ?></td>
    		<td><?php echo $row_customer['company_name']; ?></td>
    		<td><?php echo $row['invoice_no']; ?></td>
    		<td><?php echo $row['hall_type']; ?></td>
            <td><?php echo $row['booking_date']; ?></td>
    		<td><?php echo $row['check_in_date']; ?></td>
    		<td><?php echo $row['mop']; ?></td>
    		<td><?php echo $row['amount']; ?></td>
    		<td><?php echo $row['sgst']; ?></td>
    		<td><?php echo $row['cgst']; ?></td>
    		<td><?php echo $row['grand_total']; ?></td>
    		<td><a href="report_banquet_hall.php?detail=<?php echo $row['sno']; ?>">Detail</a></td>
    		<td><a href="print_ban.php?id=<?php echo $row['sno']; ?>" target="_blank">Print</a></td>
            <td><a href="banquet_hall.php?e_id=<?php echo $row['sno']; ?>" target="_blank">Edit</a></td>
            <td><a href="report_banquet_hall.php?del=<?php echo $row['sno']; ?>" onClick="return confirm(\'Are you sure?\');">Delete</a></td>
    	</tr>
    			<?php
                $amount += $row['amount'];
                $sgst += $row['sgst'];
                $cgst += $row['cgst'];
                $grand_total += $row['grand_total']; 
    		}
    	?>
        <tr>
            <th colspan="8">Total:</th>
            <th><?php echo round($amount , 3); ?></th>
            <th><?php echo round($sgst , 3); ?></th>
            <th><?php echo round($cgst , 3); ?></th>
            <th><?php echo round($grand_total , 3); ?></th>
            <th colspan="3">&nbsp;</th>
        </tr>
    </table>
<?php
			break;
		}
		case $response==2:{
		?>
	<table width="100%">
		<tr>
			<th>S.No.</th>
			<th>Particular</th>
			<th>Rate</th>
			<th>Quantity</th>
			<th>Amount</th>
			<th>SGST</th>
			<th>CGST</th>
			<th>Grand Total</th>
		</tr>
		<?php 
    		$i = 1;
    		$sql = 'SELECT * FROM `banquet_particular` WHERE `banquet_id`="'.$_GET['detail'].'"';
    		//echo $sql;
    		$result = execute_query($sql);
    		while($row = mysqli_fetch_array($result)){
    			?>
    	<tr>
    		<th><?php echo $i++; ?></th>
    		<td><?php echo $row['particular']; ?></td>
    		<td><?php echo $row['rate']; ?></td>
    		<td><?php echo $row['quantity']; ?></td>
    		<td><?php echo $row['amount']; ?></td>
    		<td><?php echo $row['sgst']; ?></td>
    		<td><?php echo $row['cgst']; ?></td>
    		<td><?php echo $row['grand_total']; ?></td>
    	</tr>
    			<?php
    		}
    	?>
	</table>
		<?php
			break;
		}
	}
?>          
	</div>
</div>
<?php

page_footer();
?>