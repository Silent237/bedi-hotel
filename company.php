<?php
include("scripts/settings.php");$msg='';

if(isset($_POST['submit'])){
	if($_POST['edit_sno']!=''){
		$sql = 'update company set description="'.$_POST['description'].'", address="'.$_POST['address'].'",mobile="'.$_POST['mobile'].'", person="'.$_POST['person'].'" where  sno="'.$_POST['edit_sno'].'"';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Company Edited Sucessfully</li>';
		}
	}
	else{
		$sql = 'insert into company(description, address, person, mobile) values("'.$_POST['description'].'", "'.$_POST['address'].'", "'.$_POST['person'].'", "'.$_POST['mobile'].'")';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Company Added Sucessfully</li>';
		}
	}
}

if(isset($_GET['id'])){
	$sql='select * from company where sno='.$_GET['id'];
	$stock = mysqli_fetch_array(execute_query($sql));
}


if(isset($_GET['delid'])){
	$sql = 'select * from stock_available where company='.$_GET['delid'];
	$result = execute_query($sql);
	if(mysqli_num_rows($result)!=0){
		$msg .= '<li>Other data exist. Can not delete this entry.</li>';
	}
	else{
		$sql = 'delete from company where sno='.$_GET['delid'];
		execute_query($sql);
	}
}
page_header();
?>
    <div id="container">
    <h2>COMPANIES</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
        <form id="add_product" name="add_product" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table>
        	<tr>
            	<td>Company Name</td>
                <td><input id="description" name="description"  tabindex="2" type="text" value="<?php if(isset($_GET['id'])){echo $stock['description'];}?>"/></td>
            	<td>Address</td>
                <td><input id="address" name="address"  tabindex="3" type="text" value="<?php if(isset($_GET['id'])){echo $stock['address'];}?>"/></td>
                <td rowspan="2"><input id="save" name="submit" class="submit" type="submit" value="Add/Edit" tabindex="91">
                <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $stock['sno'];}?>" />
                </td>
			</tr>
            <tr>
            	<td>Telephone Number</td>
                <td><input id="mobile" name="mobile"  tabindex="4" type="text" value="<?php if(isset($_GET['id'])){echo $stock['mobile'];}?>"/></td>
            	<td>Contact Person</td>
                <td><input id="person" name="person"  tabindex="5" type="text" value="<?php if(isset($_GET['id'])){echo $stock['person'];}?>"/></td>
            </tr>
		</table>
		<table>
        	<tr>
            	<th>S.No.</th>
                <th>Company Name</th>
                <th>Address</th>
                <th>Telephone No.</th>
                <th>Contact Person</th>
                <th>Edit</th>
                <th>Delete</th>
			</tr>
            <?php
            $i=1;
            $sql = 'select * from  company';
            $group = execute_query($sql);
            while($row = mysqli_fetch_array($group)){
	            echo '<tr>
				<td>'.$i++.'</td>
				<td>'.$row['description'].'</td>
				<td>'.$row['address'].'</td>
				<td>'.$row['mobile'].'</td>
				<td>'.$row['person'].'</td>
				<td><a href="company.php?id='.$row['sno'].'">Edit</a></td>
				<td><a href="company.php?delid='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
				</tr>';           
            }
            ?>
	    </table>
    	</form>
        </div>
    </div>
<?php page_footer();?>
    </body>
    </html>