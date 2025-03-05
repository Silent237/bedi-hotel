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
	$con = $db;
	if($_POST['cust_name']=='') {
		$msg .= '<li class="error">Please Enter Customer Name.</li>';
	}
	$sql = 'select * from customer where mobile="'.$_POST['mobile1'].'"';
	$result = execute_query($sql);
	if(mysqli_num_rows($result)==1 && $_POST['customer_sno']==''){
		$msg .= '<li class="error">Customer Already Exists</li>';
	}
	if($msg=='')
	{
		if($_POST['customer_sno']!=''){
			$sql = 'update customer set 
			cust_name="'.$_POST['cust_name'].'",
			company_name="'.$_POST['company_name'].'",
			fname="'.$_POST['father_name'].'",
			address="'.$_POST['address1'].'",
			city="'.$_POST['city'].'",
			zipcode="'.$_POST['postal_code'].'",
			state="'.$_POST['state'].'",
			photo="'.$new_photo.'",
			mobile="'.$_POST['mobile1'].'",
			occupation="'.$_POST['occupation'].'",
			age="'.$_POST['age'].'",
			edited_by="'.$_SESSION['username'].'",
			edited_on=CURRENT_TIMESTAMP,
			remarks="'.$_POST['remarks'].'",
			reason="'.$_POST['reason'].'",
			id_1="'.$_POST['id_1'].'",
			id_2="'.$_POST['id_2'].'",
			id_3="'.$_POST['id_3'].'",
			id_type="'.$_POST['id_type'].'"
			where sno='.$_POST['customer_sno'];
			execute_query($sql);
			/*$stmt = $con->prepare('update customer set cust_name=?,company_name=?, fname=?, address=?,  city=?, zipcode=?, state=?, photo=?, mobile=?, occupation=?, age=?, edited_by=?, edited_on=CURRENT_TIMESTAMP, remarks=?, reason=?,  id_1=?, id_2=?, id_3=?, id_type=? where sno='.$_POST['customer_sno']);
			$stmt->execute([$_POST['cust_name'],$_POST['company_name'], $_POST['father_name'], $_POST['address1'],  $_POST['city'], $_POST['postal_code'],$_POST['state'], $new_photo, $_POST['mobile1'], $_POST['occupation'], $_POST['age'], $_SESSION['username'], $_POST['remarks'], $_POST['reason'],  $_POST['id_1'], $_POST['id_2']], $_POST['id_3'], $_POST['id_type']);*/
			$id = $_POST['customer_sno'];
			if(mysqli_error($db)){
				$msg .= '<li class="error">Error # 01 : '.$sql.' >> '.mysqli_error($db).'</li>';
			}
			else{
				$msg .= '<li class="error">Update successful.</li>';	
			}			
		}
		else{
			
			$sql='INSERT INTO customer (cust_name,company_name, fname, address, add_2, city, state, zipcode, mobile,   occupation, age, created_by, created_on, remarks, photo, reason, id_1, id_2, id_3, id_type) VALUES ("'.$_POST['cust_name'].'","'.$_POST['company_name'].'", "'.$_POST['father_name'].'", "'.$_POST['address1'].'", "", "'.$_POST['city'].'", "'.$_POST['state'].'", "'.$_POST['postal_code'].'", "'.$_POST['mobile1'].'", "'.$_POST['occupation'].'", "'.$_POST['age'].'", "'.$_SESSION['username'].'", CURRENT_TIMESTAMP, "'.$_POST['remarks'].'", "", "'.$_POST['reason'].'",  "'.$_POST['id_1'].'", "'.$_POST['id_2'].'", "'.$_POST['id_3'].'", "'.$_POST['id_type'].'")';
			$result = execute_query($sql);
			$msg .= '<li class="error">Customer Added successfully</li>';
			$id = $db->insert_id;
		}
		
		$target_dir = "cust_data/";
		$temp = explode(".", $_FILES["fileToUpload"]["name"]);
		$newfilename = $id . '_img.' . end($temp);	
		$target_file = $target_dir . $newfilename;
		if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
			$msg.='<li class="error">ID Uploaded</li>';
			$sql = 'update customer set photo="'.$newfilename.'" where sno='.$id;
			execute_query($sql);
		}
		else{
			$msg.='<li class="error">ID Upload Failed. Manually Upload Use ID : '.$id.'</li>';
		}
		
	}
}

