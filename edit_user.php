<?php
include("scripts/settings.php");
session_cache_limiter('nocache');
session_start();
page_header();
navigation('');
page_footer();
$msg='';
$response=1;
if(!isset($_REQUEST['cid'])){
	$_REQUEST['cid']=1;
}

if(isset($_GET['id'])){
	$response=2;
}
else{
	$response=1;
}

if(isset($_POST['submit'])){
	$sql ='select * from users where sno ="'.$_POST['user'].'"';
	$user = mysqli_fetch_array(execute_query($sql));
	
	if($_POST['user']!=1){
		$sql='delete from user_access where user_id="'.$user['sno'].'"';
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql;
		}
		$sql = 'select * from navigation';
		$result=execute_query($sql);
		while($nav=mysqli_fetch_array($result)){
			$check1='check_'.$nav['sno'];
			if(isset($_POST[$check1])){
				$sql='INSERT INTO `user_access`(`user_id`, `file_name`, `created_by`, `creation_time`) 
				VALUES("'.$user['sno'].'","'.$nav['sno'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'")';
				execute_query($sql);
			}
		}
	}
	
	$sql='update users set 
	userid = "'.$_POST['user_id'].'", 
	pwd ="'.$_POST['user_pass'].'", 
	user_name="'.$_POST['user_name'].'", 
	father_name="'.$_POST['father_name'].'", 
	address="'.$_POST['address'].'", 
	mobile="'.$_POST['mobile'].'" 
	where sno ="'.$user['sno'].'"';
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 4 : '.mysqli_error($db).' >> '.$sql;
	}

	if($msg==''){
		$msg .= '<li>Successful</li>';
	}
	else{
		$msg .= '<li>Please insert user detail correctly</li>';
	}
}

function customer($page,$info) {
	$page = ($page*30)-30;
	$i=$page+1;
	$link = dbconnect();  echo ' 
	<div id="comment-wrapper">
    <div id="comments"> ';

	$_SESSION['sql_result_filter'] = "select * from users order by sno desc limit ".$page.",30";
	$result = execute_query($_SESSION['sql_result_filter'],$link);
	$i=0;
	$tot=0;
	while($row_invoice = mysqli_fetch_array($result)) {
		if($i%2==0){
			$bg = "#FFFFFF";
		}
		else {
			$bg = "#CCCCCC";
		}
		echo '
		<tr style="background:'.$bg.'">
		<th>'.$row_invoice['sno'].'</th>
		<td>'.$row_invoice['userid'].'</td>
		<td>'.$row_invoice['user_name'].'</td>
		<td>'.$row_invoice['father_name'].'</td>
		<td>'.$row_invoice['address'].'</td>
		<td>'.$row_invoice['mobile'].'</td>
		<td><a href="edit_user.php?id='.$row_invoice['sno'].'">Edit</a></td>
		<td><a href="'.$_SERVER['PHP_SELF'].'?del='.$row_invoice['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
		</tr>';
		$i++;
	}
	mysqli_free_result($result);
	 echo '
	</div> </div>';
}

if(isset($_REQUEST['del'])){	
	$sql='select * from users where sno='.$_REQUEST['del'];
	$s_inv=mysqli_fetch_array(execute_query($sql));
	$sql = "delete from user_access_detail where user_id=".$_REQUEST['del'];
	execute_query($sql);
	$sql = "delete from user_stock_access where user_id=".$_REQUEST['del'];
	execute_query($sql);
    $sql = "delete from user_store_access where user_id=".$_REQUEST['del'];
	execute_query($sql);
	$sql = "delete from users where sno=".$_GET['del'];
	execute_query($sql);
	echo '<script>alert("Record deleted.")</script>';
}

