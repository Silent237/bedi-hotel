<?php
session_cache_limiter('nocache');
session_start();
include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();
navigation();
page_footer();

if(isset($_POST['submit'])){
    $conn=$db;
    if($_POST['cust_id']==''){
        $msg .= '<li class="error">Enter Customer Name</li>';
    }
    if($_POST['amount']==''){
        $msg .= '<li class="error">Enter Amount</li>';
    }
    if($msg==''){
        $_POST['receipt_date'] = date("Y-m-d H:i:s", strtotime($_POST['receipt_date']));
        $type = $_POST['pay_for'] === 'ADVANCE_AMT' ? 'ADVANCE_AMT' : $_POST['pay_for'];
        $payment_for = $_POST['pay_for'] === 'ADVANCE_AMT' ? 'room_rent' : '';

        if($_POST['cust_transact_sno']!=''){
            $sql = 'UPDATE customer_transactions SET 
                cust_id="'.$_POST['cust_id'].'", 
                timestamp="'.$_POST['receipt_date'].'", 
                edited_by="'.$_SESSION['username'].'", 
                edited_on=CURRENT_TIMESTAMP,
                remarks="'.$_POST['remarks'].'", 
                amount="'.$_POST['amount'].'",
                mop="'.$_POST['mop'].'",
                type="'.$type.'",
                payment_for="'.$payment_for.'"
                WHERE sno='.$_POST['cust_transact_sno'];
            $result = execute_query($sql);
            $msg .= '<li>Update successful.</li>';
        } else {
            $sql='INSERT INTO customer_transactions 
                (cust_id , type , timestamp, amount, mop, created_by , created_on , remarks, payment_for) 
                VALUES (
                    "'.$_POST['cust_id'].'", 
                    "'.$type.'", 
                    "'.$_POST['receipt_date'].'",  
                    "'.$_POST['amount'].'", 
                    "'.$_POST['mop'].'", 
                    "'.$_SESSION['username'].'",
                    CURRENT_TIMESTAMP,
                    "'.$_POST['remarks'].'", 
                    "'.$payment_for.'")';
            $result = execute_query($sql);
            $msg .= '<li class="error">Receipt Added</li>';
        }
    }
}

if(isset($_GET['id'])){
    $sql = 'SELECT * FROM customer_transactions WHERE sno='.$_GET['id'];
    $result = execute_query($sql);
    $row_cust_trans = mysqli_fetch_assoc($result);
    $timestamp = $row_cust_trans['timestamp'];
    $sql = 'SELECT *, allotment.sno AS allotment_id, customer.sno AS cust_id 
            FROM customer 
            JOIN allotment ON customer.sno = allotment.cust_id 
            WHERE customer.sno='.$row_cust_trans['cust_id'];
    $result = execute_query($sql);
    $cust_details = mysqli_fetch_assoc($result);
}

if(isset($_GET['cid'])){
    $sql='SELECT * FROM customer WHERE sno='.$_GET['cid'];
    $result = execute_query($sql);
    $row_cust_trans = mysqli_fetch_assoc($result);
    $sql='SELECT * FROM allotment WHERE cust_id='.$_GET['cid'];
    $result = execute_query($sql);
    $room_id = mysqli_fetch_assoc($result);
    $_GET['pending'] = abs($_GET['pending']);
}

if(isset($_GET['del'])){
    $sql='DELETE FROM customer_transactions WHERE sno='.$_GET['del'];
    $result = execute_query($sql);
}
?>

<style>
.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
</style>

<script type="text/javascript">
$(function() {
    var options = {
        source: function (request, response){
            $.getJSON("scripts/ajax.php?id=customer_receipt", request, response);
        },
        minLength: 1,
        select: function(event, ui) {
            $('[name="cust_name"]').val(ui.item.label);
            $('#cust_id').val(ui.item.id);
            $('#mobile').val(ui.item.mobile);
            $('#amount').val(ui.item.amount);
            $("#ajax_loader").show();
            return false;
        }
    };
    $("input#cust_name").on("keydown.autocomplete", function() {
        $(this).autocomplete(options);
    });
});
</script>

