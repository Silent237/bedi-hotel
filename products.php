<?php
include("scripts/settings.php");$msg='';
$tabindex=1;
$connect=$db;
$software_type = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='software_type'"));
$software_type = $software_type['rate'];
if(isset($_POST['saveForm'])){
	foreach($_POST as $k=>$v){
		$_POST[$k] = htmlspecialchars(strtoupper($v), ENT_QUOTES);
	}
	if(!isset($_POST['conversion2'])){
		$_POST['conversion2']='';
		$_POST['unit2']='';
		$_POST['conversion3']='';
		$_POST['unit3']='';
	}
	//print_r($_POST);
	if($_POST['part_desc']==''){
		$msg .= '<li class="error">Description Cannot be left blank.</li>';
	}
	if($msg==''){
		if(!isset($_POST['category'])){
			$_POST['category']='';
		}
		if($_POST['edit_sno']==''){
			$sql='select * from `stock_available` where barcode="'.$_POST['part_barcode'].'"';
			$row=execute_query($sql);
			if(mysqli_num_rows($row)!=0 && $_POST['part_barcode']!=''){
				$msg.= "<li class='error'>Cannot add Product,Duplicate Barcode</li>";
			}
			$sql='select * from `stock_available` where description="'.$_POST['part_desc'].'"';
			$row=execute_query($sql);
			if(mysqli_num_rows($row)!=0){
				$msg .= '<li class="error">Duplicate Description.</li>';
			}

			if($msg==''){
				$sql='INSERT INTO `stock_available`(`description`, `type`, `product`, `part_no`, `vat`, `excise`, `company`, `unit`, `barcode`, `mrp`, `srp`, `msrp`, `warning`, `opening`, `inv_type`, `enable_barcode`, `enable_batch_no`, `category`, `warranty`) VALUES( "'.$_POST['part_desc'].'", "'.$_POST['part_type'].'", "'.$_POST['part_name'].'", "'.$_POST['part_no'].'", "'.$_POST['part_vat'].'", "'.$_POST['part_sat'].'", "'.$_POST['part_company_name'].'", "'.$_POST['part_unit'].'", "'.$_POST['part_barcode'].'", "'.$_POST['part_mrp'].'", "'.$_POST['part_srp'].'", "'.$_POST['part_msrp'].'", "'.$_POST['part_warning'].'", "'.$_POST['opening'].'", "'.$_POST['inv_type'].'", "'.$_POST['enable_barcode'].'", "'.$_POST['enable_batch_no'].'", "'.$_POST['category'].'", "'.$_POST['part_warranty'].'")';
				execute_query($sql);
				if(!mysqli_error($db)){
					$id = mysqli_insert_id($db);
					if($_POST['conversion2']!='' && $_POST['unit2']!=$_POST['part_unit']){
						$sql = 'insert into unit_conversion (parent_unit, unit, conversion, part_id)
						values("'.$_POST['part_unit'].'", "'.$_POST['unit2'].'", "'.$_POST['conversion2'].'", "'.$id.'")';
						execute_query($sql);
						if(mysqli_error($db)){
							$msg .= '<li>Error # 1 :'.$sql.'</li>';
						}
						else{
							$msg .= '<li>New Unit Added.</li>';
						}
					}
					
					if($_POST['conversion3']!='' && $_POST['unit3']!=$_POST['part_unit']){
						$sql = 'insert into unit_conversion (parent_unit, unit, conversion, part_id)
						values("'.$_POST['part_unit'].'", "'.$_POST['unit3'].'", "'.$_POST['conversion3'].'", "'.$id.'")';
						execute_query($sql);
						if(mysqli_error($db)){
							$msg .= '<li>Error # 1 :'.$sql.'</li>';
						}
						else{
							$msg .= '<li>New Unit Added.</li>';
						}
					}

					
					$msg .= '<li>Successful Inserted.</li>';
					$_POST['part_desc']="";
					$_POST['part_type'] = "";
					$_POST['part_name'] = "";
					$_POST['part_no'] = "";
					$_POST['part_vat'] = "";
					$_POST['part_sat'] = "";
					$_POST['part_company_name'] = "";
					$_POST['part_barcode'] = "";
					$_POST['part_unit'] = "";
					$_POST['part_mrp'] = "";
					$_POST['part_msrp'] = "";
					$_POST['part_srp'] = "";
					$_POST['part_warranty'] = "";
					$_POST['part_warning'] = "";
					$_POST['opening'] = "";
					$_POST['inv_type'] = "";
					unset($_POST['enable_barcode']);
					unset($_POST['enable_batch_no']);
					$_POST['category']='';
				}
				else{
					$msg .= '<li>Error PD-01 : '.mysqli_error($db).'</li>';
				}
			}
		}
		else{
			if(!isset($_POST['enable_barcode'])){
				$_POST['enable_barcode']='false';
			}
			if(!isset($_POST['enable_batch_no'])){
				$_POST['enable_batch_no']='false';
			}
			$sql = 'update stock_available set 
			`description` ="'.$_POST['part_desc'].'",
			`type` =  "'.$_POST['part_type'].'",
			`product` =  "'.$_POST['part_name'].'",
			`part_no` =  "'.$_POST['part_no'].'",
			`vat` =  "'.$_POST['part_vat'].'",
			`excise` =  "'.$_POST['part_sat'].'",
			`company` =  "'.$_POST['part_company_name'].'",
			`unit` =  "'.$_POST['part_unit'].'",
			`barcode` =  "'.$_POST['part_barcode'].'",
			`mrp` =  "'.$_POST['part_mrp'].'",
			`msrp` =  "'.$_POST['part_msrp'].'",
			`srp` =  "'.$_POST['part_srp'].'",
			`warranty` =  "'.$_POST['part_warranty'].'",
			`warning` =  "'.$_POST['part_warning'].'",
			`opening` =  "'.$_POST['opening'].'",
			`enable_barcode` =  "'.$_POST['enable_barcode'].'",
			`enable_batch_no` =  "'.$_POST['enable_batch_no'].'",
			`inv_type` =  "'.$_POST['inv_type'].'",
			`category` =  "'.$_POST['category'].'"
			 where sno="'.$_POST['edit_sno'].'"';
			 //echo $sql;
			execute_query($sql);
			if(mysqli_error($db)){
				$msg .= '<li>Error UP-01 : '.mysqli_error($db).' >> '.$sql.'</li>';
			}
			else{
				if($_POST['conversion2']!='' && $_POST['unit2']!=$_POST['part_unit']){
					$sql = 'delete from unit_conversion where part_id="'.$_POST['edit_sno'].'"';
					execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<li>Error # 1.01 :'.$sql.'</li>';
					}
					$sql = 'insert into unit_conversion (parent_unit, unit, conversion, part_id)
					values("'.$_POST['part_unit'].'", "'.$_POST['unit2'].'", "'.$_POST['conversion2'].'", "'.$_POST['edit_sno'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<li>Error # 1.02 :'.$sql.'</li>';
					}
					else{
						$msg .= '<li>New Unit Added.</li>';
					}
				}

				if($_POST['conversion3']!='' && $_POST['unit3']!=$_POST['part_unit']){
					$sql = 'insert into unit_conversion (parent_unit, unit, conversion, part_id)
					values("'.$_POST['part_unit'].'", "'.$_POST['unit3'].'", "'.$_POST['conversion3'].'", "'.$_POST['edit_sno'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<li>Error # 1.03 :'.$sql.'</li>';
					}
					else{
						$msg .= '<li>New Unit Added.</li>';
					}
				}
				$msg .= '<li>Update Successfull.</li>';
				$_POST['part_desc']="";
				$_POST['part_type'] = "";
				$_POST['part_name'] = "";
				$_POST['part_no'] = "";
				$_POST['part_vat'] = "";
				$_POST['part_sat'] = "";
				$_POST['part_company_name'] = "";
				$_POST['part_barcode'] = "";
				$_POST['part_unit'] = "";
				$_POST['part_mrp'] = "";
				$_POST['part_msrp'] = "";
				$_POST['part_srp'] = "";
				$_POST['part_warranty'] = "";
				$_POST['part_warning'] = "";
				$_POST['opening'] = "";
				$_POST['inv_type'] = "";
				unset($_POST['enable_barcode']);
				unset($_POST['enable_batch_no']);
				$_POST['category']='';
			}
		}
	}
}

