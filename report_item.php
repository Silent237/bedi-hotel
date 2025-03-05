<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
	logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
	logvalidate('admin');
$response=1;
$msg='';
if(!isset($_POST['part_type'])){
	$_POST['part_type'] = '';
}
if(!isset($_POST['item'])){
	$_POST['item'] = '';
}
//print_r($_POST);
page_header();
?>
<div id="container">
	<h2>Item Report</h2>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
        	<tr style="background:#CCC;">
            
            	<th>Date From</th>
                <td>
                <span>
                <script type="text/javascript" language="javascript">
				document.writeln(DateInput('from', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['from'])){echo $_POST['from'];}else{echo date("Y-m-d");}?>', 1))
                </script>
                </span>
                </td>
            	<th>Date To</th>
                <td>
                <span>
                <script type="text/javascript" language="javascript">
                document.writeln(DateInput('to', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['from'])){echo $_POST['to'];}else{echo date("Y-m-d");}?>', 4))
                </script>
                </span>
                </td>
               
            </tr>
            <tr>
				<td>Group WISE </td>
				<td>
                    <select name="part_type" id= "part_type" tabindex="<?php echo $tabindex++; ?>">
                    <option value=""></option>
                    <?php
                    $sql = 'select * from new_type ORDER BY description';
                    $result = execute_query($sql);
                    while($row = mysqli_fetch_array($result)){
						echo '<option value="'.$row['sno'].'"';
						if(isset($_GET['id'])){
							if($row['sno']==$product['part_type']){
								echo ' selected="selected"';
							}
						}
						else{
							if($row['sno']==$_POST['part_type']){
								echo 'selected="selected"';
							}
						}
						echo ' >'.$row['description'].'</option>';
                    }
                    ?>
                    </select>
						</td>
				<td>ITEM WISE </td>
				<td>
                    <select name="item" id="item" tabindex="<?php echo $tabindex++; ?>">
                    <option value=""></option>
                    <?php
                    $sql = 'select * from stock_available ORDER BY description';
                    $result = execute_query($sql);
                    while($row = mysqli_fetch_array($result)){
						echo '<option value="'.$row['sno'].'"';
						if(isset($_GET['id'])){
							if($row['sno']==$product['item']){
								echo ' selected="selected"';
							}
						}
						else{
							if($row['sno']==$_POST['item']){
								echo 'selected="selected"';
							}
						}
						echo ' >'.$row['description'].'</option>';
                    }
                    ?>
                    </select>
				</td>
				
            	<th colspan="5">
                   	<input type="submit" name="submit" value="Search with Filters" class="btTxt submit">
                </th>
            </tr>
        </table>
		<?php
		if(isset($_POST['submit'])){
			if($_POST['part_type']=='' && $_POST['item']==''){
				$sql = 'select * from new_type order by description';
				$result = execute_query($sql);
				echo '<table class="table table-border table-striped">
				<tr>
					<th>S.No.</th>
					<th>Group Name</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>';
				$i=1;
				while($row = mysqli_fetch_assoc($result)){
					$sql = 'select sum(amount) as amount, sum(qty) as quantity from stock_sale_restaurant left join stock_available on stock_available.sno = part_id where stock_available.type="'.$row['sno'].'" and part_dateofpurchase>="'.$_POST['from'].'" and part_dateofpurchase<="'.$_POST['to'].'"';
					$row_sum = mysqli_fetch_assoc(execute_query($sql));
					$quantity = $row_sum['quantity'];
					$amount = $row_sum['amount'];
					echo '<tr>
					<td>'.$i++.'</td>
					<td>'.$row['description'].'</td>
					<td>'.$quantity.'</td>
					<td>'.round($amount ?? 0, 2).'</td>
					</tr>';
				}
				echo '</table>';
		?>
		
		<?php
			}
			elseif($_POST['part_type']!='' && $_POST['item']==''){
		?>
        <table>
        	<tr>
	        	<th>Sno</th>
	        	<th>Item Name</th>
	        	<th>Qty</th>
	        	<th>Total Price </th>
	        </tr>
	        
	        	<?php
	        	$i=1;
	        	//$sql="SELECT sum(qty) as qty,part_id,sum(amount) as amount FROM `stock_sale_restaurant`";
				//echo $sql;
				
	        	//$sql="SELECT sum(`invoice_sale_restaurant`.`total_amount`) as tot_amt, sum(`invoice_sale_restaurant`.`tot_disc`) as disc, `stock_sale_restaurant`.`part_id`,sum(`stock_sale_restaurant`.`qty) as qty` FROM `invoice_sale_restaurant` LEFT JOIN `stock_sale_restaurant` ON `invoice_sale_restaurant`.`invoice_no`= `stock_sale_restaurant`.`invoice_no` ";

				//$result = execute_query($sql);
					$tqty=0;
					$tamt=0;
					$sql='select * from stock_available where type="'.$_POST['part_type'].'"';
					if($_POST['item']!=''){
						$sql .= ' and sno="'.$_POST['item'].'"';
					}
					//echo $sql;
					$result = execute_query($sql);
					while($row = mysqli_fetch_assoc($result)){
						if($_POST['item']!=''){
							
							
						}
						else{
							$sql = 'select sum(qty) as qty, part_id, sum(amount) as amount from stock_sale_restaurant where part_id="'.$row['sno'].'" and part_dateofpurchase>="'.$_POST['from'].'" and part_dateofpurchase<="'.$_POST['to'].'"';
							$res_trans = mysqli_fetch_assoc(execute_query($sql));
							$res_trans['amount'] = ($res_trans['amount']==''?0:$res_trans['amount']);
							$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC'; 
							echo'<tr style="background:' . $bg_color . ';">
								<td>'.$i++.'</td>
								<td>'.$row['description'].' ('.$row['sno'].')</td>
								<td>'.$res_trans['qty'].'</td>
								<td>'.round($res_trans['amount'],2).'</td>
							</tr>';
							$tqty+=$res_trans['qty'];
							$tamt+=$res_trans['amount'];
						}
                	}

                	$sql1="SELECT sum(tot_disc) as disc FROM `invoice_sale_restaurant` where dateofdispatch >='".$_POST['from']."' and dateofdispatch <= '".$_POST['to']."'";
                	//echo $sql1;
                	$res=execute_query($sql1);
                	$rowdisc=mysqli_fetch_array($res);

	        	?>
	        	<tr>
	        		<th cpolspan="2"></th>
	        		<th>Total-</th>
	        		<th><?php echo $tqty; ?></th>
	        		<th><?php echo $tamt; ?></th>
	        	</tr>
	        	<tr>
	        		<th></th>
	        		<th colspan="2">Total Discount</th>
	        		<th><?php echo $rowdisc['disc']; ?></th>
	        	</tr>
	        	<tr>
	        		<th></th>
	        		<th colspan="2">Grand Total</th>
	        		<th><?php echo $tamt-$rowdisc['disc']; ?></th>
	        	</tr>
			</table>
	        	<?php 
			} 
			elseif($_POST['part_type']!='' && $_POST['item']!=''){
				$sql = 'select * from stock_available where sno="'.$_POST['item'].'"';
				$item = mysqli_fetch_assoc(execute_query($sql));
				echo '<h2>'.$item['description'].'</h2>';
			?>
			<table>
        	<tr>
	        	<th>Sno</th>
	        	<th>Date</th>
	        	<th>Qty</th>
	        	<th>Total Price </th>
	        </tr>
			<?php
				$sql = 'select part_dateofpurchase, sum(qty) as qty, sum(amount) as amount from stock_sale_restaurant where part_id="'.$_POST['item'].'" and part_dateofpurchase>="'.$_POST['from'].'" and part_dateofpurchase<="'.$_POST['to'].'" group by part_dateofpurchase';
				$res_trans = execute_query($sql);
				$i=1;
				while($row_trans = mysqli_fetch_assoc($res_trans)){
					echo '<tr>
					<td>'.$i++.'</td>
					<td>'.$row_trans['part_dateofpurchase'].' '.$item['description'].'</td>
					<td>'.$row_trans['qty'].'</td>
					<td>'.round($row_trans['amount'],2).'</td>
					</tr>';
				}
				echo '</table>';
			}
			elseif($_POST['part_type']=='' && $_POST['item']!=''){
				$sql = 'select * from stock_available where sno="'.$_POST['item'].'"';
				$item = mysqli_fetch_assoc(execute_query($sql));
				echo '<h2>'.$item['description'].'</h2>';
			?>
			<table>
        	<tr>
	        	<th>Sno</th>
	        	<th>Date</th>
	        	<th>Qty</th>
	        	<th>Total Price </th>
	        </tr>
			<?php
				$sql = 'select part_dateofpurchase, sum(qty) as qty, sum(amount) as amount from stock_sale_restaurant where part_id="'.$_POST['item'].'" and part_dateofpurchase>="'.$_POST['from'].'" and part_dateofpurchase<="'.$_POST['to'].'" group by part_dateofpurchase';
				//echo $sql;
				$res_trans = execute_query($sql);
				$i=1;
				while($row_trans = mysqli_fetch_assoc($res_trans)){
					$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
					echo '<tr style="background:' . $bg_color . ';">
					<td>'.$i++.'</td>
					<td>'.$row_trans['part_dateofpurchase'].'</td>
					<td>'.$row_trans['qty'].'</td>
					<td>'.round($row_trans['amount'],2).'</td>
					</tr>';
				}
				echo '</table>';
			}
				
		}
		else{
			$sql = 'select sum(amount) as amount, sum(qty) as quantity, stock_available.description as description, part_dateofpurchase from stock_sale_restaurant left join stock_available on stock_available.sno = part_id group by part_dateofpurchase, part_id order by part_dateofpurchase';
			$result = execute_query($sql);
			$date = '';
			$i=1;
			echo '<table>';
			while($row = mysqli_fetch_assoc($result)){
				if($date!=$row['part_dateofpurchase']){
					echo '<tr><th colspan="4">'.$row['part_dateofpurchase'].'</th></tr>
					<tr>
	        	<th>Sno</th>
	        	<th>Item Name</th>
	        	<th>Qty</th>
	        	<th>Total Price </th>
	        </tr>';
					$date = $row['part_dateofpurchase'];
				}
				if($date==''){
					$date = $row['part_dateofpurchase'];
				}
				$quantity = $row['quantity'];
				$amount = $row['amount'];
				$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
				echo '<tr style="background:' . $bg_color . ';">
				<td>'.$i++.'</td>
				<td>'.$row['description'].'</td>
				<td>'.$quantity.'</td>
				<td>'.round($amount,2).'</td>
				</tr>';
			}
			echo '</table>';
		}
		?>
        
    </form>
</div>
<script>
/*$(document).ready(function(){
$("#part_type").change(function(){
		let selected_value = $("#part_type").val();
		console.log(selected_value);

		$.ajax({
			url: 'ajax_testing.php',
			method: 'GET',
			data : {selected_value: selected_value, id: <?php echo isset($_GET['edit'])? $_GET['edit']: '"test"' ?>},
			success: function(data){
				$("#item").html(data);
			}
		});
	 })
//$("#faculty_type").change(function(){
		//let selected_value = $("#faculty_type").val();
		//console.log(selected_value);

		
	// })
})
*/
</script>
<?php
navigation('');
page_footer();
?>