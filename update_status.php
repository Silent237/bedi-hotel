<?php
include("scripts/settings.php");
if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $type = mysqli_real_escape_string($db, $_POST['type']);
    if ($type === 'table') {
        $sql = "UPDATE `res_table` SET `booked_status`='0' WHERE sno='$id'";
    } elseif ($type === 'room') {
        $sql = "UPDATE `room_master` SET `booked_status`='0' WHERE sno='$id'";
    }
    $result = execute_query($sql);
    if ($result && !mysqli_error($db)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>