if(isset($_GET['id'])){
	$sql = 'select * from unit_conversion where part_id='.$_GET['id'];
	$result = execute_query($sql);
	//echo $sql;
	$i=2;
	while($row_data = mysqli_fetch_array($result)){
		$product['conversion'.$i]=$row_data['conversion'];
		$product['unit'.$i]=$row_data['unit'];
		$i++;
	}
	
	$sql = 'select stock_available.sno as sno, stock_available.description as description, type, product, company, part_no, vat, excise,mrp,msrp,srp,warranty,warning, unit, barcode, opening, inv_type, enable_batch_no, enable_barcode, category from stock_available where sno='.$_GET['id'];
	$old_data = mysqli_fetch_array(execute_query($sql));
	$product['part_desc']=$old_data['description'];
	$product['part_type'] = $old_data['type'];
	$product['part_name'] = $old_data['product'];
	$product['part_no'] = $old_data['part_no'];
	$product['part_vat'] = $old_data['vat'];
	$product['part_sat'] = $old_data['excise'];
	$product['part_company_name'] = $old_data['company'];
	$product['part_barcode'] = $old_data['barcode'];
	$product['part_unit'] = $old_data['unit'];
	$product['part_mrp'] = $old_data['mrp'];
	$product['part_msrp'] = $old_data['msrp'];
	$product['part_srp'] = $old_data['srp'];
	$product['part_warranty'] = $old_data['warranty'];
	$product['part_warning'] = $old_data['warning'];
	$product['opening'] = $old_data['opening'];
	$product['enable_barcode'] = $old_data['enable_barcode'];
	$product['enable_batch_no'] = $old_data['enable_batch_no'];
	$product['inv_type'] = $old_data['inv_type'];
	$product['category'] = $old_data['category'];
}

