<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
$link = $db;
$sql = "select * from customer where sno=".$_GET['id'];
$result = execute_query($sql);
$row=mysqli_fetch_assoc( $result );
$msg .= '<li class="error">Documents of '.$row['cust_name'].'</li>';

if(isset($_GET['del'])){
	//$target_dir = 'cust_data'.'/';
	unlink($_GET['del']);
}


page_header();
?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
 <div id="container">
        <h2>ID Viewer</h2>	
		<?php echo '<ul>'.$msg.'</ul>'; ?>
		<form action="vacant_room.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<th>S.No.</th>
					<th>Document Name</th>
					<th>Date Modified</th>
					<th>View</th>
					<th>Delete</th>
				</tr>
				<?php	
				$i=1;
				$scanned_directory = array_diff(scandir('cust_data'), array('..', '.'));
				$scanned_directory = glob("cust_data/".$_GET['id'].'_img.*');
				$id_len = strlen($_GET['id'])+11;
				foreach($scanned_directory as $v){
					$filename = $v;
					$date_modified = date ("F d Y H:i:s.", filemtime($filename));
					echo '<tr>
					<td>'.$i++.'</td>
					<td>'.strtoupper(substr($v, $id_len)).'</td>
					<td>'.$date_modified.'</td>
					<td><a href="'.$filename.'">Download</a></td>';
					echo '<td><a href="id_viewer.php?id='.$_GET['id'].'&del='.$v.'" onclick="return confirm(\'Are you sure ?\');">Delete</a></td>';
					
					echo'
					</tr>';
				}
				?>
		</table>
	</form>
</div>
<script src="js/jquery.datetimepicker.full.js"></script>
<script language="JavaScript">
$('#exit_date').datetimepicker({
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
page_footer();
?>
