<?php
session_cache_limiter('nocache');
include ("scripts/settings.php");
page_header();
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
if(isset($_GET['del_id'])){
	$sql='delete from personal_information where sno='.$_GET['del_id'];
	$result = execute_query($sql);
	echo 'Data Deleted...';
}
?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>

<script type="text/javascript" language="javascript">
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name1",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='cust_name']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=company_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='company_name']").val(ui.item.label);
			$('#cust_sno').val(ui.item.id);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#company_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
});
</script>
 <div id="container">
        <h2>Personal Information Report</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="report_personal_information.php" id="report_form" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
		<table width="100%" class="no-print">
            	<tr style="background:#CCC;">
                	<td>Date From</td>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_from', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_from'])){echo $_POST['allot_from'];}else{echo date("Y-m-d");}?>', 1));
                    </script>
                    </span>
                    </td>
                	<td>Date To</td>
                    <td>
                    <span>
                    <script type="text/javascript" language="javascript">
                    document.writeln(DateInput('allot_to', "report_form", false, 'YYYY-MM-DD', '<?php if(isset($_POST['allot_to'])){echo $_POST['allot_to'];}else{echo date("Y-m-d");}?>', 4));
                    </script>
                    </span>
                    </td>
                </tr>
                <tr>
                	<td>Name</td>
                	<td>
                		<input type="text" name="name" id="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];}?>">
                	</td>
                	<td>Father Name</td>
                	<td><input type="text" name="father_name" id="father_name" value="<?php if(isset($_POST['father_name'])){echo $_POST['father_name'];}?>"></td>
                </tr>
            	<tr class="no-print">
                	<th >
                    	<input type="submit" name="submit_form" value="Search with Filters" class="btTxt submit">
                    </th>
                    <th >
                    	<input type="submit" name="reset_form" value="Reset Filters" class="btTxt submit">
                    </th>
					<th colspan="2">
                <input type="submit" name="download_excel" value="Download in Excel" class="btTxt submit btn-danger" onclick="return submitExportExcel(event);">
            </th>
                </tr>
            </table>	
		</form>
			<table width="100%">
				<tr style="background:#000; color:#FFF;">
					<th>S.No.</th>
					<th>Name</th>
					<th>Father Name</th>
					<th>Mobile Number</th>
					<th>Date Of Entry</th>
                    <th>Occupation</th>
                    <th>Address</th>
                    <th>Police Station</th>
                    <th>District</th>
                    <th>State</th>
                    <th>Reason For Come</th>
					<th class="no-print">Edit</th>
					<th class="no-print">Delete</th>
				</tr>
    <?php
    			$sql_mop = '';
				$sql = 'select * from personal_information where 1=1 ';
				if(isset($_POST['submit_form'])){
					$sql .= ' and creation_time>="'.$_POST['allot_from'].'" and creation_time<"'.date("Y-m-d", strtotime($_POST['allot_to'])+86400).'"';
					if ($_POST['name'] != '') {
						$sql .= ' AND `name` like "%'.$_POST['name'].'%" ';
					}
					if ($_POST['father_name'] != '') {
						$sql .= ' AND `father_name` like "%'.$_POST['father_name'].'%" ';
					}
				}
				else{
					$sql .= ' and creation_time>="'.date("Y-m-d").'" and creation_time<"'.date("Y-m-d", strtotime(date("Y-m-d"))+86400).'"';
				}
				$result=execute_query($sql);
				$i=1;
				$tot=0;
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
					<td>'.$row['name'].'</td>
					<td>'.$row['father_name'].'</td>
					<td>'.$row['mobile_number'].'</td>
					<td>'.date('d-m-Y' , strtotime($row['creation_time'])).'</td>
					<td>'.$row['occupation'].'</td>
					<td>'.$row['address'].'</td>
					<td>'.$row['police_station'].'</td>
					<td>'.$row['district'].'</td>
					<td>'.$row['state'].'</td>
					<td>'.$row['reason_for_come'].'</td>';
					echo '<td class="no-print"><a href="personal_information.php?e_id='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-edit"></i></a></td>';
					echo '<td class="no-print"><a href="report_personal_information.php?del_id='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt" style="color:red;"></i></a></td>';
					echo '</tr>';
				}
?>
</table>
</div>

<script>
function submitExportExcel(event) {
    event.preventDefault(); // Stop normal submit
    var form = document.getElementById('report_form');
    form.action = 'prsinfo_report_export.php';
    form.submit();
    return false;
}
</script>
<?php
navigation('');
page_footer();
?>