if(isset($_GET['delid'])){
	
		$sql = 'delete from stock_available where sno='.$_GET['delid'];
		execute_query($sql);
		$msg.='Delete Successful';

}
if(isset($_GET['mid'])){
	$sql='update stock_estimate set part_id='.$_GET['mid'].' where part_id='.$_GET['alt'];
	execute_query($sql);
	$sql='update stock_issue set part_id='.$_GET['mid'].' where part_id='.$_GET['alt'];
	execute_query($sql);
	$sql='update stock_purchase set part_id='.$_GET['mid'].' where part_id='.$_GET['alt'];
	execute_query($sql);
	$sql='update stock_quotation set part_id='.$_GET['mid'].' where part_id='.$_GET['alt'];
	execute_query($sql);
	$sql='update stock_recieve set part_id='.$_GET['mid'].' where part_id='.$_GET['alt'];
	execute_query($sql);
	$sql='update stock_sale set part_id='.$_GET['mid'].' where part_id='.$_GET['alt'];
	execute_query($sql);
	$sql='delete from stock_available where sno='.$_GET['alt'];
	execute_query($sql);
}

if(!isset($_POST['part_desc'])){
	$_POST['part_desc']="";
	$_POST['part_type'] = "";
	$_POST['part_name'] = "";
	$_POST['part_no'] = "";
	$_POST['part_vat'] = "";
	$_POST['part_sat'] = "";
	$_POST['part_company_name'] = "";
	$_POST['part_barcode'] = "";
	$_POST['part_unit'] = "";
	$_POST['part_mrp'] = "";
	$_POST['part_msrp'] = "";
	$_POST['part_srp'] = "";
	$_POST['part_warranty'] = "";
	$_POST['part_warning'] = "";
	$_POST['opening'] = "";
	$_POST['category']='';
}

