<?php
session_cache_limiter('nocache');
session_start();
error_reporting(0);

include ("scripts/settings.php");
	logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
	logvalidate('admin');
$response=1;
$msg='';
page_header();

if(isset($_GET['cancel'])){
	$sql = 'select * from allotment where sno='.$_GET['cancel'];
	$row = mysqli_fetch_array(execute_query($sql));
	if($row['cancel_date']==''){
		$sql = 'update allotment set cancel_date=CURRENT_TIMESTAMP where sno='.$_GET['cancel'];
		execute_query($sql);
	}
	else{
		$sql = 'update allotment set cancel_date=NULL where sno='.$_GET['cancel'];
		execute_query($sql);
		
	}
}

?>
 <div id="container">
	<h2>Kot Report</h2>	
	<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
	<form action="" class="wufoo leftLabel page1" id="report_allotment" name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%">
            	<tr style="background:#CCC;">
                <td>Check In Date</td>
                	<th>Date From</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
					document.writeln(DateInput('din_from', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1))
                    </script>
                    </span>
                    </td>
                	<th>Date To</th>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('din_to', 'report_allotment', true, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4))
                    </script>
                    </span>
                    </td>
                </tr>
                <tr>
                	<td>KOT No</td>
                    <th>
                    	<input type="text" name="number" >
                    </th>
                    <td >Room/Table</td>
                    <th>
                    	<select name="type" value="">
                    		<option value="">Select Any One</option>
                    		<option value="room">Room</option>
                    		<option value="table">Table</option>

                    	</select>
                    </th>
                </tr>
            	<tr class="no-print">
                	<th colspan="3">
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th colspan="3">
                  <input type="submit"  value="Reset Filters" class="btTxt submit" onclick="myFunction()">
                    </th>
                </tr>
            </table>
		<table>
			<tr>
				<th>S.No</th>
				<th>KOT No</th>
				<th>Type</th>
				<th>Amount</th>
				<th>Qty</th>
				<th>Table/Room No</th>
				<th>Date</th>
				<th>Print Action</th>
				
			</tr>
			<?php 
			$type= array();
			$menu='';
			$startdate=date("Y-m-1");
			$enddate=date("Y-m-30");
			$sql="SELECT * FROM `stock_sale_restaurant` where 1=1  ";
			$condition=array();
            if($_POST['din_to'] !="" && $_POST['din_from'] !="") {

                      $condition[] .= " and `part_dateofpurchase`>= '".$_POST['din_from']."' AND `part_dateofpurchase`<='".$_POST['din_to']."'";
               }
               
               if($_POST['number'] !="") {

                      $condition[] .= " and kot_no= '".$_POST['number']."'";
               }
               if($_POST['type'] == 'room') {

                      $condition[] .= " and table_id  LIKE  '%".$_POST['type']."%'";
               }
               if($_POST['type'] == 'table') {

                      $condition[] .= " and table_id NOT LIKE  '%room%'";
               }
               
              
            	$condition[] .= "group by  kot_no";
               
              $sqql=$sql; 
             if (count($condition) > 0) {
                   $sqql .=implode($condition);
              }
             //echo $sqql;
             $run=execute_query($sqql);
			$i=1;
			$tot=0;
			while($row=mysqli_fetch_array($run)){
				echo '<tr>
				<td>'.$i++.'</td>
				<td>'.$row['kot_no'].'</td>';
				$sql1="SELECT * FROM `stock_sale_restaurant` where kot_no='".$row['kot_no']."'";
				$run1=execute_query($sql1);
				
				echo'<td>';
				$qty=0;
				$tot=0;
				while($row1=mysqli_fetch_array($run1)){
				$name_item="SELECT * FROM `stock_available` where sno='".$row1['part_id']."'";
				$name_item1=execute_query($name_item);
				$name_item_row=mysqli_fetch_array($name_item1);
                   $tot +=$row1['amount'];
					echo $name_item_row['description']."(".$row1['qty'].") &nbsp;";
					$qty+=$row1['qty'];
				}
				echo'</td>';
				$tot += $tot*5/100;
			echo	'<td>'.$tot.'</td>
				<td>'.$qty.'</td>';
				if(strpos($row['table_id'], "room")===false){
				
				echo '<td>T-'.get_table($row['table_id']).'</td>';
			}
			else{
				$room_id = substr($row['table_id'], 5);
				$sql = 'select * from room_master where sno='.$room_id;
				$room = mysqli_fetch_array(execute_query($sql));
			
			echo	'<td>R-'.$room['room_name'].'</td>';

			}
			
				
			echo	'<td>'.date('d-m-Y',strtotime($row['part_dateofpurchase'])).'</td>

			<td><a href="kot_print.php?tid='.$row['table_id'].'&ts='.$row['part_dateofpurchase'].'&kot='.$row['kot_no'].'" target="_blank">Print</a></td>
				</tr>

				'

				;
			
			}


			 ?>
			 <script>
function myFunction() {
  var win=window.open('report_kot.php');
  window.close();

}

</script>
			
		
<?php
page_footer();
?>