switch($response) {
	case 1:{
?>
    <div id="container">
    <div style="float:right; margin-right:50px;"><a href="admin_users.php"><img src="images/supplier.jpg" style="width:30px;">Create New User</a></div>
    <h2>USERS</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
			<form action="edit_user.php" class="edit_user" name="edit_user" enctype="multipart/form-data" method="POST" onSubmit="">
	            <input type="hidden" name="inv" value="<?php echo $_GET['inv'] ?>" />
                <table border="0" style="margin-bottom:10px; width:100%">
					<tr>
						<th>Sno</th>
						<th>User ID</th>
						<th>User Name</th>
						<th>Father Name</th>
						<th>Address</th>
						<th>Mobile</th>
						<th>Edit</th>
						<th>Delete</th>
					</tr>
               		<?php
						$_SESSION['sql_result_filter'] = "select * from users order by sno";
						$result = execute_query($_SESSION['sql_result_filter']);
						$i=0;
						$tot=0;
						while($row_invoice = mysqli_fetch_array($result)) {
							if($i%2==0){
								$bg = "#FFFFFF";
							}
							else {
								$bg = "#CCCCCC";
							}
							echo '
							<tr style="background:'.$bg.'">
							<th>'.$row_invoice['sno'].'</th>
							<td>'.$row_invoice['userid'].'</td>
							<td>'.$row_invoice['user_name'].'</td>
							<td>'.$row_invoice['father_name'].'</td>
							<td>'.$row_invoice['address'].'</td>
							<td>'.$row_invoice['mobile'].'</td>';
							if($row_invoice['sno']!='1'){
								echo '<td><a href="edit_user.php?id='.$row_invoice['sno'].'">Edit</a></td>
								<td><a href="'.$_SERVER['PHP_SELF'].'?del='.$row_invoice['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>';
							}
							else{
								echo '<td><a href="edit_user.php?id='.$row_invoice['sno'].'">Edit</a></td><td>&nbsp;</td>';
							}
							echo '</tr>';
							$i++;
						}		
					?>
                </table>
			</form>
		</div>
<?php
     break;
  }
  case 2: {
	  $sql='select * from users where sno='.$_REQUEST['id'];
	  $sale=mysqli_fetch_array(execute_query($sql));
	  $sql='select * from user_access where user_id = "'.$sale['sno'].'"';
	  $user = mysqli_fetch_array(execute_query($sql));
	$tab=10;
	  
?>
    <div id="container">
    <div style="float:right; margin-right:50px;"><a href="create_user.php"><img src="images/supplier.jpg" style="width:30px;">Create New User</a></div>
    <h2>EDIT USER</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
			<form action="edit_user.php" class="edit_user" name="edit_user" enctype="multipart/form-data" method="POST" onSubmit="">
            	<table width="100%">
                	<tr>	
                    	<td>User ID</td>
                        <td><input id="user_id" name="user_id" value="<?php echo $sale['userid']; ?>" class="fieldtextmedium" maxlength="25" tabindex="1" type="text" <?php if($sale['sno']==1){echo 'readonly="readonly"';}?>/></td>
                    </tr>
                    <tr>
                    	<td>Password</td>
                        <td><input id="user_pass" name="user_pass" value="<?php echo $sale['pwd']; ?>" class="fieldtextmedium" maxlength="25" tabindex="2" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>User Name</td>
                        <td><input id="user_name" name="user_name" value="<?php echo $sale['user_name']; ?>" class="fieldtextmedium" maxlength="25" tabindex="3" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>Father Name</td>
                        <td><input id="father_name" name="father_name" value="<?php echo $sale['father_name']; ?>" class="fieldtextmedium" maxlength="25" tabindex="4" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>Address</td>
                        <td><input id="address" name="address" value="<?php echo $sale['address']; ?>" class="fieldtextmedium" maxlength="25" tabindex="5" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>Mobile</td>
                        <td><input id="mobile" name="mobile" value="<?php echo $sale['mobile']; ?>" class="fieldtextmedium" maxlength="25" tabindex="6" type="text"/></td>
                    </tr>
				</table>
  				<?php if($_GET['id']!=1){?>
   				<h2>User Accsses</h2>
                   	<table>
                    	<tr>
                        	<th>Module Access</th>
                        </tr>
                        <tr>
                        	<td width="100%">
                            	<table width="100%">
									<?php
                                    $sql='select * from navigation where parent in ("") order by link_description';
                                    $new = execute_query($sql);
									$i=1;
                                    while($row = mysqli_fetch_array($new)){
										$sql = 'select * from user_access where user_id="'.$sale['sno'].'" and file_name="'.$row['sno'].'"';
										$result_access = execute_query($sql);
										if(mysqli_num_rows($result_access)==1){
											$selected = 'checked="checked"';

										}
										else{
											$selected = '';
										}
										echo '<tr>
										<td>'.$row['link_description'].'</td>
										<td><input type="checkbox"  name="check_'.$row['sno'].'" value="" tabindex="'.$tab++.'" '.$selected.'><input type="hidden" name="id" id="id" value="'.$i++.'"></td>
										</tr>';
                                    }
                                    
                                    $sql='select * from navigation where parent in ("P") order by link_description';
                                    $new = execute_query($sql);
									$i=1;
                                    while($row = mysqli_fetch_array($new)){
										echo '<tr><th colspan="2">'.$row['link_description'].'</th></tr>';
										$sql = 'select * from navigation where parent in ('.$row['sno'].') order by link_description';
										$res_sub_menu = execute_query($sql);
										while($row_sub_menu = mysqli_fetch_array($res_sub_menu)){
											$sql = 'select * from user_access where user_id="'.$sale['sno'].'" and file_name="'.$row_sub_menu['sno'].'"';
											$result_access = execute_query($sql);
											//echo $sql.'<br>';
											if(mysqli_num_rows($result_access)==1){
												$selected = 'checked="checked"';

											}
											else{
												$selected = '';
											}
											echo '<tr>
											<td>'.$row_sub_menu['link_description'].'</td>
											<td><input type="checkbox"  name="check_'.$row_sub_menu['sno'].'" value="" tabindex="'.$tab++.'" '.$selected.'><input type="hidden" name="id" id="id" value="'.$i++.'">
											</tr>';
											
										}
                                    }
                                    
                                    ?>
                                </table>
							</td>
						</tr> 
					</table>
               <?php } ?>
                <input type="hidden" value="<?php echo $_GET['id'] ?>" id="user" name="user">
                <input id="save" name="submit" class="submit" type="submit" value="Submit" tabindex="10000">
                </form> 
	</div>
</div>
<?php
break;
  }
}

?>