page_header();
?>
<script type="text/javascript" language="javascript">
function alternate_value(id){
	var alternate = prompt("Please enter product id to merge with this id.","");
	if(!alternate){
		alert("Can not merge without product id.");
		return false;
	}
	else{
		window.open("products.php?mid="+id+"&alt="+alternate, '_self');
		return true;
	}
}
</script>
    <div id="container">
    <h2>PRODUCTS</h2>
        <div id="form">
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form id="add_product" name="add_product" class="wufoo leftLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table class="no-print">
        	<tr>
            	<td>Part Name : </td>
                <td><input id="part_desc" name="part_desc" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_desc'];} else{ echo $_POST['part_desc'];} ?>" type="text"></td>
            	<td>Group : </td>
                <td>
                    <select name="part_type" tabindex="<?php echo $tabindex++; ?>">
                    <option value=""></option>
                    <?php
                    $sql = 'select * from new_type ORDER BY description';
                    $result = execute_query($sql);
                    while($row = mysqli_fetch_array($result)){
						echo '<option value="'.$row['sno'].'"';
						if(isset($_GET['id'])){
							if($row['sno']==$product['part_type']){
								echo ' selected="selected"';
							}
						}
						else{
							if($row['sno']==$_POST['part_type']){
								echo 'selected="selected"';
							}
						}
						echo ' >'.$row['description'].'</option>';
                    }
                    ?>
                    </select>
				</td>
				<!-- <td>INVOICE TYPE : </td>
				<td><select name="inv_type" id="inv_type" tabindex="<?php echo $tabindex++; ?>">
				<option value="tax" <?php if(isset($_GET['id'])){ if(strtolower($product['inv_type'])=="tax"){ echo 'selected="selected"';}}?>>TAXABLE</option>
				<option value="exempt" <?php if(isset($_GET['id'])){ if(strtolower($product['inv_type'])=="exempt"){echo 'selected="selected"';}}?>>EXEMPT</option></select></td> -->
			
            <!--<tr>
            	<td>Description</td>
                <td><input id="part_name" name="part_name" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_name'];} else{ echo $_POST['part_name'];} ?>" type="text"></td>
            	<td>Company Name</td>
                <td>
                <select name="part_company_name" tabindex="<?php echo $tabindex++; ?>">
                <option value=""></option>
				<?php
                $sql = 'select * from company ORDER BY description';
                $result = execute_query($sql);
                while($row = mysqli_fetch_array($result)){
                    echo '<option value="'.$row['sno'].'" ';
					if(isset($_GET['id'])){
						if($row['sno']==$product['part_company_name']){
							echo ' selected="selected"';
						}
					}
					else{
						if($row['sno']==$_POST['part_company_name']){
							echo 'selected="selected"';
						}
					}					
					echo '>'.$row['description'].'</option>';
                }
                ?>
                </select>
                </td>
			</tr>
            <tr>
            	<td>HSN Code</td>
                <td><input id="part_no" name="part_no" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_no'];} else{echo $_POST['part_no'];} ?>" type="text"></td>
            </tr>-->
          
            	<td>GST : </td>
                <td><select id="gst_rate" class="fieldtextmedium" tabindex="<?php echo $tabindex++; ?>" onchange="splitGST(this.value)">
    <option value="">Select</option>
    <option value="Nil Rated">Nil Rated</option>
    <option value="5">5%</option>
    <option value="12">12%</option>
    <option value="18">18%</option>
    <option value="28">28%</option>
</select>

<input type="hidden" id="part_vat" name="part_vat" value="<?php echo isset($_GET['id']) ? $product['part_vat'] : $_POST['part_vat']; ?>">
<input type="hidden" id="part_sat" name="part_sat" value="<?php echo isset($_GET['id']) ? $product['part_sat'] : $_POST['part_sat']; ?>">

