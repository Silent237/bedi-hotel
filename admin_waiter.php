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

// Delete operation
if (isset($_GET['del'])) {
    $sql_delete = 'DELETE FROM `admin_waiter` WHERE `sno`="'.$_GET['del'].'"';
    $res=execute_query($sql_delete);
    $msg = $res ? 'Data Deleted' : '<li>Error: '.mysqli_error($db).'</li>';
}

// Fetch edit data
if (isset($_GET['edit_id'])) {
    $sql_edit = 'SELECT * FROM `admin_waiter` WHERE `sno`="'.$_GET['edit_id'].'"';
    $row_edit = mysqli_fetch_array(execute_query($sql_edit));
}

// Insert or update
if(isset($_POST['submit'])){
    $id_proof_file = '';
    if(isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] == 0){
        $filename = basename($_FILES['id_proof']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if(in_array(strtolower($ext), $allowed)){
            $upload_dir = "uploads/id_proofs/";
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $id_proof_file = $upload_dir . time() . "_" . $filename;
            move_uploaded_file($_FILES['id_proof']['tmp_name'], $id_proof_file);
        } else {
            $msg = '<li>Error: Only JPG, PNG, or PDF allowed.</li>';
        }
    } else if(isset($_POST['edit_sno'])) {
        $id_proof_file = $row_edit['id_proof'];
    }

    if($_POST['edit_sno'] != ''){
        $sql_update = 'UPDATE `admin_waiter` SET
                        `name`="'.$_POST['name'].'",
                        `f_name`="'.$_POST['f_name'].'",
                        `mobile_no`="'.$_POST['mob'].'",
                        `email_id`="'.$_POST['email'].'",
                        `gender`="'.$_POST['gender'].'",
                        `designation_id`="'.$_POST['desg'].'",
                        `address`="'.$_POST['address'].'",
                        `id_proof`="'.$id_proof_file.'"
                        WHERE `sno`="'.$_POST['edit_sno'].'"';
        $res=execute_query($sql_update);
        $msg = $res ? 'Data Updated' : '<li>Error: '.mysqli_error($db).'</li>';
    } else {
        $sql_insert='INSERT INTO `admin_waiter`(`name`, `f_name`, `mobile_no`, `email_id`, `gender`, `designation_id`, `address`, `id_proof`)
                     VALUES ("'.$_POST['name'].'", "'.$_POST['f_name'].'", "'.$_POST['mob'].'", "'.$_POST['email'].'", "'.$_POST['gender'].'", "'.$_POST['desg'].'", "'.$_POST['address'].'", "'.$id_proof_file.'")';
        $res=execute_query($sql_insert);
        $msg = $res ? 'Data Saved' : '<li>Error: '.mysqli_error($db).'</li>';
    }
}
?>

<div id="container">
    <h2>Add Staff</h2>
    <?php echo $msg; $tab=1; ?>
    <form action="admin_waiter.php" class="wufoo leftLabel page1" name="addnewdesignation" enctype="multipart/form-data" method="post">
        <table>
            <tr>
                <td>Name :</td>
                <td><input id="name" name="name" class="field text medium" value="<?php if(isset($row_edit)){echo $row_edit['name'];} ?>" tabindex="<?php echo $tab++; ?>" type="text" /></td>
                <td>Father Name :</td>
                <td><input id="f_name" name="f_name" class="field text medium" value="<?php if(isset($row_edit)){echo $row_edit['f_name'];} ?>" tabindex="<?php echo $tab++; ?>" type="text" /></td>
            </tr>
            <tr>
                <td>Mobile No :</td>
                <td><input id="mob" name="mob" class="field text medium" value="<?php if(isset($row_edit)){echo $row_edit['mobile_no'];} ?>" tabindex="<?php echo $tab++; ?>" type="text" /></td>
                <td>Email :</td>
                <td><input id="email" name="email" class="field text medium" value="<?php if(isset($row_edit)){echo $row_edit['email_id'];} ?>" tabindex="<?php echo $tab++; ?>" type="email" /></td>
            </tr>
            <tr>
                <td>Gender:</td>
                <td>
                    <select id="gender" name="gender" class="field text medium" tabindex="<?php echo $tab++; ?>">
                        <option value="">-- Select Gender --</option>
                        <option value="Male" <?php if(isset($row_edit) && $row_edit['gender']=='Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if(isset($row_edit) && $row_edit['gender']=='Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if(isset($row_edit) && $row_edit['gender']=='Other') echo 'selected'; ?>>Other</option>
                    </select>
                </td>
                <td>Designation:</td>
                <td>
                    <select id="desg" name="desg" class="field text medium" tabindex="<?php echo $tab++; ?>">
                        <option value="">-- Select Designation --</option>
                        <?php
                        $sql = "SELECT * FROM designation";
                        $res = execute_query($sql);
                        while ($desg = mysqli_fetch_array($res)) {
                            $selected = (isset($row_edit) && $row_edit['designation_id'] == $desg['sno']) ? 'selected' : '';
                            echo '<option value="'.$desg['sno'].'" '.$selected.'>'.$desg['description'].'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Address :</td>
                <td><input id="address" name="address" class="field text medium" value="<?php if(isset($row_edit)){echo $row_edit['address'];} ?>" tabindex="<?php echo $tab++; ?>" type="text" /></td>
                <td>Upload ID Proof :</td>
                <td><input id="id_proof" name="id_proof" type="file" class="field text medium" tabindex="<?php echo $tab++; ?>"></td>
            </tr>
            <tr>
                <td colspan="4">
                    <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['edit_id'])){echo $_GET['edit_id'];} ?>">
                    <input type="submit" class="large" name="submit" value="Submit">
                </td>
            </tr>
        </table>
    </form>

    <table width="100%">
        <tr style="background:#000; color:#FFF;">
            <th>Sno</th>
            <th>Name</th>
            <th>F Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Designation</th>
            <th>Address</th>
            <th>ID Proof</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php
        $sql="SELECT * FROM `admin_waiter`";
        $res=execute_query($sql);
        $sno=1;
        while($row=mysqli_fetch_array($res)){
            $bg_color = $sno % 2 == 0 ? '#EEE' : '#CCC';
            echo '<tr style="background:' . $bg_color . ';">
                    <td>'.$sno++.'</td>
                    <td>'.$row['name'].'</td>
                    <td>'.$row['f_name'].'</td>
                    <td>'.$row['mobile_no'].'</td>
                    <td>'.$row['email_id'].'</td>
                    <td>'.$row['gender'].'</td>
                    <td>'.get_designation_name($row['designation_id']).'</td>
                    <td>'.$row['address'].'</td>
                    <td><a href="'.$row['id_proof'].'" target="_blank"><i class="fas fa-eye"></i></a></td>
                    <td><a href="admin_waiter.php?edit_id='.$row['sno'].'"><i class="fas fa-edit"></i></a></td>
                    <td><a href="admin_waiter.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>
                  </tr>';
        }
        ?>
    </table>
</div>

<?php
function get_designation_name($id){
    $q = execute_query("SELECT description FROM designation WHERE sno='$id'");
    $r = mysqli_fetch_array($q);
    return $r['description'];
}
?>
