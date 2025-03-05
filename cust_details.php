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
?>
<style>
    .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
    </style>
<script type="text/javascript" language="javascript">
$(function() {
	var options = {
		source: function (request, response){
			$.getJSON("scripts/ajax.php?id=cust_name",request, response);
		},
		minLength: 1,
		select: function( event, ui ) {
			log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.label :
				"Nothing selected, input was " + this.value );
		},
		select: function( event, ui ) {
		    $("[name='cust_name']").val(ui.item.label);
			$('#cust_id').val(ui.item.id);
			$('#mobile').val(ui.item.mobile);
			document.getElementById('room_name').innerHTML =(ui.item.room_name);
			$("#ajax_loader").show();
			return false;
		}
	};
$("input#cust_name").on("keydown.autocomplete", function() {
	$(this).autocomplete(options);
});
})
</script>
 <div id="container">
        <h2>Customer Details</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="vacant_room.php" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<table>
				<tr>
					<td>Customer Name</td>
					<td><input id="cust_name" name="cust_name" value="" class="field text medium" maxlength="255" tabindex="1" type="text" />
					<input type="hidden" name="cust_id" id="cust_id" value="" />
					<td>Mobile</td>
					<td><input id="mobile" name="mobile" value="" class="field text medium" maxlength="255" tabindex="2" type="text" />
				</tr>
                <tr>
                <td>Room Name</td>
					<td><select id="room_name" name="room_name" class="field text medium" tabindex="3">
					</select>
					</td>
					<td>Check In</td>
					<td><input id="check_in" name="check_in" value="" class="field text medium" maxlength="255" tabindex="2" type="text" />
				</tr>
                <tr>
                	<td>Check Out</td>
					<td><input id="check_out" name="check_out" value="" class="field text medium" maxlength="255" tabindex="2" type="text" />
                    <td>Destination</td>
					<td><input id="destination" name="destination" value="" class="field text medium" maxlength="255" tabindex="2" type="text" />
                </tr>
				<tr>
					<td>Remarks</td>
					<td><input id="remarks" name="remarks" value="" class="field text medium" maxlength="255" tabindex="10" type="text" />
				</tr>
				<tr>
				<td><input id="submit" name="submit" class="btTxt submit" type="submit" value="Add/Update" onMouseDown="" tabindex="23"></td>
				</tr>
		</table>
	</form>
</div>
<?php
page_footer();
?>
