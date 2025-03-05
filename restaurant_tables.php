 : <?php
include("scripts/settings.php");$msg='';

if(isset($_POST['submit'])){
	if($_POST['edit_sno']!=''){
		$sql = 'update res_table set table_number="'.$_POST['table_number'].'", capacity="'.$_POST['capacity'].'" where  sno="'.$_POST['edit_sno'].'"';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Table Edited Sucessfully</li>';
		}
	}
	else{
		$sql = 'insert into res_table (table_number, capacity, booked_status) values("'.$_POST['table_number'].'", "'.$_POST['capacity'].'", "0")';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Table Added Sucessfully</li>';
		}
		else{
			$msg .= '<li>Unable to Add. '.mysqli_error($db).' >> '.$sql.'</li>';
		}
	}
}

if(isset($_GET['id'])){
	$sql='select * from res_table where sno='.$_GET['id'];
	$table = mysqli_fetch_array(execute_query($sql));
}


if(isset($_GET['delid'])){
	$sql = 'select * from invoice_sale_restaurant where storeid='.$_GET['delid'];
	//echo $sql;
	$result = execute_query($sql);
	if(mysqli_num_rows($result)!=0){
		$msg .= '<li>Other data exist. Can not delete this entry.</li>';
	}
	else{
		$sql = 'delete from res_table where sno='.$_GET['delid'];
		execute_query($sql);
	}
}
page_header();
?>
    <div id="container">
    <h2>TABLES</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
        <form id="add_product" name="add_product" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table>
        	<tr>
            	<td>Table Number : </td>
                <td><input id="table_number" name="table_number"  tabindex="2" type="text" value="<?php if(isset($_GET['id'])){echo $table['table_number'];}?>"/></td>
            	<td>Capacity : </td>
                <td><input id="capacity" name="capacity"  tabindex="3" type="text" value="<?php if(isset($_GET['id'])){echo $table['capacity'];}?>"/></td>
                <td rowspan="2"><input id="save" name="submit" class="submit large" type="submit" value="Add/Edit" tabindex="91">
                <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $table['sno'];}?>" />
                </td>
			</tr>
		</table>
		<table>
        	<tr>
            	<th>S.No.</th>
                <th>Table Number</th>
                <th>Capacity</th>
                <th>Edit</th>
                <th>Delete</th>
			</tr>
            <?php
            $i=1;
            $sql = 'select * from  res_table';
            $group = execute_query($sql);
            while($row = mysqli_fetch_array($group)){
				$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
	            echo '<tr style="background:' . $bg_color . ';">
				<td>'.$i++.'</td>
				<td>'.$row['table_number'].'</td>
				<td>'.$row['capacity'].'</td>
				<td><a href="restaurant_tables.php?id='.$row['sno'].'">Edit</a></td>
				<td><a href="restaurant_tables.php?delid='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
				</tr>';           
            }
            ?>
	    </table>
    	</form>
        </div>
    </div>
<?php
navigation('');
page_footer();?>