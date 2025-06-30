<?php
session_cache_limiter('nocache');
include("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');

if (isset($_POST['ajax']) && $_POST['ajax'] === 'toggle') {
    $rid = intval($_POST['room_id']);
    $ok = execute_query("UPDATE room_master SET status = 0 WHERE sno = $rid");
    echo $ok ? 1 : 0;
    exit;
}

date_default_timezone_set('Asia/Calcutta');
page_header();
navigation('');
page_footer();
mysqli_autocommit($GLOBALS['db'], true);
?>

<style>
.room-tile {
	position: relative;
	cursor: pointer;
}
.room-tile:hover {
	transform: scale(1.05);
}
</style>

<div id="container">
	<h2>Room Status</h2>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<table width="50%">
			<tr>
				<th>Report Date</th>
				<th>
					<input
						name="allotment_date"
						type="text"
						value="<?php echo isset($_POST['allotment_date'])
							? $_POST['allotment_date']
							: date('Y-m-d H:i:s'); ?>"
						class="field text medium"
						id="allotment_date"
					/>
				</th>
				<th><input type="submit" name="search" value="Show Report"></th>
			</tr>
		</table>
	</form>

<?php
$allotSet = isset($_POST['allotment_date']);
$i = 1;
$floor = '';
echo '<div>';

$sql = $allotSet
	? 'SELECT room_master.sno AS sno, room_name, status, floor_name
	   FROM room_master JOIN floor_master ON floor_master.sno = floor_id
	   ORDER BY floor_id, room_name'
	: 'SELECT room_master.sno AS sno, room_name, status, floor_name
	   FROM room_master JOIN floor_master ON floor_master.sno = floor_id
	   WHERE room_status = 1
	   ORDER BY floor_id, room_name';

$result = execute_query($sql);
$rows = $allotSet ? mysqli_fetch_all($result, MYSQLI_ASSOC) : $result;

if ($allotSet) {
	$_POST['allotment_date'] = date('Y-m-d 23:59:59', strtotime($_POST['allotment_date']));
}

foreach ($rows as $row) {
	if ($floor !== $row['floor_name']) {
		$floor = $row['floor_name'];
		echo '</div><div style="border:0; float:left; width:95%"><h2>'
		     . $row['floor_name'] .
		     '</h2><div style="clear:both;"></div>';
	}

	// colour logic
	if ($row['status'] == 2) {
		$col = '#3399ff';
		$text_col = '#fff';
		$extraCls = 'clickable';
	} elseif (!$allotSet && ($row['status'] == '' || $row['status'] == 0)) {
		$col = '#bbff33';
		$text_col = '#666666';
		$extraCls = '';
	} else {
		if ($allotSet) {
			$sql2 = 'SELECT 1 FROM allotment
			         WHERE room_id = "' . $row['sno'] . '"
			         AND "' . $_POST['allotment_date'] . '" BETWEEN allotment_date AND exit_date';
			$hasGuest = mysqli_num_rows(execute_query($sql2));
			if ($hasGuest) {
				$col = '#F00';
				$text_col = '#fff';
			} else {
				$col = '#bbff33';
				$text_col = '#666666';
			}
		} else {
			$col = '#F00';
			$text_col = '#fff';
		}
		$extraCls = '';
	}

	echo '<div
	class="room-tile ' . $extraCls . '"
	data-roomid="' . $row['sno'] . '"
	style="
		width:60px;height:50px;
		margin:2px;float:left;
		display:flex;align-items:center;justify-content:center;
		font-weight:bold;font-size:16px;
		border-radius:12px;border:1px solid #999;
		color:' . $text_col . ';
		background:' . $col . ';
		box-shadow:0 2px 6px rgba(0,0,0,0.15);
		transition:transform 0.2s ease;
	">'
	. $row['room_name'] .
	'</div>';

}
?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/jquery.datetimepicker.full.js"></script>
<script>
$('#allotment_date').datetimepicker({
	step: 15,
	format: 'Y-m-d H:i',
	value: '<?php echo isset($_POST["allotment_date"]) ? $_POST["allotment_date"] : date("Y-m-d H:i"); ?>'
});

$(document).on('click', '.room-tile.clickable', function () {
	if (!confirm('Is Housekeeping Done ?')) return;

	const $tile = $(this);
	const roomID = $tile.data('roomid');

	$.post('', { ajax: 'toggle', room_id: roomID }, function (res) {
		if (res == 1) {
			location.reload();
		} else {
			alert('Error: update failed');
		}
	});
});
</script>
