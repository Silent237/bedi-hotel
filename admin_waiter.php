<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
navigation('');
page_footer();
$errorMessage='';
if (isset($_GET['del'])) {
    $sql_delete = 'DELETE FROM `admin_waiter` WHERE `sno`="'.$_GET['del'].'"';
    $res=execute_query($sql_delete);
    if($res){
        $msg='Data Deleted';
    }
    else{
        $msg = '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql_delete.'</li>';
    }

}
if (isset($_GET['edit_id'])) {
    $sql_edit = 'SELECT * FROM `admin_waiter` WHERE `sno`="'.$_GET['edit_id'].'"';
    $row_edit = mysqli_fetch_array(execute_query($sql_edit));
}
if(isset($_POST['submit'])){
    if($_POST['edit_sno'] != ''){

        $sql_update = 'UPDATE `admin_waiter` SET
                        `name`="'.$_POST['name'].'",
                        `f_name`="'.$_POST['f_name'].'",
                        `address`="'.$_POST['address'].'",
                        `id_proof`="'.$_POST['id_proof'].'"
                        WHERE `sno`="'.$_POST['edit_sno'].'"';
        $res=execute_query($sql_update);
        if($res){
            $msg='Data Updated';
        }
        else{
            $msg = '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql_update.'</li>';
        }

    }
    else{
        $sql_insert='INSERT INTO `admin_waiter`(`name`, `f_name`, `address`, `id_proof`) VALUES ("'.$_POST['name'].'","'.$_POST['f_name'].'","'.$_POST['address'].'","'.$_POST['id_proof'].'")';
        $res=execute_query($sql_insert);
        if($res){
            $msg='Data Saved';
        }
        else{
            $msg = '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql_insert.'</li>';
        }

    }
    
}

?>

<div id="container">
    <h2>Add Waiter</h2>	
	<?php echo $msg; $tab=1; ?>	
    <form action="admin_waiter.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
        <table>
         	<tr>
         		<td>Name : </td>
         		<td><input id="Name" name="name" class="field text medium" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['name'];} ?>" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
                <td>Father Name : </td>
                <td><input id="f_name" name="f_name" class="field text medium" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['f_name'];} ?>" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
         	</tr>
            <tr>
                <td>Address : </td>
                <td><input id="address" name="address" class="field text medium" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['address'];} ?>" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" /></td>
                <td>Id Proof : </td>
                <td><input id="id_proof" name="id_proof" class="field text medium" value="<?php if(isset($_GET['edit_id'])){echo $row_edit['id_proof'];} ?>" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" />
            </tr>
            <tr>
                <td>
                    <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['edit_id'])){echo $_GET['edit_id'];} ?>">
                    <input type="submit" class="large" name="submit">
                </td>
            </tr>	
        </table>
    </form>
    <table width="100%">
        <tr style="background:#000; color:#FFF;">
            <th>Sno</th>
            <th>Name</th>
            <th>F Name</th>
            <th>Address</th>
            <th>Id Proof</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php
            $sql="SELECT * FROM `admin_waiter`";
            $res=execute_query($sql);
            $sno=1;
            while($row=mysqli_fetch_array($res)){
                $bg_color = $sno % 2 == 0 ? '#EEE' : '#CCC';
                echo'<tr style="background:' . $bg_color . ';"><td>'.$sno++.'</td>
                        <td>'.$row['name'].'</td>
                        <td>'.$row['f_name'].'</td>
                        <td>'.$row['address'].'</td>
                        <td>'.$row['id_proof'].'</td>
                        <td><a href="admin_waiter.php?edit_id='.$row['sno'].'">Edit</a></td>
                        <td><a href="admin_waiter.php?del='.$row['sno'].'">Delete</a></td>
                    </tr>';
            }
        ?>
    </table>
</div>