if(isset($_GET['id'])){
	$sql = 'select * from customer where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row=mysqli_fetch_assoc( $result );
}
if(isset($_GET['del'])){
	$sql = 'delete from customer where sno='.$_GET['del'];
	$result = execute_query($sql);
	$sql='delete from allotment where cust_id='.$_GET['del'];
	$result = execute_query($sql);
	$sql='delete from customer_transactions where cust_id='.$_GET['del'];
	$result = execute_query($sql);
}
?>
<script type="text/javascript" language="javascript" src="form_validator.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>

 <div id="container">
 	   <a href="customer_report.php" style="float:right;font-size:22px;font-family:algrian">Guest Report</a>
        <h2>Add New Guest</h2>	
        <?php echo $msg;
		$tab=1; ?>	
		<form action="admin_customers.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
 	<table>
		<tr>
			<td>Guest Name</td>
            <td>
				<input id="cust_name" name="cust_name" class="field text medium" value="<?php if(isset($row['cust_name'])){echo $row['cust_name'];}?>" maxlength="255" tabindex="<?php echo $tab++;?>" type="text" required/>
			</td>
			<td>Father Name</td>
			<td><input id="father_name" name="father_name" type="text" class="field text medium" value="<?php if(isset($row['fname'])){echo $row['fname'];}?>" tabindex="<?php echo $tab++;?>"/></td>
	    </tr>
            <!--<input id="customer_sno" name="customer_sno" type="hidden"></td>
            <td colspan="2" rowspan="7" align="right">
            	<div style="width:150px; height:150px; border:1px solid; text-align:center; float:left;" id="profile">
                	<img src="<?php if(isset($row['photo'])){echo $row['photo'];}?>" height="150" width="150"><input type="hidden" name="profile_name" value="<?php if(isset($row['photo'])){echo $row['photo'];}?>">
                </div>
            	<div style="width:250px; border:1px solid; text-align:center;">
				<script language="JavaScript">
                        document.write( webcam.get_html(250, 200) );
                </script>
                <input type=button value="Take Snapshot" onClick="take_snapshot()" style="width:150px;">
                &nbsp;&nbsp;
                <input type=button value="Con." onClick="webcam.configure()" style="width:35px;">
                <div id="upload_results" style="background-color:#eee;"></div>
                </div>
            </td>-->
		</tr>
		<tr>
			<td>Company Name</td>
            <td>
            	<input id="company_name" name="company_name" class="field text medium" value="<?php if(isset($row['company_name'])){echo $row['company_name'];}?>" maxlength="255" tabindex="<?php echo $tab++;?>" type="text"/></td>
			<td>Address</td>
			<td><input id="address1" name="address1" type="text" class="field text addr" value="<?php if(isset($row['address'])){echo $row['address'];}?>" tabindex="<?php echo $tab++;?>"/></td>
		</tr>
        <tr>
			<td>SAC/HSN</td>
			<td><input id="id_1" name="id_1" type="text" class="field text addr" value="<?php if(isset($row['id_1'])){echo $row['id_1'];}?>" tabindex="<?php echo $tab++;?>" /></td>
			<td>GSTIN</td>
			<td><input id="id_2" name="id_2" type="text" class="field text addr" value="<?php if(isset($row['id_2'])){echo $row['id_2'];}?>" tabindex="<?php echo $tab++;?>" /></td>
		</tr>
		<tr>
		    <td>ID Number</td>
		    <td>
			<?php
			  $sql="SELECT * FROM customer";
			  $result=execute_query($sql);
			  $row=mysqli_fetch_array($result);
			  
			?>
		    <select id="id_type" name="id_type" class="field text small" maxlength="255" tabindex="<?php echo $tab++;?>" >
				<option value="AADHAAR" <?php if($row['id_type']=='AADHAAR'){echo ' selected ';} ?>>Aadhaar</option>
				<option value="PAN" <?php if($row['id_type']=='PAN'){echo ' selected ';} ?>>PAN</option>
				<option value="DL" <?php if($row['id_type']=='DL'){echo ' selected ';} ?>>Driving License</option>
				<option value="OTHERS" <?php if($row['id_type']=='OTHERS'){echo ' selected ';} ?>>Others</option>

			</select>
		    <input id="id_3" name="id_3" type="text" class="field text addr small" value="<?php if(isset($row['id_3'])){echo $row['id_3'];}?>" tabindex="<?php echo $tab++;?>" />
			</td>
	    	<td>
		    <input type="file" name="fileToUpload" id="fileToUpload"></td>
		</tr>
        <tr>
        	<td>Mobile </td>
        	<td><input name="mobile1" type="text" value="<?php if(isset($row['mobile'])){echo $row['mobile'];}?>" class="field text medium" tabindex="<?php echo $tab++;?>" id="mobile1" /></td>
			<td>City</td>
			<td><input id="city" name="city" type="text" class="field text addr" value="<?php if(isset($row['city'])){echo $row['city'];}?>" tabindex="<?php echo $tab++;?>"/></td>
		</tr>
		<tr>
			<td>State</td>
			<td>
          		<select id="state" name="state" class="field select addr" tabindex="<?php echo $tab++;?>" >
           		<?php
				$sql = 'select * from state_name';
				$result_state = execute_query($sql);
				while($row_state = mysqli_fetch_assoc($result_state)){
					echo '<option value="'.$row_state['state_code'].'" ';
					if(isset($row['state'])){
						if($row['state']==$row_state['state_code']){
							echo ' selected="selected" ';
						}
					}
					else{
						if($row_state['state_code']==9){
							echo ' selected="selected" ';
						}
					}
					echo '>'.$row_state['indian_states'].'</option>';
				}
				
				?>
				</select></td>
				<td>Postal Code</td>
				<td><input id="postal_code" name="postal_code" type="text" class="field text addr" value="<?php if(isset($row['zipcode'])){echo $row['zipcode'];}?>" maxlength="15" tabindex="<?php echo $tab++;?>"/></td>
            </tr>
            
      		
			<td>Occupation</td>
            <td><input type="text" name="occupation" tabindex="<?php echo $tab++;?>" id="occupation" value="<?php if(isset($row['occupation'])){echo $row['occupation'];}?>" class="field text medium" /></td>
            <td>Age</td>
            <td><input type="text" name="age" tabindex="<?php echo $tab++;?>" id="age" value="<?php if(isset($row['age'])){echo $row['age'];}?>" class="field text medium" /></td>
            </td>
            </tr>
            <tr>			
			<td>Reason For Coming</td>
            <td><input type="text" name="reason" tabindex="<?php echo $tab++;?>" id="reason" value="<?php if(isset($row['reason'])){echo $row['reason'];}?>" class="field text medium" /></td>
            <td>Remarks</td>
			<td><input type="text" name="remarks" tabindex="<?php echo $tab++;?>" id="remarks" value="<?php if(isset($row['remarks'])){echo $row['remarks'];}?>" class="field text medium" /></td>
        <!--</tr>
            <td>ID Proof </td><td><input name="fileToUpload" id="fileToUpload" type=file></td>
            </tr>-->
           
        <tr>
        </tr>
		<tr>
			<td colspan="4"><input type="hidden" name="customer_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
			<input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update Customer" onMouseDown="" tabindex="<?php echo $tab++;?>"></td>
        </tr>
	</table>
	</form>
