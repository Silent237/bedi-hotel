<?php
include("scripts/settings.php");
$msg='';
navigation('');
page_footer();
if(isset($_POST['submit'])){
	if($_POST['edit_sno']!=''){
		$sql = 'update new_type set `description`="'.$_POST['description'].'" WHERE sno ="'.$_POST['edit_sno'].'"';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Information Updated Sucessfully</li>';
		}
	}
	else{
		$sql = 'insert into new_type(description) values("'.$_POST['description'].'")';
		execute_query($sql);
		if(!mysqli_error($db)){
			$msg .= '<li>Type Added Sucessfully</li>';
		}
	}
}

if(isset($_GET['id'])){
	$sql='select * from new_type where sno='.$_GET['id'];
	$stock = mysqli_fetch_array(execute_query($sql));
}

if(isset($_GET['delid'])){
	$sql = 'delete from new_type where sno='.$_GET['delid'];
	execute_query($sql);
}
page_header();
?>
    <div id="container">
    <h2>GROUPS</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
        <form id="add_product" name="add_product" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">
        	<table>
            	<tr>
                	<td>Type Name : </td>
                    <td><input id="description" name="description"  class="field text medium" value="<?php if(isset($_GET['id'])){echo $stock['description'];}?>" tabindex="1" type="text"/></td>
                	<td><input id="save" name="submit" class="submit large" type="submit" value="Add/Edit" tabindex="2">
                    <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $stock['sno'];}?>" /></td>
                </tr>
			</table>
        <table>
        	<tr>
            	<th>S.No.</th>
                <th>Type</th>
                <th>Edit</th>
                <th>Delete</th>
			</tr>
            <?php
            $i=1;
            $sql = 'select * from  new_type';
            $group = execute_query($sql);
            while($row = mysqli_fetch_array($group)){
				$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
	            echo '<tr style="background:' . $bg_color . ';">
				<td>'.$i++.'</td>
				<td>'.$row['description'].'</td>
				<td><a href="groups.php?id='.$row['sno'].'"><i class="fas fa-edit"></i></a></td>
				<td><a href="groups.php?delid='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>
				</tr>';
            }
            ?>
            
    	</table>
    	</form>
		</div>
	</div>	    
