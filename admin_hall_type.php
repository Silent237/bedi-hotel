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
$errorMessage='';
if(isset($_POST['submit'])){
    if($_POST['edit'] !=''){
        $id=$_POST['edit'];
        $hall_type=$_POST['hall_type'];
        $sql="UPDATE `admin_hall_type` SET `hall_type`='$hall_type' WHERE sno='$id'";
        $res=execute_query($sql);
        if($res){
            $msg="Data Updated";
        }
        else{
            $msg="Error In Updation";
        }
    }
    else{
         $hall_type=$_POST['hall_type'];
        $sql="INSERT INTO `admin_hall_type`(`hall_type` ) VALUES ('$hall_type')";
        $res=execute_query($sql);
        if($res){
            $msg='Data Saved';
        }
        else{
            $msg="Error in Data Saving";
        }
    }
   
}

if(isset($_GET['edit_id'])){
    $id=$_GET['edit_id'];
    $sql="SELECT * FROM admin_hall_type WHERE sno='$id'";
    $res=execute_query($sql);
    $edit_hall_type=mysqli_fetch_array($res);
}
if(isset($_GET['del'])){
    $id=$_GET['del'];
    $sql="DELETE FROM `admin_hall_type` WHERE sno='$id'";
    $res=execute_query($sql);
    if($res){
        $msg="Data Deleted !";

    }
    else{
        $msg="Error in Deletion !";
    }
}
?>

<div id="container">
    <h2>Add PLANS</h2>	
	<?php echo $msg; $tab=1; ?>	
    <form action="admin_hall_type.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
        <table>
         	<tr>
         		<td>Hall Type : </td>
         		<td><input type="text" name="hall_type" value="<?php if(isset($_GET['edit_id'])){ echo $edit_hall_type['hall_type']; }?>"></td>
                <td><input type="submit" name="submit" class="large" value="Save"></td>
                <input type="hidden" name="edit" value="<?php if(isset($_GET['edit_id'])){ echo $_GET['edit_id'];}?>">
         	</tr>	

        </table>
    </form>
    <table>
        <tr>
            <th>Sno</th>
            <th>Hall Type</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php
        $sno=1;
            $sql="select * from admin_hall_type";
            $res=execute_query($sql);
            while($row=mysqli_fetch_array($res)){
                $bg_color = $sno % 2 == 0 ? '#EEE' : '#CCC';
                echo'<tr style="background:' . $bg_color . ';">
                        <td>'.$sno++.'</td>
                        <td>'.$row['hall_type'].'</td>
                        <td><a href="admin_hall_type.php?edit_id='.$row['sno'].'">Edit<a></td>
                        <td><a href="admin_hall_type.php?del='.$row['sno'].'">Delete<a></td>
                    </tr>';
            }
        ?>
    </table>
</div>
<?php
navigation('');
page_footer();
?>