<tr>
</td>
            	<td>HSN CODE : </td>
                <td><input id="part_srp" name="part_srp" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_srp'];} else{ echo $_POST['part_srp'];}?>" type="text"></td>
                            	<td>Unit : </td>
				<td><select name="part_unit"  id="part_unit" class="select" tabindex="<?php echo $tabindex++; ?>">
					<?php
					$sql='select * from unit';
					$res=execute_query($sql);
					while($row1=mysqli_fetch_array($res)){
						echo'<option value="'.$row1['sno'].'" ';
						if(isset($_POST['part_unit'])){
							if($_POST['part_unit']==$row1['sno']){
								echo 'selected="selected"';
							}
						}
						if(isset($_GET['id'])){
							if($product['part_unit']==$row1['sno']){
								echo 'selected="selected"';
							}
						}
						echo '>'.$row1['unit'].'-'.$row1['unit_desc'].'</option>';
					}
					?>
					</select>
					</div>

					<div style="float:left;"></td>
			

                <td>MRP : </td>
             	<td><input id="part_mrp" name="part_mrp" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_mrp'];} else{ echo $_POST['part_mrp']; }?>" type="text"></td>
             	<!-- <td>SRP : </td>
                <td><input id="part_srp" name="part_srp" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_srp'];} else{ echo $_POST['part_srp'];}?>" type="text"></td> -->
                </tr><tr>
				<td>Barcode : </td>
                <td><input id="part_barcode" name="part_barcode" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_barcode'];} else{ echo $_POST['part_barcode'];} ?>" type="text"></td>
            </tr>
            <!--<tr>
               <td>MSRP</td>
                <td><input id="part_msrp" name="part_msrp" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_msrp'];} else{ echo $_POST['part_msrp'];} ?>" type="text"></td>
              	
             </tr>
             <tr>
             	<td>Warranty</td>
                <td><input id="part_srp" name="part_warranty" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_warranty'];} else{ echo $_POST['part_warranty'];} ?>" type="text"></td>
                <td>Warning</td>
                <td><input id="part_srp" name="part_warning" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_warning'];} else{ echo $_POST['part_warning'];} ?>" type="text"></td>
              </tr>
              <tr>
                <td>Opening</td>
                <td><input id="opening" name="opening" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['opening'];} else{ echo $_POST['opening'];} ?>" type="text"></td>
            	<td>Barcode</td>
                <td><input id="part_barcode" name="part_barcode" class="fieldtextmedium" maxlength="255" tabindex="<?php echo $tabindex++; ?>" value="<?php if(isset($_GET['id'])){echo $product['part_barcode'];} else{ echo $_POST['part_barcode'];} ?>" type="text"></td>
			</tr>
			<tr><td>Enable Barcode</td>
			<td><input type="checkbox" name="enable_barcode" id="enable_barcode" value="true"  tabindex="<?php echo $tabindex++; ?>"
			<?php if(isset($_GET['id'])){ if(strtolower($product['enable_barcode'])=="on" || strtolower($product['enable_barcode'])=="true" ){ echo ' checked="checked"';}} if(isset($_POST['enable_barcode'])){echo 'checked="checked"';}?> /></td>
			 <td>Enable Batch No.</td>
			 <td><input type="checkbox" name="enable_batch_no" id="enable_batch_no"  tabindex="<?php echo $tabindex++; ?>"
			 value="true" <?php if(isset($_GET['id'])){ if(strtolower($product['enable_batch_no'])=="on" || strtolower($product['enable_batch_no'])=="true" ){ echo 'checked="checked"';}} if(isset($_POST['enable_batch_no'])){echo 'checked="checked"';}?>/></td>
			</tr>-->
			<?php
			if($software_type=='pharma'){
			?>
			<tr>
				<td>Category</td>
				<td colspan="3">
					<input type="radio" name="category" value="schedule_h" <?php if(isset($_GET['id'])){if($product['category']=='SCHEDULE_H'){echo 'checked="checked"';}}else{if($_POST['category']=='SCHEDULE_H'){echo 'checked="checked"';}}?>> Schedule H Drug 
					<input type="radio" name="category" value="schedule_h1" <?php if(isset($_GET['id'])){if($product['category']=='SCHEDULE_H1'){echo 'checked="checked"';}}else{if($_POST['category']=='SCHEDULE_H1'){echo 'checked="checked"';}}?>> Schedule H1 Drug 
					<input type="radio" name="category" value="narcotics" <?php if(isset($_GET['id'])){if($product['category']=='NARCOTICS'){echo 'checked="checked"';}}else{if($_POST['category']=='NARCOTICS'){echo 'checked="checked"';}}?>> Narcotic Drug 
					<input type="radio" name="category" value="other" <?php if(isset($_GET['id'])){if($product['category']=='OTHER'){echo 'checked="checked"';}}else{if($_POST['category']=='OTHER'){echo 'checked="checked"';}}?>> Other Special Category Drug 
				</td>
			</tr>
			<tr>
				<td>Unit2  :</td>
				<td><select name="unit2" id="unit2" class="select" tabindex="<?php echo $tabindex++;?>">

						<?php
						$sql='select * from unit';
						$res=execute_query($sql);
						while($row=mysqli_fetch_array($res)){
							echo'<option value="'.$row['sno'].'" ';
							if(isset($_POST['unit2'])){
								if($_POST['unit2']==$row['sno']){
									echo 'selected="selected"';
								}
							}
							if(isset($product['unit2'])){
								if($product['unit2']==$row['sno']){
									echo 'selected="selected"';
								}
							}
							echo '>'.$row['unit'].'-'.$row['unit_desc'].'</option>';
						}
						?>
					</select></td>
				<td>Conversion :</td>
				<td><input type="text" name="conversion2" id="conversion2" tabindex="<?php echo $tabindex++; ?>" class="input" value="<?php if(isset($product['conversion2'])){echo $product['conversion2'];}?>" /></td>
			</tr>
			<tr>
				<td>Unit3  :</td>
				<td><select name="unit3" id="unit3" class="select" tabindex="<?php echo $tabindex++;?>">

						<?php
						$sql='select * from unit';
						$res=execute_query($sql);
						while($row=mysqli_fetch_array($res)){
							echo'<option value="'.$row['sno'].'" ';
							if(isset($_POST['unit3'])){
								if($_POST['unit3']==$row['sno']){
									echo 'selected="selected"';
								}
							}
							if(isset($product['unit3'])){
								if($product['unit3']==$row['sno']){
									echo 'selected="selected"';
								}
							}
							echo '>'.$row['unit'].'-'.$row['unit_desc'].'</option>';
						}
						?>
					</select></td>
				<td>Conversion :</td>
				<td><input type="text" name="conversion3" id="conversion3" tabindex="<?php echo $tabindex++; ?>" class="input" value="<?php if(isset($product['conversion3'])){echo $product['conversion3'];}?>" /></td>
			</tr>
			<?php } ?>
			<tr>
                <td colspan="6" style="text-align: center;">
                <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];} ?>" /><input id="saveForm" name="saveForm" class="btTxt submit" type="submit" value="Add/Edit" onMouseDown="" tabindex="<?php echo $tabindex++; ?>"></td>
			</tr>
		</table>
        <table>
        	<thead>
        	<tr>
            	<th>S.No.</th>
            	<th>Code</th>
                <th>Part Name</th>
                <!--<th>Company</th>-->
                <th>Type</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>Unit</th>
                <!--<th>HSN Code</th>-->
                <th>MRP</th>
                <!--<th>MSRP</th>-->
                <th>HSN CODE</th>
                <!--<th>Warning</th>
                <th>Opening</th>-->
                <th>PID</th>
                <th class="no-print">Edit</th>
                <th class="no-print">Delete</th>
                <th class="no-print">Merge</th>
			</tr>
            </thead>
            <tbody>
            <?php
            $i=1;
            $sql = 'select stock_available.sno as sno, stock_available.description as description, product, company.description as company, new_type.description as type, unit, part_no, barcode, vat, mrp,msrp,srp,warranty,warning, excise, opening, category from stock_available left join company on stock_available.company=company.sno left join new_type on new_type.sno = type';
			//echo $sql;
	        $opening = execute_query($sql);
            while($row = mysqli_fetch_array($opening)){
				$unit='';
				$sql = 'select * from unit_conversion where part_id='.$row['sno'];
				$result_conv = execute_query($sql);
				if(mysqli_num_rows($result_conv)>0){
					while($row_conv = mysqli_fetch_array($result_conv)){
						$unit .= '<br />1 '.get_unit($row_conv['parent_unit']).' = '.$row_conv['conversion'].' '.get_unit($row_conv['unit']);
					}
				}
				$unit = get_unit($row['unit']).$unit;
				$bg_color = $i % 2 == 0 ? '#EEE' : '#CCC';
	            echo '<tr style="background:' . $bg_color . ';">
				<td>'.$i++.'</td>
				<td>'.$row['sno'].'
				<td>'.$row['description'].'<small>';
				if($row['product']!=''){
					echo '<br />Description : '.$row['product'];
				}
				if($row['barcode']!=''){
					echo '<br />Barcode : '.$row['barcode'];
				}
				if($row['warranty']!=''){
					echo '<br/>Warranty : '.$row['warranty'];
				}
				if($row['category']!=''){
					echo '<br/>Category : '.$row['category'];
				}
				echo '</small></td>';
				//<td>'.$row['company'].'</td>
				echo '<td>'.$row['type'].'</td>
				<td>'.$row['vat'].'</td>
				<td>'.$row['excise'].'</td>
				<td>'.$unit.'</td>';
				//<td>'.$row['part_no'].'</td>
				echo '<td>'.$row['mrp'].'</td>';
				//<td>'.$row['msrp'].'</td>
				echo '<td>'.$row['srp'].'</td>';
				//<td>'.$row['warning'].'</td>
				//<td>'.$row['opening'].'</td>
				echo '<td>'.$row['sno'].'</td>
				<td class="no-print"><a href="products.php?id='.$row['sno'].'"><i class="fas fa-edit"></i></a></td>
				<td class="no-print"><a href="products.php?delid='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>
				<td class="no-print"><a href="#" onclick="return alternate_value('.$row['sno'].')">Merge</td>
				</tr>';           
            }
            ?>
            </tbody>
		</table>
        </form>
        </div>
    </div>
<script>
function splitGST(value) {
    const vat = document.getElementById('part_vat');
    const sat = document.getElementById('part_sat');

    if (value === "Nil Rated" || value === "") {
        vat.value = 0;
        sat.value = 0;
    } else {
        const gst = parseFloat(value);
        const half = (gst / 2).toFixed(2);
        vat.value = half;
        sat.value = half;
    }
}
</script>

<?php 
navigation('');
page_footer();
?>