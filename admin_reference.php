<?php
include("scripts/settings.php");
$msg='';

if(isset($_POST['submit'])){
	if($_POST['edit_sno']!=''){
		$sql = 'update admin_reference set `name`="'.$_POST['name'].'" , `mobile`="'.$_POST['mobile'].'" , `edited_by`="'.$_SESSION['username'].'" , `edition_time`="'.date('Y-m-d h:i:s').'" WHERE sno ="'.$_POST['edit_sno'].'"';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Information Updated Sucessfully</li>';
		}
	}
	else{
		$sql = 'insert into admin_reference(`name` , `mobile` , `created_by` , `creation_time`) values("'.$_POST['name'].'" , "'.$_POST['mobile'].'" , "'.$_SESSION['username'].'" , "'.date('Y-m-d h:i:s').'")';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Information Added Sucessfully</li>';
		}
	}
}

if(isset($_GET['id'])){
	$sql='select * from admin_reference where sno='.$_GET['id'];
	$stock = mysqli_fetch_array(execute_query($sql));
}

if(isset($_GET['delid'])){
	$sql = 'delete from admin_reference where sno='.$_GET['delid'];
	execute_query($sql);
}
page_header();
?>
    <div id="container">
    <h2>References</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
        <form id="add_product" name="add_product" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">
        	<table>
            	<tr>
                	<td>Name : </td>
                    <td><input id="name" name="name"  class="field text medium" value="<?php if(isset($_GET['id'])){echo $stock['name'];}?>" tabindex="1" type="text"/></td>
                    <td>Mobile Number : </td>
                    <td><input id="mobile" name="mobile"  class="field text medium" value="<?php if(isset($_GET['id'])){echo $stock['mobile'];}?>" tabindex="2" type="text"/></td>
                	<td><input id="save" name="submit" class="submit large" type="submit" value="Add/Edit" tabindex="3">
                    <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $stock['sno'];}?>" /></td>
                </tr>
			</table>
        <table>
        	<tr>
            	<th>S.No.</th>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>Edit</th>
                <th>Delete</th>
			</tr>
            <?php
            $i=1;
            $sql = 'select * from  admin_reference';
            $group = execute_query($sql);
            while($row = mysqli_fetch_array($group)){
				$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
	            echo '<tr style="background:' . $bg_color . ';">
				<td>'.$i++.'</td>
				<td>'.$row['name'].'</td>
				<td>'.$row['mobile'].'</td>
				<td><a href="admin_reference.php?id='.$row['sno'].'">Edit</a></td>
				<td><a href="admin_reference.php?delid='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
				</tr>';
            }
            ?>
            
    	</table>
    	</form>
		</div>
	</div>	    
<?php 
navigation('');
page_footer();
?>