<div id="container">
<h2>Receipts</h2>
<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
<form action="receipts.php" class="wufoo leftLabel page1" method="post">
<table>
<tr>
<td>Company/Guest Name</td>
<td><input id="cust_name" name="cust_name" class="field text medium" maxlength="255" tabindex="1" 
value="<?php if(isset($_GET['id'])){ echo $cust_details['cust_name'];} ?>" type="text">
<input id="cust_id" name="cust_id" type="hidden" value="<?php if(isset($_GET['id'])){ echo $cust_details['cust_id']; } else if(isset($_GET['cid'])){ echo $_GET['cid']; } ?>"></td>
<td>Mobile</td>
<td><input name="mobile" type="text" value="<?php if(isset($_GET['id'])){echo $cust_details['mobile'];} ?>" class="field text medium" tabindex="2" id="mobile" /></td>
</tr>
<tr>
<td>Receipt Date</td>
<td><input name="receipt_date" type="text" value="<?php if(isset($row_cust_trans['timestamp'])){echo $row_cust_trans['timestamp'];}?>" class="field text medium" tabindex="3" id="receipt_date" /></td>
<td>Mode of Payment</td>
<td><select id="mop" name="mop" class="field select medium">
<option value="cash" <?php if(isset($_GET['id']) && $row_cust_trans['mop'] == 'cash'){ ?> selected <?php } ?>>Cash</option>
<option value="card" <?php if(isset($_GET['id']) && $row_cust_trans['mop'] == 'card'){ ?> selected <?php } ?>>Card</option>
<option value="credit" <?php if(isset($_GET['id']) && $row_cust_trans['mop'] == 'credit'){ ?> selected <?php } ?>>Credit</option>
</select></td>
</tr>
<tr>
<td>Receipt For</td>
<td><select id="pay_for" name="pay_for" class="field select medium">
<option value="RENT" <?php if(isset($_GET['id']) && $row_cust_trans['type'] == 'RENT'){ ?> selected <?php } ?>>Rent</option>
<option value="receipt" <?php if(isset($_GET['id']) && $row_cust_trans['type'] == 'receipt'){ ?> selected <?php } ?>>Other</option>
<option value="ADVANCE_AMT" <?php if(isset($_GET['id']) && $row_cust_trans['type'] == 'ADVANCE_AMT'){ ?> selected <?php } ?>>Advance</option>
</select></td>
<td>Amount</td>
<td><input id="amount" name="amount" value="<?php if(isset($_GET['id'])){echo $row_cust_trans['amount'];} else if(isset($_GET['cid'])) { echo $_GET['pending'];}?>" class="field text medium" maxlength="255" tabindex="5" type="text"/></td>
</tr>
<tr>
<td>Remarks</td>
<td colspan="3"><input id="remarks" name="remarks" value="<?php if(isset($_GET['id'])){echo $row_cust_trans['remarks'];}?>" class="field text medium" maxlength="255" tabindex="6" type="text" /></td>
</tr>
<tr>
<input type="hidden" name="cust_transact_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
<td><input id="submit" name="submit" class="btTxt submit" type="submit" value="Submit" tabindex="7"></td>
</tr>
</table>
</form>

<table width="100%">
<tr style="background:#000; color:#FFF;">
<th>S.No.</th>
<th>Guest Name</th>
<th>Mobile</th>
<th>Mode of Payment</th>
<th>Amount</th>
<th>Date Of Receipt</th>
<th>Edit</th>
<th>Delete</th>
</tr>
<?php
$sql = 'SELECT * FROM customer_transactions WHERE type IN ("receipt", "ADVANCE_AMT", "RENT") ORDER BY sno DESC LIMIT 50';
$result = execute_query($sql);
$i = 1;
while($row_cust_trans = mysqli_fetch_assoc($result)){
    $col = ($i % 2 == 0) ? '#CCC' : '#EEE';
    $sql = 'SELECT * FROM customer WHERE sno=' . $row_cust_trans['cust_id'];
    $result2 = execute_query($sql);
    $details = mysqli_fetch_assoc($result2);
    echo '<tr style="background:' . $col . '">
    <td>' . $i . '</td>
    <td>' . $details['cust_name'] . '</td>
    <td>' . $details['mobile'] . '</td>
    <td>' . ucfirst($row_cust_trans['mop']) . '</td>
    <td>' . $row_cust_trans['amount'] . '</td>
    <td>' . $row_cust_trans['timestamp'] . '</td>
    <td><a href="receipts.php?id=' . $row_cust_trans['sno'] . '">Edit</a></td>
    <td><a href="receipts.php?del=' . $row_cust_trans['sno'] . '" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
    </tr>';
    $i++;
}
?>
</table>
</div>

<script src="js/jquery.datetimepicker.full.js"></script>
<script>
$('#receipt_date').datetimepicker({
    step:15,
    format: 'd-m-Y H:i',
    value: '<?php
        if(isset($_POST['date_from'])){
            echo $_POST['date_from'];
        } elseif(isset($_GET['id'])){
            echo date("d-m-Y H:i", strtotime($timestamp));
        } else {
            echo date("d-m-Y H:i");	
        }
    ?>'
});
</script>
