<?php
include("scripts/settings.php");
session_cache_limiter('nocache');
session_start();
page_header();
$msg='';
$tab=1;
if(isset($_POST['submit'])){
	if($_POST['user_pass']!==''){
		$sql='select * from users where userid = "'.$_POST['user_name'].'"';
		$user = execute_query($sql);
		if(mysqli_num_rows($user)==0) {
			$sql='INSERT INTO `users`(`userid`,`pwd`,`type`, user_name, father_name, address, mobile) VALUES 
			("'.$_POST['user_id'].'","'.$_POST['user_pass'].'",1, "'.$_POST['user_name'].'", "'.$_POST['father_name'].'", "'.$_POST['address'].'", "'.$_POST['mobile'].'")';
			execute_query($sql);
			$user_sno = insert_id($db);
			
			$sql = 'select * from navigation';
			$result=execute_query($sql);
			while($nav=mysqli_fetch_array($result)){
				$check1='check_'.$nav['sno'];
				if(isset($_POST[$check1])){
					$sql='INSERT INTO `user_access`(`user_id`, `file_name`, `created_by`, `creation_time`) 
					VALUES("'.$user_sno.'","'.$nav['sno'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'")';
					execute_query($sql);

				}
			}
		}
	    else {
		   echo '<li>User already exist</li>';
		}
	}
	else{
		echo '<li>Please insert user detail correctly</li>';
	}
}
?>
    <div id="container">
    <div style="float:right; margin-right:50px;"><a href="edit_user.php"><img src="images/purchase_report.jpg" style="width:30px;">Edit User</a></div>
    <h2>USERS</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
			<form action="admin_users.php" class="addproduct" name="addproduct" enctype="multipart/form-data" method="POST" onSubmit="" >
            	<table>
                	<tr>
                    	<td>User ID : </td>
                        <td><input id="user_id" name="user_id"  class="fieldtextmedium" maxlength="25" tabindex="<?php echo $tab++; ?>" type="text"/></td>
                    </tr>
                    <tr>	
                    	<td>User Password : </td>
                        <td><input id="user_pass" name="user_pass"  class="fieldtextmedium" maxlength="25" tabindex="<?php echo $tab++; ?>" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>User Name : </td>
                        <td><input id="user_name" name="user_name"  class="fieldtextmedium" maxlength="25" tabindex="<?php echo $tab++; ?>" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>Father Name : </td>
                        <td><input id="father_name" name="father_name"  class="fieldtextmedium" maxlength="25" tabindex="<?php echo $tab++; ?>" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>Address : </td>
                        <td><input id="address" name="address"  class="fieldtextmedium" maxlength="25" tabindex="<?php echo $tab++; ?>" type="text"/></td>
                    </tr>
                    <tr>
                    	<td>Mobile : </td>
                        <td><input id="mobile" name="mobile"  class="fieldtextmedium" maxlength="25" tabindex="<?php echo $tab++; ?>" type="text"/></td>
                    </tr>
				</table>
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
										$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
										echo '<tr style="background:' . $bg_color . ';">
										<td>'.$row['link_description'].'</td>
										<td><input type="checkbox"  name="check_'.$row['sno'].'" value="" tabindex="'.$tab++.'"></td>
										<input type="hidden" name="id" id="id" value="'.$i++.'">
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
											$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
											echo '<tr style="background:' . $bg_color . ';">
											<td>'.$row_sub_menu['link_description'].'</td>
											<td><input type="checkbox"  name="check_'.$row_sub_menu['sno'].'" value="" tabindex="'.$tab++.'"></td>
											<input type="hidden" name="id" id="id" value="'.$i++.'">
											</tr>';
											
										}
                                    }
                                    
                                    ?>
                                </table>
							</td>
						</tr> 
					</table>
                <input id="save" name="submit" class="submit large" type="submit" value="Submit" tabindex="10000">
			</form>
		</div>
	</div>
<?php 
navigation(''); 
page_footer();
?>