<!--<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Photo</th>
					<th>Company Name</th>
					<th>Guest Name</th>
					<th>Father's Name</th>
					<th>ID</th>
					<th>GSTIN</th>
					<th>Mobile</th>
					<th>Occupation</th>
					<th>City</th>
                    <th>Edit</th>
					<th>Delete</th>
					<th>View ID</th>
					<th>Allot Room</th>
				</tr>
    <?php
			$sql = 'select * from customer';
			$result=execute_query($sql);
	$i=1;
	foreach($result as $row)
	{
		if($i%2==0){
			$col = '#CCC';
		}
		else{
			$col = '#EEE';
		}
		echo '<tr style="background:'.$col.'">
		<td>'.$i++.'</td>
		<td><a href="'.$row['photo'].'" target="_blank"><img src="'.$row['photo'].'" style="height:50px;"></a></td>
		<td>'.$row['cust_name'].'</td>
		<td>'.$row['company_name'].'</td>
		<td>'.$row['fname'].'</td>
		<td>'.$row['id_1'].'</td>
		<td>'.$row['id_2'].'</td>
		<td>'.$row['mobile'].'</td>
		<td>'.$row['occupation'].'</td>
		<td>'.$row['city'].'</td>
		<td><a href="admin_customers.php?id='.$row['sno'].'">Edit</a></td>
		<td><a href="admin_customers.php?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
		<td><a href="id_viewer.php?id='.$row['sno'].'">View ID</a></td>
		<td><a href="allotment.php?alt='.$row['sno'].'">Allot Room</a></td>
		</tr>';
	}
?>
</table>-->
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
	webcam.set_api_url( 'camera.php' );
	webcam.set_quality( 90 ); // JPEG quality (1 - 100)
	webcam.set_shutter_sound( true ); // play shutter click sound
	webcam.set_hook( 'onComplete', 'my_completion_handler' );

	function take_snapshot(){
		// take snapshot and upload to server
		document.getElementById('upload_results').innerHTML = '<h3>Uploading...</h3>';
		webcam.snap();
	}

	function my_completion_handler(msg) {
		// extract URL out of PHP output
		// show JPEG image in page
		document.getElementById('upload_results').innerHTML ='<h3>Upload Successful!</h3>';
		document.getElementById('profile').innerHTML = '<img src="'+msg+'" height="150" width="150"><input type="hidden" name="profile_name" value="'+msg+'">';
		// reset camera for another shot
		webcam.reset();
	}

	$('#check_in_time').datetimepicker({
	step:15,
	format: 'd-m-Y H:i',
	value: '<?php
	if(isset($_POST['date_from'])){
		echo $_POST['date_from'];
	}
	else{
		echo date("d-m-Y H:i");	
	}
	?>'
	});

	
</script>

<?php
navigation('');
page_footer();
?>
